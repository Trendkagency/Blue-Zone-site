@php
    $hoData = $heroOverlaysData ?? [];
    $watermarkEnabled = !isset($hoData['watermark_enabled']) || (bool) $hoData['watermark_enabled'];
    $watermarkEn = $hoData['watermark_en'] ?? 'A Sharper You';
    $watermarkAr = $hoData['watermark_ar'] ?? 'نسخة أكثر صفاءً وتركيزاً';
    $badges = $hoData['badges'] ?? [];
@endphp

<div class="space-y-6">
    <!-- Watermark Settings Box -->
    <div class="p-5 rounded-2xl bg-white dark:bg-[#031827] border-2 border-slate-200 dark:border-slate-700/80 shadow-sm space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-xl bg-sky-500/10 text-sky-500 flex items-center justify-center text-sm font-black">
                    <i class="fa-solid fa-water"></i>
                </span>
                <div>
                    <h4 class="text-xs sm:text-sm font-black text-slate-900 dark:text-white">
                        {{ $isAr ? 'العلامة المائية الخلفية لمعرض الصور (Hero Watermark)' : 'Hero Background Watermark' }}
                    </h4>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">
                        {{ $isAr ? 'نص العلامة المائية الشفافة أسفل صورة المنتج الرئيسية' : 'Stylized transparent typography overlay beneath hero image' }}
                    </p>
                </div>
            </div>
            <label class="bz-switch-pill {{ $watermarkEnabled ? 'is-active' : 'is-inactive' }}">
                <input type="hidden" name="section_settings[hero_overlays][watermark_enabled]" value="0">
                <input type="checkbox" name="section_settings[hero_overlays][watermark_enabled]" value="1" 
                       class="sr-only" onchange="handleSimpleSwitch(this)" {{ $watermarkEnabled ? 'checked' : '' }}>
                <span class="bz-switch-thumb">
                    <i class="fa-solid {{ $watermarkEnabled ? 'fa-check' : 'fa-xmark' }}"></i>
                </span>
                <span class="switch-label">{{ $isAr ? 'تفعيل' : 'Active' }}</span>
            </label>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                    <span class="px-1.5 py-0.5 rounded bg-slate-200 dark:bg-slate-700 text-[10px] font-mono">EN</span>
                    {{ $isAr ? 'نص العلامة المائية (بالإنجليزية):' : 'Watermark Text (EN):' }}
                </label>
                <input type="text" name="section_settings[hero_overlays][watermark_en]" 
                       id="inputHeroWatermarkEn"
                       value="{{ $watermarkEn }}" 
                       oninput="syncHeroOverlaysPreview()"
                       placeholder="A Sharper You"
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-[#062B49] text-slate-900 dark:text-white font-mono shadow-inner">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                    <span class="px-1.5 py-0.5 rounded bg-sky-500/15 text-sky-600 text-[10px] font-mono">AR</span>
                    {{ $isAr ? 'نص العلامة المائية (بالعربية):' : 'Watermark Text (AR):' }}
                </label>
                <input type="text" name="section_settings[hero_overlays][watermark_ar]" 
                       id="inputHeroWatermarkAr"
                       value="{{ $watermarkAr }}" 
                       oninput="syncHeroOverlaysPreview()"
                       dir="rtl" 
                       placeholder="نسخة أكثر صفاءً وتركيزاً"
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-[#062B49] text-slate-900 dark:text-white font-tajawal shadow-inner">
            </div>
        </div>
    </div>

    <!-- Floating Badges Repeater -->
    <div class="p-5 rounded-2xl bg-white dark:bg-[#031827] border-2 border-slate-200 dark:border-slate-700/80 shadow-sm space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <div class="flex items-center gap-2">
                <span class="w-8 h-8 rounded-xl bg-sky-500/10 text-sky-500 flex items-center justify-center text-sm font-black">
                    <i class="fa-solid fa-shapes"></i>
                </span>
                <div>
                    <h4 class="text-xs sm:text-sm font-black text-slate-900 dark:text-white">
                        {{ $isAr ? 'الشارات التفاعلية العائمة على الصورة (Floating Image Badges)' : 'Floating Image Badges & Seals' }}
                    </h4>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">
                        {{ $isAr ? 'شارات الفوائد السريرية، ختم فحص HPLC، ومؤشرات الجودة المتراكبة على الصورة' : 'Overlaid benefit pills, purity seals, and clinical highlights' }}
                    </p>
                </div>
            </div>
            <button type="button" onclick="addOverlayBadgeRow()" 
                    class="px-3 py-1.5 rounded-xl bg-[#0ea5e9] hover:bg-[#0284c7] text-white text-xs font-black transition-all flex items-center gap-1.5 shadow-sm active:scale-95 cursor-pointer">
                <i class="fa-solid fa-plus text-[10px]"></i>
                <span>{{ $isAr ? 'إضافة شارة عائمة' : 'Add Badge' }}</span>
            </button>
        </div>

        <div class="space-y-3" id="heroOverlayBadgesList">
            @forelse($badges as $idx => $b)
                <div class="p-4 rounded-xl bg-slate-50 dark:bg-[#062B49] border border-slate-200 dark:border-slate-700 flex flex-col md:flex-row items-stretch md:items-center gap-3 overlay-badge-row" data-index="{{ $idx }}">
                    <input type="hidden" name="section_settings[hero_overlays][badges][{{ $idx }}][id]" value="{{ $b['id'] ?? ('b_'.$idx) }}">
                    
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-2 flex-1">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-1">{{ $isAr ? 'الموضع:' : 'Position:' }}</label>
                            <select name="section_settings[hero_overlays][badges][{{ $idx }}][position]" 
                                    onchange="syncHeroOverlaysPreview()"
                                    class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-[#031827] dark:text-white font-mono">
                                <option value="top_right" {{ ($b['position'] ?? '') === 'top_right' ? 'selected' : '' }}>Top Right</option>
                                <option value="bottom_left" {{ ($b['position'] ?? '') === 'bottom_left' ? 'selected' : '' }}>Bottom Left</option>
                                <option value="top_left" {{ ($b['position'] ?? '') === 'top_left' ? 'selected' : '' }}>Top Left</option>
                                <option value="bottom_right" {{ ($b['position'] ?? '') === 'bottom_right' ? 'selected' : '' }}>Bottom Right</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-1">{{ $isAr ? 'النمط:' : 'Style:' }}</label>
                            <select name="section_settings[hero_overlays][badges][{{ $idx }}][style]" 
                                    class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-[#031827] dark:text-white font-mono">
                                <option value="pill" {{ ($b['style'] ?? '') === 'pill' ? 'selected' : '' }}>Pill Badge</option>
                                <option value="pulse_dot" {{ ($b['style'] ?? '') === 'pulse_dot' ? 'selected' : '' }}>Pulse Dot Seal</option>
                                <option value="solid" {{ ($b['style'] ?? '') === 'solid' ? 'selected' : '' }}>Solid Tag</option>
                            </select>
                        </div>
                        <div>
                            @include('admin.products.partials.customizer.icon_selector', [
                                'name' => "section_settings[hero_overlays][badges][{$idx}][icon]",
                                'value' => $b['icon'] ?? 'fa-circle-check',
                                'label' => $isAr ? 'الأيقونة (FA):' : 'Icon (FA):',
                                'onchange' => 'syncHeroOverlaysPreview()',
                            ])
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-1">{{ $isAr ? 'النص (EN):' : 'Text (EN):' }}</label>
                            <input type="text" name="section_settings[hero_overlays][badges][{{ $idx }}][text_en]" 
                                   value="{{ $b['text_en'] ?? '' }}" 
                                   oninput="syncHeroOverlaysPreview()"
                                   placeholder="SUPPORTS MEMORY"
                                   class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-[#031827] dark:text-white font-bold">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-1">{{ $isAr ? 'النص (AR):' : 'Text (AR):' }}</label>
                            <input type="text" name="section_settings[hero_overlays][badges][{{ $idx }}][text_ar]" 
                                   value="{{ $b['text_ar'] ?? '' }}" 
                                   oninput="syncHeroOverlaysPreview()"
                                   dir="rtl" 
                                   placeholder="يدعم الذاكرة"
                                   class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-[#031827] dark:text-white font-tajawal font-bold">
                        </div>
                    </div>
                    
                    <button type="button" onclick="this.closest('.overlay-badge-row').remove(); syncHeroOverlaysPreview();" 
                            class="w-8 h-8 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 flex items-center justify-center shrink-0 self-end md:self-center transition-colors cursor-pointer" 
                            title="{{ $isAr ? 'حذف الشارة' : 'Remove Badge' }}">
                        <i class="fa-solid fa-trash-can text-xs"></i>
                    </button>
                </div>
            @empty
                <div class="text-center p-4 text-xs text-slate-400" id="noBadgesNotice">
                    {{ $isAr ? 'لا توجد شارات عائمة مخصصة بعد. اضغط "إضافة شارة عائمة".' : 'No custom floating badges configured yet. Click "Add Badge".' }}
                </div>
            @endforelse
        </div>
    </div>
</div>
