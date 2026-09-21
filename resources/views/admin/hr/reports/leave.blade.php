<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'تقرير استهلاك الإجازات' : 'Leave Utilization & Consumption Report'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'تحليل الإجازات المعتمدة، الأيام المستهلكة، ومعدلات الغياب المصرح به' : 'Approved leave history, departmental leave trends, and workforce absence patterns'"
>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0; color: var(--text-color, #1E293B);">
                <i class="fa-solid fa-umbrella-beach text-pink-500 mr-2 ml-2"></i> 
                {{ app()->getLocale() === 'ar' ? 'تقرير استهلاك الإجازات' : 'Leave Consumption Audit' }}
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
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الموظف' : 'Employee' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'القسم' : 'Department' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'نوع الإجازة' : 'Leave Type' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'فترة الإجازة' : 'Period' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الأيام المستهلكة' : 'Days Used' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'السبب' : 'Reason' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.75rem 0.5rem;">
                                <a href="{{ route('admin.hr.employees.show', $req->employee_id) }}" style="color: #0284C7; font-weight: 700; text-decoration: none;">
                                    {{ $req->employee?->full_name ?? 'N/A' }}
                                </a>
                                <span style="font-size: 0.75rem; color: #64748B; display: block;">{{ $req->employee?->employee_number }}</span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; color: #334155;">{{ $req->employee?->department?->name }}</td>
                            <td style="padding: 0.75rem 0.5rem; font-weight: 600; color: #0F172A;">{{ $req->leaveType?->name ?? 'Annual' }}</td>
                            <td style="padding: 0.75rem 0.5rem; color: #334155;">{{ $req->start_date }} &ndash; {{ $req->end_date }}</td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <strong style="color: #0284C7; font-family: monospace; font-size: 0.9rem;">{{ $req->total_days }} days</strong>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; color: #64748B;">{{ $req->reason ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 3rem; color: #94A3B8;">
                                No approved leave history recorded.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 1rem;">
            {{ $requests->links() }}
        </div>
    </div>
</x-layouts.admin>
