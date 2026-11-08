<x-layouts.admin 
    :pageTitle="__('production.wo_number') . ': ' . ($workOrder->order_number ?? $workOrder->wo_number)" 
    :pageSubtitle="__('production.target_product') . ': ' . (app()->getLocale() === 'ar' && !empty($workOrder->product?->name_ar) ? $workOrder->product->name_ar : $workOrder->product?->name_en) . ' | ' . __('production.batch_number') . ': ' . ($workOrder->target_finished_lot ?? $workOrder->product_batch_number)"
    :breadcrumbs="[__('production.title') => route('admin.work-orders.index'), ($workOrder->order_number ?? $workOrder->wo_number) => '#']"
>
    <x-slot name="actions">
        <div class="flex items-center gap-2 flex-wrap" x-data="{ dispenseOpen: false, completeOpen: false, cancelOpen: false }">
            <a href="{{ route('admin.work-orders.index') }}" class="btn btn-secondary font-bold text-xs shadow-sm inline-flex items-center gap-1.5 px-3 py-2">
                <i class="fa-solid fa-arrow-left rtl:rotate-180"></i>
                <span>{{ app()->getLocale() === 'ar' ? 'العودة لأوامر الإنتاج' : 'Back to Work Orders' }}</span>
            </a>

            <!-- Stage: Draft -> Schedule & Reserve -->
            @if($workOrder->status === \App\Enums\WorkOrderStatus::Draft)
                <form method="POST" action="{{ route('admin.work-orders.schedule', $workOrder) }}">
                    @csrf
                    <button type="submit" class="btn btn-primary font-bold text-sm bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm">
                        <i class="fa-solid fa-calendar-check mr-1.5 ml-1.5"></i> {{ __('production.actions.schedule') }}
                    </button>
                </form>
            @endif

            <!-- Stage: Scheduled -> Begin Dispensing -->
            @if($workOrder->status === \App\Enums\WorkOrderStatus::Scheduled)
                <form method="POST" action="{{ route('admin.work-orders.dispensing', $workOrder) }}">
                    @csrf
                    <button type="submit" class="btn btn-primary font-bold text-sm bg-sky-600 hover:bg-sky-700 text-white shadow-sm">
                        <i class="fa-solid fa-scale-balanced mr-1.5 ml-1.5"></i> {{ __('production.begin_dispensing') }}
                    </button>
                </form>
            @endif

            <!-- Stage: Dispensing Actions -->
            @if($workOrder->status === \App\Enums\WorkOrderStatus::Dispensing)
                <button type="button" @click="dispenseOpen = true" class="btn btn-primary font-bold text-sm bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm">
                    <i class="fa-solid fa-barcode mr-1.5 ml-1.5"></i> {{ __('production.dispense_material') }}
                </button>

                <form method="POST" action="{{ route('admin.work-orders.start-production', $workOrder) }}">
                    @csrf
                    <button type="submit" class="btn btn-secondary font-bold text-sm">
                        <i class="fa-solid fa-gears mr-1.5 ml-1.5 text-amber-500"></i> {{ __('production.begin_compounding') }}
                    </button>
                </form>

                <button type="button" @click="completeOpen = true" class="btn btn-primary font-bold text-sm bg-purple-600 hover:bg-purple-700 text-white shadow-sm">
                    <i class="fa-solid fa-flag-checkered mr-1.5 ml-1.5"></i> {{ __('production.complete_wo') }}
                </button>
            @endif

            <!-- Stage: InProduction Actions -->
            @if($workOrder->status === \App\Enums\WorkOrderStatus::InProduction)
                <form method="POST" action="{{ route('admin.work-orders.qc-review', $workOrder) }}">
                    @csrf
                    <button type="submit" class="btn btn-secondary font-bold text-sm">
                        <i class="fa-solid fa-microscope mr-1.5 ml-1.5 text-indigo-500"></i> {{ __('production.submit_qc') }}
                    </button>
                </form>

                <button type="button" @click="completeOpen = true" class="btn btn-primary font-bold text-sm bg-purple-600 hover:bg-purple-700 text-white shadow-sm">
                    <i class="fa-solid fa-flag-checkered mr-1.5 ml-1.5"></i> {{ __('production.complete_wo') }}
                </button>
            @endif

            <!-- Stage: QcReview Actions -->
            @if($workOrder->status === \App\Enums\WorkOrderStatus::QcReview)
                <button type="button" @click="completeOpen = true" class="btn btn-primary font-bold text-sm bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm">
                    <i class="fa-solid fa-certificate mr-1.5 ml-1.5"></i> {{ __('production.complete_wo') }}
                </button>
            @endif

            <!-- Cancel Button for uncompleted orders -->
            @if(!in_array($workOrder->status, [\App\Enums\WorkOrderStatus::Completed, \App\Enums\WorkOrderStatus::Cancelled]))
                <button type="button" @click="cancelOpen = true" class="btn btn-ghost btn-sm text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30">
                    <i class="fa-solid fa-ban mr-1 ml-1"></i> {{ __('production.cancel_wo') }}
                </button>
            @endif

            <!-- Dispense Modal -->
            <div x-show="dispenseOpen" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" style="display: none;">
                <div class="card max-w-lg w-full p-6 shadow-2xl space-y-4 border border-gray-100 dark:border-gray-800" @click.away="dispenseOpen = false">
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3">
                        <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-scale-balanced text-emerald-600"></i>
                            {{ app()->getLocale() === 'ar' ? 'صرف مادة أولية معقمة إلى غرفة الخلط' : 'Cleanroom Gravimetric Dispense' }}
                        </h3>
                        <button type="button" @click="dispenseOpen = false" class="text-gray-400 hover:text-gray-600">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('admin.work-orders.dispense', $workOrder) }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="form-label font-bold text-xs">{{ app()->getLocale() === 'ar' ? 'تشغيلة المادة الأولية المعتمدة' : 'Approved Raw Material Batch' }} <span class="text-rose-500">*</span></label>
                            <select name="raw_material_batch_id" required class="form-select text-xs font-mono">
                                <option value="">{{ app()->getLocale() === 'ar' ? '-- اختر تشغيلة المادة الأولية المعتمدة --' : '-- Select Approved Batch --' }}</option>
                                @foreach($batches as $b)
                                    <option value="{{ $b->id }}">
                                        {{ app()->getLocale() === 'ar' && !empty($b->rawMaterial?->name_ar) ? $b->rawMaterial->name_ar : ($b->rawMaterial?->name_en ?? $b->rawMaterial?->name) }}
                                        — [{{ $b->batch_number }} | Exp: {{ $b->expiry_date?->format('Y-m-d') }} | On-hand: {{ number_format($b->current_qty, 4) }} {{ $b->rawMaterial?->uom?->value ?? '' }}]
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label font-bold text-xs">{{ __('production.quantity') }} <span class="text-rose-500">*</span></label>
                            <input type="number" step="0.0001" min="0.0001" name="quantity" required class="form-control text-xs font-mono font-bold" placeholder="0.0000">
                        </div>
                        <div>
                            <label class="form-label font-bold text-xs">{{ app()->getLocale() === 'ar' ? 'الباركود الممسوح ضوئياً (التحقق الإلزامي)' : 'Scanned Barcode / Verification' }} <span class="text-rose-500">*</span></label>
                            <input type="text" name="scanned_barcode" required class="form-control text-xs font-mono" placeholder="Scan batch barcode label...">
                        </div>
                        <div>
                            <label class="form-label font-bold text-xs">{{ __('production.notes') }}</label>
                            <input type="text" name="notes" class="form-control text-xs" placeholder="Balance scale calibration ID #...">
                        </div>
                        <div class="flex justify-end gap-2 pt-2 border-t border-gray-100 dark:border-gray-800">
                            <button type="button" @click="dispenseOpen = false" class="btn btn-secondary btn-sm">{{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}</button>
                            <button type="submit" class="btn btn-primary btn-sm font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm">
                                <i class="fa-solid fa-check mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'تأكيد الصرف وخصم المخزون' : 'Confirm Dispense' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Complete Modal -->
            <div x-show="completeOpen" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" style="display: none;">
                <div class="card max-w-md w-full p-6 shadow-2xl space-y-4 border border-gray-100 dark:border-gray-800" @click.away="completeOpen = false">
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3">
                        <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-flag-checkered text-purple-600"></i>
                            {{ app()->getLocale() === 'ar' ? 'إنهاء أمر التشغيل وإيداع المخزون التام' : 'Complete Work Order & Yield Stock' }}
                        </h3>
                        <button type="button" @click="completeOpen = false" class="text-gray-400 hover:text-gray-600">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('admin.work-orders.complete', $workOrder) }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="form-label font-bold text-xs">{{ __('production.actual_quantity') }} ({{ app()->getLocale() === 'ar' ? 'العبوات التامة المطابقة' : 'Finished Goods Units' }}) <span class="text-rose-500">*</span></label>
                            <input type="number" name="actual_quantity" value="{{ $workOrder->planned_qty ?? $workOrder->target_quantity }}" min="1" required class="form-control text-sm font-mono font-bold">
                        </div>
                        <div>
                            <label class="form-label font-bold text-xs">{{ __('production.scrap_quantity') }} ({{ app()->getLocale() === 'ar' ? 'العبوات التالفة' : 'Scrap Units' }})</label>
                            <input type="number" name="scrap_quantity" value="0" min="0" class="form-control text-sm font-mono">
                        </div>
                        <div>
                            <label class="form-label font-bold text-xs">{{ __('production.notes') }}</label>
                            <textarea name="notes" rows="2" class="form-control text-xs" placeholder="{{ app()->getLocale() === 'ar' ? 'ملاحظات فحص الجودة التام ومطابقة العبوات والتعبئة...' : 'Final packaging yield, packaging line reconciliation...' }}"></textarea>
                        </div>
                        <div class="flex justify-end gap-2 pt-2 border-t border-gray-100 dark:border-gray-800">
                            <button type="button" @click="completeOpen = false" class="btn btn-secondary btn-sm">{{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}</button>
                            <button type="submit" class="btn btn-primary btn-sm font-bold bg-purple-600 hover:bg-purple-700 text-white shadow-sm">
                                <i class="fa-solid fa-check mr-1 ml-1"></i> {{ __('production.actions.complete') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Cancel Confirmation Modal -->
            <div x-show="cancelOpen" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" style="display: none;">
                <div class="card max-w-md w-full p-6 shadow-2xl space-y-4 border border-rose-100 dark:border-rose-900" @click.away="cancelOpen = false">
                    <div class="flex items-center gap-3 text-rose-600">
                        <i class="fa-solid fa-triangle-exclamation text-2xl"></i>
                        <h3 class="text-base font-bold text-slate-800 dark:text-white">
                            {{ __('production.cancel_wo') }}
                        </h3>
                    </div>
                    <p class="text-xs text-slate-600 dark:text-gray-400">
                        {{ app()->getLocale() === 'ar' ? 'هل أنت متأكد من إلغاء أمر التشغيل هذا؟ سيتم تحرير أي مواد أولية تم حجزها وإعادتها فوراً إلى الرصيد المتاح بالمستودع.' : 'Are you sure you want to cancel this Work Order? Any reserved raw material batches will be automatically unlocked and returned to available inventory.' }}
                    </p>
                    <div class="flex justify-end gap-2 pt-2 border-t border-gray-100 dark:border-gray-800">
                        <button type="button" @click="cancelOpen = false" class="btn btn-secondary btn-sm">{{ app()->getLocale() === 'ar' ? 'تراجع' : 'Back' }}</button>
                        <form method="POST" action="{{ route('admin.work-orders.cancel', $workOrder) }}">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm font-bold">
                                {{ __('production.cancel_wo') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <!-- Work Order Status Pipeline Ribbon -->
    <div class="card p-5 mb-6 border shadow-sm border-gray-100 dark:border-gray-800">
        <div class="flex items-center justify-between flex-wrap gap-4 border-b border-gray-100 dark:border-gray-800 pb-4 mb-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 flex items-center justify-center text-xl shadow-inner">
                    <i class="fa-solid fa-industry"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h4 class="text-lg font-black text-slate-800 dark:text-white font-mono">{{ $workOrder->order_number ?? $workOrder->wo_number }}</h4>
                        <span class="badge {{ $workOrder->status->badgeClass() }} text-xs">
                            {{ $workOrder->status->label() }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        {{ __('production.batch_number') }}: <span class="font-mono font-bold text-purple-600 dark:text-purple-400">{{ $workOrder->target_finished_lot ?? $workOrder->product_batch_number }}</span> |
                        {{ __('production.scheduled_date') }}: <span class="font-mono">{{ ($workOrder->scheduled_start ?? $workOrder->scheduled_date)?->format('Y-m-d') }}</span>
                    </p>
                </div>
            </div>

            <!-- Quick Metrics -->
            <div class="flex items-center gap-3 text-xs flex-wrap">
                <div class="bg-gray-50 dark:bg-slate-800/50 p-2.5 rounded-lg border border-gray-200 dark:border-gray-700 text-center min-w-[100px]">
                    <span class="text-gray-400 block">{{ __('production.target_quantity') }}</span>
                    <span class="font-mono font-bold text-slate-800 dark:text-white text-sm">{{ number_format($workOrder->planned_qty ?? $workOrder->target_quantity) }}</span>
                </div>
                <div class="bg-gray-50 dark:bg-slate-800/50 p-2.5 rounded-lg border border-gray-200 dark:border-gray-700 text-center min-w-[100px]">
                    <span class="text-gray-400 block">{{ __('production.actual_quantity') }}</span>
                    <span class="font-mono font-bold text-emerald-600 text-sm">{{ number_format($workOrder->produced_qty ?? $workOrder->actual_quantity ?? 0) }}</span>
                </div>
                <div class="bg-gray-50 dark:bg-slate-800/50 p-2.5 rounded-lg border border-gray-200 dark:border-gray-700 text-center min-w-[110px]">
                    <span class="text-gray-400 block">{{ __('production.unit_cost') }}</span>
                    <span class="font-mono font-bold text-sky-600 text-sm">{{ number_format($workOrder->unit_cost ?? 0, 4) }} <span class="text-[10px]">SAR</span></span>
                </div>
            </div>
        </div>

        <!-- Manufacturing Stages Visual Progress Bar -->
        @php
            $stages = [
                ['key' => 'draft', 'label' => app()->getLocale() === 'ar' ? 'المسودة' : 'Draft', 'icon' => 'fa-file-lines'],
                ['key' => 'scheduled', 'label' => app()->getLocale() === 'ar' ? 'مجدول ومحجوز' : 'Scheduled & Reserved', 'icon' => 'fa-calendar-check'],
                ['key' => 'dispensing', 'label' => app()->getLocale() === 'ar' ? 'صرف المواد' : 'Dispensing', 'icon' => 'fa-scale-balanced'],
                ['key' => 'in_production', 'label' => app()->getLocale() === 'ar' ? 'خط التصنيع' : 'In Production', 'icon' => 'fa-gears'],
                ['key' => 'qc_review', 'label' => app()->getLocale() === 'ar' ? 'فحص الجودة' : 'QC Review', 'icon' => 'fa-microscope'],
                ['key' => 'completed', 'label' => app()->getLocale() === 'ar' ? 'مكتمل ومودع' : 'Completed', 'icon' => 'fa-circle-check'],
            ];

            $orderOfStages = ['draft' => 1, 'scheduled' => 2, 'dispensing' => 3, 'in_production' => 4, 'qc_review' => 5, 'completed' => 6, 'cancelled' => 0];
            $currentStepIndex = $orderOfStages[$workOrder->status->value] ?? 1;
        @endphp

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2 pt-2">
            @foreach($stages as $idx => $st)
                @php
                    $stepNum = $idx + 1;
                    $isPassed = $workOrder->status->value !== 'cancelled' && $currentStepIndex > $stepNum;
                    $isCurrent = $workOrder->status->value === $st['key'];
                @endphp
                <div class="p-2.5 rounded-lg border text-center transition-all {{ $isCurrent ? 'bg-indigo-50 dark:bg-indigo-950/40 border-indigo-300 dark:border-indigo-700 font-bold text-indigo-700 dark:text-indigo-300' : ($isPassed ? 'bg-emerald-50/40 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400' : 'bg-gray-50/50 dark:bg-slate-800/30 border-gray-100 dark:border-gray-800 text-gray-400') }}">
                    <div class="text-sm mb-1">
                        @if($isPassed)
                            <i class="fa-solid fa-check text-emerald-600"></i>
                        @else
                            <i class="fa-solid {{ $st['icon'] }}"></i>
                        @endif
                    </div>
                    <div class="text-[11px] leading-tight">{{ $st['label'] }}</div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Product & Formulation Recipe (BOM) Requirements Section -->
    <div class="card p-5 mb-6 border border-gray-100 dark:border-gray-800 shadow-sm space-y-4">
        <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3">
            <div>
                <h4 class="text-sm font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-flask text-indigo-600"></i>
                    {{ __('production.bom_preview') }}
                </h4>
                <p class="text-xs text-gray-400 mt-0.5">
                    {{ app()->getLocale() === 'ar' ? 'المكونات المطلوبة من واقع تركيبة المنتج القياسية لحجم هذه التشغيلة' : 'Formulation BOM dosages scaled to target batch yield.' }}
                </p>
            </div>
            <div class="text-xs font-mono font-semibold text-gray-500">
                {{ app()->getLocale() === 'ar' ? 'المستودع المستهدف:' : 'Destination Warehouse:' }}
                <span class="text-slate-800 dark:text-white font-bold">{{ $workOrder->location?->name_en ?? $workOrder->location_id }}</span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table text-xs">
                <thead>
                    <tr>
                        <th>{{ app()->getLocale() === 'ar' ? 'المادة الأولية' : 'Raw Material' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'الرمز الكودي' : 'Code' }}</th>
                        <th style="text-align: right;">{{ app()->getLocale() === 'ar' ? 'الجرعة / وحدة' : 'Dosage / Unit' }}</th>
                        <th style="text-align: right;">{{ app()->getLocale() === 'ar' ? 'نسبة الفاقد' : 'Scrap %' }}</th>
                        <th style="text-align: right;">{{ app()->getLocale() === 'ar' ? 'الاحتياج الإجمالي' : 'Total Required' }}</th>
                        <th style="text-align: right;">{{ app()->getLocale() === 'ar' ? 'تم صرفه فعلياً' : 'Total Dispensed' }}</th>
                        <th style="text-align: center;">{{ app()->getLocale() === 'ar' ? 'حالة الاستيفاء' : 'Fulfillment Status' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $plannedUnits = $workOrder->planned_qty ?? $workOrder->target_quantity;
                    @endphp
                    @forelse($workOrder->product?->recipeItems ?? [] as $ri)
                        @php
                            $dosage = (float) $ri->quantity_per_unit;
                            $scrap = (float) $ri->loss_percentage;
                            $totalReq = ($dosage * $plannedUnits) * (1 + ($scrap / 100));
                            $uom = $ri->rawMaterial?->uom?->value ?? ($ri->rawMaterial?->unit_of_measure?->value ?? '');
                            $dispensedQty = (float) $workOrder->consumptions
                                ->filter(fn($c) => $c->batch?->raw_material_id === $ri->raw_material_id)
                                ->sum('quantity');
                            $isComplete = $dispensedQty >= ($totalReq * 0.999);
                        @endphp
                        <tr>
                            <td class="font-bold text-slate-800 dark:text-white">
                                <a href="{{ route('admin.raw-materials.show', $ri->rawMaterial) }}" class="text-sky-600 hover:underline">
                                    {{ app()->getLocale() === 'ar' && !empty($ri->rawMaterial?->name_ar) ? $ri->rawMaterial->name_ar : ($ri->rawMaterial?->name_en ?? $ri->rawMaterial?->name) }}
                                </a>
                            </td>
                            <td class="font-mono text-gray-500">{{ $ri->rawMaterial?->code ?? $ri->rawMaterial?->sku }}</td>
                            <td class="font-mono" style="text-align: right;">{{ number_format($dosage, 6) }} {{ $uom }}</td>
                            <td class="font-mono text-gray-500" style="text-align: right;">{{ $scrap }}%</td>
                            <td class="font-mono font-bold text-indigo-600" style="text-align: right;">{{ number_format($totalReq, 4) }} {{ $uom }}</td>
                            <td class="font-mono font-bold text-emerald-600" style="text-align: right;">{{ number_format($dispensedQty, 4) }} {{ $uom }}</td>
                            <td style="text-align: center;">
                                @if($isComplete)
                                    <span class="badge badge-emerald text-[11px]">{{ app()->getLocale() === 'ar' ? 'مكتمل الصرف' : 'Fully Dispensed' }}</span>
                                @elseif($dispensedQty > 0)
                                    <span class="badge badge-warning text-[11px]">{{ app()->getLocale() === 'ar' ? 'صرف جزئي' : 'Partially Dispensed' }}</span>
                                @else
                                    <span class="badge badge-neutral text-[11px]">{{ app()->getLocale() === 'ar' ? 'بانتظار الصرف' : 'Awaiting Dispense' }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-6 text-gray-400">
                                {{ __('production.errors.no_recipe') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- BOM Genealogy & Consumptions Table (Immutable) -->
    <div class="card p-5 mb-6 border border-gray-100 dark:border-gray-800 shadow-sm">
        <h4 class="text-sm font-bold text-slate-800 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-3 mb-4 flex items-center justify-between">
            <span>
                <i class="fa-solid fa-dna text-emerald-600 mr-2 ml-2"></i> 
                {{ __('production.genealogy_ledger') }}
            </span>
            <span class="badge badge-emerald text-xs font-mono font-bold">
                {{ $workOrder->consumptions->count() }} {{ app()->getLocale() === 'ar' ? 'عمليات صرف موثقة' : 'Immutable Records' }}
            </span>
        </h4>

        <div class="table-responsive">
            <table class="table text-xs">
                <thead>
                    <tr>
                        <th>{{ app()->getLocale() === 'ar' ? 'المادة الأولية' : 'Raw Material' }}</th>
                        <th>{{ __('batches.batch_number') }}</th>
                        <th>{{ __('batches.supplier_lot') }}</th>
                        <th style="text-align: right;">{{ __('production.quantity') }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'الباركود الممسوح' : 'Scanned Barcode' }}</th>
                        <th>{{ __('production.operator') }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'تاريخ ووقت الصرف' : 'Timestamp' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($workOrder->consumptions as $c)
                        <tr>
                            <td class="font-bold text-slate-800 dark:text-white">
                                <a href="{{ route('admin.raw-materials.show', $c->batch?->rawMaterial) }}" class="text-sky-600 hover:underline">
                                    {{ app()->getLocale() === 'ar' && !empty($c->batch?->rawMaterial?->name_ar) ? $c->batch->rawMaterial->name_ar : ($c->batch?->rawMaterial?->name_en ?? $c->batch?->rawMaterial?->name) }}
                                </a>
                                <span class="block text-[11px] text-gray-400 font-mono">{{ $c->batch?->rawMaterial?->code ?? $c->batch?->rawMaterial?->sku }}</span>
                            </td>
                            <td class="font-mono font-bold text-purple-600">
                                <a href="{{ route('admin.batches.show', $c->batch) }}" class="hover:underline">
                                    {{ $c->batch?->batch_number }}
                                </a>
                            </td>
                            <td class="font-mono text-gray-500">{{ $c->batch?->supplier_lot ?? '—' }}</td>
                            <td class="font-mono font-bold text-emerald-600" style="text-align: right;">
                                {{ number_format($c->quantity, 4) }} {{ $c->batch?->rawMaterial?->uom?->value ?? '' }}
                            </td>
                            <td class="font-mono text-gray-500">{{ $c->scanned_barcode ?? 'VERIFIED' }}</td>
                            <td>{{ $c->operator?->name ?? 'Cleanroom Tech' }}</td>
                            <td class="font-mono text-gray-400">{{ $c->created_at?->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-6 text-gray-400">
                                {{ app()->getLocale() === 'ar' ? 'لم يتم صرف أي مواد لهذا الأمر بعد. استخدم زر "صرف مادة أولية معقمة" أعلاه لتوثيق الصرف من واقع الباركود.' : 'No materials dispensed yet. Click "Dispense Material" above to log cleanroom batch addition.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
