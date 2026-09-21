<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'اعتمادات الإجازات' : 'Leave Approvals Queue'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'طلبات الإجازة المعلقة بانتظار موافقة أو رفض الإدارة' : 'Pending leave authorizations awaiting administrative decision'"
>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0; color: var(--text-color, #1E293B);">
                <i class="fa-solid fa-stamp text-rose-500 mr-2 ml-2"></i> {{ app()->getLocale() === 'ar' ? 'طلبات الإجازة المعلقة' : 'Pending Leave Approvals' }}
            </h2>
        </div>
        <a href="{{ route('admin.hr.leave.requests') }}" class="btn btn-secondary btn-sm">
            All Leave Requests
        </a>
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
                        <th style="padding: 0.75rem 0.5rem;">Employee</th>
                        <th style="padding: 0.75rem 0.5rem;">Department / Position</th>
                        <th style="padding: 0.75rem 0.5rem;">Leave Type</th>
                        <th style="padding: 0.75rem 0.5rem;">Dates</th>
                        <th style="padding: 0.75rem 0.5rem;">Duration</th>
                        <th style="padding: 0.75rem 0.5rem;">Reason</th>
                        <th style="padding: 0.75rem 0.5rem; text-align: right;">Decision</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingRequests as $req)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.75rem 0.5rem;">
                                <a href="{{ route('admin.hr.employees.show', $req->employee_id) }}" style="color: #0284C7; font-weight: 700; text-decoration: none;">
                                    {{ $req->employee?->full_name ?? 'N/A' }}
                                </a>
                                <span style="font-size: 0.75rem; color: #64748B; display: block; font-family: monospace;">{{ $req->employee?->employee_number }}</span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <div>{{ $req->employee?->department?->name_en ?? '—' }}</div>
                                <div style="font-size: 0.75rem; color: #64748B;">{{ $req->employee?->position?->name_en ?? '—' }}</div>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; font-weight: 600;">{{ $req->leaveType?->name ?? 'Leave' }}</td>
                            <td style="padding: 0.75rem 0.5rem;">{{ $req->start_date }} &rarr; {{ $req->end_date }}</td>
                            <td style="padding: 0.75rem 0.5rem; font-weight: 700; color: #0284C7;">{{ $req->total_days }} days</td>
                            <td style="padding: 0.75rem 0.5rem; color: #64748B;">{{ $req->reason ?? '—' }}</td>
                            <td style="padding: 0.75rem 0.5rem; text-align: right;">
                                <div style="display: inline-flex; gap: 0.35rem;">
                                    <form action="{{ route('admin.hr.leave.requests.approve', $req->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-xs" style="background: #10B981; border-color: #10B981;">
                                            <i class="fa-solid fa-check mr-1"></i> Approve
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.hr.leave.requests.reject', $req->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-xs" style="background: #EF4444; border-color: #EF4444;">
                                            <i class="fa-solid fa-xmark mr-1"></i> Reject
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 3rem; color: #94A3B8;">
                                <i class="fa-solid fa-circle-check text-3xl text-emerald-400 mb-2 block"></i>
                                All clear! No pending leave approvals awaiting action.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 1rem;">
            {{ $pendingRequests->links() }}
        </div>
    </div>
</x-layouts.admin>
