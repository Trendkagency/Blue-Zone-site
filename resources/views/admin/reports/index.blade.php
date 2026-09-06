<x-layouts.admin 
    :pageTitle="__('admin.reports.title')" 
    :pageSubtitle="__('admin.reports.subtitle')"
    :breadcrumbs="[__('admin.menu.reports') => route('admin.reports.index')]"
>
    <!-- Top Executive Command & Master Action Banner -->
    <div class="card no-print" style="padding: 1.5rem 1.75rem; margin-bottom: 2rem; background: linear-gradient(135deg, rgba(10, 79, 120, 0.08), rgba(42, 143, 194, 0.04)); border: 1px solid var(--color-primary-light, rgba(10, 79, 120, 0.2)); border-radius: 1rem;">
        <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1.25rem;">
            
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="width: 48px; height: 48px; border-radius: 12px; background: linear-gradient(135deg, #0A4F78, #2A8FC2); color: #FFF; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; box-shadow: 0 4px 12px rgba(10,79,120,0.25);">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
                <div>
                    <h2 style="font-size: 1.35rem; font-weight: 900; margin: 0; color: var(--color-text); letter-spacing: -0.3px;">
                        {{ __('admin.reports.title') }}
                    </h2>
                    <p style="font-size: 0.8125rem; color: var(--color-text-muted); margin: 0.25rem 0 0 0;">
                        {{ __('admin.reports.subtitle') }}
                    </p>
                </div>
            </div>

            <!-- Master Excel Dossier & Board Print Buttons -->
            <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                <!-- Full Master Excel Dossier Button -->
                <a href="{{ route('admin.reports.export', array_merge(request()->query(), ['type' => 'master', 'format' => 'excel'])) }}" 
                   class="btn btn-primary" 
                   style="background: linear-gradient(135deg, #0A4F78, #10B981); border: none; font-weight: 800; padding: 0.6rem 1.25rem; box-shadow: 0 4px 12px rgba(16,185,129,0.25);"
                   title="{{ __('admin.reports.export_excel_all') }}">
                    <i class="fa-solid fa-file-excel mr-1.5 ml-1.5 text-base"></i> {{ __('admin.reports.export_excel_all') }}
                </a>

                <!-- Dedicated Print Board Dossier -->
                <a href="{{ route('admin.reports.print', request()->query()) }}" 
                   target="_blank"
                   class="btn btn-secondary" 
                   style="font-weight: 800; padding: 0.6rem 1.15rem;"
                   title="{{ __('admin.reports.print_report') }}">
                    <i class="fa-solid fa-print mr-1.5 ml-1.5 text-primary text-base"></i> {{ __('admin.reports.print_report') }}
                </a>
            </div>

        </div>
    </div>

    <!-- Interactive Filter Toolbar -->
    <div class="card no-print" style="padding: 1.25rem 1.5rem; margin-bottom: 2rem; border: 1px solid var(--color-border);">
        <form action="{{ route('admin.reports.index') }}" method="GET" id="reportFilterForm">
            <div style="display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between; gap: 1rem;">
                
                <div style="display: flex; flex-wrap: wrap; align-items: flex-end; gap: 0.75rem; flex: 1;">
                    <!-- Date Range Preset -->
                    <div style="min-width: 160px;">
                        <label class="form-label text-xs font-bold mb-1" style="display: block; color: var(--color-text-muted);">
                            <i class="fa-regular fa-calendar mr-1 ml-1 text-primary"></i> {{ __('admin.reports.custom_range') }}
                        </label>
                        <select name="range" id="filterRange" class="form-select text-sm w-full" onchange="toggleCustomDates(this.value); this.form.submit();">
                            <option value="today" {{ $filters['range'] === 'today' ? 'selected' : '' }}>{{ __('admin.reports.today') }}</option>
                            <option value="last_7_days" {{ $filters['range'] === 'last_7_days' ? 'selected' : '' }}>{{ __('admin.reports.last_7_days') }}</option>
                            <option value="last_30_days" {{ $filters['range'] === 'last_30_days' ? 'selected' : '' }}>{{ __('admin.reports.last_30_days') }}</option>
                            <option value="this_month" {{ $filters['range'] === 'this_month' ? 'selected' : '' }}>{{ __('admin.reports.this_month') }}</option>
                            <option value="last_month" {{ $filters['range'] === 'last_month' ? 'selected' : '' }}>{{ __('admin.reports.last_month') }}</option>
                            <option value="this_quarter" {{ $filters['range'] === 'this_quarter' ? 'selected' : '' }}>{{ __('admin.reports.this_quarter') }}</option>
                            <option value="year_to_date" {{ $filters['range'] === 'year_to_date' ? 'selected' : '' }}>{{ __('admin.reports.year_to_date') }}</option>
                            <option value="all_time" {{ $filters['range'] === 'all_time' ? 'selected' : '' }}>{{ __('admin.reports.all_time') }}</option>
                            <option value="custom" {{ $filters['range'] === 'custom' ? 'selected' : '' }}>{{ __('admin.reports.custom_range') }}...</option>
                        </select>
                    </div>

                    <!-- Channel Filter -->
                    <div style="min-width: 160px;">
                        <label class="form-label text-xs font-bold mb-1" style="display: block; color: var(--color-text-muted);">
                            <i class="fa-solid fa-store mr-1 ml-1 text-primary"></i> {{ __('admin.orders.channel') }}
                        </label>
                        <select name="channel" class="form-select text-sm w-full" onchange="this.form.submit();">
                            <option value="all" {{ $filters['channel'] === 'all' ? 'selected' : '' }}>{{ __('admin.reports.all_channels') }}</option>
                            <option value="online" {{ $filters['channel'] === 'online' ? 'selected' : '' }}>{{ __('admin.reports.online_only') }}</option>
                            <option value="offline" {{ $filters['channel'] === 'offline' ? 'selected' : '' }}>{{ __('admin.reports.pos_only') }}</option>
                        </select>
                    </div>

                    <!-- Longevity System Filter -->
                    <div style="min-width: 170px;">
                        <label class="form-label text-xs font-bold mb-1" style="display: block; color: var(--color-text-muted);">
                            <i class="fa-solid fa-dna mr-1 ml-1 text-primary"></i> {{ __('admin.menu.categories') }}
                        </label>
                        <select name="category_id" class="form-select text-sm w-full" onchange="this.form.submit();">
                            <option value="all" {{ $filters['category_id'] === 'all' ? 'selected' : '' }}>{{ __('admin.reports.all_systems') }}</option>
                            @foreach($allCategories as $category)
                                <option value="{{ $category->id }}" {{ (string)$filters['category_id'] === (string)$category->id ? 'selected' : '' }}>
                                    {{ app()->getLocale() === 'ar' ? ($category->name_ar ?? $category->name_en) : $category->name_en }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Custom Date Range inputs (Toggled) -->
                    <div id="customDateFields" style="display: {{ $filters['range'] === 'custom' ? 'flex' : 'none' }}; align-items: flex-end; gap: 0.5rem;">
                        <div>
                            <label class="form-label text-xs font-bold mb-1" style="display: block; color: var(--color-text-muted);">{{ __('admin.reports.from_date') }}</label>
                            <input type="date" name="start_date" value="{{ $filters['start_date'] }}" class="form-control text-sm" style="padding: 0.4rem 0.6rem;">
                        </div>
                        <div>
                            <label class="form-label text-xs font-bold mb-1" style="display: block; color: var(--color-text-muted);">{{ __('admin.reports.to_date') }}</label>
                            <input type="date" name="end_date" value="{{ $filters['end_date'] }}" class="form-control text-sm" style="padding: 0.4rem 0.6rem;">
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm" style="height: 38px;">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </div>
                </div>

                <!-- Right: Section Export Dropdown & Reset -->
                <div style="display: flex; align-items: center; gap: 0.6rem;">
                    @if($filters['range'] !== 'last_30_days' || $filters['channel'] !== 'all' || $filters['category_id'] !== 'all')
                        <a href="{{ route('admin.reports.index') }}" class="btn btn-ghost btn-sm text-danger" title="{{ __('admin.reports.reset_filters') }}">
                            <i class="fa-solid fa-rotate-left mr-1 ml-1"></i> {{ __('admin.reports.reset_filters') }}
                        </a>
                    @endif

                    <!-- Section Excel Export Dropdown -->
                    <div class="relative inline-block text-start" id="exportDropdownWrapper">
                        <button type="button" class="btn btn-secondary btn-sm font-bold" onclick="toggleExportMenu(event)">
                            <i class="fa-solid fa-file-excel mr-1.5 ml-1.5 text-success"></i> {{ __('admin.reports.export_excel') }}
                            <i class="fa-solid fa-chevron-down mr-1 ml-1 text-xs opacity-70"></i>
                        </button>

                        <div id="exportMenu" style="display: none; position: absolute; {{ app()->getLocale() === 'ar' ? 'left: 0;' : 'right: 0;' }} top: 110%; z-index: 50; width: 260px; background: var(--color-surface); border: 1px solid var(--color-border); border-radius: 0.75rem; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.2); padding: 0.5rem;">
                            <a href="{{ route('admin.reports.export', array_merge(request()->query(), ['type' => 'master', 'format' => 'excel'])) }}" class="admin-dropdown-item text-sm" style="padding: 0.5rem 0.75rem; display: flex; align-items: center; gap: 0.5rem; text-decoration: none; color: var(--color-primary); font-weight: 800; border-bottom: 1px solid var(--color-border); margin-bottom: 0.25rem;">
                                <i class="fa-solid fa-file-excel text-success" style="width: 16px;"></i>
                                <span>{{ __('admin.reports.export_excel_all') }}</span>
                            </a>
                            <a href="{{ route('admin.reports.export', array_merge(request()->query(), ['type' => 'sales', 'format' => 'excel'])) }}" class="admin-dropdown-item text-sm" style="padding: 0.45rem 0.75rem; display: flex; align-items: center; gap: 0.5rem; text-decoration: none; color: var(--color-text);">
                                <i class="fa-solid fa-receipt text-primary" style="width: 16px;"></i>
                                <span>{{ __('admin.reports.export_sales') }}</span>
                            </a>
                            <a href="{{ route('admin.reports.export', array_merge(request()->query(), ['type' => 'products', 'format' => 'excel'])) }}" class="admin-dropdown-item text-sm" style="padding: 0.45rem 0.75rem; display: flex; align-items: center; gap: 0.5rem; text-decoration: none; color: var(--color-text);">
                                <i class="fa-solid fa-dna text-accent" style="width: 16px;"></i>
                                <span>{{ __('admin.reports.export_products') }}</span>
                            </a>
                            <a href="{{ route('admin.reports.export', array_merge(request()->query(), ['type' => 'tax', 'format' => 'excel'])) }}" class="admin-dropdown-item text-sm" style="padding: 0.45rem 0.75rem; display: flex; align-items: center; gap: 0.5rem; text-decoration: none; color: var(--color-text);">
                                <i class="fa-solid fa-file-invoice-dollar text-warning" style="width: 16px;"></i>
                                <span>{{ __('admin.reports.export_tax') }}</span>
                            </a>
                            <a href="{{ route('admin.reports.export', array_merge(request()->query(), ['type' => 'inventory', 'format' => 'excel'])) }}" class="admin-dropdown-item text-sm" style="padding: 0.45rem 0.75rem; display: flex; align-items: center; gap: 0.5rem; text-decoration: none; color: var(--color-text);">
                                <i class="fa-solid fa-boxes-stacked text-success" style="width: 16px;"></i>
                                <span>{{ __('admin.reports.export_inventory') }}</span>
                            </a>
                            <a href="{{ route('admin.reports.export', array_merge(request()->query(), ['type' => 'customers', 'format' => 'excel'])) }}" class="admin-dropdown-item text-sm" style="padding: 0.45rem 0.75rem; display: flex; align-items: center; gap: 0.5rem; text-decoration: none; color: var(--color-text);">
                                <i class="fa-solid fa-users text-primary" style="width: 16px;"></i>
                                <span>{{ __('admin.reports.export_customers') }}</span>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>

    <!-- Executive Summary KPI Cards (6 Grid) -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
        
        <!-- 1. Gross Revenue -->
        <div class="card stat-card stat-accent" style="padding: 1.25rem; position: relative;">
            <div class="stat-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                <span class="stat-label" style="font-size: 0.8125rem; font-weight: 700; text-transform: uppercase;">{{ __('admin.reports.gross_revenue') }}</span>
                <span style="width: 32px; height: 32px; border-radius: 8px; background: rgba(10, 79, 120, 0.12); color: #0A4F78; display: flex; align-items: center; justify-content: center;">
                    <i class="fa-solid fa-wallet"></i>
                </span>
            </div>
            <div class="stat-value" style="font-size: 1.85rem; font-weight: 900; color: var(--color-text); margin-bottom: 0.35rem; font-variant-numeric: tabular-nums;">
                ${{ number_format($kpi['total_sales'], 2) }}
            </div>
            <div class="stat-footer text-success font-bold" style="font-size: 0.8125rem; display: flex; align-items: center; gap: 0.35rem;">
                <i class="fa-solid fa-arrow-trend-up"></i>
                <span>{{ $kpi['growth_rate'] }} YoY Velocity</span>
            </div>
        </div>

        <!-- 2. Net Realized Revenue -->
        <div class="card stat-card stat-success" style="padding: 1.25rem;">
            <div class="stat-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                <span class="stat-label" style="font-size: 0.8125rem; font-weight: 700; text-transform: uppercase;">{{ __('admin.reports.net_revenue') }}</span>
                <span style="width: 32px; height: 32px; border-radius: 8px; background: rgba(16, 185, 129, 0.12); color: #10B981; display: flex; align-items: center; justify-content: center;">
                    <i class="fa-solid fa-coins"></i>
                </span>
            </div>
            <div class="stat-value" style="font-size: 1.85rem; font-weight: 900; color: #10B981; margin-bottom: 0.35rem; font-variant-numeric: tabular-nums;">
                ${{ number_format($kpi['net_revenue'], 2) }}
            </div>
            <div class="stat-footer text-muted" style="font-size: 0.8125rem;">
                {{ __('admin.reports.discounts_given') }}: <strong>${{ number_format($kpi['total_discounts'], 2) }}</strong>
            </div>
        </div>

        <!-- 3. Average Order Value (AOV) -->
        <div class="card stat-card" style="padding: 1.25rem;">
            <div class="stat-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                <span class="stat-label" style="font-size: 0.8125rem; font-weight: 700; text-transform: uppercase;">{{ __('admin.reports.avg_order_value') }}</span>
                <span style="width: 32px; height: 32px; border-radius: 8px; background: rgba(42, 143, 194, 0.12); color: #2A8FC2; display: flex; align-items: center; justify-content: center;">
                    <i class="fa-solid fa-scale-balanced"></i>
                </span>
            </div>
            <div class="stat-value" style="font-size: 1.85rem; font-weight: 900; color: var(--color-text); margin-bottom: 0.35rem; font-variant-numeric: tabular-nums;">
                ${{ number_format($kpi['average_order_value'], 2) }}
            </div>
            <div class="stat-footer text-muted" style="font-size: 0.8125rem;">
                {{ __('admin.reports.across_channels') }}
            </div>
        </div>

        <!-- 4. Total Transactions -->
        <div class="card stat-card" style="padding: 1.25rem;">
            <div class="stat-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                <span class="stat-label" style="font-size: 0.8125rem; font-weight: 700; text-transform: uppercase;">{{ __('admin.reports.total_transactions') }}</span>
                <span style="width: 32px; height: 32px; border-radius: 8px; background: rgba(139, 92, 246, 0.12); color: #8B5CF6; display: flex; align-items: center; justify-content: center;">
                    <i class="fa-solid fa-cart-shopping"></i>
                </span>
            </div>
            <div class="stat-value" style="font-size: 1.85rem; font-weight: 900; color: var(--color-text); margin-bottom: 0.35rem; font-variant-numeric: tabular-nums;">
                {{ $kpi['total_orders'] }}
            </div>
            <div class="stat-footer text-muted" style="font-size: 0.8125rem; display: flex; align-items: center; gap: 0.5rem;">
                <span class="badge badge-accent text-xs" style="padding: 0.15rem 0.4rem;">{{ $channels['online']['orders_count'] }} Online</span>
                <span class="badge badge-warning text-xs" style="padding: 0.15rem 0.4rem;">{{ $channels['pos']['orders_count'] }} POS</span>
            </div>
        </div>

        <!-- 5. Registered Customers & Repeat Rate -->
        <div class="card stat-card stat-warning" style="padding: 1.25rem;">
            <div class="stat-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                <span class="stat-label" style="font-size: 0.8125rem; font-weight: 700; text-transform: uppercase;">{{ __('admin.kpi.customers_registered') }}</span>
                <span style="width: 32px; height: 32px; border-radius: 8px; background: rgba(245, 158, 11, 0.12); color: #F59E0B; display: flex; align-items: center; justify-content: center;">
                    <i class="fa-solid fa-users"></i>
                </span>
            </div>
            <div class="stat-value" style="font-size: 1.85rem; font-weight: 900; color: var(--color-text); margin-bottom: 0.35rem; font-variant-numeric: tabular-nums;">
                {{ $kpi['total_customers'] }}
            </div>
            <div class="stat-footer text-success font-bold" style="font-size: 0.8125rem;">
                <i class="fa-solid fa-arrows-rotate mr-1 ml-1"></i> {{ $kpi['repeat_rate'] }}% {{ __('admin.reports.repeat_rate') }}
            </div>
        </div>

        <!-- 6. Inventory Valuation -->
        <div class="card stat-card" style="padding: 1.25rem;">
            <div class="stat-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                <span class="stat-label" style="font-size: 0.8125rem; font-weight: 700; text-transform: uppercase;">{{ __('admin.reports.inventory_valuation') }}</span>
                <span style="width: 32px; height: 32px; border-radius: 8px; background: rgba(14, 165, 233, 0.12); color: #0EA5E9; display: flex; align-items: center; justify-content: center;">
                    <i class="fa-solid fa-cubes-stacked"></i>
                </span>
            </div>
            <div class="stat-value" style="font-size: 1.85rem; font-weight: 900; color: var(--color-text); margin-bottom: 0.35rem; font-variant-numeric: tabular-nums;">
                ${{ number_format($inventory['total_valuation'], 2) }}
            </div>
            <div class="stat-footer text-muted" style="font-size: 0.8125rem;">
                {{ $inventory['total_units'] }} {{ __('admin.reports.total_units_sold') }}
            </div>
        </div>

    </div>

    <!-- Navigation Tabs for Deep Intelligence (Hidden in print) -->
    <div class="no-print" style="margin-bottom: 1.75rem; border-bottom: 2px solid var(--color-border); display: flex; gap: 0.5rem; overflow-x: auto;">
        <button type="button" class="report-tab-btn active" onclick="switchReportTab('overview', this)">
            {{ __('admin.reports.tabs.overview') }}
        </button>
        <button type="button" class="report-tab-btn" onclick="switchReportTab('products', this)">
            {{ __('admin.reports.tabs.products') }}
        </button>
        <button type="button" class="report-tab-btn" onclick="switchReportTab('channels', this)">
            {{ __('admin.reports.tabs.channels') }}
        </button>
        <button type="button" class="report-tab-btn" onclick="switchReportTab('customers', this)">
            {{ __('admin.reports.tabs.customers') }}
        </button>
        <button type="button" class="report-tab-btn" onclick="switchReportTab('tax_inventory', this)">
            {{ __('admin.reports.tabs.tax_inventory') }}
        </button>
        <button type="button" class="report-tab-btn" onclick="switchReportTab('full_system', this)">
            {{ __('admin.reports.tabs.full_system') }}
        </button>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 1: OVERVIEW & FINANCIAL VELOCITY                                      -->
    <!-- ========================================================================= -->
    <div id="tab-overview" class="report-tab-content">
        <div style="display: grid; grid-template-columns: 1.4fr 1fr; gap: 1.75rem; margin-bottom: 2rem;">
            
            <!-- Revenue Growth & Velocity Chart -->
            <div class="card" style="padding: 1.75rem; display: flex; flex-direction: column;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                    <div>
                        <h3 style="font-size: 1.15rem; font-weight: 800; margin: 0; color: var(--color-text);">
                            <i class="fa-solid fa-chart-simple text-primary mr-1.5 ml-1.5"></i> {{ __('admin.reports.revenue_trend') }}
                        </h3>
                        <p style="font-size: 0.8125rem; color: var(--color-text-muted); margin: 0.25rem 0 0 0;">
                            {{ __('admin.reports.combined_channels') }} (2026)
                        </p>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.75rem; font-size: 0.75rem; font-weight: 700;">
                        <span style="display: inline-flex; align-items: center; gap: 0.35rem;">
                            <span style="width: 10px; height: 10px; border-radius: 3px; background: #0A4F78;"></span> {{ __('admin.reports.online_only') }}
                        </span>
                        <span style="display: inline-flex; align-items: center; gap: 0.35rem;">
                            <span style="width: 10px; height: 10px; border-radius: 3px; background: #2A8FC2;"></span> {{ __('admin.reports.pos_only') }}
                        </span>
                    </div>
                </div>

                <!-- Responsive Multi-Bar Chart -->
                <div style="flex: 1; min-height: 240px; display: flex; align-items: flex-end; justify-content: space-between; gap: 0.5rem; padding-top: 2rem; border-bottom: 1px solid var(--color-border);">
                    @php
                        $maxTotal = 1;
                        foreach($chartData['monthly'] as $m) {
                            if ($m['total'] > $maxTotal) $maxTotal = $m['total'];
                        }
                    @endphp

                    @foreach($chartData['monthly'] as $point)
                        @php
                            $heightPct = round(($point['total'] / $maxTotal) * 100);
                            $onlineHeightPct = $point['total'] > 0 ? round(($point['online'] / $point['total']) * 100) : 70;
                            $posHeightPct = 100 - $onlineHeightPct;
                        @endphp
                        <div style="flex: 1; display: flex; flex-direction: column; align-items: center; height: 100%; justify-content: flex-end; position: relative;">
                            
                            <div class="chart-tooltip" style="position: absolute; bottom: calc({{ $heightPct }}% + 10px); background: #031827; color: #FFF; padding: 0.35rem 0.6rem; border-radius: 6px; font-size: 0.7rem; font-weight: 700; white-space: nowrap; opacity: 0; pointer-events: none; transition: opacity 0.2s; z-index: 10;">
                                <div>{{ $point['month'] }} Total: ${{ number_format($point['total'], 0) }}</div>
                                <div style="color: #67B34A; font-size: 0.65rem;">Online: ${{ number_format($point['online'], 0) }} | POS: ${{ number_format($point['pos'], 0) }}</div>
                            </div>

                            <div style="width: 70%; max-width: 32px; height: {{ max(10, $heightPct) }}%; border-radius: 6px 6px 0 0; overflow: hidden; display: flex; flex-direction: column; cursor: pointer; transition: transform 0.2s;" onmouseover="this.previousElementSibling.style.opacity=1" onmouseout="this.previousElementSibling.style.opacity=0">
                                <div style="height: {{ $onlineHeightPct }}%; background: #0A4F78; width: 100%;"></div>
                                <div style="height: {{ $posHeightPct }}%; background: #2A8FC2; width: 100%;"></div>
                            </div>

                            <div style="font-size: 0.75rem; font-weight: 700; color: var(--color-text-muted); margin-top: 0.6rem;">
                                {{ $point['month'] }}
                            </div>
                        </div>
                    @endforeach
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.75rem; font-size: 0.75rem; color: var(--color-text-muted);">
                    <span><i class="fa-solid fa-circle-info mr-1 ml-1"></i> {{ __('admin.reports.across_channels') }}</span>
                    <span>Peak Month Volume: <strong>${{ number_format($maxTotal, 2) }}</strong></span>
                </div>
            </div>

            <!-- Longevity Systems Breakdown -->
            <div class="card" style="padding: 1.75rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 0.75rem;">
                    <h3 style="font-size: 1.15rem; font-weight: 800; margin: 0; color: var(--color-text);">
                        <i class="fa-solid fa-dna text-accent mr-1.5 ml-1.5"></i> {{ __('admin.reports.volume_by_system') }}
                    </h3>
                </div>

                <div style="display: flex; flex-direction: column; gap: 1.35rem;">
                    @foreach($categories as $cat)
                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem; font-size: 0.875rem;">
                                <span class="font-bold text-slate-800 dark:text-slate-100">
                                    {{ app()->getLocale() === 'ar' ? $cat['name_ar'] : $cat['name_en'] }}
                                </span>
                                <span style="font-variant-numeric: tabular-nums;">
                                    <strong>${{ number_format($cat['amount'], 2) }}</strong> 
                                    <span style="color: var(--color-text-muted); font-size: 0.8rem; margin-inline-start: 0.35rem;">({{ $cat['percentage'] }}%)</span>
                                </span>
                            </div>
                            <div class="progress-track" style="height: 9px; border-radius: 6px; background: rgba(0,0,0,0.06); overflow: hidden;">
                                <div class="progress-fill" style="width: {{ $cat['percentage'] }}%; height: 100%; border-radius: 6px; background: linear-gradient(90deg, #0A4F78, #2A8FC2);"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 2: FORMULATIONS & PRODUCTS PERFORMANCE                               -->
    <!-- ========================================================================= -->
    <div id="tab-products" class="report-tab-content" style="display: none;">
        <div class="card" style="padding: 1.75rem; margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h3 style="font-size: 1.25rem; font-weight: 800; margin: 0; color: var(--color-text);">
                        <i class="fa-solid fa-flask-vial text-primary mr-1.5 ml-1.5"></i> {{ __('admin.reports.top_formulations') }}
                    </h3>
                    <p style="font-size: 0.8125rem; color: var(--color-text-muted); margin: 0.25rem 0 0 0;">
                        {{ __('admin.reports.volume_by_system') }}
                    </p>
                </div>
                <a href="{{ route('admin.reports.export', array_merge(request()->query(), ['type' => 'products', 'format' => 'excel'])) }}" class="btn btn-secondary btn-sm no-print font-bold">
                    <i class="fa-solid fa-file-excel mr-1 ml-1 text-success"></i> {{ __('admin.reports.export_products') }}
                </a>
            </div>

            <div class="table-responsive" style="border: 1px solid var(--color-border); border-radius: 0.75rem;">
                <table class="table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: var(--color-bg-subtle, rgba(0,0,0,0.02)); border-bottom: 1px solid var(--color-border);">
                            <th style="padding: 0.85rem 1rem; text-align: start;">#</th>
                            <th style="padding: 0.85rem 1rem; text-align: start;">{{ __('admin.products.fields.name_en') }}</th>
                            <th style="padding: 0.85rem 1rem; text-align: start;">{{ __('admin.menu.categories') }}</th>
                            <th style="padding: 0.85rem 1rem; text-align: start;">{{ __('admin.products.fields.price') }}</th>
                            <th style="padding: 0.85rem 1rem; text-align: center;">{{ __('admin.reports.units_sold') }}</th>
                            <th style="padding: 0.85rem 1rem; text-align: start;">{{ __('admin.reports.revenue') }}</th>
                            <th style="padding: 0.85rem 1rem; text-align: center;">{{ __('admin.reports.share_pct') }}</th>
                            <th style="padding: 0.85rem 1rem; text-align: start;">{{ __('admin.reports.stock_status') }}</th>
                            <th style="padding: 0.85rem 1rem; text-align: center;" class="no-print">{{ __('app.actions.manage') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $idx => $p)
                            <tr style="border-bottom: 1px solid var(--color-border);">
                                <td style="padding: 0.85rem 1rem; font-weight: 700; color: var(--color-text-muted);">{{ $idx + 1 }}</td>
                                <td style="padding: 0.85rem 1rem;">
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <img src="{{ $p['image'] }}" alt="{{ $p['name_en'] }}" style="width: 36px; height: 36px; border-radius: 6px; object-fit: cover; border: 1px solid var(--color-border);" onerror="this.src='/assets/products/blue-mind.jpg'">
                                        <div>
                                            <div style="font-weight: 800; font-size: 0.9375rem; color: var(--color-text);">
                                                {{ app()->getLocale() === 'ar' ? $p['name_ar'] : $p['name_en'] }}
                                            </div>
                                            <div style="font-size: 0.75rem; color: var(--color-text-muted); font-family: monospace;">{{ $p['sku'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 0.85rem 1rem;">
                                    <span class="badge badge-neutral text-xs">
                                        {{ app()->getLocale() === 'ar' ? $p['category_ar'] : $p['category_en'] }}
                                    </span>
                                </td>
                                <td style="padding: 0.85rem 1rem; font-weight: 700; font-variant-numeric: tabular-nums;">
                                    ${{ number_format($p['price'], 2) }}
                                </td>
                                <td style="padding: 0.85rem 1rem; text-align: center; font-weight: 800; color: var(--color-primary); font-variant-numeric: tabular-nums;">
                                    {{ $p['units_sold'] }}
                                </td>
                                <td style="padding: 0.85rem 1rem; font-weight: 800; color: var(--color-text); font-variant-numeric: tabular-nums;">
                                    ${{ number_format($p['revenue'], 2) }}
                                </td>
                                <td style="padding: 0.85rem 1rem; text-align: center;">
                                    <span class="badge badge-accent font-bold text-xs" style="padding: 0.2rem 0.5rem;">
                                        {{ $p['share_pct'] }}%
                                    </span>
                                </td>
                                <td style="padding: 0.85rem 1rem;">
                                    @if($p['stock_status'] === 'in_stock')
                                        <span class="badge badge-success text-xs font-bold">
                                            <i class="fa-solid fa-check mr-1 ml-1"></i> {{ $p['total_stock'] }} {{ __('admin.reports.in_stock') }}
                                        </span>
                                    @elseif($p['stock_status'] === 'low_stock')
                                        <span class="badge badge-warning text-xs font-bold">
                                            <i class="fa-solid fa-triangle-exclamation mr-1 ml-1"></i> {{ $p['total_stock'] }} {{ __('admin.reports.low_stock') }}
                                        </span>
                                    @else
                                        <span class="badge badge-danger text-xs font-bold">
                                            <i class="fa-solid fa-xmark mr-1 ml-1"></i> {{ __('admin.reports.out_of_stock') }}
                                        </span>
                                    @endif
                                </td>
                                <td style="padding: 0.85rem 1rem; text-align: center;" class="no-print">
                                    <a href="{{ route('admin.products.edit', $p['id']) }}" class="btn btn-ghost btn-sm" title="{{ __('app.actions.edit') }}">
                                        <i class="fa-solid fa-pen-to-square text-primary"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 3: CHANNELS & POS BOUTIQUE PERFORMANCE                               -->
    <!-- ========================================================================= -->
    <div id="tab-channels" class="report-tab-content" style="display: none;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
            
            <!-- Online Store Card -->
            <div class="card" style="padding: 1.75rem;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
                    <div>
                        <h3 style="font-size: 1.25rem; font-weight: 800; margin: 0; color: var(--color-text);">
                            <i class="fa-solid fa-globe text-primary mr-1 ml-1"></i> {{ __('admin.reports.online_only') }}
                        </h3>
                        <p style="font-size: 0.8125rem; color: var(--color-text-muted); margin: 0.2rem 0 0 0;">
                            Global Direct-to-Consumer Platform
                        </p>
                    </div>
                    <span class="badge badge-primary font-bold text-sm">{{ $channels['online']['percentage'] }}% {{ __('admin.reports.share_pct') }}</span>
                </div>

                <div style="display: flex; flex-direction: column; gap: 1rem; margin-bottom: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; padding: 0.75rem; background: var(--color-bg-subtle, rgba(0,0,0,0.02)); border-radius: 0.5rem;">
                        <span class="font-bold text-sm">{{ __('admin.reports.gross_revenue') }}:</span>
                        <span class="font-extrabold text-sm" style="color: var(--color-primary);">${{ number_format($channels['online']['revenue'], 2) }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.75rem; background: var(--color-bg-subtle, rgba(0,0,0,0.02)); border-radius: 0.5rem;">
                        <span class="font-bold text-sm">{{ __('admin.reports.total_transactions') }}:</span>
                        <span class="font-extrabold text-sm">{{ $channels['online']['orders_count'] }} Orders</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.75rem; background: var(--color-bg-subtle, rgba(0,0,0,0.02)); border-radius: 0.5rem;">
                        <span class="font-bold text-sm">{{ __('admin.reports.avg_order_value') }}:</span>
                        <span class="font-extrabold text-sm">${{ number_format($channels['online']['avg_ticket'], 2) }}</span>
                    </div>
                </div>

                <div class="no-print">
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-primary btn-sm w-full" style="justify-content: center;">
                        <i class="fa-solid fa-bag-shopping mr-1.5 ml-1.5"></i> {{ __('admin.menu.online_orders') }}
                    </a>
                </div>
            </div>

            <!-- POS Flagship Boutique Card -->
            <div class="card" style="padding: 1.75rem;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
                    <div>
                        <h3 style="font-size: 1.25rem; font-weight: 800; margin: 0; color: var(--color-text);">
                            <i class="fa-solid fa-cash-register text-warning mr-1 ml-1"></i> {{ __('admin.reports.pos_only') }}
                        </h3>
                        <p style="font-size: 0.8125rem; color: var(--color-text-muted); margin: 0.2rem 0 0 0;">
                            Flagship Physical Wellness Boutique
                        </p>
                    </div>
                    <span class="badge badge-warning font-bold text-sm">{{ $channels['pos']['percentage'] }}% {{ __('admin.reports.share_pct') }}</span>
                </div>

                <div style="display: flex; flex-direction: column; gap: 1rem; margin-bottom: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; padding: 0.75rem; background: var(--color-bg-subtle, rgba(0,0,0,0.02)); border-radius: 0.5rem;">
                        <span class="font-bold text-sm">{{ __('admin.reports.gross_revenue') }}:</span>
                        <span class="font-extrabold text-sm" style="color: #F59E0B;">${{ number_format($channels['pos']['revenue'], 2) }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.75rem; background: var(--color-bg-subtle, rgba(0,0,0,0.02)); border-radius: 0.5rem;">
                        <span class="font-bold text-sm">{{ __('admin.reports.total_transactions') }}:</span>
                        <span class="font-extrabold text-sm">{{ $channels['pos']['orders_count'] }} Tickets</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: 0.75rem; background: var(--color-bg-subtle, rgba(0,0,0,0.02)); border-radius: 0.5rem;">
                        <span class="font-bold text-sm">{{ __('admin.reports.avg_order_value') }}:</span>
                        <span class="font-extrabold text-sm">${{ number_format($channels['pos']['avg_ticket'], 2) }}</span>
                    </div>
                </div>

                <div class="no-print" style="display: flex; gap: 0.75rem;">
                    <a href="{{ route('admin.offline-sales.create') }}" class="btn btn-warning btn-sm" style="flex: 1; justify-content: center; font-weight: 700;">
                        <i class="fa-solid fa-plus mr-1 ml-1"></i> {{ __('admin.dashboard.open_pos') }}
                    </a>
                    <a href="{{ route('admin.offline-sales.index') }}" class="btn btn-secondary btn-sm" style="flex: 1; justify-content: center;">
                        <i class="fa-solid fa-list mr-1 ml-1"></i> {{ __('admin.menu.offline_sales') }}
                    </a>
                </div>
            </div>

        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 4: CUSTOMER CRM & LOYALTY ANALYTICS                                   -->
    <!-- ========================================================================= -->
    <div id="tab-customers" class="report-tab-content" style="display: none;">
        <div class="card" style="padding: 1.75rem; margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                <div>
                    <h3 style="font-size: 1.25rem; font-weight: 800; margin: 0; color: var(--color-text);">
                        <i class="fa-solid fa-crown text-warning mr-1.5 ml-1.5"></i> {{ __('admin.reports.top_customers') }}
                    </h3>
                    <p style="font-size: 0.8125rem; color: var(--color-text-muted); margin: 0.25rem 0 0 0;">
                        {{ __('admin.reports.customer_insights') }}
                    </p>
                </div>
                <a href="{{ route('admin.reports.export', array_merge(request()->query(), ['type' => 'customers', 'format' => 'excel'])) }}" class="btn btn-secondary btn-sm no-print font-bold">
                    <i class="fa-solid fa-file-excel mr-1 ml-1 text-success"></i> {{ __('admin.reports.export_customers') }}
                </a>
            </div>

            <div class="table-responsive" style="border: 1px solid var(--color-border); border-radius: 0.75rem;">
                <table class="table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: var(--color-bg-subtle, rgba(0,0,0,0.02)); border-bottom: 1px solid var(--color-border);">
                            <th style="padding: 0.85rem 1rem; text-align: start;">{{ __('admin.customers.fields.name') }}</th>
                            <th style="padding: 0.85rem 1rem; text-align: start;">{{ __('admin.customers.fields.email') }}</th>
                            <th style="padding: 0.85rem 1rem; text-align: start;">{{ __('admin.customers.fields.phone') }}</th>
                            <th style="padding: 0.85rem 1rem; text-align: start;">{{ __('admin.customers.fields.city') }}</th>
                            <th style="padding: 0.85rem 1rem; text-align: center;">{{ __('admin.orders.title') }}</th>
                            <th style="padding: 0.85rem 1rem; text-align: start;">{{ __('admin.customers.fields.total_spent') }}</th>
                            <th style="padding: 0.85rem 1rem; text-align: center;" class="no-print">{{ __('app.actions.view') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customers['top_customers'] as $vip)
                            <tr style="border-bottom: 1px solid var(--color-border);">
                                <td style="padding: 0.85rem 1rem; font-weight: 800; color: var(--color-text);">
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <div style="width: 30px; height: 30px; border-radius: 50%; background: linear-gradient(135deg, #0A4F78, #2A8FC2); color: #FFF; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 800;">
                                            {{ strtoupper(substr($vip['name'], 0, 2)) }}
                                        </div>
                                        <span><bdi>{{ $vip['name'] }}</bdi></span>
                                    </div>
                                </td>
                                <td style="padding: 0.85rem 1rem; font-size: 0.875rem; color: var(--color-text-muted);">{{ $vip['email'] }}</td>
                                <td style="padding: 0.85rem 1rem; font-size: 0.875rem; font-family: monospace;">{{ $vip['phone'] }}</td>
                                <td style="padding: 0.85rem 1rem;">
                                    <span class="badge badge-neutral text-xs">{{ $vip['city'] }}</span>
                                </td>
                                <td style="padding: 0.85rem 1rem; text-align: center; font-weight: 800; font-variant-numeric: tabular-nums;">
                                    {{ $vip['orders_count'] }}
                                </td>
                                <td style="padding: 0.85rem 1rem; font-weight: 800; color: #10B981; font-variant-numeric: tabular-nums;">
                                    ${{ number_format($vip['total_spent'], 2) }}
                                </td>
                                <td style="padding: 0.85rem 1rem; text-align: center;" class="no-print">
                                    <a href="{{ route('admin.customers.show', $vip['id']) }}" class="btn btn-ghost btn-sm" title="{{ __('app.actions.view') }}">
                                        <i class="fa-solid fa-arrow-up-right-from-square text-primary"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 5: TAX, SHIPPING & INVENTORY VALUATION                                -->
    <!-- ========================================================================= -->
    <div id="tab-tax-inventory" class="report-tab-content" style="display: none;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
            
            <!-- Tax & Settlement Statement -->
            <div class="card" style="padding: 1.75rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--color-border); padding-bottom: 0.75rem;">
                    <h3 style="font-size: 1.15rem; font-weight: 800; margin: 0; color: var(--color-text);">
                        <i class="fa-solid fa-file-invoice-dollar text-warning mr-1.5 ml-1.5"></i> {{ __('admin.reports.tax_statement') }}
                    </h3>
                    <a href="{{ route('admin.reports.export', array_merge(request()->query(), ['type' => 'tax', 'format' => 'excel'])) }}" class="btn btn-secondary btn-sm no-print font-bold">
                        <i class="fa-solid fa-file-excel mr-1 ml-1 text-success"></i> {{ __('admin.reports.export_tax') }}
                    </a>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                    <div style="display: flex; justify-content: space-between; font-size: 0.875rem;">
                        <span class="text-muted">{{ __('admin.reports.subtotal_gross') }}:</span>
                        <span class="font-bold font-mono">${{ number_format($tax['subtotal_gross'], 2) }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.875rem; color: #EF4444;">
                        <span><i class="fa-solid fa-tag mr-1 ml-1"></i> {{ __('admin.reports.discounts_given') }}:</span>
                        <span class="font-bold font-mono">-${{ number_format($tax['discounts'], 2) }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.875rem; border-top: 1px dashed var(--color-border); padding-top: 0.6rem;">
                        <span class="font-bold">{{ __('admin.reports.net_taxable') }}:</span>
                        <span class="font-bold font-mono">${{ number_format($tax['net_taxable'], 2) }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.875rem; color: #0A4F78;">
                        <span class="font-bold">{{ __('admin.reports.tax_collected') }}:</span>
                        <span class="font-bold font-mono">+${{ number_format($tax['vat_15'], 2) }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.875rem; color: #10B981;">
                        <span class="font-bold">{{ __('admin.reports.shipping_collected') }}:</span>
                        <span class="font-bold font-mono">+${{ number_format($tax['shipping_total'], 2) }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 1.05rem; font-weight: 800; border-top: 2px solid var(--color-border); padding-top: 0.75rem; color: var(--color-text);">
                        <span>{{ __('admin.reports.gross_revenue') }}:</span>
                        <span class="font-mono text-primary">${{ number_format($tax['realized_gross'], 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Inventory Valuation & Warehousing Status -->
            <div class="card" style="padding: 1.75rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--color-border); padding-bottom: 0.75rem;">
                    <h3 style="font-size: 1.15rem; font-weight: 800; margin: 0; color: var(--color-text);">
                        <i class="fa-solid fa-boxes-stacked text-primary mr-1.5 ml-1.5"></i> {{ __('admin.reports.inventory_valuation') }}
                    </h3>
                    <a href="{{ route('admin.reports.export', array_merge(request()->query(), ['type' => 'inventory', 'format' => 'excel'])) }}" class="btn btn-secondary btn-sm no-print font-bold">
                        <i class="fa-solid fa-file-excel mr-1 ml-1 text-success"></i> {{ __('admin.reports.export_inventory') }}
                    </a>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                    <div style="display: flex; justify-content: space-between; font-size: 0.875rem;">
                        <span class="text-muted">Total SKUs Monitored:</span>
                        <span class="font-bold">{{ $inventory['total_skus'] }} Formulations</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.875rem;">
                        <span class="text-muted">Total Units On Hand:</span>
                        <span class="font-bold font-mono">{{ $inventory['total_units'] }} Units</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.875rem;">
                        <span class="text-muted">{{ __('admin.kpi.low_stock_alerts') }}:</span>
                        <span class="badge badge-warning font-bold">{{ $inventory['low_stock_count'] }} SKUs</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.875rem;">
                        <span class="text-muted">{{ __('admin.reports.out_of_stock') }}:</span>
                        <span class="badge badge-danger font-bold">{{ $inventory['out_of_stock_count'] }} SKUs</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 1.05rem; font-weight: 800; border-top: 2px solid var(--color-border); padding-top: 0.75rem; color: var(--color-text);">
                        <span>Retail Asset Valuation:</span>
                        <span class="font-mono text-success">${{ number_format($inventory['total_valuation'], 2) }}</span>
                    </div>
                </div>

                <div style="margin-top: 1.5rem;" class="no-print">
                    <a href="{{ route('admin.inventory.index') }}" class="btn btn-outline btn-sm w-full" style="justify-content: center;">
                        <i class="fa-solid fa-warehouse mr-1.5 ml-1.5"></i> {{ __('admin.menu.stock_levels') }}
                    </a>
                </div>
            </div>

        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 6: FULL SYSTEM MASTER DOSSIER                                         -->
    <!-- ========================================================================= -->
    <div id="tab-full-system" class="report-tab-content" style="display: none;">
        
        <!-- System Health & Platform Integrity Strip -->
        <div class="card" style="padding: 1.75rem; margin-bottom: 2rem; background: var(--color-bg-subtle, rgba(0,0,0,0.02));">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                <h3 style="font-size: 1.15rem; font-weight: 800; margin: 0; color: var(--color-text);">
                    <i class="fa-solid fa-server text-primary mr-1.5 ml-1.5"></i> {{ __('admin.reports.system_health') }}
                </h3>
                <span class="badge badge-success font-bold text-xs" style="padding: 0.35rem 0.75rem;">
                    <i class="fa-solid fa-circle-check mr-1 ml-1"></i> {{ $system['active_status'] }}
                </span>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 1rem;">
                <div style="background: var(--color-surface); padding: 1rem; border-radius: 8px; border: 1px solid var(--color-border); text-align: center;">
                    <div style="font-size: 0.75rem; color: var(--color-text-muted);">{{ __('admin.menu.products') }}</div>
                    <div style="font-size: 1.35rem; font-weight: 900; color: var(--color-primary); margin-top: 0.25rem;">{{ $system['total_products'] }}</div>
                </div>
                <div style="background: var(--color-surface); padding: 1rem; border-radius: 8px; border: 1px solid var(--color-border); text-align: center;">
                    <div style="font-size: 0.75rem; color: var(--color-text-muted);">{{ __('admin.menu.categories') }}</div>
                    <div style="font-size: 1.35rem; font-weight: 900; color: var(--color-accent); margin-top: 0.25rem;">{{ $system['total_categories'] }}</div>
                </div>
                <div style="background: var(--color-surface); padding: 1rem; border-radius: 8px; border: 1px solid var(--color-border); text-align: center;">
                    <div style="font-size: 0.75rem; color: var(--color-text-muted);">{{ __('admin.orders.title') }}</div>
                    <div style="font-size: 1.35rem; font-weight: 900; color: var(--color-text); margin-top: 0.25rem;">{{ $system['total_orders'] }}</div>
                </div>
                <div style="background: var(--color-surface); padding: 1rem; border-radius: 8px; border: 1px solid var(--color-border); text-align: center;">
                    <div style="font-size: 0.75rem; color: var(--color-text-muted);">{{ __('admin.menu.customers') }}</div>
                    <div style="font-size: 1.35rem; font-weight: 900; color: #10B981; margin-top: 0.25rem;">{{ $system['total_customers'] }}</div>
                </div>
                <div style="background: var(--color-surface); padding: 1rem; border-radius: 8px; border: 1px solid var(--color-border); text-align: center;">
                    <div style="font-size: 0.75rem; color: var(--color-text-muted);">Inventory Records</div>
                    <div style="font-size: 1.35rem; font-weight: 900; color: #F59E0B; margin-top: 0.25rem;">{{ $system['total_inventory'] }}</div>
                </div>
                <div style="background: var(--color-surface); padding: 1rem; border-radius: 8px; border: 1px solid var(--color-border); text-align: center;">
                    <div style="font-size: 0.75rem; color: var(--color-text-muted);">Audit Trail Entries</div>
                    <div style="font-size: 1.35rem; font-weight: 900; color: #8B5CF6; margin-top: 0.25rem;">{{ $system['total_movements'] }}</div>
                </div>
            </div>
        </div>

        <!-- Geographic Sales & Clientele Distribution Across Saudi Arabia -->
        <div class="card" style="padding: 1.75rem; margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                <div>
                    <h3 style="font-size: 1.15rem; font-weight: 800; margin: 0; color: var(--color-text);">
                        <i class="fa-solid fa-map-location-dot text-primary mr-1.5 ml-1.5"></i> {{ __('admin.reports.geographic_distribution') }}
                    </h3>
                    <p style="font-size: 0.8125rem; color: var(--color-text-muted); margin: 0.2rem 0 0 0;">
                        Regional market penetration across GCC hubs
                    </p>
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                @foreach($geo as $city)
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem; font-size: 0.875rem;">
                            <span class="font-bold">
                                <i class="fa-solid fa-location-dot text-danger mr-1 ml-1 text-xs"></i> 
                                {{ app()->getLocale() === 'ar' ? $city['name_ar'] : $city['name_en'] }} 
                                <span style="font-size: 0.8rem; color: var(--color-text-muted);">({{ $city['orders'] }} Orders)</span>
                            </span>
                            <span style="font-variant-numeric: tabular-nums;">
                                <strong>${{ number_format($city['sales'], 2) }}</strong> 
                                <span style="color: var(--color-text-muted); font-size: 0.8rem;">({{ $city['share'] }}%)</span>
                            </span>
                        </div>
                        <div class="progress-track" style="height: 8px; border-radius: 4px;">
                            <div class="progress-fill" style="width: {{ $city['share'] }}%; background: linear-gradient(90deg, #0A4F78, #2A8FC2); border-radius: 4px;"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    @push('scripts')
    <script>
        function switchReportTab(tabName, btnElement) {
            document.querySelectorAll('.report-tab-content').forEach(function(el) {
                el.style.display = 'none';
            });
            document.querySelectorAll('.report-tab-btn').forEach(function(el) {
                el.classList.remove('active');
            });
            const target = document.getElementById('tab-' + tabName);
            if (target) {
                target.style.display = 'block';
            }
            if (btnElement) {
                btnElement.classList.add('active');
            }
        }

        function toggleCustomDates(val) {
            const el = document.getElementById('customDateFields');
            if (el) {
                el.style.display = (val === 'custom') ? 'flex' : 'none';
            }
        }

        function toggleExportMenu(e) {
            e.stopPropagation();
            const menu = document.getElementById('exportMenu');
            if (menu) {
                menu.style.display = (menu.style.display === 'none' || menu.style.display === '') ? 'block' : 'none';
            }
        }

        document.addEventListener('click', function(e) {
            const menu = document.getElementById('exportMenu');
            if (menu && !menu.contains(e.target)) {
                menu.style.display = 'none';
            }
        });
    </script>
    @endpush

    <style>
        .report-tab-btn {
            background: transparent;
            border: none;
            border-bottom: 3px solid transparent;
            padding: 0.75rem 1.25rem;
            font-size: 0.9375rem;
            font-weight: 700;
            color: var(--color-text-muted);
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
        }
        .report-tab-btn:hover {
            color: var(--color-primary);
            border-bottom-color: var(--color-border);
        }
        .report-tab-btn.active {
            color: var(--color-primary);
            border-bottom-color: var(--color-primary);
        }
        .admin-dropdown-item:hover {
            background: var(--color-bg-subtle, rgba(0,0,0,0.05));
        }
    </style>
</x-layouts.admin>
