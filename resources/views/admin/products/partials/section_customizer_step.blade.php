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
            'has_custom_content' => true,
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
            'name_en' => 'Bio-Certifications & Quality Assurances (cGMP Standards)',
            'name_ar' => 'شهادات الجودة، الاعتمادات الدوائية وتراخيص cGMP',
            'tag' => 'MONOGRAPH 06',
            'has_open_state' => true,
            'default_open' => false,
            'has_custom_content' => true,
            'default_badge_en' => $detailsSettings['pdetail_sec6_badge_en'] ?? 'cGMP Certified Formulations',
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
            'name_ar' => 'التكامل الحيوي (التركيبات التكميلية وباقة الخصم المشترك)',
            'tag' => 'RECOMMENDATIONS',
            'has_open_state' => false,
            'has_custom_content' => false,
            'default_badge_en' => $detailsSettings['pdetail_related_badge_en'] ?? 'SYNERGISTIC LONGEVITY PROTOCOLS',
            'default_badge_ar' => $detailsSettings['pdetail_related_badge_ar'] ?? 'التكامل الحيوي',
            'default_title_en' => $detailsSettings['pdetail_related_title_en'] ?? 'Complementary Formulations',
            'default_title_ar' => $detailsSettings['pdetail_related_title_ar'] ?? 'تركيبات متكاملة تعزز النتائج',
            'description_en' => 'Recommended complementary formulations that act synergistically with this bioceutical with controlled discounts.',
            'description_ar' => 'المستحضرات التكميلية الموصى بها وباقات الخصم المنسق لتعزيز النتائج الحيوية.',
        ],
    ];

    $allProductList = $allProducts ?? collect();
    $currentProductPrice = (float) ($product['sale_price'] ?? ($product['price'] ?? 68.0));
    $currentProductName = $product['name_' . app()->getLocale()] ?? ($product['name_en'] ?? 'Current Product');
@endphp

<style>
    /* ==========================================================
       BLUE ZONE DUAL-PANE SECTION CUSTOMIZER & BUNDLE STUDIO
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
        gap: 0.45rem;
        padding: 0.32rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.72rem;
        font-weight: 700;
        cursor: pointer;
        user-select: none;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        border: 1.5px solid transparent;
        white-space: nowrap;
    }
    @media (max-width: 640px) {
        .bz-switch-pill {
            padding: 0.25rem 0.55rem;
            font-size: 0.68rem;
            gap: 0.35rem;
        }
        .bz-switch-pill .bz-switch-thumb {
            width: 1rem !important;
            height: 1rem !important;
            font-size: 8px !important;
        }
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

    /* Complementary Product Selectable Card */
    .bz-bundle-product-card {
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        cursor: pointer;
        user-select: none;
    }
    .bz-bundle-product-card.is-selected {
        border-color: #67B34A !important;
        background-color: rgba(103, 179, 74, 0.08) !important;
        box-shadow: 0 4px 15px -3px rgba(103, 179, 74, 0.25);
    }

    /* Live Preview Simulator Window */
    .bz-preview-device {
        border-radius: 2rem;
        background: #031827;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.1);
        overflow: hidden;
        transition: all 0.3s ease;
    }
    .bz-preview-device.is-mobile-mode {
        max-width: 380px;
        margin: 0 auto;
        border-radius: 2.5rem;
        border: 8px solid #062B49;
    }
</style>

<div class="space-y-6 pb-40">
    <!-- ==========================================================
         TOP EXECUTIVE BANNER
         ========================================================== -->
    <div class="p-5 sm:p-7 md:p-8 rounded-3xl bg-gradient-to-br from-[#02131F] via-[#062B49] to-[#0A4F78] text-white shadow-2xl relative overflow-hidden border border-white/10">
        <!-- Ambient Glowing Orbs -->
        <div class="absolute -right-16 -top-16 w-72 h-72 rounded-full bg-[#67B34A]/25 blur-3xl pointer-events-none"></div>
        <div class="absolute right-1/3 -bottom-20 w-80 h-80 rounded-full bg-[#2A8FC2]/20 blur-3xl pointer-events-none"></div>
        
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-3 min-w-0 flex-1">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#67B34A]/25 text-[#B8D98A] font-mono text-[11px] font-black uppercase tracking-wider border border-[#67B34A]/30 shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-[#67B34A] bz-live-dot"></span>
                        {{ $isAr ? 'الخطوة 6 من 7' : 'STEP 6 OF 7' }}
                    </span>
                    <span class="px-3 py-1 rounded-full bg-white/10 text-white font-mono text-[11px] font-bold border border-white/15 backdrop-blur-md">
                        <i class="fa-solid fa-layer-group text-[#2A8FC2] mr-1.5 ml-1.5"></i>
                        {{ $isAr ? 'استوديو استجابة الشاشات والأقسام' : 'RESPONSIVE VIEWPORT STUDIO' }}
                    </span>
                    <span class="px-3 py-1 rounded-full bg-pink-500/20 text-pink-300 font-mono text-[11px] font-bold border border-pink-500/30">
                        <i class="fa-solid fa-tag mr-1 ml-1"></i>
                        {{ $isAr ? 'باقات الخصم المنسق' : 'CONTROLLED BUNDLE DISCOUNT' }}
                    </span>
                </div>

                <div>
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-black tracking-tight text-white flex items-center gap-2.5">
                        <span>{{ $isAr ? 'تخصيص هيكل ومظهر صفحة تفاصيل المنتج وباقات الخصم' : 'Product Details Page, Sections & Protocol Bundle Studio' }}</span>
                    </h2>
                    <p class="text-xs sm:text-sm text-white/80 mt-1 max-w-2xl leading-relaxed font-normal">
                        {{ $isAr 
                            ? 'تحكم هندسي شامل في تفاصيل صفحة المنتج: تخصيص الظهور حسب نوع الشاشة (كمبيوتر 🖥️ أو هاتف 📱)، تحديد حالة الطي والتوسيع، تعديل العناوين والشارات، وتكوين باقة التكامل الحيوي بخصم منسق ومحدد.' 
                            : 'Granular control over public product page sections: toggle viewport visibility (Desktop 🖥️ vs Mobile 📱), control initial accordion expansion, override bilingual clinical titles & badges, and configure Synergistic Protocol Bundles with controlled discounts.' }}
                    </p>
                </div>
            </div>

            <!-- Global Action Presets Toolbar -->
            <div class="grid grid-cols-2 sm:flex sm:flex-row items-stretch sm:items-center gap-2 shrink-0 bg-white/10 p-1.5 sm:p-2 rounded-2xl border border-white/15 backdrop-blur-md shadow-lg">
                <button type="button" onclick="customizerApplyPreset('expand_cards')" 
                        class="px-3 sm:px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-[11px] sm:text-xs font-bold transition-all flex items-center justify-center gap-1.5 sm:gap-2 cursor-pointer border border-white/15 shadow-sm active:scale-95"
                        title="{{ $isAr ? 'توسيع جميع بطاقات التعديل' : 'Expand all section editing panels' }}">
                    <i class="fa-solid fa-angles-down text-[#67B34A]"></i>
                    <span>{{ $isAr ? 'توسيع البطاقات' : 'Expand All' }}</span>
                </button>
                <button type="button" onclick="customizerApplyPreset('collapse_cards')" 
                        class="px-3 sm:px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white text-[11px] sm:text-xs font-bold transition-all flex items-center justify-center gap-1.5 sm:gap-2 cursor-pointer border border-white/15 shadow-sm active:scale-95"
                        title="{{ $isAr ? 'طي جميع بطاقات التعديل' : 'Collapse all section editing panels' }}">
                    <i class="fa-solid fa-angles-up text-amber-400"></i>
                    <span>{{ $isAr ? 'طي البطاقات' : 'Collapse All' }}</span>
                </button>
                <button type="button" onclick="customizerApplyPreset('enable_all')" 
                        class="px-3.5 sm:px-4 py-2 rounded-xl bg-[#67B34A] hover:bg-[#5aa13f] text-white text-[11px] sm:text-xs font-black transition-all flex items-center justify-center gap-1.5 sm:gap-2 cursor-pointer shadow-md active:scale-95">
                    <i class="fa-solid fa-check-double"></i>
                    <span>{{ $isAr ? 'تفعيل الكل' : 'Enable All' }}</span>
                </button>
                <button type="button" onclick="triggerStickyPreviewSync()" 
                        class="px-3 sm:px-3.5 py-2 rounded-xl bg-[#2A8FC2] hover:bg-[#2277a3] text-white text-[11px] sm:text-xs font-black transition-all flex items-center justify-center gap-1.5 sm:gap-2 cursor-pointer shadow-md active:scale-95">
                    <i class="fa-solid fa-rotate"></i>
                    <span>{{ $isAr ? 'تحديث المعاينة' : 'Refresh Preview' }}</span>
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
                <span>{{ $isAr ? 'نطاق تصفية الأجهزة:' : 'DEVICE PREVIEW SCOPE:' }}</span>
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
         DUAL-PANE SPLIT STUDIO: SECTION CARDS (LEFT) + LIVE PREVIEW (RIGHT)
         ========================================================== -->
    <div class="grid grid-cols-1 2xl:grid-cols-12 gap-6 items-start">
        
        <!-- ======================================================
             LEFT COLUMN: SECTION ACCORDION CARDS & BUNDLE BUILDER
             ====================================================== -->
        <div class="2xl:col-span-7 space-y-4" id="customizerSectionCardsContainer">
            @foreach($sections as $sKey => $sDef)
                @php
                    $sConf = $secData[$sKey] ?? [];
                    
                    $enDesktop = array_key_exists('enabled_desktop', $sConf) ? (bool) $sConf['enabled_desktop'] : true;
                    $enMobile = array_key_exists('enabled_mobile', $sConf) ? (bool) $sConf['enabled_mobile'] : true;
                    $defOpen = array_key_exists('default_open', $sConf) ? (bool) $sConf['default_open'] : ($sDef['default_open'] ?? false);
                    $hasCustom = !empty($sConf['custom_content_enabled']);
                    $contentScreenMode = $sConf['content_screen_mode'] ?? 'unified';
                    $contentEn = $sConf['custom_content_en'] ?? '';
                    $contentAr = $sConf['custom_content_ar'] ?? '';
                    $contentMobileEn = $sConf['custom_content_mobile_en'] ?? '';
                    $contentMobileAr = $sConf['custom_content_mobile_ar'] ?? '';

                    $titleEn = $sConf['title_en'] ?? '';
                    $titleAr = $sConf['title_ar'] ?? '';
                    $badgeEn = $sConf['badge_en'] ?? '';
                    $badgeAr = $sConf['badge_ar'] ?? '';

                    // Protocol Bundle Data for section 'related'
                    $bundleConf = ($sKey === 'related') ? ($sConf['bundle'] ?? []) : [];
                    $bundleEnabled = !empty($bundleConf['enabled']);
                    $bundleDiscType = $bundleConf['discount_type'] ?? 'percentage';
                    $bundleDiscVal = (float) ($bundleConf['discount_value'] ?? 15.0);
                    $bundleBadgeEn = $bundleConf['badge_en'] ?? 'Optimal Synergistic Protocol';
                    $bundleBadgeAr = $bundleConf['badge_ar'] ?? 'البروتوكول التكاملي الموصى به';
                    $bundleProductIds = (array) ($bundleConf['product_ids'] ?? []);

                    // Determine whether to start expanded (first 2 sections open by default)
                    $isCardDrawerOpen = in_array($sKey, ['dossier', 'sec1', 'related']);
                @endphp

                <div class="bz-customizer-card rounded-3xl bg-white dark:bg-[#031827] border border-slate-200/90 dark:border-slate-800 shadow-sm relative overflow-hidden" 
                     id="card-sec-{{ $sKey }}" data-section-key="{{ $sKey }}"
                     style="border-inline-start: 6px solid {{ $sDef['color'] }};">
                    
                    <!-- Card Header Mini-Dashboard -->
                    <div class="p-4 sm:p-5 md:p-6 border-b border-slate-100 dark:border-slate-800/80 cursor-pointer transition-colors hover:bg-slate-50/60 dark:hover:bg-[#062B49]/40 space-y-3.5"
                         onclick="toggleSectionCardDrawer('{{ $sKey }}')">
                        
                        <!-- Primary Row: Icon, Titles, Badges, and Expand Chevron -->
                        <div class="flex items-start justify-between gap-3 sm:gap-4">
                            <!-- Icon & Title Details -->
                            <div class="flex items-start gap-3 sm:gap-4 min-w-0 flex-1">
                                <div class="w-11 h-11 sm:w-13 sm:h-13 rounded-2xl flex items-center justify-center text-lg sm:text-xl shrink-0 shadow-md border-2 transition-transform duration-300" 
                                     style="background-color: {{ $sDef['color_light'] }}; border-color: {{ $sDef['color_border'] }}; color: {{ $sDef['color'] }};">
                                    <i class="fa-solid {{ $sDef['icon'] }}"></i>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <!-- Badges Line -->
                                    <div class="flex items-center gap-1.5 sm:gap-2 flex-wrap mb-1.5">
                                        <span class="px-2.5 py-0.5 rounded-full font-mono text-[10px] font-black uppercase tracking-wider shadow-xs" 
                                              style="background-color: {{ $sDef['color_light'] }}; color: {{ $sDef['color'] }}; border: 1.5px solid {{ $sDef['color_border'] }};">
                                            {{ $sDef['tag'] }}
                                        </span>
                                        <span class="text-[10px] font-mono text-slate-700 dark:text-slate-200 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-md font-extrabold border border-slate-200 dark:border-slate-700">
                                            #{{ $sDef['num'] }}
                                        </span>
                                        <span class="text-[10px] font-mono text-[#0A4F78] dark:text-[#2A8FC2] bg-[#0A4F78]/10 dark:bg-[#2A8FC2]/20 px-2 py-0.5 rounded-md font-black border border-[#0A4F78]/20">
                                            {{ $sKey }}
                                        </span>

                                        @if($sKey === 'related' && $bundleEnabled)
                                            <span class="px-2.5 py-0.5 rounded-full bg-pink-500/15 text-pink-600 dark:text-pink-400 border border-pink-500/30 text-[10px] font-black tracking-wide flex items-center gap-1">
                                                <i class="fa-solid fa-sparkles text-[8px]"></i>
                                                {{ $isAr ? 'الباقة مفعلة' : 'Bundle Active' }}
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Section Title (Bold, High-Contrast, Senior Design) -->
                                    <h3 class="text-base sm:text-lg font-black text-[#031827] dark:text-[#F6F5EF] tracking-tight leading-snug break-words">
                                        {{ $isAr ? $sDef['name_ar'] : $sDef['name_en'] }}
                                    </h3>
                                    <p class="text-xs text-slate-600 dark:text-slate-300 mt-1 leading-relaxed hidden sm:block font-medium">
                                        {{ $isAr ? $sDef['description_ar'] : $sDef['description_en'] }}
                                    </p>
                                </div>
                            </div>

                            <!-- Expand / Collapse Drawer Chevron Button -->
                            <button type="button" 
                                    class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-500 dark:text-slate-300 flex items-center justify-center transition-all cursor-pointer shrink-0 mt-0.5"
                                    onclick="toggleSectionCardDrawer('{{ $sKey }}')">
                                <i class="fa-solid fa-chevron-down text-xs transition-transform duration-300 {{ $isCardDrawerOpen ? 'rotate-180' : '' }}" 
                                   id="chevron-icon-{{ $sKey }}"></i>
                            </button>
                        </div>

                        <!-- Secondary Action Dock: Status Badge & Interactive Viewport Switches -->
                        <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-800/80 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5" 
                             onclick="event.stopPropagation()">
                            
                            <!-- Status Chip (Left) -->
                            <div class="flex items-center gap-2">
                                <span class="bz-status-chip text-[10px] font-mono font-bold px-2.5 py-1 rounded-full {{ ($enDesktop && $enMobile) ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20' : (($enDesktop || $enMobile) ? 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20' : 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20') }}"
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

                            <!-- Interactive Switches (Right) -->
                            <div class="flex items-center gap-1.5 sm:gap-2 flex-wrap">
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
                                        <span class="switch-label">{{ $isAr ? 'مفتوح تلقائياً' : 'Open by Default' }}</span>
                                    </label>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Card Body Drawer -->
                    <div class="{{ $isCardDrawerOpen ? 'block' : 'hidden' }} p-5 sm:p-6 bg-slate-50/50 dark:bg-[#02131F]/50 border-t border-slate-100 dark:border-slate-800/80 space-y-5"
                         id="drawer-body-{{ $sKey }}">

                        @if(!empty($sDef['default_title_en']) || !empty($sDef['default_title_ar']))
                            <!-- Section Title Overrides: High-Contrast Executive Clinical Title Studio -->
                            <div class="space-y-3.5">
                                <div class="flex items-center justify-between gap-2 border-b border-slate-200 dark:border-slate-800 pb-2.5">
                                    <div class="flex items-center gap-2">
                                        <span class="w-3 h-3 rounded-full bg-[#0A4F78] dark:bg-[#2A8FC2] shadow-xs"></span>
                                        <h4 class="text-xs sm:text-sm font-black text-slate-900 dark:text-white uppercase tracking-wider">
                                            {{ $isAr ? 'تخصيص عناوين القسم السريرية (Section Title Overrides)' : 'Section Clinical Titles (Bilingual Overrides)' }}
                                        </h4>
                                    </div>
                                    <span class="text-[11px] font-bold text-slate-600 dark:text-slate-300">
                                        {{ $isAr ? 'يتحكم بالعنوان الظاهر في رأس الأكورديون بالمتجر' : 'Controls storefront public section heading' }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <!-- Title EN -->
                                    <div class="space-y-3 bg-white dark:bg-[#031827] p-5 rounded-2xl border-2 border-slate-200 dark:border-slate-700/80 shadow-md hover:border-[#0A4F78]/50 transition-all">
                                        <div class="flex items-center justify-between gap-2">
                                            <div class="flex items-center gap-2">
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-[#0A4F78]/10 dark:bg-[#2A8FC2]/20 text-[11px] font-mono text-[#0A4F78] dark:text-[#2A8FC2] font-black border border-[#0A4F78]/20">
                                                    🇬🇧 EN
                                                </span>
                                                <label class="block text-xs font-black text-slate-900 dark:text-white">
                                                    {{ $isAr ? 'عنوان القسم بالإنجليزية:' : 'English Section Title:' }}
                                                </label>
                                            </div>

                                            <button type="button" onclick="resetFieldToDefault('title_en', '{{ $sKey }}', '{{ addslashes($sDef['default_title_en']) }}')" 
                                                    class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-[11px] font-bold text-slate-700 dark:text-slate-200 hover:text-[#0A4F78] dark:hover:text-[#2A8FC2] transition-all cursor-pointer flex items-center gap-1.5 border border-slate-200 dark:border-slate-700"
                                                    title="{{ $isAr ? 'استعادة النص الافتراضي' : 'Reset to default title' }}">
                                                <i class="fa-solid fa-rotate-left text-[10px]"></i>
                                                <span>{{ $isAr ? 'استعادة الافتراضي' : 'Reset' }}</span>
                                            </button>
                                        </div>

                                        <div class="relative">
                                            <input type="text" name="section_settings[{{ $sKey }}][title_en]" 
                                                   id="input-title-en-{{ $sKey }}"
                                                   value="{{ $titleEn }}" 
                                                   placeholder="{{ $sDef['default_title_en'] }}" 
                                                   oninput="syncPreviewTitle('{{ $sKey }}', this.value, 'en')"
                                                   class="w-full px-4 py-3 text-sm font-bold text-slate-900 dark:text-white bg-slate-50/80 hover:bg-white focus:bg-white dark:bg-[#062B49] rounded-xl border-2 border-slate-300 dark:border-slate-600 focus:border-[#0A4F78] dark:focus:border-[#2A8FC2] focus:ring-4 focus:ring-[#0A4F78]/15 transition-all outline-none placeholder:text-slate-400 dark:placeholder:text-slate-500 shadow-inner">
                                        </div>

                                        <!-- Default Reference Box with high contrast -->
                                        <div class="p-2.5 rounded-xl bg-slate-100/90 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 flex items-start gap-2 text-xs">
                                            <span class="w-5 h-5 rounded-full bg-[#0A4F78]/15 dark:bg-[#2A8FC2]/20 text-[#0A4F78] dark:text-[#2A8FC2] flex items-center justify-center shrink-0 text-[10px] mt-0.5">
                                                <i class="fa-solid fa-font"></i>
                                            </span>
                                            <div class="min-w-0 flex-1 leading-snug">
                                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 block mb-0.5">
                                                    {{ $isAr ? 'القيمة الافتراضية المعتمدة بالمتجر:' : 'Storefront Default Title:' }}
                                                </span>
                                                <span class="font-bold text-slate-900 dark:text-slate-100 select-all break-words">
                                                    {{ $sDef['default_title_en'] }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Title AR -->
                                    <div class="space-y-3 bg-white dark:bg-[#031827] p-5 rounded-2xl border-2 border-slate-200 dark:border-slate-700/80 shadow-md hover:border-[#67B34A]/50 transition-all">
                                        <div class="flex items-center justify-between gap-2">
                                            <div class="flex items-center gap-2">
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-[#67B34A]/15 text-[11px] font-mono text-[#67B34A] font-black border border-[#67B34A]/25">
                                                    🇸🇦 AR
                                                </span>
                                                <label class="block text-xs font-black text-slate-900 dark:text-white">
                                                    {{ $isAr ? 'عنوان القسم بالعربية:' : 'Arabic Section Title:' }}
                                                </label>
                                            </div>

                                            <button type="button" onclick="resetFieldToDefault('title_ar', '{{ $sKey }}', '{{ addslashes($sDef['default_title_ar']) }}')" 
                                                    class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-[11px] font-bold text-slate-700 dark:text-slate-200 hover:text-[#67B34A] transition-all cursor-pointer flex items-center gap-1.5 border border-slate-200 dark:border-slate-700"
                                                    title="{{ $isAr ? 'استعادة النص الافتراضي' : 'Reset to default title' }}">
                                                <i class="fa-solid fa-rotate-left text-[10px]"></i>
                                                <span>{{ $isAr ? 'استعادة الافتراضي' : 'Reset' }}</span>
                                            </button>
                                        </div>

                                        <div class="relative">
                                            <input type="text" name="section_settings[{{ $sKey }}][title_ar]" 
                                                   id="input-title-ar-{{ $sKey }}"
                                                   value="{{ $titleAr }}" 
                                                   placeholder="{{ $sDef['default_title_ar'] }}" 
                                                   dir="rtl" 
                                                   oninput="syncPreviewTitle('{{ $sKey }}', this.value, 'ar')"
                                                   class="w-full px-4 py-3 text-sm font-bold text-slate-900 dark:text-white bg-slate-50/80 hover:bg-white focus:bg-white dark:bg-[#062B49] rounded-xl border-2 border-slate-300 dark:border-slate-600 focus:border-[#67B34A] focus:ring-4 focus:ring-[#67B34A]/15 transition-all outline-none placeholder:text-slate-400 dark:placeholder:text-slate-500 font-tajawal shadow-inner">
                                        </div>

                                        <!-- Default Reference Box with high contrast -->
                                        <div class="p-2.5 rounded-xl bg-slate-100/90 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 flex items-start gap-2 text-xs" dir="rtl">
                                            <span class="w-5 h-5 rounded-full bg-[#67B34A]/15 text-[#67B34A] flex items-center justify-center shrink-0 text-[10px] mt-0.5">
                                                <i class="fa-solid fa-font"></i>
                                            </span>
                                            <div class="min-w-0 flex-1 leading-snug">
                                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 block mb-0.5">
                                                    {{ $isAr ? 'القيمة الافتراضية المعتمدة بالمتجر:' : 'Storefront Default Title:' }}
                                                </span>
                                                <span class="font-bold text-slate-900 dark:text-slate-100 select-all break-words font-tajawal">
                                                    {{ $sDef['default_title_ar'] }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if(!empty($sDef['default_badge_en']) || !empty($sDef['default_badge_ar']))
                            <!-- Eyebrow Badges: High Contrast Design -->
                            <div class="space-y-3 pt-2 border-t border-slate-200/80 dark:border-slate-800">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-slate-400"></span>
                                    <h5 class="text-xs font-black text-slate-800 dark:text-slate-200 uppercase tracking-wider">
                                        {{ $isAr ? 'الشارات التعريفية العلوية (Eyebrow Badges)' : 'Eyebrow Badges (Micro-Tags)' }}
                                    </h5>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <!-- Badge EN -->
                                    <div class="space-y-2 bg-white dark:bg-[#031827] p-4 rounded-2xl border-2 border-slate-200 dark:border-slate-700/80 shadow-sm">
                                        <div class="flex items-center justify-between">
                                            <label class="block text-xs font-black text-slate-800 dark:text-slate-200">
                                                <span class="inline-block px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-[10px] font-mono text-[#0A4F78] dark:text-[#2A8FC2] mr-1.5 ml-1.5 font-bold">EN BADGE</span>
                                                {{ $isAr ? 'الشارة العلوية (الإنجليزية):' : 'Eyebrow Badge (English):' }}
                                            </label>
                                            <span class="text-[10px] font-mono text-slate-500 dark:text-slate-400">
                                                {{ $isAr ? 'افتراضي: ' : 'Default: ' }}<span class="font-bold text-slate-800 dark:text-slate-200">{{ $sDef['default_badge_en'] }}</span>
                                            </span>
                                        </div>
                                        <input type="text" name="section_settings[{{ $sKey }}][badge_en]" 
                                               value="{{ $badgeEn }}" 
                                               placeholder="{{ $sDef['default_badge_en'] }}" 
                                               class="w-full px-4 py-2.5 text-xs font-mono font-bold uppercase tracking-wider rounded-xl border-2 border-slate-300 dark:border-slate-600 dark:bg-[#062B49] text-slate-900 dark:text-white focus:border-[#0A4F78] focus:ring-4 focus:ring-[#0A4F78]/15 transition-all outline-none shadow-inner">
                                    </div>

                                    <!-- Badge AR -->
                                    <div class="space-y-2 bg-white dark:bg-[#031827] p-4 rounded-2xl border-2 border-slate-200 dark:border-slate-700/80 shadow-sm">
                                        <div class="flex items-center justify-between">
                                            <label class="block text-xs font-black text-slate-800 dark:text-slate-200">
                                                <span class="inline-block px-2 py-0.5 rounded bg-[#67B34A]/10 text-[10px] font-mono text-[#67B34A] mr-1.5 ml-1.5 font-bold">AR BADGE</span>
                                                {{ $isAr ? 'الشارة العلوية (العربية):' : 'Eyebrow Badge (Arabic):' }}
                                            </label>
                                            <span class="text-[10px] font-tajawal text-slate-500 dark:text-slate-400">
                                                {{ $isAr ? 'افتراضي: ' : 'Default: ' }}<span class="font-bold text-slate-800 dark:text-slate-200">{{ $sDef['default_badge_ar'] }}</span>
                                            </span>
                                        </div>
                                        <input type="text" name="section_settings[{{ $sKey }}][badge_ar]" 
                                               value="{{ $badgeAr }}" 
                                               placeholder="{{ $sDef['default_badge_ar'] }}" 
                                               dir="rtl" 
                                               class="w-full px-4 py-2.5 text-xs font-tajawal font-bold rounded-xl border-2 border-slate-300 dark:border-slate-600 dark:bg-[#062B49] text-slate-900 dark:text-white focus:border-[#67B34A] focus:ring-4 focus:ring-[#67B34A]/15 transition-all outline-none shadow-inner">
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($sDef['has_custom_content'])
                            <!-- ==========================================================
                                 SECTION CONTENT & VISUAL ELEMENTS STUDIO
                                 ========================================================== -->
                            <div class="mt-5 pt-4 border-t border-slate-200/80 dark:border-slate-800 space-y-4">
                                <!-- Studio Master Bar: Title, Description, and Master Toggle -->
                                <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-slate-50 via-white to-amber-50/20 dark:from-[#031827] dark:via-[#062B49] dark:to-amber-950/20 border-2 {{ $hasCustom ? 'border-amber-500/50 dark:border-amber-500/40 shadow-md' : 'border-slate-200 dark:border-slate-700/80' }} transition-all">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                        <div class="space-y-1 min-w-0 flex-1">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full {{ $hasCustom ? 'bg-amber-500 text-white' : 'bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300' }} font-mono text-[10px] font-black uppercase tracking-wider transition-all"
                                                      id="badge-custom-status-{{ $sKey }}">
                                                    <i class="fa-solid {{ $hasCustom ? 'fa-wand-magic-sparkles' : 'fa-cubes' }}"></i>
                                                    <span>{{ $hasCustom ? ($isAr ? 'عناصر مخصصة مفعلة' : 'BESPOKE ELEMENTS ACTIVE') : ($isAr ? 'القالب التلقائي الافتراضي' : 'STANDARD SYSTEM TEMPLATE') }}</span>
                                                </span>
                                                <span class="text-xs text-slate-500 dark:text-slate-400 font-bold">
                                                    {{ $isAr ? 'التحكم الدقيق بمحتوى وعناصر القسم' : 'Granular Content & Screen Overrides' }}
                                                </span>
                                            </div>

                                            <h4 class="text-sm sm:text-base font-black text-slate-900 dark:text-white flex items-center gap-2">
                                                <span>{{ $isAr ? 'استوديو تخصيص محتوى وعناصر القسم' : 'Section Content & Elements Studio' }}</span>
                                            </h4>
                                            <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed font-normal">
                                                {{ $isAr 
                                                    ? 'يتيح لك استبدال الهيكل التلقائي بمحتوى وعناصر بصرية مخصصة، مع إمكانية التخصيص حسب نوع الشاشة (كمبيوتر وهاتف) وإدراج قوالب جاهزة بضغطة زر.'
                                                    : 'Override default automated elements with bespoke content, cards, badges, and directives with screen-specific targeting (Desktop vs Mobile).' }}
                                            </p>
                                        </div>

                                        <!-- Master Content Studio Switch Pill -->
                                        <div class="shrink-0 flex items-center gap-3">
                                            <label class="bz-switch-pill {{ $hasCustom ? 'is-active' : 'is-inactive' }}" 
                                                   id="pill-custom-master-{{ $sKey }}"
                                                   style="{{ $hasCustom ? 'background-color: rgba(245, 158, 11, 0.15); border-color: rgba(245, 158, 11, 0.6); color: #f59e0b;' : '' }}">
                                                <input type="hidden" name="section_settings[{{ $sKey }}][custom_content_enabled]" value="0">
                                                <input type="checkbox" name="section_settings[{{ $sKey }}][custom_content_enabled]" value="1" 
                                                       id="input-custom-master-{{ $sKey }}"
                                                       class="sr-only"
                                                       onchange="toggleSectionContentStudio(this, '{{ $sKey }}')"
                                                       {{ $hasCustom ? 'checked' : '' }}>
                                                <span class="bz-switch-thumb" style="{{ $hasCustom ? 'background-color: #f59e0b; color: #fff;' : '' }}">
                                                    <i class="fa-solid {{ $hasCustom ? 'fa-wand-magic-sparkles' : 'fa-xmark' }}"></i>
                                                </span>
                                                <i class="fa-solid fa-code text-xs"></i>
                                                <span class="switch-label">{{ $isAr ? 'تخصيص المحتوى' : 'Custom Content' }}</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Studio Drawer Panels (Visible only when custom_content_enabled is checked) -->
                                <div class="{{ $hasCustom ? 'block' : 'hidden' }} space-y-4 pt-1" 
                                     id="studio-drawer-{{ $sKey }}">

                                    <!-- Studio Controls Bar: Screen Mode + Quick Blueprints + Element Inserters -->
                                    <div class="p-3.5 sm:p-4 rounded-2xl bg-white dark:bg-[#031827] border-2 border-slate-200 dark:border-slate-700/80 shadow-sm flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-3">
                                        
                                        <!-- Screen Viewport Target Selector -->
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5 mr-1 ml-1">
                                                <i class="fa-solid fa-sliders text-[#0A4F78] dark:text-[#2A8FC2]"></i>
                                                <span>{{ $isAr ? 'توجيه الشاشات:' : 'Screen Targeting:' }}</span>
                                            </span>

                                            <div class="inline-flex rounded-xl p-1 bg-slate-100 dark:bg-[#062B49] border border-slate-200 dark:border-slate-700">
                                                <label class="px-3 py-1 rounded-lg text-xs font-extrabold cursor-pointer transition-all flex items-center gap-1.5 {{ $contentScreenMode === 'unified' ? 'bg-[#0A4F78] text-white shadow-xs' : 'text-slate-700 dark:text-slate-300 hover:text-slate-900' }}"
                                                       id="lbl-screen-unified-{{ $sKey }}">
                                                    <input type="radio" name="section_settings[{{ $sKey }}][content_screen_mode]" value="unified" 
                                                           class="sr-only" onchange="setSectionScreenMode('{{ $sKey }}', 'unified')"
                                                           {{ $contentScreenMode === 'unified' ? 'checked' : '' }}>
                                                    <i class="fa-solid fa-globe text-[11px]"></i>
                                                    <span>{{ $isAr ? 'جميع الشاشات (متجاوب)' : 'All Screens (Unified)' }}</span>
                                                </label>

                                                <label class="px-3 py-1 rounded-lg text-xs font-extrabold cursor-pointer transition-all flex items-center gap-1.5 {{ $contentScreenMode === 'per_screen' ? 'bg-[#0A4F78] text-white shadow-xs' : 'text-slate-700 dark:text-slate-300 hover:text-slate-900' }}"
                                                       id="lbl-screen-perscreen-{{ $sKey }}">
                                                    <input type="radio" name="section_settings[{{ $sKey }}][content_screen_mode]" value="per_screen" 
                                                           class="sr-only" onchange="setSectionScreenMode('{{ $sKey }}', 'per_screen')"
                                                           {{ $contentScreenMode === 'per_screen' ? 'checked' : '' }}>
                                                    <i class="fa-solid fa-mobile-screen text-[11px]"></i>
                                                    <span>{{ $isAr ? 'تخصيص محتوى الهاتف منفصلاً' : 'Dedicated Mobile Content' }}</span>
                                                </label>
                                            </div>
                                        </div>

                                        <!-- Blueprint & Quick Snippet Inserters -->
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <button type="button" onclick="loadSectionBlueprint('{{ $sKey }}')" 
                                                    class="px-3 py-1.5 rounded-xl bg-amber-500/15 hover:bg-amber-500/25 text-amber-700 dark:text-amber-300 text-xs font-black transition-all flex items-center gap-1.5 border border-amber-500/30 cursor-pointer shadow-xs active:scale-95"
                                                    title="{{ $isAr ? 'تحميل كود القالب الافتراضي لتعديل العناصر الجاهزة' : 'Load default system layout elements into editors' }}">
                                                <i class="fa-solid fa-bolt text-amber-500"></i>
                                                <span>{{ $isAr ? 'تحميل قالب القسم' : 'Load Blueprint' }}</span>
                                            </button>

                                            <div class="hidden sm:flex items-center gap-1">
                                                <button type="button" onclick="insertStudioSnippet('{{ $sKey }}', 'card')" 
                                                        class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 text-[11px] font-bold border border-slate-200 dark:border-slate-700 transition-all cursor-pointer">
                                                    + {{ $isAr ? 'بطاقة' : 'Card' }}
                                                </button>
                                                <button type="button" onclick="insertStudioSnippet('{{ $sKey }}', 'pill')" 
                                                        class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 text-[11px] font-bold border border-slate-200 dark:border-slate-700 transition-all cursor-pointer">
                                                    + {{ $isAr ? 'شارة' : 'Pill' }}
                                                </button>
                                                <button type="button" onclick="insertStudioSnippet('{{ $sKey }}', 'callout')" 
                                                        class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 text-[11px] font-bold border border-slate-200 dark:border-slate-700 transition-all cursor-pointer">
                                                    + {{ $isAr ? 'تنبيه' : 'Alert' }}
                                                </button>
                                                <button type="button" onclick="insertStudioSnippet('{{ $sKey }}', 'grid')" 
                                                        class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 text-[11px] font-bold border border-slate-200 dark:border-slate-700 transition-all cursor-pointer">
                                                    + {{ $isAr ? 'شبكة' : 'Grid' }}
                                                </button>
                                            </div>

                                            <button type="button" onclick="toggleInCardPreview('{{ $sKey }}')" 
                                                    class="px-3 py-1.5 rounded-xl bg-[#0A4F78]/10 hover:bg-[#0A4F78]/20 dark:bg-[#2A8FC2]/20 text-[#0A4F78] dark:text-[#2A8FC2] text-xs font-bold transition-all flex items-center gap-1.5 border border-[#0A4F78]/20 cursor-pointer">
                                                <i class="fa-solid fa-eye"></i>
                                                <span id="btn-text-preview-{{ $sKey }}">{{ $isAr ? 'معاينة العناصر' : 'Live Preview' }}</span>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Bilingual Language Tabs -->
                                    <div class="flex items-center gap-2 border-b border-slate-200 dark:border-slate-800 pb-2">
                                        <button type="button" onclick="setStudioLangTab('{{ $sKey }}', 'en')" 
                                                id="tab-btn-en-{{ $sKey }}"
                                                class="px-4 py-2 rounded-xl text-xs font-black transition-all flex items-center gap-2 cursor-pointer bg-[#0A4F78] text-white shadow-xs">
                                            <span>🇬🇧 English Content</span>
                                        </button>
                                        <button type="button" onclick="setStudioLangTab('{{ $sKey }}', 'ar')" 
                                                id="tab-btn-ar-{{ $sKey }}"
                                                class="px-4 py-2 rounded-xl text-xs font-black transition-all flex items-center gap-2 cursor-pointer bg-slate-100 dark:bg-[#062B49] text-slate-700 dark:text-slate-300 hover:text-[#67B34A]">
                                            <span>🇸🇦 المحتوى بالعربية</span>
                                        </button>
                                    </div>

                                    <!-- English Editor Pane -->
                                    <div class="space-y-4" id="pane-lang-en-{{ $sKey }}">
                                        <!-- Desktop / Standard EN Editor -->
                                        <div class="space-y-2 bg-white dark:bg-[#031827] p-4 sm:p-5 rounded-2xl border-2 border-slate-200 dark:border-slate-700/80 shadow-inner">
                                            <div class="flex items-center justify-between">
                                                <label class="block text-xs font-black text-slate-900 dark:text-white flex items-center gap-2">
                                                    <span class="px-2 py-0.5 rounded bg-[#0A4F78]/10 text-[#0A4F78] dark:text-[#2A8FC2] text-[10px] font-mono font-bold">🇬🇧 EN DESKTOP / MAIN</span>
                                                    <span>{{ $isAr ? 'المحتوى الرئيسي / سطح المكتب (HTML أو نصوص):' : 'Main / Desktop Content (HTML / Rich Elements):' }}</span>
                                                </label>
                                                <span class="text-[10px] font-mono text-slate-400">Responsive Elements</span>
                                            </div>
                                            <textarea name="section_settings[{{ $sKey }}][custom_content_en]" 
                                                      id="editor-en-{{ $sKey }}"
                                                      rows="7"
                                                      oninput="syncCustomContentToSimulator('{{ $sKey }}', 'en')"
                                                      placeholder="<div class=&quot;grid grid-cols-1 md:grid-cols-2 gap-4&quot;>&#10;  <!-- Insert custom cards, clinical parameters or directives here -->&#10;</div>"
                                                      class="w-full p-4 font-mono text-xs text-slate-900 dark:text-slate-100 bg-slate-50 dark:bg-[#062B49] rounded-xl border-2 border-slate-300 dark:border-slate-600 focus:border-[#0A4F78] dark:focus:border-[#2A8FC2] focus:ring-4 focus:ring-[#0A4F78]/15 outline-none transition-all resize-y shadow-inner leading-relaxed">{{ $contentEn }}</textarea>
                                        </div>

                                        <!-- Mobile-Dedicated EN Editor (Conditioned by per_screen mode) -->
                                        <div class="{{ $contentScreenMode === 'per_screen' ? 'block' : 'hidden' }} space-y-2 bg-slate-50/80 dark:bg-[#062B49]/40 p-4 sm:p-5 rounded-2xl border-2 border-dashed border-[#0A4F78]/40 shadow-inner"
                                             id="wrapper-mobile-en-{{ $sKey }}">
                                            <div class="flex items-center justify-between">
                                                <label class="block text-xs font-black text-slate-900 dark:text-white flex items-center gap-2">
                                                    <span class="px-2 py-0.5 rounded bg-amber-500/15 text-amber-600 dark:text-amber-400 text-[10px] font-mono font-bold">📱 EN MOBILE EXCLUSIVE</span>
                                                    <span>{{ $isAr ? 'محتوى مخصص للهاتف فقط (English Mobile Override):' : 'Mobile-Exclusive Viewport Content (English):' }}</span>
                                                </label>
                                                <span class="text-[10px] text-amber-600 dark:text-amber-400 font-bold">Visible on Phones Only</span>
                                            </div>
                                            <textarea name="section_settings[{{ $sKey }}][custom_content_mobile_en]" 
                                                      id="editor-mobile-en-{{ $sKey }}"
                                                      rows="5"
                                                      oninput="syncCustomContentToSimulator('{{ $sKey }}', 'en')"
                                                      placeholder="Compact cards or touch-optimized elements for smartphones..."
                                                      class="w-full p-4 font-mono text-xs text-slate-900 dark:text-slate-100 bg-white dark:bg-[#031827] rounded-xl border-2 border-slate-300 dark:border-slate-600 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/15 outline-none transition-all resize-y shadow-inner leading-relaxed">{{ $contentMobileEn }}</textarea>
                                        </div>
                                    </div>

                                    <!-- Arabic Editor Pane -->
                                    <div class="space-y-4 hidden" id="pane-lang-ar-{{ $sKey }}">
                                        <!-- Desktop / Standard AR Editor -->
                                        <div class="space-y-2 bg-white dark:bg-[#031827] p-4 sm:p-5 rounded-2xl border-2 border-slate-200 dark:border-slate-700/80 shadow-inner">
                                            <div class="flex items-center justify-between" dir="rtl">
                                                <label class="block text-xs font-black text-slate-900 dark:text-white flex items-center gap-2">
                                                    <span class="px-2 py-0.5 rounded bg-[#67B34A]/15 text-[#67B34A] text-[10px] font-mono font-bold">🇸🇦 AR DESKTOP / MAIN</span>
                                                    <span class="font-tajawal">{{ $isAr ? 'المحتوى الرئيسي / سطح المكتب (عربي):' : 'Main / Desktop Content (Arabic):' }}</span>
                                                </label>
                                                <span class="text-[10px] font-tajawal text-slate-400">عناصر متجاوبة</span>
                                            </div>
                                            <textarea name="section_settings[{{ $sKey }}][custom_content_ar]" 
                                                      id="editor-ar-{{ $sKey }}"
                                                      dir="rtl"
                                                      rows="7"
                                                      oninput="syncCustomContentToSimulator('{{ $sKey }}', 'ar')"
                                                      placeholder="<div class=&quot;grid grid-cols-1 md:grid-cols-2 gap-4&quot; dir=&quot;rtl&quot;>&#10;  <!-- أضف البطاقات أو التوجيهات السريرية المخصصة هنا -->&#10;</div>"
                                                      class="w-full p-4 font-mono text-xs text-slate-900 dark:text-slate-100 bg-slate-50 dark:bg-[#062B49] rounded-xl border-2 border-slate-300 dark:border-slate-600 focus:border-[#67B34A] focus:ring-4 focus:ring-[#67B34A]/15 outline-none transition-all resize-y shadow-inner leading-relaxed">{{ $contentAr }}</textarea>
                                        </div>

                                        <!-- Mobile-Dedicated AR Editor (Conditioned by per_screen mode) -->
                                        <div class="{{ $contentScreenMode === 'per_screen' ? 'block' : 'hidden' }} space-y-2 bg-slate-50/80 dark:bg-[#062B49]/40 p-4 sm:p-5 rounded-2xl border-2 border-dashed border-[#67B34A]/40 shadow-inner"
                                             id="wrapper-mobile-ar-{{ $sKey }}">
                                            <div class="flex items-center justify-between" dir="rtl">
                                                <label class="block text-xs font-black text-slate-900 dark:text-white flex items-center gap-2">
                                                    <span class="px-2 py-0.5 rounded bg-amber-500/15 text-amber-600 dark:text-amber-400 text-[10px] font-mono font-bold">📱 AR MOBILE EXCLUSIVE</span>
                                                    <span class="font-tajawal">{{ $isAr ? 'محتوى مخصص للهاتف فقط (بالعربية):' : 'Mobile-Exclusive Viewport Content (Arabic):' }}</span>
                                                </label>
                                                <span class="text-[10px] text-amber-600 dark:text-amber-400 font-bold font-tajawal">يظهر على الهواتف الذكية فقط</span>
                                            </div>
                                            <textarea name="section_settings[{{ $sKey }}][custom_content_mobile_ar]" 
                                                      id="editor-mobile-ar-{{ $sKey }}"
                                                      dir="rtl"
                                                      rows="5"
                                                      oninput="syncCustomContentToSimulator('{{ $sKey }}', 'ar')"
                                                      placeholder="بطاقات ملائمة لشاشات اللمس والهواتف الذكية..."
                                                      class="w-full p-4 font-mono text-xs text-slate-900 dark:text-slate-100 bg-white dark:bg-[#031827] rounded-xl border-2 border-slate-300 dark:border-slate-600 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/15 outline-none transition-all resize-y shadow-inner leading-relaxed">{{ $contentMobileAr }}</textarea>
                                        </div>
                                    </div>

                                    <!-- In-Card Live Rendered Elements Preview Box -->
                                    <div class="hidden p-4 sm:p-5 rounded-2xl bg-white dark:bg-[#031827] border-2 border-dashed border-[#0A4F78]/30 shadow-md space-y-3"
                                         id="incard-preview-{{ $sKey }}">
                                        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-2">
                                            <span class="text-xs font-black text-[#0A4F78] dark:text-[#2A8FC2] uppercase tracking-wider flex items-center gap-1.5">
                                                <i class="fa-solid fa-sparkles"></i>
                                                <span>{{ $isAr ? 'معاينة العناصر الحية في هذه البطاقة' : 'In-Card Rendered Elements Preview' }}</span>
                                            </span>
                                            <button type="button" onclick="renderInCardPreviewContent('{{ $sKey }}')" 
                                                    class="text-xs font-bold text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white transition-colors cursor-pointer flex items-center gap-1">
                                                <i class="fa-solid fa-rotate text-[10px]"></i>
                                                <span>{{ $isAr ? 'تحديث' : 'Refresh' }}</span>
                                            </button>
                                        </div>
                                        <div class="p-4 rounded-xl bg-slate-50 dark:bg-[#062B49] text-xs leading-relaxed text-slate-900 dark:text-slate-100 overflow-x-auto min-h-[60px]"
                                             id="incard-preview-body-{{ $sKey }}">
                                            <!-- Rendered HTML will inject here -->
                                        </div>
                                    </div>

                                </div>
                            </div>
                        @endif

                        <!-- ==========================================================
                             SPECIAL SECTION 08: SYNERGISTIC PROTOCOL BUNDLE BUILDER
                             ========================================================== -->
                        @if($sKey === 'related')
                            <div class="mt-6 pt-5 border-t border-slate-200/80 dark:border-slate-800">
                                <div class="p-6 rounded-2xl bg-gradient-to-br from-white via-slate-50 to-pink-50/30 dark:from-[#062B49] dark:via-[#031827] dark:to-pink-950/20 border-2 border-pink-500/25 shadow-md space-y-6">
                                    
                                    <!-- Bundle Master Header & Switch -->
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                        <div class="space-y-1">
                                            <div class="flex items-center gap-2">
                                                <span class="px-2.5 py-0.5 rounded-full bg-pink-500 text-white font-mono text-[10px] font-black uppercase tracking-wider">
                                                    {{ $isAr ? 'باقة الخصم التكاملي' : 'CONTROLLED DISCOUNT BUNDLE' }}
                                                </span>
                                                <span class="text-xs text-pink-600 dark:text-pink-400 font-bold">
                                                    {{ $isAr ? 'تحويل المنتجات التكميلية إلى بروتوكول بخصم تلقائي' : 'Combine with Complementary Formulations' }}
                                                </span>
                                            </div>
                                            <h4 class="text-base font-black text-slate-900 dark:text-white">
                                                {{ $isAr ? 'بناء باقة التكامل الحيوي الذكية (Synergistic Protocol Bundle)' : 'Synergistic Protocol Bundle & Multi-Product Discount' }}
                                            </h4>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 max-w-xl">
                                                {{ $isAr 
                                                    ? 'عند تفعيل هذه الميزة، تظهر باقة متكاملة في صفحة المنتج تتيح للعميل إضافة المنتجات التكميلية المحددة بضغطة زر واحدة مع خصم مخصص يتم حسابه تلقائياً في السلة.'
                                                    : 'When enabled, customers can add complementary formulations together as an optimal protocol with a single click, receiving an automated controlled discount in cart.' }}
                                            </p>
                                        </div>

                                        <!-- Master Bundle Toggle -->
                                        <label class="bz-switch-pill {{ $bundleEnabled ? 'is-active' : 'is-inactive' }} shrink-0" 
                                               id="pill-bundle-master"
                                               style="{{ $bundleEnabled ? 'background-color: rgba(236, 72, 153, 0.15); border-color: rgba(236, 72, 153, 0.6); color: #ec4899;' : '' }}">
                                            <input type="hidden" name="section_settings[related][bundle][enabled]" value="0">
                                            <input type="checkbox" name="section_settings[related][bundle][enabled]" value="1" 
                                                   id="input-bundle-master"
                                                   class="sr-only"
                                                   onchange="toggleBundleConfiguration(this)"
                                                   {{ $bundleEnabled ? 'checked' : '' }}>
                                            <span class="bz-switch-thumb" style="{{ $bundleEnabled ? 'background-color: #ec4899; color: #fff;' : '' }}">
                                                <i class="fa-solid {{ $bundleEnabled ? 'fa-check' : 'fa-xmark' }}"></i>
                                            </span>
                                            <i class="fa-solid fa-gift text-xs"></i>
                                            <span class="switch-label">{{ $isAr ? 'تفعيل باقة الخصم' : 'Enable Protocol Bundle' }}</span>
                                        </label>
                                    </div>

                                    <!-- Bundle Configuration Drawer -->
                                    <div id="bundleConfigDrawer" class="{{ $bundleEnabled ? 'block' : 'hidden' }} space-y-6 pt-4 border-t border-pink-500/15">
                                        
                                        <!-- Discount Type & Value Row -->
                                        <div class="grid grid-cols-1 md:grid-cols-12 gap-5 items-end">
                                            
                                            <!-- Discount Type (Percentage vs Fixed) -->
                                            <div class="md:col-span-5 space-y-2">
                                                <label class="block text-xs font-black text-slate-700 dark:text-slate-300">
                                                    <i class="fa-solid fa-percent text-pink-500 mr-1 ml-1"></i>
                                                    {{ $isAr ? 'نوع الخصم المطبق على الباقة:' : 'Bundle Discount Type:' }}
                                                </label>
                                                <div class="grid grid-cols-2 gap-2">
                                                    <label class="flex items-center justify-center gap-2 p-2.5 rounded-xl border cursor-pointer font-bold text-xs transition-all {{ $bundleDiscType === 'percentage' ? 'bg-pink-500 text-white border-pink-600 shadow-md' : 'bg-white dark:bg-[#031827] text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700' }}"
                                                           id="btnDiscTypePercentage">
                                                        <input type="radio" name="section_settings[related][bundle][discount_type]" value="percentage" 
                                                               {{ $bundleDiscType === 'percentage' ? 'checked' : '' }} 
                                                               onchange="setBundleDiscountType('percentage')" 
                                                               class="sr-only">
                                                        <i class="fa-solid fa-percent"></i>
                                                        <span>{{ $isAr ? 'نسبة مئوية (%)' : 'Percentage (%)' }}</span>
                                                    </label>
                                                    <label class="flex items-center justify-center gap-2 p-2.5 rounded-xl border cursor-pointer font-bold text-xs transition-all {{ $bundleDiscType === 'fixed' ? 'bg-pink-500 text-white border-pink-600 shadow-md' : 'bg-white dark:bg-[#031827] text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700' }}"
                                                           id="btnDiscTypeFixed">
                                                        <input type="radio" name="section_settings[related][bundle][discount_type]" value="fixed" 
                                                               {{ $bundleDiscType === 'fixed' ? 'checked' : '' }} 
                                                               onchange="setBundleDiscountType('fixed')" 
                                                               class="sr-only">
                                                        <i class="fa-solid fa-coins"></i>
                                                        <span>{{ $isAr ? 'مبلغ ثابت (ج.م)' : 'Fixed (EGP)' }}</span>
                                                    </label>
                                                </div>
                                            </div>

                                            <!-- Discount Value & Quick Presets -->
                                            <div class="md:col-span-7 space-y-2">
                                                <div class="flex items-center justify-between">
                                                    <label class="block text-xs font-black text-slate-700 dark:text-slate-300">
                                                        {{ $isAr ? 'قيمة الخصم:' : 'Discount Value:' }}
                                                    </label>
                                                    <div class="flex items-center gap-1">
                                                        <span class="text-[10px] text-slate-400 font-mono">{{ $isAr ? 'خيارات سريعة:' : 'Presets:' }}</span>
                                                        <button type="button" onclick="setDiscountPreset(10)" class="px-2 py-0.5 rounded bg-pink-100 dark:bg-pink-950/40 text-pink-700 dark:text-pink-300 text-[10px] font-mono font-bold hover:bg-pink-200 transition-colors">10%</button>
                                                        <button type="button" onclick="setDiscountPreset(15)" class="px-2 py-0.5 rounded bg-pink-100 dark:bg-pink-950/40 text-pink-700 dark:text-pink-300 text-[10px] font-mono font-bold hover:bg-pink-200 transition-colors">15%</button>
                                                        <button type="button" onclick="setDiscountPreset(20)" class="px-2 py-0.5 rounded bg-pink-100 dark:bg-pink-950/40 text-pink-700 dark:text-pink-300 text-[10px] font-mono font-bold hover:bg-pink-200 transition-colors">20%</button>
                                                    </div>
                                                </div>
                                                <div class="relative">
                                                    <input type="number" step="0.5" min="0" 
                                                           name="section_settings[related][bundle][discount_value]" 
                                                           id="inputBundleDiscountValue"
                                                           value="{{ $bundleDiscVal }}" 
                                                           oninput="recalculateBundleHUD()"
                                                           class="w-full px-4 py-2.5 text-sm font-mono font-bold rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-[#031827] dark:text-white focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500">
                                                    <span class="absolute inset-y-0 end-4 flex items-center text-xs font-black text-pink-600 dark:text-pink-400 font-mono" id="bundleDiscountUnitLabel">
                                                        {{ $bundleDiscType === 'percentage' ? '%' : 'EGP' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Promotional Badges Row (EN & AR) -->
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div class="space-y-1.5">
                                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                                                    <span class="px-1.5 py-0.5 rounded bg-slate-200 dark:bg-slate-700 text-[10px] font-mono font-bold">EN</span>
                                                    {{ $isAr ? 'شارة العرض الترويجي (الإنجليزية):' : 'Promotional Bundle Badge (EN):' }}
                                                </label>
                                                <input type="text" name="section_settings[related][bundle][badge_en]" 
                                                       id="inputBundleBadgeEn"
                                                       value="{{ $bundleBadgeEn }}" 
                                                       placeholder="Optimal Synergistic Protocol"
                                                       class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-[#031827] dark:text-white font-mono">
                                            </div>
                                            <div class="space-y-1.5">
                                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                                                    <span class="px-1.5 py-0.5 rounded bg-pink-100 text-pink-700 text-[10px] font-mono font-bold">AR</span>
                                                    {{ $isAr ? 'شارة العرض الترويجي (العربية):' : 'Promotional Bundle Badge (AR):' }}
                                                </label>
                                                <input type="text" name="section_settings[related][bundle][badge_ar]" 
                                                       id="inputBundleBadgeAr"
                                                       value="{{ $bundleBadgeAr }}" 
                                                       dir="rtl"
                                                       placeholder="البروتوكول التكاملي الموصى به"
                                                       class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-700 dark:bg-[#031827] dark:text-white font-tajawal">
                                            </div>
                                        </div>

                                        <!-- Complementary Products Multi-Selector -->
                                        <div class="space-y-3">
                                            <div class="flex items-center justify-between">
                                                <label class="block text-xs font-black text-slate-800 dark:text-slate-200">
                                                    <i class="fa-solid fa-cubes-stacked text-pink-500 mr-1 ml-1"></i>
                                                    {{ $isAr ? 'اختر المنتجات التكميلية المندرجة في الباقة:' : 'Select Complementary Products in this Protocol:' }}
                                                </label>
                                                <input type="text" id="bundleProductFilterInput" 
                                                       placeholder="{{ $isAr ? 'تصفية المنتجات...' : 'Filter products...' }}" 
                                                       oninput="filterBundleProductCards(this.value)"
                                                       class="px-3 py-1 text-xs rounded-lg border border-slate-200 dark:border-slate-700 dark:bg-[#031827] dark:text-white w-48">
                                            </div>

                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-72 overflow-y-auto p-2 bg-white/70 dark:bg-[#031827]/70 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-inner" id="bundleProductsPickerList">
                                                @forelse($allProductList as $compProd)
                                                    @php
                                                        $isSelected = in_array($compProd->id, $bundleProductIds);
                                                        $compPrice = (float) ($compProd->sale_price ?: $compProd->price);
                                                        $compName = $compProd->{'name_' . app()->getLocale()} ?? $compProd->name_en;
                                                    @endphp
                                                    <div class="bz-bundle-product-card flex items-center justify-between p-3 rounded-xl border border-slate-200 dark:border-slate-800 {{ $isSelected ? 'is-selected' : 'bg-white dark:bg-[#062B49]' }}"
                                                         id="bundle-item-card-{{ $compProd->id }}"
                                                         data-prod-id="{{ $compProd->id }}"
                                                         data-prod-name="{{ $compName }}"
                                                         data-prod-price="{{ $compPrice }}"
                                                         onclick="toggleBundleProductCard({{ $compProd->id }})">
                                                        
                                                        <div class="flex items-center gap-3">
                                                            <input type="checkbox" 
                                                                   name="section_settings[related][bundle][product_ids][]" 
                                                                   value="{{ $compProd->id }}" 
                                                                   id="bundle-prod-check-{{ $compProd->id }}"
                                                                   class="w-4 h-4 rounded text-pink-600 focus:ring-pink-500 cursor-pointer pointer-events-none"
                                                                   {{ $isSelected ? 'checked' : '' }}>
                                                            
                                                            <div class="w-10 h-10 rounded-lg overflow-hidden bg-slate-100 dark:bg-slate-800 border shrink-0">
                                                                <img src="{{ asset($compProd->image ?: 'image.jpg') }}" alt="" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';">
                                                            </div>
                                                            <div>
                                                                <div class="text-xs font-bold text-slate-800 dark:text-white line-clamp-1 prod-title">{{ $compName }}</div>
                                                                <div class="text-[10px] font-mono text-slate-400 prod-sku">{{ $compProd->sku }}</div>
                                                            </div>
                                                        </div>

                                                        <div class="text-end shrink-0 ps-2">
                                                            <div class="text-xs font-mono font-black text-pink-600 dark:text-pink-400">@currency($compPrice)</div>
                                                        </div>
                                                    </div>
                                                @empty
                                                    <div class="col-span-2 text-center p-6 text-xs text-slate-400">
                                                        {{ $isAr ? 'لا توجد منتجات أخرى نشطة في المتجر للربط.' : 'No other active products available in catalog.' }}
                                                    </div>
                                                @endforelse
                                            </div>
                                        </div>

                                        <!-- Live Dynamic Calculation HUD Card -->
                                        <div class="p-4 rounded-2xl bg-gradient-to-br from-[#02131F] to-[#0A4F78] text-white space-y-3 shadow-lg border border-white/10" id="bundleLiveCalculationHUD">
                                            <div class="flex items-center justify-between text-xs font-mono border-b border-white/10 pb-2">
                                                <span class="flex items-center gap-1.5 text-pink-300 font-bold">
                                                    <i class="fa-solid fa-calculator"></i>
                                                    {{ $isAr ? 'الحساب التلقائي لأسعار باقة البروتوكول:' : 'Protocol Bundle Pricing Breakdown:' }}
                                                </span>
                                                <span class="px-2 py-0.5 rounded-full bg-pink-500/20 text-pink-200 text-[10px] font-bold" id="bundleItemCountPill">
                                                    1 + 0 items
                                                </span>
                                            </div>

                                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
                                                <div class="bg-white/5 p-2 rounded-xl">
                                                    <div class="text-[10px] text-white/60 uppercase font-mono">{{ $isAr ? 'المنتج الحالي' : 'Primary Product' }}</div>
                                                    <div class="text-xs font-mono font-bold text-white mt-0.5">EGP <span id="hudPrimaryPrice">{{ number_format($currentProductPrice, 2) }}</span></div>
                                                </div>
                                                <div class="bg-white/5 p-2 rounded-xl">
                                                    <div class="text-[10px] text-white/60 uppercase font-mono">{{ $isAr ? 'المجموع الأصلي' : 'Original Total' }}</div>
                                                    <div class="text-xs font-mono font-bold text-slate-300 mt-0.5 line-through">EGP <span id="hudOriginalTotal">{{ number_format($currentProductPrice, 2) }}</span></div>
                                                </div>
                                                <div class="bg-pink-500/15 p-2 rounded-xl border border-pink-500/30">
                                                    <div class="text-[10px] text-pink-300 uppercase font-mono">{{ $isAr ? 'قيمة الخصم' : 'Savings' }}</div>
                                                    <div class="text-xs font-mono font-black text-pink-400 mt-0.5">-EGP <span id="hudDiscountAmount">0.00</span></div>
                                                </div>
                                                <div class="bg-emerald-500/20 p-2 rounded-xl border border-emerald-500/40">
                                                    <div class="text-[10px] text-[#B8D98A] uppercase font-mono">{{ $isAr ? 'السعر النهائي للباقة' : 'Bundle Price' }}</div>
                                                    <div class="text-sm font-mono font-black text-[#67B34A] mt-0.5">EGP <span id="hudFinalPrice">{{ number_format($currentProductPrice, 2) }}</span></div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            @endforeach
        </div>

        <!-- ======================================================
             RIGHT COLUMN: STICKY LIVE INTERACTIVE STOREFRONT PREVIEW
             ====================================================== -->
        <div class="2xl:col-span-5 sticky top-24 space-y-4" id="customizerStickyPreviewStudio">
            <div class="rounded-3xl bg-white dark:bg-[#031827] border border-slate-200/90 dark:border-slate-800 shadow-xl overflow-hidden">
                
                <!-- Studio Header & Viewport Mode Switcher -->
                <div class="p-4 bg-gradient-to-r from-[#02131F] to-[#0A4F78] text-white flex items-center justify-between border-b border-white/10">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#67B34A] bz-live-dot"></span>
                        <h4 class="text-xs font-black tracking-wider uppercase">
                            {{ $isAr ? 'معاينة حية للمتجر' : 'Live Storefront Simulator' }}
                        </h4>
                    </div>

                    <div class="flex items-center gap-2">
                        <!-- Preview Device Toggle -->
                        <div class="inline-flex rounded-lg p-0.5 bg-black/30 border border-white/15">
                            <button type="button" onclick="setStudioPreviewMode('desktop')" id="btnPreviewDesktop"
                                    class="px-2.5 py-1 rounded-md text-[10px] font-bold text-white bg-white/20 transition-all flex items-center gap-1">
                                <i class="fa-solid fa-display"></i>
                                <span>Desktop</span>
                            </button>
                            <button type="button" onclick="setStudioPreviewMode('mobile')" id="btnPreviewMobile"
                                    class="px-2.5 py-1 rounded-md text-[10px] font-bold text-white/70 hover:text-white transition-all flex items-center gap-1">
                                <i class="fa-solid fa-mobile-screen"></i>
                                <span>Mobile</span>
                            </button>
                        </div>

                        <!-- Language Switch -->
                        <button type="button" onclick="toggleStudioPreviewLang()" id="btnPreviewLang"
                                class="px-2.5 py-1 rounded-lg bg-white/10 hover:bg-white/20 text-[10px] font-mono font-bold text-white border border-white/15 transition-all">
                            {{ $isAr ? 'AR' : 'EN' }}
                        </button>
                    </div>
                </div>

                <!-- Simulator Screen Body -->
                <div class="p-4 sm:p-5 bg-slate-50 dark:bg-[#02131F] max-h-[750px] overflow-y-auto space-y-4" id="previewDeviceBody">
                    
                    <!-- Simulated Product Header Capsule -->
                    <div class="p-4 rounded-2xl bg-white dark:bg-[#062B49] border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-800 overflow-hidden border shrink-0">
                            <img src="{{ asset($product['image'] ?? 'image.jpg') }}" alt="" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';">
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-[10px] font-mono text-[#0A4F78] dark:text-[#2A8FC2] font-black uppercase tracking-wider">
                                {{ $product['category_name_en'] ?? 'CELLULAR LONGEVITY' }}
                            </div>
                            <div class="text-xs font-black text-slate-900 dark:text-white truncate" id="simProductName">
                                {{ $currentProductName }}
                            </div>
                            <div class="text-xs font-mono font-bold text-[#67B34A]">
                                @currency($currentProductPrice)
                            </div>
                        </div>
                    </div>

                    <!-- Simulated Accordion Sections List -->
                    <div class="space-y-2.5" id="simSectionsList">
                        @foreach($sections as $sKey => $sDef)
                            @php
                                $sConf = $secData[$sKey] ?? [];
                                $enDesktop = array_key_exists('enabled_desktop', $sConf) ? (bool) $sConf['enabled_desktop'] : true;
                                $enMobile = array_key_exists('enabled_mobile', $sConf) ? (bool) $sConf['enabled_mobile'] : true;
                                $defOpen = array_key_exists('default_open', $sConf) ? (bool) $sConf['default_open'] : ($sDef['default_open'] ?? false);
                                $tTitle = !empty($sConf['title_' . app()->getLocale()]) ? $sConf['title_' . app()->getLocale()] : ($sConf['title_en'] ?? $sDef['default_title_' . app()->getLocale()]);
                            @endphp
                            <div class="sim-section-card rounded-xl bg-white dark:bg-[#062B49] border border-slate-200/70 dark:border-slate-800 overflow-hidden transition-all duration-200"
                                 id="sim-sec-{{ $sKey }}"
                                 data-desktop="{{ $enDesktop ? '1' : '0' }}"
                                 data-mobile="{{ $enMobile ? '1' : '0' }}">
                                
                                <div class="p-3 flex items-center justify-between cursor-pointer select-none"
                                     onclick="toggleSimAccordion('{{ $sKey }}')">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <span class="w-6 h-6 rounded-lg flex items-center justify-center text-[11px] shrink-0" 
                                              style="background-color: {{ $sDef['color_light'] }}; color: {{ $sDef['color'] }};">
                                            <i class="fa-solid {{ $sDef['icon'] }}"></i>
                                        </span>
                                        <div class="min-w-0 flex-1">
                                            <div class="text-xs font-black text-slate-900 dark:text-white truncate" id="sim-title-{{ $sKey }}">
                                                {{ $tTitle }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-1.5 shrink-0">
                                        <span class="sim-hidden-badge hidden text-[9px] font-mono px-1.5 py-0.5 rounded bg-rose-500/10 text-rose-500 font-bold">
                                            HIDDEN
                                        </span>
                                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200 {{ $defOpen ? 'rotate-180' : '' }}" id="sim-chevron-{{ $sKey }}"></i>
                                    </div>
                                </div>

                                <div class="sim-drawer {{ $defOpen ? 'block' : 'hidden' }} px-3 pb-3 pt-1 border-t border-slate-100 dark:border-slate-800/60 text-[10px] text-slate-500 dark:text-slate-400 space-y-1.5" id="sim-drawer-{{ $sKey }}">
                                    @if($sKey === 'buy_box')
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-1 rounded bg-[#0A4F78] text-white font-bold flex-1 text-center">Add to Cart</span>
                                            <span class="px-2 py-1 rounded bg-slate-100 dark:bg-slate-800 font-mono">Qty: 1</span>
                                        </div>
                                    @elseif($sKey === 'related')
                                        <!-- Mini Protocol Bundle Preview in Simulator -->
                                        <div class="p-2.5 rounded-lg bg-pink-500/10 border border-pink-500/25 space-y-1.5 {{ $bundleEnabled ? 'block' : 'hidden' }}" id="simBundlePreviewBox">
                                            <div class="flex items-center justify-between text-[10px] font-bold text-pink-600 dark:text-pink-400">
                                                <span id="simBundleBadgeText">{{ $bundleBadgeEn }}</span>
                                                <span class="font-mono" id="simBundleSaveBadge">SAVE 15%</span>
                                            </div>
                                            <div class="text-[9px] text-slate-500 dark:text-slate-400">
                                                1-Click Add Protocol with Complementary Formulations
                                            </div>
                                        </div>
                                        <div class="text-[9px] text-slate-400 italic">
                                            Renders complementary formulations catalog grid.
                                        </div>
                                    @else
                                        @php
                                            $sHasCustom = !empty($sConf['custom_content_enabled']);
                                            $sCustomEn = $sConf['custom_content_en'] ?? '';
                                            $sCustomAr = $sConf['custom_content_ar'] ?? '';
                                            $sCustomText = app()->getLocale() === 'ar' ? ($sCustomAr ?: $sCustomEn) : ($sCustomEn ?: $sCustomAr);
                                        @endphp
                                        <div class="text-[10px] leading-relaxed text-slate-600 dark:text-slate-300" id="sim-content-{{ $sKey }}">
                                            @if($sHasCustom && !empty($sCustomText))
                                                <div class="p-2 rounded-lg bg-amber-500/10 border border-amber-500/25 space-y-1">
                                                    <span class="font-bold text-amber-600 dark:text-amber-400 block text-[9px] uppercase tracking-wider">
                                                        <i class="fa-solid fa-wand-magic-sparkles mr-1 ml-1"></i> {{ $isAr ? 'عناصر مخصصة نشطة' : 'Bespoke Elements Active' }}
                                                    </span>
                                                    <div class="line-clamp-2 text-[9px] font-mono text-slate-500 dark:text-slate-400">
                                                        {{ strip_tags($sCustomText) }}
                                                    </div>
                                                </div>
                                            @else
                                                {{ $sDef['description_en'] }}
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="text-center pt-2 text-[10px] text-slate-400 font-mono">
                        <i class="fa-solid fa-bolt text-amber-500 mr-1 ml-1"></i>
                        {{ $isAr ? 'التعديلات على اليسار تنعكس فورياً في هذه المعاينة.' : 'Live instant synchronization with edits.' }}
                    </div>

                </div>
            </div>
        </div>

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

        // Update Simulator Sync
        syncSimulatorCard(secKey);

        // Re-evaluate Active Device Scope Filtering if active
        reapplyCurrentScopeFilter();
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
            if (input) {
                input.value = defaultValue;
                syncPreviewTitle(secKey, defaultValue, 'en');
            }
        } else if (fieldType === 'title_ar') {
            const input = document.getElementById('input-title-ar-' + secKey);
            if (input) {
                input.value = defaultValue;
                syncPreviewTitle(secKey, defaultValue, 'ar');
            }
        }
    }

    // ==========================================================
    // SYNERGISTIC PROTOCOL BUNDLE CONTROLLER & DYNAMIC HUD
    // ==========================================================
    let currentDiscountType = '{{ $bundleDiscType }}';
    const primaryProductPrice = {{ $currentProductPrice }};

    function toggleBundleConfiguration(toggleInput) {
        const isChecked = toggleInput.checked;
        const pill = document.getElementById('pill-bundle-master');
        const drawer = document.getElementById('bundleConfigDrawer');
        const simBundleBox = document.getElementById('simBundlePreviewBox');

        if (isChecked) {
            pill.classList.remove('is-inactive');
            pill.classList.add('is-active');
            pill.style.backgroundColor = 'rgba(236, 72, 153, 0.15)';
            pill.style.borderColor = 'rgba(236, 72, 153, 0.6)';
            pill.style.color = '#ec4899';
            pill.querySelector('.bz-switch-thumb').style.backgroundColor = '#ec4899';
            pill.querySelector('.bz-switch-thumb').style.color = '#fff';
            pill.querySelector('.bz-switch-thumb i').className = 'fa-solid fa-check';
            if (drawer) drawer.classList.remove('hidden');
            if (simBundleBox) simBundleBox.classList.remove('hidden');
        } else {
            pill.classList.remove('is-active');
            pill.classList.add('is-inactive');
            pill.style.backgroundColor = '';
            pill.style.borderColor = '';
            pill.style.color = '';
            pill.querySelector('.bz-switch-thumb').style.backgroundColor = '';
            pill.querySelector('.bz-switch-thumb').style.color = '';
            pill.querySelector('.bz-switch-thumb i').className = 'fa-solid fa-xmark';
            if (drawer) drawer.classList.add('hidden');
            if (simBundleBox) simBundleBox.classList.add('hidden');
        }

        recalculateBundleHUD();
    }

    function setBundleDiscountType(type) {
        currentDiscountType = type;
        const btnPct = document.getElementById('btnDiscTypePercentage');
        const btnFix = document.getElementById('btnDiscTypeFixed');
        const unitLbl = document.getElementById('bundleDiscountUnitLabel');

        if (type === 'percentage') {
            btnPct.className = 'flex items-center justify-center gap-2 p-2.5 rounded-xl border cursor-pointer font-bold text-xs transition-all bg-pink-500 text-white border-pink-600 shadow-md';
            btnFix.className = 'flex items-center justify-center gap-2 p-2.5 rounded-xl border cursor-pointer font-bold text-xs transition-all bg-white dark:bg-[#031827] text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700';
            if (unitLbl) unitLbl.textContent = '%';
        } else {
            btnFix.className = 'flex items-center justify-center gap-2 p-2.5 rounded-xl border cursor-pointer font-bold text-xs transition-all bg-pink-500 text-white border-pink-600 shadow-md';
            btnPct.className = 'flex items-center justify-center gap-2 p-2.5 rounded-xl border cursor-pointer font-bold text-xs transition-all bg-white dark:bg-[#031827] text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700';
            if (unitLbl) unitLbl.textContent = 'EGP';
        }

        recalculateBundleHUD();
    }

    function setDiscountPreset(val) {
        const input = document.getElementById('inputBundleDiscountValue');
        if (input) {
            input.value = val;
            recalculateBundleHUD();
        }
    }

    function toggleBundleProductCard(prodId) {
        const check = document.getElementById('bundle-prod-check-' + prodId);
        const card = document.getElementById('bundle-item-card-' + prodId);
        if (!check || !card) return;

        check.checked = !check.checked;
        if (check.checked) {
            card.classList.add('is-selected');
        } else {
            card.classList.remove('is-selected');
        }

        recalculateBundleHUD();
    }

    function filterBundleProductCards(term) {
        const q = term.toLowerCase().trim();
        const cards = document.querySelectorAll('.bz-bundle-product-card');
        cards.forEach(card => {
            const title = card.querySelector('.prod-title')?.textContent.toLowerCase() || '';
            const sku = card.querySelector('.prod-sku')?.textContent.toLowerCase() || '';
            if (title.includes(q) || sku.includes(q)) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function recalculateBundleHUD() {
        const selectedChecks = document.querySelectorAll('input[name="section_settings[related][bundle][product_ids][]"]:checked');
        let compTotal = 0;
        let selectedCount = 0;

        selectedChecks.forEach(chk => {
            const card = chk.closest('.bz-bundle-product-card');
            if (card) {
                const pr = parseFloat(card.dataset.prodPrice) || 0;
                compTotal += pr;
                selectedCount++;
            }
        });

        const originalTotal = primaryProductPrice + compTotal;
        const discountInput = document.getElementById('inputBundleDiscountValue');
        const discountVal = parseFloat(discountInput?.value) || 0;

        let discountAmount = 0;
        if (currentDiscountType === 'percentage') {
            discountAmount = (originalTotal * discountVal) / 100.0;
        } else {
            discountAmount = Math.min(originalTotal, discountVal);
        }

        const finalPrice = Math.max(0, originalTotal - discountAmount);
        const pctSavings = originalTotal > 0 ? Math.round((discountAmount / originalTotal) * 100) : 0;

        // Update HUD Elements
        const hudOriginal = document.getElementById('hudOriginalTotal');
        const hudDiscount = document.getElementById('hudDiscountAmount');
        const hudFinal = document.getElementById('hudFinalPrice');
        const hudCount = document.getElementById('bundleItemCountPill');

        if (hudOriginal) hudOriginal.textContent = originalTotal.toFixed(2);
        if (hudDiscount) hudDiscount.textContent = discountAmount.toFixed(2);
        if (hudFinal) hudFinal.textContent = finalPrice.toFixed(2);
        if (hudCount) hudCount.textContent = `1 + ${selectedCount} items`;

        // Update Simulator mini badge
        const simSaveBadge = document.getElementById('simBundleSaveBadge');
        if (simSaveBadge) {
            simSaveBadge.textContent = currentDiscountType === 'percentage' 
                ? `SAVE ${discountVal}%` 
                : `SAVE EGP ${discountVal.toFixed(0)}`;
        }
    }

    // ==========================================================
    // STICKY SIMULATOR CONTROLLER & REAL-TIME REFLECTION
    // ==========================================================
    let activeStudioMode = 'desktop';
    let activeStudioLang = '{{ app()->getLocale() }}';

    function setStudioPreviewMode(mode) {
        activeStudioMode = mode;
        const btnD = document.getElementById('btnPreviewDesktop');
        const btnM = document.getElementById('btnPreviewMobile');
        const simBody = document.getElementById('previewDeviceBody');

        if (mode === 'desktop') {
            btnD.className = 'px-2.5 py-1 rounded-md text-[10px] font-bold text-white bg-white/20 transition-all flex items-center gap-1';
            btnM.className = 'px-2.5 py-1 rounded-md text-[10px] font-bold text-white/70 hover:text-white transition-all flex items-center gap-1';
            simBody?.classList.remove('bz-preview-device', 'is-mobile-mode');
        } else {
            btnM.className = 'px-2.5 py-1 rounded-md text-[10px] font-bold text-white bg-white/20 transition-all flex items-center gap-1';
            btnD.className = 'px-2.5 py-1 rounded-md text-[10px] font-bold text-white/70 hover:text-white transition-all flex items-center gap-1';
            simBody?.classList.add('bz-preview-device', 'is-mobile-mode');
        }

        triggerStickyPreviewSync();
    }

    function toggleStudioPreviewLang() {
        activeStudioLang = activeStudioLang === 'en' ? 'ar' : 'en';
        const btn = document.getElementById('btnPreviewLang');
        if (btn) btn.textContent = activeStudioLang.toUpperCase();
        triggerStickyPreviewSync();
    }

    function syncSimulatorCard(secKey) {
        const simCard = document.getElementById('sim-sec-' + secKey);
        const inputDesktop = document.getElementById('input-desktop-' + secKey);
        const inputMobile = document.getElementById('input-mobile-' + secKey);
        if (!simCard) return;

        const isDesktop = inputDesktop?.checked ?? true;
        const isMobile = inputMobile?.checked ?? true;

        simCard.dataset.desktop = isDesktop ? '1' : '0';
        simCard.dataset.mobile = isMobile ? '1' : '0';

        const isVisibleInCurrentMode = (activeStudioMode === 'desktop') ? isDesktop : isMobile;
        const hiddenBadge = simCard.querySelector('.sim-hidden-badge');

        if (isVisibleInCurrentMode) {
            simCard.style.opacity = '1';
            simCard.style.filter = 'none';
            if (hiddenBadge) hiddenBadge.classList.add('hidden');
        } else {
            simCard.style.opacity = '0.4';
            simCard.style.filter = 'grayscale(100%)';
            if (hiddenBadge) hiddenBadge.classList.remove('hidden');
        }
    }

    function syncPreviewTitle(secKey, title, lang) {
        if (lang === activeStudioLang) {
            const simTitle = document.getElementById('sim-title-' + secKey);
            if (simTitle && title.trim()) {
                simTitle.textContent = title;
            }
        }
    }

    function toggleSimAccordion(secKey) {
        const drawer = document.getElementById('sim-drawer-' + secKey);
        const icon = document.getElementById('sim-chevron-' + secKey);
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
    // SECTION CONTENT & VISUAL ELEMENTS STUDIO CONTROLLER
    // ==========================================================
    const sectionBlueprints = {
        dossier: {
            en: `<div class="p-6 rounded-3xl bg-[#0A4F78]/5 dark:bg-[#062B49]/50 border border-[#0A4F78]/15 space-y-3">
  <div class="flex items-center gap-2">
    <span class="w-2.5 h-2.5 rounded-full bg-[#67B34A]"></span>
    <span class="text-xs font-mono font-bold uppercase tracking-wider text-[#0A4F78] dark:text-[#2A8FC2]">Master Monograph Overview</span>
  </div>
  <p class="text-xs sm:text-sm text-[#031827]/80 dark:text-[#F6F5EF]/80 font-medium leading-relaxed">
    Comprehensive pharmaceutical monograph and clinical protocol dossier for this advanced longevity formulation.
  </p>
</div>`,
            ar: `<div class="p-6 rounded-3xl bg-[#0A4F78]/5 dark:bg-[#062B49]/50 border border-[#0A4F78]/15 space-y-3" dir="rtl">
  <div class="flex items-center gap-2">
    <span class="w-2.5 h-2.5 rounded-full bg-[#67B34A]"></span>
    <span class="text-xs font-tajawal font-bold uppercase tracking-wider text-[#0A4F78] dark:text-[#2A8FC2]">نظرة عامة على الملف الطبي المعتمد</span>
  </div>
  <p class="text-xs sm:text-sm text-[#031827]/80 dark:text-[#F6F5EF]/80 font-medium leading-relaxed font-tajawal">
    الملف الدوائي والمواصفات السريرية المعتمدة لهذا المستحضر المتطور لدعم طول العمر والنشاط الخلوي.
  </p>
</div>`
        },
        sec1: {
            en: `<p class="text-xs sm:text-sm text-[#031827]/75 dark:text-[#F6F5EF]/75 leading-relaxed font-medium mb-4">
  Every active bio-compound in this formulation is calibrated to therapeutic clinical thresholds using triple third-party HPLC verification, guaranteeing maximum cellular uptake and batch-to-batch consistency.
</p>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
  <div class="p-4 rounded-2xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 space-y-2 hover:border-[#67B34A]/50 transition-colors">
    <div class="flex justify-between items-start gap-2">
      <span class="text-xs font-bold text-[#031827] dark:text-[#F6F5EF]">Active Bio-Compound Matrix</span>
      <span class="px-2.5 py-0.5 rounded-full bg-[#67B34A]/15 text-[#67B34A] text-[11px] font-mono font-black shrink-0">Standardized</span>
    </div>
    <p class="text-[11px] text-[#031827]/70 dark:text-[#F6F5EF]/70 leading-relaxed font-medium">Targeted cellular biological pathway and cellular energy support.</p>
    <div class="pt-2 border-t border-[#0A4F78]/10 flex items-center justify-between text-[10px] text-[#0A4F78] dark:text-[#2A8FC2] font-mono">
      <span><i class="fa-solid fa-seedling mr-1 text-[#67B34A]"></i> Botanical Bio-Active</span>
      <span>99%+ PURE</span>
    </div>
  </div>
</div>
<div class="mt-4 p-4 rounded-2xl bg-[#0A4F78]/5 dark:bg-white/5 border border-[#0A4F78]/10 flex flex-wrap gap-4 items-center justify-around text-xs font-bold text-[#0A4F78] dark:text-[#2A8FC2]">
  <span><i class="fa-solid fa-check-double text-[#67B34A] mr-1.5"></i> Triple HPLC Assayed</span>
  <span><i class="fa-solid fa-shield-virus text-[#67B34A] mr-1.5"></i> Heavy Metal Screened</span>
  <span><i class="fa-solid fa-dna text-[#67B34A] mr-1.5"></i> 100% Non-GMO & Vegan</span>
</div>`,
            ar: `<p class="text-xs sm:text-sm text-[#031827]/75 dark:text-[#F6F5EF]/75 leading-relaxed font-medium mb-4 font-tajawal" dir="rtl">
  تخضع جميع المواد الفعالة في هذه التركيبة لمعايرة مخبرية دقيقة وفق دستور الأدوية العالمي، لضمان أعلى توافر حيوي وامتصاص خلوي دون أي شوائب اصطناعية.
</p>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" dir="rtl">
  <div class="p-4 rounded-2xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 space-y-2 hover:border-[#67B34A]/50 transition-colors">
    <div class="flex justify-between items-start gap-2">
      <span class="text-xs font-bold text-[#031827] dark:text-[#F6F5EF] font-tajawal">مصفوفة المركبات النشطة المعتمدة</span>
      <span class="px-2.5 py-0.5 rounded-full bg-[#67B34A]/15 text-[#67B34A] text-[11px] font-mono font-black shrink-0">معياري</span>
    </div>
    <p class="text-[11px] text-[#031827]/70 dark:text-[#F6F5EF]/70 leading-relaxed font-medium font-tajawal">دعم الوظائف الحيوية الخلوية وتنشيط مسارات الطاقة الميتوكوندرية.</p>
    <div class="pt-2 border-t border-[#0A4F78]/10 flex items-center justify-between text-[10px] text-[#0A4F78] dark:text-[#2A8FC2] font-mono">
      <span><i class="fa-solid fa-seedling ml-1 text-[#67B34A]"></i> مستخلص نقي</span>
      <span>99%+ نقاء</span>
    </div>
  </div>
</div>
<div class="mt-4 p-4 rounded-2xl bg-[#0A4F78]/5 dark:bg-white/5 border border-[#0A4F78]/10 flex flex-wrap gap-4 items-center justify-around text-xs font-bold text-[#0A4F78] dark:text-[#2A8FC2]" dir="rtl">
  <span><i class="fa-solid fa-check-double text-[#67B34A] ml-1.5"></i> فحص HPLC ثلاثي</span>
  <span><i class="fa-solid fa-shield-virus text-[#67B34A] ml-1.5"></i> خالٍ من الملوثات</span>
  <span><i class="fa-solid fa-dna text-[#67B34A] ml-1.5"></i> نباتي غير معدل وراثياً</span>
</div>`
        },
        sec2: {
            en: `<div class="space-y-3 mb-5">
  <div class="flex items-start gap-3 p-3.5 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/10">
    <span class="w-6 h-6 rounded-full bg-[#67B34A] text-white flex items-center justify-center shrink-0 text-xs font-bold mt-0.5">
      <i class="fa-solid fa-check"></i>
    </span>
    <span class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 font-medium leading-relaxed">
      Sustained mitochondrial energy output and cellular ATP production without jitter or crash.
    </span>
  </div>
  <div class="flex items-start gap-3 p-3.5 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/10">
    <span class="w-6 h-6 rounded-full bg-[#67B34A] text-white flex items-center justify-center shrink-0 text-xs font-bold mt-0.5">
      <i class="fa-solid fa-check"></i>
    </span>
    <span class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 font-medium leading-relaxed">
      Supports cellular autophagy and youthful biomarker signaling.
    </span>
  </div>
</div>
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
  <div class="p-4 rounded-2xl bg-[#0A4F78]/5 dark:bg-[#031827] border border-[#0A4F78]/15 space-y-1.5">
    <span class="text-xs font-extrabold text-[#67B34A] font-mono uppercase block">Phase 1 · Days 1–7</span>
    <h4 class="text-xs font-bold text-[#031827] dark:text-white">Acute Cellular Perfusion</h4>
    <p class="text-[11px] text-[#031827]/70 dark:text-[#F6F5EF]/70 leading-relaxed">Immediate microcirculation and vitality enhancement.</p>
  </div>
  <div class="p-4 rounded-2xl bg-[#0A4F78]/5 dark:bg-[#031827] border border-[#0A4F78]/15 space-y-1.5">
    <span class="text-xs font-extrabold text-[#2A8FC2] font-mono uppercase block">Phase 2 · Days 14–30</span>
    <h4 class="text-xs font-bold text-[#031827] dark:text-white">Synaptic Consolidation</h4>
    <p class="text-[11px] text-[#031827]/70 dark:text-[#F6F5EF]/70 leading-relaxed">Enhanced metabolic balance and cellular resilience.</p>
  </div>
  <div class="p-4 rounded-2xl bg-[#0A4F78]/5 dark:bg-[#031827] border border-[#0A4F78]/15 space-y-1.5">
    <span class="text-xs font-extrabold text-[#0A4F78] dark:text-[#67B34A] font-mono uppercase block">Phase 3 · Days 60+</span>
    <h4 class="text-xs font-bold text-[#031827] dark:text-white">Deep Longevity Defense</h4>
    <p class="text-[11px] text-[#031827]/70 dark:text-[#F6F5EF]/70 leading-relaxed">Ongoing genomic stability and mitochondrial defense.</p>
  </div>
</div>`,
            ar: `<div class="space-y-3 mb-5" dir="rtl">
  <div class="flex items-start gap-3 p-3.5 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/10">
    <span class="w-6 h-6 rounded-full bg-[#67B34A] text-white flex items-center justify-center shrink-0 text-xs font-bold mt-0.5">
      <i class="fa-solid fa-check"></i>
    </span>
    <span class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 font-medium leading-relaxed font-tajawal">
      دعم مستدام لإنتاج الطاقة الميتوكوندرية وتوليد ATP دون أي إجهاد عصبي.
    </span>
  </div>
  <div class="flex items-start gap-3 p-3.5 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/10">
    <span class="w-6 h-6 rounded-full bg-[#67B34A] text-white flex items-center justify-center shrink-0 text-xs font-bold mt-0.5">
      <i class="fa-solid fa-check"></i>
    </span>
    <span class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 font-medium leading-relaxed font-tajawal">
      تحفيز الالتهام الذاتي الخلوي وتجديد المؤشرات الحيوية للشباب.
    </span>
  </div>
</div>
<div class="grid grid-cols-1 md:grid-cols-3 gap-4" dir="rtl">
  <div class="p-4 rounded-2xl bg-[#0A4F78]/5 dark:bg-[#031827] border border-[#0A4F78]/15 space-y-1.5">
    <span class="text-xs font-extrabold text-[#67B34A] font-tajawal uppercase block">المرحلة 1 · اليوم 1 إلى 7</span>
    <h4 class="text-xs font-bold text-[#031827] dark:text-white font-tajawal">الاستجابة الفورية</h4>
    <p class="text-[11px] text-[#031827]/70 dark:text-[#F6F5EF]/70 leading-relaxed font-tajawal">تحسين تدفق الدورة الدموية الدقيقة وتعزيز النشاط.</p>
  </div>
  <div class="p-4 rounded-2xl bg-[#0A4F78]/5 dark:bg-[#031827] border border-[#0A4F78]/15 space-y-1.5">
    <span class="text-xs font-extrabold text-[#2A8FC2] font-tajawal uppercase block">المرحلة 2 · اليوم 14 إلى 30</span>
    <h4 class="text-xs font-bold text-[#031827] dark:text-white font-tajawal">استقرار المسارات</h4>
    <p class="text-[11px] text-[#031827]/70 dark:text-[#F6F5EF]/70 leading-relaxed font-tajawal">توازن التمثيل الغذائي الخلوي ومقاومة الإجهاد.</p>
  </div>
  <div class="p-4 rounded-2xl bg-[#0A4F78]/5 dark:bg-[#031827] border border-[#0A4F78]/15 space-y-1.5">
    <span class="text-xs font-extrabold text-[#0A4F78] dark:text-[#67B34A] font-tajawal uppercase block">المرحلة 3 · اليوم 60 فما فوق</span>
    <h4 class="text-xs font-bold text-[#031827] dark:text-white font-tajawal">حماية طول العمر</h4>
    <p class="text-[11px] text-[#031827]/70 dark:text-[#F6F5EF]/70 leading-relaxed font-tajawal">حماية مستمرة لجينوم الخلية وسلامة الميتوكوندريا.</p>
  </div>
</div>`
        },
        sec3: {
            en: `<div class="p-4 sm:p-6 rounded-2xl bg-[#0A4F78]/5 dark:bg-[#031827] border-inline-start-4 border-primary space-y-2 mb-4">
  <span class="text-xs font-extrabold uppercase tracking-wider text-[#0A4F78] dark:text-[#2A8FC2] block">
    <i class="fa-solid fa-circle-info mr-1"></i> Primary Administration Directive:
  </span>
  <p class="text-xs sm:text-sm text-[#031827] dark:text-[#F6F5EF] font-medium leading-relaxed">
    Take 2 capsules each morning with 250ml of pure water, preferably alongside healthy fats such as cold-pressed extra virgin olive oil.
  </p>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
  <div class="p-4 rounded-2xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 space-y-2 text-center">
    <div class="w-10 h-10 rounded-full bg-[#67B34A]/15 text-[#67B34A] mx-auto flex items-center justify-center text-base"><i class="fa-solid fa-sun"></i></div>
    <h4 class="text-xs font-bold text-[#031827] dark:text-white">1. Timing</h4>
    <p class="text-[11px] text-[#031827]/70 dark:text-[#F6F5EF]/70 leading-relaxed">Administer each morning to align with circadian rhythm.</p>
  </div>
  <div class="p-4 rounded-2xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 space-y-2 text-center">
    <div class="w-10 h-10 rounded-full bg-[#2A8FC2]/15 text-[#2A8FC2] mx-auto flex items-center justify-center text-base"><i class="fa-solid fa-glass-water"></i></div>
    <h4 class="text-xs font-bold text-[#031827] dark:text-white">2. Hydration</h4>
    <p class="text-[11px] text-[#031827]/70 dark:text-[#F6F5EF]/70 leading-relaxed">250ml pure mineral water aids rapid enteric dissolution.</p>
  </div>
  <div class="p-4 rounded-2xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 space-y-2 text-center">
    <div class="w-10 h-10 rounded-full bg-amber-500/15 text-amber-500 mx-auto flex items-center justify-center text-base"><i class="fa-solid fa-bottle-droplet"></i></div>
    <h4 class="text-xs font-bold text-[#031827] dark:text-white">3. Lipid Synergy</h4>
    <p class="text-[11px] text-[#031827]/70 dark:text-[#F6F5EF]/70 leading-relaxed">Consuming with healthy fats boosts bio-absorption.</p>
  </div>
  <div class="p-4 rounded-2xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 space-y-2 text-center">
    <div class="w-10 h-10 rounded-full bg-purple-500/15 text-purple-500 mx-auto flex items-center justify-center text-base"><i class="fa-solid fa-repeat"></i></div>
    <h4 class="text-xs font-bold text-[#031827] dark:text-white">4. Regularity</h4>
    <p class="text-[11px] text-[#031827]/70 dark:text-[#F6F5EF]/70 leading-relaxed">Engineered for safe, uninterrupted daily longevity support.</p>
  </div>
</div>`,
            ar: `<div class="p-4 sm:p-6 rounded-2xl bg-[#0A4F78]/5 dark:bg-[#031827] border-inline-start-4 border-primary space-y-2 mb-4" dir="rtl">
  <span class="text-xs font-extrabold uppercase tracking-wider text-[#0A4F78] dark:text-[#2A8FC2] block font-tajawal">
    <i class="fa-solid fa-circle-info ml-1"></i> الإرشاد السريري المباشر:
  </span>
  <p class="text-xs sm:text-sm text-[#031827] dark:text-[#F6F5EF] font-medium leading-relaxed font-tajawal">
    تناول كبسولتين صباحاً مع 250 مل من الماء النقي، يفضل مع مصدر دهون صحية كزيت الزيتون البكر الممتاز لتعزيز الامتصاص.
  </p>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4" dir="rtl">
  <div class="p-4 rounded-2xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 space-y-2 text-center">
    <div class="w-10 h-10 rounded-full bg-[#67B34A]/15 text-[#67B34A] mx-auto flex items-center justify-center text-base"><i class="fa-solid fa-sun"></i></div>
    <h4 class="text-xs font-bold text-[#031827] dark:text-white font-tajawal">1. التوقيت</h4>
    <p class="text-[11px] text-[#031827]/70 dark:text-[#F6F5EF]/70 leading-relaxed font-tajawal">يُفضل تناوله صباحاً ليتزامن مع إيقاع الساعة البيولوجية.</p>
  </div>
  <div class="p-4 rounded-2xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 space-y-2 text-center">
    <div class="w-10 h-10 rounded-full bg-[#2A8FC2]/15 text-[#2A8FC2] mx-auto flex items-center justify-center text-base"><i class="fa-solid fa-glass-water"></i></div>
    <h4 class="text-xs font-bold text-[#031827] dark:text-white font-tajawal">2. الترطيب</h4>
    <p class="text-[11px] text-[#031827]/70 dark:text-[#F6F5EF]/70 leading-relaxed font-tajawal">250 مل ماء نقي لتسهيل ذوبان الكبسولة المعوية.</p>
  </div>
  <div class="p-4 rounded-2xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 space-y-2 text-center">
    <div class="w-10 h-10 rounded-full bg-amber-500/15 text-amber-500 mx-auto flex items-center justify-center text-base"><i class="fa-solid fa-bottle-droplet"></i></div>
    <h4 class="text-xs font-bold text-[#031827] dark:text-white font-tajawal">3. الدهون الصحية</h4>
    <p class="text-[11px] text-[#031827]/70 dark:text-[#F6F5EF]/70 leading-relaxed font-tajawal">تناوله مع زيت الزيتون يضاعف الامتصاص الحيوي.</p>
  </div>
  <div class="p-4 rounded-2xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 space-y-2 text-center">
    <div class="w-10 h-10 rounded-full bg-purple-500/15 text-purple-500 mx-auto flex items-center justify-center text-base"><i class="fa-solid fa-repeat"></i></div>
    <h4 class="text-xs font-bold text-[#031827] dark:text-white font-tajawal">4. الاستمرارية</h4>
    <p class="text-[11px] text-[#031827]/70 dark:text-[#F6F5EF]/70 leading-relaxed font-tajawal">مصمم للاستخدام اليومي المستمر دون أي اعتياد.</p>
  </div>
</div>`
        },
        sec4: {
            en: `<p class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 leading-relaxed font-medium mb-4">
  Formulated to activate cellular bio-energetics, support endogenous NAD+ synthesis, and protect mitochondrial DNA from oxidative stress.
</p>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
  <div class="p-4 rounded-2xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 space-y-2">
    <span class="text-xs font-extrabold text-[#0A4F78] dark:text-[#2A8FC2] uppercase block">
      <i class="fa-solid fa-microchip mr-1 text-[#67B34A]"></i> Blood-Brain Barrier (BBB) Kinetics:
    </span>
    <p class="text-[11px] text-[#031827]/75 dark:text-[#F6F5EF]/75 leading-relaxed">
      Phospholipid carriers enable efficient bioavailability and localized receptor binding.
    </p>
  </div>
  <div class="p-4 rounded-2xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 space-y-2">
    <span class="text-xs font-extrabold text-[#0A4F78] dark:text-[#2A8FC2] uppercase block">
      <i class="fa-solid fa-atom mr-1 text-purple-500"></i> Mitochondrial Redox Equilibrium:
    </span>
    <p class="text-[11px] text-[#031827]/75 dark:text-[#F6F5EF]/75 leading-relaxed">
      Shields respiratory electron chain complexes against premature cellular senescence.
    </p>
  </div>
</div>`,
            ar: `<p class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 leading-relaxed font-medium mb-4 font-tajawal" dir="rtl">
  مُصمم لتنشيط مسارات الطاقة الخلوية، تحفيز التخليق الحيوي لمركب NAD+، وحماية الميتوكوندريا من التلف التأكسدي.
</p>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4" dir="rtl">
  <div class="p-4 rounded-2xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 space-y-2">
    <span class="text-xs font-extrabold text-[#0A4F78] dark:text-[#2A8FC2] uppercase block font-tajawal">
      <i class="fa-solid fa-microchip ml-1 text-[#67B34A]"></i> نفاذية الحاجز الدموي الدماغي:
    </span>
    <p class="text-[11px] text-[#031827]/75 dark:text-[#F6F5EF]/75 leading-relaxed font-tajawal">
      مركبات الفسفوليبيد تدعم الامتصاص السريع والنفاذ الخلوي بكفاءة عالية.
    </p>
  </div>
  <div class="p-4 rounded-2xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 space-y-2">
    <span class="text-xs font-extrabold text-[#0A4F78] dark:text-[#2A8FC2] uppercase block font-tajawal">
      <i class="fa-solid fa-atom ml-1 text-purple-500"></i> التوازن الميتوكوندري:
    </span>
    <p class="text-[11px] text-[#031827]/75 dark:text-[#F6F5EF]/75 leading-relaxed font-tajawal">
      حماية متقدمة لسلسلة نقل الإلكترونات وحماية DNA الخلية من الشيخوخة المبكرة.
    </p>
  </div>
</div>`
        },
        sec5: {
            en: `<div class="p-4 sm:p-5 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-900 dark:text-amber-200 space-y-2 mb-4">
  <div class="flex items-center gap-2 text-xs font-bold text-amber-700 dark:text-amber-400 uppercase tracking-wider">
    <i class="fa-solid fa-triangle-exclamation"></i>
    <span>Medical Advisory & Precautions:</span>
  </div>
  <p class="text-xs leading-relaxed">
    Keep out of reach of children. Store in a cool, dry environment below 25°C away from direct sunlight. Individuals on clinical supervision or pregnant/nursing mothers should consult their physician before use.
  </p>
</div>
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center text-xs font-bold">
  <div class="p-3 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 space-y-1">
    <i class="fa-solid fa-ban text-[#67B34A] text-base"></i>
    <span class="block text-[#031827] dark:text-white">Gluten-Free</span>
  </div>
  <div class="p-3 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 space-y-1">
    <i class="fa-solid fa-leaf text-[#67B34A] text-base"></i>
    <span class="block text-[#031827] dark:text-white">Soy-Free</span>
  </div>
  <div class="p-3 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 space-y-1">
    <i class="fa-solid fa-droplet-slash text-[#67B34A] text-base"></i>
    <span class="block text-[#031827] dark:text-white">100% Vegan</span>
  </div>
  <div class="p-3 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 space-y-1">
    <i class="fa-solid fa-flask text-[#67B34A] text-base"></i>
    <span class="block text-[#031827] dark:text-white">No Preservatives</span>
  </div>
</div>`,
            ar: `<div class="p-4 sm:p-5 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-900 dark:text-amber-200 space-y-2 mb-4" dir="rtl">
  <div class="flex items-center gap-2 text-xs font-bold text-amber-700 dark:text-amber-400 uppercase tracking-wider font-tajawal">
    <i class="fa-solid fa-triangle-exclamation"></i>
    <span>تنبيه سريري وإرشادات الاستعمال:</span>
  </div>
  <p class="text-xs leading-relaxed font-tajawal">
    يُحفظ بعيداً عن متناول الأطفال في مكان جاف وبارد تحت 25 درجة مئوية بعيداً عن أشعة الشمس المباشرة. يُنصح باستشارة الطبيب المعالج لمن يتناولون أدوية موصوفة أو الحوامل والمرضعات.
  </p>
</div>
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center text-xs font-bold" dir="rtl">
  <div class="p-3 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 space-y-1">
    <i class="fa-solid fa-ban text-[#67B34A] text-base"></i>
    <span class="block text-[#031827] dark:text-white font-tajawal">خالٍ من الجلوتين</span>
  </div>
  <div class="p-3 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 space-y-1">
    <i class="fa-solid fa-leaf text-[#67B34A] text-base"></i>
    <span class="block text-[#031827] dark:text-white font-tajawal">خالٍ من الصويا</span>
  </div>
  <div class="p-3 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 space-y-1">
    <i class="fa-solid fa-droplet-slash text-[#67B34A] text-base"></i>
    <span class="block text-[#031827] dark:text-white font-tajawal">100% نباتي</span>
  </div>
  <div class="p-3 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 space-y-1">
    <i class="fa-solid fa-flask text-[#67B34A] text-base"></i>
    <span class="block text-[#031827] dark:text-white font-tajawal">دون مواد حافظة</span>
  </div>
</div>`
        },
        sec6: {
            en: `<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-xs font-medium">
  <div class="p-3.5 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 flex justify-between items-center">
    <span class="text-[#031827]/70 dark:text-[#F6F5EF]/70">Supply Volume:</span>
    <span class="font-bold text-[#031827] dark:text-white">60 Veg Capsules</span>
  </div>
  <div class="p-3.5 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 flex justify-between items-center">
    <span class="text-[#031827]/70 dark:text-[#F6F5EF]/70">Facility Standards:</span>
    <span class="font-bold text-[#67B34A]">cGMP & ISO Assayed</span>
  </div>
  <div class="p-3.5 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 flex justify-between items-center">
    <span class="text-[#031827]/70 dark:text-[#F6F5EF]/70">Satisfaction Guarantee:</span>
    <span class="font-bold text-[#67B34A]">30-Day Clinical Refund</span>
  </div>
</div>`,
            ar: `<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-xs font-medium" dir="rtl">
  <div class="p-3.5 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 flex justify-between items-center">
    <span class="text-[#031827]/70 dark:text-[#F6F5EF]/70 font-tajawal">حجم العبوة:</span>
    <span class="font-bold text-[#031827] dark:text-white font-tajawal">60 كبسولة نباتية</span>
  </div>
  <div class="p-3.5 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 flex justify-between items-center">
    <span class="text-[#031827]/70 dark:text-[#F6F5EF]/70 font-tajawal">معايير التصنيع:</span>
    <span class="font-bold text-[#67B34A]">cGMP & ISO</span>
  </div>
  <div class="p-3.5 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 flex justify-between items-center">
    <span class="text-[#031827]/70 dark:text-[#F6F5EF]/70 font-tajawal">ضمان الرضا الطبي:</span>
    <span class="font-bold text-[#67B34A] font-tajawal">استرداد كامل خلال 30 يوماً</span>
  </div>
</div>`
        }
    };

    function toggleSectionContentStudio(checkbox, secKey) {
        if (!checkbox) return;
        const isChecked = checkbox.checked;
        const pill = document.getElementById('pill-custom-master-' + secKey);
        const drawer = document.getElementById('studio-drawer-' + secKey);
        const badge = document.getElementById('badge-custom-status-' + secKey);
        const isAr = {{ $isAr ? 'true' : 'false' }};

        if (isChecked) {
            pill.classList.remove('is-inactive');
            pill.classList.add('is-active');
            pill.style.backgroundColor = 'rgba(245, 158, 11, 0.15)';
            pill.style.borderColor = 'rgba(245, 158, 11, 0.6)';
            pill.style.color = '#f59e0b';
            pill.querySelector('.bz-switch-thumb').style.backgroundColor = '#f59e0b';
            pill.querySelector('.bz-switch-thumb').style.color = '#fff';
            pill.querySelector('.bz-switch-thumb i').className = 'fa-solid fa-wand-magic-sparkles';
            if (drawer) drawer.classList.remove('hidden');
            if (badge) {
                badge.className = 'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-amber-500 text-white font-mono text-[10px] font-black uppercase tracking-wider transition-all';
                badge.innerHTML = '<i class="fa-solid fa-wand-magic-sparkles"></i> <span>' + (isAr ? 'عناصر مخصصة مفعلة' : 'BESPOKE ELEMENTS ACTIVE') + '</span>';
            }
        } else {
            pill.classList.remove('is-active');
            pill.classList.add('is-inactive');
            pill.style.backgroundColor = '';
            pill.style.borderColor = '';
            pill.style.color = '';
            pill.querySelector('.bz-switch-thumb').style.backgroundColor = '';
            pill.querySelector('.bz-switch-thumb').style.color = '';
            pill.querySelector('.bz-switch-thumb i').className = 'fa-solid fa-xmark';
            if (drawer) drawer.classList.add('hidden');
            if (badge) {
                badge.className = 'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-mono text-[10px] font-black uppercase tracking-wider transition-all';
                badge.innerHTML = '<i class="fa-solid fa-cubes"></i> <span>' + (isAr ? 'القالب التلقائي الافتراضي' : 'STANDARD SYSTEM TEMPLATE') + '</span>';
            }
        }

        syncSimulatorCard(secKey);
    }

    function setSectionScreenMode(secKey, mode) {
        const lblUnified = document.getElementById('lbl-screen-unified-' + secKey);
        const lblPerScreen = document.getElementById('lbl-screen-perscreen-' + secKey);
        const wrapMobileEn = document.getElementById('wrapper-mobile-en-' + secKey);
        const wrapMobileAr = document.getElementById('wrapper-mobile-ar-' + secKey);

        if (mode === 'unified') {
            if (lblUnified) lblUnified.className = 'px-3 py-1 rounded-lg text-xs font-extrabold cursor-pointer transition-all flex items-center gap-1.5 bg-[#0A4F78] text-white shadow-xs';
            if (lblPerScreen) lblPerScreen.className = 'px-3 py-1 rounded-lg text-xs font-extrabold cursor-pointer transition-all flex items-center gap-1.5 text-slate-700 dark:text-slate-300 hover:text-slate-900';
            if (wrapMobileEn) wrapMobileEn.classList.add('hidden');
            if (wrapMobileAr) wrapMobileAr.classList.add('hidden');
        } else {
            if (lblPerScreen) lblPerScreen.className = 'px-3 py-1 rounded-lg text-xs font-extrabold cursor-pointer transition-all flex items-center gap-1.5 bg-[#0A4F78] text-white shadow-xs';
            if (lblUnified) lblUnified.className = 'px-3 py-1 rounded-lg text-xs font-extrabold cursor-pointer transition-all flex items-center gap-1.5 text-slate-700 dark:text-slate-300 hover:text-slate-900';
            if (wrapMobileEn) wrapMobileEn.classList.remove('hidden');
            if (wrapMobileAr) wrapMobileAr.classList.remove('hidden');
        }
    }

    let activeStudioLangs = {};
    function setStudioLangTab(secKey, lang) {
        activeStudioLangs[secKey] = lang;
        const btnEn = document.getElementById('tab-btn-en-' + secKey);
        const btnAr = document.getElementById('tab-btn-ar-' + secKey);
        const paneEn = document.getElementById('pane-lang-en-' + secKey);
        const paneAr = document.getElementById('pane-lang-ar-' + secKey);

        if (lang === 'en') {
            if (btnEn) btnEn.className = 'px-4 py-2 rounded-xl text-xs font-black transition-all flex items-center gap-2 cursor-pointer bg-[#0A4F78] text-white shadow-xs';
            if (btnAr) btnAr.className = 'px-4 py-2 rounded-xl text-xs font-black transition-all flex items-center gap-2 cursor-pointer bg-slate-100 dark:bg-[#062B49] text-slate-700 dark:text-slate-300 hover:text-[#67B34A]';
            if (paneEn) paneEn.classList.remove('hidden');
            if (paneAr) paneAr.classList.add('hidden');
        } else {
            if (btnAr) btnAr.className = 'px-4 py-2 rounded-xl text-xs font-black transition-all flex items-center gap-2 cursor-pointer bg-[#67B34A] text-white shadow-xs';
            if (btnEn) btnEn.className = 'px-4 py-2 rounded-xl text-xs font-black transition-all flex items-center gap-2 cursor-pointer bg-slate-100 dark:bg-[#062B49] text-slate-700 dark:text-slate-300 hover:text-[#0A4F78]';
            if (paneAr) paneAr.classList.remove('hidden');
            if (paneEn) paneEn.classList.add('hidden');
        }

        renderInCardPreviewContent(secKey);
    }

    function loadSectionBlueprint(secKey) {
        const bp = sectionBlueprints[secKey];
        if (!bp) return;

        const editorEn = document.getElementById('editor-en-' + secKey);
        const editorAr = document.getElementById('editor-ar-' + secKey);

        if (editorEn) editorEn.value = bp.en.trim();
        if (editorAr) editorAr.value = bp.ar.trim();

        // Make sure studio switch is ON
        const masterInput = document.getElementById('input-custom-master-' + secKey);
        if (masterInput && !masterInput.checked) {
            masterInput.checked = true;
            toggleSectionContentStudio(masterInput, secKey);
        }

        renderInCardPreviewContent(secKey);
        syncCustomContentToSimulator(secKey, 'en');
        syncCustomContentToSimulator(secKey, 'ar');
    }

    function insertStudioSnippet(secKey, type) {
        const activeLang = activeStudioLangs[secKey] || 'en';
        const editor = activeLang === 'ar' 
            ? document.getElementById('editor-ar-' + secKey) 
            : document.getElementById('editor-en-' + secKey);
        if (!editor) return;

        let snippet = '';
        if (type === 'card') {
            snippet = activeLang === 'ar'
                ? `\n<div class="p-4 rounded-2xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 space-y-2" dir="rtl">\n  <h4 class="text-xs font-bold text-[#031827] dark:text-white font-tajawal">عنوان البطاقة</h4>\n  <p class="text-[11px] text-[#031827]/70 dark:text-[#F6F5EF]/70 leading-relaxed font-tajawal">وصف الميزة أو العنصر هنا...</p>\n</div>\n`
                : `\n<div class="p-4 rounded-2xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 space-y-2">\n  <h4 class="text-xs font-bold text-[#031827] dark:text-white">Feature Card Heading</h4>\n  <p class="text-[11px] text-[#031827]/70 dark:text-[#F6F5EF]/70 leading-relaxed">Detailed clinical benefit or parameter description...</p>\n</div>\n`;
        } else if (type === 'pill') {
            snippet = ` <span class="px-2.5 py-0.5 rounded-full bg-[#67B34A]/15 text-[#67B34A] text-[10px] font-mono font-bold">Standardized Purity</span> `;
        } else if (type === 'callout') {
            snippet = activeLang === 'ar'
                ? `\n<div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-900 dark:text-amber-200 space-y-1" dir="rtl">\n  <span class="text-xs font-bold block font-tajawal"><i class="fa-solid fa-triangle-exclamation ml-1"></i> تنبيه هام:</span>\n  <p class="text-[11px] leading-relaxed font-tajawal">نص التحذير أو التوجيه هنا...</p>\n</div>\n`
                : `\n<div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-900 dark:text-amber-200 space-y-1">\n  <span class="text-xs font-bold block"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Important Advisory:</span>\n  <p class="text-[11px] leading-relaxed">Enter cautionary directives or safety advisory text here...</p>\n</div>\n`;
        } else if (type === 'grid') {
            snippet = `\n<div class="grid grid-cols-1 md:grid-cols-2 gap-4">\n  <!-- Item 1 -->\n  <div class="p-3.5 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15">Column 1</div>\n  <!-- Item 2 -->\n  <div class="p-3.5 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15">Column 2</div>\n</div>\n`;
        }

        const start = editor.selectionStart || editor.value.length;
        const end = editor.selectionEnd || editor.value.length;
        editor.value = editor.value.substring(0, start) + snippet + editor.value.substring(end);
        editor.focus();

        renderInCardPreviewContent(secKey);
        syncCustomContentToSimulator(secKey, activeLang);
    }

    function toggleInCardPreview(secKey) {
        const box = document.getElementById('incard-preview-' + secKey);
        const btnText = document.getElementById('btn-text-preview-' + secKey);
        const isAr = {{ $isAr ? 'true' : 'false' }};
        if (!box) return;

        if (box.classList.contains('hidden')) {
            box.classList.remove('hidden');
            renderInCardPreviewContent(secKey);
            if (btnText) btnText.textContent = isAr ? 'إخفاء المعاينة' : 'Hide Preview';
        } else {
            box.classList.add('hidden');
            if (btnText) btnText.textContent = isAr ? 'معاينة العناصر' : 'Live Preview';
        }
    }

    function renderInCardPreviewContent(secKey) {
        const box = document.getElementById('incard-preview-' + secKey);
        if (!box || box.classList.contains('hidden')) return;

        const body = document.getElementById('incard-preview-body-' + secKey);
        if (!body) return;

        const activeLang = activeStudioLangs[secKey] || 'en';
        const editor = activeLang === 'ar'
            ? document.getElementById('editor-ar-' + secKey)
            : document.getElementById('editor-en-' + secKey);

        const content = editor?.value?.trim() || '';
        if (content) {
            body.innerHTML = content;
        } else {
            body.innerHTML = '<span class="text-slate-400 italic font-mono text-[11px]">' + (activeLang === 'ar' ? 'المحرر فارغ، اكتب محتوى أو اضغط "تحميل قالب القسم"' : 'Editor is empty. Type content or click "Load Blueprint".') + '</span>';
        }
    }

    function syncCustomContentToSimulator(secKey, lang) {
        const simBox = document.getElementById('sim-content-' + secKey);
        if (!simBox) return;

        const editor = lang === 'ar' 
            ? document.getElementById('editor-ar-' + secKey) 
            : document.getElementById('editor-en-' + secKey);

        const val = editor?.value?.trim() || '';
        const isAr = {{ $isAr ? 'true' : 'false' }};

        if (val) {
            // Strip tags for clean snippet preview
            const tmp = document.createElement('div');
            tmp.innerHTML = val;
            const cleanText = tmp.textContent || tmp.innerText || '';

            simBox.innerHTML = `
                <div class="p-2 rounded-lg bg-amber-500/10 border border-amber-500/25 space-y-1">
                    <span class="font-bold text-amber-600 dark:text-amber-400 block text-[9px] uppercase tracking-wider">
                        <i class="fa-solid fa-wand-magic-sparkles mr-1 ml-1"></i> ${isAr ? 'عناصر مخصصة نشطة' : 'Bespoke Elements Active'}
                    </span>
                    <div class="line-clamp-2 text-[9px] font-mono text-slate-500 dark:text-slate-400">
                        ${cleanText.substring(0, 100)}...
                    </div>
                </div>
            `;
        }
    }
        document.querySelectorAll('.sim-section-card').forEach(simCard => {
            const secKey = simCard.id.replace('sim-sec-', '');
            syncSimulatorCard(secKey);
        });
        recalculateBundleHUD();
    }

    // Initialize HUD on load
    document.addEventListener('DOMContentLoaded', () => {
        recalculateBundleHUD();
        triggerStickyPreviewSync();
    });
</script>
