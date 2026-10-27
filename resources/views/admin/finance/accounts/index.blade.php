<x-layouts.admin :title="__('finance.accounts')">
    <div class="space-y-6">
        <!-- Top Breadcrumbs & Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-1">
                    <a href="{{ route('admin.finance.dashboard') }}" class="hover:text-blue-500 transition-colors">{{ __('finance.finance') }}</a>
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    <span class="text-slate-200">{{ __('finance.accounts') }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">{{ __('finance.accounts_title') }}</h1>
                <p class="text-sm text-slate-400 mt-1">{{ __('finance.accounts_subtitle') }}</p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="document.getElementById('transferFundsModal').showModal()" class="btn btn-outline border-slate-700 text-slate-200 hover:bg-slate-800 font-bold px-4 py-2.5 rounded-xl flex items-center gap-2">
                    <i class="fa-solid fa-arrow-right-arrow-left text-sm"></i>
                    <span>{{ __('finance.transfer_funds') }}</span>
                </button>
                <button type="button" onclick="document.getElementById('newAccountModal').showModal()" class="btn btn-primary shadow-lg shadow-blue-500/20 font-bold px-5 py-2.5 rounded-xl flex items-center gap-2">
                    <i class="fa-solid fa-plus-circle text-base"></i>
                    <span>{{ __('finance.create_account') }}</span>
                </button>
            </div>
        </div>

        <!-- Total Liquidity Hero Banner -->
        <div class="card p-6 bg-gradient-to-r from-blue-950 via-slate-900 to-emerald-950/60 border border-blue-500/30 rounded-3xl relative overflow-hidden shadow-2xl">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
                <div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-500/10 text-blue-400 text-xs font-black uppercase tracking-wider mb-2 border border-blue-500/20">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        Reconciled Corporate Treasury
                    </span>
                    <h2 class="text-xs font-bold text-slate-400 uppercase tracking-widest">{{ __('finance.total_treasury_balance') }}</h2>
                    <div class="text-3xl sm:text-4xl lg:text-5xl font-black text-white font-mono mt-1">
                        {{ number_format($totalLiquidity, 2) }} <span class="text-lg font-bold text-emerald-400">SAR</span>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4 border-t md:border-t-0 md:border-l rtl:md:border-l-0 rtl:md:border-r border-slate-800/80 pt-4 md:pt-0 md:pl-6 rtl:md:pl-0 rtl:md:pr-6">
                    <div>
                        <div class="text-[11px] font-bold text-slate-400 uppercase">Commercial Banks</div>
                        <div class="text-lg font-black text-blue-400 font-mono mt-0.5">
                            {{ number_format($accounts->where('type', 'bank')->sum('current_balance'), 2) }} <span class="text-[10px] text-slate-500">SAR</span>
                        </div>
                    </div>
                    <div>
                        <div class="text-[11px] font-bold text-slate-400 uppercase">Vaults & Cash</div>
                        <div class="text-lg font-black text-amber-400 font-mono mt-0.5">
                            {{ number_format($accounts->where('type', 'cash')->sum('current_balance'), 2) }} <span class="text-[10px] text-slate-500">SAR</span>
                        </div>
                    </div>
                    <div>
                        <div class="text-[11px] font-bold text-slate-400 uppercase">Gateways / POS</div>
                        <div class="text-lg font-black text-purple-400 font-mono mt-0.5">
                            {{ number_format($accounts->where('type', 'gateway')->sum('current_balance'), 2) }} <span class="text-[10px] text-slate-500">SAR</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Accounts Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($accounts as $acc)
                <div class="card p-6 bg-slate-900 border border-slate-800 hover:border-blue-500/40 rounded-3xl transition-all duration-300 relative group overflow-hidden shadow-xl">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl
                                {{ $acc->type === 'bank' ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : '' }}
                                {{ $acc->type === 'cash' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : '' }}
                                {{ $acc->type === 'gateway' ? 'bg-purple-500/10 text-purple-400 border border-purple-500/20' : '' }}
                            ">
                                @if($acc->type === 'bank')
                                    <i class="fa-solid fa-building-columns"></i>
                                @elseif($acc->type === 'cash')
                                    <i class="fa-solid fa-vault"></i>
                                @else
                                    <i class="fa-solid fa-credit-card"></i>
                                @endif
                            </div>
                            <div>
                                <h3 class="font-black text-white text-base group-hover:text-blue-400 transition-colors">{{ $acc->localized_name }}</h3>
                                <span class="text-xs text-slate-400 font-mono">{{ $acc->code }}</span>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider
                            {{ $acc->type === 'bank' ? 'bg-blue-900/40 text-blue-300 border border-blue-700/50' : '' }}
                            {{ $acc->type === 'cash' ? 'bg-amber-900/40 text-amber-300 border border-amber-700/50' : '' }}
                            {{ $acc->type === 'gateway' ? 'bg-purple-900/40 text-purple-300 border border-purple-700/50' : '' }}
                        ">
                            {{ __('finance.type_' . $acc->type) }}
                        </span>
                    </div>

                    <div class="space-y-2 py-3 border-y border-slate-800/80 text-xs">
                        @if($acc->bank_name)
                            <div class="flex justify-between items-center">
                                <span class="text-slate-400">{{ __('finance.bank_name') }}</span>
                                <span class="font-semibold text-slate-200">{{ $acc->bank_name }}</span>
                            </div>
                        @endif
                        @if($acc->account_number)
                            <div class="flex justify-between items-center">
                                <span class="text-slate-400">{{ __('finance.account_number') }}</span>
                                <span class="font-mono text-slate-300">{{ $acc->account_number }}</span>
                            </div>
                        @endif
                        @if($acc->iban)
                            <div class="flex justify-between items-center">
                                <span class="text-slate-400">{{ __('finance.iban') }}</span>
                                <span class="font-mono text-[11px] text-slate-300">{{ $acc->iban }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between items-center">
                            <span class="text-slate-400">Total Operations</span>
                            <span class="font-mono font-bold text-slate-200">{{ $acc->transactions_count }} entries</span>
                        </div>
                    </div>

                    <div class="mt-4 pt-2 flex items-baseline justify-between">
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">{{ __('finance.current_balance') }}</span>
                            <span class="font-mono font-black text-2xl text-emerald-400">{{ number_format($acc->current_balance, 2) }}</span>
                            <span class="text-xs text-slate-500 font-bold ml-1">SAR</span>
                        </div>
                        <a href="{{ route('admin.finance.accounts.show', $acc->id) }}" class="btn btn-sm btn-outline border-slate-700 text-blue-400 hover:bg-blue-600 hover:text-white rounded-xl text-xs font-bold">
                            {{ __('finance.account_statement') }} <i class="fa-solid fa-arrow-right rtl:rotate-180 ml-1"></i>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center text-slate-500">
                    <i class="fa-solid fa-building-columns text-4xl mb-3 block opacity-30"></i>
                    No financial accounts defined yet.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Add Account Modal -->
    <dialog id="newAccountModal" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box bg-slate-900 border border-slate-800 text-white rounded-3xl max-w-xl p-6 sm:p-8">
            <h3 class="font-black text-xl mb-1 flex items-center gap-2">
                <i class="fa-solid fa-plus-circle text-blue-400"></i>
                {{ __('finance.create_account') }}
            </h3>
            <p class="text-xs text-slate-400 mb-6">{{ __('finance.accounts_subtitle') }}</p>

            <form action="{{ route('admin.finance.accounts.store') }}" method="POST" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">{{ __('finance.account_code') }} *</label>
                        <input type="text" name="code" required placeholder="e.g. ACC-SNB-01" class="input input-sm w-full bg-slate-800 border-slate-700 text-white rounded-xl text-xs font-mono uppercase">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">{{ __('finance.account_type') }} *</label>
                        <select name="type" required class="select select-sm w-full bg-slate-800 border-slate-700 text-white rounded-xl text-xs">
                            <option value="bank">{{ __('finance.type_bank') }}</option>
                            <option value="cash">{{ __('finance.type_cash') }}</option>
                            <option value="gateway">{{ __('finance.type_gateway') }}</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Account Name (EN) *</label>
                        <input type="text" name="name_en" required placeholder="e.g. SNB Main Operating Treasury" class="input input-sm w-full bg-slate-800 border-slate-700 text-white rounded-xl text-xs">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Account Name (AR) *</label>
                        <input type="text" name="name_ar" required placeholder="مثال: البنك الأهلي - حساب العمليات الرئيسي" class="input input-sm w-full bg-slate-800 border-slate-700 text-white rounded-xl text-xs">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">{{ __('finance.bank_name') }}</label>
                        <input type="text" name="bank_name" placeholder="e.g. Saudi National Bank (SNB)" class="input input-sm w-full bg-slate-800 border-slate-700 text-white rounded-xl text-xs">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">{{ __('finance.account_number') }}</label>
                        <input type="text" name="account_number" placeholder="e.g. 109823480001" class="input input-sm w-full bg-slate-800 border-slate-700 text-white rounded-xl text-xs font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">{{ __('finance.iban') }}</label>
                        <input type="text" name="iban" placeholder="SA..." class="input input-sm w-full bg-slate-800 border-slate-700 text-white rounded-xl text-xs font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">{{ __('finance.initial_balance') }} (SAR) *</label>
                        <input type="number" step="0.01" min="0" name="initial_balance" required value="0.00" class="input input-sm w-full bg-slate-800 border-slate-700 text-white rounded-xl text-xs font-mono">
                        <input type="hidden" name="currency" value="SAR">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">{{ __('finance.notes') }}</label>
                    <textarea name="notes" rows="2" placeholder="Account purpose or bank branch details..." class="textarea textarea-sm w-full bg-slate-800 border-slate-700 text-white rounded-xl text-xs"></textarea>
                </div>

                <div class="modal-action pt-4 border-t border-slate-800 flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('newAccountModal').close()" class="btn btn-sm btn-ghost text-slate-400 hover:text-white rounded-xl">{{ __('finance.cancel') }}</button>
                    <button type="submit" class="btn btn-sm btn-primary font-bold rounded-xl px-5">{{ __('finance.save') }}</button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>

    <!-- Transfer Funds Modal -->
    <dialog id="transferFundsModal" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box bg-slate-900 border border-slate-800 text-white rounded-3xl max-w-lg p-6 sm:p-8">
            <h3 class="font-black text-xl mb-1 flex items-center gap-2">
                <i class="fa-solid fa-arrow-right-arrow-left text-blue-400"></i>
                {{ __('finance.transfer_funds') }}
            </h3>
            <p class="text-xs text-slate-400 mb-6">Internal treasury reallocation between authorized corporate accounts.</p>

            <form action="{{ route('admin.finance.accounts.transfer') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">{{ __('finance.from_account') }} *</label>
                    <select name="from_account_id" required class="select select-sm w-full bg-slate-800 border-slate-700 text-white rounded-xl text-xs">
                        <option value="">Select source account...</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->localized_name }} ({{ number_format($acc->current_balance, 2) }} SAR)</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">{{ __('finance.to_account') }} *</label>
                    <select name="to_account_id" required class="select select-sm w-full bg-slate-800 border-slate-700 text-white rounded-xl text-xs">
                        <option value="">Select destination account...</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->localized_name }} ({{ number_format($acc->current_balance, 2) }} SAR)</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">{{ __('finance.transfer_amount') }} *</label>
                        <input type="number" step="0.01" min="0.01" name="amount" required placeholder="0.00" class="input input-sm w-full bg-slate-800 border-slate-700 text-white rounded-xl text-xs font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">{{ __('finance.transfer_date') }} *</label>
                        <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="input input-sm w-full bg-slate-800 border-slate-700 text-white rounded-xl text-xs font-mono">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">{{ __('finance.reference_number') }}</label>
                    <input type="text" name="reference_number" placeholder="Wire transfer reference" class="input input-sm w-full bg-slate-800 border-slate-700 text-white rounded-xl text-xs font-mono">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">{{ __('finance.notes') }}</label>
                    <textarea name="notes" rows="2" placeholder="Internal treasury transfer purpose..." class="textarea textarea-sm w-full bg-slate-800 border-slate-700 text-white rounded-xl text-xs"></textarea>
                </div>

                <div class="modal-action pt-4 border-t border-slate-800 flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('transferFundsModal').close()" class="btn btn-sm btn-ghost text-slate-400 hover:text-white rounded-xl">{{ __('finance.cancel') }}</button>
                    <button type="submit" class="btn btn-sm btn-primary font-bold rounded-xl px-5">{{ __('finance.confirm') }}</button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>
</x-layouts.admin>
