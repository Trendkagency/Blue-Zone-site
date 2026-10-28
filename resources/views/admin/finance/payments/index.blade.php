<x-layouts.admin :title="app()->getLocale() === 'ar' ? 'سندات وقيود الصرف - Payment Entry' : 'Payment Entry - Finance'">
    <div class="space-y-6" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

        <!-- Top Breadcrumbs & Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
            <div>
                <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 mb-1">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-primary transition">{{ __('admin.menu.dashboard') }}</a>
                    <span>/</span>
                    <a href="{{ route('admin.finance.dashboard') }}" class="hover:text-primary transition">{{ app()->getLocale() === 'ar' ? 'المالية' : 'Finance' }}</a>
                    <span>/</span>
                    <span class="text-slate-900 dark:text-slate-100 font-bold">{{ app()->getLocale() === 'ar' ? 'سندات وقيود الصرف' : 'Payment Entry' }}</span>
                </div>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2.5">
                    <span class="w-3 h-8 bg-rose-600 rounded-sm"></span>
                    {{ app()->getLocale() === 'ar' ? 'سندات وقيود الصرف المالي (Payment Entry)' : 'Payment Entry & Disbursements' }}
                </h1>
                <p class="text-xs font-medium text-slate-600 dark:text-slate-300 mt-0.5">
                    {{ app()->getLocale() === 'ar' ? 'إدارة قيود وسندات الصرف للموردين، والشحن، والمصروفات التشغيلية، وتسوية فواتير المشتريات والمدفوعات العامة.' : 'Manage corporate payments, vendor bill settlements, transportation, operating expenses, and general disbursements.' }}
                </p>
            </div>

            <!-- Top Action Buttons -->
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('admin.finance.payments.export', request()->query()) }}" class="btn btn-secondary text-xs font-bold py-2 px-3 shadow-sm flex items-center gap-1.5 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200">
                    <i class="fa-solid fa-file-excel text-emerald-600 dark:text-emerald-400"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'تصدير إكسل' : 'Export Excel' }}</span>
                </a>

                <button type="button" onclick="openPaymentModal()" class="btn text-xs font-bold py-2 px-3.5 shadow-md flex items-center gap-1.5 text-white bg-rose-700 hover:bg-rose-800 border-rose-700 transition cursor-pointer">
                    <i class="fa-solid fa-plus"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'تسجيل سند وقيد صرف جديد' : '+ New Payment Entry' }}</span>
                </button>
            </div>
        </div>

        @if(session('status'))
            <div class="p-3.5 rounded-xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-800 dark:text-emerald-300 text-xs font-bold flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-circle-check text-sm"></i>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <!-- KPI Metric Summary Badges (High Contrast Light & Dark Mode) -->
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
            <!-- 1. Total Gross Disbursed -->
            <div class="card p-3.5 border border-rose-200 dark:border-rose-900 bg-gradient-to-br from-rose-50/70 to-white dark:from-slate-900 dark:to-slate-900 shadow-sm relative overflow-hidden">
                <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-rose-600 to-amber-600"></div>
                <div class="flex items-center justify-between text-xs text-slate-700 dark:text-slate-300 mb-1 font-bold">
                    <span>{{ app()->getLocale() === 'ar' ? 'إجمالي المدفوع شامل الضريبة' : 'Total Gross Disbursed' }}</span>
                    <span class="w-7 h-7 rounded-lg bg-rose-100 dark:bg-rose-950/60 flex items-center justify-center text-rose-700 dark:text-rose-300">
                        <i class="fa-solid fa-money-bill-wave text-xs"></i>
                    </span>
                </div>
                <div class="text-xl sm:text-2xl font-black tabular-nums tracking-tight text-rose-700 dark:text-rose-400">
                    {{ number_format($totalGross, 2) }} <span class="text-xs font-bold text-slate-500 dark:text-slate-400">SAR</span>
                </div>
                <div class="text-[11px] font-bold text-rose-700 dark:text-rose-400 mt-1 flex items-center gap-1">
                    <i class="fa-solid fa-receipt"></i> {{ $totalEntries }} {{ app()->getLocale() === 'ar' ? 'سند مسجل' : 'Entries' }}
                </div>
            </div>

            <!-- 2. Amount Excl. VAT -->
            <div class="card p-3.5 border border-slate-300 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm relative overflow-hidden">
                <div class="absolute top-0 inset-x-0 h-1 bg-emerald-500"></div>
                <div class="flex items-center justify-between text-xs text-slate-700 dark:text-slate-300 mb-1 font-bold">
                    <span>{{ app()->getLocale() === 'ar' ? 'صافي المبالغ بدون الضريبة' : 'Amount Excl. VAT' }}</span>
                    <span class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                        <i class="fa-solid fa-file-invoice-dollar text-xs"></i>
                    </span>
                </div>
                <div class="text-xl sm:text-2xl font-black tabular-nums text-slate-900 dark:text-white">
                    {{ number_format($totalNet, 2) }} <span class="text-xs font-bold text-slate-500 dark:text-slate-400">SAR</span>
                </div>
                <div class="text-[11px] font-bold text-emerald-700 dark:text-emerald-400 mt-1 flex items-center gap-1">
                    <i class="fa-solid fa-check"></i> {{ app()->getLocale() === 'ar' ? 'صافي تكلفة المشتريات والمصروفات' : 'Net Recognized Costs' }}
                </div>
            </div>

            <!-- 3. VAT Component (15% Input Tax) -->
            <div class="card p-3.5 border border-slate-300 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm relative overflow-hidden">
                <div class="absolute top-0 inset-x-0 h-1 bg-amber-500"></div>
                <div class="flex items-center justify-between text-xs text-slate-700 dark:text-slate-300 mb-1 font-bold">
                    <span>{{ app()->getLocale() === 'ar' ? 'ضريبة المدخلات (15% Input VAT)' : 'Input VAT (15%)' }}</span>
                    <span class="w-7 h-7 rounded-lg bg-amber-50 dark:bg-amber-950/40 flex items-center justify-center text-amber-600 dark:text-amber-400">
                        <i class="fa-solid fa-receipt text-xs"></i>
                    </span>
                </div>
                <div class="text-xl sm:text-2xl font-black tabular-nums text-slate-900 dark:text-white">
                    {{ number_format($totalVat, 2) }} <span class="text-xs font-bold text-slate-500 dark:text-slate-400">SAR</span>
                </div>
                <div class="text-[11px] font-bold text-amber-700 dark:text-amber-400 mt-1 flex items-center gap-1">
                    <i class="fa-solid fa-scale-balanced"></i> {{ app()->getLocale() === 'ar' ? 'ضريبة مدخلات قابلة للاسترداد من الزكاة' : 'Deductible Input Tax' }}
                </div>
            </div>

            <!-- 4. Pending Cheques -->
            <div class="card p-3.5 border border-slate-300 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm relative overflow-hidden">
                <div class="absolute top-0 inset-x-0 h-1 bg-purple-500"></div>
                <div class="flex items-center justify-between text-xs text-slate-700 dark:text-slate-300 mb-1 font-bold">
                    <span>{{ app()->getLocale() === 'ar' ? 'شيكات معلقة تحت الصرف' : 'Uncleared Cheques' }}</span>
                    <span class="w-7 h-7 rounded-lg bg-purple-50 dark:bg-purple-950/40 flex items-center justify-center text-purple-600 dark:text-purple-400">
                        <i class="fa-solid fa-clock-rotate-left text-xs"></i>
                    </span>
                </div>
                <div class="text-xl sm:text-2xl font-black tabular-nums text-purple-700 dark:text-purple-300">
                    {{ number_format($totalPendingCheques, 2) }} <span class="text-xs font-bold text-slate-500 dark:text-slate-400">SAR</span>
                </div>
                <div class="text-[11px] font-bold text-purple-700 dark:text-purple-400 mt-1 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-purple-500 animate-pulse"></span>
                    {{ app()->getLocale() === 'ar' ? 'التزامات شيكات مؤجلة الصرف' : 'Pending Bank Clearance' }}
                </div>
            </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="card p-3.5 border border-slate-300 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 shadow-sm">
            <form method="GET" action="{{ route('admin.finance.payments.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-6 gap-3 items-end">
                <input type="hidden" name="sort_by" id="filterSortBy" value="{{ $sortBy }}">
                <input type="hidden" name="sort_dir" id="filterSortDir" value="{{ $sortDir }}">
                <input type="hidden" name="per_page" id="filterPerPage" value="{{ $perPage }}">
                
                <div>
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300 block mb-1">{{ app()->getLocale() === 'ar' ? 'البحث بالرقم أو المستفيد أو البيان' : 'Search' }}</label>
                    <input type="text" name="search" value="{{ $search }}" placeholder="{{ app()->getLocale() === 'ar' ? 'رقم السند، المستفيد، البيان...' : 'No., payee, description...' }}" class="form-control text-xs font-medium text-slate-900 dark:text-white bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-700">
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300 block mb-1">{{ app()->getLocale() === 'ar' ? 'التصنيف (Category)' : 'Category' }}</label>
                    <select name="category" class="form-control text-xs font-medium text-slate-900 dark:text-white bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-700">
                        <option value="">{{ app()->getLocale() === 'ar' ? '-- كافة التصنيفات --' : '-- All Categories --' }}</option>
                        @foreach($categories as $catKey => $catInfo)
                            <option value="{{ $catKey }}" {{ $category === $catKey ? 'selected' : '' }}>
                                {{ app()->getLocale() === 'ar' ? $catInfo['name_ar'] : $catInfo['name_en'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300 block mb-1">{{ app()->getLocale() === 'ar' ? 'الحساب / البنك المصروف منه' : 'Account / Bank' }}</label>
                    <select name="account_id" class="form-control text-xs font-medium text-slate-900 dark:text-white bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-700">
                        <option value="">{{ app()->getLocale() === 'ar' ? '-- كافة الحسابات --' : '-- All Accounts --' }}</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}" {{ $accountId == $acc->id ? 'selected' : '' }}>
                                {{ $acc->name_en }} / {{ $acc->name_ar }} ({{ number_format($acc->current_balance, 2) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300 block mb-1">{{ app()->getLocale() === 'ar' ? 'حالة السند / الشيك' : 'Status' }}</label>
                    <select name="status" class="form-control text-xs font-medium text-slate-900 dark:text-white bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-700">
                        <option value="">{{ app()->getLocale() === 'ar' ? '-- كافة الحالات --' : '-- All Statuses --' }}</option>
                        <option value="posted" {{ $status === 'posted' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'مصروف ومرحل (Posted)' : 'Posted' }}</option>
                        <option value="pending_clearance" {{ $status === 'pending_clearance' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'شيك معلق (Pending Cheque)' : 'Pending Cheque' }}</option>
                    </select>
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300 block mb-1">{{ app()->getLocale() === 'ar' ? 'الفترة من / إلى' : 'Date Range' }}</label>
                    <div class="flex items-center gap-1.5">
                        <input type="date" name="date_from" value="{{ $dateFrom }}" class="form-control text-xs font-medium text-slate-900 dark:text-white bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-700 px-1.5">
                        <input type="date" name="date_to" value="{{ $dateTo }}" class="form-control text-xs font-medium text-slate-900 dark:text-white bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-700 px-1.5">
                    </div>
                </div>

                <div class="flex items-center gap-1.5">
                    <button type="submit" class="btn text-xs font-bold py-2 px-3 flex-1 text-white shadow-sm bg-rose-700 hover:bg-rose-800 border-rose-700">
                        <i class="fa-solid fa-filter mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'تصفية' : 'Filter' }}
                    </button>
                    @if($search || $category || $accountId || $status || $dateFrom || $dateTo)
                        <a href="{{ route('admin.finance.payments.index') }}" class="btn btn-secondary text-xs font-bold py-2 px-2.5 text-slate-700 dark:text-slate-200" title="Reset Filters">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Corporate Ledger Table (Executive SaaS Edition) -->
        <div id="paymentLedgerCard" class="card border border-slate-200 dark:border-slate-800 shadow-sm rounded-2xl overflow-hidden bg-white dark:bg-slate-900 transition-all duration-200">
            <!-- Executive Header Ribbon: High-Contrast Light & Dark Mode -->
            <div class="px-5 py-4 bg-slate-50/95 dark:bg-[#062B49] border-b border-slate-200 dark:border-slate-700/80 flex flex-col md:flex-row md:items-center justify-between gap-3.5 transition-colors">
                <div class="flex items-center gap-3 flex-wrap">
                    <span class="w-10 h-10 rounded-xl bg-rose-100 dark:bg-rose-950/70 text-rose-700 dark:text-rose-300 border border-rose-200/80 dark:border-rose-800 flex items-center justify-center shadow-xs shrink-0">
                        <i class="fa-solid fa-money-bill-transfer text-base"></i>
                    </span>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h2 class="text-sm sm:text-base font-black tracking-wide uppercase m-0 text-slate-900 dark:text-white">
                                {{ app()->getLocale() === 'ar' ? 'دفتر قيود وسندات الصرف المالي' : 'Payment Entry Ledger' }}
                            </h2>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100/90 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300 border border-rose-200 dark:border-rose-900 shadow-2xs">
                                <span>{{ app()->getLocale() === 'ar' ? 'معروض' : 'Showing' }}</span>
                                <span id="displayShowingCount" class="font-black text-slate-900 dark:text-white">{{ $payments->count() }}</span>
                                <span class="text-slate-400 dark:text-slate-500">/</span>
                                <span id="displayTotalCount" class="font-black text-slate-900 dark:text-white">{{ $payments->total() }}</span>
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 m-0 mt-0.5 hidden sm:flex items-center gap-1.5 font-medium">
                            <span>Blue Zone Pharmaceuticals ERP</span>
                            <span class="text-slate-300 dark:text-slate-600">&bull;</span>
                            <span>Financial Disbursement Ledger</span>
                            <span class="text-slate-300 dark:text-slate-600">&bull;</span>
                            <span class="inline-flex items-center gap-1 text-emerald-600 dark:text-emerald-400 font-bold">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                Live Outflow Audit
                            </span>
                        </p>
                    </div>
                </div>

                <!-- Ledger Quick Toolbar: Sort Pill & Batch Selector & Auto-Load Toggle -->
                <div class="flex items-center gap-2.5 flex-wrap">
                    <!-- Current Sort Indicator Pill -->
                    <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white dark:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 shadow-2xs">
                        <i class="fa-solid {{ $sortDir === 'asc' ? 'fa-arrow-up-short-wide' : 'fa-arrow-down-wide-short' }} text-rose-600 dark:text-rose-400"></i>
                        <span class="text-slate-500 dark:text-slate-400 font-medium">{{ app()->getLocale() === 'ar' ? 'الترتيب:' : 'Sorted by:' }}</span>
                        <span class="font-extrabold text-slate-900 dark:text-white capitalize">{{ str_replace('_', ' ', $sortBy) }}</span>
                        <span class="text-[10px] uppercase font-bold text-rose-600 dark:text-rose-400">({{ $sortDir }})</span>
                    </div>

                    <!-- Auto-load on Scroll Toggle -->
                    <label class="inline-flex items-center gap-2 text-xs font-bold text-slate-700 dark:text-slate-200 cursor-pointer select-none bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700/60 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 transition shadow-2xs" title="{{ app()->getLocale() === 'ar' ? 'تحميل تلقائي عند التمرير لأسفل الجدول' : 'Automatically load more records when scrolling down' }}">
                        <input type="checkbox" id="toggleAutoLazy" onchange="toggleAutoScroll(this.checked)" class="rounded text-rose-600 dark:text-rose-500 focus:ring-0 cursor-pointer accent-rose-600">
                        <span class="flex items-center gap-1">
                            <i class="fa-solid fa-bolt text-amber-500 text-[11px]"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'تحميل تلقائي بالتمرير' : 'Auto-load on scroll' }}</span>
                        </span>
                    </label>

                    <!-- Batch Per Page Selector -->
                    <div class="inline-flex items-center gap-1.5 bg-white dark:bg-slate-800 px-2.5 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs text-slate-700 dark:text-slate-200 font-bold shadow-2xs">
                        <span class="text-[11px] text-slate-500 dark:text-slate-400 font-medium">{{ app()->getLocale() === 'ar' ? 'الدفعة:' : 'Batch:' }}</span>
                        <select id="selectPerPage" onchange="changePerPage(this.value)" class="bg-transparent text-slate-900 dark:text-white font-black text-xs cursor-pointer focus:outline-none border-0 p-0 pr-1">
                            <option value="15" {{ $perPage == 15 ? 'selected' : '' }} class="text-slate-900 bg-white dark:bg-slate-900 dark:text-white">15</option>
                            <option value="20" {{ $perPage == 20 ? 'selected' : '' }} class="text-slate-900 bg-white dark:bg-slate-900 dark:text-white">20</option>
                            <option value="50" {{ $perPage == 50 ? 'selected' : '' }} class="text-slate-900 bg-white dark:bg-slate-900 dark:text-white">50</option>
                            <option value="100" {{ $perPage == 100 ? 'selected' : '' }} class="text-slate-900 bg-white dark:bg-slate-900 dark:text-white">100</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Spreadsheet Grid Table with Interactive Sorting -->
            <div class="overflow-x-auto relative">
                <table class="w-full text-left text-xs sm:text-[13px] border-collapse" style="font-family: 'Outfit', 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                    <thead>
                        <tr class="bg-slate-100/90 dark:bg-slate-800/90 text-slate-700 dark:text-slate-200 font-extrabold border-b border-slate-200 dark:border-slate-700 select-none text-[11px] uppercase tracking-wider">
                            
                            <!-- 1. Date -->
                            <th scope="col" onclick="triggerSort('entry_date')"
                                class="py-3.5 px-3 text-center font-bold transition-colors cursor-pointer hover:bg-slate-200/70 dark:hover:bg-slate-700/60 group {{ $sortBy === 'entry_date' ? 'bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300 font-black border-b-2 border-rose-600 dark:border-rose-400' : '' }}" style="width: 110px;"
                                title="{{ app()->getLocale() === 'ar' ? 'انقر للترتيب حسب التاريخ' : 'Click to sort by date' }}">
                                <div class="inline-flex items-center gap-1.5 justify-center">
                                    <span>{{ app()->getLocale() === 'ar' ? 'التاريخ' : 'Date' }}</span>
                                    @if($sortBy === 'entry_date')
                                        <i class="fa-solid fa-arrow-{{ $sortDir === 'asc' ? 'up-short-wide' : 'down-wide-short' }} text-rose-600 dark:text-rose-400"></i>
                                    @else
                                        <i class="fa-solid fa-sort text-slate-400 dark:text-slate-500 group-hover:text-rose-600 dark:group-hover:text-rose-400 text-[10px] transition-colors"></i>
                                    @endif
                                </div>
                            </th>

                            <!-- 2. Payment No. -->
                            <th scope="col" onclick="triggerSort('payment_number')"
                                class="py-3.5 px-3 text-center font-bold transition-colors cursor-pointer hover:bg-slate-200/70 dark:hover:bg-slate-700/60 group {{ $sortBy === 'payment_number' ? 'bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300 font-black border-b-2 border-rose-600 dark:border-rose-400' : '' }}" style="width: 130px;"
                                title="{{ app()->getLocale() === 'ar' ? 'انقر للترتيب حسب رقم السند' : 'Click to sort by payment number' }}">
                                <div class="inline-flex items-center gap-1.5 justify-center">
                                    <span>{{ app()->getLocale() === 'ar' ? 'رقم السند' : 'Payment No.' }}</span>
                                    @if($sortBy === 'payment_number')
                                        <i class="fa-solid fa-arrow-{{ $sortDir === 'asc' ? 'up-short-wide' : 'down-wide-short' }} text-rose-600 dark:text-rose-400"></i>
                                    @else
                                        <i class="fa-solid fa-sort text-slate-400 dark:text-slate-500 group-hover:text-rose-600 dark:group-hover:text-rose-400 text-[10px] transition-colors"></i>
                                    @endif
                                </div>
                            </th>

                            <!-- 3. Category -->
                            <th scope="col" onclick="triggerSort('category')"
                                class="py-3.5 px-3 text-center font-bold transition-colors cursor-pointer hover:bg-slate-200/70 dark:hover:bg-slate-700/60 group {{ $sortBy === 'category' ? 'bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300 font-black border-b-2 border-rose-600 dark:border-rose-400' : '' }}" style="width: 165px;"
                                title="{{ app()->getLocale() === 'ar' ? 'انقر للترتيب حسب التصنيف' : 'Click to sort by category' }}">
                                <div class="inline-flex items-center gap-1.5 justify-center">
                                    <span>{{ app()->getLocale() === 'ar' ? 'التصنيف' : 'Category' }}</span>
                                    @if($sortBy === 'category')
                                        <i class="fa-solid fa-arrow-{{ $sortDir === 'asc' ? 'up-short-wide' : 'down-wide-short' }} text-rose-600 dark:text-rose-400"></i>
                                    @else
                                        <i class="fa-solid fa-sort text-slate-400 dark:text-slate-500 group-hover:text-rose-600 dark:group-hover:text-rose-400 text-[10px] transition-colors"></i>
                                    @endif
                                </div>
                            </th>

                            <!-- 4. Description -->
                            <th scope="col" onclick="triggerSort('description')"
                                class="py-3.5 px-3 font-bold transition-colors cursor-pointer hover:bg-slate-200/70 dark:hover:bg-slate-700/60 group {{ $sortBy === 'description' ? 'bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300 font-black border-b-2 border-rose-600 dark:border-rose-400' : '' }}" style="min-width: 180px;"
                                title="{{ app()->getLocale() === 'ar' ? 'انقر للترتيب حسب البيان' : 'Click to sort by description' }}">
                                <div class="inline-flex items-center gap-1.5">
                                    <span>{{ app()->getLocale() === 'ar' ? 'البيان والمرجع' : 'Description' }}</span>
                                    @if($sortBy === 'description')
                                        <i class="fa-solid fa-arrow-{{ $sortDir === 'asc' ? 'up-short-wide' : 'down-wide-short' }} text-rose-600 dark:text-rose-400"></i>
                                    @else
                                        <i class="fa-solid fa-sort text-slate-400 dark:text-slate-500 group-hover:text-rose-600 dark:group-hover:text-rose-400 text-[10px] transition-colors"></i>
                                    @endif
                                </div>
                            </th>

                            <!-- 5. Supplier Code -->
                            <th scope="col" onclick="triggerSort('supplier_code')"
                                class="py-3.5 px-3 text-center font-bold transition-colors cursor-pointer hover:bg-slate-200/70 dark:hover:bg-slate-700/60 group {{ $sortBy === 'supplier_code' ? 'bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300 font-black border-b-2 border-rose-600 dark:border-rose-400' : '' }}" style="width: 110px;"
                                title="{{ app()->getLocale() === 'ar' ? 'انقر للترتيب حسب كود المورد' : 'Click to sort by supplier code' }}">
                                <div class="inline-flex items-center gap-1.5 justify-center">
                                    <span>{{ app()->getLocale() === 'ar' ? 'كود المورد' : 'Supplier Code' }}</span>
                                    @if($sortBy === 'supplier_code')
                                        <i class="fa-solid fa-arrow-{{ $sortDir === 'asc' ? 'up-short-wide' : 'down-wide-short' }} text-rose-600 dark:text-rose-400"></i>
                                    @else
                                        <i class="fa-solid fa-sort text-slate-400 dark:text-slate-500 group-hover:text-rose-600 dark:group-hover:text-rose-400 text-[10px] transition-colors"></i>
                                    @endif
                                </div>
                            </th>

                            <!-- 6. Supplier / Payee -->
                            <th scope="col" onclick="triggerSort('payee_name')"
                                class="py-3.5 px-3 font-bold transition-colors cursor-pointer hover:bg-slate-200/70 dark:hover:bg-slate-700/60 group {{ $sortBy === 'payee_name' ? 'bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300 font-black border-b-2 border-rose-600 dark:border-rose-400' : '' }}" style="min-width: 180px;"
                                title="{{ app()->getLocale() === 'ar' ? 'انقر للترتيب حسب اسم المورد / المستفيد' : 'Click to sort by payee name' }}">
                                <div class="inline-flex items-center gap-1.5">
                                    <span>{{ app()->getLocale() === 'ar' ? 'المورد / المستفيد' : 'Supplier / Payee' }}</span>
                                    @if($sortBy === 'payee_name')
                                        <i class="fa-solid fa-arrow-{{ $sortDir === 'asc' ? 'up-short-wide' : 'down-wide-short' }} text-rose-600 dark:text-rose-400"></i>
                                    @else
                                        <i class="fa-solid fa-sort text-slate-400 dark:text-slate-500 group-hover:text-rose-600 dark:group-hover:text-rose-400 text-[10px] transition-colors"></i>
                                    @endif
                                </div>
                            </th>

                            <!-- 7. Amount Excl. VAT -->
                            <th scope="col" onclick="triggerSort('amount_excl_vat')"
                                class="py-3.5 px-3 text-right font-bold transition-colors cursor-pointer hover:bg-slate-200/70 dark:hover:bg-slate-700/60 group {{ $sortBy === 'amount_excl_vat' ? 'bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300 font-black border-b-2 border-rose-600 dark:border-rose-400' : '' }}" style="width: 130px;"
                                title="{{ app()->getLocale() === 'ar' ? 'انقر للترتيب حسب الصافي' : 'Click to sort by net amount' }}">
                                <div class="inline-flex items-center gap-1.5 justify-end">
                                    <span>{{ app()->getLocale() === 'ar' ? 'بدون ضريبة' : 'Amount Excl.' }}</span>
                                    @if($sortBy === 'amount_excl_vat')
                                        <i class="fa-solid fa-arrow-{{ $sortDir === 'asc' ? 'up-short-wide' : 'down-wide-short' }} text-rose-600 dark:text-rose-400"></i>
                                    @else
                                        <i class="fa-solid fa-sort text-slate-400 dark:text-slate-500 group-hover:text-rose-600 dark:group-hover:text-rose-400 text-[10px] transition-colors"></i>
                                    @endif
                                </div>
                            </th>

                            <!-- 8. VAT (15%) -->
                            <th scope="col" onclick="triggerSort('vat_amount')"
                                class="py-3.5 px-3 text-right font-bold transition-colors cursor-pointer hover:bg-slate-200/70 dark:hover:bg-slate-700/60 group {{ $sortBy === 'vat_amount' ? 'bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300 font-black border-b-2 border-rose-600 dark:border-rose-400' : '' }}" style="width: 110px;"
                                title="{{ app()->getLocale() === 'ar' ? 'انقر للترتيب حسب الضريبة' : 'Click to sort by VAT amount' }}">
                                <div class="inline-flex items-center gap-1.5 justify-end">
                                    <span>{{ app()->getLocale() === 'ar' ? 'الضريبة (15%)' : 'VAT (15%)' }}</span>
                                    @if($sortBy === 'vat_amount')
                                        <i class="fa-solid fa-arrow-{{ $sortDir === 'asc' ? 'up-short-wide' : 'down-wide-short' }} text-rose-600 dark:text-rose-400"></i>
                                    @else
                                        <i class="fa-solid fa-sort text-slate-400 dark:text-slate-500 group-hover:text-rose-600 dark:group-hover:text-rose-400 text-[10px] transition-colors"></i>
                                    @endif
                                </div>
                            </th>

                            <!-- 9. Gross Amount -->
                            <th scope="col" onclick="triggerSort('gross_amount')"
                                class="py-3.5 px-3 text-right font-extrabold transition-colors cursor-pointer hover:bg-slate-200/70 dark:hover:bg-slate-700/60 group {{ $sortBy === 'gross_amount' ? 'bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300 font-black border-b-2 border-rose-600 dark:border-rose-400' : 'text-slate-900 dark:text-white' }}" style="width: 140px;"
                                title="{{ app()->getLocale() === 'ar' ? 'انقر للترتيب حسب الإجمالي شامل الضريبة' : 'Click to sort by gross amount' }}">
                                <div class="inline-flex items-center gap-1.5 justify-end">
                                    <span>{{ app()->getLocale() === 'ar' ? 'الإجمالي' : 'Gross Total' }}</span>
                                    @if($sortBy === 'gross_amount')
                                        <i class="fa-solid fa-arrow-{{ $sortDir === 'asc' ? 'up-short-wide' : 'down-wide-short' }} text-rose-600 dark:text-rose-400"></i>
                                    @else
                                        <i class="fa-solid fa-sort text-slate-400 dark:text-slate-500 group-hover:text-rose-600 dark:group-hover:text-rose-400 text-[10px] transition-colors"></i>
                                    @endif
                                </div>
                            </th>

                            <!-- 10. Payment Method & Account -->
                            <th scope="col" onclick="triggerSort('payment_method')"
                                class="py-3.5 px-3 text-center font-bold transition-colors cursor-pointer hover:bg-slate-200/70 dark:hover:bg-slate-700/60 group {{ $sortBy === 'payment_method' ? 'bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300 font-black border-b-2 border-rose-600 dark:border-rose-400' : '' }}" style="min-width: 160px;"
                                title="{{ app()->getLocale() === 'ar' ? 'انقر للترتيب حسب طريقة الصرف' : 'Click to sort by payment method' }}">
                                <div class="inline-flex items-center gap-1.5 justify-center">
                                    <span>{{ app()->getLocale() === 'ar' ? 'طريقة الصرف / الحساب' : 'Payment / Account' }}</span>
                                    @if($sortBy === 'payment_method')
                                        <i class="fa-solid fa-arrow-{{ $sortDir === 'asc' ? 'up-short-wide' : 'down-wide-short' }} text-rose-600 dark:text-rose-400"></i>
                                    @else
                                        <i class="fa-solid fa-sort text-slate-400 dark:text-slate-500 group-hover:text-rose-600 dark:group-hover:text-rose-400 text-[10px] transition-colors"></i>
                                    @endif
                                </div>
                            </th>

                            <!-- 11. Action -->
                            <th scope="col" class="py-3.5 px-3 text-center font-bold text-slate-600 dark:text-slate-400" style="width: 100px;">
                                {{ app()->getLocale() === 'ar' ? 'الإجراء' : 'Action' }}
                            </th>
                        </tr>
                    </thead>

                    <!-- Dynamic Table Rows -->
                    <tbody id="paymentTableBody" class="divide-y divide-slate-200/80 dark:divide-slate-800 bg-white dark:bg-slate-900 font-medium">
                        @include('admin.finance.payments._rows', ['payments' => $payments])
                    </tbody>

                    <!-- Executive High-Contrast Summary Footer -->
                    <tfoot>
                        <tr class="bg-slate-100/95 dark:bg-slate-800/95 text-slate-900 dark:text-white font-black border-t-2 border-slate-300 dark:border-slate-700">
                            <td colspan="6" class="py-3.5 px-4 text-right uppercase tracking-wider font-extrabold text-xs sm:text-[13px]">
                                <div class="flex items-center justify-end gap-2 text-slate-700 dark:text-slate-300">
                                    <i class="fa-solid fa-calculator text-rose-600 dark:text-rose-400"></i>
                                    <span>{{ app()->getLocale() === 'ar' ? 'إجمالي السجلات المعروضة (' : 'LOADED TOTALS (' }}</span>
                                    <span id="footerRowCount" class="text-rose-700 dark:text-rose-300 font-black tabular-nums">{{ $payments->count() }}</span>
                                    <span>{{ app()->getLocale() === 'ar' ? 'سند):' : 'ENTRIES):' }}</span>
                                </div>
                            </td>

                            <!-- Dynamic Sum Amount Excl -->
                            <td class="py-3.5 px-3.5 text-right tabular-nums font-bold text-slate-900 dark:text-white whitespace-nowrap">
                                <span id="footerExclTotal">{{ number_format($payments->sum('amount_excl_vat'), 2) }}</span>
                                <span class="text-[10px] text-slate-500 font-semibold ml-0.5">SAR</span>
                            </td>

                            <!-- Dynamic Sum VAT -->
                            <td class="py-3.5 px-3.5 text-right tabular-nums font-bold text-amber-700 dark:text-amber-400 whitespace-nowrap">
                                <span id="footerVatTotal">{{ number_format($payments->sum('vat_amount'), 2) }}</span>
                                <span class="text-[10px] text-amber-600/70 font-semibold ml-0.5">SAR</span>
                            </td>

                            <!-- Dynamic Sum Gross (Highlighted Card) -->
                            <td class="py-3.5 px-3.5 text-right tabular-nums whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 font-black text-slate-950 dark:text-white bg-rose-100/90 dark:bg-slate-700 px-2.5 py-1 rounded-md border border-rose-300/60 dark:border-slate-600 shadow-2xs">
                                    <span id="footerGrossTotal" class="text-xs sm:text-[13px] font-black text-rose-700 dark:text-rose-300">{{ number_format($payments->sum('gross_amount'), 2) }}</span>
                                    <span class="text-[10px] font-bold text-rose-700 dark:text-rose-300">SAR</span>
                                </span>
                            </td>

                            <td colspan="2" class="py-3.5 px-3.5"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Modern Lazy Loading Control Hub -->
            <div id="lazyLoadingHub" class="p-4 sm:p-5 border-t border-slate-200 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-900/80 flex flex-col sm:flex-row items-center justify-between gap-4">
                
                <!-- Left: Visual Progress Bar & Live Count Status -->
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <div class="flex flex-col gap-1 w-full sm:w-64">
                        <div class="flex items-center justify-between text-xs text-slate-600 dark:text-slate-400 font-bold">
                            <span>
                                {{ app()->getLocale() === 'ar' ? 'عرض' : 'Showing' }}
                                <strong id="lblShowingCount" class="text-slate-900 dark:text-white tabular-nums">{{ $payments->count() }}</strong>
                                {{ app()->getLocale() === 'ar' ? 'من أصل' : 'of' }}
                                <strong id="lblTotalCount" class="text-slate-900 dark:text-white tabular-nums">{{ $payments->total() }}</strong>
                            </span>
                            <span id="lblPercentage" class="tabular-nums font-black text-rose-700 dark:text-rose-400">
                                {{ $payments->total() > 0 ? min(100, round(($payments->count() / $payments->total()) * 100)) : 100 }}%
                            </span>
                        </div>
                        <!-- Animated Progress Track -->
                        <div class="w-full bg-slate-200 dark:bg-slate-700 h-2 rounded-full overflow-hidden shadow-inner">
                            <div id="hubProgressBar"
                                 class="h-full bg-gradient-to-r from-rose-600 to-amber-600 rounded-full transition-all duration-300"
                                 style="width: {{ $payments->total() > 0 ? min(100, round(($payments->count() / $payments->total()) * 100)) : 100 }}%;">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: "See More" & "See Less" Action Cluster -->
                <div class="flex items-center gap-2.5 flex-wrap justify-center sm:justify-end w-full sm:w-auto">
                    <!-- 1. "See Less" -->
                    <button type="button" id="btnSeeLess" onclick="lazyShowLess()"
                            class="hidden btn btn-secondary text-xs font-bold py-2.5 px-4 rounded-xl border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition flex items-center gap-1.5 cursor-pointer shadow-2xs">
                        <i class="fa-solid fa-angles-up text-slate-500 dark:text-slate-400 text-xs"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'عرض أقل (طي لأول ' . $perPage . ' قيد)' : 'See Less (Fold to ' . $perPage . ')' }}</span>
                    </button>

                    <!-- 2. "See More" -->
                    <button type="button" id="btnSeeMore" onclick="lazyLoadMore()"
                            class="{{ $payments->hasMorePages() ? 'inline-flex' : 'hidden' }} btn text-xs font-black py-2.5 px-6 rounded-xl shadow-md items-center gap-2 text-white transition hover:brightness-110 active:scale-95 cursor-pointer bg-gradient-to-r from-rose-600 to-rose-700">
                        <i class="fa-solid fa-angles-down text-rose-200 text-xs"></i>
                        <span id="btnSeeMoreLabel">{{ app()->getLocale() === 'ar' ? 'عرض المزيد (+ ' . $perPage . ' سجل)' : 'See More (+' . $perPage . ' Records)' }}</span>
                    </button>

                    <!-- 3. "All Loaded" Indicator -->
                    <div id="allLoadedNotice" class="{{ $payments->hasMorePages() ? 'hidden' : 'inline-flex' }} items-center gap-2 px-3.5 py-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 text-xs font-bold border border-emerald-200 dark:border-emerald-800 shadow-2xs">
                        <i class="fa-solid fa-circle-check text-emerald-500 text-sm"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'تم استعراض كافة السجلات بالكامل' : 'All ' . $payments->total() . ' entries loaded' }}</span>
                    </div>
                </div>
            </div>

            <!-- Intersection Sentinel for automatic scroll lazy loading -->
            <div id="lazySentinel" class="h-1 w-full pointer-events-none"></div>
        </div>

    </div>

    <!-- Interactive Professional Payment Entry Modal -->
    <div id="newPaymentModal" class="fixed inset-0 z-50 bg-slate-950/75 backdrop-blur-sm hidden flex items-center justify-center p-3 sm:p-4 overflow-y-auto">
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-300 dark:border-slate-800 w-full max-w-3xl overflow-hidden animate-in fade-in zoom-in-95 duration-200 my-8" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
            
            <!-- Top Accent Stripe -->
            <div class="h-1 w-full bg-gradient-to-r from-rose-600 via-rose-500 to-amber-500" style="height: 4px; background: linear-gradient(90deg, #e11d48 0%, #f43f5e 50%, #f59e0b 100%);"></div>

            <!-- Modal Header (High-Contrast Professional Light & Dark Mode) -->
            <div class="px-6 py-4.5 bg-slate-50/95 dark:bg-[#06243C] border-b border-slate-200 dark:border-slate-800 flex items-center justify-between gap-4 transition-colors">
                <div class="flex items-center gap-3.5 min-w-0">
                    <div class="w-11 h-11 rounded-xl bg-rose-50 dark:bg-rose-950/70 border border-rose-200/80 dark:border-rose-900/60 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0 shadow-xs">
                        <i class="fa-solid fa-receipt text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <h3 class="text-base sm:text-lg font-black text-slate-900 dark:text-white m-0 tracking-tight leading-tight">
                                {{ app()->getLocale() === 'ar' ? 'تسجيل سند وقيد صرف مالي جديد' : 'New Payment Entry' }}
                            </h3>
                            <span class="hidden sm:inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-100/80 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300 border border-rose-200 dark:border-rose-800/70">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-600 dark:bg-rose-400 animate-pulse"></span>
                                {{ app()->getLocale() === 'ar' ? 'سند صرف مالي' : 'Disbursement Voucher' }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 m-0 mt-0.5 font-medium leading-relaxed truncate max-w-xl">
                            {{ app()->getLocale() === 'ar' ? 'صرف مالي للموردين، تسوية فواتير المشتريات، الشحن، والمصروفات التشغيلية' : 'Disburse funds to suppliers, settle vendor bills, or record operational expenses' }}
                        </p>
                    </div>
                </div>
                <button type="button" onclick="closePaymentModal()" 
                        aria-label="{{ app()->getLocale() === 'ar' ? 'إغلاق' : 'Close' }}"
                        class="w-9 h-9 rounded-xl flex items-center justify-center text-slate-400 hover:text-slate-700 dark:text-slate-400 dark:hover:text-white hover:bg-slate-200/70 dark:hover:bg-slate-800 border border-transparent hover:border-slate-300 dark:hover:border-slate-700 transition cursor-pointer shrink-0">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Modal Form -->
            <form action="{{ route('admin.finance.payments.store') }}" method="POST" id="paymentForm" class="p-5 space-y-4 max-h-[82vh] overflow-y-auto">
                @csrf
                <input type="hidden" name="payee_type" id="modalPayeeType" value="supplier">

                <!-- 1. Payee Type Switcher (Supplier vs General Payee / Employee) -->
                <div>
                    <label class="text-xs font-bold text-slate-800 dark:text-slate-200 block mb-1.5">
                        {{ app()->getLocale() === 'ar' ? 'جهة الصرف والمستفيد (Payee Entity) *' : 'Payee Entity Type *' }}
                    </label>
                    <div class="grid grid-cols-2 gap-2 p-1.5 bg-slate-100 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700">
                        <button type="button" id="btnPayeeSupplier" onclick="switchPayeeType('supplier')" class="py-2 px-3 text-xs font-black rounded-lg transition flex items-center justify-center gap-1.5 bg-white dark:bg-slate-700 text-rose-700 dark:text-rose-300 shadow-sm border border-slate-200 dark:border-slate-600">
                            <i class="fa-solid fa-truck-field"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'مورد معتمد من النظام (Supplier)' : 'System Supplier' }}</span>
                        </button>
                        <button type="button" id="btnPayeeGeneral" onclick="switchPayeeType('general_payee')" class="py-2 px-3 text-xs font-bold rounded-lg transition flex items-center justify-center gap-1.5 text-slate-600 dark:text-slate-300 hover:bg-white/60 dark:hover:bg-slate-700/60">
                            <i class="fa-solid fa-user-tag"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'مستفيد عام / موظف / أخرى' : 'General Payee / Employee' }}</span>
                        </button>
                    </div>
                </div>

                <!-- 2. Supplier / Payee & Linked Purchase Invoice Selector -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <!-- Supplier Selector Container -->
                    <div id="supplierSelectorContainer" class="space-y-1">
                        <label class="text-xs font-bold text-slate-800 dark:text-slate-200 block mb-1">
                            {{ app()->getLocale() === 'ar' ? 'اختر المورد من النظام (Supplier) *' : 'Select System Supplier *' }}
                        </label>
                        <div class="relative bz-dropdown-root" id="bzRoot_modalSupplierSelect">
                            <select name="supplier_id" id="modalSupplierSelect" onchange="onSupplierChange(this)" class="hidden">
                                <option value="">-- {{ app()->getLocale() === 'ar' ? 'اختر المورد المعتمد' : 'Select Supplier' }} --</option>
                                @foreach($suppliers as $s)
                                    <option value="{{ $s->id }}"
                                            data-name="{{ $s->name_en ?: $s->name_ar }}"
                                            data-code="{{ $s->code ?: ('SUP-' . $s->id) }}">
                                        {{ $s->name_en }} / {{ $s->name_ar }} ({{ $s->code }})
                                    </option>
                                @endforeach
                            </select>

                            <button type="button" onclick="BzDropdown.toggle('modalSupplierSelect')" id="bzTrigger_modalSupplierSelect"
                                    class="w-full flex items-center justify-between gap-2 px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold text-slate-800 dark:text-slate-100 hover:border-rose-400 dark:hover:border-rose-500 focus:outline-none transition shadow-2xs cursor-pointer select-none">
                                <div class="flex items-center gap-2.5 min-w-0 flex-1">
                                    <span class="w-6 h-6 rounded-lg bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0 text-xs">
                                        <i class="fa-solid fa-truck-field"></i>
                                    </span>
                                    <span id="bzLabel_modalSupplierSelect" class="truncate text-slate-800 dark:text-slate-100">
                                        -- {{ app()->getLocale() === 'ar' ? 'اختر المورد المعتمد' : 'Select Supplier' }} --
                                    </span>
                                </div>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <span id="bzClear_modalSupplierSelect" onclick="BzDropdown.clear(event, 'modalSupplierSelect', '{{ app()->getLocale() === 'ar' ? '-- اختر المورد المعتمد --' : '-- Select Supplier --' }}')" class="hidden w-5 h-5 rounded-md hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 flex items-center justify-center text-xs transition" title="Clear">
                                        <i class="fa-solid fa-xmark"></i>
                                    </span>
                                    <i id="bzChevron_modalSupplierSelect" class="fa-solid fa-chevron-down text-slate-400 text-[10px] transition-transform duration-200"></i>
                                </div>
                            </button>

                            <!-- Dropdown Menu -->
                            <div id="bzMenu_modalSupplierSelect" class="hidden absolute left-0 right-0 top-full mt-1.5 z-50 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-150">
                                <div class="p-2 border-b border-slate-100 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-900/90">
                                    <div class="relative flex items-center">
                                        <i class="fa-solid fa-magnifying-glass absolute {{ app()->getLocale() === 'ar' ? 'right-3' : 'left-3' }} text-slate-400 text-xs pointer-events-none"></i>
                                        <input type="text" id="bzSearch_modalSupplierSelect" oninput="BzDropdown.filter('modalSupplierSelect')"
                                               placeholder="{{ app()->getLocale() === 'ar' ? 'بحث باسم المورد أو الكود...' : 'Search supplier name or code...' }}"
                                               class="w-full text-xs py-2 {{ app()->getLocale() === 'ar' ? 'pr-8 pl-8' : 'pl-8 pr-8' }} bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:border-rose-500 focus:ring-1 focus:ring-rose-500 transition">
                                        <span id="bzCount_modalSupplierSelect" class="absolute {{ app()->getLocale() === 'ar' ? 'left-2.5' : 'right-2.5' }} text-[10px] font-bold text-slate-400"></span>
                                    </div>
                                </div>
                                <div id="bzOptions_modalSupplierSelect" class="max-h-56 overflow-y-auto p-1.5 space-y-1 bz-select-scroll">
                                    @foreach($suppliers as $s)
                                        <div onclick="BzDropdown.select('modalSupplierSelect', '{{ $s->id }}', '{{ addslashes($s->name_en ?: $s->name_ar) }} ({{ $s->code ?: 'SUP-' . $s->id }})')"
                                             data-value="{{ $s->id }}"
                                             data-search="{{ strtolower($s->name_en . ' ' . $s->name_ar . ' ' . $s->code) }}"
                                             class="bz-option-item flex items-center justify-between gap-2.5 px-3 py-2 rounded-xl text-xs cursor-pointer text-slate-700 dark:text-slate-200 hover:bg-rose-50/70 dark:hover:bg-rose-950/40 hover:text-rose-900 dark:hover:text-rose-200 transition-colors">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <span class="w-2 h-2 rounded-full bg-slate-300 dark:bg-slate-600 shrink-0"></span>
                                                <div class="min-w-0">
                                                    <div class="font-bold text-slate-900 dark:text-white truncate">
                                                        {{ $s->name_en }} @if($s->name_ar && $s->name_ar !== $s->name_en) <span class="text-slate-400 dark:text-slate-500 font-normal">/ {{ $s->name_ar }}</span> @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2 shrink-0">
                                                <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-[10px] font-mono font-bold text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                                    {{ $s->code ?: ('SUP-' . $s->id) }}
                                                </span>
                                                <i class="fa-solid fa-check text-rose-600 dark:text-rose-400 text-xs hidden bz-option-check"></i>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div id="bzEmpty_modalSupplierSelect" class="hidden p-6 text-center text-xs text-slate-400 dark:text-slate-500">
                                    <i class="fa-solid fa-folder-open text-xl mb-1.5 block opacity-40"></i>
                                    <span>{{ app()->getLocale() === 'ar' ? 'لا يوجد مورد مطابق للبحث' : 'No matching suppliers found' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- General Payee Name Input (Visible when General Payee selected) -->
                    <div id="generalPayeeContainer" class="hidden space-y-1">
                        <label class="text-xs font-bold text-slate-800 dark:text-slate-200 block mb-1">
                            {{ app()->getLocale() === 'ar' ? 'اسم المستفيد / الجهة (Payee Name) *' : 'Payee / Recipient Name *' }}
                        </label>
                        <input type="text" name="payee_name" id="modalPayeeName" placeholder="{{ app()->getLocale() === 'ar' ? 'اسم المستلم، شركة النقل، موظف...' : 'Recipient name, transport carrier...' }}" class="form-control text-xs font-medium text-slate-900 dark:text-white bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-700 py-2.5">
                    </div>

                    <!-- Linked Purchase Invoice Selector (Optional Bill Settlement) -->
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-800 dark:text-slate-200 block mb-1">
                            {{ app()->getLocale() === 'ar' ? 'ربط بفاتورة مشتريات مستحقة (اختياري)' : 'Link Unpaid Purchase Bill (Optional)' }}
                        </label>
                        <div class="relative bz-dropdown-root" id="bzRoot_modalInvoiceSelect">
                            <select name="purchase_invoice_id" id="modalInvoiceSelect" onchange="onInvoiceChange(this)" class="hidden">
                                <option value="">-- {{ app()->getLocale() === 'ar' ? 'صرف حر (بدون فاتورة محددة)' : 'Direct Disbursement (No specific bill)' }} --</option>
                                @foreach($unpaidInvoices as $inv)
                                    <option value="{{ $inv->id }}"
                                            data-supplier-id="{{ $inv->supplier_id }}"
                                            data-due="{{ (float) $inv->due_balance }}"
                                            data-total="{{ (float) $inv->total_amount }}"
                                            data-num="{{ $inv->invoice_number }}">
                                        {{ $inv->invoice_number }} &bull; {{ $inv->supplier?->name_en }} ({{ number_format($inv->due_balance, 2) }} SAR Due)
                                    </option>
                                @endforeach
                            </select>

                            <button type="button" onclick="BzDropdown.toggle('modalInvoiceSelect')" id="bzTrigger_modalInvoiceSelect"
                                    class="w-full flex items-center justify-between gap-2 px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold text-slate-800 dark:text-slate-100 hover:border-rose-400 dark:hover:border-rose-500 focus:outline-none transition shadow-2xs cursor-pointer select-none">
                                <div class="flex items-center gap-2.5 min-w-0 flex-1">
                                    <span class="w-6 h-6 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0 text-xs">
                                        <i class="fa-solid fa-file-invoice-dollar"></i>
                                    </span>
                                    <span id="bzLabel_modalInvoiceSelect" class="truncate text-slate-800 dark:text-slate-100">
                                        -- {{ app()->getLocale() === 'ar' ? 'صرف حر (بدون فاتورة محددة)' : 'Direct Disbursement (No specific bill)' }} --
                                    </span>
                                </div>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <span id="bzClear_modalInvoiceSelect" onclick="BzDropdown.clear(event, 'modalInvoiceSelect', '{{ app()->getLocale() === 'ar' ? '-- صرف حر (بدون فاتورة محددة) --' : '-- Direct Disbursement (No specific bill) --' }}')" class="hidden w-5 h-5 rounded-md hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 flex items-center justify-center text-xs transition" title="Clear">
                                        <i class="fa-solid fa-xmark"></i>
                                    </span>
                                    <i id="bzChevron_modalInvoiceSelect" class="fa-solid fa-chevron-down text-slate-400 text-[10px] transition-transform duration-200"></i>
                                </div>
                            </button>

                            <!-- Dropdown Menu -->
                            <div id="bzMenu_modalInvoiceSelect" class="hidden absolute left-0 right-0 top-full mt-1.5 z-50 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-150">
                                <div class="p-2 border-b border-slate-100 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-900/90">
                                    <div class="relative flex items-center">
                                        <i class="fa-solid fa-magnifying-glass absolute {{ app()->getLocale() === 'ar' ? 'right-3' : 'left-3' }} text-slate-400 text-xs pointer-events-none"></i>
                                        <input type="text" id="bzSearch_modalInvoiceSelect" oninput="BzDropdown.filter('modalInvoiceSelect')"
                                               placeholder="{{ app()->getLocale() === 'ar' ? 'بحث برقم الفاتورة أو المورد...' : 'Search invoice # or supplier...' }}"
                                               class="w-full text-xs py-2 {{ app()->getLocale() === 'ar' ? 'pr-8 pl-8' : 'pl-8 pr-8' }} bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:border-rose-500 focus:ring-1 focus:ring-rose-500 transition">
                                        <span id="bzCount_modalInvoiceSelect" class="absolute {{ app()->getLocale() === 'ar' ? 'left-2.5' : 'right-2.5' }} text-[10px] font-bold text-slate-400"></span>
                                    </div>
                                </div>
                                <div id="bzOptions_modalInvoiceSelect" class="max-h-56 overflow-y-auto p-1.5 space-y-1 bz-select-scroll">
                                    <div onclick="BzDropdown.select('modalInvoiceSelect', '', '{{ app()->getLocale() === 'ar' ? '-- صرف حر (بدون فاتورة محددة) --' : '-- Direct Disbursement (No specific bill) --' }}')"
                                         data-value=""
                                         data-search="direct disbursement no bill"
                                         class="bz-option-item flex items-center justify-between gap-2.5 px-3 py-2 rounded-xl text-xs cursor-pointer text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                                        <span class="font-medium text-slate-500 dark:text-slate-400">-- {{ app()->getLocale() === 'ar' ? 'صرف حر (بدون فاتورة محددة)' : 'Direct Disbursement (No specific bill)' }} --</span>
                                        <i class="fa-solid fa-check text-rose-600 dark:text-rose-400 text-xs hidden bz-option-check"></i>
                                    </div>
                                    @foreach($unpaidInvoices as $inv)
                                        <div onclick="BzDropdown.select('modalInvoiceSelect', '{{ $inv->id }}', '#{{ $inv->invoice_number }} &bull; {{ number_format($inv->due_balance, 2) }} SAR')"
                                             data-value="{{ $inv->id }}"
                                             data-supplier-id="{{ $inv->supplier_id }}"
                                             data-search="{{ strtolower($inv->invoice_number . ' ' . ($inv->supplier?->name_en ?? '') . ' ' . ($inv->supplier?->name_ar ?? '')) }}"
                                             class="bz-option-item flex items-center justify-between gap-2.5 px-3 py-2 rounded-xl text-xs cursor-pointer text-slate-700 dark:text-slate-200 hover:bg-rose-50/70 dark:hover:bg-rose-950/40 hover:text-rose-900 dark:hover:text-rose-200 transition-colors">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <i class="fa-solid fa-file-invoice text-amber-500 text-xs shrink-0"></i>
                                                <div class="min-w-0">
                                                    <div class="font-bold text-slate-900 dark:text-white truncate">
                                                        #{{ $inv->invoice_number }}
                                                    </div>
                                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 truncate">
                                                        {{ $inv->supplier?->name_en ?: $inv->supplier?->name_ar }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2 shrink-0">
                                                <span class="px-2 py-0.5 rounded-md bg-amber-50 dark:bg-amber-950/50 text-[10px] font-mono font-bold text-amber-700 dark:text-amber-300 border border-amber-200/60 dark:border-amber-800/60">
                                                    {{ number_format($inv->due_balance, 2) }} SAR Due
                                                </span>
                                                <i class="fa-solid fa-check text-rose-600 dark:text-rose-400 text-xs hidden bz-option-check"></i>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div id="bzEmpty_modalInvoiceSelect" class="hidden p-6 text-center text-xs text-slate-400 dark:text-slate-500">
                                    <i class="fa-solid fa-folder-open text-xl mb-1.5 block opacity-40"></i>
                                    <span>{{ app()->getLocale() === 'ar' ? 'لا توجد فواتير مطابقة' : 'No matching invoices found' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Category & Disbursement Date -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-800 dark:text-slate-200 block mb-1">
                            {{ app()->getLocale() === 'ar' ? 'تصنيف الصرف والنفقة (Category) *' : 'Disbursement Category *' }}
                        </label>
                        <div class="relative bz-dropdown-root" id="bzRoot_modalCategory">
                            <select name="category" id="modalCategory" onchange="onCategoryChange(this.value)" class="hidden" required>
                                @foreach($categories as $catKey => $catMeta)
                                    <option value="{{ $catKey }}" data-vat="{{ $catMeta['default_vat'] }}">
                                        {{ app()->getLocale() === 'ar' ? $catMeta['name_ar'] : $catMeta['name_en'] }} ({{ $catMeta['default_vat'] }}% VAT)
                                    </option>
                                @endforeach
                            </select>

                            <button type="button" onclick="BzDropdown.toggle('modalCategory')" id="bzTrigger_modalCategory"
                                    class="w-full flex items-center justify-between gap-2 px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold text-slate-800 dark:text-slate-100 hover:border-rose-400 dark:hover:border-rose-500 focus:outline-none transition shadow-2xs cursor-pointer select-none">
                                <div class="flex items-center gap-2.5 min-w-0 flex-1">
                                    <span class="w-6 h-6 rounded-lg bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0 text-xs">
                                        <i class="fa-solid fa-tags"></i>
                                    </span>
                                    <span id="bzLabel_modalCategory" class="truncate text-slate-800 dark:text-slate-100">
                                        @php
                                            $firstCatKey = array_key_first($categories);
                                            $firstCat = $categories[$firstCatKey] ?? null;
                                        @endphp
                                        {{ $firstCat ? (app()->getLocale() === 'ar' ? $firstCat['name_ar'] : $firstCat['name_en']) . ' (' . $firstCat['default_vat'] . '% VAT)' : '-- Select Category --' }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <i id="bzChevron_modalCategory" class="fa-solid fa-chevron-down text-slate-400 text-[10px] transition-transform duration-200"></i>
                                </div>
                            </button>

                            <!-- Dropdown Menu -->
                            <div id="bzMenu_modalCategory" class="hidden absolute left-0 right-0 top-full mt-1.5 z-50 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-150">
                                <div class="p-2 border-b border-slate-100 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-900/90">
                                    <div class="relative flex items-center">
                                        <i class="fa-solid fa-magnifying-glass absolute {{ app()->getLocale() === 'ar' ? 'right-3' : 'left-3' }} text-slate-400 text-xs pointer-events-none"></i>
                                        <input type="text" id="bzSearch_modalCategory" oninput="BzDropdown.filter('modalCategory')"
                                               placeholder="{{ app()->getLocale() === 'ar' ? 'بحث في التصنيفات...' : 'Search categories...' }}"
                                               class="w-full text-xs py-2 {{ app()->getLocale() === 'ar' ? 'pr-8 pl-8' : 'pl-8 pr-8' }} bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:border-rose-500 focus:ring-1 focus:ring-rose-500 transition">
                                        <span id="bzCount_modalCategory" class="absolute {{ app()->getLocale() === 'ar' ? 'left-2.5' : 'right-2.5' }} text-[10px] font-bold text-slate-400"></span>
                                    </div>
                                </div>
                                <div id="bzOptions_modalCategory" class="max-h-56 overflow-y-auto p-1.5 space-y-1 bz-select-scroll">
                                    @foreach($categories as $catKey => $catMeta)
                                        <div onclick="BzDropdown.select('modalCategory', '{{ $catKey }}', '{{ addslashes(app()->getLocale() === 'ar' ? $catMeta['name_ar'] : $catMeta['name_en']) }} ({{ $catMeta['default_vat'] }}% VAT)')"
                                             data-value="{{ $catKey }}"
                                             data-search="{{ strtolower($catKey . ' ' . $catMeta['name_en'] . ' ' . $catMeta['name_ar']) }}"
                                             class="bz-option-item flex items-center justify-between gap-2.5 px-3 py-2 rounded-xl text-xs cursor-pointer text-slate-700 dark:text-slate-200 hover:bg-rose-50/70 dark:hover:bg-rose-950/40 hover:text-rose-900 dark:hover:text-rose-200 transition-colors {{ $loop->first ? 'bg-rose-50 dark:bg-rose-950/40 font-bold' : '' }}">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <i class="fa-solid fa-folder text-rose-500 text-xs shrink-0"></i>
                                                <span class="font-bold text-slate-900 dark:text-white truncate">
                                                    {{ app()->getLocale() === 'ar' ? $catMeta['name_ar'] : $catMeta['name_en'] }}
                                                </span>
                                            </div>
                                            <div class="flex items-center gap-2 shrink-0">
                                                <span class="px-2 py-0.5 rounded-md bg-rose-100/70 dark:bg-rose-950/60 text-[10px] font-bold text-rose-700 dark:text-rose-300 border border-rose-200/60 dark:border-rose-800/60">
                                                    {{ $catMeta['default_vat'] }}% VAT
                                                </span>
                                                <i class="fa-solid fa-check text-rose-600 dark:text-rose-400 text-xs {{ $loop->first ? '' : 'hidden' }} bz-option-check"></i>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div id="bzEmpty_modalCategory" class="hidden p-6 text-center text-xs text-slate-400 dark:text-slate-500">
                                    <i class="fa-solid fa-folder-open text-xl mb-1.5 block opacity-40"></i>
                                    <span>{{ app()->getLocale() === 'ar' ? 'لا يوجد تصنيف مطابق' : 'No matching categories found' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-800 dark:text-slate-200 block mb-1">
                            {{ app()->getLocale() === 'ar' ? 'تاريخ الصرف والسند (Disbursement Date) *' : 'Disbursement Date *' }}
                        </label>
                        <input type="date" name="entry_date" value="{{ date('Y-m-d') }}" class="form-control text-xs font-bold text-slate-900 dark:text-white bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-700" required>
                    </div>
                </div>

                <!-- 4. Dynamic Two-Way VAT & Amount Calculator (High-Contrast Corporate Card) -->
                <div class="p-4 bg-slate-50 dark:bg-slate-800/80 rounded-xl border border-slate-200 dark:border-slate-700 space-y-3">
                    <div class="flex items-center justify-between text-xs font-bold text-slate-700 dark:text-slate-300 border-b border-slate-200 dark:border-slate-700 pb-2">
                        <span class="flex items-center gap-1.5 text-rose-700 dark:text-rose-400">
                            <i class="fa-solid fa-calculator"></i>
                            {{ app()->getLocale() === 'ar' ? 'حاسبة الضريبة والقيمة الإجمالية التلقائية' : 'Live Tax & Net/Gross Calculator' }}
                        </span>
                        <span class="text-[11px] text-slate-500 font-medium">
                            {{ app()->getLocale() === 'ar' ? 'يدعم الحساب بالصافي أو بالإجمالي' : 'Two-way Auto Calculation' }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="text-[11px] font-bold text-slate-700 dark:text-slate-300 block mb-1">
                                {{ app()->getLocale() === 'ar' ? 'المبلغ بدون الضريبة (SAR)' : 'Amount Excl. VAT (SAR)' }}
                            </label>
                            <input type="number" step="0.01" min="0" name="amount_excl_vat" id="modalAmountExcl" oninput="calculateFromNet()" placeholder="0.00" class="form-control text-xs font-bold font-mono text-slate-900 dark:text-white bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700">
                        </div>

                        <div>
                            <label class="text-[11px] font-bold text-slate-700 dark:text-slate-300 block mb-1">
                                {{ app()->getLocale() === 'ar' ? 'نسبة الضريبة (VAT Rate %)' : 'VAT Rate (%)' }}
                            </label>
                            <div class="flex items-center gap-1.5">
                                <input type="number" step="0.01" min="0" max="100" name="vat_rate" id="modalVatRate" value="15.00" oninput="recalculateVAT()" class="form-control text-xs font-bold font-mono text-slate-900 dark:text-white bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700">
                                <span class="text-xs font-bold text-slate-500">%</span>
                            </div>
                            <span id="displayVatAmount" class="block text-[11px] font-mono font-bold text-amber-600 dark:text-amber-400 mt-1">
                                VAT: 0.00 SAR
                            </span>
                        </div>

                        <div>
                            <label class="text-[11px] font-black text-rose-700 dark:text-rose-400 block mb-1">
                                {{ app()->getLocale() === 'ar' ? 'الإجمالي النهائي شامل الضريبة *' : 'Gross Total (SAR) *' }}
                            </label>
                            <input type="number" step="0.01" min="0.01" name="gross_amount" id="modalGrossAmount" oninput="calculateFromGross()" placeholder="0.00" class="form-control text-xs font-black font-mono text-rose-700 dark:text-rose-400 bg-rose-50/60 dark:bg-rose-950/30 border-rose-300 dark:border-rose-900" required>
                        </div>
                    </div>
                </div>

                <!-- 5. Payment Method & Financial Account Selector -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <!-- Payment Method Dropdown -->
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-800 dark:text-slate-200 block mb-1">
                            {{ app()->getLocale() === 'ar' ? 'طريقة السداد / الصرف *' : 'Disbursement Method *' }}
                        </label>
                        <div class="relative bz-dropdown-root" id="bzRoot_modalPaymentMethod">
                            <select name="payment_method" id="modalPaymentMethod" onchange="onPaymentMethodChange(this.value)" class="hidden" required>
                                <option value="bank_transfer" selected>{{ app()->getLocale() === 'ar' ? 'تحويل بنكي (Bank Transfer)' : 'Bank Transfer' }}</option>
                                <option value="cheque">{{ app()->getLocale() === 'ar' ? 'شيك بنكي (Cheque)' : 'Cheque' }}</option>
                                <option value="cash">{{ app()->getLocale() === 'ar' ? 'نقداً من الصندوق (Cash)' : 'Cash' }}</option>
                                <option value="corporate_card">{{ app()->getLocale() === 'ar' ? 'بطاقة ائتمان الشركة (Corporate Card)' : 'Corporate Card' }}</option>
                            </select>

                            <button type="button" onclick="BzDropdown.toggle('modalPaymentMethod')" id="bzTrigger_modalPaymentMethod"
                                    class="w-full flex items-center justify-between gap-2 px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold text-slate-800 dark:text-slate-100 hover:border-rose-400 dark:hover:border-rose-500 focus:outline-none transition shadow-2xs cursor-pointer select-none">
                                <div class="flex items-center gap-2.5 min-w-0 flex-1">
                                    <span class="w-6 h-6 rounded-lg bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0 text-xs">
                                        <i class="fa-solid fa-wallet"></i>
                                    </span>
                                    <span id="bzLabel_modalPaymentMethod" class="truncate text-slate-800 dark:text-slate-100">
                                        {{ app()->getLocale() === 'ar' ? 'تحويل بنكي (Bank Transfer)' : 'Bank Transfer' }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <i id="bzChevron_modalPaymentMethod" class="fa-solid fa-chevron-down text-slate-400 text-[10px] transition-transform duration-200"></i>
                                </div>
                            </button>

                            <!-- Dropdown Menu -->
                            <div id="bzMenu_modalPaymentMethod" class="hidden absolute left-0 right-0 top-full mt-1.5 z-50 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-150">
                                <div id="bzOptions_modalPaymentMethod" class="p-1.5 space-y-1">
                                    <div onclick="BzDropdown.select('modalPaymentMethod', 'bank_transfer', '{{ app()->getLocale() === 'ar' ? 'تحويل بنكي (Bank Transfer)' : 'Bank Transfer' }}')"
                                         data-value="bank_transfer"
                                         class="bz-option-item flex items-center justify-between gap-2.5 px-3 py-2.5 rounded-xl text-xs cursor-pointer text-slate-700 dark:text-slate-200 hover:bg-rose-50/70 dark:hover:bg-rose-950/40 hover:text-rose-900 dark:hover:text-rose-200 transition-colors bg-rose-50 dark:bg-rose-950/40 font-bold">
                                        <div class="flex items-center gap-2">
                                            <i class="fa-solid fa-building-columns text-rose-600 text-xs"></i>
                                            <span>{{ app()->getLocale() === 'ar' ? 'تحويل بنكي (Bank Transfer)' : 'Bank Transfer' }}</span>
                                        </div>
                                        <i class="fa-solid fa-check text-rose-600 dark:text-rose-400 text-xs bz-option-check"></i>
                                    </div>
                                    <div onclick="BzDropdown.select('modalPaymentMethod', 'cheque', '{{ app()->getLocale() === 'ar' ? 'شيك بنكي (Cheque)' : 'Cheque' }}')"
                                         data-value="cheque"
                                         class="bz-option-item flex items-center justify-between gap-2.5 px-3 py-2.5 rounded-xl text-xs cursor-pointer text-slate-700 dark:text-slate-200 hover:bg-rose-50/70 dark:hover:bg-rose-950/40 hover:text-rose-900 dark:hover:text-rose-200 transition-colors">
                                        <div class="flex items-center gap-2">
                                            <i class="fa-solid fa-money-check-dollar text-purple-600 text-xs"></i>
                                            <span>{{ app()->getLocale() === 'ar' ? 'شيك بنكي (Cheque)' : 'Cheque' }}</span>
                                        </div>
                                        <i class="fa-solid fa-check text-rose-600 dark:text-rose-400 text-xs hidden bz-option-check"></i>
                                    </div>
                                    <div onclick="BzDropdown.select('modalPaymentMethod', 'cash', '{{ app()->getLocale() === 'ar' ? 'نقداً من الصندوق (Cash)' : 'Cash' }}')"
                                         data-value="cash"
                                         class="bz-option-item flex items-center justify-between gap-2.5 px-3 py-2.5 rounded-xl text-xs cursor-pointer text-slate-700 dark:text-slate-200 hover:bg-rose-50/70 dark:hover:bg-rose-950/40 hover:text-rose-900 dark:hover:text-rose-200 transition-colors">
                                        <div class="flex items-center gap-2">
                                            <i class="fa-solid fa-money-bill-wave text-emerald-600 text-xs"></i>
                                            <span>{{ app()->getLocale() === 'ar' ? 'نقداً من الصندوق (Cash)' : 'Cash' }}</span>
                                        </div>
                                        <i class="fa-solid fa-check text-rose-600 dark:text-rose-400 text-xs hidden bz-option-check"></i>
                                    </div>
                                    <div onclick="BzDropdown.select('modalPaymentMethod', 'corporate_card', '{{ app()->getLocale() === 'ar' ? 'بطاقة ائتمان الشركة (Corporate Card)' : 'Corporate Card' }}')"
                                         data-value="corporate_card"
                                         class="bz-option-item flex items-center justify-between gap-2.5 px-3 py-2.5 rounded-xl text-xs cursor-pointer text-slate-700 dark:text-slate-200 hover:bg-rose-50/70 dark:hover:bg-rose-950/40 hover:text-rose-900 dark:hover:text-rose-200 transition-colors">
                                        <div class="flex items-center gap-2">
                                            <i class="fa-solid fa-credit-card text-blue-600 text-xs"></i>
                                            <span>{{ app()->getLocale() === 'ar' ? 'بطاقة ائتمان الشركة (Corporate Card)' : 'Corporate Card' }}</span>
                                        </div>
                                        <i class="fa-solid fa-check text-rose-600 dark:text-rose-400 text-xs hidden bz-option-check"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Financial Account Dropdown -->
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-800 dark:text-slate-200 block mb-1">
                            {{ app()->getLocale() === 'ar' ? 'الحساب / البنك المصروف منه *' : 'Financial Account (Bank / Cash) *' }}
                        </label>
                        <div class="relative bz-dropdown-root" id="bzRoot_modalFinancialAccount">
                            <select name="financial_account_id" id="modalFinancialAccount" class="hidden" required>
                                <option value="">-- {{ app()->getLocale() === 'ar' ? 'اختر حساب الصرف' : 'Select Bank / Cash Account' }} --</option>
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}">
                                        {{ $acc->name_en }} / {{ $acc->name_ar }} &bull; (Balance: {{ number_format($acc->current_balance, 2) }} SAR)
                                    </option>
                                @endforeach
                            </select>

                            <button type="button" onclick="BzDropdown.toggle('modalFinancialAccount')" id="bzTrigger_modalFinancialAccount"
                                    class="w-full flex items-center justify-between gap-2 px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold text-slate-800 dark:text-slate-100 hover:border-rose-400 dark:hover:border-rose-500 focus:outline-none transition shadow-2xs cursor-pointer select-none">
                                <div class="flex items-center gap-2.5 min-w-0 flex-1">
                                    <span class="w-6 h-6 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 text-xs">
                                        <i class="fa-solid fa-building-columns"></i>
                                    </span>
                                    <span id="bzLabel_modalFinancialAccount" class="truncate text-slate-800 dark:text-slate-100">
                                        -- {{ app()->getLocale() === 'ar' ? 'اختر حساب الصرف' : 'Select Bank / Cash Account' }} --
                                    </span>
                                </div>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <span id="bzClear_modalFinancialAccount" onclick="BzDropdown.clear(event, 'modalFinancialAccount', '{{ app()->getLocale() === 'ar' ? '-- اختر حساب الصرف --' : '-- Select Bank / Cash Account --' }}')" class="hidden w-5 h-5 rounded-md hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 flex items-center justify-center text-xs transition" title="Clear">
                                        <i class="fa-solid fa-xmark"></i>
                                    </span>
                                    <i id="bzChevron_modalFinancialAccount" class="fa-solid fa-chevron-down text-slate-400 text-[10px] transition-transform duration-200"></i>
                                </div>
                            </button>

                            <!-- Dropdown Menu -->
                            <div id="bzMenu_modalFinancialAccount" class="hidden absolute left-0 right-0 top-full mt-1.5 z-50 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-150">
                                <div class="p-2 border-b border-slate-100 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-900/90">
                                    <div class="relative flex items-center">
                                        <i class="fa-solid fa-magnifying-glass absolute {{ app()->getLocale() === 'ar' ? 'right-3' : 'left-3' }} text-slate-400 text-xs pointer-events-none"></i>
                                        <input type="text" id="bzSearch_modalFinancialAccount" oninput="BzDropdown.filter('modalFinancialAccount')"
                                               placeholder="{{ app()->getLocale() === 'ar' ? 'بحث باسم الحساب أو البنك...' : 'Search account or bank name...' }}"
                                               class="w-full text-xs py-2 {{ app()->getLocale() === 'ar' ? 'pr-8 pl-8' : 'pl-8 pr-8' }} bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:border-rose-500 focus:ring-1 focus:ring-rose-500 transition">
                                        <span id="bzCount_modalFinancialAccount" class="absolute {{ app()->getLocale() === 'ar' ? 'left-2.5' : 'right-2.5' }} text-[10px] font-bold text-slate-400"></span>
                                    </div>
                                </div>
                                <div id="bzOptions_modalFinancialAccount" class="max-h-56 overflow-y-auto p-1.5 space-y-1 bz-select-scroll">
                                    @foreach($accounts as $acc)
                                        <div onclick="BzDropdown.select('modalFinancialAccount', '{{ $acc->id }}', '{{ addslashes($acc->name_en) }} ({{ number_format($acc->current_balance, 2) }} SAR)')"
                                             data-value="{{ $acc->id }}"
                                             data-search="{{ strtolower($acc->name_en . ' ' . $acc->name_ar . ' ' . $acc->type) }}"
                                             class="bz-option-item flex items-center justify-between gap-2.5 px-3 py-2 rounded-xl text-xs cursor-pointer text-slate-700 dark:text-slate-200 hover:bg-rose-50/70 dark:hover:bg-rose-950/40 hover:text-rose-900 dark:hover:text-rose-200 transition-colors">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <i class="fa-solid fa-building-columns text-emerald-500 text-xs shrink-0"></i>
                                                <div class="min-w-0">
                                                    <div class="font-bold text-slate-900 dark:text-white truncate">
                                                        {{ $acc->name_en }} @if($acc->name_ar && $acc->name_ar !== $acc->name_en) <span class="text-slate-400 dark:text-slate-500 font-normal">/ {{ $acc->name_ar }}</span> @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2 shrink-0">
                                                <span class="px-2 py-0.5 rounded-md bg-emerald-50 dark:bg-emerald-950/50 text-[10px] font-mono font-bold text-emerald-700 dark:text-emerald-300 border border-emerald-200/60 dark:border-emerald-800/60">
                                                    {{ number_format($acc->current_balance, 2) }} SAR
                                                </span>
                                                <i class="fa-solid fa-check text-rose-600 dark:text-rose-400 text-xs hidden bz-option-check"></i>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div id="bzEmpty_modalFinancialAccount" class="hidden p-6 text-center text-xs text-slate-400 dark:text-slate-500">
                                    <i class="fa-solid fa-folder-open text-xl mb-1.5 block opacity-40"></i>
                                    <span>{{ app()->getLocale() === 'ar' ? 'لا توجد حسابات مطابقة' : 'No matching accounts found' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 6. Conditional Cheque Details Container (Visible only when Cheque selected) -->
                <div id="chequeFieldsContainer" class="hidden p-3.5 bg-purple-50/70 dark:bg-purple-950/30 rounded-xl border border-purple-200 dark:border-purple-800/80 space-y-3">
                    <div class="flex items-center gap-2 text-xs font-bold text-purple-800 dark:text-purple-300">
                        <i class="fa-solid fa-money-check-dollar"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'بيانات الشيك البنكي وحالة المقاصة' : 'Bank Cheque Details & Clearance Flow' }}</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="text-[11px] font-bold text-slate-700 dark:text-slate-300 block mb-1">{{ app()->getLocale() === 'ar' ? 'رقم الشيك *' : 'Cheque Number *' }}</label>
                            <input type="text" name="cheque_number" id="modalChequeNo" placeholder="e.g. 0049201" class="form-control text-xs font-mono font-bold text-slate-900 dark:text-white bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700">
                        </div>
                        <div>
                            <label class="text-[11px] font-bold text-slate-700 dark:text-slate-300 block mb-1">{{ app()->getLocale() === 'ar' ? 'البنك المسحوب عليه' : 'Drawn Bank' }}</label>
                            <input type="text" name="cheque_bank" id="modalChequeBank" placeholder="e.g. Al Rajhi / الأهلي" class="form-control text-xs font-medium text-slate-900 dark:text-white bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700">
                        </div>
                        <div>
                            <label class="text-[11px] font-bold text-slate-700 dark:text-slate-300 block mb-1">{{ app()->getLocale() === 'ar' ? 'تاريخ استحقاق الشيك' : 'Cheque Due Date' }}</label>
                            <input type="date" name="cheque_due_date" id="modalChequeDueDate" class="form-control text-xs font-medium text-slate-900 dark:text-white bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700">
                        </div>
                    </div>
                    <div class="flex items-center gap-2 text-[11px] text-purple-700 dark:text-purple-300 font-medium pt-1">
                        <input type="checkbox" name="status" value="posted" id="chkDirectClear" class="rounded text-purple-600 focus:ring-0">
                        <label for="chkDirectClear" class="cursor-pointer">
                            {{ app()->getLocale() === 'ar' ? 'صرف الشيك وخصم الرصيد فوراً (بدون المرور بحالة شيك تحت التحصيل)' : 'Clear and deduct balance immediately (skip pending clearance status)' }}
                        </label>
                    </div>
                </div>

                <!-- 7. Description & Reference Notes -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-800 dark:text-slate-200 block mb-1">
                        {{ app()->getLocale() === 'ar' ? 'البيان وتفاصيل الصرف (Description / Purpose)' : 'Description / Disbursement Purpose' }}
                    </label>
                    <textarea name="description" id="modalDescription" rows="2" placeholder="{{ app()->getLocale() === 'ar' ? 'شرح سبب الصرف، رقم الإرسالية، بيانات بوليصة الشحن...' : 'Purpose of payment, waybill number, purchase details...' }}" class="form-control text-xs font-medium text-slate-900 dark:text-white bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-700"></textarea>
                </div>

                <!-- Modal Footer -->
                <div class="pt-3 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end gap-2">
                    <button type="button" onclick="closePaymentModal()" class="btn btn-secondary text-xs font-bold py-2 px-4 text-slate-700 dark:text-slate-300">
                        {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                    </button>
                    <button type="submit" class="btn text-xs font-black py-2.5 px-6 text-white shadow-md bg-rose-700 hover:bg-rose-800 border-rose-700 cursor-pointer">
                        <i class="fa-solid fa-check mr-1 ml-1"></i>
                        {{ app()->getLocale() === 'ar' ? 'تسجيل وترحيل سند الصرف' : 'Post Payment Entry' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Custom Slim Scrollbar for Dropdown Overflow -->
    <style>
        .bz-select-scroll::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }
        .bz-select-scroll::-webkit-scrollbar-track {
            background: transparent;
        }
        .bz-select-scroll::-webkit-scrollbar-thumb {
            background-color: rgba(148, 163, 184, 0.4);
            border-radius: 9999px;
        }
        .bz-select-scroll::-webkit-scrollbar-thumb:hover {
            background-color: rgba(100, 116, 139, 0.7);
        }
        .dark .bz-select-scroll::-webkit-scrollbar-thumb {
            background-color: rgba(51, 65, 85, 0.6);
        }
        .dark .bz-select-scroll::-webkit-scrollbar-thumb:hover {
            background-color: rgba(71, 85, 105, 0.9);
        }
    </style>

    <!-- Interactive Client Engine for Payment Ledger -->
    <script>
        // Universal High-End Searchable Dropdown Engine
        window.BzDropdown = {
            openId: null,

            toggle(id) {
                if (this.openId === id) {
                    this.close();
                } else {
                    this.open(id);
                }
            },

            open(id) {
                if (this.openId && this.openId !== id) {
                    this.close();
                }
                const menu = document.getElementById('bzMenu_' + id);
                const trigger = document.getElementById('bzTrigger_' + id);
                const chevron = document.getElementById('bzChevron_' + id);
                if (!menu || !trigger) return;

                // Smart viewport positioning
                const rect = trigger.getBoundingClientRect();
                const spaceBelow = window.innerHeight - rect.bottom;
                if (spaceBelow < 280 && rect.top > 280) {
                    menu.classList.remove('top-full', 'mt-1.5');
                    menu.classList.add('bottom-full', 'mb-1.5');
                } else {
                    menu.classList.remove('bottom-full', 'mb-1.5');
                    menu.classList.add('top-full', 'mt-1.5');
                }

                menu.classList.remove('hidden');
                if (chevron) chevron.classList.add('rotate-180');
                trigger.classList.add('ring-2', 'ring-rose-500/20', 'border-rose-500');
                this.openId = id;

                // Reset search query & focus
                const search = document.getElementById('bzSearch_' + id);
                if (search) {
                    search.value = '';
                    this.filter(id);
                    setTimeout(() => search.focus(), 50);
                }
            },

            close() {
                if (!this.openId) return;
                const menu = document.getElementById('bzMenu_' + this.openId);
                const trigger = document.getElementById('bzTrigger_' + this.openId);
                const chevron = document.getElementById('bzChevron_' + this.openId);
                if (menu) menu.classList.add('hidden');
                if (chevron) chevron.classList.remove('rotate-180');
                if (trigger) trigger.classList.remove('ring-2', 'ring-rose-500/20', 'border-rose-500');
                this.openId = null;
            },

            select(id, value, label) {
                const select = document.getElementById(id);
                if (select) {
                    select.value = value;
                    select.dispatchEvent(new Event('change', { bubbles: true }));
                }
                this.updateTrigger(id, label, Boolean(value));
                this.markActiveOption(id, value);
                this.close();
            },

            clear(e, id, placeholder) {
                if (e) e.stopPropagation();
                const select = document.getElementById(id);
                if (select) {
                    select.value = '';
                    select.dispatchEvent(new Event('change', { bubbles: true }));
                }
                this.updateTrigger(id, placeholder, false);
                this.markActiveOption(id, '');
                this.close();
            },

            updateTrigger(id, text, hasValue) {
                const labelEl = document.getElementById('bzLabel_' + id);
                const clearBtn = document.getElementById('bzClear_' + id);
                if (labelEl) labelEl.textContent = text;
                if (clearBtn) {
                    if (hasValue) clearBtn.classList.remove('hidden');
                    else clearBtn.classList.add('hidden');
                }
            },

            markActiveOption(id, value) {
                const menu = document.getElementById('bzMenu_' + id);
                if (!menu) return;
                const items = menu.querySelectorAll('.bz-option-item');
                items.forEach(item => {
                    const val = item.getAttribute('data-value');
                    const check = item.querySelector('.bz-option-check');
                    if (val === String(value)) {
                        item.classList.add('bg-rose-50', 'dark:bg-rose-950/40', 'font-bold');
                        if (check) check.classList.remove('hidden');
                    } else {
                        item.classList.remove('bg-rose-50', 'dark:bg-rose-950/40', 'font-bold');
                        if (check) check.classList.add('hidden');
                    }
                });
            },

            filter(id) {
                const searchInput = document.getElementById('bzSearch_' + id);
                const menu = document.getElementById('bzMenu_' + id);
                const emptyEl = document.getElementById('bzEmpty_' + id);
                const countEl = document.getElementById('bzCount_' + id);
                if (!searchInput || !menu) return;

                const q = searchInput.value.trim().toLowerCase();
                const items = menu.querySelectorAll('.bz-option-item');
                let visibleCount = 0;

                items.forEach(item => {
                    const text = (item.getAttribute('data-search') || item.innerText).toLowerCase();
                    if (!q || text.includes(q)) {
                        item.classList.remove('hidden');
                        visibleCount++;
                    } else {
                        item.classList.add('hidden');
                    }
                });

                if (emptyEl) {
                    if (visibleCount === 0) emptyEl.classList.remove('hidden');
                    else emptyEl.classList.add('hidden');
                }
                if (countEl) {
                    countEl.textContent = q ? visibleCount + ' {{ app()->getLocale() === "ar" ? "نتيجة" : "found" }}' : '';
                }
            }
        };

        // Global dismiss listeners
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.bz-dropdown-root')) {
                window.BzDropdown.close();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                window.BzDropdown.close();
            }
        });

        // Modal Control
        function openPaymentModal() {
            document.getElementById('newPaymentModal').classList.remove('hidden');
        }
        function closePaymentModal() {
            document.getElementById('newPaymentModal').classList.add('hidden');
        }

        // Payee Type Switcher (Supplier vs General Payee)
        function switchPayeeType(type) {
            document.getElementById('modalPayeeType').value = type;
            const btnSupplier = document.getElementById('btnPayeeSupplier');
            const btnGeneral = document.getElementById('btnPayeeGeneral');
            const suppContainer = document.getElementById('supplierSelectorContainer');
            const genContainer = document.getElementById('generalPayeeContainer');

            if (type === 'supplier') {
                btnSupplier.className = 'py-2 px-3 text-xs font-black rounded-lg transition flex items-center justify-center gap-1.5 bg-white dark:bg-slate-700 text-rose-700 dark:text-rose-300 shadow-sm border border-slate-200 dark:border-slate-600';
                btnGeneral.className = 'py-2 px-3 text-xs font-bold rounded-lg transition flex items-center justify-center gap-1.5 text-slate-600 dark:text-slate-300 hover:bg-white/60 dark:hover:bg-slate-700/60';
                suppContainer.classList.remove('hidden');
                genContainer.classList.add('hidden');
            } else {
                btnGeneral.className = 'py-2 px-3 text-xs font-black rounded-lg transition flex items-center justify-center gap-1.5 bg-white dark:bg-slate-700 text-rose-700 dark:text-rose-300 shadow-sm border border-slate-200 dark:border-slate-600';
                btnSupplier.className = 'py-2 px-3 text-xs font-bold rounded-lg transition flex items-center justify-center gap-1.5 text-slate-600 dark:text-slate-300 hover:bg-white/60 dark:hover:bg-slate-700/60';
                suppContainer.classList.add('hidden');
                genContainer.classList.remove('hidden');
            }
        }

        // On Supplier Change
        function onSupplierChange(select) {
            const opt = select.options[select.selectedIndex];
            const suppId = select.value;
            if (opt) {
                window.BzDropdown.updateTrigger('modalSupplierSelect', opt.text, Boolean(suppId));
                window.BzDropdown.markActiveOption('modalSupplierSelect', suppId);
            }

            // Filter invoice options to match this supplier in both native & custom dropdown
            const invContainer = document.getElementById('bzOptions_modalInvoiceSelect');
            if (invContainer) {
                const items = invContainer.querySelectorAll('.bz-option-item');
                items.forEach(item => {
                    const itemSuppId = item.getAttribute('data-supplier-id');
                    if (!itemSuppId || !suppId || itemSuppId === suppId) {
                        item.classList.remove('hidden');
                    } else {
                        item.classList.add('hidden');
                    }
                });
            }
        }

        // On Invoice Change (Auto-populate due balance)
        function onInvoiceChange(select) {
            const opt = select.options[select.selectedIndex];
            if (opt && opt.value) {
                const due = opt.getAttribute('data-due');
                const num = opt.getAttribute('data-num');
                const suppId = opt.getAttribute('data-supplier-id');

                if (suppId) {
                    const suppSelect = document.getElementById('modalSupplierSelect');
                    suppSelect.value = suppId;
                    onSupplierChange(suppSelect);
                }

                if (due) {
                    document.getElementById('modalGrossAmount').value = parseFloat(due).toFixed(2);
                    calculateFromGross();
                }

                const desc = document.getElementById('modalDescription');
                if (!desc.value) {
                    desc.value = 'Settlement for Bill #' + num;
                }
            }
        }

        // Category Auto VAT rate update
        function onCategoryChange(cat) {
            const select = document.getElementById('modalCategory');
            const opt = select.options[select.selectedIndex];
            const defaultVat = opt.getAttribute('data-vat') || '15.00';
            document.getElementById('modalVatRate').value = parseFloat(defaultVat).toFixed(2);
            recalculateVAT();
        }

        // Two-Way VAT Calculations
        function calculateFromNet() {
            const excl = parseFloat(document.getElementById('modalAmountExcl').value) || 0;
            const vatRate = parseFloat(document.getElementById('modalVatRate').value) || 0;
            const vatAmount = Math.round((excl * (vatRate / 100)) * 100) / 100;
            const gross = Math.round((excl + vatAmount) * 100) / 100;

            document.getElementById('displayVatAmount').innerText = 'VAT: ' + vatAmount.toFixed(2) + ' SAR';
            document.getElementById('modalGrossAmount').value = gross > 0 ? gross.toFixed(2) : '';
        }

        function calculateFromGross() {
            const gross = parseFloat(document.getElementById('modalGrossAmount').value) || 0;
            const vatRate = parseFloat(document.getElementById('modalVatRate').value) || 0;

            let excl = gross;
            let vatAmount = 0;
            if (vatRate > 0) {
                excl = Math.round((gross / (1 + (vatRate / 100))) * 100) / 100;
                vatAmount = Math.round((gross - excl) * 100) / 100;
            }

            document.getElementById('modalAmountExcl').value = excl > 0 ? excl.toFixed(2) : '';
            document.getElementById('displayVatAmount').innerText = 'VAT: ' + vatAmount.toFixed(2) + ' SAR';
        }

        function recalculateVAT() {
            if (document.getElementById('modalAmountExcl').value) {
                calculateFromNet();
            } else if (document.getElementById('modalGrossAmount').value) {
                calculateFromGross();
            }
        }

        // Payment Method toggles (Cheque fields)
        function onPaymentMethodChange(method) {
            const container = document.getElementById('chequeFieldsContainer');
            const chqInput = document.getElementById('modalChequeNo');
            if (method === 'cheque') {
                container.classList.remove('hidden');
                chqInput.setAttribute('required', 'required');
            } else {
                container.classList.add('hidden');
                chqInput.removeAttribute('required');
            }
        }

        // Interactive Sorting Engine
        function triggerSort(field) {
            const currentField = "{{ $sortBy }}";
            const currentDir = "{{ $sortDir }}";
            let newDir = 'asc';
            if (currentField === field && currentDir === 'asc') {
                newDir = 'desc';
            }

            const url = new URL(window.location.href);
            url.searchParams.set('sort_by', field);
            url.searchParams.set('sort_dir', newDir);
            window.location.href = url.toString();
        }

        // Per Page Batch Switcher
        function changePerPage(val) {
            const url = new URL(window.location.href);
            url.searchParams.set('per_page', val);
            window.location.href = url.toString();
        }

        // Lazy Loading & Pagination Hub
        let nextPage = {{ $payments->currentPage() + 1 }};
        let hasMore = {{ $payments->hasMorePages() ? 'true' : 'false' }};
        let isLoading = false;
        let isAutoScrollActive = false;

        function toggleAutoScroll(enabled) {
            isAutoScrollActive = enabled;
            if (enabled && hasMore) {
                lazyLoadMore();
            }
        }

        function lazyLoadMore() {
            if (!hasMore || isLoading) return;
            isLoading = true;

            const btnSeeMore = document.getElementById('btnSeeMore');
            const lbl = document.getElementById('btnSeeMoreLabel');
            if (lbl) lbl.innerText = '{{ app()->getLocale() === "ar" ? "جاري التحميل..." : "Loading..." }}';

            const url = new URL(window.location.href);
            url.searchParams.set('page', nextPage);

            fetch(url.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                const tbody = document.getElementById('paymentTableBody');
                tbody.insertAdjacentHTML('beforeend', data.html);

                nextPage = data.next_page;
                hasMore = data.has_more;
                isLoading = false;

                updateProgressUI(data);

                if (!hasMore) {
                    if (btnSeeMore) btnSeeMore.classList.add('hidden');
                    document.getElementById('allLoadedNotice').classList.remove('hidden');
                    document.getElementById('btnSeeLess').classList.remove('hidden');
                } else {
                    if (lbl) lbl.innerText = '{{ app()->getLocale() === "ar" ? "عرض المزيد (+ " . $perPage . " سجل)" : "See More (+" . $perPage . " Records)" }}';
                }
            })
            .catch(err => {
                console.error(err);
                isLoading = false;
                if (lbl) lbl.innerText = '{{ app()->getLocale() === "ar" ? "إعادة المحاولة" : "Try Again" }}';
            });
        }

        function lazyShowLess() {
            const rows = document.querySelectorAll('.payment-row');
            const perPage = {{ $perPage }};
            rows.forEach((r, idx) => {
                if (idx >= perPage) {
                    r.remove();
                }
            });

            nextPage = 2;
            hasMore = true;
            document.getElementById('btnSeeLess').classList.add('hidden');
            document.getElementById('btnSeeMore').classList.remove('hidden');
            document.getElementById('allLoadedNotice').classList.add('hidden');
            updateDynamicTotals();
        }

        function updateProgressUI(data) {
            const loaded = document.querySelectorAll('.payment-row').length;
            const total = {{ $payments->total() }};

            const dispShowing = document.getElementById('displayShowingCount');
            const lblShowing = document.getElementById('lblShowingCount');
            const lblTotal = document.getElementById('lblTotalCount');
            const lblPercent = document.getElementById('lblPercentage');
            const progress = document.getElementById('hubProgressBar');

            if (dispShowing) dispShowing.innerText = loaded;
            if (lblShowing) lblShowing.innerText = loaded;
            if (lblTotal) lblTotal.innerText = total;

            const pct = total > 0 ? Math.min(100, Math.round((loaded / total) * 100)) : 100;
            if (lblPercent) lblPercent.innerText = pct + '%';
            if (progress) progress.style.width = pct + '%';

            updateDynamicTotals();
        }

        function updateDynamicTotals() {
            const rows = document.querySelectorAll('.payment-row');
            let exclSum = 0;
            let vatSum = 0;
            let grossSum = 0;

            rows.forEach(r => {
                exclSum += parseFloat(r.getAttribute('data-excl') || 0);
                vatSum += parseFloat(r.getAttribute('data-vat') || 0);
                grossSum += parseFloat(r.getAttribute('data-gross') || 0);
            });

            document.getElementById('footerRowCount').innerText = rows.length;
            document.getElementById('footerExclTotal').innerText = exclSum.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.getElementById('footerVatTotal').innerText = vatSum.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.getElementById('footerGrossTotal').innerText = grossSum.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }

        // Intersection Observer for Auto-load Sentinel
        document.addEventListener('DOMContentLoaded', () => {
            const sentinel = document.getElementById('lazySentinel');
            if (sentinel && 'IntersectionObserver' in window) {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting && isAutoScrollActive && hasMore && !isLoading) {
                            lazyLoadMore();
                        }
                    });
                }, { rootMargin: '200px' });
                observer.observe(sentinel);
            }
        });
    </script>
</x-layouts.admin>
