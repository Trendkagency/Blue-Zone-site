<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'سجل شهادات التدريب والاعتماد' : 'Training Certificates Registry'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'شهادات إتمام الدورات، الاعتمادات المهنية، وتواريخ الصلاحية للموظفين' : 'Employee certifications, professional accreditations, and credential validity dates'"
>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0; color: var(--text-color, #1E293B);">
                <i class="fa-solid fa-certificate text-amber-500 mr-2 ml-2"></i> 
                {{ app()->getLocale() === 'ar' ? 'الشهادات التدريبية المعتمدة' : 'Accredited Certificates' }}
            </h2>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('admin.hr.training.programs') }}" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-graduation-cap mr-1 ml-1 text-sky-500"></i> {{ app()->getLocale() === 'ar' ? 'البرامج' : 'Programs' }}
            </a>
            <a href="{{ route('admin.hr.training.employee-training') }}" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-users mr-1 ml-1 text-indigo-500"></i> {{ app()->getLocale() === 'ar' ? 'التسجيلات' : 'Enrollments' }}
            </a>
        </div>
    </div>

    <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
        <div class="table-responsive">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                <thead>
                    <tr style="border-bottom: 2px solid #E2E8F0; text-align: left; color: #64748B;">
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الموظف الحاصل' : 'Employee' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'البرنامج / الدورة' : 'Training Program' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'رقم الشهادة' : 'Certificate #' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'تاريخ الإصدار' : 'Issue Date' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'تاريخ الانتهاء' : 'Expiry Date' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($certificates as $cert)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.75rem 0.5rem;">
                                <a href="{{ route('admin.hr.employees.show', $cert->employee_id) }}" style="color: #0284C7; font-weight: 700; text-decoration: none;">
                                    {{ $cert->employee?->full_name ?? 'N/A' }}
                                </a>
                                <span style="font-size: 0.75rem; color: #64748B; display: block;">{{ $cert->employee?->department?->name }}</span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; font-weight: 600; color: #0F172A;">
                                {{ $cert->program?->name ?? 'Professional Course' }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem; font-family: monospace; font-weight: 700; color: #475569;">
                                {{ $cert->certificate_number ?? 'CERT-'.str_pad($cert->id, 5, '0', STR_PAD_LEFT) }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem; color: #334155;">{{ $cert->issue_date ?? '—' }}</td>
                            <td style="padding: 0.75rem 0.5rem; color: #334155;">{{ $cert->expiry_date ?? 'Permanent' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 3rem; color: #94A3B8;">
                                <i class="fa-solid fa-certificate" style="font-size: 2.5rem; margin-bottom: 0.75rem; display: block; opacity: 0.4;"></i>
                                {{ app()->getLocale() === 'ar' ? 'لا توجد شهادات مسجلة حالياً.' : 'No certificates logged yet.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 1rem;">
            {{ $certificates->links() }}
        </div>
    </div>
</x-layouts.admin>
