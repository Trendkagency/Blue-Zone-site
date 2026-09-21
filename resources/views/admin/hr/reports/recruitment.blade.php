<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'تقرير التوظيف ومسار المرشحين' : 'Talent Acquisition & Hiring Pipeline Report'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'إحصائيات الشواغر المفتوحة، تدفق المرشحين، ومراحل الفرز والتوظيف' : 'Talent acquisition pipeline analytics, conversion rates, and applicant tracking report'"
>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0; color: var(--text-color, #1E293B);">
                <i class="fa-solid fa-user-plus text-purple-500 mr-2 ml-2"></i> 
                {{ app()->getLocale() === 'ar' ? 'تقرير التوظيف والاستقطاب' : 'Hiring Pipeline Analytics' }}
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

    <!-- Vacancies Summary -->
    <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0); margin-bottom: 1.5rem;">
        <h3 style="font-size: 1rem; font-weight: 700; color: #0F172A; margin: 0 0 1rem;">
            <i class="fa-solid fa-briefcase text-purple-500 mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'الشواغر الوظيفية المفتوحة ومعدل الإقبال' : 'Open Job Vacancies' }}
        </h3>
        <div class="table-responsive">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                <thead>
                    <tr style="border-bottom: 2px solid #E2E8F0; text-align: left; color: #64748B;">
                        <th style="padding: 0.5rem;">{{ app()->getLocale() === 'ar' ? 'المسمى الوظيفي' : 'Job Title' }}</th>
                        <th style="padding: 0.5rem;">{{ app()->getLocale() === 'ar' ? 'عدد الشواغر' : 'Openings' }}</th>
                        <th style="padding: 0.5rem;">{{ app()->getLocale() === 'ar' ? 'المتقدمون' : 'Applicants' }}</th>
                        <th style="padding: 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vacancies as $vac)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.6rem 0.5rem; font-weight: 700; color: #0F172A;">{{ $vac->title }}</td>
                            <td style="padding: 0.6rem 0.5rem; font-weight: 600;">{{ $vac->openings ?? 1 }}</td>
                            <td style="padding: 0.6rem 0.5rem;">
                                <span style="background: #F3E8FF; color: #7E22CE; font-weight: 700; font-size: 0.75rem; padding: 0.2rem 0.5rem; border-radius: 999px;">
                                    {{ $vac->candidates_count ?? 0 }} candidates
                                </span>
                            </td>
                            <td style="padding: 0.6rem 0.5rem;">
                                <span class="badge" style="background: #DCFCE7; color: #166534; padding: 0.15rem 0.45rem; border-radius: 999px; font-weight: 700; font-size: 0.75rem;">
                                    {{ ucfirst($vac->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 2rem; color: #94A3B8;">No vacancies registered.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Candidate Pipeline Sample -->
    <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
        <h3 style="font-size: 1rem; font-weight: 700; color: #0F172A; margin: 0 0 1rem;">
            <i class="fa-solid fa-user-group text-sky-500 mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'سجل المرشحين الأخير' : 'Recent Candidate Inflow' }}
        </h3>
        <div class="table-responsive">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                <thead>
                    <tr style="border-bottom: 2px solid #E2E8F0; text-align: left; color: #64748B;">
                        <th style="padding: 0.5rem;">{{ app()->getLocale() === 'ar' ? 'المرشح' : 'Candidate' }}</th>
                        <th style="padding: 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الوظيفة المتقدم لها' : 'Applied Position' }}</th>
                        <th style="padding: 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الخبرة السابقة' : 'Experience' }}</th>
                        <th style="padding: 0.5rem;">{{ app()->getLocale() === 'ar' ? 'المرحلة الحالية' : 'Pipeline Stage' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($candidates as $cand)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.6rem 0.5rem; font-weight: 700; color: #0F172A;">{{ $cand->name }}</td>
                            <td style="padding: 0.6rem 0.5rem; color: #334155;">{{ $cand->applied_position ?? $cand->vacancy?->title }}</td>
                            <td style="padding: 0.6rem 0.5rem; color: #64748B;">{{ $cand->experience ?? '—' }}</td>
                            <td style="padding: 0.6rem 0.5rem;">
                                <span class="badge" style="background: #E0F2FE; color: #0369A1; padding: 0.15rem 0.5rem; border-radius: 999px; font-weight: 700; font-size: 0.75rem;">
                                    {{ ucfirst($cand->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 2rem; color: #94A3B8;">No candidates in current pool.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
