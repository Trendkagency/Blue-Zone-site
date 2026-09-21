<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'الأهداف ومؤشرات الأداء للموظفين' : 'Employee Performance Goals'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'تحديد وتتبع الأهداف الاستراتيجية ومعدلات الإنجاز الفعلي' : 'Set objective targets, track milestones, and measure completion progress'"
>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0; color: var(--text-color, #1E293B);">
                <i class="fa-solid fa-bullseye text-rose-500 mr-2 ml-2"></i> 
                {{ app()->getLocale() === 'ar' ? 'أهداف الأداء' : 'Performance Goals' }}
            </h2>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('admin.hr.performance.reviews') }}" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-arrow-left mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'التقييمات' : 'Reviews' }}
            </a>
            <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('newGoalModal').style.display='flex'">
                <i class="fa-solid fa-plus text-xs mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'تعيين هدف جديد' : 'Assign Goal' }}
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
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'عنوان الهدف' : 'Goal Title' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'المستهدف' : 'Target Target' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'نسبة الإنجاز' : 'Progress' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($goals as $goal)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.75rem 0.5rem;">
                                <a href="{{ route('admin.hr.employees.show', $goal->employee_id) }}" style="color: #0284C7; font-weight: 700; text-decoration: none;">
                                    {{ $goal->employee?->full_name ?? 'N/A' }}
                                </a>
                                <span style="font-size: 0.75rem; color: #64748B; display: block;">{{ $goal->employee?->department?->name }}</span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <div style="font-weight: 700; color: #0F172A;">{{ $goal->title }}</div>
                                <span style="font-size: 0.75rem; color: #64748B;">{{ $goal->description }}</span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; color: #334155; font-weight: 600;">
                                {{ $goal->target ?? '100%' }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <div style="flex: 1; max-width: 100px; height: 6px; background: #E2E8F0; border-radius: 999px; overflow: hidden;">
                                        <div style="height: 100%; width: {{ min(100, $goal->achievement_percentage) }}%; background: #10B981; border-radius: 999px;"></div>
                                    </div>
                                    <span style="font-weight: 700; font-family: monospace; font-size: 0.8rem; color: #10B981;">
                                        {{ $goal->achievement_percentage }}%
                                    </span>
                                </div>
                            </td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <span class="badge" style="background: {{ $goal->status === 'completed' ? '#DCFCE7' : '#FEF3C7' }}; color: {{ $goal->status === 'completed' ? '#166534' : '#92400E' }}; padding: 0.2rem 0.5rem; border-radius: 999px; font-weight: 700;">
                                    {{ ucfirst(str_replace('_', ' ', $goal->status)) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 3rem; color: #94A3B8;">
                                <i class="fa-solid fa-bullseye" style="font-size: 2.5rem; margin-bottom: 0.75rem; display: block; opacity: 0.4;"></i>
                                {{ app()->getLocale() === 'ar' ? 'لا توجد أهداف أداء مسجلة.' : 'No goals assigned yet.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 1rem;">
            {{ $goals->links() }}
        </div>
    </div>

    <!-- Assign Goal Modal -->
    <div id="newGoalModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: #ffffff; border-radius: 0.75rem; padding: 1.5rem; width: 100%; max-width: 480px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0;">{{ app()->getLocale() === 'ar' ? 'تعيين هدف وظيفي جديد' : 'Assign Performance Goal' }}</h3>
                <button type="button" onclick="document.getElementById('newGoalModal').style.display='none'" style="background: none; border: none; font-size: 1.25rem; cursor: pointer;">&times;</button>
            </div>
            <form action="{{ route('admin.hr.performance.goals.store') }}" method="POST">
                @csrf
                <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'الموظف *' : 'Employee *' }}</label>
                        <select name="employee_id" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->full_name }} ({{ $emp->employee_number }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'دورة التقييم *' : 'Performance Cycle *' }}</label>
                        <select name="cycle_id" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                            @foreach($cycles as $cyc)
                                <option value="{{ $cyc->id }}">{{ $cyc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'عنوان الهدف *' : 'Goal Title *' }}</label>
                        <input type="text" name="title" required placeholder="e.g. Expand Blue Zone Clinic Network by 15%" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'المستهدف الكمي' : 'Quantifiable Target' }}</label>
                        <input type="text" name="target" placeholder="e.g. 50 new partner clinics" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'الوصف' : 'Description' }}</label>
                        <textarea name="description" rows="2" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;"></textarea>
                    </div>
                    <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1rem;">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('newGoalModal').style.display='none'">{{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}</button>
                        <button type="submit" class="btn btn-primary btn-sm">{{ app()->getLocale() === 'ar' ? 'حفظ الهدف' : 'Assign Goal' }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
