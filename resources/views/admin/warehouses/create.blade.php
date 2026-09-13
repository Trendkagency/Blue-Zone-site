<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'إضافة منشأة أو مستودع جديد' : 'Add New Storage Facility'"
    :pageSubtitle="app()->getLocale() === 'ar' ? 'تسجيل مستودع لوجستي، فرع إقليمي، أو نقطة بيع وربطها بالدولة والمدينة.' : 'Register a new warehouse, regional hub, or warehouse POS linked to a dynamic country & city.'"
    :breadcrumbs="[
        (app()->getLocale() === 'ar' ? 'المخزون' : 'Inventory') => route('admin.inventory.index'),
        (app()->getLocale() === 'ar' ? 'المستودعات' : 'Warehouses') => route('admin.warehouses.index'),
        (app()->getLocale() === 'ar' ? 'إضافة مستودع' : 'Create Facility') => route('admin.warehouses.create')
    ]"
>
    <x-slot name="actions">
        <a href="{{ route('admin.warehouses.index') }}" class="btn btn-ghost font-semibold">
            <i class="fa-solid fa-arrow-left mr-1.5 ml-1.5"></i>
            {{ app()->getLocale() === 'ar' ? 'رجوع للمستودعات' : 'Back to Facilities' }}
        </a>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        @if($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 text-rose-800 dark:text-rose-300 text-sm space-y-1">
                @foreach($errors->all() as $error)
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-exclamation text-rose-600 dark:text-rose-400"></i>
                        <span>{{ $error }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Creation Card -->
        <div class="card p-6 sm:p-8 shadow-sm border border-slate-200/80 dark:border-slate-800/80">
            <form method="POST" action="{{ route('admin.warehouses.store') }}" class="space-y-6">
                @csrf

                <!-- Bilingual Names -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            {{ app()->getLocale() === 'ar' ? 'اسم المستودع (بالإنجليزية)' : 'Warehouse Name (English)' }} <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="name_en" id="createNameEn" value="{{ old('name_en') }}" required placeholder="e.g. Jeddah Regional Logistics Hub"
                            class="form-control text-sm w-full">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            {{ app()->getLocale() === 'ar' ? 'اسم المستودع (بالعربية)' : 'Warehouse Name (Arabic)' }} <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="name_ar" value="{{ old('name_ar') }}" required placeholder="مثال: مركز جدة اللوجستي الإقليمي"
                            class="form-control text-sm w-full" dir="rtl">
                    </div>
                </div>

                <!-- Code, Slug & Facility Type -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            {{ app()->getLocale() === 'ar' ? 'رمز المنشأة (Code)' : 'Facility Code' }}
                        </label>
                        <input type="text" name="code" value="{{ old('code') }}" placeholder="e.g. LOC-JED"
                            class="form-control text-sm w-full uppercase font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            {{ app()->getLocale() === 'ar' ? 'المعرف الفريد (Slug ID)' : 'System Slug ID' }}
                        </label>
                        <input type="text" name="id" id="createSlugId" value="{{ old('id') }}" placeholder="e.g. wh_jeddah"
                            class="form-control text-sm w-full font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            {{ app()->getLocale() === 'ar' ? 'نوع المنشأة' : 'Facility Type' }} <span class="text-rose-500">*</span>
                        </label>
                        <select name="type" required class="form-select text-sm w-full">
                            <option value="warehouse" {{ old('type') === 'warehouse' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'مستودع لوجستي' : 'Logistics Warehouse' }}</option>
                            <option value="branch" {{ old('type') === 'branch' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'فرع إقليمي' : 'Regional Branch' }}</option>
                            <option value="offline" {{ old('type') === 'offline' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'مستودع / نقطة بيع POS' : 'Warehouse / POS' }}</option>
                            <option value="online" {{ old('type') === 'online' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'مركز طلبات إلكترونية' : 'E-Commerce Hub' }}</option>
                        </select>
                    </div>
                </div>

                <!-- Country, City & Physical Address -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            {{ app()->getLocale() === 'ar' ? 'الدولة' : 'Country' }}
                        </label>
                        <select name="country_id" id="createCountrySelect" class="form-select text-sm w-full" onchange="handleCreateCountryChange(this.value)">
                            <option value="">{{ app()->getLocale() === 'ar' ? 'اختر الدولة...' : 'Select Country...' }}</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}" data-dial="{{ $country->phone_code }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                    {{ $country->flag_emoji }} {{ $country->name_en }} ({{ $country->name_ar }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            {{ app()->getLocale() === 'ar' ? 'المدينة / المنطقة' : 'City / Zone' }}
                        </label>
                        <select name="city_id" id="createCitySelect" class="form-select text-sm w-full">
                            <option value="">{{ app()->getLocale() === 'ar' ? 'اختر الدولة أولاً...' : 'Select Country First...' }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            {{ app()->getLocale() === 'ar' ? 'العنوان الجغرافي التفصيلي' : 'Physical Address' }}
                        </label>
                        <input type="text" name="address" value="{{ old('address') }}" placeholder="e.g. Industrial Area 2"
                            class="form-control text-sm w-full">
                    </div>
                </div>

                <!-- Manager, Phone & Capacity -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            {{ app()->getLocale() === 'ar' ? 'المشرف / مدير المستودع' : 'Facility Manager' }}
                        </label>
                        <input type="text" name="manager_name" value="{{ old('manager_name') }}" placeholder="e.g. Tariq Mansoor"
                            class="form-control text-sm w-full">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            {{ app()->getLocale() === 'ar' ? 'هاتف التواصل' : 'Contact Phone' }}
                        </label>
                        <div class="relative flex items-center">
                            <span id="createPhonePrefixBadge" class="inline-flex items-center px-3 py-2.5 rounded-l-xl bg-slate-100 dark:bg-slate-800 border border-r-0 border-slate-200 dark:border-slate-700 text-xs font-mono font-bold text-indigo-600 dark:text-indigo-400" dir="ltr">
                                +966
                            </span>
                            <input type="text" name="phone" id="createPhoneInput" value="{{ old('phone') }}" placeholder="50 123 4567"
                                class="form-control text-sm w-full rounded-l-none" dir="ltr">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            {{ app()->getLocale() === 'ar' ? 'السعة الاستيعابية (وحدة)' : 'Max Capacity (Units)' }}
                        </label>
                        <input type="number" name="capacity_units" value="{{ old('capacity_units', 10000) }}" min="10" step="100"
                            class="form-control text-sm w-full">
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        {{ app()->getLocale() === 'ar' ? 'ملاحظات تشغيلية' : 'Operational Notes' }}
                    </label>
                    <textarea name="notes" rows="3" placeholder="{{ app()->getLocale() === 'ar' ? 'ملاحظات عن درجات الحرارة، معايير التخزين...' : 'Temperature standards, access hours, security notes...' }}"
                        class="form-control text-sm w-full">{{ old('notes') }}</textarea>
                </div>

                <!-- Active Status -->
                <div class="flex items-center gap-3 pt-2">
                    <input type="checkbox" name="is_active" id="createIsActive" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}
                        class="form-check-input w-4 h-4">
                    <label for="createIsActive" class="text-sm font-semibold text-slate-700 dark:text-slate-300 select-none cursor-pointer">
                        {{ app()->getLocale() === 'ar' ? 'تفعيل المنشأة فورياً لاستقبال وتوزيع الشحنات والمخزون' : 'Activate facility immediately for inventory distribution' }}
                    </label>
                </div>

                <!-- Submit Toolbar -->
                <div class="pt-6 border-t border-slate-200/80 dark:border-slate-800 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.warehouses.index') }}" class="btn btn-secondary font-semibold text-sm">
                        {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                    </a>
                    <button type="submit" class="btn btn-primary font-bold text-sm shadow-sm">
                        <i class="fa-solid fa-check mr-1.5 ml-1.5"></i>
                        {{ app()->getLocale() === 'ar' ? 'حفظ وإنشاء المستودع' : 'Save & Provision Hub' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Auto-slug generator
        document.getElementById('createNameEn')?.addEventListener('input', function(e) {
            const slugInput = document.getElementById('createSlugId');
            if (slugInput && !slugInput.dataset.manual) {
                slugInput.value = 'wh_' + e.target.value.toLowerCase().replace(/[^a-z0-9]/g, '_').replace(/_+/g, '_').replace(/^_|_$/g, '');
            }
        });

        document.getElementById('createSlugId')?.addEventListener('input', function() {
            this.dataset.manual = 'true';
        });

        function handleCreateCountryChange(countryId) {
            const citySelect = document.getElementById('createCitySelect');
            const dialBadge = document.getElementById('createPhonePrefixBadge');
            const countrySelect = document.getElementById('createCountrySelect');

            if (!countryId) {
                citySelect.innerHTML = '<option value="">{{ app()->getLocale() === 'ar' ? 'اختر الدولة أولاً...' : 'Select Country First...' }}</option>';
                return;
            }

            const selectedOption = countrySelect.options[countrySelect.selectedIndex];
            const dialCode = selectedOption ? selectedOption.getAttribute('data-dial') : '+966';
            if (dialBadge && dialCode) {
                dialBadge.textContent = dialCode;
            }

            citySelect.innerHTML = '<option value="">{{ app()->getLocale() === 'ar' ? 'جاري تحميل المدن...' : 'Loading cities...' }}</option>';
            citySelect.disabled = true;

            fetch(`{{ url('admin/api/countries') }}/${countryId}/cities`)
                .then(res => res.json())
                .then(data => {
                    citySelect.innerHTML = '<option value="">{{ app()->getLocale() === 'ar' ? 'اختر المدينة...' : 'Select City...' }}</option>';
                    if (data.cities && data.cities.length > 0) {
                        data.cities.forEach(city => {
                            const opt = document.createElement('option');
                            opt.value = city.id;
                            opt.textContent = `{{ app()->getLocale() === 'ar' ? '${city.name_ar} (${city.name_en})' : '${city.name_en} (${city.name_ar})' }}`;
                            citySelect.appendChild(opt);
                        });
                    } else {
                        citySelect.innerHTML = '<option value="">{{ app()->getLocale() === 'ar' ? 'لا توجد مدن مضافة لهذه الدولة' : 'No cities found for this country' }}</option>';
                    }
                    citySelect.disabled = false;
                })
                .catch(err => {
                    console.error('Error fetching cities:', err);
                    citySelect.disabled = false;
                });
        }
    </script>
</x-layouts.admin>
