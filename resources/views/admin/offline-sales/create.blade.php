<x-layouts.admin 
    :pageTitle="__('admin.pos.title')" 
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
                </div>
            </div>

            <!-- Quick Tap Product Cards -->
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1rem;">
                @foreach($products as $p)
                    <div class="card card-hover-lift" style="padding: 1rem; cursor: pointer; text-align: center; border-radius: var(--radius-lg);" onclick="alert('Added {{ $p['name_en'] }} to POS cart!')">
                        <img src="{{ asset($p['image']) }}" alt="{{ $p['name_en'] }}" style="width: 70px; height: 70px; margin: 0 auto 0.5rem auto; object-fit: cover; border-radius: var(--radius-sm); background: var(--color-bg-subtle);" onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';">
                        <div class="font-bold text-xs" style="margin-bottom: 0.25rem;">{{ $p['name_en'] }}</div>
                        <div class="font-black text-sm text-primary">${{ number_format($p['price'], 2) }}</div>
                        <div class="text-xs text-muted" style="margin-top: 0.25rem;">
                            Stock: <strong>{{ $p['stock_offline'] }}</strong> units
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Right: POS Register Cart & Tender Processing -->
        <div class="card" style="padding: 1.75rem; display: flex; flex-direction: column; height: fit-content; gap: 1.25rem;">
            <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;">
                <h3 style="font-size: 1.15rem; font-weight: 800; margin: 0;">{{ __('admin.pos.cart_summary') }}</h3>
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
</x-layouts.admin>
