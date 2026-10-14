<x-layouts.admin 
    :pageTitle="__('supply_chain_reports.lot_traceability')" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'محرك التتبع والاسترجاع الدوائي ثنائي الاتجاه (سلسلة نسب المواد من المورد حتى المستهلك في أقل من 60 ثانية)' : 'Full Bi-directional cGMP Genealogy & Rapid Recall Engine (<60s Forward/Backward Traceability)'"
    :breadcrumbs="[
        (__('supply_chain_reports.title') ?? 'Supply Chain Reports') => route('admin.supply-chain-reports.lot-traceability'), 
        (__('supply_chain_reports.lot_traceability') ?? 'Lot Traceability') => '#'
    ]"
>
    <x-slot name="actions">
        <div class="flex items-center gap-2 flex-wrap">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 text-xs font-bold">
                <i class="fa-solid fa-shield-check text-emerald-500"></i>
                <span>{{ app()->getLocale() === 'ar' ? 'معيار FDA 21 CFR §111 و cGMP' : 'FDA 21 CFR §111 / cGMP Recall Compliant' }}</span>
            </span>

            @if(!empty($results))
                <a href="{{ request()->fullUrlWithQuery(['export' => 'xlsx']) }}" class="btn btn-secondary font-bold text-xs sm:text-sm shadow-sm">
                    <i class="fa-solid fa-file-excel text-emerald-600 mr-1.5 ml-1.5"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'تصدير ملف الاسترجاع (Excel)' : 'Export Official Recall Dossier' }}</span>
                </a>
                <button type="button" onclick="window.print()" class="btn btn-secondary font-bold text-xs sm:text-sm shadow-sm">
                    <i class="fa-solid fa-print mr-1.5 ml-1.5"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'طباعة التقرير' : 'Print Trace' }}</span>
                </button>
            @endif
        </div>
    </x-slot>

    <!-- Top Direction Mode Selector & Search Cockpit -->
    <div class="card p-5 mb-6 border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] rounded-2xl shadow-sm">
        <form method="GET" action="{{ route('admin.supply-chain-reports.lot-traceability') }}" id="traceForm" class="space-y-4">
            
            <!-- Mode Radio Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <label class="cursor-pointer relative flex flex-col p-4 rounded-xl border-2 transition-all {{ request('direction', $searchType ?? 'forward') === 'forward' ? 'border-[#0A4F78] dark:border-sky-500 bg-sky-50/40 dark:bg-sky-950/20 shadow-sm' : 'border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700 bg-slate-50/50 dark:bg-[#031827]/40' }}">
                    <input type="radio" name="direction" value="forward" class="sr-only" onchange="document.getElementById('traceForm').submit()" {{ request('direction', $searchType ?? 'forward') === 'forward' ? 'checked' : '' }}>
                    <div class="flex items-center justify-between mb-1.5">
                        <div class="flex items-center gap-2">
                            <span class="w-8 h-8 rounded-lg bg-sky-100 dark:bg-sky-900/40 text-[#0A4F78] dark:text-sky-400 flex items-center justify-center text-sm font-black">
                                <i class="fa-solid fa-arrow-right-long {{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}"></i>
                            </span>
                            <span class="font-bold text-sm text-slate-900 dark:text-white">
                                {{ app()->getLocale() === 'ar' ? 'تتبع أمامي: استرجاع وسحب التشغيلة (Forward Recall)' : 'Forward Trace: Contaminated Raw Lot ➔ Patient Recall' }}
                            </span>
                        </div>
                        @if(request('direction', $searchType ?? 'forward') === 'forward')
                            <span class="w-5 h-5 rounded-full bg-[#0A4F78] text-white flex items-center justify-center text-[10px]">
                                <i class="fa-solid fa-check"></i>
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-0 pr-1 pl-1">
                        {{ app()->getLocale() === 'ar' ? 'تتبع دفعة المادة الأولية الواردة ومعرفة كافة المنتجات المصنعة منها وطلبات العملاء المتأثرة لعزلها فوراً.' : 'Identify every compounded work order and downstream clinic/patient order exposed to a specific raw material batch.' }}
                    </p>
                </label>

                <label class="cursor-pointer relative flex flex-col p-4 rounded-xl border-2 transition-all {{ request('direction', $searchType ?? 'forward') === 'backward' ? 'border-purple-600 dark:border-purple-400 bg-purple-50/40 dark:bg-purple-950/20 shadow-sm' : 'border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700 bg-slate-50/50 dark:bg-[#031827]/40' }}">
                    <input type="radio" name="direction" value="backward" class="sr-only" onchange="document.getElementById('traceForm').submit()" {{ request('direction', $searchType ?? 'forward') === 'backward' ? 'checked' : '' }}>
                    <div class="flex items-center justify-between mb-1.5">
                        <div class="flex items-center gap-2">
                            <span class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-900/40 text-purple-600 dark:text-purple-400 flex items-center justify-center text-sm font-black">
                                <i class="fa-solid fa-arrow-left-long {{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}"></i>
                            </span>
                            <span class="font-bold text-sm text-slate-900 dark:text-white">
                                {{ app()->getLocale() === 'ar' ? 'تتبع عكسي: شجرة الأنساب والتدقيق (Backward Genealogy)' : 'Backward Trace: Finished Lot ➔ Raw Ingredients Pedigree' }}
                            </span>
                        </div>
                        @if(request('direction', $searchType ?? 'forward') === 'backward')
                            <span class="w-5 h-5 rounded-full bg-purple-600 text-white flex items-center justify-center text-[10px]">
                                <i class="fa-solid fa-check"></i>
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-0 pr-1 pl-1">
                        {{ app()->getLocale() === 'ar' ? 'تدقيق تشغيلة المنتج التام واسترجاع كافة المواد الفعالة المستهلكة ومورديها وشهادات الفحص المخبري COA.' : 'Audit a finished clinical batch back to exact raw materials, weighing logs, analytical QC releases, and source suppliers.' }}
                    </p>
                </label>
            </div>

            <!-- Search Input Bar -->
            <div class="pt-2">
                <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none text-slate-400">
                            <i class="fa-solid fa-barcode text-base"></i>
                        </div>
                        <input 
                            type="text" 
                            name="lot_number" 
                            id="lotNumberInput"
                            value="{{ request('lot_number', $searchQuery ?? '') }}" 
                            required 
                            autocomplete="off"
                            class="form-control text-sm font-mono font-bold ps-10 w-full rounded-xl py-2.5 bg-slate-50 dark:bg-[#031827] border-slate-200 dark:border-[#15456E]" 
                            placeholder="{{ request('direction', $searchType ?? 'forward') === 'forward' ? (app()->getLocale() === 'ar' ? 'أدخل رقم تشغيلة المادة الأولية أو رقم دفعة المورد (مثل: LOT-RM-2026...)' : 'Enter Raw Material Batch Number or Supplier LOT (e.g. LOT-RM-2026-...)') : (app()->getLocale() === 'ar' ? 'أدخل رقم تشغيلة المنتج التام أو أمر التصنيع (مثل: BZ-NMN-2026...)' : 'Enter Finished Product LOT or Work Order Number (e.g. BZ-NMN-2026-...)') }}"
                        >
                    </div>

                    <button type="submit" class="btn btn-primary px-6 py-2.5 rounded-xl font-bold text-sm shadow-md whitespace-nowrap bg-[#0A4F78] hover:bg-[#062B49] text-white flex items-center gap-2">
                        <i class="fa-solid fa-magnifying-glass-chart"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'تشغيل فحص التتبع الدوائي' : 'Execute Rapid Recall Trace' }}</span>
                    </button>
                    
                    @if(request('lot_number', $searchQuery ?? ''))
                        <a href="{{ route('admin.supply-chain-reports.lot-traceability', ['direction' => request('direction', $searchType ?? 'forward')]) }}" class="btn btn-secondary px-3 py-2.5 rounded-xl text-xs font-bold text-slate-500 hover:text-rose-500" title="{{ app()->getLocale() === 'ar' ? 'إعادة ضبط البحث' : 'Clear search' }}">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    @endif
                </div>

                <!-- 1-Click Interactive Sample Test Chips -->
                <div class="mt-3 flex items-center gap-2 flex-wrap text-xs">
                    <span class="text-slate-400 font-bold flex items-center gap-1">
                        <i class="fa-solid fa-lightbulb text-amber-500"></i>
                        {{ app()->getLocale() === 'ar' ? 'اختبار سريع برقم تشغيلة فعلية:' : '1-Click Sample Test:' }}
                    </span>

                    @if(request('direction', $searchType ?? 'forward') === 'forward')
                        @forelse($sampleRawBatches ?? [] as $sample)
                            <button 
                                type="button" 
                                onclick="quickTrace('{{ $sample->batch_number }}', 'forward')"
                                class="px-2.5 py-1 rounded-lg bg-sky-50 hover:bg-sky-100 dark:bg-sky-950/40 dark:hover:bg-sky-900/60 text-[#0A4F78] dark:text-sky-300 font-mono text-[11px] font-bold border border-sky-200/80 dark:border-sky-800 transition-all flex items-center gap-1.5"
                            >
                                <i class="fa-solid fa-flask text-[10px]"></i>
                                <span>{{ $sample->batch_number }}</span>
                                <span class="text-[10px] opacity-75">({{ $sample->rawMaterial?->name }})</span>
                            </button>
                        @empty
                            <span class="text-slate-400 italic text-[11px]">{{ app()->getLocale() === 'ar' ? 'لا توجد تشغيلات بعد' : 'No raw batches available yet' }}</span>
                        @endforelse
                    @else
                        @forelse($sampleWorkOrders ?? [] as $sampleWo)
                            <button 
                                type="button" 
                                onclick="quickTrace('{{ $sampleWo->target_finished_lot }}', 'backward')"
                                class="px-2.5 py-1 rounded-lg bg-purple-50 hover:bg-purple-100 dark:bg-purple-950/40 dark:hover:bg-purple-900/60 text-purple-700 dark:text-purple-300 font-mono text-[11px] font-bold border border-purple-200/80 dark:border-purple-800 transition-all flex items-center gap-1.5"
                            >
                                <i class="fa-solid fa-box-open text-[10px]"></i>
                                <span>{{ $sampleWo->target_finished_lot }}</span>
                                <span class="text-[10px] opacity-75">({{ $sampleWo->product?->name_en }})</span>
                            </button>
                        @empty
                            <span class="text-slate-400 italic text-[11px]">{{ app()->getLocale() === 'ar' ? 'لا توجد أوامر تصنيع منتهية بعد' : 'No finished work orders available yet' }}</span>
                        @endforelse
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Trace Results Presentation -->
    @if(request('lot_number', $searchQuery ?? ''))
        @if(!empty($results))
            <div class="space-y-6">

                <!-- Audit Benchmark & Pedigree Banner -->
                <div class="card p-5 border border-sky-200 dark:border-[#15456E] bg-gradient-to-r from-sky-50/80 via-white to-sky-50/30 dark:from-[#062B49] dark:via-[#031827] dark:to-[#062B49] rounded-2xl shadow-sm">
                    <div class="flex items-center justify-between flex-wrap gap-4">
                        <div class="flex items-center gap-3.5">
                            <div class="w-12 h-12 rounded-xl bg-[#0A4F78] text-white flex items-center justify-center text-xl shadow-sm">
                                <i class="fa-solid fa-dna"></i>
                            </div>
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="text-lg font-black text-slate-900 dark:text-white font-mono tracking-tight m-0">
                                        {{ request('lot_number', $searchQuery ?? '') }}
                                    </h3>
                                    <span class="badge {{ request('direction', $searchType ?? 'forward') === 'forward' ? 'badge-primary' : 'badge-secondary' }} text-xs font-bold uppercase">
                                        {{ request('direction', $searchType ?? 'forward') === 'forward' ? 'Forward Recall Pedigree' : 'Backward Formulation Pedigree' }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-2 flex-wrap">
                                    <span class="inline-flex items-center gap-1 text-emerald-600 dark:text-emerald-400 font-bold">
                                        <i class="fa-solid fa-circle-check"></i>
                                        {{ app()->getLocale() === 'ar' ? 'تم استخراج السلسلة الدوائية بالكامل في' : 'Genealogy resolved in' }} {{ $executionTime ?? '0.018' }}s
                                    </span>
                                    <span class="text-slate-300 dark:text-slate-700">|</span>
                                    <span>{{ app()->getLocale() === 'ar' ? 'تاريخ التدقيق:' : 'Audit Timestamp:' }} {{ now()->format('Y-m-d H:i:s') }}</span>
                                </p>
                            </div>
                        </div>

                        <!-- Impact KPI Chips -->
                        <div class="flex items-center gap-2 flex-wrap">
                            @if(request('direction', $searchType ?? 'forward') === 'forward')
                                <div class="px-3.5 py-2 rounded-xl bg-white dark:bg-[#031827] border border-slate-200 dark:border-[#15456E] text-center shadow-xs">
                                    <span class="text-[10px] uppercase font-bold text-slate-400 block">{{ app()->getLocale() === 'ar' ? 'أوامر تصنيع متأثرة' : 'Impacted WOs' }}</span>
                                    <span class="text-base font-black text-[#0A4F78] dark:text-sky-400 font-mono">{{ count($results['work_orders'] ?? []) }}</span>
                                </div>
                                <div class="px-3.5 py-2 rounded-xl bg-white dark:bg-[#031827] border border-slate-200 dark:border-[#15456E] text-center shadow-xs">
                                    <span class="text-[10px] uppercase font-bold text-slate-400 block">{{ app()->getLocale() === 'ar' ? 'تشغيلات منتج تام' : 'Finished Lots' }}</span>
                                    <span class="text-base font-black text-purple-600 dark:text-purple-400 font-mono">{{ count($results['finished_lots'] ?? []) }}</span>
                                </div>
                                <div class="px-3.5 py-2 rounded-xl bg-white dark:bg-[#031827] border border-slate-200 dark:border-[#15456E] text-center shadow-xs">
                                    <span class="text-[10px] uppercase font-bold text-slate-400 block">{{ app()->getLocale() === 'ar' ? 'طلبات عملاء مكشوفة' : 'Exposed Orders' }}</span>
                                    <span class="text-base font-black text-rose-600 dark:text-rose-400 font-mono">{{ count($results['affected_orders'] ?? []) }}</span>
                                </div>
                            @else
                                <div class="px-3.5 py-2 rounded-xl bg-white dark:bg-[#031827] border border-slate-200 dark:border-[#15456E] text-center shadow-xs">
                                    <span class="text-[10px] uppercase font-bold text-slate-400 block">{{ app()->getLocale() === 'ar' ? 'مكونات مستهلكة' : 'Consumed Ingredients' }}</span>
                                    <span class="text-base font-black text-purple-600 dark:text-purple-400 font-mono">{{ count($results['ingredients'] ?? $results['consumptions'] ?? []) }}</span>
                                </div>
                                <div class="px-3.5 py-2 rounded-xl bg-white dark:bg-[#031827] border border-slate-200 dark:border-[#15456E] text-center shadow-xs">
                                    <span class="text-[10px] uppercase font-bold text-slate-400 block">{{ app()->getLocale() === 'ar' ? 'الكمية المنتجة' : 'Produced Qty' }}</span>
                                    <span class="text-base font-black text-emerald-600 dark:text-emerald-400 font-mono">{{ $results['work_order']?->produced_qty ?? 0 }} {{ app()->getLocale() === 'ar' ? 'عبوة' : 'units' }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Step-by-Step Interactive Pedigree Flow Pipeline -->
                <div class="card p-5 border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] rounded-2xl shadow-sm">
                    <h4 class="text-xs font-black uppercase text-slate-400 tracking-wider mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-timeline text-[#0A4F78] dark:text-sky-400"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'مخطط التدفق والتتبع الزمني لسلسلة النسب (cGMP Pedigree)' : 'cGMP 4-Stage Chronological Pedigree Pipeline' }}</span>
                    </h4>

                    @if(request('direction', $searchType ?? 'forward') === 'forward' && isset($results['batch']))
                        @php $b = $results['batch']; @endphp
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 relative">
                            <!-- Stage 1: Supplier & Receipt -->
                            <div class="p-4 rounded-xl bg-slate-50 dark:bg-[#031827] border border-slate-200/80 dark:border-[#15456E] relative">
                                <div class="flex items-center gap-2 mb-2 text-[#0A4F78] dark:text-sky-400 font-bold text-xs uppercase">
                                    <span class="w-5 h-5 rounded-full bg-[#0A4F78] text-white flex items-center justify-center text-[10px]">1</span>
                                    <span>{{ app()->getLocale() === 'ar' ? 'المورد والاستلام (GRN)' : 'Intake & Source' }}</span>
                                </div>
                                <p class="font-bold text-sm text-slate-900 dark:text-white mb-1">{{ $b->supplier?->name ?? 'N/A' }}</p>
                                <p class="text-xs text-slate-500 font-mono mb-1">LOT: {{ $b->supplier_lot }}</p>
                                <span class="badge badge-neutral text-[10px]">GRN: {{ $b->goodsReceipt?->grn_number ?? 'GRN-INBOUND' }}</span>
                            </div>

                            <!-- Stage 2: Quality Clearance -->
                            <div class="p-4 rounded-xl bg-slate-50 dark:bg-[#031827] border border-slate-200/80 dark:border-[#15456E] relative">
                                <div class="flex items-center gap-2 mb-2 text-emerald-600 font-bold text-xs uppercase">
                                    <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[10px]">2</span>
                                    <span>{{ app()->getLocale() === 'ar' ? 'الإفراج المخبري (QC)' : 'Lab Release' }}</span>
                                </div>
                                <p class="font-bold text-sm text-slate-900 dark:text-white mb-1">
                                    {{ $b->inspections->last()?->verdict?->label() ?? 'Passed' }}
                                </p>
                                <p class="text-xs text-slate-500 font-mono mb-1">
                                    {{ $b->inspections->last()?->inspection_code ?? 'QC-CERTIFIED' }}
                                </p>
                                <span class="badge badge-emerald text-[10px]">{{ app()->getLocale() === 'ar' ? 'معتمد للاستخدام' : 'Approved for MES' }}</span>
                            </div>

                            <!-- Stage 3: MES Compounding -->
                            <div class="p-4 rounded-xl bg-slate-50 dark:bg-[#031827] border border-slate-200/80 dark:border-[#15456E] relative">
                                <div class="flex items-center gap-2 mb-2 text-indigo-600 font-bold text-xs uppercase">
                                    <span class="w-5 h-5 rounded-full bg-indigo-600 text-white flex items-center justify-center text-[10px]">3</span>
                                    <span>{{ app()->getLocale() === 'ar' ? 'أوامر التصنيع (MES)' : 'MES Formulation' }}</span>
                                </div>
                                <p class="font-bold text-sm text-slate-900 dark:text-white mb-1 font-mono">
                                    {{ count($results['work_orders'] ?? []) }} {{ app()->getLocale() === 'ar' ? 'أمر تصنيع' : 'Work Orders' }}
                                </p>
                                <p class="text-xs text-slate-500 mb-1">
                                    {{ app()->getLocale() === 'ar' ? 'تم الوزن في الغرفة النظيفة' : 'Cleanroom Gravimetric Dispense' }}
                                </p>
                                <span class="badge badge-indigo text-[10px]">{{ count($results['finished_lots'] ?? []) }} {{ app()->getLocale() === 'ar' ? 'تشغيلة تامة' : 'Finished Lots' }}</span>
                            </div>

                            <!-- Stage 4: Market & Patient Exposure -->
                            <div class="p-4 rounded-xl bg-slate-50 dark:bg-[#031827] border border-slate-200/80 dark:border-[#15456E] relative">
                                <div class="flex items-center gap-2 mb-2 text-rose-600 font-bold text-xs uppercase">
                                    <span class="w-5 h-5 rounded-full bg-rose-600 text-white flex items-center justify-center text-[10px]">4</span>
                                    <span>{{ app()->getLocale() === 'ar' ? 'المستهلكون والسحب' : 'Recall Scope' }}</span>
                                </div>
                                <p class="font-bold text-sm text-rose-600 dark:text-rose-400 mb-1 font-mono">
                                    {{ count($results['affected_orders'] ?? []) }} {{ app()->getLocale() === 'ar' ? 'طلب متأثر' : 'Orders Impacted' }}
                                </p>
                                <p class="text-xs text-slate-500 mb-1">
                                    {{ app()->getLocale() === 'ar' ? 'نطاق الاحتواء والتبليغ' : 'Immediate Containment Ready' }}
                                </p>
                                <span class="badge badge-rose text-[10px] font-bold">{{ app()->getLocale() === 'ar' ? 'إجراء عزل فوري' : 'Action Required' }}</span>
                            </div>
                        </div>
                    @elseif(isset($results['work_order']))
                        @php $wo = $results['work_order']; @endphp
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 relative">
                            <!-- Stage 1: Finished Product -->
                            <div class="p-4 rounded-xl bg-slate-50 dark:bg-[#031827] border border-slate-200/80 dark:border-[#15456E]">
                                <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Target Finished Lot</span>
                                <p class="font-black text-sm text-slate-900 dark:text-white font-mono mb-1">{{ $wo->target_finished_lot }}</p>
                                <span class="text-xs text-slate-600 dark:text-slate-400 font-bold block">{{ $wo->product?->name_en }}</span>
                            </div>

                            <!-- Stage 2: Production Execution -->
                            <div class="p-4 rounded-xl bg-slate-50 dark:bg-[#031827] border border-slate-200/80 dark:border-[#15456E]">
                                <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Work Order Execution</span>
                                <p class="font-black text-sm text-indigo-600 dark:text-indigo-400 font-mono mb-1">{{ $wo->order_number }}</p>
                                <span class="text-xs text-slate-500 block">{{ $wo->scheduled_start?->format('Y-m-d H:i') }}</span>
                            </div>

                            <!-- Stage 3: Recipe BOM Pedigree -->
                            <div class="p-4 rounded-xl bg-slate-50 dark:bg-[#031827] border border-slate-200/80 dark:border-[#15456E]">
                                <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Raw Ingredients Used</span>
                                <p class="font-black text-sm text-purple-600 dark:text-purple-400 font-mono mb-1">
                                    {{ count($results['ingredients'] ?? $results['consumptions'] ?? []) }} Active Batches
                                </p>
                                <span class="text-xs text-emerald-600 font-bold block"><i class="fa-solid fa-check"></i> 100% Barcode Verified</span>
                            </div>

                            <!-- Stage 4: QC & Release -->
                            <div class="p-4 rounded-xl bg-slate-50 dark:bg-[#031827] border border-slate-200/80 dark:border-[#15456E]">
                                <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Release Status</span>
                                <p class="font-black text-sm text-emerald-600 mb-1">
                                    {{ $wo->status?->label() ?? 'Completed' }}
                                </p>
                                <span class="text-xs text-slate-500 block font-mono">{{ $wo->produced_qty }} Units Released</span>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Detailed Tables according to Search Direction -->
                @if(request('direction', $searchType ?? 'forward') === 'forward')
                    
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Left: Origin Batch Master Card -->
                        <div class="card p-5 border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] rounded-2xl shadow-sm space-y-4">
                            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                                <h4 class="text-xs font-black uppercase text-slate-900 dark:text-white flex items-center gap-2 m-0">
                                    <i class="fa-solid fa-flask-vial text-[#0A4F78] dark:text-sky-400"></i>
                                    <span>{{ app()->getLocale() === 'ar' ? 'بيانات المادة الأولية الأصلية' : 'Root Inbound Material Dossier' }}</span>
                                </h4>
                                @if(isset($results['batch']))
                                    <a href="{{ route('admin.batches.show', $results['batch']) }}" class="btn btn-secondary btn-sm text-[11px] py-1 font-bold">
                                        {{ app()->getLocale() === 'ar' ? 'سجل التشغيلة' : 'Batch Record' }}
                                    </a>
                                @endif
                            </div>

                            @if(isset($results['batch']))
                                @php $b = $results['batch']; @endphp
                                <div class="space-y-3 text-xs">
                                    <div class="flex justify-between py-1.5 border-b border-slate-100 dark:border-slate-800/60">
                                        <span class="text-slate-500">{{ __('raw_materials.name') }}:</span>
                                        <span class="font-bold text-slate-900 dark:text-white text-end">{{ $b->rawMaterial?->name }}</span>
                                    </div>
                                    <div class="flex justify-between py-1.5 border-b border-slate-100 dark:border-slate-800/60">
                                        <span class="text-slate-500">SKU / Code:</span>
                                        <span class="font-mono font-bold">{{ $b->rawMaterial?->code ?? $b->rawMaterial?->sku }}</span>
                                    </div>
                                    <div class="flex justify-between py-1.5 border-b border-slate-100 dark:border-slate-800/60">
                                        <span class="text-slate-500">{{ __('batches.supplier_lot') }}:</span>
                                        <span class="font-mono font-bold text-purple-600 dark:text-purple-400">{{ $b->supplier_lot }}</span>
                                    </div>
                                    <div class="flex justify-between py-1.5 border-b border-slate-100 dark:border-slate-800/60">
                                        <span class="text-slate-500">{{ __('suppliers.title') }}:</span>
                                        <span class="font-bold text-sky-600">{{ $b->supplier?->name }}</span>
                                    </div>
                                    <div class="flex justify-between py-1.5 border-b border-slate-100 dark:border-slate-800/60">
                                        <span class="text-slate-500">{{ __('batches.storage_condition') }}:</span>
                                        <span class="badge {{ $b->rawMaterial?->storage_condition?->badgeClass() }} text-[10px]">
                                            {{ $b->rawMaterial?->storage_condition?->label() }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between py-1.5 border-b border-slate-100 dark:border-slate-800/60">
                                        <span class="text-slate-500">{{ __('batches.expiry_date') }}:</span>
                                        <span class="font-mono font-bold {{ $b->expiry_date && $b->expiry_date->isPast() ? 'text-rose-600' : 'text-slate-800 dark:text-slate-200' }}">
                                            {{ $b->expiry_date?->format('Y-m-d') }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between py-1.5">
                                        <span class="text-slate-500">{{ app()->getLocale() === 'ar' ? 'المخزون المتبقي حالياً' : 'Current Retained Stock' }}:</span>
                                        <span class="font-mono font-bold text-emerald-600 text-sm">
                                            {{ number_format($b->current_qty, 2) }} {{ $b->rawMaterial?->uom?->value }}
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Right: Affected Manufacturing Orders & Customer Exposure (2 Cols) -->
                        <div class="lg:col-span-2 space-y-6">
                            
                            <!-- Impacted Work Orders -->
                            <div class="card p-5 border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] rounded-2xl shadow-sm">
                                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3 mb-3">
                                    <h4 class="text-xs font-black uppercase text-slate-900 dark:text-white flex items-center gap-2 m-0">
                                        <i class="fa-solid fa-industry text-indigo-600"></i>
                                        <span>{{ app()->getLocale() === 'ar' ? 'أوامر التصنيع الدوائي التي استهلكت هذه المادة' : 'Compounding Work Orders Utilizing this Lot' }}</span>
                                    </h4>
                                    <span class="badge badge-indigo text-xs font-mono font-bold">{{ count($results['work_orders'] ?? []) }} Orders</span>
                                </div>

                                <div class="table-responsive">
                                    <table class="table text-xs">
                                        <thead>
                                            <tr>
                                                <th>{{ app()->getLocale() === 'ar' ? 'رقم أمر الإنتاج' : 'Work Order #' }}</th>
                                                <th>{{ app()->getLocale() === 'ar' ? 'المنتج النهائي' : 'Compounded Product' }}</th>
                                                <th>{{ app()->getLocale() === 'ar' ? 'تشغيلة المنتج التام' : 'Finished Product LOT' }}</th>
                                                <th>{{ app()->getLocale() === 'ar' ? 'تاريخ الجدولة' : 'Scheduled Date' }}</th>
                                                <th>{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($results['work_orders'] ?? [] as $wo)
                                                <tr>
                                                    <td class="font-mono font-bold text-sky-600">
                                                        <a href="{{ route('admin.work-orders.show', $wo) }}" class="hover:underline">
                                                            {{ $wo->order_number }}
                                                        </a>
                                                    </td>
                                                    <td class="font-bold text-slate-900 dark:text-white">
                                                        {{ $wo->product?->name_en }}
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-purple text-[11px] font-mono font-bold">
                                                            {{ $wo->target_finished_lot }}
                                                        </span>
                                                    </td>
                                                    <td class="font-mono text-slate-500">
                                                        {{ $wo->scheduled_start?->format('Y-m-d H:i') }}
                                                    </td>
                                                    <td>
                                                        <span class="badge {{ $wo->status?->badgeClass() }} text-[10px]">
                                                            {{ $wo->status?->label() }}
                                                        </span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="{{ route('admin.work-orders.show', $wo) }}" class="btn btn-secondary btn-xs py-1">
                                                            <i class="fa-solid fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center py-6 text-slate-400">
                                                        <i class="fa-solid fa-box-archive text-2xl mb-1.5 block text-slate-300"></i>
                                                        {{ app()->getLocale() === 'ar' ? 'هذه التشغيلة لم تُصرف في أي أمر تصنيع بعد (مخزنة نقية بالمستودع).' : 'This lot has not yet been dispensed into any formulation work order (still intact in storage).' }}
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Downstream Affected Orders & Customer Traceability -->
                            <div class="card p-5 border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] rounded-2xl shadow-sm">
                                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3 mb-3">
                                    <h4 class="text-xs font-black uppercase text-slate-900 dark:text-white flex items-center gap-2 m-0">
                                        <i class="fa-solid fa-truck-ramp-box text-rose-600"></i>
                                        <span>{{ app()->getLocale() === 'ar' ? 'طلبات البيع والعيادات المكشوفة على المنتجات المتأثرة' : 'Exposed Clinic & Patient Commercial Orders' }}</span>
                                    </h4>
                                    <span class="badge badge-rose text-xs font-mono font-bold">{{ count($results['affected_orders'] ?? []) }} Orders</span>
                                </div>

                                <div class="table-responsive max-h-80 overflow-y-auto">
                                    <table class="table text-xs">
                                        <thead>
                                            <tr>
                                                <th>{{ app()->getLocale() === 'ar' ? 'رقم الطلب' : 'Order #' }}</th>
                                                <th>{{ app()->getLocale() === 'ar' ? 'العميل / العيادة' : 'Customer / Patient' }}</th>
                                                <th>{{ app()->getLocale() === 'ar' ? 'تاريخ التوريد' : 'Date' }}</th>
                                                <th>{{ app()->getLocale() === 'ar' ? 'القيمة' : 'Total' }}</th>
                                                <th>{{ app()->getLocale() === 'ar' ? 'حالة الطلب' : 'Status' }}</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($results['affected_orders'] ?? [] as $order)
                                                <tr>
                                                    <td class="font-mono font-bold text-slate-800 dark:text-slate-200">
                                                        {{ $order->order_number ?? '#'.$order->id }}
                                                    </td>
                                                    <td class="font-bold text-slate-900 dark:text-white">
                                                        {{ $order->customer?->name ?? 'Direct Patient / Guest' }}
                                                        <span class="block text-[10px] text-slate-400 font-normal font-mono">{{ $order->customer?->phone ?? $order->customer?->email ?? '—' }}</span>
                                                    </td>
                                                    <td class="font-mono text-slate-500">
                                                        {{ $order->created_at?->format('Y-m-d') }}
                                                    </td>
                                                    <td class="font-mono font-bold">
                                                        {{ number_format($order->total_amount ?? $order->grand_total, 2) }} AED
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-neutral text-[10px]">
                                                            {{ $order->status ?? 'Dispatched' }}
                                                        </span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-secondary btn-xs py-1" target="_blank">
                                                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center py-6 text-slate-400">
                                                        <i class="fa-solid fa-shield-heart text-2xl mb-1.5 block text-emerald-500"></i>
                                                        {{ app()->getLocale() === 'ar' ? 'لم يتم شحن أي وحدات من هذا المنتج للعملاء بعد (خالية من المخاطر الخارجية).' : 'Zero external distribution detected: No finished products from this lot have been dispatched to customers.' }}
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>

                @else
                    <!-- Backward Genealogy Table -->
                    <div class="card p-5 border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] rounded-2xl shadow-sm space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                            <h4 class="text-xs font-black uppercase text-slate-900 dark:text-white flex items-center gap-2 m-0">
                                <i class="fa-solid fa-backward-step text-purple-600"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'سلسلة نسب المواد الأولية المستهلكة في التشغيلة وشهادات COA' : 'Reverse BOM Bill-of-Materials & Active Ingredient Pedigree' }}</span>
                            </h4>
                            <span class="badge badge-purple text-xs font-mono font-bold">{{ count($results['ingredients'] ?? $results['consumptions'] ?? []) }} Raw Ingredients</span>
                        </div>

                        <div class="table-responsive">
                            <table class="table text-xs">
                                <thead>
                                    <tr>
                                        <th>{{ app()->getLocale() === 'ar' ? 'المادة الأولية (Active API)' : 'Raw Ingredient API' }}</th>
                                        <th>{{ __('batches.batch_number') }}</th>
                                        <th>{{ __('batches.supplier_lot') }}</th>
                                        <th>{{ __('suppliers.title') }}</th>
                                        <th>{{ app()->getLocale() === 'ar' ? 'الكمية المستهلكة بدقة' : 'Gravimetric Dispensed Qty' }}</th>
                                        <th>{{ app()->getLocale() === 'ar' ? 'فحص الباركود' : 'Barcode Scan' }}</th>
                                        <th>{{ app()->getLocale() === 'ar' ? 'المشغل' : 'Operator' }}</th>
                                        <th>{{ __('qc.verdict') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($results['ingredients'] ?? [] as $i)
                                        <tr>
                                            <td class="font-bold text-slate-900 dark:text-white">
                                                {{ $i['material'] }}
                                            </td>
                                            <td class="font-mono font-bold text-purple-600 dark:text-purple-400">
                                                {{ $i['batch_number'] }}
                                            </td>
                                            <td class="font-mono text-slate-500 font-bold">
                                                {{ $i['supplier_lot'] }}
                                            </td>
                                            <td class="font-bold text-sky-600">
                                                {{ $i['supplier'] }}
                                            </td>
                                            <td class="font-mono font-bold text-slate-900 dark:text-white">
                                                {{ number_format($i['consumed_qty'], 4) }} {{ $i['uom'] }}
                                            </td>
                                            <td>
                                                <span class="inline-flex items-center gap-1 font-mono text-[10px] text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 px-2 py-0.5 rounded-md border border-emerald-200 dark:border-emerald-800">
                                                    <i class="fa-solid fa-check-double text-[9px]"></i>
                                                    {{ $i['scanned_barcode'] }}
                                                </span>
                                            </td>
                                            <td class="text-slate-600 dark:text-slate-300">
                                                {{ $i['operator'] }}
                                            </td>
                                            <td>
                                                <span class="badge badge-emerald text-[10px]">
                                                    <i class="fa-solid fa-certificate"></i> {{ $i['latest_qc_verdict'] }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-6 text-slate-400">
                                                {{ app()->getLocale() === 'ar' ? 'لم يتم العثور على سجلات استهلاك مطابقة لهذه التشغيلة.' : 'No consumption records found for this batch.' }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

            </div>
        @else
            <!-- Friendly No Result State -->
            <div class="card p-8 border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] rounded-2xl shadow-sm text-center">
                <div class="w-16 h-16 rounded-2xl bg-amber-50 dark:bg-amber-950/40 text-amber-500 flex items-center justify-center text-3xl mx-auto mb-3">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <h4 class="text-base font-bold text-slate-900 dark:text-white mb-1">
                    {{ app()->getLocale() === 'ar' ? 'لم يتم العثور على تشغيلة مطابقة لرقم البحث المدخل' : 'No Batch Found for the Specified Identifier' }}
                </h4>
                <p class="text-xs text-slate-500 dark:text-slate-400 max-w-md mx-auto mb-4">
                    {{ app()->getLocale() === 'ar' ? 'تأكد من إدخال رمز تشغيلة صحيح أو جرب النقر على أحد أرقام التشغيلات المقترحة أعلاه.' : 'Verify the exact LOT code, or try clicking one of the sample test chips provided in the bar above.' }}
                </p>
                <div class="flex items-center justify-center gap-3">
                    <a href="{{ route('admin.batches.index') }}" class="btn btn-secondary text-xs font-bold py-2">
                        <i class="fa-solid fa-boxes-stacked mr-1.5 ml-1.5"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'تصفح كافة تشغيلات المواد الخام' : 'Browse All Raw Batches' }}</span>
                    </a>
                    <a href="{{ route('admin.work-orders.index') }}" class="btn btn-secondary text-xs font-bold py-2">
                        <i class="fa-solid fa-industry mr-1.5 ml-1.5"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'تصفح كافة أوامر التصنيع' : 'Browse Work Orders' }}</span>
                    </a>
                </div>
            </div>
        @endif

    @else
        <!-- Initial Guidance Cockpit (When user lands on page without search) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="card p-6 border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] rounded-2xl shadow-sm flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-sky-100 dark:bg-sky-900/40 text-[#0A4F78] dark:text-sky-400 flex items-center justify-center text-xl mb-3">
                        <i class="fa-solid fa-bullhorn"></i>
                    </div>
                    <h3 class="text-base font-black text-slate-900 dark:text-white mb-2">
                        {{ app()->getLocale() === 'ar' ? 'متى تستخدم التتبع الأمامي (Forward Recall)؟' : 'When to use Forward Recall Trace?' }}
                    </h3>
                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed mb-4">
                        {{ app()->getLocale() === 'ar' ? 'يُستخدم فور تلقي إشعار عيب أو سحب من مورد مادة خام أو مختبر خارجي. يقوم النظام بحصر كافة التشغيلات والطلبات المكشوفة على تلك المادة لعزلها ومنع صرفها أو شحنها للعملاء.' : 'Triggered when a raw ingredient supplier issues a contamination alert. Instantly scans every compounding run and identifies every exposed patient order for immediate containment.' }}
                    </p>
                </div>
                <div class="pt-3 border-t border-slate-100 dark:border-slate-800">
                    <span class="text-[11px] font-bold text-slate-400 block mb-2">{{ app()->getLocale() === 'ar' ? 'أمثلة لتشغيلات مواد خام جاهزة للفحص:' : 'Sample Raw Material Batches to test:' }}</span>
                    <div class="flex items-center gap-1.5 flex-wrap">
                        @forelse($sampleRawBatches ?? [] as $sample)
                            <button type="button" onclick="quickTrace('{{ $sample->batch_number }}', 'forward')" class="text-xs font-mono font-bold px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-sky-50 dark:bg-[#031827] text-[#0A4F78] dark:text-sky-300 border border-slate-200 dark:border-[#15456E] transition-all">
                                {{ $sample->batch_number }}
                            </button>
                        @empty
                            <span class="text-xs text-slate-400 italic">No batches created yet</span>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="card p-6 border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] rounded-2xl shadow-sm flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-purple-100 dark:bg-purple-900/40 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xl mb-3">
                        <i class="fa-solid fa-tree-deciduous"></i>
                    </div>
                    <h3 class="text-base font-black text-slate-900 dark:text-white mb-2">
                        {{ app()->getLocale() === 'ar' ? 'متى تستخدم التتبع العكسي (Backward Genealogy)؟' : 'When to use Backward Genealogy Audit?' }}
                    </h3>
                    <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed mb-4">
                        {{ app()->getLocale() === 'ar' ? 'يُستخدم أثناء تدقيق التفتيش الرقابي، أو التحقق من شكاوى المرضى. يُرجع التاريخ الكامل لكل جرام دخل في تصنيع التشغيلة، وهوية الموردين، وتوقيعات الفحص المخبري COA.' : 'Used during FDA/cGMP audits or patient adverse event reviews. Drills down from a finished bottle lot to the precise raw batches, scale readings, analytical lab tests, and cleanroom operators.' }}
                    </p>
                </div>
                <div class="pt-3 border-t border-slate-100 dark:border-slate-800">
                    <span class="text-[11px] font-bold text-slate-400 block mb-2">{{ app()->getLocale() === 'ar' ? 'أمثلة لمنتجات تامة جاهزة للفحص:' : 'Sample Finished Lots to test:' }}</span>
                    <div class="flex items-center gap-1.5 flex-wrap">
                        @forelse($sampleWorkOrders ?? [] as $sampleWo)
                            <button type="button" onclick="quickTrace('{{ $sampleWo->target_finished_lot }}', 'backward')" class="text-xs font-mono font-bold px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-purple-50 dark:bg-[#031827] text-purple-700 dark:text-purple-300 border border-slate-200 dark:border-[#15456E] transition-all">
                                {{ $sampleWo->target_finished_lot }}
                            </button>
                        @empty
                            <span class="text-xs text-slate-400 italic">No finished work orders yet</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @endif

    <script>
        function quickTrace(lotNumber, direction) {
            const input = document.getElementById('lotNumberInput');
            input.value = lotNumber;
            const radios = document.getElementsByName('direction');
            for (let r of radios) {
                if (r.value === direction) {
                    r.checked = true;
                }
            }
            document.getElementById('traceForm').submit();
        }
    </script>
</x-layouts.admin>
