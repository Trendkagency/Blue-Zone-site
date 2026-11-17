@php
    $sec3Conf = $secData['sec3'] ?? [];
    $doseBadgeEn = $sec3Conf['dose_badge_en'] ?? '';
    $doseBadgeAr = $sec3Conf['dose_badge_ar'] ?? '';
    $primaryDirectiveEn = $sec3Conf['primary_directive_en'] ?? '';
    $primaryDirectiveAr = $sec3Conf['primary_directive_ar'] ?? '';

    $sec3Cards = $sec3Conf['cards'] ?? null;
    if (!is_array($sec3Cards) || empty($sec3Cards)) {
        $sec3Cards = [
            [
                'icon' => 'fa-sun',
                'title_en' => '1. Timing',
                'title_ar' => '1. التوقيت اليومي',
                'description_en' => 'Administer each morning to harmonize with your natural circadian cortisol curve.',
                'description_ar' => 'يُفضل تناوله صباحاً ليتزامن مع إيقاع الساعة البيولوجية للجسم.'
            ],
            [
                'icon' => 'fa-glass-water',
                'title_en' => '2. Hydration',
                'title_ar' => '2. السائل الناقل',
                'description_en' => '250ml mineralized water facilitates rapid gastro-resistant dissolution.',
                'description_ar' => '250 مل من الماء النقي لتسريع ذوبان الكبسولة المعوية النباتية.'
            ],
            [
                'icon' => 'fa-bottle-droplet',
                'title_en' => '3. Lipid Synergy',
                'title_ar' => '3. تعزيز الامتصاص',
                'description_en' => 'Ingesting with healthy fats boosts lipophilic flavonoid bioavailability by up to 300%.',
                'description_ar' => 'تناوله مع زيت الزيتون البكر الممتاز يضاعف امتصاص مضادات الأكسدة.'
            ],
            [
                'icon' => 'fa-repeat',
                'title_en' => '4. Regularity',
                'title_ar' => '4. الاستمرارية',
                'description_en' => 'Zero tolerance buildup or dependency. Engineered for uninterrupted daily longevity.',
                'description_ar' => 'لا يسبب الإدمان أو الاعتمادية، ومصمم للاستخدام اليومي المستمر بأمان.'
            ],
        ];
    }
@endphp

<div class="space-y-6">
    <!-- 1. PRIMARY DOSAGE & DIRECTIVE STUDIO -->
    <div class="p-5 rounded-2xl bg-white dark:bg-[#031827] border-2 border-sky-500/30 shadow-sm space-y-4">
        <div class="flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
            <span class="w-8 h-8 rounded-xl bg-sky-500/10 text-sky-600 flex items-center justify-center text-sm font-black">
                <i class="fa-solid fa-prescription-bottle"></i>
            </span>
            <div>
                <h4 class="text-xs sm:text-sm font-black text-slate-900 dark:text-white">
                    {{ $isAr ? 'الجرعة الأساسية والتوجيه السريري المباشر (Primary Directive)' : 'Dosage Badge & Primary Administration Directive' }}
                </h4>
                <p class="text-[11px] text-slate-500 dark:text-slate-400">
                    {{ $isAr ? 'شارة الجرعة في رأس القسم، ونص التوجيه الطبي المباشر في المربع الإرشادي' : 'Header pill badge dosage and primary clinical directive callout text' }}
                </p>
            </div>
        </div>

        <!-- Dose Badges EN / AR -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                    <span class="px-1.5 py-0.5 rounded bg-sky-500/10 text-sky-600 font-mono text-[10px]">EN</span>
                    {{ $isAr ? 'شارة الجرعة (الإنجليزية):' : 'Dosage Badge (English):' }}
                </label>
                <input type="text" name="section_settings[sec3][dose_badge_en]" 
                       value="{{ $doseBadgeEn }}" 
                       placeholder="e.g. 2 Vegetable Capsules Daily" 
                       class="w-full px-3.5 py-2 text-xs font-bold rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-[#062B49] text-slate-900 dark:text-white shadow-inner">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                    <span class="px-1.5 py-0.5 rounded bg-sky-500/10 text-sky-600 font-mono text-[10px]">AR</span>
                    {{ $isAr ? 'شارة الجرعة (العربية):' : 'Dosage Badge (Arabic):' }}
                </label>
                <input type="text" name="section_settings[sec3][dose_badge_ar]" 
                       value="{{ $doseBadgeAr }}" 
                       dir="rtl"
                       placeholder="مثال: كبسولتان نباتيتان يومياً" 
                       class="w-full px-3.5 py-2 text-xs font-bold rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-[#062B49] text-slate-900 dark:text-white font-tajawal shadow-inner">
            </div>
        </div>

        <!-- Primary Directive Textarea EN / AR -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                    <span class="px-1.5 py-0.5 rounded bg-slate-200 dark:bg-slate-700 font-mono text-[10px]">EN</span>
                    {{ $isAr ? 'نص التوجيه السريري المباشر (Primary Directive EN):' : 'Primary Directive Text (English):' }}
                </label>
                <textarea name="section_settings[sec3][primary_directive_en]" 
                          rows="3" 
                          placeholder="Administer 2 capsules daily with mineralized water or healthy fats..."
                          class="w-full p-3 text-xs text-slate-900 dark:text-white bg-slate-50 dark:bg-[#062B49] rounded-xl border border-slate-300 dark:border-slate-600 outline-none focus:ring-2 focus:ring-sky-500/30 transition-all shadow-inner leading-relaxed">{{ $primaryDirectiveEn }}</textarea>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                    <span class="px-1.5 py-0.5 rounded bg-sky-500/15 text-sky-600 font-mono text-[10px]">AR</span>
                    {{ $isAr ? 'نص التوجيه السريري المباشر (Primary Directive AR):' : 'Primary Directive Text (Arabic):' }}
                </label>
                <textarea name="section_settings[sec3][primary_directive_ar]" 
                          rows="3" 
                          dir="rtl"
                          placeholder="تناول كبسولتين يومياً صباحاً مع الماء النقي أو وجبة تحتوي على دهون صحية..."
                          class="w-full p-3 text-xs text-slate-900 dark:text-white bg-slate-50 dark:bg-[#062B49] rounded-xl border border-slate-300 dark:border-slate-600 outline-none focus:ring-2 focus:ring-sky-500/30 transition-all font-tajawal shadow-inner leading-relaxed">{{ $primaryDirectiveAr }}</textarea>
            </div>
        </div>
    </div>

    <!-- 2. VISUAL PROTOCOL CARDS STUDIO (4 PROTOCOL PILLARS) -->
    <div class="p-5 rounded-2xl bg-white dark:bg-[#031827] border-2 border-sky-500/30 shadow-sm space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-xl bg-sky-500/10 text-sky-600 flex items-center justify-center text-sm font-black">
                    <i class="fa-solid fa-table-cells-large"></i>
                </span>
                <div>
                    <h4 class="text-xs sm:text-sm font-black text-slate-900 dark:text-white">
                        {{ $isAr ? 'بطاقات بروتوكول الاستخدام المرئية (4 Visual Protocol Cards)' : 'Visual Protocol Cards (4 Pillars)' }}
                    </h4>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">
                        {{ $isAr ? 'البطاقات الأربعة المعروضة بصفحة المنتج: التوقيت، السائل الناقل، تعزيز الامتصاص، والاستمرارية' : 'The 4 cards displayed on storefront: Timing, Hydration, Lipid Synergy, and Regularity' }}
                    </p>
                </div>
            </div>
            <button type="button" onclick="addProtocolCardRow()" 
                    class="px-3 py-1.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-xs font-black transition-all flex items-center gap-1.5 shadow-sm active:scale-95 cursor-pointer">
                <i class="fa-solid fa-plus text-[10px]"></i>
                <span>{{ $isAr ? 'إضافة بطاقة' : 'Add Card' }}</span>
            </button>
        </div>

        <div class="space-y-4" id="sec3CardsList">
            @foreach($sec3Cards as $idx => $card)
                <div class="p-4 rounded-xl bg-slate-50 dark:bg-[#062B49] border border-slate-200 dark:border-slate-700 space-y-3 protocol-card-row" data-index="{{ $idx }}">
                    <div class="flex items-center justify-between">
                        <span class="px-2 py-0.5 rounded-md bg-sky-500/15 text-sky-600 font-mono text-[10px] font-black">
                            CARD #{{ $idx + 1 }}
                        </span>
                        <button type="button" onclick="this.closest('.protocol-card-row').remove()" 
                                class="w-7 h-7 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 flex items-center justify-center transition-colors cursor-pointer"
                                title="{{ $isAr ? 'حذف البطاقة' : 'Remove Card' }}">
                            <i class="fa-solid fa-trash-can text-xs"></i>
                        </button>
                    </div>

                    <input type="hidden" name="section_settings[sec3][cards][{{ $idx }}][icon]" value="{{ $card['icon'] ?? ($idx === 0 ? 'fa-sun' : ($idx === 1 ? 'fa-glass-water' : ($idx === 2 ? 'fa-bottle-droplet' : 'fa-repeat'))) }}">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Title (EN):</label>
                            <input type="text" name="section_settings[sec3][cards][{{ $idx }}][title_en]" 
                                   value="{{ $card['title_en'] ?? '' }}" 
                                   placeholder="1. Timing"
                                   class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-[#031827] dark:text-white font-bold">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-0.5">العنوان (AR):</label>
                            <input type="text" name="section_settings[sec3][cards][{{ $idx }}][title_ar]" 
                                   value="{{ $card['title_ar'] ?? '' }}" 
                                   dir="rtl"
                                   placeholder="1. التوقيت اليومي"
                                   class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-[#031827] dark:text-white font-tajawal font-bold">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Description (EN):</label>
                            <textarea name="section_settings[sec3][cards][{{ $idx }}][description_en]" 
                                      rows="2"
                                      placeholder="Administer each morning to harmonize..."
                                      class="w-full p-2 text-xs rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-[#031827] dark:text-white">{{ $card['description_en'] ?? '' }}</textarea>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-0.5">الوصف (AR):</label>
                            <textarea name="section_settings[sec3][cards][{{ $idx }}][description_ar]" 
                                      rows="2"
                                      dir="rtl"
                                      placeholder="يُفضل تناوله صباحاً ليتزامن مع إيقاع الساعة البيولوجية..."
                                      class="w-full p-2 text-xs rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-[#031827] dark:text-white font-tajawal">{{ $card['description_ar'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
