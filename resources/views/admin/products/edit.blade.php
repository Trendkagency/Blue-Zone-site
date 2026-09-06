<x-layouts.admin 
    :pageTitle="'Edit: ' . $product['name_en']" 
    pageSubtitle="Update formulation metadata, multi-lingual translations, variants, and stock thresholds."
    :breadcrumbs="['Products' => route('admin.products.index'), $product['name_en'] => route('admin.products.edit', $product['id'])]"
>
    <x-slot name="actions">
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
            {{ __('app.actions.cancel') }}
        </a>
        <button type="button" class="btn btn-primary" onclick="alert('Formulation changes updated successfully!')">
            💾 {{ __('app.actions.save') }}
        </button>
    </x-slot>

    <!-- Form Section Tabs -->
    <div class="product-tabs-nav" data-tab-group="admin-product-edit" style="margin-bottom: 2rem;">
        <button type="button" class="tab-btn active" data-tab-target="tab-basic">
            1. Core & Categorization
        </button>
        <button type="button" class="tab-btn" data-tab-target="tab-content">
            2. Multi-Lingual Content (EN/AR)
        </button>
        <button type="button" class="tab-btn" data-tab-target="tab-variants">
            3. Variants & Pricing
        </button>
        <button type="button" class="tab-btn tab-professional" data-tab-target="tab-professional">
            🩺 4. Professional Section
        </button>
    </div>

    <!-- Tab 1: Core -->
    <div id="tab-basic" data-tab-content="admin-product-edit" style="display: block;">
        <div class="card" style="padding: 2rem;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <x-forms.input name="sku" label="SKU" :value="$product['sku']" required />
                <x-forms.input name="barcode" label="GTIN / Barcode" :value="$product['barcode']" required />
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem; margin-top: 1rem;">
                <x-forms.input name="brand" label="Brand" :value="$product['brand']" required />
                <x-forms.input name="price" label="Base Price ($)" type="number" step="0.01" :value="$product['price']" required />
                <x-forms.input name="sale_price" label="Sale Price ($)" type="number" step="0.01" :value="$product['sale_price'] ?? ''" />
            </div>
        </div>
    </div>

    <!-- Tab 2: Content -->
    <div id="tab-content" data-tab-content="admin-product-edit" style="display: none;">
        <div class="card" style="padding: 2rem;">
            <!-- English -->
            <div style="background: var(--color-bg-subtle); padding: 1.5rem; border-radius: var(--radius-md); margin-bottom: 2rem;">
                <h4 style="font-size: 1.05rem; font-weight: 700; color: var(--color-primary); margin-bottom: 1rem;">English (LTR)</h4>
                <x-forms.input name="name_en" label="Product Name (EN)" :value="$product['name_en']" required />
                <x-forms.input name="tagline_en" label="Tagline (EN)" :value="$product['tagline_en']" required />
                <x-forms.textarea name="description_en" label="Description (EN)" rows="4" :value="$product['description_en']" required />
            </div>

            <!-- Arabic -->
            <div style="background: var(--color-bg-subtle); padding: 1.5rem; border-radius: var(--radius-md);" dir="rtl">
                <h4 style="font-size: 1.05rem; font-weight: 700; color: var(--color-primary); margin-bottom: 1rem;">العربية (RTL)</h4>
                <x-forms.input name="name_ar" label="اسم المنتج بالعربية" :value="$product['name_ar']" required />
                <x-forms.input name="tagline_ar" label="الوصف المختصر بالعربية" :value="$product['tagline_ar']" required />
                <x-forms.textarea name="description_ar" label="الوصف التفصيلي بالعربية" rows="4" :value="$product['description_ar']" required />
            </div>
        </div>
    </div>

    <!-- Tab 3: Variants -->
    <div id="tab-variants" data-tab-content="admin-product-edit" style="display: none;">
        <div class="card" style="padding: 2rem;">
            <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 1.5rem;">Variants for {{ $product['name_en'] }}</h3>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Variant Name (EN)</th>
                            <th>SKU</th>
                            <th>Price ($)</th>
                            <th>Online Stock</th>
                            <th>Offline Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($product['variants'] as $v)
                            <tr>
                                <td class="font-bold">{{ $v['name_en'] }}</td>
                                <td class="font-mono text-xs">{{ $v['sku'] }}</td>
                                <td class="font-bold">${{ number_format($v['price'], 2) }}</td>
                                <td><span class="badge badge-success">{{ $v['stock_online'] }} units</span></td>
                                <td><span class="badge badge-neutral">{{ $v['stock_offline'] }} units</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tab 4: Professional -->
    <div id="tab-professional" data-tab-content="admin-product-edit" style="display: none;">
        <div class="card" style="padding: 2rem; border-color: rgba(2, 132, 199, 0.4);">
            <div class="professional-notice-box">
                <span class="professional-badge">Practitioner Only</span>
                <span class="text-sm font-semibold" style="color: #0369A1;">
                    Technical pharmacological data entered here will strictly render inside the verified Clinical Professional tab.
                </span>
            </div>

            <x-forms.textarea 
                name="clinical_mechanism" 
                label="Biochemical Mechanism of Action" 
                rows="3" 
                :value="$product['professional_info']['clinical_mechanism'] ?? ''" 
            />

            <x-forms.textarea 
                name="contraindications" 
                label="Clinical Contraindications & Interactions" 
                rows="3" 
                :value="$product['professional_info']['contraindications'] ?? ''" 
            />
        </div>
    </div>
</x-layouts.admin>
