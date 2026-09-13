<x-layouts.admin 
    :pageTitle="__('admin.pos.title')" 
    :pageSubtitle="app()->getLocale() == 'ar' ? 'واجهة كاشير المستودع السريعة مع التحقق المباشر من توفر المخزون وخصم الكميات فورياً.' : 'High-speed warehouse counter register interface with instant offline stock validation and real-time ledger deduction.'"
    :breadcrumbs="[__('admin.menu.offline_sales') => route('admin.offline-sales.index'), __('admin.pos.title') => route('admin.offline-sales.create')]"
>
    @php
        $isAr = app()->getLocale() === 'ar';
        $currencySymbol = \App\Services\CurrencyService::symbol();
    @endphp

    @if($errors->has('sale_error'))
        <div class="alert alert-danger" style="margin-bottom: 1.25rem; display: flex; align-items: center; justify-content: space-between; border-radius: var(--radius-md); animation: posFadeIn 0.3s ease;">
            <div>
                <i class="fa-solid fa-triangle-exclamation mr-2 ml-2 text-danger"></i> {{ $errors->first('sale_error') }}
            </div>
            <button type="button" class="btn btn-sm btn-ghost" onclick="this.parentElement.remove()">✕</button>
        </div>
    @endif

    <!-- POS Master Container -->
    <div id="posMasterContainer" style="display: flex; flex-direction: column; gap: 1.25rem;">

        <!-- 1. Top Register Header & Action Toolbar -->
        <div class="card" style="padding: 1rem 1.25rem; border-radius: var(--radius-lg); background: linear-gradient(135deg, rgba(255,255,255,0.98), rgba(248,250,252,0.98)); border: 1px solid var(--color-border); box-shadow: 0 4px 15px -3px rgba(0,0,0,0.04);">
            <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.85rem;">
                
                <!-- Cashier & Warehouse Info -->
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 42px; height: 42px; border-radius: 12px; background: linear-gradient(135deg, var(--color-primary), #0284C7); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1.15rem; font-weight: bold; box-shadow: 0 4px 10px rgba(10,17,40,0.15); flex-shrink: 0;">
                        <i class="fa-solid fa-cash-register"></i>
                    </div>
                    <div>
                        <div style="display: flex; align-items: center; gap: 0.45rem; flex-wrap: wrap;">
                            <span class="font-black text-sm" style="color: var(--color-text-main); font-weight: 800;">
                                {{ auth()->user()?->name ?? ($isAr ? 'أخصائي المستودع' : 'Warehouse Specialist') }}
                            </span>
                            <span class="badge badge-success" style="font-size: 0.68rem; padding: 0.2rem 0.45rem;">
                                <span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: #10B981; margin-inline-end: 4px; animation: posPulseGlow 1.5s infinite;"></span>
                                {{ $isAr ? 'متصل بنقطة البيع' : 'POS Online' }}
                            </span>
                        </div>
                        <div class="text-xs text-muted" style="display: flex; align-items: center; gap: 0.4rem; margin-top: 0.15rem; font-size: 0.75rem;">
                            <span><i class="fa-solid fa-store mr-1 ml-1 text-primary"></i> {{ __('admin.pos.flagship_store') }}</span>
                            <span>•</span>
                            <span id="posDigitalClock" style="font-family: monospace; font-weight: 600; color: var(--color-text-main);">--:--:--</span>
                        </div>
                    </div>
                </div>

                <!-- Action Toolbar -->
                <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                    
                    <!-- Sound FX Toggle Button -->
                    <button type="button" id="posAudioToggleBtn" class="btn btn-sm btn-ghost" onclick="togglePOSAudio()" title="{{ __('admin.pos.sound_fx') }}" style="border: 1px solid var(--color-border); border-radius: var(--radius-md); padding: 0.4rem 0.75rem;">
                        <i class="fa-solid fa-volume-high text-primary" id="posAudioIcon"></i>
                        <span class="text-xs font-semibold" id="posAudioLabel" style="margin-inline-start: 4px;">{{ $isAr ? 'الصوت: مفعّل' : 'Sound: ON' }}</span>
                    </button>

                    <!-- Hotkeys Guide Button -->
                    <button type="button" class="btn btn-sm btn-ghost" onclick="openHotkeysModal()" style="border: 1px solid var(--color-border); border-radius: var(--radius-md); padding: 0.4rem 0.75rem;">
                        <i class="fa-solid fa-keyboard text-muted"></i>
                        <span class="text-xs font-semibold" style="margin-inline-start: 4px;">{{ __('admin.pos.hotkeys') }}</span>
                    </button>

                    <!-- Interactive Tour Button -->
                    <button type="button" id="tourTriggerBtn" class="btn btn-sm btn-outline-primary" onclick="startPOSTour(true)" style="border-radius: var(--radius-md); font-weight: 700; padding: 0.4rem 0.75rem;">
                        <i class="fa-solid fa-circle-question mr-1 ml-1 text-primary"></i>
                        <span>{{ __('admin.pos.start_tour') }}</span>
                    </button>

                    <!-- Barcode Scan Simulation -->
                    <button type="button" class="btn btn-sm btn-primary" onclick="simulateBarcodeScan()" style="border-radius: var(--radius-md); font-weight: 700; padding: 0.4rem 0.75rem;">
                        <i class="fa-solid fa-barcode mr-1 ml-1"></i>
                        <span>{{ __('admin.pos.scan_sim') }}</span>
                    </button>

                    <!-- Fullscreen Toggle -->
                    <button type="button" class="btn btn-sm btn-ghost" onclick="togglePOSFullscreen()" title="Fullscreen" style="border: 1px solid var(--color-border); border-radius: var(--radius-md); padding: 0.4rem 0.6rem;">
                        <i class="fa-solid fa-expand" id="fullscreenIcon"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- 2. Dismissible First-Time User Banner -->
        <div id="firstTimeUserBanner" class="card" style="padding: 0.85rem 1.25rem; border-radius: var(--radius-lg); background: linear-gradient(135deg, rgba(14,165,233,0.08), rgba(99,102,241,0.08)); border: 1.5px dashed rgba(14,165,233,0.35); display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.75rem;">
            <div style="display: flex; align-items: center; gap: 0.85rem;">
                <div style="width: 36px; height: 36px; border-radius: 50%; background: #0EA5E9; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; box-shadow: 0 4px 10px rgba(14,165,233,0.25);">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                </div>
                <div>
                    <h4 style="margin: 0; font-size: 0.9rem; font-weight: 800; color: var(--color-primary);">{{ __('admin.pos.tour_title') }}</h4>
                    <p style="margin: 0.15rem 0 0 0; font-size: 0.75rem; color: var(--color-text-muted);">{{ __('admin.pos.tour_desc') }}</p>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem; flex-shrink: 0;">
                <button type="button" class="btn btn-sm btn-primary" onclick="startPOSTour(true)" style="border-radius: var(--radius-md); font-weight: 700;">
                    <i class="fa-solid fa-play mr-1 ml-1"></i> {{ __('admin.pos.start_tour') }}
                </button>
                <button type="button" class="btn btn-sm btn-ghost" onclick="dismissFirstTimeBanner()" title="Dismiss" style="color: var(--color-text-muted);">✕</button>
            </div>
        </div>

        <!-- 3. Held Cart Notification Banner (Appears if a draft cart exists) -->
        <div id="heldCartNotification" class="card" style="display: none; padding: 0.75rem 1.25rem; border-radius: var(--radius-lg); background: rgba(245,158,11,0.08); border: 1.5px solid rgba(245,158,11,0.4); align-items: center; justify-content: space-between; gap: 0.75rem; animation: posFadeIn 0.3s ease;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <i class="fa-solid fa-circle-pause text-warning" style="font-size: 1.2rem;"></i>
                <div>
                    <strong class="text-xs text-warning font-bold" id="heldCartText">{{ $isAr ? 'توجد سلة مشتريات معلقة مسبقاً' : 'A held cart is saved from previous session' }}</strong>
                </div>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button type="button" class="btn btn-xs btn-warning font-bold" onclick="recallHeldCart()">
                    <i class="fa-solid fa-clock-rotate-left mr-1 ml-1"></i> {{ __('admin.pos.recall_draft') }}
                </button>
                <button type="button" class="btn btn-xs btn-ghost text-muted" onclick="discardHeldCart()">✕</button>
            </div>
        </div>

        <!-- 4. Main Responsive POS Layout -->
        <div class="pos-main-layout" id="posMainGrid">
            
            <!-- Left: Product Catalog, Category Filters & Barcode Search -->
            <div style="display: flex; flex-direction: column; gap: 1rem; min-width: 0;">
                
                <!-- Search & Filter Card -->
                <div class="card" id="tour-step-search" style="padding: 1rem 1.25rem; border-radius: var(--radius-lg); background: #fff; border: 1px solid var(--color-border);">
                    <div style="display: flex; gap: 0.65rem; align-items: center;">
                        <div class="search-wrapper" style="flex: 1; position: relative;">
                            <svg class="search-icon" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="position: absolute; inset-inline-start: 12px; top: 50%; transform: translateY(-50%); color: var(--color-text-muted); pointer-events: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input type="text" id="posSearch" class="form-control search-input" placeholder="{{ __('admin.pos.scan_sku') }}" oninput="filterPOS()" style="padding-inline-start: 2.35rem; padding-inline-end: 2.35rem; height: 42px; font-size: 0.88rem; border-radius: var(--radius-md); border: 1.5px solid var(--color-border); width: 100%;">
                            <button type="button" id="clearSearchBtn" onclick="clearPOSSearch()" style="position: absolute; inset-inline-end: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--color-text-muted); cursor: pointer; display: none; font-size: 0.85rem;">✕</button>
                        </div>
                        <span class="badge" style="padding: 0.45rem 0.65rem; background: var(--color-bg-subtle); border: 1px solid var(--color-border); font-family: monospace; font-size: 0.75rem; font-weight: bold; border-radius: var(--radius-sm); color: var(--color-text-muted); flex-shrink: 0;">
                            F2
                        </span>
                    </div>

                    <!-- Category Filter Tabs -->
                    <div id="tour-step-categories" style="display: flex; gap: 0.45rem; overflow-x: auto; padding-top: 0.85rem; margin-top: 0.75rem; border-top: 1px solid var(--color-border); scrollbar-width: none;" class="hide-scrollbar">
                        <button type="button" class="btn btn-sm pos-cat-btn active" data-cat="all" onclick="filterByCategory('all', this)" style="border-radius: 20px; padding: 0.35rem 0.9rem; font-size: 0.75rem; font-weight: 700; white-space: nowrap; transition: all 0.2s ease;">
                            {{ __('admin.pos.all_categories') }} <span class="badge badge-subtle" style="margin-inline-start: 4px; font-size: 0.68rem;">{{ count($products) }}</span>
                        </button>
                        @foreach($categories as $cat)
                            @php
                                $catName = $isAr ? ($cat->name_ar ?? $cat->name_en) : $cat->name_en;
                                $catCount = $products->where('category_id', $cat->id)->count();
                            @endphp
                            @if($catCount > 0)
                                <button type="button" class="btn btn-sm pos-cat-btn btn-ghost" data-cat="{{ $cat->id }}" onclick="filterByCategory('{{ $cat->id }}', this)" style="border-radius: 20px; padding: 0.35rem 0.85rem; font-size: 0.75rem; font-weight: 700; white-space: nowrap; border: 1px solid var(--color-border); transition: all 0.2s ease;">
                                    {{ $catName }} <span class="badge badge-subtle" style="margin-inline-start: 4px; font-size: 0.68rem;">{{ $catCount }}</span>
                                </button>
                            @endif
                        @endforeach
                    </div>
                </div>

                <!-- Product Tap Grid Container -->
                <div id="tour-step-products">
                    <div id="productGrid" class="pos-product-grid">
                        @foreach($products as $p)
                            @php
                                $pId = $p->id;
                                $pNameEn = $p->name_en ?? $p->sku;
                                $pNameAr = $p->name_ar ?? $pNameEn;
                                $pDisplay = $isAr ? $pNameAr : $pNameEn;
                                $pPrice = (float) $p->price;
                                $pStock = (int) $p->stock_offline;
                                $pImg = $p->image ?? 'assets/products/blue-mind.jpg';
                                $pSku = $p->sku ?? 'BZ-SKU';
                                $pBarcode = $p->barcode ?? '';
                                $pCatId = $p->category_id ?? '0';
                                $pVariant = 'Standard Pack (60 Caps)';
                                $isOutOfStock = $pStock <= 0;
                            @endphp
                            <div class="card pos-card {{ $isOutOfStock ? 'pos-card-oos' : '' }}" 
                                 id="card-prod-{{ $pId }}"
                                 data-id="{{ $pId }}"
                                 data-name="{{ strtolower($pNameEn . ' ' . $pNameAr) }}" 
                                 data-sku="{{ strtolower($pSku) }}"
                                 data-barcode="{{ strtolower($pBarcode) }}"
                                 data-category="{{ $pCatId }}"
                                 data-price="{{ $pPrice }}"
                                 data-stock="{{ $pStock }}"
                                 data-title="{{ $pDisplay }}"
                                 data-img="{{ asset($pImg) }}"
                                 data-variant="{{ $pVariant }}"
                                 onclick="handleProductClick({{ $pId }}, '{{ addslashes($pDisplay) }}', '{{ addslashes($pVariant) }}', {{ $pPrice }}, {{ $pStock }}, '{{ asset($pImg) }}', '{{ $pSku }}')">
                                
                                <!-- Stock Badge -->
                                <div style="position: absolute; top: 8px; inset-inline-end: 8px; z-index: 2;">
                                    @if($pStock > 10)
                                        <span class="badge badge-success" style="font-size: 0.62rem; padding: 0.15rem 0.4rem;">{{ $pStock }} {{ $isAr ? 'وحدة' : 'in stock' }}</span>
                                    @elseif($pStock > 0)
                                        <span class="badge badge-warning" style="font-size: 0.62rem; padding: 0.15rem 0.4rem;">{{ $pStock }} {{ $isAr ? 'متبقي' : 'low' }}</span>
                                    @else
                                        <span class="badge badge-danger" style="font-size: 0.62rem; padding: 0.15rem 0.4rem;">{{ $isAr ? 'نفد' : 'Out of stock' }}</span>
                                    @endif
                                </div>

                                <!-- In-Cart Active Counter Badge -->
                                <div id="card-in-cart-{{ $pId }}" class="pos-in-cart-indicator" style="display: none;">
                                    <i class="fa-solid fa-check mr-1 ml-1"></i> <span class="in-cart-qty">0</span> {{ $isAr ? 'في السلة' : 'in cart' }}
                                </div>

                                <div class="pos-card-body">
                                    <div class="pos-img-wrapper">
                                        <img src="{{ asset($pImg) }}" alt="{{ $pDisplay }}" class="pos-prod-img" onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';">
                                    </div>
                                    <div class="font-bold text-xs pos-prod-title">
                                        {{ $pDisplay }}
                                    </div>
                                    <div class="text-xs text-muted pos-prod-sku">
                                        {{ $pSku }}
                                    </div>
                                </div>

                                <div class="pos-card-footer">
                                    <div class="font-black text-sm text-primary pos-prod-price">
                                        @currency($pPrice)
                                    </div>
                                    <button type="button" class="btn btn-sm btn-ghost pos-add-quick-btn" title="{{ $isAr ? 'إضافة للسلة' : 'Add to Cart' }}">
                                        <i class="fa-solid fa-plus text-primary"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div id="noProductsFound" style="display: none; padding: 2.5rem 1rem; text-align: center;" class="card">
                        <i class="fa-solid fa-magnifying-glass" style="font-size: 2.2rem; color: var(--color-text-muted); opacity: 0.5; margin-bottom: 0.75rem;"></i>
                        <h4 style="margin: 0; font-weight: 700; font-size: 0.95rem;">{{ $isAr ? 'لم يتم العثور على أي تركيبة مطابقة' : 'No formulations matching search criteria' }}</h4>
                        <p class="text-xs text-muted" style="margin-top: 0.35rem;">{{ $isAr ? 'جرب البحث برمز SKU آخر أو تفريغ حقل البحث.' : 'Try scanning a different barcode or clearing the filter.' }}</p>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="clearPOSSearch()" style="margin: 0.75rem auto 0 auto;">{{ $isAr ? 'تفريغ البحث' : 'Clear Search' }}</button>
                    </div>
                </div>
            </div>

            <!-- Right: POS Register Cart Panel -->
            <div class="card pos-cart-panel" id="tour-step-cart">
                
                <!-- Cart Title & Header Action -->
                <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--color-border); padding-bottom: 0.75rem;">
                    <div>
                        <h3 style="font-size: 1.1rem; font-weight: 800; margin: 0; display: flex; align-items: center; gap: 0.45rem;">
                            <i class="fa-solid fa-cart-shopping text-primary"></i>
                            <span>{{ __('admin.pos.cart_summary') }}</span>
                        </h3>
                        <div class="text-xs text-muted" style="margin-top: 0.15rem; font-size: 0.75rem;">
                            <span id="cartItemCount" class="font-bold text-primary">0</span> {{ __('admin.pos.items_count') }} • <span id="cartUnitCount" class="font-bold">0</span> {{ __('admin.pos.units_count') }}
                        </div>
                    </div>
                    <div style="display: flex; gap: 0.35rem;">
                        <button type="button" class="btn btn-sm btn-ghost" onclick="holdCurrentCart()" title="{{ __('admin.pos.draft_hold') }}" style="border-radius: var(--radius-sm); border: 1px solid var(--color-border); padding: 0.3rem 0.55rem;">
                            <i class="fa-solid fa-pause text-warning"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-ghost" onclick="clearEntireCart()" title="{{ __('admin.pos.clear_register') }}" style="border-radius: var(--radius-sm); border: 1px solid var(--color-border); padding: 0.3rem 0.55rem; color: #EF4444;">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </div>
                </div>

                <!-- Active Cart Items List Container -->
                <div id="cartItemsContainer" class="custom-scroll" style="max-height: 240px; overflow-y: auto; display: flex; flex-direction: column; gap: 0.5rem; padding-inline-end: 2px;">
                    <!-- Dynamic cart items rendered via JS -->
                </div>

                <!-- Empty Cart State -->
                <div id="emptyCartMessage" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1.75rem 1rem; text-align: center; background: var(--color-bg-subtle); border-radius: var(--radius-md); border: 1.5px dashed var(--color-border);">
                    <div style="width: 44px; height: 44px; border-radius: 50%; background: rgba(10,17,40,0.05); display: flex; align-items: center; justify-content: center; margin-bottom: 0.6rem; color: var(--color-text-muted); font-size: 1.15rem;">
                        <i class="fa-solid fa-basket-shopping"></i>
                    </div>
                    <div class="font-bold text-xs" style="color: var(--color-text-main); margin-bottom: 0.2rem;">{{ __('admin.pos.empty_cart') }}</div>
                    <div class="text-xs text-muted" style="font-size: 0.72rem;">{{ $isAr ? 'اضغط على أي تركيبة من القائمة لإضافتها فوراً' : 'Tap any formulation card to add items to this order.' }}</div>
                </div>

                <!-- Master Checkout Form -->
                <form action="{{ route('admin.offline-sales.store') }}" method="POST" id="posForm" onsubmit="handlePOSSubmit(event)">
                    @csrf
                    <!-- Hidden JSON data for multi-item cart -->
                    <input type="hidden" name="cart_items" id="posCartItemsJson" value="[]">
                    
                    <!-- Fallback fields for legacy backend test support -->
                    <input type="hidden" name="product_id" id="fallbackProductId" value="{{ $products[0]->id ?? 1 }}">
                    <input type="hidden" name="quantity" id="fallbackQuantity" value="1">
                    <input type="hidden" name="unit_price" id="fallbackUnitPrice" value="{{ $products[0]->price ?? 68 }}">
                    <input type="hidden" name="variant" id="fallbackVariant" value="Standard Pack (60 Caps)">

                    <!-- Customer Lookup & VIP Selection -->
                    <div id="tour-step-customer" style="margin-bottom: 0.75rem;">
                        <label class="text-xs font-bold text-muted" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.25rem;">
                            <span>{{ __('admin.pos.select_customer') }}</span>
                            <span class="badge badge-accent" style="font-size: 0.62rem;">VIP Ready</span>
                        </label>
                        <select name="customer_name" id="customerSelect" class="form-select text-xs" style="width: 100%; height: 36px; border-radius: var(--radius-md);" onchange="toggleCustomCustomer(this.value)">
                            <option value="Walk-In Warehouse VIP">{{ __('admin.pos.walk_in') }}</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->name }}" data-phone="{{ $c->phone }}" data-email="{{ $c->email }}">{{ $c->name }} ({{ $c->tier ?? 'Member' }})</option>
                            @endforeach
                            <option value="__custom__">{{ $isAr ? '➕ إدخال عميل جديد يدوي...' : '➕ Enter new customer details...' }}</option>
                        </select>

                        <div id="customCustomerFields" style="display: none; margin-top: 0.45rem; gap: 0.45rem; flex-direction: column;">
                            <input type="text" name="custom_customer_name" id="customName" class="form-control text-xs" placeholder="{{ $isAr ? 'اسم العميل الكامل' : 'Customer Full Name' }}">
                            <input type="text" name="customer_phone" id="customPhone" class="form-control text-xs" placeholder="{{ $isAr ? 'رقم الجوال (+966)' : 'Phone Number (+966)' }}">
                            <input type="email" name="customer_email" id="customEmail" class="form-control text-xs" placeholder="{{ $isAr ? 'البريد الإلكتروني (اختياري)' : 'Email Address (Optional)' }}">
                        </div>
                    </div>

                    <!-- Promotional Discount Selector with Quick Percentages -->
                    <div id="tour-step-discount" style="margin-bottom: 0.75rem; background: var(--color-bg-subtle); padding: 0.65rem; border-radius: var(--radius-md); border: 1px solid var(--color-border);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                            <label class="text-xs font-bold text-muted">{{ __('admin.pos.discount') }} ({{ $currencySymbol }})</label>
                            <span class="badge" style="font-size: 0.62rem; font-family: monospace;">F4</span>
                        </div>
                        <div style="display: flex; gap: 0.35rem; margin-bottom: 0.35rem; flex-wrap: wrap;">
                            <button type="button" class="btn btn-xs disc-pill-btn active" onclick="applyQuickDiscountPct(0, this)" style="border-radius: 12px; font-weight: 700; padding: 0.15rem 0.55rem;">0%</button>
                            <button type="button" class="btn btn-xs disc-pill-btn btn-ghost" onclick="applyQuickDiscountPct(5, this)" style="border-radius: 12px; font-weight: 700; border: 1px solid var(--color-border); padding: 0.15rem 0.55rem;">5%</button>
                            <button type="button" class="btn btn-xs disc-pill-btn btn-ghost" onclick="applyQuickDiscountPct(10, this)" style="border-radius: 12px; font-weight: 700; border: 1px solid var(--color-border); padding: 0.15rem 0.55rem;">10%</button>
                            <button type="button" class="btn btn-xs disc-pill-btn btn-ghost" onclick="applyQuickDiscountPct(15, this)" style="border-radius: 12px; font-weight: 700; border: 1px solid var(--color-border); padding: 0.15rem 0.55rem;">15%</button>
                            <button type="button" class="btn btn-xs disc-pill-btn btn-ghost" onclick="applyQuickDiscountPct(20, this)" style="border-radius: 12px; font-weight: 700; border: 1px solid var(--color-border); color: #0284C7; padding: 0.15rem 0.55rem;">VIP 20%</button>
                        </div>
                        <input type="number" step="0.01" min="0" name="discount" id="posDiscount" value="0.00" class="form-control text-xs" oninput="recalcPOSCart()" placeholder="0.00" style="font-weight: 700; height: 32px;">
                    </div>

                    <!-- Tender & Payment Selection -->
                    <div id="tour-step-tender" style="margin-bottom: 0.85rem;">
                        <label class="text-xs font-bold text-muted" style="display: block; margin-bottom: 0.35rem;">{{ __('admin.pos.payment_method') }}</label>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.4rem;" id="paymentTenderGrid">
                            <label class="pos-tender-label active" onclick="selectTender('Mada / Debit POS Terminal', this)">
                                <input type="radio" name="payment_method" value="Mada / Debit POS Terminal" checked style="display: none;">
                                <i class="fa-solid fa-credit-card"></i>
                                <span>{{ $isAr ? 'مدى (POS)' : 'Mada Debit' }}</span>
                            </label>
                            <label class="pos-tender-label" onclick="selectTender('Credit Card (Visa / Mastercard / Amex)', this)">
                                <input type="radio" name="payment_method" value="Credit Card (Visa / Mastercard / Amex)" style="display: none;">
                                <i class="fa-brands fa-cc-visa"></i>
                                <span>{{ $isAr ? 'بطاقة ائتمان' : 'Visa / Master' }}</span>
                            </label>
                            <label class="pos-tender-label" onclick="selectTender('Cash Tender', this)">
                                <input type="radio" name="payment_method" value="Cash Tender" style="display: none;">
                                <i class="fa-solid fa-money-bill-wave"></i>
                                <span>{{ $isAr ? 'سداد نقدي' : 'Cash Tender' }}</span>
                            </label>
                            <label class="pos-tender-label" onclick="selectTender('Apple Pay / Contactless', this)">
                                <input type="radio" name="payment_method" value="Apple Pay / Contactless" style="display: none;">
                                <i class="fa-brands fa-apple"></i>
                                <span>{{ $isAr ? 'أبل باي' : 'Apple Pay' }}</span>
                            </label>
                        </div>

                        <!-- Cash Tender Quick Calculator -->
                        <div id="cashCalculatorBox" style="display: none; margin-top: 0.65rem; padding: 0.75rem; background: rgba(16,185,129,0.06); border: 1px solid rgba(16,185,129,0.3); border-radius: var(--radius-md); animation: posFadeIn 0.2s ease;">
                            <div class="text-xs font-bold text-success" style="margin-bottom: 0.35rem; display: flex; justify-content: space-between;">
                                <span>{{ __('admin.pos.quick_tender') }}</span>
                                <span id="exactTenderBadge" class="badge badge-success" style="cursor: pointer;" onclick="setExactCashTender()">{{ $isAr ? 'المبلغ بالضبط' : 'Exact Total' }}</span>
                            </div>
                            <div style="display: flex; gap: 0.3rem; margin-bottom: 0.45rem; flex-wrap: wrap;">
                                <button type="button" class="btn btn-xs btn-outline-success" onclick="addCashAmount(50)">+50</button>
                                <button type="button" class="btn btn-xs btn-outline-success" onclick="addCashAmount(100)">+100</button>
                                <button type="button" class="btn btn-xs btn-outline-success" onclick="addCashAmount(200)">+200</button>
                                <button type="button" class="btn btn-xs btn-outline-success" onclick="addCashAmount(500)">+500</button>
                                <button type="button" class="btn btn-xs btn-ghost" onclick="resetCashAmount()" style="color: #EF4444; font-size: 0.7rem;">✕ {{ $isAr ? 'تصفير' : 'Clear' }}</button>
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.45rem; align-items: center;">
                                <div>
                                    <label class="text-xs text-muted" style="font-size: 0.7rem;">{{ __('admin.pos.cash_received') }}:</label>
                                    <input type="number" step="0.01" min="0" name="amount_tendered" id="cashReceivedInput" class="form-control text-xs font-bold" value="0.00" oninput="calculateChangeDue()" style="height: 32px;">
                                </div>
                                <div style="text-align: inset-inline-end;">
                                    <label class="text-xs text-muted" style="font-size: 0.7rem;">{{ __('admin.pos.change_due') }}:</label>
                                    <div class="font-black text-sm" id="changeDueDisplay" style="color: #10B981; margin-top: 0.15rem;">@currency(0.00)</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Totals Breakdown -->
                    <div style="display: flex; flex-direction: column; gap: 0.35rem; border-top: 1px solid var(--color-border); padding-top: 0.75rem; margin-bottom: 0.85rem;">
                        <div class="summary-row" style="display: flex; justify-content: space-between; font-size: 0.82rem;">
                            <span class="text-muted">{{ __('admin.pos.subtotal') }}:</span>
                            <span class="font-bold" id="posSubtotal">@currency(0.00)</span>
                        </div>
                        <div class="summary-row" style="display: flex; justify-content: space-between; font-size: 0.82rem; color: #10B981;">
                            <span>{{ $isAr ? 'قيمة الخصم:' : 'Discount Amount:' }}</span>
                            <span class="font-bold" id="posDiscountDisplay">-@currency(0.00)</span>
                        </div>
                        <div class="summary-row" style="display: flex; justify-content: space-between; font-size: 0.82rem;">
                            <span class="text-muted">{{ __('admin.pos.tax') }} (15% VAT):</span>
                            <span id="posTax" class="font-semibold">@currency(0.00)</span>
                        </div>
                        <div class="summary-row total" style="display: flex; justify-content: space-between; border-top: 1.5px dashed var(--color-border); padding-top: 0.5rem; margin-top: 0.15rem; font-size: 1.2rem;">
                            <span class="font-black">{{ __('admin.pos.total_payable') }}:</span>
                            <span class="text-primary font-black" id="posGrandTotal" style="font-weight: 900;">@currency(0.00)</span>
                        </div>
                    </div>

                    <!-- Submit & Preview Buttons -->
                    <div style="display: flex; flex-direction: column; gap: 0.45rem;" id="tour-step-checkout">
                        <button type="submit" id="posSubmitBtn" class="btn btn-primary btn-lg" style="width: 100%; height: 48px; font-size: 0.95rem; font-weight: 800; border-radius: var(--radius-md); box-shadow: 0 4px 15px rgba(10,17,40,0.15); display: flex; align-items: center; justify-content: center; gap: 0.5rem;" disabled>
                            <i class="fa-solid fa-receipt"></i>
                            <span>{{ __('admin.pos.complete_sale') }}</span>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="openThermalReceiptModal(false)" style="border-radius: var(--radius-md); font-weight: 700; height: 36px;">
                            <i class="fa-solid fa-eye mr-1 ml-1"></i> {{ __('admin.pos.preview_receipt') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 5. Mobile Floating Cart Action Bar (< 992px) -->
    <div id="posMobileCartBar" class="pos-mobile-cart-bar" onclick="scrollToCartPanel()">
        <div style="display: flex; align-items: center; gap: 0.6rem;">
            <div style="width: 32px; height: 32px; border-radius: 50%; background: #fff; color: var(--color-primary); display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.85rem;">
                <span id="mobileCartItemCount">0</span>
            </div>
            <span class="font-bold text-xs text-white">{{ $isAr ? 'سلة المشتريات' : 'View Register Cart' }}</span>
        </div>
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <span class="font-black text-sm text-white" id="mobileCartGrandTotal">@currency(0.00)</span>
            <i class="fa-solid fa-arrow-down text-white text-xs"></i>
        </div>
    </div>

    <!-- 6. Interactive Guided Tour Spotlight Overlay -->
    <div id="posTourOverlay" class="pos-tour-overlay" style="display: none;">
        <div id="posTourHighlightBox" class="pos-tour-highlight-box"></div>
        <div id="posTourCard" class="pos-tour-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                <span class="badge badge-accent" id="posTourStepBadge">Step 1 of 5</span>
                <button type="button" class="btn btn-xs btn-ghost" onclick="endPOSTour()" style="font-size: 0.85rem;">✕</button>
            </div>
            <h4 id="posTourTitle" style="margin: 0 0 0.35rem 0; font-size: 0.95rem; font-weight: 800; color: var(--color-primary);">Tour Title</h4>
            <p id="posTourContent" class="text-xs text-muted" style="margin: 0 0 0.85rem 0; line-height: 1.5; font-size: 0.78rem;">Tour content explanation...</p>
            <div style="display: flex; justify-content: space-between; align-items: center; gap: 0.5rem;">
                <button type="button" id="posTourPrevBtn" class="btn btn-xs btn-ghost" onclick="prevPOSTourStep()" style="font-weight: 700;">
                    {{ $isAr ? '➔ السابق' : '← Previous' }}
                </button>
                <div style="display: flex; gap: 0.35rem;">
                    <button type="button" class="btn btn-xs btn-outline-primary" onclick="endPOSTour()">{{ $isAr ? 'تخطي' : 'Skip' }}</button>
                    <button type="button" id="posTourNextBtn" class="btn btn-xs btn-primary" onclick="nextPOSTourStep()" style="font-weight: 700;">
                        {{ $isAr ? 'التالي ⬅' : 'Next →' }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 7. Keyboard Hotkeys Modal -->
    <div id="hotkeysModal" class="pos-modal-backdrop" style="display: none;">
        <div class="pos-modal-card card" style="max-width: 480px; width: 90%; padding: 1.5rem; border-radius: var(--radius-lg); animation: posSlideDown 0.25s ease;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="margin: 0; font-size: 1.1rem; font-weight: 800; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fa-solid fa-keyboard text-primary"></i>
                    <span>{{ __('admin.pos.hotkeys') }}</span>
                </h3>
                <button type="button" class="btn btn-sm btn-ghost" onclick="closeHotkeysModal()">✕</button>
            </div>
            <div style="display: flex; flex-direction: column; gap: 0.65rem;">
                <div class="hotkey-row">
                    <span class="hotkey-badge">F2</span>
                    <span class="text-xs font-semibold">{{ $isAr ? 'التركيز على حقل البحث والمسح' : 'Focus Search & Barcode Scan' }}</span>
                </div>
                <div class="hotkey-row">
                    <span class="hotkey-badge">F4</span>
                    <span class="text-xs font-semibold">{{ $isAr ? 'الانتقال إلى حقل الخصم الترويجي' : 'Jump to Promo Discount field' }}</span>
                </div>
                <div class="hotkey-row">
                    <span class="hotkey-badge">F8</span>
                    <span class="text-xs font-semibold">{{ $isAr ? 'تحديد السداد النقدي وحاسبة الفكة' : 'Select Cash Tender & Change Calc' }}</span>
                </div>
                <div class="hotkey-row">
                    <span class="hotkey-badge">Ctrl + Enter</span>
                    <span class="text-xs font-semibold">{{ $isAr ? 'إتمام البيع وطباعة الإيصال فوراً' : 'Complete Sale & Print Receipt' }}</span>
                </div>
                <div class="hotkey-row">
                    <span class="hotkey-badge">ESC</span>
                    <span class="text-xs font-semibold">{{ $isAr ? 'إغلاق النوافذ المنبثقة أو تفريغ البحث' : 'Close Modals / Clear Search' }}</span>
                </div>
            </div>
            <div style="margin-top: 1.25rem; text-align: center;">
                <button type="button" class="btn btn-sm btn-primary" onclick="closeHotkeysModal()" style="width: 100%;">{{ $isAr ? 'فهمت، متابعة البيع' : 'Got it, Back to Register' }}</button>
            </div>
        </div>
    </div>

    <!-- 8. Thermal Receipt Modal Live Preview -->
    <div id="receiptModal" class="pos-modal-backdrop" style="display: none;">
        <div class="pos-modal-card card" style="max-width: 420px; width: 92%; padding: 1.5rem; border-radius: var(--radius-lg); animation: posSlideDown 0.25s ease;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.85rem; border-bottom: 1px solid var(--color-border); padding-bottom: 0.65rem;">
                <h4 style="margin: 0; font-weight: 800; font-size: 0.95rem;">
                    <i class="fa-solid fa-receipt text-primary mr-1 ml-1"></i> {{ __('admin.pos.preview_receipt') }}
                </h4>
                <button type="button" class="btn btn-sm btn-ghost" onclick="closeReceiptModal()">✕</button>
            </div>

            <!-- Authentic Thermal Slip Paper Container -->
            <div id="thermalReceiptSlip" style="background: #FFFDF9; border: 1px dashed #D1D5DB; padding: 1.15rem; font-family: 'Courier New', Courier, monospace; color: #111827; border-radius: 4px; box-shadow: inset 0 0 10px rgba(0,0,0,0.02); direction: {{ $isAr ? 'rtl' : 'ltr' }};">
                <div style="text-align: center; border-bottom: 1px dashed #9CA3AF; padding-bottom: 0.65rem; margin-bottom: 0.65rem;">
                    <div style="font-weight: 900; font-size: 1.1rem; letter-spacing: 1px;">BLUE ZONE</div>
                    <div style="font-size: 0.7rem; color: #4B5563;">{{ $isAr ? 'عيادة ومستودع المعالجة الحيوية وطول العمر' : 'ADVANCED LONGEVITY CLINICAL WAREHOUSE' }}</div>
                    <div style="font-size: 0.7rem; color: #4B5563;">{{ $isAr ? 'معرض الرياض الرئيسي • الرقم الضريبي: 310928374800003' : 'Riyadh Flagship Hub • VAT: 310928374800003' }}</div>
                    <div style="font-size: 0.7rem; margin-top: 0.3rem;" id="receiptOrderNum">ORDER: POS-PREVIEW</div>
                    <div style="font-size: 0.7rem;" id="receiptDate">DATE: {{ now()->format('Y-m-d H:i') }}</div>
                    <div style="font-size: 0.7rem;" id="receiptCashier">{{ $isAr ? 'الكاشير:' : 'CASHIER:' }} {{ auth()->user()?->name ?? 'Specialist' }}</div>
                </div>

                <!-- Customer Details -->
                <div style="font-size: 0.72rem; margin-bottom: 0.65rem; border-bottom: 1px dashed #9CA3AF; padding-bottom: 0.45rem;">
                    <div><strong>{{ $isAr ? 'العميل:' : 'CUSTOMER:' }}</strong> <span id="receiptCustomer">Walk-In Warehouse VIP</span></div>
                    <div><strong>{{ $isAr ? 'طريقة الدفع:' : 'PAYMENT:' }}</strong> <span id="receiptTender">Mada / Debit POS</span></div>
                </div>

                <!-- Itemized Table -->
                <div style="font-size: 0.72rem; margin-bottom: 0.65rem;">
                    <div style="display: flex; justify-content: space-between; font-weight: bold; border-bottom: 1px solid #111827; padding-bottom: 0.2rem; margin-bottom: 0.3rem;">
                        <span>{{ $isAr ? 'التركيبة' : 'ITEM' }}</span>
                        <span>{{ $isAr ? 'الكمية × السعر' : 'QTY × PRICE' }}</span>
                        <span>{{ $isAr ? 'الإجمالي' : 'TOTAL' }}</span>
                    </div>
                    <div id="receiptItemsRows" style="display: flex; flex-direction: column; gap: 0.3rem;">
                        <!-- Dynamic receipt rows -->
                    </div>
                </div>

                <!-- Totals -->
                <div style="border-top: 1px dashed #9CA3AF; padding-top: 0.45rem; font-size: 0.72rem; display: flex; flex-direction: column; gap: 0.2rem;">
                    <div style="display: flex; justify-content: space-between;">
                        <span>{{ $isAr ? 'المجموع الفرعي:' : 'SUBTOTAL:' }}</span>
                        <span id="receiptSubtotal">$0.00</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; color: #047857;">
                        <span>{{ $isAr ? 'الخصم الترويجي:' : 'DISCOUNT:' }}</span>
                        <span id="receiptDiscount">-$0.00</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span>{{ $isAr ? 'ضريبة القيمة المضافة (15%):' : 'VAT (15%):' }}</span>
                        <span id="receiptTax">$0.00</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-weight: 900; font-size: 0.92rem; border-top: 1px solid #111827; padding-top: 0.3rem; margin-top: 0.15rem;">
                        <span>{{ $isAr ? 'المبلغ المستحق:' : 'TOTAL DUE:' }}</span>
                        <span id="receiptGrandTotal">$0.00</span>
                    </div>
                </div>

                <!-- QR Code Simulation (ZATCA Compliant Style) -->
                <div style="text-align: center; margin-top: 0.85rem; border-top: 1px dashed #9CA3AF; padding-top: 0.65rem;">
                    <div style="width: 75px; height: 75px; margin: 0 auto; background: #fff; border: 1px solid #000; padding: 3px; display: flex; align-items: center; justify-content: center;">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=70x70&data=BLUEZONE_POS_VERIFIED" alt="ZATCA QR" style="width: 100%; height: 100%;" onerror="this.parentElement.innerHTML='[ZATCA QR CODE]'">
                    </div>
                    <div style="font-size: 0.62rem; color: #6B7280; margin-top: 0.35rem;">{{ $isAr ? 'شكراً لزيارتكم • نتمنى لكم دوام الصحة والعافية' : 'THANK YOU FOR CHOOSING BLUE ZONE' }}</div>
                </div>
            </div>

            <!-- Modal Action Buttons -->
            <div style="display: flex; gap: 0.45rem; margin-top: 1rem;">
                <button type="button" class="btn btn-sm btn-ghost" onclick="closeReceiptModal()" style="flex: 1;">{{ $isAr ? 'إغلاق' : 'Close' }}</button>
                <button type="button" class="btn btn-sm btn-primary" onclick="printThermalSlip()" style="flex: 2; font-weight: 700;">
                    <i class="fa-solid fa-print mr-1 ml-1"></i> {{ __('admin.pos.print_receipt') }}
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Layout & Rich Animation Styles -->
    <style>
        /* 1. Main Responsive POS Grid */
        .pos-main-layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 420px;
            gap: 1.5rem;
            align-items: start;
        }
        @media (max-width: 1200px) {
            .pos-main-layout {
                grid-template-columns: minmax(0, 1fr) 370px;
                gap: 1.25rem;
            }
        }
        @media (max-width: 991px) {
            .pos-main-layout {
                grid-template-columns: 1fr;
                gap: 1.25rem;
            }
            .pos-cart-panel {
                position: static !important;
            }
            .pos-mobile-cart-bar {
                display: flex !important;
            }
        }

        /* 2. Responsive Product Grid */
        .pos-product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(175px, 1fr));
            gap: 1rem;
        }
        @media (max-width: 576px) {
            .pos-product-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 0.65rem;
            }
            .pos-card {
                padding: 0.85rem !important;
            }
        }

        /* 3. Luxury Formulation Cards */
        .pos-card {
            padding: 1.1rem;
            cursor: pointer;
            text-align: center;
            border-radius: var(--radius-lg);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid transparent;
            background: #fff;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 240px;
        }
        .pos-card:hover {
            border-color: var(--color-primary) !important;
            box-shadow: 0 12px 26px -6px rgba(10, 17, 40, 0.12) !important;
            transform: translateY(-5px);
        }
        .pos-card:active {
            transform: scale(0.97);
        }
        .pos-card.in-cart {
            border-color: #0EA5E9 !important;
            background: linear-gradient(180deg, rgba(14, 165, 233, 0.03), #fff) !important;
            box-shadow: 0 8px 20px -4px rgba(14, 165, 233, 0.18) !important;
        }
        .pos-card-oos {
            opacity: 0.55;
            filter: grayscale(0.7);
        }
        .pos-card-oos:hover {
            border-color: #EF4444 !important;
        }
        .pos-img-wrapper {
            position: relative;
            width: 75px;
            height: 75px;
            margin: 0.2rem auto 0.65rem auto;
        }
        .pos-prod-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: var(--radius-md);
            background: var(--color-bg-subtle);
            transition: transform 0.3s ease;
        }
        .pos-card:hover .pos-prod-img {
            transform: scale(1.06);
        }
        .pos-prod-title {
            margin-bottom: 0.25rem;
            line-height: 1.3;
            height: 32px;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            color: var(--color-text-main);
            font-weight: 700;
        }
        .pos-prod-sku {
            font-family: monospace;
            font-size: 0.68rem;
            margin-bottom: 0.35rem;
        }
        .pos-card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-top: 1px solid var(--color-border);
            padding-top: 0.5rem;
            margin-top: 0.4rem;
        }
        .pos-in-cart-indicator {
            position: absolute;
            top: 8px;
            inset-inline-start: 8px;
            background: #0EA5E9;
            color: #fff;
            font-size: 0.62rem;
            font-weight: bold;
            padding: 0.15rem 0.4rem;
            border-radius: 12px;
            z-index: 2;
            box-shadow: 0 2px 6px rgba(14, 165, 233, 0.4);
            animation: posFadeIn 0.2s ease;
        }

        /* 4. Cart Panel Sticky Positioning */
        .pos-cart-panel {
            padding: 1.35rem;
            border-radius: var(--radius-lg);
            display: flex;
            flex-direction: column;
            gap: 1rem;
            background: #fff;
            border: 1px solid var(--color-border);
            box-shadow: 0 10px 30px -5px rgba(0,0,0,0.06);
            position: sticky;
            top: 1.25rem;
        }

        /* 5. Mobile Floating Cart Bar */
        .pos-mobile-cart-bar {
            display: none;
            position: fixed;
            bottom: 1rem;
            left: 1rem;
            right: 1rem;
            background: var(--color-primary);
            color: #fff;
            padding: 0.75rem 1.25rem;
            border-radius: 30px;
            box-shadow: 0 10px 25px rgba(10, 17, 40, 0.4);
            z-index: 999;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            animation: posSlideUp 0.3s ease;
        }

        /* 6. Filter & Pill Buttons */
        .pos-cat-btn.active {
            background: var(--color-primary) !important;
            color: #fff !important;
            border-color: var(--color-primary) !important;
            box-shadow: 0 4px 12px rgba(10, 17, 40, 0.2);
        }
        .disc-pill-btn.active {
            background: var(--color-primary) !important;
            color: #fff !important;
        }
        .pos-tender-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.3rem;
            padding: 0.65rem 0.45rem;
            border-radius: var(--radius-md);
            border: 1.5px solid var(--color-border);
            cursor: pointer;
            text-align: center;
            font-size: 0.72rem;
            font-weight: 700;
            transition: all 0.2s ease;
            background: #fff;
        }
        .pos-tender-label i {
            font-size: 1.15rem;
            color: var(--color-text-muted);
            transition: color 0.2s ease;
        }
        .pos-tender-label.active {
            border-color: var(--color-primary);
            background: rgba(10, 17, 40, 0.04);
            color: var(--color-primary);
        }
        .pos-tender-label.active i {
            color: var(--color-primary);
        }

        /* 7. Steppers & Cart Items */
        .cart-item-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.55rem 0.65rem;
            background: var(--color-bg-subtle);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            animation: posFadeIn 0.2s ease;
            gap: 0.5rem;
        }
        .cart-item-row:hover {
            border-color: rgba(10,17,40,0.2);
            background: #fff;
        }
        .qty-stepper-btn {
            width: 22px;
            height: 22px;
            border-radius: 4px;
            border: 1px solid var(--color-border);
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-weight: bold;
            font-size: 0.78rem;
            transition: all 0.15s ease;
        }
        .qty-stepper-btn:hover {
            background: var(--color-primary);
            color: #fff;
            border-color: var(--color-primary);
        }
        .qty-stepper-btn:active {
            transform: scale(0.9);
        }

        /* 8. Hotkeys & Modals */
        .hotkey-row {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.45rem 0;
            border-bottom: 1px solid var(--color-border);
        }
        .hotkey-badge {
            display: inline-block;
            min-width: 60px;
            text-align: center;
            background: var(--color-bg-subtle);
            border: 1px solid var(--color-border);
            padding: 0.2rem 0.45rem;
            border-radius: var(--radius-sm);
            font-family: monospace;
            font-weight: bold;
            font-size: 0.72rem;
            color: var(--color-primary);
        }
        .pos-modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(10, 17, 40, 0.65);
            backdrop-filter: blur(4px);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        /* 9. Interactive Tour Spotlight */
        .pos-tour-overlay {
            position: fixed;
            inset: 0;
            z-index: 10000;
            pointer-events: auto;
        }
        .pos-tour-highlight-box {
            position: absolute;
            border-radius: 12px;
            box-shadow: 0 0 0 9999px rgba(10, 17, 40, 0.75), 0 0 20px rgba(14, 165, 233, 0.85);
            border: 2px solid #0EA5E9;
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            pointer-events: none;
            animation: posPulseTourGlow 2s infinite;
        }
        .pos-tour-card {
            position: absolute;
            width: 310px;
            max-width: 90vw;
            background: #fff;
            border-radius: var(--radius-lg);
            padding: 1.15rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            border: 1px solid var(--color-border);
            z-index: 10001;
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* 10. Keyframe Animations */
        @keyframes posPulseTourGlow {
            0%, 100% { border-color: #0EA5E9; box-shadow: 0 0 0 9999px rgba(10, 17, 40, 0.75), 0 0 15px rgba(14, 165, 233, 0.6); }
            50% { border-color: #38BDF8; box-shadow: 0 0 0 9999px rgba(10, 17, 40, 0.75), 0 0 25px rgba(56, 189, 248, 0.9); }
        }
        @keyframes posPulseGlow {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.3); }
        }
        @keyframes posSlideDown {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        @keyframes posSlideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        @keyframes posFadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes posShake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-5px); }
            40%, 80% { transform: translateX(5px); }
        }
        .pos-shake {
            animation: posShake 0.4s ease;
        }

        /* 11. Scrollbars */
        .custom-scroll::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scroll::-webkit-scrollbar-thumb {
            background: rgba(0,0,0,0.15);
            border-radius: 4px;
        }
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
    </style>

    <!-- 12. Interactive POS Engine JavaScript -->
    <script>
        // Global POS State
        let posCart = [];
        let audioEnabled = true;
        let activeDiscountPct = 0;
        let selectedPaymentMethod = 'Mada / Debit POS Terminal';
        let currentTourStep = 0;
        const isArabicLocale = {{ $isAr ? 'true' : 'false' }};

        // Sound Synthesizer via Web Audio API
        const audioCtx = (window.AudioContext || window.webkitAudioContext) ? new (window.AudioContext || window.webkitAudioContext)() : null;

        function playBeepSound(type = 'scan') {
            if (!audioEnabled || !audioCtx) return;
            try {
                if (audioCtx.state === 'suspended') {
                    audioCtx.resume();
                }
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();
                osc.connect(gain);
                gain.connect(audioCtx.destination);

                if (type === 'scan') {
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(1400, audioCtx.currentTime);
                    osc.frequency.exponentialRampToValueAtTime(850, audioCtx.currentTime + 0.08);
                    gain.gain.setValueAtTime(0.2, audioCtx.currentTime);
                    gain.gain.linearRampToValueAtTime(0.01, audioCtx.currentTime + 0.08);
                    osc.start();
                    osc.stop(audioCtx.currentTime + 0.08);
                } else if (type === 'success') {
                    // Harmonious cash chime
                    osc.type = 'triangle';
                    osc.frequency.setValueAtTime(523.25, audioCtx.currentTime); // C5
                    osc.frequency.setValueAtTime(659.25, audioCtx.currentTime + 0.1); // E5
                    osc.frequency.setValueAtTime(783.99, audioCtx.currentTime + 0.2); // G5
                    gain.gain.setValueAtTime(0.25, audioCtx.currentTime);
                    gain.gain.linearRampToValueAtTime(0.01, audioCtx.currentTime + 0.4);
                    osc.start();
                    osc.stop(audioCtx.currentTime + 0.4);
                } else if (type === 'warning') {
                    osc.type = 'sawtooth';
                    osc.frequency.setValueAtTime(280, audioCtx.currentTime);
                    gain.gain.setValueAtTime(0.2, audioCtx.currentTime);
                    gain.gain.linearRampToValueAtTime(0.01, audioCtx.currentTime + 0.15);
                    osc.start();
                    osc.stop(audioCtx.currentTime + 0.15);
                }
            } catch (e) {
                console.warn('Audio notice:', e);
            }
        }

        function togglePOSAudio() {
            audioEnabled = !audioEnabled;
            const icon = document.getElementById('posAudioIcon');
            const label = document.getElementById('posAudioLabel');
            if (audioEnabled) {
                icon.className = 'fa-solid fa-volume-high text-primary';
                label.innerText = isArabicLocale ? 'الصوت: مفعّل' : 'Sound: ON';
                playBeepSound('scan');
            } else {
                icon.className = 'fa-solid fa-volume-xmark text-muted';
                label.innerText = isArabicLocale ? 'الصوت: مكتوم' : 'Sound: OFF';
            }
        }

        // Live Clock
        function updatePOSClock() {
            const clockEl = document.getElementById('posDigitalClock');
            if (clockEl) {
                const now = new Date();
                clockEl.innerText = now.toLocaleTimeString();
            }
        }
        setInterval(updatePOSClock, 1000);
        updatePOSClock();

        // Product Catalog Interaction & Fly to Cart Animation
        function handleProductClick(id, name, variant, price, stock, img, sku) {
            if (stock <= 0) {
                playBeepSound('warning');
                showNotification(isArabicLocale ? 'تنبيه: هذا المنتج غير متوفر في مخزون المستودع حالياً!' : 'Warning: This product is out of warehouse stock!', 'danger');
                return;
            }

            const card = document.getElementById('card-prod-' + id);
            if (card) {
                animateFlyToCart(card);
            }

            addToCart({
                id: id,
                name: name,
                variant: variant,
                price: parseFloat(price),
                stock: parseInt(stock),
                img: img,
                sku: sku,
                qty: 1
            });

            playBeepSound('scan');
        }

        function animateFlyToCart(cardEl) {
            const imgEl = cardEl.querySelector('.pos-prod-img') || cardEl;
            const rect = imgEl.getBoundingClientRect();
            const cartIcon = document.getElementById('tour-step-cart') || document.getElementById('cartItemCount');
            const cartRect = cartIcon.getBoundingClientRect();

            const ghost = document.createElement('div');
            ghost.style.position = 'fixed';
            ghost.style.left = rect.left + 'px';
            ghost.style.top = rect.top + 'px';
            ghost.style.width = rect.width + 'px';
            ghost.style.height = rect.height + 'px';
            ghost.style.background = 'rgba(14, 165, 233, 0.25)';
            ghost.style.border = '2px solid #0EA5E9';
            ghost.style.borderRadius = '12px';
            ghost.style.zIndex = '9998';
            ghost.style.pointerEvents = 'none';
            ghost.style.transition = 'all 0.4s cubic-bezier(0.2, 0.8, 0.2, 1)';
            document.body.appendChild(ghost);

            setTimeout(() => {
                ghost.style.left = (cartRect.left + (isArabicLocale ? -20 : 20)) + 'px';
                ghost.style.top = (cartRect.top + 20) + 'px';
                ghost.style.width = '24px';
                ghost.style.height = '24px';
                ghost.style.opacity = '0';
                ghost.style.transform = 'scale(0.2)';
            }, 20);

            setTimeout(() => {
                ghost.remove();
            }, 450);
        }

        // Cart Management
        function addToCart(product) {
            const existing = posCart.find(item => item.id === product.id);
            if (existing) {
                if (existing.qty + 1 > product.stock) {
                    playBeepSound('warning');
                    showNotification(isArabicLocale ? 'الكمية المطلوبة تتجاوز رصيد المستودع المتوفر!' : 'Cannot add more: exceeds warehouse stock limit!', 'warning');
                    return;
                }
                existing.qty += 1;
            } else {
                posCart.push({
                    product_id: product.id,
                    id: product.id,
                    name: product.name,
                    variant: product.variant,
                    unit_price: product.price,
                    stock: product.stock,
                    image: product.img,
                    sku: product.sku,
                    quantity: 1,
                    qty: 1
                });
            }

            renderCart();
        }

        function updateCartItemQty(id, delta) {
            const item = posCart.find(i => i.id === id);
            if (!item) return;

            const newQty = item.qty + delta;
            if (newQty <= 0) {
                removeCartItem(id);
                return;
            }
            if (newQty > item.stock) {
                playBeepSound('warning');
                const row = document.getElementById('cart-item-' + id);
                if (row) {
                    row.classList.add('pos-shake');
                    setTimeout(() => row.classList.remove('pos-shake'), 400);
                }
                showNotification(isArabicLocale ? 'الكمية تتجاوز رصيد المستودع المتوفر!' : 'Exceeds available warehouse stock!', 'warning');
                return;
            }

            item.qty = newQty;
            item.quantity = newQty;
            renderCart();
        }

        function removeCartItem(id) {
            posCart = posCart.filter(i => i.id !== id);
            renderCart();
        }

        function clearEntireCart() {
            if (posCart.length === 0) return;
            posCart = [];
            renderCart();
            showNotification(isArabicLocale ? 'تم تفريغ السلة بنجاح' : 'Cart cleared', 'info');
        }

        function renderCart() {
            const container = document.getElementById('cartItemsContainer');
            const emptyMsg = document.getElementById('emptyCartMessage');
            const itemCountEl = document.getElementById('cartItemCount');
            const unitCountEl = document.getElementById('cartUnitCount');
            const mobileCountEl = document.getElementById('mobileCartItemCount');
            const mobileTotalEl = document.getElementById('mobileCartGrandTotal');
            const submitBtn = document.getElementById('posSubmitBtn');

            // Reset all card active states
            document.querySelectorAll('.pos-card').forEach(card => {
                card.classList.remove('in-cart');
                const badge = card.querySelector('.pos-in-cart-indicator');
                if (badge) badge.style.display = 'none';
            });

            if (posCart.length === 0) {
                container.innerHTML = '';
                emptyMsg.style.display = 'flex';
                itemCountEl.innerText = '0';
                unitCountEl.innerText = '0';
                if (mobileCountEl) mobileCountEl.innerText = '0';
                submitBtn.disabled = true;
                recalcPOSCart();
                syncFormInputs();
                return;
            }

            emptyMsg.style.display = 'none';
            submitBtn.disabled = false;

            let totalUnits = 0;
            let html = '';

            const formatCurr = (val) => (window.BLUEZONE_CURRENCY ? window.BLUEZONE_CURRENCY.format(val) : ('{{ $currencySymbol }}' + Number(val).toFixed(2)));

            posCart.forEach(item => {
                totalUnits += item.qty;
                const itemTotal = item.unit_price * item.qty;

                // Mark product card active
                const card = document.getElementById('card-prod-' + item.id);
                if (card) {
                    card.classList.add('in-cart');
                    const badge = document.getElementById('card-in-cart-' + item.id);
                    if (badge) {
                        badge.style.display = 'block';
                        const qtySpan = badge.querySelector('.in-cart-qty');
                        if (qtySpan) qtySpan.innerText = item.qty;
                    }
                }

                html += `
                    <div class="cart-item-row" id="cart-item-${item.id}">
                        <img src="${item.image}" alt="${item.name}" style="width: 36px; height: 36px; border-radius: var(--radius-sm); object-fit: cover; background: #fff;" onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';">
                        
                        <div style="flex: 1; min-width: 0;">
                            <div class="font-bold text-xs" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: var(--color-text-main);">${item.name}</div>
                            <div class="text-xs text-muted" style="font-size: 0.68rem;">
                                ${formatCurr(item.unit_price)} × ${item.qty} = <strong class="text-primary">${formatCurr(itemTotal)}</strong>
                            </div>
                        </div>

                        <!-- Stepper -->
                        <div style="display: flex; align-items: center; gap: 0.2rem;">
                            <button type="button" class="qty-stepper-btn" onclick="updateCartItemQty(${item.id}, -1)">-</button>
                            <span class="font-bold text-xs" style="min-width: 18px; text-align: center;">${item.qty}</span>
                            <button type="button" class="qty-stepper-btn" onclick="updateCartItemQty(${item.id}, 1)">+</button>
                        </div>

                        <button type="button" class="btn btn-xs btn-ghost" onclick="removeCartItem(${item.id})" style="color: #EF4444; padding: 0.2rem 0.35rem;" title="${isArabicLocale ? 'حذف' : 'Remove'}">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                `;
            });

            container.innerHTML = html;
            itemCountEl.innerText = posCart.length;
            unitCountEl.innerText = totalUnits;
            if (mobileCountEl) mobileCountEl.innerText = totalUnits;

            recalcPOSCart();
            syncFormInputs();
        }

        function recalcPOSCart() {
            let subtotal = 0;
            posCart.forEach(item => {
                subtotal += (item.unit_price * item.qty);
            });

            const discountInput = document.getElementById('posDiscount');
            let discount = parseFloat(discountInput.value) || 0;

            if (discount > subtotal) {
                discount = subtotal;
                discountInput.value = discount.toFixed(2);
            }

            const discountedSubtotal = Math.max(0, subtotal - discount);
            const tax = discountedSubtotal * 0.15;
            const grandTotal = discountedSubtotal + tax;

            const formatCurr = (val) => (window.BLUEZONE_CURRENCY ? window.BLUEZONE_CURRENCY.format(val) : ('{{ $currencySymbol }}' + Number(val).toFixed(2)));

            document.getElementById('posSubtotal').innerText = formatCurr(subtotal);
            document.getElementById('posDiscountDisplay').innerText = '-' + formatCurr(discount);
            document.getElementById('posTax').innerText = formatCurr(tax);
            document.getElementById('posGrandTotal').innerText = formatCurr(grandTotal);

            const mobileTotalEl = document.getElementById('mobileCartGrandTotal');
            if (mobileTotalEl) mobileTotalEl.innerText = formatCurr(grandTotal);

            calculateChangeDue();
        }

        function syncFormInputs() {
            const jsonField = document.getElementById('posCartItemsJson');
            const fallbackId = document.getElementById('fallbackProductId');
            const fallbackQty = document.getElementById('fallbackQuantity');
            const fallbackPrice = document.getElementById('fallbackUnitPrice');
            const fallbackVar = document.getElementById('fallbackVariant');

            jsonField.value = JSON.stringify(posCart);

            if (posCart.length > 0) {
                fallbackId.value = posCart[0].id;
                fallbackQty.value = posCart[0].qty;
                fallbackPrice.value = posCart[0].unit_price;
                fallbackVar.value = posCart[0].variant;
            }
        }

        // Quick Discount Percentages
        function applyQuickDiscountPct(pct, btn) {
            activeDiscountPct = pct;
            document.querySelectorAll('.disc-pill-btn').forEach(b => b.classList.remove('active'));
            if (btn) btn.classList.add('active');

            let subtotal = 0;
            posCart.forEach(item => subtotal += (item.unit_price * item.qty));

            const discountVal = (subtotal * (pct / 100)).toFixed(2);
            document.getElementById('posDiscount').value = discountVal;
            recalcPOSCart();
        }

        // Tender / Payment Method
        function selectTender(method, labelEl) {
            selectedPaymentMethod = method;
            document.querySelectorAll('.pos-tender-label').forEach(l => l.classList.remove('active'));
            if (labelEl) labelEl.classList.add('active');

            const radio = labelEl ? labelEl.querySelector('input[type="radio"]') : null;
            if (radio) radio.checked = true;

            const cashBox = document.getElementById('cashCalculatorBox');
            if (method === 'Cash Tender') {
                cashBox.style.display = 'block';
                setExactCashTender();
            } else {
                cashBox.style.display = 'none';
            }
        }

        function setExactCashTender() {
            let subtotal = 0;
            posCart.forEach(item => subtotal += (item.unit_price * item.qty));
            const discount = parseFloat(document.getElementById('posDiscount').value) || 0;
            const discountedSubtotal = Math.max(0, subtotal - discount);
            const total = discountedSubtotal * 1.15;

            document.getElementById('cashReceivedInput').value = total.toFixed(2);
            calculateChangeDue();
        }

        function addCashAmount(amount) {
            const input = document.getElementById('cashReceivedInput');
            const current = parseFloat(input.value) || 0;
            input.value = (current + amount).toFixed(2);
            calculateChangeDue();
        }

        function resetCashAmount() {
            document.getElementById('cashReceivedInput').value = '0.00';
            calculateChangeDue();
        }

        function calculateChangeDue() {
            let subtotal = 0;
            posCart.forEach(item => subtotal += (item.unit_price * item.qty));
            const discount = parseFloat(document.getElementById('posDiscount').value) || 0;
            const total = Math.max(0, subtotal - discount) * 1.15;

            const cashReceived = parseFloat(document.getElementById('cashReceivedInput').value) || 0;
            const change = Math.max(0, cashReceived - total);

            const formatCurr = (val) => (window.BLUEZONE_CURRENCY ? window.BLUEZONE_CURRENCY.format(val) : ('{{ $currencySymbol }}' + Number(val).toFixed(2)));
            document.getElementById('changeDueDisplay').innerText = formatCurr(change);
        }

        // Category Filtering
        function filterByCategory(catId, btn) {
            document.querySelectorAll('.pos-cat-btn').forEach(b => b.classList.remove('active'));
            if (btn) btn.classList.add('active');

            const searchVal = document.getElementById('posSearch').value.toLowerCase().trim();
            const cards = document.querySelectorAll('.pos-card');
            let visibleCount = 0;

            cards.forEach(card => {
                const cardCat = card.getAttribute('data-category');
                const cardName = card.getAttribute('data-name') || '';
                const cardSku = card.getAttribute('data-sku') || '';
                const cardBarcode = card.getAttribute('data-barcode') || '';

                const matchesCat = (catId === 'all' || cardCat === catId);
                const matchesSearch = !searchVal || cardName.includes(searchVal) || cardSku.includes(searchVal) || cardBarcode.includes(searchVal);

                if (matchesCat && matchesSearch) {
                    card.style.display = 'flex';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            document.getElementById('noProductsFound').style.display = visibleCount === 0 ? 'block' : 'none';
        }

        function filterPOS() {
            const query = document.getElementById('posSearch').value.toLowerCase().trim();
            document.getElementById('clearSearchBtn').style.display = query.length > 0 ? 'block' : 'none';

            const activeCatBtn = document.querySelector('.pos-cat-btn.active');
            const catId = activeCatBtn ? activeCatBtn.getAttribute('data-cat') : 'all';

            filterByCategory(catId, activeCatBtn);
        }

        function clearPOSSearch() {
            const input = document.getElementById('posSearch');
            input.value = '';
            document.getElementById('clearSearchBtn').style.display = 'none';
            input.focus();
            filterPOS();
        }

        // Barcode Scan Simulation
        function simulateBarcodeScan() {
            const availableCards = Array.from(document.querySelectorAll('.pos-card:not(.pos-card-oos)'));
            if (availableCards.length === 0) {
                showNotification(isArabicLocale ? 'لا توجد منتجات متوفرة للمحاكاة!' : 'No available items to scan!', 'warning');
                return;
            }

            const randomCard = availableCards[Math.floor(Math.random() * availableCards.length)];
            randomCard.click();

            showNotification(isArabicLocale ? 'تم مسح الباركود بنجاح!' : 'Barcode scanned successfully!', 'success');
        }

        // Customer Selection
        function toggleCustomCustomer(val) {
            const customFields = document.getElementById('customCustomerFields');
            if (val === '__custom__') {
                customFields.style.display = 'flex';
            } else {
                customFields.style.display = 'none';
            }
        }

        // Draft Hold & Recall Functionality
        function checkHeldCartOnLoad() {
            const held = localStorage.getItem('bz_held_pos_cart');
            const banner = document.getElementById('heldCartNotification');
            if (held && banner) {
                try {
                    const parsed = JSON.parse(held);
                    if (Array.isArray(parsed) && parsed.length > 0) {
                        const count = parsed.reduce((sum, item) => sum + item.qty, 0);
                        document.getElementById('heldCartText').innerText = isArabicLocale
                            ? `توجد سلة معلقة محفوظة (${count} وحدات)`
                            : `A held cart is available (${count} units)`;
                        banner.style.display = 'flex';
                    }
                } catch(e) {}
            }
        }

        function holdCurrentCart() {
            if (posCart.length === 0) return;
            localStorage.setItem('bz_held_pos_cart', JSON.stringify(posCart));
            posCart = [];
            renderCart();
            checkHeldCartOnLoad();
            showNotification(isArabicLocale ? 'تم تعليق السلة مؤقتاً بنجاح!' : 'Cart placed on hold successfully!', 'info');
        }

        function recallHeldCart() {
            const held = localStorage.getItem('bz_held_pos_cart');
            if (!held) return;
            try {
                posCart = JSON.parse(held);
                localStorage.removeItem('bz_held_pos_cart');
                document.getElementById('heldCartNotification').style.display = 'none';
                renderCart();
                playBeepSound('success');
                showNotification(isArabicLocale ? 'تم استعادة السلة المعلقة بنجاح!' : 'Held cart restored successfully!', 'success');
            } catch(e) {}
        }

        function discardHeldCart() {
            localStorage.removeItem('bz_held_pos_cart');
            document.getElementById('heldCartNotification').style.display = 'none';
        }

        function scrollToCartPanel() {
            const panel = document.getElementById('tour-step-cart');
            if (panel) {
                panel.scrollIntoView({ behavior: 'smooth' });
            }
        }

        // Thermal Receipt Modal
        function openThermalReceiptModal(isPostSale = false, saleData = null) {
            if (posCart.length === 0 && !isPostSale) {
                showNotification(isArabicLocale ? 'السلة فارغة للمعاينة!' : 'Cart is empty for preview!', 'warning');
                return;
            }

            const formatCurr = (val) => (window.BLUEZONE_CURRENCY ? window.BLUEZONE_CURRENCY.format(val) : ('{{ $currencySymbol }}' + Number(val).toFixed(2)));
            const rowsContainer = document.getElementById('receiptItemsRows');
            rowsContainer.innerHTML = '';

            let subtotal = 0;
            posCart.forEach(item => {
                const lineTotal = item.unit_price * item.qty;
                subtotal += lineTotal;
                rowsContainer.innerHTML += `
                    <div style="display: flex; justify-content: space-between;">
                        <span style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${item.name}</span>
                        <span>${item.qty} × ${formatCurr(item.unit_price)}</span>
                        <span>${formatCurr(lineTotal)}</span>
                    </div>
                `;
            });

            const discount = parseFloat(document.getElementById('posDiscount').value) || 0;
            const discountedSubtotal = Math.max(0, subtotal - discount);
            const tax = discountedSubtotal * 0.15;
            const total = discountedSubtotal + tax;

            document.getElementById('receiptSubtotal').innerText = formatCurr(subtotal);
            document.getElementById('receiptDiscount').innerText = '-' + formatCurr(discount);
            document.getElementById('receiptTax').innerText = formatCurr(tax);
            document.getElementById('receiptGrandTotal').innerText = formatCurr(total);
            document.getElementById('receiptCustomer').innerText = document.getElementById('customerSelect').value;
            document.getElementById('receiptTender').innerText = selectedPaymentMethod;

            if (saleData) {
                document.getElementById('receiptOrderNum').innerText = 'ORDER: ' + saleData.order_number;
            }

            document.getElementById('receiptModal').style.display = 'flex';
        }

        function closeReceiptModal() {
            document.getElementById('receiptModal').style.display = 'none';
        }

        function printThermalSlip() {
            const printContent = document.getElementById('thermalReceiptSlip').innerHTML;
            const printWin = window.open('', '_blank', 'width=450,height=600');
            printWin.document.write(`
                <html dir="${isArabicLocale ? 'rtl' : 'ltr'}">
                    <head>
                        <title>Receipt Print</title>
                        <style>
                            body { font-family: 'Courier New', monospace; padding: 20px; font-size: 12px; color: #000; direction: ${isArabicLocale ? 'rtl' : 'ltr'}; }
                            @media print { body { padding: 0; } }
                        </style>
                    </head>
                    <body onload="window.print(); window.close();">
                        ${printContent}
                    </body>
                </html>
            `);
            printWin.document.close();
        }

        // Form Submit Handler
        function handlePOSSubmit(e) {
            if (posCart.length === 0) {
                e.preventDefault();
                showNotification(isArabicLocale ? 'يرجى إضافة تركيبة واحدة على الأقل للسلة!' : 'Please add at least one formulation to cart!', 'warning');
                return false;
            }
            playBeepSound('success');
            return true;
        }

        // Keyboard Hotkeys
        function openHotkeysModal() {
            document.getElementById('hotkeysModal').style.display = 'flex';
        }
        function closeHotkeysModal() {
            document.getElementById('hotkeysModal').style.display = 'none';
        }

        window.addEventListener('keydown', (e) => {
            if (e.key === 'F2') {
                e.preventDefault();
                document.getElementById('posSearch').focus();
            } else if (e.key === 'F4') {
                e.preventDefault();
                document.getElementById('posDiscount').focus();
            } else if (e.key === 'F8') {
                e.preventDefault();
                const cashLabel = document.querySelectorAll('.pos-tender-label')[2];
                if (cashLabel) cashLabel.click();
            } else if (e.key === 'Escape') {
                closeHotkeysModal();
                closeReceiptModal();
                endPOSTour();
            } else if (e.ctrlKey && e.key === 'Enter') {
                e.preventDefault();
                const submitBtn = document.getElementById('posSubmitBtn');
                if (!submitBtn.disabled) {
                    document.getElementById('posForm').submit();
                }
            }
        });

        // Interactive Tour System
        const tourSteps = [
            {
                elementId: 'tour-step-search',
                title: isArabicLocale ? '١. مسح الباركود والبحث الفوري' : '1. Fast Barcode Scanner & Search',
                content: isArabicLocale ? 'يمكنك مسح باركود أي عبوة مباشرة أو كتابة اسم التركيبة أو رمز SKU للفلترة الفورية.' : 'Scan any bottle barcode with your physical scanner or type formulation name / SKU to filter products instantly.'
            },
            {
                elementId: 'tour-step-categories',
                title: isArabicLocale ? '٢. تصنيفات التركيبات الصيدلانية' : '2. Clinical Category Filters',
                content: isArabicLocale ? 'تنقل بسلاسة بين الأنظمة الصحية (طول العمر الخلوي، الإدراك، الحيوية، المناعة) للوصول السريع.' : 'Switch between longevity health systems (Cellular, Nootropic, Vitality, Immunity) for one-tap access.'
            },
            {
                elementId: 'tour-step-products',
                title: isArabicLocale ? '٣. بطاقات المنتجات والإضافة بلمسة واحدة' : '3. Fast Tap Product Cards',
                content: isArabicLocale ? 'انقر على أي تركيبة لإضافتها للسلة فورياً مع مؤثر صوتي والتحقق من رصيد المعرض.' : 'Tap any formulation card to add it to the active cart with instant audio feedback and stock validation.'
            },
            {
                elementId: 'tour-step-cart',
                title: isArabicLocale ? '٤. إدارة السلة وتعديل الكميات' : '4. Multi-Item Cart & Quantities',
                content: isArabicLocale ? 'تحكم في كميات العناصر المضافة، احذف العناصر، أو علق السلة مؤقتاً عند الحاجة.' : 'Adjust quantities with (+/-) steppers, review line totals, or place the cart on draft hold.'
            },
            {
                elementId: 'tour-step-tender',
                title: isArabicLocale ? '٥. طرق السداد وحاسبة النقد الفورية' : '5. Tender Options & Cash Calculator',
                content: isArabicLocale ? 'اختر طريقة الدفع (مدى، بطاقة، أبل باي، أو كاش مع حاسبة الفكة الدقيقة) ثم اطبع الإيصال فوراً!' : 'Select payment tender (Mada, Cards, Apple Pay, or Cash with change calculator) and complete sale!'
            }
        ];

        function startPOSTour(force = false) {
            currentTourStep = 0;
            document.getElementById('posTourOverlay').style.display = 'block';
            renderTourStep();
        }

        function endPOSTour() {
            document.getElementById('posTourOverlay').style.display = 'none';
            localStorage.setItem('bz_pos_tour_seen', 'true');
        }

        function renderTourStep() {
            const step = tourSteps[currentTourStep];
            if (!step) {
                endPOSTour();
                return;
            }

            const targetEl = document.getElementById(step.elementId);
            if (!targetEl) {
                nextPOSTourStep();
                return;
            }

            const rect = targetEl.getBoundingClientRect();
            const highlightBox = document.getElementById('posTourHighlightBox');
            const tourCard = document.getElementById('posTourCard');

            highlightBox.style.top = (rect.top + window.scrollY - 6) + 'px';
            highlightBox.style.left = (rect.left + window.scrollX - 6) + 'px';
            highlightBox.style.width = (rect.width + 12) + 'px';
            highlightBox.style.height = (rect.height + 12) + 'px';

            document.getElementById('posTourStepBadge').innerText = isArabicLocale 
                ? `خطوة ${currentTourStep + 1} من ${tourSteps.length}` 
                : `Step ${currentTourStep + 1} of ${tourSteps.length}`;
            document.getElementById('posTourTitle').innerText = step.title;
            document.getElementById('posTourContent').innerText = step.content;

            // Responsive position for tooltip card
            const cardTop = Math.min(window.innerHeight - 230, Math.max(20, rect.top + 20));
            let cardLeft = rect.left > 340 ? (rect.left - 330) : (rect.right + 20);
            if (cardLeft + 320 > window.innerWidth) {
                cardLeft = Math.max(10, window.innerWidth - 330);
            }
            if (cardLeft < 10) {
                cardLeft = 10;
            }

            tourCard.style.top = (cardTop + window.scrollY) + 'px';
            tourCard.style.left = (cardLeft + window.scrollX) + 'px';

            document.getElementById('posTourPrevBtn').style.display = currentTourStep === 0 ? 'none' : 'block';
            document.getElementById('posTourNextBtn').innerText = currentTourStep === tourSteps.length - 1 
                ? (isArabicLocale ? 'إنهاء الجولة 🎉' : 'Finish Tour 🎉') 
                : (isArabicLocale ? 'التالي ⬅' : 'Next Step →');
        }

        function nextPOSTourStep() {
            if (currentTourStep < tourSteps.length - 1) {
                currentTourStep++;
                renderTourStep();
            } else {
                endPOSTour();
            }
        }

        function prevPOSTourStep() {
            if (currentTourStep > 0) {
                currentTourStep--;
                renderTourStep();
            }
        }

        function dismissFirstTimeBanner() {
            document.getElementById('firstTimeUserBanner').style.display = 'none';
            localStorage.setItem('bz_pos_banner_dismissed', 'true');
        }

        function togglePOSFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(() => {});
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            }
        }

        function showNotification(msg, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `alert alert-${type}`;
            toast.style.position = 'fixed';
            toast.style.bottom = '20px';
            toast.style.insetInlineEnd = '20px';
            toast.style.zIndex = '99999';
            toast.style.boxShadow = '0 10px 25px rgba(0,0,0,0.2)';
            toast.style.borderRadius = 'var(--radius-md)';
            toast.style.animation = 'posSlideDown 0.3s ease';
            toast.innerText = msg;
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // Initialize on Load
        document.addEventListener('DOMContentLoaded', () => {
            if (localStorage.getItem('bz_pos_banner_dismissed') === 'true') {
                const banner = document.getElementById('firstTimeUserBanner');
                if (banner) banner.style.display = 'none';
            }

            checkHeldCartOnLoad();

            // Pre-select first product if any into cart for instant ready-to-test capability
            const initialProducts = @json($products);
            if (initialProducts && initialProducts.length > 0) {
                const first = initialProducts[0];
                if (first.stock_offline > 0) {
                    addToCart({
                        id: first.id,
                        name: '{{ $isAr ? ($products[0]->name_ar ?? $products[0]->name_en) : $products[0]->name_en }}',
                        variant: 'Standard Pack (60 Caps)',
                        price: parseFloat(first.price),
                        stock: parseInt(first.stock_offline),
                        img: '{{ asset($products[0]->image ?? "assets/products/blue-mind.jpg") }}',
                        sku: first.sku,
                        qty: 1
                    });
                }
            }
        });
    </script>
</x-layouts.admin>
