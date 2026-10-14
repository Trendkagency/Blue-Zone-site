<x-layouts.admin 
    :title="app()->getLocale() === 'ar' ? 'إدارة سلايدر الواجهة والمنتجات المميزة' : 'Hero Slider & Product Carousel Command Center'" 
    :breadcrumbs="[
        (app()->getLocale() === 'ar' ? 'المنتجات' : 'Products') => route('admin.products.index'),
        (app()->getLocale() === 'ar' ? 'سلايدر الواجهة الرئيسي' : 'Hero Carousel') => route('admin.hero-slides.index')
    ]">

    <!-- ========================================================================= -->
    <!-- HEADER & ACTION BAR -->
    <!-- ========================================================================= -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <span class="w-11 h-11 rounded-2xl bg-gradient-to-br from-[#0A4F78] via-[#0A4F78] to-[#2A8FC2] text-white shadow-lg shadow-[#0A4F78]/25 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-images text-lg"></i>
                </span>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                            {{ app()->getLocale() === 'ar' ? 'سلايدر الواجهة الرئيسي وقسم الشرائح الدوارة' : 'Hero Slider & Product Carousel Manager' }}
                        </h1>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                            {{ app()->getLocale() === 'ar' ? 'نشط ومباشر' : 'Live Sync' }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        {{ app()->getLocale() === 'ar' ? 'تحكم كامل في شرائح العرض، العناوين الثنائية، الخلفيات السينمائية، وبطاقات المنتجات العائمة (Floating Cards).' : 'Full visual control over hero slides, bilingual headlines, cinematic backgrounds, dual CTAs, and floating product showcase cards.' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <a href="{{ url('/') }}" target="_blank" class="btn btn-secondary btn-sm font-bold shadow-xs">
                <i class="fa-solid fa-arrow-up-right-from-square mr-1 ml-1 text-[#2A8FC2]"></i>
                {{ app()->getLocale() === 'ar' ? 'معاينة في المتجر' : 'View Live Carousel' }}
            </a>

            <button type="button" onclick="openSlideModal()" class="btn btn-primary btn-sm font-bold shadow-md shadow-[#0A4F78]/25 cursor-pointer">
                <i class="fa-solid fa-plus mr-1 ml-1"></i>
                {{ app()->getLocale() === 'ar' ? 'إضافة شريحة جديدة' : 'Add New Slide' }}
            </button>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="mb-6 p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 flex items-center justify-between text-sm font-bold shadow-xs animate-in fade-in duration-300">
            <div class="flex items-center gap-2.5">
                <i class="fa-solid fa-circle-check text-emerald-500 text-lg"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 dark:hover:text-white cursor-pointer"><i class="fa-solid fa-xmark"></i></button>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- METRIC OVERVIEW CARDS (RESPONSIVE & LUXURY STYLING) -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="p-4 sm:p-5 rounded-2xl bg-white dark:bg-[#031827] border border-slate-200/90 dark:border-slate-800 shadow-sm flex items-center gap-4 transition-all hover:shadow-md">
            <div class="w-12 h-12 rounded-2xl bg-[#0A4F78]/10 text-[#0A4F78] dark:text-[#2A8FC2] flex items-center justify-center text-xl flex-shrink-0">
                <i class="fa-solid fa-layer-group"></i>
            </div>
            <div>
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 block">{{ app()->getLocale() === 'ar' ? 'إجمالي الشرائح المسجلة' : 'Total Registered Slides' }}</span>
                <span class="text-2xl font-black text-slate-900 dark:text-white">{{ $totalSlides }}</span>
            </div>
        </div>

        <div class="p-4 sm:p-5 rounded-2xl bg-white dark:bg-[#031827] border border-slate-200/90 dark:border-slate-800 shadow-sm flex items-center gap-4 transition-all hover:shadow-md">
            <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl flex-shrink-0">
                <i class="fa-solid fa-play"></i>
            </div>
            <div>
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 block">{{ app()->getLocale() === 'ar' ? 'الشرائح المعروضة حالياً' : 'Live Active Slides' }}</span>
                <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400" id="activeSlidesMetric">{{ $activeSlides }}</span>
            </div>
        </div>

        <div class="p-4 sm:p-5 rounded-2xl bg-white dark:bg-[#031827] border border-slate-200/90 dark:border-slate-800 shadow-sm flex items-center gap-4 transition-all hover:shadow-md">
            <div class="w-12 h-12 rounded-2xl bg-cyan-500/10 text-[#2A8FC2] flex items-center justify-center text-xl flex-shrink-0">
                <i class="fa-solid fa-capsules"></i>
            </div>
            <div>
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 block">{{ app()->getLocale() === 'ar' ? 'منتجات مرتبطة بالشرائح' : 'Connected Products' }}</span>
                <span class="text-2xl font-black text-slate-900 dark:text-white">{{ $connectedProductsCount }}</span>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- GLOBAL SLIDER CONTROLS PANEL (DARK/LIGHT HARMONY) -->
    <!-- ========================================================================= -->
    <div class="p-5 mb-6 shadow-sm border border-slate-200/90 dark:border-slate-800 bg-white dark:bg-[#031827] rounded-3xl">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800/80 pb-3 mb-4 flex-wrap gap-2">
            <div class="flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-xl bg-[#0A4F78]/10 text-[#0A4F78] dark:text-[#2A8FC2] flex items-center justify-center text-xs">
                    <i class="fa-solid fa-sliders"></i>
                </span>
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">
                        {{ app()->getLocale() === 'ar' ? 'إعدادات تشغيل السلايدر العامة (Global Carousel Settings)' : 'Global Hero Carousel & Autoplay Controls' }}
                    </h3>
                    <span class="text-[11px] text-slate-400">
                        {{ app()->getLocale() === 'ar' ? 'التحكم بالتبديل التلقائي وسرعة العرض وإظهار/إخفاء القسم' : 'Configure autoplay cycle, transition duration, and storefront visibility' }}
                    </span>
                </div>
            </div>

            <form action="{{ route('admin.hero-slides.reset-defaults') }}" method="POST" onsubmit="return confirm('{{ app()->getLocale() === 'ar' ? 'هل أنت متأكد من رغبتك في استعادة الشرائح الافتراضية الـ 5؟' : 'Are you sure you want to restore the 5 original curated brand slides?' }}');">
                @csrf
                <button type="submit" class="text-xs font-bold text-slate-500 hover:text-amber-600 dark:text-slate-400 dark:hover:text-amber-400 transition-colors flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-arrow-rotate-left text-amber-500"></i>
                    {{ app()->getLocale() === 'ar' ? 'استعادة الشرائح الـ 5 الافتراضية' : 'Reset to Curated Defaults' }}
                </button>
            </form>
        </div>

        <form method="POST" action="{{ route('admin.hero-slides.global-settings') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
            @csrf

            <!-- Master Toggle -->
            <div class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50/80 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800">
                <div>
                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">{{ app()->getLocale() === 'ar' ? 'حالة السلايدر في الواجهة' : 'Carousel Visibility' }}</span>
                    <span class="text-[10px] text-slate-500 dark:text-slate-400">{{ app()->getLocale() === 'ar' ? 'إظهار أو إخفاء القسم كاملاً' : 'Show or hide hero slider' }}</span>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="landing_hero_slider_enabled" value="1" class="sr-only peer" {{ $heroSettings['landing_hero_slider_enabled'] ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                </label>
            </div>

            <!-- Autoplay Toggle -->
            <div class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50/80 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800">
                <div>
                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">{{ app()->getLocale() === 'ar' ? 'التبديل التلقائي (Autoplay)' : 'Autoplay Cycling' }}</span>
                    <span class="text-[10px] text-slate-500 dark:text-slate-400">{{ app()->getLocale() === 'ar' ? 'تبديل الشرائح تلقائياً' : 'Cycle slides automatically' }}</span>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="landing_hero_slider_autoplay" value="1" class="sr-only peer" {{ $heroSettings['landing_hero_slider_autoplay'] ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#2A8FC2]"></div>
                </label>
            </div>

            <!-- Duration Selector -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                    {{ app()->getLocale() === 'ar' ? 'مدة بقاء الشريحة (ثانية)' : 'Slide Transition Delay' }}
                </label>
                <select name="landing_hero_slider_delay" class="form-select text-xs w-full bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white rounded-xl focus:ring-2 focus:ring-[#2A8FC2]/20 focus:border-[#2A8FC2]">
                    <option value="3500" {{ $heroSettings['landing_hero_slider_delay'] == 3500 ? 'selected' : '' }}>3.5s (Fast / سريع)</option>
                    <option value="5500" {{ $heroSettings['landing_hero_slider_delay'] == 5500 ? 'selected' : '' }}>5.5s (Recommended / افتراضي)</option>
                    <option value="7500" {{ $heroSettings['landing_hero_slider_delay'] == 7500 ? 'selected' : '' }}>7.5s (Relaxed / متأنٍ)</option>
                    <option value="10000" {{ $heroSettings['landing_hero_slider_delay'] == 10000 ? 'selected' : '' }}>10.0s (Cinematic / سينمائي)</option>
                </select>
            </div>

            <div>
                <button type="submit" class="btn btn-primary text-xs font-bold w-full h-[40px] shadow-sm cursor-pointer">
                    <i class="fa-solid fa-floppy-disk mr-1.5 ml-1.5"></i>
                    {{ app()->getLocale() === 'ar' ? 'حفظ إعدادات السلايدر' : 'Save Carousel Settings' }}
                </button>
            </div>
        </form>
    </div>

    <!-- ========================================================================= -->
    <!-- SLIDES LIST (RESPONSIVE CARDS WITH LIVE MINI SHOWCASES) -->
    <!-- ========================================================================= -->
    <div class="space-y-4">
        @forelse($slides as $slide)
            @php
                $sId = $slide->id;
                $sActive = $slide->is_active;
                $sOrder = $slide->slide_order;
                $sBadge = $slide->display_badge;
                $sTitle = $slide->display_title;
                $sSub = $slide->display_subtitle;
                $sBg = $slide->display_bg_image_url;
                $sProduct = $slide->product;
                $sShowCard = $slide->show_floating_card;
                $sCardTitle = $slide->display_card_title;
                $sCardBadge = $slide->display_card_badge;
                $sCardSub = $slide->display_card_subtitle;
                $sCardImg = $slide->display_card_image_url;
            @endphp

            <div class="p-4 sm:p-5 rounded-3xl bg-white dark:bg-[#031827] border border-slate-200/90 dark:border-slate-800 shadow-sm transition-all hover:shadow-md hover:border-[#2A8FC2]/40 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4" id="slide-card-{{ $sId }}">
                <!-- Left: Order, Thumbnail, & Headline Info -->
                <div class="flex items-center gap-3.5 sm:gap-4 flex-1 min-w-0 w-full sm:w-auto">
                    <!-- Order Badge -->
                    <div class="flex flex-col items-center justify-center p-2 rounded-2xl bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 font-mono text-xs font-black min-w-[42px] flex-shrink-0">
                        <span class="text-[9px] text-slate-400 font-normal">#</span>
                        <span>{{ $sOrder }}</span>
                    </div>

                    <!-- Slide Background Visual Thumbnail -->
                    <div class="relative w-24 sm:w-32 h-20 rounded-2xl overflow-hidden shadow-xs border border-slate-200 dark:border-white/10 flex-shrink-0 group">
                        <img src="{{ $sBg }}" alt="{{ $sTitle }}" class="w-full h-full object-cover filter brightness-90 transition-transform duration-300 group-hover:scale-105" onerror="this.onerror=null; this.src='/assets/images/hero/hero-01.webp';">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent flex items-end p-1.5">
                            <span class="text-[8px] font-black text-white truncate drop-shadow-sm">{{ $sBadge }}</span>
                        </div>
                    </div>

                    <!-- Headline & Details -->
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 flex-wrap mb-1">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider {{ $slide->badge_color === 'cyan' ? 'bg-[#2A8FC2]/20 text-[#2A8FC2]' : ($slide->badge_color === 'amber' ? 'bg-amber-500/20 text-amber-600' : 'bg-[#67B34A]/20 text-[#67B34A]') }}">
                                {{ $sBadge }}
                            </span>

                            @if($sProduct)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#0A4F78]/15 text-[#0A4F78] dark:text-[#2A8FC2] flex items-center gap-1">
                                    <i class="fa-solid fa-capsules text-[9px]"></i>
                                    {{ $sProduct->name }}
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/15 text-amber-700 dark:text-amber-400 flex items-center gap-1">
                                    <i class="fa-solid fa-earth-americas text-[9px]"></i>
                                    {{ app()->getLocale() === 'ar' ? 'بؤرة إقليمية / تصميم مخصص' : 'Regional Hotspot / Feature' }}
                                </span>
                            @endif
                        </div>

                        <h4 class="text-sm sm:text-base font-black text-slate-900 dark:text-white truncate">
                            {!! $slide->formatted_title !!}
                        </h4>

                        <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-1 mt-0.5">
                            {{ $sSub }}
                        </p>

                        <!-- Dual CTA Links Preview -->
                        <div class="flex items-center gap-3 mt-1.5 text-[11px] text-slate-400 flex-wrap">
                            <span class="flex items-center gap-1">
                                <i class="fa-solid fa-link text-[10px] text-emerald-500"></i>
                                <span class="font-bold text-slate-700 dark:text-slate-300">{{ $slide->display_primary_cta_text }}</span> ➔ {{ $slide->primary_cta_link ?: '/products' }}
                            </span>
                            @if($slide->secondary_cta_text_en)
                                <span class="hidden sm:inline-flex items-center gap-1">
                                    <i class="fa-solid fa-link text-[10px] text-[#2A8FC2]"></i>
                                    <span class="font-bold text-slate-700 dark:text-slate-300">{{ $slide->display_secondary_cta_text }}</span> ➔ {{ $slide->secondary_cta_link ?: '/products' }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Middle: Floating Card Preview (Exact representation of storefront card) -->
                @if($sShowCard)
                    <div class="w-full sm:w-auto p-2.5 rounded-2xl bg-gradient-to-r from-[#031827] to-[#0A4F78] text-white border border-[#2A8FC2]/40 flex items-center gap-3 shadow-md min-w-[220px] max-w-[270px] flex-shrink-0">
                        <div class="w-10 h-10 rounded-xl overflow-hidden bg-white/10 p-0.5 flex-shrink-0 flex items-center justify-center">
                            <img src="{{ $sCardImg }}" alt="{{ $sCardTitle }}" class="w-full h-full object-contain" onerror="this.onerror=null; this.src='/assets/products/blue-mind.webp';">
                        </div>
                        <div class="min-w-0 flex-1">
                            <span class="block text-[8px] font-mono font-bold text-[#67B34A] uppercase tracking-wider truncate">{{ $sCardBadge }}</span>
                            <span class="block text-xs font-black truncate text-white">{{ $sCardTitle }}</span>
                            <span class="block text-[9px] text-[#2A8FC2] font-semibold truncate">{{ $sCardSub }}</span>
                        </div>
                    </div>
                @else
                    <div class="text-xs text-slate-400 font-italic px-3 py-1.5 bg-slate-50 dark:bg-slate-900 rounded-xl border border-dashed border-slate-200 dark:border-slate-800 flex-shrink-0">
                        {{ app()->getLocale() === 'ar' ? 'البطاقة العائمة مخفية' : 'Floating Card: Hidden' }}
                    </div>
                @endif

                <!-- Right: Active Toggle & Management Actions -->
                <div class="flex items-center gap-2 self-end lg:self-center flex-shrink-0">
                    <!-- Status Toggle Button -->
                    <button type="button" 
                            onclick="toggleSlideStatus(this, {{ $sId }})"
                            data-active="{{ $sActive ? '1' : '0' }}"
                            class="slide-toggle-btn inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold transition-all shadow-xs border cursor-pointer {{ $sActive ? 'bg-emerald-50 text-emerald-700 border-emerald-300 hover:bg-emerald-100 dark:bg-emerald-950/50 dark:text-emerald-300 dark:border-emerald-800' : 'bg-slate-100 text-slate-500 border-slate-200 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700' }}"
                            title="{{ app()->getLocale() === 'ar' ? 'تبديل حالة العرض' : 'Toggle display in carousel' }}">
                        <span class="w-2 h-2 rounded-full {{ $sActive ? 'bg-emerald-500 ring-2 ring-emerald-300/50' : 'bg-slate-400' }} indicator-dot"></span>
                        <span class="indicator-text">{{ $sActive ? (app()->getLocale() === 'ar' ? 'معروض' : 'Active') : (app()->getLocale() === 'ar' ? 'مخفي' : 'Hidden') }}</span>
                    </button>

                    <!-- Edit Slide -->
                    <button type="button" onclick="editSlide({{ $sId }})" class="action-btn text-primary hover:bg-blue-50 dark:hover:bg-blue-900/30 cursor-pointer" title="{{ __('app.actions.edit') }}">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </button>

                    <!-- Delete Slide -->
                    <form action="{{ route('admin.hero-slides.destroy', $sId) }}" method="POST" onsubmit="return confirm('{{ app()->getLocale() === 'ar' ? 'هل أنت متأكد من حذف هذه الشريحة؟' : 'Are you sure you want to delete this slide?' }}');" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="action-btn action-danger cursor-pointer" title="{{ __('app.actions.delete') }}">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="p-12 text-center rounded-3xl bg-white dark:bg-[#031827] border border-slate-200/90 dark:border-slate-800 shadow-sm">
                <i class="fa-solid fa-images text-4xl text-slate-400 mb-3 block"></i>
                <h3 class="text-base font-bold text-slate-700 dark:text-slate-300">{{ app()->getLocale() === 'ar' ? 'لا توجد شرائح مسجلة حالياً' : 'No hero slides created yet' }}</h3>
                <p class="text-xs text-slate-500 mt-1 mb-4">{{ app()->getLocale() === 'ar' ? 'يمكنك استعادة الشرائح الافتراضية الـ 5 بضغطة زر واحدة.' : 'You can restore the 5 curated brand slides with one click.' }}</p>
                <form action="{{ route('admin.hero-slides.reset-defaults') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-sm font-bold">
                        <i class="fa-solid fa-arrow-rotate-left mr-1 ml-1"></i>
                        {{ app()->getLocale() === 'ar' ? 'استعادة الشرائح الـ 5 الافتراضية الآن' : 'Restore Curated Brand Slides' }}
                    </button>
                </form>
            </div>
        @endforelse
    </div>

    <!-- ========================================================================= -->
    <!-- SLIDE CREATE / EDIT MODAL (LUXURY EXECUTIVE DESIGN & SEAMLESS DARK/LIGHT) -->
    <!-- ========================================================================= -->
    <div id="slideModal" class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-5 bg-slate-950/70 backdrop-blur-md hidden transition-all duration-300">
        <div class="bg-white dark:bg-[#031827] rounded-3xl shadow-[0_25px_70px_-15px_rgba(0,0,0,0.4)] border border-slate-200/90 dark:border-slate-800 w-full max-w-4xl max-h-[92vh] flex flex-col overflow-hidden animate-in fade-in zoom-in-95 duration-200">
            
            <!-- Modal Header -->
            <div class="p-4 sm:p-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-gradient-to-r from-slate-50 via-white to-slate-50 dark:from-[#062B49]/60 dark:via-[#031827] dark:to-[#062B49]/60 flex-shrink-0">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-2xl bg-gradient-to-br from-[#0A4F78] via-[#0A4F78] to-[#2A8FC2] text-white flex items-center justify-center text-sm shadow-md shadow-[#0A4F78]/25 flex-shrink-0">
                        <i class="fa-solid fa-sliders"></i>
                    </span>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-base sm:text-lg font-black text-slate-900 dark:text-white" id="modalTitle">
                                {{ app()->getLocale() === 'ar' ? 'تخصيص شريحة السلايدر والمنتج' : 'Customize Carousel Slide & Product Showcase' }}
                            </h3>
                            <span class="px-2 py-0.5 rounded-md bg-[#2A8FC2]/15 text-[#2A8FC2] text-[11px] font-mono font-bold hidden" id="modalSlideBadge">#</span>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                            {{ app()->getLocale() === 'ar' ? 'تحكم بالمنتج المربوط، والبطاقة العائمة، والعناوين الثنائية، والأزرار والخلفية' : 'Configure linked formulation, floating card, bilingual copy, dual action buttons, and visual media' }}
                        </p>
                    </div>
                </div>
                <button type="button" onclick="closeSlideModal()" class="w-9 h-9 rounded-full flex items-center justify-center text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer" aria-label="Close">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <!-- Modal Body Form -->
            <form id="slideForm" method="POST" action="{{ route('admin.hero-slides.store') }}" enctype="multipart/form-data" class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-5">
                @csrf
                <div id="methodContainer"></div>
                <input type="hidden" name="slide_id" id="slide_id" value="">

                <!-- Tab Navigation in Modal (Responsive Scrollable Pills) -->
                <div class="p-1.5 rounded-2xl bg-slate-100/90 dark:bg-slate-900/90 border border-slate-200 dark:border-slate-800 flex items-center gap-1.5 overflow-x-auto scrollbar-none no-scrollbar flex-nowrap">
                    <button type="button" onclick="switchModalTab('tab-product')" id="btn-tab-product" class="modal-tab-btn flex-shrink-0 px-3.5 py-2 rounded-xl text-xs font-black transition-all bg-gradient-to-r from-[#0A4F78] to-[#2A8FC2] text-white shadow-sm flex items-center gap-2">
                        <i class="fa-solid fa-capsules text-emerald-300"></i>
                        <span>{{ app()->getLocale() === 'ar' ? '1. ربط المنتج والبطاقة' : '1. Product & Floating Card' }}</span>
                    </button>
                    <button type="button" onclick="switchModalTab('tab-copy')" id="btn-tab-copy" class="modal-tab-btn flex-shrink-0 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-white/80 dark:hover:bg-slate-800/80 transition-all flex items-center gap-2">
                        <i class="fa-solid fa-heading text-sky-400"></i>
                        <span>{{ app()->getLocale() === 'ar' ? '2. العناوين والنصوص (EN / AR)' : '2. Titles & Copy (EN / AR)' }}</span>
                    </button>
                    <button type="button" onclick="switchModalTab('tab-cta')" id="btn-tab-cta" class="modal-tab-btn flex-shrink-0 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-white/80 dark:hover:bg-slate-800/80 transition-all flex items-center gap-2">
                        <i class="fa-solid fa-arrow-pointer text-amber-400"></i>
                        <span>{{ app()->getLocale() === 'ar' ? '3. أزرار الإجراء (CTAs)' : '3. Action Buttons' }}</span>
                    </button>
                    <button type="button" onclick="switchModalTab('tab-media')" id="btn-tab-media" class="modal-tab-btn flex-shrink-0 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-white/80 dark:hover:bg-slate-800/80 transition-all flex items-center gap-2">
                        <i class="fa-solid fa-image text-purple-400"></i>
                        <span>{{ app()->getLocale() === 'ar' ? '4. وسائط وخلفية الشريحة' : '4. Background Media' }}</span>
                    </button>
                </div>

                <!-- ========================================================================= -->
                <!-- TAB 1: PRODUCT & FLOATING CARD (WITH LIVE PREVIEW) -->
                <!-- ========================================================================= -->
                <div id="tab-product" class="modal-tab-content space-y-5">
                    
                    <!-- 1. Product Link Selector -->
                    <div class="p-4 sm:p-5 rounded-2xl bg-slate-50/80 dark:bg-slate-900/40 border border-slate-200 dark:border-slate-800 space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <label class="block text-xs font-black text-slate-900 dark:text-white">
                                    {{ app()->getLocale() === 'ar' ? 'اختر المنتج المراد ربطه بالشريحة' : 'Choose Connected Product Formulation' }}
                                </label>
                                <span class="text-[11px] text-slate-500 dark:text-slate-400 block mt-0.5">
                                    {{ app()->getLocale() === 'ar' ? 'اربط الشريحة بمنتج حقيقي لتعبئة بيانات البطاقة العائمة والتقييمات والرابط تلقائياً' : 'Link to a real product to auto-fill reviews, image, and link' }}
                                </span>
                            </div>

                            <button type="button" onclick="autoFillFromSelectedProduct()" class="btn btn-secondary btn-xs font-bold text-xs shadow-xs self-start sm:self-auto cursor-pointer hover:border-[#2A8FC2] hover:text-[#2A8FC2]">
                                <i class="fa-solid fa-bolt mr-1.5 ml-1.5 text-amber-500"></i>
                                {{ app()->getLocale() === 'ar' ? 'تعبئة البطاقة تلقائياً من المنتج' : 'Auto-fill Floating Card' }}
                            </button>
                        </div>

                        <select name="product_id" id="modal_product_id" class="form-select text-xs w-full bg-white dark:bg-slate-950 border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-xl focus:ring-2 focus:ring-[#2A8FC2]/20 focus:border-[#2A8FC2]">
                            <option value="">{{ app()->getLocale() === 'ar' ? '— بدون منتج (شريحة بؤرة إقليمية / تصميم مخصص) —' : '— No Product (Regional Hotspot / Custom Feature) —' }}</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}" 
                                        data-name-en="{{ $p->name_en }}" 
                                        data-name-ar="{{ $p->name_ar }}" 
                                        data-slug="{{ $p->slug }}" 
                                        data-rating="{{ $p->rating ?? 4.9 }}" 
                                        data-image="{{ \App\Models\Product::normalizeUrl($p->image) }}">
                                    {{ $p->name_en }} ({{ $p->name_ar }}) — [{{ $p->slug }}]
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 2. Floating Card Visualizer & Configuration Studio -->
                    <div class="p-4 sm:p-5 rounded-3xl bg-slate-50/70 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-800 space-y-4">
                        
                        <!-- Section Header with Master Toggle -->
                        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                            <div class="flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-xl bg-[#2A8FC2]/15 text-[#2A8FC2] flex items-center justify-center text-xs">
                                    <i class="fa-solid fa-window-restore"></i>
                                </span>
                                <div>
                                    <span class="text-xs font-black text-slate-900 dark:text-white block">
                                        {{ app()->getLocale() === 'ar' ? 'تخصيص البطاقة الزجاجية العائمة (Floating Glassmorphic Card)' : 'Floating Glassmorphic Product Card' }}
                                    </span>
                                    <span class="text-[10px] text-slate-500 dark:text-slate-400">
                                        {{ app()->getLocale() === 'ar' ? 'تظهر في الزاوية السفلية من الشريحة كما في واجهة المتجر' : 'Renders at the bottom-right of the hero slide on storefront' }}
                                    </span>
                                </div>
                            </div>

                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="show_floating_card" id="modal_show_floating_card" value="1" class="sr-only peer" checked onchange="updateLiveCardPreview()">
                                <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#2A8FC2]"></div>
                            </label>
                        </div>

                        <!-- LIVE STOREFRONT CARD VISUALIZER (Dark Cinematic Preview Box) -->
                        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#031827] via-[#062B49] to-[#0A4F78] p-4 sm:p-5 border border-[#2A8FC2]/30 shadow-xl" id="cardPreviewContainer">
                            <div class="flex items-center justify-between mb-3 text-xs">
                                <span class="text-[10px] font-mono font-bold tracking-wider uppercase text-sky-300 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                    {{ app()->getLocale() === 'ar' ? 'معاينة حية لشكل البطاقة في المتجر' : 'Storefront Live Simulation' }}
                                </span>

                                <!-- EN / AR Preview Switcher -->
                                <div class="inline-flex items-center p-0.5 rounded-lg bg-black/40 border border-white/10 text-[10px] font-bold">
                                    <button type="button" onclick="setPreviewLang('en')" id="btnPreviewLangEn" class="px-2 py-0.5 rounded-md bg-[#2A8FC2] text-white">EN</button>
                                    <button type="button" onclick="setPreviewLang('ar')" id="btnPreviewLangAr" class="px-2 py-0.5 rounded-md text-slate-400 hover:text-white">AR</button>
                                </div>
                            </div>

                            <!-- The Actual Glassmorphic Floating Card Replica -->
                            <div class="p-3 sm:p-3.5 rounded-2xl bg-white/10 dark:bg-black/30 backdrop-blur-md border border-white/20 shadow-2xl flex items-center gap-3.5 max-w-sm transition-all" id="glassmorphicCardWidget">
                                <div class="w-12 h-12 rounded-xl overflow-hidden bg-white/10 p-1 flex-shrink-0 flex items-center justify-center border border-white/15 shadow-inner">
                                    <img src="/assets/products/blue-mind.webp" id="preview_card_img" alt="Card Preview" class="w-full h-full object-contain" onerror="this.onerror=null; this.src='/assets/products/blue-mind.webp';">
                                </div>
                                <div class="min-w-0 flex-1">
                                    <span class="block text-[9px] font-mono font-bold text-[#67B34A] uppercase tracking-wider truncate" id="preview_card_badge">
                                        CENTENARIAN HOTSPOT
                                    </span>
                                    <span class="block text-sm font-black truncate text-white drop-shadow-sm" id="preview_card_title">
                                        OKINAWA, JAPAN
                                    </span>
                                    <span class="block text-[10px] text-[#2A8FC2] font-semibold truncate" id="preview_card_sub">
                                        ★ 4.9 Verified Reviews
                                    </span>
                                </div>
                                <span class="w-7 h-7 rounded-full bg-white/10 text-white/80 flex items-center justify-center text-xs flex-shrink-0">
                                    <i class="fa-solid fa-arrow-right -rotate-45 rtl:rotate-45 text-[10px]"></i>
                                </span>
                            </div>
                        </div>

                        <!-- Card Configuration Inputs (Clean, Adaptive for Dark and Light Mode) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 flex items-center justify-between">
                                    <span>{{ app()->getLocale() === 'ar' ? 'شارة البطاقة العليا (EN)' : 'Card Tag / Badge (EN)' }}</span>
                                    <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-sky-100 text-sky-800 dark:bg-sky-950 dark:text-sky-300 font-bold border border-sky-200 dark:border-sky-800">EN</span>
                                </label>
                                <input type="text" name="card_badge_en" id="modal_card_badge_en" class="form-control text-xs rounded-xl bg-white dark:bg-slate-950 border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#2A8FC2]/20 focus:border-[#2A8FC2]" placeholder="e.g. CENTENARIAN HOTSPOT" oninput="updateLiveCardPreview()">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 flex items-center justify-between" dir="rtl">
                                    <span>{{ app()->getLocale() === 'ar' ? 'شارة البطاقة بالعربية' : 'Card Tag / Badge (AR)' }}</span>
                                    <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 font-bold border border-emerald-200 dark:border-emerald-800">AR</span>
                                </label>
                                <input type="text" name="card_badge_ar" id="modal_card_badge_ar" class="form-control text-xs rounded-xl bg-white dark:bg-slate-950 border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#2A8FC2]/20 focus:border-[#2A8FC2]" placeholder="مثال: مصفوفة الأعصاب والنوم المريح" dir="rtl" oninput="updateLiveCardPreview()">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 flex items-center justify-between">
                                    <span>{{ app()->getLocale() === 'ar' ? 'عنوان البطاقة (EN)' : 'Card Title (EN)' }}</span>
                                    <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-sky-100 text-sky-800 dark:bg-sky-950 dark:text-sky-300 font-bold border border-sky-200 dark:border-sky-800">EN</span>
                                </label>
                                <input type="text" name="card_title_en" id="modal_card_title_en" class="form-control text-xs rounded-xl bg-white dark:bg-slate-950 border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#2A8FC2]/20 focus:border-[#2A8FC2]" placeholder="e.g. BLUE PEA B COMPLEX" oninput="updateLiveCardPreview()">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 flex items-center justify-between" dir="rtl">
                                    <span>{{ app()->getLocale() === 'ar' ? 'عنوان البطاقة بالعربية' : 'Card Title (AR)' }}</span>
                                    <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 font-bold border border-emerald-200 dark:border-emerald-800">AR</span>
                                </label>
                                <input type="text" name="card_title_ar" id="modal_card_title_ar" class="form-control text-xs rounded-xl bg-white dark:bg-slate-950 border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#2A8FC2]/20 focus:border-[#2A8FC2]" placeholder="مثال: بلو بي إيه ب كومبلكس" dir="rtl" oninput="updateLiveCardPreview()">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 flex items-center justify-between">
                                    <span>{{ app()->getLocale() === 'ar' ? 'النص الفرعي / التقييمات (EN)' : 'Card Subtitle / Meta (EN)' }}</span>
                                    <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-sky-100 text-sky-800 dark:bg-sky-950 dark:text-sky-300 font-bold border border-sky-200 dark:border-sky-800">EN</span>
                                </label>
                                <input type="text" name="card_subtitle_en" id="modal_card_subtitle_en" class="form-control text-xs rounded-xl bg-white dark:bg-slate-950 border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#2A8FC2]/20 focus:border-[#2A8FC2]" placeholder="e.g. ★ 4.9 Verified Reviews" oninput="updateLiveCardPreview()">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 flex items-center justify-between" dir="rtl">
                                    <span>{{ app()->getLocale() === 'ar' ? 'النص الفرعي بالعربية' : 'Card Subtitle / Meta (AR)' }}</span>
                                    <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 font-bold border border-emerald-200 dark:border-emerald-800">AR</span>
                                </label>
                                <input type="text" name="card_subtitle_ar" id="modal_card_subtitle_ar" class="form-control text-xs rounded-xl bg-white dark:bg-slate-950 border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#2A8FC2]/20 focus:border-[#2A8FC2]" placeholder="مثال: ★ 4.9 تقييم موثق" dir="rtl" oninput="updateLiveCardPreview()">
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                    {{ app()->getLocale() === 'ar' ? 'رابط النقر على البطاقة (Target URL / Anchor)' : 'Card Click Target URL' }}
                                </label>
                                <div class="relative">
                                    <i class="fa-solid fa-link absolute start-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                    <input type="text" name="card_link" id="modal_card_link" class="form-control text-xs rounded-xl ps-9 bg-white dark:bg-slate-950 border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-[#2A8FC2]/20 focus:border-[#2A8FC2]" placeholder="/product/blue_pea_b_complex">
                                </div>
                            </div>

                            <div class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-slate-200 dark:border-slate-800">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                        {{ app()->getLocale() === 'ar' ? 'رفع صورة مصغرة للبطاقة' : 'Upload Card Thumbnail Image' }}
                                    </label>
                                    <input type="file" name="card_image_file" class="form-control text-xs rounded-xl bg-white dark:bg-slate-950 border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-[#0A4F78]/10 file:text-[#0A4F78] dark:file:bg-[#2A8FC2]/20 dark:file:text-[#2A8FC2]" accept="image/*" onchange="previewUploadedCardImage(this)">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                        {{ app()->getLocale() === 'ar' ? 'أو مسار / رابط الصورة' : 'Or Card Image URL / Path' }}
                                    </label>
                                    <input type="text" name="card_image_url" id="modal_card_image_url" class="form-control text-xs rounded-xl bg-white dark:bg-slate-950 border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100" placeholder="/assets/products/blue-mind.webp" oninput="updateLiveCardPreview()">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ========================================================================= -->
                <!-- TAB 2: TITLES & COPY (BILINGUAL EN / AR) -->
                <!-- ========================================================================= -->
                <div id="tab-copy" class="modal-tab-content space-y-4 hidden">
                    
                    <!-- Eyebrow Badge -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 flex items-center justify-between">
                                <span>{{ app()->getLocale() === 'ar' ? 'شارة الشريحة العليا (EN)' : 'Top Eyebrow Badge (EN)' }}</span>
                                <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-sky-100 text-sky-800 dark:bg-sky-950 dark:text-sky-300 font-bold border border-sky-200 dark:border-sky-800">EN</span>
                            </label>
                            <input type="text" name="badge_en" id="modal_badge_en" class="form-control text-xs rounded-xl bg-white dark:bg-slate-950 border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100" placeholder="e.g. GLOBAL BLUE ZONES" oninput="updateLiveHeadlinePreview()">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 flex items-center justify-between" dir="rtl">
                                <span>{{ app()->getLocale() === 'ar' ? 'شارة الشريحة العليا (AR)' : 'Top Eyebrow Badge (AR)' }}</span>
                                <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 font-bold border border-emerald-200 dark:border-emerald-800">AR</span>
                            </label>
                            <input type="text" name="badge_ar" id="modal_badge_ar" class="form-control text-xs rounded-xl bg-white dark:bg-slate-950 border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100" placeholder="مثال: المناطق الزرقاء العالمية" dir="rtl" oninput="updateLiveHeadlinePreview()">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                {{ app()->getLocale() === 'ar' ? 'لون الشارة التمييزي' : 'Badge Color Accent' }}
                            </label>
                            <select name="badge_color" id="modal_badge_color" class="form-select text-xs rounded-xl bg-white dark:bg-slate-950 border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100" onchange="updateLiveHeadlinePreview()">
                                <option value="emerald">Emerald Green (#67B34A)</option>
                                <option value="cyan">Cyan Blue (#2A8FC2)</option>
                                <option value="amber">Amber Gold (#F59E0B)</option>
                                <option value="rose">Rose Red (#F43F5E)</option>
                                <option value="purple">Royal Purple (#A855F7)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Headlines -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 flex items-center justify-between">
                                <span>{{ app()->getLocale() === 'ar' ? 'العنوان الرئيسي الكامل (EN) *' : 'Main Headline (EN) *' }}</span>
                                <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-sky-100 text-sky-800 dark:bg-sky-950 dark:text-sky-300 font-bold border border-sky-200 dark:border-sky-800">EN</span>
                            </label>
                            <input type="text" name="title_en" id="modal_title_en" class="form-control text-xs rounded-xl bg-white dark:bg-slate-950 border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100" required placeholder="e.g. INSPIRED BY THE 5 BLUE ZONES." oninput="updateLiveHeadlinePreview()">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 flex items-center justify-between" dir="rtl">
                                <span>{{ app()->getLocale() === 'ar' ? 'العنوان الرئيسي الكامل (AR) *' : 'Main Headline (AR) *' }}</span>
                                <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 font-bold border border-emerald-200 dark:border-emerald-800">AR</span>
                            </label>
                            <input type="text" name="title_ar" id="modal_title_ar" class="form-control text-xs rounded-xl bg-white dark:bg-slate-950 border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100" required placeholder="مثال: مستوحاة من أقاليم المناطق الزرقاء الخمس." dir="rtl" oninput="updateLiveHeadlinePreview()">
                        </div>
                    </div>

                    <!-- Highlight Word & Color -->
                    <div class="p-4 sm:p-5 rounded-2xl bg-slate-50/80 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 space-y-3">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-md bg-amber-500/15 text-amber-500 flex items-center justify-center text-xs">
                                <i class="fa-solid fa-highlighter"></i>
                            </span>
                            <span class="text-xs font-extrabold text-slate-900 dark:text-white">
                                {{ app()->getLocale() === 'ar' ? 'تمييز كلمة أو عبارة بلون مخصص' : 'Highlighted Accent Word / Phrase' }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">
                                    {{ app()->getLocale() === 'ar' ? 'الكلمة الملونة (EN)' : 'Word to Highlight (EN)' }}
                                </label>
                                <input type="text" name="title_highlight_en" id="modal_title_highlight_en" class="form-control text-xs rounded-xl bg-white dark:bg-slate-950 border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100" placeholder="e.g. 5 BLUE ZONES." oninput="updateLiveHeadlinePreview()">
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1" dir="rtl">
                                    {{ app()->getLocale() === 'ar' ? 'الكلمة الملونة (AR)' : 'Word to Highlight (AR)' }}
                                </label>
                                <input type="text" name="title_highlight_ar" id="modal_title_highlight_ar" class="form-control text-xs rounded-xl bg-white dark:bg-slate-950 border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100" placeholder="مثال: المناطق الزرقاء الخمس." dir="rtl" oninput="updateLiveHeadlinePreview()">
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 dark:text-slate-400 mb-1">
                                    {{ app()->getLocale() === 'ar' ? 'لون التمييز المخصص' : 'Highlight Hex Color' }}
                                </label>
                                <div class="flex items-center gap-2">
                                    <input type="color" name="title_highlight_color" id="modal_title_highlight_color" value="#2A8FC2" class="w-10 h-8 rounded-lg border border-slate-300 dark:border-slate-700 p-0.5 cursor-pointer flex-shrink-0" onchange="updateLiveHeadlinePreview()">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <button type="button" onclick="setHighlightColor('#2A8FC2')" class="w-5 h-5 rounded-full bg-[#2A8FC2] border border-white/40 shadow-xs cursor-pointer hover:scale-110 transition-transform" title="Cyan"></button>
                                        <button type="button" onclick="setHighlightColor('#67B34A')" class="w-5 h-5 rounded-full bg-[#67B34A] border border-white/40 shadow-xs cursor-pointer hover:scale-110 transition-transform" title="Emerald"></button>
                                        <button type="button" onclick="setHighlightColor('#E8DCC4')" class="w-5 h-5 rounded-full bg-[#E8DCC4] border border-white/40 shadow-xs cursor-pointer hover:scale-110 transition-transform" title="Warm Sand"></button>
                                        <button type="button" onclick="setHighlightColor('#F59E0B')" class="w-5 h-5 rounded-full bg-[#F59E0B] border border-white/40 shadow-xs cursor-pointer hover:scale-110 transition-transform" title="Amber"></button>
                                        <button type="button" onclick="setHighlightColor('#F43F5E')" class="w-5 h-5 rounded-full bg-[#F43F5E] border border-white/40 shadow-xs cursor-pointer hover:scale-110 transition-transform" title="Rose"></button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- LIVE HEADLINE PREVIEW -->
                        <div class="p-3.5 rounded-xl bg-slate-900 text-white border border-slate-700/80 mt-2">
                            <span class="text-[9px] font-mono text-slate-400 block mb-1 uppercase tracking-wider">{{ app()->getLocale() === 'ar' ? 'معاينة العنوان المباشر' : 'Live Headline Preview' }}</span>
                            <div class="text-sm sm:text-base font-black tracking-tight" id="preview_headline_en">
                                INSPIRED BY THE <span style="color: #2A8FC2;">5 BLUE ZONES.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Subtitles / Description -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 flex items-center justify-between">
                                <span>{{ app()->getLocale() === 'ar' ? 'الوصف الفرعي (EN)' : 'Subtitle / Description (EN)' }}</span>
                                <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-sky-100 text-sky-800 dark:bg-sky-950 dark:text-sky-300 font-bold border border-sky-200 dark:border-sky-800">EN</span>
                            </label>
                            <textarea name="subtitle_en" id="modal_subtitle_en" rows="3" class="form-control text-xs rounded-xl bg-white dark:bg-slate-950 border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100" placeholder="Unlocking the longevity principles observed across Okinawa..."></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 flex items-center justify-between" dir="rtl">
                                <span>{{ app()->getLocale() === 'ar' ? 'الوصف الفرعي (AR)' : 'Subtitle / Description (AR)' }}</span>
                                <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 font-bold border border-emerald-200 dark:border-emerald-800">AR</span>
                            </label>
                            <textarea name="subtitle_ar" id="modal_subtitle_ar" rows="3" class="form-control text-xs rounded-xl bg-white dark:bg-slate-950 border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100" placeholder="استكشاف أسرار طول العمر المعمرة في أوكيناوا..." dir="rtl"></textarea>
                        </div>
                    </div>
                </div>

                <!-- ========================================================================= -->
                <!-- TAB 3: DUAL CTAs (ACTION BUTTONS) -->
                <!-- ========================================================================= -->
                <div id="tab-cta" class="modal-tab-content space-y-4 hidden">
                    <!-- Primary CTA -->
                    <div class="p-4 sm:p-5 rounded-2xl bg-emerald-50/60 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-800 space-y-3.5">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-md bg-emerald-600 text-white flex items-center justify-center text-xs font-black">1</span>
                            <span class="text-xs font-extrabold text-emerald-900 dark:text-emerald-300">
                                {{ app()->getLocale() === 'ar' ? 'زر الإجراء الرئيسي (Primary Action Button)' : 'Primary Action Button' }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Text (EN)</label>
                                <input type="text" name="primary_cta_text_en" id="modal_primary_cta_text_en" class="form-control text-xs rounded-xl bg-white dark:bg-slate-950 border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100" placeholder="e.g. EXPLORE REGIONS">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1" dir="rtl">Text (AR)</label>
                                <input type="text" name="primary_cta_text_ar" id="modal_primary_cta_text_ar" class="form-control text-xs rounded-xl bg-white dark:bg-slate-950 border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100" placeholder="مثال: استكشف الأقاليم" dir="rtl">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Target Link URL</label>
                                <input type="text" name="primary_cta_link" id="modal_primary_cta_link" class="form-control text-xs rounded-xl bg-white dark:bg-slate-950 border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100" placeholder="e.g. #five-blue-zones or /products">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Button Visual Style</label>
                            <select name="primary_cta_style" id="modal_primary_cta_style" class="form-select text-xs rounded-xl bg-white dark:bg-slate-950 border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100 w-full sm:w-60">
                                <option value="emerald">Emerald Green Solid (#67B34A)</option>
                                <option value="cyan">Cyan Blue Solid (#2A8FC2)</option>
                                <option value="white">Luxury White Solid</option>
                            </select>
                        </div>
                    </div>

                    <!-- Secondary CTA -->
                    <div class="p-4 sm:p-5 rounded-2xl bg-slate-50/80 dark:bg-slate-900/40 border border-slate-200 dark:border-slate-800 space-y-3.5">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-md bg-slate-400 text-white flex items-center justify-center text-xs font-black">2</span>
                            <span class="text-xs font-extrabold text-slate-900 dark:text-slate-200">
                                {{ app()->getLocale() === 'ar' ? 'زر الإجراء الثانوي (Secondary Button)' : 'Secondary Action Button' }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Text (EN)</label>
                                <input type="text" name="secondary_cta_text_en" id="modal_secondary_cta_text_en" class="form-control text-xs rounded-xl bg-white dark:bg-slate-950 border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100" placeholder="e.g. SHOP CATALOG">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1" dir="rtl">Text (AR)</label>
                                <input type="text" name="secondary_cta_text_ar" id="modal_secondary_cta_text_ar" class="form-control text-xs rounded-xl bg-white dark:bg-slate-950 border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100" placeholder="مثال: تسوق الكتالوج" dir="rtl">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">Target Link URL</label>
                                <input type="text" name="secondary_cta_link" id="modal_secondary_cta_link" class="form-control text-xs rounded-xl bg-white dark:bg-slate-950 border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100" placeholder="e.g. /products">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ========================================================================= -->
                <!-- TAB 4: MEDIA & BACKGROUND (HD PRESETS & UPLOADS) -->
                <!-- ========================================================================= -->
                <div id="tab-media" class="modal-tab-content space-y-4 hidden">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">
                            {{ app()->getLocale() === 'ar' ? 'اختر خلفية سينمائية جاهزة (Curated Brand Presets)' : 'Select High-Resolution Cinematic Preset' }}
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                            @foreach([1, 2, 3, 4, 5] as $presetNum)
                                @php $presetPath = "assets/images/hero/hero-0{$presetNum}.webp"; @endphp
                                <label class="cursor-pointer border-2 rounded-2xl overflow-hidden block relative border-transparent hover:border-[#2A8FC2] transition-all has-[:checked]:border-[#2A8FC2] has-[:checked]:ring-2 has-[:checked]:ring-[#2A8FC2] shadow-xs group">
                                    <input type="radio" name="bg_image_preset" value="{{ $presetPath }}" class="sr-only">
                                    <img src="{{ asset($presetPath) }}" alt="Hero {{ $presetNum }}" class="w-full h-20 object-cover filter brightness-90 group-hover:scale-105 transition-transform" onerror="this.onerror=null; this.src='{{ asset('assets/images/hero/hero-01.webp') }}';">
                                    <span class="absolute bottom-1.5 start-1.5 px-2 py-0.5 rounded-md bg-black/70 backdrop-blur-xs text-[10px] font-black text-white">Preset #{{ $presetNum }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-3 border-t border-slate-200 dark:border-slate-800">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                {{ app()->getLocale() === 'ar' ? 'أو ارفع صورة خلفية جديدة (HD 1920x1080)' : 'Or Upload Custom High-Res Image' }}
                            </label>
                            <input type="file" name="bg_image_file" class="form-control text-xs rounded-xl bg-white dark:bg-slate-950 border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-[#0A4F78]/10 file:text-[#0A4F78]" accept="image/*">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                {{ app()->getLocale() === 'ar' ? 'أو مسار / رابط صورة مباشر' : 'Or Direct Image URL / Path' }}
                            </label>
                            <input type="text" name="bg_image_url" id="modal_bg_image_url" class="form-control text-xs rounded-xl bg-white dark:bg-slate-950 border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100" placeholder="assets/images/hero/hero-01.webp">
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50/80 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-slate-900 dark:text-white block">{{ app()->getLocale() === 'ar' ? 'تفعيل الشريحة مباشرة' : 'Active Status' }}</span>
                            <span class="text-[11px] text-slate-500 dark:text-slate-400">{{ app()->getLocale() === 'ar' ? 'عرض هذه الشريحة في السلايدر مباشرة بعد الحفظ' : 'Display this slide immediately in live carousel' }}</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" id="modal_is_active" value="1" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        </label>
                    </div>
                </div>

                <!-- Modal Sticky Footer (Backdrop Blur & Clear Actions) -->
                <div class="sticky bottom-0 -mx-4 -mb-4 sm:-mx-6 sm:-mb-6 p-4 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between bg-white/95 dark:bg-[#031827]/95 backdrop-blur-md rounded-b-3xl">
                    <span class="text-xs text-slate-400 hidden sm:inline-block">
                        <i class="fa-solid fa-sparkles text-amber-500 mr-1 ml-1"></i>
                        {{ app()->getLocale() === 'ar' ? 'التغييرات تنعكس مباشرة على الواجهة' : 'All updates sync directly to customer storefront' }}
                    </span>

                    <div class="flex items-center gap-2.5 ms-auto">
                        <button type="button" onclick="closeSlideModal()" class="btn btn-secondary text-xs font-bold px-4 py-2 cursor-pointer">
                            {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                        </button>
                        <button type="submit" class="btn btn-primary text-xs font-bold shadow-md shadow-[#0A4F78]/25 px-6 py-2 cursor-pointer flex items-center gap-1.5">
                            <i class="fa-solid fa-floppy-disk"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'حفظ شريحة السلايدر' : 'Save Carousel Slide' }}</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- INTERACTIVE SCRIPTS: LIVE REACTIVE PREVIEWS & AJAX TOGGLES -->
    <!-- ========================================================================= -->
    <script>
        let currentPreviewLang = 'en';

        function switchModalTab(tabId) {
            document.querySelectorAll('.modal-tab-content').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.modal-tab-btn').forEach(btn => {
                btn.className = "modal-tab-btn flex-shrink-0 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-white/80 dark:hover:bg-slate-800/80 transition-all flex items-center gap-2";
            });

            const content = document.getElementById(tabId);
            const btn = document.getElementById('btn-' + tabId);
            if (content) content.classList.remove('hidden');
            if (btn) {
                btn.className = "modal-tab-btn flex-shrink-0 px-3.5 py-2 rounded-xl text-xs font-black transition-all bg-gradient-to-r from-[#0A4F78] to-[#2A8FC2] text-white shadow-sm flex items-center gap-2";
            }
        }

        function setPreviewLang(lang) {
            currentPreviewLang = lang;
            const btnEn = document.getElementById('btnPreviewLangEn');
            const btnAr = document.getElementById('btnPreviewLangAr');

            if (lang === 'ar') {
                btnAr.className = "px-2 py-0.5 rounded-md bg-[#2A8FC2] text-white";
                btnEn.className = "px-2 py-0.5 rounded-md text-slate-400 hover:text-white";
                document.getElementById('glassmorphicCardWidget').setAttribute('dir', 'rtl');
            } else {
                btnEn.className = "px-2 py-0.5 rounded-md bg-[#2A8FC2] text-white";
                btnAr.className = "px-2 py-0.5 rounded-md text-slate-400 hover:text-white";
                document.getElementById('glassmorphicCardWidget').setAttribute('dir', 'ltr');
            }
            updateLiveCardPreview();
        }

        function openSlideModal(isEdit = false) {
            const modal = document.getElementById('slideModal');
            const form = document.getElementById('slideForm');
            const title = document.getElementById('modalTitle');
            const methodContainer = document.getElementById('methodContainer');
            const badge = document.getElementById('modalSlideBadge');

            switchModalTab('tab-product');

            if (!isEdit) {
                form.reset();
                form.action = "{{ route('admin.hero-slides.store') }}";
                methodContainer.innerHTML = '';
                document.getElementById('slide_id').value = '';
                badge.classList.add('hidden');
                title.textContent = "{{ app()->getLocale() === 'ar' ? 'إضافة شريحة عرض جديدة' : 'Add New Carousel Slide' }}";
                updateLiveCardPreview();
                updateLiveHeadlinePreview();
            }

            modal.classList.remove('hidden');
        }

        function closeSlideModal() {
            document.getElementById('slideModal').classList.add('hidden');
        }

        function setHighlightColor(color) {
            document.getElementById('modal_title_highlight_color').value = color;
            updateLiveHeadlinePreview();
        }

        function resolveCardImageUrl(url) {
            if (!url || typeof url !== 'string') return '/assets/products/blue-mind.webp';
            let trimmed = url.trim();
            if (!trimmed) return '/assets/products/blue-mind.webp';
            if (trimmed.startsWith('http://') || trimmed.startsWith('https://') || trimmed.startsWith('data:')) {
                return trimmed;
            }
            if (trimmed.startsWith('/')) {
                return trimmed;
            }
            if (trimmed.startsWith('assets/') || trimmed.startsWith('storage/') || trimmed.startsWith('uploads/')) {
                return '/' + trimmed;
            }
            return '/assets/products/' + trimmed;
        }

        function updateLiveCardPreview() {
            const isAr = (currentPreviewLang === 'ar');
            const badgeInput = isAr ? document.getElementById('modal_card_badge_ar') : document.getElementById('modal_card_badge_en');
            const titleInput = isAr ? document.getElementById('modal_card_title_ar') : document.getElementById('modal_card_title_en');
            const subInput   = isAr ? document.getElementById('modal_card_subtitle_ar') : document.getElementById('modal_card_subtitle_en');

            const badgeVal = (badgeInput && badgeInput.value) ? badgeInput.value : (isAr ? 'بؤرة المعمرين الفائقة' : 'CENTENARIAN HOTSPOT');
            const titleVal = (titleInput && titleInput.value) ? titleInput.value : (isAr ? 'أوكيناوا، اليابان' : 'OKINAWA, JAPAN');
            const subVal   = (subInput && subInput.value) ? subInput.value : (isAr ? '★ 4.9 تقييم موثق' : '★ 4.9 Verified Reviews');
            const imgVal   = document.getElementById('modal_card_image_url').value;
            const isVisible = document.getElementById('modal_show_floating_card').checked;

            const cardEl = document.getElementById('glassmorphicCardWidget');
            if (cardEl) {
                cardEl.style.opacity = isVisible ? '1' : '0.3';
            }

            const pBadge = document.getElementById('preview_card_badge');
            const pTitle = document.getElementById('preview_card_title');
            const pSub   = document.getElementById('preview_card_sub');

            if (pBadge) pBadge.textContent = badgeVal;
            if (pTitle) pTitle.textContent = titleVal;
            if (pSub)   pSub.textContent = subVal;

            const pImg = document.getElementById('preview_card_img');
            if (pImg) {
                pImg.src = resolveCardImageUrl(imgVal);
            }
        }

        function updateLiveHeadlinePreview() {
            const title = document.getElementById('modal_title_en').value || 'INSPIRED BY THE 5 BLUE ZONES.';
            const highlight = document.getElementById('modal_title_highlight_en').value || '';
            const color = document.getElementById('modal_title_highlight_color').value || '#2A8FC2';

            const previewEl = document.getElementById('preview_headline_en');
            if (!previewEl) return;

            if (highlight && title.includes(highlight)) {
                const parts = title.split(highlight);
                previewEl.innerHTML = `${escapeHtml(parts[0])}<span style="color: ${color};">${escapeHtml(highlight)}</span>${escapeHtml(parts.slice(1).join(highlight))}`;
            } else {
                previewEl.textContent = title;
            }
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function previewUploadedCardImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const pImg = document.getElementById('preview_card_img');
                    if (pImg) pImg.src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        async function editSlide(slideId) {
            try {
                const response = await fetch(`{{ url('admin/hero-slider') }}/${slideId}`);
                const data = await response.json();

                if (data.success && data.slide) {
                    const s = data.slide;
                    openSlideModal(true);

                    const form = document.getElementById('slideForm');
                    form.action = `{{ url('admin/hero-slider') }}/${slideId}`;
                    document.getElementById('methodContainer').innerHTML = '<input type="hidden" name="_method" value="PUT">';
                    document.getElementById('slide_id').value = s.id;

                    const badge = document.getElementById('modalSlideBadge');
                    badge.textContent = `#${s.slide_order}`;
                    badge.classList.remove('hidden');

                    document.getElementById('modalTitle').textContent = `{{ app()->getLocale() === 'ar' ? 'تعديل الشريحة: ' : 'Edit Carousel Slide: ' }}${s.title_en}`;

                    // Populate Tab 1: Product & Floating Card
                    document.getElementById('modal_product_id').value = s.product_id || '';
                    document.getElementById('modal_show_floating_card').checked = !!s.show_floating_card;
                    document.getElementById('modal_card_badge_en').value = s.card_badge_en || '';
                    document.getElementById('modal_card_badge_ar').value = s.card_badge_ar || '';
                    document.getElementById('modal_card_title_en').value = s.card_title_en || '';
                    document.getElementById('modal_card_title_ar').value = s.card_title_ar || '';
                    document.getElementById('modal_card_subtitle_en').value = s.card_subtitle_en || '';
                    document.getElementById('modal_card_subtitle_ar').value = s.card_subtitle_ar || '';
                    document.getElementById('modal_card_link').value = s.card_link || '';
                    document.getElementById('modal_card_image_url').value = s.display_card_image_url || s.card_image || '';

                    // Populate Tab 2: Titles & Copy
                    document.getElementById('modal_badge_en').value = s.badge_en || '';
                    document.getElementById('modal_badge_ar').value = s.badge_ar || '';
                    document.getElementById('modal_badge_color').value = s.badge_color || 'emerald';
                    document.getElementById('modal_title_en').value = s.title_en || '';
                    document.getElementById('modal_title_ar').value = s.title_ar || '';
                    document.getElementById('modal_title_highlight_en').value = s.title_highlight_en || '';
                    document.getElementById('modal_title_highlight_ar').value = s.title_highlight_ar || '';
                    document.getElementById('modal_title_highlight_color').value = s.title_highlight_color || '#67B34A';
                    document.getElementById('modal_subtitle_en').value = s.subtitle_en || '';
                    document.getElementById('modal_subtitle_ar').value = s.subtitle_ar || '';

                    // Populate Tab 3: Dual CTAs
                    document.getElementById('modal_primary_cta_text_en').value = s.primary_cta_text_en || '';
                    document.getElementById('modal_primary_cta_text_ar').value = s.primary_cta_text_ar || '';
                    document.getElementById('modal_primary_cta_link').value = s.primary_cta_link || '';
                    document.getElementById('modal_primary_cta_style').value = s.primary_cta_style || 'emerald';
                    document.getElementById('modal_secondary_cta_text_en').value = s.secondary_cta_text_en || '';
                    document.getElementById('modal_secondary_cta_text_ar').value = s.secondary_cta_text_ar || '';
                    document.getElementById('modal_secondary_cta_link').value = s.secondary_cta_link || '';

                    // Populate Tab 4: Media
                    document.getElementById('modal_bg_image_url').value = s.bg_image || '';
                    document.getElementById('modal_is_active').checked = !!s.is_active;

                    // Preset radio check
                    document.querySelectorAll('input[name="bg_image_preset"]').forEach(radio => {
                        radio.checked = (radio.value === s.bg_image);
                    });

                    updateLiveCardPreview();
                    updateLiveHeadlinePreview();
                }
            } catch (err) {
                console.error(err);
                alert('Could not load slide data');
            }
        }

        function autoFillFromSelectedProduct() {
            const select = document.getElementById('modal_product_id');
            const opt = select.options[select.selectedIndex];
            if (!opt || !opt.value) {
                alert("{{ app()->getLocale() === 'ar' ? 'الرجاء اختيار منتج أولاً من القائمة' : 'Please select a product formulation from the dropdown first' }}");
                return;
            }

            const nameEn = opt.getAttribute('data-name-en');
            const nameAr = opt.getAttribute('data-name-ar');
            const slug = opt.getAttribute('data-slug');
            const rating = opt.getAttribute('data-rating') || '4.9';
            const img = opt.getAttribute('data-image');

            document.getElementById('modal_show_floating_card').checked = true;
            document.getElementById('modal_card_badge_en').value = 'FLAGSHIP FORMULATION';
            document.getElementById('modal_card_badge_ar').value = 'تركيبة خلوية رائدة';
            document.getElementById('modal_card_title_en').value = nameEn;
            document.getElementById('modal_card_title_ar').value = nameAr || nameEn;
            document.getElementById('modal_card_subtitle_en').value = `★ ${rating} Verified Reviews`;
            document.getElementById('modal_card_subtitle_ar').value = `★ ${rating} تقييم سريري معتمد`;
            document.getElementById('modal_card_link').value = `/product/${slug}`;
            if (img) {
                document.getElementById('modal_card_image_url').value = resolveCardImageUrl(img);
            }

            // Also set primary CTA if empty
            if (!document.getElementById('modal_primary_cta_text_en').value) {
                document.getElementById('modal_primary_cta_text_en').value = `SHOP ${nameEn.toUpperCase()}`;
                document.getElementById('modal_primary_cta_text_ar').value = `تسوق ${nameAr || nameEn}`;
                document.getElementById('modal_primary_cta_link').value = `/product/${slug}`;
            }

            updateLiveCardPreview();
        }

        async function toggleSlideStatus(button, slideId) {
            const currentActive = button.getAttribute('data-active') === '1';
            button.disabled = true;
            button.style.opacity = '0.7';

            try {
                const response = await fetch(`{{ url('admin/hero-slider') }}/${slideId}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    const newActive = data.is_active;
                    button.setAttribute('data-active', newActive ? '1' : '0');

                    const isAr = {{ app()->getLocale() === 'ar' ? 'true' : 'false' }};
                    const label = newActive ? (isAr ? 'معروض' : 'Active') : (isAr ? 'مخفي' : 'Hidden');

                    if (newActive) {
                        button.className = "slide-toggle-btn inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold transition-all shadow-xs border cursor-pointer bg-emerald-50 text-emerald-700 border-emerald-300 hover:bg-emerald-100 dark:bg-emerald-950/50 dark:text-emerald-300 dark:border-emerald-800";
                        button.innerHTML = `
                            <span class="w-2 h-2 rounded-full bg-emerald-500 ring-2 ring-emerald-300/50 indicator-dot"></span>
                            <span class="indicator-text">${label}</span>
                        `;
                    } else {
                        button.className = "slide-toggle-btn inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold transition-all shadow-xs border cursor-pointer bg-slate-100 text-slate-500 border-slate-200 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700";
                        button.innerHTML = `
                            <span class="w-2 h-2 rounded-full bg-slate-400 indicator-dot"></span>
                            <span class="indicator-text">${label}</span>
                        `;
                    }

                    // Update metric counter
                    const metric = document.getElementById('activeSlidesMetric');
                    if (metric) {
                        let cur = parseInt(metric.textContent.trim(), 10) || 0;
                        metric.textContent = newActive ? (cur + 1) : Math.max(0, cur - 1);
                    }
                } else {
                    alert(data.message || 'Error toggling slide status');
                }
            } catch (err) {
                console.error(err);
                alert('Connection error');
            } finally {
                button.disabled = false;
                button.style.opacity = '1';
            }
        }
    </script>
</x-layouts.admin>
