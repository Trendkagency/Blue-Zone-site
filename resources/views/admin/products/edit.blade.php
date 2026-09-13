<x-layouts.admin 
    :pageTitle="__('admin.products.edit_title', ['name' => $product['name_' . app()->getLocale()] ?? $product['name_en']])" 
    :pageSubtitle="__('admin.products.edit_subtitle')"
    :breadcrumbs="[__('admin.menu.products') => route('admin.products.index'), ($product['name_' . app()->getLocale()] ?? $product['name_en']) => route('admin.products.edit', $product['id'])]"
>
    <!-- Server-Side Error Alert (if any) -->
    @if ($errors->any())
        <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid var(--color-danger); color: var(--color-danger); padding: 1.25rem 1.5rem; border-radius: var(--radius-md); margin-bottom: 2rem;">
            <div style="font-weight: 800; font-size: 1rem; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fa-solid fa-triangle-exclamation text-danger"></i>
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
            <button type="button" class="btn btn-outline-primary" onclick="openLiveProductPreview()" style="font-weight: 700;">
                <i class="fa-solid fa-eye mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'معاينة المنتج الحية' : 'Live Product Preview' }}
            </button>
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
                        <i class="fa-solid fa-language text-sky-500"></i>
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
                        <i class="fa-solid fa-globe text-primary"></i>
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
                            <i class="fa-solid fa-chart-pie text-primary"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'التحليل المالي المباشر والضرائب (ZATCA Engine)' : 'Live Tax Breakdown & Margin Analysis' }}</span>
                        </h4>
                        <span class="badge badge-success text-xs font-mono" id="badgeTaxRate">
                            VAT {{ $taxInfo['tax_rate'] }}%
                        </span>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1.5rem;">
                        <div class="stat-card" style="background: var(--color-bg-surface); padding: 1rem; border-radius: var(--radius-md);">
                            <div class="text-xs text-muted" style="margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'تكلفة الإنتاج' : 'Unit Cost' }}</div>
                            <div class="font-bold font-mono text-lg" id="displayCostPrice">@currency($taxInfo['cost_price'])</div>
                        </div>

                        <div class="stat-card" style="background: var(--color-bg-surface); padding: 1rem; border-radius: var(--radius-md);">
                            <div class="text-xs text-muted" style="margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'السعر الأساسي' : 'Net Base Price' }}</div>
                            <div class="font-bold font-mono text-lg text-primary" id="displayNetPrice">@currency($taxInfo['net_price'])</div>
                        </div>

                        <div class="stat-card" style="background: var(--color-bg-surface); padding: 1rem; border-radius: var(--radius-md);">
                            <div class="text-xs text-muted" style="margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'ضريبة القيمة المضافة' : 'VAT Tax Amount' }}</div>
                            <div class="font-bold font-mono text-lg text-warning" id="displayTaxAmount">@currency($taxInfo['tax_amount'])</div>
                        </div>

                        <div class="stat-card" style="background: var(--color-bg-surface); padding: 1rem; border-radius: var(--radius-md);">
                            <div class="text-xs text-muted" style="margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'السعر الإجمالي للعميل' : 'Gross Consumer Price' }}</div>
                            <div class="font-bold font-mono text-lg text-success" id="displayGrossPrice">@currency($taxInfo['gross_price'])</div>
                        </div>

                        <div class="stat-card" style="background: var(--color-bg-surface); padding: 1rem; border-radius: var(--radius-md);">
                            <div class="text-xs text-muted" style="margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'هامش الربح الصافي' : 'Net Margin' }}</div>
                            <div class="font-bold font-mono text-lg text-success" id="displayProfitMargin">
                                @currency($taxInfo['profit_margin']) ({{ $taxInfo['profit_margin_percentage'] }}%)
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
                        :existingFiles="!empty($product['image']) ? [\App\Models\Product::normalizeUrl($product['image'])] : []"
                        accept="image/*"
                        :maxSize="10"
                    />

                    <!-- Gallery & Packaging Assets -->
                    <x-file-uploader 
                        name="gallery" 
                        :label="app()->getLocale() === 'ar' ? 'معرض صور العبوة والتفاصيل (Gallery Assets)' : 'Product Gallery & Packaging Shots'"
                        :helper="app()->getLocale() === 'ar' ? 'يمكنك رفع عدة صور لزوايا مختلفة والمكونات (WebP, PNG, JPG)' : 'You can upload multiple high-res product angles and ingredients shots'"
                        :existingFiles="!empty($product['images']) && is_array($product['images']) ? array_map(fn($img) => \App\Models\Product::normalizeUrl($img), $product['images']) : []"
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

        <!-- STEP 5: Our Science & Clinical Dossier -->
        <div class="wizard-step-pane" id="step-pane-5" style="display: none;">
            <div class="card" style="padding: 2.25rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;">
                    <div>
                        <h3 style="font-size: 1.25rem; font-weight: 800; margin: 0 0 0.25rem 0; display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fa-solid fa-flask-vial text-primary"></i>
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
                            :value="old('science_en', $product['science_en'] ?? '')"
                            placeholder="e.g. Extensive pharmacological breakdown of the cellular pathway, human clinical trials, and mitochondrial bioenergetics..." 
                            hint="Feeds directly into the Our Science section on the homepage and /our-science/{slug} page."
                        />

                        <x-forms.textarea 
                            name="science_ar" 
                            :label="__('admin.products.fields.science_ar')" 
                            rows="4" 
                            :value="old('science_ar', $product['science_ar'] ?? '')"
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
                            :value="old('clinical_mechanism', $product['clinical_mechanism'] ?? '')"
                            placeholder="e.g. Upregulates acetylcholine synthesis and stimulates brain-derived neurotrophic factor (BDNF)..." 
                        />

                        <x-forms.textarea 
                            name="formula_details" 
                            :label="__('admin.products.fields.formula_details')" 
                            rows="3" 
                            :value="old('formula_details', $product['formula_details'] ?? '')"
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
                            :value="old('benefits_en', $product['benefits_en'] ?? '')"
                            placeholder="Enter each benefit or trial metric on a new line:&#10;+38% NAD+ elevation in 14 days&#10;Sustained mental stamina for 8+ hours&#10;Supports neurogenesis and BDNF signaling" 
                            hint="Enter one clinical benefit per line. Will be displayed as verified biomarker pills."
                        />

                        <x-forms.textarea 
                            name="benefits_ar" 
                            :label="__('admin.products.fields.benefits_ar')" 
                            rows="4" 
                            :value="old('benefits_ar', $product['benefits_ar'] ?? '')"
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
                            :value="old('contraindications', $product['contraindications'] ?? '')"
                            placeholder="e.g. Not recommended for pregnant or lactating individuals without physician consult..." 
                        />

                        <x-forms.textarea 
                            name="warnings" 
                            :label="__('admin.products.fields.warnings')" 
                            rows="3" 
                            :value="old('warnings', $product['warnings'] ?? '')"
                            placeholder="e.g. Store in a cool dry place away from direct sunlight. Keep out of reach of children." 
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- STEP 6: Inventory & Controls & Summary Review -->
        <div class="wizard-step-pane" id="step-pane-6" style="display: none;">
            <div style="display: flex; flex-direction: column; gap: 2rem;">
                <!-- Audited Live Inventory Multi-Warehouse Control Hub -->
                <div class="card" style="padding: 2.25rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
                        <div>
                            <h3 style="font-size: 1.25rem; font-weight: 800; margin: 0 0 0.25rem 0; display: flex; align-items: center; gap: 0.5rem;">
                                <i class="fa-solid fa-boxes-stacked text-primary"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'الرقابة على المخزون والأرصدة الحية' : 'Audited Inventory & Multi-Hub Balances' }}</span>
                            </h3>
                            <p class="text-xs text-muted" style="margin: 0;">
                                {{ app()->getLocale() === 'ar' ? 'الخطوة 6 من 6: الرصيد الفعلي المعتمد عبر المستودعات وضوابط التنبيهات' : 'Step 6 of 6: Live multi-location inventory ledger and replenishment controls' }}
                            </p>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.6rem;">
                            <a href="{{ route('admin.inventory.history', ['product_id' => $product['id']]) }}" target="_blank" class="btn btn-secondary btn-sm" style="font-weight: 700;">
                                <i class="fa-solid fa-clock-rotate-left mr-1 ml-1 text-primary"></i> {{ app()->getLocale() === 'ar' ? 'سجل حركات التركيبة' : 'Stock Audit Trail' }}
                            </a>
                            <button type="button" class="btn btn-primary btn-sm" onclick="openProductStockInModal()" style="font-weight: 800;">
                                <i class="fa-solid fa-plus-circle mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'توريد / إضافة كميات' : 'Add Stock / Intake' }}
                            </button>
                        </div>
                    </div>

                    <!-- Regulatory & Audit Notification Banner -->
                    <div class="alert alert-info" style="margin-bottom: 1.5rem; display: flex; align-items: flex-start; gap: 0.85rem; border-radius: var(--radius-md); background: rgba(14, 165, 233, 0.08); border: 1px solid rgba(14, 165, 233, 0.25);">
                        <i class="fa-solid fa-shield-halved text-info" style="font-size: 1.25rem; margin-top: 0.15rem;"></i>
                        <div style="font-size: 0.8125rem; line-height: 1.5;">
                            <strong style="color: var(--color-primary); display: block; margin-bottom: 0.15rem;">
                                {{ app()->getLocale() === 'ar' ? 'حماية سلامة وسجل تدقيق المخزون (Audited Ledger Integrity):' : 'Audited Inventory Ledger Protection:' }}
                            </strong>
                            <span>
                                {{ app()->getLocale() === 'ar' 
                                    ? 'لحماية دقة السجلات المحاسبية والرقابية، تتم إضافة وتعديل الكميات حصرياً عبر أوامر التوريد والتسويات الموثقة لتسجيل السبب والمستخدم وتاريخ الحركة في سجل التدقيق المركزي.' 
                                    : 'To maintain supply chain integrity and financial audit trails, stock quantities are modified exclusively through auditable stock intakes and adjustment vouchers.' }}
                            </span>
                        </div>
                    </div>

                    <!-- Multi-Hub Live Stock Cards Grid -->
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 1.75rem;">
                        
                        <!-- 1. Online Fulfillment Hub -->
                        <div class="card" style="padding: 1.25rem; border: 1px solid rgba(37, 99, 235, 0.2); background: linear-gradient(135deg, rgba(37, 99, 235, 0.03), rgba(255,255,255,0.9)); position: relative; border-radius: var(--radius-lg);">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                <span class="text-xs font-bold text-muted" style="text-transform: uppercase;">
                                    🌐 {{ app()->getLocale() === 'ar' ? 'مستودع المتجر الإلكتروني' : 'Online Store Hub' }}
                                </span>
                                <span class="badge badge-primary font-mono text-xs" id="badgeOnlineStatus">Online</span>
                            </div>
                            <div class="font-black text-2xl font-mono text-primary" style="margin: 0.35rem 0;" id="liveStockOnlineDisplay">
                                {{ number_format($inventoryBreakdown['online'] ?? $product['stock_online']) }}
                                <span class="text-xs text-muted font-normal">{{ app()->getLocale() === 'ar' ? 'وحدة' : 'units' }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.75rem; padding-top: 0.5rem; border-top: 1px dashed var(--color-border);">
                                <span class="text-xs text-muted">{{ app()->getLocale() === 'ar' ? 'مخصص للطلبات أونلاين' : 'E-comm buffer' }}</span>
                                <button type="button" class="btn btn-xs btn-outline" onclick="openProductStockInModal('online')" style="padding: 0.2rem 0.5rem; font-size: 0.75rem;">
                                    + {{ app()->getLocale() === 'ar' ? 'توريد' : 'Intake' }}
                                </button>
                            </div>
                        </div>

                        <!-- 2. POS Warehouse -->
                        <div class="card" style="padding: 1.25rem; border: 1px solid rgba(16, 185, 129, 0.2); background: linear-gradient(135deg, rgba(16, 185, 129, 0.03), rgba(255,255,255,0.9)); position: relative; border-radius: var(--radius-lg);">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                <span class="text-xs font-bold text-muted" style="text-transform: uppercase;">
                                    🏬 {{ app()->getLocale() === 'ar' ? 'مستودع المبيعات المباشرة' : 'POS Sales Warehouse' }}
                                </span>
                                <span class="badge badge-success font-mono text-xs" id="badgeOfflineStatus">POS</span>
                            </div>
                            <div class="font-black text-2xl font-mono text-success" style="margin: 0.35rem 0;" id="liveStockOfflineDisplay">
                                {{ number_format($inventoryBreakdown['offline'] ?? $product['stock_offline']) }}
                                <span class="text-xs text-muted font-normal">{{ app()->getLocale() === 'ar' ? 'وحدة' : 'units' }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.75rem; padding-top: 0.5rem; border-top: 1px dashed var(--color-border);">
                                <span class="text-xs text-muted">{{ app()->getLocale() === 'ar' ? 'متاح لكاشير الصندوق' : 'Direct POS sales' }}</span>
                                <button type="button" class="btn btn-xs btn-outline" onclick="openProductStockInModal('offline')" style="padding: 0.2rem 0.5rem; font-size: 0.75rem;">
                                    + {{ app()->getLocale() === 'ar' ? 'توريد' : 'Intake' }}
                                </button>
                            </div>
                        </div>

                        <!-- 3. Central Warehouse Buffer -->
                        <div class="card" style="padding: 1.25rem; border: 1px solid rgba(139, 92, 246, 0.2); background: linear-gradient(135deg, rgba(139, 92, 246, 0.03), rgba(255,255,255,0.9)); position: relative; border-radius: var(--radius-lg);">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                <span class="text-xs font-bold text-muted" style="text-transform: uppercase;">
                                    🏭 {{ app()->getLocale() === 'ar' ? 'المستودع المركزي الرئيسي' : 'Central Depot Buffer' }}
                                </span>
                                <span class="badge badge-accent font-mono text-xs">Depot</span>
                            </div>
                            <div class="font-black text-2xl font-mono text-accent" style="margin: 0.35rem 0;" id="liveStockCentralDisplay">
                                {{ number_format($inventoryBreakdown['central'] ?? 0) }}
                                <span class="text-xs text-muted font-normal">{{ app()->getLocale() === 'ar' ? 'وحدة' : 'units' }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.75rem; padding-top: 0.5rem; border-top: 1px dashed var(--color-border);">
                                <span class="text-xs text-muted">{{ app()->getLocale() === 'ar' ? 'المخزون الاحتياطي' : 'Reserve storage' }}</span>
                                <button type="button" class="btn btn-xs btn-outline" onclick="openProductStockInModal('central_wh')" style="padding: 0.2rem 0.5rem; font-size: 0.75rem;">
                                    + {{ app()->getLocale() === 'ar' ? 'توريد' : 'Intake' }}
                                </button>
                            </div>
                        </div>

                        <!-- 4. Total Physical Stock & Valuation -->
                        <div class="card" style="padding: 1.25rem; border: 1px solid rgba(15, 23, 42, 0.15); background: var(--color-bg-subtle); border-radius: var(--radius-lg);">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                <span class="text-xs font-bold text-muted" style="text-transform: uppercase;">
                                    📦 {{ app()->getLocale() === 'ar' ? 'إجمالي الرصيد الفعلي' : 'Total Audited Inventory' }}
                                </span>
                                <span class="badge badge-neutral font-mono text-xs">{{ app()->getLocale() === 'ar' ? 'شامل' : 'Global' }}</span>
                            </div>
                            <div class="font-black text-2xl font-mono" style="margin: 0.35rem 0; color: var(--color-text-emphasis);" id="liveStockTotalDisplay">
                                {{ number_format($inventoryBreakdown['total'] ?? ($product['stock_online'] + $product['stock_offline'])) }}
                                <span class="text-xs text-muted font-normal">{{ app()->getLocale() === 'ar' ? 'وحدة' : 'units' }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.75rem; padding-top: 0.5rem; border-top: 1px dashed var(--color-border); font-size: 0.75rem;" class="text-muted">
                                <span>{{ app()->getLocale() === 'ar' ? 'القيمة التقديرية:' : 'Asset Valuation:' }}</span>
                                <strong class="font-mono text-primary" id="liveStockValuationDisplay">
                                    @currency($inventoryBreakdown['valuation'] ?? (($product['stock_online'] + $product['stock_offline']) * $product['price']))
                                </strong>
                            </div>
                        </div>

                    </div>

                    <!-- Hidden inputs preserving values for client review step without manual overwriting -->
                    <input type="hidden" name="stock_online" id="stock_online" value="{{ $product['stock_online'] }}">
                    <input type="hidden" name="stock_offline" id="stock_offline" value="{{ $product['stock_offline'] }}">

                    <!-- Threshold & Controls Section -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; border-top: 1px solid var(--color-border); padding-top: 1.5rem;">
                        <x-forms.input 
                            name="low_stock_threshold" 
                            type="number" 
                            min="1"
                            :label="__('admin.products.fields.low_stock_threshold')" 
                            :value="old('low_stock_threshold', $product['low_stock_threshold'])" 
                            required 
                            hint="يطلق إشعاراً عاجلاً للمدراء ومسؤولي الإمداد عند انخفاض الرصيد عن هذا الحد."
                        />

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
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-top: 1.5rem; border-top: 1px dashed var(--color-border); padding-top: 1rem;">
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
                        <i class="fa-solid fa-clipboard-check text-primary"></i>
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
                            <div class="font-bold font-mono text-primary" id="reviewPrice">@currency($product['price'])</div>
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

        function getNumericInput(names) {
            for (const name of names) {
                const el = document.getElementById(name) || document.querySelector(`input[name="${name}"]`);
                if (el && el.value !== '' && el.value !== undefined && !isNaN(el.value)) {
                    return parseFloat(el.value) || 0;
                }
            }
            return 0;
        }

        function recalculateTaxAndMargin() {
            const cost = getNumericInput(['inputCostPrice', 'cost_price']);
            const retail = getNumericInput(['inputRetailPrice', 'price']);
            const sale = getNumericInput(['inputSalePrice', 'sale_price']);

            const effective = (sale > 0 && sale < retail) ? sale : retail;

            let netPrice, taxAmount, grossPrice;

            if (pricesIncludeTax) {
                netPrice = (taxRate > 0) ? (effective / (1 + (taxRate / 100))) : effective;
                taxAmount = effective - netPrice;
                grossPrice = effective;
            } else {
                netPrice = effective;
                taxAmount = effective * (taxRate / 100);
                grossPrice = netPrice + taxAmount;
            }

            const margin = netPrice - cost;
            const marginPct = (netPrice > 0) ? ((margin / netPrice) * 100).toFixed(1) : '0.0';

            const formatC = (val) => (window.BLUEZONE_CURRENCY ? window.BLUEZONE_CURRENCY.format(val) : ('$' + Number(val).toFixed(2)));

            const elCost = document.getElementById('displayCostPrice');
            const elNet = document.getElementById('displayNetPrice');
            const elTax = document.getElementById('displayTaxAmount');
            const elGross = document.getElementById('displayGrossPrice');
            const elMargin = document.getElementById('displayProfitMargin');

            if (elCost) elCost.innerText = formatC(cost);
            if (elNet) elNet.innerText = formatC(netPrice);
            if (elTax) elTax.innerText = formatC(taxAmount);
            if (elGross) elGross.innerText = formatC(grossPrice);
            if (elMargin) {
                const sign = margin >= 0 ? '+' : '';
                elMargin.innerText = `${formatC(margin)} (${sign}${marginPct}%)`;
                if (margin > 0) {
                    elMargin.style.color = 'var(--color-success, #10b981)';
                } else if (margin < 0) {
                    elMargin.style.color = 'var(--color-danger, #ef4444)';
                } else {
                    elMargin.style.color = 'var(--color-text-main, #0A4F78)';
                }
            }

            // Sync with Live Preview if active
            updateLivePreviewData();
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
            if (rPrice) rPrice.innerText = window.BLUEZONE_CURRENCY ? window.BLUEZONE_CURRENCY.format(price) : '$' + price.toFixed(2);
            if (rStock) rStock.innerText = (stockOnline + stockOffline) + ' Units (' + stockOnline + ' Online / ' + stockOffline + ' Warehouse)';
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

        // Live Interactive Product Preview Modal
        let activePrevLang = '{{ app()->getLocale() }}';

        function openLiveProductPreview() {
            updateLivePreviewData();
            document.getElementById('liveProductPreviewModal').style.display = 'flex';
        }

        function closeLiveProductPreview() {
            document.getElementById('liveProductPreviewModal').style.display = 'none';
        }

        function switchPreviewLang(lang) {
            activePrevLang = lang;
            const btnEn = document.getElementById('prevLangEnBtn');
            const btnAr = document.getElementById('prevLangArBtn');
            if (lang === 'ar') {
                btnAr.className = 'btn btn-xs btn-primary';
                btnEn.className = 'btn btn-xs btn-ghost';
            } else {
                btnEn.className = 'btn btn-xs btn-primary';
                btnAr.className = 'btn btn-xs btn-ghost';
            }
            updateLivePreviewData();
        }

        function updateLivePreviewData() {
            const isAr = activePrevLang === 'ar';
            const nameEn = document.querySelector('input[name="name_en"]')?.value || 'BLUE FORMULATION';
            const nameAr = document.querySelector('input[name="name_ar"]')?.value || nameEn;
            const sku = document.querySelector('input[name="sku"]')?.value || 'BZ-SKU-001';
            const taglineEn = document.querySelector('input[name="tagline_en"]')?.value || 'Clinical Longevity Support';
            const taglineAr = document.querySelector('input[name="tagline_ar"]')?.value || taglineEn;
            const descEn = document.querySelector('textarea[name="short_description_en"]')?.value || document.querySelector('textarea[name="description_en"]')?.value || 'Bioceutical formulation engineered for optimal longevity and cellular energy.';
            const descAr = document.querySelector('textarea[name="short_description_ar"]')?.value || document.querySelector('textarea[name="description_ar"]')?.value || descEn;
            const price = parseFloat(document.querySelector('input[name="price"]')?.value) || 68.00;
            const stockOffline = parseInt(document.querySelector('input[name="stock_offline"]')?.value) || 50;

            const title = isAr ? nameAr : nameEn;
            const tagline = isAr ? taglineAr : taglineEn;
            const desc = isAr ? descAr : descEn;

            const formatC = (val) => (window.BLUEZONE_CURRENCY ? window.BLUEZONE_CURRENCY.format(val) : ('$' + Number(val).toFixed(2)));

            // Update Card Elements
            document.getElementById('prevCardTitle').innerText = title;
            document.getElementById('prevCardSku').innerText = sku;
            document.getElementById('prevCardPrice').innerText = formatC(price);
            document.getElementById('prevCardStock').innerText = stockOffline + (isAr ? ' وحدة' : ' units');

            // Update Dossier Elements
            document.getElementById('prevDossierTitle').innerText = title;
            document.getElementById('prevDossierTagline').innerText = tagline;
            document.getElementById('prevDossierPrice').innerHTML = formatC(price) + ' <span class="text-xs text-muted" style="font-weight: normal;">' + (isAr ? '(شامل 15% ضريبة)' : '(incl. 15% VAT)') + '</span>';
            document.getElementById('prevDossierDesc').innerText = desc;

            // Existing image URL
            const existingImg = @json($product['image'] ?? 'assets/products/blue-mind.jpg');
            if (existingImg) {
                const fullUrl = existingImg.startsWith('http') ? existingImg : ('/' + existingImg.replace(/^\//, ''));
                document.getElementById('prevCardImg').src = fullUrl;
                document.getElementById('prevDossierImg').src = fullUrl;
            }
        }

        // Stock Intake Modal Functions
        window.openProductStockInModal = function(preselectedLoc = 'online') {
            const modal = document.getElementById('productStockInModal');
            if (modal) {
                if (preselectedLoc) {
                    const locSelect = document.getElementById('stockInLocation');
                    if (locSelect) locSelect.value = preselectedLoc;
                }
                modal.style.display = 'flex';
            }
        };

        window.closeProductStockInModal = function() {
            const modal = document.getElementById('productStockInModal');
            if (modal) modal.style.display = 'none';
            const form = document.getElementById('productStockInForm');
            if (form) form.reset();
            const alertBox = document.getElementById('stockInAlertMsg');
            if (alertBox) {
                alertBox.style.display = 'none';
                alertBox.innerText = '';
            }
        };

        window.setStockInQty = function(qty) {
            const input = document.getElementById('stockInQuantity');
            if (input) {
                input.value = qty;
            }
        };

        window.submitProductStockIn = async function(e) {
            e.preventDefault();
            const btn = document.getElementById('btnSubmitStockIn');
            const spinner = document.getElementById('stockInSpinner');
            const alertBox = document.getElementById('stockInAlertMsg');
            
            const productId = document.getElementById('stockInProductId').value;
            const locationId = document.getElementById('stockInLocation').value;
            const movementType = document.getElementById('stockInType').value;
            const quantity = parseInt(document.getElementById('stockInQuantity').value);
            const referenceNumber = document.getElementById('stockInReference').value;
            const reason = document.getElementById('stockInReason').value;

            if (!quantity || quantity <= 0) {
                alert('{{ app()->getLocale() === 'ar' ? 'يرجى إدخال كمية صحيحة أكبر من الصفر' : 'Please enter a valid quantity greater than zero' }}');
                return;
            }

            if (!reason.trim()) {
                alert('{{ app()->getLocale() === 'ar' ? 'يرجى توضيح سبب الحركة أو البيان لضمان تدقيق السجل' : 'Please provide a reason or note for this movement' }}');
                return;
            }

            btn.disabled = true;
            if (spinner) spinner.style.display = 'inline-block';
            if (alertBox) alertBox.style.display = 'none';

            try {
                const response = await fetch('{{ route('admin.inventory.adjustments.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        location_id: locationId,
                        movement_type: movementType,
                        quantity: quantity,
                        reference_number: referenceNumber,
                        reason: reason
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    if (data.stock_online !== undefined) {
                        const elOnline = document.getElementById('liveStockOnlineDisplay');
                        if (elOnline) elOnline.innerText = data.stock_online;
                        const hiddenOnline = document.getElementById('stock_online');
                        if (hiddenOnline) hiddenOnline.value = data.stock_online;
                    }
                    if (data.stock_offline !== undefined) {
                        const elOffline = document.getElementById('liveStockOfflineDisplay');
                        if (elOffline) elOffline.innerText = data.stock_offline;
                        const hiddenOffline = document.getElementById('stock_offline');
                        if (hiddenOffline) hiddenOffline.value = data.stock_offline;
                    }
                    if (data.stock_central !== undefined) {
                        const elCentral = document.getElementById('liveStockCentralDisplay');
                        if (elCentral) elCentral.innerText = data.stock_central;
                    }
                    if (data.total_stock !== undefined) {
                        const elTotal = document.getElementById('liveStockTotalDisplay');
                        if (elTotal) elTotal.innerText = data.total_stock;
                        
                        const cost = parseFloat(document.getElementById('cost_price')?.value) || 0;
                        const elValuation = document.getElementById('liveStockValuationDisplay');
                        if (elValuation && cost > 0) {
                            const totalVal = data.total_stock * cost;
                            const formatC = (val) => (window.BLUEZONE_CURRENCY ? window.BLUEZONE_CURRENCY.format(val) : ('$' + Number(val).toFixed(2)));
                            elValuation.innerText = formatC(totalVal);
                        }
                    }

                    if (typeof updateReviewSummary === 'function') updateReviewSummary();
                    if (typeof updateLivePreviewData === 'function') updateLivePreviewData();

                    closeProductStockInModal();
                    showStockInToast(data.message || '{{ app()->getLocale() === 'ar' ? 'تم تسجيل حركة المخزون بنجاح!' : 'Inventory movement recorded successfully!' }}');
                } else {
                    if (alertBox) {
                        alertBox.innerText = data.message || '{{ app()->getLocale() === 'ar' ? 'حدث خطأ أثناء تسجيل الحركة' : 'Error recording inventory adjustment' }}';
                        alertBox.style.display = 'block';
                    } else {
                        alert(data.message || 'Error recording inventory adjustment');
                    }
                }
            } catch (err) {
                console.error('Stock adjustment error:', err);
                if (alertBox) {
                    alertBox.innerText = err.message || 'Network error occurred';
                    alertBox.style.display = 'block';
                }
            } finally {
                btn.disabled = false;
                if (spinner) spinner.style.display = 'none';
            }
        };

        function showStockInToast(msg) {
            const toast = document.createElement('div');
            toast.style.position = 'fixed';
            toast.style.bottom = '2rem';
            toast.style.left = '50%';
            toast.style.transform = 'translateX(-50%)';
            toast.style.background = 'var(--color-primary, #0A1128)';
            toast.style.color = '#fff';
            toast.style.padding = '0.85rem 1.75rem';
            toast.style.borderRadius = '30px';
            toast.style.boxShadow = '0 12px 35px rgba(0,0,0,0.25)';
            toast.style.zIndex = '99999';
            toast.style.display = 'flex';
            toast.style.alignItems = 'center';
            toast.style.gap = '0.65rem';
            toast.style.fontWeight = '700';
            toast.style.fontSize = '0.92rem';
            toast.innerHTML = `<i class="fa-solid fa-circle-check" style="color: #10b981; font-size: 1.1rem;"></i> <span>${msg}</span>`;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(-50%) translateY(15px)';
                setTimeout(() => toast.remove(), 500);
            }, 3500);
        }

        document.addEventListener('DOMContentLoaded', () => {
            ['cost_price', 'price', 'sale_price', 'inputCostPrice', 'inputRetailPrice', 'inputSalePrice'].forEach(name => {
                const el = document.getElementById(name) || document.querySelector(`input[name="${name}"]`);
                if (el) {
                    el.addEventListener('input', recalculateTaxAndMargin);
                    el.addEventListener('change', recalculateTaxAndMargin);
                    el.addEventListener('keyup', recalculateTaxAndMargin);
                    el.addEventListener('paste', () => setTimeout(recalculateTaxAndMargin, 50));
                }
            });

            recalculateTaxAndMargin();

            const initialIngredients = @json(old('ingredients', $product['ingredients'] ?? []));
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

            // Sync Primary Image Input Preview
            const primaryInput = document.querySelector('input[name="primary_image"]');
            if (primaryInput) {
                primaryInput.addEventListener('change', function(e) {
                    const file = e.target.files && e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(evt) {
                            const dataUrl = evt.target.result;
                            const cardImg = document.getElementById('prevCardImg');
                            const dossierImg = document.getElementById('prevDossierImg');
                            if (cardImg) cardImg.src = dataUrl;
                            if (dossierImg) dossierImg.src = dataUrl;
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
        });
    </script>

    <!-- Stock In / Quick Adjustment Modal -->
    <div id="productStockInModal" class="pos-modal-backdrop" style="display: none; z-index: 10000; position: fixed; inset: 0; background: rgba(10, 17, 40, 0.7); backdrop-filter: blur(5px); align-items: center; justify-content: center; padding: 1rem;">
        <div class="card" style="max-width: 560px; width: 95%; max-height: 90vh; overflow-y: auto; padding: 1.75rem; border-radius: var(--radius-lg); background: #fff; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); border: 1px solid var(--color-border);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--color-border); padding-bottom: 0.75rem;">
                <div style="display: flex; align-items: center; gap: 0.6rem;">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(16, 185, 129, 0.1); display: flex; align-items: center; justify-content: center; color: var(--color-success);">
                        <i class="fa-solid fa-boxes-stacked" style="font-size: 1.1rem;"></i>
                    </div>
                    <div>
                        <h3 style="margin: 0; font-size: 1.1rem; font-weight: 800;">
                            {{ app()->getLocale() === 'ar' ? 'إدخال وتوريد كميات مخزنية' : 'Quick Stock Intake & Adjustment' }}
                        </h3>
                        <p class="text-xs text-muted" style="margin: 0;">
                            {{ $product['name_' . app()->getLocale()] ?? $product['name_en'] }} (SKU: {{ $product['sku'] }})
                        </p>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-ghost" onclick="closeProductStockInModal()" style="font-size: 1.1rem;">✕</button>
            </div>

            <div id="stockInAlertMsg" class="alert alert-danger" style="display: none; margin-bottom: 1rem; padding: 0.6rem 0.9rem; font-size: 0.85rem; border-radius: var(--radius-md); background: #fee2e2; color: #991b1b; border: 1px solid #f87171;"></div>

            <form id="productStockInForm" onsubmit="submitProductStockIn(event)" style="display: flex; flex-direction: column; gap: 1.1rem;">
                <input type="hidden" id="stockInProductId" value="{{ $product['id'] }}">

                <!-- Destination Location -->
                <div>
                    <label class="font-bold text-xs" style="display: block; margin-bottom: 0.35rem;">
                        <i class="fa-solid fa-warehouse text-primary" style="margin-inline-end: 0.3rem;"></i>
                        {{ app()->getLocale() === 'ar' ? 'المستودع / الوجهة المستهدفة *' : 'Target Hub / Location *' }}
                    </label>
                    <select id="stockInLocation" class="form-control" style="width: 100%; font-size: 0.9rem; padding: 0.55rem 0.75rem;" required>
                        <option value="online">{{ app()->getLocale() === 'ar' ? 'مستودع المتجر الإلكتروني (Online Hub)' : 'E-Commerce Online Hub' }}</option>
                        <option value="offline">{{ app()->getLocale() === 'ar' ? 'مستودع المعرض ونقطة البيع (POS Warehouse)' : 'POS Warehouse' }}</option>
                        <option value="central_wh">{{ app()->getLocale() === 'ar' ? 'المستودع المركزي الرئيسي (Central Warehouse Buffer)' : 'Central Warehouse Buffer' }}</option>
                    </select>
                </div>

                <!-- Movement Type -->
                <div>
                    <label class="font-bold text-xs" style="display: block; margin-bottom: 0.35rem;">
                        <i class="fa-solid fa-tag text-primary" style="margin-inline-end: 0.3rem;"></i>
                        {{ app()->getLocale() === 'ar' ? 'نوع الحركة المحاسبية *' : 'Movement Ledger Type *' }}
                    </label>
                    <select id="stockInType" class="form-control" style="width: 100%; font-size: 0.9rem; padding: 0.55rem 0.75rem;" required>
                        <option value="Stock In" selected>{{ app()->getLocale() === 'ar' ? 'إدخال مخزون جديد / توريد (Stock In)' : 'Stock In / Direct Intake (+)' }}</option>
                        <option value="Return">{{ app()->getLocale() === 'ar' ? 'مرتجع عميل (Customer Return)' : 'Customer Return (+)' }}</option>
                        <option value="Manual Adjustment">{{ app()->getLocale() === 'ar' ? 'تسوية جردية دورية (Manual Adjustment)' : 'Manual Adjustment (+)' }}</option>
                        <option value="Damaged">{{ app()->getLocale() === 'ar' ? 'إهلاك بضاعة تالفة (Damaged Write-off)' : 'Damaged Write-off (-)' }}</option>
                        <option value="Expired">{{ app()->getLocale() === 'ar' ? 'بضاعة منتهية الصلاحية (Expired Write-off)' : 'Expired Write-off (-)' }}</option>
                    </select>
                </div>

                <!-- Quantity with Quick Pills -->
                <div>
                    <label class="font-bold text-xs" style="display: block; margin-bottom: 0.35rem;">
                        <i class="fa-solid fa-cubes text-primary" style="margin-inline-end: 0.3rem;"></i>
                        {{ app()->getLocale() === 'ar' ? 'الكمية المراد إدخالها (بالوحدات) *' : 'Quantity to Add/Adjust (Units) *' }}
                    </label>
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <input type="number" id="stockInQuantity" class="form-control font-black" style="font-size: 1.15rem; width: 140px; text-align: center;" min="1" step="1" value="50" required>
                        <!-- Quick Add Buttons -->
                        <div style="display: flex; gap: 0.3rem; flex-wrap: wrap;">
                            <button type="button" class="btn btn-xs btn-ghost" onclick="setStockInQty(10)" style="border: 1px solid var(--color-border); font-weight: 700;">+10</button>
                            <button type="button" class="btn btn-xs btn-ghost" onclick="setStockInQty(25)" style="border: 1px solid var(--color-border); font-weight: 700;">+25</button>
                            <button type="button" class="btn btn-xs btn-ghost" onclick="setStockInQty(50)" style="border: 1px solid var(--color-border); font-weight: 700;">+50</button>
                            <button type="button" class="btn btn-xs btn-ghost" onclick="setStockInQty(100)" style="border: 1px solid var(--color-border); font-weight: 700;">+100</button>
                            <button type="button" class="btn btn-xs btn-ghost" onclick="setStockInQty(500)" style="border: 1px solid var(--color-border); font-weight: 700;">+500</button>
                        </div>
                    </div>
                </div>

                <!-- Reference / PO Number -->
                <div>
                    <label class="font-bold text-xs" style="display: block; margin-bottom: 0.35rem;">
                        <i class="fa-solid fa-receipt text-primary" style="margin-inline-end: 0.3rem;"></i>
                        {{ app()->getLocale() === 'ar' ? 'رقم أمر الشراء / الفاتورة / التشغيلة (اختياري)' : 'PO / Batch Reference (Optional)' }}
                    </label>
                    <input type="text" id="stockInReference" class="form-control text-xs" placeholder="e.g. PO-2026-09-001 / BATCH-A4" style="width: 100%;">
                </div>

                <!-- Note / Reason -->
                <div>
                    <label class="font-bold text-xs" style="display: block; margin-bottom: 0.35rem;">
                        <i class="fa-solid fa-comment-dots text-primary" style="margin-inline-end: 0.3rem;"></i>
                        {{ app()->getLocale() === 'ar' ? 'بيان وسبب الحركة (إلزامي للتدقيق المالي) *' : 'Audit Note / Reason (Mandatory for ledger) *' }}
                    </label>
                    <input type="text" id="stockInReason" class="form-control text-xs" placeholder="{{ app()->getLocale() === 'ar' ? 'مثال: توريد دفعة تشغيلية جديدة من المصنع' : 'e.g. Received new shipment batch from laboratory' }}" value="{{ app()->getLocale() === 'ar' ? 'توريد كميات جديدة للمنتج' : 'Procured new product inventory batch' }}" required style="width: 100%;">
                </div>

                <!-- Audit Notice -->
                <div style="background: rgba(30, 58, 138, 0.05); border: 1px dashed var(--color-primary); padding: 0.75rem 1rem; border-radius: var(--radius-md); display: flex; gap: 0.6rem; align-items: center;">
                    <i class="fa-solid fa-shield-halved text-primary" style="font-size: 1.1rem; flex-shrink: 0;"></i>
                    <p class="text-xs text-muted" style="margin: 0; line-height: 1.4;">
                        {{ app()->getLocale() === 'ar' ? 'يتم قيد هذه العملية فوراً في سجل حركات المخزون المركزي Inventory Movement Ledger باسم المستخدم وتاريخ اللحظة.' : 'This intake will immediately generate an unalterable movement entry in the central audit ledger.' }}
                    </p>
                </div>

                <!-- Modal Actions -->
                <div style="display: flex; gap: 0.75rem; justify-content: flex-end; margin-top: 0.5rem; border-top: 1px solid var(--color-border); padding-top: 1rem;">
                    <button type="button" class="btn btn-sm btn-ghost" onclick="closeProductStockInModal()">
                        {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                    </button>
                    <button type="submit" id="btnSubmitStockIn" class="btn btn-sm btn-primary font-bold" style="padding: 0.55rem 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                        <span id="stockInSpinner" style="display: none;"><i class="fa-solid fa-spinner fa-spin"></i></span>
                        <i class="fa-solid fa-check"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'تأكيد وإيداع المخزون' : 'Confirm & Post Intake' }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Live Product Interactive Preview Modal -->
    <div id="liveProductPreviewModal" class="pos-modal-backdrop" style="display: none; z-index: 10000; position: fixed; inset: 0; background: rgba(10, 17, 40, 0.7); backdrop-filter: blur(5px); align-items: center; justify-content: center; padding: 1rem;">
        <div class="card" style="max-width: 620px; width: 92%; max-height: 90vh; overflow-y: auto; padding: 1.75rem; border-radius: var(--radius-lg); background: #fff; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); border: 1px solid var(--color-border);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--color-border); padding-bottom: 0.75rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fa-solid fa-wand-magic-sparkles text-primary" style="font-size: 1.15rem;"></i>
                    <h3 style="margin: 0; font-size: 1.1rem; font-weight: 800;">
                        {{ app()->getLocale() === 'ar' ? 'معاينة بطاقة المنتج الحية' : 'Live Product Card & Dossier Preview' }}
                    </h3>
                </div>
                <!-- EN / AR Toggle -->
                <div style="display: flex; gap: 0.35rem; align-items: center;">
                    <div style="display: flex; background: var(--color-bg-subtle); border: 1px solid var(--color-border); border-radius: 20px; padding: 2px;">
                        <button type="button" class="btn btn-xs {{ app()->getLocale() === 'en' ? 'btn-primary' : 'btn-ghost' }}" id="prevLangEnBtn" onclick="switchPreviewLang('en')" style="border-radius: 18px; font-weight: bold; padding: 0.2rem 0.6rem;">EN</button>
                        <button type="button" class="btn btn-xs {{ app()->getLocale() === 'ar' ? 'btn-primary' : 'btn-ghost' }}" id="prevLangArBtn" onclick="switchPreviewLang('ar')" style="border-radius: 18px; font-weight: bold; padding: 0.2rem 0.6rem;">العربية</button>
                    </div>
                    <button type="button" class="btn btn-sm btn-ghost" onclick="closeLiveProductPreview()">✕</button>
                </div>
            </div>

            <!-- Preview Card (Warehouse & E-commerce dual view) -->
            <div style="display: flex; flex-direction: column; gap: 1.25rem;" id="livePreviewContainer">
                
                <!-- Warehouse POS Card Preview -->
                <div style="background: var(--color-bg-subtle); padding: 1.25rem; border-radius: var(--radius-md); border: 1px solid var(--color-border);">
                    <div class="text-xs font-bold text-muted" style="margin-bottom: 0.75rem; text-transform: uppercase;">
                        {{ app()->getLocale() === 'ar' ? 'مظهر المنتج في شاشة الكاشير ونقطة البيع (POS Card):' : 'Warehouse POS Counter Card Appearance:' }}
                    </div>

                    <div class="card" style="max-width: 220px; margin: 0 auto; padding: 1rem; text-align: center; border-radius: var(--radius-lg); border: 2px solid var(--color-primary); box-shadow: 0 10px 25px rgba(10,17,40,0.08); background: #fff;">
                        <div style="width: 80px; height: 80px; margin: 0 auto 0.5rem auto;">
                            <img id="prevCardImg" src="{{ asset($product['image'] ?? 'assets/products/blue-mind.jpg') }}" alt="Preview" style="width: 100%; height: 100%; object-fit: cover; border-radius: var(--radius-md); background: var(--color-bg-subtle);" onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';">
                        </div>
                        <div class="font-bold text-xs" id="prevCardTitle" style="margin-bottom: 0.25rem; height: 32px; overflow: hidden; color: var(--color-text-main);">
                            {{ $product['name_' . app()->getLocale()] ?? $product['name_en'] }}
                        </div>
                        <div class="text-xs text-muted" id="prevCardSku" style="font-family: monospace; font-size: 0.7rem; margin-bottom: 0.4rem;">
                            {{ $product['sku'] ?? 'BZ-SKU' }}
                        </div>
                        <div class="font-black text-sm text-primary" id="prevCardPrice" style="font-weight: 800; border-top: 1px solid var(--color-border); padding-top: 0.4rem;">
                            @currency($product['price'] ?? 68)
                        </div>
                        <div class="text-xs text-muted" style="margin-top: 0.25rem; font-size: 0.68rem;">
                            {{ app()->getLocale() === 'ar' ? 'مخزون المستودع: ' : 'Warehouse Stock: ' }} <strong id="prevCardStock" style="color: var(--color-success);">{{ $product['stock_offline'] ?? 50 }} units</strong>
                        </div>
                    </div>
                </div>

                <!-- Storefront Product Dossier Preview -->
                <div style="background: #fff; padding: 1.25rem; border-radius: var(--radius-md); border: 1px solid var(--color-border);">
                    <div class="text-xs font-bold text-muted" style="margin-bottom: 0.75rem; text-transform: uppercase;">
                        {{ app()->getLocale() === 'ar' ? 'مظهر تفاصيل المنتج في المتجر والكتالوج (Storefront Dossier):' : 'Storefront Clinical Details Appearance:' }}
                    </div>

                    <div style="display: grid; grid-template-columns: 110px 1fr; gap: 1rem; align-items: start;">
                        <img id="prevDossierImg" src="{{ asset($product['image'] ?? 'assets/products/blue-mind.jpg') }}" alt="Preview" style="width: 110px; height: 110px; object-fit: cover; border-radius: var(--radius-md); border: 1px solid var(--color-border);" onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';">
                        <div>
                            <div style="display: flex; gap: 0.35rem; margin-bottom: 0.35rem; flex-wrap: wrap;" id="prevBadges">
                                <span class="badge badge-accent" id="prevCategoryBadge">{{ $product['category_name_en'] ?? 'Cellular Longevity' }}</span>
                                @if(!empty($product['is_featured']))
                                    <span class="badge badge-success" id="prevFeaturedBadge">Featured</span>
                                @endif
                            </div>
                            <h4 id="prevDossierTitle" style="margin: 0 0 0.25rem 0; font-size: 1.05rem; font-weight: 800; color: var(--color-primary);">{{ $product['name_' . app()->getLocale()] ?? $product['name_en'] }}</h4>
                            <p id="prevDossierTagline" class="text-xs text-muted" style="margin: 0 0 0.4rem 0; font-style: italic;">{{ $product['tagline_' . app()->getLocale()] ?? ($product['tagline_en'] ?? '') }}</p>
                            <div class="font-black text-lg text-primary" id="prevDossierPrice">@currency($product['price'] ?? 68) <span class="text-xs text-muted" style="font-weight: normal;">(incl. 15% VAT)</span></div>
                        </div>
                    </div>

                    <!-- Short Description -->
                    <div style="margin-top: 1rem; border-top: 1px dashed var(--color-border); padding-top: 0.75rem;">
                        <div class="text-xs font-bold text-muted" style="margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'الوصف السريع:' : 'Short Description:' }}</div>
                        <p id="prevDossierDesc" class="text-xs text-muted" style="margin: 0; line-height: 1.5;">{{ $product['short_description_' . app()->getLocale()] ?? ($product['short_description_en'] ?? 'Clinical bioceutical formulation engineered for optimal cellular bio-energetics...') }}</p>
                    </div>
                </div>
            </div>

            <div style="margin-top: 1.25rem; text-align: center;">
                <button type="button" class="btn btn-sm btn-primary" onclick="closeLiveProductPreview()" style="width: 100%;">
                    {{ app()->getLocale() === 'ar' ? 'إغلاق المعاينة ومتابعة التعديل' : 'Close Preview & Continue Editing' }}
                </button>
            </div>
        </div>
    </div>
</x-layouts.admin>
