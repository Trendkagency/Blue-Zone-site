<?php

namespace App\Services;

use App\Models\CollectionEntry;
use App\Models\FinancialAccount;
use App\Models\FinancialTransaction;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CollectionService
{
    /**
     * Record a new manual or automated collection entry.
     */
    public function recordEntry(array $data): CollectionEntry
    {
        return DB::transaction(function () use ($data) {
            $category = $data['category'] ?? CollectionEntry::CATEGORY_SALES;
            $vatRate = isset($data['vat_rate']) ? (float) $data['vat_rate'] : ($category === CollectionEntry::CATEGORY_EQUITIES ? 0.00 : 15.00);

            // Calculate amounts dynamically
            if (isset($data['gross_amount']) && (!isset($data['amount_excl_vat']) || $data['amount_excl_vat'] === '')) {
                $gross = (float) $data['gross_amount'];
                if ($vatRate > 0) {
                    $amountExcl = round($gross / (1 + ($vatRate / 100)), 2);
                    $vatAmount = round($gross - $amountExcl, 2);
                } else {
                    $amountExcl = $gross;
                    $vatAmount = 0.00;
                }
            } else {
                $amountExcl = (float) ($data['amount_excl_vat'] ?? 0);
                $vatAmount = round($amountExcl * ($vatRate / 100), 2);
                $gross = round($amountExcl + $vatAmount, 2);
            }

            $collectionNumber = $data['collection_number'] ?? CollectionEntry::generateCollectionNumber();

            // Find financial account if provided
            $financialAccount = null;
            if (!empty($data['financial_account_id'])) {
                $financialAccount = FinancialAccount::find($data['financial_account_id']);
            }

            $entry = CollectionEntry::create([
                'collection_number' => $collectionNumber,
                'entry_date' => $data['entry_date'] ?? now()->toDateString(),
                'category' => $category,
                'description' => $data['description'] ?? null,
                'source_type' => $data['source_type'] ?? 'customer',
                'customer_id' => $data['customer_id'] ?? null,
                'supplier_id' => $data['supplier_id'] ?? null,
                'customer_code' => $data['customer_code'] ?? null,
                'customer_source' => $data['customer_source'] ?? null,
                'amount_excl_vat' => $amountExcl,
                'vat_rate' => $vatRate,
                'vat_amount' => $vatAmount,
                'gross_amount' => $gross,
                'financial_account_id' => $financialAccount?->id,
                'payment_method' => $data['payment_method'] ?? 'bank_transfer',
                'cheque_number' => $data['cheque_number'] ?? null,
                'cheque_bank' => $data['cheque_bank'] ?? null,
                'cheque_due_date' => $data['cheque_due_date'] ?? null,
                'order_id' => $data['order_id'] ?? null,
                'is_auto_synced' => !empty($data['is_auto_synced']),
                'status' => $data['status'] ?? 'posted',
                'created_by' => $data['created_by'] ?? auth()->id(),
                'notes' => $data['notes'] ?? null,
            ]);

            // If a financial account is linked, update ledger balance
            if ($financialAccount && $gross > 0 && ($data['status'] ?? 'posted') === 'posted') {
                $financialAccount->increment('current_balance', $gross);
                $financialAccount->refresh();

                // Log audit financial transaction
                $txCount = FinancialTransaction::count() + 1;
                FinancialTransaction::create([
                    'transaction_number' => 'TXN-' . date('Y') . '-' . str_pad((string) $txCount, 5, '0', STR_PAD_LEFT),
                    'type' => 'customer_payment',
                    'direction' => 'credit',
                    'financial_account_id' => $financialAccount->id,
                    'order_id' => $entry->order_id,
                    'customer_id' => $entry->customer_id,
                    'amount' => $gross,
                    'balance_after' => $financialAccount->current_balance,
                    'payment_method' => $entry->payment_method,
                    'transaction_date' => $entry->entry_date,
                    'reference_number' => $entry->collection_number,
                    'receipt_voucher_number' => $entry->collection_number,
                    'notes' => 'Collection Entry #' . $entry->collection_number . ' [' . $entry->category . ']',
                    'created_by' => $entry->created_by ?? auth()->id(),
                ]);
            }

            return $entry;
        });
    }

    /**
     * Synchronize a single sales order into a Collection Entry.
     */
    public function syncOrder(Order $order): ?CollectionEntry
    {
        // Don't duplicate if already synced
        $existing = CollectionEntry::where('order_id', $order->id)->first();
        if ($existing) {
            return $existing;
        }

        // Only sync paid orders
        $isPaid = in_array(strtolower((string) $order->payment_status), ['paid', 'completed']) ||
                  in_array(strtolower((string) $order->status), ['completed', 'delivered']);

        if (!$isPaid && (float) $order->total > 0) {
            return null;
        }

        // Determine default online payment or bank account
        $account = FinancialAccount::where('is_active', true)
            ->where(function ($q) {
                $q->where('type', 'bank')
                  ->orWhere('name_en', 'like', '%online%')
                  ->orWhere('name_en', 'like', '%gateway%');
            })
            ->first() ?? FinancialAccount::where('is_active', true)->first();

        $subtotal = (float) $order->subtotal;
        $tax = (float) $order->tax;
        $total = (float) $order->total;

        if ($subtotal <= 0 && $total > 0) {
            $subtotal = round($total / 1.15, 2);
            $tax = round($total - $subtotal, 2);
        }

        return $this->recordEntry([
            'collection_number' => CollectionEntry::generateCollectionNumber(),
            'entry_date' => $order->invoice_date ?? $order->date ?? ($order->created_at ? $order->created_at->format('Y-m-d') : now()->toDateString()),
            'category' => CollectionEntry::CATEGORY_ONLINE_SALES_AUTO,
            'description' => 'Invoice #' . ($order->invoice_number ?? $order->order_number),
            'customer_id' => $order->customer_id,
            'customer_code' => $order->customer_id ? ('CUST-' . $order->customer_id) : 'ONLINE-GUEST',
            'customer_source' => $order->customer_name ?? 'Online Store Customer',
            'amount_excl_vat' => $subtotal,
            'vat_rate' => 15.00,
            'vat_amount' => $tax,
            'gross_amount' => $total,
            'financial_account_id' => $account?->id,
            'payment_method' => $order->payment_method ?? 'online_gateway',
            'order_id' => $order->id,
            'is_auto_synced' => true,
            'status' => 'posted',
            'notes' => 'Auto-synced from Online Store order #' . $order->order_number,
        ]);
    }

    /**
     * Batch synchronize all unpaid / unlinked online sales orders.
     */
    public function syncAllOnlineSales(): int
    {
        $orders = Order::where(function ($q) {
                $q->whereIn('payment_status', ['Paid', 'paid', 'completed'])
                  ->orWhere('status', 'completed');
            })
            ->whereDoesntHave('collectionEntry')
            ->orderBy('id', 'asc')
            ->get();

        $count = 0;
        foreach ($orders as $order) {
            if ($this->syncOrder($order)) {
                $count++;
            }
        }

        return $count;
    }
}
