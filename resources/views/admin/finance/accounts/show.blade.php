<x-layouts.admin :title="$account->localized_name . ' — ' . __('finance.account_statement')">
    <div class="space-y-6">
        <!-- Top Breadcrumbs & Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-1">
                    <a href="{{ route('admin.finance.dashboard') }}" class="hover:text-blue-500 transition-colors">{{ __('finance.finance') }}</a>
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    <a href="{{ route('admin.finance.accounts.index') }}" class="hover:text-blue-500 transition-colors">{{ __('finance.accounts') }}</a>
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    <span class="text-slate-200">{{ $account->code }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">{{ $account->localized_name }}</h1>
                <p class="text-sm text-slate-400 mt-1 font-mono">
                    {{ $account->bank_name ? $account->bank_name . ' • ' : '' }}
                    {{ $account->account_number ? 'Acc: ' . $account->account_number . ' • ' : '' }}
                    {{ $account->iban ? 'IBAN: ' . $account->iban : '' }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="window.print()" class="btn btn-outline border-slate-700 text-slate-300 hover:bg-slate-800 font-bold px-4 py-2.5 rounded-xl flex items-center gap-2">
                    <i class="fa-solid fa-print text-sm"></i>
                    <span>Print Statement</span>
                </button>
                <a href="{{ route('admin.finance.accounts.index') }}" class="btn btn-secondary font-bold px-4 py-2.5 rounded-xl">
                    Back to Accounts
                </a>
            </div>
        </div>

        <!-- Account Stats & Balance Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="card p-6 bg-gradient-to-br from-slate-900 via-blue-950/40 to-slate-900 border border-blue-500/30 rounded-3xl relative overflow-hidden shadow-xl">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">{{ __('finance.current_balance') }}</span>
                <div class="text-3xl sm:text-4xl font-black text-emerald-400 font-mono">
                    {{ number_format($account->current_balance, 2) }} <span class="text-base text-slate-400">SAR</span>
                </div>
                <div class="mt-3 text-xs text-slate-400">
                    Initial Balance: <span class="font-mono text-slate-200">{{ number_format($account->initial_balance, 2) }} SAR</span>
                </div>
            </div>

            <div class="card p-6 bg-slate-900 border border-slate-800 rounded-3xl shadow-xl">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Total Inflow (Credits)</span>
                <div class="text-2xl sm:text-3xl font-black text-emerald-400 font-mono">
                    +{{ number_format($transactions->where('direction', 'credit')->sum('amount'), 2) }} <span class="text-sm text-slate-400">SAR</span>
                </div>
                <div class="mt-3 text-xs text-slate-400">
                    Collections & Transferred in
                </div>
            </div>

            <div class="card p-6 bg-slate-900 border border-slate-800 rounded-3xl shadow-xl">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Total Outflow (Debits)</span>
                <div class="text-2xl sm:text-3xl font-black text-rose-400 font-mono">
                    -{{ number_format($transactions->where('direction', 'debit')->sum('amount'), 2) }} <span class="text-sm text-slate-400">SAR</span>
                </div>
                <div class="mt-3 text-xs text-slate-400">
                    Disbursements, expenses & transfers
                </div>
            </div>
        </div>

        <!-- Ledger Statement Table -->
        <div class="card bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
            <div class="p-4 border-b border-slate-800 flex items-center justify-between">
                <h3 class="font-bold text-sm text-white uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-list-ol text-blue-400"></i>
                    {{ __('finance.account_statement') }} ({{ $transactions->total() }} Records)
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="table w-full text-slate-300 text-xs">
                    <thead>
                        <tr class="bg-slate-950/60 text-slate-400 font-bold border-b border-slate-800 uppercase tracking-wider text-[11px]">
                            <th class="py-3 px-4">{{ __('finance.date') }}</th>
                            <th class="py-3 px-4">{{ __('finance.payment_number') }}</th>
                            <th class="py-3 px-4">Transaction Type</th>
                            <th class="py-3 px-4">Counterparty / Description</th>
                            <th class="py-3 px-4">{{ __('finance.payment_method') }}</th>
                            <th class="py-3 px-4 text-right rtl:text-left text-emerald-400">Credit In (+)</th>
                            <th class="py-3 px-4 text-right rtl:text-left text-rose-400">Debit Out (-)</th>
                            <th class="py-3 px-4 text-center">{{ __('finance.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 font-medium">
                        @forelse($transactions as $trx)
                            <tr class="hover:bg-slate-800/40 transition-colors">
                                <td class="py-3.5 px-4 font-mono text-slate-400">{{ $trx->transaction_date ? $trx->transaction_date->format('Y-m-d') : '—' }}</td>
                                <td class="py-3.5 px-4 font-mono font-bold text-white">{{ $trx->transaction_number }}</td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                                        {{ $trx->type === 'customer_payment' ? 'bg-emerald-900/40 text-emerald-300 border border-emerald-700/50' : '' }}
                                        {{ $trx->type === 'supplier_payment' ? 'bg-blue-900/40 text-blue-300 border border-blue-700/50' : '' }}
                                        {{ $trx->type === 'expense' ? 'bg-rose-900/40 text-rose-300 border border-rose-700/50' : '' }}
                                        {{ str_starts_with($trx->type, 'transfer_') ? 'bg-purple-900/40 text-purple-300 border border-purple-700/50' : '' }}
                                    ">
                                        {{ str_replace('_', ' ', $trx->type) }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4">
                                    @if($trx->customer)
                                        <div class="font-bold text-white">{{ $trx->customer->name }}</div>
                                    @elseif($trx->supplier)
                                        <div class="font-bold text-white">{{ $trx->supplier->name }}</div>
                                    @elseif($trx->expense)
                                        <div class="font-bold text-white">{{ $trx->expense->localized_title }}</div>
                                    @elseif($trx->destinationAccount)
                                        <div class="font-bold text-white">To: {{ $trx->destinationAccount->localized_name }}</div>
                                    @endif
                                    @if($trx->reference_number)
                                        <div class="text-[10px] font-mono text-blue-400">Ref: {{ $trx->reference_number }}</div>
                                    @endif
                                    <div class="text-[11px] text-slate-400 truncate max-w-xs">{{ $trx->notes ?? '—' }}</div>
                                </td>
                                <td class="py-3.5 px-4 font-mono uppercase text-slate-400">{{ $trx->payment_method }}</td>
                                <td class="py-3.5 px-4 text-right rtl:text-left font-mono font-bold text-emerald-400">
                                    {{ $trx->direction === 'credit' ? '+' . number_format($trx->amount, 2) . ' SAR' : '—' }}
                                </td>
                                <td class="py-3.5 px-4 text-right rtl:text-left font-mono font-bold text-rose-400">
                                    {{ $trx->direction === 'debit' ? '-' . number_format($trx->amount, 2) . ' SAR' : '—' }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($trx->type === 'customer_payment')
                                        <a href="{{ route('admin.finance.customer-payments.voucher', $trx->id) }}" target="_blank" class="btn btn-xs btn-outline border-slate-700 text-emerald-400 hover:bg-slate-800 rounded-lg" title="Print Receipt Voucher">
                                            <i class="fa-solid fa-receipt"></i>
                                        </a>
                                    @elseif($trx->type === 'supplier_payment')
                                        <a href="{{ route('admin.finance.supplier-payments.voucher', $trx->id) }}" target="_blank" class="btn btn-xs btn-outline border-slate-700 text-blue-400 hover:bg-slate-800 rounded-lg" title="Print Disbursement Voucher">
                                            <i class="fa-solid fa-receipt"></i>
                                        </a>
                                    @else
                                        <span class="text-slate-600">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-slate-500">
                                    <i class="fa-solid fa-receipt text-3xl mb-2 block opacity-40"></i>
                                    No transaction movements recorded in this account statement yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($transactions->hasPages())
                <div class="p-4 border-t border-slate-800 bg-slate-950/40">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
