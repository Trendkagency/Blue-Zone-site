<x-layouts.admin 
    :pageTitle="__('admin.menu.products')" 
    pageSubtitle="Manage clinical formulations, batch metadata, pricing, and variant availability."
    :breadcrumbs="['Products' => route('admin.products.index')]"
>
    <x-slot name="actions">
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
            + {{ __('admin.menu.add_product') }}
        </a>
    </x-slot>

    <!-- Toolbar Filters -->
    <div class="shop-toolbar" style="margin-bottom: 1.5rem;">
        <div class="search-wrapper" style="max-width: 320px;">
            <svg class="search-icon" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input type="text" class="form-control search-input text-sm" placeholder="Search SKU, name, barcode...">
        </div>

        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
            <select class="form-select text-sm" style="width: auto;">
                <option value="">All Systems</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat['id'] }}">{{ $cat['name_en'] }}</option>
                @endforeach
            </select>

            <select class="form-select text-sm" style="width: auto;">
                <option value="">All Statuses</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
    </div>

    <!-- Products Data Table -->
    <div class="card">
        <div class="table-responsive" style="border: none; border-radius: 0;">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 40px;"><input type="checkbox" class="form-check-input"></th>
                        <th>Product</th>
                        <th>SKU / GTIN</th>
                        <th>Category</th>
                        <th>Retail Price</th>
                        <th>Online Stock</th>
                        <th>Offline Stock</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                        <tr>
                            <td><input type="checkbox" class="form-check-input"></td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <img src="{{ asset($product['image']) }}" alt="{{ $product['name_en'] }}" style="width: 40px; height: 40px; border-radius: var(--radius-sm); object-fit: cover; background: var(--color-bg-subtle);" onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';">
                                    <div>
                                        <div class="font-bold text-sm">
                                            <a href="{{ route('admin.products.edit', $product['id']) }}" class="text-primary">
                                                {{ $product['name_en'] }}
                                            </a>
                                        </div>
                                        <div class="text-xs text-muted" dir="rtl">
                                            {{ $product['name_ar'] }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="font-mono text-xs">
                                <div>{{ $product['sku'] }}</div>
                                <div class="text-muted">{{ $product['barcode'] }}</div>
                            </td>
                            <td>
                                <span class="badge badge-neutral text-xs">{{ $product['category_en'] }}</span>
                            </td>
                            <td class="font-bold">${{ number_format($product['price'], 2) }}</td>
                            <td>
                                <span class="badge {{ $product['stock_online'] <= $product['low_stock_threshold'] ? 'badge-warning' : 'badge-success' }}">
                                    {{ $product['stock_online'] }} units
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $product['stock_offline'] <= $product['low_stock_threshold'] ? 'badge-danger' : 'badge-neutral' }}">
                                    {{ $product['stock_offline'] }} units
                                </span>
                            </td>
                            <td>
                                <x-status-badge :status="$product['status']" />
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a href="{{ route('admin.products.edit', $product['id']) }}" class="action-btn" title="Edit">
                                        ✏️
                                    </a>
                                    <a href="{{ route('admin.products.show', $product['id']) }}" class="action-btn" title="View Dossier">
                                        👁️
                                    </a>
                                    <button type="button" class="action-btn action-danger" onclick="openModal('deleteProductModal')" title="Delete">
                                        🗑️
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <x-pagination :currentPage="$currentPage" :totalPages="$totalPages" :totalItems="count($products)" />
    </div>

    <!-- Confirmation Modal for Deletion -->
    <x-confirmation-modal 
        id="deleteProductModal" 
        title="Delete Formulation" 
        message="Are you sure you wish to deprecate and delete this formulation? Active subscriptions and historical stock logs will require archiving." 
        confirmText="Confirm Delete" 
        confirmType="btn-danger" 
    />
</x-layouts.admin>
