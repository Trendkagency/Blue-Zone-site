@props([
    'product',
    'showBadge' => true,
])

@php
    $isAr = app()->getLocale() === 'ar';
    $name = $isAr ? ($product['name_ar'] ?? $product['name_en']) : $product['name_en'];
    $tagline = $isAr ? ($product['tagline_ar'] ?? $product['tagline_en']) : $product['tagline_en'];
    $category = $isAr ? ($product['category_ar'] ?? $product['category_en']) : $product['category_en'];
@endphp

<div class="card product-card card-hover-lift">
    <div class="product-card-img-wrap">
        @if($showBadge && !empty($product['is_best_seller']))
            <span class="badge badge-accent product-card-badge">Best Seller</span>
        @elseif($showBadge && !empty($product['is_new']))
            <span class="badge badge-success product-card-badge">New Formula</span>
        @endif

        <a href="{{ route('customer.product.show', $product['slug']) }}" style="display: block; width: 100%; height: 100%;">
            <img src="{{ asset($product['image']) }}" alt="{{ $name }}" class="product-card-img" onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';">
        </a>
    </div>

    <div class="product-card-content">
        <span class="product-category-tag">{{ $category }}</span>
        
        <h3 class="product-title">
            <a href="{{ route('customer.product.show', $product['slug']) }}" style="color: inherit; text-decoration: none;">
                {{ $name }}
            </a>
        </h3>

        <p class="text-sm text-muted" style="margin: 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
            {{ $tagline }}
        </p>

        <div class="product-price-row">
            <div>
                <span class="price-current">${{ number_format($product['sale_price'] ?? $product['price'], 2) }}</span>
                @if(isset($product['sale_price']) && $product['sale_price'] < $product['price'])
                    <span class="price-original">${{ number_format($product['price'], 2) }}</span>
                @endif
            </div>

            <a href="{{ route('customer.product.show', $product['slug']) }}" class="btn btn-outline btn-sm">
                {{ __('app.actions.view') }}
            </a>
        </div>
    </div>
</div>
