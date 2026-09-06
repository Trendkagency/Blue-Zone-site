<x-layouts.admin 
    :pageTitle="__('admin.menu.reports')" 
    pageSubtitle="Financial analytics, multi-channel performance, inventory valuations, and customer growth."
    :breadcrumbs="['Reports' => route('admin.reports.index')]"
>
    <!-- Filter Bar -->
    <div class="shop-toolbar" style="margin-bottom: 2rem;">
        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
            <select class="form-select text-sm" style="width: auto;">
                <option value="last_30">Last 30 Days</option>
                <option value="this_quarter">This Quarter (Q3 2026)</option>
                <option value="year_to_date">Year to Date (2026)</option>
            </select>

            <select class="form-select text-sm" style="width: auto;">
                <option value="all_channels">All Channels (Online + POS)</option>
                <option value="online">Online Store Only</option>
                <option value="offline">Flagship Boutique POS Only</option>
            </select>
        </div>

        <button type="button" class="btn btn-secondary btn-sm">
            📥 Export Executive Report (CSV)
        </button>
    </div>

    <!-- KPI Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; margin-bottom: 2.5rem;">
        <div class="card stat-card stat-accent">
            <div class="stat-label">Gross Revenue</div>
            <div class="stat-value">${{ number_format($kpi['total_sales'], 2) }}</div>
            <div class="stat-footer text-success font-bold">▲ {{ $kpi['growth_rate'] }} YoY</div>
        </div>

        <div class="card stat-card stat-success">
            <div class="stat-label">Average Order Value</div>
            <div class="stat-value">${{ number_format($kpi['average_order_value'], 2) }}</div>
            <div class="stat-footer">Across all channels</div>
        </div>

        <div class="card stat-card">
            <div class="stat-label">Total Transactions</div>
            <div class="stat-value">{{ $kpi['total_orders'] }}</div>
            <div class="stat-footer">Online & POS combined</div>
        </div>

        <div class="card stat-card stat-warning">
            <div class="stat-label">Registered Customers</div>
            <div class="stat-value">{{ $kpi['total_customers'] }}</div>
            <div class="stat-footer text-success font-bold">84% Repeat Rate</div>
        </div>
    </div>

    <!-- Category Sales Breakdown -->
    <div class="card" style="padding: 2rem; margin-bottom: 2rem;">
        <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;">
            Sales Volume by Longevity Health System
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
