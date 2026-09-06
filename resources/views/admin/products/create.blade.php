<x-layouts.admin 
<<<<<<< HEAD
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
=======
    :pageTitle="__('admin.products.create_title')" 
    :pageSubtitle="__('admin.products.create_subtitle')"
    :breadcrumbs="[__('admin.menu.products') => route('admin.products.index'), __('app.actions.create') => route('admin.products.create')]"
>
    <!-- Server-Side Error Alert (if any) -->
    @if ($errors->any())
        <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid var(--color-danger); color: var(--color-danger); padding: 1.25rem 1.5rem; border-radius: var(--radius-md); margin-bottom: 2rem;">
            <div style="font-weight: 800; font-size: 1rem; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                <span>⚠️</span>
                <span>{{ app()->getLocale() === 'ar' ? 'يرجى مراجعة الحقول المطلوبة التالية وتصحيحها:' : 'Please correct the following validation errors:' }}</span>
            </div>
            <ul style="margin: 0; padding-inline-start: 1.5rem; font-size: 0.875rem; line-height: 1.6;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Multi-Step Wizard Navigation Indicator -->
    <div class="product-wizard-steps" style="display: flex; gap: 0.75rem; overflow-x: auto; padding-bottom: 1rem; margin-bottom: 2rem; border-bottom: 1px solid var(--color-border);">
        <div class="wizard-step active" data-step="1" onclick="goToStep(1)">
            <div class="step-num">1</div>
            <div class="step-info">
                <span class="step-title">{{ __('admin.products.tabs.core') }}</span>
                <span class="step-sub">{{ app()->getLocale() == 'ar' ? 'الرمز والتصنيف' : 'SKU & Category' }}</span>
            </div>
        </div>

        <div class="wizard-step" data-step="2" onclick="goToStep(2)">
            <div class="step-num">2</div>
            <div class="step-info">
                <span class="step-title">{{ __('admin.products.tabs.content') }}</span>
                <span class="step-sub">{{ app()->getLocale() == 'ar' ? 'المحتوى ثنائي اللغة' : 'EN & AR Content' }}</span>
            </div>
        </div>

        <div class="wizard-step" data-step="3" onclick="goToStep(3)">
            <div class="step-num">3</div>
            <div class="step-info">
                <span class="step-title">{{ __('admin.products.tabs.pricing') }}</span>
                <span class="step-sub">{{ app()->getLocale() == 'ar' ? 'الأسعار والضرائب' : 'Pricing & Taxes' }}</span>
            </div>
        </div>

        <div class="wizard-step" data-step="4" onclick="goToStep(4)">
            <div class="step-num">4</div>
            <div class="step-info">
                <span class="step-title">{{ __('admin.products.tabs.media') }}</span>
                <span class="step-sub">{{ app()->getLocale() == 'ar' ? 'الصور والوسائط' : 'Assets & Media' }}</span>
            </div>
        </div>

        <div class="wizard-step" data-step="5" onclick="goToStep(5)">
            <div class="step-num">5</div>
            <div class="step-info">
                <span class="step-title">{{ __('admin.products.tabs.clinical') }}</span>
                <span class="step-sub">{{ app()->getLocale() == 'ar' ? 'الأبحاث والجرعات' : 'Clinical Data' }}</span>
            </div>
        </div>

        <div class="wizard-step" data-step="6" onclick="goToStep(6)">
            <div class="step-num">6</div>
            <div class="step-info">
                <span class="step-title">{{ __('admin.products.tabs.inventory') }}</span>
                <span class="step-sub">{{ app()->getLocale() == 'ar' ? 'المخزون والمراجعة' : 'Stock & Review' }}</span>
>>>>>>> origin/main
            </div>
        </div>
    </div>

<<<<<<< HEAD
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
=======
    <!-- Main Creation Form -->
    <form method="POST" action="{{ route('admin.products.store') }}" id="productCreationForm" enctype="multipart/form-data">
        @csrf

        <!-- Top Actions Bar -->
        <x-slot name="actions">
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                {{ __('app.actions.cancel') }}
            </a>
            <button type="button" class="btn btn-secondary" id="btnPrevStep" onclick="navigateStep(-1)" style="display: none;">
                <i class="fa-solid fa-arrow-left mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'الخطوة السابقة' : 'Previous Step' }}
            </button>
            <button type="button" class="btn btn-primary" id="btnNextStep" onclick="navigateStep(1)">
                {{ app()->getLocale() === 'ar' ? 'الخطوة التالية' : 'Next Step' }} <i class="fa-solid fa-arrow-right mr-1.5 ml-1.5"></i>
            </button>
            <button type="submit" class="btn btn-primary" id="btnSubmitForm" style="display: none;">
                <i class="fa-solid fa-floppy-disk mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'حفظ وإدراج التركيبة' : 'Save & Publish Formulation' }}
            </button>
        </x-slot>

        <!-- STEP 1: Core Identifiers & Biological Classification -->
        <div class="wizard-step-pane" id="step-pane-1" style="display: block;">
            <div class="card" style="padding: 2.25rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;">
                    <div>
                        <h3 style="font-size: 1.25rem; font-weight: 800; margin: 0 0 0.25rem 0;">
                            {{ __('admin.products.sections.core_info') }}
                        </h3>
                        <p class="text-xs text-muted" style="margin: 0;">
                            {{ app()->getLocale() === 'ar' ? 'الخطوة 1 من 6: تحديد المعرفات الأساسية والنظام الحيوي' : 'Step 1 of 6: Establish global SKU, Barcode, and biological health system' }}
                        </p>
                    </div>
                    <span class="badge badge-accent">{{ app()->getLocale() === 'ar' ? 'خطوة 1 / 6' : 'Step 1 / 6' }}</span>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div>
                        <x-forms.input 
                            name="sku" 
                            :label="__('admin.products.fields.sku')" 
                            :placeholder="__('admin.products.placeholders.sku')" 
                            :value="old('sku', 'BZ-MND-001')" 
                            required 
                        />
                    </div>
                    <div>
                        <x-forms.input 
                            name="barcode" 
                            :label="__('admin.products.fields.barcode')" 
                            :placeholder="__('admin.products.placeholders.barcode')" 
                            :value="old('barcode', '628100091001')" 
                        />
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem; margin-top: 1rem;">
                    <div>
                        <x-forms.select 
                            name="category_id" 
                            :label="__('admin.products.fields.primary_system')" 
                            :options="collect($categories)->mapWithKeys(function($c) {
                                return [$c['id'] => (app()->getLocale() === 'ar' && !empty($c['name_ar']) ? $c['name_ar'] : $c['name_en'])];
                            })->toArray()" 
                            :selected="old('category_id')"
                            required 
                        />
                    </div>
                    <div>
                        <x-forms.input 
                            name="subcategory_en" 
                            :label="__('admin.products.fields.subcategory') . ' (EN)'" 
                            placeholder="e.g. Nootropics" 
                            :value="old('subcategory_en', 'Nootropics & Cognitive')" 
                        />
                    </div>
                    <div>
                        <x-forms.input 
                            name="brand" 
                            :label="__('admin.products.fields.brand')" 
                            :value="old('brand', 'Blue Zone Bioceuticals')" 
                            required 
                        />
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem; margin-top: 1rem;">
                    <div>
                        <x-forms.input 
                            name="target_gender" 
                            :label="__('admin.products.fields.target_demographic')" 
                            :value="old('target_gender', 'Unisex')" 
                        />
                    </div>
                    <div>
                        <x-forms.input 
                            name="age_group" 
                            :label="__('admin.products.fields.age_cohort')" 
                            :value="old('age_group', '18+')" 
                        />
                    </div>
                    <div>
                        <x-forms.input 
                            name="product_size" 
                            :label="__('admin.products.fields.dosage_form')" 
                            :value="old('product_size', '60 Vegetable Capsules')" 
                        />
                    </div>
                </div>

                <div style="margin-top: 1.5rem; border-top: 1px solid var(--color-border); padding-top: 1rem;">
                    <x-forms.input 
                        name="slug" 
                        label="URL Slug" 
                        placeholder="e.g. blue-mind-precision-nootropic" 
                        :value="old('slug', 'blue-mind-precision-nootropic')" 
                        hint="Unique URL path for storefront routing." 
                    />
                </div>
            </div>
        </div>

        <!-- STEP 2: Multi-Lingual Content (EN/AR) -->
        <div class="wizard-step-pane" id="step-pane-2" style="display: none;">
            <div class="card" style="padding: 2.25rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;">
                    <div>
                        <h3 style="font-size: 1.25rem; font-weight: 800; margin: 0 0 0.25rem 0;">
                            {{ __('admin.products.sections.bilingual_desc') }}
                        </h3>
                        <p class="text-xs text-muted" style="margin: 0;">
                            {{ app()->getLocale() === 'ar' ? 'الخطوة 2 من 6: الأوصاف والمسميات باللغتين العربية والإنجليزية' : 'Step 2 of 6: Commercial and scientific storytelling in Arabic & English' }}
                        </p>
                    </div>
                    <span class="badge badge-accent">{{ app()->getLocale() === 'ar' ? 'خطوة 2 / 6' : 'Step 2 / 6' }}</span>
                </div>

                <!-- Arabic Content (RTL) -->
                <div style="background: var(--color-bg-subtle); padding: 1.75rem; border-radius: var(--radius-lg); border: 1px solid var(--color-border); margin-bottom: 2rem;" dir="rtl">
                    <h4 style="font-size: 1.1rem; font-weight: 800; color: var(--color-primary); margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                        <span>🇸🇦</span>
                        <span>{{ __('admin.products.sections.arabic_content') }}</span>
                    </h4>

                    <x-forms.input 
                        name="name_ar" 
                        :label="__('admin.products.fields.name_ar')" 
                        :placeholder="__('admin.products.placeholders.name_ar')" 
                        :value="old('name_ar', 'بلو مايند منشط ذهني دقيق')" 
                        required 
                    />

                    <div style="margin-top: 1rem;">
                        <x-forms.input 
                            name="tagline_ar" 
                            :label="__('admin.products.fields.tagline_ar')" 
                            :placeholder="__('admin.products.placeholders.tagline_ar')" 
                            :value="old('tagline_ar', 'دعم يومي للوظائف الإدراكية والتركيز الذهني')" 
                        />
                    </div>

                    <div style="margin-top: 1rem;">
                        <x-forms.textarea 
                            name="description_ar" 
                            :label="__('admin.products.fields.description_ar')" 
                            rows="4" 
                            :placeholder="__('admin.products.placeholders.description_ar')" 
                        >{{ old('description_ar', 'تركيبة حيوية متطورة مصممة لدعم النواقل العصبية والنشاط الذهني وتحفيز التركيز المستدام طوال اليوم.') }}</x-forms.textarea>
                    </div>

                    <div style="margin-top: 1rem;">
                        <x-forms.textarea 
                            name="usage_ar" 
                            :label="__('admin.products.fields.usage_ar')" 
                            rows="2" 
                            :placeholder="__('admin.products.placeholders.usage_ar')" 
                        >{{ old('usage_ar', 'تناول كبسولتين يومياً مع وجبة الإفطار أو عند الحاجة لتركيز ذهني مضاعف.') }}</x-forms.textarea>
                    </div>
                </div>

                <!-- English Content (LTR) -->
                <div style="background: var(--color-bg-subtle); padding: 1.75rem; border-radius: var(--radius-lg); border: 1px solid var(--color-border);" dir="ltr">
                    <h4 style="font-size: 1.1rem; font-weight: 800; color: var(--color-primary); margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                        <span>🇬🇧</span>
                        <span>{{ __('admin.products.sections.english_content') }}</span>
                    </h4>

                    <x-forms.input 
                        name="name_en" 
                        :label="__('admin.products.fields.name_en')" 
                        :placeholder="__('admin.products.placeholders.name_en')" 
                        :value="old('name_en', 'BLUE MIND Precision Nootropic')" 
                        required 
                    />

                    <div style="margin-top: 1rem;">
                        <x-forms.input 
                            name="tagline_en" 
                            :label="__('admin.products.fields.tagline_en')" 
                            :placeholder="__('admin.products.placeholders.tagline_en')" 
                            :value="old('tagline_en', 'Daily Cognitive & Nootropic Support')" 
                        />
                    </div>

                    <div style="margin-top: 1rem;">
                        <x-forms.textarea 
                            name="description_en" 
                            :label="__('admin.products.fields.description_en')" 
                            rows="4" 
                            :placeholder="__('admin.products.placeholders.description_en')" 
                        >{{ old('description_en', 'Engineered to support mental clarity, neurotransmitter synthesis, and sustained neural energy throughout the day without jitter or crash.') }}</x-forms.textarea>
                    </div>

                    <div style="margin-top: 1rem;">
                        <x-forms.textarea 
                            name="usage_en" 
                            :label="__('admin.products.fields.usage_en')" 
                            rows="2" 
                            :placeholder="__('admin.products.placeholders.usage_en')" 
                        >{{ old('usage_en', 'Take 2 capsules daily with your morning meal, or as recommended by your physician.') }}</x-forms.textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- STEP 3: Commercial Pricing, Margin & Tax Engine -->
        <div class="wizard-step-pane" id="step-pane-3" style="display: none;">
            <div class="card" style="padding: 2.25rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;">
                    <div>
                        <h3 style="font-size: 1.25rem; font-weight: 800; margin: 0 0 0.25rem 0;">
                            {{ __('admin.products.sections.pricing_structure') }}
                        </h3>
                        <p class="text-xs text-muted" style="margin: 0;">
                            {{ app()->getLocale() === 'ar' ? 'الخطوة 3 من 6: تكلفة الإنتاج، سعر البيع النهائي، واحتساب الضرائب وهوامش الربح' : 'Step 3 of 6: Cost margins, retail pricing, and real-time VAT calculation' }}
                        </p>
                    </div>
                    <span class="badge badge-accent">{{ app()->getLocale() === 'ar' ? 'خطوة 3 / 6' : 'Step 3 / 6' }}</span>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem;">
                    <div>
                        <x-forms.input 
                            name="cost_price" 
                            id="inputCostPrice"
                            type="number" 
                            step="0.01" 
                            min="0"
                            :label="__('admin.products.fields.cost_price')" 
                            :value="old('cost_price', '22.00')" 
                            required 
                            oninput="recalculateTaxAndMargin()"
                        />
                    </div>
                    <div>
                        <x-forms.input 
                            name="price" 
                            id="inputRetailPrice"
                            type="number" 
                            step="0.01" 
                            min="0.01"
                            :label="__('admin.products.fields.retail_price')" 
                            :value="old('price', '68.00')" 
                            required 
                            oninput="recalculateTaxAndMargin()"
                        />
                    </div>
                    <div>
                        <x-forms.input 
                            name="sale_price" 
                            id="inputSalePrice"
                            type="number" 
                            step="0.01" 
                            min="0"
                            :label="__('admin.products.fields.sale_price')" 
                            :value="old('sale_price')" 
                            hint="Optional promotional campaign price" 
                            oninput="recalculateTaxAndMargin()"
                        />
                    </div>
                </div>

                <!-- Live Dynamic Tax & Margin Calculator Breakdown Card -->
                <div style="margin-top: 2rem; background: var(--color-bg-subtle); padding: 1.5rem 2rem; border-radius: var(--radius-lg); border: 1px solid var(--color-border);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <h4 style="font-size: 1.05rem; font-weight: 800; color: var(--color-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                            <span>📊</span>
                            <span>{{ app()->getLocale() === 'ar' ? 'التحليل المالي المباشر والضرائب (ZATCA Engine)' : 'Live Tax Breakdown & Margin Analysis' }}</span>
                        </h4>
                        <span class="badge badge-success text-xs font-mono" id="badgeTaxRate">
                            VAT {{ $taxInfo['tax_rate'] }}%
                        </span>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1.5rem;">
                        <div class="stat-card" style="background: var(--color-bg-surface); padding: 1rem; border-radius: var(--radius-md);">
                            <div class="text-xs text-muted" style="margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'تكلفة الإنتاج' : 'Unit Cost' }}</div>
                            <div class="font-bold font-mono text-lg" id="displayCostPrice">${{ number_format($taxInfo['cost_price'], 2) }}</div>
                        </div>

                        <div class="stat-card" style="background: var(--color-bg-surface); padding: 1rem; border-radius: var(--radius-md);">
                            <div class="text-xs text-muted" style="margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'السعر الأساسي' : 'Net Base Price' }}</div>
                            <div class="font-bold font-mono text-lg text-primary" id="displayNetPrice">${{ number_format($taxInfo['net_price'], 2) }}</div>
                        </div>

                        <div class="stat-card" style="background: var(--color-bg-surface); padding: 1rem; border-radius: var(--radius-md);">
                            <div class="text-xs text-muted" style="margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'ضريبة القيمة المضافة (15%)' : 'VAT Tax Amount' }}</div>
                            <div class="font-bold font-mono text-lg text-warning" id="displayTaxAmount">${{ number_format($taxInfo['tax_amount'], 2) }}</div>
                        </div>

                        <div class="stat-card" style="background: var(--color-bg-surface); padding: 1rem; border-radius: var(--radius-md);">
                            <div class="text-xs text-muted" style="margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'السعر الإجمالي للعميل' : 'Gross Consumer Price' }}</div>
                            <div class="font-bold font-mono text-lg text-success" id="displayGrossPrice">${{ number_format($taxInfo['gross_price'], 2) }}</div>
                        </div>

                        <div class="stat-card" style="background: var(--color-bg-surface); padding: 1rem; border-radius: var(--radius-md);">
                            <div class="text-xs text-muted" style="margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'هامش الربح الصافي' : 'Net Margin' }}</div>
                            <div class="font-bold font-mono text-lg text-success" id="displayProfitMargin">
                                ${{ number_format($taxInfo['profit_margin'], 2) }} ({{ $taxInfo['profit_margin_percentage'] }}%)
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- STEP 4: Media & Packaging Assets -->
        <div class="wizard-step-pane" id="step-pane-4" style="display: none;">
            <div class="card" style="padding: 2.25rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;">
                    <div>
                        <h3 style="font-size: 1.25rem; font-weight: 800; margin: 0 0 0.25rem 0;">
                            {{ __('admin.products.sections.media_upload') }}
                        </h3>
                        <p class="text-xs text-muted" style="margin: 0;">
                            {{ app()->getLocale() === 'ar' ? 'الخطوة 4 من 6: الصورة الرئيسية ومعرض التغليف والشهادات الحيوية' : 'Step 4 of 6: Primary formulation render, gallery packaging assets, and clinical documents' }}
                        </p>
                    </div>
                    <span class="badge badge-accent">{{ app()->getLocale() === 'ar' ? 'خطوة 4 / 6' : 'Step 4 / 6' }}</span>
                </div>

                <div class="space-y-6">
                    <!-- Primary Formulation Render -->
                    <x-file-uploader 
                        name="primary_image" 
                        :label="app()->getLocale() === 'ar' ? 'الصورة الرئيسية للمنتج (Primary Hero Render)' : 'Primary Product Hero Image'"
                        :helper="app()->getLocale() === 'ar' ? 'ارفع صورة رئيسية بدقة عالية (WebP, PNG, JPG حتى 10MB)' : 'Upload high-resolution clinical hero render (WebP, PNG, JPG up to 10MB)'"
                        accept="image/*"
                        :maxSize="10"
                    />

                    <!-- Gallery & Packaging Assets -->
                    <x-file-uploader 
                        name="gallery" 
                        :label="app()->getLocale() === 'ar' ? 'معرض صور العبوة والتفاصيل (Gallery Assets)' : 'Product Gallery & Packaging Shots'"
                        :helper="app()->getLocale() === 'ar' ? 'يمكنك رفع عدة صور لزوايا مختلفة والمكونات (WebP, PNG, JPG)' : 'You can upload multiple high-res product angles and ingredients shots'"
                        accept="image/*"
                        :multiple="true"
                        :maxSize="10"
                    />

                    <!-- Fallback Direct Asset Path -->
                    <div style="margin-top: 1.5rem; background: var(--color-bg-subtle); padding: 1.25rem; border-radius: var(--radius-md); border: 1px dashed var(--color-border);">
                        <x-forms.input 
                            name="image" 
                            id="inputMainImage"
                            label="Default Asset Fallback Path" 
                            :value="old('image', 'assets/products/blue-mind.jpg')" 
                            placeholder="e.g. assets/products/blue-mind.jpg" 
                            hint="Used automatically when no Spatie media file is uploaded."
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- STEP 5: Our Science & Clinical Dossier -->
        <div class="wizard-step-pane" id="step-pane-5" style="display: none;">
            <div class="card" style="padding: 2.25rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;">
                    <div>
                        <h3 style="font-size: 1.25rem; font-weight: 800; margin: 0 0 0.25rem 0; display: flex; align-items: center; gap: 0.5rem;">
                            <span>🔬</span>
                            <span>{{ app()->getLocale() === 'ar' ? 'قسم أبحاث العلوم وطول العمر (Our Science & Clinical Data)' : 'Our Science & Clinical Research Dossier' }}</span>
                        </h3>
                        <p class="text-xs text-muted" style="margin: 0;">
                            {{ app()->getLocale() === 'ar' 
                               ? 'تحكم كامل ثنائي اللغة بمعلومات قسم أبحاث العلوم المعروضة في الصفحة الرئيسية وصفحة تفاصيل علوم المنتج' 
                               : 'Comprehensive bilingual control over the Our Science section on the homepage and the product science details page.' }}
                        </p>
                    </div>
                    <span class="badge badge-accent">{{ app()->getLocale() === 'ar' ? 'خطوة 5 / 6' : 'Step 5 / 6' }}</span>
                </div>

                <!-- 1. Scientific Dossier / Research Foundation (Bilingual) -->
                <div style="margin-bottom: 1.75rem;">
                    <div style="font-weight: 700; font-size: 0.95rem; margin-bottom: 0.75rem; color: var(--color-text-emphasis); display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fa-solid fa-dna" style="color: #67B34A;"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'أبحاث طول العمر والأساس العلمي (Scientific Longevity Foundation)' : 'Scientific Longevity Foundation & Research Dossier' }}</span>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <x-forms.textarea 
                            name="science_en" 
                            :label="__('admin.products.fields.science_en')" 
                            rows="4" 
                            :value="old('science_en')"
                            placeholder="e.g. Extensive pharmacological breakdown of the cellular pathway, human clinical trials, and mitochondrial bioenergetics..." 
                            hint="Feeds directly into the Our Science section on the homepage and /our-science/{slug} page."
                        />

                        <x-forms.textarea 
                            name="science_ar" 
                            :label="__('admin.products.fields.science_ar')" 
                            rows="4" 
                            :value="old('science_ar')"
                            placeholder="مثال: تفصيل سريري للمسارات الحيوية، التجارب البشرية المنشورة، وميكانيكية عمل المركبات على المستوى الخلوي..." 
                            hint="يتم عرضه مباشرة في قسم Our Science بالصفحة الرئيسية وصفحة تفاصيل علوم المنتج."
                        />
                    </div>
                </div>

                <!-- 2. Cellular Mechanism & Molecular Purity -->
                <div style="margin-bottom: 1.75rem; border-top: 1px dashed var(--color-border); padding-top: 1.5rem;">
                    <div style="font-weight: 700; font-size: 0.95rem; margin-bottom: 0.75rem; color: var(--color-text-emphasis); display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fa-solid fa-atom" style="color: #2A8FC2;"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'الآلية الحيوية والنقاء الجزيئي (Bioactive Mechanisms & Purity)' : 'Pharmacological Mechanisms & Bioactive Purity Assays' }}</span>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <x-forms.textarea 
                            name="clinical_mechanism" 
                            :label="__('admin.products.fields.clinical_mechanism')" 
                            rows="3" 
                            :value="old('clinical_mechanism')"
                            placeholder="e.g. Upregulates acetylcholine synthesis and stimulates brain-derived neurotrophic factor (BDNF)..." 
                        />

                        <x-forms.textarea 
                            name="formula_details" 
                            :label="__('admin.products.fields.formula_details')" 
                            rows="3" 
                            :value="old('formula_details')"
                            placeholder="e.g. 99.4% HPLC verified bioactive purity, solvent-free supercritical CO2 extraction..." 
                        />
                    </div>
                </div>

                <!-- 3. Clinical Benefits & Measured Biomarkers (Bilingual list) -->
                <div style="margin-bottom: 1.75rem; border-top: 1px dashed var(--color-border); padding-top: 1.5rem;">
                    <div style="font-weight: 700; font-size: 0.95rem; margin-bottom: 0.75rem; color: var(--color-text-emphasis); display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fa-solid fa-chart-line" style="color: #10B981;"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'الفوائد الإكلينيكية والمؤشرات الحيوية (Clinical Benefits & Biomarkers)' : 'Clinical Benefits & Measured Biomarkers' }}</span>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <x-forms.textarea 
                            name="benefits_en" 
                            :label="__('admin.products.fields.benefits_en')" 
                            rows="4" 
                            :value="old('benefits_en')"
                            placeholder="Enter each benefit or trial metric on a new line:&#10;+38% NAD+ elevation in 14 days&#10;Sustained mental stamina for 8+ hours&#10;Supports neurogenesis and BDNF signaling" 
                            hint="Enter one clinical benefit per line. Will be displayed as verified biomarker pills."
                        />

                        <x-forms.textarea 
                            name="benefits_ar" 
                            :label="__('admin.products.fields.benefits_ar')" 
                            rows="4" 
                            :value="old('benefits_ar')"
                            placeholder="أدخل كل فائدة سريرية في سطر مستقل:&#10;+38% ارتفاع في مستويات NAD+ خلال 14 يوماً&#10;طاقة ذهنية متواصلة لأكثر من 8 ساعات دون هبوط&#10;يدعم تجدد الخلايا العصبية وإشارات BDNF" 
                            hint="أدخل كل فائدة في سطر مستقل لتظهر في بطاقات الأبحاث السريرية."
                        />
                    </div>
                </div>

                <!-- 4. Standardized Active Compounds (Dynamic Repeater) -->
                <div style="margin-bottom: 1.75rem; border-top: 1px dashed var(--color-border); padding-top: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                        <div style="font-weight: 700; font-size: 0.95rem; color: var(--color-text-emphasis); display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fa-solid fa-flask-vial" style="color: #8B5CF6;"></i>
                            <span>{{ __('admin.products.fields.ingredients') }}</span>
                        </div>
                        <button type="button" class="btn btn-secondary btn-sm" id="btnAddIngredient" onclick="addIngredientRow('', '', '')" style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.8rem; padding: 0.35rem 0.75rem;">
                            <i class="fa-solid fa-plus"></i> {{ __('admin.products.fields.add_ingredient') }}
                        </button>
                    </div>

                    <div style="overflow-x: auto; border: 1px solid var(--color-border); border-radius: var(--radius-md); background: var(--color-bg-subtle);">
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;" id="ingredientsTable">
                            <thead>
                                <tr style="border-bottom: 1px solid var(--color-border); background: rgba(0,0,0,0.02); text-align: start;">
                                    <th style="padding: 0.75rem 1rem; font-weight: 700; width: 35%;">{{ __('admin.products.fields.ingredient_name_en') }}</th>
                                    <th style="padding: 0.75rem 1rem; font-weight: 700; width: 35%;">{{ __('admin.products.fields.ingredient_name_ar') }}</th>
                                    <th style="padding: 0.75rem 1rem; font-weight: 700; width: 20%;">{{ __('admin.products.fields.ingredient_dose') }}</th>
                                    <th style="padding: 0.75rem 1rem; text-align: center; width: 10%;">{{ app()->getLocale() === 'ar' ? 'إجراء' : 'Action' }}</th>
                                </tr>
                            </thead>
                            <tbody id="ingredientsContainer">
                                <!-- Dynamic rows inserted here by script -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 5. Contraindications & Warnings -->
                <div style="border-top: 1px dashed var(--color-border); padding-top: 1.5rem;">
                    <div style="font-weight: 700; font-size: 0.95rem; margin-bottom: 0.75rem; color: var(--color-text-emphasis); display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fa-solid fa-shield-halved" style="color: #F59E0B;"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'إرشادات السلامة وموانع الاستخدام' : 'Clinical Safety Guidelines & Storage' }}</span>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <x-forms.textarea 
                            name="contraindications" 
                            :label="__('admin.products.fields.contraindications')" 
                            rows="3" 
                            :value="old('contraindications')"
                            placeholder="e.g. Not recommended for pregnant or lactating individuals without physician consult..." 
                        />

                        <x-forms.textarea 
                            name="warnings" 
                            :label="__('admin.products.fields.warnings')" 
                            rows="3" 
                            :value="old('warnings')"
                            placeholder="e.g. Store in a cool dry place away from direct sunlight. Keep out of reach of children." 
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- STEP 6: Inventory & Controls & Summary Review -->
        <div class="wizard-step-pane" id="step-pane-6" style="display: none;">
            <div style="display: flex; flex-direction: column; gap: 2rem;">
                <!-- Inventory Limits Card -->
                <div class="card" style="padding: 2.25rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;">
                        <div>
                            <h3 style="font-size: 1.25rem; font-weight: 800; margin: 0 0 0.25rem 0;">
                                {{ __('admin.products.sections.inventory_control') }}
                            </h3>
                            <p class="text-xs text-muted" style="margin: 0;">
                                {{ app()->getLocale() === 'ar' ? 'الخطوة 6 من 6: تحديد كميات المخزون الأولي والتنبيهات' : 'Step 6 of 6: Inventory buffer allocation and replenishment thresholds' }}
                            </p>
                        </div>
                        <span class="badge badge-accent">{{ app()->getLocale() === 'ar' ? 'خطوة 6 / 6' : 'Step 6 / 6' }}</span>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem;">
                        <x-forms.input 
                            name="stock_online" 
                            type="number" 
                            min="0"
                            :label="__('admin.products.fields.online_stock')" 
                            :value="old('stock_online', '100')" 
                            required 
                        />

                        <x-forms.input 
                            name="stock_offline" 
                            type="number" 
                            min="0"
                            :label="__('admin.products.fields.offline_stock')" 
                            :value="old('stock_offline', '50')" 
                            required 
                        />

                        <x-forms.input 
                            name="low_stock_threshold" 
                            type="number" 
                            min="1"
                            :label="__('admin.products.fields.low_stock_threshold')" 
                            :value="old('low_stock_threshold', '10')" 
                            required 
                        />
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 1rem; margin-top: 1.5rem; border-top: 1px solid var(--color-border); padding-top: 1rem;">
                        <x-forms.select 
                            name="status" 
                            label="Publication Status" 
                            :options="[
                                'active' => app()->getLocale() == 'ar' ? 'نشط في الكتالوج' : 'Active (Published)',
                                'draft' => app()->getLocale() == 'ar' ? 'مسودة سريرية' : 'Draft Protocol',
                                'inactive' => app()->getLocale() == 'ar' ? 'معطل / غير متاح' : 'Inactive',
                            ]" 
                            :selected="old('status', 'active')"
                            required 
                        />

                        <x-forms.toggle 
                            name="is_featured" 
                            :label="__('admin.products.fields.is_featured')" 
                            :checked="old('is_featured', true)" 
                        />

                        <x-forms.toggle 
                            name="is_best_seller" 
                            :label="__('admin.products.fields.is_best_seller')" 
                            :checked="old('is_best_seller', false)" 
                        />

                        <x-forms.toggle 
                            name="enable_backorders" 
                            :label="__('admin.products.fields.enable_backorders')" 
                            :checked="old('enable_backorders', false)" 
                        />
                    </div>
                </div>

                <!-- Final Verification & Review Card -->
                <div class="card" style="padding: 2.25rem; background: var(--color-bg-subtle); border: 1px solid var(--color-primary);">
                    <h4 style="font-size: 1.15rem; font-weight: 800; color: var(--color-primary); margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                        <span>📋</span>
                        <span>{{ app()->getLocale() === 'ar' ? 'ملخص مراجعة إدراج التركيبة الحيوية' : 'Formulation Ingestion Final Verification' }}</span>
                    </h4>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; font-size: 0.875rem;">
                        <div>
                            <span class="text-muted">{{ __('admin.products.fields.sku') }}:</span>
                            <div class="font-bold font-mono" id="reviewSKU">BZ-MND-001</div>
                        </div>
                        <div>
                            <span class="text-muted">{{ __('admin.products.fields.name_ar') }}:</span>
                            <div class="font-bold" id="reviewNameAR">بلو مايند</div>
                        </div>
                        <div>
                            <span class="text-muted">{{ __('admin.products.fields.retail_price') }}:</span>
                            <div class="font-bold font-mono text-primary" id="reviewPrice">$68.00</div>
                        </div>
                        <div>
                            <span class="text-muted">{{ app()->getLocale() === 'ar' ? 'إجمالي المخزون الأولي' : 'Total Initial Units' }}:</span>
                            <div class="font-bold font-mono text-success" id="reviewStock">150 Units</div>
                        </div>
                    </div>

                    <div style="margin-top: 1.5rem; display: flex; justify-content: flex-end; gap: 1rem;">
                        <button type="submit" class="btn btn-primary btn-lg" style="font-size: 1rem; padding: 0.85rem 2rem;">
                            <i class="fa-solid fa-floppy-disk mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'تأكيد وإدراج التركيبة في الكتالوج' : 'Confirm & Publish Formulation to Central ERP' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <style>
        .wizard-step {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1.25rem;
            background: var(--color-bg-surface);
            border: 1px solid var(--color-border);
            border-radius: var(--radius-lg);
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
        }
        .wizard-step:hover {
            border-color: var(--color-primary);
            background: var(--color-bg-subtle);
        }
        .wizard-step.active {
            border-color: var(--color-primary);
            background: rgba(30, 58, 138, 0.08);
            box-shadow: 0 0 0 2px rgba(30, 58, 138, 0.2);
        }
        .wizard-step.completed .step-num {
            background: var(--color-success);
            color: #fff;
        }
        .step-num {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: var(--color-bg-subtle);
            border: 1px solid var(--color-border);
            color: var(--color-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.8125rem;
        }
        .wizard-step.active .step-num {
            background: var(--color-primary);
            color: #fff;
        }
        .step-info {
            display: flex;
            flex-direction: column;
        }
        .step-title {
            font-weight: 700;
            font-size: 0.875rem;
            color: var(--color-text-main);
        }
        .step-sub {
            font-size: 0.7rem;
            color: var(--color-text-muted);
        }
        .input-error-highlight {
            border-color: var(--color-danger) !important;
            box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.2) !important;
        }
    </style>

    <script>
        let currentStep = 1;
        const totalSteps = 6;
        const taxRate = {{ (float) $taxInfo['tax_rate'] }};
        const pricesIncludeTax = {{ $taxInfo['is_inclusive'] ? 'true' : 'false' }};

        function goToStep(step) {
            if (step > currentStep) {
                // Validate all intermediate steps before jumping forward
                for (let s = currentStep; s < step; s++) {
                    if (!validateStep(s)) {
                        return;
                    }
                }
            }
            setStep(step);
        }

        function navigateStep(direction) {
            if (direction > 0) {
                if (!validateStep(currentStep)) {
                    return;
                }
            }

            const nextStep = currentStep + direction;
            if (nextStep >= 1 && nextStep <= totalSteps) {
                setStep(nextStep);
            }
        }

        function setStep(step) {
            currentStep = step;

            // Toggle panes
            for (let i = 1; i <= totalSteps; i++) {
                const pane = document.getElementById('step-pane-' + i);
                if (pane) {
                    pane.style.display = (i === step) ? 'block' : 'none';
                }

                // Update wizard indicators
                const stepEl = document.querySelector(`.wizard-step[data-step="${i}"]`);
                if (stepEl) {
                    stepEl.classList.remove('active');
                    if (i < step) {
                        stepEl.classList.add('completed');
                    } else if (i === step) {
                        stepEl.classList.add('active');
                    }
                }
            }

            // Update action buttons
            const btnPrev = document.getElementById('btnPrevStep');
            const btnNext = document.getElementById('btnNextStep');
            const btnSubmit = document.getElementById('btnSubmitForm');

            if (btnPrev) btnPrev.style.display = (step > 1) ? 'inline-flex' : 'none';
            if (btnNext) btnNext.style.display = (step < totalSteps) ? 'inline-flex' : 'none';
            if (btnSubmit) btnSubmit.style.display = (step === totalSteps) ? 'inline-flex' : 'none';

            // If Step 6, update review summary
            if (step === 6) {
                updateReviewSummary();
            }

            // Scroll smoothly to top of form
            window.scrollTo({ top: 120, behavior: 'smooth' });
        }

        function validateStep(step) {
            const pane = document.getElementById('step-pane-' + step);
            if (!pane) return true;

            const requiredInputs = pane.querySelectorAll('input[required], select[required], textarea[required]');
            let isValid = true;
            let firstInvalid = null;

            requiredInputs.forEach(input => {
                input.classList.remove('input-error-highlight');
                if (!input.value || input.value.trim() === '') {
                    isValid = false;
                    input.classList.add('input-error-highlight');
                    if (!firstInvalid) firstInvalid = input;
                }
            });

            if (!isValid) {
                const isAr = "{{ app()->getLocale() }}" === 'ar';
                const msg = isAr 
                    ? 'يرجى استكمال جميع الحقول الإلزامية المطلوبة في هذه الخطوة قبل المتابعة.' 
                    : 'Please fill out all mandatory fields in this step before proceeding.';
                const title = isAr ? 'بيانات غير مكتملة' : 'Incomplete Step Data';

                if (window.toast) {
                    window.toast.error(msg, title);
                } else {
                    alert(msg);
                }

                if (firstInvalid) {
                    firstInvalid.focus();
                }
                return false;
            }

            return true;
        }

        function recalculateTaxAndMargin() {
            const cost = parseFloat(document.getElementById('inputCostPrice')?.value) || 0;
            const retail = parseFloat(document.getElementById('inputRetailPrice')?.value) || 0;
            const sale = parseFloat(document.getElementById('inputSalePrice')?.value) || 0;

            const effective = (sale > 0 && sale < retail) ? sale : retail;

            let netPrice, taxAmount, grossPrice;

            if (pricesIncludeTax) {
                netPrice = effective / (1 + (taxRate / 100));
                taxAmount = effective - netPrice;
                grossPrice = effective;
            } else {
                netPrice = effective;
                taxAmount = effective * (taxRate / 100);
                grossPrice = netPrice + taxAmount;
            }

            const margin = netPrice - cost;
            const marginPct = (netPrice > 0) ? ((margin / netPrice) * 100).toFixed(1) : 0;

            document.getElementById('displayCostPrice').innerText = '$' + cost.toFixed(2);
            document.getElementById('displayNetPrice').innerText = '$' + netPrice.toFixed(2);
            document.getElementById('displayTaxAmount').innerText = '$' + taxAmount.toFixed(2);
            document.getElementById('displayGrossPrice').innerText = '$' + grossPrice.toFixed(2);
            document.getElementById('displayProfitMargin').innerText = '$' + margin.toFixed(2) + ' (' + marginPct + '%)';
        }

        function updateReviewSummary() {
            const sku = document.querySelector('input[name="sku"]')?.value || 'N/A';
            const nameAr = document.querySelector('input[name="name_ar"]')?.value || document.querySelector('input[name="name_en"]')?.value || 'N/A';
            const price = parseFloat(document.querySelector('input[name="price"]')?.value) || 0;
            const stockOnline = parseInt(document.querySelector('input[name="stock_online"]')?.value) || 0;
            const stockOffline = parseInt(document.querySelector('input[name="stock_offline"]')?.value) || 0;

            const rSKU = document.getElementById('reviewSKU');
            const rName = document.getElementById('reviewNameAR');
            const rPrice = document.getElementById('reviewPrice');
            const rStock = document.getElementById('reviewStock');

            if (rSKU) rSKU.innerText = sku;
            if (rName) rName.innerText = nameAr;
            if (rPrice) rPrice.innerText = '$' + price.toFixed(2);
            if (rStock) rStock.innerText = (stockOnline + stockOffline) + ' Units (' + stockOnline + ' Online / ' + stockOffline + ' Boutique)';
        }

        // Dynamic Active Compounds Repeater
        let ingredientCount = 0;
        function addIngredientRow(nameEn = '', nameAr = '', dose = '') {
            const tbody = document.getElementById('ingredientsContainer');
            if (!tbody) return;
            const tr = document.createElement('tr');
            tr.style.borderBottom = '1px solid var(--color-border)';
            tr.id = `ingredient-row-${ingredientCount}`;
            tr.innerHTML = `
                <td style="padding: 0.5rem 0.75rem;">
                    <input type="text" name="ingredients[${ingredientCount}][name_en]" value="${escapeHtml(nameEn)}" class="form-input" placeholder="e.g. Beta-NMN (99.8%)" style="font-size: 0.85rem; padding: 0.4rem 0.6rem; width: 100%;">
                </td>
                <td style="padding: 0.5rem 0.75rem;">
                    <input type="text" name="ingredients[${ingredientCount}][name_ar]" value="${escapeHtml(nameAr)}" class="form-input" placeholder="مثال: بيتا-NMN فائق النقاء" style="font-size: 0.85rem; padding: 0.4rem 0.6rem; width: 100%;">
                </td>
                <td style="padding: 0.5rem 0.75rem;">
                    <input type="text" name="ingredients[${ingredientCount}][dose]" value="${escapeHtml(dose)}" class="form-input" placeholder="e.g. 500 mg" style="font-size: 0.85rem; padding: 0.4rem 0.6rem; width: 100%;">
                </td>
                <td style="padding: 0.5rem 0.75rem; text-align: center;">
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeIngredientRow(${ingredientCount})" style="padding: 0.35rem 0.6rem; font-size: 0.75rem;" title="{{ __('admin.products.fields.remove_ingredient') }}">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
            ingredientCount++;
        }

        function removeIngredientRow(id) {
            const row = document.getElementById(`ingredient-row-${id}`);
            if (row) row.remove();
        }

        function escapeHtml(text) {
            if (!text) return '';
            return String(text).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/'/g, '&#039;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        }

        document.addEventListener('DOMContentLoaded', () => {
            recalculateTaxAndMargin();

            const initialIngredients = @json(old('ingredients', []));
            if (Array.isArray(initialIngredients) && initialIngredients.length > 0) {
                initialIngredients.forEach(item => {
                    if (item) {
                        addIngredientRow(item.name_en || '', item.name_ar || '', item.dose || '');
                    }
                });
            } else {
                addIngredientRow('', '', '');
                addIngredientRow('', '', '');
            }
        });
    </script>
>>>>>>> origin/main
</x-layouts.admin>
