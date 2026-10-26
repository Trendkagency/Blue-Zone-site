@php
    $isAr = app()->getLocale() === 'ar';
    $mode = $mode ?? 'edit'; // 'edit' or 'create'
    $prodId = $productId ?? ($product['id'] ?? null);
    $formId = $formId ?? ($mode === 'edit' ? 'productEditForm' : 'productCreationForm');
@endphp

<!-- Bottom Spacer to prevent content clipping behind floating bar -->
<div style="height: 5.5rem;"></div>

<style>
    @media (max-width: 640px) {
        #bzWizardFloatingBar {
            padding: 0.5rem 0.75rem !important;
        }
        #floatingStepIndicator {
            max-width: 140px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-size: 0.7rem !important;
        }
        #floatingAutosaveStatus {
            display: none !important;
        }
        #bzWizardFloatingBar .btn {
            padding: 0.35rem 0.65rem !important;
            font-size: 0.7rem !important;
            border-radius: 8px !important;
        }
        #bzWizardFloatingBar .btn span.ctrl-s-hint {
            display: none !important;
        }
    }
</style>

<!-- ==========================================================
     PERSISTENT FLOATING REAL-TIME WIZARD ACTION BAR
     ========================================================== -->
<div id="bzWizardFloatingBar" 
     style="position: fixed; bottom: 0; left: 0; right: 0; z-index: 999; background: rgba(2, 19, 31, 0.94); backdrop-filter: blur(16px); border-top: 1px solid rgba(255, 255, 255, 0.12); box-shadow: 0 -10px 35px rgba(0, 0, 0, 0.4); padding: 0.85rem 1.5rem; transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);">
    
    <div style="max-width: 1440px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
        
        <!-- Left: Step Info Pill & Live Auto-Save Status -->
        <div style="display: flex; align-items: center; gap: 0.85rem; flex-wrap: wrap;">
            
            <!-- Active Step Indicator Capsule -->
            <div style="display: flex; align-items: center; gap: 0.5rem; background: rgba(255, 255, 255, 0.08); padding: 0.35rem 0.85rem; border-radius: 9999px; border: 1px solid rgba(255, 255, 255, 0.12);">
                <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #67B34A; box-shadow: 0 0 8px #67B34A;" id="floatingStepDot"></span>
                <span style="font-size: 0.75rem; font-family: monospace; font-weight: 800; color: #fff; letter-spacing: 0.03em;" id="floatingStepIndicator">
                    {{ $isAr ? 'الخطوة 1 من 7: الرمز والتصنيف' : 'STEP 1 OF 7: SKU & Category' }}
                </span>
            </div>

            <!-- Real-Time Auto-Save Status Badge -->
            <div id="floatingAutosaveStatus" 
                 style="display: flex; align-items: center; gap: 0.45rem; font-size: 0.75rem; font-family: monospace; font-weight: 700; color: #94a3b8; background: rgba(255, 255, 255, 0.05); padding: 0.35rem 0.8rem; border-radius: 9999px; border: 1px solid rgba(255, 255, 255, 0.08); transition: all 0.25s ease;">
                <i class="fa-solid fa-cloud-check text-emerald-400" id="floatingAutosaveIcon"></i>
                <span id="floatingAutosaveText">{{ $isAr ? 'جميع التعديلات محفوظة' : 'All changes saved' }}</span>
            </div>

        </div>

        <!-- Right: Step-by-Step Action Controls -->
        <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
            
            <!-- Previous Step Button -->
            <button type="button" 
                    id="floatingBtnPrev" 
                    onclick="navigateStep(-1)" 
                    class="btn btn-secondary btn-sm" 
                    style="border-radius: 12px; font-weight: 700; padding: 0.45rem 0.9rem; display: none; background: rgba(255,255,255,0.08); border-color: rgba(255,255,255,0.15); color: #fff;">
                <i class="fa-solid fa-arrow-left {{ $isAr ? 'fa-flip-horizontal' : '' }} mr-1 ml-1"></i>
                <span>{{ $isAr ? 'الخطوة السابقة' : 'Previous Step' }}</span>
            </button>

            <!-- Live Product Preview Button -->
            <button type="button" 
                    onclick="openLiveProductPreview()" 
                    class="btn btn-sm" 
                    style="border-radius: 12px; font-weight: 700; padding: 0.45rem 0.9rem; background: rgba(42, 143, 194, 0.15); border: 1px solid rgba(42, 143, 194, 0.4); color: #2A8FC2;">
                <i class="fa-solid fa-eye mr-1 ml-1"></i>
                <span>{{ $isAr ? 'معاينة حية' : 'Live Preview' }}</span>
            </button>

            <!-- Quick Save Active Step Button (Ctrl+S) -->
            <button type="button" 
                    id="floatingBtnQuickSave" 
                    onclick="quickSaveCurrentStep(false, false)" 
                    class="btn btn-sm" 
                    style="border-radius: 12px; font-weight: 800; padding: 0.45rem 1rem; background: rgba(103, 179, 74, 0.18); border: 1.5px solid rgba(103, 179, 74, 0.5); color: #B8D98A;"
                    title="{{ $isAr ? 'حفظ فوري للخطوة الحالية (Ctrl+S)' : 'Instant Save Active Step (Ctrl+S)' }}">
                <i class="fa-solid fa-floppy-disk mr-1 ml-1" id="floatingQuickSaveIcon"></i>
                <span id="floatingQuickSaveLabel">{{ $isAr ? 'حفظ الخطوة الحالية' : 'Save Step' }}</span>
                <span class="ctrl-s-hint" style="font-size: 0.65rem; opacity: 0.65; margin-inline-start: 0.25rem; font-family: monospace;">(Ctrl+S)</span>
            </button>

            <!-- Save & Next Step Button -->
            <button type="button" 
                    id="floatingBtnSaveNext" 
                    onclick="quickSaveCurrentStep(true, false)" 
                    class="btn btn-primary btn-sm" 
                    style="border-radius: 12px; font-weight: 800; padding: 0.45rem 1.15rem; background: linear-gradient(135deg, #0A4F78, #2A8FC2); border: 1px solid rgba(255,255,255,0.15); box-shadow: 0 4px 15px rgba(10, 79, 120, 0.45); color: #fff;">
                <span id="floatingSaveNextLabel">{{ $isAr ? 'حفظ والخطوة التالية' : 'Save & Next Step' }}</span>
                <i class="fa-solid fa-arrow-right {{ $isAr ? 'fa-flip-horizontal' : '' }} mr-1 ml-1"></i>
            </button>

            <!-- Final Publish Formulation Button (Visible on Step 7) -->
            <button type="button" 
                    id="floatingBtnFinalPublish" 
                    onclick="submitFinalProductForm()" 
                    class="btn btn-success btn-sm" 
                    style="border-radius: 12px; font-weight: 900; padding: 0.45rem 1.25rem; background: linear-gradient(135deg, #4d8736, #67B34A); border: 1px solid rgba(255,255,255,0.2); box-shadow: 0 4px 15px rgba(103, 179, 74, 0.4); color: #fff; display: none;">
                <i class="fa-solid fa-circle-check mr-1.5 ml-1.5"></i>
                <span>{{ $isAr ? 'اعتماد ونشر المستحضر' : 'Finalize & Publish' }}</span>
            </button>

        </div>
    </div>
</div>

<!-- Floating Notification Toast -->
<div id="bzSaveToast" 
     style="position: fixed; top: 1.5rem; inset-inline-end: 1.5rem; z-index: 10001; transform: translateY(-150%); opacity: 0; transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1); pointer-events: none;">
    <div style="background: rgba(2, 19, 31, 0.95); backdrop-filter: blur(12px); border: 1px solid #67B34A; color: #fff; padding: 0.75rem 1.25rem; border-radius: 1rem; box-shadow: 0 15px 35px rgba(0,0,0,0.35); display: flex; align-items: center; gap: 0.75rem;">
        <span style="width: 28px; height: 28px; border-radius: 50%; background: rgba(103, 179, 74, 0.2); color: #67B34A; display: flex; align-items: center; justify-content: center; font-size: 13px;">
            <i class="fa-solid fa-check" id="bzToastIcon"></i>
        </span>
        <div>
            <div style="font-weight: 800; font-size: 0.825rem;" id="bzToastTitle">{{ $isAr ? 'تم الحفظ بنجاح' : 'Saved Successfully' }}</div>
            <div style="font-size: 0.725rem; color: #94a3b8;" id="bzToastMsg">{{ $isAr ? 'تم تحديث الخطوة وحفظ البيانات في قاعدة البيانات.' : 'Step data updated in database.' }}</div>
        </div>
    </div>
</div>

<script>
    // ==========================================================
    // FLOATING ACTION BAR & REAL-TIME SAVE ENGINE
    // ==========================================================
    let bzWizardMode = '{{ $mode }}';
    let bzActiveProductId = {{ $prodId ? (int)$prodId : 'null' }};
    const bzFormId = '{{ $formId }}';
    const bzIsAr = {{ $isAr ? 'true' : 'false' }};

    const bzStepNames = {
        1: { en: 'SKU & Category', ar: 'الرمز والتصنيف' },
        2: { en: 'EN & AR Content', ar: 'المحتوى ثنائي اللغة' },
        3: { en: 'Pricing & Taxes', ar: 'الأسعار والضرائب' },
        4: { en: 'Assets & Media', ar: 'الصور والوسائط' },
        5: { en: 'Clinical Data', ar: 'الأبحاث والجرعات' },
        6: { en: 'Page & Sections Studio', ar: 'تخصيص الأقسام وباقات الخصم' },
        7: { en: 'Stock & Final Review', ar: 'المخزون والمراجعة النهائية' },
    };

    // Update Floating Bar UI when step changes
    function syncFloatingBarUI(step) {
        const stepDot = document.getElementById('floatingStepDot');
        const stepInd = document.getElementById('floatingStepIndicator');
        const btnPrev = document.getElementById('floatingBtnPrev');
        const btnSaveNext = document.getElementById('floatingBtnSaveNext');
        const btnFinal = document.getElementById('floatingBtnFinalPublish');

        const stepName = bzIsAr ? (bzStepNames[step]?.ar || '') : (bzStepNames[step]?.en || '');
        if (stepInd) {
            stepInd.textContent = bzIsAr 
                ? `الخطوة ${step} من 7: ${stepName}` 
                : `STEP ${step} OF 7: ${stepName}`;
        }

        if (btnPrev) btnPrev.style.display = (step > 1) ? 'inline-flex' : 'none';
        if (btnSaveNext) btnSaveNext.style.display = (step < 7) ? 'inline-flex' : 'none';
        if (btnFinal) btnFinal.style.display = (step === 7) ? 'inline-flex' : 'none';
    }

    // Update Auto-save badge
    function updateFloatingStatusBadge(state, text = '') {
        const badge = document.getElementById('floatingAutosaveStatus');
        const icon = document.getElementById('floatingAutosaveIcon');
        const textEl = document.getElementById('floatingAutosaveText');
        if (!badge || !icon || !textEl) return;

        if (state === 'saved') {
            badge.style.borderColor = 'rgba(103, 179, 74, 0.4)';
            badge.style.color = '#B8D98A';
            icon.className = 'fa-solid fa-cloud-check text-emerald-400';
            textEl.textContent = text || (bzIsAr ? 'تم الحفظ في قاعدة البيانات' : 'Saved to database');
        } else if (state === 'saving') {
            badge.style.borderColor = 'rgba(42, 143, 194, 0.4)';
            badge.style.color = '#38bdf8';
            icon.className = 'fa-solid fa-spinner fa-spin text-sky-400';
            textEl.textContent = text || (bzIsAr ? 'جاري الحفظ الفوري...' : 'Saving in real-time...');
        } else if (state === 'unsaved') {
            badge.style.borderColor = 'rgba(245, 158, 11, 0.4)';
            badge.style.color = '#fbbf24';
            icon.className = 'fa-solid fa-circle-dot text-amber-400';
            textEl.textContent = text || (bzIsAr ? 'تعديلات غير محفوظة' : 'Unsaved changes');
        } else if (state === 'error') {
            badge.style.borderColor = 'rgba(239, 68, 68, 0.4)';
            badge.style.color = '#f87171';
            icon.className = 'fa-solid fa-triangle-exclamation text-rose-400';
            textEl.textContent = text || (bzIsAr ? 'تعذر الحفظ' : 'Save error');
        }
    }

    // Show floating toast
    function showFloatingToast(title, msg, isSuccess = true) {
        const toast = document.getElementById('bzSaveToast');
        const titleEl = document.getElementById('bzToastTitle');
        const msgEl = document.getElementById('bzToastMsg');
        const iconEl = document.getElementById('bzToastIcon');
        if (!toast) return;

        if (titleEl) titleEl.textContent = title;
        if (msgEl) msgEl.textContent = msg;
        if (iconEl) {
            iconEl.className = isSuccess ? 'fa-solid fa-check text-emerald-400' : 'fa-solid fa-xmark text-rose-400';
        }

        toast.style.transform = 'translateY(0)';
        toast.style.opacity = '1';

        setTimeout(() => {
            toast.style.transform = 'translateY(-150%)';
            toast.style.opacity = '0';
        }, 3200);
    }

    // Core Step-by-Step Auto-Save Engine
    let isSavingStep = false;
    async function quickSaveCurrentStep(andNext = false, isSilent = false) {
        if (isSavingStep) return;

        // If andNext is requested, ensure current step passes client validation
        if (andNext && typeof validateStep === 'function') {
            if (!validateStep(currentStep)) {
                return;
            }
        }

        const form = document.getElementById(bzFormId);
        if (!form) return;

        isSavingStep = true;
        updateFloatingStatusBadge('saving');

        const quickIcon = document.getElementById('floatingQuickSaveIcon');
        if (quickIcon && !isSilent) quickIcon.className = 'fa-solid fa-spinner fa-spin mr-1 ml-1';

        try {
            const formData = new FormData(form);
            formData.delete('_method');
            formData.append('step', currentStep);
            formData.append('_token', '{{ csrf_token() }}');

            // If in create mode and no product record exists yet, create draft on Step 1
            let targetUrl = '';
            if (bzWizardMode === 'create' && !bzActiveProductId) {
                targetUrl = '{{ route("admin.products.auto-create-draft") }}';
            } else {
                targetUrl = `/admin/products/${bzActiveProductId}/auto-save-step`;
            }

            const response = await fetch(targetUrl, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData,
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(data.message || (bzIsAr ? 'حدث خطأ أثناء حفظ الخطوة.' : 'Failed to save step.'));
            }

            // If draft was just created, upgrade wizard context to edit mode
            if (data.product_id && !bzActiveProductId) {
                bzActiveProductId = data.product_id;
                bzWizardMode = 'edit';
                if (data.edit_url) {
                    window.history.replaceState({ path: data.edit_url }, '', data.edit_url);
                }
                form.action = `/admin/products/${bzActiveProductId}`;
                // Add hidden PUT method for standard submits
                if (!form.querySelector('input[name="_method"]')) {
                    const putInput = document.createElement('input');
                    putInput.type = 'hidden';
                    putInput.name = '_method';
                    putInput.value = 'PUT';
                    form.appendChild(putInput);
                }
            }

            // If Step 3 and taxInfo returned, update pricing display
            if (data.taxInfo && typeof updatePricingDisplayFromTaxInfo === 'function') {
                updatePricingDisplayFromTaxInfo(data.taxInfo);
            }

            // Mark wizard indicator completed
            const stepEl = document.querySelector(`.wizard-step[data-step="${currentStep}"]`);
            if (stepEl) stepEl.classList.add('completed');

            updateFloatingStatusBadge('saved', `${bzIsAr ? 'تم الحفظ في' : 'Saved at'} ${data.saved_at || new Date().toLocaleTimeString()}`);

            if (!isSilent) {
                showFloatingToast(
                    bzIsAr ? `تم حفظ الخطوة ${currentStep} بنجاح` : `Step ${currentStep} Saved Successfully`,
                    data.message || (bzIsAr ? 'تم تحديث البيانات في قاعدة البيانات.' : 'Changes committed to database.')
                );
            }

            // If requested, advance to next step smoothly
            if (andNext && currentStep < totalSteps) {
                setStep(currentStep + 1);
            }

        } catch (err) {
            console.error('Step Auto-Save Error:', err);
            updateFloatingStatusBadge('error', err.message);
            if (!isSilent) {
                showFloatingToast(
                    bzIsAr ? 'خطأ في الحفظ' : 'Save Error',
                    err.message,
                    false
                );
            }
        } finally {
            isSavingStep = false;
            if (quickIcon) quickIcon.className = 'fa-solid fa-floppy-disk mr-1 ml-1';
        }
    }

    // Submit final form (Step 7)
    function submitFinalProductForm() {
        const form = document.getElementById(bzFormId);
        if (form) {
            form.submit();
        }
    }

    // Debounced real-time autosave
    let bzAutosaveTimer = null;
    function setupRealTimeAutosave() {
        const form = document.getElementById(bzFormId);
        if (!form) return;

        form.addEventListener('input', (e) => {
            // Ignore file inputs from debounced autosave
            if (e.target.type === 'file') return;

            updateFloatingStatusBadge('unsaved');
            clearTimeout(bzAutosaveTimer);
            bzAutosaveTimer = setTimeout(() => {
                quickSaveCurrentStep(false, true);
            }, 1800);
        });

        form.addEventListener('change', (e) => {
            if (e.target.type === 'checkbox' || e.target.type === 'radio' || e.target.tagName === 'SELECT') {
                updateFloatingStatusBadge('unsaved');
                clearTimeout(bzAutosaveTimer);
                bzAutosaveTimer = setTimeout(() => {
                    quickSaveCurrentStep(false, true);
                }, 1500);
            }
        });
    }

    // Keyboard shortcut (Ctrl+S / Cmd+S)
    document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 's') {
            e.preventDefault();
            quickSaveCurrentStep(false, false);
        }
    });

    // Hook into existing setStep to sync floating bar
    document.addEventListener('DOMContentLoaded', () => {
        setupRealTimeAutosave();

        const originalSetStep = window.setStep;
        if (typeof originalSetStep === 'function') {
            window.setStep = function(step) {
                originalSetStep(step);
                syncFloatingBarUI(step);
            };
        }

        // Initialize UI
        if (typeof currentStep !== 'undefined') {
            syncFloatingBarUI(currentStep);
        }
    });
</script>
