<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'إدارة الأقسام' : 'Departments Management'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'الهيكل التنظيمي وأقسام الشركة' : 'Company Organizational Structure & Departments'"
>
    <!-- Header Actions -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0; color: var(--text-color, #1E293B);">
                <i class="fa-solid fa-sitemap text-teal-500 mr-2 ml-2"></i> {{ app()->getLocale() === 'ar' ? 'الأقسام' : 'Departments' }}
            </h2>
        </div>
        <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('addDeptModal').style.display='flex'">
            <i class="fa-solid fa-plus text-xs"></i> {{ app()->getLocale() === 'ar' ? 'إضافة قسم جديد' : 'Add Department' }}
        </button>
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

    <!-- Departments Table Card -->
    <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
        <div class="table-responsive">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                <thead>
                    <tr style="border-bottom: 2px solid #E2E8F0; text-align: left; color: #64748B;">
                        <th style="padding: 0.75rem 0.5rem;">Code</th>
                        <th style="padding: 0.75rem 0.5rem;">Department Name (EN)</th>
                        <th style="padding: 0.75rem 0.5rem;">Department Name (AR)</th>
                        <th style="padding: 0.75rem 0.5rem;">Manager</th>
                        <th style="padding: 0.75rem 0.5rem;">Positions</th>
                        <th style="padding: 0.75rem 0.5rem;">Employees</th>
                        <th style="padding: 0.75rem 0.5rem;">Status</th>
                        <th style="padding: 0.75rem 0.5rem; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($departments as $dept)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.75rem 0.5rem; font-weight: 700; color: #0284C7;">{{ $dept->code }}</td>
                            <td style="padding: 0.75rem 0.5rem; font-weight: 600;">{{ $dept->name_en }}</td>
                            <td style="padding: 0.75rem 0.5rem;">{{ $dept->name_ar }}</td>
                            <td style="padding: 0.75rem 0.5rem; color: #64748B;">
                                {{ $dept->manager ? $dept->manager->full_name : '—' }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem;">{{ $dept->positions->count() }}</td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <span class="badge" style="background: #E0F2FE; color: #0369A1; padding: 0.2rem 0.5rem; border-radius: 999px; font-weight: 700;">
                                    {{ $dept->employees_count }}
                                </span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <span class="badge" style="background: {{ $dept->is_active ? '#DCFCE7' : '#F1F5F9' }}; color: {{ $dept->is_active ? '#166534' : '#64748B' }}; padding: 0.2rem 0.5rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600;">
                                    {{ $dept->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; text-align: right;">
                                <form action="{{ route('admin.hr.organization.departments.destroy', $dept->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this department?');" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-ghost btn-xs text-red-500" title="Delete">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 2rem; color: #94A3B8;">No departments created yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 1rem;">
            {{ $departments->links() }}
        </div>
    </div>

    <!-- Add Department Modal -->
    <div id="addDeptModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: #ffffff; border-radius: 0.75rem; padding: 1.5rem; width: 100%; max-width: 500px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0;">Add New Department</h3>
                <button type="button" onclick="document.getElementById('addDeptModal').style.display='none'" style="background: none; border: none; font-size: 1.25rem; cursor: pointer; color: #94A3B8;">&times;</button>
            </div>
            <form action="{{ route('admin.hr.organization.departments.store') }}" method="POST">
                @csrf
                <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Department Code *</label>
                        <input type="text" name="code" required class="form-control" placeholder="e.g. HR, IT, SLS" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Department Name (English) *</label>
                        <input type="text" name="name_en" required class="form-control" placeholder="e.g. Human Resources" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Department Name (Arabic) *</label>
                        <input type="text" name="name_ar" required class="form-control" placeholder="e.g. الموارد البشرية" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Department Manager</label>
                        <select name="manager_employee_id" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                            <option value="">-- Select Active Employee --</option>
                            @foreach($managers as $mgr)
                                <option value="{{ $mgr->id }}">{{ $mgr->full_name }} ({{ $mgr->employee_number }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">Description</label>
                        <textarea name="description" rows="2" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;"></textarea>
                    </div>
                    <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1rem;">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('addDeptModal').style.display='none'">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">Save Department</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
