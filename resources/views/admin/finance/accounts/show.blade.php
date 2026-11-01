<x-layouts.admin :title="$account->localized_name . ' — ' . __('finance.account_statement')">
    <div class="space-y-6" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        
        <!-- Top Breadcrumbs & Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
            <div>
                <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 mb-1">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-primary transition">{{ __('admin.menu.dashboard') }}</a>
                    <span>/</span>
                    <a href="{{ route('admin.finance.dashboard') }}" class="hover:text-primary transition">{{ __('finance.finance') }}</a>
                    <span>/</span>
                    <a href="{{ route('admin.finance.accounts.index') }}" class="hover:text-primary transition">{{ __('finance.accounts') }}</a>
                    <span>/</span>
                    <span class="text-slate-900 dark:text-slate-100 font-bold">{{ $account->code }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2.5">
                    <span class="w-2.5 h-8 bg-[#0A4F78] rounded-sm"></span>
                    {{ $account->localized_name }}
                </h1>
                <p class="text-xs sm:text-sm font-medium text-slate-600 dark:text-slate-300 mt-1 font-mono">
                    {{ $account->bank_name ? $account->bank_name . ' • ' : '' }}
                    {{ $account->account_number ? 'Acc: ' . $account->account_number . ' • ' : '' }}
                    {{ $account->iban ? 'IBAN: ' . $account->iban : '' }}
                </p>
            </div>
            <div class="flex items-center gap-2.5 flex-wrap">
                <button type="button" onclick="window.print()" class="btn btn-secondary border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 font-bold px-4 py-2 rounded-xl flex items-center gap-2 text-xs sm:text-sm shadow-sm transition">
                    <i class="fa-solid fa-print text-sm text-sky-600 dark:text-sky-400"></i>
                    <span>Print Statement</span>
                </button>
                <a href="{{ route('admin.finance.accounts.index') }}" class="btn btn-outline border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 font-bold px-4 py-2 rounded-xl text-xs sm:text-sm transition">
                    <i class="fa-solid fa-arrow-left rtl:rotate-180 mr-1 ml-1 text-xs"></i>
                    <span>Back to Accounts</span>
                </a>
            </div>
        </div>

        <!-- Account Stats & Balance Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 sm:gap-6">
            <div class="card p-5 sm:p-6 bg-white dark:bg-[#062B49] border border-slate-200 dark:border-slate-800 rounded-2xl sm:rounded-3xl shadow-sm relative overflow-hidden">
                <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-emerald-500 to-sky-500"></div>
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block mb-1">
                    {{ __('finance.current_balance') }}
                </span>
                <div class="text-3xl sm:text-4xl font-black text-emerald-600 dark:text-emerald-400 font-mono tracking-tight">
                    {{ number_format($account->current_balance, 2) }} <span class="text-sm font-bold text-slate-500 dark:text-slate-400">SAR</span>
                </div>
                <div class="mt-3 text-xs text-slate-500 dark:text-slate-400 pt-2 border-t border-slate-100 dark:border-slate-800/80">
                    Initial Balance: <span class="font-mono font-bold text-slate-800 dark:text-slate-200">{{ number_format($account->initial_balance, 2) }} SAR</span>
                </div>
            </div>

            <div class="card p-5 sm:p-6 bg-white dark:bg-[#062B49] border border-slate-200 dark:border-slate-800 rounded-2xl sm:rounded-3xl shadow-sm relative overflow-hidden">
                <div class="absolute top-0 inset-x-0 h-1 bg-emerald-500"></div>
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block mb-1">
                    Total Inflow (Credits)
                </span>
                <div class="text-2xl sm:text-3xl font-black text-emerald-600 dark:text-emerald-400 font-mono tracking-tight">
                    +{{ number_format($transactions->where('direction', 'credit')->sum('amount'), 2) }} <span class="text-xs font-bold text-slate-500 dark:text-slate-400">SAR</span>
                </div>
                <div class="mt-3 text-xs text-slate-500 dark:text-slate-400 pt-2 border-t border-slate-100 dark:border-slate-800/80">
                    Collections & Transferred In
                </div>
            </div>

            <div class="card p-5 sm:p-6 bg-white dark:bg-[#062B49] border border-slate-200 dark:border-slate-800 rounded-2xl sm:rounded-3xl shadow-sm relative overflow-hidden">
                <div class="absolute top-0 inset-x-0 h-1 bg-rose-500"></div>
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block mb-1">
                    Total Outflow (Debits)
                </span>
                <div class="text-2xl sm:text-3xl font-black text-rose-600 dark:text-rose-400 font-mono tracking-tight">
                    -{{ number_format($transactions->where('direction', 'debit')->sum('amount'), 2) }} <span class="text-xs font-bold text-slate-500 dark:text-slate-400">SAR</span>
                </div>
                <div class="mt-3 text-xs text-slate-500 dark:text-slate-400 pt-2 border-t border-slate-100 dark:border-slate-800/80">
                    Disbursements, Expenses & Transfers
                </div>
            </div>
        </div>

        <!-- Ledger Statement Table Card -->
        <div class="card bg-white dark:bg-[#062B49] border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden shadow-sm">
            <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                <h3 class="font-bold text-sm text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2 m-0">
                    <i class="fa-solid fa-list-ol text-sky-600 dark:text-sky-400"></i>
                    {{ __('finance.account_statement') }} ({{ $transactions->total() }} Records)
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="table w-full text-slate-800 dark:text-slate-200 text-xs">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-900/60 text-slate-500 dark:text-slate-400 font-bold border-b border-slate-200 dark:border-slate-800 uppercase tracking-wider text-[11px]">
                            <th class="py-3 px-4">{{ __('finance.date') }}</th>
                            <th class="py-3 px-4">{{ __('finance.payment_number') }}</th>
                            <th class="py-3 px-4">Transaction Type</th>
                            <th class="py-3 px-4">Counterparty / Description</th>
                            <th class="py-3 px-4">{{ __('finance.payment_method') }}</th>
                            <th class="py-3 px-4 text-right rtl:text-left text-emerald-600 dark:text-emerald-400">Credit In (+)</th>
                            <th class="py-3 px-4 text-right rtl:text-left text-rose-600 dark:text-rose-400">Debit Out (-)</th>
                            <th class="py-3 px-4 text-center">{{ __('finance.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 font-medium">
                        @forelse($transactions as $trx)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="py-3.5 px-4 font-mono text-slate-600 dark:text-slate-400 whitespace-nowrap">
                                    {{ $trx->transaction_date ? $trx->transaction_date->format('Y-m-d') : '—' }}
                                </td>
                                <td class="py-3.5 px-4 font-mono font-bold text-slate-900 dark:text-white whitespace-nowrap">
                                    {{ $trx->transaction_number }}
                                </td>
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                                        {{ $trx->type === 'customer_payment' ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60' : '' }}
                                        {{ $trx->type === 'supplier_payment' ? 'bg-sky-50 dark:bg-sky-950/40 text-sky-700 dark:text-sky-300 border border-sky-200 dark:border-sky-800/60' : '' }}
                                        {{ $trx->type === 'expense' ? 'bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800/60' : '' }}
                                        {{ str_starts_with($trx->type, 'transfer_') ? 'bg-purple-50 dark:bg-purple-950/40 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800/60' : '' }}
                                    ">
                                        {{ str_replace('_', ' ', $trx->type) }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4">
                                    @if($trx->customer)
                                        <div class="font-bold text-slate-900 dark:text-white">{{ $trx->customer->name }}</div>
                                    @elseif($trx->supplier)
                                        <div class="font-bold text-slate-900 dark:text-white">{{ $trx->supplier->name }}</div>
                                    @elseif($trx->expense)
                                        <div class="font-bold text-slate-900 dark:text-white">{{ $trx->expense->localized_title }}</div>
                                    @elseif($trx->destinationAccount)
                                        <div class="font-bold text-slate-900 dark:text-white">To: {{ $trx->destinationAccount->localized_name }}</div>
                                    @endif
                                    @if($trx->reference_number)
                                        <div class="text-[10px] font-mono text-sky-600 dark:text-sky-400">Ref: {{ $trx->reference_number }}</div>
                                    @endif
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 truncate max-w-xs">{{ $trx->notes ?? '—' }}</div>
                                </td>
                                <td class="py-3.5 px-4 font-mono uppercase text-slate-600 dark:text-slate-400 whitespace-nowrap">{{ $trx->payment_method }}</td>
                                <td class="py-3.5 px-4 text-right rtl:text-left font-mono font-bold text-emerald-600 dark:text-emerald-400 whitespace-nowrap">
                                    {{ $trx->direction === 'credit' ? '+' . number_format($trx->amount, 2) . ' SAR' : '—' }}
                                </td>
                                <td class="py-3.5 px-4 text-right rtl:text-left font-mono font-bold text-rose-600 dark:text-rose-400 whitespace-nowrap">
                                    {{ $trx->direction === 'debit' ? '-' . number_format($trx->amount, 2) . ' SAR' : '—' }}
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    @if($trx->type === 'customer_payment')
                                        <a href="{{ route('admin.finance.customer-payments.voucher', $trx->id) }}" target="_blank" class="btn btn-xs btn-outline border-slate-300 dark:border-slate-700 text-emerald-600 dark:text-emerald-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg p-1.5" title="Print Receipt Voucher">
                                            <i class="fa-solid fa-receipt"></i>
                                        </a>
                                    @elseif($trx->type === 'supplier_payment')
                                        <a href="{{ route('admin.finance.supplier-payments.voucher', $trx->id) }}" target="_blank" class="btn btn-xs btn-outline border-slate-300 dark:border-slate-700 text-sky-600 dark:text-sky-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg p-1.5" title="Print Disbursement Voucher">
                                            <i class="fa-solid fa-receipt"></i>
                                        </a>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-slate-400 dark:text-slate-500">
                                    <i class="fa-solid fa-receipt text-3xl mb-2 block opacity-40"></i>
                                    No transaction movements recorded in this account statement yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($transactions->hasPages())
                <div class="p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/40">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
