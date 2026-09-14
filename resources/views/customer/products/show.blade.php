@php
    $isRtl = app()->getLocale() === 'ar';
    $isObj = is_object($product);

    $pSlug = $isObj ? ($product->slug ?? 'blue-mind') : ($product['slug'] ?? 'blue-mind');
    $pId = $isObj ? ($product->id ?? 1) : ($product['id'] ?? 1);
    
    $pName = $isObj 
        ? ($isRtl && !empty($product->name_ar) ? $product->name_ar : ($product->name_en ?? $product->name ?? 'BLUE MIND'))
        : ($isRtl && !empty($product['name_ar']) ? $product['name_ar'] : ($product['name_en'] ?? $product['name'] ?? 'BLUE MIND'));
        
    $pTagline = $isObj
        ? ($isRtl && !empty($product->tagline_ar) ? $product->tagline_ar : ($product->tagline_en ?? 'SYNAPTIC PLASTICITY & NEURAL LONGEVITY'))
        : ($isRtl && !empty($product['tagline_ar']) ? $product['tagline_ar'] : ($product['tagline_en'] ?? 'SYNAPTIC PLASTICITY & NEURAL LONGEVITY'));

    $pCategory = $isObj 
        ? ($isRtl && !empty($product->category?->name_ar) ? $product->category->name_ar : ($product->category?->name ?? 'COGNITIVE'))
        : ($isRtl && !empty($product['category_ar']) ? $product['category_ar'] : ($product['category_en'] ?? 'COGNITIVE'));

    $pImage = $isObj 
        ? ($product->primary_image_url ?? asset('assets/products/blue-mind.webp'))
        : asset($product['image'] ?? 'assets/products/blue-mind.webp');

    $pImages = $isObj ? ($product->images ?? [$pImage]) : ($product['images'] ?? [$pImage]);
    if (empty($pImages)) $pImages = [$pImage];

    $pShortDesc = $isObj 
        ? ($isRtl && !empty($product->short_description_ar) ? $product->short_description_ar : ($product->short_description_en ?? $product->short_description ?? 'Precision clinical formulation inspired by Blue Zone longevity research.'))
        : ($isRtl && !empty($product['short_description_ar']) ? $product['short_description_ar'] : ($product['short_description_en'] ?? 'Precision clinical formulation inspired by Blue Zone longevity research.'));

    $pDescription = $isObj 
        ? ($isRtl && !empty($product->description_ar) ? $product->description_ar : ($product->description_en ?? $product->description ?? ''))
        : ($isRtl && !empty($product['description_ar']) ? $product['description_ar'] : ($product['description_en'] ?? ''));

    if (empty($pDescription)) {
        $pDescription = $pShortDesc;
    }

    $pPrice = $isObj ? ($product->price ?? 79) : ($product['price'] ?? 79);
    $pSalePrice = $isObj ? ($product->sale_price ?? null) : ($product['sale_price'] ?? null);
    $pSku = $isObj ? ($product->sku ?? 'BZ-' . strtoupper(substr($pSlug, 0, 3)) . '-001') : ($product['sku'] ?? 'BZ-MND-001');
    $pRating = $isObj ? ($product->rating ?? 4.95) : ($product['rating'] ?? 4.95);
    $pReviewsCount = $isObj ? ($product->reviews_count ?? 340) : ($product['reviews_count'] ?? 340);
    $pStock = $isObj ? ($product->stock_online ?? 95) : ($product['stock_online'] ?? 95);

    // Dynamic resolution helper
    $getVal = function ($fieldEn, $fieldAr = null) use ($product, $isRtl, $isObj) {
        if ($isRtl && $fieldAr) {
            $arVal = $isObj ? ($product->{$fieldAr} ?? null) : ($product[$fieldAr] ?? null);
            if (!empty($arVal)) return $arVal;
        }
        $enVal = $isObj ? ($product->{$fieldEn} ?? null) : ($product[$fieldEn] ?? null);
        if (!empty($enVal)) return $enVal;
        if ($fieldAr) {
            $fallbackAr = $isObj ? ($product->{$fieldAr} ?? null) : ($product[$fieldAr] ?? null);
            if (!empty($fallbackAr)) return $fallbackAr;
        }
        return null;
    };

    // Ingredients Resolution
    $rawIngredients = $isObj ? ($product->ingredients ?? []) : ($product['ingredients'] ?? []);
    if (is_string($rawIngredients)) {
        $rawIngredients = json_decode($rawIngredients, true) ?: [];
    }

    $defaultIngredientsBySlug = [
        'blue-mind' => [
            ['name_en' => 'Ginkgo Biloba Extract (50:1 Standardized)', 'name_ar' => 'مستخلص الجنكة بيلوبا المعياري (50:1)', 'dose' => '120 mg', 'role' => 'Cerebral perfusion & microvascular blood flow', 'origin' => 'Mediterranean Leaf'],
            ['name_en' => 'Phosphatidylserine Matrix', 'name_ar' => 'مصفوفة فوسفاتيديل سيرين النشطة', 'dose' => '150 mg', 'role' => 'Neuronal synaptic membrane fluidity & acetylcholine', 'origin' => 'Sunflower Lecithin'],
            ['name_en' => 'Phosphatidylcholine Complex', 'name_ar' => 'مركب فوسفاتيديل كولين النقي', 'dose' => '100 mg', 'role' => 'Neurotransmitter precursor & memory retention', 'origin' => 'Bio-Identical'],
            ['name_en' => 'Kaneka Ubiquinol (CoQ10)', 'name_ar' => 'يوبيكوينول كانيكا المنقى (CoQ10)', 'dose' => '100 mg', 'role' => 'Cellular mitochondrial respiratory chain defense', 'origin' => 'Bio-Fermented Yeast'],
            ['name_en' => 'Setria® L-Glutathione', 'name_ar' => 'إل-جلوتاثيون نقي (Setria®)', 'dose' => '50 mg', 'role' => 'Master neural intracellular antioxidant', 'origin' => 'Fermented Bio-Active'],
        ],
        'blue-cell' => [
            ['name_en' => 'Pharmaceutical β-NMN (99.8% Pure)', 'name_ar' => 'بيتا-NMN دوائي فائق النقاء (99.8%)', 'dose' => '300 mg', 'role' => 'Direct intracellular NAD+ biosynthetic precursor', 'origin' => 'Enzymatic Bio-Synthesis'],
            ['name_en' => 'Micronized Trans-Resveratrol', 'name_ar' => 'ترانس-ريسفيراترول ميكروني مجزأ', 'dose' => '200 mg', 'role' => 'Allosteric activator of longevity sirtuins SIRT1', 'origin' => 'Japanese Knotweed'],
            ['name_en' => 'Apigenin Botanical Complex', 'name_ar' => 'مركب الأبيجينين النباتي المركز', 'dose' => '50 mg', 'role' => 'CD38 enzymatic inhibitor preserving cellular NAD+', 'origin' => 'Chamomile Flower'],
            ['name_en' => 'PQQ (Pyrroloquinoline Quinone)', 'name_ar' => 'بيرولوكينولين كينون (PQQ)', 'dose' => '20 mg', 'role' => 'Stimulates de-novo mitochondrial biogenesis', 'origin' => 'Organic Ferment'],
        ],
        'blue-sleep' => [
            ['name_en' => 'Standardized Tart Cherry (Anthocyanins)', 'name_ar' => 'مستخلص الكرز الحامض المعياري', 'dose' => '400 mg', 'role' => 'Natural circadian melatonin peptide balance', 'origin' => 'Montmorency Cherry'],
            ['name_en' => 'Magnesium Bisglycinate Chelate', 'name_ar' => 'مغنيسيوم بيسجليسينات مخلبي', 'dose' => '250 mg', 'role' => 'GABAergic receptor relaxation & muscle recovery', 'origin' => 'Fully Chelated'],
            ['name_en' => 'L-Theanine Bio-Pure', 'name_ar' => 'إل-ثيانين نقي بيولوجياً', 'dose' => '200 mg', 'role' => 'Promotes alpha-brainwave architecture', 'origin' => 'Green Tea Extract'],
            ['name_en' => 'Valerian Root Supercritical Extract', 'name_ar' => 'مستخلص جذر الناردين عالي النقاوة', 'dose' => '150 mg', 'role' => 'Enhances slow-wave delta restorative sleep', 'origin' => 'European Botanical'],
        ],
        'blue-defense' => [
            ['name_en' => 'Elderberry Extract (Sambucus Nigra)', 'name_ar' => 'مستخلص الخلسان الأسود المركز', 'dose' => '500 mg', 'role' => 'Mucosal barrier defense & viral shielding', 'origin' => 'Wild European Berry'],
            ['name_en' => 'Liposomal Vitamin C', 'name_ar' => 'فيتامين سي الجسيمي فائق الامتصاص', 'dose' => '500 mg', 'role' => 'Phagocyte stimulation & collagen protection', 'origin' => 'Liposomal Matrix'],
            ['name_en' => 'Zinc Picolinate Bio-Chelate', 'name_ar' => 'زنك بيكولينات عالي التوافر الحيوي', 'dose' => '25 mg', 'role' => 'T-lymphocyte activation & thymus support', 'origin' => 'Chelated Mineral'],
            ['name_en' => 'Optimum Vitamin D3 (Cholecalciferol)', 'name_ar' => 'فيتامين D3 بالصورة الفعالة', 'dose' => '2500 IU', 'role' => 'Innate antimicrobial peptide modulation', 'origin' => 'Wild Lanolin'],
        ],
    ];

    $ingredientsList = !empty($rawIngredients) 
        ? $rawIngredients 
        : ($defaultIngredientsBySlug[$pSlug] ?? $defaultIngredientsBySlug['blue-mind']);

    // Benefits Resolution
    $rawBenefits = $isObj ? ($product->benefits_en ?? []) : ($product['benefits_en'] ?? []);
    if ($isRtl) {
        $arBenefits = $isObj ? ($product->benefits_ar ?? []) : ($product['benefits_ar'] ?? []);
        if (!empty($arBenefits)) $rawBenefits = $arBenefits;
    }
    if (is_string($rawBenefits)) {
        $rawBenefits = json_decode($rawBenefits, true) ?: [$rawBenefits];
    }

    $defaultBenefits = $isRtl ? [
        'تعزيز سرعة الاستجابة الإدراكية والتركيز الذهني العميق دون هبوط أو توتر عصبي',
        'حماية الميتوكوندريا العصبية والأوعية الدماغية الدقيقة من الإجهاد التأكسدي والالتهابات الخلوية',
        'دعم عامل التغذية العصبية BDNF وتحفيز مرونة المشابك المسؤولة عن الذاكرة طويلة المدى',
        'توفير طاقة خلوية مستدامة لأكثر من 8 ساعات متواصلة عبر تحسين تدفق الأكسجين'
    ] : [
        'Sustained mental sharpness & deep focus without central nervous system jitters or crashes',
        'Shields neuronal mitochondria & cerebral microvessels from oxidative decay and cellular stress',
        'Supports BDNF (Brain-Derived Neurotrophic Factor) and synaptic plasticity for learning recall',
        'Optimizes cellular ATP and oxygen perfusion across blood-brain neural networks for 8+ hours'
    ];

    $benefitsList = !empty($rawBenefits) ? $rawBenefits : $defaultBenefits;

    // Usage & Protocol Resolution
    $usageText = $getVal('usage_en', 'usage_ar') ?: ($isRtl 
        ? 'تناول كبسولتين صباحاً مع 250 مل من الماء النقي، يفضل مع مصدر دهون صحية كزيت الزيتون البكر الممتاز لتعزيز الامتصاص الحيوي للمركبات النشطة.' 
        : 'Take 2 capsules every morning with 250ml of pure mineral water, preferably alongside a healthy fat source such as cold-pressed extra virgin olive oil to maximize phospholipid bioavailability.');

    $dosageText = $getVal('dosage_en', 'dosage_ar') ?: ($isRtl ? 'كبسولتان نباتيتان يومياً' : '2 Vegetable Capsules Daily');
    $packageSize = $getVal('product_size', 'package_size_ar') ?: ($isRtl ? '60 كبسولة نباتية معوية (تكفي 30 يوماً)' : '60 Enteric Vegetable Capsules (30-Day Supply)');

    // Science & Cellular Mechanism Resolution
    $clinicalMechanism = $getVal('science_en', 'science_ar') 
        ?: ($isObj ? $product->clinical_mechanism : ($product['professional_info']['clinical_mechanism'] ?? null));

    if (empty($clinicalMechanism)) {
        $clinicalMechanism = $isRtl
            ? 'يعمل المستحضر عبر آلية دوائية ثلاثية: تحفيز تكوين الأستيل كولين العصبي، الحفاظ على سلامة الغشاء الفسفوليبيدي للخلية العصبية، وتحييد الجذور الحرة داخل الميتوكوندريا لحماية الطاقة الخلوية.'
            : 'Formulated to target the core bio-energetic pathways: activates presynaptic acetylcholine synthesis, stabilizes neuronal phospholipid membrane bilayer fluidity, and quenches reactive oxygen species within mitochondrial complexes.';
    }

    // Warnings & Contraindications Resolution
    $warningsText = $isObj ? ($product->warnings ?? null) : ($product['professional_info']['warnings'] ?? null);
    $contraindicationsText = $isObj ? ($product->contraindications ?? null) : ($product['professional_info']['contraindications'] ?? null);

    if (empty($warningsText)) {
        $warningsText = $isRtl 
            ? 'يُحفظ بعيداً عن متناول الأطفال في مكان جاف وبارد تحت 25 درجة مئوية بعيداً عن أشعة الشمس المباشرة. يُنصح باستشارة الطبيب المعالج لمن يتناولون مسيلات الدم أو الحوامل والمرضعات.'
            : 'Keep out of reach of children. Store in a cool, dry, dark environment below 25°C. Individuals on anticoagulants (blood thinners) or under specialized clinical supervision should consult their physician prior to use.';
    }
@endphp

<x-layouts.customer :title="$pName . ' — ' . __('app.brand_name')" :description="$pShortDesc">
    <!-- Inline Scoped CSS for Ultra-Smooth Collapsible Accordion Grid Animations -->
    <style>
        .bz-collapsible-grid {
            display: grid;
            grid-template-rows: 0fr;
            transition: grid-template-rows 0.38s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.3s ease;
            opacity: 0;
        }
        .bz-collapsible-item.is-open .bz-collapsible-grid {
            grid-template-rows: 1fr;
            opacity: 1;
        }
        .bz-collapsible-inner {
            overflow: hidden;
        }
        .bz-chevron-icon {
            transition: transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .bz-collapsible-item.is-open .bz-chevron-icon {
            transform: rotate(180deg);
        }
        .bz-collapsible-item.is-open {
            border-color: rgba(103, 179, 74, 0.45);
            box-shadow: 0 12px 30px -10px rgba(10, 79, 120, 0.08);
        }
        .filter-tab-pill.active {
            background: #0A4F78;
            color: #ffffff;
            border-color: #0A4F78;
        }
        .dark .filter-tab-pill.active {
            background: #2A8FC2;
            color: #031827;
            border-color: #2A8FC2;
        }
        @keyframes bzPulseGlow {
            0%, 100% { opacity: 0.6; transform: scale(1); }
            50% { opacity: 1; transform: scale(1.15); }
        }
        .bz-pulse-dot {
            animation: bzPulseGlow 2.5s infinite ease-in-out;
        }
    </style>

    <div class="py-10 sm:py-16 bg-[#F6F5EF] dark:bg-[#031827] min-h-screen transition-colors">
      <div id="product-detail-container" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-16">
        
        <!-- Top Back Navigation & Breadcrumb -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-4 border-b border-[#0A4F78]/15">
          <a href="{{ route('customer.shop') }}" class="inline-flex items-center gap-2 text-xs font-extrabold uppercase tracking-widest text-[#0A4F78] dark:text-[#2A8FC2] hover:text-[#67B34A] transition-colors">
            <i class="fa-solid fa-arrow-left rtl:rotate-180 mr-1.5 ml-1.5"></i> {{ $isRtl ? 'العودة لكتالوج المستحضرات' : 'BACK TO PRODUCTS' }}
          </a>
          <div class="text-[11px] font-bold uppercase tracking-widest text-[#031827]/60 dark:text-[#F6F5EF]/60">
            <a href="{{ route('customer.home') }}" class="hover:text-[#0A4F78] dark:hover:text-[#2A8FC2]">{{ $isRtl ? 'الرئيسية' : 'HOME' }}</a> / 
            <a href="{{ route('customer.shop') }}" class="hover:text-[#0A4F78] dark:hover:text-[#2A8FC2]">{{ $isRtl ? 'المتجر' : 'PRODUCTS' }}</a> / 
            <span class="text-[#0A4F78] dark:text-[#2A8FC2]">{{ $pName }}</span>
          </div>
        </div>

        <!-- 1. PRODUCT HERO & BUY BOX -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
          
          <!-- Image Gallery Container -->
          <div class="lg:col-span-6 space-y-4">
            <div class="aspect-square rounded-3xl overflow-hidden border border-[#0A4F78]/20 shadow-2xl relative bg-[#031827] group">
              <img 
                id="product-main-img"
                src="{{ $pImage }}" 
                alt="{{ $pName }}" 
                onerror="this.onerror=null; this.src='{{ asset('assets/products/blue-mind.webp') }}';" 
                class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-700 ease-out" 
              />
              <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-[#031827]/50 via-transparent to-transparent"></div>
              <div class="pointer-events-none absolute inset-0 ring-1 ring-inset ring-white/10 rounded-3xl"></div>
              
              <!-- Category Badge -->
              <span class="absolute top-5 left-5 rtl:left-auto rtl:right-5 z-10 text-xs font-black uppercase tracking-widest px-3.5 py-1.5 rounded-full bg-white/95 dark:bg-[#031827]/95 text-[#0A4F78] dark:text-[#2A8FC2] backdrop-blur-md shadow-md border border-white/40 dark:border-[#2A8FC2]/40">
                {{ $pCategory }}
              </span>

              <!-- Live Verification Pill -->
              <div class="absolute bottom-5 left-5 rtl:left-auto rtl:right-5 z-10 flex items-center gap-2 px-3 py-1.5 rounded-full bg-[#031827]/85 backdrop-blur-md border border-white/15 text-[11px] text-white font-mono">
                <span class="w-2 h-2 rounded-full bg-[#67B34A] bz-pulse-dot"></span>
                <span>HPLC TRIPLE-ASSAYED</span>
              </div>
            </div>

            <!-- Thumbnail Carousel if Multiple Images -->
            @if(count($pImages) > 1)
              <div class="flex gap-3 overflow-x-auto pb-2 scrollbar-none">
                @foreach($pImages as $idx => $tImg)
                  <button type="button" onclick="switchMainImage('{{ asset($tImg) }}', this)" 
                    class="w-20 h-20 rounded-xl overflow-hidden border-2 transition-all shrink-0 cursor-pointer {{ $idx === 0 ? 'border-[#67B34A] ring-2 ring-[#67B34A]/30' : 'border-[#0A4F78]/20 opacity-70 hover:opacity-100' }}">
                    <img src="{{ asset($tImg) }}" alt="{{ $pName }} thumbnail" class="w-full h-full object-cover" />
                  </button>
                @endforeach
              </div>
            @endif
          </div>

          <!-- Product Buy Box Details -->
          <div class="lg:col-span-6 space-y-6">
            <div class="space-y-2">
              <div class="flex items-center gap-2.5 flex-wrap">
                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-[#67B34A]/15 text-[#67B34A] font-bold text-xs">
                  <i class="fa-solid fa-star text-amber-400"></i>
                  <span>{{ number_format($pRating, 2) }}</span>
                </div>
                <span class="text-xs text-[#031827]/60 dark:text-[#F6F5EF]/60 font-medium">
                  ({{ $pReviewsCount }}+ {{ $isRtl ? 'تقييم سريري معتمد' : 'Verified Clinical Reviews' }})
                </span>
                <span class="text-xs px-2.5 py-0.5 rounded-full bg-[#0A4F78]/10 text-[#0A4F78] dark:bg-[#2A8FC2]/10 dark:text-[#2A8FC2] font-mono font-bold">
                  SKU: {{ $pSku }}
                </span>
              </div>

              <h1 class="text-3xl sm:text-5xl font-black text-[#031827] dark:text-[#F6F5EF] tracking-tight">
                {{ $pName }}
              </h1>
              
              <p class="text-xs uppercase font-extrabold tracking-widest text-[#0A4F78] dark:text-[#2A8FC2]">
                {{ $pTagline }}
              </p>
            </div>

            <!-- Price & Stock Status -->
            <div class="flex items-baseline gap-4 flex-wrap">
              <div class="text-3xl sm:text-4xl font-black text-[#0A4F78] dark:text-[#2A8FC2]">
                @currency($pPrice)
              </div>
              @if($pSalePrice && $pSalePrice < $pPrice)
                <div class="text-lg line-through text-[#031827]/40 dark:text-[#F6F5EF]/40 font-mono">
                  @currency($pSalePrice)
                </div>
              @endif
              <span class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1 rounded-full {{ $pStock > 10 ? 'bg-[#67B34A]/15 text-[#67B34A]' : 'bg-amber-500/15 text-amber-600 dark:text-amber-400' }}">
                <span class="w-2 h-2 rounded-full {{ $pStock > 10 ? 'bg-[#67B34A]' : 'bg-amber-500' }}"></span>
                {{ $pStock > 10 ? ($isRtl ? 'متوفر بالمخزون البارد' : 'In Stock · Cold-Chain Ready') : ($isRtl ? 'كمية محدودة متبقية' : 'Limited Batch Remaining') }}
              </span>
            </div>

            <p class="text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 leading-relaxed font-medium">
              {{ $pDescription }}
            </p>

            <!-- Protocol Variant Selection Options -->
            <div class="p-4 rounded-2xl bg-white dark:bg-[#062B49] border border-[#0A4F78]/15 space-y-3">
              <span class="text-xs font-bold uppercase tracking-wider text-[#031827] dark:text-[#F6F5EF] block">
                <i class="fa-solid fa-layer-group text-primary mr-1 ml-1"></i> {{ $isRtl ? 'اختر خطة البروتوكول العلاجي:' : 'Select Protocol Duration:' }}
              </span>
              <div class="grid grid-cols-2 gap-3">
                <button type="button" onclick="selectProtocolVariant(1, {{ $pPrice }}, this)" 
                  class="protocol-variant-btn p-3 rounded-xl border-2 border-[#67B34A] bg-[#67B34A]/5 text-left rtl:text-right transition-all cursor-pointer">
                  <div class="flex justify-between items-center mb-1">
                    <span class="text-xs font-extrabold text-[#031827] dark:text-white">{{ $isRtl ? 'بروتوكول 30 يوماً' : '30-Day Starter' }}</span>
                    <span class="text-xs font-bold text-[#67B34A]">1x</span>
                  </div>
                  <span class="text-[11px] text-[#031827]/70 dark:text-[#F6F5EF]/70 block">{{ $packageSize }}</span>
                </button>

                <button type="button" onclick="selectProtocolVariant(3, {{ $pPrice * 2.55 }}, this)" 
                  class="protocol-variant-btn p-3 rounded-xl border border-[#0A4F78]/20 hover:border-[#2A8FC2] text-left rtl:text-right transition-all cursor-pointer relative overflow-hidden">
                  <span class="absolute top-0 right-0 rtl:right-auto rtl:left-0 text-[9px] font-black uppercase tracking-wider bg-[#0A4F78] text-white px-2 py-0.5 rounded-bl-lg rtl:rounded-bl-none rtl:rounded-br-lg">
                    {{ $isRtl ? 'خصم 15%' : 'SAVE 15%' }}
                  </span>
                  <div class="flex justify-between items-center mb-1">
                    <span class="text-xs font-extrabold text-[#031827] dark:text-white">{{ $isRtl ? 'بروتوكول 90 يوماً' : '90-Day Cellular' }}</span>
                    <span class="text-xs font-bold text-primary">3x</span>
                  </div>
                  <span class="text-[11px] text-[#031827]/70 dark:text-[#F6F5EF]/70 block">{{ $isRtl ? '3 عبوات كاملة (الأنسب للنتائج)' : '3 Complete Bottles (Recommended)' }}</span>
                </button>
              </div>
            </div>

            <!-- Quantity & Actions -->
            <div class="space-y-4 pt-2 border-t border-[#0A4F78]/20">
              <div class="flex items-center gap-4">
                <span class="text-xs font-bold uppercase tracking-wider text-[#031827] dark:text-[#F6F5EF]">
                  {{ $isRtl ? 'الكمية:' : 'QUANTITY:' }}
                </span>
                <div class="flex items-center border border-[#0A4F78]/30 rounded-xl overflow-hidden bg-white dark:bg-[#031827]">
                  <button type="button" onclick="updateQty(-1)" class="px-3.5 py-2.5 cursor-pointer font-bold hover:bg-[#0A4F78]/10 text-[#031827] dark:text-white transition-colors">
                    <i class="fa-solid fa-minus text-xs"></i>
                  </button>
                  <span id="detail-qty-val" class="px-4 text-xs font-bold text-[#031827] dark:text-white font-mono">1</span>
                  <button type="button" onclick="updateQty(1)" class="px-3.5 py-2.5 cursor-pointer font-bold hover:bg-[#0A4F78]/10 text-[#031827] dark:text-white transition-colors">
                    <i class="fa-solid fa-plus text-xs"></i>
                  </button>
                </div>
              </div>

              <div class="flex gap-4">
                <button type="button" onclick="BLUEZONE_CART.add('{{ $pSlug }}', currentDetailQty); if(window.toast){ window.toast.success('{{ $isRtl ? 'تمت إضافة المستحضر إلى السلة بنجاح' : 'Added to Clinical Protocol Cart' }}'); }" 
                  class="flex-1 py-4 px-6 rounded-2xl bg-[#0A4F78] hover:bg-[#062B49] text-white text-xs uppercase font-extrabold tracking-widest shadow-xl cursor-pointer transition-all active:scale-95 flex items-center justify-center gap-3">
                  <i class="fa-solid fa-cart-shopping"></i>
                  <span>{{ $isRtl ? 'إضافة إلى السلة والبروتوكول' : 'ADD TO CLINICAL PROTOCOL' }}</span>
                </button>

                <button type="button" onclick="BLUEZONE_WISHLIST.toggle('{{ $pSlug }}')" aria-label="Wishlist" 
                  class="p-4 rounded-2xl border-2 border-[#0A4F78]/30 dark:border-[#2A8FC2]/40 text-[#0A4F78] dark:text-[#2A8FC2] hover:bg-[#0A4F78]/10 font-bold cursor-pointer transition-all">
                  <i class="fa-regular fa-heart text-lg"></i>
                </button>
              </div>
            </div>

            <!-- Clinical Features Badge List -->
            <div class="grid grid-cols-2 gap-3 pt-3 border-t border-[#0A4F78]/15 text-xs font-bold text-[#031827]/75 dark:text-[#F6F5EF]/75">
              <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-[#67B34A]"></i> 
                <span>{{ $isRtl ? 'مستخلصات نباتية مطابقة حيوياً 100%' : '100% Bio-Identical Extracts' }}</span>
              </div>
              <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-[#67B34A]"></i> 
                <span>{{ $isRtl ? 'فحص مخبري ثلاثي للمعادن الثقيلة' : 'Third-Party Heavy Metal Screened' }}</span>
              </div>
              <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-[#67B34A]"></i> 
                <span>{{ $isRtl ? 'خالٍ من الملونات والمواد المالئة' : 'Zero Artificial Fillers or Binders' }}</span>
              </div>
              <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-[#67B34A]"></i> 
                <span>{{ $isRtl ? 'محفوظ في سلاسل شحن مبردة' : 'Cold-Chain Nitrogen Sealed' }}</span>
              </div>
            </div>

          </div>
        </div>

        <!-- ========================================================================= -->
        <!-- 2. PROFESSIONAL COLLAPSIBLE PRODUCT DETAILS ACCORDION STUDIO              -->
        <!-- ========================================================================= -->
        <div class="space-y-6 pt-6" id="clinical-specifications-dossier">
          
          <!-- Dossier Section Header & Interactive Controls -->
          <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 pb-4 border-b border-[#0A4F78]/20">
            <div>
              <span class="text-[11px] font-extrabold uppercase tracking-[0.28em] text-[#0A4F78] dark:text-[#2A8FC2] block mb-1">
                {{ $isRtl ? 'الملف الطبي والمواصفات السريرية المعتمدة' : 'CLINICAL DOSSIER & BIO-MOLECULAR MONOGRAPH' }}
              </span>
              <h2 class="text-2xl sm:text-4xl font-black text-[#031827] dark:text-[#F6F5EF] tracking-tight">
                {{ $isRtl ? 'التفاصيل الدقيقة والخصائص الدوائية للمستحضر' : 'Comprehensive Formulation Specifications' }}
              </h2>
            </div>

            <!-- Accordion Global Actions -->
            <div class="flex items-center gap-2">
              <button type="button" onclick="expandAllProductDetails()" 
                class="px-3.5 py-2 rounded-xl text-xs font-bold border border-[#0A4F78]/20 dark:border-white/20 hover:bg-[#0A4F78]/10 text-[#0A4F78] dark:text-[#2A8FC2] transition-colors flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-angles-down"></i>
                <span>{{ $isRtl ? 'توسيع الكل' : 'Expand All' }}</span>
              </button>
              <button type="button" onclick="collapseAllProductDetails()" 
                class="px-3.5 py-2 rounded-xl text-xs font-bold border border-[#0A4F78]/20 dark:border-white/20 hover:bg-[#0A4F78]/10 text-[#031827]/70 dark:text-[#F6F5EF]/70 transition-colors flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-angles-up"></i>
                <span>{{ $isRtl ? 'طي الكل' : 'Collapse All' }}</span>
              </button>
            </div>
          </div>

          <!-- Quick Navigation Jump Filter Pills -->
          <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none text-xs font-bold">
            <button type="button" onclick="openAndScrollToAccordion('accordion-ingredients')" 
              class="filter-tab-pill px-4 py-2 rounded-full border border-[#0A4F78]/20 text-[#031827] dark:text-[#F6F5EF] hover:border-[#0A4F78] transition-all shrink-0 flex items-center gap-2 cursor-pointer">
              <i class="fa-solid fa-flask-vial text-[#67B34A]"></i>
              <span>{{ $isRtl ? 'المكونات الفعالة' : 'Active Compounds' }}</span>
            </button>
            <button type="button" onclick="openAndScrollToAccordion('accordion-benefits')" 
              class="filter-tab-pill px-4 py-2 rounded-full border border-[#0A4F78]/20 text-[#031827] dark:text-[#F6F5EF] hover:border-[#0A4F78] transition-all shrink-0 flex items-center gap-2 cursor-pointer">
              <i class="fa-solid fa-bolt text-amber-500"></i>
              <span>{{ $isRtl ? 'الفوائد السريرية' : 'Clinical Benefits' }}</span>
            </button>
            <button type="button" onclick="openAndScrollToAccordion('accordion-usage')" 
              class="filter-tab-pill px-4 py-2 rounded-full border border-[#0A4F78]/20 text-[#031827] dark:text-[#F6F5EF] hover:border-[#0A4F78] transition-all shrink-0 flex items-center gap-2 cursor-pointer">
              <i class="fa-solid fa-calendar-check text-[#2A8FC2]"></i>
              <span>{{ $isRtl ? 'بروتوكول الاستخدام' : 'Usage Protocol' }}</span>
            </button>
            <button type="button" onclick="openAndScrollToAccordion('accordion-science')" 
              class="filter-tab-pill px-4 py-2 rounded-full border border-[#0A4F78]/20 text-[#031827] dark:text-[#F6F5EF] hover:border-[#0A4F78] transition-all shrink-0 flex items-center gap-2 cursor-pointer">
              <i class="fa-solid fa-dna text-purple-500"></i>
              <span>{{ $isRtl ? 'الآلية العلمية' : 'Cellular Science' }}</span>
            </button>
            <button type="button" onclick="openAndScrollToAccordion('accordion-safety')" 
              class="filter-tab-pill px-4 py-2 rounded-full border border-[#0A4F78]/20 text-[#031827] dark:text-[#F6F5EF] hover:border-[#0A4F78] transition-all shrink-0 flex items-center gap-2 cursor-pointer">
              <i class="fa-solid fa-shield-halved text-emerald-600"></i>
              <span>{{ $isRtl ? 'السلامة والتحذيرات' : 'Safety & Warnings' }}</span>
            </button>
            <button type="button" onclick="openAndScrollToAccordion('accordion-specs')" 
              class="filter-tab-pill px-4 py-2 rounded-full border border-[#0A4F78]/20 text-[#031827] dark:text-[#F6F5EF] hover:border-[#0A4F78] transition-all shrink-0 flex items-center gap-2 cursor-pointer">
              <i class="fa-solid fa-certificate text-indigo-500"></i>
              <span>{{ $isRtl ? 'المواصفات والضمان' : 'Monograph & Specs' }}</span>
            </button>
          </div>

          <!-- THE 6 COLLAPSIBLE ACCORDION ITEMS -->
          <div class="space-y-4">
            
            <!-- 1. ACCORDION: ACTIVE BIO-COMPOUNDS & INGREDIENTS -->
            <div id="accordion-ingredients" class="bz-collapsible-item is-open rounded-3xl bg-white dark:bg-[#062B49] border border-[#0A4F78]/20 transition-all duration-300 overflow-hidden">
              <button type="button" onclick="toggleProductDetailItem('accordion-ingredients')" 
                class="w-full p-5 sm:p-7 flex justify-between items-center text-left rtl:text-right cursor-pointer select-none group">
                <div class="flex items-center gap-4">
                  <div class="w-12 h-12 rounded-2xl bg-[#67B34A]/15 text-[#67B34A] flex items-center justify-center text-xl shrink-0 group-hover:scale-105 transition-transform">
                    <i class="fa-solid fa-flask-vial"></i>
                  </div>
                  <div>
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                      <span class="text-[10px] font-mono uppercase px-2 py-0.5 rounded bg-[#67B34A]/15 text-[#67B34A] font-extrabold">SECTION 01</span>
                      <span class="text-[11px] font-bold text-[#031827]/60 dark:text-[#F6F5EF]/60 uppercase tracking-widest">{{ $isRtl ? 'النقاء والجرعات القياسية' : 'Standardized Monograph' }}</span>
                    </div>
                    <h3 class="text-lg sm:text-xl font-black text-[#031827] dark:text-[#F6F5EF]">
                      {{ $isRtl ? 'المكونات الفعالة والجرعات المعيارية' : 'Active Bio-Compounds & Potencies' }}
                    </h3>
                  </div>
                </div>

                <div class="flex items-center gap-3">
                  <span class="hidden sm:inline-block px-3 py-1 rounded-full bg-[#0A4F78]/10 text-[#0A4F78] dark:bg-[#2A8FC2]/15 dark:text-[#2A8FC2] text-xs font-bold font-mono">
                    {{ count($ingredientsList) }} {{ $isRtl ? 'مركبات نشطة' : 'Bio-Actives' }}
                  </span>
                  <span class="bz-chevron-icon w-8 h-8 rounded-full bg-[#0A4F78]/5 dark:bg-white/5 flex items-center justify-center text-sm text-[#0A4F78] dark:text-[#2A8FC2]">
                    <i class="fa-solid fa-chevron-down"></i>
                  </span>
                </div>
              </button>

              <div class="bz-collapsible-grid">
                <div class="bz-collapsible-inner p-5 sm:p-8 pt-0 border-t border-[#0A4F78]/10 dark:border-[#0A4F78]/20 space-y-6">
                  <p class="text-xs sm:text-sm text-[#031827]/75 dark:text-[#F6F5EF]/75 leading-relaxed font-medium">
                    {{ $isRtl 
                        ? 'تخضع جميع المواد الفعالة في هذه التركيبة لمعايرة مخبرية دقيقة وفق دستور الأدوية العالمي، لضمان أعلى توافر حيوي وامتصاص خلوي دون أي شوائب اصطناعية.'
                        : 'Every active bio-compound in this formulation is calibrated to therapeutic clinical thresholds using triple third-party HPLC verification, guaranteeing maximum cellular uptake and batch-to-batch consistency.' }}
                  </p>

                  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($ingredientsList as $ing)
                      @php
                        $ingName = $isRtl ? ($ing['name_ar'] ?? $ing['name_en'] ?? $ing['name'] ?? '') : ($ing['name_en'] ?? $ing['name'] ?? '');
                        $ingDose = $ing['dose'] ?? $ing['dosage'] ?? $ing['amount'] ?? 'Standardized';
                        $ingRole = $ing['role'] ?? ($isRtl ? 'دعم الوظائف الحيوية الخلوية' : 'Targeted cellular biological pathway');
                        $ingOrigin = $ing['origin'] ?? ($isRtl ? 'مستخلص طبيعي' : 'Botanical Bio-Active');
                      @endphp
                      <div class="p-4 rounded-2xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 space-y-2 relative overflow-hidden group/ing hover:border-[#67B34A]/50 transition-colors">
                        <div class="flex justify-between items-start gap-2">
                          <span class="text-xs font-bold text-[#031827] dark:text-[#F6F5EF] leading-snug">
                            {{ $ingName }}
                          </span>
                          <span class="px-2.5 py-0.5 rounded-full bg-[#67B34A]/15 text-[#67B34A] text-[11px] font-mono font-black shrink-0">
                            {{ $ingDose }}
                          </span>
                        </div>
                        <p class="text-[11px] text-[#031827]/70 dark:text-[#F6F5EF]/70 leading-relaxed font-medium">
                          {{ $ingRole }}
                        </p>
                        <div class="pt-2 border-t border-[#0A4F78]/10 flex items-center justify-between text-[10px] text-[#0A4F78] dark:text-[#2A8FC2] font-mono">
                          <span><i class="fa-solid fa-seedling mr-1 ml-1 text-[#67B34A]"></i> {{ $ingOrigin }}</span>
                          <span>99%+ PURE</span>
                        </div>
                      </div>
                    @endforeach
                  </div>

                  <!-- Quality Standards Badges -->
                  <div class="p-4 rounded-2xl bg-[#0A4F78]/5 dark:bg-white/5 border border-[#0A4F78]/10 flex flex-wrap gap-4 items-center justify-around text-xs font-bold text-[#0A4F78] dark:text-[#2A8FC2]">
                    <span><i class="fa-solid fa-check-double text-[#67B34A] mr-1.5 ml-1.5"></i> {{ $isRtl ? 'فحص HPLC ثلاثي' : 'Triple HPLC Assayed' }}</span>
                    <span><i class="fa-solid fa-shield-virus text-[#67B34A] mr-1.5 ml-1.5"></i> {{ $isRtl ? 'خالٍ من الملوثات والسموم' : 'Heavy Metal Screened' }}</span>
                    <span><i class="fa-solid fa-dna text-[#67B34A] mr-1.5 ml-1.5"></i> {{ $isRtl ? 'نباتي غير معدل وراثياً' : '100% Non-GMO & Vegan' }}</span>
                    <span><i class="fa-solid fa-snowflake text-[#67B34A] mr-1.5 ml-1.5"></i> {{ $isRtl ? 'معبأ بتقنية حفظ النيتروجين' : 'Nitrogen Flushed' }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- 2. ACCORDION: CLINICAL BENEFITS & TIMELINE -->
            <div id="accordion-benefits" class="bz-collapsible-item rounded-3xl bg-white dark:bg-[#062B49] border border-[#0A4F78]/20 transition-all duration-300 overflow-hidden">
              <button type="button" onclick="toggleProductDetailItem('accordion-benefits')" 
                class="w-full p-5 sm:p-7 flex justify-between items-center text-left rtl:text-right cursor-pointer select-none group">
                <div class="flex items-center gap-4">
                  <div class="w-12 h-12 rounded-2xl bg-amber-500/15 text-amber-500 flex items-center justify-center text-xl shrink-0 group-hover:scale-105 transition-transform">
                    <i class="fa-solid fa-bolt"></i>
                  </div>
                  <div>
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                      <span class="text-[10px] font-mono uppercase px-2 py-0.5 rounded bg-amber-500/15 text-amber-600 dark:text-amber-400 font-extrabold">SECTION 02</span>
                      <span class="text-[11px] font-bold text-[#031827]/60 dark:text-[#F6F5EF]/60 uppercase tracking-widest">{{ $isRtl ? 'الأثر الفسيولوجي المباشر والمستدام' : 'Physiological Biomarkers' }}</span>
                    </div>
                    <h3 class="text-lg sm:text-xl font-black text-[#031827] dark:text-[#F6F5EF]">
                      {{ $isRtl ? 'الفوائد السريرية والمسارات البيولوجية' : 'Clinical Benefits & Physiological Targets' }}
                    </h3>
                  </div>
                </div>

                <div class="flex items-center gap-3">
                  <span class="hidden sm:inline-block px-3 py-1 rounded-full bg-amber-500/10 text-amber-600 dark:text-amber-400 text-xs font-bold font-mono">
                    {{ count($benefitsList) }} {{ $isRtl ? 'محاور رئيسية' : 'Core Outcomes' }}
                  </span>
                  <span class="bz-chevron-icon w-8 h-8 rounded-full bg-[#0A4F78]/5 dark:bg-white/5 flex items-center justify-center text-sm text-[#0A4F78] dark:text-[#2A8FC2]">
                    <i class="fa-solid fa-chevron-down"></i>
                  </span>
                </div>
              </button>

              <div class="bz-collapsible-grid">
                <div class="bz-collapsible-inner p-5 sm:p-8 pt-0 border-t border-[#0A4F78]/10 dark:border-[#0A4F78]/20 space-y-6">
                  
                  <!-- Bullet list of clinical outcomes -->
                  <div class="space-y-3">
                    @foreach($benefitsList as $benefit)
                      <div class="flex items-start gap-3 p-3.5 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/10">
                        <span class="w-6 h-6 rounded-full bg-[#67B34A] text-white flex items-center justify-center shrink-0 text-xs font-bold mt-0.5">
                          <i class="fa-solid fa-check"></i>
                        </span>
                        <span class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 font-medium leading-relaxed">
                          {{ $benefit }}
                        </span>
                      </div>
                    @endforeach
                  </div>

                  <!-- 3-Phase Cumulative Timeline -->
                  <div class="space-y-3 pt-2">
                    <span class="text-xs font-extrabold uppercase tracking-widest text-[#0A4F78] dark:text-[#2A8FC2] block">
                      {{ $isRtl ? 'مراحل التراكم الحيوي واستجابة الجسم:' : 'Progressive Biological Timeline:' }}
                    </span>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                      <div class="p-4 rounded-2xl bg-[#0A4F78]/5 dark:bg-[#031827] border border-[#0A4F78]/15 space-y-1.5">
                        <span class="text-xs font-extrabold text-[#67B34A] font-mono uppercase block">{{ $isRtl ? 'المرحلة 1 · اليوم 1 إلى 7' : 'Phase 1 · Days 1–7' }}</span>
                        <h4 class="text-xs font-bold text-[#031827] dark:text-white">{{ $isRtl ? 'الاستجابة الخلوية الفورية' : 'Acute Perfusion & Alertness' }}</h4>
                        <p class="text-[11px] text-[#031827]/70 dark:text-[#F6F5EF]/70 leading-relaxed">
                          {{ $isRtl ? 'تحسين تدفق الدم الدماغي، رفع مستوى اليقظة، وتحييد بوادر الإجهاد الذهني اليومي.' : 'Immediate microcirculation enhancement and neural stamina without nervous system crash.' }}
                        </p>
                      </div>

                      <div class="p-4 rounded-2xl bg-[#0A4F78]/5 dark:bg-[#031827] border border-[#0A4F78]/15 space-y-1.5">
                        <span class="text-xs font-extrabold text-[#2A8FC2] font-mono uppercase block">{{ $isRtl ? 'المرحلة 2 · اليوم 14 إلى 30' : 'Phase 2 · Days 14–30' }}</span>
                        <h4 class="text-xs font-bold text-[#031827] dark:text-white">{{ $isRtl ? 'تجدد المشابك العصبية' : 'Synaptic Consolidation' }}</h4>
                        <p class="text-[11px] text-[#031827]/70 dark:text-[#F6F5EF]/70 leading-relaxed">
                          {{ $isRtl ? 'تحفيز إشارات BDNF، استقرار غشاء الخلايا الفسفوليبيدي، وزيادة كفاءة الذاكرة العاملة.' : 'Elevated BDNF growth signaling and sustained neurotransmitter balance for cognitive resilience.' }}
                        </p>
                      </div>

                      <div class="p-4 rounded-2xl bg-[#0A4F78]/5 dark:bg-[#031827] border border-[#0A4F78]/15 space-y-1.5">
                        <span class="text-xs font-extrabold text-[#0A4F78] dark:text-[#67B34A] font-mono uppercase block">{{ $isRtl ? 'المرحلة 3 · اليوم 60 فما فوق' : 'Phase 3 · Days 60+' }}</span>
                        <h4 class="text-xs font-bold text-[#031827] dark:text-white">{{ $isRtl ? 'حماية طول العمر الخلوي' : 'Deep Cellular Longevity' }}</h4>
                        <p class="text-[11px] text-[#031827]/70 dark:text-[#F6F5EF]/70 leading-relaxed">
                          {{ $isRtl ? 'حماية شاملة للميتوكوندريا، ودعم صحة الأوعية الدموية ومرونة الأداء الدماغي الممتد.' : 'Mitochondrial resilience and ongoing genomic stability supporting lifelong cognitive health.' }}
                        </p>
                      </div>
                    </div>
                  </div>

                </div>
              </div>
            </div>

            <!-- 3. ACCORDION: PROTOCOL & USAGE INSTRUCTIONS -->
            <div id="accordion-usage" class="bz-collapsible-item rounded-3xl bg-white dark:bg-[#062B49] border border-[#0A4F78]/20 transition-all duration-300 overflow-hidden">
              <button type="button" onclick="toggleProductDetailItem('accordion-usage')" 
                class="w-full p-5 sm:p-7 flex justify-between items-center text-left rtl:text-right cursor-pointer select-none group">
                <div class="flex items-center gap-4">
                  <div class="w-12 h-12 rounded-2xl bg-[#2A8FC2]/15 text-[#2A8FC2] flex items-center justify-center text-xl shrink-0 group-hover:scale-105 transition-transform">
                    <i class="fa-solid fa-calendar-check"></i>
                  </div>
                  <div>
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                      <span class="text-[10px] font-mono uppercase px-2 py-0.5 rounded bg-[#2A8FC2]/15 text-[#2A8FC2] font-extrabold">SECTION 03</span>
                      <span class="text-[11px] font-bold text-[#031827]/60 dark:text-[#F6F5EF]/60 uppercase tracking-widest">{{ $isRtl ? 'الجرعات والتكامل الحيوي' : 'Synergistic Protocol' }}</span>
                    </div>
                    <h3 class="text-lg sm:text-xl font-black text-[#031827] dark:text-[#F6F5EF]">
                      {{ $isRtl ? 'بروتوكول الاستخدام والتوجيهات السريرية' : 'Clinical Protocol & Usage Guidelines' }}
                    </h3>
                  </div>
                </div>

                <div class="flex items-center gap-3">
                  <span class="hidden sm:inline-block px-3 py-1 rounded-full bg-[#2A8FC2]/10 text-[#2A8FC2] text-xs font-bold font-mono">
                    {{ $dosageText }}
                  </span>
                  <span class="bz-chevron-icon w-8 h-8 rounded-full bg-[#0A4F78]/5 dark:bg-white/5 flex items-center justify-center text-sm text-[#0A4F78] dark:text-[#2A8FC2]">
                    <i class="fa-solid fa-chevron-down"></i>
                  </span>
                </div>
              </button>

              <div class="bz-collapsible-grid">
                <div class="bz-collapsible-inner p-5 sm:p-8 pt-0 border-t border-[#0A4F78]/10 dark:border-[#0A4F78]/20 space-y-6">
                  
                  <div class="p-4 sm:p-6 rounded-2xl bg-[#0A4F78]/5 dark:bg-[#031827] border-inline-start-4 border-primary space-y-2">
                    <span class="text-xs font-extrabold uppercase tracking-wider text-[#0A4F78] dark:text-[#2A8FC2] block">
                      <i class="fa-solid fa-circle-info mr-1 ml-1"></i> {{ $isRtl ? 'الإرشاد السريري المباشر:' : 'Primary Administration Directive:' }}
                    </span>
                    <p class="text-xs sm:text-sm text-[#031827] dark:text-[#F6F5EF] font-medium leading-relaxed">
                      {{ $usageText }}
                    </p>
                  </div>

                  <!-- Visual Protocol Cards -->
                  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="p-4 rounded-2xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 space-y-2 text-center">
                      <div class="w-10 h-10 rounded-full bg-[#67B34A]/15 text-[#67B34A] mx-auto flex items-center justify-center text-base">
                        <i class="fa-solid fa-sun"></i>
                      </div>
                      <h4 class="text-xs font-bold text-[#031827] dark:text-white">{{ $isRtl ? '1. التوقيت اليومي' : '1. Timing' }}</h4>
                      <p class="text-[11px] text-[#031827]/70 dark:text-[#F6F5EF]/70 leading-relaxed">
                        {{ $isRtl ? 'يُفضل تناوله صباحاً ليتزامن مع إيقاع الساعة البيولوجية للجسم.' : 'Administer each morning to harmonize with your natural circadian cortisol curve.' }}
                      </p>
                    </div>

                    <div class="p-4 rounded-2xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 space-y-2 text-center">
                      <div class="w-10 h-10 rounded-full bg-[#2A8FC2]/15 text-[#2A8FC2] mx-auto flex items-center justify-center text-base">
                        <i class="fa-solid fa-glass-water"></i>
                      </div>
                      <h4 class="text-xs font-bold text-[#031827] dark:text-white">{{ $isRtl ? '2. السائل الناقل' : '2. Hydration' }}</h4>
                      <p class="text-[11px] text-[#031827]/70 dark:text-[#F6F5EF]/70 leading-relaxed">
                        {{ $isRtl ? '250 مل من الماء النقي لتسريع ذوبان الكبسولة المعوية النباتية.' : '250ml mineralized water facilitates rapid gastro-resistant dissolution.' }}
                      </p>
                    </div>

                    <div class="p-4 rounded-2xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 space-y-2 text-center">
                      <div class="w-10 h-10 rounded-full bg-amber-500/15 text-amber-500 mx-auto flex items-center justify-center text-base">
                        <i class="fa-solid fa-bottle-droplet"></i>
                      </div>
                      <h4 class="text-xs font-bold text-[#031827] dark:text-white">{{ $isRtl ? '3. تعزيز الامتصاص' : '3. Lipid Synergy' }}</h4>
                      <p class="text-[11px] text-[#031827]/70 dark:text-[#F6F5EF]/70 leading-relaxed">
                        {{ $isRtl ? 'تناوله مع زيت الزيتون البكر الممتاز يضاعف امتصاص مضادات الأكسدة.' : 'Ingesting with healthy fats boosts lipophilic flavonoid bioavailability by up to 300%.' }}
                      </p>
                    </div>

                    <div class="p-4 rounded-2xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 space-y-2 text-center">
                      <div class="w-10 h-10 rounded-full bg-purple-500/15 text-purple-500 mx-auto flex items-center justify-center text-base">
                        <i class="fa-solid fa-repeat"></i>
                      </div>
                      <h4 class="text-xs font-bold text-[#031827] dark:text-white">{{ $isRtl ? '4. الاستمرارية' : '4. Regularity' }}</h4>
                      <p class="text-[11px] text-[#031827]/70 dark:text-[#F6F5EF]/70 leading-relaxed">
                        {{ $isRtl ? 'لا يسبب الإدمان أو الاعتمادية، ومصمم للاستخدام اليومي المستمر بأمان.' : 'Zero tolerance buildup or dependency. Engineered for uninterrupted daily longevity.' }}
                      </p>
                    </div>
                  </div>

                </div>
              </div>
            </div>

            <!-- 4. ACCORDION: CELLULAR SCIENCE & PHARMACOKINETICS -->
            <div id="accordion-science" class="bz-collapsible-item rounded-3xl bg-white dark:bg-[#062B49] border border-[#0A4F78]/20 transition-all duration-300 overflow-hidden">
              <button type="button" onclick="toggleProductDetailItem('accordion-science')" 
                class="w-full p-5 sm:p-7 flex justify-between items-center text-left rtl:text-right cursor-pointer select-none group">
                <div class="flex items-center gap-4">
                  <div class="w-12 h-12 rounded-2xl bg-purple-500/15 text-purple-500 flex items-center justify-center text-xl shrink-0 group-hover:scale-105 transition-transform">
                    <i class="fa-solid fa-dna"></i>
                  </div>
                  <div>
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                      <span class="text-[10px] font-mono uppercase px-2 py-0.5 rounded bg-purple-500/15 text-purple-600 dark:text-purple-400 font-extrabold">SECTION 04</span>
                      <span class="text-[11px] font-bold text-[#031827]/60 dark:text-[#F6F5EF]/60 uppercase tracking-widest">{{ $isRtl ? 'الأبحاث الصيدلانية وطول العمر' : 'Molecular Pharmacokinetics' }}</span>
                    </div>
                    <h3 class="text-lg sm:text-xl font-black text-[#031827] dark:text-[#F6F5EF]">
                      {{ $isRtl ? 'الآلية العلمية الدوائية المتقدمة' : 'Cellular Science & Mechanism of Action' }}
                    </h3>
                  </div>
                </div>

                <div class="flex items-center gap-3">
                  <span class="hidden sm:inline-block px-3 py-1 rounded-full bg-purple-500/10 text-purple-600 dark:text-purple-400 text-xs font-bold font-mono">
                    {{ $isRtl ? 'مسارات حيوية دقيقة' : 'Precision Pathways' }}
                  </span>
                  <span class="bz-chevron-icon w-8 h-8 rounded-full bg-[#0A4F78]/5 dark:bg-white/5 flex items-center justify-center text-sm text-[#0A4F78] dark:text-[#2A8FC2]">
                    <i class="fa-solid fa-chevron-down"></i>
                  </span>
                </div>
              </button>

              <div class="bz-collapsible-grid">
                <div class="bz-collapsible-inner p-5 sm:p-8 pt-0 border-t border-[#0A4F78]/10 dark:border-[#0A4F78]/20 space-y-6">
                  
                  <div class="space-y-4">
                    <p class="text-xs sm:text-sm text-[#031827]/85 dark:text-[#F6F5EF]/85 leading-relaxed font-medium">
                      {{ $clinicalMechanism }}
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                      <div class="p-4 rounded-2xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 space-y-2">
                        <span class="text-xs font-extrabold text-[#0A4F78] dark:text-[#2A8FC2] uppercase block">
                          <i class="fa-solid fa-microchip mr-1 ml-1 text-[#67B34A]"></i> {{ $isRtl ? 'نفاذية الحاجز الدموي الدماغي (BBB):' : 'Blood-Brain Barrier (BBB) Kinetics:' }}
                        </span>
                        <p class="text-[11px] text-[#031827]/75 dark:text-[#F6F5EF]/75 leading-relaxed">
                          {{ $isRtl ? 'مركبات الفسفوليبيد المرفقة تمكن الجزيئات الحيوية من عبور الحاجز الدموي الدماغي بكفاءة، مما يوفر تركيزاً نشطاً داخل المشابك العصبية دون فقدان هضمي.' : 'Phospholipid-conjugated carriers enable efficient transport across the blood-brain barrier for localized neuronal receptor binding.' }}
                        </p>
                      </div>

                      <div class="p-4 rounded-2xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 space-y-2">
                        <span class="text-xs font-extrabold text-[#0A4F78] dark:text-[#2A8FC2] uppercase block">
                          <i class="fa-solid fa-atom mr-1 ml-1 text-purple-500"></i> {{ $isRtl ? 'حماية ميتوكوندريا الخلية والتنفس الهوائي:' : 'Mitochondrial Redox Equilibrium:' }}
                        </span>
                        <p class="text-[11px] text-[#031827]/75 dark:text-[#F6F5EF]/75 leading-relaxed">
                          {{ $isRtl ? 'يحمي الإنزيم المساعد CoQ10 وسلسلة مضادات الأكسدة المعقدة من تسرب الإلكترونات والإجهاد التأكسدي، مما يعزز عمر الخلية وقدرتها الإنتاجية.' : 'Sustains electron transport chain integrity (Complexes I & III), shielding mitochondrial DNA from premature senescence.' }}
                        </p>
                      </div>
                    </div>

                    <div class="p-4 rounded-xl bg-purple-500/10 border border-purple-500/20 text-xs text-[#031827] dark:text-[#F6F5EF] flex items-center gap-3">
                      <span class="w-8 h-8 rounded-lg bg-purple-500 text-white flex items-center justify-center shrink-0 font-bold">
                        <i class="fa-solid fa-book-bookmark"></i>
                      </span>
                      <span>
                        {{ $isRtl ? 'مبني على أبحاث طول العمر المستوحاة من دراسات المعمرين في المناطق الزرقاء الخمس المنشورة في كبرى الدوريات الطبية.' : 'Validated against empirical longevity data from centenarian cohort studies published in peer-reviewed journals of cellular pharmacology.' }}
                      </span>
                    </div>
                  </div>

                </div>
              </div>
            </div>

            <!-- 5. ACCORDION: SAFETY, WARNINGS & QUALITY CONTROLS -->
            <div id="accordion-safety" class="bz-collapsible-item rounded-3xl bg-white dark:bg-[#062B49] border border-[#0A4F78]/20 transition-all duration-300 overflow-hidden">
              <button type="button" onclick="toggleProductDetailItem('accordion-safety')" 
                class="w-full p-5 sm:p-7 flex justify-between items-center text-left rtl:text-right cursor-pointer select-none group">
                <div class="flex items-center gap-4">
                  <div class="w-12 h-12 rounded-2xl bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl shrink-0 group-hover:scale-105 transition-transform">
                    <i class="fa-solid fa-shield-halved"></i>
                  </div>
                  <div>
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                      <span class="text-[10px] font-mono uppercase px-2 py-0.5 rounded bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 font-extrabold">SECTION 05</span>
                      <span class="text-[11px] font-bold text-[#031827]/60 dark:text-[#F6F5EF]/60 uppercase tracking-widest">{{ $isRtl ? 'الأمان والنزاهة الصيدلانية' : 'Clinical Safety & Warnings' }}</span>
                    </div>
                    <h3 class="text-lg sm:text-xl font-black text-[#031827] dark:text-[#F6F5EF]">
                      {{ $isRtl ? 'إرشادات السلامة، التحذيرات وموانع الاستعمال' : 'Safety Profile, Warnings & Precautions' }}
                    </h3>
                  </div>
                </div>

                <div class="flex items-center gap-3">
                  <span class="hidden sm:inline-block px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-xs font-bold font-mono">
                    {{ $isRtl ? 'معايير أمان معتمدة' : 'cGMP Verified' }}
                  </span>
                  <span class="bz-chevron-icon w-8 h-8 rounded-full bg-[#0A4F78]/5 dark:bg-white/5 flex items-center justify-center text-sm text-[#0A4F78] dark:text-[#2A8FC2]">
                    <i class="fa-solid fa-chevron-down"></i>
                  </span>
                </div>
              </button>

              <div class="bz-collapsible-grid">
                <div class="bz-collapsible-inner p-5 sm:p-8 pt-0 border-t border-[#0A4F78]/10 dark:border-[#0A4F78]/20 space-y-6">
                  
                  <!-- Amber Medical Warning Box -->
                  <div class="p-4 sm:p-5 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-900 dark:text-amber-200 space-y-2">
                    <div class="flex items-center gap-2 text-xs font-bold text-amber-700 dark:text-amber-400 uppercase tracking-wider">
                      <i class="fa-solid fa-triangle-exclamation"></i>
                      <span>{{ $isRtl ? 'تنبيه سريري وإرشادات الحفظ:' : 'Medical Advisory & Precautions:' }}</span>
                    </div>
                    <p class="text-xs leading-relaxed">
                      {{ $warningsText }}
                    </p>
                  </div>

                  <!-- Allergen & Purity Grid -->
                  <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center text-xs font-bold">
                    <div class="p-3 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 space-y-1">
                      <i class="fa-solid fa-ban text-[#67B34A] text-base"></i>
                      <span class="block text-[#031827] dark:text-white">{{ $isRtl ? 'خالٍ من الجلوتين' : 'Gluten-Free' }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 space-y-1">
                      <i class="fa-solid fa-leaf text-[#67B34A] text-base"></i>
                      <span class="block text-[#031827] dark:text-white">{{ $isRtl ? 'خالٍ من الصويا' : 'Soy-Free' }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 space-y-1">
                      <i class="fa-solid fa-droplet-slash text-[#67B34A] text-base"></i>
                      <span class="block text-[#031827] dark:text-white">{{ $isRtl ? 'خالٍ من المشتقات الحيوانية' : '100% Vegan' }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 space-y-1">
                      <i class="fa-solid fa-flask text-[#67B34A] text-base"></i>
                      <span class="block text-[#031827] dark:text-white">{{ $isRtl ? 'دون مواد حافظة' : 'No Preservatives' }}</span>
                    </div>
                  </div>

                </div>
              </div>
            </div>

            <!-- 6. ACCORDION: CLINICAL MONOGRAPH & SUPPLY SPECIFICATIONS -->
            <div id="accordion-specs" class="bz-collapsible-item rounded-3xl bg-white dark:bg-[#062B49] border border-[#0A4F78]/20 transition-all duration-300 overflow-hidden">
              <button type="button" onclick="toggleProductDetailItem('accordion-specs')" 
                class="w-full p-5 sm:p-7 flex justify-between items-center text-left rtl:text-right cursor-pointer select-none group">
                <div class="flex items-center gap-4">
                  <div class="w-12 h-12 rounded-2xl bg-indigo-500/15 text-indigo-500 flex items-center justify-center text-xl shrink-0 group-hover:scale-105 transition-transform">
                    <i class="fa-solid fa-certificate"></i>
                  </div>
                  <div>
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                      <span class="text-[10px] font-mono uppercase px-2 py-0.5 rounded bg-indigo-500/15 text-indigo-600 dark:text-indigo-400 font-extrabold">SECTION 06</span>
                      <span class="text-[11px] font-bold text-[#031827]/60 dark:text-[#F6F5EF]/60 uppercase tracking-widest">{{ $isRtl ? 'بيانات التوثيق والضمان' : 'Batch Certification' }}</span>
                    </div>
                    <h3 class="text-lg sm:text-xl font-black text-[#031827] dark:text-[#F6F5EF]">
                      {{ $isRtl ? 'المواصفات الفنية، التعبئة وضمان النقاء' : 'Monograph & Supply Specifications' }}
                    </h3>
                  </div>
                </div>

                <div class="flex items-center gap-3">
                  <span class="hidden sm:inline-block px-3 py-1 rounded-full bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 text-xs font-bold font-mono">
                    {{ $isRtl ? 'بيانات المنتج' : 'Technical Data' }}
                  </span>
                  <span class="bz-chevron-icon w-8 h-8 rounded-full bg-[#0A4F78]/5 dark:bg-white/5 flex items-center justify-center text-sm text-[#0A4F78] dark:text-[#2A8FC2]">
                    <i class="fa-solid fa-chevron-down"></i>
                  </span>
                </div>
              </button>

              <div class="bz-collapsible-grid">
                <div class="bz-collapsible-inner p-5 sm:p-8 pt-0 border-t border-[#0A4F78]/10 dark:border-[#0A4F78]/20 space-y-6">
                  
                  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-xs font-medium">
                    <div class="p-3.5 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 flex justify-between items-center">
                      <span class="text-[#031827]/70 dark:text-[#F6F5EF]/70">{{ $isRtl ? 'حجم العبوة والجرعات:' : 'Supply Volume:' }}</span>
                      <span class="font-bold text-[#031827] dark:text-white">{{ $packageSize }}</span>
                    </div>
                    <div class="p-3.5 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 flex justify-between items-center">
                      <span class="text-[#031827]/70 dark:text-[#F6F5EF]/70">{{ $isRtl ? 'رمز المنتج SKU:' : 'Product SKU:' }}</span>
                      <span class="font-bold text-[#0A4F78] dark:text-[#2A8FC2] font-mono">{{ $pSku }}</span>
                    </div>
                    <div class="p-3.5 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 flex justify-between items-center">
                      <span class="text-[#031827]/70 dark:text-[#F6F5EF]/70">{{ $isRtl ? 'جهة التصنيع المعتمدة:' : 'Facility Standards:' }}</span>
                      <span class="font-bold text-[#67B34A]">cGMP & FDA Registered</span>
                    </div>
                    <div class="p-3.5 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 flex justify-between items-center">
                      <span class="text-[#031827]/70 dark:text-[#F6F5EF]/70">{{ $isRtl ? 'صيغة الكبسولة:' : 'Delivery Matrix:' }}</span>
                      <span class="font-bold text-[#031827] dark:text-white">{{ $isRtl ? 'كبسولات نباتية معوية' : 'Enteric Veg Capsule' }}</span>
                    </div>
                    <div class="p-3.5 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 flex justify-between items-center">
                      <span class="text-[#031827]/70 dark:text-[#F6F5EF]/70">{{ $isRtl ? 'العمر التخزيني:' : 'Shelf Life Stability:' }}</span>
                      <span class="font-bold text-[#031827] dark:text-white">24 Months Unopened</span>
                    </div>
                    <div class="p-3.5 rounded-xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/15 flex justify-between items-center">
                      <span class="text-[#031827]/70 dark:text-[#F6F5EF]/70">{{ $isRtl ? 'ضمان الرضا الطبي:' : 'Satisfaction Guarantee:' }}</span>
                      <span class="font-bold text-[#67B34A]">{{ $isRtl ? '30 يوماً استرداد كامل' : '30-Day Clinical Refund' }}</span>
                    </div>
                  </div>

                </div>
              </div>
            </div>

          </div>
        </div>

        <!-- ========================================================================= -->
        <!-- 3. COMPLEMENTARY PROTOCOL FORMULATIONS (RELATED PRODUCTS)                 -->
        <!-- ========================================================================= -->
        @if(isset($relatedProducts) && count($relatedProducts) > 0)
          <div class="space-y-6 pt-10 border-t border-[#0A4F78]/20">
            <div class="flex justify-between items-end">
              <div>
                <span class="text-[11px] font-extrabold uppercase tracking-[0.3em] text-[#0A4F78] dark:text-[#2A8FC2] block mb-1">
                  {{ $isRtl ? 'التكامل الحيوي' : 'SYNERGISTIC LONGEVITY PROTOCOLS' }}
                </span>
                <h3 class="text-2xl sm:text-3xl font-black text-[#031827] dark:text-[#F6F5EF]">
                  {{ $isRtl ? 'تركيبات متكاملة تعزز النتائج' : 'Complementary Formulations' }}
                </h3>
              </div>
              <a href="{{ route('customer.shop') }}" class="text-xs font-extrabold uppercase tracking-widest text-[#0A4F78] dark:text-[#2A8FC2] hover:text-[#67B34A] transition-colors">
                {{ $isRtl ? 'عرض المتجر كاملاً' : 'EXPLORE ALL' }} <i class="fa-solid fa-arrow-right rtl:rotate-180 mr-1 ml-1"></i>
              </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8">
              @foreach($relatedProducts as $relProduct)
                @php
                  $relIsObj = is_object($relProduct);
                  $relSlug = $relIsObj ? ($relProduct->slug ?? '') : ($relProduct['slug'] ?? '');
                  $relName = $relIsObj 
                      ? ($isRtl && !empty($relProduct->name_ar) ? $relProduct->name_ar : ($relProduct->name_en ?? $relProduct->name ?? ''))
                      : ($isRtl && !empty($relProduct['name_ar']) ? $relProduct['name_ar'] : ($relProduct['name_en'] ?? $relProduct['name'] ?? ''));
                  $relImg = $relIsObj 
                      ? ($relProduct->primary_image_url ?? asset('assets/products/blue-cell.webp'))
                      : asset($relProduct['image'] ?? 'assets/products/blue-cell.webp');
                  $relPrice = $relIsObj ? ($relProduct->price ?? 79) : ($relProduct['price'] ?? 79);
                  $relCategory = $relIsObj ? ($relProduct->category?->name ?? 'LONGEVITY') : ($relProduct['category_en'] ?? 'LONGEVITY');
                @endphp
                <div class="rounded-3xl bg-white dark:bg-[#062B49] border border-[#0A4F78]/15 overflow-hidden shadow-xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between group">
                  <div>
                    <a href="{{ route('customer.product.show', $relSlug) }}" class="block aspect-square relative overflow-hidden bg-[#031827]">
                      <img src="{{ $relImg }}" alt="{{ $relName }}" 
                        onerror="this.onerror=null; this.src='{{ asset('assets/products/blue-cell.webp') }}';"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" />
                      <span class="absolute top-4 left-4 rtl:left-auto rtl:right-4 text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded-full bg-white/90 dark:bg-[#031827]/90 text-[#0A4F78] dark:text-[#2A8FC2] backdrop-blur-md">
                        {{ $relCategory }}
                      </span>
                    </a>

                    <div class="p-6 space-y-2">
                      <h4 class="text-lg font-black text-[#031827] dark:text-[#F6F5EF]">
                        <a href="{{ route('customer.product.show', $relSlug) }}" class="hover:text-primary transition-colors">
                          {{ $relName }}
                        </a>
                      </h4>
                      <div class="text-xl font-black text-[#0A4F78] dark:text-[#2A8FC2]">
                        @currency($relPrice)
                      </div>
                    </div>
                  </div>

                  <div class="p-6 pt-0">
                    <a href="{{ route('customer.product.show', $relSlug) }}" class="block w-full py-3 text-center text-xs font-extrabold uppercase tracking-wider rounded-xl border border-[#0A4F78]/30 dark:border-white/30 text-[#031827] dark:text-white hover:bg-[#0A4F78] hover:text-white hover:border-[#0A4F78] transition-all">
                      {{ $isRtl ? 'عرض ملف المستحضر' : 'VIEW CLINICAL FILE' }}
                    </a>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        @endif

      </div>
    </div>

    <!-- Product Detail Script with Smooth Collapsible Controller -->
    <script>
      let currentDetailQty = 1;

      function updateQty(delta) {
        currentDetailQty = Math.max(1, currentDetailQty + delta);
        const qtyEl = document.getElementById('detail-qty-val');
        if (qtyEl) qtyEl.textContent = currentDetailQty;
      }

      function switchMainImage(src, btn) {
        const mainImg = document.getElementById('product-main-img');
        if (mainImg) {
          mainImg.style.opacity = '0.3';
          setTimeout(() => {
            mainImg.src = src;
            mainImg.style.opacity = '1';
          }, 150);
        }
        document.querySelectorAll('.aspect-square button, .flex button').forEach(b => {
          b.classList.remove('border-[#67B34A]', 'ring-2', 'ring-[#67B34A]/30');
        });
        if (btn) {
          btn.classList.add('border-[#67B34A]', 'ring-2', 'ring-[#67B34A]/30');
        }
      }

      function selectProtocolVariant(months, price, btn) {
        document.querySelectorAll('.protocol-variant-btn').forEach(b => {
          b.classList.remove('border-[#67B34A]', 'bg-[#67B34A]/5');
          b.classList.add('border-[#0A4F78]/20');
        });
        if (btn) {
          btn.classList.remove('border-[#0A4F78]/20');
          btn.classList.add('border-[#67B34A]', 'bg-[#67B34A]/5');
        }
        currentDetailQty = months;
        const qtyEl = document.getElementById('detail-qty-val');
        if (qtyEl) qtyEl.textContent = currentDetailQty;
      }

      // ==========================================
      // ACCORDION COLLAPSE CONTROLLER
      // ==========================================
      function toggleProductDetailItem(accordionId) {
        const item = document.getElementById(accordionId);
        if (!item) return;
        item.classList.toggle('is-open');
      }

      function expandAllProductDetails() {
        document.querySelectorAll('.bz-collapsible-item').forEach(item => {
          item.classList.add('is-open');
        });
      }

      function collapseAllProductDetails() {
        document.querySelectorAll('.bz-collapsible-item').forEach(item => {
          item.classList.remove('is-open');
        });
      }

      function openAndScrollToAccordion(accordionId) {
        const item = document.getElementById(accordionId);
        if (!item) return;

        // Open item if closed
        item.classList.add('is-open');

        // Smooth scroll to the element
        const yOffset = -100; 
        const y = item.getBoundingClientRect().top + window.pageYOffset + yOffset;
        window.scrollTo({ top: y, behavior: 'smooth' });

        // Highlight tab pill
        document.querySelectorAll('.filter-tab-pill').forEach(pill => {
          pill.classList.remove('active');
        });
        if (event && event.currentTarget) {
          event.currentTarget.classList.add('active');
        }
      }
    </script>
</x-layouts.customer>
