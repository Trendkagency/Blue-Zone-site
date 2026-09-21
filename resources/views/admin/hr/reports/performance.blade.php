<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'تقرير تقييمات الأداء الشامل' : 'Workforce Performance Evaluation Report'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'سجل نتائج التقييمات السنوية وملاحظات الكفاءة للأقسام' : 'Organizational appraisal distributions, review outcomes, and employee ratings'"
>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0; color: var(--text-color, #1E293B);">
                <i class="fa-solid fa-chart-line text-blue-500 mr-2 ml-2"></i> 
                {{ app()->getLocale() === 'ar' ? 'تقرير مراجعات الأداء' : 'Performance Appraisals Audit' }}
            </h2>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('admin.hr.reports.index') }}" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-arrow-left mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'مركز التقارير' : 'Reports Hub' }}
            </a>
            <button type="button" class="btn btn-primary btn-sm" onclick="window.print()">
                <i class="fa-solid fa-print mr-1 ml-1 text-xs"></i> {{ app()->getLocale() === 'ar' ? 'طباعة' : 'Print' }}
            </button>
        </div>
    </div>

    <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
        <div class="table-responsive">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                <thead>
                    <tr style="border-bottom: 2px solid #E2E8F0; text-align: left; color: #64748B;">
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الموظف' : 'Employee' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'القسم' : 'Department' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الدورة' : 'Cycle' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'المقيّم' : 'Reviewer' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الملاحظات والنتائج' : 'Summary Feedback' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reviews as $rev)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.75rem 0.5rem;">
                                <a href="{{ route('admin.hr.employees.show', $rev->employee_id) }}" style="color: #0284C7; font-weight: 700; text-decoration: none;">
                                    {{ $rev->employee?->full_name ?? 'N/A' }}
                                </a>
                                <span style="font-size: 0.75rem; color: #64748B; display: block;">{{ $rev->employee?->employee_number }}</span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; color: #334155;">{{ $rev->employee?->department?->name }}</td>
                            <td style="padding: 0.75rem 0.5rem; font-weight: 600; color: #0F172A;">{{ $rev->cycle?->name ?? 'Standard Cycle' }}</td>
                            <td style="padding: 0.75rem 0.5rem; color: #334155;">{{ $rev->reviewer?->full_name ?? 'HR Lead' }}</td>
                            <td style="padding: 0.75rem 0.5rem; color: #64748B; max-width: 250px;">
                                {{ \Illuminate\Support\Str::limit($rev->overall_notes ?? 'Performance appraisal completed.', 70) }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <span class="badge" style="background: {{ $rev->status === 'approved' ? '#DCFCE7' : '#FEF3C7' }}; color: {{ $rev->status === 'approved' ? '#166534' : '#92400E' }}; padding: 0.2rem 0.5rem; border-radius: 999px; font-weight: 700;">
                                    {{ ucfirst($rev->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 3rem; color: #94A3B8;">
                                No performance review records found.
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
