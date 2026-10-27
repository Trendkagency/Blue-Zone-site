<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\FinancialAccount;
use App\Services\FinanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function __construct(protected FinanceService $financeService)
    {
    }

    /**
     * List all operational expenses.
     */
    public function index(Request $request): View
    {
        $categoryId = $request->query('category_id', 'all');
        $accountId = $request->query('account_id', 'all');
        $search = $request->query('search');

        $query = Expense::with(['category', 'account', 'creator'])->latest('id');

        if ($categoryId && $categoryId !== 'all') {
            $query->where('expense_category_id', $categoryId);
        }

        if ($accountId && $accountId !== 'all') {
            $query->where('financial_account_id', $accountId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('expense_number', 'like', "%{$search}%")
                  ->orWhere('title_en', 'like', "%{$search}%")
                  ->orWhere('title_ar', 'like', "%{$search}%")
                  ->orWhere('beneficiary_name', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%");
            });
        }

        $expenses = $query->paginate(15)->withQueryString();

        $categories = ExpenseCategory::where('is_active', true)->get();
        $accounts = FinancialAccount::where('is_active', true)->get();

        $totalExpenses = (float) Expense::sum('total_amount');
        $totalVat = (float) Expense::sum('tax_amount');

        return view('admin.finance.expenses.index', [
            'expenses' => $expenses,
            'categories' => $categories,
            'accounts' => $accounts,
            'totalExpenses' => $totalExpenses,
            'totalVat' => $totalVat,
            'selectedCategory' => $categoryId,
            'selectedAccount' => $accountId,
            'search' => $search,
        ]);
    }

    /**
     * Store new expense with real-time account deduction.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'financial_account_id' => 'required|exists:financial_accounts,id',
            'title_en' => 'required|string|max:200',
            'title_ar' => 'nullable|string|max:200',
            'amount' => 'required|numeric|min:0.01',
            'tax_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'payment_method' => 'required|string|max:50',
            'beneficiary_name' => 'nullable|string|max:150',
            'reference_number' => 'nullable|string|max:100',
            'receipt' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:4096',
            'notes' => 'nullable|string',
        ]);

        if ($request->hasFile('receipt')) {
            $path = $request->file('receipt')->store('finance/expenses', 'public');
            $validated['receipt_path'] = '/storage/' . $path;
        }

        $this->financeService->recordExpense($validated);

        return redirect()->back()->with('status', __('finance.success_created'));
    }
}
