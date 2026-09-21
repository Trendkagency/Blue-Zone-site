<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'سجل الموظفين' : 'Employees Directory'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'إدارة الموظفين والملفات الشخصية والعقود' : 'Workforce directory, profiles, contracts and status'"
>
    <!-- Header Actions -->
    <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0; color: var(--text-color, #1E293B);">
                <i class="fa-solid fa-users text-sky-500 mr-2 ml-2"></i> {{ app()->getLocale() === 'ar' ? 'دليل الموظفين' : 'Employee Directory' }}
            </h2>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('admin.hr.employees.create') }}" class="btn btn-primary btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem;">
                <i class="fa-solid fa-user-plus text-xs"></i> {{ app()->getLocale() === 'ar' ? 'إضافة موظف جديد' : 'New Employee' }}
            </a>
            <a href="{{ route('admin.hr.reports.employees') }}" class="btn btn-outline btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem;">
                <i class="fa-solid fa-download text-xs"></i> {{ app()->getLocale() === 'ar' ? 'تقرير الموظفين' : 'Export Directory' }}
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card" style="padding: 1rem 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0); margin-bottom: 1.5rem;">
        <form action="{{ route('admin.hr.employees.index') }}" method="GET" style="display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: flex-end;">
            <div style="flex: 1; min-width: 200px;">
                <label style="font-size: 0.75rem; font-weight: 600; color: #64748B; display: block; margin-bottom: 0.25rem;">Search Employee</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, EMP ID, Phone, Email..." class="form-control" style="width: 100%; padding: 0.45rem 0.75rem; border: 1px solid #CBD5E1; border-radius: 0.375rem; font-size: 0.85rem;">
            </div>

            <div style="width: 180px;">
                <label style="font-size: 0.75rem; font-weight: 600; color: #64748B; display: block; margin-bottom: 0.25rem;">Department</label>
                <select name="department_id" class="form-control" style="width: 100%; padding: 0.45rem 0.75rem; border: 1px solid #CBD5E1; border-radius: 0.375rem; font-size: 0.85rem;">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name_en }}</option>
                    @endforeach
                </select>
            </div>

            <div style="width: 150px;">
                <label style="font-size: 0.75rem; font-weight: 600; color: #64748B; display: block; margin-bottom: 0.25rem;">Status</label>
                <select name="status" class="form-control" style="width: 100%; padding: 0.45rem 0.75rem; border: 1px solid #CBD5E1; border-radius: 0.375rem; font-size: 0.85rem;">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="probation" {{ request('status') === 'probation' ? 'selected' : '' }}>Probation</option>
                    <option value="on_leave" {{ request('status') === 'on_leave' ? 'selected' : '' }}>On Leave</option>
                    <option value="resigned" {{ request('status') === 'resigned' ? 'selected' : '' }}>Resigned</option>
                    <option value="terminated" {{ request('status') === 'terminated' ? 'selected' : '' }}>Terminated</option>
                </select>
            </div>

            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-secondary btn-sm" style="height: 34px;">
                    <i class="fa-solid fa-filter text-xs"></i> Filter
                </button>
                <a href="{{ route('admin.hr.employees.index') }}" class="btn btn-ghost btn-sm" style="height: 34px;">Clear</a>
            </div>
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="padding: 0.75rem 1rem; margin-bottom: 1rem; border-radius: 0.5rem; background: #DCFCE7; color: #166534; border: 1px solid #BBF7D0;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Employee Table -->
    <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
        <div class="table-responsive">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                <thead>
                    <tr style="border-bottom: 2px solid #E2E8F0; text-align: left; color: #64748B;">
                        <th style="padding: 0.75rem 0.5rem;">Employee</th>
                        <th style="padding: 0.75rem 0.5rem;">Department / Position</th>
                        <th style="padding: 0.75rem 0.5rem;">Contact</th>
                        <th style="padding: 0.75rem 0.5rem;">Location</th>
                        <th style="padding: 0.75rem 0.5rem;">Type</th>
                        <th style="padding: 0.75rem 0.5rem;">Status</th>
                        <th style="padding: 0.75rem 0.5rem;">Joined</th>
                        <th style="padding: 0.75rem 0.5rem; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $emp)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.75rem 0.5rem;">
                                <div style="display: flex; align-items: center; gap: 0.6rem;">
                                    <div style="width: 36px; height: 36px; border-radius: 50%; background: #0284C7; color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.8rem; flex-shrink: 0;">
                                        {{ strtoupper(substr($emp->first_name, 0, 1) . substr($emp->last_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div style="display: flex; align-items: center; gap: 0.35rem;">
                                            <a href="{{ route('admin.hr.employees.show', $emp->id) }}" style="font-weight: 700; color: #0284C7; text-decoration: none;">
                                                {{ $emp->first_name }} {{ $emp->last_name }}
                                            </a>
                                            @if($emp->user)
                                                <span title="{{ app()->getLocale() === 'ar' ? 'حساب مستخدم بالنظام' : 'Linked System User' }}" style="background: rgba(14, 165, 233, 0.1); color: #0284C7; font-size: 0.65rem; font-weight: 700; padding: 0.1rem 0.4rem; border-radius: 999px; display: inline-flex; align-items: center; gap: 0.2rem;">
                                                    <i class="fa-solid fa-user-check text-[9px]"></i> {{ app()->getLocale() === 'ar' ? 'مستخدم مصرح' : 'User' }}
                                                </span>
                                            @endif
                                        </div>
                                        <div style="font-size: 0.75rem; color: #64748B; font-family: monospace;">{{ $emp->employee_number }}</div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <div style="font-weight: 600; color: #334155;">{{ $emp->position?->name_en ?? 'Position Unassigned' }}</div>
                                <div style="font-size: 0.75rem; color: #64748B;">{{ $emp->department?->name_en ?? 'No Dept' }}</div>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; font-size: 0.8rem;">
                                <div><i class="fa-solid fa-envelope text-slate-400 mr-1 ml-1 text-xs"></i> {{ $emp->email ?? '—' }}</div>
                                <div style="color: #64748B;"><i class="fa-solid fa-phone text-slate-400 mr-1 ml-1 text-xs"></i> {{ $emp->phone ?? '—' }}</div>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; color: #475569;">
                                {{ $emp->location?->name ?? 'Main Branch' }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <span class="badge" style="background: #F1F5F9; color: #475569; padding: 0.2rem 0.5rem; border-radius: 0.25rem; font-size: 0.7rem; text-transform: capitalize;">
                                    {{ str_replace('_', ' ', $emp->employment_type) }}
                                </span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem;">
                                @php
                                    $statusBg = '#DCFCE7'; $statusColor = '#166534';
                                    if ($emp->employment_status === 'probation') { $statusBg = '#FEF3C7'; $statusColor = '#92400E'; }
                                    elseif ($emp->employment_status === 'on_leave') { $statusBg = '#E0F2FE'; $statusColor = '#0369A1'; }
                                    elseif (in_array($emp->employment_status, ['resigned', 'terminated'])) { $statusBg = '#FEE2E2'; $statusColor = '#991B1B'; }
                                @endphp
                                <span class="badge" style="background: {{ $statusBg }}; color: {{ $statusColor }}; padding: 0.2rem 0.5rem; border-radius: 999px; font-size: 0.75rem; font-weight: 700;">
                                    {{ ucfirst(str_replace('_', ' ', $emp->employment_status)) }}
                                </span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; color: #64748B; font-size: 0.8rem;">
                                {{ $emp->hire_date ? \Carbon\Carbon::parse($emp->hire_date)->format('M d, Y') : '—' }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem; text-align: right;">
                                <div style="display: inline-flex; gap: 0.25rem;">
                                    <a href="{{ route('admin.hr.employees.show', $emp->id) }}" class="btn btn-ghost btn-xs text-sky-600" title="View Profile">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.hr.employees.edit', $emp->id) }}" class="btn btn-ghost btn-xs text-amber-600" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('admin.hr.employees.destroy', $emp->id) }}" method="POST" onsubmit="return confirm('Archive employee?');" style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-ghost btn-xs text-red-500" title="Archive">
                                            <i class="fa-solid fa-box-archive"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 3rem; color: #94A3B8;">
                                <i class="fa-solid fa-users-slash text-3xl mb-2 block"></i>
                                No employee profiles found matching criteria.
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
