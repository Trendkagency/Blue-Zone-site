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
        $defaultEyebrow = $isArabic ? 'مكمل دعم الإدراك الشامل' : 'COMPREHENSIVE COGNITIVE SUPPORT SUPPLEMENT';
        $defaultHeadingPrefix = $isArabic ? 'الركائز الست لـ' : 'THE 6 PILLARS OF';
        $defaultHeadingHighlight = $isArabic ? 'بلو مايند' : 'BLUE MIND';
        $defaultDescription = $isArabic 
            ? 'بلو مايند: غذِّ عقلك، ونشّط جسدك. تركيبة علمية شاملة صُممت لحماية مدخولك الغذائي وتحسين الوظائف الإدراكية.' 
            : 'Blue Mind: Fuel your mind, Energize your body. A scientifically formulated, all-in-one daily supplement designed to safeguard your dietary intake and optimize cognitive function.';
        
        $eyebrow = \App\Models\Setting::get("pillars_eyebrow_{$locale}", $defaultEyebrow);
        $headingPrefix = \App\Models\Setting::get("pillars_heading_prefix_{$locale}", $defaultHeadingPrefix);
        $headingHighlight = \App\Models\Setting::get("pillars_heading_highlight_{$locale}", $defaultHeadingHighlight);
        $sectionDesc = \App\Models\Setting::get("pillars_description_{$locale}", $defaultDescription);

        $centerTitle = \App\Models\Setting::get("pillars_center_title_{$locale}", ($isArabic ? 'بلو مايند' : 'BLUE MIND'));
        $centerSubtitle = \App\Models\Setting::get("pillars_center_subtitle_{$locale}", ($isArabic ? 'الجوهر' : 'CORE'));

        // Default Pillars content definitions
        $defaultPillars = [
            1 => [
                'icon' => 'fa-solid fa-brain',
                'menu_en' => 'THE SCIENCE',
                'menu_ar' => 'العلم وراء التركيبة',
                'title_en' => 'The Science Behind Blue Mind',
                'title_ar' => 'العلم وراء بلو مايند',
                'tag_en' => 'Cognitive Optimization & Safeguard',
                'tag_ar' => 'تحسين الإدراك والحماية الخلوية',
                'desc_en' => '<p class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 font-normal leading-relaxed mb-3">The brain acts as the command center of your nervous system and contains over <strong class="text-[#0A4F78] dark:text-[#2A8FC2] font-semibold">80 billion intricate neural pathways</strong>. It is highly demanding, using about <strong class="text-[#0A4F78] dark:text-[#2A8FC2] font-semibold">30% of the energy</strong> your body produces from food.</p><p class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 font-normal leading-relaxed mb-3">Since brain cells are irreplaceable, they have the highest priority for specific micronutrients. Just like any advanced machine, the quality of what you put in directly affects performance.</p><div class="p-3.5 rounded-xl bg-[#67B34A]/10 border border-[#67B34A]/25 text-xs text-[#031827] dark:text-[#F6F5EF] leading-relaxed"><strong class="text-[#67B34A] font-bold">Blue mind</strong> is a scientifically formulated, all-in-one daily supplement designed to safeguard your dietary intake and optimize cognitive function.</div>',
                'desc_ar' => '<p class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 font-normal leading-relaxed mb-3">يعمل الدماغ كمركز تحكم لجهازك العصبي ويحتوي على أكثر من <strong class="text-[#0A4F78] dark:text-[#2A8FC2] font-semibold">80 مليار مسار عصبي معقد</strong>. يستهلك الدماغ طاقة هائلة، حيث يحتاج إلى حوالي <strong class="text-[#0A4F78] dark:text-[#2A8FC2] font-semibold">30% من الطاقة</strong> التي ينتجها جسمك من الطعام.</p><p class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 font-normal leading-relaxed mb-3">ولأن خلايا الدماغ لا يمكن تعويضها أو استبدالها، فإنها تحظى بالأولوية القصوى للعناصر الغذائية الدقيقة المتخصصة. تمامًا مثل أي محرك متقدم، فإن جودة ما تغذيه به تؤثر مباشرة على كفاءته وأدائه.</p><div class="p-3.5 rounded-xl bg-[#67B34A]/10 border border-[#67B34A]/25 text-xs text-[#031827] dark:text-[#F6F5EF] leading-relaxed"><strong class="text-[#67B34A] font-bold">بلو مايند</strong> هو مكمل يومي شامل ومُصاغ علميًا لضمان حماية مدخولك الغذائي وتحسين وظائفك الإدراكية إلى أقصى حد.</div>'
            ],
            2 => [
                'icon' => 'fa-solid fa-bolt',
                'menu_en' => 'TARGETED NOOTROPICS',
                'menu_ar' => 'منشطات الإدراك',
                'title_en' => 'Targeted Nootropics for Mental Performance',
                'title_ar' => 'منشطات إدراكية مستهدفة للأداء العقلي',
                'tag_en' => 'Memory, Focus & Alertness',
                'tag_ar' => 'الذاكرة والتركيز واليقظة الذهنية',
                'desc_en' => '<p class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 font-medium leading-relaxed mb-3">Our advanced formula combines specialist nutrients designed to support memory, focus, and psychological function:</p><ul class="space-y-3 text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85"><li class="flex items-start gap-2.5"><span class="w-2 h-2 rounded-full bg-[#67B34A] mt-1.5 shrink-0"></span><span><strong class="text-[#031827] dark:text-[#F6F5EF] font-bold">Ginkgo Biloba (120 mg):</strong> Enhances cerebral blood flow and nutrient delivery, helping to maintain memory with age and support mental alertness.</span></li><li class="flex items-start gap-2.5"><span class="w-2 h-2 rounded-full bg-[#67B34A] mt-1.5 shrink-0"></span><span><strong class="text-[#031827] dark:text-[#F6F5EF] font-bold">Essential Phospholipids:</strong> Phosphatidylserine and Phosphatidylcholine preserve neural membrane integrity and support acetylcholine production, which is crucial for learning speed and memory storage.</span></li><li class="flex items-start gap-2.5"><span class="w-2 h-2 rounded-full bg-[#67B34A] mt-1.5 shrink-0"></span><span><strong class="text-[#031827] dark:text-[#F6F5EF] font-bold">Cellular Antioxidant Defense:</strong> Co-Q10, L-Glutathione, and Selenium protect delicate brain tissue from oxidative stress and cellular damage.</span></li></ul>',
                'desc_ar' => '<p class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 font-medium leading-relaxed mb-3">تجمع تركيبتنا المتطورة بين مغذيات تخصصية فائقة لدعم الذاكرة والتركيز والوظائف النفسية السليمة:</p><ul class="space-y-3 text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85"><li class="flex items-start gap-2.5"><span class="w-2 h-2 rounded-full bg-[#67B34A] mt-1.5 shrink-0"></span><span><strong class="text-[#031827] dark:text-[#F6F5EF] font-bold">الجنكة بيلوبا (120 ملغ):</strong> تعزز تدفق الدم الدماغي وتوصيل المغذيات الحيوية، مما يساعد على دعم الذاكرة مع التقدم في العمر وتعزيز اليقظة الذهنية.</span></li><li class="flex items-start gap-2.5"><span class="w-2 h-2 rounded-full bg-[#67B34A] mt-1.5 shrink-0"></span><span><strong class="text-[#031827] dark:text-[#F6F5EF] font-bold">الفسفوليبيدات الأساسية:</strong> يحافظ كل من الفوسفاتيديل سيرين والفوسفاتيديل كولين على سلامة الغشاء العصبي ويدعمان إنتاج الأسيتيل كولين، الحاسم لسرعة التعلم وتخزين الذاكرة.</span></li><li class="flex items-start gap-2.5"><span class="w-2 h-2 rounded-full bg-[#67B34A] mt-1.5 shrink-0"></span><span><strong class="text-[#031827] dark:text-[#F6F5EF] font-bold">الدفاع الخلوي المضاد للأكسدة:</strong> يحمي الإنزيم المساعد Co-Q10، وL-جلوتاثيون، والسيلينيوم أنسجة الدماغ الحساسة من الإجهاد التأكسدي والتلف الخلوي.</span></li></ul>'
            ],
            3 => [
                'icon' => 'fa-solid fa-flask-vial',
                'menu_en' => 'VITAMINS & COFACTORS',
                'menu_ar' => 'الفيتامينات والعوامل المساعدة',
                'title_en' => 'Essential Vitamins & Neurological Cofactors',
                'title_ar' => 'فيتامينات أساسية وعوامل عصبية مساعدة',
                'tag_en' => 'Energy Metabolism & Neuro-Balance',
                'tag_ar' => 'أيض الطاقة وتوازن النواقل العصبية',
                'desc_en' => '<p class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 font-medium leading-relaxed mb-3">A healthy brain relies on a broad supply of essential vitamins and minerals to maintain optimal cognitive capacity and a healthy nervous system:</p><ul class="space-y-3 text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85"><li class="flex items-start gap-2.5"><span class="w-2 h-2 rounded-full bg-[#2A8FC2] mt-1.5 shrink-0"></span><span><strong class="text-[#031827] dark:text-[#F6F5EF] font-bold">High-Potency B-Complex:</strong> High doses of B-vitamins (including B12, B6, and Pantothenic Acid) optimize cellular energy metabolism, reduce central nervous system fatigue, and support normal psychological function.</span></li><li class="flex items-start gap-2.5"><span class="w-2 h-2 rounded-full bg-[#2A8FC2] mt-1.5 shrink-0"></span><span><strong class="text-[#031827] dark:text-[#F6F5EF] font-bold">Key Brain Minerals:</strong> Zinc, Iodine, and Iron plus Pantothenic acid work synergistically to support neurotransmitter balance, thyroid function, and normal cognitive function.</span></li><li class="flex items-start gap-2.5"><span class="w-2 h-2 rounded-full bg-[#2A8FC2] mt-1.5 shrink-0"></span><span><strong class="text-[#031827] dark:text-[#F6F5EF] font-bold">Optimum Vitamin D3 (1000 IU):</strong> Delivers the preferred D3 form to support neuroprotective pathways, mood regulation, and overall immune health.</span></li></ul>',
                'desc_ar' => '<p class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 font-medium leading-relaxed mb-3">يعتمد الدماغ الصحي على إمداد واسع ومتوازن من الفيتامينات والمعادن الأساسية للحفاظ على القدرة الإدراكية القصوى وجهاز عصبي سليم:</p><ul class="space-y-3 text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85"><li class="flex items-start gap-2.5"><span class="w-2 h-2 rounded-full bg-[#2A8FC2] mt-1.5 shrink-0"></span><span><strong class="text-[#031827] dark:text-[#F6F5EF] font-bold">فيتامينات ب عالية الفعالية:</strong> جرعات متقدمة من فيتامينات ب (بما في ذلك B12 وB6 وحمض البانتوثنيك) لتحفيز أيض الطاقة الخلوية وتقليل إجهاد الجهاز العصبي المركزي ودعم الوظائف النفسية الطبيعية.</span></li><li class="flex items-start gap-2.5"><span class="w-2 h-2 rounded-full bg-[#2A8FC2] mt-1.5 shrink-0"></span><span><strong class="text-[#031827] dark:text-[#F6F5EF] font-bold">معادن الدماغ الحيوية:</strong> يعمل الزنك واليود والحديد مع حمض البانتوثنيك بتآزر تام لدعم توازن النواقل العصبية ووظائف الغدة الدرقية والأداء الإدراكي السليم.</span></li><li class="flex items-start gap-2.5"><span class="w-2 h-2 rounded-full bg-[#2A8FC2] mt-1.5 shrink-0"></span><span><strong class="text-[#031827] dark:text-[#F6F5EF] font-bold">فيتامين D3 بالجرعة المثالية (1000 وحدة دولية):</strong> يوفر الصورة المفضلة D3 لدعم المسارات الواقية للأعصاب وتنظيم المزاج وتعزيز صحة المناعة الشاملة.</span></li></ul>'
            ],
            4 => [
                'icon' => 'fa-solid fa-shield-halved',
                'menu_en' => 'DAILY FOUNDATION',
                'menu_ar' => 'الأساس اليومي',
                'title_en' => 'Your Complete Daily Foundation',
                'title_ar' => 'أساسك اليومي المتكامل',
                'tag_en' => 'Complete Multivitamin Base',
                'tag_ar' => 'قاعدة فيتامينات شاملة',
                'desc_en' => '<p class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 font-normal leading-relaxed mb-3">This formula goes beyond targeted brain health to provide a <strong class="text-[#0A4F78] dark:text-[#2A8FC2] font-semibold">complete multivitamin foundation</strong>.</p><p class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 font-normal leading-relaxed mb-4">With added <strong class="text-[#031827] dark:text-[#F6F5EF] font-semibold">Vitamin C, Copper, and Folic Acid</strong> to support vascular health, red blood cell formation, and natural energy release, an additional daily multivitamin is no longer necessary.</p><div class="p-3.5 rounded-xl bg-[#67B34A]/10 border border-[#67B34A]/25 text-xs text-[#031827] dark:text-[#F6F5EF] flex items-center gap-3"><span class="w-6 h-6 rounded-full bg-[#67B34A] text-white flex items-center justify-center shrink-0 text-xs font-bold"><i class="fa-solid fa-check"></i></span><span>Convenient all-in-one daily foundation replaces the need for additional general multivitamin tablets.</span></div>',
                'desc_ar' => '<p class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 font-normal leading-relaxed mb-3">تتجاوز هذه التركيبة دعم صحة الدماغ المستهدفة لتوفر <strong class="text-[#0A4F78] dark:text-[#2A8FC2] font-semibold">أساساً متكاملاً من الفيتامينات المتعددة اليومية</strong>.</p><p class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 font-normal leading-relaxed mb-4">مع إضافة <strong class="text-[#031827] dark:text-[#F6F5EF] font-semibold">فيتامين C، والنحاس، وحمض الفوليك</strong> لدعم صحة الأوعية الدموية، وتكوين خلايا الدم الحمراء، وإطلاق الطاقة الطبيعية، لم يعد هناك أي داعٍ لتناول مكمل فيتامينات متعددة يومي إضافي.</p><div class="p-3.5 rounded-xl bg-[#67B34A]/10 border border-[#67B34A]/25 text-xs text-[#031827] dark:text-[#F6F5EF] flex items-center gap-3"><span class="w-6 h-6 rounded-full bg-[#67B34A] text-white flex items-center justify-center shrink-0 text-xs font-bold"><i class="fa-solid fa-check"></i></span><span>تكامل يومي شامل يغنيك عن تناول أقراص فيتامينات متعددة منفصلة.</span></div>'
            ],
            5 => [
                'icon' => 'fa-solid fa-star',
                'menu_en' => 'CORE HIGHLIGHTS',
                'menu_ar' => 'أبرز المزايا',
                'title_en' => 'High-Performance Formula Highlights',
                'title_ar' => 'أبرز مزايا التركيبة المركزة',
                'tag_en' => 'Clinical Synergy & Potency',
                'tag_ar' => 'تآزر وفعالية سريرية',
                'desc_en' => '<ul class="space-y-3 text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85"><li class="flex items-start gap-2.5"><span class="w-2 h-2 rounded-full bg-[#67B34A] mt-1.5 shrink-0"></span><span><strong class="text-[#031827] dark:text-[#F6F5EF] font-bold">Comprehensive Nootropic Support:</strong> Formulated with 120 mg Ginkgo Biloba, Phosphatidylserine, and Phosphatidylcholine to help support memory, mental focus, and cognitive function.</span></li><li class="flex items-start gap-2.5"><span class="w-2 h-2 rounded-full bg-[#67B34A] mt-1.5 shrink-0"></span><span><strong class="text-[#031827] dark:text-[#F6F5EF] font-bold">High-Potency Cellular Energy:</strong> Packed with Vitamin B12 and high-dose B-complex vitamins to assist in energy metabolism and fight mental fatigue.</span></li><li class="flex items-start gap-2.5"><span class="w-2 h-2 rounded-full bg-[#67B34A] mt-1.5 shrink-0"></span><span><strong class="text-[#031827] dark:text-[#F6F5EF] font-bold">Cellular Antioxidant Defense:</strong> Features Co-Q10, L-Glutathione, and Vitamin E to help protect brain cells and neural tissue from oxidative stress.</span></li><li class="flex items-start gap-2.5"><span class="w-2 h-2 rounded-full bg-[#67B34A] mt-1.5 shrink-0"></span><span><strong class="text-[#031827] dark:text-[#F6F5EF] font-bold">Essential Brain & Thyroid Minerals:</strong> Supplies key doses of Zinc, Iodine, and Iron to support neurotransmitter balance, thyroid function, and normal brain oxygenation.</span></li><li class="flex items-start gap-2.5"><span class="w-2 h-2 rounded-full bg-[#67B34A] mt-1.5 shrink-0"></span><span><strong class="text-[#031827] dark:text-[#F6F5EF] font-bold">Convenient All-in-One Daily Tablet:</strong> Combines specialized brain-boosting nutrients with a complete multivitamin foundation into a single daily dose.</span></li></ul>',
                'desc_ar' => '<ul class="space-y-3 text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85"><li class="flex items-start gap-2.5"><span class="w-2 h-2 rounded-full bg-[#67B34A] mt-1.5 shrink-0"></span><span><strong class="text-[#031827] dark:text-[#F6F5EF] font-bold">دعم نتروبيك شامل:</strong> تركيبة غنية بـ 120 ملغ من الجنكة بيلوبا، وفوسفاتيديل سيرين، وفوسفاتيديل كولين للمساعدة في دعم الذاكرة والتركيز والوظائف الإدراكية.</span></li><li class="flex items-start gap-2.5"><span class="w-2 h-2 rounded-full bg-[#67B34A] mt-1.5 shrink-0"></span><span><strong class="text-[#031827] dark:text-[#F6F5EF] font-bold">طاقة خلوية فائقة الفعالية:</strong> مدعم بفيتامين B12 ومجموعة فيتامينات B بجرعات عالية للمساعدة في استقلاب الطاقة ومكافحة الإجهاد الذهني.</span></li><li class="flex items-start gap-2.5"><span class="w-2 h-2 rounded-full bg-[#67B34A] mt-1.5 shrink-0"></span><span><strong class="text-[#031827] dark:text-[#F6F5EF] font-bold">حماية خلوية بمضادات الأكسدة:</strong> يحتوي على Co-Q10، وL-جلوتاثيون، وفيتامين E للمساعدة في حماية خلايا الدماغ والأنسجة العصبية من الإجهاد التأكسدي.</span></li><li class="flex items-start gap-2.5"><span class="w-2 h-2 rounded-full bg-[#67B34A] mt-1.5 shrink-0"></span><span><strong class="text-[#031827] dark:text-[#F6F5EF] font-bold">معادن أساسية للدماغ والغدة الدرقية:</strong> يوفر جرعات رئيسية من الزنك، واليود، والحديد لدعم توازن النواقل العصبية، ووظيفة الغدة الدرقية، وأكسجة الدماغ الطبيعية.</span></li><li class="flex items-start gap-2.5"><span class="w-2 h-2 rounded-full bg-[#67B34A] mt-1.5 shrink-0"></span><span><strong class="text-[#031827] dark:text-[#F6F5EF] font-bold">قرص يومي واحد متكامل وسهل التناول:</strong> يجمع بين المغذيات المعززة للدماغ وقاعدة الفيتامينات المتعددة الكاملة في جرعة يومية واحدة مريحة.</span></li></ul>'
            ],
            6 => [
                'icon' => 'fa-solid fa-circle-question',
                'menu_en' => 'INFO & FAQS',
                'menu_ar' => 'معلومات وأسئلة شائعة',
                'title_en' => 'Important Information & FAQs',
                'title_ar' => 'معلومات هامة وأسئلة شائعة',
                'tag_en' => 'Usage, Safety & Clinical Guidance',
                'tag_ar' => 'السلامة وطريقة الاستخدام والإرشادات',
                'desc_en' => '<div class="space-y-3.5 text-xs"><div class="p-3 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-900 dark:text-amber-200"><div class="font-bold text-xs uppercase flex items-center gap-1.5 mb-1 text-amber-700 dark:text-amber-300"><i class="fa-solid fa-triangle-exclamation"></i> Warning & Usage Guidance</div><p class="text-[11px] leading-relaxed">Always read product directions before use. Do not exceed recommended intake. Contains Ginkgo Biloba; those taking anticoagulants (blood thinners) should consult their doctor before using. Contains iron (harmful to very young children in excess). Seek professional advice if pregnant, breast-feeding, under medical supervision, or suffering from allergies. Do not take if allergic to soya. Food supplements must not replace a varied diet and healthy lifestyle.</p></div><div class="space-y-2.5 text-[#031827]/85 dark:text-[#F6F5EF]/85"><div><strong class="text-[#031827] dark:text-[#F6F5EF] block text-xs">Why has Blue Mind been developed?</strong><p class="text-[11px] leading-relaxed text-[#031827]/75 dark:text-[#F6F5EF]/75 mt-0.5">Maintaining mental performance requires optimal functioning of brain cells. Blue Mind safeguards your dietary intake of essential nutrients such as iron, zinc, and iodine to contribute to normal cognitive function.</p></div><div><strong class="text-[#031827] dark:text-[#F6F5EF] block text-xs">When is Blue Mind recommended?</strong><p class="text-[11px] leading-relaxed text-[#031827]/75 dark:text-[#F6F5EF]/75 mt-0.5">Recommended for men and women of all ages, and ideal for exam periods or intensive professional qualification study.</p></div><div><strong class="text-[#031827] dark:text-[#F6F5EF] block text-xs">Can Blue Mind be taken simultaneously with other medications?</strong><p class="text-[11px] leading-relaxed text-[#031827]/75 dark:text-[#F6F5EF]/75 mt-0.5">Free from drugs and hormones. Contains Ginkgo Biloba (consult doctor/pharmacist if taking blood thinners).</p></div><div><strong class="text-[#031827] dark:text-[#F6F5EF] block text-xs">How and when should Blue Mind be used?</strong><p class="text-[11px] leading-relaxed text-[#031827]/75 dark:text-[#F6F5EF]/75 mt-0.5">1 tablet per day with or immediately after your main meal, with water or cold drink. Do not chew. Always take on a full stomach.</p></div><div><strong class="text-[#031827] dark:text-[#F6F5EF] block text-xs">Side effects & Duration of benefits:</strong><p class="text-[11px] leading-relaxed text-[#031827]/75 dark:text-[#F6F5EF]/75 mt-0.5">No known side effects when taken as directed. Benefits build over several weeks with regular intake; no maximum duration limit.</p></div></div></div>',
                'desc_ar' => '<div class="space-y-3.5 text-xs"><div class="p-3 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-900 dark:text-amber-200"><div class="font-bold text-xs uppercase flex items-center gap-1.5 mb-1 text-amber-700 dark:text-amber-300"><i class="fa-solid fa-triangle-exclamation"></i> تحذير وإرشادات هامة</div><p class="text-[11px] leading-relaxed">اقرأ دائمًا إرشادات المنتج قبل الاستخدام. لا تتجاوز الجرعة الموصى بها. يحتوي على الجنكة بيلوبا؛ يجب على من يتناولون مضادات التخثر (مسيلات الدم) استشارة الطبيب. يحتوي على الحديد. استشر طبيبك في حال الحمل، الإرضاع، أو وجود حساسية. يحتوي على الصويا. لا تغني المكملات عن نظام غذائي متوازن ونمط حياة صحي.</p></div><div class="space-y-2.5 text-[#031827]/85 dark:text-[#F6F5EF]/85"><div><strong class="text-[#031827] dark:text-[#F6F5EF] block text-xs">لماذا تم تطوير بلو مايند؟</strong><p class="text-[11px] leading-relaxed text-[#031827]/75 dark:text-[#F6F5EF]/75 mt-0.5">يتطلب الحفاظ على الأداء العقلي عمل خلايا الدماغ والشبكة العصبية المعقدة بكفاءة مثالية. يوفر بلو مايند تركيبة متكاملة لحماية مدخولك الغذائي وتزويدك بالحديد والزنك واليود للوظائف الإدراكية.</p></div><div><strong class="text-[#031827] dark:text-[#F6F5EF] block text-xs">متى يُنصح بتناول بلو مايند؟</strong><p class="text-[11px] leading-relaxed text-[#031827]/75 dark:text-[#F6F5EF]/75 mt-0.5">يوصى به للرجال والنساء من جميع الأعمار، ومثالي للطلاب خلال فترات الامتحانات والمهنيين الذين تتطلب أعمالهم تركيزاً عالياً.</p></div><div><strong class="text-[#031827] dark:text-[#F6F5EF] block text-xs">هل يمكن تناوله مع أدوية أخرى؟</strong><p class="text-[11px] leading-relaxed text-[#031827]/75 dark:text-[#F6F5EF]/75 mt-0.5">خالٍ من العقاقير والهرمونات. نظرًا لاحتوائه على الجنكة بيلوبا، يُنصح باستشارة الطبيب أو الصيدلي في حال تناول مسيلات الدم.</p></div><div><strong class="text-[#031827] dark:text-[#F6F5EF] block text-xs">كيف ومتى يُستخدم؟</strong><p class="text-[11px] leading-relaxed text-[#031827]/75 dark:text-[#F6F5EF]/75 mt-0.5">قرص واحد يوميًا مع الوجبة الرئيسية أو بعدها مباشرة مع الماء أو مشروب بارد دون مضغ وعلى معدة ممتلئة لزيادة الامتصاص وتجنب الغثيان.</p></div><div><strong class="text-[#031827] dark:text-[#F6F5EF] block text-xs">هل هناك آثار جانبية وكم تستغرق النتائج؟</strong><p class="text-[11px] leading-relaxed text-[#031827]/75 dark:text-[#F6F5EF]/75 mt-0.5">ليس له آثار جانبية معروفة عند تناوله وفق التعليمات. تظهر الفوائد تدريجيًا على مدار عدة أسابيع من الاستخدام المنتظم، ولا توجد مدة أقصى للاستخدام.</p></div></div></div>'
            ],
        ];

        $bluezonePillars = [];
        for ($i = 1; $i <= 6; $i++) {
            $pDef = $defaultPillars[$i];
            $pad = str_pad($i, 2, '0', STR_PAD_LEFT);
            $num = \App\Models\Setting::get("pillars_item_{$i}_num", $pad);
            $icon = \App\Models\Setting::get("pillars_item_{$i}_icon", $pDef['icon']);
            $menuTitle = \App\Models\Setting::get("pillars_item_{$i}_menu_{$locale}", $pDef["menu_{$locale}"]);
            $title = \App\Models\Setting::get("pillars_item_{$i}_title_{$locale}", $pDef["title_{$locale}"]);
            $tag = \App\Models\Setting::get("pillars_item_{$i}_tag_{$locale}", $pDef["tag_{$locale}"]);
            $desc = \App\Models\Setting::get("pillars_item_{$i}_desc_{$locale}", $pDef["desc_{$locale}"]);

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
                <span
                    class="text-[10px] sm:text-[11px] font-extrabold uppercase tracking-[0.25em] sm:tracking-[0.3em]"
                    style="color: {{ $activeTheme['hex'] }};">
                    {{ $eyebrow }}
                </span>
                <h2
                    class="text-2xl sm:text-4xl lg:text-5xl font-light text-[#031827] dark:text-[#F6F5EF] tracking-tight">
                    {{ $headingPrefix }} <span
                        class="font-bold" style="color: {{ $activeTheme['hex'] }};">{{ $headingHighlight }}</span>
                </h2>
                <p
                    class="text-xs sm:text-sm text-[#031827]/70 dark:text-[#F6F5EF]/70 font-medium max-w-xl mx-auto leading-relaxed">
                    {{ $sectionDesc }}
                </p>
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
                        <div class="hidden lg:flex lg:col-span-4 items-center justify-center relative py-4">
                            <div class="w-56 h-56 xl:w-72 xl:h-72 relative flex items-center justify-center">
                                <svg class="w-full h-full" viewBox="0 0 300 300" fill="none">
                                    <circle cx="150" cy="150" r="110" stroke="#0A4F78" stroke-width="1.5"
                                        stroke-opacity="0.25" stroke-dasharray="4 4" />
                                    <circle cx="150" cy="150" r="70" stroke="#2A8FC2" stroke-width="1"
                                        stroke-opacity="0.2" />

                                    <line id="spoke-0" x1="150" y1="150" x2="150" y2="40"
                                        stroke="{{ $activeTheme['hex'] }}" stroke-width="2" opacity="0.9" />
                                    <line id="spoke-1" x1="150" y1="150" x2="245" y2="95"
                                        stroke="#2A8FC2" stroke-width="1" opacity="0.3" />
                                    <line id="spoke-2" x1="150" y1="150" x2="245" y2="205"
                                        stroke="#2A8FC2" stroke-width="1" opacity="0.3" />
                                    <line id="spoke-3" x1="150" y1="150" x2="150" y2="260"
                                        stroke="#2A8FC2" stroke-width="1" opacity="0.3" />
                                    <line id="spoke-4" x1="150" y1="150" x2="55" y2="205"
                                        stroke="#2A8FC2" stroke-width="1" opacity="0.3" />
                                    <line id="spoke-5" x1="150" y1="150" x2="55" y2="95"
                                        stroke="#2A8FC2" stroke-width="1" opacity="0.3" />

                                    <circle id="node-0" cx="150" cy="40" r="10" fill="{{ $activeTheme['hex'] }}"
                                        stroke="#FFFFFF" stroke-width="2" class="transition-all duration-300" />
                                    <circle id="node-1" cx="245" cy="95" r="7" fill="#0A4F78"
                                        stroke="#2A8FC2" stroke-width="1.5" class="transition-all duration-300" />
                                    <circle id="node-2" cx="245" cy="205" r="7" fill="#0A4F78"
                                        stroke="#2A8FC2" stroke-width="1.5" class="transition-all duration-300" />
                                    <circle id="node-3" cx="150" cy="260" r="7" fill="#0A4F78"
                                        stroke="#2A8FC2" stroke-width="1.5" class="transition-all duration-300" />
                                    <circle id="node-4" cx="55" cy="205" r="7" fill="#0A4F78"
                                        stroke="#2A8FC2" stroke-width="1.5" class="transition-all duration-300" />
                                    <circle id="node-5" cx="55" cy="95" r="7" fill="#0A4F78"
                                        stroke="#2A8FC2" stroke-width="1.5" class="transition-all duration-300" />
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
                                        PILLAR PROFILE
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
                for (let i = 0; i < 6; i++) {
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
