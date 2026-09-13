<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'إدارة المستودعات والمخازن' : 'Warehouses & Storage Facilities'"
    :pageSubtitle="app()->getLocale() === 'ar' ? 'إدارة شاملة لشبكة المستودعات، الفروع، مراكز التوزيع اللوجستي ومتابعة أرصدتها الحية.' : 'Comprehensive network management for distribution hubs, warehouses, logistics depots, and live inventory.'"
    :breadcrumbs="[
        (app()->getLocale() === 'ar' ? 'المخزون' : 'Inventory') => route('admin.inventory.index'),
        (app()->getLocale() === 'ar' ? 'المستودعات والمخازن' : 'Warehouses') => route('admin.warehouses.index')
    ]"
>
    <x-slot name="actions">
        <a href="{{ route('admin.inventory.allocator') }}" class="btn btn-secondary font-semibold">
            <i class="fa-solid fa-network-wired text-sky-500 mr-1.5 ml-1.5"></i>
            {{ app()->getLocale() === 'ar' ? 'موزع المخزون المباشر' : 'Live Stock Allocator' }}
        </a>

        <button type="button" onclick="openCreateWarehouseModal()" class="btn btn-primary font-bold shadow-sm">
            <i class="fa-solid fa-plus mr-1.5 ml-1.5"></i>
            {{ app()->getLocale() === 'ar' ? 'إضافة مستودع جديد' : 'Add Storage Facility' }}
        </button>
    </x-slot>

    <div class="space-y-6">
        <!-- Flash Alerts -->
        @if(session('status'))
            <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-800 dark:text-emerald-300 text-sm flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400 text-lg"></i>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 text-rose-800 dark:text-rose-300 text-sm space-y-1">
                @foreach($errors->all() as $error)
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-exclamation text-rose-600 dark:text-rose-400"></i>
                        <span>{{ $error }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Network KPI Summary Row -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Total Facilities -->
            <div class="card p-5 shadow-sm border border-slate-200/80 dark:border-slate-800/80 transition-all hover:shadow-md">
                <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider">{{ app()->getLocale() === 'ar' ? 'إجمالي المستودعات' : 'Total Facilities' }}</span>
                    <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-warehouse text-sm"></i>
                    </div>
                </div>
                <div class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">{{ $kpis['total_warehouses'] }}</div>
                <div class="text-xs text-emerald-600 dark:text-emerald-400 mt-1 font-semibold flex items-center gap-1">
                    <i class="fa-solid fa-circle text-[8px]"></i>
                    <span>{{ $kpis['active_warehouses'] }} {{ app()->getLocale() === 'ar' ? 'نشط ويعمل' : 'Active Hubs' }}</span>
                </div>
            </div>

            <!-- Total Units Stocked -->
            <div class="card p-5 shadow-sm border border-slate-200/80 dark:border-slate-800/80 transition-all hover:shadow-md">
                <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider">{{ app()->getLocale() === 'ar' ? 'إجمالي وحدات المخزون' : 'Network Units' }}</span>
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-boxes-stacked text-sm"></i>
                    </div>
                </div>
                <div class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">{{ number_format($kpis['total_stock_units']) }}</div>
                <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    {{ app()->getLocale() === 'ar' ? 'موزعة عبر جميع المنشآت' : 'Across all facilities' }}
                </div>
            </div>

            <!-- Total Stock Valuation -->
            <div class="card p-5 shadow-sm border border-slate-200/80 dark:border-slate-800/80 transition-all hover:shadow-md">
                <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider">{{ app()->getLocale() === 'ar' ? 'القيمة التقديرية للمخزون' : 'Stock Valuation' }}</span>
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-dollar-sign text-sm"></i>
                    </div>
                </div>
                <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 tracking-tight">${{ number_format($kpis['total_stock_valuation'], 2) }}</div>
                <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    {{ app()->getLocale() === 'ar' ? 'على أساس سعر التكلفة' : 'Acquisition cost basis' }}
                </div>
            </div>

            <!-- Core Hubs -->
            <div class="card p-5 shadow-sm border border-slate-200/80 dark:border-slate-800/80 transition-all hover:shadow-md">
                <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider">{{ app()->getLocale() === 'ar' ? 'المستودعات الأساسية' : 'System Core Hubs' }}</span>
                    <div class="w-8 h-8 rounded-lg bg-cyan-50 dark:bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 border border-cyan-200 dark:border-cyan-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-shield-halved text-sm"></i>
                    </div>
                </div>
                <div class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">3</div>
                <div class="text-xs text-cyan-600 dark:text-cyan-400 mt-1 font-semibold">
                    {{ app()->getLocale() === 'ar' ? 'Online + POS + Central' : 'Online + POS + Central' }}
                </div>
            </div>

            <!-- Warnings / Alerts -->
            <div class="card p-5 shadow-sm border border-slate-200/80 dark:border-slate-800/80 transition-all hover:shadow-md">
                <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider">{{ app()->getLocale() === 'ar' ? 'مستودعات بها تنبيهات' : 'Facilities w/ Alerts' }}</span>
                    <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-triangle-exclamation text-sm"></i>
                    </div>
                </div>
                <div class="text-2xl font-black {{ $kpis['low_stock_facilities'] > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-slate-700 dark:text-slate-300' }} tracking-tight">{{ $kpis['low_stock_facilities'] }}</div>
                <div class="text-xs text-amber-600 dark:text-amber-400 mt-1 font-medium">
                    {{ app()->getLocale() === 'ar' ? 'أصناف بحاجة لإعادة التوريد' : 'Low stock items present' }}
                </div>
            </div>
        </div>

        <!-- Filters & Search Toolbar -->
        <div class="card p-4 shadow-sm border border-slate-200/80 dark:border-slate-800/80">
            <form method="GET" action="{{ route('admin.warehouses.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <!-- Search Input -->
                <div class="search-wrapper w-full">
                    <i class="fa-solid fa-magnifying-glass search-icon text-slate-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" 
                        placeholder="{{ app()->getLocale() === 'ar' ? 'بحث بالاسم، الرمز، المدينة، المدير...' : 'Search name, code, city, manager...' }}"
                        class="form-control search-input text-sm w-full">
                </div>

                <!-- Type Filter -->
                <div>
                    <select name="type" onchange="this.form.submit()" class="form-select text-sm w-full">
                        <option value="all">{{ app()->getLocale() === 'ar' ? 'جميع أنواع المستودعات' : 'All Facility Types' }}</option>
                        @foreach($types as $key => $label)
                            <option value="{{ $key }}" {{ request('type') === $key ? 'selected' : '' }}>
                                {{ $label[app()->getLocale()] ?? $label['en'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <select name="status" onchange="this.form.submit()" class="form-select text-sm w-full">
                        <option value="all">{{ app()->getLocale() === 'ar' ? 'جميع الحالات' : 'All Statuses' }}</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'نشط فقط' : 'Active Only' }}</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'معطل فقط' : 'Inactive Only' }}</option>
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center gap-2">
                    <button type="submit" class="btn btn-secondary font-bold text-sm flex-1">
                        <i class="fa-solid fa-filter mr-1 ml-1"></i>
                        {{ app()->getLocale() === 'ar' ? 'تصفية' : 'Filter' }}
                    </button>

                    @if(request()->hasAny(['search', 'type', 'status']))
                        <a href="{{ route('admin.warehouses.index') }}" class="btn btn-ghost font-bold text-sm" title="{{ app()->getLocale() === 'ar' ? 'إعادة ضبط' : 'Reset' }}">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Facilities Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($warehouses as $wh)
                @php
                    $stockUnits = $wh->total_stock_units;
                    $capacity = max(1, $wh->capacity_units ?? 10000);
                    $utilization = min(100, round(($stockUnits / $capacity) * 100, 1));
                @endphp
                <div class="card p-6 shadow-sm border border-slate-200/80 dark:border-slate-800/80 flex flex-col justify-between transition-all duration-300 hover:shadow-lg hover:border-indigo-400/50 dark:hover:border-indigo-500/50 group relative overflow-hidden">
                    <!-- Top Gradient Line on Hover -->
                    <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-transparent via-indigo-500 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>

                    <div>
                        <!-- Top Row: Type Badge & Status -->
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $wh->type_badge_class }}">
                                {{ $wh->type_label }}
                            </span>

                            <div class="flex items-center gap-2">
                                @if($wh->is_system_core)
                                    <span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-cyan-700 dark:text-cyan-400 border border-slate-200 dark:border-slate-700 text-[11px] font-mono font-bold" title="{{ app()->getLocale() === 'ar' ? 'مستودع أساسي للنظام' : 'Core system hub' }}">
                                        ⚡ CORE
                                    </span>
                                @endif

                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $wh->is_active ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20' : 'bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-500/20' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $wh->is_active ? 'bg-emerald-500 animate-pulse' : 'bg-rose-500' }}"></span>
                                    {{ $wh->is_active ? (app()->getLocale() === 'ar' ? 'نشط' : 'Active') : (app()->getLocale() === 'ar' ? 'معطل' : 'Inactive') }}
                                </span>
                            </div>
                        </div>

                        <!-- Facility Name & Code -->
                        <div class="mb-4">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                <a href="{{ route('admin.warehouses.show', $wh->id) }}" class="hover:underline">
                                    {{ $wh->name }}
                                </a>
                            </h3>
                            <div class="flex items-center gap-3 text-xs text-slate-500 dark:text-slate-400 mt-1 font-mono">
                                <span class="bg-slate-100 dark:bg-slate-950 px-2 py-0.5 rounded border border-slate-200 dark:border-slate-800 font-semibold text-slate-700 dark:text-slate-300">{{ $wh->code }}</span>
                                <span>ID: <code class="text-slate-600 dark:text-slate-400">{{ $wh->id }}</code></span>
                            </div>
                        </div>

                        <!-- Location & Contact Details -->
                        <div class="space-y-1.5 text-xs text-slate-600 dark:text-slate-300 bg-slate-50 dark:bg-slate-950/40 rounded-xl p-3 border border-slate-200/80 dark:border-slate-800/60 mb-5">
                            <div class="flex items-center gap-2">
                                <span class="text-sm">{{ $wh->country?->flag_emoji ?: '📍' }}</span>
                                <span class="truncate font-medium">
                                    <strong class="text-slate-800 dark:text-slate-200">{{ $wh->display_location }}</strong>
                                    @if($wh->address)
                                        <span class="text-slate-400 text-[11px]">— {{ $wh->address }}</span>
                                    @endif
                                </span>
                            </div>

                            @if($wh->manager_name || $wh->phone)
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-user-tie text-slate-400 flex-shrink-0"></i>
                                    <span class="truncate">{{ $wh->manager_name ?? 'Operations Lead' }} {{ $wh->phone ? "({$wh->phone_with_code})" : '' }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Capacity & Storage Gauge -->
                        <div class="space-y-2 mb-5">
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-slate-500 dark:text-slate-400 font-medium">{{ app()->getLocale() === 'ar' ? 'نسبة إشغال السعة' : 'Capacity Utilization' }}</span>
                                <span class="font-mono font-bold {{ $utilization > 85 ? 'text-rose-600 dark:text-rose-400' : ($utilization > 60 ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400') }}">
                                    {{ $utilization }}% ({{ number_format($stockUnits) }} / {{ number_format($capacity) }} {{ app()->getLocale() === 'ar' ? 'وحدة' : 'units' }})
                                </span>
                            </div>
                            <div class="w-full bg-slate-100 dark:bg-slate-950 rounded-full h-2 overflow-hidden border border-slate-200 dark:border-slate-800">
                                <div class="h-full rounded-full transition-all duration-500 {{ $utilization > 85 ? 'bg-rose-500' : ($utilization > 60 ? 'bg-amber-500' : 'bg-gradient-to-r from-blue-500 to-indigo-500') }}"
                                     style="width: {{ $utilization }}%"></div>
                            </div>
                        </div>

                        <!-- Key Metrics Pill Grid -->
                        <div class="grid grid-cols-2 gap-2 text-xs mb-6">
                            <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-950/60 border border-slate-200/80 dark:border-slate-800/80">
                                <div class="text-slate-500 dark:text-slate-400">{{ app()->getLocale() === 'ar' ? 'أصناف متوفرة' : 'Active Formulations' }}</div>
                                <div class="font-bold text-slate-900 dark:text-white mt-0.5 text-sm">{{ $wh->active_skus_count }} {{ app()->getLocale() === 'ar' ? 'صنف' : 'SKUs' }}</div>
                            </div>
                            <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-950/60 border border-slate-200/80 dark:border-slate-800/80">
                                <div class="text-slate-500 dark:text-slate-400">{{ app()->getLocale() === 'ar' ? 'قيمة مخزون المنشأة' : 'Facility Valuation' }}</div>
                                <div class="font-bold text-emerald-600 dark:text-emerald-400 mt-0.5 text-sm">${{ number_format($wh->total_valuation, 2) }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Action Toolbar -->
                    <div class="pt-4 border-t border-slate-200/80 dark:border-slate-800/80 flex items-center justify-between gap-2">
                        <a href="{{ route('admin.warehouses.show', $wh->id) }}" class="btn btn-secondary btn-sm flex-1 font-bold text-xs flex items-center justify-center gap-1.5">
                            <i class="fa-solid fa-eye text-indigo-500"></i>
                            {{ app()->getLocale() === 'ar' ? 'عرض الأصناف' : 'View Stock' }}
                        </a>

                        <a href="{{ route('admin.warehouses.edit', $wh->id) }}" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition-colors" title="{{ app()->getLocale() === 'ar' ? 'تعديل' : 'Edit' }}">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>

                        <!-- Toggle Status -->
                        <form method="POST" action="{{ route('admin.warehouses.toggle-status', $wh->id) }}">
                            @csrf
                            <button type="submit" class="p-2 rounded-xl {{ $wh->is_active ? 'bg-amber-50 hover:bg-amber-100 dark:bg-amber-500/10 dark:hover:bg-amber-500/20 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-500/20' : 'bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-500/10 dark:hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20' }} transition-colors" title="{{ $wh->is_active ? (app()->getLocale() === 'ar' ? 'تعطيل المستودع' : 'Deactivate') : (app()->getLocale() === 'ar' ? 'تفعيل المستودع' : 'Activate') }}">
                                <i class="fa-solid fa-power-off"></i>
                            </button>
                        </form>

                        <!-- Delete Button (Only for non-core hubs with 0 stock) -->
                        @if(!$wh->is_system_core)
                            <form method="POST" action="{{ route('admin.warehouses.destroy', $wh->id) }}" onsubmit="return confirm('{{ app()->getLocale() === 'ar' ? 'هل أنت متأكد من رغبتك في حذف هذا المستودع؟' : 'Are you sure you want to delete this warehouse?' }}');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 rounded-xl bg-rose-50 hover:bg-rose-100 dark:bg-rose-500/10 dark:hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-500/20 transition-colors" title="{{ app()->getLocale() === 'ar' ? 'حذف' : 'Delete' }}">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center card bg-slate-50 dark:bg-slate-900/40 border border-slate-200 dark:border-slate-800">
                    <i class="fa-solid fa-warehouse text-4xl text-slate-400 dark:text-slate-600 mx-auto mb-3"></i>
                    <h4 class="text-slate-900 dark:text-white font-bold text-base">{{ app()->getLocale() === 'ar' ? 'لا توجد مستودعات مطابقة للبحث' : 'No storage facilities found' }}</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ app()->getLocale() === 'ar' ? 'جرب تغيير خيارات التصفية أو أضف مستودعاً جديداً.' : 'Try adjusting your search criteria or add a new storage facility.' }}</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Create Warehouse Modal -->
    <div id="createWarehouseModal" class="fixed inset-0 z-[9999] bg-slate-950/75 backdrop-blur-md hidden p-4 sm:p-6 overflow-y-auto flex items-center justify-center min-h-screen">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-8 max-w-2xl w-full shadow-2xl space-y-6 relative my-auto animate-fadeIn" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-500/20 rounded-xl">
                        <i class="fa-solid fa-warehouse text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white">{{ app()->getLocale() === 'ar' ? 'إضافة منشأة أو مستودع جديد' : 'Add New Storage Facility' }}</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ app()->getLocale() === 'ar' ? 'سيتم فورياً تهيئة جميع المنتجات الحالية داخل هذا المستودع.' : 'All current catalog products will be initialized in this hub immediately.' }}</p>
                    </div>
                </div>

                <button onclick="closeCreateWarehouseModal()" class="w-9 h-9 flex items-center justify-center text-slate-400 hover:text-slate-700 dark:hover:text-white rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('admin.warehouses.store') }}" class="space-y-4">
                @csrf

                <!-- Bilingual Names -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            {{ app()->getLocale() === 'ar' ? 'اسم المستودع (بالإنجليزية)' : 'Warehouse Name (English)' }} <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="name_en" id="modalNameEn" required placeholder="e.g. Jeddah Logistics Depot"
                            class="form-control text-sm w-full" dir="ltr">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            {{ app()->getLocale() === 'ar' ? 'اسم المستودع (بالعربية)' : 'Warehouse Name (Arabic)' }} <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="name_ar" required placeholder="مثال: مستودع جدة اللوجستي"
                            class="form-control text-sm w-full" dir="rtl">
                    </div>
                </div>

                <!-- Code, Slug & Facility Type -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            {{ app()->getLocale() === 'ar' ? 'رمز المنشأة (Code)' : 'Facility Code' }}
                        </label>
                        <input type="text" name="code" placeholder="LOC-JED"
                            class="form-control text-sm w-full uppercase font-mono" dir="ltr">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            {{ app()->getLocale() === 'ar' ? 'المعرف الفريد (Slug ID)' : 'System Slug ID' }}
                        </label>
                        <input type="text" name="id" id="modalSlugId" placeholder="wh_jeddah"
                            class="form-control text-sm w-full font-mono" dir="ltr">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            {{ app()->getLocale() === 'ar' ? 'نوع المنشأة' : 'Facility Type' }} <span class="text-rose-500">*</span>
                        </label>
                        <select name="type" required class="form-select text-sm w-full">
                            <option value="warehouse">{{ app()->getLocale() === 'ar' ? 'مستودع لوجستي' : 'Logistics Warehouse' }}</option>
                            <option value="branch">{{ app()->getLocale() === 'ar' ? 'فرع إقليمي' : 'Regional Branch' }}</option>
                            <option value="offline">{{ app()->getLocale() === 'ar' ? 'مستودع / نقطة بيع POS' : 'Warehouse / POS' }}</option>
                            <option value="online">{{ app()->getLocale() === 'ar' ? 'مركز طلبات إلكترونية' : 'E-Commerce Hub' }}</option>
                        </select>
                    </div>
                </div>

                <!-- Country, City & Physical Address -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            {{ app()->getLocale() === 'ar' ? 'الدولة' : 'Country' }}
                        </label>
                        <select name="country_id" id="modalCountrySelect" class="form-select text-sm w-full" onchange="handleModalCountryChange(this.value)">
                            <option value="">{{ app()->getLocale() === 'ar' ? 'اختر الدولة...' : 'Select Country...' }}</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}" data-dial="{{ $country->phone_code }}">
                                    {{ $country->flag_emoji }} {{ $country->name_en }} ({{ $country->name_ar }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            {{ app()->getLocale() === 'ar' ? 'المدينة / المنطقة' : 'City / Zone' }}
                        </label>
                        <select name="city_id" id="modalCitySelect" class="form-select text-sm w-full">
                            <option value="">{{ app()->getLocale() === 'ar' ? 'اختر الدولة أولاً...' : 'Select Country First...' }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            {{ app()->getLocale() === 'ar' ? 'العنوان الجغرافي التفصيلي' : 'Physical Address' }}
                        </label>
                        <input type="text" name="address" placeholder="e.g. Building 12, Industrial Zone 2"
                            class="form-control text-sm w-full">
                    </div>
                </div>

                <!-- Manager, Phone & Capacity -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            {{ app()->getLocale() === 'ar' ? 'المشرف / مدير المستودع' : 'Facility Manager' }}
                        </label>
                        <input type="text" name="manager_name" placeholder="e.g. Tariq Mansoor"
                            class="form-control text-sm w-full">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            {{ app()->getLocale() === 'ar' ? 'هاتف التواصل' : 'Contact Phone' }}
                        </label>
                        <div class="relative flex items-center" dir="ltr">
                            <span id="modalPhonePrefixBadge" class="inline-flex items-center px-3 py-2.5 rounded-l-xl bg-slate-100 dark:bg-slate-800 border border-r-0 border-slate-200 dark:border-slate-700 text-xs font-mono font-bold text-indigo-600 dark:text-indigo-400">
                                +966
                            </span>
                            <input type="text" name="phone" id="modalPhoneInput" placeholder="50 123 4567"
                                class="form-control text-sm w-full rounded-l-none text-left font-mono">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            {{ app()->getLocale() === 'ar' ? 'السعة الاستيعابية (وحدة)' : 'Max Capacity (Units)' }}
                        </label>
                        <input type="number" name="capacity_units" value="10000" min="10" step="100"
                            class="form-control text-sm w-full font-mono" dir="ltr">
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        {{ app()->getLocale() === 'ar' ? 'ملاحظات تشغيلية' : 'Operational Notes' }}
                    </label>
                    <textarea name="notes" rows="2" placeholder="{{ app()->getLocale() === 'ar' ? 'ملاحظات عن درجات الحرارة، معايير التخزين...' : 'Temperature standards, access hours, security notes...' }}"
                        class="form-control text-sm w-full"></textarea>
                </div>

                <!-- Active Status -->
                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" name="is_active" id="modalIsActive" value="1" checked
                        class="form-check-input w-4 h-4">
                    <label for="modalIsActive" class="text-sm font-semibold text-slate-700 dark:text-slate-300 select-none cursor-pointer">
                        {{ app()->getLocale() === 'ar' ? 'تفعيل المنشأة فورياً لاستقبال وتوزيع الشحنات والمخزون' : 'Activate facility immediately for inventory distribution' }}
                    </label>
                </div>

                <!-- Buttons -->
                <div class="pt-4 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end gap-3">
                    <button type="button" onclick="closeCreateWarehouseModal()" class="px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-semibold text-sm hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-md shadow-indigo-600/20 transition-all flex items-center gap-2">
                        <i class="fa-solid fa-check text-xs"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'حفظ وإنشاء المستودع' : 'Save & Provision Hub' }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openCreateWarehouseModal() {
            document.getElementById('createWarehouseModal').classList.remove('hidden');
        }

        function closeCreateWarehouseModal() {
            document.getElementById('createWarehouseModal').classList.add('hidden');
        }

        // Dynamic Country -> City & Phone Code in Modal
        function handleModalCountryChange(countryId) {
            const citySelect = document.getElementById('modalCitySelect');
            const dialBadge = document.getElementById('modalPhonePrefixBadge');
            const countrySelect = document.getElementById('modalCountrySelect');

            if (!countryId) {
                citySelect.innerHTML = '<option value="">{{ app()->getLocale() === 'ar' ? 'اختر الدولة أولاً...' : 'Select Country First...' }}</option>';
                return;
            }

            const selectedOption = countrySelect.options[countrySelect.selectedIndex];
            const dialCode = selectedOption ? selectedOption.getAttribute('data-dial') : '+966';
            if (dialBadge && dialCode) {
                dialBadge.textContent = dialCode;
            }

            citySelect.innerHTML = '<option value="">{{ app()->getLocale() === 'ar' ? 'جاري تحميل المدن...' : 'Loading cities...' }}</option>';
            citySelect.disabled = true;

            fetch(`{{ url('admin/api/countries') }}/${countryId}/cities`)
                .then(res => res.json())
                .then(data => {
                    citySelect.innerHTML = '<option value="">{{ app()->getLocale() === 'ar' ? 'اختر المدينة...' : 'Select City...' }}</option>';
                    if (data.cities && data.cities.length > 0) {
                        data.cities.forEach(city => {
                            const opt = document.createElement('option');
                            opt.value = city.id;
                            opt.textContent = `{{ app()->getLocale() === 'ar' ? '${city.name_ar} (${city.name_en})' : '${city.name_en} (${city.name_ar})' }}`;
                            citySelect.appendChild(opt);
                        });
                    } else {
                        citySelect.innerHTML = '<option value="">{{ app()->getLocale() === 'ar' ? 'لا توجد مدن مضافة لهذه الدولة' : 'No cities found for this country' }}</option>';
                    }
                    citySelect.disabled = false;
                })
                .catch(err => {
                    console.error('Error fetching cities:', err);
                    citySelect.disabled = false;
                });
        }

        // Auto-slug generator
        document.getElementById('modalNameEn')?.addEventListener('input', function(e) {
            const slugInput = document.getElementById('modalSlugId');
            if (slugInput && !slugInput.dataset.manual) {
                slugInput.value = 'wh_' + e.target.value.toLowerCase().replace(/[^a-z0-9]/g, '_').replace(/_+/g, '_').replace(/^_|_$/g, '');
            }
        });

        document.getElementById('modalSlugId')?.addEventListener('input', function() {
            this.dataset.manual = 'true';
        });

        // Close on ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeCreateWarehouseModal();
        });
    </script>
</x-layouts.admin>
