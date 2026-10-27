<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Models\FinancialAccount;
use App\Models\FinancialTransaction;
use App\Services\FinanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinancialAccountController extends Controller
{
    public function __construct(protected FinanceService $financeService)
    {
    }

    /**
     * List all corporate cash and bank accounts.
     */
    public function index(): View
    {
        $accounts = FinancialAccount::withCount('transactions')->latest('id')->get();
        $totalLiquidity = (float) $accounts->where('is_active', true)->sum('current_balance');

        return view('admin.finance.accounts.index', [
            'accounts' => $accounts,
            'totalLiquidity' => $totalLiquidity,
        ]);
    }

    /**
     * Store new account.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:financial_accounts,code',
            'name_en' => 'required|string|max:150',
            'name_ar' => 'required|string|max:150',
            'type' => 'required|in:bank,cash,gateway',
            'bank_name' => 'nullable|string|max:150',
            'account_number' => 'nullable|string|max:100',
            'iban' => 'nullable|string|max:50',
            'currency' => 'required|string|max:10',
            'initial_balance' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['current_balance'] = $validated['initial_balance'];
        $validated['is_active'] = true;

        FinancialAccount::create($validated);

        return redirect()->back()->with('status', __('finance.success_created'));
    }

    /**
     * Show account statement ledger.
     */
    public function show(int $id): View
    {
        $account = FinancialAccount::findOrFail($id);

        $transactions = FinancialTransaction::with(['customer', 'supplier', 'order', 'purchaseInvoice', 'expense', 'destinationAccount'])
            ->where('financial_account_id', $account->id)
            ->latest('transaction_date')
            ->latest('id')
            ->paginate(20);

        $otherAccounts = FinancialAccount::where('id', '!=', $account->id)->where('is_active', true)->get();

        return view('admin.finance.accounts.show', [
            'account' => $account,
            'transactions' => $transactions,
            'otherAccounts' => $otherAccounts,
        ]);
    }

    /**
     * Transfer funds between two accounts.
     */
    public function transfer(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'from_account_id' => 'required|exists:financial_accounts,id',
            'to_account_id' => 'required|exists:financial_accounts,id|different:from_account_id',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $from = FinancialAccount::findOrFail($validated['from_account_id']);
        $to = FinancialAccount::findOrFail($validated['to_account_id']);

        if ((float) $from->current_balance < (float) $validated['amount']) {
            return redirect()->back()->withErrors(['amount' => 'Insufficient funds in the source account.']);
        }

        $this->financeService->transferFunds($from, $to, (float) $validated['amount'], $validated);

        return redirect()->back()->with('status', __('finance.success_transfer'));
    }
}
