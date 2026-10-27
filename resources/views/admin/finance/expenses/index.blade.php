<x-layouts.admin :title="__('finance.expenses')">
    <div class="space-y-6">
        <!-- Top Breadcrumbs & Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-1">
                    <a href="{{ route('admin.finance.dashboard') }}" class="hover:text-blue-500 transition-colors">{{ __('finance.finance') }}</a>
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    <span class="text-slate-200">{{ __('finance.expenses') }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">{{ __('finance.expenses_title') }}</h1>
                <p class="text-sm text-slate-400 mt-1">{{ __('finance.expenses_subtitle') }}</p>
            </div>
            <div>
                <button type="button" onclick="document.getElementById('newExpenseModal').showModal()" class="btn btn-primary shadow-lg shadow-blue-500/20 font-bold px-5 py-2.5 rounded-xl flex items-center gap-2">
                    <i class="fa-solid fa-plus-circle text-base"></i>
                    <span>{{ __('finance.create_expense') }}</span>
                </button>
            </div>
        </div>

        <!-- Metric Badges -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="card p-5 bg-gradient-to-br from-slate-900 to-slate-800/80 border border-slate-700/60 rounded-2xl relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">{{ __('finance.total_operating_expenses') }}</span>
                        <div class="text-2xl font-black text-rose-400 mt-1 font-mono">{{ number_format($totalExpenses, 2) }} <span class="text-xs text-slate-400">SAR</span></div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-rose-500/10 border border-rose-500/20 flex items-center justify-center text-rose-400 text-xl">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                </div>
            </div>

            <div class="card p-5 bg-gradient-to-br from-slate-900 to-slate-800/80 border border-slate-700/60 rounded-2xl relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">{{ __('finance.tax_amount') }} (VAT)</span>
                        <div class="text-2xl font-black text-amber-400 mt-1 font-mono">{{ number_format($totalVat, 2) }} <span class="text-xs text-slate-400">SAR</span></div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400 text-xl">
                        <i class="fa-solid fa-percent"></i>
                    </div>
                </div>
            </div>

            <div class="card p-5 bg-gradient-to-br from-slate-900 to-slate-800/80 border border-slate-700/60 rounded-2xl relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Active Categories</span>
                        <div class="text-2xl font-black text-blue-400 mt-1 font-mono">{{ $categories->count() }}</div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400 text-xl">
                        <i class="fa-solid fa-tags"></i>
                    </div>
                </div>
            </div>

            <div class="card p-5 bg-gradient-to-br from-slate-900 to-slate-800/80 border border-slate-700/60 rounded-2xl relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Records</span>
                        <div class="text-2xl font-black text-emerald-400 mt-1 font-mono">{{ $expenses->total() }}</div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 text-xl">
                        <i class="fa-solid fa-list-check"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters & Search -->
        <div class="card p-4 bg-slate-900/90 border border-slate-800 rounded-2xl">
            <form method="GET" action="{{ route('admin.finance.expenses.index') }}" class="flex flex-wrap items-center gap-3">
                <div class="flex-1 min-w-[200px] relative">
                    <i class="fa-solid fa-search absolute left-3 rtl:left-auto rtl:right-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" name="search" value="{{ $search }}" placeholder="{{ __('finance.search') }}" class="input input-sm w-full bg-slate-800 border-slate-700 text-white pl-9 rtl:pl-3 rtl:pr-9 rounded-xl focus:border-blue-500 text-xs">
                </div>

                <div class="w-48">
                    <select name="category_id" class="select select-sm w-full bg-slate-800 border-slate-700 text-white rounded-xl text-xs">
                        <option value="all">{{ __('finance.all') }} {{ __('finance.expense_category') }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $selectedCategory == $category->id ? 'selected' : '' }}>
                                {{ $category->localized_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="w-48">
                    <select name="account_id" class="select select-sm w-full bg-slate-800 border-slate-700 text-white rounded-xl text-xs">
                        <option value="all">{{ __('finance.all') }} {{ __('finance.accounts') }}</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}" {{ $selectedAccount == $acc->id ? 'selected' : '' }}>
                                {{ $acc->localized_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-sm btn-outline border-slate-700 text-slate-200 hover:bg-slate-800 rounded-xl px-4 text-xs font-bold">
                    <i class="fa-solid fa-filter mr-1.5 ml-1.5"></i> {{ __('finance.filter') }}
                </button>

                @if($search || ($selectedCategory && $selectedCategory !== 'all') || ($selectedAccount && $selectedAccount !== 'all'))
                    <a href="{{ route('admin.finance.expenses.index') }}" class="btn btn-sm btn-ghost text-slate-400 hover:text-white text-xs">
                        <i class="fa-solid fa-rotate-left mr-1"></i> Reset
                    </a>
                @endif
            </form>
        </div>

        <!-- Expenses Table -->
        <div class="card bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="table w-full text-slate-300 text-xs">
                    <thead>
                        <tr class="bg-slate-950/60 text-slate-400 font-bold border-b border-slate-800 uppercase tracking-wider text-[11px]">
                            <th class="py-3 px-4">{{ __('finance.expense_number') }}</th>
                            <th class="py-3 px-4">{{ __('finance.date') }}</th>
                            <th class="py-3 px-4">{{ __('finance.expense_category') }}</th>
                            <th class="py-3 px-4">{{ __('finance.expense_title') }}</th>
                            <th class="py-3 px-4">{{ __('finance.paid_from') }}</th>
                            <th class="py-3 px-4">{{ __('finance.payment_method') }}</th>
                            <th class="py-3 px-4 text-right rtl:text-left">{{ __('finance.amount_before_tax') }}</th>
                            <th class="py-3 px-4 text-right rtl:text-left">{{ __('finance.tax_amount') }}</th>
                            <th class="py-3 px-4 text-right rtl:text-left">{{ __('finance.total_expense_amount') }}</th>
                            <th class="py-3 px-4 text-center">{{ __('finance.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 font-medium">
                        @forelse($expenses as $expense)
                            <tr class="hover:bg-slate-800/40 transition-colors">
                                <td class="py-3.5 px-4 font-mono font-bold text-white">{{ $expense->expense_number }}</td>
                                <td class="py-3.5 px-4 text-slate-400 font-mono">{{ $expense->expense_date ? $expense->expense_date->format('Y-m-d') : '—' }}</td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-800 text-blue-300 border border-slate-700">
                                        {{ $expense->category?->localized_name ?? '—' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-100">{{ $expense->localized_title }}</div>
                                    @if($expense->beneficiary_name)
                                        <div class="text-[11px] text-slate-400 flex items-center gap-1 mt-0.5">
                                            <i class="fa-solid fa-user-tag text-[9px]"></i>
                                            <span>{{ $expense->beneficiary_name }}</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="text-slate-300 font-medium">{{ $expense->account?->localized_name ?? '—' }}</span>
                                </td>
                                <td class="py-3.5 px-4 font-mono uppercase text-slate-400">{{ $expense->payment_method }}</td>
                                <td class="py-3.5 px-4 text-right rtl:text-left font-mono text-slate-300">{{ number_format($expense->amount, 2) }}</td>
                                <td class="py-3.5 px-4 text-right rtl:text-left font-mono text-amber-400">{{ number_format($expense->tax_amount, 2) }}</td>
                                <td class="py-3.5 px-4 text-right rtl:text-left font-mono font-bold text-rose-400">{{ number_format($expense->total_amount, 2) }} SAR</td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($expense->receipt_path)
                                        <a href="{{ $expense->receipt_path }}" target="_blank" class="btn btn-xs btn-outline border-slate-700 text-blue-400 hover:bg-slate-800 rounded-lg" title="View Receipt Attachment">
                                            <i class="fa-solid fa-paperclip"></i>
                                        </a>
                                    @else
                                        <span class="text-slate-600">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="py-12 text-center text-slate-500">
                                    <i class="fa-solid fa-receipt text-3xl mb-2 block opacity-40"></i>
                                    No operating expenses recorded yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($expenses->hasPages())
                <div class="p-4 border-t border-slate-800 bg-slate-950/40">
                    {{ $expenses->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Record New Expense Modal -->
    <dialog id="newExpenseModal" class="modal modal-bottom sm:modal-middle">
        <div class="modal-box bg-slate-900 border border-slate-800 text-white rounded-3xl max-w-2xl p-6 sm:p-8">
            <h3 class="font-black text-xl mb-1 flex items-center gap-2">
                <i class="fa-solid fa-receipt text-rose-400"></i>
                {{ __('finance.create_expense') }}
            </h3>
            <p class="text-xs text-slate-400 mb-6">{{ __('finance.expenses_subtitle') }}</p>

            <form action="{{ route('admin.finance.expenses.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">{{ __('finance.expense_category') }} *</label>
                        <select name="expense_category_id" required class="select select-sm w-full bg-slate-800 border-slate-700 text-white rounded-xl text-xs">
                            <option value="">Select category...</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->localized_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">{{ __('finance.paid_from') }} *</label>
                        <select name="financial_account_id" required class="select select-sm w-full bg-slate-800 border-slate-700 text-white rounded-xl text-xs">
                            <option value="">Select bank/cash vault...</option>
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}">{{ $acc->localized_name }} ({{ number_format($acc->current_balance, 2) }} SAR)</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Expense Title (EN) *</label>
                        <input type="text" name="title_en" required placeholder="e.g. Cleanroom Certification Audit" class="input input-sm w-full bg-slate-800 border-slate-700 text-white rounded-xl text-xs">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Expense Title (AR)</label>
                        <input type="text" name="title_ar" placeholder="مثال: رسوم فحص وتوثيق الغرف المعقمة" class="input input-sm w-full bg-slate-800 border-slate-700 text-white rounded-xl text-xs">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">{{ __('finance.amount_before_tax') }} *</label>
                        <input type="number" step="0.01" min="0.01" name="amount" id="exp_amount" required oninput="calcExpenseTotal()" placeholder="0.00" class="input input-sm w-full bg-slate-800 border-slate-700 text-white rounded-xl text-xs font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">{{ __('finance.tax_amount') }} (15%)</label>
                        <input type="number" step="0.01" min="0" name="tax_amount" id="exp_tax" oninput="calcExpenseTotal(false)" placeholder="0.00" class="input input-sm w-full bg-slate-800 border-slate-700 text-white rounded-xl text-xs font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">{{ __('finance.total_expense_amount') }} *</label>
                        <input type="number" step="0.01" min="0.01" name="total_amount" id="exp_total" required readonly class="input input-sm w-full bg-slate-950 border-slate-800 text-rose-400 font-bold rounded-xl text-xs font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">{{ __('finance.date') }} *</label>
                        <input type="date" name="expense_date" required value="{{ date('Y-m-d') }}" class="input input-sm w-full bg-slate-800 border-slate-700 text-white rounded-xl text-xs font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">{{ __('finance.payment_method') }} *</label>
                        <select name="payment_method" required class="select select-sm w-full bg-slate-800 border-slate-700 text-white rounded-xl text-xs">
                            <option value="bank_transfer">Bank Wire / Transfer</option>
                            <option value="corporate_card">Corporate Card</option>
                            <option value="petty_cash">Petty Cash</option>
                            <option value="check">Check / Promissory Note</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">{{ __('finance.beneficiary') }}</label>
                        <input type="text" name="beneficiary_name" placeholder="Payee / Vendor" class="input input-sm w-full bg-slate-800 border-slate-700 text-white rounded-xl text-xs">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">{{ __('finance.reference_number') }}</label>
                        <input type="text" name="reference_number" placeholder="Bill / Receipt / Wire Ref" class="input input-sm w-full bg-slate-800 border-slate-700 text-white rounded-xl text-xs font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">{{ __('finance.receipt_attachment') }}</label>
                        <input type="file" name="receipt" accept="image/*,application/pdf" class="file-input file-input-sm file-input-bordered w-full bg-slate-800 border-slate-700 text-white rounded-xl text-xs">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">{{ __('finance.notes') }}</label>
                    <textarea name="notes" rows="2" placeholder="Clinical justification or audit remarks..." class="textarea textarea-sm w-full bg-slate-800 border-slate-700 text-white rounded-xl text-xs"></textarea>
                </div>

                <div class="modal-action pt-4 border-t border-slate-800 flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('newExpenseModal').close()" class="btn btn-sm btn-ghost text-slate-400 hover:text-white rounded-xl">{{ __('finance.cancel') }}</button>
                    <button type="submit" class="btn btn-sm btn-primary font-bold rounded-xl px-5">{{ __('finance.save') }}</button>
                </div>
            </form>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>

    <script>
        function calcExpenseTotal(autoCalcTax = true) {
            const amount = parseFloat(document.getElementById('exp_amount').value) || 0;
            let tax = parseFloat(document.getElementById('exp_tax').value) || 0;

            if (autoCalcTax) {
                tax = Math.round(amount * 0.15 * 100) / 100;
                document.getElementById('exp_tax').value = tax > 0 ? tax.toFixed(2) : '0.00';
            }

            const total = amount + tax;
            document.getElementById('exp_total').value = total.toFixed(2);
        }
    </script>
</x-layouts.admin>
