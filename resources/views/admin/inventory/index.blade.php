<x-layouts.admin 
    :pageTitle="__('admin.menu.inventory')" 
    pageSubtitle="Centralized multi-location inventory for online fulfillment hubs and offline flagship boutiques."
    :breadcrumbs="['Inventory' => route('admin.inventory.index')]"
>
    <x-slot name="actions">
        <a href="{{ route('admin.inventory.transfers') }}" class="btn btn-primary">
            🔄 {{ __('admin.inventory.transfer_title') }}
        </a>
        <a href="{{ route('admin.inventory.history') }}" class="btn btn-secondary">
            📜 {{ __('admin.menu.stock_history') }}
        </a>
    </x-slot>

    <!-- Location Filter Tabs -->
    <div style="display: flex; gap: 0.75rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
        <button type="button" class="btn btn-primary btn-sm">All Locations (Centralized)</button>
        @foreach($locations as $loc)
            <button type="button" class="btn btn-secondary btn-sm">
                {{ app()->getLocale() === 'ar' ? ($loc['name_ar'] ?? $loc['name_en']) : $loc['name_en'] }}
            </button>
        @endforeach
    </div>

    <!-- Inventory Table -->
    <div class="card">
        <div class="table-responsive" style="border: none; border-radius: 0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Formulation / SKU</th>
                        <th>Location</th>
                        <th>Physical Stock</th>
                        <th>Available</th>
                        <th>Allocated</th>
                        <th>Min Threshold</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stockItems as $item)
                        <tr>
                            <td>
                                <div class="font-bold text-sm text-primary">
                                    <a href="{{ route('admin.inventory.show', $item['id']) }}">
                                        {{ $item['product_name_en'] }}
                                    </a>
                                </div>
                                <div class="text-xs text-muted font-mono">
                                    {{ $item['sku'] }} • {{ $item['variant_en'] }}
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-neutral text-xs">
                                    {{ app()->getLocale() === 'ar' ? ($item['location_name_ar'] ?? $item['location_name_en']) : $item['location_name_en'] }}
                                </span>
                            </td>
                            <td class="font-bold">{{ $item['current_stock'] }} units</td>
                            <td class="font-bold text-success">{{ $item['available_stock'] }} units</td>
                            <td class="text-muted">{{ $item['reserved_stock'] }} units</td>
                            <td>{{ $item['low_stock_threshold'] }} units</td>
                            <td>
                                <x-status-badge :status="$item['status']" />
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a href="{{ route('admin.inventory.transfers') }}" class="btn btn-secondary btn-sm" title="Initiate Transfer">
                                        🔄 Transfer
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <x-pagination :currentPage="$currentPage" :totalPages="$totalPages" :totalItems="count($stockItems)" />
    </div>
</x-layouts.admin>
