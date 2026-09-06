<?php

namespace App\View\ViewModels;

class CategoryViewModel
{
    /**
     * Get all categories with subcategories.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function all(): array
    {
        return [
            [
                'id' => 1,
                'name_en' => 'Cognitive & Brain Health',
                'name_ar' => 'صحة الدماغ والإدراك',
                'slug' => 'cognitive-brain-health',
                'description_en' => 'Pharmaceutical-grade nootropics and brain adaptogens for memory, focus, and neuro-protection.',
                'description_ar' => 'منشطات ذهنية وأعشاب تكيّف صيدلانية لدعم الذاكرة والتركيز وحماية الخلايا العصبية.',
                'products_count' => 12,
                'status' => 'active',
                'sort_order' => 1,
                'icon' => 'brain',
                'subcategories' => [
                    ['id' => 11, 'name_en' => 'Nootropics', 'name_ar' => 'منشطات الذهن', 'slug' => 'nootropics'],
                    ['id' => 12, 'name_en' => 'Phospholipids', 'name_ar' => 'الفوسفوليبيدات', 'slug' => 'phospholipids'],
                    ['id' => 13, 'name_en' => 'Neuro-Protectors', 'name_ar' => 'حماة الأعصاب', 'slug' => 'neuro-protectors'],
                ],
            ],
            [
                'id' => 2,
                'name_en' => 'Cellular Longevity',
                'name_ar' => 'طول العمر وتجديد الخلايا',
                'slug' => 'cellular-longevity',
                'description_en' => 'Mitochondrial revitalizers, NAD+ precursors, and senolytic bio-compounds.',
                'description_ar' => 'مجددات حيوية للميتوكوندريا ومحفزات إنزيم NAD+ ومضادات شيخوخة الخلايا.',
                'products_count' => 8,
                'status' => 'active',
                'sort_order' => 2,
                'icon' => 'sparkles',
                'subcategories' => [
                    ['id' => 21, 'name_en' => 'NAD+ Boosters', 'name_ar' => 'معززات NAD+', 'slug' => 'nad-boosters'],
                    ['id' => 22, 'name_en' => 'Senolytics', 'name_ar' => 'سينوليتيكس', 'slug' => 'senolytics'],
                    ['id' => 23, 'name_en' => 'Sirtuin Activators', 'name_ar' => 'منشطات السيرتوين', 'slug' => 'sirtuin-activators'],
                ],
            ],
            [
                'id' => 3,
                'name_en' => 'Immunity & Resilience',
                'name_ar' => 'المناعة والمرونة الحيوية',
                'slug' => 'immunity-resilience',
                'description_en' => 'Wild mountain botanicals and chelates for deep immune defense and antioxidant shielding.',
                'description_ar' => 'أعشاب جبلية برية ومعادن مخلبية للدفاع المناعي العميق ومضادات الأكسدة.',
                'products_count' => 15,
                'status' => 'active',
                'sort_order' => 3,
                'icon' => 'shield-check',
                'subcategories' => [
                    ['id' => 31, 'name_en' => 'Polyphenols', 'name_ar' => 'البوليفينول', 'slug' => 'polyphenols'],
                    ['id' => 32, 'name_en' => 'Chelated Minerals', 'name_ar' => 'المعادن المخلبية', 'slug' => 'chelated-minerals'],
                    ['id' => 33, 'name_en' => 'Bio-Fermented Vitamins', 'name_ar' => 'فيتامينات مخمرة', 'slug' => 'bio-fermented-vitamins'],
                ],
            ],
            [
                'id' => 4,
                'name_en' => 'Metabolic Health',
                'name_ar' => 'الأيض والصحة الاستقلابية',
                'slug' => 'metabolic-health',
                'description_en' => 'Fasting mimetics and AMPK catalysts supporting cellular autophagy and glucose harmony.',
                'description_ar' => 'محاكيات الصيام ومنشطات إنزيم AMPK لدعم التهام الخلايا الذاتي وتوازن السكر.',
                'products_count' => 6,
                'status' => 'active',
                'sort_order' => 4,
                'icon' => 'flame',
                'subcategories' => [
                    ['id' => 41, 'name_en' => 'AMPK Catalysts', 'name_ar' => 'محفزات AMPK', 'slug' => 'ampk-catalysts'],
                    ['id' => 42, 'name_en' => 'Glucose Regulation', 'name_ar' => 'تنظيم السكر', 'slug' => 'glucose-regulation'],
                ],
            ],
            [
                'id' => 5,
                'name_en' => 'Sleep & Circadian Restoration',
                'name_ar' => 'النوم والاستشفاء الإيقاعي',
                'slug' => 'sleep-restoration',
                'description_en' => 'Non-habit forming botanical elixirs for slow-wave delta sleep and nocturnal cellular repair.',
                'description_ar' => 'مركبات نباتية طبيعية لتعزيز النوم العميق وإعادة ضبط الساعة البيولوجية.',
                'products_count' => 5,
                'status' => 'active',
                'sort_order' => 5,
                'icon' => 'moon',
                'subcategories' => [
                    ['id' => 51, 'name_en' => 'Circadian Optimization', 'name_ar' => 'تنظيم الإيقاع', 'slug' => 'circadian-optimization'],
                    ['id' => 52, 'name_en' => 'Nocturnal Neuro-Recovery', 'name_ar' => 'الاستشفاء الليلي', 'slug' => 'nocturnal-recovery'],
                ],
            ],
            [
                'id' => 6,
                'name_en' => 'Cardiovascular Longevity',
                'name_ar' => 'طول عمر القلب والأوعية',
                'slug' => 'cardiovascular-longevity',
                'description_en' => 'Endothelial elasticity modulators and natural nitric oxide bio-promoters.',
                'description_ar' => 'معززات مرونة الأوعية الدموية وإنتاج أكسيد النيتريك الطبيعي لصحة الشرايين.',
                'products_count' => 7,
                'status' => 'active',
                'sort_order' => 6,
                'icon' => 'heart',
                'subcategories' => [
                    ['id' => 61, 'name_en' => 'Nitric Oxide Promoters', 'name_ar' => 'محفزات أكسيد النيتريك', 'slug' => 'nitric-oxide'],
                    ['id' => 62, 'name_en' => 'Arterial Elasticity', 'name_ar' => 'مرونة الشرايين', 'slug' => 'arterial-elasticity'],
                ],
            ],
        ];
    }
}
