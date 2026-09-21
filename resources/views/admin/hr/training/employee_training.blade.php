<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'تدريب الموظفين والتسجيل' : 'Employee Training Enrollments'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'متابعة التحاق الموظفين بالدورات التدريبية ومعدلات الإتمام والنتائج' : 'Enroll workforce members into courses, track attendance, and log completion results'"
>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0; color: var(--text-color, #1E293B);">
                <i class="fa-solid fa-users text-indigo-500 mr-2 ml-2"></i> 
                {{ app()->getLocale() === 'ar' ? 'سجل تدريب الموظفين' : 'Employee Training Records' }}
            </h2>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('admin.hr.training.programs') }}" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-graduation-cap mr-1 ml-1 text-sky-500"></i> {{ app()->getLocale() === 'ar' ? 'البرامج' : 'Programs' }}
            </a>
            <a href="{{ route('admin.hr.training.certificates') }}" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-certificate mr-1 ml-1 text-amber-500"></i> {{ app()->getLocale() === 'ar' ? 'الشهادات' : 'Certificates' }}
            </a>
            <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('assignTrainingModal').style.display='flex'">
                <i class="fa-solid fa-plus text-xs mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'تسجيل موظف بدورة' : 'Enroll Employee' }}
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
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'البرنامج التدريبي' : 'Training Program' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'تاريخ التكليف' : 'Assigned Date' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'تاريخ الإتمام' : 'Completed Date' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'النتيجة / الدرجة' : 'Result' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($trainings as $item)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.75rem 0.5rem;">
                                <a href="{{ route('admin.hr.employees.show', $item->employee_id) }}" style="color: #0284C7; font-weight: 700; text-decoration: none;">
                                    {{ $item->employee?->full_name ?? 'N/A' }}
                                </a>
                                <span style="font-size: 0.75rem; color: #64748B; display: block;">{{ $item->employee?->department?->name }}</span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; font-weight: 600; color: #0F172A;">
                                {{ $item->program?->name ?? 'Specialized Course' }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem; color: #64748B;">
                                {{ $item->assigned_at ? \Carbon\Carbon::parse($item->assigned_at)->format('Y-m-d') : '—' }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem; color: #64748B;">
                                {{ $item->completed_at ? \Carbon\Carbon::parse($item->completed_at)->format('Y-m-d') : 'In Progress' }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem; font-weight: 700; color: #059669;">
                                {{ $item->result ?? 'Passed' }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <span class="badge" style="background: {{ $item->status === 'completed' ? '#DCFCE7' : '#FEF3C7' }}; color: {{ $item->status === 'completed' ? '#166534' : '#92400E' }}; padding: 0.2rem 0.5rem; border-radius: 999px; font-weight: 700;">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 3rem; color: #94A3B8;">
                                <i class="fa-solid fa-user-graduate" style="font-size: 2.5rem; margin-bottom: 0.75rem; display: block; opacity: 0.4;"></i>
                                {{ app()->getLocale() === 'ar' ? 'لا توجد تسجيلات تدريب حالياً.' : 'No active training enrollments.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 1rem;">
            {{ $trainings->links() }}
        </div>
    </div>

    <!-- Enroll Modal -->
    <div id="assignTrainingModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: #ffffff; border-radius: 0.75rem; padding: 1.5rem; width: 100%; max-width: 480px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0;">{{ app()->getLocale() === 'ar' ? 'تسجيل موظف في دورة' : 'Enroll Employee into Course' }}</h3>
                <button type="button" onclick="document.getElementById('assignTrainingModal').style.display='none'" style="background: none; border: none; font-size: 1.25rem; cursor: pointer;">&times;</button>
            </div>
            <form action="{{ route('admin.hr.training.employee-training.assign') }}" method="POST">
                @csrf
                <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'الموظف المتدرب *' : 'Trainee Employee *' }}</label>
                        <select name="employee_id" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->full_name }} ({{ $emp->employee_number }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'البرنامج التدريبي *' : 'Training Program *' }}</label>
                        <select name="training_program_id" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                            @foreach($programs as $prg)
                                <option value="{{ $prg->id }}">{{ $prg->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1rem;">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('assignTrainingModal').style.display='none'">{{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}</button>
                        <button type="submit" class="btn btn-primary btn-sm">{{ app()->getLocale() === 'ar' ? 'تسجيل وإلحاق' : 'Enroll Employee' }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
