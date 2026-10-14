<x-layouts.admin 
    :pageTitle="__('qc.new_inspection')" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'تسجيل نتائج الفحص المخبري، مطابقة مواصفات دستور الأدوية، ورفع وثيقة COA الرسمية' : 'Log Analytical Parameters, Verify Pharmacopeia Specs & Ingest Manufacturer COA'"
    :breadcrumbs="[__('qc.title') => route('admin.qc.index'), __('qc.new_inspection') => '#']"
>
    <div class="max-w-5xl mx-auto">
        <form method="POST" action="{{ route('admin.qc.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Header & Batch Selection -->
            <div class="card p-6 border border-gray-100 dark:border-gray-800 shadow-sm space-y-4">
                <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2 border-b border-gray-100 dark:border-gray-800 pb-3">
                    <i class="fa-solid fa-microscope text-purple-600"></i>
                    {{ app()->getLocale() === 'ar' ? 'بيانات الفحص والتشغيلة المحجورة' : 'Inspection Identification & Quarantine Batch' }}
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="form-label font-bold text-xs">{{ __('batches.batch_number') }} <span class="text-rose-500">*</span></label>
                        <select name="raw_material_batch_id" required class="form-select text-sm font-mono">
                            <option value="">{{ app()->getLocale() === 'ar' ? '-- اختر التشغيلة المحجورة --' : '-- Select Quarantine Batch --' }}</option>
                            @foreach($batches as $b)
                                <option value="{{ $b->id }}" {{ (old('raw_material_batch_id', request('batch_id')) == $b->id) ? 'selected' : '' }}>
                                    {{ $b->batch_number }} - {{ $b->rawMaterial?->name }} (Lot: {{ $b->supplier_lot }})
                                </option>
                            @endforeach
                        </select>
                        @error('raw_material_batch_id') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="form-label font-bold text-xs">{{ __('qc.inspection_date') }} <span class="text-rose-500">*</span></label>
                        <input type="date" name="inspection_date" value="{{ old('inspection_date', date('Y-m-d')) }}" required class="form-control text-sm">
                    </div>

                    <div>
                        <label class="form-label font-bold text-xs">{{ __('qc.preliminary_verdict') }} <span class="text-rose-500">*</span></label>
                        <select name="verdict" required class="form-select text-sm font-bold">
                            @foreach(\App\Enums\QcVerdict::cases() as $v)
                                <option value="{{ $v->value }}" {{ old('verdict') === $v->value ? 'selected' : '' }}>
                                    {{ $v->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- COA Document Upload (PDF Only) -->
                <div class="border border-dashed border-gray-300 dark:border-gray-700 rounded-xl p-4 bg-gray-50/50 dark:bg-slate-800/40">
                    <label class="form-label font-bold text-xs text-slate-800 dark:text-white block mb-1">
                        <i class="fa-solid fa-file-pdf text-rose-500 mr-1.5 ml-1.5"></i>
                        {{ __('qc.coa_document') }} ({{ app()->getLocale() === 'ar' ? 'ملف PDF فقط - حد أقصى 10MB' : 'PDF Only - Max 10MB' }}) <span class="text-rose-500">*</span>
                    </label>
                    <input type="file" name="coa_document" accept=".pdf" required class="form-control text-xs">
                    <span class="text-[11px] text-gray-500 block mt-1">
                        {{ app()->getLocale() === 'ar' ? 'شهادة التحليل الصادرة من المصنع متضمنة نتائج HPLC، الأطياف، وفحوصات المعادن الثقيلة والأحياء الدقيقة' : 'Manufacturer Certificate of Analysis detailing HPLC potency assay, heavy metals & microbiology' }}
                    </span>
                    @error('coa_document') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Parameters Grid -->
            <div class="card p-6 border border-gray-100 dark:border-gray-800 shadow-sm space-y-4">
                <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3">
                    <span class="flex items-center gap-2">
                        <i class="fa-solid fa-list-check text-sky-600"></i>
                        {{ app()->getLocale() === 'ar' ? 'شبكة الاختبارات والمعايير المخبرية الدوائية' : 'Analytical Parameters & Specification Grid' }}
                    </span>
                    <span class="text-xs text-gray-500 font-mono">{{ app()->getLocale() === 'ar' ? 'مطابقة معايير cGMP / USP' : 'USP / Ph. Eur. Compliant' }}</span>
                </h3>

                @php
                    $standardParameters = [
                        [
                            'name' => 'Assay / Potency',
                            'method' => 'HPLC',
                            'spec' => '>= 99.0%',
                            'result' => '99.4%',
                            'is_passed' => true,
                        ],
                        [
                            'name' => 'Identity Confirmation',
                            'method' => 'FTIR / NMR',
                            'spec' => 'Conforms to Reference Standard',
                            'result' => 'Conforms',
                            'is_passed' => true,
                        ],
                        [
                            'name' => 'Heavy Metals (Pb, As, Cd, Hg)',
                            'method' => 'ICP-MS',
                            'spec' => '< 10 ppm total',
                            'result' => '< 1 ppm',
                            'is_passed' => true,
                        ],
                        [
                            'name' => 'Microbial Total Count (TAMC)',
                            'method' => 'USP <61>',
                            'spec' => '< 1000 CFU/g',
                            'result' => '< 10 CFU/g',
                            'is_passed' => true,
                        ],
                        [
                            'name' => 'Loss on Drying',
                            'method' => 'Gravimetric',
                            'spec' => '<= 1.0%',
                            'result' => '0.35%',
                            'is_passed' => true,
                        ],
                    ];
                @endphp

                <div class="table-responsive">
                    <table class="table text-xs">
                        <thead>
                            <tr>
                                <th>{{ __('qc.parameter_name') }}</th>
                                <th>{{ __('qc.test_method') }}</th>
                                <th>{{ __('qc.specification') }}</th>
                                <th>{{ __('qc.observed_result') }}</th>
                                <th style="text-align: center;">{{ __('qc.status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($standardParameters as $index => $param)
                                <tr>
                                    <td>
                                        <input type="text" name="parameters[{{ $index }}][parameter_name]" value="{{ $param['name'] }}" required class="form-control text-xs font-bold">
                                    </td>
                                    <td>
                                        <input type="text" name="parameters[{{ $index }}][test_method]" value="{{ $param['method'] }}" class="form-control text-xs font-mono">
                                    </td>
                                    <td>
                                        <input type="text" name="parameters[{{ $index }}][specification]" value="{{ $param['spec'] }}" required class="form-control text-xs font-mono">
                                    </td>
                                    <td>
                                        <input type="text" name="parameters[{{ $index }}][result_value]" value="{{ $param['result'] }}" required class="form-control text-xs font-mono font-bold">
                                    </td>
                                    <td style="text-align: center;">
                                        <select name="parameters[{{ $index }}][is_passed]" class="form-select text-xs font-bold text-center">
                                            <option value="1" selected>PASS</option>
                                            <option value="0">FAIL</option>
                                        </select>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div>
                    <label class="form-label font-bold text-xs">{{ __('qc.notes') }}</label>
                    <textarea name="notes" rows="2" class="form-control text-sm" placeholder="{{ app()->getLocale() === 'ar' ? 'ملاحظات الفاحص والتحقق من التناقضات...' : 'Analyst remarks, chromatogram reference, waiver details if applicable...' }}">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.qc.index') }}" class="btn btn-secondary text-sm">
                    {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                </a>
                <button type="submit" class="btn btn-primary text-sm font-bold shadow-sm">
                    <i class="fa-solid fa-stamp mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'حفظ الفحص وإرساله للاعتماد المزدوج' : 'Save Inspection & Route for Sign-off' }}
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>
