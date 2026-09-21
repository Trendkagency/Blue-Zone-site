<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'العمل الإضافي (Overtime)' : 'Overtime Requests & Approvals'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'إدارة واعتماد ساعات العمل الإضافي وتجهيزها لمسيرات الرواتب' : 'Track, approve and integrate overtime hours into payroll compensation'"
>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0; color: var(--text-color, #1E293B);">
                <i class="fa-solid fa-clock-rotate-left text-purple-500 mr-2 ml-2"></i> {{ app()->getLocale() === 'ar' ? 'طلبات العمل الإضافي' : 'Overtime Requests' }}
            </h2>
        </div>
        <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('addOtModal').style.display='flex'">
            <i class="fa-solid fa-plus text-xs"></i> Request Overtime
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="padding: 0.75rem 1rem; margin-bottom: 1rem; border-radius: 0.5rem; background: #DCFCE7; color: #166534; border: 1px solid #BBF7D0;">
            {{ session('success') }}
        </div>
    @endif

    <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
        <div class="table-responsive">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                <thead>
                    <tr style="border-bottom: 2px solid #E2E8F0; text-align: left; color: #64748B;">
                        <th style="padding: 0.75rem 0.5rem;">Date</th>
                        <th style="padding: 0.75rem 0.5rem;">Employee</th>
                        <th style="padding: 0.75rem 0.5rem;">Time Interval</th>
                        <th style="padding: 0.75rem 0.5rem;">Duration</th>
                        <th style="padding: 0.75rem 0.5rem;">Reason</th>
                        <th style="padding: 0.75rem 0.5rem;">Status</th>
                        <th style="padding: 0.75rem 0.5rem; text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($overtimeRequests as $ot)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.75rem 0.5rem; font-weight: 600;">{{ $ot->date }}</td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <a href="{{ route('admin.hr.employees.show', $ot->employee_id) }}" style="color: #0284C7; font-weight: 700; text-decoration: none;">
                                    {{ $ot->employee?->full_name ?? 'N/A' }}
                                </a>
                                <span style="font-size: 0.75rem; color: #64748B; display: block; font-family: monospace;">{{ $ot->employee?->employee_number }}</span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; font-family: monospace;">
                                {{ substr($ot->start_time, 0, 5) }} &ndash; {{ substr($ot->end_time, 0, 5) }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem; font-weight: 700; color: #7C3AED;">
                                {{ round($ot->minutes / 60, 1) }} hrs ({{ $ot->minutes }}m)
                            </td>
                            <td style="padding: 0.75rem 0.5rem; color: #64748B;">{{ $ot->reason ?? '—' }}</td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <span class="badge" style="background: {{ $ot->status === 'approved' ? '#DCFCE7' : ($ot->status === 'pending' ? '#FEF3C7' : '#FEE2E2') }}; color: {{ $ot->status === 'approved' ? '#166534' : ($ot->status === 'pending' ? '#92400E' : '#991B1B') }}; padding: 0.2rem 0.5rem; border-radius: 999px; font-weight: 700;">
                                    {{ ucfirst($ot->status) }}
                                </span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; text-align: right;">
                                @if($ot->status === 'pending')
                                    <form action="{{ route('admin.hr.attendance.overtime.approve', $ot->id) }}" method="POST" style="display: inline-block;">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-xs">
                                            <i class="fa-solid fa-check mr-1"></i> Approve
                                        </button>
                                    </form>
                                @else
                                    <span style="color: #64748B; font-size: 0.75rem;">Processed</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 3rem; color: #94A3B8;">No overtime requests registered.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 1rem;">
            {{ $overtimeRequests->links() }}
        </div>
    </div>

    <!-- Request Overtime Modal -->
    <div id="addOtModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: #ffffff; border-radius: 0.75rem; padding: 1.5rem; width: 100%; max-width: 480px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0;">Submit Overtime</h3>
                <button type="button" onclick="document.getElementById('addOtModal').style.display='none'" style="background: none; border: none; font-size: 1.25rem; cursor: pointer;">&times;</button>
            </div>
            <form action="{{ route('admin.hr.attendance.overtime.store') }}" method="POST">
                @csrf
                <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Employee *</label>
                        <select name="employee_id" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->full_name }} ({{ $emp->employee_number }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Date *</label>
                        <input type="date" name="date" value="{{ now()->toDateString() }}" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Start Time *</label>
                            <input type="time" name="start_time" value="17:00" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        </div>
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">End Time *</label>
                            <input type="time" name="end_time" value="19:00" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        </div>
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Minutes *</label>
                        <input type="number" name="minutes" value="120" min="15" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Justification / Project Reason</label>
                        <input type="text" name="reason" placeholder="e.g. Month-end inventory audit" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                    </div>
                    <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1rem;">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('addOtModal').style.display='none'">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">Submit Request</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
