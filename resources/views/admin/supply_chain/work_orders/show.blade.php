<x-layouts.admin 
    :pageTitle="__('production.wo_number') . ': ' . $workOrder->wo_number" 
    :pageSubtitle="__('production.product') . ': ' . $workOrder->product?->name_en . ' | ' . __('production.batch_number') . ': ' . $workOrder->product_batch_number"
    :breadcrumbs="[__('production.title') => route('admin.work-orders.index'), $workOrder->wo_number => '#']"
>
    <x-slot name="actions">
        <div class="flex items-center gap-2" x-data="{ dispenseOpen: false, completeOpen: false }">
            @if($workOrder->status === \App\Enums\WorkOrderStatus::Planned)
                <form method="POST" action="{{ route('admin.work-orders.start', $workOrder) }}">
                    @csrf
                    <button type="submit" class="btn btn-primary font-bold text-sm bg-indigo-600 hover:bg-indigo-700 text-white">
                        <i class="fa-solid fa-play mr-1 ml-1"></i> {{ __('production.start_production') }}
                    </button>
                </form>
            @endif

            @if($workOrder->status === \App\Enums\WorkOrderStatus::InProgress)
                <button type="button" @click="dispenseOpen = true" class="btn btn-primary font-bold text-sm bg-emerald-600 hover:bg-emerald-700 text-white">
                    <i class="fa-solid fa-barcode mr-1 ml-1"></i> {{ __('production.dispense_material') }}
                </button>

                <button type="button" @click="completeOpen = true" class="btn btn-secondary font-bold text-sm">
                    <i class="fa-solid fa-flag-checkered mr-1 ml-1"></i> {{ __('production.complete_wo') }}
                </button>
            @endif

            <!-- Dispense Modal -->
            <div x-show="dispenseOpen" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-black/60" style="display: none;">
                <div class="card max-w-lg w-full p-6 shadow-2xl space-y-4" @click.away="dispenseOpen = false">
                    <h3 class="text-base font-bold text-slate-800 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-2 flex items-center gap-2">
                        <i class="fa-solid fa-scale-balanced text-emerald-600"></i>
                        {{ app()->getLocale() === 'ar' ? 'صرف مادة أولية إلى غرفة الخلط' : 'Cleanroom Gravimetric Dispense' }}
                    </h3>
                    <form method="POST" action="{{ route('admin.work-orders.dispense', $workOrder) }}" class="space-y-3">
                        @csrf
                        <div>
                            <label class="form-label font-bold text-xs">{{ __('purchase_orders.raw_material') }} <span class="text-rose-500">*</span></label>
                            <select name="raw_material_batch_id" required class="form-select text-xs font-mono">
                                <option value="">{{ app()->getLocale() === 'ar' ? '-- اختر تشغيلة المادة الأولية المعتمدة --' : '-- Select Approved Batch --' }}</option>
                                @foreach($batches as $b)
                                    <option value="{{ $b->id }}">
                                        {{ $b->rawMaterial?->name }} ({{ $b->batch_number }} - Avail: {{ number_format($b->available_qty, 2) }} {{ $b->rawMaterial?->uom?->value }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label font-bold text-xs">{{ __('production.quantity') }} <span class="text-rose-500">*</span></label>
                            <input type="number" step="0.0001" name="quantity" required class="form-control text-xs font-mono font-bold" placeholder="0.0000">
                        </div>
                        <div>
                            <label class="form-label font-bold text-xs">{{ app()->getLocale() === 'ar' ? 'الباركود الممسوح ضوئياً (التحقق الإلزامي)' : 'Scanned Barcode / Verification' }} <span class="text-rose-500">*</span></label>
                            <input type="text" name="scanned_barcode" required class="form-control text-xs font-mono" placeholder="Scan batch barcode label...">
                        </div>
                        <div>
                            <label class="form-label font-bold text-xs">{{ __('production.notes') }}</label>
                            <input type="text" name="notes" class="form-control text-xs" placeholder="Balance scale calibration ID #...">
                        </div>
                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" @click="dispenseOpen = false" class="btn btn-secondary btn-sm">{{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}</button>
                            <button type="submit" class="btn btn-primary btn-sm font-bold bg-emerald-600 hover:bg-emerald-700 text-white">{{ app()->getLocale() === 'ar' ? 'تأكيد الصرف وتحديث السجل' : 'Confirm Dispense' }}</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Complete Modal -->
            <div x-show="completeOpen" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-black/60" style="display: none;">
                <div class="card max-w-md w-full p-6 shadow-2xl space-y-4" @click.away="completeOpen = false">
                    <h3 class="text-base font-bold text-slate-800 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-2">
                        {{ app()->getLocale() === 'ar' ? 'إنهاء أمر التشغيل وإيداع المنتج التام' : 'Complete Work Order & Yield Stock' }}
                    </h3>
                    <form method="POST" action="{{ route('admin.work-orders.complete', $workOrder) }}" class="space-y-3">
                        @csrf
                        <div>
                            <label class="form-label font-bold text-xs">{{ app()->getLocale() === 'ar' ? 'الكمية الفعلية المنتجة (العبوات التامة)' : 'Actual Finished Units Produced' }} <span class="text-rose-500">*</span></label>
                            <input type="number" name="actual_quantity" value="{{ $workOrder->target_quantity }}" required class="form-control text-sm font-mono font-bold">
                        </div>
                        <div>
                            <label class="form-label font-bold text-xs">{{ app()->getLocale() === 'ar' ? 'عدد العبوات التالفة (Scrap)' : 'Scrap Units Count' }}</label>
                            <input type="number" name="scrap_quantity" value="0" class="form-control text-sm font-mono">
                        </div>
                        <div>
                            <label class="form-label font-bold text-xs">{{ __('production.notes') }}</label>
                            <textarea name="notes" rows="2" class="form-control text-xs" placeholder="Final packaging yield, packaging line reconciliation..."></textarea>
                        </div>
                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" @click="completeOpen = false" class="btn btn-secondary btn-sm">{{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}</button>
                            <button type="submit" class="btn btn-primary btn-sm font-bold">{{ app()->getLocale() === 'ar' ? 'إتمام الإنتاج وحساب التكلفة' : 'Finalize & Rollup Cost' }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </x-slot>

    <!-- Work Order Status Ribbon -->
    <div class="card p-5 mb-6 border shadow-sm border-gray-100 dark:border-gray-800">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-industry"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h4 class="text-base font-black text-slate-800 dark:text-white font-mono">{{ $workOrder->wo_number }}</h4>
                        <span class="badge {{ $workOrder->status->badgeClass() }} text-xs">
                            {{ $workOrder->status->label() }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500">
                        {{ __('production.batch_number') }}: <span class="font-mono font-bold text-purple-600">{{ $workOrder->product_batch_number }}</span> |
                        {{ __('production.scheduled_date') }}: {{ $workOrder->scheduled_date?->format('Y-m-d') }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3 text-xs">
                <div class="bg-gray-50 dark:bg-slate-800/50 p-2.5 rounded-lg border border-gray-200 dark:border-gray-700 text-center">
                    <span class="text-gray-400 block">{{ __('production.target_quantity') }}</span>
                    <span class="font-mono font-bold text-slate-800 dark:text-white text-sm">{{ number_format($workOrder->target_quantity) }}</span>
                </div>
                <div class="bg-gray-50 dark:bg-slate-800/50 p-2.5 rounded-lg border border-gray-200 dark:border-gray-700 text-center">
                    <span class="text-gray-400 block">{{ __('production.actual_quantity') }}</span>
                    <span class="font-mono font-bold text-emerald-600 text-sm">{{ number_format($workOrder->actual_quantity) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- BOM Genealogy & Consumptions Table (Immutable) -->
    <div class="card p-5 mb-6 border border-gray-100 dark:border-gray-800 shadow-sm">
        <h4 class="text-sm font-bold text-slate-800 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-3 mb-4 flex items-center justify-between">
            <span><i class="fa-solid fa-dna text-emerald-600 mr-2 ml-2"></i> {{ app()->getLocale() === 'ar' ? 'سجل صرف المواد وسلسلة نسب التشغيلة (Genealogy)' : 'Material Dispense Genealogy Ledger (Immutable)' }}</span>
            <span class="badge badge-emerald text-xs">{{ $workOrder->consumptions->count() }} {{ app()->getLocale() === 'ar' ? 'عمليات صرف' : 'Dispensations' }}</span>
        </h4>

        <div class="table-responsive">
            <table class="table text-xs">
                <thead>
                    <tr>
                        <th>{{ app()->getLocale() === 'ar' ? 'المادة الأولية' : 'Raw Material' }}</th>
                        <th>{{ __('batches.batch_number') }}</th>
                        <th>{{ __('batches.supplier_lot') }}</th>
                        <th>{{ __('production.quantity') }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'الباركود الممسوح' : 'Scanned Barcode' }}</th>
                        <th>{{ __('production.operator') }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'تاريخ الصرف' : 'Timestamp' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($workOrder->consumptions as $c)
                        <tr>
                            <td class="font-bold text-slate-800 dark:text-white">
                                <a href="{{ route('admin.raw-materials.show', $c->batch?->rawMaterial) }}" class="text-sky-600 hover:underline">
                                    {{ $c->batch?->rawMaterial?->name }}
                                </a>
                                <span class="block text-[11px] text-gray-400 font-mono">{{ $c->batch?->rawMaterial?->sku }}</span>
                            </td>
                            <td class="font-mono font-bold text-purple-600">
                                <a href="{{ route('admin.batches.show', $c->batch) }}" class="hover:underline">
                                    {{ $c->batch?->batch_number }}
                                </a>
                            </td>
                            <td class="font-mono text-gray-500">{{ $c->batch?->supplier_lot }}</td>
                            <td class="font-mono font-bold text-emerald-600">
                                {{ number_format($c->quantity, 4) }} {{ $c->batch?->rawMaterial?->uom?->value }}
                            </td>
                            <td class="font-mono text-gray-500">{{ $c->scanned_barcode ?? 'VERIFIED' }}</td>
                            <td>{{ $c->operator?->name ?? 'Cleanroom Tech' }}</td>
                            <td class="font-mono text-gray-400">{{ $c->created_at?->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-6 text-gray-400">
                                {{ app()->getLocale() === 'ar' ? 'لم يتم صرف أي مواد لهذا الأمر بعد. استخدم زر صرف مادة أعلاه.' : 'No materials dispensed yet. Click "Dispense Material" to log cleanroom batch addition.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
