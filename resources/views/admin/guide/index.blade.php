@php
    $isRtl = app()->getLocale() === 'ar' || session('locale', 'en') === 'ar';

    $accentMap = [
        'emerald' => [
            'badge' => 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border-emerald-500/25',
            'num' => 'from-emerald-500 to-teal-600 shadow-emerald-500/25',
            'tab_active' => 'bg-emerald-600 text-white border-emerald-600 shadow-md shadow-emerald-600/20',
            'ring' => 'ring-emerald-500/40',
            'dot' => 'bg-emerald-500',
        ],
        'sky' => [
            'badge' => 'bg-sky-500/10 text-sky-700 dark:text-sky-300 border-sky-500/25',
            'num' => 'from-sky-500 to-blue-600 shadow-sky-500/25',
            'tab_active' => 'bg-sky-600 text-white border-sky-600 shadow-md shadow-sky-600/20',
            'ring' => 'ring-sky-500/40',
            'dot' => 'bg-sky-500',
        ],
        'indigo' => [
            'badge' => 'bg-indigo-500/10 text-indigo-700 dark:text-indigo-300 border-indigo-500/25',
            'num' => 'from-indigo-500 to-violet-600 shadow-indigo-500/25',
            'tab_active' => 'bg-indigo-600 text-white border-indigo-600 shadow-md shadow-indigo-600/20',
            'ring' => 'ring-indigo-500/40',
            'dot' => 'bg-indigo-500',
        ],
        'teal' => [
            'badge' => 'bg-teal-500/10 text-teal-700 dark:text-teal-300 border-teal-500/25',
            'num' => 'from-teal-500 to-cyan-600 shadow-teal-500/25',
            'tab_active' => 'bg-teal-600 text-white border-teal-600 shadow-md shadow-teal-600/20',
            'ring' => 'ring-teal-500/40',
            'dot' => 'bg-teal-500',
        ],
        'amber' => [
            'badge' => 'bg-amber-500/10 text-amber-700 dark:text-amber-300 border-amber-500/25',
            'num' => 'from-amber-500 to-orange-600 shadow-amber-500/25',
            'tab_active' => 'bg-amber-600 text-white border-amber-600 shadow-md shadow-amber-600/20',
            'ring' => 'ring-amber-500/40',
            'dot' => 'bg-amber-500',
        ],
        'violet' => [
            'badge' => 'bg-violet-500/10 text-violet-700 dark:text-violet-300 border-violet-500/25',
            'num' => 'from-violet-500 to-purple-600 shadow-violet-500/25',
            'tab_active' => 'bg-violet-600 text-white border-violet-600 shadow-md shadow-violet-600/20',
            'ring' => 'ring-violet-500/40',
            'dot' => 'bg-violet-500',
        ],
        'blue' => [
            'badge' => 'bg-blue-500/10 text-blue-700 dark:text-blue-300 border-blue-500/25',
            'num' => 'from-blue-500 to-indigo-600 shadow-blue-500/25',
            'tab_active' => 'bg-blue-600 text-white border-blue-600 shadow-md shadow-blue-600/20',
            'ring' => 'ring-blue-500/40',
            'dot' => 'bg-blue-500',
        ],
        'rose' => [
            'badge' => 'bg-rose-500/10 text-rose-700 dark:text-rose-300 border-rose-500/25',
            'num' => 'from-rose-500 to-pink-600 shadow-rose-500/25',
            'tab_active' => 'bg-rose-600 text-white border-rose-600 shadow-md shadow-rose-600/20',
            'ring' => 'ring-rose-500/40',
            'dot' => 'bg-rose-500',
        ],
        'orange' => [
            'badge' => 'bg-orange-500/10 text-orange-700 dark:text-orange-300 border-orange-500/25',
            'num' => 'from-orange-500 to-red-600 shadow-orange-500/25',
            'tab_active' => 'bg-orange-600 text-white border-orange-600 shadow-md shadow-orange-600/20',
            'ring' => 'ring-orange-500/40',
            'dot' => 'bg-orange-500',
        ],
        'cyan' => [
            'badge' => 'bg-cyan-500/10 text-cyan-700 dark:text-cyan-300 border-cyan-500/25',
            'num' => 'from-cyan-500 to-sky-600 shadow-cyan-500/25',
            'tab_active' => 'bg-cyan-600 text-white border-cyan-600 shadow-md shadow-cyan-600/20',
            'ring' => 'ring-cyan-500/40',
            'dot' => 'bg-cyan-500',
        ],
        'slate' => [
            'badge' => 'bg-slate-500/10 text-slate-700 dark:text-slate-300 border-slate-500/25',
            'num' => 'from-slate-600 to-slate-800 shadow-slate-500/25',
            'tab_active' => 'bg-slate-700 text-white border-slate-700 shadow-md shadow-slate-700/20',
            'ring' => 'ring-slate-500/40',
            'dot' => 'bg-slate-500',
        ],
    ];

    // Build strictly section-scoped galleries JSON for Lightbox & Horizontal Strips
    $sectionGalleriesPayload = [
        'overview' => [
            'label' => '00 • ARCHITECTURE',
            'title_ar' => $overviewArchitecture['title_ar'],
            'slides' => [
                [
                    'kind' => 'diagram',
                    'step_badge' => $overviewArchitecture['badge'],
                    'title_ar' => $overviewArchitecture['title_ar'],
                    'caption_ar' => $overviewArchitecture['subtitle_ar'],
                    'light_src' => asset($overviewArchitecture['light_img']),
                    'dark_src' => asset($overviewArchitecture['dark_img']),
                    'html_url' => asset($overviewArchitecture['html_url']),
                ],
            ],
        ],
    ];

    foreach ($guideModules as $mod) {
        $slides = [];
        foreach ($mod['gallery'] as $slide) {
            $slides[] = [
                'kind' => $slide['kind'],
                'step_badge' => $slide['step_badge'],
                'title_ar' => $slide['title_ar'],
                'caption_ar' => $slide['caption_ar'],
                'light_src' => asset($slide['light_src']),
                'dark_src' => !empty($slide['dark_src']) ? asset($slide['dark_src']) : null,
                'html_url' => !empty($slide['html_url']) ? asset($slide['html_url']) : null,
            ];
        }
        $sectionGalleriesPayload[$mod['key']] = [
            'label' => $mod['order'] . ' • ' . strtoupper($mod['key']),
            'title_ar' => $mod['title_ar'],
            'slides' => $slides,
        ];
    }
@endphp

<x-layouts.admin
    :pageTitle="$isRtl ? 'مركز الدعم ودليل دورات عمل النظام الشامل' : 'Comprehensive System Support & Logic Cycles Guide'"
    :pageSubtitle="$isRtl ? 'المرجع المعماري التفاعلي (Archify + لقطات حية منظمة حسب كل قسم بدون أي فجوات)' : 'Section-Scoped Interactive Architecture (Archify) & Full Logic Cycles for Bluezone OS'"
    :breadcrumbs="[
        ($isRtl ? 'لوحة التحكم' : 'Dashboard') => route('admin.dashboard'),
        ($isRtl ? 'الدعم والدليل الشامل' : 'Support & Guide') => route('admin.guide.index'),
    ]"
>
    <style>
        .bz-guide-shell {
            font-family: 'Cairo', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            color: #0f172a;
        }
        .dark .bz-guide-shell {
            color: #f1f5f9;
        }

        .bz-hero-card {
            background: linear-gradient(135deg, #ffffff 0%, #f0f9ff 52%, #e0f2fe 100%);
            border: 1px solid #bae6fd;
            box-shadow: 0 12px 32px -12px rgba(2, 132, 199, 0.16);
        }
        .dark .bz-hero-card {
            background: radial-gradient(circle at 12% 18%, #0f3a5c 0%, #081e30 58%, #04111c 100%);
            border-color: rgba(56, 189, 248, 0.32);
            box-shadow: 0 24px 50px -16px rgba(0, 0, 0, 0.72);
        }

        .bz-surface-card {
            background: #ffffff;
            border: 1px solid #dbe4ef;
            box-shadow: 0 4px 18px -6px rgba(15, 23, 42, 0.08);
        }
        .dark .bz-surface-card {
            background: #071b2d;
            border-color: #163b5c;
            box-shadow: 0 14px 32px -12px rgba(0, 0, 0, 0.65);
        }

        /* Toolbar: sits safely below 70px desktop header without overlap */
        .bz-g-toolbar {
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid #cbd5e1;
            box-shadow: 0 8px 24px -10px rgba(15, 23, 42, 0.12);
        }
        .dark .bz-g-toolbar {
            background: rgba(7, 27, 45, 0.96);
            border-color: #1b466c;
            box-shadow: 0 14px 30px -10px rgba(0, 0, 0, 0.7);
        }
        @media (min-width: 1024px) {
            .bz-g-toolbar {
                position: sticky;
                top: 76px;
                z-index: 30;
            }
        }

        /* Explicit Dual-Theme Action Buttons (never washed out by resets) */
        .bz-g-btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.65rem 1.15rem;
            border-radius: 0.75rem;
            font-size: 0.8125rem;
            font-weight: 800;
            line-height: 1.25;
            background: linear-gradient(135deg, #0369a1 0%, #0284c7 100%) !important;
            color: #ffffff !important;
            border: 1px solid #0369a1 !important;
            box-shadow: 0 8px 20px -6px rgba(2, 132, 199, 0.5);
            transition: transform 0.18s ease, box-shadow 0.2s ease, filter 0.2s ease;
        }
        .bz-g-btn-primary:hover {
            transform: translateY(-1px);
            filter: brightness(1.08);
            box-shadow: 0 12px 24px -6px rgba(2, 132, 199, 0.6);
        }
        .bz-g-btn-primary svg { color: #ffffff !important; stroke: #ffffff !important; }

        .bz-g-btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            padding: 0.65rem 1.05rem;
            border-radius: 0.75rem;
            font-size: 0.8125rem;
            font-weight: 800;
            line-height: 1.25;
            background: #ffffff !important;
            color: #0f172a !important;
            border: 1px solid #cbd5e1 !important;
            box-shadow: 0 2px 8px -2px rgba(15, 23, 42, 0.08);
            transition: transform 0.18s ease, border-color 0.2s ease, background-color 0.2s ease;
        }
        .bz-g-btn-secondary:hover {
            border-color: #0284c7 !important;
            color: #0369a1 !important;
            background: #f0f9ff !important;
        }
        .dark .bz-g-btn-secondary {
            background: #0d2b45 !important;
            color: #f1f5f9 !important;
            border-color: #21517a !important;
        }
        .dark .bz-g-btn-secondary:hover {
            background: #123859 !important;
            border-color: #38bdf8 !important;
            color: #38bdf8 !important;
        }

        .bz-g-btn-archify {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            padding: 0.48rem 0.9rem;
            border-radius: 0.75rem;
            font-size: 0.75rem;
            font-weight: 800;
            background: #062b49 !important;
            color: #ffffff !important;
            border: 1px solid #0a4f78 !important;
            box-shadow: 0 4px 12px -4px rgba(3, 24, 39, 0.35);
            transition: transform 0.18s ease, background-color 0.2s ease;
        }
        .bz-g-btn-archify:hover {
            background: #0a4f78 !important;
            transform: translateY(-1px);
        }
        .bz-g-btn-archify svg { stroke: #ffffff !important; }
        .dark .bz-g-btn-archify {
            background: #38bdf8 !important;
            color: #031827 !important;
            border-color: #7dd3fc !important;
        }
        .dark .bz-g-btn-archify:hover {
            background: #7dd3fc !important;
        }
        .dark .bz-g-btn-archify svg { stroke: #031827 !important; }

        /* Module Number Badges (Explicit Vivid Dual-Theme Gradients) */
        .bz-g-num-badge {
            width: 3rem;
            height: 3rem;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 1.05rem;
            font-weight: 900;
            color: #ffffff !important;
            box-shadow: 0 8px 18px -6px rgba(15, 23, 42, 0.35);
        }
        .bz-g-num-badge[data-accent="emerald"] { background: linear-gradient(135deg, #059669 0%, #0d9488 100%); }
        .bz-g-num-badge[data-accent="sky"]     { background: linear-gradient(135deg, #0284c7 0%, #2563eb 100%); }
        .bz-g-num-badge[data-accent="indigo"]  { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); }
        .bz-g-num-badge[data-accent="teal"]    { background: linear-gradient(135deg, #0d9488 0%, #0891b2 100%); }
        .bz-g-num-badge[data-accent="amber"]   { background: linear-gradient(135deg, #d97706 0%, #ea580c 100%); }
        .bz-g-num-badge[data-accent="violet"]  { background: linear-gradient(135deg, #7c3aed 0%, #9333ea 100%); }
        .bz-g-num-badge[data-accent="blue"]    { background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%); }
        .bz-g-num-badge[data-accent="rose"]    { background: linear-gradient(135deg, #e11d48 0%, #db2777 100%); }
        .bz-g-num-badge[data-accent="orange"]  { background: linear-gradient(135deg, #ea580c 0%, #dc2626 100%); }
        .bz-g-num-badge[data-accent="cyan"]    { background: linear-gradient(135deg, #0891b2 0%, #0284c7 100%); }
        .bz-g-num-badge[data-accent="slate"]   { background: linear-gradient(135deg, #334155 0%, #0f172a 100%); }

        .bz-g-chevron-btn {
            width: 2.35rem;
            height: 2.35rem;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f1f5f9 !important;
            color: #1e293b !important;
            border: 1px solid #cbd5e1 !important;
            transition: background-color 0.2s ease, border-color 0.2s ease;
        }
        .bz-g-chevron-btn:hover {
            background: #e0f2fe !important;
            border-color: #0284c7 !important;
            color: #0369a1 !important;
        }
        .dark .bz-g-chevron-btn {
            background: #0d2b45 !important;
            color: #f1f5f9 !important;
            border-color: #21517a !important;
        }
        .dark .bz-g-chevron-btn:hover {
            background: #16446b !important;
            border-color: #38bdf8 !important;
            color: #38bdf8 !important;
        }

        /* Sub-screen Tabs */
        .bz-g-subtab {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.55rem 0.9rem;
            border-radius: 0.75rem;
            font-size: 0.75rem;
            font-weight: 800;
            white-space: nowrap;
            cursor: pointer;
            background: #ffffff !important;
            color: #1e293b !important;
            border: 1px solid #cbd5e1 !important;
            transition: all 0.18s ease;
        }
        .bz-g-subtab:hover {
            border-color: #0284c7 !important;
            color: #0369a1 !important;
            background: #f0f9ff !important;
        }
        .bz-g-subtab.is-active {
            background: linear-gradient(135deg, #0a4f78 0%, #0284c7 100%) !important;
            color: #ffffff !important;
            border-color: transparent !important;
            box-shadow: 0 6px 16px -4px rgba(2, 132, 199, 0.45);
        }
        .dark .bz-g-subtab {
            background: #0a243b !important;
            color: #e2e8f0 !important;
            border-color: #1d4a70 !important;
        }
        .dark .bz-g-subtab:hover {
            border-color: #38bdf8 !important;
            color: #38bdf8 !important;
            background: #103555 !important;
        }
        .dark .bz-g-subtab.is-active {
            background: linear-gradient(135deg, #38bdf8 0%, #0ea5e9 100%) !important;
            color: #031827 !important;
            border-color: transparent !important;
        }

        /* Top Category Filter Pills */
        .bz-filter-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.5rem 0.9rem;
            border-radius: 9999px;
            font-size: 0.78rem;
            font-weight: 800;
            white-space: nowrap;
            cursor: pointer;
            border: 1px solid #cbd5e1 !important;
            background: #f8fafc !important;
            color: #1e293b !important;
            transition: transform 0.18s ease, background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease, box-shadow 0.2s ease;
        }
        .bz-filter-pill:hover {
            transform: translateY(-1px);
            border-color: #0284c7 !important;
            color: #0369a1 !important;
            background: #f0f9ff !important;
        }
        .bz-filter-pill.is-active {
            background: linear-gradient(135deg, #0369a1 0%, #0ea5e9 100%) !important;
            border-color: transparent !important;
            color: #ffffff !important;
            box-shadow: 0 6px 16px -4px rgba(2, 132, 199, 0.45);
        }
        .dark .bz-filter-pill {
            background: #0b2236 !important;
            border-color: #1e4b73 !important;
            color: #e2e8f0 !important;
        }
        .dark .bz-filter-pill:hover {
            border-color: #38bdf8 !important;
            color: #38bdf8 !important;
        }
        .dark .bz-filter-pill.is-active {
            background: linear-gradient(135deg, #0ea5e9 0%, #38bdf8 100%) !important;
            color: #031827 !important;
        }

        /* Section Carousel Arrow Buttons */
        .bz-g-arrow-btn {
            width: 2.15rem;
            height: 2.15rem;
            border-radius: 0.65rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            font-weight: 900;
            background: #ffffff !important;
            color: #0f172a !important;
            border: 1px solid #cbd5e1 !important;
            transition: all 0.18s ease;
        }
        .bz-g-arrow-btn:hover {
            background: #0284c7 !important;
            color: #ffffff !important;
            border-color: #0284c7 !important;
        }
        .dark .bz-g-arrow-btn {
            background: #0d2b45 !important;
            color: #f1f5f9 !important;
            border-color: #21517a !important;
        }
        .dark .bz-g-arrow-btn:hover {
            background: #38bdf8 !important;
            color: #031827 !important;
            border-color: #38bdf8 !important;
        }

        /* Accordion Smooth Transition */
        .bz-accordion-panel {
            display: grid;
            grid-template-rows: 0fr;
            transition: grid-template-rows 0.34s cubic-bezier(0.22, 1, 0.36, 1);
        }
        .bz-accordion-card.is-open .bz-accordion-panel {
            grid-template-rows: 1fr;
        }
        .bz-accordion-inner {
            overflow: hidden;
            min-height: 0;
        }
        .bz-accordion-chevron {
            transition: transform 0.28s cubic-bezier(0.22, 1, 0.36, 1);
        }
        .bz-accordion-card.is-open .bz-accordion-chevron {
            transform: rotate(180deg);
        }

        /* Section Horizontal Media Strip */
        .bz-media-strip {
            scrollbar-width: thin;
            scrollbar-color: rgba(14, 165, 233, 0.35) transparent;
        }
        .bz-media-strip::-webkit-scrollbar { height: 6px; }
        .bz-media-strip::-webkit-scrollbar-thumb {
            background: rgba(14, 165, 233, 0.35);
            border-radius: 999px;
        }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* Animated Image Reveal & Stage Zoom */
        @keyframes bzFadeScaleIn {
            from { opacity: 0; transform: translateY(6px) scale(0.985); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        .bz-stage-animate {
            animation: bzFadeScaleIn 0.28s cubic-bezier(0.22, 1, 0.36, 1);
        }

        .bz-strip-card.is-highlighted {
            border-color: #0284c7 !important;
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.25);
        }
        .dark .bz-strip-card.is-highlighted {
            border-color: #38bdf8 !important;
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.28);
        }

        /* Lightbox & Archify Modal Top-Most Stacking */
        #guideLightbox,
        #archifyModal {
            z-index: 110000;
        }
        #guideLightbox.is-open,
        #archifyModal.is-open {
            display: flex;
            animation: bzFadeScaleIn 0.22s ease-out;
        }
    </style>

    <div class="bz-guide-shell w-full max-w-[1600px] mx-auto space-y-5 sm:space-y-6" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">

        {{-- ======================================================================= --}}
        {{-- 1. EXECUTIVE HERO BANNER + LIVE METRICS + ARCHIFY OVERVIEW LAUNCH --}}
        {{-- ======================================================================= --}}
        <section class="bz-hero-card rounded-2xl sm:rounded-3xl p-4 sm:p-6 lg:p-8 relative overflow-hidden">
            <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-center">
                <div class="xl:col-span-7 space-y-4">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-extrabold tracking-wide bg-sky-500/15 text-sky-700 dark:text-sky-300 border border-sky-500/30">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            BLUEZONE OS • GUIDE &amp; ARCHIFY WORKFLOW HUB
                        </span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-slate-900/5 dark:bg-white/10 text-slate-700 dark:text-slate-200">
                            Singleton + 17 Model Observers
                        </span>
                    </div>

                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-black text-slate-900 dark:text-white leading-snug">
                        {{ $isRtl ? 'الدليل الشامل ودورات العمل التفصيلية لمنظومة Bluezone OS' : 'Bluezone OS Interactive Architecture & Section-Scoped Guide' }}
                    </h1>

                    <p class="text-sm sm:text-base text-slate-600 dark:text-slate-300 leading-relaxed max-w-3xl">
                        {{ $isRtl
                            ? 'توثيق معماري ومرئي كامل لجميع أقسام النظام (11 قطاعاً رئيسياً + كافة الشاشات الفرعية + 12 مخطط Archify تفاعلي ثنائي الوضع Light/Dark). الصور والتنقل يميناً ويساراً مضبوطان حصرياً داخل كل قسم لضمان عدم تداخل الشاشات.'
                            : 'Complete technical & visual documentation across all 11 modules with Archify diagrams synced live to Dark & Light themes and strict section-isolated image scrolling.' }}
                    </p>

                    {{-- Architectural Pillars --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1">
                        @foreach($overviewArchitecture['pillars'] as $pillar)
                            <div class="rounded-xl p-3 bg-white/80 dark:bg-slate-900/55 border border-slate-200/80 dark:border-slate-700/70">
                                <div class="text-xs font-extrabold text-sky-700 dark:text-sky-300 mb-1">{{ $pillar['title_ar'] }}</div>
                                <div class="text-[11px] leading-relaxed text-slate-600 dark:text-slate-300">{{ $pillar['detail_ar'] }}</div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Quick Anchor Action Row --}}
                    <div class="flex flex-wrap items-center gap-2.5 pt-1">
                        <button type="button"
                            onclick="openArchifyModal('{{ asset($overviewArchitecture['html_url']) }}', @js($overviewArchitecture['title_ar']))"
                            class="bz-g-btn-primary">
                            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            <span>فتح خريطة النظام التفاعلية (Archify)</span>
                        </button>
                        <a href="#logic-cycles" onclick="scrollToGuideAnchor('logic-cycles'); return false;"
                           class="bz-g-btn-secondary">
                            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                            <span>تصفح دورات الأقسام (11 قسم)</span>
                        </a>
                        <a href="#system-catalog" onclick="scrollToGuideAnchor('system-catalog'); return false;"
                           class="bz-g-btn-secondary">
                            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            <span>فهرس العناصر والمسارات</span>
                        </a>
                    </div>
                </div>

                {{-- Right Column: Live Enterprise Archify Diagram Card (Light/Dark Synced) --}}
                <div class="xl:col-span-5">
                    <div id="archify-enterprise-map"
                         class="group relative rounded-2xl overflow-hidden border border-sky-200 dark:border-sky-800/70 bg-white dark:bg-slate-950 shadow-xl cursor-pointer transition-all hover:shadow-2xl"
                         onclick="openSectionLightbox('overview', 0)">
                        <div class="flex items-center justify-between px-3.5 py-2.5 bg-slate-50 dark:bg-slate-900/90 border-b border-slate-200 dark:border-slate-800 text-xs">
                            <div class="flex items-center gap-2 font-bold text-slate-700 dark:text-slate-200 truncate">
                                <span class="w-2.5 h-2.5 rounded-full bg-sky-500"></span>
                                <span class="truncate">{{ $overviewArchitecture['title_en'] }}</span>
                            </div>
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-sky-500/15 text-sky-700 dark:text-sky-300">
                                CLICK TO ZOOM
                            </span>
                        </div>
                        <div class="relative aspect-[16/10] bg-slate-100 dark:bg-slate-950 overflow-hidden">
                            <img
                                src="{{ asset($overviewArchitecture['light_img']) }}"
                                data-light-src="{{ asset($overviewArchitecture['light_img']) }}"
                                data-dark-src="{{ asset($overviewArchitecture['dark_img']) }}"
                                alt="{{ $overviewArchitecture['title_en'] }}"
                                class="w-full h-full object-contain transition-transform duration-500 group-hover:scale-[1.02]"
                                loading="eager"
                            />
                        </div>
                        <div class="grid grid-cols-4 divide-x divide-x-reverse divide-slate-200 dark:divide-slate-800 bg-slate-50/95 dark:bg-slate-900/90 border-t border-slate-200 dark:border-slate-800 text-center py-2">
                            <div class="px-1">
                                <div class="text-sm font-black text-slate-900 dark:text-white">{{ $systemMetrics['products_count'] ?? 0 }}</div>
                                <div class="text-[10px] font-bold text-slate-500 dark:text-slate-400">المنتجات</div>
                            </div>
                            <div class="px-1">
                                <div class="text-sm font-black text-emerald-600 dark:text-emerald-400">{{ $systemMetrics['financial_accounts_count'] ?? 0 }}</div>
                                <div class="text-[10px] font-bold text-slate-500 dark:text-slate-400">الخزائن</div>
                            </div>
                            <div class="px-1">
                                <div class="text-sm font-black text-sky-600 dark:text-sky-400">{{ $systemMetrics['orders_count'] ?? 0 }}</div>
                                <div class="text-[10px] font-bold text-slate-500 dark:text-slate-400">الطلبات</div>
                            </div>
                            <div class="px-1">
                                <div class="text-sm font-black text-violet-600 dark:text-violet-400">{{ $systemMetrics['employees_count'] ?? 0 }}</div>
                                <div class="text-[10px] font-bold text-slate-500 dark:text-slate-400">الموظفين</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ======================================================================= --}}
        {{-- 2. SEARCH & SECTION FILTER TOOLBAR --}}
        {{-- ======================================================================= --}}
        <section class="bz-g-toolbar rounded-2xl p-3.5 sm:p-4 space-y-3 backdrop-blur-md">
            <div class="flex flex-col sm:flex-row gap-3 sm:items-center justify-between">
                <div class="relative flex-1">
                    <span class="absolute inset-y-0 {{ $isRtl ? 'right-3.5' : 'left-3.5' }} flex items-center text-slate-500 dark:text-slate-300 pointer-events-none">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/></svg>
                    </span>
                    <input
                        id="guideSearchInput"
                        type="search"
                        oninput="filterGuideSections(this.value)"
                        placeholder="{{ $isRtl ? 'ابحث في كل الأقسام والشاشات والمراقبين (مثل: Sales Invoices, Collection, FEFO, GPS, Payroll)...' : 'Search sections, screens, singletons, observers (e.g. Sales Invoices, FEFO, GPS, Payroll)...' }}"
                        class="w-full {{ $isRtl ? 'pr-10 pl-4' : 'pl-10 pr-4' }} py-2.5 rounded-xl text-xs sm:text-sm bg-slate-50 dark:bg-slate-900/95 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-100 placeholder-slate-500 dark:placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500/40 focus:border-sky-500"
                    />
                </div>
                <div class="flex items-center gap-2 shrink-0 justify-between sm:justify-end">
                    <button type="button" id="toggleAllBtn" onclick="toggleAllModules()"
                        class="bz-g-btn-secondary text-xs">
                        {{ $isRtl ? 'فتح / طي كل الأقسام' : 'Expand / Collapse All' }}
                    </button>
                </div>
            </div>

            {{-- Section Filter Pills (Snap-scrollable on mobile) --}}
            <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-0.5 snap-x">
                <button type="button" data-filter="all" onclick="filterBySectionTag('all', this)" class="bz-filter-pill is-active snap-start">
                    <span>الكل (11 قسم)</span>
                </button>
                @foreach($guideModules as $mod)
                    <button type="button" data-filter="{{ $mod['key'] }}" onclick="filterBySectionTag('{{ $mod['key'] }}', this)" class="bz-filter-pill snap-start">
                        <span class="opacity-70 font-black">{{ $mod['order'] }}</span>
                        <span>{{ $mod['badge_label'] }}</span>
                    </button>
                @endforeach
            </div>
        </section>

        {{-- ======================================================================= --}}
        {{-- 3. LOGIC CYCLES: 11 COLLAPSIBLE SYSTEM MODULES --}}
        {{-- ======================================================================= --}}
        <div id="logic-cycles" class="space-y-4 sm:space-y-5 scroll-mt-36">
            @foreach($guideModules as $mod)
                @php
                    $accent = $accentMap[$mod['accent']] ?? $accentMap['sky'];
                    $slideCount = count($mod['gallery']);
                    $subCount = count($mod['subscreens']);
                    $firstSub = $mod['subscreens'][0];
                @endphp

                <section
                    id="sec-{{ $mod['key'] }}"
                    data-module-key="{{ $mod['key'] }}"
                    data-tags="{{ strtolower($mod['tags'] . ' ' . $mod['title_en'] . ' ' . $mod['title_ar']) }}"
                    class="bz-accordion-card bz-surface-card rounded-2xl sm:rounded-3xl overflow-hidden transition-all duration-300 scroll-mt-36 {{ $mod['default_open'] ? 'is-open' : '' }}"
                >
                    {{-- Accordion Header --}}
                    <div class="p-4 sm:p-5 lg:p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 cursor-pointer select-none hover:bg-slate-50/70 dark:hover:bg-white/[0.02] transition-colors"
                         onclick="toggleModuleAccordion('{{ $mod['key'] }}', event)">
                        <div class="flex items-start sm:items-center gap-3.5 sm:gap-4 min-w-0">
                            <div class="bz-g-num-badge" data-accent="{{ $mod['accent'] }}">
                                {{ $mod['order'] }}
                            </div>
                            <div class="min-w-0 space-y-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10px] sm:text-[11px] font-extrabold tracking-wide border {{ $accent['badge'] }}">
                                        {{ $mod['badge_label'] }}
                                    </span>
                                    <span class="text-[11px] font-bold text-slate-600 dark:text-slate-300">
                                        {{ $mod['metrics_badge'] }}
                                    </span>
                                </div>
                                <h2 class="text-base sm:text-lg lg:text-xl font-black text-slate-900 dark:text-white leading-snug">
                                    {{ $mod['title_ar'] }}
                                </h2>
                                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 line-clamp-2">
                                    {{ $mod['subtitle_ar'] }}
                                </p>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-2 shrink-0 self-end md:self-center">
                            <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-slate-100 dark:bg-slate-800/90 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700">
                                {{ $subCount }} شاشات فرعية
                            </span>
                            <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-sky-500/15 text-sky-800 dark:text-sky-200 border border-sky-500/25">
                                {{ $slideCount }} صور القسم
                            </span>
                            <button type="button"
                                onclick="event.stopPropagation(); openArchifyModal('{{ asset($mod['archify']['html_url']) }}', @js($mod['archify']['title_ar']))"
                                class="bz-g-btn-archify">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zm10 4l4-4m0 0l-4-4m4 4H10"/></svg>
                                <span>Archify</span>
                            </button>
                            <button type="button" aria-label="Toggle Section" class="bz-g-chevron-btn">
                                <svg class="w-5 h-5 bz-accordion-chevron" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Accordion Collapsible Body --}}
                    <div class="bz-accordion-panel">
                        <div class="bz-accordion-inner border-t border-slate-200/80 dark:border-slate-800/80">
                            <div class="p-4 sm:p-5 lg:p-6 space-y-6 bg-slate-50/45 dark:bg-slate-950/35">

                                {{-- A. SUB-SCREEN TABS --}}
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between text-[11px] font-extrabold text-slate-500 dark:text-slate-400">
                                        <span>شاشات القسم الفرعية (اختر الشاشة لعرض دورة عملها الكاملة):</span>
                                        <span class="hidden sm:inline">{{ $subCount }} Screens Covered</span>
                                    </div>
                                    <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1 snap-x" role="tablist">
                                        @foreach($mod['subscreens'] as $subIdx => $sub)
                                            <button
                                                type="button"
                                                role="tab"
                                                aria-selected="{{ $subIdx === 0 ? 'true' : 'false' }}"
                                                data-sub-tab="{{ $mod['key'] }}"
                                                data-target="{{ $sub['id'] }}"
                                                data-gallery-idx="{{ $sub['preview']['gallery_index'] }}"
                                                onclick="switchSubScreen('{{ $mod['key'] }}', '{{ $sub['id'] }}', this)"
                                                class="bz-g-subtab snap-start shrink-0 {{ $subIdx === 0 ? 'is-active' : '' }}"
                                            >
                                                <span>{{ $sub['tab_label_ar'] }}</span>
                                                <span class="opacity-80 font-semibold mx-0.5">({{ $sub['tab_label_en'] }})</span>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- B. ACTIVE SUB-SCREEN DETAIL PANELS --}}
                                @foreach($mod['subscreens'] as $subIdx => $sub)
                                    <div
                                        id="panel-{{ $sub['id'] }}"
                                        data-sub-panel="{{ $mod['key'] }}"
                                        class="{{ $subIdx === 0 ? 'block bz-stage-animate' : 'hidden' }} space-y-5"
                                    >
                                        {{-- Title + Safe Route Button --}}
                                        <div class="bz-surface-card rounded-2xl p-4 sm:p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                                            <div class="space-y-1.5">
                                                <h3 class="text-base sm:text-lg font-black text-slate-900 dark:text-white">{{ $sub['title_ar'] }}</h3>
                                                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed">{{ $sub['description_ar'] }}</p>
                                            </div>
                                            @if(!empty($sub['route_name']) && \Illuminate\Support\Facades\Route::has($sub['route_name']))
                                                <a href="{{ route($sub['route_name']) }}" target="_blank" rel="noopener"
                                                   class="bz-g-btn-archify shrink-0 px-4 py-2.5">
                                                    <span>{{ $sub['route_label'] }}</span>
                                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                </a>
                                            @endif
                                        </div>

                                        {{-- 4-Phase Logic Cycle Cards --}}
                                        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3.5">
                                            @foreach($sub['phases'] as $phase)
                                                <div class="bz-surface-card rounded-2xl p-4 flex flex-col gap-2 relative overflow-hidden">
                                                    <div class="flex items-center justify-between">
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-black border {{ $accent['badge'] }}">
                                                            PHASE {{ $phase['step'] }}
                                                        </span>
                                                        <span class="w-2 h-2 rounded-full {{ $accent['dot'] }}"></span>
                                                    </div>
                                                    <h4 class="text-sm font-black text-slate-900 dark:text-white">{{ $phase['title'] }}</h4>
                                                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">{{ $phase['detail'] }}</p>
                                                </div>
                                            @endforeach
                                        </div>

                                        {{-- Controls + Observer Impact + Sub-screen Visual Preview --}}
                                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-stretch">
                                            <div class="lg:col-span-5 bz-surface-card rounded-2xl p-4 sm:p-5 flex flex-col justify-between gap-4">
                                                <div class="space-y-3">
                                                    <div>
                                                        <div class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">
                                                            أدوات التحكم والأزرار داخل الشاشة (UI Controls)
                                                        </div>
                                                        <div class="flex flex-wrap gap-1.5">
                                                            @foreach($sub['controls'] as $ctrl)
                                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200/80 dark:border-slate-700">
                                                                    <span class="w-1.5 h-1.5 rounded-full {{ $accent['dot'] }}"></span>
                                                                    {{ $ctrl }}
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="rounded-xl p-3.5 bg-sky-50/80 dark:bg-sky-950/35 border border-sky-200/80 dark:border-sky-800/60">
                                                    <div class="text-[11px] font-extrabold text-sky-700 dark:text-sky-300 mb-1">
                                                        الأثر البرمجي والمراقبين (Observers &amp; Tables)
                                                    </div>
                                                    <p class="text-xs leading-relaxed text-slate-700 dark:text-slate-200 font-semibold">
                                                        {{ $sub['observer_impact'] }}
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="lg:col-span-7 bz-surface-card rounded-2xl overflow-hidden flex flex-col">
                                                <div class="px-4 py-2.5 bg-slate-50 dark:bg-slate-900/80 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between gap-2">
                                                    <span class="text-xs font-extrabold text-slate-700 dark:text-slate-200 truncate">
                                                        {{ $sub['preview']['highlight_step'] }}
                                                    </span>
                                                    <button type="button"
                                                        onclick="openSectionLightbox('{{ $mod['key'] }}', {{ $sub['preview']['gallery_index'] }})"
                                                        class="text-[11px] font-extrabold text-sky-600 dark:text-sky-400 hover:underline shrink-0">
                                                        تكبير الشاشة ⤢
                                                    </button>
                                                </div>
                                                <div class="relative flex-1 min-h-[210px] sm:min-h-[260px] bg-slate-100 dark:bg-slate-950 cursor-pointer group overflow-hidden"
                                                     onclick="openSectionLightbox('{{ $mod['key'] }}', {{ $sub['preview']['gallery_index'] }})">
                                                    <img
                                                        src="{{ asset($sub['preview']['light_src']) }}"
                                                        data-light-src="{{ asset($sub['preview']['light_src']) }}"
                                                        @if(!empty($sub['preview']['dark_src'])) data-dark-src="{{ asset($sub['preview']['dark_src']) }}" @endif
                                                        alt="{{ $sub['title_ar'] }}"
                                                        class="w-full h-full max-h-[340px] object-contain mx-auto transition-transform duration-500 group-hover:scale-[1.015]"
                                                        loading="lazy"
                                                    />
                                                    <div class="absolute bottom-2.5 {{ $isRtl ? 'left-2.5' : 'right-2.5' }} px-2.5 py-1 rounded-lg bg-slate-900/75 text-white text-[10px] font-bold backdrop-blur-sm">
                                                        {{ $sub['preview']['type'] === 'screenshot' ? 'Live Admin UI Capture' : 'Archify Interactive Diagram' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                {{-- C. SECTION-SCOPED HORIZONTAL MEDIA CAROUSEL / STRIP --}}
                                <div class="pt-3 border-t border-slate-200 dark:border-slate-800 space-y-3">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <div class="flex items-center gap-2">
                                            <span class="w-2.5 h-2.5 rounded-full {{ $accent['dot'] }}"></span>
                                            <h4 class="text-xs sm:text-sm font-black text-slate-900 dark:text-white">
                                                معرض صور ومخططات هذا القسم فقط ({{ $slideCount }} عناصر مرتبة)
                                            </h4>
                                            <span class="hidden sm:inline-block px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border border-emerald-500/20">
                                                Section-Locked Scroll
                                            </span>
                                        </div>

                                        <div class="flex items-center gap-1.5">
                                            <button type="button"
                                                onclick="scrollSectionStrip('{{ $mod['key'] }}', -1)"
                                                aria-label="Scroll section media backward"
                                                class="bz-g-arrow-btn">
                                                @if($isRtl) › @else ‹ @endif
                                            </button>
                                            <button type="button"
                                                onclick="scrollSectionStrip('{{ $mod['key'] }}', 1)"
                                                aria-label="Scroll section media forward"
                                                class="bz-g-arrow-btn">
                                                @if($isRtl) ‹ @else › @endif
                                            </button>
                                        </div>
                                    </div>

                                    <div id="strip-{{ $mod['key'] }}"
                                         class="bz-media-strip flex gap-3.5 overflow-x-auto pb-2 snap-x snap-mandatory scroll-smooth">
                                        @foreach($mod['gallery'] as $gIdx => $slide)
                                            <div
                                                data-strip-card="{{ $mod['key'] }}-{{ $gIdx }}"
                                                onclick="openSectionLightbox('{{ $mod['key'] }}', {{ $gIdx }})"
                                                class="bz-strip-card snap-start shrink-0 w-[265px] sm:w-[310px] bz-surface-card rounded-2xl overflow-hidden cursor-pointer transition-all hover:-translate-y-0.5 hover:border-sky-400 {{ $gIdx === $firstSub['preview']['gallery_index'] ? 'is-highlighted' : '' }}"
                                            >
                                                <div class="relative aspect-[16/10] bg-slate-100 dark:bg-slate-950 overflow-hidden">
                                                    <img
                                                        src="{{ asset($slide['light_src']) }}"
                                                        data-light-src="{{ asset($slide['light_src']) }}"
                                                        @if(!empty($slide['dark_src'])) data-dark-src="{{ asset($slide['dark_src']) }}" @endif
                                                        alt="{{ $slide['title_ar'] }}"
                                                        class="w-full h-full object-cover"
                                                        loading="lazy"
                                                    />
                                                    <div class="absolute top-2 {{ $isRtl ? 'right-2' : 'left-2' }} flex items-center gap-1.5">
                                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-black {{ $slide['kind'] === 'diagram' ? 'bg-sky-600 text-white' : 'bg-slate-900/80 text-white' }}">
                                                            {{ $slide['step_badge'] }}
                                                        </span>
                                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-white/90 dark:bg-slate-900/90 text-slate-700 dark:text-slate-200">
                                                            {{ $gIdx + 1 }}/{{ $slideCount }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="p-3 space-y-1">
                                                    <div class="text-xs font-extrabold text-slate-900 dark:text-white line-clamp-1">{{ $slide['title_ar'] }}</div>
                                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed">{{ $slide['caption_ar'] }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </section>
            @endforeach
        </div>

        {{-- ======================================================================= --}}
        {{-- 4. SYSTEM ELEMENTS & LOGIC CATALOG MATRIX (#system-catalog) --}}
        {{-- ======================================================================= --}}
        <section id="system-catalog" class="bz-surface-card rounded-2xl sm:rounded-3xl p-4 sm:p-6 space-y-4 scroll-mt-24">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10px] font-extrabold bg-amber-500/10 text-amber-700 dark:text-amber-300 border border-amber-500/20 mb-1.5">
                        SYSTEM ELEMENTS CATALOG
                    </span>
                    <h2 class="text-lg sm:text-xl font-black text-slate-900 dark:text-white">
                        فهرس العناصر، المسارات، المحركات الأحادية والمراقبين (بدون فجوات)
                    </h2>
                </div>
                <input
                    id="catalogSearchInput"
                    type="search"
                    oninput="filterCatalogTable(this.value)"
                    placeholder="فلترة الجدول بالمحرك أو المراقب أو الجدول..."
                    class="w-full sm:w-72 px-3.5 py-2 rounded-xl text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-sky-500/40"
                />
            </div>

            {{-- Desktop Table --}}
            <div class="hidden md:block overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-800">
                <table class="w-full text-xs text-{{ $isRtl ? 'right' : 'left' }} border-collapse">
                    <thead class="bg-slate-100 dark:bg-slate-900/90 text-slate-700 dark:text-slate-200 font-extrabold border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="p-3">القسم / Domain</th>
                            <th class="p-3">الشاشات والصفحات المشمولة</th>
                            <th class="p-3">المحركات الأحادية (Singletons)</th>
                            <th class="p-3">المراقبين (Observers)</th>
                            <th class="p-3">الجداول الرئيسية</th>
                            <th class="p-3 text-center">فتح</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @foreach($catalogRows as $row)
                            <tr data-catalog-row class="hover:bg-slate-50 dark:hover:bg-slate-900/45 transition-colors">
                                <td class="p-3 align-top font-black text-slate-900 dark:text-white whitespace-nowrap">
                                    <div>{{ $row['section'] }}</div>
                                    <div class="text-[11px] font-semibold text-sky-600 dark:text-sky-400">{{ $row['domain_ar'] }}</div>
                                </td>
                                <td class="p-3 align-top text-slate-600 dark:text-slate-300 leading-relaxed">{{ $row['screens'] }}</td>
                                <td class="p-3 align-top font-mono text-[11px] text-emerald-700 dark:text-emerald-300">{{ $row['singletons'] }}</td>
                                <td class="p-3 align-top font-mono text-[11px] text-violet-700 dark:text-violet-300">{{ $row['observers'] }}</td>
                                <td class="p-3 align-top font-mono text-[11px] text-slate-500 dark:text-slate-400">{{ $row['tables'] }}</td>
                                <td class="p-3 align-top text-center">
                                    @if(!empty($row['route_name']) && \Illuminate\Support\Facades\Route::has($row['route_name']))
                                        <a href="{{ route($row['route_name']) }}" target="_blank" rel="noopener"
                                           class="inline-flex px-2.5 py-1 rounded-lg bg-sky-600 hover:bg-sky-500 text-white font-bold text-[11px]">
                                            فتح
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile Responsive Cards --}}
            <div class="md:hidden space-y-3">
                @foreach($catalogRows as $row)
                    <div data-catalog-row class="rounded-xl p-3.5 bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 space-y-2">
                        <div class="flex items-center justify-between gap-2">
                            <div>
                                <div class="text-sm font-black text-slate-900 dark:text-white">{{ $row['section'] }}</div>
                                <div class="text-xs font-bold text-sky-600 dark:text-sky-400">{{ $row['domain_ar'] }}</div>
                            </div>
                            @if(!empty($row['route_name']) && \Illuminate\Support\Facades\Route::has($row['route_name']))
                                <a href="{{ route($row['route_name']) }}" target="_blank" rel="noopener"
                                   class="px-3 py-1 rounded-lg bg-sky-600 text-white font-bold text-xs">
                                    فتح الشاشة
                                </a>
                            @endif
                        </div>
                        <p class="text-xs text-slate-600 dark:text-slate-300">{{ $row['screens'] }}</p>
                        <div class="text-[11px] font-mono text-emerald-700 dark:text-emerald-300">Singletons: {{ $row['singletons'] }}</div>
                        <div class="text-[11px] font-mono text-violet-700 dark:text-violet-300">Observers: {{ $row['observers'] }}</div>
                        <div class="text-[11px] font-mono text-slate-500 dark:text-slate-400">Tables: {{ $row['tables'] }}</div>
                    </div>
                @endforeach
            </div>
        </section>

    </div>

    {{-- ======================================================================= --}}
    {{-- 5. STRICT SECTION-SCOPED LIGHTBOX MODAL (WITH THUMBNAIL STRIP & SWIPE) --}}
    {{-- ======================================================================= --}}
    <div id="guideLightbox"
         class="fixed inset-0 z-[110000] hidden flex-col bg-slate-950/95 backdrop-blur-md text-white p-3 sm:p-5 select-none"
         onclick="if (event.target === this) closeLightbox();">
        {{-- Top Chrome Bar --}}
        <div class="flex flex-wrap items-center justify-between gap-2 pb-3 border-b border-white/15 shrink-0">
            <div class="flex flex-wrap items-center gap-2 min-w-0">
                <span id="lbSectionBadge" class="px-2.5 py-1 rounded-lg text-[11px] font-black bg-emerald-500/25 text-emerald-200 border border-emerald-400/40"></span>
                <span id="lbStepBadge" class="px-2.5 py-1 rounded-lg text-[11px] font-black bg-sky-500/30 text-sky-100 border border-sky-400/40"></span>
                <span id="lbCounter" class="text-xs font-extrabold text-slate-200"></span>
                <h3 id="lbTitle" class="text-xs sm:text-sm lg:text-base font-black text-white truncate max-w-[90vw] sm:max-w-xl"></h3>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" id="lbArchifyBtn" onclick="openArchifyFromLightbox()"
                    style="background: #38bdf8; color: #031827;"
                    class="hidden items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-extrabold hover:opacity-90 transition-opacity">
                    <span>فتح المخطط التفاعلي Archify</span>
                </button>
                <button type="button" onclick="navigateSectionLightbox(-1)" title="السابق داخل القسم"
                    style="background: rgba(255,255,255,0.14); color: #ffffff; border: 1px solid rgba(255,255,255,0.22);"
                    class="w-9 h-9 rounded-lg flex items-center justify-center hover:opacity-85 font-bold">
                    @if($isRtl) → @else ← @endif
                </button>
                <button type="button" onclick="navigateSectionLightbox(1)" title="التالي داخل القسم"
                    style="background: rgba(255,255,255,0.14); color: #ffffff; border: 1px solid rgba(255,255,255,0.22);"
                    class="w-9 h-9 rounded-lg flex items-center justify-center hover:opacity-85 font-bold">
                    @if($isRtl) ← @else → @endif
                </button>
                <button type="button" onclick="closeLightbox()" title="إغلاق (Esc)"
                    style="background: #e11d48; color: #ffffff;"
                    class="w-9 h-9 rounded-lg flex items-center justify-center hover:opacity-90 font-bold">
                    ✕
                </button>
            </div>
        </div>

        {{-- Center Viewport with Touch Swipe --}}
        <div id="guideLightboxStage" class="flex-1 min-h-0 flex items-center justify-center relative py-3 overflow-hidden">
            <img id="lightboxImage" src="" alt="" class="max-w-full max-h-full object-contain rounded-xl shadow-2xl bz-stage-animate" />
        </div>

        {{-- Caption & Strictly Section-Only Thumbnails --}}
        <div class="pt-3 border-t border-white/15 space-y-2.5 shrink-0">
            <p id="lbCaption" class="text-xs sm:text-sm text-slate-100 text-center max-w-4xl mx-auto leading-relaxed"></p>
            <div id="lbThumbStrip" class="flex items-center justify-start sm:justify-center gap-2 overflow-x-auto no-scrollbar pb-1"></div>
        </div>
    </div>

    {{-- ======================================================================= --}}
    {{-- 6. INTERACTIVE ARCHIFY HTML MODAL VIEWER --}}
    {{-- ======================================================================= --}}
    <div id="archifyModal"
         class="fixed inset-0 z-[110000] hidden flex-col bg-slate-950/92 backdrop-blur-md p-2 sm:p-4 lg:p-6"
         onclick="if (event.target === this) closeArchifyModal();">
        <div class="flex-1 flex flex-col bg-white dark:bg-slate-950 rounded-2xl overflow-hidden border border-slate-700 shadow-2xl">
            <div class="flex items-center justify-between gap-3 px-4 py-3 bg-slate-900 text-white border-b border-slate-800 shrink-0">
                <div class="flex items-center gap-2 min-w-0">
                    <span class="px-2 py-0.5 rounded-md text-[10px] font-black bg-sky-500 text-slate-950">ARCHIFY LIVE</span>
                    <h3 id="archifyModalTitle" class="text-xs sm:text-sm font-black truncate text-white">Archify Interactive Diagram</h3>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <a id="archifyNewTabLink" href="#" target="_blank" rel="noopener"
                       style="background: rgba(255,255,255,0.14); color: #ffffff; border: 1px solid rgba(255,255,255,0.22);"
                       class="px-3 py-1.5 rounded-lg text-xs font-bold hover:opacity-85">
                        فتح في تبويب مستقل ↗
                    </a>
                    <button type="button" onclick="closeArchifyModal()"
                        style="background: #e11d48; color: #ffffff;"
                        class="w-8 h-8 rounded-lg flex items-center justify-center hover:opacity-90 font-bold">
                        ✕
                    </button>
                </div>
            </div>
            <iframe id="archifyIframe" src="about:blank" class="w-full flex-1 border-0 bg-slate-950" title="Archify Interactive Architecture Viewer"></iframe>
        </div>
    </div>

    <script>
        window.SECTION_GALLERIES = @json($sectionGalleriesPayload);

        let lbSectionKey = null;
        let lbIndex = 0;
        let lbTouchStartX = null;

        function isDarkThemeActive() {
            return document.documentElement.classList.contains('dark');
        }

        function resolveThemeImage(slide) {
            if (!slide) return '';
            if (isDarkThemeActive() && slide.dark_src) {
                return slide.dark_src;
            }
            return slide.light_src;
        }

        function syncAllThemeImages() {
            const dark = isDarkThemeActive();
            document.querySelectorAll('img[data-light-src][data-dark-src]').forEach(img => {
                const targetSrc = dark ? img.dataset.darkSrc : img.dataset.lightSrc;
                if (targetSrc && img.src !== targetSrc) {
                    img.src = targetSrc;
                }
            });

            if (lbSectionKey && !document.getElementById('guideLightbox').classList.contains('hidden')) {
                renderLightboxSlide();
            }
        }

        // Observe global theme toggle (<html class="dark">)
        const bzThemeObserver = new MutationObserver(() => syncAllThemeImages());
        bzThemeObserver.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
        window.addEventListener('DOMContentLoaded', syncAllThemeImages);

        // =========================================================================
        // ACCORDION & SUB-SCREEN TAB CONTROLLERS
        // =========================================================================
        window.toggleModuleAccordion = function(moduleKey, ev) {
            if (ev && ev.target && ev.target.closest('button, a')) {
                const btn = ev.target.closest('button');
                if (btn && btn.getAttribute('aria-label') !== 'Toggle Section') return;
            }
            const card = document.querySelector(`[data-module-key="${moduleKey}"]`);
            if (card) card.classList.toggle('is-open');
        };

        window.toggleAllModules = function() {
            const cards = Array.from(document.querySelectorAll('.bz-accordion-card')).filter(c => c.style.display !== 'none');
            const anyClosed = cards.some(c => !c.classList.contains('is-open'));
            cards.forEach(c => c.classList.toggle('is-open', anyClosed));
        };

        window.switchSubScreen = function(moduleKey, targetId, tabBtn) {
            document.querySelectorAll(`[data-sub-tab="${moduleKey}"]`).forEach(btn => {
                btn.classList.remove('is-active');
                btn.setAttribute('aria-selected', 'false');
            });

            if (tabBtn) {
                tabBtn.classList.add('is-active');
                tabBtn.setAttribute('aria-selected', 'true');
            }

            document.querySelectorAll(`[data-sub-panel="${moduleKey}"]`).forEach(panel => {
                panel.classList.add('hidden');
                panel.classList.remove('block', 'bz-stage-animate');
            });

            const activePanel = document.getElementById(`panel-${targetId}`);
            if (activePanel) {
                activePanel.classList.remove('hidden');
                activePanel.classList.add('block', 'bz-stage-animate');
            }

            // Highlight corresponding slide inside THIS section's strip
            if (tabBtn && tabBtn.dataset.galleryIdx !== undefined) {
                const gIdx = tabBtn.dataset.galleryIdx;
                document.querySelectorAll(`[data-strip-card^="${moduleKey}-"]`).forEach(card => {
                    card.classList.remove('is-highlighted');
                });
                const targetCard = document.querySelector(`[data-strip-card="${moduleKey}-${gIdx}"]`);
                if (targetCard) {
                    targetCard.classList.add('is-highlighted');
                    targetCard.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'nearest' });
                }
            }
        };

        // =========================================================================
        // SECTION-LOCKED HORIZONTAL CAROUSEL SCROLLING
        // =========================================================================
        window.scrollSectionStrip = function(moduleKey, direction) {
            const strip = document.getElementById(`strip-${moduleKey}`);
            if (!strip) return;
            const isRtl = document.documentElement.dir === 'rtl' || getComputedStyle(strip).direction === 'rtl';
            const delta = (isRtl ? -direction : direction) * 330;
            strip.scrollBy({ left: delta, behavior: 'smooth' });
        };

        // =========================================================================
        // SECTION-LOCKED LIGHTBOX (PREVENTS CROSS-SECTION NAVIGATION)
        // =========================================================================
        window.openSectionLightbox = function(sectionKey, idx) {
            const group = window.SECTION_GALLERIES[sectionKey];
            if (!group || !Array.isArray(group.slides) || group.slides.length === 0) return;

            lbSectionKey = sectionKey;
            lbIndex = Math.max(0, Math.min(Number(idx) || 0, group.slides.length - 1));

            const modal = document.getElementById('guideLightbox');
            modal.classList.remove('hidden');
            modal.classList.add('is-open');
            document.body.style.overflow = 'hidden';

            renderLightboxSlide();
        };

        function renderLightboxSlide() {
            const group = window.SECTION_GALLERIES[lbSectionKey];
            if (!group) return;
            const slides = group.slides;
            const slide = slides[lbIndex];

            document.getElementById('lbSectionBadge').textContent = group.label;
            document.getElementById('lbStepBadge').textContent = slide.step_badge;
            document.getElementById('lbCounter').textContent = `${lbIndex + 1} / ${slides.length}`;
            document.getElementById('lbTitle').textContent = slide.title_ar;
            document.getElementById('lbCaption').textContent = slide.caption_ar;

            const img = document.getElementById('lightboxImage');
            img.classList.remove('bz-stage-animate');
            void img.offsetWidth;
            img.src = resolveThemeImage(slide);
            img.alt = slide.title_ar;
            img.classList.add('bz-stage-animate');

            const archBtn = document.getElementById('lbArchifyBtn');
            if (slide.html_url) {
                archBtn.dataset.htmlUrl = slide.html_url;
                archBtn.dataset.htmlTitle = slide.title_ar;
                archBtn.classList.remove('hidden');
                archBtn.classList.add('inline-flex');
            } else {
                archBtn.classList.add('hidden');
                archBtn.classList.remove('inline-flex');
            }

            // Build thumbnails strictly for the current section ONLY
            const thumbContainer = document.getElementById('lbThumbStrip');
            thumbContainer.innerHTML = '';
            slides.forEach((item, i) => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = `shrink-0 relative w-24 h-14 rounded-lg overflow-hidden border-2 transition-all ${
                    i === lbIndex
                        ? 'border-sky-400 ring-2 ring-sky-400/50 scale-105 opacity-100'
                        : 'border-white/15 opacity-55 hover:opacity-90'
                }`;
                btn.onclick = () => {
                    lbIndex = i;
                    renderLightboxSlide();
                };

                const thumbImg = document.createElement('img');
                thumbImg.src = resolveThemeImage(item);
                thumbImg.alt = item.step_badge;
                thumbImg.className = 'w-full h-full object-cover';
                btn.appendChild(thumbImg);

                const tag = document.createElement('span');
                tag.className = 'absolute bottom-0.5 inset-x-0.5 text-[9px] font-black bg-black/75 text-white text-center rounded px-1 truncate';
                tag.textContent = item.step_badge;
                btn.appendChild(tag);

                thumbContainer.appendChild(btn);
            });
        }

        window.navigateSectionLightbox = function(delta) {
            if (!lbSectionKey || !window.SECTION_GALLERIES[lbSectionKey]) return;
            const slides = window.SECTION_GALLERIES[lbSectionKey].slides;
            if (!slides.length) return;
            lbIndex = (lbIndex + delta + slides.length) % slides.length;
            renderLightboxSlide();
        };

        window.closeLightbox = function() {
            const modal = document.getElementById('guideLightbox');
            modal.classList.add('hidden');
            modal.classList.remove('is-open');
            document.body.style.overflow = '';
            lbSectionKey = null;
        };

        window.openArchifyFromLightbox = function() {
            const btn = document.getElementById('lbArchifyBtn');
            if (!btn.dataset.htmlUrl) return;
            closeLightbox();
            openArchifyModal(btn.dataset.htmlUrl, btn.dataset.htmlTitle || 'Archify Diagram');
        };

        // Touch swipe inside Lightbox strictly within current section
        const lbStage = document.getElementById('guideLightboxStage');
        lbStage.addEventListener('touchstart', (e) => {
            if (e.touches && e.touches.length === 1) {
                lbTouchStartX = e.touches[0].clientX;
            }
        }, { passive: true });
        lbStage.addEventListener('touchend', (e) => {
            if (lbTouchStartX === null || !e.changedTouches || !e.changedTouches.length) return;
            const diffX = e.changedTouches[0].clientX - lbTouchStartX;
            lbTouchStartX = null;
            if (Math.abs(diffX) > 40) {
                const isRtl = document.documentElement.dir === 'rtl';
                const step = diffX < 0 ? (isRtl ? -1 : 1) : (isRtl ? 1 : -1);
                navigateSectionLightbox(step);
            }
        }, { passive: true });

        // Keyboard controls
        document.addEventListener('keydown', (e) => {
            const lbOpen = !document.getElementById('guideLightbox').classList.contains('hidden');
            const archOpen = !document.getElementById('archifyModal').classList.contains('hidden');
            if (e.key === 'Escape') {
                if (archOpen) closeArchifyModal();
                else if (lbOpen) closeLightbox();
            } else if (lbOpen && (e.key === 'ArrowRight' || e.key === 'ArrowLeft')) {
                const isRtl = document.documentElement.dir === 'rtl';
                const delta = e.key === 'ArrowRight' ? (isRtl ? -1 : 1) : (isRtl ? 1 : -1);
                navigateSectionLightbox(delta);
            }
        });

        // =========================================================================
        // INTERACTIVE ARCHIFY MODAL VIEWER
        // =========================================================================
        window.openArchifyModal = function(url, title) {
            const modal = document.getElementById('archifyModal');
            document.getElementById('archifyModalTitle').textContent = title || 'Archify Interactive Diagram';
            document.getElementById('archifyNewTabLink').href = url;
            document.getElementById('archifyIframe').src = url;
            modal.classList.remove('hidden');
            modal.classList.add('is-open');
            document.body.style.overflow = 'hidden';
        };

        window.closeArchifyModal = function() {
            const modal = document.getElementById('archifyModal');
            modal.classList.add('hidden');
            modal.classList.remove('is-open');
            document.getElementById('archifyIframe').src = 'about:blank';
            document.body.style.overflow = '';
        };

        // =========================================================================
        // SEARCH & FILTERING + DEEP ANCHOR HASH HANDLER
        // =========================================================================
        window.filterBySectionTag = function(filterKey, btnEl) {
            document.querySelectorAll('.bz-filter-pill').forEach(p => p.classList.remove('is-active'));
            if (btnEl) btnEl.classList.add('is-active');

            document.querySelectorAll('.bz-accordion-card').forEach(card => {
                const show = filterKey === 'all' || card.dataset.moduleKey === filterKey;
                card.style.display = show ? '' : 'none';
                if (filterKey !== 'all' && show) {
                    card.classList.add('is-open');
                }
            });
        };

        window.filterGuideSections = function(rawQuery) {
            const q = (rawQuery || '').trim().toLowerCase();
            if (!q) {
                const activeFilterBtn = document.querySelector('.bz-filter-pill.is-active');
                filterBySectionTag(activeFilterBtn ? activeFilterBtn.dataset.filter : 'all', activeFilterBtn);
                return;
            }
            document.querySelectorAll('.bz-accordion-card').forEach(card => {
                const hay = (card.dataset.tags || '') + ' ' + (card.innerText || '').toLowerCase();
                const match = hay.includes(q);
                card.style.display = match ? '' : 'none';
                if (match) card.classList.add('is-open');
            });
        };

        window.filterCatalogTable = function(rawQuery) {
            const q = (rawQuery || '').trim().toLowerCase();
            document.querySelectorAll('[data-catalog-row]').forEach(row => {
                const match = !q || (row.innerText || '').toLowerCase().includes(q);
                row.style.display = match ? '' : 'none';
            });
        };

        window.scrollToGuideAnchor = function(id) {
            const el = document.getElementById(id);
            if (!el) return;
            el.scrollIntoView({ behavior: 'smooth', block: 'start' });
            el.classList.add('ring-4', 'ring-sky-500/40');
            setTimeout(() => el.classList.remove('ring-4', 'ring-sky-500/40'), 2000);
        };

        window.handleUrlHash = function() {
            const hash = (window.location.hash || '').replace(/^#/, '').trim();
            if (!hash) return;

            let target = document.getElementById(hash) || document.getElementById(`sec-${hash}`);
            if (!target) return;

            if (target.dataset && target.dataset.moduleKey) {
                const allBtn = document.querySelector('.bz-filter-pill[data-filter="all"]');
                filterBySectionTag('all', allBtn);
                target.classList.add('is-open');
            }

            setTimeout(() => {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                target.classList.add('ring-4', 'ring-sky-500/40');
                setTimeout(() => target.classList.remove('ring-4', 'ring-sky-500/40'), 2200);
            }, 80);
        };

        window.addEventListener('DOMContentLoaded', window.handleUrlHash);
        window.addEventListener('hashchange', window.handleUrlHash);
    </script>
</x-layouts.admin>
