<x-layouts.admin 
    :pageTitle="__('admin.users.edit_title', ['name' => $user['name']])" 
    :pageSubtitle="__('admin.users.edit_subtitle')"
    :breadcrumbs="[__('admin.menu.users') => route('admin.users.index'), $user['name'] => route('admin.users.show', $user['id']), __('app.actions.edit') => route('admin.users.edit', $user['id'])]"
>
    <form method="POST" action="{{ route('admin.users.update', $user['id']) }}">
        @csrf
        @method('PUT')

        <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-bottom: 1.5rem;">
            <a href="{{ route('admin.users.show', $user['id']) }}" class="btn btn-secondary font-bold">
                <i class="fa-solid fa-eye mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'عرض الملف' : 'View Profile' }}
            </a>
            <button type="submit" class="btn btn-primary font-bold shadow-sm">
                <i class="fa-solid fa-floppy-disk mr-1.5 ml-1.5"></i> {{ __('app.actions.save') }}
            </button>
        </div>

        <div class="card" style="padding: 2rem; max-width: 800px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <x-forms.input name="name" :label="__('admin.users.name')" :value="old('name', $user['name'])" required />
                <x-forms.input name="email" type="email" :label="__('admin.users.email')" :value="old('email', $user['email'])" required />
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <x-forms.input name="phone" :label="app()->getLocale() === 'ar' ? 'رقم الهاتف' : 'Phone Number'" placeholder="+966 50 123 4567" :value="old('phone', $user['phone'] ?? '')" />

                <div class="form-group mb-4">
                    <label class="form-label font-bold text-sm mb-1.5 block">{{ __('admin.users.role') }}</label>
                    <select name="role_id" class="form-select text-sm w-full">
                        <option value="">{{ app()->getLocale() === 'ar' ? '-- حدد الدور الأمني --' : '-- Select Security Role --' }}</option>
                        @foreach($roles as $r)
                            <option value="{{ $r['id'] ?? $r->id }}" {{ old('role_id', $user['role_id'] ?? '') == ($r['id'] ?? $r->id) ? 'selected' : '' }}>
                                {{ $r['name'] ?? $r->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem;">
                <div class="form-group mb-4">
                    <label class="form-label font-bold text-sm mb-1.5 block">{{ __('admin.users.status') }}</label>
                    <select name="status" class="form-select text-sm w-full">
                        <option value="active" {{ old('status', $user['status']) === 'active' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'نشط ومصرح (Active)' : 'Active' }}</option>
                        <option value="suspended" {{ old('status', $user['status']) === 'suspended' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'معلق وموقوف (Suspended)' : 'Suspended' }}</option>
                        <option value="inactive" {{ old('status', $user['status']) === 'inactive' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'غير نشط (Inactive)' : 'Inactive' }}</option>
                    </select>
                </div>
            </div>

            <!-- Territory & Operational Area Section (Country -> City -> Area) -->
            <div class="p-4 rounded-xl border border-sky-100 dark:border-sky-900/60 bg-sky-50/40 dark:bg-sky-950/20 mb-5"
                x-data="{
                    countryId: '{{ old('country_id', $user['country_id'] ?? '') }}',
                    cityId: '{{ old('city_id', $user['city_id'] ?? '') }}',
                    areaId: '{{ old('area_id', $user['area_id'] ?? '') }}',
                    cities: {{ Js::from($cities->map(fn($c) => ['id' => $c->id, 'name_en' => $c->name_en, 'name_ar' => $c->name_ar])) }},
                    areas: {{ Js::from($areas->map(fn($a) => ['id' => $a->id, 'name_en' => $a->name_en, 'name_ar' => $a->name_ar])) }},
                    loadingCities: false,
                    loadingAreas: false,

                    async onCountryChange() {
                        this.cityId = '';
                        this.areaId = '';
                        this.cities = [];
                        this.areas = [];
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
                        this.areas = [];
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
                    }
                }"
            >
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-7 h-7 rounded-lg bg-sky-100 dark:bg-sky-900/60 text-sky-600 dark:text-sky-400 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-map-location-dot"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-xs uppercase tracking-wider text-slate-800 dark:text-slate-200">
                            {{ app()->getLocale() === 'ar' ? 'النطاق الجغرافي والمربع الميداني (MR Territory & Area)' : 'Field Territory & Operating Area (MR)' }}
                        </h4>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">
                            {{ app()->getLocale() === 'ar' ? 'تعديل أو تعيين النطاق والمربع الجغرافي المخصص للمندوب الطبي' : 'Update assigned territory and operating area for this user' }}
                        </p>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                    <!-- 1. Country -->
                    <div class="form-group">
                        <label class="form-label font-bold text-xs mb-1 block text-slate-700 dark:text-slate-300">
                            {{ app()->getLocale() === 'ar' ? '1. الدولة' : '1. Country' }}
                        </label>
                        <select name="country_id" x-model="countryId" @change="onCountryChange()" class="form-select text-xs w-full rounded-lg">
                            <option value="">{{ app()->getLocale() === 'ar' ? '-- اختر الدولة --' : '-- Select Country --' }}</option>
                            @foreach($countries as $c)
                                <option value="{{ $c->id }}" {{ old('country_id', $user['country_id'] ?? '') == $c->id ? 'selected' : '' }}>
                                    {{ $c->flag_emoji }} {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 2. City -->
                    <div class="form-group">
                        <label class="form-label font-bold text-xs mb-1 block text-slate-700 dark:text-slate-300 flex items-center justify-between">
                            <span>{{ app()->getLocale() === 'ar' ? '2. المدينة' : '2. City' }}</span>
                            <span x-show="loadingCities" class="text-[10px] text-sky-500 font-normal">
                                <i class="fa-solid fa-spinner fa-spin"></i>
                            </span>
                        </label>
                        <select name="city_id" x-model="cityId" @change="onCityChange()" :disabled="!countryId || loadingCities" class="form-select text-xs w-full rounded-lg disabled:opacity-50">
                            <option value="">{{ app()->getLocale() === 'ar' ? '-- اختر المدينة --' : '-- Select City --' }}</option>
                            <template x-for="city in cities" :key="city.id">
                                <option :value="city.id" :selected="city.id == cityId" x-text="'{{ app()->getLocale() }}' === 'ar' ? (city.name_ar || city.name_en) : (city.name_en || city.name_ar)"></option>
                            </template>
                        </select>
                    </div>

                    <!-- 3. Area / Territory -->
                    <div class="form-group">
                        <label class="form-label font-bold text-xs mb-1 block text-slate-700 dark:text-slate-300 flex items-center justify-between">
                            <span>{{ app()->getLocale() === 'ar' ? '3. المنطقة / المربع *' : '3. Area / Territory *' }}</span>
                            <span x-show="loadingAreas" class="text-[10px] text-sky-500 font-normal">
                                <i class="fa-solid fa-spinner fa-spin"></i>
                            </span>
                        </label>
                        <select name="area_id" x-model="areaId" :disabled="!cityId || loadingAreas" class="form-select text-xs w-full rounded-lg disabled:opacity-50 border-sky-300 dark:border-sky-700">
                            <option value="">{{ app()->getLocale() === 'ar' ? '-- اختر المنطقة الميدانية --' : '-- Select Field Area --' }}</option>
                            <template x-for="area in areas" :key="area.id">
                                <option :value="area.id" :selected="area.id == areaId" x-text="'{{ app()->getLocale() }}' === 'ar' ? (area.name_ar || area.name_en) : (area.name_en || area.name_ar)"></option>
                            </template>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group mb-4">
                <label class="form-label font-bold text-sm mb-1.5 block">{{ app()->getLocale() === 'ar' ? 'نبذة تعريفية وملاحظات الموظف' : 'Staff Bio & Description' }}</label>
                <textarea name="bio" rows="2" class="form-control text-sm w-full" placeholder="{{ app()->getLocale() === 'ar' ? 'أخصائي التغذية العلاجية وإطالة العمر الافتراضي...' : 'Clinical nutritionist & longevity formulations specialist...' }}">{{ old('bio', $user['bio'] ?? '') }}</textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <x-forms.input name="password" type="password" :label="__('admin.users.password')" placeholder="{{ app()->getLocale() === 'ar' ? 'اتركه فارغاً للإبقاء على كلمة المرور الحالية' : 'Leave blank to keep current' }}" />
                <x-forms.input name="password_confirmation" type="password" :label="__('admin.users.password_confirmation')" placeholder="{{ app()->getLocale() === 'ar' ? 'تأكيد كلمة المرور الجديدة' : 'Confirm new password' }}" />
            </div>
        </div>
    </form>
</x-layouts.admin>
