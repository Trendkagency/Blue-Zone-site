<?php

namespace App\View\ViewModels;

class ContentViewModel
{
    /**
     * Homepage banners and content sections.
     *
     * @return array<string, mixed>
     */
    public static function all(): array
    {
        return [
            'hero' => [
                'badge_en' => 'CENTENARIAN CELLULAR PROTOCOLS',
                'badge_ar' => 'بروتوكولات طول العمر الخلوية',
                'title_en' => 'Formulated by Nature. Validated by Science.',
                'title_ar' => 'صاغتها الطبيعة، وأثبتها العلم التجريبي.',
                'subtitle_en' => 'Bio-active cellular nutraceuticals inspired by the world’s five verified longevity Blue Zones: Okinawa, Sardinia, Nicoya, Ikaria, and Loma Linda.',
                'subtitle_ar' => 'مكملات غذائية خلوية نشطة حيوياً مستوحاة من أقاليم المناطق الزرقاء الخمس المعمرة حول العالم.',
            ],
            'zones' => [
                ['name_en' => 'Okinawa, Japan', 'name_ar' => 'أوكيناوا، اليابان', 'focus_en' => 'Ikigai & Antioxidant Marine Botanicals', 'focus_ar' => 'إيكيغاي ومضادات الأكسدة البحرية', 'lat' => '26.33', 'lng' => '127.80'],
                ['name_en' => 'Sardinia, Italy', 'name_ar' => 'سردينيا، إيطاليا', 'focus_en' => 'M26 Genetic Marker & Mountain Flavonoids', 'focus_ar' => 'جين M26 وفلافونويد الجبال', 'lat' => '40.12', 'lng' => '9.01'],
                ['name_en' => 'Nicoya, Costa Rica', 'name_ar' => 'نيكوييا، كوستاريكا', 'focus_en' => 'Calcium Rich Aquifers & Plan de Vida', 'focus_ar' => 'مياه غنية بالكالسيوم وهدف الحياة', 'lat' => '10.14', 'lng' => '-85.45'],
                ['name_en' => 'Ikaria, Greece', 'name_ar' => 'إيكاريا، اليونان', 'focus_en' => 'Wild Mountain Herbs & Circadian Rhythms', 'focus_ar' => 'أعشاب برية وإيقاع بيولوجي هادئ', 'lat' => '37.59', 'lng' => '26.15'],
                ['name_en' => 'Loma Linda, USA', 'name_ar' => 'لوما ليندا، كاليفورنيا', 'focus_en' => 'Plant Centric Diet & Communal Longevity', 'focus_ar' => 'تغذية نباتية وترابط اجتماعي متين', 'lat' => '34.04', 'lng' => '-117.26'],
            ],
            'faqs' => [
                [
                    'q_en' => 'What distinguishes BLUE ZONE™ from traditional multivitamins?',
                    'q_ar' => 'ما الذي يميز بلو زون™ عن الفيتامينات التجارية التقليدية؟',
                    'a_en' => 'BLUE ZONE formulations use clinically validated, branded bio-available raw compounds in therapeutic dosages rather than token dusting. We specifically target the hallmarks of biological senescence—mitochondrial health, NAD+ replenishment, and synaptic elasticity.',
                    'a_ar' => 'تعتمد تركيبات بلو زون على جزيئات حيوية معتمدة سريرياً بجرعات علاجية حقيقية غير مخففة. نستهدف جذور الشيخوخة البيولوجية مثل تجديد الميتوكوندريا ورفع إنزيم NAD+ وحماية مرونة المشابك العصبية.',
                ],
                [
                    'q_en' => 'Are your formulations third-party batch tested for purity?',
                    'q_ar' => 'هل تخضع جميع الدفعات لفحص مخبري مستقل لنقاء المكونات؟',
                    'a_en' => 'Every single batch undergoes triple independent ISO/IEC 17025 laboratory assays for heavy metals, microbial pathogens, residual solvents, and exact active constituent concentration.',
                    'a_ar' => 'تخضع كل تشغيلة إنتاجية لثلاثة اختبارات معملية مستقلة بمعايير ISO/IEC 17025 لفحص المعادن الثقيلة، النقاء الميكروبي، وخلوها من المذيبات وضمان تركيز المادة الفعالة بدقة.',
                ],
                [
                    'q_en' => 'Can BLUE ZONE formulations be stacked together?',
                    'q_ar' => 'هل يمكن استخدام أكثر من تركيبة معاً بأمان؟',
                    'a_en' => 'Yes. Our clinical team engineered our primary formulations (e.g., BLUE MIND and BLUE CELL) with zero nutrient conflicts or antagonistic co-factors.',
                    'a_ar' => 'نعم. صممت تركيباتنا الرئيسية (مثل بلو مايند وبلو سيل) من قبل فريقنا الطبي لتكون متناغمة حيوياً بالكامل دون أي تعارض في مسارات الامتصاص.',
                ],
            ],
        ];
    }
}
