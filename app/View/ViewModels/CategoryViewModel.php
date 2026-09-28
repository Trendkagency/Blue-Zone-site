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
                'name_en' => 'Joint & Cartilage Support',
                'name_ar' => 'صحة المفاصل والغضاريف',
                'slug' => 'joint-cartilage-support',
                'description_en' => 'Comprehensive joint, connective tissue, and cartilage support formulas with bio-botanicals.',
                'description_ar' => 'تركيبات متقدمة لدعم الغضاريف والمفاصل والأنسجة الضامة بالمركبات النباتية الحيوية.',
                'products_count' => 8,
                'status' => 'active',
                'sort_order' => 1,
                'icon' => 'bone',
                'subcategories' => [
                    ['id' => 11, 'name_en' => 'Cartilage Matrix', 'name_ar' => 'مصفوفة الغضاريف', 'slug' => 'cartilage-matrix'],
                    ['id' => 12, 'name_en' => 'Joint Lubrication', 'name_ar' => 'تزييت المفاصل', 'slug' => 'joint-lubrication'],
                    ['id' => 13, 'name_en' => 'Botanical Comfort', 'name_ar' => 'راحة نباتية', 'slug' => 'botanical-comfort'],
                ],
            ],
            [
                'id' => 2,
                'name_en' => 'Cognitive & Brain Health',
                'name_ar' => 'صحة الدماغ والإدراك',
                'slug' => 'cognitive-brain-health',
                'description_en' => 'Pharmaceutical-grade nootropics and brain adaptogens for memory, focus, and neuro-protection.',
                'description_ar' => 'منشطات ذهنية وأعشاب تكيّف صيدلانية لدعم الذاكرة والتركيز وحماية الخلايا العصبية.',
                'products_count' => 12,
                'status' => 'active',
                'sort_order' => 2,
                'icon' => 'brain',
                'subcategories' => [
                    ['id' => 21, 'name_en' => 'Nootropics', 'name_ar' => 'منشطات الذهن', 'slug' => 'nootropics'],
                    ['id' => 22, 'name_en' => 'Phospholipids', 'name_ar' => 'الفوسفوليبيدات', 'slug' => 'phospholipids'],
                    ['id' => 23, 'name_en' => 'Neuro-Protectors', 'name_ar' => 'حماة الأعصاب', 'slug' => 'neuro-protectors'],
                ],
            ],
            [
                'id' => 3,
                'name_en' => 'Nerve & Neurological Health',
                'name_ar' => 'صحة الأعصاب والجهاز العصبي',
                'slug' => 'nerve-neurological-health',
                'description_en' => 'Targeted neuro-protective and PEA formulations for peripheral nerve comfort and neurological resilience.',
                'description_ar' => 'تركيبات مستهدفة لحماية الأعصاب ومركب PEA لراحة الأعصاب الطرفية والصحة العصبية.',
                'products_count' => 6,
                'status' => 'active',
                'sort_order' => 3,
                'icon' => 'bolt',
                'subcategories' => [
                    ['id' => 31, 'name_en' => 'Nerve Comfort & PEA', 'name_ar' => 'راحة الأعصاب وPEA', 'slug' => 'nerve-comfort-pea'],
                    ['id' => 32, 'name_en' => 'B-Complex Synergy', 'name_ar' => 'تكامل فيتامينات B', 'slug' => 'b-complex-synergy'],
                ],
            ],
            [
                'id' => 4,
                'name_en' => 'Immunity & Bone Health',
                'name_ar' => 'المناعة وصحة العظام',
                'slug' => 'immunity-resilience',
                'description_en' => 'Complete cofactor systems with Vitamin D3, K2, Magnesium, Zinc, and Boron for bone mineralization and immune resilience.',
                'description_ar' => 'أنظمة عوامل مساعدة متكاملة بفيتامينات D3 وK2 والمغنيسيوم والزنك والبورون لتقوية العظام والمناعة.',
                'products_count' => 15,
                'status' => 'active',
                'sort_order' => 4,
                'icon' => 'shield-check',
                'subcategories' => [
                    ['id' => 41, 'name_en' => 'D3 & K2 Synergy', 'name_ar' => 'تكامل D3 وK2', 'slug' => 'd3-k2-synergy'],
                    ['id' => 42, 'name_en' => 'Chelated Minerals', 'name_ar' => 'المعادن المخلبية', 'slug' => 'chelated-minerals'],
                    ['id' => 43, 'name_en' => 'Immune Defense', 'name_ar' => 'الدفاع المناعي', 'slug' => 'immune-defense'],
                ],
            ],
            [
                'id' => 5,
                'name_en' => 'Cellular Longevity',
                'name_ar' => 'طول العمر وتجديد الخلايا',
                'slug' => 'cellular-longevity',
                'description_en' => 'Mitochondrial revitalizers, NAD+ precursors, and senolytic bio-compounds.',
                'description_ar' => 'مجددات حيوية للميتوكوندريا ومحفزات إنزيم NAD+ ومضادات شيخوخة الخلايا.',
                'products_count' => 8,
                'status' => 'active',
                'sort_order' => 5,
                'icon' => 'sparkles',
                'subcategories' => [
                    ['id' => 51, 'name_en' => 'NAD+ Boosters', 'name_ar' => 'معززات NAD+', 'slug' => 'nad-boosters'],
                    ['id' => 52, 'name_en' => 'Senolytics', 'name_ar' => 'سينوليتيكس', 'slug' => 'senolytics'],
                ],
            ],
            [
                'id' => 6,
                'name_en' => 'Sleep & Circadian Restoration',
                'name_ar' => 'النوم والاستشفاء الإيقاعي',
                'slug' => 'sleep-restoration',
                'description_en' => 'Non-habit forming botanical elixirs for slow-wave delta sleep and nocturnal cellular repair.',
                'description_ar' => 'مركبات نباتية طبيعية لتعزيز النوم العميق وإعادة ضبط الساعة البيولوجية.',
                'products_count' => 5,
                'status' => 'active',
                'sort_order' => 6,
                'icon' => 'moon',
                'subcategories' => [
                    ['id' => 61, 'name_en' => 'Circadian Optimization', 'name_ar' => 'تنظيم الإيقاع', 'slug' => 'circadian-optimization'],
                    ['id' => 62, 'name_en' => 'Nocturnal Neuro-Recovery', 'name_ar' => 'الاستشفاء الليلي', 'slug' => 'nocturnal-recovery'],
                ],
            ],
            [
                'id' => 7,
                'name_en' => 'Cardiovascular Longevity',
                'name_ar' => 'طول عمر القلب والأوعية',
                'slug' => 'cardiovascular-longevity',
                'description_en' => 'Endothelial elasticity modulators and natural nitric oxide bio-promoters.',
                'description_ar' => 'معززات مرونة الأوعية الدموية وإنتاج أكسيد النيتريك الطبيعي لصحة الشرايين.',
                'products_count' => 7,
                'status' => 'active',
                'sort_order' => 7,
                'icon' => 'heart',
                'subcategories' => [
                    ['id' => 71, 'name_en' => 'Nitric Oxide Promoters', 'name_ar' => 'محفزات أكسيد النيتريك', 'slug' => 'nitric-oxide'],
                    ['id' => 72, 'name_en' => 'Arterial Elasticity', 'name_ar' => 'مرونة الشرايين', 'slug' => 'arterial-elasticity'],
                ],
            ],
        ];
    }
}
