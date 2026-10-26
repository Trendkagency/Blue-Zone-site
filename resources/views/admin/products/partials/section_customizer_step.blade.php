@php
    $isAr = app()->getLocale() === 'ar';
    $secData = $sectionSettings ?? ($product['section_settings'] ?? []);

    $sections = [
        'dossier' => [
            'num' => '00',
            'icon' => 'fa-folder-open',
            'color' => '#0A4F78',
            'color_light' => 'rgba(10, 79, 120, 0.12)',
            'color_border' => 'rgba(10, 79, 120, 0.35)',
            'name_en' => 'Master Monograph Header & Dossier Container',
            'name_ar' => 'رأس ملف المواصفات السريرية والحاوية الرئيسية',
            'tag' => 'CORE CONTAINER',
            'has_open_state' => false,
            'has_custom_content' => false,
            'default_badge_en' => $detailsSettings['pdetail_dossier_badge_en'] ?? 'CLINICAL DOSSIER & BIO-MOLECULAR MONOGRAPH',
            'default_badge_ar' => $detailsSettings['pdetail_dossier_badge_ar'] ?? 'الملف الطبي والمواصفات السريرية المعتمدة',
            'default_title_en' => $detailsSettings['pdetail_dossier_title_en'] ?? 'Comprehensive Formulation Specifications',
            'default_title_ar' => $detailsSettings['pdetail_dossier_title_ar'] ?? 'التفاصيل الدقيقة والخصائص الدوائية للمستحضر',
            'description_en' => 'Controls the master clinical header container, monograph title badge, and quick-filter pills bar.',
            'description_ar' => 'يتحكم في الحاوية الرئيسية للملف الطبي، شارة الرأس، وشريط التصفية السريع.',
        ],
        'sec1' => [
            'num' => '01',
            'icon' => 'fa-flask-vial',
            'color' => '#67B34A',
            'color_light' => 'rgba(103, 179, 74, 0.12)',
            'color_border' => 'rgba(103, 179, 74, 0.35)',
            'name_en' => 'Active Bio-Compounds & Potencies (Ingredients)',
            'name_ar' => 'المكونات الفعالة والجرعات المعيارية',
            'tag' => 'MONOGRAPH 01',
            'has_open_state' => true,
            'default_open' => true,
            'has_custom_content' => true,
            'default_badge_en' => $detailsSettings['pdetail_sec1_badge_en'] ?? 'Standardized Monograph',
            'default_badge_ar' => $detailsSettings['pdetail_sec1_badge_ar'] ?? 'النقاء والجرعات القياسية',
            'default_title_en' => $detailsSettings['pdetail_sec1_title_en'] ?? 'Active Bio-Compounds & Potencies',
            'default_title_ar' => $detailsSettings['pdetail_sec1_title_ar'] ?? 'المكونات الفعالة والجرعات المعيارية',
            'description_en' => 'Renders the standardized active ingredients table, elemental potencies, and botanical extracts.',
            'description_ar' => 'يعرض جدول المكونات النشطة، العيارات الدوائية، والمستخلصات العشبية القياسية.',
        ],
        'sec2' => [
            'num' => '02',
            'icon' => 'fa-bolt',
            'color' => '#f59e0b',
            'color_light' => 'rgba(245, 158, 11, 0.12)',
            'color_border' => 'rgba(245, 158, 11, 0.35)',
            'name_en' => 'Clinical Benefits & Physiological Targets',
            'name_ar' => 'الفوائد السريرية والمسارات البيولوجية',
            'tag' => 'MONOGRAPH 02',
            'has_open_state' => true,
            'default_open' => false,
            'has_custom_content' => true,
            'default_badge_en' => $detailsSettings['pdetail_sec2_badge_en'] ?? 'Physiological Biomarkers',
            'default_badge_ar' => $detailsSettings['pdetail_sec2_badge_ar'] ?? 'الأثر الفسيولوجي المباشر والمستدام',
            'default_title_en' => $detailsSettings['pdetail_sec2_title_en'] ?? 'Clinical Benefits & Physiological Targets',
            'default_title_ar' => $detailsSettings['pdetail_sec2_title_ar'] ?? 'الفوائد السريرية والمسارات البيولوجية',
            'description_en' => 'Displays verified clinical biomarker trial results, metabolic outcomes, and longevity benefits.',
            'description_ar' => 'يعرض المؤشرات الحيوية السريرية المعتمدة ونتائج التجارب على استعادة النشاط الحيوي.',
        ],
        'sec3' => [
            'num' => '03',
            'icon' => 'fa-calendar-check',
            'color' => '#2A8FC2',
            'color_light' => 'rgba(42, 143, 194, 0.12)',
            'color_border' => 'rgba(42, 143, 194, 0.35)',
            'name_en' => 'Clinical Protocol & Usage Guidelines (Direction of Use)',
            'name_ar' => 'بروتوكول الاستخدام والتوجيهات السريرية',
            'tag' => 'MONOGRAPH 03',
            'has_open_state' => true,
            'default_open' => false,
            'has_custom_content' => true,
            'default_badge_en' => $detailsSettings['pdetail_sec3_badge_en'] ?? 'Synergistic Protocol',
            'default_badge_ar' => $detailsSettings['pdetail_sec3_badge_ar'] ?? 'الجرعات والتكامل الحيوي',
            'default_title_en' => $detailsSettings['pdetail_sec3_title_en'] ?? 'Clinical Protocol & Usage Guidelines',
            'default_title_ar' => $detailsSettings['pdetail_sec3_title_ar'] ?? 'بروتوكول الاستخدام والتوجيهات السريرية',
            'description_en' => 'Step-by-step intake protocol, synergistic timing (morning/evening), and dietary guidance.',
            'description_ar' => 'إرشادات الاستخدام المجدولة زمنياً والتوافق مع الوجبات الغذائية اليومية.',
        ],
        'sec4' => [
            'num' => '04',
            'icon' => 'fa-dna',
            'color' => '#8b5cf6',
            'color_light' => 'rgba(139, 92, 246, 0.12)',
            'color_border' => 'rgba(139, 92, 246, 0.35)',
            'name_en' => 'Cellular Science & Pharmacokinetics (Supplement Facts)',
            'name_ar' => 'الآلية العلمية الدوائية المتقدمة والامتصاص',
            'tag' => 'MONOGRAPH 04',
            'has_open_state' => true,
            'default_open' => false,
            'has_custom_content' => true,
            'default_badge_en' => $detailsSettings['pdetail_sec4_badge_en'] ?? 'Molecular Pharmacokinetics',
            'default_badge_ar' => $detailsSettings['pdetail_sec4_badge_ar'] ?? 'الأبحاث الصيدلانية وطول العمر',
            'default_title_en' => $detailsSettings['pdetail_sec4_title_en'] ?? 'Cellular Science & Mechanism of Action',
            'default_title_ar' => $detailsSettings['pdetail_sec4_title_ar'] ?? 'الآلية العلمية الدوائية المتقدمة',
            'description_en' => 'Cellular pathway activation, NAD+ signaling, bio-availability kinetics, and supplement facts.',
            'description_ar' => 'الآلية الجزيئية ومسارات الطاقة الخلوية والتوافر الحيوي للمركبات النشطة.',
        ],
        'sec5' => [
            'num' => '05',
            'icon' => 'fa-shield-halved',
            'color' => '#10b981',
            'color_light' => 'rgba(16, 185, 129, 0.12)',
            'color_border' => 'rgba(16, 185, 129, 0.35)',
            'name_en' => 'Safety Profile, Warnings & Precautions',
            'name_ar' => 'إرشادات السلامة، التحذيرات وموانع الاستعمال',
            'tag' => 'MONOGRAPH 05',
            'has_open_state' => true,
            'default_open' => false,
            'has_custom_content' => true,
            'default_badge_en' => $detailsSettings['pdetail_sec5_badge_en'] ?? 'Pharmacovigilance & Safety',
            'default_badge_ar' => $detailsSettings['pdetail_sec5_badge_ar'] ?? 'السلامة والتحذيرات الطبية',
            'default_title_en' => $detailsSettings['pdetail_sec5_title_en'] ?? 'Safety Profile & Precautionary Directives',
            'default_title_ar' => $detailsSettings['pdetail_sec5_title_ar'] ?? 'إرشادات السلامة، التحذيرات وموانع الاستعمال',
            'description_en' => 'Safety certifications, allergen declarations, contraindications, and medical warnings.',
            'description_ar' => 'توجيهات السلامة الصيدلانية، موانع الاستعمال، والتحذيرات المعتمدة.',
        ],
        'sec6' => [
            'num' => '06',
            'icon' => 'fa-award',
            'color' => '#6366f1',
            'color_light' => 'rgba(99, 102, 241, 0.12)',
            'color_border' => 'rgba(99, 102, 241, 0.35)',
            'name_en' => 'Bio-Certifications & Quality Assurances (c罩P Standards)',
            'name_ar' => 'شهادات الجودة، الاعتمادات الدوائية وتراخيص cGMP',
            'tag' => 'MONOGRAPH 06',
            'has_open_state' => true,
            'default_open' => false,
            'has_custom_content' => true,
            'default_badge_en' => $detailsSettings['pdetail_sec6_badge_en'] ?? 'c罩P Certified Formulations',
            'default_badge_ar' => $detailsSettings['pdetail_sec6_badge_ar'] ?? 'الاعتمادات الدوائية الموثقة',
            'default_title_en' => $detailsSettings['pdetail_sec6_title_en'] ?? 'Bio-Certifications & Quality Assurances',
            'default_title_ar' => $detailsSettings['pdetail_sec6_title_ar'] ?? 'شهادات الجودة، الاعتمادات الدوائية وتراخيص cGMP',
            'description_en' => 'Displays ISO/cGMP certification badges, third-party lab assay reports, and quality audits.',
            'description_ar' => 'يعرض شهادات الاعتماد العالمية، معايير cGMP، واختبارات النقاء المخبرية.',
        ],
        'buy_box' => [
            'num' => '07',
            'icon' => 'fa-cart-shopping',
            'color' => '#0A4F78',
            'color_light' => 'rgba(10, 79, 120, 0.12)',
            'color_border' => 'rgba(10, 79, 120, 0.35)',
            'name_en' => 'Commercial Buy Box & Order Action Container',
            'name_ar' => 'صندوق الشراء والطلب التجاري (السعر، الكمية، زر الإضافة للسلة)',
            'tag' => 'COMMERCIAL HUB',
            'has_open_state' => false,
            'has_custom_content' => false,
            'default_badge_en' => '',
            'default_badge_ar' => '',
            'default_title_en' => '',
            'default_title_ar' => '',
            'description_en' => 'The primary commercial action area: dynamic pricing, quantity selector, add-to-cart, and stock status.',
            'description_ar' => 'منطقة الطلب المباشر: التسعير، محدد الكمية، وزر الإضافة لسلة التسوق.',
        ],
        'related' => [
            'num' => '08',
            'icon' => 'fa-layer-group',
            'color' => '#ec4899',
            'color_light' => 'rgba(236, 72, 153, 0.12)',
            'color_border' => 'rgba(236, 72, 153, 0.35)',
            'name_en' => 'Synergistic Longevity Protocols (Related Formulations)',
            'name_ar' => 'التكامل الحيوي (التركيبات التكميلية المقترحة)',
            'tag' => 'RECOMMENDATIONS',
            'has_open_state' => false,
            'has_custom_content' => false,
            'default_badge_en' => $detailsSettings['pdetail_related_badge_en'] ?? 'SYNERGISTIC LONGEVITY PROTOCOLS',
            'default_badge_ar' => $detailsSettings['pdetail_related_badge_ar'] ?? 'التكامل الحيوي',
            'default_title_en' => $detailsSettings['pdetail_related_title_en'] ?? 'Complementary Formulations',
            'default_title_ar' => $detailsSettings['pdetail_related_title_ar'] ?? 'تركيبات متكاملة تعزز النتائج',
            'description_en' => 'Recommended complementary formulations that act synergistically with this bioceutical.',
            'description_ar' => 'المستحضرات التكميلية الموصى بها لتعزيز النتائج الصحية والوقائية.',
        ],
    ];
@endphp

<style>
    /* ==========================================================
       BLUE ZONE ULTIMATE SECTION CUSTOMIZER STUDIO ENGINE
       ========================================================== */
    .bz-customizer-scope-btn.active {
        background-color: #ffffff;
        color: #0A4F78;
        box-shadow: 0 4px 12px -2px rgba(10, 79, 120, 0.25);
    }
    .dark .bz-customizer-scope-btn.active {
        background-color: #0A4F78;
        color: #ffffff;
        box-shadow: 0 4px 12px -2px rgba(0, 0, 0, 0.5);
    }

    /* Tactile Apple-Style Toggle Switch Pill */
    .bz-switch-pill {
        position: relative;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.35rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 700;
        cursor: pointer;
        user-select: none;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        border: 1.5px solid transparent;
    }
    .bz-switch-pill input[type="checkbox"] {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
        pointer-events: none;
    }
    .bz-switch-pill .bz-switch-thumb {
        width: 1.15rem;
        height: 1.15rem;
        border-radius: 9999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 9px;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 1px 3px rgba(0,0,0,0.15);
    }

    /* Desktop Switch */
    .bz-switch-pill.desktop-switch.is-active {
        background-color: rgba(10, 79, 120, 0.12);
        border-color: rgba(10, 79, 120, 0.55);
        color: #0A4F78;
    }
    .dark .bz-switch-pill.desktop-switch.is-active {
        background-color: rgba(42, 143, 194, 0.18);
        border-color: rgba(42, 143, 194, 0.6);
        color: #2A8FC2;
    }
    .bz-switch-pill.desktop-switch.is-active .bz-switch-thumb {
        background-color: #0A4F78;
        color: #ffffff;
    }
    .dark .bz-switch-pill.desktop-switch.is-active .bz-switch-thumb {
        background-color: #2A8FC2;
        color: #031827;
    }

    /* Mobile Switch */
    .bz-switch-pill.mobile-switch.is-active {
        background-color: rgba(103, 179, 74, 0.12);
        border-color: rgba(103, 179, 74, 0.55);
        color: #4d8736;
    }
    .dark .bz-switch-pill.mobile-switch.is-active {
        background-color: rgba(103, 179, 74, 0.18);
        border-color: rgba(103, 179, 74, 0.6);
        color: #B8D98A;
    }
    .bz-switch-pill.mobile-switch.is-active .bz-switch-thumb {
        background-color: #67B34A;
        color: #ffffff;
    }

    /* Open by Default Switch */
    .bz-switch-pill.open-switch.is-active {
        background-color: rgba(139, 92, 246, 0.12);
        border-color: rgba(139, 92, 246, 0.55);
        color: #7c3aed;
    }
    .dark .bz-switch-pill.open-switch.is-active {
        background-color: rgba(139, 92, 246, 0.2);
        border-color: rgba(139, 92, 246, 0.6);
        color: #c4b5fd;
    }
    .bz-switch-pill.open-switch.is-active .bz-switch-thumb {
        background-color: #8b5cf6;
        color: #ffffff;
    }

    /* Inactive State Generic */
    .bz-switch-pill.is-inactive {
        background-color: rgba(148, 163, 184, 0.1);
        border-color: rgba(148, 163, 184, 0.3);
        color: #94a3b8;
    }
    .dark .bz-switch-pill.is-inactive {
        background-color: rgba(15, 23, 42, 0.6);
        border-color: rgba(51, 65, 85, 0.6);
        color: #64748b;
    }
    .bz-switch-pill.is-inactive .bz-switch-thumb {
        background-color: #cbd5e1;
        color: #64748b;
    }
    .dark .bz-switch-pill.is-inactive .bz-switch-thumb {
        background-color: #334155;
        color: #94a3b8;
    }

    /* Master Section Card */
    .bz-customizer-card {
        transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.2s ease, opacity 0.25s ease;
    }
    .bz-customizer-card:hover {
        box-shadow: 0 12px 30px -8px rgba(10, 79, 120, 0.12);
    }
    .bz-customizer-card.is-dimmed {
        opacity: 0.35 !important;
        filter: grayscale(80%);
    }

    /* Pulse animation */
    @keyframes bzPulseGreen {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.3; transform: scale(1.35); }
    }
    .bz-live-dot {
        animation: bzPulseGreen 2s infinite ease-in-out;
    }
</style>

<div class="space-y-6">
    <!-- ==========================================================
         TOP EXECUTIVE BANNER
         ========================================================== -->
    <div class="p-6 sm:p-8 rounded-3xl bg-gradient-to-br from-[#02131F] via-[#062B49] to-[#0A4F78] text-white shadow-2xl relative overflow-hidden border border-white/10">
        <!-- Ambient Glowing Orbs -->
        <div class="absolute -right-16 -top-16 w-72 h-72 rounded-full bg-[#67B34A]/25 blur-3xl pointer-events-none"></div>
        <div class="absolute right-1/3 -bottom-20 w-80 h-80 rounded-full bg-[#2A8FC2]/20 blur-3xl pointer-events-none"></div>
        
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-3">
                <div class="flex items-center gap-2.5 flex-wrap">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#67B34A]/25 text-[#B8D98A] font-mono text-[11px] font-black uppercase tracking-wider border border-[#67B34A]/30 shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-[#67B34A] bz-live-dot"></span>
                        {{ $isAr ? 'الخطوة 6 من 7' : 'STEP 6 OF 7' }}
                    </span>
                    <span class="px-3 py-1 rounded-full bg-white/10 text-white font-mono text-[11px] font-bold border border-white/15 backdrop-blur-md">
                        <i class="fa-solid fa-layer-group text-[#2A8FC2] mr-1.5 ml-1.5"></i>
                        {{ $isAr ? 'استوديو استجابة الشاشات والأقسام' : 'RESPONSIVE VIEWPORT STUDIO' }}
                    </span>
                    <span class="px-3 py-1 rounded-full bg-white/5 text-white/80 font-mono text-[11px] font-semibold border border-white/10">
                        8 {{ $isAr ? 'أقسام سريرية وتجارية' : 'CLINICAL & COMMERCIAL SECTIONS' }}
                    </span>
                </div>

                <div>
                    <h2 class="text-2xl sm:text-3xl font-black tracking-tight text-white flex items-center gap-2.5">
                        <span>{{ $isAr ? 'تخصيص هيكل ومظهر صفحة تفاصيل المنتج' : 'Product Details Page & Responsive Section Customizer' }}</span>
                    </h2>
                    <p class="text-xs sm:text-sm text-white/80 mt-1 max-w-2xl leading-relaxed font-normal">
                        {{ $isAr 
                            ? 'تحكم هندسي شامل في تفاصيل صفحة المنتج: تخصيص الظهور حسب نوع الشاشة (كمبيوتر 🖥️ أو هاتف 📱)، تحديد حالة الطي والتوسيع التلقائي للأكورديون، تعديل العناوين والشارات التعريفيّة بلغتين، أو حقن محتوى سريري بديل مخصص بالكامل.' 
                            : 'Granular engineering control over every public product page section: toggle viewport visibility (Desktop 🖥️ vs Mobile 📱), control initial accordion expansion, override bilingual clinical titles & badges, or inject bespoke HTML.' }}
                    </p>
                </div>
            </div>

            <!-- Global Action Presets Toolbar -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 shrink-0 bg-white/10 p-2 rounded-2xl border border-white/15 backdrop-blur-md shadow-lg">
                <button type="button" onclick="customizerApplyPreset('expand_cards')" 
                        class="px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold transition-all flex items-center justify-center gap-2 cursor-pointer border border-white/15 shadow-sm active:scale-95"
                        title="{{ $isAr ? 'توسيع جميع بطاقات التعديل' : 'Expand all section editing panels' }}">
                    <i class="fa-solid fa-angles-down text-[#67B34A]"></i>
                    <span>{{ $isAr ? 'توسيع البطاقات' : 'Expand All' }}</span>
                </button>
                <button type="button" onclick="customizerApplyPreset('collapse_cards')" 
                        class="px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold transition-all flex items-center justify-center gap-2 cursor-pointer border border-white/15 shadow-sm active:scale-95"
                        title="{{ $isAr ? 'طي جميع بطاقات التعديل' : 'Collapse all section editing panels' }}">
                    <i class="fa-solid fa-angles-up text-amber-400"></i>
                    <span>{{ $isAr ? 'طي البطاقات' : 'Collapse All' }}</span>
                </button>
                <button type="button" onclick="customizerApplyPreset('enable_all')" 
                        class="px-4 py-2 rounded-xl bg-[#67B34A] hover:bg-[#5aa13f] text-white text-xs font-black transition-all flex items-center justify-center gap-2 cursor-pointer shadow-md active:scale-95">
                    <i class="fa-solid fa-check-double"></i>
                    <span>{{ $isAr ? 'تفعيل الكل' : 'Enable All' }}</span>
                </button>
                <button type="button" onclick="openLiveProductPreview()" 
                        class="px-3.5 py-2 rounded-xl bg-[#2A8FC2] hover:bg-[#2277a3] text-white text-xs font-black transition-all flex items-center justify-center gap-2 cursor-pointer shadow-md active:scale-95">
                    <i class="fa-solid fa-eye"></i>
                    <span>{{ $isAr ? 'معاينة حية' : 'Live Preview' }}</span>
                </button>
            </div>
        </div>
    </div>

    <!-- ==========================================================
         DEVICE PREVIEW SCOPE FILTER & STATS BAR
         ========================================================== -->
    <div class="flex items-center justify-between gap-4 p-4 rounded-2xl bg-white dark:bg-[#062B49] border border-slate-200/90 dark:border-slate-800 shadow-sm flex-wrap">
        <div class="flex items-center gap-3 flex-wrap">
            <span class="text-xs font-black text-slate-500 uppercase tracking-wider flex items-center gap-1.5 ml-1 mr-1">
                <i class="fa-solid fa-sliders text-[#0A4F78] dark:text-[#2A8FC2]"></i>
                <span>{{ $isAr ? 'نطاق معاينة الأجهزة:' : 'DEVICE PREVIEW SCOPE:' }}</span>
            </span>

            <div class="inline-flex rounded-xl p-1 bg-slate-100 dark:bg-[#031827] border border-slate-200 dark:border-slate-700 shadow-inner">
                <button type="button" onclick="filterCustomizerView('all', this)" 
                        class="bz-customizer-scope-btn active px-4 py-1.5 rounded-lg text-xs font-extrabold transition-all cursor-pointer flex items-center gap-1.5">
                    <i class="fa-solid fa-globe text-[11px]"></i>
                    <span>{{ $isAr ? 'جميع الأجهزة' : 'All Devices' }}</span>
                    <span class="px-1.5 py-0.2 rounded-full bg-[#0A4F78]/10 dark:bg-white/20 text-[10px] font-mono">8</span>
                </button>
                <button type="button" onclick="filterCustomizerView('desktop', this)" 
                        class="bz-customizer-scope-btn px-4 py-1.5 rounded-lg text-xs font-extrabold text-slate-600 dark:text-slate-400 hover:text-[#0A4F78] dark:hover:text-white transition-all cursor-pointer flex items-center gap-1.5">
                    <i class="fa-solid fa-display text-[11px]"></i>
                    <span>{{ $isAr ? 'سطح المكتب' : 'Desktop' }}</span>
                </button>
                <button type="button" onclick="filterCustomizerView('mobile', this)" 
                        class="bz-customizer-scope-btn px-4 py-1.5 rounded-lg text-xs font-extrabold text-slate-600 dark:text-slate-400 hover:text-[#0A4F78] dark:hover:text-white transition-all cursor-pointer flex items-center gap-1.5">
                    <i class="fa-solid fa-mobile-screen text-[11px]"></i>
                    <span>{{ $isAr ? 'الهواتف الذكية' : 'Mobile' }}</span>
                </button>
            </div>
        </div>

        <div class="text-[11px] font-medium text-slate-500 dark:text-slate-400 flex items-center gap-2">
            <span class="w-5 h-5 rounded-full bg-[#2A8FC2]/15 text-[#2A8FC2] flex items-center justify-center text-[10px]">
                <i class="fa-solid fa-circle-info"></i>
            </span>
            <span>{{ $isAr ? 'الحقول الفارغة ترث تلقائياً النصوص والبادجات العامة للمتجر.' : 'Empty fields automatically inherit global defaults.' }}</span>
        </div>
    </div>

    <!-- ==========================================================
         MASTER SECTION CARDS LIST
         ========================================================== -->
    <div class="space-y-4" id="customizerSectionCardsContainer">
        @foreach($sections as $sKey => $sDef)
            @php
                $sConf = $secData[$sKey] ?? [];
                
                $enDesktop = array_key_exists('enabled_desktop', $sConf) ? (bool) $sConf['enabled_desktop'] : true;
                $enMobile = array_key_exists('enabled_mobile', $sConf) ? (bool) $sConf['enabled_mobile'] : true;
                $defOpen = array_key_exists('default_open', $sConf) ? (bool) $sConf['default_open'] : ($sDef['default_open'] ?? false);
                $hasCustom = !empty($sConf['custom_content_enabled']);
                $contentEn = $sConf['custom_content_en'] ?? '';
                $contentAr = $sConf['custom_content_ar'] ?? '';

                $titleEn = $sConf['title_en'] ?? '';
                $titleAr = $sConf['title_ar'] ?? '';
                $badgeEn = $sConf['badge_en'] ?? '';
                $badgeAr = $sConf['badge_ar'] ?? '';

                // Determine whether to start expanded (first 2 sections open by default)
                $isCardDrawerOpen = in_array($sKey, ['dossier', 'sec1']);
            @endphp

            <div class="bz-customizer-card rounded-3xl bg-white dark:bg-[#031827] border border-slate-200/90 dark:border-slate-800 shadow-sm relative overflow-hidden" 
                 id="card-sec-{{ $sKey }}" data-section-key="{{ $sKey }}"
                 style="border-inline-start: 6px solid {{ $sDef['color'] }};">
                
                <!-- Card Header Mini-Dashboard -->
                <div class="p-5 sm:p-6 flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800/80 cursor-pointer"
                     onclick="toggleSectionCardDrawer('{{ $sKey }}')">
                    
                    <!-- Left: Section Icon, Tag, Title, Subtitle -->
                    <div class="flex items-start sm:items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl shrink-0 shadow-sm border transition-transform duration-300" 
                             style="background-color: {{ $sDef['color_light'] }}; border-color: {{ $sDef['color_border'] }}; color: {{ $sDef['color'] }};">
                            <i class="fa-solid {{ $sDef['icon'] }}"></i>
                        </div>

                        <div>
                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                <span class="px-2.5 py-0.5 rounded-full font-mono text-[10px] font-black uppercase tracking-wider" 
                                      style="background-color: {{ $sDef['color_light'] }}; color: {{ $sDef['color'] }}; border: 1px solid {{ $sDef['color_border'] }};">
                                    {{ $sDef['tag'] }}
                                </span>
                                <span class="text-[10px] font-mono text-slate-400 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-md">
                                    #{{ $sDef['num'] }}
                                </span>
                                <span class="text-[11px] font-mono text-[#0A4F78] dark:text-[#2A8FC2] bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-md font-bold">
                                    {{ $sKey }}
                                </span>

                                <!-- Live Viewport Summary Status Chip -->
                                <span class="bz-status-chip text-[10px] font-mono font-bold px-2 py-0.5 rounded-full {{ ($enDesktop && $enMobile) ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' : (($enDesktop || $enMobile) ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20' : 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20') }}"
                                      id="status-chip-{{ $sKey }}">
                                    @if($enDesktop && $enMobile)
                                        <i class="fa-solid fa-check text-[9px] mr-1 ml-1"></i> {{ $isAr ? 'مفعل على جميع الشاشات' : 'All Viewports' }}
                                    @elseif($enDesktop)
                                        <i class="fa-solid fa-display text-[9px] mr-1 ml-1"></i> {{ $isAr ? 'كمبيوتر فقط' : 'Desktop Only' }}
                                    @elseif($enMobile)
                                        <i class="fa-solid fa-mobile-screen text-[9px] mr-1 ml-1"></i> {{ $isAr ? 'موبايل فقط' : 'Mobile Only' }}
                                    @else
                                        <i class="fa-solid fa-eye-slash text-[9px] mr-1 ml-1"></i> {{ $isAr ? 'معطل بالكامل' : 'Hidden Everywhere' }}
                                    @endif
                                </span>
                            </div>

                            <h3 class="text-base sm:text-lg font-black text-slate-900 dark:text-white tracking-tight">
                                {{ $isAr ? $sDef['name_ar'] : $sDef['name_en'] }}
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 leading-relaxed">
                                {{ $isAr ? $sDef['description_ar'] : $sDef['description_en'] }}
                            </p>
                        </div>
                    </div>

                    <!-- Right: Modern Interactive Toggle Switches & Expand Chevron -->
                    <div class="flex items-center gap-2.5 flex-wrap shrink-0" onclick="event.stopPropagation()">
                        
                        <!-- Desktop Visibility Toggle Switch -->
                        <label class="bz-switch-pill desktop-switch {{ $enDesktop ? 'is-active' : 'is-inactive' }}" 
                               id="pill-desktop-{{ $sKey }}"
                               title="{{ $isAr ? 'تفعيل أو إخفاء القسم على شاشات الكمبيوتر' : 'Toggle visibility on Desktop viewports' }}">
                            <input type="hidden" name="section_settings[{{ $sKey }}][enabled_desktop]" value="0">
                            <input type="checkbox" name="section_settings[{{ $sKey }}][enabled_desktop]" value="1" 
                                   class="customizer-desktop-toggle sr-only" 
                                   id="input-desktop-{{ $sKey }}"
                                   onchange="handleSwitchChange(this, 'desktop', '{{ $sKey }}')"
                                   {{ $enDesktop ? 'checked' : '' }}>
                            <span class="bz-switch-thumb">
                                <i class="fa-solid {{ $enDesktop ? 'fa-check' : 'fa-xmark' }}"></i>
                            </span>
                            <i class="fa-solid fa-display text-xs"></i>
                            <span class="switch-label">{{ $isAr ? 'كمبيوتر' : 'Desktop' }}</span>
                        </label>

                        <!-- Mobile Visibility Toggle Switch -->
                        <label class="bz-switch-pill mobile-switch {{ $enMobile ? 'is-active' : 'is-inactive' }}" 
                               id="pill-mobile-{{ $sKey }}"
                               title="{{ $isAr ? 'تفعيل أو إخفاء القسم على شاشات الهواتف' : 'Toggle visibility on Mobile viewports' }}">
                            <input type="hidden" name="section_settings[{{ $sKey }}][enabled_mobile]" value="0">
                            <input type="checkbox" name="section_settings[{{ $sKey }}][enabled_mobile]" value="1" 
                                   class="customizer-mobile-toggle sr-only" 
                                   id="input-mobile-{{ $sKey }}"
                                   onchange="handleSwitchChange(this, 'mobile', '{{ $sKey }}')"
                                   {{ $enMobile ? 'checked' : '' }}>
                            <span class="bz-switch-thumb">
                                <i class="fa-solid {{ $enMobile ? 'fa-check' : 'fa-xmark' }}"></i>
                            </span>
                            <i class="fa-solid fa-mobile-screen text-xs"></i>
                            <span class="switch-label">{{ $isAr ? 'موبايل' : 'Mobile' }}</span>
                        </label>

                        @if($sDef['has_open_state'])
                            <!-- Default Open State Toggle Switch -->
                            <label class="bz-switch-pill open-switch {{ $defOpen ? 'is-active' : 'is-inactive' }}" 
                                   id="pill-open-{{ $sKey }}"
                                   title="{{ $isAr ? 'فتح الأكورديون تلقائياً عند تحميل الصفحة' : 'Open accordion panel automatically upon page load' }}">
                                <input type="hidden" name="section_settings[{{ $sKey }}][default_open]" value="0">
                                <input type="checkbox" name="section_settings[{{ $sKey }}][default_open]" value="1" 
                                       class="customizer-open-toggle sr-only" 
                                       id="input-open-{{ $sKey }}"
                                       onchange="handleSwitchChange(this, 'open', '{{ $sKey }}')"
                                       {{ $defOpen ? 'checked' : '' }}>
                                <span class="bz-switch-thumb">
                                    <i class="fa-solid {{ $defOpen ? 'fa-bolt' : 'fa-folder-closed' }}"></i>
                                </span>
                                <i class="fa-solid fa-folder-open text-xs"></i>
                                <span class="switch-label">{{ $isAr ? 'مفتوح تلقائياً' : 'Auto-Open' }}</span>
                            </label>
                        @endif

                        <!-- Chevron Expand Drawer Button -->
                        <button type="button" 
                                class="px-3 py-1.5 rounded-full text-xs font-bold text-slate-500 hover:text-slate-800 dark:hover:text-white bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 transition-all flex items-center gap-1.5 cursor-pointer ml-1"
                                onclick="toggleSectionCardDrawer('{{ $sKey }}'); event.stopPropagation();">
                            <span class="hidden sm:inline text-[11px]">{{ $isAr ? 'تخصيص الحقول' : 'Customize' }}</span>
                            <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-300 {{ $isCardDrawerOpen ? 'rotate-180' : '' }}" 
                               id="chevron-icon-{{ $sKey }}"></i>
                        </button>
                    </div>
                </div>

                <!-- Card Collapsible Body: Custom Titles, Eyebrows & Bespoke Content -->
                <div class="{{ $isCardDrawerOpen ? 'block' : 'hidden' }} p-6 bg-slate-50/60 dark:bg-[#062B49]/30 border-t border-slate-100 dark:border-slate-800/80 transition-all"
                     id="drawer-body-{{ $sKey }}">
                    
                    @if(!empty($sDef['default_title_en']) || !empty($sDef['default_title_ar']))
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- Title EN -->
                            <div class="space-y-1.5 bg-white dark:bg-[#031827] p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
                                <div class="flex items-center justify-between">
                                    <label class="block text-xs font-black text-slate-700 dark:text-slate-300">
                                        <span class="inline-block px-2 py-0.5 rounded bg-[#0A4F78]/10 text-[10px] font-mono text-[#0A4F78] dark:text-[#2A8FC2] mr-1.5 ml-1.5 font-bold">🇬🇧 EN</span>
                                        {{ $isAr ? 'تعديل عنوان القسم (الإنجليزية):' : 'Section Title Override (English):' }}
                                    </label>
                                    <button type="button" onclick="resetFieldToDefault('title_en', '{{ $sKey }}', '{{ addslashes($sDef['default_title_en']) }}')" 
                                            class="text-[10px] font-mono text-slate-400 hover:text-[#0A4F78] dark:hover:text-[#2A8FC2] transition-colors cursor-pointer flex items-center gap-1">
                                        <i class="fa-solid fa-rotate-left text-[9px]"></i>
                                        <span>{{ $isAr ? 'استعادة الافتراضي' : 'Reset Default' }}</span>
                                    </button>
                                </div>
                                <input type="text" name="section_settings[{{ $sKey }}][title_en]" 
                                       id="input-title-en-{{ $sKey }}"
                                       value="{{ $titleEn }}" 
                                       placeholder="{{ $sDef['default_title_en'] }}" 
                                       class="w-full px-4 py-2.5 text-xs font-semibold rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-[#062B49] dark:text-white focus:ring-2 focus:ring-[#0A4F78]/20 focus:border-[#0A4F78] transition-all">
                                <div class="text-[10px] text-slate-400 flex items-center gap-1 font-mono">
                                    <i class="fa-solid fa-arrow-turn-down text-[9px] text-slate-300"></i>
                                    <span>{{ $isAr ? 'القيمة الموروثة: ' : 'Inherited default: ' }}</span>
                                    <span class="text-slate-600 dark:text-slate-300 font-semibold truncate">{{ $sDef['default_title_en'] }}</span>
                                </div>
                            </div>

                            <!-- Title AR -->
                            <div class="space-y-1.5 bg-white dark:bg-[#031827] p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
                                <div class="flex items-center justify-between">
                                    <label class="block text-xs font-black text-slate-700 dark:text-slate-300">
                                        <span class="inline-block px-2 py-0.5 rounded bg-[#67B34A]/10 text-[10px] font-mono text-[#67B34A] mr-1.5 ml-1.5 font-bold">🇸🇦 AR</span>
                                        {{ $isAr ? 'تعديل عنوان القسم (العربية):' : 'Section Title Override (Arabic):' }}
                                    </label>
                                    <button type="button" onclick="resetFieldToDefault('title_ar', '{{ $sKey }}', '{{ addslashes($sDef['default_title_ar']) }}')" 
                                            class="text-[10px] font-mono text-slate-400 hover:text-[#67B34A] transition-colors cursor-pointer flex items-center gap-1">
                                        <i class="fa-solid fa-rotate-left text-[9px]"></i>
                                        <span>{{ $isAr ? 'استعادة الافتراضي' : 'Reset Default' }}</span>
                                    </button>
                                </div>
                                <input type="text" name="section_settings[{{ $sKey }}][title_ar]" 
                                       id="input-title-ar-{{ $sKey }}"
                                       value="{{ $titleAr }}" 
                                       placeholder="{{ $sDef['default_title_ar'] }}" 
                                       dir="rtl" 
                                       class="w-full px-4 py-2.5 text-xs font-semibold rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-[#062B49] dark:text-white focus:ring-2 focus:ring-[#67B34A]/20 focus:border-[#67B34A] transition-all font-tajawal">
                                <div class="text-[10px] text-slate-400 flex items-center gap-1 font-mono" dir="rtl">
                                    <i class="fa-solid fa-arrow-turn-down text-[9px] text-slate-300"></i>
                                    <span>{{ $isAr ? 'القيمة الموروثة: ' : 'Inherited default: ' }}</span>
                                    <span class="text-slate-600 dark:text-slate-300 font-semibold truncate">{{ $sDef['default_title_ar'] }}</span>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(!empty($sDef['default_badge_en']) || !empty($sDef['default_badge_ar']))
                        <!-- Eyebrow Badges -->
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- Badge EN -->
                            <div class="space-y-1.5 bg-white dark:bg-[#031827] p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
                                <label class="block text-xs font-black text-slate-700 dark:text-slate-300">
                                    <span class="inline-block px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-[10px] font-mono text-[#0A4F78] dark:text-[#2A8FC2] mr-1.5 ml-1.5 font-bold">EN BADGE</span>
                                    {{ $isAr ? 'البادج التعريفي العلوي (Eyebrow Badge EN):' : 'Eyebrow Badge (English):' }}
                                </label>
                                <input type="text" name="section_settings[{{ $sKey }}][badge_en]" 
                                       value="{{ $badgeEn }}" 
                                       placeholder="{{ $sDef['default_badge_en'] }}" 
                                       class="w-full px-4 py-2 text-xs font-mono uppercase tracking-wider rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-[#062B49] dark:text-white focus:ring-2 focus:ring-[#0A4F78]/20 focus:border-[#0A4F78] transition-all">
                            </div>

                            <!-- Badge AR -->
                            <div class="space-y-1.5 bg-white dark:bg-[#031827] p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
                                <label class="block text-xs font-black text-slate-700 dark:text-slate-300">
                                    <span class="inline-block px-2 py-0.5 rounded bg-[#67B34A]/10 text-[10px] font-mono text-[#67B34A] mr-1.5 ml-1.5 font-bold">AR BADGE</span>
                                    {{ $isAr ? 'البادج التعريفي العلوي (Eyebrow Badge AR):' : 'Eyebrow Badge (Arabic):' }}
                                </label>
                                <input type="text" name="section_settings[{{ $sKey }}][badge_ar]" 
                                       value="{{ $badgeAr }}" 
                                       placeholder="{{ $sDef['default_badge_ar'] }}" 
                                       dir="rtl" 
                                       class="w-full px-4 py-2 text-xs font-tajawal rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-[#062B49] dark:text-white focus:ring-2 focus:ring-[#67B34A]/20 focus:border-[#67B34A] transition-all">
                            </div>
                        </div>
                    @endif

                    @if($sDef['has_custom_content'])
                        {{-- Preserved hidden inputs so existing values are maintained upon save without exposing dev-only UI --}}
                        <input type="hidden" name="section_settings[{{ $sKey }}][custom_content_enabled]" value="{{ $hasCustom ? '1' : '0' }}">
                        <input type="hidden" name="section_settings[{{ $sKey }}][custom_content_en]" value="{{ $contentEn }}">
                        <input type="hidden" name="section_settings[{{ $sKey }}][custom_content_ar]" value="{{ $contentAr }}">

                        @if(false)
                        <!-- Bespoke HTML / Rich Content Injection Studio (Hidden for development) -->
                        <div class="mt-5 pt-4 border-t border-slate-200/70 dark:border-slate-800">
                            <div class="flex items-center justify-between cursor-pointer py-2 px-3 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800/60 transition-colors" 
                                 onclick="toggleCustomContentDrawer('{{ $sKey }}')">
                                <div class="flex items-center gap-2.5">
                                    <label class="inline-flex items-center gap-2 text-xs font-black text-slate-700 dark:text-slate-300 cursor-pointer" onclick="event.stopPropagation()">
                                        <input type="checkbox" name="section_settings[{{ $sKey }}][custom_content_enabled]" value="1" 
                                               id="custom-content-check-{{ $sKey }}" 
                                               {{ $hasCustom ? 'checked' : '' }} 
                                               onchange="syncDrawerVisibilityFromCheck('{{ $sKey }}')"
                                               class="w-4 h-4 rounded text-amber-500 focus:ring-0">
                                        <span class="text-amber-600 dark:text-amber-400 flex items-center gap-1.5 font-bold">
                                            <i class="fa-solid fa-code text-xs"></i>
                                            <span>{{ $isAr ? 'تفعيل محتوى مخصص بالكامل (يتجاوز البيانات القياسية)' : 'Enable Bespoke Rich Content (Overrides Standard Monograph Data)' }}</span>
                                        </span>
                                    </label>
                                </div>
                                <span class="text-xs text-slate-400 font-mono flex items-center gap-1.5">
                                    <span class="px-2 py-0.5 rounded bg-amber-500/10 text-amber-600 dark:text-amber-400 text-[10px] font-bold">HTML / TEXT</span>
                                    <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-300 {{ $hasCustom ? 'rotate-180' : '' }}" id="custom-drawer-icon-{{ $sKey }}"></i>
                                </span>
                            </div>

                            <div id="custom-content-drawer-{{ $sKey }}" class="{{ $hasCustom ? 'block' : 'hidden' }} mt-3 space-y-3 bg-amber-500/5 dark:bg-amber-500/10 p-5 rounded-2xl border border-amber-500/20">
                                <div class="flex items-center justify-between text-xs text-amber-800 dark:text-amber-300 pb-2 border-b border-amber-500/15">
                                    <span class="font-bold flex items-center gap-1.5">
                                        <i class="fa-solid fa-terminal text-amber-600"></i>
                                        {{ $isAr ? 'محرر الشيفرات البرمجية والنصوص المخصصة:' : 'Bespoke HTML & Text Content Editor:' }}
                                    </span>
                                    <span class="text-[11px] font-mono text-amber-700 dark:text-amber-400">
                                        {{ $isAr ? 'يدعم وسوم HTML الأساسية (p, ul, li, blockquote, strong)' : 'Accepts HTML & CSS (p, ul, li, blockquote, strong)' }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-amber-950 dark:text-amber-200 mb-1 font-mono">
                                            <span class="px-1.5 py-0.5 rounded bg-amber-500/20 text-[10px] mr-1 ml-1 font-bold">EN</span>
                                            {{ $isAr ? 'المحتوى المخصص (الإنجليزية):' : 'Bespoke Content (English):' }}
                                        </label>
                                        <textarea name="section_settings[{{ $sKey }}][custom_content_en]" rows="4" 
                                                  class="w-full p-3 text-xs font-mono rounded-xl border border-amber-500/30 bg-white dark:bg-[#031827] text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-amber-500/30" 
                                                  placeholder="<div class='custom-box'><h3>Clinical Note</h3><p>Enter bespoke clinical specifications...</p></div>">{{ $contentEn }}</textarea>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-amber-950 dark:text-amber-200 mb-1 font-mono">
                                            <span class="px-1.5 py-0.5 rounded bg-amber-500/20 text-[10px] mr-1 ml-1 font-bold">AR</span>
                                            {{ $isAr ? 'المحتوى المخصص (العربية):' : 'Bespoke Content (Arabic):' }}
                                        </label>
                                        <textarea name="section_settings[{{ $sKey }}][custom_content_ar]" rows="4" dir="rtl" 
                                                  class="w-full p-3 text-xs font-tajawal rounded-xl border border-amber-500/30 bg-white dark:bg-[#031827] text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-amber-500/30" 
                                                  placeholder="<div class='custom-box'><h3>ملاحظة سريرية</h3><p>أدخل نصوص أو مواصفات مخصصة بديلة هنا...</p></div>">{{ $contentAr }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endif

                </div>
            </div>
        @endforeach
    </div>
</div>

<script>
    // ==========================================================
    // TACTILE PILL SWITCH CONTROLLER (Native OnChange Engine)
    // ==========================================================
    function handleSwitchChange(checkbox, type, secKey) {
        if (!checkbox) return;
        const pill = checkbox.closest('.bz-switch-pill');
        if (!pill) return;

        const isChecked = checkbox.checked;
        const thumbIcon = pill.querySelector('.bz-switch-thumb i');

        if (isChecked) {
            pill.classList.remove('is-inactive');
            pill.classList.add('is-active');
            if (thumbIcon) {
                if (type === 'desktop' || type === 'mobile') {
                    thumbIcon.className = 'fa-solid fa-check';
                } else if (type === 'open') {
                    thumbIcon.className = 'fa-solid fa-bolt';
                }
            }
        } else {
            pill.classList.remove('is-active');
            pill.classList.add('is-inactive');
            if (thumbIcon) {
                if (type === 'desktop' || type === 'mobile') {
                    thumbIcon.className = 'fa-solid fa-xmark';
                } else if (type === 'open') {
                    thumbIcon.className = 'fa-solid fa-folder-closed';
                }
            }
        }

        // Update Header Status Badge
        updateCardStatusChip(secKey);

        // Re-evaluate Active Device Scope Filtering if active
        reapplyCurrentScopeFilter();
    }

    // Fallback if called programmatically
    function togglePillSwitch(pillElement, type, secKey) {
        const checkbox = pillElement.querySelector('input[type="checkbox"]');
        if (!checkbox) return;
        checkbox.checked = !checkbox.checked;
        handleSwitchChange(checkbox, type, secKey);
        checkbox.dispatchEvent(new Event('change', { bubbles: true }));
    }

    // Update Header Status Chip
    function updateCardStatusChip(secKey) {
        const card = document.getElementById('card-sec-' + secKey);
        const chip = document.getElementById('status-chip-' + secKey);
        if (!card || !chip) return;

        const isDesktop = card.querySelector('.customizer-desktop-toggle')?.checked;
        const isMobile = card.querySelector('.customizer-mobile-toggle')?.checked;
        const isAr = {{ $isAr ? 'true' : 'false' }};

        chip.className = 'bz-status-chip text-[10px] font-mono font-bold px-2 py-0.5 rounded-full transition-all';
        if (isDesktop && isMobile) {
            chip.classList.add('bg-emerald-500/10', 'text-emerald-600', 'dark:text-emerald-400', 'border', 'border-emerald-500/20');
            chip.innerHTML = '<i class="fa-solid fa-check text-[9px] mr-1 ml-1"></i> ' + (isAr ? 'مفعل على جميع الشاشات' : 'All Viewports');
        } else if (isDesktop) {
            chip.classList.add('bg-sky-500/10', 'text-sky-600', 'dark:text-sky-400', 'border', 'border-sky-500/20');
            chip.innerHTML = '<i class="fa-solid fa-display text-[9px] mr-1 ml-1"></i> ' + (isAr ? 'كمبيوتر فقط' : 'Desktop Only');
        } else if (isMobile) {
            chip.classList.add('bg-amber-500/10', 'text-amber-600', 'dark:text-amber-400', 'border', 'border-amber-500/20');
            chip.innerHTML = '<i class="fa-solid fa-mobile-screen text-[9px] mr-1 ml-1"></i> ' + (isAr ? 'موبايل فقط' : 'Mobile Only');
        } else {
            chip.classList.add('bg-rose-500/10', 'text-rose-600', 'dark:text-rose-400', 'border', 'border-rose-500/20');
            chip.innerHTML = '<i class="fa-solid fa-eye-slash text-[9px] mr-1 ml-1"></i> ' + (isAr ? 'معطل بالكامل' : 'Hidden Everywhere');
        }
    }

    // Toggle Individual Section Card Drawer
    function toggleSectionCardDrawer(secKey) {
        const drawer = document.getElementById('drawer-body-' + secKey);
        const icon = document.getElementById('chevron-icon-' + secKey);
        if (!drawer) return;

        const isHidden = drawer.classList.contains('hidden');
        if (isHidden) {
            drawer.classList.remove('hidden');
            drawer.classList.add('block');
            if (icon) icon.classList.add('rotate-180');
        } else {
            drawer.classList.add('hidden');
            drawer.classList.remove('block');
            if (icon) icon.classList.remove('rotate-180');
        }
    }

    // ==========================================================
    // SCOPE FILTER (All Devices / Desktop / Mobile)
    // ==========================================================
    let currentCustomizerScope = 'all';

    function filterCustomizerView(scope, btn) {
        currentCustomizerScope = scope;
        document.querySelectorAll('.bz-customizer-scope-btn').forEach(b => {
            b.classList.remove('active');
            b.classList.add('text-slate-600', 'dark:text-slate-400');
        });
        if (btn) {
            btn.classList.add('active');
            btn.classList.remove('text-slate-600', 'dark:text-slate-400');
        }

        reapplyCurrentScopeFilter();
    }

    function reapplyCurrentScopeFilter() {
        const cards = document.querySelectorAll('.bz-customizer-card');
        cards.forEach(card => {
            if (currentCustomizerScope === 'all') {
                card.classList.remove('is-dimmed');
            } else if (currentCustomizerScope === 'desktop') {
                const isDesktop = card.querySelector('.customizer-desktop-toggle')?.checked;
                if (isDesktop) {
                    card.classList.remove('is-dimmed');
                } else {
                    card.classList.add('is-dimmed');
                }
            } else if (currentCustomizerScope === 'mobile') {
                const isMobile = card.querySelector('.customizer-mobile-toggle')?.checked;
                if (isMobile) {
                    card.classList.remove('is-dimmed');
                } else {
                    card.classList.add('is-dimmed');
                }
            }
        });
    }

    // ==========================================================
    // GLOBAL QUICK PRESETS
    // ==========================================================
    function customizerApplyPreset(preset) {
        if (preset === 'expand_cards') {
            document.querySelectorAll('[id^="drawer-body-"]').forEach(drawer => {
                drawer.classList.remove('hidden');
                drawer.classList.add('block');
            });
            document.querySelectorAll('[id^="chevron-icon-"]').forEach(icon => {
                icon.classList.add('rotate-180');
            });
        } else if (preset === 'collapse_cards') {
            document.querySelectorAll('[id^="drawer-body-"]').forEach(drawer => {
                drawer.classList.add('hidden');
                drawer.classList.remove('block');
            });
            document.querySelectorAll('[id^="chevron-icon-"]').forEach(icon => {
                icon.classList.remove('rotate-180');
            });
        } else if (preset === 'enable_all') {
            document.querySelectorAll('.customizer-desktop-toggle').forEach(el => {
                if (!el.checked) {
                    el.checked = true;
                    const secKey = el.closest('.bz-customizer-card')?.dataset.sectionKey;
                    handleSwitchChange(el, 'desktop', secKey);
                }
            });
            document.querySelectorAll('.customizer-mobile-toggle').forEach(el => {
                if (!el.checked) {
                    el.checked = true;
                    const secKey = el.closest('.bz-customizer-card')?.dataset.sectionKey;
                    handleSwitchChange(el, 'mobile', secKey);
                }
            });
        }
    }

    // Reset Title/Badge to Default Value
    function resetFieldToDefault(fieldType, secKey, defaultValue) {
        if (fieldType === 'title_en') {
            const input = document.getElementById('input-title-en-' + secKey);
            if (input) input.value = defaultValue;
        } else if (fieldType === 'title_ar') {
            const input = document.getElementById('input-title-ar-' + secKey);
            if (input) input.value = defaultValue;
        }
    }

    // Bespoke Content Drawer Accordion
    function toggleCustomContentDrawer(secKey) {
        const drawer = document.getElementById('custom-content-drawer-' + secKey);
        const icon = document.getElementById('custom-drawer-icon-' + secKey);
        const check = document.getElementById('custom-content-check-' + secKey);
        if (!drawer) return;
        const isHidden = drawer.classList.contains('hidden');
        if (isHidden) {
            drawer.classList.remove('hidden');
            drawer.classList.add('block');
            if (icon) icon.classList.add('rotate-180');
            if (check) check.checked = true;
        } else {
            drawer.classList.add('hidden');
            drawer.classList.remove('block');
            if (icon) icon.classList.remove('rotate-180');
        }
    }

    function syncDrawerVisibilityFromCheck(secKey) {
        const drawer = document.getElementById('custom-content-drawer-' + secKey);
        const icon = document.getElementById('custom-drawer-icon-' + secKey);
        const check = document.getElementById('custom-content-check-' + secKey);
        if (!drawer || !check) return;
        if (check.checked) {
            drawer.classList.remove('hidden');
            drawer.classList.add('block');
            if (icon) icon.classList.add('rotate-180');
        } else {
            drawer.classList.add('hidden');
            drawer.classList.remove('block');
            if (icon) icon.classList.remove('rotate-180');
        }
    }
</script>
