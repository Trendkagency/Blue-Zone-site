<?php

namespace App\Services;

use App\Models\FinancialAccount;
use App\Models\FinancialTransaction;
use App\Models\PaymentEntry;
use App\Models\PurchaseInvoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    /**
     * Record a new manual or automated payment disbursement entry.
     */
    public function recordEntry(array $data): PaymentEntry
    {
        return DB::transaction(function () use ($data) {
            $category = $data['category'] ?? PaymentEntry::CATEGORY_PURCHASE;
            $cats = PaymentEntry::getCategories();
            $defaultVat = $cats[$category]['default_vat'] ?? 15.00;
            $vatRate = isset($data['vat_rate']) && $data['vat_rate'] !== '' ? (float) $data['vat_rate'] : $defaultVat;

            // Two-way dynamic calculation
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

            $paymentNumber = $data['payment_number'] ?? PaymentEntry::generatePaymentNumber();

            // Find financial account if provided
            $financialAccount = null;
            if (!empty($data['financial_account_id'])) {
                $financialAccount = FinancialAccount::find($data['financial_account_id']);
            }

            // Cheque clearance handling
            $paymentMethod = $data['payment_method'] ?? 'bank_transfer';
            $status = $data['status'] ?? 'posted';
            if ($paymentMethod === 'cheque' && empty($data['status'])) {
                $status = 'pending_clearance';
            }

            $entry = PaymentEntry::create([
                'payment_number' => $paymentNumber,
                'entry_date' => $data['entry_date'] ?? now()->toDateString(),
                'category' => $category,
                'description' => $data['description'] ?? null,
                'payee_type' => $data['payee_type'] ?? 'supplier',
                'supplier_id' => $data['supplier_id'] ?? null,
                'purchase_invoice_id' => $data['purchase_invoice_id'] ?? null,
                'supplier_code' => $data['supplier_code'] ?? null,
                'payee_name' => $data['payee_name'] ?? 'Payee',
                'amount_excl_vat' => $amountExcl,
                'vat_rate' => $vatRate,
                'vat_amount' => $vatAmount,
                'gross_amount' => $gross,
                'financial_account_id' => $financialAccount?->id,
                'payment_method' => $paymentMethod,
                'cheque_number' => $data['cheque_number'] ?? null,
                'cheque_bank' => $data['cheque_bank'] ?? null,
                'cheque_due_date' => $data['cheque_due_date'] ?? null,
                'status' => $status,
                'cleared_at' => $status === 'posted' ? now() : null,
                'created_by' => $data['created_by'] ?? auth()->id(),
                'notes' => $data['notes'] ?? null,
            ]);

            // If a Purchase Invoice is linked, update its balance and payment status
            if (!empty($entry->purchase_invoice_id)) {
                $invoice = PurchaseInvoice::find($entry->purchase_invoice_id);
                if ($invoice) {
                    $newPaid = round((float) $invoice->paid_amount + $gross, 2);
                    $total = (float) ($invoice->grand_total ?? 0);
                    $newDue = round(max(0, $total - $newPaid), 2);
                    $newStatus = $newDue <= 0.01 ? 'paid' : ($newPaid > 0 ? 'partially_paid' : 'unpaid');

                    $invoice->update([
                        'paid_amount' => $newPaid,
                        'payment_status' => $newStatus,
                    ]);
                }
            }

            // If financial account linked and status is posted, deduct balance and log audit transaction
            if ($financialAccount && $gross > 0 && $status === 'posted') {
                $financialAccount->decrement('current_balance', $gross);
                $financialAccount->refresh();

                // Log audit financial transaction
                $txCount = FinancialTransaction::count() + 1;
                FinancialTransaction::create([
                    'transaction_number' => 'TXN-' . date('Y') . '-' . str_pad((string) $txCount, 5, '0', STR_PAD_LEFT),
                    'type' => 'supplier_payment',
                    'direction' => 'debit',
                    'financial_account_id' => $financialAccount->id,
                    'supplier_id' => $entry->supplier_id,
                    'purchase_invoice_id' => $entry->purchase_invoice_id,
                    'amount' => $gross,
                    'balance_after' => $financialAccount->current_balance,
                    'payment_method' => $entry->payment_method,
                    'transaction_date' => $entry->entry_date,
                    'reference_number' => $entry->payment_number,
                    'receipt_voucher_number' => $entry->payment_number,
                    'notes' => 'Payment Entry #' . $entry->payment_number . ' [' . $entry->category . '] - ' . $entry->payee_name,
                    'created_by' => $entry->created_by ?? auth()->id(),
                ]);
            }

            return $entry;
        });
    }

    /**
     * Clear a pending cheque and deduct the financial account balance.
     */
    public function clearCheque(PaymentEntry $entry): PaymentEntry
    {
        if ($entry->status === 'posted') {
            return $entry;
        }

        return DB::transaction(function () use ($entry) {
            $financialAccount = $entry->financialAccount;
            $gross = (float) $entry->gross_amount;

            if ($financialAccount && $gross > 0) {
                $financialAccount->decrement('current_balance', $gross);
                $financialAccount->refresh();

                $txCount = FinancialTransaction::count() + 1;
                FinancialTransaction::create([
                    'transaction_number' => 'TXN-' . date('Y') . '-' . str_pad((string) $txCount, 5, '0', STR_PAD_LEFT),
                    'type' => 'supplier_payment',
                    'direction' => 'debit',
                    'financial_account_id' => $financialAccount->id,
                    'supplier_id' => $entry->supplier_id,
                    'purchase_invoice_id' => $entry->purchase_invoice_id,
                    'amount' => $gross,
                    'balance_after' => $financialAccount->current_balance,
                    'payment_method' => 'cheque',
                    'transaction_date' => now()->toDateString(),
                    'reference_number' => $entry->payment_number,
                    'receipt_voucher_number' => $entry->payment_number,
                    'notes' => 'Cheque Clearance #' . $entry->cheque_number . ' for Payment Entry #' . $entry->payment_number,
                    'created_by' => auth()->id() ?? $entry->created_by,
                ]);
            }

            $entry->update([
                'status' => 'posted',
                'cleared_at' => now(),
            ]);

            return $entry->refresh();
        });
    }
}
