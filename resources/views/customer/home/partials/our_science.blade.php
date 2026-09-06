    <!-- 09. OUR SCIENCE (COMPREHENSIVE SCIENTIFIC DOSSIER & IN-DEPTH CELLULAR MECHANISMS) -->
    <section id="our-science" class="py-24 bg-white dark:bg-[#062B49] border-b border-[#0A4F78]/10 transition-colors">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-16">
        
        <!-- Header -->
        <div class="text-center max-w-3xl mx-auto space-y-4">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#67B34A]/15 text-[#67B34A] text-xs font-black uppercase tracking-widest border border-[#67B34A]/30">
            <span class="w-2 h-2 rounded-full bg-[#67B34A]"></span>
            {{ app()->getLocale() === 'ar' ? 'العلم والأبحاث السريرية الكاملة' : 'OUR SCIENCE & CLINICAL RIGOR' }}
          </div>
          <h2 class="text-3xl sm:text-5xl font-black text-[#031827] dark:text-[#F6F5EF] tracking-tight">
            {{ app()->getLocale() === 'ar' ? 'البيولوجيا الجزيئية وراء طول العمر' : 'THE MOLECULAR SCIENCE OF LONGEVITY' }}
          </h2>
          <p class="text-sm sm:text-base text-[#031827]/75 dark:text-[#F6F5EF]/75 font-medium leading-relaxed">
            {{ app()->getLocale() === 'ar' 
               ? 'معلومات علمية شاملة وغير مقتضبة تفصّل المسارات الحيوية، التجارب السريرية المنشورة، والتوافر الحيوي لكل مركب نستخدمه.' 
               : 'A complete, unabridged scientific breakdown of our 4 biological pathways, published human clinical trials, cellular pharmacokinetics, and standardizations.' }}
          </p>
        </div>

        <!-- Dynamic Scientific Mechanism Dossiers (Full admin controlled AR/EN) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          @php
            $badgeColors = ['#67B34A', '#2A8FC2', '#F59E0B', '#8B5CF6', '#EC4899', '#06B6D4'];
          @endphp

          @forelse($scienceProducts as $index => $sp)
            @php
              $spSlug = is_array($sp) ? ($sp['slug'] ?? '') : $sp->slug;
              $spName = is_array($sp) ? ($sp['name_' . app()->getLocale()] ?? $sp['name_en'] ?? $sp['name'] ?? '') : $sp->name;
              $spCategory = is_array($sp) ? ($sp['category_' . app()->getLocale()] ?? $sp['category_en'] ?? '') : ($sp->category?->name ?? 'Cellular Longevity');
              $spTagline = is_array($sp) ? ($sp['tagline_' . app()->getLocale()] ?? $sp['tagline_en'] ?? '') : $sp->tagline;
              $spScience = is_array($sp) ? ($sp['science_' . app()->getLocale()] ?? $sp['science_en'] ?? $sp['description_' . app()->getLocale()] ?? '') : ($sp->science ?: $sp->description);
              $spMechanism = is_array($sp) ? ($sp['professional_info']['clinical_mechanism'] ?? $sp['clinical_mechanism'] ?? '') : $sp->clinical_mechanism;
              $spFormula = is_array($sp) ? ($sp['professional_info']['formula_details'] ?? $sp['formula_details'] ?? '') : $sp->formula_details;
              $spIngredients = is_array($sp) ? ($sp['ingredients'] ?? []) : ($sp->ingredients ?? []);
              $spBenefits = is_array($sp) ? ($sp['benefits_' . app()->getLocale()] ?? $sp['benefits_en'] ?? $sp['benefits'] ?? []) : $sp->benefits;
              $color = $badgeColors[$index % count($badgeColors)];

              // Extract top active compounds
              $compSummaries = [];
              if (is_array($spIngredients) && !empty($spIngredients)) {
                foreach (array_slice($spIngredients, 0, 2) as $ing) {
                  $ingName = is_array($ing) ? ($ing['name_' . app()->getLocale()] ?? $ing['name_en'] ?? $ing['name'] ?? '') : '';
                  $ingDose = is_array($ing) ? ($ing['dose'] ?? '') : '';
                  if ($ingName) {
                    $compSummaries[] = $ingDose ? "{$ingName} ({$ingDose})" : $ingName;
                  }
                }
              }
              $compText = !empty($compSummaries) ? implode(' + ', $compSummaries) : ($spFormula ? Str::limit($spFormula, 50) : 'Pharmaceutical Grade Actives');

              // Top clinical benefit
              $benefitText = !empty($spBenefits) && is_array($spBenefits) ? $spBenefits[0] : (app()->getLocale() === 'ar' ? 'تحسن مثبت سريرياً في المؤشرات الحيوية' : 'Clinically validated biomarker optimization');
            @endphp

            <div class="p-8 rounded-3xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/20 shadow-md space-y-6 hover:shadow-xl transition-all flex flex-col justify-between">
              <div class="space-y-6">
                <!-- Header Card -->
                <div class="flex items-center justify-between border-b border-[#0A4F78]/15 dark:border-[#0A4F78]/30 pb-4">
                  <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-2xl flex items-center justify-center font-mono font-black text-sm" style="background-color: {{ $color }}20; color: {{ $color }}; border: 1px solid {{ $color }}40;">
                      {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                    </span>
                    <div>
                      <span class="text-[10px] font-mono font-bold uppercase tracking-wider block" style="color: {{ $color }};">
                        {{ $spCategory }}
                      </span>
                      <h3 class="text-xl font-black text-[#031827] dark:text-[#F6F5EF]">
                        {{ $spName }}
                      </h3>
                    </div>
                  </div>
                  @if($spTagline)
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-mono font-bold max-w-[140px] truncate" style="background-color: {{ $color }}15; color: {{ $color }};">
                      {{ $spTagline }}
                    </span>
                  @endif
                </div>

                <!-- Scientific Breakdown / Longevity Foundation -->
                <div class="space-y-3 text-xs sm:text-sm text-[#031827]/80 dark:text-[#F6F5EF]/80 leading-relaxed">
                  @if($spScience)
                    <p>
                      <strong>{{ app()->getLocale() === 'ar' ? 'الأساس العلمي الخلوي:' : 'Scientific Foundation:' }}</strong>
                      {{ Str::limit($spScience, 210) }}
                    </p>
                  @endif
                  @if($spMechanism)
                    <p>
                      <strong>{{ app()->getLocale() === 'ar' ? 'الآلية الحيوية:' : 'Molecular Mechanism:' }}</strong>
                      {{ Str::limit($spMechanism, 220) }}
                    </p>
                  @endif
                </div>

                <!-- Clinical Evidence & Dosing Box -->
                <div class="p-4 rounded-2xl bg-white dark:bg-[#062B49] border border-[#0A4F78]/15 space-y-2">
                  <span class="text-[10px] font-black uppercase tracking-wider text-[#2A8FC2] block">
                    {{ app()->getLocale() === 'ar' ? 'الأدلة السريرية والجرعات القياسية' : 'CLINICAL EVIDENCE & DOSING' }}
                  </span>
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                    <div>
                      <span class="text-slate-400 block text-[10px]">
                        {{ app()->getLocale() === 'ar' ? 'المركبات النشطة الموحدة:' : 'Standardized Agents:' }}
                      </span>
                      <span class="font-bold text-[#031827] dark:text-[#F6F5EF]">
                        {{ $compText }}
                      </span>
                    </div>
                    <div>
                      <span class="text-slate-400 block text-[10px]">
                        {{ app()->getLocale() === 'ar' ? 'النتائج السريرية والمؤشرات:' : 'Clinical Evidence / Result:' }}
                      </span>
                      <span class="font-bold text-[#67B34A]">
                        {{ Str::limit($benefitText, 65) }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Footer with Our Science Details Link -->
              <div class="flex items-center justify-between pt-4 border-t border-[#0A4F78]/10 dark:border-[#0A4F78]/20">
                <span class="text-xs font-semibold text-slate-500">
                  {{ app()->getLocale() === 'ar' ? 'الملف العلمي الكامل:' : 'Associated Formulation:' }}
                </span>
                <a href="{{ route('customer.science.product', $spSlug) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#0A4F78] dark:text-[#2A8FC2] hover:text-[#67B34A] transition-colors group">
                  <span>{{ app()->getLocale() === 'ar' ? 'تفاصيل أبحاث العلوم' : 'Our Science Details' }}</span>
                  <span class="transition-transform group-hover:translate-x-1 rtl:group-hover:-translate-x-1">&rarr;</span>
                </a>
              </div>
            </div>
          @empty
            <div class="col-span-2 text-center py-12 text-slate-500">
              {{ app()->getLocale() === 'ar' ? 'لا توجد بيانات أبحاث منشورة حالياً.' : 'No scientific formulations available at this time.' }}
            </div>
          @endforelse
        </div>

        <!-- Analytical Standards Table -->
        <div class="p-8 rounded-3xl bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/20 space-y-6">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
              <span class="text-[10px] font-mono font-bold uppercase tracking-widest text-[#67B34A] block">ANALYTICAL RIGOR</span>
              <h4 class="text-xl font-black text-[#031827] dark:text-[#F6F5EF]">
                How Blue Zone Standards Compare to Generic Supplements
              </h4>
            </div>
            <a href="{{ route('customer.pages.about') }}" class="text-xs font-black uppercase tracking-wider text-[#0A4F78] dark:text-[#2A8FC2] hover:underline">
              {{ app()->getLocale() === 'ar' ? 'عرض معايير RS الكاملة ←' : 'EXPLORE RS STANDARDS →' }}
            </a>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
              <thead>
                <tr class="border-b border-[#0A4F78]/20 text-[#0A4F78] dark:text-[#2A8FC2] uppercase text-[10px] font-mono">
                  <th class="py-3 px-4">Quality Metric</th>
                  <th class="py-3 px-4 text-slate-400">Generic Market Brands</th>
                  <th class="py-3 px-4 text-[#67B34A]">Blue Zone Formulation Standard</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-[#0A4F78]/10 dark:divide-[#0A4F78]/20 text-[#031827]/80 dark:text-[#F6F5EF]/80">
                <tr>
                  <td class="py-3 px-4 font-bold">Potency Verification</td>
                  <td class="py-3 px-4 text-slate-400">Crude unstandardized ground powder</td>
                  <td class="py-3 px-4 font-bold text-[#67B34A]">High-Performance Liquid Chromatography (HPLC) Fingerprinted</td>
                </tr>
                <tr>
                  <td class="py-3 px-4 font-bold">Heavy Metal Threshold</td>
                  <td class="py-3 px-4 text-slate-400">General food-grade threshold (<10 ppm)</td>
                  <td class="py-3 px-4 font-bold text-[#67B34A]">ICP-MS pharmaceutical screening (<0.01 ppm lead, arsenic, mercury)</td>
                </tr>
                <tr>
                  <td class="py-3 px-4 font-bold">Bioavailability Delivery</td>
                  <td class="py-3 px-4 text-slate-400">Unprotected crystals (low absorption)</td>
                  <td class="py-3 px-4 font-bold text-[#67B34A]">Phospholipid Phytosome matrix (9.6x - 29x enhanced absorption)</td>
                </tr>
                <tr>
                  <td class="py-3 px-4 font-bold">Excipients & Additives</td>
                  <td class="py-3 px-4 text-slate-400">Magnesium stearate, silicon dioxide, dyes</td>
                  <td class="py-3 px-4 font-bold text-[#67B34A]">100% Clean Label (Zero synthetic binders, zero artificial colorants)</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </div>
    </section>
