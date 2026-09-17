<x-layouts.admin 
    :pageTitle="$campaign->name" 
    :pageSubtitle="__('crm.campaigns.subtitle')"
>
    <!-- KPI Row -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
        <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
            <span style="font-size: 0.8rem; color: #64748B; font-weight: 600;">Total Budget</span>
            <div style="font-size: 1.5rem; font-weight: 800; color: #0F172A; margin-top: 0.35rem;">
                {{ number_format($campaign->budget, 2) }} {{ $campaign->currency ?? currency_code() }}
            </div>
        </div>

        <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
            <span style="font-size: 0.8rem; color: #64748B; font-weight: 600;">Attributed Revenue</span>
            <div style="font-size: 1.5rem; font-weight: 800; color: #059669; margin-top: 0.35rem;">
                {{ number_format($campaign->revenue_generated, 2) }} {{ $campaign->currency ?? currency_code() }}
            </div>
        </div>

        <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
            <span style="font-size: 0.8rem; color: #64748B; font-weight: 600;">Return on Investment (ROI)</span>
            <div style="font-size: 1.5rem; font-weight: 800; color: {{ $campaign->roi_percentage >= 0 ? '#16A34A' : '#DC2626' }}; margin-top: 0.35rem;">
                {{ $campaign->roi_percentage }}%
            </div>
        </div>

        <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
            <span style="font-size: 0.8rem; color: #64748B; font-weight: 600;">Leads Acquired</span>
            <div style="font-size: 1.5rem; font-weight: 800; color: #0284C7; margin-top: 0.35rem;">
                {{ $campaign->leads->count() }}
            </div>
        </div>
    </div>

    <!-- Attributed Leads Table -->
    <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
        <h3 style="font-size: 1rem; font-weight: 700; margin-bottom: 1rem; border-bottom: 1px solid #F1F5F9; padding-bottom: 0.5rem;">
            Attributed Prospective Leads
        </h3>

        @if($campaign->leads->isEmpty())
            <p style="font-size: 0.85rem; color: #94A3B8; text-align: center; margin: 2rem 0;">No leads attributed to this campaign yet.</p>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: start; font-size: 0.875rem;">
                    <thead>
                        <tr style="border-bottom: 1px solid #E2E8F0; color: #64748B;">
                            <th style="padding: 0.75rem;">Lead Number</th>
                            <th style="padding: 0.75rem;">Name</th>
                            <th style="padding: 0.75rem;">Status</th>
                            <th style="padding: 0.75rem;">Value</th>
                            <th style="padding: 0.75rem;">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($campaign->leads as $lead)
                            <tr style="border-bottom: 1px solid #F1F5F9;">
                                <td style="padding: 0.75rem; font-weight: 700; color: #0284C7;">
                                    <a href="{{ route('admin.crm.leads.show', $lead->id) }}">{{ $lead->lead_number }}</a>
                                </td>
                                <td style="padding: 0.75rem; font-weight: 600;">{{ $lead->full_name }}</td>
                                <td style="padding: 0.75rem;">{{ ucfirst($lead->status) }}</td>
                                <td style="padding: 0.75rem; font-weight: 700; color: #059669;">{{ number_format($lead->estimated_value ?? 0, 2) }} {{ $lead->currency ?? currency_code() }}</td>
                                <td style="padding: 0.75rem; font-size: 0.8rem; color: #64748B;">{{ $lead->created_at->format('Y-m-d') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-layouts.admin>
