<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'مركز تقارير الموارد البشرية والتحليلات' : 'HR Analytics & Reports Hub'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'لوحة مركزية لتقارير القوى العاملة، الحضور، الإجازات، والرواتب' : 'Central executive reporting intelligence, workforce KPIs, and departmental breakdown'"
>
    <!-- Executive KPI Highlights -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
        <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: #ffffff; border: 1px solid #E2E8F0; display: flex; align-items: center; gap: 1rem;">
            <div style="width: 44px; height: 44px; border-radius: 0.5rem; background: #EEF2FF; color: #4F46E5; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <span style="font-size: 0.75rem; color: #64748B; font-weight: 600; display: block;">{{ app()->getLocale() === 'ar' ? 'إجمالي الموظفين' : 'Total Headcount' }}</span>
                <strong style="font-size: 1.35rem; color: #0F172A;">{{ $totalEmployees }}</strong>
            </div>
        </div>

        <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: #ffffff; border: 1px solid #E2E8F0; display: flex; align-items: center; gap: 1rem;">
            <div style="width: 44px; height: 44px; border-radius: 0.5rem; background: #DCFCE7; color: #166534; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                <i class="fa-solid fa-user-check"></i>
            </div>
            <div>
                <span style="font-size: 0.75rem; color: #64748B; font-weight: 600; display: block;">{{ app()->getLocale() === 'ar' ? 'الموظفون النشطون' : 'Active Employees' }}</span>
                <strong style="font-size: 1.35rem; color: #166534;">{{ $activeEmployees }}</strong>
            </div>
        </div>

        <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: #ffffff; border: 1px solid #E2E8F0; display: flex; align-items: center; gap: 1rem;">
            <div style="width: 44px; height: 44px; border-radius: 0.5rem; background: #FEF3C7; color: #92400E; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div>
                <span style="font-size: 0.75rem; color: #64748B; font-weight: 600; display: block;">{{ app()->getLocale() === 'ar' ? 'فترة التجربة' : 'On Probation' }}</span>
                <strong style="font-size: 1.35rem; color: #92400E;">{{ $probationEmployees }}</strong>
            </div>
        </div>

        <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: #ffffff; border: 1px solid #E2E8F0; display: flex; align-items: center; gap: 1rem;">
            <div style="width: 44px; height: 44px; border-radius: 0.5rem; background: #FEE2E2; color: #991B1B; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
            </div>
            <div>
                <span style="font-size: 0.75rem; color: #64748B; font-weight: 600; display: block;">{{ app()->getLocale() === 'ar' ? 'معدل دوران العمالة' : 'Turnover Rate' }}</span>
                <strong style="font-size: 1.35rem; color: #991B1B;">{{ $turnoverRate }}%</strong>
            </div>
        </div>
    </div>

    <!-- Reports Directory Matrix -->
    <h3 style="font-size: 1.05rem; font-weight: 700; color: #0F172A; margin: 0 0 1rem;">
        <i class="fa-solid fa-folder-open text-sky-500 mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'فهارس وموديولات التقارير التخصصية' : 'Specialized Analytics Reports' }}
    </h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
        <a href="{{ route('admin.hr.reports.employees') }}" class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: #ffffff; border: 1px solid #E2E8F0; text-decoration: none; display: flex; gap: 1rem; align-items: flex-start; transition: transform 0.15s ease;">
            <div style="width: 40px; height: 40px; border-radius: 0.5rem; background: #E0F2FE; color: #0284C7; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">
                <i class="fa-solid fa-address-book"></i>
            </div>
            <div>
                <h4 style="margin: 0; font-size: 0.95rem; font-weight: 700; color: #0F172A;">{{ app()->getLocale() === 'ar' ? 'تقرير القوى العاملة' : 'Workforce Directory' }}</h4>
                <p style="margin: 0.25rem 0 0; font-size: 0.75rem; color: #64748B;">{{ app()->getLocale() === 'ar' ? 'توزيع الموظفين حسب الأقسام والفروع والحالات' : 'Employees by department, branch, and status' }}</p>
            </div>
        </a>

        <a href="{{ route('admin.hr.reports.attendance') }}" class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: #ffffff; border: 1px solid #E2E8F0; text-decoration: none; display: flex; gap: 1rem; align-items: flex-start;">
            <div style="width: 40px; height: 40px; border-radius: 0.5rem; background: #DCFCE7; color: #166534; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">
                <i class="fa-solid fa-business-time"></i>
            </div>
            <div>
                <h4 style="margin: 0; font-size: 0.95rem; font-weight: 700; color: #0F172A;">{{ app()->getLocale() === 'ar' ? 'تقرير الحضور والانصراف' : 'Attendance & Punctuality' }}</h4>
                <p style="margin: 0.25rem 0 0; font-size: 0.75rem; color: #64748B;">{{ app()->getLocale() === 'ar' ? 'نسب الانضباط، التأخيرات وساعات العمل الإضافي' : 'Timesheets, late arrivals, and overtime costs' }}</p>
            </div>
        </a>

        <a href="{{ route('admin.hr.reports.leave') }}" class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: #ffffff; border: 1px solid #E2E8F0; text-decoration: none; display: flex; gap: 1rem; align-items: flex-start;">
            <div style="width: 40px; height: 40px; border-radius: 0.5rem; background: #FDF2F8; color: #DB2777; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">
                <i class="fa-solid fa-umbrella-beach"></i>
            </div>
            <div>
                <h4 style="margin: 0; font-size: 0.95rem; font-weight: 700; color: #0F172A;">{{ app()->getLocale() === 'ar' ? 'تقرير استهلاك الإجازات' : 'Leave Utilization' }}</h4>
                <p style="margin: 0.25rem 0 0; font-size: 0.75rem; color: #64748B;">{{ app()->getLocale() === 'ar' ? 'الأيام المستهلكة والأرصدة المتبقية لكل موظف' : 'Leave ledger, balance consumption, and trends' }}</p>
            </div>
        </a>

        <a href="{{ route('admin.hr.reports.payroll') }}" class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: #ffffff; border: 1px solid #E2E8F0; text-decoration: none; display: flex; gap: 1rem; align-items: flex-start;">
            <div style="width: 40px; height: 40px; border-radius: 0.5rem; background: #ECFDF5; color: #059669; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">
                <i class="fa-solid fa-money-check-dollar"></i>
            </div>
            <div>
                <h4 style="margin: 0; font-size: 0.95rem; font-weight: 700; color: #0F172A;">{{ app()->getLocale() === 'ar' ? 'تقرير تكاليف الرواتب' : 'Payroll Expenditure' }}</h4>
                <p style="margin: 0.25rem 0 0; font-size: 0.75rem; color: #64748B;">{{ app()->getLocale() === 'ar' ? 'ملخص الأجور الشهرية، البدلات، واستقطاعات السلف' : 'Monthly salary totals, allowances, and net pay' }}</p>
            </div>
        </a>

        <a href="{{ route('admin.hr.reports.recruitment') }}" class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: #ffffff; border: 1px solid #E2E8F0; text-decoration: none; display: flex; gap: 1rem; align-items: flex-start;">
            <div style="width: 40px; height: 40px; border-radius: 0.5rem; background: #F5F3FF; color: #7C3AED; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">
                <i class="fa-solid fa-user-plus"></i>
            </div>
            <div>
                <h4 style="margin: 0; font-size: 0.95rem; font-weight: 700; color: #0F172A;">{{ app()->getLocale() === 'ar' ? 'تقرير مسار التوظيف' : 'Recruitment Pipeline' }}</h4>
                <p style="margin: 0.25rem 0 0; font-size: 0.75rem; color: #64748B;">{{ app()->getLocale() === 'ar' ? 'الشواغر، المرشحون، المقابلات، وعروض العمل' : 'Vacancies, interview pipelines, and hiring metrics' }}</p>
            </div>
        </a>

        <a href="{{ route('admin.hr.reports.performance') }}" class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: #ffffff; border: 1px solid #E2E8F0; text-decoration: none; display: flex; gap: 1rem; align-items: flex-start;">
            <div style="width: 40px; height: 40px; border-radius: 0.5rem; background: #EFF6FF; color: #2563EB; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">
                <i class="fa-solid fa-chart-line"></i>
            </div>
            <div>
                <h4 style="margin: 0; font-size: 0.95rem; font-weight: 700; color: #0F172A;">{{ app()->getLocale() === 'ar' ? 'تقرير تقييم الأداء' : 'Performance Appraisals' }}</h4>
                <p style="margin: 0.25rem 0 0; font-size: 0.75rem; color: #64748B;">{{ app()->getLocale() === 'ar' ? 'نتائج التقييمات الدورية، الأهداف ومؤشرات KPI' : 'Periodic review scores and promotion milestones' }}</p>
            </div>
        </a>

        <a href="{{ route('admin.hr.reports.training') }}" class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: #ffffff; border: 1px solid #E2E8F0; text-decoration: none; display: flex; gap: 1rem; align-items: flex-start;">
            <div style="width: 40px; height: 40px; border-radius: 0.5rem; background: #FFFBEB; color: #D97706; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>
            <div>
                <h4 style="margin: 0; font-size: 0.95rem; font-weight: 700; color: #0F172A;">{{ app()->getLocale() === 'ar' ? 'تقرير التدريب والتطوير' : 'Training & Certifications' }}</h4>
                <p style="margin: 0.25rem 0 0; font-size: 0.75rem; color: #64748B;">{{ app()->getLocale() === 'ar' ? 'ساعات التدريب المكتملة، التكاليف، والشهادات' : 'Training hours, investment, and credentials' }}</p>
            </div>
        </a>
    </div>

    <!-- Department Distribution Table -->
    <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
        <h3 style="font-size: 1rem; font-weight: 700; color: #0F172A; margin: 0 0 1rem;">
            <i class="fa-solid fa-sitemap text-indigo-500 mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'توزيع القوى العاملة حسب الأقسام' : 'Headcount Distribution by Department' }}
        </h3>
        <div class="table-responsive">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                <thead>
                    <tr style="border-bottom: 2px solid #E2E8F0; text-align: left; color: #64748B;">
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'القسم' : 'Department' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'كود القسم' : 'Code' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'عدد الموظفين' : 'Employees' }}</th>
                        <th style="padding: 0.75rem 0.5rem; text-align: right;">{{ app()->getLocale() === 'ar' ? 'النسبة من الإجمالي' : 'Workforce Share' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($deptDistribution as $dept)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.75rem 0.5rem; font-weight: 700; color: #0F172A;">
                                {{ $dept->name }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem; font-family: monospace; color: #64748B;">
                                {{ $dept->code ?? '—' }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem; font-weight: 600; color: #0284C7;">
                                {{ $dept->employees_count }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem; text-align: right;">
                                @php
                                    $pct = $totalEmployees > 0 ? round(($dept->employees_count / $totalEmployees) * 100, 1) : 0;
                                @endphp
                                <div style="display: inline-flex; align-items: center; gap: 0.5rem;">
                                    <div style="width: 80px; height: 6px; background: #E2E8F0; border-radius: 999px; overflow: hidden;">
                                        <div style="height: 100%; width: {{ $pct }}%; background: #4F46E5; border-radius: 999px;"></div>
                                    </div>
                                    <span style="font-weight: 700; font-family: monospace; font-size: 0.8rem; color: #4F46E5;">{{ $pct }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 2rem; color: #94A3B8;">
                                No active departmental workforce distributions yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
