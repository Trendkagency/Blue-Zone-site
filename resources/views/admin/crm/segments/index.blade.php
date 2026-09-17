<x-layouts.admin 
    :pageTitle="__('crm.segments.title')" 
    :pageSubtitle="__('crm.segments.subtitle')"
>
    <!-- Top Action Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <p style="font-size: 0.9rem; color: #64748B; margin: 0;">
            Real-time customer segmentation cohorts based on purchase recency, frequency, and monetary lifetime value.
        </p>

        <form method="POST" action="{{ route('admin.crm.segments.refresh') }}">
            @csrf
            <button type="submit" class="btn btn-secondary btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem;">
                <i class="fa-solid fa-arrows-rotate text-xs"></i> {{ __('crm.segments.refresh_all') }}
            </button>
        </form>
    </div>

    <!-- Segments Cards Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem;">
        @forelse($segments as $seg)
            <div class="card" style="padding: 1.5rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0); display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                        <h3 style="font-size: 1.05rem; font-weight: 700; margin: 0; color: #0F172A;">
                            {{ $seg->name }}
                        </h3>
                        <span style="font-size: 0.75rem; font-weight: 700; background: #E0F2FE; color: #0284C7; padding: 0.15rem 0.5rem; border-radius: 9999px;">
                            {{ $seg->slug }}
                        </span>
                    </div>

                    <p style="font-size: 0.85rem; color: #64748B; margin-bottom: 1rem;">
                        {{ $seg->description ?? 'Dynamic customer retention cohort' }}
                    </p>

                    <div style="font-size: 2rem; font-weight: 800; color: #0284C7; margin-bottom: 0.5rem;">
                        {{ number_format($seg->customer_count) }} <span style="font-size: 0.85rem; font-weight: 600; color: #64748B;">clients</span>
                    </div>
                </div>

                <div style="border-top: 1px solid #F1F5F9; padding-top: 0.75rem; display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 0.75rem; color: #94A3B8;">
                        Updated: {{ $seg->last_evaluated_at?->diffForHumans() ?? 'Just now' }}
                    </span>
                    <a href="{{ route('admin.crm.segments.show', $seg->id) }}" class="btn btn-outline btn-xs">
                        {{ __('crm.segments.view_customers') }} <i class="fa-solid fa-arrow-right text-xs mr-1 ml-1"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="card" style="grid-column: 1 / -1; padding: 3rem; text-align: center; color: #94A3B8;">
                <i class="fa-solid fa-layer-group text-4xl mb-2"></i>
                <p>No customer segments configured.</p>
            </div>
        @endforelse
    </div>
</x-layouts.admin>
