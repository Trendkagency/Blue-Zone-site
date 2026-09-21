<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'إدارة الاستقالات وفترة الإشعار' : 'Workforce Resignations & Notice Periods'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'سجل طلبات الاستقالة، فترة الإشعار وتتبع إجراءات تسليم المهام' : 'Track voluntary resignation submissions, notice period deadlines, and handover milestones'"
>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0; color: var(--text-color, #1E293B);">
                <i class="fa-solid fa-person-walking-dashed-line-arrow-right text-rose-500 mr-2 ml-2"></i> 
                {{ app()->getLocale() === 'ar' ? 'سجل الاستقالات' : 'Resignations Log' }}
            </h2>
        </div>
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <a href="{{ route('admin.hr.offboarding.terminations') }}" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-user-xmark mr-1 ml-1 text-red-500"></i> {{ app()->getLocale() === 'ar' ? 'إنهاء الخدمات' : 'Terminations' }}
            </a>
            <a href="{{ route('admin.hr.offboarding.exit-interviews') }}" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-comments mr-1 ml-1 text-sky-500"></i> {{ app()->getLocale() === 'ar' ? 'مقابلات نهاية الخدمة' : 'Exit Interviews' }}
            </a>
            <a href="{{ route('admin.hr.offboarding.settlements') }}" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-calculator mr-1 ml-1 text-emerald-500"></i> {{ app()->getLocale() === 'ar' ? 'المخالصات النهائية' : 'Settlements' }}
            </a>
            <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('newResignationModal').style.display='flex'">
                <i class="fa-solid fa-plus text-xs mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'تسجيل استقالة' : 'Submit Resignation' }}
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
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'تاريخ تقديم الاستقالة' : 'Submission Date' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'فترة الإشعار' : 'Notice Period' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'آخر يوم عمل' : 'Last Working Date' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'أسباب الاستقالة' : 'Reason' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($resignations as $res)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.75rem 0.5rem;">
                                <a href="{{ route('admin.hr.employees.show', $res->employee_id) }}" style="color: #0284C7; font-weight: 700; text-decoration: none;">
                                    {{ $res->employee?->full_name ?? 'N/A' }}
                                </a>
                                <span style="font-size: 0.75rem; color: #64748B; display: block;">{{ $res->employee?->department?->name }}</span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; color: #334155; font-weight: 600;">{{ $res->submission_date }}</td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <span style="background: #FEF3C7; color: #92400E; font-weight: 700; font-size: 0.75rem; padding: 0.2rem 0.5rem; border-radius: 999px;">
                                    {{ $res->notice_period_days }} {{ app()->getLocale() === 'ar' ? 'يوماً' : 'days' }}
                                </span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; color: #DC2626; font-weight: 700;">{{ $res->last_working_date ?? 'Calculating' }}</td>
                            <td style="padding: 0.75rem 0.5rem; color: #64748B; max-width: 250px;">{{ $res->reason }}</td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <span class="badge" style="background: {{ $res->status === 'approved' ? '#DCFCE7' : '#FEF3C7' }}; color: {{ $res->status === 'approved' ? '#166534' : '#92400E' }}; padding: 0.2rem 0.5rem; border-radius: 999px; font-weight: 700;">
                                    {{ ucfirst($res->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 3rem; color: #94A3B8;">
                                <i class="fa-solid fa-person-walking-dashed-line-arrow-right" style="font-size: 2.5rem; margin-bottom: 0.75rem; display: block; opacity: 0.4;"></i>
                                {{ app()->getLocale() === 'ar' ? 'لا توجد طلبات استقالة مسجلة حالياً.' : 'No employee resignations submitted.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 1rem;">
            {{ $resignations->links() }}
        </div>
    </div>

    <!-- Submit Resignation Modal -->
    <div id="newResignationModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: #ffffff; border-radius: 0.75rem; padding: 1.5rem; width: 100%; max-width: 480px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0;">{{ app()->getLocale() === 'ar' ? 'تسجيل استقالة موظف' : 'Register Resignation' }}</h3>
                <button type="button" onclick="document.getElementById('newResignationModal').style.display='none'" style="background: none; border: none; font-size: 1.25rem; cursor: pointer;">&times;</button>
            </div>
            <form action="{{ route('admin.hr.offboarding.resignations.store') }}" method="POST">
                @csrf
                <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'الموظف المستقيل *' : 'Employee *' }}</label>
                        <select name="employee_id" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->full_name }} ({{ $emp->employee_number }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'تاريخ التقديم *' : 'Submission Date *' }}</label>
                            <input type="date" name="submission_date" required value="{{ now()->toDateString() }}" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        </div>
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'فترة الإشعار (أيام) *' : 'Notice Period (Days) *' }}</label>
                            <input type="number" name="notice_period_days" value="30" min="0" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        </div>
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'آخر يوم عمل متوقع' : 'Last Working Date' }}</label>
                        <input type="date" name="last_working_date" value="{{ now()->addDays(30)->toDateString() }}" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'سبب الاستقالة *' : 'Reason for Resignation *' }}</label>
                        <textarea name="reason" required rows="2" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;"></textarea>
                    </div>
                    <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1rem;">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('newResignationModal').style.display='none'">{{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}</button>
                        <button type="submit" class="btn btn-primary btn-sm">{{ app()->getLocale() === 'ar' ? 'تسجيل الاستقالة' : 'Record Resignation' }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
