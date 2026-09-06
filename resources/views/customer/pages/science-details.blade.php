<x-layouts.customer :title="$product instanceof \App\Models\Product ? $product->name . ' — ' . __('app.brand_name') . ' SCIENCE' : (data_get($product, 'name_en') . ' — SCIENCE')" :description="'Clinical pharmacology, scientific validation, and molecular mechanism of action for ' . (data_get($product, 'name_en'))">
@php
    $isRtl = app()->getLocale() === 'ar';
    $name = $isRtl ? (data_get($product, 'name_ar') ?: data_get($product, 'name_en')) : data_get($product, 'name_en');
    $tagline = $isRtl ? (data_get($product, 'tagline_ar') ?: data_get($product, 'tagline_en')) : (data_get($product, 'tagline_en') ?: data_get($product, 'short_description_en'));
    $slug = data_get($product, 'slug');
    $sku = data_get($product, 'sku', 'BZ-PROT-001');
    $price = (float) data_get($product, 'price', 0);
    $rating = (float) data_get($product, 'rating', 4.9);
    $reviewsCount = (int) data_get($product, 'reviews_count', 128);
    $size = data_get($product, 'product_size', ($isRtl ? '60 كبسولة نباتية' : '60 Veg Capsules'));

    // Category
    $catObj = data_get($product, 'category');
    $categoryName = $catObj 
        ? ($isRtl ? data_get($catObj, 'name_ar') : data_get($catObj, 'name_en'))
        : (data_get($product, 'category_en') ?: ($isRtl ? 'تركيبة طول العمر' : 'Longevity Formulation'));

    // Image
    $rawImage = data_get($product, 'image');
    if ($product instanceof \App\Models\Product) {
        $imageUrl = $product->primary_image_url;
    } else {
        $imageUrl = $rawImage ? (str_starts_with($rawImage, 'http') ? $rawImage : asset(ltrim($rawImage, '/'))) : asset('assets/products/blue-mind.jpg');
    }

    // Science description
    $scienceDesc = $isRtl ? data_get($product, 'science_ar') : data_get($product, 'science_en');
    if (empty($scienceDesc)) {
        $scienceDesc = data_get($product, 'science_en') ?: data_get($product, 'description_en');
    }

    // Short description
    $shortDesc = $isRtl ? (data_get($product, 'short_description_ar') ?: data_get($product, 'short_description_en')) : data_get($product, 'short_description_en');

    // Clinical mechanism
    $clinicalMech = data_get($product, 'clinical_mechanism');
    if (empty($clinicalMech)) {
        $clinicalMech = data_get($product, 'professional_info.clinical_mechanism');
    }
    if (empty($clinicalMech)) {
        $clinicalMech = $isRtl 
            ? 'تستهدف هذه التركيبة تنشيط مسارات الاستقلاب الخلوي وتعزيز كفاءة الميتوكوندريا مع توفير حماية حيوية ضد الإجهاد التأكسدي.' 
            : 'Targeted biochemical mechanism modulating cellular metabolic pathways, optimizing mitochondrial ATP synthesis, and shielding against oxidative stress.';
    }

    // Formula details
    $formulaDetails = data_get($product, 'formula_details');
    if (empty($formulaDetails)) {
        $formulaDetails = data_get($product, 'professional_info.formula_details');
    }

    // Benefits
    $benefits = $isRtl ? data_get($product, 'benefits_ar') : data_get($product, 'benefits_en');
    if (!is_array($benefits) && is_string($benefits)) {
        $benefits = json_decode($benefits, true) ?: [];
    }
    if (empty($benefits)) {
        $benefits = data_get($product, 'benefits_en') ?: [
            $isRtl ? 'دعم حيوي معتمد سريرياً' : 'Clinically validated biomarker support',
            $isRtl ? 'امتصاص فائق التوافر الحيوي' : 'Enhanced bioavailability matrix',
            $isRtl ? 'حماية خلوية مستدامة' : 'Sustained cellular protection'
        ];
    }

    // Ingredients
    $ingredients = data_get($product, 'ingredients') ?: [];
    if (!is_array($ingredients) && is_string($ingredients)) {
        $ingredients = json_decode($ingredients, true) ?: [];
    }

    // Usage & Protocol
    $usage = $isRtl ? (data_get($product, 'usage_ar') ?: data_get($product, 'usage_en')) : data_get($product, 'usage_en');
    if (empty($usage)) {
        $usage = $isRtl ? 'تناول كبسولتين يومياً كل صباح مع كوب ماء ومصدر دهون صحية كزيت الزيتون البكر الممتاز.' : 'Take 2 capsules daily every morning with 250ml mineral water alongside healthy fats.';
    }

    // Contraindications & Warnings
    $contraindications = data_get($product, 'contraindications') ?: data_get($product, 'professional_info.contraindications');
    $warnings = data_get($product, 'warnings') ?: data_get($product, 'professional_info.warnings');
    if (empty($warnings)) {
        $warnings = $isRtl ? 'يُحفظ بعيداً عن متناول الأطفال في مكان بارد وجاف. استشر طبيبك في حال تناول أدوية سيولة الدم أو أثناء الحمل.' : 'Keep out of reach of children. Store in a cool, dry place. Consult your physician if taking blood thinners or if pregnant.';
    }
@endphp

    <div class="py-8 sm:py-12 bg-[#F6F5EF] dark:bg-[#031827] min-h-screen transition-colors">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12 sm:space-y-16">

        <!-- 1. BREADCRUMB NAVIGATION -->
        <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-xs font-bold text-[#031827]/60 dark:text-[#F6F5EF]/60">
          <a href="{{ route('customer.home') }}" class="hover:text-[#0A4F78] dark:hover:text-[#2A8FC2] transition-colors">
            {{ $isRtl ? 'الرئيسية' : 'Home' }}
          </a>
          <span>/</span>
          <a href="{{ route('customer.shop') }}" class="hover:text-[#0A4F78] dark:hover:text-[#2A8FC2] transition-colors">
            {{ $isRtl ? 'المنتجات' : 'Products' }}
          </a>
          <span>/</span>
          <a href="{{ route('customer.pages.science') }}" class="hover:text-[#0A4F78] dark:hover:text-[#2A8FC2] transition-colors">
            {{ $isRtl ? 'علوم بلوزون' : 'Our Science' }}
          </a>
          <span>/</span>
          <span class="text-[#0A4F78] dark:text-[#2A8FC2] font-black">{{ $name }}</span>
        </nav>

        <!-- 2. SCIENCE HERO: PRODUCT OVERVIEW & CLINICAL HIGHLIGHTS -->
        <section class="bg-white dark:bg-[#062B49] rounded-3xl p-6 sm:p-12 border border-[#0A4F78]/15 dark:border-[#0A4F78]/30 shadow-xl transition-colors relative overflow-hidden">
          <div class="absolute -top-32 -right-32 w-80 h-80 bg-[#2A8FC2]/10 dark:bg-[#2A8FC2]/15 rounded-full blur-3xl pointer-events-none"></div>
          <div class="absolute -bottom-32 -left-32 w-80 h-80 bg-[#67B34A]/10 rounded-full blur-3xl pointer-events-none"></div>

          <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center relative z-10">
            
            <!-- Left: High-Res Medical Visual & Certification Badges -->
            <div class="lg:col-span-5 flex flex-col items-center justify-center">
              <div class="relative w-full max-w-sm aspect-square bg-[#F6F5EF] dark:bg-[#031827]/70 rounded-3xl border border-[#0A4F78]/20 dark:border-[#2A8FC2]/30 p-8 shadow-inner flex items-center justify-center group overflow-hidden">
                <!-- Precision Blueprint Rings -->
                <svg class="absolute inset-0 w-full h-full opacity-20 dark:opacity-30 pointer-events-none" viewBox="0 0 400 400" fill="none">
                  <circle cx="200" cy="200" r="170" stroke="#2A8FC2" stroke-width="1" stroke-dasharray="4 4"/>
                  <circle cx="200" cy="200" r="120" stroke="#2A8FC2" stroke-width="1"/>
                  <line x1="20" y1="200" x2="380" y2="200" stroke="#2A8FC2" stroke-width="0.5"/>
                  <line x1="200" y1="20" x2="200" y2="380" stroke="#2A8FC2" stroke-width="0.5"/>
                </svg>

                <img
                  src="{{ $imageUrl }}"
                  alt="{{ $name }}"
                  onerror="this.onerror=null; this.src='{{ asset('assets/products/blue-mind.jpg') }}';"
                  class="w-4/5 h-4/5 object-contain relative z-10 group-hover:scale-108 transition-transform duration-700 filter drop-shadow-2xl"
                />

                <div class="absolute top-4 left-4 px-3 py-1.5 rounded-xl bg-white/90 dark:bg-[#031827]/90 border border-[#0A4F78]/20 dark:border-[#2A8FC2]/40 text-[10px] font-black uppercase tracking-wider text-[#0A4F78] dark:text-[#2A8FC2] backdrop-blur-md shadow-sm">
                  HPLC Verified ✓
                </div>
                <div class="absolute bottom-4 right-4 px-3 py-1.5 rounded-xl bg-white/90 dark:bg-[#031827]/90 border border-[#67B34A]/30 dark:border-[#67B34A]/50 text-[10px] font-black uppercase tracking-wider text-[#589c3e] dark:text-[#67B34A] backdrop-blur-md shadow-sm">
                  100% Bio-Identical
                </div>
              </div>

              <!-- Quick Meta Strip -->
              <div class="w-full max-w-sm mt-4 grid grid-cols-2 gap-2 text-center text-xs font-bold">
                <div class="p-2.5 rounded-xl bg-[#F6F5EF] dark:bg-[#031827]/50 border border-[#0A4F78]/10 dark:border-white/10 text-[#031827] dark:text-[#F6F5EF]">
                  <span class="block text-[10px] uppercase font-mono text-[#031827]/60 dark:text-[#F6F5EF]/60">{{ $isRtl ? 'البروتوكول' : 'PROTOCOL SIZE' }}</span>
                  <span>{{ $size }}</span>
                </div>
                <div class="p-2.5 rounded-xl bg-[#F6F5EF] dark:bg-[#031827]/50 border border-[#0A4F78]/10 dark:border-white/10 text-[#031827] dark:text-[#F6F5EF]">
                  <span class="block text-[10px] uppercase font-mono text-[#031827]/60 dark:text-[#F6F5EF]/60">{{ $isRtl ? 'الرمز التعريفي' : 'FORMULATION SKU' }}</span>
                  <span class="font-mono text-[#0A4F78] dark:text-[#2A8FC2]">{{ $sku }}</span>
                </div>
              </div>
            </div>

            <!-- Right: Clinical Dossier Header -->
            <div class="lg:col-span-7 space-y-6">
              <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#0A4F78]/10 dark:bg-[#2A8FC2]/20 border border-[#0A4F78]/25 dark:border-[#2A8FC2]/40 text-[#0A4F78] dark:text-[#2A8FC2] text-xs font-black uppercase tracking-[0.25em]">
                <i class="fa-solid fa-flask-vial"></i>
                <span>{{ $categoryName }} — {{ $isRtl ? 'الملف العلمي السريري' : 'SCIENTIFIC DOSSIER' }}</span>
              </div>

              <div>
                <h1 class="text-3xl sm:text-5xl font-black text-[#031827] dark:text-white tracking-tight leading-tight">
                  {{ $name }}
                </h1>
                <p class="text-base sm:text-lg font-bold text-[#0A4F78] dark:text-[#2A8FC2] mt-1 tracking-wide">
                  {{ $tagline }}
                </p>
              </div>

              @if($shortDesc)
                <p class="text-sm text-[#031827]/80 dark:text-[#F6F5EF]/80 font-medium leading-relaxed">
                  {{ $shortDesc }}
                </p>
              @endif

              <!-- Scientific Abstract Box -->
              <div class="p-5 rounded-2xl bg-[#F6F5EF] dark:bg-[#031827]/60 border border-[#0A4F78]/15 dark:border-[#2A8FC2]/20 space-y-2">
                <span class="block text-xs font-black uppercase tracking-widest text-[#0A4F78] dark:text-[#7EA5B8]">
                  {{ $isRtl ? 'الأساس البيولوجي لطول العمر' : 'BIOLOGICAL LONGEVITY FOUNDATION' }}
                </span>
                <p class="text-xs sm:text-sm text-[#031827] dark:text-[#E8DCC4] leading-relaxed font-medium">
                  {{ $scienceDesc }}
                </p>
              </div>

              <!-- Quick Actions: Add to Cart or View Shop -->
              <div class="pt-4 border-t border-[#0A4F78]/15 dark:border-[#0A4F78]/30 flex flex-wrap items-center gap-4">
                @if($price > 0)
                  <div>
                    <span class="block text-[10px] font-mono uppercase text-[#031827]/60 dark:text-[#F6F5EF]/60">{{ $isRtl ? 'السعر السريري' : 'CLINICAL PRICE' }}</span>
                    <span class="text-2xl sm:text-3xl font-black text-[#0A4F78] dark:text-[#2A8FC2]">${{ number_format($price, 2) }}</span>
                  </div>
                  <button onclick="if(window.BLUEZONE_CART){BLUEZONE_CART.add('{{ $slug }}', 1);}" class="px-8 py-3.5 bg-[#2A8FC2] hover:bg-[#0A4F78] text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all shadow-xl btn-sheen cursor-pointer flex items-center gap-2">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span>{{ $isRtl ? 'أضف للبروتوكول' : 'ADD TO PROTOCOL' }}</span>
                  </button>
                @endif
                <a href="{{ route('customer.product.show', $slug) }}" class="px-6 py-3.5 rounded-xl border border-[#0A4F78]/30 dark:border-[#2A8FC2]/40 hover:border-[#0A4F78] dark:hover:border-[#2A8FC2] text-[#0A4F78] dark:text-white text-xs font-extrabold uppercase tracking-widest transition-colors flex items-center gap-2">
                  <span>{{ $isRtl ? 'عرض صفحة المنتج' : 'VIEW PRODUCT PAGE' }}</span>
                  <i class="fa-solid {{ $isRtl ? 'fa-arrow-left' : 'fa-arrow-right' }}"></i>
                </a>
              </div>

            </div>
          </div>
        </section>

        <!-- 3. BIOCHEMICAL MECHANISM OF ACTION -->
        <section class="space-y-6">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-[#2A8FC2]/15 text-[#0A4F78] dark:text-[#2A8FC2] flex items-center justify-center text-lg">
              <i class="fa-solid fa-dna"></i>
            </div>
            <div>
              <span class="text-[10px] font-mono font-black uppercase tracking-widest text-[#0A4F78] dark:text-[#2A8FC2] block">
                {{ $isRtl ? 'المسار الكيميائي الحيوي' : 'BIOCHEMICAL PATHWAY' }}
              </span>
              <h2 class="text-2xl sm:text-3xl font-black text-[#031827] dark:text-white tracking-tight">
                {{ $isRtl ? 'آلية التأثير الإكلينيكي على الخلايا' : 'Cellular Mechanism of Action' }}
              </h2>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
            <div class="md:col-span-8 p-6 sm:p-8 rounded-3xl bg-white dark:bg-[#062B49] border border-[#0A4F78]/15 dark:border-[#0A4F78]/30 shadow-md space-y-4">
              <span class="text-xs font-mono font-bold text-[#589c3e] dark:text-[#67B34A] uppercase block">
                {{ $isRtl ? 'التقرير المخبري والسريري' : 'CLINICAL PHARMACOLOGICAL DOSSIER' }}
              </span>
              <p class="text-sm sm:text-base text-[#031827]/85 dark:text-[#F6F5EF]/85 leading-relaxed font-medium">
                {{ $clinicalMech }}
              </p>
              @if($formulaDetails)
                <div class="pt-4 border-t border-[#0A4F78]/10 dark:border-white/10 space-y-2">
                  <span class="block text-xs font-bold text-[#0A4F78] dark:text-[#2A8FC2] uppercase">
                    {{ $isRtl ? 'معايير النقاء والاستخلاص' : 'EXTRACTION & PURITY SPECIFICATION' }}:
                  </span>
                  <p class="text-xs text-[#031827]/75 dark:text-white/75 font-mono leading-relaxed">
                    {{ $formulaDetails }}
                  </p>
                </div>
              @endif
            </div>

            <!-- Molecular Validation Badges Card -->
            <div class="md:col-span-4 p-6 sm:p-8 rounded-3xl bg-white dark:bg-[#062B49] border border-[#0A4F78]/15 dark:border-[#0A4F78]/30 shadow-md space-y-4 flex flex-col justify-between">
              <div>
                <span class="text-xs font-mono font-bold text-[#0A4F78] dark:text-[#2A8FC2] uppercase block">
                  {{ $isRtl ? 'شهادات التحقق الجزيئي' : 'MOLECULAR STANDARDS' }}
                </span>
                <ul class="mt-4 space-y-3 text-xs font-semibold text-[#031827]/80 dark:text-[#F6F5EF]/80">
                  <li class="flex items-center gap-2">
                    <span class="text-[#589c3e] dark:text-[#67B34A]">✓</span>
                    <span>{{ $isRtl ? 'مطابقة الهوية الجزيئية بنسبة 100%' : '100% Bio-Identical Structure' }}</span>
                  </li>
                  <li class="flex items-center gap-2">
                    <span class="text-[#589c3e] dark:text-[#67B34A]">✓</span>
                    <span>{{ $isRtl ? 'خالٍ تماماً من الكافيين والمنشطات الاصطناعية' : 'Zero Synthetic Fillers & Caffeine' }}</span>
                  </li>
                  <li class="flex items-center gap-2">
                    <span class="text-[#589c3e] dark:text-[#67B34A]">✓</span>
                    <span>{{ $isRtl ? 'مصنّع وفق معايير cGMP الأوروبية' : 'cGMP European Certified Batch' }}</span>
                  </li>
                  <li class="flex items-center gap-2">
                    <span class="text-[#589c3e] dark:text-[#67B34A]">✓</span>
                    <span>{{ $isRtl ? 'فحص المعادن الثقيلة والملوثات' : 'Third-Party Heavy Metal Screened' }}</span>
                  </li>
                </ul>
              </div>

              <div class="p-3 rounded-xl bg-[#F6F5EF] dark:bg-[#031827]/50 border border-[#0A4F78]/10 text-center">
                <span class="text-[11px] font-mono text-[#589c3e] dark:text-[#67B34A] font-bold">
                  {{ $isRtl ? 'معتمد للاستخدام السريري المستمر' : 'CLINICALLY TOLERATED DAILY' }}
                </span>
              </div>
            </div>
          </div>
        </section>

        <!-- 4. STANDARDIZED BIOACTIVES & CLINICAL INGREDIENTS -->
        @if(!empty($ingredients))
        <section class="space-y-6">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-[#67B34A]/15 text-[#589c3e] dark:text-[#67B34A] flex items-center justify-center text-lg">
              <i class="fa-solid fa-leaf"></i>
            </div>
            <div>
              <span class="text-[10px] font-mono font-black uppercase tracking-widest text-[#589c3e] dark:text-[#67B34A] block">
                {{ $isRtl ? 'المكونات النشطة الموحدة' : 'STANDARDIZED BOTANICALS' }}
              </span>
              <h2 class="text-2xl sm:text-3xl font-black text-[#031827] dark:text-white tracking-tight">
                {{ $isRtl ? 'المعايرة الحيوية والجرعات السريرية' : 'Standardized Active Ingredients' }}
              </h2>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($ingredients as $ing)
              @php
                $ingName = is_array($ing) ? ($isRtl && !empty($ing['name_ar']) ? $ing['name_ar'] : ($ing['name_en'] ?? $ing['name'] ?? 'Bioactive Compound')) : (string)$ing;
                $ingDose = is_array($ing) ? ($ing['dose'] ?? $ing['amount'] ?? '') : '';
              @endphp
              <div class="p-5 rounded-2xl bg-white dark:bg-[#062B49] border border-[#0A4F78]/15 dark:border-[#0A4F78]/30 shadow-sm flex items-center justify-between gap-4">
                <div class="space-y-1">
                  <span class="text-xs font-black text-[#031827] dark:text-white block">
                    {{ $ingName }}
                  </span>
                  <span class="text-[10px] font-mono text-[#0A4F78] dark:text-[#2A8FC2]">
                    {{ $isRtl ? 'مستخلص نقي معاير' : 'Standardized Pure Fraction' }}
                  </span>
                </div>
                @if($ingDose)
                  <span class="px-3 py-1 rounded-lg bg-[#F6F5EF] dark:bg-[#031827] font-mono font-bold text-xs text-[#589c3e] dark:text-[#67B34A] border border-[#67B34A]/20">
                    {{ $ingDose }}
                  </span>
                @endif
              </div>
            @endforeach
          </div>
        </section>
        @endif

        <!-- 5. VERIFIED BENEFITS & CLINICAL PROTOCOL -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
          
          <!-- Benefits -->
          <div class="lg:col-span-7 space-y-6">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl bg-[#0A4F78]/15 text-[#0A4F78] dark:text-[#2A8FC2] flex items-center justify-center text-lg">
                <i class="fa-solid fa-chart-line"></i>
              </div>
              <div>
                <span class="text-[10px] font-mono font-black uppercase tracking-widest text-[#0A4F78] dark:text-[#2A8FC2] block">
                  {{ $isRtl ? 'المؤشرات الحيوية' : 'BIOMARKER IMPACT' }}
                </span>
                <h3 class="text-xl sm:text-2xl font-black text-[#031827] dark:text-white tracking-tight">
                  {{ $isRtl ? 'الفوائد والنتائج السريرية المثبتة' : 'Documented Clinical Benefits' }}
                </h3>
              </div>
            </div>

            <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-[#062B49] border border-[#0A4F78]/15 dark:border-[#0A4F78]/30 shadow-md space-y-4">
              <div class="space-y-3">
                @foreach($benefits as $b)
                  <div class="flex items-start gap-3">
                    <span class="w-5 h-5 rounded-full bg-[#67B34A]/20 text-[#589c3e] dark:text-[#67B34A] flex items-center justify-center text-xs font-black shrink-0 mt-0.5">✓</span>
                    <span class="text-xs sm:text-sm text-[#031827]/80 dark:text-[#F6F5EF]/80 font-medium leading-relaxed">{{ $b }}</span>
                  </div>
                @endforeach
              </div>
            </div>
          </div>

          <!-- Protocol & Safety -->
          <div class="lg:col-span-5 space-y-6">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl bg-amber-500/15 text-amber-600 dark:text-amber-400 flex items-center justify-center text-lg">
                <i class="fa-solid fa-shield-halved"></i>
              </div>
              <div>
                <span class="text-[10px] font-mono font-black uppercase tracking-widest text-amber-600 dark:text-amber-400 block">
                  {{ $isRtl ? 'البروتوكول والاستخدام' : 'DOSAGE & SAFETY' }}
                </span>
                <h3 class="text-xl sm:text-2xl font-black text-[#031827] dark:text-white tracking-tight">
                  {{ $isRtl ? 'بروتوكول الاستخدام الطبي' : 'Clinical Usage Protocol' }}
                </h3>
              </div>
            </div>

            <div class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-[#062B49] border border-[#0A4F78]/15 dark:border-[#0A4F78]/30 shadow-md space-y-4">
              <div class="space-y-2">
                <span class="block text-[11px] font-bold text-[#0A4F78] dark:text-[#2A8FC2] uppercase">
                  {{ $isRtl ? 'طريقة الاستخدام الموصى بها' : 'RECOMMENDED DOSAGE' }}:
                </span>
                <p class="text-xs sm:text-sm text-[#031827]/80 dark:text-[#F6F5EF]/80 leading-relaxed font-medium">
                  {{ $usage }}
                </p>
              </div>

              @if($contraindications)
              <div class="pt-4 border-t border-[#0A4F78]/10 dark:border-white/10 space-y-1">
                <span class="block text-[10px] font-mono font-bold text-amber-600 dark:text-amber-400 uppercase">
                  {{ $isRtl ? 'موانع الاستعمال' : 'CONTRAINDICATIONS' }}:
                </span>
                <p class="text-xs text-[#031827]/70 dark:text-white/70 leading-relaxed">
                  {{ $contraindications }}
                </p>
              </div>
              @endif

              <div class="pt-4 border-t border-[#0A4F78]/10 dark:border-white/10 space-y-1">
                <span class="block text-[10px] font-mono font-bold text-[#031827]/60 dark:text-[#F6F5EF]/60 uppercase">
                  {{ $isRtl ? 'إرشادات السلامة' : 'SAFETY GUIDELINE' }}:
                </span>
                <p class="text-[11px] text-[#031827]/60 dark:text-white/60 leading-relaxed">
                  {{ $warnings }}
                </p>
              </div>
            </div>
          </div>

        </div>

        <!-- 6. CROSS-NAVIGATION: EXPLORE OTHER FORMULATIONS' SCIENCE DOSSIERS -->
        @if(isset($relatedProducts) && count($relatedProducts) > 0)
        <section class="space-y-6 pt-6 border-t border-[#0A4F78]/15 dark:border-[#0A4F78]/30">
          <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div class="space-y-1">
              <span class="text-xs font-mono font-black uppercase tracking-widest text-[#0A4F78] dark:text-[#2A8FC2]">
                {{ $isRtl ? 'ملفات علمية أخرى' : 'CROSS-FORMULATION SCIENCE' }}
              </span>
              <h2 class="text-2xl sm:text-3xl font-black text-[#031827] dark:text-white tracking-tight">
                {{ $isRtl ? 'استكشف الملفات السريرية للتركيبات الأخرى' : 'Explore Other Formulations\' Science' }}
              </h2>
            </div>
            <a href="{{ route('customer.shop') }}" class="text-xs font-black text-[#0A4F78] dark:text-[#2A8FC2] hover:underline uppercase tracking-wider flex items-center gap-1.5">
              <span>{{ $isRtl ? 'جميع المنتجات' : 'View All Products' }}</span>
              <i class="fa-solid {{ $isRtl ? 'fa-arrow-left' : 'fa-arrow-right' }}"></i>
            </a>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($relatedProducts as $rel)
              @php
                $relSlug = data_get($rel, 'slug');
                $relName = $isRtl ? (data_get($rel, 'name_ar') ?: data_get($rel, 'name_en')) : data_get($rel, 'name_en');
                $relImage = data_get($rel, 'image');
                $relImageUrl = $rel instanceof \App\Models\Product 
                    ? $rel->primary_image_url 
                    : ($relImage ? (str_starts_with($relImage, 'http') ? $relImage : asset(ltrim($relImage, '/'))) : asset('assets/products/blue-mind.jpg'));
                $relCat = data_get($rel, 'category');
                $relCatName = $relCat ? ($isRtl ? data_get($relCat, 'name_ar') : data_get($relCat, 'name_en')) : (data_get($rel, 'category_en') ?: 'Science');
              @endphp
              <div class="p-5 rounded-3xl bg-white dark:bg-[#062B49] border border-[#0A4F78]/15 dark:border-[#0A4F78]/30 shadow-md hover:shadow-xl transition-all duration-300 flex flex-col justify-between group hover:-translate-y-1">
                <div class="space-y-3">
                  <div class="aspect-square bg-[#F6F5EF] dark:bg-[#031827]/60 rounded-2xl p-4 flex items-center justify-center overflow-hidden border border-[#0A4F78]/10 dark:border-white/10">
                    <img
                      src="{{ $relImageUrl }}"
                      alt="{{ $relName }}"
                      onerror="this.onerror=null; this.src='{{ asset('assets/products/blue-mind.jpg') }}';"
                      class="max-h-36 object-contain group-hover:scale-105 transition-transform"
                    />
                  </div>
                  <div>
                    <span class="text-[9px] font-mono font-bold text-[#0A4F78] dark:text-[#2A8FC2] uppercase block">{{ $relCatName }}</span>
                    <h4 class="text-base font-black text-[#031827] dark:text-white group-hover:text-[#2A8FC2] transition-colors line-clamp-1">
                      {{ $relName }}
                    </h4>
                  </div>
                </div>

                <div class="pt-4 mt-3 border-t border-[#0A4F78]/10 dark:border-white/10">
                  <a href="{{ route('customer.science.product', $relSlug) }}" class="w-full py-2.5 rounded-xl bg-[#0A4F78]/10 dark:bg-[#0A4F78]/30 hover:bg-[#0A4F78] dark:hover:bg-[#2A8FC2] text-[#0A4F78] dark:text-white hover:text-white text-center text-xs font-black uppercase tracking-wider transition-all block">
                    {{ $isRtl ? 'ملف العلوم السريرية ←' : 'Our Science Details →' }}
                  </a>
                </div>
              </div>
            @endforeach
          </div>
        </section>
        @endif

      </div>
    </div>
</x-layouts.customer>
