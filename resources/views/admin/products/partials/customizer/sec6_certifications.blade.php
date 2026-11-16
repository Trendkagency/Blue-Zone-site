@php
    $sec6Conf = $secData['sec6'] ?? [];
    $sec6Certs = $sec6Conf['certificates'] ?? null;
    if (!is_array($sec6Certs) || empty($sec6Certs)) {
        $sec6Certs = [
            ['icon' => 'fa-award', 'name_en' => 'cGMP Manufacturing Facility', 'name_ar' => 'منشأة معتمدة وفق معايير cGMP', 'standard' => 'FDA 21 CFR Part 111'],
            ['icon' => 'fa-check-double', 'name_en' => 'Triple HPLC Assay Tested', 'name_ar' => 'فحص كيميائي ونقاء HPLC ثلاثي', 'standard' => 'ISO/IEC 17025 Verified'],
            ['icon' => 'fa-leaf', 'name_en' => '100% Non-GMO & Heavy Metal Free', 'name_ar' => 'خالٍ من التعديل الوراثي والمعادن الثقيلة', 'standard' => 'USP Monograph Standard'],
        ];
    }

    $specs = $sec6Conf['specs'] ?? [];
    $supplyEn = $specs['supply_volume_en'] ?? '';
    $supplyAr = $specs['supply_volume_ar'] ?? '';
    $facilityEn = $specs['facility_standards_en'] ?? '';
    $facilityAr = $specs['facility_standards_ar'] ?? '';
    $matrixEn = $specs['delivery_matrix_en'] ?? '';
    $matrixAr = $specs['delivery_matrix_ar'] ?? '';
    $shelfEn = $specs['shelf_life_en'] ?? '';
    $shelfAr = $specs['shelf_life_ar'] ?? '';
@endphp

<div class="space-y-6">
    <!-- 1. QUALITY CERTIFICATES REPEATER -->
    <div class="p-5 rounded-2xl bg-white dark:bg-[#031827] border-2 border-indigo-500/30 shadow-sm space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-xl bg-indigo-500/10 text-indigo-600 flex items-center justify-center text-sm font-black">
                    <i class="fa-solid fa-award"></i>
                </span>
                <div>
                    <h4 class="text-xs sm:text-sm font-black text-slate-900 dark:text-white">
                        {{ $isAr ? 'الشهادات والاعتمادات الدوائية وتراخيص الجودة (cGMP Certifications)' : 'Bio-Certifications & Quality Assurances' }}
                    </h4>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">
                        {{ $isAr ? 'تراخيص cGMP، اعتمادات ISO، وتقارير الفحص المخبري المستقل للنقاء الدوائي' : 'Documented ISO/cGMP standards, purity assay audits, and third-party seals' }}
                    </p>
                </div>
            </div>
            <button type="button" onclick="addCertificateRow()" 
                    class="px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black transition-all flex items-center gap-1.5 shadow-sm active:scale-95 cursor-pointer">
                <i class="fa-solid fa-plus text-[10px]"></i>
                <span>{{ $isAr ? 'إضافة شهادة اعتماد' : 'Add Certificate' }}</span>
            </button>
        </div>

        <div class="space-y-3" id="sec6CertificatesList">
            @foreach($sec6Certs as $idx => $c)
                <div class="p-4 rounded-xl bg-slate-50 dark:bg-[#062B49] border border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row items-stretch sm:items-center gap-3 cert-row" data-index="{{ $idx }}">
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-2 flex-1">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Icon (FA):</label>
                            <input type="text" name="section_settings[sec6][certificates][{{ $idx }}][icon]" 
                                   value="{{ $c['icon'] ?? 'fa-award' }}" 
                                   class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-[#031827] dark:text-white font-mono">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Certificate Name (EN):</label>
                            <input type="text" name="section_settings[sec6][certificates][{{ $idx }}][name_en]" 
                                   value="{{ $c['name_en'] ?? '' }}" 
                                   placeholder="cGMP Certified Facility"
                                   class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-[#031827] dark:text-white font-bold">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-0.5">اسم الشهادة (AR):</label>
                            <input type="text" name="section_settings[sec6][certificates][{{ $idx }}][name_ar]" 
                                   value="{{ $c['name_ar'] ?? '' }}" 
                                   dir="rtl" 
                                   placeholder="منشأة معتمدة وفق cGMP"
                                   class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-[#031827] dark:text-white font-tajawal font-bold">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Standard / Code:</label>
                            <input type="text" name="section_settings[sec6][certificates][{{ $idx }}][standard]" 
                                   value="{{ $c['standard'] ?? '' }}" 
                                   placeholder="FDA 21 CFR Part 111"
                                   class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-[#031827] dark:text-white font-mono">
                        </div>
                    </div>
                    <button type="button" onclick="this.closest('.cert-row').remove()" 
                            class="w-8 h-8 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 flex items-center justify-center shrink-0 self-end sm:self-center transition-colors cursor-pointer"
                            title="{{ $isAr ? 'حذف الشهادة' : 'Remove' }}">
                        <i class="fa-solid fa-trash-can text-xs"></i>
                    </button>
                </div>
            @endforeach
        </div>
    </div>

    <!-- 2. TECHNICAL & SUPPLY SPECIFICATIONS GRID -->
    <div class="p-5 rounded-2xl bg-white dark:bg-[#031827] border-2 border-indigo-500/30 shadow-sm space-y-4">
        <div class="flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
            <span class="w-8 h-8 rounded-xl bg-indigo-500/10 text-indigo-600 flex items-center justify-center text-sm font-black">
                <i class="fa-solid fa-microscope"></i>
            </span>
            <div>
                <h4 class="text-xs sm:text-sm font-black text-slate-900 dark:text-white">
                    {{ $isAr ? 'المواصفات الفنية والتعبئة (Technical Monograph Specifications)' : 'Technical Monograph & Supply Specifications' }}
                </h4>
                <p class="text-[11px] text-slate-500 dark:text-slate-400">
                    {{ $isAr ? 'حجم العبوة، جهة التصنيع، صيغة الكبسولة، والعمر التخزيني (فارغ = يرث تلقائياً من بيانات المنتج)' : 'Supply Volume, Facility Standards, Delivery Matrix, and Shelf Life' }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Facility Standards -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                    {{ $isAr ? 'جهة التصنيع المعتمدة (EN / AR):' : 'Facility Standards (EN / AR):' }}
                </label>
                <div class="grid grid-cols-2 gap-2">
                    <input type="text" name="section_settings[sec6][specs][facility_standards_en]" 
                           value="{{ $facilityEn }}" 
                           placeholder="cGMP & FDA Registered" 
                           class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-[#062B49] text-slate-900 dark:text-white">
                    <input type="text" name="section_settings[sec6][specs][facility_standards_ar]" 
                           value="{{ $facilityAr }}" 
                           dir="rtl"
                           placeholder="منشأة مسجلة لدى هيئة الغذاء والدواء" 
                           class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-[#062B49] text-slate-900 dark:text-white font-tajawal">
                </div>
            </div>

            <!-- Delivery Matrix -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                    {{ $isAr ? 'صيغة الكبسولة (Delivery Matrix):' : 'Delivery Matrix (EN / AR):' }}
                </label>
                <div class="grid grid-cols-2 gap-2">
                    <input type="text" name="section_settings[sec6][specs][delivery_matrix_en]" 
                           value="{{ $matrixEn }}" 
                           placeholder="Enteric Veg Capsule" 
                           class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-[#062B49] text-slate-900 dark:text-white">
                    <input type="text" name="section_settings[sec6][specs][delivery_matrix_ar]" 
                           value="{{ $matrixAr }}" 
                           dir="rtl"
                           placeholder="كبسولات نباتية معوية" 
                           class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-[#062B49] text-slate-900 dark:text-white font-tajawal">
                </div>
            </div>

            <!-- Shelf Life -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                    {{ $isAr ? 'العمر التخزيني (Shelf Life):' : 'Shelf Life Stability (EN / AR):' }}
                </label>
                <div class="grid grid-cols-2 gap-2">
                    <input type="text" name="section_settings[sec6][specs][shelf_life_en]" 
                           value="{{ $shelfEn }}" 
                           placeholder="24 Months Unopened" 
                           class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-[#062B49] text-slate-900 dark:text-white">
                    <input type="text" name="section_settings[sec6][specs][shelf_life_ar]" 
                           value="{{ $shelfAr }}" 
                           dir="rtl"
                           placeholder="24 شهراً في العبوة المغلقة" 
                           class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-[#062B49] text-slate-900 dark:text-white font-tajawal">
                </div>
            </div>

            <!-- Supply Volume Override -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                    {{ $isAr ? 'تخصيص حجم العبوة (اختياري):' : 'Supply Volume Override (Optional):' }}
                </label>
                <div class="grid grid-cols-2 gap-2">
                    <input type="text" name="section_settings[sec6][specs][supply_volume_en]" 
                           value="{{ $supplyEn }}" 
                           placeholder="e.g. 60 Vegetable Capsules" 
                           class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-[#062B49] text-slate-900 dark:text-white">
                    <input type="text" name="section_settings[sec6][specs][supply_volume_ar]" 
                           value="{{ $supplyAr }}" 
                           dir="rtl"
                           placeholder="مثال: 60 كبسولة نباتية" 
                           class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-[#062B49] text-slate-900 dark:text-white font-tajawal">
                </div>
            </div>
        </div>
    </div>
</div>
