<x-layouts.admin 
    :pageTitle="__('batches.batch_number') . ': ' . $batch->batch_number" 
    :pageSubtitle="__('purchase_orders.raw_material') . ': ' . $batch->rawMaterial?->name . ' (' . $batch->rawMaterial?->sku . ')'"
    :breadcrumbs="[__('batches.title') => route('admin.batches.index'), $batch->batch_number => '#']"
>
    <x-slot name="actions">
        <div class="flex items-center gap-2" x-data="{ transferOpen: false, adjustOpen: false }">
            @if($batch->status === \App\Enums\RawMaterialBatchStatus::Quarantine)
                <a href="{{ route('admin.qc.create', ['batch_id' => $batch->id]) }}" class="btn btn-primary font-bold text-sm bg-purple-600 hover:bg-purple-700 text-white">
                    <i class="fa-solid fa-microscope mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'فحص الجودة وتسجيل COA' : 'Perform QC & Release' }}
                </a>
            @endif

            @if($batch->status === \App\Enums\RawMaterialBatchStatus::Approved && $batch->available_qty > 0)
                <button type="button" @click="transferOpen = true" class="btn btn-secondary font-bold text-sm">
                    <i class="fa-solid fa-arrow-right-arrow-left mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'نقل إلى موقع آخر' : 'Bin Transfer' }}
                </button>

                <button type="button" @click="adjustOpen = true" class="btn btn-secondary font-bold text-sm">
                    <i class="fa-solid fa-sliders mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'تسوية جردية' : 'Stock Adjustment' }}
                </button>
            @endif

            <!-- Bin Transfer Modal -->
            <div x-show="transferOpen" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-black/60" style="display: none;">
                <div class="card max-w-md w-full p-6 shadow-2xl space-y-4" @click.away="transferOpen = false">
                    <h3 class="text-base font-bold text-slate-800 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-2">
                        {{ app()->getLocale() === 'ar' ? 'نقل التشغيلة بين المستودعات' : 'Transfer Batch to New Location' }}
                    </h3>
                    <form method="POST" action="{{ route('admin.batches.transfer', $batch) }}" class="space-y-3">
                        @csrf
                        <div>
                            <label class="form-label font-bold text-xs">{{ app()->getLocale() === 'ar' ? 'الموقع الحالي' : 'Current Location' }}</label>
                            <input type="text" readonly value="{{ $batch->location_id }}" class="form-control text-xs font-mono bg-gray-100 dark:bg-slate-800">
                        </div>
                        <div>
                            <label class="form-label font-bold text-xs">{{ app()->getLocale() === 'ar' ? 'الموقع الجديد' : 'Destination Location' }} <span class="text-rose-500">*</span></label>
                            <select name="to_location_id" required class="form-select text-xs font-mono">
                                @foreach($locations as $loc)
                                    @if($loc->id !== $batch->location_id)
                                        <option value="{{ $loc->id }}">{{ $loc->name ?? $loc->id }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label font-bold text-xs">{{ app()->getLocale() === 'ar' ? 'الكمية المنقولة' : 'Transfer Quantity' }} <span class="text-rose-500">*</span></label>
                            <input type="number" step="0.0001" name="quantity" max="{{ $batch->available_qty }}" value="{{ $batch->available_qty }}" required class="form-control text-xs font-mono">
                            <span class="text-[11px] text-gray-500">{{ app()->getLocale() === 'ar' ? 'المتاح' : 'Available' }}: {{ number_format($batch->available_qty, 2) }} {{ $batch->rawMaterial?->uom?->value }}</span>
                        </div>
                        <div>
                            <label class="form-label font-bold text-xs">{{ app()->getLocale() === 'ar' ? 'سبب النقل' : 'Reason / Note' }}</label>
                            <textarea name="reason" rows="2" class="form-control text-xs" placeholder="e.g. Relocated to Cleanroom Dispense staging"></textarea>
                        </div>
                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" @click="transferOpen = false" class="btn btn-secondary btn-sm">{{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}</button>
                            <button type="submit" class="btn btn-primary btn-sm font-bold">{{ app()->getLocale() === 'ar' ? 'تنفيذ النقل' : 'Execute Transfer' }}</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Stock Adjustment Modal -->
            <div x-show="adjustOpen" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-black/60" style="display: none;">
                <div class="card max-w-md w-full p-6 shadow-2xl space-y-4" @click.away="adjustOpen = false">
                    <h3 class="text-base font-bold text-slate-800 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-2">
                        {{ app()->getLocale() === 'ar' ? 'تسوية جردية للتشغيلة' : 'Batch Inventory Adjustment' }}
                    </h3>
                    <form method="POST" action="{{ route('admin.batches.adjust', $batch) }}" class="space-y-3">
                        @csrf
                        <div>
                            <label class="form-label font-bold text-xs">{{ app()->getLocale() === 'ar' ? 'الكمية الحالية' : 'Current Physical Qty' }}</label>
                            <input type="text" readonly value="{{ number_format($batch->current_qty, 2) }} {{ $batch->rawMaterial?->uom?->value }}" class="form-control text-xs font-mono bg-gray-100 dark:bg-slate-800">
                        </div>
                        <div>
                            <label class="form-label font-bold text-xs">{{ app()->getLocale() === 'ar' ? 'فرق التسوية (+ أو -)' : 'Adjustment Delta (+ or -)' }} <span class="text-rose-500">*</span></label>
                            <input type="number" step="0.0001" name="adjustment_qty" required class="form-control text-xs font-mono" placeholder="e.g. -0.5000 or +1.2000">
                            <span class="text-[11px] text-gray-500">{{ app()->getLocale() === 'ar' ? 'التسويات الكبيرة تتطلب اعتماداً ثانياً' : 'Significant variances require secondary approval' }}</span>
                        </div>
                        <div>
                            <label class="form-label font-bold text-xs">{{ app()->getLocale() === 'ar' ? 'السبب الإلزامي للتسوية' : 'Mandatory cGMP Reason' }} <span class="text-rose-500">*</span></label>
                            <textarea name="reason" required rows="2" class="form-control text-xs" placeholder="e.g. Periodic cycle count variance verification"></textarea>
                        </div>
                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" @click="adjustOpen = false" class="btn btn-secondary btn-sm">{{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}</button>
                            <button type="submit" class="btn btn-primary btn-sm font-bold">{{ app()->getLocale() === 'ar' ? 'حفظ التسوية' : 'Apply Adjustment' }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </x-slot>

    <!-- Invariant Ribbon -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="card p-4 border border-emerald-100 dark:border-emerald-950 bg-emerald-50/30 dark:bg-emerald-950/20 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-emerald-700 dark:text-emerald-400 uppercase">{{ __('batches.available_qty') }}</p>
                <p class="text-2xl font-black text-emerald-800 dark:text-white mt-1 font-mono">
                    {{ number_format($batch->available_qty, 2) }}
                    <span class="text-xs font-normal text-emerald-600">{{ $batch->rawMaterial?->uom?->value }}</span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>

        <div class="card p-4 border border-purple-100 dark:border-purple-950 bg-purple-50/30 dark:bg-purple-950/20 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-purple-700 dark:text-purple-400 uppercase">{{ __('batches.quarantine_qty') }}</p>
                <p class="text-2xl font-black text-purple-800 dark:text-white mt-1 font-mono">
                    {{ number_format($batch->quarantine_qty, 2) }}
                    <span class="text-xs font-normal text-purple-600">{{ $batch->rawMaterial?->uom?->value }}</span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-purple-100 dark:bg-purple-900/40 text-purple-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-flask-vial"></i>
            </div>
        </div>

        <div class="card p-4 border border-blue-100 dark:border-blue-950 bg-blue-50/30 dark:bg-blue-950/20 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-blue-700 dark:text-blue-400 uppercase">{{ __('batches.reserved_qty') }}</p>
                <p class="text-2xl font-black text-blue-800 dark:text-white mt-1 font-mono">
                    {{ number_format($batch->reserved_qty, 2) }}
                    <span class="text-xs font-normal text-blue-600">{{ $batch->rawMaterial?->uom?->value }}</span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-900/40 text-blue-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-lock"></i>
            </div>
        </div>

        <div class="card p-4 border border-slate-100 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase">{{ __('batches.current_qty') }}</p>
                <p class="text-2xl font-black text-slate-800 dark:text-white mt-1 font-mono">
                    {{ number_format($batch->current_qty, 2) }}
                    <span class="text-xs font-normal text-slate-500">{{ $batch->rawMaterial?->uom?->value }}</span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-scale-balanced"></i>
            </div>
        </div>
    </div>

    <!-- Batch Genealogy & Metadata Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Lot Attributes -->
        <div class="card p-5 border border-gray-100 dark:border-gray-800 shadow-sm space-y-3">
            <h4 class="text-xs font-black uppercase text-gray-500 tracking-wider border-b border-gray-100 dark:border-gray-800 pb-2">
                {{ app()->getLocale() === 'ar' ? 'بيانات التشغيلة والتواريخ' : 'Batch Attributes & Dates' }}
            </h4>
            <div class="space-y-2 text-xs">
                <div class="flex justify-between">
                    <span class="text-gray-500">{{ __('batches.supplier_lot') }}:</span>
                    <span class="font-mono font-bold text-slate-800 dark:text-white">{{ $batch->supplier_lot ?? '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">{{ __('batches.location') }}:</span>
                    <span class="font-mono text-sky-600 font-bold">{{ $batch->location_id }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">{{ __('batches.expiry_date') }}:</span>
                    <span class="font-mono font-bold {{ $batch->expiry_date?->isPast() ? 'text-rose-600' : 'text-slate-800 dark:text-white' }}">
                        {{ $batch->expiry_date?->format('Y-m-d') }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">{{ __('batches.retest_date') }}:</span>
                    <span class="font-mono">{{ $batch->retest_date?->format('Y-m-d') ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">{{ __('batches.status') }}:</span>
                    <span class="badge {{ $batch->status->badgeClass() }} text-[11px]">{{ $batch->status->label() }}</span>
                </div>
            </div>
        </div>

        <!-- Supplier & Inbound Origin -->
        <div class="card p-5 border border-gray-100 dark:border-gray-800 shadow-sm space-y-3">
            <h4 class="text-xs font-black uppercase text-gray-500 tracking-wider border-b border-gray-100 dark:border-gray-800 pb-2">
                {{ app()->getLocale() === 'ar' ? 'سلسلة التوريد والشحنة الواردة' : 'Inbound Pedigree & GRN Origin' }}
            </h4>
            <div class="space-y-2 text-xs">
                <div class="flex justify-between">
                    <span class="text-gray-500">{{ __('suppliers.title') }}:</span>
                    <a href="{{ route('admin.suppliers.show', $batch->supplier) }}" class="font-bold text-sky-600 hover:underline">
                        {{ $batch->supplier?->name }}
                    </a>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">{{ __('goods_receipts.grn_number') }}:</span>
                    @if($batch->goodsReceiptItem?->goodsReceipt)
                        <a href="{{ route('admin.goods-receipts.show', $batch->goodsReceiptItem->goodsReceipt) }}" class="font-mono text-indigo-600 hover:underline">
                            {{ $batch->goodsReceiptItem->goodsReceipt->grn_number }}
                        </a>
                    @else
                        <span class="text-gray-400">—</span>
                    @endif
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">{{ __('purchase_orders.po_number') }}:</span>
                    @if($batch->goodsReceiptItem?->goodsReceipt?->purchaseOrder)
                        <a href="{{ route('admin.purchase-orders.show', $batch->goodsReceiptItem->goodsReceipt->purchaseOrder) }}" class="font-mono text-indigo-600 hover:underline">
                            {{ $batch->goodsReceiptItem->goodsReceipt->purchaseOrder->po_number }}
                        </a>
                    @else
                        <span class="text-gray-400">—</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- QC Verdict & COA Release -->
        <div class="card p-5 border border-gray-100 dark:border-gray-800 shadow-sm space-y-3">
            <h4 class="text-xs font-black uppercase text-gray-500 tracking-wider border-b border-gray-100 dark:border-gray-800 pb-2">
                {{ app()->getLocale() === 'ar' ? 'فحص الجودة واعتماد الإفراج QC' : 'QC Verdict & COA Sign-off' }}
            </h4>
            @if($batch->qcInspections->isNotEmpty())
                @php $qc = $batch->qcInspections->last(); @endphp
                <div class="space-y-2 text-xs">
                    <div class="flex justify-between">
                        <span class="text-gray-500">{{ app()->getLocale() === 'ar' ? 'القرار النهائي' : 'Verdict' }}:</span>
                        <span class="badge {{ $qc->verdict->badgeClass() }} text-[11px]">{{ $qc->verdict->label() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">{{ app()->getLocale() === 'ar' ? 'رقم الفحص' : 'Inspection #' }}:</span>
                        <a href="{{ route('admin.qc.show', $qc) }}" class="font-mono text-sky-600 hover:underline font-bold">{{ $qc->inspection_number }}</a>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">{{ app()->getLocale() === 'ar' ? 'الفاحص' : 'Inspector' }}:</span>
                        <span>{{ $qc->inspector?->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">{{ app()->getLocale() === 'ar' ? 'المعتمد (فصل المهام)' : 'Verifier (Dual)' }}:</span>
                        <span class="font-bold text-emerald-600">{{ $qc->verifier?->name ?? 'Pending' }}</span>
                    </div>
                </div>
            @else
                <div class="text-center py-4 text-gray-400 text-xs">
                    <i class="fa-solid fa-hourglass-half mb-1 block text-lg"></i>
                    {{ app()->getLocale() === 'ar' ? 'لم يتم إجراء فحص جودة لهذه التشغيلة بعد.' : 'No QC inspection filed for this lot yet.' }}
                </div>
            @endif
        </div>
    </div>

    <!-- Movement History Ledger (Append-Only) -->
    <div class="card p-5 border border-gray-100 dark:border-gray-800 shadow-sm">
        <h4 class="text-sm font-bold text-slate-800 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-3 mb-4 flex items-center justify-between">
            <span><i class="fa-solid fa-timeline text-sky-600 mr-2 ml-2"></i> {{ app()->getLocale() === 'ar' ? 'سجل حركات التشغيلة وتتبع المعاملات (غير قابل للتعديل)' : 'Immutable Batch Transaction Ledger' }}</span>
            <span class="badge badge-sky text-xs">{{ $batch->movements->count() }} {{ app()->getLocale() === 'ar' ? 'حركة' : 'Movements' }}</span>
        </h4>

        <div class="table-responsive">
            <table class="table text-xs">
                <thead>
                    <tr>
                        <th>{{ app()->getLocale() === 'ar' ? 'التاريخ والوقت' : 'Timestamp' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'نوع الحركة' : 'Movement Type' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'الكمية' : 'Quantity' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'الرصيد السابق' : 'Before' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'الرصيد اللاحق' : 'After' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'الموقع' : 'Location' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'المسؤول' : 'User' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'السبب / الملاحظة' : 'Reason / Reference' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($batch->movements->sortByDesc('id') as $mov)
                        <tr>
                            <td class="font-mono text-gray-500">{{ $mov->created_at?->format('Y-m-d H:i:s') }}</td>
                            <td>
                                <span class="badge badge-slate text-[11px] font-mono">
                                    {{ $mov->movement_type?->label() ?? $mov->movement_type->value }}
                                </span>
                            </td>
                            <td class="font-mono font-bold {{ $mov->quantity >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $mov->quantity >= 0 ? '+' : '' }}{{ number_format($mov->quantity, 4) }}
                            </td>
                            <td class="font-mono text-gray-500">{{ number_format($mov->balance_before, 4) }}</td>
                            <td class="font-mono font-bold text-slate-800 dark:text-white">{{ number_format($mov->balance_after, 4) }}</td>
                            <td class="font-mono">{{ $mov->to_location_id ?? $mov->from_location_id }}</td>
                            <td>{{ $mov->creator?->name ?? 'System' }}</td>
                            <td class="text-gray-500">{{ $mov->notes ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-gray-400">
                                {{ app()->getLocale() === 'ar' ? 'لا توجد حركات مسجلة.' : 'No movements on file.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
