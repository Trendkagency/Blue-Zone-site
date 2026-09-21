<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'سجل الحضور اليومي' : 'Daily Attendance Roll'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'متابعة مباشرة لتسجيل الحضور والانصراف وحالات التأخير' : 'Live attendance check-in, check-out and punctuality tracking'"
>
    <!-- Header with Date Selector -->
    <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0; color: var(--text-color, #1E293B);">
                <i class="fa-solid fa-clipboard-user text-emerald-500 mr-2 ml-2"></i> {{ app()->getLocale() === 'ar' ? 'سجل الحضور اليومي' : 'Daily Roll Sheet' }}
            </h2>
            <span style="font-size: 0.85rem; color: #64748B;">Date: <strong>{{ $targetDate }}</strong></span>
        </div>
        <form action="{{ route('admin.hr.attendance.daily') }}" method="GET" style="display: flex; gap: 0.5rem; align-items: center;">
            <input type="date" name="date" value="{{ $targetDate }}" class="form-control" style="padding: 0.4rem 0.6rem; border: 1px solid #CBD5E1; border-radius: 0.375rem; font-size: 0.85rem;">
            <button type="submit" class="btn btn-secondary btn-sm">Switch Date</button>
            <a href="{{ route('admin.hr.attendance.index') }}" class="btn btn-outline btn-sm">Full History</a>
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="padding: 0.75rem 1rem; margin-bottom: 1rem; border-radius: 0.5rem; background: #DCFCE7; color: #166534; border: 1px solid #BBF7D0;">
            {{ session('success') }}
        </div>
    @endif

    <!-- 3 Mini Summary KPI Badges -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
        <div class="card" style="padding: 1rem; border-radius: 0.5rem; background: #ECFDF5; border: 1px solid #A7F3D0; text-align: center;">
            <span style="font-size: 0.8rem; color: #065F46; font-weight: 600;">Present Today</span>
            <div style="font-size: 1.75rem; font-weight: 800; color: #059669;">{{ $presentCount }}</div>
        </div>
        <div class="card" style="padding: 1rem; border-radius: 0.5rem; background: #FFFBEB; border: 1px solid #FDE68A; text-align: center;">
            <span style="font-size: 0.8rem; color: #92400E; font-weight: 600;">Late Arrivals</span>
            <div style="font-size: 1.75rem; font-weight: 800; color: #D97706;">{{ $lateCount }}</div>
        </div>
        <div class="card" style="padding: 1rem; border-radius: 0.5rem; background: #FEF2F2; border: 1px solid #FECACA; text-align: center;">
            <span style="font-size: 0.8rem; color: #991B1B; font-weight: 600;">Absent</span>
            <div style="font-size: 1.75rem; font-weight: 800; color: #DC2626;">{{ $absentCount }}</div>
        </div>
    </div>

    <!-- Active Employees Roll Table -->
    <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
        <div class="table-responsive">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                <thead>
                    <tr style="border-bottom: 2px solid #E2E8F0; text-align: left; color: #64748B;">
                        <th style="padding: 0.75rem 0.5rem;">Employee</th>
                        <th style="padding: 0.75rem 0.5rem;">Department / Position</th>
                        <th style="padding: 0.75rem 0.5rem;">Shift</th>
                        <th style="padding: 0.75rem 0.5rem;">Check In</th>
                        <th style="padding: 0.75rem 0.5rem;">Check Out</th>
                        <th style="padding: 0.75rem 0.5rem;">Status</th>
                        <th style="padding: 0.75rem 0.5rem; text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($activeEmployees as $emp)
                        @php
                            $rec = $records->get($emp->id);
                        @endphp
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.75rem 0.5rem;">
                                <a href="{{ route('admin.hr.employees.show', $emp->id) }}" style="font-weight: 700; color: #0284C7; text-decoration: none;">
                                    {{ $emp->full_name }}
                                </a>
                                <span style="font-size: 0.75rem; color: #64748B; display: block; font-family: monospace;">{{ $emp->employee_number }}</span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <div>{{ $emp->department?->name_en ?? '—' }}</div>
                                <div style="font-size: 0.75rem; color: #64748B;">{{ $emp->position?->name_en ?? '—' }}</div>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; color: #475569;">
                                {{ $emp->workSchedule ? substr($emp->workSchedule->start_time, 0, 5) . '-' . substr($emp->workSchedule->end_time, 0, 5) : '09:00-17:00' }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem; font-family: monospace;">
                                {{ $rec && $rec->check_in ? $rec->check_in : '—' }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem; font-family: monospace;">
                                {{ $rec && $rec->check_out ? $rec->check_out : '—' }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem;">
                                @if($rec)
                                    <span class="badge" style="background: {{ $rec->status === 'present' ? '#DCFCE7' : ($rec->status === 'late' ? '#FEF3C7' : '#FEE2E2') }}; color: {{ $rec->status === 'present' ? '#166534' : ($rec->status === 'late' ? '#92400E' : '#991B1B') }}; padding: 0.2rem 0.5rem; border-radius: 999px; font-weight: 700;">
                                        {{ ucfirst($rec->status) }}
                                        @if($rec->late_minutes > 0) ({{ $rec->late_minutes }}m) @endif
                                    </span>
                                @else
                                    <span class="badge" style="background: #FEE2E2; color: #991B1B; padding: 0.2rem 0.5rem; border-radius: 999px;">
                                        Absent
                                    </span>
                                @endif
                            </td>
                            <td style="padding: 0.75rem 0.5rem; text-align: right;">
                                @if(!$rec || empty($rec->check_in))
                                    <form action="{{ route('admin.hr.attendance.check-in') }}" method="POST" style="display: inline-block;">
                                        @csrf
                                        <input type="hidden" name="employee_id" value="{{ $emp->id }}">
                                        <input type="hidden" name="attendance_date" value="{{ $targetDate }}">
                                        <button type="submit" class="btn btn-primary btn-xs">
                                            <i class="fa-solid fa-arrow-right-to-bracket mr-1"></i> Check In
                                        </button>
                                    </form>
                                @elseif(empty($rec->check_out))
                                    <form action="{{ route('admin.hr.attendance.check-out') }}" method="POST" style="display: inline-block;">
                                        @csrf
                                        <input type="hidden" name="employee_id" value="{{ $emp->id }}">
                                        <input type="hidden" name="attendance_date" value="{{ $targetDate }}">
                                        <button type="submit" class="btn btn-secondary btn-xs">
                                            <i class="fa-solid fa-arrow-right-from-bracket mr-1"></i> Check Out
                                        </button>
                                    </form>
                                @else
                                    <span style="color: #10B981; font-weight: 700; font-size: 0.75rem;"><i class="fa-solid fa-check-double"></i> Done</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
