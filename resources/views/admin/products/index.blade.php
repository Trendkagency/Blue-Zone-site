<x-layouts.admin 
    :pageTitle="__('admin.menu.products')" 
    pageSubtitle="Manage clinical formulations, batch metadata, pricing, and variant availability."
    :breadcrumbs="['Products' => route('admin.products.index')]"
>
    <x-slot name="actions">
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary font-bold shadow-sm">
            <i class="fa-solid fa-plus mr-1.5 ml-1.5"></i> {{ __('admin.menu.add_product') }}
        </a>
    </x-slot>

    <!-- Global Landing Page Products Section Configuration Card -->
    <div class="mb-6 rounded-2xl border border-[#0A4F78]/20 dark:border-[#2A8FC2]/25 bg-gradient-to-br from-white via-white to-slate-50 dark:from-[#062B49] dark:via-[#031827] dark:to-[#02131F] p-5 sm:p-6 shadow-md transition-all">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-4 border-b border-[#0A4F78]/10 dark:border-white/10">
            <div class="flex items-start sm:items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-[#0A4F78]/10 dark:bg-[#2A8FC2]/20 text-[#0A4F78] dark:text-[#2A8FC2] flex items-center justify-center text-xl shrink-0 shadow-xs">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h3 class="text-base sm:text-lg font-black text-gray-900 dark:text-white tracking-tight">
                            {{ app()->getLocale() === 'ar' ? 'إعدادات قسم المنتجات بالصفحة الرئيسية' : 'Landing Page Products Showcase Control' }}
                        </h3>
                        @if(!empty($landingSettings['landing_featured_products_enabled']))
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-700">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                {{ app()->getLocale() === 'ar' ? 'القسم معروض (OPEN)' : 'Section is OPEN' }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-black bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-300 dark:border-amber-700">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                {{ app()->getLocale() === 'ar' ? 'القسم مخفي (HIDDEN)' : 'Section is HIDDEN' }}
                            </span>
                        @endif

                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#0A4F78]/10 text-[#0A4F78] dark:bg-[#2A8FC2]/15 dark:text-[#2A8FC2]">
                            <i class="fa-solid fa-circle-check text-[10px]"></i>
                            {{ $landingProductsCount ?? 0 }} {{ app()->getLocale() === 'ar' ? 'منتج معروض في الواجهة' : 'Formulations in Landing' }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 font-medium">
                        {{ app()->getLocale() === 'ar' 
                            ? 'تحكّم في فتح أو إغلاق قسم المنتجات بالواجهة، تخصيص العناوين ثنائية اللغة، وتحديد عدد المنتجات المعروضة (أو عرض الكل).' 
                            : 'Open or hide the products section on the storefront homepage, customize bilingual headlines, and control product limits.' }}
                    </p>
                </div>
            </div>

            <!-- Header Quick Controls -->
            <div class="flex items-center gap-2.5 self-start lg:self-center shrink-0">
                <form method="POST" action="{{ route('admin.products.landing-section-settings') }}" class="inline-flex items-center gap-2">
                    @csrf
                    <input type="hidden" name="landing_featured_products_enabled" value="{{ !empty($landingSettings['landing_featured_products_enabled']) ? '0' : '1' }}">
                    <button type="submit" 
                            class="btn btn-sm font-bold shadow-xs cursor-pointer {{ !empty($landingSettings['landing_featured_products_enabled']) ? 'bg-amber-600 hover:bg-amber-700 text-white border-transparent' : 'bg-emerald-600 hover:bg-emerald-700 text-white border-transparent' }}">
                        <i class="fa-solid {{ !empty($landingSettings['landing_featured_products_enabled']) ? 'fa-eye-slash' : 'fa-eye' }} mr-1 ml-1"></i>
                        {{ !empty($landingSettings['landing_featured_products_enabled']) 
                            ? (app()->getLocale() === 'ar' ? 'إخفاء القسم من الواجهة' : 'Hide Section on Landing') 
                            : (app()->getLocale() === 'ar' ? 'إظهار القسم في الواجهة' : 'Open Section on Landing') }}
                    </button>
                </form>

                <button type="button" 
                        onclick="toggleLandingSettingsCollapse()" 
                        class="btn btn-sm btn-outline font-bold dark:border-gray-600 dark:text-gray-200"
                        id="toggleLandingSettingsBtn">
                    <i class="fa-solid fa-sliders mr-1 ml-1"></i>
                    <span id="toggleLandingSettingsBtnText">{{ app()->getLocale() === 'ar' ? 'تخصيص العناوين والمحتوى' : 'Customize Headlines & Settings' }}</span>
                    <i class="fa-solid fa-chevron-down text-xs ml-1 mr-1 transition-transform" id="toggleLandingSettingsIcon"></i>
                </button>

                <a href="{{ route('admin.hero-slides.index') }}" 
                   class="btn btn-sm font-bold bg-[#0A4F78]/10 hover:bg-[#0A4F78]/20 text-[#0A4F78] dark:text-[#2A8FC2] dark:bg-white/10 dark:hover:bg-white/15 border border-[#0A4F78]/20 dark:border-white/20">
                    <i class="fa-solid fa-images mr-1 ml-1 text-sky-500"></i>
                    {{ app()->getLocale() === 'ar' ? 'إدارة سلايدر الواجهة والشرائح' : 'Hero Carousel Manager' }}
                </a>
            </div>
        </div>

        <!-- Collapsible Content & Titles Customization Form -->
        <div id="landingSettingsCollapseBody" class="mt-5 pt-4 border-t border-dashed border-[#0A4F78]/15 dark:border-white/10 hidden">
            <form method="POST" action="{{ route('admin.products.landing-section-settings') }}" id="landingSectionGlobalForm" class="space-y-4">
                @csrf

                <!-- Section Visibility Switch & Display Mode -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 rounded-xl bg-slate-50 dark:bg-slate-900/40 border border-gray-200/80 dark:border-gray-800">
                    <div class="flex items-center justify-between p-3 rounded-lg bg-white dark:bg-[#031827] border border-gray-200 dark:border-gray-700">
                        <div>
                            <span class="text-xs font-bold text-gray-800 dark:text-gray-200 block">{{ app()->getLocale() === 'ar' ? 'حالة ظهور القسم في الصفحة الرئيسية' : 'Homepage Section Status' }}</span>
                            <span class="text-[11px] text-gray-500 dark:text-gray-400">{{ app()->getLocale() === 'ar' ? 'إظهار (Open) أو إخفاء (Hidden)' : 'Open (Visible) or Hidden (Closed)' }}</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="landing_featured_products_enabled" value="1" class="sr-only peer" {{ !empty($landingSettings['landing_featured_products_enabled']) ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        </label>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                            {{ app()->getLocale() === 'ar' ? 'الحد الأقصى للمنتجات المعروضة' : 'Products Showcase Limit' }}
                        </label>
                        <select name="landing_products_limit" class="form-select text-xs w-full">
                            <option value="0" {{ ($landingSettings['landing_products_limit'] ?? 6) == 0 ? 'selected' : '' }}>
                                {{ app()->getLocale() === 'ar' ? 'عرض جميع المنتجات المحددة (Show ALL Products)' : 'Show ALL Configured Formulations (No Limit)' }}
                            </option>
                            <option value="3" {{ ($landingSettings['landing_products_limit'] ?? 6) == 3 ? 'selected' : '' }}>3 Formulations (1 Row)</option>
                            <option value="6" {{ ($landingSettings['landing_products_limit'] ?? 6) == 6 ? 'selected' : '' }}>6 Formulations (2 Rows - Recommended)</option>
                            <option value="9" {{ ($landingSettings['landing_products_limit'] ?? 6) == 9 ? 'selected' : '' }}>9 Formulations (3 Rows)</option>
                            <option value="12" {{ ($landingSettings['landing_products_limit'] ?? 6) == 12 ? 'selected' : '' }}>12 Formulations (4 Rows)</option>
                        </select>
                        <span class="text-[10px] text-gray-400 mt-1 block">
                            {{ app()->getLocale() === 'ar' ? 'اختر 0 لعرض كافة المنتجات التي تم تفعيلها للواجهة' : 'Select 0 to display all products with "Show on Landing" turned ON' }}
                        </span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                            {{ app()->getLocale() === 'ar' ? 'نص زر تصفح الكتالوج' : 'View Catalog CTA Button' }}
                        </label>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="text" name="landing_products_cta_text_en" value="{{ $landingSettings['landing_products_cta_text_en'] ?? 'VIEW ALL FORMULATIONS' }}" class="form-control text-xs" placeholder="EN: VIEW ALL">
                            <input type="text" name="landing_products_cta_text_ar" value="{{ $landingSettings['landing_products_cta_text_ar'] ?? 'عرض جميع المستحضرات' }}" class="form-control text-xs" placeholder="AR: عرض الكل" dir="rtl">
                        </div>
                    </div>
                </div>

                <!-- Section Bilingual Titles & Badges -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- English Content -->
                    <div class="p-4 rounded-xl bg-white dark:bg-[#031827] border border-gray-200 dark:border-gray-800 space-y-3">
                        <div class="flex items-center gap-2 text-xs font-bold text-[#0A4F78] dark:text-[#2A8FC2] uppercase tracking-wider">
                            <span class="w-2 h-2 rounded-full bg-[#0A4F78] dark:bg-[#2A8FC2]"></span>
                            {{ app()->getLocale() === 'ar' ? 'المحتوى بالإنجليزية (English LTR)' : 'English Content (LTR)' }}
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">Section Eyebrow / Badge</label>
                            <input type="text" name="landing_products_badge_en" value="{{ $landingSettings['landing_products_badge_en'] ?? 'CLINICAL FORMULATIONS' }}" class="form-control text-xs font-bold" placeholder="e.g. CLINICAL FORMULATIONS">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">Section Main Title</label>
                            <input type="text" name="landing_products_title_en" value="{{ $landingSettings['landing_products_title_en'] ?? 'Engineered for Systemic Longevity & Vitality' }}" class="form-control text-xs font-bold" placeholder="e.g. Engineered for Systemic Longevity">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">Section Subtitle / Description</label>
                            <textarea name="landing_products_subtitle_en" rows="2" class="form-control text-xs" placeholder="Clinical subtitle describing the formulations...">{{ $landingSettings['landing_products_subtitle_en'] ?? 'Targeted botanical bio-compounds designed to support cellular repair, cognitive sharpness, and daily metabolic energy.' }}</textarea>
                        </div>
                    </div>

                    <!-- Arabic Content -->
                    <div class="p-4 rounded-xl bg-white dark:bg-[#031827] border border-gray-200 dark:border-gray-800 space-y-3" dir="rtl">
                        <div class="flex items-center gap-2 text-xs font-bold text-[#67B34A] uppercase tracking-wider">
                            <span class="w-2 h-2 rounded-full bg-[#67B34A]"></span>
                            {{ app()->getLocale() === 'ar' ? 'المحتوى بالعربية (Arabic RTL)' : 'Arabic Content (RTL)' }}
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">شارة القسم العلوية (Badge)</label>
                            <input type="text" name="landing_products_badge_ar" value="{{ $landingSettings['landing_products_badge_ar'] ?? 'التركيبات الطبية السريرية' }}" class="form-control text-xs font-bold" placeholder="مثال: التركيبات الطبية السريرية">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">العنوان الرئيسي للقسم</label>
                            <input type="text" name="landing_products_title_ar" value="{{ $landingSettings['landing_products_title_ar'] ?? 'مصممة خصيصاً للصحة الخلوية وطول العمر المديد' }}" class="form-control text-xs font-bold" placeholder="مثال: مصممة خصيصاً للصحة الخلوية وطول العمر">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">الوصف التوضيحي السريري</label>
                            <textarea name="landing_products_subtitle_ar" rows="2" class="form-control text-xs" placeholder="الوصف الطبي المرافق للقسم...">{{ $landingSettings['landing_products_subtitle_ar'] ?? 'مركبات نباتية نشطة بيولوجياً تستهدف تحفيز الترميم الخلوي، تعزيز صفاء الذهن، ودعم الطاقة الأيضية اليومية.' }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Action Footer -->
                <div class="flex items-center justify-between pt-3 border-t border-gray-200 dark:border-gray-800 flex-wrap gap-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        <i class="fa-solid fa-lightbulb text-amber-500 mr-1 ml-1"></i>
                        {{ app()->getLocale() === 'ar' ? 'التغييرات تنعكس فورياً على قسم المنتجات في الصفحة الرئيسية.' : 'Changes apply immediately to the landing page products showcase.' }}
                    </p>
                    <button type="submit" class="btn btn-primary font-bold shadow-md px-6">
                        <i class="fa-solid fa-floppy-disk mr-1.5 ml-1.5"></i>
                        {{ app()->getLocale() === 'ar' ? 'حفظ إعدادات قسم الصفحة الرئيسية' : 'Save Section Settings' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Product Details Page Sections Customization Control Hub -->
    <div class="mb-6 rounded-2xl border border-[#0A4F78]/20 dark:border-[#2A8FC2]/25 bg-gradient-to-br from-white via-white to-slate-50 dark:from-[#062B49] dark:via-[#031827] dark:to-[#02131F] p-5 sm:p-6 shadow-md transition-all">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-4 border-b border-[#0A4F78]/10 dark:border-white/10">
            <div class="flex items-start sm:items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-purple-500/10 dark:bg-purple-500/20 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xl shrink-0 shadow-xs">
                    <i class="fa-solid fa-microscope"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h3 class="text-base sm:text-lg font-black text-gray-900 dark:text-white tracking-tight">
                            {{ app()->getLocale() === 'ar' ? 'إعدادات وتخصيص أقسام صفحة تفاصيل المنتج' : 'Product Details Dossier Sections & Content Studio' }}
                        </h3>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800 dark:bg-purple-950/60 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                            <i class="fa-solid fa-pen-ruler text-[10px]"></i>
                            {{ app()->getLocale() === 'ar' ? 'تخصيص ثنائي اللغة كامل' : 'Full Bilingual Customizer' }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 font-medium">
                        {{ app()->getLocale() === 'ar' 
                            ? 'تحكّم في عناوين الأقسام الستة والبادجات التوضيحية لملف تفاصيل المنتج (المكونات، الفوائد، البروتوكول، العلوم، السلامة، المواصفات والتركيبات المتكاملة).' 
                            : 'Customize bilingual titles, badges, and headers for all 6 dossier sections and complementary pairings on product detail pages.' }}
                    </p>
                </div>
            </div>

            <!-- Quick Action Toggle -->
            <div class="flex items-center gap-2.5 self-start lg:self-center shrink-0">
                <button type="button" 
                        onclick="toggleDetailsSettingsCollapse()" 
                        class="btn btn-sm font-bold bg-[#0A4F78] hover:bg-[#062B49] text-white shadow-xs cursor-pointer"
                        id="toggleDetailsSettingsBtn">
                    <i class="fa-solid fa-sliders mr-1 ml-1"></i>
                    <span id="toggleDetailsSettingsBtnText">{{ app()->getLocale() === 'ar' ? 'تخصيص عناوين الأقسام' : 'Customize Dossier Sections' }}</span>
                    <i class="fa-solid fa-chevron-down text-xs ml-1 mr-1 transition-transform" id="toggleDetailsSettingsIcon"></i>
                </button>
            </div>
        </div>

        <!-- Collapsible Product Details Sections Customization Form -->
        <div id="detailsSettingsCollapseBody" class="mt-5 pt-4 border-t border-dashed border-[#0A4F78]/15 dark:border-white/10 hidden">
            <form method="POST" action="{{ route('admin.products.details-section-settings') }}" id="detailsSectionGlobalForm" class="space-y-6">
                @csrf

                <!-- 1. Master Dossier Header -->
                <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900/40 border border-gray-200 dark:border-gray-800 space-y-3">
                    <div class="flex items-center gap-2 text-xs font-bold text-gray-800 dark:text-white uppercase tracking-wider">
                        <i class="fa-solid fa-folder-open text-[#0A4F78] dark:text-[#2A8FC2]"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'رأس ملف المواصفات السريرية العام (Master Dossier Header)' : 'Master Dossier Header & Eyebrow' }}</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">Dossier Eyebrow / Badge (EN)</label>
                            <input type="text" name="pdetail_dossier_badge_en" value="{{ $detailsSettings['pdetail_dossier_badge_en'] ?? '' }}" class="form-control text-xs font-bold">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">بادج ملف المواصفات (AR)</label>
                            <input type="text" name="pdetail_dossier_badge_ar" value="{{ $detailsSettings['pdetail_dossier_badge_ar'] ?? '' }}" class="form-control text-xs font-bold" dir="rtl">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">Dossier Main Title (EN)</label>
                            <input type="text" name="pdetail_dossier_title_en" value="{{ $detailsSettings['pdetail_dossier_title_en'] ?? '' }}" class="form-control text-xs font-bold">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">العنوان الرئيسي لملف المواصفات (AR)</label>
                            <input type="text" name="pdetail_dossier_title_ar" value="{{ $detailsSettings['pdetail_dossier_title_ar'] ?? '' }}" class="form-control text-xs font-bold" dir="rtl">
                        </div>
                    </div>
                </div>

                <!-- 2. Accordion 1 & 2: Active Compounds & Clinical Benefits -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Accordion 01: Compounds -->
                    <div class="p-4 rounded-xl bg-white dark:bg-[#031827] border border-gray-200 dark:border-gray-800 space-y-3">
                        <div class="flex items-center gap-2 text-xs font-bold text-[#67B34A] uppercase tracking-wider">
                            <i class="fa-solid fa-flask-vial"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'القسم 1: المكونات الفعالة والجرعات' : 'Section 01: Active Compounds' }}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[11px] font-semibold text-gray-600 dark:text-gray-400 mb-1">Badge (EN)</label>
                                <input type="text" name="pdetail_sec1_badge_en" value="{{ $detailsSettings['pdetail_sec1_badge_en'] ?? '' }}" class="form-control text-xs">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-gray-600 dark:text-gray-400 mb-1">البادج (AR)</label>
                                <input type="text" name="pdetail_sec1_badge_ar" value="{{ $detailsSettings['pdetail_sec1_badge_ar'] ?? '' }}" class="form-control text-xs" dir="rtl">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-600 dark:text-gray-400 mb-1">Title (EN)</label>
                            <input type="text" name="pdetail_sec1_title_en" value="{{ $detailsSettings['pdetail_sec1_title_en'] ?? '' }}" class="form-control text-xs font-bold">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-600 dark:text-gray-400 mb-1">العنوان (AR)</label>
                            <input type="text" name="pdetail_sec1_title_ar" value="{{ $detailsSettings['pdetail_sec1_title_ar'] ?? '' }}" class="form-control text-xs font-bold" dir="rtl">
                        </div>
                    </div>

                    <!-- Accordion 02: Benefits -->
                    <div class="p-4 rounded-xl bg-white dark:bg-[#031827] border border-gray-200 dark:border-gray-800 space-y-3">
                        <div class="flex items-center gap-2 text-xs font-bold text-amber-500 uppercase tracking-wider">
                            <i class="fa-solid fa-bolt"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'القسم 2: الفوائد والمسارات الحيوية' : 'Section 02: Clinical Benefits' }}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[11px] font-semibold text-gray-600 dark:text-gray-400 mb-1">Badge (EN)</label>
                                <input type="text" name="pdetail_sec2_badge_en" value="{{ $detailsSettings['pdetail_sec2_badge_en'] ?? '' }}" class="form-control text-xs">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-gray-600 dark:text-gray-400 mb-1">البادج (AR)</label>
                                <input type="text" name="pdetail_sec2_badge_ar" value="{{ $detailsSettings['pdetail_sec2_badge_ar'] ?? '' }}" class="form-control text-xs" dir="rtl">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-600 dark:text-gray-400 mb-1">Title (EN)</label>
                            <input type="text" name="pdetail_sec2_title_en" value="{{ $detailsSettings['pdetail_sec2_title_en'] ?? '' }}" class="form-control text-xs font-bold">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-600 dark:text-gray-400 mb-1">العنوان (AR)</label>
                            <input type="text" name="pdetail_sec2_title_ar" value="{{ $detailsSettings['pdetail_sec2_title_ar'] ?? '' }}" class="form-control text-xs font-bold" dir="rtl">
                        </div>
                    </div>
                </div>

                <!-- 3. Accordion 3 & 4: Protocol & Cellular Science -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Accordion 03: Protocol -->
                    <div class="p-4 rounded-xl bg-white dark:bg-[#031827] border border-gray-200 dark:border-gray-800 space-y-3">
                        <div class="flex items-center gap-2 text-xs font-bold text-[#2A8FC2] uppercase tracking-wider">
                            <i class="fa-solid fa-calendar-check"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'القسم 3: بروتوكول الاستخدام' : 'Section 03: Protocol & Guidelines' }}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[11px] font-semibold text-gray-600 dark:text-gray-400 mb-1">Badge (EN)</label>
                                <input type="text" name="pdetail_sec3_badge_en" value="{{ $detailsSettings['pdetail_sec3_badge_en'] ?? '' }}" class="form-control text-xs">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-gray-600 dark:text-gray-400 mb-1">البادج (AR)</label>
                                <input type="text" name="pdetail_sec3_badge_ar" value="{{ $detailsSettings['pdetail_sec3_badge_ar'] ?? '' }}" class="form-control text-xs" dir="rtl">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-600 dark:text-gray-400 mb-1">Title (EN)</label>
                            <input type="text" name="pdetail_sec3_title_en" value="{{ $detailsSettings['pdetail_sec3_title_en'] ?? '' }}" class="form-control text-xs font-bold">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-600 dark:text-gray-400 mb-1">العنوان (AR)</label>
                            <input type="text" name="pdetail_sec3_title_ar" value="{{ $detailsSettings['pdetail_sec3_title_ar'] ?? '' }}" class="form-control text-xs font-bold" dir="rtl">
                        </div>
                    </div>

                    <!-- Accordion 04: Cellular Science -->
                    <div class="p-4 rounded-xl bg-white dark:bg-[#031827] border border-gray-200 dark:border-gray-800 space-y-3">
                        <div class="flex items-center gap-2 text-xs font-bold text-purple-500 uppercase tracking-wider">
                            <i class="fa-solid fa-dna"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'القسم 4: الآلية العلمية والدوائية' : 'Section 04: Cellular Science' }}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[11px] font-semibold text-gray-600 dark:text-gray-400 mb-1">Badge (EN)</label>
                                <input type="text" name="pdetail_sec4_badge_en" value="{{ $detailsSettings['pdetail_sec4_badge_en'] ?? '' }}" class="form-control text-xs">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-gray-600 dark:text-gray-400 mb-1">البادج (AR)</label>
                                <input type="text" name="pdetail_sec4_badge_ar" value="{{ $detailsSettings['pdetail_sec4_badge_ar'] ?? '' }}" class="form-control text-xs" dir="rtl">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-600 dark:text-gray-400 mb-1">Title (EN)</label>
                            <input type="text" name="pdetail_sec4_title_en" value="{{ $detailsSettings['pdetail_sec4_title_en'] ?? '' }}" class="form-control text-xs font-bold">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-600 dark:text-gray-400 mb-1">العنوان (AR)</label>
                            <input type="text" name="pdetail_sec4_title_ar" value="{{ $detailsSettings['pdetail_sec4_title_ar'] ?? '' }}" class="form-control text-xs font-bold" dir="rtl">
                        </div>
                    </div>
                </div>

                <!-- 4. Accordion 5 & 6: Safety & Supply Specs -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Accordion 05: Safety -->
                    <div class="p-4 rounded-xl bg-white dark:bg-[#031827] border border-gray-200 dark:border-gray-800 space-y-3">
                        <div class="flex items-center gap-2 text-xs font-bold text-emerald-600 uppercase tracking-wider">
                            <i class="fa-solid fa-shield-halved"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'القسم 5: إرشادات السلامة والتحذيرات' : 'Section 05: Safety & Warnings' }}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[11px] font-semibold text-gray-600 dark:text-gray-400 mb-1">Badge (EN)</label>
                                <input type="text" name="pdetail_sec5_badge_en" value="{{ $detailsSettings['pdetail_sec5_badge_en'] ?? '' }}" class="form-control text-xs">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-gray-600 dark:text-gray-400 mb-1">البادج (AR)</label>
                                <input type="text" name="pdetail_sec5_badge_ar" value="{{ $detailsSettings['pdetail_sec5_badge_ar'] ?? '' }}" class="form-control text-xs" dir="rtl">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-600 dark:text-gray-400 mb-1">Title (EN)</label>
                            <input type="text" name="pdetail_sec5_title_en" value="{{ $detailsSettings['pdetail_sec5_title_en'] ?? '' }}" class="form-control text-xs font-bold">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-600 dark:text-gray-400 mb-1">العنوان (AR)</label>
                            <input type="text" name="pdetail_sec5_title_ar" value="{{ $detailsSettings['pdetail_sec5_title_ar'] ?? '' }}" class="form-control text-xs font-bold" dir="rtl">
                        </div>
                    </div>

                    <!-- Accordion 06: Monograph Specs -->
                    <div class="p-4 rounded-xl bg-white dark:bg-[#031827] border border-gray-200 dark:border-gray-800 space-y-3">
                        <div class="flex items-center gap-2 text-xs font-bold text-indigo-500 uppercase tracking-wider">
                            <i class="fa-solid fa-certificate"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'القسم 6: المواصفات وضمان النقاء' : 'Section 06: Monograph & Supply Specs' }}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[11px] font-semibold text-gray-600 dark:text-gray-400 mb-1">Badge (EN)</label>
                                <input type="text" name="pdetail_sec6_badge_en" value="{{ $detailsSettings['pdetail_sec6_badge_en'] ?? '' }}" class="form-control text-xs">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-gray-600 dark:text-gray-400 mb-1">البادج (AR)</label>
                                <input type="text" name="pdetail_sec6_badge_ar" value="{{ $detailsSettings['pdetail_sec6_badge_ar'] ?? '' }}" class="form-control text-xs" dir="rtl">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-600 dark:text-gray-400 mb-1">Title (EN)</label>
                            <input type="text" name="pdetail_sec6_title_en" value="{{ $detailsSettings['pdetail_sec6_title_en'] ?? '' }}" class="form-control text-xs font-bold">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-600 dark:text-gray-400 mb-1">العنوان (AR)</label>
                            <input type="text" name="pdetail_sec6_title_ar" value="{{ $detailsSettings['pdetail_sec6_title_ar'] ?? '' }}" class="form-control text-xs font-bold" dir="rtl">
                        </div>
                    </div>
                </div>

                <!-- 5. Complementary Formulations Section -->
                <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900/40 border border-gray-200 dark:border-gray-800 space-y-3">
                    <div class="flex items-center gap-2 text-xs font-bold text-gray-800 dark:text-white uppercase tracking-wider">
                        <i class="fa-solid fa-layer-group text-primary"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'قسم التركيبات المتكاملة الموصى بها (Complementary Formulations Pairing)' : 'Complementary Formulations Section' }}</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">Eyebrow / Badge (EN)</label>
                            <input type="text" name="pdetail_related_badge_en" value="{{ $detailsSettings['pdetail_related_badge_en'] ?? '' }}" class="form-control text-xs font-bold">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">البادج (AR)</label>
                            <input type="text" name="pdetail_related_badge_ar" value="{{ $detailsSettings['pdetail_related_badge_ar'] ?? '' }}" class="form-control text-xs font-bold" dir="rtl">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">Section Title (EN)</label>
                            <input type="text" name="pdetail_related_title_en" value="{{ $detailsSettings['pdetail_related_title_en'] ?? '' }}" class="form-control text-xs font-bold">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-1">عنوان القسم (AR)</label>
                            <input type="text" name="pdetail_related_title_ar" value="{{ $detailsSettings['pdetail_related_title_ar'] ?? '' }}" class="form-control text-xs font-bold" dir="rtl">
                        </div>
                    </div>
                </div>

                <!-- Action Footer -->
                <div class="flex items-center justify-between pt-3 border-t border-gray-200 dark:border-gray-800 flex-wrap gap-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        <i class="fa-solid fa-lightbulb text-amber-500 mr-1 ml-1"></i>
                        {{ app()->getLocale() === 'ar' ? 'يتم تطبيق التعديلات مباشرة على ملفات كافة المنتجات في المتجر.' : 'Changes apply immediately across all product detail pages.' }}
                    </p>
                    <button type="submit" class="btn btn-primary font-bold shadow-md px-6">
                        <i class="fa-solid fa-floppy-disk mr-1.5 ml-1.5"></i>
                        {{ app()->getLocale() === 'ar' ? 'حفظ إعدادات أقسام صفحة تفاصيل المنتج' : 'Save Product Details Settings' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==========================================================
         SECTION SETTING 3: PRODUCT ACTION BUTTONS & CTA CONTROLLER (ADD TO CLINICAL PROTOCOL)
         ========================================================== -->
    <div class="mb-6 p-5 sm:p-6 rounded-2xl bg-white dark:bg-[#062B49] border border-[#0A4F78]/15 dark:border-white/10 shadow-sm transition-all duration-300">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="flex items-start sm:items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-pink-500/10 dark:bg-pink-500/20 text-pink-600 dark:text-pink-400 flex items-center justify-center text-lg shrink-0 border border-pink-500/25">
                    <i class="fa-solid fa-cart-shopping"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="px-2.5 py-0.5 rounded-full bg-pink-500/15 text-pink-600 dark:text-pink-400 font-mono text-[10px] font-black uppercase tracking-wider border border-pink-500/25">
                            BUTTONS & CTA CMS CONTROLLER
                        </span>
                        <span class="text-xs font-bold text-gray-800 dark:text-gray-100 flex items-center gap-1.5">
                            <span>{{ app()->getLocale() === 'ar' ? 'تخصيص أزرار الشراء ونصوص الإجراءات السريرية' : 'Product & Action Buttons Customizer' }}</span>
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 font-medium">
                        {{ app()->getLocale() === 'ar' 
                            ? 'تحكّم كامل في نصوص وزر الشراء الرئيسي (مثل: ADD TO CLINICAL PROTOCOL)، أزرار بطاقات المتجر، أزرار نفاد المخزون، وباقة البروتوكول.' 
                            : 'Customize the primary Buy Button text (e.g. ADD TO CLINICAL PROTOCOL), catalog card action buttons, out-of-stock states, and protocol bundle CTAs.' }}
                    </p>
                </div>
            </div>

            <!-- Quick Action Toggle -->
            <div class="flex items-center gap-2.5 self-start lg:self-center shrink-0">
                <button type="button" 
                        onclick="toggleButtonSettingsCollapse()" 
                        class="btn btn-sm font-bold bg-pink-600 hover:bg-pink-700 text-white shadow-xs cursor-pointer flex items-center gap-2"
                        id="toggleButtonSettingsBtn">
                    <i class="fa-solid fa-hand-pointer text-xs"></i>
                    <span id="toggleButtonSettingsBtnText">{{ app()->getLocale() === 'ar' ? 'تخصيص أزرار الشراء' : 'Customize Product Buttons' }}</span>
                    <i class="fa-solid fa-chevron-down text-xs transition-transform" id="toggleButtonSettingsIcon"></i>
                </button>
            </div>
        </div>

        <!-- Collapsible Buttons Customization Form -->
        <div id="buttonSettingsCollapseBody" class="mt-5 pt-4 border-t border-dashed border-pink-500/20 dark:border-white/10 hidden">
            <form method="POST" action="{{ route('admin.products.button-settings') }}" id="productButtonsGlobalForm" class="space-y-6">
                @csrf

                <!-- Live Button Preview Simulator -->
                <div class="p-5 rounded-2xl bg-gradient-to-br from-[#02131F] to-[#0A4F78] text-white space-y-4 border border-white/10 shadow-lg">
                    <div class="flex items-center justify-between border-b border-white/10 pb-3 flex-wrap gap-2">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#67B34A] animate-pulse"></span>
                            <span class="font-mono text-xs font-black uppercase text-pink-300">{{ app()->getLocale() === 'ar' ? 'معاينة حية لأزرار المتجر' : 'Live Interactive Button Previews' }}</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-[10px] text-white/60 font-mono">{{ app()->getLocale() === 'ar' ? 'نماذج سريعة:' : 'Quick Presets:' }}</span>
                            <button type="button" onclick="applyButtonPreset('clinical')" class="px-2.5 py-1 rounded-lg bg-white/10 hover:bg-white/20 text-white text-[10px] font-bold transition-all border border-white/15">Clinical</button>
                            <button type="button" onclick="applyButtonPreset('longevity')" class="px-2.5 py-1 rounded-lg bg-white/10 hover:bg-white/20 text-white text-[10px] font-bold transition-all border border-white/15">Longevity</button>
                            <button type="button" onclick="applyButtonPreset('ecommerce')" class="px-2.5 py-1 rounded-lg bg-white/10 hover:bg-white/20 text-white text-[10px] font-bold transition-all border border-white/15">Classic</button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 items-center">
                        <!-- Primary Product Detail CTA Button Preview -->
                        <div class="space-y-1.5 text-center">
                            <span class="text-[10px] font-mono uppercase text-white/70 block">{{ app()->getLocale() === 'ar' ? 'زر صفحة المنتج الرئيسي (Buy Box):' : 'Primary Product Page Button:' }}</span>
                            <div class="py-3.5 px-6 rounded-2xl bg-[#0A4F78] border border-white/20 shadow-xl flex items-center justify-center gap-2.5 font-extrabold text-xs tracking-wider text-white">
                                <i class="fa-solid {{ $buttonSettings['btn_buy_now_icon'] ?? 'fa-cart-shopping' }}" id="prevBtnBuyNowIcon"></i>
                                <span id="prevBtnBuyNowText">{{ app()->getLocale() === 'ar' ? ($buttonSettings['btn_buy_now_text_ar'] ?? 'إضافة إلى السلة والبروتوكول') : ($buttonSettings['btn_buy_now_text_en'] ?? 'ADD TO CLINICAL PROTOCOL') }}</span>
                            </div>
                            <div class="text-[10px] font-mono text-[#67B34A]" id="prevBtnBuyNowSubtext">
                                <i class="fa-solid fa-circle-check text-[9px] mr-1 ml-1"></i>
                                <span>{{ app()->getLocale() === 'ar' ? ($buttonSettings['btn_buy_now_subtext_ar'] ?? 'تركيبة سريرية موثقة') : ($buttonSettings['btn_buy_now_subtext_en'] ?? 'Clinically Verified Formulation') }}</span>
                            </div>
                        </div>

                        <!-- Catalog Quick Add Button Preview -->
                        <div class="space-y-1.5 text-center">
                            <span class="text-[10px] font-mono uppercase text-white/70 block">{{ app()->getLocale() === 'ar' ? 'زر بطاقة الكتالوج (Quick Add):' : 'Catalog Card Button:' }}</span>
                            <div class="py-2.5 px-5 rounded-xl bg-white/10 border border-white/20 flex items-center justify-center gap-2 font-black text-xs text-white">
                                <i class="fa-solid fa-cart-plus"></i>
                                <span id="prevBtnQuickAddText">{{ app()->getLocale() === 'ar' ? ($buttonSettings['btn_quick_add_text_ar'] ?? 'إضافة سريعة') : ($buttonSettings['btn_quick_add_text_en'] ?? 'QUICK ADD') }}</span>
                            </div>
                        </div>

                        <!-- Protocol Bundle Button Preview -->
                        <div class="space-y-1.5 text-center">
                            <span class="text-[10px] font-mono uppercase text-white/70 block">{{ app()->getLocale() === 'ar' ? 'زر باقة البروتوكول 1-Click:' : 'Protocol Bundle 1-Click Button:' }}</span>
                            <div class="py-2.5 px-5 rounded-xl bg-gradient-to-r from-pink-500 to-rose-500 border border-white/20 flex items-center justify-center gap-2 font-black text-xs text-white shadow-md">
                                <i class="fa-solid fa-sparkles"></i>
                                <span id="prevBtnBundleText">{{ app()->getLocale() === 'ar' ? ($buttonSettings['btn_bundle_buy_text_ar'] ?? 'إضافة البروتوكول التكاملي') : ($buttonSettings['btn_bundle_buy_text_en'] ?? 'ADD COMPLETE PROTOCOL') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 1. Primary Product Details Button -->
                <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-gray-200 dark:border-gray-800 space-y-4">
                    <div class="flex items-center gap-2 text-xs font-bold text-gray-800 dark:text-white uppercase tracking-wider">
                        <i class="fa-solid fa-square-check text-[#0A4F78] dark:text-[#2A8FC2]"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'زر الشراء الرئيسي في صفحة المنتج (Primary Buy Box Action Button)' : 'Primary Product Detail Page Buy Button' }}</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                <span class="px-1.5 py-0.5 rounded bg-slate-200 dark:bg-slate-700 text-[10px] font-mono font-bold mr-1 ml-1">EN</span>
                                Button Label (English)
                            </label>
                            <input type="text" name="btn_buy_now_text_en" id="inputBtnBuyNowEn"
                                   value="{{ $buttonSettings['btn_buy_now_text_en'] ?? 'ADD TO CLINICAL PROTOCOL' }}" 
                                   oninput="updateButtonPreview()"
                                   class="form-control text-xs font-bold font-mono">
                            <span class="text-[10px] text-gray-400">{{ app()->getLocale() === 'ar' ? 'النص الإنجليزي المعروض داخل زر الشراء' : 'Default: ADD TO CLINICAL PROTOCOL' }}</span>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                <span class="px-1.5 py-0.5 rounded bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 text-[10px] font-mono font-bold mr-1 ml-1">AR</span>
                                نص الزر (العربية)
                            </label>
                            <input type="text" name="btn_buy_now_text_ar" id="inputBtnBuyNowAr"
                                   value="{{ $buttonSettings['btn_buy_now_text_ar'] ?? 'إضافة إلى السلة والبروتوكول' }}" 
                                   oninput="updateButtonPreview()"
                                   dir="rtl"
                                   class="form-control text-xs font-bold font-tajawal">
                            <span class="text-[10px] text-gray-400" dir="rtl">{{ app()->getLocale() === 'ar' ? 'الافتراضي: إضافة إلى السلة والبروتوكول' : 'Default: إضافة إلى السلة والبروتوكول' }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Button Icon (FontAwesome)</label>
                            <select name="btn_buy_now_icon" id="inputBtnBuyNowIcon" onchange="updateButtonPreview()" class="form-control text-xs font-mono">
                                <option value="fa-cart-shopping" {{ ($buttonSettings['btn_buy_now_icon'] ?? '') === 'fa-cart-shopping' ? 'selected' : '' }}>fa-cart-shopping (Cart)</option>
                                <option value="fa-bolt" {{ ($buttonSettings['btn_buy_now_icon'] ?? '') === 'fa-bolt' ? 'selected' : '' }}>fa-bolt (Lightning)</option>
                                <option value="fa-capsules" {{ ($buttonSettings['btn_buy_now_icon'] ?? '') === 'fa-capsules' ? 'selected' : '' }}>fa-capsules (Bioceutical)</option>
                                <option value="fa-shield-halved" {{ ($buttonSettings['btn_buy_now_icon'] ?? '') === 'fa-shield-halved' ? 'selected' : '' }}>fa-shield-halved (Clinical)</option>
                                <option value="fa-bag-shopping" {{ ($buttonSettings['btn_buy_now_icon'] ?? '') === 'fa-bag-shopping' ? 'selected' : '' }}>fa-bag-shopping (Shopping Bag)</option>
                                <option value="fa-plus" {{ ($buttonSettings['btn_buy_now_icon'] ?? '') === 'fa-plus' ? 'selected' : '' }}>fa-plus (Add Plus)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Trust Guarantee Subtext (EN)</label>
                            <input type="text" name="btn_buy_now_subtext_en" id="inputBtnSubtextEn"
                                   value="{{ $buttonSettings['btn_buy_now_subtext_en'] ?? '' }}" 
                                   oninput="updateButtonPreview()"
                                   placeholder="Clinically Verified Formulation • 100% Purity"
                                   class="form-control text-xs">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">نص الضمان والمطابقة (AR)</label>
                            <input type="text" name="btn_buy_now_subtext_ar" id="inputBtnSubtextAr"
                                   value="{{ $buttonSettings['btn_buy_now_subtext_ar'] ?? '' }}" 
                                   oninput="updateButtonPreview()"
                                   dir="rtl"
                                   placeholder="تركيبة سريرية موثقة • نقاء دوائي 100%"
                                   class="form-control text-xs font-tajawal">
                        </div>
                    </div>
                </div>

                <!-- 2. Catalog & Card Buttons -->
                <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-gray-200 dark:border-gray-800 space-y-4">
                    <div class="flex items-center gap-2 text-xs font-bold text-gray-800 dark:text-white uppercase tracking-wider">
                        <i class="fa-solid fa-layer-group text-[#67B34A]"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'أزرار بطاقات المتجر والكتالوج (Catalog Cards & Quick Add)' : 'Storefront Catalog Cards & Quick Add Buttons' }}</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Quick Add (EN)</label>
                            <input type="text" name="btn_quick_add_text_en" id="inputBtnQuickAddEn"
                                   value="{{ $buttonSettings['btn_quick_add_text_en'] ?? 'QUICK ADD' }}" 
                                   oninput="updateButtonPreview()"
                                   class="form-control text-xs font-bold">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">إضافة سريعة (AR)</label>
                            <input type="text" name="btn_quick_add_text_ar" id="inputBtnQuickAddAr"
                                   value="{{ $buttonSettings['btn_quick_add_text_ar'] ?? 'إضافة سريعة' }}" 
                                   oninput="updateButtonPreview()"
                                   dir="rtl"
                                   class="form-control text-xs font-bold font-tajawal">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">View Dossier (EN)</label>
                            <input type="text" name="btn_view_details_text_en" 
                                   value="{{ $buttonSettings['btn_view_details_text_en'] ?? 'VIEW DOSSIER' }}" 
                                   class="form-control text-xs font-bold">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">عرض الملف (AR)</label>
                            <input type="text" name="btn_view_details_text_ar" 
                                   value="{{ $buttonSettings['btn_view_details_text_ar'] ?? 'عرض الملف السريري' }}" 
                                   dir="rtl"
                                   class="form-control text-xs font-bold font-tajawal">
                        </div>
                    </div>
                </div>

                <!-- 3. Availability & Protocol Bundle Buttons -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Stock State Buttons -->
                    <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-gray-200 dark:border-gray-800 space-y-3">
                        <div class="flex items-center gap-2 text-xs font-bold text-amber-600 uppercase tracking-wider">
                            <i class="fa-solid fa-boxes-packing"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'نصوص نفاد المخزون والحجز المسبق' : 'Out of Stock & Pre-Order States' }}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-semibold text-gray-600 dark:text-gray-400 mb-1">Out of Stock (EN)</label>
                                <input type="text" name="btn_out_of_stock_text_en" value="{{ $buttonSettings['btn_out_of_stock_text_en'] ?? 'CURRENTLY OUT OF STOCK' }}" class="form-control text-xs">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-gray-600 dark:text-gray-400 mb-1">نفدت الكمية (AR)</label>
                                <input type="text" name="btn_out_of_stock_text_ar" value="{{ $buttonSettings['btn_out_of_stock_text_ar'] ?? 'نفذت الكمية حالياً' }}" class="form-control text-xs" dir="rtl">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-gray-600 dark:text-gray-400 mb-1">Pre-Order (EN)</label>
                                <input type="text" name="btn_preorder_text_en" value="{{ $buttonSettings['btn_preorder_text_en'] ?? 'PRE-ORDER PROTOCOL' }}" class="form-control text-xs">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-gray-600 dark:text-gray-400 mb-1">حجز مسبق (AR)</label>
                                <input type="text" name="btn_preorder_text_ar" value="{{ $buttonSettings['btn_preorder_text_ar'] ?? 'طلب حجز مسبق' }}" class="form-control text-xs" dir="rtl">
                            </div>
                        </div>
                    </div>

                    <!-- Protocol Bundle 1-Click Button -->
                    <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-gray-200 dark:border-gray-800 space-y-3">
                        <div class="flex items-center gap-2 text-xs font-bold text-pink-600 uppercase tracking-wider">
                            <i class="fa-solid fa-gift"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'زر باقة البروتوكول التكاملي 1-Click' : 'Protocol Bundle 1-Click Button' }}</span>
                        </div>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-[11px] font-semibold text-gray-600 dark:text-gray-400 mb-1">Bundle Action Text (EN)</label>
                                <input type="text" name="btn_bundle_buy_text_en" id="inputBtnBundleEn"
                                       value="{{ $buttonSettings['btn_bundle_buy_text_en'] ?? 'ADD COMPLETE PROTOCOL TO CART' }}" 
                                       oninput="updateButtonPreview()"
                                       class="form-control text-xs font-mono font-bold">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-gray-600 dark:text-gray-400 mb-1">نص زر الباقة (AR)</label>
                                <input type="text" name="btn_bundle_buy_text_ar" id="inputBtnBundleAr"
                                       value="{{ $buttonSettings['btn_bundle_buy_text_ar'] ?? 'إضافة البروتوكول التكاملي بالكامل للسلة' }}" 
                                       oninput="updateButtonPreview()"
                                       dir="rtl"
                                       class="form-control text-xs font-bold font-tajawal">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Footer -->
                <div class="flex items-center justify-between pt-3 border-t border-gray-200 dark:border-gray-800 flex-wrap gap-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        <i class="fa-solid fa-bolt text-pink-500 mr-1 ml-1"></i>
                        {{ app()->getLocale() === 'ar' ? 'يتم تطبيق نصوص الأزرار فوراً على كامل صفحات وتطبيقات المتجر.' : 'Changes apply immediately across all product pages and catalog cards.' }}
                    </p>
                    <button type="submit" class="btn btn-primary font-bold shadow-md px-6 bg-pink-600 hover:bg-pink-700 border-pink-600">
                        <i class="fa-solid fa-floppy-disk mr-1.5 ml-1.5"></i>
                        {{ app()->getLocale() === 'ar' ? 'حفظ وتطبيق إعدادات الأزرار' : 'Save Product Button Settings' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Toolbar & Filter Tabs -->
    <div class="mb-6 space-y-4">
        <div class="flex items-center gap-2 border-b border-gray-200 dark:border-gray-700 pb-3 flex-wrap">
            <a href="{{ route('admin.products.index') }}" 
               class="px-4 py-2 rounded-lg text-sm font-bold transition-colors {{ !$isTrashed && empty($currentStatus) ? 'bg-[#0A4F78] text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                <i class="fa-solid fa-boxes-stacked mr-1.5 ml-1.5"></i>
                {{ app()->getLocale() === 'ar' ? 'جميع التركيبات النشطة' : 'All Active Formulations' }}
                <span class="ml-1.5 mr-1.5 px-2 py-0.5 text-xs rounded-full {{ !$isTrashed && empty($currentStatus) ? 'bg-white/20 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">{{ $activeCount }}</span>
            </a>

            <a href="{{ route('admin.products.index', ['status' => 'trashed']) }}" 
               class="px-4 py-2 rounded-lg text-sm font-bold transition-colors {{ $isTrashed ? 'bg-red-600 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-red-50 dark:hover:bg-red-900/20' }}">
                <i class="fa-solid fa-trash-can mr-1.5 ml-1.5"></i>
                {{ app()->getLocale() === 'ar' ? 'سلة المحذوفات' : 'Trash Archive' }}
                <span class="ml-1.5 mr-1.5 px-2 py-0.5 text-xs rounded-full {{ $isTrashed ? 'bg-white/20 text-white' : 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300' }}">{{ $trashedCount }}</span>
            </a>
        </div>

        <form method="GET" action="{{ route('admin.products.index') }}" class="shop-toolbar flex items-center justify-between gap-4 flex-wrap">
            @if($isTrashed)
                <input type="hidden" name="status" value="trashed">
            @endif

            <div class="search-wrapper flex-1 min-w-[260px] max-w-md">
                <svg class="search-icon" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control search-input text-sm" placeholder="{{ app()->getLocale() === 'ar' ? 'بحث بالاسم، الرمز SKU، الباركود...' : 'Search formulation, SKU, barcode...' }}">
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                <select name="category_id" onchange="this.form.submit()" class="form-select text-sm w-auto">
                    <option value="">{{ app()->getLocale() === 'ar' ? 'جميع التصنيفات' : 'All Categories' }}</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat['id'] ?? $cat->id }}" {{ request('category_id') == ($cat['id'] ?? $cat->id) ? 'selected' : '' }}>
                            {{ app()->getLocale() === 'ar' ? ($cat['name_ar'] ?? $cat['name_en'] ?? $cat->name_ar) : ($cat['name_en'] ?? $cat->name_en) }}
                        </option>
                    @endforeach
                </select>

                @if(!$isTrashed)
                    <select name="status" onchange="this.form.submit()" class="form-select text-sm w-auto">
                        <option value="">{{ app()->getLocale() === 'ar' ? 'كل الحالات' : 'All Statuses' }}</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="low_stock" {{ request('status') === 'low_stock' ? 'selected' : '' }}>Low Stock Alert</option>
                        <option value="out_of_stock" {{ request('status') === 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                    </select>
                @endif

                <button type="submit" class="btn btn-secondary btn-sm font-bold">
                    <i class="fa-solid fa-filter mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'تصفية' : 'Filter' }}
                </button>
            </div>
        </form>
    </div>

    <!-- Table Action Toolbar (Search, Excel Export, CSV & Print) -->
    <x-admin.table-toolbar 
        table="#productsTable" 
        :title="app()->getLocale() === 'ar' ? 'سجل المنتجات والتركيبات' : 'Products & Formulations Catalog'" 
    />

    <!-- Products Data Table -->
    <div class="card shadow-sm border border-gray-100 dark:border-gray-800">
        <div class="table-responsive" style="border: none; border-radius: 0;">
            <table class="table" id="productsTable">
                <thead>
                    <tr>
                        <th style="width: 40px;" data-no-sort data-no-export><input type="checkbox" class="form-check-input"></th>
                        <th data-sort-type="text">{{ app()->getLocale() === 'ar' ? 'التركيبة' : 'Product Formulation' }}</th>
                        <th data-sort-type="text">{{ app()->getLocale() === 'ar' ? 'الرمز والباركود' : 'SKU / Barcode' }}</th>
                        <th data-sort-type="text">{{ app()->getLocale() === 'ar' ? 'التصنيف' : 'Category' }}</th>
                        <th data-sort-type="number">{{ app()->getLocale() === 'ar' ? 'سعر البيع' : 'Retail Price' }}</th>
                        <th data-sort-type="number">{{ app()->getLocale() === 'ar' ? 'مخزون الأونلاين' : 'Online Stock' }}</th>
                        <th data-sort-type="number">{{ app()->getLocale() === 'ar' ? 'مخزون المستودع' : 'Warehouse Stock' }}</th>
                        <th data-sort-type="text">{{ app()->getLocale() === 'ar' ? 'الصفحة الرئيسية' : 'Landing Page' }}</th>
                        <th data-sort-type="text">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                        <th style="text-align: center;" data-no-sort data-no-export>{{ app()->getLocale() === 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        @php
                            $pId = $product['id'] ?? $product->id ?? 1;
                            $pNameEn = $product['name_en'] ?? $product->name_en ?? 'Formulation';
                            $pNameAr = $product['name_ar'] ?? $product->name_ar ?? '';
                            $pDisplayName = app()->getLocale() === 'ar' && !empty($pNameAr) ? $pNameAr : $pNameEn;
                            $pSku = $product['sku'] ?? $product->sku ?? 'BZ-SKU';
                            $pBarcode = $product['barcode'] ?? $product->barcode ?? '6281100000000';
                            $pCat = $product['category_en'] ?? $product['category_name_en'] ?? 'Precision Cellular';
                            $pPrice = $product['price'] ?? $product->price ?? 0;
                            $pOnlineStock = $product['stock_online'] ?? $product->stock_online ?? 0;
                            $pOfflineStock = $product['stock_offline'] ?? $product->stock_offline ?? 0;
                            $pLowThreshold = $product['low_stock_threshold'] ?? $product->low_stock_threshold ?? 10;
                            $pStatus = $product['status'] ?? $product->status ?? 'active';
                            $rawImage = $product['image'] ?? ($product->primary_image_url ?? 'assets/products/blue-mind.jpg');
                            $pImage = \App\Models\Product::normalizeUrl($rawImage);
                            $isItemTrashed = !empty($product['deleted_at']);
                            $pShowLanding = (bool) ($product['show_on_landing'] ?? $product->show_on_landing ?? false);
                            $pLandingOrder = (int) ($product['landing_order'] ?? $product->landing_order ?? 0);
                            $pLandingBadgeEn = $product['landing_badge_en'] ?? $product->landing_badge_en ?? null;
                            $pLandingBadgeAr = $product['landing_badge_ar'] ?? $product->landing_badge_ar ?? null;
                            $pLandingBadge = app()->getLocale() === 'ar' ? ($pLandingBadgeAr ?: $pLandingBadgeEn) : ($pLandingBadgeEn ?: $pLandingBadgeAr);
                        @endphp
                        <tr>
                            <td><input type="checkbox" class="form-check-input"></td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <img src="{{ $pImage }}" alt="{{ $pNameEn }}" style="width: 44px; height: 44px; border-radius: var(--radius-sm); object-fit: cover; background: var(--color-bg-subtle);" onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';">
                                    <div>
                                        <div class="font-bold text-sm">
                                            @if(!$isItemTrashed)
                                                <a href="{{ route('admin.products.edit', $pId) }}" class="text-primary hover:underline">
                                                    {{ $pDisplayName }}
                                                </a>
                                            @else
                                                <span class="text-gray-700 dark:text-gray-300">{{ $pDisplayName }}</span>
                                            @endif
                                        </div>
                                        @if($pNameAr && app()->getLocale() !== 'ar')
                                            <div class="text-xs text-muted" dir="rtl">
                                                {{ $pNameAr }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="font-mono text-xs">
                                <div>{{ $pSku }}</div>
                                <div class="text-muted">{{ $pBarcode }}</div>
                            </td>
                            <td>
                                <span class="badge badge-neutral text-xs">{{ $pCat }}</span>
                            </td>
                            <td>
                                @php
                                    $pSalePrice = $product['sale_price'] ?? $product->sale_price ?? null;
                                    $pHasDiscount = !empty($pSalePrice) && (float)$pSalePrice < (float)$pPrice && (float)$pSalePrice > 0;
                                    $pDiscPct = $pHasDiscount ? (int) round((((float)$pPrice - (float)$pSalePrice) / (float)$pPrice) * 100) : 0;
                                @endphp
                                @if($pHasDiscount)
                                    <div>
                                        <span class="font-bold font-mono" style="color: var(--color-success, #16a34a);">@currency((float)$pSalePrice)</span>
                                        <div style="display: flex; align-items: center; gap: 0.35rem; margin-top: 2px;">
                                            <span class="text-xs text-muted line-through font-mono">@currency((float)$pPrice)</span>
                                            <span class="badge" style="font-size: 0.65rem; padding: 0.1rem 0.35rem; background: #fee2e2; color: #b91c1c; border-radius: 4px; font-weight: 800;">
                                                -{{ $pDiscPct }}%
                                            </span>
                                        </div>
                                    </div>
                                @else
                                    <span class="font-bold font-mono">@currency((float)$pPrice)</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $pOnlineStock <= $pLowThreshold ? 'badge-warning' : 'badge-success' }}">
                                    {{ $pOnlineStock }} units
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $pOfflineStock <= $pLowThreshold ? 'badge-danger' : 'badge-neutral' }}">
                                    {{ $pOfflineStock }} units
                                </span>
                            </td>
                            <td>
                                @if(!$isItemTrashed)
                                    <button type="button" 
                                            onclick="toggleProductLanding(this, {{ $pId }})"
                                            data-product-id="{{ $pId }}"
                                            data-active="{{ $pShowLanding ? '1' : '0' }}"
                                            class="landing-toggle-btn inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold transition-all shadow-xs border cursor-pointer {{ $pShowLanding ? 'bg-emerald-50 text-emerald-700 border-emerald-300 hover:bg-emerald-100 dark:bg-emerald-950/50 dark:text-emerald-300 dark:border-emerald-800' : 'bg-gray-100 text-gray-500 border-gray-200 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700' }}"
                                            title="{{ app()->getLocale() === 'ar' ? 'تبديل الظهور في الصفحة الرئيسية' : 'Toggle landing showcase' }}">
                                        <span class="w-2 h-2 rounded-full {{ $pShowLanding ? 'bg-emerald-500 ring-2 ring-emerald-300/50 dark:ring-emerald-700/50' : 'bg-gray-400' }} indicator-dot"></span>
                                        <span class="indicator-text">
                                            {{ $pShowLanding ? (app()->getLocale() === 'ar' ? 'معروض' : 'Showcased') : (app()->getLocale() === 'ar' ? 'مخفي' : 'Hidden') }}
                                        </span>
                                        @if($pLandingOrder > 0)
                                            <span class="text-[10px] px-1.5 py-0.2 rounded bg-emerald-200/60 dark:bg-emerald-800/60 text-emerald-900 dark:text-emerald-100 font-mono">#{{ $pLandingOrder }}</span>
                                        @endif
                                    </button>
                                    @if(!empty($pLandingBadge))
                                        <div class="text-[10px] text-primary font-semibold mt-1 max-w-[120px] truncate" title="{{ $pLandingBadge }}">
                                            <i class="fa-solid fa-tag text-[9px] mr-0.5 ml-0.5"></i>{{ $pLandingBadge }}
                                        </div>
                                    @endif
                                @else
                                    <span class="text-xs text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($isItemTrashed)
                                    <span class="badge badge-danger text-xs font-bold">
                                        <i class="fa-solid fa-trash-can mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'محذوف' : 'Trashed' }}
                                    </span>
                                @else
                                    <x-status-badge :status="$pStatus" />
                                @endif
                            </td>
                            <td>
                                <div class="table-actions justify-center flex items-center gap-1.5">
                                    @if(!$isItemTrashed)
                                        <a href="{{ route('admin.products.edit', $pId) }}" class="action-btn" title="{{ __('app.actions.edit') }}">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <a href="{{ route('admin.products.show', $pId) }}" class="action-btn" title="{{ __('app.actions.view') }}">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <button type="button" class="action-btn action-danger cursor-pointer" 
                                                onclick="confirmDelete('{{ route('admin.products.destroy', $pId) }}', '{{ addslashes($pDisplayName) }}', false)" 
                                                title="{{ __('app.actions.delete') }}">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    @else
                                        <button type="button" class="action-btn text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 cursor-pointer" 
                                                onclick="confirmRestore('{{ route('admin.products.restore', $pId) }}', '{{ addslashes($pDisplayName) }}')" 
                                                title="{{ app()->getLocale() === 'ar' ? 'استعادة' : 'Restore' }}">
                                            <i class="fa-solid fa-rotate-left"></i>
                                        </button>
                                        <button type="button" class="action-btn action-danger cursor-pointer" 
                                                onclick="confirmDelete('{{ route('admin.products.force-delete', $pId) }}', '{{ addslashes($pDisplayName) }}', true)" 
                                                title="{{ app()->getLocale() === 'ar' ? 'حذف نهائي فوري' : 'Force Delete Permanently' }}">
                                            <i class="fa-solid fa-radiation"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-8 text-muted">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i class="fa-solid fa-box-open text-3xl text-gray-400"></i>
                                    <p class="text-sm font-semibold">{{ app()->getLocale() === 'ar' ? 'لا توجد عناصر مطابقة في هذا القسم' : 'No formulations found matching criteria' }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-pagination :currentPage="$currentPage" :totalPages="$totalPages" :totalItems="$totalCount ?? count($products)" />
    </div>

    <!-- Scripts for Landing Section Settings & Interactive Toggle -->
    <script>
        function toggleLandingSettingsCollapse() {
            const body = document.getElementById('landingSettingsCollapseBody');
            const icon = document.getElementById('toggleLandingSettingsIcon');
            const isHidden = body.classList.contains('hidden');

            if (isHidden) {
                body.classList.remove('hidden');
                icon.classList.add('rotate-180');
            } else {
                body.classList.add('hidden');
                icon.classList.remove('rotate-180');
            }
        }

        function toggleDetailsSettingsCollapse() {
            const body = document.getElementById('detailsSettingsCollapseBody');
            const icon = document.getElementById('toggleDetailsSettingsIcon');
            const isHidden = body.classList.contains('hidden');

            if (isHidden) {
                body.classList.remove('hidden');
                icon.classList.add('rotate-180');
            } else {
                body.classList.add('hidden');
                icon.classList.remove('rotate-180');
            }
        }

        function toggleButtonSettingsCollapse() {
            const body = document.getElementById('buttonSettingsCollapseBody');
            const icon = document.getElementById('toggleButtonSettingsIcon');
            if (!body) return;
            const isHidden = body.classList.contains('hidden');

            if (isHidden) {
                body.classList.remove('hidden');
                if (icon) icon.classList.add('rotate-180');
            } else {
                body.classList.add('hidden');
                if (icon) icon.classList.remove('rotate-180');
            }
        }

        function updateButtonPreview() {
            const isAr = {{ app()->getLocale() === 'ar' ? 'true' : 'false' }};
            const buyNowEn = document.getElementById('inputBtnBuyNowEn')?.value;
            const buyNowAr = document.getElementById('inputBtnBuyNowAr')?.value;
            const subtextEn = document.getElementById('inputBtnSubtextEn')?.value;
            const subtextAr = document.getElementById('inputBtnSubtextAr')?.value;
            const quickAddEn = document.getElementById('inputBtnQuickAddEn')?.value;
            const quickAddAr = document.getElementById('inputBtnQuickAddAr')?.value;
            const bundleEn = document.getElementById('inputBtnBundleEn')?.value;
            const bundleAr = document.getElementById('inputBtnBundleAr')?.value;
            const iconClass = document.getElementById('inputBtnBuyNowIcon')?.value;

            const prevBuyNowText = document.getElementById('prevBtnBuyNowText');
            if (prevBuyNowText) {
                prevBuyNowText.textContent = isAr ? (buyNowAr || 'إضافة إلى السلة والبروتوكول') : (buyNowEn || 'ADD TO CLINICAL PROTOCOL');
            }

            const prevBuyNowIcon = document.getElementById('prevBtnBuyNowIcon');
            if (prevBuyNowIcon && iconClass) {
                prevBuyNowIcon.className = `fa-solid ${iconClass}`;
            }

            const prevSubtext = document.getElementById('prevBtnBuyNowSubtext');
            if (prevSubtext) {
                const subVal = isAr ? subtextAr : subtextEn;
                if (subVal && subVal.trim() !== '') {
                    prevSubtext.style.display = 'block';
                    prevSubtext.innerHTML = `<i class="fa-solid fa-circle-check text-[9px] mr-1 ml-1"></i><span>${subVal}</span>`;
                } else {
                    prevSubtext.style.display = 'none';
                }
            }

            const prevQuickAdd = document.getElementById('prevBtnQuickAddText');
            if (prevQuickAdd) {
                prevQuickAdd.textContent = isAr ? (quickAddAr || 'إضافة سريعة') : (quickAddEn || 'QUICK ADD');
            }

            const prevBundle = document.getElementById('prevBtnBundleText');
            if (prevBundle) {
                prevBundle.textContent = isAr ? (bundleAr || 'إضافة البروتوكول التكاملي') : (bundleEn || 'ADD COMPLETE PROTOCOL');
            }
        }

        function applyButtonPreset(preset) {
            const presets = {
                clinical: {
                    buy_now_en: 'ADD TO CLINICAL PROTOCOL',
                    buy_now_ar: 'إضافة إلى السلة والبروتوكول',
                    icon: 'fa-cart-shopping',
                    subtext_en: 'Clinically Verified Formulation • 100% Purity',
                    subtext_ar: 'تركيبة سريرية موثقة • نقاء دوائي 100%',
                    quick_add_en: 'QUICK ADD',
                    quick_add_ar: 'إضافة سريعة',
                    bundle_en: 'ADD COMPLETE PROTOCOL TO CART',
                    bundle_ar: 'إضافة البروتوكول التكاملي بالكامل للسلة'
                },
                longevity: {
                    buy_now_en: 'COMMENCE LONGEVITY REGIMEN',
                    buy_now_ar: 'بدء برنامج طول العمر الحيوي',
                    icon: 'fa-capsules',
                    subtext_en: 'Targeted Longevity Biomarkers & Cellular Support',
                    subtext_ar: 'دعم حيوي متقدم للمؤشرات الخلوية والعمر الصحي',
                    quick_add_en: 'RESERVE',
                    quick_add_ar: 'حجز فوري',
                    bundle_en: 'START CELLULAR LONGEVITY STACK',
                    bundle_ar: 'تفعيل باقة تعزيز الخلايا المتكاملة'
                },
                ecommerce: {
                    buy_now_en: 'ADD TO SHOPPING BAG',
                    buy_now_ar: 'أضف إلى حقيبة التسوق',
                    icon: 'fa-bag-shopping',
                    subtext_en: 'Fast Shipping • Satisfaction Guaranteed',
                    subtext_ar: 'شحن سريع ومجاني • ضمان استرجاع موثوق',
                    quick_add_en: 'ADD TO BAG',
                    quick_add_ar: 'أضف للحقيبة',
                    bundle_en: 'BUY COMPLETE BUNDLE NOW',
                    bundle_ar: 'شراء الباقة بالكامل الآن'
                }
            };

            const data = presets[preset];
            if (!data) return;

            if (document.getElementById('inputBtnBuyNowEn')) document.getElementById('inputBtnBuyNowEn').value = data.buy_now_en;
            if (document.getElementById('inputBtnBuyNowAr')) document.getElementById('inputBtnBuyNowAr').value = data.buy_now_ar;
            if (document.getElementById('inputBtnBuyNowIcon')) document.getElementById('inputBtnBuyNowIcon').value = data.icon;
            if (document.getElementById('inputBtnSubtextEn')) document.getElementById('inputBtnSubtextEn').value = data.subtext_en;
            if (document.getElementById('inputBtnSubtextAr')) document.getElementById('inputBtnSubtextAr').value = data.subtext_ar;
            if (document.getElementById('inputBtnQuickAddEn')) document.getElementById('inputBtnQuickAddEn').value = data.quick_add_en;
            if (document.getElementById('inputBtnQuickAddAr')) document.getElementById('inputBtnQuickAddAr').value = data.quick_add_ar;
            if (document.getElementById('inputBtnBundleEn')) document.getElementById('inputBtnBundleEn').value = data.bundle_en;
            if (document.getElementById('inputBtnBundleAr')) document.getElementById('inputBtnBundleAr').value = data.bundle_ar;

            updateButtonPreview();
        }

        async function toggleProductLanding(button, productId) {
            const currentActive = button.getAttribute('data-active') === '1';
            const originalHtml = button.innerHTML;
            
            // Show loading spinner inside button
            button.disabled = true;
            button.style.opacity = '0.7';

            try {
                const response = await fetch(`{{ url('admin/products') }}/${productId}/toggle-landing`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    const newActive = data.show_on_landing;
                    button.setAttribute('data-active', newActive ? '1' : '0');

                    const isAr = {{ app()->getLocale() === 'ar' ? 'true' : 'false' }};
                    const textLabel = newActive ? (isAr ? 'معروض' : 'Showcased') : (isAr ? 'مخفي' : 'Hidden');

                    // Reset classes
                    if (newActive) {
                        button.className = "landing-toggle-btn inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold transition-all shadow-xs border cursor-pointer bg-emerald-50 text-emerald-700 border-emerald-300 hover:bg-emerald-100 dark:bg-emerald-950/50 dark:text-emerald-300 dark:border-emerald-800";
                        button.innerHTML = `
                            <span class="w-2 h-2 rounded-full bg-emerald-500 ring-2 ring-emerald-300/50 dark:ring-emerald-700/50 indicator-dot"></span>
                            <span class="indicator-text">${textLabel}</span>
                        `;
                    } else {
                        button.className = "landing-toggle-btn inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold transition-all shadow-xs border cursor-pointer bg-gray-100 text-gray-500 border-gray-200 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700";
                        button.innerHTML = `
                            <span class="w-2 h-2 rounded-full bg-gray-400 indicator-dot"></span>
                            <span class="indicator-text">${textLabel}</span>
                        `;
                    }

                    // Update counter in hero card if it exists
                    const countBadge = document.getElementById('landingCountBadge');
                    if (countBadge) {
                        let currentCount = parseInt(countBadge.textContent.trim(), 10) || 0;
                        countBadge.textContent = newActive ? (currentCount + 1) : Math.max(0, currentCount - 1);
                    }

                    // Toast notification if helper exists, otherwise clean floating message
                    if (typeof showToast === 'function') {
                        showToast(data.message, 'success');
                    }
                } else {
                    alert(data.message || 'Error toggling product landing visibility');
                    button.innerHTML = originalHtml;
                }
            } catch (err) {
                console.error(err);
                alert('Connection error while toggling landing state');
                button.innerHTML = originalHtml;
            } finally {
                button.disabled = false;
                button.style.opacity = '1';
            }
        }
    </script>
</x-layouts.admin>
