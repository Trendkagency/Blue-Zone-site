    <!-- 08. BLUEMINT / BREPS (STANDALONE PREPARATIVE PROTOCOL SECTION) -->
    <section id="bluemint-preps" class="py-24 bg-[#F6F5EF] dark:bg-[#031827] text-[#031827] dark:text-white border-b border-[#0A4F78]/10 dark:border-[#0A4F78]/30 relative overflow-hidden transition-colors">
      <!-- Ambient Glows -->
      <div class="absolute -top-40 -left-40 w-[500px] h-[500px] bg-[#2A8FC2]/10 dark:bg-[#2A8FC2]/15 rounded-full blur-3xl pointer-events-none"></div>
      <div class="absolute -bottom-40 -right-40 w-[500px] h-[500px] bg-[#67B34A]/10 rounded-full blur-3xl pointer-events-none"></div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 space-y-16">
        <!-- Section Header -->
        <div class="text-center max-w-3xl mx-auto space-y-4">
          <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#2A8FC2]/15 dark:bg-[#2A8FC2]/20 text-[#0A4F78] dark:text-[#2A8FC2] text-xs font-black uppercase tracking-[0.25em] border border-[#2A8FC2]/30 backdrop-blur-md">
            {{ app()->getLocale() === 'ar' ? 'المستحضرات والبروتوكولات السريرية المستقلة' : 'CLINICAL PREPARATIONS & PROTOCOLS' }}
          </div>
          <h2 class="text-3xl sm:text-6xl font-black tracking-tight leading-tight text-[#031827] dark:text-white">
            BLUEMINT <span class="text-[#0A4F78] dark:text-[#2A8FC2]">BREPS</span>
          </h2>
          <p class="text-sm sm:text-base text-[#031827]/75 dark:text-[#E8DCC4] font-medium leading-relaxed">
            {{ app()->getLocale() === 'ar' 
               ? 'مستحضرات تحضيرية سريرية متطورة (Breps) مصممة لتأهيل الخلايا والأعصاب في مراحل بيولوجية محددة: الإدراك الصباحي، الحيوية الخلوية، ضبط الأيض، والاسترخاء العميق.' 
               : 'Specialized bio-active preparations (Breps) engineered to prime specific physiological windows: morning cognitive readiness, mitochondrial ATP respiration, metabolic buffering, and nocturnal glymphatic restoration.' }}
          </p>
        </div>

        <!-- 4 Specialized Bluemint Preparations Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          
          <!-- Brep 1: Bluemint Focus Prep (Blue Mind) -->
          <div class="bg-white dark:bg-[#062B49]/70 backdrop-blur-md rounded-3xl border border-[#0A4F78]/15 dark:border-[#2A8FC2]/30 p-6 flex flex-col justify-between hover:border-[#2A8FC2] transition-all duration-300 group hover:-translate-y-2 shadow-lg dark:shadow-xl">
            <div class="space-y-4">
              <div class="flex items-center justify-between">
                <span class="text-[9px] font-mono font-black uppercase tracking-widest px-2.5 py-1 rounded-full bg-[#2A8FC2]/15 dark:bg-[#2A8FC2]/20 text-[#0A4F78] dark:text-[#2A8FC2] border border-[#2A8FC2]/30">
                  BREP 01 • MORNING
                </span>
                <span class="text-[10px] font-bold text-[#67B34A]">★ 4.9</span>
              </div>

              <div class="aspect-square bg-[#F6F5EF] dark:bg-[#031827]/60 rounded-2xl p-4 flex items-center justify-center relative overflow-hidden border border-[#0A4F78]/10 dark:border-[#2A8FC2]/20">
                <img
                  src="{{ asset('assets/products/blue-mind.webp') }}"
                  alt="Bluemint Focus Prep"
                  onerror="this.onerror=null; this.src='{{ asset('assets/products/blue-mind.jpg') }}';"
                  width="200" height="200"
                  loading="lazy" decoding="async"
                  class="w-4/5 h-4/5 object-contain group-hover:scale-110 transition-transform duration-500"
                />
              </div>

              <div>
                <span class="text-[10px] font-mono text-[#589c3e] dark:text-[#67B34A] uppercase font-bold tracking-wider block">SYNAPTIC PRIMING</span>
                <h3 class="text-xl font-black text-[#031827] dark:text-white group-hover:text-[#2A8FC2] transition-colors">
                  Bluemint Focus Prep
                </h3>
                <p class="text-xs text-[#031827]/70 dark:text-white/70 mt-1 leading-relaxed">
                  {{ app()->getLocale() === 'ar' ? 'تأهيل الناقلات العصبية، تنشيط الأستيل كولين، ورفع سرعة المعالجة الذهنية بدون أي كافيين.' : 'Choline-rich nootropic preparation priming acetylcholine synthesis and microcapillary cerebral oxygenation.' }}
                </p>
              </div>

              <div class="space-y-1 pt-2 border-t border-[#0A4F78]/10 dark:border-white/10 text-[11px]">
                <div class="flex justify-between text-[#031827]/60 dark:text-white/60">
                  <span>{{ app()->getLocale() === 'ar' ? 'المركب الأساسي' : 'Primary Bioactive' }}:</span>
                  <span class="font-bold text-[#031827] dark:text-white">Sunflower PS 100mg</span>
                </div>
                <div class="flex justify-between text-[#031827]/60 dark:text-white/60">
                  <span>{{ app()->getLocale() === 'ar' ? 'التوقيت الموصى به' : 'Timing Protocol' }}:</span>
                  <span class="font-bold text-[#589c3e] dark:text-[#67B34A]">Morning Fasting</span>
                </div>
              </div>
            </div>

            <div class="pt-6 mt-4 border-t border-[#0A4F78]/10 dark:border-white/10 space-y-2">
              <div class="flex justify-between items-baseline">
                <span class="text-2xl font-black text-[#031827] dark:text-white">$58.00</span>
                <span class="text-xs text-[#0A4F78] dark:text-[#2A8FC2] font-semibold">60 Capsules</span>
              </div>
              <div class="grid grid-cols-2 gap-2">
                <a href="{{ route('customer.product.show', 'blue-mind') }}" class="py-2.5 text-center text-xs font-bold rounded-xl border border-[#0A4F78]/20 dark:border-white/20 hover:border-[#0A4F78] dark:hover:border-white text-[#0A4F78] dark:text-white transition-colors">
                  {{ app()->getLocale() === 'ar' ? 'الملف' : 'Monograph' }}
                </a>
                <button onclick="if(window.BLUEZONE_CART){window.BLUEZONE_CART.addItem(1, 'BLUE MIND', 58.00, '{{ asset('assets/products/blue-mind.jpg') }}', 1);}" class="py-2.5 text-center text-xs font-black uppercase rounded-xl bg-[#2A8FC2] hover:bg-[#0A4F78] text-white transition-all shadow-md cursor-pointer">
                  {{ app()->getLocale() === 'ar' ? 'أضف' : 'Add Prep' }}
                </button>
              </div>
            </div>
          </div>

          <!-- Brep 2: Bluemint Cell Prep (Blue Cell) -->
          <div class="bg-white dark:bg-[#062B49]/70 backdrop-blur-md rounded-3xl border border-[#67B34A]/25 dark:border-[#67B34A]/30 p-6 flex flex-col justify-between hover:border-[#67B34A] transition-all duration-300 group hover:-translate-y-2 shadow-lg dark:shadow-xl">
            <div class="space-y-4">
              <div class="flex items-center justify-between">
                <span class="text-[9px] font-mono font-black uppercase tracking-widest px-2.5 py-1 rounded-full bg-[#67B34A]/20 text-[#589c3e] dark:text-[#67B34A] border border-[#67B34A]/30">
                  BREP 02 • CELLULAR
                </span>
                <span class="text-[10px] font-bold text-[#67B34A]">★ 4.95</span>
              </div>

              <div class="aspect-square bg-[#F6F5EF] dark:bg-[#031827]/60 rounded-2xl p-4 flex items-center justify-center relative overflow-hidden border border-[#0A4F78]/10 dark:border-[#67B34A]/20">
                <img
                  src="{{ asset('assets/products/blue-cell.webp') }}"
                  alt="Bluemint Cell Prep"
                  onerror="this.onerror=null; this.src='{{ asset('assets/products/blue-cell.jpg') }}';"
                  width="200" height="200"
                  loading="lazy" decoding="async"
                  class="w-4/5 h-4/5 object-contain group-hover:scale-110 transition-transform duration-500"
                />
              </div>

              <div>
                <span class="text-[10px] font-mono text-[#589c3e] dark:text-[#67B34A] uppercase font-bold tracking-wider block">MITOCHONDRIAL NAD+</span>
                <h3 class="text-xl font-black text-[#031827] dark:text-white group-hover:text-[#67B34A] transition-colors">
                  Bluemint Cell Prep
                </h3>
                <p class="text-xs text-[#031827]/70 dark:text-white/70 mt-1 leading-relaxed">
                  {{ app()->getLocale() === 'ar' ? 'تجديد مخزون إنزيم NAD+ الخلوي، تفعيل بروتينات السيرتوين، وحماية الميتوكوندريا.' : 'Enzymatic NMN & resveratrol formulation accelerating cellular NAD+ recycling and ATP production.' }}
                </p>
              </div>

              <div class="space-y-1 pt-2 border-t border-[#0A4F78]/10 dark:border-white/10 text-[11px]">
                <div class="flex justify-between text-[#031827]/60 dark:text-white/60">
                  <span>{{ app()->getLocale() === 'ar' ? 'المركب الأساسي' : 'Primary Bioactive' }}:</span>
                  <span class="font-bold text-[#031827] dark:text-white">Beta-NMN 300mg</span>
                </div>
                <div class="flex justify-between text-[#031827]/60 dark:text-white/60">
                  <span>{{ app()->getLocale() === 'ar' ? 'التوقيت الموصى به' : 'Timing Protocol' }}:</span>
                  <span class="font-bold text-[#589c3e] dark:text-[#67B34A]">Pre-Breakfast</span>
                </div>
              </div>
            </div>

            <div class="pt-6 mt-4 border-t border-[#0A4F78]/10 dark:border-white/10 space-y-2">
              <div class="flex justify-between items-baseline">
                <span class="text-2xl font-black text-[#031827] dark:text-white">$64.00</span>
                <span class="text-xs text-[#589c3e] dark:text-[#67B34A] font-semibold">60 Capsules</span>
              </div>
              <div class="grid grid-cols-2 gap-2">
                <a href="{{ route('customer.product.show', 'blue-cell') }}" class="py-2.5 text-center text-xs font-bold rounded-xl border border-[#0A4F78]/20 dark:border-white/20 hover:border-[#0A4F78] dark:hover:border-white text-[#0A4F78] dark:text-white transition-colors">
                  {{ app()->getLocale() === 'ar' ? 'الملف' : 'Monograph' }}
                </a>
                <button onclick="if(window.BLUEZONE_CART){window.BLUEZONE_CART.addItem(2, 'BLUE CELL', 64.00, '{{ asset('assets/products/blue-cell.jpg') }}', 1);}" class="py-2.5 text-center text-xs font-black uppercase rounded-xl bg-[#67B34A] hover:bg-[#589c3e] text-white transition-all shadow-md cursor-pointer">
                  {{ app()->getLocale() === 'ar' ? 'أضف' : 'Add Prep' }}
                </button>
              </div>
            </div>
          </div>

          <!-- Brep 3: Bluemint Rest Prep (Blue Sleep) -->
          <div class="bg-white dark:bg-[#062B49]/70 backdrop-blur-md rounded-3xl border border-indigo-400/25 dark:border-indigo-400/30 p-6 flex flex-col justify-between hover:border-indigo-400 transition-all duration-300 group hover:-translate-y-2 shadow-lg dark:shadow-xl">
            <div class="space-y-4">
              <div class="flex items-center justify-between">
                <span class="text-[9px] font-mono font-black uppercase tracking-widest px-2.5 py-1 rounded-full bg-indigo-500/20 text-indigo-700 dark:text-indigo-300 border border-indigo-500/30">
                  BREP 03 • TWILIGHT
                </span>
                <span class="text-[10px] font-bold text-[#67B34A]">★ 4.92</span>
              </div>

              <div class="aspect-square bg-[#F6F5EF] dark:bg-[#031827]/60 rounded-2xl p-4 flex items-center justify-center relative overflow-hidden border border-[#0A4F78]/10 dark:border-indigo-500/20">
                <img
                  src="{{ asset('assets/products/blue-sleep.webp') }}"
                  alt="Bluemint Rest Prep"
                  onerror="this.onerror=null; this.src='{{ asset('assets/products/blue-sleep.jpg') }}';"
                  width="200" height="200"
                  loading="lazy" decoding="async"
                  class="w-4/5 h-4/5 object-contain group-hover:scale-110 transition-transform duration-500"
                />
              </div>

              <div>
                <span class="text-[10px] font-mono text-indigo-600 dark:text-indigo-300 uppercase font-bold tracking-wider block">CIRCADIAN RESTORATION</span>
                <h3 class="text-xl font-black text-[#031827] dark:text-white group-hover:text-indigo-400 transition-colors">
                  Bluemint Rest Prep
                </h3>
                <p class="text-xs text-[#031827]/70 dark:text-white/70 mt-1 leading-relaxed">
                  {{ app()->getLocale() === 'ar' ? 'تهيئة الجهاز العصبي لموجات دلتا العميقة، استرخاء العضلات، وإزالة السموم الدماغية ليلاً.' : 'Chelated magnesium and L-theanine elixir activating nocturnal glymphatic brain detox and slow-wave sleep.' }}
                </p>
              </div>

              <div class="space-y-1 pt-2 border-t border-[#0A4F78]/10 dark:border-white/10 text-[11px]">
                <div class="flex justify-between text-[#031827]/60 dark:text-white/60">
                  <span>{{ app()->getLocale() === 'ar' ? 'المركب الأساسي' : 'Primary Bioactive' }}:</span>
                  <span class="font-bold text-[#031827] dark:text-white">Magnesium TRAACS 200mg</span>
                </div>
                <div class="flex justify-between text-[#031827]/60 dark:text-white/60">
                  <span>{{ app()->getLocale() === 'ar' ? 'التوقيت الموصى به' : 'Timing Protocol' }}:</span>
                  <span class="font-bold text-indigo-600 dark:text-indigo-300">45m Pre-Sleep</span>
                </div>
              </div>
            </div>

            <div class="pt-6 mt-4 border-t border-[#0A4F78]/10 dark:border-white/10 space-y-2">
              <div class="flex justify-between items-baseline">
                <span class="text-2xl font-black text-[#031827] dark:text-white">$42.00</span>
                <span class="text-xs text-indigo-600 dark:text-indigo-300 font-semibold">60 Capsules</span>
              </div>
              <div class="grid grid-cols-2 gap-2">
                <a href="{{ route('customer.product.show', 'blue-sleep') }}" class="py-2.5 text-center text-xs font-bold rounded-xl border border-[#0A4F78]/20 dark:border-white/20 hover:border-[#0A4F78] dark:hover:border-white text-[#0A4F78] dark:text-white transition-colors">
                  {{ app()->getLocale() === 'ar' ? 'الملف' : 'Monograph' }}
                </a>
                <button onclick="if(window.BLUEZONE_CART){window.BLUEZONE_CART.addItem(5, 'BLUE SLEEP', 42.00, '{{ asset('assets/products/blue-sleep.jpg') }}', 1);}" class="py-2.5 text-center text-xs font-black uppercase rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white transition-all shadow-md cursor-pointer">
                  {{ app()->getLocale() === 'ar' ? 'أضف' : 'Add Prep' }}
                </button>
              </div>
            </div>
          </div>

          <!-- Brep 4: Bluemint Metabolic Prep (Blue Metabolic) -->
          <div class="bg-white dark:bg-[#062B49]/70 backdrop-blur-md rounded-3xl border border-amber-500/25 dark:border-amber-400/30 p-6 flex flex-col justify-between hover:border-amber-400 transition-all duration-300 group hover:-translate-y-2 shadow-lg dark:shadow-xl">
            <div class="space-y-4">
              <div class="flex items-center justify-between">
                <span class="text-[9px] font-mono font-black uppercase tracking-widest px-2.5 py-1 rounded-full bg-amber-500/20 text-amber-700 dark:text-amber-300 border border-amber-500/30">
                  BREP 04 • METABOLIC
                </span>
                <span class="text-[10px] font-bold text-[#67B34A]">★ 4.9</span>
              </div>

              <div class="aspect-square bg-[#F6F5EF] dark:bg-[#031827]/60 rounded-2xl p-4 flex items-center justify-center relative overflow-hidden border border-[#0A4F78]/10 dark:border-amber-500/20">
                <img
                  src="{{ asset('assets/products/blue-metabolic.webp') }}"
                  alt="Bluemint Metabolic Prep"
                  onerror="this.onerror=null; this.src='{{ asset('assets/products/blue-metabolic.jpg') }}';"
                  width="200" height="200"
                  loading="lazy" decoding="async"
                  class="w-4/5 h-4/5 object-contain group-hover:scale-110 transition-transform duration-500"
                />
              </div>

              <div>
                <span class="text-[10px] font-mono text-amber-600 dark:text-amber-300 uppercase font-bold tracking-wider block">AMPK & AUTOPHAGY</span>
                <h3 class="text-xl font-black text-[#031827] dark:text-white group-hover:text-amber-400 transition-colors">
                  Bluemint Metabolic Prep
                </h3>
                <p class="text-xs text-[#031827]/70 dark:text-white/70 mt-1 leading-relaxed">
                  {{ app()->getLocale() === 'ar' ? 'تنشيط مسار AMPK المركزي، تنظيم سكر الدم بعد الوجبات، ومحاكاة فوائد الصيام المتقطع.' : 'Phospholipid-bound berberine complex activating cellular AMPK, glucose sensitivity, and autophagy.' }}
                </p>
              </div>

              <div class="space-y-1 pt-2 border-t border-[#0A4F78]/10 dark:border-white/10 text-[11px]">
                <div class="flex justify-between text-[#031827]/60 dark:text-white/60">
                  <span>{{ app()->getLocale() === 'ar' ? 'المركب الأساسي' : 'Primary Bioactive' }}:</span>
                  <span class="font-bold text-[#031827] dark:text-white">Berbevis 550mg</span>
                </div>
                <div class="flex justify-between text-[#031827]/60 dark:text-white/60">
                  <span>{{ app()->getLocale() === 'ar' ? 'التوقيت الموصى به' : 'Timing Protocol' }}:</span>
                  <span class="font-bold text-amber-600 dark:text-amber-300">15m Pre-Meal</span>
                </div>
              </div>
            </div>

            <div class="pt-6 mt-4 border-t border-[#0A4F78]/10 dark:border-white/10 space-y-2">
              <div class="flex justify-between items-baseline">
                <span class="text-2xl font-black text-[#031827] dark:text-white">$52.00</span>
                <span class="text-xs text-amber-600 dark:text-amber-300 font-semibold">60 Capsules</span>
              </div>
              <div class="grid grid-cols-2 gap-2">
                <a href="{{ route('customer.product.show', 'blue-metabolic') }}" class="py-2.5 text-center text-xs font-bold rounded-xl border border-[#0A4F78]/20 dark:border-white/20 hover:border-[#0A4F78] dark:hover:border-white text-[#0A4F78] dark:text-white transition-colors">
                  {{ app()->getLocale() === 'ar' ? 'الملف' : 'Monograph' }}
                </a>
                <button onclick="if(window.BLUEZONE_CART){window.BLUEZONE_CART.addItem(4, 'BLUE METABOLIC', 52.00, '{{ asset('assets/products/blue-metabolic.jpg') }}', 1);}" class="py-2.5 text-center text-xs font-black uppercase rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-900 font-black transition-all shadow-md cursor-pointer">
                  {{ app()->getLocale() === 'ar' ? 'أضف' : 'Add Prep' }}
                </button>
              </div>
            </div>
          </div>

        </div>
      </div>
    </section>
