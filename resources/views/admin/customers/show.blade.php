<x-layouts.admin 
    :pageTitle="$customer['name']" 
    pageSubtitle="Customer relationship dossier, total lifetime spend, addresses, and order history."
    :breadcrumbs="['Customers' => route('admin.customers.index'), $customer['name'] => route('admin.customers.show', $customer['id'])]"
>
    <div style="display: grid; grid-template-columns: 1fr 340px; gap: 2rem;">
        <div>
            <!-- Stats Row -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
                <div class="card stat-card stat-accent">
                    <div class="stat-label">Total Spend</div>
                    <div class="stat-value text-success">${{ number_format($customer['total_spent'], 2) }}</div>
                </div>
                <div class="card stat-card">
                    <div class="stat-label">Orders Count</div>
                    <div class="stat-value">{{ $customer['orders_count'] }}</div>
                </div>
                <div class="card stat-card stat-success">
                    <div class="stat-label">Member Tier</div>
                    <div class="stat-value" style="font-size: 1.25rem;">{{ $customer['tier'] }}</div>
                </div>
            </div>

            <!-- Customer Orders History -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Order History</h3>
                </div>
                <div class="table-responsive" style="border: none; border-radius: 0;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Gross Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $o)
                                <tr>
                                    <td class="font-bold text-primary">{{ $o['order_number'] }}</td>
                                    <td>{{ $o['date'] }}</td>
                                    <td><x-status-badge :status="$o['status']" /></td>
                                    <td class="font-bold">${{ number_format($o['total'], 2) }}</td>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $o['order_number']) }}" class="action-btn">
                                            👁️
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column: Contact & Addresses -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <div class="card" style="padding: 1.5rem;">
                <h4 style="font-size: 1rem; font-weight: 800; margin-bottom: 1rem;">Contact Info</h4>
                <div class="text-sm" style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <div><strong>Email:</strong> {{ $customer['email'] }}</div>
                    <div><strong>Phone:</strong> {{ $customer['phone'] }}</div>
                    <div><strong>Location:</strong> {{ $customer['city'] }}, {{ $customer['country'] }}</div>
                    <div><strong>Status:</strong> <x-status-badge :status="$customer['status']" /></div>
                </div>
            </div>

            <div class="card" style="padding: 1.5rem;">
                <h4 style="font-size: 1rem; font-weight: 800; margin-bottom: 1rem;">Saved Addresses ({{ count($customer['addresses']) }})</h4>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    @foreach($customer['addresses'] as $ad)
                        <div style="background: var(--color-bg-subtle); padding: 0.75rem 1rem; border-radius: var(--radius-md); font-size: 0.8125rem;">
                            <div class="font-bold">{{ $ad['title'] }}</div>
                            <div class="text-secondary">{{ $ad['street'] }}, {{ $ad['city'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
