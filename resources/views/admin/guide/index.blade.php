<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'مركز الدعم ودليل دورات عمل النظام الشامل' : 'Comprehensive System Support & Logic Cycles Guide'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'المرجع المعماري والتشغيلي المكتمل لكافة أقسام ودورات عمل (Logic Cycles) منصة Bluezone OS' : 'Full Architectural & Operational Cycles for all Bluezone Bioceuticals Enterprise Sections'"
    :breadcrumbs="[
        (app()->getLocale() === 'ar' ? 'لوحة التحكم' : 'Dashboard') => route('admin.dashboard'),
        (app()->getLocale() === 'ar' ? 'الدعم والدليل الشامل' : 'Support & Guide') => route('admin.guide.index')
    ]"
>
    <!-- ========================================================================= -->
    <!-- DUAL-THEME STYLES (WCAG AAA DARK & LIGHT COMPLIANT) -->
    <!-- ========================================================================= -->
    <style>
        .bz-guide-shell {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Cairo", sans-serif;
            transition: all 0.25s ease;
        }

        /* Hero Banner */
        .bz-guide-hero {
            background: linear-gradient(135deg, #ffffff 0%, #f0f9ff 50%, #e0f2fe 100%);
            border: 1px solid #bae6fd;
            box-shadow: 0 10px 25px -5px rgba(2, 132, 199, 0.08);
        }
        .dark .bz-guide-hero {
            background: radial-gradient(circle at 15% 20%, #0d3859 0%, #081d2f 60%, #02121E 100%);
            border-color: rgba(42, 143, 194, 0.35);
            box-shadow: 0 20px 45px -10px rgba(0, 0, 0, 0.7);
        }

        /* Top Category Navigation Pills */
        .bz-tab-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.65rem 1.15rem;
            border-radius: 9999px;
            font-size: 0.825rem;
            font-weight: 700;
            color: #334155;
            background: #ffffff;
            border: 1.5px solid #cbd5e1;
            white-space: nowrap;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }
        .dark .bz-tab-pill {
            color: #cbd5e1;
            background: #081d2f;
            border-color: #1e3a52;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
        }
        .bz-tab-pill:hover {
            color: #0284c7;
            border-color: #0284c7;
            background: #f8fafc;
            transform: translateY(-2px);
        }
        .dark .bz-tab-pill:hover {
            color: #ffffff;
            border-color: #38bdf8;
            background: #0c273e;
            transform: translateY(-2px);
        }
        .bz-tab-pill.active {
            background: linear-gradient(135deg, #0A4F78 0%, #0284c7 100%) !important;
            color: #ffffff !important;
            border-color: #0A4F78 !important;
            box-shadow: 0 4px 16px -2px rgba(2, 132, 199, 0.4) !important;
        }

        /* Collapsible Accordion Module Card */
        .bz-accordion-module {
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 1.5rem;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.04);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            overflow: hidden;
        }
        .dark .bz-accordion-module {
            background: #081d2f;
            border-color: #1e3a52;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.5);
        }

        /* Accordion Header Bar (Clickable) */
        .bz-accordion-header {
            cursor: pointer;
            user-select: none;
            transition: background-color 0.2s ease;
        }
        .bz-accordion-header:hover {
            background-color: #f8fafc;
        }
        .dark .bz-accordion-header:hover {
            background-color: #0a253d;
        }

        /* Accordion Chevron Icon Animation */
        .bz-accordion-chevron {
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        /* Sub-screen Navigation Pill Bar inside Accordion */
        .bz-subscreen-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 0.85rem;
            font-size: 0.775rem;
            font-weight: 700;
            color: #475569;
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            white-space: nowrap;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .dark .bz-subscreen-pill {
            color: #94a3b8;
            background: #061a2b;
            border-color: #1e3a52;
        }
        .bz-subscreen-pill:hover {
            color: #0284c7;
            background: #e2e8f0;
            border-color: #0284c7;
        }
        .dark .bz-subscreen-pill:hover {
            color: #38bdf8;
            background: #0a253d;
            border-color: #38bdf8;
        }
        .bz-subscreen-pill.active {
            color: #ffffff !important;
            background: linear-gradient(135deg, #0A4F78 0%, #0284c7 100%) !important;
            border-color: #0A4F78 !important;
            box-shadow: 0 2px 10px rgba(2, 132, 199, 0.35) !important;
        }

        /* Flow Node Card (Cycle Step) */
        .bz-cycle-step {
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 1.25rem;
            padding: 1.25rem;
            position: relative;
            transition: all 0.25s ease;
        }
        .dark .bz-cycle-step {
            background: #061a2b;
            border-color: #15395b;
        }
        .bz-cycle-step:hover {
            border-color: #0284c7;
            background: #ffffff;
            transform: translateY(-3px);
            box-shadow: 0 10px 25px -5px rgba(2, 132, 199, 0.15);
        }
        .dark .bz-cycle-step:hover {
            border-color: #38bdf8;
            background: #0a253d;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.6);
        }

        /* Screen Preview Card Thumbnail */
        .bz-screen-card {
            position: relative;
            border-radius: 1.25rem;
            border: 1.5px solid #e2e8f0;
            background: #ffffff;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.05);
        }
        .dark .bz-screen-card {
            background: #061e31;
            border-color: #1e3a52;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
        }
        .bz-screen-card:hover {
            transform: translateY(-4px) scale(1.01);
            border-color: #0284c7;
            box-shadow: 0 16px 35px -8px rgba(2, 132, 199, 0.25);
        }
        .dark .bz-screen-card:hover {
            border-color: #38bdf8;
            box-shadow: 0 16px 40px -8px rgba(0, 242, 254, 0.3);
        }
        .bz-screen-card:hover .bz-screen-zoom-overlay {
            opacity: 1;
        }
        .bz-screen-zoom-overlay {
            position: absolute;
            inset: 0;
            background: rgba(8, 29, 47, 0.7);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.25s ease;
        }

        /* Fade-in Animation for Sub-screen Switching */
        .bz-fade-in {
            animation: bzFadeIn 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        @keyframes bzFadeIn {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Fullscreen Lightbox Modal */
        #guideLightbox {
            position: fixed;
            inset: 0;
            z-index: 99999999;
            display: none;
            align-items: center;
            justify-content: center;
            background: rgba(2, 18, 30, 0.9);
            backdrop-filter: blur(24px);
            opacity: 0;
            transition: opacity 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        #guideLightbox.active {
            display: flex;
            opacity: 1;
        }
        #guideLightboxContent {
            position: relative;
            max-width: 95vw;
            max-height: 94vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            transform: scale(0.92);
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        #guideLightbox.active #guideLightboxContent {
            transform: scale(1);
        }
        #lightboxImage {
            max-width: 100%;
            max-height: 76vh;
            border-radius: 1.25rem;
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.8), 0 0 45px rgba(56, 189, 248, 0.3);
            border: 2px solid rgba(56, 189, 248, 0.4);
            user-select: none;
        }
    </style>

    <div class="bz-guide-shell space-y-8 pb-24">
        
        <!-- ========================================================================= -->
        <!-- 1. EXECUTIVE HERO COMMAND BANNER WITH REAL-TIME METRICS -->
        <!-- ========================================================================= -->
        <div class="bz-guide-hero relative overflow-hidden rounded-3xl p-6 sm:p-8 transition-colors">
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-sky-400/15 dark:bg-sky-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-emerald-400/15 dark:bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
                <div class="space-y-3 max-w-3xl">
                    <div class="inline-flex items-center gap-2.5 px-3.5 py-1.5 rounded-full text-xs font-bold uppercase tracking-wide bg-sky-100 dark:bg-sky-500/20 text-sky-800 dark:text-sky-300 border border-sky-300 dark:border-sky-500/30">
                        <i class="fa-solid fa-compass-drafting text-sky-600 dark:text-sky-400"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'المرجع المعماري التنفيذي لدورات عمل شاشات النظام (Collapsible Zero-Gap OS Guide)' : 'Bluezone OS Complete Collapsible Logic Cycles & Screens Knowledge Hub' }}</span>
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    </div>

                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-slate-900 dark:text-white tracking-tight leading-tight">
                        {{ app()->getLocale() === 'ar' ? 'دليل شاشات ودورات عمل النظام بالكامل' : 'Complete System Sections, Sub-Screens & Logic Cycles' }}
                    </h1>

                    <p class="text-sm sm:text-base text-slate-600 dark:text-slate-300 font-medium leading-relaxed">
                        {{ app()->getLocale() === 'ar' 
                            ? 'تنظيم معماري احترافي بنظام الأكورديون القابل للطي (Collapsible Tree)، حيث يتضمن كل قسم رئيسي تبويبات فرعية لكافة شاشاته مع تفصيل دورة العمل الكاملة (Full Cycle) والأثر المحاسبي والمخزني ولقطات حية مكبرة بنمط آبل.' 
                            : 'Professional collapsible tree architecture where every main enterprise domain holds dedicated sub-screen navigation pills, zero-gap workflow cycles, UI controls, ledger rules, and real live screenshots.' }}
                    </p>
                </div>

                <!-- Live Metrics Summary Grid (Reflecting All System Modules) -->
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2.5 w-full lg:w-auto">
                    <div class="p-3 rounded-2xl bg-white/80 dark:bg-[#061a2b]/80 border border-slate-200 dark:border-[#1e3a52] text-center shadow-xs">
                        <div class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-0.5">{{ app()->getLocale() === 'ar' ? 'المنتجات' : 'Products' }}</div>
                        <div class="text-lg font-black text-slate-900 dark:text-sky-400 font-mono">{{ $systemMetrics['products_count'] }}</div>
                    </div>
                    <div class="p-3 rounded-2xl bg-white/80 dark:bg-[#061a2b]/80 border border-slate-200 dark:border-[#1e3a52] text-center shadow-xs">
                        <div class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-0.5">{{ app()->getLocale() === 'ar' ? 'الموردين' : 'Suppliers' }}</div>
                        <div class="text-lg font-black text-slate-900 dark:text-emerald-400 font-mono">{{ $systemMetrics['suppliers_count'] }}</div>
                    </div>
                    <div class="p-3 rounded-2xl bg-white/80 dark:bg-[#061a2b]/80 border border-slate-200 dark:border-[#1e3a52] text-center shadow-xs">
                        <div class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-0.5">{{ app()->getLocale() === 'ar' ? 'أوامر الشراء' : 'POs' }}</div>
                        <div class="text-lg font-black text-slate-900 dark:text-amber-400 font-mono">{{ $systemMetrics['purchase_orders_count'] }}</div>
                    </div>
                    <div class="p-3 rounded-2xl bg-white/80 dark:bg-[#061a2b]/80 border border-slate-200 dark:border-[#1e3a52] text-center shadow-xs">
                        <div class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-0.5">{{ app()->getLocale() === 'ar' ? 'التصنيع' : 'Production' }}</div>
                        <div class="text-lg font-black text-slate-900 dark:text-purple-400 font-mono">{{ $systemMetrics['work_orders_count'] }}</div>
                    </div>
                    <div class="p-3 rounded-2xl bg-white/80 dark:bg-[#061a2b]/80 border border-slate-200 dark:border-[#1e3a52] text-center shadow-xs">
                        <div class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-0.5">{{ app()->getLocale() === 'ar' ? 'الموظفين' : 'HR Staff' }}</div>
                        <div class="text-lg font-black text-slate-900 dark:text-teal-400 font-mono">{{ $systemMetrics['employees_count'] }}</div>
                    </div>
                    <div class="p-3 rounded-2xl bg-white/80 dark:bg-[#061a2b]/80 border border-slate-200 dark:border-[#1e3a52] text-center shadow-xs">
                        <div class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-0.5">{{ app()->getLocale() === 'ar' ? 'القيود المالية' : 'Transactions' }}</div>
                        <div class="text-lg font-black text-slate-900 dark:text-indigo-400 font-mono">{{ $systemMetrics['financial_transactions_count'] }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- 2. QUICK SEARCH, EXPAND/COLLAPSE ALL & SECTION PILLS NAVIGATOR -->
        <!-- ========================================================================= -->
        <div id="logic-cycles" class="scroll-mt-24 space-y-4">
            <div class="bg-white dark:bg-[#081d2f] border border-slate-200 dark:border-[#1e3a52] rounded-3xl p-4 sm:p-5 space-y-4 shadow-sm">
                <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
                    <!-- Search Filter Input -->
                    <div class="relative flex-1">
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none text-slate-400">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </span>
                        <input type="text" id="guideSearchInput" oninput="filterGuideSections(this.value)" 
                               placeholder="{{ app()->getLocale() === 'ar' ? 'ابحث عن أي قسم، شاشة، أو دورة عمل (مثال: Finance Dashboard, Sales Invoices, حجر صحي، راتب، أمر شراء)...' : 'Filter modules, screens, and cycles (e.g. Finance Dashboard, Sales Invoices, Quarantine, PO, Payroll)...' }}"
                               class="w-full pl-4 pr-10 py-2.5 text-sm rounded-xl bg-slate-50 dark:bg-[#061a2b] border border-slate-200 dark:border-[#1e3a52] text-slate-800 dark:text-white placeholder-slate-400 focus:outline-hidden focus:ring-2 focus:ring-[#0284c7]">
                    </div>

                    <!-- Expand/Collapse All Button & Counter -->
                    <div class="flex items-center gap-2.5 self-center">
                        <button type="button" onclick="toggleAllModules()" class="px-3.5 py-2 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-200 bg-slate-100 hover:bg-slate-200 dark:bg-[#061a2b] dark:hover:bg-[#0c273e] border border-slate-300 dark:border-[#1e3a52] transition-all flex items-center gap-2 cursor-pointer">
                            <i id="btnToggleAllIcon" class="fa-solid fa-arrows-down-to-line text-xs"></i>
                            <span id="btnToggleAllText">{{ app()->getLocale() === 'ar' ? 'فتح كافة الأقسام (Expand All)' : 'Expand All Sections' }}</span>
                        </button>

                        <div class="text-xs font-bold text-slate-500 dark:text-slate-400 hidden sm:flex items-center gap-1.5">
                            <i class="fa-solid fa-layer-group text-[#0284c7]"></i>
                            <span>{{ app()->getLocale() === 'ar' ? '11 قسم معماري' : '11 Main Modules' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Dynamic Quick Domain Pills -->
                <div class="flex items-center gap-2 overflow-x-auto pb-1.5 scrollbar-none">
                    <button type="button" class="bz-tab-pill active" onclick="filterBySectionTag('all', this)">
                        <i class="fa-solid fa-border-all"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'كافة الأقسام' : 'All Modules' }}</span>
                    </button>
                    <button type="button" class="bz-tab-pill" onclick="filterBySectionTag('finance', this)">
                        <i class="fa-solid fa-coins text-teal-500"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'المالية والحسابات (Finance)' : 'Finance & Ledger' }}</span>
                    </button>
                    <button type="button" class="bz-tab-pill" onclick="filterBySectionTag('products', this)">
                        <i class="fa-solid fa-box-open text-sky-500"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'الكتالوج والمنتجات (Products)' : 'Catalog & Products' }}</span>
                    </button>
                    <button type="button" class="bz-tab-pill" onclick="filterBySectionTag('purchasing', this)">
                        <i class="fa-solid fa-cart-shopping text-emerald-500"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'المشتريات والموردين (Purchasing)' : 'Purchasing & Vendors' }}</span>
                    </button>
                    <button type="button" class="bz-tab-pill" onclick="filterBySectionTag('qc', this)">
                        <i class="fa-solid fa-snowflake text-cyan-500"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'سلسلة التبريد والحجر (QC)' : 'Cold-Chain & QC' }}</span>
                    </button>
                    <button type="button" class="bz-tab-pill" onclick="filterBySectionTag('inventory', this)">
                        <i class="fa-solid fa-warehouse text-amber-500"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'المخزون والتشغيلات (Inventory)' : 'Inventory & Batches' }}</span>
                    </button>
                    <button type="button" class="bz-tab-pill" onclick="filterBySectionTag('manufacturing', this)">
                        <i class="fa-solid fa-industry text-purple-500"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'التصنيع cGMP (Manufacturing)' : 'Manufacturing' }}</span>
                    </button>
                    <button type="button" class="bz-tab-pill" onclick="filterBySectionTag('sales', this)">
                        <i class="fa-solid fa-truck-fast text-rose-500"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'الطلبات والشحن (Orders)' : 'Orders & Shipping' }}</span>
                    </button>
                    <button type="button" class="bz-tab-pill" onclick="filterBySectionTag('field-crm', this)">
                        <i class="fa-solid fa-user-doctor text-blue-500"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'المندوبون والعيادات (Field MR)' : 'Field MR & Clinics' }}</span>
                    </button>
                    <button type="button" class="bz-tab-pill" onclick="filterBySectionTag('sales-crm', this)">
                        <i class="fa-solid fa-handshake text-orange-500"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'علاقات العملاء (Deals CRM)' : 'Sales Deals CRM' }}</span>
                    </button>
                    <button type="button" class="bz-tab-pill" onclick="filterBySectionTag('hr', this)">
                        <i class="fa-solid fa-users text-green-500"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'الموارد والرواتب (HR)' : 'HR & Payroll' }}</span>
                    </button>
                    <button type="button" class="bz-tab-pill" onclick="filterBySectionTag('security', this)">
                        <i class="fa-solid fa-shield-halved text-indigo-500"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'السلايدر والأمان (Security)' : 'Slider & Security' }}</span>
                    </button>
                </div>
            </div>

            <!-- ========================================================================= -->
            <!-- MODULE 1: FINANCE & GENERAL LEDGER (ACCORDION) -->
            <!-- ========================================================================= -->
            <div class="bz-accordion-module" data-section="finance" data-keywords="finance dashboard sales invoices purchase invoices collection entry customer payments payment entry disb cash bank accounts coa مالية شجرة حسابات فواتير مبيعات مشتريات قبض صرف">
                <!-- Clickable Accordion Header -->
                <div class="bz-accordion-header p-5 sm:p-6 flex items-center justify-between gap-4 border-b border-slate-200 dark:border-[#1e3a52]" onclick="toggleModuleAccordion('module-finance')">
                    <div class="flex items-center gap-4">
                        <span class="w-12 h-12 rounded-2xl bg-gradient-to-br from-teal-600 to-emerald-800 text-white flex items-center justify-center text-xl shadow-md shadow-teal-600/25 flex-shrink-0">
                            <i class="fa-solid fa-coins"></i>
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="badge text-[10px] font-black uppercase tracking-wider bg-teal-100 dark:bg-teal-500/20 text-teal-800 dark:text-teal-300 border border-teal-300 dark:border-teal-500/30 px-2 py-0.5 rounded-full">CORE FINANCE</span>
                                <h2 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white">
                                    {{ app()->getLocale() === 'ar' ? 'المنظومة المالية، شجرة الحسابات وسندات الفواتير والتحصيل' : 'Enterprise Finance, General Ledger, COA & Invoicing' }}
                                </h2>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5">
                                {{ app()->getLocale() === 'ar' ? '7 شاشات رئيسية: لوحة المالية، فواتير المبيعات، فواتير المشتريات، سندات التحصيل، دفعات العملاء، سندات الصرف، وشجرة الحسابات' : '7 Sub-Screens: Dashboard, Sales Invoices, Purchase Invoices, Collection Entry, Customer Payments, Payment Entry, Cash & Bank Accounts' }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-slate-100 dark:bg-[#061a2b] text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-[#1e3a52]">7 Screens</span>
                        <i id="module-finance-chevron" class="bz-accordion-chevron fa-solid fa-chevron-down text-slate-400 text-sm" style="transform: rotate(180deg);"></i>
                    </div>
                </div>

                <!-- Collapsible Body -->
                <div id="module-finance-body" class="bz-accordion-body p-6 sm:p-8 space-y-6" style="display: block;">
                    <!-- Sub-screen Navigation Pills Bar -->
                    <div class="bz-subscreen-nav flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none border-b border-slate-200 dark:border-[#1e3a52]">
                        <button type="button" class="bz-subscreen-pill active" onclick="switchSubScreen('module-finance', 'fin-screen-dashboard', this)">
                            <i class="fa-solid fa-chart-line text-teal-500"></i>
                            <span>Finance Dashboard (لوحة المالية)</span>
                        </button>
                        <button type="button" class="bz-subscreen-pill" onclick="switchSubScreen('module-finance', 'fin-screen-sales-invoices', this)">
                            <i class="fa-solid fa-file-invoice text-sky-500"></i>
                            <span>Sales Invoices (فواتير المبيعات)</span>
                        </button>
                        <button type="button" class="bz-subscreen-pill" onclick="switchSubScreen('module-finance', 'fin-screen-purchase-invoices', this)">
                            <i class="fa-solid fa-file-invoice-dollar text-emerald-500"></i>
                            <span>Purchase Invoices (فواتير المشتريات)</span>
                        </button>
                        <button type="button" class="bz-subscreen-pill" onclick="switchSubScreen('module-finance', 'fin-screen-collections', this)">
                            <i class="fa-solid fa-hand-holding-dollar text-amber-500"></i>
                            <span>Collection Entry Auto (سندات التحصيل)</span>
                        </button>
                        <button type="button" class="bz-subscreen-pill" onclick="switchSubScreen('module-finance', 'fin-screen-customer-payments', this)">
                            <i class="fa-solid fa-credit-card text-indigo-500"></i>
                            <span>Customer Payments (دفعات العملاء)</span>
                        </button>
                        <button type="button" class="bz-subscreen-pill" onclick="switchSubScreen('module-finance', 'fin-screen-payment-entry', this)">
                            <i class="fa-solid fa-money-bill-transfer text-rose-500"></i>
                            <span>Payment Entry Disb (سندات الصرف)</span>
                        </button>
                        <button type="button" class="bz-subscreen-pill" onclick="switchSubScreen('module-finance', 'fin-screen-accounts-coa', this)">
                            <i class="fa-solid fa-sitemap text-purple-500"></i>
                            <span>Cash & Bank Accounts (شجرة الحسابات)</span>
                        </button>
                    </div>

                    <!-- SUB-SCREEN 1: FINANCE DASHBOARD -->
                    <div id="fin-screen-dashboard" class="bz-subscreen-view space-y-6">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50 dark:bg-[#061a2b] p-4 rounded-2xl border border-slate-200 dark:border-[#1e3a52]">
                            <div>
                                <h3 class="font-black text-slate-900 dark:text-white text-base">Finance Dashboard — مركز قيادة المؤشرات المالية والتدفق النقدي</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Route: <span class="font-mono text-sky-600 dark:text-sky-400">/admin/finance/dashboard</span> | Controller: <span class="font-mono">FinanceDashboardController@index</span></p>
                            </div>
                            <a href="{{ route('admin.finance.dashboard') }}" target="_blank" class="btn btn-primary text-xs self-start sm:self-center">
                                <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                                <span>فتح الشاشة الحية</span>
                            </a>
                        </div>

                        <!-- Full Cycle Steps for Dashboard -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-teal-600 dark:text-teal-400">PHASE 01</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">تجميع القيود اللحظية</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">قراءة جداول المعاملات المالية `financial_transactions` وربطها بالمبيعات والمشتريات فور حدوثها.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-sky-600 dark:text-sky-400">PHASE 02</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">احتساب الذمم والتحصيل</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">حساب الذمم المدينة (AR) والدائنة (AP) ومعدل دوران الصناديق النقدية والبنوك.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-amber-600 dark:text-amber-400">PHASE 03</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">منحنى التدفق النقدي</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">رسم بياني للمقبوضات والمدفوعات والمصروفات التشغيلية لحساب صافي السيولة المتاحة.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-emerald-600 dark:text-emerald-400">PHASE 04</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">ميزان المراجعة والأرباح</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">توليد تقرير الأرباح التشغيلية، هوامش الربحية للمنتجات، والتنبيه بالالتزامات المستحقة.</p>
                            </div>
                        </div>

                        <!-- Real Screenshot Preview Frame -->
                        <div class="space-y-2">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5"><i class="fa-solid fa-camera text-teal-500"></i> المعاينة الحقيقية للشاشة (انقر للتكبير السلس):</span>
                            <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot13_finance_dashboard.png') }}', 'لوحة تحكم المنظومة المالية والتدفق النقدي', 'متابعة الذمم المدينة والدائنة، إجمالي المبيعات، ومصروفات التشغيل وحركة الصناديق والبنوك.', 13)">
                                <img src="{{ asset('images/guide/shot13_finance_dashboard.png') }}" alt="Finance Dashboard" class="w-full h-64 object-cover object-top" loading="lazy">
                                <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-3xl"></i></div>
                                <div class="p-3 bg-slate-50 dark:bg-[#061a2b] text-xs font-bold text-slate-800 dark:text-slate-200 flex justify-between">
                                    <span>لوحة مؤشرات المالية والتدفق النقدي</span>
                                    <span class="text-teal-600 dark:text-teal-400 font-mono">Live High-Res Preview</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SUB-SCREEN 2: SALES INVOICES -->
                    <div id="fin-screen-sales-invoices" class="bz-subscreen-view space-y-6" style="display: none;">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50 dark:bg-[#061a2b] p-4 rounded-2xl border border-slate-200 dark:border-[#1e3a52]">
                            <div>
                                <h3 class="font-black text-slate-900 dark:text-white text-base">Sales Invoices — إدارة فواتير المبيعات والفوترة الضريبية</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Route: <span class="font-mono text-sky-600 dark:text-sky-400">/admin/finance/sales-invoices</span> | Controller: <span class="font-mono">SalesInvoiceController@index</span></p>
                            </div>
                            <span class="badge text-xs bg-sky-100 dark:bg-sky-500/20 text-sky-700 dark:text-sky-300 font-bold px-3 py-1 rounded-full">Zakat Tax Compliant</span>
                        </div>

                        <!-- Full Cycle Steps -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-sky-600 dark:text-sky-400">PHASE 01</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">توليد الفاتورة التلقائي</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">إنشاء الفاتورة فور اعتماد الطلب من المتجر أو نقطة البيع وتخصيص رقم تسلسلي فريد.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-teal-600 dark:text-teal-400">PHASE 02</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">احتساب ضريبة القيمة المضافة</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">فصل قيمة المنتجات عن ضريبة الـ VAT (15%) وتوليد الـ Cryptographic Stamp و QR Code.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-amber-600 dark:text-amber-400">PHASE 03</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">تسجيل استحقاق العميل</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">ترحيل القيد المزدوج: (من حـ/ العملاء أو الصندوق إلى حـ/ المبيعات وحـ/ الضريبة المستحقة).</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-emerald-600 dark:text-emerald-400">PHASE 04</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">السداد والتحصيل</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">إغلاق الفاتورة فور السداد الإلكتروني أو تسجيل سند التحصيل النقدي عبر زر Receive Payment.</p>
                            </div>
                        </div>

                        <!-- Real Screenshot Preview Frame -->
                        <div class="space-y-2">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5"><i class="fa-solid fa-camera text-sky-500"></i> المعاينة الحقيقية لفواتير المبيعات:</span>
                            <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot15_sales_orders.png') }}', 'فواتير المبيعات وسندات الطلبات الضريبية', 'جدول فواتير المبيعات، ومطابقة مبالغ الدفع الإلكتروني والتحصيل.', 15)">
                                <img src="{{ asset('images/guide/shot15_sales_orders.png') }}" alt="Sales Invoices" class="w-full h-64 object-cover object-top" loading="lazy">
                                <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-3xl"></i></div>
                                <div class="p-3 bg-slate-50 dark:bg-[#061a2b] text-xs font-bold text-slate-800 dark:text-slate-200">سجل فواتير المبيعات وسندات الفوترة الإلكترونية</div>
                            </div>
                        </div>
                    </div>

                    <!-- SUB-SCREEN 3: PURCHASE INVOICES -->
                    <div id="fin-screen-purchase-invoices" class="bz-subscreen-view space-y-6" style="display: none;">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50 dark:bg-[#061a2b] p-4 rounded-2xl border border-slate-200 dark:border-[#1e3a52]">
                            <div>
                                <h3 class="font-black text-slate-900 dark:text-white text-base">Purchase Invoices — فواتير الموردين والمشتريات والمطابقة الثلاثية</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Route: <span class="font-mono text-emerald-600 dark:text-emerald-400">/admin/finance/purchase-invoices</span> | Controller: <span class="font-mono">PurchaseInvoiceController</span></p>
                            </div>
                            <span class="badge text-xs bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 font-bold px-3 py-1 rounded-full">3-Way Matched</span>
                        </div>

                        <!-- Full Cycle Steps -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-emerald-600 dark:text-emerald-400">PHASE 01</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">استلام فاتورة المورد</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">تسجيل رقم فاتورة المورد الخارجية وتاريخها وشروط السداد وفترة الائتمان المتفق عليها.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-sky-600 dark:text-sky-400">PHASE 02</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">المطابقة الثلاثية</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">مطابقة بنود الفاتورة مع أمر الشراء PO وسند استلام البضائع الفعلي GRN لضمان عدم زيادة الأسعار أو الكميات.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-amber-600 dark:text-amber-400">PHASE 03</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">قيد استحقاق الموردين</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">ترحيل القيد المزدوج: (من حـ/ المخزون وحـ/ ضريبة المدخلات إلى حـ/ الموردين - الذمم الدائنة AP).</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-teal-600 dark:text-teal-400">PHASE 04</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">إصدار أمر السداد</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">جدولة السداد في موعد الاستحقاق عبر شيك أو تحويل بنكي وإصدار سند الصرف المرتبط.</p>
                            </div>
                        </div>

                        <!-- Real Screenshot Preview Frame -->
                        <div class="space-y-2">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5"><i class="fa-solid fa-camera text-emerald-500"></i> المعاينة الحقيقية لأوامر وفواتير المشتريات:</span>
                            <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot11_purchases_po.png') }}', 'أوامر وفواتير الشراء واعتماد الموردين', 'جدول أوامر الشراء، متابعة التوريد ومطابقة فواتير المواد الخام.', 11)">
                                <img src="{{ asset('images/guide/shot11_purchases_po.png') }}" alt="Purchase Invoices" class="w-full h-64 object-cover object-top" loading="lazy">
                                <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-3xl"></i></div>
                                <div class="p-3 bg-slate-50 dark:bg-[#061a2b] text-xs font-bold text-slate-800 dark:text-slate-200">سجل فواتير المشتريات والاعتمادات الرسمية</div>
                            </div>
                        </div>
                    </div>

                    <!-- SUB-SCREEN 4: COLLECTION ENTRY AUTO -->
                    <div id="fin-screen-collections" class="bz-subscreen-view space-y-6" style="display: none;">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50 dark:bg-[#061a2b] p-4 rounded-2xl border border-slate-200 dark:border-[#1e3a52]">
                            <div>
                                <h3 class="font-black text-slate-900 dark:text-white text-base">Collection Entry Auto — سندات التحصيل والمزامنة التلقائية لمبيعات المتجر</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Route: <span class="font-mono text-amber-600 dark:text-amber-400">/admin/finance/collections</span> | Action: <span class="font-mono">syncOnlineSales</span></p>
                            </div>
                            <span class="badge text-xs bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300 font-bold px-3 py-1 rounded-full">Auto-Sync Engine</span>
                        </div>

                        <!-- Full Cycle Steps -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-amber-600 dark:text-amber-400">PHASE 01</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">التقاط مبيعات المتجر</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">سحب أوامر البيع المسلمة والمدفوعة من بوابة المتجر عبر دالة المزامنة التلقائية (Sync Online Sales).</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-sky-600 dark:text-sky-400">PHASE 02</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">توليد سند القبض</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">توليد سند قبض رسمي (Collection Voucher) مع توثيق طريقة السداد (فيزا، مدى، تحويل، COD).</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-teal-600 dark:text-teal-400">PHASE 03</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">تغذية الصندوق والبنك</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">إيداع المبلغ في الحساب البنكي أو صندوق التحصيل وتخفيض مديونية العميل المحققة.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-emerald-600 dark:text-emerald-400">PHASE 04</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">تصدير شيت الإكسيل</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">تصدير تقرير المقبوضات بصيغة Excel لمطابقة التسويات مع بوابات الدفع الإلكترونية.</p>
                            </div>
                        </div>

                        <!-- Real Screenshot Preview Frame -->
                        <div class="space-y-2">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5"><i class="fa-solid fa-camera text-amber-500"></i> المعاينة الحقيقية لسندات التحصيل والمقبوضات:</span>
                            <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot13_finance_dashboard.png') }}', 'لوحة تحكم المنظومة المالية وسندات التحصيل', 'متابعة الذمم المدينة والدائنة، إجمالي المبيعات، ومصروفات التشغيل وحركة الصناديق.', 13)">
                                <img src="{{ asset('images/guide/shot13_finance_dashboard.png') }}" alt="Collections" class="w-full h-64 object-cover object-top" loading="lazy">
                                <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-3xl"></i></div>
                                <div class="p-3 bg-slate-50 dark:bg-[#061a2b] text-xs font-bold text-slate-800 dark:text-slate-200">سندات التحصيل ومزامنة المبيعات اللحظية</div>
                            </div>
                        </div>
                    </div>

                    <!-- SUB-SCREEN 5: CUSTOMER PAYMENTS -->
                    <div id="fin-screen-customer-payments" class="bz-subscreen-view space-y-6" style="display: none;">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50 dark:bg-[#061a2b] p-4 rounded-2xl border border-slate-200 dark:border-[#1e3a52]">
                            <div>
                                <h3 class="font-black text-slate-900 dark:text-white text-base">Customer Payments — دفعات العملاء والمراكز الطبية B2B</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Route: <span class="font-mono text-indigo-600 dark:text-indigo-400">/admin/finance/customer-payments</span> | Controller: <span class="font-mono">CustomerPaymentController</span></p>
                            </div>
                            <span class="badge text-xs bg-indigo-100 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300 font-bold px-3 py-1 rounded-full">B2B Settlement</span>
                        </div>

                        <!-- Full Cycle Steps -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-indigo-600 dark:text-indigo-400">PHASE 01</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">تحديد العميل والفاتورة</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">اختيار حساب العميل أو المستشفى وعرض الفواتير المستحقة مع الأرصدة الآجلة.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-sky-600 dark:text-sky-400">PHASE 02</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">تسجيل الدفعة والخصم</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">إدخال المبلغ المدفوع وتطبيق الخصم النقدي الممنوح إن وجد مع توثيق الحساب البنكي.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-amber-600 dark:text-amber-400">PHASE 03</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">طباعة سند القبض Voucher</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">طباعة إشعار رسمي معتمد بالسداد برقم مرجعي وتحديث الحد الائتماني للعميل.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-emerald-600 dark:text-emerald-400">PHASE 04</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">تحديث كشف الحساب</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">خصم الرصيد المدين للعميل فوراً وتعديل تقرير أعمار الديون (AR Aging Report).</p>
                            </div>
                        </div>

                        <!-- Real Screenshot Preview Frame -->
                        <div class="space-y-2">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5"><i class="fa-solid fa-camera text-indigo-500"></i> المعاينة الحقيقية لدفعات وسندات العملاء:</span>
                            <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot13_finance_dashboard.png') }}', 'لوحة تحكم دفعات العملاء والذمم المدينة', 'متابعة سداد المستشفيات والمراكز الطبية، وأرصدة الديون المفتوحة.', 13)">
                                <img src="{{ asset('images/guide/shot13_finance_dashboard.png') }}" alt="Customer Payments" class="w-full h-64 object-cover object-top" loading="lazy">
                                <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-3xl"></i></div>
                                <div class="p-3 bg-slate-50 dark:bg-[#061a2b] text-xs font-bold text-slate-800 dark:text-slate-200">سجل دفعات العملاء وسندات القبض المعتمدة</div>
                            </div>
                        </div>
                    </div>

                    <!-- SUB-SCREEN 6: PAYMENT ENTRY DISB -->
                    <div id="fin-screen-payment-entry" class="bz-subscreen-view space-y-6" style="display: none;">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50 dark:bg-[#061a2b] p-4 rounded-2xl border border-slate-200 dark:border-[#1e3a52]">
                            <div>
                                <h3 class="font-black text-slate-900 dark:text-white text-base">Payment Entry Disb — سندات الصرف وسداد مستحقات الموردين ومقاصة الشيكات</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Route: <span class="font-mono text-rose-600 dark:text-rose-400">/admin/finance/payments</span> | Action: <span class="font-mono">clearCheque</span></p>
                            </div>
                            <span class="badge text-xs bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300 font-bold px-3 py-1 rounded-full">Disbursement Engine</span>
                        </div>

                        <!-- Full Cycle Steps -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-rose-600 dark:text-rose-400">PHASE 01</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">إنشاء سند الصرف</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">تحديد المستفيد (مورد، مقاول، مصاريف تشغيلية)، وتحديد الحساب البنكي أو الخزينة الصارفة.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-amber-600 dark:text-amber-400">PHASE 02</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">تحديد وسيلة السداد والشيكات</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">توثيق رقم الشيك، تاريخ الاستحقاق، واسم البنك، مع تعيين الحالة المبدئية (Pending / Issued).</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-teal-600 dark:text-teal-400">PHASE 03</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">مقاصة وصرف الشيك Clear</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">عند استحقاق الشيك وصرفه فعلياً من البنك، يتم تفعيل زر (Clear Cheque) لتأكيد الخصم المالي النهائي.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-emerald-600 dark:text-emerald-400">PHASE 04</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">الترحيل والتسوية البنكية</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">توليد قيد الصرف النهائي: (من حـ/ الموردين إلى حـ/ البنك)، ومطابقة رصيد كشف الحساب البنكي.</p>
                            </div>
                        </div>

                        <!-- Real Screenshot Preview Frame -->
                        <div class="space-y-2">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5"><i class="fa-solid fa-camera text-rose-500"></i> المعاينة الحقيقية لسندات الصرف والمدفوعات:</span>
                            <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot13_finance_dashboard.png') }}', 'لوحة تحكم سندات الصرف ومستحقات الموردين', 'إدارة الدفعات الصادرة، مقاصة الشيكات، وتوثيق أذونات الصرف النقدية.', 13)">
                                <img src="{{ asset('images/guide/shot13_finance_dashboard.png') }}" alt="Disbursements" class="w-full h-64 object-cover object-top" loading="lazy">
                                <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-3xl"></i></div>
                                <div class="p-3 bg-slate-50 dark:bg-[#061a2b] text-xs font-bold text-slate-800 dark:text-slate-200">سندات الصرف ومتابعة مقاصة الشيكات البنكية</div>
                            </div>
                        </div>
                    </div>

                    <!-- SUB-SCREEN 7: CASH & BANK ACCOUNTS (COA) -->
                    <div id="fin-screen-accounts-coa" class="bz-subscreen-view space-y-6" style="display: none;">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50 dark:bg-[#061a2b] p-4 rounded-2xl border border-slate-200 dark:border-[#1e3a52]">
                            <div>
                                <h3 class="font-black text-slate-900 dark:text-white text-base">Cash & Bank Accounts — دليل شجرة الحسابات العامة والخزائن والبنوك</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Route: <span class="font-mono text-purple-600 dark:text-purple-400">/admin/finance/accounts</span> | Controller: <span class="font-mono">FinancialAccountController@index</span></p>
                            </div>
                            <a href="{{ route('admin.finance.accounts.index') }}" target="_blank" class="btn btn-secondary text-xs self-start sm:self-center">
                                <i class="fa-solid fa-sitemap text-xs"></i>
                                <span>فتح شجرة الحسابات COA</span>
                            </a>
                        </div>

                        <!-- Full Cycle Steps -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-purple-600 dark:text-purple-400">PHASE 01</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">هيكلة دليل الحسابات</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">بناء الشجرة المحاسبية (الأصول 1، الخصوم 2، حقوق الملكية 3، الإيرادات 4، المصروفات 5).</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-sky-600 dark:text-sky-400">PHASE 02</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">تكويد الخزائن والبنوك</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">تعريف حسابات البنوك برقم الآيبان (IBAN)، وربط عهد الصناديق النقدية بالمسؤولين الماليين.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-amber-600 dark:text-amber-400">PHASE 03</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">سندات التحويل المالي Transfer</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">إجراء تحويلات بين الحسابات المالية (من الخزينة للبنك أو العكس) عبر أمر التحويل المعتمد.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-emerald-600 dark:text-emerald-400">PHASE 04</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">ميزان المراجعة والقوائم</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">تحديث أرصدة الحسابات تلقائياً مع كل حركة قيد، واستخراج كشف حساب تفصيلي لأي بند.</p>
                            </div>
                        </div>

                        <!-- Real Screenshot Preview Frame -->
                        <div class="space-y-2">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5"><i class="fa-solid fa-camera text-purple-500"></i> المعاينة الحقيقية لشجرة الحسابات COA:</span>
                            <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot19_finance_chart_of_accounts.png') }}', 'شجرة دليل الحسابات العام والخزائن (COA)', 'إدارة الأصول والخصوم والمصروفات والإيرادات والمستويات المحاسبية الشجرية.', 19)">
                                <img src="{{ asset('images/guide/shot19_finance_chart_of_accounts.png') }}" alt="Chart of Accounts" class="w-full h-64 object-cover object-top" loading="lazy">
                                <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-3xl"></i></div>
                                <div class="p-3 bg-slate-50 dark:bg-[#061a2b] text-xs font-bold text-slate-800 dark:text-slate-200">دليل شجرة الحسابات المعياري وحسابات البنوك والخزائن</div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- ========================================================================= -->
            <!-- MODULE 2: CATALOG & PRODUCTS (ACCORDION) -->
            <!-- ========================================================================= -->
            <div class="bz-accordion-module" data-section="products" data-keywords="products catalog wizard customizer bundles pricing clinical ingredients كتالوج منتجات معالج تسعير باقات تآزر مخصص شاشات">
                <!-- Header -->
                <div class="bz-accordion-header p-5 sm:p-6 flex items-center justify-between gap-4 border-b border-slate-200 dark:border-[#1e3a52]" onclick="toggleModuleAccordion('module-products')">
                    <div class="flex items-center gap-4">
                        <span class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#0A4F78] to-[#0284c7] text-white flex items-center justify-center text-xl shadow-md shadow-[#0A4F78]/25 flex-shrink-0">
                            <i class="fa-solid fa-box-open"></i>
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="badge text-[10px] font-black uppercase tracking-wider bg-sky-100 dark:bg-sky-500/20 text-sky-800 dark:text-sky-300 border border-sky-300 dark:border-sky-500/30 px-2 py-0.5 rounded-full">CORE CATALOG</span>
                                <h2 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white">
                                    {{ app()->getLocale() === 'ar' ? 'الكتالوج، معالج المنتجات، مخصص الأقسام وباقات التآزر' : 'Product Lifecycle, 7-Step Wizard & Customizer' }}
                                </h2>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5">
                                {{ app()->getLocale() === 'ar' ? 'معالج الخطوات السبع، الأقسام الثمانية، الحفظ التلقائي للمسودات، وباقات التآزر Synergistic Bundles' : '7-Step Wizard, 8 Monograph Sections, Desktop vs Mobile Switches & Bundles' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-slate-100 dark:bg-[#061a2b] text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-[#1e3a52]">8 Steps / Screens</span>
                        <i id="module-products-chevron" class="bz-accordion-chevron fa-solid fa-chevron-down text-slate-400 text-sm"></i>
                    </div>
                </div>

                <!-- Body -->
                <div id="module-products-body" class="bz-accordion-body p-6 sm:p-8 space-y-6" style="display: none;">
                    <div class="bz-subscreen-nav flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none border-b border-slate-200 dark:border-[#1e3a52]">
                        <button type="button" class="bz-subscreen-pill active" onclick="switchSubScreen('module-products', 'prod-screen-step1', this)">
                            <span>الخطوة 1: الرمز والمسودة</span>
                        </button>
                        <button type="button" class="bz-subscreen-pill" onclick="switchSubScreen('module-products', 'prod-screen-step2', this)">
                            <span>الخطوة 2: المحتوى والوصف</span>
                        </button>
                        <button type="button" class="bz-subscreen-pill" onclick="switchSubScreen('module-products', 'prod-screen-step3', this)">
                            <span>الخطوة 3: التسعير والضريبة</span>
                        </button>
                        <button type="button" class="bz-subscreen-pill" onclick="switchSubScreen('module-products', 'prod-screen-step4', this)">
                            <span>الخطوة 4: الأصول والوسائط</span>
                        </button>
                        <button type="button" class="bz-subscreen-pill" onclick="switchSubScreen('module-products', 'prod-screen-step5', this)">
                            <span>الخطوة 5: الملف الطبي والجرعات</span>
                        </button>
                        <button type="button" class="bz-subscreen-pill" onclick="switchSubScreen('module-products', 'prod-screen-step6', this)">
                            <span>الخطوة 6: مخصص الشاشات</span>
                        </button>
                        <button type="button" class="bz-subscreen-pill" onclick="switchSubScreen('module-products', 'prod-screen-step7', this)">
                            <span>الخطوة 6 (موسع): باقات التآزر</span>
                        </button>
                        <button type="button" class="bz-subscreen-pill" onclick="switchSubScreen('module-products', 'prod-screen-step8', this)">
                            <span>الخطوة 7: المخزون والباركود</span>
                        </button>
                    </div>

                    <!-- STEP 1 PANE -->
                    <div id="prod-screen-step1" class="bz-subscreen-view space-y-6">
                        <div class="flex items-center justify-between gap-3 bg-slate-50 dark:bg-[#061a2b] p-4 rounded-2xl border border-slate-200 dark:border-[#1e3a52]">
                            <div>
                                <h3 class="font-black text-slate-900 dark:text-white text-base">الخطوة 1: الرمز المعياري والتصنيف الأساسي والحفظ التلقائي</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Route: <span class="font-mono text-sky-600 dark:text-sky-400">/admin/products/create</span> | AJAX Auto-Drafting Engine</p>
                            </div>
                            <a href="{{ route('admin.products.create') }}" target="_blank" class="btn btn-primary text-xs">معالج منتج جديد</a>
                        </div>
                        <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot1_step1_core.png') }}', 'الخطوة 1: الرمز والتصنيف الأساسي والحفظ التلقائي', 'تسجيل رمز SKU المعياري، الرابط الدائم، وتحديد التصنيف العلاجي مع الحفظ التلقائي.', 1)">
                            <img src="{{ asset('images/guide/shot1_step1_core.png') }}" alt="Step 1 SKU" class="w-full h-64 object-cover object-top" loading="lazy">
                            <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-3xl"></i></div>
                            <div class="p-3 bg-slate-50 dark:bg-[#061a2b] text-xs font-bold text-slate-800 dark:text-slate-200">الخطوة 1: توليد رمز SKU والمسار الدائم SEO</div>
                        </div>
                    </div>

                    <!-- STEP 2 PANE -->
                    <div id="prod-screen-step2" class="bz-subscreen-view space-y-6" style="display: none;">
                        <div class="bg-slate-50 dark:bg-[#061a2b] p-4 rounded-2xl border border-slate-200 dark:border-[#1e3a52]">
                            <h3 class="font-black text-slate-900 dark:text-white text-base">الخطوة 2: المحتوى الدوائي ثنائي اللغة والملخص التسويقي</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">كتابة الاسم التجاري والعلمي والوصف السريري المفصل باللغتين العربية والإنجليزية.</p>
                        </div>
                        <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot2_step2_content.png') }}', 'الخطوة 2: المحتوى الدوائي ثنائي اللغة', 'كتابة الاسم التجاري والعلمي والملخص التسويقي والوصف السريري.', 2)">
                            <img src="{{ asset('images/guide/shot2_step2_content.png') }}" alt="Step 2 Content" class="w-full h-64 object-cover object-top" loading="lazy">
                            <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-3xl"></i></div>
                            <div class="p-3 bg-slate-50 dark:bg-[#061a2b] text-xs font-bold text-slate-800 dark:text-slate-200">الخطوة 2: المحتوى الدوائي ثنائي اللغة والوصف التسويقي</div>
                        </div>
                    </div>

                    <!-- STEP 3 PANE -->
                    <div id="prod-screen-step3" class="bz-subscreen-view space-y-6" style="display: none;">
                        <div class="bg-slate-50 dark:bg-[#061a2b] p-4 rounded-2xl border border-slate-200 dark:border-[#1e3a52]">
                            <h3 class="font-black text-slate-900 dark:text-white text-base">الخطوة 3: السياسة السعرية وهوامش الربح والضرائب</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">تحديد سعر البيع الأساسي والتخفيض، احتساب ضريبة القيمة المضافة ديناميكياً، وتكلفة الاستيراد.</p>
                        </div>
                        <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot3_step3_pricing.png') }}', 'الخطوة 3: السياسة السعرية وهوامش الربح والضرائب', 'تحديد سعر البيع الأساسي والتخفيض واحتساب الضريبة وهامش الربح.', 3)">
                            <img src="{{ asset('images/guide/shot3_step3_pricing.png') }}" alt="Step 3 Pricing" class="w-full h-64 object-cover object-top" loading="lazy">
                            <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-3xl"></i></div>
                            <div class="p-3 bg-slate-50 dark:bg-[#061a2b] text-xs font-bold text-slate-800 dark:text-slate-200">الخطوة 3: تسعير المنتجات وحساب ضريبة القيمة المضافة</div>
                        </div>
                    </div>

                    <!-- STEP 4 PANE -->
                    <div id="prod-screen-step4" class="bz-subscreen-view space-y-6" style="display: none;">
                        <div class="bg-slate-50 dark:bg-[#061a2b] p-4 rounded-2xl border border-slate-200 dark:border-[#1e3a52]">
                            <h3 class="font-black text-slate-900 dark:text-white text-base">الخطوة 4: الأصول ومعرض الوسائط المفرغة</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">رفع صورة العبوة المفرغة (Master Packshot) وتوليد الصور المصغرة ومعرض الزوايا المتعددة.</p>
                        </div>
                        <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot4_step4_media.png') }}', 'الخطوة 4: الأصول ومعرض الوسائط المفرغة', 'رفع صورة العبوة المفرغة وتوليد الصور المصغرة والمعرض.', 4)">
                            <img src="{{ asset('images/guide/shot4_step4_media.png') }}" alt="Step 4 Media" class="w-full h-64 object-cover object-top" loading="lazy">
                            <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-3xl"></i></div>
                            <div class="p-3 bg-slate-50 dark:bg-[#061a2b] text-xs font-bold text-slate-800 dark:text-slate-200">الخطوة 4: رفع صورة Packshot ومعرض الزوايا</div>
                        </div>
                    </div>

                    <!-- STEP 5 PANE -->
                    <div id="prod-screen-step5" class="bz-subscreen-view space-y-6" style="display: none;">
                        <div class="bg-slate-50 dark:bg-[#061a2b] p-4 rounded-2xl border border-slate-200 dark:border-[#1e3a52]">
                            <h3 class="font-black text-slate-900 dark:text-white text-base">الخطوة 5: الملف الطبي وجدول العيارات والجرعات</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">جدول المكونات الفعالة والعيارات المعيارية والمؤشرات الفسيولوجية المستهدفة.</p>
                        </div>
                        <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot5_step5_clinical.png') }}', 'الخطوة 5: الملف الطبي وجدول العيارات والجرعات', 'جدول المكونات الفعالة والعيارات والمؤشرات الفسيولوجية.', 5)">
                            <img src="{{ asset('images/guide/shot5_step5_clinical.png') }}" alt="Step 5 Clinical" class="w-full h-64 object-cover object-top" loading="lazy">
                            <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-3xl"></i></div>
                            <div class="p-3 bg-slate-50 dark:bg-[#061a2b] text-xs font-bold text-slate-800 dark:text-slate-200">الخطوة 5: جدول التركيب السريري والجرعات المعتمدة</div>
                        </div>
                    </div>

                    <!-- STEP 6 PANE (CUSTOMIZER) -->
                    <div id="prod-screen-step6" class="bz-subscreen-view space-y-6" style="display: none;">
                        <div class="bg-slate-50 dark:bg-[#061a2b] p-4 rounded-2xl border border-slate-200 dark:border-[#1e3a52]">
                            <h3 class="font-black text-slate-900 dark:text-white text-base">الخطوة 6: مخصص أقسام صفحة المنتج (Desktop vs Mobile Switches)</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">التحكم المنفصل لظهور الأقسام الثمانية على الكمبيوتر والموبايل وحالة فتح الأكورديون الافتراضية.</p>
                        </div>
                        <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot6_step6_customizer.png') }}', 'الخطوة 6: مخصص أقسام صفحة المنتج', 'التحكم المنفصل لظهور الأقسام على الكمبيوتر والموبايل.', 6)">
                            <img src="{{ asset('images/guide/shot6_step6_customizer.png') }}" alt="Step 6 Customizer" class="w-full h-64 object-cover object-top" loading="lazy">
                            <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-3xl"></i></div>
                            <div class="p-3 bg-slate-50 dark:bg-[#061a2b] text-xs font-bold text-slate-800 dark:text-slate-200">الخطوة 6: مخصص الشاشات ومفاتيح الأقسام الثمانية</div>
                        </div>
                    </div>

                    <!-- STEP 7 PANE (BUNDLES) -->
                    <div id="prod-screen-step7" class="bz-subscreen-view space-y-6" style="display: none;">
                        <div class="bg-slate-50 dark:bg-[#061a2b] p-4 rounded-2xl border border-slate-200 dark:border-[#1e3a52]">
                            <h3 class="font-black text-slate-900 dark:text-white text-base">الخطوة 6 (موسع): باقات التآزر الحيوي الموحدة (Synergistic Bundles)</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">اختيار المكملات التكميلية وربطها في باقة متكاملة بخصم مخصص وزر إضافة جماعي موحد.</p>
                        </div>
                        <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot7_step6_bundle.png') }}', 'الخطوة 6 (موسع): باقات التآزر الحيوي الموحدة', 'اختيار المكملات وربطها في باقة متكاملة بخصم مخصص.', 7)">
                            <img src="{{ asset('images/guide/shot7_step6_bundle.png') }}" alt="Step 6 Bundles" class="w-full h-64 object-cover object-top" loading="lazy">
                            <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-3xl"></i></div>
                            <div class="p-3 bg-slate-50 dark:bg-[#061a2b] text-xs font-bold text-slate-800 dark:text-slate-200">الخطوة 6 (موسع): استوديو باقات التآزر وزر الشراء الجماعي</div>
                        </div>
                    </div>

                    <!-- STEP 8 PANE (INVENTORY) -->
                    <div id="prod-screen-step8" class="bz-subscreen-view space-y-6" style="display: none;">
                        <div class="bg-slate-50 dark:bg-[#061a2b] p-4 rounded-2xl border border-slate-200 dark:border-[#1e3a52]">
                            <h3 class="font-black text-slate-900 dark:text-white text-base">الخطوة 7: تكامل المخزون والباركود وإعادة الطلب</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">تحديد نوع المخزون، باركود العبوة، وزن الشحن، وتعيين حدود التنبيه المبكر لإعادة الطلب.</p>
                        </div>
                        <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot8_step7_inventory.png') }}', 'الخطوة 7: تكامل المخزون والباركود وإعادة الطلب', 'تحديد نوع المخزون والباركود وحدود إعادة الطلب.', 8)">
                            <img src="{{ asset('images/guide/shot8_step7_inventory.png') }}" alt="Step 7 Inventory" class="w-full h-64 object-cover object-top" loading="lazy">
                            <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-3xl"></i></div>
                            <div class="p-3 bg-slate-50 dark:bg-[#061a2b] text-xs font-bold text-slate-800 dark:text-slate-200">الخطوة 7: ربط الباركود وتعيين حدود إعادة الطلب</div>
                        </div>
                    </div>

                </div>
            </div>

                        <!-- ========================================================================= -->
            <!-- MODULE 3: PURCHASING & PROCUREMENT (ACCORDION) -->
            <!-- ========================================================================= -->
            <div class="bz-accordion-module" data-section="purchasing" data-keywords="purchasing suppliers vendors po quotations procurement مشتريات موردين أوامر شراء عروض أسعار">
                <div class="bz-accordion-header p-5 sm:p-6 flex items-center justify-between gap-4 border-b border-slate-200 dark:border-[#1e3a52]" onclick="toggleModuleAccordion('module-purchasing')">
                    <div class="flex items-center gap-4">
                        <span class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-600 to-teal-700 text-white flex items-center justify-center text-xl shadow-md shadow-emerald-600/25 flex-shrink-0">
                            <i class="fa-solid fa-cart-shopping"></i>
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="badge text-[10px] font-black uppercase tracking-wider bg-emerald-100 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-500/30 px-2 py-0.5 rounded-full">PROCUREMENT</span>
                                <h2 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white">
                                    {{ app()->getLocale() === 'ar' ? 'المشتريات، الموردين وعروض الأسعار وأوامر الشراء' : 'Procurement, Suppliers & Purchase Orders' }}
                                </h2>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5">
                                {{ app()->getLocale() === 'ar' ? '3 شاشات رئيسية: أوامر الشراء الرسمية PO، سجل الموردين المعتمدين cGMP، وعروض الأسعار واستدراج العطاءات RFQ' : '3 Sub-Screens: Purchase Orders (POs), Approved Suppliers Directory & RFQ Price Quotations' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-slate-100 dark:bg-[#061a2b] text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-[#1e3a52]">3 Screens</span>
                        <i id="module-purchasing-chevron" class="bz-accordion-chevron fa-solid fa-chevron-down text-slate-400 text-sm"></i>
                    </div>
                </div>

                <div id="module-purchasing-body" class="bz-accordion-body p-6 sm:p-8 space-y-6" style="display: none;">
                    <div class="bz-subscreen-nav flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none border-b border-slate-200 dark:border-[#1e3a52]">
                        <button type="button" class="bz-subscreen-pill active" onclick="switchSubScreen('module-purchasing', 'purch-screen-po', this)">
                            <i class="fa-solid fa-file-invoice text-emerald-500"></i>
                            <span>Purchase Orders (أوامر الشراء 3-Way Match)</span>
                        </button>
                        <button type="button" class="bz-subscreen-pill" onclick="switchSubScreen('module-purchasing', 'purch-screen-suppliers', this)">
                            <i class="fa-solid fa-truck-field text-teal-500"></i>
                            <span>Approved Suppliers (سجل الموردين المعتمدين)</span>
                        </button>
                        <button type="button" class="bz-subscreen-pill" onclick="switchSubScreen('module-purchasing', 'purch-screen-rfq', this)">
                            <i class="fa-solid fa-tags text-sky-500"></i>
                            <span>Supplier RFQs (عروض الأسعار واستدراج العطاءات)</span>
                        </button>
                    </div>

                    <!-- SUB-SCREEN 1: PURCHASE ORDERS -->
                    <div id="purch-screen-po" class="bz-subscreen-view space-y-6">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50 dark:bg-[#061a2b] p-4 rounded-2xl border border-slate-200 dark:border-[#1e3a52]">
                            <div>
                                <h3 class="font-black text-slate-900 dark:text-white text-base">Purchase Orders (POs) — أوامر الشراء الرسمية والمطابقة الثلاثية 3-Way Matching</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Route: <span class="font-mono text-emerald-600 dark:text-emerald-400">/admin/purchase-orders</span> | Controller: <span class="font-mono">PurchaseOrderController@index</span></p>
                            </div>
                            <a href="{{ route('admin.purchase-orders.index') }}" target="_blank" class="btn btn-secondary text-xs self-start sm:self-center">
                                <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                                <span>فتح أوامر الشراء</span>
                            </a>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-emerald-600 dark:text-emerald-400">PHASE 01</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">طلب الشراء والمبرر</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">تحديد المواد الخام المطلوبة، كمياتها بناءً على خطة الإنتاج، وتحديد المورد المعتمد.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-sky-600 dark:text-sky-400">PHASE 02</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">الاعتماد المالي والإداري</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">مراجعة الأسعار وشروط الدفع والاعتماد المزدوج وتوليد رقم الـ PO التسلسلي الرسمي.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-amber-600 dark:text-amber-400">PHASE 03</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">إرسال التعميد للمورد</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">تصدير وثيقة أمر الشراء بصيغة PDF وتعميد شروط الشحن Incoterms وموعد الوصول المحدد.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-teal-600 dark:text-teal-400">PHASE 04</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">المطابقة الثلاثية 3-Way Match</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">ربط الـ PO تلقائياً بسند استلام البضائع (GRN) وفاتورة المورد قبل تحويلها للصرف المالي.</p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5"><i class="fa-solid fa-camera text-emerald-500"></i> المعاينة الحقيقية لأوامر الشراء الرسمية:</span>
                            <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot11_purchases_po.png') }}', 'أوامر الشراء الرسمية واعتماد الموردين', 'جدول أوامر الشراء (POs)، اعتماد شروط التوريد، ومتابعة فواتير المواد الخام والمطابقة الثلاثية.', 11)">
                                <img src="{{ asset('images/guide/shot11_purchases_po.png') }}" alt="PO Index" class="w-full h-64 object-cover object-top" loading="lazy">
                                <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-3xl"></i></div>
                                <div class="p-3 bg-slate-50 dark:bg-[#061a2b] text-xs font-bold text-slate-800 dark:text-slate-200 flex justify-between">
                                    <span>جدول أوامر الشراء الرسمية (Purchase Orders)</span>
                                    <span class="text-emerald-600 dark:text-emerald-400 font-mono">Live High-Res Preview</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SUB-SCREEN 2: APPROVED SUPPLIERS -->
                    <div id="purch-screen-suppliers" class="bz-subscreen-view space-y-6" style="display: none;">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50 dark:bg-[#061a2b] p-4 rounded-2xl border border-slate-200 dark:border-[#1e3a52]">
                            <div>
                                <h3 class="font-black text-slate-900 dark:text-white text-base">Approved Suppliers Directory — سجل الموردين المعتمدين وتأهيل معايير cGMP الدوائية</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Route: <span class="font-mono text-teal-600 dark:text-teal-400">/admin/suppliers</span> | Controller: <span class="font-mono">SupplierController@index</span></p>
                            </div>
                            <a href="{{ route('admin.suppliers.index') }}" target="_blank" class="btn btn-secondary text-xs self-start sm:self-center">
                                <i class="fa-solid fa-truck-field text-xs"></i>
                                <span>سجل الموردين</span>
                            </a>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-teal-600 dark:text-teal-400">PHASE 01</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">تأهيل المورد والتراخيص</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">فحص شهادات ISO 9001، شهادة cGMP الدوائية، والسجل التجاري والتراخيص النظامية.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-sky-600 dark:text-sky-400">PHASE 02</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">التصنيف وشروط الائتمان</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">تحديد فترة السداد (30، 60، 90 يوماً)، سقف الائتمان المالي، وعملة التوريد الأساسية.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-amber-600 dark:text-amber-400">PHASE 03</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">سجل التوريد ومؤشر الجودة</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">حساب مؤشر التزام المورد بمواعيد التسليم ونسبة الشحنات المطابقة لمواصفات الجودة.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-emerald-600 dark:text-emerald-400">PHASE 04</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">كشف الحساب والمصادقة</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">إصدار كشف حساب تفصيلي للذمم الدائنة ومطابقة الأرصدة وإجراء المصادقات الدورية.</p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5"><i class="fa-solid fa-camera text-teal-500"></i> المعاينة الحقيقية لسجل الموردين المعتمدين:</span>
                            <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot11_purchases_po.png') }}', 'سجل الموردين المعتمدين وتأهيل الجودة', 'قاعدة بيانات الموردين المؤهلين، تصنيفات الجودة، ومتابعة شروط الائتمان والتوريد.', 11)">
                                <img src="{{ asset('images/guide/shot11_purchases_po.png') }}" alt="Suppliers Directory" class="w-full h-64 object-cover object-top" loading="lazy">
                                <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-3xl"></i></div>
                                <div class="p-3 bg-slate-50 dark:bg-[#061a2b] text-xs font-bold text-slate-800 dark:text-slate-200 flex justify-between">
                                    <span>سجل واعتماد الموردين الدوائيين cGMP</span>
                                    <span class="text-teal-600 dark:text-teal-400 font-mono">Live High-Res Preview</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SUB-SCREEN 3: SUPPLIER RFQ -->
                    <div id="purch-screen-rfq" class="bz-subscreen-view space-y-6" style="display: none;">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50 dark:bg-[#061a2b] p-4 rounded-2xl border border-slate-200 dark:border-[#1e3a52]">
                            <div>
                                <h3 class="font-black text-slate-900 dark:text-white text-base">Supplier RFQs — استدراج ومقارنة عروض الأسعار للمواد الخام والمستلزمات</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Route: <span class="font-mono text-sky-600 dark:text-sky-400">/admin/supplier-quotations</span> | Controller: <span class="font-mono">SupplierQuotationController@index</span></p>
                            </div>
                            <a href="{{ route('admin.supplier-quotations.index') }}" target="_blank" class="btn btn-secondary text-xs self-start sm:self-center">
                                <i class="fa-solid fa-tags text-xs"></i>
                                <span>عروض الأسعار</span>
                            </a>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-sky-600 dark:text-sky-400">PHASE 01</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">طرح طلب السعر RFQ</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">تحديد المواصفات الدوائية بدقة وإرسال طلب التسعير لعدة موردين معتمدين بالتوازي.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-amber-600 dark:text-amber-400">PHASE 02</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">مصفوفة المقارنة الفنية</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">مقارنة الأسعار، تكاليف الشحن، فترات الصلاحية، وشهادات المنشأ في جدول موحد.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-teal-600 dark:text-teal-400">PHASE 03</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">التفاوض والترسية</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">اختيار العرض الأنسب وترسية الطلب وتوثيق مبررات الاختيار إدارياً للمراجعة.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-emerald-600 dark:text-emerald-400">PHASE 04</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">التحويل التلقائي لأمر شراء</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">تحويل عرض السعر المعتمد مباشرة بنقرة واحدة إلى أمر شراء رسمي (PO) دون إعادة إدخال البيانات.</p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5"><i class="fa-solid fa-camera text-sky-500"></i> المعاينة الحقيقية لعروض أسعار الموردين:</span>
                            <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot11_purchases_po.png') }}', 'عروض أسعار الموردين واستدراج العطاءات RFQ', 'مقارنة عروض أسعار المواد الخام، شروط التوريد، وتحويل العرض المعتمد لأمر شراء رسمي.', 11)">
                                <img src="{{ asset('images/guide/shot11_purchases_po.png') }}" alt="Supplier RFQs" class="w-full h-64 object-cover object-top" loading="lazy">
                                <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-3xl"></i></div>
                                <div class="p-3 bg-slate-50 dark:bg-[#061a2b] text-xs font-bold text-slate-800 dark:text-slate-200 flex justify-between">
                                    <span>مصفوفة مقارنة عروض الأسعار (Supplier RFQs)</span>
                                    <span class="text-sky-600 dark:text-sky-400 font-mono">Live High-Res Preview</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================================================= -->
            <!-- MODULE 4: COLD-CHAIN & QC QUARANTINE (ACCORDION) -->
            <!-- ========================================================================= -->
            <div class="bz-accordion-module" data-section="qc" data-keywords="qc cold-chain quarantine inspection temperature coa حجر صحي فحص جودة سلسلة تبريد عينات استلام">
                <div class="bz-accordion-header p-5 sm:p-6 flex items-center justify-between gap-4 border-b border-slate-200 dark:border-[#1e3a52]" onclick="toggleModuleAccordion('module-qc')">
                    <div class="flex items-center gap-4">
                        <span class="w-12 h-12 rounded-2xl bg-gradient-to-br from-cyan-600 to-blue-700 text-white flex items-center justify-center text-xl shadow-md shadow-cyan-600/25 flex-shrink-0">
                            <i class="fa-solid fa-snowflake"></i>
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="badge text-[10px] font-black uppercase tracking-wider bg-cyan-100 dark:bg-cyan-500/20 text-cyan-800 dark:text-cyan-300 border border-cyan-300 dark:border-cyan-500/30 px-2 py-0.5 rounded-full">COLD-CHAIN & QC</span>
                                <h2 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white">
                                    {{ app()->getLocale() === 'ar' ? 'سلسلة التبريد، الحجر الصحي وضبط الجودة والفحص المخبري' : 'Cold-Chain Logistics, Quarantine & QC Intake' }}
                                </h2>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5">
                                {{ app()->getLocale() === 'ar' ? '3 شاشات رئيسية: سندات استلام البضائع GRN ومسجلات الحرارة، مستودعات الحجر الصحي، وسحب العينات والإفراج الدوائي COA' : '3 Sub-Screens: Goods Receipts (GRN) & Temp Loggers, Quarantine Depots, Laboratory Sampling & COA' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-slate-100 dark:bg-[#061a2b] text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-[#1e3a52]">3 Screens</span>
                        <i id="module-qc-chevron" class="bz-accordion-chevron fa-solid fa-chevron-down text-slate-400 text-sm"></i>
                    </div>
                </div>

                <div id="module-qc-body" class="bz-accordion-body p-6 sm:p-8 space-y-6" style="display: none;">
                    <div class="bz-subscreen-nav flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none border-b border-slate-200 dark:border-[#1e3a52]">
                        <button type="button" class="bz-subscreen-pill active" onclick="switchSubScreen('module-qc', 'qc-screen-grn', this)">
                            <i class="fa-solid fa-temperature-arrow-down text-cyan-500"></i>
                            <span>Goods Receipts GRN (سندات الاستلام ومسجلات التبريد)</span>
                        </button>
                        <button type="button" class="bz-subscreen-pill" onclick="switchSubScreen('module-qc', 'qc-screen-quarantine', this)">
                            <i class="fa-solid fa-lock text-amber-500"></i>
                            <span>Quarantine Depots (مستودعات الحجر الصحي والعزل)</span>
                        </button>
                        <button type="button" class="bz-subscreen-pill" onclick="switchSubScreen('module-qc', 'qc-screen-coa', this)">
                            <i class="fa-solid fa-vial-circle-check text-emerald-500"></i>
                            <span>Lab Sampling & COA (سحب العينات والإفراج التام)</span>
                        </button>
                    </div>

                    <!-- SUB-SCREEN 1: GOODS RECEIPTS (GRN) -->
                    <div id="qc-screen-grn" class="bz-subscreen-view space-y-6">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50 dark:bg-[#061a2b] p-4 rounded-2xl border border-slate-200 dark:border-[#1e3a52]">
                            <div>
                                <h3 class="font-black text-slate-900 dark:text-white text-base">Goods Receipts (GRN) — سندات استلام البضائع وقراءة مسجلات درجات الحرارة</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Route: <span class="font-mono text-cyan-600 dark:text-cyan-400">/admin/goods-receipts</span> | Controller: <span class="font-mono">GoodsReceiptController@index</span></p>
                            </div>
                            <a href="{{ route('admin.goods-receipts.index') }}" target="_blank" class="btn btn-secondary text-xs self-start sm:self-center">
                                <i class="fa-solid fa-boxes-packing text-xs"></i>
                                <span>سندات الاستلام GRN</span>
                            </a>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-cyan-600 dark:text-cyan-400">PHASE 01</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">فحص التبريد والأختام</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">قراءة مسجلات الحرارة (Data Loggers) والتأكد من نطاق التخزين المعتمد (2°C - 8°C).</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-amber-600 dark:text-amber-400">PHASE 02</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">تفريغ وفحص الطرود</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">مطابقة عدد الكراتين، سلامة الأغلفة المانعة للعبث، ومطابقة رقم التشغيلة Lot مع وثيقة الشحن.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-purple-600 dark:text-purple-400">PHASE 03</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">توليد سند الاستلام GRN</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">إصدار سند الاستلام الرسمي برقم تسلسلي وربطه بأمر الشراء PO الأصلي.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-emerald-600 dark:text-emerald-400">PHASE 04</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">الإيداع التلقائي بالحجر</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">نقل البضاعة فوراً لمستودع الحجر الصحي مع وضع بطاقة حظر صفراء إلكترونية.</p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5"><i class="fa-solid fa-camera text-cyan-500"></i> المعاينة الحقيقية لسندات استلام البضائع وفحص الحرارة:</span>
                            <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot12_goods_receipts.png') }}', 'سندات استلام البضائع وفحص الأختام والحرارة (GRN)', 'جدول استلام الشحنات المبردة، تسجيل مسجلات الحرارة (Data Loggers)، وإيداع التشغيلات في الحجر الصحي.', 12)">
                                <img src="{{ asset('images/guide/shot12_goods_receipts.png') }}" alt="GRN Index" class="w-full h-64 object-cover object-top" loading="lazy">
                                <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-3xl"></i></div>
                                <div class="p-3 bg-slate-50 dark:bg-[#061a2b] text-xs font-bold text-slate-800 dark:text-slate-200 flex justify-between">
                                    <span>سندات استلام البضائع ومسجلات الحرارة (GRN)</span>
                                    <span class="text-cyan-600 dark:text-cyan-400 font-mono">Live High-Res Preview</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SUB-SCREEN 2: QUARANTINE DEPOTS -->
                    <div id="qc-screen-quarantine" class="bz-subscreen-view space-y-6" style="display: none;">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50 dark:bg-[#061a2b] p-4 rounded-2xl border border-slate-200 dark:border-[#1e3a52]">
                            <div>
                                <h3 class="font-black text-slate-900 dark:text-white text-base">Quarantine Depots — مستودعات الحجر الصحي والعزل البرمجي الإلزامي</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Route: <span class="font-mono text-amber-600 dark:text-amber-400">/admin/warehouses</span> | Controller: <span class="font-mono">WarehouseController@index</span></p>
                            </div>
                            <a href="{{ route('admin.warehouses.index') }}" target="_blank" class="btn btn-secondary text-xs self-start sm:self-center">
                                <i class="fa-solid fa-warehouse text-xs"></i>
                                <span>مستودعات الحجر</span>
                            </a>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-amber-600 dark:text-amber-400">PHASE 01</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">العزل الفيزيائي والبرمجي</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">إيداع الشحنة في مستودع الحجر الصحي وحظر صرفها كلياً ومنع ظهورها بالمتجر.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-rose-600 dark:text-rose-400">PHASE 02</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">حظر البيع والتداول</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">قفل المخزون برمجياً فلا يمكن لنقاط البيع أو المتجر سحب أي حبة من هذه التشغيلة.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-sky-600 dark:text-sky-400">PHASE 03</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">متابعة فترة الاحتجاز</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">سجل زمني لعدد أيام الاحتجاز تحت الحجر لحين صدور التقارير المخبرية المعتمدة.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-emerald-600 dark:text-emerald-400">PHASE 04</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">فك الحظر أو الرفض</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">الإفراج ونقلها للمستودع المتاح أو إشعار الرفض للتشغيلات غير المطابقة لإعادتها للمورد.</p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5"><i class="fa-solid fa-camera text-amber-500"></i> المعاينة الحقيقية لمستودعات الحجر الصحي:</span>
                            <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot12_goods_receipts.png') }}', 'مستودعات الحجر الصحي والعزل الدوائي', 'إدارة مستودعات العزل، قفل التشغيلات برمجياً، ومنع التداول قبل إشعار المطابقة.', 12)">
                                <img src="{{ asset('images/guide/shot12_goods_receipts.png') }}" alt="Quarantine Depot" class="w-full h-64 object-cover object-top" loading="lazy">
                                <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-3xl"></i></div>
                                <div class="p-3 bg-slate-50 dark:bg-[#061a2b] text-xs font-bold text-slate-800 dark:text-slate-200 flex justify-between">
                                    <span>مستودعات الحجر الصحي والعزل الدوائي</span>
                                    <span class="text-amber-600 dark:text-amber-400 font-mono">Live High-Res Preview</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SUB-SCREEN 3: LAB SAMPLING & COA -->
                    <div id="qc-screen-coa" class="bz-subscreen-view space-y-6" style="display: none;">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50 dark:bg-[#061a2b] p-4 rounded-2xl border border-slate-200 dark:border-[#1e3a52]">
                            <div>
                                <h3 class="font-black text-slate-900 dark:text-white text-base">Laboratory Sampling & COA — سحب العينات المخبرية وشهادة التحليل والإفراج النهائي</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Route: <span class="font-mono text-emerald-600 dark:text-emerald-400">/admin/batches</span> | Controller: <span class="font-mono">RawMaterialBatchController@show</span></p>
                            </div>
                            <a href="{{ route('admin.batches.index') }}" target="_blank" class="btn btn-secondary text-xs self-start sm:self-center">
                                <i class="fa-solid fa-vials text-xs"></i>
                                <span>سجل التشغيلات والفحص</span>
                            </a>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-purple-600 dark:text-purple-400">PHASE 01</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">سحب العينات الإحصائي</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">سحب عينات عشوائية إحصائية وفق MIL-STD-105E وتوثيق أرقام الحاويات المسحوبة.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-sky-600 dark:text-sky-400">PHASE 02</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">فحص المقايسة والنقاوة</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">إجراء فحوصات HPLC، اختبار المعادن الثقيلة، والتأكد من خلو الشحنة من الملوثات الميكروبية.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-amber-600 dark:text-amber-400">PHASE 03</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">مطابقة شهادة التحليل COA</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">مطابقة نتائج الفحص المخبري مع مواصفات شهادة التحليل الأصلية المعتمدة من المصنع.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-emerald-600 dark:text-emerald-400">PHASE 04</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">الإفراج الدوائي cGMP Release</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">توقيع مسؤول الجودة QP ونقل الشحنة آلياً للمستودع المركزي مع تحديث حالتها إلى Available.</p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5"><i class="fa-solid fa-camera text-emerald-500"></i> المعاينة الحقيقية لسحب العينات وشهادة التحليل COA:</span>
                            <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot12_goods_receipts.png') }}', 'سحب العينات المخبرية وشهادة التحليل COA', 'فحص العينات، مطابقة تقارير الميكروبيولوجي، وتوقيع الإفراج الدوائي النهائي.', 12)">
                                <img src="{{ asset('images/guide/shot12_goods_receipts.png') }}" alt="COA Release" class="w-full h-64 object-cover object-top" loading="lazy">
                                <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-3xl"></i></div>
                                <div class="p-3 bg-slate-50 dark:bg-[#061a2b] text-xs font-bold text-slate-800 dark:text-slate-200 flex justify-between">
                                    <span>سحب العينات وشهادات التحليل المخبرية COA</span>
                                    <span class="text-emerald-600 dark:text-emerald-400 font-mono">Live High-Res Preview</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================================================= -->
            <!-- MODULE 5: INVENTORY & DUAL ALLOCATION (ACCORDION) -->
            <!-- ========================================================================= -->
            <div class="bz-accordion-module" data-section="inventory" data-keywords="inventory batches stock transfer locations online central warehouse مخزون تشغيلات تحويل مستودعات صلاحية">
                <div class="bz-accordion-header p-5 sm:p-6 flex items-center justify-between gap-4 border-b border-slate-200 dark:border-[#1e3a52]" onclick="toggleModuleAccordion('module-inventory')">
                    <div class="flex items-center gap-4">
                        <span class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-500 to-amber-700 text-white flex items-center justify-center text-xl shadow-md shadow-amber-500/25 flex-shrink-0">
                            <i class="fa-solid fa-warehouse"></i>
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="badge text-[10px] font-black uppercase tracking-wider bg-amber-100 dark:bg-amber-500/20 text-amber-800 dark:text-amber-300 border border-amber-300 dark:border-amber-500/30 px-2 py-0.5 rounded-full">INVENTORY CONTROL</span>
                                <h2 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white">
                                    {{ app()->getLocale() === 'ar' ? 'المخزون والتشغيلات، التوزيع المزدوج والمناقلات المخزنية' : 'Inventory Batches, Dual Allocation & Stock Transfers' }}
                                </h2>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5">
                                {{ app()->getLocale() === 'ar' ? '3 شاشات رئيسية: تتبع التشغيلات والصلاحية FEFO/FIFO، التوزيع المزدوج وحماية المتجر، والمناقلات بين المستودعات' : '3 Sub-Screens: Batches & FIFO Expiry, Central vs Online Allocation & Stock Transfers' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-slate-100 dark:bg-[#061a2b] text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-[#1e3a52]">3 Screens</span>
                        <i id="module-inventory-chevron" class="bz-accordion-chevron fa-solid fa-chevron-down text-slate-400 text-sm"></i>
                    </div>
                </div>

                <div id="module-inventory-body" class="bz-accordion-body p-6 sm:p-8 space-y-6" style="display: none;">
                    <div class="bz-subscreen-nav flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none border-b border-slate-200 dark:border-[#1e3a52]">
                        <button type="button" class="bz-subscreen-pill active" onclick="switchSubScreen('module-inventory', 'inv-screen-batches', this)">
                            <i class="fa-solid fa-barcode text-amber-500"></i>
                            <span>Batches & Expiry (التشغيلات وتتبع الصلاحية FEFO)</span>
                        </button>
                        <button type="button" class="bz-subscreen-pill" onclick="switchSubScreen('module-inventory', 'inv-screen-allocation', this)">
                            <i class="fa-solid fa-store text-sky-500"></i>
                            <span>Central vs Online (التوزيع المزدوج وحماية المتجر)</span>
                        </button>
                        <button type="button" class="bz-subscreen-pill" onclick="switchSubScreen('module-inventory', 'inv-screen-transfers', this)">
                            <i class="fa-solid fa-dolly text-emerald-500"></i>
                            <span>Stock Transfers (المناقلات المخزنية والجرد)</span>
                        </button>
                    </div>

                    <!-- SUB-SCREEN 1: BATCHES & EXPIRY -->
                    <div id="inv-screen-batches" class="bz-subscreen-view space-y-6">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50 dark:bg-[#061a2b] p-4 rounded-2xl border border-slate-200 dark:border-[#1e3a52]">
                            <div>
                                <h3 class="font-black text-slate-900 dark:text-white text-base">Batches & Expiry — إدارة التشغيلات، أرقام Lot وتتبع الصلاحية FEFO/FIFO</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Route: <span class="font-mono text-amber-600 dark:text-amber-400">/admin/batches</span> | Controller: <span class="font-mono">RawMaterialBatchController@index</span></p>
                            </div>
                            <a href="{{ route('admin.batches.index') }}" target="_blank" class="btn btn-secondary text-xs self-start sm:self-center">
                                <i class="fa-solid fa-barcode text-xs"></i>
                                <span>سجل التشغيلات</span>
                            </a>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-amber-600 dark:text-amber-400">PHASE 01</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">تكويد التشغيلة والباركود</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">تخصيص كود تشغيلة فريد (Batch Lot #) وتاريخ الإنتاج وتاريخ انتهاء الصلاحية المعتمد.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-sky-600 dark:text-sky-400">PHASE 02</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">الصرف بأولوية الصلاحية FEFO</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">برمجة سحب البضاعة للطلبات بحسب الأقرب انتهاءً (First-Expired, First-Out) لمنع التلف.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-rose-600 dark:text-rose-400">PHASE 03</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">التنبيه المبكر للصلاحيات</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">نظام إنذار تلقائي قبل 6 أشهر، 3 أشهر، وشهر من تاريخ الانتهاء لعرض خصومات أو حظر الصرف.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-emerald-600 dark:text-emerald-400">PHASE 04</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">التتبع العكسي والسحب Recall</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">إمكانية حصر كافة العملاء والمستشفيات المستلمين لتشغيلة معينة بنقرة واحدة في حال السحب الدوائي.</p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5"><i class="fa-solid fa-camera text-amber-500"></i> المعاينة الحقيقية للتشغيلات والمخزون:</span>
                            <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot8_step7_inventory.png') }}', 'الخطوة 7: ربط المخزون، التشغيلات والباركود', 'إدارة أرقام التشغيلات، تواريخ الصلاحية، والتوزيع التلقائي بين المستودع والمتجر.', 8)">
                                <img src="{{ asset('images/guide/shot8_step7_inventory.png') }}" alt="Batches Index" class="w-full h-64 object-cover object-top" loading="lazy">
                                <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-3xl"></i></div>
                                <div class="p-3 bg-slate-50 dark:bg-[#061a2b] text-xs font-bold text-slate-800 dark:text-slate-200 flex justify-between">
                                    <span>جدول تتبع التشغيلات والصلاحيات الدوائية (FEFO Batches)</span>
                                    <span class="text-amber-600 dark:text-amber-400 font-mono">Live High-Res Preview</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SUB-SCREEN 2: CENTRAL VS ONLINE ALLOCATION -->
                    <div id="inv-screen-allocation" class="bz-subscreen-view space-y-6" style="display: none;">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50 dark:bg-[#061a2b] p-4 rounded-2xl border border-slate-200 dark:border-[#1e3a52]">
                            <div>
                                <h3 class="font-black text-slate-900 dark:text-white text-base">Central vs Online Allocation — التوزيع المزدوج وحماية المتجر من البيع الزائد (Oversell Prevention)</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Route: <span class="font-mono text-sky-600 dark:text-sky-400">/admin/warehouses</span> | Component: <span class="font-mono">StockAllocatorEngine</span></p>
                            </div>
                            <span class="badge text-xs bg-sky-100 dark:bg-sky-500/20 text-sky-700 dark:text-sky-300 font-bold px-3 py-1 rounded-full">Buffer Shield Active</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-sky-600 dark:text-sky-400">PHASE 01</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">الرصيد المركزي الكلي</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">تسجيل إجمالي الكميات الفعلية الموجودة في المستودع المركزي الرئيسي بعد إفراج الجودة.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-teal-600 dark:text-teal-400">PHASE 02</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">تخصيص رصيد المتجر الإلكتروني</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">تحديد نسبة أو كمية محددة تظهر للجمهور في المتجر مع عزل باقي المخزون لطلبات الـ B2B والمشافي.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-amber-600 dark:text-amber-400">PHASE 03</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">الحجز اللحظي للطلبات</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">عند قيام العميل بالدفع، يتم حجز الكمية فوراً ومنع أي عميل آخر من شرائها في نفس اللحظة.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-emerald-600 dark:text-emerald-400">PHASE 04</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">إعادة التغذية الذاتية Buffer</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">تغذية رصيد المتجر تلقائياً من المستودع المركزي عند انخفاض الرصيد عن حد الأمان المحدد.</p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5"><i class="fa-solid fa-camera text-sky-500"></i> المعاينة الحقيقية للتوزيع المزدوج:</span>
                            <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot8_step7_inventory.png') }}', 'التوزيع المزدوج بين المستودع والمتجر الإلكتروني', 'توزيع الحصص، تخصيص رصيد المتجر، وضبط حدود إعادة الطلب التلقائية.', 8)">
                                <img src="{{ asset('images/guide/shot8_step7_inventory.png') }}" alt="Stock Allocator" class="w-full h-64 object-cover object-top" loading="lazy">
                                <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-3xl"></i></div>
                                <div class="p-3 bg-slate-50 dark:bg-[#061a2b] text-xs font-bold text-slate-800 dark:text-slate-200 flex justify-between">
                                    <span>محرك التوزيع المزدوج وحصص المتجر الإلكتروني</span>
                                    <span class="text-sky-600 dark:text-sky-400 font-mono">Live High-Res Preview</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SUB-SCREEN 3: STOCK TRANSFERS -->
                    <div id="inv-screen-transfers" class="bz-subscreen-view space-y-6" style="display: none;">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50 dark:bg-[#061a2b] p-4 rounded-2xl border border-slate-200 dark:border-[#1e3a52]">
                            <div>
                                <h3 class="font-black text-slate-900 dark:text-white text-base">Stock Transfers & Barcode Audit — المناقلات المخزنية بين الفروع والجرد الباركودي</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Route: <span class="font-mono text-emerald-600 dark:text-emerald-400">/admin/inventory/transfers</span> | Controller: <span class="font-mono">InventoryController@transfers</span></p>
                            </div>
                            <a href="{{ route('admin.inventory.transfers') }}" target="_blank" class="btn btn-secondary text-xs self-start sm:self-center">
                                <i class="fa-solid fa-dolly text-xs"></i>
                                <span>المناقلات المخزنية</span>
                            </a>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-emerald-600 dark:text-emerald-400">PHASE 01</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">طلب المناقلة الداخلي</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">تحديد المستودع المصدر والمستودع الوجهة، واختيار الأصناف والتشغيلات المراد نقلها.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-amber-600 dark:text-amber-400">PHASE 02</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">الصرف وحالة العبور (In-Transit)</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">خصم الكميات من المستودع المصدر ووضعها في حساب بضاعة بالطريق تحت مسؤولية السائق.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-sky-600 dark:text-sky-400">PHASE 03</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">فحص الباركود عند الاستلام</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">مسح الباركود عند وصول الشحنة للمستودع الهدف لمطابقة الكميات ومنع العجز أو الفقد.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-teal-600 dark:text-teal-400">PHASE 04</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">إغلاق وتحديث القيود</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">إضافة الرصيد المستلم للمستودع الهدف وإصدار إشعار إتمام المناقلة إلكترونياً.</p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5"><i class="fa-solid fa-camera text-emerald-500"></i> المعاينة الحقيقية للمناقلات المخزنية:</span>
                            <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot8_step7_inventory.png') }}', 'المناقلات المخزنية والجرد الباركودي', 'سجل أذونات الصرف والإضافة، بضاعة بالطريق، وتأكيد الاستلام بالباركود.', 8)">
                                <img src="{{ asset('images/guide/shot8_step7_inventory.png') }}" alt="Transfers" class="w-full h-64 object-cover object-top" loading="lazy">
                                <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-3xl"></i></div>
                                <div class="p-3 bg-slate-50 dark:bg-[#061a2b] text-xs font-bold text-slate-800 dark:text-slate-200 flex justify-between">
                                    <span>سجل أذونات التحويل والمناقلة بين المستودعات</span>
                                    <span class="text-emerald-600 dark:text-emerald-400 font-mono">Live High-Res Preview</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================================================= -->
            <!-- MODULE 6: MANUFACTURING & WORK ORDERS (ACCORDION) -->
            <!-- ========================================================================= -->
            <div class="bz-accordion-module" data-section="manufacturing" data-keywords="manufacturing work orders bom formula cgmp production raw materials تصنيع أوامر إنتاج تشغيل مواد خام">
                <div class="bz-accordion-header p-5 sm:p-6 flex items-center justify-between gap-4 border-b border-slate-200 dark:border-[#1e3a52]" onclick="toggleModuleAccordion('module-manufacturing')">
                    <div class="flex items-center gap-4">
                        <span class="w-12 h-12 rounded-2xl bg-gradient-to-br from-purple-600 to-indigo-800 text-white flex items-center justify-center text-xl shadow-md shadow-purple-600/25 flex-shrink-0">
                            <i class="fa-solid fa-industry"></i>
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="badge text-[10px] font-black uppercase tracking-wider bg-purple-100 dark:bg-purple-500/20 text-purple-800 dark:text-purple-300 border border-purple-300 dark:border-purple-500/30 px-2 py-0.5 rounded-full">cGMP MANUFACTURING</span>
                                <h2 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white">
                                    {{ app()->getLocale() === 'ar' ? 'التصنيع الدوائي، أوامر الإنتاج ومعادلات التركيب BOM' : 'Pharmaceutical Manufacturing, Work Orders & BOM Recipes' }}
                                </h2>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5">
                                {{ app()->getLocale() === 'ar' ? '3 شاشات رئيسية: مركز قيادة أوامر التشغيل، معادلات التركيب وصرف المواد، وسجل التشغيلة الدوائي والتفتيش BMR' : '3 Sub-Screens: Work Orders Command, BOM Dispensing Protocol & Batch Manufacturing Records' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-slate-100 dark:bg-[#061a2b] text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-[#1e3a52]">3 Screens</span>
                        <i id="module-manufacturing-chevron" class="bz-accordion-chevron fa-solid fa-chevron-down text-slate-400 text-sm"></i>
                    </div>
                </div>

                <div id="module-manufacturing-body" class="bz-accordion-body p-6 sm:p-8 space-y-6" style="display: none;">
                    <div class="bz-subscreen-nav flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none border-b border-slate-200 dark:border-[#1e3a52]">
                        <button type="button" class="bz-subscreen-pill active" onclick="switchSubScreen('module-manufacturing', 'mfg-screen-work-orders', this)">
                            <i class="fa-solid fa-gears text-purple-500"></i>
                            <span>Work Orders (مركز أوامر التشغيل والإنتاج)</span>
                        </button>
                        <button type="button" class="bz-subscreen-pill" onclick="switchSubScreen('module-manufacturing', 'mfg-screen-dispensing', this)">
                            <i class="fa-solid fa-flask-vial text-sky-500"></i>
                            <span>BOM Dispensing (معادلات التركيب وصرف المواد)</span>
                        </button>
                        <button type="button" class="bz-subscreen-pill" onclick="switchSubScreen('module-manufacturing', 'mfg-screen-bmr', this)">
                            <i class="fa-solid fa-clipboard-check text-emerald-500"></i>
                            <span>BMR & Cleanroom (سجل التشغيلة والتفتيش الدوائي)</span>
                        </button>
                    </div>

                    <!-- SUB-SCREEN 1: WORK ORDERS -->
                    <div id="mfg-screen-work-orders" class="bz-subscreen-view space-y-6">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50 dark:bg-[#061a2b] p-4 rounded-2xl border border-slate-200 dark:border-[#1e3a52]">
                            <div>
                                <h3 class="font-black text-slate-900 dark:text-white text-base">Work Orders Command — مركز قيادة أوامر التشغيل والإنتاج الدوائي cGMP</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Route: <span class="font-mono text-purple-600 dark:text-purple-400">/admin/work-orders</span> | Controller: <span class="font-mono">WorkOrderController@index</span></p>
                            </div>
                            <a href="{{ route('admin.work-orders.index') }}" target="_blank" class="btn btn-secondary text-xs self-start sm:self-center">
                                <i class="fa-solid fa-gears text-xs"></i>
                                <span>أوامر التشغيل</span>
                            </a>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-purple-600 dark:text-purple-400">PHASE 01</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">توليد أمر التشغيل وجدولته</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">تحديد المستحضر المستهدف، حجم الوجبة (Batch Size)، وخط الإنتاج وتاريخ البدء المعتمد.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-sky-600 dark:text-sky-400">PHASE 02</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">حجز المواد الخام آلياً</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">حجز الكميات الدقيقة من المواد الفعالة والمضافة برمجياً من المستودع ومنع صرفها لأي أمر آخر.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-amber-600 dark:text-amber-400">PHASE 03</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">بدء الصرف والتشغيل</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">تفعيل حالة Start Production ونقل المواد لمنطقة الوزن والخلط تحت رقابة الجودة.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-emerald-600 dark:text-emerald-400">PHASE 04</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">إيداع المنتج وحساب العائد Yield</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">حساب نسبة الإنتاجية الفعلية مقابل النظرية، ترحيل تكلفة الوجبة، وإيداع المنتج التام بالمخزون.</p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5"><i class="fa-solid fa-camera text-purple-500"></i> المعاينة الحقيقية لأوامر التشغيل والإنتاج:</span>
                            <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot14_manufacturing_work_orders.png') }}', 'أوامر التشغيل والإنتاج الدوائي cGMP', 'إدارة خطوط الإنتاج، بدء صرف المواد (Dispensing Protocol)، المراجعة الدوائية، وإيداع المنتج التام.', 14)">
                                <img src="{{ asset('images/guide/shot14_manufacturing_work_orders.png') }}" alt="Work Orders Index" class="w-full h-64 object-cover object-top" loading="lazy">
                                <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-3xl"></i></div>
                                <div class="p-3 bg-slate-50 dark:bg-[#061a2b] text-xs font-bold text-slate-800 dark:text-slate-200 flex justify-between">
                                    <span>لوحة إدارة أوامر التشغيل وخطوط الإنتاج (Work Orders)</span>
                                    <span class="text-purple-600 dark:text-purple-400 font-mono">Live High-Res Preview</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SUB-SCREEN 2: BOM & DISPENSING -->
                    <div id="mfg-screen-dispensing" class="bz-subscreen-view space-y-6" style="display: none;">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50 dark:bg-[#061a2b] p-4 rounded-2xl border border-slate-200 dark:border-[#1e3a52]">
                            <div>
                                <h3 class="font-black text-slate-900 dark:text-white text-base">BOM Dispensing — بروتوكول صرف المواد الخام والوزن المزدوج لمعادلات التركيب</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Route: <span class="font-mono text-sky-600 dark:text-sky-400">/admin/work-orders/{id}</span> | Action: <span class="font-mono">startDispensing</span></p>
                            </div>
                            <span class="badge text-xs bg-sky-100 dark:bg-sky-500/20 text-sky-700 dark:text-sky-300 font-bold px-3 py-1 rounded-full">Dual-Sign Verified</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-sky-600 dark:text-sky-400">PHASE 01</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">استدعاء معادلة التركيب BOM</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">تحميل قائمة المواد الخام الفعالة (APIs) والمواد المساعدة بدقة الملغرام بناءً على حجم التشغيلة.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-amber-600 dark:text-amber-400">PHASE 02</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">التحقق من رقم التشغيلة Lot</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">مسح باركود براميل المواد الخام للتأكد من أنها نفس التشغيلات المفحوصة والمج اجازتها بمستودع الحجر.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-purple-600 dark:text-purple-400">PHASE 03</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">الوزن المزدوج والتوثيق</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">توقيع الصيدلي القائم بالوزن وتوقيع مراقب الجودة المرافق للتحقق من دقة الموازين الإلكترونية.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-emerald-600 dark:text-emerald-400">PHASE 04</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">إخلاء الخط Line Clearance</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">التأكد من خلو منطقة الخلط من أي بقايا لمنتجات سابقة وإصدار تصريح بدء التشغيل.</p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5"><i class="fa-solid fa-camera text-sky-500"></i> المعاينة الحقيقية لبروتوكول صرف المواد BOM:</span>
                            <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot14_manufacturing_work_orders.png') }}', 'بروتوكول صرف المواد الخام والوزن الدوائي', 'تأكيد أوزان المواد الفعالة، مسح باركود التشغيلات، وتوقيع الإخلاء والبدء.', 14)">
                                <img src="{{ asset('images/guide/shot14_manufacturing_work_orders.png') }}" alt="Dispensing Protocol" class="w-full h-64 object-cover object-top" loading="lazy">
                                <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-3xl"></i></div>
                                <div class="p-3 bg-slate-50 dark:bg-[#061a2b] text-xs font-bold text-slate-800 dark:text-slate-200 flex justify-between">
                                    <span>بروتوكول صرف المواد الخام والوزن المزدوج (BOM Dispensing)</span>
                                    <span class="text-sky-600 dark:text-sky-400 font-mono">Live High-Res Preview</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SUB-SCREEN 3: BMR & CLEANROOM -->
                    <div id="mfg-screen-bmr" class="bz-subscreen-view space-y-6" style="display: none;">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50 dark:bg-[#061a2b] p-4 rounded-2xl border border-slate-200 dark:border-[#1e3a52]">
                            <div>
                                <h3 class="font-black text-slate-900 dark:text-white text-base">BMR & Cleanroom QC — سجل التشغيلة الإلكتروني، التفتيش الدوائي والاعتماد النهائي QP</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Route: <span class="font-mono text-emerald-600 dark:text-emerald-400">/admin/work-orders/{id}</span> | Action: <span class="font-mono">qcReview</span></p>
                            </div>
                            <span class="badge text-xs bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 font-bold px-3 py-1 rounded-full">cGMP QP Sign-off</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-purple-600 dark:text-purple-400">PHASE 01</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">مراقبة بيئة الغرفة النظيفة</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">فحص فوارق الضغط، عدد الجزيئات بالهواء (Class B/C)، ودرجات الحرارة والرطوبة النسبية.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-sky-600 dark:text-sky-400">PHASE 02</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">فحص الجودة أثناء التشغيل</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">أخذ عينات دورية كل نصف ساعة لفحص تفاوت الأوزان، الصلابة، وزمن الذوبان والتفتت.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-amber-600 dark:text-amber-400">PHASE 03</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">التعبئة والأختام الآمنة</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">طباعة رقم التشغيلة والباركود وتثبيت ملصقات الأمان المانعة للعبث على العبوات.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-emerald-600 dark:text-emerald-400">PHASE 04</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">توقيع مسؤول الإفراج الدوائي QP</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">مراجعة ملف BMR بالكامل وتوقيع الإفراج النهائي لإتاحة المستحضر للبيع والشحن.</p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5"><i class="fa-solid fa-camera text-emerald-500"></i> المعاينة الحقيقية لسجل التشغيلة BMR والتفتيش:</span>
                            <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot14_manufacturing_work_orders.png') }}', 'سجل التشغيلة والتفتيش الدوائي cGMP BMR', 'سجل الفحوصات المرحلية، التوثيق الدوائي، وتوقيع مسؤول الإفراج النهائي QP.', 14)">
                                <img src="{{ asset('images/guide/shot14_manufacturing_work_orders.png') }}" alt="BMR Review" class="w-full h-64 object-cover object-top" loading="lazy">
                                <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-3xl"></i></div>
                                <div class="p-3 bg-slate-50 dark:bg-[#061a2b] text-xs font-bold text-slate-800 dark:text-slate-200 flex justify-between">
                                    <span>سجل التشغيلة الدوائي الإلكتروني (e-BMR Inspection)</span>
                                    <span class="text-emerald-600 dark:text-emerald-400 font-mono">Live High-Res Preview</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================================================= -->
            <!-- MODULE 7: SALES ORDERS & FULFILLMENT (ACCORDION) -->
            <!-- ========================================================================= -->
            <div class="bz-accordion-module" data-section="sales" data-keywords="sales orders pos checkout shipping waybill fulfillment مبيعات طلبات دفع فواتير شحن نقطة بيع">
                <div class="bz-accordion-header p-5 sm:p-6 flex items-center justify-between gap-4 border-b border-slate-200 dark:border-[#1e3a52]" onclick="toggleModuleAccordion('module-sales')">
                    <div class="flex items-center gap-4">
                        <span class="w-12 h-12 rounded-2xl bg-gradient-to-br from-rose-500 to-red-700 text-white flex items-center justify-center text-xl shadow-md shadow-rose-500/25 flex-shrink-0">
                            <i class="fa-solid fa-truck-fast"></i>
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="badge text-[10px] font-black uppercase tracking-wider bg-rose-100 dark:bg-rose-500/20 text-rose-800 dark:text-rose-300 border border-rose-300 dark:border-rose-500/30 px-2 py-0.5 rounded-full">FULFILLMENT & POS</span>
                                <h2 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white">
                                    {{ app()->getLocale() === 'ar' ? 'إدارة الطلبات، الفوترة الضريبية وبوالص الشحن المبردة' : 'Order Management, Zakat Invoicing & Cold Shipping' }}
                                </h2>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5">
                                {{ app()->getLocale() === 'ar' ? '3 شاشات رئيسية: مصفوفة حالات الطلبات، الفوترة الإلكترونية المشفرة Zakat Phase 2، وتجهيز الشحن وبوالص AWB' : '3 Sub-Screens: Storefront Orders Matrix, Zakat Phase 2 QR Invoicing & Cold Courier Waybills' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-slate-100 dark:bg-[#061a2b] text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-[#1e3a52]">3 Screens</span>
                        <i id="module-sales-chevron" class="bz-accordion-chevron fa-solid fa-chevron-down text-slate-400 text-sm"></i>
                    </div>
                </div>

                <div id="module-sales-body" class="bz-accordion-body p-6 sm:p-8 space-y-6" style="display: none;">
                    <div class="bz-subscreen-nav flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none border-b border-slate-200 dark:border-[#1e3a52]">
                        <button type="button" class="bz-subscreen-pill active" onclick="switchSubScreen('module-sales', 'sales-screen-orders', this)">
                            <i class="fa-solid fa-list-check text-rose-500"></i>
                            <span>Orders Matrix (مصفوفة إدارة الطلبات والمراحل)</span>
                        </button>
                        <button type="button" class="bz-subscreen-pill" onclick="switchSubScreen('module-sales', 'sales-screen-zakat', this)">
                            <i class="fa-solid fa-qrcode text-emerald-500"></i>
                            <span>Zakat Phase 2 QR (الفوترة الإلكترونية المشفرة)</span>
                        </button>
                        <button type="button" class="bz-subscreen-pill" onclick="switchSubScreen('module-sales', 'sales-screen-fulfillment', this)">
                            <i class="fa-solid fa-box-open text-sky-500"></i>
                            <span>Picking & Waybills (التجهيز والتعبئة وبوالص الشحن)</span>
                        </button>
                    </div>

                    <!-- SUB-SCREEN 1: ORDERS MATRIX -->
                    <div id="sales-screen-orders" class="bz-subscreen-view space-y-6">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50 dark:bg-[#061a2b] p-4 rounded-2xl border border-slate-200 dark:border-[#1e3a52]">
                            <div>
                                <h3 class="font-black text-slate-900 dark:text-white text-base">Orders Matrix — مركز إدارة الطلبات والمصفوفة المرحلية للطلبات الإلكترونية</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Route: <span class="font-mono text-rose-600 dark:text-rose-400">/admin/orders</span> | Controller: <span class="font-mono">OrderController@index</span></p>
                            </div>
                            <a href="{{ route('admin.orders.index') }}" target="_blank" class="btn btn-secondary text-xs self-start sm:self-center">
                                <i class="fa-solid fa-bag-shopping text-xs"></i>
                                <span>جدول الطلبات</span>
                            </a>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-rose-600 dark:text-rose-400">PHASE 01</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">استقبال الطلب وفحص الدفع</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">التقاط الطلب من متجر Bluezone والتأكد من إشعار بوابة الدفع (مدى، فيزا، تابي، تمارا).</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-sky-600 dark:text-sky-400">PHASE 02</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">الحجز وتوليد الفاتورة</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">حجز المخزون المخصص تلقائياً وإصدار الفاتورة الضريبية وقيد الاستحقاق المالي.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-amber-600 dark:text-amber-400">PHASE 03</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">التجهيز والتعبئة المبردة</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">تحويل الطلب لحالة Processing وإصدار أمر الالتقاط للمستودع وإرفاق أكياس الجل المبرد.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-emerald-600 dark:text-emerald-400">PHASE 04</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">الشحن والتسليم التام</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">تسليم المندوب أو شركة الشحن وتحديث الحالة إلى Shipped ثم Delivered وتنبيه العميل بـ SMS.</p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5"><i class="fa-solid fa-camera text-rose-500"></i> المعاينة الحقيقية لمصفوفة الطلبات:</span>
                            <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot15_sales_orders.png') }}', 'مركز إدارة الطلبات وبوالص الشحن', 'جدول الطلبات، متابعة الحالات (جديد، قيد الشحن، تم التسليم)، والتحقق من سندات الفواتير الضريبية.', 15)">
                                <img src="{{ asset('images/guide/shot15_sales_orders.png') }}" alt="Sales Orders" class="w-full h-64 object-cover object-top" loading="lazy">
                                <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-3xl"></i></div>
                                <div class="p-3 bg-slate-50 dark:bg-[#061a2b] text-xs font-bold text-slate-800 dark:text-slate-200 flex justify-between">
                                    <span>مصفوفة ومتابعة حالات طلبات المتجر (Sales Orders)</span>
                                    <span class="text-rose-600 dark:text-rose-400 font-mono">Live High-Res Preview</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SUB-SCREEN 2: ZAKAT PHASE 2 QR -->
                    <div id="sales-screen-zakat" class="bz-subscreen-view space-y-6" style="display: none;">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50 dark:bg-[#061a2b] p-4 rounded-2xl border border-slate-200 dark:border-[#1e3a52]">
                            <div>
                                <h3 class="font-black text-slate-900 dark:text-white text-base">Zakat Phase 2 QR — الفوترة الإلكترونية الضريبية المشفرة المعتمدة لدى هيئة الزكاة والضريبة والجمارك (ZATCA)</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Route: <span class="font-mono text-emerald-600 dark:text-emerald-400">/admin/orders/{id}/invoice</span> | Standard: <span class="font-mono">ZATCA TLV Base64 Encoding</span></p>
                            </div>
                            <span class="badge text-xs bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 font-bold px-3 py-1 rounded-full">ZATCA Compliant</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-emerald-600 dark:text-emerald-400">PHASE 01</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">تفكيك بنود الفاتورة والضريبة</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">فصل القيمة الصافية عن ضريبة الـ VAT بنسبة 15% وتطبيق الخصومات الترويجية بدقة سنتيمترية.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-sky-600 dark:text-sky-400">PHASE 02</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">توليد الـ Hash والبصمة المشفرة</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">إنشاء الـ Cryptographic Stamp وربط الفاتورة بالسلسلة غير القابلة للتلاعب (Invoice Hash Chain).</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-purple-600 dark:text-purple-400">PHASE 03</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">توليد باركود TLV Base64 QR</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">تشفير اسم المنشأة، الرقم الضريبي، التوقيت، الإجمالي، والضريبة في كود QR مطابق لتطبيق زاتكا.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-teal-600 dark:text-teal-400">PHASE 04</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">الأرشفة وإرسال الـ PDF</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">تصدير الفاتورة الرسمية PDF/A-3 متوافقة مع المتطلبات، وحفظ نسخة مشفرة بأرشيف النظام.</p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5"><i class="fa-solid fa-camera text-emerald-500"></i> المعاينة الحقيقية للفواتير والباركود الضريبي:</span>
                            <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot15_sales_orders.png') }}', 'الفاتورة الضريبية المشفرة Zakat Phase 2 QR', 'توليد الفاتورة الضريبية المعتمدة، رمز الاستجابة السريعة TLV، والأرشفة المحاسبية.', 15)">
                                <img src="{{ asset('images/guide/shot15_sales_orders.png') }}" alt="Zakat Invoice" class="w-full h-64 object-cover object-top" loading="lazy">
                                <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-3xl"></i></div>
                                <div class="p-3 bg-slate-50 dark:bg-[#061a2b] text-xs font-bold text-slate-800 dark:text-slate-200 flex justify-between">
                                    <span>نظام الفوترة الإلكترونية وباركود الزكاة (Zakat Invoicing)</span>
                                    <span class="text-emerald-600 dark:text-emerald-400 font-mono">Live High-Res Preview</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SUB-SCREEN 3: PICKING & WAYBILLS -->
                    <div id="sales-screen-fulfillment" class="bz-subscreen-view space-y-6" style="display: none;">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50 dark:bg-[#061a2b] p-4 rounded-2xl border border-slate-200 dark:border-[#1e3a52]">
                            <div>
                                <h3 class="font-black text-slate-900 dark:text-white text-base">Picking & Waybills — تجهيز الطرود، التغليف العازل وبوالص الشحن السريع AWB</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Route: <span class="font-mono text-sky-600 dark:text-sky-400">/admin/orders</span> | Integration: <span class="font-mono">ColdCourierWaybillAPI</span></p>
                            </div>
                            <span class="badge text-xs bg-sky-100 dark:bg-sky-500/20 text-sky-700 dark:text-sky-300 font-bold px-3 py-1 rounded-full">Cold AWB Ready</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-sky-600 dark:text-sky-400">PHASE 01</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">توليد قائمة الالتقاط Pick List</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">تجميع الأصناف حسب مواقع الرفوف بالمستودع لتقليص زمن حركة عمال المستودع إلى الحد الأدنى.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-amber-600 dark:text-amber-400">PHASE 02</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">التحقق بالباركود والتغليف</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">مسح باركود كل صنف للتأكد من مطابقة الطلب بنسبة 100% والتغليف في حافظات حرارية مبردة.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-teal-600 dark:text-teal-400">PHASE 03</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">طباعة بوليصة الشحن AWB</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">استدعاء بوابة الشحن وتوليد ملصق الشحن الحراري 4x6 متضمناً باركود التتبع وعنوان العميل.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-emerald-600 dark:text-emerald-400">PHASE 04</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">المناولة والتتبع اللحظي</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">تسليم الطرد لسيارة النقل المبردة وتفعيل رابط التتبع المباشر للعميل حتى باب المنزل.</p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5"><i class="fa-solid fa-camera text-sky-500"></i> المعاينة الحقيقية لبوالص الشحن والتجهيز:</span>
                            <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot15_sales_orders.png') }}', 'بوالص الشحن السريع وتجهيز الطرود المبردة', 'توليد بوالص الشحن AWB، متابعة أرقام التتبع، وإدارة تسليم الطرود المبردة للعملاء.', 15)">
                                <img src="{{ asset('images/guide/shot15_sales_orders.png') }}" alt="Waybills & Picking" class="w-full h-64 object-cover object-top" loading="lazy">
                                <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-3xl"></i></div>
                                <div class="p-3 bg-slate-50 dark:bg-[#061a2b] text-xs font-bold text-slate-800 dark:text-slate-200 flex justify-between">
                                    <span>نظام تجهيز الطرود وبوالص الشحن السريع (Courier Fulfillment)</span>
                                    <span class="text-sky-600 dark:text-sky-400 font-mono">Live High-Res Preview</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================================================= -->
            <!-- MODULE 8: FIELD MEDICAL REP CRM (ACCORDION) -->
            <!-- ========================================================================= -->
            <div class="bz-accordion-module" data-section="field-crm" data-keywords="field crm mr medical rep doctors clinics visits samples مندوبين زيارات عيادات أطباء عينات خطوط سير">
                <div class="bz-accordion-header p-5 sm:p-6 flex items-center justify-between gap-4 border-b border-slate-200 dark:border-[#1e3a52]" onclick="toggleModuleAccordion('module-field-crm')">
                    <div class="flex items-center gap-4">
                        <span class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-800 text-white flex items-center justify-center text-xl shadow-md shadow-blue-600/25 flex-shrink-0">
                            <i class="fa-solid fa-user-doctor"></i>
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="badge text-[10px] font-black uppercase tracking-wider bg-blue-100 dark:bg-blue-500/20 text-blue-800 dark:text-blue-300 border border-blue-300 dark:border-blue-500/30 px-2 py-0.5 rounded-full">FIELD MEDICAL REP</span>
                                <h2 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white">
                                    {{ app()->getLocale() === 'ar' ? 'المندوبون، الزيارات الميدانية والتحقق الجغرافي GPS' : 'Field Medical Reps, Clinic Visits & GPS Geofencing' }}
                                </h2>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5">
                                {{ app()->getLocale() === 'ar' ? '3 شاشات رئيسية: سجل الزيارات والتحقق الجغرافي Geofence، دليل الأطباء والمراكز، وتوزيع العينات الطبية وعائد الوصفات' : '3 Sub-Screens: GPS Field Visits, Doctor & Clinic Directory & Clinical Sample Drops' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-slate-100 dark:bg-[#061a2b] text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-[#1e3a52]">3 Screens</span>
                        <i id="module-field-crm-chevron" class="bz-accordion-chevron fa-solid fa-chevron-down text-slate-400 text-sm"></i>
                    </div>
                </div>

                <div id="module-field-crm-body" class="bz-accordion-body p-6 sm:p-8 space-y-6" style="display: none;">
                    <div class="bz-subscreen-nav flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none border-b border-slate-200 dark:border-[#1e3a52]">
                        <button type="button" class="bz-subscreen-pill active" onclick="switchSubScreen('module-field-crm', 'mr-screen-visits', this)">
                            <i class="fa-solid fa-location-dot text-blue-500"></i>
                            <span>Field Visits & GPS (سجل الزيارات والتحقق الجغرافي)</span>
                        </button>
                        <button type="button" class="bz-subscreen-pill" onclick="switchSubScreen('module-field-crm', 'mr-screen-doctors', this)">
                            <i class="fa-solid fa-hospital-user text-indigo-500"></i>
                            <span>Doctors Directory (سجل الأطباء والعيادات KOL)</span>
                        </button>
                        <button type="button" class="bz-subscreen-pill" onclick="switchSubScreen('module-field-crm', 'mr-screen-samples', this)">
                            <i class="fa-solid fa-capsules text-teal-500"></i>
                            <span>Clinical Samples (توزيع العينات الطبية والوصفات)</span>
                        </button>
                    </div>

                    <!-- SUB-SCREEN 1: FIELD VISITS & GPS -->
                    <div id="mr-screen-visits" class="bz-subscreen-view space-y-6">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50 dark:bg-[#061a2b] p-4 rounded-2xl border border-slate-200 dark:border-[#1e3a52]">
                            <div>
                                <h3 class="font-black text-slate-900 dark:text-white text-base">Field Visits & GPS — سجل الزيارات الميدانية والتحقق الجغرافي Geofencing للأطباء</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Route: <span class="font-mono text-blue-600 dark:text-blue-400">/admin/visits</span> | Controller: <span class="font-mono">VisitController@index</span></p>
                            </div>
                            <a href="{{ route('admin.visits.index') }}" target="_blank" class="btn btn-secondary text-xs self-start sm:self-center">
                                <i class="fa-solid fa-location-dot text-xs"></i>
                                <span>سجل الزيارات الميدانية</span>
                            </a>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-blue-600 dark:text-blue-400">PHASE 01</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">تخطيط خط السير الشهري</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">توزيع الأطباء المستهدفين على الأيام والمناطق الجغرافية بحسب تصنيف الأهمية (A/B/C).</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-cyan-600 dark:text-cyan-400">PHASE 02</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">التحقق الجغرافي GPS Geofence</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">إلزام المندوب بتسجيل الحضور داخل نطاق 50 متراً من موقع عيادة الطبيب لتوثيق المصداقية.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-amber-600 dark:text-amber-400">PHASE 03</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">العرض العلمي وتوثيق الملاحظات</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">شرح الميزات السريرية لتركيبات Bluezone وتوثيق استجابة الطبيب والمنتجات المنافسة.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-emerald-600 dark:text-emerald-400">PHASE 04</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">تحديث مؤشرات التغطية KPI</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">حساب نسبة التغطية الشهرية الفعلية مقابل المخطط ورفع التقرير لمدير المنطقة تلقائياً.</p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5"><i class="fa-solid fa-camera text-blue-500"></i> المعاينة الحقيقية للزيارات والتحقق الجغرافي:</span>
                            <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot17_field_mr_visits.png') }}', 'إدارة الزيارات الميدانية والتحقق GPS', 'سجل الزيارات الطبية، التحقق الجغرافي GPS، توثيق العينات الطبية وتتبع التغطية الشهرية للمناطق.', 17)">
                                <img src="{{ asset('images/guide/shot17_field_mr_visits.png') }}" alt="Field Visits" class="w-full h-64 object-cover object-top" loading="lazy">
                                <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-3xl"></i></div>
                                <div class="p-3 bg-slate-50 dark:bg-[#061a2b] text-xs font-bold text-slate-800 dark:text-slate-200 flex justify-between">
                                    <span>سجل الزيارات الميدانية والتحقق الجغرافي (MR Visits GPS)</span>
                                    <span class="text-blue-600 dark:text-blue-400 font-mono">Live High-Res Preview</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SUB-SCREEN 2: DOCTORS DIRECTORY -->
                    <div id="mr-screen-doctors" class="bz-subscreen-view space-y-6" style="display: none;">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50 dark:bg-[#061a2b] p-4 rounded-2xl border border-slate-200 dark:border-[#1e3a52]">
                            <div>
                                <h3 class="font-black text-slate-900 dark:text-white text-base">Doctors Directory — دليل الأطباء، العيادات وتصنيف قادة الرأي Key Opinion Leaders (KOL)</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Route: <span class="font-mono text-indigo-600 dark:text-indigo-400">/admin/contacts</span> | Controller: <span class="font-mono">ContactController@index</span></p>
                            </div>
                            <a href="{{ route('admin.contacts.index') }}" target="_blank" class="btn btn-secondary text-xs self-start sm:self-center">
                                <i class="fa-solid fa-address-book text-xs"></i>
                                <span>دليل الأطباء والجهات</span>
                            </a>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-indigo-600 dark:text-indigo-400">PHASE 01</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">توصيف وتصنيف الطبيب</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">توثيق التخصص الدقيق، المستشفى أو المركز التابع له، وساعات العمل وأيام الاستقبال.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-sky-600 dark:text-sky-400">PHASE 02</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">تحديد القوة الإكلينيكية</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">تقييم عدد المرضى أسبوعياً، القدرة على كتابة الوصفات، وتصنيف الطبيب (Tier A/B/C).</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-amber-600 dark:text-amber-400">PHASE 03</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">ربط التخصص بالتركيبات</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">تحديد مكملات Bluezone التي تتطابق علمياً مع حالات المرضى المعالجة لدى الطبيب.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-emerald-600 dark:text-emerald-400">PHASE 04</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">سجل التفاعلات التاريخي</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">أرشفة كافة الزيارات السابقة، العينات المسلمة، والملاحظات السريرية في بروفايل موحد.</p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5"><i class="fa-solid fa-camera text-indigo-500"></i> المعاينة الحقيقية لدليل الأطباء والعيادات:</span>
                            <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot17_field_mr_visits.png') }}', 'دليل الأطباء والمراكز وتصنيف KOL', 'قاعدة بيانات الأطباء، تصنيفات التخصصات، وإدارة علاقات قادة الرأي الطبي.', 17)">
                                <img src="{{ asset('images/guide/shot17_field_mr_visits.png') }}" alt="Doctors Directory" class="w-full h-64 object-cover object-top" loading="lazy">
                                <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-3xl"></i></div>
                                <div class="p-3 bg-slate-50 dark:bg-[#061a2b] text-xs font-bold text-slate-800 dark:text-slate-200 flex justify-between">
                                    <span>دليل الأطباء والمراكز الطبية (KOL Directory)</span>
                                    <span class="text-indigo-600 dark:text-indigo-400 font-mono">Live High-Res Preview</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SUB-SCREEN 3: CLINICAL SAMPLES -->
                    <div id="mr-screen-samples" class="bz-subscreen-view space-y-6" style="display: none;">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50 dark:bg-[#061a2b] p-4 rounded-2xl border border-slate-200 dark:border-[#1e3a52]">
                            <div>
                                <h3 class="font-black text-slate-900 dark:text-white text-base">Clinical Samples — توزيع العينات الطبية التجريبية وتتبع عائد الوصفات الصيدلانية</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Route: <span class="font-mono text-teal-600 dark:text-teal-400">/admin/visits</span> | Component: <span class="font-mono">SampleDispensingLog</span></p>
                            </div>
                            <span class="badge text-xs bg-teal-100 dark:bg-teal-500/20 text-teal-700 dark:text-teal-300 font-bold px-3 py-1 rounded-full">Sample Custody Tracked</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-teal-600 dark:text-teal-400">PHASE 01</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">تسليم عهدة العينات للمندوب</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">صرف عينات تشغيلات مخصصة بملصق (Medical Sample - Not for Sale) وربطها بعهدة المندوب.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-sky-600 dark:text-sky-400">PHASE 02</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">التسليم والتوقيع بالعيادة</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">تسليم العينات للطبيب أثناء الزيارة الميدانية وتوثيق الكميات المسلمة ورقم التشغيلة إلكترونياً.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-amber-600 dark:text-amber-400">PHASE 03</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">متابعة الأثر السريري</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">استطلاع رأي الطبيب في الزيارة التالية حول فعالية التركيبة واستجابة المرضى للعلاج التجريبي.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-emerald-600 dark:text-emerald-400">PHASE 04</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">قياس عائد التحويل ROI</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">مقارنة العينات المصروفة بمعدل سحب الصيدليات المجاورة لعيادة الطبيب لقياس العائد التجاري.</p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5"><i class="fa-solid fa-camera text-teal-500"></i> المعاينة الحقيقية لسجل العينات الطبية:</span>
                            <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot17_field_mr_visits.png') }}', 'توزيع العينات الطبية وعائد الوصفات', 'إدارة عهد العينات، توثيق التسليم للأطباء، ومتابعة نمو الوصفات الطبية بالصيدليات.', 17)">
                                <img src="{{ asset('images/guide/shot17_field_mr_visits.png') }}" alt="Medical Samples" class="w-full h-64 object-cover object-top" loading="lazy">
                                <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-3xl"></i></div>
                                <div class="p-3 bg-slate-50 dark:bg-[#061a2b] text-xs font-bold text-slate-800 dark:text-slate-200 flex justify-between">
                                    <span>سجل عهد وتوزيع العينات الطبية (Clinical Samples)</span>
                                    <span class="text-teal-600 dark:text-teal-400 font-mono">Live High-Res Preview</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================================================= -->
            <!-- MODULE 9: SALES CRM & DEALS PIPELINE (ACCORDION) -->
            <!-- ========================================================================= -->
            <div class="bz-accordion-module" data-section="sales-crm" data-keywords="sales crm leads pipeline deals b2b opportunities عملاء محتملين صفقات عروض أسعار تفاوض">
                <div class="bz-accordion-header p-5 sm:p-6 flex items-center justify-between gap-4 border-b border-slate-200 dark:border-[#1e3a52]" onclick="toggleModuleAccordion('module-sales-crm')">
                    <div class="flex items-center gap-4">
                        <span class="w-12 h-12 rounded-2xl bg-gradient-to-br from-orange-500 to-amber-700 text-white flex items-center justify-center text-xl shadow-md shadow-orange-500/25 flex-shrink-0">
                            <i class="fa-solid fa-handshake"></i>
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="badge text-[10px] font-black uppercase tracking-wider bg-orange-100 dark:bg-orange-500/20 text-orange-800 dark:text-orange-300 border border-orange-300 dark:border-orange-500/30 px-2 py-0.5 rounded-full">B2B DEALS CRM</span>
                                <h2 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white">
                                    {{ app()->getLocale() === 'ar' ? 'علاقات العملاء، مسار الصفقات Kanban والعقود المؤسسية' : 'Sales Deals CRM, Pipeline Kanban & Institutional B2B' }}
                                </h2>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5">
                                {{ app()->getLocale() === 'ar' ? '3 شاشات رئيسية: لوحة مسار الصفقات Kanban، تأهيل وتقييم العملاء B2B، والعقود المؤسسية والحدود الائتمانية' : '3 Sub-Screens: Kanban Deals Pipeline, Lead Qualification & B2B Institutional Contracts' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-slate-100 dark:bg-[#061a2b] text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-[#1e3a52]">3 Screens</span>
                        <i id="module-sales-crm-chevron" class="bz-accordion-chevron fa-solid fa-chevron-down text-slate-400 text-sm"></i>
                    </div>
                </div>

                <div id="module-sales-crm-body" class="bz-accordion-body p-6 sm:p-8 space-y-6" style="display: none;">
                    <div class="bz-subscreen-nav flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none border-b border-slate-200 dark:border-[#1e3a52]">
                        <button type="button" class="bz-subscreen-pill active" onclick="switchSubScreen('module-sales-crm', 'crm-screen-deals', this)">
                            <i class="fa-solid fa-table-columns text-orange-500"></i>
                            <span>Kanban Pipeline (مسار الصفقات ومراحل التفاوض)</span>
                        </button>
                        <button type="button" class="bz-subscreen-pill" onclick="switchSubScreen('module-sales-crm', 'crm-screen-leads', this)">
                            <i class="fa-solid fa-user-plus text-sky-500"></i>
                            <span>Lead Qualification (تأهيل وتقييم العملاء المحتملين)</span>
                        </button>
                        <button type="button" class="bz-subscreen-pill" onclick="switchSubScreen('module-sales-crm', 'crm-screen-contracts', this)">
                            <i class="fa-solid fa-file-contract text-emerald-500"></i>
                            <span>B2B Contracts (العقود المؤسسية والائتمان)</span>
                        </button>
                    </div>

                    <!-- SUB-SCREEN 1: KANBAN PIPELINE -->
                    <div id="crm-screen-deals" class="bz-subscreen-view space-y-6">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50 dark:bg-[#061a2b] p-4 rounded-2xl border border-slate-200 dark:border-[#1e3a52]">
                            <div>
                                <h3 class="font-black text-slate-900 dark:text-white text-base">Kanban Pipeline — لوحة قيادة مسار الصفقات والمراحل المرحلية للمؤسسات والمستشفيات</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Route: <span class="font-mono text-orange-600 dark:text-orange-400">/admin/crm</span> | Controller: <span class="font-mono">CrmController@index</span></p>
                            </div>
                            <a href="{{ route('admin.crm.dashboard') }}" target="_blank" class="btn btn-secondary text-xs self-start sm:self-center">
                                <i class="fa-solid fa-table-columns text-xs"></i>
                                <span>لوحة الصفقات Kanban</span>
                            </a>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-orange-600 dark:text-orange-400">PHASE 01</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">التقاط الفرصة وتحديد القيمة</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">إنشاء بطاقة الصفقة، إدخال القيمة المتوقعة بالريال، وتحديد المستشفى أو الجهة الطالبة.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-sky-600 dark:text-sky-400">PHASE 02</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">تقديم العرض الفني والمالي</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">نقل الصفقة لمرحلة Proposal وإرفاق عروض الأسعار والملفات الفنية للمنتجات المطلوبة.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-amber-600 dark:text-amber-400">PHASE 03</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">التفاوض وشروط السداد</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">مرحلة Negotiation لمناقشة الخصومات الكمية، فترات الائتمان، ومواعيد التوريد الدورية.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-emerald-600 dark:text-emerald-400">PHASE 04</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">الإغلاق الرابح Closed-Won</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">توقيع العقد الرسمي، تحويل الفرصة إلى أمر بيع فعلي، وتحديث تقرير الأرباح المحققة.</p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5"><i class="fa-solid fa-camera text-orange-500"></i> المعاينة الحقيقية لمسار الصفقات Kanban:</span>
                            <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot18_sales_crm_dashboard.png') }}', 'علاقات العملاء: لوحة قيادة مسار الصفقات CRM', 'متابعة مسار الصفقات Kanban، تأهيل العملاء المحتملين، وقيمة الصفقات المتوقعة والمحققة للمراكز.', 18)">
                                <img src="{{ asset('images/guide/shot18_sales_crm_dashboard.png') }}" alt="Deals Kanban" class="w-full h-64 object-cover object-top" loading="lazy">
                                <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-3xl"></i></div>
                                <div class="p-3 bg-slate-50 dark:bg-[#061a2b] text-xs font-bold text-slate-800 dark:text-slate-200 flex justify-between">
                                    <span>لوحة قيادة مسار الصفقات B2B (Kanban Deals Pipeline)</span>
                                    <span class="text-orange-600 dark:text-orange-400 font-mono">Live High-Res Preview</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SUB-SCREEN 2: LEAD QUALIFICATION -->
                    <div id="crm-screen-leads" class="bz-subscreen-view space-y-6" style="display: none;">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50 dark:bg-[#061a2b] p-4 rounded-2xl border border-slate-200 dark:border-[#1e3a52]">
                            <div>
                                <h3 class="font-black text-slate-900 dark:text-white text-base">Lead Qualification — تأهيل وتقييم العملاء المحتملين وفرز الطلبات المؤسسية</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Route: <span class="font-mono text-sky-600 dark:text-sky-400">/admin/crm/leads</span> | Controller: <span class="font-mono">CrmLeadController@index</span></p>
                            </div>
                            <a href="{{ route('admin.crm.leads.index') }}" target="_blank" class="btn btn-secondary text-xs self-start sm:self-center">
                                <i class="fa-solid fa-user-plus text-xs"></i>
                                <span>سجل العملاء المحتملين</span>
                            </a>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-sky-600 dark:text-sky-400">PHASE 01</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">جمع وتسجيل العميل</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">استيراد بيانات العميل من المعارض الطبية، الاتصالات الواردة، أو طلبات الشراء عبر الموقع.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-amber-600 dark:text-amber-400">PHASE 02</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">تطبيق معايير BANT</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">فحص الميزانية (Budget)، صاحب القرار (Authority)، الحاجة الطبية (Need)، والجدول الزمني (Timeline).</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-purple-600 dark:text-purple-400">PHASE 03</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">التقييم الرقمي Lead Scoring</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">احتساب درجة الجدية (Score من 1 إلى 100) وتحديد ما إذا كان العميل ساخناً (Hot Lead) أو بارداً.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-emerald-600 dark:text-emerald-400">PHASE 04</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">التحويل لصفقة وتعيين المسؤول</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">تحويل العميل المؤهل إلى صفقة Kanban وتعيين مدير حسابات متخصص لمتابعة الإغلاق.</p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5"><i class="fa-solid fa-camera text-sky-500"></i> المعاينة الحقيقية لتأهيل العملاء المحتملين:</span>
                            <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot18_sales_crm_dashboard.png') }}', 'تأهيل وتقييم العملاء المحتملين B2B Leads', 'فرز العملاء، تقييم الجدية، وتوجيه الفرص المؤهلة لمديري الحسابات.', 18)">
                                <img src="{{ asset('images/guide/shot18_sales_crm_dashboard.png') }}" alt="Leads Qualification" class="w-full h-64 object-cover object-top" loading="lazy">
                                <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-3xl"></i></div>
                                <div class="p-3 bg-slate-50 dark:bg-[#061a2b] text-xs font-bold text-slate-800 dark:text-slate-200 flex justify-between">
                                    <span>نظام تأهيل وتقييم العملاء المؤسسيين (Lead Qualification)</span>
                                    <span class="text-sky-600 dark:text-sky-400 font-mono">Live High-Res Preview</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SUB-SCREEN 3: B2B CONTRACTS -->
                    <div id="crm-screen-contracts" class="bz-subscreen-view space-y-6" style="display: none;">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50 dark:bg-[#061a2b] p-4 rounded-2xl border border-slate-200 dark:border-[#1e3a52]">
                            <div>
                                <h3 class="font-black text-slate-900 dark:text-white text-base">B2B Institutional Contracts — العقود المؤسسية، فترات السداد والحدود الائتمانية للمشافي</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Route: <span class="font-mono text-emerald-600 dark:text-emerald-400">/admin/crm</span> | Module: <span class="font-mono">InstitutionalAgreementManager</span></p>
                            </div>
                            <span class="badge text-xs bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 font-bold px-3 py-1 rounded-full">Credit Limit Active</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-emerald-600 dark:text-emerald-400">PHASE 01</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">صياغة اتفاقية التوريد</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">تحديد المستحضرات المعتمدة، الكميات السنوية الملتزم بها، ونسب الخصم الممنوحة للمركز.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-sky-600 dark:text-sky-400">PHASE 02</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">تحديد الحد الائتماني Credit Limit</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">اعتماد سقف مالي محدد لا يمكن للعميل تجاوزه في الشراء الآجل دون سداد الفواتير السابقة.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-amber-600 dark:text-amber-400">PHASE 03</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">تفعيل شروط الدفع الآجل</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">ضبط استحقاق الفواتير على (30 أو 60 يوماً) وربطها تلقائياً بنظام المالية للتنبيه بالسداد.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-teal-600 dark:text-teal-400">PHASE 04</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">التجديد السنوي ومراجعة الأداء</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">تقييم نسبة التزام المستشفى بالسحب والسداد السريع وتحديث بنود العقد للسنة المالية الجديدة.</p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5"><i class="fa-solid fa-camera text-emerald-500"></i> المعاينة الحقيقية للعقود المؤسسية والائتمان:</span>
                            <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot18_sales_crm_dashboard.png') }}', 'العقود المؤسسية والحدود الائتمانية B2B Contracts', 'إدارة اتفاقيات التوريد السنوية، الحدود الائتمانية، ومتابعة التحصيلات الآجلة للمراكز.', 18)">
                                <img src="{{ asset('images/guide/shot18_sales_crm_dashboard.png') }}" alt="B2B Contracts" class="w-full h-64 object-cover object-top" loading="lazy">
                                <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-3xl"></i></div>
                                <div class="p-3 bg-slate-50 dark:bg-[#061a2b] text-xs font-bold text-slate-800 dark:text-slate-200 flex justify-between">
                                    <span>نظام إدارة العقود المؤسسية والحدود الائتمانية (B2B Contracts)</span>
                                    <span class="text-emerald-600 dark:text-emerald-400 font-mono">Live High-Res Preview</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================================================= -->
            <!-- MODULE 10: HR & PAYROLL OPERATIONS (ACCORDION) -->
            <!-- ========================================================================= -->
            <div class="bz-accordion-module" data-section="hr" data-keywords="hr payroll attendance employees leaves shifts موارد بشرية رواتب حضور بصمة إجازات ورديات مسير">
                <div class="bz-accordion-header p-5 sm:p-6 flex items-center justify-between gap-4 border-b border-slate-200 dark:border-[#1e3a52]" onclick="toggleModuleAccordion('module-hr')">
                    <div class="flex items-center gap-4">
                        <span class="w-12 h-12 rounded-2xl bg-gradient-to-br from-green-600 to-emerald-800 text-white flex items-center justify-center text-xl shadow-md shadow-green-600/25 flex-shrink-0">
                            <i class="fa-solid fa-users"></i>
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="badge text-[10px] font-black uppercase tracking-wider bg-green-100 dark:bg-green-500/20 text-green-800 dark:text-green-300 border border-green-300 dark:border-green-500/30 px-2 py-0.5 rounded-full">HR & WORKFORCE</span>
                                <h2 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white">
                                    {{ app()->getLocale() === 'ar' ? 'الموارد البشرية، الحضور البيومتري ومسير الرواتب الموحد' : 'Human Resources, Biometric Attendance & Payroll WPS' }}
                                </h2>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5">
                                {{ app()->getLocale() === 'ar' ? '3 شاشات رئيسية: لوحة عمليات الموارد البشرية، سجل الموظفين والورديات، ومسير الرواتب الموحد WPS والبصمة' : '3 Sub-Screens: HR Operations Command, Employee Directory & Shifts, Biometrics & Payroll WPS' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-slate-100 dark:bg-[#061a2b] text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-[#1e3a52]">3 Screens</span>
                        <i id="module-hr-chevron" class="bz-accordion-chevron fa-solid fa-chevron-down text-slate-400 text-sm"></i>
                    </div>
                </div>

                <div id="module-hr-body" class="bz-accordion-body p-6 sm:p-8 space-y-6" style="display: none;">
                    <div class="bz-subscreen-nav flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none border-b border-slate-200 dark:border-[#1e3a52]">
                        <button type="button" class="bz-subscreen-pill active" onclick="switchSubScreen('module-hr', 'hr-screen-dashboard', this)">
                            <i class="fa-solid fa-chart-pie text-green-500"></i>
                            <span>HR Dashboard (مركز قيادة الموارد البشرية)</span>
                        </button>
                        <button type="button" class="bz-subscreen-pill" onclick="switchSubScreen('module-hr', 'hr-screen-employees', this)">
                            <i class="fa-solid fa-id-card-clip text-sky-500"></i>
                            <span>Employee Directory (سجل الموظفين والورديات)</span>
                        </button>
                        <button type="button" class="bz-subscreen-pill" onclick="switchSubScreen('module-hr', 'hr-screen-payroll', this)">
                            <i class="fa-solid fa-money-check-dollar text-emerald-500"></i>
                            <span>Payroll & WPS (البصمة الحية ومسير الرواتب)</span>
                        </button>
                    </div>

                    <!-- SUB-SCREEN 1: HR DASHBOARD -->
                    <div id="hr-screen-dashboard" class="bz-subscreen-view space-y-6">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50 dark:bg-[#061a2b] p-4 rounded-2xl border border-slate-200 dark:border-[#1e3a52]">
                            <div>
                                <h3 class="font-black text-slate-900 dark:text-white text-base">HR Command Center — لوحة عمليات الموارد البشرية ومؤشرات القوى العاملة</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Route: <span class="font-mono text-green-600 dark:text-green-400">/admin/hr</span> | Controller: <span class="font-mono">HrDashboardController@index</span></p>
                            </div>
                            <a href="{{ route('admin.hr.dashboard') }}" target="_blank" class="btn btn-secondary text-xs self-start sm:self-center">
                                <i class="fa-solid fa-users text-xs"></i>
                                <span>لوحة الموارد البشرية</span>
                            </a>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-green-600 dark:text-green-400">PHASE 01</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">رصد مؤشرات القوى العاملة</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">متابعة إجمالي الموظفين، النشطين، فترة التجربة، ونسب التوزيع بين الأقسام والإدارات.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-sky-600 dark:text-sky-400">PHASE 02</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">نبض الحضور اليومي اللحظي</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">عرض نسبة الحضور الفعلية اليومية، المتأخرين، والغياب غير المبرر فور انتهاء فترة السماح.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-amber-600 dark:text-amber-400">PHASE 03</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">تنبيهات انتهاء الوثائق الرسمية</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">حصر الإقامات، الجوازات، التراخيص الطبية، والعقود التي تنتهي خلال 30 يوماً لتفادي الغرامات.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-emerald-600 dark:text-emerald-400">PHASE 04</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">طابور الموافقات السريعة</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">معالجة طلبات الإجازات، السلف المالية، ومبررات الاستئذان المرفوعة من بوابة الخدمة الذاتية.</p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5"><i class="fa-solid fa-camera text-green-500"></i> المعاينة الحقيقية للوحة الموارد البشرية:</span>
                            <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot16_hr_dashboard.png') }}', 'الموارد البشرية: لوحة قيادة الحضور والرواتب', 'متابعة نسب الحضور والانصراف، الورديات، مسير الرواتب الشهري، وطلبات الإجازات المعتمدة.', 16)">
                                <img src="{{ asset('images/guide/shot16_hr_dashboard.png') }}" alt="HR Dashboard" class="w-full h-64 object-cover object-top" loading="lazy">
                                <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-3xl"></i></div>
                                <div class="p-3 bg-slate-50 dark:bg-[#061a2b] text-xs font-bold text-slate-800 dark:text-slate-200 flex justify-between">
                                    <span>لوحة عمليات وإحصاءات الموارد البشرية (HR Operations Dashboard)</span>
                                    <span class="text-green-600 dark:text-green-400 font-mono">Live High-Res Preview</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SUB-SCREEN 2: EMPLOYEE DIRECTORY -->
                    <div id="hr-screen-employees" class="bz-subscreen-view space-y-6" style="display: none;">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50 dark:bg-[#061a2b] p-4 rounded-2xl border border-slate-200 dark:border-[#1e3a52]">
                            <div>
                                <h3 class="font-black text-slate-900 dark:text-white text-base">Employee Directory — سجل الموظفين، الملفات الشخصية، الورديات والتعاقدات</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Route: <span class="font-mono text-sky-600 dark:text-sky-400">/admin/hr/employees</span> | Controller: <span class="font-mono">EmployeeController@index</span></p>
                            </div>
                            <a href="{{ route('admin.hr.employees.index') }}" target="_blank" class="btn btn-secondary text-xs self-start sm:self-center">
                                <i class="fa-solid fa-id-badge text-xs"></i>
                                <span>سجل الموظفين</span>
                            </a>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-sky-600 dark:text-sky-400">PHASE 01</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">تسجيل وتأهيل الموظف</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">إدخال البيانات الشخصية، المؤهلات العلمية، رقم الهوية/الإقامة، والآيبان البنكي للسداد.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-purple-600 dark:text-purple-400">PHASE 02</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">تسكين الهيكل الإداري</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">ربط الموظف بالقسم، المسمى الوظيفي، والمدير المباشر المسؤول عن اعتماد طلباته وإجازاته.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-amber-600 dark:text-amber-400">PHASE 03</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">تخصيص الوردية وسياسة الدوام</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">تعيين جدول العمل (صباحي، مسائي، مختبرات)، وتحديد دقائق السماح قبل احتساب التأخير.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-emerald-600 dark:text-emerald-400">PHASE 04</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">الأرشيف الرقمي والتقييم</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">أرشفة العقود الموقعة، خطابات الترقية، وسجلات تقييم الأداء الموسمي KPI في ملفه.</p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5"><i class="fa-solid fa-camera text-sky-500"></i> المعاينة الحقيقية لسجل الموظفين والورديات:</span>
                            <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot16_hr_dashboard.png') }}', 'سجل الموظفين والملفات الإدارية', 'إدارة بروفايلات الموظفين، إسناد الورديات، وتوثيق العقود والوثائق الرسمية.', 16)">
                                <img src="{{ asset('images/guide/shot16_hr_dashboard.png') }}" alt="Employees Directory" class="w-full h-64 object-cover object-top" loading="lazy">
                                <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-3xl"></i></div>
                                <div class="p-3 bg-slate-50 dark:bg-[#061a2b] text-xs font-bold text-slate-800 dark:text-slate-200 flex justify-between">
                                    <span>دليل وسجلات الموظفين والورديات (Employees Directory)</span>
                                    <span class="text-sky-600 dark:text-sky-400 font-mono">Live High-Res Preview</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SUB-SCREEN 3: PAYROLL & BIOMETRICS -->
                    <div id="hr-screen-payroll" class="bz-subscreen-view space-y-6" style="display: none;">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50 dark:bg-[#061a2b] p-4 rounded-2xl border border-slate-200 dark:border-[#1e3a52]">
                            <div>
                                <h3 class="font-black text-slate-900 dark:text-white text-base">Payroll & Biometrics — البصمة الحية، احتساب الاستحقاقات ومسير الرواتب المعتمد لحماية الأجور (WPS SAMA)</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Route: <span class="font-mono text-emerald-600 dark:text-emerald-400">/admin/hr/payroll</span> | Controller: <span class="font-mono">PayrollController@index</span></p>
                            </div>
                            <a href="{{ route('admin.hr.payroll.index') }}" target="_blank" class="btn btn-secondary text-xs self-start sm:self-center">
                                <i class="fa-solid fa-money-check-dollar text-xs"></i>
                                <span>مسير الرواتب</span>
                            </a>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-emerald-600 dark:text-emerald-400">PHASE 01</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">تجميع حركات البصمة الشهرية</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">سحب سجلات أجهزة البصمة البيومترية وتطبيق الحضور بالهاتف GPS واحتساب صافي الساعات.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-sky-600 dark:text-sky-400">PHASE 02</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">حساب البدلات والاستقطاعات</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">إضافة الساعات الإضافية، بدل السكن والنقل، وخصم التأخيرات وسداد السلف واشتراك التأمينات (GOSI).</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-amber-600 dark:text-amber-400">PHASE 03</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">توليد ملف حماية الأجور WPS</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">تصدير ملف الرواتب بصيغة SAMA المعتمدة لتحويل الرواتب عبر البنوك السعودية بضغطة زر.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-purple-600 dark:text-purple-400">PHASE 04</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">ترحيل القيد وإصدار القسائم</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">ترحيل قيد الرواتب آلياً لشجرة الحسابات COA، وإرسال قسائم الرواتب (Payslips) للموظفين.</p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5"><i class="fa-solid fa-camera text-emerald-500"></i> المعاينة الحقيقية لمسير الرواتب والبصمة:</span>
                            <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot16_hr_dashboard.png') }}', 'مسير الرواتب وحماية الأجور WPS', 'احتساب مسير الرواتب الشهري، خصومات البصمة، إصدار ملف WPS، وقيد الأجور العام.', 16)">
                                <img src="{{ asset('images/guide/shot16_hr_dashboard.png') }}" alt="Payroll WPS" class="w-full h-64 object-cover object-top" loading="lazy">
                                <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-3xl"></i></div>
                                <div class="p-3 bg-slate-50 dark:bg-[#061a2b] text-xs font-bold text-slate-800 dark:text-slate-200 flex justify-between">
                                    <span>نظام مسير الرواتب وحماية الأجور (WPS Payroll Engine)</span>
                                    <span class="text-emerald-600 dark:text-emerald-400 font-mono">Live High-Res Preview</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================================================= -->
            <!-- MODULE 11: HERO SLIDER STUDIO & RBAC SECURITY (ACCORDION) -->
            <!-- ========================================================================= -->
            <div class="bz-accordion-module" data-section="security" data-keywords="slider hero science rbac security permissions roles سلايدر محتوى أبحاث أدوار صلاحيات أمان">
                <div class="bz-accordion-header p-5 sm:p-6 flex items-center justify-between gap-4 border-b border-slate-200 dark:border-[#1e3a52]" onclick="toggleModuleAccordion('module-security')">
                    <div class="flex items-center gap-4">
                        <span class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-600 to-sky-700 text-white flex items-center justify-center text-xl shadow-md shadow-indigo-600/25 flex-shrink-0">
                            <i class="fa-solid fa-shield-halved"></i>
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="badge text-[10px] font-black uppercase tracking-wider bg-indigo-100 dark:bg-indigo-500/20 text-indigo-800 dark:text-indigo-300 border border-indigo-300 dark:border-indigo-500/30 px-2 py-0.5 rounded-full">STUDIO & GOVERNANCE</span>
                                <h2 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white">
                                    {{ app()->getLocale() === 'ar' ? 'استوديو سلايدر الواجهة، والأمان ومصفوفة الصلاحيات RBAC' : 'Hero Slider Studio & RBAC Security Governance' }}
                                </h2>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5">
                                {{ app()->getLocale() === 'ar' ? '3 شاشات رئيسية: استوديو سلايدر الواجهة وسرعات الحركة، محرر الشرائح ومحاكاة التوهج، ومصفوفة الأدوار والصلاحيات RBAC' : '3 Sub-Screens: Hero Slider Studio & Motion, Live Slide Editor & Glow, Fine-Grained RBAC Roles Matrix' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-slate-100 dark:bg-[#061a2b] text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-[#1e3a52]">3 Screens</span>
                        <i id="module-security-chevron" class="bz-accordion-chevron fa-solid fa-chevron-down text-slate-400 text-sm"></i>
                    </div>
                </div>

                <div id="module-security-body" class="bz-accordion-body p-6 sm:p-8 space-y-6" style="display: none;">
                    <div class="bz-subscreen-nav flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none border-b border-slate-200 dark:border-[#1e3a52]">
                        <button type="button" class="bz-subscreen-pill active" onclick="switchSubScreen('module-security', 'sec-screen-slider', this)">
                            <i class="fa-solid fa-images text-indigo-500"></i>
                            <span>Hero Slider Studio (استوديو سلايدر الواجهة)</span>
                        </button>
                        <button type="button" class="bz-subscreen-pill" onclick="switchSubScreen('module-security', 'sec-screen-slide-editor', this)">
                            <i class="fa-solid fa-wand-magic-sparkles text-sky-500"></i>
                            <span>Slide Editor & Glow (محرر الشرائح والمحاكاة)</span>
                        </button>
                        <button type="button" class="bz-subscreen-pill" onclick="switchSubScreen('module-security', 'sec-screen-rbac', this)">
                            <i class="fa-solid fa-user-shield text-purple-500"></i>
                            <span>RBAC Security Matrix (مصفوفة الأدوار والصلاحيات)</span>
                        </button>
                    </div>

                    <!-- SUB-SCREEN 1: HERO SLIDER STUDIO -->
                    <div id="sec-screen-slider" class="bz-subscreen-view space-y-6">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50 dark:bg-[#061a2b] p-4 rounded-2xl border border-slate-200 dark:border-[#1e3a52]">
                            <div>
                                <h3 class="font-black text-slate-900 dark:text-white text-base">Hero Slider Studio — استوديو إدارة سلايدر الواجهة الرئيسية وتوقيتات الحركة الفيزيائية</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Route: <span class="font-mono text-indigo-600 dark:text-indigo-400">/admin/hero-slider</span> | Controller: <span class="font-mono">HeroSlideController@index</span></p>
                            </div>
                            <a href="{{ route('admin.hero-slides.index') }}" target="_blank" class="btn btn-secondary text-xs self-start sm:self-center">
                                <i class="fa-solid fa-sliders text-xs"></i>
                                <span>استوديو السلايدر</span>
                            </a>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-indigo-600 dark:text-indigo-400">PHASE 01</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">ترتيب الشرائح بالسحب</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">إعادة ترتيب أولوية ظهور الشرائح بالسحب والإفلات (Drag & Drop) مع تفعيل/تعطيل فوري.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-sky-600 dark:text-sky-400">PHASE 02</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">فيزياء وتوقيت الانتقال</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">ضبط سرعة الانتقال الذاتي (Autoplay Duration) ومعدلات النعومة Bezier لضمان تجربة فاخرة.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-amber-600 dark:text-amber-400">PHASE 03</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">ربط الدراسات العلمية</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">ربط كل شريحة بالدراسة الإكلينيكية الموثقة وبالمستحضر المعني لتعزيز المصداقية الطبية.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-emerald-600 dark:text-emerald-400">PHASE 04</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">المزامنة مع الواجهة الرئيسية</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">نشر التحديثات فوراً لواجهة المتجر دون الحاجة لإفراغ الكاش أو إعادة تحميل الصفحات.</p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5"><i class="fa-solid fa-camera text-indigo-500"></i> المعاينة الحقيقية لاستوديو سلايدر الواجهة:</span>
                            <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot9_hero_slider_overview.png') }}', 'سلايدر الواجهة: الاستوديو وترتيب الشرائح', 'جدول الشرائح، تفعيل/تعطيل العرض، ضبط التوقيتات، ونشر الشرائح على واجهة المتجر.', 9)">
                                <img src="{{ asset('images/guide/shot9_hero_slider_overview.png') }}" alt="Hero Slider Studio" class="w-full h-64 object-cover object-top" loading="lazy">
                                <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-3xl"></i></div>
                                <div class="p-3 bg-slate-50 dark:bg-[#061a2b] text-xs font-bold text-slate-800 dark:text-slate-200 flex justify-between">
                                    <span>استوديو إدارة سلايدر الواجهة الرئيسية (Hero Slider Studio)</span>
                                    <span class="text-indigo-600 dark:text-indigo-400 font-mono">Live High-Res Preview</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SUB-SCREEN 2: SLIDE EDITOR & GLOW -->
                    <div id="sec-screen-slide-editor" class="bz-subscreen-view space-y-6" style="display: none;">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50 dark:bg-[#061a2b] p-4 rounded-2xl border border-slate-200 dark:border-[#1e3a52]">
                            <div>
                                <h3 class="font-black text-slate-900 dark:text-white text-base">Slide Editor & Glow — محرر الشريحة التفاعلي، ضبط هالات التوهج والمحاكاة الحية</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Route: <span class="font-mono text-sky-600 dark:text-sky-400">/admin/hero-slider</span> | Component: <span class="font-mono">LiveSlideSimulatorModal</span></p>
                            </div>
                            <span class="badge text-xs bg-sky-100 dark:bg-sky-500/20 text-sky-700 dark:text-sky-300 font-bold px-3 py-1 rounded-full">Particle Glow Engine</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-sky-600 dark:text-sky-400">PHASE 01</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">المحتوى ثنائي اللغة</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">إدخال العنوان الرئيسي، الوصف الفرعي باللغتين العربية والإنجليزية مع دعم تنسيقات الخطوط.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-purple-600 dark:text-purple-400">PHASE 02</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">رفع الأصول وضبط التركيز</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">رفع صور المستحضرات بدقة فائقة وضبط نقطة الارتكاز (Focal Point) لتناسب شاشات الهواتف والحواسيب.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-amber-600 dark:text-amber-400">PHASE 03</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">تخصيص لون التوهج Glow</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">اختيار كود لون الإشعاع المحيطي المتوافق مع هوية التركيبة (Cyan, Emerald, Violet).</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-emerald-600 dark:text-emerald-400">PHASE 04</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">المعاينة اللحظية والمحاكاة</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">محاكاة فورية للشريحة داخل الاستوديو للتأكد من تناسق النصوص وأزرار الشراء قبل الحفظ.</p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5"><i class="fa-solid fa-camera text-sky-500"></i> المعاينة الحقيقية لمحرر الشرائح والتوهج:</span>
                            <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot10_hero_slide_modal.png') }}', 'سلايدر الواجهة: محرر الشريحة واستوديو المحاكاة', 'ربط المنتج التلقائي، ضبط نصوص التوهج التدرجي، وتنسيق أزرار الطلب مع معاينة حية للشريحة.', 10)">
                                <img src="{{ asset('images/guide/shot10_hero_slide_modal.png') }}" alt="Slide Editor Modal" class="w-full h-64 object-cover object-top" loading="lazy">
                                <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-3xl"></i></div>
                                <div class="p-3 bg-slate-50 dark:bg-[#061a2b] text-xs font-bold text-slate-800 dark:text-slate-200 flex justify-between">
                                    <span>محرر الشريحة التفاعلي ومحاكي التوهج (Slide Editor Modal)</span>
                                    <span class="text-sky-600 dark:text-sky-400 font-mono">Live High-Res Preview</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SUB-SCREEN 3: RBAC ROLES MATRIX -->
                    <div id="sec-screen-rbac" class="bz-subscreen-view space-y-6" style="display: none;">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50 dark:bg-[#061a2b] p-4 rounded-2xl border border-slate-200 dark:border-[#1e3a52]">
                            <div>
                                <h3 class="font-black text-slate-900 dark:text-white text-base">RBAC Security Matrix — مصفوفة الأدوار والصلاحيات الدقيقة وسجل التدقيق الأمني (Audit Trail)</h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Route: <span class="font-mono text-purple-600 dark:text-purple-400">/admin/roles</span> | Controller: <span class="font-mono">RoleController@index</span></p>
                            </div>
                            <a href="{{ route('admin.roles.index') }}" target="_blank" class="btn btn-secondary text-xs self-start sm:self-center">
                                <i class="fa-solid fa-user-shield text-xs"></i>
                                <span>مصفوفة الأدوار</span>
                            </a>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-purple-600 dark:text-purple-400">PHASE 01</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">تعريف الأدوار الوظيفية</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">إنشاء الأدوار المخصصة (مدير عام، مدير مالي، مسؤول جودة QP، مندوب طبي، أمين مستودع).</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-sky-600 dark:text-sky-400">PHASE 02</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">تحديد الصلاحيات الدقيقة Granular</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">ضبط صلاحيات العرض، الإضافة، التعديل، الحذف، والاعتمادات لكل شاشة وقسم على حدة.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-amber-600 dark:text-amber-400">PHASE 03</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">إسناد المستخدمين وفرض الأمان</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">ربط حسابات المستخدمين بالأدوار وفرض التحقق الثنائي وسياسات انتهاء الجلسات التلقائي.</p>
                            </div>
                            <div class="bz-cycle-step">
                                <span class="text-[10px] font-mono font-black text-emerald-600 dark:text-emerald-400">PHASE 04</span>
                                <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-1">سجل العمليات Audit Trail</h4>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">تسجيل تاريخي غير قابل للتعديل لكافة حركات النظام (من قام بماذا، ومتى، ومع أي تشغيلة أو فاتورة).</p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5"><i class="fa-solid fa-camera text-purple-500"></i> المعاينة الحقيقية لمصفوفة الأدوار والصلاحيات:</span>
                            <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot20_roles_rbac_security.png') }}', 'الأمان: مصفوفة الأدوار والصلاحيات الدقيقة RBAC', 'إدارة الصلاحيات الدقيقة، إنشاء الأدوار، وعزل مهام المشرفين والمحاسبين والمندوبين بدقة تامة.', 20)">
                                <img src="{{ asset('images/guide/shot20_roles_rbac_security.png') }}" alt="RBAC Roles" class="w-full h-64 object-cover object-top" loading="lazy">
                                <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-3xl"></i></div>
                                <div class="p-3 bg-slate-50 dark:bg-[#061a2b] text-xs font-bold text-slate-800 dark:text-slate-200 flex justify-between">
                                    <span>مصفوفة الأدوار والصلاحيات الدقيقة (RBAC Security Matrix)</span>
                                    <span class="text-purple-600 dark:text-purple-400 font-mono">Live High-Res Preview</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            </div> <!-- End of #logic-cycles container -->

        <!-- ========================================================================= -->
        <!-- 3. MASTER SYSTEM ELEMENTS & SCREENS CATALOG (#system-catalog) -->
        <!-- ========================================================================= -->
        <div id="system-catalog" class="scroll-mt-24 space-y-6">
            <div class="bg-white dark:bg-[#081d2f] border border-slate-200 dark:border-[#1e3a52] rounded-3xl p-6 sm:p-8 space-y-6 border-l-4 border-l-amber-500 shadow-sm">
                <!-- Section Header -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 dark:border-[#1e3a52] pb-6">
                    <div class="flex items-center gap-4">
                        <span class="w-13 h-13 rounded-2xl bg-gradient-to-br from-amber-500 to-amber-700 text-white flex items-center justify-center text-2xl shadow-lg shadow-amber-500/25 flex-shrink-0">
                            <i class="fa-solid fa-table-cells"></i>
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="badge text-[10px] font-black uppercase tracking-wider bg-amber-100 dark:bg-amber-500/20 text-amber-800 dark:text-amber-300 border border-amber-300 dark:border-amber-500/30 px-2 py-0.5 rounded-full">MASTER CATALOG</span>
                                <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">
                                    {{ app()->getLocale() === 'ar' ? 'فهرس الشاشات، العناصر، الحقول، وأزرار الإجراءات' : 'Master System Screens, Elements & Actions Catalog' }}
                                </h2>
                            </div>
                            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-medium mt-1">
                                {{ app()->getLocale() === 'ar' ? 'المرجع المعماري والميداني لكافة شاشات وحقول النظام، قواعد التحقق، أزرار الإجراءات، والأثر المحاسبي والمخزني التلقائي' : 'Comprehensive directory of all system pages: form controls, validation rules, triggers and automated inventory & financial outputs' }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="relative w-full sm:w-64">
                            <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                                <i class="fa-solid fa-filter text-xs"></i>
                            </span>
                            <input type="text" id="catalogSearchInput" oninput="filterCatalogTable(this.value)" 
                                   placeholder="{{ app()->getLocale() === 'ar' ? 'تصفية الفهرس...' : 'Filter elements...' }}" 
                                   class="w-full pl-3 pr-8 py-2 text-xs rounded-xl bg-slate-50 dark:bg-[#061a2b] border border-slate-200 dark:border-[#1e3a52] text-slate-800 dark:text-white placeholder-slate-400 focus:outline-hidden focus:ring-2 focus:ring-amber-500">
                        </div>
                    </div>
                </div>

                <!-- Master Elements Table -->
                <div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-[#1e3a52]">
                    <table class="w-full text-right text-xs">
                        <thead class="bg-slate-100 dark:bg-[#061a2b] text-slate-700 dark:text-slate-300 font-black uppercase text-[11px] border-b border-slate-200 dark:border-[#1e3a52]">
                            <tr>
                                <th class="p-3.5">{{ app()->getLocale() === 'ar' ? 'القسم والموديول' : 'Module' }}</th>
                                <th class="p-3.5">{{ app()->getLocale() === 'ar' ? 'الشاشة والمسار البرمجي' : 'Screen & Route' }}</th>
                                <th class="p-3.5">{{ app()->getLocale() === 'ar' ? 'عناصر التحكم والحقول الأساسية' : 'Key Form Fields & Elements' }}</th>
                                <th class="p-3.5">{{ app()->getLocale() === 'ar' ? 'أزرار الإجراءات (Actions)' : 'Triggers & Actions' }}</th>
                                <th class="p-3.5">{{ app()->getLocale() === 'ar' ? 'الأثر المحاسبي والمخزني والحالات' : 'Ledger & Inventory Rules' }}</th>
                            </tr>
                        </thead>
                        <tbody id="catalogTableBody" class="divide-y divide-slate-200 dark:divide-[#1e3a52] text-slate-600 dark:text-slate-300">
                            <!-- 1. Catalog & Products -->
                            <tr class="bz-catalog-row hover:bg-slate-50 dark:hover:bg-[#0a253d] transition-colors">
                                <td class="p-3.5 font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                    <span class="w-7 h-7 rounded-lg bg-sky-100 dark:bg-sky-500/20 text-sky-600 flex items-center justify-center text-xs"><i class="fa-solid fa-box-open"></i></span>
                                    <span>الكتالوج والمنتجات</span>
                                </td>
                                <td class="p-3.5 font-mono text-[11px] text-sky-600 dark:text-sky-400">
                                    /admin/products/create<br>
                                    <span class="text-slate-400">Route: admin.products.create</span>
                                </td>
                                <td class="p-3.5">
                                    SKU (فريد)، SEO Slug، الاسم ثنائي اللغة، السعر، ضريبة القيمة المضافة، صورة Packshot المفرغة، 8 مفاتيح مخصص الشاشات (Desktop vs Mobile)، وباقات التآزر.
                                </td>
                                <td class="p-3.5">
                                    <span class="px-2 py-0.5 rounded-md bg-sky-100 dark:bg-sky-500/20 text-sky-700 dark:text-sky-300 font-bold">Auto-Save AJAX</span>,
                                    <span class="px-2 py-0.5 rounded-md bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 font-bold">نشر فوري Live</span>
                                </td>
                                <td class="p-3.5">
                                    إنشاء سجل في جدول `products`، حفظ المسودة تلقائياً، حجز SKU في جدول `inventory_items`، ومنع كسر الروابط الدائمة (SEO Slug Uniqueness).
                                </td>
                            </tr>

                            <!-- 2. Procurement -->
                            <tr class="bz-catalog-row hover:bg-slate-50 dark:hover:bg-[#0a253d] transition-colors">
                                <td class="p-3.5 font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                    <span class="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 flex items-center justify-center text-xs"><i class="fa-solid fa-cart-shopping"></i></span>
                                    <span>المشتريات والموردين</span>
                                </td>
                                <td class="p-3.5 font-mono text-[11px] text-emerald-600 dark:text-emerald-400">
                                    /admin/purchase-orders/create<br>
                                    <span class="text-slate-400">Route: admin.purchase-orders.create</span>
                                </td>
                                <td class="p-3.5">
                                    اختيار المورد المعتمد، تاريخ الاستحقاق، جدول بنود المواد الخام، الكمية، سعر الوحدة، العملة، وشروط الشحن Incoterms.
                                </td>
                                <td class="p-3.5">
                                    <span class="px-2 py-0.5 rounded-md bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 font-bold">اعتماد الشراء Approve</span>,
                                    <span class="px-2 py-0.5 rounded-md bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300 font-bold">إلغاء PO Cancel</span>
                                </td>
                                <td class="p-3.5">
                                    حالة PO: (Draft -> Approved -> Received). تثبيت التزامات الشراء في جدول الذمم الدائنة (Accounts Payable) وتفعيل تتبع التوريد.
                                </td>
                            </tr>

                            <!-- 3. Cold-Chain & QC -->
                            <tr class="bz-catalog-row hover:bg-slate-50 dark:hover:bg-[#0a253d] transition-colors">
                                <td class="p-3.5 font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                    <span class="w-7 h-7 rounded-lg bg-cyan-100 dark:bg-cyan-500/20 text-cyan-600 flex items-center justify-center text-xs"><i class="fa-solid fa-snowflake"></i></span>
                                    <span>الحجر وضبط الجودة</span>
                                </td>
                                <td class="p-3.5 font-mono text-[11px] text-cyan-600 dark:text-cyan-400">
                                    /admin/goods-receipts<br>
                                    <span class="text-slate-400">Route: admin.goods-receipts.index</span>
                                </td>
                                <td class="p-3.5">
                                    رقم بوليصة الشحن، حالة أختام الحاوية، قراءات مسجلات الحرارة (Data Loggers)، شهادة التحليل COA، وموقع الحجر الصحي Quarantine.
                                </td>
                                <td class="p-3.5">
                                    <span class="px-2 py-0.5 rounded-md bg-cyan-100 dark:bg-cyan-500/20 text-cyan-700 dark:text-cyan-300 font-bold">إيداع الحجر Quarantine</span>,
                                    <span class="px-2 py-0.5 rounded-md bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 font-bold">إفراج كلي QC Release</span>
                                </td>
                                <td class="p-3.5">
                                    حظر صرف المخزون نهائياً لحين صدور شهادة الفحص المخبري، تحويل الكمية آلياً للمستودع المركزي فور اعتماد الإفراج.
                                </td>
                            </tr>

                            <!-- 4. Inventory -->
                            <tr class="bz-catalog-row hover:bg-slate-50 dark:hover:bg-[#0a253d] transition-colors">
                                <td class="p-3.5 font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                    <span class="w-7 h-7 rounded-lg bg-amber-100 dark:bg-amber-500/20 text-amber-600 flex items-center justify-center text-xs"><i class="fa-solid fa-warehouse"></i></span>
                                    <span>المخزون والتشغيلات</span>
                                </td>
                                <td class="p-3.5 font-mono text-[11px] text-amber-600 dark:text-amber-400">
                                    /admin/batches<br>
                                    <span class="text-slate-400">Route: admin.batches.index</span>
                                </td>
                                <td class="p-3.5">
                                    كود التشغيلة (Batch)، تاريخ الإنتاج والصلاحية، موقع التخزين، حصة المتجر (Online)، رصيد المستودع المركزي، ورقم الباركود.
                                </td>
                                <td class="p-3.5">
                                    <span class="px-2 py-0.5 rounded-md bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300 font-bold">تحويل بيني Transfer</span>,
                                    <span class="px-2 py-0.5 rounded-md bg-purple-100 dark:bg-purple-500/20 text-purple-700 dark:text-purple-300 font-bold">تسوية جردية Adjust</span>
                                </td>
                                <td class="p-3.5">
                                    تطبيق قاعدة FIFO تلقائياً، تسجيل حركة في جدول `inventory_movements`، وتوليد قيد تسوية فروقات الجرد المحاسبي.
                                </td>
                            </tr>

                            <!-- 5. Manufacturing -->
                            <tr class="bz-catalog-row hover:bg-slate-50 dark:hover:bg-[#0a253d] transition-colors">
                                <td class="p-3.5 font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                    <span class="w-7 h-7 rounded-lg bg-purple-100 dark:bg-purple-500/20 text-purple-600 flex items-center justify-center text-xs"><i class="fa-solid fa-industry"></i></span>
                                    <span>التصنيع الدوائي cGMP</span>
                                </td>
                                <td class="p-3.5 font-mono text-[11px] text-purple-600 dark:text-purple-400">
                                    /admin/work-orders<br>
                                    <span class="text-slate-400">Route: admin.work-orders.index</span>
                                </td>
                                <td class="p-3.5">
                                    رقم أمر التشغيل، كود المنتج التام، صيغة التشغيلة (BOM)، كمية المواد الفعالة والمساعدة، ومسؤول وزن وصرف المواد (Dispenser).
                                </td>
                                <td class="p-3.5">
                                    <span class="px-2 py-0.5 rounded-md bg-purple-100 dark:bg-purple-500/20 text-purple-700 dark:text-purple-300 font-bold">بدء الصرف Dispense</span>,
                                    <span class="px-2 py-0.5 rounded-md bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 font-bold">إتمام الإنتاج Complete</span>
                                </td>
                                <td class="p-3.5">
                                    خصم المواد الخام المستهلكة من مستودع الخامات، إضافة كمية المنتج التام للمركزي، وترحيل تكلفة التصنيع المباشرة.
                                </td>
                            </tr>

                            <!-- 6. Sales & Orders -->
                            <tr class="bz-catalog-row hover:bg-slate-50 dark:hover:bg-[#0a253d] transition-colors">
                                <td class="p-3.5 font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                    <span class="w-7 h-7 rounded-lg bg-rose-100 dark:bg-rose-500/20 text-rose-600 flex items-center justify-center text-xs"><i class="fa-solid fa-truck-fast"></i></span>
                                    <span>الطلبات والشحن</span>
                                </td>
                                <td class="p-3.5 font-mono text-[11px] text-rose-600 dark:text-rose-400">
                                    /admin/orders<br>
                                    <span class="text-slate-400">Route: admin.orders.index</span>
                                </td>
                                <td class="p-3.5">
                                    رقم الطلب، اسم العميل، عنوان التوصيل، بنود المنتجات والكميات، إجمالي الضريبة، بوابة الدفع، وحالة الشحن.
                                </td>
                                <td class="p-3.5">
                                    <span class="px-2 py-0.5 rounded-md bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300 font-bold">تجهيز الشحنة Pack</span>,
                                    <span class="px-2 py-0.5 rounded-md bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-300 font-bold">إصدار بوليصة Waybill</span>
                                </td>
                                <td class="p-3.5">
                                    حجز فوري للمخزون (Reserved Stock)، خصم فعلي عند الشحن، وتوليد الفاتورة الضريبية Zakat Compliant مع رمز QR.
                                </td>
                            </tr>

                            <!-- 7. Finance -->
                            <tr class="bz-catalog-row hover:bg-slate-50 dark:hover:bg-[#0a253d] transition-colors">
                                <td class="p-3.5 font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                    <span class="w-7 h-7 rounded-lg bg-teal-100 dark:bg-teal-500/20 text-teal-600 flex items-center justify-center text-xs"><i class="fa-solid fa-coins"></i></span>
                                    <span>المالية وشجرة الحسابات</span>
                                </td>
                                <td class="p-3.5 font-mono text-[11px] text-teal-600 dark:text-teal-400">
                                    /admin/finance/accounts<br>
                                    <span class="text-slate-400">Route: admin.finance.accounts.index</span>
                                </td>
                                <td class="p-3.5">
                                    رمز الحساب الشجري (Account Code)، نوع الحساب (أصل، خصم، مصروف، إيراد)، الرصيد المدين والدائن، وسندات القبض والصرف.
                                </td>
                                <td class="p-3.5">
                                    <span class="px-2 py-0.5 rounded-md bg-teal-100 dark:bg-teal-500/20 text-teal-700 dark:text-teal-300 font-bold">قيد يدوي Manual Entry</span>,
                                    <span class="px-2 py-0.5 rounded-md bg-sky-100 dark:bg-sky-500/20 text-sky-700 dark:text-sky-300 font-bold">تسوية بنك Reconcile</span>
                                </td>
                                <td class="p-3.5">
                                    توازن القيد المحاسبي المزدوج (Debit = Credit)، ربط حركات البيع والشراء آلياً، وتحديث ميزان المراجعة اللحظي.
                                </td>
                            </tr>

                            <!-- 8. Field MR -->
                            <tr class="bz-catalog-row hover:bg-slate-50 dark:hover:bg-[#0a253d] transition-colors">
                                <td class="p-3.5 font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                    <span class="w-7 h-7 rounded-lg bg-blue-100 dark:bg-blue-500/20 text-blue-600 flex items-center justify-center text-xs"><i class="fa-solid fa-user-doctor"></i></span>
                                    <span>المندوبون والعيادات</span>
                                </td>
                                <td class="p-3.5 font-mono text-[11px] text-blue-600 dark:text-blue-400">
                                    /admin/visits<br>
                                    <span class="text-slate-400">Route: admin.visits.index</span>
                                </td>
                                <td class="p-3.5">
                                    اسم الطبيب، المركز الطبي/العيادة، تصنيف التخصص، إحداثيات GPS المباشرة، خط السير، وسجل العينات المسلمة (Sample Drops).
                                </td>
                                <td class="p-3.5">
                                    <span class="px-2 py-0.5 rounded-md bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-300 font-bold">تحقق GPS Check-in</span>,
                                    <span class="px-2 py-0.5 rounded-md bg-indigo-100 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300 font-bold">تسجيل عينة Drop</span>
                                </td>
                                <td class="p-3.5">
                                    منع تسجيل الزيارة خارج نطاق 100 متر من العيادة، خصم العينات تلقائياً من عهدة المندوب، وتحديث نسبة تغطية التارجت.
                                </td>
                            </tr>

                            <!-- 9. Sales CRM -->
                            <tr class="bz-catalog-row hover:bg-slate-50 dark:hover:bg-[#0a253d] transition-colors">
                                <td class="p-3.5 font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                    <span class="w-7 h-7 rounded-lg bg-orange-100 dark:bg-orange-500/20 text-orange-600 flex items-center justify-center text-xs"><i class="fa-solid fa-handshake"></i></span>
                                    <span>علاقات العملاء CRM</span>
                                </td>
                                <td class="p-3.5 font-mono text-[11px] text-orange-600 dark:text-orange-400">
                                    /admin/crm<br>
                                    <span class="text-slate-400">Route: admin.crm.dashboard</span>
                                </td>
                                <td class="p-3.5">
                                    العميل المحتمل (Lead)، جهة الاتصال، مرحلة الصفقات Kanban، قيمة العقد المتوقعة، شروط الخصم، وتاريخ الإغلاق المستهدف.
                                </td>
                                <td class="p-3.5">
                                    <span class="px-2 py-0.5 rounded-md bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 font-bold">إغلاق رابح Won</span>,
                                    <span class="px-2 py-0.5 rounded-md bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300 font-bold">خسارة الصفقة Lost</span>
                                </td>
                                <td class="p-3.5">
                                    تحويل العميل الفائز إلى عميل مؤسسي دائم في جدول `customers`، تخصيص حد ائتماني وربط جدول الأسعار الخاص به.
                                </td>
                            </tr>

                            <!-- 10. HR & Payroll -->
                            <tr class="bz-catalog-row hover:bg-slate-50 dark:hover:bg-[#0a253d] transition-colors">
                                <td class="p-3.5 font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                    <span class="w-7 h-7 rounded-lg bg-green-100 dark:bg-green-500/20 text-green-600 flex items-center justify-center text-xs"><i class="fa-solid fa-users"></i></span>
                                    <span>الموارد والرواتب</span>
                                </td>
                                <td class="p-3.5 font-mono text-[11px] text-green-600 dark:text-green-400">
                                    /admin/hr/attendance<br>
                                    <span class="text-slate-400">Route: admin.hr.attendance.index</span>
                                </td>
                                <td class="p-3.5">
                                    كود الموظف، وقت البصمة الحية، إحداثيات الموقع الجغرافي، الوردية، دقائق التأخير، ساعات الإضافي، ورصيد الإجازات.
                                </td>
                                <td class="p-3.5">
                                    <span class="px-2 py-0.5 rounded-md bg-green-100 dark:bg-green-500/20 text-green-700 dark:text-green-300 font-bold">بصمة حضور Punch-in</span>,
                                    <span class="px-2 py-0.5 rounded-md bg-teal-100 dark:bg-teal-500/20 text-teal-700 dark:text-teal-300 font-bold">ترحيل المسير Payroll</span>
                                </td>
                                <td class="p-3.5">
                                    حساب الاستقطاعات والبدلات آلياً، وتوليد قيد استحقاق الرواتب في الحسابات العامة (من حـ/ مصروفات الرواتب إلى حـ/ الرواتب المستحقة).
                                </td>
                            </tr>

                            <!-- 11. Slider & Security -->
                            <tr class="bz-catalog-row hover:bg-slate-50 dark:hover:bg-[#0a253d] transition-colors">
                                <td class="p-3.5 font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                    <span class="w-7 h-7 rounded-lg bg-indigo-100 dark:bg-indigo-500/20 text-indigo-600 flex items-center justify-center text-xs"><i class="fa-solid fa-shield-halved"></i></span>
                                    <span>السلايدر والأمان</span>
                                </td>
                                <td class="p-3.5 font-mono text-[11px] text-indigo-600 dark:text-indigo-400">
                                    /admin/hero-slider و /admin/roles<br>
                                    <span class="text-slate-400">Routes: admin.hero-slides.index, admin.roles.index</span>
                                </td>
                                <td class="p-3.5">
                                    سرعة السلايدر (Delay 5500ms)، تدرج النصوص، ربط المنتج، مصفوفة الصلاحيات (مشاهدة/إنشاء/تعديل/حذف)، وسجل عمليات الـ Audit.
                                </td>
                                <td class="p-3.5">
                                    <span class="px-2 py-0.5 rounded-md bg-indigo-100 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300 font-bold">حفظ الشريحة Save</span>,
                                    <span class="px-2 py-0.5 rounded-md bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300 font-bold">عزل الصلاحية Revoke</span>
                                </td>
                                <td class="p-3.5">
                                    تحديث فوري لواجهة المتجر، منع الوصول غير المصرح عبر Middleware الصلاحيات، وتسجيل كل عملية إدارية مع عنوان IP.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- 4. APPLE-STYLE FULLSCREEN LIGHTBOX MODAL WITH ZOOM & CONTROLS -->
    <!-- ========================================================================= -->
    <div id="guideLightbox" onclick="closeLightboxModal(event)">
        <div id="guideLightboxContent" onclick="event.stopPropagation()">
            <!-- Top Bar Actions -->
            <div class="w-full flex items-center justify-between text-white pb-3 px-2">
                <div class="flex items-center gap-3">
                    <span id="lightboxStepBadge" class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-sky-500/30 text-sky-300 border border-sky-400/40">
                        STEP 01
                    </span>
                    <h3 id="lightboxTitle" class="text-base sm:text-lg font-black tracking-tight text-white drop-shadow-sm">
                        عنوان الشاشة
                    </h3>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" onclick="prevLightboxImage()" class="w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white flex items-center justify-center transition-all cursor-pointer" title="السابق (Arrow Left)">
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                    <button type="button" onclick="nextLightboxImage()" class="w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white flex items-center justify-center transition-all cursor-pointer" title="التالي (Arrow Right)">
                        <i class="fa-solid fa-arrow-left"></i>
                    </button>
                    <button type="button" onclick="closeLightboxModal()" class="w-10 h-10 rounded-xl bg-rose-500/20 hover:bg-rose-500/40 border border-rose-500/40 text-rose-300 flex items-center justify-center transition-all cursor-pointer" title="إغلاق (Esc)">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>
            </div>

            <!-- Image View -->
            <div class="relative overflow-hidden rounded-2xl flex items-center justify-center bg-black/40">
                <img id="lightboxImage" src="" alt="Guide Screenshot">
            </div>

            <!-- Bottom Description Card -->
            <div class="w-full mt-3 p-4 rounded-2xl bg-[#081d2f]/90 border border-sky-500/30 backdrop-blur-md text-white shadow-xl">
                <p id="lightboxDescription" class="text-xs sm:text-sm font-medium leading-relaxed text-slate-200">
                    وصف الإجراء والخطوة الميدانية بالتفصيل.
                </p>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 5. LIGHTBOX & DYNAMIC ACCORDION CONTROLLER SCRIPT -->
    <!-- ========================================================================= -->
    <script>
        // All 20 Real Distinct System Screenshots
        const guideGallery = [
            {
                step: 1,
                url: "{{ asset('images/guide/shot1_step1_core.png') }}",
                title: "الخطوة 1: الرمز والتصنيف الأساسي والحفظ التلقائي",
                desc: "تسجيل رمز SKU المعياري، الرابط الدائم (SEO Slug)، وتحديد التصنيف العلاجي مع الحفظ التلقائي الفوري للمسودة."
            },
            {
                step: 2,
                url: "{{ asset('images/guide/shot2_step2_content.png') }}",
                title: "الخطوة 2: المحتوى الدوائي ثنائي اللغة",
                desc: "كتابة الاسم التجاري والعلمي، الملخص التسويقي، والوصف السريري المفصل باللغتين العربية والإنجليزية."
            },
            {
                step: 3,
                url: "{{ asset('images/guide/shot3_step3_pricing.png') }}",
                title: "الخطوة 3: السياسة السعرية وهوامش الربح والضرائب",
                desc: "تحديد سعر البيع الأساسي والتخفيض، احتساب ضريبة القيمة المضافة ديناميكياً، وتكلفة الاستيراد لحساب هامش الربح."
            },
            {
                step: 4,
                url: "{{ asset('images/guide/shot4_step4_media.png') }}",
                title: "الخطوة 4: الأصول ومعرض الوسائط المفرغة",
                desc: "رفع صورة العبوة المفرغة (Master Packshot) وتوليد الصور المصغرة ومعرض الزوايا المتعددة بجودة عالية."
            },
            {
                step: 5,
                url: "{{ asset('images/guide/shot5_step5_clinical.png') }}",
                title: "الخطوة 5: الملف الطبي وجدول العيارات والجرعات",
                desc: "جدول المكونات الفعالة والعيارات المعيارية والمؤشرات الفسيولوجية المستهدفة والفئات العمرية المسموحة."
            },
            {
                step: 6,
                url: "{{ asset('images/guide/shot6_step6_customizer.png') }}",
                title: "الخطوة 6: مخصص أقسام صفحة المنتج (Desktop vs Mobile)",
                desc: "التحكم المنفصل لظهور الأقسام الثمانية على الكمبيوتر والموبايل وحالة الفتح الافتراضية للأكورديون."
            },
            {
                step: 7,
                url: "{{ asset('images/guide/shot7_step6_bundle.png') }}",
                title: "الخطوة 6 (موسع): باقات التآزر الحيوي الموحدة",
                desc: "اختيار المكملات التكميلية وربطها في باقة متكاملة بخصم مخصص وزر إضافة جماعي موحد."
            },
            {
                step: 8,
                url: "{{ asset('images/guide/shot8_step7_inventory.png') }}",
                title: "الخطوة 7: تكامل المخزون والباركود وإعادة الطلب",
                desc: "تحديد نوع المخزون، باركود العبوة، وزن الشحن، وتعيين حدود التنبيه المبكر لإعادة الطلب."
            },
            {
                step: 9,
                url: "{{ asset('images/guide/shot9_hero_slider_overview.png') }}",
                title: "سلايدر الواجهة: مركز القيادة والترتيب المباشر",
                desc: "التحكم في زمن الحركة التلقائي (Delay 5500ms)، تشغيل السلايدر، وإعادة الترتيب المباشر بالسحب والإفلات."
            },
            {
                step: 10,
                url: "{{ asset('images/guide/shot10_hero_slide_modal.png') }}",
                title: "سلايدر الواجهة: محرر الشريحة واستوديو المحاكاة",
                desc: "ربط المنتج التلقائي، ضبط نصوص التوهج التدرجي، وتنسيق أزرار الطلب مع معاينة حية للشريحة."
            },
            {
                step: 11,
                url: "{{ asset('images/guide/shot11_purchases_po.png') }}",
                title: "المشتريات: أوامر الشراء الرسمية واعتماد الموردين",
                desc: "جدول أوامر الشراء (POs)، اعتماد شروط التوريد، ومتابعة فواتير المواد الخام والمطابقة الثلاثية."
            },
            {
                step: 12,
                url: "{{ asset('images/guide/shot12_goods_receipts.png') }}",
                title: "الحجر الصحي: سندات استلام البضائع وفحص الحرارة (GRN)",
                desc: "سندات استلام الشحنات المبردة، تسجيل مسجلات الحرارة (Data Loggers)، وإيداع التشغيلات في الحجر الصحي."
            },
            {
                step: 13,
                url: "{{ asset('images/guide/shot13_finance_dashboard.png') }}",
                title: "المالية: لوحة تحكم المنظومة والتدفق النقدي",
                desc: "متابعة الذمم المدينة والدائنة، إجمالي المبيعات، ومصروفات التشغيل وحركة الصناديق والبنوك."
            },
            {
                step: 14,
                url: "{{ asset('images/guide/shot14_manufacturing_work_orders.png') }}",
                title: "التصنيع: أوامر التشغيل والإنتاج الدوائي cGMP",
                desc: "إدارة خطوط الإنتاج، بدء صرف المواد (Dispensing Protocol)، المراجعة الدوائية، وإيداع المنتج التام."
            },
            {
                step: 15,
                url: "{{ asset('images/guide/shot15_sales_orders.png') }}",
                title: "المبيعات: مركز إدارة الطلبات وبوالص الشحن",
                desc: "جدول الطلبات، متابعة الحالات (جديد، قيد الشحن، تم التسليم)، والتحقق من سندات الفواتير الضريبية."
            },
            {
                step: 16,
                url: "{{ asset('images/guide/shot16_hr_dashboard.png') }}",
                title: "الموارد البشرية: لوحة قيادة الحضور والرواتب",
                desc: "متابعة نسب الحضور والانصراف، الورديات، مسير الرواتب الشهري، وطلبات الإجازات المعتمدة."
            },
            {
                step: 17,
                url: "{{ asset('images/guide/shot17_field_mr_visits.png') }}",
                title: "المندوبون: إدارة الزيارات الميدانية والتحقق GPS",
                desc: "سجل الزيارات الطبية، التحقق الجغرافي GPS، توثيق العينات الطبية وتتبع التغطية الشهرية للمناطق."
            },
            {
                step: 18,
                url: "{{ asset('images/guide/shot18_sales_crm_dashboard.png') }}",
                title: "علاقات العملاء: لوحة قيادة مسار الصفقات CRM",
                desc: "متابعة مسار الصفقات Kanban، تأهيل العملاء المحتملين، وقيمة الصفقات المتوقعة والمحققة للمراكز."
            },
            {
                step: 19,
                url: "{{ asset('images/guide/shot19_finance_chart_of_accounts.png') }}",
                title: "المالية: شجرة دليل الحسابات العام (COA)",
                desc: "إدارة الأصول والخصوم والمصروفات والإيرادات والمستويات المحاسبية الشجرية والقيود المزدوجة."
            },
            {
                step: 20,
                url: "{{ asset('images/guide/shot20_roles_rbac_security.png') }}",
                title: "الأمان: مصفوفة الأدوار والصلاحيات الدقيقة RBAC",
                desc: "إدارة الصلاحيات الدقيقة، إنشاء الأدوار، وعزل مهام المشرفين والمحاسبين والمندوبين بدقة تامة."
            }
        ];

        let currentLightboxIndex = 0;

        function openLightboxModal(url, title, desc, stepNum) {
            let foundIdx = guideGallery.findIndex(item => item.url === url || item.step === stepNum);
            currentLightboxIndex = (foundIdx !== -1) ? foundIdx : 0;
            renderLightboxContent();

            const modal = document.getElementById('guideLightbox');
            modal.style.display = 'flex';
            setTimeout(() => modal.classList.add('active'), 10);
            document.body.style.overflow = 'hidden';
        }

        function closeLightboxModal(event) {
            if (event && event.target && event.target.id !== 'guideLightbox' && !event.target.closest('button')) {
                return;
            }
            const modal = document.getElementById('guideLightbox');
            modal.classList.remove('active');
            setTimeout(() => {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }, 300);
        }

        function renderLightboxContent() {
            const item = guideGallery[currentLightboxIndex];
            if (!item) return;

            document.getElementById('lightboxImage').src = item.url;
            document.getElementById('lightboxTitle').textContent = item.title;
            document.getElementById('lightboxDescription').textContent = item.desc;
            document.getElementById('lightboxStepBadge').textContent = 'STEP ' + String(item.step).padStart(2, '0');
        }

        function nextLightboxImage() {
            currentLightboxIndex = (currentLightboxIndex + 1) % guideGallery.length;
            renderLightboxContent();
        }

        function prevLightboxImage() {
            currentLightboxIndex = (currentLightboxIndex - 1 + guideGallery.length) % guideGallery.length;
            renderLightboxContent();
        }

        // Keyboard Controls for Lightbox
        window.addEventListener('keydown', function(e) {
            const modal = document.getElementById('guideLightbox');
            if (!modal || !modal.classList.contains('active')) return;

            if (e.key === 'Escape') closeLightboxModal();
            if (e.key === 'ArrowRight') prevLightboxImage();
            if (e.key === 'ArrowLeft') nextLightboxImage();
        });

        // 1. Accordion Toggle for Main Modules
        function toggleModuleAccordion(moduleId) {
            const body = document.getElementById(moduleId + '-body');
            const chevron = document.getElementById(moduleId + '-chevron');
            if (!body) return;

            const isExpanded = body.style.display !== 'none';
            body.style.display = isExpanded ? 'none' : 'block';
            if (chevron) {
                chevron.style.transform = isExpanded ? 'rotate(0deg)' : 'rotate(180deg)';
            }
        }

        // 2. Sub-Screen Navigation inside Accordions
        function switchSubScreen(moduleId, screenId, btn) {
            const moduleContainer = document.getElementById(moduleId + '-body') || document.querySelector(`[data-section="${moduleId.replace('module-', '')}"]`);
            if (btn) {
                const navBar = btn.closest('.bz-subscreen-nav');
                if (navBar) {
                    navBar.querySelectorAll('.bz-subscreen-pill').forEach(p => p.classList.remove('active'));
                    btn.classList.add('active');
                }
            }

            if (moduleContainer) {
                moduleContainer.querySelectorAll('.bz-subscreen-view').forEach(v => v.style.display = 'none');
                const targetView = document.getElementById(screenId);
                if (targetView) {
                    targetView.style.display = 'block';
                    targetView.classList.remove('bz-fade-in');
                    void targetView.offsetWidth; // Trigger reflow
                    targetView.classList.add('bz-fade-in');
                }
            }
        }

        // 3. Global Expand All / Collapse All Toggle
        let allModulesExpanded = false;
        function toggleAllModules() {
            allModulesExpanded = !allModulesExpanded;
            const bodies = document.querySelectorAll('.bz-accordion-body');
            const chevrons = document.querySelectorAll('.bz-accordion-chevron');
            const btnText = document.getElementById('btnToggleAllText');
            const btnIcon = document.getElementById('btnToggleAllIcon');

            bodies.forEach(b => b.style.display = allModulesExpanded ? 'block' : 'none');
            chevrons.forEach(c => c.style.transform = allModulesExpanded ? 'rotate(180deg)' : 'rotate(0deg)');

            if (btnText) {
                btnText.textContent = allModulesExpanded ? '{{ app()->getLocale() === "ar" ? "طي كافة الأقسام (Collapse All)" : "Collapse All Sections" }}' : '{{ app()->getLocale() === "ar" ? "فتح كافة الأقسام (Expand All)" : "Expand All Sections" }}';
            }
            if (btnIcon) {
                btnIcon.className = allModulesExpanded ? 'fa-solid fa-arrows-up-to-line text-xs' : 'fa-solid fa-arrows-down-to-line text-xs';
            }
        }

        // 4. Quick Domain Filter Buttons
        function filterBySectionTag(tag, btn) {
            document.querySelectorAll('.bz-tab-pill').forEach(b => b.classList.remove('active'));
            if (btn) btn.classList.add('active');

            const modules = document.querySelectorAll('.bz-accordion-module');
            modules.forEach(m => {
                if (tag === 'all' || m.getAttribute('data-section') === tag) {
                    m.style.display = 'block';
                    // Auto-open if selected specifically
                    if (tag !== 'all') {
                        const body = m.querySelector('.bz-accordion-body');
                        const chevron = m.querySelector('.bz-accordion-chevron');
                        if (body) body.style.display = 'block';
                        if (chevron) chevron.style.transform = 'rotate(180deg)';
                    }
                } else {
                    m.style.display = 'none';
                }
            });
        }

        // 5. Search Filter Auto-Expands Matching Modules
        function filterGuideSections(query) {
            const q = query.trim().toLowerCase();
            const modules = document.querySelectorAll('.bz-accordion-module');

            if (!q) {
                modules.forEach(m => m.style.display = 'block');
                return;
            }

            modules.forEach(m => {
                const keywords = (m.getAttribute('data-keywords') || '').toLowerCase();
                const text = m.innerText.toLowerCase();
                if (keywords.includes(q) || text.includes(q)) {
                    m.style.display = 'block';
                    const body = m.querySelector('.bz-accordion-body');
                    const chevron = m.querySelector('.bz-accordion-chevron');
                    if (body) body.style.display = 'block';
                    if (chevron) chevron.style.transform = 'rotate(180deg)';
                } else {
                    m.style.display = 'none';
                }
            });
        }

        // 6. Live Search for Master Elements Catalog Table
        function filterCatalogTable(query) {
            const q = query.trim().toLowerCase();
            const rows = document.querySelectorAll('.bz-catalog-row');
            rows.forEach(row => {
                if (!q) {
                    row.style.display = '';
                    return;
                }
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(q) ? '' : 'none';
            });
        }

        // 7. Global URL Hash Handler for #logic-cycles & #system-catalog
        window.handleUrlHash = function() {
            const hash = window.location.hash;
            if (!hash) return;

            const targetEl = document.querySelector(hash);
            if (targetEl) {
                // If it was hidden by section filters, reset to 'all'
                const allPill = document.querySelector('.bz-tab-pill');
                if (targetEl.style.display === 'none' && allPill) {
                    filterBySectionTag('all', allPill);
                }

                // Smooth scroll to target anchor
                setTimeout(() => {
                    targetEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    targetEl.classList.add('ring-4', 'ring-[#0284c7]/40', 'transition-all');
                    setTimeout(() => targetEl.classList.remove('ring-4', 'ring-[#0284c7]/40'), 2500);
                }, 50);
            }
        };

        window.addEventListener('DOMContentLoaded', window.handleUrlHash);
        window.addEventListener('hashchange', window.handleUrlHash);
        window.handleUrlHash();
    </script>
</x-layouts.admin>
