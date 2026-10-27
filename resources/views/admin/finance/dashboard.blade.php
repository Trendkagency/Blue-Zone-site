<x-layouts.admin 
    :pageTitle="__('finance.dashboard')" 
    :pageSubtitle="__('finance.dashboard_subtitle')"
    :breadcrumbs="[
        __('finance.finance') => route('admin.finance.dashboard'),
        __('finance.dashboard') => route('admin.finance.dashboard')
    ]"
>
    <x-slot name="actions">
        <div class="flex items-center gap-2.5 flex-wrap">
            <button type="button" onclick="document.getElementById('modal-quick-expense').showModal()" class="btn btn-outline font-bold shadow-sm">
                <i class="fa-solid fa-receipt mr-1.5 ml-1.5 text-rose-500"></i>
                {{ __('finance.new_expense') }}
            </button>
            <button type="button" onclick="document.getElementById('modal-quick-transfer').showModal()" class="btn btn-secondary font-bold">
                <i class="fa-solid fa-arrow-right-arrow-left mr-1.5 ml-1.5 text-indigo-500"></i>
                {{ __('finance.transfer_funds') }}
            </button>
            <a href="{{ route('admin.finance.purchase-invoices.create') }}" class="btn btn-primary font-bold shadow-md">
                <i class="fa-solid fa-file-circle-plus mr-1.5 ml-1.5"></i>
                {{ __('finance.create_purchase_invoice') }}
            </a>
        </div>
    </x-slot>

    @if(session('status'))
        <div class="alert alert-success shadow-sm mb-6 flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-500 text-lg"></i>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger shadow-sm mb-6">
            <ul class="m-0 list-disc list-inside text-xs font-bold text-rose-600">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- 1. Executive Treasury KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        
        <!-- Total Liquid Treasury Balance -->
        <div class="card p-5 border border-slate-200 dark:border-slate-800 rounded-2xl bg-white dark:bg-[#062B49] shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    {{ __('finance.total_treasury_balance') }}
                </span>
                <div class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <i class="fa-solid fa-vault text-base"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-white font-mono">
                {{ number_format($totalTreasuryBalance, 2) }} <span class="text-xs font-sans text-slate-400">{{ __('finance.sar') }}</span>
            </div>
            <div class="text-xs text-slate-500 dark:text-slate-400 mt-2 flex items-center gap-1.5">
                <span class="text-emerald-500 font-bold"><i class="fa-solid fa-building-columns"></i> {{ $accounts->count() }}</span>
                <span>{{ __('finance.liquid_funds') }}</span>
            </div>
        </div>

        <!-- Accounts Receivables (Customer Invoices) -->
        <div class="card p-5 border border-slate-200 dark:border-slate-800 rounded-2xl bg-white dark:bg-[#062B49] shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    {{ __('finance.customer_receivables') }}
                </span>
                <div class="w-9 h-9 rounded-xl bg-sky-500/10 text-sky-600 dark:text-sky-400 flex items-center justify-center">
                    <i class="fa-solid fa-file-invoice-dollar text-base"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-sky-600 dark:text-sky-400 font-mono">
                {{ number_format($totalReceivables, 2) }} <span class="text-xs font-sans text-slate-400">{{ __('finance.sar') }}</span>
            </div>
            <div class="text-xs text-slate-500 dark:text-slate-400 mt-2 flex items-center gap-1.5">
                <span class="text-amber-500 font-bold">{{ $unpaidOrdersCount }}</span>
                <span>{{ __('finance.unpaid_customer_invoices') }}</span>
            </div>
        </div>

        <!-- Accounts Payables (Supplier Bills) -->
        <div class="card p-5 border border-slate-200 dark:border-slate-800 rounded-2xl bg-white dark:bg-[#062B49] shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    {{ __('finance.supplier_payables') }}
                </span>
                <div class="w-9 h-9 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                    <i class="fa-solid fa-money-bill-transfer text-base"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-amber-500 font-mono">
                {{ number_format($totalPayables, 2) }} <span class="text-xs font-sans text-slate-400">{{ __('finance.sar') }}</span>
            </div>
            <div class="text-xs text-slate-500 dark:text-slate-400 mt-2 flex items-center gap-1.5">
                <span class="text-rose-500 font-bold">{{ $unpaidBillsCount }}</span>
                <span>{{ __('finance.unpaid_supplier_bills') }}</span>
            </div>
        </div>

        <!-- Net Operating Cash Flow -->
        <div class="card p-5 border border-slate-200 dark:border-slate-800 rounded-2xl bg-white dark:bg-[#062B49] shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    {{ __('finance.net_cashflow') }}
                </span>
                <div class="w-9 h-9 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center">
                    <i class="fa-solid fa-chart-line text-base"></i>
                </div>
            </div>
            <div class="text-2xl font-black {{ $netCashflow >= 0 ? 'text-emerald-500' : 'text-rose-500' }} font-mono">
                {{ ($netCashflow >= 0 ? '+' : '') . number_format($netCashflow, 2) }} <span class="text-xs font-sans text-slate-400">{{ __('finance.sar') }}</span>
            </div>
            <div class="text-xs text-slate-500 dark:text-slate-400 mt-2">
                {{ __('finance.net_surplus_deficit') }}
            </div>
        </div>

    </div>

    <!-- 2. Accounts Breakdown Cards -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-black uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-2 m-0">
                <i class="fa-solid fa-building-columns text-emerald-500"></i>
                {{ __('finance.accounts') }}
            </h3>
            <a href="{{ route('admin.finance.accounts.index') }}" class="text-xs font-bold text-sky-600 dark:text-sky-400 hover:underline">
                {{ __('finance.accounts_statement') ?? 'Manage All Accounts' }} &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($accounts as $acc)
                <div class="card p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-[#062B49] shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between text-xs text-slate-400 mb-1">
                            <span class="font-mono font-bold">{{ $acc->code }}</span>
                            <span class="badge {{ $acc->type === 'bank' ? 'badge-primary' : ($acc->type === 'cash' ? 'badge-accent' : 'badge-neutral') }} text-[10px]">
                                {{ __('finance.type_' . $acc->type) }}
                            </span>
                        </div>
                        <h4 class="text-sm font-bold text-slate-900 dark:text-white truncate m-0" title="{{ $acc->localized_name }}">
                            {{ $acc->localized_name }}
                        </h4>
                        @if($acc->bank_name)
                            <div class="text-[11px] text-slate-400 mt-0.5 truncate">{{ $acc->bank_name }}</div>
                        @endif
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-baseline justify-between">
                        <span class="text-xs text-slate-400">{{ __('finance.current_balance') }}:</span>
                        <span class="text-base font-black font-mono text-emerald-600 dark:text-emerald-400">
                            {{ number_format($acc->current_balance, 2) }} SAR
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- 3. Cashflow Trend & Expenses by Category Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        
        <!-- 30-Day Cashflow Dynamics Canvas Chart -->
        <div class="lg:col-span-2 card p-5 border border-slate-200 dark:border-slate-800 rounded-2xl bg-white dark:bg-[#062B49] shadow-sm">
            <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
                <h3 class="text-sm font-black text-slate-900 dark:text-white flex items-center gap-2 m-0">
                    <i class="fa-solid fa-chart-area text-sky-500"></i>
                    {{ __('finance.days_30_trend') }}
                </h3>
                <div class="flex items-center gap-4 text-xs font-bold">
                    <span class="flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> {{ __('finance.cash_inflow') }}
                    </span>
                    <span class="flex items-center gap-1.5 text-rose-500">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span> {{ __('finance.cash_outflow') }}
                    </span>
                </div>
            </div>

            <div class="w-full h-64 relative">
                <canvas id="cashflowChart"></canvas>
            </div>
        </div>

        <!-- Expenses by Category Breakdown -->
        <div class="card p-5 border border-slate-200 dark:border-slate-800 rounded-2xl bg-white dark:bg-[#062B49] shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-black text-slate-900 dark:text-white flex items-center gap-2 m-0">
                        <i class="fa-solid fa-pie-chart text-purple-500"></i>
                        {{ __('finance.expenses') }}
                    </h3>
                    <a href="{{ route('admin.finance.expenses.index') }}" class="text-xs text-sky-500 font-bold hover:underline">
                        {{ __('finance.all') }} &rarr;
                    </a>
                </div>

                <div class="space-y-3">
                    @forelse($expensesByCategory as $expCat)
                        @php
                            $cat = $expCat->category;
                            $pct = $totalExpenses > 0 ? round(($expCat->total / $totalExpenses) * 100, 1) : 0;
                        @endphp
                        <div>
                            <div class="flex justify-between text-xs font-bold mb-1">
                                <span class="text-slate-700 dark:text-slate-300 truncate" title="{{ $cat?->localized_name }}">
                                    {{ $cat?->localized_name ?? 'General' }}
                                </span>
                                <span class="font-mono text-slate-900 dark:text-white">{{ number_format($expCat->total, 2) }} SAR</span>
                            </div>
                            <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2 overflow-hidden">
                                <div class="h-2 rounded-full" style="width: {{ $pct }}%; background-color: {{ $cat?->color ?? '#0A4F78' }};"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-slate-400 text-xs">
                            <i class="fa-solid fa-receipt text-2xl mb-1 opacity-50 block"></i>
                            No expenses recorded yet.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 mt-4 flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500">{{ __('finance.total_operating_expenses') }}:</span>
                <span class="font-mono font-black text-base text-slate-900 dark:text-white">{{ number_format($totalExpenses, 2) }} SAR</span>
            </div>
        </div>

    </div>

    <!-- 4. Recent Financial Transactions Ledger Table -->
    <div class="card overflow-hidden shadow-sm border border-slate-200 dark:border-slate-800 rounded-2xl bg-white dark:bg-[#062B49]">
        <div class="card-header p-5 bg-slate-50/70 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between flex-wrap gap-2">
            <h3 class="text-base font-black text-slate-900 dark:text-white flex items-center gap-2 m-0">
                <i class="fa-solid fa-list-check text-emerald-600 dark:text-emerald-400"></i>
                {{ __('finance.recent_transactions') }}
            </h3>
            <span class="text-xs text-slate-400">
                {{ __('finance.liquid_funds') }}
            </span>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/80 text-xs uppercase text-slate-500 dark:text-slate-400 font-bold border-b border-slate-200 dark:border-slate-800">
                        <th class="p-4">{{ __('finance.transaction_number') ?? 'TXN #' }}</th>
                        <th class="p-4">{{ __('finance.date') }}</th>
                        <th class="p-4">{{ __('finance.account_name') }}</th>
                        <th class="p-4">{{ __('finance.notes') }}</th>
                        <th class="p-4 text-center">{{ __('finance.payment_method') }}</th>
                        <th class="p-4 text-right rtl:text-left">{{ __('finance.amount') }}</th>
                        <th class="p-4 text-center">{{ __('finance.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 text-sm">
                    @forelse($recentTransactions as $tx)
                        @php
                            $isCredit = $tx->direction === 'credit';
                        @endphp
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
                            <!-- TXN # -->
                            <td class="p-4 font-mono font-bold text-xs text-slate-900 dark:text-white">
                                {{ $tx->transaction_number }}
                                @if($tx->receipt_voucher_number)
                                    <div class="text-[10px] text-slate-400">{{ $tx->receipt_voucher_number }}</div>
                                @endif
                            </td>

                            <!-- Date -->
                            <td class="p-4 text-xs font-mono text-slate-500 dark:text-slate-400">
                                {{ $tx->transaction_date ? $tx->transaction_date->format('Y-m-d') : '—' }}
                            </td>

                            <!-- Account -->
                            <td class="p-4">
                                <span class="font-bold text-slate-800 dark:text-slate-200 text-xs block truncate max-w-[180px]">
                                    {{ $tx->account?->localized_name }}
                                </span>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $tx->account?->code }}</span>
                            </td>

                            <!-- Notes / Description -->
                            <td class="p-4 text-xs text-slate-600 dark:text-slate-300 max-w-xs truncate">
                                {{ $tx->notes ?? 'Financial movement' }}
                            </td>

                            <!-- Payment Method -->
                            <td class="p-4 text-center">
                                <span class="badge badge-neutral text-xs uppercase font-mono">
                                    {{ $tx->payment_method }}
                                </span>
                            </td>

                            <!-- Amount with Inflow / Outflow Color -->
                            <td class="p-4 text-right rtl:text-left font-mono font-black text-sm {{ $isCredit ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                {{ $isCredit ? '+' : '-' }}{{ number_format($tx->amount, 2) }} SAR
                            </td>

                            <!-- Action Voucher -->
                            <td class="p-4 text-center">
                                @if($tx->type === 'customer_payment')
                                    <a href="{{ route('admin.finance.customer-payments.voucher', $tx->id) }}" target="_blank" class="btn btn-secondary btn-sm p-1.5 rounded-lg" title="Print Receipt">
                                        <i class="fa-solid fa-print text-xs text-emerald-500"></i>
                                    </a>
                                @elseif($tx->type === 'supplier_payment')
                                    <a href="{{ route('admin.finance.supplier-payments.voucher', $tx->id) }}" target="_blank" class="btn btn-secondary btn-sm p-1.5 rounded-lg" title="Print Voucher">
                                        <i class="fa-solid fa-print text-xs text-amber-500"></i>
                                    </a>
                                @else
                                    <span class="text-slate-300 dark:text-slate-600 text-xs">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400 text-xs">
                                No financial transactions recorded yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Expense Modal -->
    <dialog id="modal-quick-expense" class="modal">
        <div class="modal-box rounded-2xl max-w-lg bg-white dark:bg-[#062B49] text-slate-900 dark:text-white p-6">
            <h3 class="font-black text-base flex items-center gap-2 mb-4">
                <i class="fa-solid fa-receipt text-rose-500"></i>
                {{ __('finance.new_expense') }}
            </h3>
            <form method="POST" action="{{ route('admin.finance.expenses.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">{{ __('finance.expense_category') }} *</label>
                    <select name="expense_category_id" required class="form-select text-xs w-full rounded-xl">
                        @foreach($expenseCategories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->localized_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">{{ __('finance.paid_from') }} *</label>
                    <select name="financial_account_id" required class="form-select text-xs w-full rounded-xl">
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->localized_name }} ({{ number_format($acc->current_balance, 2) }} SAR)</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">{{ __('finance.expense_title') }} (EN) *</label>
                        <input type="text" name="title_en" required placeholder="e.g. Lab Assay Testing" class="form-control text-xs w-full rounded-xl">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">{{ __('finance.expense_title') }} (AR)</label>
                        <input type="text" name="title_ar" placeholder="مثال: فحص مخبري cGMP" class="form-control text-xs w-full rounded-xl">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">{{ __('finance.amount_before_tax') }} (SAR) *</label>
                        <input type="number" step="0.01" name="amount" id="modal-exp-amount" required oninput="calculateExpenseTotal()" class="form-control text-xs w-full rounded-xl">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">{{ __('finance.total_expense_amount') }} (SAR) *</label>
                        <input type="number" step="0.01" name="total_amount" id="modal-exp-total" required class="form-control text-xs w-full rounded-xl font-bold font-mono">
                    </div>
                </div>
                <input type="hidden" name="tax_amount" id="modal-exp-tax" value="0">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">{{ __('finance.payment_method') }} *</label>
                        <select name="payment_method" class="form-select text-xs w-full rounded-xl">
                            <option value="bank_transfer">Bank Transfer / حوالة بنكية</option>
                            <option value="mada">Mada / بطاقة مدى</option>
                            <option value="cash">Cash / نقدي</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">{{ __('finance.date') }} *</label>
                        <input type="date" name="expense_date" value="{{ date('Y-m-d') }}" required class="form-control text-xs w-full rounded-xl">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">{{ __('finance.beneficiary') }}</label>
                    <input type="text" name="beneficiary_name" placeholder="Vendor / Payee Name" class="form-control text-xs w-full rounded-xl">
                </div>
                <div class="modal-action flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('modal-quick-expense').close()" class="btn btn-secondary btn-sm rounded-xl">
                        {{ __('finance.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-xl font-bold">
                        <i class="fa-solid fa-check mr-1 ml-1"></i> {{ __('finance.save') }}
                    </button>
                </div>
            </form>
        </div>
    </dialog>

    <!-- Quick Transfer Modal -->
    <dialog id="modal-quick-transfer" class="modal">
        <div class="modal-box rounded-2xl max-w-md bg-white dark:bg-[#062B49] text-slate-900 dark:text-white p-6">
            <h3 class="font-black text-base flex items-center gap-2 mb-4">
                <i class="fa-solid fa-arrow-right-arrow-left text-indigo-500"></i>
                {{ __('finance.transfer_funds') }}
            </h3>
            <form method="POST" action="{{ route('admin.finance.accounts.transfer') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">{{ __('finance.from_account') }} *</label>
                    <select name="from_account_id" required class="form-select text-xs w-full rounded-xl">
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->localized_name }} ({{ number_format($acc->current_balance, 2) }} SAR)</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">{{ __('finance.to_account') }} *</label>
                    <select name="to_account_id" required class="form-select text-xs w-full rounded-xl">
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->localized_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">{{ __('finance.transfer_amount') }} (SAR) *</label>
                        <input type="number" step="0.01" name="amount" required min="1" class="form-control text-xs w-full rounded-xl font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1">{{ __('finance.transfer_date') }} *</label>
                        <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="form-control text-xs w-full rounded-xl">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-1">{{ __('finance.reference_number') }}</label>
                    <input type="text" name="reference_number" placeholder="Bank confirmation / reference #" class="form-control text-xs w-full rounded-xl">
                </div>
                <div class="modal-action flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('modal-quick-transfer').close()" class="btn btn-secondary btn-sm rounded-xl">
                        {{ __('finance.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-xl font-bold">
                        <i class="fa-solid fa-arrow-right-arrow-left mr-1 ml-1"></i> {{ __('finance.confirm') }}
                    </button>
                </div>
            </form>
        </div>
    </dialog>

    <!-- Chart.js Engine -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function calculateExpenseTotal() {
            const amt = parseFloat(document.getElementById('modal-exp-amount').value) || 0;
            const tax = Math.round(amt * 0.15 * 100) / 100;
            document.getElementById('modal-exp-tax').value = tax;
            document.getElementById('modal-exp-total').value = (amt + tax).toFixed(2);
        }

        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('cashflowChart').getContext('2d');
            const labels = @json($chartDays);
            const incomeData = @json($chartIncome);
            const expenseData = @json($chartExpense);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Inflow / المقبوضات',
                            data: incomeData,
                            borderColor: '#10B981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            fill: true,
                            tension: 0.35,
                            borderWidth: 2,
                            pointRadius: 2,
                        },
                        {
                            label: 'Outflow / المصروفات',
                            data: expenseData,
                            borderColor: '#EF4444',
                            backgroundColor: 'rgba(239, 68, 68, 0.08)',
                            fill: true,
                            tension: 0.35,
                            borderWidth: 2,
                            pointRadius: 2,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 10 } }
                        },
                        y: {
                            grid: { color: 'rgba(150, 150, 150, 0.1)' },
                            ticks: {
                                font: { size: 10 },
                                callback: function(val) { return val.toLocaleString() + ' SAR'; }
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-layouts.admin>
