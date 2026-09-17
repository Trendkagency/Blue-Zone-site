<x-layouts.admin 
    :pageTitle="__('crm.companies.title')" 
    :pageSubtitle="__('crm.companies.subtitle')"
>
    <!-- Top Action Bar -->
    <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
        <form method="GET" action="{{ route('admin.crm.companies.index') }}" style="display: flex; gap: 0.5rem; flex: 1;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('crm.companies.company_name') }}, {{ __('crm.companies.industry') }}..." 
                class="form-input" style="max-width: 300px; padding: 0.45rem 0.75rem; border-radius: 0.5rem; border: 1px solid #CBD5E1; font-size: 0.875rem;">
            <button type="submit" class="btn btn-secondary btn-sm">
                <i class="fa-solid fa-magnifying-glass"></i> Filter
            </button>
        </form>

        <a href="{{ route('admin.crm.companies.create') }}" class="btn btn-primary btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem;">
            <i class="fa-solid fa-plus text-xs"></i> New Company
        </a>
    </div>

    <!-- Companies Table Card -->
    <div class="card" style="padding: 0; border-radius: 0.75rem; overflow: hidden; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: start; font-size: 0.875rem;">
                <thead>
                    <tr style="background: #F8FAFC; border-bottom: 1px solid #E2E8F0; color: #475569;">
                        <th style="padding: 0.85rem 1rem; font-weight: 700;">{{ __('crm.companies.company_name') }}</th>
                        <th style="padding: 0.85rem 1rem; font-weight: 700;">{{ __('crm.companies.industry') }}</th>
                        <th style="padding: 0.85rem 1rem; font-weight: 700;">Contact</th>
                        <th style="padding: 0.85rem 1rem; font-weight: 700;">Country</th>
                        <th style="padding: 0.85rem 1rem; font-weight: 700;">Linked Deals</th>
                        <th style="padding: 0.85rem 1rem; font-weight: 700;">Status</th>
                        <th style="padding: 0.85rem 1rem; text-align: end; font-weight: 700;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($companies as $company)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.85rem 1rem;">
                                <a href="{{ route('admin.crm.companies.show', $company->id) }}" style="font-weight: 700; color: #0284C7; text-decoration: none;">
                                    {{ $company->name }}
                                </a>
                                @if($company->legal_name)
                                    <span style="font-size: 0.75rem; color: #64748B; display: block;">{{ $company->legal_name }}</span>
                                @endif
                            </td>
                            <td style="padding: 0.85rem 1rem; font-size: 0.8rem; color: #334155;">
                                {{ $company->industry ?? 'Longevity Healthcare' }}
                            </td>
                            <td style="padding: 0.85rem 1rem; font-size: 0.8rem; color: #64748B;">
                                {{ $company->phone ?? $company->email ?? 'N/A' }}
                            </td>
                            <td style="padding: 0.85rem 1rem; font-size: 0.8rem; color: #334155;">
                                {{ $company->country?->name_en ?? 'Saudi Arabia' }}
                            </td>
                            <td style="padding: 0.85rem 1rem; font-weight: 700; color: #0F172A;">
                                {{ $company->opportunities_count }}
                            </td>
                            <td style="padding: 0.85rem 1rem;">
                                <span style="font-size: 0.75rem; font-weight: 700; padding: 0.2rem 0.55rem; border-radius: 9999px; background: #DCFCE7; color: #16A34A;">
                                    {{ ucfirst($company->status) }}
                                </span>
                            </td>
                            <td style="padding: 0.85rem 1rem; text-align: end;">
                                <a href="{{ route('admin.crm.companies.show', $company->id) }}" class="btn btn-ghost btn-xs">
                                    <i class="fa-solid fa-eye text-sky-600"></i>
                                </a>
                                <a href="{{ route('admin.crm.companies.edit', $company->id) }}" class="btn btn-ghost btn-xs">
                                    <i class="fa-solid fa-pen-to-square text-amber-600"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 3rem; color: #94A3B8;">
                                No B2B accounts found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($companies->hasPages())
            <div style="padding: 1rem; border-top: 1px solid #E2E8F0;">
                {{ $companies->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>
