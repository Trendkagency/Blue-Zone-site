@php
    $sec2Conf = $secData['sec2'] ?? [];
    $sec2Items = $sec2Conf['items'] ?? null;
    if (!is_array($sec2Items) || empty($sec2Items)) {
        $sec2Items = [
            ['metric_en' => 'Mitochondrial ATP Output', 'metric_ar' => 'إنتاج طاقة ATP الميتوكوندرية', 'value' => '+38%', 'description_en' => 'Observed cellular bioenergetic elevation in randomized trials.', 'description_ar' => 'تحسن ملحوظ في توليد الطاقة الخلوية دون إجهاد عصبي.'],
            ['metric_en' => 'Cerebral Perfusion & Focus', 'metric_ar' => 'التروية الدماغية وسرعة التركيز', 'value' => '2.4x', 'description_en' => 'Enhanced microvascular velocity and synaptic reaction speed.', 'description_ar' => 'زيادة تدفق الدورة الدموية الدقيقة وسرعة الاستجابة الذهنية.'],
        ];
    }

    $sec2Benefits = $sec2Conf['benefits'] ?? null;
    if (!is_array($sec2Benefits) || empty($sec2Benefits)) {
        $sec2Benefits = [
            ['text_en' => 'Accelerates neural pathway conductivity and sharpens cognitive acuity within 45 minutes.', 'text_ar' => 'تسريع التوصيل العصبي وشحذ اليقظة والتركيز الذهني خلال 45 دقيقة من التناول.'],
            ['text_en' => 'Protects vascular endothelial cells and promotes localized cerebral microcirculation.', 'text_ar' => 'حماية خلايا بطانة الأوعية الدموية ودعم التروية الدقيقة لأنسجة المخ.'],
            ['text_en' => 'Modulates cortisol response to sustain calm, high-performance focus under pressure.', 'text_ar' => 'تنظيم مستويات الكورتيزول لضمان صفاء ذهني مستدام تحت وطأة ضغوط العمل.'],
        ];
    }

    $sec2Phases = $sec2Conf['phases'] ?? null;
    if (!is_array($sec2Phases) || empty($sec2Phases)) {
        $sec2Phases = [
            [
                'phase_label' => 'Phase 1 · Days 1–7',
                'title_en' => 'Acute Perfusion & Alertness',
                'title_ar' => 'الاستجابة الخلوية الفورية',
                'description_en' => 'Immediate microcirculation enhancement and neural stamina without nervous system crash.',
                'description_ar' => 'تحسين تدفق الدم الدماغي، رفع مستوى اليقظة، وتحييد بوادر الإجهاد الذهني اليومي.'
            ],
            [
                'phase_label' => 'Phase 2 · Days 14–30',
                'title_en' => 'Synaptic Consolidation',
                'title_ar' => 'تجدد المشابك العصبية',
                'description_en' => 'Elevated BDNF growth signaling and sustained neurotransmitter balance for cognitive resilience.',
                'description_ar' => 'تحفيز إشارات BDNF، استقرار غشاء الخلايا الفسفوليبيدي، وزيادة كفاءة الذاكرة العاملة.'
            ],
            [
                'phase_label' => 'Phase 3 · Days 60+',
                'title_en' => 'Deep Cellular Longevity',
                'title_ar' => 'حماية طول العمر الخلوي',
                'description_en' => 'Mitochondrial resilience and ongoing genomic stability supporting lifelong cognitive health.',
                'description_ar' => 'حماية شاملة للميتوكوندريا، ودعم صحة الأوعية الدموية ومرونة الأداء الدماغي الممتد.'
            ],
        ];
    }
@endphp

<div class="space-y-6">
    <!-- 1. CLINICAL BENEFITS BULLET LIST -->
    <div class="p-5 rounded-2xl bg-white dark:bg-[#031827] border-2 border-amber-500/30 shadow-sm space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center text-sm font-black">
                    <i class="fa-solid fa-list-check"></i>
                </span>
                <div>
                    <h4 class="text-xs sm:text-sm font-black text-slate-900 dark:text-white">
                        {{ $isAr ? 'قائمة الفوائد السريرية والنتائج المباشرة (Key Clinical Benefits)' : 'Key Clinical Benefits List' }}
                    </h4>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">
                        {{ $isAr ? 'نقاط الفوائد ذات علامة الصح الأخضر المعروضة في القسم الثاني' : 'Bullet check-mark outcomes displayed in section 02' }}
                    </p>
                </div>
            </div>
            <button type="button" onclick="addBenefitRow()" 
                    class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black transition-all flex items-center gap-1.5 shadow-sm active:scale-95 cursor-pointer">
                <i class="fa-solid fa-plus text-[10px]"></i>
                <span>{{ $isAr ? 'إضافة فائدة' : 'Add Benefit' }}</span>
            </button>
        </div>

        <div class="space-y-3" id="sec2BenefitsList">
            @foreach($sec2Benefits as $idx => $ben)
                @php
                    $benEn = is_array($ben) ? ($ben['text_en'] ?? '') : (string)$ben;
                    $benAr = is_array($ben) ? ($ben['text_ar'] ?? '') : '';
                @endphp
                <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-[#062B49] border border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row items-stretch sm:items-center gap-3 benefit-row" data-index="{{ $idx }}">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 flex-1">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Benefit (EN):</label>
                            <input type="text" name="section_settings[sec2][benefits][{{ $idx }}][text_en]" 
                                   value="{{ $benEn }}" 
                                   placeholder="Accelerates neural pathway conductivity..."
                                   class="w-full px-3 py-1.5 text-xs rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-[#031827] dark:text-white font-medium">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-0.5">الفائدة (AR):</label>
                            <input type="text" name="section_settings[sec2][benefits][{{ $idx }}][text_ar]" 
                                   value="{{ $benAr }}" 
                                   dir="rtl"
                                   placeholder="تسريع التوصيل العصبي وشحذ اليقظة..."
                                   class="w-full px-3 py-1.5 text-xs rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-[#031827] dark:text-white font-tajawal font-medium">
                        </div>
                    </div>
                    <button type="button" onclick="this.closest('.benefit-row').remove()" 
                            class="w-7 h-7 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 flex items-center justify-center shrink-0 self-end sm:self-center transition-colors cursor-pointer"
                            title="{{ $isAr ? 'حذف الفائدة' : 'Remove' }}">
                        <i class="fa-solid fa-trash-can text-xs"></i>
                    </button>
                </div>
            @endforeach
        </div>
    </div>

    <!-- 2. 3-PHASE PROGRESSIVE BIOLOGICAL TIMELINE -->
    <div class="p-5 rounded-2xl bg-white dark:bg-[#031827] border-2 border-amber-500/30 shadow-sm space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center text-sm font-black">
                    <i class="fa-solid fa-timeline"></i>
                </span>
                <div>
                    <h4 class="text-xs sm:text-sm font-black text-slate-900 dark:text-white">
                        {{ $isAr ? 'مراحل التراكم الحيوي واستجابة الجسم (3-Phase Biological Timeline)' : 'Progressive Biological Timeline (3 Phases)' }}
                    </h4>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">
                        {{ $isAr ? 'المراحل الثلاث التراكمية لأثر المستحضر (الأسبوع 1، الشهر 1، والشهر 2 فما فوق)' : 'Phase 1 (Days 1–7), Phase 2 (Days 14–30), and Phase 3 (Days 60+) cards' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="space-y-4" id="sec2PhasesList">
            @foreach($sec2Phases as $idx => $ph)
                <div class="p-4 rounded-xl bg-slate-50 dark:bg-[#062B49] border border-slate-200 dark:border-slate-700 space-y-3 phase-card-row" data-index="{{ $idx }}">
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                        <div class="sm:col-span-3">
                            <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Phase Label (رمز المرحلة):</label>
                            <input type="text" name="section_settings[sec2][phases][{{ $idx }}][phase_label]" 
                                   value="{{ $ph['phase_label'] ?? '' }}" 
                                   placeholder="Phase 1 · Days 1–7"
                                   class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-[#031827] dark:text-white font-mono font-bold text-amber-600 dark:text-amber-400">
                        </div>
                        <div class="sm:col-span-4">
                            <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Title (EN):</label>
                            <input type="text" name="section_settings[sec2][phases][{{ $idx }}][title_en]" 
                                   value="{{ $ph['title_en'] ?? '' }}" 
                                   placeholder="Acute Perfusion & Alertness"
                                   class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-[#031827] dark:text-white font-bold">
                        </div>
                        <div class="sm:col-span-5">
                            <label class="block text-[10px] font-bold text-slate-500 mb-0.5">العنوان (AR):</label>
                            <input type="text" name="section_settings[sec2][phases][{{ $idx }}][title_ar]" 
                                   value="{{ $ph['title_ar'] ?? '' }}" 
                                   dir="rtl"
                                   placeholder="الاستجابة الخلوية الفورية"
                                   class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-[#031827] dark:text-white font-tajawal font-bold">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Description (EN):</label>
                            <textarea name="section_settings[sec2][phases][{{ $idx }}][description_en]" 
                                      rows="2"
                                      placeholder="Immediate microcirculation enhancement..."
                                      class="w-full p-2 text-xs rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-[#031827] dark:text-white">{{ $ph['description_en'] ?? '' }}</textarea>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-0.5">الوصف (AR):</label>
                            <textarea name="section_settings[sec2][phases][{{ $idx }}][description_ar]" 
                                      rows="2"
                                      dir="rtl"
                                      placeholder="تحسين تدفق الدم الدماغي، رفع مستوى اليقظة..."
                                      class="w-full p-2 text-xs rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-[#031827] dark:text-white font-tajawal">{{ $ph['description_ar'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
