<x-layouts.admin 
    :pageTitle="__('admin.menu.customers')" 
    pageSubtitle="Customer relationship profiles, member tiers, purchase history, and delivery addresses."
    :breadcrumbs="['Customers' => route('admin.customers.index')]"
>
    <x-slot name="actions">
        <a href="{{ route('admin.customers.create') }}" class="btn btn-primary">
            + New Customer Profile
        </a>
    </x-slot>

    <!-- Customers Table -->
    <div class="card">
        <div class="table-responsive" style="border: none; border-radius: 0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Contact</th>
                        <th>Location</th>
                        <th>Member Tier</th>
                        <th>Orders Count</th>
                        <th>Total Spent</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customers as $c)
                        <tr>
                            <td>
                                <div class="font-bold text-sm text-primary">
                                    <a href="{{ route('admin.customers.show', $c['id']) }}">
                                        {{ $c['name'] }}
                                    </a>
                                </div>
                                <div class="text-xs text-muted">Member since {{ $c['registered_at'] }}</div>
                            </td>
                            <td class="text-sm">
                                <div>{{ $c['email'] }}</div>
                                <div class="text-xs text-muted">{{ $c['phone'] }}</div>
                            </td>
                            <td>{{ $c['city'] }}, {{ $c['country'] }}</td>
                            <td>
                                <span class="badge badge-accent text-xs">{{ $c['tier'] }}</span>
                            </td>
                            <td class="font-bold">{{ $c['orders_count'] }} orders</td>
                            <td class="font-bold text-success">${{ number_format($c['total_spent'], 2) }}</td>
                            <td>
                                <x-status-badge :status="$c['status']" />
                            </td>
                            <td>
                                <a href="{{ route('admin.customers.show', $c['id']) }}" class="action-btn" title="View Customer Dossier">
                                    👁️
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <x-pagination :currentPage="$currentPage" :totalPages="$totalPages" :totalItems="count($customers)" />
    </div>
</x-layouts.admin>
