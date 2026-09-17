<x-layouts.admin 
    :pageTitle="__('crm.campaigns.title')" 
    :pageSubtitle="__('crm.campaigns.subtitle')"
>
    <!-- Top Action Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0; color: #0F172A;">
            <i class="fa-solid fa-bullhorn text-purple-500 mr-2 ml-2"></i> All Marketing Channels & Campaigns
        </h3>
        <a href="{{ route('admin.crm.campaigns.create') }}" class="btn btn-primary btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem;">
            <i class="fa-solid fa-plus text-xs"></i> New Campaign
        </a>
    </div>

    <!-- Campaigns Table -->
    <div class="card" style="padding: 0; border-radius: 0.75rem; overflow: hidden; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: start; font-size: 0.875rem;">
                <thead>
                    <tr style="background: #F8FAFC; border-bottom: 1px solid #E2E8F0; color: #475569;">
                        <th style="padding: 0.85rem 1rem; font-weight: 700;">{{ __('crm.campaigns.campaign_name') }}</th>
                        <th style="padding: 0.85rem 1rem; font-weight: 700;">{{ __('crm.campaigns.type') }}</th>
                        <th style="padding: 0.85rem 1rem; font-weight: 700;">{{ __('crm.campaigns.budget') }}</th>
                        <th style="padding: 0.85rem 1rem; font-weight: 700;">{{ __('crm.campaigns.revenue') }}</th>
                        <th style="padding: 0.85rem 1rem; font-weight: 700;">{{ __('crm.campaigns.roi') }}</th>
                        <th style="padding: 0.85rem 1rem; font-weight: 700;">Leads</th>
                        <th style="padding: 0.85rem 1rem; font-weight: 700;">Status</th>
                        <th style="padding: 0.85rem 1rem; text-align: end; font-weight: 700;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($campaigns as $camp)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.85rem 1rem;">
                                <a href="{{ route('admin.crm.campaigns.show', $camp->id) }}" style="font-weight: 700; color: #0284C7; text-decoration: none;">
                                    {{ $camp->name }}
                                </a>
                                @if($camp->utm_campaign)
                                    <span style="font-size: 0.75rem; color: #64748B; display: block;">utm_campaign={{ $camp->utm_campaign }}</span>
                                @endif
                            </td>
                            <td style="padding: 0.85rem 1rem;">
                                <span style="font-size: 0.8rem; font-weight: 600; color: #334155;">{{ ucfirst($camp->type) }}</span>
                            </td>
                            <td style="padding: 0.85rem 1rem; font-weight: 600;">
                                {{ number_format($camp->budget, 2) }} {{ $camp->currency ?? currency_code() }}
                            </td>
                            <td style="padding: 0.85rem 1rem; font-weight: 800; color: #059669;">
                                {{ number_format($camp->revenue_generated, 2) }} {{ $camp->currency ?? currency_code() }}
                            </td>
                            <td style="padding: 0.85rem 1rem;">
                                <span style="font-weight: 800; font-size: 0.85rem; color: {{ $camp->roi_percentage > 0 ? '#16A34A' : ($camp->roi_percentage < 0 ? '#DC2626' : '#64748B') }};">
                                    {{ $camp->roi_percentage }}%
                                </span>
                            </td>
                            <td style="padding: 0.85rem 1rem; font-weight: 700;">
                                {{ $camp->leads_count }}
                            </td>
                            <td style="padding: 0.85rem 1rem;">
                                <span style="font-size: 0.75rem; font-weight: 700; padding: 0.2rem 0.55rem; border-radius: 9999px; background: {{ $camp->status === 'active' ? '#DCFCE7' : '#FEF3C7' }}; color: {{ $camp->status === 'active' ? '#16A34A' : '#D97706' }};">
                                    {{ ucfirst($camp->status) }}
                                </span>
                            </td>
                            <td style="padding: 0.85rem 1rem; text-align: end;">
                                <a href="{{ route('admin.crm.campaigns.show', $camp->id) }}" class="btn btn-ghost btn-xs">
                                    <i class="fa-solid fa-eye text-sky-600"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 3rem; color: #94A3B8;">
                                No marketing campaigns created yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($campaigns->hasPages())
            <div style="padding: 1rem; border-top: 1px solid #E2E8F0;">
                {{ $campaigns->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>
