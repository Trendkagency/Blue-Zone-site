<x-layouts.admin 
    :pageTitle="__('suppliers.edit_supplier') . ': ' . $supplier->name" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'تعديل بيانات المورد ومواعيد تجديد التدقيق والشهادات' : 'Modify supplier profile, audit validity deadlines and certifications'"
    :breadcrumbs="[__('suppliers.title') => route('admin.suppliers.index'), $supplier->name => route('admin.suppliers.show', $supplier), __('suppliers.edit_supplier') => '#']"
>
    <div class="max-w-4xl mx-auto">
        <form method="POST" action="{{ route('admin.suppliers.update', $supplier) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="card p-6 border border-gray-100 dark:border-gray-800 shadow-sm space-y-4">
                <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2 border-b border-gray-100 dark:border-gray-800 pb-3">
                    <i class="fa-solid fa-building text-sky-600"></i>
                    {{ app()->getLocale() === 'ar' ? 'المعلومات الأساسية للمنشأة والتوريد' : 'Company & Supply Information' }}
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label font-bold text-xs">{{ __('suppliers.name_en') }} <span class="text-rose-500">*</span></label>
                        <input type="text" name="name_en" value="{{ old('name_en', $supplier->name_en) }}" required class="form-control text-sm">
                        @error('name_en') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="form-label font-bold text-xs">{{ __('suppliers.name_ar') }}</label>
                        <input type="text" name="name_ar" value="{{ old('name_ar', $supplier->name_ar) }}" class="form-control text-sm">
                        @error('name_ar') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="form-label font-bold text-xs">{{ __('suppliers.type') }} <span class="text-rose-500">*</span></label>
                        <select name="type" required class="form-select text-sm">
                            @foreach(\App\Enums\SupplierType::cases() as $type)
                                <option value="{{ $type->value }}" {{ old('type', $supplier->type?->value) === $type->value ? 'selected' : '' }}>
                                    {{ $type->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('type') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="form-label font-bold text-xs">{{ __('suppliers.country') }} <span class="text-rose-500">*</span></label>
                        <input type="text" name="country" value="{{ old('country', $supplier->country) }}" required class="form-control text-sm">
                        @error('country') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="form-label font-bold text-xs">{{ __('suppliers.contact_person') }}</label>
                        <input type="text" name="contact_person" value="{{ old('contact_person', $supplier->contact_person) }}" class="form-control text-sm">
                    </div>

                    <div>
                        <label class="form-label font-bold text-xs">{{ __('suppliers.email') }} <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $supplier->email) }}" required class="form-control text-sm">
                        @error('email') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="form-label font-bold text-xs">{{ __('suppliers.phone') }}</label>
                        <input type="text" name="phone" value="{{ old('phone', $supplier->phone) }}" class="form-control text-sm">
                    </div>

                    <div>
                        <label class="form-label font-bold text-xs">{{ __('suppliers.payment_terms') }}</label>
                        <input type="text" name="payment_terms" value="{{ old('payment_terms', $supplier->payment_terms) }}" class="form-control text-sm">
                    </div>

                    <div>
                        <label class="form-label font-bold text-xs">{{ __('suppliers.lead_time_days') }}</label>
                        <input type="number" name="lead_time_days" value="{{ old('lead_time_days', $supplier->lead_time_days) }}" min="1" class="form-control text-sm">
                    </div>

                    <div>
                        <label class="form-label font-bold text-xs">{{ __('suppliers.audit_valid_until') }}</label>
                        <input type="date" name="audit_valid_until" value="{{ old('audit_valid_until', $supplier->audit_valid_until?->format('Y-m-d')) }}" class="form-control text-sm">
                    </div>
                </div>

                <div>
                    <label class="form-label font-bold text-xs">{{ __('suppliers.address') }}</label>
                    <textarea name="address" rows="2" class="form-control text-sm">{{ old('address', $supplier->address) }}</textarea>
                </div>
            </div>

            <!-- Documents Upload -->
            <div class="card p-6 border border-gray-100 dark:border-gray-800 shadow-sm space-y-4">
                <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2 border-b border-gray-100 dark:border-gray-800 pb-3">
                    <i class="fa-solid fa-file-shield text-emerald-600"></i>
                    {{ app()->getLocale() === 'ar' ? 'تحديث المستندات والشهادات (PDF فقط)' : 'Update Audit Documents (PDF only)' }}
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="border border-dashed border-gray-300 dark:border-gray-700 rounded-lg p-3">
                        <label class="font-bold text-xs text-slate-700 dark:text-gray-300 block mb-1">
                            <i class="fa-solid fa-certificate text-emerald-500 mr-1"></i> cGMP Audit Certificate
                        </label>
                        <input type="file" name="doc_cgmp" accept=".pdf" class="form-control text-xs">
                    </div>

                    <div class="border border-dashed border-gray-300 dark:border-gray-700 rounded-lg p-3">
                        <label class="font-bold text-xs text-slate-700 dark:text-gray-300 block mb-1">
                            <i class="fa-solid fa-award text-blue-500 mr-1"></i> ISO Quality Certificate
                        </label>
                        <input type="file" name="doc_iso" accept=".pdf" class="form-control text-xs">
                    </div>

                    <div class="border border-dashed border-gray-300 dark:border-gray-700 rounded-lg p-3">
                        <label class="font-bold text-xs text-slate-700 dark:text-gray-300 block mb-1">
                            <i class="fa-solid fa-hand-holding-heart text-amber-500 mr-1"></i> Halal / Kosher / Organic
                        </label>
                        <input type="file" name="doc_halal" accept=".pdf" class="form-control text-xs">
                    </div>

                    <div class="border border-dashed border-gray-300 dark:border-gray-700 rounded-lg p-3">
                        <label class="font-bold text-xs text-slate-700 dark:text-gray-300 block mb-1">
                            <i class="fa-solid fa-shield-virus text-rose-500 mr-1"></i> Allergen Statement
                        </label>
                        <input type="file" name="doc_allergen" accept=".pdf" class="form-control text-xs">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.suppliers.show', $supplier) }}" class="btn btn-secondary text-sm">
                    {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                </a>
                <button type="submit" class="btn btn-primary text-sm font-bold shadow-sm">
                    <i class="fa-solid fa-save mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'حفظ التعديلات' : 'Update Supplier' }}
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>
