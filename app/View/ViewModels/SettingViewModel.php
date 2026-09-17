<?php

namespace App\View\ViewModels;

class SettingViewModel
{
    /**
     * Get system configuration settings.
     *
     * @return array<string, mixed>
     */
    public static function all(): array
    {
        return [
            // General & Brand
            'site_name' => 'BLUE ZONE™ Longevity & Cellular Health',
            'store_name' => 'BLUE ZONE™ Longevity & Cellular Health',
            'tagline' => 'Cellular Longevity & Botanical Medicine',
            'default_language' => 'en',
            'default_locale' => 'en',
            'supported_locales' => ['en' => 'English (LTR)', 'ar' => 'العربية (RTL)'],
            'currency' => 'USD',
            'default_currency' => 'SAR',
            'currency' => 'SAR',
            'currency_symbol' => '',
            'currency_position' => 'after',
            'currency_decimals' => 2,
            'timezone' => 'Asia/Riyadh',
            'contact_email' => 'care@bluezone.com',
            'support_email' => 'care@bluezone.com',
            'contact_phone' => '+966 800 123 4567',
            'support_phone' => '+966 800 123 4567',
            'enable_whatsapp' => true,
            'whatsapp_number' => '+966501234567',
            'whatsapp_default_message' => 'Hello BLUE ZONE, I would like clinical guidance on longevity formulations.',
            'whatsapp_position' => 'auto',

            // Social Media & External Channels (Storefront Footer)
            'social_instagram' => '',
            'social_x' => '',
            'social_facebook' => '',
            'social_linkedin' => '',
            'social_youtube' => '',
            'social_tiktok' => '',
            'social_snapchat' => '',
            'social_telegram' => '',
            'social_whatsapp' => '',
            'social_pinterest' => '',

            // Store & Inventory
            'low_stock_threshold' => 10,
            'inventory_low_stock_global_threshold' => 10,
            'zero_stock_behavior' => 'mark_out_of_stock',
            'enable_backorders' => false,
            'enable_reviews' => true,
            'enable_coupons' => true,

            // Payments & Tax
            'tax_percentage' => 15,
            'tax_number' => '31004829100003',
            'enable_online_payment' => true,
            'enable_cod' => true,
            'payment_stripe_enabled' => true,
            'payment_stripe_mode' => 'test',
            'payment_stripe_public_key' => 'pk_test_51MockStripeKeyBlueZoneLongevityDemo',
            'payment_stripe_secret_key' => 'sk_test_51MockStripeSecretBlueZoneLongevityDemo',
            'payment_stripe_webhook_secret' => 'whsec_mockBlueZoneWebhookSecret2026',
            'payment_cod_enabled' => true,
            'payment_cod_extra_fee' => 0.00,
            'payment_default_gateway' => 'stripe',
            'active_payment_methods' => ['Credit Card', 'Apple Pay', 'Mada', 'Cash on Delivery'],

            // Shipping & Logistics
            'free_shipping_threshold' => 75.00,
            'flat_shipping_rate' => 9.99,

            // Notifications
            'notify_low_stock' => true,
            'notify_new_order' => true,

            // System Prefixes & Theme
            'order_prefix' => 'BZ-',
            'invoice_prefix' => 'INV-',
            'theme_preference' => 'light',

            // ==========================================
            // LANDING PAGE CONFIGURATION & CMS DEFAULTS
            // ==========================================
            'landing_sections_order' => [
                'hero_slider',
                'who_we_are',
                'philosophy',
                'new_arrivals',
                'featured_products',
                'products_vertical',
                'blue_mind_flagship',
                'five_blue_zones',
                'bluemint_preps',
                'our_science',
                'journal_news',
                'final_cta',
            ],

            // Master Section Switches
            'landing_hero_slider_enabled' => true,
            'landing_who_we_are_enabled' => true,
            'landing_philosophy_enabled' => true,
            'landing_new_arrivals_enabled' => true,
            'landing_featured_products_enabled' => true,
            'landing_products_vertical_enabled' => true,
            'landing_blue_mind_flagship_enabled' => true,
            'landing_five_blue_zones_enabled' => true,
            'landing_bluemint_preps_enabled' => true,
            'landing_our_science_enabled' => true,
            'landing_journal_news_enabled' => true,
            'landing_final_cta_enabled' => true,
            
            // 1. Top Announcement Bar
            'landing_announcement_enabled' => true,
            'landing_announcement_badge_en' => 'GLOBAL CLINICAL EXPEDITION',
            'landing_announcement_badge_ar' => 'بعثة الأبحاث السريرية العالمية',
            'landing_announcement_text_en' => 'Complimentary worldwide cold-chain shipping on all longevity orders over $75',
            'landing_announcement_text_ar' => 'شحن مبرد مجاني لجميع طلبات تعزيز طول العمر التي تتجاوز 75 دولاراً',
            'landing_announcement_link' => '/shop',

            // 2. Hero Main Headline & Value Proposition
            'landing_hero_badge_en' => 'CENTENARIAN WISDOM & CELLULAR MEDICINE',
            'landing_hero_badge_ar' => 'حكمة المعمرين والطب الخلوي المتقدم',
            'landing_hero_title_en' => 'LIVE LONG. LIVE WELL.',
            'landing_hero_title_ar' => 'عش أطول. عش بحيوية فائقة.',
            'landing_hero_subtitle_en' => 'Translating the lifestyle, diet, and biological resilience of the world’s 5 longest-lived communities into modern wellness formulations.',
            'landing_hero_subtitle_ar' => 'ترجمة أسلوب الحياة والتغذية والمرونة البيولوجية لأطول 5 مجتمعات عمراً في العالم إلى تركيبات وقائية متطورة.',
            'landing_hero_cta_primary_text_en' => 'DISCOVER OUR STORY',
            'landing_hero_cta_primary_text_ar' => 'اكتشف قصتنا وأبحاثنا',
            'landing_hero_cta_primary_link' => '#who-we-are',
            'landing_hero_cta_secondary_text_en' => 'EXPLORE FORMULATIONS',
            'landing_hero_cta_secondary_text_ar' => 'استكشف المستحضرات الطبية',
            'landing_hero_cta_secondary_link' => '/shop',

            // 3. Clinical Trust & Purity Stats Bar
            'landing_stats_enabled' => true,
            'landing_stat_1_val' => '99.8%',
            'landing_stat_1_label_en' => 'Standardized Active Molecular Purity',
            'landing_stat_1_label_ar' => 'نقاء جزيئي قياسي معتمد للمواد الفعالة',
            'landing_stat_2_val' => '5 Regions',
            'landing_stat_2_label_en' => 'Blue Zones Validated Longevity Ecosystems',
            'landing_stat_2_label_ar' => 'أقاليم المناطق الزرقاء الموثقة سريرياً',
            'landing_stat_3_val' => '100%',
            'landing_stat_3_label_en' => 'Bio-Identical Cellular Bioavailability',
            'landing_stat_3_label_ar' => 'توافر حيوي خلوي مطابق حيوياً بنسبة 100%',
            'landing_stat_4_val' => '24/7',
            'landing_stat_4_label_en' => 'Longevity Guidance & Clinical Protocol Advisory',
            'landing_stat_4_label_ar' => 'إرشاد طبي متخصص واستشارات بروتوكولات طول العمر',

            // 4. Who We Are & Philosophy Section
            'landing_philosophy_badge_en' => 'CENTENARIAN WISDOM',
            'landing_philosophy_badge_ar' => 'حكمة المعمرين البيولوجية',
            'landing_philosophy_title_en' => 'Rooted in Nature. Validated by Modern Cellular Biology.',
            'landing_philosophy_title_ar' => 'متجذرة في الطبيعة، ومثبتة بأحدث علوم البيولوجيا الخلوية.',
            'landing_philosophy_desc_en' => 'For over two decades, longevity researchers studied the world’s Blue Zones—remote pockets on Earth where individuals regularly thrive past 100 with extraordinary physical vitality. BLUE ZONE™ was founded to formulate these precise biological mechanisms.',
            'landing_philosophy_desc_ar' => 'على مدار أكثر من عقدين، عكف علماء أبحاث طول العمر على دراسة المناطق الزرقاء، تلك البقاع الفريدة حول العالم التي يتجاوز سكانها سن المائة بحيوية ونشاط استثنائي. تأسست بلو زون™ لترجمة هذه المسارات الحيوية إلى مستحضرات دقيقة.',

            // 5. Five Blue Zones Interactive Geographic Section
            'landing_zones_badge_en' => 'THE FIVE LONGEVITY ECOSYSTEMS',
            'landing_zones_badge_ar' => 'الأقاليم الخمسة المعمرة حول العالم',
            'landing_zones_title_en' => 'Explore the Blueprint of Longevity Across Continents',
            'landing_zones_title_ar' => 'استكشف خارطة طول العمر والصحة الخلوية عبر القارات',
            'landing_zones_desc_en' => 'From Okinawa’s marine polyphenols to Sardinia’s mountain flavonoids, discover the geographical sources behind our formulations.',
            'landing_zones_desc_ar' => 'من بوليفينولات أوكيناوا البحرية إلى فلافونويدات جبال سردينيا، اكتشف المصادر الجغرافية الأصيلة وراء تركيباتنا.',

            // 6. Featured Products Showcase
            'landing_products_badge_en' => 'CLINICAL FORMULATIONS',
            'landing_products_badge_ar' => 'التركيبات الطبية السريرية',
            'landing_products_title_en' => 'Engineered for Systemic Longevity & Vitality',
            'landing_products_title_ar' => 'مصممة خصيصاً للصحة الخلوية وطول العمر المديد',
            'landing_products_subtitle_en' => 'Targeted botanical bio-compounds designed to support cellular repair, cognitive sharpness, and daily metabolic energy.',
            'landing_products_subtitle_ar' => 'مركبات نباتية نشطة بيولوجياً تستهدف تحفيز الترميم الخلوي، تعزيز صفاء الذهن، ودعم الطاقة الأيضية اليومية.',
            'landing_products_limit' => 6,
            'landing_products_cta_text_en' => 'VIEW ALL FORMULATIONS',
            'landing_products_cta_text_ar' => 'عرض جميع المستحضرات',

            // 7. Clinical Quality & Verification Standards
            'landing_quality_enabled' => true,
            'landing_quality_badge_en' => 'CLINICAL INTEGRITY & PURITY',
            'landing_quality_badge_ar' => 'النزاهة السريرية ومعايير النقاء',
            'landing_quality_title_en' => 'Uncompromising Pharmaceutical-Grade Standards',
            'landing_quality_title_ar' => 'معايير تصنيع صيدلانية صارمة لا تقبل المساومة',
            'landing_quality_desc_en' => 'Every single formulation is manufactured in cGMP-certified, FDA-registered facilities and undergoes rigorous triple third-party HPLC assays.',
            'landing_quality_desc_ar' => 'تُصنع جميع تركيباتنا داخل منشآت معتمدة وفق معايير التصنيع الدوائي cGMP ومسجلة لدى هيئات الغذاء والدواء، وتخضع لفحوصات ثلاثية مخبرية مستقلة.',

            // 8. Testimonials & Clinical Endorsements
            'landing_testimonials_enabled' => true,
            'landing_testimonials_badge_en' => 'CLINICAL & CLIENT ENDORSEMENTS',
            'landing_testimonials_badge_ar' => 'شهادات وتجارب العملاء والأطباء',
            'landing_testimonials_title_en' => 'Trusted by Clinicians and Longevity Seekers Worldwide',
            'landing_testimonials_title_ar' => 'موثوق من كبار الأطباء والباحثين عن جودة الحياة حول العالم',
            'landing_testimonials_subtitle_en' => 'Real experiences from patients, biohackers, and longevity physicians integrating Blue Zone into daily protocols.',
            'landing_testimonials_subtitle_ar' => 'تجارب حقيقية من ممارسي الرعاية الصحية والأفراد الملتزمين بنمط حياة حيوي مستدام.',

            // 9. FAQ Accordion Section
            'landing_faqs_enabled' => true,
            'landing_faqs_badge_en' => 'FREQUENTLY ASKED QUESTIONS',
            'landing_faqs_badge_ar' => 'الأسئلة الشائعة والإرشادات',
            'landing_faqs_title_en' => 'Everything You Need to Know About Our Formulations',
            'landing_faqs_title_ar' => 'كل ما تود معرفته حول تركيباتنا وبروتوكولات الاستخدام',
            'landing_faqs_subtitle_en' => 'Find clinical answers regarding dosages, synergies, sourcing purity, and subscription delivery schedules.',
            'landing_faqs_subtitle_ar' => 'إجابات طبية دقيقة حول الجرعات، التناغم بين المستحضرات، مصادر النقاء، وجداول الشحن والتسليم.',

            // 10. Newsletter & Longevity Protocol Capture
            'landing_newsletter_enabled' => true,
            'landing_newsletter_badge_en' => 'JOIN THE LONGEVITY COLLECTIVE',
            'landing_newsletter_badge_ar' => 'انضم إلى مجتمع طول العمر والعافية',
            'landing_newsletter_title_en' => 'Begin Your Biological Longevity Protocol Today',
            'landing_newsletter_title_ar' => 'ابدأ بروتوكولك الخلوي للوقاية وطول العمر اليوم',
            'landing_newsletter_desc_en' => 'Subscribe to receive exclusive clinical research briefings, early access to new micro-batch formulations, and 15% off your initial order.',
            'landing_newsletter_desc_ar' => 'اشترك لتصلك أحدث أوراق الأبحاث الطبية، وأسبقية الحصول على التشغيلات الإنتاجية المحدودة، مع خصم 15% على طلبك الأول.',
            'landing_newsletter_discount_badge' => '15% WELCOME OFFER',
            'landing_newsletter_btn_en' => 'SUBSCRIBE NOW',
            'landing_newsletter_btn_ar' => 'اشترك الآن مجاناً',

            // 11. Landing Page SEO & Meta Tags
            'landing_meta_title_en' => 'BLUE ZONE™ — Cellular Longevity & Botanical Medicine',
            'landing_meta_title_ar' => 'بلو زون™ — الطب الخلوي وطول العمر والمستحضرات النباتية',
            'landing_meta_desc_en' => 'Discover pharmaceutical-grade cellular formulations inspired by the world’s longest-lived centenarian communities. Standardized bio-actives for NAD+ and mitochondrial vitality.',
            'landing_meta_desc_ar' => 'اكتشف تركيبات خلوية صيدلانية مستوحاة من أطول مجتمعات العالم عمراً. مستخلصات قياسية نقية لدعم طاقة الميتوكوندريا وإنزيم NAD+ والتجدد الخلوي.',
            'landing_meta_keywords' => 'longevity, blue zones, cellular health, NAD+, mitochondrial energy, Nootropics, anti-aging, botanical medicine',

            // ==========================================
            // 6 PILLARS MANAGEMENT & REDESIGN DEFAULTS
            // ==========================================
            'pillars_layout_format' => 'orbital_matrix',
            'pillars_accent_color' => 'green',
            'pillars_card_style' => 'adaptive',
            'pillars_show_orbital' => true,
            'pillars_show_tags' => true,
            'pillars_show_numbers' => true,

            'pillars_eyebrow_en' => '',
            'pillars_eyebrow_ar' => '',
            'pillars_heading_prefix_en' => 'About',
            'pillars_heading_prefix_ar' => 'عن',
            'pillars_heading_highlight_en' => 'Blue Zone',
            'pillars_heading_highlight_ar' => 'بلو زون',
            'pillars_description_en' => '',
            'pillars_description_ar' => '',
            'pillars_center_title_en' => 'BLUE MIND',
            'pillars_center_title_ar' => 'بلو مايند',
            'pillars_center_subtitle_en' => 'CORE',
            'pillars_center_subtitle_ar' => 'الجوهر',

            // Item 1
            'pillars_item_1_num' => '01',
            'pillars_item_1_icon' => 'fa-solid fa-brain',
            'pillars_item_1_menu_en' => 'THE SCIENCE',
            'pillars_item_1_menu_ar' => 'العلم وراء التركيبة',
            'pillars_item_1_title_en' => 'The Science Behind Blue Mind',
            'pillars_item_1_title_ar' => 'العلم وراء بلو مايند',
            'pillars_item_1_tag_en' => 'Cognitive Optimization & Safeguard',
            'pillars_item_1_tag_ar' => 'تحسين الإدراك والحماية الخلوية',
            'pillars_item_1_desc_en' => '<p class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 font-normal leading-relaxed mb-3">The brain acts as the command center of your nervous system and contains over <strong class="text-[#0A4F78] dark:text-[#2A8FC2] font-semibold">80 billion intricate neural pathways</strong>. It is highly demanding, using about <strong class="text-[#0A4F78] dark:text-[#2A8FC2] font-semibold">30% of the energy</strong> your body produces from food.</p><p class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 font-normal leading-relaxed mb-3">Since brain cells are irreplaceable, they have the highest priority for specific micronutrients. Just like any advanced machine, the quality of what you put in directly affects performance.</p><div class="p-3.5 rounded-xl bg-[#67B34A]/10 border border-[#67B34A]/25 text-xs text-[#031827] dark:text-[#F6F5EF] leading-relaxed"><strong class="text-[#67B34A] font-bold">Blue mind</strong> is a scientifically formulated, all-in-one daily supplement designed to safeguard your dietary intake and optimize cognitive function.</div>',
            'pillars_item_1_desc_ar' => '<p class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 font-normal leading-relaxed mb-3">يعمل الدماغ كمركز تحكم لجهازك العصبي ويحتوي على أكثر من <strong class="text-[#0A4F78] dark:text-[#2A8FC2] font-semibold">80 مليار مسار عصبي معقد</strong>. يستهلك الدماغ طاقة هائلة، حيث يحتاج إلى حوالي <strong class="text-[#0A4F78] dark:text-[#2A8FC2] font-semibold">30% من الطاقة</strong> التي ينتجها جسمك من الطعام.</p><p class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 font-normal leading-relaxed mb-3">ولأن خلايا الدماغ لا يمكن تعويضها أو استبدالها، فإنها تحظى بالأولوية القصوى للعناصر الغذائية الدقيقة المتخصصة. تمامًا مثل أي محرك متقدم، فإن جودة ما تغذيه به تؤثر مباشرة على كفاءته وأدائه.</p><div class="p-3.5 rounded-xl bg-[#67B34A]/10 border border-[#67B34A]/25 text-xs text-[#031827] dark:text-[#F6F5EF] leading-relaxed"><strong class="text-[#67B34A] font-bold">بلو مايند</strong> هو مكمل يومي شامل ومُصاغ علميًا لضمان حماية مدخولك الغذائي وتحسين وظائفك الإدراكية إلى أقصى حد.</div>',

            // Item 2
            'pillars_item_2_num' => '02',
            'pillars_item_2_icon' => 'fa-solid fa-bolt',
            'pillars_item_2_menu_en' => 'TARGETED NOOTROPICS',
            'pillars_item_2_menu_ar' => 'منشطات الإدراك',
            'pillars_item_2_title_en' => 'Targeted Nootropics for Mental Performance',
            'pillars_item_2_title_ar' => 'منشطات إدراكية مستهدفة للأداء العقلي',
            'pillars_item_2_tag_en' => 'Memory, Focus & Alertness',
            'pillars_item_2_tag_ar' => 'الذاكرة والتركيز واليقظة الذهنية',
            'pillars_item_2_desc_en' => '<p class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 font-medium leading-relaxed mb-3">Our advanced formula combines specialist nutrients designed to support memory, focus, and psychological function:</p><ul class="space-y-3 text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85"><li class="flex items-start gap-2.5"><span class="w-2 h-2 rounded-full bg-[#67B34A] mt-1.5 shrink-0"></span><span><strong class="text-[#031827] dark:text-[#F6F5EF] font-bold">Ginkgo Biloba (120 mg):</strong> Enhances cerebral blood flow and nutrient delivery, helping to maintain memory with age and support mental alertness.</span></li><li class="flex items-start gap-2.5"><span class="w-2 h-2 rounded-full bg-[#67B34A] mt-1.5 shrink-0"></span><span><strong class="text-[#031827] dark:text-[#F6F5EF] font-bold">Essential Phospholipids:</strong> Phosphatidylserine and Phosphatidylcholine preserve neural membrane integrity and support acetylcholine production, which is crucial for learning speed and memory storage.</span></li><li class="flex items-start gap-2.5"><span class="w-2 h-2 rounded-full bg-[#67B34A] mt-1.5 shrink-0"></span><span><strong class="text-[#031827] dark:text-[#F6F5EF] font-bold">Cellular Antioxidant Defense:</strong> Co-Q10, L-Glutathione, and Selenium protect delicate brain tissue from oxidative stress and cellular damage.</span></li></ul>',
            'pillars_item_2_desc_ar' => '<p class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 font-medium leading-relaxed mb-3">تجمع تركيبتنا المتطورة بين مغذيات تخصصية فائقة لدعم الذاكرة والتركيز والوظائف النفسية السليمة:</p><ul class="space-y-3 text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85"><li class="flex items-start gap-2.5"><span class="w-2 h-2 rounded-full bg-[#67B34A] mt-1.5 shrink-0"></span><span><strong class="text-[#031827] dark:text-[#F6F5EF] font-bold">الجنكة بيلوبا (120 ملغ):</strong> تعزز تدفق الدم الدماغي وتوصيل المغذيات الحيوية، مما يساعد على دعم الذاكرة مع التقدم في العمر وتعزيز اليقظة الذهنية.</span></li><li class="flex items-start gap-2.5"><span class="w-2 h-2 rounded-full bg-[#67B34A] mt-1.5 shrink-0"></span><span><strong class="text-[#031827] dark:text-[#F6F5EF] font-bold">الفسفوليبيدات الأساسية:</strong> يحافظ كل من الفوسفاتيديل سيرين والفوسفاتيديل كولين على سلامة الغشاء العصبي ويدعمان إنتاج الأسيتيل كولين، الحاسم لسرعة التعلم وتخزين الذاكرة.</span></li><li class="flex items-start gap-2.5"><span class="w-2 h-2 rounded-full bg-[#67B34A] mt-1.5 shrink-0"></span><span><strong class="text-[#031827] dark:text-[#F6F5EF] font-bold">الدفاع الخلوي المضاد للأكسدة:</strong> يحمي الإنزيم المساعد Co-Q10، وL-جلوتاثيون، والسيلينيوم أنسجة الدماغ الحساسة من الإجهاد التأكسدي والتلف الخلوي.</span></li></ul>',

            // Item 3
            'pillars_item_3_num' => '03',
            'pillars_item_3_icon' => 'fa-solid fa-flask-vial',
            'pillars_item_3_menu_en' => 'VITAMINS & COFACTORS',
            'pillars_item_3_menu_ar' => 'الفيتامينات والعوامل المساعدة',
            'pillars_item_3_title_en' => 'Essential Vitamins & Neurological Cofactors',
            'pillars_item_3_title_ar' => 'فيتامينات أساسية وعوامل عصبية مساعدة',
            'pillars_item_3_tag_en' => 'Energy Metabolism & Neuro-Balance',
            'pillars_item_3_tag_ar' => 'أيض الطاقة وتوازن النواقل العصبية',
            'pillars_item_3_desc_en' => '<p class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 font-medium leading-relaxed mb-3">A healthy brain relies on a broad supply of essential vitamins and minerals to maintain optimal cognitive capacity and a healthy nervous system:</p><ul class="space-y-3 text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85"><li class="flex items-start gap-2.5"><span class="w-2 h-2 rounded-full bg-[#2A8FC2] mt-1.5 shrink-0"></span><span><strong class="text-[#031827] dark:text-[#F6F5EF] font-bold">High-Potency B-Complex:</strong> High doses of B-vitamins (including B12, B6, and Pantothenic Acid) optimize cellular energy metabolism, reduce central nervous system fatigue, and support normal psychological function.</span></li><li class="flex items-start gap-2.5"><span class="w-2 h-2 rounded-full bg-[#2A8FC2] mt-1.5 shrink-0"></span><span><strong class="text-[#031827] dark:text-[#F6F5EF] font-bold">Key Brain Minerals:</strong> Zinc, Iodine, and Iron plus Pantothenic acid work synergistically to support neurotransmitter balance, thyroid function, and normal cognitive function.</span></li><li class="flex items-start gap-2.5"><span class="w-2 h-2 rounded-full bg-[#2A8FC2] mt-1.5 shrink-0"></span><span><strong class="text-[#031827] dark:text-[#F6F5EF] font-bold">Optimum Vitamin D3 (1000 IU):</strong> Delivers the preferred D3 form to support neuroprotective pathways, mood regulation, and overall immune health.</span></li></ul>',
            'pillars_item_3_desc_ar' => '<p class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 font-medium leading-relaxed mb-3">يعتمد الدماغ الصحي على إمداد واسع ومتوازن من الفيتامينات والمعادن الأساسية للحفاظ على القدرة الإدراكية القصوى وجهاز عصبي سليم:</p><ul class="space-y-3 text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85"><li class="flex items-start gap-2.5"><span class="w-2 h-2 rounded-full bg-[#2A8FC2] mt-1.5 shrink-0"></span><span><strong class="text-[#031827] dark:text-[#F6F5EF] font-bold">فيتامينات ب عالية الفعالية:</strong> جرعات متقدمة من فيتامينات ب (بما في ذلك B12 وB6 وحمض البانتوثنيك) لتحفيز أيض الطاقة الخلوية وتقليل إجهاد الجهاز العصبي المركزي ودعم الوظائف النفسية الطبيعية.</span></li><li class="flex items-start gap-2.5"><span class="w-2 h-2 rounded-full bg-[#2A8FC2] mt-1.5 shrink-0"></span><span><strong class="text-[#031827] dark:text-[#F6F5EF] font-bold">معادن الدماغ الحيوية:</strong> يعمل الزنك واليود والحديد مع حمض البانتوثنيك بتآزر تام لدعم توازن النواقل العصبية ووظائف الغدة الدرقية والأداء الإدراكي السليم.</span></li><li class="flex items-start gap-2.5"><span class="w-2 h-2 rounded-full bg-[#2A8FC2] mt-1.5 shrink-0"></span><span><strong class="text-[#031827] dark:text-[#F6F5EF] font-bold">فيتامين D3 بالجرعة المثالية (1000 وحدة دولية):</strong> يوفر الصورة المفضلة D3 لدعم المسارات الواقية للأعصاب وتنظيم المزاج وتعزيز صحة المناعة الشاملة.</span></li></ul>',

            // Item 4
            'pillars_item_4_num' => '04',
            'pillars_item_4_icon' => 'fa-solid fa-shield-halved',
            'pillars_item_4_menu_en' => 'DAILY FOUNDATION',
            'pillars_item_4_menu_ar' => 'الأساس اليومي',
            'pillars_item_4_title_en' => 'Your Complete Daily Foundation',
            'pillars_item_4_title_ar' => 'أساسك اليومي المتكامل',
            'pillars_item_4_tag_en' => 'Complete Multivitamin Base',
            'pillars_item_4_tag_ar' => 'قاعدة فيتامينات شاملة',
            'pillars_item_4_desc_en' => '<p class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 font-normal leading-relaxed mb-3">This formula goes beyond targeted brain health to provide a <strong class="text-[#0A4F78] dark:text-[#2A8FC2] font-semibold">complete multivitamin foundation</strong>.</p><p class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 font-normal leading-relaxed mb-4">With added <strong class="text-[#031827] dark:text-[#F6F5EF] font-semibold">Vitamin C, Copper, and Folic Acid</strong> to support vascular health, red blood cell formation, and natural energy release, an additional daily multivitamin is no longer necessary.</p><div class="p-3.5 rounded-xl bg-[#67B34A]/10 border border-[#67B34A]/25 text-xs text-[#031827] dark:text-[#F6F5EF] flex items-center gap-3"><span class="w-6 h-6 rounded-full bg-[#67B34A] text-white flex items-center justify-center shrink-0 text-xs font-bold"><i class="fa-solid fa-check"></i></span><span>Convenient all-in-one daily foundation replaces the need for additional general multivitamin tablets.</span></div>',
            'pillars_item_4_desc_ar' => '<p class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 font-normal leading-relaxed mb-3">تتجاوز هذه التركيبة دعم صحة الدماغ المستهدفة لتوفر <strong class="text-[#0A4F78] dark:text-[#2A8FC2] font-semibold">أساساً متكاملاً من الفيتامينات المتعددة اليومية</strong>.</p><p class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 font-normal leading-relaxed mb-4">مع إضافة <strong class="text-[#031827] dark:text-[#F6F5EF] font-semibold">فيتامين C، والنحاس، وحمض الفوليك</strong> لدعم صحة الأوعية الدموية، وتكوين خلايا الدم الحمراء، وإطلاق الطاقة الطبيعية، لم يعد هناك أي داعٍ لتناول مكمل فيتامينات متعددة يومي إضافي.</p><div class="p-3.5 rounded-xl bg-[#67B34A]/10 border border-[#67B34A]/25 text-xs text-[#031827] dark:text-[#F6F5EF] flex items-center gap-3"><span class="w-6 h-6 rounded-full bg-[#67B34A] text-white flex items-center justify-center shrink-0 text-xs font-bold"><i class="fa-solid fa-check"></i></span><span>تكامل يومي شامل يغنيك عن تناول أقراص فيتامينات متعددة منفصلة.</span></div>',

            // Item 5
            'pillars_item_5_num' => '05',
            'pillars_item_5_icon' => 'fa-solid fa-star',
            'pillars_item_5_menu_en' => 'CORE HIGHLIGHTS',
            'pillars_item_5_menu_ar' => 'أبرز المزايا',
            'pillars_item_5_title_en' => 'High-Performance Formula Highlights',
            'pillars_item_5_title_ar' => 'أبرز مزايا التركيبة المركزة',
            'pillars_item_5_tag_en' => 'Clinical Synergy & Potency',
            'pillars_item_5_tag_ar' => 'تآزر وفعالية سريرية',
            'pillars_item_5_desc_en' => '<ul class="space-y-3 text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85"><li class="flex items-start gap-2.5"><span class="w-2 h-2 rounded-full bg-[#67B34A] mt-1.5 shrink-0"></span><span><strong class="text-[#031827] dark:text-[#F6F5EF] font-bold">Comprehensive Nootropic Support:</strong> Formulated with 120 mg Ginkgo Biloba, Phosphatidylserine, and Phosphatidylcholine to help support memory, mental focus, and cognitive function.</span></li><li class="flex items-start gap-2.5"><span class="w-2 h-2 rounded-full bg-[#67B34A] mt-1.5 shrink-0"></span><span><strong class="text-[#031827] dark:text-[#F6F5EF] font-bold">High-Potency Cellular Energy:</strong> Packed with Vitamin B12 and high-dose B-complex vitamins to assist in energy metabolism and fight mental fatigue.</span></li><li class="flex items-start gap-2.5"><span class="w-2 h-2 rounded-full bg-[#67B34A] mt-1.5 shrink-0"></span><span><strong class="text-[#031827] dark:text-[#F6F5EF] font-bold">Cellular Antioxidant Defense:</strong> Features Co-Q10, L-Glutathione, and Vitamin E to help protect brain cells and neural tissue from oxidative stress.</span></li><li class="flex items-start gap-2.5"><span class="w-2 h-2 rounded-full bg-[#67B34A] mt-1.5 shrink-0"></span><span><strong class="text-[#031827] dark:text-[#F6F5EF] font-bold">Essential Brain & Thyroid Minerals:</strong> Supplies key doses of Zinc, Iodine, and Iron to support neurotransmitter balance, thyroid function, and normal brain oxygenation.</span></li><li class="flex items-start gap-2.5"><span class="w-2 h-2 rounded-full bg-[#67B34A] mt-1.5 shrink-0"></span><span><strong class="text-[#031827] dark:text-[#F6F5EF] font-bold">Convenient All-in-One Daily Tablet:</strong> Combines specialized brain-boosting nutrients with a complete multivitamin foundation into a single daily dose.</span></li></ul>',
            'pillars_item_5_desc_ar' => '<ul class="space-y-3 text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85"><li class="flex items-start gap-2.5"><span class="w-2 h-2 rounded-full bg-[#67B34A] mt-1.5 shrink-0"></span><span><strong class="text-[#031827] dark:text-[#F6F5EF] font-bold">دعم نتروبيك شامل:</strong> تركيبة غنية بـ 120 ملغ من الجنكة بيلوبا، وفوسفاتيديل سيرين، وفوسفاتيديل كولين للمساعدة في دعم الذاكرة والتركيز والوظائف الإدراكية.</span></li><li class="flex items-start gap-2.5"><span class="w-2 h-2 rounded-full bg-[#67B34A] mt-1.5 shrink-0"></span><span><strong class="text-[#031827] dark:text-[#F6F5EF] font-bold">طاقة خلوية فائقة الفعالية:</strong> مدعم بفيتامين B12 ومجموعة فيتامينات B بجرعات عالية للمساعدة في استقلاب الطاقة ومكافحة الإجهاد الذهني.</span></li><li class="flex items-start gap-2.5"><span class="w-2 h-2 rounded-full bg-[#67B34A] mt-1.5 shrink-0"></span><span><strong class="text-[#031827] dark:text-[#F6F5EF] font-bold">حماية خلوية بمضادات الأكسدة:</strong> يحتوي على Co-Q10، وL-جلوتاثيون، وفيتامين E للمساعدة في حماية خلايا الدماغ والأنسجة العصبية من الإجهاد التأكسدي.</span></li><li class="flex items-start gap-2.5"><span class="w-2 h-2 rounded-full bg-[#67B34A] mt-1.5 shrink-0"></span><span><strong class="text-[#031827] dark:text-[#F6F5EF] font-bold">معادن أساسية للدماغ والغدة الدرقية:</strong> يوفر جرعات رئيسية من الزنك، واليود، والحديد لدعم توازن النواقل العصبية، ووظيفة الغدة الدرقية، وأكسجة الدماغ الطبيعية.</span></li><li class="flex items-start gap-2.5"><span class="w-2 h-2 rounded-full bg-[#67B34A] mt-1.5 shrink-0"></span><span><strong class="text-[#031827] dark:text-[#F6F5EF] font-bold">قرص يومي واحد متكامل وسهل التناول:</strong> يجمع بين المغذيات المعززة للدماغ وقاعدة الفيتامينات المتعددة الكاملة في جرعة يومية واحدة مريحة.</span></li></ul>',

            // Item 6
            'pillars_item_6_num' => '06',
            'pillars_item_6_icon' => 'fa-solid fa-circle-question',
            'pillars_item_6_menu_en' => 'INFO & FAQS',
            'pillars_item_6_menu_ar' => 'معلومات وأسئلة شائعة',
            'pillars_item_6_title_en' => 'Important Information & FAQs',
            'pillars_item_6_title_ar' => 'معلومات هامة وأسئلة شائعة',
            'pillars_item_6_tag_en' => 'Usage, Safety & Clinical Guidance',
            'pillars_item_6_tag_ar' => 'السلامة وطريقة الاستخدام والإرشادات',
            'pillars_item_6_desc_en' => '<div class="space-y-3.5 text-xs"><div class="p-3 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-900 dark:text-amber-200"><div class="font-bold text-xs uppercase flex items-center gap-1.5 mb-1 text-amber-700 dark:text-amber-300"><i class="fa-solid fa-triangle-exclamation"></i> Warning & Usage Guidance</div><p class="text-[11px] leading-relaxed">Always read product directions before use. Do not exceed recommended intake. Contains Ginkgo Biloba; those taking anticoagulants (blood thinners) should consult their doctor before using. Contains iron (harmful to very young children in excess). Seek professional advice if pregnant, breast-feeding, under medical supervision, or suffering from allergies. Do not take if allergic to soya. Food supplements must not replace a varied diet and healthy lifestyle.</p></div><div class="space-y-2.5 text-[#031827]/85 dark:text-[#F6F5EF]/85"><div><strong class="text-[#031827] dark:text-[#F6F5EF] block text-xs">Why has Blue Mind been developed?</strong><p class="text-[11px] leading-relaxed text-[#031827]/75 dark:text-[#F6F5EF]/75 mt-0.5">Maintaining mental performance requires optimal functioning of brain cells. Blue Mind safeguards your dietary intake of essential nutrients such as iron, zinc, and iodine to contribute to normal cognitive function.</p></div><div><strong class="text-[#031827] dark:text-[#F6F5EF] block text-xs">When is Blue Mind recommended?</strong><p class="text-[11px] leading-relaxed text-[#031827]/75 dark:text-[#F6F5EF]/75 mt-0.5">Recommended for men and women of all ages, and ideal for exam periods or intensive professional qualification study.</p></div><div><strong class="text-[#031827] dark:text-[#F6F5EF] block text-xs">Can Blue Mind be taken simultaneously with other medications?</strong><p class="text-[11px] leading-relaxed text-[#031827]/75 dark:text-[#F6F5EF]/75 mt-0.5">Free from drugs and hormones. Contains Ginkgo Biloba (consult doctor/pharmacist if taking blood thinners).</p></div><div><strong class="text-[#031827] dark:text-[#F6F5EF] block text-xs">How and when should Blue Mind be used?</strong><p class="text-[11px] leading-relaxed text-[#031827]/75 dark:text-[#F6F5EF]/75 mt-0.5">1 tablet per day with or immediately after your main meal, with water or cold drink. Do not chew. Always take on a full stomach.</p></div><div><strong class="text-[#031827] dark:text-[#F6F5EF] block text-xs">Side effects & Duration of benefits:</strong><p class="text-[11px] leading-relaxed text-[#031827]/75 dark:text-[#F6F5EF]/75 mt-0.5">No known side effects when taken as directed. Benefits build over several weeks with regular intake; no maximum duration limit.</p></div></div></div>',
            'pillars_item_6_desc_ar' => '<div class="space-y-3.5 text-xs"><div class="p-3 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-900 dark:text-amber-200"><div class="font-bold text-xs uppercase flex items-center gap-1.5 mb-1 text-amber-700 dark:text-amber-300"><i class="fa-solid fa-triangle-exclamation"></i> تحذير وإرشادات هامة</div><p class="text-[11px] leading-relaxed">اقرأ دائمًا إرشادات المنتج قبل الاستخدام. لا تتجاوز الجرعة الموصى بها. يحتوي على الجنكة بيلوبا؛ يجب على من يتناولون مضادات التخثر (مسيلات الدم) استشارة الطبيب. يحتوي على الحديد. استشر طبيبك في حال الحمل، الإرضاع، أو وجود حساسية. يحتوي على الصويا. لا تغني المكملات عن نظام غذائي متوازن ونمط حياة صحي.</p></div><div class="space-y-2.5 text-[#031827]/85 dark:text-[#F6F5EF]/85"><div><strong class="text-[#031827] dark:text-[#F6F5EF] block text-xs">لماذا تم تطوير بلو مايند؟</strong><p class="text-[11px] leading-relaxed text-[#031827]/75 dark:text-[#F6F5EF]/75 mt-0.5">يتطلب الحفاظ على الأداء العقلي عمل خلايا الدماغ والشبكة العصبية المعقدة بكفاءة مثالية. يوفر بلو مايند تركيبة متكاملة لحماية مدخولك الغذائي وتزويدك بالحديد والزنك واليود للوظائف الإدراكية.</p></div><div><strong class="text-[#031827] dark:text-[#F6F5EF] block text-xs">متى يُنصح بتناول بلو مايند؟</strong><p class="text-[11px] leading-relaxed text-[#031827]/75 dark:text-[#F6F5EF]/75 mt-0.5">يوصى به للرجال والنساء من جميع الأعمار، ومثالي للطلاب خلال فترات الامتحانات والمهنيين الذين تتطلب أعمالهم تركيزاً عالياً.</p></div><div><strong class="text-[#031827] dark:text-[#F6F5EF] block text-xs">هل يمكن تناوله مع أدوية أخرى؟</strong><p class="text-[11px] leading-relaxed text-[#031827]/75 dark:text-[#F6F5EF]/75 mt-0.5">خالٍ من العقاقير والهرمونات. نظرًا لاحتوائه على الجنكة بيلوبا، يُنصح باستشارة الطبيب أو الصيدلي في حال تناول مسيلات الدم.</p></div><div><strong class="text-[#031827] dark:text-[#F6F5EF] block text-xs">كيف ومتى يُستخدم؟</strong><p class="text-[11px] leading-relaxed text-[#031827]/75 dark:text-[#F6F5EF]/75 mt-0.5">قرص واحد يوميًا مع الوجبة الرئيسية أو بعدها مباشرة مع الماء أو مشروب بارد دون مضغ وعلى معدة ممتلئة لزيادة الامتصاص وتجنب الغثيان.</p></div><div><strong class="text-[#031827] dark:text-[#F6F5EF] block text-xs">هل هناك آثار جانبية وكم تستغرق النتائج؟</strong><p class="text-[11px] leading-relaxed text-[#031827]/75 dark:text-[#F6F5EF]/75 mt-0.5">ليس له آثار جانبية معروفة عند تناوله وفق التعليمات. تظهر الفوائد تدريجيًا على مدار عدة أسابيع من الاستخدام المنتظم، ولا توجد مدة أقصى للاستخدام.</p></div></div></div>',
        ];
    }

    /**
     * Get list and metadata of all configurable landing page sections.
     *
     * @return array<string, array<string, string>>
     */
    public static function landingSections(): array
    {
        return [
            'hero_slider' => [
                'name_en' => 'Hero Lifestyle & Product Showcase Slider',
                'name_ar' => 'سلايدر الواجهة الرئيسي (عرض المنتجات وأسلوب الحياة)',
                'icon' => 'fa-solid fa-images',
                'desc_en' => 'Full-screen 5-slide dynamic carousel with floating flagship product cards and dual CTAs.',
                'desc_ar' => 'سلايدر تفاعلي بـ 5 شرائح مع بطاقات للمنتجات الرائدة وأزرار اتخاذ القرار.',
            ],
            'who_we_are' => [
                'name_en' => 'Who We Are (Brand Legacy & Pure Origin)',
                'name_ar' => 'من نحن (تراث العلامة والنقاء الطبيعي)',
                'icon' => 'fa-solid fa-landmark',
                'desc_en' => 'Brand background, extraction standards, and Mediterranean longevity philosophy.',
                'desc_ar' => 'نبذة عن العلامة، معايير الاستخلاص، وفلسفة طول العمر في المناطق الزرقاء.',
            ],
            'philosophy' => [
                'name_en' => 'The 6 Pillars of Longevity Philosophy',
                'name_ar' => 'ركائز طول العمر الست (فلسفة العافية)',
                'icon' => 'fa-solid fa-seedling',
                'desc_en' => 'Interactive cards detailing Plant-Slant nutrition, daily movement, downshift, and purpose.',
                'desc_ar' => 'بطاقات تفاعلية تشرح التغذية النباتية، الحركة الطبيعية، الهدوء، والهدف.',
            ],
            'new_arrivals' => [
                'name_en' => 'New Arrivals & Breakthrough Longevity Packs',
                'name_ar' => 'أحدث التركيبات وباقات تعزيز طول العمر',
                'icon' => 'fa-solid fa-sparkles',
                'desc_en' => 'Product carousel showing newly released micro-batch formulations and packs.',
                'desc_ar' => 'عرض دوار لأحدث التركيبات والتشغيلات المحدودة مع التقييمات والأسعار.',
            ],
            'featured_products' => [
                'name_en' => 'Featured Formulations Spotlight',
                'name_ar' => 'المستحضرات المميزة (الأكثر طلباً)',
                'icon' => 'fa-solid fa-flask-vial',
                'desc_en' => 'Highlight grid for best-selling mitochondrial and cellular health formulations.',
                'desc_ar' => 'شبكة استعراض لأكثر التركيبات طلباً مع التقييمات والشراء السريع.',
            ],
            'products_vertical' => [
                'name_en' => 'Structured Vertical Formulation Breakdown',
                'name_ar' => 'استعراض التركيبات الرأسي المفصل',
                'icon' => 'fa-solid fa-table-columns',
                'desc_en' => 'Vertical structured layout grouping clinical longevity bio-compounds.',
                'desc_ar' => 'تصميم رأسي منظم يعرض الفوائد والمكونات الصيدلانية القياسية.',
            ],
            'blue_mind_flagship' => [
                'name_en' => 'Blue Mind Flagship Nootropic Spotlight Hero',
                'name_ar' => 'مستحضر بلو مايند الرائد (التركيز والصفاء الذهني)',
                'icon' => 'fa-solid fa-brain',
                'desc_en' => 'High-impact product feature highlighting cognitive performance and BDNF support.',
                'desc_ar' => 'قسم تسليط الضوء على منشط الذهن الطبيعي والتركيز الخلوي الفائق.',
            ],
            'five_blue_zones' => [
                'name_en' => 'The 5 Global Blue Zones Interactive Ecosystems',
                'name_ar' => 'الأقاليم الخمسة المعمرة حول العالم',
                'icon' => 'fa-solid fa-earth-americas',
                'desc_en' => 'Interactive geographical exploration of Okinawa, Sardinia, Nicoya, Ikaria, and Loma Linda.',
                'desc_ar' => 'استكشاف تفاعلي لأقاليم أوكيناوا وسردينيا ونيكوييا وإيكاريا ولوما ليندا.',
            ],
            'bluemint_preps' => [
                'name_en' => 'Bluemint Specialized Preparations & Clinical Protocols',
                'name_ar' => 'مستحضرات بلو مينت وبروتوكولات الاستخدام',
                'icon' => 'fa-solid fa-mortar-pestle',
                'desc_en' => '4 specialized preparative protocols (Tea, Elixir, Drops, Micro-Capsules).',
                'desc_ar' => '4 مستحضرات متخصصة (المستخلص المركز، القطرات، الكبسولات الدقيقة، الشاي).',
            ],
            'our_science' => [
                'name_en' => 'Our Science & Clinical Dossier (Cellular Mechanisms)',
                'name_ar' => 'الأبحاث والنزاهة السريرية (الآليات الخلوية)',
                'icon' => 'fa-solid fa-microscope',
                'desc_en' => 'HPLC assay testing, cGMP compliance, mitochondrial autophagy, and molecular pathways.',
                'desc_ar' => 'فحوصات النقاوة المخبرية HPLC، مسارات الالتهام الذاتي، وطاقة الميتوكوندريا.',
            ],
            'journal_news' => [
                'name_en' => 'Blue Zone Longevity Journal & Insights',
                'name_ar' => 'مجلة وأبحاث طول العمر والعافية',
                'icon' => 'fa-solid fa-newspaper',
                'desc_en' => 'Latest articles, clinician insights, and lifestyle longevity recommendations.',
                'desc_ar' => 'أحدث المقالات الطبية، أبحاث التغذية، وإرشادات العافية من الخبراء.',
            ],
            'final_cta' => [
                'name_en' => 'Diagnostic Assessment & Consultation Final CTA',
                'name_ar' => 'التقييم الخلوي والدعوة للاستشارة الطبية',
                'icon' => 'fa-solid fa-stethoscope',
                'desc_en' => 'Final bottom banner encouraging longevity quiz completion and personalized protocol.',
                'desc_ar' => 'دعوة ختامية لإجراء التقييم الصحي والحصول على بروتوكول مخصص.',
            ],
        ];
    }
}
