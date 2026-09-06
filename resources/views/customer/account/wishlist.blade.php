<x-layouts.customer :title="(app()->getLocale() === 'ar' ? 'المنتجات المحفوظة وقائمة الرغبات' : 'Saved Formulations & Wishlist') . ' — ' . __('app.brand_name')">
    <div class="container" style="padding-top: 3rem; margin-bottom: 5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 2rem;">
            <div>
                <h1 style="font-size: 2.25rem; font-weight: 800; margin: 0;">
                    {{ app()->getLocale() === 'ar' ? 'المنتجات المحفوظة وقائمة الرغبات' : 'Saved Formulations & Wishlist' }}
                </h1>
                <div class="text-xs text-muted" style="margin-top: 0.25rem;">
                    {{ app()->getLocale() === 'ar' ? 'التركيبات الحيوية المفضلة لسهولة إضافتها لسلة التسوق وتجديد البروتوكول.' : 'Your clinical favorites saved for easy re-ordering and routine protocol renewals.' }}
                </div>
            </div>

            <a href="{{ route('customer.shop') }}" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-compass mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'استكشاف جميع التركيبات' : 'Explore All Formulations' }}
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success" style="margin-bottom: 1.5rem;">
                <i class="fa-solid fa-circle-check mr-1.5 ml-1.5 text-success"></i> {{ session('success') }}
            </div>
        @endif

        <div class="account-layout">
            <!-- Navigation -->
            <aside class="account-sidebar-nav">
                <a href="{{ route('customer.account.dashboard') }}" class="account-nav-link">
                    <i class="fa-solid fa-chart-pie mr-1.5 ml-1.5"></i> {{ __('shop.account.dashboard') }}
                </a>
                <a href="{{ route('customer.account.orders') }}" class="account-nav-link">
                    <i class="fa-solid fa-box mr-1.5 ml-1.5"></i> {{ __('shop.account.orders') }}
                </a>
                <a href="{{ route('customer.account.invoices') }}" class="account-nav-link">
                    <i class="fa-solid fa-file-invoice-dollar mr-1.5 ml-1.5"></i> {{ __('shop.account.invoices') }}
                </a>
                <a href="{{ route('customer.account.addresses') }}" class="account-nav-link">
                    <i class="fa-solid fa-location-dot mr-1.5 ml-1.5"></i> {{ __('shop.account.addresses') }}
                </a>
                <a href="{{ route('customer.account.wishlist') }}" class="account-nav-link active">
                    <i class="fa-solid fa-heart mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'المنتجات المحفوظة' : 'Saved Formulations' }}
                </a>
                <a href="{{ route('customer.account.profile') }}" class="account-nav-link">
                    <i class="fa-solid fa-user mr-1.5 ml-1.5"></i> {{ __('shop.account.profile') }}
                </a>
                <a href="{{ route('customer.account.settings') }}" class="account-nav-link">
                    <i class="fa-solid fa-gear mr-1.5 ml-1.5"></i> {{ __('shop.account.settings') }}
                </a>

                <form action="{{ route('customer.auth.logout') }}" method="POST" style="margin-top: 0.5rem; border-top: 1px solid var(--color-border); padding-top: 0.5rem;">
                    @csrf
                    <button type="submit" class="account-nav-link" style="width: 100%; text-align: start; background: none; border: none; cursor: pointer; color: var(--color-danger);">
                        <i class="fa-solid fa-right-from-bracket mr-1.5 ml-1.5"></i> {{ __('app.nav.logout') }}
                    </button>
                </form>
            </aside>

            <!-- Wishlist Products Grid -->
            <div>
                @if($products->isNotEmpty())
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 1.5rem;">
                        @foreach($products as $product)
                            @php
                                $pName = app()->getLocale() === 'ar' ? ($product->name_ar ?? $product->name_en) : $product->name_en;
                                $pImg = $product->image ?? 'assets/products/blue-mind.jpg';
                            @endphp
                            <div class="card card-hover-lift" style="padding: 1.5rem; display: flex; flex-direction: column; justify-content: space-between;">
                                <div>
                                    <div style="position: relative; margin-bottom: 1rem; text-align: center;">
                                        <img src="{{ asset($pImg) }}" alt="{{ $pName }}" style="width: 120px; height: 120px; object-fit: cover; border-radius: var(--radius-md); margin: 0 auto; background: var(--color-bg-subtle);" onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';">
                                    </div>

                                    <h3 style="font-size: 1.05rem; font-weight: 800; margin-bottom: 0.25rem;">
                                        <a href="{{ route('customer.product.show', $product->slug) }}" class="text-primary">
                                            {{ $pName }}
                                        </a>
                                    </h3>
                                    <div class="text-xs text-muted font-mono" style="margin-bottom: 0.75rem;">
                                        SKU: {{ $product->sku }}
                                    </div>

                                    <div class="font-black text-lg text-primary" style="margin-bottom: 1rem;">
                                        ${{ number_format((float)$product->price, 2) }}
                                    </div>
                                </div>

                                <div style="display: flex; gap: 0.5rem; border-top: 1px solid var(--color-border); padding-top: 1rem;">
                                    <a href="{{ route('customer.product.show', $product->slug) }}" class="btn btn-primary btn-xs" style="flex: 1; text-align: center;">
                                        <i class="fa-solid fa-cart-plus mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'طلب التركيبة' : 'Order Now' }}
                                    </a>

                                    <form action="{{ route('customer.account.wishlist.toggle') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <button type="submit" class="btn btn-secondary btn-xs text-danger" title="{{ app()->getLocale() === 'ar' ? 'إزالة من المحفوظات' : 'Remove from saved' }}">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="card" style="padding: 4rem 2rem; text-align: center;">
                        <i class="fa-regular fa-heart fa-3x text-muted" style="margin-bottom: 1rem; opacity: 0.4;"></i>
                        <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 0.5rem;">
                            {{ app()->getLocale() === 'ar' ? 'قائمة الرغبات فارغة حالياً' : 'Your Wishlist is Empty' }}
                        </h3>
                        <p class="text-sm text-muted" style="max-width: 420px; margin: 0 auto 1.5rem auto;">
                            {{ app()->getLocale() === 'ar' ? 'احفظ التركيبات الحيوية المفضلة لديك من الكتالوج الطبي للوصول السريع إليها وإعادة طلبها.' : 'Save clinical bioceuticals from our catalog for rapid refilling and protocol tracking.' }}
                        </p>
                        <a href="{{ route('customer.shop') }}" class="btn btn-primary">
                            <i class="fa-solid fa-bag-shopping mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'تصفح الكتالوج الطبي' : 'Browse Formulations Catalog' }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.customer>
