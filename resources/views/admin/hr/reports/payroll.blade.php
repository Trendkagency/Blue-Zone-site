<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'تقرير تكاليف ومسيرات الرواتب' : 'Payroll Expenditure & Compensation Report'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'تحليل تكاليف الأجور الشهرية، إجمالي المستحقات والاستقطاعات وصافي الصرف' : 'Comprehensive executive compensation analysis, monthly wage trends, and statutory deductions'"
>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0; color: var(--text-color, #1E293B);">
                <i class="fa-solid fa-money-check-dollar text-emerald-500 mr-2 ml-2"></i> 
                {{ app()->getLocale() === 'ar' ? 'تقرير مسيرات الرواتب' : 'Payroll Cost Audit' }}
            </h2>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('admin.hr.reports.index') }}" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-arrow-left mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'مركز التقارير' : 'Reports Hub' }}
            </a>
            <button type="button" class="btn btn-primary btn-sm" onclick="window.print()">
                <i class="fa-solid fa-print mr-1 ml-1 text-xs"></i> {{ app()->getLocale() === 'ar' ? 'طباعة' : 'Print' }}
            </button>
        </div>
    </div>

    <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
        <div class="table-responsive">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                <thead>
                    <tr style="border-bottom: 2px solid #E2E8F0; text-align: left; color: #64748B;">
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الدورة / الفترة' : 'Batch / Period' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'عدد المسيرات' : 'Headcount' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الرواتب الأساسية' : 'Basic Salaries' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'إجمالي البدلات' : 'Allowances' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الاستقطاعات' : 'Deductions' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'صافي التكلفة المنصرفة' : 'Net Total Payout' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($periods as $p)
                        @php
                            $totalBasic = $p->records->sum('basic_salary');
                            $totalAllowances = $p->records->sum('allowances');
                            $totalDeductions = $p->records->sum('deductions');
                            $totalNet = $p->records->sum('net_salary');
                        @endphp
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.75rem 0.5rem;">
                                <strong style="color: #0F172A;">{{ $p->name }}</strong>
                                <span style="font-size: 0.75rem; color: #64748B; display: block;">{{ $p->start_date }} &ndash; {{ $p->end_date }}</span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <span style="background: #E0F2FE; color: #0369A1; font-weight: 700; font-size: 0.75rem; padding: 0.2rem 0.6rem; border-radius: 999px;">
                                    {{ $p->records->count() }} slips
                                </span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; font-family: monospace; font-weight: 600;">
                                {{ number_format($totalBasic, 2) }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem; font-family: monospace; font-weight: 600; color: #059669;">
                                +{{ number_format($totalAllowances, 2) }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem; font-family: monospace; font-weight: 600; color: #DC2626;">
                                -{{ number_format($totalDeductions, 2) }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <strong style="font-family: monospace; font-size: 1rem; color: #059669;">
                                    {{ number_format($totalNet, 2) }} {{ function_exists('currency_code') ? currency_code() : 'SAR' }}
                                </strong>
                            </td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <span class="badge" style="background: {{ $p->status === 'finalized' ? '#DCFCE7' : '#FEF3C7' }}; color: {{ $p->status === 'finalized' ? '#166534' : '#92400E' }}; padding: 0.2rem 0.5rem; border-radius: 999px; font-weight: 700;">
                                    {{ ucfirst($p->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 3rem; color: #94A3B8;">
                                No payroll periods computed yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
