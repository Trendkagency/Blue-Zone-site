<x-layouts.admin 
    :pageTitle="$company->name" 
    :pageSubtitle="__('crm.companies.subtitle')"
>
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; background: var(--card-bg, #ffffff); padding: 1rem 1.25rem; border-radius: 0.75rem; border: 1px solid var(--border-color, #E2E8F0);">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <span style="font-size: 0.85rem; font-weight: 700; padding: 0.3rem 0.75rem; border-radius: 9999px; background: #DCFCE7; color: #16A34A;">
                {{ ucfirst($company->status) }}
            </span>
            <span style="font-size: 0.85rem; color: #64748B;">
                {{ $company->industry ?? 'Healthcare / Longevity' }}
            </span>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('admin.crm.opportunities.create') }}?company_id={{ $company->id }}" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-plus text-xs"></i> New Deal
            </a>
            <a href="{{ route('admin.crm.companies.edit', $company->id) }}" class="btn btn-secondary btn-sm">
                <i class="fa-solid fa-pen-to-square text-xs"></i> Edit
            </a>
        </div>
    </div>

    <!-- Detail Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem;">
        <!-- Left: Company Info -->
        <div class="card" style="padding: 1.5rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
            <h3 style="font-size: 1rem; font-weight: 700; margin-bottom: 1rem; border-bottom: 1px solid #F1F5F9; padding-bottom: 0.5rem;">
                Account Information
            </h3>

            <div style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.875rem;">
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #64748B;">Legal Name:</span>
                    <strong style="color: #0F172A;">{{ $company->legal_name ?? $company->name }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #64748B;">Email:</span>
                    <span style="font-weight: 600;">{{ $company->email ?? 'N/A' }}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #64748B;">Phone:</span>
                    <span style="font-weight: 600;">{{ $company->phone ?? 'N/A' }}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #64748B;">Tax / VAT Number:</span>
                    <span style="font-weight: 600;">{{ $company->tax_number ?? 'N/A' }}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #64748B;">Sales Owner:</span>
                    <span style="font-weight: 600;">{{ $company->owner?->name ?? 'Unassigned' }}</span>
                </div>
            </div>
        </div>

        <!-- Right: Linked Opportunities -->
        <div class="card" style="padding: 1.5rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
            <h3 style="font-size: 1rem; font-weight: 700; margin-bottom: 1rem; border-bottom: 1px solid #F1F5F9; padding-bottom: 0.5rem;">
                Linked Deals & Opportunities
            </h3>

            @forelse($company->opportunities as $opp)
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.65rem 0.75rem; border-radius: 0.5rem; background: #F8FAFC; border: 1px solid #E2E8F0; margin-bottom: 0.5rem;">
                    <div>
                        <a href="{{ route('admin.crm.opportunities.show', $opp->id) }}" style="font-weight: 700; font-size: 0.85rem; color: #0284C7; text-decoration: none;">
                            {{ $opp->name }}
                        </a>
                        <div style="font-size: 0.75rem; color: #64748B;">
                            Stage: {{ $opp->stage?->name }}
                        </div>
                    </div>
                    <strong style="color: #059669; font-size: 0.875rem;">{{ number_format($opp->value, 2) }} {{ $opp->currency ?? currency_code() }}</strong>
                </div>
            @empty
                <p style="font-size: 0.85rem; color: #94A3B8; text-align: center; margin: 1rem 0;">No deals linked yet.</p>
            @endforelse
        </div>
    </div>
</x-layouts.admin>
