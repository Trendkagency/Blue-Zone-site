<x-layouts.admin 
    :pageTitle="__('hr.dashboard_title')" 
    :pageSubtitle="__('hr.dashboard_subtitle')"
>
    <!-- Quick Actions Bar -->
    <div style="display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; background: var(--card-bg, #ffffff); padding: 1rem 1.25rem; border-radius: 0.75rem; border: 1px solid var(--border-color, #E2E8F0); box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <div style="font-weight: 700; font-size: 0.95rem; color: var(--text-color, #1E293B);">
            <i class="fa-solid fa-bolt text-amber-500 mr-2 ml-2"></i> {{ __('hr.quick_actions') }}
        </div>
        <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
            <a href="{{ route('admin.hr.employees.create') }}" class="btn btn-primary btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem;">
                <i class="fa-solid fa-user-plus text-xs"></i> {{ __('hr.add_employee') }}
            </a>
            <a href="{{ route('admin.hr.attendance.daily') }}" class="btn btn-secondary btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem;">
                <i class="fa-solid fa-clipboard-user text-xs"></i> {{ __('hr.record_attendance') }}
            </a>
            <a href="{{ route('admin.hr.leave.requests') }}" class="btn btn-outline btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem;">
                <i class="fa-solid fa-calendar-plus text-xs"></i> {{ __('hr.request_leave') }}
            </a>
            <a href="{{ route('admin.hr.payroll.periods') }}" class="btn btn-outline btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem;">
                <i class="fa-solid fa-money-bill-transfer text-xs"></i> {{ __('hr.start_payroll') }}
            </a>
            {{--
            <a href="{{ route('admin.hr.organization.departments.index') }}" class="btn btn-ghost btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem;">
                <i class="fa-solid fa-sitemap text-xs text-teal-500"></i> {{ app()->getLocale() === 'ar' ? 'الأقسام والوظائف' : 'Org Structure' }}
            </a>
            --}}
            <a href="{{ route('admin.hr.settings.index') }}" class="btn btn-ghost btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem;">
                <i class="fa-solid fa-sliders text-xs text-violet-500"></i> {{ __('hr.settings') }}
            </a>
        </div>
    </div>

    <!-- Core HR Modules Hub Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
        <a href="{{ route('admin.hr.employees.index') }}" style="text-decoration: none; color: inherit; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0); border-radius: 0.75rem; padding: 1rem; display: flex; align-items: center; gap: 0.75rem; transition: transform 0.15s ease, box-shadow 0.15s ease;" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 12px rgba(0,0,0,0.06)';" onmouseout="this.style.transform='none';this.style.boxShadow='none';">
            <div style="width: 42px; height: 42px; border-radius: 0.6rem; background: rgba(2, 132, 199, 0.1); color: #0284C7; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <div style="font-weight: 700; font-size: 0.9rem;">{{ app()->getLocale() === 'ar' ? 'دليل الموظفين' : 'Employees' }}</div>
                <div style="font-size: 0.75rem; color: #64748B;">{{ $totalEmployees }} {{ app()->getLocale() === 'ar' ? 'موظف' : 'Profiles' }}</div>
            </div>
        </a>

        <a href="{{ route('admin.hr.attendance.daily') }}" style="text-decoration: none; color: inherit; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0); border-radius: 0.75rem; padding: 1rem; display: flex; align-items: center; gap: 0.75rem; transition: transform 0.15s ease, box-shadow 0.15s ease;" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 12px rgba(0,0,0,0.06)';" onmouseout="this.style.transform='none';this.style.boxShadow='none';">
            <div style="width: 42px; height: 42px; border-radius: 0.6rem; background: rgba(16, 185, 129, 0.1); color: #10B981; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">
                <i class="fa-solid fa-clipboard-user"></i>
            </div>
            <div>
                <div style="font-weight: 700; font-size: 0.9rem;">{{ app()->getLocale() === 'ar' ? 'الحضور والانصراف' : 'Attendance' }}</div>
                <div style="font-size: 0.75rem; color: #64748B;">{{ $presentToday }} {{ app()->getLocale() === 'ar' ? 'حاضر اليوم' : 'Present' }}</div>
            </div>
        </a>

        <a href="{{ route('admin.hr.leave.requests') }}" style="text-decoration: none; color: inherit; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0); border-radius: 0.75rem; padding: 1rem; display: flex; align-items: center; gap: 0.75rem; transition: transform 0.15s ease, box-shadow 0.15s ease;" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 12px rgba(0,0,0,0.06)';" onmouseout="this.style.transform='none';this.style.boxShadow='none';">
            <div style="width: 42px; height: 42px; border-radius: 0.6rem; background: rgba(236, 72, 153, 0.1); color: #EC4899; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
            <div>
                <div style="font-weight: 700; font-size: 0.9rem;">{{ app()->getLocale() === 'ar' ? 'الإجازات' : 'Leaves' }}</div>
                <div style="font-size: 0.75rem; color: #64748B;">{{ $pendingLeaves }} {{ app()->getLocale() === 'ar' ? 'طلب معلق' : 'Pending' }}</div>
            </div>
        </a>

        <a href="{{ route('admin.hr.payroll.periods') }}" style="text-decoration: none; color: inherit; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0); border-radius: 0.75rem; padding: 1rem; display: flex; align-items: center; gap: 0.75rem; transition: transform 0.15s ease, box-shadow 0.15s ease;" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 12px rgba(0,0,0,0.06)';" onmouseout="this.style.transform='none';this.style.boxShadow='none';">
            <div style="width: 42px; height: 42px; border-radius: 0.6rem; background: rgba(20, 184, 166, 0.1); color: #14B8A6; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">
                <i class="fa-solid fa-money-bill-wave"></i>
            </div>
            <div>
                <div style="font-weight: 700; font-size: 0.9rem;">{{ app()->getLocale() === 'ar' ? 'مسيرات الرواتب' : 'Payroll' }}</div>
                <div style="font-size: 0.75rem; color: #64748B;">{{ $pendingPayroll }} {{ app()->getLocale() === 'ar' ? 'فترة نشطة' : 'Periods' }}</div>
            </div>
        </a>

        {{--
        <a href="{{ route('admin.hr.organization.departments.index') }}" style="text-decoration: none; color: inherit; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0); border-radius: 0.75rem; padding: 1rem; display: flex; align-items: center; gap: 0.75rem; transition: transform 0.15s ease, box-shadow 0.15s ease;" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 12px rgba(0,0,0,0.06)';" onmouseout="this.style.transform='none';this.style.boxShadow='none';">
            <div style="width: 42px; height: 42px; border-radius: 0.6rem; background: rgba(139, 92, 246, 0.1); color: #8B5CF6; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">
                <i class="fa-solid fa-sitemap"></i>
            </div>
            <div>
                <div style="font-weight: 700; font-size: 0.9rem;">{{ app()->getLocale() === 'ar' ? 'الهيكل التنظيمي' : 'Organization' }}</div>
                <div style="font-size: 0.75rem; color: #64748B;">{{ app()->getLocale() === 'ar' ? 'الأقسام والمسميات' : 'Depts & Positions' }}</div>
            </div>
        </a>
        --}}

        <a href="{{ route('admin.hr.settings.index') }}" style="text-decoration: none; color: inherit; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0); border-radius: 0.75rem; padding: 1rem; display: flex; align-items: center; gap: 0.75rem; transition: transform 0.15s ease, box-shadow 0.15s ease;" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 12px rgba(0,0,0,0.06)';" onmouseout="this.style.transform='none';this.style.boxShadow='none';">
            <div style="width: 42px; height: 42px; border-radius: 0.6rem; background: rgba(100, 116, 139, 0.1); color: #64748B; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">
                <i class="fa-solid fa-sliders"></i>
            </div>
            <div>
                <div style="font-weight: 700; font-size: 0.9rem;">{{ app()->getLocale() === 'ar' ? 'إعدادات النظام' : 'Settings' }}</div>
                <div style="font-size: 0.75rem; color: #64748B;">{{ app()->getLocale() === 'ar' ? 'سياسات الـ HR' : 'HR Policies' }}</div>
            </div>
        </a>
    </div>

    <!-- 15 Operational KPI Stat Cards in Clean Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
        <!-- Total Employees -->
        <div class="card stat-card" style="padding: 1rem 1.25rem; border-radius: 0.75rem; border-left: 4px solid #0284C7;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                <span style="font-size: 0.8rem; font-weight: 600; color: #64748B;">{{ __('hr.total_employees') }}</span>
                <i class="fa-solid fa-users text-sky-500 text-lg"></i>
            </div>
            <div style="font-size: 1.6rem; font-weight: 800; color: #0284C7;">{{ $totalEmployees }}</div>
            <div style="font-size: 0.75rem; color: #64748B; margin-top: 0.25rem;">
                {{ $newHires }} {{ __('hr.new_hires') }}
            </div>
        </div>

        <!-- Active Employees -->
        <div class="card stat-card" style="padding: 1rem 1.25rem; border-radius: 0.75rem; border-left: 4px solid #10B981;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                <span style="font-size: 0.8rem; font-weight: 600; color: #64748B;">{{ __('hr.active_employees') }}</span>
                <i class="fa-solid fa-user-check text-emerald-500 text-lg"></i>
            </div>
            <div style="font-size: 1.6rem; font-weight: 800; color: #059669;">{{ $activeEmployees }}</div>
            <div style="font-size: 0.75rem; color: #64748B; margin-top: 0.25rem;">
                {{ $totalEmployees > 0 ? round(($activeEmployees / $totalEmployees) * 100) : 0 }}% of workforce
            </div>
        </div>

        <!-- Probation -->
        <div class="card stat-card" style="padding: 1rem 1.25rem; border-radius: 0.75rem; border-left: 4px solid #F59E0B;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                <span style="font-size: 0.8rem; font-weight: 600; color: #64748B;">{{ __('hr.probation_employees') }}</span>
                <i class="fa-solid fa-user-clock text-amber-500 text-lg"></i>
            </div>
            <div style="font-size: 1.6rem; font-weight: 800; color: #D97706;">{{ $probationEmployees }}</div>
            <div style="font-size: 0.75rem; color: #64748B; margin-top: 0.25rem;">Under active review</div>
        </div>

        <!-- On Leave -->
        <div class="card stat-card" style="padding: 1rem 1.25rem; border-radius: 0.75rem; border-left: 4px solid #38BDF8;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                <span style="font-size: 0.8rem; font-weight: 600; color: #64748B;">{{ __('hr.on_leave_employees') }}</span>
                <i class="fa-solid fa-plane-departure text-sky-400 text-lg"></i>
            </div>
            <div style="font-size: 1.6rem; font-weight: 800; color: #0284C7;">{{ $onLeaveEmployees }}</div>
            <div style="font-size: 0.75rem; color: #64748B; margin-top: 0.25rem;">Approved leaves</div>
        </div>

        <!-- Attendance Today: Present -->
        <div class="card stat-card" style="padding: 1rem 1.25rem; border-radius: 0.75rem; border-left: 4px solid #10B981;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                <span style="font-size: 0.8rem; font-weight: 600; color: #64748B;">{{ __('hr.present_today') }}</span>
                <i class="fa-solid fa-clipboard-check text-emerald-500 text-lg"></i>
            </div>
            <div style="font-size: 1.6rem; font-weight: 800; color: #059669;">{{ $presentToday }}</div>
            <div style="font-size: 0.75rem; color: #64748B; margin-top: 0.25rem;">
                {{ $lateToday }} {{ __('hr.late_today') }}
            </div>
        </div>

        <!-- Attendance Today: Absent -->
        <div class="card stat-card" style="padding: 1rem 1.25rem; border-radius: 0.75rem; border-left: 4px solid #EF4444;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                <span style="font-size: 0.8rem; font-weight: 600; color: #64748B;">{{ __('hr.absent_today') }}</span>
                <i class="fa-solid fa-user-xmark text-red-500 text-lg"></i>
            </div>
            <div style="font-size: 1.6rem; font-weight: 800; color: #DC2626;">{{ $absentToday }}</div>
            <div style="font-size: 0.75rem; color: #64748B; margin-top: 0.25rem;">Unrecorded or excused</div>
        </div>

        <!-- Pending Leaves -->
        <div class="card stat-card" style="padding: 1rem 1.25rem; border-radius: 0.75rem; border-left: 4px solid #EC4899;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                <span style="font-size: 0.8rem; font-weight: 600; color: #64748B;">{{ __('hr.pending_leaves') }}</span>
                <i class="fa-solid fa-stamp text-pink-500 text-lg"></i>
            </div>
            <div style="font-size: 1.6rem; font-weight: 800; color: #DB2777;">{{ $pendingLeaves }}</div>
            <div style="font-size: 0.75rem; color: #64748B; margin-top: 0.25rem;">
                <a href="{{ route('admin.hr.leave.approvals') }}" style="color: #DB2777; font-weight: 600; text-decoration: none;">Review approvals &rarr;</a>
            </div>
        </div>

        <!-- Pending Employee Requests -->
        <div class="card stat-card" style="padding: 1rem 1.25rem; border-radius: 0.75rem; border-left: 4px solid #8B5CF6;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                <span style="font-size: 0.8rem; font-weight: 600; color: #64748B;">{{ __('hr.pending_requests') }}</span>
                <i class="fa-solid fa-inbox text-purple-500 text-lg"></i>
            </div>
            <div style="font-size: 1.6rem; font-weight: 800; color: #7C3AED;">{{ $pendingRequests }}</div>
            <div style="font-size: 0.75rem; color: #64748B; margin-top: 0.25rem;">
                <a href="{{ route('admin.hr.requests.index') }}" style="color: #7C3AED; font-weight: 600; text-decoration: none;">View queue &rarr;</a>
            </div>
        </div>

        <!-- Contracts Expiring Soon -->
        <div class="card stat-card" style="padding: 1rem 1.25rem; border-radius: 0.75rem; border-left: 4px solid #F97316;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                <span style="font-size: 0.8rem; font-weight: 600; color: #64748B;">{{ __('hr.expiring_contracts') }}</span>
                <i class="fa-solid fa-file-contract text-orange-500 text-lg"></i>
            </div>
            <div style="font-size: 1.6rem; font-weight: 800; color: #EA580C;">{{ $contractsExpiringSoon }}</div>
            <div style="font-size: 0.75rem; color: #64748B; margin-top: 0.25rem;">Action needed in &le;30d</div>
        </div>

        <!-- Documents Expiring Soon -->
        <div class="card stat-card" style="padding: 1rem 1.25rem; border-radius: 0.75rem; border-left: 4px solid #EAB308;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                <span style="font-size: 0.8rem; font-weight: 600; color: #64748B;">{{ __('hr.expiring_documents') }}</span>
                <i class="fa-solid fa-id-card text-yellow-500 text-lg"></i>
            </div>
            <div style="font-size: 1.6rem; font-weight: 800; color: #CA8A04;">{{ $documentsExpiringSoon }}</div>
            <div style="font-size: 0.75rem; color: #64748B; margin-top: 0.25rem;">IDs / Passports / Certs</div>
        </div>

        <!-- Payroll Pending -->
        <div class="card stat-card" style="padding: 1rem 1.25rem; border-radius: 0.75rem; border-left: 4px solid #14B8A6;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                <span style="font-size: 0.8rem; font-weight: 600; color: #64748B;">{{ __('hr.pending_payroll') }}</span>
                <i class="fa-solid fa-file-invoice-dollar text-teal-500 text-lg"></i>
            </div>
            <div style="font-size: 1.6rem; font-weight: 800; color: #0D9488;">{{ $pendingPayroll }}</div>
            <div style="font-size: 0.75rem; color: #64748B; margin-top: 0.25rem;">Draft / In process</div>
        </div>

        <!-- Upcoming Reviews -->
        <div class="card stat-card" style="padding: 1rem 1.25rem; border-radius: 0.75rem; border-left: 4px solid #6366F1;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                <span style="font-size: 0.8rem; font-weight: 600; color: #64748B;">{{ __('hr.upcoming_reviews') }}</span>
                <i class="fa-solid fa-star-half-stroke text-indigo-500 text-lg"></i>
            </div>
            <div style="font-size: 1.6rem; font-weight: 800; color: #4F46E5;">{{ $upcomingReviews }}</div>
            <div style="font-size: 0.75rem; color: #64748B; margin-top: 0.25rem;">Performance appraisals</div>
        </div>

        <!-- Resigned -->
        <div class="card stat-card" style="padding: 1rem 1.25rem; border-radius: 0.75rem; border-left: 4px solid #94A3B8;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                <span style="font-size: 0.8rem; font-weight: 600; color: #64748B;">{{ __('hr.resigned_employees') }}</span>
                <i class="fa-solid fa-door-open text-slate-400 text-lg"></i>
            </div>
            <div style="font-size: 1.6rem; font-weight: 800; color: #475569;">{{ $resignedEmployees }}</div>
            <div style="font-size: 0.75rem; color: #64748B; margin-top: 0.25rem;">Completed offboardings</div>
        </div>

        <!-- Terminated -->
        <div class="card stat-card" style="padding: 1rem 1.25rem; border-radius: 0.75rem; border-left: 4px solid #EF4444;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                <span style="font-size: 0.8rem; font-weight: 600; color: #64748B;">{{ __('hr.terminated_employees') }}</span>
                <i class="fa-solid fa-user-slash text-red-500 text-lg"></i>
            </div>
            <div style="font-size: 1.6rem; font-weight: 800; color: #DC2626;">{{ $terminatedEmployees }}</div>
            <div style="font-size: 0.75rem; color: #64748B; margin-top: 0.25rem;">Historical terminations</div>
        </div>
    </div>

    <!-- Charts & Analytics Section (8 visual insights) -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
        <!-- Chart 1: Employees by Department -->
        <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="font-size: 0.95rem; font-weight: 700; margin: 0; color: var(--text-color, #1E293B);">
                    <i class="fa-solid fa-sitemap text-sky-500 mr-2 ml-2"></i> Employees by Department
                </h3>
                <span class="badge" style="font-size: 0.75rem; background: #F1F5F9; color: #475569; padding: 0.2rem 0.5rem; border-radius: 0.25rem;">Active</span>
            </div>
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                @forelse($departmentsChart as $dept)
                    @php
                        $pct = $totalEmployees > 0 ? round(($dept['count'] / $totalEmployees) * 100) : 0;
                    @endphp
                    <div>
                        <div style="display: flex; justify-content: space-between; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem;">
                            <span>{{ $dept['name'] }}</span>
                            <span>{{ $dept['count'] }} ({{ $pct }}%)</span>
                        </div>
                        <div style="height: 6px; width: 100%; background: #E2E8F0; border-radius: 999px; overflow: hidden;">
                            <div style="width: {{ $pct }}%; height: 100%; background: #0284C7; border-radius: 999px;"></div>
                        </div>
                    </div>
                @empty
                    <p style="font-size: 0.8rem; color: #94A3B8; text-align: center; margin: 1rem 0;">No department records yet.</p>
                @endforelse
            </div>
        </div>

        <!-- Chart 2: Workforce Status Breakdown -->
        <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="font-size: 0.95rem; font-weight: 700; margin: 0; color: var(--text-color, #1E293B);">
                    <i class="fa-solid fa-chart-pie text-emerald-500 mr-2 ml-2"></i> Employment Status Distribution
                </h3>
            </div>
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                @foreach($statusBreakdown as $sb)
                    @php
                        $sPct = $totalEmployees > 0 ? round(($sb['count'] / $totalEmployees) * 100) : 0;
                    @endphp
                    <div>
                        <div style="display: flex; justify-content: space-between; font-size: 0.8rem; font-weight: 600; margin-bottom: 0.25rem;">
                            <span style="display: flex; align-items: center; gap: 0.4rem;">
                                <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: {{ $sb['color'] }};"></span>
                                {{ $sb['status'] }}
                            </span>
                            <span>{{ $sb['count'] }} ({{ $sPct }}%)</span>
                        </div>
                        <div style="height: 6px; width: 100%; background: #E2E8F0; border-radius: 999px; overflow: hidden;">
                            <div style="width: {{ $sPct }}%; height: 100%; background: {{ $sb['color'] }}; border-radius: 999px;"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Chart 3: Today's Attendance Gauge -->
        <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="font-size: 0.95rem; font-weight: 700; margin: 0; color: var(--text-color, #1E293B);">
                    <i class="fa-solid fa-clock text-amber-500 mr-2 ml-2"></i> Daily Attendance Realtime Ratio
                </h3>
            </div>
            @php
                $attPct = $activeEmployees > 0 ? round(($presentToday / $activeEmployees) * 100) : 0;
            @endphp
            <div style="text-align: center; padding: 1rem 0;">
                <div style="font-size: 2.5rem; font-weight: 900; color: {{ $attPct >= 80 ? '#10B981' : ($attPct >= 50 ? '#F59E0B' : '#EF4444') }};">
                    {{ $attPct }}%
                </div>
                <div style="font-size: 0.85rem; font-weight: 600; color: #64748B; margin-top: 0.25rem;">
                    {{ $presentToday }} of {{ $activeEmployees }} Active Employees Present Today
                </div>
                <div style="display: flex; justify-content: center; gap: 1.5rem; margin-top: 1.25rem; font-size: 0.8rem;">
                    <div><span style="color: #10B981; font-weight: 800;">{{ $presentToday - $lateToday }}</span> On Time</div>
                    <div><span style="color: #F59E0B; font-weight: 800;">{{ $lateToday }}</span> Late</div>
                    <div><span style="color: #EF4444; font-weight: 800;">{{ $absentToday }}</span> Absent</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Feeds & Operations Tables -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(380px, 1fr)); gap: 1.25rem;">
        <!-- Recent Leave Requests Queue -->
        <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="font-size: 0.95rem; font-weight: 700; margin: 0; color: var(--text-color, #1E293B);">
                    <i class="fa-solid fa-calendar-check text-rose-500 mr-2 ml-2"></i> Recent Leave Requests
                </h3>
                <a href="{{ route('admin.hr.leave.requests') }}" style="font-size: 0.75rem; color: #0284C7; text-decoration: none; font-weight: 600;">View all &rarr;</a>
            </div>
            <div class="table-responsive">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.8rem;">
                    <thead>
                        <tr style="border-bottom: 1px solid #E2E8F0; text-align: left; color: #64748B;">
                            <th style="padding: 0.5rem 0;">Employee</th>
                            <th style="padding: 0.5rem 0;">Type</th>
                            <th style="padding: 0.5rem 0;">Duration</th>
                            <th style="padding: 0.5rem 0;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentLeaves as $lr)
                            <tr style="border-bottom: 1px solid #F1F5F9;">
                                <td style="padding: 0.5rem 0; font-weight: 600;">
                                    {{ $lr->employee?->full_name ?? 'N/A' }}
                                </td>
                                <td style="padding: 0.5rem 0;">{{ $lr->leaveType?->name ?? 'Leave' }}</td>
                                <td style="padding: 0.5rem 0; color: #64748B;">{{ $lr->total_days }}d</td>
                                <td style="padding: 0.5rem 0;">
                                    @if($lr->status === 'approved')
                                        <span class="badge" style="background: #DCFCE7; color: #166534; padding: 0.2rem 0.5rem; border-radius: 999px; font-size: 0.7rem; font-weight: 600;">Approved</span>
                                    @elseif($lr->status === 'pending')
                                        <span class="badge" style="background: #FEF3C7; color: #92400E; padding: 0.2rem 0.5rem; border-radius: 999px; font-size: 0.7rem; font-weight: 600;">Pending</span>
                                    @else
                                        <span class="badge" style="background: #FEE2E2; color: #991B1B; padding: 0.2rem 0.5rem; border-radius: 999px; font-size: 0.7rem; font-weight: 600;">{{ ucfirst($lr->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 1rem; color: #94A3B8;">No recent leave requests.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Employees Added -->
        <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="font-size: 0.95rem; font-weight: 700; margin: 0; color: var(--text-color, #1E293B);">
                    <i class="fa-solid fa-users text-sky-500 mr-2 ml-2"></i> {{ app()->getLocale() === 'ar' ? 'أحدث الموظفين المسجلين' : 'Recently Added Employees' }}
                </h3>
                <a href="{{ route('admin.hr.employees.index') }}" style="font-size: 0.75rem; color: #0284C7; text-decoration: none; font-weight: 600;">View all &rarr;</a>
            </div>
            <div class="table-responsive">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.8rem;">
                    <thead>
                        <tr style="border-bottom: 1px solid #E2E8F0; text-align: left; color: #64748B;">
                            <th style="padding: 0.5rem 0;">{{ app()->getLocale() === 'ar' ? 'الموظف' : 'Employee' }}</th>
                            <th style="padding: 0.5rem 0;">{{ app()->getLocale() === 'ar' ? 'القسم' : 'Department' }}</th>
                            <th style="padding: 0.5rem 0;">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentEmployees as $emp)
                            <tr style="border-bottom: 1px solid #F1F5F9;">
                                <td style="padding: 0.5rem 0; font-weight: 600;">
                                    <a href="{{ route('admin.hr.employees.show', $emp->id) }}" style="text-decoration: none; color: #0284C7;">
                                        {{ $emp->full_name }}
                                    </a>
                                    <div style="font-size: 0.7rem; color: #94A3B8;">{{ $emp->employee_number }}</div>
                                </td>
                                <td style="padding: 0.5rem 0;">{{ $emp->department?->name ?? 'N/A' }}</td>
                                <td style="padding: 0.5rem 0;">
                                    <span class="badge" style="background: {{ $emp->employment_status === 'active' ? '#DCFCE7' : '#FEF3C7' }}; color: {{ $emp->employment_status === 'active' ? '#166534' : '#92400E' }}; padding: 0.2rem 0.5rem; border-radius: 999px; font-size: 0.7rem; font-weight: 600;">
                                        {{ ucfirst($emp->employment_status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align: center; padding: 1rem; color: #94A3B8;">No employee records yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Secondary HR Features & Tools (Accessible from backend) -->
    <div style="margin-top: 2rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0); border-radius: 0.75rem; padding: 1.25rem;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 0.75rem;">
            <div style="font-weight: 700; font-size: 0.85rem; color: #64748B;">
                <i class="fa-solid fa-layer-group mr-1 ml-1 text-slate-400"></i>
                {{ app()->getLocale() === 'ar' ? 'أدوات ووحدات إضافية (اختيارية)' : 'Additional HR Modules (Optional Utilities)' }}
            </div>
            <span style="font-size: 0.75rem; color: #94A3B8;">{{ app()->getLocale() === 'ar' ? 'متاحة للاستخدام المباشر دون إرباك القائمة الجانبية' : 'Available for direct access without cluttering the main menu' }}</span>
        </div>
        <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
            <a href="{{ route('admin.hr.recruitment.vacancies') }}" class="btn btn-ghost btn-xs" style="border: 1px solid #E2E8F0; font-size: 0.75rem;">
                <i class="fa-solid fa-bullhorn text-rose-400 mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'التوظيف والشواغر' : 'Recruitment' }}
            </a>
            <a href="{{ route('admin.hr.requests.index') }}" class="btn btn-ghost btn-xs" style="border: 1px solid #E2E8F0; font-size: 0.75rem;">
                <i class="fa-solid fa-inbox text-purple-400 mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'طلبات الموظفين' : 'Employee Requests' }}
            </a>
            <a href="{{ route('admin.hr.assets.index') }}" class="btn btn-ghost btn-xs" style="border: 1px solid #E2E8F0; font-size: 0.75rem;">
                <i class="fa-solid fa-laptop text-sky-400 mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'العهد والممتلكات' : 'Assets' }}
            </a>
            <a href="{{ route('admin.hr.performance.reviews') }}" class="btn btn-ghost btn-xs" style="border: 1px solid #E2E8F0; font-size: 0.75rem;">
                <i class="fa-solid fa-star-half-stroke text-amber-400 mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'تقييمات الأداء' : 'Performance' }}
            </a>
            <a href="{{ route('admin.hr.training.programs') }}" class="btn btn-ghost btn-xs" style="border: 1px solid #E2E8F0; font-size: 0.75rem;">
                <i class="fa-solid fa-chalkboard-user text-teal-400 mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'التدريب والتطوير' : 'Training' }}
            </a>
            <a href="{{ route('admin.hr.offboarding.resignations') }}" class="btn btn-ghost btn-xs" style="border: 1px solid #E2E8F0; font-size: 0.75rem;">
                <i class="fa-solid fa-door-open text-orange-400 mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'مغادرة ونهاية الخدمة' : 'Offboarding' }}
            </a>
            <a href="{{ route('admin.hr.reports.index') }}" class="btn btn-ghost btn-xs" style="border: 1px solid #E2E8F0; font-size: 0.75rem;">
                <i class="fa-solid fa-chart-pie text-pink-400 mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'مركز التقارير الشامل' : 'Reports Center' }}
            </a>
        </div>
    </div>
</x-layouts.admin>
