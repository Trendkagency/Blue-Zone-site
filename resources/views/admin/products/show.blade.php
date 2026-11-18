<x-layouts.admin 
    :pageTitle="$product['name_en'] ?? 'Product Details'" 
    pageSubtitle="Clinical formulation dossier, inventory distribution, and assay parameters."
    :breadcrumbs="['Products' => route('admin.products.index'), ($product['name_en'] ?? 'Product') => route('admin.products.show', $product['id'])]"
>
    <x-slot name="actions">
        <a href="{{ route('admin.products.edit', $product['id']) }}" class="btn btn-primary">
            <i class="fa-solid fa-pen-to-square mr-1.5 ml-1.5"></i> {{ __('app.actions.edit') }}
        </a>
        <a href="{{ route('customer.product.show', $product['slug'] ?? $product['id']) }}" class="btn btn-outline" target="_blank">
            Customer View <i class="fa-solid fa-arrow-up-right-from-square mr-1 ml-1"></i>
        </a>
    </x-slot>

    <div style="display: grid; grid-template-columns: 1fr 340px; gap: 2rem;">
        <div>
            <!-- Overview Card -->
            <div class="card" style="padding: 2rem; margin-bottom: 2rem;">
                <div style="display: flex; gap: 1.5rem; align-items: flex-start; margin-bottom: 1.5rem;">
                    @php
                        $showImg = \App\Models\Product::normalizeUrl($product['image'] ?? null);
                    @endphp
                    <img src="{{ $showImg }}" alt="{{ $product['name_en'] ?? '' }}" style="width: 100px; height: 100px; border-radius: var(--radius-lg); object-fit: cover; background: var(--color-bg-subtle);" onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';">
                    <div>
                        <span class="product-category-tag">{{ $product['category_en'] ?? ($product['category_name_en'] ?? 'General') }}</span>
                        <h2 style="font-size: 1.75rem; font-weight: 800; margin: 0.25rem 0;">{{ $product['name_en'] ?? 'Product' }}</h2>
                        <div class="text-sm text-muted" dir="rtl">{{ $product['name_ar'] ?? '' }}</div>
                    </div>
                </div>

                <p style="color: var(--color-text-secondary); line-height: 1.8;">
                    {{ $product['description_en'] ?? ($product['short_description_en'] ?? '') }}
                </p>
            </div>

            <!-- Ingredients Card -->
            @php
                $ingredientsList = is_array($product['ingredients'] ?? null) ? $product['ingredients'] : [];
            @endphp
            @if(!empty($ingredientsList))
            <div class="card" style="padding: 2rem;">
                <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 1.5rem;">Clinical Actives</h3>
                <div class="ingredients-matrix">
                    @foreach($ingredientsList as $ing)
                        @if(is_array($ing))
                        <div class="ingredient-item">
                            <div class="ingredient-name">{{ $ing['name_en'] ?? ($ing['name_ar'] ?? 'Active Compound') }}</div>
                            <div class="ingredient-dose">{{ $ing['dose'] ?? '' }}</div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Right: Stock & Identifiers Card -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <div class="card" style="padding: 1.5rem;">
                <h4 style="font-size: 1rem; font-weight: 800; margin-bottom: 1rem;">Stock Status</h4>
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <div class="summary-row">
                        <span>Online Hub Stock:</span>
                        <span class="badge badge-success font-bold">{{ $product['stock_online'] ?? 0 }} units</span>
                    </div>
                    <div class="summary-row">
                        <span>Low Stock Alert at:</span>
                        <span class="text-muted">{{ $product['low_stock_threshold'] ?? 10 }} units</span>
                    </div>
                </div>

                <a href="{{ route('admin.inventory.transfers') }}" class="btn btn-secondary btn-sm" style="width: 100%; margin-top: 1rem;">
                    <i class="fa-solid fa-arrow-right-arrow-left mr-1.5 ml-1.5"></i> {{ __('admin.inventory.transfer_stock') ?? 'Transfer Stock' }}
                </a>
            </div>

            <div class="card" style="padding: 1.5rem;">
                <h4 style="font-size: 1rem; font-weight: 800; margin-bottom: 1rem;">Commercial Spec</h4>
                <div class="text-sm" style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <div><strong>SKU:</strong> {{ $product['sku'] ?? 'N/A' }}</div>
                    <div><strong>GTIN:</strong> {{ $product['barcode'] ?? 'N/A' }}</div>
                    <div><strong>Retail:</strong> @currency($product['price'] ?? 0)</div>
                    <div><strong>Status:</strong> <x-status-badge :status="$product['status'] ?? 'active'" /></div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
