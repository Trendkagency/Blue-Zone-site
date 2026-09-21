<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'سجل النقل والتنقلات الداخلية' : 'Internal Employee Transfers'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'إدارة حركات نقل الموظفين بين الفروع والأقسام والمواقع الإقليمية' : 'Manage cross-branch transfers, department reassignments, and regional location changes'"
>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0; color: var(--text-color, #1E293B);">
                <i class="fa-solid fa-right-left text-indigo-500 mr-2 ml-2"></i> 
                {{ app()->getLocale() === 'ar' ? 'النقل والتنقلات الداخلية' : 'Internal Reassignments' }}
            </h2>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('admin.hr.requests.index') }}" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-arrow-left mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'كل الطلبات' : 'All Requests' }}
            </a>
            <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('newTransferModal').style.display='flex'">
                <i class="fa-solid fa-plus text-xs mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'تنفيذ نقل داخلي' : 'Execute Transfer' }}
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="padding: 0.75rem 1rem; margin-bottom: 1rem; border-radius: 0.5rem; background: #DCFCE7; color: #166534; border: 1px solid #BBF7D0;">
            <i class="fa-solid fa-circle-check mr-1 ml-1"></i> {{ session('success') }}
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

    <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
        <div class="table-responsive">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                <thead>
                    <tr style="border-bottom: 2px solid #E2E8F0; text-align: left; color: #64748B;">
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الموظف' : 'Employee' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'التعيين السابق' : 'Previous Assignment' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'التعيين الجديد' : 'New Assignment' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'تاريخ السريان' : 'Effective Date' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'سبب النقل' : 'Reason' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transfers as $tr)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.75rem 0.5rem;">
                                <a href="{{ route('admin.hr.employees.show', $tr->employee_id) }}" style="color: #0284C7; font-weight: 700; text-decoration: none;">
                                    {{ $tr->employee?->full_name ?? 'N/A' }}
                                </a>
                                <span style="font-size: 0.75rem; color: #64748B; display: block;">{{ $tr->employee?->employee_number }}</span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; color: #64748B;">
                                <div>{{ $tr->oldDepartment?->name ?? 'Dept' }}</div>
                                <span style="font-size: 0.75rem; color: #94A3B8;">{{ $tr->oldLocation?->name ?? 'Branch' }} &bull; {{ $tr->oldPosition?->name }}</span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <strong style="color: #059669;">{{ $tr->newDepartment?->name }}</strong>
                                <span style="font-size: 0.75rem; color: #64748B; display: block;">
                                    {{ $tr->newLocation?->name ?? 'Branch' }} &bull; {{ $tr->newPosition?->name }}
                                </span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; font-weight: 600; color: #334155;">
                                {{ $tr->effective_date }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem; color: #64748B;">
                                {{ $tr->reason }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 3rem; color: #94A3B8;">
                                <i class="fa-solid fa-right-left" style="font-size: 2.5rem; margin-bottom: 0.75rem; display: block; opacity: 0.4;"></i>
                                {{ app()->getLocale() === 'ar' ? 'لا توجد حركات نقل داخلية مسجلة.' : 'No employee transfers executed.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 1rem;">
            {{ $transfers->links() }}
        </div>
    </div>

    <!-- Execute Transfer Modal -->
    <div id="newTransferModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: #ffffff; border-radius: 0.75rem; padding: 1.5rem; width: 100%; max-width: 480px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0;">{{ app()->getLocale() === 'ar' ? 'تنفيذ نقل داخلي' : 'Execute Internal Transfer' }}</h3>
                <button type="button" onclick="document.getElementById('newTransferModal').style.display='none'" style="background: none; border: none; font-size: 1.25rem; cursor: pointer;">&times;</button>
            </div>
            <form action="{{ route('admin.hr.requests.transfers.store') }}" method="POST">
                @csrf
                <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'الموظف المطلوب نقله *' : 'Employee *' }}</label>
                        <select name="employee_id" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->full_name }} ({{ $emp->employee_number }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'القسم الجديد' : 'New Department' }}</label>
                        <select name="new_department_id" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                            <option value="">{{ app()->getLocale() === 'ar' ? '-- بدون تغيير --' : '-- Keep Current --' }}</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'المسمى الوظيفي الجديد' : 'New Position' }}</label>
                        <select name="new_position_id" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                            <option value="">{{ app()->getLocale() === 'ar' ? '-- بدون تغيير --' : '-- Keep Current --' }}</option>
                            @foreach($positions as $pos)
                                <option value="{{ $pos->id }}">{{ $pos->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'الفرع / الموقع الجديد' : 'New Branch / Location' }}</label>
                        <select name="new_location_id" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                            <option value="">{{ app()->getLocale() === 'ar' ? '-- بدون تغيير --' : '-- Keep Current --' }}</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'تاريخ السريان *' : 'Effective Date *' }}</label>
                        <input type="date" name="effective_date" required value="{{ now()->toDateString() }}" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'سبب وقرار النقل *' : 'Reason *' }}</label>
                        <textarea name="reason" required rows="2" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;"></textarea>
                    </div>
                    <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1rem;">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('newTransferModal').style.display='none'">{{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}</button>
                        <button type="submit" class="btn btn-primary btn-sm">{{ app()->getLocale() === 'ar' ? 'اعتماد النقل' : 'Execute Transfer' }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
