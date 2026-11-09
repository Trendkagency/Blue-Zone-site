<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'مركز الدعم والدليل الشامل للنظام' : 'System Knowledge Hub & Support Guide'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'المرجع المعماري والتشغيلي المكتمل لكافة دورات العمل، الشاشات، والمنطق المحاسبي والمخزني في منصة Bluezone' : 'Comprehensive Architectural & Operational Knowledge Base for Bluezone Bioceuticals OS'"
    :breadcrumbs="[
        (app()->getLocale() === 'ar' ? 'لوحة التحكم' : 'Dashboard') => route('admin.dashboard'),
        (app()->getLocale() === 'ar' ? 'الدعم والدليل' : 'Support & Guide') => route('admin.guide.index')
    ]"
>
    <!-- ========================================================================= -->
    <!-- EXECUTIVE DUAL-THEME STYLES (WCAG AAA DARK & LIGHT COMPLIANT) -->
    <!-- ========================================================================= -->
    <style>
        /* Base Container */
        .bz-guide-shell {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Cairo", sans-serif;
            transition: all 0.25s ease;
        }

        /* Executive Header Banner */
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

        /* Interactive Navigation Tab Button */
        .bz-guide-tab-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.65rem;
            padding: 0.75rem 1.25rem;
            border-radius: 1rem;
            font-size: 0.85rem;
            font-weight: 700;
            color: #334155;
            background: #ffffff;
            border: 1.5px solid #cbd5e1;
            white-space: nowrap;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }
        .dark .bz-guide-tab-btn {
            color: #cbd5e1;
            background: #081d2f;
            border-color: #1e3a52;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
        }
        .bz-guide-tab-btn:hover {
            color: #0369a1;
            border-color: #0284c7;
            background: #f8fafc;
            transform: translateY(-2px);
        }
        .dark .bz-guide-tab-btn:hover {
            color: #ffffff;
            border-color: #38bdf8;
            background: #0c273e;
            transform: translateY(-2px);
        }
        .bz-guide-tab-btn.active-tab {
            background: linear-gradient(135deg, #0A4F78 0%, #0284c7 100%) !important;
            color: #ffffff !important;
            border-color: #0A4F78 !important;
            box-shadow: 0 6px 20px -2px rgba(2, 132, 199, 0.4) !important;
        }
        .bz-guide-tab-btn.active-tab i {
            color: #7dd3fc !important;
        }

        /* Modular Section Cards */
        .bz-guide-panel {
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 1.5rem;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.04);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .dark .bz-guide-panel {
            background: #081d2f;
            border-color: #1e3a52;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.5);
        }

        /* Flowchart Node Cards with Zero Text Overlaps */
        .bz-flow-node {
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 1.15rem;
            padding: 1.15rem;
            transition: all 0.25s ease;
        }
        .dark .bz-flow-node {
            background: #061a2b;
            border-color: #15395b;
        }
        .bz-flow-node:hover {
            border-color: #0284c7;
            background: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px -4px rgba(2, 132, 199, 0.12);
        }
        .dark .bz-flow-node:hover {
            border-color: #38bdf8;
            background: #0a253d;
            box-shadow: 0 8px 25px -4px rgba(0, 0, 0, 0.6);
        }

        /* Gallery Screen Card with Apple-Style Zoom Action */
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
            background: rgba(8, 29, 47, 0.65);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: all 0.25s ease;
        }

        /* Fullscreen Smooth Lightbox Modal */
        #guideLightbox {
            position: fixed;
            inset: 0;
            z-index: 99999999;
            display: none;
            align-items: center;
            justify-content: center;
            background: rgba(2, 18, 30, 0.88);
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

    <div class="bz-guide-shell space-y-8 pb-20">
        
        <!-- ========================================================================= -->
        <!-- 1. EXECUTIVE HERO COMMAND BANNER -->
        <!-- ========================================================================= -->
        <div class="bz-guide-hero relative overflow-hidden rounded-3xl p-6 sm:p-8 transition-colors">
            <!-- Decorative Accent Elements -->
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-sky-400/15 dark:bg-sky-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-emerald-400/15 dark:bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
                <div class="space-y-3 max-w-3xl">
                    <div class="inline-flex items-center gap-2.5 px-3.5 py-1.5 rounded-full text-xs font-bold uppercase tracking-wide bg-sky-100 dark:bg-sky-500/20 text-sky-800 dark:text-sky-300 border border-sky-300 dark:border-sky-500/30">
                        <i class="fa-solid fa-compass-drafting"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'المرجع التشغيلي والمعماري المعتمد لـ Bluezone' : 'Official Bluezone OS Architecture & Operations' }}</span>
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    </div>

                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-slate-900 dark:text-white tracking-tight leading-tight">
                        {{ app()->getLocale() === 'ar' ? 'دليل النظام والتوثيق التشغيلي الشامل' : 'System Knowledge Hub & Support Guide' }}
                    </h1>

                    <p class="text-sm sm:text-base text-slate-600 dark:text-slate-300 font-medium leading-relaxed">
                        {{ app()->getLocale() === 'ar' 
                            ? 'شرح معماري وتطبيقي دقيق بدون أي فجوات يشمل كافة دورات العمل (Logic Cycles)، الشاشات، عناصر التحكم، والقيود المحاسبية والمخزنية مع لقطات حية وتكبير سلس متوافق كلياً مع الوضعين الداكن والفاتح.' 
                            : 'Exhaustive zero-gap enterprise documentation covering all business cycles, screens, inputs, accounting directives, and inventory movements with live smooth visual lightbox in Dark and Light modes.' }}
                    </p>
                </div>

                <!-- Live Metrics Summary Pills -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 w-full lg:w-auto">
                    <div class="p-3.5 rounded-2xl bg-white/80 dark:bg-[#061a2b]/80 border border-slate-200 dark:border-[#1e3a52] text-center shadow-xs">
                        <div class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-0.5">{{ app()->getLocale() === 'ar' ? 'المنتجات' : 'Products' }}</div>
                        <div class="text-xl font-black text-slate-900 dark:text-sky-400 font-mono">{{ $systemMetrics['products_count'] }}</div>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-white/80 dark:bg-[#061a2b]/80 border border-slate-200 dark:border-[#1e3a52] text-center shadow-xs">
                        <div class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-0.5">{{ app()->getLocale() === 'ar' ? 'الموردين' : 'Suppliers' }}</div>
                        <div class="text-xl font-black text-slate-900 dark:text-emerald-400 font-mono">{{ $systemMetrics['suppliers_count'] }}</div>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-white/80 dark:bg-[#061a2b]/80 border border-slate-200 dark:border-[#1e3a52] text-center shadow-xs">
                        <div class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-0.5">{{ app()->getLocale() === 'ar' ? 'أوامر الشراء' : 'Orders' }}</div>
                        <div class="text-xl font-black text-slate-900 dark:text-amber-400 font-mono">{{ $systemMetrics['purchase_orders_count'] }}</div>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-white/80 dark:bg-[#061a2b]/80 border border-slate-200 dark:border-[#1e3a52] text-center shadow-xs">
                        <div class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-0.5">{{ app()->getLocale() === 'ar' ? 'المستودعات' : 'Depots' }}</div>
                        <div class="text-xl font-black text-slate-900 dark:text-purple-400 font-mono">{{ $systemMetrics['warehouses_count'] }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- 2. INTERACTIVE MODULE NAVIGATION TABS BAR -->
        <!-- ========================================================================= -->
        <div class="bz-guide-panel p-4">
            <div class="flex items-center gap-2.5 overflow-x-auto pb-1 scrollbar-none">
                <button type="button" class="bz-guide-tab-btn active-tab" onclick="switchGuideModule('products', this)">
                    <i class="fa-solid fa-box-open text-sky-500"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'المنتجات وتخصيص الواجهة' : 'Products & Customizer' }}</span>
                </button>
                <button type="button" class="bz-guide-tab-btn" onclick="switchGuideModule('purchases', this)">
                    <i class="fa-solid fa-cart-shopping text-emerald-500"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'المشتريات وسندات الاستلام' : 'Purchasing & GRN' }}</span>
                </button>
                <button type="button" class="bz-guide-tab-btn" onclick="switchGuideModule('inventory', this)">
                    <i class="fa-solid fa-warehouse text-amber-500"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'المخزون والتوزيع المزدوج' : 'Inventory & Batches' }}</span>
                </button>
                <button type="button" class="bz-guide-tab-btn" onclick="switchGuideModule('slider', this)">
                    <i class="fa-solid fa-images text-indigo-500"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'سلايدر الواجهة والمحاكاة' : 'Hero Slider Studio' }}</span>
                </button>
                <button type="button" class="bz-guide-tab-btn" onclick="switchGuideModule('manufacturing', this)">
                    <i class="fa-solid fa-industry text-purple-500"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'التصنيع وفحص الجودة cGMP' : 'Manufacturing & QC' }}</span>
                </button>
                <button type="button" class="bz-guide-tab-btn" onclick="switchGuideModule('finance', this)">
                    <i class="fa-solid fa-coins text-teal-500"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'المالية وسندات التحصيل' : 'Finance & Ledger' }}</span>
                </button>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- 3. TAB 1: PRODUCTS & SECTION CUSTOMIZER -->
        <!-- ========================================================================= -->
        <div id="module-products" class="bz-guide-module-view space-y-6">
            <div class="bz-guide-panel p-6 sm:p-8 space-y-8">
                <!-- Section Header -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 dark:border-[#1e3a52] pb-6">
                    <div class="flex items-center gap-4">
                        <span class="w-13 h-13 rounded-2xl bg-gradient-to-br from-[#0A4F78] to-[#0284c7] text-white flex items-center justify-center text-2xl shadow-lg shadow-[#0A4F78]/25 flex-shrink-0">
                            <i class="fa-solid fa-box-open"></i>
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="badge text-[10px] font-black uppercase tracking-wider bg-sky-100 dark:bg-sky-500/20 text-sky-800 dark:text-sky-300 border border-sky-300 dark:border-sky-500/30 px-2 py-0.5 rounded-full">CORE CATALOG</span>
                                <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">
                                    {{ app()->getLocale() === 'ar' ? 'إدارة المنتجات، تخصيص الأقسام وباقات التآزر' : 'Product Lifecycle, Section Customizer & Bundles' }}
                                </h2>
                            </div>
                            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-medium mt-1">
                                {{ app()->getLocale() === 'ar' ? 'المسار: /admin/products/create — معالج الخطوات السبع، الأقسام الثمانية، الحفظ التلقائي للمسودات، وباقات التآزر' : 'Path: /admin/products/create — 7-Step Floating Wizard, 8 Monograph Sections, Auto-Drafting & Synergistic Bundles' }}
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary" target="_blank">
                        <i class="fa-solid fa-plus text-xs"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'إنشاء منتج جديد' : 'New Product' }}</span>
                    </a>
                </div>

                <!-- Clean Responsive Flowchart -->
                <div class="space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <i class="fa-solid fa-diagram-project text-[#0284c7]"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'مخطط دورة حياة إنشاء وتخصيص المنتج (7-Step Wizard Flow)' : 'Product Wizard Flowchart' }}</span>
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bz-flow-node">
                            <div class="flex items-center justify-between text-xs font-mono font-bold text-sky-600 dark:text-sky-400 mb-2">
                                <span>PHASE 01</span>
                                <i class="fa-solid fa-barcode"></i>
                            </div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white mb-1.5">الرمز والبيانات الأساسية</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                                توليد رمز SKU المعياري، مسار الـ SEO (الرابط الدائم)، التصنيف العلاجي، وحفظ المسودة تلقائياً عبر AJAX.
                            </p>
                        </div>

                        <div class="bz-flow-node">
                            <div class="flex items-center justify-between text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400 mb-2">
                                <span>PHASE 02</span>
                                <i class="fa-solid fa-coins"></i>
                            </div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white mb-1.5">السياسة السعرية والضرائب</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                                تحديد سعر البيع الأساسي والتخفيض، احتساب ضريبة القيمة المضافة ديناميكياً، وتكلفة الاستيراد لحساب هامش الربح.
                            </p>
                        </div>

                        <div class="bz-flow-node">
                            <div class="flex items-center justify-between text-xs font-mono font-bold text-amber-600 dark:text-amber-400 mb-2">
                                <span>PHASE 03</span>
                                <i class="fa-solid fa-sliders"></i>
                            </div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white mb-1.5">مخصص الأقسام والشاشات</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                                مفاتيح تحكم مستقلة للكمبيوتر والهاتف (Desktop vs Mobile)، تحديد حالة فتح الأكورديون، وتخصيص المحتوى.
                            </p>
                        </div>

                        <div class="bz-flow-node">
                            <div class="flex items-center justify-between text-xs font-mono font-bold text-purple-600 dark:text-purple-400 mb-2">
                                <span>PHASE 04</span>
                                <i class="fa-solid fa-warehouse"></i>
                            </div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white mb-1.5">المستودعات والتشغيلات</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                                التوزيع المزدوج الفوري لمستودع المتجر والمستودع المركزي، وتسجيل التشغيلة وتاريخ الانتهاء لتتبع الجودة.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Clean Screenshots Grid (Distinct Steps with Zero Blocking Modals) -->
                <div class="space-y-4 pt-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-2">
                            <i class="fa-solid fa-camera-retro text-[#059669]"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'اللقطات الحية الحقيقية لخطوات المعالج (انقر على أي شاشة للتكبير السلس)' : 'Actual Real Step Screenshots (Click to Zoom Smoothly)' }}</span>
                        </h3>
                        <span class="text-xs text-slate-500 dark:text-slate-400 font-mono">8 Distinct Views</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Step 1 -->
                        <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot1_step1_core.png') }}', 'الخطوة 1: الرمز والتصنيف الأساسي', 'تحديد رمز SKU، الرابط الدائم، التصنيف العلاجي، وحالة المنتج الأولية.', 1)">
                            <div class="p-3 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                                <span class="text-[11px] font-bold text-sky-600 dark:text-sky-400 font-mono">STEP 01</span>
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200">الرمز والتصنيف</span>
                            </div>
                            <div class="relative overflow-hidden bg-slate-100 dark:bg-slate-900">
                                <img src="{{ asset('images/guide/shot1_step1_core.png') }}" alt="Step 1 Core SKU" class="w-full h-44 object-cover object-top" loading="lazy">
                                <div class="bz-screen-zoom-overlay">
                                    <span class="px-3.5 py-1.5 rounded-full bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-bold shadow-lg flex items-center gap-1.5">
                                        <i class="fa-solid fa-magnifying-glass-plus text-sky-500"></i> تكبير الشاشة
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot2_step2_content.png') }}', 'الخطوة 2: المحتوى والوصف ثنائي اللغة', 'كتابة الاسم التجاري والعلمي والملخص التسويقي والوصف الدوائي باللغتين.', 2)">
                            <div class="p-3 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                                <span class="text-[11px] font-bold text-sky-600 dark:text-sky-400 font-mono">STEP 02</span>
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200">المحتوى ثنائي اللغة</span>
                            </div>
                            <div class="relative overflow-hidden bg-slate-100 dark:bg-slate-900">
                                <img src="{{ asset('images/guide/shot2_step2_content.png') }}" alt="Step 2 Content" class="w-full h-44 object-cover object-top" loading="lazy">
                                <div class="bz-screen-zoom-overlay">
                                    <span class="px-3.5 py-1.5 rounded-full bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-bold shadow-lg flex items-center gap-1.5">
                                        <i class="fa-solid fa-magnifying-glass-plus text-sky-500"></i> تكبير الشاشة
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot3_step3_pricing.png') }}', 'الخطوة 3: السياسة السعرية والضرائب', 'تحديد أسعار البيع والتخفيض، حساب ضريبة القيمة المضافة، وتكلفة الاستيراد لحساب هامش الربح.', 3)">
                            <div class="p-3 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                                <span class="text-[11px] font-bold text-sky-600 dark:text-sky-400 font-mono">STEP 03</span>
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200">الأسعار والضرائب</span>
                            </div>
                            <div class="relative overflow-hidden bg-slate-100 dark:bg-slate-900">
                                <img src="{{ asset('images/guide/shot3_step3_pricing.png') }}" alt="Step 3 Pricing" class="w-full h-44 object-cover object-top" loading="lazy">
                                <div class="bz-screen-zoom-overlay">
                                    <span class="px-3.5 py-1.5 rounded-full bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-bold shadow-lg flex items-center gap-1.5">
                                        <i class="fa-solid fa-magnifying-glass-plus text-sky-500"></i> تكبير الشاشة
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Step 4 -->
                        <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot4_step4_media.png') }}', 'الخطوة 4: الأصول ومعرض الوسائط', 'رفع صورة العبوة المفرغة (Master Packshot) وتوليد الصور المصغرة ومعرض الزوايا.', 4)">
                            <div class="p-3 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                                <span class="text-[11px] font-bold text-sky-600 dark:text-sky-400 font-mono">STEP 04</span>
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200">الصور والوسائط</span>
                            </div>
                            <div class="relative overflow-hidden bg-slate-100 dark:bg-slate-900">
                                <img src="{{ asset('images/guide/shot4_step4_media.png') }}" alt="Step 4 Media" class="w-full h-44 object-cover object-top" loading="lazy">
                                <div class="bz-screen-zoom-overlay">
                                    <span class="px-3.5 py-1.5 rounded-full bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-bold shadow-lg flex items-center gap-1.5">
                                        <i class="fa-solid fa-magnifying-glass-plus text-sky-500"></i> تكبير الشاشة
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Step 5 -->
                        <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot5_step5_clinical.png') }}', 'الخطوة 5: الملف الطبي والجرعات', 'جدول المكونات الفعالة والعيارات المعيارية والمؤشرات الفسيولوجية المستهدفة.', 5)">
                            <div class="p-3 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                                <span class="text-[11px] font-bold text-sky-600 dark:text-sky-400 font-mono">STEP 05</span>
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200">الملف الطبي</span>
                            </div>
                            <div class="relative overflow-hidden bg-slate-100 dark:bg-slate-900">
                                <img src="{{ asset('images/guide/shot5_step5_clinical.png') }}" alt="Step 5 Clinical" class="w-full h-44 object-cover object-top" loading="lazy">
                                <div class="bz-screen-zoom-overlay">
                                    <span class="px-3.5 py-1.5 rounded-full bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-bold shadow-lg flex items-center gap-1.5">
                                        <i class="fa-solid fa-magnifying-glass-plus text-sky-500"></i> تكبير الشاشة
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Step 6 Customizer -->
                        <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot6_step6_customizer.png') }}', 'الخطوة 6: مخصص أقسام صفحة المنتج', 'التحكم المنفصل لظهور الأقسام على الكمبيوتر والموبايل وحالة الفتح الافتراضية للأكورديون.', 6)">
                            <div class="p-3 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                                <span class="text-[11px] font-bold text-sky-600 dark:text-sky-400 font-mono">STEP 06</span>
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200">مخصص الأقسام</span>
                            </div>
                            <div class="relative overflow-hidden bg-slate-100 dark:bg-slate-900">
                                <img src="{{ asset('images/guide/shot6_step6_customizer.png') }}" alt="Step 6 Customizer" class="w-full h-44 object-cover object-top" loading="lazy">
                                <div class="bz-screen-zoom-overlay">
                                    <span class="px-3.5 py-1.5 rounded-full bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-bold shadow-lg flex items-center gap-1.5">
                                        <i class="fa-solid fa-magnifying-glass-plus text-sky-500"></i> تكبير الشاشة
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Step 6 Bundles -->
                        <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot7_step6_bundle.png') }}', 'الخطوة 6 (موسع): باقات التآزر الحيوي', 'اختيار المكملات التكميلية وربطها في باقة متكاملة بخصم مخصص وزر إضافة موحد.', 7)">
                            <div class="p-3 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                                <span class="text-[11px] font-bold text-sky-600 dark:text-sky-400 font-mono">BUNDLES</span>
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200">استوديو الباقات</span>
                            </div>
                            <div class="relative overflow-hidden bg-slate-100 dark:bg-slate-900">
                                <img src="{{ asset('images/guide/shot7_step6_bundle.png') }}" alt="Step 6 Bundles" class="w-full h-44 object-cover object-top" loading="lazy">
                                <div class="bz-screen-zoom-overlay">
                                    <span class="px-3.5 py-1.5 rounded-full bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-bold shadow-lg flex items-center gap-1.5">
                                        <i class="fa-solid fa-magnifying-glass-plus text-sky-500"></i> تكبير الشاشة
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Step 7 Inventory -->
                        <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot8_step7_inventory.png') }}', 'الخطوة 7: المستودعات والتشغيلات', 'التوزيع المزدوج الفوري لمستودع المتجر والمستودع المركزي وتسجيل تشغيلة الإنتاج.', 8)">
                            <div class="p-3 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                                <span class="text-[11px] font-bold text-sky-600 dark:text-sky-400 font-mono">STEP 07</span>
                                <span class="text-xs font-bold text-slate-800 dark:text-slate-200">المستودعات والمخزون</span>
                            </div>
                            <div class="relative overflow-hidden bg-slate-100 dark:bg-slate-900">
                                <img src="{{ asset('images/guide/shot8_step7_inventory.png') }}" alt="Step 7 Inventory" class="w-full h-44 object-cover object-top" loading="lazy">
                                <div class="bz-screen-zoom-overlay">
                                    <span class="px-3.5 py-1.5 rounded-full bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-bold shadow-lg flex items-center gap-1.5">
                                        <i class="fa-solid fa-magnifying-glass-plus text-sky-500"></i> تكبير الشاشة
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- 4. TAB 2: PURCHASING & GOODS RECEIPTS (GRN) -->
        <!-- ========================================================================= -->
        <div id="module-purchases" class="bz-guide-module-view space-y-6" style="display: none;">
            <div class="bz-guide-panel p-6 sm:p-8 space-y-8">
                <!-- Section Header -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 dark:border-[#1e3a52] pb-6">
                    <div class="flex items-center gap-4">
                        <span class="w-13 h-13 rounded-2xl bg-gradient-to-br from-[#059669] to-[#10b981] text-white flex items-center justify-center text-2xl shadow-lg shadow-[#059669]/25 flex-shrink-0">
                            <i class="fa-solid fa-cart-shopping"></i>
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="badge text-[10px] font-black uppercase tracking-wider bg-emerald-100 dark:bg-emerald-500/20 text-emerald-800 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-500/30 px-2 py-0.5 rounded-full">PROCUREMENT & SOURCING</span>
                                <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">
                                    {{ app()->getLocale() === 'ar' ? 'دورة المشتريات، الموردين وسندات استلام البضائع (GRN)' : 'Purchases, Suppliers & Goods Receiving' }}
                                </h2>
                            </div>
                            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-medium mt-1">
                                {{ app()->getLocale() === 'ar' ? 'المسارات: /admin/suppliers | /admin/purchase-orders/create | /admin/goods-receipts' : 'Paths: /admin/suppliers | /admin/purchase-orders/create | /admin/goods-receipts' }}
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('admin.purchase-orders.create') }}" class="btn btn-primary" target="_blank">
                        <i class="fa-solid fa-file-invoice text-xs"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'أمر شراء جديد' : 'New PO' }}</span>
                    </a>
                </div>

                <!-- Clean Flowchart -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bz-flow-node">
                        <div class="text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400 mb-1">01. المورد المعياري (SUP)</div>
                        <div class="font-bold text-sm text-slate-900 dark:text-white mb-1">تسجيل المورد وشهادات cGMP</div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                            تحديد عملة التوريد، مدة التوريد بالأيام، وصلاحية اعتماد جودة صيدلانية لسنة كاملة، مع رمز الدولة المعياري (ISO2).
                        </p>
                    </div>

                    <div class="bz-flow-node">
                        <div class="text-xs font-mono font-bold text-sky-600 dark:text-sky-400 mb-1">02. عروض الأسعار (RFQ)</div>
                        <div class="font-bold text-sm text-slate-900 dark:text-white mb-1">مقارنة عروض الموردين</div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                            المفاضلة بين أسعار الموردين للمواد الخام والمنتجات التجارية مع حساب تكاليف الشحن الدولي ورسوم الاستيراد.
                        </p>
                    </div>

                    <div class="bz-flow-node">
                        <div class="text-xs font-mono font-bold text-amber-600 dark:text-amber-400 mb-1">03. أمر الشراء (PO)</div>
                        <div class="font-bold text-sm text-slate-900 dark:text-white mb-1">تثبيت الصرف وتكاليف الوصول</div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                            إصدار أمر الشراء المعتمد، تثبيت سعر الصرف FX Rate، وحساب تكلفة الوصول النهائية للوحدة (Landed Cost).
                        </p>
                    </div>

                    <div class="bz-flow-node">
                        <div class="text-xs font-mono font-bold text-teal-600 dark:text-teal-400 mb-1">04. سند الاستلام (GRN)</div>
                        <div class="font-bold text-sm text-slate-900 dark:text-white mb-1">فحص الأختام والإيداع بالحجر</div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                            فحص سلامة أختام الشحنة، تسجيل قراءات الحرارة، وإيداع البضائع في الحجر الصحي بانتظار فحص الجودة المخبري.
                        </p>
                    </div>
                </div>

                <!-- Real Clean Screenshots Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4">
                    <!-- PO Create Shot -->
                    <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot11_purchases_po.png') }}', 'أمر شراء جديد مع حساب تكاليف الوصول', 'إدخال البنود، اختيار المورد المعتمد، تثبيت العملة وسعر الصرف، وحساب تكاليف الشحن والجمارك.', 11)">
                        <div class="p-3 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                            <span class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 font-mono">PURCHASE ORDER</span>
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200">نموذج أمر الشراء المعتمد</span>
                        </div>
                        <div class="relative overflow-hidden bg-slate-100 dark:bg-slate-900">
                            <img src="{{ asset('images/guide/shot11_purchases_po.png') }}" alt="Purchase Order Create" class="w-full h-56 object-cover object-top" loading="lazy">
                            <div class="bz-screen-zoom-overlay">
                                <span class="px-4 py-2 rounded-full bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-bold shadow-lg flex items-center gap-2">
                                    <i class="fa-solid fa-magnifying-glass-plus text-emerald-500"></i> تكبير شاشة أمر الشراء
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- GRN List Shot -->
                    <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot12_goods_receipts.png') }}', 'سندات استلام البضائع وفحص الأختام والحرارة', 'جدول استلام الشحنات المبردة، تسجيل مسجلات الحرارة، وإيداع التشغيلات في الحجر الصحي.', 12)">
                        <div class="p-3 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                            <span class="text-[11px] font-bold text-teal-600 dark:text-teal-400 font-mono">GOODS RECEIPTS (GRN)</span>
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200">مذكرات استلام البضائع</span>
                        </div>
                        <div class="relative overflow-hidden bg-slate-100 dark:bg-slate-900">
                            <img src="{{ asset('images/guide/shot12_goods_receipts.png') }}" alt="Goods Receipts Index" class="w-full h-56 object-cover object-top" loading="lazy">
                            <div class="bz-screen-zoom-overlay">
                                <span class="px-4 py-2 rounded-full bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-bold shadow-lg flex items-center gap-2">
                                    <i class="fa-solid fa-magnifying-glass-plus text-teal-500"></i> تكبير شاشة سندات الاستلام
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- 5. TAB 3: INVENTORY & DUAL BUFFERS -->
        <!-- ========================================================================= -->
        <div id="module-inventory" class="bz-guide-module-view space-y-6" style="display: none;">
            <div class="bz-guide-panel p-6 sm:p-8 space-y-8">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 dark:border-[#1e3a52] pb-6">
                    <div class="flex items-center gap-4">
                        <span class="w-13 h-13 rounded-2xl bg-gradient-to-br from-[#d97706] to-[#f59e0b] text-white flex items-center justify-center text-2xl shadow-lg shadow-[#d97706]/25 flex-shrink-0">
                            <i class="fa-solid fa-warehouse"></i>
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="badge text-[10px] font-black uppercase tracking-wider bg-amber-100 dark:bg-amber-500/20 text-amber-800 dark:text-amber-300 border border-amber-300 dark:border-amber-500/30 px-2 py-0.5 rounded-full">INVENTORY MANAGEMENT</span>
                                <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">
                                    {{ app()->getLocale() === 'ar' ? 'المخزون الموحد، استراتيجية التوزيع المزدوج ونظام FEFO' : 'Unified Inventory, Dual Depot Buffers & FEFO Lot Expiry' }}
                                </h2>
                            </div>
                            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-medium mt-1">
                                {{ app()->getLocale() === 'ar' ? 'المسارات: /admin/inventory | /admin/warehouses | /admin/batches — إدارة المواقع وتتبع الصلاحيات' : 'Paths: /admin/inventory | /admin/warehouses | /admin/batches' }}
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('admin.inventory.index') }}" class="btn btn-primary" target="_blank">
                        <i class="fa-solid fa-boxes-stacked text-xs"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'فتح المخزون' : 'Open Inventory' }}</span>
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bz-flow-node space-y-2">
                        <div class="flex items-center gap-2 text-sky-600 dark:text-sky-400 font-bold text-sm">
                            <i class="fa-solid fa-globe"></i>
                            <span>مخزن المتجر السريع (Online Store Buffer)</span>
                        </div>
                        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                            المخزون المخصص للبيع الإلكتروني المباشر؛ يُخصم فور تأكيد الطلب إلكترونياً وتحديث عداد الكمية المتوفرة على صفحة المتجر فوراً.
                        </p>
                    </div>

                    <div class="bz-flow-node space-y-2">
                        <div class="flex items-center gap-2 text-emerald-600 dark:text-emerald-400 font-bold text-sm">
                            <i class="fa-solid fa-building-shield"></i>
                            <span>المستودع المركزي (Central Warehouse)</span>
                        </div>
                        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                            المخزون الاستراتيجي المخصص لمبيعات الجملة، المندوبين، والمستشفيات؛ يتم الصرف منه عبر أذونات الصرف والتحويل المخزني الداخلي.
                        </p>
                    </div>

                    <div class="bz-flow-node space-y-2">
                        <div class="flex items-center gap-2 text-amber-600 dark:text-amber-400 font-bold text-sm">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                            <span>نظام FEFO لتتبع الصلاحية</span>
                        </div>
                        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                            صرف التشغيلات الأقرب انتهاءً أولاً (First-Expired First-Out) لمنع الركود، مع تنبيهات استباقية للتشغيلات التي قاربت على الانتهاء.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- 6. TAB 4: HERO SLIDER & SIMULATION STUDIO -->
        <!-- ========================================================================= -->
        <div id="module-slider" class="bz-guide-module-view space-y-6" style="display: none;">
            <div class="bz-guide-panel p-6 sm:p-8 space-y-8">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 dark:border-[#1e3a52] pb-6">
                    <div class="flex items-center gap-4">
                        <span class="w-13 h-13 rounded-2xl bg-gradient-to-br from-[#4f46e5] to-[#6366f1] text-white flex items-center justify-center text-2xl shadow-lg shadow-[#4f46e5]/25 flex-shrink-0">
                            <i class="fa-solid fa-images"></i>
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="badge text-[10px] font-black uppercase tracking-wider bg-indigo-100 dark:bg-indigo-500/20 text-indigo-800 dark:text-indigo-300 border border-indigo-300 dark:border-indigo-500/30 px-2 py-0.5 rounded-full">HERO CAROUSEL STUDIO</span>
                                <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">
                                    {{ app()->getLocale() === 'ar' ? 'سلايدر الواجهة الرئيسي، ربط المنتجات ولوحة المحاكاة' : 'Hero Slider Command Center, Live Simulation & Drag-Drop' }}
                                </h2>
                            </div>
                            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-medium mt-1">
                                {{ app()->getLocale() === 'ar' ? 'المسار: /admin/hero-slider — التحكم في زمن التبديل (Delay 5500ms)، الربط التلقائي للمنتج، والمحاكاة الحية' : 'Path: /admin/hero-slider — Autoplay Delay (5500ms), Auto-Link Active Product & Canvas Simulator' }}
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('admin.hero-slides.index') }}" class="btn btn-primary" target="_blank">
                        <i class="fa-solid fa-sliders text-xs"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'فتح مركز السلايدر' : 'Open Slider Hub' }}</span>
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-2">
                    <!-- Slider Overview Shot -->
                    <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot9_hero_slider_overview.png') }}', 'مركز قيادة سلايدر الواجهة الرئيسي', 'التحكم في زمن الحركة التلقائي (Delay 5500ms)، تشغيل السلايدر، وإعادة الترتيب المباشر بالسحب والإفلات.', 9)">
                        <div class="p-3 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                            <span class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400 font-mono">HERO SLIDER OVERVIEW</span>
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200">لوحة التحكم وإعادة الترتيب</span>
                        </div>
                        <div class="relative overflow-hidden bg-slate-100 dark:bg-slate-900">
                            <img src="{{ asset('images/guide/shot9_hero_slider_overview.png') }}" alt="Hero Slider Overview" class="w-full h-56 object-cover object-top" loading="lazy">
                            <div class="bz-screen-zoom-overlay">
                                <span class="px-4 py-2 rounded-full bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-bold shadow-lg flex items-center gap-2">
                                    <i class="fa-solid fa-magnifying-glass-plus text-indigo-500"></i> تكبير السلايدر
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Slide Modal Studio Shot -->
                    <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot10_hero_slide_modal.png') }}', 'محرر الشريحة التفاعلي ولوحة المحاكاة الحية', 'ربط المنتج التلقائي، ضبط نصوص التوهج التدرجي، وتنسيق أزرار الطلب مع معاينة حية للشريحة.', 10)">
                        <div class="p-3 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                            <span class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400 font-mono">INTERACTIVE STUDIO MODAL</span>
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200">محرر الشريحة والمحاكاة</span>
                        </div>
                        <div class="relative overflow-hidden bg-slate-100 dark:bg-slate-900">
                            <img src="{{ asset('images/guide/shot10_hero_slide_modal.png') }}" alt="Slide Modal Studio" class="w-full h-56 object-cover object-top" loading="lazy">
                            <div class="bz-screen-zoom-overlay">
                                <span class="px-4 py-2 rounded-full bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-bold shadow-lg flex items-center gap-2">
                                    <i class="fa-solid fa-magnifying-glass-plus text-indigo-500"></i> تكبير محرر الشريحة
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- 7. TAB 5: MANUFACTURING & QC -->
        <!-- ========================================================================= -->
        <div id="module-manufacturing" class="bz-guide-module-view space-y-6" style="display: none;">
            <div class="bz-guide-panel p-6 sm:p-8 space-y-8">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 dark:border-[#1e3a52] pb-6">
                    <div class="flex items-center gap-4">
                        <span class="w-13 h-13 rounded-2xl bg-gradient-to-br from-[#7c3aed] to-[#8b5cf6] text-white flex items-center justify-center text-2xl shadow-lg shadow-[#7c3aed]/25 flex-shrink-0">
                            <i class="fa-solid fa-industry"></i>
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="badge text-[10px] font-black uppercase tracking-wider bg-purple-100 dark:bg-purple-500/20 text-purple-800 dark:text-purple-300 border border-purple-300 dark:border-purple-500/30 px-2 py-0.5 rounded-full">cGMP PHARMACEUTICAL</span>
                                <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">
                                    {{ app()->getLocale() === 'ar' ? 'التصنيع الدوائي، أوامر التشغيل وشهادات الفحص المخبري (COA)' : 'Pharmaceutical Manufacturing, Work Orders & COA' }}
                                </h2>
                            </div>
                            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-medium mt-1">
                                {{ app()->getLocale() === 'ar' ? 'المسارات: /admin/work-orders | /admin/qc | /admin/raw-materials' : 'Paths: /admin/work-orders | /admin/qc | /admin/raw-materials' }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bz-flow-node">
                        <div class="text-xs font-mono font-bold text-purple-600 dark:text-purple-400 mb-1">01. صرف المواد الخام</div>
                        <div class="font-bold text-sm text-slate-900 dark:text-white mb-1">فاتورة المواد (BOM)</div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                            صرف المواد الأولية بالكميات الدقيقة طبقاً للعيارات المسجلة في تركيبة المستحضر المعتمدة.
                        </p>
                    </div>

                    <div class="bz-flow-node">
                        <div class="text-xs font-mono font-bold text-sky-600 dark:text-sky-400 mb-1">02. خط الإنتاج والخلط</div>
                        <div class="font-bold text-sm text-slate-900 dark:text-white mb-1">أمر التشغيل (Work Order)</div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                            تتبع مراحل الكبسلة، الخلط، والتعبئة، واحتساب الفاقد الصناعي الطبيعي ونسبة الهدر المسموح بها.
                        </p>
                    </div>

                    <div class="bz-flow-node">
                        <div class="text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400 mb-1">03. الفحص المخبري (QC)</div>
                        <div class="font-bold text-sm text-slate-900 dark:text-white mb-1">شهادة النقاء (COA)</div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                            إجراء اختبارات التفتيت، المعايرة، والنقاء المخبري للتحقق من مطابقة معايير cGMP قبل الإفراج.
                        </p>
                    </div>

                    <div class="bz-flow-node">
                        <div class="text-xs font-mono font-bold text-teal-600 dark:text-teal-400 mb-1">04. الإيداع بالمستودع</div>
                        <div class="font-bold text-sm text-slate-900 dark:text-white mb-1">توريد المنتج التام للبيع</div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                            إيداع العبوات الجاهزة وتوليد الباركود التجاري ورقم التشغيلة الرسمي لتصبح جاهزة للصرف والطلب.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- 8. TAB 6: FINANCE & COLLECTIONS -->
        <!-- ========================================================================= -->
        <div id="module-finance" class="bz-guide-module-view space-y-6" style="display: none;">
            <div class="bz-guide-panel p-6 sm:p-8 space-y-8">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 dark:border-[#1e3a52] pb-6">
                    <div class="flex items-center gap-4">
                        <span class="w-13 h-13 rounded-2xl bg-gradient-to-br from-[#0d9488] to-[#14b8a6] text-white flex items-center justify-center text-2xl shadow-lg shadow-[#0d9488]/25 flex-shrink-0">
                            <i class="fa-solid fa-coins"></i>
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="badge text-[10px] font-black uppercase tracking-wider bg-teal-100 dark:bg-teal-500/20 text-teal-800 dark:text-teal-300 border border-teal-300 dark:border-teal-500/30 px-2 py-0.5 rounded-full">FINANCE & ACCOUNTING</span>
                                <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">
                                    {{ app()->getLocale() === 'ar' ? 'الإدارة المالية، شبكة سندات التحصيل وفواتير المبيعات والمشتريات' : 'Financial Accounting, Collections & Ledgers' }}
                                </h2>
                            </div>
                            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-medium mt-1">
                                {{ app()->getLocale() === 'ar' ? 'المسارات: /admin/finance/dashboard | /admin/finance/collections | /admin/finance/accounts' : 'Paths: /admin/finance/dashboard | /admin/finance/collections | /admin/finance/accounts' }}
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('admin.finance.dashboard') }}" class="btn btn-primary" target="_blank">
                        <i class="fa-solid fa-chart-line text-xs"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'لوحة تحكم المالية' : 'Finance Dashboard' }}</span>
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-2">
                    <div class="bz-screen-card" onclick="openLightboxModal('{{ asset('images/guide/shot13_finance_dashboard.png') }}', 'لوحة تحكم المنظومة المالية والحسابات', 'متابعة الذمم المدينة والدائنة، إجمالي المبيعات، ومصروفات التشغيل وحركة الصناديق.', 13)">
                        <div class="p-3 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                            <span class="text-[11px] font-bold text-teal-600 dark:text-teal-400 font-mono">FINANCE DASHBOARD</span>
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200">لوحة تحكم المالية</span>
                        </div>
                        <div class="relative overflow-hidden bg-slate-100 dark:bg-slate-900">
                            <img src="{{ asset('images/guide/shot13_finance_dashboard.png') }}" alt="Finance Dashboard" class="w-full h-56 object-cover object-top" loading="lazy">
                            <div class="bz-screen-zoom-overlay">
                                <span class="px-4 py-2 rounded-full bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-bold shadow-lg flex items-center gap-2">
                                    <i class="fa-solid fa-magnifying-glass-plus text-teal-500"></i> تكبير لوحة المالية
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="bz-flow-node space-y-1.5">
                            <div class="font-bold text-sm text-slate-900 dark:text-white flex items-center gap-2">
                                <i class="fa-solid fa-table-list text-teal-500"></i>
                                <span>شبكة سندات وقيود التحصيل (Collection Grid)</span>
                            </div>
                            <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                                شبكة تفاعلية سريعة تتيح تسجيل مقبوضات العملاء، الشيكات، والحوالات البنكية مع المزامنة التلقائية اللحظية مع قيود اليومية.
                            </p>
                        </div>

                        <div class="bz-flow-node space-y-1.5">
                            <div class="font-bold text-sm text-slate-900 dark:text-white flex items-center gap-2">
                                <i class="fa-solid fa-money-bill-transfer text-rose-500"></i>
                                <span>سندات الصرف ومستحقات الموردين (Payment Vouchers)</span>
                            </div>
                            <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
                                تسوية فواتير المشتريات وإصدار سندات الصرف النقدية والبنكية مع إمكانية طباعة سند الصرف المعتمد (Voucher PDF).
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- 9. FULLSCREEN SMOOTH APPLE-STYLE LIGHTBOX MODAL -->
    <!-- ========================================================================= -->
    <div id="guideLightbox" onclick="closeLightboxModal(event)">
        <div id="guideLightboxContent" onclick="event.stopPropagation()">
            <!-- Top Controls Header -->
            <div class="w-full flex items-center justify-between text-white pb-3 px-2">
                <div class="flex items-center gap-3">
                    <span id="lightboxStepBadge" class="px-3 py-1 rounded-lg bg-sky-500/20 text-sky-300 border border-sky-400/40 text-xs font-mono font-bold">STEP 01</span>
                    <h3 id="lightboxTitle" class="text-base sm:text-lg font-black text-white">عنوان الخطوة</h3>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" class="w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition" onclick="navigateLightboxModal(-1)" title="Previous (Left Arrow)">
                        <i class="fa-solid fa-chevron-left text-sm"></i>
                    </button>
                    <button type="button" class="w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition" onclick="navigateLightboxModal(1)" title="Next (Right Arrow)">
                        <i class="fa-solid fa-chevron-right text-sm"></i>
                    </button>
                    <button type="button" class="w-9 h-9 rounded-full bg-rose-500/80 hover:bg-rose-600 text-white flex items-center justify-center transition ml-2" onclick="forceCloseLightboxModal()" title="Close (Esc)">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>
            </div>

            <!-- Image Viewport -->
            <img id="lightboxImage" src="" alt="Guide Screenshot">

            <!-- Bottom Action Description Card -->
            <div class="mt-4 p-4 rounded-2xl bg-[#081d2f]/95 border border-sky-500/30 text-white max-w-2xl w-full text-right shadow-2xl backdrop-blur-md">
                <div class="flex items-center gap-2 text-emerald-400 font-bold text-xs mb-1">
                    <i class="fa-solid fa-bolt"></i>
                    <span>الإجراء المنفذ في هذه الشاشة (Action Details):</span>
                </div>
                <p id="lightboxDescription" class="text-xs sm:text-sm text-slate-200 leading-relaxed font-medium"></p>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 10. JAVASCRIPT CONTROLLERS -->
    <!-- ========================================================================= -->
    <script>
        const galleryDatabase = [
            {
                url: "{{ asset('images/guide/shot1_step1_core.png') }}",
                title: "الخطوة 1: الرمز والتصنيف الأساسي (Core SKU & Category)",
                desc: "تحديد رمز SKU المعياري، مسار الـ SEO، التصنيف العلاجي، والعلامة التجارية مع حفظ المسودة تلقائياً.",
                step: 1
            },
            {
                url: "{{ asset('images/guide/shot2_step2_content.png') }}",
                title: "الخطوة 2: المحتوى والوصف ثنائي اللغة (Bilingual Content)",
                desc: "إدخال الاسم التجاري والعلمي والملخص التسويقي والوصف الدوائي الكامل باللغتين العربية والإنجليزية.",
                step: 2
            },
            {
                url: "{{ asset('images/guide/shot3_step3_pricing.png') }}",
                title: "الخطوة 3: السياسة السعرية والضرائب (Pricing & VAT)",
                desc: "تحديد أسعار البيع والعروض، احتساب ضريبة القيمة المضافة ديناميكياً، وتكلفة الاستيراد لحساب هامش الربح الإجمالي.",
                step: 3
            },
            {
                url: "{{ asset('images/guide/shot4_step4_media.png') }}",
                title: "الخطوة 4: الأصول ومعرض الوسائط (High-Res Media)",
                desc: "رفع صورة العبوة المفرغة (Master Packshot) وتوليد الصور المصغرة ومعرض الزوايا المتعددة.",
                step: 4
            },
            {
                url: "{{ asset('images/guide/shot5_step5_clinical.png') }}",
                title: "الخطوة 5: الملف الطبي والجرعات (Clinical Monograph)",
                desc: "تسجيل المكونات الفعالة والعيارات الصيدلانية القياسية والمؤشرات الفسيولوجية المستهدفة.",
                step: 5
            },
            {
                url: "{{ asset('images/guide/shot6_step6_customizer.png') }}",
                title: "الخطوة 6: مخصص أقسام صفحة المنتج (Section Customizer)",
                desc: "مفاتيح تحكم مستقلة لظهور الأقسام على أجهزة الكمبيوتر والموبايل وحالة الفتح الافتراضية لكل قسم.",
                step: 6
            },
            {
                url: "{{ asset('images/guide/shot7_step6_bundle.png') }}",
                title: "الخطوة 6 (موسع): باقات التآزر الحيوي (Synergistic Bundles)",
                desc: "اختيار المكملات التكميلية وربطها في باقة متكاملة بخصم مخصص وإمكانية الشراء بنقرة واحدة.",
                step: 7
            },
            {
                url: "{{ asset('images/guide/shot8_step7_inventory.png') }}",
                title: "الخطوة 7: المستودعات والمخزون الأولي (Depot Allocation)",
                desc: "التوزيع المزدوج الفوري لمستودع المتجر والمستودع المركزي وتسجيل بيانات تشغيلة الإنتاج الأولى.",
                step: 8
            },
            {
                url: "{{ asset('images/guide/shot9_hero_slider_overview.png') }}",
                title: "سلايدر الواجهة: مركز التحكم والإعدادات العامة (Hero Slider)",
                desc: "التحكم في زمن الحركة التلقائي (Delay 5500ms)، تشغيل السلايدر، وإعادة الترتيب المباشر بالسحب والإفلات.",
                step: 9
            },
            {
                url: "{{ asset('images/guide/shot10_hero_slide_modal.png') }}",
                title: "سلايدر الواجهة: محرر الشريحة والمحاكاة الحية (Slide Studio Modal)",
                desc: "ربط المنتج التلقائي، ضبط نصوص التوهج التدرجي، وتنسيق أزرار الطلب مع معاينة مباشرة للشريحة.",
                step: 10
            },
            {
                url: "{{ asset('images/guide/shot11_purchases_po.png') }}",
                title: "أمر شراء جديد مع حساب تكاليف الوصول (Purchase Order)",
                desc: "إدخال البنود، اختيار المورد المعتمد، تثبيت العملة وسعر الصرف، وحساب تكاليف الشحن والجمارك.",
                step: 11
            },
            {
                url: "{{ asset('images/guide/shot12_goods_receipts.png') }}",
                title: "سندات استلام البضائع وفحص الأختام والحرارة (Goods Receipts GRN)",
                desc: "جدول استلام الشحنات المبردة، تسجيل مسجلات الحرارة، وإيداع التشغيلات في الحجر الصحي.",
                step: 12
            },
            {
                url: "{{ asset('images/guide/shot13_finance_dashboard.png') }}",
                title: "لوحة تحكم المنظومة المالية والحسابات (Finance Dashboard)",
                desc: "متابعة الذمم المدينة والدائنة، إجمالي المبيعات، ومصروفات التشغيل وحركة الصناديق.",
                step: 13
            }
        ];

        let activeLightboxIdx = 0;

        function openLightboxModal(url, title, desc, stepNum) {
            activeLightboxIdx = galleryDatabase.findIndex(item => item.step === stepNum);
            if (activeLightboxIdx === -1) activeLightboxIdx = 0;
            renderLightboxView();
            const modal = document.getElementById('guideLightbox');
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function renderLightboxView() {
            const item = galleryDatabase[activeLightboxIdx];
            document.getElementById('lightboxImage').src = item.url;
            document.getElementById('lightboxTitle').textContent = item.title;
            document.getElementById('lightboxDescription').textContent = item.desc;
            document.getElementById('lightboxStepBadge').textContent = 'STEP ' + String(item.step).padStart(2, '0');
        }

        function navigateLightboxModal(dir) {
            activeLightboxIdx += dir;
            if (activeLightboxIdx < 0) activeLightboxIdx = galleryDatabase.length - 1;
            if (activeLightboxIdx >= galleryDatabase.length) activeLightboxIdx = 0;
            renderLightboxView();
        }

        function closeLightboxModal(e) {
            if (e.target.id === 'guideLightbox') {
                forceCloseLightboxModal();
            }
        }

        function forceCloseLightboxModal() {
            const modal = document.getElementById('guideLightbox');
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }

        document.addEventListener('keydown', (e) => {
            const modal = document.getElementById('guideLightbox');
            if (modal.classList.contains('active')) {
                if (e.key === 'Escape') forceCloseLightboxModal();
                if (e.key === 'ArrowRight') navigateLightboxModal(1);
                if (e.key === 'ArrowLeft') navigateLightboxModal(-1);
            }
        });

        function switchGuideModule(modId, btn) {
            document.querySelectorAll('.bz-guide-tab-btn').forEach(b => b.classList.remove('active-tab'));
            btn.classList.add('active-tab');

            document.querySelectorAll('.bz-guide-module-view').forEach(view => {
                view.style.display = 'none';
            });

            const targetView = document.getElementById('module-' + modId);
            if (targetView) {
                targetView.style.display = 'block';
            }
        }
    </script>
</x-layouts.admin>
