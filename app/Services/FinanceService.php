<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\FinancialAccount;
use App\Models\FinancialTransaction;
use App\Models\Order;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceItem;
use App\Models\PurchaseOrder;
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

                    PurchaseInvoiceItem::create([
                        'purchase_invoice_id' => $invoice->id,
                        'raw_material_id' => $item['raw_material_id'] ?? null,
                        'item_name' => $item['item_name'] ?? 'Item',
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
