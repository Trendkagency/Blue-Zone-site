<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Models\FinancialAccount;
use App\Models\FinancialTransaction;
use App\Models\PurchaseInvoice;
use App\Services\FinanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierPaymentController extends Controller
{
    public function __construct(protected FinanceService $financeService)
    {
    }

    /**
     * List all supplier disbursements.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');

        $query = FinancialTransaction::with(['account', 'supplier', 'purchaseInvoice'])
            ->where('type', 'supplier_payment')
            ->latest('id');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('transaction_number', 'like', "%{$search}%")
                  ->orWhere('receipt_voucher_number', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function ($sq) use ($search) {
                      $sq->where('name_en', 'like', "%{$search}%")
                        ->orWhere('name_ar', 'like', "%{$search}%");
                  });
            });
        }

        $payments = $query->paginate(15)->withQueryString();

        $totalDisbursed = (float) FinancialTransaction::where('type', 'supplier_payment')->sum('amount');
        $accounts = FinancialAccount::where('is_active', true)->get();
        $unpaidBills = PurchaseInvoice::with('supplier')
            ->whereIn('payment_status', ['unpaid', 'partially_paid'])
            ->get();

        return view('admin.finance.supplier-payments.index', [
            'payments' => $payments,
            'totalDisbursed' => $totalDisbursed,
            'accounts' => $accounts,
            'unpaidBills' => $unpaidBills,
            'search' => $search,
        ]);
    }

    /**
     * Store outgoing supplier payment.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'purchase_invoice_id' => 'required|exists:purchase_invoices,id',
            'financial_account_id' => 'required|exists:financial_accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|max:50',
            'payment_date' => 'required|date',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $invoice = PurchaseInvoice::findOrFail($validated['purchase_invoice_id']);
        $account = FinancialAccount::findOrFail($validated['financial_account_id']);

        $this->financeService->recordSupplierPayment($invoice, (float) $validated['amount'], $account, $validated);

        return redirect()->back()->with('status', __('finance.success_payment'));
    }

    /**
     * Print official payment disbursement voucher.
     */
    public function voucher(int $id): View
    {
        $payment = FinancialTransaction::with(['account', 'supplier', 'purchaseInvoice.items'])
            ->where('type', 'supplier_payment')
            ->findOrFail($id);

        return view('admin.finance.supplier-payments.voucher', [
            'payment' => $payment,
        ]);
    }
}
