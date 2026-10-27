<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Models\ExpenseCategory;
use App\Models\FinancialAccount;
use App\Models\Order;
use App\Models\PurchaseInvoice;
use App\Services\FinanceService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinanceDashboardController extends Controller
{
    public function __construct(protected FinanceService $financeService)
    {
    }

    /**
     * Executive Finance & Treasury Command Center.
     */
    public function index(Request $request): View
    {
        $analytics = $this->financeService->getDashboardAnalytics();

        $accounts = FinancialAccount::where('is_active', true)->orderBy('type')->get();
        $expenseCategories = ExpenseCategory::where('is_active', true)->get();
        $unpaidOrders = Order::whereIn('payment_status', ['Pending', 'Partially Paid', 'unpaid'])
            ->latest('id')
            ->limit(20)
            ->get();
        $unpaidBills = PurchaseInvoice::with('supplier')
            ->whereIn('payment_status', ['unpaid', 'partially_paid'])
            ->latest('id')
            ->limit(20)
            ->get();

        return view('admin.finance.dashboard', array_merge($analytics, [
            'accounts' => $accounts,
            'expenseCategories' => $expenseCategories,
            'unpaidOrders' => $unpaidOrders,
            'unpaidBills' => $unpaidBills,
        ]));
    }
}
