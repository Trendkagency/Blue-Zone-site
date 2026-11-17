@php
    $bbData = $buyBoxData ?? [];
    $stockStatusEn = $bbData['stock_status_en'] ?? 'In Stock • Cold-Chain Ready';
    $stockStatusAr = $bbData['stock_status_ar'] ?? 'متوفر في المستودع • جاهز للشحن المبرد';
    $saveBadgeEn = $bbData['save_badge_en'] ?? 'SAVE 15% TODAY';
    $saveBadgeAr = $bbData['save_badge_ar'] ?? 'وفّر 15% اليوم';

    $btnAddCartEn = $bbData['btn_add_cart_en'] ?? 'ADD TO CLINICAL PROTOCOL';
    $btnAddCartAr = $bbData['btn_add_cart_ar'] ?? 'إضافة إلى السلة والبروتوكول';
    $btnBuyNowEn = $bbData['btn_buy_now_en'] ?? 'ORDER & INSTANT CHECKOUT';
    $btnBuyNowAr = $bbData['btn_buy_now_ar'] ?? 'طلب المستحضر والشراء السريع';
    $ctaSubtextEn = $bbData['cta_subtext_en'] ?? 'Clinically Verified Formulation • 100% Purity';
    $ctaSubtextAr = $bbData['cta_subtext_ar'] ?? 'تركيبة سريرية موثقة • نقاء دوائي 100%';

    $guaranteeDays = $bbData['guarantee_days'] ?? 30;
    $guaranteeTitleEn = $bbData['guarantee_title_en'] ?? 'Satisfaction Guarantee:';
    $guaranteeTitleAr = $bbData['guarantee_title_ar'] ?? 'ضمان الرضا الطبي:';
    $guaranteeTextEn = $bbData['guarantee_text_en'] ?? '30-Day Clinical Refund';
    $guaranteeTextAr = $bbData['guarantee_text_ar'] ?? '30 يوماً استرداد كامل';

    $trustBadges = $bbData['trust_badges'] ?? [];
@endphp

<div class="space-y-6">
    <!-- Stock Status & Price Badges Box -->
    <div class="p-5 rounded-2xl bg-white dark:bg-[#031827] border-2 border-slate-200 dark:border-slate-700/80 shadow-sm space-y-4">
        <div class="flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
            <span class="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-600 flex items-center justify-center text-sm font-black">
                <i class="fa-solid fa-tag"></i>
            </span>
            <div>
                <h4 class="text-xs sm:text-sm font-black text-slate-900 dark:text-white">
                    {{ $isAr ? 'حالة التوفر وشارة التوفير (Stock Status & Save Badges)' : 'Stock Status & Save Badges' }}
                </h4>
                <p class="text-[11px] text-slate-500 dark:text-slate-400">
                    {{ $isAr ? 'نصوص حالة توفر المستحضر وسلسلة التبريد وشارة التوفير بجوار السعر' : 'In-stock reassurance text & price promotional badge' }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                    <span class="px-1.5 py-0.5 rounded bg-slate-200 dark:bg-slate-700 text-[10px] font-mono">EN</span>
                    Stock Status Text (EN):
                </label>
                <input type="text" name="section_settings[buy_box][stock_status_en]" 
                       id="inputStockStatusEn"
                       value="{{ $stockStatusEn }}" 
                       oninput="syncBuyBoxPreview()"
                       placeholder="In Stock • Cold-Chain Ready"
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-[#062B49] text-slate-900 dark:text-white font-mono shadow-inner">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                    <span class="px-1.5 py-0.5 rounded bg-blue-500/15 text-blue-600 text-[10px] font-mono">AR</span>
                    Stock Status Text (AR):
                </label>
                <input type="text" name="section_settings[buy_box][stock_status_ar]" 
                       id="inputStockStatusAr"
                       value="{{ $stockStatusAr }}" 
                       oninput="syncBuyBoxPreview()"
                       dir="rtl" 
                       placeholder="متوفر في المستودع • جاهز للشحن المبرد"
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-[#062B49] text-slate-900 dark:text-white font-tajawal shadow-inner">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                    <span class="px-1.5 py-0.5 rounded bg-slate-200 dark:bg-slate-700 text-[10px] font-mono">EN</span>
                    Save Badge (EN):
                </label>
                <input type="text" name="section_settings[buy_box][save_badge_en]" 
                       id="inputSaveBadgeEn"
                       value="{{ $saveBadgeEn }}" 
                       oninput="syncBuyBoxPreview()"
                       placeholder="SAVE 15% TODAY"
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-[#062B49] text-slate-900 dark:text-white font-mono shadow-inner">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                    <span class="px-1.5 py-0.5 rounded bg-blue-500/15 text-blue-600 text-[10px] font-mono">AR</span>
                    Save Badge (AR):
                </label>
                <input type="text" name="section_settings[buy_box][save_badge_ar]" 
                       id="inputSaveBadgeAr"
                       value="{{ $saveBadgeAr }}" 
                       oninput="syncBuyBoxPreview()"
                       dir="rtl" 
                       placeholder="وفّر 15% اليوم"
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-[#062B49] text-slate-900 dark:text-white font-tajawal shadow-inner">
            </div>
        </div>
    </div>

    <!-- Commercial CTA Buttons Box -->
    <div class="p-5 rounded-2xl bg-white dark:bg-[#031827] border-2 border-slate-200 dark:border-slate-700/80 shadow-sm space-y-4">
        <div class="flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
            <span class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center text-sm font-black">
                <i class="fa-solid fa-cart-shopping"></i>
            </span>
            <div>
                <h4 class="text-xs sm:text-sm font-black text-slate-900 dark:text-white">
                    {{ $isAr ? 'أزرار الإجراء والطلب التجاري (Commercial CTA Buttons)' : 'Commercial Action & Checkout Buttons (CTA)' }}
                </h4>
                <p class="text-[11px] text-slate-500 dark:text-slate-400">
                    {{ $isAr ? 'تخصيص نص زر الإضافة للسلة وزر الشراء السريع والنص الموثق أسفلهما' : 'Customize Add-to-Cart, Direct Checkout, and reassurance directives' }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                    Add to Cart Button (EN):
                </label>
                <input type="text" name="section_settings[buy_box][btn_add_cart_en]" 
                       id="inputBtnCartEn"
                       value="{{ $btnAddCartEn }}" 
                       oninput="syncBuyBoxPreview()"
                       placeholder="ADD TO CLINICAL PROTOCOL"
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-[#062B49] text-slate-900 dark:text-white font-bold shadow-inner">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                    Add to Cart Button (AR):
                </label>
                <input type="text" name="section_settings[buy_box][btn_add_cart_ar]" 
                       id="inputBtnCartAr"
                       value="{{ $btnAddCartAr }}" 
                       oninput="syncBuyBoxPreview()"
                       dir="rtl" 
                       placeholder="إضافة إلى السلة والبروتوكول"
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-[#062B49] text-slate-900 dark:text-white font-tajawal font-bold shadow-inner">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                    Instant Buy Now Button (EN):
                </label>
                <input type="text" name="section_settings[buy_box][btn_buy_now_en]" 
                       value="{{ $btnBuyNowEn }}" 
                       placeholder="ORDER & INSTANT CHECKOUT"
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-[#062B49] text-slate-900 dark:text-white font-bold shadow-inner">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                    Instant Buy Now Button (AR):
                </label>
                <input type="text" name="section_settings[buy_box][btn_buy_now_ar]" 
                       value="{{ $btnBuyNowAr }}" 
                       dir="rtl" 
                       placeholder="طلب المستحضر والشراء السريع"
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-[#062B49] text-slate-900 dark:text-white font-tajawal font-bold shadow-inner">
            </div>
            <div class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-100 dark:border-slate-800">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">CTA Subtext (EN):</label>
                    <input type="text" name="section_settings[buy_box][cta_subtext_en]" 
                           value="{{ $ctaSubtextEn }}" 
                           placeholder="Clinically Verified Formulation • 100% Purity"
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-[#062B49] text-slate-900 dark:text-white shadow-inner">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">CTA Subtext (AR):</label>
                    <input type="text" name="section_settings[buy_box][cta_subtext_ar]" 
                           value="{{ $ctaSubtextAr }}" 
                           dir="rtl" 
                           placeholder="تركيبة سريرية موثقة • نقاء دوائي 100%"
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-[#062B49] text-slate-900 dark:text-white font-tajawal shadow-inner">
                </div>
            </div>
        </div>
    </div>

    <!-- Clinical Guarantee Box -->
    <div class="p-5 rounded-2xl bg-white dark:bg-[#031827] border-2 border-slate-200 dark:border-slate-700/80 shadow-sm space-y-4">
        <div class="flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
            <span class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center text-sm font-black">
                <i class="fa-solid fa-shield-heart"></i>
            </span>
            <div>
                <h4 class="text-xs sm:text-sm font-black text-slate-900 dark:text-white">
                    {{ $isAr ? 'سياسة ضمان الرضا السريري (Satisfaction Guarantee Policy)' : 'Clinical Satisfaction Guarantee Policy' }}
                </h4>
                <p class="text-[11px] text-slate-500 dark:text-slate-400">
                    {{ $isAr ? 'عدد أيام الضمان، العنوان الترويجي، والشارة الموثقة أسفل صندوق الشراء' : 'Guarantee refund duration, titles, and reassuring seals' }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                    {{ $isAr ? 'مدة الضمان (أيام):' : 'Guarantee Days:' }}
                </label>
                <input type="number" min="0" max="365" 
                       name="section_settings[buy_box][guarantee_days]" 
                       value="{{ $guaranteeDays }}" 
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-[#062B49] text-slate-900 dark:text-white font-mono font-bold shadow-inner">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Guarantee Title (EN):</label>
                <input type="text" name="section_settings[buy_box][guarantee_title_en]" 
                       value="{{ $guaranteeTitleEn }}" 
                       placeholder="Satisfaction Guarantee:"
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-[#062B49] text-slate-900 dark:text-white shadow-inner">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Guarantee Title (AR):</label>
                <input type="text" name="section_settings[buy_box][guarantee_title_ar]" 
                       value="{{ $guaranteeTitleAr }}" 
                       dir="rtl" 
                       placeholder="ضمان الرضا الطبي:"
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-[#062B49] text-slate-900 dark:text-white font-tajawal shadow-inner">
            </div>
            <div class="sm:col-span-1"></div>
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Badge Text (EN):</label>
                <input type="text" name="section_settings[buy_box][guarantee_text_en]" 
                       id="inputGuaranteeTextEn"
                       value="{{ $guaranteeTextEn }}" 
                       oninput="syncBuyBoxPreview()"
                       placeholder="30-Day Clinical Refund"
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-[#062B49] text-slate-900 dark:text-white font-mono font-bold shadow-inner">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Badge Text (AR):</label>
                <input type="text" name="section_settings[buy_box][guarantee_text_ar]" 
                       id="inputGuaranteeTextAr"
                       value="{{ $guaranteeTextAr }}" 
                       oninput="syncBuyBoxPreview()"
                       dir="rtl" 
                       placeholder="30 يوماً استرداد كامل"
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-[#062B49] text-slate-900 dark:text-white font-tajawal font-bold shadow-inner">
            </div>
        </div>
    </div>

    <!-- Trust Badges Repeater -->
    <div class="p-5 rounded-2xl bg-white dark:bg-[#031827] border-2 border-slate-200 dark:border-slate-700/80 shadow-sm space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center text-sm font-black">
                    <i class="fa-solid fa-award"></i>
                </span>
                <div>
                    <h4 class="text-xs sm:text-sm font-black text-slate-900 dark:text-white">
                        {{ $isAr ? 'شارات الثقة الدوائية (Clinical Trust Badges)' : 'Clinical Trust Badges & Assurances' }}
                    </h4>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">
                        {{ $isAr ? 'الشارات التوضيحية أسفل زر الشراء (HPLC، خلو من الملوثات، نباتي غير معدل)' : 'Reassurance badges beneath commercial CTA buttons' }}
                    </p>
                </div>
            </div>
            <button type="button" onclick="addTrustBadgeRow()" 
                    class="px-3 py-1.5 rounded-xl bg-[#0A4F78] hover:bg-[#062B49] text-white text-xs font-black transition-all flex items-center gap-1.5 shadow-sm active:scale-95 cursor-pointer">
                <i class="fa-solid fa-plus text-[10px]"></i>
                <span>{{ $isAr ? 'إضافة شارة ثقة' : 'Add Trust Badge' }}</span>
            </button>
        </div>

        <div class="space-y-3" id="trustBadgesList">
            @foreach($trustBadges as $idx => $tb)
                <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-[#062B49] border border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row items-stretch sm:items-center gap-3 trust-badge-row" data-index="{{ $idx }}">
                    <input type="hidden" name="section_settings[buy_box][trust_badges][{{ $idx }}][icon]" value="{{ $tb['icon'] ?? 'fa-check-double' }}">
                    <div class="w-full sm:w-1/2">
                        <label class="block text-[10px] font-bold text-slate-500 mb-1">Text (EN):</label>
                        <input type="text" name="section_settings[buy_box][trust_badges][{{ $idx }}][text_en]" 
                               value="{{ $tb['text_en'] ?? '' }}" 
                               oninput="syncBuyBoxPreview()"
                               class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-[#031827] dark:text-white font-bold">
                    </div>
                    <div class="w-full sm:w-1/2">
                        <label class="block text-[10px] font-bold text-slate-500 mb-1">Text (AR):</label>
                        <input type="text" name="section_settings[buy_box][trust_badges][{{ $idx }}][text_ar]" 
                               value="{{ $tb['text_ar'] ?? '' }}" 
                               oninput="syncBuyBoxPreview()"
                               dir="rtl" 
                               class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-[#031827] dark:text-white font-tajawal font-bold">
                    </div>
                    <button type="button" onclick="this.closest('.trust-badge-row').remove(); syncBuyBoxPreview();" 
                            class="w-8 h-8 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 flex items-center justify-center shrink-0 self-end sm:self-center transition-colors cursor-pointer"
                            title="{{ $isAr ? 'حذف الشارة' : 'Remove' }}">
                        <i class="fa-solid fa-trash-can text-xs"></i>
                    </button>
                </div>
            @endforeach
        </div>
    </div>
</div>
