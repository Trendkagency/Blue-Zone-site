<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'تقرير برامج التدريب والتطوير' : 'Workforce Training & Development Report'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'سجل مشاركات الموظفين بالدورات التدريبية ومعدلات الإنجاز' : 'Employee participation logs, course completions, and capability building records'"
>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0; color: var(--text-color, #1E293B);">
                <i class="fa-solid fa-graduation-cap text-amber-500 mr-2 ml-2"></i> 
                {{ app()->getLocale() === 'ar' ? 'تقرير تدريب الموظفين' : 'Training Activity Audit' }}
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
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'البرنامج التدريبي' : 'Training Program' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'القسم' : 'Department' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'تاريخ التكليف' : 'Assigned' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'النتيجة' : 'Result' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($trainings as $tr)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.75rem 0.5rem;">
                                <a href="{{ route('admin.hr.employees.show', $tr->employee_id) }}" style="color: #0284C7; font-weight: 700; text-decoration: none;">
                                    {{ $tr->employee?->full_name ?? 'N/A' }}
                                </a>
                                <span style="font-size: 0.75rem; color: #64748B; display: block;">{{ $tr->employee?->employee_number }}</span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; font-weight: 600; color: #0F172A;">
                                {{ $tr->program?->name ?? 'Course' }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem; color: #334155;">{{ $tr->employee?->department?->name }}</td>
                            <td style="padding: 0.75rem 0.5rem; color: #64748B;">
                                {{ $tr->assigned_at ? \Carbon\Carbon::parse($tr->assigned_at)->format('Y-m-d') : '—' }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem; font-weight: 700; color: #059669;">
                                {{ $tr->result ?? 'Satisfactory' }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <span class="badge" style="background: {{ $tr->status === 'completed' ? '#DCFCE7' : '#FEF3C7' }}; color: {{ $tr->status === 'completed' ? '#166534' : '#92400E' }}; padding: 0.2rem 0.5rem; border-radius: 999px; font-weight: 700;">
                                    {{ ucfirst($tr->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 3rem; color: #94A3B8;">
                                No training participation records logged.
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
</x-layouts.admin>
