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

        /* Tab / Section Button */
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

        /* Section Container Panel */
        .bz-section-panel {
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 1.5rem;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.04);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            overflow: hidden;
        }
        .dark .bz-section-panel {
            background: #081d2f;
            border-color: #1e3a52;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.5);
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

        /* Screen Card Thumbnail */
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
                        <span>{{ app()->getLocale() === 'ar' ? 'المرجع التشغيلي والمعماري الشامل لكافة دورات النظام (End-to-End Logic Cycles)' : 'Bluezone OS Complete Architecture & Logic Cycles Knowledge Hub' }}</span>
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    </div>

                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-slate-900 dark:text-white tracking-tight leading-tight">
                        {{ app()->getLocale() === 'ar' ? 'دليل أقسام ودورات عمل النظام بالكامل' : 'Complete System Sections & Workflow Logic Cycles' }}
                    </h1>

                    <p class="text-sm sm:text-base text-slate-600 dark:text-slate-300 font-medium leading-relaxed">
                        {{ app()->getLocale() === 'ar' 
                            ? 'توثيق معماري وميداني تفصيلي مقسم إلى أقسام متخصصة، حيث يحتوي كل قسم على دورة العمل (Cycle) الخاصة به بدون أي فجوات، متضمناً الجداول، عناصر التحكم، واللقطات الحية المكبرة مع دعم الوضعين الفاتح والداكن.' 
                            : 'Zero-gap modular documentation partitioned into dedicated enterprise sections, each holding its complete end-to-end business cycle, schema mapping, UI actions and zoomable live screenshots.' }}
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
        <!-- 2. LOGIC CYCLES & FLOWCHARTS NAVIGATOR (#logic-cycles) -->
        <!-- ========================================================================= -->
        <div id="logic-cycles" class="scroll-mt-24 space-y-4">
            <div class="bz-section-panel p-4 space-y-4">
            <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
                <!-- Search Filter Input -->
                <div class="relative flex-1">
                    <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none text-slate-400">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" id="guideSearchInput" oninput="filterGuideSections(this.value)" 
                           placeholder="{{ app()->getLocale() === 'ar' ? 'ابحث عن أي قسم أو دورة عمل (مثال: حجر صحي، أمر شراء، باقات تآزر، راتب، شجرة حسابات)...' : 'Filter sections and logic cycles (e.g. quarantine, PO, bundles, payroll, ledger)...' }}"
                           class="w-full pl-4 pr-10 py-2.5 text-sm rounded-xl bg-slate-50 dark:bg-[#061a2b] border border-slate-200 dark:border-[#1e3a52] text-slate-800 dark:text-white placeholder-slate-400 focus:outline-hidden focus:ring-2 focus:ring-[#0284c7]">
                </div>

                <div class="text-xs font-bold text-slate-500 dark:text-slate-400 flex items-center gap-1.5 self-center">
                    <i class="fa-solid fa-layer-group text-[#0284c7]"></i>
                    <span>{{ app()->getLocale() === 'ar' ? '11 قسم معماري متكامل' : '11 Enterprise Core Sections' }}</span>
                </div>
            </div>

            <!-- Dynamic Pill Selectors -->
            <div class="flex items-center gap-2 overflow-x-auto pb-1.5 scrollbar-none">
                <button type="button" class="bz-tab-pill active" onclick="filterBySectionTag('all', this)">
                    <i class="fa-solid fa-border-all"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'كافة الأقسام' : 'All Sections' }}</span>
                </button>
                <button type="button" class="bz-tab-pill" onclick="filterBySectionTag('products', this)">
                    <i class="fa-solid fa-box-open text-sky-500"></i>
                    <span>{{ app()->getLocale() === 'ar' ? '1. الكتالوج والمنتجات' : '1. Catalog & Products' }}</span>
                </button>
                <button type="button" class="bz-tab-pill" onclick="filterBySectionTag('purchasing', this)">
                    <i class="fa-solid fa-cart-shopping text-emerald-500"></i>
                    <span>{{ app()->getLocale() === 'ar' ? '2. المشتريات والموردين' : '2. Purchasing & Vendors' }}</span>
                </button>
                <button type="button" class="bz-tab-pill" onclick="filterBySectionTag('qc', this)">
                    <i class="fa-solid fa-snowflake text-cyan-500"></i>
                    <span>{{ app()->getLocale() === 'ar' ? '3. الحجر وضبط الجودة' : '3. Cold-Chain & QC' }}</span>
                </button>
                <button type="button" class="bz-tab-pill" onclick="filterBySectionTag('inventory', this)">
                    <i class="fa-solid fa-warehouse text-amber-500"></i>
                    <span>{{ app()->getLocale() === 'ar' ? '4. المخزون والتشغيلات' : '4. Inventory & Batches' }}</span>
                </button>
                <button type="button" class="bz-tab-pill" onclick="filterBySectionTag('manufacturing', this)">
                    <i class="fa-solid fa-industry text-purple-500"></i>
                    <span>{{ app()->getLocale() === 'ar' ? '5. التصنيع cGMP' : '5. Manufacturing' }}</span>
                </button>
                <button type="button" class="bz-tab-pill" onclick="filterBySectionTag('sales', this)">
                    <i class="fa-solid fa-truck-fast text-rose-500"></i>
                    <span>{{ app()->getLocale() === 'ar' ? '6. الطلبات والشحن' : '6. Orders & Shipping' }}</span>
                </button>
                <button type="button" class="bz-tab-pill" onclick="filterBySectionTag('finance', this)">
                    <i class="fa-solid fa-coins text-teal-500"></i>
                    <span>{{ app()->getLocale() === 'ar' ? '7. المالية والحسابات' : '7. Finance & Ledger' }}</span>
                </button>
                <button type="button" class="bz-tab-pill" onclick="filterBySectionTag('field-crm', this)">
                    <i class="fa-solid fa-user-doctor text-blue-500"></i>
                    <span>{{ app()->getLocale() === 'ar' ? '8. المندوبون والعيادات' : '8. Field MR & Clinics' }}</span>
                </button>
                <button type="button" class="bz-tab-pill" onclick="filterBySectionTag('sales-crm', this)">
                    <i class="fa-solid fa-handshake text-orange-500"></i>
                    <span>{{ app()->getLocale() === 'ar' ? '9. علاقات العملاء CRM' : '9. Sales Deals CRM' }}</span>
                </button>
                <button type="button" class="bz-tab-pill" onclick="filterBySectionTag('hr', this)">
                    <i class="fa-solid fa-users text-green-500"></i>
                    <span>{{ app()->getLocale() === 'ar' ? '10. الموارد والرواتب' : '10. HR & Payroll' }}</span>
                </button>
                <button type="button" class="bz-tab-pill" onclick="filterBySectionTag('security', this)">
                    <i class="fa-solid fa-shield-halved text-indigo-500"></i>
                    <span>{{ app()->getLocale() === 'ar' ? '11. السلايدر والأمان' : '11. Slider & Security' }}</span>
                </button>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- SECTION 1: CATALOG, PRODUCTS & CUSTOMIZER -->
        <!-- ========================================================================= -->
        <div class="bz-system-section space-y-6" data-section="products" data-keywords="products catalog customizer bundles pricing sku clinical ingredients مخصص أقسام باقات تآزر تسعير">
            <div class="bz-section-panel p-6 sm:p-8 space-y-8">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 dark:border-[#1e3a52] pb-6">
                    <div class="flex items-center gap-4">
                        <span class="w-13 h-13 rounded-2xl bg-gradient-to-br from-[#0A4F78] to-[#0284c7] text-white flex items-center justify-center text-2xl shadow-lg shadow-[#0A4F78]/25 flex-shrink-0">
                            <i class="fa-solid fa-box-open"></i>
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="badge text-[10px] font-black uppercase tracking-wider bg-sky-100 dark:bg-sky-500/20 text-sky-800 dark:text-sky-300 border border-sky-300 dark:border-sky-500/30 px-2 py-0.5 rounded-full">SECTION 01</span>
                                <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">
                                    {{ app()->getLocale() === 'ar' ? 'إدارة المنتجات، تخصيص الأقسام وباقات التآزر' : 'Product Lifecycle, Section Customizer & Bundles' }}
                                </h2>
                            </div>
                            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-medium mt-1">
                                {{ app()->getLocale() === 'ar' ? 'المسار: /admin/products — معالج الخطوات السبع، الأقسام الثمانية، الحفظ التلقائي للمسودات، وباقات التآزر الموحدة' : 'Route: /admin/products — 7-Step Floating Wizard, 8 Monograph Sections, Auto-Drafting & Synergistic Bundles' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary text-xs" target="_blank">
                            <i class="fa-solid fa-list text-xs"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'الكتالوج' : 'Catalog' }}</span>
                        </a>
                        <a href="{{ route('admin.products.create') }}" class="btn btn-primary text-xs" target="_blank">
                            <i class="fa-solid fa-plus text-xs"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'معالج منتج جديد' : 'New Product Wizard' }}</span>
                        </a>
                    </div>
                </div>

                <!-- Complete Product Cycle (Step-by-Step Flow) -->
                <div class="space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <i class="fa-solid fa-diagram-project text-[#0284c7]"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'دورة حياة المنتج ومعالج الخطوات السبع (Product Lifecycle & Wizard Cycle)' : 'Complete Product Logic Cycle' }}</span>
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bz-cycle-step">
                            <div class="flex items-center justify-between text-xs font-mono font-bold text-sky-600 dark:text-sky-400 mb-2">
                                <span>PHASE 01</span>
                                <i class="fa-solid fa-barcode"></i>
                            </div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white mb-1.5">الرمز والبيانات الأساسية</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                                توليد رمز SKU المعياري، مسار الـ SEO (الرابط الدائم)، التصنيف العلاجي، وحفظ المسودة تلقائياً عبر AJAX بمجرد كتابة أول حقل.
                            </p>
                        </div>

                        <div class="bz-cycle-step">
                            <div class="flex items-center justify-between text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400 mb-2">
                                <span>PHASE 02</span>
                                <i class="fa-solid fa-coins"></i>
                            </div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white mb-1.5">السياسة السعرية والضرائب</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                                تحديد سعر البيع والتخفيض، احتساب ضريبة القيمة المضافة ديناميكياً، وتكلفة الاستيراد لحساب هامش الربح المحقق في كل عبوة.
                            </p>
                        </div>

                        <div class="bz-cycle-step">
                            <div class="flex items-center justify-between text-xs font-mono font-bold text-amber-600 dark:text-amber-400 mb-2">
                                <span>PHASE 03</span>
                                <i class="fa-solid fa-sliders"></i>
                            </div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white mb-1.5">مخصص الأقسام والشاشات</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                                مفاتيح تحكم مستقلة للكمبيوتر والهاتف (Desktop vs Mobile)، تحديد حالة فتح الأكورديون، وتخصيص الأقسام الثمانية بالمتجر.
                            </p>
                        </div>

                        <div class="bz-cycle-step">
                            <div class="flex items-center justify-between text-xs font-mono font-bold text-purple-600 dark:text-purple-400 mb-2">
                                <span>PHASE 04</span>
                                <i class="fa-solid fa-cubes-stacked"></i>
                            </div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white mb-1.5">باقات التآزر والمخزون</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                                ربط المكملات التكميلية في باقة Synergistic موحدة بزر شراء جماعي، وربط الباركود وتعيين حدود إعادة الطلب بالمستودعات.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Screenshots Gallery for Section 1 -->
                <div class="space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <i class="fa-solid fa-camera text-sky-500"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'اللقطات الحية لمعالج المنتج وباقات التآزر (انقر للتكبير السلس)' : 'Live Product Wizard Screenshots' }}</span>
                    </h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                        <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot1_step1_core.png') }}', 'الخطوة 1: الرمز والتصنيف الأساسي', 'تحديد رمز SKU، الرابط الدائم، التصنيف العلاجي، وحفظ المسودة التلقائي.', 1)">
                            <img src="{{ asset('images/guide/shot1_step1_core.png') }}" alt="Step 1 SKU" class="w-full h-40 object-cover object-top" loading="lazy">
                            <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-2xl"></i></div>
                            <div class="p-2.5 bg-slate-50 dark:bg-[#061a2b] text-[11px] font-bold text-slate-800 dark:text-slate-200">الخطوة 1: SKU والمسودة</div>
                        </div>

                        <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot2_step2_content.png') }}', 'الخطوة 2: المحتوى ثنائي اللغة', 'كتابة الاسم التجاري والعلمي والملخص التسويقي باللغتين العربية والإنجليزية.', 2)">
                            <img src="{{ asset('images/guide/shot2_step2_content.png') }}" alt="Step 2 Content" class="w-full h-40 object-cover object-top" loading="lazy">
                            <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-2xl"></i></div>
                            <div class="p-2.5 bg-slate-50 dark:bg-[#061a2b] text-[11px] font-bold text-slate-800 dark:text-slate-200">الخطوة 2: المحتوى والوصف</div>
                        </div>

                        <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot3_step3_pricing.png') }}', 'الخطوة 3: السياسة السعرية والضرائب', 'تحديد أسعار البيع والتخفيض واحتساب الضريبة وهامش الربح.', 3)">
                            <img src="{{ asset('images/guide/shot3_step3_pricing.png') }}" alt="Step 3 Pricing" class="w-full h-40 object-cover object-top" loading="lazy">
                            <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-2xl"></i></div>
                            <div class="p-2.5 bg-slate-50 dark:bg-[#061a2b] text-[11px] font-bold text-slate-800 dark:text-slate-200">الخطوة 3: التسعير والضريبة</div>
                        </div>

                        <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot6_step6_customizer.png') }}', 'الخطوة 6: مخصص أقسام صفحة المنتج', 'التحكم المنفصل لظهور الأقسام على الكمبيوتر والموبايل وحالة فتح الأكورديون.', 6)">
                            <img src="{{ asset('images/guide/shot6_step6_customizer.png') }}" alt="Step 6 Customizer" class="w-full h-40 object-cover object-top" loading="lazy">
                            <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-2xl"></i></div>
                            <div class="p-2.5 bg-slate-50 dark:bg-[#061a2b] text-[11px] font-bold text-slate-800 dark:text-slate-200">الخطوة 6: مخصص الشاشات</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- SECTION 2: PURCHASING & SUPPLIERS -->
        <!-- ========================================================================= -->
        <div class="bz-system-section space-y-6" data-section="purchasing" data-keywords="purchasing suppliers vendors po quotations procurement مشتريات موردين أوامر شراء عروض أسعار">
            <div class="bz-section-panel p-6 sm:p-8 space-y-8">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 dark:border-[#1e3a52] pb-6">
                    <div class="flex items-center gap-4">
                        <span class="w-13 h-13 rounded-2xl bg-gradient-to-br from-emerald-600 to-teal-700 text-white flex items-center justify-center text-2xl shadow-lg shadow-emerald-600/25 flex-shrink-0">
                            <i class="fa-solid fa-cart-shopping"></i>
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="badge text-[10px] font-black uppercase tracking-wider bg-emerald-100 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-500/30 px-2 py-0.5 rounded-full">SECTION 02</span>
                                <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">
                                    {{ app()->getLocale() === 'ar' ? 'إدارة المشتريات، الموردين وعروض الأسعار' : 'Procurement, Supplier Lifecycle & Purchase Orders' }}
                                </h2>
                            </div>
                            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-medium mt-1">
                                {{ app()->getLocale() === 'ar' ? 'المسار: /admin/purchase-orders و /admin/suppliers — اعتماد الموردين cGMP، طلبات الشراء، والمطابقة الثلاثية 3-Way Matching' : 'Routes: /admin/purchase-orders & /admin/suppliers — cGMP Vendor Audits, PO Workflows & 3-Way Matching' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary text-xs" target="_blank">
                            <i class="fa-solid fa-truck-field text-xs"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'سجل الموردين' : 'Suppliers' }}</span>
                        </a>
                        <a href="{{ route('admin.purchase-orders.create') }}" class="btn btn-primary text-xs" target="_blank">
                            <i class="fa-solid fa-plus text-xs"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'أمر شراء جديد' : 'Create PO' }}</span>
                        </a>
                    </div>
                </div>

                <!-- Complete Purchasing Cycle -->
                <div class="space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <i class="fa-solid fa-diagram-project text-emerald-500"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'دورة المشتريات والمطابقة الثلاثية (Procurement & 3-Way Matching Cycle)' : 'Complete Purchasing Logic Cycle' }}</span>
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bz-cycle-step">
                            <div class="flex items-center justify-between text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400 mb-2">
                                <span>PHASE 01</span>
                                <i class="fa-solid fa-file-circle-check"></i>
                            </div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white mb-1.5">تأهيل واعتماد المورد</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                                التحقق من شهادات الـ ISO ومعايير التصنيع الدوائي، تعيين كود الدولة ISO2، وفحص فترات الائتمان وسجل التوريد السابق.
                            </p>
                        </div>

                        <div class="bz-cycle-step">
                            <div class="flex items-center justify-between text-xs font-mono font-bold text-sky-600 dark:text-sky-400 mb-2">
                                <span>PHASE 02</span>
                                <i class="fa-solid fa-calculator"></i>
                            </div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white mb-1.5">عروض الأسعار والمقارنة</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                                استدراج عروض الأسعار (RFQ)، المفاضلة بين أسعار المواد الخام، ومطابقة تكلفة الشحن والشروط اللوجستية Incoterms.
                            </p>
                        </div>

                        <div class="bz-cycle-step">
                            <div class="flex items-center justify-between text-xs font-mono font-bold text-amber-600 dark:text-amber-400 mb-2">
                                <span>PHASE 03</span>
                                <i class="fa-solid fa-file-signature"></i>
                            </div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white mb-1.5">إصدار أمر الشراء PO</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                                توليد أمر شراء رسمي بحالة Approved، إرساله للمورد، وتثبيت الأسعار والكميات المطلوبة وتاريخ التوريد الملزم.
                            </p>
                        </div>

                        <div class="bz-cycle-step">
                            <div class="flex items-center justify-between text-xs font-mono font-bold text-teal-600 dark:text-teal-400 mb-2">
                                <span>PHASE 04</span>
                                <i class="fa-solid fa-receipt"></i>
                            </div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white mb-1.5">المطابقة الثلاثية 3-Way Match</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                                مطابقة الكميات والأسعار بين (أمر الشراء PO + سند الاستلام الفعلي GRN + فاتورة المورد) قبل ترحيل القيد المحاسبي.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Screenshots Gallery for Section 2 -->
                <div class="space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <i class="fa-solid fa-camera text-emerald-500"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'اللقطات الحية لأوامر الشراء والموردين' : 'Purchasing Live Screenshots' }}</span>
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot11_purchases_po.png') }}', 'أوامر الشراء واعتماد الموردين', 'جدول أوامر الشراء، متابعة التوريد ومطابقة فواتير المواد الخام.', 11)">
                            <img src="{{ asset('images/guide/shot11_purchases_po.png') }}" alt="PO Index" class="w-full h-52 object-cover object-top" loading="lazy">
                            <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-2xl"></i></div>
                            <div class="p-3 bg-slate-50 dark:bg-[#061a2b] text-xs font-bold text-slate-800 dark:text-slate-200">سجل أوامر الشراء الرسمية المعتمدة (POs)</div>
                        </div>

                        <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot12_goods_receipts.png') }}', 'سندات استلام البضائع وفحص الأختام والحرارة', 'جدول استلام الشحنات المبردة، تسجيل مسجلات الحرارة، وإيداع التشغيلات في الحجر الصحي.', 12)">
                            <img src="{{ asset('images/guide/shot12_goods_receipts.png') }}" alt="GRN Index" class="w-full h-52 object-cover object-top" loading="lazy">
                            <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-2xl"></i></div>
                            <div class="p-3 bg-slate-50 dark:bg-[#061a2b] text-xs font-bold text-slate-800 dark:text-slate-200">سندات استلام البضائع وسلسلة التبريد (GRN)</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- SECTION 3: QC, COLD-CHAIN & QUARANTINE -->
        <!-- ========================================================================= -->
        <div class="bz-system-section space-y-6" data-section="qc" data-keywords="qc cold-chain quarantine inspection temperature coa حجر صحي فحص جودة سلسلة تبريد عينات">
            <div class="bz-section-panel p-6 sm:p-8 space-y-8">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 dark:border-[#1e3a52] pb-6">
                    <div class="flex items-center gap-4">
                        <span class="w-13 h-13 rounded-2xl bg-gradient-to-br from-cyan-600 to-blue-700 text-white flex items-center justify-center text-2xl shadow-lg shadow-cyan-600/25 flex-shrink-0">
                            <i class="fa-solid fa-snowflake"></i>
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="badge text-[10px] font-black uppercase tracking-wider bg-cyan-100 dark:bg-cyan-500/20 text-cyan-800 dark:text-cyan-300 border border-cyan-300 dark:border-cyan-500/30 px-2 py-0.5 rounded-full">SECTION 03</span>
                                <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">
                                    {{ app()->getLocale() === 'ar' ? 'استلام الشحنات، سلسلة التبريد والحجر الصحي وضبط الجودة' : 'Cold-Chain Logistics, Quarantine & QC Intake Lifecycle' }}
                                </h2>
                            </div>
                            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-medium mt-1">
                                {{ app()->getLocale() === 'ar' ? 'المسار: /admin/goods-receipts — توثيق مسجلات الحرارة، سحب العينات المخبرية، وإجراءات الإفراج الدوائي cGMP' : 'Route: /admin/goods-receipts — Temperature Data Loggers, Laboratory Sampling & cGMP Quarantine Release' }}
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('admin.goods-receipts.index') }}" class="btn btn-primary text-xs" target="_blank">
                        <i class="fa-solid fa-boxes-packing text-xs"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'سجل سندات الاستلام GRN' : 'View GRN Log' }}</span>
                    </a>
                </div>

                <!-- Complete QC & Quarantine Cycle -->
                <div class="space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <i class="fa-solid fa-diagram-project text-cyan-500"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'دورة الحجر الصحي وضبط الجودة (Cold-Chain & QC Intake Cycle)' : 'Complete QC Logic Cycle' }}</span>
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bz-cycle-step">
                            <div class="flex items-center justify-between text-xs font-mono font-bold text-cyan-600 dark:text-cyan-400 mb-2">
                                <span>PHASE 01</span>
                                <i class="fa-solid fa-temperature-arrow-down"></i>
                            </div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white mb-1.5">فحص سلسلة التبريد والأختام</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                                فحص سلامة أختام الحاويات، قراءة مسجلات الحرارة (Data Loggers)، والتأكد من نطاق التخزين المعتمد (2°C - 8°C أو 15°C - 25°C).
                            </p>
                        </div>

                        <div class="bz-cycle-step">
                            <div class="flex items-center justify-between text-xs font-mono font-bold text-amber-600 dark:text-amber-400 mb-2">
                                <span>PHASE 02</span>
                                <i class="fa-solid fa-dungeon"></i>
                            </div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white mb-1.5">الإيداع في مستودع الحجر الصحي</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                                إيداع الشحنة في موقع الحجر الصحي (Quarantine Location)، حظر صرفها كلياً ومنع ظهورها في المتجر أو خطوط الإنتاج.
                            </p>
                        </div>

                        <div class="bz-cycle-step">
                            <div class="flex items-center justify-between text-xs font-mono font-bold text-purple-600 dark:text-purple-400 mb-2">
                                <span>PHASE 03</span>
                                <i class="fa-solid fa-vial-circle-check"></i>
                            </div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white mb-1.5">الفحص المخبري ومطابقة COA</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                                سحب عينات عشوائية إحصائية، التحقق من شهادة التحليل (COA) واختبارات الميكروبيولوجي والمعادن الثقيلة والفعالية الحيوية.
                            </p>
                        </div>

                        <div class="bz-cycle-step">
                            <div class="flex items-center justify-between text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400 mb-2">
                                <span>PHASE 04</span>
                                <i class="fa-solid fa-box-archive"></i>
                            </div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white mb-1.5">قرار الجودة والإفراج التام</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                                اعتماد تقرير الفحص QC Pass، تحويل الكمية آلياً إلى المستودع المركزي Central WH، أو التكهين والإرجاع في حالة الرفض.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- SECTION 4: INVENTORY, BATCHES & DUAL-STOCK ALLOCATION -->
        <!-- ========================================================================= -->
        <div class="bz-system-section space-y-6" data-section="inventory" data-keywords="inventory batches stock transfer locations online central warehouse مخزون تشغيلات تحويل مستودعات صلاحية">
            <div class="bz-section-panel p-6 sm:p-8 space-y-8">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 dark:border-[#1e3a52] pb-6">
                    <div class="flex items-center gap-4">
                        <span class="w-13 h-13 rounded-2xl bg-gradient-to-br from-amber-500 to-amber-700 text-white flex items-center justify-center text-2xl shadow-lg shadow-amber-500/25 flex-shrink-0">
                            <i class="fa-solid fa-warehouse"></i>
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="badge text-[10px] font-black uppercase tracking-wider bg-amber-100 dark:bg-amber-500/20 text-amber-800 dark:text-amber-300 border border-amber-300 dark:border-amber-500/30 px-2 py-0.5 rounded-full">SECTION 04</span>
                                <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">
                                    {{ app()->getLocale() === 'ar' ? 'إدارة المخزون، المستودع المركزي والتوزيع المزدوج والتشغيلات' : 'Inventory Management, Central Depot & Dual-Stock Allocation' }}
                                </h2>
                            </div>
                            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-medium mt-1">
                                {{ app()->getLocale() === 'ar' ? 'المسار: /admin/batches و /admin/warehouses — الفصل الهندسي بين حصة المتجر الإلكتروني والمخزون الطبي المركزي وسندات التحويل' : 'Routes: /admin/batches & /admin/warehouses — Physical vs Storefront Stock Segregation & Inter-depot Transfers' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <a href="{{ route('admin.batches.index') }}" class="btn btn-secondary text-xs" target="_blank">
                            <i class="fa-solid fa-cubes text-xs"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'التشغيلات والصلاحية' : 'Batches & Expiry' }}</span>
                        </a>
                        <a href="{{ route('admin.warehouses.index') }}" class="btn btn-primary text-xs" target="_blank">
                            <i class="fa-solid fa-building-columns text-xs"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'المستودعات والمواقع' : 'Locations' }}</span>
                        </a>
                    </div>
                </div>

                <!-- Complete Inventory Cycle -->
                <div class="space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <i class="fa-solid fa-diagram-project text-amber-500"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'دورة تخصيص وحركة المخزون المزدوج (Dual-Stock & Allocation Cycle)' : 'Complete Inventory Logic Cycle' }}</span>
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bz-cycle-step">
                            <div class="flex items-center justify-between text-xs font-mono font-bold text-amber-600 dark:text-amber-400 mb-2">
                                <span>PHASE 01</span>
                                <i class="fa-solid fa-tags"></i>
                            </div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white mb-1.5">تسجيل التشغيلة والصلاحية</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                                تخصيص رقم تشغيلة فريد (Batch Code)، تاريخ الإنتاج والانتهاء، والربط مع رصيد المستودع لضمان قاعدة الوارد أولاً يصرف أولاً (FIFO).
                            </p>
                        </div>

                        <div class="bz-cycle-step">
                            <div class="flex items-center justify-between text-xs font-mono font-bold text-sky-600 dark:text-sky-400 mb-2">
                                <span>PHASE 02</span>
                                <i class="fa-solid fa-network-wired"></i>
                            </div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white mb-1.5">التخصيص المزدوج للحصص</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                                تقسيم الرصيد بدقة: حجز حصة المتجر الإلكتروني (Online Stock) لحماية تجربة العميل، وعزل حصة الصيدليات والمراكز الطبية بالمركزي.
                            </p>
                        </div>

                        <div class="bz-cycle-step">
                            <div class="flex items-center justify-between text-xs font-mono font-bold text-purple-600 dark:text-purple-400 mb-2">
                                <span>PHASE 03</span>
                                <i class="fa-solid fa-dolly"></i>
                            </div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white mb-1.5">سندات التحويل البيني</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                                إصدار أمر تحويل بين المستودعات (Inter-depot Transfer)، خصم الكمية من المصدر وإضافتها للوجهة مع توثيق اسم أمين المستودع.
                            </p>
                        </div>

                        <div class="bz-cycle-step">
                            <div class="flex items-center justify-between text-xs font-mono font-bold text-rose-600 dark:text-rose-400 mb-2">
                                <span>PHASE 04</span>
                                <i class="fa-solid fa-barcode"></i>
                            </div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white mb-1.5">الجرد ومطابقة الباركود</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                                مسح الباركود الفعلي دورياً، معالجة فروقات الجرد عبر سندات التسوية المخزنية المعتمدة مع الترحيل المحاسبي التلقائي.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- SECTION 5: MANUFACTURING & WORK ORDERS (cGMP) -->
        <!-- ========================================================================= -->
        <div class="bz-system-section space-y-6" data-section="manufacturing" data-keywords="manufacturing work orders bom formula cgmp production raw materials تصنيع أوامر إنتاج تشغيل مواد خام">
            <div class="bz-section-panel p-6 sm:p-8 space-y-8">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 dark:border-[#1e3a52] pb-6">
                    <div class="flex items-center gap-4">
                        <span class="w-13 h-13 rounded-2xl bg-gradient-to-br from-purple-600 to-indigo-800 text-white flex items-center justify-center text-2xl shadow-lg shadow-purple-600/25 flex-shrink-0">
                            <i class="fa-solid fa-industry"></i>
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="badge text-[10px] font-black uppercase tracking-wider bg-purple-100 dark:bg-purple-500/20 text-purple-800 dark:text-purple-300 border border-purple-300 dark:border-purple-500/30 px-2 py-0.5 rounded-full">SECTION 05</span>
                                <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">
                                    {{ app()->getLocale() === 'ar' ? 'التصنيع، أوامر التشغيل والإنتاج الدوائي cGMP' : 'cGMP Pharmaceutical Manufacturing & Production Work Orders' }}
                                </h2>
                            </div>
                            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-medium mt-1">
                                {{ app()->getLocale() === 'ar' ? 'المسار: /admin/work-orders — وصفات المواد الخام (BOM)، صرف المواد (Dispensing)، والمراجعة الدوائية والإنتاج التام' : 'Route: /admin/work-orders — Bill of Materials (BOM), Dispensing Protocols, QC In-process & Finished Goods Release' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <a href="{{ route('admin.work-orders.index') }}" class="btn btn-primary text-xs" target="_blank">
                            <i class="fa-solid fa-list-check text-xs"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'سجل أوامر التشغيل' : 'Work Orders' }}</span>
                        </a>
                    </div>
                </div>

                <!-- Complete Manufacturing Cycle -->
                <div class="space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <i class="fa-solid fa-diagram-project text-purple-500"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'دورة الإنتاج والتصنيع الدوائي (Pharmaceutical Manufacturing Cycle)' : 'Complete Production Logic Cycle' }}</span>
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bz-cycle-step">
                            <div class="flex items-center justify-between text-xs font-mono font-bold text-purple-600 dark:text-purple-400 mb-2">
                                <span>PHASE 01</span>
                                <i class="fa-solid fa-flask"></i>
                            </div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white mb-1.5">صيغة التشغيلة (BOM Formula)</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                                تحديد نسب المواد الفعالة والمكونات المساعدة الدقيقة، التحقق من توفر المواد الخام في مستودع الخامات قبل جدولة الإنتاج.
                            </p>
                        </div>

                        <div class="bz-cycle-step">
                            <div class="flex items-center justify-between text-xs font-mono font-bold text-indigo-600 dark:text-indigo-400 mb-2">
                                <span>PHASE 02</span>
                                <i class="fa-solid fa-scale-balanced"></i>
                            </div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white mb-1.5">صرف ووزن المواد (Dispensing)</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                                بدء بروتوكول الصرف الدوائي المزدوج (Double Check)، وزن المواد بدقة وتوثيق أرقام تشغيلات المواد الخام المستهلكة.
                            </p>
                        </div>

                        <div class="bz-cycle-step">
                            <div class="flex items-center justify-between text-xs font-mono font-bold text-amber-600 dark:text-amber-400 mb-2">
                                <span>PHASE 03</span>
                                <i class="fa-solid fa-gears"></i>
                            </div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white mb-1.5">مراحل الخلط والكبس والتعبئة</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                                تشغيل خطوط الإنتاج تحت ظروف الغرف النظيفة (Cleanrooms)، أخذ عينات أثناء التشغيل (In-process Checks) للوزن والصلابة.
                            </p>
                        </div>

                        <div class="bz-cycle-step">
                            <div class="flex items-center justify-between text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400 mb-2">
                                <span>PHASE 04</span>
                                <i class="fa-solid fa-box-open"></i>
                            </div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white mb-1.5">الإفراج وإيداع المنتج التام</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                                اعتماد تقرير الجودة النهائي للمنتج التام، توليد رقم التشغيلة الرسمي، وإيداع الكميات المنتجة في المستودع المركزي للبيع.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Screenshots Gallery for Section 5 -->
                <div class="space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <i class="fa-solid fa-camera text-purple-500"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'اللقطات الحية لأوامر التشغيل والتصنيع' : 'Manufacturing Live Screenshots' }}</span>
                    </h3>
                    <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot14_manufacturing_work_orders.png') }}', 'أوامر التشغيل والتصنيع الدوائي cGMP', 'جدول إدارة أوامر الإنتاج، حالة الصرف Dispensing، والمراجعة الدوائية والإنتاج التام.', 14)">
                        <img src="{{ asset('images/guide/shot14_manufacturing_work_orders.png') }}" alt="Work Orders Index" class="w-full h-56 object-cover object-top" loading="lazy">
                        <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-2xl"></i></div>
                        <div class="p-3 bg-slate-50 dark:bg-[#061a2b] text-xs font-bold text-slate-800 dark:text-slate-200">سجل أوامر الإنتاج ومراحل الصرف والمطابقة cGMP</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- SECTION 6: SALES, ORDERS, POS & FULFILLMENT -->
        <!-- ========================================================================= -->
        <div class="bz-system-section space-y-6" data-section="sales" data-keywords="sales orders pos checkout shipping waybill fulfillment مبيعات طلبات دفع فواتير شحن نقطة بيع">
            <div class="bz-section-panel p-6 sm:p-8 space-y-8">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 dark:border-[#1e3a52] pb-6">
                    <div class="flex items-center gap-4">
                        <span class="w-13 h-13 rounded-2xl bg-gradient-to-br from-rose-600 to-pink-700 text-white flex items-center justify-center text-2xl shadow-lg shadow-rose-600/25 flex-shrink-0">
                            <i class="fa-solid fa-truck-fast"></i>
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="badge text-[10px] font-black uppercase tracking-wider bg-rose-100 dark:bg-rose-500/20 text-rose-800 dark:text-rose-300 border border-rose-300 dark:border-rose-500/30 px-2 py-0.5 rounded-full">SECTION 06</span>
                                <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">
                                    {{ app()->getLocale() === 'ar' ? 'الطلبات، مبيعات المتجر، نقاط البيع والشحن' : 'Sales Operations, Orders, POS & Fulfillment Lifecycle' }}
                                </h2>
                            </div>
                            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-medium mt-1">
                                {{ app()->getLocale() === 'ar' ? 'المسار: /admin/orders — حجز المخزون الفوري، بوابات الدفع الإلكتروني، التجهيز والتغليف، وبوالص الشحن' : 'Route: /admin/orders — Live Stock Reservation, Payment Gateways, Picking/Packing & Shipping Waybills' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-primary text-xs" target="_blank">
                            <i class="fa-solid fa-cart-arrow-down text-xs"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'سجل الطلبات والمبيعات' : 'View Orders' }}</span>
                        </a>
                    </div>
                </div>

                <!-- Complete Sales Cycle -->
                <div class="space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <i class="fa-solid fa-diagram-project text-rose-500"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'دورة الطلب والتحصيل والشحن (Order-to-Cash Cycle)' : 'Complete Sales Logic Cycle' }}</span>
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bz-cycle-step">
                            <div class="flex items-center justify-between text-xs font-mono font-bold text-rose-600 dark:text-rose-400 mb-2">
                                <span>PHASE 01</span>
                                <i class="fa-solid fa-cart-plus"></i>
                            </div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white mb-1.5">إنشاء الطلب وحجز المخزون</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                                إنشاء الطلب من المتجر أو الـ POS، حجز فوري للمنتجات من رصيد المتجر الإلكتروني لمنع البيع الزائد (Overselling).
                            </p>
                        </div>

                        <div class="bz-cycle-step">
                            <div class="flex items-center justify-between text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400 mb-2">
                                <span>PHASE 02</span>
                                <i class="fa-solid fa-credit-card"></i>
                            </div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white mb-1.5">الدفع وإصدار الفاتورة الضريبية</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                                معالجة الدفع عبر البوابات الإلكترونية أو تثبيت خيار الدفع عند الاستلام (COD)، وتوليد الفاتورة الضريبية المعتمدة بـ QR Code.
                            </p>
                        </div>

                        <div class="bz-cycle-step">
                            <div class="flex items-center justify-between text-xs font-mono font-bold text-amber-600 dark:text-amber-400 mb-2">
                                <span>PHASE 03</span>
                                <i class="fa-solid fa-box"></i>
                            </div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white mb-1.5">التجهيز والتغليف الدوائي</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                                طباعة أمر التجهيز (Pick List)، فحص تاريخ الصلاحية وسلامة العبوات، وإرفاق ملصق الشحن المعياري للطرود.
                            </p>
                        </div>

                        <div class="bz-cycle-step">
                            <div class="flex items-center justify-between text-xs font-mono font-bold text-sky-600 dark:text-sky-400 mb-2">
                                <span>PHASE 04</span>
                                <i class="fa-solid fa-route"></i>
                            </div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white mb-1.5">بوليصة الشحن والتسليم الميداني</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                                تسليم الشحنة لشركة الشحن، توليد رابط التتبع المباشر للعميل عبر الرسائل والإيميل، وتحديث حالة الطلب إلى Delivered.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Screenshots Gallery for Section 6 -->
                <div class="space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <i class="fa-solid fa-camera text-rose-500"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'اللقطات الحية لإدارة الطلبات والمبيعات' : 'Orders Live Screenshots' }}</span>
                    </h3>
                    <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot15_sales_orders.png') }}', 'مركز إدارة الطلبات والمبيعات والشحن', 'جدول الطلبات، متابعة الحالات (جديد، قيد الشحن، تم التسليم)، والتحقق من سندات الفواتير.', 15)">
                        <img src="{{ asset('images/guide/shot15_sales_orders.png') }}" alt="Orders Index" class="w-full h-56 object-cover object-top" loading="lazy">
                        <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-2xl"></i></div>
                        <div class="p-3 bg-slate-50 dark:bg-[#061a2b] text-xs font-bold text-slate-800 dark:text-slate-200">لوحة إدارة ومتابعة الطلبات الإلكترونية والشحن الميداني</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- SECTION 7: FINANCE, GENERAL LEDGER & TREASURY -->
        <!-- ========================================================================= -->
        <div class="bz-system-section space-y-6" data-section="finance" data-keywords="finance ledger treasury accounts coa transactions مالية شجرة حسابات قيود يومية خزائن بنوك">
            <div class="bz-section-panel p-6 sm:p-8 space-y-8">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 dark:border-[#1e3a52] pb-6">
                    <div class="flex items-center gap-4">
                        <span class="w-13 h-13 rounded-2xl bg-gradient-to-br from-teal-600 to-emerald-800 text-white flex items-center justify-center text-2xl shadow-lg shadow-teal-600/25 flex-shrink-0">
                            <i class="fa-solid fa-coins"></i>
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="badge text-[10px] font-black uppercase tracking-wider bg-teal-100 dark:bg-teal-500/20 text-teal-800 dark:text-teal-300 border border-teal-300 dark:border-teal-500/30 px-2 py-0.5 rounded-full">SECTION 07</span>
                                <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">
                                    {{ app()->getLocale() === 'ar' ? 'المالية، شجرة الحسابات، قيود اليومية والخزائن' : 'Enterprise Finance, General Ledger, COA & Treasury Lifecycle' }}
                                </h2>
                            </div>
                            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-medium mt-1">
                                {{ app()->getLocale() === 'ar' ? 'المسار: /admin/financial-accounts و /admin/finance — دليل الحسابات التوافقي، قيود اليومية المزدوجة التلقائية، وسندات القبض والصرف' : 'Routes: /admin/financial-accounts & /admin/finance — Chart of Accounts, Automated Double-Entry Ledger & Cashflow' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <a href="{{ route('admin.finance.accounts.index') }}" class="btn btn-secondary text-xs" target="_blank">
                            <i class="fa-solid fa-sitemap text-xs"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'شجرة الحسابات COA' : 'COA Tree' }}</span>
                        </a>
                        <a href="{{ route('admin.finance.dashboard') }}" class="btn btn-primary text-xs" target="_blank">
                            <i class="fa-solid fa-chart-line text-xs"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'لوحة المالية' : 'Finance Dashboard' }}</span>
                        </a>
                    </div>
                </div>

                <!-- Complete Finance Cycle -->
                <div class="space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <i class="fa-solid fa-diagram-project text-teal-500"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'دورة الترحيل المالي والقيود المزدوجة (Automated Financial Accounting Cycle)' : 'Complete Finance Logic Cycle' }}</span>
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bz-cycle-step">
                            <div class="flex items-center justify-between text-xs font-mono font-bold text-teal-600 dark:text-teal-400 mb-2">
                                <span>PHASE 01</span>
                                <i class="fa-solid fa-sitemap"></i>
                            </div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white mb-1.5">هيكل دليل الحسابات (COA)</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                                تصنيف الحسابات الشجرية (الأصول، الخصوم، حقوق الملكية، الإيرادات، والمصروفات) وفق المعايير المحاسبية المعتمدة.
                            </p>
                        </div>

                        <div class="bz-cycle-step">
                            <div class="flex items-center justify-between text-xs font-mono font-bold text-sky-600 dark:text-sky-400 mb-2">
                                <span>PHASE 02</span>
                                <i class="fa-solid fa-money-bill-transfer"></i>
                            </div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white mb-1.5">القيود الآلية المزدوجة</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                                توليد قيود اليومية تلقائياً عند أي حركة (من حـ/ المخزون إلى حـ/ الموردين عند الشراء، ومن حـ/ الصندوق إلى حـ/ المبيعات عند الطلب).
                            </p>
                        </div>

                        <div class="bz-cycle-step">
                            <div class="flex items-center justify-between text-xs font-mono font-bold text-amber-600 dark:text-amber-400 mb-2">
                                <span>PHASE 03</span>
                                <i class="fa-solid fa-cash-register"></i>
                            </div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white mb-1.5">سندات القبض والصرف والخزائن</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                                توثيق المقبوضات النقدية والبنكية، سداد مستحقات الموردين، وإدارة العهد المالية والمصروفات التشغيلية.
                            </p>
                        </div>

                        <div class="bz-cycle-step">
                            <div class="flex items-center justify-between text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400 mb-2">
                                <span>PHASE 04</span>
                                <i class="fa-solid fa-file-invoice-dollar"></i>
                            </div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white mb-1.5">ميزان المراجعة والتقارير</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                                استخراج ميزان المراجعة اللحظي، قائمة الدخل ومتابعة الأرباح الإجمالية والصافية وهامش الربحية التشغيلي.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Screenshots Gallery for Section 7 -->
                <div class="space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <i class="fa-solid fa-camera text-teal-500"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'اللقطات الحية للمنظومة المالية وشجرة الحسابات' : 'Finance Live Screenshots' }}</span>
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot13_finance_dashboard.png') }}', 'لوحة تحكم المنظومة المالية والحسابات', 'متابعة الذمم المدينة والدائنة، إجمالي المبيعات، ومصروفات التشغيل وحركة الصناديق.', 13)">
                            <img src="{{ asset('images/guide/shot13_finance_dashboard.png') }}" alt="Finance Dashboard" class="w-full h-52 object-cover object-top" loading="lazy">
                            <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-2xl"></i></div>
                            <div class="p-3 bg-slate-50 dark:bg-[#061a2b] text-xs font-bold text-slate-800 dark:text-slate-200">لوحة قيادة المؤشرات المالية والتدفق النقدي</div>
                        </div>

                        <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot19_finance_chart_of_accounts.png') }}', 'شجرة دليل الحسابات المعياري (COA)', 'إدارة الأصول والخصوم والمصروفات والإيرادات والمستويات المحاسبية الشجرية.', 19)">
                            <img src="{{ asset('images/guide/shot19_finance_chart_of_accounts.png') }}" alt="COA Tree" class="w-full h-52 object-cover object-top" loading="lazy">
                            <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-2xl"></i></div>
                            <div class="p-3 bg-slate-50 dark:bg-[#061a2b] text-xs font-bold text-slate-800 dark:text-slate-200">دليل الحسابات الشجري العام (Chart of Accounts)</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- SECTION 8: FIELD CRM, MEDICAL REPS & DOCTORS -->
        <!-- ========================================================================= -->
        <div class="bz-system-section space-y-6" data-section="field-crm" data-keywords="field crm mr medical rep doctors clinics visits samples مندوبين زيارات عيادات أطباء عينات خطوط سير">
            <div class="bz-section-panel p-6 sm:p-8 space-y-8">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 dark:border-[#1e3a52] pb-6">
                    <div class="flex items-center gap-4">
                        <span class="w-13 h-13 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white flex items-center justify-center text-2xl shadow-lg shadow-blue-600/25 flex-shrink-0">
                            <i class="fa-solid fa-user-doctor"></i>
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="badge text-[10px] font-black uppercase tracking-wider bg-blue-100 dark:bg-blue-500/20 text-blue-800 dark:text-blue-300 border border-blue-300 dark:border-blue-500/30 px-2 py-0.5 rounded-full">SECTION 08</span>
                                <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">
                                    {{ app()->getLocale() === 'ar' ? 'المندوبون الميدانيون والزيارات والعيادات (Field MR CRM)' : 'Field Medical Representatives, Doctor Visits & Samples Lifecycle' }}
                                </h2>
                            </div>
                            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-medium mt-1">
                                {{ app()->getLocale() === 'ar' ? 'المسار: /admin/visits و /admin/mr — خطوط السير الجغرافية، تسجيل الزيارات بالبصمة الجغرافية GPS، وتوزيع العينات الدوائية' : 'Routes: /admin/visits & /admin/mr — Territory Planning, Geofenced GPS Check-ins & Clinical Sample Drops' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <a href="{{ route('admin.visits.index') }}" class="btn btn-primary text-xs" target="_blank">
                            <i class="fa-solid fa-map-location-dot text-xs"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'سجل الزيارات الميدانية' : 'Field Visits' }}</span>
                        </a>
                    </div>
                </div>

                <!-- Complete Field MR Cycle -->
                <div class="space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <i class="fa-solid fa-diagram-project text-blue-500"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'دورة الزيارات الطبية الميدانية للمندوبين (Medical Rep Field Visit Cycle)' : 'Complete Field CRM Logic Cycle' }}</span>
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bz-cycle-step">
                            <div class="flex items-center justify-between text-xs font-mono font-bold text-blue-600 dark:text-blue-400 mb-2">
                                <span>PHASE 01</span>
                                <i class="fa-solid fa-route"></i>
                            </div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white mb-1.5">تخطيط المسارات والمناطق</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                                تقسيم المناطق الجغرافية والمدن، وتعيين قائمة الأطباء والمراكز الطبية المستهدفة لكل مندوب دعاية طبية (MR).
                            </p>
                        </div>

                        <div class="bz-cycle-step">
                            <div class="flex items-center justify-between text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400 mb-2">
                                <span>PHASE 02</span>
                                <i class="fa-solid fa-location-crosshairs"></i>
                            </div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white mb-1.5">التحقق الجغرافي (GPS Geofence)</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                                منع تسجيل الزيارة إلا في حال تواجد المندوب داخل النطاق الجغرافي المحدد للعيادة أو المستشفى باستخدام إحداثيات GPS المباشرة.
                            </p>
                        </div>

                        <div class="bz-cycle-step">
                            <div class="flex items-center justify-between text-xs font-mono font-bold text-amber-600 dark:text-amber-400 mb-2">
                                <span>PHASE 03</span>
                                <i class="fa-solid fa-capsules"></i>
                            </div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white mb-1.5">تسليم العينات وتوثيق الفيدباك</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                                تسجيل العينات الدوائية المسلمة للطبيب (Sample Drops) مع خصمها التلقائي من عهدة المندوب، وتوثيق استجابة الطبيب للمنتج.
                            </p>
                        </div>

                        <div class="bz-cycle-step">
                            <div class="flex items-center justify-between text-xs font-mono font-bold text-purple-600 dark:text-purple-400 mb-2">
                                <span>PHASE 04</span>
                                <i class="fa-solid fa-chart-pie"></i>
                            </div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white mb-1.5">تحليل التغطية ومعدل التحويل</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                                قياس نسبة تغطية الزيارات مقابل التارجت الشهري، ومقارنة الزيارات بمعدل الوصفات الطبية والمبيعات الناتجة في كل منطقة.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Screenshots Gallery for Section 8 -->
                <div class="space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <i class="fa-solid fa-camera text-blue-500"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'اللقطات الحية للزيارات الميدانية والمندوبين' : 'Field CRM Live Screenshots' }}</span>
                    </h3>
                    <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot17_field_mr_visits.png') }}', 'إدارة الزيارات الميدانية ومندوبي الدعاية الطبية', 'سجل الزيارات، التحقق الجغرافي GPS، توثيق العينات الطبية وتتبع التغطية الشهرية.', 17)">
                        <img src="{{ asset('images/guide/shot17_field_mr_visits.png') }}" alt="Field Visits" class="w-full h-56 object-cover object-top" loading="lazy">
                        <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-2xl"></i></div>
                        <div class="p-3 bg-slate-50 dark:bg-[#061a2b] text-xs font-bold text-slate-800 dark:text-slate-200">سجل الزيارات الطبية والتحقق الميداني GPS</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- SECTION 9: SALES CRM & B2B PIPELINE -->
        <!-- ========================================================================= -->
        <div class="bz-system-section space-y-6" data-section="sales-crm" data-keywords="sales crm leads pipeline deals b2b opportunities عملاء محتملين صفقات عروض أسعار تفاوض">
            <div class="bz-section-panel p-6 sm:p-8 space-y-8">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 dark:border-[#1e3a52] pb-6">
                    <div class="flex items-center gap-4">
                        <span class="w-13 h-13 rounded-2xl bg-gradient-to-br from-orange-600 to-amber-700 text-white flex items-center justify-center text-2xl shadow-lg shadow-orange-600/25 flex-shrink-0">
                            <i class="fa-solid fa-handshake"></i>
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="badge text-[10px] font-black uppercase tracking-wider bg-orange-100 dark:bg-orange-500/20 text-orange-800 dark:text-orange-300 border border-orange-300 dark:border-orange-500/30 px-2 py-0.5 rounded-full">SECTION 09</span>
                                <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">
                                    {{ app()->getLocale() === 'ar' ? 'إدارة علاقات العملاء وفرص البيع والمراكز B2B' : 'Sales CRM Pipeline, Leads & B2B Medical Deal Cycle' }}
                                </h2>
                            </div>
                            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-medium mt-1">
                                {{ app()->getLocale() === 'ar' ? 'المسار: /admin/crm — إدارة مسارات الصفقات (Deals Pipeline)، تأهيل العملاء المحتملين، والعقود الآجلة للمراكز' : 'Route: /admin/crm — Kanban Deals Pipeline, Lead Qualification & Institutional Medical Contracts' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <a href="{{ route('admin.crm.dashboard') }}" class="btn btn-primary text-xs" target="_blank">
                            <i class="fa-solid fa-chart-column text-xs"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'لوحة قيادة الـ CRM' : 'CRM Dashboard' }}</span>
                        </a>
                    </div>
                </div>

                <!-- Complete Sales CRM Cycle -->
                <div class="space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <i class="fa-solid fa-diagram-project text-orange-500"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'دورة مسار الصفقات وإغلاق العقود (B2B Deals Pipeline Cycle)' : 'Complete CRM Logic Cycle' }}</span>
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bz-cycle-step">
                            <div class="flex items-center justify-between text-xs font-mono font-bold text-orange-600 dark:text-orange-400 mb-2">
                                <span>PHASE 01</span>
                                <i class="fa-solid fa-filter"></i>
                            </div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white mb-1.5">التقاط وتأهيل العميل (Lead)</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                                تسجيل بيانات المستشفيات والمراكز الطبية وسلاسل الصيدليات، وتحديد مستوى الاحتياج والملاءة الائتمانية.
                            </p>
                        </div>

                        <div class="bz-cycle-step">
                            <div class="flex items-center justify-between text-xs font-mono font-bold text-sky-600 dark:text-sky-400 mb-2">
                                <span>PHASE 02</span>
                                <i class="fa-solid fa-comments-dollar"></i>
                            </div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white mb-1.5">تقديم العرض المالي والتفاوض</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                                إنشاء عرض سعر رسمي (Quotation)، تحديد شرائح الخصم الكمي، والتفاوض على فترات السداد والتوريد الدوري.
                            </p>
                        </div>

                        <div class="bz-cycle-step">
                            <div class="flex items-center justify-between text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400 mb-2">
                                <span>PHASE 03</span>
                                <i class="fa-solid fa-trophy"></i>
                            </div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white mb-1.5">إغلاق الصفقة (Deal Won)</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                                تحويل الصفقة لحالة Won، وتوليد عقد مؤسسي رسمي وحساب مديونية معتمد برقم ضريبي ورصيد ائتماني.
                            </p>
                        </div>

                        <div class="bz-cycle-step">
                            <div class="flex items-center justify-between text-xs font-mono font-bold text-indigo-600 dark:text-indigo-400 mb-2">
                                <span>PHASE 04</span>
                                <i class="fa-solid fa-arrows-rotate"></i>
                            </div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white mb-1.5">التوريد والتحصيل المستمر</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                                جدولة أوامر الشراء الآلية من العميل، متابعة فترات الاستحقاق، وإصدار كشوف الحساب الدورية والتحصيل.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Screenshots Gallery for Section 9 -->
                <div class="space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <i class="fa-solid fa-camera text-orange-500"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'اللقطات الحية لمنظومة علاقات العملاء CRM' : 'CRM Live Screenshots' }}</span>
                    </h3>
                    <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot18_sales_crm_dashboard.png') }}', 'لوحة تحكم إدارة علاقات العملاء والصفقات', 'متابعة مسار الصفقات Kanban، تأهيل العملاء المحتملين، وقيمة الصفقات المتوقعة والمحققة.', 18)">
                        <img src="{{ asset('images/guide/shot18_sales_crm_dashboard.png') }}" alt="CRM Dashboard" class="w-full h-56 object-cover object-top" loading="lazy">
                        <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-2xl"></i></div>
                        <div class="p-3 bg-slate-50 dark:bg-[#061a2b] text-xs font-bold text-slate-800 dark:text-slate-200">لوحة تحكم علاقات العملاء ومسار الصفقات Kanban</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- SECTION 10: HR, ATTENDANCE & PAYROLL -->
        <!-- ========================================================================= -->
        <div class="bz-system-section space-y-6" data-section="hr" data-keywords="hr payroll attendance employees leaves shifts موارد بشرية رواتب حضور بصمة إجازات ورديات مسير">
            <div class="bz-section-panel p-6 sm:p-8 space-y-8">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 dark:border-[#1e3a52] pb-6">
                    <div class="flex items-center gap-4">
                        <span class="w-13 h-13 rounded-2xl bg-gradient-to-br from-green-600 to-emerald-800 text-white flex items-center justify-center text-2xl shadow-lg shadow-green-600/25 flex-shrink-0">
                            <i class="fa-solid fa-users"></i>
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="badge text-[10px] font-black uppercase tracking-wider bg-green-100 dark:bg-green-500/20 text-green-800 dark:text-green-300 border border-green-300 dark:border-green-500/30 px-2 py-0.5 rounded-full">SECTION 10</span>
                                <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">
                                    {{ app()->getLocale() === 'ar' ? 'الموارد البشرية، بصمة الحضور، الإجازات والرواتب' : 'Human Resources, Biometric Attendance & Automated Payroll Lifecycle' }}
                                </h2>
                            </div>
                            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-medium mt-1">
                                {{ app()->getLocale() === 'ar' ? 'المسار: /admin/hr — تسجيل الحضور الذاتي بالبصمة الحية، تسوية الإجازات والورديات، ومسير الرواتب الآلي' : 'Routes: /admin/hr — Live Punch-in Biometrics, Shift Schedules, Leave Balances & Automated Payroll Batches' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <a href="{{ route('admin.hr.employees.index') }}" class="btn btn-secondary text-xs" target="_blank">
                            <i class="fa-solid fa-id-badge text-xs"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'سجل الموظفين' : 'Employees' }}</span>
                        </a>
                        <a href="{{ route('admin.hr.dashboard') }}" class="btn btn-primary text-xs" target="_blank">
                            <i class="fa-solid fa-user-clock text-xs"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'لوحة الـ HR' : 'HR Dashboard' }}</span>
                        </a>
                    </div>
                </div>

                <!-- Complete HR Cycle -->
                <div class="space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <i class="fa-solid fa-diagram-project text-green-500"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'دورة الحضور ومسير الرواتب الآلي (Attendance & Payroll Cycle)' : 'Complete HR Logic Cycle' }}</span>
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bz-cycle-step">
                            <div class="flex items-center justify-between text-xs font-mono font-bold text-green-600 dark:text-green-400 mb-2">
                                <span>PHASE 01</span>
                                <i class="fa-solid fa-fingerprint"></i>
                            </div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white mb-1.5">تسجيل الحضور بالبصمة الحية</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                                تسجيل الحضور والانصراف اللحظي (Self Check-in / Punch-in) مع مطابقة النطاق الجغرافي وساعات الوردية المعتمدة.
                            </p>
                        </div>

                        <div class="bz-cycle-step">
                            <div class="flex items-center justify-between text-xs font-mono font-bold text-amber-600 dark:text-amber-400 mb-2">
                                <span>PHASE 02</span>
                                <i class="fa-solid fa-clock"></i>
                            </div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white mb-1.5">حساب التأخير والإضافي آلياً</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                                معالجة دقائق التأخير وفترات السماح، واحتساب ساعات العمل الإضافي وفق القواعد التنظيمية المعتمدة في النظام.
                            </p>
                        </div>

                        <div class="bz-cycle-step">
                            <div class="flex items-center justify-between text-xs font-mono font-bold text-sky-600 dark:text-sky-400 mb-2">
                                <span>PHASE 03</span>
                                <i class="fa-solid fa-calendar-check"></i>
                            </div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white mb-1.5">تسوية الإجازات والأذونات</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                                تقديم وموافقة طلبات الإجازات (الاعتيادية، المرضية، العارضة) مع الخصم التلقائي من الرصيد السنوي للموظف.
                            </p>
                        </div>

                        <div class="bz-cycle-step">
                            <div class="flex items-center justify-between text-xs font-mono font-bold text-teal-600 dark:text-teal-400 mb-2">
                                <span>PHASE 04</span>
                                <i class="fa-solid fa-file-invoice-dollar"></i>
                            </div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white mb-1.5">مسير الرواتب وترحيل القيود</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                                توليد مسير الرواتب الشهري، احتساب الاستقطاعات والبدلات، وتوليد قيد استحقاق الرواتب تلقائياً في دليل الحسابات.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Screenshots Gallery for Section 10 -->
                <div class="space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <i class="fa-solid fa-camera text-green-500"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'اللقطات الحية لمنظومة الموارد البشرية' : 'HR Live Screenshots' }}</span>
                    </h3>
                    <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot16_hr_dashboard.png') }}', 'لوحة تحكم الموارد البشرية والرواتب وبصمة الحضور', 'متابعة نسب الحضور والانصراف، الورديات، مسير الرواتب الشهري، وطلبات الإجازات.', 16)">
                        <img src="{{ asset('images/guide/shot16_hr_dashboard.png') }}" alt="HR Dashboard" class="w-full h-56 object-cover object-top" loading="lazy">
                        <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-2xl"></i></div>
                        <div class="p-3 bg-slate-50 dark:bg-[#061a2b] text-xs font-bold text-slate-800 dark:text-slate-200">لوحة قيادة الموارد البشرية وسجل الحضور والرواتب</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- SECTION 11: HERO SLIDER, SCIENCE & RBAC SECURITY -->
        <!-- ========================================================================= -->
        <div class="bz-system-section space-y-6" data-section="security" data-keywords="slider hero science rbac security permissions roles سلايدر محتوى أبحاث أدوار صلاحيات أمان">
            <div class="bz-section-panel p-6 sm:p-8 space-y-8">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 dark:border-[#1e3a52] pb-6">
                    <div class="flex items-center gap-4">
                        <span class="w-13 h-13 rounded-2xl bg-gradient-to-br from-indigo-600 to-purple-800 text-white flex items-center justify-center text-2xl shadow-lg shadow-indigo-600/25 flex-shrink-0">
                            <i class="fa-solid fa-shield-halved"></i>
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="badge text-[10px] font-black uppercase tracking-wider bg-indigo-100 dark:bg-indigo-500/20 text-indigo-800 dark:text-indigo-300 border border-indigo-300 dark:border-indigo-500/30 px-2 py-0.5 rounded-full">SECTION 11</span>
                                <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">
                                    {{ app()->getLocale() === 'ar' ? 'سلايدر الواجهة، المحتوى العلمي والصلاحيات والأمان' : 'Hero Slider Studio, Science Monographs & RBAC Governance' }}
                                </h2>
                            </div>
                            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-medium mt-1">
                                {{ app()->getLocale() === 'ar' ? 'المسار: /admin/hero-slider و /admin/roles — استوديو محاكاة الشرائح، الأبحاث السريرية، ومصفوفة الصلاحيات الدقيقة' : 'Routes: /admin/hero-slider & /admin/roles — Live Slide Simulator, Monograph Studies & RBAC Permission Matrix' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <a href="{{ route('admin.hero-slides.index') }}" class="btn btn-secondary text-xs" target="_blank">
                            <i class="fa-solid fa-images text-xs"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'استوديو السلايدر' : 'Slider Studio' }}</span>
                        </a>
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-primary text-xs" target="_blank">
                            <i class="fa-solid fa-user-shield text-xs"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'الأدوار والصلاحيات' : 'Roles & RBAC' }}</span>
                        </a>
                    </div>
                </div>

                <!-- Complete Governance Cycle -->
                <div class="space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <i class="fa-solid fa-diagram-project text-indigo-500"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'دورة حوكمة المحتوى والصلاحيات (Content & Security Governance Cycle)' : 'Complete Security Logic Cycle' }}</span>
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bz-cycle-step">
                            <div class="flex items-center justify-between text-xs font-mono font-bold text-indigo-600 dark:text-indigo-400 mb-2">
                                <span>PHASE 01</span>
                                <i class="fa-solid fa-sliders"></i>
                            </div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white mb-1.5">استوديو ومحاكي السلايدر</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                                تخصيص شرائح العرض الرئيسية، ضبط زمن الحركة التلقائي (Delay 5500ms)، وتأثيرات التوهج اللوني مع المحاكاة الحية.
                            </p>
                        </div>

                        <div class="bz-cycle-step">
                            <div class="flex items-center justify-between text-xs font-mono font-bold text-sky-600 dark:text-sky-400 mb-2">
                                <span>PHASE 02</span>
                                <i class="fa-solid fa-book-medical"></i>
                            </div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white mb-1.5">نشر الأبحاث السريرية</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                                توثيق الدراسات الطبية للمنتجات، المراجع العلمية الدولية، وربطها التلقائي بصفحات الكتالوج لإثبات الجدوى الطبية.
                            </p>
                        </div>

                        <div class="bz-cycle-step">
                            <div class="flex items-center justify-between text-xs font-mono font-bold text-amber-600 dark:text-amber-400 mb-2">
                                <span>PHASE 03</span>
                                <i class="fa-solid fa-user-lock"></i>
                            </div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white mb-1.5">مصفوفة الصلاحيات (RBAC)</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                                عزل الصلاحيات بدقة متناهية (مشاهدة، إنشاء، تعديل، حذف، واعتماد مالي) لكل دور وظيفي لمنع أي تجاوزات تشغيلية.
                            </p>
                        </div>

                        <div class="bz-cycle-step">
                            <div class="flex items-center justify-between text-xs font-mono font-bold text-rose-600 dark:text-rose-400 mb-2">
                                <span>PHASE 04</span>
                                <i class="fa-solid fa-shield-virus"></i>
                            </div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white mb-1.5">سجل التدقيق والأمان الحصين</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                                تسجيل كل حركة إدارية في سجل التدقيق (Audit Trail) مع توثيق الـ IP ووقت الإجراء، وحماية الجلسات ضد الثغرات.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Screenshots Gallery for Section 11 -->
                <div class="space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <i class="fa-solid fa-camera text-indigo-500"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'اللقطات الحية للسلايدر والصلاحيات' : 'Slider & Security Live Screenshots' }}</span>
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot9_hero_slider_overview.png') }}', 'مركز قيادة سلايدر الواجهة الرئيسي', 'التحكم في زمن الحركة التلقائي (Delay 5500ms)، تشغيل السلايدر، وإعادة الترتيب المباشر بالسحب والإفلات.', 9)">
                            <img src="{{ asset('images/guide/shot9_hero_slider_overview.png') }}" alt="Slider Overview" class="w-full h-44 object-cover object-top" loading="lazy">
                            <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-2xl"></i></div>
                            <div class="p-2.5 bg-slate-50 dark:bg-[#061a2b] text-[11px] font-bold text-slate-800 dark:text-slate-200">سلايدر الواجهة ومحرك الحركة</div>
                        </div>

                        <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot10_hero_slide_modal.png') }}', 'محرر الشريحة التفاعلي ولوحة المحاكاة الحية', 'ربط المنتج التلقائي، ضبط نصوص التوهج التدرجي، وتنسيق أزرار الطلب مع معاينة حية للشريحة.', 10)">
                            <img src="{{ asset('images/guide/shot10_hero_slide_modal.png') }}" alt="Slide Modal" class="w-full h-44 object-cover object-top" loading="lazy">
                            <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-2xl"></i></div>
                            <div class="p-2.5 bg-slate-50 dark:bg-[#061a2b] text-[11px] font-bold text-slate-800 dark:text-slate-200">محرر الشريحة والمحاكاة الحية</div>
                        </div>

                        <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot20_roles_rbac_security.png') }}', 'مصفوفة الأدوار والصلاحيات والأمان (RBAC)', 'إدارة الصلاحيات الدقيقة، إنشاء الأدوار، وعزل مهام المشرفين والمحاسبين والمندوبين.', 20)">
                            <img src="{{ asset('images/guide/shot20_roles_rbac_security.png') }}" alt="Roles RBAC" class="w-full h-44 object-cover object-top" loading="lazy">
                            <div class="bz-screen-zoom-overlay"><i class="fa-solid fa-magnifying-glass-plus text-white text-2xl"></i></div>
                            <div class="p-2.5 bg-slate-50 dark:bg-[#061a2b] text-[11px] font-bold text-slate-800 dark:text-slate-200">مصفوفة الصلاحيات والأدوار RBAC</div>
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
            <div class="bz-section-panel p-6 sm:p-8 space-y-6 border-l-4 border-l-amber-500">
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
                                    /admin/financial-accounts<br>
                                    <span class="text-slate-400">Route: admin.financial-accounts.index</span>
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
                                    <span class="text-slate-400">Routes: admin.hero-slider.index, admin.roles.index</span>
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
    <!-- 5. LIGHTBOX & DYNAMIC FILTER CONTROLLER SCRIPT -->
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

        // Filter Sections by Tag Pill
        function filterBySectionTag(tag, btn) {
            document.querySelectorAll('.bz-tab-pill').forEach(b => b.classList.remove('active'));
            if (btn) btn.classList.add('active');

            const sections = document.querySelectorAll('.bz-system-section');
            sections.forEach(sec => {
                if (tag === 'all' || sec.getAttribute('data-section') === tag) {
                    sec.style.display = 'block';
                } else {
                    sec.style.display = 'none';
                }
            });
        }

        // Live Search Input Filter
        function filterGuideSections(query) {
            const q = query.trim().toLowerCase();
            const sections = document.querySelectorAll('.bz-system-section');

            if (!q) {
                sections.forEach(sec => sec.style.display = 'block');
                return;
            }

            sections.forEach(sec => {
                const keywords = (sec.getAttribute('data-keywords') || '').toLowerCase();
                const text = sec.innerText.toLowerCase();
                if (keywords.includes(q) || text.includes(q)) {
                    sec.style.display = 'block';
                } else {
                    sec.style.display = 'none';
                }
            });
        }

        // Live Search for Master Elements Catalog Table
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

        // Global Dynamic Hash Handler for #logic-cycles & #system-catalog
        window.handleUrlHash = function() {
            const hash = window.location.hash;
            if (!hash) return;

            const targetEl = document.querySelector(hash);
            if (targetEl) {
                // If it was hidden by section tag filters, reset to 'all'
                const allPill = document.querySelector('.bz-tab-pill');
                if (targetEl.style.display === 'none' && allPill) {
                    filterBySectionTag('all', allPill);
                }

                // Smooth scroll to target anchor
                setTimeout(() => {
                    targetEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    // Highlight with pulse ring
                    targetEl.classList.add('ring-4', 'ring-[#0284c7]/40', 'transition-all');
                    setTimeout(() => targetEl.classList.remove('ring-4', 'ring-[#0284c7]/40'), 2500);
                }, 50);
            }
        };

        window.addEventListener('DOMContentLoaded', window.handleUrlHash);
        window.addEventListener('hashchange', window.handleUrlHash);
        // Also run immediately on execution
        window.handleUrlHash();
    </script>
</x-layouts.admin>
