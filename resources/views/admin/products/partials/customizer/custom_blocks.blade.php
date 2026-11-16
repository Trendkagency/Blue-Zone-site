@php
    $cBlocks = $customBlocksData ?? [];
@endphp

<!-- ==========================================================
     SPECIAL SECTION 09: MODULAR CUSTOM BLOCKS & FAQ STUDIO
     ========================================================== -->
<div class="bz-customizer-card rounded-3xl bg-white dark:bg-[#031827] border border-slate-200/90 dark:border-slate-800 shadow-sm relative overflow-hidden" 
     id="card-sec-custom_blocks" data-section-key="custom_blocks"
     style="border-inline-start: 6px solid #8b5cf6;">
    
    <!-- Header Mini-Dashboard -->
    <div class="p-4 sm:p-5 md:p-6 border-b border-slate-100 dark:border-slate-800/80 cursor-pointer transition-colors hover:bg-slate-50/60 dark:hover:bg-[#062B49]/40 space-y-3.5"
         onclick="toggleSectionCardDrawer('custom_blocks')">
        
        <div class="flex items-start justify-between gap-3 sm:gap-4">
            <div class="flex items-start gap-3 sm:gap-4 min-w-0 flex-1">
                <div class="w-11 h-11 sm:w-13 sm:h-13 rounded-2xl flex items-center justify-center text-lg sm:text-xl shrink-0 shadow-md border-2 transition-transform duration-300" 
                     style="background-color: rgba(139, 92, 246, 0.12); border-color: rgba(139, 92, 246, 0.35); color: #8b5cf6;">
                    <i class="fa-solid fa-cubes-stacked"></i>
                </div>

                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-1.5 sm:gap-2 flex-wrap mb-1.5">
                        <span class="px-2.5 py-0.5 rounded-full font-mono text-[10px] font-black uppercase tracking-wider shadow-xs" 
                              style="background-color: rgba(139, 92, 246, 0.12); color: #8b5cf6; border: 1.5px solid rgba(139, 92, 246, 0.35);">
                            MODULAR EXPANSION
                        </span>
                        <span class="text-[10px] font-mono text-slate-700 dark:text-slate-200 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-md font-extrabold border border-slate-200 dark:border-slate-700">
                            #09
                        </span>
                        <span class="text-[10px] font-mono text-[#8b5cf6] bg-[#8b5cf6]/10 px-2 py-0.5 rounded-md font-black border border-[#8b5cf6]/20">
                            custom_blocks
                        </span>
                    </div>

                    <h3 class="text-base sm:text-lg font-black text-[#031827] dark:text-[#F6F5EF] tracking-tight leading-snug break-words">
                        {{ $isAr ? 'الأقسام الموديلر الإضافية وبنك الأسئلة الشائعة (Modular Custom Blocks & FAQs)' : 'Modular Custom Sections & FAQ Studio' }}
                    </h3>
                    <p class="text-xs text-slate-600 dark:text-slate-300 mt-1 leading-relaxed hidden sm:block font-medium">
                        {{ $isAr ? 'إضافة أقسام مخصصة إضافية أسفل صفحة المنتج مثل بنك الأسئلة الشائعة أو بطاقات التوعية الدوائية.' : 'Add custom modular sections like product FAQs, feature highlights, or clinical banners.' }}
                    </p>
                </div>
            </div>

            <button type="button" 
                    class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-500 dark:text-slate-300 flex items-center justify-center transition-all cursor-pointer shrink-0 mt-0.5"
                    onclick="toggleSectionCardDrawer('custom_blocks')">
                <i class="fa-solid fa-chevron-down text-xs transition-transform duration-300" 
                   id="chevron-icon-custom_blocks"></i>
            </button>
        </div>
    </div>

    <!-- Card Body Drawer -->
    <div class="hidden p-5 sm:p-6 bg-slate-50/50 dark:bg-[#02131F]/50 border-t border-slate-100 dark:border-slate-800/80 space-y-5"
         id="drawer-body-custom_blocks">
        
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
            <div>
                <h4 class="text-xs sm:text-sm font-black text-slate-900 dark:text-white">
                    {{ $isAr ? 'قائمة الأقسام الموديلر المخصصة' : 'Modular Blocks Registry' }}
                </h4>
                <p class="text-[11px] text-slate-500 dark:text-slate-400">
                    {{ $isAr ? 'يمكنك إنشاء أسئلة شائعة مخصصة لهذا المنتج أو بطاقات إرشادية' : 'Create FAQs or informative highlight sections' }}
                </p>
            </div>
            <button type="button" onclick="addCustomBlockRow('faq')" 
                    class="px-3 py-1.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-black transition-all flex items-center gap-1.5 shadow-sm active:scale-95 cursor-pointer">
                <i class="fa-solid fa-plus text-[10px]"></i>
                <span>{{ $isAr ? 'إضافة قسم أسئلة شائعة (FAQ)' : 'Add FAQ Block' }}</span>
            </button>
        </div>

        <div class="space-y-4" id="customBlocksContainer">
            @forelse($cBlocks as $bIdx => $blk)
                <div class="p-4 sm:p-5 rounded-2xl bg-white dark:bg-[#031827] border-2 border-purple-500/30 space-y-4 custom-block-row" data-index="{{ $bIdx }}">
                    <input type="hidden" name="section_settings[custom_blocks][{{ $bIdx }}][id]" value="{{ $blk['id'] ?? ('cb_'.$bIdx) }}">
                    <input type="hidden" name="section_settings[custom_blocks][{{ $bIdx }}][type]" value="{{ $blk['type'] ?? 'faq' }}">

                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full bg-purple-500/15 text-purple-600 dark:text-purple-300 font-mono text-[10px] font-black uppercase">
                                {{ strtoupper($blk['type'] ?? 'FAQ') }} BLOCK #{{ $bIdx + 1 }}
                            </span>
                        </div>
                        <button type="button" onclick="this.closest('.custom-block-row').remove()" 
                                class="text-rose-500 hover:text-rose-700 text-xs font-bold flex items-center gap-1 transition-colors cursor-pointer">
                            <i class="fa-solid fa-trash-can text-[10px]"></i> <span>{{ $isAr ? 'حذف هذا القسم' : 'Delete Block' }}</span>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Block Title (EN):</label>
                            <input type="text" name="section_settings[custom_blocks][{{ $bIdx }}][title_en]" 
                                   value="{{ $blk['title_en'] ?? 'Frequently Asked Questions' }}" 
                                   class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-[#062B49] text-slate-900 dark:text-white font-bold">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">عنوان القسم (AR):</label>
                            <input type="text" name="section_settings[custom_blocks][{{ $bIdx }}][title_ar]" 
                                   value="{{ $blk['title_ar'] ?? 'الأسئلة الشائعة حول المستحضر' }}" 
                                   dir="rtl" 
                                   class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 dark:border-slate-600 dark:bg-[#062B49] text-slate-900 dark:text-white font-tajawal font-bold">
                        </div>
                    </div>

                    <!-- Items Repeater for FAQ -->
                    <div class="pt-2 border-t border-slate-100 dark:border-slate-800 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                {{ $isAr ? 'عناصر الأسئلة والأجوبة (FAQ Items):' : 'Question & Answer Items:' }}
                            </span>
                            <button type="button" onclick="addFaqItemToBlock({{ $bIdx }})" 
                                    class="text-xs font-bold text-purple-600 dark:text-purple-400 hover:underline flex items-center gap-1 cursor-pointer">
                                <i class="fa-solid fa-plus text-[10px]"></i> <span>{{ $isAr ? 'إضافة سؤال وجواب' : 'Add Question' }}</span>
                            </button>
                        </div>

                        <div class="space-y-3" id="faqItemsContainer-{{ $bIdx }}">
                            @php $items = $blk['items'] ?? []; @endphp
                            @foreach($items as $iIdx => $it)
                                <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-[#062B49] border border-slate-200 dark:border-slate-700 space-y-2 faq-item-row" data-index="{{ $iIdx }}">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[10px] font-mono text-slate-400 font-bold">Q#{{ $iIdx + 1 }}</span>
                                        <button type="button" onclick="this.closest('.faq-item-row').remove()" class="text-rose-500 hover:text-rose-700 text-xs">
                                            <i class="fa-solid fa-times"></i>
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                        <input type="text" name="section_settings[custom_blocks][{{ $bIdx }}][items][{{ $iIdx }}][question_en]" 
                                               value="{{ $it['question_en'] ?? '' }}" 
                                               placeholder="Question (EN)" 
                                               class="w-full px-3 py-1.5 text-xs rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-[#031827] dark:text-white font-bold">
                                        <input type="text" name="section_settings[custom_blocks][{{ $bIdx }}][items][{{ $iIdx }}][question_ar]" 
                                               value="{{ $it['question_ar'] ?? '' }}" 
                                               dir="rtl" 
                                               placeholder="السؤال (AR)" 
                                               class="w-full px-3 py-1.5 text-xs rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-[#031827] dark:text-white font-tajawal font-bold">
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                        <textarea name="section_settings[custom_blocks][{{ $bIdx }}][items][{{ $iIdx }}][answer_en]" 
                                                  rows="2" 
                                                  placeholder="Answer (EN)" 
                                                  class="w-full p-2 text-xs rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-[#031827] dark:text-white">{{ $it['answer_en'] ?? '' }}</textarea>
                                        <textarea name="section_settings[custom_blocks][{{ $bIdx }}][items][{{ $iIdx }}][answer_ar]" 
                                                  rows="2" 
                                                  dir="rtl" 
                                                  placeholder="الإجابة (AR)" 
                                                  class="w-full p-2 text-xs rounded-lg border border-slate-300 dark:border-slate-600 dark:bg-[#031827] dark:text-white font-tajawal">{{ $it['answer_ar'] ?? '' }}</textarea>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center p-6 text-xs text-slate-400 bg-white dark:bg-[#031827] rounded-2xl border border-dashed border-slate-300 dark:border-slate-700" id="noBlocksNotice">
                    {{ $isAr ? 'لا توجد أقسام موديلر إضافية حالياً. اضغط "إضافة قسم أسئلة شائعة (FAQ)" لإنشاء قسم جديد.' : 'No additional modular blocks configured yet. Click "Add FAQ Block" to create one.' }}
                </div>
            @endforelse
        </div>
    </div>
</div>
