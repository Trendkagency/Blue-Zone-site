<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'تقرير القوى العاملة ودليل الموظفين' : 'Workforce Directory & Headcount Report'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'بيانات تفصيلية شاملة للموظفين موزعين حسب الأقسام والفروع والمهن' : 'Comprehensive workforce census, departmental allocations, and employment status report'"
>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0; color: var(--text-color, #1E293B);">
                <i class="fa-solid fa-address-book text-sky-500 mr-2 ml-2"></i> 
                {{ app()->getLocale() === 'ar' ? 'تقرير القوى العاملة' : 'Workforce Census' }}
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

    <!-- Filters -->
    <div class="card" style="padding: 1rem 1.25rem; margin-bottom: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
        <form action="{{ route('admin.hr.reports.employees') }}" method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            <div style="min-width: 220px;">
                <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem; color: #64748B;">{{ app()->getLocale() === 'ar' ? 'تصفية حسب القسم' : 'Department' }}</label>
                <select name="department_id" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                    <option value="">{{ app()->getLocale() === 'ar' ? 'كل الأقسام' : 'All Departments' }}</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="min-width: 180px;">
                <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem; color: #64748B;">{{ app()->getLocale() === 'ar' ? 'حالة التوظيف' : 'Status' }}</label>
                <select name="status" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                    <option value="">{{ app()->getLocale() === 'ar' ? 'كل الحالات' : 'All Statuses' }}</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="probation" {{ request('status') === 'probation' ? 'selected' : '' }}>Probation</option>
                    <option value="on_leave" {{ request('status') === 'on_leave' ? 'selected' : '' }}>On Leave</option>
                    <option value="resigned" {{ request('status') === 'resigned' ? 'selected' : '' }}>Resigned</option>
                    <option value="terminated" {{ request('status') === 'terminated' ? 'selected' : '' }}>Terminated</option>
                </select>
            </div>
            <div>
                <button type="submit" class="btn btn-primary btn-sm" style="height: 38px;">
                    <i class="fa-solid fa-filter mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'تصفية' : 'Filter' }}
                </button>
            </div>
        </form>
    </div>

    <!-- Workforce Table -->
    <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
        <div class="table-responsive">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                <thead>
                    <tr style="border-bottom: 2px solid #E2E8F0; text-align: left; color: #64748B;">
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الرقم الوظيفي' : 'EMP #' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الموظف' : 'Employee' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'القسم' : 'Department' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'المسمى الوظيفي' : 'Position' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الفرع / الموقع' : 'Location' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'تاريخ التعيين' : 'Hire Date' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $emp)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.75rem 0.5rem; font-family: monospace; font-weight: 700; color: #0284C7;">
                                {{ $emp->employee_number }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <a href="{{ route('admin.hr.employees.show', $emp->id) }}" style="color: #0F172A; font-weight: 700; text-decoration: none;">
                                    {{ $emp->full_name }}
                                </a>
                                <span style="font-size: 0.75rem; color: #64748B; display: block;">{{ $emp->email }}</span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; color: #334155; font-weight: 600;">
                                {{ $emp->department?->name ?? '—' }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem; color: #334155;">
                                {{ $emp->position?->name ?? '—' }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem; color: #64748B;">
                                {{ $emp->location?->name ?? 'HQ' }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem; color: #334155;">{{ $emp->hire_date }}</td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <span class="badge" style="background: {{ $emp->employment_status === 'active' ? '#DCFCE7' : '#FEF3C7' }}; color: {{ $emp->employment_status === 'active' ? '#166534' : '#92400E' }}; padding: 0.2rem 0.5rem; border-radius: 999px; font-weight: 700;">
                                    {{ ucfirst(str_replace('_', ' ', $emp->employment_status)) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 3rem; color: #94A3B8;">
                                No employees found matching the criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 1rem;">
            {{ $employees->links() }}
        </div>
    </div>
</x-layouts.admin>
