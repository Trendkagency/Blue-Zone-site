<x-layouts.admin 
    :pageTitle="__('admin.products.edit_title', ['name' => $product['name_' . app()->getLocale()] ?? $product['name_en']])" 
    :pageSubtitle="__('admin.products.edit_subtitle')"
    :breadcrumbs="[__('admin.menu.products') => route('admin.products.index'), ($product['name_' . app()->getLocale()] ?? $product['name_en']) => route('admin.products.edit', $product['id'])]"
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
            </div>
        </div>
    </div>

    <!-- Main Edit Form -->
    <form method="POST" action="{{ route('admin.products.update', $product['id']) }}" id="productEditForm" enctype="multipart/form-data">
        @csrf
        @method('PUT')

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
                <i class="fa-solid fa-floppy-disk mr-1.5 ml-1.5"></i> {{ __('app.actions.save') }}
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
                            {{ app()->getLocale() === 'ar' ? 'الخطوة 1 من 6: تحديد المعرفات الأساسية والنظام الحيوي' : 'Step 1 of 6: SKU, Barcode, category classification, and demographic target' }}
                        </p>
                    </div>
                    <span class="badge badge-accent">{{ app()->getLocale() === 'ar' ? 'خطوة 1 / 6' : 'Step 1 / 6' }}</span>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <div>
                        <x-forms.input 
                            name="sku" 
                            :label="__('admin.products.fields.sku')" 
                            :value="old('sku', $product['sku'])" 
                            required 
                        />
                    </div>
                    <div>
                        <x-forms.input 
                            name="barcode" 
                            :label="__('admin.products.fields.barcode')" 
                            :value="old('barcode', $product['barcode'])" 
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
                            :selected="old('category_id', $product['category_id'])"
                            required 
                        />
                    </div>
                    <div>
                        <x-forms.input 
                            name="subcategory_en" 
                            :label="__('admin.products.fields.subcategory') . ' (EN)'" 
                            :value="old('subcategory_en', $product['subcategory_en'] ?? 'Nootropics')" 
                        />
                    </div>
                    <div>
                        <x-forms.input 
                            name="brand" 
                            :label="__('admin.products.fields.brand')" 
                            :value="old('brand', $product['brand'] ?? 'Blue Zone Bioceuticals')" 
                            required 
                        />
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem; margin-top: 1rem;">
                    <div>
                        <x-forms.input 
                            name="target_gender" 
                            :label="__('admin.products.fields.target_demographic')" 
                            :value="old('target_gender', $product['target_gender'] ?? 'Unisex')" 
                        />
                    </div>
                    <div>
                        <x-forms.input 
                            name="age_group" 
                            :label="__('admin.products.fields.age_cohort')" 
                            :value="old('age_group', $product['age_group'] ?? '18+')" 
                        />
                    </div>
                    <div>
                        <x-forms.input 
                            name="product_size" 
                            :label="__('admin.products.fields.dosage_form')" 
                            :value="old('product_size', $product['product_size'] ?? '60 Vegetable Capsules')" 
                        />
                    </div>
                </div>

                <div style="margin-top: 1.5rem; border-top: 1px solid var(--color-border); padding-top: 1rem;">
                    <x-forms.input 
                        name="slug" 
                        label="URL Slug" 
                        :value="old('slug', $product['slug'])" 
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

                <!-- Arabic Section -->
                <div style="background: var(--color-bg-subtle); padding: 1.75rem; border-radius: var(--radius-lg); border: 1px solid var(--color-border); margin-bottom: 2rem;" dir="rtl">
                    <h4 style="font-size: 1.1rem; font-weight: 800; color: var(--color-primary); margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                        <span>🇸🇦</span>
                        <span>{{ __('admin.products.sections.arabic_content') }}</span>
                    </h4>

                    <x-forms.input 
                        name="name_ar" 
                        :label="__('admin.products.fields.name_ar')" 
                        :value="old('name_ar', $product['name_ar'])" 
                        required 
                    />

                    <div style="margin-top: 1rem;">
                        <x-forms.input 
                            name="tagline_ar" 
                            :label="__('admin.products.fields.tagline_ar')" 
                            :value="old('tagline_ar', $product['tagline_ar'])" 
                        />
                    </div>

                    <div style="margin-top: 1rem;">
                        <x-forms.textarea 
                            name="description_ar" 
                            :label="__('admin.products.fields.description_ar')" 
                            rows="4" 
                            required
                        >{{ old('description_ar', $product['description_ar'] ?? $product['short_description_ar'] ?? '') }}</x-forms.textarea>
                    </div>

                    <div style="margin-top: 1rem;">
                        <x-forms.textarea 
                            name="usage_ar" 
                            :label="__('admin.products.fields.usage_ar')" 
                            rows="2" 
                        >{{ old('usage_ar', $product['usage_ar']) }}</x-forms.textarea>
                    </div>
                </div>

                <!-- English Section -->
                <div style="background: var(--color-bg-subtle); padding: 1.75rem; border-radius: var(--radius-lg); border: 1px solid var(--color-border);" dir="ltr">
                    <h4 style="font-size: 1.1rem; font-weight: 800; color: var(--color-primary); margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                        <span>🇬🇧</span>
                        <span>{{ __('admin.products.sections.english_content') }}</span>
                    </h4>

                    <x-forms.input 
                        name="name_en" 
                        :label="__('admin.products.fields.name_en')" 
                        :value="old('name_en', $product['name_en'])" 
                        required 
                    />

                    <div style="margin-top: 1rem;">
                        <x-forms.input 
                            name="tagline_en" 
                            :label="__('admin.products.fields.tagline_en')" 
                            :value="old('tagline_en', $product['tagline_en'])" 
                        />
                    </div>

                    <div style="margin-top: 1rem;">
                        <x-forms.textarea 
                            name="description_en" 
                            :label="__('admin.products.fields.description_en')" 
                            rows="4" 
                            required
                        >{{ old('description_en', $product['description_en'] ?? $product['short_description_en'] ?? '') }}</x-forms.textarea>
                    </div>

                    <div style="margin-top: 1rem;">
                        <x-forms.textarea 
                            name="usage_en" 
                            :label="__('admin.products.fields.usage_en')" 
                            rows="2" 
                        >{{ old('usage_en', $product['usage_en']) }}</x-forms.textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- STEP 3: Pricing, Margins & Taxes -->
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
                            :value="old('cost_price', $product['cost_price'])" 
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
                            :value="old('price', $product['price'])" 
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
                            :value="old('sale_price', $product['sale_price'])" 
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
                            <div class="text-xs text-muted" style="margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'ضريبة القيمة المضافة' : 'VAT Tax Amount' }}</div>
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
                        :helper="app()->getLocale() === 'ar' ? 'ارفع صورة رئيسية جديدة (WebP, PNG, JPG حتى 10MB)' : 'Upload new high-resolution clinical hero render'"
                        :existingFiles="!empty($product['image']) ? [asset($product['image'])] : []"
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
                            :value="old('image', $product['image'])" 
                            placeholder="e.g. assets/products/blue-mind.jpg" 
                            hint="Used automatically when no Spatie media file is uploaded."
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- STEP 5: Clinical Section & Active Compounds -->
        <div class="wizard-step-pane" id="step-pane-5" style="display: none;">
            <div class="card" style="padding: 2.25rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;">
                    <div>
                        <h3 style="font-size: 1.25rem; font-weight: 800; margin: 0 0 0.25rem 0;">
                            {{ __('admin.products.sections.clinical_research') }}
                        </h3>
                        <p class="text-xs text-muted" style="margin: 0;">
                            {{ app()->getLocale() === 'ar' ? 'الخطوة 5 من 6: الآليات الحيوية، موانع الاستخدام، ونقاء التركيبة' : 'Step 5 of 6: Pharmacological mechanisms, purity assays, and clinical contraindications' }}
                        </p>
                    </div>
                    <span class="badge badge-accent">{{ app()->getLocale() === 'ar' ? 'خطوة 5 / 6' : 'Step 5 / 6' }}</span>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                    <x-forms.textarea 
                        name="clinical_mechanism" 
                        :label="__('admin.products.fields.clinical_mechanism')" 
                        rows="4" 
                    >{{ old('clinical_mechanism', $product['clinical_mechanism']) }}</x-forms.textarea>

                    <x-forms.textarea 
                        name="formula_details" 
                        :label="__('admin.products.fields.formula_details')" 
                        rows="4" 
                    >{{ old('formula_details', $product['formula_details']) }}</x-forms.textarea>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 1.5rem;">
                    <x-forms.textarea 
                        name="contraindications" 
                        :label="__('admin.products.fields.contraindications')" 
                        rows="3" 
                    >{{ old('contraindications', $product['contraindications']) }}</x-forms.textarea>

                    <x-forms.textarea 
                        name="warnings" 
                        :label="__('admin.products.fields.warnings')" 
                        rows="3" 
                    >{{ old('warnings', $product['warnings']) }}</x-forms.textarea>
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
                                {{ app()->getLocale() === 'ar' ? 'الخطوة 6 من 6: تحديد كميات المخزون والتنبيهات' : 'Step 6 of 6: Inventory buffer allocation and replenishment thresholds' }}
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
                            :value="old('stock_online', $product['stock_online'])" 
                            required 
                        />

                        <x-forms.input 
                            name="stock_offline" 
                            type="number" 
                            min="0"
                            :label="__('admin.products.fields.offline_stock')" 
                            :value="old('stock_offline', $product['stock_offline'])" 
                            required 
                        />

                        <x-forms.input 
                            name="low_stock_threshold" 
                            type="number" 
                            min="1"
                            :label="__('admin.products.fields.low_stock_threshold')" 
                            :value="old('low_stock_threshold', $product['low_stock_threshold'])" 
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
                            :selected="old('status', $product['status'] ?? 'active')"
                            required 
                        />

                        <x-forms.toggle 
                            name="is_featured" 
                            :label="__('admin.products.fields.is_featured')" 
                            :checked="old('is_featured', $product['is_featured'] ?? false)" 
                        />

                        <x-forms.toggle 
                            name="is_best_seller" 
                            :label="__('admin.products.fields.is_best_seller')" 
                            :checked="old('is_best_seller', $product['is_best_seller'] ?? false)" 
                        />

                        <x-forms.toggle 
                            name="enable_backorders" 
                            :label="__('admin.products.fields.enable_backorders')" 
                            :checked="old('enable_backorders', $product['enable_backorders'] ?? false)" 
                        />
                    </div>
                </div>

                <!-- Final Verification & Review Card -->
                <div class="card" style="padding: 2.25rem; background: var(--color-bg-subtle); border: 1px solid var(--color-primary);">
                    <h4 style="font-size: 1.15rem; font-weight: 800; color: var(--color-primary); margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                        <span>📋</span>
                        <span>{{ app()->getLocale() === 'ar' ? 'ملخص مراجعة تعديل التركيبة الحيوية' : 'Formulation Update Final Verification' }}</span>
                    </h4>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; font-size: 0.875rem;">
                        <div>
                            <span class="text-muted">{{ __('admin.products.fields.sku') }}:</span>
                            <div class="font-bold font-mono" id="reviewSKU">{{ $product['sku'] }}</div>
                        </div>
                        <div>
                            <span class="text-muted">{{ __('admin.products.fields.name_ar') }}:</span>
                            <div class="font-bold" id="reviewNameAR">{{ $product['name_ar'] }}</div>
                        </div>
                        <div>
                            <span class="text-muted">{{ __('admin.products.fields.retail_price') }}:</span>
                            <div class="font-bold font-mono text-primary" id="reviewPrice">${{ number_format($product['price'], 2) }}</div>
                        </div>
                        <div>
                            <span class="text-muted">{{ app()->getLocale() === 'ar' ? 'إجمالي المخزون الأولي' : 'Total Initial Units' }}:</span>
                            <div class="font-bold font-mono text-success" id="reviewStock">{{ ($product['stock_online'] + $product['stock_offline']) }} Units</div>
                        </div>
                    </div>

                    <div style="margin-top: 1.5rem; display: flex; justify-content: flex-end; gap: 1rem;">
                        <button type="submit" class="btn btn-primary btn-lg" style="font-size: 1rem; padding: 0.85rem 2rem;">
                            <i class="fa-solid fa-floppy-disk mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'حفظ وتحديث التركيبة' : 'Save & Update Formulation' }}
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
                // Validate intermediate steps before jumping forward
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

        document.addEventListener('DOMContentLoaded', () => {
            recalculateTaxAndMargin();
        });
    </script>
</x-layouts.admin>
