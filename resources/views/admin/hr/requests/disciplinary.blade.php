<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'سجل الجزاءات والإجراءات التأديبية' : 'Disciplinary Actions & Sanctions'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'توثيق الإنذارات الرسمية، التحقيقات الإدارية والخصومات التأديبية' : 'Formal warnings, disciplinary hearings, statutory deduction penalties, and conduct audits'"
>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0; color: var(--text-color, #1E293B);">
                <i class="fa-solid fa-gavel text-rose-500 mr-2 ml-2"></i> 
                {{ app()->getLocale() === 'ar' ? 'سجل الإجراءات التأديبية' : 'Disciplinary Sanctions Log' }}
            </h2>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('admin.hr.requests.index') }}" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-arrow-left mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'كل الطلبات' : 'All Requests' }}
            </a>
            <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('newDisciplinaryModal').style.display='flex'">
                <i class="fa-solid fa-plus text-xs mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'تسجيل إجراء تأديبي' : 'Record Action' }}
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="padding: 0.75rem 1rem; margin-bottom: 1rem; border-radius: 0.5rem; background: #DCFCE7; color: #166534; border: 1px solid #BBF7D0;">
            <i class="fa-solid fa-circle-check mr-1 ml-1"></i> {{ session('success') }}
        </div>
    @endif

    <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
        <div class="table-responsive">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                <thead>
                    <tr style="border-bottom: 2px solid #E2E8F0; text-align: left; color: #64748B;">
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الموظف' : 'Employee' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'نوع الواقعة / المخالفة' : 'Incident Type' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الإجراء المتخذ' : 'Sanction / Action' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'تاريخ الواقعة' : 'Incident Date' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'تاريخ الإجراء' : 'Action Date' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($actions as $act)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.75rem 0.5rem;">
                                <a href="{{ route('admin.hr.employees.show', $act->employee_id) }}" style="color: #0284C7; font-weight: 700; text-decoration: none;">
                                    {{ $act->employee?->full_name ?? 'N/A' }}
                                </a>
                                <span style="font-size: 0.75rem; color: #64748B; display: block;">{{ $act->employee?->department?->name }}</span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; color: #0F172A; font-weight: 600;">
                                {{ ucfirst(str_replace('_', ' ', $act->incident_type)) }}
                                <span style="font-size: 0.75rem; color: #64748B; display: block; font-weight: normal;">{{ \Illuminate\Support\Str::limit($act->description, 60) }}</span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <span class="badge" style="background: #FEE2E2; color: #991B1B; padding: 0.2rem 0.5rem; border-radius: 999px; font-weight: 700;">
                                    {{ ucfirst(str_replace('_', ' ', $act->action_type)) }}
                                </span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; color: #334155;">{{ $act->incident_date }}</td>
                            <td style="padding: 0.75rem 0.5rem; color: #334155;">{{ $act->action_date }}</td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <span class="badge" style="background: #DCFCE7; color: #166534; padding: 0.2rem 0.5rem; border-radius: 999px; font-weight: 700;">
                                    Active Record
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 3rem; color: #94A3B8;">
                                <i class="fa-solid fa-gavel" style="font-size: 2.5rem; margin-bottom: 0.75rem; display: block; opacity: 0.4;"></i>
                                {{ app()->getLocale() === 'ar' ? 'لا توجد إجراءات تأديبية مسجلة.' : 'No disciplinary actions recorded.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 1rem;">
            {{ $actions->links() }}
        </div>
    </div>

    <!-- Record Disciplinary Action Modal -->
    <div id="newDisciplinaryModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: #ffffff; border-radius: 0.75rem; padding: 1.5rem; width: 100%; max-width: 480px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0;">{{ app()->getLocale() === 'ar' ? 'تسجيل إجراء تأديبي' : 'Record Disciplinary Action' }}</h3>
                <button type="button" onclick="document.getElementById('newDisciplinaryModal').style.display='none'" style="background: none; border: none; font-size: 1.25rem; cursor: pointer;">&times;</button>
            </div>
            <form action="{{ route('admin.hr.requests.disciplinary.store') }}" method="POST">
                @csrf
                <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'الموظف المعني *' : 'Employee *' }}</label>
                        <select name="employee_id" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->full_name }} ({{ $emp->employee_number }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'نوع المخالفة *' : 'Incident Type *' }}</label>
                            <select name="incident_type" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                                <option value="unauthorized_absence">Unauthorized Absence</option>
                                <option value="chronic_lateness">Chronic Lateness</option>
                                <option value="policy_violation">Company Policy Violation</option>
                                <option value="insubordination">Insubordination</option>
                                <option value="performance_negligence">Performance Negligence</option>
                                <option value="other">Other Violation</option>
                            </select>
                        </div>
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'نوع الإجراء *' : 'Sanction Type *' }}</label>
                            <select name="action_type" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                                <option value="verbal_warning">Verbal Warning</option>
                                <option value="written_warning">Written Warning (First)</option>
                                <option value="final_warning">Final Warning</option>
                                <option value="salary_deduction">Salary Deduction Penalty</option>
                                <option value="suspension">Temporary Suspension</option>
                            </select>
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'تاريخ الواقعة *' : 'Incident Date *' }}</label>
                            <input type="date" name="incident_date" required value="{{ now()->toDateString() }}" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        </div>
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'تاريخ اتخاذ الإجراء *' : 'Action Date *' }}</label>
                            <input type="date" name="action_date" required value="{{ now()->toDateString() }}" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        </div>
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'تفاصيل الواقعة وقرار الإدارة *' : 'Incident Description & Findings *' }}</label>
                        <textarea name="description" required rows="2" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;"></textarea>
                    </div>
                    <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1rem;">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('newDisciplinaryModal').style.display='none'">{{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}</button>
                        <button type="submit" class="btn btn-primary btn-sm">{{ app()->getLocale() === 'ar' ? 'حفظ الإجراء' : 'Record Sanction' }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
