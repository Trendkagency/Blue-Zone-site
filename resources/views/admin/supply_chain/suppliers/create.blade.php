<x-layouts.admin 
    :pageTitle="__('suppliers.new_supplier')" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'إضافة مورد جديد إلى سجل cGMP وتحديد الشهادات والاعتمادات الأولية' : 'Register a new cGMP raw material supplier and configure audit credentials'"
    :breadcrumbs="[__('suppliers.title') => route('admin.suppliers.index'), __('suppliers.new_supplier') => '#']"
>
    <div class="max-w-4xl mx-auto">
        <form method="POST" action="{{ route('admin.suppliers.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="card p-6 border border-gray-100 dark:border-gray-800 shadow-sm space-y-4">
                <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2 border-b border-gray-100 dark:border-gray-800 pb-3">
                    <i class="fa-solid fa-building text-sky-600"></i>
                    {{ app()->getLocale() === 'ar' ? 'المعلومات الأساسية للمنشأة والتوريد' : 'Company & Supply Information' }}
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label font-bold text-xs">{{ __('suppliers.name_en') }} <span class="text-rose-500">*</span></label>
                        <input type="text" name="name_en" value="{{ old('name_en') }}" required class="form-control text-sm" placeholder="e.g. ChromaDex Inc.">
                        @error('name_en') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="form-label font-bold text-xs">{{ __('suppliers.name_ar') }}</label>
                        <input type="text" name="name_ar" value="{{ old('name_ar') }}" class="form-control text-sm" placeholder="مثال: شركة كروما دكس للمغذيات">
                        @error('name_ar') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="form-label font-bold text-xs">{{ __('suppliers.type') }} <span class="text-rose-500">*</span></label>
                        <select name="type" required class="form-select text-sm">
                            @foreach(\App\Enums\SupplierType::cases() as $type)
                                <option value="{{ $type->value }}" {{ old('type') === $type->value ? 'selected' : '' }}>
                                    {{ $type->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('type') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="form-label font-bold text-xs">{{ __('suppliers.country') }} <span class="text-rose-500">*</span></label>
                        <input type="text" name="country" value="{{ old('country', 'United States') }}" required class="form-control text-sm" placeholder="Country of origin">
                        @error('country') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="form-label font-bold text-xs">{{ __('suppliers.contact_person') }}</label>
                        <input type="text" name="contact_person" value="{{ old('contact_person') }}" class="form-control text-sm" placeholder="Key Account Manager">
                    </div>

                    <div>
                        <label class="form-label font-bold text-xs">{{ __('suppliers.email') }} <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="form-control text-sm" placeholder="compliance@supplier.com">
                        @error('email') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="form-label font-bold text-xs">{{ __('suppliers.phone') }}</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" class="form-control text-sm" placeholder="+1 555-0199">
                    </div>

                    <div>
                        <label class="form-label font-bold text-xs">{{ __('suppliers.payment_terms') }}</label>
                        <input type="text" name="payment_terms" value="{{ old('payment_terms', 'Net 30') }}" class="form-control text-sm" placeholder="Net 30, LC at sight">
                    </div>

                    <div>
                        <label class="form-label font-bold text-xs">{{ __('suppliers.lead_time_days') }}</label>
                        <input type="number" name="lead_time_days" value="{{ old('lead_time_days', 14) }}" min="1" class="form-control text-sm">
                    </div>

                    <div>
                        <label class="form-label font-bold text-xs">{{ __('suppliers.audit_valid_until') }}</label>
                        <input type="date" name="audit_valid_until" value="{{ old('audit_valid_until') }}" class="form-control text-sm">
                    </div>
                </div>

                <div>
                    <label class="form-label font-bold text-xs">{{ __('suppliers.address') }}</label>
                    <textarea name="address" rows="2" class="form-control text-sm" placeholder="Physical manufacturing site address...">{{ old('address') }}</textarea>
                </div>
            </div>

            <!-- Documents & Onboarding Wizard -->
            <div class="card p-6 border border-gray-100 dark:border-gray-800 shadow-sm space-y-4">
                <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2 border-b border-gray-100 dark:border-gray-800 pb-3">
                    <i class="fa-solid fa-file-shield text-emerald-600"></i>
                    {{ app()->getLocale() === 'ar' ? 'مرفقات ملف الاعتماد والتدقيق cGMP (PDF فقط)' : 'cGMP Qualification Attachments (PDF only)' }}
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="border border-dashed border-gray-300 dark:border-gray-700 rounded-lg p-3">
                        <label class="font-bold text-xs text-slate-700 dark:text-gray-300 block mb-1">
                            <i class="fa-solid fa-certificate text-emerald-500 mr-1"></i> cGMP Audit Certificate
                        </label>
                        <input type="file" name="doc_cgmp" accept=".pdf" class="form-control text-xs">
                        <span class="text-[11px] text-gray-500">FDA/EMA or ISO 22716 compliance audit report</span>
                    </div>

                    <div class="border border-dashed border-gray-300 dark:border-gray-700 rounded-lg p-3">
                        <label class="font-bold text-xs text-slate-700 dark:text-gray-300 block mb-1">
                            <i class="fa-solid fa-award text-blue-500 mr-1"></i> ISO Quality Certificate
                        </label>
                        <input type="file" name="doc_iso" accept=".pdf" class="form-control text-xs">
                        <span class="text-[11px] text-gray-500">ISO 9001 / ISO 17025 accredited certificate</span>
                    </div>

                    <div class="border border-dashed border-gray-300 dark:border-gray-700 rounded-lg p-3">
                        <label class="font-bold text-xs text-slate-700 dark:text-gray-300 block mb-1">
                            <i class="fa-solid fa-hand-holding-heart text-amber-500 mr-1"></i> Halal / Kosher / Organic
                        </label>
                        <input type="file" name="doc_halal" accept=".pdf" class="form-control text-xs">
                        <span class="text-[11px] text-gray-500">Religious dietary or organic bio-origin certification</span>
                    </div>

                    <div class="border border-dashed border-gray-300 dark:border-gray-700 rounded-lg p-3">
                        <label class="font-bold text-xs text-slate-700 dark:text-gray-300 block mb-1">
                            <i class="fa-solid fa-shield-virus text-rose-500 mr-1"></i> Allergen & BSE/TSE Statement
                        </label>
                        <input type="file" name="doc_allergen" accept=".pdf" class="form-control text-xs">
                        <span class="text-[11px] text-gray-500">Zero-cross-contamination declaration</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary text-sm">
                    {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                </a>
                <button type="submit" class="btn btn-primary text-sm font-bold shadow-sm">
                    <i class="fa-solid fa-save mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'حفظ وتسجيل المورد' : 'Save & Register Supplier' }}
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>
