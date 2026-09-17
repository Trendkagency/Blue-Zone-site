<x-layouts.admin 
    :pageTitle="__('crm.dashboard.title')" 
    :pageSubtitle="__('crm.dashboard.subtitle')"
>
    <!-- Quick Actions Bar -->
    <div style="display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; background: var(--card-bg, #ffffff); padding: 1rem 1.25rem; border-radius: 0.75rem; border: 1px solid var(--border-color, #E2E8F0);">
        <div style="font-weight: 700; font-size: 0.95rem; color: var(--text-color, #1E293B);">
            <i class="fa-solid fa-bolt text-amber-500 mr-2 ml-2"></i> {{ __('crm.dashboard.quick_actions') }}
        </div>
        <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
            <a href="{{ route('admin.crm.leads.create') }}" class="btn btn-primary btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem;">
                <i class="fa-solid fa-user-plus text-xs"></i> {{ __('crm.dashboard.create_lead') }}
            </a>
            <a href="{{ route('admin.crm.opportunities.create') }}" class="btn btn-secondary btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem;">
                <i class="fa-solid fa-handshake text-xs"></i> {{ __('crm.dashboard.create_opportunity') }}
            </a>
            <a href="{{ route('admin.crm.activities.create') }}" class="btn btn-outline btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem;">
                <i class="fa-solid fa-calendar-plus text-xs"></i> {{ __('crm.dashboard.schedule_activity') }}
            </a>
            <a href="{{ route('admin.crm.opportunities.index') }}" class="btn btn-ghost btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem;">
                <i class="fa-solid fa-table-columns text-xs text-indigo-400"></i> {{ __('crm.dashboard.view_pipeline') }}
            </a>
        </div>
    </div>

    <!-- 4 High Impact KPI Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
        <!-- Leads & Conversion -->
        <div class="card stat-card stat-accent" style="padding: 1.25rem; border-radius: 0.75rem;">
            <div class="stat-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                <span class="stat-label" style="font-size: 0.85rem; font-weight: 600; color: #64748B;">{{ __('crm.dashboard.new_leads') }} / {{ __('crm.dashboard.conversion_rate') }}</span>
                <span class="stat-icon"><i class="fa-solid fa-bullseye text-sky-500 text-lg"></i></span>
            </div>
            <div class="stat-value" style="font-size: 1.75rem; font-weight: 800; color: #0284C7;">
                {{ $leadStats->new_leads ?? 0 }} <span style="font-size: 0.95rem; font-weight: 600; color: #64748B;">({{ $conversionRate }}%)</span>
            </div>
            <div class="stat-footer" style="font-size: 0.8rem; color: #64748B; margin-top: 0.5rem;">
                {{ $leadStats->converted_leads ?? 0 }} {{ __('crm.status.converted') }} &bull; {{ $leadStats->total_leads ?? 0 }} Total Leads
            </div>
        </div>

        <!-- Open Pipeline Value -->
        <div class="card stat-card stat-success" style="padding: 1.25rem; border-radius: 0.75rem;">
            <div class="stat-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                <span class="stat-label" style="font-size: 0.85rem; font-weight: 600; color: #64748B;">{{ __('crm.dashboard.pipeline_value') }}</span>
                <span class="stat-icon"><i class="fa-solid fa-sack-dollar text-emerald-500 text-lg"></i></span>
            </div>
            <div class="stat-value" style="font-size: 1.75rem; font-weight: 800; color: #059669;">
                {{ number_format($oppStats->pipeline_value ?? 0, 2) }} <span style="font-size: 0.9rem; font-weight: 600;">{{ currency_code() }}</span>
            </div>
            <div class="stat-footer" style="font-size: 0.8rem; color: #64748B; margin-top: 0.5rem;">
                {{ __('crm.dashboard.weighted_pipeline') }}: {{ number_format($oppStats->weighted_value ?? 0, 2) }} {{ currency_code() }}
            </div>
        </div>

        <!-- Won Revenue & Win Rate -->
        <div class="card stat-card" style="padding: 1.25rem; border-radius: 0.75rem; border-left: 4px solid #8B5CF6;">
            <div class="stat-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                <span class="stat-label" style="font-size: 0.85rem; font-weight: 600; color: #64748B;">{{ __('crm.dashboard.won_revenue') }}</span>
                <span class="stat-icon"><i class="fa-solid fa-trophy text-purple-500 text-lg"></i></span>
            </div>
            <div class="stat-value" style="font-size: 1.75rem; font-weight: 800; color: #7C3AED;">
                {{ number_format($oppStats->won_value ?? 0, 2) }} <span style="font-size: 0.9rem; font-weight: 600;">{{ currency_code() }}</span>
            </div>
            <div class="stat-footer" style="font-size: 0.8rem; color: #64748B; margin-top: 0.5rem;">
                {{ __('crm.dashboard.win_rate') }}: {{ $winRate }}% ({{ $oppStats->won_opps ?? 0 }} {{ __('crm.status.won') }})
            </div>
        </div>

        <!-- Activities Alert -->
        <div class="card stat-card" style="padding: 1.25rem; border-radius: 0.75rem; border-left: 4px solid {{ $overdueCount > 0 ? '#EF4444' : '#10B981' }};">
            <div class="stat-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                <span class="stat-label" style="font-size: 0.85rem; font-weight: 600; color: #64748B;">{{ __('crm.dashboard.overdue_tasks') }}</span>
                <span class="stat-icon"><i class="fa-solid fa-clock-rotate-left {{ $overdueCount > 0 ? 'text-red-500' : 'text-emerald-500' }} text-lg"></i></span>
            </div>
            <div class="stat-value" style="font-size: 1.75rem; font-weight: 800; color: {{ $overdueCount > 0 ? '#DC2626' : '#059669' }};">
                {{ $overdueCount }} <span style="font-size: 0.85rem; font-weight: 600; color: #64748B;">/ {{ $pendingCount }} {{ __('crm.status.pending') }}</span>
            </div>
            <div class="stat-footer" style="font-size: 0.8rem; color: #64748B; margin-top: 0.5rem;">
                {{ $todayTasks->count() }} {{ __('crm.dashboard.today_tasks') }}
            </div>
        </div>
    </div>

    <!-- Main Grid: Recent Leads & Today's Tasks -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
        <!-- Recent Leads Card -->
        <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid #F1F5F9; padding-bottom: 0.75rem;">
                <h3 style="font-size: 1rem; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fa-solid fa-user-tag text-sky-500"></i> {{ __('crm.dashboard.recent_leads') }}
                </h3>
                <a href="{{ route('admin.crm.leads.index') }}" class="btn btn-ghost btn-xs text-sky-600" style="font-size: 0.75rem;">
                    View All <i class="fa-solid fa-arrow-right text-xs mr-1 ml-1"></i>
                </a>
            </div>

            @if($recentLeads->isEmpty())
                <div style="text-align: center; padding: 2rem; color: #94A3B8;">
                    <i class="fa-solid fa-inbox text-3xl mb-2"></i>
                    <p style="font-size: 0.85rem; margin: 0;">No leads found.</p>
                </div>
            @else
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    @foreach($recentLeads as $lead)
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.65rem 0.75rem; border-radius: 0.5rem; background: #F8FAFC; border: 1px solid #E2E8F0;">
                            <div>
                                <a href="{{ route('admin.crm.leads.show', $lead->id) }}" style="font-weight: 700; font-size: 0.9rem; text-decoration: none; color: #0F172A;">
                                    {{ $lead->full_name }}
                                </a>
                                <div style="font-size: 0.75rem; color: #64748B;">
                                    {{ $lead->lead_number }} &bull; {{ $lead->source?->name ?? 'Direct' }}
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <span style="font-size: 0.75rem; padding: 0.2rem 0.55rem; border-radius: 9999px; font-weight: 700; background: #E0F2FE; color: #0284C7;">
                                    {{ ucfirst($lead->status) }}
                                </span>
                                <a href="{{ route('admin.crm.leads.show', $lead->id) }}" class="btn btn-ghost btn-xs" style="padding: 0.25rem 0.4rem;">
                                    <i class="fa-solid fa-chevron-right text-xs"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Today & Overdue Tasks Card -->
        <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid #F1F5F9; padding-bottom: 0.75rem;">
                <h3 style="font-size: 1rem; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fa-solid fa-list-check text-emerald-500"></i> {{ __('crm.dashboard.today_tasks') }}
                </h3>
                <a href="{{ route('admin.crm.activities.index') }}" class="btn btn-ghost btn-xs text-sky-600" style="font-size: 0.75rem;">
                    View All <i class="fa-solid fa-arrow-right text-xs mr-1 ml-1"></i>
                </a>
            </div>

            @if($todayTasks->isEmpty())
                <div style="text-align: center; padding: 2rem; color: #94A3B8;">
                    <i class="fa-solid fa-circle-check text-3xl mb-2 text-emerald-400"></i>
                    <p style="font-size: 0.85rem; margin: 0;">All tasks completed for today!</p>
                </div>
            @else
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    @foreach($todayTasks as $task)
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.65rem 0.75rem; border-radius: 0.5rem; background: #F8FAFC; border: 1px solid #E2E8F0;">
                            <div>
                                <div style="font-weight: 700; font-size: 0.85rem; color: #0F172A;">
                                    <i class="fa-solid fa-tag text-xs text-sky-400 mr-1 ml-1"></i> {{ $task->subject }}
                                </div>
                                <div style="font-size: 0.75rem; color: #64748B;">
                                    {{ $task->customer?->name ?? $task->lead?->full_name ?? 'Client' }} &bull; {{ $task->due_at?->format('H:i') ?? 'Today' }}
                                </div>
                            </div>
                            <form action="{{ route('admin.crm.activities.complete', $task->id) }}" method="POST" style="margin: 0;">
                                @csrf
                                <button type="submit" class="btn btn-xs btn-outline btn-success" title="{{ __('crm.activities.complete_btn') }}">
                                    <i class="fa-solid fa-check"></i>
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Secondary Grid: Top Sources & Active Campaigns -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 1.5rem;">
        <!-- Top Lead Sources -->
        <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
            <h3 style="font-size: 1rem; font-weight: 700; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fa-solid fa-chart-pie text-indigo-500"></i> {{ __('crm.dashboard.top_lead_sources') }}
            </h3>
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                @forelse($topSources as $source)
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <span style="font-size: 0.85rem; font-weight: 600; color: #334155;">
                            <span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: {{ $source->color ?? '#0284C7' }}; margin-right: 0.4rem; margin-left: 0.4rem;"></span>
                            {{ app()->getLocale() === 'ar' ? ($source->name_ar ?: $source->name_en) : ($source->name_en ?: $source->name_ar) }}
                        </span>
                        <span style="font-weight: 700; font-size: 0.85rem; color: #0F172A; background: #F1F5F9; padding: 0.15rem 0.5rem; border-radius: 0.35rem;">
                            {{ $source->count }}
                        </span>
                    </div>
                @empty
                    <p style="font-size: 0.85rem; color: #94A3B8;">No lead sources tracked yet.</p>
                @endforelse
            </div>
        </div>

        <!-- Top Campaigns -->
        <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="font-size: 1rem; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fa-solid fa-bullhorn text-purple-500"></i> {{ __('crm.dashboard.top_campaigns') }}
                </h3>
                <a href="{{ route('admin.crm.campaigns.index') }}" class="btn btn-ghost btn-xs text-sky-600" style="font-size: 0.75rem;">
                    {{ __('app.actions.view_all') ?? 'View All' }}
                </a>
            </div>
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                @forelse($topCampaigns as $camp)
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.65rem 0.75rem; border-radius: 0.5rem; background: #F8FAFC; border: 1px solid #E2E8F0;">
                        <div>
                            <div style="font-weight: 700; font-size: 0.85rem; color: #0F172A;">
                                {{ $camp->name }}
                            </div>
                            <div style="font-size: 0.75rem; color: #64748B;">
                                {{ ucfirst($camp->type) }} &bull; ROI: <span class="text-emerald-600 font-bold">{{ $camp->roi_percentage }}%</span>
                            </div>
                        </div>
                        <div style="font-weight: 800; font-size: 0.9rem; color: #059669;">
                            {{ number_format($camp->revenue_generated, 2) }} {{ currency_code() }}
                        </div>
                    </div>
                @empty
                    <p style="font-size: 0.85rem; color: #94A3B8;">No active campaigns with revenue attribution.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.admin>
