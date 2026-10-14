<x-layouts.admin 
    :pageTitle="__('raw_materials.title')" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'كتالوج المواد الخام الدوائية والكيميائية ومواد التعبئة وتتبع شروط التخزين ونقاط إعادة الطلب' : 'Clinical Active Ingredients, Excipients, Packaging Materials & Safety Stock Matrix'"
    :breadcrumbs="[__('raw_materials.title') => route('admin.raw-materials.index')]"
>
    <x-slot name="actions">
        <a href="{{ route('admin.raw-materials.create') }}" class="btn btn-primary font-bold shadow-sm">
            <i class="fa-solid fa-plus mr-1.5 ml-1.5"></i> {{ __('raw_materials.new_material') }}
        </a>
    </x-slot>

    <!-- Top KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">{{ app()->getLocale() === 'ar' ? 'إجمالي المواد' : 'Total Catalog SKUs' }}</p>
                <p class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ $kpis['total_skus'] ?? $materials->total() }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-sky-50 dark:bg-sky-900/30 text-sky-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-flask-vial"></i>
            </div>
        </div>
        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">{{ app()->getLocale() === 'ar' ? 'مواد فعالة API' : 'Active Ingredients' }}</p>
                <p class="text-2xl font-black text-emerald-600 mt-1">{{ $kpis['active_ingredients'] ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-dna"></i>
            </div>
        </div>
        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">{{ app()->getLocale() === 'ar' ? 'تنبيه انخفاض المخزون' : 'Below Safety Stock' }}</p>
                <p class="text-2xl font-black text-amber-500 mt-1">
                    {{ $kpis['low_stock'] ?? 0 }}
                </p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-900/30 text-amber-500 flex items-center justify-center text-xl">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
        </div>
        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">{{ app()->getLocale() === 'ar' ? 'تشغيلات قيد الحجر' : 'Active Quarantine Batches' }}</p>
                <p class="text-2xl font-black text-purple-600 mt-1">{{ $kpis['quarantine_batches'] ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-900/30 text-purple-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-shield-virus"></i>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card p-4 mb-6 border border-gray-100 dark:border-gray-800 shadow-sm">
        <form method="GET" action="{{ route('admin.raw-materials.index') }}" class="flex items-center justify-between gap-4 flex-wrap">
            <div class="search-wrapper flex-1 min-w-[260px] max-w-md">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control search-input text-sm w-full" placeholder="{{ app()->getLocale() === 'ar' ? 'بحث باسم المادة، الرمز SKU، رقم CAS، الاسم العلمي INCI...' : 'Search material name, SKU, CAS number, INCI...' }}">
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                <select name="category" onchange="this.form.submit()" class="form-select text-sm w-auto">
                    <option value="">{{ app()->getLocale() === 'ar' ? 'جميع التصنيفات' : 'All Categories' }}</option>
                    @foreach(\App\Enums\RawMaterialCategory::cases() as $cat)
                        <option value="{{ $cat->value }}" {{ request('category') === $cat->value ? 'selected' : '' }}>
                            {{ $cat->label() }}
                        </option>
                    @endforeach
                </select>

                <select name="storage_condition" onchange="this.form.submit()" class="form-select text-sm w-auto">
                    <option value="">{{ app()->getLocale() === 'ar' ? 'شروط التخزين' : 'Storage Conditions' }}</option>
                    @foreach(\App\Enums\StorageCondition::cases() as $sc)
                        <option value="{{ $sc->value }}" {{ request('storage_condition') === $sc->value ? 'selected' : '' }}>
                            {{ $sc->label() }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="btn btn-secondary btn-sm font-bold">
                    <i class="fa-solid fa-filter mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'تصفية' : 'Filter' }}
                </button>
            </div>
        </form>
    </div>

    <!-- Materials Table -->
    <x-admin.table-toolbar 
        table="#materialsTable" 
        :title="app()->getLocale() === 'ar' ? 'سجل المواد الأولية الدوائية' : 'Pharmaceutical Raw Material Catalog'" 
    />

    <div class="card shadow-sm border border-gray-100 dark:border-gray-800">
        <div class="table-responsive">
            <table class="table" id="materialsTable">
                <thead>
                    <tr>
                        <th data-sort-type="text">{{ __('raw_materials.sku') }}</th>
                        <th data-sort-type="text">{{ __('raw_materials.name') }}</th>
                        <th data-sort-type="text">{{ __('raw_materials.category') }}</th>
                        <th data-sort-type="text">{{ __('raw_materials.cas_number') }}</th>
                        <th data-sort-type="text">{{ __('raw_materials.storage_condition') }}</th>
                        <th data-sort-type="number">{{ __('raw_materials.reorder_level') }}</th>
                        <th data-sort-type="text">{{ __('raw_materials.default_supplier') }}</th>
                        <th style="text-align: center;" data-no-sort data-no-export>{{ app()->getLocale() === 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($materials as $material)
                        <tr>
                            <td class="font-mono font-bold text-sky-600 dark:text-sky-400">
                                <a href="{{ route('admin.raw-materials.show', $material) }}" class="hover:underline">
                                    {{ $material->sku }}
                                </a>
                            </td>
                            <td>
                                <div class="font-bold text-slate-800 dark:text-white">
                                    {{ $material->name }}
                                </div>
                                @if($material->inci_name)
                                    <div class="text-[11px] text-gray-500 font-mono italic">
                                        INCI: {{ $material->inci_name }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-indigo text-xs">
                                    {{ $material->category?->label() }}
                                </span>
                            </td>
                            <td class="font-mono text-xs text-gray-600 dark:text-gray-300">
                                {{ $material->cas_number ?? '—' }}
                            </td>
                            <td>
                                <span class="badge {{ $material->storage_condition?->badgeClass() }} text-xs">
                                    <i class="fa-solid fa-temperature-half mr-1 ml-1"></i>
                                    {{ $material->storage_condition?->label() }}
                                </span>
                            </td>
                            <td class="font-mono text-sm">
                                <span class="font-bold">{{ number_format($material->reorder_level, 2) }}</span>
                                <span class="text-xs text-gray-500">{{ $material->uom?->value }}</span>
                            </td>
                            <td class="text-xs">
                                @if($material->defaultSupplier)
                                    <a href="{{ route('admin.suppliers.show', $material->defaultSupplier) }}" class="text-sky-600 hover:underline">
                                        {{ $material->defaultSupplier->name }}
                                    </a>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('admin.raw-materials.show', $material) }}" class="btn btn-ghost btn-sm text-sky-600" title="{{ app()->getLocale() === 'ar' ? 'عرض التشغيلات والتفاصيل' : 'View Batches & Profile' }}">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.raw-materials.edit', $material) }}" class="btn btn-ghost btn-sm text-indigo-600" title="{{ app()->getLocale() === 'ar' ? 'تعديل' : 'Edit' }}">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-gray-500">
                                <i class="fa-solid fa-flask-vial text-3xl mb-2 text-gray-400"></i>
                                <p>{{ app()->getLocale() === 'ar' ? 'لم يتم العثور على أي مواد مطابقة.' : 'No raw materials found matching criteria.' }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($materials->hasPages())
            <div class="p-4 border-t border-gray-100 dark:border-gray-800">
                {{ $materials->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>
