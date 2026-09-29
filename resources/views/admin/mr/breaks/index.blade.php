<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'إدارة القطاعات الفرعية والبريكات (Territory Breaks)' : 'Territory Breaks & Sub-Sectors'"
    :pageSubtitle="app()->getLocale() === 'ar' ? 'تحديد وتوزيع البريكات والقطاعات الميدانية التابعة للمناطق للمناديب والمراكز الطبية' : 'Define sub-area breaks and assign field sectors to medical reps and doctors'"
    :breadcrumbs="[
        (app()->getLocale() === 'ar' ? 'نظام المندوب الطبي' : 'MR CRM') => route('admin.mr.assignments.index'),
        (app()->getLocale() === 'ar' ? 'المناطق' : 'Areas') => route('admin.mr.areas.index'),
        (app()->getLocale() === 'ar' ? 'البريكات والقطاعات' : 'Breaks') => route('admin.mr.breaks.index')
    ]"
>
    <div class="space-y-6" x-data="{
        showCreateModal: false,
        showEditModal: false,
        showDeleteModal: false,
        selectedCountryForModal: '{{ $countryId ?? ($countries->first()->id ?? '') }}',
        selectedCityForModal: '{{ $cityId ?? '' }}',
        createCities: [],
        createAreas: [],
        editBreak: { id: null, country_id: '', city_id: '', area_id: '', name_en: '', name_ar: '', code: '', sort_order: 0, is_active: true },
        editCities: [],
        editAreas: [],
        deleteTarget: { id: null, name: '' },

        async loadCities(countryId, target = 'create') {
            if (!countryId) {
                if (target === 'create') {
                    this.createCities = [];
                    this.createAreas = [];
                } else {
                    this.editCities = [];
                    this.editAreas = [];
                }
                return;
            }
            try {
                const res = await fetch(`{{ url('/admin/api/countries') }}/${countryId}/cities`);
                const data = await res.json();
                if (target === 'create') {
                    this.createCities = data.cities || [];
                    this.createAreas = [];
                } else {
                    this.editCities = data.cities || [];
                }
            } catch (err) {
                console.error('Error loading cities:', err);
            }
        },

        async loadAreas(cityId, target = 'create') {
            if (!cityId) {
                if (target === 'create') this.createAreas = [];
                else this.editAreas = [];
                return;
            }
            try {
                const res = await fetch(`{{ url('/admin/api/cities') }}/${cityId}/areas`);
                const data = await res.json();
                if (target === 'create') {
                    this.createAreas = data.areas || [];
                } else {
                    this.editAreas = data.areas || [];
                }
            } catch (err) {
                console.error('Error loading areas:', err);
            }
        },

        async openEdit(item) {
            this.editBreak = { ...item };
            await this.loadCities(item.country_id, 'edit');
            this.editBreak.city_id = item.city_id;
            await this.loadAreas(item.city_id, 'edit');
            this.editBreak.area_id = item.area_id;
            this.showEditModal = true;
        },

        confirmDelete(id, name) {
            this.deleteTarget = { id, name };
            this.showDeleteModal = true;
        }
    }" x-init="
        if (selectedCountryForModal) {
            loadCities(selectedCountryForModal, 'create').then(() => {
                if (selectedCityForModal) {
                    loadAreas(selectedCityForModal, 'create');
                }
            });
        }
    ">

        <!-- Navigation Tabs: Areas vs Breaks -->
        <div class="flex items-center gap-2 border-b border-slate-200 dark:border-slate-800 pb-2">
            <a href="{{ route('admin.mr.areas.index') }}" class="px-4 py-2 text-sm font-bold rounded-xl transition-all text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white flex items-center gap-2">
                <i class="fa-solid fa-map-location-dot"></i>
                <span>{{ app()->getLocale() === 'ar' ? 'المناطق الجغرافية (Areas)' : 'Geographic Areas' }}</span>
                <span class="px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-xs text-slate-600 dark:text-slate-400">{{ $stats['total_areas'] }}</span>
            </a>
            <a href="{{ route('admin.mr.breaks.index') }}" class="px-4 py-2 text-sm font-bold rounded-xl bg-sky-500 text-white shadow-md shadow-sky-500/20 flex items-center gap-2">
                <i class="fa-solid fa-network-wired"></i>
                <span>{{ app()->getLocale() === 'ar' ? 'البريكات والقطاعات الفرعية (Breaks)' : 'Territory Breaks' }}</span>
                <span class="px-2 py-0.5 rounded-full bg-white/20 text-xs text-white">{{ $stats['total_breaks'] ?? 0 }}</span>
            </a>
        </div>

        <!-- Top Metrics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="card p-4 border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 rounded-2xl flex items-center justify-between shadow-sm">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                        {{ app()->getLocale() === 'ar' ? 'إجمالي البريكات' : 'Total Breaks' }}
                    </span>
                    <h3 class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ $stats['total_breaks'] ?? 0 }}</h3>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $stats['total_areas'] }} {{ app()->getLocale() === 'ar' ? 'منطقة تابعة' : 'Parent Areas' }}</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-cyan-50 dark:bg-cyan-950/60 text-cyan-600 dark:text-cyan-400 flex items-center justify-center text-xl border border-cyan-100 dark:border-cyan-900">
                    <i class="fa-solid fa-network-wired"></i>
                </div>
            </div>

            <div class="card p-4 border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 rounded-2xl flex items-center justify-between shadow-sm">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                        {{ app()->getLocale() === 'ar' ? 'البريكات النشطة' : 'Active Breaks' }}
                    </span>
                    <h3 class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ $stats['active_breaks'] ?? 0 }}</h3>
                    <p class="text-xs text-emerald-500 mt-0.5">{{ app()->getLocale() === 'ar' ? 'جاهزة للتكليف والزيارات' : 'Operational' }}</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl border border-emerald-100 dark:border-emerald-900">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>

            <div class="card p-4 border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 rounded-2xl flex items-center justify-between shadow-sm">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                        {{ app()->getLocale() === 'ar' ? 'المناطق المغطاة' : 'Covered Areas' }}
                    </span>
                    <h3 class="text-2xl font-black text-indigo-600 dark:text-indigo-400 mt-1">{{ $stats['total_areas'] }}</h3>
                    <p class="text-xs text-indigo-500 mt-0.5">{{ app()->getLocale() === 'ar' ? 'موزعة على بريكات' : 'Divided into breaks' }}</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xl border border-indigo-100 dark:border-indigo-900">
                    <i class="fa-solid fa-map-location-dot"></i>
                </div>
            </div>

            <div class="card p-4 border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 rounded-2xl flex items-center justify-between shadow-sm">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                        {{ app()->getLocale() === 'ar' ? 'المناديب المكلفين' : 'Assigned Reps' }}
                    </span>
                    <h3 class="text-2xl font-black text-sky-600 dark:text-sky-400 mt-1">{{ $stats['assigned_reps_count'] }}</h3>
                    <p class="text-xs text-slate-500 mt-0.5">{{ app()->getLocale() === 'ar' ? 'مرتبطين بمناطق وبريكات' : 'Assigned to sectors' }}</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 flex items-center justify-center text-xl border border-sky-100 dark:border-sky-900">
                    <i class="fa-solid fa-user-shield"></i>
                </div>
            </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="card p-4 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-sm">
            <form method="GET" action="{{ route('admin.mr.breaks.index') }}" class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-3">
                    <!-- Country Filter -->
                    <div class="w-40">
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
                    <div class="w-40">
                        <select name="city_id" class="form-select text-xs w-full rounded-xl" onchange="this.form.submit()">
                            <option value="">{{ app()->getLocale() === 'ar' ? '-- كل المدن --' : '-- All Cities --' }}</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" {{ $cityId == $city->id ? 'selected' : '' }}>
                                    {{ $city->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Area Filter -->
                    <div class="w-44">
                        <select name="area_id" class="form-select text-xs w-full rounded-xl" onchange="this.form.submit()">
                            <option value="">{{ app()->getLocale() === 'ar' ? '-- كل المناطق --' : '-- All Areas --' }}</option>
                            @foreach($areas as $a)
                                <option value="{{ $a->id }}" {{ $areaId == $a->id ? 'selected' : '' }}>
                                    {{ $a->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Search Input -->
                    <div class="relative w-56">
                        <input type="text" name="search" value="{{ $search ?? '' }}" 
                            placeholder="{{ app()->getLocale() === 'ar' ? 'بحث باسم البريك أو الكود...' : 'Search break name or code...' }}" 
                            class="form-input text-xs w-full rounded-xl pl-8 rtl:pr-8 rtl:pl-3">
                        <i class="fa-solid fa-magnifying-glass absolute left-2.5 rtl:right-2.5 rtl:left-auto top-2.5 text-slate-400 text-xs"></i>
                    </div>

                    <button type="submit" class="btn btn-secondary btn-sm rounded-xl">
                        <i class="fa-solid fa-filter mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'تصفية' : 'Filter' }}
                    </button>

                    @if($countryId || $cityId || $areaId || $search)
                        <a href="{{ route('admin.mr.breaks.index') }}" class="text-xs text-rose-500 hover:underline font-bold">
                            {{ app()->getLocale() === 'ar' ? 'إلغاء التصفية' : 'Reset' }}
                        </a>
                    @endif
                </div>

                <!-- Action Button -->
                <div class="flex items-center gap-2">
                    <button type="button" @click="showCreateModal = true" class="btn btn-primary btn-sm rounded-xl font-bold shadow-md shadow-sky-500/10">
                        <i class="fa-solid fa-plus mr-1.5 ml-1.5"></i>
                        {{ app()->getLocale() === 'ar' ? 'إضافة بريك جديد' : 'Add New Break' }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Breaks Table -->
        <div class="card p-0 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left rtl:text-right">
                    <thead class="text-xs uppercase bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 border-b border-slate-200/80 dark:border-slate-800">
                        <tr>
                            <th class="px-5 py-3.5">{{ app()->getLocale() === 'ar' ? 'البريك / القطاع الفرعي' : 'Break / Sub-Sector' }}</th>
                            <th class="px-4 py-3.5">{{ app()->getLocale() === 'ar' ? 'الكود' : 'Code' }}</th>
                            <th class="px-4 py-3.5">{{ app()->getLocale() === 'ar' ? 'التسلسل الجغرافي الكامل (دولة › مدينة › منطقة)' : 'Full Hierarchy (Country › City › Area)' }}</th>
                            <th class="px-4 py-3.5 text-center">{{ app()->getLocale() === 'ar' ? 'المناديب المسجلين' : 'Assigned MRs' }}</th>
                            <th class="px-4 py-3.5 text-center">{{ app()->getLocale() === 'ar' ? 'العيادات / الأطباء' : 'Contacts' }}</th>
                            <th class="px-4 py-3.5 text-center">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                            <th class="px-5 py-3.5 text-right rtl:text-left">{{ app()->getLocale() === 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 font-medium">
                        @forelse($breaks as $item)
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-sky-50 dark:bg-sky-950/50 text-sky-600 dark:text-sky-400 flex items-center justify-center border border-sky-100 dark:border-sky-900 flex-shrink-0">
                                            <i class="fa-solid fa-network-wired text-sm"></i>
                                        </div>
                                        <div>
                                            <span class="font-bold text-slate-900 dark:text-white block">{{ $item->name }}</span>
                                            <span class="text-[11px] text-slate-400 dark:text-slate-500 font-mono">
                                                {{ $item->name_en }} / {{ $item->name_ar }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5">
                                    @if($item->code)
                                        <span class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-mono font-bold">
                                            {{ $item->code }}
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-1.5 text-xs text-slate-700 dark:text-slate-300 flex-wrap">
                                        <span class="font-semibold">{{ $item->country?->name ?? 'N/A' }}</span>
                                        <span class="text-slate-400">›</span>
                                        <span class="font-semibold text-slate-600 dark:text-slate-400">{{ $item->city?->name ?? 'N/A' }}</span>
                                        <span class="text-slate-400">›</span>
                                        <span class="font-bold text-sky-600 dark:text-sky-400">{{ $item->area?->name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    @if($item->medical_reps_count > 0)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 font-bold text-xs border border-indigo-200/60 dark:border-indigo-800">
                                            <i class="fa-solid fa-user-doctor text-[10px]"></i>
                                            {{ $item->medical_reps_count }}
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400 font-normal">0</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    @if($item->contacts_count > 0)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 font-bold text-xs border border-emerald-200/60 dark:border-emerald-800">
                                            <i class="fa-solid fa-stethoscope text-[10px]"></i>
                                            {{ $item->contacts_count }}
                                        </span>
                                    @else
                                        <span class="text-xs text-slate-400 font-normal">0</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <form method="POST" action="{{ route('admin.mr.breaks.toggle-status', $item->id) }}" class="inline-block">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold transition-all {{ $item->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/80 dark:text-emerald-300 hover:bg-emerald-200' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400 hover:bg-slate-200' }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $item->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                            {{ $item->is_active ? (app()->getLocale() === 'ar' ? 'نشط' : 'Active') : (app()->getLocale() === 'ar' ? 'معطل' : 'Inactive') }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-5 py-3.5 text-right rtl:text-left">
                                    <div class="flex items-center justify-end rtl:justify-start gap-1.5">
                                        <button type="button" @click="openEdit({
                                            id: {{ $item->id }},
                                            country_id: {{ $item->country_id }},
                                            city_id: {{ $item->city_id }},
                                            area_id: {{ $item->area_id }},
                                            name_en: '{{ addslashes($item->name_en) }}',
                                            name_ar: '{{ addslashes($item->name_ar) }}',
                                            code: '{{ addslashes($item->code ?? '') }}',
                                            sort_order: {{ $item->sort_order ?? 0 }},
                                            is_active: {{ $item->is_active ? 'true' : 'false' }}
                                        })" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-slate-800 transition-colors" title="{{ app()->getLocale() === 'ar' ? 'تعديل' : 'Edit' }}">
                                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                                        </button>

                                        <button type="button" @click="confirmDelete({{ $item->id }}, '{{ addslashes($item->name) }}')" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-slate-800 transition-colors" title="{{ app()->getLocale() === 'ar' ? 'حذف' : 'Delete' }}">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500">
                                    <i class="fa-solid fa-network-wired text-4xl mb-3 block opacity-40"></i>
                                    <p class="font-bold">{{ app()->getLocale() === 'ar' ? 'لا توجد بريكات أو قطاعات فرعية مسجلة حالياً' : 'No breaks found matching criteria' }}</p>
                                    <p class="text-xs mt-1">{{ app()->getLocale() === 'ar' ? 'يمكنك إضافة بريك جديد بالنقر على زر الإضافة أعلاه' : 'Click Add New Break above to create sector divisions' }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($breaks->hasPages())
                <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                    {{ $breaks->links() }}
                </div>
            @endif
        </div>

        <!-- CREATE BREAK MODAL -->
        <div x-show="showCreateModal" x-cloak style="display: none;" 
            class="fixed inset-0 z-[1050] overflow-y-auto bg-slate-950/75 backdrop-blur-md p-3 sm:p-6 flex items-center justify-center transition-all duration-300"
            @click.self="showCreateModal = false"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            
            <div class="max-w-xl w-full shadow-2xl relative flex flex-col max-h-[92vh] border rounded-3xl overflow-hidden transition-all transform bg-white dark:bg-[#071F33] border-slate-200/90 dark:border-[#133957]"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b px-6 py-4.5 flex-shrink-0 bg-slate-50/80 dark:bg-[#06243C] border-slate-100 dark:border-[#133957]">
                    <div class="flex items-center gap-3">
                        <div style="background: linear-gradient(135deg, #0A4F78 0%, #0284C7 100%); color: #ffffff;" class="w-11 h-11 rounded-2xl flex items-center justify-center shadow-lg shadow-sky-500/25 flex-shrink-0">
                            <i class="fa-solid fa-plus text-base text-white"></i>
                        </div>
                        <div>
                            <h3 class="font-black text-base sm:text-lg text-slate-900 dark:text-white">
                                {{ app()->getLocale() === 'ar' ? 'إضافة بريك / قطاع فرعي جديد' : 'Add New Break / Sector' }}
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                {{ app()->getLocale() === 'ar' ? 'إنشاء قطاع ميداني فرعي وربطه بالمنطقة الجغرافية' : 'Create a field sector and attach it to parent territory' }}
                            </p>
                        </div>
                    </div>
                    <button type="button" @click="showCreateModal = false" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-[#0B2942] dark:hover:bg-[#133957] border border-transparent dark:border-[#1E4E73] flex items-center justify-center text-slate-400 hover:text-slate-700 dark:text-slate-400 dark:hover:text-white transition-all cursor-pointer">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <form method="POST" action="{{ route('admin.mr.breaks.store') }}" class="flex flex-col flex-1 min-h-0">
                    @csrf
                    <div class="p-6 space-y-5 overflow-y-auto flex-1 custom-scrollbar bg-white dark:bg-[#071F33]">
                        
                        <!-- 1. Geographic Anchor Box -->
                        <div class="p-4 rounded-2xl bg-slate-50/90 dark:bg-[#031827]/70 border border-slate-200/90 dark:border-[#133957] space-y-3.5">
                            <div class="flex items-center gap-2 text-xs font-black uppercase text-sky-600 dark:text-sky-400 tracking-wider">
                                <i class="fa-solid fa-earth-americas"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'التسلسل الجغرافي للمنطقة' : 'Territory Geographic Hierarchy' }}</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <!-- Country -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                        {{ app()->getLocale() === 'ar' ? 'الدولة' : 'Country' }} <span class="text-rose-500">*</span>
                                    </label>
                                    <select name="country_id" x-model="selectedCountryForModal" @change="loadCities(selectedCountryForModal, 'create')" required class="form-select text-xs sm:text-sm w-full rounded-xl font-medium border border-slate-200 dark:border-[#1E4E73] bg-white dark:bg-[#031827] text-slate-900 dark:text-white focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 outline-none">
                                        <option value="">{{ app()->getLocale() === 'ar' ? '-- اختر الدولة --' : '-- Country --' }}</option>
                                        @foreach($countries as $c)
                                            <option value="{{ $c->id }}">{{ $c->flag_emoji }} {{ $c->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- City -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                        {{ app()->getLocale() === 'ar' ? 'المدينة / المحافظة' : 'City' }} <span class="text-rose-500">*</span>
                                    </label>
                                    <select name="city_id" x-model="selectedCityForModal" @change="loadAreas(selectedCityForModal, 'create')" required class="form-select text-xs sm:text-sm w-full rounded-xl font-medium border border-slate-200 dark:border-[#1E4E73] bg-white dark:bg-[#031827] text-slate-900 dark:text-white focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 outline-none">
                                        <option value="">{{ app()->getLocale() === 'ar' ? '-- اختر المدينة --' : '-- City --' }}</option>
                                        <template x-for="city in createCities" :key="city.id">
                                            <option :value="city.id" x-text="'{{ app()->getLocale() }}' === 'ar' ? (city.name_ar || city.name_en) : (city.name_en || city.name_ar)"></option>
                                        </template>
                                    </select>
                                </div>

                                <!-- Area -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                        {{ app()->getLocale() === 'ar' ? 'المنطقة التابعة' : 'Parent Area' }} <span class="text-rose-500">*</span>
                                    </label>
                                    <select name="area_id" required class="form-select text-xs sm:text-sm w-full rounded-xl font-semibold border border-slate-200 dark:border-[#1E4E73] bg-white dark:bg-[#031827] text-sky-600 dark:text-sky-400 focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 outline-none">
                                        <option value="">{{ app()->getLocale() === 'ar' ? '-- اختر المنطقة --' : '-- Area --' }}</option>
                                        <template x-for="area in createAreas" :key="area.id">
                                            <option :value="area.id" x-text="'{{ app()->getLocale() }}' === 'ar' ? (area.name_ar || area.name_en) : (area.name_en || area.name_ar)"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Break Information -->
                        <div class="space-y-3.5">
                            <div class="flex items-center gap-2 text-xs font-black uppercase text-slate-400 dark:text-slate-400 tracking-wider">
                                <i class="fa-solid fa-network-wired"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'بيانات وتعريف البريك' : 'Break Identification' }}</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                        {{ app()->getLocale() === 'ar' ? 'اسم البريك (عربي)' : 'Break Name (Arabic)' }} <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="text" name="name_ar" required placeholder="مثال: بريك دجلة أو قطاع 1" class="form-input text-xs sm:text-sm w-full rounded-xl font-medium border border-slate-200 dark:border-[#1E4E73] bg-white dark:bg-[#031827] text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 outline-none pr-9 rtl:pr-3 rtl:pl-9">
                                        <span class="absolute right-3 rtl:right-auto rtl:left-3 top-2.5 text-slate-400 dark:text-slate-500 text-xs font-bold pointer-events-none">AR</span>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                        {{ app()->getLocale() === 'ar' ? 'اسم البريك (إنجليزي)' : 'Break Name (English)' }} <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="text" name="name_en" required placeholder="e.g. Degla Break or Sector 1" class="form-input text-xs sm:text-sm w-full rounded-xl font-medium border border-slate-200 dark:border-[#1E4E73] bg-white dark:bg-[#031827] text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 outline-none pr-9 rtl:pr-3 rtl:pl-9">
                                        <span class="absolute right-3 rtl:right-auto rtl:left-3 top-2.5 text-slate-400 dark:text-slate-500 text-xs font-bold pointer-events-none">EN</span>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                        {{ app()->getLocale() === 'ar' ? 'كود البريك الفريد' : 'Break Code' }}
                                    </label>
                                    <div class="relative">
                                        <input type="text" name="code" placeholder="e.g. MAA-BRK-01" class="form-input text-xs sm:text-sm w-full rounded-xl font-mono uppercase border border-slate-200 dark:border-[#1E4E73] bg-white dark:bg-[#031827] text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 outline-none pl-8 rtl:pr-8 rtl:pl-3">
                                        <i class="fa-solid fa-hashtag absolute left-2.5 rtl:right-2.5 rtl:left-auto top-3 text-slate-400 dark:text-slate-500 text-xs pointer-events-none"></i>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                        {{ app()->getLocale() === 'ar' ? 'ترتيب الظهور' : 'Display Sort Order' }}
                                    </label>
                                    <input type="number" name="sort_order" value="0" min="0" class="form-input text-xs sm:text-sm w-full rounded-xl font-medium border border-slate-200 dark:border-[#1E4E73] bg-white dark:bg-[#031827] text-slate-900 dark:text-white focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 outline-none">
                                </div>
                            </div>
                        </div>

                        <!-- 3. Activation Toggle Box -->
                        <div class="p-3.5 rounded-2xl bg-emerald-50/70 dark:bg-emerald-950/25 border border-emerald-200/80 dark:border-emerald-800/40 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-emerald-100 dark:bg-emerald-900/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-sm">
                                    <i class="fa-solid fa-toggle-on"></i>
                                </div>
                                <div>
                                    <span class="text-xs font-bold text-slate-900 dark:text-white block">
                                        {{ app()->getLocale() === 'ar' ? 'تفعيل البريك والقطاع فورياً' : 'Activate Break Immediately' }}
                                    </span>
                                    <span class="text-[11px] text-slate-500 dark:text-slate-400">
                                        {{ app()->getLocale() === 'ar' ? 'متاح مباشرة للتكليف للمناديب وربط الأطباء' : 'Available for MR assignments and clinic visits' }}
                                    </span>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" checked class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-hidden rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-emerald-500"></div>
                            </label>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="border-t px-6 py-4 flex-shrink-0 bg-slate-50/90 dark:bg-[#051A2C] border-slate-100 dark:border-[#133957] flex items-center justify-end gap-3">
                        <button type="button" @click="showCreateModal = false" class="btn btn-cancel text-xs sm:text-sm font-bold px-4 py-2.5 rounded-xl cursor-pointer">
                            {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                        </button>
                        <button type="submit" class="btn btn-save text-xs sm:text-sm font-black px-6 py-2.5 rounded-xl flex items-center gap-2 cursor-pointer border-0">
                            <i class="fa-solid fa-floppy-disk text-white"></i>
                            <span class="text-white">{{ app()->getLocale() === 'ar' ? 'حفظ البريك' : 'Save Break' }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- EDIT BREAK MODAL -->
        <div x-show="showEditModal" x-cloak style="display: none;" 
            class="fixed inset-0 z-[1050] overflow-y-auto bg-slate-950/75 backdrop-blur-md p-3 sm:p-6 flex items-center justify-center transition-all duration-300"
            @click.self="showEditModal = false"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            
            <div class="max-w-xl w-full shadow-2xl relative flex flex-col max-h-[92vh] border rounded-3xl overflow-hidden transition-all transform bg-white dark:bg-[#071F33] border-slate-200/90 dark:border-[#133957]"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b px-6 py-4.5 flex-shrink-0 bg-slate-50/80 dark:bg-[#06243C] border-slate-100 dark:border-[#133957]">
                    <div class="flex items-center gap-3">
                        <div style="background: linear-gradient(135deg, #4338CA 0%, #6366F1 100%); color: #ffffff;" class="w-11 h-11 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-500/25 flex-shrink-0">
                            <i class="fa-solid fa-pen-to-square text-base text-white"></i>
                        </div>
                        <div>
                            <h3 class="font-black text-base sm:text-lg text-slate-900 dark:text-white">
                                {{ app()->getLocale() === 'ar' ? 'تعديل بيانات البريك' : 'Edit Break Details' }}
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                {{ app()->getLocale() === 'ar' ? 'تحديث مسمى البريك ونطاقه الجغرافي وكوده' : 'Update break naming, geographic link, and operational code' }}
                            </p>
                        </div>
                    </div>
                    <button type="button" @click="showEditModal = false" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-[#0B2942] dark:hover:bg-[#133957] border border-transparent dark:border-[#1E4E73] flex items-center justify-center text-slate-400 hover:text-slate-700 dark:text-slate-400 dark:hover:text-white transition-all cursor-pointer">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <form :action="`{{ url('/admin/mr/breaks') }}/${editBreak.id}`" method="POST" class="flex flex-col flex-1 min-h-0">
                    @csrf
                    @method('PUT')
                    <div class="p-6 space-y-5 overflow-y-auto flex-1 custom-scrollbar bg-white dark:bg-[#071F33]">
                        
                        <!-- 1. Geographic Anchor Box -->
                        <div class="p-4 rounded-2xl bg-slate-50/90 dark:bg-[#031827]/70 border border-slate-200/90 dark:border-[#133957] space-y-3.5">
                            <div class="flex items-center gap-2 text-xs font-black uppercase text-indigo-600 dark:text-indigo-400 tracking-wider">
                                <i class="fa-solid fa-earth-americas"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'التسلسل الجغرافي للمنطقة' : 'Territory Geographic Hierarchy' }}</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                        {{ app()->getLocale() === 'ar' ? 'الدولة' : 'Country' }} <span class="text-rose-500">*</span>
                                    </label>
                                    <select name="country_id" x-model="editBreak.country_id" @change="loadCities(editBreak.country_id, 'edit')" required class="form-select text-xs sm:text-sm w-full rounded-xl font-medium border border-slate-200 dark:border-[#1E4E73] bg-white dark:bg-[#031827] text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                                        <option value="">{{ app()->getLocale() === 'ar' ? '-- اختر الدولة --' : '-- Country --' }}</option>
                                        @foreach($countries as $c)
                                            <option value="{{ $c->id }}">{{ $c->flag_emoji }} {{ $c->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                        {{ app()->getLocale() === 'ar' ? 'المدينة / المحافظة' : 'City' }} <span class="text-rose-500">*</span>
                                    </label>
                                    <select name="city_id" x-model="editBreak.city_id" @change="loadAreas(editBreak.city_id, 'edit')" required class="form-select text-xs sm:text-sm w-full rounded-xl font-medium border border-slate-200 dark:border-[#1E4E73] bg-white dark:bg-[#031827] text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                                        <option value="">{{ app()->getLocale() === 'ar' ? '-- اختر المدينة --' : '-- City --' }}</option>
                                        <template x-for="city in editCities" :key="city.id">
                                            <option :value="city.id" :selected="city.id == editBreak.city_id" x-text="'{{ app()->getLocale() }}' === 'ar' ? (city.name_ar || city.name_en) : (city.name_en || city.name_ar)"></option>
                                        </template>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                        {{ app()->getLocale() === 'ar' ? 'المنطقة التابعة' : 'Parent Area' }} <span class="text-rose-500">*</span>
                                    </label>
                                    <select name="area_id" x-model="editBreak.area_id" required class="form-select text-xs sm:text-sm w-full rounded-xl font-semibold border border-slate-200 dark:border-[#1E4E73] bg-white dark:bg-[#031827] text-indigo-600 dark:text-indigo-400 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                                        <option value="">{{ app()->getLocale() === 'ar' ? '-- اختر المنطقة --' : '-- Area --' }}</option>
                                        <template x-for="area in editAreas" :key="area.id">
                                            <option :value="area.id" :selected="area.id == editBreak.area_id" x-text="'{{ app()->getLocale() }}' === 'ar' ? (area.name_ar || area.name_en) : (area.name_en || area.name_ar)"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Break Information -->
                        <div class="space-y-3.5">
                            <div class="flex items-center gap-2 text-xs font-black uppercase text-slate-400 dark:text-slate-400 tracking-wider">
                                <i class="fa-solid fa-network-wired"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'بيانات وتعريف البريك' : 'Break Identification' }}</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                        {{ app()->getLocale() === 'ar' ? 'اسم البريك (عربي)' : 'Break Name (Arabic)' }} <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="text" name="name_ar" x-model="editBreak.name_ar" required class="form-input text-xs sm:text-sm w-full rounded-xl font-medium border border-slate-200 dark:border-[#1E4E73] bg-white dark:bg-[#031827] text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none pr-9 rtl:pr-3 rtl:pl-9">
                                        <span class="absolute right-3 rtl:right-auto rtl:left-3 top-2.5 text-slate-400 dark:text-slate-500 text-xs font-bold pointer-events-none">AR</span>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                        {{ app()->getLocale() === 'ar' ? 'اسم البريك (إنجليزي)' : 'Break Name (English)' }} <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="text" name="name_en" x-model="editBreak.name_en" required class="form-input text-xs sm:text-sm w-full rounded-xl font-medium border border-slate-200 dark:border-[#1E4E73] bg-white dark:bg-[#031827] text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none pr-9 rtl:pr-3 rtl:pl-9">
                                        <span class="absolute right-3 rtl:right-auto rtl:left-3 top-2.5 text-slate-400 dark:text-slate-500 text-xs font-bold pointer-events-none">EN</span>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                        {{ app()->getLocale() === 'ar' ? 'كود البريك الفريد' : 'Break Code' }}
                                    </label>
                                    <div class="relative">
                                        <input type="text" name="code" x-model="editBreak.code" class="form-input text-xs sm:text-sm w-full rounded-xl font-mono uppercase border border-slate-200 dark:border-[#1E4E73] bg-white dark:bg-[#031827] text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none pl-8 rtl:pr-8 rtl:pl-3">
                                        <i class="fa-solid fa-hashtag absolute left-2.5 rtl:right-2.5 rtl:left-auto top-3 text-slate-400 dark:text-slate-500 text-xs pointer-events-none"></i>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                        {{ app()->getLocale() === 'ar' ? 'ترتيب الظهور' : 'Display Sort Order' }}
                                    </label>
                                    <input type="number" name="sort_order" x-model="editBreak.sort_order" min="0" class="form-input text-xs sm:text-sm w-full rounded-xl font-medium border border-slate-200 dark:border-[#1E4E73] bg-white dark:bg-[#031827] text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none">
                                </div>
                            </div>
                        </div>

                        <!-- 3. Activation Toggle Box -->
                        <div class="p-3.5 rounded-2xl bg-indigo-50/70 dark:bg-indigo-950/25 border border-indigo-200/80 dark:border-indigo-800/40 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-indigo-100 dark:bg-indigo-900/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-sm">
                                    <i class="fa-solid fa-toggle-on"></i>
                                </div>
                                <div>
                                    <span class="text-xs font-bold text-slate-900 dark:text-white block">
                                        {{ app()->getLocale() === 'ar' ? 'حالة نشاط البريك' : 'Break Operational Status' }}
                                    </span>
                                    <span class="text-[11px] text-slate-500 dark:text-slate-400">
                                        {{ app()->getLocale() === 'ar' ? 'البريك نشط ومتاح للتكليف وجدولة الزيارات' : 'Active and open for representative scheduling' }}
                                    </span>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" :checked="editBreak.is_active" class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-hidden rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-indigo-600"></div>
                            </label>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="border-t px-6 py-4 flex-shrink-0 bg-slate-50/90 dark:bg-[#051A2C] border-slate-100 dark:border-[#133957] flex items-center justify-end gap-3">
                        <button type="button" @click="showEditModal = false" class="btn btn-cancel text-xs sm:text-sm font-bold px-4 py-2.5 rounded-xl cursor-pointer">
                            {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                        </button>
                        <button type="submit" style="background: linear-gradient(135deg, #4338CA 0%, #6366F1 100%) !important;" class="btn text-xs sm:text-sm font-black px-6 py-2.5 rounded-xl text-white shadow-lg shadow-indigo-900/30 hover:shadow-indigo-800/50 hover:brightness-110 active:scale-95 transition-all flex items-center gap-2 cursor-pointer border-0">
                            <i class="fa-solid fa-floppy-disk text-white"></i>
                            <span class="text-white">{{ app()->getLocale() === 'ar' ? 'تحديث البريك' : 'Update Break' }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- DELETE CONFIRMATION MODAL -->
        <div x-show="showDeleteModal" x-cloak style="display: none;" 
            class="fixed inset-0 z-[1050] overflow-y-auto bg-slate-950/75 backdrop-blur-md p-3 sm:p-6 flex items-center justify-center transition-all duration-300"
            @click.self="showDeleteModal = false"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            
            <div class="max-w-md w-full shadow-2xl relative p-6 sm:p-8 border rounded-3xl text-center transition-all transform bg-white dark:bg-[#071F33] border-rose-200/90 dark:border-rose-900/50"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                
                <!-- Danger Icon -->
                <div style="background: linear-gradient(135deg, #B91C1C 0%, #EF4444 100%); color: #ffffff;" class="w-16 h-16 rounded-3xl flex items-center justify-center text-2xl mx-auto mb-4 shadow-xl shadow-rose-500/25 border-4 border-white dark:border-[#071F33]">
                    <i class="fa-solid fa-triangle-exclamation text-white"></i>
                </div>

                <h3 class="font-black text-lg text-slate-900 dark:text-white mb-1.5">
                    {{ app()->getLocale() === 'ar' ? 'تأكيد حذف البريك / القطاع' : 'Confirm Delete Break' }}
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-5">
                    {{ app()->getLocale() === 'ar' ? 'هل أنت متأكد من رغبتك في حذف هذا البريك نهائياً من النظام؟' : 'Are you sure you want to permanently remove this sector break?' }}
                </p>

                <!-- Item Target Preview Card -->
                <div class="p-3.5 rounded-2xl bg-rose-50/80 dark:bg-rose-950/30 border border-rose-200/80 dark:border-rose-900/40 text-rose-800 dark:text-rose-300 text-xs font-bold mb-6 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-network-wired text-rose-500"></i>
                    <span x-text="deleteTarget.name"></span>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-center gap-3">
                    <button type="button" @click="showDeleteModal = false" class="btn btn-cancel text-xs sm:text-sm font-bold px-5 py-2.5 rounded-xl cursor-pointer">
                        {{ app()->getLocale() === 'ar' ? 'إلغاء الأمر' : 'Cancel' }}
                    </button>
                    <form :action="`{{ url('/admin/mr/breaks') }}/${deleteTarget.id}`" method="POST" class="inline-block m-0">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger-action text-xs sm:text-sm font-black px-6 py-2.5 rounded-xl flex items-center gap-2 cursor-pointer border-0">
                            <i class="fa-solid fa-trash text-white"></i>
                            <span class="text-white">{{ app()->getLocale() === 'ar' ? 'نعم، حذف نهائي' : 'Yes, Delete' }}</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-layouts.admin>
