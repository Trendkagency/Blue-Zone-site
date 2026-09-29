<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'إضافة طبيب جديد' : 'Register Doctor / Clinic'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'تسجيل بيانات الطبيب، التخصص، التصنيف الفئوي، والإحداثيات الجغرافية لمطابقة الـ GPS' : 'Enter doctor profile, specialty, class, and clinic coordinates for GPS visit verification.'"
    :breadcrumbs="[
        (app()->getLocale() === 'ar' ? 'نظام المندوب الطبي' : 'MR CRM') => route('admin.mr.live-map'),
        (app()->getLocale() === 'ar' ? 'الأطباء والعيادات' : 'Doctors & Clinics') => route('admin.mr.contacts.index'),
        (app()->getLocale() === 'ar' ? 'إضافة جديد' : 'New Doctor') => route('admin.mr.contacts.create')
    ]"
>
    <div class="max-w-4xl">
        <form method="POST" action="{{ route('admin.mr.contacts.store') }}" class="card p-6 space-y-6">
            @csrf

            <!-- Section 1: Basic Profile -->
            <div>
                <h3 class="font-bold text-base text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-2 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-user-doctor text-sky-500"></i>
                    {{ app()->getLocale() === 'ar' ? 'البيانات المهنية والتعريفية' : 'Doctor & Professional Info' }}
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                            {{ app()->getLocale() === 'ar' ? 'اسم الطبيب بالكامل *' : 'Doctor Full Name *' }}
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="form-control text-sm" placeholder="e.g. Dr. Ahmed El-Sherif">
                        @error('name') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                            {{ app()->getLocale() === 'ar' ? 'كود الطبيب (اتركه فارغاً للتوليد التلقائي)' : 'Doctor Code (Leave blank to auto-generate)' }}
                        </label>
                        <input type="text" name="code" value="{{ old('code') }}" class="form-control text-sm" placeholder="e.g. DOC-101">
                        @error('code') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                            {{ app()->getLocale() === 'ar' ? 'التخصص الطبي *' : 'Medical Specialty *' }}
                        </label>
                        <select name="specialty_id" required class="form-select text-sm">
                            <option value="">{{ app()->getLocale() === 'ar' ? 'اختر التخصص' : 'Select Specialty' }}</option>
                            @foreach($specialties as $sp)
                                <option value="{{ $sp->id }}" {{ old('specialty_id') == $sp->id ? 'selected' : '' }}>
                                    {{ $sp->name }} ({{ $sp->code }})
                                </option>
                            @endforeach
                        </select>
                        @error('specialty_id') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                            {{ app()->getLocale() === 'ar' ? 'التصنيف الفئوي (A+/A/B/C) *' : 'Classification (A+/A/B/C) *' }}
                        </label>
                        <select name="classification_id" required class="form-select text-sm">
                            <option value="">{{ app()->getLocale() === 'ar' ? 'اختر الفئة' : 'Select Classification' }}</option>
                            @foreach($classifications as $cl)
                                <option value="{{ $cl->id }}" {{ old('classification_id') == $cl->id ? 'selected' : '' }}>
                                    Class {{ $cl->code }} — {{ $cl->label }} ({{ $cl->points }} pts, {{ $cl->required_visits }} visits/cycle)
                                </option>
                            @endforeach
                        </select>
                        @error('classification_id') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase">
                                {{ app()->getLocale() === 'ar' ? 'اسم المستشفى أو المركز الطبي أو العيادة' : 'Hospital / Medical Center / Clinic Name' }}
                            </label>
                            <button type="button" onclick="openDirectGoogleMapsSearch()" class="text-xs font-bold text-amber-600 dark:text-amber-400 hover:text-amber-500 flex items-center gap-1 transition">
                                <i class="fa-brands fa-google"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'فتح في خرائط Google للبحث ↗' : 'Search on Google Maps ↗' }}</span>
                            </button>
                        </div>
                        <div class="flex gap-2">
                            <input type="text" name="hospital_clinic_name" value="{{ old('hospital_clinic_name') }}" class="form-control text-sm flex-1" placeholder="e.g. Cairo Heart Institute, 4th Floor">
                            <button type="button" onclick="openDirectGoogleMapsSearch()" class="px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 active:scale-95 text-white font-bold text-xs transition flex items-center gap-1.5 shadow-sm shrink-0">
                                <i class="fa-brands fa-google"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'بحث خرائط Google' : 'Search Google Maps' }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Contact & Location -->
            <div>
                <h3 class="font-bold text-base text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-2 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-map-location-dot text-emerald-500"></i>
                    {{ app()->getLocale() === 'ar' ? 'الموقع الجغرافي وبيانات الاتصال' : 'Location & Contact Details' }}
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Interactive OpenMap & Google Maps Precision Address Picker -->
                    @include('admin.mr.contacts.partials.map-picker')

                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                            {{ app()->getLocale() === 'ar' ? 'رقم الهاتف' : 'Phone Number' }}
                        </label>
                        <input type="text" name="phone" value="{{ old('phone') }}" class="form-control text-sm" placeholder="e.g. +20 100 123 4567">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                            {{ app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Email Address' }}
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control text-sm" placeholder="doctor@clinic.com">
                    </div>

                    <!-- Territory Hierarchy Cascading Pickers (Country -> City -> Area -> Break) -->
                    <div class="md:col-span-2 p-4 rounded-xl border border-sky-100 dark:border-sky-900/60 bg-sky-50/40 dark:bg-sky-950/20"
                        x-data="{
                            countryId: '{{ old('country_id', '') }}',
                            cityId: '{{ old('city_id', '') }}',
                            areaId: '{{ old('area_id', '') }}',
                            breakId: '{{ old('break_id', '') }}',
                            cities: [],
                            areas: [],
                            breaks: [],
                            loadingCities: false,
                            loadingAreas: false,
                            loadingBreaks: false,

                            async onCountryChange() {
                                this.cityId = '';
                                this.areaId = '';
                                this.breakId = '';
                                this.cities = [];
                                this.areas = [];
                                this.breaks = [];
                                if (!this.countryId) return;

                                this.loadingCities = true;
                                try {
                                    const res = await fetch(`{{ url('/admin/api/countries') }}/${this.countryId}/cities`);
                                    const data = await res.json();
                                    this.cities = data.cities || [];
                                } catch (e) {
                                    console.error('Failed to load cities:', e);
                                } finally {
                                    this.loadingCities = false;
                                }
                            },

                            async onCityChange() {
                                this.areaId = '';
                                this.breakId = '';
                                this.areas = [];
                                this.breaks = [];
                                if (!this.cityId) return;

                                this.loadingAreas = true;
                                try {
                                    const res = await fetch(`{{ url('/admin/api/cities') }}/${this.cityId}/areas`);
                                    const data = await res.json();
                                    this.areas = data.areas || [];
                                } catch (e) {
                                    console.error('Failed to load areas:', e);
                                } finally {
                                    this.loadingAreas = false;
                                }
                            },

                            async onAreaChange() {
                                this.breakId = '';
                                this.breaks = [];
                                if (!this.areaId) return;

                                this.loadingBreaks = true;
                                try {
                                    const res = await fetch(`{{ url('/admin/api/areas') }}/${this.areaId}/breaks`);
                                    const data = await res.json();
                                    this.breaks = data.breaks || [];
                                } catch (e) {
                                    console.error('Failed to load breaks:', e);
                                } finally {
                                    this.loadingBreaks = false;
                                }
                            }
                        }"
                        x-init="if (countryId) onCountryChange()"
                    >
                        <div class="flex items-center gap-2 mb-3">
                            <i class="fa-solid fa-map-location-dot text-sky-600 text-sm"></i>
                            <h4 class="font-bold text-xs uppercase tracking-wider text-slate-800 dark:text-slate-200">
                                {{ app()->getLocale() === 'ar' ? 'التسلسل الجغرافي والبريك (الدولة › المدينة › المنطقة › البريك)' : 'Territory Hierarchy (Country › City › Area › Break)' }}
                            </h4>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                            <!-- 1. Country -->
                            <div>
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                                    {{ app()->getLocale() === 'ar' ? '1. الدولة' : '1. Country' }}
                                </label>
                                <select name="country_id" x-model="countryId" @change="onCountryChange()" class="form-select text-xs w-full rounded-lg">
                                    <option value="">{{ app()->getLocale() === 'ar' ? '-- اختر الدولة --' : '-- Select Country --' }}</option>
                                    @foreach($countries as $c)
                                        <option value="{{ $c->id }}" {{ old('country_id') == $c->id ? 'selected' : '' }}>
                                            {{ $c->flag_emoji }} {{ $c->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- 2. City -->
                            <div>
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1 flex items-center justify-between">
                                    <span>{{ app()->getLocale() === 'ar' ? '2. المدينة' : '2. City' }}</span>
                                    <span x-show="loadingCities" class="text-[10px] text-sky-500 font-normal"><i class="fa-solid fa-spinner fa-spin"></i></span>
                                </label>
                                <select name="city_id" x-model="cityId" @change="onCityChange()" :disabled="!countryId || loadingCities" class="form-select text-xs w-full rounded-lg disabled:opacity-50">
                                    <option value="">{{ app()->getLocale() === 'ar' ? '-- اختر المدينة --' : '-- Select City --' }}</option>
                                    <template x-for="city in cities" :key="city.id">
                                        <option :value="city.id" :selected="city.id == cityId" x-text="'{{ app()->getLocale() }}' === 'ar' ? (city.name_ar || city.name_en) : (city.name_en || city.name_ar)"></option>
                                    </template>
                                </select>
                            </div>

                            <!-- 3. Area -->
                            <div>
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1 flex items-center justify-between">
                                    <span>{{ app()->getLocale() === 'ar' ? '3. المنطقة / المربع' : '3. Area' }}</span>
                                    <span x-show="loadingAreas" class="text-[10px] text-sky-500 font-normal"><i class="fa-solid fa-spinner fa-spin"></i></span>
                                </label>
                                <select name="area_id" x-model="areaId" @change="onAreaChange()" :disabled="!cityId || loadingAreas" class="form-select text-xs w-full rounded-lg disabled:opacity-50">
                                    <option value="">{{ app()->getLocale() === 'ar' ? '-- اختر المنطقة --' : '-- Select Area --' }}</option>
                                    <template x-for="a in areas" :key="a.id">
                                        <option :value="a.id" :selected="a.id == areaId" x-text="'{{ app()->getLocale() }}' === 'ar' ? (a.name_ar || a.name_en) : (a.name_en || a.name_ar)"></option>
                                    </template>
                                </select>
                            </div>

                            <!-- 4. Break -->
                            <div>
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1 flex items-center justify-between">
                                    <span>{{ app()->getLocale() === 'ar' ? '4. البريك / القطاع' : '4. Break' }}</span>
                                    <span x-show="loadingBreaks" class="text-[10px] text-sky-500 font-normal"><i class="fa-solid fa-spinner fa-spin"></i></span>
                                </label>
                                <select name="break_id" x-model="breakId" :disabled="!areaId || loadingBreaks" class="form-select text-xs w-full rounded-lg disabled:opacity-50">
                                    <option value="">{{ app()->getLocale() === 'ar' ? '-- اختر البريك --' : '-- Select Break --' }}</option>
                                    <template x-for="b in breaks" :key="b.id">
                                        <option :value="b.id" :selected="b.id == breakId" x-text="'{{ app()->getLocale() }}' === 'ar' ? (b.name_ar || b.name_en) : (b.name_en || b.name_ar)"></option>
                                    </template>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                            {{ app()->getLocale() === 'ar' ? 'العنوان التفصيلي' : 'Detailed Address' }}
                        </label>
                        <textarea name="address" rows="2" class="form-control text-sm" placeholder="Building no, street name, landmarks...">{{ old('address') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                            {{ app()->getLocale() === 'ar' ? 'خط العرض (Latitude)' : 'GPS Latitude' }}
                        </label>
                        <input type="number" step="any" name="latitude" value="{{ old('latitude') }}" class="form-control text-sm font-mono" placeholder="e.g. 30.044420">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                            {{ app()->getLocale() === 'ar' ? 'خط الطول (Longitude)' : 'GPS Longitude' }}
                        </label>
                        <input type="number" step="any" name="longitude" value="{{ old('longitude') }}" class="form-control text-sm font-mono" placeholder="e.g. 31.235712">
                    </div>
                </div>
            </div>

            <!-- Notes & Status -->
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                        {{ app()->getLocale() === 'ar' ? 'ملاحظات وتوجيهات للمندوب' : 'Rep Visiting Notes & Directives' }}
                    </label>
                    <textarea name="notes" rows="3" class="form-control text-sm" placeholder="Best visit hours, receptionist preferences, specific products of interest...">{{ old('notes') }}</textarea>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" id="is_active" class="form-checkbox rounded text-sky-600" {{ old('is_active', true) ? 'checked' : '' }}>
                    <label for="is_active" class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                        {{ app()->getLocale() === 'ar' ? 'الطبيب نشط ومتاح للتكليف بالزيارات' : 'Active Doctor (Available for Cycle Assignments)' }}
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-800">
                <a href="{{ route('admin.mr.contacts.index') }}" class="btn btn-secondary text-sm">
                    {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                </a>
                <button type="submit" class="btn btn-primary font-bold text-sm shadow-sm">
                    <i class="fa-solid fa-floppy-disk mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'حفظ بيانات الطبيب' : 'Save Doctor Profile' }}
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>
