<x-layouts.admin 
    :pageTitle="__('qc.inspection_number') . ': ' . $qcInspection->inspection_number" 
    :pageSubtitle="__('batches.batch_number') . ': ' . $qcInspection->batch?->batch_number . ' | ' . $qcInspection->batch?->rawMaterial?->name"
    :breadcrumbs="[__('qc.title') => route('admin.qc.index'), $qcInspection->inspection_number => '#']"
>
    <!-- Dual Sign-off & Disposition Ribbon -->
    <div class="card p-5 mb-6 border shadow-sm {{ $qcInspection->verified_at ? 'border-emerald-200 dark:border-emerald-900/40 bg-emerald-50/40 dark:bg-emerald-950/20' : 'border-amber-200 dark:border-amber-900/40 bg-amber-50/40 dark:bg-amber-950/20' }}">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl {{ $qcInspection->verified_at ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                    <i class="fa-solid {{ $qcInspection->verified_at ? 'fa-certificate' : 'fa-user-check' }}"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h4 class="text-base font-black text-slate-800 dark:text-white font-mono">{{ $qcInspection->inspection_number }}</h4>
                        <span class="badge {{ $qcInspection->verdict->badgeClass() }} text-xs">
                            {{ $qcInspection->verdict->label() }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-600 dark:text-gray-400">
                        {{ app()->getLocale() === 'ar' ? 'فصل المهام الإلزامي: لا يحق للفاحص المخبري اعتماد فحص الجودة الخاص به.' : 'Mandatory cGMP Separation of Duties: Analyst cannot self-verify analytical inspection.' }}
                    </p>
                </div>
            </div>

            <!-- Signatures Grid -->
            <div class="flex items-center gap-3">
                <!-- Inspector Box -->
                <div class="bg-white dark:bg-slate-900 border border-gray-200 dark:border-gray-800 rounded-lg p-3 text-xs text-center min-w-[160px]">
                    <span class="text-gray-400 block font-bold mb-1">{{ __('qc.inspector') }}</span>
                    <span class="font-bold text-slate-800 dark:text-white flex items-center justify-center gap-1">
                        <i class="fa-solid fa-user-pen text-sky-500"></i> {{ $qcInspection->inspector?->name }}
                    </span>
                    <span class="text-[10px] text-gray-400 block font-mono">{{ $qcInspection->inspection_date?->format('Y-m-d') }}</span>
                </div>

                <!-- Verifier Box -->
                <div class="bg-white dark:bg-slate-900 border border-gray-200 dark:border-gray-800 rounded-lg p-3 text-xs text-center min-w-[160px]">
                    <span class="text-gray-400 block font-bold mb-1">{{ __('qc.verifier') }}</span>
                    @if($qcInspection->verified_at)
                        <span class="font-bold text-emerald-600 flex items-center justify-center gap-1">
                            <i class="fa-solid fa-circle-check"></i> {{ $qcInspection->verifier?->name }}
                        </span>
                        <span class="text-[10px] text-emerald-500 block font-mono">{{ $qcInspection->verified_at->format('Y-m-d H:i') }}</span>
                    @else
                        @if(auth()->id() !== $qcInspection->inspector_id)
                            <form method="POST" action="{{ route('admin.qc.verify', $qcInspection) }}">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-xs w-full font-bold">
                                    <i class="fa-solid fa-stamp mr-1 ml-1"></i> {{ __('qc.verify_and_release') }}
                                </button>
                            </form>
                        @else
                            <span class="text-amber-500 font-bold block text-[11px]">{{ app()->getLocale() === 'ar' ? 'يتطلب فاحصاً آخر' : 'Requires 2nd Approver' }}</span>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Details Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Batch & Material Linkage -->
        <div class="card p-5 border border-gray-100 dark:border-gray-800 shadow-sm space-y-3">
            <h4 class="text-xs font-black uppercase text-gray-500 tracking-wider border-b border-gray-100 dark:border-gray-800 pb-2">
                {{ app()->getLocale() === 'ar' ? 'التشغيلة والمادة المفحوصة' : 'Sampled Batch & Material' }}
            </h4>
            <div class="space-y-2 text-xs">
                <div class="flex justify-between">
                    <span class="text-gray-500">{{ __('batches.batch_number') }}:</span>
                    <a href="{{ route('admin.batches.show', $qcInspection->batch) }}" class="font-mono font-bold text-purple-600 hover:underline">
                        {{ $qcInspection->batch?->batch_number }}
                    </a>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">{{ __('batches.supplier_lot') }}:</span>
                    <span class="font-mono text-slate-800 dark:text-white">{{ $qcInspection->batch?->supplier_lot }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">{{ __('raw_materials.sku') }}:</span>
                    <span class="font-mono text-slate-800 dark:text-white">{{ $qcInspection->batch?->rawMaterial?->sku }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">{{ __('batches.expiry_date') }}:</span>
                    <span class="font-mono font-bold">{{ $qcInspection->batch?->expiry_date?->format('Y-m-d') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">{{ app()->getLocale() === 'ar' ? 'موقع التخزين' : 'Bin Location' }}:</span>
                    <span class="font-mono">{{ $qcInspection->batch?->location_id }}</span>
                </div>
            </div>
        </div>

        <!-- COA Dossier & Attachments -->
        <div class="card p-5 border border-gray-100 dark:border-gray-800 shadow-sm space-y-3">
            <h4 class="text-xs font-black uppercase text-gray-500 tracking-wider border-b border-gray-100 dark:border-gray-800 pb-2">
                {{ app()->getLocale() === 'ar' ? 'شهادة التحليل الرسمية COA' : 'Manufacturer Certificate of Analysis' }}
            </h4>
            @php $coaMedia = $qcInspection->getFirstMedia('coa_document'); @endphp
            @if($coaMedia)
                <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-gray-200 dark:border-gray-700 text-center space-y-2">
                    <i class="fa-solid fa-file-pdf text-4xl text-rose-500"></i>
                    <div>
                        <p class="font-bold text-slate-800 dark:text-white text-xs">{{ $coaMedia->file_name }}</p>
                        <span class="text-[10px] text-gray-500 font-mono">{{ number_format($coaMedia->size / 1024, 1) }} KB</span>
                    </div>
                    <a href="{{ $coaMedia->getUrl() }}" target="_blank" class="btn btn-secondary btn-sm font-bold text-xs inline-flex items-center gap-1.5">
                        <i class="fa-solid fa-download"></i> {{ app()->getLocale() === 'ar' ? 'فتح / تحميل COA' : 'View / Download PDF' }}
                    </a>
                </div>
            @else
                <div class="text-center py-6 text-gray-400 text-xs">
                    {{ app()->getLocale() === 'ar' ? 'لا توجد وثيقة COA مرفقة.' : 'No COA document uploaded.' }}
                </div>
            @endif
        </div>

        <!-- Inspection Remarks & Waiver -->
        <div class="card p-5 border border-gray-100 dark:border-gray-800 shadow-sm space-y-3">
            <h4 class="text-xs font-black uppercase text-gray-500 tracking-wider border-b border-gray-100 dark:border-gray-800 pb-2">
                {{ app()->getLocale() === 'ar' ? 'ملاحظات وتوجيهات الإفراج' : 'Analyst Remarks & Release Scope' }}
            </h4>
            <div class="text-xs text-slate-700 dark:text-gray-300 space-y-2">
                <p>{{ $qcInspection->notes ?? (app()->getLocale() === 'ar' ? 'لا توجد ملاحظات إضافية مسجلة.' : 'All analytical parameters conform to release monograph.') }}</p>
                @if($qcInspection->verdict === \App\Enums\QcVerdict::ConditionalPass)
                    <div class="p-2.5 rounded bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800 text-amber-900 dark:text-amber-200 text-[11px]">
                        <strong>{{ app()->getLocale() === 'ar' ? 'إفراج مشروط:' : 'Conditional Waiver:' }}</strong> {{ app()->getLocale() === 'ar' ? 'صالح لمدة محددة تحت إشراف مدير الجودة.' : 'Released with temporary expiry-limited waiver under QA supervision.' }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Analytical Parameter Grid -->
    <div class="card p-5 mb-6 border border-gray-100 dark:border-gray-800 shadow-sm">
        <h4 class="text-sm font-bold text-slate-800 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-3 mb-4 flex items-center justify-between">
            <span><i class="fa-solid fa-list-check text-purple-600 mr-2 ml-2"></i> {{ app()->getLocale() === 'ar' ? 'نتائج الفحص المخبري التحليلي' : 'Analytical Parameter Test Results' }}</span>
            <span class="badge badge-purple text-xs">{{ $qcInspection->parameters->count() }} {{ app()->getLocale() === 'ar' ? 'اختبار' : 'Tests' }}</span>
        </h4>

        <div class="table-responsive">
            <table class="table text-xs">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('qc.parameter_name') }}</th>
                        <th>{{ __('qc.test_method') }}</th>
                        <th>{{ __('qc.specification') }}</th>
                        <th>{{ __('qc.observed_result') }}</th>
                        <th style="text-align: center;">{{ __('qc.status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($qcInspection->parameters as $index => $param)
                        <tr>
                            <td class="font-mono text-gray-400">{{ $index + 1 }}</td>
                            <td class="font-bold text-slate-800 dark:text-white">{{ $param->parameter_name }}</td>
                            <td class="font-mono text-gray-500">{{ $param->test_method ?? 'In-house' }}</td>
                            <td class="font-mono">{{ $param->specification }}</td>
                            <td class="font-mono font-bold text-slate-800 dark:text-white">{{ $param->result_value }}</td>
                            <td style="text-align: center;">
                                @if($param->is_passed)
                                    <span class="badge badge-emerald text-[11px] font-bold">
                                        <i class="fa-solid fa-check mr-1 ml-1"></i> PASS
                                    </span>
                                @else
                                    <span class="badge badge-rose text-[11px] font-bold">
                                        <i class="fa-solid fa-xmark mr-1 ml-1"></i> FAIL
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
