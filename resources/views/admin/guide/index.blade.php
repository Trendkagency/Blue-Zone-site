<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'الدعم والدليل الشامل للنظام' : 'System Knowledge Hub & Support Guide'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'المرجع المعماري والتشغيلي المكتمل لكافة دورات العمل، الشاشات، المنطق المحاسبي والمخزني في منصة Bluezone' : 'Comprehensive Architectural & Operational Knowledge Base for Bluezone Bioceuticals OS'"
    :breadcrumbs="[
        (app()->getLocale() === 'ar' ? 'لوحة التحكم' : 'Dashboard') => route('admin.dashboard'),
        (app()->getLocale() === 'ar' ? 'الدعم والدليل' : 'Support & Guide') => route('admin.guide.index')
    ]"
>
    <!-- ========================================================================= -->
    <!-- GUIDE STYLES & SMOOTH APPLE-STYLE LIGHTBOX ENGINE -->
    <!-- ========================================================================= -->
    <style>
        .bz-guide-container {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Cairo", sans-serif;
        }

        /* Module Pill Buttons */
        .bz-module-nav-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.65rem;
            padding: 0.65rem 1.1rem;
            border-radius: 1rem;
            font-size: 0.825rem;
            font-weight: 700;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            color: #475569;
            background: #ffffff;
            border: 1.5px solid #cbd5e1;
            white-space: nowrap;
            cursor: pointer;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }
        .dark .bz-module-nav-btn {
            color: #cbd5e1;
            background: #081d2f;
            border-color: #1e3a52;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.4);
        }
        .bz-module-nav-btn:hover {
            border-color: #2A8FC2;
            color: #0A4F78;
            transform: translateY(-1px);
        }
        .dark .bz-module-nav-btn:hover {
            border-color: #38bdf8;
            color: #ffffff;
            background: #0c273e;
        }
        .bz-module-nav-btn.active {
            background: linear-gradient(135deg, #0A4F78 0%, #156291 100%) !important;
            color: #ffffff !important;
            border-color: #0A4F78 !important;
            box-shadow: 0 4px 14px rgba(10, 79, 120, 0.35) !important;
        }
        .bz-module-nav-btn.active i {
            color: #38bdf8 !important;
        }

        /* Glassmorphism Card System */
        .bz-guide-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 1.25rem;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .dark .bz-guide-card {
            background: #081d2f;
            border-color: #1e3a52;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.5);
        }
        .bz-guide-card:hover {
            box-shadow: 0 12px 32px -4px rgba(10, 79, 120, 0.15);
            border-color: #2A8FC2;
        }
        .dark .bz-guide-card:hover {
            box-shadow: 0 14px 40px -4px rgba(2, 18, 30, 0.8);
            border-color: #38bdf8;
        }

        /* Interactive Click-to-Zoom Screen Thumbnail */
        .bz-screen-thumb-container {
            position: relative;
            overflow: hidden;
            border-radius: 1rem;
            border: 1.5px solid #e2e8f0;
            background: #061e31;
            cursor: zoom-in;
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .dark .bz-screen-thumb-container {
            border-color: #1e3a52;
        }
        .bz-screen-thumb-container:hover {
            transform: scale(1.015);
            border-color: #00f2fe;
            box-shadow: 0 14px 35px -8px rgba(0, 242, 254, 0.35);
        }
        .bz-screen-thumb-container:hover .bz-zoom-badge {
            opacity: 1;
            transform: translateY(0);
        }
        .bz-screen-thumb-img {
            width: 100%;
            height: auto;
            display: block;
            transition: transform 0.4s ease-out;
        }
        .bz-zoom-badge {
            position: absolute;
            bottom: 12px;
            left: 50%;
            transform: translate(-50%, 8px);
            opacity: 0;
            transition: all 0.25s ease;
            background: rgba(8, 29, 47, 0.9);
            color: #ffffff;
            border: 1px solid #38bdf8;
            padding: 6px 14px;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(8px);
            pointer-events: none;
        }

        /* Logic Cycle Flowchart Node */
        .bz-cycle-step {
            position: relative;
            background: rgba(248, 250, 252, 0.85);
            border: 1.5px solid #cbd5e1;
            border-radius: 1rem;
            padding: 1.1rem;
            transition: all 0.25s ease;
        }
        .dark .bz-cycle-step {
            background: rgba(15, 44, 68, 0.65);
            border-color: #1e3a52;
        }
        .bz-cycle-step:hover {
            border-color: #2A8FC2;
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(42, 143, 194, 0.2);
        }

        /* Apple-Style Smooth Lightbox Modal */
        #guideLightbox {
            position: fixed;
            inset: 0;
            z-index: 9999999;
            display: none;
            align-items: center;
            justify-content: center;
            background: rgba(2, 18, 30, 0.85);
            backdrop-filter: blur(20px);
            opacity: 0;
            transition: opacity 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        #guideLightbox.active {
            display: flex;
            opacity: 1;
        }
        #guideLightboxContent {
            position: relative;
            max-width: 94vw;
            max-height: 92vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            transform: scale(0.92);
            transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }
        #guideLightbox.active #guideLightboxContent {
            transform: scale(1);
        }
        #lightboxImage {
            max-width: 100%;
            max-height: 78vh;
            border-radius: 1.25rem;
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.8), 0 0 40px rgba(42, 143, 194, 0.3);
            border: 2px solid rgba(56, 189, 248, 0.4);
            user-select: none;
        }
    </style>

    <div class="bz-guide-container space-y-8 pb-16">
        
        <!-- ========================================================================= -->
        <!-- 1. EXECUTIVE HERO COMMAND BANNER -->
        <!-- ========================================================================= -->
        <div class="relative overflow-hidden rounded-3xl p-6 sm:p-8 border border-[#2A8FC2]/30 shadow-2xl"
             style="background: radial-gradient(circle at 15% 20%, #0d3859 0%, #081d2f 60%, #02121E 100%);">
            
            <!-- Ambient Decorative Glows -->
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-[#2A8FC2]/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-[#67B34A]/15 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
                <div class="space-y-3 max-w-3xl">
                    <div class="inline-flex items-center gap-2.5 px-3.5 py-1.5 rounded-full text-xs font-bold tracking-wide uppercase"
                         style="background: rgba(56, 189, 248, 0.15); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.3);">
                        <i class="fa-solid fa-compass-drafting animate-spin-slow"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'المرجع التشغيلي والمعماري المعتمد لـ Bluezone' : 'Official Bluezone OS Architecture & Operations' }}</span>
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span>
                    </div>

                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-white tracking-tight leading-tight">
                        {{ app()->getLocale() === 'ar' ? 'مركز الدعم ودليل النظام التفاعلي الشامل' : 'Support & Comprehensive System Guide Hub' }}
                    </h1>

                    <p class="text-sm sm:text-base text-slate-300 font-medium leading-relaxed">
                        {{ app()->getLocale() === 'ar' 
                            ? 'توثيق تنفيذي دقيق بدون أي فجوات يشمل كافة دورات العمل (Logic Cycles)، الشاشات، عناصر التحكم، التوجيهات المحاسبية، وحركات المخزون مع لقطات حية وتكبير سلس.' 
                            : 'Exhaustive zero-gap enterprise documentation covering all business cycles, screens, inputs, accounting directives, and inventory movements with live smooth visual lightbox.' }}
                    </p>
                </div>

                <!-- Quick Action Buttons -->
                <div class="flex items-center gap-3 w-full lg:w-auto">
                    <a href="#logic-cycles" class="flex-1 lg:flex-none inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-gradient-to-r from-[#0A4F78] to-[#2A8FC2] text-white font-bold text-sm shadow-lg shadow-[#0A4F78]/40 hover:brightness-110 transition">
                        <i class="fa-solid fa-diagram-project"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'مخططات دورات العمل' : 'Logic Cycles' }}</span>
                    </a>
                    <a href="#system-catalog" class="flex-1 lg:flex-none inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-white/10 hover:bg-white/15 text-white font-bold text-sm border border-white/20 transition">
                        <i class="fa-solid fa-table-cells"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'فهرس الشاشات' : 'Screens Catalog' }}</span>
                    </a>
                </div>
            </div>

            <!-- Live Platform Metrics Strip -->
            <div class="relative z-10 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4 mt-8 pt-6 border-t border-white/10">
                <div class="bg-white/5 rounded-2xl p-3 border border-white/10 text-center">
                    <div class="text-xs text-slate-400 font-semibold mb-1">{{ app()->getLocale() === 'ar' ? 'المنتجات المسجلة' : 'Products' }}</div>
                    <div class="text-lg font-black text-white font-mono">{{ $systemMetrics['products_count'] }}</div>
                </div>
                <div class="bg-white/5 rounded-2xl p-3 border border-white/10 text-center">
                    <div class="text-xs text-slate-400 font-semibold mb-1">{{ app()->getLocale() === 'ar' ? 'الموردين المعتمدين' : 'Suppliers' }}</div>
                    <div class="text-lg font-black text-sky-400 font-mono">{{ $systemMetrics['suppliers_count'] }}</div>
                </div>
                <div class="bg-white/5 rounded-2xl p-3 border border-white/10 text-center">
                    <div class="text-xs text-slate-400 font-semibold mb-1">{{ app()->getLocale() === 'ar' ? 'المستودعات والمخازن' : 'Warehouses' }}</div>
                    <div class="text-lg font-black text-emerald-400 font-mono">{{ $systemMetrics['warehouses_count'] }}</div>
                </div>
                <div class="bg-white/5 rounded-2xl p-3 border border-white/10 text-center">
                    <div class="text-xs text-slate-400 font-semibold mb-1">{{ app()->getLocale() === 'ar' ? 'أوامر الشراء' : 'Purchase Orders' }}</div>
                    <div class="text-lg font-black text-amber-400 font-mono">{{ $systemMetrics['purchase_orders_count'] }}</div>
                </div>
                <div class="bg-white/5 rounded-2xl p-3 border border-white/10 text-center">
                    <div class="text-xs text-slate-400 font-semibold mb-1">{{ app()->getLocale() === 'ar' ? 'سندات الاستلام (GRN)' : 'Goods Receipts' }}</div>
                    <div class="text-lg font-black text-teal-400 font-mono">{{ $systemMetrics['goods_receipts_count'] }}</div>
                </div>
                <div class="bg-white/5 rounded-2xl p-3 border border-white/10 text-center">
                    <div class="text-xs text-slate-400 font-semibold mb-1">{{ app()->getLocale() === 'ar' ? 'أوامر التشغيل والتصنيع' : 'Work Orders' }}</div>
                    <div class="text-lg font-black text-purple-400 font-mono">{{ $systemMetrics['work_orders_count'] }}</div>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- 2. REAL-TIME SEARCH BAR & QUICK MODULE PILLS -->
        <!-- ========================================================================= -->
        <div class="bz-guide-card p-4 sm:p-5">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <!-- Search Input with Clear Button -->
                <div class="relative w-full md:w-96">
                    <i class="fa-solid fa-magnifying-glass absolute top-1/2 -translate-y-1/2 text-slate-400 text-sm {{ app()->getLocale() === 'ar' ? 'right-4' : 'left-4' }}"></i>
                    <input type="text" id="guideSearchInput" onkeyup="filterGuideTopics()"
                           placeholder="{{ app()->getLocale() === 'ar' ? 'بحث سريع في دورات العمل والشاشات والعناصر...' : 'Search cycles, screens, inputs, elements...' }}"
                           class="w-full py-2.5 rounded-xl border border-slate-300 dark:border-[#1e3a52] bg-slate-50 dark:bg-[#061a2b] text-slate-800 dark:text-slate-100 text-sm font-medium focus:ring-2 focus:ring-[#2A8FC2] focus:border-transparent transition {{ app()->getLocale() === 'ar' ? 'pr-11 pl-4' : 'pl-11 pr-4' }}">
                </div>

                <!-- Module Filter Pills -->
                <div class="flex items-center gap-2 overflow-x-auto w-full pb-2 md:pb-0 scrollbar-none">
                    <button type="button" class="bz-module-nav-btn active" onclick="switchGuideTab('all')">
                        <i class="fa-solid fa-layer-group"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'كافة المنظومة' : 'All Modules' }}</span>
                    </button>
                    <button type="button" class="bz-module-nav-btn" onclick="switchGuideTab('products')">
                        <i class="fa-solid fa-box-open text-sky-400"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'المنتجات والسلايدر' : 'Products & Slider' }}</span>
                    </button>
                    <button type="button" class="bz-module-nav-btn" onclick="switchGuideTab('purchases')">
                        <i class="fa-solid fa-cart-shopping text-emerald-400"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'المشتريات والموردين' : 'Purchasing' }}</span>
                    </button>
                    <button type="button" class="bz-module-nav-btn" onclick="switchGuideTab('inventory')">
                        <i class="fa-solid fa-warehouse text-amber-400"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'المخزون والتشغيلات' : 'Inventory' }}</span>
                    </button>
                    <button type="button" class="bz-module-nav-btn" onclick="switchGuideTab('manufacturing')">
                        <i class="fa-solid fa-industry text-purple-400"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'التصنيع cGMP' : 'Manufacturing' }}</span>
                    </button>
                    <button type="button" class="bz-module-nav-btn" onclick="switchGuideTab('finance')">
                        <i class="fa-solid fa-coins text-teal-400"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'المالية والحسابات' : 'Finance' }}</span>
                    </button>
                    <button type="button" class="bz-module-nav-btn" onclick="switchGuideTab('sales')">
                        <i class="fa-solid fa-truck-fast text-rose-400"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'المبيعات والطلبات' : 'Sales' }}</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- 3. COMPREHENSIVE SYSTEM MODULES BREAKDOWN -->
        <!-- ========================================================================= -->
        <div class="space-y-12" id="guideModulesContainer">

            <!-- ================================================================= -->
            <!-- MODULE 1: PRODUCTS & HERO SLIDER COMMAND CENTER -->
            <!-- ================================================================= -->
            <div class="bz-guide-card p-6 sm:p-8 space-y-6 guide-module-section" data-module="products">
                <!-- Module Header -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 dark:border-[#1e3a52] pb-5">
                    <div class="flex items-center gap-3.5">
                        <span class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#0A4F78] to-[#2A8FC2] text-white flex items-center justify-center text-xl shadow-lg shadow-[#0A4F78]/30 flex-shrink-0">
                            <i class="fa-solid fa-box-open"></i>
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="badge text-[10px] font-black uppercase tracking-wider bg-sky-500/20 text-sky-400 border border-sky-500/30 px-2 py-0.5 rounded-full">CORE CATALOG</span>
                                <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">
                                    {{ app()->getLocale() === 'ar' ? '1. إدارة المنتجات، تخصيص الأقسام وسلايدر الواجهة' : '1. Product Management, Section Customizer & Hero Slider' }}
                                </h2>
                            </div>
                            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-medium mt-0.5">
                                {{ app()->getLocale() === 'ar' ? 'المسارات: /admin/products/create | /admin/hero-slider — معالج الخطوات السبع، الأقسام الثمانية، وباقات التآزر' : 'Routes: /admin/products/create | /admin/hero-slider — 7-Step Wizard, 8 Monograph Sections & Synergistic Bundles' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.products.create') }}" class="btn btn-sm btn-outline-primary" target="_blank">
                            <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'فتح معالج المنتج' : 'Open Wizard' }}</span>
                        </a>
                        <a href="{{ route('admin.hero-slides.index') }}" class="btn btn-sm btn-outline-primary" target="_blank">
                            <i class="fa-solid fa-images text-xs"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'فتح السلايدر' : 'Open Slider' }}</span>
                        </a>
                    </div>
                </div>

                <!-- Business Logic Flowchart -->
                <div class="space-y-3">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-2">
                        <i class="fa-solid fa-diagram-project text-[#2A8FC2]"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'مخطط سير دورة إنشاء المنتج وتخصيص الواجهة (Start-to-End Logic Cycle)' : 'Product Creation & Slider Lifecycle' }}</span>
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                        <div class="bz-cycle-step">
                            <div class="flex items-center justify-between text-xs font-mono font-bold text-sky-400 mb-1.5">
                                <span>PHASE 01</span>
                                <i class="fa-solid fa-barcode"></i>
                            </div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white mb-1">البيانات والملف الطبي</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                                تسجيل الرمز (SKU)، الرابط الدائم (Slug)، العيارات الدوائية، والمكونات المعيارية باللغتين مع الحفظ التلقائي للمسودة.
                            </p>
                        </div>

                        <div class="bz-cycle-step">
                            <div class="flex items-center justify-between text-xs font-mono font-bold text-emerald-400 mb-1.5">
                                <span>PHASE 02</span>
                                <i class="fa-solid fa-sliders"></i>
                            </div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white mb-1">مخصص الأقسام والشاشات</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                                تفعيل أو إخفاء الأقسام الثمانية بشكل مستقل للكمبيوتر والهاتف (Desktop vs Mobile)، وضبط حالة الأكورديون الافتراضية.
                            </p>
                        </div>

                        <div class="bz-cycle-step">
                            <div class="flex items-center justify-between text-xs font-mono font-bold text-amber-400 mb-1.5">
                                <span>PHASE 03</span>
                                <i class="fa-solid fa-layer-group"></i>
                            </div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white mb-1">استوديو باقات التآزر</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                                ربط المكملات التكميلية (Synergistic Formulations) وتحديد نسبة خصم الباقة لتنشيط الشراء الموحد بنقرة واحدة.
                            </p>
                        </div>

                        <div class="bz-cycle-step">
                            <div class="flex items-center justify-between text-xs font-mono font-bold text-purple-400 mb-1.5">
                                <span>PHASE 04</span>
                                <i class="fa-solid fa-images"></i>
                            </div>
                            <div class="font-bold text-sm text-slate-900 dark:text-white mb-1">سلايدر الواجهة والمستودعات</div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                                ربط المنتج التلقائي بشريحة السلايدر، إبراز التوهج، وتوزيع المخزون بين المتجر الإلكتروني والمستودع المركزي.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Interactive Visual Lightbox Showcase (10 Real Screenshots with Highlights) -->
                <div class="space-y-4 pt-2">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center gap-2">
                            <i class="fa-solid fa-camera-retro text-[#67B34A]"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'معرض اللقطات الحية والشاشات المحددة (انقر على أي شاشة للتكبير السلس)' : 'Live Screen Walkthrough with Highlight Actions (Click to Zoom)' }}</span>
                        </h3>
                        <span class="text-xs text-slate-400 font-mono">10 Visual Steps</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Shot 1 -->
                        <div class="bz-screen-thumb-container" onclick="openLightbox('{{ asset('images/guide/shot1_step1_core.png') }}', 'الخطوة 1: البيانات الأساسية والتصنيف', 'تحديد رمز المنتج (SKU)، الرابط الدائم (Slug)، القسم العلاجي، والعلامة التجارية.', 1)">
                            <img src="{{ asset('images/guide/shot1_step1_core.png') }}" alt="Step 1 Core SKU" class="bz-screen-thumb-img" loading="lazy">
                            <div class="bz-zoom-badge"><i class="fa-solid fa-magnifying-glass-plus"></i> تكبير الشاشة (Step 1)</div>
                        </div>

                        <!-- Shot 2 -->
                        <div class="bz-screen-thumb-container" onclick="openLightbox('{{ asset('images/guide/shot2_step2_content.png') }}', 'الخطوة 2: المحتوى والوصف ثنائي اللغة', 'إدخال الاسم والملخص التسويقي والوصف العلمي الموسع باللغتين العربية والإنجليزية.', 2)">
                            <img src="{{ asset('images/guide/shot2_step2_content.png') }}" alt="Step 2 Content" class="bz-screen-thumb-img" loading="lazy">
                            <div class="bz-zoom-badge"><i class="fa-solid fa-magnifying-glass-plus"></i> تكبير الشاشة (Step 2)</div>
                        </div>

                        <!-- Shot 3 -->
                        <div class="bz-screen-thumb-container" onclick="openLightbox('{{ asset('images/guide/shot3_step3_pricing.png') }}', 'الخطوة 3: السياسة السعرية وحساب الضرائب والهامش', 'تحديد سعر البيع، احتساب ضريبة القيمة المضافة، وتكلفة الاستيراد لحساب هامش الربح.', 3)">
                            <img src="{{ asset('images/guide/shot3_step3_pricing.png') }}" alt="Step 3 Pricing" class="bz-screen-thumb-img" loading="lazy">
                            <div class="bz-zoom-badge"><i class="fa-solid fa-magnifying-glass-plus"></i> تكبير الشاشة (Step 3)</div>
                        </div>

                        <!-- Shot 4 -->
                        <div class="bz-screen-thumb-container" onclick="openLightbox('{{ asset('images/guide/shot4_step4_media.png') }}', 'الخطوة 4: معرض الأصول والوسائط عالية الدقة', 'رفع صورة العبوة المفرغة (Master Packshot) وتوليد الصور المصغرة ومعرض الزوايا.', 4)">
                            <img src="{{ asset('images/guide/shot4_step4_media.png') }}" alt="Step 4 Media" class="bz-screen-thumb-img" loading="lazy">
                            <div class="bz-zoom-badge"><i class="fa-solid fa-magnifying-glass-plus"></i> تكبير الشاشة (Step 4)</div>
                        </div>

                        <!-- Shot 5 -->
                        <div class="bz-screen-thumb-container" onclick="openLightbox('{{ asset('images/guide/shot5_step5_clinical.png') }}', 'الخطوة 5: الملف الطبي والمواصفات السريرية', 'تسجيل المكونات النشطة، العيارات الصيدلانية القياسية، والمسارات الحيوية المستهدفة.', 5)">
                            <img src="{{ asset('images/guide/shot5_step5_clinical.png') }}" alt="Step 5 Clinical" class="bz-screen-thumb-img" loading="lazy">
                            <div class="bz-zoom-badge"><i class="fa-solid fa-magnifying-glass-plus"></i> تكبير الشاشة (Step 5)</div>
                        </div>

                        <!-- Shot 6 -->
                        <div class="bz-screen-thumb-container" onclick="openLightbox('{{ asset('images/guide/shot6_step6_customizer.png') }}', 'الخطوة 6: مخصص أقسام صفحة المنتج والشاشات', 'التحكم المنفصل لظهور الأقسام على أجهزة الكمبيوتر والموبايل وحالة الفتح الافتراضية.', 6)">
                            <img src="{{ asset('images/guide/shot6_step6_customizer.png') }}" alt="Step 6 Customizer" class="bz-screen-thumb-img" loading="lazy">
                            <div class="bz-zoom-badge"><i class="fa-solid fa-magnifying-glass-plus"></i> تكبير الشاشة (Step 6)</div>
                        </div>

                        <!-- Shot 7 -->
                        <div class="bz-screen-thumb-container" onclick="openLightbox('{{ asset('images/guide/shot7_step6_bundle.png') }}', 'الخطوة 6 (موسع): استوديو باقات التآزر الحيوي', 'اختيار المكملات التكميلية وربطها في باقة علاجية واحدة مع خصم مخصص.', 7)">
                            <img src="{{ asset('images/guide/shot7_step6_bundle.png') }}" alt="Step 6 Bundle" class="bz-screen-thumb-img" loading="lazy">
                            <div class="bz-zoom-badge"><i class="fa-solid fa-magnifying-glass-plus"></i> تكبير الشاشة (Bundles)</div>
                        </div>

                        <!-- Shot 8 -->
                        <div class="bz-screen-thumb-container" onclick="openLightbox('{{ asset('images/guide/shot8_step7_inventory.png') }}', 'الخطوة 7: تهيئة المستودعات والمخزون الأولي', 'توزيع المخزون بين المتجر الإلكتروني والمستودع المركزي وتسجيل بيانات التشغيلة الأولى.', 8)">
                            <img src="{{ asset('images/guide/shot8_step7_inventory.png') }}" alt="Step 7 Inventory" class="bz-screen-thumb-img" loading="lazy">
                            <div class="bz-zoom-badge"><i class="fa-solid fa-magnifying-glass-plus"></i> تكبير الشاشة (Step 7)</div>
                        </div>

                        <!-- Shot 9 -->
                        <div class="bz-screen-thumb-container" onclick="openLightbox('{{ asset('images/guide/shot9_hero_slider_overview.png') }}', 'سلايدر الواجهة: مركز التحكم والإعدادات العامة', 'التحكم في زمن الحركة (Delay 5500ms)، تشغيل السلايدر، وإعادة الترتيب بالسحب والإفلات.', 9)">
                            <img src="{{ asset('images/guide/shot9_hero_slider_overview.png') }}" alt="Hero Slider Overview" class="bz-screen-thumb-img" loading="lazy">
                            <div class="bz-zoom-badge"><i class="fa-solid fa-magnifying-glass-plus"></i> تكبير السلايدر</div>
                        </div>

                        <!-- Shot 10 -->
                        <div class="bz-screen-thumb-container lg:col-span-3 sm:col-span-2" onclick="openLightbox('{{ asset('images/guide/shot10_hero_slide_modal.png') }}', 'سلايدر الواجهة: محرر الشريحة والمحاكاة الحية', 'ربط المنتج التلقائي، ضبط نصوص التوهج التدرجي، وتنسيق أزرار الطلب مع معاينة مباشرة.', 10)">
                            <img src="{{ asset('images/guide/shot10_hero_slide_modal.png') }}" alt="Hero Slide Modal Simulator" class="bz-screen-thumb-img" style="max-height: 380px; object-fit: cover; object-position: top;" loading="lazy">
                            <div class="bz-zoom-badge"><i class="fa-solid fa-magnifying-glass-plus"></i> تكبير محرر الشريحة والمحاكاة الحية</div>
                        </div>
                    </div>
                </div>

                <!-- Database, Models & Behind-the-Scenes Architecture Table -->
                <div class="overflow-x-auto pt-2">
                    <table class="w-full text-xs text-right border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200 dark:border-[#1e3a52] bg-slate-50 dark:bg-[#061a2b] text-slate-700 dark:text-slate-300 font-bold">
                                <th class="p-3">العنصر / الجدول</th>
                                <th class="p-3">المتحكم / النموذج</th>
                                <th class="p-3">الحقول الرئيسية</th>
                                <th class="p-3">الإجراء البرمجي التلقائي</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-[#15344e] text-slate-600 dark:text-slate-300 font-medium">
                            <tr>
                                <td class="p-3 font-mono font-bold text-sky-400">products</td>
                                <td class="p-3">Product / ProductController</td>
                                <td class="p-3 font-mono text-[11px]">sku, slug, price, sale_price, section_settings (JSON)</td>
                                <td class="p-3">إنشاء مسودة تلقائية (Auto-Draft) عبر AJAX، وتوليد حركات المخزون المزدوجة فورياً.</td>
                            </tr>
                            <tr>
                                <td class="p-3 font-mono font-bold text-amber-400">hero_slides</td>
                                <td class="p-3">HeroSlide / HeroSlideController</td>
                                <td class="p-3 font-mono text-[11px]">product_id, title_highlight_color, sort_order, is_active</td>
                                <td class="p-3">ربط المنتج تلقائياً، وتحديث الترتيب اللحظي بالسحب والإفلات عبر <code>POST /hero-slider/reorder</code>.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ================================================================= -->
            <!-- MODULE 2: PURCHASING & SOURCING CYCLE -->
            <!-- ================================================================= -->
            <div class="bz-guide-card p-6 sm:p-8 space-y-6 guide-module-section" data-module="purchases">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 dark:border-[#1e3a52] pb-5">
                    <div class="flex items-center gap-3.5">
                        <span class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#059669] to-[#10b981] text-white flex items-center justify-center text-xl shadow-lg shadow-[#059669]/30 flex-shrink-0">
                            <i class="fa-solid fa-cart-flatbed"></i>
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="badge text-[10px] font-black uppercase tracking-wider bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 px-2 py-0.5 rounded-full">PROCUREMENT ERP</span>
                                <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">
                                    {{ app()->getLocale() === 'ar' ? '2. دورة المشتريات والموردين وسندات الاستلام (Procurement Lifecycle)' : '2. Purchases, Sourcing & Goods Receiving' }}
                                </h2>
                            </div>
                            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-medium mt-0.5">
                                {{ app()->getLocale() === 'ar' ? 'المسارات: /admin/suppliers | /admin/purchase-orders | /admin/goods-receipts' : 'Routes: /admin/suppliers | /admin/purchase-orders | /admin/goods-receipts' }}
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('admin.purchase-orders.index') }}" class="btn btn-sm btn-outline-primary" target="_blank">
                        <i class="fa-solid fa-file-invoice text-xs"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'أوامر الشراء' : 'Purchase Orders' }}</span>
                    </a>
                </div>

                <!-- Procurement Flowchart -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                    <div class="bz-cycle-step">
                        <div class="text-xs font-mono font-bold text-emerald-400 mb-1">01. المورد والاعتماد (SUP)</div>
                        <div class="font-bold text-sm text-slate-900 dark:text-white mb-1">تسجيل المورد وشهادات cGMP</div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                            إدخال اسم المورد، العملة المالية، مدة التوريد، وصلاحية التدقيق المعتمدة لسنة كاملة، مع رمز الدولة المعياري (ISO2).
                        </p>
                    </div>

                    <div class="bz-cycle-step">
                        <div class="text-xs font-mono font-bold text-sky-400 mb-1">02. عروض الأسعار (RFQ)</div>
                        <div class="font-bold text-sm text-slate-900 dark:text-white mb-1">المفاضلة السعرية والتحليل</div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                            مقارنة عروض أسعار الموردين للمواد الخام أو المنتجات التجارية وتحديد السعر الأفضل وتكاليف الشحن.
                        </p>
                    </div>

                    <div class="bz-cycle-step">
                        <div class="text-xs font-mono font-bold text-amber-400 mb-1">03. أمر الشراء (PO)</div>
                        <div class="font-bold text-sm text-slate-900 dark:text-white mb-1">الاعتماد المالي وتكاليف الوصول</div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                            إصدار أمر الشراء المعتمد، تثبيت سعر الصرف FX Rate، وحساب تكاليف الوصول (Landed Cost Breakdown).
                        </p>
                    </div>

                    <div class="bz-cycle-step">
                        <div class="text-xs font-mono font-bold text-teal-400 mb-1">04. سند الاستلام (GRN)</div>
                        <div class="font-bold text-sm text-slate-900 dark:text-white mb-1">الفحص والتخزين في الحجر</div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                            فحص الأختام ومسجلات الحرارة (Data Loggers)، وإيداع الشحنة في مخزن الحجر الصحي بانتظار فحص الجودة (QC).
                        </p>
                    </div>
                </div>

                <!-- Key Rules & Architecture Directives -->
                <div class="p-4 rounded-xl bg-slate-50 dark:bg-[#061a2b] border border-slate-200 dark:border-[#1e3a52] space-y-2">
                    <div class="font-bold text-xs text-emerald-500 uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-shield-halved"></i>
                        <span>قواعد النزاهة المحاسبية والمخزنية في المشتريات (Integrity Directives):</span>
                    </div>
                    <ul class="text-xs text-slate-600 dark:text-slate-300 space-y-1.5 list-disc list-inside">
                        <li><strong>منع الاستلام المباشر للمخزن التام:</strong> أي شحنة مواد خام أو منتجات يتم إدخالها أولاً في مستودع الحجر الصحي (Quarantine Warehouse) ولا تضاف للمخزون الصالح للبيع إلا بعد اعتماد نتيجة فحص الجودة (QC Inspection).</li>
                        <li><strong>تسجيل التشغيلة (Batch/Lot Creation):</strong> يُنشئ النظام تلقائياً رقم تشغيلة فريد مع تاريخ الصلاحية وسعر التكلفة الفعلية للوحدة شاملاً مصاريف الشحن والجمارك.</li>
                    </ul>
                </div>
            </div>

            <!-- ================================================================= -->
            <!-- MODULE 3: UNIFIED INVENTORY & WAREHOUSE BUFFERS -->
            <!-- ================================================================= -->
            <div class="bz-guide-card p-6 sm:p-8 space-y-6 guide-module-section" data-module="inventory">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 dark:border-[#1e3a52] pb-5">
                    <div class="flex items-center gap-3.5">
                        <span class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#d97706] to-[#f59e0b] text-white flex items-center justify-center text-xl shadow-lg shadow-[#d97706]/30 flex-shrink-0">
                            <i class="fa-solid fa-warehouse"></i>
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="badge text-[10px] font-black uppercase tracking-wider bg-amber-500/20 text-amber-400 border border-amber-500/30 px-2 py-0.5 rounded-full">INVENTORY & LOT TRACKING</span>
                                <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">
                                    {{ app()->getLocale() === 'ar' ? '3. المخزون الموحد، التوزيع المزدوج وتتبع الصلاحيات' : '3. Unified Inventory, Dual Buffers & Lot Expiry Tracking' }}
                                </h2>
                            </div>
                            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-medium mt-0.5">
                                {{ app()->getLocale() === 'ar' ? 'المسارات: /admin/inventory | /admin/warehouses | /admin/batches' : 'Routes: /admin/inventory | /admin/warehouses | /admin/batches' }}
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('admin.inventory.index') }}" class="btn btn-sm btn-outline-primary" target="_blank">
                        <i class="fa-solid fa-boxes-stacked text-xs"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'إدارة المخزون' : 'Inventory Hub' }}</span>
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="p-4 rounded-xl border border-slate-200 dark:border-[#1e3a52] bg-slate-50/50 dark:bg-[#061a2b]/50 space-y-2">
                        <div class="flex items-center gap-2 text-sky-400 font-bold text-sm">
                            <i class="fa-solid fa-globe"></i>
                            <span>مخزن المتجر السريع (Online Buffer)</span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                            المخزون المخصص للبيع المباشر عبر الموقع الإلكتروني؛ يتم خصمه فور تأكيد الطلب إلكترونياً وتحديث العداد في صفحة المنتج.
                        </p>
                    </div>

                    <div class="p-4 rounded-xl border border-slate-200 dark:border-[#1e3a52] bg-slate-50/50 dark:bg-[#061a2b]/50 space-y-2">
                        <div class="flex items-center gap-2 text-emerald-400 font-bold text-sm">
                            <i class="fa-solid fa-building-shield"></i>
                            <span>المستودع المركزي (Central Depot)</span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                            المخزون الاستراتيجي المخصص لطلبات الجملة، الصيدليات، والمشافي؛ يتم الصرف منه عبر أذونات الصرف والتحويل الداخلي.
                        </p>
                    </div>

                    <div class="p-4 rounded-xl border border-slate-200 dark:border-[#1e3a52] bg-slate-50/50 dark:bg-[#061a2b]/50 space-y-2">
                        <div class="flex items-center gap-2 text-amber-400 font-bold text-sm">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                            <span>نظام FEFO لتتبع الصلاحية</span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                            يعتمد النظام مبدأ (First-Expired, First-Out) لصرف التشغيلات الأقرب انتهاءً أولاً، مع تنبيهات استباقية قبل 6 أشهر من الانتهاء.
                        </p>
                    </div>
                </div>
            </div>

            <!-- ================================================================= -->
            <!-- MODULE 4: MANUFACTURING & QUALITY CONTROL cGMP -->
            <!-- ================================================================= -->
            <div class="bz-guide-card p-6 sm:p-8 space-y-6 guide-module-section" data-module="manufacturing">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 dark:border-[#1e3a52] pb-5">
                    <div class="flex items-center gap-3.5">
                        <span class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#7c3aed] to-[#8b5cf6] text-white flex items-center justify-center text-xl shadow-lg shadow-[#7c3aed]/30 flex-shrink-0">
                            <i class="fa-solid fa-industry"></i>
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="badge text-[10px] font-black uppercase tracking-wider bg-purple-500/20 text-purple-400 border border-purple-500/30 px-2 py-0.5 rounded-full">cGMP MANUFACTURING</span>
                                <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">
                                    {{ app()->getLocale() === 'ar' ? '4. التصنيع الدوائي، أوامر التشغيل وفحص الجودة' : '4. Pharmaceutical Manufacturing & cGMP Quality Control' }}
                                </h2>
                            </div>
                            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-medium mt-0.5">
                                {{ app()->getLocale() === 'ar' ? 'المسارات: /admin/work-orders | /admin/qc | /admin/raw-materials' : 'Routes: /admin/work-orders | /admin/qc | /admin/raw-materials' }}
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('admin.work-orders.index') }}" class="btn btn-sm btn-outline-primary" target="_blank">
                        <i class="fa-solid fa-gears text-xs"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'أوامر التشغيل' : 'Work Orders' }}</span>
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                    <div class="bz-cycle-step">
                        <div class="text-xs font-mono font-bold text-purple-400 mb-1">01. صرف المواد الخام</div>
                        <div class="font-bold text-sm text-slate-900 dark:text-white mb-1">فاتورة المواد (BOM)</div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                            صرف المواد الأولية من المستودع بالكميات المحددة بدقة طبقاً لمعايير التركيبة الصيدلانية المعتمدة.
                        </p>
                    </div>

                    <div class="bz-cycle-step">
                        <div class="text-xs font-mono font-bold text-sky-400 mb-1">02. خط الإنتاج والخلط</div>
                        <div class="font-bold text-sm text-slate-900 dark:text-white mb-1">أمر التشغيل (Work Order)</div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                            تتبع مراحل الكبسلة، الخلط، والتغليف الأولي وتحديد نسبة الفاقد الطبيعي (Production Scrap %).
                        </p>
                    </div>

                    <div class="bz-cycle-step">
                        <div class="text-xs font-mono font-bold text-emerald-400 mb-1">03. الفحص المخبري (QC)</div>
                        <div class="font-bold text-sm text-slate-900 dark:text-white mb-1">شهادة النقاء (COA)</div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                            إجراء اختبارات التفتيت، النقاء، والتحليل الميكروبيولوجي لضمان خلو المنتج من المعادن الثقيلة ومطابقة cGMP.
                        </p>
                    </div>

                    <div class="bz-cycle-step">
                        <div class="text-xs font-mono font-bold text-teal-400 mb-1">04. الإيداع المخزني</div>
                        <div class="font-bold text-sm text-slate-900 dark:text-white mb-1">توريد المنتج التام</div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                            إيداع العبوات الجاهزة في المستودع التام وإصدار الباركود التجاري ورقم التشغيلة الرسمي للبيع.
                        </p>
                    </div>
                </div>
            </div>

            <!-- ================================================================= -->
            <!-- MODULE 5: UNIFIED FINANCE & ACCOUNTING ERP -->
            <!-- ================================================================= -->
            <div class="bz-guide-card p-6 sm:p-8 space-y-6 guide-module-section" data-module="finance">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 dark:border-[#1e3a52] pb-5">
                    <div class="flex items-center gap-3.5">
                        <span class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#0d9488] to-[#14b8a6] text-white flex items-center justify-center text-xl shadow-lg shadow-[#0d9488]/30 flex-shrink-0">
                            <i class="fa-solid fa-coins"></i>
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="badge text-[10px] font-black uppercase tracking-wider bg-teal-500/20 text-teal-400 border border-teal-500/30 px-2 py-0.5 rounded-full">FINANCE & LEDGER</span>
                                <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">
                                    {{ app()->getLocale() === 'ar' ? '5. الإدارة المالية، فواتير المبيعات والمشتريات وسندات التحصيل' : '5. Financial Accounting, Invoices & Collections' }}
                                </h2>
                            </div>
                            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-medium mt-0.5">
                                {{ app()->getLocale() === 'ar' ? 'المسارات: /admin/finance/sales-invoices | /admin/finance/collections | /admin/finance/accounts' : 'Routes: /admin/finance/sales-invoices | /admin/finance/collections | /admin/finance/accounts' }}
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('admin.finance.dashboard') }}" class="btn btn-sm btn-outline-primary" target="_blank">
                        <i class="fa-solid fa-chart-line text-xs"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'لوحة تحكم المالية' : 'Finance Dashboard' }}</span>
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="p-4 rounded-xl border border-slate-200 dark:border-[#1e3a52] bg-slate-50/50 dark:bg-[#061a2b]/50 space-y-2">
                        <div class="flex items-center gap-2 text-teal-400 font-bold text-sm">
                            <i class="fa-solid fa-table-list"></i>
                            <span>سندات وقيود التحصيل (Collection Grid)</span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                            شبكة إدخال سريعة شبيهة بالإكسل تتيح للمحاسب تسجيل مقبوضات العملاء، الشيكات، والتحويلات البنكية مع المزامنة التلقائية اللحظية.
                        </p>
                    </div>

                    <div class="p-4 rounded-xl border border-slate-200 dark:border-[#1e3a52] bg-slate-50/50 dark:bg-[#061a2b]/50 space-y-2">
                        <div class="flex items-center gap-2 text-rose-400 font-bold text-sm">
                            <i class="fa-solid fa-money-bill-transfer"></i>
                            <span>سندات الصرف ومستحقات الموردين</span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                            إدارة مدفوعات الموردين وفواتير الشراء وسندات الصرف النقدية مع طباعة سند الصرف المعتمد (Payment Voucher PDF).
                        </p>
                    </div>

                    <div class="p-4 rounded-xl border border-slate-200 dark:border-[#1e3a52] bg-slate-50/50 dark:bg-[#061a2b]/50 space-y-2">
                        <div class="flex items-center gap-2 text-purple-400 font-bold text-sm">
                            <i class="fa-solid fa-building-columns"></i>
                            <span>الحسابات البنكية والصناديق</span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                            متابعة أرصدة الحسابات البنكية بالعملات المختلفة (SAR, USD, EUR)، التحويلات بين الخزائن، وتسوية الحسابات الدورية.
                        </p>
                    </div>
                </div>
            </div>

            <!-- ================================================================= -->
            <!-- MODULE 6: MEDICAL REPRESENTATIVES (MR) & CRM -->
            <!-- ================================================================= -->
            <div class="bz-guide-card p-6 sm:p-8 space-y-6 guide-module-section" data-module="sales">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 dark:border-[#1e3a52] pb-5">
                    <div class="flex items-center gap-3.5">
                        <span class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#0284c7] to-[#0ea5e9] text-white flex items-center justify-center text-xl shadow-lg shadow-[#0284c7]/30 flex-shrink-0">
                            <i class="fa-solid fa-user-doctor"></i>
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="badge text-[10px] font-black uppercase tracking-wider bg-sky-500/20 text-sky-400 border border-sky-500/30 px-2 py-0.5 rounded-full">FIELD MR CRM</span>
                                <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">
                                    {{ app()->getLocale() === 'ar' ? '6. المندوب العلمي الطبي وإدارة علاقات العملاء (MR CRM)' : '6. Medical Representative Field CRM & Pipelines' }}
                                </h2>
                            </div>
                            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-medium mt-0.5">
                                {{ app()->getLocale() === 'ar' ? 'المسارات: /admin/mr/visits | /admin/crm/leads | /admin/customers' : 'Routes: /admin/mr/visits | /admin/crm/leads | /admin/customers' }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="bz-cycle-step">
                        <div class="font-bold text-sm text-slate-900 dark:text-white mb-1">خطة الزيارات الميدانية للأطباء</div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                            جدولة زيارات الأطباء والمراكز الطبية بحسب التخصص والموقع الجغرافي وتحديد المنتجات المستهدفة للشرح.
                        </p>
                    </div>

                    <div class="bz-cycle-step">
                        <div class="font-bold text-sm text-slate-900 dark:text-white mb-1">تسجيل تقرير الزيارة والعينات</div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                            توثيق العينات الطبية المسلّمة للطبيب، اهتمام الطبيب بالمستحضر، وملاحظات الوصفة الطبية (Prescription Feedback).
                        </p>
                    </div>

                    <div class="bz-cycle-step">
                        <div class="font-bold text-sm text-slate-900 dark:text-white mb-1">مسار الفرص البيعية (Pipelines)</div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                            لوحة كانبان تفاعلية لنقل الفرص البيعية من مرحلة التواصل الأولي حتى التعاقد مع الصيدليات والمراكز الطبية.
                        </p>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- 4. APPLE-STYLE SMOOTH LIGHTBOX MODAL WITH ZOOM & NAVIGATION -->
    <!-- ========================================================================= -->
    <div id="guideLightbox" onclick="closeLightbox(event)">
        <div id="guideLightboxContent" onclick="event.stopPropagation()">
            <!-- Top Action Header -->
            <div class="w-full flex items-center justify-between text-white pb-3 px-2">
                <div class="flex items-center gap-3">
                    <span id="lightboxStepBadge" class="px-2.5 py-1 rounded-lg bg-sky-500/20 text-sky-400 border border-sky-400/40 text-xs font-mono font-bold">STEP 01</span>
                    <h3 id="lightboxTitle" class="text-base sm:text-lg font-black text-white">عنوان الخطوة</h3>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" class="w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition" onclick="navigateLightbox(-1)" title="Previous (Left Arrow)">
                        <i class="fa-solid fa-chevron-left text-sm"></i>
                    </button>
                    <button type="button" class="w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition" onclick="navigateLightbox(1)" title="Next (Right Arrow)">
                        <i class="fa-solid fa-chevron-right text-sm"></i>
                    </button>
                    <button type="button" class="w-9 h-9 rounded-full bg-rose-500/80 hover:bg-rose-600 text-white flex items-center justify-center transition ml-2" onclick="forceCloseLightbox()" title="Close (Esc)">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>
            </div>

            <!-- Image Viewport -->
            <img id="lightboxImage" src="" alt="Guide Screenshot">

            <!-- Bottom Action Description Card -->
            <div class="mt-4 p-4 rounded-2xl bg-[#081d2f]/90 border border-sky-500/30 text-white max-w-2xl w-full text-right shadow-2xl backdrop-blur-md">
                <div class="flex items-center gap-2 text-emerald-400 font-bold text-xs mb-1">
                    <i class="fa-solid fa-bolt"></i>
                    <span>الإجراء المنفذ في هذه الشاشة:</span>
                </div>
                <p id="lightboxDescription" class="text-xs sm:text-sm text-slate-200 leading-relaxed font-medium"></p>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 5. JAVASCRIPT LIGHTBOX & REAL-TIME SEARCH SCRIPT -->
    <!-- ========================================================================= -->
    <script>
        // Gallery items database for next/prev navigation
        const galleryItems = [
            {
                url: "{{ asset('images/guide/shot1_step1_core.png') }}",
                title: "الخطوة 1: البيانات الأساسية والرمز والتصنيف",
                desc: "تحديد رمز المنتج (SKU)، الرابط الدائم (Slug)، القسم العلاجي، والعلامة التجارية مع حفظ المسودة تلقائياً.",
                step: 1
            },
            {
                url: "{{ asset('images/guide/shot2_step2_content.png') }}",
                title: "الخطوة 2: المحتوى والوصف ثنائي اللغة",
                desc: "إدخال الاسم والملخص التسويقي والوصف العلمي الموسع باللغتين العربية والإنجليزية.",
                step: 2
            },
            {
                url: "{{ asset('images/guide/shot3_step3_pricing.png') }}",
                title: "الخطوة 3: السياسة السعرية وحساب الضرائب والهامش",
                desc: "تحديد سعر البيع، احتساب ضريبة القيمة المضافة، وتكلفة الاستيراد لحساب هامش الربح الإجمالي في الوقت الفعلي.",
                step: 3
            },
            {
                url: "{{ asset('images/guide/shot4_step4_media.png') }}",
                title: "الخطوة 4: معرض الأصول والوسائط عالية الدقة",
                desc: "رفع صورة العبوة المفرغة (Master Packshot) وتوليد الصور المصغرة WebP ومعرض الزوايا.",
                step: 4
            },
            {
                url: "{{ asset('images/guide/shot5_step5_clinical.png') }}",
                title: "الخطوة 5: الملف الطبي والمواصفات السريرية",
                desc: "تسجيل المكونات النشطة، العيارات الصيدلانية القياسية، والمسارات الحيوية المستهدفة.",
                step: 5
            },
            {
                url: "{{ asset('images/guide/shot6_step6_customizer.png') }}",
                title: "الخطوة 6: مخصص أقسام صفحة المنتج والشاشات",
                desc: "التحكم المنفصل لظهور الأقسام على أجهزة الكمبيوتر والموبايل وحالة الفتح الافتراضية لكل قسم.",
                step: 6
            },
            {
                url: "{{ asset('images/guide/shot7_step6_bundle.png') }}",
                title: "الخطوة 6 (موسع): استوديو باقات التآزر الحيوي",
                desc: "اختيار المكملات التكميلية وربطها في باقة علاجية واحدة مع خصم مخصص وإمكانية الشراء بنقرة واحدة.",
                step: 7
            },
            {
                url: "{{ asset('images/guide/shot8_step7_inventory.png') }}",
                title: "الخطوة 7: تهيئة المستودعات والمخزون الأولي",
                desc: "توزيع المخزون بين المتجر الإلكتروني والمستودع المركزي وتسجيل بيانات تشغيلة الإنتاج الأولى.",
                step: 8
            },
            {
                url: "{{ asset('images/guide/shot9_hero_slider_overview.png') }}",
                title: "سلايدر الواجهة: مركز التحكم والإعدادات العامة",
                desc: "التحكم في زمن الحركة (Delay 5500ms)، تشغيل السلايدر، وإعادة الترتيب بالسحب والإفلات.",
                step: 9
            },
            {
                url: "{{ asset('images/guide/shot10_hero_slide_modal.png') }}",
                title: "سلايدر الواجهة: محرر الشريحة والمحاكاة الحية",
                desc: "ربط المنتج التلقائي، ضبط نصوص التوهج التدرجي، وتنسيق الأزرار مع معاينة مباشرة للهاتف وسطح المكتب.",
                step: 10
            }
        ];

        let currentLightboxIndex = 0;

        function openLightbox(url, title, desc, stepNum) {
            currentLightboxIndex = galleryItems.findIndex(item => item.step === stepNum);
            if (currentLightboxIndex === -1) currentLightboxIndex = 0;
            renderLightboxItem();
            const lb = document.getElementById('guideLightbox');
            lb.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function renderLightboxItem() {
            const item = galleryItems[currentLightboxIndex];
            document.getElementById('lightboxImage').src = item.url;
            document.getElementById('lightboxTitle').textContent = item.title;
            document.getElementById('lightboxDescription').textContent = item.desc;
            document.getElementById('lightboxStepBadge').textContent = 'STEP ' + String(item.step).padStart(2, '0');
        }

        function navigateLightbox(dir) {
            currentLightboxIndex += dir;
            if (currentLightboxIndex < 0) currentLightboxIndex = galleryItems.length - 1;
            if (currentLightboxIndex >= galleryItems.length) currentLightboxIndex = 0;
            renderLightboxItem();
        }

        function closeLightbox(e) {
            if (e.target.id === 'guideLightbox') {
                forceCloseLightbox();
            }
        }

        function forceCloseLightbox() {
            const lb = document.getElementById('guideLightbox');
            lb.classList.remove('active');
            document.body.style.overflow = '';
        }

        // Keyboard Shortcuts
        document.addEventListener('keydown', (e) => {
            const lb = document.getElementById('guideLightbox');
            if (lb.classList.contains('active')) {
                if (e.key === 'Escape') forceCloseLightbox();
                if (e.key === 'ArrowRight') navigateLightbox(1);
                if (e.key === 'ArrowLeft') navigateLightbox(-1);
            }
        });

        // Module Tabs Filter
        function switchGuideTab(mod) {
            document.querySelectorAll('.bz-module-nav-btn').forEach(btn => btn.classList.remove('active'));
            event.currentTarget.classList.add('active');

            const sections = document.querySelectorAll('.guide-module-section');
            sections.forEach(sec => {
                if (mod === 'all' || sec.getAttribute('data-module') === mod) {
                    sec.style.display = 'block';
                } else {
                    sec.style.display = 'none';
                }
            });
        }

        // Live Real-Time Search in Guide
        function filterGuideTopics() {
            const q = document.getElementById('guideSearchInput').value.toLowerCase().trim();
            const sections = document.querySelectorAll('.guide-module-section');
            
            sections.forEach(sec => {
                const text = sec.textContent.toLowerCase();
                if (text.includes(q)) {
                    sec.style.display = 'block';
                } else {
                    sec.style.display = 'none';
                }
            });
        }
    </script>
</x-layouts.admin>
