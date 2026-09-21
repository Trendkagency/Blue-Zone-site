<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'قسائم الرواتب الإلكترونية' : 'Employee Payslips Directory'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'استعراض وتحميل قسائم رواتب الموظفين الشهرية المفصلة' : 'Browse, review, and print itemized monthly compensation statements'"
>
    <!-- Action Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0; color: var(--text-color, #1E293B);">
                <i class="fa-solid fa-receipt text-indigo-500 mr-2 ml-2"></i> 
                {{ app()->getLocale() === 'ar' ? 'قسائم الرواتب الصادرة' : 'Issued Employee Payslips' }}
            </h2>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('admin.hr.payroll.index') }}" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-money-check-dollar mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'سجل الرواتب' : 'Payroll Ledger' }}
            </a>
            <a href="{{ route('admin.hr.payroll.periods') }}" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-calendar-days mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'الفترات الشهرية' : 'Payroll Periods' }}
            </a>
        </div>
    </div>

    <!-- Filter by Period -->
    <div class="card" style="padding: 1rem 1.25rem; margin-bottom: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
        <form action="{{ route('admin.hr.payroll.payslips') }}" method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 1; min-width: 240px;">
                <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem; color: #64748B;">
                    {{ app()->getLocale() === 'ar' ? 'اختر دورة الرواتب' : 'Filter by Payroll Period' }}
                </label>
                <select name="payroll_period_id" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                    <option value="">{{ app()->getLocale() === 'ar' ? 'كل الدورات' : 'All Periods' }}</option>
                    @foreach($periods as $period)
                        <option value="{{ $period->id }}" {{ request('payroll_period_id') == $period->id ? 'selected' : '' }}>
                            {{ $period->name }} ({{ $period->start_date }} &ndash; {{ $period->end_date }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="submit" class="btn btn-primary btn-sm" style="height: 38px;">
                    <i class="fa-solid fa-filter mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'عرض القسائم' : 'Filter Slips' }}
                </button>
            </div>
        </form>
    </div>

    <!-- Payslips List -->
    <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
        <div class="table-responsive">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                <thead>
                    <tr style="border-bottom: 2px solid #E2E8F0; text-align: left; color: #64748B;">
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'رقم القسيمة' : 'Slip #' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الموظف' : 'Employee' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الفترة' : 'Period' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'إجمالي الراتب' : 'Gross Pay' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الاستقطاعات' : 'Deductions' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'صافي المبلغ المستحق' : 'Net Disbursement' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'تاريخ التوليد' : 'Generated' }}</th>
                        <th style="padding: 0.75rem 0.5rem; text-align: right;">{{ app()->getLocale() === 'ar' ? 'معاينة وطباعة' : 'View / Print' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payslips as $slip)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.75rem 0.5rem; font-family: monospace; font-weight: 700; color: #475569;">
                                {{ $slip->payslip_number ?? 'SLIP-'.str_pad($slip->id, 5, '0', STR_PAD_LEFT) }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <div style="font-weight: 700; color: #0284C7;">{{ $slip->employee?->full_name ?? 'N/A' }}</div>
                                <span style="font-size: 0.75rem; color: #64748B;">
                                    {{ $slip->employee?->employee_number }} &bull; {{ $slip->employee?->position?->name ?? 'Position' }}
                                </span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; color: #334155; font-weight: 600;">
                                {{ $slip->period?->name ?? 'Period' }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem; font-family: monospace; font-weight: 600; color: #334155;">
                                {{ number_format($slip->gross_salary, 2) }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem; font-family: monospace; font-weight: 600; color: #DC2626;">
                                -{{ number_format($slip->total_deductions, 2) }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <span style="font-weight: 800; font-family: monospace; color: #059669; font-size: 0.95rem;">
                                    {{ number_format($slip->net_salary, 2) }} {{ function_exists('currency_code') ? currency_code() : 'SAR' }}
                                </span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; color: #64748B;">
                                {{ $slip->created_at?->format('Y-m-d') }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem; text-align: right;">
                                <a href="{{ route('admin.hr.payroll.payslips.show', $slip->id) }}" class="btn btn-primary btn-xs" target="_blank">
                                    <i class="fa-solid fa-file-invoice-dollar mr-1"></i> {{ app()->getLocale() === 'ar' ? 'عرض القسيمة' : 'View Payslip' }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 3rem; color: #94A3B8;">
                                <i class="fa-solid fa-receipt" style="font-size: 2.5rem; margin-bottom: 0.75rem; display: block; opacity: 0.4;"></i>
                                {{ app()->getLocale() === 'ar' ? 'لم تصدر قسائم رواتب حتى الآن لهذه الدورة.' : 'No payslips generated for this period yet.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 1rem;">
            {{ $payslips->links() }}
        </div>
    </div>
</x-layouts.admin>
