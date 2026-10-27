<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Models\FinancialAccount;
use App\Models\FinancialTransaction;
use App\Models\Order;
use App\Services\FinanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerPaymentController extends Controller
{
    public function __construct(protected FinanceService $financeService)
    {
    }

    /**
     * List all customer payment collections.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');

        $query = FinancialTransaction::with(['account', 'customer', 'order'])
            ->where('type', 'customer_payment')
            ->latest('id');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('transaction_number', 'like', "%{$search}%")
                  ->orWhere('receipt_voucher_number', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%")
                  ->orWhereHas('order', function ($oq) use ($search) {
                      $oq->where('order_number', 'like', "%{$search}%")
                        ->orWhere('invoice_number', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%");
                  });
            });
        }

        $payments = $query->paginate(15)->withQueryString();

        $totalCollected = (float) FinancialTransaction::where('type', 'customer_payment')->sum('amount');
        $accounts = FinancialAccount::where('is_active', true)->get();
        $unpaidOrders = Order::whereIn('payment_status', ['Pending', 'Partially Paid', 'unpaid'])->get();

        return view('admin.finance.customer-payments.index', [
            'payments' => $payments,
            'totalCollected' => $totalCollected,
            'accounts' => $accounts,
            'unpaidOrders' => $unpaidOrders,
            'search' => $search,
        ]);
    }

    /**
     * Store incoming customer payment.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'financial_account_id' => 'required|exists:financial_accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|max:50',
            'payment_date' => 'required|date',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $order = Order::findOrFail($validated['order_id']);
        $account = FinancialAccount::findOrFail($validated['financial_account_id']);

        $this->financeService->recordCustomerPayment($order, (float) $validated['amount'], $account, $validated);

        return redirect()->back()->with('status', __('finance.success_payment'));
    }

    /**
     * Print official receipt voucher.
     */
    public function voucher(int $id): View
    {
        $payment = FinancialTransaction::with(['account', 'customer', 'order.items'])
            ->where('type', 'customer_payment')
            ->findOrFail($id);

        return view('admin.finance.customer-payments.voucher', [
            'payment' => $payment,
        ]);
    }
}
