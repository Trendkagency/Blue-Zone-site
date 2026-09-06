<x-layouts.admin 
    :pageTitle="__('admin.menu.add_product')" 
    pageSubtitle="Register a new bioceutical formulation into the centralized product catalog."
    :breadcrumbs="['Products' => route('admin.products.index'), 'Create' => route('admin.products.create')]"
>
    <x-slot name="actions">
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
            {{ __('app.actions.cancel') }}
        </a>
        <button type="button" class="btn btn-primary" onclick="alert('Formulation draft saved successfully!')">
            💾 {{ __('app.actions.save') }}
        </button>
    </x-slot>

    <!-- Form Section Tabs -->
    <div class="product-tabs-nav" data-tab-group="admin-product-form" style="margin-bottom: 2rem;">
        <button type="button" class="tab-btn active" data-tab-target="tab-basic">
            1. Core & Categorization
        </button>
        <button type="button" class="tab-btn" data-tab-target="tab-content">
            2. Multi-Lingual Content (EN/AR)
        </button>
        <button type="button" class="tab-btn" data-tab-target="tab-variants">
            3. Variants & Pricing
        </button>
        <button type="button" class="tab-btn" data-tab-target="tab-media">
            4. Media & Assets
        </button>
        <button type="button" class="tab-btn tab-professional" data-tab-target="tab-professional">
            🩺 5. Professional Section
        </button>
        <button type="button" class="tab-btn" data-tab-target="tab-inventory">
            6. Inventory Thresholds
        </button>
    </div>

    <!-- Tab 1: Core & Categorization -->
    <div id="tab-basic" data-tab-content="admin-product-form" style="display: block;">
        <div class="card" style="padding: 2rem;">
            <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;">
                Core Identifiers & Classification
            </h3>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <x-forms.input name="sku" label="SKU (Stock Keeping Unit)" placeholder="e.g. BZ-MND-001" required />
                <x-forms.input name="barcode" label="GTIN / Barcode" placeholder="e.g. 628100091001" required />
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem;">
                <x-forms.select 
                    name="category_id" 
                    label="Primary Health System" 
                    :options="[
                        '1' => 'Cognitive & Brain Health',
                        '2' => 'Cellular Longevity',
                        '3' => 'Immunity & Resilience',
                        '4' => 'Metabolic Health',
                        '5' => 'Sleep & Recovery',
                        '6' => 'Cardiovascular Longevity',
                    ]" 
                    required 
                />

                <x-forms.select 
                    name="subcategory_id" 
                    label="Subcategory" 
                    :options="[
                        '11' => 'Nootropics',
                        '12' => 'Phospholipids',
                        '21' => 'NAD+ Boosters',
                        '31' => 'Polyphenols',
                    ]" 
                />

                <x-forms.input name="brand" label="Laboratory Brand" value="Blue Zone Bioceuticals" required />
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem;">
                <x-forms.input name="target_gender" label="Target Demographic" value="Unisex" />
                <x-forms.input name="age_group" label="Age Cohort" value="18+" />
                <x-forms.input name="product_size" label="Dosage Form" value="60 Vegetable Capsules" />
            </div>
        </div>
    </div>

    <!-- Tab 2: Multi-Lingual Content -->
    <div id="tab-content" data-tab-content="admin-product-form" style="display: none;">
        <div class="card" style="padding: 2rem;">
            <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;">
                Multi-Lingual Customer Descriptions
            </h3>

            <!-- English Fields -->
            <div style="background: var(--color-bg-subtle); padding: 1.5rem; border-radius: var(--radius-md); margin-bottom: 2rem;">
                <h4 style="font-size: 1.05rem; font-weight: 700; color: var(--color-primary); margin-bottom: 1rem;">English (LTR)</h4>
                <x-forms.input name="name_en" label="Product Name (EN)" placeholder="e.g. BLUE MIND" required />
                <x-forms.input name="tagline_en" label="Tagline / Short Summary (EN)" placeholder="e.g. Daily Cognitive & Nootropic Support" required />
                <x-forms.textarea name="description_en" label="Full Customer Description (EN)" rows="4" required />
                <x-forms.textarea name="usage_en" label="Administration & Dosage Instructions (EN)" rows="2" />
            </div>

            <!-- Arabic Fields -->
            <div style="background: var(--color-bg-subtle); padding: 1.5rem; border-radius: var(--radius-md);" dir="rtl">
                <h4 style="font-size: 1.05rem; font-weight: 700; color: var(--color-primary); margin-bottom: 1rem;">العربية (RTL)</h4>
                <x-forms.input name="name_ar" label="اسم المنتج بالعربية" placeholder="مثال: بلو مايند" required />
                <x-forms.input name="tagline_ar" label="الوصف المختصر بالعربية" placeholder="مثال: دعم إدراكي وتركيز عصبي يومي متطور" required />
                <x-forms.textarea name="description_ar" label="الوصف التفصيلي للعميل بالعربية" rows="4" required />
                <x-forms.textarea name="usage_ar" label="طريقة الاستخدام والجرعات بالعربية" rows="2" />
            </div>
        </div>
    </div>

    <!-- Tab 3: Variants & Pricing -->
    <div id="tab-variants" data-tab-content="admin-product-form" style="display: none;">
        <div class="card" style="padding: 2rem;">
            <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;">
                Base Pricing & Multi-Variant Options
            </h3>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
                <x-forms.input name="price" label="Base Retail Price ($)" type="number" step="0.01" value="68.00" required />
                <x-forms.input name="sale_price" label="Sale / Promotional Price ($)" type="number" step="0.01" value="58.00" />
                <x-forms.input name="cost_price" label="Manufacturing Unit Cost ($)" type="number" step="0.01" value="24.50" />
            </div>

            <h4 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 1rem;">Product Variants Matrix</h4>
            <div class="table-responsive" style="border: 1px solid var(--color-border); margin-bottom: 1rem;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Variant Name (EN)</th>
                            <th>Variant Name (AR)</th>
                            <th>Variant SKU</th>
                            <th>Price ($)</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="text" class="form-control text-sm" value="Standard 30-Day Protocol (60 Capsules)"></td>
                            <td><input type="text" class="form-control text-sm" value="بروتوكول 30 يوماً القياسي (60 كبسولة)" dir="rtl"></td>
                            <td><input type="text" class="form-control text-sm font-mono" value="BZ-MND-60C"></td>
                            <td><input type="number" class="form-control text-sm" value="68.00" step="0.01"></td>
                            <td><button type="button" class="btn btn-ghost btn-sm text-danger">✕</button></td>
                        </tr>
                        <tr>
                            <td><input type="text" class="form-control text-sm" value="Longevity 90-Day Protocol (180 Capsules)"></td>
                            <td><input type="text" class="form-control text-sm" value="بروتوكول طول العمر 90 يوماً (180 كبسولة)" dir="rtl"></td>
                            <td><input type="text" class="form-control text-sm font-mono" value="BZ-MND-180C"></td>
                            <td><input type="number" class="form-control text-sm" value="174.00" step="0.01"></td>
                            <td><button type="button" class="btn btn-ghost btn-sm text-danger">✕</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <button type="button" class="btn btn-secondary btn-sm">+ Add Another Variant</button>
        </div>
    </div>

    <!-- Tab 4: Media & Assets -->
    <div id="tab-media" data-tab-content="admin-product-form" style="display: none;">
        <div class="card" style="padding: 2rem;">
            <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;">
                Formulation Imagery & Laboratory Proofs
            </h3>

            <div style="border: 2px dashed var(--color-border); border-radius: var(--radius-xl); padding: 3rem; text-align: center; background: var(--color-bg-subtle); margin-bottom: 2rem;">
                <div style="font-size: 2.5rem; margin-bottom: 1rem;">📷</div>
                <h4 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 0.5rem;">Drag & drop bottle renders or click to browse</h4>
                <p class="text-xs text-muted">Supports WebP, PNG, JPG up to 10MB each (High-resolution square aspect ratio recommended).</p>
                <button type="button" class="btn btn-secondary btn-sm" style="margin-top: 1rem;">Select Files</button>
            </div>
        </div>
    </div>

    <!-- Tab 5: Healthcare Professional Section -->
    <div id="tab-professional" data-tab-content="admin-product-form" style="display: none;">
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
                placeholder="Detail cellular pathways, neurotransmitter modulation, and enzymatic receptors..." 
            />

            <x-forms.textarea 
                name="formula_details" 
                label="Standardized Extraction Assay Details" 
                rows="3" 
                placeholder="Exact active bio-marker percentages and extraction solvent ratios..." 
            />

            <x-forms.textarea 
                name="contraindications" 
                label="Clinical Contraindications & Pharmaceutical Interactions" 
                rows="3" 
                placeholder="Known antagonism with anticoagulants, MAO inhibitors, etc..." 
            />

            <x-forms.textarea 
                name="warnings" 
                label="Specialist Precautions & Storage Specifications" 
                rows="2" 
                placeholder="Preservation temperatures, pediatric exclusion parameters..." 
            />
        </div>
    </div>

    <!-- Tab 6: Inventory & Thresholds -->
    <div id="tab-inventory" data-tab-content="admin-product-form" style="display: none;">
        <div class="card" style="padding: 2rem;">
            <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;">
                Inventory Thresholds & Location Allocation
            </h3>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem;">
                <x-forms.input name="stock_online" label="Initial Online Stock" type="number" value="100" required />
                <x-forms.input name="stock_offline" label="Initial Flagship POS Stock" type="number" value="30" required />
                <x-forms.input name="low_stock_threshold" label="Low Stock Warning Threshold" type="number" value="15" required />
            </div>

            <x-forms.toggle 
                name="enable_backorders" 
                label="Allow Pre-orders when depleted" 
                description="If enabled, customers may reserve items while a new laboratory batch is undergoing HPLC assay." 
            />
        </div>
    </div>
</x-layouts.admin>
