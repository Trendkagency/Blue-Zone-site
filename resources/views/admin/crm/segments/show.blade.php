<x-layouts.admin 
    :pageTitle="'Cohort: ' . $segment->name" 
    :pageSubtitle="__('crm.segments.subtitle')"
>
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; background: var(--card-bg, #ffffff); padding: 1rem 1.25rem; border-radius: 0.75rem; border: 1px solid var(--border-color, #E2E8F0);">
        <div>
            <strong style="font-size: 1.1rem; color: #0F172A;">{{ $segment->name }}</strong>
            <p style="margin: 0.25rem 0 0; font-size: 0.85rem; color: #64748B;">{{ $segment->description }}</p>
        </div>
        <div style="font-size: 1.25rem; font-weight: 800; color: #0284C7;">
            {{ $customers->count() }} Customers
        </div>
    </div>

    <!-- Customers in Segment Table -->
    <div class="card" style="padding: 0; border-radius: 0.75rem; overflow: hidden; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: start; font-size: 0.875rem;">
                <thead>
                    <tr style="background: #F8FAFC; border-bottom: 1px solid #E2E8F0; color: #475569;">
                        <th style="padding: 0.85rem 1rem; font-weight: 700;">Customer</th>
                        <th style="padding: 0.85rem 1rem; font-weight: 700;">Phone / Email</th>
                        <th style="padding: 0.85rem 1rem; font-weight: 700;">Total Orders</th>
                        <th style="padding: 0.85rem 1rem; font-weight: 700;">Total Spent</th>
                        <th style="padding: 0.85rem 1rem; font-weight: 700;">Tier</th>
                        <th style="padding: 0.85rem 1rem; text-align: end; font-weight: 700;">360 Profile</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $c)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.85rem 1rem;">
                                <strong style="color: #0F172A;">{{ $c->name }}</strong>
                            </td>
                            <td style="padding: 0.85rem 1rem; font-size: 0.8rem; color: #64748B;">
                                {{ $c->phone ?? $c->email }}
                            </td>
                            <td style="padding: 0.85rem 1rem; font-weight: 700;">
                                {{ $c->total_orders }}
                            </td>
                            <td style="padding: 0.85rem 1rem; font-weight: 800; color: #059669;">
                                {{ number_format($c->total_spent, 2) }} {{ currency_code() }}
                            </td>
                            <td style="padding: 0.85rem 1rem;">
                                <span style="font-size: 0.75rem; font-weight: 700; padding: 0.15rem 0.5rem; border-radius: 0.25rem; background: #FEF3C7; color: #D97706;">
                                    {{ $c->tier }}
                                </span>
                            </td>
                            <td style="padding: 0.85rem 1rem; text-align: end;">
                                <a href="{{ route('admin.customers.crm-360', $c->id) }}" class="btn btn-outline btn-xs">
                                    <i class="fa-solid fa-user-check text-xs mr-1 ml-1"></i> Customer 360
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 3rem; color: #94A3B8;">
                                No customers currently meet the conditions for this segment.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
