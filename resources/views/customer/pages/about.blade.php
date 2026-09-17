<x-layouts.customer :title="'OUR STORY & ABOUT US — ' . __('app.brand_name')" :description="'Discover the origins, clinical research standards (RS), and cellular biology behind BLUE ZONE Longevity & Cellular Health.'">
    <!-- Who We Are   Section -->
    <section id="who-we-are" class="py-24 bg-[#F6F5EF] dark:bg-[#031827] border-b border-[#0A4F78]/10 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">

                <!-- Image Column with Dynamic Nodes -->
                <div class="lg:col-span-6 relative">
                    <div class="relative rounded-3xl overflow-hidden shadow-2xl border border-[#0A4F78]/20 group">
                        <img src="{{ asset('assets/images/story-lifestyle.webp') }}" alt="Blue Zone Centenarian Lifestyle"
                            onerror="this.onerror=null; this.src='{{ asset('assets/images/story-lifestyle.jpg') }}';"
                            width="800" height="460" loading="lazy" decoding="async"
                            class="w-full h-[460px] object-cover group-hover:scale-105 transition-transform duration-700" />
                        <div class="absolute inset-0 bg-gradient-to-t from-[#031827]/80 via-transparent to-transparent">
                        </div>

                        <!-- Floating Overlay Stats Card -->
                        <div
                            class="absolute bottom-6 left-6 right-6 p-6 rounded-2xl bg-[#031827]/85 backdrop-blur-md border border-[#2A8FC2]/40 text-white space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-[10px] font-black uppercase tracking-[0.25em] text-[#2A8FC2]">
                                    {{ __('app.research_dossier') }}
                                </span>

                                <span class="w-2 h-2 rounded-full bg-[#67B34A] animate-pulse"></span>
                            </div>

                            <p class="text-sm font-bold text-[#E8DCC4] leading-relaxed">
                                "{{ __('app.research_dossier_quote') }}"
                            </p>

                            <div class="grid grid-cols-3 gap-2 pt-2 border-t border-[#0A4F78]/40 text-center">
                                <div>
                                    <span class="block text-lg font-black text-[#2A8FC2]">100+</span>
                                    <span class="text-[9px] font-bold uppercase tracking-wider text-white/70">
                                        {{ __('app.cententarians') }}
                                    </span>
                                </div>

                                <div>
                                    <span class="block text-lg font-black text-[#2A8FC2]">5</span>
                                    <span class="text-[9px] font-bold uppercase tracking-wider text-white/70">
                                        {{ __('app.blue_zones') }}
                                    </span>
                                </div>

                                <div>
                                    <span class="block text-lg font-black text-[#67B34A]">100%</span>
                                    <span class="text-[9px] font-bold uppercase tracking-wider text-white/70">
                                        {{ __('app.bio_identical') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Editorial Content Column -->
                <div class="lg:col-span-6 space-y-6">
                    <span
                        class="inline-block text-xs font-black uppercase tracking-[0.3em] text-[#0A4F78] dark:text-[#2A8FC2]">
                        {{ __('app.who_is_blue_zone') }}
                    </span>

                    <h2
                        class="text-3xl sm:text-5xl font-black text-[#031827] dark:text-[#F6F5EF] tracking-tight leading-tight">
                        {{ __('app.inspired_by_the_worlds_longest_lived') }}
                    </h2>

                    <div class="w-16 h-1.5 bg-[#0A4F78] dark:bg-[#2A8FC2] rounded-full"></div>

                    <p class="text-base text-[#031827]/80 dark:text-[#F6F5EF]/80 font-medium leading-relaxed">
                        {{ __('app.blue_zone_intro') }}
                    </p>

                    <div class="space-y-3 pt-2">

                        <div class="flex items-start gap-3">
                            <span
                                class="w-6 h-6 rounded-full bg-[#67B34A]/20 text-[#67B34A] flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">
                                <i class="fa-solid fa-check"></i>
                            </span>

                            <p class="text-xs text-[#031827]/75 dark:text-[#F6F5EF]/75 font-semibold">
                                {{ __('app.blue_zone_benefit_1') }}
                            </p>
                        </div>

                        <div class="flex items-start gap-3">
                            <span
                                class="w-6 h-6 rounded-full bg-[#67B34A]/20 text-[#67B34A] flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">
                                <i class="fa-solid fa-check"></i>
                            </span>

                            <p class="text-xs text-[#031827]/75 dark:text-[#F6F5EF]/75 font-semibold">
                                {{ __('app.blue_zone_benefit_2') }}
                            </p>
                        </div>

                        <div class="flex items-start gap-3">
                            <span
                                class="w-6 h-6 rounded-full bg-[#67B34A]/20 text-[#67B34A] flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">
                                <i class="fa-solid fa-check"></i>
                            </span>

                            <p class="text-xs text-[#031827]/75 dark:text-[#F6F5EF]/75 font-semibold">
                                {{ __('app.blue_zone_benefit_3') }}
                            </p>
                        </div>

                    </div>

                    <div class="pt-4 flex flex-wrap gap-4">

                        <a href="#philosophy"
                            class="px-7 py-3.5 bg-[#0A4F78] hover:bg-[#062B49] text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all shadow-md btn-sheen">
                            {{ __('app.our_philosophy') }}
                            <i class="fa-solid fa-arrow-right rtl:rotate-180 ml-1.5"></i>
                        </a>

                        <a href="{{ route('customer.pages.science') }}"
                            class="px-7 py-3.5 border border-[#0A4F78]/30 hover:border-[#0A4F78] text-[#031827] dark:text-[#F6F5EF] text-xs font-extrabold uppercase tracking-widest rounded-xl transition-colors">
                            {{ __('app.our_science') }}
                        </a>

                    </div>
                </div>


            </div>
        </div>
    </section>
    <!-- PHILOSOPHY SECTION -->
    <!-- 04. WHAT WE BELIEVE (THE 6 PILLARS) -->
    @php
        $isArabic = app()->getLocale() === 'ar';
        $locale = $isArabic ? 'ar' : 'en';

        // Layout format, accent theme, card surface style, toggles
        $pillarsFormat = \App\Models\Setting::get('pillars_layout_format', 'orbital_matrix');
        $pillarsAccent = \App\Models\Setting::get('pillars_accent_color', 'green');
        $pillarsCardStyle = \App\Models\Setting::get('pillars_card_style', 'adaptive');
        $showOrbital = (bool) \App\Models\Setting::get('pillars_show_orbital', true);
        $showTags = (bool) \App\Models\Setting::get('pillars_show_tags', true);
        $showNumbers = (bool) \App\Models\Setting::get('pillars_show_numbers', true);

        $accentThemes = [
            'green' => [
                'hex' => '#67B34A',
                'rgba_bg' => 'rgba(103, 179, 74, 0.12)',
                'rgba_border' => 'rgba(103, 179, 74, 0.3)',
                'text_class' => 'text-[#67B34A]',
                'bg_class' => 'bg-[#67B34A]',
                'border_class' => 'border-[#67B34A]',
            ],
            'cyan' => [
                'hex' => '#2A8FC2',
                'rgba_bg' => 'rgba(42, 143, 194, 0.12)',
                'rgba_border' => 'rgba(42, 143, 194, 0.3)',
                'text_class' => 'text-[#2A8FC2]',
                'bg_class' => 'bg-[#2A8FC2]',
                'border_class' => 'border-[#2A8FC2]',
            ],
            'navy' => [
                'hex' => '#0A4F78',
                'rgba_bg' => 'rgba(10, 79, 120, 0.12)',
                'rgba_border' => 'rgba(10, 79, 120, 0.3)',
                'text_class' => 'text-[#0A4F78]',
                'bg_class' => 'bg-[#0A4F78]',
                'border_class' => 'border-[#0A4F78]',
            ],
            'amber' => [
                'hex' => '#F59E0B',
                'rgba_bg' => 'rgba(245, 158, 11, 0.12)',
                'rgba_border' => 'rgba(245, 158, 11, 0.3)',
                'text_class' => 'text-amber-500',
                'bg_class' => 'bg-amber-500',
                'border_class' => 'border-amber-500',
            ],
            'purple' => [
                'hex' => '#8B5CF6',
                'rgba_bg' => 'rgba(139, 92, 246, 0.12)',
                'rgba_border' => 'rgba(139, 92, 246, 0.3)',
                'text_class' => 'text-purple-500',
                'bg_class' => 'bg-purple-500',
                'border_class' => 'border-purple-500',
            ],
        ];
        $activeTheme = $accentThemes[$pillarsAccent] ?? $accentThemes['green'];

        $cardSurfaceClass = match($pillarsCardStyle) {
            'glassmorphism' => 'bg-white/80 dark:bg-[#031827]/80 backdrop-blur-md border border-white/20 shadow-xl',
            'elevated_card' => 'bg-white dark:bg-[#0a2238] shadow-2xl border border-[#0A4F78]/10',
            default => 'bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/20 shadow-xl',
        };

        // Header values
        $defaultEyebrow = '';
        $defaultHeadingPrefix = $isArabic ? 'عن' : 'About';
        $defaultHeadingHighlight = $isArabic ? 'بلو زون' : 'Blue Zone';
        $defaultDescription = $isArabic 
            ? 'انطلاقاً من مفهوم المناطق الزرقاء (Blue Zones) — تلك المناطق التي يعيش فيها الناس حياة أطول وأكثر صحة — تتطلع بلو زون للصناعات الدوائية إلى تعزيز أنماط الحياة الصحية وجودة العيش من خلال تركيبات متقدمة عالية الجودة للصحة الخلوية والمكملات الغذائية المتخصصة.'
            : 'Inspired by the concept of Blue Zones—regions where people live longer, healthier lives— Blue Zone Pharmaceuticals envisions promoting healthier lives and enhanced well-being through high-quality nutraceutical and cellular health formulations.';
        
        $eyebrow = \App\Models\Setting::get("pillars_eyebrow_{$locale}", $defaultEyebrow);
        $headingPrefix = \App\Models\Setting::get("pillars_heading_prefix_{$locale}", $defaultHeadingPrefix);
        $headingHighlight = \App\Models\Setting::get("pillars_heading_highlight_{$locale}", $defaultHeadingHighlight);
        $sectionDesc = \App\Models\Setting::get("pillars_description_{$locale}", $defaultDescription);

        // Discard old placeholder text if present in database
        if (str_contains($eyebrow, 'COMPREHENSIVE COGNITIVE') || str_contains($eyebrow, 'COGNITIVE SUPPORT')) {
            $eyebrow = $defaultEyebrow;
        }
        if (str_contains($headingPrefix, 'THE 6 PILLARS') || str_contains($headingPrefix, 'ركائز بلو مايند')) {
            $headingPrefix = $defaultHeadingPrefix;
        }
        if (trim($headingHighlight) === 'BLUE MIND' && $headingPrefix !== 'About') {
            $headingHighlight = $defaultHeadingHighlight;
        }
        if (str_contains($sectionDesc, 'Fuel your mind') || str_contains($sectionDesc, 'safeguard your dietary intake')) {
            $sectionDesc = $defaultDescription;
        }

        $centerTitle = \App\Models\Setting::get("pillars_center_title_{$locale}", ($isArabic ? 'بلو زون' : 'BLUE ZONE'));
        $centerSubtitle = \App\Models\Setting::get("pillars_center_subtitle_{$locale}", ($isArabic ? 'الدوائية' : 'PHARMA'));

        // Default Pillars content definitions (About Blue Zone Core Elements)
        $defaultPillars = [
            1 => [
                'icon' => 'fa-solid fa-eye',
                'menu_en' => 'VISION',
                'menu_ar' => 'الرؤية',
                'title_en' => 'Vision',
                'title_ar' => 'رؤيتنا',
                'tag_en' => 'Evidence-Based Nutrition',
                'tag_ar' => 'حلول غذائية قائمة على الدليل العلمي',
                'desc_en' => '<p class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 leading-relaxed">To become one of the leading evidence-based dietary supplement companies in Egypt and the Middle East, improving people\'s quality of life through innovative, scientifically formulated nutritional solutions, manufactured with stringent quality standards, and communicated with clarity and transparency.</p>',
                'desc_ar' => '<p class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 leading-relaxed">أن نصبح إحدى الشركات الرائدة في مجال المكملات الغذائية القائمة على الدليل العلمي في مصر والشرق الأوسط، والارتقاء بجودة حياة الناس من خلال حلول غذائية مبتكرة ومصاغة علمياً، ومصنعة وفقاً لأعلى معايير الجودة الصارمة، مع التواصل بكل وضوح وشفافية.</p>'
            ],
            2 => [
                'icon' => 'fa-solid fa-bullseye',
                'menu_en' => 'MISSION',
                'menu_ar' => 'الرسالة',
                'title_en' => 'Mission',
                'title_ar' => 'رسالتنا',
                'tag_en' => 'Healthcare Value & Excellence',
                'tag_ar' => 'معايير استثنائية وقيمة مستدامة',
                'desc_en' => '<p class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 leading-relaxed">Blue Zone Pharmaceuticals is committed to developing premium-quality dietary supplements that combine scientific evidence, exceptional manufacturing standards, and innovative branding to support healthier lives while creating long-term value for healthcare professionals, patients, and business partners.</p>',
                'desc_ar' => '<p class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 leading-relaxed">تلتزم بلو زون للصناعات الدوائية بتطوير مكملات غذائية عالية الجودة تجمع بين الأدلة العلمية، ومعايير التصنيع الاستثنائية، والهوية المبتكرة لدعم حياة أكثر صحة، مع خلق قيمة مستدامة طويلة الأمد لمتخصصي الرعاية الصحية والمرضى وشركاء الأعمال.</p>'
            ],
            3 => [
                'icon' => 'fa-solid fa-gem',
                'menu_en' => 'CORE VALUES',
                'menu_ar' => 'القيم الجوهرية',
                'title_en' => 'Core Values',
                'title_ar' => 'قيمنا الجوهرية',
                'tag_en' => 'Integrity, Quality & Innovation',
                'tag_ar' => 'النزاهة، الجودة والابتكار',
                'desc_en' => '<div class="space-y-2.5 text-xs sm:text-sm leading-relaxed"><div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-[#0A4F78]/10"><strong class="text-[#0A4F78] dark:text-[#2A8FC2] block font-bold mb-0.5"><i class="fa-solid fa-flask-vial mr-1.5 text-xs text-[#67B34A]"></i>Scientific Integrity</strong><p class="text-[#031827]/80 dark:text-[#F6F5EF]/80 text-[11px] sm:text-xs">We believe in science-led decisions, responsible formulation, and clear communication grounded in reliable information.</p></div><div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-[#0A4F78]/10"><strong class="text-[#0A4F78] dark:text-[#2A8FC2] block font-bold mb-0.5"><i class="fa-solid fa-shield-halved mr-1.5 text-xs text-[#67B34A]"></i>Quality Without Compromise</strong><p class="text-[#031827]/80 dark:text-[#F6F5EF]/80 text-[11px] sm:text-xs">We maintain high standards throughout product selection and development, with careful attention to quality, safety, and consistency.</p></div><div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-[#0A4F78]/10"><strong class="text-[#0A4F78] dark:text-[#2A8FC2] block font-bold mb-0.5"><i class="fa-solid fa-lightbulb mr-1.5 text-xs text-[#67B34A]"></i>Innovation</strong><p class="text-[#031827]/80 dark:text-[#F6F5EF]/80 text-[11px] sm:text-xs">We continuously explore better ideas, ingredients, and approaches to create thoughtful products that respond to evolving health and wellness needs.</p></div><div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-[#0A4F78]/10"><strong class="text-[#0A4F78] dark:text-[#2A8FC2] block font-bold mb-0.5"><i class="fa-solid fa-handshake mr-1.5 text-xs text-[#67B34A]"></i>Trust & Transparency</strong><p class="text-[#031827]/80 dark:text-[#F6F5EF]/80 text-[11px] sm:text-xs">We build lasting relationships through consistency, responsibility, and straightforward information about our products, ingredients, and standards.</p></div><div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-[#0A4F78]/10"><strong class="text-[#0A4F78] dark:text-[#2A8FC2] block font-bold mb-0.5"><i class="fa-solid fa-heart-pulse mr-1.5 text-xs text-[#67B34A]"></i>Patient First & Continuous Improvement</strong><p class="text-[#031827]/80 dark:text-[#F6F5EF]/80 text-[11px] sm:text-xs">We keep the needs, well-being, and experience of the people we serve at the heart of what we do, always learning and raising our standards across everything.</p></div></div>',
                'desc_ar' => '<div class="space-y-2.5 text-xs sm:text-sm leading-relaxed"><div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-[#0A4F78]/10"><strong class="text-[#0A4F78] dark:text-[#2A8FC2] block font-bold mb-0.5"><i class="fa-solid fa-flask-vial ml-1.5 text-xs text-[#67B34A]"></i>النزاهة العلمية</strong><p class="text-[#031827]/80 dark:text-[#F6F5EF]/80 text-[11px] sm:text-xs">نؤمن بالقرارات المبنية على العلم، والصياغة المسؤولة، والتواصل الواضح القائم على معلومات موثوقة.</p></div><div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-[#0A4F78]/10"><strong class="text-[#0A4F78] dark:text-[#2A8FC2] block font-bold mb-0.5"><i class="fa-solid fa-shield-halved ml-1.5 text-xs text-[#67B34A]"></i>الجودة دون مساومة</strong><p class="text-[#031827]/80 dark:text-[#F6F5EF]/80 text-[11px] sm:text-xs">نحافظ على معايير رفيعة طوال مراحل اختيار وتطوير المنتجات، مع عناية دقيقة بالجودة والسلامة والاتساق.</p></div><div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-[#0A4F78]/10"><strong class="text-[#0A4F78] dark:text-[#2A8FC2] block font-bold mb-0.5"><i class="fa-solid fa-lightbulb ml-1.5 text-xs text-[#67B34A]"></i>الابتكار</strong><p class="text-[#031827]/80 dark:text-[#F6F5EF]/80 text-[11px] sm:text-xs">نستكشف باستمرار أفكاراً ومكونات وحلولاً أفضل لابتكار منتجات مدروسة تلبي الاحتياجات الصحية المتطورة.</p></div><div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-[#0A4F78]/10"><strong class="text-[#0A4F78] dark:text-[#2A8FC2] block font-bold mb-0.5"><i class="fa-solid fa-handshake ml-1.5 text-xs text-[#67B34A]"></i>الثقة والشفافية</strong><p class="text-[#031827]/80 dark:text-[#F6F5EF]/80 text-[11px] sm:text-xs">نبني علاقات دائمة قائمة على الاتساق والمسؤولية والشفافية الصادقة حول منتجاتنا ومكوناتنا ومعاييرنا.</p></div><div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-[#0A4F78]/10"><strong class="text-[#0A4F78] dark:text-[#2A8FC2] block font-bold mb-0.5"><i class="fa-solid fa-heart-pulse ml-1.5 text-xs text-[#67B34A]"></i>المريض أولاً والتحسين المستمر</strong><p class="text-[#031827]/80 dark:text-[#F6F5EF]/80 text-[11px] sm:text-xs">نضع احتياجات وعافية وتجربة مستخدمي منتجاتنا في صميم كل ما نقوم به، مؤمنين بأن هناك دائماً فرصة للتعلم ورفع المعايير في كل ما نقدمه.</p></div></div>'
            ],
            4 => [
                'icon' => 'fa-solid fa-handshake-angle',
                'menu_en' => 'OUR COMMITMENT',
                'menu_ar' => 'التزامنا',
                'title_en' => 'Our Commitment',
                'title_ar' => 'التزامنا',
                'tag_en' => 'Trust & Responsible Communication',
                'tag_ar' => 'أمانة وتواصل مسؤول',
                'desc_en' => '<p class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 leading-relaxed mb-3">At Blue Zone Pharmaceuticals, we are committed to developing reliable nutraceutical products based on scientific evidence and stringent quality standards.</p><p class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 leading-relaxed">We prioritize transparency and responsible communication, aiming to provide clear product information and support healthier lives with integrity and care.</p>',
                'desc_ar' => '<p class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 leading-relaxed mb-3">في بلو زون للصناعات الدوائية، نلتزم بتطوير منتجات غذائية علاجية موثوقة تستند إلى الأدلة العلمية ومعايير الجودة الصارمة.</p><p class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 leading-relaxed">نضع الشفافية والتواصل المسؤول على رأس أولوياتنا، بهدف تقديم معلومات واضحة وموثوقة عن المنتجات ودعم حياة أكثر صحة بنزاهة ورعاية حقيقية.</p>'
            ],
            5 => [
                'icon' => 'fa-solid fa-shield-halved',
                'menu_en' => 'QUALITY & SAFETY',
                'menu_ar' => 'الجودة والسلامة',
                'title_en' => 'Quality and Safety',
                'title_ar' => 'الجودة والسلامة',
                'tag_en' => 'Stringent Quality & Thoughtful Formulation',
                'tag_ar' => 'معايير صارمة وصياغة مدروسة',
                'desc_en' => '<p class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 leading-relaxed mb-3">We follow strict quality standards to ensure reliable products.</p><p class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 leading-relaxed">Every ingredient in a BlueZone product is carefully selected and evaluated for quality, safety, and suitability, reflecting our commitment to thoughtful formulation and creating products designed to add meaningful value to everyday health and well-being.</p>',
                'desc_ar' => '<p class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 leading-relaxed mb-3">نتبع معايير جودة صارمة لضمان منتجات موثوقة تدعم صحة الإنسان.</p><p class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 leading-relaxed">يتم اختيار كل مكون في منتجات بلو زون وتقييمه بعناية فائقة لضمان الجودة والسلامة والملاءمة، مما يعكس التزامنا بالصياغة المدروسة وابتكار منتجات تهدف إلى إضفاء قيمة ملموسة على الصحة اليومية والعافية العامة.</p>'
            ],
        ];

        $bluezonePillars = [];
        for ($i = 1; $i <= count($defaultPillars); $i++) {
            $pDef = $defaultPillars[$i];
            $pad = str_pad($i, 2, '0', STR_PAD_LEFT);
            $num = \App\Models\Setting::get("pillars_item_{$i}_num", $pad);
            $icon = \App\Models\Setting::get("pillars_item_{$i}_icon", $pDef['icon']);
            $menuTitle = \App\Models\Setting::get("pillars_item_{$i}_menu_{$locale}", $pDef["menu_{$locale}"]);
            $title = \App\Models\Setting::get("pillars_item_{$i}_title_{$locale}", $pDef["title_{$locale}"]);
            $tag = \App\Models\Setting::get("pillars_item_{$i}_tag_{$locale}", $pDef["tag_{$locale}"]);
            $desc = \App\Models\Setting::get("pillars_item_{$i}_desc_{$locale}", $pDef["desc_{$locale}"]);

            if (str_contains($title, 'Cognitive Fuel') || str_contains($title, 'Neuro-Protection') || str_contains($desc, 'Fuel your mind')) {
                $menuTitle = $pDef["menu_{$locale}"];
                $title = $pDef["title_{$locale}"];
                $tag = $pDef["tag_{$locale}"];
                $desc = $pDef["desc_{$locale}"];
            }

            $bluezonePillars[] = [
                'num' => $num,
                'icon' => $icon,
                'menu_title' => $menuTitle,
                'title' => $title,
                'tag' => $tag,
                'desc' => $desc,
            ];
        }
    @endphp

    <section id="philosophy"
        class="py-14 sm:py-20 bg-white dark:bg-[#062B49] border-b border-[#0A4F78]/10 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 sm:space-y-12">
            <!-- Section Header -->
            <div class="text-center max-w-3xl mx-auto space-y-3">
                @if(!empty(trim($eyebrow)))
                    <span
                        class="text-[10px] sm:text-[11px] font-extrabold uppercase tracking-[0.25em] sm:tracking-[0.3em]"
                        style="color: {{ $activeTheme['hex'] }};">
                        {{ $eyebrow }}
                    </span>
                @endif
                <h2
                    class="text-2xl sm:text-4xl lg:text-5xl font-light text-[#031827] dark:text-[#F6F5EF] tracking-tight">
                    @if(!empty(trim($headingPrefix)))
                        {{ $headingPrefix }}
                    @endif
                    @if(!empty(trim($headingHighlight)))
                        <span class="font-bold" style="color: {{ $activeTheme['hex'] }};">{{ $headingHighlight }}</span>
                    @endif
                </h2>
                @if(!empty(trim($sectionDesc)))
                    <p
                        class="text-xs sm:text-sm text-[#031827]/70 dark:text-[#F6F5EF]/70 font-medium max-w-xl mx-auto leading-relaxed">
                        {{ $sectionDesc }}
                    </p>
                @endif
            </div>

            {{-- FORMAT 1: INTERACTIVE ORBITAL MATRIX --}}
            @if ($pillarsFormat === 'orbital_matrix')
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8 lg:gap-12 items-center lg:min-h-[520px]">
                    <!-- Column 1: Pillar Selectors -->
                    <div class="{{ $showOrbital ? 'lg:col-span-4' : 'lg:col-span-5' }} space-y-2">
                        <!-- Mobile Horizontal Pill Nav -->
                        <div class="lg:hidden flex overflow-x-auto gap-2 pb-2 scrollbar-none border-b border-[#0A4F78]/15">
                            @foreach ($bluezonePillars as $i => $pillar)
                                <button onclick="BLUEZONE_PILLARS.select({{ $i }})"
                                    class="pillar-nav-btn shrink-0 px-3 sm:px-4 py-2 rounded-full text-[11px] sm:text-xs font-bold transition-all"
                                    style="{{ $i === 0 ? 'background: ' . $activeTheme['hex'] . '; color: #fff;' : 'background: rgba(10, 79, 120, 0.08); color: var(--color-text);' }}"
                                    data-index="{{ $i }}">
                                    @if($showNumbers)<span class="font-mono">{{ $pillar['num'] }}</span>@endif
                                    <span>{{ $pillar['menu_title'] }}</span>
                                </button>
                            @endforeach
                        </div>

                        <!-- Desktop Vertical Selectors -->
                        <div class="hidden lg:block divide-y divide-[#0A4F78]/15 dark:divide-[#0A4F78]/30 border-y border-[#0A4F78]/15 dark:border-[#0A4F78]/30">
                            @foreach ($bluezonePillars as $i => $pillar)
                                <button onclick="BLUEZONE_PILLARS.select({{ $i }})"
                                    onmouseenter="BLUEZONE_PILLARS.select({{ $i }})"
                                    class="pillar-desktop-btn w-full py-4 px-3 flex items-center gap-4 text-left rtl:text-right transition-all duration-300 group cursor-pointer border-l-4 rtl:border-r-4 rtl:border-l-0"
                                    style="{{ $i === 0 ? 'border-color: ' . $activeTheme['hex'] . '; background: ' . $activeTheme['rgba_bg'] . ';' : 'border-color: transparent;' }}"
                                    data-index="{{ $i }}">
                                    @if($showNumbers)
                                        <span class="text-xs font-extrabold font-mono"
                                            style="color: {{ $i === 0 ? $activeTheme['hex'] : 'rgba(3, 24, 39, 0.4)' }};">
                                            {{ $pillar['num'] }}
                                        </span>
                                    @endif
                                    <span class="w-6 text-center text-sm" style="color: {{ $i === 0 ? $activeTheme['hex'] : '#94A3B8' }};">
                                        @if(str_starts_with(trim($pillar['icon']), '<svg'))
                                            {!! $pillar['icon'] !!}
                                        @else
                                            <i class="{{ $pillar['icon'] }}"></i>
                                        @endif
                                    </span>
                                    <span class="text-sm tracking-wider uppercase {{ $i === 0 ? 'font-bold' : 'font-medium text-[#031827]/80 dark:text-[#F6F5EF]/80 group-hover:opacity-100' }}"
                                        style="{{ $i === 0 ? 'color: ' . $activeTheme['hex'] . ';' : '' }}">
                                        {{ $pillar['menu_title'] }}
                                    </span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Column 2: Orbital SVG Animation -->
                    @if ($showOrbital)
                        @php
                            $totalPillars = count($bluezonePillars);
                            $radius = 110;
                            $centerX = 150;
                            $centerY = 150;
                            $spokeCoords = [];
                            for ($idx = 0; $idx < $totalPillars; $idx++) {
                                $angle = deg2rad(-90 + ($idx * (360 / $totalPillars)));
                                $x = round($centerX + $radius * cos($angle));
                                $y = round($centerY + $radius * sin($angle));
                                $spokeCoords[] = ['x' => $x, 'y' => $y];
                            }
                        @endphp
                        <div class="hidden lg:flex lg:col-span-4 items-center justify-center relative py-4">
                            <div class="w-56 h-56 xl:w-72 xl:h-72 relative flex items-center justify-center">
                                <svg class="w-full h-full" viewBox="0 0 300 300" fill="none">
                                    <circle cx="150" cy="150" r="110" stroke="#0A4F78" stroke-width="1.5"
                                        stroke-opacity="0.25" stroke-dasharray="4 4" />
                                    <circle cx="150" cy="150" r="70" stroke="#2A8FC2" stroke-width="1"
                                        stroke-opacity="0.2" />

                                    @foreach ($spokeCoords as $k => $coord)
                                        <line id="spoke-{{ $k }}" x1="150" y1="150" x2="{{ $coord['x'] }}" y2="{{ $coord['y'] }}"
                                            stroke="{{ $k === 0 ? $activeTheme['hex'] : '#2A8FC2' }}" 
                                            stroke-width="{{ $k === 0 ? 2 : 1 }}" 
                                            opacity="{{ $k === 0 ? 0.9 : 0.3 }}" />
                                    @endforeach

                                    @foreach ($spokeCoords as $k => $coord)
                                        <circle id="node-{{ $k }}" cx="{{ $coord['x'] }}" cy="{{ $coord['y'] }}" 
                                            r="{{ $k === 0 ? 10 : 7 }}" 
                                            fill="{{ $k === 0 ? $activeTheme['hex'] : '#0A4F78' }}"
                                            stroke="{{ $k === 0 ? '#FFFFFF' : '#2A8FC2' }}" 
                                            stroke-width="{{ $k === 0 ? 2 : 1.5 }}" 
                                            class="transition-all duration-300 cursor-pointer"
                                            onclick="BLUEZONE_PILLARS.select({{ $k }})" />
                                    @endforeach
                                </svg>

                                <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                                    <div class="w-20 h-20 xl:w-24 xl:h-24 rounded-full bg-[#031827] border-2 border-[#2A8FC2] flex flex-col items-center justify-center p-2 shadow-xl">
                                        <span class="text-[8px] xl:text-[9px] font-black tracking-widest text-[#2A8FC2] uppercase">
                                            {{ $centerTitle }}
                                        </span>
                                        <span class="text-[7px] xl:text-[8px] font-bold uppercase tracking-wider mt-0.5" style="color: {{ $activeTheme['hex'] }};">
                                            {{ $centerSubtitle }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Column 3: Active Pillar Content Display Panel (Text Only - Controlled By Admin) -->
                    <div class="{{ $showOrbital ? 'lg:col-span-4' : 'lg:col-span-7' }} p-5 sm:p-6 lg:p-7 rounded-2xl sm:rounded-3xl {{ $cardSurfaceClass }} transition-all duration-500 relative min-h-[460px] lg:min-h-[500px] flex flex-col justify-between"
                        id="pillar-content-panel">
                        <div class="space-y-4">
                            <div class="flex justify-between items-center pb-3 border-b border-[#0A4F78]/15 dark:border-[#0A4F78]/30">
                                <div class="flex items-center gap-3">
                                    @if($showNumbers)
                                        <span id="pillar-active-num"
                                            class="text-3xl sm:text-4xl font-light font-mono leading-none"
                                            style="color: {{ $activeTheme['hex'] }};">
                                            {{ $bluezonePillars[0]['num'] }}
                                        </span>
                                        <div class="h-6 w-[1px] bg-[#0A4F78]/20 dark:bg-[#0A4F78]/40"></div>
                                    @endif
                                    <span class="text-[10px] sm:text-[11px] font-black tracking-widest text-[#0A4F78] dark:text-[#2A8FC2] uppercase">
                                        {{ $isArabic ? 'ملف التعريف' : 'PROFILE' }}
                                    </span>
                                </div>
                                <span id="pillar-active-icon-box"
                                    class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center shrink-0 text-base"
                                    style="background: {{ $activeTheme['rgba_bg'] }}; color: {{ $activeTheme['hex'] }};">
                                    @if(str_starts_with(trim($bluezonePillars[0]['icon']), '<svg'))
                                        {!! $bluezonePillars[0]['icon'] !!}
                                    @else
                                        <i class="{{ $bluezonePillars[0]['icon'] }}"></i>
                                    @endif
                                </span>
                            </div>

                            <div class="space-y-2.5">
                                <h3 id="pillar-active-title"
                                    class="text-lg sm:text-xl font-bold text-[#031827] dark:text-[#F6F5EF] tracking-tight leading-snug">
                                    {{ $bluezonePillars[0]['title'] }}
                                </h3>
                                <div id="pillar-active-desc"
                                    class="text-xs sm:text-sm text-[#031827]/80 dark:text-[#F6F5EF]/80 leading-relaxed max-h-[300px] sm:max-h-[320px] overflow-y-auto pr-2"
                                    style="scrollbar-width: thin; scrollbar-color: rgba(10, 79, 120, 0.3) transparent;">
                                    {!! $bluezonePillars[0]['desc'] !!}
                                </div>
                            </div>
                        </div>

                        @if ($showTags)
                            <div class="pt-4 border-t border-[#0A4F78]/15 dark:border-[#0A4F78]/30 space-y-1.5" id="pillar-active-tag-wrap">
                                <span class="block text-[10px] font-extrabold uppercase tracking-widest text-[#0A4F78] dark:text-[#2A8FC2]">
                                    {{ __('app.philosophy.impact_label') }}
                                </span>
                                <div id="pillar-active-tag"
                                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-bold"
                                    style="background: {{ $activeTheme['rgba_bg'] }}; color: {{ $activeTheme['hex'] }};">
                                    <span>{{ $bluezonePillars[0]['tag'] }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

            {{-- FORMAT 2: MODERN 6-CARD SHOWCASE GRID --}}
            @elseif ($pillarsFormat === 'showcase_grid')
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                    @foreach ($bluezonePillars as $pillar)
                        <div class="rounded-2xl sm:rounded-3xl {{ $cardSurfaceClass }} p-6 sm:p-7 flex flex-col justify-between transition-all duration-300 hover:-translate-y-1.5 hover:shadow-2xl border-t-4"
                            style="border-top-color: {{ $activeTheme['hex'] }};">
                            <div>
                                <div class="flex justify-between items-center mb-4 pb-3 border-b border-[#0A4F78]/15 dark:border-[#0A4F78]/30">
                                    <div class="flex items-center gap-2.5">
                                        @if ($showNumbers)
                                            <span class="text-2xl font-light font-mono" style="color: {{ $activeTheme['hex'] }};">
                                                {{ $pillar['num'] }}
                                            </span>
                                        @endif
                                        <span class="text-[10px] font-bold tracking-wider uppercase text-[#0A4F78] dark:text-[#2A8FC2]">
                                            {{ $pillar['menu_title'] }}
                                        </span>
                                    </div>
                                    <span class="w-9 h-9 rounded-xl flex items-center justify-center text-sm shrink-0"
                                        style="background: {{ $activeTheme['rgba_bg'] }}; color: {{ $activeTheme['hex'] }};">
                                        @if(str_starts_with(trim($pillar['icon']), '<svg'))
                                            {!! $pillar['icon'] !!}
                                        @else
                                            <i class="{{ $pillar['icon'] }}"></i>
                                        @endif
                                    </span>
                                </div>

                                <h3 class="text-base sm:text-lg font-bold text-[#031827] dark:text-[#F6F5EF] tracking-tight mb-3">
                                    {{ $pillar['title'] }}
                                </h3>

                                <div class="text-xs sm:text-sm text-[#031827]/80 dark:text-[#F6F5EF]/80 leading-relaxed mb-4 max-h-[220px] overflow-y-auto pr-1"
                                    style="scrollbar-width: thin;">
                                    {!! $pillar['desc'] !!}
                                </div>
                            </div>

                            @if ($showTags && !empty($pillar['tag']))
                                <div class="pt-3 border-t border-[#0A4F78]/15 dark:border-[#0A4F78]/30">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold"
                                        style="background: {{ $activeTheme['rgba_bg'] }}; color: {{ $activeTheme['hex'] }};">
                                        {{ $pillar['tag'] }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

            {{-- FORMAT 3: INTERACTIVE STACKED ACCORDION --}}
            @elseif ($pillarsFormat === 'interactive_accordion')
                <div class="max-w-4xl mx-auto space-y-4">
                    @foreach ($bluezonePillars as $i => $pillar)
                        <div class="rounded-2xl {{ $cardSurfaceClass }} overflow-hidden transition-all duration-300">
                            <!-- Accordion Header -->
                            <button type="button" onclick="toggleStorefrontPillar({{ $i }})"
                                class="w-full p-4 sm:p-5 flex justify-between items-center text-left rtl:text-right cursor-pointer group select-none transition-colors border-l-4 rtl:border-r-4 rtl:border-l-0"
                                style="border-color: {{ $activeTheme['hex'] }};">
                                <div class="flex items-center gap-3 sm:gap-4 flex-wrap sm:flex-nowrap">
                                    @if ($showNumbers)
                                        <span class="font-mono font-bold text-xs px-2.5 py-1 rounded-md text-white shrink-0"
                                            style="background: {{ $activeTheme['hex'] }};">
                                            {{ $pillar['num'] }}
                                        </span>
                                    @endif
                                    <span class="w-8 h-8 rounded-lg flex items-center justify-center text-sm shrink-0"
                                        style="background: {{ $activeTheme['rgba_bg'] }}; color: {{ $activeTheme['hex'] }};">
                                        @if(str_starts_with(trim($pillar['icon']), '<svg'))
                                            {!! $pillar['icon'] !!}
                                        @else
                                            <i class="{{ $pillar['icon'] }}"></i>
                                        @endif
                                    </span>
                                    <span class="text-sm sm:text-base font-bold text-[#031827] dark:text-[#F6F5EF] group-hover:opacity-90">
                                        {{ $pillar['title'] }}
                                    </span>
                                    @if ($showTags && !empty($pillar['tag']))
                                        <span class="hidden sm:inline-block px-2.5 py-0.5 rounded-full text-[11px] font-bold shrink-0"
                                            style="background: {{ $activeTheme['rgba_bg'] }}; color: {{ $activeTheme['hex'] }};">
                                            {{ $pillar['tag'] }}
                                        </span>
                                    @endif
                                </div>
                                <span class="text-xs transition-transform duration-300 text-muted shrink-0 ml-2 rtl:mr-2"
                                    id="storefront_chevron_{{ $i }}"
                                    style="{{ $i === 0 ? 'transform: rotate(180deg);' : '' }}">
                                    <i class="fa-solid fa-chevron-down"></i>
                                </span>
                            </button>

                            <!-- Accordion Body -->
                            <div id="storefront_body_{{ $i }}"
                                style="display: {{ $i === 0 ? 'block' : 'none' }};"
                                class="p-5 sm:p-6 border-t border-[#0A4F78]/15 dark:border-[#0A4F78]/30 text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 leading-relaxed">
                                {!! $pillar['desc'] !!}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <!-- Script for Dynamic Storefront Formats -->
    <script>
        (function() {
            const PILLARS_DATA = @json($bluezonePillars);
            const ACCENT_COLOR = @json($activeTheme['hex']);
            const ACCENT_BG = @json($activeTheme['rgba_bg']);

            // Matrix Controller
            function selectPillar(idx) {
                if (idx < 0 || idx >= PILLARS_DATA.length) return;
                const p = PILLARS_DATA[idx];

                // 1. Update Desktop Left Nav
                const desktopBtns = document.querySelectorAll('.pillar-desktop-btn');
                desktopBtns.forEach((btn, i) => {
                    const numSpan = btn.querySelector('span:first-child');
                    const iconSpan = btn.querySelector('span:nth-child(2)');
                    const titleSpan = btn.querySelector('span:last-child');
                    if (i === idx) {
                        btn.style.borderColor = ACCENT_COLOR;
                        btn.style.background = ACCENT_BG;
                        if (numSpan) numSpan.style.color = ACCENT_COLOR;
                        if (iconSpan) iconSpan.style.color = ACCENT_COLOR;
                        if (titleSpan) {
                            titleSpan.style.color = ACCENT_COLOR;
                            titleSpan.classList.add('font-bold');
                            titleSpan.classList.remove('font-medium');
                        }
                    } else {
                        btn.style.borderColor = 'transparent';
                        btn.style.background = 'transparent';
                        if (numSpan) numSpan.style.color = 'rgba(3, 24, 39, 0.4)';
                        if (iconSpan) iconSpan.style.color = '#94A3B8';
                        if (titleSpan) {
                            titleSpan.style.color = '';
                            titleSpan.classList.remove('font-bold');
                            titleSpan.classList.add('font-medium');
                        }
                    }
                });

                // 2. Update Mobile Horizontal Nav
                const mobileBtns = document.querySelectorAll('.pillar-nav-btn');
                mobileBtns.forEach((btn, i) => {
                    if (i === idx) {
                        btn.style.background = ACCENT_COLOR;
                        btn.style.color = '#ffffff';
                    } else {
                        btn.style.background = 'rgba(10, 79, 120, 0.08)';
                        btn.style.color = '';
                    }
                });

                // 3. Update Orbital Center SVG Nodes & Spokes
                for (let i = 0; i < PILLARS_DATA.length; i++) {
                    const node = document.getElementById(`node-${i}`);
                    const spoke = document.getElementById(`spoke-${i}`);
                    if (i === idx) {
                        if (node) {
                            node.setAttribute('r', '11');
                            node.setAttribute('fill', ACCENT_COLOR);
                            node.setAttribute('stroke', '#FFFFFF');
                            node.setAttribute('stroke-width', '2.5');
                        }
                        if (spoke) {
                            spoke.setAttribute('stroke', ACCENT_COLOR);
                            spoke.setAttribute('stroke-width', '2');
                            spoke.setAttribute('opacity', '0.9');
                        }
                    } else {
                        if (node) {
                            node.setAttribute('r', '7');
                            node.setAttribute('fill', '#0A4F78');
                            node.setAttribute('stroke', '#2A8FC2');
                            node.setAttribute('stroke-width', '1.5');
                        }
                        if (spoke) {
                            spoke.setAttribute('stroke', '#2A8FC2');
                            spoke.setAttribute('stroke-width', '1');
                            spoke.setAttribute('opacity', '0.25');
                        }
                    }
                }

                // 4. Update Right Display Panel with smooth fade
                const panel = document.getElementById('pillar-content-panel');
                if (panel) {
                    panel.style.opacity = '0.4';
                    setTimeout(() => {
                        const numEl = document.getElementById('pillar-active-num');
                        const titleEl = document.getElementById('pillar-active-title');
                        const descEl = document.getElementById('pillar-active-desc');
                        const tagEl = document.getElementById('pillar-active-tag');
                        const iconBox = document.getElementById('pillar-active-icon-box');

                        if (numEl) numEl.textContent = p.num;
                        if (titleEl) titleEl.textContent = p.title;
                        if (descEl) descEl.innerHTML = p.desc;
                        if (tagEl) tagEl.innerHTML = `<span>${p.tag}</span>`;
                        if (iconBox) {
                            if (p.icon && p.icon.trim().startsWith('<svg')) {
                                iconBox.innerHTML = p.icon;
                            } else {
                                iconBox.innerHTML = `<i class="${p.icon}"></i>`;
                            }
                        }

                        panel.style.opacity = '1';
                    }, 180);
                }
            }

            // Accordion Controller
            function toggleStorefrontPillar(idx) {
                const body = document.getElementById(`storefront_body_${idx}`);
                const chevron = document.getElementById(`storefront_chevron_${idx}`);
                if (!body) return;

                const isClosed = body.style.display === 'none' || body.offsetParent === null;
                if (isClosed) {
                    body.style.display = 'block';
                    if (chevron) chevron.style.transform = 'rotate(180deg)';
                } else {
                    body.style.display = 'none';
                    if (chevron) chevron.style.transform = 'rotate(0deg)';
                }
            }

            window.BLUEZONE_PILLARS = {
                select: selectPillar
            };
            window.toggleStorefrontPillar = toggleStorefrontPillar;
        })();
    </script>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-20 space-y-20 sm:space-y-28">

        <!-- 01. EDITORIAL BRAND HERO -->




        {{-- =========================================================
    2. THE 4-STAGE SCIENCE JOURNEY
    ========================================================== --}}
        <section class="space-y-10" id="bz-science-journey-section" aria-labelledby="bz-science-process-title">

            {{-- Section Heading --}}
            <div class="text-center max-w-xl mx-auto space-y-2">

                <span
                    class="text-[10px] font-extrabold uppercase tracking-[0.25em]
                       text-[#0A4F78] dark:text-[#2A8FC2]">
                    {{ __('app.the_process') }}
                </span>

                <h2 id="bz-science-process-title"
                    class="text-2xl sm:text-3xl font-bold tracking-tight
                       text-[#031827] dark:text-[#F6F5EF]">
                    {{ __('app.the_four_stages') }}
                </h2>

            </div>


            {{-- =====================================================
        Desktop Horizontal Timeline
        ====================================================== --}}
            <div class="hidden md:block relative py-6 max-w-3xl mx-auto" id="bz-science-desktop-timeline">

                {{-- Base Line --}}
                <div class="absolute top-1/2 left-[12%] right-[12%]
                       h-0.5 bg-[#0A4F78]/15 dark:bg-[#0A4F78]/30
                       -translate-y-1/2 z-0"
                    aria-hidden="true"></div>

                {{-- Progress --}}
                <div id="bz-timeline-progress"
                    class="absolute top-1/2 left-[12%]
                       h-0.5 bg-[#67B34A]
                       -translate-y-1/2 z-0
                       transition-all duration-500"
                    style="width: 0%;" aria-hidden="true"></div>

                <div class="grid grid-cols-4 gap-4 relative z-10 text-center">

                    {{-- Stage 01 --}}
                    <button type="button" onclick="BLUEZONE_SCIENCE.select(0)"
                        onmouseenter="BLUEZONE_SCIENCE.select(0)"
                        class="bz-timeline-node group cursor-pointer
                           flex flex-col items-center gap-2
                           transition-transform duration-300
                           focus:outline-none focus-visible:ring-2
                           focus-visible:ring-[#67B34A] focus-visible:ring-offset-2
                           rounded-xl"
                        data-index="0" aria-label="{{ __('app.source_from_nature') }}">

                        <div
                            class="node-circle w-11 h-11 rounded-full
                               bg-[#67B34A] text-white
                               flex items-center justify-center
                               font-mono text-xs font-black
                               shadow-[0_0_15px_rgba(103,179,74,0.4)]
                               scale-110 border-2 border-[#67B34A]
                               transition-all">
                            01
                        </div>

                        <span
                            class="node-title text-xs font-extrabold
                               uppercase tracking-widest text-[#67B34A]">
                            {{ __('app.source') }}
                        </span>

                    </button>


                    {{-- Stage 02 --}}
                    <button type="button" onclick="BLUEZONE_SCIENCE.select(1)"
                        onmouseenter="BLUEZONE_SCIENCE.select(1)"
                        class="bz-timeline-node group cursor-pointer
                           flex flex-col items-center gap-2
                           transition-transform duration-300
                           focus:outline-none focus-visible:ring-2
                           focus-visible:ring-[#67B34A] focus-visible:ring-offset-2
                           rounded-xl"
                        data-index="1" aria-label="{{ __('app.formulation_precision') }}">

                        <div
                            class="node-circle w-9 h-9 rounded-full
                               bg-[#F6F5EF] dark:bg-[#031827]
                               border-2 border-[#0A4F78]/30
                               flex items-center justify-center
                               font-mono text-xs font-bold
                               text-[#031827]/50 dark:text-[#F6F5EF]/50
                               transition-all
                               group-hover:border-[#67B34A]">
                            02
                        </div>

                        <span
                            class="node-title text-xs font-semibold
                               uppercase tracking-widest
                               text-[#031827]/50 dark:text-[#F6F5EF]/50
                               group-hover:text-[#67B34A]
                               transition-colors">
                            {{ __('app.formulation') }}
                        </span>

                    </button>


                    {{-- Stage 03 --}}
                    <button type="button" onclick="BLUEZONE_SCIENCE.select(2)"
                        onmouseenter="BLUEZONE_SCIENCE.select(2)"
                        class="bz-timeline-node group cursor-pointer
                           flex flex-col items-center gap-2
                           transition-transform duration-300
                           focus:outline-none focus-visible:ring-2
                           focus-visible:ring-[#67B34A] focus-visible:ring-offset-2
                           rounded-xl"
                        data-index="2" aria-label="{{ __('app.validation_quality_focus') }}">

                        <div
                            class="node-circle w-9 h-9 rounded-full
                               bg-[#F6F5EF] dark:bg-[#031827]
                               border-2 border-[#0A4F78]/30
                               flex items-center justify-center
                               font-mono text-xs font-bold
                               text-[#031827]/50 dark:text-[#F6F5EF]/50
                               transition-all
                               group-hover:border-[#67B34A]">
                            03
                        </div>

                        <span
                            class="node-title text-xs font-semibold
                               uppercase tracking-widest
                               text-[#031827]/50 dark:text-[#F6F5EF]/50
                               group-hover:text-[#67B34A]
                               transition-colors">
                            {{ __('app.validation') }}
                        </span>

                    </button>


                    {{-- Stage 04 --}}
                    <button type="button" onclick="BLUEZONE_SCIENCE.select(3)"
                        onmouseenter="BLUEZONE_SCIENCE.select(3)"
                        class="bz-timeline-node group cursor-pointer
                           flex flex-col items-center gap-2
                           transition-transform duration-300
                           focus:outline-none focus-visible:ring-2
                           focus-visible:ring-[#67B34A] focus-visible:ring-offset-2
                           rounded-xl"
                        data-index="3" aria-label="{{ __('app.wellness_daily_life') }}">

                        <div
                            class="node-circle w-9 h-9 rounded-full
                               bg-[#F6F5EF] dark:bg-[#031827]
                               border-2 border-[#0A4F78]/30
                               flex items-center justify-center
                               font-mono text-xs font-bold
                               text-[#031827]/50 dark:text-[#F6F5EF]/50
                               transition-all
                               group-hover:border-[#67B34A]">
                            04
                        </div>

                        <span
                            class="node-title text-xs font-semibold
                               uppercase tracking-widest
                               text-[#031827]/50 dark:text-[#F6F5EF]/50
                               group-hover:text-[#67B34A]
                               transition-colors">
                            {{ __('app.wellness') }}
                        </span>

                    </button>

                </div>
            </div>


            {{-- =====================================================
        Mobile Vertical Timeline
        ====================================================== --}}
            <div class="md:hidden space-y-2
                   border-l-2 border-[#0A4F78]/20
                   pl-4 py-2"
                id="bz-science-mobile-timeline">

                <button type="button" onclick="BLUEZONE_SCIENCE.select(0)"
                    class="bz-mobile-node flex items-center gap-3
                       py-2 text-xs font-bold text-[#67B34A]"
                    data-index="0">

                    <span
                        class="w-6 h-6 rounded-full
                           bg-[#67B34A] text-white
                           flex items-center justify-center
                           text-[10px] shrink-0">
                        01
                    </span>

                    <span>
                        {{ __('app.source_from_nature') }}
                    </span>

                </button>


                <button type="button" onclick="BLUEZONE_SCIENCE.select(1)"
                    class="bz-mobile-node flex items-center gap-3
                       py-2 text-xs font-medium
                       text-[#031827]/60 dark:text-[#F6F5EF]/60"
                    data-index="1">

                    <span
                        class="w-6 h-6 rounded-full
                           bg-[#0A4F78]/20
                           text-[#031827] dark:text-[#F6F5EF]
                           flex items-center justify-center
                           text-[10px] shrink-0">
                        02
                    </span>

                    <span>
                        {{ __('app.formulation_precision') }}
                    </span>

                </button>


                <button type="button" onclick="BLUEZONE_SCIENCE.select(2)"
                    class="bz-mobile-node flex items-center gap-3
                       py-2 text-xs font-medium
                       text-[#031827]/60 dark:text-[#F6F5EF]/60"
                    data-index="2">

                    <span
                        class="w-6 h-6 rounded-full
                           bg-[#0A4F78]/20
                           text-[#031827] dark:text-[#F6F5EF]
                           flex items-center justify-center
                           text-[10px] shrink-0">
                        03
                    </span>

                    <span>
                        {{ __('app.validation_quality_focus') }}
                    </span>

                </button>


                <button type="button" onclick="BLUEZONE_SCIENCE.select(3)"
                    class="bz-mobile-node flex items-center gap-3
                       py-2 text-xs font-medium
                       text-[#031827]/60 dark:text-[#F6F5EF]/60"
                    data-index="3">

                    <span
                        class="w-6 h-6 rounded-full
                           bg-[#0A4F78]/20
                           text-[#031827] dark:text-[#F6F5EF]
                           flex items-center justify-center
                           text-[10px] shrink-0">
                        04
                    </span>

                    <span>
                        {{ __('app.wellness_daily_life') }}
                    </span>

                </button>

            </div>


            {{-- =====================================================
        Science Stage Display
        ====================================================== --}}
            <div id="bz-science-panel"
                class="p-6 sm:p-10 lg:p-12
                   rounded-3xl
                   bg-white dark:bg-[#062B49]
                   border border-[#0A4F78]/20
                   shadow-xl
                   transition-opacity duration-500
                   min-h-[380px]">

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">

                    {{-- Visual --}}
                    <div class="lg:col-span-6 relative">

                        <div
                            class="w-full
                               h-72 sm:h-96
                               rounded-2xl
                               overflow-hidden
                               relative
                               shadow-md
                               border border-[#0A4F78]/20
                               bg-[#031827]
                               group">

                            <img id="bz-science-active-img" src="{{ asset('assets/images/hero_longevity.jpg') }}"
                                alt="{{ __('app.from_nature') }}" loading="eager" decoding="async"
                                fetchpriority="high"
                                onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';"
                                class="w-full h-full object-cover
                                   transition-transform duration-700
                                   group-hover:scale-105" />

                            <div class="absolute inset-0
                                   bg-gradient-to-t
                                   from-[#031827]/90
                                   via-[#031827]/30
                                   to-transparent
                                   pointer-events-none"
                                aria-hidden="true"></div>


                            {{-- Biological Flow --}}
                            <div
                                class="absolute inset-x-4 bottom-4
                                   p-4 rounded-xl
                                   bg-[#031827]/85
                                   border border-[#2A8FC2]/30
                                   backdrop-blur-md
                                   text-white space-y-2">

                                <span
                                    class="block text-[9px] font-mono font-bold
                                       tracking-widest
                                       text-[#2A8FC2] uppercase">
                                    {{ __('app.biological_system_flow') }}
                                </span>

                                <div
                                    class="flex items-center justify-between
                                       gap-2
                                       text-[10px] font-mono font-bold
                                       text-white/90">

                                    <span id="bz-flow-step-1" class="text-[#67B34A]">
                                        {{ __('app.ingredient') }}
                                    </span>

                                    <i class="fa-solid fa-arrow-right
                                           rtl:rotate-180
                                           text-xs text-[#2A8FC2]"
                                        aria-hidden="true"></i>

                                    <span id="bz-flow-step-2" class="text-white/40">
                                        {{ __('app.function') }}
                                    </span>

                                    <i class="fa-solid fa-arrow-right
                                           rtl:rotate-180
                                           text-xs text-[#2A8FC2]"
                                        aria-hidden="true"></i>

                                    <span id="bz-flow-step-3" class="text-white/40">
                                        {{ __('app.body') }}
                                    </span>

                                    <i class="fa-solid fa-arrow-right
                                           rtl:rotate-180
                                           text-xs text-[#2A8FC2]"
                                        aria-hidden="true"></i>

                                    <span id="bz-flow-step-4" class="text-white/40">
                                        {{ __('app.wellness') }}
                                    </span>

                                </div>
                            </div>


                            {{-- Stage Code --}}
                            <div
                                class="absolute top-4 left-4
                                   px-3 py-1 rounded-full
                                   bg-[#031827]/80
                                   text-[#67B34A]
                                   text-[10px] font-mono font-bold
                                   border border-[#67B34A]/40
                                   backdrop-blur-sm">

                                {{ __('app.stage') }}

                                <span id="bz-science-stage-code">
                                    01/04
                                </span>

                            </div>

                        </div>

                    </div>


                    {{-- Stage Details --}}
                    <div class="lg:col-span-6 space-y-6">

                        <div class="space-y-2">

                            <div class="flex items-center gap-3">

                                <span id="bz-science-active-num"
                                    class="text-4xl font-light font-mono
                                       text-[#67B34A]">
                                    01
                                </span>

                                <span class="text-xs font-bold text-[#67B34A]">
                                    —
                                </span>

                                <span id="bz-science-active-stage"
                                    class="text-xs font-extrabold
                                       uppercase tracking-widest
                                       text-[#0A4F78]
                                       dark:text-[#2A8FC2]">
                                    {{ __('app.source') }}
                                </span>

                            </div>

                            <h3 id="bz-science-active-title"
                                class="text-2xl sm:text-4xl
                                   font-bold
                                   text-[#031827]
                                   dark:text-[#F6F5EF]
                                   tracking-tight">
                                {{ __('app.from_nature') }}
                            </h3>

                        </div>


                        <p id="bz-science-active-desc"
                            class="text-xs sm:text-sm
                               text-[#031827]/80
                               dark:text-[#F6F5EF]/80
                               font-medium leading-relaxed">
                            {{ __('app.source_stage_description') }}
                        </p>


                        <div
                            class="pt-4
                               border-t border-[#0A4F78]/15
                               dark:border-[#0A4F78]/30
                               space-y-2">

                            <span
                                class="block text-[10px] font-extrabold
                                   uppercase tracking-widest
                                   text-[#0A4F78]
                                   dark:text-[#2A8FC2]">
                                {{ __('app.key_highlights') }}
                            </span>

                            <div id="bz-science-active-chips" class="flex flex-wrap gap-2">

                                <span
                                    class="px-3 py-1.5 rounded-lg
                                       bg-[#67B34A]/15
                                       text-[#67B34A]
                                       text-xs font-bold">
                                    {{ __('app.standardized_botanical_extraction') }}
                                </span>

                                <span
                                    class="px-3 py-1.5 rounded-lg
                                       bg-[#0A4F78]/10
                                       dark:bg-[#0A4F78]/40
                                       text-[#031827]
                                       dark:text-[#F6F5EF]
                                       text-xs font-bold">
                                    {{ __('app.peak_potency_sourcing') }}
                                </span>

                            </div>
                        </div>

                    </div>

                </div>

            </div>

        </section>

    </div>

    <script>
        (() => {
            'use strict';

            const SCIENCE_DATA = [{
                    num: "01",
                    code: "01/04",
                    stage: @json(__('app.source')),
                    title: @json(__('app.from_nature')),
                    desc: @json(__('app.source_stage_description')),
                    img: @json(asset('assets/images/hero_longevity.jpg')),
                    chips: [
                        @json(__('app.standardized_botanical_extraction')),
                        @json(__('app.peak_potency_sourcing'))
                    ],
                    flowStep: 1
                },
                {
                    num: "02",
                    code: "02/04",
                    stage: @json(__('app.formulation')),
                    title: @json(__('app.precision_in_every_formula')),
                    desc: @json(__('app.formulation_stage_description')),
                    img: @json(asset('assets/products/blue-mind.webp')),
                    chips: [
                        @json(__('app.bio_identical_nutrient_ratios')),
                        @json(__('app.cellular_absorption_focus'))
                    ],
                    flowStep: 2
                },
                {
                    num: "03",
                    code: "03/04",
                    stage: @json(__('app.validation')),
                    title: @json(__('app.quality_you_can_trust')),
                    desc: @json(__('app.validation_stage_description')),
                    img: @json(asset('assets/images/blog-1.jpg')),
                    chips: [
                        @json(__('app.third_party_quality_verified')),
                        @json(__('app.zero_synthetic_additives'))
                    ],
                    flowStep: 3
                },
                {
                    num: "04",
                    code: "04/04",
                    stage: @json(__('app.wellness')),
                    title: @json(__('app.designed_for_daily_life')),
                    desc: @json(__('app.wellness_stage_description')),
                    img: @json(asset('assets/images/blog-2.jpg')),
                    chips: [
                        @json(__('app.cognitive_resilience')),
                        @json(__('app.daily_vitality_support'))
                    ],
                    flowStep: 4
                }
            ];


            const SELECTORS = {
                progress: '#bz-timeline-progress',
                desktopNodes: '#bz-science-desktop-timeline .bz-timeline-node',
                mobileNodes: '#bz-science-mobile-timeline .bz-mobile-node',
                panel: '#bz-science-panel',
                image: '#bz-science-active-img',
                code: '#bz-science-stage-code',
                number: '#bz-science-active-num',
                stage: '#bz-science-active-stage',
                title: '#bz-science-active-title',
                description: '#bz-science-active-desc',
                chips: '#bz-science-active-chips'
            };


            const getElement = (selector) => {
                return document.querySelector(selector);
            };


            const getElements = (selector) => {
                return document.querySelectorAll(selector);
            };


            function updateTimeline(index) {
                const progress = getElement(SELECTORS.progress);

                if (progress) {
                    const progressValues = [0, 33.3, 66.6, 100];
                    progress.style.width = `${progressValues[index]}%`;
                }


                getElements(SELECTORS.desktopNodes).forEach((node, nodeIndex) => {
                    const circle = node.querySelector('.node-circle');
                    const title = node.querySelector('.node-title');

                    if (nodeIndex === index) {
                        if (circle) {
                            circle.className =
                                'node-circle w-11 h-11 rounded-full ' +
                                'bg-[#67B34A] text-white flex items-center justify-center ' +
                                'font-mono text-xs font-black ' +
                                'shadow-[0_0_15px_rgba(103,179,74,0.4)] ' +
                                'scale-110 border-2 border-[#67B34A] transition-all';
                        }

                        if (title) {
                            title.className =
                                'node-title text-xs font-extrabold ' +
                                'uppercase tracking-widest text-[#67B34A]';
                        }

                    } else {

                        if (circle) {
                            circle.className =
                                'node-circle w-9 h-9 rounded-full ' +
                                'bg-[#F6F5EF] dark:bg-[#031827] ' +
                                'border-2 border-[#0A4F78]/30 ' +
                                'flex items-center justify-center ' +
                                'font-mono text-xs font-bold ' +
                                'text-[#031827]/50 dark:text-[#F6F5EF]/50 ' +
                                'transition-all group-hover:border-[#67B34A]';
                        }

                        if (title) {
                            title.className =
                                'node-title text-xs font-semibold ' +
                                'uppercase tracking-widest ' +
                                'text-[#031827]/50 dark:text-[#F6F5EF]/50 ' +
                                'group-hover:text-[#67B34A] ' +
                                'transition-colors';
                        }
                    }
                });


                getElements(SELECTORS.mobileNodes).forEach((node, nodeIndex) => {

                    if (nodeIndex === index) {
                        node.className =
                            'bz-mobile-node flex items-center gap-3 ' +
                            'py-2 text-xs font-bold text-[#67B34A]';
                    } else {
                        node.className =
                            'bz-mobile-node flex items-center gap-3 ' +
                            'py-2 text-xs font-medium ' +
                            'text-[#031827]/60 dark:text-[#F6F5EF]/60';
                    }
                });
            }


            function updateFlow(flowStep) {
                for (let step = 1; step <= 4; step++) {

                    const element = document.getElementById(
                        `bz-flow-step-${step}`
                    );

                    if (!element) continue;

                    element.className =
                        step <= flowStep ?
                        'text-[#67B34A] font-bold' :
                        'text-white/40 font-normal';
                }
            }


            function updateChips(chips) {
                const chipsContainer = getElement(SELECTORS.chips);

                if (!chipsContainer) return;

                const fragment = document.createDocumentFragment();

                chips.forEach((chip, index) => {

                    const span = document.createElement('span');

                    span.className =
                        index === 0 ?
                        'px-3 py-1.5 rounded-lg bg-[#67B34A]/15 text-[#67B34A] text-xs font-bold' :
                        'px-3 py-1.5 rounded-lg bg-[#0A4F78]/10 dark:bg-[#0A4F78]/40 text-[#031827] dark:text-[#F6F5EF] text-xs font-bold';

                    span.textContent = chip;

                    fragment.appendChild(span);
                });

                chipsContainer.replaceChildren(fragment);
            }


            function updatePanel(stage) {

                const image = getElement(SELECTORS.image);
                const code = getElement(SELECTORS.code);
                const number = getElement(SELECTORS.number);
                const stageElement = getElement(SELECTORS.stage);
                const title = getElement(SELECTORS.title);
                const description = getElement(SELECTORS.description);

                if (image) {
                    image.src = stage.img;
                    image.alt = stage.title;
                }

                if (code) {
                    code.textContent = stage.code;
                }

                if (number) {
                    number.textContent = stage.num;
                }

                if (stageElement) {
                    stageElement.textContent = stage.stage;
                }

                if (title) {
                    title.textContent = stage.title;
                }

                if (description) {
                    description.textContent = stage.desc;
                }

                updateChips(stage.chips);
            }


            function selectScience(index) {

                if (
                    !Number.isInteger(index) ||
                    index < 0 ||
                    index >= SCIENCE_DATA.length
                ) {
                    return;
                }

                const stage = SCIENCE_DATA[index];
                const panel = getElement(SELECTORS.panel);

                updateTimeline(index);
                updateFlow(stage.flowStep);

                if (!panel) {
                    updatePanel(stage);
                    return;
                }

                panel.style.opacity = '0.3';

                window.setTimeout(() => {
                    updatePanel(stage);
                    panel.style.opacity = '1';
                }, 180);
            }


            window.BLUEZONE_SCIENCE = {
                select: selectScience
            };


            // Initialize first stage.
            if (document.readyState === 'loading') {
                document.addEventListener(
                    'DOMContentLoaded',
                    () => selectScience(0), {
                        once: true
                    }
                );
            } else {
                selectScience(0);
            }

        })();
    </script>
</x-layouts.customer>
