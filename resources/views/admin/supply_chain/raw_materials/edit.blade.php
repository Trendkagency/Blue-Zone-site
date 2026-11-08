<x-layouts.admin 
    :pageTitle="__('raw_materials.edit_material') . ': ' . $rawMaterial->name" 
    :pageSubtitle="__('raw_materials.code') . ': ' . $rawMaterial->code" 
    :breadcrumbs="[__('raw_materials.title') => route('admin.raw-materials.index'), $rawMaterial->name => route('admin.raw-materials.show', $rawMaterial), __('raw_materials.edit_material') => '#']"
>
    <div class="max-w-4xl mx-auto pb-12">
        <form method="POST" action="{{ route('admin.raw-materials.update', $rawMaterial) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Section 1: Chemical & Identification Details -->
            <div class="card p-6 border border-gray-100 dark:border-gray-800 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3">
                    <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-flask text-sky-600"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'المعلومات التعريفية والكيميائية' : 'Identity & Chemical Specifications' }}</span>
                    </h3>
                    <span class="text-xs font-mono font-bold px-2.5 py-0.5 rounded-full {{ $rawMaterial->is_active ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-slate-100 text-slate-700' }}">
                        {{ $rawMaterial->is_active ? __('raw_materials.active') : __('raw_materials.inactive') }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Material Code -->
                    <div>
                        <label class="form-label font-bold text-xs">{{ __('raw_materials.code') }} <span class="text-rose-500">*</span></label>
                        <input type="text" 
                               name="code" 
                               value="{{ old('code', old('sku', $rawMaterial->code)) }}" 
                               required 
                               class="form-control text-sm font-mono tracking-wider @error('code') border-rose-500 @enderror">
                        @error('code') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="form-label font-bold text-xs">{{ __('raw_materials.category') }} <span class="text-rose-500">*</span></label>
                        <select name="category" required class="form-select text-sm @error('category') border-rose-500 @enderror">
                            @foreach($categories as $category)
                                <option value="{{ $category->value }}" {{ old('category', $rawMaterial->category?->value) === $category->value ? 'selected' : '' }}>
                                    {{ $category->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('category') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Name (English) -->
                    <div>
                        <label class="form-label font-bold text-xs">{{ __('raw_materials.name_en') }} <span class="text-rose-500">*</span></label>
                        <input type="text" 
                               name="name_en" 
                               value="{{ old('name_en', $rawMaterial->name_en) }}" 
                               required 
                               class="form-control text-sm @error('name_en') border-rose-500 @enderror">
                        @error('name_en') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Name (Arabic) -->
                    <div>
                        <label class="form-label font-bold text-xs">{{ __('raw_materials.name_ar') }}</label>
                        <input type="text" 
                               name="name_ar" 
                               value="{{ old('name_ar', $rawMaterial->name_ar) }}" 
                               class="form-control text-sm font-tajawal @error('name_ar') border-rose-500 @enderror">
                        @error('name_ar') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- INCI Name -->
                    <div>
                        <label class="form-label font-bold text-xs">{{ __('raw_materials.inci_name') }}</label>
                        <input type="text" 
                               name="inci_name" 
                               value="{{ old('inci_name', $rawMaterial->inci_name) }}" 
                               class="form-control text-sm font-mono" 
                               placeholder="INCI Standard Chemical Name">
                    </div>

                    <!-- CAS Number -->
                    <div>
                        <label class="form-label font-bold text-xs">{{ __('raw_materials.cas_number') }}</label>
                        <input type="text" 
                               name="cas_number" 
                               value="{{ old('cas_number', $rawMaterial->cas_number) }}" 
                               class="form-control text-sm font-mono" 
                               placeholder="e.g. 1094-61-7">
                    </div>

                    <!-- Unit of Measure (UOM) -->
                    <div>
                        <label class="form-label font-bold text-xs">{{ __('raw_materials.unit_of_measure') }} <span class="text-rose-500">*</span></label>
                        <select name="unit_of_measure" required class="form-select text-sm font-bold @error('unit_of_measure') border-rose-500 @enderror">
                            @foreach($units as $uom)
                                <option value="{{ $uom->value }}" {{ old('unit_of_measure', old('uom', $rawMaterial->unit_of_measure?->value ?? $rawMaterial->uom?->value)) === $uom->value ? 'selected' : '' }}>
                                    {{ $uom->label() }} ({{ $uom->value }})
                                </option>
                            @endforeach
                        </select>
                        @error('unit_of_measure') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Storage Condition -->
                    <div>
                        <label class="form-label font-bold text-xs">{{ __('raw_materials.storage_condition') }} <span class="text-rose-500">*</span></label>
                        <select name="storage_condition" required class="form-select text-sm @error('storage_condition') border-rose-500 @enderror">
                            @foreach($storageConditions as $sc)
                                <option value="{{ $sc->value }}" {{ old('storage_condition', $rawMaterial->storage_condition?->value) === $sc->value ? 'selected' : '' }}>
                                    {{ $sc->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('storage_condition') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Section 2: Inventory Planning, Safety Stock & Sourcing -->
            <div class="card p-6 border border-gray-100 dark:border-gray-800 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3">
                    <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-boxes-packing text-emerald-600"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'التخطيط وإدارة المخزون والتوريد' : 'Inventory Planning & Sourcing' }}</span>
                    </h3>
                    <span class="text-xs text-gray-500">
                        {{ app()->getLocale() === 'ar' ? 'حسابات إعادة الطلب وسياسة FEFO' : 'MRP & FEFO Parameters' }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Min Reorder Level (ROP) -->
                    <div>
                        <label class="form-label font-bold text-xs">{{ __('raw_materials.min_reorder_qty') }} <span class="text-rose-500">*</span></label>
                        <input type="number" 
                               step="0.0001" 
                               name="min_reorder_qty" 
                               value="{{ old('min_reorder_qty', old('reorder_level', $rawMaterial->min_reorder_qty)) }}" 
                               required 
                               class="form-control text-sm font-mono @error('min_reorder_qty') border-rose-500 @enderror">
                        @error('min_reorder_qty') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Safety Stock Buffer -->
                    <div>
                        <label class="form-label font-bold text-xs">{{ __('raw_materials.safety_stock') }} <span class="text-rose-500">*</span></label>
                        <input type="number" 
                               step="0.0001" 
                               name="safety_stock" 
                               value="{{ old('safety_stock', $rawMaterial->safety_stock) }}" 
                               required 
                               class="form-control text-sm font-mono @error('safety_stock') border-rose-500 @enderror">
                        @error('safety_stock') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Standard Cost -->
                    <div>
                        <label class="form-label font-bold text-xs">{{ __('raw_materials.standard_cost') }}</label>
                        <input type="number" 
                               step="0.01" 
                               name="standard_cost" 
                               value="{{ old('standard_cost', $rawMaterial->standard_cost) }}" 
                               class="form-control text-sm font-mono @error('standard_cost') border-rose-500 @enderror">
                        @error('standard_cost') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Primary Sourcing Partner -->
                    <div class="md:col-span-2">
                        <label class="form-label font-bold text-xs">{{ __('raw_materials.default_supplier') }}</label>
                        <select name="default_supplier_id" class="form-select text-sm">
                            <option value="">{{ __('raw_materials.select_supplier') }}</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('default_supplier_id', $rawMaterial->default_supplier_id) == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }} ({{ $supplier->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Shelf Life (Days) -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="form-label font-bold text-xs">{{ __('raw_materials.shelf_life_days') }} <span class="text-rose-500">*</span></label>
                            <span class="text-[10px] text-gray-400 font-mono" id="shelfLifeMonthsHint">
                                ({{ round(($rawMaterial->shelf_life_days ?? 730) / 30) }} {{ app()->getLocale() === 'ar' ? 'شهراً' : 'months' }})
                            </span>
                        </div>
                        <input type="number" 
                               name="shelf_life_days" 
                               id="shelfLifeDaysInput"
                               value="{{ old('shelf_life_days', $rawMaterial->shelf_life_days ?? 730) }}" 
                               min="30" 
                               max="3650"
                               required 
                               class="form-control text-sm font-mono @error('shelf_life_days') border-rose-500 @enderror"
                               oninput="updateShelfLifeHint(this.value)">
                        @error('shelf_life_days') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- COA Requirement Checkbox -->
                <div class="pt-2">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" 
                               name="requires_coa" 
                               value="1" 
                               {{ old('requires_coa', $rawMaterial->requires_coa) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-sky-600 shadow-sm focus:ring-sky-500">
                        <span class="text-xs font-bold text-slate-800 dark:text-slate-200">
                            {{ __('raw_materials.requires_coa') }}
                        </span>
                        <span class="text-[11px] text-gray-400">
                            ({{ app()->getLocale() === 'ar' ? 'يمنع صرف أي تشغيلة دون فحص مخبري ومطابقة شهادة التحليل' : 'Enforces QC quarantine lock until COA is verified' }})
                        </span>
                    </label>
                </div>

                <!-- Notes & Special Storage Handling -->
                <div>
                    <label class="form-label font-bold text-xs">{{ __('raw_materials.notes') }}</label>
                    <textarea name="notes" 
                              rows="3" 
                              class="form-control text-sm" 
                              placeholder="{{ app()->getLocale() === 'ar' ? 'تعليمات التخزين، الحساسية للضوء أو الرطوبة، اشتراطات الغاز الخامل أو متطلبات cGMP...' : 'Handling precautions, light sensitivity, hygroscopic notes, inert nitrogen flush requirements...' }}">{{ old('notes', $rawMaterial->notes) }}</textarea>
                </div>
            </div>

            <!-- Submit Button Bar -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('admin.raw-materials.show', $rawMaterial) }}" class="btn btn-secondary text-sm">
                    {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                </a>
                <button type="submit" class="btn btn-primary text-sm font-bold shadow-sm px-5">
                    <i class="fa-solid fa-save mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'حفظ التعديلات' : 'Update Raw Material' }}
                </button>
            </div>
        </form>
    </div>

    <script>
        function updateShelfLifeHint(days) {
            const num = parseInt(days) || 0;
            const months = Math.round(num / 30);
            const isAr = "{{ app()->getLocale() }}" === 'ar';
            const hint = document.getElementById('shelfLifeMonthsHint');
            if (hint) {
                hint.textContent = `(${months} ${isAr ? 'شهراً' : 'months'})`;
            }
        }
    </script>
</x-layouts.admin>
