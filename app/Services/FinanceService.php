<?php

namespace App\Services;

use App\Enums\RawMaterialBatchStatus;
use App\Enums\RawMaterialMovementType;
use App\Models\Expense;
use App\Models\FinancialAccount;
use App\Models\FinancialTransaction;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItem;
use App\Models\PurchaseOrder;
use App\Models\RawMaterial;
use App\Models\RawMaterialBatch;
use App\Models\RawMaterialMovement;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinanceService
{
    /**
     * Record an incoming customer payment for a sales order.
     * Credits the financial account, logs transaction, and updates order payment status.
     */
    public function recordCustomerPayment(Order $order, float $amount, FinancialAccount $account, array $data = []): FinancialTransaction
    {
        return DB::transaction(function () use ($order, $amount, $account, $data) {
            $account->increment('current_balance', $amount);
            $account->refresh();

            $txCount = FinancialTransaction::count() + 1;
            $txNum = 'TXN-' . date('Y') . '-' . str_pad((string) $txCount, 5, '0', STR_PAD_LEFT);
            $rcpNum = 'RCP-' . date('Y') . '-' . str_pad((string) $txCount, 5, '0', STR_PAD_LEFT);

            $transaction = FinancialTransaction::create([
                'transaction_number' => $txNum,
                'type' => 'customer_payment',
                'direction' => 'credit',
                'financial_account_id' => $account->id,
                'order_id' => $order->id,
                'customer_id' => $order->customer_id,
                'amount' => $amount,
                'balance_after' => $account->current_balance,
                'payment_method' => $data['payment_method'] ?? 'bank_transfer',
                'transaction_date' => $data['payment_date'] ?? now()->toDateString(),
                'reference_number' => $data['reference_number'] ?? null,
                'receipt_voucher_number' => $rcpNum,
                'notes' => $data['notes'] ?? ('Customer payment for Order #' . $order->order_number),
                'created_by' => auth()->id(),
            ]);

            // Update Order Payment Status
            $totalCollected = (float) $order->financialPayments()->where('direction', 'credit')->sum('amount');
            if ($totalCollected >= (float) $order->total) {
                $order->update(['payment_status' => 'Paid']);
                try {
                    app(\App\Services\CollectionService::class)->syncOrder($order);
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('Collection auto-sync failed: ' . $e->getMessage());
                }
            } elseif ($totalCollected > 0) {
                $order->update(['payment_status' => 'Partially Paid']);
            }

            return $transaction;
        });
    }

    /**
     * Record an outgoing supplier payment for a purchase invoice.
     * Debits the financial account, logs transaction, and updates invoice paid balance.
     */
    public function recordSupplierPayment(PurchaseInvoice $invoice, float $amount, FinancialAccount $account, array $data = []): FinancialTransaction
    {
        return DB::transaction(function () use ($invoice, $amount, $account, $data) {
            $account->decrement('current_balance', $amount);
            $account->refresh();

            $txCount = FinancialTransaction::count() + 1;
            $txNum = 'TXN-' . date('Y') . '-' . str_pad((string) $txCount, 5, '0', STR_PAD_LEFT);
            $vchNum = 'VCH-' . date('Y') . '-' . str_pad((string) $txCount, 5, '0', STR_PAD_LEFT);

            $transaction = FinancialTransaction::create([
                'transaction_number' => $txNum,
                'type' => 'supplier_payment',
                'direction' => 'debit',
                'financial_account_id' => $account->id,
                'purchase_invoice_id' => $invoice->id,
                'supplier_id' => $invoice->supplier_id,
                'amount' => $amount,
                'balance_after' => $account->current_balance,
                'payment_method' => $data['payment_method'] ?? 'bank_transfer',
                'transaction_date' => $data['payment_date'] ?? now()->toDateString(),
                'reference_number' => $data['reference_number'] ?? null,
                'receipt_voucher_number' => $vchNum,
                'notes' => $data['notes'] ?? ('Supplier payment for Bill #' . $invoice->invoice_number),
                'created_by' => auth()->id(),
            ]);

            // Update Purchase Invoice Paid Balance & Payment Status
            $newPaid = (float) $invoice->paid_amount + $amount;
            $paymentStatus = 'partially_paid';
            if ($newPaid >= (float) $invoice->grand_total) {
                $paymentStatus = 'paid';
            } elseif ($newPaid <= 0) {
                $paymentStatus = 'unpaid';
            }

            $invoice->update([
                'paid_amount' => $newPaid,
                'payment_status' => $paymentStatus,
            ]);

            return $transaction;
        });
    }

    /**
     * Record an operational expense and automatically debit the selected cash/bank account.
     */
    public function recordExpense(array $data): Expense
    {
        return DB::transaction(function () use ($data) {
            $account = FinancialAccount::findOrFail($data['financial_account_id']);
            $amount = (float) $data['amount'];
            $taxAmount = (float) ($data['tax_amount'] ?? 0);
            $totalAmount = (float) ($data['total_amount'] ?? ($amount + $taxAmount));

            $expCount = Expense::count() + 1;
            $expNum = 'EXP-' . date('Y') . '-' . str_pad((string) $expCount, 5, '0', STR_PAD_LEFT);

            $expense = Expense::create([
                'expense_number' => $expNum,
                'expense_category_id' => $data['expense_category_id'],
                'financial_account_id' => $account->id,
                'title_en' => $data['title_en'],
                'title_ar' => $data['title_ar'] ?? $data['title_en'],
                'amount' => $amount,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'expense_date' => $data['expense_date'] ?? now()->toDateString(),
                'payment_method' => $data['payment_method'] ?? 'bank_transfer',
                'beneficiary_name' => $data['beneficiary_name'] ?? null,
                'reference_number' => $data['reference_number'] ?? null,
                'receipt_path' => $data['receipt_path'] ?? null,
                'status' => 'approved',
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            // Debit Account & Log Transaction
            $account->decrement('current_balance', $totalAmount);
            $account->refresh();

            $txCount = FinancialTransaction::count() + 1;
            $txNum = 'TXN-' . date('Y') . '-' . str_pad((string) $txCount, 5, '0', STR_PAD_LEFT);

            FinancialTransaction::create([
                'transaction_number' => $txNum,
                'type' => 'expense',
                'direction' => 'debit',
                'financial_account_id' => $account->id,
                'expense_id' => $expense->id,
                'amount' => $totalAmount,
                'balance_after' => $account->current_balance,
                'payment_method' => $expense->payment_method,
                'transaction_date' => $expense->expense_date,
                'reference_number' => $expense->reference_number,
                'receipt_voucher_number' => 'VCH-EXP-' . $expense->id,
                'notes' => 'Expense: ' . $expense->title_en,
                'created_by' => auth()->id(),
            ]);

            return $expense;
        });
    }

    /**
     * Transfer funds between two cash/bank accounts with double-entry audit.
     */
    public function transferFunds(FinancialAccount $from, FinancialAccount $to, float $amount, array $data = []): array
    {
        return DB::transaction(function () use ($from, $to, $amount, $data) {
            $from->decrement('current_balance', $amount);
            $from->refresh();

            $to->increment('current_balance', $amount);
            $to->refresh();

            $date = $data['date'] ?? now()->toDateString();
            $ref = $data['reference_number'] ?? null;
            $notes = $data['notes'] ?? ('Funds transfer from ' . $from->name_en . ' to ' . $to->name_en);

            $txCount = FinancialTransaction::count() + 1;
            $txOutNum = 'TXN-' . date('Y') . '-' . str_pad((string) $txCount, 5, '0', STR_PAD_LEFT);

            $out = FinancialTransaction::create([
                'transaction_number' => $txOutNum,
                'type' => 'transfer_out',
                'direction' => 'debit',
                'financial_account_id' => $from->id,
                'destination_account_id' => $to->id,
                'amount' => $amount,
                'balance_after' => $from->current_balance,
                'payment_method' => 'bank_transfer',
                'transaction_date' => $date,
                'reference_number' => $ref,
                'notes' => $notes,
                'created_by' => auth()->id(),
            ]);

            $txCount++;
            $txInNum = 'TXN-' . date('Y') . '-' . str_pad((string) $txCount, 5, '0', STR_PAD_LEFT);

            $in = FinancialTransaction::create([
                'transaction_number' => $txInNum,
                'type' => 'transfer_in',
                'direction' => 'credit',
                'financial_account_id' => $to->id,
                'destination_account_id' => $from->id,
                'amount' => $amount,
                'balance_after' => $to->current_balance,
                'payment_method' => 'bank_transfer',
                'transaction_date' => $date,
                'reference_number' => $ref,
                'notes' => $notes,
                'created_by' => auth()->id(),
            ]);

            return ['transfer_out' => $out, 'transfer_in' => $in];
        });
    }

    /**
     * Create a Purchase Invoice (standalone or generated from Purchase Order).
     */
    public function createPurchaseInvoice(array $data, ?PurchaseOrder $po = null): PurchaseInvoice
    {
        return DB::transaction(function () use ($data, $po) {
            $invCount = PurchaseInvoice::count() + 1;
            $invNum = 'PINV-' . date('Y') . '-' . str_pad((string) $invCount, 5, '0', STR_PAD_LEFT);

            $invoice = PurchaseInvoice::create([
                'invoice_number' => $data['invoice_number'] ?? $invNum,
                'supplier_id' => $data['supplier_id'] ?? ($po?->supplier_id),
                'purchase_order_id' => $po?->id ?? ($data['purchase_order_id'] ?? null),
                'invoice_date' => $data['invoice_date'] ?? now()->toDateString(),
                'due_date' => $data['due_date'] ?? Carbon::parse($data['invoice_date'] ?? now())->addDays(30)->toDateString(),
                'subtotal' => (float) ($data['subtotal'] ?? 0),
                'tax_amount' => (float) ($data['tax_amount'] ?? 0),
                'grand_total' => (float) ($data['grand_total'] ?? 0),
                'paid_amount' => 0,
                'payment_status' => 'unpaid',
                'status' => 'approved',
                'reference_number' => $data['reference_number'] ?? null,
                'attachment_path' => $data['attachment_path'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            if (!empty($data['items']) && is_array($data['items'])) {
                foreach ($data['items'] as $item) {
                    $qty = (float) ($item['quantity'] ?? 1);
                    $price = (float) ($item['unit_price'] ?? 0);
                    $sub = $qty * $price;
                    $taxRate = (float) ($item['tax_rate'] ?? 15.0);
                    $tax = round($sub * ($taxRate / 100), 2);

                    $itemType = $item['item_type'] ?? (!empty($item['product_id']) ? 'product' : 'raw_material');
                    $rawMaterialId = $item['raw_material_id'] ?? null;
                    $productId = $item['product_id'] ?? null;
                    $batchNumber = !empty($item['batch_number']) ? trim($item['batch_number']) : null;
                    $expiryDate = !empty($item['expiry_date']) ? $item['expiry_date'] : null;

                    $rawMaterialBatchId = $item['raw_material_batch_id'] ?? null;
                    $productBatchId = $item['product_batch_id'] ?? null;
                    $rawMat = null;
                    $prod = null;

                    // Direct Inwarding: Auto-register Raw Material Batch only if not already linked from a GRN
                    if ($itemType === 'raw_material' && $rawMaterialId) {
                        $rawMat = RawMaterial::find($rawMaterialId);
                        if ($rawMat) {
                            $rawMat->standard_cost = $price;
                            $rawMat->save();

                            if (!$rawMaterialBatchId) {
                                $supplierLot = $batchNumber;
                                if (empty($batchNumber) || RawMaterialBatch::where('batch_number', $batchNumber)->exists()) {
                                    $batchNumber = RawMaterialBatch::generateBatchNumber();
                                }

                                if (empty($expiryDate)) {
                                    $days = (int) ($rawMat->shelf_life_days ?: 730);
                                    $expiryDate = Carbon::parse($invoice->invoice_date)->addDays($days)->toDateString();
                                }

                                $createdRawBatch = RawMaterialBatch::create([
                                    'raw_material_id' => $rawMat->id,
                                    'batch_number' => $batchNumber,
                                    'supplier_lot' => $supplierLot ?? $batchNumber,
                                    'supplier_id' => $invoice->supplier_id,
                                    'location_id' => 'central_wh',
                                    'initial_qty' => $qty,
                                    'current_qty' => $qty,
                                    'available_qty' => $qty,
                                    'reserved_qty' => 0,
                                    'quarantine_qty' => 0,
                                    'unit_cost' => $price,
                                    'manufacture_date' => $invoice->invoice_date,
                                    'expiry_date' => $expiryDate,
                                    'status' => RawMaterialBatchStatus::Approved,
                                ]);
                                $rawMaterialBatchId = $createdRawBatch->id;

                                RawMaterialMovement::create([
                                    'raw_material_batch_id' => $createdRawBatch->id,
                                    'movement_type' => RawMaterialMovementType::Receipt,
                                    'from_location' => 'Supplier: ' . ($invoice->supplier?->name_en ?? 'Direct Vendor Intake'),
                                    'to_location' => 'central_wh',
                                    'quantity' => $qty,
                                    'previous_qty' => 0,
                                    'new_qty' => $qty,
                                    'user_id' => auth()->id() ?? 1,
                                    'reference_type' => 'purchase_invoice',
                                    'reference_id' => $invoice->id,
                                    'note' => "Direct intake from Purchase Invoice #{$invoice->invoice_number}",
                                    'ip_address' => request()?->ip(),
                                    'created_at' => now(),
                                ]);
                            }
                        }
                    } elseif ($itemType === 'product' && $productId) {
                        $prod = Product::find($productId);
                        if ($prod && !$productBatchId) {
                            $supplierLot = $batchNumber;
                            if (empty($batchNumber) || ProductBatch::where('batch_number', $batchNumber)->exists()) {
                                $batchNumber = ProductBatch::generateBatchNumber();
                            }

                            if (empty($expiryDate)) {
                                $expiryDate = $prod->calculateDefaultExpiryDate($invoice->invoice_date);
                            }

                            $intQty = (int) round($qty);
                            $createdProdBatch = ProductBatch::create([
                                'product_id' => $prod->id,
                                'batch_number' => $batchNumber,
                                'supplier_lot' => $supplierLot ?? $batchNumber,
                                'location_id' => 'central_wh',
                                'initial_qty' => $intQty,
                                'current_qty' => $intQty,
                                'available_qty' => $intQty,
                                'reserved_qty' => 0,
                                'quarantine_qty' => 0,
                                'unit_cost' => $price,
                                'manufacture_date' => $invoice->invoice_date,
                                'expiry_date' => $expiryDate,
                                'status' => 'approved',
                            ]);
                            $productBatchId = $createdProdBatch->id;

                            if ($intQty > 0) {
                                InventoryService::adjustStock(
                                    $prod,
                                    'central_wh',
                                    $intQty,
                                    'Purchase Receipt',
                                    "Bill {$invoice->invoice_number} | Batch: {$batchNumber}",
                                    auth()->user()?->name ?? 'Finance System',
                                    $invoice->supplier?->name_en ?? 'Supplier',
                                    false
                                );
                            }
                        }
                    }

                    PurchaseInvoiceItem::create([
                        'purchase_invoice_id' => $invoice->id,
                        'item_type' => $itemType,
                        'product_id' => $productId,
                        'raw_material_id' => $rawMaterialId,
                        'product_batch_id' => $productBatchId,
                        'raw_material_batch_id' => $rawMaterialBatchId,
                        'batch_number' => $batchNumber,
                        'expiry_date' => $expiryDate,
                        'item_name' => $item['item_name'] ?? ($rawMat?->name_en ?? $prod?->name_en ?? 'Item'),
                        'quantity' => $qty,
                        'unit_price' => $price,
                        'subtotal' => $sub,
                        'tax_rate' => $taxRate,
                        'tax_amount' => $tax,
                        'total' => $sub + $tax,
                    ]);
                }
            }

            return $invoice;
        });
    }

    /**
     * Executive Finance Dashboard Analytics.
     */
    public function getDashboardAnalytics(): array
    {
        // 1. Total Treasury Cash in Accounts
        $accounts = FinancialAccount::where('is_active', true)->get();
        $totalTreasuryBalance = (float) $accounts->sum('current_balance');

        // 2. Total Invoiced & Receivables (Customer Orders)
        $allOrders = Order::all();
        $totalSalesInvoiced = (float) $allOrders->sum('total');
        $totalCustomerPaid = (float) FinancialTransaction::where('type', 'customer_payment')->sum('amount');
        if ($totalCustomerPaid <= 0) {
            $totalCustomerPaid = (float) $allOrders->where('payment_status', 'Paid')->sum('total');
        }
        $totalReceivables = max(0, $totalSalesInvoiced - $totalCustomerPaid);

        // 3. Total Purchase Invoices & Payables (Supplier Bills)
        $allPurchaseInvoices = PurchaseInvoice::all();
        $totalPurchasesInvoiced = (float) $allPurchaseInvoices->sum('grand_total');
        $totalSupplierPaid = (float) $allPurchaseInvoices->sum('paid_amount');
        $totalPayables = max(0, $totalPurchasesInvoiced - $totalSupplierPaid);

        // 4. Operating Expenses
        $totalExpenses = (float) Expense::sum('total_amount');

        // 5. Net Operating Cashflow (Inflow - Outflow)
        $netCashflow = $totalCustomerPaid - ($totalSupplierPaid + $totalExpenses);

        // 6. 30-Day Daily Cash Flow Trend
        $days = [];
        $incomeData = [];
        $expenseData = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $days[] = Carbon::now()->subDays($i)->format('d M');

            $dayIncome = (float) FinancialTransaction::where('direction', 'credit')
                ->whereDate('transaction_date', $date)
                ->sum('amount');

            $dayExpense = (float) FinancialTransaction::where('direction', 'debit')
                ->whereDate('transaction_date', $date)
                ->sum('amount');

            $incomeData[] = $dayIncome;
            $expenseData[] = $dayExpense;
        }

        // 7. Recent Financial Transactions
        $recentTransactions = FinancialTransaction::with(['account', 'customer', 'supplier', 'order', 'purchaseInvoice', 'expense'])
            ->latest('id')
            ->limit(10)
            ->get();

        // 8. Expense Breakdown by Category
        $expensesByCategory = Expense::with('category')
            ->select('expense_category_id', DB::raw('SUM(total_amount) as total'))
            ->groupBy('expense_category_id')
            ->get();

        return [
            'accounts' => $accounts,
            'totalTreasuryBalance' => $totalTreasuryBalance,
            'totalSalesInvoiced' => $totalSalesInvoiced,
            'totalReceivables' => $totalReceivables,
            'totalPurchasesInvoiced' => $totalPurchasesInvoiced,
            'totalPayables' => $totalPayables,
            'totalExpenses' => $totalExpenses,
            'netCashflow' => $netCashflow,
            'chartDays' => $days,
            'chartIncome' => $incomeData,
            'chartExpense' => $expenseData,
            'recentTransactions' => $recentTransactions,
            'expensesByCategory' => $expensesByCategory,
            'unpaidOrdersCount' => $allOrders->whereIn('payment_status', ['Pending', 'Partially Paid', 'unpaid'])->count(),
            'unpaidBillsCount' => $allPurchaseInvoices->whereIn('payment_status', ['unpaid', 'partially_paid'])->count(),
        ];
    }
}
