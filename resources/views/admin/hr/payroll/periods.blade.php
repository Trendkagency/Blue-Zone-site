<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'فترات ومسيرات الرواتب الشهرية' : 'Monthly Payroll Periods'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'إنشاء دورات الرواتب الشهرية، احتساب المستحقات، وقفل المسيرات' : 'Initialize payroll batches, trigger automated salary calculations, and lock periods'"
>
    <!-- Header & Action Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0; color: var(--text-color, #1E293B);">
                <i class="fa-solid fa-calendar-days text-indigo-500 mr-2 ml-2"></i> 
                {{ app()->getLocale() === 'ar' ? 'دورات الرواتب الشهرية' : 'Payroll Periods Batches' }}
            </h2>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('admin.hr.payroll.index') }}" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-arrow-left mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'سجل الرواتب' : 'Payroll Ledger' }}
            </a>
            <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('newPeriodModal').style.display='flex'">
                <i class="fa-solid fa-plus mr-1 ml-1 text-xs"></i> {{ app()->getLocale() === 'ar' ? 'تهيئة دورة رواتب جديدة' : 'Initialize Payroll Period' }}
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="padding: 0.75rem 1rem; margin-bottom: 1rem; border-radius: 0.5rem; background: #DCFCE7; color: #166534; border: 1px solid #BBF7D0;">
            <i class="fa-solid fa-circle-check mr-1 ml-1"></i> {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger" style="padding: 0.75rem 1rem; margin-bottom: 1rem; border-radius: 0.5rem; background: #FEE2E2; color: #991B1B; border: 1px solid #FECACA;">
            <ul style="margin: 0; padding-left: 1.25rem;">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Periods Grid / Table -->
    <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
        <div class="table-responsive">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                <thead>
                    <tr style="border-bottom: 2px solid #E2E8F0; text-align: left; color: #64748B;">
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'اسم الدورة / الفترة' : 'Batch / Period Name' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'تاريخ البداية' : 'Start Date' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'تاريخ النهاية' : 'End Date' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'يوم الصرف' : 'Payment Date' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'عدد المسيرات' : 'Records Count' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'حالة الدورة' : 'Batch Status' }}</th>
                        <th style="padding: 0.75rem 0.5rem; text-align: right;">{{ app()->getLocale() === 'ar' ? 'الإجراءات والعمليات' : 'Processing Actions' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($periods as $period)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.75rem 0.5rem;">
                                <div style="font-weight: 700; color: #0F172A; font-size: 0.95rem;">{{ $period->name }}</div>
                                <span style="font-size: 0.75rem; color: #64748B;">ID: #{{ $period->id }}</span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; color: #334155;">{{ $period->start_date }}</td>
                            <td style="padding: 0.75rem 0.5rem; color: #334155;">{{ $period->end_date }}</td>
                            <td style="padding: 0.75rem 0.5rem; color: #334155;">{{ $period->payment_date ?? '—' }}</td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <span style="font-weight: 700; color: #0284C7; background: #E0F2FE; padding: 0.2rem 0.6rem; border-radius: 999px; font-size: 0.8rem;">
                                    {{ $period->records_count }} {{ app()->getLocale() === 'ar' ? 'موظف' : 'slips' }}
                                </span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem;">
                                @php
                                    $pBg = match($period->status) {
                                        'finalized' => '#DCFCE7',
                                        'approved' => '#E0E7FF',
                                        'processing' => '#FEF3C7',
                                        default => '#F1F5F9',
                                    };
                                    $pColor = match($period->status) {
                                        'finalized' => '#166534',
                                        'approved' => '#3730A3',
                                        'processing' => '#92400E',
                                        default => '#475569',
                                    };
                                @endphp
                                <span class="badge" style="background: {{ $pBg }}; color: {{ $pColor }}; padding: 0.25rem 0.65rem; border-radius: 999px; font-weight: 700; font-size: 0.75rem;">
                                    {{ ucfirst($period->status) }}
                                </span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; text-align: right;">
                                <div style="display: inline-flex; gap: 0.4rem; align-items: center;">
                                    @if($period->status !== 'finalized')
                                        <!-- Generate Payroll -->
                                        <form action="{{ route('admin.hr.payroll.periods.generate', $period->id) }}" method="POST" onsubmit="return confirm('{{ app()->getLocale() === 'ar' ? 'هل أنت متأكد من بدء احتساب الرواتب لهذه الفترة؟ سيتم سحب الحضور والبدلات والسلف.' : 'Calculate and compile payroll records for active employees in this period?' }}');">
                                            @csrf
                                            <button type="submit" class="btn btn-primary btn-xs" style="background: #2563EB; border-color: #2563EB;">
                                                <i class="fa-solid fa-calculator mr-1"></i> {{ app()->getLocale() === 'ar' ? 'احتساب' : 'Compute' }}
                                            </button>
                                        </form>
                                        <!-- Finalize Payroll -->
                                        <form action="{{ route('admin.hr.payroll.periods.finalize', $period->id) }}" method="POST" onsubmit="return confirm('{{ app()->getLocale() === 'ar' ? 'تحذير: إقفال مسير الرواتب نهائي ولن يمكن التعديل بعده. متابعة؟' : 'Warning: Finalizing this period permanently locks all records and generates payslips. Proceed?' }}');">
                                            @csrf
                                            <button type="submit" class="btn btn-xs" style="background: #059669; color: #fff; border: 1px solid #059669;">
                                                <i class="fa-solid fa-lock mr-1"></i> {{ app()->getLocale() === 'ar' ? 'إقفال واعتماد' : 'Finalize' }}
                                            </button>
                                        </form>
                                    @else
                                        <span style="font-size: 0.75rem; color: #059669; font-weight: 700;">
                                            <i class="fa-solid fa-circle-check"></i> {{ app()->getLocale() === 'ar' ? 'مقفل ومعتمد' : 'Locked & Audited' }}
                                        </span>
                                    @endif
                                    <a href="{{ route('admin.hr.payroll.index', ['payroll_period_id' => $period->id]) }}" class="btn btn-ghost btn-xs text-sky-600" title="{{ app()->getLocale() === 'ar' ? 'عرض السجلات' : 'View Ledger' }}">
                                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 3rem; color: #94A3B8;">
                                <i class="fa-solid fa-calendar-xmark" style="font-size: 2.5rem; margin-bottom: 0.75rem; display: block; opacity: 0.4;"></i>
                                {{ app()->getLocale() === 'ar' ? 'لم يتم العثور على أي دورات رواتب مسجلة حتى الآن.' : 'No payroll periods initialized yet.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 1rem;">
            {{ $periods->links() }}
        </div>
    </div>

    <!-- Initialize New Period Modal -->
    <div id="newPeriodModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: #ffffff; border-radius: 0.75rem; padding: 1.5rem; width: 100%; max-width: 480px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0; color: #0F172A;">
                    <i class="fa-solid fa-calendar-plus text-indigo-500 mr-2"></i> {{ app()->getLocale() === 'ar' ? 'تهيئة دورة رواتب جديدة' : 'Initialize New Payroll Period' }}
                </h3>
                <button type="button" onclick="document.getElementById('newPeriodModal').style.display='none'" style="background: none; border: none; font-size: 1.25rem; cursor: pointer;">&times;</button>
            </div>
            <form action="{{ route('admin.hr.payroll.periods.store') }}" method="POST">
                @csrf
                <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'اسم الدورة الشهرية *' : 'Period Name / Label *' }}</label>
                        <input type="text" name="name" required placeholder="{{ app()->getLocale() === 'ar' ? 'مثال: راتب سبتمبر 2026' : 'e.g. September 2026 Payroll' }}" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'بداية الدورة *' : 'Start Date *' }}</label>
                            <input type="date" name="start_date" required class="form-control" value="{{ now()->startOfMonth()->toDateString() }}" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        </div>
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'نهاية الدورة *' : 'End Date *' }}</label>
                            <input type="date" name="end_date" required class="form-control" value="{{ now()->endOfMonth()->toDateString() }}" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        </div>
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'تاريخ الصرف المستهدف' : 'Target Disbursement Date' }}</label>
                        <input type="date" name="payment_date" class="form-control" value="{{ now()->endOfMonth()->toDateString() }}" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                    </div>
                    <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1rem;">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('newPeriodModal').style.display='none'">{{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}</button>
                        <button type="submit" class="btn btn-primary btn-sm">{{ app()->getLocale() === 'ar' ? 'حفظ وتهيئة' : 'Create & Initialize' }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
