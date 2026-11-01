<x-layouts.admin :title="__('finance.accounts')">
    <div class="space-y-6" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
        
        <!-- Top Breadcrumbs & Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
            <div>
                <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 mb-1">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-primary transition">{{ __('admin.menu.dashboard') }}</a>
                    <span>/</span>
                    <a href="{{ route('admin.finance.dashboard') }}" class="hover:text-primary transition">{{ __('finance.finance') }}</a>
                    <span>/</span>
                    <span class="text-slate-900 dark:text-slate-100 font-bold">{{ __('finance.accounts') }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2.5">
                    <span class="w-2.5 h-8 bg-[#0A4F78] rounded-sm"></span>
                    {{ __('finance.accounts_title') }}
                </h1>
                <p class="text-xs sm:text-sm font-medium text-slate-600 dark:text-slate-300 mt-1">
                    {{ __('finance.accounts_subtitle') }}
                </p>
            </div>
            <div class="flex items-center gap-2.5 flex-wrap">
                <button type="button" onclick="document.getElementById('transferFundsModal').showModal()" class="btn btn-secondary border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 font-bold px-4 py-2 rounded-xl flex items-center gap-2 shadow-sm text-xs sm:text-sm transition">
                    <i class="fa-solid fa-arrow-right-arrow-left text-sky-600 dark:text-sky-400"></i>
                    <span>{{ __('finance.transfer_funds') }}</span>
                </button>
                <button type="button" onclick="document.getElementById('newAccountModal').showModal()" class="btn text-white font-bold px-4 py-2 rounded-xl flex items-center gap-2 shadow-md text-xs sm:text-sm transition" style="background: #0A4F78; border-color: #0A4F78;">
                    <i class="fa-solid fa-plus-circle text-sm"></i>
                    <span>{{ __('finance.create_account') }}</span>
                </button>
            </div>
        </div>

        @if(session('status'))
            <div class="p-3.5 rounded-xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-800 dark:text-emerald-300 text-xs font-bold flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-circle-check text-sm"></i>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <!-- Reconciled Corporate Treasury Hero Card (High Contrast Light & Dark Mode) -->
        <div class="card p-5 sm:p-6 border border-slate-200 dark:border-slate-800 bg-white dark:bg-[#062B49] rounded-2xl sm:rounded-3xl shadow-sm relative overflow-hidden">
            <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-[#0A4F78] via-[#2A8FC2] to-emerald-500"></div>
            
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
                <div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-sky-50 dark:bg-sky-950/60 text-[#0A4F78] dark:text-sky-300 text-xs font-black uppercase tracking-wider mb-2 border border-sky-200 dark:border-sky-800/60">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Reconciled Corporate Treasury
                    </span>
                    <h2 class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mt-1">
                        {{ __('finance.total_treasury_balance') }}
                    </h2>
                    <div class="text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 dark:text-white font-mono mt-1 tracking-tight">
                        {{ number_format($totalLiquidity, 2) }} <span class="text-lg font-bold text-emerald-600 dark:text-emerald-400">SAR</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 border-t md:border-t-0 md:border-l rtl:md:border-l-0 rtl:md:border-r border-slate-200 dark:border-slate-800 pt-4 md:pt-0 md:pl-6 rtl:md:pl-0 rtl:md:pr-6">
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                        <div class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase flex items-center gap-1.5">
                            <i class="fa-solid fa-building-columns text-sky-600 dark:text-sky-400"></i>
                            Commercial Banks
                        </div>
                        <div class="text-lg sm:text-xl font-black text-sky-700 dark:text-sky-400 font-mono mt-1">
                            {{ number_format($accounts->where('type', 'bank')->sum('current_balance'), 2) }} <span class="text-[10px] text-slate-500 font-bold">SAR</span>
                        </div>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                        <div class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase flex items-center gap-1.5">
                            <i class="fa-solid fa-vault text-amber-600 dark:text-amber-400"></i>
                            Vaults & Cash
                        </div>
                        <div class="text-lg sm:text-xl font-black text-amber-600 dark:text-amber-400 font-mono mt-1">
                            {{ number_format($accounts->where('type', 'cash')->sum('current_balance'), 2) }} <span class="text-[10px] text-slate-500 font-bold">SAR</span>
                        </div>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                        <div class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase flex items-center gap-1.5">
                            <i class="fa-solid fa-credit-card text-purple-600 dark:text-purple-400"></i>
                            Gateways / POS
                        </div>
                        <div class="text-lg sm:text-xl font-black text-purple-700 dark:text-purple-400 font-mono mt-1">
                            {{ number_format($accounts->where('type', 'gateway')->sum('current_balance'), 2) }} <span class="text-[10px] text-slate-500 font-bold">SAR</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Accounts Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6">
            @forelse($accounts as $acc)
                <div class="card p-5 sm:p-6 bg-white dark:bg-[#062B49] border border-slate-200 dark:border-slate-800 hover:border-sky-400 dark:hover:border-sky-500/50 rounded-2xl sm:rounded-3xl transition-all duration-300 relative group overflow-hidden shadow-sm hover:shadow-md flex flex-col justify-between">
                    
                    <!-- Type Accent Top Bar -->
                    <div class="absolute top-0 inset-x-0 h-1 
                        {{ $acc->type === 'bank' ? 'bg-sky-500' : '' }}
                        {{ $acc->type === 'cash' ? 'bg-amber-500' : '' }}
                        {{ $acc->type === 'gateway' ? 'bg-purple-500' : '' }}
                    "></div>

                    <div>
                        <!-- Header with Icon & Type Badge -->
                        <div class="flex items-start justify-between gap-3 mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl flex-shrink-0 shadow-sm
                                    {{ $acc->type === 'bank' ? 'bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-300 border border-sky-200 dark:border-sky-800' : '' }}
                                    {{ $acc->type === 'cash' ? 'bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-300 border border-amber-200 dark:border-amber-800' : '' }}
                                    {{ $acc->type === 'gateway' ? 'bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-300 border border-purple-200 dark:border-purple-800' : '' }}
                                ">
                                    @if($acc->type === 'bank')
                                        <i class="fa-solid fa-building-columns"></i>
                                    @elseif($acc->type === 'cash')
                                        <i class="fa-solid fa-vault"></i>
                                    @else
                                        <i class="fa-solid fa-credit-card"></i>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <h3 class="font-black text-slate-900 dark:text-white text-base group-hover:text-sky-600 dark:group-hover:text-sky-400 transition-colors truncate">
                                        {{ $acc->localized_name }}
                                    </h3>
                                    <span class="text-xs text-slate-500 dark:text-slate-400 font-mono font-bold block mt-0.5">
                                        {{ $acc->code }}
                                    </span>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider whitespace-nowrap shadow-xs
                                {{ $acc->type === 'bank' ? 'bg-sky-50 dark:bg-sky-950/50 text-sky-700 dark:text-sky-300 border border-sky-200 dark:border-sky-800' : '' }}
                                {{ $acc->type === 'cash' ? 'bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800' : '' }}
                                {{ $acc->type === 'gateway' ? 'bg-purple-50 dark:bg-purple-950/50 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800' : '' }}
                            ">
                                {{ __('finance.type_' . $acc->type) }}
                            </span>
                        </div>

                        <!-- Details Table -->
                        <div class="space-y-2 py-3 border-y border-slate-100 dark:border-slate-800/80 text-xs">
                            <div class="flex justify-between items-center gap-2">
                                <span class="text-slate-500 dark:text-slate-400 font-medium">
                                    {{ __('finance.bank_name') }}
                                </span>
                                <span class="font-bold text-slate-800 dark:text-slate-100 truncate text-end">
                                    {{ $acc->bank_name ?? ($acc->type === 'cash' ? 'Cash in Vault' : 'Payment Network') }}
                                </span>
                            </div>
                            @if($acc->account_number)
                                <div class="flex justify-between items-center gap-2">
                                    <span class="text-slate-500 dark:text-slate-400 font-medium">
                                        {{ __('finance.account_number') }}
                                    </span>
                                    <span class="font-mono font-bold text-slate-800 dark:text-slate-200">
                                        {{ $acc->account_number }}
                                    </span>
                                </div>
                            @endif
                            @if($acc->iban)
                                <div class="flex justify-between items-center gap-2">
                                    <span class="text-slate-500 dark:text-slate-400 font-medium">
                                        {{ __('finance.iban') }}
                                    </span>
                                    <span class="font-mono text-[11px] font-bold text-slate-800 dark:text-slate-200 truncate max-w-[200px]">
                                        {{ $acc->iban }}
                                    </span>
                                </div>
                            @endif
                            <div class="flex justify-between items-center gap-2">
                                <span class="text-slate-500 dark:text-slate-400 font-medium">
                                    Total Operations
                                </span>
                                <span class="font-mono font-bold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800/60 px-2 py-0.5 rounded text-[11px]">
                                    {{ $acc->transactions_count }} entries
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Balance & Statement Action -->
                    <div class="mt-4 pt-2 flex items-center justify-between gap-2 flex-wrap">
                        <div>
                            <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">
                                {{ __('finance.current_balance') }}
                            </span>
                            <div class="flex items-baseline">
                                <span class="font-mono font-black text-2xl text-emerald-600 dark:text-emerald-400">
                                    {{ number_format($acc->current_balance, 2) }}
                                </span>
                                <span class="text-xs text-slate-500 dark:text-slate-400 font-bold ml-1.5 mr-1.5">SAR</span>
                            </div>
                        </div>
                        <a href="{{ route('admin.finance.accounts.show', $acc->id) }}" class="btn btn-sm btn-secondary border border-slate-300 dark:border-slate-700 text-sky-700 dark:text-sky-300 hover:bg-sky-50 dark:hover:bg-sky-950/50 rounded-xl text-xs font-bold px-3 py-1.5 flex items-center gap-1.5 shadow-sm transition">
                            <span>{{ __('finance.account_statement') }}</span>
                            <i class="fa-solid fa-arrow-right rtl:rotate-180 text-[10px]"></i>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center text-slate-500 dark:text-slate-400 card border border-slate-200 dark:border-slate-800 bg-white dark:bg-[#062B49] rounded-2xl">
                    <i class="fa-solid fa-building-columns text-4xl mb-3 block opacity-30 text-sky-600"></i>
                    <p class="font-bold text-sm">No financial accounts defined yet.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Add Account Modal (Fully Optimized Light & Dark Mode) -->
    <dialog id="newAccountModal" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box bg-white dark:bg-[#062B49] border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-2xl sm:rounded-3xl max-w-xl p-6 sm:p-7 shadow-2xl relative">
            
            <!-- Top Gradient Accent -->
            <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-[#0A4F78] to-[#2A8FC2] rounded-t-3xl"></div>

            <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-sky-50 dark:bg-sky-950/60 text-[#0A4F78] dark:text-sky-300 flex items-center justify-center text-base border border-sky-200 dark:border-sky-800">
                        <i class="fa-solid fa-plus-circle"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-base sm:text-lg text-slate-900 dark:text-white">
                            {{ __('finance.create_account') }}
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            {{ __('finance.accounts_subtitle') }}
                        </p>
                    </div>
                </div>
                <button type="button" onclick="document.getElementById('newAccountModal').close()" class="text-slate-400 hover:text-slate-600 dark:hover:text-white text-lg">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form action="{{ route('admin.finance.accounts.store') }}" method="POST" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">{{ __('finance.account_code') }} *</label>
                        <input type="text" name="code" required placeholder="e.g. ACC-SNB-01" class="form-control text-xs w-full rounded-xl bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white font-mono uppercase">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">{{ __('finance.account_type') }} *</label>
                        <select name="type" required class="form-select text-xs w-full rounded-xl bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white">
                            <option value="bank">{{ __('finance.type_bank') }}</option>
                            <option value="cash">{{ __('finance.type_cash') }}</option>
                            <option value="gateway">{{ __('finance.type_gateway') }}</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Account Name (EN) *</label>
                        <input type="text" name="name_en" required placeholder="e.g. SNB Main Operating Treasury" class="form-control text-xs w-full rounded-xl bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">Account Name (AR) *</label>
                        <input type="text" name="name_ar" required placeholder="مثال: البنك الأهلي - حساب العمليات الرئيسي" class="form-control text-xs w-full rounded-xl bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">{{ __('finance.bank_name') }}</label>
                        <input type="text" name="bank_name" placeholder="e.g. Saudi National Bank (SNB)" class="form-control text-xs w-full rounded-xl bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">{{ __('finance.account_number') }}</label>
                        <input type="text" name="account_number" placeholder="e.g. 109823480001" class="form-control text-xs w-full rounded-xl bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">{{ __('finance.iban') }}</label>
                        <input type="text" name="iban" placeholder="SA..." class="form-control text-xs w-full rounded-xl bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">{{ __('finance.initial_balance') }} (SAR) *</label>
                        <input type="number" step="0.01" min="0" name="initial_balance" required value="0.00" class="form-control text-xs w-full rounded-xl bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white font-mono font-bold">
                        <input type="hidden" name="currency" value="SAR">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">{{ __('finance.notes') }}</label>
                    <textarea name="notes" rows="2" placeholder="Account purpose or bank branch details..." class="form-control text-xs w-full rounded-xl bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white"></textarea>
                </div>

                <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-2.5">
                    <button type="button" onclick="document.getElementById('newAccountModal').close()" class="btn btn-secondary text-xs font-bold text-slate-600 dark:text-slate-300 rounded-xl px-4">{{ __('finance.cancel') }}</button>
                    <button type="submit" class="btn text-xs font-bold text-white rounded-xl px-5 shadow-sm" style="background: #0A4F78; border-color: #0A4F78;">{{ __('finance.save') }}</button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>

    <!-- Transfer Funds Modal (Fully Optimized Light & Dark Mode) -->
    <dialog id="transferFundsModal" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box bg-white dark:bg-[#062B49] border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-2xl sm:rounded-3xl max-w-lg p-6 sm:p-7 shadow-2xl relative">
            
            <!-- Top Gradient Accent -->
            <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-sky-500 to-indigo-600 rounded-t-3xl"></div>

            <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-sky-50 dark:bg-sky-950/60 text-[#0A4F78] dark:text-sky-300 flex items-center justify-center text-base border border-sky-200 dark:border-sky-800">
                        <i class="fa-solid fa-arrow-right-arrow-left"></i>
                    </div>
                    <div>
                        <h3 class="font-black text-base sm:text-lg text-slate-900 dark:text-white">
                            {{ __('finance.transfer_funds') }}
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Internal treasury reallocation between authorized corporate accounts.</p>
                    </div>
                </div>
                <button type="button" onclick="document.getElementById('transferFundsModal').close()" class="text-slate-400 hover:text-slate-600 dark:hover:text-white text-lg">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form action="{{ route('admin.finance.accounts.transfer') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">{{ __('finance.from_account') }} *</label>
                    <select name="from_account_id" required class="form-select text-xs w-full rounded-xl bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white font-medium">
                        <option value="">Select source account...</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->localized_name }} ({{ number_format($acc->current_balance, 2) }} SAR)</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">{{ __('finance.to_account') }} *</label>
                    <select name="to_account_id" required class="form-select text-xs w-full rounded-xl bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white font-medium">
                        <option value="">Select destination account...</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->localized_name }} ({{ number_format($acc->current_balance, 2) }} SAR)</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">{{ __('finance.transfer_amount') }} *</label>
                        <input type="number" step="0.01" min="0.01" name="amount" required placeholder="0.00" class="form-control text-xs w-full rounded-xl bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white font-mono font-bold">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">{{ __('finance.transfer_date') }} *</label>
                        <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="form-control text-xs w-full rounded-xl bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white font-mono">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">{{ __('finance.reference_number') }}</label>
                    <input type="text" name="reference_number" placeholder="Wire transfer reference" class="form-control text-xs w-full rounded-xl bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white font-mono">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">{{ __('finance.notes') }}</label>
                    <textarea name="notes" rows="2" placeholder="Internal treasury transfer purpose..." class="form-control text-xs w-full rounded-xl bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white"></textarea>
                </div>

                <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-2.5">
                    <button type="button" onclick="document.getElementById('transferFundsModal').close()" class="btn btn-secondary text-xs font-bold text-slate-600 dark:text-slate-300 rounded-xl px-4">{{ __('finance.cancel') }}</button>
                    <button type="submit" class="btn text-xs font-bold text-white rounded-xl px-5 shadow-sm" style="background: #0A4F78; border-color: #0A4F78;">{{ __('finance.confirm') }}</button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>
</x-layouts.admin>
