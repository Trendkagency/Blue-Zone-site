<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'إدارة المناطق والمربعات الميدانية (Territories & Areas)' : 'MR Territories & Field Areas'"
    :pageSubtitle="app()->getLocale() === 'ar' ? 'تحديد وتوزيع النطاقات الجغرافية والمربعات الميدانية للمناديب والمراكز الطبية' : 'Define geographic zones and territory assignments for reps and clinics'"
    :breadcrumbs="[
        (app()->getLocale() === 'ar' ? 'نظام المندوب الطبي' : 'MR CRM') => route('admin.mr.assignments.index'),
        (app()->getLocale() === 'ar' ? 'المناطق والمربعات' : 'Territories & Areas') => route('admin.mr.areas.index')
    ]"
>
    <div class="space-y-6" x-data="{
        showCreateModal: false,
        showEditModal: false,
        showDeleteModal: false,
        selectedCountryForModal: '{{ $countryId ?? ($countries->first()->id ?? '') }}',
        createCities: [],
        editArea: { id: null, country_id: '', city_id: '', name_en: '', name_ar: '', code: '', sort_order: 0, is_active: true },
        editCities: [],
        deleteTarget: { id: null, name: '' },

        async loadCities(countryId, target = 'create') {
            if (!countryId) {
                if (target === 'create') this.createCities = [];
                else this.editCities = [];
                return;
            }
            try {
                const res = await fetch(`{{ url('/admin/api/countries') }}/${countryId}/cities`);
                const data = await res.json();
                if (target === 'create') {
                    this.createCities = data.cities || [];
                } else {
                    this.editCities = data.cities || [];
                }
            } catch (err) {
                console.error('Error loading cities:', err);
            }
        },

        openEdit(area) {
            this.editArea = { ...area };
            this.loadCities(area.country_id, 'edit').then(() => {
                this.editArea.city_id = area.city_id;
                this.showEditModal = true;
            });
        },

        confirmDelete(id, name) {
            this.deleteTarget = { id, name };
            this.showDeleteModal = true;
        }
    }" x-init="loadCities(selectedCountryForModal, 'create')">

        <!-- Top Metrics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="card p-4 border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 rounded-2xl flex items-center justify-between shadow-sm">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                        {{ app()->getLocale() === 'ar' ? 'إجمالي المناطق' : 'Total Areas' }}
                    </span>
                    <h3 class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ $stats['total_areas'] }}</h3>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $stats['covered_cities'] }} {{ app()->getLocale() === 'ar' ? 'مدينة مغطاة' : 'Cities Covered' }}</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-cyan-50 dark:bg-cyan-950/60 text-cyan-600 dark:text-cyan-400 flex items-center justify-center text-xl border border-cyan-100 dark:border-cyan-900">
                    <i class="fa-solid fa-map-location-dot"></i>
                </div>
            </div>

            <div class="card p-4 border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 rounded-2xl flex items-center justify-between shadow-sm">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                        {{ app()->getLocale() === 'ar' ? 'المناطق النشطة' : 'Active Areas' }}
                    </span>
                    <h3 class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ $stats['active_areas'] }}</h3>
                    <p class="text-xs text-emerald-500 mt-0.5">{{ app()->getLocale() === 'ar' ? 'جاهزة للتكليف والزيارات' : 'Operational' }}</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl border border-emerald-100 dark:border-emerald-900">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>

            <div class="card p-4 border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 rounded-2xl flex items-center justify-between shadow-sm">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                        {{ app()->getLocale() === 'ar' ? 'المناديب المكلفين بمناطق' : 'Assigned Reps' }}
                    </span>
                    <h3 class="text-2xl font-black text-indigo-600 dark:text-indigo-400 mt-1">{{ $stats['assigned_reps_count'] }}</h3>
                    <p class="text-xs text-indigo-500 mt-0.5">{{ app()->getLocale() === 'ar' ? 'مرتبطين بمربعات جغرافية' : 'Linked to territories' }}</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xl border border-indigo-100 dark:border-indigo-900">
                    <i class="fa-solid fa-user-shield"></i>
                </div>
            </div>

            <div class="card p-4 border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 rounded-2xl flex items-center justify-between shadow-sm">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                        {{ app()->getLocale() === 'ar' ? 'مناديب بدون منطقة' : 'Unassigned Reps' }}
                    </span>
                    <h3 class="text-2xl font-black {{ $stats['unassigned_reps_count'] > 0 ? 'text-amber-500' : 'text-slate-400' }} mt-1">{{ $stats['unassigned_reps_count'] }}</h3>
                    <p class="text-xs text-slate-500 mt-0.5">{{ app()->getLocale() === 'ar' ? 'بحاجة لتحديد المربع' : 'Need territory assignment' }}</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/60 text-amber-500 flex items-center justify-center text-xl border border-amber-100 dark:border-amber-900">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="card p-4 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-sm">
            <form method="GET" action="{{ route('admin.mr.areas.index') }}" class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-3">
                    <!-- Country Filter -->
                    <div class="w-44">
                        <select name="country_id" class="form-select text-xs w-full rounded-xl" onchange="this.form.submit()">
                            <option value="">{{ app()->getLocale() === 'ar' ? '-- كل الدول --' : '-- All Countries --' }}</option>
                            @foreach($countries as $c)
                                <option value="{{ $c->id }}" {{ $countryId == $c->id ? 'selected' : '' }}>
                                    {{ $c->flag_emoji }} {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- City Filter -->
                    <div class="w-44">
                        <select name="city_id" class="form-select text-xs w-full rounded-xl" onchange="this.form.submit()">
                            <option value="">{{ app()->getLocale() === 'ar' ? '-- كل المدن --' : '-- All Cities --' }}</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" {{ $cityId == $city->id ? 'selected' : '' }}>
                                    {{ $city->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Search Input -->
                    <div class="relative w-64">
                        <input type="text" name="search" value="{{ $search ?? '' }}" 
                            placeholder="{{ app()->getLocale() === 'ar' ? 'بحث بالاسم أو الكود...' : 'Search by name or code...' }}" 
                            class="form-input text-xs w-full rounded-xl pl-8 rtl:pr-8 rtl:pl-3">
                        <i class="fa-solid fa-magnifying-glass absolute left-2.5 rtl:right-2.5 rtl:left-auto top-2.5 text-slate-400 text-xs"></i>
                    </div>

                    <button type="submit" class="btn btn-secondary btn-sm rounded-xl">
                        <i class="fa-solid fa-filter mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'تصفية' : 'Filter' }}
                    </button>

                    @if($countryId || $cityId || $search)
                        <a href="{{ route('admin.mr.areas.index') }}" class="text-xs text-rose-500 hover:underline font-bold">
                            {{ app()->getLocale() === 'ar' ? 'إلغاء التصفية' : 'Reset' }}
                        </a>
                    @endif
                </div>

                <!-- Action Button -->
                <div class="flex items-center gap-2">
                    <button type="button" @click="showCreateModal = true" class="btn btn-primary btn-sm rounded-xl font-bold shadow-md shadow-sky-500/10">
                        <i class="fa-solid fa-plus mr-1.5 ml-1.5"></i>
                        {{ app()->getLocale() === 'ar' ? 'إضافة منطقة جديدة' : 'Add New Area' }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Areas Table -->
        <div class="card p-0 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left rtl:text-right">
                    <thead class="text-xs uppercase bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 border-b border-slate-200/80 dark:border-slate-800">
                        <tr>
                            <th class="px-5 py-3.5">{{ app()->getLocale() === 'ar' ? 'المنطقة / المربع' : 'Area / Territory' }}</th>
                            <th class="px-4 py-3.5">{{ app()->getLocale() === 'ar' ? 'الكود' : 'Code' }}</th>
                            <th class="px-4 py-3.5">{{ app()->getLocale() === 'ar' ? 'التسلسل الجغرافي (الدولة / المدينة)' : 'Hierarchy (Country / City)' }}</th>
                            <th class="px-4 py-3.5 text-center">{{ app()->getLocale() === 'ar' ? 'المناديب المسجلين' : 'Assigned MRs' }}</th>
                            <th class="px-4 py-3.5 text-center">{{ app()->getLocale() === 'ar' ? 'العيادات / الأطباء' : 'Contacts' }}</th>
                            <th class="px-4 py-3.5 text-center">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                            <th class="px-5 py-3.5 text-right rtl:text-left">{{ app()->getLocale() === 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 font-medium">
                        @forelse($areas as $area)
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-sky-50 dark:bg-sky-950/50 text-sky-600 dark:text-sky-400 flex items-center justify-center border border-sky-100 dark:border-sky-900 flex-shrink-0">
                                            <i class="fa-solid fa-location-crosshairs text-sm"></i>
                                        </div>
                                        <div>
                                            <span class="font-bold text-slate-900 dark:text-white block">{{ $area->name }}</span>
                                            <span class="text-[11px] text-slate-400 dark:text-slate-500 font-mono">
                                                {{ $area->name_en }} / {{ $area->name_ar }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5">
                                    @if($area->code)
                                        <span class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-mono font-bold">
                                            {{ $area->code }}
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-1.5 text-xs text-slate-700 dark:text-slate-300">
                                        <span class="font-semibold">{{ $area->country?->name ?? 'N/A' }}</span>
                                        <span class="text-slate-400">›</span>
                                        <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ $area->city?->name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    @if($area->medical_reps_count > 0)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 font-bold text-xs border border-indigo-200/60 dark:border-indigo-800">
                                            <i class="fa-solid fa-user-doctor text-[10px]"></i>
                                            {{ $area->medical_reps_count }}
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400 font-normal">0</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    @if($area->contacts_count > 0)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 font-bold text-xs border border-emerald-200/60 dark:border-emerald-800">
                                            <i class="fa-solid fa-stethoscope text-[10px]"></i>
                                            {{ $area->contacts_count }}
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400 font-normal">0</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <form method="POST" action="{{ route('admin.mr.areas.toggle-status', $area->id) }}" class="inline-block">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold transition-all {{ $area->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/80 dark:text-emerald-300 hover:bg-emerald-200' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 hover:bg-slate-200' }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $area->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                            {{ $area->is_active ? (app()->getLocale() === 'ar' ? 'نشط' : 'Active') : (app()->getLocale() === 'ar' ? 'معطل' : 'Inactive') }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-5 py-3.5 text-right rtl:text-left">
                                    <div class="flex items-center justify-end rtl:justify-start gap-1.5">
                                        <button type="button" @click="openEdit({
                                            id: {{ $area->id }},
                                            country_id: {{ $area->country_id }},
                                            city_id: {{ $area->city_id }},
                                            name_en: '{{ addslashes($area->name_en) }}',
                                            name_ar: '{{ addslashes($area->name_ar) }}',
                                            code: '{{ addslashes($area->code ?? '') }}',
                                            sort_order: {{ $area->sort_order ?? 0 }},
                                            is_active: {{ $area->is_active ? 'true' : 'false' }}
                                        })" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-slate-800 transition-colors" title="{{ app()->getLocale() === 'ar' ? 'تعديل' : 'Edit' }}">
                                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                                        </button>

                                        <button type="button" @click="confirmDelete({{ $area->id }}, '{{ addslashes($area->name) }}')" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-slate-800 transition-colors" title="{{ app()->getLocale() === 'ar' ? 'حذف' : 'Delete' }}">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500">
                                    <i class="fa-solid fa-map-location-dot text-4xl mb-3 block opacity-40"></i>
                                    <p class="font-bold">{{ app()->getLocale() === 'ar' ? 'لا توجد مناطق جغرافية مسجلة حالياً' : 'No areas found matching criteria' }}</p>
                                    <p class="text-xs mt-1">{{ app()->getLocale() === 'ar' ? 'يمكنك إضافة مربع جغرافي جديد بالنقر على زر الإضافة أعلاه' : 'Click Add New Area above to start mapping territories' }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($areas->hasPages())
                <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                    {{ $areas->links() }}
                </div>
            @endif
        </div>

        <!-- CREATE AREA MODAL -->
        <div x-show="showCreateModal" x-cloak style="display: none;" class="fixed inset-0 z-[1050] overflow-y-auto bg-black/60 p-3 sm:p-4 backdrop-blur-sm flex items-center justify-center" @click.self="showCreateModal = false">
            <div class="card max-w-lg w-full p-0 shadow-2xl relative flex flex-col max-h-[90vh] border border-gray-100 dark:border-gray-800 rounded-2xl overflow-hidden bg-white dark:bg-gray-850">
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 px-6 py-4 flex-shrink-0 bg-white dark:bg-gray-800">
                    <h3 class="font-bold text-base text-gray-900 dark:text-white flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 flex items-center justify-center border border-sky-100 dark:border-sky-900">
                            <i class="fa-solid fa-plus text-sm"></i>
                        </div>
                        <span>{{ app()->getLocale() === 'ar' ? 'إضافة منطقة / مربع ميداني جديد' : 'Add New Area / Territory' }}</span>
                    </h3>
                    <button type="button" @click="showCreateModal = false" class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.mr.areas.store') }}" class="flex flex-col flex-1 min-h-0">
                    @csrf
                    <div class="p-6 space-y-4 overflow-y-auto flex-1">
                        <!-- Country Selector -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                                {{ app()->getLocale() === 'ar' ? 'الدولة التابعة لها *' : 'Country *' }}
                            </label>
                            <select name="country_id" x-model="selectedCountryForModal" @change="loadCities(selectedCountryForModal, 'create')" required class="form-select text-sm w-full">
                                <option value="">{{ app()->getLocale() === 'ar' ? '-- اختر الدولة --' : '-- Select Country --' }}</option>
                                @foreach($countries as $c)
                                    <option value="{{ $c->id }}">{{ $c->flag_emoji }} {{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- City Selector (Cascading) -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                                {{ app()->getLocale() === 'ar' ? 'المدينة *' : 'City *' }}
                            </label>
                            <select name="city_id" required class="form-select text-sm w-full">
                                <option value="">{{ app()->getLocale() === 'ar' ? '-- اختر المدينة --' : '-- Select City --' }}</option>
                                <template x-for="city in createCities" :key="city.id">
                                    <option :value="city.id" x-text="'{{ app()->getLocale() }}' === 'ar' ? (city.name_ar || city.name_en) : (city.name_en || city.name_ar)"></option>
                                </template>
                            </select>
                            <p class="text-[11px] text-slate-400 mt-1" x-show="createCities.length === 0">
                                {{ app()->getLocale() === 'ar' ? 'حدد الدولة أولاً لتحميل مدنها المتاحة' : 'Select country first to load available cities' }}
                            </p>
                        </div>

                        <!-- Area Name (EN & AR) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                                    {{ app()->getLocale() === 'ar' ? 'اسم المنطقة (إنجليزي) *' : 'Area Name (EN) *' }}
                                </label>
                                <input type="text" name="name_en" required placeholder="e.g. Maadi or Al-Olaya" class="form-input text-sm w-full">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                                    {{ app()->getLocale() === 'ar' ? 'اسم المنطقة (عربي) *' : 'Area Name (AR) *' }}
                                </label>
                                <input type="text" name="name_ar" required placeholder="مثال: المعادي أو العليا" class="form-input text-sm w-full">
                            </div>
                        </div>

                        <!-- Code & Sort Order -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                                    {{ app()->getLocale() === 'ar' ? 'كود المربع / المنطقة' : 'Area / Territory Code' }}
                                </label>
                                <input type="text" name="code" placeholder="e.g. CAI-MAA-01" class="form-input text-sm w-full font-mono">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                                    {{ app()->getLocale() === 'ar' ? 'ترتيب الظهور' : 'Sort Order' }}
                                </label>
                                <input type="number" name="sort_order" value="0" min="0" class="form-input text-sm w-full">
                            </div>
                        </div>

                        <div class="pt-1">
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" checked class="form-checkbox text-sky-600 rounded">
                                <span class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                    {{ app()->getLocale() === 'ar' ? 'تفعيل المنطقة فورياً للتكليف' : 'Active and available for assignments' }}
                                </span>
                            </label>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 dark:border-gray-800 px-6 py-3 flex-shrink-0 bg-gray-50 dark:bg-gray-800/80 flex items-center justify-end gap-3">
                        <button type="button" @click="showCreateModal = false" class="btn btn-secondary btn-sm">
                            {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                        </button>
                        <button type="submit" class="btn btn-primary btn-sm font-bold">
                            <i class="fa-solid fa-floppy-disk mr-1 ml-1"></i>
                            {{ app()->getLocale() === 'ar' ? 'حفظ المنطقة' : 'Save Area' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- EDIT AREA MODAL -->
        <div x-show="showEditModal" x-cloak style="display: none;" class="fixed inset-0 z-[1050] overflow-y-auto bg-black/60 p-3 sm:p-4 backdrop-blur-sm flex items-center justify-center" @click.self="showEditModal = false">
            <div class="card max-w-lg w-full p-0 shadow-2xl relative flex flex-col max-h-[90vh] border border-gray-100 dark:border-gray-800 rounded-2xl overflow-hidden bg-white dark:bg-gray-850">
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 px-6 py-4 flex-shrink-0 bg-white dark:bg-gray-800">
                    <h3 class="font-bold text-base text-gray-900 dark:text-white flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center border border-indigo-100 dark:border-indigo-900">
                            <i class="fa-solid fa-pen-to-square text-sm"></i>
                        </div>
                        <span>{{ app()->getLocale() === 'ar' ? 'تعديل بيانات المنطقة' : 'Edit Area' }}</span>
                    </h3>
                    <button type="button" @click="showEditModal = false" class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                <form :action="`{{ url('/admin/mr/areas') }}/${editArea.id}`" method="POST" class="flex flex-col flex-1 min-h-0">
                    @csrf
                    @method('PUT')
                    <div class="p-6 space-y-4 overflow-y-auto flex-1">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                                {{ app()->getLocale() === 'ar' ? 'الدولة *' : 'Country *' }}
                            </label>
                            <select name="country_id" x-model="editArea.country_id" @change="loadCities(editArea.country_id, 'edit')" required class="form-select text-sm w-full">
                                <option value="">{{ app()->getLocale() === 'ar' ? '-- اختر الدولة --' : '-- Select Country --' }}</option>
                                @foreach($countries as $c)
                                    <option value="{{ $c->id }}">{{ $c->flag_emoji }} {{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                                {{ app()->getLocale() === 'ar' ? 'المدينة *' : 'City *' }}
                            </label>
                            <select name="city_id" x-model="editArea.city_id" required class="form-select text-sm w-full">
                                <option value="">{{ app()->getLocale() === 'ar' ? '-- اختر المدينة --' : '-- Select City --' }}</option>
                                <template x-for="city in editCities" :key="city.id">
                                    <option :value="city.id" :selected="city.id == editArea.city_id" x-text="'{{ app()->getLocale() }}' === 'ar' ? (city.name_ar || city.name_en) : (city.name_en || city.name_ar)"></option>
                                </template>
                            </select>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                                    {{ app()->getLocale() === 'ar' ? 'اسم المنطقة (إنجليزي) *' : 'Area Name (EN) *' }}
                                </label>
                                <input type="text" name="name_en" x-model="editArea.name_en" required class="form-input text-sm w-full">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                                    {{ app()->getLocale() === 'ar' ? 'اسم المنطقة (عربي) *' : 'Area Name (AR) *' }}
                                </label>
                                <input type="text" name="name_ar" x-model="editArea.name_ar" required class="form-input text-sm w-full">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                                    {{ app()->getLocale() === 'ar' ? 'الكود' : 'Code' }}
                                </label>
                                <input type="text" name="code" x-model="editArea.code" class="form-input text-sm w-full font-mono">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                                    {{ app()->getLocale() === 'ar' ? 'ترتيب الظهور' : 'Sort Order' }}
                                </label>
                                <input type="number" name="sort_order" x-model="editArea.sort_order" min="0" class="form-input text-sm w-full">
                            </div>
                        </div>

                        <div class="pt-1">
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" :checked="editArea.is_active" class="form-checkbox text-indigo-600 rounded">
                                <span class="text-xs font-bold text-slate-700 dark:text-slate-300">
                                    {{ app()->getLocale() === 'ar' ? 'المنطقة نشطة' : 'Area is active' }}
                                </span>
                            </label>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 dark:border-gray-800 px-6 py-3 flex-shrink-0 bg-gray-50 dark:bg-gray-800/80 flex items-center justify-end gap-3">
                        <button type="button" @click="showEditModal = false" class="btn btn-secondary btn-sm">
                            {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                        </button>
                        <button type="submit" class="btn btn-primary btn-sm font-bold">
                            <i class="fa-solid fa-floppy-disk mr-1 ml-1"></i>
                            {{ app()->getLocale() === 'ar' ? 'حفظ التعديلات' : 'Update Area' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- DELETE CONFIRMATION MODAL -->
        <div x-show="showDeleteModal" x-cloak style="display: none;" class="fixed inset-0 z-[1050] overflow-y-auto bg-black/60 p-3 sm:p-4 backdrop-blur-sm flex items-center justify-center" @click.self="showDeleteModal = false">
            <div class="card max-w-md w-full p-6 shadow-2xl border border-rose-100 dark:border-rose-900 rounded-2xl bg-white dark:bg-gray-850">
                <div class="w-12 h-12 rounded-2xl bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center text-xl mx-auto mb-4 border border-rose-100 dark:border-rose-900">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <h3 class="font-bold text-center text-base text-gray-900 dark:text-white mb-2">
                    {{ app()->getLocale() === 'ar' ? 'تأكيد حذف المنطقة' : 'Confirm Delete Area' }}
                </h3>
                <p class="text-xs text-center text-slate-500 mb-6">
                    {{ app()->getLocale() === 'ar' ? 'هل أنت متأكد من رغبتك في حذف المنطقة التالية؟ لن تتمكن من حذفها في حال وجود مناديب أو أطباء مرتبطين بها.' : 'Are you sure you want to delete this area? You cannot delete it if reps or clinics are assigned to it.' }}
                    <strong class="block text-slate-900 dark:text-white mt-1.5" x-text="deleteTarget.name"></strong>
                </p>
                <form :action="`{{ url('/admin/mr/areas') }}/${deleteTarget.id}`" method="POST" class="flex items-center justify-center gap-3">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="showDeleteModal = false" class="btn btn-secondary btn-sm">
                        {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                    </button>
                    <button type="submit" class="btn btn-danger btn-sm font-bold bg-rose-600 hover:bg-rose-700 text-white">
                        <i class="fa-solid fa-trash mr-1 ml-1"></i>
                        {{ app()->getLocale() === 'ar' ? 'تأكيد الحذف' : 'Confirm Delete' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.admin>
