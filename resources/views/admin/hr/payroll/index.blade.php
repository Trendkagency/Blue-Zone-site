<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'سجل الرواتب والأجور' : 'Payroll Ledger & Records'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'متابعة مسيرات الرواتب، البدلات، الاستقطاعات وصافي الأجور للموظفين' : 'Monitor monthly employee payroll calculations, allowances, deductions and net pay'"
>
    <!-- Header & Action Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0; color: var(--text-color, #1E293B);">
                <i class="fa-solid fa-money-check-dollar text-emerald-500 mr-2 ml-2"></i> 
                {{ app()->getLocale() === 'ar' ? 'مسيرات الرواتب' : 'Payroll Records' }}
            </h2>
        </div>
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <a href="{{ route('admin.hr.payroll.periods') }}" class="btn btn-outline btn-sm" style="border-color: #CBD5E1;">
                <i class="fa-solid fa-calendar-days mr-1 ml-1 text-sky-500"></i> {{ app()->getLocale() === 'ar' ? 'الفترات الشهرية' : 'Payroll Periods' }}
            </a>
            <a href="{{ route('admin.hr.payroll.payslips') }}" class="btn btn-outline btn-sm" style="border-color: #CBD5E1;">
                <i class="fa-solid fa-receipt mr-1 ml-1 text-indigo-500"></i> {{ app()->getLocale() === 'ar' ? 'قسائم الرواتب' : 'Payslips' }}
            </a>
            <a href="{{ route('admin.hr.payroll.advances') }}" class="btn btn-outline btn-sm" style="border-color: #CBD5E1;">
                <i class="fa-solid fa-hand-holding-dollar mr-1 ml-1 text-amber-500"></i> {{ app()->getLocale() === 'ar' ? 'السلف' : 'Advances' }}
            </a>
            <a href="{{ route('admin.hr.payroll.loans') }}" class="btn btn-outline btn-sm" style="border-color: #CBD5E1;">
                <i class="fa-solid fa-landmark mr-1 ml-1 text-purple-500"></i> {{ app()->getLocale() === 'ar' ? 'القروض' : 'Loans' }}
            </a>
            <a href="{{ route('admin.hr.payroll.structures') }}" class="btn btn-secondary btn-sm">
                <i class="fa-solid fa-sliders mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'هيكل الرواتب' : 'Structures' }}
            </a>
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

    <!-- Filters & Search Bar -->
    <div class="card" style="padding: 1rem 1.25rem; margin-bottom: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
        <form action="{{ route('admin.hr.payroll.index') }}" method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 1; min-width: 200px;">
                <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem; color: #64748B;">
                    {{ app()->getLocale() === 'ar' ? 'البحث عن موظف' : 'Search Employee' }}
                </label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ app()->getLocale() === 'ar' ? 'الاسم أو الرقم الوظيفي...' : 'Name or Employee #...' }}" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
            </div>
            <div style="min-width: 200px;">
                <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem; color: #64748B;">
                    {{ app()->getLocale() === 'ar' ? 'الفترة الشهرية' : 'Payroll Period' }}
                </label>
                <select name="payroll_period_id" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                    <option value="">{{ app()->getLocale() === 'ar' ? 'كل الفترات' : 'All Periods' }}</option>
                    @foreach($periods as $period)
                        <option value="{{ $period->id }}" {{ request('payroll_period_id') == $period->id ? 'selected' : '' }}>
                            {{ $period->name }} ({{ $period->start_date }} &ndash; {{ $period->end_date }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-primary btn-sm" style="height: 38px;">
                    <i class="fa-solid fa-filter mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'تصفية' : 'Filter' }}
                </button>
                @if(request()->hasAny(['search', 'payroll_period_id']))
                    <a href="{{ route('admin.hr.payroll.index') }}" class="btn btn-ghost btn-sm" style="height: 38px; display: inline-flex; align-items: center;">
                        <i class="fa-solid fa-rotate-left mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'إعادة ضبط' : 'Reset' }}
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Payroll Records Ledger Table -->
    <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
        <div class="table-responsive">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                <thead>
                    <tr style="border-bottom: 2px solid #E2E8F0; text-align: left; color: #64748B;">
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الموظف' : 'Employee' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الفترة' : 'Period' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الراتب الأساسي' : 'Basic Salary' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'إجمالي البدلات' : 'Allowances' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الاستقطاعات' : 'Deductions' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'صافي الراتب' : 'Net Pay' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                        <th style="padding: 0.75rem 0.5rem; text-align: right;">{{ app()->getLocale() === 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $rec)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.75rem 0.5rem;">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div style="width: 34px; height: 34px; border-radius: 50%; background: #EEF2FF; color: #4F46E5; display: flex; align-items: center; justify-content: center; font-weight: 700;">
                                        {{ substr($rec->employee?->first_name ?? 'E', 0, 1) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.hr.employees.show', $rec->employee_id) }}" style="color: #0284C7; font-weight: 700; text-decoration: none;">
                                            {{ $rec->employee?->full_name ?? 'N/A' }}
                                        </a>
                                        <span style="font-size: 0.75rem; color: #64748B; display: block; font-family: monospace;">
                                            {{ $rec->employee?->employee_number }} &bull; {{ $rec->employee?->department?->name ?? 'Dept' }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; color: #1E293B; font-weight: 600;">
                                {{ $rec->period?->name ?? 'Current Period' }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem; font-family: monospace; font-weight: 600;">
                                {{ number_format($rec->basic_salary, 2) }} {{ function_exists('currency_code') ? currency_code() : 'SAR' }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem; color: #059669; font-family: monospace; font-weight: 600;">
                                +{{ number_format($rec->allowances ?? 0, 2) }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem; color: #DC2626; font-family: monospace; font-weight: 600;">
                                -{{ number_format($rec->deductions ?? 0, 2) }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <span style="font-size: 0.95rem; font-weight: 800; color: #059669; font-family: monospace;">
                                    {{ number_format($rec->net_salary, 2) }} {{ function_exists('currency_code') ? currency_code() : 'SAR' }}
                                </span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem;">
                                @php
                                    $statusBg = match($rec->status) {
                                        'paid' => '#DCFCE7',
                                        'approved' => '#E0E7FF',
                                        'draft' => '#F1F5F9',
                                        default => '#FEF3C7',
                                    };
                                    $statusColor = match($rec->status) {
                                        'paid' => '#166534',
                                        'approved' => '#3730A3',
                                        'draft' => '#475569',
                                        default => '#92400E',
                                    };
                                @endphp
                                <span class="badge" style="background: {{ $statusBg }}; color: {{ $statusColor }}; padding: 0.25rem 0.6rem; border-radius: 999px; font-weight: 700; font-size: 0.75rem;">
                                    {{ ucfirst($rec->status) }}
                                </span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; text-align: right;">
                                <a href="{{ route('admin.hr.payroll.payslips', ['payroll_period_id' => $rec->payroll_period_id]) }}" class="btn btn-ghost btn-xs" title="{{ app()->getLocale() === 'ar' ? 'عرض القسيمة' : 'View Payslip' }}">
                                    <i class="fa-solid fa-receipt text-sky-500"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 3rem; color: #94A3B8;">
                                <i class="fa-solid fa-coins" style="font-size: 2.5rem; margin-bottom: 0.75rem; display: block; opacity: 0.4;"></i>
                                {{ app()->getLocale() === 'ar' ? 'لا توجد سجلات رواتب مسجلة لهذه المعايير.' : 'No payroll ledger records found matching the criteria.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 1rem;">
            {{ $records->links() }}
        </div>
    </div>
</x-layouts.admin>
