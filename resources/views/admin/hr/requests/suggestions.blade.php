<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'بنك الأفكار ومقترحات التطوير' : 'Employee Innovation & Suggestions Box'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'أفكار ومقترحات الموظفين لتحسين بيئة العمل وخدمات المنتجات' : 'Workforce ideation, workflow enhancement proposals, and workplace innovations'"
>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0; color: var(--text-color, #1E293B);">
                <i class="fa-solid fa-lightbulb text-amber-500 mr-2 ml-2"></i> 
                {{ app()->getLocale() === 'ar' ? 'مقترحات الموظفين' : 'Suggestions Box' }}
            </h2>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('admin.hr.requests.index') }}" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-arrow-left mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'كل الطلبات' : 'All Requests' }}
            </a>
        </div>
    </div>

    <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
        <div class="table-responsive">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                <thead>
                    <tr style="border-bottom: 2px solid #E2E8F0; text-align: left; color: #64748B;">
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'صاحب المقترح' : 'Employee' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'فكرة المقترح' : 'Suggestion Title' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الشرح والأثر المتوقع' : 'Description & Impact' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'تاريخ التقديم' : 'Submitted' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suggestions as $sug)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.75rem 0.5rem;">
                                <a href="{{ route('admin.hr.employees.show', $sug->employee_id) }}" style="color: #0284C7; font-weight: 700; text-decoration: none;">
                                    {{ $sug->employee?->full_name ?? 'N/A' }}
                                </a>
                                <span style="font-size: 0.75rem; color: #64748B; display: block;">{{ $sug->employee?->department?->name }}</span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; font-weight: 700; color: #0F172A;">
                                {{ $sug->subject }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem; color: #475569; max-width: 320px;">
                                {{ $sug->description }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem; color: #64748B;">
                                {{ $sug->created_at?->format('Y-m-d') }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <span class="badge" style="background: #FEF3C7; color: #92400E; padding: 0.2rem 0.5rem; border-radius: 999px; font-weight: 700;">
                                    {{ ucfirst($sug->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 3rem; color: #94A3B8;">
                                <i class="fa-solid fa-lightbulb" style="font-size: 2.5rem; margin-bottom: 0.75rem; display: block; opacity: 0.4;"></i>
                                {{ app()->getLocale() === 'ar' ? 'لا توجد مقترحات مسجلة حالياً.' : 'No suggestions submitted yet.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 1rem;">
            {{ $suggestions->links() }}
        </div>
    </div>
</x-layouts.admin>
