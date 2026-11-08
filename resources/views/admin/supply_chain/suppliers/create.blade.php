<x-layouts.admin 
    :pageTitle="__('suppliers.new_supplier')" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'إضافة مورد جديد إلى سجل cGMP وتحديد الشهادات والاعتمادات الأولية' : 'Register a new cGMP raw material supplier and configure audit credentials'"
    :breadcrumbs="[__('suppliers.title') => route('admin.suppliers.index'), __('suppliers.new_supplier') => '#']"
>
    <div class="max-w-5xl mx-auto pb-12">
        <form method="POST" action="{{ route('admin.suppliers.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Card 1: Core Company & Identification -->
            <div class="card p-6 border border-gray-100 dark:border-gray-800 shadow-sm space-y-5">
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3">
                    <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-building text-sky-600"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'المعلومات الأساسية للمنشأة والتوريد' : 'Company & Supply Information' }}</span>
                    </h3>
                    <span class="text-xs font-mono px-2.5 py-0.5 rounded-full bg-sky-100 dark:bg-sky-950 text-sky-800 dark:text-sky-300 font-bold">
                        cGMP Sourcing Vendor
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Supplier Code (Auto-Generated with Manual Override) -->
                    <div class="lg:col-span-1">
                        <div class="flex items-center justify-between mb-1">
                            <label class="form-label font-bold text-xs">{{ __('suppliers.supplier_code') }} <span class="text-rose-500">*</span></label>
                            <span class="text-[10px] text-sky-600 dark:text-sky-400 font-bold cursor-pointer hover:underline" onclick="document.getElementById('supplierCodeInput').value = '{{ $defaultCode }}'">
                                <i class="fa-solid fa-arrows-rotate mr-0.5 ml-0.5"></i> {{ __('suppliers.auto_generate') }}
                            </span>
                        </div>
                        <div class="relative">
                            <input type="text" 
                                   name="code" 
                                   id="supplierCodeInput"
                                   value="{{ old('code', $defaultCode) }}" 
                                   required 
                                   class="form-control text-sm font-mono tracking-wider @error('code') border-rose-500 @enderror" 
                                   placeholder="e.g. SUP-2026-0001">
                        </div>
                        @error('code') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Name English -->
                    <div class="lg:col-span-1">
                        <label class="form-label font-bold text-xs">{{ __('suppliers.name_en') }} <span class="text-rose-500">*</span></label>
                        <input type="text" name="name_en" value="{{ old('name_en') }}" required class="form-control text-sm @error('name_en') border-rose-500 @enderror" placeholder="e.g. Lonza Consumer Health Inc.">
                        @error('name_en') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Name Arabic -->
                    <div class="lg:col-span-1">
                        <label class="form-label font-bold text-xs">{{ __('suppliers.name_ar') }}</label>
                        <input type="text" name="name_ar" value="{{ old('name_ar') }}" class="form-control text-sm font-tajawal @error('name_ar') border-rose-500 @enderror" placeholder="مثال: شركة لونزا للرعاية الصحية">
                        @error('name_ar') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Partner Type -->
                    <div>
                        <label class="form-label font-bold text-xs">{{ __('suppliers.type') }} <span class="text-rose-500">*</span></label>
                        <select name="type" required class="form-select text-sm @error('type') border-rose-500 @enderror">
                            @foreach($types as $type)
                                <option value="{{ $type->value }}" {{ old('type') === $type->value ? 'selected' : '' }}>
                                    {{ $type->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('type') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Origin Country -->
                    <div>
                        <label class="form-label font-bold text-xs">{{ __('suppliers.country') }} <span class="text-rose-500">*</span></label>
                        <select name="country_id" required class="form-select text-sm @error('country_id') border-rose-500 @enderror">
                            <option value="">-- {{ __('suppliers.select_country') }} --</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}" {{ old('country_id', 1) == $country->id ? 'selected' : '' }}>
                                    {{ app()->getLocale() === 'ar' ? ($country->name_ar ?? $country->name_en) : $country->name_en }} ({{ $country->code }})
                                </option>
                            @endforeach
                        </select>
                        @error('country_id') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Currency Selection -->
                    <div>
                        <label class="form-label font-bold text-xs">{{ __('suppliers.currency') }} <span class="text-rose-500">*</span></label>
                        <select name="currency" required class="form-select text-sm font-bold @error('currency') border-rose-500 @enderror">
                            @foreach($currencies as $curr)
                                <option value="{{ $curr }}" {{ old('currency', 'SAR') === $curr ? 'selected' : '' }}>
                                    {{ $curr }}
                                </option>
                            @endforeach
                        </select>
                        @error('currency') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Contact Name -->
                    <div>
                        <label class="form-label font-bold text-xs">{{ __('suppliers.contact_name') }}</label>
                        <input type="text" name="contact_name" value="{{ old('contact_name', old('contact_person')) }}" class="form-control text-sm" placeholder="e.g. Dr. Arthur Pendelton">
                    </div>

                    <!-- Procurement Email -->
                    <div>
                        <label class="form-label font-bold text-xs">{{ __('suppliers.email') }} <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="form-control text-sm @error('email') border-rose-500 @enderror" placeholder="orders@supplier.com">
                        @error('email') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Phone Number -->
                    <div>
                        <label class="form-label font-bold text-xs">{{ __('suppliers.phone') }}</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" class="form-control text-sm" placeholder="+966 50 123 4567">
                    </div>

                    <!-- Payment Terms -->
                    <div>
                        <label class="form-label font-bold text-xs">{{ __('suppliers.payment_terms') }} <span class="text-rose-500">*</span></label>
                        <input type="text" name="payment_terms" value="{{ old('payment_terms', 'Net 30') }}" required class="form-control text-sm" placeholder="e.g. Net 30, LC at sight, Advance 50%">
                        @error('payment_terms') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Lead Time (Days) -->
                    <div>
                        <label class="form-label font-bold text-xs">{{ __('suppliers.lead_time_days') }} <span class="text-rose-500">*</span></label>
                        <input type="number" name="lead_time_days" value="{{ old('lead_time_days', 14) }}" min="1" max="365" required class="form-control text-sm" placeholder="14">
                        @error('lead_time_days') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Audit Expiry / Valid Until Date -->
                    <div>
                        <label class="form-label font-bold text-xs">{{ __('suppliers.audit_valid_until') }}</label>
                        <input type="date" name="audit_expiry" value="{{ old('audit_expiry', old('audit_valid_until', date('Y-m-d', strtotime('+1 year')))) }}" class="form-control text-sm font-mono">
                        <span class="text-[10px] text-slate-500 dark:text-slate-400 mt-1 block">{{ app()->getLocale() === 'ar' ? 'الصلاحية الافتراضية لتدقيق الجودة cGMP محددة بسنة كاملة.' : 'Default cGMP audit validity is set to 1 year.' }}</span>
                        @error('audit_expiry') <span class="text-rose-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- City & Address -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                    <div>
                        <label class="form-label font-bold text-xs">{{ __('suppliers.city') }}</label>
                        <input type="text" name="city" value="{{ old('city') }}" class="form-control text-sm" placeholder="e.g. Basel, Riyadh">
                    </div>
                    <div class="md:col-span-2">
                        <label class="form-label font-bold text-xs">{{ __('suppliers.address') }}</label>
                        <input type="text" name="address" value="{{ old('address') }}" class="form-control text-sm" placeholder="Physical manufacturing plant or headquarters address...">
                    </div>
                </div>
            </div>

            <!-- Card 2: Compliance Documents & cGMP Qualification Wizard -->
            <div class="card p-6 border border-gray-100 dark:border-gray-800 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3">
                    <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-file-shield text-emerald-600"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'مرفقات ملف الاعتماد والتدقيق cGMP (PDF فقط)' : 'cGMP Qualification Attachments (PDF only)' }}</span>
                    </h3>
                    <span class="text-xs text-slate-500 dark:text-slate-400 font-mono">
                        Max 10MB per certificate
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- cGMP Certificate -->
                    <div class="border border-dashed border-gray-300 dark:border-gray-700 rounded-xl p-4 bg-slate-50/50 dark:bg-slate-900/30 hover:border-emerald-500 transition-colors">
                        <label class="font-bold text-xs text-slate-800 dark:text-gray-200 block mb-1.5 flex items-center gap-1.5">
                            <i class="fa-solid fa-certificate text-emerald-500 text-sm"></i> 
                            <span>{{ __('suppliers.cgmp_document') }}</span>
                        </label>
                        <input type="file" name="doc_cgmp" accept=".pdf" class="form-control text-xs">
                        <span class="text-[11px] text-gray-500 mt-1 block">FDA, EMA, SFDA or ISO 22716 compliance audit certificate</span>
                    </div>

                    <!-- ISO Certificate -->
                    <div class="border border-dashed border-gray-300 dark:border-gray-700 rounded-xl p-4 bg-slate-50/50 dark:bg-slate-900/30 hover:border-sky-500 transition-colors">
                        <label class="font-bold text-xs text-slate-800 dark:text-gray-200 block mb-1.5 flex items-center gap-1.5">
                            <i class="fa-solid fa-award text-sky-500 text-sm"></i> 
                            <span>{{ __('suppliers.iso_document') }}</span>
                        </label>
                        <input type="file" name="doc_iso" accept=".pdf" class="form-control text-xs">
                        <span class="text-[11px] text-gray-500 mt-1 block">ISO 9001 / ISO 17025 accredited laboratory test certificate</span>
                    </div>

                    <!-- Halal / Kosher -->
                    <div class="border border-dashed border-gray-300 dark:border-gray-700 rounded-xl p-4 bg-slate-50/50 dark:bg-slate-900/30 hover:border-amber-500 transition-colors">
                        <label class="font-bold text-xs text-slate-800 dark:text-gray-200 block mb-1.5 flex items-center gap-1.5">
                            <i class="fa-solid fa-hand-holding-heart text-amber-500 text-sm"></i> 
                            <span>{{ __('suppliers.halal_document') }}</span>
                        </label>
                        <input type="file" name="doc_halal" accept=".pdf" class="form-control text-xs">
                        <span class="text-[11px] text-gray-500 mt-1 block">Halal, Kosher, or certified organic biological origin</span>
                    </div>

                    <!-- Allergen Statement -->
                    <div class="border border-dashed border-gray-300 dark:border-gray-700 rounded-xl p-4 bg-slate-50/50 dark:bg-slate-900/30 hover:border-rose-500 transition-colors">
                        <label class="font-bold text-xs text-slate-800 dark:text-gray-200 block mb-1.5 flex items-center gap-1.5">
                            <i class="fa-solid fa-shield-virus text-rose-500 text-sm"></i> 
                            <span>{{ __('suppliers.allergen_document') }}</span>
                        </label>
                        <input type="file" name="doc_allergen" accept=".pdf" class="form-control text-xs">
                        <span class="text-[11px] text-gray-500 mt-1 block">BSE/TSE free statement & non-cross-contamination declaration</span>
                    </div>
                </div>
            </div>

            <!-- Footer Action Buttons -->
            <div class="flex items-center justify-between pt-2">
                <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary text-sm">
                    <i class="fa-solid fa-arrow-left mr-1.5 ml-1.5 {{ app()->getLocale() === 'ar' ? 'fa-flip-horizontal' : '' }}"></i>
                    {{ app()->getLocale() === 'ar' ? 'إلغاء والعودة' : 'Cancel & Return' }}
                </a>
                <button type="submit" class="btn btn-primary text-sm font-bold shadow-md hover:shadow-lg transition-all px-6 py-2.5">
                    <i class="fa-solid fa-check-circle mr-1.5 ml-1.5"></i> 
                    {{ app()->getLocale() === 'ar' ? 'حفظ وتأهيل المورد الجديد' : 'Save & Onboard Supplier' }}
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>
