@php
    $sec1Conf = $secData['sec1'] ?? [];
    $sec1Items = $sec1Conf['items'] ?? null;
    if (!is_array($sec1Items) || empty($sec1Items)) {
        $sec1Items = [
            ['compound_en' => 'Ginkgo Biloba Extract (50:1)', 'compound_ar' => 'مستخلص الجنكة بيلوبا (50:1)', 'potency' => '120 mg', 'purity' => '24% Flavone Glycosides', 'role_en' => 'Cerebral microcirculation & NAD+ support', 'role_ar' => 'تنشيط الدورة الدموية الدماغية والطاقة'],
            ['compound_en' => 'L-Arginine Bio-Active', 'compound_ar' => 'إل-أرجينين النقي', 'potency' => '250 mg', 'purity' => '99.5% Pure', 'role_en' => 'Nitric oxide synthesis & endothelial health', 'role_ar' => 'تحفيز أكسيد النيتريك ومرونة الأوعية الدموية'],
            ['compound_en' => 'Coenzyme Q10 (Ubiquinone)', 'compound_ar' => 'مساعد الإنزيم كيو 10', 'potency' => '50 mg', 'purity' => 'Fermented Bio-Identical', 'role_en' => 'Mitochondrial ATP cellular bioenergetics', 'role_ar' => 'دعم إنتاج طاقة ATP الخلوية في الميتوكوندريا'],
        ];
    }
@endphp

<div class="p-5 rounded-2xl bg-white dark:bg-[#031827] border-2 border-emerald-500/30 shadow-sm space-y-4">
    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
        <div class="flex items-center gap-2">
            <span class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center text-sm font-black">
                <i class="fa-solid fa-flask-vial"></i>
            </span>
            <div>
                <h4 class="text-xs sm:text-sm font-black text-slate-900 dark:text-white">
                    {{ $isAr ? 'جدول المكونات الفعالة والجرعات المعيارية (Active Ingredients Matrix)' : 'Active Ingredients Matrix & Potencies' }}
                </h4>
                <p class="text-[11px] text-slate-500 dark:text-slate-400">
                    {{ $isAr ? 'المركبات النشطة، العيارات الدوائية، النقاء المخبري، والدور الحيوي المعياري' : 'Standardized compounds, therapeutic potencies, purity, and biological roles' }}
                </p>
            </div>
        </div>
        <button type="button" onclick="addIngredientRow()" 
                class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black transition-all flex items-center gap-1.5 shadow-sm active:scale-95 cursor-pointer">
            <i class="fa-solid fa-plus text-[10px]"></i>
            <span>{{ $isAr ? 'إضافة مادة فعالة' : 'Add Compound' }}</span>
        </button>
    </div>

    <div class="space-y-3" id="sec1IngredientsList">
        @foreach($sec1Items as $idx => $it)
            <div class="p-4 rounded-xl bg-slate-50 dark:bg-[#062B49] border border-slate-200 dark:border-slate-700 flex flex-col lg:flex-row items-stretch lg:items-center gap-3 ingredient-row" data-index="{{ $idx }}">
                <div class="grid grid-cols-2 lg:grid-cols-6 gap-2 flex-1">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Compound (EN):</label>
                        <input type="text" name="section_settings[sec1][items][{{ $idx }}][compound_en]" 
                               value="{{ $it['compound_en'] ?? '' }}" 
                               placeholder="e.g. Ginkgo Biloba"
                               class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-[#031827] dark:text-white font-bold">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 mb-0.5">المادة (AR):</label>
                        <input type="text" name="section_settings[sec1][items][{{ $idx }}][compound_ar]" 
                               value="{{ $it['compound_ar'] ?? '' }}" 
                               dir="rtl" 
                               placeholder="مثال: خلاصة الجنكة"
                               class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-[#031827] dark:text-white font-tajawal font-bold">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Potency:</label>
                        <input type="text" name="section_settings[sec1][items][{{ $idx }}][potency]" 
                               value="{{ $it['potency'] ?? '' }}" 
                               placeholder="120 mg" 
                               class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-[#031827] dark:text-white font-mono">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Purity:</label>
                        <input type="text" name="section_settings[sec1][items][{{ $idx }}][purity]" 
                               value="{{ $it['purity'] ?? '' }}" 
                               placeholder="99.5% Pure" 
                               class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-[#031827] dark:text-white font-mono">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Role (EN):</label>
                        <input type="text" name="section_settings[sec1][items][{{ $idx }}][role_en]" 
                               value="{{ $it['role_en'] ?? '' }}" 
                               placeholder="Cellular energy" 
                               class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-[#031827] dark:text-white">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 mb-0.5">الدور الحيوي (AR):</label>
                        <input type="text" name="section_settings[sec1][items][{{ $idx }}][role_ar]" 
                               value="{{ $it['role_ar'] ?? '' }}" 
                               dir="rtl" 
                               placeholder="تنشيط الطاقة الخلوية" 
                               class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-[#031827] dark:text-white font-tajawal">
                    </div>
                </div>
                <button type="button" onclick="this.closest('.ingredient-row').remove()" 
                        class="w-8 h-8 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 flex items-center justify-center shrink-0 self-end lg:self-center transition-colors cursor-pointer"
                        title="{{ $isAr ? 'حذف المادة' : 'Remove' }}">
                    <i class="fa-solid fa-trash-can text-xs"></i>
                </button>
            </div>
        @endforeach
    </div>
</div>
