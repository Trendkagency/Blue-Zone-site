<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'أرصدة إجازات الموظفين' : 'Employee Leave Balances'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'متابعة الأيام المستحقة، المستهلكة، والمتبقية لكل موظف' : 'Track annual allowances, days utilized, pending and remaining balances'"
>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0; color: var(--text-color, #1E293B);">
                <i class="fa-solid fa-scale-balanced text-teal-500 mr-2 ml-2"></i> {{ app()->getLocale() === 'ar' ? 'أرصدة الإجازات' : 'Leave Balances Ledger' }}
            </h2>
        </div>
        <form action="{{ route('admin.hr.leave.balances') }}" method="GET" style="display: flex; gap: 0.5rem; align-items: center;">
            <select name="year" class="form-control" style="padding: 0.4rem 0.6rem; border: 1px solid #CBD5E1; border-radius: 0.375rem; font-size: 0.85rem;">
                @for($y = now()->year + 1; $y >= now()->year - 2; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>Year {{ $y }}</option>
                @endfor
            </select>
            <button type="submit" class="btn btn-secondary btn-sm">Filter Year</button>
        </form>
    </div>

    <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
        <div class="table-responsive">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                <thead>
                    <tr style="border-bottom: 2px solid #E2E8F0; text-align: left; color: #64748B;">
                        <th style="padding: 0.75rem 0.5rem;">Employee</th>
                        <th style="padding: 0.75rem 0.5rem;">Department</th>
                        <th style="padding: 0.75rem 0.5rem;">Leave Type</th>
                        <th style="padding: 0.75rem 0.5rem;">Allocated</th>
                        <th style="padding: 0.75rem 0.5rem;">Used</th>
                        <th style="padding: 0.75rem 0.5rem;">Pending</th>
                        <th style="padding: 0.75rem 0.5rem;">Remaining Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($balances as $bal)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.75rem 0.5rem;">
                                <a href="{{ route('admin.hr.employees.show', $bal->employee_id) }}" style="color: #0284C7; font-weight: 700; text-decoration: none;">
                                    {{ $bal->employee?->full_name ?? 'N/A' }}
                                </a>
                                <span style="font-size: 0.75rem; color: #64748B; display: block; font-family: monospace;">{{ $bal->employee?->employee_number }}</span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem;">{{ $bal->employee?->department?->name_en ?? '—' }}</td>
                            <td style="padding: 0.75rem 0.5rem; font-weight: 600;">{{ $bal->leaveType?->name ?? 'Leave' }}</td>
                            <td style="padding: 0.75rem 0.5rem;">{{ $bal->allocated_days }} days</td>
                            <td style="padding: 0.75rem 0.5rem; color: #EF4444; font-weight: 600;">{{ $bal->used_days }} days</td>
                            <td style="padding: 0.75rem 0.5rem; color: #F59E0B;">{{ $bal->pending_days }} days</td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <span class="badge" style="background: #E0F2FE; color: #0369A1; padding: 0.25rem 0.6rem; border-radius: 999px; font-weight: 800; font-size: 0.85rem;">
                                    {{ $bal->remaining_days }} days
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 3rem; color: #94A3B8;">No leave balances initialized for this period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 1rem;">
            {{ $balances->links() }}
        </div>
    </div>
</x-layouts.admin>
