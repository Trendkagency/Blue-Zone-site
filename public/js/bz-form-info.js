/**
 * BlueZone OS - Universal Form Guidance & Info Engine
 * Zero Clipping, Fixed Viewport Portal, Strict Deduplication, Adaptive Dark/Light Mode.
 */

(function () {
    'use strict';

    const isAr = document.documentElement.lang === 'ar' || document.documentElement.dir === 'rtl';

    // 1. Enterprise Field Knowledge Base
    const FIELD_KNOWLEDGE = {
        'supplier_id': {
            type: 'info',
            title: isAr ? 'المورد المعتمد' : 'Qualified Supplier',
            text: isAr ? 'المورد المعتمد رسمياً في سجل الجودة (cGMP). يؤدي اختياره لمزامنة عملة التوريد وشروط السداد آلياً.' : 'Officially certified cGMP supplier. Selecting synchronizes order currency and trading terms automatically.'
        },
        'order_date': {
            type: 'accounting',
            title: isAr ? 'تاريخ التحرير' : 'Order Date',
            text: isAr ? 'تاريخ التحرير الرسمي المعتمد لإثبات الالتزام المالي ومطابقة الفترة المحاسبية.' : 'Official issuance date establishing legal commitment and fiscal period matching for accounts payable.'
        },
        'expected_date': {
            type: 'inventory',
            title: isAr ? 'تاريخ الوصول المتوقع' : 'Promised Dock Date',
            text: isAr ? 'تاريخ وصول الشحنة المتفق عليه عند رصيف المستودع لمراقبة التوريد وحساب معدل كفاءة المورد (KPIs).' : 'Agreed dock arrival date used for lead-time tracking and vendor performance KPIs.'
        },
        'expected_delivery_date': {
            type: 'inventory',
            title: isAr ? 'تاريخ الوصول المتوقع' : 'Promised Dock Date',
            text: isAr ? 'تاريخ وصول الشحنة المتفق عليه عند رصيف المستودع لمراقبة التوريد وحساب معدل كفاءة المورد (KPIs).' : 'Agreed dock arrival date used for lead-time tracking and vendor performance KPIs.'
        },
        'destination_location_id': {
            type: 'inventory',
            title: isAr ? 'مستودع الاستلام' : 'Receiving Warehouse',
            text: isAr ? 'المستودع أو الرصيف الفعلي الذي سيتم استلام وفحص البضاعة فيه عند إصدار سند الاستلام (GRN).' : 'The designated warehouse or dock where physical unloading and QC intake inspection occur.'
        },
        'currency': {
            type: 'accounting',
            title: isAr ? 'عملة المعاملة' : 'Transaction Currency',
            text: isAr ? 'عملة الفاتورة الأصلية. يتم تقييم الدفاتر والمخزون بالعملة الأساسية للنظام وفق سعر الصرف المسجل.' : 'Originating invoice currency. Ledger postings and inventory asset valuation convert at the specified exchange rate.'
        },
        'exchange_rate': {
            type: 'accounting',
            title: isAr ? 'سعر الصرف' : 'Exchange Rate',
            text: isAr ? 'سعر تحويل عملة الفاتورة إلى العملة الأساسية للمنشأة لضمان دقة قيود المشتريات والمخازن.' : 'FX conversion multiplier against company base currency for accurate ledger booking and cost basis.'
        },
        'supplier_reference': {
            type: 'info',
            title: isAr ? 'مرجع المورد / عرض السعر' : 'Supplier Quote / Proforma Ref',
            text: isAr ? 'رقم عرض الأسعار (RFQ) أو الفاتورة المبدئية الخاصة بالمورد لضمان المطابقة الثلاثية عند الفوترة.' : 'Vendor quotation or proforma reference ensuring 3-way matching during invoice audit and accounts payable sign-off.'
        },
        'freight_cost': {
            type: 'inventory',
            title: isAr ? 'تكاليف الشحن والنقل' : 'Inbound Shipping / Freight',
            text: isAr ? 'رسوم الشحن الداخلي أو الدولي. يتم توزيعها بنسبة القيمة على الأصناف لزيادة تكلفة المخزون فقط (IAS 2).' : 'Inbound freight capitalized across line items into inventory valuation under IAS 2 without inflating vendor payable.'
        },
        'customs_duties': {
            type: 'accounting',
            title: isAr ? 'الرسوم الجمركية والتخليص' : 'Customs & Clearance Duties',
            text: isAr ? 'الرسوم الجمركية ورسوم الموانئ غير القابلة للاسترداد، تُرسمل مباشرة ضمن تكلفة دفعة المخزون الواردة.' : 'Non-recoverable port tariffs and customs fees added directly to the capitalized batch unit cost upon dock intake.'
        },
        'tax_amount': {
            type: 'accounting',
            title: isAr ? 'ضريبة القيمة المضافة (VAT)' : 'Tax / VAT Amount',
            text: isAr ? 'ضريبة القيمة المضافة (مدخلات). تسجل في حساب وسيط للخصم الضريبي ولا تضاف إلى تكلفة المخزون إذا كانت مستردة.' : 'Input VAT recorded in tax clearing accounts. Not capitalized into inventory if legally recoverable on VAT returns.'
        },
        'sku': {
            type: 'info',
            title: isAr ? 'رمز الصنف (SKU)' : 'Stock Keeping Unit (SKU)',
            text: isAr ? 'المعرف الفريد للصنف في المخازن ونقاط البيع لتتبع الحركات والمخزون.' : 'Unique product identifier across warehouse management and POS systems for barcode tracking.'
        },
        'barcode': {
            type: 'info',
            title: isAr ? 'الباركود الدولي' : 'Barcode / EAN-13',
            text: isAr ? 'الباركود القياسي (EAN-13 أو UPC) الممسوح ضوئياً في نقاط البيع والمستودعات.' : 'Standard international barcode (EAN-13 / UPC) scanned at POS checkout and dock intake.'
        },
        'cost_price': {
            type: 'accounting',
            title: isAr ? 'سعر التكلفة الأساسي' : 'Standard Cost Price',
            text: isAr ? 'تكلفة شراء الوحدة. يُعاد احتسابها آلياً بنظام متوسط التكلفة المرجح (WAC) مع كل استلام بضاعة.' : 'Unit purchase cost, dynamically updated via Weighted Average Cost (WAC) upon goods receipt.'
        },
        'price': {
            type: 'info',
            title: isAr ? 'سعر البيع الافتراضي' : 'Default Retail Price',
            text: isAr ? 'سعر البيع النهائي للمستهلك أو الصيدليات قبل تطبيق الخصومات الخاصة والعروض الترويجية.' : 'Base retail selling price to customers before volume discounts and promotional tiers.'
        },
        'min_stock': {
            type: 'inventory',
            title: isAr ? 'الحد الأدنى للمخزون' : 'Minimum Stock / Reorder Point',
            text: isAr ? 'مستوى المخزون الذي يُطلق تنبيهاً آلياً لمسؤول المشتريات لإصدار أمر شراء تفادياً لنفاد الصنف.' : 'Threshold quantity triggering automated procurement alerts before critical stock-outs occur.'
        },
        'max_stock': {
            type: 'inventory',
            title: isAr ? 'الحد الأقصى للمخزون' : 'Maximum Stock Cap',
            text: isAr ? 'الحد الأقصى للتخزين لتجنب تكدس البضاعة وتجميد السيولة وتجاوز الطاقة الاستيعابية للأرفف.' : 'Upper inventory ceiling to avoid working capital lockup, spoilage, and storage bin congestion.'
        },
        'code': {
            type: 'info',
            title: isAr ? 'كود المورد' : 'Supplier Code',
            text: isAr ? 'الرمز التعريفي الفريد للمنشأة في دليل الموردين المعتمدين.' : 'Unique vendor registration code in the approved supplier directory.'
        },
        'name_en': {
            type: 'info',
            title: isAr ? 'الاسم بالإنجليزية' : 'Official Trade Name (EN)',
            text: isAr ? 'الاسم التجاري للمنشأة كما هو مثبت في السجل التجاري والشهادات الدولية.' : 'Official legal registered business name matching commercial registry and trade certificates.'
        },
        'name_ar': {
            type: 'info',
            title: isAr ? 'الاسم بالعربية' : 'Trade Name (AR)',
            text: isAr ? 'الاسم المعرب للمنشأة للظهور في الفواتير والتقارير المعتمدة باللغة العربية.' : 'Localized Arabic commercial name for domestic reporting and official tax invoices.'
        },
        'email': {
            type: 'info',
            title: isAr ? 'البريد الإلكتروني' : 'Official Email',
            text: isAr ? 'البريد المعتمد لإرسال أوامر الشراء الرسمية وتأكيدات الاستلام والمطالبات المالية.' : 'Primary address for receiving dispatch PO PDFs, delivery appointments, and remittance slips.'
        },
        'phone': {
            type: 'info',
            title: isAr ? 'رقم الهاتف' : 'Contact Phone / WhatsApp',
            text: isAr ? 'رقم الهاتف المباشر لمسؤول التوريد ومتابعة شحنات الرصيف الطارئة.' : 'Direct telephone or WhatsApp for logistics coordination and dock delivery bays.'
        },
        'tax_number': {
            type: 'accounting',
            title: isAr ? 'الرقم الضريبي' : 'Tax Registration Number (TRN)',
            text: isAr ? 'الرقم الضريبي المعتمد لإصدار فواتير ضريبية نظامية وفق اشتراطات الفوترة الإلكترونية.' : 'Official Tax Identification Number for compliant B2B electronic tax invoicing.'
        },
        'cr_number': {
            type: 'info',
            title: isAr ? 'رقم السجل التجاري' : 'Commercial Registration (CR)',
            text: isAr ? 'رقم السجل التجاري للمنشأة للتحقق من الأهلية القانونية والامتثال التجاري.' : 'Legal registry license verifying commercial entity standing and validity.'
        },
    };

    // 2. Global Portal Popover (Appended to document.body)
    let popoverEl = null;
    let activeBtn = null;
    let hideTimer = null;

    function getPopover() {
        if (popoverEl && document.body.contains(popoverEl)) {
            return popoverEl;
        }

        popoverEl = document.createElement('div');
        popoverEl.className = 'bz-info-popover';
        popoverEl.setAttribute('role', 'tooltip');
        popoverEl.setAttribute('aria-hidden', 'true');
        popoverEl.innerHTML = `
            <div class="bz-info-header">
                <span class="bz-info-badge" id="bzInfoBadge">
                    <i class="fa-solid fa-circle-info"></i>
                    <span id="bzInfoTitleText"></span>
                </span>
                <button type="button" class="bz-info-close" style="background:none;border:none;color:#94a3b8;cursor:pointer;font-size:0.75rem;padding:2px 4px;">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="bz-info-body" id="bzInfoBodyText"></div>
            <div class="bz-info-arrow" id="bzInfoArrow"></div>
        `;

        document.body.appendChild(popoverEl);

        popoverEl.querySelector('.bz-info-close').addEventListener('click', hidePopover);

        popoverEl.addEventListener('mouseenter', () => {
            if (hideTimer) clearTimeout(hideTimer);
        });

        popoverEl.addEventListener('mouseleave', () => {
            hideTimer = setTimeout(hidePopover, 200);
        });

        // Global dismiss on click outside
        document.addEventListener('click', (e) => {
            if (activeBtn && !activeBtn.contains(e.target) && !popoverEl.contains(e.target)) {
                hidePopover();
            }
        });

        // Global dismiss on Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') hidePopover();
        });

        // Reposition on scroll or resize
        window.addEventListener('scroll', () => {
            if (activeBtn && popoverEl.classList.contains('is-visible')) {
                updatePosition();
            }
        }, { passive: true });

        window.addEventListener('resize', () => {
            if (activeBtn && popoverEl.classList.contains('is-visible')) {
                updatePosition();
            }
        }, { passive: true });

        return popoverEl;
    }

    function updatePosition() {
        if (!activeBtn || !popoverEl) return;

        const rect = activeBtn.getBoundingClientRect();
        const popWidth = Math.min(290, window.innerWidth - 32);
        popoverEl.style.width = popWidth + 'px';
        const popHeight = popoverEl.offsetHeight || 110;
        const arrow = popoverEl.querySelector('#bzInfoArrow');
        const margin = 8;

        // Calculate horizontal center
        const btnCenter = rect.left + (rect.width / 2);
        let left = btnCenter - (popWidth / 2);

        // Strict clamp inside visible viewport
        const minLeft = 16;
        const maxLeft = window.innerWidth - popWidth - 16;
        left = Math.max(minLeft, Math.min(maxLeft, left));

        // Calculate vertical position (prefer top, flip to bottom if clipping)
        let top = rect.top - popHeight - margin;
        let isBottom = false;

        if (top < 12) {
            top = rect.bottom + margin;
            isBottom = true;
        }

        popoverEl.style.top = `${Math.round(top)}px`;
        popoverEl.style.left = `${Math.round(left)}px`;

        // Position arrow pointing right at button center
        const arrowRelativeLeft = btnCenter - left - 4;
        const clampedArrowLeft = Math.max(12, Math.min(popWidth - 20, arrowRelativeLeft));
        arrow.style.left = `${Math.round(clampedArrowLeft)}px`;

        if (isBottom) {
            arrow.style.top = '-4px';
            arrow.style.bottom = 'auto';
            arrow.style.borderTop = 'inherit';
            arrow.style.borderLeft = 'inherit';
            arrow.style.borderBottom = 'none';
            arrow.style.borderRight = 'none';
        } else {
            arrow.style.bottom = '-4px';
            arrow.style.top = 'auto';
            arrow.style.borderBottom = 'inherit';
            arrow.style.borderRight = 'inherit';
            arrow.style.borderTop = 'none';
            arrow.style.borderLeft = 'none';
        }
    }

    function showPopover(btn, config) {
        if (hideTimer) clearTimeout(hideTimer);

        const pop = getPopover();
        activeBtn = btn;

        const badge = pop.querySelector('#bzInfoBadge');
        const titleText = pop.querySelector('#bzInfoTitleText');
        const bodyText = pop.querySelector('#bzInfoBodyText');

        const type = config.type || 'info';
        badge.className = 'bz-info-badge ' + (type ? 'is-' + type : '');
        const iconEl = badge.querySelector('i');
        iconEl.className = type === 'accounting' ? 'fa-solid fa-calculator' :
                          type === 'inventory' ? 'fa-solid fa-warehouse' :
                          type === 'warning' ? 'fa-solid fa-triangle-exclamation' : 'fa-solid fa-circle-info';

        titleText.textContent = config.title || (isAr ? 'إرشادات الإدخال' : 'Field Information');
        bodyText.innerHTML = config.text || '';

        pop.classList.add('is-visible');
        pop.setAttribute('aria-hidden', 'false');

        updatePosition();
    }

    function hidePopover() {
        if (!popoverEl) return;
        popoverEl.classList.remove('is-visible');
        popoverEl.setAttribute('aria-hidden', 'true');
        activeBtn = null;
    }

    // 3. Bind Existing Buttons (e.g. from <x-info-tooltip>)
    function bindExistingButton(btn) {
        if (btn.dataset.bzBound) return;
        btn.dataset.bzBound = 'true';

        const config = {
            type: btn.dataset.bzInfoType || 'info',
            title: btn.dataset.bzInfoTitle || (isAr ? 'إرشادات الإدخال' : 'Input Guidance'),
            text: btn.dataset.bzInfo || ''
        };

        btn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            if (activeBtn === btn && popoverEl?.classList.contains('is-visible')) {
                hidePopover();
            } else {
                showPopover(btn, config);
            }
        });

        btn.addEventListener('mouseenter', () => showPopover(btn, config));
        btn.addEventListener('mouseleave', () => {
            hideTimer = setTimeout(hidePopover, 250);
        });
        btn.addEventListener('focus', () => showPopover(btn, config));
        btn.addEventListener('blur', () => {
            hideTimer = setTimeout(hidePopover, 250);
        });
    }

    // 4. Auto-Scan & Inject on Plain Forms (Zero-Configuration Fallback)
    function processInput(input) {
        if (input.type === 'hidden' || input.dataset.bzBound) return;

        // Skip if input is already bound or associated label already has a tooltip
        let label = null;
        if (input.id) {
            label = document.querySelector(`label[for="${input.id}"]`);
        }
        if (!label) {
            label = input.closest('.form-group, div')?.querySelector('label.form-label, label');
        }

        if (label && label.querySelector('.bz-info-btn, [data-bz-info-bound]')) {
            input.dataset.bzBound = 'true';
            return;
        }

        // Determine field key
        const fieldName = (input.name || input.id || '').replace(/\[.*\]/g, '').replace(/_id$/g, '').trim();
        const rawName = (input.name || input.id || '').replace(/^.*\[/, '').replace(/\].*$/, '');

        let config = null;
        if (input.dataset.bzInfo) {
            config = {
                type: input.dataset.bzInfoType || 'info',
                title: input.dataset.bzInfoTitle || (isAr ? 'إرشادات الحقل' : 'Input Guidance'),
                text: input.dataset.bzInfo
            };
        } else if (FIELD_KNOWLEDGE[rawName]) {
            config = FIELD_KNOWLEDGE[rawName];
        } else if (FIELD_KNOWLEDGE[fieldName]) {
            config = FIELD_KNOWLEDGE[fieldName];
        } else if (FIELD_KNOWLEDGE[fieldName + '_id']) {
            config = FIELD_KNOWLEDGE[fieldName + '_id'];
        }

        if (!config || !label) {
            input.dataset.bzBound = 'true';
            return;
        }

        // Create unified info button
        const wrapper = document.createElement('span');
        wrapper.className = 'bz-info-wrapper';
        wrapper.dataset.bzInfoBound = 'true';

        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = `bz-info-btn ${config.type ? 'is-' + config.type : ''}`;
        btn.setAttribute('aria-label', config.title || 'Info');
        btn.dataset.bzInfo = config.text;
        btn.dataset.bzInfoTitle = config.title;
        btn.dataset.bzInfoType = config.type;
        btn.tabIndex = 0;

        const iconClass = config.type === 'accounting' ? 'fa-solid fa-calculator' :
                          config.type === 'inventory' ? 'fa-solid fa-warehouse' :
                          config.type === 'warning' ? 'fa-solid fa-triangle-exclamation' : 'fa-solid fa-circle-info';
        btn.innerHTML = `<i class="${iconClass}"></i>`;

        wrapper.appendChild(btn);

        const firstSpan = label.querySelector('span') || label;
        firstSpan.appendChild(wrapper);

        input.dataset.bzBound = 'true';
        bindExistingButton(btn);
    }

    function scanAll() {
        // 1. Bind any pre-rendered .bz-info-btn (from Blade component)
        document.querySelectorAll('.bz-info-btn').forEach(bindExistingButton);

        // 2. Discover raw inputs without tooltips
        document.querySelectorAll('form input, form select, form textarea').forEach(processInput);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', scanAll);
    } else {
        scanAll();
    }

    // Debounced observer for dynamic elements
    let observerTimer = null;
    const observer = new MutationObserver(() => {
        if (observerTimer) clearTimeout(observerTimer);
        observerTimer = setTimeout(scanAll, 100);
    });
    observer.observe(document.body, { childList: true, subtree: true });

    window.BZ_FORM_INFO = {
        scan: scanAll,
        show: showPopover,
        hide: hidePopover,
        knowledge: FIELD_KNOWLEDGE
    };
})();
