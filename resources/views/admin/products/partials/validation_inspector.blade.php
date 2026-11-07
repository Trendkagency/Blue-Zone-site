@php
    $errors = $errors ?? new \Illuminate\Support\ViewErrorBag();
    $isAr = app()->getLocale() === 'ar';
    $fieldMap = [
        // Step 1: Core Identifiers & Biological Classification
        'sku' => ['step' => 1, 'label' => $isAr ? 'رمز المنتج (SKU)' : 'Product SKU', 'id' => 'sku'],
        'barcode' => ['step' => 1, 'label' => $isAr ? 'الباركود الدولي' : 'Barcode', 'id' => 'barcode'],
        'initial_batch_number' => ['step' => 1, 'label' => $isAr ? 'رقم التشغيلة / الدفعة (Batch #)' : 'Batch / Lot Number', 'id' => 'step1_batch_number'],
        'batch_number' => ['step' => 1, 'label' => $isAr ? 'رقم التشغيلة / الدفعة (Batch #)' : 'Batch / Lot Number', 'id' => 'step1_batch_number'],
        'manufacture_date' => ['step' => 1, 'label' => $isAr ? 'تاريخ الإنتاج / البدء' : 'Manufacture Date', 'id' => 'step1_mfg_date'],
        'mfg_date' => ['step' => 1, 'label' => $isAr ? 'تاريخ الإنتاج / البدء' : 'Manufacture Date', 'id' => 'step1_mfg_date'],
        'expiry_date' => ['step' => 1, 'label' => $isAr ? 'تاريخ انتهاء الصلاحية (FEFO)' : 'Expiry Date (FEFO Target)', 'id' => 'step1_exp_date'],
        'exp_date' => ['step' => 1, 'label' => $isAr ? 'تاريخ انتهاء الصلاحية (FEFO)' : 'Expiry Date (FEFO Target)', 'id' => 'step1_exp_date'],
        'category_id' => ['step' => 1, 'label' => $isAr ? 'التصنيف الحيوي' : 'Biological Category', 'id' => 'category_id'],
        'brand_id' => ['step' => 1, 'label' => $isAr ? 'العلامة التجارية' : 'Brand', 'id' => 'brand_id'],
        'origin_country_id' => ['step' => 1, 'label' => $isAr ? 'بلد المنشأ' : 'Country of Origin', 'id' => 'origin_country_id'],
        'status' => ['step' => 1, 'label' => $isAr ? 'حالة النشر' : 'Publishing Status', 'id' => 'status'],

        // Step 2: Content (EN / AR)
        'name_en' => ['step' => 2, 'label' => $isAr ? 'اسم المستحضر (إنجليزي)' : 'Product Name (English)', 'id' => 'name_en'],
        'name_ar' => ['step' => 2, 'label' => $isAr ? 'اسم المستحضر (عربي)' : 'Product Name (Arabic)', 'id' => 'name_ar'],
        'short_description_en' => ['step' => 2, 'label' => $isAr ? 'الوصف الموجز (إنجليزي)' : 'Short Description (EN)', 'id' => 'short_description_en'],
        'short_description_ar' => ['step' => 2, 'label' => $isAr ? 'الوصف الموجز (عربي)' : 'Short Description (AR)', 'id' => 'short_description_ar'],
        'description_en' => ['step' => 2, 'label' => $isAr ? 'الوصف الكامل (إنجليزي)' : 'Full Description (EN)', 'id' => 'description_en'],
        'description_ar' => ['step' => 2, 'label' => $isAr ? 'الوصف الكامل (عربي)' : 'Full Description (AR)', 'id' => 'description_ar'],
        'storage_conditions' => ['step' => 2, 'label' => $isAr ? 'شروط التخزين والحفظ' : 'Storage Conditions', 'id' => 'storage_conditions'],
        'tags' => ['step' => 2, 'label' => $isAr ? 'الكلمات المفتاحية' : 'Tags', 'id' => 'tags'],

        // Step 3: Pricing & Taxes
        'price' => ['step' => 3, 'label' => $isAr ? 'سعر البيع الأساسي' : 'Base Retail Price', 'id' => 'inputBasePrice'],
        'cost_price' => ['step' => 3, 'label' => $isAr ? 'سعر التكلفة' : 'Cost Price', 'id' => 'inputCostPrice'],
        'sale_price' => ['step' => 3, 'label' => $isAr ? 'سعر الخصم / العرض' : 'Discount / Sale Price', 'id' => 'inputSalePrice'],
        'tax_id' => ['step' => 3, 'label' => $isAr ? 'قاعدة الضريبة المضافة' : 'VAT / Tax Rule', 'id' => 'tax_id'],
        'tax_rate' => ['step' => 3, 'label' => $isAr ? 'نسبة الضريبة' : 'Tax Rate', 'id' => 'tax_rate'],

        // Step 4: Assets & Media
        'primary_image' => ['step' => 4, 'label' => $isAr ? 'الصورة الرئيسية للمستحضر' : 'Primary Product Image', 'id' => 'primary_image'],
        'image' => ['step' => 4, 'label' => $isAr ? 'الصورة الرئيسية للمستحضر' : 'Primary Product Image', 'id' => 'primary_image'],
        'gallery_images' => ['step' => 4, 'label' => $isAr ? 'معرض صور المستحضر' : 'Gallery Images', 'id' => 'gallery_images'],

        // Step 5: Clinical Data
        'dosage_and_administration' => ['step' => 5, 'label' => $isAr ? 'طريقة الاستخدام والجرعات' : 'Dosage & Administration', 'id' => 'dosage_and_administration'],
        'contraindications' => ['step' => 5, 'label' => $isAr ? 'موانع الاستخدام والتحذيرات' : 'Contraindications', 'id' => 'contraindications'],
        'active_ingredients' => ['step' => 5, 'label' => $isAr ? 'المكونات والمواد الفعالة' : 'Active Ingredients', 'id' => 'active_ingredients'],

        // Step 6: Sections & Screen Controls
        'landing_badge' => ['step' => 6, 'label' => $isAr ? 'شارة التمييز' : 'Landing Badge', 'id' => 'landing_badge'],

        // Step 7: Stock, Batches & Review
        'stock_online' => ['step' => 7, 'label' => $isAr ? 'الرصيد المتاح بالمخزن' : 'Initial Available Stock', 'id' => 'stock_online'],
        'shelf_life_months' => ['step' => 7, 'label' => $isAr ? 'مدة الصلاحية (بالأشهر)' : 'Shelf Life (Months)', 'id' => 'inputShelfLifeMonths'],
        'min_stock_alert' => ['step' => 7, 'label' => $isAr ? 'حد تنبيه انخفاض المخزون' : 'Minimum Stock Alert', 'id' => 'min_stock_alert'],
        'reorder_point' => ['step' => 7, 'label' => $isAr ? 'نقطة إعادة الطلب' : 'Reorder Point', 'id' => 'reorder_point'],
        'batch_status' => ['step' => 7, 'label' => $isAr ? 'حالة فحص الجودة للتشغيلة' : 'Batch Quality Status', 'id' => 'batch_status'],
    ];

    $stepNames = [
        1 => $isAr ? 'الخطوة 1: الرمز والتصنيف' : 'Step 1: SKU & Category',
        2 => $isAr ? 'الخطوة 2: المحتوى ثنائي اللغة' : 'Step 2: Bilingual Content',
        3 => $isAr ? 'الخطوة 3: الأسعار والضرائب' : 'Step 3: Pricing & Taxes',
        4 => $isAr ? 'الخطوة 4: الصور والوسائط' : 'Step 4: Assets & Media',
        5 => $isAr ? 'الخطوة 5: الأبحاث والجرعات' : 'Step 5: Clinical Data',
        6 => $isAr ? 'الخطوة 6: تخصيص الأقسام' : 'Step 6: Screen Controls',
        7 => $isAr ? 'الخطوة 7: المخزون والتشغيلات والصلاحية' : 'Step 7: Stock, Batches & Expiry',
    ];
@endphp

<!-- ==========================================================
     LUXURY INTERACTIVE VALIDATION ERROR INSPECTOR
     ========================================================== -->
<div id="bzValidationInspectorCard" 
     class="card"
     style="margin-bottom: 2rem; border: 1.5px solid rgba(239, 68, 68, 0.4); background: linear-gradient(135deg, rgba(239, 68, 68, 0.08), rgba(2, 19, 31, 0.03)); border-radius: 16px; overflow: hidden; box-shadow: 0 10px 25px -5px rgba(239, 68, 68, 0.15); display: {{ $errors->any() ? 'block' : 'none' }};">

    <!-- Inspector Header Bar with Collapse / Toggle Button -->
    <div style="padding: 1.15rem 1.5rem; background: rgba(239, 68, 68, 0.12); border-bottom: 1px solid rgba(239, 68, 68, 0.25); display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
        <div style="display: flex; align-items: center; gap: 0.85rem;">
            <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(239, 68, 68, 0.2); color: #EF4444; display: flex; align-items: center; justify-content: center; font-size: 1.1rem;">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <div style="display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap;">
                    <h4 style="margin: 0; font-size: 1rem; font-weight: 800; color: #EF4444;">
                        {{ $isAr ? 'قسم فحص وتصحيح أخطاء الإدخال' : 'Interactive Validation Error Inspector' }}
                    </h4>
                    <span id="bzValidationCountBadge" class="badge badge-danger" style="font-family: monospace; font-size: 0.75rem; padding: 0.2rem 0.6rem; border-radius: 9999px;">
                        <span id="bzValidationCount">{{ $errors->count() }}</span> {{ $isAr ? 'أخطاء بحاجة للتصحيح' : 'Errors Found' }}
                    </span>
                </div>
                <p style="margin: 0.2rem 0 0 0; font-size: 0.8rem; color: var(--color-muted, #94a3b8);">
                    {{ $isAr ? 'اضغط على زر «انتقال وتصحيح» بجانب أي حقل للانتقال التلقائي إلى خطوته وتحديده بنبض ضوئي فوري.' : 'Click "Jump & Fix" beside any error to immediately navigate to its wizard step and focus the field.' }}
                </p>
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <button type="button" 
                    id="btnToggleValidationErrors" 
                    onclick="toggleValidationInspectorSection()" 
                    class="btn btn-sm btn-outline-danger" 
                    style="border-radius: 10px; font-weight: 700; padding: 0.35rem 0.85rem; display: inline-flex; align-items: center; gap: 0.4rem;">
                <i class="fa-solid fa-chevron-up" id="bzValidationToggleIcon"></i>
                <span id="bzValidationToggleLabel">{{ $isAr ? 'طي / إخفاء القائمة' : 'Collapse List' }}</span>
            </button>
        </div>
    </div>

    <!-- Inspector Body: List of Errors with 1-Click Jump Actions -->
    <div id="bzValidationErrorsContent" style="padding: 1.25rem 1.5rem; display: block;">
        <div id="bzValidationItemsList" style="display: flex; flex-direction: column; gap: 0.75rem;">
            @if ($errors->any())
                @foreach ($errors->getMessages() as $key => $messages)
                    @php
                        $cleanKey = str_replace(['.*', '[]'], '', $key);
                        $info = $fieldMap[$cleanKey] ?? [
                            'step' => 1,
                            'label' => ucwords(str_replace(['_', '.'], ' ', $cleanKey)),
                            'id' => $cleanKey
                        ];
                        $targetStep = $info['step'];
                        $stepLabel = $stepNames[$targetStep] ?? ($isAr ? "الخطوة {$targetStep}" : "Step {$targetStep}");
                    @endphp
                    @foreach ($messages as $msg)
                        <div class="bz-validation-error-row" 
                             style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: 0.85rem 1rem; background: var(--color-surface, rgba(255, 255, 255, 0.03)); border: 1px solid rgba(239, 68, 68, 0.2); border-radius: 12px; transition: all 0.2s ease;">
                            
                            <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                                <span style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.25rem 0.6rem; border-radius: 6px; font-size: 0.725rem; font-weight: 800; background: rgba(42, 143, 194, 0.15); color: #2A8FC2; border: 1px solid rgba(42, 143, 194, 0.3); white-space: nowrap;">
                                    <i class="fa-solid fa-layer-group"></i>
                                    {{ $stepLabel }}
                                </span>
                                <div>
                                    <div style="font-weight: 800; font-size: 0.875rem; color: var(--color-heading, #fff); display: flex; align-items: center; gap: 0.4rem;">
                                        <span>{{ $info['label'] }}</span>
                                        <code style="font-size: 0.725rem; opacity: 0.7; font-family: monospace;">({{ $key }})</code>
                                    </div>
                                    <div style="font-size: 0.825rem; color: #EF4444; margin-top: 0.2rem; font-weight: 600;">
                                        {{ $msg }}
                                    </div>
                                </div>
                            </div>

                            <button type="button" 
                                    class="btn btn-sm" 
                                    onclick="jumpToFieldError('{{ $info['id'] }}', {{ $targetStep }}, '{{ $cleanKey }}')"
                                    style="background: #EF4444; color: #fff; border: none; border-radius: 8px; font-weight: 800; padding: 0.4rem 0.85rem; font-size: 0.775rem; display: inline-flex; align-items: center; gap: 0.4rem; flex-shrink: 0; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);">
                                <span>{{ $isAr ? 'انتقال وتصحيح' : 'Jump & Fix' }}</span>
                                <i class="fa-solid fa-arrow-up-right-from-square {{ $isAr ? 'fa-flip-horizontal' : '' }}"></i>
                            </button>
                        </div>
                    @endforeach
                @endforeach
            @endif
        </div>
    </div>
</div>

<style>
    /* Dynamic pulsing animation when jumping to an invalid input */
    @keyframes bzValidationPulse {
        0% {
            box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.75);
            border-color: #EF4444 !important;
            transform: scale(1);
        }
        50% {
            box-shadow: 0 0 0 10px rgba(239, 68, 68, 0.2);
            border-color: #EF4444 !important;
            transform: scale(1.015);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
            border-color: #EF4444 !important;
            transform: scale(1);
        }
    }
    .bz-validation-pulse {
        animation: bzValidationPulse 1.2s infinite ease-in-out !important;
        border-color: #EF4444 !important;
        outline: 2px solid #EF4444 !important;
    }
    .bz-validation-error-row:hover {
        border-color: rgba(239, 68, 68, 0.5) !important;
        background: rgba(239, 68, 68, 0.06) !important;
    }
</style>

<script>
    window.bzFieldMap = @json($fieldMap);
    window.bzStepNames = @json($stepNames);

    // Toggle Inspector Collapse / Expand
    function toggleValidationInspectorSection() {
        const content = document.getElementById('bzValidationErrorsContent');
        const icon = document.getElementById('bzValidationToggleIcon');
        const label = document.getElementById('bzValidationToggleLabel');
        const isAr = {{ $isAr ? 'true' : 'false' }};
        if (!content) return;

        if (content.style.display === 'none') {
            content.style.display = 'block';
            if (icon) icon.className = 'fa-solid fa-chevron-up';
            if (label) label.textContent = isAr ? 'طي / إخفاء القائمة' : 'Collapse List';
        } else {
            content.style.display = 'none';
            if (icon) icon.className = 'fa-solid fa-chevron-down';
            if (label) label.textContent = isAr ? 'توسيع القائمة' : 'Expand List';
        }
    }

    // 1-Click Jump to Field Action with smooth scroll & glowing pulse
    window.jumpToFieldError = function(fieldId, step, cleanKey) {
        // 1. Direct step switch without block
        if (typeof setStep === 'function') {
            setStep(step);
        } else if (typeof goToStep === 'function') {
            goToStep(step);
        }

        if (typeof syncFloatingBarUI === 'function') {
            syncFloatingBarUI(step);
        }

        // 2. Locate target DOM element
        setTimeout(() => {
            let target = null;
            if (fieldId) {
                target = document.getElementById(fieldId);
            }
            if (!target && cleanKey) {
                target = document.querySelector(`[name="${cleanKey}"]`) 
                     || document.querySelector(`[name="${cleanKey}[]"]`)
                     || document.querySelector(`input[name*="${cleanKey}"]`)
                     || document.querySelector(`select[name*="${cleanKey}"]`)
                     || document.querySelector(`textarea[name*="${cleanKey}"]`);
            }
            if (!target && fieldId) {
                target = document.querySelector(`[name="${fieldId}"]`);
            }

            // Expiry / Batch special fallback
            if (!target && (cleanKey === 'expiry_date' || cleanKey === 'initial_batch_number' || cleanKey === 'manufacture_date')) {
                if (cleanKey === 'expiry_date') target = document.getElementById('inputExpiryDate') || document.getElementById('step1_exp_date');
                if (cleanKey === 'initial_batch_number' || cleanKey === 'batch_number') target = document.getElementById('step1_batch_number') || document.getElementById('step7_batch_number');
                if (cleanKey === 'manufacture_date') target = document.getElementById('inputManufactureDate') || document.getElementById('step1_mfg_date');
            }

            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'center' });
                try {
                    target.focus({ preventScroll: true });
                } catch (e) {
                    target.focus();
                }

                target.classList.add('bz-validation-pulse');
                
                // Remove pulse after 4 seconds or when user types/changes value
                const removePulse = () => {
                    target.classList.remove('bz-validation-pulse');
                    target.removeEventListener('input', removePulse);
                    target.removeEventListener('change', removePulse);
                };
                target.addEventListener('input', removePulse, { once: true });
                target.addEventListener('change', removePulse, { once: true });

                setTimeout(() => {
                    target.classList.remove('bz-validation-pulse');
                }, 4000);
            }
        }, 180);
    };

    // Dynamically render validation errors from AJAX / Autosave responses
    window.renderValidationErrors = function(errorsObj) {
        const card = document.getElementById('bzValidationInspectorCard');
        const countBadge = document.getElementById('bzValidationCount');
        const list = document.getElementById('bzValidationItemsList');
        const isAr = {{ $isAr ? 'true' : 'false' }};
        if (!card || !list) return;

        let totalErrors = 0;
        let html = '';

        for (const [key, msgs] of Object.entries(errorsObj)) {
            const cleanKey = key.replace(/\..*$/, '').replace('[]', '');
            const info = window.bzFieldMap[cleanKey] || {
                step: 1,
                label: cleanKey.replace(/_/g, ' ').toUpperCase(),
                id: cleanKey
            };
            const stepNum = info.step || 1;
            const stepTitle = window.bzStepNames[stepNum] || (isAr ? `الخطوة ${stepNum}` : `Step ${stepNum}`);

            const msgArr = Array.isArray(msgs) ? msgs : [msgs];
            msgArr.forEach(msg => {
                totalErrors++;
                html += `
                    <div class="bz-validation-error-row" 
                         style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: 0.85rem 1rem; background: var(--color-surface, rgba(255, 255, 255, 0.03)); border: 1px solid rgba(239, 68, 68, 0.2); border-radius: 12px; transition: all 0.2s ease;">
                        
                        <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                            <span style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.25rem 0.6rem; border-radius: 6px; font-size: 0.725rem; font-weight: 800; background: rgba(42, 143, 194, 0.15); color: #2A8FC2; border: 1px solid rgba(42, 143, 194, 0.3); white-space: nowrap;">
                                <i class="fa-solid fa-layer-group"></i>
                                ${stepTitle}
                            </span>
                            <div>
                                <div style="font-weight: 800; font-size: 0.875rem; color: var(--color-heading, #fff); display: flex; align-items: center; gap: 0.4rem;">
                                    <span>${info.label}</span>
                                    <code style="font-size: 0.725rem; opacity: 0.7; font-family: monospace;">(${key})</code>
                                </div>
                                <div style="font-size: 0.825rem; color: #EF4444; margin-top: 0.2rem; font-weight: 600;">
                                    ${msg}
                                </div>
                            </div>
                        </div>

                        <button type="button" 
                                class="btn btn-sm" 
                                onclick="jumpToFieldError('${info.id}', ${stepNum}, '${cleanKey}')"
                                style="background: #EF4444; color: #fff; border: none; border-radius: 8px; font-weight: 800; padding: 0.4rem 0.85rem; font-size: 0.775rem; display: inline-flex; align-items: center; gap: 0.4rem; flex-shrink: 0; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);">
                            <span>${isAr ? 'انتقال وتصحيح' : 'Jump & Fix'}</span>
                            <i class="fa-solid fa-arrow-up-right-from-square ${isAr ? 'fa-flip-horizontal' : ''}"></i>
                        </button>
                    </div>
                `;
            });
        }

        if (totalErrors > 0) {
            if (countBadge) countBadge.textContent = totalErrors;
            list.innerHTML = html;
            card.style.display = 'block';
            card.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    };
</script>
