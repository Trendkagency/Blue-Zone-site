@props([
    'product',
    'showBadge' => true,
])

@php
    $isAr = app()->getLocale() === 'ar';
    $name = $isAr ? ($product['name_ar'] ?? $product['name_en']) : $product['name_en'];
    $tagline = $isAr ? ($product['tagline_ar'] ?? $product['tagline_en']) : $product['tagline_en'];
    $category = $isAr ? ($product['category_ar'] ?? $product['category_en']) : $product['category_en'];
    $price = (float)($product['price'] ?? 0);
    $salePrice = isset($product['sale_price']) ? (float)$product['sale_price'] : null;
    $hasDiscount = !empty($salePrice) && $salePrice < $price && $salePrice > 0;
    $discPct = $hasDiscount ? (int) round((($price - $salePrice) / $price) * 100) : 0;
    $discLabel = $discPct > 0 ? ($isAr ? "خصم {$discPct}%" : "-{$discPct}% OFF") : ($isAr ? 'عرض خاص' : 'SALE');
@endphp

<div class="card product-card card-hover-lift">
    <div class="product-card-img-wrap" style="position: relative;">
        @if($hasDiscount)
            <div style="position: absolute; top: 10px; right: 10px; z-index: 5; pointer-events: none;">
                <span style="background: rgba(2, 19, 31, 0.92); color: #B8D98A; font-family: monospace; font-weight: 900; font-size: 0.72rem; border-radius: 9999px; padding: 0.25rem 0.65rem; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.2); display: inline-flex; align-items: center; gap: 0.35rem; backdrop-filter: blur(8px);">
                    <span style="width: 6px; height: 6px; border-radius: 9999px; background: #67B34A; display: inline-block;"></span>
                    <span>{{ $discLabel }}</span>
                </span>
            </div>
        @endif

        @if($showBadge && !empty($product['is_best_seller']))
            <span class="badge badge-accent product-card-badge">Best Seller</span>
        @elseif($showBadge && !empty($product['is_new']))
            <span class="badge badge-success product-card-badge">New Formula</span>
        @endif

        <a href="{{ route('customer.product.show', $product['slug']) }}"
            style="display: block; width: 100%; height: 100%;">
            <img src="{{ asset($product['image']) }}" alt="{{ $name }}" width="400" height="400" loading="lazy" decoding="async" class="product-card-img"
                onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';">
        </a>
    </div>

    <div class="product-card-content">
        <span class="product-category-tag">{{ $category }}</span>

        <h3 class="product-title">
            <a href="{{ route('customer.product.show', $product['slug']) }}"
                style="color: inherit; text-decoration: none;">
                {{ $name }}
            </a>
        </h3>

        <p class="text-sm text-muted"
            style="margin: 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
            {{ $tagline }}
        </p>

        <div class="product-price-row">
            <div>
                <span class="price-current">@currency($product['sale_price'] ?? $product['price'])</span>
                @if(isset($product['sale_price']) && $product['sale_price'] < $product['price'])
                    <span class="price-original">@currency($product['price'])</span>
                @endif
            </div>

            <a href="{{ route('customer.product.show', $product['slug']) }}" class="btn btn-outline btn-sm">
                {{ __('app.actions.view') }}
            </a>
        </div>
    </div>
</div>