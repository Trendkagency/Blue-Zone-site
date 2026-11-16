@php
    $pdData = $protocolDurationsData ?? [];
    $headingEn = $pdData['heading_en'] ?? 'SELECT PROTOCOL DURATION:';
    $headingAr = $pdData['heading_ar'] ?? 'اختر خطة البروتوكول العلاجي:';
    $subheadingEn = $pdData['subheading_en'] ?? 'Controlled Protocol Pricing';
    $subheadingAr = $pdData['subheading_ar'] ?? 'تسعير بروتوكولي مدروس';
    $tiers = $pdData['tiers'] ?? [];
@endphp

<div class="space-y-6">
    <!-- Headings Overrides Box -->
    <div class="p-5 rounded-2xl bg-white dark:bg-[#031827] border-2 border-slate-200 dark:border-slate-700/80 shadow-sm space-y-4">
        <div class="flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
            <span class="w-8 h-8 rounded-xl bg-teal-500/10 text-teal-600 flex items-center justify-center text-sm font-black">
                <i class="fa-solid fa-heading"></i>
            </span>
            <div>
                <h4 class="text-xs sm:text-sm font-black text-slate-900 dark:text-white">
                    {{ $isAr ? 'عناوين شريط اختيار مدة البروتوكول (Protocol Duration Headings)' : 'Protocol Duration Section Headings' }}
                </h4>
                <p class="text-[11px] text-slate-500 dark:text-slate-400">
                    {{ $isAr ? 'العنوان الرئيسي والفرعي الظاهر أعلى بطاقات الشراء المجدول' : 'Main title & subtitle above duration selector tabs' }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                    <span class="px-1.5 py-0.5 rounded bg-slate-200 dark:bg-slate-700 text-[10px] font-mono">EN</span>
                    Heading (EN):
                </label>
                <input type="text" name="section_settings[protocol_durations][heading_en]" 
                       id="inputDurationHeadingEn"
                       value="{{ $headingEn }}" 
                       oninput="syncProtocolDurationsPreview()"
                       placeholder="SELECT PROTOCOL DURATION:"
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-[#062B49] text-slate-900 dark:text-white font-mono shadow-inner">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                    <span class="px-1.5 py-0.5 rounded bg-teal-500/15 text-teal-600 text-[10px] font-mono">AR</span>
                    Heading (AR):
                </label>
                <input type="text" name="section_settings[protocol_durations][heading_ar]" 
                       id="inputDurationHeadingAr"
                       value="{{ $headingAr }}" 
                       oninput="syncProtocolDurationsPreview()"
                       dir="rtl" 
                       placeholder="اختر خطة البروتوكول العلاجي:"
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-[#062B49] text-slate-900 dark:text-white font-tajawal shadow-inner">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                    <span class="px-1.5 py-0.5 rounded bg-slate-200 dark:bg-slate-700 text-[10px] font-mono">EN</span>
                    Subtitle (EN):
                </label>
                <input type="text" name="section_settings[protocol_durations][subheading_en]" 
                       value="{{ $subheadingEn }}" 
                       placeholder="Controlled Protocol Pricing"
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-[#062B49] text-slate-900 dark:text-white font-mono shadow-inner">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                    <span class="px-1.5 py-0.5 rounded bg-teal-500/15 text-teal-600 text-[10px] font-mono">AR</span>
                    Subtitle (AR):
                </label>
                <input type="text" name="section_settings[protocol_durations][subheading_ar]" 
                       value="{{ $subheadingAr }}" 
                       dir="rtl" 
                       placeholder="تسعير بروتوكولي مدروس"
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-[#062B49] text-slate-900 dark:text-white font-tajawal shadow-inner">
            </div>
        </div>
    </div>

    <!-- Duration Tiers Repeater -->
    <div class="p-5 rounded-2xl bg-white dark:bg-[#031827] border-2 border-slate-200 dark:border-slate-700/80 shadow-sm space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-xl bg-teal-500/10 text-teal-600 flex items-center justify-center text-sm font-black">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </span>
                <div>
                    <h4 class="text-xs sm:text-sm font-black text-slate-900 dark:text-white">
                        {{ $isAr ? 'باقات وخطط مدة البروتوكول (Duration Plans & Discount Tiers)' : 'Duration Plans & Tier Discounts' }}
                    </h4>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">
                        {{ $isAr ? 'تحديد مضاعف العبوات (الكمية)، نسبة الخصم المبرمجة، وشارة التوفير لكل خطة' : 'Configure multipliers, tiered discounts, servings, and best-value highlight' }}
                    </p>
                </div>
            </div>
            <button type="button" onclick="addDurationTierRow()" 
                    class="px-3 py-1.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-xs font-black transition-all flex items-center gap-1.5 shadow-sm active:scale-95 cursor-pointer">
                <i class="fa-solid fa-plus text-[10px]"></i>
                <span>{{ $isAr ? 'إضافة خطة مدة' : 'Add Duration Plan' }}</span>
            </button>
        </div>

        <div class="space-y-4" id="durationTiersList">
            @foreach($tiers as $idx => $t)
                <div class="p-4 sm:p-5 rounded-2xl bg-slate-50 dark:bg-[#062B49] border-2 {{ !empty($t['is_recommended']) ? 'border-teal-500 dark:border-teal-400 shadow-md' : 'border-slate-200 dark:border-slate-700' }} space-y-3 duration-tier-row" data-index="{{ $idx }}">
                    <input type="hidden" name="section_settings[protocol_durations][tiers][{{ $idx }}][id]" value="{{ $t['id'] ?? ('tier_'.$idx) }}">
                    
                    <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-700 pb-2.5">
                        <div class="flex items-center gap-3">
                            <span class="px-2.5 py-0.5 rounded-full bg-teal-500/15 text-teal-700 dark:text-teal-300 font-mono text-[10px] font-black uppercase">
                                Tier #{{ $idx + 1 }}
                            </span>
                            <label class="inline-flex items-center gap-2 text-xs font-bold text-slate-700 dark:text-slate-200 cursor-pointer">
                                <input type="checkbox" name="section_settings[protocol_durations][tiers][{{ $idx }}][is_recommended]" 
                                       value="1" 
                                       onchange="syncProtocolDurationsPreview()"
                                       {{ !empty($t['is_recommended']) ? 'checked' : '' }} 
                                       class="w-4 h-4 rounded text-teal-600 focus:ring-teal-500">
                                <span>{{ $isAr ? 'تمييز كخطة موصى بها (Recommended Highlight)' : 'Mark as Recommended Plan' }}</span>
                            </label>
                        </div>
                        <button type="button" onclick="this.closest('.duration-tier-row').remove(); syncProtocolDurationsPreview();" 
                                class="text-rose-500 hover:text-rose-700 text-xs font-bold flex items-center gap-1 transition-colors cursor-pointer"
                                title="{{ $isAr ? 'حذف الخطة' : 'Remove Plan' }}">
                            <i class="fa-solid fa-trash-can text-[10px]"></i> <span>{{ $isAr ? 'حذف' : 'Remove' }}</span>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-1">{{ $isAr ? 'عنوان الخطة (EN):' : 'Plan Title (EN):' }}</label>
                            <input type="text" name="section_settings[protocol_durations][tiers][{{ $idx }}][title_en]" 
                                   value="{{ $t['title_en'] ?? '' }}" 
                                   oninput="syncProtocolDurationsPreview()"
                                   placeholder="30-Day Starter" 
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-[#031827] dark:text-white font-bold">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-1">{{ $isAr ? 'عنوان الخطة (AR):' : 'Plan Title (AR):' }}</label>
                            <input type="text" name="section_settings[protocol_durations][tiers][{{ $idx }}][title_ar]" 
                                   value="{{ $t['title_ar'] ?? '' }}" 
                                   oninput="syncProtocolDurationsPreview()"
                                   placeholder="بروتوكول 30 يوماً" 
                                   dir="rtl" 
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-[#031827] dark:text-white font-tajawal font-bold">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-1">{{ $isAr ? 'مضاعف العبوات (الكمية):' : 'Multiplier (Bottles):' }}</label>
                            <input type="number" min="1" max="24" 
                                   name="section_settings[protocol_durations][tiers][{{ $idx }}][multiplier]" 
                                   value="{{ $t['multiplier'] ?? 1 }}" 
                                   oninput="syncProtocolDurationsPreview()"
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-[#031827] dark:text-white font-mono font-bold">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-1">{{ $isAr ? 'نسبة الخصم (%):' : 'Discount (%):' }}</label>
                            <div class="relative">
                                <input type="number" step="0.5" min="0" max="100" 
                                       name="section_settings[protocol_durations][tiers][{{ $idx }}][discount_value]" 
                                       value="{{ $t['discount_value'] ?? 0 }}" 
                                       oninput="syncProtocolDurationsPreview()"
                                       class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-[#031827] dark:text-white font-mono font-bold">
                                <span class="absolute inset-y-0 end-3 flex items-center text-xs font-mono font-bold text-slate-400">%</span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 pt-1">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-1">{{ $isAr ? 'شارة التوفير (EN):' : 'Badge Tag (EN):' }}</label>
                            <input type="text" name="section_settings[protocol_durations][tiers][{{ $idx }}][badge_en]" 
                                   value="{{ $t['badge_en'] ?? '' }}" 
                                   oninput="syncProtocolDurationsPreview()"
                                   placeholder="SAVE 15%" 
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-[#031827] dark:text-white font-mono">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-1">{{ $isAr ? 'شارة التوفير (AR):' : 'Badge Tag (AR):' }}</label>
                            <input type="text" name="section_settings[protocol_durations][tiers][{{ $idx }}][badge_ar]" 
                                   value="{{ $t['badge_ar'] ?? '' }}" 
                                   oninput="syncProtocolDurationsPreview()"
                                   placeholder="توفير 15%" 
                                   dir="rtl" 
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-[#031827] dark:text-white font-tajawal">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-1">{{ $isAr ? 'ملاحظة الجرعة (EN):' : 'Serving Note (EN):' }}</label>
                            <input type="text" name="section_settings[protocol_durations][tiers][{{ $idx }}][serving_en]" 
                                   value="{{ $t['serving_en'] ?? '' }}" 
                                   placeholder="30 Tablets (Serving: 1 Tablet Daily)" 
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-[#031827] dark:text-white">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-1">{{ $isAr ? 'ملاحظة الجرعة (AR):' : 'Serving Note (AR):' }}</label>
                            <input type="text" name="section_settings[protocol_durations][tiers][{{ $idx }}][serving_ar]" 
                                   value="{{ $t['serving_ar'] ?? '' }}" 
                                   placeholder="30 قرصاً (الجرعة: قرص واحد)" 
                                   dir="rtl" 
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-[#031827] dark:text-white font-tajawal">
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
