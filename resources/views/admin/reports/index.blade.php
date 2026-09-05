<x-layouts.admin 
    :pageTitle="__('admin.reports.title')" 
    :pageSubtitle="__('admin.reports.subtitle')"
    :breadcrumbs="[__('admin.menu.reports') => route('admin.reports.index')]"
>
    <!-- Filter Bar -->
    <div class="shop-toolbar" style="margin-bottom: 2rem;">
        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
            <select class="form-select text-sm" style="width: auto;">
                <option value="last_30">{{ __('admin.reports.last_30_days') }}</option>
                <option value="this_quarter">{{ __('admin.reports.this_quarter') }}</option>
                <option value="year_to_date">{{ __('admin.reports.year_to_date') }}</option>
            </select>

            <select class="form-select text-sm" style="width: auto;">
                <option value="all_channels">{{ __('admin.reports.all_channels') }}</option>
                <option value="online">{{ __('admin.reports.online_only') }}</option>
                <option value="offline">{{ __('admin.reports.pos_only') }}</option>
            </select>
        </div>

        <button type="button" class="btn btn-secondary btn-sm" onclick="window.toast.info('{{ app()->getLocale() == 'ar' ? 'جاري تجهيز وتصدير التقرير المالي التنفيذي...' : 'Generating and exporting executive CSV report...' }}', 'CSV Export')">
            <i class="fa-solid fa-file-csv mr-1.5 ml-1.5"></i> {{ __('admin.reports.export_csv') }}
        </button>
    </div>

    <!-- KPI Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; margin-bottom: 2.5rem;">
        <div class="card stat-card stat-accent">
            <div class="stat-label">{{ __('admin.reports.gross_revenue') }}</div>
            <div class="stat-value">${{ number_format($kpi['total_sales'], 2) }}</div>
            <div class="stat-footer text-success font-bold"><i class="fa-solid fa-arrow-trend-up mr-1 ml-1"></i> {{ $kpi['growth_rate'] }} YoY</div>
        </div>

        <div class="card stat-card stat-success">
            <div class="stat-label">{{ __('admin.reports.avg_order_value') }}</div>
            <div class="stat-value">${{ number_format($kpi['average_order_value'], 2) }}</div>
            <div class="stat-footer">{{ __('admin.reports.across_channels') }}</div>
        </div>

        <div class="card stat-card">
            <div class="stat-label">{{ __('admin.reports.total_transactions') }}</div>
            <div class="stat-value">{{ $kpi['total_orders'] }}</div>
            <div class="stat-footer">{{ __('admin.reports.combined_channels') }}</div>
        </div>

        <div class="card stat-card stat-warning">
            <div class="stat-label">{{ __('admin.kpi.customers_registered') }}</div>
            <div class="stat-value">{{ $kpi['total_customers'] }}</div>
            <div class="stat-footer text-success font-bold">84% {{ __('admin.reports.repeat_rate') }}</div>
        </div>
    </div>

    <!-- Category Sales Breakdown -->
    <div class="card" style="padding: 2rem; margin-bottom: 2rem;">
        <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;">
            {{ __('admin.reports.volume_by_system') }}
        </h3>

        <div style="display: flex; flex-direction: column; gap: 1.25rem;">
            @foreach($salesData['categories'] as $cat)
                <div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.35rem; font-size: 0.9375rem;">
                        <span class="font-bold">{{ $cat['name'] }}</span>
                        <span><strong>${{ number_format($cat['amount'], 2) }}</strong> ({{ $cat['percentage'] }}%)</span>
                    </div>
                    <div class="progress-track">
                        <div class="progress-fill" style="width: {{ $cat['percentage'] }}%;"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-layouts.admin>
