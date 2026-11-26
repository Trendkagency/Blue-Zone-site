<x-layouts.admin 
    :pageTitle="__('finance.expenses')" 
    :pageSubtitle="__('finance.expenses_subtitle')"
    :breadcrumbs="[
        __('finance.finance') => route('admin.finance.dashboard'),
        __('finance.expenses') => route('admin.finance.expenses.index')
    ]"
>
    <x-slot name="actions">
        <div class="flex items-center gap-2.5 flex-wrap" x-data>
            <button 
                type="button" 
                onclick="window.openExpenseModal && window.openExpenseModal()" 
                @click="$dispatch('open-expense-modal')" 
                class="btn btn-primary font-bold shadow-lg shadow-blue-500/20 px-5 py-2.5 rounded-xl flex items-center gap-2 transition-all hover:scale-[1.02] cursor-pointer"
            >
                <i class="fa-solid fa-plus-circle text-base"></i>
                <span>{{ __('finance.create_expense') }}</span>
            </button>
        </div>
    </x-slot>

    <!-- Global Trigger Script -->
    <script>
        window.openExpenseModal = function() {
            window.dispatchEvent(new CustomEvent('open-expense-modal'));
            const root = document.getElementById('expensePageRoot');
            if (root && root._x_dataStack && root._x_dataStack[0]) {
                root._x_dataStack[0].openModal();
            }
        };
    </script>

    @if(session('status'))
        <div class="p-4 mb-6 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-sm flex items-center gap-3 shadow-xs">
            <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400 text-lg"></i>
            <span class="font-bold">{{ session('status') }}</span>
        </div>
    @endif

    <div class="space-y-6" id="expensePageRoot" x-data="expensePageManager()" @open-expense-modal.window="openModal()">

        <!-- Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 1. Total Operating Expenses -->
            <div class="card p-5 border border-slate-200 dark:border-slate-800 rounded-2xl bg-white dark:bg-[#062B49] shadow-sm relative overflow-hidden transition-all hover:shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">
                            {{ __('finance.total_operating_expenses') }}
                        </span>
                        <div class="text-2xl font-black text-rose-600 dark:text-rose-400 mt-1 font-mono">
                            {{ number_format($totalExpenses, 2) }} <span class="text-xs font-sans text-slate-400">SAR</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-rose-500/10 dark:bg-rose-500/20 border border-rose-500/20 flex items-center justify-center text-rose-500 dark:text-rose-400 text-xl">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                </div>
            </div>

            <!-- 2. VAT Tax Amount -->
            <div class="card p-5 border border-slate-200 dark:border-slate-800 rounded-2xl bg-white dark:bg-[#062B49] shadow-sm relative overflow-hidden transition-all hover:shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">
                            {{ __('finance.tax_amount') }} (VAT 15%)
                        </span>
                        <div class="text-2xl font-black text-amber-500 dark:text-amber-400 mt-1 font-mono">
                            {{ number_format($totalVat, 2) }} <span class="text-xs font-sans text-slate-400">SAR</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-amber-500/10 dark:bg-amber-500/20 border border-amber-500/20 flex items-center justify-center text-amber-500 dark:text-amber-400 text-xl">
                        <i class="fa-solid fa-percent"></i>
                    </div>
                </div>
            </div>

            <!-- 3. Active Categories -->
            <div class="card p-5 border border-slate-200 dark:border-slate-800 rounded-2xl bg-white dark:bg-[#062B49] shadow-sm relative overflow-hidden transition-all hover:shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">
                            {{ app()->getLocale() === 'ar' ? 'التصنيفات النشطة' : 'Active Categories' }}
                        </span>
                        <div class="text-2xl font-black text-blue-600 dark:text-blue-400 mt-1 font-mono">
                            {{ $categories->count() }}
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-blue-500/10 dark:bg-blue-500/20 border border-blue-500/20 flex items-center justify-center text-blue-600 dark:text-blue-400 text-xl">
                        <i class="fa-solid fa-tags"></i>
                    </div>
                </div>
            </div>

            <!-- 4. Total Records -->
            <div class="card p-5 border border-slate-200 dark:border-slate-800 rounded-2xl bg-white dark:bg-[#062B49] shadow-sm relative overflow-hidden transition-all hover:shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">
                            {{ app()->getLocale() === 'ar' ? 'إجمالي القيود' : 'Total Records' }}
                        </span>
                        <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1 font-mono">
                            {{ $expenses->total() }}
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-500/10 dark:bg-emerald-500/20 border border-emerald-500/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400 text-xl">
                        <i class="fa-solid fa-list-check"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="card p-4 sm:p-5 border border-slate-200 dark:border-slate-800 rounded-2xl bg-white dark:bg-[#062B49] shadow-sm">
            <form method="GET" action="{{ route('admin.finance.expenses.index') }}" class="flex flex-wrap items-center gap-3">
                <div class="flex-1 min-w-[220px] relative">
                    <i class="fa-solid fa-search absolute left-3 rtl:left-auto rtl:right-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ $search }}" 
                        placeholder="{{ __('finance.search') }}..." 
                        class="form-control text-xs w-full pl-9 rtl:pl-3 rtl:pr-9 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/80 text-slate-900 dark:text-white placeholder-slate-400 focus:bg-white dark:focus:bg-slate-900"
                    >
                </div>

                <div class="w-full sm:w-48">
                    <select name="category_id" class="form-select text-xs w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/80 text-slate-900 dark:text-white">
                        <option value="all">{{ __('finance.all') }} {{ __('finance.expense_category') }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $selectedCategory == $category->id ? 'selected' : '' }}>
                                {{ $category->localized_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="w-full sm:w-48">
                    <select name="account_id" class="form-select text-xs w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/80 text-slate-900 dark:text-white">
                        <option value="all">{{ __('finance.all') }} {{ __('finance.accounts') }}</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}" {{ $selectedAccount == $acc->id ? 'selected' : '' }}>
                                {{ $acc->localized_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-sm btn-outline border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl px-4 text-xs font-bold inline-flex items-center gap-1.5">
                    <i class="fa-solid fa-filter text-xs"></i> 
                    <span>{{ __('finance.filter') }}</span>
                </button>

                @if($search || ($selectedCategory && $selectedCategory !== 'all') || ($selectedAccount && $selectedAccount !== 'all'))
                    <a href="{{ route('admin.finance.expenses.index') }}" class="btn btn-sm btn-ghost text-slate-500 hover:text-slate-800 dark:hover:text-white text-xs inline-flex items-center gap-1">
                        <i class="fa-solid fa-rotate-left"></i> 
                        <span>{{ app()->getLocale() === 'ar' ? 'إعادة ضبط' : 'Reset' }}</span>
                    </a>
                @endif
            </form>
        </div>

        <!-- Expenses Data Table -->
        <div class="card border border-slate-200 dark:border-slate-800 rounded-2xl bg-white dark:bg-[#062B49] overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="table w-full text-xs text-slate-700 dark:text-slate-300">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-950/50 text-slate-600 dark:text-slate-400 font-bold border-b border-slate-200 dark:border-slate-800 uppercase tracking-wider text-[11px]">
                            <th class="py-3.5 px-4 text-left rtl:text-right">{{ __('finance.expense_number') }}</th>
                            <th class="py-3.5 px-4 text-left rtl:text-right">{{ __('finance.date') }}</th>
                            <th class="py-3.5 px-4 text-left rtl:text-right">{{ __('finance.expense_category') }}</th>
                            <th class="py-3.5 px-4 text-left rtl:text-right">{{ __('finance.expense_title') }}</th>
                            <th class="py-3.5 px-4 text-left rtl:text-right">{{ __('finance.paid_from') }}</th>
                            <th class="py-3.5 px-4 text-left rtl:text-right">{{ __('finance.payment_method') }}</th>
                            <th class="py-3.5 px-4 text-right rtl:text-left">{{ __('finance.amount_before_tax') }}</th>
                            <th class="py-3.5 px-4 text-right rtl:text-left">{{ __('finance.tax_amount') }}</th>
                            <th class="py-3.5 px-4 text-right rtl:text-left">{{ __('finance.total_expense_amount') }}</th>
                            <th class="py-3.5 px-4 text-center">{{ __('finance.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                        @forelse($expenses as $expense)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="py-3.5 px-4 font-mono font-bold text-slate-900 dark:text-white">
                                    {{ $expense->expense_number }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-500 dark:text-slate-400 font-mono">
                                    {{ $expense->expense_date ? $expense->expense_date->format('Y-m-d') : '—' }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-50 dark:bg-slate-800 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-slate-700">
                                        {{ $expense->category?->localized_name ?? '—' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900 dark:text-slate-100">{{ $expense->localized_title }}</div>
                                    @if($expense->beneficiary_name)
                                        <div class="text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1 mt-0.5">
                                            <i class="fa-solid fa-user-tag text-[9px] text-slate-400"></i>
                                            <span>{{ $expense->beneficiary_name }}</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="text-slate-700 dark:text-slate-300 font-medium">{{ $expense->account?->localized_name ?? '—' }}</span>
                                </td>
                                <td class="py-3.5 px-4 font-mono uppercase text-slate-500 dark:text-slate-400 text-[11px]">
                                    {{ $expense->payment_method }}
                                </td>
                                <td class="py-3.5 px-4 text-right rtl:text-left font-mono text-slate-700 dark:text-slate-300">
                                    {{ number_format($expense->amount, 2) }}
                                </td>
                                <td class="py-3.5 px-4 text-right rtl:text-left font-mono text-amber-600 dark:text-amber-400 font-semibold">
                                    {{ number_format($expense->tax_amount, 2) }}
                                </td>
                                <td class="py-3.5 px-4 text-right rtl:text-left font-mono font-bold text-rose-600 dark:text-rose-400 text-sm">
                                    {{ number_format($expense->total_amount, 2) }} <span class="text-[10px] text-slate-400 font-normal">SAR</span>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($expense->receipt_path)
                                        <a href="{{ $expense->receipt_path }}" target="_blank" class="w-8 h-8 rounded-lg inline-flex items-center justify-center border border-slate-200 dark:border-slate-700 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-slate-800 transition" title="View Receipt Attachment">
                                            <i class="fa-solid fa-paperclip text-xs"></i>
                                        </a>
                                    @else
                                        <span class="text-slate-300 dark:text-slate-600">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="py-12 text-center text-slate-400 dark:text-slate-500">
                                    <i class="fa-solid fa-receipt text-3xl mb-2 block opacity-40"></i>
                                    {{ app()->getLocale() === 'ar' ? 'لا توجد مصروفات مسجلة حتى الآن.' : 'No operating expenses recorded yet.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($expenses->hasPages())
                <div class="p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/30">
                    {{ $expenses->links() }}
                </div>
            @endif
        </div>

        <!-- ========================================================================= -->
        <!-- PROFESSIONAL DRAGGABLE MODAL: Record New Expense (Light & Dark Mode)      -->
        <!-- ========================================================================= -->
        <div 
            id="expenseDraggableModalWrapper"
            x-show="isOpen" 
            x-cloak
            class="fixed inset-0 z-[99999]"
            style="display: none;"
        >
            <!-- Semi-transparent backdrop to view underlying figures while positioning -->
            <div 
                x-show="isOpen"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-950/40 backdrop-blur-[2px] pointer-events-auto"
                @click="closeModal()"
            ></div>

            <!-- Floating Draggable Modal Window -->
            <div 
                x-ref="modalBox"
                x-show="isOpen"
                x-transition:enter="transition cubic-bezier(0.16, 1, 0.3, 1) duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                :style="modalStyle"
                class="fixed pointer-events-auto w-[95vw] sm:w-[680px] max-h-[92vh] flex flex-col rounded-3xl bg-white dark:bg-[#081B2E] border border-slate-200/90 dark:border-slate-700/80 shadow-[0_25px_60px_-15px_rgba(0,0,0,0.3)] dark:shadow-[0_25px_60px_-15px_rgba(0,0,0,0.8)] overflow-hidden select-text"
                @keydown.escape.window="closeModal()"
            >
                <!-- Draggable Header Handle -->
                <div 
                    @mousedown="startDrag($event)"
                    @touchstart.passive="startTouchDrag($event)"
                    class="px-6 py-4 bg-gradient-to-r from-slate-100 via-slate-50 to-slate-100 dark:from-slate-800/90 dark:via-[#09223A] dark:to-slate-800/90 border-b border-slate-200 dark:border-slate-700/70 flex items-center justify-between cursor-grab active:cursor-grabbing select-none transition-colors"
                >
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-rose-500/10 dark:bg-rose-500/20 border border-rose-500/30 text-rose-600 dark:text-rose-400 flex items-center justify-center text-lg shadow-xs">
                            <i class="fa-solid fa-receipt"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-black text-base sm:text-lg text-slate-900 dark:text-white tracking-tight">
                                    {{ __('finance.create_expense') }}
                                </h3>
                                <span class="hidden sm:inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-200/80 dark:bg-slate-700/60 text-slate-600 dark:text-slate-300 border border-slate-300 dark:border-slate-600">
                                    <i class="fa-solid fa-up-down-left-right text-[8px]"></i>
                                    {{ app()->getLocale() === 'ar' ? 'اسحب من هنا' : 'Drag Handle' }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                {{ __('finance.expenses_subtitle') }}
                            </p>
                        </div>
                    </div>

                    <!-- Window Action Icons -->
                    <div class="flex items-center gap-1.5" @mousedown.stop @touchstart.stop>
                        <!-- Reset to Center -->
                        <button 
                            type="button" 
                            @click="centerModal()" 
                            class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-slate-200 dark:hover:bg-slate-700/60 transition"
                            title="{{ app()->getLocale() === 'ar' ? 'إعادة للمنتصف' : 'Center Modal' }}"
                        >
                            <i class="fa-solid fa-arrows-to-dot text-xs"></i>
                        </button>

                        <!-- Close Button -->
                        <button 
                            type="button" 
                            @click="closeModal()" 
                            class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition"
                            title="{{ __('finance.cancel') }}"
                        >
                            <i class="fa-solid fa-xmark text-sm font-black"></i>
                        </button>
                    </div>
                </div>

                <!-- Scrollable Form Body -->
                <div class="p-6 sm:p-7 overflow-y-auto max-h-[calc(92vh-130px)] space-y-4">
                    <form action="{{ route('admin.finance.expenses.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4" id="expenseForm">
                        @csrf

                        <!-- Row 1: Category & Account -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                    {{ __('finance.expense_category') }} <span class="text-rose-500">*</span>
                                </label>
                                <select 
                                    name="expense_category_id" 
                                    required 
                                    class="form-select text-xs w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:bg-white dark:focus:bg-slate-900 py-2"
                                >
                                    <option value="">{{ app()->getLocale() === 'ar' ? '-- اختر تصنيف المصروف --' : 'Select category...' }}</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->localized_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                    {{ __('finance.paid_from') }} <span class="text-rose-500">*</span>
                                </label>
                                <select 
                                    name="financial_account_id" 
                                    required 
                                    class="form-select text-xs w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:bg-white dark:focus:bg-slate-900 py-2"
                                >
                                    <option value="">{{ app()->getLocale() === 'ar' ? '-- اختر الحساب / الخزينة --' : 'Select bank/cash vault...' }}</option>
                                    @foreach($accounts as $acc)
                                        <option value="{{ $acc->id }}">{{ $acc->localized_name }} ({{ number_format($acc->current_balance, 2) }} SAR)</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Row 2: Titles EN / AR -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                    Expense Title (EN) <span class="text-rose-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    name="title_en" 
                                    required 
                                    placeholder="e.g. Cleanroom Certification Audit" 
                                    class="form-control text-xs w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:bg-white dark:focus:bg-slate-900 placeholder-slate-400 py-2"
                                >
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                    Expense Title (AR)
                                </label>
                                <input 
                                    type="text" 
                                    name="title_ar" 
                                    placeholder="مثال: رسوم فحص وتوثيق الغرف المعقمة" 
                                    class="form-control text-xs w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white focus:bg-white dark:focus:bg-slate-900 placeholder-slate-400 py-2"
                                >
                            </div>
                        </div>

                        <!-- Row 3: Financials Amount / Tax / Total -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700/60">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                    {{ __('finance.amount_before_tax') }} <span class="text-rose-500">*</span>
                                </label>
                                <input 
                                    type="number" 
                                    step="0.01" 
                                    min="0.01" 
                                    name="amount" 
                                    id="exp_amount" 
                                    required 
                                    x-model.number="amount"
                                    @input="calcTotals()" 
                                    placeholder="0.00" 
                                    class="form-control text-xs w-full font-mono font-bold text-right rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white py-2"
                                >
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                    {{ __('finance.tax_amount') }} (15%)
                                </label>
                                <input 
                                    type="number" 
                                    step="0.01" 
                                    min="0" 
                                    name="tax_amount" 
                                    id="exp_tax" 
                                    x-model.number="taxAmount"
                                    @input="calcManualTax()" 
                                    placeholder="0.00" 
                                    class="form-control text-xs w-full font-mono text-right rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-amber-600 dark:text-amber-400 font-bold py-2"
                                >
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                    {{ __('finance.total_expense_amount') }} <span class="text-rose-500">*</span>
                                </label>
                                <input 
                                    type="number" 
                                    step="0.01" 
                                    min="0.01" 
                                    name="total_amount" 
                                    id="exp_total" 
                                    required 
                                    readonly 
                                    :value="totalAmount.toFixed(2)"
                                    class="form-control text-xs w-full font-mono font-black text-right rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-100 dark:bg-slate-900 text-rose-600 dark:text-rose-400 py-2 cursor-not-allowed"
                                >
                            </div>
                        </div>

                        <!-- Row 4: Date, Payment Method, Beneficiary -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                    {{ __('finance.date') }} <span class="text-rose-500">*</span>
                                </label>
                                <input 
                                    type="date" 
                                    name="expense_date" 
                                    required 
                                    value="{{ date('Y-m-d') }}" 
                                    class="form-control text-xs w-full font-mono rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white py-2"
                                >
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                    {{ __('finance.payment_method') }} <span class="text-rose-500">*</span>
                                </label>
                                <select 
                                    name="payment_method" 
                                    required 
                                    class="form-select text-xs w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white py-2"
                                >
                                    <option value="bank_transfer">Bank Wire / Transfer</option>
                                    <option value="corporate_card">Corporate Card</option>
                                    <option value="petty_cash">Petty Cash</option>
                                    <option value="check">Check / Promissory Note</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                    {{ __('finance.beneficiary') }}
                                </label>
                                <input 
                                    type="text" 
                                    name="beneficiary_name" 
                                    placeholder="Payee / Vendor" 
                                    class="form-control text-xs w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white py-2"
                                >
                            </div>
                        </div>

                        <!-- Row 5: Reference Number & Attachment -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                    {{ __('finance.reference_number') }}
                                </label>
                                <input 
                                    type="text" 
                                    name="reference_number" 
                                    placeholder="Bill / Receipt / Wire Ref" 
                                    class="form-control text-xs w-full font-mono rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white py-2"
                                >
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                    {{ __('finance.receipt_attachment') }}
                                </label>
                                <input 
                                    type="file" 
                                    name="receipt" 
                                    accept="image/*,application/pdf" 
                                    class="block w-full text-xs text-slate-500 dark:text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 dark:file:bg-slate-800 dark:file:text-blue-400 hover:file:bg-blue-100 border border-slate-300 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-800 p-1"
                                >
                            </div>
                        </div>

                        <!-- Notes & Justifications -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                                {{ __('finance.notes') }}
                            </label>
                            <textarea 
                                name="notes" 
                                rows="2" 
                                placeholder="Clinical justification, supplier contract note, or audit remarks..." 
                                class="form-control text-xs w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white py-2"
                            ></textarea>
                        </div>

                        <!-- Modal Footer Action Buttons -->
                        <div class="pt-4 border-t border-slate-200 dark:border-slate-700/80 flex items-center justify-between">
                            <span class="text-[11px] text-slate-400">
                                <i class="fa-solid fa-shield-halved mr-1 text-emerald-500"></i>
                                {{ app()->getLocale() === 'ar' ? 'توثيق مالي معتمد' : 'Authorized Fiscal Ledger Entry' }}
                            </span>
                            <div class="flex items-center gap-2">
                                <button 
                                    type="button" 
                                    @click="closeModal()" 
                                    class="btn btn-sm btn-ghost text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white rounded-xl px-4"
                                >
                                    {{ __('finance.cancel') }}
                                </button>
                                <button 
                                    type="submit" 
                                    class="btn btn-sm btn-primary font-bold rounded-xl px-6 shadow-md shadow-blue-500/20"
                                >
                                    <i class="fa-solid fa-floppy-disk mr-1.5 ml-1.5"></i>
                                    {{ __('finance.save') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <!-- Alpine.js Page & Draggable Window Manager -->
    <script>
        function expensePageManager() {
            return {
                isOpen: false,
                isDragging: false,
                posX: null,
                posY: null,
                startX: 0,
                startY: 0,
                initialX: 0,
                initialY: 0,
                amount: 0,
                taxAmount: 0,
                get totalAmount() {
                    return Math.max(0, (Number(this.amount) || 0) + (Number(this.taxAmount) || 0));
                },
                get modalStyle() {
                    if (this.posX === null || this.posY === null) {
                        return 'top: 50%; left: 50%; transform: translate(-50%, -50%); position: fixed; z-index: 100000;';
                    }
                    return `top: ${this.posY}px; left: ${this.posX}px; transform: none; position: fixed; z-index: 100000;`;
                },
                init() {
                    window.addEventListener('open-expense-modal', () => {
                        this.openModal();
                    });
                },
                openModal() {
                    this.isOpen = true;
                    this.$nextTick(() => {
                        if (this.posX === null || this.posY === null) {
                            this.centerModal();
                        }
                    });
                },
                closeModal() {
                    this.isOpen = false;
                },
                centerModal() {
                    const el = this.$refs.modalBox;
                    const w = el ? el.offsetWidth : 680;
                    const h = el ? el.offsetHeight : 550;
                    this.posX = Math.max(15, Math.floor((window.innerWidth - w) / 2));
                    this.posY = Math.max(20, Math.floor((window.innerHeight - h) / 2));
                },
                startDrag(e) {
                    if (e.button !== 0 || e.target.closest('button, a, input, select, textarea')) return;
                    this.isDragging = true;
                    this.startX = e.clientX;
                    this.startY = e.clientY;

                    const el = this.$refs.modalBox;
                    const rect = el.getBoundingClientRect();
                    this.initialX = rect.left;
                    this.initialY = rect.top;
                    this.posX = rect.left;
                    this.posY = rect.top;

                    const onMouseMove = (moveEvent) => {
                        if (!this.isDragging) return;
                        const dx = moveEvent.clientX - this.startX;
                        const dy = moveEvent.clientY - this.startY;

                        const modalWidth = el.offsetWidth;
                        const modalHeight = el.offsetHeight;

                        const maxLeft = window.innerWidth - modalWidth - 10;
                        const maxTop = window.innerHeight - modalHeight - 10;

                        this.posX = Math.min(Math.max(10, this.initialX + dx), Math.max(10, maxLeft));
                        this.posY = Math.min(Math.max(10, this.initialY + dy), Math.max(10, maxTop));
                    };

                    const onMouseUp = () => {
                        this.isDragging = false;
                        window.removeEventListener('mousemove', onMouseMove);
                        window.removeEventListener('mouseup', onMouseUp);
                    };

                    window.addEventListener('mousemove', onMouseMove);
                    window.addEventListener('mouseup', onMouseUp);
                },
                startTouchDrag(e) {
                    if (e.touches.length !== 1 || e.target.closest('button, a, input, select, textarea')) return;
                    this.isDragging = true;
                    const touch = e.touches[0];
                    this.startX = touch.clientX;
                    this.startY = touch.clientY;

                    const el = this.$refs.modalBox;
                    const rect = el.getBoundingClientRect();
                    this.initialX = rect.left;
                    this.initialY = rect.top;
                    this.posX = rect.left;
                    this.posY = rect.top;

                    const onTouchMove = (moveEvent) => {
                        if (!this.isDragging || moveEvent.touches.length !== 1) return;
                        const t = moveEvent.touches[0];
                        const dx = t.clientX - this.startX;
                        const dy = t.clientY - this.startY;

                        const modalWidth = el.offsetWidth;
                        const modalHeight = el.offsetHeight;

                        const maxLeft = window.innerWidth - modalWidth - 10;
                        const maxTop = window.innerHeight - modalHeight - 10;

                        this.posX = Math.min(Math.max(10, this.initialX + dx), Math.max(10, maxLeft));
                        this.posY = Math.min(Math.max(10, this.initialY + dy), Math.max(10, maxTop));
                    };

                    const onTouchEnd = () => {
                        this.isDragging = false;
                        window.removeEventListener('touchmove', onTouchMove);
                        window.removeEventListener('touchend', onTouchEnd);
                    };

                    window.addEventListener('touchmove', onTouchMove, { passive: true });
                    window.addEventListener('touchend', onTouchEnd);
                },
                calcTotals() {
                    const amt = Number(this.amount) || 0;
                    const tax = Math.round(amt * 0.15 * 100) / 100;
                    this.taxAmount = tax;
                },
                calcManualTax() {
                    // Manual tax adjustment retains custom value
                }
            };
        }
    </script>
</x-layouts.admin>
