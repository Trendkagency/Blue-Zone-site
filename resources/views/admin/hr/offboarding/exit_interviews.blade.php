<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'مقابلات نهاية الخدمة والملاحظات' : 'Employee Exit Interviews'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'توثيق أسباب مغادرة العمل، تقييم بيئة المؤسسة والملاحظات التطويرية' : 'Exit feedback surveys, corporate culture ratings, and attrition insight analysis'"
>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0; color: var(--text-color, #1E293B);">
                <i class="fa-solid fa-comments text-sky-500 mr-2 ml-2"></i> 
                {{ app()->getLocale() === 'ar' ? 'سجل مقابلات نهاية الخدمة' : 'Exit Interviews Dossier' }}
            </h2>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('admin.hr.offboarding.resignations') }}" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-person-walking-dashed-line-arrow-right mr-1 ml-1 text-rose-500"></i> {{ app()->getLocale() === 'ar' ? 'الاستقالات' : 'Resignations' }}
            </a>
            <a href="{{ route('admin.hr.offboarding.settlements') }}" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-calculator mr-1 ml-1 text-emerald-500"></i> {{ app()->getLocale() === 'ar' ? 'المخالصات' : 'Settlements' }}
            </a>
            <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('newExitInterviewModal').style.display='flex'">
                <i class="fa-solid fa-plus text-xs mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'تسجيل مقابلة جديدة' : 'Record Interview' }}
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
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الموظف المغادر' : 'Departing Employee' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'المحاور / المسؤول' : 'Interviewer' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'تاريخ المقابلة' : 'Date' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الدافع الأساسي للمغادرة' : 'Core Departure Reason' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'ملاحظات بيئة العمل والتعويضات' : 'Feedback Summary' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($interviews as $interview)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.75rem 0.5rem;">
                                <a href="{{ route('admin.hr.employees.show', $interview->employee_id) }}" style="color: #0284C7; font-weight: 700; text-decoration: none;">
                                    {{ $interview->employee?->full_name ?? 'N/A' }}
                                </a>
                                <span style="font-size: 0.75rem; color: #64748B; display: block;">{{ $interview->employee?->department?->name }}</span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; color: #334155; font-weight: 600;">
                                {{ $interview->interviewer?->full_name ?? 'HR Lead' }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem; color: #64748B;">{{ $interview->interview_date }}</td>
                            <td style="padding: 0.75rem 0.5rem; color: #0F172A; font-weight: 600;">
                                {{ $interview->reason_for_leaving }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem; color: #64748B; max-width: 280px;">
                                {{ \Illuminate\Support\Str::limit($interview->workplace_feedback ?? $interview->employee_feedback, 80) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 3rem; color: #94A3B8;">
                                <i class="fa-solid fa-comments" style="font-size: 2.5rem; margin-bottom: 0.75rem; display: block; opacity: 0.4;"></i>
                                {{ app()->getLocale() === 'ar' ? 'لا توجد مقابلات نهاية خدمة مسجلة.' : 'No exit interviews recorded.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 1rem;">
            {{ $interviews->links() }}
        </div>
    </div>

    <!-- Record Exit Interview Modal -->
    <div id="newExitInterviewModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: #ffffff; border-radius: 0.75rem; padding: 1.5rem; width: 100%; max-width: 520px; max-height: 90vh; overflow-y: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0;">{{ app()->getLocale() === 'ar' ? 'توثيق مقابلة نهاية الخدمة' : 'Record Exit Interview' }}</h3>
                <button type="button" onclick="document.getElementById('newExitInterviewModal').style.display='none'" style="background: none; border: none; font-size: 1.25rem; cursor: pointer;">&times;</button>
            </div>
            <form action="{{ route('admin.hr.offboarding.exit-interviews.store') }}" method="POST">
                @csrf
                <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'الموظف المغادر *' : 'Departing Employee *' }}</label>
                        <select name="employee_id" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                            @foreach($offboardings as $off)
                                <option value="{{ $off->employee_id }}">{{ $off->employee?->full_name }} ({{ $off->employee?->employee_number }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'المسؤول المحاور' : 'Interviewer' }}</label>
                            <select name="interviewer_employee_id" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                                <option value="">{{ app()->getLocale() === 'ar' ? '-- مدير الموارد البشرية --' : '-- HR Representative --' }}</option>
                                @foreach($interviewers as $inv)
                                    <option value="{{ $inv->id }}">{{ $inv->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'تاريخ المقابلة *' : 'Interview Date *' }}</label>
                            <input type="date" name="interview_date" required value="{{ now()->toDateString() }}" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        </div>
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'سبب المغادرة الأساسي *' : 'Core Departure Reason *' }}</label>
                        <input type="text" name="reason_for_leaving" required placeholder="e.g. Higher compensation offer, Relocation, Career change" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'تقييم بيئة العمل' : 'Workplace Environment Feedback' }}</label>
                        <textarea name="workplace_feedback" rows="2" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;"></textarea>
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'مقترحات الموظف للتحسين' : 'Suggestions for Improvement' }}</label>
                        <textarea name="suggestions" rows="2" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;"></textarea>
                    </div>
                    <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1rem;">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('newExitInterviewModal').style.display='none'">{{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}</button>
                        <button type="submit" class="btn btn-primary btn-sm">{{ app()->getLocale() === 'ar' ? 'حفظ المقابلة' : 'Save Exit Dossier' }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
