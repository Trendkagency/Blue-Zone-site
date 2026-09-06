<x-layouts.admin 
    :pageTitle="__('admin.pos.title')" 
<<<<<<< HEAD
    pageSubtitle="High-speed boutique counter register interface with instant stock validation."
    :breadcrumbs="['Offline Sales' => route('admin.offline-sales.index'), 'Register' => route('admin.offline-sales.create')]"
>
    <div style="display: grid; grid-template-columns: 1fr 420px; gap: 2rem;">
        <!-- Left: Product Catalog & Barcode Scanner -->
        <div>
            <!-- Barcode & Search Input -->
            <div class="card" style="padding: 1.25rem; margin-bottom: 1.5rem;">
                <div class="search-wrapper">
                    <svg class="search-icon" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                    </svg>
                    <input type="text" class="form-control search-input" placeholder="{{ __('admin.pos.scan_sku') }}" autofocus>
=======
    :pageSubtitle="app()->getLocale() == 'ar' ? 'واجهة كاشير المعرض السريعة مع التحقق المباشر من توفر المخزون وخصم الكميات فورياً.' : 'High-speed boutique counter register interface with instant offline stock validation and real-time ledger deduction.'"
    :breadcrumbs="[__('admin.menu.offline_sales') => route('admin.offline-sales.index'), __('admin.pos.title') => route('admin.offline-sales.create')]"
>
    @if($errors->has('sale_error'))
        <div class="alert alert-danger" style="margin-bottom: 1.5rem;">
            <i class="fa-solid fa-triangle-exclamation mr-1.5 ml-1.5 text-danger"></i> {{ $errors->first('sale_error') }}
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 1fr 440px; gap: 2rem;">
        <!-- Left: Product Catalog & Quick Selection -->
        <div>
            <!-- Header search / filter -->
            <div class="card" style="padding: 1.25rem; margin-bottom: 1.5rem;">
                <div class="search-wrapper">
                    <svg class="search-icon" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" id="posSearch" class="form-control search-input" placeholder="{{ __('admin.pos.scan_sku') }}" oninput="filterPOS(this.value)">
>>>>>>> origin/main
                </div>
            </div>

            <!-- Quick Tap Product Cards -->
<<<<<<< HEAD
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1rem;">
                @foreach($products as $p)
                    <div class="card card-hover-lift" style="padding: 1rem; cursor: pointer; text-align: center; border-radius: var(--radius-lg);" onclick="alert('Added {{ $p['name_en'] }} to POS cart!')">
                        <img src="{{ asset($p['image']) }}" alt="{{ $p['name_en'] }}" style="width: 70px; height: 70px; margin: 0 auto 0.5rem auto; object-fit: cover; border-radius: var(--radius-sm); background: var(--color-bg-subtle);" onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';">
                        <div class="font-bold text-xs" style="margin-bottom: 0.25rem;">{{ $p['name_en'] }}</div>
                        <div class="font-black text-sm text-primary">${{ number_format($p['price'], 2) }}</div>
                        <div class="text-xs text-muted" style="margin-top: 0.25rem;">
                            Stock: <strong>{{ $p['stock_offline'] }}</strong> units
=======
            <div id="productGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem;">
                @foreach($products as $p)
                    @php
                        $pId = $p->id;
                        $pNameEn = $p->name_en ?? $p->sku;
                        $pNameAr = $p->name_ar ?? $pNameEn;
                        $pDisplay = app()->getLocale() == 'ar' ? $pNameAr : $pNameEn;
                        $pPrice = (float) $p->price;
                        $pStock = (int) $p->stock_offline;
                        $pImg = $p->image ?? 'assets/products/blue-mind.jpg';
                        $pSku = $p->sku ?? 'BZ-SKU';
                        $pVariant = 'Standard Pack (60 Caps)';
                    @endphp
                    <div class="card card-hover-lift pos-card" 
                         id="card-prod-{{ $pId }}"
                         style="padding: 1.25rem; cursor: pointer; text-align: center; border-radius: var(--radius-lg); transition: all 0.2s ease; border: 2px solid transparent;" 
                         data-name="{{ strtolower($pNameEn . ' ' . $pNameAr) }}" 
                         data-sku="{{ strtolower($pSku) }}"
                         onclick="selectPOSProduct('{{ $pId }}', '{{ addslashes($pDisplay) }}', '{{ addslashes($pVariant) }}', {{ $pPrice }}, {{ $pStock }})">
                        <img src="{{ asset($pImg) }}" alt="{{ $pDisplay }}" style="width: 75px; height: 75px; margin: 0 auto 0.5rem auto; object-fit: cover; border-radius: var(--radius-sm); background: var(--color-bg-subtle);" onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';">
                        <div class="font-bold text-xs" style="margin-bottom: 0.25rem; height: 32px; overflow: hidden;">{{ $pDisplay }}</div>
                        <div class="font-black text-sm text-primary">${{ number_format($pPrice, 2) }}</div>
                        <div class="text-xs text-muted" style="margin-top: 0.25rem;">
                            {{ app()->getLocale() == 'ar' ? 'مخزون المعرض: ' : 'Boutique Stock: ' }}
                            <strong style="color: {{ $pStock <= 0 ? '#EF4444' : ($pStock < 5 ? '#F59E0B' : 'var(--color-success)') }};">
                                {{ $pStock }} {{ app()->getLocale() == 'ar' ? 'وحدة' : 'units' }}
                            </strong>
>>>>>>> origin/main
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Right: POS Register Cart & Tender Processing -->
        <div class="card" style="padding: 1.75rem; display: flex; flex-direction: column; height: fit-content; gap: 1.25rem;">
            <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;">
                <h3 style="font-size: 1.15rem; font-weight: 800; margin: 0;">{{ __('admin.pos.cart_summary') }}</h3>
<<<<<<< HEAD
                <span class="badge badge-accent">Boutique POS</span>
            </div>

            <!-- Customer Lookup -->
            <div>
                <label class="text-xs font-bold text-muted">{{ __('admin.pos.select_customer') }}</label>
                <select class="form-select text-sm" style="margin-top: 0.25rem;">
                    <option value="walk_in">👤 {{ __('admin.pos.walk_in') }}</option>
                    <option value="1">Dr. Zaid Al-Harbi (Platinum)</option>
                    <option value="2">Layla Bint Sultan (Gold)</option>
                </select>
            </div>

            <!-- Register Items -->
            <div style="display: flex; flex-direction: column; gap: 0.75rem; max-height: 220px; overflow-y: auto;">
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0; border-bottom: 1px solid var(--color-border);">
                    <div>
                        <div class="font-bold text-sm">BLUE MIND (60 Caps)</div>
                        <div class="text-xs text-muted">1 × $68.00</div>
                    </div>
                    <div class="font-bold text-sm">$68.00</div>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0; border-bottom: 1px solid var(--color-border);">
                    <div>
                        <div class="font-bold text-sm">BLUE CELL (60 Caps)</div>
                        <div class="text-xs text-muted">1 × $74.00</div>
                    </div>
                    <div class="font-bold text-sm">$74.00</div>
                </div>
            </div>

            <!-- Tender & Payment Selection -->
            <div>
                <label class="text-xs font-bold text-muted">{{ __('admin.pos.payment_method') }}</label>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; margin-top: 0.35rem;">
                    <button type="button" class="btn btn-outline btn-sm font-bold">💳 Mada / Card</button>
                    <button type="button" class="btn btn-secondary btn-sm font-bold">💵 Cash Tender</button>
                </div>
            </div>

            <!-- Totals -->
            <div style="display: flex; flex-direction: column; gap: 0.5rem; border-top: 1px solid var(--color-border); padding-top: 1rem;">
                <div class="summary-row">
                    <span>{{ __('admin.pos.subtotal') }}:</span>
                    <span class="font-bold">$142.00</span>
                </div>
                <div class="summary-row">
                    <span>{{ __('admin.pos.tax') }}:</span>
                    <span>$21.30</span>
                </div>
                <div class="summary-row total">
                    <span>{{ __('admin.pos.total_payable') }}:</span>
                    <span class="text-primary font-black">$163.30</span>
                </div>
            </div>

            <button type="button" class="btn btn-primary btn-lg" style="width: 100%;" onclick="alert('POS Transaction authorized! Receipt printing on POS Thermal Printer...')">
                🖨️ {{ __('admin.pos.complete_sale') }}
            </button>
        </div>
    </div>
=======
                <span class="badge badge-accent">{{ app()->getLocale() == 'ar' ? 'كاشير المعرض' : 'Boutique POS' }}</span>
            </div>

            <form action="{{ route('admin.offline-sales.store') }}" method="POST" id="posForm">
                @csrf
                <input type="hidden" name="product_id" id="selectedProductId" value="{{ $products[0]->id ?? 1 }}">

                <!-- Customer Lookup -->
                <div style="margin-bottom: 1rem;">
                    <label class="text-xs font-bold text-muted">{{ __('admin.pos.select_customer') }}</label>
                    <select name="customer_name" id="customerSelect" class="form-select text-sm" style="margin-top: 0.25rem; width: 100%;" onchange="toggleCustomCustomer(this.value)">
                        <option value="Walk-In Boutique VIP">👤 {{ __('admin.pos.walk_in') }}</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->name }}" data-phone="{{ $c->phone }}" data-email="{{ $c->email }}">{{ $c->name }} ({{ $c->tier ?? 'Member' }})</option>
                        @endforeach
                        <option value="__custom__">➕ {{ app()->getLocale() == 'ar' ? 'إدخال عميل جديد يدوي...' : 'Enter new customer details...' }}</option>
                    </select>

                    <div id="customCustomerFields" style="display: none; margin-top: 0.5rem; gap: 0.5rem; flex-direction: column;">
                        <input type="text" name="custom_customer_name" id="customName" class="form-control text-xs" placeholder="{{ app()->getLocale() == 'ar' ? 'اسم العميل' : 'Customer Full Name' }}">
                        <input type="text" name="customer_phone" id="customPhone" class="form-control text-xs" placeholder="{{ app()->getLocale() == 'ar' ? 'رقم الجوال' : 'Phone Number' }}">
                    </div>
                </div>

                <!-- Selected Item Details -->
                <div style="background: var(--color-bg-subtle); padding: 1.25rem; border-radius: var(--radius-md); margin-bottom: 1rem; border: 1px solid var(--color-border);">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span class="text-xs font-bold text-muted" style="text-transform: uppercase;">
                            {{ app()->getLocale() == 'ar' ? 'التركيبة المختارة' : 'Selected Formulation' }}
                        </span>
                        <span id="posStockIndicator" class="badge badge-success text-xs">Stock: 0</span>
                    </div>

                    <div id="selectedProdName" class="font-bold text-sm" style="margin-top: 0.35rem; color: var(--color-primary);">
                        @php
                            $firstProd = $products[0] ?? null;
                            $firstName = $firstProd ? (app()->getLocale() == 'ar' ? ($firstProd->name_ar ?? $firstProd->name_en) : $firstProd->name_en) : 'Formulation';
                        @endphp
                        {{ $firstName }}
                    </div>

                    <!-- Variant Field -->
                    <div style="margin-top: 0.75rem;">
                        <label class="text-xs text-muted font-semibold">{{ app()->getLocale() == 'ar' ? 'المواصفة / العبوة:' : 'Variant / Packaging:' }}</label>
                        <input type="text" name="variant" id="posVariant" value="Standard Pack (60 Caps)" class="form-control text-xs" style="margin-top: 0.2rem;">
                    </div>

                    <!-- Quantity & Price Row -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-top: 0.75rem;">
                        <div>
                            <label class="text-xs text-muted font-semibold">{{ app()->getLocale() == 'ar' ? 'سعر الوحدة ($):' : 'Unit Price ($):' }}</label>
                            <input type="number" step="0.01" name="unit_price" id="posUnitPrice" value="{{ $products[0]->price ?? 68 }}" class="form-control text-xs" oninput="recalcPOS()">
                        </div>
                        <div>
                            <label class="text-xs text-muted font-semibold">{{ app()->getLocale() == 'ar' ? 'الكمية المطلوبة:' : 'Quantity:' }}</label>
                            <input type="number" name="quantity" id="posQty" value="1" min="1" class="form-control text-xs text-center" oninput="recalcPOS()">
                        </div>
                    </div>

                    <!-- Stock Warning Box -->
                    <div id="stockExceededWarning" style="display: none; margin-top: 0.75rem; padding: 0.5rem; background: #FEE2E2; border-radius: var(--radius-sm); border: 1px solid #EF4444;" class="text-xs text-danger font-bold">
                        <i class="fa-solid fa-triangle-exclamation mr-1 ml-1"></i>
                        {{ app()->getLocale() == 'ar' ? 'تنبيه: الكمية المطلوبة تتجاوز رصيد المعرض المتوفر!' : 'Alert: Quantity exceeds boutique available stock!' }}
                    </div>
                </div>

                <!-- Discount Input -->
                <div style="margin-bottom: 1rem;">
                    <label class="text-xs font-bold text-muted">{{ app()->getLocale() == 'ar' ? 'الخصم الترويجي ($)' : 'Promotional Discount ($)' }}</label>
                    <input type="number" step="0.01" min="0" name="discount" id="posDiscount" value="0.00" class="form-control text-sm" style="margin-top: 0.25rem;" oninput="recalcPOS()">
                </div>

                <!-- Tender & Payment Selection -->
                <div style="margin-bottom: 1.25rem;">
                    <label class="text-xs font-bold text-muted">{{ __('admin.pos.payment_method') }}</label>
                    <select name="payment_method" class="form-select text-sm" style="margin-top: 0.25rem; width: 100%;">
                        <option value="Mada / Debit POS Terminal">💳 {{ app()->getLocale() == 'ar' ? 'مدى / بطاقة مصرفية (POS)' : 'Mada / Debit POS Terminal' }}</option>
                        <option value="Credit Card (Visa / Mastercard / Amex)">💳 {{ app()->getLocale() == 'ar' ? 'بطاقة ائتمانية (فيزا / ماستركارد)' : 'Credit Card (Visa / Mastercard / Amex)' }}</option>
                        <option value="Cash Tender">💵 {{ app()->getLocale() == 'ar' ? 'سداد نقدي' : 'Cash Tender' }}</option>
                        <option value="Apple Pay / Contactless">📱 {{ app()->getLocale() == 'ar' ? 'أبل باي / دفع بدون تلامس' : 'Apple Pay / NFC Contactless' }}</option>
                    </select>
                </div>

                <!-- Totals Breakdown -->
                <div style="display: flex; flex-direction: column; gap: 0.5rem; border-top: 1px solid var(--color-border); padding-top: 1rem; margin-bottom: 1.25rem;">
                    <div class="summary-row" style="display: flex; justify-content: space-between;">
                        <span>{{ __('admin.pos.subtotal') }}:</span>
                        <span class="font-bold" id="posSubtotal">$0.00</span>
                    </div>
                    <div class="summary-row" style="display: flex; justify-content: space-between; color: #10B981;">
                        <span>{{ app()->getLocale() == 'ar' ? 'قيمة الخصم:' : 'Discount Amount:' }}</span>
                        <span class="font-bold" id="posDiscountDisplay">-$0.00</span>
                    </div>
                    <div class="summary-row" style="display: flex; justify-content: space-between;">
                        <span>{{ __('admin.pos.tax') }} (15% VAT):</span>
                        <span id="posTax">$0.00</span>
                    </div>
                    <div class="summary-row total" style="display: flex; justify-content: space-between; border-top: 1px solid var(--color-border); padding-top: 0.5rem; font-size: 1.15rem;">
                        <span>{{ __('admin.pos.total_payable') }}:</span>
                        <span class="text-primary font-black" id="posGrandTotal">$0.00</span>
                    </div>
                </div>

                <button type="submit" id="posSubmitBtn" class="btn btn-primary btn-lg" style="width: 100%;">
                    <i class="fa-solid fa-receipt mr-1.5 ml-1.5"></i> {{ __('admin.pos.complete_sale') }}
                </button>
            </form>
        </div>
    </div>

    <script>
        let currentAvailableStock = {{ $products[0]->stock_offline ?? 20 }};

        function selectPOSProduct(id, name, variant, price, stock) {
            document.getElementById('selectedProductId').value = id;
            document.getElementById('selectedProdName').innerText = name;
            document.getElementById('posVariant').value = variant;
            document.getElementById('posUnitPrice').value = parseFloat(price).toFixed(2);
            
            currentAvailableStock = parseInt(stock);
            const stockBadge = document.getElementById('posStockIndicator');
            stockBadge.innerText = 'Boutique Stock: ' + currentAvailableStock + ' units';
            if (currentAvailableStock <= 0) {
                stockBadge.className = 'badge badge-danger text-xs';
            } else if (currentAvailableStock < 5) {
                stockBadge.className = 'badge badge-warning text-xs';
            } else {
                stockBadge.className = 'badge badge-success text-xs';
            }

            // Highlight selected card visually
            document.querySelectorAll('.pos-card').forEach(c => c.style.borderColor = 'transparent');
            const activeCard = document.getElementById('card-prod-' + id);
            if (activeCard) {
                activeCard.style.borderColor = 'var(--color-primary)';
            }

            recalcPOS();
        }

        function recalcPOS() {
            const unitPrice = parseFloat(document.getElementById('posUnitPrice').value) || 0;
            const qty = parseInt(document.getElementById('posQty').value) || 1;
            const discount = parseFloat(document.getElementById('posDiscount').value) || 0;

            const subtotal = unitPrice * qty;
            const discountedSubtotal = Math.max(0, subtotal - discount);
            const tax = discountedSubtotal * 0.15;
            const total = discountedSubtotal + tax;

            document.getElementById('posSubtotal').innerText = '$' + subtotal.toFixed(2);
            document.getElementById('posDiscountDisplay').innerText = '-$' + discount.toFixed(2);
            document.getElementById('posTax').innerText = '$' + tax.toFixed(2);
            document.getElementById('posGrandTotal').innerText = '$' + total.toFixed(2);

            const warningBox = document.getElementById('stockExceededWarning');
            const submitBtn = document.getElementById('posSubmitBtn');

            if (qty > currentAvailableStock) {
                warningBox.style.display = 'block';
                submitBtn.disabled = true;
            } else {
                warningBox.style.display = 'none';
                submitBtn.disabled = false;
            }
        }

        function toggleCustomCustomer(val) {
            const customFields = document.getElementById('customCustomerFields');
            if (val === '__custom__') {
                customFields.style.display = 'flex';
            } else {
                customFields.style.display = 'none';
            }
        }

        function filterPOS(val) {
            const query = val.toLowerCase();
            document.querySelectorAll('.pos-card').forEach(card => {
                const name = card.getAttribute('data-name') || '';
                const sku = card.getAttribute('data-sku') || '';
                if (name.includes(query) || sku.includes(query)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // Initialize on load
        document.addEventListener('DOMContentLoaded', () => {
            const first = @json($products[0] ?? null);
            if (first) {
                selectPOSProduct(first.id, first.name_en, 'Standard Pack (60 Caps)', first.price, first.stock_offline);
            }
        });
    </script>
>>>>>>> origin/main
</x-layouts.admin>
