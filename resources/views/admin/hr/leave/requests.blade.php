<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'طلبات الإجازات' : 'Leave Requests Management'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'سجل طلبات إجازات الموظفين ومتابعة القرارات' : 'View, create and track all workforce leave applications'"
>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0; color: var(--text-color, #1E293B);">
                <i class="fa-solid fa-calendar-check text-sky-500 mr-2 ml-2"></i> {{ app()->getLocale() === 'ar' ? 'طلبات الإجازات' : 'Leave Requests' }}
            </h2>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('admin.hr.leave.approvals') }}" class="btn btn-outline btn-sm" style="color: #DB2777; border-color: #FBCFE8;">
                <i class="fa-solid fa-stamp text-xs mr-1 ml-1"></i> Pending Approvals Queue
            </a>
            <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('applyLeaveModal').style.display='flex'">
                <i class="fa-solid fa-plus text-xs"></i> Apply For Leave
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="padding: 0.75rem 1rem; margin-bottom: 1rem; border-radius: 0.5rem; background: #DCFCE7; color: #166534; border: 1px solid #BBF7D0;">
            {{ session('success') }}
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

    <!-- Requests Table -->
    <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
        <div class="table-responsive">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                <thead>
                    <tr style="border-bottom: 2px solid #E2E8F0; text-align: left; color: #64748B;">
                        <th style="padding: 0.75rem 0.5rem;">Employee</th>
                        <th style="padding: 0.75rem 0.5rem;">Leave Type</th>
                        <th style="padding: 0.75rem 0.5rem;">Period</th>
                        <th style="padding: 0.75rem 0.5rem;">Days</th>
                        <th style="padding: 0.75rem 0.5rem;">Reason</th>
                        <th style="padding: 0.75rem 0.5rem;">Status</th>
                        <th style="padding: 0.75rem 0.5rem; text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.75rem 0.5rem;">
                                <a href="{{ route('admin.hr.employees.show', $req->employee_id) }}" style="color: #0284C7; font-weight: 700; text-decoration: none;">
                                    {{ $req->employee?->full_name ?? 'N/A' }}
                                </a>
                                <span style="font-size: 0.75rem; color: #64748B; display: block; font-family: monospace;">{{ $req->employee?->employee_number }}</span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; font-weight: 600;">{{ $req->leaveType?->name ?? 'Leave' }}</td>
                            <td style="padding: 0.75rem 0.5rem; color: #1E293B;">{{ $req->start_date }} &ndash; {{ $req->end_date }}</td>
                            <td style="padding: 0.75rem 0.5rem; font-weight: 700; color: #0284C7;">{{ $req->total_days }}d</td>
                            <td style="padding: 0.75rem 0.5rem; color: #64748B;">{{ $req->reason ?? '—' }}</td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <span class="badge" style="background: {{ $req->status === 'approved' ? '#DCFCE7' : ($req->status === 'pending' ? '#FEF3C7' : '#FEE2E2') }}; color: {{ $req->status === 'approved' ? '#166534' : ($req->status === 'pending' ? '#92400E' : '#991B1B') }}; padding: 0.2rem 0.5rem; border-radius: 999px; font-weight: 700;">
                                    {{ ucfirst($req->status) }}
                                </span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; text-align: right;">
                                @if($req->status === 'pending')
                                    <div style="display: inline-flex; gap: 0.25rem;">
                                        <form action="{{ route('admin.hr.leave.requests.approve', $req->id) }}" method="POST" style="display: inline-block;">
                                            @csrf
                                            <button type="submit" class="btn btn-primary btn-xs" title="Approve">
                                                <i class="fa-solid fa-check"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.hr.leave.requests.reject', $req->id) }}" method="POST" style="display: inline-block;">
                                            @csrf
                                            <button type="submit" class="btn btn-ghost btn-xs text-red-500" title="Reject">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span style="font-size: 0.75rem; color: #64748B;">Audited</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 3rem; color: #94A3B8;">No leave requests recorded.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 1rem;">
            {{ $requests->links() }}
        </div>
    </div>

    <!-- Apply Leave Modal -->
    <div id="applyLeaveModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: #ffffff; border-radius: 0.75rem; padding: 1.5rem; width: 100%; max-width: 480px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0;">Apply For Leave</h3>
                <button type="button" onclick="document.getElementById('applyLeaveModal').style.display='none'" style="background: none; border: none; font-size: 1.25rem; cursor: pointer;">&times;</button>
            </div>
            <form action="{{ route('admin.hr.leave.requests.store') }}" method="POST">
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
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Leave Type *</label>
                        <select name="leave_type_id" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                            @foreach($leaveTypes as $lt)
                                <option value="{{ $lt->id }}">{{ $lt->name_en }} ({{ $lt->annual_days }} days/yr)</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Start Date *</label>
                            <input type="date" name="start_date" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        </div>
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">End Date *</label>
                            <input type="date" name="end_date" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        </div>
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Reason / Notes</label>
                        <textarea name="reason" rows="2" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;"></textarea>
                    </div>
                    <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1rem;">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('applyLeaveModal').style.display='none'">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">Submit Request</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
