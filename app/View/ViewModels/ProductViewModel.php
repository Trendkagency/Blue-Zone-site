<?php

namespace App\View\ViewModels;

class ProductViewModel
{
    /**
     * Get all mock products for catalog listing.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function all(): array
    {
        return [

            /* ---------------------------------------------------------- */
            /* 1. BLUE JOINT                                              */
            /* ---------------------------------------------------------- */
            [
                'id' => 1,
                'slug' => 'blue-joint',
                'sku' => 'BZ-JNT-001',
                'barcode' => '628100091001',
                'name_en' => 'Blue Joint',
                'name_ar' => 'بلو جوينت',
                'tagline_en' => 'Comprehensive Joint & Cartilage Support',
                'tagline_ar' => 'دعم شامل لصحة المفاصل ومرونة الغضاريف',
                'category_en' => 'Joint & Cartilage Support',
                'category_ar' => 'صحة المفاصل والغضاريف',
                'subcategory_en' => 'Cartilage Matrix & Mobility',
                'subcategory_ar' => 'مصفوفة الغضاريف والمرونة',
                'brand' => 'Blue Zone Bioceuticals',
                'price' => 65.00,
                'sale_price' => 55.00,
                'is_featured' => true,
                'is_best_seller' => true,
                'is_new' => false,
                'status' => 'active',
                'rating' => 4.9,
                'reviews_count' => 128,
                'image' => '/assets/products/blue-joint.jpg',
                'images' => [
                    '/assets/products/blue-joint.jpg',
                    '/assets/products/blue-mind.jpg',
                ],
                'stock_online' => 150,
                'stock_offline' => 45,
                'low_stock_threshold' => 15,
                'short_description_en' => 'Blue Joint is a comprehensive joint-support formula combining glucosamine, chondroitin and MSM with complementary ingredients including Boswellia, hyaluronic acid and hydrolysed collagen. The formula is designed to provide nutritional support for normal cartilage and connective-tissue function and to help maintain joint comfort and mobility.',
                'short_description_ar' => 'بلو جوينت هو تركيبة شاملة لدعم المفاصل تجمع بين الجلوكوزامين والكوندرويتين وMSM مع مكونات تكميلية تشمل اللبان المنشاري (البوزويليا)، وحمض الهيالورونيك، والكولاجين المتحلل. تم تصميم التركيبة لتوفير الدعم الغذائي لوظائف الغضاريف والأنسجة الضامة الطبيعية والمساعدة في الحفاظ على راحة المفاصل وحركتها.',
                'description_en' => "Glucosamine – 1,000 mg: structural support for cartilage and joint tissues.\nChondroitin – 500 mg: supports the natural cartilage matrix.\nMSM – 450 mg: provides a source of organic sulfur for connective-tissue support.\nBoswellia – 330 mg: botanical support for joint comfort and a healthy inflammatory response.\nHydrolysed Collagen – 80 mg: provides collagen-derived peptides and amino acids.\nHyaluronic Acid – 30 mg: supports joint lubrication and mobility.\nZinc – 8 mg: contributes to normal connective-tissue metabolism and protection from oxidative stress.\nSelenium – 30 µg: contributes to protection of cells from oxidative stress.",
                'description_ar' => "جلوكوزامين – 1000 ملغ: دعم هيكلي للغضاريف وأنسجة المفاصل.\nكوندرويتين – 500 ملغ: يدعم مصفوفة الغضاريف الطبيعية.\nMSM – 450 ملغ: يوفر مصدراً للكبريت العضوي لدعم الأنسجة الضامة.\nاللبان المنشاري (بوزويليا) – 330 ملغ: دعم نباتي لراحة المفاصل والاستجابة الالتهابية الصحية.\nكولاجين متحلل – 80 ملغ: يوفر ببتيدات وأحماض أمينية مشتقة من الكولاجين.\nحمض الهيالورونيك – 30 ملغ: يدعم تزييت المفاصل ومرونة حركتها.\nزنك – 8 ملغ: يساهم في التمثيل الغذائي الطبيعي للأنسجة الضامة وحمايتها من الإجهاد التأكسدي.\nسيلينيوم – 30 ميكروغرام: يساهم في حماية الخلايا من الإجهاد التأكسدي.",
                'science_en' => "Blue Joint addresses joint cartilage preservation, synovial viscosity, and tissue integrity through clinically targeted biochemical pathways.\n\nGlucosamine (1,000 mg) and Chondroitin (500 mg) act as core glycosaminoglycan precursors that stimulate chondrocyte synthesis of articular cartilage proteoglycans. MSM (450 mg) supplies bioactive organic sulfur required for strengthening disulfide bonds in connective collagen matrices.\n\nBoswellia serrata (330 mg) provides standardized AKBA boswellic acids to naturally modulate 5-lipoxygenase (5-LOX) and soothe stiffness without gastrointestinal side effects. Hydrolysed Collagen (80 mg) and Hyaluronic Acid (30 mg) restore synovial fluid viscoelasticity, reducing friction during movement, while Zinc (8 mg) and Selenium (30 µg) provide essential antioxidant shielding against chondrocyte degradation.",
                'science_ar' => "يستهدف بلو جوينت الحفاظ على غضاريف المفاصل ولزوجة السائل الزليلي وسلامة الأنسجة عبر مسارات كيميائية حيوية مثبتة إكلينيكياً.\n\nيشكل الجلوكوزامين (1000 ملغ) والكوندرويتين (500 ملغ) اللبنات الأساسية لتحفيز خلايا الغضاريف على بناء البروتيوغليكان. ويوفر مركب MSM (450 ملغ) الكبريت العضوي الضروري لتقوية روابط الكولاجين في الأنسجة الضامة.\n\nيوفر مستخلص اللبان المنشاري (البوزويليا 330 ملغ) أحماض البوزويليك لتثبيط إنزيم 5-LOX وتسكين تيبس المفاصل بلطف. ويعمل الكولاجين المتحلل (80 ملغ) مع حمض الهيالورونيك (30 ملغ) على استعادة مرونة السائل الزليلي وتقليل الاحتكاك، بينما يحمي الزنك (8 ملغ) والسيلينيوم (30 ميكروغرام) خلايا المفاصل من الإجهاد التأكسدي.",
                'benefits_en' => [
                    'Complete 3-in-1 Joint & Cartilage Support: Combines Glucosamine (1000 mg), Chondroitin (500mg), and MSM (450 mg) to help rebuild cartilage, maintain joint flexibility, and cushion everyday movement.',
                    'Natural Anti-Inflammatory Comfort: Packed with 330mg of Boswellia serrata to help soothe joint stiffness and support mobility without harsh side effects.',
                    'Hydration & Structural Strength: Features Collagen (80 mg) and Hyaluronic Acid (30 mg) to support healthy joint fluid, connective tissue elasticity, and smooth lubrication.',
                    'Essential Cellular Protection: Enhanced with Zinc (8mg) and Selenium (30mcg) to protect joint cells from oxidative stress and support normal immune function.',
                    'Convenient Daily Serving: High-potency formulation designed to deliver full daily benefits in just 2 tablets.',
                ],
                'benefits_ar' => [
                    'دعم ثلاثي متكامل للمفاصل والغضاريف: يجمع بين الجلوكوزامين (1000 ملغ)، الكوندرويتين (500 ملغ)، وMSM (450 ملغ) لإعادة بناء الغضاريف ومرونة المفاصل وتوسيد الحركة اليومية.',
                    'راحة طبيعية ومضادة للالتهاب: معزز بـ 330 ملغ من اللبان المنشاري (البوزويليا) لتسكين تيبس المفاصل ودعم الحركة دون آثار جانبية قاسية.',
                    'ترطيب وقوة هيكلية: يحتوي على الكولاجين (80 ملغ) وحمض الهيالورونيك (30 ملغ) لدعم السائل الزليلي ومرونة الأنسجة والتزييت السلس للمفاصل.',
                    'حماية خلوية أساسية: مدعم بالزنك (8 ملغ) والسيلينيوم (30 ميكروغرام) لحماية خلايا المفاصل من الإجهاد التأكسدي ودعم المناعة الطبيعية.',
                    'جرعة يومية مريحة: تركيبة عالية الفعالية توفر فوائد يومية كاملة في قرصين فقط.',
                ],
                'usage_en' => 'Take 2 tablets daily with a meal and a full glass of water, or as directed by your healthcare professional. Do not exceed the recommended daily dose.',
                'usage_ar' => 'تناول قرصين يومياً مع وجبة وكوب كامل من الماء، أو حسب توجيهات أخصائي الرعاية الصحية. لا تتجاوز الجرعة اليومية الموصى بها.',
                'dosage_en' => '2 Tablets Daily',
                'dosage_ar' => 'قرصان يومياً',
                'package_size_en' => '60 Tablets (Serving: 2 Tablets Daily)',
                'package_size_ar' => '60 قرصاً (الجرعة: قرصان يومياً)',
                'gender' => 'Unisex',
                'age_group' => '18+',
                'health_goal_en' => 'Joint Comfort, Cartilage Health, Mobility',
                'health_goal_ar' => 'راحة المفاصل، صحة الغضاريف، ومرونة الحركة',
                'professional_info' => [
                    'clinical_mechanism' => 'Synergistic chondrocyte stimulation via Glucosamine-Chondroitin pathway coupled with 5-Lipoxygenase inhibition by Boswellia AKBA and synovial viscoelastic replenishment by Sodium Hyaluronate.',
                    'formula_details' => 'Glucosamine HCl 1000mg, Chondroitin Sulfate 500mg, Methylsulfonylmethane (MSM) 450mg, Boswellia serrata Extract 330mg, Type II Hydrolysed Collagen 80mg, Hyaluronic Acid 30mg, Zinc Bisglycinate 8mg (80% NRV), L-Selenomethionine 30µg (54.54% NRV).',
                    'contraindications' => 'Shellfish allergy precautions (if glucosamine sourced marine). Consult physician if taking anticoagulants or during pregnancy/breastfeeding.',
                    'warnings' => 'Dietary supplements should not be used as a substitute for a varied and balanced diet and a healthy lifestyle. Do not exceed recommended dose. Keep out of reach of young children. We do not recommend taking other supplements containing zinc at the same time. Store tightly closed in a cool, dry place.',
                ],
                'ingredients' => [
                    ['name_en' => 'Zinc', 'name_ar' => 'زنك', 'dose' => '8 mg', 'amount' => '8 mg', 'nrv' => '80%'],
                    ['name_en' => 'Selenium', 'name_ar' => 'سيلينيوم', 'dose' => '30 µg', 'amount' => '30 µg', 'nrv' => '54.54%'],
                    ['name_en' => 'Glucosamine', 'name_ar' => 'جلوكوزامين', 'dose' => '1000 mg', 'amount' => '1000 mg', 'nrv' => ''],
                    ['name_en' => 'MSM', 'name_ar' => 'إم إس إم (كبريت عضوي)', 'dose' => '450 mg', 'amount' => '450 mg', 'nrv' => ''],
                    ['name_en' => 'Chondroitin', 'name_ar' => 'كوندرويتين', 'dose' => '500 mg', 'amount' => '500 mg', 'nrv' => ''],
                    ['name_en' => 'Boswellia', 'name_ar' => 'اللبان المنشاري (بوزويليا)', 'dose' => '330 mg', 'amount' => '330 mg', 'nrv' => ''],
                    ['name_en' => 'Hydrolysed collagen', 'name_ar' => 'كولاجين متحلل', 'dose' => '80 mg', 'amount' => '80 mg', 'nrv' => ''],
                    ['name_en' => 'Hyaluronic acid', 'name_ar' => 'حمض الهيالورونيك', 'dose' => '30 mg', 'amount' => '30 mg', 'nrv' => ''],
                ],
                'variants' => [
                    [
                        'id' => 101,
                        'name_en' => 'Standard 30-Day Bottle (60 Tablets)',
                        'name_ar' => 'العبوة القياسية 30 يوماً (60 قرصاً)',
                        'sku' => 'BZ-JNT-60T',
                        'price' => 65.00,
                        'stock_online' => 105,
                        'stock_offline' => 30,
                    ],
                ],
            ],

            /* ---------------------------------------------------------- */
            /* 2. BLUE MIND                                               */
            /* ---------------------------------------------------------- */
            [
                'id' => 2,
                'slug' => 'blue-mind',
                'sku' => 'BZ-MND-001',
                'barcode' => '628100091002',
                'name_en' => 'Blue Mind',
                'name_ar' => 'بلو مايند',
                'tagline_en' => 'Feed your focus. Support your mind.',
                'tagline_ar' => 'غذِّ تركيزك. ادعم قدراتك الذهنية.',
                'category_en' => 'Cognitive & Brain Health',
                'category_ar' => 'صحة الدماغ والإدراك',
                'subcategory_en' => 'Nootropics & Cognitive Function',
                'subcategory_ar' => 'منشطات الذهن والأداء الإدراكي',
                'brand' => 'Blue Zone Bioceuticals',
                'price' => 68.00,
                'sale_price' => 58.00,
                'is_featured' => true,
                'is_best_seller' => true,
                'is_new' => false,
                'status' => 'active',
                'rating' => 4.9,
                'reviews_count' => 142,
                'image' => '/assets/products/blue-mind.jpg',
                'images' => [
                    '/assets/products/blue-mind.jpg',
                    '/assets/products/blue-joint.jpg',
                ],
                'stock_online' => 124,
                'stock_offline' => 38,
                'low_stock_threshold' => 15,
                'short_description_en' => 'Blue mind is a comprehensive nutritional support formulated to help maintain normal cognitive function and mental performance. It combines essential vitamins and minerals with Ginkgo biloba, amino acids together with other key nutrients including iron, zinc, iodine, and pantothenic acid to provide nutritional support for the brain and body.',
                'short_description_ar' => 'بلو مايند هو دعم غذائي شامل مصمم للمساعدة في الحفاظ على الوظائف الإدراكية الطبيعية والأداء الذهني. يجمع بين الفيتامينات والمعادن الأساسية مع الجنكة بيلوبا، والأحماض الأمينية جنباً إلى جنب مع المغذيات الرئيسية مثل الحديد والزنك واليود وحمض البانتوثنيك لدعم الدماغ والجسم.',
                'description_en' => "Our advanced formula contains:\n• Ginkgo biloba which helps the maintenance of memory with age, Plus specialist nutrients; L-Arginine, Phosphatidylserine and Coenzyme Q10.\n• Iron, zinc and iodine contribute to normal cognitive function plus pantothenic acid which contributes to normal mental performance.\n• Vitamin B6, riboflavin (vit. B2), niacin (vit. B3) and vitamin C contribute to the normal functioning of the nervous system.\n• Vitamins B12, B6, thiamin (vit. B1), folic acid, vitamin C and magnesium contribute to normal psychological function.\n• An optimum 25µg (1000IU) of vitamin D, which contributes to normal immune system function. The vitamin D is present in the D3 form (cholecalciferol), which is the preferred form made naturally by the skin upon exposure to sunlight.\n• Vitamin C, iron and copper contribute to normal energy release.\n• Selenium and zinc contribute to normal function of the immune system.\n• Vitamin B6 & B12 which contribute to normal red blood cell formation.",
                'description_ar' => "تحتوي تركيبتنا المتقدمة على:\n• الجنكة بيلوبا التي تساعد في الحفاظ على الذاكرة مع تقدم العمر، بالإضافة إلى عناصر متخصصة: إل-أرجينين، فوسفاتيديل سيرين، والإنزيم المساعد Co-Q10.\n• يساهم الحديد والزنك واليود في الوظائف الإدراكية الطبيعية مع حمض البانتوثنيك الذي يدعم الأداء الذهني.\n• تساهم فيتامينات B6 وB2 وB3 وفيتامين C في الأداء الطبيعي للجهاز العصبي.\n• تساهم فيتامينات B12 وB6 وB1 وحمض الفوليك وفيتامين C والمغنيسيوم في الوظيفة النفسية الطبيعية.\n• جرعة مثالية 25 ميكروغرام (1000 وحدة دولية) من فيتامين D3 لدعم الجهاز المناعي.\n• يساهم فيتامين C والحديد والنحاس في إنتاج الطاقة، والزنك والسيلينيوم في دعم المناعة، وB6 وB12 في تكوين خلايا الدم الحمراء.",
                'science_en' => "BLUE MIND is formulated to support acetylcholine neurotransmission, prefrontal cortical blood perfusion, and neuronal mitochondrial antioxidant defense.\n\nGinkgo Biloba (equivalent to 120 mg extract) promotes cerebral arteriolar dilation and microcirculatory oxygen delivery, supporting working memory recall and mental agility. Phosphatidylserine and Phosphatidylcholine replenish essential neural membrane phospholipids.\n\nCo-Q10, L-Glutathione, and L-Glutamine fuel neuronal ATP synthesis, while a complete spectrum of B-vitamins (B1, B2, B3, B5, B6, B12, Folic Acid) and minerals (Iron, Zinc, Iodine, Magnesium) supports neurotransmitter production and reduces mental fatigue.",
                'science_ar' => "تم تطوير بلو مايند لدعم النواقل العصبية (الأستيل كولين)، والتروية الدموية للقشرة الجبهية في الدماغ، وحماية الميتوكوندريا العصبية.\n\nيعمل مستخلص الجنكة بيلوبا (120 ملغ) على توسيع الشعيرات الدموية الدقيقة بالدماغ وزيادة تدفق الأكسجين لدعم الذاكرة والتركيز. ويعيد الفوسفاتيديل سيرين والفوسفاتيديل كولين بناء الأغشية الخلوية العصبية.\n\nيعمل Co-Q10 وإل-جلوتاثيون وإل-جلوتامين على تنشيط طاقة الميتوكوندريا، بينما يدعم مجمع فيتامينات B الكامل والمعادن المخلبية (الحديد، الزنك، اليود، المغنيسيوم) تخليق النواقل العصبية وتقليل الإرهاق الذهني.",
                'benefits_en' => [
                    'Blue mind is recommended for both men and women of all ages and is also ideal for those studying for exams or professional qualifications, especially during examination periods.',
                    'Blue mind has no known side-effects when taken as directed. Do not exceed the recommended tablet intake.',
                    'Cumulative Beneficial Effects: In most cases, the beneficial effects of specialist nutrients in Blue mind build over several weeks of regular use.',
                    'Suitable for vegetarians, cruelty-free (not tested on animals), with zero artificial colours.',
                ],
                'benefits_ar' => [
                    'يوصى بـ بلو مايند للرجال والنساء من جميع الأعمار، ومثالي للدارسين والمهنيين خلال فترات الامتحانات والعمل الذهني المكثف.',
                    'ليس له آثار جانبية معروفة عند تناوله وفقاً للتعليمات. لا يسبب الإدمان أو الخمول بعد انتهاء المفعول.',
                    'فوائد تراكمية ممتدة: تبنى الفوائد الداعمة للأعصاب والذاكرة على مدار عدة أسابيع من الاستخدام المنتظم دون سقف زمني محدد.',
                    'مناسب للنباتيين، خالٍ من الألوان الاصطناعية، وغير مجرب على الحيوانات (Cruelty Free).',
                ],
                'usage_en' => 'ONE TABLET PER DAY WITH YOUR MAIN MEAL. Swallow with water or a cold drink. Not to be chewed. Do not exceed the recommended intake. To be taken on a full stomach. There is no need to take an additional multivitamin.',
                'usage_ar' => 'قرص واحد يومياً مع وجبتك الرئيسية. يبتلع بالماء أو بمشروب بارد دون مضغ. لا تتجاوز الجرعة الموصى بها. يؤخذ على معدة ممتلئة. لا داعي لتناول فيتامينات إضافية.',
                'dosage_en' => '1 Tablet Daily',
                'dosage_ar' => 'قرص واحد يومياً',
                'package_size_en' => '30 Tablets (Serving: 1 Tablet Daily)',
                'package_size_ar' => '30 قرصاً (الجرعة: قرص واحد يومياً)',
                'gender' => 'Unisex',
                'age_group' => '18+',
                'health_goal_en' => 'Cognition, Mental Clarity, Memory, Focus',
                'health_goal_ar' => 'التركيز، الصفاء الذهني، والذاكرة والأداء الإدراكي',
                'professional_info' => [
                    'clinical_mechanism' => 'Choline donor phosphatidylcholine combined with phospholipid-bound serine promotes synaptic vesicle docking. Ginkgo flavonoids enhance cerebral microcirculation and oxygen perfusion rate.',
                    'formula_details' => 'Standardized Ginkgo Biloba Extract (equiv. 120mg), Phosphatidylserine 10mg, Phosphatidylcholine 10mg, Kaneka Co-Q10 10mg, L-Arginine 40mg, L-Glutathione 5mg, High-Potency Vitamin B-Complex & Chelated Minerals.',
                    'contraindications' => 'Those taking anticoagulants (blood thinners) should consult their doctor before using. Contains soya derivatives. Seek advice if pregnant/nursing or suffering from epilepsy.',
                    'warnings' => 'Always read the product directions before use. Do not exceed the recommended intake. Contains iron, which if taken in excess by very young children may be harmful. Food supplements must not replace a varied and balanced diet.',
                ],
                'ingredients' => [
                    ['name_en' => 'Ginkgo Biloba Extract equiv. to', 'name_ar' => 'مستخلص الجنكة بيلوبا',     'dose' => '120 mg',     'amount' => '120 mg',     'nrv' => ''],
                    ['name_en' => 'L-Arginine',                      'name_ar' => 'إل-أرجينين',               'dose' => '40 mg',      'amount' => '40 mg',      'nrv' => ''],
                    ['name_en' => 'L-Glutamine',                     'name_ar' => 'إل-جلوتامين',              'dose' => '10 mg',      'amount' => '10 mg',      'nrv' => ''],
                    ['name_en' => 'L-Glutathione',                    'name_ar' => 'إل-جلوتاثيون',             'dose' => '5 mg',       'amount' => '5 mg',       'nrv' => ''],
                    ['name_en' => 'Co-Q10',                          'name_ar' => 'الإنزيم المساعد كيو 10',   'dose' => '10 mg',      'amount' => '10 mg',      'nrv' => ''],
                    ['name_en' => 'Phosphatidylcholine',             'name_ar' => 'فوسفاتيديل كولين',         'dose' => '10 mg',      'amount' => '10 mg',      'nrv' => ''],
                    ['name_en' => 'Phosphatidylserine',              'name_ar' => 'فوسفاتيديل سيرين',          'dose' => '10 mg',      'amount' => '10 mg',      'nrv' => ''],
                    ['name_en' => 'Betacarotene',                    'name_ar' => 'بيتا كاروتين',             'dose' => '2 mg',       'amount' => '2 mg',       'nrv' => ''],
                    ['name_en' => 'Vitamin D (as D3 1000 IU)',       'name_ar' => 'فيتامين D3 (1000 وحدة)',   'dose' => '25 µg',      'amount' => '25 µg',      'nrv' => '500%'],
                    ['name_en' => 'Vitamin E',                       'name_ar' => 'فيتامين E',                'dose' => '36 mg α-TE', 'amount' => '36 mg α-TE', 'nrv' => '300%'],
                    ['name_en' => 'Vitamin C',                       'name_ar' => 'فيتامين C',                'dose' => '80 mg',      'amount' => '80 mg',      'nrv' => '100%'],
                    ['name_en' => 'Thiamin (Vitamin B1)',            'name_ar' => 'فيتامين B1 (ثيامين)',      'dose' => '25 mg',      'amount' => '25 mg',      'nrv' => '2273%'],
                    ['name_en' => 'Riboflavin (Vitamin B2)',         'name_ar' => 'فيتامين B2 (ريبوفلافين)',   'dose' => '3 mg',       'amount' => '3 mg',       'nrv' => '214%'],
                    ['name_en' => 'Niacin (Vitamin B3)',             'name_ar' => 'فيتامين B3 (نياسين)',       'dose' => '32 mg NE',   'amount' => '32 mg NE',   'nrv' => '200%'],
                    ['name_en' => 'Vitamin B6',                      'name_ar' => 'فيتامين B6',               'dose' => '10 mg',      'amount' => '10 mg',      'nrv' => '714%'],
                    ['name_en' => 'Folic Acid',                      'name_ar' => 'حمض الفوليك',              'dose' => '400 µg',     'amount' => '400 µg',     'nrv' => '200%'],
                    ['name_en' => 'Vitamin B12',                     'name_ar' => 'فيتامين B12',              'dose' => '100 µg',     'amount' => '100 µg',     'nrv' => '4000%'],
                    ['name_en' => 'Pantothenic acid',                'name_ar' => 'حمض البانتوثنيك (B5)',     'dose' => '36 mg',      'amount' => '36 mg',      'nrv' => '600%'],
                    ['name_en' => 'Magnesium',                       'name_ar' => 'مغنيسيوم',                 'dose' => '75 mg',      'amount' => '75 mg',      'nrv' => '20%'],
                    ['name_en' => 'Iron',                            'name_ar' => 'حديد',                     'dose' => '8 mg',       'amount' => '8 mg',       'nrv' => '57%'],
                    ['name_en' => 'Zinc',                            'name_ar' => 'زنك',                      'dose' => '15 mg',      'amount' => '15 mg',      'nrv' => '150%'],
                    ['name_en' => 'Copper',                          'name_ar' => 'نحاس',                     'dose' => '250 µg',     'amount' => '250 µg',     'nrv' => '25%'],
                    ['name_en' => 'Manganese',                       'name_ar' => 'منغنيز',                   'dose' => '2 mg',       'amount' => '2 mg',       'nrv' => '100%'],
                    ['name_en' => 'Selenium',                        'name_ar' => 'سيلينيوم',                 'dose' => '110 µg',     'amount' => '110 µg',     'nrv' => '200%'],
                    ['name_en' => 'Chromium',                        'name_ar' => 'كروم',                     'dose' => '40 µg',      'amount' => '40 µg',      'nrv' => '100%'],
                    ['name_en' => 'Iodine',                          'name_ar' => 'يود',                      'dose' => '150 µg',     'amount' => '150 µg',     'nrv' => '100%'],
                ],
                'variants' => [
                    [
                        'id' => 201,
                        'name_en' => 'Standard 30-Day Protocol (30 Tablets)',
                        'name_ar' => 'بروتوكول 30 يوماً القياسي (30 قرصاً)',
                        'sku' => 'BZ-MND-30T',
                        'price' => 68.00,
                        'stock_online' => 84,
                        'stock_offline' => 24,
                    ],
                ],
            ],

            /* ---------------------------------------------------------- */
            /* 3. BLUE PEA B COMPLEX                                      */
            /* ---------------------------------------------------------- */
            [
                'id' => 3,
                'slug' => 'blue-pea-b-complex',
                'sku' => 'BZ-PEA-001',
                'barcode' => '628100091003',
                'name_en' => 'Blue PEA B Complex',
                'name_ar' => 'بلو بي إي إيه بي كومبلكس',
                'tagline_en' => 'Nerve Support, Powered by PEA.',
                'tagline_ar' => 'دعم فائق لصحة الأعصاب مدعوم بمركب PEA',
                'category_en' => 'Nerve & Neurological Health',
                'category_ar' => 'صحة الأعصاب والجهاز العصبي',
                'subcategory_en' => 'Neuro-Protectors & Nerve Relief',
                'subcategory_ar' => 'حماية الأعصاب وتسكين الآلام العصبية',
                'brand' => 'Blue Zone Bioceuticals',
                'price' => 72.00,
                'sale_price' => 62.00,
                'is_featured' => true,
                'is_best_seller' => true,
                'is_new' => true,
                'status' => 'active',
                'rating' => 4.95,
                'reviews_count' => 96,
                'image' => '/assets/products/blue-pea-b-complex.jpg',
                'images' => [
                    '/assets/products/blue-pea-b-complex.jpg',
                    '/assets/products/blue-shield.jpg',
                ],
                'stock_online' => 110,
                'stock_offline' => 35,
                'low_stock_threshold' => 15,
                'short_description_en' => 'A targeted formula combining 800 mg of PEA with essential B vitamins to support healthy nerve function, nervous system performance, and everyday neurological well-being.',
                'short_description_ar' => 'تركيبة مستهدفة تجمع بين 800 ملغ من PEA مع فيتامينات B الأساسية لدعم صحة الأعصاب، أداء الجهاز العصبي، والراحة العصبية اليومية.',
                'description_en' => 'PEA Complex provides 800 mg of Palmitoylethanolamide (PEA) per daily serving, combined with vitamins B1, B6 and B12. PEA is an endogenous fatty-acid amide involved in physiological pathways associated with neuroimmune and inflammatory regulation. Vitamin B1 contributes to normal energy metabolism and nervous system function, while vitamins B6 and B12 contribute to normal neurological function and the normal formation of red blood cells. The formulation is intended to provide nutritional support for normal nervous system function and neuronal health.',
                'description_ar' => 'يوفر مركب PEA جرعة 800 ملغ من بالميتويل إيثانولاميد (PEA) يومياً مع فيتامينات B1 وB6 وB12. يعتبر PEA أميد حمض دهني طبيعي يشارك في المسارات الفسيولوجية المرتبطة بالتنظيم المناعي العصبي والالتهابي. يساهم فيتامين B1 في استقلاب الطاقة وعمل الجهاز العصبي، بينما يساهم B6 وB12 في الوظائف العصبية الطبيعية وتكوين خلايا الدم الحمراء لدعم صحة الخلايا العصبية.',
                'science_en' => "• PEA is a natural fatty acid made by your body to fight inflammation and calm irritated nerves.\n• How it works: It attaches to cell receptors (like PPAR-alpha) to stop the release of inflammatory chemicals and quiet overactive pain signals.\n• Sciatica and nerve studies: Clinical trials involving over 1,300 patients with nerve compression—including major studies on sciatic pain—demonstrate that PEA significantly cuts pain intensity.\n• Safety: Research shows PEA has an excellent safety profile with almost no reported side effects or drug interactions.\n• Combined effect of vitamin B complex: Clinical reviews show that taking B1, B6, and B12 together helps speed up nerve recovery in conditions like diabetic neuropathy and lumbar pain.",
                'science_ar' => "• مادة PEA هي حمض دهني طبيعي ينتجه الجسم لمكافحة الالتهابات وتهدئة الأعصاب المتهيجة.\n• آلية العمل: ترتبط بمستقبلات الخلايا (مثل PPAR-alpha) لمنع إفراز المواد الكيميائية الالتهابية وتهدئة إشارات الألم المفرطة.\n• دراسات عرق النسا وانضغاط الأعصاب: تجارب سريرية شملت أكثر من 1300 مريض يعانون من انضغاط الأعصاب أثبتت أن PEA يقلل شدة الألم بشكل ملحوظ.\n• الأمان الحيوي: أظهرت الأبحاث ملف أمان استثنائي لـ PEA دون أي آثار جانبية أو تداخلات دوائية مسجلة.\n• التأثير المشترك لمجمع فيتامين B: أظهرت المراجعات السريرية أن تناول B1 وB6 وB12 معاً يسرع تعافي الأعصاب في حالات الاعتلال العصبي وآلام أسفل الظهر.",
                'benefits_en' => [
                    'Targeted Neuro-Immune Calming: 800 mg Palmitoylethanolamide (PEA) activates PPAR-alpha receptors to quell inflammatory cascades.',
                    'Clinically Validated Sciatica & Nerve Relief: Proven in trials covering 1,300+ patients with chronic nerve compression.',
                    'High-Potency Neurotropic B-Complex: Delivers B1 (800%), B6 (400%), and B12 (4000% NRV) to accelerate myelin sheath and axonal repair.',
                    'Exceptional Clinical Safety Profile with no reported adverse interactions. Suitable for vegetarians and vegans.',
                ],
                'benefits_ar' => [
                    'تهدئة عصبية مناعية مستهدفة: 800 ملغ من بالميتويل إيثانولاميد (PEA) ينشط مستقبلات PPAR-alpha لتهدئة إشارات الألم والالتهاب.',
                    'دعم مثبت إكلينيكياً لعرق النسا والأعصاب: أثبتت التجارب على أكثر من 1300 مريض تخفيفاً ملموساً لآلام انضغاط الأعصاب.',
                    'مجمع فيتامينات B العصبية عالي الفعالية: يوفر B1 (800%)، B6 (400%)، وB12 (4000% NRV) لتسريع تجدد غلاف المايلين العصبي.',
                    'ملف أمان استثنائي دون تداخلات دوائية مسجلة، مناسب للنباتيين والنباتيين الصرف (Vegan).',
                ],
                'usage_en' => 'Take 2 capsules daily with a meal. Dose can be reduced to 1 daily for maintenance purposes. Do not exceed recommended daily dose. Food supplements should not replace a balanced diet and healthy lifestyle.',
                'usage_ar' => 'تناول كبسولتين يومياً مع الوجبة. يمكن تقليل الجرعة إلى كبسولة واحدة يومياً لأغراض الوقاية والمحافظة على النتائج. لا تتجاوز الجرعة الموصى بها.',
                'dosage_en' => '2 Veg Capsules Daily',
                'dosage_ar' => 'كبسولتان نباتيتان يومياً',
                'package_size_en' => '60 Vegetable Capsules (Serving: 2 Capsules Daily)',
                'package_size_ar' => '60 كبسولة نباتية (الجرعة: كبسولتان يومياً)',
                'gender' => 'Unisex',
                'age_group' => '18+',
                'health_goal_en' => 'Nerve Comfort, Sciatica Relief, Neurological Support',
                'health_goal_ar' => 'راحة الأعصاب، تخفيف آلام عرق النسا، والدعم العصبي',
                'professional_info' => [
                    'clinical_mechanism' => 'Palmitoylethanolamide binds to nuclear PPAR-alpha receptor, suppressing NF-kB nuclear translocation and mast cell degranulation, synergizing with B1/B6/B12 for myelin sheath axonal maintenance.',
                    'formula_details' => 'Micronized Palmitoylethanolamide (PEA) 800mg, Thiamin HCl (Vitamin B1) 8.8mg, Pyridoxine HCl (Vitamin B6) 5.0mg, Methylcobalamin (Vitamin B12) 100µg.',
                    'contraindications' => 'Not suitable during pregnancy or while breastfeeding. Consult physician for chronic neurological conditions.',
                    'warnings' => 'Not suitable during pregnancy or while breastfeeding. Perfect partner to glucosamine and turmeric. Suitable for vegetarians and vegans. STORE IN A COOL DRY PLACE.',
                ],
                'ingredients' => [
                    ['name_en' => 'Palmitoylethanolamide (PEA)', 'name_ar' => 'بالميتويل إيثانولاميد (PEA)', 'dose' => '800 mg', 'amount' => '800 mg', 'nrv' => ''],
                    ['name_en' => 'Thiamin (Vitamin B1)', 'name_ar' => 'ثيامين (فيتامين B1)', 'dose' => '8.8 mg', 'amount' => '8.8 mg', 'nrv' => '800%'],
                    ['name_en' => 'Vitamin B6', 'name_ar' => 'فيتامين B6', 'dose' => '5.0 mg', 'amount' => '5.0 mg', 'nrv' => '400%'],
                    ['name_en' => 'Vitamin B12', 'name_ar' => 'فيتامين B12', 'dose' => '100 µg', 'amount' => '100 µg', 'nrv' => '4000%'],
                ],
                'variants' => [
                    [
                        'id' => 301,
                        'name_en' => 'Standard Protocol Bottle (60 Capsules)',
                        'name_ar' => 'العبوة القياسية للبروتوكول (60 كبسولة)',
                        'sku' => 'BZ-PEA-60C',
                        'price' => 72.00,
                        'stock_online' => 80,
                        'stock_offline' => 25,
                    ],
                ],
            ],

            /* ---------------------------------------------------------- */
            /* 4. BLUE SHIELD                                             */
            /* ---------------------------------------------------------- */
            [
                'id' => 4,
                'slug' => 'blue-shield',
                'sku' => 'BZ-SHD-001',
                'barcode' => '628100091004',
                'name_en' => 'Blue Shield',
                'name_ar' => 'بلو شيلد',
                'tagline_en' => "More than D3 + K2—it's a complete support system.",
                'tagline_ar' => 'أكثر من مجرد D3 + K2 — إنه نظام دعم متكامل.',
                'category_en' => 'Immunity & Bone Health',
                'category_ar' => 'المناعة وصحة العظام',
                'subcategory_en' => 'Synergy Cofactors & D3/K2',
                'subcategory_ar' => 'العوامل المساعدة وتكامل D3/K2',
                'brand' => 'Blue Zone Bioceuticals',
                'price' => 58.00,
                'sale_price' => 48.00,
                'is_featured' => true,
                'is_best_seller' => false,
                'is_new' => true,
                'status' => 'active',
                'rating' => 4.92,
                'reviews_count' => 164,
                'image' => '/assets/products/blue-shield.jpg',
                'images' => [
                    '/assets/products/blue-shield.jpg',
                    '/assets/products/blue-pea-b-complex.jpg',
                ],
                'stock_online' => 180,
                'stock_offline' => 60,
                'low_stock_threshold' => 20,
                'short_description_en' => "A smart combination of Vitamin D3, K2, Magnesium, Zinc, and Boron, working together to support optimal calcium metabolism. Helps support strong bones, healthy muscles, and resilient immune function.",
                'short_description_ar' => 'تركيبة ذكية تجمع بين فيتامين D3، K2، المغنيسيوم، الزنك، والبورون، تعمل معاً لدعم التمثيل الغذائي الأمثل للكالسيوم، مما يدعم قوة العظام، صحة العضلات، والمناعة القوية.',
                'description_en' => "Our advanced formula is not just D3 and K2, it is designed as a full system and cofactor formula to support bone health, resilient immunity and muscle performance.\n• Vitamin D3: supports calcium absorption and immune function. It is properly dosed not underdosed.\n• Vitamin K2 (MK-7): directs calcium to bones not soft tissues, so it supports bone strength and heart health.\n• Magnesium glycinate: helps activate vitamin D3 so the body can use it effectively.\n• Zinc Picolinate: essential for the enzyme that activates vitamin D3 and supports immune and metabolic function.\n• Boron Citrate: helps vitamin D3 stay active for longer and supports calcium and magnesium metabolism.",
                'description_ar' => "تركيبتنا المتقدمة ليست مجرد D3 وK2، بل هي نظام كامل وعوامل مساعدة لدعم صحة العظام، والمناعة القوية، والأداء العضلي:\n• فيتامين D3: يدعم امتصاص الكالسيوم ووظائف المناعة بجرعة سريرية مثالية غير منقوصة.\n• فيتامين K2 (MK-7): يوجه الكالسيوم إلى العظام وليس الأنسجة الرخوة، مما يدعم قوة العظام وصحة القلب.\n• مغنيسيوم غلايسينات: يساعد في تنشيط فيتامين D3 لتمكين الجسم من استخدامه بكفاءة.\n• زنك بيكولينات: ضروري للإنزيم الذي ينشط فيتامين D3 ويدعم المناعة والأيض.\n• بورون سيترات: يساعد في بقاء فيتامين D3 نشطاً لفترة أطول ويدعم استقلاب الكالسيوم والمغنيسيوم.",
                'science_en' => "Why D3?\nStronger Bones: Vitamin D3 with all four essential cofactors to help maintain strong bones and enhance calcium absorption.\nImmune Support: Vitamin D3 supports normal immune function and helps your body's natural defences.\nMuscle Strength: Vitamin D3 supports normal muscle function to help maintain strength.\n\nWhy D3 Meets K2?\nVitamin D3 helps your body absorb calcium, while vitamin K contributes to the normal utilization of calcium in bone tissue and directs calcium away from the arteries. Together, they support complementary steps in maintaining normal bone and heart health.\n\nWhy More Than D3 + K2?\nVitamin D is not designed to work alone. Its activation and function depend on a network of complementary nutrients involved in vitamin D metabolism, calcium regulation, and bone mineralization. That is why we combined D3 + K2 (MK-7) with Magnesium, Zinc, and Boron—creating a more complete cofactor system rather than a basic two-ingredient formula.\n\nOne formula. A smarter approach to vitamin D support.",
                'science_ar' => "لماذا D3؟\nعظام أقوى: فيتامين D3 مع جميع العوامل المساعدة الأساسية الأربعة للحفاظ على قوة العظام وتعزيز امتصاص الكالسيوم.\nدعم المناعة: يدعم فيتامين D3 وظيفة الجهاز المناعي الطبيعية ودفاعات الجسم.\nقوة العضلات: يدعم فيتامين D3 وظائف العضلات للحفاظ على القوة البدنية.\n\nلماذا D3 مع K2؟\nيساعد فيتامين D3 الجسم على امتصاص الكالسيوم، بينما يساهم فيتامين K في الاستخدام السليم للكالسيوم في العظام وتوجيهه بعيداً عن الشرايين لحماية القلب والأوعية.\n\nلماذا أكثر من مجرد D3 + K2؟\nفيتامين D لا يعمل بمفرده. يعتمد تنشيطه على شبكة مغذيات متكاملة: المغنيسيوم غلايسينات ينشط الإنزيمات الكبدية والكلوية المحولة لـ D3، والزنك بيكولينات يدعم مستقبلات VDR، والبورون سيترات يطيل نصف عمر الفيتامين النشط في الدم.\n\nصيغة واحدة. نهج أذكى لدعم فيتامين D.",
                'benefits_en' => [
                    'Clinically Dosed 4,000 IU Vitamin D3 (Cholecalciferol) for complete immune activation and optimal calcium absorption.',
                    '100 mcg Vitamin K2 (Menaquinone-7 / MK-7) to direct calcium specifically to bone matrix and prevent arterial calcification.',
                    '300 mg Highly Absorbable Magnesium Glycinate (providing 60mg elemental Mg) to activate hepatic and renal Vitamin D enzymes.',
                    'Zinc Picolinate (8 mg) & Boron Citrate (3 mg) synergistically extend Vitamin D biological half-life and enhance bone mineralization.',
                ],
                'benefits_ar' => [
                    'جرعة سريرية مثالية 4000 وحدة دولية من فيتامين D3 (كوليكالسيفيرول) لتنشيط المناعة وامتصاص الكالسيوم الأقصى.',
                    '100 ميكروغرام فيتامين K2 بصيغة MK-7 لتوجيه الكالسيوم حصراً إلى العظام وتفادي تكلس الشرايين والأنسجة الرخوة.',
                    '300 ملغ مغنيسيوم غلايسينات عالي التوافر الحيوي (يوفر 60 ملغ مغنيسيوم عنصري) لتنشيط إنزيمات فيتامين D3 في الكبد والكلى.',
                    'زنك بيكولينات (8 ملغ) وبورون سيترات (3 ملغ) لإطالة عمر الفيتامين النشط في الدم وتعزيز كثافة العظام.',
                ],
                'usage_en' => 'Take 1 capsule daily with a main meal, preferably a meal containing some dietary fat, or as directed by your healthcare professional. Do not exceed the recommended daily intake.',
                'usage_ar' => 'تناول كبسولة واحدة يومياً مع وجبة رئيسية، ويفضل أن تحتوي الوجبة على بعض الدهون الصحية، أو حسب توجيهات أخصائي الرعاية الصحية. لا تتجاوز الجرعة اليومية الموصى بها.',
                'dosage_en' => '1 Capsule Daily',
                'dosage_ar' => 'كبسولة واحدة يومياً',
                'package_size_en' => '60 Capsules (Serving: 1 Capsule Daily - 2 Months Supply)',
                'package_size_ar' => '60 كبسولة (الجرعة: كبسولة واحدة يومياً - تكفي شهرين)',
                'gender' => 'Unisex',
                'age_group' => '18+',
                'health_goal_en' => 'Bone Density, Calcium Metabolism, Immunity, Muscle Strength',
                'health_goal_ar' => 'كثافة العظام، استقلاب الكالسيوم، المناعة، وقوة العضلات',
                'professional_info' => [
                    'clinical_mechanism' => 'Vitamin D3 increases enterocyte TRPV6 and calbindin-D9k expression. Vitamin K2 carboxylates osteocalcin and matrix Gla-protein. Magnesium and Zinc act as obligatory cofactors for 25-hydroxylase and 1-alpha-hydroxylase enzymes, while Boron downregulates 24-hydroxylase degradation.',
                    'formula_details' => 'Cholecalciferol (Vitamin D3) 4000 IU (100µg), Menaquinone-7 (Vitamin K2 MK-7) 100µg, Magnesium Bisglycinate Chelate 300mg (providing 60mg elemental Mg), Zinc Picolinate 8mg, Boron Citrate 3mg.',
                    'contraindications' => 'Adult use only. Not suitable for individuals taking prescription blood thinners (coumarins / warfarin) without physician clearance.',
                    'warnings' => 'Adult use only. Do not exceed stated amount. Keep out of reach of children. Consult your doctor before use if you are pregnant/nursing, taking medication or have a medical condition. Supplements should not be used to replace a varied diet. Not suitable for individuals on blood thinning medication. Do not use if safety seal is broken or damaged. Always take with food.',
                ],
                'ingredients' => [
                    ['name_en' => 'Vitamin D as Cholecalciferol', 'name_ar' => 'فيتامين D3 (كوليكالسيفيرول)', 'dose' => '4000 IU / 100 µg', 'amount' => '4000 IU / 100 µg', 'nrv' => '2000%'],
                    ['name_en' => 'Vitamin K2 as Menaquinone-7', 'name_ar' => 'فيتامين K2 (ميناكينون-7 MK-7)', 'dose' => '100 µg', 'amount' => '100 µg', 'nrv' => '133%'],
                    ['name_en' => 'Magnesium glycinate', 'name_ar' => 'مغنيسيوم غلايسينات', 'dose' => '300 mg', 'amount' => '300 mg', 'nrv' => ''],
                    ['name_en' => 'Providing Magnesium', 'name_ar' => 'يوفر مغنيسيوم عنصري', 'dose' => '60 mg', 'amount' => '60 mg', 'nrv' => '16%'],
                    ['name_en' => 'Zinc picolinate', 'name_ar' => 'زنك بيكولينات', 'dose' => '8 mg', 'amount' => '8 mg', 'nrv' => '80%'],
                    ['name_en' => 'Boron citrate', 'name_ar' => 'بورون سيترات', 'dose' => '3 mg', 'amount' => '3 mg', 'nrv' => ''],
                ],
                'variants' => [
                    [
                        'id' => 401,
                        'name_en' => 'Standard 60-Day Bottle (60 Capsules)',
                        'name_ar' => 'العبوة القياسية 60 يوماً (60 كبسولة)',
                        'sku' => 'BZ-SHD-60C',
                        'price' => 58.00,
                        'stock_online' => 120,
                        'stock_offline' => 40,
                    ],
                ],
            ],

        ];
    }

    /**
     * Find product by slug or id.
     *
     * @param string|int $identifier
     * @return array<string, mixed>|null
     */
    public static function find(string|int $identifier): ?array
    {
        $aliases = [
            'blue-flex' => 'blue-joint',
            'blue-defense' => 'blue-shield',
            'blue-immunity' => 'blue-shield',
            'blue-cell' => 'blue-pea-b-complex',
            'blue-restore' => 'blue-pea-b-complex',
            'blue-sleep' => 'blue-pea-b-complex',
            'blue-calm' => 'blue-mind',
        ];

        if (is_string($identifier) && isset($aliases[$identifier])) {
            $identifier = $aliases[$identifier];
        }

        $products = self::all();
        foreach ($products as $p) {
            if ($p['slug'] === (string)$identifier || $p['id'] === (int)$identifier) {
                return $p;
            }
        }
        return null;
    }
}
