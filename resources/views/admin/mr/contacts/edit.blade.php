<x-layouts.admin 
    :pageTitle="(app()->getLocale() === 'ar' ? 'تعديل بيانات الطبيب: ' : 'Edit Doctor: ') . $contact->name" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'تعديل بيانات الطبيب، التخصص، الفئة، وإحداثيات الموقع الجغرافي' : 'Update medical professional profile, classification, specialty, and clinic GPS coordinates.'"
    :breadcrumbs="[
        (app()->getLocale() === 'ar' ? 'نظام المندوب الطبي' : 'MR CRM') => route('admin.mr.live-map'),
        (app()->getLocale() === 'ar' ? 'الأطباء والعيادات' : 'Doctors & Clinics') => route('admin.mr.contacts.index'),
        $contact->name => route('admin.mr.contacts.show', $contact->id),
        (app()->getLocale() === 'ar' ? 'تعديل' : 'Edit') => route('admin.mr.contacts.edit', $contact->id)
    ]"
>
    <div class="max-w-4xl">
        <form method="POST" action="{{ route('admin.mr.contacts.update', $contact->id) }}" class="card p-6 space-y-6">
            @csrf
            @method('PUT')

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
                        <input type="text" name="name" value="{{ old('name', $contact->name) }}" required class="form-control text-sm">
                        @error('name') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                            {{ app()->getLocale() === 'ar' ? 'كود الطبيب *' : 'Doctor Code *' }}
                        </label>
                        <input type="text" name="code" value="{{ old('code', $contact->code) }}" required class="form-control text-sm">
                        @error('code') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                            {{ app()->getLocale() === 'ar' ? 'التخصص الطبي *' : 'Medical Specialty *' }}
                        </label>
                        <select name="specialty_id" required class="form-select text-sm">
                            @foreach($specialties as $sp)
                                <option value="{{ $sp->id }}" {{ old('specialty_id', $contact->specialty_id) == $sp->id ? 'selected' : '' }}>
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
                            @foreach($classifications as $cl)
                                <option value="{{ $cl->id }}" {{ old('classification_id', $contact->classification_id) == $cl->id ? 'selected' : '' }}>
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
                            <input type="text" name="hospital_clinic_name" value="{{ old('hospital_clinic_name', $contact->hospital_clinic_name) }}" class="form-control text-sm flex-1">
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
                        <input type="text" name="phone" value="{{ old('phone', $contact->phone) }}" class="form-control text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                            {{ app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Email Address' }}
                        </label>
                        <input type="email" name="email" value="{{ old('email', $contact->email) }}" class="form-control text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                            {{ app()->getLocale() === 'ar' ? 'المدينة' : 'City' }}
                        </label>
                        <select name="city_id" class="form-select text-sm">
                            <option value="">{{ app()->getLocale() === 'ar' ? 'اختر المدينة' : 'Select City' }}</option>
                            @foreach($cities as $ct)
                                <option value="{{ $ct->id }}" {{ old('city_id', $contact->city_id) == $ct->id ? 'selected' : '' }}>
                                    {{ $ct->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                            {{ app()->getLocale() === 'ar' ? 'المنطقة / الحي' : 'Region / District' }}
                        </label>
                        <input type="text" name="region" value="{{ old('region', $contact->region) }}" class="form-control text-sm">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                            {{ app()->getLocale() === 'ar' ? 'العنوان التفصيلي' : 'Detailed Address' }}
                        </label>
                        <textarea name="address" rows="2" class="form-control text-sm">{{ old('address', $contact->address) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                            {{ app()->getLocale() === 'ar' ? 'خط العرض (Latitude)' : 'GPS Latitude' }}
                        </label>
                        <input type="number" step="any" name="latitude" value="{{ old('latitude', $contact->latitude) }}" class="form-control text-sm font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                            {{ app()->getLocale() === 'ar' ? 'خط الطول (Longitude)' : 'GPS Longitude' }}
                        </label>
                        <input type="number" step="any" name="longitude" value="{{ old('longitude', $contact->longitude) }}" class="form-control text-sm font-mono">
                    </div>
                </div>
            </div>

            <!-- Notes & Status -->
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                        {{ app()->getLocale() === 'ar' ? 'ملاحظات وتوجيهات للمندوب' : 'Rep Visiting Notes & Directives' }}
                    </label>
                    <textarea name="notes" rows="3" class="form-control text-sm">{{ old('notes', $contact->notes) }}</textarea>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" id="is_active" class="form-checkbox rounded text-sky-600" {{ old('is_active', $contact->is_active) ? 'checked' : '' }}>
                    <label for="is_active" class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                        {{ app()->getLocale() === 'ar' ? 'الطبيب نشط ومتاح للتكليف بالزيارات' : 'Active Doctor (Available for Cycle Assignments)' }}
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-800">
                <a href="{{ route('admin.mr.contacts.show', $contact->id) }}" class="btn btn-outline text-xs">
                    <i class="fa-solid fa-eye mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'عرض الملف' : 'View Profile' }}
                </a>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.mr.contacts.index') }}" class="btn btn-secondary text-sm">
                        {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                    </a>
                    <button type="submit" class="btn btn-primary font-bold text-sm shadow-sm">
                        <i class="fa-solid fa-floppy-disk mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'حفظ التعديلات' : 'Update Doctor' }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-layouts.admin>
