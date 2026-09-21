<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'صندوق الشكاوى والتظلمات الإدارية' : 'Confidential Employee Grievances'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'متابعة شكاوى الموظفين بسرية، التحقيق واتخاذ الإجراءات التصحيحية' : 'Confidential grievance investigation, employee conflict resolution, and internal audits'"
>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0; color: var(--text-color, #1E293B);">
                <i class="fa-solid fa-triangle-exclamation text-rose-500 mr-2 ml-2"></i> 
                {{ app()->getLocale() === 'ar' ? 'سجل الشكاوى والتظلمات' : 'Employee Grievance Log' }}
            </h2>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('admin.hr.requests.index') }}" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-arrow-left mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'كل الطلبات' : 'All Requests' }}
            </a>
            <a href="{{ route('admin.hr.requests.suggestions') }}" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-lightbulb mr-1 ml-1 text-amber-500"></i> {{ app()->getLocale() === 'ar' ? 'المقترحات' : 'Suggestions' }}
            </a>
        </div>
    </div>

    <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
        <div class="table-responsive">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                <thead>
                    <tr style="border-bottom: 2px solid #E2E8F0; text-align: left; color: #64748B;">
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'المشتكي' : 'Employee' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'موضوع الشكوى' : 'Grievance Subject' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'التفاصيل والملابسات' : 'Details' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'رد الإدارة والتحقيق' : 'HR Response' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($complaints as $comp)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.75rem 0.5rem;">
                                <a href="{{ route('admin.hr.employees.show', $comp->employee_id) }}" style="color: #0284C7; font-weight: 700; text-decoration: none;">
                                    {{ $comp->employee?->full_name ?? 'N/A' }}
                                </a>
                                <span style="font-size: 0.75rem; color: #64748B; display: block;">{{ $comp->employee?->department?->name }}</span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; font-weight: 700; color: #0F172A;">
                                {{ $comp->subject }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem; color: #475569; max-width: 250px;">
                                {{ $comp->description }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem; color: #0284C7; font-size: 0.8rem;">
                                {{ $comp->admin_response ?? 'Under investigation by HR department' }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <span class="badge" style="background: {{ $comp->status === 'resolved' || $comp->status === 'completed' ? '#DCFCE7' : '#FEF3C7' }}; color: {{ $comp->status === 'resolved' || $comp->status === 'completed' ? '#166534' : '#92400E' }}; padding: 0.2rem 0.5rem; border-radius: 999px; font-weight: 700;">
                                    {{ ucfirst($comp->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 3rem; color: #94A3B8;">
                                <i class="fa-solid fa-shield-heart" style="font-size: 2.5rem; margin-bottom: 0.75rem; display: block; opacity: 0.4;"></i>
                                {{ app()->getLocale() === 'ar' ? 'لا توجد شكاوى أو نزاعات مسجلة حالياً.' : 'Healthy workplace environment — No grievances reported.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 1rem;">
            {{ $complaints->links() }}
        </div>
    </div>
</x-layouts.admin>
