<x-layouts.admin 
    :pageTitle="$rawMaterial->name" 
    :pageSubtitle="__('raw_materials.sku') . ': ' . $rawMaterial->sku . ' | ' . ($rawMaterial->category?->label() ?? 'Raw Material')"
    :breadcrumbs="[__('raw_materials.title') => route('admin.raw-materials.index'), $rawMaterial->name => '#']"
>
    <x-slot name="actions">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.raw-materials.edit', $rawMaterial) }}" class="btn btn-secondary font-bold text-sm">
                <i class="fa-solid fa-pen-to-square mr-1 ml-1"></i> {{ __('raw_materials.edit_material') }}
            </a>
            <a href="{{ route('admin.purchase-orders.create', ['material_id' => $rawMaterial->id]) }}" class="btn btn-primary font-bold text-sm">
                <i class="fa-solid fa-cart-plus mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'طلب شراء جديد' : 'Draft PO' }}
            </a>
        </div>
    </x-slot>

    <!-- Stock KPI Ribbon (Invariant Enforced) -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="card p-4 border border-emerald-100 dark:border-emerald-950 bg-emerald-50/30 dark:bg-emerald-950/20 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-emerald-700 dark:text-emerald-400 uppercase">{{ app()->getLocale() === 'ar' ? 'متاح للصرف (معتمد FEFO)' : 'Available for Dispense' }}</p>
                <p class="text-2xl font-black text-emerald-800 dark:text-white mt-1 font-mono">
                    {{ number_format($rawMaterial->batches->where('status', \App\Enums\RawMaterialBatchStatus::Approved)->sum('available_qty'), 2) }}
                    <span class="text-xs font-normal text-emerald-600">{{ $rawMaterial->uom?->value }}</span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>

        <div class="card p-4 border border-purple-100 dark:border-purple-950 bg-purple-50/30 dark:bg-purple-950/20 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-purple-700 dark:text-purple-400 uppercase">{{ app()->getLocale() === 'ar' ? 'محجور للاختبار QC' : 'In Quarantine Lock' }}</p>
                <p class="text-2xl font-black text-purple-800 dark:text-white mt-1 font-mono">
                    {{ number_format($rawMaterial->batches->where('status', \App\Enums\RawMaterialBatchStatus::Quarantine)->sum('quarantine_qty'), 2) }}
                    <span class="text-xs font-normal text-purple-600">{{ $rawMaterial->uom?->value }}</span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-purple-100 dark:bg-purple-900/40 text-purple-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-flask-vial"></i>
            </div>
        </div>

        <div class="card p-4 border border-blue-100 dark:border-blue-950 bg-blue-50/30 dark:bg-blue-950/20 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-blue-700 dark:text-blue-400 uppercase">{{ app()->getLocale() === 'ar' ? 'محجوز لأوامر تشغيل' : 'Reserved for Production' }}</p>
                <p class="text-2xl font-black text-blue-800 dark:text-white mt-1 font-mono">
                    {{ number_format($rawMaterial->batches->sum('reserved_qty'), 2) }}
                    <span class="text-xs font-normal text-blue-600">{{ $rawMaterial->uom?->value }}</span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-900/40 text-blue-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-lock"></i>
            </div>
        </div>

        <div class="card p-4 border border-slate-100 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-500 uppercase">{{ app()->getLocale() === 'ar' ? 'إجمالي المخزون الفعلي' : 'Physical On-Hand' }}</p>
                <p class="text-2xl font-black text-slate-800 dark:text-white mt-1 font-mono">
                    {{ number_format($rawMaterial->batches->sum('current_qty'), 2) }}
                    <span class="text-xs font-normal text-slate-500">{{ $rawMaterial->uom?->value }}</span>
                </p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-scale-balanced"></i>
            </div>
        </div>
    </div>

    <!-- Details Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Technical Profile -->
        <div class="card p-5 border border-gray-100 dark:border-gray-800 shadow-sm space-y-3">
            <h4 class="text-xs font-black uppercase text-gray-500 tracking-wider border-b border-gray-100 dark:border-gray-800 pb-2">
                {{ app()->getLocale() === 'ar' ? 'المواصفات الكيميائية والتعريفية' : 'Chemical Identity & Safety' }}
            </h4>
            <div class="space-y-2 text-xs">
                <div class="flex justify-between">
                    <span class="text-gray-500">INCI:</span>
                    <span class="font-mono font-bold text-slate-800 dark:text-white">{{ $rawMaterial->inci_name ?? '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">CAS Number:</span>
                    <span class="font-mono text-sky-600 font-bold">{{ $rawMaterial->cas_number ?? '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">{{ __('raw_materials.storage_condition') }}:</span>
                    <span class="badge {{ $rawMaterial->storage_condition?->badgeClass() }} text-[11px]">
                        {{ $rawMaterial->storage_condition?->label() }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">{{ __('raw_materials.standard_cost') }}:</span>
                    <span class="font-mono font-bold text-slate-800 dark:text-white">{{ number_format($rawMaterial->standard_cost, 2) }} AED / {{ $rawMaterial->uom?->value }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">{{ __('raw_materials.reorder_level') }}:</span>
                    <span class="font-mono font-bold text-amber-600">{{ number_format($rawMaterial->reorder_level, 2) }} {{ $rawMaterial->uom?->value }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">{{ __('raw_materials.safety_stock') }}:</span>
                    <span class="font-mono font-bold text-rose-600">{{ number_format($rawMaterial->safety_stock, 2) }} {{ $rawMaterial->uom?->value }}</span>
                </div>
            </div>
        </div>

        <!-- BOM Usage in Formulations -->
        <div class="card p-5 border border-gray-100 dark:border-gray-800 shadow-sm space-y-3">
            <h4 class="text-xs font-black uppercase text-gray-500 tracking-wider border-b border-gray-100 dark:border-gray-800 pb-2">
                {{ app()->getLocale() === 'ar' ? 'استخدام المادة في وصفات المنتجات (BOM)' : 'Recipe & Formulation Usage (BOM)' }}
            </h4>
            <div class="space-y-2">
                @forelse($rawMaterial->recipeItems as $recipe)
                    <div class="p-2.5 rounded-lg bg-gray-50 dark:bg-slate-800/50 flex items-center justify-between text-xs">
                        <div>
                            <p class="font-bold text-slate-800 dark:text-white">{{ $recipe->product?->name_en }}</p>
                            <span class="text-[11px] text-gray-500">{{ $recipe->product?->sku }}</span>
                        </div>
                        <div class="text-end">
                            <span class="font-mono font-bold text-emerald-600">{{ number_format($recipe->quantity, 4) }}</span>
                            <span class="text-[10px] text-gray-500">{{ $rawMaterial->uom?->value }} / unit</span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-gray-400 py-3 text-center">
                        {{ app()->getLocale() === 'ar' ? 'لا توجد منتجات تستخدم هذه المادة في تركيباتها حالياً.' : 'No active formulations linked to this material.' }}
                    </p>
                @endforelse
            </div>
        </div>

        <!-- Approved Suppliers & Price Master -->
        <div class="card p-5 border border-gray-100 dark:border-gray-800 shadow-sm space-y-3">
            <h4 class="text-xs font-black uppercase text-gray-500 tracking-wider border-b border-gray-100 dark:border-gray-800 pb-2">
                {{ app()->getLocale() === 'ar' ? 'قائمة أسعار الموردين المعتمدين' : 'Supplier Price Master' }}
            </h4>
            <div class="space-y-2">
                @forelse($rawMaterial->supplierPrices as $price)
                    <div class="p-2.5 rounded-lg bg-gray-50 dark:bg-slate-800/50 flex items-center justify-between text-xs">
                        <div>
                            <a href="{{ route('admin.suppliers.show', $price->supplier) }}" class="font-bold text-sky-600 hover:underline">
                                {{ $price->supplier?->name }}
                            </a>
                            <span class="block text-[10px] text-gray-400">{{ $price->supplier_part_number ?? 'Catalog SKU' }}</span>
                        </div>
                        <div class="text-end">
                            <span class="font-mono font-black text-slate-800 dark:text-white">{{ number_format($price->unit_price, 2) }} {{ $price->currency }}</span>
                            <span class="block text-[10px] text-gray-500">MOQ: {{ number_format($price->min_order_qty, 0) }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-gray-400 py-3 text-center">
                        {{ app()->getLocale() === 'ar' ? 'لم يتم تسجيل أسعار موردين لهذه المادة بعد.' : 'No price quotes filed for this material.' }}
                    </p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Active Batches Ledger (FEFO Queue) -->
    <div class="card p-5 border border-gray-100 dark:border-gray-800 shadow-sm">
        <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3 mb-4">
            <div>
                <h4 class="text-sm font-bold text-slate-800 dark:text-white">
                    <i class="fa-solid fa-boxes-stacked text-sky-600 mr-2 ml-2"></i>
                    {{ app()->getLocale() === 'ar' ? 'سجل تشغيلات المادة الأولية وتاريخ الصلاحية (FEFO)' : 'Raw Material Batches & Expiry Queue (FEFO Ledger)' }}
                </h4>
                <p class="text-xs text-gray-500">
                    {{ app()->getLocale() === 'ar' ? 'ترتيب الصرف التلقائي يتم دائماً بالأقرب انتهاءً (FEFO) بين التشغيلات المعتمدة فقط.' : 'Dispensing strictly prioritizes nearest expiry date among approved lots.' }}
                </p>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table text-xs">
                <thead>
                    <tr>
                        <th>{{ __('batches.batch_number') }}</th>
                        <th>{{ __('batches.supplier_lot') }}</th>
                        <th>{{ __('batches.location') }}</th>
                        <th>{{ __('batches.current_qty') }}</th>
                        <th>{{ __('batches.available_qty') }}</th>
                        <th>{{ __('batches.reserved_qty') }}</th>
                        <th>{{ __('batches.quarantine_qty') }}</th>
                        <th>{{ __('batches.expiry_date') }}</th>
                        <th>{{ __('batches.status') }}</th>
                        <th style="text-align: center;">{{ app()->getLocale() === 'ar' ? 'عرض' : 'Action' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rawMaterial->batches->sortBy('expiry_date') as $batch)
                        <tr>
                            <td class="font-mono font-bold text-sky-600">
                                <a href="{{ route('admin.batches.show', $batch) }}" class="hover:underline">
                                    {{ $batch->batch_number }}
                                </a>
                            </td>
                            <td class="font-mono text-gray-500">{{ $batch->supplier_lot ?? '—' }}</td>
                            <td class="font-mono">{{ $batch->location_id }}</td>
                            <td class="font-mono font-bold">{{ number_format($batch->current_qty, 2) }}</td>
                            <td class="font-mono font-bold text-emerald-600">{{ number_format($batch->available_qty, 2) }}</td>
                            <td class="font-mono text-blue-600">{{ number_format($batch->reserved_qty, 2) }}</td>
                            <td class="font-mono text-purple-600">{{ number_format($batch->quarantine_qty, 2) }}</td>
                            <td class="font-mono">
                                @php
                                    $days = now()->diffInDays($batch->expiry_date, false);
                                @endphp
                                <span class="{{ $days <= 30 ? 'text-rose-600 font-bold' : ($days <= 90 ? 'text-amber-500 font-bold' : 'text-slate-700 dark:text-gray-300') }}">
                                    {{ $batch->expiry_date?->format('Y-m-d') }}
                                    @if($days <= 0)
                                        <span class="text-rose-600">({{ app()->getLocale() === 'ar' ? 'منتهي' : 'Expired' }})</span>
                                    @elseif($days <= 90)
                                        <span class="text-amber-500">({{ $days }}d)</span>
                                    @endif
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $batch->status->badgeClass() }} text-[11px]">
                                    {{ $batch->status->label() }}
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <a href="{{ route('admin.batches.show', $batch) }}" class="btn btn-ghost btn-xs text-sky-600">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-6 text-gray-400">
                                {{ app()->getLocale() === 'ar' ? 'لا توجد تشغيلات مسجلة لهذه المادة بعد.' : 'No active batches recorded in inventory.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
