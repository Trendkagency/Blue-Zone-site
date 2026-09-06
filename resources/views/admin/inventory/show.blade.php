<x-layouts.admin 
    :pageTitle="'Stock Details: ' . $item['product_name_en']" 
    pageSubtitle="Location-specific stock inventory levels and allocated reservation metrics."
    :breadcrumbs="['Inventory' => route('admin.inventory.index'), $item['product_name_en'] => route('admin.inventory.show', $item['id'])]"
>
    <div style="display: grid; grid-template-columns: 1fr 340px; gap: 2rem;">
        <div>
            <!-- Stock Detail KPI -->
            <div class="card" style="padding: 2rem; margin-bottom: 2rem;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1.5rem;">
                    <div>
                        <div class="text-xs text-muted">Current Physical Stock</div>
                        <div class="font-black text-2xl" style="color: var(--color-text-primary);">{{ $item['current_stock'] }} units</div>
                    </div>
                    <div>
                        <div class="text-xs text-muted">Available for Dispatch</div>
                        <div class="font-black text-2xl text-success">{{ $item['available_stock'] }} units</div>
                    </div>
                    <div>
                        <div class="text-xs text-muted">Allocated / Reserved</div>
                        <div class="font-black text-2xl text-muted">{{ $item['reserved_stock'] }} units</div>
                    </div>
                </div>
            </div>

            <!-- Recent Movements for this SKU -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Movement Audit Log</h3>
                </div>
                <div class="table-responsive" style="border: none; border-radius: 0;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Log ID</th>
                                <th>Action</th>
                                <th>Delta</th>
                                <th>Authorized By</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(array_slice($movements, 0, 4) as $m)
                                <tr>
                                    <td class="font-mono text-xs">{{ $m['id'] }}</td>
                                    <td><span class="badge badge-neutral text-xs">{{ $m['movement_type'] }}</span></td>
                                    <td class="font-bold {{ $m['quantity'] < 0 ? 'text-danger' : 'text-success' }}">
                                        {{ $m['quantity'] > 0 ? '+' : '' }}{{ $m['quantity'] }}
                                    </td>
                                    <td class="text-xs">{{ $m['user'] }}</td>
                                    <td class="text-xs text-muted">{{ $m['date'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Side Location Card -->
        <div class="card" style="padding: 1.5rem;">
            <h4 style="font-size: 1rem; font-weight: 800; margin-bottom: 1rem;">Location Specification</h4>
            <div class="text-sm" style="display: flex; flex-direction: column; gap: 0.75rem;">
                <div><strong>Location:</strong> {{ $item['location_name_en'] }}</div>
                <div><strong>SKU:</strong> {{ $item['sku'] }}</div>
                <div><strong>Variant:</strong> {{ $item['variant_en'] }}</div>
                <div><strong>Inventory Valuation:</strong> ${{ number_format($item['current_stock'] * $item['unit_cost'], 2) }}</div>
            </div>

            <a href="{{ route('admin.inventory.transfers') }}" class="btn btn-primary btn-sm" style="width: 100%; margin-top: 1.5rem;">
                🔄 Relocate Stock
            </a>
        </div>
    </div>
</x-layouts.admin>
