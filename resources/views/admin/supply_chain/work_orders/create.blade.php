<x-layouts.admin 
    :pageTitle="__('production.new_work_order')" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'إطلاق أمر تشغيل تصنيعي جديد وحجز المواد الأولية بنظام FEFO حسب قائمة المكونات (BOM)' : 'Initiate Manufacturing Work Order & Auto-reserve Raw Material Batches (FEFO)'"
    :breadcrumbs="[__('production.title') => route('admin.work-orders.index'), __('production.new_work_order') => '#']"
>
    <div class="max-w-4xl mx-auto">
        <form method="POST" action="{{ route('admin.work-orders.store') }}" class="space-y-6">
            @csrf

            <div class="card p-6 border border-gray-100 dark:border-gray-800 shadow-sm space-y-4">
                <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2 border-b border-gray-100 dark:border-gray-800 pb-3">
                    <i class="fa-solid fa-industry text-indigo-600"></i>
                    {{ app()->getLocale() === 'ar' ? 'بيانات أمر الإنتاج والتركيبة' : 'Production Batch Specifications' }}
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label font-bold text-xs">{{ __('production.product') }} <span class="text-rose-500">*</span></label>
                        <select name="product_id" required class="form-select text-sm">
                            <option value="">{{ app()->getLocale() === 'ar' ? '-- اختر المنتج / التركيبة --' : '-- Select Formulation Product --' }}</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name_en }} ({{ $product->sku }}) - {{ $product->recipeItems->count() }} BOM items
                                </option>
                            @endforeach
                        </select>
                        @error('product_id') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="form-label font-bold text-xs">{{ __('production.batch_number') }} <span class="text-rose-500">*</span></label>
                        <input type="text" name="product_batch_number" value="{{ old('product_batch_number', 'BZ-LOT-' . date('Ymd') . '-01') }}" required class="form-control text-sm font-mono">
                    </div>

                    <div>
                        <label class="form-label font-bold text-xs">{{ __('production.target_quantity') }} ({{ app()->getLocale() === 'ar' ? 'الكمية المستهدفة' : 'Units' }}) <span class="text-rose-500">*</span></label>
                        <input type="number" name="target_quantity" value="{{ old('target_quantity', 1000) }}" min="1" required class="form-control text-sm font-mono font-bold">
                    </div>

                    <div>
                        <label class="form-label font-bold text-xs">{{ __('production.scheduled_date') }} <span class="text-rose-500">*</span></label>
                        <input type="date" name="scheduled_date" value="{{ old('scheduled_date', date('Y-m-d')) }}" required class="form-control text-sm">
                    </div>

                    <div>
                        <label class="form-label font-bold text-xs">{{ app()->getLocale() === 'ar' ? 'مستودع إيداع المنتج النهائي' : 'Finished Goods Location' }} <span class="text-rose-500">*</span></label>
                        <select name="location_id" required class="form-select text-sm font-mono">
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}">{{ $loc->name ?? $loc->id }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="form-label font-bold text-xs">{{ __('production.notes') }}</label>
                    <textarea name="notes" rows="2" class="form-control text-sm" placeholder="Cleanroom line assignment, blending duration specs...">{{ old('notes') }}</textarea>
                </div>
            </div>

            <!-- BOM Auto-Reservation Notice -->
            <div class="card p-5 border border-indigo-100 dark:border-indigo-950 bg-indigo-50/30 dark:bg-indigo-950/20 text-xs space-y-2">
                <h4 class="font-bold text-indigo-900 dark:text-indigo-300 flex items-center gap-2">
                    <i class="fa-solid fa-calculator text-indigo-600"></i>
                    {{ app()->getLocale() === 'ar' ? 'الحجز التلقائي للمواد الأولية حسب أسبقية الصلاحية (FEFO):' : 'Automatic FEFO Raw Material Reservation Protocol:' }}
                </h4>
                <p class="text-slate-600 dark:text-gray-400">
                    {{ app()->getLocale() === 'ar' ? 'عند إنشاء أمر التشغيل، سيقوم النظام تلقائياً باحتساب الاحتياجات من واقع قائمة مكونات المنتج (BOM) وحجز الكميات المطلوبة حصرياً من التشغيلات المعتمدة الأقرب انتهاءً، وتجميدها ضد أي استخدام آخر.' : 'Upon creation, the system multiplies formulation BOM dosages by target quantity and atomically locks required inventory from active approved lots using lockForUpdate.' }}
                </p>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.work-orders.index') }}" class="btn btn-secondary text-sm">
                    {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                </a>
                <button type="submit" class="btn btn-primary text-sm font-bold shadow-sm">
                    <i class="fa-solid fa-gears mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'إصدار أمر التشغيل وحجز المواد' : 'Create Work Order & Reserve Lots' }}
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>
