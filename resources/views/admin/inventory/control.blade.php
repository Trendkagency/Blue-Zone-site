<x-layouts.admin :page-title="app()->getLocale() === 'ar' ? 'مركز التحكم الشامل بالمخزون والكميات' : 'Product Inventory Control & Quantity Hub'">
    <x-slot name="pageSubtitle">
        {{ app()->getLocale() === 'ar' 
            ? 'لوحة القيادة المركزية لإدارة وتعديل كميات المنتجات (زيادة / إنقاص) عبر المستودعات مع التدقيق المحاسبي الفوري.' 
            : 'Centralized command center to adjust, increase, or decrease product quantities across all hubs with real-time audit compliance.' }}
    </x-slot>

    <x-slot name="actions">
        <!-- Interactive "How to Use" Guide Button -->
        <button type="button" class="btn btn-outline btn-sm font-bold" onclick="openInventoryGuideModal()" style="display: flex; align-items: center; gap: 0.45rem; border-color: var(--color-primary); color: var(--color-primary);">
            <i class="fa-solid fa-circle-question" style="font-size: 1rem;"></i>
            <span>{{ app()->getLocale() === 'ar' ? 'دليل الاستخدام السريع' : 'How to Use Guide' }}</span>
        </button>

        <!-- Batch Action Trigger (Enabled when rows selected) -->
        <button type="button" class="btn btn-secondary btn-sm font-bold" id="btnBatchAction" onclick="openBatchAdjustModal()" style="display: none; align-items: center; gap: 0.45rem;">
            <i class="fa-solid fa-layer-group"></i>
            <span>{{ app()->getLocale() === 'ar' ? 'تعديل جماعي للمحدد (' : 'Batch Adjust (' }}<span id="selectedCountBadge">0</span>)</span>
        </button>

        <!-- Link to Visual Allocator -->
        <a href="{{ route('admin.inventory.allocator') }}" class="btn btn-outline btn-sm font-bold" style="display: flex; align-items: center; gap: 0.45rem;">
            <i class="fa-solid fa-network-wired text-sky-500"></i>
            <span>{{ app()->getLocale() === 'ar' ? 'موزع المخزون التفاعلي' : 'Visual Allocator' }}</span>
        </a>

        <!-- Link to History Ledger -->
        <a href="{{ route('admin.inventory.history') }}" class="btn btn-primary btn-sm font-bold" style="display: flex; align-items: center; gap: 0.45rem;">
            <i class="fa-solid fa-clock-rotate-left"></i>
            <span>{{ __('admin.menu.stock_history') }}</span>
        </a>
    </x-slot>

    <div style="display: flex; flex-direction: column; gap: 1.5rem;">

        <!-- 1. Live Interactive Metric KPI Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem;">
            <!-- Total Units in System -->
            <div class="card" style="padding: 1.25rem; border-radius: var(--radius-lg); background: #fff; border: 1px solid var(--color-border); border-inline-start: 4px solid var(--color-primary); box-shadow: 0 2px 8px rgba(10,17,40,0.04);">
                <div class="text-xs font-bold text-muted" style="text-transform: uppercase; margin-bottom: 0.35rem; display: flex; justify-content: space-between; align-items: center;">
                    <span>{{ app()->getLocale() === 'ar' ? 'إجمالي المخزون بالأسطول' : 'Total System Fleet Units' }}</span>
                    <i class="fa-solid fa-boxes-stacked text-primary" style="font-size: 1.05rem;"></i>
                </div>
                <div class="font-black text-2xl" id="kpiTotalUnits" style="color: var(--color-primary);">
                    {{ number_format($kpis['total_units']) }}
                </div>
                <div class="text-xs text-muted" style="margin-top: 0.25rem;">
                    {{ app()->getLocale() === 'ar' ? 'القيمة الإجمالية:' : 'Est. Valuation:' }} <strong id="kpiTotalValuation" class="text-primary font-mono">@currency($kpis['total_valuation'])</strong>
                </div>
            </div>

            <!-- Online Hub Stock -->
            <div class="card" style="padding: 1.25rem; border-radius: var(--radius-lg); background: #fff; border: 1px solid var(--color-border); border-inline-start: 4px solid #0284c7; box-shadow: 0 2px 8px rgba(10,17,40,0.04);">
                <div class="text-xs font-bold text-muted" style="text-transform: uppercase; margin-bottom: 0.35rem; display: flex; justify-content: space-between; align-items: center;">
                    <span>{{ app()->getLocale() === 'ar' ? 'المتجر الإلكتروني (Online)' : 'E-Commerce Online Hub' }}</span>
                    <i class="fa-solid fa-globe text-sky-500" style="font-size: 1.05rem;"></i>
                </div>
                <div class="font-black text-2xl text-sky-600" id="kpiOnlineUnits">
                    {{ number_format($kpis['online_units']) }}
                </div>
                <div class="text-xs text-muted" style="margin-top: 0.25rem;">
                    {{ app()->getLocale() === 'ar' ? 'مخصص للطلبات أونلاين' : 'Dedicated for web orders' }}
                </div>
            </div>

            <!-- POS Warehouse Floor Stock -->
            <div class="card" style="padding: 1.25rem; border-radius: var(--radius-lg); background: #fff; border: 1px solid var(--color-border); border-inline-start: 4px solid #10b981; box-shadow: 0 2px 8px rgba(10,17,40,0.04);">
                <div class="text-xs font-bold text-muted" style="text-transform: uppercase; margin-bottom: 0.35rem; display: flex; justify-content: space-between; align-items: center;">
                    <span>{{ app()->getLocale() === 'ar' ? 'مستودع المعرض (POS)' : 'POS Warehouse Hub' }}</span>
                    <i class="fa-solid fa-store text-emerald-500" style="font-size: 1.05rem;"></i>
                </div>
                <div class="font-black text-2xl text-emerald-600" id="kpiOfflineUnits">
                    {{ number_format($kpis['offline_units']) }}
                </div>
                <div class="text-xs text-muted" style="margin-top: 0.25rem;">
                    {{ app()->getLocale() === 'ar' ? 'جاهز للبيع بالكاشير' : 'Ready for POS checkout' }}
                </div>
            </div>

            <!-- Central Warehouse Buffer Reserve -->
            <div class="card" style="padding: 1.25rem; border-radius: var(--radius-lg); background: #fff; border: 1px solid var(--color-border); border-inline-start: 4px solid #8b5cf6; box-shadow: 0 2px 8px rgba(10,17,40,0.04);">
                <div class="text-xs font-bold text-muted" style="text-transform: uppercase; margin-bottom: 0.35rem; display: flex; justify-content: space-between; align-items: center;">
                    <span>{{ app()->getLocale() === 'ar' ? 'المستودع المركزي الاحتياطي' : 'Central Warehouse Buffer' }}</span>
                    <i class="fa-solid fa-warehouse text-purple-500" style="font-size: 1.05rem;"></i>
                </div>
                <div class="font-black text-2xl text-purple-600" id="kpiCentralUnits">
                    {{ number_format($kpis['central_units']) }}
                </div>
                <div class="text-xs text-muted" style="margin-top: 0.25rem;">
                    {{ app()->getLocale() === 'ar' ? 'احتياطي الإمداد الاستراتيجي' : 'Strategic reserve buffer' }}
                </div>
            </div>

            <!-- Stock Alerts -->
            <div class="card" style="padding: 1.25rem; border-radius: var(--radius-lg); background: #fff; border: 1px solid var(--color-border); border-inline-start: 4px solid #f59e0b; box-shadow: 0 2px 8px rgba(10,17,40,0.04);">
                <div class="text-xs font-bold text-muted" style="text-transform: uppercase; margin-bottom: 0.35rem; display: flex; justify-content: space-between; align-items: center;">
                    <span>{{ app()->getLocale() === 'ar' ? 'تنبيهات النواقص والنفاذ' : 'Health Alerts' }}</span>
                    <i class="fa-solid fa-triangle-exclamation text-amber-500" style="font-size: 1.05rem;"></i>
                </div>
                <div style="display: flex; gap: 0.75rem; align-items: center;">
                    <div>
                        <span class="font-black text-xl text-amber-600">{{ $kpis['low_stock_count'] }}</span>
                        <span class="text-xs text-muted" style="display: block;">{{ app()->getLocale() === 'ar' ? 'منخفض' : 'Low' }}</span>
                    </div>
                    <div style="width: 1px; height: 24px; background: var(--color-border);"></div>
                    <div>
                        <span class="font-black text-xl text-rose-600">{{ $kpis['out_of_stock_count'] }}</span>
                        <span class="text-xs text-muted" style="display: block;">{{ app()->getLocale() === 'ar' ? 'نافد' : 'Out' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Real-Time Search, Filter & Quick Action Control Bar -->
        <div class="card" style="padding: 1.25rem 1.5rem; background: #fff; border-radius: var(--radius-lg); border: 1px solid var(--color-border); box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
            <div style="display: flex; flex-wrap: wrap; gap: 1rem; justify-content: space-between; align-items: center;">
                
                <!-- Search Input with Clear Button -->
                <div style="position: relative; flex: 1; min-width: 260px; max-width: 420px;">
                    <i class="fa-solid fa-magnifying-glass" style="position: absolute; inset-inline-start: 1rem; top: 50%; transform: translateY(-50%); color: var(--color-text-muted); font-size: 0.9rem;"></i>
                    <input type="text" id="inventorySearchInput" class="form-control" placeholder="{{ app()->getLocale() === 'ar' ? 'ابحث باسم المنتج، SKU، أو الباركود...' : 'Search by product name, SKU, or barcode...' }}" style="padding-inline-start: 2.5rem; padding-inline-end: 2rem; border-radius: 25px; font-size: 0.88rem;" oninput="handleSearchFilter()">
                    <button type="button" id="clearSearchBtn" onclick="clearSearchInput()" style="display: none; position: absolute; inset-inline-end: 0.75rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--color-text-muted); cursor: pointer;">✕</button>
                </div>

                <!-- Filters: Category & Health Status -->
                <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center;">
                    <!-- Category Filter -->
                    <select id="categoryFilter" class="form-control text-xs font-bold" style="width: auto; min-width: 160px; border-radius: 20px; padding: 0.45rem 1rem;" onchange="handleSearchFilter()">
                        <option value="all">{{ app()->getLocale() === 'ar' ? 'جميع التصنيفات' : 'All Categories' }}</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ $selectedCategory == $cat->id ? 'selected' : '' }}>
                                {{ app()->getLocale() === 'ar' ? ($cat->name_ar ?? $cat->name_en) : $cat->name_en }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Stock Health Status Filter -->
                    <select id="statusFilter" class="form-control text-xs font-bold" style="width: auto; min-width: 150px; border-radius: 20px; padding: 0.45rem 1rem;" onchange="handleSearchFilter()">
                        <option value="all">{{ app()->getLocale() === 'ar' ? 'جميع حالات المخزون' : 'All Stock Statuses' }}</option>
                        <option value="healthy" {{ $selectedStatus === 'healthy' ? 'selected' : '' }}>🟢 {{ app()->getLocale() === 'ar' ? 'مخزون صحي (Healthy)' : 'Healthy' }}</option>
                        <option value="low_stock" {{ $selectedStatus === 'low_stock' ? 'selected' : '' }}>🟡 {{ app()->getLocale() === 'ar' ? 'مخزون منخفض (Low)' : 'Low Stock' }}</option>
                        <option value="out_of_stock" {{ $selectedStatus === 'out_of_stock' ? 'selected' : '' }}>🔴 {{ app()->getLocale() === 'ar' ? 'مخزون نافد (Out of Stock)' : 'Out of Stock' }}</option>
                    </select>

                    <!-- Reset Filters Button -->
                    <button type="button" class="btn btn-ghost btn-sm" onclick="resetAllFilters()" title="{{ app()->getLocale() === 'ar' ? 'إعادة تعيين الفلاتر' : 'Reset Filters' }}" style="border-radius: 20px;">
                        <i class="fa-solid fa-arrow-rotate-right mr-1 ml-1"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'تصفير' : 'Reset' }}</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Table Action Toolbar (Search, Excel Export, CSV & Print) -->
        <x-admin.table-toolbar 
            table="#controlProductsTable" 
            :title="app()->getLocale() === 'ar' ? 'مركز التحكم وتوزيع كميات المخزون' : 'Inventory Control & Multi-Hub Stock Report'" 
        />

        <!-- 3. Master Product Inventory Control Matrix Table -->
        <div class="card" style="padding: 0; background: #fff; border-radius: var(--radius-lg); border: 1px solid var(--color-border); box-shadow: 0 4px 20px rgba(0,0,0,0.04); overflow: hidden;">
            <div style="overflow-x: auto;">
                <table class="table" style="width: 100%; border-collapse: separate; border-spacing: 0; margin: 0; font-size: 0.875rem;" id="controlProductsTable">
                    <thead>
                        <tr style="background: var(--color-bg-subtle, #f8fafc); border-bottom: 2px solid var(--color-border);">
                            <th style="width: 40px; text-align: center; padding: 0.85rem 0.75rem;" data-no-sort data-no-export>
                                <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this)" style="cursor: pointer; width: 16px; height: 16px;">
                            </th>
                            <th style="padding: 0.85rem 1rem; text-align: start;" data-sort-type="text">{{ app()->getLocale() === 'ar' ? 'المنتج والتركيبة' : 'Product & Formulation' }}</th>
                            <th style="padding: 0.85rem 1rem; text-align: center; min-width: 150px;" data-sort-type="number">
                                <div style="display: flex; align-items: center; justify-content: center; gap: 0.35rem; color: #0284c7;">
                                    <i class="fa-solid fa-globe"></i>
                                    <span>{{ app()->getLocale() === 'ar' ? 'المتجر الإلكتروني' : 'Online Hub' }}</span>
                                </div>
                            </th>
                            <th style="padding: 0.85rem 1rem; text-align: center; min-width: 150px;" data-sort-type="number">
                                <div style="display: flex; align-items: center; justify-content: center; gap: 0.35rem; color: #10b981;">
                                    <i class="fa-solid fa-store"></i>
                                    <span>{{ app()->getLocale() === 'ar' ? 'المعرض وPOS' : 'POS Warehouse' }}</span>
                                </div>
                            </th>
                            <th style="padding: 0.85rem 1rem; text-align: center; min-width: 150px;" data-sort-type="number">
                                <div style="display: flex; align-items: center; justify-content: center; gap: 0.35rem; color: #8b5cf6;">
                                    <i class="fa-solid fa-warehouse"></i>
                                    <span>{{ app()->getLocale() === 'ar' ? 'المستودع المركزي' : 'Central Buffer' }}</span>
                                </div>
                            </th>
                            <th style="padding: 0.85rem 1rem; text-align: center; min-width: 140px;" data-sort-type="number">
                                {{ app()->getLocale() === 'ar' ? 'إجمالي المخزون والقيمة' : 'Total Stock & Valuation' }}
                            </th>
                            <th style="padding: 0.85rem 1rem; text-align: center; width: 110px;" data-no-sort data-no-export>
                                {{ app()->getLocale() === 'ar' ? 'الإجراءات' : 'Actions' }}
                            </th>
                        </tr>
                    </thead>
                    <tbody id="controlProductsBody">
                        @forelse($products as $prod)
                            <tr class="product-row" id="row-prod-{{ $prod['id'] }}" 
                                data-id="{{ $prod['id'] }}" 
                                data-sku="{{ strtolower($prod['sku']) }}" 
                                data-barcode="{{ strtolower($prod['barcode']) }}" 
                                data-name-en="{{ strtolower($prod['name_en']) }}" 
                                data-name-ar="{{ strtolower($prod['name_ar']) }}" 
                                data-category="{{ $prod['category_id'] }}" 
                                data-status="{{ $prod['status'] }}"
                                style="border-bottom: 1px solid var(--color-border); transition: background-color 0.15s ease;">
                                
                                <!-- Checkbox -->
                                <td style="text-align: center; padding: 1rem 0.75rem; vertical-align: middle;">
                                    <input type="checkbox" class="product-select-checkbox" value="{{ $prod['id'] }}" onchange="handleRowSelect()" style="cursor: pointer; width: 16px; height: 16px;">
                                </td>

                                <!-- Product Info -->
                                <td style="padding: 1rem; vertical-align: middle;">
                                    <div style="display: flex; align-items: center; gap: 0.85rem;">
                                        <div style="width: 48px; height: 48px; border-radius: 10px; overflow: hidden; border: 1px solid var(--color-border); background: var(--color-bg-subtle); flex-shrink: 0;">
                                            <img src="{{ asset($prod['image']) }}" alt="{{ $prod['name_en'] }}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';">
                                        </div>
                                        <div>
                                            <div style="font-weight: 800; font-size: 0.92rem; color: var(--color-text-main);">
                                                {{ app()->getLocale() === 'ar' ? $prod['name_ar'] : $prod['name_en'] }}
                                            </div>
                                            <div style="display: flex; gap: 0.4rem; align-items: center; margin-top: 0.2rem; flex-wrap: wrap;">
                                                <span class="badge badge-subtle font-mono text-xs" style="padding: 0.15rem 0.45rem; font-size: 0.7rem;">{{ $prod['sku'] }}</span>
                                                <span class="text-xs text-muted">• {{ $prod['category_name'] }}</span>
                                                <span class="text-xs font-bold text-primary">• @currency($prod['price'])</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Online Hub Stock & Stepper -->
                                <td style="padding: 1rem; text-align: center; vertical-align: middle; background: rgba(2, 132, 199, 0.015);">
                                    <div style="display: flex; flex-direction: column; align-items: center; gap: 0.35rem;">
                                        <div class="font-black text-base font-mono" id="stock-online-{{ $prod['id'] }}" style="color: #0369a1;">
                                            {{ $prod['stock_online'] }}
                                        </div>
                                        <!-- Quick Stepper (+ / -) -->
                                        <div style="display: inline-flex; align-items: center; background: #fff; border: 1px solid var(--color-border); border-radius: 20px; padding: 2px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                                            <button type="button" class="btn btn-xs btn-ghost" onclick="quickInlineAdjust({{ $prod['id'] }}, 'online', 'decrease', 1)" title="{{ app()->getLocale() === 'ar' ? 'خصم 1 وحدة' : 'Deduct 1 unit' }}" style="width: 24px; height: 24px; padding: 0; border-radius: 50%; color: #dc2626; font-weight: bold;">-</button>
                                            <button type="button" class="btn btn-xs btn-ghost font-bold" onclick="openQuickAdjustModal({{ $prod['id'] }}, 'online')" title="{{ app()->getLocale() === 'ar' ? 'تعديل كمية مخصصة' : 'Custom Quantity Adjustment' }}" style="font-size: 0.72rem; padding: 0 0.35rem; color: var(--color-text-muted);">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                            <button type="button" class="btn btn-xs btn-ghost" onclick="quickInlineAdjust({{ $prod['id'] }}, 'online', 'increase', 1)" title="{{ app()->getLocale() === 'ar' ? 'إضافة 1 وحدة' : 'Add 1 unit' }}" style="width: 24px; height: 24px; padding: 0; border-radius: 50%; color: #16a34a; font-weight: bold;">+</button>
                                        </div>
                                    </div>
                                </td>

                                <!-- POS Warehouse Stock & Stepper -->
                                <td style="padding: 1rem; text-align: center; vertical-align: middle; background: rgba(16, 185, 129, 0.015);">
                                    <div style="display: flex; flex-direction: column; align-items: center; gap: 0.35rem;">
                                        <div class="font-black text-base font-mono" id="stock-offline-{{ $prod['id'] }}" style="color: #059669;">
                                            {{ $prod['stock_offline'] }}
                                        </div>
                                        <!-- Quick Stepper (+ / -) -->
                                        <div style="display: inline-flex; align-items: center; background: #fff; border: 1px solid var(--color-border); border-radius: 20px; padding: 2px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                                            <button type="button" class="btn btn-xs btn-ghost" onclick="quickInlineAdjust({{ $prod['id'] }}, 'offline', 'decrease', 1)" title="{{ app()->getLocale() === 'ar' ? 'خصم 1 وحدة' : 'Deduct 1 unit' }}" style="width: 24px; height: 24px; padding: 0; border-radius: 50%; color: #dc2626; font-weight: bold;">-</button>
                                            <button type="button" class="btn btn-xs btn-ghost font-bold" onclick="openQuickAdjustModal({{ $prod['id'] }}, 'offline')" title="{{ app()->getLocale() === 'ar' ? 'تعديل كمية مخصصة' : 'Custom Quantity Adjustment' }}" style="font-size: 0.72rem; padding: 0 0.35rem; color: var(--color-text-muted);">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                            <button type="button" class="btn btn-xs btn-ghost" onclick="quickInlineAdjust({{ $prod['id'] }}, 'offline', 'increase', 1)" title="{{ app()->getLocale() === 'ar' ? 'إضافة 1 وحدة' : 'Add 1 unit' }}" style="width: 24px; height: 24px; padding: 0; border-radius: 50%; color: #16a34a; font-weight: bold;">+</button>
                                        </div>
                                    </div>
                                </td>

                                <!-- Central Warehouse Buffer Stock & Stepper -->
                                <td style="padding: 1rem; text-align: center; vertical-align: middle; background: rgba(139, 92, 246, 0.015);">
                                    <div style="display: flex; flex-direction: column; align-items: center; gap: 0.35rem;">
                                        <div class="font-black text-base font-mono" id="stock-central-{{ $prod['id'] }}" style="color: #7c3aed;">
                                            {{ $prod['stock_central'] }}
                                        </div>
                                        <!-- Quick Stepper (+ / -) -->
                                        <div style="display: inline-flex; align-items: center; background: #fff; border: 1px solid var(--color-border); border-radius: 20px; padding: 2px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                                            <button type="button" class="btn btn-xs btn-ghost" onclick="quickInlineAdjust({{ $prod['id'] }}, 'central_wh', 'decrease', 1)" title="{{ app()->getLocale() === 'ar' ? 'خصم 1 وحدة' : 'Deduct 1 unit' }}" style="width: 24px; height: 24px; padding: 0; border-radius: 50%; color: #dc2626; font-weight: bold;">-</button>
                                            <button type="button" class="btn btn-xs btn-ghost font-bold" onclick="openQuickAdjustModal({{ $prod['id'] }}, 'central_wh')" title="{{ app()->getLocale() === 'ar' ? 'تعديل كمية مخصصة' : 'Custom Quantity Adjustment' }}" style="font-size: 0.72rem; padding: 0 0.35rem; color: var(--color-text-muted);">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                            <button type="button" class="btn btn-xs btn-ghost" onclick="quickInlineAdjust({{ $prod['id'] }}, 'central_wh', 'increase', 1)" title="{{ app()->getLocale() === 'ar' ? 'إضافة 1 وحدة' : 'Add 1 unit' }}" style="width: 24px; height: 24px; padding: 0; border-radius: 50%; color: #16a34a; font-weight: bold;">+</button>
                                        </div>
                                    </div>
                                </td>

                                <!-- Total Stock & Health Status -->
                                <td style="padding: 1rem; text-align: center; vertical-align: middle;">
                                    <div style="display: flex; flex-direction: column; align-items: center; gap: 0.25rem;">
                                        <div class="font-black text-base" id="stock-total-{{ $prod['id'] }}">
                                            {{ $prod['total_stock'] }} <span class="text-xs text-muted" style="font-weight: normal;">{{ app()->getLocale() === 'ar' ? 'وحدة' : 'units' }}</span>
                                        </div>
                                        <div id="status-badge-{{ $prod['id'] }}">
                                            @if($prod['status'] === 'out_of_stock')
                                                <span class="badge badge-danger text-xs font-bold">{{ app()->getLocale() === 'ar' ? 'نافد' : 'Out of Stock' }}</span>
                                            @elseif($prod['status'] === 'low_stock')
                                                <span class="badge badge-warning text-xs font-bold">{{ app()->getLocale() === 'ar' ? 'منخفض' : 'Low Stock' }}</span>
                                            @else
                                                <span class="badge badge-success text-xs font-bold">{{ app()->getLocale() === 'ar' ? 'صحي' : 'Healthy' }}</span>
                                            @endif
                                        </div>
                                        <div class="text-xs font-mono text-muted" id="valuation-{{ $prod['id'] }}" style="font-size: 0.72rem;">
                                            @currency($prod['valuation'])
                                        </div>
                                    </div>
                                </td>

                                <!-- Actions Dropdown / Direct Tools -->
                                <td style="padding: 1rem; text-align: center; vertical-align: middle;">
                                    <div style="display: inline-flex; gap: 0.35rem; align-items: center;">
                                        <!-- Quick Intake Modal Trigger -->
                                        <button type="button" class="btn btn-xs btn-primary font-bold" onclick="openQuickAdjustModal({{ $prod['id'] }}, 'online')" title="{{ app()->getLocale() === 'ar' ? 'إيداع وتوريد كميات' : 'Stock Intake' }}" style="padding: 0.3rem 0.55rem;">
                                            <i class="fa-solid fa-plus-minus"></i>
                                        </button>
                                        
                                        <!-- Product History Ledger Link -->
                                        <a href="{{ route('admin.inventory.show', $prod['id']) }}" class="btn btn-xs btn-ghost" title="{{ app()->getLocale() === 'ar' ? 'سجل حركات المنتج' : 'View Product Ledger' }}" style="border: 1px solid var(--color-border); padding: 0.3rem 0.55rem;">
                                            <i class="fa-solid fa-clock-rotate-left"></i>
                                        </a>

                                        <!-- Catalog Edit Link -->
                                        <a href="{{ route('admin.products.edit', $prod['id']) }}" class="btn btn-xs btn-ghost" title="{{ app()->getLocale() === 'ar' ? 'تعديل بيانات الكتالوج' : 'Edit Catalog Product' }}" style="border: 1px solid var(--color-border); padding: 0.3rem 0.55rem;">
                                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="noProductsRow">
                                <td colspan="7" style="text-align: center; padding: 3rem 1rem;">
                                    <i class="fa-solid fa-boxes-stacked text-muted" style="font-size: 2.5rem; opacity: 0.4; margin-bottom: 0.75rem; display: block;"></i>
                                    <p class="text-muted font-bold" style="margin: 0;">{{ app()->getLocale() === 'ar' ? 'لا توجد منتجات مطابقة لخيارات البحث الحالية.' : 'No products match the selected criteria.' }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Quick Stepper Adjustment Modal -->
    <div id="quickAdjustModal" class="pos-modal-backdrop" style="display: none; z-index: 10000; position: fixed; inset: 0; background: rgba(10, 17, 40, 0.7); backdrop-filter: blur(5px); align-items: center; justify-content: center; padding: 1rem;">
        <div class="card" style="max-width: 540px; width: 95%; max-height: 90vh; overflow-y: auto; padding: 1.75rem; border-radius: var(--radius-lg); background: #fff; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); border: 1px solid var(--color-border);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--color-border); padding-bottom: 0.75rem;">
                <div style="display: flex; align-items: center; gap: 0.6rem;">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(10, 79, 120, 0.1); display: flex; align-items: center; justify-content: center; color: var(--color-primary);">
                        <i class="fa-solid fa-sliders" style="font-size: 1.1rem;"></i>
                    </div>
                    <div>
                        <h3 style="margin: 0; font-size: 1.1rem; font-weight: 800;">
                            {{ app()->getLocale() === 'ar' ? 'تعديل كمية المخزون السريع' : 'Quick Stock Quantity Adjustment' }}
                        </h3>
                        <p class="text-xs text-muted" id="modalProductSubtitle" style="margin: 0;">Product Info</p>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-ghost" onclick="closeQuickAdjustModal()">✕</button>
            </div>

            <div id="quickAdjustAlert" class="alert alert-danger" style="display: none; margin-bottom: 1rem; padding: 0.6rem 0.9rem; font-size: 0.85rem; border-radius: var(--radius-md); background: #fee2e2; color: #991b1b; border: 1px solid #f87171;"></div>

            <form id="quickAdjustForm" onsubmit="submitQuickAdjust(event)" style="display: flex; flex-direction: column; gap: 1.1rem;">
                <input type="hidden" id="adjustProductId">

                <!-- Action & Location Selector -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div>
                        <label class="font-bold text-xs" style="display: block; margin-bottom: 0.35rem;">
                            {{ app()->getLocale() === 'ar' ? 'نوع التعديل *' : 'Action Type *' }}
                        </label>
                        <select id="adjustAction" class="form-control font-bold" style="width: 100%; padding: 0.5rem 0.75rem;" onchange="handleActionTypeChange()">
                            <option value="increase">➕ {{ app()->getLocale() === 'ar' ? 'زيادة وتوريد (+)' : 'Increase (+)' }}</option>
                            <option value="decrease">➖ {{ app()->getLocale() === 'ar' ? 'إنقاص وخصم (-)' : 'Decrease (-)' }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="font-bold text-xs" style="display: block; margin-bottom: 0.35rem;">
                            {{ app()->getLocale() === 'ar' ? 'المستودع المستهدف *' : 'Target Hub *' }}
                        </label>
                        <select id="adjustLocation" class="form-control" style="width: 100%; padding: 0.5rem 0.75rem;">
                            <option value="online">{{ app()->getLocale() === 'ar' ? 'المتجر الإلكتروني (Online)' : 'Online Hub' }}</option>
                            <option value="offline">{{ app()->getLocale() === 'ar' ? 'المعرض ونقطة البيع (POS)' : 'POS Warehouse' }}</option>
                            <option value="central_wh">{{ app()->getLocale() === 'ar' ? 'المستودع المركزي (Central)' : 'Central Buffer' }}</option>
                        </select>
                    </div>
                </div>

                <!-- Ledger Movement Type -->
                <div>
                    <label class="font-bold text-xs" style="display: block; margin-bottom: 0.35rem;">
                        {{ app()->getLocale() === 'ar' ? 'نوع الحركة المحاسبية في السجل *' : 'Movement Ledger Type *' }}
                    </label>
                    <select id="adjustMovementType" class="form-control" style="width: 100%; font-size: 0.9rem; padding: 0.5rem 0.75rem;">
                        <option value="Stock In">{{ app()->getLocale() === 'ar' ? 'إدخال مخزون جديد / توريد (Stock In)' : 'Stock In / Procurement' }}</option>
                        <option value="Manual Adjustment">{{ app()->getLocale() === 'ar' ? 'تسوية جردية دورية (Manual Count Adjustment)' : 'Manual Count Adjustment' }}</option>
                        <option value="Return">{{ app()->getLocale() === 'ar' ? 'مرتجع عميل (Customer Return)' : 'Customer Return' }}</option>
                        <option value="Damaged">{{ app()->getLocale() === 'ar' ? 'إهلاك بضاعة تالفة (Damaged Write-off)' : 'Damaged Write-off' }}</option>
                        <option value="Expired">{{ app()->getLocale() === 'ar' ? 'بضاعة منتهية الصلاحية (Expired Write-off)' : 'Expired Write-off' }}</option>
                    </select>
                </div>

                <!-- Quantity with Quick Preset Buttons -->
                <div>
                    <label class="font-bold text-xs" style="display: block; margin-bottom: 0.35rem;">
                        {{ app()->getLocale() === 'ar' ? 'الكمية (بالوحدات) *' : 'Quantity (Units) *' }}
                    </label>
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <input type="number" id="adjustQuantity" class="form-control font-black" style="font-size: 1.2rem; width: 130px; text-align: center;" min="1" step="1" value="10" required>
                        <div style="display: flex; gap: 0.3rem; flex-wrap: wrap;">
                            <button type="button" class="btn btn-xs btn-ghost" onclick="setAdjustQty(1)" style="border: 1px solid var(--color-border); font-weight: bold;">1</button>
                            <button type="button" class="btn btn-xs btn-ghost" onclick="setAdjustQty(5)" style="border: 1px solid var(--color-border); font-weight: bold;">5</button>
                            <button type="button" class="btn btn-xs btn-ghost" onclick="setAdjustQty(10)" style="border: 1px solid var(--color-border); font-weight: bold;">10</button>
                            <button type="button" class="btn btn-xs btn-ghost" onclick="setAdjustQty(25)" style="border: 1px solid var(--color-border); font-weight: bold;">25</button>
                            <button type="button" class="btn btn-xs btn-ghost" onclick="setAdjustQty(50)" style="border: 1px solid var(--color-border); font-weight: bold;">50</button>
                            <button type="button" class="btn btn-xs btn-ghost" onclick="setAdjustQty(100)" style="border: 1px solid var(--color-border); font-weight: bold;">100</button>
                        </div>
                    </div>
                </div>

                <!-- PO / Reference Number -->
                <div>
                    <label class="font-bold text-xs" style="display: block; margin-bottom: 0.35rem;">
                        {{ app()->getLocale() === 'ar' ? 'رقم الفاتورة / أمر الشراء / رقم التشغيلة (اختياري)' : 'PO / Invoice / Batch Reference (Optional)' }}
                    </label>
                    <input type="text" id="adjustReference" class="form-control text-xs" placeholder="e.g. PO-2026-09-A / BATCH-01" style="width: 100%;">
                </div>

                <!-- Audit Note / Reason -->
                <div>
                    <label class="font-bold text-xs" style="display: block; margin-bottom: 0.35rem;">
                        {{ app()->getLocale() === 'ar' ? 'البيان وسبب الحركة (إلزامي للتدقيق المالي) *' : 'Audit Reason / Note *' }}
                    </label>
                    <input type="text" id="adjustReason" class="form-control text-xs" value="{{ app()->getLocale() === 'ar' ? 'تعديل كمية رصيد مخزني سريع' : 'Quick inventory balance adjustment' }}" required style="width: 100%;">
                </div>

                <!-- Actions -->
                <div style="display: flex; gap: 0.75rem; justify-content: flex-end; margin-top: 0.5rem; border-top: 1px solid var(--color-border); padding-top: 1rem;">
                    <button type="button" class="btn btn-sm btn-ghost" onclick="closeQuickAdjustModal()">
                        {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                    </button>
                    <button type="submit" id="btnSubmitQuickAdjust" class="btn btn-sm btn-primary font-bold" style="padding: 0.55rem 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                        <span id="quickAdjustSpinner" style="display: none;"><i class="fa-solid fa-spinner fa-spin"></i></span>
                        <i class="fa-solid fa-check"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'تأكيد وحفظ الحركة' : 'Post Adjustment' }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Batch Adjust Modal for Multiple Products -->
    <div id="batchAdjustModal" class="pos-modal-backdrop" style="display: none; z-index: 10000; position: fixed; inset: 0; background: rgba(10, 17, 40, 0.7); backdrop-filter: blur(5px); align-items: center; justify-content: center; padding: 1rem;">
        <div class="card" style="max-width: 580px; width: 95%; max-height: 90vh; overflow-y: auto; padding: 1.75rem; border-radius: var(--radius-lg); background: #fff; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); border: 1px solid var(--color-border);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--color-border); padding-bottom: 0.75rem;">
                <div style="display: flex; align-items: center; gap: 0.6rem;">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(139, 92, 246, 0.1); display: flex; align-items: center; justify-content: center; color: #8b5cf6;">
                        <i class="fa-solid fa-layer-group" style="font-size: 1.1rem;"></i>
                    </div>
                    <div>
                        <h3 style="margin: 0; font-size: 1.1rem; font-weight: 800;">
                            {{ app()->getLocale() === 'ar' ? 'تعديل كميات جماعي للمنتجات المحددة' : 'Bulk Batch Quantity Adjustment' }}
                        </h3>
                        <p class="text-xs text-muted" style="margin: 0;">
                            {{ app()->getLocale() === 'ar' ? 'سيتم تطبيق التعديل على ' : 'Applying adjustment to ' }}<strong id="batchModalCountText">0</strong> {{ app()->getLocale() === 'ar' ? 'منتجات محددة' : 'selected products' }}
                        </p>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-ghost" onclick="closeBatchAdjustModal()">✕</button>
            </div>

            <div id="batchAdjustAlert" class="alert alert-danger" style="display: none; margin-bottom: 1rem; padding: 0.6rem 0.9rem; font-size: 0.85rem; border-radius: var(--radius-md); background: #fee2e2; color: #991b1b; border: 1px solid #f87171;"></div>

            <form id="batchAdjustForm" onsubmit="submitBatchAdjust(event)" style="display: flex; flex-direction: column; gap: 1.1rem;">
                
                <!-- Destination Location & Movement Type -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div>
                        <label class="font-bold text-xs" style="display: block; margin-bottom: 0.35rem;">
                            {{ app()->getLocale() === 'ar' ? 'المستودع المستهدف *' : 'Target Hub *' }}
                        </label>
                        <select id="batchLocation" class="form-control" style="width: 100%; padding: 0.5rem 0.75rem;" required>
                            <option value="online">{{ app()->getLocale() === 'ar' ? 'المتجر الإلكتروني (Online)' : 'Online Hub' }}</option>
                            <option value="offline">{{ app()->getLocale() === 'ar' ? 'المعرض ونقطة البيع (POS)' : 'POS Warehouse' }}</option>
                            <option value="central_wh">{{ app()->getLocale() === 'ar' ? 'المستودع المركزي (Central)' : 'Central Buffer' }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="font-bold text-xs" style="display: block; margin-bottom: 0.35rem;">
                            {{ app()->getLocale() === 'ar' ? 'نوع الحركة المحاسبية *' : 'Movement Type *' }}
                        </label>
                        <select id="batchMovementType" class="form-control" style="width: 100%; padding: 0.5rem 0.75rem;" required>
                            <option value="Stock In">{{ app()->getLocale() === 'ar' ? 'إدخال وتوريد جديد (+)' : 'Stock In / Intake (+)' }}</option>
                            <option value="Manual Adjustment">{{ app()->getLocale() === 'ar' ? 'تسوية جردية (+)' : 'Manual Adjustment (+)' }}</option>
                            <option value="Damaged">{{ app()->getLocale() === 'ar' ? 'إهلاك تالف (-)' : 'Damaged Write-off (-)' }}</option>
                            <option value="Expired">{{ app()->getLocale() === 'ar' ? 'منتهي الصلاحية (-)' : 'Expired Write-off (-)' }}</option>
                        </select>
                    </div>
                </div>

                <!-- Quantity per product -->
                <div>
                    <label class="font-bold text-xs" style="display: block; margin-bottom: 0.35rem;">
                        {{ app()->getLocale() === 'ar' ? 'الكمية لكل منتج محدد (بالوحدات) *' : 'Quantity per product (Units) *' }}
                    </label>
                    <input type="number" id="batchQuantity" class="form-control font-black" style="font-size: 1.15rem; width: 140px; text-align: center;" min="1" step="1" value="25" required>
                </div>

                <!-- Reference & Note -->
                <div>
                    <label class="font-bold text-xs" style="display: block; margin-bottom: 0.35rem;">
                        {{ app()->getLocale() === 'ar' ? 'رقم أمر الشراء / الفاتورة (اختياري)' : 'PO / Invoice Reference (Optional)' }}
                    </label>
                    <input type="text" id="batchReference" class="form-control text-xs" placeholder="e.g. PO-BULK-2026" style="width: 100%;">
                </div>

                <div>
                    <label class="font-bold text-xs" style="display: block; margin-bottom: 0.35rem;">
                        {{ app()->getLocale() === 'ar' ? 'البيان والسبب (إلزامي) *' : 'Audit Reason / Note *' }}
                    </label>
                    <input type="text" id="batchReason" class="form-control text-xs" value="{{ app()->getLocale() === 'ar' ? 'توريد كميات جماعي للمنتجات المحددة' : 'Bulk stock shipment intake' }}" required style="width: 100%;">
                </div>

                <!-- Actions -->
                <div style="display: flex; gap: 0.75rem; justify-content: flex-end; margin-top: 0.5rem; border-top: 1px solid var(--color-border); padding-top: 1rem;">
                    <button type="button" class="btn btn-sm btn-ghost" onclick="closeBatchAdjustModal()">
                        {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                    </button>
                    <button type="submit" id="btnSubmitBatchAdjust" class="btn btn-sm btn-primary font-bold" style="padding: 0.55rem 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                        <span id="batchAdjustSpinner" style="display: none;"><i class="fa-solid fa-spinner fa-spin"></i></span>
                        <i class="fa-solid fa-check"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'تنفيذ التعديل الجماعي' : 'Execute Bulk Adjustment' }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Interactive "How to Use" Guide Modal -->
    <div id="inventoryGuideModal" class="pos-modal-backdrop" style="display: none; z-index: 10000; position: fixed; inset: 0; background: rgba(10, 17, 40, 0.75); backdrop-filter: blur(6px); align-items: center; justify-content: center; padding: 1rem;">
        <div class="card" style="max-width: 680px; width: 95%; max-height: 90vh; overflow-y: auto; padding: 2rem; border-radius: var(--radius-lg); background: #fff; box-shadow: 0 25px 60px rgba(0,0,0,0.3); border: 1px solid var(--color-border);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 0.85rem;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(10, 79, 120, 0.1); display: flex; align-items: center; justify-content: center; color: var(--color-primary);">
                        <i class="fa-solid fa-book-open-reader" style="font-size: 1.25rem;"></i>
                    </div>
                    <div>
                        <h3 style="margin: 0; font-size: 1.2rem; font-weight: 800; color: var(--color-primary);">
                            {{ app()->getLocale() === 'ar' ? 'دليل استخدام مركز التحكم بالمخزون' : 'How to Use Inventory Control Center' }}
                        </h3>
                        <p class="text-xs text-muted" style="margin: 0;">
                            {{ app()->getLocale() === 'ar' ? 'شرح تفاعلي لكيفية إدارة وتعديل كميات المنتجات باحترافية' : 'Interactive visual walkthrough of quick adjustments and ledger compliance' }}
                        </p>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-ghost" onclick="closeInventoryGuideModal()" style="font-size: 1.2rem;">✕</button>
            </div>

            <!-- Guide Steps Carousel / Visual Cards -->
            <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                
                <!-- Card 1: 1-Click Stepper (+ / -) -->
                <div style="display: flex; gap: 1rem; align-items: start; background: var(--color-bg-subtle); padding: 1rem 1.25rem; border-radius: var(--radius-md); border-inline-start: 4px solid #0284c7;">
                    <div style="width: 32px; height: 32px; border-radius: 50%; background: #0284c7; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 800; flex-shrink: 0; font-size: 0.9rem;">
                        1
                    </div>
                    <div>
                        <h4 style="margin: 0 0 0.25rem 0; font-size: 0.95rem; font-weight: 800;">
                            {{ app()->getLocale() === 'ar' ? 'الزيادة والإنقاص السريع بضغطة زر (+ / -)' : '1-Click Quick Incremental & Decremental Stepper' }}
                        </h4>
                        <p class="text-xs text-muted" style="margin: 0; line-height: 1.5;">
                            {{ app()->getLocale() === 'ar' 
                                ? 'يمكنك زيادة أو إنقاص كمية أي منتج في أي مستودع فوراً عبر أزرار (+) و (-) المدمجة في الجدول، أو الضغط على أيقونة القلم لتحديد كميات دقيقة (+10, +50, +100) وتحديد نوع الحركة.' 
                                : 'Instantly increment or decrement stock in any hub using the inline (+) and (-) stepper buttons, or click the pen icon for custom quantity presets.' }}
                        </p>
                    </div>
                </div>

                <!-- Card 2: Multi-Hub Architecture -->
                <div style="display: flex; gap: 1rem; align-items: start; background: var(--color-bg-subtle); padding: 1rem 1.25rem; border-radius: var(--radius-md); border-inline-start: 4px solid #10b981;">
                    <div style="width: 32px; height: 32px; border-radius: 50%; background: #10b981; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 800; flex-shrink: 0; font-size: 0.9rem;">
                        2
                    </div>
                    <div>
                        <h4 style="margin: 0 0 0.25rem 0; font-size: 0.95rem; font-weight: 800;">
                            {{ app()->getLocale() === 'ar' ? 'هيكل المستودعات المتعددة (Multi-Hub Matrix)' : 'Multi-Hub Segregation (Online vs POS vs Central)' }}
                        </h4>
                        <p class="text-xs text-muted" style="margin: 0; line-height: 1.5;">
                            {{ app()->getLocale() === 'ar'
                                ? 'النظام يفصل المخزون إلى 3 مستودعات: المتجر الإلكتروني (الطلبات أونلاين)، مستودع المعرض (مبيعات الكاشير وPOS)، والمستودع المركزي الاحتياطي لضمان دقة الرصيد وعدم التداخل.'
                                : 'Stock is partitioned across E-Commerce Online Hub, POS Warehouse Counter, and Central Buffer Reserve to prevent inventory collisions.' }}
                        </p>
                    </div>
                </div>

                <!-- Card 3: Batch Operations -->
                <div style="display: flex; gap: 1rem; align-items: start; background: var(--color-bg-subtle); padding: 1rem 1.25rem; border-radius: var(--radius-md); border-inline-start: 4px solid #8b5cf6;">
                    <div style="width: 32px; height: 32px; border-radius: 50%; background: #8b5cf6; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 800; flex-shrink: 0; font-size: 0.9rem;">
                        3
                    </div>
                    <div>
                        <h4 style="margin: 0 0 0.25rem 0; font-size: 0.95rem; font-weight: 800;">
                            {{ app()->getLocale() === 'ar' ? 'التعديل والتوريد الجماعي (Bulk Batch Adjustments)' : 'Bulk Batch Adjustments' }}
                        </h4>
                        <p class="text-xs text-muted" style="margin: 0; line-height: 1.5;">
                            {{ app()->getLocale() === 'ar'
                                ? 'يمكنك تحديد عدة منتجات عبر مربعات الاختيار بالجدول، ثم الضغط على زر "تعديل جماعي للمحدد" لإضافة توريدات جديدة أو تسوية رصيد لعشرات المنتجات بنقرة واحدة.'
                                : 'Select multiple products via table checkboxes and click "Batch Adjust" to apply stock intakes or rebalances across selected catalog items in one go.' }}
                        </p>
                    </div>
                </div>

                <!-- Card 4: Ledger Audit Integrity -->
                <div style="display: flex; gap: 1rem; align-items: start; background: var(--color-bg-subtle); padding: 1rem 1.25rem; border-radius: var(--radius-md); border-inline-start: 4px solid #f59e0b;">
                    <div style="width: 32px; height: 32px; border-radius: 50%; background: #f59e0b; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 800; flex-shrink: 0; font-size: 0.9rem;">
                        4
                    </div>
                    <div>
                        <h4 style="margin: 0 0 0.25rem 0; font-size: 0.95rem; font-weight: 800;">
                            {{ app()->getLocale() === 'ar' ? 'التدقيق المحاسبي وسجل الحركات غير القابل للتعديل' : 'Immutable Ledger & Audit Trail' }}
                        </h4>
                        <p class="text-xs text-muted" style="margin: 0; line-height: 1.5;">
                            {{ app()->getLocale() === 'ar'
                                ? 'كل زيادة أو إنقاص للمخزون يتم تقييدها فوراً في سجل حركات المخزون المركزي (Inventory Movements) مع اسم المسؤول، التوقيت، السبب، والرقم المرجعي لضمان التدقيق المالي الكامل.'
                                : 'Every stock change writes an immutable audit record in the central movements ledger with timestamp, user ID, reference, and reason.' }}
                        </p>
                    </div>
                </div>
            </div>

            <div style="margin-top: 1.75rem; text-align: center;">
                <button type="button" class="btn btn-primary font-bold" onclick="closeInventoryGuideModal()" style="width: 100%; padding: 0.65rem;">
                    {{ app()->getLocale() === 'ar' ? 'فهمت ذلك، ابدأ التحكم بالمخزون الآن' : 'Got it! Start Managing Stock' }}
                </button>
            </div>
        </div>
    </div>

    <!-- Client-Side Real-Time Reactive Controller -->
    <script>
        const productsData = @json($products);
        let selectedProductIds = new Set();
        let currentModalProduct = null;

        // Modal Open / Close Logic
        function openInventoryGuideModal() {
            document.getElementById('inventoryGuideModal').style.display = 'flex';
        }

        function closeInventoryGuideModal() {
            document.getElementById('inventoryGuideModal').style.display = 'none';
        }

        function openQuickAdjustModal(productId, preselectedLoc = 'online') {
            const product = productsData.find(p => p.id == productId);
            if (!product) return;

            currentModalProduct = product;
            document.getElementById('adjustProductId').value = product.id;
            document.getElementById('adjustLocation').value = preselectedLoc;
            document.getElementById('adjustQuantity').value = 10;
            document.getElementById('adjustAction').value = 'increase';
            handleActionTypeChange();

            const isAr = '{{ app()->getLocale() }}' === 'ar';
            document.getElementById('modalProductSubtitle').innerText = `${isAr ? product.name_ar : product.name_en} (SKU: ${product.sku})`;
            document.getElementById('quickAdjustAlert').style.display = 'none';

            document.getElementById('quickAdjustModal').style.display = 'flex';
        }

        function closeQuickAdjustModal() {
            document.getElementById('quickAdjustModal').style.display = 'none';
            document.getElementById('quickAdjustForm').reset();
            document.getElementById('quickAdjustAlert').style.display = 'none';
            currentModalProduct = null;
        }

        function setAdjustQty(qty) {
            document.getElementById('adjustQuantity').value = qty;
        }

        function handleActionTypeChange() {
            const action = document.getElementById('adjustAction').value;
            const moveSelect = document.getElementById('adjustMovementType');
            const isAr = '{{ app()->getLocale() }}' === 'ar';

            if (action === 'increase') {
                moveSelect.innerHTML = `
                    <option value="Stock In" selected>${isAr ? 'إدخال مخزون جديد / توريد (Stock In)' : 'Stock In / Procurement'}</option>
                    <option value="Return">${isAr ? 'مرتجع عميل (Customer Return)' : 'Customer Return'}</option>
                    <option value="Manual Adjustment">${isAr ? 'تسوية جردية دورية (Manual Count Adjustment)' : 'Manual Count Adjustment'}</option>
                `;
                document.getElementById('adjustReason').value = isAr ? 'توريد كميات جديدة للمنتج' : 'Stock intake batch';
            } else {
                moveSelect.innerHTML = `
                    <option value="Manual Adjustment" selected>${isAr ? 'تسوية جردية بالخصم (Manual Count Deduction)' : 'Manual Count Deduction'}</option>
                    <option value="Damaged">${isAr ? 'إهلاك بضاعة تالفة (Damaged Write-off)' : 'Damaged Write-off'}</option>
                    <option value="Expired">${isAr ? 'بضاعة منتهية الصلاحية (Expired Write-off)' : 'Expired Write-off'}</option>
                    <option value="Stock Out">${isAr ? 'إخراج بضاعة (Stock Out)' : 'Stock Out'}</option>
                `;
                document.getElementById('adjustReason').value = isAr ? 'خصم وإهلاك وحدات من المخزون' : 'Stock write-off adjustment';
            }
        }

        // 1-Click Fast Inline Stepper (+ / -)
        async function quickInlineAdjust(productId, locationId, action, qty = 1) {
            const row = document.getElementById(`row-prod-${productId}`);
            if (!row) return;

            const isAr = '{{ app()->getLocale() }}' === 'ar';
            const defaultReason = action === 'increase' 
                ? (isAr ? `زيادة سريعة بمقدار +${qty} وحدة` : `Quick inline addition (+${qty})`)
                : (isAr ? `خصم سريع بمقدار -${qty} وحدة` : `Quick inline deduction (-${qty})`);

            try {
                const response = await fetch('{{ route('admin.inventory.quick-adjust') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        location_id: locationId,
                        action: action,
                        quantity: qty,
                        reason: defaultReason
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    updateProductRowDOM(data.product);
                    updateGlobalKPIs(data.kpis);
                    showFloatingToast(data.message, 'success');
                } else {
                    showFloatingToast(data.message || 'Error executing adjustment', 'error');
                }
            } catch (err) {
                console.error('Quick inline adjust error:', err);
                showFloatingToast(err.message || 'Network error occurred', 'error');
            }
        }

        // Submit Quick Stepper Modal Form
        async function submitQuickAdjust(e) {
            e.preventDefault();
            const btn = document.getElementById('btnSubmitQuickAdjust');
            const spinner = document.getElementById('quickAdjustSpinner');
            const alertBox = document.getElementById('quickAdjustAlert');

            const productId = document.getElementById('adjustProductId').value;
            const locationId = document.getElementById('adjustLocation').value;
            const action = document.getElementById('adjustAction').value;
            const quantity = parseInt(document.getElementById('adjustQuantity').value);
            const movementType = document.getElementById('adjustMovementType').value;
            const referenceNumber = document.getElementById('adjustReference').value;
            const reason = document.getElementById('adjustReason').value;

            if (!quantity || quantity <= 0) {
                alert('{{ app()->getLocale() === 'ar' ? 'يرجى إدخال كمية صحيحة أكبر من الصفر' : 'Please enter a valid quantity greater than zero' }}');
                return;
            }

            btn.disabled = true;
            if (spinner) spinner.style.display = 'inline-block';
            if (alertBox) alertBox.style.display = 'none';

            try {
                const response = await fetch('{{ route('admin.inventory.quick-adjust') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        location_id: locationId,
                        action: action,
                        quantity: quantity,
                        movement_type: movementType,
                        reference_number: referenceNumber,
                        reason: reason
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    updateProductRowDOM(data.product);
                    updateGlobalKPIs(data.kpis);
                    closeQuickAdjustModal();
                    showFloatingToast(data.message, 'success');
                } else {
                    alertBox.innerText = data.message || 'Error updating stock';
                    alertBox.style.display = 'block';
                }
            } catch (err) {
                console.error('Submit adjust error:', err);
                alertBox.innerText = err.message || 'Network error occurred';
                alertBox.style.display = 'block';
            } finally {
                btn.disabled = false;
                if (spinner) spinner.style.display = 'none';
            }
        }

        // Batch Adjust Modal Handlers
        function openBatchAdjustModal() {
            if (selectedProductIds.size === 0) return;
            document.getElementById('batchModalCountText').innerText = selectedProductIds.size;
            document.getElementById('batchAdjustAlert').style.display = 'none';
            document.getElementById('batchAdjustModal').style.display = 'flex';
        }

        function closeBatchAdjustModal() {
            document.getElementById('batchAdjustModal').style.display = 'none';
            document.getElementById('batchAdjustForm').reset();
            document.getElementById('batchAdjustAlert').style.display = 'none';
        }

        async function submitBatchAdjust(e) {
            e.preventDefault();
            const btn = document.getElementById('btnSubmitBatchAdjust');
            const spinner = document.getElementById('batchAdjustSpinner');
            const alertBox = document.getElementById('batchAdjustAlert');

            const locationId = document.getElementById('batchLocation').value;
            const movementType = document.getElementById('batchMovementType').value;
            const quantity = parseInt(document.getElementById('batchQuantity').value);
            const referenceNumber = document.getElementById('batchReference').value;
            const reason = document.getElementById('batchReason').value;

            if (!quantity || quantity <= 0) {
                alert('{{ app()->getLocale() === 'ar' ? 'يرجى إدخال كمية صحيحة أكبر من الصفر' : 'Please enter a valid quantity greater than zero' }}');
                return;
            }

            btn.disabled = true;
            if (spinner) spinner.style.display = 'inline-block';
            if (alertBox) alertBox.style.display = 'none';

            try {
                const response = await fetch('{{ route('admin.inventory.batch-adjust') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        product_ids: Array.from(selectedProductIds),
                        location_id: locationId,
                        movement_type: movementType,
                        quantity: quantity,
                        reference_number: referenceNumber,
                        reason: reason
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    if (Array.isArray(data.products)) {
                        data.products.forEach(p => updateProductRowDOM(p));
                    }
                    if (data.kpis) {
                        updateGlobalKPIs(data.kpis);
                    }
                    closeBatchAdjustModal();
                    // Clear selections
                    document.getElementById('selectAllCheckbox').checked = false;
                    toggleSelectAll(document.getElementById('selectAllCheckbox'));
                    showFloatingToast(data.message, 'success');
                } else {
                    alertBox.innerText = data.message || 'Error updating batch';
                    alertBox.style.display = 'block';
                }
            } catch (err) {
                console.error('Batch adjust error:', err);
                alertBox.innerText = err.message || 'Network error occurred';
                alertBox.style.display = 'block';
            } finally {
                btn.disabled = false;
                if (spinner) spinner.style.display = 'none';
            }
        }

        // Live DOM Updating Helpers
        function updateProductRowDOM(product) {
            if (!product || !product.id) return;

            const pId = product.id;
            const row = document.getElementById(`row-prod-${pId}`);
            if (row) {
                row.setAttribute('data-status', product.status);
            }

            const elOnline = document.getElementById(`stock-online-${pId}`);
            if (elOnline && product.stock_online !== undefined) {
                elOnline.innerText = product.stock_online;
                animateHighlight(elOnline);
            }

            const elOffline = document.getElementById(`stock-offline-${pId}`);
            if (elOffline && product.stock_offline !== undefined) {
                elOffline.innerText = product.stock_offline;
                animateHighlight(elOffline);
            }

            const elCentral = document.getElementById(`stock-central-${pId}`);
            if (elCentral && product.stock_central !== undefined) {
                elCentral.innerText = product.stock_central;
                animateHighlight(elCentral);
            }

            const elTotal = document.getElementById(`stock-total-${pId}`);
            if (elTotal && product.total_stock !== undefined) {
                const isAr = '{{ app()->getLocale() }}' === 'ar';
                elTotal.innerHTML = `${product.total_stock} <span class="text-xs text-muted" style="font-weight: normal;">${isAr ? 'وحدة' : 'units'}</span>`;
                animateHighlight(elTotal);
            }

            const elValuation = document.getElementById(`valuation-${pId}`);
            if (elValuation && product.valuation !== undefined) {
                elValuation.innerText = window.BLUEZONE_CURRENCY ? window.BLUEZONE_CURRENCY.format(product.valuation) : ('$' + Number(product.valuation).toFixed(2));
            }

            const badgeContainer = document.getElementById(`status-badge-${pId}`);
            if (badgeContainer && product.status) {
                const isAr = '{{ app()->getLocale() }}' === 'ar';
                if (product.status === 'out_of_stock') {
                    badgeContainer.innerHTML = `<span class="badge badge-danger text-xs font-bold">${isAr ? 'نافد' : 'Out of Stock'}</span>`;
                } else if (product.status === 'low_stock') {
                    badgeContainer.innerHTML = `<span class="badge badge-warning text-xs font-bold">${isAr ? 'منخفض' : 'Low Stock'}</span>`;
                } else {
                    badgeContainer.innerHTML = `<span class="badge badge-success text-xs font-bold">${isAr ? 'صحي' : 'Healthy'}</span>`;
                }
            }

            // Also update in-memory cache
            const cacheItem = productsData.find(p => p.id == pId);
            if (cacheItem) {
                if (product.stock_online !== undefined) cacheItem.stock_online = product.stock_online;
                if (product.stock_offline !== undefined) cacheItem.stock_offline = product.stock_offline;
                if (product.stock_central !== undefined) cacheItem.stock_central = product.stock_central;
                if (product.total_stock !== undefined) cacheItem.total_stock = product.total_stock;
                if (product.status !== undefined) cacheItem.status = product.status;
            }
        }

        function updateGlobalKPIs(kpis) {
            if (!kpis) return;
            if (kpis.total_units !== undefined) {
                const el = document.getElementById('kpiTotalUnits');
                if (el) el.innerText = Number(kpis.total_units).toLocaleString();
            }
            if (kpis.online_units !== undefined) {
                const el = document.getElementById('kpiOnlineUnits');
                if (el) el.innerText = Number(kpis.online_units).toLocaleString();
            }
            if (kpis.offline_units !== undefined) {
                const el = document.getElementById('kpiOfflineUnits');
                if (el) el.innerText = Number(kpis.offline_units).toLocaleString();
            }
            if (kpis.central_units !== undefined) {
                const el = document.getElementById('kpiCentralUnits');
                if (el) el.innerText = Number(kpis.central_units).toLocaleString();
            }
        }

        function animateHighlight(element) {
            element.style.transition = 'transform 0.2s ease, color 0.2s ease';
            element.style.transform = 'scale(1.25)';
            setTimeout(() => {
                element.style.transform = 'scale(1)';
            }, 300);
        }

        // Multi-Selection Checkboxes
        function toggleSelectAll(masterCheckbox) {
            const checkboxes = document.querySelectorAll('.product-select-checkbox');
            selectedProductIds.clear();
            checkboxes.forEach(cb => {
                const row = cb.closest('.product-row');
                if (row && row.style.display !== 'none') {
                    cb.checked = masterCheckbox.checked;
                    if (masterCheckbox.checked) {
                        selectedProductIds.add(cb.value);
                    }
                }
            });
            updateBatchButtonState();
        }

        function handleRowSelect() {
            selectedProductIds.clear();
            const checkboxes = document.querySelectorAll('.product-select-checkbox:checked');
            checkboxes.forEach(cb => selectedProductIds.add(cb.value));
            updateBatchButtonState();
        }

        function updateBatchButtonState() {
            const btn = document.getElementById('btnBatchAction');
            const badge = document.getElementById('selectedCountBadge');
            if (selectedProductIds.size > 0) {
                if (badge) badge.innerText = selectedProductIds.size;
                if (btn) btn.style.display = 'inline-flex';
            } else {
                if (btn) btn.style.display = 'none';
            }
        }

        // Search & Filtering Engine
        function handleSearchFilter() {
            const query = document.getElementById('inventorySearchInput').value.trim().toLowerCase();
            const category = document.getElementById('categoryFilter').value;
            const status = document.getElementById('statusFilter').value;
            const clearBtn = document.getElementById('clearSearchBtn');

            if (clearBtn) {
                clearBtn.style.display = query.length > 0 ? 'inline-block' : 'none';
            }

            const rows = document.querySelectorAll('.product-row');
            let visibleCount = 0;

            rows.forEach(row => {
                const sku = row.getAttribute('data-sku') || '';
                const barcode = row.getAttribute('data-barcode') || '';
                const nameEn = row.getAttribute('data-name-en') || '';
                const nameAr = row.getAttribute('data-name-ar') || '';
                const rowCat = row.getAttribute('data-category');
                const rowStatus = row.getAttribute('data-status');

                const matchesQuery = !query || sku.includes(query) || barcode.includes(query) || nameEn.includes(query) || nameAr.includes(query);
                const matchesCat = category === 'all' || rowCat === category;
                const matchesStatus = status === 'all' || rowStatus === status;

                if (matchesQuery && matchesCat && matchesStatus) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            const noRow = document.getElementById('noProductsRow');
            if (noRow) {
                noRow.style.display = visibleCount === 0 ? '' : 'none';
            }
        }

        function clearSearchInput() {
            const input = document.getElementById('inventorySearchInput');
            if (input) {
                input.value = '';
                handleSearchFilter();
            }
        }

        function resetAllFilters() {
            document.getElementById('inventorySearchInput').value = '';
            document.getElementById('categoryFilter').value = 'all';
            document.getElementById('statusFilter').value = 'all';
            handleSearchFilter();
        }

        // Floating Toast Feedback Engine
        function showFloatingToast(msg, type = 'success') {
            const toast = document.createElement('div');
            toast.style.position = 'fixed';
            toast.style.bottom = '2rem';
            toast.style.insetInlineStart = '50%';
            toast.style.transform = 'translateX(-50%)';
            toast.style.background = type === 'success' ? '#0A1128' : '#991b1b';
            toast.style.color = '#fff';
            toast.style.padding = '0.85rem 1.75rem';
            toast.style.borderRadius = '30px';
            toast.style.boxShadow = '0 12px 35px rgba(0,0,0,0.25)';
            toast.style.zIndex = '99999';
            toast.style.display = 'flex';
            toast.style.alignItems = 'center';
            toast.style.gap = '0.65rem';
            toast.style.fontWeight = '700';
            toast.style.fontSize = '0.92rem';
            
            const icon = type === 'success' ? 'fa-circle-check text-emerald-400' : 'fa-circle-xmark text-rose-400';
            toast.innerHTML = `<i class="fa-solid ${icon}" style="font-size: 1.1rem;"></i> <span>${msg}</span>`;
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(-50%) translateY(15px)';
                setTimeout(() => toast.remove(), 400);
            }, 3500);
        }
    </script>
</x-layouts.admin>
