<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'تقييمات الأداء الوظيفي' : 'Employee Performance Reviews'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'متابعة تقييمات الكفاءة والنتائج الدورية وملاحظات المدراء' : 'Appraisal cycles, manager assessments, evaluation ratings, and performance feedback'"
>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0; color: var(--text-color, #1E293B);">
                <i class="fa-solid fa-chart-line text-emerald-500 mr-2 ml-2"></i> 
                {{ app()->getLocale() === 'ar' ? 'تقييمات الأداء' : 'Performance Reviews' }}
            </h2>
        </div>
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <a href="{{ route('admin.hr.performance.goals') }}" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-bullseye mr-1 ml-1 text-rose-500"></i> {{ app()->getLocale() === 'ar' ? 'الأهداف' : 'Goals' }}
            </a>
            <a href="{{ route('admin.hr.performance.kpis') }}" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-gauge mr-1 ml-1 text-sky-500"></i> {{ app()->getLocale() === 'ar' ? 'مؤشرات KPI' : 'KPIs' }}
            </a>
            <a href="{{ route('admin.hr.performance.promotions') }}" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-arrow-trend-up mr-1 ml-1 text-xs"></i> {{ app()->getLocale() === 'ar' ? 'الترقيات الوظيفية' : 'Promotions' }}
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="padding: 0.75rem 1rem; margin-bottom: 1rem; border-radius: 0.5rem; background: #DCFCE7; color: #166534; border: 1px solid #BBF7D0;">
            <i class="fa-solid fa-circle-check mr-1 ml-1"></i> {{ session('success') }}
        </div>
    @endif

    <!-- Reviews Table -->
    <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
        <div class="table-responsive">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                <thead>
                    <tr style="border-bottom: 2px solid #E2E8F0; text-align: left; color: #64748B;">
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الموظف' : 'Employee' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'دورة التقييم' : 'Review Cycle' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'المقيّم' : 'Reviewer' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'ملاحظات الأداء' : 'Feedback / Notes' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'تاريخ الاعتماد' : 'Approved Date' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reviews as $rev)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.75rem 0.5rem;">
                                <a href="{{ route('admin.hr.employees.show', $rev->employee_id) }}" style="color: #0284C7; font-weight: 700; text-decoration: none;">
                                    {{ $rev->employee?->full_name ?? 'N/A' }}
                                </a>
                                <span style="font-size: 0.75rem; color: #64748B; display: block;">
                                    {{ $rev->employee?->employee_number }} &bull; {{ $rev->employee?->department?->name }}
                                </span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; font-weight: 600; color: #334155;">
                                {{ $rev->cycle?->name ?? 'Standard Annual Review' }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem; color: #334155;">
                                {{ $rev->reviewer?->full_name ?? 'HR Manager' }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem; color: #64748B; max-width: 250px;">
                                {{ \Illuminate\Support\Str::limit($rev->overall_notes ?? 'Performance appraisal completed with satisfactory feedback.', 80) }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <span class="badge" style="background: {{ $rev->status === 'approved' ? '#DCFCE7' : '#FEF3C7' }}; color: {{ $rev->status === 'approved' ? '#166534' : '#92400E' }}; padding: 0.2rem 0.5rem; border-radius: 999px; font-weight: 700;">
                                    {{ ucfirst($rev->status) }}
                                </span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; color: #64748B;">
                                {{ $rev->approved_at ? \Carbon\Carbon::parse($rev->approved_at)->format('Y-m-d') : 'Pending' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 3rem; color: #94A3B8;">
                                <i class="fa-solid fa-chart-line" style="font-size: 2.5rem; margin-bottom: 0.75rem; display: block; opacity: 0.4;"></i>
                                {{ app()->getLocale() === 'ar' ? 'لا توجد مراجعات أداء مسجلة حتى الآن.' : 'No performance appraisals recorded.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 1rem;">
            {{ $reviews->links() }}
        </div>
    </div>
</x-layouts.admin>
