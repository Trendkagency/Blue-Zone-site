<x-layouts.admin 
    :pageTitle="__('raw_materials.new_material')" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'تعريف مادة أولية جديدة، ومواصفات التخزين، والمورد الافتراضي وحدود إعادة الطلب' : 'Register a new clinical raw material with chemical specifications, storage norms and safety stock'"
    :breadcrumbs="[__('raw_materials.title') => route('admin.raw-materials.index'), __('raw_materials.new_material') => '#']"
>
    <div class="max-w-4xl mx-auto">
        <form method="POST" action="{{ route('admin.raw-materials.store') }}" class="space-y-6">
            @csrf

            <div class="card p-6 border border-gray-100 dark:border-gray-800 shadow-sm space-y-4">
                <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2 border-b border-gray-100 dark:border-gray-800 pb-3">
                    <i class="fa-solid fa-flask text-sky-600"></i>
                    {{ app()->getLocale() === 'ar' ? 'المعلومات التعريفية والكيميائية' : 'Identity & Chemical Specifications' }}
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label font-bold text-xs">{{ __('raw_materials.sku') }} <span class="text-rose-500">*</span></label>
                        <input type="text" name="sku" value="{{ old('sku') }}" required class="form-control text-sm font-mono" placeholder="e.g. RM-NMN-001">
                        @error('sku') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="form-label font-bold text-xs">{{ __('raw_materials.category') }} <span class="text-rose-500">*</span></label>
                        <select name="category" required class="form-select text-sm">
                            @foreach(\App\Enums\RawMaterialCategory::cases() as $category)
                                <option value="{{ $category->value }}" {{ old('category') === $category->value ? 'selected' : '' }}>
                                    {{ $category->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('category') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="form-label font-bold text-xs">{{ __('raw_materials.name_en') }} <span class="text-rose-500">*</span></label>
                        <input type="text" name="name_en" value="{{ old('name_en') }}" required class="form-control text-sm" placeholder="e.g. Nicotinamide Mononucleotide (NMN)">
                        @error('name_en') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="form-label font-bold text-xs">{{ __('raw_materials.name_ar') }}</label>
                        <input type="text" name="name_ar" value="{{ old('name_ar') }}" class="form-control text-sm" placeholder="مثال: نيوكليوتيد أحادي النيكوتيناميد (NMN)">
                        @error('name_ar') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="form-label font-bold text-xs">{{ __('raw_materials.inci_name') }}</label>
                        <input type="text" name="inci_name" value="{{ old('inci_name') }}" class="form-control text-sm font-mono" placeholder="INCI Standard Name">
                    </div>

                    <div>
                        <label class="form-label font-bold text-xs">{{ __('raw_materials.cas_number') }}</label>
                        <input type="text" name="cas_number" value="{{ old('cas_number') }}" class="form-control text-sm font-mono" placeholder="e.g. 1094-61-7">
                    </div>

                    <div>
                        <label class="form-label font-bold text-xs">{{ __('raw_materials.uom') }} <span class="text-rose-500">*</span></label>
                        <select name="uom" required class="form-select text-sm">
                            @foreach(\App\Enums\UnitOfMeasure::cases() as $uom)
                                <option value="{{ $uom->value }}" {{ old('uom') === $uom->value ? 'selected' : '' }}>
                                    {{ $uom->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('uom') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="form-label font-bold text-xs">{{ __('raw_materials.storage_condition') }} <span class="text-rose-500">*</span></label>
                        <select name="storage_condition" required class="form-select text-sm">
                            @foreach(\App\Enums\StorageCondition::cases() as $sc)
                                <option value="{{ $sc->value }}" {{ old('storage_condition') === $sc->value ? 'selected' : '' }}>
                                    {{ $sc->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('storage_condition') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Inventory Planning & Sourcing -->
            <div class="card p-6 border border-gray-100 dark:border-gray-800 shadow-sm space-y-4">
                <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2 border-b border-gray-100 dark:border-gray-800 pb-3">
                    <i class="fa-solid fa-boxes-packing text-emerald-600"></i>
                    {{ app()->getLocale() === 'ar' ? 'التخطيط وإدارة المخزون والتوريد' : 'Inventory Planning & Sourcing' }}
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="form-label font-bold text-xs">{{ __('raw_materials.reorder_level') }} <span class="text-rose-500">*</span></label>
                        <input type="number" step="0.0001" name="reorder_level" value="{{ old('reorder_level', 10.0000) }}" required class="form-control text-sm font-mono">
                    </div>

                    <div>
                        <label class="form-label font-bold text-xs">{{ __('raw_materials.safety_stock') }} <span class="text-rose-500">*</span></label>
                        <input type="number" step="0.0001" name="safety_stock" value="{{ old('safety_stock', 5.0000) }}" required class="form-control text-sm font-mono">
                    </div>

                    <div>
                        <label class="form-label font-bold text-xs">{{ __('raw_materials.standard_cost') }} (AED)</label>
                        <input type="number" step="0.01" name="standard_cost" value="{{ old('standard_cost', 0.00) }}" class="form-control text-sm font-mono">
                    </div>

                    <div class="md:col-span-2">
                        <label class="form-label font-bold text-xs">{{ __('raw_materials.default_supplier') }}</label>
                        <select name="default_supplier_id" class="form-select text-sm">
                            <option value="">{{ app()->getLocale() === 'ar' ? 'بدون مورد افتراضي' : 'None (Select when creating PO)' }}</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('default_supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }} ({{ $supplier->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label font-bold text-xs">{{ app()->getLocale() === 'ar' ? 'فترة الصلاحية الافتراضية (بالأشهر)' : 'Shelf Life (Months)' }}</label>
                        <input type="number" name="shelf_life_months" value="{{ old('shelf_life_months', 24) }}" class="form-control text-sm font-mono">
                    </div>
                </div>

                <div>
                    <label class="form-label font-bold text-xs">{{ __('raw_materials.notes') }}</label>
                    <textarea name="notes" rows="2" class="form-control text-sm" placeholder="Handling precautions, light sensitivity, hygroscopic notes...">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.raw-materials.index') }}" class="btn btn-secondary text-sm">
                    {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                </a>
                <button type="submit" class="btn btn-primary text-sm font-bold shadow-sm">
                    <i class="fa-solid fa-save mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'حفظ المادة الأولية' : 'Save Material' }}
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>
