<x-layouts.admin 
    :pageTitle="__('crm.reports.title')" 
    :pageSubtitle="__('crm.reports.subtitle')"
>
    <!-- Date Filter Form -->
    <div style="background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0); padding: 1rem 1.25rem; border-radius: 0.75rem; margin-bottom: 1.5rem;">
        <form method="GET" action="{{ route('admin.crm.reports.index') }}" style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end;">
            <div>
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.25rem;">Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="form-input" style="padding: 0.45rem 0.65rem; border-radius: 0.35rem; border: 1px solid #CBD5E1; font-size: 0.85rem;">
            </div>

            <div>
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.25rem;">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="form-input" style="padding: 0.45rem 0.65rem; border-radius: 0.35rem; border: 1px solid #CBD5E1; font-size: 0.85rem;">
            </div>

            <button type="submit" class="btn btn-primary btn-sm" style="padding: 0.5rem 1.25rem;">
                <i class="fa-solid fa-filter mr-1 ml-1"></i> {{ __('crm.reports.filter') }}
            </button>
        </form>
    </div>

    <!-- Summary KPI Row -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
        <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
            <span style="font-size: 0.8rem; color: #64748B; font-weight: 600;">Total Leads</span>
            <div style="font-size: 1.75rem; font-weight: 800; color: #0284C7; margin-top: 0.25rem;">
                {{ $leadsReport['total_leads'] }}
            </div>
            <span style="font-size: 0.75rem; color: #16A34A; font-weight: 700;">
                {{ $leadsReport['conversion_rate'] }}% Conversion
            </span>
        </div>

        <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
            <span style="font-size: 0.8rem; color: #64748B; font-weight: 600;">Won Revenue</span>
            <div style="font-size: 1.75rem; font-weight: 800; color: #059669; margin-top: 0.25rem;">
                {{ number_format($oppsReport['won_value'], 2) }} {{ currency_code() }}
            </div>
            <span style="font-size: 0.75rem; color: #64748B;">
                {{ $oppsReport['won_deals'] }} Deals Closed
            </span>
        </div>

        <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
            <span style="font-size: 0.8rem; color: #64748B; font-weight: 600;">Opportunity Win Rate</span>
            <div style="font-size: 1.75rem; font-weight: 800; color: #7C3AED; margin-top: 0.25rem;">
                {{ $oppsReport['win_rate'] }}%
            </div>
            <span style="font-size: 0.75rem; color: #64748B;">
                Avg Deal: {{ number_format($oppsReport['avg_deal_size'], 2) }} {{ currency_code() }}
            </span>
        </div>

        <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
            <span style="font-size: 0.8rem; color: #64748B; font-weight: 600;">Open Pipeline</span>
            <div style="font-size: 1.75rem; font-weight: 800; color: #D97706; margin-top: 0.25rem;">
                {{ number_format($oppsReport['open_pipeline_value'], 2) }} {{ currency_code() }}
            </div>
            <span style="font-size: 0.75rem; color: #64748B;">
                In Active Stages
            </span>
        </div>
    </div>

    <!-- Sales Rep Performance Table -->
    <div class="card" style="padding: 1.5rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0); margin-bottom: 1.5rem;">
        <h3 style="font-size: 1rem; font-weight: 700; margin-bottom: 1rem; border-bottom: 1px solid #F1F5F9; padding-bottom: 0.5rem;">
            <i class="fa-solid fa-users-gear text-sky-500 mr-2 ml-2"></i> {{ __('crm.reports.sales_rep_perf') }}
        </h3>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: start; font-size: 0.875rem;">
                <thead>
                    <tr style="border-bottom: 1px solid #E2E8F0; color: #64748B;">
                        <th style="padding: 0.75rem;">Sales Owner</th>
                        <th style="padding: 0.75rem;">Leads Assigned</th>
                        <th style="padding: 0.75rem;">Deals Handled</th>
                        <th style="padding: 0.75rem;">Won Deals</th>
                        <th style="padding: 0.75rem; text-align: end;">Attributed Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ownersReport as $rep)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.75rem; font-weight: 700; color: #0F172A;">
                                {{ $rep['owner']->name }}
                            </td>
                            <td style="padding: 0.75rem;">{{ $rep['leads_count'] }}</td>
                            <td style="padding: 0.75rem;">{{ $rep['opps_count'] }}</td>
                            <td style="padding: 0.75rem; font-weight: 700; color: #16A34A;">{{ $rep['won_deals'] }}</td>
                            <td style="padding: 0.75rem; text-align: end; font-weight: 800; color: #059669;">
                                {{ number_format($rep['won_revenue'], 2) }} {{ currency_code() }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Campaigns ROI Table -->
    <div class="card" style="padding: 1.5rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
        <h3 style="font-size: 1rem; font-weight: 700; margin-bottom: 1rem; border-bottom: 1px solid #F1F5F9; padding-bottom: 0.5rem;">
            <i class="fa-solid fa-bullhorn text-purple-500 mr-2 ml-2"></i> {{ __('crm.reports.campaign_attribution') }}
        </h3>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: start; font-size: 0.875rem;">
                <thead>
                    <tr style="border-bottom: 1px solid #E2E8F0; color: #64748B;">
                        <th style="padding: 0.75rem;">Campaign</th>
                        <th style="padding: 0.75rem;">Leads</th>
                        <th style="padding: 0.75rem;">Budget</th>
                        <th style="padding: 0.75rem;">Attributed Revenue</th>
                        <th style="padding: 0.75rem; text-align: end;">ROI</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($campaignsReport as $cData)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.75rem; font-weight: 700; color: #0F172A;">
                                {{ $cData['campaign']->name }}
                            </td>
                            <td style="padding: 0.75rem;">{{ $cData['leads'] }}</td>
                            <td style="padding: 0.75rem;">{{ number_format($cData['budget'], 2) }} {{ currency_code() }}</td>
                            <td style="padding: 0.75rem; font-weight: 800; color: #059669;">{{ number_format($cData['revenue'], 2) }} {{ currency_code() }}</td>
                            <td style="padding: 0.75rem; text-align: end; font-weight: 800; color: {{ $cData['roi'] >= 0 ? '#16A34A' : '#DC2626' }};">
                                {{ $cData['roi'] }}%
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
