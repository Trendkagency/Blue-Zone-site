<x-layouts.admin :title="app()->getLocale() === 'ar' ? 'سندات وقيود التحصيل - Collection Entry' : 'Collection Entry - Finance'">
    <div class="space-y-6" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

        <!-- Top Breadcrumbs & Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200 dark:border-slate-800">
            <div>
                <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 mb-1">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-primary transition">{{ __('admin.menu.dashboard') }}</a>
                    <span>/</span>
                    <a href="{{ route('admin.finance.dashboard') }}" class="hover:text-primary transition">{{ app()->getLocale() === 'ar' ? 'المالية' : 'Finance' }}</a>
                    <span>/</span>
                    <span class="text-slate-900 dark:text-slate-100 font-bold">{{ app()->getLocale() === 'ar' ? 'سندات وقيود التحصيل' : 'Collection Entry' }}</span>
                </div>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-2.5">
                    <span class="w-3 h-8 bg-[#0A4F78] rounded-sm"></span>
                    {{ app()->getLocale() === 'ar' ? 'سندات وقيود التحصيل المالي (Collection Entry)' : 'Collection Entry & Revenue Ingestion' }}
                </h1>
                <p class="text-xs font-medium text-slate-600 dark:text-slate-300 mt-0.5">
                    {{ app()->getLocale() === 'ar' ? 'إدارة قيود التحصيل من العملاء والموردين والأصول، والتحصيل الآلي للفواتير والمزامنة مع المتجر الإلكتروني.' : 'Manage collection entries from system customers, suppliers, and assets with automated invoice collection and online sales sync.' }}
                </p>
            </div>

            <!-- Top Action Buttons -->
            <div class="flex items-center gap-2 flex-wrap">
                <form action="{{ route('admin.finance.collections.sync-online') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="btn btn-secondary text-xs font-bold py-2 px-3 shadow-sm flex items-center gap-1.5 border border-sky-300 dark:border-sky-800 text-sky-800 dark:text-sky-300 hover:bg-sky-50 dark:hover:bg-sky-950/40" title="Sync Online Store Orders">
                        <i class="fa-solid fa-arrows-rotate text-sky-600 dark:text-sky-400"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'مزامنة مبيعات الأونلاين الآلية' : 'Sync Online Sales (Auto)' }}</span>
                    </button>
                </form>

                <a href="{{ route('admin.finance.collections.export', request()->query()) }}" class="btn btn-secondary text-xs font-bold py-2 px-3 shadow-sm flex items-center gap-1.5 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200">
                    <i class="fa-solid fa-file-excel text-emerald-600 dark:text-emerald-400"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'تصدير إكسل' : 'Export Excel' }}</span>
                </a>

                <button type="button" onclick="openCollectionModal()" class="btn text-xs font-bold py-2 px-3.5 shadow-md flex items-center gap-1.5 text-white" style="background: #0A4F78; border-color: #0A4F78;">
                    <i class="fa-solid fa-plus"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'تسجيل قيد تحصيل جديد' : '+ New Collection Entry' }}</span>
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
            <!-- 1. Total Gross Collected (Corporate Highlight) -->
            <div class="card p-3.5 border border-sky-200 dark:border-sky-800 bg-gradient-to-br from-sky-50/70 to-white dark:from-slate-900 dark:to-slate-900 shadow-sm relative overflow-hidden">
                <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-[#0A4F78] to-[#2A8FC2]"></div>
                <div class="flex items-center justify-between text-xs text-slate-700 dark:text-slate-300 mb-1 font-bold">
                    <span>{{ app()->getLocale() === 'ar' ? 'إجمالي المحصل شامل الضريبة' : 'Total Gross Collected' }}</span>
                    <span class="w-7 h-7 rounded-lg bg-sky-100 dark:bg-sky-900/50 flex items-center justify-center text-[#0A4F78] dark:text-sky-300">
                        <i class="fa-solid fa-coins text-xs"></i>
                    </span>
                </div>
                <div class="text-xl sm:text-2xl font-black tabular-nums tracking-tight text-[#0A4F78] dark:text-white">
                    {{ number_format($totalGross, 2) }} <span class="text-xs font-bold text-slate-500 dark:text-slate-400">SAR</span>
                </div>
                <div class="text-[11px] font-bold text-sky-700 dark:text-sky-400 mt-1 flex items-center gap-1">
                    <i class="fa-solid fa-chart-line"></i> {{ $totalEntries }} {{ app()->getLocale() === 'ar' ? 'حركة مسجلة' : 'Entries' }}
                </div>
            </div>

            <!-- 2. Amount Excl. VAT -->
            <div class="card p-3.5 border border-slate-300 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm relative overflow-hidden">
                <div class="absolute top-0 inset-x-0 h-1 bg-emerald-500"></div>
                <div class="flex items-center justify-between text-xs text-slate-700 dark:text-slate-300 mb-1 font-bold">
                    <span>{{ app()->getLocale() === 'ar' ? 'صافي المبالغ بدون الضريبة' : 'Amount Excl. VAT' }}</span>
                    <span class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                        <i class="fa-solid fa-file-invoice text-xs"></i>
                    </span>
                </div>
                <div class="text-xl sm:text-2xl font-black tabular-nums text-slate-900 dark:text-white">
                    {{ number_format($totalNet, 2) }} <span class="text-xs font-bold text-slate-500 dark:text-slate-400">SAR</span>
                </div>
                <div class="text-[11px] font-bold text-emerald-700 dark:text-emerald-400 mt-1 flex items-center gap-1">
                    <i class="fa-solid fa-check"></i> {{ app()->getLocale() === 'ar' ? 'الإيرادات الصافية المقبوضة' : 'Net Recognized Revenue' }}
                </div>
            </div>

            <!-- 3. VAT Amount -->
            <div class="card p-3.5 border border-slate-300 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm relative overflow-hidden">
                <div class="absolute top-0 inset-x-0 h-1 bg-amber-500"></div>
                <div class="flex items-center justify-between text-xs text-slate-700 dark:text-slate-300 mb-1 font-bold">
                    <span>{{ app()->getLocale() === 'ar' ? 'إجمالي ضريبة القيمة المضافة' : 'VAT Amount (15%)' }}</span>
                    <span class="w-7 h-7 rounded-lg bg-amber-50 dark:bg-amber-950/40 flex items-center justify-center text-amber-600 dark:text-amber-400">
                        <i class="fa-solid fa-receipt text-xs"></i>
                    </span>
                </div>
                <div class="text-xl sm:text-2xl font-black tabular-nums text-slate-900 dark:text-white">
                    {{ number_format($totalVat, 2) }} <span class="text-xs font-bold text-slate-500 dark:text-slate-400">SAR</span>
                </div>
                <div class="text-[11px] font-bold text-amber-700 dark:text-amber-400 mt-1 flex items-center gap-1">
                    <i class="fa-solid fa-scale-balanced"></i> {{ app()->getLocale() === 'ar' ? 'مستحقات هيئة الزكاة والضريبة' : 'ZATCA VAT Component' }}
                </div>
            </div>

            <!-- 4. Auto Synced Online Sales -->
            <div class="card p-3.5 border border-slate-300 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm relative overflow-hidden">
                <div class="absolute top-0 inset-x-0 h-1 bg-cyan-500"></div>
                <div class="flex items-center justify-between text-xs text-slate-700 dark:text-slate-300 mb-1 font-bold">
                    <span>{{ app()->getLocale() === 'ar' ? 'تحصيلات الأونلاين الآلية' : 'Auto Synced Online Sales' }}</span>
                    <span class="w-7 h-7 rounded-lg bg-cyan-50 dark:bg-cyan-950/40 flex items-center justify-center text-cyan-600 dark:text-cyan-400">
                        <i class="fa-solid fa-cart-shopping text-xs"></i>
                    </span>
                </div>
                <div class="text-xl sm:text-2xl font-black tabular-nums text-sky-700 dark:text-sky-300">
                    {{ $totalAutoSynced }} <span class="text-xs font-bold text-slate-500 dark:text-slate-400">{{ app()->getLocale() === 'ar' ? 'طلب' : 'Orders' }}</span>
                </div>
                <div class="text-[11px] font-bold text-cyan-700 dark:text-cyan-400 mt-1 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-500 animate-pulse"></span>
                    {{ app()->getLocale() === 'ar' ? 'مزامنة فورية مع السلة' : 'Realtime Gateway Ingestion' }}
                </div>
            </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="card p-3.5 border border-slate-300 dark:border-slate-800 bg-slate-50 dark:bg-slate-900 shadow-sm">
            <form method="GET" action="{{ route('admin.finance.collections.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3 items-end">
                <input type="hidden" name="sort_by" id="filterSortBy" value="{{ $sortBy }}">
                <input type="hidden" name="sort_dir" id="filterSortDir" value="{{ $sortDir }}">
                <input type="hidden" name="per_page" id="filterPerPage" value="{{ $perPage }}">
                
                <div>
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300 block mb-1">{{ app()->getLocale() === 'ar' ? 'البحث بالرقم أو البيان أو العميل' : 'Search' }}</label>
                    <input type="text" name="search" value="{{ $search }}" placeholder="{{ app()->getLocale() === 'ar' ? 'رقم القيد، البيان، كود العميل...' : 'No., description, customer...' }}" class="form-control text-xs font-medium text-slate-900 dark:text-white bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-700">
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
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300 block mb-1">{{ app()->getLocale() === 'ar' ? 'الحساب / البنك' : 'Account / Bank' }}</label>
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
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300 block mb-1">{{ app()->getLocale() === 'ar' ? 'الفترة من / إلى' : 'Date Range' }}</label>
                    <div class="flex items-center gap-1.5">
                        <input type="date" name="date_from" value="{{ $dateFrom }}" class="form-control text-xs font-medium text-slate-900 dark:text-white bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-700 px-1.5">
                        <input type="date" name="date_to" value="{{ $dateTo }}" class="form-control text-xs font-medium text-slate-900 dark:text-white bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-700 px-1.5">
                    </div>
                </div>

                <div class="flex items-center gap-1.5">
                    <button type="submit" class="btn text-xs font-bold py-2 px-3 flex-1 text-white shadow-sm" style="background: #0A4F78; border-color: #0A4F78;">
                        <i class="fa-solid fa-filter mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'تصفية' : 'Filter' }}
                    </button>
                    @if($search || $category || $accountId || $dateFrom || $dateTo)
                        <a href="{{ route('admin.finance.collections.index') }}" class="btn btn-secondary text-xs font-bold py-2 px-2.5 text-slate-700 dark:text-slate-200" title="Reset Filters">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Creative Corporate Ledger Table (Executive SaaS Edition) -->
        <div id="collectionLedgerCard" class="card border border-slate-200 dark:border-slate-800 shadow-sm rounded-2xl overflow-hidden bg-white dark:bg-slate-900 transition-all duration-200">
            <!-- Executive Header Ribbon: High-Contrast Light & Dark Mode -->
            <div class="px-5 py-4 bg-slate-50/95 dark:bg-[#062B49] border-b border-slate-200 dark:border-slate-700/80 flex flex-col md:flex-row md:items-center justify-between gap-3.5 transition-colors">
                <div class="flex items-center gap-3 flex-wrap">
                    <span class="w-10 h-10 rounded-xl bg-sky-100 dark:bg-sky-950/70 text-[#0A4F78] dark:text-sky-300 border border-sky-200/80 dark:border-sky-800 flex items-center justify-center shadow-xs shrink-0">
                        <i class="fa-solid fa-table-list text-base"></i>
                    </span>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h2 class="text-sm sm:text-base font-black tracking-wide uppercase m-0 text-slate-900 dark:text-white">
                                {{ app()->getLocale() === 'ar' ? 'دفتر قيود وسندات التحصيل المالي' : 'Collection Entry Ledger' }}
                            </h2>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-sky-100/90 dark:bg-sky-950/60 text-[#0A4F78] dark:text-sky-300 border border-sky-200 dark:border-sky-800 shadow-2xs">
                                <span>{{ app()->getLocale() === 'ar' ? 'معروض' : 'Showing' }}</span>
                                <span id="displayShowingCount" class="font-black text-slate-900 dark:text-white">{{ $collections->count() }}</span>
                                <span class="text-slate-400 dark:text-slate-500">/</span>
                                <span id="displayTotalCount" class="font-black text-slate-900 dark:text-white">{{ $collections->total() }}</span>
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 m-0 mt-0.5 hidden sm:flex items-center gap-1.5 font-medium">
                            <span>Blue Zone Pharmaceuticals ERP</span>
                            <span class="text-slate-300 dark:text-slate-600">&bull;</span>
                            <span>Financial Ledger System</span>
                            <span class="text-slate-300 dark:text-slate-600">&bull;</span>
                            <span class="inline-flex items-center gap-1 text-emerald-600 dark:text-emerald-400 font-bold">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                Live Audit Trail
                            </span>
                        </p>
                    </div>
                </div>

                <!-- Ledger Quick Toolbar: Sort Pill & Batch Selector & Auto-Load Toggle -->
                <div class="flex items-center gap-2.5 flex-wrap">
                    <!-- Current Sort Indicator Pill -->
                    <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white dark:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 shadow-2xs">
                        <i class="fa-solid {{ $sortDir === 'asc' ? 'fa-arrow-up-short-wide' : 'fa-arrow-down-wide-short' }} text-sky-600 dark:text-sky-400"></i>
                        <span class="text-slate-500 dark:text-slate-400 font-medium">{{ app()->getLocale() === 'ar' ? 'الترتيب:' : 'Sorted by:' }}</span>
                        <span class="font-extrabold text-slate-900 dark:text-white capitalize">{{ str_replace('_', ' ', $sortBy) }}</span>
                        <span class="text-[10px] uppercase font-bold text-sky-600 dark:text-sky-400">({{ $sortDir }})</span>
                    </div>

                    <!-- Auto-load on Scroll Toggle -->
                    <label class="inline-flex items-center gap-2 text-xs font-bold text-slate-700 dark:text-slate-200 cursor-pointer select-none bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700/60 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 transition shadow-2xs" title="{{ app()->getLocale() === 'ar' ? 'تحميل تلقائي عند التمرير لأسفل الجدول' : 'Automatically load more records when scrolling down' }}">
                        <input type="checkbox" id="toggleAutoLazy" onchange="toggleAutoScroll(this.checked)" class="rounded text-sky-600 dark:text-sky-500 focus:ring-0 cursor-pointer accent-sky-600">
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
                                class="py-3.5 px-3 text-center font-bold transition-colors cursor-pointer hover:bg-slate-200/70 dark:hover:bg-slate-700/60 group {{ $sortBy === 'entry_date' ? 'bg-sky-100 dark:bg-sky-950/60 text-[#0A4F78] dark:text-sky-300 font-black border-b-2 border-sky-600 dark:border-sky-400' : '' }}" style="width: 110px;"
                                title="{{ app()->getLocale() === 'ar' ? 'انقر للترتيب حسب التاريخ' : 'Click to sort by date' }}">
                                <div class="inline-flex items-center gap-1.5 justify-center">
                                    <span>{{ app()->getLocale() === 'ar' ? 'التاريخ' : 'Date' }}</span>
                                    @if($sortBy === 'entry_date')
                                        <i class="fa-solid fa-arrow-{{ $sortDir === 'asc' ? 'up-short-wide' : 'down-wide-short' }} text-sky-600 dark:text-sky-400"></i>
                                    @else
                                        <i class="fa-solid fa-sort text-slate-400 dark:text-slate-500 group-hover:text-sky-600 dark:group-hover:text-sky-400 text-[10px] transition-colors"></i>
                                    @endif
                                </div>
                            </th>

                            <!-- 2. Collection No. -->
                            <th scope="col" onclick="triggerSort('collection_number')"
                                class="py-3.5 px-3 text-center font-bold transition-colors cursor-pointer hover:bg-slate-200/70 dark:hover:bg-slate-700/60 group {{ $sortBy === 'collection_number' ? 'bg-sky-100 dark:bg-sky-950/60 text-[#0A4F78] dark:text-sky-300 font-black border-b-2 border-sky-600 dark:border-sky-400' : '' }}" style="width: 130px;"
                                title="{{ app()->getLocale() === 'ar' ? 'انقر للترتيب حسب رقم القيد' : 'Click to sort by collection number' }}">
                                <div class="inline-flex items-center gap-1.5 justify-center">
                                    <span>{{ app()->getLocale() === 'ar' ? 'رقم السند' : 'Collection No.' }}</span>
                                    @if($sortBy === 'collection_number')
                                        <i class="fa-solid fa-arrow-{{ $sortDir === 'asc' ? 'up-short-wide' : 'down-wide-short' }} text-sky-600 dark:text-sky-400"></i>
                                    @else
                                        <i class="fa-solid fa-sort text-slate-400 dark:text-slate-500 group-hover:text-sky-600 dark:group-hover:text-sky-400 text-[10px] transition-colors"></i>
                                    @endif
                                </div>
                            </th>

                            <!-- 3. Category -->
                            <th scope="col" onclick="triggerSort('category')"
                                class="py-3.5 px-3 text-center font-bold transition-colors cursor-pointer hover:bg-slate-200/70 dark:hover:bg-slate-700/60 group {{ $sortBy === 'category' ? 'bg-sky-100 dark:bg-sky-950/60 text-[#0A4F78] dark:text-sky-300 font-black border-b-2 border-sky-600 dark:border-sky-400' : '' }}" style="width: 165px;"
                                title="{{ app()->getLocale() === 'ar' ? 'انقر للترتيب حسب التصنيف' : 'Click to sort by category' }}">
                                <div class="inline-flex items-center gap-1.5 justify-center">
                                    <span>{{ app()->getLocale() === 'ar' ? 'التصنيف' : 'Category' }}</span>
                                    @if($sortBy === 'category')
                                        <i class="fa-solid fa-arrow-{{ $sortDir === 'asc' ? 'up-short-wide' : 'down-wide-short' }} text-sky-600 dark:text-sky-400"></i>
                                    @else
                                        <i class="fa-solid fa-sort text-slate-400 dark:text-slate-500 group-hover:text-sky-600 dark:group-hover:text-sky-400 text-[10px] transition-colors"></i>
                                    @endif
                                </div>
                            </th>

                            <!-- 4. Description -->
                            <th scope="col" onclick="triggerSort('description')"
                                class="py-3.5 px-3 font-bold transition-colors cursor-pointer hover:bg-slate-200/70 dark:hover:bg-slate-700/60 group {{ $sortBy === 'description' ? 'bg-sky-100 dark:bg-sky-950/60 text-[#0A4F78] dark:text-sky-300 font-black border-b-2 border-sky-600 dark:border-sky-400' : '' }}" style="min-width: 180px;"
                                title="{{ app()->getLocale() === 'ar' ? 'انقر للترتيب حسب البيان' : 'Click to sort by description' }}">
                                <div class="inline-flex items-center gap-1.5">
                                    <span>{{ app()->getLocale() === 'ar' ? 'البيان والمرجع' : 'Description' }}</span>
                                    @if($sortBy === 'description')
                                        <i class="fa-solid fa-arrow-{{ $sortDir === 'asc' ? 'up-short-wide' : 'down-wide-short' }} text-sky-600 dark:text-sky-400"></i>
                                    @else
                                        <i class="fa-solid fa-sort text-slate-400 dark:text-slate-500 group-hover:text-sky-600 dark:group-hover:text-sky-400 text-[10px] transition-colors"></i>
                                    @endif
                                </div>
                            </th>

                            <!-- 5. Customer Code -->
                            <th scope="col" onclick="triggerSort('customer_code')"
                                class="py-3.5 px-3 text-center font-bold transition-colors cursor-pointer hover:bg-slate-200/70 dark:hover:bg-slate-700/60 group {{ $sortBy === 'customer_code' ? 'bg-sky-100 dark:bg-sky-950/60 text-[#0A4F78] dark:text-sky-300 font-black border-b-2 border-sky-600 dark:border-sky-400' : '' }}" style="width: 110px;"
                                title="{{ app()->getLocale() === 'ar' ? 'انقر للترتيب حسب كود العميل' : 'Click to sort by customer code' }}">
                                <div class="inline-flex items-center gap-1.5 justify-center">
                                    <span>{{ app()->getLocale() === 'ar' ? 'كود العميل' : 'Cust. Code' }}</span>
                                    @if($sortBy === 'customer_code')
                                        <i class="fa-solid fa-arrow-{{ $sortDir === 'asc' ? 'up-short-wide' : 'down-wide-short' }} text-sky-600 dark:text-sky-400"></i>
                                    @else
                                        <i class="fa-solid fa-sort text-slate-400 dark:text-slate-500 group-hover:text-sky-600 dark:group-hover:text-sky-400 text-[10px] transition-colors"></i>
                                    @endif
                                </div>
                            </th>

                            <!-- 6. Customer / Source -->
                            <th scope="col" onclick="triggerSort('customer_source')"
                                class="py-3.5 px-3 font-bold transition-colors cursor-pointer hover:bg-slate-200/70 dark:hover:bg-slate-700/60 group {{ $sortBy === 'customer_source' ? 'bg-sky-100 dark:bg-sky-950/60 text-[#0A4F78] dark:text-sky-300 font-black border-b-2 border-sky-600 dark:border-sky-400' : '' }}" style="min-width: 180px;"
                                title="{{ app()->getLocale() === 'ar' ? 'انقر للترتيب حسب اسم العميل / المصدر' : 'Click to sort by customer / source' }}">
                                <div class="inline-flex items-center gap-1.5">
                                    <span>{{ app()->getLocale() === 'ar' ? 'العميل / المصدر' : 'Customer / Source' }}</span>
                                    @if($sortBy === 'customer_source')
                                        <i class="fa-solid fa-arrow-{{ $sortDir === 'asc' ? 'up-short-wide' : 'down-wide-short' }} text-sky-600 dark:text-sky-400"></i>
                                    @else
                                        <i class="fa-solid fa-sort text-slate-400 dark:text-slate-500 group-hover:text-sky-600 dark:group-hover:text-sky-400 text-[10px] transition-colors"></i>
                                    @endif
                                </div>
                            </th>

                            <!-- 7. Amount Excl. VAT -->
                            <th scope="col" onclick="triggerSort('amount_excl_vat')"
                                class="py-3.5 px-3 text-right font-bold transition-colors cursor-pointer hover:bg-slate-200/70 dark:hover:bg-slate-700/60 group {{ $sortBy === 'amount_excl_vat' ? 'bg-sky-100 dark:bg-sky-950/60 text-[#0A4F78] dark:text-sky-300 font-black border-b-2 border-sky-600 dark:border-sky-400' : '' }}" style="width: 130px;"
                                title="{{ app()->getLocale() === 'ar' ? 'انقر للترتيب حسب الصافي' : 'Click to sort by net amount' }}">
                                <div class="inline-flex items-center gap-1.5 justify-end">
                                    <span>{{ app()->getLocale() === 'ar' ? 'بدون ضريبة' : 'Amount Excl.' }}</span>
                                    @if($sortBy === 'amount_excl_vat')
                                        <i class="fa-solid fa-arrow-{{ $sortDir === 'asc' ? 'up-short-wide' : 'down-wide-short' }} text-sky-600 dark:text-sky-400"></i>
                                    @else
                                        <i class="fa-solid fa-sort text-slate-400 dark:text-slate-500 group-hover:text-sky-600 dark:group-hover:text-sky-400 text-[10px] transition-colors"></i>
                                    @endif
                                </div>
                            </th>

                            <!-- 8. VAT -->
                            <th scope="col" onclick="triggerSort('vat_amount')"
                                class="py-3.5 px-3 text-right font-bold transition-colors cursor-pointer hover:bg-slate-200/70 dark:hover:bg-slate-700/60 group {{ $sortBy === 'vat_amount' ? 'bg-sky-100 dark:bg-sky-950/60 text-[#0A4F78] dark:text-sky-300 font-black border-b-2 border-sky-600 dark:border-sky-400' : '' }}" style="width: 110px;"
                                title="{{ app()->getLocale() === 'ar' ? 'انقر للترتيب حسب الضريبة' : 'Click to sort by VAT amount' }}">
                                <div class="inline-flex items-center gap-1.5 justify-end">
                                    <span>{{ app()->getLocale() === 'ar' ? 'الضريبة (15%)' : 'VAT (15%)' }}</span>
                                    @if($sortBy === 'vat_amount')
                                        <i class="fa-solid fa-arrow-{{ $sortDir === 'asc' ? 'up-short-wide' : 'down-wide-short' }} text-sky-600 dark:text-sky-400"></i>
                                    @else
                                        <i class="fa-solid fa-sort text-slate-400 dark:text-slate-500 group-hover:text-sky-600 dark:group-hover:text-sky-400 text-[10px] transition-colors"></i>
                                    @endif
                                </div>
                            </th>

                            <!-- 9. Gross Amount -->
                            <th scope="col" onclick="triggerSort('gross_amount')"
                                class="py-3.5 px-3 text-right font-extrabold transition-colors cursor-pointer hover:bg-slate-200/70 dark:hover:bg-slate-700/60 group {{ $sortBy === 'gross_amount' ? 'bg-sky-100 dark:bg-sky-950/60 text-[#0A4F78] dark:text-sky-300 font-black border-b-2 border-sky-600 dark:border-sky-400' : 'text-slate-900 dark:text-white' }}" style="width: 140px;"
                                title="{{ app()->getLocale() === 'ar' ? 'انقر للترتيب حسب الإجمالي شامل الضريبة' : 'Click to sort by gross amount' }}">
                                <div class="inline-flex items-center gap-1.5 justify-end">
                                    <span>{{ app()->getLocale() === 'ar' ? 'الإجمالي' : 'Gross Total' }}</span>
                                    @if($sortBy === 'gross_amount')
                                        <i class="fa-solid fa-arrow-{{ $sortDir === 'asc' ? 'up-short-wide' : 'down-wide-short' }} text-sky-600 dark:text-sky-400"></i>
                                    @else
                                        <i class="fa-solid fa-sort text-slate-400 dark:text-slate-500 group-hover:text-sky-600 dark:group-hover:text-sky-400 text-[10px] transition-colors"></i>
                                    @endif
                                </div>
                            </th>

                            <!-- 10. Payment Method / Account -->
                            <th scope="col" onclick="triggerSort('payment_method')"
                                class="py-3.5 px-3 text-center font-bold transition-colors cursor-pointer hover:bg-slate-200/70 dark:hover:bg-slate-700/60 group {{ $sortBy === 'payment_method' ? 'bg-sky-100 dark:bg-sky-950/60 text-[#0A4F78] dark:text-sky-300 font-black border-b-2 border-sky-600 dark:border-sky-400' : '' }}" style="min-width: 160px;"
                                title="{{ app()->getLocale() === 'ar' ? 'انقر للترتيب حسب طريقة الدفع' : 'Click to sort by payment method' }}">
                                <div class="inline-flex items-center gap-1.5 justify-center">
                                    <span>{{ app()->getLocale() === 'ar' ? 'طريقة السداد / الحساب' : 'Payment / Account' }}</span>
                                    @if($sortBy === 'payment_method')
                                        <i class="fa-solid fa-arrow-{{ $sortDir === 'asc' ? 'up-short-wide' : 'down-wide-short' }} text-sky-600 dark:text-sky-400"></i>
                                    @else
                                        <i class="fa-solid fa-sort text-slate-400 dark:text-slate-500 group-hover:text-sky-600 dark:group-hover:text-sky-400 text-[10px] transition-colors"></i>
                                    @endif
                                </div>
                            </th>

                            <!-- 11. Action -->
                            <th scope="col" class="py-3.5 px-3 text-center font-bold text-slate-600 dark:text-slate-400" style="width: 90px;">
                                {{ app()->getLocale() === 'ar' ? 'الإجراء' : 'Action' }}
                            </th>
                        </tr>
                    </thead>

                    <!-- Dynamic Table Rows -->
                    <tbody id="collectionTableBody" class="divide-y divide-slate-200/80 dark:divide-slate-800 bg-white dark:bg-slate-900 font-medium">
                        @include('admin.finance.collections._rows', ['collections' => $collections])
                    </tbody>

                    <!-- Executive High-Contrast Summary Footer -->
                    <tfoot>
                        <tr class="bg-slate-100 dark:bg-slate-800/90 text-slate-900 dark:text-white font-black border-t-2 border-slate-300 dark:border-slate-700">
                            <td colspan="6" class="py-3.5 px-4 text-right uppercase tracking-wider font-extrabold text-xs sm:text-[13px]">
                                <div class="flex items-center justify-end gap-2 text-slate-700 dark:text-slate-300">
                                    <i class="fa-solid fa-calculator text-sky-600 dark:text-sky-400"></i>
                                    <span>{{ app()->getLocale() === 'ar' ? 'إجمالي السجلات المعروضة (' : 'LOADED TOTALS (' }}</span>
                                    <span id="footerRowCount" class="text-[#0A4F78] dark:text-sky-300 font-black tabular-nums">{{ $collections->count() }}</span>
                                    <span>{{ app()->getLocale() === 'ar' ? 'سجل):' : 'ENTRIES):' }}</span>
                                </div>
                            </td>

                            <!-- Dynamic Sum Amount Excl -->
                            <td class="py-3.5 px-3.5 text-right tabular-nums font-bold text-slate-900 dark:text-white whitespace-nowrap">
                                <span id="footerExclTotal">{{ number_format($collections->sum('amount_excl_vat'), 2) }}</span>
                                <span class="text-[10px] text-slate-500 font-semibold ml-0.5">SAR</span>
                            </td>

                            <!-- Dynamic Sum VAT -->
                            <td class="py-3.5 px-3.5 text-right tabular-nums font-bold text-amber-700 dark:text-amber-400 whitespace-nowrap">
                                <span id="footerVatTotal">{{ number_format($collections->sum('vat_amount'), 2) }}</span>
                                <span class="text-[10px] text-amber-600/70 font-semibold ml-0.5">SAR</span>
                            </td>

                            <!-- Dynamic Sum Gross (Highlighted Card) -->
                            <td class="py-3.5 px-3.5 text-right tabular-nums whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 font-black text-slate-950 dark:text-white bg-sky-100/90 dark:bg-slate-700 px-2.5 py-1 rounded-md border border-sky-300/60 dark:border-slate-600 shadow-2xs">
                                    <span id="footerGrossTotal" class="text-xs sm:text-[13px] font-black text-[#0A4F78] dark:text-sky-300">{{ number_format($collections->sum('gross_amount'), 2) }}</span>
                                    <span class="text-[10px] font-bold text-sky-700 dark:text-sky-300">SAR</span>
                                </span>
                            </td>

                            <td colspan="2" class="py-3.5 px-3.5"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Modern Lazy Loading Control Hub (Replaces Traditional Clunky Pagination) -->
            <div id="lazyLoadingHub" class="p-4 sm:p-5 border-t border-slate-200 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-900/80 flex flex-col sm:flex-row items-center justify-between gap-4">
                
                <!-- Left: Visual Progress Bar & Live Count Status -->
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <div class="flex flex-col gap-1 w-full sm:w-64">
                        <div class="flex items-center justify-between text-xs text-slate-600 dark:text-slate-400 font-bold">
                            <span>
                                {{ app()->getLocale() === 'ar' ? 'عرض' : 'Showing' }}
                                <strong id="lblShowingCount" class="text-slate-900 dark:text-white tabular-nums">{{ $collections->count() }}</strong>
                                {{ app()->getLocale() === 'ar' ? 'من أصل' : 'of' }}
                                <strong id="lblTotalCount" class="text-slate-900 dark:text-white tabular-nums">{{ $collections->total() }}</strong>
                            </span>
                            <span id="lblPercentage" class="tabular-nums font-black text-[#0A4F78] dark:text-sky-400">
                                {{ $collections->total() > 0 ? min(100, round(($collections->count() / $collections->total()) * 100)) : 100 }}%
                            </span>
                        </div>
                        <!-- Animated Progress Track -->
                        <div class="w-full bg-slate-200 dark:bg-slate-700 h-2 rounded-full overflow-hidden shadow-inner">
                            <div id="hubProgressBar"
                                 class="h-full bg-gradient-to-r from-[#0A4F78] to-[#2A8FC2] rounded-full transition-all duration-300"
                                 style="width: {{ $collections->total() > 0 ? min(100, round(($collections->count() / $collections->total()) * 100)) : 100 }}%;">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: "See More" & "See Less" Action Cluster -->
                <div class="flex items-center gap-2.5 flex-wrap justify-center sm:justify-end w-full sm:w-auto">
                    
                    <!-- 1. "See Less" (Fold to initial batch) -->
                    <button type="button" id="btnSeeLess" onclick="lazyShowLess()"
                            class="hidden btn btn-secondary text-xs font-bold py-2.5 px-4 rounded-xl border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition flex items-center gap-1.5 cursor-pointer shadow-2xs">
                        <i class="fa-solid fa-angles-up text-slate-500 dark:text-slate-400 text-xs"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'عرض أقل (طي لأول ' . $perPage . ' قيد)' : 'See Less (Fold to ' . $perPage . ')' }}</span>
                    </button>

                    <!-- 2. "See More" (Lazy Load next batch) -->
                    <button type="button" id="btnSeeMore" onclick="lazyLoadMore()"
                            class="{{ $collections->hasMorePages() ? 'inline-flex' : 'hidden' }} btn text-xs font-black py-2.5 px-6 rounded-xl shadow-md items-center gap-2 text-white transition hover:brightness-110 active:scale-95 cursor-pointer"
                            style="background: linear-gradient(135deg, #0A4F78 0%, #2A8FC2 100%);">
                        <i class="fa-solid fa-angles-down text-sky-200 text-xs"></i>
                        <span id="btnSeeMoreLabel">{{ app()->getLocale() === 'ar' ? 'عرض المزيد (+ ' . $perPage . ' سجل)' : 'See More (+' . $perPage . ' Records)' }}</span>
                    </button>

                    <!-- 3. "All Loaded" Indicator (Celebratory checkmark) -->
                    <div id="allLoadedNotice" class="{{ $collections->hasMorePages() ? 'hidden' : 'inline-flex' }} items-center gap-2 px-3.5 py-2 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 text-xs font-bold border border-emerald-200 dark:border-emerald-800 shadow-2xs">
                        <i class="fa-solid fa-circle-check text-emerald-500 text-sm"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'تم استعراض كافة السجلات بالكامل' : 'All ' . $collections->total() . ' entries loaded' }}</span>
                    </div>
                </div>
            </div>

            <!-- Intersection Sentinel for automatic scroll lazy loading -->
            <div id="lazySentinel" class="h-1 w-full pointer-events-none"></div>
        </div>

    </div>

    <!-- Interactive Professional Collection Entry Modal -->
    <div id="newCollectionModal" class="fixed inset-0 z-50 bg-slate-950/75 backdrop-blur-sm hidden flex items-center justify-center p-3 sm:p-4 overflow-y-auto">
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-300 dark:border-slate-800 w-full max-w-3xl overflow-hidden animate-in fade-in zoom-in-95 duration-200 my-8" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
            
            <!-- Top Accent Stripe -->
            <div class="h-1 w-full bg-gradient-to-r from-[#0A4F78] via-[#2A8FC2] to-emerald-500" style="height: 4px; background: linear-gradient(90deg, #0A4F78 0%, #2A8FC2 50%, #10B981 100%);"></div>

            <!-- Modal Header (High-Contrast Professional Light & Dark Mode) -->
            <div class="px-6 py-4.5 bg-slate-50/95 dark:bg-[#06243C] border-b border-slate-200 dark:border-slate-800 flex items-center justify-between gap-4 transition-colors">
                <div class="flex items-center gap-3.5 min-w-0">
                    <div class="w-11 h-11 rounded-xl bg-sky-50 dark:bg-sky-950/70 border border-sky-200/80 dark:border-sky-900/60 text-[#0A4F78] dark:text-sky-300 flex items-center justify-center shrink-0 shadow-xs">
                        <i class="fa-solid fa-file-circle-plus text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <h3 class="text-base sm:text-lg font-black text-slate-900 dark:text-white m-0 tracking-tight leading-tight">
                                {{ app()->getLocale() === 'ar' ? 'تسجيل سند وقيد تحصيل جديد' : 'New Collection Entry' }}
                            </h3>
                            <span class="hidden sm:inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-sky-100/80 dark:bg-sky-950/60 text-[#0A4F78] dark:text-sky-300 border border-sky-200 dark:border-sky-800/70">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                {{ app()->getLocale() === 'ar' ? 'سند تحصيل فوري' : 'Revenue Ingestion' }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 m-0 mt-0.5 font-medium leading-relaxed truncate max-w-xl">
                            {{ app()->getLocale() === 'ar' ? 'تحصيل من عملاء النظام، الموردين، أو الأصول مع إمكانية الربط التلقائي بالفواتير' : 'Collect from system customers, suppliers, or assets with automatic invoice synchronization' }}
                        </p>
                    </div>
                </div>
                <button type="button" onclick="closeCollectionModal()" 
                        aria-label="{{ app()->getLocale() === 'ar' ? 'إغلاق' : 'Close' }}"
                        class="w-9 h-9 rounded-xl flex items-center justify-center text-slate-400 hover:text-slate-700 dark:text-slate-400 dark:hover:text-white hover:bg-slate-200/70 dark:hover:bg-slate-800 border border-transparent hover:border-slate-300 dark:hover:border-slate-700 transition cursor-pointer shrink-0">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Modal Form -->
            <form action="{{ route('admin.finance.collections.store') }}" method="POST" id="collectionForm" class="p-5 space-y-4 max-h-[82vh] overflow-y-auto">
                @csrf
                <input type="hidden" name="source_type" id="modalSourceType" value="customer">

                <!-- 1. Source Type Pill Switcher (Customer vs Supplier vs Assets) -->
                <div>
                    <label class="text-xs font-bold text-slate-800 dark:text-slate-200 block mb-1.5">
                        {{ app()->getLocale() === 'ar' ? 'جهة التحصيل والمصدر (Collection Source) *' : 'Collection Source Type *' }}
                    </label>
                    <div class="grid grid-cols-3 gap-2 p-1.5 bg-slate-100 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700">
                        <button type="button" id="btnSourceCustomer" onclick="switchSourceType('customer')" class="py-2 px-3 text-xs font-black rounded-lg transition flex items-center justify-center gap-1.5 bg-white dark:bg-slate-700 text-[#0A4F78] dark:text-sky-300 shadow-sm border border-slate-200 dark:border-slate-600">
                            <i class="fa-solid fa-user-check"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'عميل من النظام (Customer)' : 'System Customer' }}</span>
                        </button>
                        <button type="button" id="btnSourceSupplier" onclick="switchSourceType('supplier')" class="py-2 px-3 text-xs font-bold rounded-lg transition flex items-center justify-center gap-1.5 text-slate-600 dark:text-slate-300 hover:bg-white/60 dark:hover:bg-slate-700/60">
                            <i class="fa-solid fa-truck-field"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'مورد (Supplier)' : 'Supplier' }}</span>
                        </button>
                        <button type="button" id="btnSourceAsset" onclick="switchSourceType('asset')" class="py-2 px-3 text-xs font-bold rounded-lg transition flex items-center justify-center gap-1.5 text-slate-600 dark:text-slate-300 hover:bg-white/60 dark:hover:bg-slate-700/60">
                            <i class="fa-solid fa-landmark"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'أصول / أخرى (Assets / Other)' : 'Assets / Other' }}</span>
                        </button>
                    </div>
                </div>

                <!-- 2. System Entity Selector & Category Row -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <!-- Customer Selector Container -->
                    <div id="customerSelectorContainer" class="space-y-1">
                        <label class="text-xs font-bold text-slate-800 dark:text-slate-200 block mb-1">
                            {{ app()->getLocale() === 'ar' ? 'اختر العميل من النظام (Customer) *' : 'Select System Customer *' }}
                        </label>
                        <div class="relative bz-dropdown-root" id="bzRoot_modalCustomerSelect">
                            <select name="customer_id" id="modalCustomerSelect" onchange="onCustomerChange(this)" class="hidden">
                                <option value="">-- {{ app()->getLocale() === 'ar' ? 'اختر عميل النظام' : 'Select Customer' }} --</option>
                                @foreach($customers as $cust)
                                    <option value="{{ $cust->id }}"
                                            data-name="{{ $cust->name }}"
                                            data-code="CUST-{{ str_pad((string)$cust->id, 4, '0', STR_PAD_LEFT) }}">
                                        {{ $cust->name }} &bull; {{ $cust->phone ?: $cust->email }}
                                    </option>
                                @endforeach
                            </select>

                            <button type="button" onclick="BzDropdown.toggle('modalCustomerSelect')" id="bzTrigger_modalCustomerSelect"
                                    class="w-full flex items-center justify-between gap-2 px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold text-slate-800 dark:text-slate-100 hover:border-sky-400 dark:hover:border-sky-500 focus:outline-none transition shadow-2xs cursor-pointer select-none">
                                <div class="flex items-center gap-2.5 min-w-0 flex-1">
                                    <span class="w-6 h-6 rounded-lg bg-sky-50 dark:bg-sky-950/60 text-[#0A4F78] dark:text-sky-300 flex items-center justify-center shrink-0 text-xs">
                                        <i class="fa-solid fa-user-check"></i>
                                    </span>
                                    <span id="bzLabel_modalCustomerSelect" class="truncate text-slate-800 dark:text-slate-100">
                                        -- {{ app()->getLocale() === 'ar' ? 'اختر عميل النظام' : 'Select Customer' }} --
                                    </span>
                                </div>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <span id="bzClear_modalCustomerSelect" onclick="BzDropdown.clear(event, 'modalCustomerSelect', '{{ app()->getLocale() === 'ar' ? '-- اختر عميل النظام --' : '-- Select Customer --' }}')" class="hidden w-5 h-5 rounded-md hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 flex items-center justify-center text-xs transition" title="Clear">
                                        <i class="fa-solid fa-xmark"></i>
                                    </span>
                                    <i id="bzChevron_modalCustomerSelect" class="fa-solid fa-chevron-down text-slate-400 text-[10px] transition-transform duration-200"></i>
                                </div>
                            </button>

                            <!-- Dropdown Menu -->
                            <div id="bzMenu_modalCustomerSelect" class="hidden absolute left-0 right-0 top-full mt-1.5 z-50 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-150">
                                <div class="p-2 border-b border-slate-100 dark:border-slate-800 bg-slate-50/80 dark:bg-slate-900/90">
                                    <div class="relative flex items-center">
                                        <i class="fa-solid fa-magnifying-glass absolute {{ app()->getLocale() === 'ar' ? 'right-3' : 'left-3' }} text-slate-400 text-xs pointer-events-none"></i>
                                        <input type="text" id="bzSearch_modalCustomerSelect" oninput="BzDropdown.filter('modalCustomerSelect')"
                                               placeholder="{{ app()->getLocale() === 'ar' ? 'بحث باسم العميل، الهاتف، أو الكود...' : 'Search customer name, phone, or code...' }}"
                                               class="w-full text-xs py-2 {{ app()->getLocale() === 'ar' ? 'pr-8 pl-8' : 'pl-8 pr-8' }} bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 transition">
                                        <span id="bzCount_modalCustomerSelect" class="absolute {{ app()->getLocale() === 'ar' ? 'left-2.5' : 'right-2.5' }} text-[10px] font-bold text-slate-400"></span>
                                    </div>
                                </div>
                                <div id="bzOptions_modalCustomerSelect" class="max-h-56 overflow-y-auto p-1.5 space-y-1 bz-select-scroll">
                                    @foreach($customers as $cust)
                                        @php
                                            $custCode = 'CUST-' . str_pad((string)$cust->id, 4, '0', STR_PAD_LEFT);
                                        @endphp
                                        <div onclick="BzDropdown.select('modalCustomerSelect', '{{ $cust->id }}', '{{ addslashes($cust->name) }} ({{ $custCode }})')"
                                             data-value="{{ $cust->id }}"
                                             data-search="{{ strtolower($cust->name . ' ' . $custCode . ' ' . $cust->phone . ' ' . $cust->email) }}"
                                             class="bz-option-item flex items-center justify-between gap-2.5 px-3 py-2 rounded-xl text-xs cursor-pointer text-slate-700 dark:text-slate-200 hover:bg-sky-50/70 dark:hover:bg-sky-950/40 hover:text-sky-900 dark:hover:text-sky-200 transition-colors">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <span class="w-2 h-2 rounded-full bg-slate-300 dark:bg-slate-600 shrink-0"></span>
                                                <div class="min-w-0">
                                                    <div class="font-bold text-slate-900 dark:text-white truncate">
                                                        {{ $cust->name }}
                                                    </div>
                                                    @if($cust->phone || $cust->email)
                                                        <div class="text-[11px] text-slate-400 truncate">
                                                            {{ $cust->phone ?: $cust->email }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2 shrink-0">
                                                <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-[10px] font-mono font-bold text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                                    {{ $custCode }}
                                                </span>
                                                <i class="fa-solid fa-check text-[#0A4F78] dark:text-sky-400 text-xs hidden bz-option-check"></i>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div id="bzEmpty_modalCustomerSelect" class="hidden p-6 text-center text-xs text-slate-400 dark:text-slate-500">
                                    <i class="fa-solid fa-folder-open text-xl mb-1.5 block opacity-40"></i>
                                    <span>{{ app()->getLocale() === 'ar' ? 'لا يوجد عميل مطابق للبحث' : 'No matching customers found' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Supplier Selector Container (Hidden by default) -->
                    <div id="supplierSelectorContainer" class="hidden space-y-1">
                        <label class="text-xs font-bold text-slate-800 dark:text-slate-200 block mb-1">
                            {{ app()->getLocale() === 'ar' ? 'اختر المورد من النظام (Supplier) *' : 'Select Supplier *' }}
                        </label>
                        <div class="relative bz-dropdown-root" id="bzRoot_modalSupplierSelect">
                            <select name="supplier_id" id="modalSupplierSelect" onchange="onSupplierChange(this)" class="hidden">
                                <option value="">-- {{ app()->getLocale() === 'ar' ? 'اختر مورد النظام' : 'Select Supplier' }} --</option>
                                @foreach($suppliers as $sup)
                                    <option value="{{ $sup->id }}"
                                            data-name="{{ $sup->name_en ?: $sup->name_ar }}"
                                            data-code="{{ $sup->code ?: ('SUP-' . $sup->id) }}">
                                        [{{ $sup->code }}] {{ $sup->name_en }} / {{ $sup->name_ar }}
                                    </option>
                                @endforeach
                            </select>

                            <button type="button" onclick="BzDropdown.toggle('modalSupplierSelect')" id="bzTrigger_modalSupplierSelect"
                                    class="w-full flex items-center justify-between gap-2 px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold text-slate-800 dark:text-slate-100 hover:border-sky-400 dark:hover:border-sky-500 focus:outline-none transition shadow-2xs cursor-pointer select-none">
                                <div class="flex items-center gap-2.5 min-w-0 flex-1">
                                    <span class="w-6 h-6 rounded-lg bg-sky-50 dark:bg-sky-950/60 text-[#0A4F78] dark:text-sky-300 flex items-center justify-center shrink-0 text-xs">
                                        <i class="fa-solid fa-truck-field"></i>
                                    </span>
                                    <span id="bzLabel_modalSupplierSelect" class="truncate text-slate-800 dark:text-slate-100">
                                        -- {{ app()->getLocale() === 'ar' ? 'اختر مورد النظام' : 'Select Supplier' }} --
                                    </span>
                                </div>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <span id="bzClear_modalSupplierSelect" onclick="BzDropdown.clear(event, 'modalSupplierSelect', '{{ app()->getLocale() === 'ar' ? '-- اختر مورد النظام --' : '-- Select Supplier --' }}')" class="hidden w-5 h-5 rounded-md hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 flex items-center justify-center text-xs transition" title="Clear">
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
                                               class="w-full text-xs py-2 {{ app()->getLocale() === 'ar' ? 'pr-8 pl-8' : 'pl-8 pr-8' }} bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 transition">
                                        <span id="bzCount_modalSupplierSelect" class="absolute {{ app()->getLocale() === 'ar' ? 'left-2.5' : 'right-2.5' }} text-[10px] font-bold text-slate-400"></span>
                                    </div>
                                </div>
                                <div id="bzOptions_modalSupplierSelect" class="max-h-56 overflow-y-auto p-1.5 space-y-1 bz-select-scroll">
                                    @foreach($suppliers as $sup)
                                        <div onclick="BzDropdown.select('modalSupplierSelect', '{{ $sup->id }}', '{{ addslashes($sup->name_en ?: $sup->name_ar) }} [{{ $sup->code }}]')"
                                             data-value="{{ $sup->id }}"
                                             data-search="{{ strtolower($sup->name_en . ' ' . $sup->name_ar . ' ' . $sup->code) }}"
                                             class="bz-option-item flex items-center justify-between gap-2.5 px-3 py-2 rounded-xl text-xs cursor-pointer text-slate-700 dark:text-slate-200 hover:bg-sky-50/70 dark:hover:bg-sky-950/40 hover:text-sky-900 dark:hover:text-sky-200 transition-colors">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <span class="w-2 h-2 rounded-full bg-slate-300 dark:bg-slate-600 shrink-0"></span>
                                                <div class="min-w-0">
                                                    <div class="font-bold text-slate-900 dark:text-white truncate">
                                                        {{ $sup->name_en }} @if($sup->name_ar && $sup->name_ar !== $sup->name_en) <span class="text-slate-400 dark:text-slate-500 font-normal">/ {{ $sup->name_ar }}</span> @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2 shrink-0">
                                                <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-[10px] font-mono font-bold text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                                    {{ $sup->code ?: ('SUP-' . $sup->id) }}
                                                </span>
                                                <i class="fa-solid fa-check text-[#0A4F78] dark:text-sky-400 text-xs hidden bz-option-check"></i>
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

                    <!-- Category Selector -->
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-800 dark:text-slate-200 block mb-1">
                            {{ app()->getLocale() === 'ar' ? 'التصنيف المالي (Category) *' : 'Category *' }}
                        </label>
                        <div class="relative bz-dropdown-root" id="bzRoot_modalCategorySelect">
                            <select name="category" id="modalCategorySelect" onchange="handleCategoryChange(this.value)" required class="hidden">
                                <option value="sales" selected>{{ app()->getLocale() === 'ar' ? 'مبيعات تجارية (sales)' : 'sales' }}</option>
                                <option value="supplier_refund">{{ app()->getLocale() === 'ar' ? 'استرداد / تسوية مورد (supplier refund)' : 'supplier refund / credit' }}</option>
                                <option value="assets_disposal">{{ app()->getLocale() === 'ar' ? 'تحصيل بيع أصول (assets disposal)' : 'asset sale / disposal' }}</option>
                                <option value="non_operational_revenue">{{ app()->getLocale() === 'ar' ? 'إيرادات غير تشغيلية (non-operational revenue)' : 'non-operational revenue' }}</option>
                                <option value="equities">{{ app()->getLocale() === 'ar' ? 'حقوق ملكية ورأس مال (equities)' : 'equities' }}</option>
                            </select>

                            <button type="button" onclick="BzDropdown.toggle('modalCategorySelect')" id="bzTrigger_modalCategorySelect"
                                    class="w-full flex items-center justify-between gap-2 px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold text-slate-800 dark:text-slate-100 hover:border-sky-400 dark:hover:border-sky-500 focus:outline-none transition shadow-2xs cursor-pointer select-none">
                                <div class="flex items-center gap-2.5 min-w-0 flex-1">
                                    <span class="w-6 h-6 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 text-xs">
                                        <i class="fa-solid fa-layer-group"></i>
                                    </span>
                                    <span id="bzLabel_modalCategorySelect" class="truncate text-slate-800 dark:text-slate-100">
                                        {{ app()->getLocale() === 'ar' ? 'مبيعات تجارية (sales)' : 'sales' }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <i id="bzChevron_modalCategorySelect" class="fa-solid fa-chevron-down text-slate-400 text-[10px] transition-transform duration-200"></i>
                                </div>
                            </button>

                            <!-- Dropdown Menu -->
                            <div id="bzMenu_modalCategorySelect" class="hidden absolute left-0 right-0 top-full mt-1.5 z-50 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-150">
                                <div id="bzOptions_modalCategorySelect" class="p-1.5 space-y-1">
                                    <div onclick="BzDropdown.select('modalCategorySelect', 'sales', '{{ app()->getLocale() === 'ar' ? 'مبيعات تجارية (sales)' : 'sales' }}')"
                                         data-value="sales"
                                         class="bz-option-item flex items-center justify-between gap-2.5 px-3 py-2 rounded-xl text-xs cursor-pointer text-slate-700 dark:text-slate-200 hover:bg-sky-50/70 dark:hover:bg-sky-950/40 hover:text-sky-900 dark:hover:text-sky-200 transition-colors bg-sky-50 dark:bg-sky-950/40 font-bold">
                                        <span>{{ app()->getLocale() === 'ar' ? 'مبيعات تجارية (sales)' : 'sales' }}</span>
                                        <i class="fa-solid fa-check text-[#0A4F78] dark:text-sky-400 text-xs bz-option-check"></i>
                                    </div>
                                    <div onclick="BzDropdown.select('modalCategorySelect', 'supplier_refund', '{{ app()->getLocale() === 'ar' ? 'استرداد / تسوية مورد (supplier refund)' : 'supplier refund / credit' }}')"
                                         data-value="supplier_refund"
                                         class="bz-option-item flex items-center justify-between gap-2.5 px-3 py-2 rounded-xl text-xs cursor-pointer text-slate-700 dark:text-slate-200 hover:bg-sky-50/70 dark:hover:bg-sky-950/40 hover:text-sky-900 dark:hover:text-sky-200 transition-colors">
                                        <span>{{ app()->getLocale() === 'ar' ? 'استرداد / تسوية مورد (supplier refund)' : 'supplier refund / credit' }}</span>
                                        <i class="fa-solid fa-check text-[#0A4F78] dark:text-sky-400 text-xs hidden bz-option-check"></i>
                                    </div>
                                    <div onclick="BzDropdown.select('modalCategorySelect', 'assets_disposal', '{{ app()->getLocale() === 'ar' ? 'تحصيل بيع أصول (assets disposal)' : 'asset sale / disposal' }}')"
                                         data-value="assets_disposal"
                                         class="bz-option-item flex items-center justify-between gap-2.5 px-3 py-2 rounded-xl text-xs cursor-pointer text-slate-700 dark:text-slate-200 hover:bg-sky-50/70 dark:hover:bg-sky-950/40 hover:text-sky-900 dark:hover:text-sky-200 transition-colors">
                                        <span>{{ app()->getLocale() === 'ar' ? 'تحصيل بيع أصول (assets disposal)' : 'asset sale / disposal' }}</span>
                                        <i class="fa-solid fa-check text-[#0A4F78] dark:text-sky-400 text-xs hidden bz-option-check"></i>
                                    </div>
                                    <div onclick="BzDropdown.select('modalCategorySelect', 'non_operational_revenue', '{{ app()->getLocale() === 'ar' ? 'إيرادات غير تشغيلية (non-operational revenue)' : 'non-operational revenue' }}')"
                                         data-value="non_operational_revenue"
                                         class="bz-option-item flex items-center justify-between gap-2.5 px-3 py-2 rounded-xl text-xs cursor-pointer text-slate-700 dark:text-slate-200 hover:bg-sky-50/70 dark:hover:bg-sky-950/40 hover:text-sky-900 dark:hover:text-sky-200 transition-colors">
                                        <span>{{ app()->getLocale() === 'ar' ? 'إيرادات غير تشغيلية (non-operational revenue)' : 'non-operational revenue' }}</span>
                                        <i class="fa-solid fa-check text-[#0A4F78] dark:text-sky-400 text-xs hidden bz-option-check"></i>
                                    </div>
                                    <div onclick="BzDropdown.select('modalCategorySelect', 'equities', '{{ app()->getLocale() === 'ar' ? 'حقوق ملكية ورأس مال (equities)' : 'equities' }}')"
                                         data-value="equities"
                                         class="bz-option-item flex items-center justify-between gap-2.5 px-3 py-2 rounded-xl text-xs cursor-pointer text-slate-700 dark:text-slate-200 hover:bg-sky-50/70 dark:hover:bg-sky-950/40 hover:text-sky-900 dark:hover:text-sky-200 transition-colors">
                                        <span>{{ app()->getLocale() === 'ar' ? 'حقوق ملكية ورأس مال (equities)' : 'equities' }}</span>
                                        <i class="fa-solid fa-check text-[#0A4F78] dark:text-sky-400 text-xs hidden bz-option-check"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Customer Code & Customer Name Row (Auto-filled or editable) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="text-xs font-bold text-slate-800 dark:text-slate-200 block mb-1">
                            {{ app()->getLocale() === 'ar' ? 'كود العميل / المورد (Entity Code)' : 'Entity Code' }}
                        </label>
                        <input type="text" name="customer_code" id="modalCustomerCode" placeholder="e.g. CUST-0001" class="form-control text-xs tabular-nums font-bold text-slate-900 dark:text-white bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-700">
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-800 dark:text-slate-200 block mb-1">
                            {{ app()->getLocale() === 'ar' ? 'اسم العميل / الجهة / المصدر (Customer / Source) *' : 'Customer / Source Name *' }}
                        </label>
                        <input type="text" name="customer_source" id="modalCustomerSource" placeholder="{{ app()->getLocale() === 'ar' ? 'اسم العميل، الشركة، أو الشريك' : 'Customer or Partner Name' }}" required class="form-control text-xs font-bold text-slate-900 dark:text-white bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-700">
                    </div>
                </div>

                <!-- 4. Invoice Quick-Picker (Auto-Collect Feature) -->
                <div class="p-3.5 rounded-xl bg-sky-50 dark:bg-sky-950/40 border border-sky-200 dark:border-sky-800 space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-black text-[#0A4F78] dark:text-sky-300 flex items-center gap-1.5">
                            <i class="fa-solid fa-bolt text-amber-500"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'تحصيل آلي من فاتورة حالية (Auto-Collect from Invoice)' : 'Auto-Collect from Invoice' }}</span>
                        </label>
                        <span class="text-[11px] font-bold text-sky-700 dark:text-sky-400">
                            {{ app()->getLocale() === 'ar' ? 'اختيار الفاتورة يملأ المبالغ والضريبة ورقم الفاتورة فورياً' : 'Selecting an invoice auto-fills Net, VAT, & Gross' }}
                        </span>
                    </div>

                    <div class="relative bz-dropdown-root" id="bzRoot_modalInvoiceSelect">
                        <select name="order_id" id="modalInvoiceSelect" onchange="onInvoiceChange(this)" class="hidden">
                            <option value="">-- {{ app()->getLocale() === 'ar' ? 'اختر الفاتورة للتحصيل الآلي (أو اترك فارغاً للتحصيل الحر)' : '-- Select Invoice (or leave empty for manual collection) --' }} --</option>
                            @foreach($invoices as $inv)
                                <option value="{{ $inv->id }}"
                                        data-invoice-number="{{ $inv->invoice_number ?: $inv->order_number }}"
                                        data-customer-id="{{ $inv->customer_id }}"
                                        data-customer-name="{{ $inv->customer_name }}"
                                        data-subtotal="{{ $inv->subtotal }}"
                                        data-tax="{{ $inv->tax }}"
                                        data-total="{{ $inv->total }}">
                                    [{{ $inv->invoice_number ?: ('Order #' . $inv->order_number) }}] &bull; {{ $inv->customer_name }} &bull; Total: {{ number_format($inv->total, 2) }} SAR ({{ $inv->payment_status ?: 'Open' }})
                                </option>
                            @endforeach
                        </select>

                        <button type="button" onclick="BzDropdown.toggle('modalInvoiceSelect')" id="bzTrigger_modalInvoiceSelect"
                                class="w-full flex items-center justify-between gap-2 px-3.5 py-2.5 rounded-xl border border-sky-300 dark:border-sky-700 bg-white dark:bg-slate-900 text-xs font-bold text-slate-800 dark:text-slate-100 hover:border-sky-400 dark:hover:border-sky-500 focus:outline-none transition shadow-2xs cursor-pointer select-none">
                            <div class="flex items-center gap-2.5 min-w-0 flex-1">
                                <span class="w-6 h-6 rounded-lg bg-sky-50 dark:bg-sky-950/60 text-[#0A4F78] dark:text-sky-300 flex items-center justify-center shrink-0 text-xs">
                                    <i class="fa-solid fa-file-invoice"></i>
                                </span>
                                <span id="bzLabel_modalInvoiceSelect" class="truncate text-slate-800 dark:text-slate-100">
                                    -- {{ app()->getLocale() === 'ar' ? 'اختر الفاتورة للتحصيل الآلي (أو اترك فارغاً للتحصيل الحر)' : '-- Select Invoice (or leave empty for manual collection) --' }} --
                                </span>
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <span id="bzClear_modalInvoiceSelect" onclick="BzDropdown.clear(event, 'modalInvoiceSelect', '{{ app()->getLocale() === 'ar' ? '-- اختر الفاتورة للتحصيل الآلي --' : '-- Select Invoice --' }}')" class="hidden w-5 h-5 rounded-md hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 flex items-center justify-center text-xs transition" title="Clear">
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
                                           placeholder="{{ app()->getLocale() === 'ar' ? 'بحث برقم الفاتورة، العميل، أو القيمة...' : 'Search invoice #, customer, or total...' }}"
                                           class="w-full text-xs py-2 {{ app()->getLocale() === 'ar' ? 'pr-8 pl-8' : 'pl-8 pr-8' }} bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 transition">
                                    <span id="bzCount_modalInvoiceSelect" class="absolute {{ app()->getLocale() === 'ar' ? 'left-2.5' : 'right-2.5' }} text-[10px] font-bold text-slate-400"></span>
                                </div>
                            </div>
                            <div id="bzOptions_modalInvoiceSelect" class="max-h-56 overflow-y-auto p-1.5 space-y-1 bz-select-scroll">
                                <div onclick="BzDropdown.select('modalInvoiceSelect', '', '{{ app()->getLocale() === 'ar' ? '-- بدون ربط بفاتورة (تحصيل حر) --' : '-- Direct Collection (No specific invoice) --' }}')"
                                     data-value=""
                                     data-search="manual direct collection"
                                     class="bz-option-item flex items-center justify-between gap-2.5 px-3 py-2 rounded-xl text-xs cursor-pointer text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                                    <span class="font-medium text-slate-500 dark:text-slate-400">-- {{ app()->getLocale() === 'ar' ? 'بدون ربط بفاتورة (تحصيل حر)' : 'Direct Collection (No invoice)' }} --</span>
                                    <i class="fa-solid fa-check text-[#0A4F78] dark:text-sky-400 text-xs hidden bz-option-check"></i>
                                </div>
                                @foreach($invoices as $inv)
                                    <div onclick="BzDropdown.select('modalInvoiceSelect', '{{ $inv->id }}', '#{{ $inv->invoice_number ?: $inv->order_number }} &bull; {{ number_format($inv->total, 2) }} SAR')"
                                         data-value="{{ $inv->id }}"
                                         data-search="{{ strtolower(($inv->invoice_number ?: $inv->order_number) . ' ' . $inv->customer_name . ' ' . $inv->total) }}"
                                         class="bz-option-item flex items-center justify-between gap-2.5 px-3 py-2 rounded-xl text-xs cursor-pointer text-slate-700 dark:text-slate-200 hover:bg-sky-50/70 dark:hover:bg-sky-950/40 hover:text-sky-900 dark:hover:text-sky-200 transition-colors">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <i class="fa-solid fa-file-invoice text-sky-500 text-xs shrink-0"></i>
                                            <div class="min-w-0">
                                                <div class="font-bold text-slate-900 dark:text-white truncate">
                                                    #{{ $inv->invoice_number ?: $inv->order_number }}
                                                </div>
                                                <div class="text-[11px] text-slate-500 dark:text-slate-400 truncate">
                                                    {{ $inv->customer_name }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2 shrink-0">
                                            <span class="px-2 py-0.5 rounded-md bg-sky-50 dark:bg-sky-950/50 text-[10px] font-mono font-bold text-sky-700 dark:text-sky-300 border border-sky-200/60 dark:border-sky-800/60">
                                                {{ number_format($inv->total, 2) }} SAR
                                            </span>
                                            <i class="fa-solid fa-check text-[#0A4F78] dark:text-sky-400 text-xs hidden bz-option-check"></i>
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

                <!-- 5. Date & Description / Ref -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label class="text-xs font-bold text-slate-800 dark:text-slate-200 block mb-1">
                            {{ app()->getLocale() === 'ar' ? 'تاريخ التحصيل (Date) *' : 'Entry Date *' }}
                        </label>
                        <input type="date" name="entry_date" value="{{ date('Y-m-d') }}" required class="form-control text-xs tabular-nums font-bold text-slate-900 dark:text-white bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-700">
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-800 dark:text-slate-200 block mb-1">
                            {{ app()->getLocale() === 'ar' ? 'البيان ورقم المرجع (Description / Ref)' : 'Description (Typing / Ref)' }}
                        </label>
                        <input type="text" name="description" id="modalDescription" placeholder="{{ app()->getLocale() === 'ar' ? 'رقم الفاتورة أو بيان التحصيل...' : 'Invoice ref or notes...' }}" class="form-control text-xs font-medium text-slate-900 dark:text-white bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-700">
                    </div>
                </div>

                <!-- 6. Live 2-Way VAT Calculator (High Contrast & Clear Typography) -->
                <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-300 dark:border-slate-700 space-y-3">
                    <div class="flex items-center justify-between text-xs text-slate-800 dark:text-slate-200 font-bold">
                        <span><i class="fa-solid fa-calculator text-primary mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'احتساب المبالغ والضريبة تلقائياً (Live 2-Way VAT Calculator)' : 'Live 2-Way VAT Calculator' }}</span>
                        <div class="flex items-center gap-1.5 tabular-nums text-xs">
                            <span class="font-bold">VAT Rate:</span>
                            <input type="number" id="modalVatRate" name="vat_rate" value="15" step="0.01" min="0" max="100" class="w-16 text-center font-bold text-xs py-1 border border-slate-300 dark:border-slate-600 rounded bg-white dark:bg-slate-900 text-slate-900 dark:text-white" oninput="calculateFromGross()">
                            <span class="font-bold">%</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="text-xs font-bold text-slate-700 dark:text-slate-300 block mb-1">
                                Amount Excl. VAT (الصافي بدون ضريبة)
                            </label>
                            <input type="number" id="modalAmountExcl" name="amount_excl_vat" step="0.01" min="0" placeholder="0.00" class="form-control text-xs tabular-nums font-bold text-slate-900 dark:text-white bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700" oninput="calculateFromExcl()">
                        </div>

                        <div>
                            <label class="text-xs font-bold text-slate-700 dark:text-slate-300 block mb-1">
                                VAT Amount (قيمة الضريبة)
                            </label>
                            <input type="number" id="modalVatAmount" name="vat_amount" step="0.01" min="0" readonly placeholder="0.00" class="form-control text-xs tabular-nums font-black bg-amber-500/10 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 border-amber-300 dark:border-amber-700">
                        </div>

                        <div>
                            <label class="text-xs font-black text-[#0A4F78] dark:text-sky-300 block mb-1">
                                Gross Amount (الإجمالي شامل الضريبة) *
                            </label>
                            <input type="number" id="modalGrossAmount" name="gross_amount" step="0.01" min="0.01" required placeholder="0.00" class="form-control text-xs tabular-nums font-black text-[#0A4F78] dark:text-sky-200 bg-sky-50/50 dark:bg-sky-950/40 border-2 border-sky-400 dark:border-sky-600" oninput="calculateFromGross()">
                        </div>
                    </div>
                </div>

                <!-- 7. Payment Method & Financial Account -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <!-- Payment Method Dropdown -->
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-800 dark:text-slate-200 block mb-1">
                            {{ app()->getLocale() === 'ar' ? 'طريقة السداد (Payment Method) *' : 'Payment Method *' }}
                        </label>
                        <div class="relative bz-dropdown-root" id="bzRoot_modalPaymentMethod">
                            <select name="payment_method" id="modalPaymentMethod" onchange="handlePaymentMethodChange(this.value)" class="hidden" required>
                                <option value="bank_transfer" selected>bank transfer (تحويل بنكي)</option>
                                <option value="cash">cash (نقداً)</option>
                                <option value="cheque">cheque (شيك)</option>
                                <option value="online_gateway">online payment (بوابة إلكترونية)</option>
                            </select>

                            <button type="button" onclick="BzDropdown.toggle('modalPaymentMethod')" id="bzTrigger_modalPaymentMethod"
                                    class="w-full flex items-center justify-between gap-2 px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold text-slate-800 dark:text-slate-100 hover:border-sky-400 dark:hover:border-sky-500 focus:outline-none transition shadow-2xs cursor-pointer select-none">
                                <div class="flex items-center gap-2.5 min-w-0 flex-1">
                                    <span class="w-6 h-6 rounded-lg bg-sky-50 dark:bg-sky-950/60 text-[#0A4F78] dark:text-sky-300 flex items-center justify-center shrink-0 text-xs">
                                        <i class="fa-solid fa-wallet"></i>
                                    </span>
                                    <span id="bzLabel_modalPaymentMethod" class="truncate text-slate-800 dark:text-slate-100">
                                        bank transfer (تحويل بنكي)
                                    </span>
                                </div>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <i id="bzChevron_modalPaymentMethod" class="fa-solid fa-chevron-down text-slate-400 text-[10px] transition-transform duration-200"></i>
                                </div>
                            </button>

                            <!-- Dropdown Menu -->
                            <div id="bzMenu_modalPaymentMethod" class="hidden absolute left-0 right-0 top-full mt-1.5 z-50 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-150">
                                <div id="bzOptions_modalPaymentMethod" class="p-1.5 space-y-1">
                                    <div onclick="BzDropdown.select('modalPaymentMethod', 'bank_transfer', 'bank transfer (تحويل بنكي)')"
                                         data-value="bank_transfer"
                                         class="bz-option-item flex items-center justify-between gap-2.5 px-3 py-2 rounded-xl text-xs cursor-pointer text-slate-700 dark:text-slate-200 hover:bg-sky-50/70 dark:hover:bg-sky-950/40 hover:text-sky-900 dark:hover:text-sky-200 transition-colors bg-sky-50 dark:bg-sky-950/40 font-bold">
                                        <div class="flex items-center gap-2">
                                            <i class="fa-solid fa-building-columns text-sky-600 text-xs"></i>
                                            <span>bank transfer (تحويل بنكي)</span>
                                        </div>
                                        <i class="fa-solid fa-check text-[#0A4F78] dark:text-sky-400 text-xs bz-option-check"></i>
                                    </div>
                                    <div onclick="BzDropdown.select('modalPaymentMethod', 'cash', 'cash (نقداً)')"
                                         data-value="cash"
                                         class="bz-option-item flex items-center justify-between gap-2.5 px-3 py-2 rounded-xl text-xs cursor-pointer text-slate-700 dark:text-slate-200 hover:bg-sky-50/70 dark:hover:bg-sky-950/40 hover:text-sky-900 dark:hover:text-sky-200 transition-colors">
                                        <div class="flex items-center gap-2">
                                            <i class="fa-solid fa-money-bill-wave text-emerald-600 text-xs"></i>
                                            <span>cash (نقداً)</span>
                                        </div>
                                        <i class="fa-solid fa-check text-[#0A4F78] dark:text-sky-400 text-xs hidden bz-option-check"></i>
                                    </div>
                                    <div onclick="BzDropdown.select('modalPaymentMethod', 'cheque', 'cheque (شيك)')"
                                         data-value="cheque"
                                         class="bz-option-item flex items-center justify-between gap-2.5 px-3 py-2 rounded-xl text-xs cursor-pointer text-slate-700 dark:text-slate-200 hover:bg-sky-50/70 dark:hover:bg-sky-950/40 hover:text-sky-900 dark:hover:text-sky-200 transition-colors">
                                        <div class="flex items-center gap-2">
                                            <i class="fa-solid fa-money-check-dollar text-purple-600 text-xs"></i>
                                            <span>cheque (شيك)</span>
                                        </div>
                                        <i class="fa-solid fa-check text-[#0A4F78] dark:text-sky-400 text-xs hidden bz-option-check"></i>
                                    </div>
                                    <div onclick="BzDropdown.select('modalPaymentMethod', 'online_gateway', 'online payment (بوابة إلكترونية)')"
                                         data-value="online_gateway"
                                         class="bz-option-item flex items-center justify-between gap-2.5 px-3 py-2 rounded-xl text-xs cursor-pointer text-slate-700 dark:text-slate-200 hover:bg-sky-50/70 dark:hover:bg-sky-950/40 hover:text-sky-900 dark:hover:text-sky-200 transition-colors">
                                        <div class="flex items-center gap-2">
                                            <i class="fa-solid fa-globe text-blue-600 text-xs"></i>
                                            <span>online payment (بوابة إلكترونية)</span>
                                        </div>
                                        <i class="fa-solid fa-check text-[#0A4F78] dark:text-sky-400 text-xs hidden bz-option-check"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Financial Account Dropdown -->
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-800 dark:text-slate-200 block mb-1">
                            {{ app()->getLocale() === 'ar' ? 'الحساب البنكي / الصندوق (Financial Account)' : 'Account (Bank / Cash Box)' }}
                        </label>
                        <div class="relative bz-dropdown-root" id="bzRoot_modalFinancialAccount">
                            <select name="financial_account_id" id="modalFinancialAccount" class="hidden">
                                <option value="">{{ app()->getLocale() === 'ar' ? '-- بدون ربط فوري --' : '-- No Account Link --' }}</option>
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}" {{ $loop->first ? 'selected' : '' }}>
                                        {{ $acc->name_en }} &bull; {{ $acc->type }} (Bal: {{ number_format($acc->current_balance, 2) }})
                                    </option>
                                @endforeach
                            </select>

                            <button type="button" onclick="BzDropdown.toggle('modalFinancialAccount')" id="bzTrigger_modalFinancialAccount"
                                    class="w-full flex items-center justify-between gap-2 px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold text-slate-800 dark:text-slate-100 hover:border-sky-400 dark:hover:border-sky-500 focus:outline-none transition shadow-2xs cursor-pointer select-none">
                                <div class="flex items-center gap-2.5 min-w-0 flex-1">
                                    <span class="w-6 h-6 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 text-xs">
                                        <i class="fa-solid fa-building-columns"></i>
                                    </span>
                                    <span id="bzLabel_modalFinancialAccount" class="truncate text-slate-800 dark:text-slate-100">
                                        @php $firstAcc = $accounts->first(); @endphp
                                        {{ $firstAcc ? $firstAcc->name_en . ' (' . number_format($firstAcc->current_balance, 2) . ' SAR)' : (app()->getLocale() === 'ar' ? '-- بدون ربط فوري --' : '-- No Account Link --') }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <span id="bzClear_modalFinancialAccount" onclick="BzDropdown.clear(event, 'modalFinancialAccount', '{{ app()->getLocale() === 'ar' ? '-- بدون ربط فوري --' : '-- No Account Link --' }}')" class="hidden w-5 h-5 rounded-md hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 flex items-center justify-center text-xs transition" title="Clear">
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
                                               class="w-full text-xs py-2 {{ app()->getLocale() === 'ar' ? 'pr-8 pl-8' : 'pl-8 pr-8' }} bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 transition">
                                        <span id="bzCount_modalFinancialAccount" class="absolute {{ app()->getLocale() === 'ar' ? 'left-2.5' : 'right-2.5' }} text-[10px] font-bold text-slate-400"></span>
                                    </div>
                                </div>
                                <div id="bzOptions_modalFinancialAccount" class="max-h-56 overflow-y-auto p-1.5 space-y-1 bz-select-scroll">
                                    <div onclick="BzDropdown.select('modalFinancialAccount', '', '{{ app()->getLocale() === 'ar' ? '-- بدون ربط فوري --' : '-- No Account Link --' }}')"
                                         data-value=""
                                         data-search="no account link"
                                         class="bz-option-item flex items-center justify-between gap-2.5 px-3 py-2 rounded-xl text-xs cursor-pointer text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                                        <span class="font-medium text-slate-500 dark:text-slate-400">-- {{ app()->getLocale() === 'ar' ? 'بدون ربط فوري' : 'No Account Link' }} --</span>
                                        <i class="fa-solid fa-check text-[#0A4F78] dark:text-sky-400 text-xs hidden bz-option-check"></i>
                                    </div>
                                    @foreach($accounts as $acc)
                                        <div onclick="BzDropdown.select('modalFinancialAccount', '{{ $acc->id }}', '{{ addslashes($acc->name_en) }} ({{ number_format($acc->current_balance, 2) }} SAR)')"
                                             data-value="{{ $acc->id }}"
                                             data-search="{{ strtolower($acc->name_en . ' ' . $acc->name_ar . ' ' . $acc->type) }}"
                                             class="bz-option-item flex items-center justify-between gap-2.5 px-3 py-2 rounded-xl text-xs cursor-pointer text-slate-700 dark:text-slate-200 hover:bg-sky-50/70 dark:hover:bg-sky-950/40 hover:text-sky-900 dark:hover:text-sky-200 transition-colors {{ $loop->first ? 'bg-sky-50 dark:bg-sky-950/40 font-bold' : '' }}">
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
                                                <i class="fa-solid fa-check text-[#0A4F78] dark:text-sky-400 text-xs {{ $loop->first ? '' : 'hidden' }} bz-option-check"></i>
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

                <!-- 8. Cheque Details Container (Hidden by default) -->
                <div id="modalChequeFields" class="hidden p-3.5 rounded-xl bg-purple-500/10 border border-purple-500/30 space-y-2.5">
                    <div class="text-xs font-bold text-purple-800 dark:text-purple-300 flex items-center gap-1.5">
                        <i class="fa-solid fa-money-check-dollar"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'بيانات الشيك البنكي' : 'Cheque Information' }}</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                        <div>
                            <label class="text-xs font-bold text-slate-700 dark:text-slate-300 block mb-1">رقم الشيك / Cheque No.</label>
                            <input type="text" name="cheque_number" placeholder="CHQ-99410" class="form-control text-xs tabular-nums font-bold text-slate-900 dark:text-white bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-700 dark:text-slate-300 block mb-1">البنك المسحوب عليه / Bank</label>
                            <input type="text" name="cheque_bank" placeholder="e.g. Al Rajhi Bank" class="form-control text-xs font-medium text-slate-900 dark:text-white bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-700 dark:text-slate-300 block mb-1">تاريخ استحقاق الشيك / Due Date</label>
                            <input type="date" name="cheque_due_date" class="form-control text-xs tabular-nums font-medium text-slate-900 dark:text-white bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-700">
                        </div>
                    </div>
                </div>

                <!-- Modal Actions -->
                <div class="pt-3 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="closeCollectionModal()" class="btn btn-secondary text-xs font-bold py-2.5 px-4 text-slate-700 dark:text-slate-200 border-slate-300 dark:border-slate-700">
                        {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                    </button>
                    <button type="submit" class="btn text-xs font-black py-2.5 px-5 text-white shadow-md flex items-center gap-1.5" style="background: #0A4F78; border-color: #0A4F78;">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'حفظ وترحيل السند' : 'Post Collection Entry' }}</span>
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

    <!-- Client-side Calculation & Modal Scripts -->
    <script>
        // Universal Searchable Dropdown Controller
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
                trigger.classList.add('ring-2', 'ring-sky-500/20', 'border-sky-500');
                this.openId = id;

                // Reset search & focus
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
                if (trigger) trigger.classList.remove('ring-2', 'ring-sky-500/20', 'border-sky-500');
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
                        item.classList.add('bg-sky-50', 'dark:bg-sky-950/40', 'font-bold');
                        if (check) check.classList.remove('hidden');
                    } else {
                        item.classList.remove('bg-sky-50', 'dark:bg-sky-950/40', 'font-bold');
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

        // Dismiss listeners
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

        function openCollectionModal() {
            document.getElementById('newCollectionModal').classList.remove('hidden');
        }

        function closeCollectionModal() {
            document.getElementById('newCollectionModal').classList.add('hidden');
        }

        function switchSourceType(type) {
            document.getElementById('modalSourceType').value = type;

            const btnCustomer = document.getElementById('btnSourceCustomer');
            const btnSupplier = document.getElementById('btnSourceSupplier');
            const btnAsset = document.getElementById('btnSourceAsset');

            const custContainer = document.getElementById('customerSelectorContainer');
            const suppContainer = document.getElementById('supplierSelectorContainer');
            const categorySelect = document.getElementById('modalCategorySelect');

            const activeClass = 'bg-white dark:bg-slate-700 text-[#0A4F78] dark:text-sky-300 shadow-sm border border-slate-200 dark:border-slate-600 font-black';
            const inactiveClass = 'text-slate-600 dark:text-slate-300 hover:bg-white/60 dark:hover:bg-slate-700/60 font-bold';

            // Reset buttons
            [btnCustomer, btnSupplier, btnAsset].forEach(b => {
                b.className = 'py-2 px-3 text-xs rounded-lg transition flex items-center justify-center gap-1.5 ' + inactiveClass;
            });

            if (type === 'customer') {
                btnCustomer.className = 'py-2 px-3 text-xs rounded-lg transition flex items-center justify-center gap-1.5 ' + activeClass;
                custContainer.classList.remove('hidden');
                suppContainer.classList.add('hidden');
                categorySelect.value = 'sales';
                BzDropdown.updateTrigger('modalCategorySelect', '{{ app()->getLocale() === "ar" ? "مبيعات تجارية (sales)" : "sales" }}', true);
                BzDropdown.markActiveOption('modalCategorySelect', 'sales');
                handleCategoryChange('sales');
            } else if (type === 'supplier') {
                btnSupplier.className = 'py-2 px-3 text-xs rounded-lg transition flex items-center justify-center gap-1.5 ' + activeClass;
                custContainer.classList.add('hidden');
                suppContainer.classList.remove('hidden');
                categorySelect.value = 'supplier_refund';
                BzDropdown.updateTrigger('modalCategorySelect', '{{ app()->getLocale() === "ar" ? "استرداد / تسوية مورد (supplier refund)" : "supplier refund / credit" }}', true);
                BzDropdown.markActiveOption('modalCategorySelect', 'supplier_refund');
                handleCategoryChange('supplier_refund');
            } else {
                btnAsset.className = 'py-2 px-3 text-xs rounded-lg transition flex items-center justify-center gap-1.5 ' + activeClass;
                custContainer.classList.add('hidden');
                suppContainer.classList.add('hidden');
                categorySelect.value = 'assets_disposal';
                BzDropdown.updateTrigger('modalCategorySelect', '{{ app()->getLocale() === "ar" ? "تحصيل بيع أصول (assets disposal)" : "asset sale / disposal" }}', true);
                BzDropdown.markActiveOption('modalCategorySelect', 'assets_disposal');
                handleCategoryChange('assets_disposal');
                document.getElementById('modalCustomerCode').value = 'ASSET-EQ';
                document.getElementById('modalCustomerSource').value = '';
            }
        }

        function onCustomerChange(select) {
            const opt = select.options[select.selectedIndex];
            if (!opt || !opt.value) return;

            const name = opt.getAttribute('data-name') || '';
            const code = opt.getAttribute('data-code') || '';

            BzDropdown.updateTrigger('modalCustomerSelect', opt.text, Boolean(opt.value));
            BzDropdown.markActiveOption('modalCustomerSelect', opt.value);

            document.getElementById('modalCustomerSource').value = name;
            document.getElementById('modalCustomerCode').value = code;
        }

        function onSupplierChange(select) {
            const opt = select.options[select.selectedIndex];
            if (!opt || !opt.value) return;

            const name = opt.getAttribute('data-name') || '';
            const code = opt.getAttribute('data-code') || '';

            BzDropdown.updateTrigger('modalSupplierSelect', opt.text, Boolean(opt.value));
            BzDropdown.markActiveOption('modalSupplierSelect', opt.value);

            document.getElementById('modalCustomerSource').value = name;
            document.getElementById('modalCustomerCode').value = code;
        }

        function onInvoiceChange(select) {
            const opt = select.options[select.selectedIndex];
            if (!opt || !opt.value) return;

            const invNo = opt.getAttribute('data-invoice-number') || '';
            const custId = opt.getAttribute('data-customer-id') || '';
            const custName = opt.getAttribute('data-customer-name') || '';
            const subtotal = parseFloat(opt.getAttribute('data-subtotal')) || 0;
            const tax = parseFloat(opt.getAttribute('data-tax')) || 0;
            const total = parseFloat(opt.getAttribute('data-total')) || 0;

            // Auto-fill Description
            if (invNo) {
                document.getElementById('modalDescription').value = 'Invoice #' + invNo;
            }

            // Auto-fill customer if available
            if (custId) {
                const custSelect = document.getElementById('modalCustomerSelect');
                if (custSelect) {
                    custSelect.value = custId;
                    onCustomerChange(custSelect);
                }
            } else if (custName) {
                document.getElementById('modalCustomerSource').value = custName;
            }

            // Auto-fill amounts
            document.getElementById('modalAmountExcl').value = subtotal.toFixed(2);
            document.getElementById('modalVatAmount').value = tax.toFixed(2);
            document.getElementById('modalGrossAmount').value = total.toFixed(2);
        }

        function handleCategoryChange(category) {
            const vatRateInput = document.getElementById('modalVatRate');
            if (category === 'equities' || category === 'non_operational_revenue') {
                vatRateInput.value = 0;
            } else {
                vatRateInput.value = 15;
            }
            calculateFromGross();
        }

        function handlePaymentMethodChange(method) {
            const chequeDiv = document.getElementById('modalChequeFields');
            if (method === 'cheque') {
                chequeDiv.classList.remove('hidden');
            } else {
                chequeDiv.classList.add('hidden');
            }
        }

        function calculateFromGross() {
            const grossInput = document.getElementById('modalGrossAmount');
            const exclInput = document.getElementById('modalAmountExcl');
            const vatInput = document.getElementById('modalVatAmount');
            const rateInput = document.getElementById('modalVatRate');

            const gross = parseFloat(grossInput.value) || 0;
            const rate = parseFloat(rateInput.value) || 0;

            if (gross <= 0) {
                exclInput.value = '';
                vatInput.value = '0.00';
                return;
            }

            if (rate > 0) {
                const excl = (gross / (1 + (rate / 100)));
                const vat = gross - excl;
                exclInput.value = excl.toFixed(2);
                vatInput.value = vat.toFixed(2);
            } else {
                exclInput.value = gross.toFixed(2);
                vatInput.value = '0.00';
            }
        }

        function calculateFromExcl() {
            const grossInput = document.getElementById('modalGrossAmount');
            const exclInput = document.getElementById('modalAmountExcl');
            const vatInput = document.getElementById('modalVatAmount');
            const rateInput = document.getElementById('modalVatRate');

            const excl = parseFloat(exclInput.value) || 0;
            const rate = parseFloat(rateInput.value) || 0;

            if (excl <= 0) {
                grossInput.value = '';
                vatInput.value = '0.00';
                return;
            }

            const vat = (excl * (rate / 100));
            const gross = excl + vat;
            vatInput.value = vat.toFixed(2);
            grossInput.value = gross.toFixed(2);
        }

        // Close modal on Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeCollectionModal();
        });

        /* ==========================================================================
           Interactive Lazy Loading & Column Sorting Engine
           ========================================================================== */
        const lazyState = {
            currentPage: {{ $collections->currentPage() }},
            lastPage: {{ $collections->lastPage() }},
            hasMore: {{ $collections->hasMorePages() ? 'true' : 'false' }},
            total: {{ $collections->total() }},
            perPage: {{ $perPage }},
            initialLoadedCount: {{ $collections->count() }},
            currentCount: {{ $collections->count() }},
            isLoading: false,
            autoLoadOnScroll: false,
            sortBy: '{{ $sortBy }}',
            sortDir: '{{ $sortDir }}',
            filterParams: {
                search: @json($search),
                category: @json($category),
                account_id: @json($accountId),
                date_from: @json($dateFrom),
                date_to: @json($dateTo),
                per_page: {{ $perPage }}
            }
        };

        let scrollObserver = null;

        /**
         * Trigger server-side column sorting while preserving all active filters.
         */
        function triggerSort(column) {
            let newDir = 'asc';
            if (lazyState.sortBy === column) {
                newDir = (lazyState.sortDir === 'asc' ? 'desc' : 'asc');
            } else {
                // Dates and financial amounts default to desc for intuitive ERP view
                const descByDefault = ['entry_date', 'date', 'amount_excl_vat', 'vat_amount', 'gross_amount', 'id'];
                newDir = descByDefault.includes(column) ? 'desc' : 'asc';
            }

            const url = new URL(window.location.origin + window.location.pathname);
            if (lazyState.filterParams.search) url.searchParams.set('search', lazyState.filterParams.search);
            if (lazyState.filterParams.category) url.searchParams.set('category', lazyState.filterParams.category);
            if (lazyState.filterParams.account_id) url.searchParams.set('account_id', lazyState.filterParams.account_id);
            if (lazyState.filterParams.date_from) url.searchParams.set('date_from', lazyState.filterParams.date_from);
            if (lazyState.filterParams.date_to) url.searchParams.set('date_to', lazyState.filterParams.date_to);
            url.searchParams.set('per_page', lazyState.perPage);
            url.searchParams.set('sort_by', column);
            url.searchParams.set('sort_dir', newDir);

            window.location.href = url.toString();
        }

        /**
         * Change per-page batch size.
         */
        function changePerPage(size) {
            const url = new URL(window.location.href);
            url.searchParams.set('per_page', size);
            url.searchParams.delete('page');
            window.location.href = url.toString();
        }

        /**
         * Lazy Load next batch of entries (See More).
         */
        async function lazyLoadMore() {
            if (lazyState.isLoading || !lazyState.hasMore) return;

            lazyState.isLoading = true;
            const btnMore = document.getElementById('btnSeeMore');
            const btnMoreLabel = document.getElementById('btnSeeMoreLabel');
            const originalHtml = btnMoreLabel ? btnMoreLabel.innerHTML : '';

            if (btnMore) {
                btnMore.disabled = true;
                btnMore.classList.add('opacity-75');
                if (btnMoreLabel) {
                    btnMoreLabel.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin text-xs"></i> {{ app()->getLocale() === 'ar' ? 'جارٍ تحميل السجلات...' : 'Loading records...' }}';
                }
            }

            // Append shimmer skeleton rows
            const tableBody = document.getElementById('collectionTableBody');
            const skeletonRowId = 'skeletonLoadingRow';
            const skeletonHtml = `
                <tr id="${skeletonRowId}" class="animate-pulse bg-sky-50/30 dark:bg-slate-800/30 border-b border-slate-200 dark:border-slate-800">
                    <td colspan="11" class="py-4 px-4 text-center">
                        <div class="flex items-center justify-center gap-3 text-slate-500 dark:text-slate-400 text-xs font-bold">
                            <span class="w-4 h-4 rounded-full border-2 border-[#0A4F78] border-t-transparent animate-spin"></span>
                            <span>{{ app()->getLocale() === 'ar' ? 'جارٍ جلب وتحميل الدفعة التالية من قيود التحصيل...' : 'Fetching and rendering next batch of collection entries...' }}</span>
                        </div>
                    </td>
                </tr>
            `;
            tableBody.insertAdjacentHTML('beforeend', skeletonHtml);

            try {
                const nextPage = lazyState.currentPage + 1;
                const url = new URL('{{ route("admin.finance.collections.index") }}');
                if (lazyState.filterParams.search) url.searchParams.set('search', lazyState.filterParams.search);
                if (lazyState.filterParams.category) url.searchParams.set('category', lazyState.filterParams.category);
                if (lazyState.filterParams.account_id) url.searchParams.set('account_id', lazyState.filterParams.account_id);
                if (lazyState.filterParams.date_from) url.searchParams.set('date_from', lazyState.filterParams.date_from);
                if (lazyState.filterParams.date_to) url.searchParams.set('date_to', lazyState.filterParams.date_to);
                url.searchParams.set('sort_by', lazyState.sortBy);
                url.searchParams.set('sort_dir', lazyState.sortDir);
                url.searchParams.set('per_page', lazyState.perPage);
                url.searchParams.set('page', nextPage);
                url.searchParams.set('format', 'json');

                const response = await fetch(url.toString(), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const data = await response.json();

                // Remove skeleton
                const skeleton = document.getElementById(skeletonRowId);
                if (skeleton) skeleton.remove();

                if (data.success && data.html) {
                    // Remove empty message if any
                    const emptyRow = document.getElementById('emptyTableRow');
                    if (emptyRow) emptyRow.remove();

                    // Insert rows
                    tableBody.insertAdjacentHTML('beforeend', data.html);

                    lazyState.currentPage = data.currentPage;
                    lazyState.hasMore = data.hasMorePages;
                    lazyState.currentCount += data.count;

                    // Update UI state
                    updateProgressUI();
                    recalculateTotals();

                    // Show "See Less" button
                    const btnLess = document.getElementById('btnSeeLess');
                    if (btnLess && lazyState.currentCount > lazyState.initialLoadedCount) {
                        btnLess.classList.remove('hidden');
                        btnLess.classList.add('inline-flex');
                    }

                    // Handle "See More" and "All Loaded" badges
                    if (!data.hasMorePages) {
                        if (btnMore) {
                            btnMore.classList.add('hidden');
                            btnMore.classList.remove('inline-flex');
                        }
                        const allLoadedNotice = document.getElementById('allLoadedNotice');
                        if (allLoadedNotice) {
                            allLoadedNotice.classList.remove('hidden');
                            allLoadedNotice.classList.add('inline-flex');
                        }
                    }
                }
            } catch (err) {
                console.error('Lazy loading error:', err);
                const skeleton = document.getElementById(skeletonRowId);
                if (skeleton) skeleton.remove();
            } finally {
                lazyState.isLoading = false;
                if (btnMore) {
                    btnMore.disabled = false;
                    btnMore.classList.remove('opacity-75');
                    if (btnMoreLabel) {
                        btnMoreLabel.innerHTML = originalHtml;
                    }
                }
            }
        }

        /**
         * See Less: Fold back to initial batch size and scroll up.
         */
        function lazyShowLess() {
            const tableBody = document.getElementById('collectionTableBody');
            const allRows = tableBody.querySelectorAll('.collection-row');

            if (allRows.length > lazyState.initialLoadedCount) {
                // Remove all rows past initial batch
                for (let i = lazyState.initialLoadedCount; i < allRows.length; i++) {
                    allRows[i].remove();
                }

                lazyState.currentPage = 1;
                lazyState.hasMore = (lazyState.total > lazyState.initialLoadedCount);
                lazyState.currentCount = lazyState.initialLoadedCount;

                // Update UI state
                updateProgressUI();
                recalculateTotals();

                // Hide "See Less"
                const btnLess = document.getElementById('btnSeeLess');
                if (btnLess) {
                    btnLess.classList.add('hidden');
                    btnLess.classList.remove('inline-flex');
                }

                // Show "See More" if more exist
                const btnMore = document.getElementById('btnSeeMore');
                if (btnMore && lazyState.hasMore) {
                    btnMore.classList.remove('hidden');
                    btnMore.classList.add('inline-flex');
                }

                // Hide "All Loaded" badge if more exist
                const allLoadedNotice = document.getElementById('allLoadedNotice');
                if (allLoadedNotice && lazyState.hasMore) {
                    allLoadedNotice.classList.add('hidden');
                    allLoadedNotice.classList.remove('inline-flex');
                }

                // Smooth scroll back to ledger top
                const ledgerCard = document.getElementById('collectionLedgerCard');
                if (ledgerCard) {
                    ledgerCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        }

        /**
         * Update UI progress bar and counters.
         */
        function updateProgressUI() {
            const dispShowing = document.getElementById('displayShowingCount');
            const lblShowing = document.getElementById('lblShowingCount');
            const lblTotal = document.getElementById('lblTotalCount');
            const lblPercent = document.getElementById('lblPercentage');
            const progressBar = document.getElementById('hubProgressBar');

            if (dispShowing) dispShowing.textContent = lazyState.currentCount;
            if (lblShowing) lblShowing.textContent = lazyState.currentCount;
            if (lblTotal) lblTotal.textContent = lazyState.total;

            const percentage = lazyState.total > 0
                ? Math.min(100, Math.round((lazyState.currentCount / lazyState.total) * 100))
                : 100;

            if (lblPercent) lblPercent.textContent = percentage + '%';
            if (progressBar) progressBar.style.width = percentage + '%';
        }

        /**
         * Recalculate and update loaded totals in the table footer.
         */
        function recalculateTotals() {
            const tableBody = document.getElementById('collectionTableBody');
            const rows = tableBody.querySelectorAll('.collection-row');

            let sumExcl = 0;
            let sumVat = 0;
            let sumGross = 0;

            rows.forEach(r => {
                sumExcl += parseFloat(r.getAttribute('data-excl')) || 0;
                sumVat += parseFloat(r.getAttribute('data-vat')) || 0;
                sumGross += parseFloat(r.getAttribute('data-gross')) || 0;
            });

            const formatNum = (val) => val.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            const rowCountEl = document.getElementById('footerRowCount');
            const exclEl = document.getElementById('footerExclTotal');
            const vatEl = document.getElementById('footerVatTotal');
            const grossEl = document.getElementById('footerGrossTotal');

            if (rowCountEl) rowCountEl.textContent = rows.length;
            if (exclEl) exclEl.textContent = formatNum(sumExcl);
            if (vatEl) vatEl.textContent = formatNum(sumVat);
            if (grossEl) grossEl.textContent = formatNum(sumGross);
        }

        /**
         * Toggle Automatic Infinite Lazy Loading on Scroll.
         */
        function toggleAutoScroll(enabled) {
            lazyState.autoLoadOnScroll = enabled;
            const sentinel = document.getElementById('lazySentinel');

            if (enabled) {
                if (!scrollObserver && 'IntersectionObserver' in window) {
                    scrollObserver = new IntersectionObserver((entries) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting && lazyState.autoLoadOnScroll && lazyState.hasMore && !lazyState.isLoading) {
                                lazyLoadMore();
                            }
                        });
                    }, { rootMargin: '200px' });
                }
                if (scrollObserver && sentinel) {
                    scrollObserver.observe(sentinel);
                }
            } else {
                if (scrollObserver && sentinel) {
                    scrollObserver.unobserve(sentinel);
                }
            }
        }
    </script>
</x-layouts.admin>
