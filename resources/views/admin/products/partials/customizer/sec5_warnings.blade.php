@php
    $sec5Conf = $secData['sec5'] ?? [];
    $warningsEn = $sec5Conf['warnings_en'] ?? '';
    $warningsAr = $sec5Conf['warnings_ar'] ?? '';
    $contraEn = $sec5Conf['contraindications_en'] ?? '';
    $contraAr = $sec5Conf['contraindications_ar'] ?? '';

    $sec5PurityBadges = $sec5Conf['purity_badges'] ?? null;
    if (!is_array($sec5PurityBadges) || empty($sec5PurityBadges)) {
        $sec5PurityBadges = [
            ['icon' => 'fa-ban', 'text_en' => 'Gluten-Free', 'text_ar' => 'خالٍ من الجلوتين'],
            ['icon' => 'fa-leaf', 'text_en' => 'Soy-Free', 'text_ar' => 'خالٍ من الصويا'],
            ['icon' => 'fa-droplet-slash', 'text_en' => '100% Vegan', 'text_ar' => 'خالٍ من المشتقات الحيوانية'],
            ['icon' => 'fa-flask', 'text_en' => 'No Preservatives', 'text_ar' => 'دون مواد حافظة'],
        ];
    }
@endphp

<div class="space-y-6">
    <!-- 1. WARNINGS & DIRECTIVES -->
    <div class="p-5 rounded-2xl bg-white dark:bg-[#031827] border-2 border-emerald-500/30 shadow-sm space-y-5">
        <div class="flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
            <span class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center text-sm font-black">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </span>
            <div>
                <h4 class="text-xs sm:text-sm font-black text-slate-900 dark:text-white">
                    {{ $isAr ? 'إرشادات السلامة الصيدلانية، التحذيرات وموانع الاستعمال' : 'Safety Profile, Warnings & Contraindications' }}
                </h4>
                <p class="text-[11px] text-slate-500 dark:text-slate-400">
                    {{ $isAr ? 'تنبيهات الاستخدام، إرشادات الحوامل والمرضعات، وتعارض الأدوية' : 'Pharmacovigilance directives, pregnancy advisories, and medical contraindications' }}
                </p>
            </div>
        </div>

        <!-- Warnings Row -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                    <span class="px-1.5 py-0.5 rounded bg-slate-200 dark:bg-slate-700 text-[10px] font-mono">EN</span>
                    Safety Warnings & Precautions (EN):
                </label>
                <textarea name="section_settings[sec5][warnings_en]" 
                          rows="3" 
                          placeholder="Do not exceed recommended dose. Consult physician if pregnant or nursing..."
                          class="w-full p-3 text-xs text-slate-900 dark:text-white bg-slate-50 dark:bg-[#062B49] rounded-xl border border-slate-300 dark:border-slate-600 outline-none focus:ring-2 focus:ring-emerald-500/30 transition-all shadow-inner leading-relaxed">{{ $warningsEn }}</textarea>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                    <span class="px-1.5 py-0.5 rounded bg-emerald-500/15 text-emerald-600 text-[10px] font-mono">AR</span>
                    التحذيرات وإرشادات السلامة (AR):
                </label>
                <textarea name="section_settings[sec5][warnings_ar]" 
                          rows="3" 
                          dir="rtl" 
                          placeholder="لا تتجاوز الجرعة الموصى بها. يرجى استشارة الطبيب في حالات الحمل أو الرضاعة..."
                          class="w-full p-3 text-xs text-slate-900 dark:text-white bg-slate-50 dark:bg-[#062B49] rounded-xl border border-slate-300 dark:border-slate-600 outline-none focus:ring-2 focus:ring-emerald-500/30 transition-all font-tajawal shadow-inner leading-relaxed">{{ $warningsAr }}</textarea>
            </div>
        </div>

        <!-- Contraindications Row -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2 border-t border-slate-100 dark:border-slate-800">
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                    <span class="px-1.5 py-0.5 rounded bg-slate-200 dark:bg-slate-700 text-[10px] font-mono">EN</span>
                    Contraindications & Allergens (EN):
                </label>
                <textarea name="section_settings[sec5][contraindications_en]" 
                          rows="3" 
                          placeholder="Not recommended for individuals with sensitivity to active bio-compounds..."
                          class="w-full p-3 text-xs text-slate-900 dark:text-white bg-slate-50 dark:bg-[#062B49] rounded-xl border border-slate-300 dark:border-slate-600 outline-none focus:ring-2 focus:ring-emerald-500/30 transition-all shadow-inner leading-relaxed">{{ $contraEn }}</textarea>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                    <span class="px-1.5 py-0.5 rounded bg-emerald-500/15 text-emerald-600 text-[10px] font-mono">AR</span>
                    موانع الاستعمال ومسببات الحساسية (AR):
                </label>
                <textarea name="section_settings[sec5][contraindications_ar]" 
                          rows="3" 
                          dir="rtl" 
                          placeholder="لا ينصح به للأشخاص الذين يعانون من حساسية مفرطة لأي من المكونات النشطة..."
                          class="w-full p-3 text-xs text-slate-900 dark:text-white bg-slate-50 dark:bg-[#062B49] rounded-xl border border-slate-300 dark:border-slate-600 outline-none focus:ring-2 focus:ring-emerald-500/30 transition-all font-tajawal shadow-inner leading-relaxed">{{ $contraAr }}</textarea>
            </div>
        </div>
    </div>

    <!-- 2. PURITY & ALLERGEN BADGES GRID (4 BADGES) -->
    <div class="p-5 rounded-2xl bg-white dark:bg-[#031827] border-2 border-emerald-500/30 shadow-sm space-y-4">
        <div class="flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
            <span class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center text-sm font-black">
                <i class="fa-solid fa-leaf"></i>
            </span>
            <div>
                <h4 class="text-xs sm:text-sm font-black text-slate-900 dark:text-white">
                    {{ $isAr ? 'شارات النقاء والتحقق من الحساسية (Purity & Allergen Badges)' : 'Purity & Allergen Verification Badges' }}
                </h4>
                <p class="text-[11px] text-slate-500 dark:text-slate-400">
                    {{ $isAr ? 'البطاقات الأربعة المعروضة بأسفل قسم السلامة (خالٍ من الجلوتين، الصويا، نباتي، إلخ)' : '4 verification badges displayed at bottom: Gluten-Free, Soy-Free, 100% Vegan, No Preservatives' }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            @foreach($sec5PurityBadges as $idx => $badge)
                <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-[#062B49] border border-slate-200 dark:border-slate-700 space-y-2">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Icon (FA):</label>
                        <input type="text" name="section_settings[sec5][purity_badges][{{ $idx }}][icon]" 
                               value="{{ $badge['icon'] ?? 'fa-leaf' }}" 
                               class="w-full px-2.5 py-1 text-xs rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-[#031827] dark:text-white font-mono">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Text (EN):</label>
                        <input type="text" name="section_settings[sec5][purity_badges][{{ $idx }}][text_en]" 
                               value="{{ $badge['text_en'] ?? '' }}" 
                               placeholder="Gluten-Free"
                               class="w-full px-2.5 py-1 text-xs rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-[#031827] dark:text-white font-bold">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 mb-0.5">النص (AR):</label>
                        <input type="text" name="section_settings[sec5][purity_badges][{{ $idx }}][text_ar]" 
                               value="{{ $badge['text_ar'] ?? '' }}" 
                               dir="rtl"
                               placeholder="خالٍ من الجلوتين"
                               class="w-full px-2.5 py-1 text-xs rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-[#031827] dark:text-white font-tajawal font-bold">
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
