<x-layouts.admin 
    :pageTitle="(app()->getLocale() === 'ar' ? 'تعديل بيانات المستودع: ' : 'Edit Facility: ') . $warehouse->name"
    :pageSubtitle="app()->getLocale() === 'ar' ? 'تحديث المعلومات التشغيلية، العنوان، السعة، وبيانات التواصل للمنشأة.' : 'Update operational attributes, physical address, capacity, and contacts.'"
    :breadcrumbs="[
        (app()->getLocale() === 'ar' ? 'المخزون' : 'Inventory') => route('admin.inventory.index'),
        (app()->getLocale() === 'ar' ? 'المستودعات' : 'Warehouses') => route('admin.warehouses.index'),
        (app()->getLocale() === 'ar' ? 'تعديل: ' : 'Edit: ') . $warehouse->name => route('admin.warehouses.edit', $warehouse->id)
    ]"
>
    <x-slot name="actions">
        <a href="{{ route('admin.warehouses.show', $warehouse->id) }}" class="btn btn-secondary font-semibold">
            <i class="fa-solid fa-eye text-indigo-500 mr-1.5 ml-1.5"></i>
            {{ app()->getLocale() === 'ar' ? 'عرض المخزون' : 'View Stock' }}
        </a>

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

        <!-- Edit Form Card -->
        <div class="card p-6 sm:p-8 shadow-sm border border-slate-200/80 dark:border-slate-800/80">
            <form method="POST" action="{{ route('admin.warehouses.update', $warehouse->id) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Bilingual Names -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            {{ app()->getLocale() === 'ar' ? 'اسم المستودع (بالإنجليزية)' : 'Warehouse Name (English)' }} <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="name_en" value="{{ old('name_en', $warehouse->name_en) }}" required
                            class="form-control text-sm w-full">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            {{ app()->getLocale() === 'ar' ? 'اسم المستودع (بالعربية)' : 'Warehouse Name (Arabic)' }} <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="name_ar" value="{{ old('name_ar', $warehouse->name_ar) }}" required dir="rtl"
                            class="form-control text-sm w-full">
                    </div>
                </div>

                <!-- Code & Facility Type -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            {{ app()->getLocale() === 'ar' ? 'رمز المنشأة (Code)' : 'Facility Code' }} <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="code" value="{{ old('code', $warehouse->code) }}" required
                            class="form-control text-sm w-full uppercase font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            {{ app()->getLocale() === 'ar' ? 'نوع المنشأة' : 'Facility Type' }} <span class="text-rose-500">*</span>
                        </label>
                        <select name="type" required class="form-select text-sm w-full">
                            @foreach($types as $key => $label)
                                <option value="{{ $key }}" {{ old('type', $warehouse->type) === $key ? 'selected' : '' }}>
                                    {{ $label[app()->getLocale()] ?? $label['en'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Country, City & Physical Address -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            {{ app()->getLocale() === 'ar' ? 'الدولة' : 'Country' }}
                        </label>
                        <select name="country_id" id="editCountrySelect" class="form-select text-sm w-full" onchange="handleEditCountryChange(this.value)">
                            <option value="">{{ app()->getLocale() === 'ar' ? 'اختر الدولة...' : 'Select Country...' }}</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}" data-dial="{{ $country->phone_code }}" {{ (old('country_id', $warehouse->country_id) == $country->id) ? 'selected' : '' }}>
                                    {{ $country->flag_emoji }} {{ $country->name_en }} ({{ $country->name_ar }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            {{ app()->getLocale() === 'ar' ? 'المدينة / المنطقة' : 'City / Zone' }}
                        </label>
                        <select name="city_id" id="editCitySelect" class="form-select text-sm w-full">
                            <option value="">{{ app()->getLocale() === 'ar' ? 'اختر المدينة...' : 'Select City...' }}</option>
                            @if($warehouse->country)
                                @foreach($warehouse->country->cities as $ct)
                                    <option value="{{ $ct->id }}" {{ (old('city_id', $warehouse->city_id) == $ct->id) ? 'selected' : '' }}>
                                        {{ $ct->name_en }} ({{ $ct->name_ar }})
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            {{ app()->getLocale() === 'ar' ? 'العنوان الجغرافي التفصيلي' : 'Physical Address' }}
                        </label>
                        <input type="text" name="address" value="{{ old('address', $warehouse->address) }}" placeholder="e.g. Industrial Area 2"
                            class="form-control text-sm w-full">
                    </div>
                </div>

                <!-- Manager, Phone & Capacity -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            {{ app()->getLocale() === 'ar' ? 'المشرف / مدير المستودع' : 'Facility Manager' }}
                        </label>
                        <input type="text" name="manager_name" value="{{ old('manager_name', $warehouse->manager_name) }}"
                            class="form-control text-sm w-full">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            {{ app()->getLocale() === 'ar' ? 'هاتف التواصل' : 'Contact Phone' }}
                        </label>
                        <div class="relative flex items-center">
                            <span id="editPhonePrefixBadge" class="inline-flex items-center px-3 py-2.5 rounded-l-xl bg-slate-100 dark:bg-slate-800 border border-r-0 border-slate-200 dark:border-slate-700 text-xs font-mono font-bold text-indigo-600 dark:text-indigo-400" dir="ltr">
                                {{ $warehouse->country?->phone_code ?? '+966' }}
                            </span>
                            <input type="text" name="phone" id="editPhoneInput" value="{{ old('phone', $warehouse->phone) }}" placeholder="50 123 4567"
                                class="form-control text-sm w-full rounded-l-none" dir="ltr">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            {{ app()->getLocale() === 'ar' ? 'السعة الاستيعابية (وحدة)' : 'Max Capacity (Units)' }}
                        </label>
                        <input type="number" name="capacity_units" value="{{ old('capacity_units', $warehouse->capacity_units ?? 10000) }}" min="10" step="100"
                            class="form-control text-sm w-full">
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        {{ app()->getLocale() === 'ar' ? 'ملاحظات تشغيلية' : 'Operational Notes' }}
                    </label>
                    <textarea name="notes" rows="3"
                        class="form-control text-sm w-full">{{ old('notes', $warehouse->notes) }}</textarea>
                </div>

                <!-- Active Status -->
                <div class="flex items-center gap-3 pt-2">
                    <input type="checkbox" name="is_active" id="editIsActive" value="1" {{ old('is_active', $warehouse->is_active) ? 'checked' : '' }}
                        class="form-check-input w-4 h-4">
                    <label for="editIsActive" class="text-sm font-semibold text-slate-700 dark:text-slate-300 select-none cursor-pointer">
                        {{ app()->getLocale() === 'ar' ? 'المنشأة نشطة وتستقبل حركات التخزين وتوزيع المنتجات' : 'Facility is active for inventory operations and transfers' }}
                    </label>
                </div>

                <!-- Submit Toolbar -->
                <div class="pt-6 border-t border-slate-200/80 dark:border-slate-800 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.warehouses.index') }}" class="btn btn-secondary font-semibold text-sm">
                        {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                    </a>
                    <button type="submit" class="btn btn-primary font-bold text-sm shadow-sm">
                        <i class="fa-solid fa-check mr-1.5 ml-1.5"></i>
                        {{ app()->getLocale() === 'ar' ? 'حفظ التعديلات' : 'Save Changes' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function handleEditCountryChange(countryId) {
            const citySelect = document.getElementById('editCitySelect');
            const dialBadge = document.getElementById('editPhonePrefixBadge');
            const countrySelect = document.getElementById('editCountrySelect');

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
