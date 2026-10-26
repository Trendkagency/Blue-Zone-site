<x-layouts.admin 
    :pageTitle="__('admin.products.create_title')" 
    :pageSubtitle="__('admin.products.create_subtitle')"
    :breadcrumbs="[__('admin.menu.products') => route('admin.products.index'), __('app.actions.create') => route('admin.products.create')]"
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
                <span class="step-title">{{ app()->getLocale() == 'ar' ? 'تخصيص الأقسام' : 'Page & Sections' }}</span>
                <span class="step-sub">{{ app()->getLocale() == 'ar' ? 'إعدادات الشاشات' : 'Screen Controls' }}</span>
            </div>
        </div>

        <div class="wizard-step" data-step="7" onclick="goToStep(7)">
            <div class="step-num">7</div>
            <div class="step-info">
                <span class="step-title">{{ __('admin.products.tabs.inventory') }}</span>
                <span class="step-sub">{{ app()->getLocale() == 'ar' ? 'المخزون والمراجعة' : 'Stock & Review' }}</span>
            </div>
        </div>
    </div>

    <!-- Main Creation Form -->
    <form method="POST" action="{{ route('admin.products.store') }}" id="productCreationForm" enctype="multipart/form-data">
        @csrf

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
                            :value="old('sku')" 
                            required 
                        />
                    </div>
                    <div>
                        <x-forms.input 
                            name="barcode" 
                            :label="__('admin.products.fields.barcode')" 
                            :placeholder="__('admin.products.placeholders.barcode')" 
                            :value="old('barcode')" 
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
                            :value="old('subcategory_en')" 
                        />
                    </div>
                    <div>
                        <x-forms.input 
                            name="brand" 
                            :label="__('admin.products.fields.brand')" 
                            :value="old('brand')" 
                            required 
                        />
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem; margin-top: 1rem;">
                    <div>
                        <x-forms.input 
                            name="target_gender" 
                            :label="__('admin.products.fields.target_demographic')" 
                            :value="old('target_gender')" 
                        />
                    </div>
                    <div>
                        <x-forms.input 
                            name="age_group" 
                            :label="__('admin.products.fields.age_cohort')" 
                            :value="old('age_group')" 
                        />
                    </div>
                    <div>
                        <x-forms.input 
                            name="product_size" 
                            :label="__('admin.products.fields.dosage_form')" 
                            :value="old('product_size')" 
                        />
                    </div>
                </div>

                <div style="margin-top: 1.5rem; border-top: 1px solid var(--color-border); padding-top: 1.25rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; flex-wrap: wrap; gap: 0.5rem;">
                        <label for="slug" class="form-label" style="margin-bottom: 0; display: flex; align-items: center; gap: 0.5rem; font-weight: 700;">
                            <span>{{ __('admin.products.fields.slug') ?? 'URL Slug' }}</span>
                            <span class="badge" style="font-size: 0.72rem; padding: 0.2rem 0.6rem; background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; border-radius: 9999px; display: inline-flex; align-items: center; gap: 0.35rem;">
                                <i class="fa-solid fa-lock text-slate-500"></i>
                                {{ app()->getLocale() === 'ar' ? 'توليد تلقائي محمي' : 'Auto-Generated & Locked' }}
                            </span>
                        </label>
                        <div style="font-family: monospace; font-size: 0.78rem; color: var(--color-primary); background: rgba(59, 130, 246, 0.08); padding: 0.25rem 0.65rem; border-radius: 6px; border: 1px solid rgba(59, 130, 246, 0.15);">
                            <span class="text-muted">{{ url('/products') }}/</span><strong id="slugPreviewText">{{ old('slug', 'auto-generated') }}</strong>
                        </div>
                    </div>
                    
                    <div style="position: relative;">
                        <input 
                            type="text" 
                            name="slug" 
                            id="slug" 
                            value="{{ old('slug') }}" 
                            readonly 
                            tabindex="-1"
                            class="form-control" 
                            style="background: #f8fafc; cursor: not-allowed; color: #475569; font-family: monospace; font-weight: 600; border-color: #cbd5e1; padding-left: 2.5rem;"
                            placeholder="{{ app()->getLocale() === 'ar' ? 'يتم التوليد تلقائياً من اسم المنتج...' : 'Auto-generated based on product name...' }}"
                        >
                        <div style="position: absolute; left: 0.85rem; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none;">
                            <i class="fa-solid fa-link"></i>
                        </div>
                    </div>

                    <span class="form-hint" style="display: flex; align-items: center; gap: 0.4rem; margin-top: 0.5rem; font-size: 0.78rem; color: var(--color-text-muted);">
                        <i class="fa-solid fa-shield-halved text-sky-500"></i>
                        {{ app()->getLocale() === 'ar' 
                            ? 'الرابط الدائم للمنتج (Slug) يتم توليده وتأمينه تلقائياً استناداً إلى اسم المنتج باللغة الإنجليزية، ومحمي ضد التعديل اليدوي لضمان استقرار روابط المتجر والسيو (SEO).' 
                            : 'The URL Slug is automatically generated based on the product name and locked against manual editing to protect SEO search rankings and storefront routing.' }}
                    </span>
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
                        <i class="fa-solid fa-language text-sky-500"></i>
                        <span>{{ __('admin.products.sections.arabic_content') }}</span>
                    </h4>

                    <x-forms.input 
                        name="name_ar" 
                        :label="__('admin.products.fields.name_ar')" 
                        :placeholder="__('admin.products.placeholders.name_ar')" 
                        :value="old('name_ar')" 
                        required 
                    />

                    <div style="margin-top: 1rem;">
                        <x-forms.input 
                            name="tagline_ar" 
                            :label="__('admin.products.fields.tagline_ar')" 
                            :placeholder="__('admin.products.placeholders.tagline_ar')" 
                            :value="old('tagline_ar')" 
                        />
                    </div>

                    <div style="margin-top: 1rem;">
                        <x-forms.textarea 
                            name="description_ar" 
                            :label="__('admin.products.fields.description_ar')" 
                            rows="4" 
                            :placeholder="__('admin.products.placeholders.description_ar')" 
                        >{{ old('description_ar') }}</x-forms.textarea>
                    </div>

                    <div style="margin-top: 1rem;">
                        <x-forms.textarea 
                            name="usage_ar" 
                            :label="__('admin.products.fields.usage_ar')" 
                            rows="2" 
                            :placeholder="__('admin.products.placeholders.usage_ar')" 
                        >{{ old('usage_ar') }}</x-forms.textarea>
                    </div>
                </div>

                <!-- English Content (LTR) -->
                <div style="background: var(--color-bg-subtle); padding: 1.75rem; border-radius: var(--radius-lg); border: 1px solid var(--color-border);" dir="ltr">
                    <h4 style="font-size: 1.1rem; font-weight: 800; color: var(--color-primary); margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fa-solid fa-globe text-primary"></i>
                        <span>{{ __('admin.products.sections.english_content') }}</span>
                    </h4>

                    <x-forms.input 
                        name="name_en" 
                        :label="__('admin.products.fields.name_en')" 
                        :placeholder="__('admin.products.placeholders.name_en')" 
                        :value="old('name_en')" 
                        required 
                    />
                    <div style="margin-top: 0.35rem; display: flex; align-items: center; justify-content: space-between; font-size: 0.75rem; color: #64748b; font-family: monospace; background: #f8fafc; padding: 0.35rem 0.65rem; border-radius: 6px; border: 1px solid #e2e8f0;">
                        <span style="display: flex; align-items: center; gap: 0.4rem;">
                            <i class="fa-solid fa-link text-sky-500"></i>
                            <span class="text-slate-400">{{ url('/products') }}/</span><strong id="step2SlugPreview" class="text-sky-600 font-bold">{{ old('slug', 'auto-generated') }}</strong>
                        </span>
                        <span class="badge" style="font-size: 0.65rem; padding: 0.1rem 0.45rem; background: #e2e8f0; color: #475569; border-radius: 4px;">{{ app()->getLocale() === 'ar' ? 'مقفل تلقائياً' : 'Auto-Locked' }}</span>
                    </div>

                    <div style="margin-top: 1rem;">
                        <x-forms.input 
                            name="tagline_en" 
                            :label="__('admin.products.fields.tagline_en')" 
                            :placeholder="__('admin.products.placeholders.tagline_en')" 
                            :value="old('tagline_en')" 
                        />
                    </div>

                    <div style="margin-top: 1rem;">
                        <x-forms.textarea 
                            name="description_en" 
                            :label="__('admin.products.fields.description_en')" 
                            rows="4" 
                            :placeholder="__('admin.products.placeholders.description_en')" 
                        >{{ old('description_en') }}</x-forms.textarea>
                    </div>

                    <div style="margin-top: 1rem;">
                        <x-forms.textarea 
                            name="usage_en" 
                            :label="__('admin.products.fields.usage_en')" 
                            rows="2" 
                            :placeholder="__('admin.products.placeholders.usage_en')" 
                        >{{ old('usage_en') }}</x-forms.textarea>
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
                            :value="old('cost_price')" 
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
                            :value="old('price')" 
                            required 
                            oninput="recalculateTaxAndMargin()"
                        />
                    </div>
                </div>

                <!-- Dynamic Promotional Discount Module -->
                <div style="margin-top: 1.5rem; background: var(--color-bg-subtle, #f8fafc); border: 1px solid var(--color-border); border-radius: var(--radius-lg, 16px); padding: 1.5rem; transition: all 0.3s ease;">
                    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <div style="font-weight: 800; font-size: 1rem; color: var(--color-primary); display: flex; align-items: center; gap: 0.5rem;">
                                <i class="fa-solid fa-tags text-rose-500"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'العرض الترويجي والخصم الخاص' : 'Promotional Campaign & Discount' }}</span>
                                <span class="badge" id="discountLiveBadge" style="display: none; font-size: 0.72rem; padding: 0.2rem 0.65rem; background: linear-gradient(135deg, #dc2626, #f43f5e); color: white; border-radius: 9999px; font-weight: 800; box-shadow: 0 4px 10px rgba(220, 38, 38, 0.25);">
                                    -0% OFF
                                </span>
                            </div>
                            <p class="text-xs text-muted" style="margin: 0.25rem 0 0 0;">
                                {{ app()->getLocale() === 'ar' ? 'تفعيل خصم مباشر على التركيبة يظهر كشارة وشطب للسعر القديم في كافة بطاقات وأقسام المتجر' : 'Enable a special promotional discount synced across all product cards with dynamic discount badges' }}
                            </p>
                        </div>

                        <!-- Toggle Switch -->
                        <label style="display: inline-flex; align-items: center; gap: 0.75rem; cursor: pointer;">
                            <span class="text-xs font-bold" id="discountToggleLabel" style="color: var(--color-text-muted);">
                                {{ old('sale_price') ? (app()->getLocale() === 'ar' ? 'الخصم مفعل' : 'Discount Active') : (app()->getLocale() === 'ar' ? 'الخصم معطل' : 'Discount Disabled') }}
                            </span>
                            <div style="position: relative; width: 48px; height: 26px;">
                                <input type="checkbox" id="toggleDiscountEnabled" onchange="handleDiscountToggle(this.checked)" style="opacity: 0; width: 0; height: 0;" {{ old('sale_price') ? 'checked' : '' }}>
                                <div id="discountToggleTrack" style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: {{ old('sale_price') ? '#10b981' : '#cbd5e1' }}; transition: .3s; border-radius: 34px;">
                                    <div id="discountToggleThumb" style="position: absolute; height: 20px; width: 20px; left: {{ old('sale_price') ? '25px' : '3px' }}; bottom: 3px; background-color: white; transition: .3s; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.2);"></div>
                                </div>
                            </div>
                        </label>
                    </div>

                    <!-- Discount Configuration Fields (Shown when toggle is ON) -->
                    <div id="discountControlsContainer" style="display: {{ old('sale_price') ? 'block' : 'none' }}; border-top: 1px dashed var(--color-border); padding-top: 1.25rem;">
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; align-items: end;">
                            
                            <!-- Mode Selector (Percentage vs Fixed) -->
                            <div>
                                <label class="form-label" style="font-size: 0.8rem; font-weight: 700; margin-bottom: 0.5rem; display: block;">
                                    {{ app()->getLocale() === 'ar' ? 'نوع الخصم' : 'Discount Calculation Mode' }}
                                </label>
                                <div style="display: flex; background: var(--color-bg-surface, #fff); border: 1px solid var(--color-border); border-radius: var(--radius-md, 8px); padding: 3px; gap: 4px;">
                                    <button type="button" id="btnDiscountModePct" onclick="setDiscountMode('percentage')" class="btn btn-sm" style="flex: 1; border: none; font-size: 0.78rem; font-weight: 700; border-radius: 6px; padding: 0.4rem; background: var(--color-primary); color: #fff;">
                                        <i class="fa-solid fa-percent" style="margin-right: 0.25rem;"></i> {{ app()->getLocale() === 'ar' ? 'نسبة مئوية (%)' : 'Percentage (%)' }}
                                    </button>
                                    <button type="button" id="btnDiscountModeFixed" onclick="setDiscountMode('fixed')" class="btn btn-sm btn-ghost" style="flex: 1; border: none; font-size: 0.78rem; font-weight: 700; border-radius: 6px; padding: 0.4rem; color: var(--color-text-muted);">
                                        <i class="fa-solid fa-dollar-sign" style="margin-right: 0.25rem;"></i> {{ app()->getLocale() === 'ar' ? 'مبلغ ثابت' : 'Fixed Amount' }}
                                    </button>
                                </div>
                            </div>

                            <!-- Discount Rate / Value Input -->
                            <div>
                                <label class="form-label" id="labelDiscountRate" style="font-size: 0.8rem; font-weight: 700; margin-bottom: 0.5rem; display: block;">
                                    {{ app()->getLocale() === 'ar' ? 'قيمة نسبة الخصم (%)' : 'Discount Percentage (%)' }}
                                </label>
                                <div style="position: relative;">
                                    <input 
                                        type="number" 
                                        id="inputDiscountRate" 
                                        step="0.5" 
                                        min="1" 
                                        max="95" 
                                        placeholder="e.g. 20" 
                                        class="form-control" 
                                        style="font-family: monospace; font-weight: 700; font-size: 0.95rem; padding-left: 2.25rem;"
                                        oninput="onDiscountRateInput(this.value)"
                                    >
                                    <span id="discountRateSymbol" style="position: absolute; left: 0.85rem; top: 50%; transform: translateY(-50%); font-weight: 800; color: #94a3b8; font-size: 0.9rem;">%</span>
                                </div>
                            </div>

                            <!-- Resulting Sale Price Input (Bidirectional) -->
                            <div>
                                <label class="form-label" style="font-size: 0.8rem; font-weight: 700; margin-bottom: 0.5rem; display: block;">
                                    <span>{{ __('admin.products.fields.sale_price') }}</span>
                                    <span class="text-xs text-muted" style="font-weight: normal;">({{ app()->getLocale() === 'ar' ? 'السعر النهائي بعد الخصم' : 'Final Price to Customer' }})</span>
                                </label>
                                <div style="position: relative;">
                                    <input 
                                        type="number" 
                                        name="sale_price" 
                                        id="inputSalePrice" 
                                        step="0.01" 
                                        min="0" 
                                        placeholder="0.00" 
                                        value="{{ old('sale_price') }}" 
                                        class="form-control" 
                                        style="font-family: monospace; font-weight: 800; color: #16a34a; font-size: 1.05rem; padding-left: 2.25rem;"
                                        oninput="onSalePriceInput(this.value)"
                                    >
                                    <span style="position: absolute; left: 0.85rem; top: 50%; transform: translateY(-50%); font-weight: 800; color: #16a34a; font-size: 0.95rem;">$</span>
                                </div>
                            </div>

                        </div>

                        <!-- Savings Feedback Bar -->
                        <div id="savingsFeedbackBar" style="display: none; margin-top: 1rem; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 8px; padding: 0.65rem 1rem; color: #065f46; font-size: 0.82rem; font-weight: 600; align-items: center; justify-content: space-between;">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <i class="fa-solid fa-circle-check text-emerald-600"></i>
                                <span id="savingsFeedbackText">Customer saves $0.00 (0% OFF)</span>
                            </div>
                            <span class="badge" style="background: #10b981; color: white; font-size: 0.7rem; font-weight: 800; padding: 0.15rem 0.5rem; border-radius: 9999px;">
                                ACTIVE PROMO
                            </span>
                        </div>
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
                            <div class="text-xs text-muted" style="margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'ضريبة القيمة المضافة (15%)' : 'VAT Tax Amount' }}</div>
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
                            {{ app()->getLocale() === 'ar' ? 'الخطوة 4 من 6: الصورة الرئيسية ومعرض صور المنتج والعبوة' : 'Step 4 of 6: Primary formulation render and multi-angle packaging gallery' }}
                        </p>
                    </div>
                    <span class="badge badge-accent">{{ app()->getLocale() === 'ar' ? 'خطوة 4 / 6' : 'Step 4 / 6' }}</span>
                </div>

                <div class="space-y-8">
                    <!-- 1. Primary Formulation Visual Studio -->
                    <div class="p-6 rounded-2xl bg-gradient-to-br from-slate-50 to-slate-100/80 dark:from-slate-900/60 dark:to-slate-900/30 border border-slate-200 dark:border-slate-800">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-xl bg-sky-500/10 text-sky-600 dark:bg-sky-500/20 dark:text-sky-400 flex items-center justify-center font-bold">
                                    <i class="fa-solid fa-star"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider">
                                        {{ app()->getLocale() === 'ar' ? 'الصورة الرئيسية للمنتج (Hero Render)' : 'Primary Product Hero Image' }}
                                    </h4>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">
                                        {{ app()->getLocale() === 'ar' ? 'الصورة الأساسية التي تظهر في المتجر وكتالوج المنتجات وصفحة الهبوط' : 'Main image displayed across catalog, search, and storefront landing' }}
                                    </p>
                                </div>
                            </div>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-sky-500/10 text-sky-600 dark:text-sky-300">
                                <i class="fa-solid fa-sparkles text-[10px]"></i>
                                {{ app()->getLocale() === 'ar' ? 'صورة الغلاف' : 'Primary Cover' }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
                            <!-- Hero Preview Card -->
                            <div class="md:col-span-4 flex flex-col items-center">
                                <div class="relative w-48 h-48 rounded-2xl border-2 border-dashed border-sky-400/40 bg-white dark:bg-slate-950 p-2 shadow-sm overflow-hidden flex items-center justify-center group">
                                    <img id="primaryHeroLiveImg" 
                                         src="{{ asset(old('image', 'assets/products/blue-mind.webp')) }}" 
                                         alt="Product Hero Preview" 
                                         class="w-full h-full object-contain rounded-xl transition-all duration-300 group-hover:scale-105"
                                         onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';">
                                    <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center rounded-xl pointer-events-none">
                                        <span class="px-2.5 py-1 bg-white/90 dark:bg-slate-900/90 rounded-lg text-[11px] font-bold text-slate-800 dark:text-slate-200 shadow">
                                            {{ app()->getLocale() === 'ar' ? 'معاينة حية' : 'Live Preview' }}
                                        </span>
                                    </div>
                                </div>
                                <span class="text-[11px] text-slate-400 dark:text-slate-500 mt-2 text-center">
                                    {{ app()->getLocale() === 'ar' ? 'نسبة 1:1 مربعة بدقة عالية' : '1:1 Square recommended' }}
                                </span>
                            </div>

                            <!-- Upload & Fallback Controls -->
                            <div class="md:col-span-8 space-y-4">
                                <!-- File Upload Trigger Card -->
                                <div class="relative border-2 border-dashed border-slate-300 dark:border-slate-700 hover:border-sky-500 dark:hover:border-sky-400 bg-white dark:bg-slate-950/60 rounded-2xl p-6 text-center cursor-pointer transition-all group"
                                     onclick="document.getElementById('primary_image_file').click()"
                                     ondragover="event.preventDefault(); this.classList.add('border-sky-500')"
                                     ondragleave="this.classList.remove('border-sky-500')"
                                     ondrop="handlePrimaryDrop(event)">
                                    <input type="file" 
                                           name="primary_image" 
                                           id="primary_image_file" 
                                           accept="image/jpeg,image/png,image/webp,image/gif" 
                                           class="sr-only" 
                                           onchange="previewPrimaryProductImage(this)">
                                    <div class="w-12 h-12 rounded-2xl bg-sky-500/10 text-sky-600 dark:bg-sky-500/20 dark:text-sky-400 flex items-center justify-center mx-auto mb-3 text-xl group-hover:scale-110 transition-transform">
                                        <i class="fa-solid fa-cloud-arrow-up"></i>
                                    </div>
                                    <div class="text-sm font-bold text-slate-800 dark:text-slate-100 mb-1">
                                        {{ app()->getLocale() === 'ar' ? 'انقر لرفع صورة جديدة أو اسحبها وأفلتها هنا' : 'Click to browse new image or drag & drop here' }}
                                    </div>
                                    <p class="text-xs text-slate-400">
                                        WebP, PNG, JPG ({{ app()->getLocale() === 'ar' ? 'حتى 10 ميغابايت' : 'up to 10MB' }})
                                    </p>
                                    <button type="button" class="mt-3 px-3.5 py-1.5 rounded-xl text-xs font-bold bg-sky-600 hover:bg-sky-700 text-white transition-all shadow-xs inline-flex items-center gap-1.5">
                                        <i class="fa-solid fa-folder-open text-[11px]"></i>
                                        {{ app()->getLocale() === 'ar' ? 'اختيار ملف من جهازك' : 'Choose Local File' }}
                                    </button>
                                </div>

                                <!-- File details banner when file selected -->
                                <div id="primaryFileDetails" class="hidden p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-xs text-emerald-700 dark:text-emerald-300 flex items-center gap-2">
                                    <i class="fa-solid fa-circle-check text-emerald-500"></i>
                                    <span id="primaryFileName" class="font-mono font-bold"></span>
                                </div>

                                <!-- Direct Path / Fallback URL Input -->
                                <div class="pt-2">
                                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1">
                                        {{ app()->getLocale() === 'ar' ? 'أو أدخل مسار الصورة / الرابط المباشر:' : 'Or direct asset path / URL:' }}
                                    </label>
                                    <div class="flex gap-2">
                                        <input type="text" 
                                               name="image" 
                                               id="primary_image_url_input"
                                               value="{{ old('image', 'assets/products/blue-mind.webp') }}" 
                                               placeholder="e.g. assets/products/blue-mind.webp" 
                                               class="w-full text-xs font-mono rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-900 px-3 py-2 text-slate-800 dark:text-slate-200"
                                               oninput="syncPrimaryFromTextInput(this.value)">
                                    </div>
                                    <span class="text-[11px] text-slate-400 block mt-1">
                                        {{ app()->getLocale() === 'ar' ? 'يتم استخدامه كمسار افتراضي للمنتج في حالة عدم رفع ملف جديد.' : 'Serves as fallback path if no file is uploaded.' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Multi-Angle Packaging Gallery Studio -->
                    <div class="p-6 rounded-2xl bg-gradient-to-br from-slate-50 to-slate-100/80 dark:from-slate-900/60 dark:to-slate-900/30 border border-slate-200 dark:border-slate-800">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 rounded-xl bg-purple-500/10 text-purple-600 dark:bg-purple-500/20 dark:text-purple-400 flex items-center justify-center font-bold">
                                    <i class="fa-solid fa-images"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-wider">
                                        {{ app()->getLocale() === 'ar' ? 'معرض صور المنتج وزوايا العبوة (Product Gallery)' : 'Multi-Angle Gallery & Packaging Shots' }}
                                    </h4>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">
                                        {{ app()->getLocale() === 'ar' ? 'أضف صوراً تفاعلية لزوايا المنتج ومعلومات الحقائق الغذائية والشهادات' : 'Upload secondary angles, clinical facts panel, and box renderings' }}
                                    </p>
                                </div>
                            </div>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-purple-500/10 text-purple-600 dark:text-purple-300">
                                <i class="fa-solid fa-layer-group text-[10px]"></i>
                                {{ app()->getLocale() === 'ar' ? 'صور متعددة' : 'Multi-Image' }}
                            </span>
                        </div>

                        <!-- Multi-File Drag & Drop Upload Zone for Gallery Photos -->
                        <div class="space-y-3">
                            <div class="relative border-2 border-dashed border-slate-300 dark:border-slate-700 hover:border-purple-500 dark:hover:border-purple-400 bg-white dark:bg-slate-950/60 rounded-2xl p-6 text-center cursor-pointer transition-all group"
                                 onclick="document.getElementById('gallery_files_input').click()"
                                 ondragover="event.preventDefault(); this.classList.add('border-purple-500')"
                                 ondragleave="this.classList.remove('border-purple-500')"
                                 ondrop="handleGalleryDrop(event)">
                                <input type="file" 
                                       name="gallery[]" 
                                       id="gallery_files_input" 
                                       multiple 
                                       accept="image/jpeg,image/png,image/webp,image/gif" 
                                       class="sr-only" 
                                       onchange="previewNewGalleryFiles(this)">
                                
                                <div class="w-12 h-12 rounded-2xl bg-purple-500/10 text-purple-600 dark:bg-purple-500/20 dark:text-purple-300 flex items-center justify-center mx-auto mb-3 text-xl group-hover:scale-110 transition-transform">
                                    <i class="fa-solid fa-photo-film"></i>
                                </div>
                                <div class="text-sm font-bold text-slate-800 dark:text-slate-100 mb-1">
                                    {{ app()->getLocale() === 'ar' ? 'انقر لاختيار صور متعددة للمعرض أو اسحب وأفلت الملفات هنا' : 'Click to select multiple gallery photos or drag & drop here' }}
                                </div>
                                <p class="text-xs text-slate-400">
                                    {{ app()->getLocale() === 'ar' ? 'يمكنك اختيار عدة ملفات دفعة واحدة (WebP, PNG, JPG حتى 10 ميغابايت لكل صورة)' : 'You can select multiple files at once (WebP, PNG, JPG up to 10MB each)' }}
                                </p>
                                <button type="button" class="mt-3 px-3.5 py-1.5 rounded-xl text-xs font-bold bg-purple-600 hover:bg-purple-700 text-white transition-all shadow-xs inline-flex items-center gap-1.5">
                                    <i class="fa-solid fa-images text-[11px]"></i>
                                    {{ app()->getLocale() === 'ar' ? 'اختيار صور المعرض' : 'Select Gallery Photos' }}
                                </button>
                            </div>

                            <!-- Live Previews of Newly Selected Gallery Photos -->
                            <div id="newGalleryPreviewSection" class="hidden mt-4 p-4 rounded-xl bg-purple-500/5 border border-purple-500/20">
                                <div class="flex items-center justify-between mb-3 text-xs font-bold text-slate-800 dark:text-slate-200">
                                    <span class="flex items-center gap-1.5">
                                        <i class="fa-solid fa-circle-check text-emerald-500"></i>
                                        <span id="newGalleryCountText">0 new images ready to upload</span>
                                    </span>
                                    <button type="button" onclick="clearNewGalleryFiles()" class="text-xs text-rose-500 hover:underline">
                                        {{ app()->getLocale() === 'ar' ? 'إلغاء التحديد' : 'Clear selection' }}
                                    </button>
                                </div>
                                <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-3" id="newGalleryPreviewsGrid"></div>
                            </div>
                        </div>
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

        <!-- STEP 6: Page & Section Customizer (Screen & Layout Settings) -->
        <div class="wizard-step-pane" id="step-pane-6" style="display: none;">
            @include('admin.products.partials.section_customizer_step')
        </div>

        <!-- STEP 7: Inventory & Controls & Summary Review -->
        <div class="wizard-step-pane" id="step-pane-7" style="display: none;">
            <div style="display: flex; flex-direction: column; gap: 2rem;">
                <!-- Inventory Limits Card -->
                <div class="card" style="padding: 2.25rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;">
                        <div>
                            <h3 style="font-size: 1.25rem; font-weight: 800; margin: 0 0 0.25rem 0;">
                                {{ __('admin.products.sections.inventory_control') }}
                            </h3>
                            <p class="text-xs text-muted" style="margin: 0;">
                                {{ app()->getLocale() === 'ar' ? 'الخطوة 7 من 7: تحديد كميات المخزون الأولي والتنبيهات' : 'Step 7 of 7: Inventory buffer allocation and replenishment thresholds' }}
                            </p>
                        </div>
                        <span class="badge badge-accent">{{ app()->getLocale() === 'ar' ? 'خطوة 7 / 7' : 'Step 7 / 7' }}</span>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem;">
                        <x-forms.input 
                            name="stock_online" 
                            type="number" 
                            min="0"
                            :label="__('admin.products.fields.online_stock')" 
                            :value="old('stock_online')" 
                            required 
                        />

                        <x-forms.input 
                            name="stock_offline" 
                            type="number" 
                            min="0"
                            :label="__('admin.products.fields.offline_stock')" 
                            :value="old('stock_offline')" 
                            required 
                        />

                        <x-forms.input 
                            name="low_stock_threshold" 
                            type="number" 
                            min="1"
                            :label="__('admin.products.fields.low_stock_threshold')" 
                            :value="old('low_stock_threshold')" 
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

                <!-- Landing Page Display & Showcase Configuration Card -->
                <div class="card" style="padding: 2rem; border: 1.5px solid rgba(10, 79, 120, 0.25); background: linear-gradient(135deg, rgba(10, 79, 120, 0.03), var(--color-bg)); border-radius: var(--radius-lg); position: relative; overflow: hidden;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem; flex-wrap: wrap; gap: 0.75rem;">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 2.5rem; height: 2.5rem; border-radius: var(--radius-md); background: rgba(10, 79, 120, 0.1); color: var(--color-primary); display: flex; align-items: center; justify-content: center; font-size: 1.15rem;">
                                <i class="fa-solid fa-house-chimney-window"></i>
                            </div>
                            <div>
                                <h4 style="font-size: 1.1rem; font-weight: 800; margin: 0; color: var(--color-text-emphasis);">
                                    {{ app()->getLocale() === 'ar' ? 'إعدادات الظهور في الصفحة الرئيسية (Landing Page Placement)' : 'Landing Page Showcase Configuration' }}
                                </h4>
                                <p class="text-xs text-muted" style="margin: 0.15rem 0 0 0;">
                                    {{ app()->getLocale() === 'ar' ? 'تحكّم في إظهار أو إخفاء هذا المستحضر بالصفحة الرئيسية وترتيب ظهوره وشاراته المميزة' : 'Control whether this formulation is visible in the landing page products section, display priority, and custom badge' }}
                                </p>
                            </div>
                        </div>
                        <span class="badge badge-success text-xs font-bold" id="landingVisibilityBadge">
                            <i class="fa-solid fa-eye mr-1 ml-1"></i>
                            {{ app()->getLocale() === 'ar' ? 'معروض بالواجهة (افتراضي)' : 'Visible on Landing (Default)' }}
                        </span>
                    </div>

                    <!-- Master Switch for this Product -->
                    <div style="background: var(--color-bg-subtle); padding: 1.25rem; border-radius: var(--radius-md); border: 1px solid var(--color-border); margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
                        <div>
                            <strong style="font-size: 0.95rem; color: var(--color-text-emphasis); display: block; margin-bottom: 0.25rem;">
                                {{ app()->getLocale() === 'ar' ? 'عرض هذا المنتج في الصفحة الرئيسية (Landing Page Products Section)' : 'Display this formulation in the Landing Page Products section' }}
                            </strong>
                            <p class="text-xs text-muted" style="margin: 0;">
                                {{ app()->getLocale() === 'ar' ? 'عند التفعيل، سيتم إدراج المستحضر في قسم المنتجات بالصفحة الرئيسية للموقع بناءً على الترتيب المحدد' : 'When enabled, this formulation is included in the landing page formulations showcase.' }}
                            </p>
                        </div>
                        <div style="flex-shrink: 0;">
                            <x-forms.toggle 
                                name="show_on_landing" 
                                :label="app()->getLocale() === 'ar' ? 'إظهار في الصفحة الرئيسية' : 'Show on Landing'" 
                                :checked="old('show_on_landing', true)" 
                            />
                        </div>
                    </div>

                    <!-- Priority & Custom Badges (Fully Responsive) -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <x-forms.input 
                                name="landing_order" 
                                type="number" 
                                min="0"
                                :label="app()->getLocale() === 'ar' ? 'ترتيب الظهور في الواجهة' : 'Landing Display Priority (Order)'" 
                                :value="old('landing_order', 0)" 
                                hint="الأرقام الأقل تظهر أولاً (0 = الأولوية الأولى)."
                            />
                        </div>
                        <div>
                            <x-forms.input 
                                name="landing_badge_en" 
                                :label="app()->getLocale() === 'ar' ? 'شارة مخصصة بالإنجليزية (EN)' : 'Custom Landing Badge (EN)'" 
                                :value="old('landing_badge_en', '')" 
                                placeholder="e.g. Flagship Cellular Bioactive"
                                hint="تظهر كشارة عائمة أعلى صورة المنتج في الواجهة."
                            />
                        </div>
                        <div>
                            <x-forms.input 
                                name="landing_badge_ar" 
                                :label="app()->getLocale() === 'ar' ? 'شارة مخصصة بالعربية (AR)' : 'Custom Landing Badge (AR)'" 
                                :value="old('landing_badge_ar', '')" 
                                placeholder="مثال: التركيبة الخلوية الرائدة"
                                hint="تظهر كشارة عائمة أعلى صورة المنتج في الواجهة."
                            />
                        </div>
                    </div>

                    <!-- Link to Hero Carousel Manager -->
                    <div style="margin-top: 1.25rem; padding: 1rem 1.25rem; border-radius: var(--radius-md); background: rgba(10, 79, 120, 0.06); border: 1px dashed rgba(10, 79, 120, 0.25); display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <i class="fa-solid fa-images text-lg text-primary"></i>
                            <div>
                                <strong style="font-size: 0.85rem; color: var(--color-text-emphasis); display: block;">
                                    {{ app()->getLocale() === 'ar' ? 'سلايدر الواجهة الرئيسي وبطاقات العرض العائمة' : 'Hero Carousel & Floating Showcase Cards' }}
                                </strong>
                                <span class="text-xs text-muted">
                                    {{ app()->getLocale() === 'ar' ? 'يمكنك ربط هذا المنتج بأي شريحة في السلايدر العلوي للواجهة مع بطاقته العائمة' : 'You can feature this formulation in the top hero slider with a custom floating card' }}
                                </span>
                            </div>
                        </div>
                        <a href="{{ route('admin.hero-slides.index') }}" class="btn btn-secondary btn-sm font-bold">
                            <i class="fa-solid fa-sliders mr-1 ml-1 text-sky-500"></i>
                            {{ app()->getLocale() === 'ar' ? 'إدارة شرائح السلايدر ➔' : 'Manage Hero Carousel ➔' }}
                        </a>
                    </div>
                </div>

                <!-- Final Verification & Review Card -->
                <div class="card" style="padding: 2.25rem; background: var(--color-bg-subtle); border: 1px solid var(--color-primary);">
                    <h4 style="font-size: 1.15rem; font-weight: 800; color: var(--color-primary); margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fa-solid fa-clipboard-check text-primary"></i>
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

    {{-- Persistent Floating Real-Time Action Bar --}}
    @include('admin.products.partials.floating_wizard_bar', ['mode' => 'create', 'productId' => null, 'formId' => 'productCreationForm'])

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
        const totalSteps = 7;
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

            // If Step 7, update review summary
            if (step === 7) {
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

        let discountMode = 'percentage';

        function handleDiscountToggle(enabled) {
            const container = document.getElementById('discountControlsContainer');
            const track = document.getElementById('discountToggleTrack');
            const thumb = document.getElementById('discountToggleThumb');
            const label = document.getElementById('discountToggleLabel');
            const saleInput = document.getElementById('inputSalePrice');
            const isAr = "{{ app()->getLocale() }}" === 'ar';

            if (enabled) {
                container.style.display = 'block';
                track.style.backgroundColor = '#10b981';
                thumb.style.left = '25px';
                label.textContent = isAr ? 'الخصم مفعل' : 'Discount Active';
                label.style.color = '#10b981';
                if (!saleInput.value || parseFloat(saleInput.value) <= 0) {
                    document.getElementById('inputDiscountRate').value = '15';
                    onDiscountRateInput(15);
                } else {
                    syncDiscountRateFromSalePrice();
                }
            } else {
                container.style.display = 'none';
                track.style.backgroundColor = '#cbd5e1';
                thumb.style.left = '3px';
                label.textContent = isAr ? 'الخصم معطل' : 'Discount Disabled';
                label.style.color = 'var(--color-text-muted)';
                saleInput.value = '';
                document.getElementById('inputDiscountRate').value = '';
                document.getElementById('discountLiveBadge').style.display = 'none';
                document.getElementById('savingsFeedbackBar').style.display = 'none';
                recalculateTaxAndMargin();
            }
        }

        function setDiscountMode(mode) {
            discountMode = mode;
            const btnPct = document.getElementById('btnDiscountModePct');
            const btnFixed = document.getElementById('btnDiscountModeFixed');
            const label = document.getElementById('labelDiscountRate');
            const symbol = document.getElementById('discountRateSymbol');
            const isAr = "{{ app()->getLocale() }}" === 'ar';

            if (mode === 'percentage') {
                btnPct.style.background = 'var(--color-primary)';
                btnPct.style.color = '#fff';
                btnFixed.style.background = 'transparent';
                btnFixed.style.color = 'var(--color-text-muted)';
                label.textContent = isAr ? 'قيمة نسبة الخصم (%)' : 'Discount Percentage (%)';
                symbol.textContent = '%';
            } else {
                btnFixed.style.background = 'var(--color-primary)';
                btnFixed.style.color = '#fff';
                btnPct.style.background = 'transparent';
                btnPct.style.color = 'var(--color-text-muted)';
                label.textContent = isAr ? 'مبلغ الخصم المالي المباشر ($)' : 'Discount Amount ($)';
                symbol.textContent = '$';
            }

            syncDiscountRateFromSalePrice();
        }

        function onDiscountRateInput(val) {
            const retail = parseFloat(document.getElementById('inputRetailPrice')?.value) || 0;
            const saleInput = document.getElementById('inputSalePrice');
            const rate = parseFloat(val) || 0;

            if (retail <= 0 || rate <= 0) {
                updateSavingsFeedback(0, 0);
                return;
            }

            let calculatedSale = 0;
            if (discountMode === 'percentage') {
                const clampedPct = Math.min(99, Math.max(0, rate));
                calculatedSale = retail * (1 - (clampedPct / 100));
            } else {
                const clampedFixed = Math.min(retail - 0.01, Math.max(0, rate));
                calculatedSale = Math.max(0.01, retail - clampedFixed);
            }

            if (saleInput) {
                saleInput.value = calculatedSale.toFixed(2);
            }

            const savings = Math.max(0, retail - calculatedSale);
            const pct = retail > 0 ? Math.round((savings / retail) * 100) : 0;
            updateSavingsFeedback(savings, pct);
            recalculateTaxAndMargin();
        }

        function onSalePriceInput(val) {
            const retail = parseFloat(document.getElementById('inputRetailPrice')?.value) || 0;
            const sale = parseFloat(val) || 0;

            if (retail <= 0 || sale <= 0 || sale >= retail) {
                document.getElementById('inputDiscountRate').value = '';
                updateSavingsFeedback(0, 0);
                recalculateTaxAndMargin();
                return;
            }

            const savings = retail - sale;
            const pct = Math.round((savings / retail) * 100);

            const rateInput = document.getElementById('inputDiscountRate');
            if (rateInput) {
                rateInput.value = discountMode === 'percentage' ? pct : savings.toFixed(2);
            }

            updateSavingsFeedback(savings, pct);
            recalculateTaxAndMargin();
        }

        function syncDiscountRateFromSalePrice() {
            const saleInput = document.getElementById('inputSalePrice');
            if (saleInput && saleInput.value) {
                onSalePriceInput(saleInput.value);
            }
        }

        function updateSavingsFeedback(savings, pct) {
            const bar = document.getElementById('savingsFeedbackBar');
            const text = document.getElementById('savingsFeedbackText');
            const badge = document.getElementById('discountLiveBadge');
            const isAr = "{{ app()->getLocale() }}" === 'ar';

            if (savings > 0 && pct > 0) {
                if (bar) bar.style.display = 'flex';
                if (badge) {
                    badge.style.display = 'inline-flex';
                    badge.textContent = isAr ? `خصم ${pct}%` : `-${pct}% OFF`;
                }
                if (text) {
                    text.textContent = isAr 
                        ? `يوفر العميل $${savings.toFixed(2)} (${pct}% خصم ترويجي)`
                        : `Customer saves $${savings.toFixed(2)} (${pct}% promotional discount)`;
                }
            } else {
                if (bar) bar.style.display = 'none';
                if (badge) badge.style.display = 'none';
            }
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

            // Direct Fallback Image sync
            const fallbackPath = document.querySelector('input[name="image"]')?.value;
            if (fallbackPath) {
                const fullUrl = fallbackPath.startsWith('http') ? fallbackPath : ('/' + fallbackPath.replace(/^\//, ''));
                document.getElementById('prevCardImg').src = fullUrl;
                document.getElementById('prevDossierImg').src = fullUrl;
            }
        }

        // Media Studio Image Handlers
        function previewPrimaryProductImage(input) {
            const file = input.files && input.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(e) {
                const dataUrl = e.target.result;
                const heroImg = document.getElementById('primaryHeroLiveImg');
                const cardImg = document.getElementById('prevCardImg');
                const dossierImg = document.getElementById('prevDossierImg');
                const detailsBox = document.getElementById('primaryFileDetails');
                const nameLabel = document.getElementById('primaryFileName');

                if (heroImg) heroImg.src = dataUrl;
                if (cardImg) cardImg.src = dataUrl;
                if (dossierImg) dossierImg.src = dataUrl;
                if (detailsBox) detailsBox.classList.remove('hidden');
                if (nameLabel) nameLabel.textContent = `${file.name} (${(file.size / (1024 * 1024)).toFixed(2)} MB)`;
            };
            reader.readAsDataURL(file);
        }

        function handlePrimaryDrop(e) {
            e.preventDefault();
            const zone = e.currentTarget;
            if (zone) zone.classList.remove('border-sky-500');
            const files = e.dataTransfer.files;
            if (files && files.length > 0) {
                const input = document.getElementById('primary_image_file');
                if (input) {
                    input.files = files;
                    previewPrimaryProductImage(input);
                }
            }
        }

        function syncPrimaryFromTextInput(url) {
            url = (url || '').trim();
            if (!url) return;
            const heroImg = document.getElementById('primaryHeroLiveImg');
            const cardImg = document.getElementById('prevCardImg');
            const dossierImg = document.getElementById('prevDossierImg');
            if (heroImg) heroImg.src = url;
            if (cardImg) cardImg.src = url;
            if (dossierImg) dossierImg.src = url;
        }

        function previewNewGalleryFiles(input) {
            const files = input.files;
            const previewSection = document.getElementById('newGalleryPreviewSection');
            const grid = document.getElementById('newGalleryPreviewsGrid');
            const countText = document.getElementById('newGalleryCountText');

            if (!files || files.length === 0) {
                if (previewSection) previewSection.classList.add('hidden');
                if (grid) grid.innerHTML = '';
                return;
            }

            if (previewSection) previewSection.classList.remove('hidden');
            if (countText) {
                const isAr = "{{ app()->getLocale() }}" === 'ar';
                countText.innerText = isAr 
                    ? `${files.length} صور جديدة محددة وجاهزة للرفع` 
                    : `${files.length} new photos ready to upload`;
            }

            if (grid) {
                grid.innerHTML = '';
                Array.from(files).forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const col = document.createElement('div');
                        col.className = 'relative group rounded-xl overflow-hidden border border-purple-500/30 bg-white dark:bg-slate-900 shadow-sm aspect-square';
                        col.innerHTML = `
                            <img src="${e.target.result}" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent flex flex-col justify-end p-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <span class="text-[10px] text-white font-mono truncate">${file.name}</span>
                                <span class="text-[9px] text-purple-300">${(file.size / 1024).toFixed(0)} KB</span>
                            </div>
                            <span class="absolute top-1 right-1 w-5 h-5 rounded-full bg-purple-600 text-white text-[10px] font-bold flex items-center justify-center">
                                ${index + 1}
                            </span>
                        `;
                        grid.appendChild(col);
                    };
                    reader.readAsDataURL(file);
                });
            }
        }

        function handleGalleryDrop(e) {
            e.preventDefault();
            const files = e.dataTransfer.files;
            if (files && files.length > 0) {
                const input = document.getElementById('gallery_files_input');
                if (input) {
                    input.files = files;
                    previewNewGalleryFiles(input);
                }
            }
        }

        function clearNewGalleryFiles() {
            const input = document.getElementById('gallery_files_input');
            if (input) input.value = '';
            previewNewGalleryFiles({ files: [] });
        }

        function slugifyText(text) {
            if (!text) return '';
            return text.toString()
                .toLowerCase()
                .trim()
                .replace(/&/g, '-and-')
                .replace(/[^\w\s-]/g, '')
                .replace(/[\s_-]+/g, '-')
                .replace(/^-+|-+$/g, '');
        }

        function syncAutoSlug() {
            const nameEnInput = document.querySelector('input[name="name_en"]');
            const nameArInput = document.querySelector('input[name="name_ar"]');
            const slugInput = document.getElementById('slug');
            const slugPreviewText = document.getElementById('slugPreviewText');
            const step2SlugPreview = document.getElementById('step2SlugPreview');

            const source = (nameEnInput?.value || '').trim() || (nameArInput?.value || '').trim();
            const slug = slugifyText(source);

            if (slugInput) {
                slugInput.value = slug;
            }
            if (slugPreviewText) {
                slugPreviewText.textContent = slug || 'auto-generated';
            }
            if (step2SlugPreview) {
                step2SlugPreview.textContent = slug || 'auto-generated';
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            ['name_en', 'name_ar'].forEach(name => {
                const el = document.querySelector(`input[name="${name}"]`);
                if (el) {
                    el.addEventListener('input', syncAutoSlug);
                    el.addEventListener('change', syncAutoSlug);
                    el.addEventListener('keyup', syncAutoSlug);
                    el.addEventListener('paste', () => setTimeout(syncAutoSlug, 50));
                }
            });
            syncAutoSlug();

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
            syncDiscountRateFromSalePrice();

            const initialIngredients = @json(old('ingredients', []));
            if (Array.isArray(initialIngredients) && initialIngredients.length > 0) {
                initialIngredients.forEach(item => {
                    if (item) {
                        addIngredientRow(item.name_en || '', item.name_ar || '', item.dose || '');
                    }
                });
            } else {
                addIngredientRow('', '', '');
            }

            // Sync Primary Image Input Preview
            const primaryInput = document.getElementById('primary_image_file') || document.querySelector('input[name="primary_image"]');
            if (primaryInput) {
                primaryInput.addEventListener('change', function(e) {
                    previewPrimaryProductImage(e.target);
                });
            }
        });
    </script>

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
                            <img id="prevCardImg" src="{{ asset('assets/products/blue-mind.jpg') }}" alt="Preview" style="width: 100%; height: 100%; object-fit: cover; border-radius: var(--radius-md); background: var(--color-bg-subtle);" onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';">
                        </div>
                        <div class="font-bold text-xs" id="prevCardTitle" style="margin-bottom: 0.25rem; height: 32px; overflow: hidden; color: var(--color-text-main);">
                            BLUE MIND Precision Nootropic
                        </div>
                        <div class="text-xs text-muted" id="prevCardSku" style="font-family: monospace; font-size: 0.7rem; margin-bottom: 0.4rem;">
                            BZ-MND-001
                        </div>
                        <div class="font-black text-sm text-primary" id="prevCardPrice" style="font-weight: 800; border-top: 1px solid var(--color-border); padding-top: 0.4rem;">
                            $68.00
                        </div>
                        <div class="text-xs text-muted" style="margin-top: 0.25rem; font-size: 0.68rem;">
                            {{ app()->getLocale() === 'ar' ? 'مخزون المستودع: ' : 'Warehouse Stock: ' }} <strong id="prevCardStock" style="color: var(--color-success);">50 units</strong>
                        </div>
                    </div>
                </div>

                <!-- Storefront Product Dossier Preview -->
                <div style="background: #fff; padding: 1.25rem; border-radius: var(--radius-md); border: 1px solid var(--color-border);">
                    <div class="text-xs font-bold text-muted" style="margin-bottom: 0.75rem; text-transform: uppercase;">
                        {{ app()->getLocale() === 'ar' ? 'مظهر تفاصيل المنتج في المتجر والكتالوج (Storefront Dossier):' : 'Storefront Clinical Details Appearance:' }}
                    </div>

                    <div style="display: grid; grid-template-columns: 110px 1fr; gap: 1rem; align-items: start;">
                        <img id="prevDossierImg" src="{{ asset('assets/products/blue-mind.jpg') }}" alt="Preview" style="width: 110px; height: 110px; object-fit: cover; border-radius: var(--radius-md); border: 1px solid var(--color-border);" onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';">
                        <div>
                            <div style="display: flex; gap: 0.35rem; margin-bottom: 0.35rem; flex-wrap: wrap;" id="prevBadges">
                                <span class="badge badge-accent" id="prevCategoryBadge">Cellular Longevity</span>
                                <span class="badge badge-success" id="prevFeaturedBadge">Featured</span>
                            </div>
                            <h4 id="prevDossierTitle" style="margin: 0 0 0.25rem 0; font-size: 1.05rem; font-weight: 800; color: var(--color-primary);">BLUE MIND</h4>
                            <p id="prevDossierTagline" class="text-xs text-muted" style="margin: 0 0 0.4rem 0; font-style: italic;">Daily Cognitive & Neurogenesis Support</p>
                            <div class="font-black text-lg text-primary" id="prevDossierPrice">$68.00 <span class="text-xs text-muted" style="font-weight: normal;">(incl. 15% VAT)</span></div>
                        </div>
                    </div>

                    <!-- Short Description -->
                    <div style="margin-top: 1rem; border-top: 1px dashed var(--color-border); padding-top: 0.75rem;">
                        <div class="text-xs font-bold text-muted" style="margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'الوصف السريع:' : 'Short Description:' }}</div>
                        <p id="prevDossierDesc" class="text-xs text-muted" style="margin: 0; line-height: 1.5;">Clinical bioceutical formulation engineered for optimal cellular bio-energetics...</p>
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
