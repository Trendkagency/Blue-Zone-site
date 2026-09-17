<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;

return new class extends Migration
{
    public function up(): void
    {
        $settings = [
            'pillars_eyebrow_en' => '',
            'pillars_eyebrow_ar' => '',
            'pillars_heading_prefix_en' => 'About',
            'pillars_heading_prefix_ar' => 'عن',
            'pillars_heading_highlight_en' => 'Blue Zone',
            'pillars_heading_highlight_ar' => 'بلو زون',
            'pillars_description_en' => 'Inspired by the concept of Blue Zones—regions where people live longer, healthier lives— Blue Zone Pharmaceuticals envisions promoting healthier lives and enhanced well-being through high-quality nutraceutical and cellular health formulations.',
            'pillars_description_ar' => 'انطلاقاً من مفهوم المناطق الزرقاء (Blue Zones) — تلك المناطق التي يعيش فيها الناس حياة أطول وأكثر صحة — تتطلع بلو زون للصناعات الدوائية إلى تعزيز أنماط الحياة الصحية وجودة العيش من خلال تركيبات متقدمة عالية الجودة للصحة الخلوية والمكملات الغذائية المتخصصة.',
            'pillars_center_title_en' => 'BLUE ZONE',
            'pillars_center_title_ar' => 'بلو زون',
            'pillars_center_subtitle_en' => 'PHARMA',
            'pillars_center_subtitle_ar' => 'الدوائية',

            // 1: Vision
            'pillars_item_1_num' => '01',
            'pillars_item_1_icon' => 'fa-solid fa-eye',
            'pillars_item_1_menu_en' => 'VISION',
            'pillars_item_1_menu_ar' => 'الرؤية',
            'pillars_item_1_title_en' => 'Vision',
            'pillars_item_1_title_ar' => 'رؤيتنا',
            'pillars_item_1_tag_en' => 'Evidence-Based Nutrition',
            'pillars_item_1_tag_ar' => 'حلول غذائية قائمة على الدليل العلمي',
            'pillars_item_1_desc_en' => '<p class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 leading-relaxed">To become one of the leading evidence-based dietary supplement companies in Egypt and the Middle East, improving people\'s quality of life through innovative, scientifically formulated nutritional solutions, manufactured with stringent quality standards, and communicated with clarity and transparency.</p>',
            'pillars_item_1_desc_ar' => '<p class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 leading-relaxed">أن نصبح إحدى الشركات الرائدة في مجال المكملات الغذائية القائمة على الدليل العلمي في مصر والشرق الأوسط، والارتقاء بجودة حياة الناس من خلال حلول غذائية مبتكرة ومصاغة علمياً، ومصنعة وفقاً لأعلى معايير الجودة الصارمة، مع التواصل بكل وضوح وشفافية.</p>',

            // 2: Mission
            'pillars_item_2_num' => '02',
            'pillars_item_2_icon' => 'fa-solid fa-bullseye',
            'pillars_item_2_menu_en' => 'MISSION',
            'pillars_item_2_menu_ar' => 'الرسالة',
            'pillars_item_2_title_en' => 'Mission',
            'pillars_item_2_title_ar' => 'رسالتنا',
            'pillars_item_2_tag_en' => 'Healthcare Value & Excellence',
            'pillars_item_2_tag_ar' => 'معايير استثنائية وقيمة مستدامة',
            'pillars_item_2_desc_en' => '<p class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 leading-relaxed">Blue Zone Pharmaceuticals is committed to developing premium-quality dietary supplements that combine scientific evidence, exceptional manufacturing standards, and innovative branding to support healthier lives while creating long-term value for healthcare professionals, patients, and business partners.</p>',
            'pillars_item_2_desc_ar' => '<p class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 leading-relaxed">تلتزم بلو زون للصناعات الدوائية بتطوير مكملات غذائية عالية الجودة تجمع بين الأدلة العلمية، ومعايير التصنيع الاستثنائية، والهوية المبتكرة لدعم حياة أكثر صحة، مع خلق قيمة مستدامة طويلة الأمد لمتخصصي الرعاية الصحية والمرضى وشركاء الأعمال.</p>',

            // 3: Core Values
            'pillars_item_3_num' => '03',
            'pillars_item_3_icon' => 'fa-solid fa-gem',
            'pillars_item_3_menu_en' => 'CORE VALUES',
            'pillars_item_3_menu_ar' => 'القيم الجوهرية',
            'pillars_item_3_title_en' => 'Core Values',
            'pillars_item_3_title_ar' => 'قيمنا الجوهرية',
            'pillars_item_3_tag_en' => 'Integrity, Quality & Innovation',
            'pillars_item_3_tag_ar' => 'النزاهة، الجودة والابتكار',
            'pillars_item_3_desc_en' => '<div class="space-y-2.5 text-xs sm:text-sm leading-relaxed"><div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-[#0A4F78]/10"><strong class="text-[#0A4F78] dark:text-[#2A8FC2] block font-bold mb-0.5"><i class="fa-solid fa-flask-vial mr-1.5 text-xs text-[#67B34A]"></i>Scientific Integrity</strong><p class="text-[#031827]/80 dark:text-[#F6F5EF]/80 text-[11px] sm:text-xs">We believe in science-led decisions, responsible formulation, and clear communication grounded in reliable information.</p></div><div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-[#0A4F78]/10"><strong class="text-[#0A4F78] dark:text-[#2A8FC2] block font-bold mb-0.5"><i class="fa-solid fa-shield-halved mr-1.5 text-xs text-[#67B34A]"></i>Quality Without Compromise</strong><p class="text-[#031827]/80 dark:text-[#F6F5EF]/80 text-[11px] sm:text-xs">We maintain high standards throughout product selection and development, with careful attention to quality, safety, and consistency.</p></div><div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-[#0A4F78]/10"><strong class="text-[#0A4F78] dark:text-[#2A8FC2] block font-bold mb-0.5"><i class="fa-solid fa-lightbulb mr-1.5 text-xs text-[#67B34A]"></i>Innovation</strong><p class="text-[#031827]/80 dark:text-[#F6F5EF]/80 text-[11px] sm:text-xs">We continuously explore better ideas, ingredients, and approaches to create thoughtful products that respond to evolving health and wellness needs.</p></div><div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-[#0A4F78]/10"><strong class="text-[#0A4F78] dark:text-[#2A8FC2] block font-bold mb-0.5"><i class="fa-solid fa-handshake mr-1.5 text-xs text-[#67B34A]"></i>Trust & Transparency</strong><p class="text-[#031827]/80 dark:text-[#F6F5EF]/80 text-[11px] sm:text-xs">We build lasting relationships through consistency, responsibility, and straightforward information about our products, ingredients, and standards.</p></div><div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-[#0A4F78]/10"><strong class="text-[#0A4F78] dark:text-[#2A8FC2] block font-bold mb-0.5"><i class="fa-solid fa-heart-pulse mr-1.5 text-xs text-[#67B34A]"></i>Patient First & Continuous Improvement</strong><p class="text-[#031827]/80 dark:text-[#F6F5EF]/80 text-[11px] sm:text-xs">We keep the needs, well-being, and experience of the people we serve at the heart of what we do, always learning and raising our standards across everything.</p></div></div>',
            'pillars_item_3_desc_ar' => '<div class="space-y-2.5 text-xs sm:text-sm leading-relaxed"><div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-[#0A4F78]/10"><strong class="text-[#0A4F78] dark:text-[#2A8FC2] block font-bold mb-0.5"><i class="fa-solid fa-flask-vial ml-1.5 text-xs text-[#67B34A]"></i>النزاهة العلمية</strong><p class="text-[#031827]/80 dark:text-[#F6F5EF]/80 text-[11px] sm:text-xs">نؤمن بالقرارات المبنية على العلم، والصياغة المسؤولة، والتواصل الواضح القائم على معلومات موثوقة.</p></div><div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-[#0A4F78]/10"><strong class="text-[#0A4F78] dark:text-[#2A8FC2] block font-bold mb-0.5"><i class="fa-solid fa-shield-halved ml-1.5 text-xs text-[#67B34A]"></i>الجودة دون مساومة</strong><p class="text-[#031827]/80 dark:text-[#F6F5EF]/80 text-[11px] sm:text-xs">نحافظ على معايير رفيعة طوال مراحل اختيار وتطوير المنتجات، مع عناية دقيقة بالجودة والسلامة والاتساق.</p></div><div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-[#0A4F78]/10"><strong class="text-[#0A4F78] dark:text-[#2A8FC2] block font-bold mb-0.5"><i class="fa-solid fa-lightbulb ml-1.5 text-xs text-[#67B34A]"></i>الابتكار</strong><p class="text-[#031827]/80 dark:text-[#F6F5EF]/80 text-[11px] sm:text-xs">نستكشف باستمرار أفكاراً ومكونات وحلولاً أفضل لابتكار منتجات مدروسة تلبي الاحتياجات الصحية المتطورة.</p></div><div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-[#0A4F78]/10"><strong class="text-[#0A4F78] dark:text-[#2A8FC2] block font-bold mb-0.5"><i class="fa-solid fa-handshake ml-1.5 text-xs text-[#67B34A]"></i>الثقة والشفافية</strong><p class="text-[#031827]/80 dark:text-[#F6F5EF]/80 text-[11px] sm:text-xs">نبني علاقات دائمة قائمة على الاتساق والمسؤولية والشفافية الصادقة حول منتجاتنا ومكوناتنا ومعاييرنا.</p></div><div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-[#0A4F78]/10"><strong class="text-[#0A4F78] dark:text-[#2A8FC2] block font-bold mb-0.5"><i class="fa-solid fa-heart-pulse ml-1.5 text-xs text-[#67B34A]"></i>المريض أولاً والتحسين المستمر</strong><p class="text-[#031827]/80 dark:text-[#F6F5EF]/80 text-[11px] sm:text-xs">نضع احتياجات وعافية وتجربة مستخدمي منتجاتنا في صميم كل ما نقوم به، مؤمنين بأن هناك دائماً فرصة للتعلم ورفع المعايير في كل ما نقدمه.</p></div></div>',

            // 4: Our Commitment
            'pillars_item_4_num' => '04',
            'pillars_item_4_icon' => 'fa-solid fa-handshake-angle',
            'pillars_item_4_menu_en' => 'OUR COMMITMENT',
            'pillars_item_4_menu_ar' => 'التزامنا',
            'pillars_item_4_title_en' => 'Our Commitment',
            'pillars_item_4_title_ar' => 'التزامنا',
            'pillars_item_4_tag_en' => 'Trust & Responsible Communication',
            'pillars_item_4_tag_ar' => 'أمانة وتواصل مسؤول',
            'pillars_item_4_desc_en' => '<p class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 leading-relaxed mb-3">At Blue Zone Pharmaceuticals, we are committed to developing reliable nutraceutical products based on scientific evidence and stringent quality standards.</p><p class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 leading-relaxed">We prioritize transparency and responsible communication, aiming to provide clear product information and support healthier lives with integrity and care.</p>',
            'pillars_item_4_desc_ar' => '<p class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 leading-relaxed mb-3">في بلو زون للصناعات الدوائية، نلتزم بتطوير منتجات غذائية علاجية موثوقة تستند إلى الأدلة العلمية ومعايير الجودة الصارمة.</p><p class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 leading-relaxed">نضع الشفافية والتواصل المسؤول على رأس أولوياتنا، بهدف تقديم معلومات واضحة وموثوقة عن المنتجات ودعم حياة أكثر صحة بنزاهة ورعاية حقيقية.</p>',

            // 5: Quality & Safety
            'pillars_item_5_num' => '05',
            'pillars_item_5_icon' => 'fa-solid fa-shield-halved',
            'pillars_item_5_menu_en' => 'QUALITY & SAFETY',
            'pillars_item_5_menu_ar' => 'الجودة والسلامة',
            'pillars_item_5_title_en' => 'Quality and Safety',
            'pillars_item_5_title_ar' => 'الجودة والسلامة',
            'pillars_item_5_tag_en' => 'Stringent Quality & Thoughtful Formulation',
            'pillars_item_5_tag_ar' => 'معايير صارمة وصياغة مدروسة',
            'pillars_item_5_desc_en' => '<p class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 leading-relaxed mb-3">We follow strict quality standards to ensure reliable products.</p><p class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 leading-relaxed">Every ingredient in a BlueZone product is carefully selected and evaluated for quality, safety, and suitability, reflecting our commitment to thoughtful formulation and creating products designed to add meaningful value to everyday health and well-being.</p>',
            'pillars_item_5_desc_ar' => '<p class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 leading-relaxed mb-3">نتبع معايير جودة صارمة لضمان منتجات موثوقة تدعم صحة الإنسان.</p><p class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 leading-relaxed">يتم اختيار كل مكون في منتجات بلو زون وتقييمه بعناية فائقة لضمان الجودة والسلامة والملاءمة، مما يعكس التزامنا بالصياغة المدروسة وابتكار منتجات تهدف إلى إضفاء قيمة ملموسة على الصحة اليومية والعافية العامة.</p>',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'group' => 'pillars', 'type' => 'string']
            );
        }

        Setting::clearCache();
        Cache::flush();
    }

    public function down(): void
    {
        // Keep settings intact
    }
};
