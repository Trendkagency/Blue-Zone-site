<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'موزع المخزون التفاعلي (Drag & Drop)' : 'Interactive Stock Allocator'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'تحكم كامل وتوزيع فوري للرصيد بين مستودع الأونلاين ونقاط البيع والمستودع المركزي مع تتبع الحركات الحية' : 'Full interactive control, instant stock transfers across distribution hubs, and live movement ledger.'"
    :breadcrumbs="[__('admin.menu.inventory') => route('admin.inventory.index'), (app()->getLocale() === 'ar' ? 'موزع المخزون' : 'Stock Allocator') => route('admin.inventory.allocator')]"
>
    <x-slot name="actions">
        <button type="button" class="btn btn-secondary" onclick="openBatchSplitModal()">
            <i class="fa-solid fa-chart-pie mr-1.5 ml-1.5 text-primary"></i> {{ app()->getLocale() === 'ar' ? 'توزيع نسبي مجمّع (Batch Split)' : 'Batch Ratio Allocation' }}
        </button>
        <a href="{{ route('admin.inventory.transfers') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-right-arrow-left mr-1.5 ml-1.5"></i> {{ __('admin.inventory.transfer_title') }}
        </a>
        <a href="{{ route('admin.inventory.index') }}" class="btn btn-outline-primary">
            <i class="fa-solid fa-table mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'جدول الجرد التفصيلي' : 'Inventory Table' }}
        </a>
    </x-slot>

    <!-- Custom Allocator Styles -->
    <style>
        .allocator-kpi-bar {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.25rem;
            margin-bottom: 1.75rem;
        }
        .allocator-kpi-card {
            background: var(--color-bg-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-lg);
            padding: 1.25rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
        }
        .allocator-kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08);
        }
        .allocator-hubs-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            align-items: start;
            margin-bottom: 2rem;
        }
        @media (max-width: 1024px) {
            .allocator-hubs-grid {
                grid-template-columns: 1fr;
            }
        }
        .hub-column {
            background: var(--color-bg-surface);
            border: 2px solid var(--color-border);
            border-radius: var(--radius-xl);
            display: flex;
            flex-direction: column;
            min-height: 600px;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }
        .hub-column.drag-over {
            border-color: #0A4F78;
            background: rgba(10, 79, 120, 0.04);
            box-shadow: 0 0 0 4px rgba(10, 79, 120, 0.15);
            transform: scale(1.008);
        }
        .hub-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--color-border);
            border-top-left-radius: calc(var(--radius-xl) - 2px);
            border-top-right-radius: calc(var(--radius-xl) - 2px);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .hub-header-online {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.08) 0%, rgba(37, 99, 235, 0.03) 100%);
            border-bottom-color: rgba(59, 130, 246, 0.2);
        }
        .hub-header-offline {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.08) 0%, rgba(5, 150, 105, 0.03) 100%);
            border-bottom-color: rgba(16, 185, 129, 0.2);
        }
        .hub-header-central {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.08) 0%, rgba(109, 40, 217, 0.03) 100%);
            border-bottom-color: rgba(139, 92, 246, 0.2);
        }
        .hub-dropzone {
            padding: 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
            flex-grow: 1;
            min-height: 450px;
            overflow-y: auto;
            max-height: 720px;
        }
        .draggable-stock-card {
            background: var(--color-bg-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-lg);
            padding: 0.85rem;
            cursor: grab;
            user-select: none;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.03);
            position: relative;
        }
        .draggable-stock-card:active {
            cursor: grabbing;
        }
        .draggable-stock-card.is-dragging {
            opacity: 0.35;
            transform: scale(0.96) rotate(1deg);
            border-color: #0A4F78;
            box-shadow: 0 12px 24px rgba(10, 79, 120, 0.2);
        }
        .draggable-stock-card:hover {
            border-color: var(--color-primary);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.08);
            transform: translateY(-1px);
        }
        .stock-progress-bar {
            height: 6px;
            border-radius: 999px;
            background: var(--color-border);
            overflow: hidden;
            margin-top: 0.5rem;
            display: flex;
        }
        .hub-breakdown-pills {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.4rem;
            margin-top: 0.65rem;
            background: var(--color-bg-subtle);
            padding: 0.5rem 0.6rem;
            border-radius: var(--radius-md);
            border: 1px solid var(--color-border);
            font-size: 0.72rem;
            text-align: center;
        }
        .hub-pill-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            border-radius: var(--radius-sm);
            padding: 0.2rem 0.3rem;
            transition: background 0.2s;
        }
        .hub-pill-item.active-hub {
            background: rgba(10, 79, 120, 0.1);
            font-weight: 800;
        }
        .chip-preset {
            cursor: pointer;
            padding: 0.35rem 0.75rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 700;
            border: 1px solid var(--color-border);
            background: var(--color-bg-surface);
            color: var(--color-text-main);
            transition: all 0.15s;
        }
        .chip-preset:hover {
            border-color: var(--color-primary);
            color: var(--color-primary);
            background: rgba(10, 79, 120, 0.05);
        }
        .pulse-deduct {
            animation: pulseDeduct 0.8s ease;
        }
        .pulse-add {
            animation: pulseAdd 0.8s ease;
        }
        @keyframes pulseDeduct {
            0% { background-color: rgba(239, 68, 68, 0.35); transform: scale(1.05); }
            100% { background-color: transparent; transform: scale(1); }
        }
        @keyframes pulseAdd {
            0% { background-color: rgba(16, 185, 129, 0.35); transform: scale(1.05); }
            100% { background-color: transparent; transform: scale(1); }
        }
        .new-movement-row {
            animation: highlightNewRow 2s ease;
        }
        @keyframes highlightNewRow {
            0% { background-color: rgba(16, 185, 129, 0.2); }
            100% { background-color: transparent; }
        }
    </style>

    <!-- 1. Top KPI & Distribution Capacity Bar -->
    <div class="allocator-kpi-bar">
        <div class="allocator-kpi-card" style="border-inline-start: 4px solid var(--color-primary);">
            <div class="text-xs text-muted font-bold" style="text-transform: uppercase;">
                {{ app()->getLocale() === 'ar' ? 'إجمالي المخزون المتاح' : 'Total Inventory Units' }}
            </div>
            <div class="font-black text-2xl text-primary" style="margin-top: 0.35rem;" id="kpiTotalUnits">
                {{ number_format($kpis['total_units']) }} <span class="text-xs text-muted font-normal">{{ app()->getLocale() === 'ar' ? 'وحدة' : 'units' }}</span>
            </div>
            <div class="text-xs text-muted" style="margin-top: 0.25rem;">
                {{ $kpis['total_products'] }} {{ app()->getLocale() === 'ar' ? 'تركيبة مسجلة' : 'active formulations' }}
            </div>
        </div>

        <div class="allocator-kpi-card" style="border-inline-start: 4px solid #3B82F6;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div class="text-xs text-muted font-bold" style="text-transform: uppercase;">
                    {{ app()->getLocale() === 'ar' ? 'مستودع الأونلاين' : 'Online Storefront' }}
                </div>
                <span class="badge badge-neutral font-mono text-xs" id="kpiOnlinePct">{{ $kpis['online_pct'] }}%</span>
            </div>
            <div class="font-black text-2xl text-blue-600 dark:text-blue-400" style="margin-top: 0.35rem;" id="kpiOnlineUnits">
                {{ number_format($kpis['online_units']) }} <span class="text-xs text-muted font-normal">{{ app()->getLocale() === 'ar' ? 'وحدة' : 'units' }}</span>
            </div>
            <div class="text-xs text-muted" style="margin-top: 0.25rem;">
                {{ app()->getLocale() === 'ar' ? 'متاح للشحن الفوري' : 'Fulfillment buffer' }}
            </div>
        </div>

        <div class="allocator-kpi-card" style="border-inline-start: 4px solid #10B981;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div class="text-xs text-muted font-bold" style="text-transform: uppercase;">
                    {{ app()->getLocale() === 'ar' ? 'مستودع المبيعات (POS)' : 'Warehouse (POS)' }}
                </div>
                <span class="badge badge-neutral font-mono text-xs" id="kpiOfflinePct">{{ $kpis['offline_pct'] }}%</span>
            </div>
            <div class="font-black text-2xl text-emerald-600 dark:text-emerald-400" style="margin-top: 0.35rem;" id="kpiOfflineUnits">
                {{ number_format($kpis['offline_units']) }} <span class="text-xs text-muted font-normal">{{ app()->getLocale() === 'ar' ? 'وحدة' : 'units' }}</span>
            </div>
            <div class="text-xs text-muted" style="margin-top: 0.25rem;">
                {{ app()->getLocale() === 'ar' ? 'متاح للمبيعات المباشرة' : 'Direct retail stock' }}
            </div>
        </div>

        <div class="allocator-kpi-card" style="border-inline-start: 4px solid #8B5CF6;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div class="text-xs text-muted font-bold" style="text-transform: uppercase;">
                    {{ app()->getLocale() === 'ar' ? 'المستودع اللوجستي المركزي' : 'Central Depot' }}
                </div>
                <span class="badge badge-neutral font-mono text-xs" id="kpiCentralPct">{{ $kpis['central_pct'] }}%</span>
            </div>
            <div class="font-black text-2xl text-purple-600 dark:text-purple-400" style="margin-top: 0.35rem;" id="kpiCentralUnits">
                {{ number_format($kpis['central_units']) }} <span class="text-xs text-muted font-normal">{{ app()->getLocale() === 'ar' ? 'وحدة' : 'units' }}</span>
            </div>
            <div class="text-xs text-muted" style="margin-top: 0.25rem;">
                {{ app()->getLocale() === 'ar' ? 'احتياطي إعادة التوريد' : 'Replenishment reservoir' }}
            </div>
        </div>
    </div>

    <!-- 2. Interactive Search, Category Filters, & Quick Actions Bar -->
    <div class="card" style="padding: 1.25rem; margin-bottom: 1.5rem;">
        <div style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: center; justify-content: space-between;">
            <!-- Search & Filters -->
            <div style="display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: center; flex: 1; min-width: 300px;">
                <div style="position: relative; flex: 1; min-width: 240px;">
                    <i class="fa-solid fa-magnifying-glass" style="position: absolute; inset-inline-start: 1rem; top: 50%; transform: translateY(-50%); color: var(--color-text-muted);"></i>
                    <input type="text" 
                           id="allocatorSearchInput" 
                           class="form-input" 
                           style="padding-inline-start: 2.5rem;" 
                           placeholder="{{ app()->getLocale() === 'ar' ? 'ابحث باسم المنتج، SKU، أو الباركود...' : 'Search formulation by name, SKU, or barcode...' }}"
                           oninput="filterAllocatorCards()">
                </div>

                <select id="categoryFilterSelect" class="form-select" style="min-width: 180px;" onchange="filterAllocatorCards()">
                    <option value="all">{{ app()->getLocale() === 'ar' ? 'جميع الفئات الحيوية' : 'All Categories' }}</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ app()->getLocale() === 'ar' && !empty($cat->name_ar) ? $cat->name_ar : $cat->name_en }}</option>
                    @endforeach
                </select>

                <select id="stockStatusSelect" class="form-select" style="min-width: 150px;" onchange="filterAllocatorCards()">
                    <option value="all">{{ app()->getLocale() === 'ar' ? 'كل حالات المخزون' : 'All Stock Status' }}</option>
                    <option value="healthy">{{ app()->getLocale() === 'ar' ? 'رصيد ممتاز (Healthy)' : 'Healthy' }}</option>
                    <option value="low_stock">{{ app()->getLocale() === 'ar' ? 'منخفض (Low Stock)' : 'Low Stock Alert' }}</option>
                    <option value="out_of_stock">{{ app()->getLocale() === 'ar' ? 'نافد (Out of Stock)' : 'Out of Stock' }}</option>
                </select>
            </div>

            <!-- Batch Selection Controls -->
            <div style="display: flex; gap: 0.5rem; align-items: center;">
                <button type="button" class="btn btn-sm btn-ghost" onclick="toggleSelectAllProducts()">
                    <i class="fa-solid fa-check-double mr-1 ml-1"></i> <span id="btnSelectAllText">{{ app()->getLocale() === 'ar' ? 'تحديد الكل للتقسيم' : 'Select All' }}</span>
                </button>
                <button type="button" class="btn btn-sm btn-primary" onclick="openBatchSplitModal()">
                    <i class="fa-solid fa-sliders mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'إعادة توزيع محدد' : 'Rebalance Selected' }} (<span id="selectedCountBadge">0</span>)
                </button>
            </div>
        </div>
    </div>

    <!-- 3. Multi-Hub Drag & Drop Workspace (3 Drop Columns) -->
    <div class="allocator-hubs-grid">
        
        <!-- HUB 1: Online Store Fulfillment (online) -->
        <div class="hub-column" id="hub_online" data-location="online" ondragover="handleDragOver(event)" ondragleave="handleDragLeave(event)" ondrop="handleDrop(event, 'online')">
            <div class="hub-header hub-header-online">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 38px; height: 38px; border-radius: var(--radius-md); background: #3B82F6; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.15rem;">
                        <i class="fa-solid fa-globe"></i>
                    </div>
                    <div>
                        <h4 style="margin: 0; font-size: 1.05rem; font-weight: 800; color: #1E3A8A;">
                            {{ app()->getLocale() === 'ar' ? 'مستودع المتجر الإلكتروني' : 'Online Store Hub' }}
                        </h4>
                        <div class="text-xs text-muted" style="margin-top: 0.15rem;">
                            {{ app()->getLocale() === 'ar' ? 'الشحن السريع والطلبات الأونلاين' : 'E-commerce dispatch & orders' }}
                        </div>
                    </div>
                </div>
                <span class="badge badge-primary font-mono text-xs" id="hubTotalBadge_online">
                    {{ number_format($kpis['online_units']) }} units
                </span>
            </div>

            <div class="hub-dropzone" id="dropzone_online">
                @foreach($products as $p)
                    <div class="draggable-stock-card product-card-item" 
                         id="card_online_{{ $p['id'] }}"
                         data-product-id="{{ $p['id'] }}"
                         data-current-location="online"
                         data-category-id="{{ $p['category_id'] }}"
                         data-status="{{ $p['status'] }}"
                         data-search-text="{{ strtolower($p['name_en'] . ' ' . $p['name_ar'] . ' ' . $p['sku'] . ' ' . $p['barcode']) }}"
                         draggable="true" 
                         ondragstart="handleDragStart(event, {{ $p['id'] }}, 'online')">
                        
                        <div style="display: flex; gap: 0.75rem; align-items: flex-start;">
                            <input type="checkbox" class="batch-product-checkbox form-check-input" value="{{ $p['id'] }}" onclick="event.stopPropagation(); updateBatchCount();">
                            <img src="{{ $p['image'] }}" alt="{{ $p['name_en'] }}" style="width: 48px; height: 48px; border-radius: var(--radius-md); object-fit: cover; background: var(--color-bg-subtle);" onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';">
                            <div style="flex: 1; min-width: 0;">
                                <div style="display: flex; justify-content: space-between; align-items: start; gap: 0.5rem;">
                                    <div class="font-bold text-sm" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        {{ app()->getLocale() === 'ar' ? $p['name_ar'] : $p['name_en'] }}
                                    </div>
                                    <span class="font-mono text-xs font-black text-blue-600 dark:text-blue-400 stock-qty-display" id="stock_online_{{ $p['id'] }}">
                                        {{ $p['stock_online'] }} {{ app()->getLocale() === 'ar' ? 'وحدة' : 'units' }}
                                    </span>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.25rem;">
                                    <span class="font-mono text-xs text-muted">{{ $p['sku'] }}</span>
                                    <span class="badge {{ $p['stock_online'] <= $p['low_stock_threshold'] ? 'badge-warning' : 'badge-neutral' }} text-xs">
                                        @currency($p['price'])
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- All-Hubs Live Breakdown Pills -->
                        <div class="hub-breakdown-pills">
                            <div class="hub-pill-item active-hub">
                                <span class="text-blue-600 font-bold">🌐 Web</span>
                                <strong id="pill_online_{{ $p['id'] }}_online">{{ $p['stock_online'] }}</strong>
                            </div>
                            <div class="hub-pill-item">
                                <span class="text-emerald-600">🏬 POS</span>
                                <strong id="pill_online_{{ $p['id'] }}_offline">{{ $p['stock_offline'] }}</strong>
                            </div>
                            <div class="hub-pill-item">
                                <span class="text-purple-600">🏭 Cent.</span>
                                <strong id="pill_online_{{ $p['id'] }}_central">{{ $p['stock_central'] }}</strong>
                            </div>
                        </div>

                        <!-- Mini progress representation: Online / Offline / Central -->
                        @php
                            $totalP = max(1, $p['total_stock']);
                            $onlPct = round(($p['stock_online'] / $totalP) * 100);
                            $offPct = round(($p['stock_offline'] / $totalP) * 100);
                            $cenPct = 100 - ($onlPct + $offPct);
                        @endphp
                        <div class="stock-progress-bar" id="pbar_online_{{ $p['id'] }}" title="Online: {{ $p['stock_online'] }} | POS: {{ $p['stock_offline'] }} | Central: {{ $p['stock_central'] }}">
                            <div class="bar-onl" style="width: {{ $onlPct }}%; background: #3B82F6;"></div>
                            <div class="bar-off" style="width: {{ $offPct }}%; background: #10B981;"></div>
                            <div class="bar-cen" style="width: {{ $cenPct }}%; background: #8B5CF6;"></div>
                        </div>

                        <!-- Card Transfer Actions -->
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.65rem; border-top: 1px dashed var(--color-border); padding-top: 0.45rem;">
                            <span class="text-xs text-muted">
                                {{ app()->getLocale() === 'ar' ? 'إجمالي الرصيد: ' : 'Total: ' }} <strong class="total-stock-display text-primary font-bold">{{ $p['total_stock'] }}</strong>
                            </span>
                            <div style="display: flex; gap: 0.35rem;">
                                <button type="button" class="btn btn-xs btn-outline" onclick="event.stopPropagation(); quickTransferPrompt({{ $p['id'] }}, 'online', 'offline', 5)" title="{{ app()->getLocale() === 'ar' ? 'تحويل 5 وحدات إلى المعرض' : 'Move 5 to POS' }}">
                                    ➔ POS (5)
                                </button>
                                <button type="button" class="btn btn-xs btn-ghost" onclick="event.stopPropagation(); openManualTransferModal({{ $p['id'] }}, 'online')" title="{{ app()->getLocale() === 'ar' ? 'تحويل مخصص' : 'Custom Transfer' }}">
                                    <i class="fa-solid fa-arrows-split-up-and-left text-primary"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- HUB 2: POS Warehouse (offline) -->
        <div class="hub-column" id="hub_offline" data-location="offline" ondragover="handleDragOver(event)" ondragleave="handleDragLeave(event)" ondrop="handleDrop(event, 'offline')">
            <div class="hub-header hub-header-offline">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 38px; height: 38px; border-radius: var(--radius-md); background: #10B981; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.15rem;">
                        <i class="fa-solid fa-cash-register"></i>
                    </div>
                    <div>
                        <h4 style="margin: 0; font-size: 1.05rem; font-weight: 800; color: #065F46;">
                            {{ app()->getLocale() === 'ar' ? 'مستودع المبيعات المباشرة (POS)' : 'POS Warehouse' }}
                        </h4>
                        <div class="text-xs text-muted" style="margin-top: 0.15rem;">
                            {{ app()->getLocale() === 'ar' ? 'مبيعات الكاشير المباشرة للعملاء' : 'Direct retail walk-in counter' }}
                        </div>
                    </div>
                </div>
                <span class="badge badge-success font-mono text-xs" id="hubTotalBadge_offline">
                    {{ number_format($kpis['offline_units']) }} units
                </span>
            </div>

            <div class="hub-dropzone" id="dropzone_offline">
                @foreach($products as $p)
                    <div class="draggable-stock-card product-card-item" 
                         id="card_offline_{{ $p['id'] }}"
                         data-product-id="{{ $p['id'] }}"
                         data-current-location="offline"
                         data-category-id="{{ $p['category_id'] }}"
                         data-status="{{ $p['status'] }}"
                         data-search-text="{{ strtolower($p['name_en'] . ' ' . $p['name_ar'] . ' ' . $p['sku'] . ' ' . $p['barcode']) }}"
                         draggable="true" 
                         ondragstart="handleDragStart(event, {{ $p['id'] }}, 'offline')">
                        
                        <div style="display: flex; gap: 0.75rem; align-items: flex-start;">
                            <input type="checkbox" class="batch-product-checkbox form-check-input" value="{{ $p['id'] }}" onclick="event.stopPropagation(); updateBatchCount();">
                            <img src="{{ $p['image'] }}" alt="{{ $p['name_en'] }}" style="width: 48px; height: 48px; border-radius: var(--radius-md); object-fit: cover; background: var(--color-bg-subtle);" onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';">
                            <div style="flex: 1; min-width: 0;">
                                <div style="display: flex; justify-content: space-between; align-items: start; gap: 0.5rem;">
                                    <div class="font-bold text-sm" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        {{ app()->getLocale() === 'ar' ? $p['name_ar'] : $p['name_en'] }}
                                    </div>
                                    <span class="font-mono text-xs font-black text-emerald-600 dark:text-emerald-400 stock-qty-display" id="stock_offline_{{ $p['id'] }}">
                                        {{ $p['stock_offline'] }} {{ app()->getLocale() === 'ar' ? 'وحدة' : 'units' }}
                                    </span>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.25rem;">
                                    <span class="font-mono text-xs text-muted">{{ $p['sku'] }}</span>
                                    <span class="badge {{ $p['stock_offline'] <= $p['low_stock_threshold'] ? 'badge-danger' : 'badge-neutral' }} text-xs">
                                        @currency($p['price'])
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- All-Hubs Live Breakdown Pills -->
                        <div class="hub-breakdown-pills">
                            <div class="hub-pill-item">
                                <span class="text-blue-600">🌐 Web</span>
                                <strong id="pill_offline_{{ $p['id'] }}_online">{{ $p['stock_online'] }}</strong>
                            </div>
                            <div class="hub-pill-item active-hub">
                                <span class="text-emerald-600 font-bold">🏬 POS</span>
                                <strong id="pill_offline_{{ $p['id'] }}_offline">{{ $p['stock_offline'] }}</strong>
                            </div>
                            <div class="hub-pill-item">
                                <span class="text-purple-600">🏭 Cent.</span>
                                <strong id="pill_offline_{{ $p['id'] }}_central">{{ $p['stock_central'] }}</strong>
                            </div>
                        </div>

                        <!-- Mini progress representation -->
                        @php
                            $totalP = max(1, $p['total_stock']);
                            $onlPct = round(($p['stock_online'] / $totalP) * 100);
                            $offPct = round(($p['stock_offline'] / $totalP) * 100);
                            $cenPct = 100 - ($onlPct + $offPct);
                        @endphp
                        <div class="stock-progress-bar" id="pbar_offline_{{ $p['id'] }}" title="Online: {{ $p['stock_online'] }} | POS: {{ $p['stock_offline'] }} | Central: {{ $p['stock_central'] }}">
                            <div class="bar-onl" style="width: {{ $onlPct }}%; background: #3B82F6;"></div>
                            <div class="bar-off" style="width: {{ $offPct }}%; background: #10B981;"></div>
                            <div class="bar-cen" style="width: {{ $cenPct }}%; background: #8B5CF6;"></div>
                        </div>

                        <!-- Card Transfer Actions -->
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.65rem; border-top: 1px dashed var(--color-border); padding-top: 0.45rem;">
                            <span class="text-xs text-muted">
                                {{ app()->getLocale() === 'ar' ? 'إجمالي الرصيد: ' : 'Total: ' }} <strong class="total-stock-display text-primary font-bold">{{ $p['total_stock'] }}</strong>
                            </span>
                            <div style="display: flex; gap: 0.35rem;">
                                <button type="button" class="btn btn-xs btn-outline" onclick="event.stopPropagation(); quickTransferPrompt({{ $p['id'] }}, 'offline', 'online', 5)" title="{{ app()->getLocale() === 'ar' ? 'تحويل 5 وحدات إلى الأونلاين' : 'Move 5 to Online' }}">
                                    ➔ Web (5)
                                </button>
                                <button type="button" class="btn btn-xs btn-ghost" onclick="event.stopPropagation(); openManualTransferModal({{ $p['id'] }}, 'offline')" title="{{ app()->getLocale() === 'ar' ? 'تحويل مخصص' : 'Custom Transfer' }}">
                                    <i class="fa-solid fa-arrows-split-up-and-left text-primary"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- HUB 3: Central Logistics Depot (central_wh) -->
        <div class="hub-column" id="hub_central_wh" data-location="central_wh" ondragover="handleDragOver(event)" ondragleave="handleDragLeave(event)" ondrop="handleDrop(event, 'central_wh')">
            <div class="hub-header hub-header-central">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 38px; height: 38px; border-radius: var(--radius-md); background: #8B5CF6; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.15rem;">
                        <i class="fa-solid fa-warehouse"></i>
                    </div>
                    <div>
                        <h4 style="margin: 0; font-size: 1.05rem; font-weight: 800; color: #5B21B6;">
                            {{ app()->getLocale() === 'ar' ? 'المستودع المركزي الاحتياطي' : 'Central Depot Buffer' }}
                        </h4>
                        <div class="text-xs text-muted" style="margin-top: 0.15rem;">
                            {{ app()->getLocale() === 'ar' ? 'مستودع الأمان وإعادة التوزيع' : 'Quarantine & master reserve' }}
                        </div>
                    </div>
                </div>
                <span class="badge badge-accent font-mono text-xs" id="hubTotalBadge_central_wh">
                    {{ number_format($kpis['central_units']) }} units
                </span>
            </div>

            <div class="hub-dropzone" id="dropzone_central_wh">
                @foreach($products as $p)
                    <div class="draggable-stock-card product-card-item" 
                         id="card_central_wh_{{ $p['id'] }}"
                         data-product-id="{{ $p['id'] }}"
                         data-current-location="central_wh"
                         data-category-id="{{ $p['category_id'] }}"
                         data-status="{{ $p['status'] }}"
                         data-search-text="{{ strtolower($p['name_en'] . ' ' . $p['name_ar'] . ' ' . $p['sku'] . ' ' . $p['barcode']) }}"
                         draggable="true" 
                         ondragstart="handleDragStart(event, {{ $p['id'] }}, 'central_wh')">
                        
                        <div style="display: flex; gap: 0.75rem; align-items: flex-start;">
                            <input type="checkbox" class="batch-product-checkbox form-check-input" value="{{ $p['id'] }}" onclick="event.stopPropagation(); updateBatchCount();">
                            <img src="{{ $p['image'] }}" alt="{{ $p['name_en'] }}" style="width: 48px; height: 48px; border-radius: var(--radius-md); object-fit: cover; background: var(--color-bg-subtle);" onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';">
                            <div style="flex: 1; min-width: 0;">
                                <div style="display: flex; justify-content: space-between; align-items: start; gap: 0.5rem;">
                                    <div class="font-bold text-sm" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        {{ app()->getLocale() === 'ar' ? $p['name_ar'] : $p['name_en'] }}
                                    </div>
                                    <span class="font-mono text-xs font-black text-purple-600 dark:text-purple-400 stock-qty-display" id="stock_central_{{ $p['id'] }}">
                                        {{ $p['stock_central'] }} {{ app()->getLocale() === 'ar' ? 'وحدة' : 'units' }}
                                    </span>
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.25rem;">
                                    <span class="font-mono text-xs text-muted">{{ $p['sku'] }}</span>
                                    <span class="badge badge-neutral text-xs">
                                        @currency($p['price'])
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- All-Hubs Live Breakdown Pills -->
                        <div class="hub-breakdown-pills">
                            <div class="hub-pill-item">
                                <span class="text-blue-600">🌐 Web</span>
                                <strong id="pill_central_{{ $p['id'] }}_online">{{ $p['stock_online'] }}</strong>
                            </div>
                            <div class="hub-pill-item">
                                <span class="text-emerald-600">🏬 POS</span>
                                <strong id="pill_central_{{ $p['id'] }}_offline">{{ $p['stock_offline'] }}</strong>
                            </div>
                            <div class="hub-pill-item active-hub">
                                <span class="text-purple-600 font-bold">🏭 Cent.</span>
                                <strong id="pill_central_{{ $p['id'] }}_central">{{ $p['stock_central'] }}</strong>
                            </div>
                        </div>

                        <!-- Mini progress representation -->
                        @php
                            $totalP = max(1, $p['total_stock']);
                            $onlPct = round(($p['stock_online'] / $totalP) * 100);
                            $offPct = round(($p['stock_offline'] / $totalP) * 100);
                            $cenPct = 100 - ($onlPct + $offPct);
                        @endphp
                        <div class="stock-progress-bar" id="pbar_central_wh_{{ $p['id'] }}" title="Online: {{ $p['stock_online'] }} | POS: {{ $p['stock_offline'] }} | Central: {{ $p['stock_central'] }}">
                            <div class="bar-onl" style="width: {{ $onlPct }}%; background: #3B82F6;"></div>
                            <div class="bar-off" style="width: {{ $offPct }}%; background: #10B981;"></div>
                            <div class="bar-cen" style="width: {{ $cenPct }}%; background: #8B5CF6;"></div>
                        </div>

                        <!-- Card Transfer Actions -->
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.65rem; border-top: 1px dashed var(--color-border); padding-top: 0.45rem;">
                            <span class="text-xs text-muted">
                                {{ app()->getLocale() === 'ar' ? 'إجمالي الرصيد: ' : 'Total: ' }} <strong class="total-stock-display text-primary font-bold">{{ $p['total_stock'] }}</strong>
                            </span>
                            <div style="display: flex; gap: 0.35rem;">
                                <button type="button" class="btn btn-xs btn-outline" onclick="event.stopPropagation(); quickTransferPrompt({{ $p['id'] }}, 'central_wh', 'offline', 10)" title="{{ app()->getLocale() === 'ar' ? 'تزويد المعرض بـ 10 وحدات' : 'Supply 10 to POS' }}">
                                    ➔ POS (10)
                                </button>
                                <button type="button" class="btn btn-xs btn-ghost" onclick="event.stopPropagation(); openManualTransferModal({{ $p['id'] }}, 'central_wh')" title="{{ app()->getLocale() === 'ar' ? 'تحويل مخصص' : 'Custom Transfer' }}">
                                    <i class="fa-solid fa-arrows-split-up-and-left text-primary"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    <!-- 4. Real-time Live Inventory Movement & Transfer Audit Ledger Table -->
    <div class="card" style="padding: 1.5rem; margin-top: 1rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--color-border); padding-bottom: 0.75rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <i class="fa-solid fa-clock-rotate-left text-primary" style="font-size: 1.15rem;"></i>
                <h3 style="margin: 0; font-size: 1.15rem; font-weight: 800;">
                    {{ app()->getLocale() === 'ar' ? 'سجل تحويلات وحركات المخزون الحية (Live Movement Ledger)' : 'Live Inventory Relocation Audit Ledger' }}
                </h3>
            </div>
            <span class="badge badge-success text-xs font-mono">
                <i class="fa-solid fa-circle-dot mr-1 ml-1 text-emerald-500 fa-fade"></i> {{ app()->getLocale() === 'ar' ? 'تحديث مباشر لحظي' : 'Real-time Live Sync' }}
            </span>
        </div>

        <div class="table-responsive">
            <table class="table" id="liveMovementTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'الوقت والتاريخ' : 'Time & Date' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'المنتج / SKU' : 'Product / SKU' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'نوع الحركة' : 'Movement Type' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'من (المصدر)' : 'From Location' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'إلى (الوجهة)' : 'To Location' }}</th>
                        <th style="text-align: center;">{{ app()->getLocale() === 'ar' ? 'الكمية المحولة' : 'Transferred Qty' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'المسؤول' : 'User' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'الملاحظة' : 'Note' }}</th>
                    </tr>
                </thead>
                <tbody id="liveMovementTableBody">
                    @forelse($recentMovements as $m)
                        <tr>
                            <td class="font-mono text-xs">#{{ $m->id }}</td>
                            <td class="text-xs text-muted">{{ $m->time }} <span class="text-gray-400">({{ $m->date }})</span></td>
                            <td>
                                <div class="font-bold text-sm">{{ app()->getLocale() === 'ar' && !empty($m->product_name_ar) ? $m->product_name_ar : $m->product_name_en }}</div>
                                <div class="font-mono text-xs text-muted">{{ $m->sku }}</div>
                            </td>
                            <td>
                                <span class="badge badge-primary text-xs">{{ $m->movement_type }}</span>
                            </td>
                            <td>
                                <span class="badge badge-neutral text-xs">{{ $m->from_location }}</span>
                            </td>
                            <td>
                                <span class="badge badge-success text-xs">{{ $m->to_location }}</span>
                            </td>
                            <td style="text-align: center;">
                                <strong class="font-mono text-sm text-primary">{{ $m->quantity }} {{ app()->getLocale() === 'ar' ? 'وحدة' : 'units' }}</strong>
                            </td>
                            <td class="text-xs">{{ $m->user }}</td>
                            <td class="text-xs text-muted">{{ $m->note }}</td>
                        </tr>
                    @empty
                        <tr id="emptyMovementRow">
                            <td colspan="9" class="text-center py-6 text-muted">
                                {{ app()->getLocale() === 'ar' ? 'لا توجد حركات مسجلة حالياً' : 'No recent movements recorded yet' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- 5. Interactive Drag & Drop Transfer Prompt Modal -->
    <div id="transferPromptModal" class="pos-modal-backdrop" style="display: none; z-index: 10000; position: fixed; inset: 0; background: rgba(10, 17, 40, 0.75); backdrop-filter: blur(5px); align-items: center; justify-content: center; padding: 1rem;">
        <div class="card" style="max-width: 480px; width: 95%; padding: 1.75rem; border-radius: var(--radius-xl); background: #fff; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); border: 1px solid var(--color-border);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--color-border); padding-bottom: 0.75rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fa-solid fa-arrow-right-arrow-left text-primary" style="font-size: 1.15rem;"></i>
                    <h3 style="margin: 0; font-size: 1.15rem; font-weight: 800;">
                        {{ app()->getLocale() === 'ar' ? 'تحويل رصيد المخزون الفوري' : 'Stock Transfer Allocation' }}
                    </h3>
                </div>
                <button type="button" class="btn btn-sm btn-ghost" onclick="closeTransferModal()">✕</button>
            </div>

            <!-- Product Header Summary -->
            <div style="display: flex; gap: 1rem; align-items: center; background: var(--color-bg-subtle); padding: 0.85rem 1rem; border-radius: var(--radius-md); border: 1px solid var(--color-border); margin-bottom: 1.25rem;">
                <img id="modalProductImg" src="" alt="Product" style="width: 50px; height: 50px; border-radius: var(--radius-md); object-fit: cover; background: #fff;">
                <div style="flex: 1; min-width: 0;">
                    <h4 id="modalProductName" style="margin: 0; font-size: 0.95rem; font-weight: 800;"></h4>
                    <div style="display: flex; gap: 0.5rem; margin-top: 0.25rem; font-size: 0.75rem;" class="text-muted">
                        <span id="modalProductSku" class="font-mono"></span>
                        <span>•</span>
                        <span>{{ app()->getLocale() === 'ar' ? 'الرصيد المتاح بالمصدر: ' : 'Available in Source: ' }}<strong id="modalAvailableStock" class="text-primary font-black">0</strong></span>
                    </div>
                </div>
            </div>

            <!-- Route Visualizer -->
            <div style="display: grid; grid-template-columns: 1fr auto 1fr; gap: 0.75rem; align-items: center; margin-bottom: 1.25rem; text-align: center;">
                <div style="background: rgba(59, 130, 246, 0.08); padding: 0.75rem; border-radius: var(--radius-md); border: 1px solid rgba(59, 130, 246, 0.2);">
                    <div class="text-xs text-muted font-bold" style="text-transform: uppercase;">{{ app()->getLocale() === 'ar' ? 'من (المصدر)' : 'From Source' }}</div>
                    <div class="font-bold text-sm text-primary" id="modalFromLocationName" style="margin-top: 0.25rem;"></div>
                </div>
                <div style="font-size: 1.25rem; color: var(--color-primary);">
                    <i class="fa-solid fa-arrow-right"></i>
                </div>
                <div style="background: rgba(16, 185, 129, 0.08); padding: 0.75rem; border-radius: var(--radius-md); border: 1px solid rgba(16, 185, 129, 0.2);">
                    <div class="text-xs text-muted font-bold" style="text-transform: uppercase;">{{ app()->getLocale() === 'ar' ? 'إلى (الوجهة)' : 'To Destination' }}</div>
                    <div class="font-bold text-sm text-success" id="modalToLocationName" style="margin-top: 0.25rem;"></div>
                </div>
            </div>

            <!-- Quantity Input & Slider -->
            <div style="margin-bottom: 1.25rem;">
                <label class="form-label font-bold text-xs" style="margin-bottom: 0.5rem; display: flex; justify-content: space-between;">
                    <span>{{ app()->getLocale() === 'ar' ? 'الكمية المراد تحويلها:' : 'Quantity to Transfer:' }}</span>
                    <span class="font-mono text-primary font-black" id="modalQtyDisplay">1 units</span>
                </label>
                
                <input type="range" 
                       id="modalQtyRange" 
                       min="1" 
                       max="100" 
                       value="1" 
                       style="width: 100%; accent-color: var(--color-primary); cursor: pointer;"
                       oninput="syncTransferQty(this.value)">

                <div style="display: flex; gap: 0.5rem; justify-content: center; margin-top: 0.75rem; flex-wrap: wrap;">
                    <span class="chip-preset" onclick="setTransferQty(1)">1</span>
                    <span class="chip-preset" onclick="setTransferQty(5)">5</span>
                    <span class="chip-preset" onclick="setTransferQty(10)">10</span>
                    <span class="chip-preset" onclick="setTransferPercentage(25)">25%</span>
                    <span class="chip-preset" onclick="setTransferPercentage(50)">50%</span>
                    <span class="chip-preset" onclick="setTransferPercentage(100)">{{ app()->getLocale() === 'ar' ? 'الكل (100%)' : 'Max All' }}</span>
                </div>
            </div>

            <!-- Notes -->
            <div style="margin-bottom: 1.25rem;">
                <input type="text" 
                       id="modalTransferReason" 
                       class="form-input" 
                       placeholder="{{ app()->getLocale() === 'ar' ? 'ملاحظة اختيارية (مثل: تزويد المستودع...)' : 'Optional reason or note...' }}" 
                       value="Visual Drag & Drop Rebalancing">
            </div>

            <!-- Submit Buttons -->
            <div style="display: flex; gap: 0.75rem;">
                <button type="button" class="btn btn-secondary" onclick="closeTransferModal()" style="flex: 1;">
                    {{ __('app.actions.cancel') }}
                </button>
                <button type="button" class="btn btn-primary" id="btnExecuteTransfer" onclick="submitAjaxTransfer()" style="flex: 2; font-weight: 800;">
                    <i class="fa-solid fa-check mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'تأكيد ونقل الكمية فوراً' : 'Confirm Transfer' }}
                </button>
            </div>
        </div>
    </div>

    <!-- 6. Batch Ratio Split & Rebalance Modal -->
    <div id="batchSplitModal" class="pos-modal-backdrop" style="display: none; z-index: 10000; position: fixed; inset: 0; background: rgba(10, 17, 40, 0.75); backdrop-filter: blur(5px); align-items: center; justify-content: center; padding: 1rem;">
        <div class="card" style="max-width: 520px; width: 95%; padding: 1.75rem; border-radius: var(--radius-xl); background: #fff; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); border: 1px solid var(--color-border);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--color-border); padding-bottom: 0.75rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fa-solid fa-chart-pie text-primary" style="font-size: 1.15rem;"></i>
                    <h3 style="margin: 0; font-size: 1.15rem; font-weight: 800;">
                        {{ app()->getLocale() === 'ar' ? 'إعادة التوزيع والتقسيم بالنسب المئوية' : 'Batch Ratio Allocation & Split' }}
                    </h3>
                </div>
                <button type="button" class="btn btn-sm btn-ghost" onclick="closeBatchSplitModal()">✕</button>
            </div>

            <p class="text-xs text-muted" style="margin: 0 0 1.25rem 0;">
                {{ app()->getLocale() === 'ar' 
                   ? 'حدد نسب توزيع المخزون عبر القنوات. سيتم احتساب وتقسيم رصيد كل منتج محدد بدقة وتسجيل الحركات في السجل المركزي.' 
                   : 'Set percentage ratios across distribution hubs. Selected products will be dynamically rebalanced.' }}
            </p>

            <!-- Preset Templates -->
            <div style="margin-bottom: 1.25rem;">
                <label class="form-label font-bold text-xs" style="margin-bottom: 0.5rem;">{{ app()->getLocale() === 'ar' ? 'القوالب الجاهزة:' : 'Quick Presets:' }}</label>
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.5rem;">
                    <button type="button" class="btn btn-xs btn-outline" onclick="applyRatioPreset(50, 50, 0)">
                        50% Web / 50% POS
                    </button>
                    <button type="button" class="btn btn-xs btn-outline" onclick="applyRatioPreset(70, 30, 0)">
                        70% Web / 30% POS
                    </button>
                    <button type="button" class="btn btn-xs btn-outline" onclick="applyRatioPreset(60, 30, 10)">
                        60 / 30 / 10%
                    </button>
                </div>
            </div>

            <!-- Custom Ratio Sliders -->
            <div style="display: flex; flex-direction: column; gap: 1rem; background: var(--color-bg-subtle); padding: 1.25rem; border-radius: var(--radius-lg); border: 1px solid var(--color-border); margin-bottom: 1.25rem;">
                <!-- Online Slider -->
                <div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.8rem; font-weight: 800; color: #2563EB; margin-bottom: 0.25rem;">
                        <span>🌐 {{ app()->getLocale() === 'ar' ? 'المتجر الإلكتروني (Online):' : 'Online Storefront:' }}</span>
                        <span id="batchOnlinePctDisplay" class="font-mono">50%</span>
                    </div>
                    <input type="range" id="batchOnlineRange" min="0" max="100" value="50" style="width: 100%; accent-color: #2563EB;" oninput="updateBatchRatioSliders('online')">
                </div>

                <!-- Offline POS Slider -->
                <div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.8rem; font-weight: 800; color: #059669; margin-bottom: 0.25rem;">
                        <span>🏬 {{ app()->getLocale() === 'ar' ? 'مستودع المبيعات (POS):' : 'POS Warehouse:' }}</span>
                        <span id="batchOfflinePctDisplay" class="font-mono">50%</span>
                    </div>
                    <input type="range" id="batchOfflineRange" min="0" max="100" value="50" style="width: 100%; accent-color: #059669;" oninput="updateBatchRatioSliders('offline')">
                </div>

                <!-- Central Buffer Slider -->
                <div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.8rem; font-weight: 800; color: #7C3AED; margin-bottom: 0.25rem;">
                        <span>🏭 {{ app()->getLocale() === 'ar' ? 'المستودع المركزي (Central Buffer):' : 'Central Depot Buffer:' }}</span>
                        <span id="batchCentralPctDisplay" class="font-mono">0%</span>
                    </div>
                    <input type="range" id="batchCentralRange" min="0" max="100" value="0" style="width: 100%; accent-color: #7C3AED;" oninput="updateBatchRatioSliders('central')">
                </div>

                <!-- Sum Indicator -->
                <div style="display: flex; justify-content: space-between; font-size: 0.8rem; border-top: 1px dashed var(--color-border); padding-top: 0.5rem;">
                    <span>{{ app()->getLocale() === 'ar' ? 'إجمالي النسب:' : 'Total Sum:' }}</span>
                    <strong id="batchRatioSumBadge" class="font-mono text-success font-black">100%</strong>
                </div>
            </div>

            <!-- Target Summary -->
            <div style="margin-bottom: 1.25rem; font-size: 0.8rem;" class="text-muted">
                {{ app()->getLocale() === 'ar' ? 'سيتم تطبيق التوزيع على: ' : 'Applying to: ' }} <strong id="modalBatchSelectedCount" class="text-primary font-black">0</strong> {{ app()->getLocale() === 'ar' ? 'منتجات محددة' : 'selected products' }}
            </div>

            <!-- Submit -->
            <div style="display: flex; gap: 0.75rem;">
                <button type="button" class="btn btn-secondary" onclick="closeBatchSplitModal()" style="flex: 1;">
                    {{ __('app.actions.cancel') }}
                </button>
                <button type="button" class="btn btn-primary" id="btnSubmitBatchSplit" onclick="submitBatchSplit()" style="flex: 2; font-weight: 800;">
                    <i class="fa-solid fa-play mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'تطبيق وإعادة التوزيع' : 'Execute Batch Rebalance' }}
                </button>
            </div>
        </div>
    </div>

    <!-- Client-side Interactive Allocator Engine Script -->
    <script>
        const productsData = @json($products);
        const csrfToken = '{{ csrf_token() }}';
        const isAr = {{ app()->getLocale() === 'ar' ? 'true' : 'false' }};

        const locationNames = {
            online: isAr ? 'مستودع الأونلاين (Online Hub)' : 'Online Store Hub',
            offline: isAr ? 'مستودع المبيعات (POS Warehouse)' : 'POS Warehouse',
            central_wh: isAr ? 'المستودع المركزي (Central Depot)' : 'Central Depot Buffer'
        };

        let currentTransferData = {
            productId: null,
            fromLocation: null,
            toLocation: null,
            availableStock: 0,
            quantity: 1
        };

        function getProductCurrentStock(productId, location) {
            const p = productsData.find(item => item.id === productId);
            if (!p) return 0;
            if (location === 'online') return parseInt(p.stock_online) || 0;
            if (location === 'offline') return parseInt(p.stock_offline) || 0;
            if (location === 'central_wh') return parseInt(p.stock_central) || 0;
            return 0;
        }

        // --- Drag & Drop Handlers ---
        function handleDragStart(e, productId, fromLocation) {
            const currentStock = getProductCurrentStock(productId, fromLocation);
            currentTransferData.productId = productId;
            currentTransferData.fromLocation = fromLocation;
            currentTransferData.availableStock = currentStock;

            e.dataTransfer.setData('text/plain', JSON.stringify({
                productId: productId,
                fromLocation: fromLocation,
                availableStock: currentStock
            }));
            e.dataTransfer.effectAllowed = 'move';

            const card = document.getElementById(`card_${fromLocation}_${productId}`);
            if (card) {
                card.classList.add('is-dragging');
            }
        }

        document.addEventListener('dragend', function(e) {
            document.querySelectorAll('.draggable-stock-card').forEach(c => c.classList.remove('is-dragging'));
            document.querySelectorAll('.hub-column').forEach(h => h.classList.remove('drag-over'));
        });

        function handleDragOver(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            e.currentTarget.classList.add('drag-over');
        }

        function handleDragLeave(e) {
            e.currentTarget.classList.remove('drag-over');
        }

        function handleDrop(e, toLocation) {
            e.preventDefault();
            e.currentTarget.classList.remove('drag-over');

            const fromLocation = currentTransferData.fromLocation;
            const productId = currentTransferData.productId;

            if (!productId || !fromLocation) return;
            if (fromLocation === toLocation) {
                if (window.toast) {
                    window.toast.warning(isAr ? 'الموقع الوجهة هو نفس موقع المصدر.' : 'Destination hub is the same as source.');
                }
                return;
            }

            openTransferModal(productId, fromLocation, toLocation);
        }

        // --- Open Transfer Modal ---
        function openTransferModal(productId, fromLocation, toLocation) {
            const product = productsData.find(p => p.id === productId);
            if (!product) return;

            const available = getProductCurrentStock(productId, fromLocation);

            if (available <= 0) {
                if (window.toast) {
                    window.toast.error(isAr ? 'لا يوجد رصيد متاح في هذا الموقع للتحويل.' : 'No stock available in source location to transfer.');
                }
                return;
            }

            currentTransferData = {
                productId: productId,
                fromLocation: fromLocation,
                toLocation: toLocation,
                availableStock: available,
                quantity: Math.min(available, 5)
            };

            document.getElementById('modalProductImg').src = product.image;
            document.getElementById('modalProductName').innerText = isAr ? product.name_ar : product.name_en;
            document.getElementById('modalProductSku').innerText = product.sku;
            document.getElementById('modalAvailableStock').innerText = available + (isAr ? ' وحدة' : ' units');

            document.getElementById('modalFromLocationName').innerText = locationNames[fromLocation] || fromLocation;
            document.getElementById('modalToLocationName').innerText = locationNames[toLocation] || toLocation;

            const range = document.getElementById('modalQtyRange');
            range.max = available;
            range.value = currentTransferData.quantity;
            syncTransferQty(currentTransferData.quantity);

            document.getElementById('transferPromptModal').style.display = 'flex';
        }

        function closeTransferModal() {
            document.getElementById('transferPromptModal').style.display = 'none';
        }

        function syncTransferQty(val) {
            const qty = Math.min(Math.max(1, parseInt(val) || 1), currentTransferData.availableStock);
            currentTransferData.quantity = qty;
            document.getElementById('modalQtyRange').value = qty;
            document.getElementById('modalQtyDisplay').innerText = qty + (isAr ? ' وحدة' : ' units');
        }

        function setTransferQty(num) {
            syncTransferQty(num);
        }

        function setTransferPercentage(pct) {
            const qty = Math.max(1, Math.round((pct / 100) * currentTransferData.availableStock));
            syncTransferQty(qty);
        }

        function quickTransferPrompt(productId, fromLocation, toLocation, qty) {
            const product = productsData.find(p => p.id === productId);
            if (!product) return;

            const available = getProductCurrentStock(productId, fromLocation);
            const transferUnits = Math.min(available, qty);
            
            if (transferUnits <= 0) {
                if (window.toast) window.toast.error(isAr ? 'الرصيد المتاح غير كافٍ.' : 'Insufficient available stock.');
                return;
            }

            executeTransferRequest(productId, fromLocation, toLocation, transferUnits, 'Quick Allocation Stepper');
        }

        function openManualTransferModal(productId, fromLocation) {
            const target = fromLocation === 'online' ? 'offline' : 'online';
            openTransferModal(productId, fromLocation, target);
        }

        function submitAjaxTransfer() {
            const btn = document.getElementById('btnExecuteTransfer');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1 ml-1"></i> ' + (isAr ? 'جاري النقل...' : 'Transferring...');

            const reason = document.getElementById('modalTransferReason').value || 'Visual Drag & Drop Rebalance';

            executeTransferRequest(
                currentTransferData.productId,
                currentTransferData.fromLocation,
                currentTransferData.toLocation,
                currentTransferData.quantity,
                reason,
                () => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa-solid fa-check mr-1 ml-1"></i> ' + (isAr ? 'تأكيد ونقل الكمية فوراً' : 'Confirm Transfer');
                    closeTransferModal();
                }
            );
        }

        function executeTransferRequest(productId, fromLocation, toLocation, quantity, reason, onComplete) {
            fetch('{{ route("admin.inventory.allocator.transfer") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId,
                    from_location: fromLocation,
                    to_location: toLocation,
                    quantity: quantity,
                    reason: reason
                })
            })
            .then(res => res.json())
            .then(data => {
                if (onComplete) onComplete();
                if (data.success) {
                    if (window.toast) window.toast.success(data.message, isAr ? 'نجاح التحويل والخصم' : 'Stock Allocated');
                    updateProductCardUI(data.product, fromLocation, toLocation, quantity);
                    if (data.movement) prependMovementToLedger(data.movement);
                    if (data.kpis) updateKpiUI(data.kpis);
                } else {
                    if (window.toast) window.toast.error(data.message || (isAr ? 'حدث خطأ أثناء التحويل' : 'Transfer failed'));
                }
            })
            .catch(err => {
                if (onComplete) onComplete();
                if (window.toast) window.toast.error(isAr ? 'تعذر الاتصال بالخادم' : 'Network error');
            });
        }

        function updateProductCardUI(updatedProd, fromLoc, toLoc, qty) {
            const p = productsData.find(item => item.id === updatedProd.id);
            if (p) {
                p.stock_online = updatedProd.stock_online;
                p.stock_offline = updatedProd.stock_offline;
                p.stock_central = updatedProd.stock_central;
                p.total_stock = updatedProd.total_stock;
            }

            // Update main quantity labels
            const onlDisp = document.getElementById(`stock_online_${updatedProd.id}`);
            const offDisp = document.getElementById(`stock_offline_${updatedProd.id}`);
            const cenDisp = document.getElementById(`stock_central_${updatedProd.id}`);

            if (onlDisp) onlDisp.innerText = updatedProd.stock_online + (isAr ? ' وحدة' : ' units');
            if (offDisp) offDisp.innerText = updatedProd.stock_offline + (isAr ? ' وحدة' : ' units');
            if (cenDisp) cenDisp.innerText = updatedProd.stock_central + (isAr ? ' وحدة' : ' units');

            // Update all 3 location pill breakdowns across every hub card
            ['online', 'offline', 'central'].forEach(hubPrefix => {
                const pOnl = document.getElementById(`pill_${hubPrefix}_${updatedProd.id}_online`);
                const pOff = document.getElementById(`pill_${hubPrefix}_${updatedProd.id}_offline`);
                const pCen = document.getElementById(`pill_${hubPrefix}_${updatedProd.id}_central`);

                if (pOnl) pOnl.innerText = updatedProd.stock_online;
                if (pOff) pOff.innerText = updatedProd.stock_offline;
                if (pCen) pCen.innerText = updatedProd.stock_central;
            });

            // Update progress bars
            const total = Math.max(1, updatedProd.total_stock);
            const onlPct = Math.round((updatedProd.stock_online / total) * 100);
            const offPct = Math.round((updatedProd.stock_offline / total) * 100);
            const cenPct = Math.max(0, 100 - (onlPct + offPct));

            ['online', 'offline', 'central_wh'].forEach(loc => {
                const pbar = document.getElementById(`pbar_${loc}_${updatedProd.id}`);
                if (pbar) {
                    const bOnl = pbar.querySelector('.bar-onl');
                    const bOff = pbar.querySelector('.bar-off');
                    const bCen = pbar.querySelector('.bar-cen');
                    if (bOnl) bOnl.style.width = onlPct + '%';
                    if (bOff) bOff.style.width = offPct + '%';
                    if (bCen) bCen.style.width = cenPct + '%';
                }
                const card = document.getElementById(`card_${loc}_${updatedProd.id}`);
                if (card) {
                    const totEl = card.querySelector('.total-stock-display');
                    if (totEl) totEl.innerText = updatedProd.total_stock;
                }
            });

            // Trigger visual pulse animation on affected source & destination cards
            if (fromLoc) {
                const srcCard = document.getElementById(`card_${fromLoc}_${updatedProd.id}`);
                if (srcCard) {
                    srcCard.classList.remove('pulse-deduct');
                    void srcCard.offsetWidth;
                    srcCard.classList.add('pulse-deduct');
                }
            }
            if (toLoc) {
                const dstCard = document.getElementById(`card_${toLoc}_${updatedProd.id}`);
                if (dstCard) {
                    dstCard.classList.remove('pulse-add');
                    void dstCard.offsetWidth;
                    dstCard.classList.add('pulse-add');
                }
            }
        }

        function prependMovementToLedger(m) {
            const tbody = document.getElementById('liveMovementTableBody');
            const emptyRow = document.getElementById('emptyMovementRow');
            if (emptyRow) emptyRow.remove();

            const tr = document.createElement('tr');
            tr.className = 'new-movement-row';
            tr.innerHTML = `
                <td class="font-mono text-xs">#${m.id}</td>
                <td class="text-xs text-muted">${m.time} <span class="text-emerald-600 font-bold">(${isAr ? 'الآن' : 'just now'})</span></td>
                <td>
                    <div class="font-bold text-sm">${m.product_name}</div>
                    <div class="font-mono text-xs text-muted">${m.sku}</div>
                </td>
                <td><span class="badge badge-primary text-xs">Stock Transfer</span></td>
                <td><span class="badge badge-neutral text-xs">${m.from}</span></td>
                <td><span class="badge badge-success text-xs">${m.to}</span></td>
                <td style="text-align: center;"><strong class="font-mono text-sm text-primary">${m.quantity} ${isAr ? 'وحدة' : 'units'}</strong></td>
                <td class="text-xs">${m.user || 'Admin'}</td>
                <td class="text-xs text-muted">${m.note || ''}</td>
            `;

            tbody.insertBefore(tr, tbody.firstChild);
        }

        function updateKpiUI(kpis) {
            document.getElementById('kpiTotalUnits').innerHTML = Number(kpis.total_units).toLocaleString() + ` <span class="text-xs text-muted font-normal">${isAr ? 'وحدة' : 'units'}</span>`;
            document.getElementById('kpiOnlineUnits').innerHTML = Number(kpis.online_units).toLocaleString() + ` <span class="text-xs text-muted font-normal">${isAr ? 'وحدة' : 'units'}</span>`;
            document.getElementById('kpiOfflineUnits').innerHTML = Number(kpis.offline_units).toLocaleString() + ` <span class="text-xs text-muted font-normal">${isAr ? 'وحدة' : 'units'}</span>`;
            document.getElementById('kpiCentralUnits').innerHTML = Number(kpis.central_units).toLocaleString() + ` <span class="text-xs text-muted font-normal">${isAr ? 'وحدة' : 'units'}</span>`;

            document.getElementById('kpiOnlinePct').innerText = kpis.online_pct + '%';
            document.getElementById('kpiOfflinePct').innerText = kpis.offline_pct + '%';
            document.getElementById('kpiCentralPct').innerText = kpis.central_pct + '%';

            document.getElementById('hubTotalBadge_online').innerText = Number(kpis.online_units).toLocaleString() + ' units';
            document.getElementById('hubTotalBadge_offline').innerText = Number(kpis.offline_units).toLocaleString() + ' units';
            document.getElementById('hubTotalBadge_central_wh').innerText = Number(kpis.central_units).toLocaleString() + ' units';
        }

        // --- Filtering Engine ---
        function filterAllocatorCards() {
            const query = (document.getElementById('allocatorSearchInput')?.value || '').toLowerCase().trim();
            const cat = document.getElementById('categoryFilterSelect')?.value || 'all';
            const status = document.getElementById('stockStatusSelect')?.value || 'all';

            document.querySelectorAll('.product-card-item').forEach(card => {
                const text = card.getAttribute('data-search-text') || '';
                const cardCat = card.getAttribute('data-category-id') || '';
                const cardStatus = card.getAttribute('data-status') || '';

                const matchesQuery = !query || text.includes(query);
                const matchesCat = cat === 'all' || cardCat === cat;
                const matchesStatus = status === 'all' || cardStatus === status;

                if (matchesQuery && matchesCat && matchesStatus) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // --- Batch Rebalance Logic ---
        function updateBatchCount() {
            const checked = document.querySelectorAll('.batch-product-checkbox:checked');
            const count = checked.length;
            document.getElementById('selectedCountBadge').innerText = count;
            document.getElementById('modalBatchSelectedCount').innerText = count;
        }

        function toggleSelectAllProducts() {
            const checkboxes = document.querySelectorAll('.batch-product-checkbox');
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            checkboxes.forEach(cb => cb.checked = !allChecked);
            updateBatchCount();
            document.getElementById('btnSelectAllText').innerText = allChecked 
                ? (isAr ? 'تحديد الكل للتقسيم' : 'Select All')
                : (isAr ? 'إلغاء تحديد الكل' : 'Deselect All');
        }

        function openBatchSplitModal() {
            const checked = document.querySelectorAll('.batch-product-checkbox:checked');
            if (checked.length === 0) {
                document.querySelectorAll('.product-card-item[style*="display: block"], .product-card-item:not([style*="display: none"]) .batch-product-checkbox').forEach(cb => cb.checked = true);
                updateBatchCount();
            }
            document.getElementById('batchSplitModal').style.display = 'flex';
        }

        function closeBatchSplitModal() {
            document.getElementById('batchSplitModal').style.display = 'none';
        }

        function applyRatioPreset(onl, off, cen) {
            document.getElementById('batchOnlineRange').value = onl;
            document.getElementById('batchOfflineRange').value = off;
            document.getElementById('batchCentralRange').value = cen;

            document.getElementById('batchOnlinePctDisplay').innerText = onl + '%';
            document.getElementById('batchOfflinePctDisplay').innerText = off + '%';
            document.getElementById('batchCentralPctDisplay').innerText = cen + '%';

            validateBatchRatioSum();
        }

        function updateBatchRatioSliders(changed) {
            const onl = parseInt(document.getElementById('batchOnlineRange').value) || 0;
            const off = parseInt(document.getElementById('batchOfflineRange').value) || 0;
            const cen = parseInt(document.getElementById('batchCentralRange').value) || 0;

            document.getElementById('batchOnlinePctDisplay').innerText = onl + '%';
            document.getElementById('batchOfflinePctDisplay').innerText = off + '%';
            document.getElementById('batchCentralPctDisplay').innerText = cen + '%';

            validateBatchRatioSum();
        }

        function validateBatchRatioSum() {
            const onl = parseInt(document.getElementById('batchOnlineRange').value) || 0;
            const off = parseInt(document.getElementById('batchOfflineRange').value) || 0;
            const cen = parseInt(document.getElementById('batchCentralRange').value) || 0;
            const sum = onl + off + cen;

            const badge = document.getElementById('batchRatioSumBadge');
            const submitBtn = document.getElementById('btnSubmitBatchSplit');
            badge.innerText = sum + '%';

            if (sum === 100) {
                badge.className = 'font-mono text-success font-black';
                submitBtn.disabled = false;
            } else {
                badge.className = 'font-mono text-danger font-black';
                submitBtn.disabled = true;
            }
        }

        function submitBatchSplit() {
            const checkedBoxes = document.querySelectorAll('.batch-product-checkbox:checked');
            const pIds = Array.from(new Set(Array.from(checkedBoxes).map(cb => parseInt(cb.value))));

            if (pIds.length === 0) {
                if (window.toast) window.toast.warning(isAr ? 'يرجى تحديد منتج واحد على الأقل.' : 'Please select at least one product.');
                return;
            }

            const onl = parseInt(document.getElementById('batchOnlineRange').value) || 0;
            const off = parseInt(document.getElementById('batchOfflineRange').value) || 0;
            const cen = parseInt(document.getElementById('batchCentralRange').value) || 0;

            const btn = document.getElementById('btnSubmitBatchSplit');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1 ml-1"></i> ' + (isAr ? 'جاري التطبيق...' : 'Applying...');

            fetch('{{ route("admin.inventory.allocator.batch_split") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    product_ids: pIds,
                    online_pct: onl,
                    offline_pct: off,
                    central_pct: cen,
                    reason: `Batch ratio split (${onl}% Online / ${off}% POS / ${cen}% Central)`
                })
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-play mr-1 ml-1"></i> ' + (isAr ? 'تطبيق وإعادة التوزيع' : 'Execute Batch Rebalance');
                closeBatchSplitModal();

                if (data.success) {
                    if (window.toast) window.toast.success(data.message, isAr ? 'نجاح إعادة التوزيع' : 'Batch Split Applied');
                    if (Array.isArray(data.products)) {
                        data.products.forEach(p => updateProductCardUI(p));
                    }
                    if (data.kpis) updateKpiUI(data.kpis);
                } else {
                    if (window.toast) window.toast.error(data.message || 'Error occurred');
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-play mr-1 ml-1"></i> ' + (isAr ? 'تطبيق وإعادة التوزيع' : 'Execute Batch Rebalance');
                if (window.toast) window.toast.error(isAr ? 'تعذر الاتصال بالخادم' : 'Network error');
            });
        }
    </script>
</x-layouts.admin>
