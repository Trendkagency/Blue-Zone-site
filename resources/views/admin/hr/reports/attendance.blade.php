<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'تقرير الحضور والانصراف التفصيلي' : 'Attendance & Punctuality Audit Report'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'تحليل معدلات الحضور، ساعات التأخير، والغياب للفترة المحددة' : 'Audited timesheet logs, punctual check-ins, tardiness metrics, and absence trends'"
>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0; color: var(--text-color, #1E293B);">
                <i class="fa-solid fa-business-time text-teal-500 mr-2 ml-2"></i> 
                {{ app()->getLocale() === 'ar' ? 'تقرير الحضور والانصراف' : 'Attendance Audit Log' }}
            </h2>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('admin.hr.reports.index') }}" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-arrow-left mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'مركز التقارير' : 'Reports Hub' }}
            </a>
            <button type="button" class="btn btn-primary btn-sm" onclick="window.print()">
                <i class="fa-solid fa-print mr-1 ml-1 text-xs"></i> {{ app()->getLocale() === 'ar' ? 'طباعة / تصدير' : 'Print / Export' }}
            </button>
        </div>
    </div>

    <!-- Stats Bar -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 1rem; margin-bottom: 1.25rem;">
        <div class="card" style="padding: 1rem; border-radius: 0.5rem; background: #ffffff; border: 1px solid #E2E8F0; text-align: center;">
            <span style="font-size: 0.75rem; color: #64748B; font-weight: 600; display: block;">{{ app()->getLocale() === 'ar' ? 'حاضر (Present)' : 'Present' }}</span>
            <strong style="font-size: 1.25rem; color: #166534;">{{ $stats['present'] ?? 0 }}</strong>
        </div>
        <div class="card" style="padding: 1rem; border-radius: 0.5rem; background: #ffffff; border: 1px solid #E2E8F0; text-align: center;">
            <span style="font-size: 0.75rem; color: #64748B; font-weight: 600; display: block;">{{ app()->getLocale() === 'ar' ? 'متأخر (Late)' : 'Late' }}</span>
            <strong style="font-size: 1.25rem; color: #D97706;">{{ $stats['late'] ?? 0 }}</strong>
        </div>
        <div class="card" style="padding: 1rem; border-radius: 0.5rem; background: #ffffff; border: 1px solid #E2E8F0; text-align: center;">
            <span style="font-size: 0.75rem; color: #64748B; font-weight: 600; display: block;">{{ app()->getLocale() === 'ar' ? 'غائب (Absent)' : 'Absent' }}</span>
            <strong style="font-size: 1.25rem; color: #DC2626;">{{ $stats['absent'] ?? 0 }}</strong>
        </div>
    </div>

    <!-- Date Filter -->
    <div class="card" style="padding: 1rem 1.25rem; margin-bottom: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
        <form action="{{ route('admin.hr.reports.attendance') }}" method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            <div>
                <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem; color: #64748B;">{{ app()->getLocale() === 'ar' ? 'من تاريخ' : 'Start Date' }}</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="form-control" style="padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
            </div>
            <div>
                <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem; color: #64748B;">{{ app()->getLocale() === 'ar' ? 'إلى تاريخ' : 'End Date' }}</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="form-control" style="padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
            </div>
            <div>
                <button type="submit" class="btn btn-primary btn-sm" style="height: 38px;">
                    <i class="fa-solid fa-filter mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'تحديث التقرير' : 'Update Report' }}
                </button>
            </div>
        </form>
    </div>

    <!-- Attendance Table -->
    <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
        <div class="table-responsive">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                <thead>
                    <tr style="border-bottom: 2px solid #E2E8F0; text-align: left; color: #64748B;">
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'التاريخ' : 'Date' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الموظف' : 'Employee' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'القسم' : 'Department' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'وقت الدخول' : 'Check In' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'وقت الخروج' : 'Check Out' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'ساعات العمل' : 'Worked' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $rec)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.75rem 0.5rem; font-weight: 600; color: #334155;">{{ $rec->attendance_date }}</td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <a href="{{ route('admin.hr.employees.show', $rec->employee_id) }}" style="color: #0284C7; font-weight: 700; text-decoration: none;">
                                    {{ $rec->employee?->full_name ?? 'N/A' }}
                                </a>
                                <span style="font-size: 0.75rem; color: #64748B; display: block;">{{ $rec->employee?->employee_number }}</span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; color: #334155;">{{ $rec->employee?->department?->name }}</td>
                            <td style="padding: 0.75rem 0.5rem; font-family: monospace; color: #059669;">{{ $rec->check_in ?? '—' }}</td>
                            <td style="padding: 0.75rem 0.5rem; font-family: monospace; color: #0284C7;">{{ $rec->check_out ?? '—' }}</td>
                            <td style="padding: 0.75rem 0.5rem; font-family: monospace; font-weight: 600;">
                                {{ round(($rec->worked_minutes ?? 0) / 60, 1) }} hrs
                            </td>
                            <td style="padding: 0.75rem 0.5rem;">
                                @php
                                    $sBg = match($rec->status) {
                                        'present' => '#DCFCE7',
                                        'late' => '#FEF3C7',
                                        'absent' => '#FEE2E2',
                                        default => '#F1F5F9',
                                    };
                                    $sCol = match($rec->status) {
                                        'present' => '#166534',
                                        'late' => '#92400E',
                                        'absent' => '#991B1B',
                                        default => '#475569',
                                    };
                                @endphp
                                <span class="badge" style="background: {{ $sBg }}; color: {{ $sCol }}; padding: 0.2rem 0.5rem; border-radius: 999px; font-weight: 700;">
                                    {{ ucfirst($rec->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 3rem; color: #94A3B8;">
                                No attendance records found for this timeframe.
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
