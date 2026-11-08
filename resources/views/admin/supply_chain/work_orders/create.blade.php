<x-layouts.admin 
    :pageTitle="__('production.new_work_order')" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'إطلاق أمر تشغيل تصنيعي جديد وحجز المواد الأولية بنظام FEFO حسب قائمة المكونات (BOM)' : 'Initiate Manufacturing Work Order & Auto-reserve Raw Material Batches (FEFO)'"
    :breadcrumbs="[__('production.title') => route('admin.work-orders.index'), __('production.new_work_order') => '#']"
>
    <x-slot name="actions">
        <a href="{{ route('admin.batches.index') }}" class="btn btn-secondary font-bold text-xs shadow-sm inline-flex items-center gap-1.5 px-3 py-2">
            <i class="fa-solid fa-boxes-stacked text-sky-500"></i>
            <span>{{ app()->getLocale() === 'ar' ? 'أرصدة التشغيلات FEFO' : 'FEFO Batches' }}</span>
        </a>
        <a href="{{ route('admin.work-orders.index') }}" class="btn btn-secondary font-bold text-xs shadow-sm inline-flex items-center gap-1.5 px-3 py-2">
            <i class="fa-solid fa-arrow-left rtl:rotate-180"></i>
            <span>{{ app()->getLocale() === 'ar' ? 'العودة لأوامر الإنتاج' : 'Back to Work Orders' }}</span>
        </a>
    </x-slot>
    <div class="max-w-5xl mx-auto" x-data="{
        selectedProduct: '{{ old('product_id', '') }}',
        targetQty: {{ old('planned_qty', old('target_quantity', 1000)) }},
        products: {{ Js::from($products->map(fn($p) => [
            'id' => $p->id,
            'name' => app()->getLocale() === 'ar' && !empty($p->name_ar) ? $p->name_ar : $p->name_en,
            'sku' => $p->sku,
            'recipes_count' => $p->recipe_items_count ?? $p->recipeItems->count(),
            'recipes' => $p->recipeItems->map(fn($ri) => [
                'raw_material_name' => app()->getLocale() === 'ar' && !empty($ri->rawMaterial?->name_ar) ? $ri->rawMaterial->name_ar : ($ri->rawMaterial?->name_en ?? $ri->rawMaterial?->name),
                'raw_material_sku' => $ri->rawMaterial?->code ?? $ri->rawMaterial?->sku,
                'qty_per_unit' => (float) $ri->quantity_per_unit,
                'uom' => $ri->rawMaterial?->unit_of_measure?->value ?? ($ri->rawMaterial?->uom?->value ?? 'units'),
                'is_critical' => (bool) $ri->is_critical,
                'loss_percentage' => (float) $ri->loss_percentage,
                'stock_available' => (float) ($ri->rawMaterial?->current_stock ?? $ri->rawMaterial?->approved_stock ?? 0),
            ]),
        ])) }},
        getCurrentProduct() {
            return this.products.find(p => p.id == this.selectedProduct) || null;
        }
    }">
        <form method="POST" action="{{ route('admin.work-orders.store') }}" class="space-y-6">
            @csrf

            <!-- Production Batch Specifications Card -->
            <div class="card p-6 border border-gray-100 dark:border-gray-800 shadow-sm space-y-5">
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3">
                    <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-industry text-indigo-600"></i>
                        {{ app()->getLocale() === 'ar' ? 'بيانات أمر الإنتاج والتشغيلة' : 'Production Batch Specifications' }}
                    </h3>
                    <span class="text-xs px-2.5 py-1 rounded-full bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 font-mono font-semibold">
                        cGMP Compounding Standard
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Target Product Select -->
                    <div>
                        <label class="form-label font-bold text-xs">
                            {{ __('production.target_product') }} <span class="text-rose-500">*</span>
                        </label>
                        <select 
                            name="product_id" 
                            x-model="selectedProduct" 
                            required 
                            class="form-select text-sm @error('product_id') border-rose-500 @enderror"
                        >
                            <option value="">{{ __('production.select_product') }}</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ app()->getLocale() === 'ar' && !empty($product->name_ar) ? $product->name_ar : $product->name_en }} 
                                    ({{ $product->sku }})
                                    @if(($product->recipe_items_count ?? $product->recipeItems->count()) > 0)
                                        — [{{ $product->recipe_items_count ?? $product->recipeItems->count() }} BOM items]
                                    @else
                                        — [{{ app()->getLocale() === 'ar' ? 'لا توجد تركيبة مسجلة' : 'No BOM defined' }}]
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('product_id') 
                            <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> 
                        @enderror
                    </div>

                    <!-- Destination Finished Lot -->
                    <div>
                        <label class="form-label font-bold text-xs">
                            {{ __('production.batch_number') }} <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="target_finished_lot" 
                            value="{{ old('target_finished_lot', old('product_batch_number', $suggestedLot)) }}" 
                            required 
                            class="form-control text-sm font-mono font-bold text-purple-700 dark:text-purple-400 @error('target_finished_lot') border-rose-500 @enderror"
                            placeholder="LOT-FG-YYYYMM-0001"
                        >
                        @error('target_finished_lot') 
                            <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> 
                        @enderror
                    </div>

                    <!-- Planned Target Quantity -->
                    <div>
                        <label class="form-label font-bold text-xs">
                            {{ __('production.target_quantity') }} ({{ app()->getLocale() === 'ar' ? 'العبوات التامة المخططة' : 'Planned Units' }}) <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <input 
                                type="number" 
                                name="planned_qty" 
                                x-model.number="targetQty" 
                                min="1" 
                                required 
                                class="form-control text-sm font-mono font-bold pr-12 pl-12 @error('planned_qty') border-rose-500 @enderror"
                            >
                            <span class="absolute inset-y-0 right-3 flex items-center text-xs text-gray-400 font-semibold pointer-events-none">
                                {{ app()->getLocale() === 'ar' ? 'عبوة' : 'Units' }}
                            </span>
                        </div>
                        @error('planned_qty') 
                            <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> 
                        @enderror
                    </div>

                    <!-- Scheduled Start Date -->
                    <div>
                        <label class="form-label font-bold text-xs">
                            {{ __('production.scheduled_date') }} <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="date" 
                            name="scheduled_start" 
                            value="{{ old('scheduled_start', old('scheduled_date', date('Y-m-d'))) }}" 
                            required 
                            class="form-control text-sm @error('scheduled_start') border-rose-500 @enderror"
                        >
                        @error('scheduled_start') 
                            <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> 
                        @enderror
                    </div>

                    <!-- Finished Goods Location -->
                    <div class="md:col-span-2">
                        <label class="form-label font-bold text-xs">
                            {{ __('production.finished_location') }} <span class="text-rose-500">*</span>
                        </label>
                        <select name="location_id" required class="form-select text-sm font-mono @error('location_id') border-rose-500 @enderror">
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" {{ old('location_id') == $loc->id ? 'selected' : '' }}>
                                    {{ app()->getLocale() === 'ar' && !empty($loc->name_ar) ? $loc->name_ar : ($loc->name_en ?? $loc->name ?? $loc->id) }}
                                    ({{ $loc->id }})
                                </option>
                            @endforeach
                        </select>
                        @error('location_id') 
                            <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> 
                        @enderror
                    </div>
                </div>

                <!-- Manufacturing Notes -->
                <div>
                    <label class="form-label font-bold text-xs">{{ __('production.notes') }}</label>
                    <textarea 
                        name="notes" 
                        rows="2" 
                        class="form-control text-sm" 
                        placeholder="{{ app()->getLocale() === 'ar' ? 'ملاحظات خط الإنتاج، خط التعبئة والتغليف، تعليمات غرفة الخلط...' : 'Cleanroom line assignment, blending duration specs, encapsulation machine...' }}"
                    >{{ old('notes') }}</textarea>
                    @error('notes') 
                        <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> 
                    @enderror
                </div>
            </div>

            <!-- Dynamic BOM Formulation Breakdown Card (Shown when product is selected) -->
            <div 
                x-show="getCurrentProduct() && getCurrentProduct().recipes && getCurrentProduct().recipes.length > 0" 
                class="card p-5 border border-emerald-100 dark:border-emerald-950 bg-emerald-50/20 dark:bg-emerald-950/10 shadow-sm space-y-3"
                style="display: none;"
            >
                <div class="flex items-center justify-between border-b border-emerald-100 dark:border-emerald-900 pb-2.5">
                    <h4 class="font-bold text-emerald-900 dark:text-emerald-300 text-sm flex items-center gap-2">
                        <i class="fa-solid fa-flask-vial text-emerald-600"></i>
                        {{ __('production.bom_preview') }}
                    </h4>
                    <span class="badge badge-emerald text-xs font-mono font-bold" x-text="(getCurrentProduct() ? getCurrentProduct().recipes.length : 0) + ' {{ app()->getLocale() === 'ar' ? 'مكونات نشطة' : 'Active Components' }}'"></span>
                </div>

                <div class="table-responsive">
                    <table class="table text-xs">
                        <thead>
                            <tr class="text-emerald-900 dark:text-emerald-300">
                                <th>{{ app()->getLocale() === 'ar' ? 'المادة الأولية' : 'Raw Material' }}</th>
                                <th>{{ app()->getLocale() === 'ar' ? 'الرمز الكودي' : 'Code' }}</th>
                                <th style="text-align: right;">{{ app()->getLocale() === 'ar' ? 'الجرعة / وحدة' : 'Dosage / Unit' }}</th>
                                <th style="text-align: right;">{{ app()->getLocale() === 'ar' ? 'نسبة الفاقد' : 'Scrap %' }}</th>
                                <th style="text-align: right;">{{ app()->getLocale() === 'ar' ? 'الكمية المطلوبة للدفعة' : 'Total Batch Needed' }}</th>
                                <th style="text-align: center;">{{ app()->getLocale() === 'ar' ? 'حالة التوفر' : 'Stock Availability' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(r, idx) in (getCurrentProduct() ? getCurrentProduct().recipes : [])" :key="idx">
                                <tr>
                                    <td class="font-bold text-slate-800 dark:text-white" x-text="r.raw_material_name"></td>
                                    <td class="font-mono text-gray-500" x-text="r.raw_material_sku"></td>
                                    <td class="font-mono" style="text-align: right;" x-text="r.qty_per_unit.toFixed(6) + ' ' + r.uom"></td>
                                    <td class="font-mono text-gray-500" style="text-align: right;" x-text="r.loss_percentage + '%'"></td>
                                    <td class="font-mono font-bold text-indigo-600" style="text-align: right;" x-text="((r.qty_per_unit * targetQty) * (1 + (r.loss_percentage / 100))).toFixed(4) + ' ' + r.uom"></td>
                                    <td style="text-align: center;">
                                        <span 
                                            class="badge text-[11px]" 
                                            :class="r.stock_available >= ((r.qty_per_unit * targetQty) * (1 + (r.loss_percentage / 100))) ? 'badge-emerald' : 'badge-danger'"
                                            x-text="r.stock_available >= ((r.qty_per_unit * targetQty) * (1 + (r.loss_percentage / 100))) ? '{{ app()->getLocale() === 'ar' ? 'متوفر' : 'In Stock' }}' : '{{ app()->getLocale() === 'ar' ? 'عجز محتمل' : 'Shortage' }}'"
                                        ></span>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Warning if product has NO recipe items -->
            <div 
                x-show="getCurrentProduct() && (!getCurrentProduct().recipes || getCurrentProduct().recipes.length === 0)" 
                class="card p-4 border border-amber-200 dark:border-amber-900/50 bg-amber-50/50 dark:bg-amber-950/20 text-xs text-amber-800 dark:text-amber-300 space-y-1.5"
                style="display: none;"
            >
                <div class="font-bold flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation text-amber-500"></i>
                    {{ __('production.errors.no_recipe') }}
                </div>
                <p class="text-slate-600 dark:text-gray-400">
                    {{ app()->getLocale() === 'ar' ? 'يمكنك إنشاء أمر التشغيل كمسودة، لكن لن يتم حجز أي مواد تلقائياً حتى يتم إدخال تركيبة المنتج (BOM).' : 'You can still draft this work order, but no raw materials can be auto-reserved until a formulation recipe (BOM) is linked to this product.' }}
                </p>
            </div>

            <!-- BOM Auto-Reservation Notice & Toggle -->
            <div class="card p-5 border border-indigo-100 dark:border-indigo-950 bg-indigo-50/30 dark:bg-indigo-950/20 text-xs space-y-3">
                <div class="flex items-start gap-3">
                    <input 
                        type="checkbox" 
                        name="auto_reserve" 
                        id="autoReserveCheckbox" 
                        value="1" 
                        checked 
                        class="rounded border-indigo-300 text-indigo-600 focus:ring-indigo-500 mt-0.5"
                    >
                    <label for="autoReserveCheckbox" class="font-bold text-indigo-950 dark:text-indigo-200 cursor-pointer">
                        {{ app()->getLocale() === 'ar' ? 'تفعيل الحجز التلقائي للمواد الأولية حسب أسبقية الصلاحية (FEFO):' : 'Automatic FEFO Raw Material Reservation Protocol' }}
                    </label>
                </div>
                <p class="text-slate-600 dark:text-gray-400 ml-6 mr-6">
                    {{ app()->getLocale() === 'ar' ? 'عند اعتماد هذا الخيار، سيقوم النظام تلقائياً باحتساب الاحتياجات من واقع قائمة مكونات المنتج (BOM) وحجز الكميات المطلوبة حصرياً من التشغيلات المعتمدة الأقرب انتهاءً وتجميدها ضد أي استخدام آخر.' : 'Upon creation, the system multiplies formulation BOM dosages by target quantity and atomically locks required inventory from active approved lots using lockForUpdate.' }}
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('admin.work-orders.index') }}" class="btn btn-secondary text-sm">
                    {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                </a>
                <button type="submit" class="btn btn-primary text-sm font-bold shadow-sm">
                    <i class="fa-solid fa-gears mr-1.5 ml-1.5"></i> {{ __('production.create_order') }}
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>
