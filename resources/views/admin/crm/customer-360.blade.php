<x-layouts.admin 
    :pageTitle="__('crm.customer_360.title') . ': ' . $customer->name" 
    :pageSubtitle="__('crm.customer_360.subtitle')"
>
    <!-- Header Banner -->
    <div style="background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0); padding: 1.25rem 1.5rem; border-radius: 0.75rem; margin-bottom: 1.5rem; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.25rem;">
                <h2 style="font-size: 1.25rem; font-weight: 800; color: #0F172A; margin: 0;">{{ $customer->name }}</h2>
                <span style="font-size: 0.75rem; font-weight: 700; padding: 0.2rem 0.6rem; border-radius: 9999px; background: #FEF3C7; color: #D97706;">
                    {{ $metrics['loyalty_tier'] }} ({{ $metrics['loyalty_points'] }} pts)
                </span>
                @php
                    $lifecycleColors = [
                        'vip' => ['bg' => '#F5F3FF', 'text' => '#7C3AED'],
                        'repeat_customer' => ['bg' => '#ECFDF5', 'text' => '#059669'],
                        'customer' => ['bg' => '#EFF6FF', 'text' => '#2563EB'],
                        'at_risk' => ['bg' => '#FFFBEB', 'text' => '#D97706'],
                        'inactive' => ['bg' => '#FEF2F2', 'text' => '#DC2626'],
                        'prospect' => ['bg' => '#F8FAFC', 'text' => '#64748B'],
                    ];
                    $lc = $lifecycleColors[$metrics['lifecycle']] ?? ['bg' => '#F1F5F9', 'text' => '#475569'];
                @endphp
                <span style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase; padding: 0.2rem 0.6rem; border-radius: 9999px; background: {{ $lc['bg'] }}; color: {{ $lc['text'] }};">
                    {{ str_replace('_', ' ', $metrics['lifecycle']) }}
                </span>
            </div>
            <div style="font-size: 0.85rem; color: #64748B;">
                {{ $customer->email }} &bull; {{ $customer->phone ?? 'No phone' }} &bull; {{ $customer->country }}
            </div>
        </div>

        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('admin.crm.opportunities.create') }}?customer_id={{ $customer->id }}" class="btn btn-secondary btn-sm">
                <i class="fa-solid fa-plus text-xs"></i> New Opportunity
            </a>
            <a href="{{ route('admin.crm.activities.create') }}?customer_id={{ $customer->id }}" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-calendar-plus text-xs"></i> Schedule Activity
            </a>
            <a href="{{ route('admin.customers.show', $customer->id) }}" class="btn btn-ghost btn-sm">
                <i class="fa-solid fa-arrow-left mr-1 ml-1"></i> Customer Account
            </a>
        </div>
    </div>

    <!-- Smart CRM Intelligence Recommendations -->
    @if(!empty($recommendations))
        <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1.5rem;">
            @foreach($recommendations as $rec)
                <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.85rem 1.25rem; border-radius: 0.5rem; background: {{ $rec['level'] === 'danger' ? '#FEF2F2' : ($rec['level'] === 'warning' ? '#FFFBEB' : ($rec['level'] === 'success' ? '#ECFDF5' : '#EFF6FF')) }}; border: 1px solid {{ $rec['level'] === 'danger' ? '#FCA5A5' : ($rec['level'] === 'warning' ? '#FDE68A' : ($rec['level'] === 'success' ? '#A7F3D0' : '#BFDBFE')) }};">
                    <i class="{{ $rec['icon'] }} {{ $rec['level'] === 'danger' ? 'text-red-600' : ($rec['level'] === 'warning' ? 'text-amber-600' : ($rec['level'] === 'success' ? 'text-emerald-600' : 'text-blue-600')) }} text-lg"></i>
                    <div style="font-size: 0.875rem; font-weight: 600; color: #0F172A;">
                        {{ app()->getLocale() === 'ar' ? $rec['text_ar'] : $rec['text_en'] }}
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- 4 Key Metrics Row -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
        <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
            <span style="font-size: 0.8rem; color: #64748B; font-weight: 600;">{{ __('crm.customer_360.lifetime_revenue') }}</span>
            <div style="font-size: 1.75rem; font-weight: 800; color: #059669; margin-top: 0.25rem;">
                {{ number_format($metrics['total_revenue'], 2) }} <span style="font-size: 0.85rem;">{{ currency_code() }}</span>
            </div>
            <span style="font-size: 0.75rem; color: #64748B;">
                Strict Delivered Revenue
            </span>
        </div>

        <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
            <span style="font-size: 0.8rem; color: #64748B; font-weight: 600;">{{ __('crm.customer_360.delivered_orders') }}</span>
            <div style="font-size: 1.75rem; font-weight: 800; color: #0284C7; margin-top: 0.25rem;">
                {{ $metrics['delivered_orders_count'] }}
            </div>
            <span style="font-size: 0.75rem; color: #64748B;">
                First: {{ $metrics['first_order_date']?->format('M Y') ?? 'None' }}
            </span>
        </div>

        <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
            <span style="font-size: 0.8rem; color: #64748B; font-weight: 600;">{{ __('crm.customer_360.aov') }}</span>
            <div style="font-size: 1.75rem; font-weight: 800; color: #7C3AED; margin-top: 0.25rem;">
                {{ number_format($metrics['average_order_value'], 2) }} <span style="font-size: 0.85rem;">{{ currency_code() }}</span>
            </div>
            <span style="font-size: 0.75rem; color: #64748B;">
                Per Completed Order
            </span>
        </div>

        <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
            <span style="font-size: 0.8rem; color: #64748B; font-weight: 600;">{{ __('crm.customer_360.reorder_cycle') }}</span>
            <div style="font-size: 1.75rem; font-weight: 800; color: #0F172A; margin-top: 0.25rem;">
                {{ $metrics['order_frequency_days'] ? $metrics['order_frequency_days'] . ' days' : 'N/A' }}
            </div>
            <span style="font-size: 0.75rem; color: #64748B;">
                Last: {{ $metrics['last_order_date']?->diffForHumans() ?? 'Never' }}
            </span>
        </div>
    </div>

    <!-- Main 2-Column Layout -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 1.5rem;">
        <!-- Left: Open Deals, Scheduled Activities & Notes -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <!-- Open Opportunities -->
            <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
                <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 1rem; border-bottom: 1px solid #F1F5F9; padding-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fa-solid fa-handshake text-amber-500"></i> {{ __('crm.customer_360.open_deals') }}
                </h3>

                @forelse($open_opportunities as $opp)
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.65rem 0.75rem; border-radius: 0.5rem; background: #F8FAFC; border: 1px solid #E2E8F0; margin-bottom: 0.5rem;">
                        <div>
                            <a href="{{ route('admin.crm.opportunities.show', $opp->id) }}" style="font-weight: 700; font-size: 0.85rem; color: #0284C7; text-decoration: none;">
                                {{ $opp->name }}
                            </a>
                            <div style="font-size: 0.75rem; color: #64748B;">Stage: {{ $opp->stage?->name }} &bull; {{ $opp->probability }}%</div>
                        </div>
                        <strong style="color: #059669; font-size: 0.85rem;">{{ number_format($opp->value, 2) }} {{ currency_code() }}</strong>
                    </div>
                @empty
                    <p style="font-size: 0.85rem; color: #94A3B8; text-align: center; margin: 1rem 0;">No open opportunities.</p>
                @endforelse
            </div>

            <!-- Scheduled Tasks -->
            <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
                <h3 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 1rem; border-bottom: 1px solid #F1F5F9; padding-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fa-solid fa-list-check text-emerald-500"></i> {{ __('crm.customer_360.pending_tasks') }}
                </h3>

                @forelse($pending_activities as $act)
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.65rem 0.75rem; border-radius: 0.5rem; background: #F8FAFC; border: 1px solid #E2E8F0; margin-bottom: 0.5rem;">
                        <div>
                            <span style="font-weight: 700; font-size: 0.85rem; color: #0F172A;">{{ $act->subject }}</span>
                            <div style="font-size: 0.75rem; color: #64748B;">Due: {{ $act->due_at?->format('Y-m-d') }} &bull; {{ $act->assignee?->name }}</div>
                        </div>
                        <form action="{{ route('admin.crm.activities.complete', $act->id) }}" method="POST" style="margin: 0;">
                            @csrf
                            <button type="submit" class="btn btn-xs btn-outline btn-success">
                                <i class="fa-solid fa-check"></i>
                            </button>
                        </form>
                    </div>
                @empty
                    <p style="font-size: 0.85rem; color: #94A3B8; text-align: center; margin: 1rem 0;">No pending follow-ups scheduled.</p>
                @endforelse
            </div>
        </div>

        <!-- Right: Unified Interactive Timeline -->
        <div class="card" style="padding: 1.5rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
            <h3 style="font-size: 1rem; font-weight: 700; margin-bottom: 1.25rem; border-bottom: 1px solid #F1F5F9; padding-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fa-solid fa-timeline text-indigo-500"></i> {{ __('crm.customer_360.unified_timeline') }}
            </h3>

            <div style="position: relative; padding-inline-start: 1.5rem; display: flex; flex-direction: column; gap: 1.25rem;">
                <div style="position: absolute; inset-inline-start: 6px; top: 8px; bottom: 8px; width: 2px; background: #E2E8F0;"></div>

                @forelse($timeline as $event)
                    <div style="position: relative;">
                        <!-- Timeline Node Icon -->
                        <div style="position: absolute; inset-inline-start: -1.75rem; top: 2px; width: 20px; height: 20px; border-radius: 50%; background: {{ $event['color'] }}; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 0.6rem; box-shadow: 0 0 0 3px #ffffff;">
                            <i class="{{ $event['icon'] }}"></i>
                        </div>

                        <div>
                            <strong style="font-size: 0.875rem; color: #0F172A; display: block;">{{ $event['title'] }}</strong>
                            <p style="font-size: 0.8rem; color: #475569; margin: 0.2rem 0 0.25rem;">{{ $event['details'] }}</p>
                            <span style="font-size: 0.7rem; color: #94A3B8;">
                                {{ $event['timestamp']?->format('Y-m-d H:i') }} &bull; By {{ $event['actor'] }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p style="font-size: 0.85rem; color: #94A3B8;">No timeline history recorded.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.admin>
