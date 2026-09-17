<x-layouts.admin>
    <x-slot:title>
        {{ app()->getLocale() === 'ar' ? 'إدارة النطاقات الجغرافية والدول والمدن' : 'Geographic Settings: Countries & Cities' }}
    </x-slot:title>

    <div class="space-y-8 pb-12" x-data="{
        activeTab: 'countries',
        showAddCountryModal: false,
        showEditCountryModal: false,
        editCountry: { id: null, name_en: '', name_ar: '', iso2: '', phone_code: '', currency_code: '', currency_symbol_en: '', currency_symbol_ar: '', flag_emoji: '', sort_order: 0, is_active: true },
        showAddCityModal: false,
        showEditCityModal: false,
        editCity: { id: null, country_id: '', name_en: '', name_ar: '', state_or_province: '', shipping_cost: 0, sort_order: 0, is_active: true }
    }">

        <!-- Breadcrumb & Top Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 flex items-center justify-center border border-indigo-500/20 shadow-sm shadow-indigo-500/5">
                    <i class="fa-solid fa-earth-americas text-xl"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">
                        <a href="{{ route('admin.settings.index') }}" class="hover:text-indigo-500 transition-colors">{{ __('admin.menu.settings') }}</a>
                        <span>/</span>
                        <span class="text-indigo-500">{{ app()->getLocale() === 'ar' ? 'النطاقات الجغرافية' : 'Geographic Hierarchy' }}</span>
                    </div>
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">
                        {{ app()->getLocale() === 'ar' ? 'إدارة الدول والمدن والمواقع الجغرافية' : 'Country, City & Regional Geo Hub' }}
                    </h1>
                </div>
            </div>

            <!-- Header Quick Action Buttons -->
            <div class="flex items-center gap-3">
                <a href="{{ Route::has('admin.locations.index') ? route('admin.locations.index') : (Route::has('admin.warehouses.index') ? route('admin.warehouses.index') : url('/admin/locations')) }}" class="px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/80 font-semibold text-sm transition-all flex items-center gap-2">
                    <i class="fa-solid fa-building-columns text-indigo-500"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'عرض المستودعات والمواقع' : 'View Storage Hubs' }}</span>
                </a>
                <button @click="showAddCountryModal = true" class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm shadow-md shadow-indigo-600/20 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'إضافة دولة جديدة' : 'Add Country' }}</span>
                </button>
                <button @click="showAddCityModal = true" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm shadow-md shadow-emerald-600/20 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'إضافة مدينة جديدة' : 'Add City' }}</span>
                </button>
            </div>
        </div>

        <!-- Feedback Messages -->
        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-sm flex items-center justify-between animate-fadeIn">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-lg"></i>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700"><i class="fa-solid fa-xmark"></i></button>
            </div>
        @endif

        @if(session('status'))
            <div class="p-4 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-600 dark:text-indigo-400 text-sm flex items-center justify-between animate-fadeIn">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-circle-info text-lg"></i>
                    <span class="font-medium">{{ session('status') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-indigo-500 hover:text-indigo-700"><i class="fa-solid fa-xmark"></i></button>
            </div>
        @endif

        @if($errors->has('delete_error'))
            <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-600 dark:text-rose-400 text-sm flex items-center justify-between animate-fadeIn">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                    <span class="font-medium">{{ $errors->first('delete_error') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700"><i class="fa-solid fa-xmark"></i></button>
            </div>
        @endif

        <!-- KPI Metrics Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- 1. Active Countries -->
            <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1">
                        {{ app()->getLocale() === 'ar' ? 'الدول النشطة' : 'Active Countries' }}
                    </p>
                    <h3 class="text-2xl font-black text-slate-900 dark:text-white">{{ $activeCountriesCount }} <span class="text-xs text-slate-400 font-normal">/ {{ count($countries) }}</span></h3>
                    <p class="text-xs text-emerald-500 mt-1 flex items-center gap-1 font-medium">
                        <i class="fa-solid fa-globe text-[10px]"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'تغطية إقليمية كاملة' : 'Global Dial Routing' }}</span>
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 dark:bg-indigo-500/20 text-indigo-500 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-flag"></i>
                </div>
            </div>

            <!-- 2. Registered Cities -->
            <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1">
                        {{ app()->getLocale() === 'ar' ? 'إجمالي المدن' : 'Configured Cities' }}
                    </p>
                    <h3 class="text-2xl font-black text-slate-900 dark:text-white">{{ $totalCitiesCount }}</h3>
                    <p class="text-xs text-indigo-500 mt-1 flex items-center gap-1 font-medium">
                        <i class="fa-solid fa-truck text-[10px]"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'شحن ومواقع مخصصة' : 'Delivery & Facility Zones' }}</span>
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-500 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-city"></i>
                </div>
            </div>

            <!-- 3. Physical Storage Facilities -->
            <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1">
                        {{ app()->getLocale() === 'ar' ? 'المستودعات والمواقع' : 'Hubs & Warehouses' }}
                    </p>
                    <h3 class="text-2xl font-black text-slate-900 dark:text-white">{{ $totalLocations }}</h3>
                    <p class="text-xs text-sky-500 mt-1 flex items-center gap-1 font-medium">
                        <i class="fa-solid fa-link text-[10px]"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'مرتبطة بالدول والمدن' : 'Linked To Geographies' }}</span>
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-sky-500/10 dark:bg-sky-500/20 text-sky-500 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
            </div>

            <!-- 4. Phone Dynamic Logic Info -->
            <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1">
                        {{ app()->getLocale() === 'ar' ? 'أكواد الاتصال والمفاتيح' : 'Phone Dial System' }}
                    </p>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">{{ app()->getLocale() === 'ar' ? 'تزامن تلقائي' : 'Auto-Sync' }}</h3>
                    <p class="text-xs text-amber-500 mt-1 flex items-center gap-1 font-medium">
                        <i class="fa-solid fa-phone-volume text-[10px]"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'حسب الدولة المختارة' : 'Country-Driven Prefix' }}</span>
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-500/10 dark:bg-amber-500/20 text-amber-500 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-phone"></i>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs Container -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-5">
                <div class="flex items-center gap-2 p-1 bg-slate-100 dark:bg-slate-800/80 rounded-2xl w-fit">
                    <button @click="activeTab = 'countries'" :class="activeTab === 'countries' ? 'bg-white dark:bg-slate-900 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'" class="px-5 py-2.5 rounded-xl font-bold text-sm transition-all flex items-center gap-2">
                        <i class="fa-solid fa-flag"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'الدول وأكواد الاتصال' : 'Countries & Dial Codes' }} ({{ count($countries) }})</span>
                    </button>
                    <button @click="activeTab = 'cities'" :class="activeTab === 'cities' ? 'bg-white dark:bg-slate-900 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'" class="px-5 py-2.5 rounded-xl font-bold text-sm transition-all flex items-center gap-2">
                        <i class="fa-solid fa-city"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'المدن والمناطق' : 'Cities & Shipping Zones' }} ({{ $totalCitiesCount }})</span>
                    </button>
                </div>

                <!-- Tab Quick Info -->
                <div class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-2">
                    <i class="fa-solid fa-shield-halved text-indigo-500"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'التغييرات تنعكس فورياً في نماذج إضافة المستودعات وطلبات البيع والـ POS' : 'Real-time sync across Location Forms, POS & Checkout' }}</span>
                </div>
            </div>

            <!-- TAB 1: COUNTRIES TABLE -->
            <div x-show="activeTab === 'countries'" class="mt-6 space-y-4">
                <div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-800">
                    <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                        <thead class="bg-slate-50 dark:bg-slate-800/60 text-xs uppercase font-bold text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                            <tr>
                                <th scope="col" class="px-5 py-4">{{ app()->getLocale() === 'ar' ? 'الدولة' : 'Country' }}</th>
                                <th scope="col" class="px-5 py-4">{{ app()->getLocale() === 'ar' ? 'رمز ISO' : 'ISO Code' }}</th>
                                <th scope="col" class="px-5 py-4">{{ app()->getLocale() === 'ar' ? 'مفتاح الاتصال' : 'Phone Dial Code' }}</th>
                                <th scope="col" class="px-5 py-4">{{ app()->getLocale() === 'ar' ? 'العملة' : 'Currency' }}</th>
                                <th scope="col" class="px-5 py-4">{{ app()->getLocale() === 'ar' ? 'المدن التابعة' : 'Cities' }}</th>
                                <th scope="col" class="px-5 py-4">{{ app()->getLocale() === 'ar' ? 'المستودعات' : 'Warehouses' }}</th>
                                <th scope="col" class="px-5 py-4">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                                <th scope="col" class="px-5 py-4 text-center">{{ app()->getLocale() === 'ar' ? 'إجراءات' : 'Actions' }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800 font-medium">
                            @forelse($countries as $country)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-colors">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <span class="text-2xl leading-none">{{ $country->flag_emoji ?: '🌐' }}</span>
                                            <div>
                                                <div class="font-bold text-slate-900 dark:text-white">{{ $country->name_en }}</div>
                                                <div class="text-xs text-slate-400">{{ $country->name_ar }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 font-mono text-xs font-bold border border-slate-200 dark:border-slate-700">
                                            {{ $country->iso2 }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 font-mono font-bold text-xs border border-indigo-200 dark:border-indigo-500/20" dir="ltr">
                                            <i class="fa-solid fa-phone text-[10px]"></i>
                                            <span>{{ $country->phone_code }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">
                                            {{ $country->currency_code ?: '—' }} ({{ $country->currency_symbol ?: '—' }})
                                        </span>
                                    </td>
                                    <td class="px-5 py-4">
                                        <button @click="activeTab = 'cities'; window.location.href='{{ route('admin.settings.geo.index', ['country_id' => $country->id]) }}'" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 text-xs font-bold hover:underline">
                                            <i class="fa-solid fa-city text-[10px]"></i>
                                            <span>{{ $country->cities_count }} {{ app()->getLocale() === 'ar' ? 'مدينة' : 'cities' }}</span>
                                        </button>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-sky-50 dark:bg-sky-500/10 text-sky-600 dark:text-sky-400 text-xs font-bold">
                                            <i class="fa-solid fa-building-columns text-[10px]"></i>
                                            <span>{{ $country->locations_count }} {{ app()->getLocale() === 'ar' ? 'موقع' : 'hubs' }}</span>
                                        </span>
                                    </td>
                                    <td class="px-5 py-4">
                                        <form action="{{ route('admin.countries.toggle-status', $country->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold transition-all {{ $country->is_active ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 hover:bg-emerald-500/20' : 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20 hover:bg-rose-500/20' }}">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $country->is_active ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                                <span>{{ $country->is_active ? (app()->getLocale() === 'ar' ? 'نشط' : 'Active') : (app()->getLocale() === 'ar' ? 'معطل' : 'Inactive') }}</span>
                                            </button>
                                        </form>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button @click="
                                                editCountry = {
                                                    id: {{ $country->id }},
                                                    name_en: '{{ addslashes($country->name_en) }}',
                                                    name_ar: '{{ addslashes($country->name_ar) }}',
                                                    iso2: '{{ $country->iso2 }}',
                                                    phone_code: '{{ $country->phone_code }}',
                                                    currency_code: '{{ $country->currency_code }}',
                                                    currency_symbol_en: '{{ addslashes($country->currency_symbol_en ?? '') }}',
                                                    currency_symbol_ar: '{{ addslashes($country->currency_symbol_ar ?? '') }}',
                                                    flag_emoji: '{{ $country->flag_emoji }}',
                                                    sort_order: {{ $country->sort_order ?? 0 }},
                                                    is_active: {{ $country->is_active ? 'true' : 'false' }}
                                                };
                                                showEditCountryModal = true;
                                            " class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-indigo-50 dark:hover:bg-indigo-500/20 text-slate-600 dark:text-slate-400 hover:text-indigo-600 transition-colors flex items-center justify-center text-xs" title="Edit">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>

                                            @if($country->locations_count == 0)
                                                <form action="{{ route('admin.countries.destroy', $country->id) }}" method="POST" onsubmit="return confirm('{{ app()->getLocale() === 'ar' ? 'هل أنت متأكد من حذف هذه الدولة وجميع مدنها المرتبطة؟' : 'Are you sure you want to delete this country and all linked cities?' }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-rose-50 dark:hover:bg-rose-500/20 text-slate-600 dark:text-slate-400 hover:text-rose-600 transition-colors flex items-center justify-center text-xs" title="Delete">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-10 text-slate-400">
                                        {{ app()->getLocale() === 'ar' ? 'لا توجد دول مسجلة حالياً' : 'No countries found.' }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 2: CITIES TABLE -->
            <div x-show="activeTab === 'cities'" class="mt-6 space-y-4" style="display: none;">
                <!-- Filter Bar -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-800">
                    <form method="GET" action="{{ route('admin.settings.geo.index') }}" class="flex items-center gap-3 w-full sm:w-auto">
                        <label class="text-xs font-bold text-slate-500 uppercase">{{ app()->getLocale() === 'ar' ? 'تصفية حسب الدولة:' : 'Filter Country:' }}</label>
                        <select name="country_id" onchange="this.form.submit()" class="px-4 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-sm font-semibold text-slate-800 dark:text-slate-200">
                            <option value="">{{ app()->getLocale() === 'ar' ? '— جميع الدول —' : '— All Countries —' }}</option>
                            @foreach($countries as $c)
                                <option value="{{ $c->id }}" {{ ($selectedCountryId == $c->id) ? 'selected' : '' }}>
                                    {{ $c->flag_emoji }} {{ $c->name_en }} ({{ $c->name_ar }})
                                </option>
                            @endforeach
                        </select>
                        @if($selectedCountryId)
                            <a href="{{ route('admin.settings.geo.index') }}" class="text-xs text-rose-500 hover:underline font-semibold">{{ app()->getLocale() === 'ar' ? 'إلغاء التصفية' : 'Clear' }}</a>
                        @endif
                    </form>
                    <button @click="showAddCityModal = true" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs transition-all flex items-center gap-1.5 self-end sm:self-auto">
                        <i class="fa-solid fa-plus"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'إضافة مدينة' : 'Add City' }}</span>
                    </button>
                </div>

                <div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-800">
                    <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                        <thead class="bg-slate-50 dark:bg-slate-800/60 text-xs uppercase font-bold text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800">
                            <tr>
                                <th scope="col" class="px-5 py-4">{{ app()->getLocale() === 'ar' ? 'المدينة' : 'City' }}</th>
                                <th scope="col" class="px-5 py-4">{{ app()->getLocale() === 'ar' ? 'الدولة التابعة' : 'Country' }}</th>
                                <th scope="col" class="px-5 py-4">{{ app()->getLocale() === 'ar' ? 'تكلفة الشحن الافتراضية' : 'Default Shipping Cost' }}</th>
                                <th scope="col" class="px-5 py-4">{{ app()->getLocale() === 'ar' ? 'المستودعات' : 'Warehouses' }}</th>
                                <th scope="col" class="px-5 py-4">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                                <th scope="col" class="px-5 py-4 text-center">{{ app()->getLocale() === 'ar' ? 'إجراءات' : 'Actions' }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800 font-medium">
                            @forelse($cities as $city)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-colors">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 text-indigo-500 flex items-center justify-center text-xs">
                                                <i class="fa-solid fa-city"></i>
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-900 dark:text-white">{{ $city->name_en }}</div>
                                                <div class="text-xs text-slate-400">{{ $city->name_ar }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-2">
                                            <span>{{ $city->country?->flag_emoji ?: '🌐' }}</span>
                                            <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $city->country?->name_en ?: '—' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="font-mono font-bold text-xs text-emerald-600 dark:text-emerald-400">
                                            {{ number_format($city->shipping_cost, 2) }} {{ $city->country?->currency_symbol ?: 'SAR' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-sky-50 dark:bg-sky-500/10 text-sky-600 dark:text-sky-400 text-xs font-bold">
                                            <i class="fa-solid fa-building-columns text-[10px]"></i>
                                            <span>{{ $city->locations_count }} {{ app()->getLocale() === 'ar' ? 'موقع' : 'hubs' }}</span>
                                        </span>
                                    </td>
                                    <td class="px-5 py-4">
                                        <form action="{{ route('admin.cities.toggle-status', $city->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold transition-all {{ $city->is_active ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 hover:bg-emerald-500/20' : 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20 hover:bg-rose-500/20' }}">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $city->is_active ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                                <span>{{ $city->is_active ? (app()->getLocale() === 'ar' ? 'نشط' : 'Active') : (app()->getLocale() === 'ar' ? 'معطل' : 'Inactive') }}</span>
                                            </button>
                                        </form>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button @click="
                                                editCity = {
                                                    id: {{ $city->id }},
                                                    country_id: '{{ $city->country_id }}',
                                                    name_en: '{{ addslashes($city->name_en) }}',
                                                    name_ar: '{{ addslashes($city->name_ar) }}',
                                                    state_or_province: '{{ addslashes($city->state_or_province ?? '') }}',
                                                    shipping_cost: '{{ $city->shipping_cost }}',
                                                    sort_order: {{ $city->sort_order ?? 0 }},
                                                    is_active: {{ $city->is_active ? 'true' : 'false' }}
                                                };
                                                showEditCityModal = true;
                                            " class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-emerald-500/20 text-slate-600 dark:text-slate-400 hover:text-emerald-600 transition-colors flex items-center justify-center text-xs" title="Edit">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>

                                            @if($city->locations_count == 0)
                                                <form action="{{ route('admin.cities.destroy', $city->id) }}" method="POST" onsubmit="return confirm('{{ app()->getLocale() === 'ar' ? 'هل أنت متأكد من حذف هذه المدينة؟' : 'Are you sure you want to delete this city?' }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-rose-50 dark:hover:bg-rose-500/20 text-slate-600 dark:text-slate-400 hover:text-rose-600 transition-colors flex items-center justify-center text-xs" title="Delete">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-10 text-slate-400">
                                        {{ app()->getLocale() === 'ar' ? 'لا توجد مدن مسجلة تطابق التصفية' : 'No cities found for the current selection.' }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($cities->hasPages())
                    <div class="mt-4">
                        {{ $cities->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- ================= MODAL: ADD COUNTRY ================= -->
        <div x-show="showAddCountryModal" class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-950/75 backdrop-blur-md" style="display: none;">
            <div @click.away="showAddCountryModal = false" class="bg-white dark:bg-slate-900 rounded-3xl max-w-xl w-full p-6 sm:p-8 border border-slate-200 dark:border-slate-800 shadow-2xl relative max-h-[90vh] overflow-y-auto animate-fadeIn" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
                <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-4 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-600 flex items-center justify-center text-lg">
                            <i class="fa-solid fa-flag"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ app()->getLocale() === 'ar' ? 'إضافة دولة جديدة' : 'Add New Country' }}</h3>
                            <p class="text-xs text-slate-400">{{ app()->getLocale() === 'ar' ? 'تحديد كود الدولة ومفتاح الاتصال والعملة' : 'Configure ISO code, phone dial prefix, and currency' }}</p>
                        </div>
                    </div>
                    <button @click="showAddCountryModal = false" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-slate-800 dark:hover:text-white flex items-center justify-center"><i class="fa-solid fa-xmark"></i></button>
                </div>

                <form action="{{ route('admin.countries.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase">{{ app()->getLocale() === 'ar' ? 'اسم الدولة (EN)' : 'Country Name (EN)' }} *</label>
                            <input type="text" name="name_en" required placeholder="e.g. Saudi Arabia" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm font-semibold text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500" dir="ltr">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase">{{ app()->getLocale() === 'ar' ? 'اسم الدولة (AR)' : 'Country Name (AR)' }} *</label>
                            <input type="text" name="name_ar" required placeholder="مثال: المملكة العربية السعودية" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm font-semibold text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500" dir="rtl">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase">{{ app()->getLocale() === 'ar' ? 'كود ISO2' : 'ISO2 Code' }} *</label>
                            <input type="text" name="iso2" maxlength="2" required placeholder="SA" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm font-mono font-bold text-slate-900 dark:text-white uppercase focus:ring-2 focus:ring-indigo-500" dir="ltr">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase">{{ app()->getLocale() === 'ar' ? 'مفتاح الاتصال' : 'Phone Dial Code' }} *</label>
                            <input type="text" name="phone_code" required placeholder="+966" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm font-mono font-bold text-indigo-600 dark:text-indigo-400 focus:ring-2 focus:ring-indigo-500" dir="ltr">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase">{{ app()->getLocale() === 'ar' ? 'رمز العلم' : 'Flag Emoji' }}</label>
                            <input type="text" name="flag_emoji" placeholder="🇸🇦" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm text-center font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase">{{ app()->getLocale() === 'ar' ? 'رمز العملة' : 'Currency Code' }}</label>
                            <input type="text" name="currency_code" placeholder="SAR" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm font-semibold uppercase text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500" dir="ltr">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase">{{ app()->getLocale() === 'ar' ? 'رمز العملة EN' : 'Symbol EN' }}</label>
                            <input type="text" name="currency_symbol_en" placeholder="SAR" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm font-semibold text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500" dir="ltr">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase">{{ app()->getLocale() === 'ar' ? 'رمز العملة AR' : 'Symbol AR' }}</label>
                            <input type="text" name="currency_symbol_ar" placeholder="ر.س" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm font-semibold text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500" dir="rtl">
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end gap-3">
                        <button type="button" @click="showAddCountryModal = false" class="px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-sm font-semibold">{{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}</button>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold shadow-md shadow-indigo-600/20">{{ app()->getLocale() === 'ar' ? 'حفظ الدولة' : 'Save Country' }}</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ================= MODAL: EDIT COUNTRY ================= -->
        <div x-show="showEditCountryModal" class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-950/75 backdrop-blur-md" style="display: none;">
            <div @click.away="showEditCountryModal = false" class="bg-white dark:bg-slate-900 rounded-3xl max-w-xl w-full p-6 sm:p-8 border border-slate-200 dark:border-slate-800 shadow-2xl relative max-h-[90vh] overflow-y-auto animate-fadeIn" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
                <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-4 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-600 flex items-center justify-center text-lg">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ app()->getLocale() === 'ar' ? 'تعديل بيانات الدولة' : 'Edit Country' }}</h3>
                        </div>
                    </div>
                    <button @click="showEditCountryModal = false" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-slate-800 dark:hover:text-white flex items-center justify-center"><i class="fa-solid fa-xmark"></i></button>
                </div>

                <form :action="'{{ url('admin/countries') }}/' + editCountry.id" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase">{{ app()->getLocale() === 'ar' ? 'اسم الدولة (EN)' : 'Country Name (EN)' }} *</label>
                            <input type="text" name="name_en" x-model="editCountry.name_en" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm font-semibold text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500" dir="ltr">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase">{{ app()->getLocale() === 'ar' ? 'اسم الدولة (AR)' : 'Country Name (AR)' }} *</label>
                            <input type="text" name="name_ar" x-model="editCountry.name_ar" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm font-semibold text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500" dir="rtl">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase">{{ app()->getLocale() === 'ar' ? 'كود ISO2' : 'ISO2 Code' }} *</label>
                            <input type="text" name="iso2" x-model="editCountry.iso2" maxlength="2" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm font-mono font-bold text-slate-900 dark:text-white uppercase focus:ring-2 focus:ring-indigo-500" dir="ltr">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase">{{ app()->getLocale() === 'ar' ? 'مفتاح الاتصال' : 'Phone Dial Code' }} *</label>
                            <input type="text" name="phone_code" x-model="editCountry.phone_code" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm font-mono font-bold text-indigo-600 dark:text-indigo-400 focus:ring-2 focus:ring-indigo-500" dir="ltr">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase">{{ app()->getLocale() === 'ar' ? 'رمز العلم' : 'Flag Emoji' }}</label>
                            <input type="text" name="flag_emoji" x-model="editCountry.flag_emoji" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm text-center font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase">{{ app()->getLocale() === 'ar' ? 'رمز العملة' : 'Currency Code' }}</label>
                            <input type="text" name="currency_code" x-model="editCountry.currency_code" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm font-semibold uppercase text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500" dir="ltr">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase">{{ app()->getLocale() === 'ar' ? 'رمز العملة EN' : 'Symbol EN' }}</label>
                            <input type="text" name="currency_symbol_en" x-model="editCountry.currency_symbol_en" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm font-semibold text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500" dir="ltr">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase">{{ app()->getLocale() === 'ar' ? 'رمز العملة AR' : 'Symbol AR' }}</label>
                            <input type="text" name="currency_symbol_ar" x-model="editCountry.currency_symbol_ar" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm font-semibold text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500" dir="rtl">
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end gap-3">
                        <button type="button" @click="showEditCountryModal = false" class="px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-sm font-semibold">{{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}</button>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold shadow-md shadow-indigo-600/20">{{ app()->getLocale() === 'ar' ? 'حفظ التعديلات' : 'Update Country' }}</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ================= MODAL: ADD CITY ================= -->
        <div x-show="showAddCityModal" class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-950/75 backdrop-blur-md" style="display: none;">
            <div @click.away="showAddCityModal = false" class="bg-white dark:bg-slate-900 rounded-3xl max-w-lg w-full p-6 sm:p-8 border border-slate-200 dark:border-slate-800 shadow-2xl relative max-h-[90vh] overflow-y-auto animate-fadeIn" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
                <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-4 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center text-lg">
                            <i class="fa-solid fa-city"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ app()->getLocale() === 'ar' ? 'إضافة مدينة جديدة' : 'Add New City' }}</h3>
                            <p class="text-xs text-slate-400">{{ app()->getLocale() === 'ar' ? 'ربط المدينة بالدولة وتحديد تكلفة الشحن' : 'Link city to country & set default delivery rate' }}</p>
                        </div>
                    </div>
                    <button @click="showAddCityModal = false" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-slate-800 dark:hover:text-white flex items-center justify-center"><i class="fa-solid fa-xmark"></i></button>
                </div>

                <form action="{{ route('admin.cities.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase">{{ app()->getLocale() === 'ar' ? 'الدولة التابعة' : 'Parent Country' }} *</label>
                        <select name="country_id" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm font-semibold text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500">
                            <option value="">{{ app()->getLocale() === 'ar' ? 'اختر الدولة...' : 'Select Country...' }}</option>
                            @foreach($countries as $c)
                                <option value="{{ $c->id }}" {{ ($selectedCountryId == $c->id) ? 'selected' : '' }}>
                                    {{ $c->flag_emoji }} {{ $c->name_en }} ({{ $c->name_ar }}) - [{{ $c->phone_code }}]
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase">{{ app()->getLocale() === 'ar' ? 'اسم المدينة (EN)' : 'City Name (EN)' }} *</label>
                            <input type="text" name="name_en" required placeholder="e.g. Riyadh" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm font-semibold text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500" dir="ltr">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase">{{ app()->getLocale() === 'ar' ? 'اسم المدينة (AR)' : 'City Name (AR)' }} *</label>
                            <input type="text" name="name_ar" required placeholder="مثال: الرياض" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm font-semibold text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500" dir="rtl">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase">{{ app()->getLocale() === 'ar' ? 'تكلفة الشحن الافتراضية' : 'Default Shipping Cost' }}</label>
                        <input type="number" step="0.01" name="shipping_cost" value="0.00" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm font-semibold text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500" dir="ltr">
                    </div>

                    <div class="pt-4 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end gap-3">
                        <button type="button" @click="showAddCityModal = false" class="px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-sm font-semibold">{{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}</button>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold shadow-md shadow-emerald-600/20">{{ app()->getLocale() === 'ar' ? 'حفظ المدينة' : 'Save City' }}</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ================= MODAL: EDIT CITY ================= -->
        <div x-show="showEditCityModal" class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-950/75 backdrop-blur-md" style="display: none;">
            <div @click.away="showEditCityModal = false" class="bg-white dark:bg-slate-900 rounded-3xl max-w-lg w-full p-6 sm:p-8 border border-slate-200 dark:border-slate-800 shadow-2xl relative max-h-[90vh] overflow-y-auto animate-fadeIn" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
                <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-4 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center text-lg">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ app()->getLocale() === 'ar' ? 'تعديل بيانات المدينة' : 'Edit City' }}</h3>
                        </div>
                    </div>
                    <button @click="showEditCityModal = false" class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-slate-800 dark:hover:text-white flex items-center justify-center"><i class="fa-solid fa-xmark"></i></button>
                </div>

                <form :action="'{{ url('admin/cities') }}/' + editCity.id" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase">{{ app()->getLocale() === 'ar' ? 'الدولة التابعة' : 'Parent Country' }} *</label>
                        <select name="country_id" x-model="editCity.country_id" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm font-semibold text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500">
                            @foreach($countries as $c)
                                <option value="{{ $c->id }}">
                                    {{ $c->flag_emoji }} {{ $c->name_en }} ({{ $c->name_ar }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase">{{ app()->getLocale() === 'ar' ? 'اسم المدينة (EN)' : 'City Name (EN)' }} *</label>
                            <input type="text" name="name_en" x-model="editCity.name_en" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm font-semibold text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500" dir="ltr">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase">{{ app()->getLocale() === 'ar' ? 'اسم المدينة (AR)' : 'City Name (AR)' }} *</label>
                            <input type="text" name="name_ar" x-model="editCity.name_ar" required class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm font-semibold text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500" dir="rtl">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 uppercase">{{ app()->getLocale() === 'ar' ? 'تكلفة الشحن الافتراضية' : 'Default Shipping Cost' }}</label>
                        <input type="number" step="0.01" name="shipping_cost" x-model="editCity.shipping_cost" class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm font-semibold text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500" dir="ltr">
                    </div>

                    <div class="pt-4 border-t border-slate-200 dark:border-slate-800 flex items-center justify-end gap-3">
                        <button type="button" @click="showEditCityModal = false" class="px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-sm font-semibold">{{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}</button>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold shadow-md shadow-emerald-600/20">{{ app()->getLocale() === 'ar' ? 'حفظ التعديلات' : 'Update City' }}</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-layouts.admin>
