@php
    $sec4Conf = $secData['sec4'] ?? [];
    $mechanismEn = $sec4Conf['mechanism_en'] ?? '';
    $mechanismAr = $sec4Conf['mechanism_ar'] ?? '';
    $diagramImage = $sec4Conf['diagram_image'] ?? '';
    $researchNoteEn = $sec4Conf['research_note_en'] ?? '';
    $researchNoteAr = $sec4Conf['research_note_ar'] ?? '';

    $sec4Pathways = $sec4Conf['pathways'] ?? null;
    if (!is_array($sec4Pathways) || empty($sec4Pathways)) {
        $sec4Pathways = [
            [
                'icon' => 'fa-microchip',
                'title_en' => 'Blood-Brain Barrier (BBB) Kinetics:',
                'title_ar' => 'نفاذية الحاجز الدموي الدماغي (BBB):',
                'description_en' => 'Phospholipid-conjugated carriers enable efficient transport across the blood-brain barrier for localized neuronal receptor binding.',
                'description_ar' => 'مركبات الفسفوليبيد المرفقة تمكن الجزيئات الحيوية من عبور الحاجز الدموي الدماغي بكفاءة، مما يوفر تركيزاً نشطاً داخل المشابك العصبية دون فقدان هضمي.'
            ],
            [
                'icon' => 'fa-atom',
                'title_en' => 'Mitochondrial Redox Equilibrium:',
                'title_ar' => 'حماية ميتوكوندريا الخلية والتنفس الهوائي:',
                'description_en' => 'Sustains electron transport chain integrity (Complexes I & III), shielding mitochondrial DNA from premature senescence.',
                'description_ar' => 'يحمي الإنزيم المساعد CoQ10 وسلسلة مضادات الأكسدة المعقدة من تسرب الإلكترونات والإجهاد التأكسدي، مما يعزز عمر الخلية وقدرتها الإنتاجية.'
            ],
        ];
    }
@endphp

<div class="space-y-6">
    <!-- 1. CELLULAR MECHANISM TEXT -->
    <div class="p-5 rounded-2xl bg-white dark:bg-[#031827] border-2 border-purple-500/30 shadow-sm space-y-4">
        <div class="flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
            <span class="w-8 h-8 rounded-xl bg-purple-500/10 text-purple-600 flex items-center justify-center text-sm font-black">
                <i class="fa-solid fa-dna"></i>
            </span>
            <div>
                <h4 class="text-xs sm:text-sm font-black text-slate-900 dark:text-white">
                    {{ $isAr ? 'الآلية العلمية الدوائية والامتصاص الخلوي (Pharmacokinetics & Mechanism)' : 'Cellular Science & Mechanism of Action' }}
                </h4>
                <p class="text-[11px] text-slate-500 dark:text-slate-400">
                    {{ $isAr ? 'شرح المسارات الخلوية، إشارات NAD+، والتوافر الحيوي الدقيق للمركبات' : 'Detailed molecular cascade, NAD+ signaling kinetics, and bioavailability pathway' }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                    <span class="px-1.5 py-0.5 rounded bg-slate-200 dark:bg-slate-700 text-[10px] font-mono">EN</span>
                    Mechanism of Action (EN):
                </label>
                <textarea name="section_settings[sec4][mechanism_en]" 
                          rows="4" 
                          placeholder="Explain cellular pathways, mitochondrial cascade, and kinetic bio-absorption..."
                          class="w-full p-3.5 text-xs text-slate-900 dark:text-white bg-slate-50 dark:bg-[#062B49] rounded-xl border border-slate-300 dark:border-slate-600 outline-none focus:ring-2 focus:ring-purple-500/30 transition-all shadow-inner leading-relaxed">{{ $mechanismEn }}</textarea>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                    <span class="px-1.5 py-0.5 rounded bg-purple-500/15 text-purple-600 text-[10px] font-mono">AR</span>
                    الآلية العلمية الدوائية (AR):
                </label>
                <textarea name="section_settings[sec4][mechanism_ar]" 
                          rows="4" 
                          dir="rtl" 
                          placeholder="شرح المسارات الحيوية، تجديد خلايا الميتوكوندريا والتوافر الحيوي في الجسم..."
                          class="w-full p-3.5 text-xs text-slate-900 dark:text-white bg-slate-50 dark:bg-[#062B49] rounded-xl border border-slate-300 dark:border-slate-600 outline-none focus:ring-2 focus:ring-purple-500/30 transition-all font-tajawal shadow-inner leading-relaxed">{{ $mechanismAr }}</textarea>
            </div>
        </div>
    </div>

    <!-- 2. PRECISION PATHWAYS CARDS -->
    <div class="p-5 rounded-2xl bg-white dark:bg-[#031827] border-2 border-purple-500/30 shadow-sm space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-xl bg-purple-500/10 text-purple-600 flex items-center justify-center text-sm font-black">
                    <i class="fa-solid fa-network-wired"></i>
                </span>
                <div>
                    <h4 class="text-xs sm:text-sm font-black text-slate-900 dark:text-white">
                        {{ $isAr ? 'بطاقات المسارات الحيوية الدقيقة (Precision Pathways)' : 'Precision Pathways Cards' }}
                    </h4>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">
                        {{ $isAr ? 'بطاقات المسارات السريرية: الحاجز الدموي الدماغي، توازن الميتوكوندريا، وغيرها' : 'Pathway cards: BBB kinetics, mitochondrial redox, and neurotransmitter pathways' }}
                    </p>
                </div>
            </div>
            <button type="button" onclick="addPathwayRow()" 
                    class="px-3 py-1.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-black transition-all flex items-center gap-1.5 shadow-sm active:scale-95 cursor-pointer">
                <i class="fa-solid fa-plus text-[10px]"></i>
                <span>{{ $isAr ? 'إضافة مسار' : 'Add Pathway' }}</span>
            </button>
        </div>

        <div class="space-y-4" id="sec4PathwaysList">
            @foreach($sec4Pathways as $idx => $pw)
                <div class="p-4 rounded-xl bg-slate-50 dark:bg-[#062B49] border border-slate-200 dark:border-slate-700 space-y-3 pathway-card-row" data-index="{{ $idx }}">
                    <div class="flex items-center justify-between">
                        <span class="px-2 py-0.5 rounded-md bg-purple-500/15 text-purple-600 font-mono text-[10px] font-black">
                            PATHWAY #{{ $idx + 1 }}
                        </span>
                        <button type="button" onclick="this.closest('.pathway-card-row').remove()" 
                                class="w-7 h-7 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 flex items-center justify-center transition-colors cursor-pointer"
                                title="{{ $isAr ? 'حذف المسار' : 'Remove' }}">
                            <i class="fa-solid fa-trash-can text-xs"></i>
                        </button>
                    </div>

                    <input type="hidden" name="section_settings[sec4][pathways][{{ $idx }}][icon]" value="{{ $pw['icon'] ?? ($idx === 1 ? 'fa-atom' : ($idx === 2 ? 'fa-dna' : 'fa-microchip')) }}">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Title (EN):</label>
                            <input type="text" name="section_settings[sec4][pathways][{{ $idx }}][title_en]" 
                                   value="{{ $pw['title_en'] ?? '' }}" 
                                   placeholder="Blood-Brain Barrier (BBB) Kinetics:"
                                   class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-[#031827] dark:text-white font-bold">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-0.5">العنوان (AR):</label>
                            <input type="text" name="section_settings[sec4][pathways][{{ $idx }}][title_ar]" 
                                   value="{{ $pw['title_ar'] ?? '' }}" 
                                   dir="rtl"
                                   placeholder="نفاذية الحاجز الدموي الدماغي (BBB):"
                                   class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-[#031827] dark:text-white font-tajawal font-bold">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Description (EN):</label>
                            <textarea name="section_settings[sec4][pathways][{{ $idx }}][description_en]" 
                                      rows="2"
                                      placeholder="Phospholipid-conjugated carriers enable..."
                                      class="w-full p-2 text-xs rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-[#031827] dark:text-white">{{ $pw['description_en'] ?? '' }}</textarea>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-0.5">الوصف (AR):</label>
                            <textarea name="section_settings[sec4][pathways][{{ $idx }}][description_ar]" 
                                      rows="2"
                                      dir="rtl"
                                      placeholder="مركبات الفسفوليبيد المرفقة تمكن الجزيئات..."
                                      class="w-full p-2 text-xs rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-[#031827] dark:text-white font-tajawal">{{ $pw['description_ar'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- 3. RESEARCH NOTE CALLOUT -->
    <div class="p-5 rounded-2xl bg-white dark:bg-[#031827] border-2 border-purple-500/30 shadow-sm space-y-4">
        <div class="flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
            <span class="w-8 h-8 rounded-xl bg-purple-500/10 text-purple-600 flex items-center justify-center text-sm font-black">
                <i class="fa-solid fa-book-bookmark"></i>
            </span>
            <div>
                <h4 class="text-xs sm:text-sm font-black text-slate-900 dark:text-white">
                    {{ $isAr ? 'ملاحظة الأبحاث السريرية والدراسات المرجعية (Research Note)' : 'Clinical Research Note Callout' }}
                </h4>
                <p class="text-[11px] text-slate-500 dark:text-slate-400">
                    {{ $isAr ? 'شريط التوثيق البنفسجي السفلي المخصص لدراسات المناطق الزرقاء والأبحاث المحكمة' : 'Bottom purple banner citing centenarian cohort studies and longevity trials' }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                    <span class="px-1.5 py-0.5 rounded bg-slate-200 dark:bg-slate-700 font-mono text-[10px]">EN</span>
                    Research Note (EN):
                </label>
                <textarea name="section_settings[sec4][research_note_en]" 
                          rows="2" 
                          placeholder="Validated against empirical longevity data from centenarian cohort studies..."
                          class="w-full p-3 text-xs text-slate-900 dark:text-white bg-slate-50 dark:bg-[#062B49] rounded-xl border border-slate-300 dark:border-slate-600 outline-none focus:ring-2 focus:ring-purple-500/30 transition-all shadow-inner leading-relaxed">{{ $researchNoteEn }}</textarea>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                    <span class="px-1.5 py-0.5 rounded bg-purple-500/15 text-purple-600 font-mono text-[10px]">AR</span>
                    ملاحظة الأبحاث (AR):
                </label>
                <textarea name="section_settings[sec4][research_note_ar]" 
                          rows="2" 
                          dir="rtl" 
                          placeholder="مبني على أبحاث طول العمر المستوحاة من دراسات المعمرين في المناطق الزرقاء الخمس..."
                          class="w-full p-3 text-xs text-slate-900 dark:text-white bg-slate-50 dark:bg-[#062B49] rounded-xl border border-slate-300 dark:border-slate-600 outline-none focus:ring-2 focus:ring-purple-500/30 transition-all font-tajawal shadow-inner leading-relaxed">{{ $researchNoteAr }}</textarea>
            </div>
        </div>
    </div>
</div>
