<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Models\FinancialAccount;
use App\Models\Order;
use App\Services\FinanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalesInvoiceController extends Controller
{
    public function __construct(protected FinanceService $financeService)
    {
    }

    /**
     * List all commercial sales invoices with payment reconciliation.
     */
    public function index(Request $request): View
    {
        $status = $request->query('payment_status', 'all');
        $search = $request->query('search');

        $query = Order::with(['items', 'customer', 'financialPayments'])->latest('id');

        if ($status && $status !== 'all') {
            $query->where('payment_status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        $invoices = $query->paginate(15)->withQueryString();

        $accounts = FinancialAccount::where('is_active', true)->get();

        // Metrics
        $allOrders = Order::all();
        $totalInvoiced = (float) $allOrders->sum('total');
        $paidOrdersCount = $allOrders->where('payment_status', 'Paid')->count();
        $unpaidCount = $allOrders->whereIn('payment_status', ['Pending', 'Partially Paid', 'unpaid'])->count();

        return view('admin.finance.sales-invoices.index', [
            'invoices' => $invoices,
            'accounts' => $accounts,
            'totalInvoiced' => $totalInvoiced,
            'paidOrdersCount' => $paidOrdersCount,
            'unpaidCount' => $unpaidCount,
            'selectedPayment' => $status,
            'search' => $search,
        ]);
    }

    /**
     * Record a customer payment directly against an invoice/order.
     */
    public function receivePayment(Request $request, int $orderId): RedirectResponse
    {
        $order = Order::findOrFail($orderId);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'financial_account_id' => 'required|exists:financial_accounts,id',
            'payment_method' => 'required|string|max:50',
            'payment_date' => 'required|date',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $account = FinancialAccount::findOrFail($validated['financial_account_id']);

        $this->financeService->recordCustomerPayment($order, (float) $validated['amount'], $account, $validated);

        return redirect()->back()->with('status', __('finance.success_payment'));
    }
}
