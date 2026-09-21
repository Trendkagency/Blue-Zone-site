<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'سجلات الحضور الكاملة' : 'Attendance Records'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'أرشيف وسجلات الحضور والانصراف وساعات العمل لجميع الموظفين' : 'Comprehensive attendance logs, hours worked, and punctuality audit'"
>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0; color: var(--text-color, #1E293B);">
                <i class="fa-solid fa-list-check text-sky-500 mr-2 ml-2"></i> {{ app()->getLocale() === 'ar' ? 'سجلات الحضور' : 'Attendance History' }}
            </h2>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('admin.hr.attendance.daily') }}" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-clipboard-user text-xs mr-1 ml-1"></i> Today's Live Roll
            </a>
            <a href="{{ route('admin.hr.attendance.overtime') }}" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-clock-rotate-left text-xs mr-1 ml-1"></i> Overtime
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card" style="padding: 1rem 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0); margin-bottom: 1.5rem;">
        <form action="{{ route('admin.hr.attendance.index') }}" method="GET" style="display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: flex-end;">
            <div style="flex: 1; min-width: 200px;">
                <label style="font-size: 0.75rem; font-weight: 600; color: #64748B; display: block; margin-bottom: 0.25rem;">Search Employee</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, EMP ID..." class="form-control" style="width: 100%; padding: 0.45rem 0.75rem; border: 1px solid #CBD5E1; border-radius: 0.375rem; font-size: 0.85rem;">
            </div>

            <div style="width: 160px;">
                <label style="font-size: 0.75rem; font-weight: 600; color: #64748B; display: block; margin-bottom: 0.25rem;">Date</label>
                <input type="date" name="date" value="{{ request('date') }}" class="form-control" style="width: 100%; padding: 0.45rem 0.75rem; border: 1px solid #CBD5E1; border-radius: 0.375rem; font-size: 0.85rem;">
            </div>

            <div style="width: 150px;">
                <label style="font-size: 0.75rem; font-weight: 600; color: #64748B; display: block; margin-bottom: 0.25rem;">Status</label>
                <select name="status" class="form-control" style="width: 100%; padding: 0.45rem 0.75rem; border: 1px solid #CBD5E1; border-radius: 0.375rem; font-size: 0.85rem;">
                    <option value="">All Statuses</option>
                    <option value="present" {{ request('status') === 'present' ? 'selected' : '' }}>Present</option>
                    <option value="late" {{ request('status') === 'late' ? 'selected' : '' }}>Late</option>
                    <option value="absent" {{ request('status') === 'absent' ? 'selected' : '' }}>Absent</option>
                </select>
            </div>

            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-secondary btn-sm" style="height: 34px;">Filter</button>
                <a href="{{ route('admin.hr.attendance.index') }}" class="btn btn-ghost btn-sm" style="height: 34px;">Clear</a>
            </div>
        </form>
    </div>

    <!-- Attendance Table -->
    <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
        <div class="table-responsive">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                <thead>
                    <tr style="border-bottom: 2px solid #E2E8F0; text-align: left; color: #64748B;">
                        <th style="padding: 0.75rem 0.5rem;">Date</th>
                        <th style="padding: 0.75rem 0.5rem;">Employee</th>
                        <th style="padding: 0.75rem 0.5rem;">Department</th>
                        <th style="padding: 0.75rem 0.5rem;">Check In</th>
                        <th style="padding: 0.75rem 0.5rem;">Check Out</th>
                        <th style="padding: 0.75rem 0.5rem;">Hours</th>
                        <th style="padding: 0.75rem 0.5rem;">Late</th>
                        <th style="padding: 0.75rem 0.5rem;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $rec)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.75rem 0.5rem; font-weight: 600; color: #1E293B;">{{ $rec->attendance_date }}</td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <a href="{{ route('admin.hr.employees.show', $rec->employee_id) }}" style="color: #0284C7; font-weight: 700; text-decoration: none;">
                                    {{ $rec->employee?->full_name ?? 'N/A' }}
                                </a>
                                <span style="font-size: 0.75rem; color: #64748B; display: block; font-family: monospace;">{{ $rec->employee?->employee_number }}</span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem;">{{ $rec->employee?->department?->name_en ?? '—' }}</td>
                            <td style="padding: 0.75rem 0.5rem; font-family: monospace;">{{ $rec->check_in ?? '—' }}</td>
                            <td style="padding: 0.75rem 0.5rem; font-family: monospace;">{{ $rec->check_out ?? '—' }}</td>
                            <td style="padding: 0.75rem 0.5rem; font-weight: 600;">
                                {{ $rec->worked_minutes ? round($rec->worked_minutes / 60, 1) . ' hrs' : '—' }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem; color: {{ $rec->late_minutes > 0 ? '#DC2626' : '#64748B' }}; font-weight: {{ $rec->late_minutes > 0 ? '700' : 'normal' }};">
                                {{ $rec->late_minutes ? $rec->late_minutes . 'm' : '—' }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <span class="badge" style="background: {{ $rec->status === 'present' ? '#DCFCE7' : ($rec->status === 'late' ? '#FEF3C7' : '#FEE2E2') }}; color: {{ $rec->status === 'present' ? '#166534' : ($rec->status === 'late' ? '#92400E' : '#991B1B') }}; padding: 0.2rem 0.5rem; border-radius: 999px; font-weight: 700;">
                                    {{ ucfirst($rec->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 3rem; color: #94A3B8;">No attendance records found matching filters.</td>
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
