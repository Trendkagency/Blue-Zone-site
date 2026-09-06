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
            'default_currency' => 'USD',
            'timezone' => 'Asia/Riyadh',
            'contact_email' => 'care@bluezone.com',
            'support_email' => 'care@bluezone.com',
            'contact_phone' => '+966 800 123 4567',
            'support_phone' => '+966 800 123 4567',
            'enable_whatsapp' => true,
            'whatsapp_number' => '+966501234567',
            'whatsapp_default_message' => 'Hello BLUE ZONE, I would like clinical guidance on longevity formulations.',
            'whatsapp_position' => 'auto',

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
