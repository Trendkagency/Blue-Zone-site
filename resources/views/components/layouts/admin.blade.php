@props([
    'title' => null,
    'pageTitle' => null,
    'pageSubtitle' => null,
    'breadcrumbs' => [],
])

<x-layouts.app :title="($title ?? $pageTitle ?? 'Admin') . ' — ' . __('admin.portal_title')">
    <div class="admin-layout">
        <!-- Mobile Sidebar Backdrop -->
        <div id="adminSidebarBackdrop" class="admin-sidebar-backdrop" onclick="toggleAdminSidebar()"></div>

        <!-- Admin Sidebar -->
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="sidebar-header">
                <a href="{{ route('admin.dashboard') }}"
                    style="display: flex; align-items: center; gap: 0.75rem; text-decoration: none;">
                    <img src="{{ asset('assets/logo/logo-dark.webp') }}" alt="{{ __('app.brand_name') }}"
                        style="height: 28px;"
                        onerror="this.onerror=null; this.src='{{ asset('assets/logo/logo-dark.png') }}';">
                    <span class="sidebar-brand-title">BZ-OS</span>
                </a>
                <button type="button" class="btn btn-ghost btn-sm lg:hidden cursor-pointer"
                    onclick="toggleAdminSidebar()" aria-label="Close sidebar"
                    style="color: #94A3B8; padding: 0.25rem 0.5rem;">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <nav class="sidebar-menu" id="adminSidebarMenu">
                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') }}"
                    class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                    title="{{ __('admin.menu.dashboard') }}">
                    <i class="fa-solid fa-chart-line sidebar-link-icon text-sky-400"></i>
                    <span>{{ __('admin.menu.dashboard') }}</span>
                </a>

                @php
                    $u = auth()->user();
                    $isCatalogActive = request()->routeIs('admin.products.*') || request()->routeIs('admin.categories.*');
                    $isInventoryActive = request()->routeIs('admin.inventory.*') || request()->routeIs('admin.locations.*') || request()->routeIs('admin.warehouses.*');
                    $isSalesActive = request()->routeIs('admin.orders.*') || request()->routeIs('admin.offline-sales.*') || request()->routeIs('admin.invoices.*');
                    $isCustomersActive = request()->routeIs('admin.customers.*') || request()->routeIs('admin.reports.*');
                    $isMrActive = request()->routeIs('admin.mr.*') || request()->is('admin/mr*');
                    $isCrmActive = request()->routeIs('admin.crm.*');
                    $isHrActive = request()->routeIs('admin.hr.*') || request()->is('admin/hr*');
                    $isContentAccessActive = request()->routeIs('admin.content.*') || request()->routeIs('admin.users.*') || request()->routeIs('admin.roles.*');
                    $isSettingsActive = request()->routeIs('admin.settings.*') || request()->routeIs('admin.countries.*') || request()->routeIs('admin.profile.*');
                @endphp

                @php
                    $canManageMr = $u ? $u->canManageAllMr() : false;
                @endphp
                <!-- Medical Representative (MR) CRM Dropdown -->
                @if($u && ($u->hasPermission('mr.view') || $u->hasPermission('mr') || $u->hasPermission('mr_visits') || $u->hasPermission('mr_dashboard') || $u->isMedicalRep() || $u->canManageAllMr()))
                    <div class="sidebar-dropdown-group {{ $isMrActive ? 'is-open' : '' }}" id="group-mr-crm">
                        <button type="button" class="sidebar-dropdown-btn {{ $isMrActive ? 'active-parent' : '' }}" onclick="toggleSidebarDropdown('group-mr-crm')">
                            <div class="sidebar-dropdown-label">
                                <i class="fa-solid fa-user-doctor sidebar-link-icon text-cyan-400"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'نظام CRM' : 'CRM' }}</span>
                            </div>
                            <div class="sidebar-dropdown-meta">
                                <i class="fa-solid fa-chevron-down sidebar-dropdown-chevron"></i>
                            </div>
                        </button>
                        <div class="sidebar-submenu">
                            <a href="{{ route('admin.mr.dashboard') }}"
                                class="sidebar-sublink {{ request()->routeIs('admin.mr.dashboard') || request()->routeIs('admin.mr.dashboard.alt') ? 'active' : '' }}">
                                <i class="fa-solid fa-gauge-high text-xs text-cyan-400"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'لوحة تحكم CRM' : 'CRM dashboard' }}</span>
                            </a>
                            <a href="{{ route('admin.mr.live-map') }}"
                                class="sidebar-sublink {{ request()->routeIs('admin.mr.live-map') ? 'active' : '' }}">
                                <i class="fa-solid fa-earth-americas text-xs text-emerald-400"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'خريطة العمليات المباشرة (Live Map)' : 'Live Ops Field Map' }}</span>
                            </a>
                            @if($canManageMr)
                            <a href="{{ route('admin.mr.areas.index') }}"
                                class="sidebar-sublink {{ request()->routeIs('admin.mr.areas.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-map-location-dot text-xs text-teal-400"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'المناطق والمربعات (Territories & Areas)' : 'Territories & Areas' }}</span>
                            </a>
                            @endif
                            <a href="{{ route('admin.mr.contacts.index') }}"
                                class="sidebar-sublink {{ request()->routeIs('admin.mr.contacts.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-stethoscope text-xs text-sky-400"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'الأطباء والعيادات (Contacts)' : 'Doctors & Clinics (Contacts)' }}</span>
                            </a>
                            @if($canManageMr)
                            <a href="{{ route('admin.mr.classifications.index') }}"
                                class="sidebar-sublink {{ request()->routeIs('admin.mr.classifications.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-ranking-star text-xs text-amber-400"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'تصنيفات الأطباء' : 'Doctor Classes' }}</span>
                            </a>
                            <a href="{{ route('admin.mr.specialties.index') }}"
                                class="sidebar-sublink {{ request()->routeIs('admin.mr.specialties.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-heart-pulse text-xs text-rose-400"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'التخصصات الطبية' : 'Medical Specialties' }}</span>
                            </a>
                            <a href="{{ route('admin.mr.assignments.index') }}"
                                class="sidebar-sublink {{ request()->routeIs('admin.mr.assignments.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-clipboard-check text-xs text-indigo-400"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'توزيع وتعيين الأطباء' : 'Doctor Assignments' }}</span>
                            </a>
                            <a href="{{ route('admin.mr.cycles.index') }}"
                                class="sidebar-sublink {{ request()->routeIs('admin.mr.cycles.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-calendar-days text-xs text-purple-400"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'دورات الزيارات (Cycles)' : 'Visit Cycles' }}</span>
                            </a>
                            @endif
                            <a href="{{ route('admin.mr.visits.index') }}"
                                class="sidebar-sublink {{ request()->routeIs('admin.mr.visits.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-location-dot text-xs text-cyan-400"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'سجل الزيارات الميدانية (GPS)' : 'Executed Visits & GPS Log' }}</span>
                            </a>
                            <a href="{{ route('admin.mr.reports.coverage') }}"
                                class="sidebar-sublink {{ request()->routeIs('admin.mr.reports.coverage') ? 'active' : '' }}">
                                <i class="fa-solid fa-chart-column text-xs text-teal-400"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'تقرير التغطية والأطباء غير المزارين' : 'Doctor Coverage Report' }}</span>
                            </a>
                            <a href="{{ route('admin.mr.reports.performance') }}"
                                class="sidebar-sublink {{ request()->routeIs('admin.mr.reports.performance') ? 'active' : '' }}">
                                <i class="fa-solid fa-trophy text-xs text-yellow-400"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'بطاقة أداء المناديب (Scorecard)' : 'Rep Performance Scorecard' }}</span>
                            </a>
                            @if($canManageMr)
                            <a href="{{ route('admin.mr.gps-config.index') }}"
                                class="sidebar-sublink {{ request()->routeIs('admin.mr.gps-config.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-satellite-dish text-xs text-purple-400"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'إعدادات وقواعد الـ GPS' : 'GPS Geofence & Rules' }}</span>
                            </a>
                            @endif
                            <a href="{{ url('/mr') }}" target="_blank"
                                class="sidebar-sublink text-cyan-300 font-bold bg-cyan-950/20 rounded-lg">
                                <i class="fa-solid fa-mobile-screen-button text-xs text-cyan-400"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'بوابة المندوب الميدانية (MR Portal) ↗' : 'MR Mobile Portal ↗' }}</span>
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Commercial Sales CRM Dropdown -->
                @if($u && ($u->hasPermission('crm.view') || $u->hasPermission('crm') || $u->hasPermission('crm_leads') || $u->hasPermission('crm_opportunities') || $u->hasPermission('crm_activities')))
                    <div class="sidebar-dropdown-group {{ $isCrmActive ? 'is-open' : '' }}" id="group-sales-crm">
                        <button type="button" class="sidebar-dropdown-btn {{ $isCrmActive ? 'active-parent' : '' }}" onclick="toggleSidebarDropdown('group-sales-crm')">
                            <div class="sidebar-dropdown-label">
                                <i class="fa-solid fa-briefcase sidebar-link-icon text-amber-400"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'إدارة المبيعات والعملاء (Sales CRM)' : 'Commercial Sales CRM' }}</span>
                            </div>
                            <div class="sidebar-dropdown-meta">
                                <i class="fa-solid fa-chevron-down sidebar-dropdown-chevron"></i>
                            </div>
                        </button>
                        <div class="sidebar-submenu">
                            <a href="{{ route('admin.crm.dashboard') }}" class="sidebar-sublink {{ request()->routeIs('admin.crm.dashboard') ? 'active' : '' }}">
                                <i class="fa-solid fa-chart-pie text-xs text-amber-400"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'لوحة تحكم الـ CRM' : 'CRM Dashboard' }}</span>
                            </a>
                            <a href="{{ route('admin.crm.leads.index') }}" class="sidebar-sublink {{ request()->routeIs('admin.crm.leads.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-user-tag text-xs text-sky-400"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'العملاء المحتملين (Leads)' : 'Leads Pipeline' }}</span>
                            </a>
                            <a href="{{ route('admin.crm.opportunities.index') }}" class="sidebar-sublink {{ request()->routeIs('admin.crm.opportunities.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-handshake text-xs text-emerald-400"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'الصفقات والفرص (Deals)' : 'Deals & Kanban' }}</span>
                            </a>
                            <a href="{{ route('admin.crm.activities.index') }}" class="sidebar-sublink {{ request()->routeIs('admin.crm.activities.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-list-check text-xs text-purple-400"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'المهام والأنشطة' : 'Activities & Tasks' }}</span>
                            </a>
                            <a href="{{ route('admin.crm.companies.index') }}" class="sidebar-sublink {{ request()->routeIs('admin.crm.companies.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-building text-xs text-indigo-400"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'حسابات الشركات (B2B)' : 'Corporate Accounts' }}</span>
                            </a>
                            <a href="{{ route('admin.crm.campaigns.index') }}" class="sidebar-sublink {{ request()->routeIs('admin.crm.campaigns.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-bullhorn text-xs text-rose-400"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'الحملات التسويقية' : 'Marketing Campaigns' }}</span>
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Human Resources (HR) Dropdown -->
                @if($u && ($u->hasPermission('hr.view') || $u->hasPermission('hr') || $u->hasPermission('hr_employees') || $u->hasPermission('employees.view') || $u->hasPermission('departments.view') || $u->hasPermission('payroll.view')))
                    <div class="sidebar-dropdown-group {{ $isHrActive ? 'is-open' : '' }}" id="group-hr">
                    <button type="button" class="sidebar-dropdown-btn {{ $isHrActive ? 'active-parent' : '' }}" onclick="toggleSidebarDropdown('group-hr')">
                        <div class="sidebar-dropdown-label">
                            <i class="fa-solid fa-people-roof sidebar-link-icon text-pink-400"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'الموارد البشرية (HR)' : 'Human Resources (HR)' }}</span>
                        </div>
                        <div class="sidebar-dropdown-meta">
                            <i class="fa-solid fa-chevron-down sidebar-dropdown-chevron"></i>
                        </div>
                    </button>
                    <div class="sidebar-submenu">
                        <!-- HR Dashboard -->
                        <a href="{{ route('admin.hr.dashboard') }}" class="sidebar-sublink {{ request()->routeIs('admin.hr.dashboard') ? 'active' : '' }}">
                            <i class="fa-solid fa-gauge-high text-xs text-pink-400"></i>
                            <span class="font-bold">{{ app()->getLocale() === 'ar' ? 'لوحة تحكم الموارد البشرية' : 'HR Dashboard' }}</span>
                        </a>

                        {{-- 1. Organization (Temporarily hidden)
                        <div style="padding: 0.5rem 0.75rem 0.2rem; font-size: 0.65rem; font-weight: 700; text-transform: uppercase; color: #64748B; letter-spacing: 0.05em;">
                            {{ app()->getLocale() === 'ar' ? 'الهيكل التنظيمي' : 'Organization' }}
                        </div>
                        <a href="{{ route('admin.hr.organization.departments.index') }}" class="sidebar-sublink {{ request()->routeIs('admin.hr.organization.departments.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-sitemap text-xs text-teal-400"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'الأقسام (Departments)' : 'Departments' }}</span>
                        </a>
                        <a href="{{ route('admin.hr.organization.positions.index') }}" class="sidebar-sublink {{ request()->routeIs('admin.hr.organization.positions.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-id-card-clip text-xs text-sky-400"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'الوظائف والمهن (Positions)' : 'Positions' }}</span>
                        </a>
                        <a href="{{ route('admin.hr.organization.work-schedules.index') }}" class="sidebar-sublink {{ request()->routeIs('admin.hr.organization.work-schedules.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-business-time text-xs text-amber-400"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'ورديات ومواعيد العمل' : 'Work Schedules' }}</span>
                        </a>
                        <a href="{{ Route::has('admin.locations.index') ? route('admin.locations.index') : url('/admin/locations') }}" class="sidebar-sublink {{ request()->routeIs('admin.locations.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-building-circle-check text-xs text-indigo-400"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'الفروع والمواقع (Branches)' : 'Branches / Hubs' }}</span>
                        </a>
                        --}}

                        <!-- 2. Employees -->
                        <div style="padding: 0.5rem 0.75rem 0.2rem; font-size: 0.65rem; font-weight: 700; text-transform: uppercase; color: #64748B; letter-spacing: 0.05em;">
                            {{ app()->getLocale() === 'ar' ? 'إدارة الموظفين' : 'Employees' }}
                        </div>
                        <a href="{{ route('admin.hr.employees.index') }}" class="sidebar-sublink {{ request()->routeIs('admin.hr.employees.index') ? 'active' : '' }}">
                            <i class="fa-solid fa-users text-xs text-sky-400"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'دليل الموظفين (All Employees)' : 'All Employees' }}</span>
                        </a>
                        <a href="{{ route('admin.hr.employees.create') }}" class="sidebar-sublink {{ request()->routeIs('admin.hr.employees.create') ? 'active' : '' }}">
                            <i class="fa-solid fa-user-plus text-xs text-emerald-400"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'إضافة موظف جديد' : 'New Employee Profile' }}</span>
                        </a>
                        <a href="{{ route('admin.hr.employees.documents.index') }}" class="sidebar-sublink {{ request()->routeIs('admin.hr.employees.documents.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-folder-open text-xs text-violet-400"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'أرشيف ومستندات الموظفين' : 'Employee Documents' }}</span>
                        </a>

                        <!-- 3. Attendance -->
                        <div style="padding: 0.5rem 0.75rem 0.2rem; font-size: 0.65rem; font-weight: 700; text-transform: uppercase; color: #64748B; letter-spacing: 0.05em;">
                            {{ app()->getLocale() === 'ar' ? 'الحضور والانصراف' : 'Attendance' }}
                        </div>
                        <a href="{{ route('admin.hr.attendance.daily') }}" class="sidebar-sublink {{ request()->routeIs('admin.hr.attendance.daily') ? 'active' : '' }}">
                            <i class="fa-solid fa-clipboard-user text-xs text-emerald-400"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'سجل الحضور اليومي' : 'Daily Attendance Sheet' }}</span>
                        </a>
                        <a href="{{ route('admin.hr.attendance.index') }}" class="sidebar-sublink {{ request()->routeIs('admin.hr.attendance.index') ? 'active' : '' }}">
                            <i class="fa-solid fa-list-check text-xs text-sky-400"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'سجلات الحضور الكاملة' : 'Attendance Records' }}</span>
                        </a>
                        <a href="{{ route('admin.hr.attendance.overtime') }}" class="sidebar-sublink {{ request()->routeIs('admin.hr.attendance.overtime*') ? 'active' : '' }}">
                            <i class="fa-solid fa-clock-rotate-left text-xs text-purple-400"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'العمل الإضافي (Overtime)' : 'Overtime Requests' }}</span>
                        </a>

                        <!-- 4. Leave Management -->
                        <div style="padding: 0.5rem 0.75rem 0.2rem; font-size: 0.65rem; font-weight: 700; text-transform: uppercase; color: #64748B; letter-spacing: 0.05em;">
                            {{ app()->getLocale() === 'ar' ? 'إدارة الإجازات' : 'Leave Management' }}
                        </div>
                        <a href="{{ route('admin.hr.leave.approvals') }}" class="sidebar-sublink {{ request()->routeIs('admin.hr.leave.approvals') ? 'active' : '' }}">
                            <i class="fa-solid fa-stamp text-xs text-rose-400"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'طلبات بانتظار الاعتماد' : 'Leave Approvals Queue' }}</span>
                        </a>
                        <a href="{{ route('admin.hr.leave.requests') }}" class="sidebar-sublink {{ request()->routeIs('admin.hr.leave.requests') ? 'active' : '' }}">
                            <i class="fa-solid fa-calendar-check text-xs text-sky-400"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'كافة طلبات الإجازات' : 'Leave Requests' }}</span>
                        </a>
                        <a href="{{ route('admin.hr.leave.balances') }}" class="sidebar-sublink {{ request()->routeIs('admin.hr.leave.balances') ? 'active' : '' }}">
                            <i class="fa-solid fa-scale-balanced text-xs text-teal-400"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'أرصدة إجازات الموظفين' : 'Leave Balances' }}</span>
                        </a>
                        <a href="{{ route('admin.hr.leave.types') }}" class="sidebar-sublink {{ request()->routeIs('admin.hr.leave.types') ? 'active' : '' }}">
                            <i class="fa-solid fa-tags text-xs text-amber-400"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'أنواع الإجازات والسياسات' : 'Leave Types & Policies' }}</span>
                        </a>

                        <!-- 5. Payroll -->
                        <div style="padding: 0.5rem 0.75rem 0.2rem; font-size: 0.65rem; font-weight: 700; text-transform: uppercase; color: #64748B; letter-spacing: 0.05em;">
                            {{ app()->getLocale() === 'ar' ? 'الرواتب والمسيرات' : 'Payroll & Compensation' }}
                        </div>
                        <a href="{{ route('admin.hr.payroll.periods') }}" class="sidebar-sublink {{ request()->routeIs('admin.hr.payroll.periods*') ? 'active' : '' }}">
                            <i class="fa-solid fa-calendar-days text-xs text-emerald-400"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'فترات ومسيرات الرواتب' : 'Payroll Periods' }}</span>
                        </a>
                        <a href="{{ route('admin.hr.payroll.payslips') }}" class="sidebar-sublink {{ request()->routeIs('admin.hr.payroll.payslips*') ? 'active' : '' }}">
                            <i class="fa-solid fa-receipt text-xs text-sky-400"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'قسائم الرواتب (Payslips)' : 'Employee Payslips' }}</span>
                        </a>
                        <a href="{{ route('admin.hr.payroll.structures') }}" class="sidebar-sublink {{ request()->routeIs('admin.hr.payroll.structures*') ? 'active' : '' }}">
                            <i class="fa-solid fa-cubes-stacked text-xs text-purple-400"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'هياكل ومكونات الأجور' : 'Salary Structures' }}</span>
                        </a>
                        <a href="{{ route('admin.hr.payroll.advances') }}" class="sidebar-sublink {{ request()->routeIs('admin.hr.payroll.advances*') || request()->routeIs('admin.hr.payroll.loans*') ? 'active' : '' }}">
                            <i class="fa-solid fa-hand-holding-dollar text-xs text-amber-400"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'السلف والقروض المالية' : 'Salary Advances & Loans' }}</span>
                        </a>

                        <!-- 6. Settings -->
                        <div style="padding: 0.5rem 0.75rem 0.2rem; font-size: 0.65rem; font-weight: 700; text-transform: uppercase; color: #64748B; letter-spacing: 0.05em;">
                            {{ app()->getLocale() === 'ar' ? 'الإعدادات' : 'Settings' }}
                        </div>
                        <a href="{{ route('admin.hr.settings.index') }}" class="sidebar-sublink {{ request()->routeIs('admin.hr.settings.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-sliders text-xs text-violet-400"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'إعدادات وسياسات الـ HR' : 'HR System Settings' }}</span>
                        </a>
                    </div>
                </div>
                @endif

                <!-- 1. Catalog & Formulations Dropdown -->
                @if($u && ($u->hasPermission('products.view') || $u->hasPermission('products') || $u->hasPermission('products.create') || $u->hasPermission('products.edit')))
                    <div class="sidebar-dropdown-group {{ $isCatalogActive ? 'is-open' : '' }}" id="group-catalog">
                        <button type="button" class="sidebar-dropdown-btn {{ $isCatalogActive ? 'active-parent' : '' }}" onclick="toggleSidebarDropdown('group-catalog')">
                            <div class="sidebar-dropdown-label">
                                <i class="fa-solid fa-boxes-stacked sidebar-link-icon text-emerald-400"></i>
                                <span>{{ __('admin.menu.catalog') }}</span>
                            </div>
                            <div class="sidebar-dropdown-meta">
                                <i class="fa-solid fa-chevron-down sidebar-dropdown-chevron"></i>
                            </div>
                        </button>
                        <div class="sidebar-submenu">
                            <a href="{{ route('admin.products.index') }}"
                                class="sidebar-sublink {{ request()->routeIs('admin.products.index') || request()->routeIs('admin.products.show') ? 'active' : '' }}">
                                <i class="fa-solid fa-pills text-xs"></i>
                                <span>{{ __('admin.menu.products') }}</span>
                            </a>
                            @if($u->hasPermission('products.create') || $u->hasPermission('products'))
                                <a href="{{ route('admin.products.create') }}"
                                    class="sidebar-sublink {{ request()->routeIs('admin.products.create') ? 'active' : '' }}">
                                    <i class="fa-solid fa-plus text-xs"></i>
                                    <span>{{ __('admin.menu.add_product') }}</span>
                                </a>
                            @endif
                            <a href="{{ route('admin.categories.index') }}"
                                class="sidebar-sublink {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-layer-group text-xs"></i>
                                <span>{{ __('admin.menu.categories') }}</span>
                            </a>
                        </div>
                    </div>
                @endif

                <!-- 2. Inventory & Multi-Hubs Dropdown -->
                @if($u && ($u->hasPermission('inventory.view') || $u->hasPermission('inventory') || $u->hasPermission('inventory.create')))
                    <div class="sidebar-dropdown-group {{ $isInventoryActive ? 'is-open' : '' }}" id="group-inventory">
                        <button type="button" class="sidebar-dropdown-btn {{ $isInventoryActive ? 'active-parent' : '' }}" onclick="toggleSidebarDropdown('group-inventory')">
                            <div class="sidebar-dropdown-label">
                                <i class="fa-solid fa-warehouse sidebar-link-icon text-indigo-400"></i>
                                <span>{{ __('admin.menu.inventory') }}</span>
                            </div>
                            <div class="sidebar-dropdown-meta">
                                <i class="fa-solid fa-chevron-down sidebar-dropdown-chevron"></i>
                            </div>
                        </button>
                        <div class="sidebar-submenu">
                            <a href="{{ Route::has('admin.inventory.control') ? route('admin.inventory.control') : url('/admin/inventory/control') }}"
                                class="sidebar-sublink {{ request()->routeIs('admin.inventory.control') ? 'active' : '' }}"
                                title="{{ app()->getLocale() === 'ar' ? 'مركز التحكم السريع وإدارة كميات المنتجات' : 'Quick Stock Control Hub' }}">
                                <i class="fa-solid fa-sliders text-xs text-emerald-400"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'مركز التحكم بالمخزون' : 'Inventory Control Hub' }}</span>
                            </a>
                            <a href="{{ Route::has('admin.inventory.index') ? route('admin.inventory.index') : url('/admin/inventory') }}"
                                class="sidebar-sublink {{ request()->routeIs('admin.inventory.index') ? 'active' : '' }}">
                                <i class="fa-solid fa-boxes-packing text-xs"></i>
                                <span>{{ __('admin.menu.stock_levels') }}</span>
                            </a>
                            <a href="{{ Route::has('admin.locations.index') ? route('admin.locations.index') : (Route::has('admin.warehouses.index') ? route('admin.warehouses.index') : url('/admin/locations')) }}"
                                class="sidebar-sublink {{ request()->routeIs('admin.locations.*') || request()->routeIs('admin.warehouses.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-building-columns text-xs text-indigo-400"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'المستودعات والمواقع' : 'Locations & Hubs' }}</span>
                            </a>
                            <a href="{{ Route::has('admin.inventory.allocator') ? route('admin.inventory.allocator') : url('/admin/inventory/allocator') }}"
                                class="sidebar-sublink {{ request()->routeIs('admin.inventory.allocator') ? 'active' : '' }}">
                                <i class="fa-solid fa-network-wired text-xs text-sky-400"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'موزع المخزون (Live)' : 'Stock Allocator (Live)' }}</span>
                            </a>
                            <a href="{{ Route::has('admin.inventory.transfers') ? route('admin.inventory.transfers') : url('/admin/inventory/transfers') }}"
                                class="sidebar-sublink {{ request()->routeIs('admin.inventory.transfers') ? 'active' : '' }}">
                                <i class="fa-solid fa-arrow-right-arrow-left text-xs"></i>
                                <span>{{ __('admin.menu.stock_transfers') }}</span>
                            </a>
                            <a href="{{ Route::has('admin.inventory.history') ? route('admin.inventory.history') : url('/admin/inventory/history') }}"
                                class="sidebar-sublink {{ request()->routeIs('admin.inventory.history') ? 'active' : '' }}">
                                <i class="fa-solid fa-clock-rotate-left text-xs"></i>
                                <span>{{ __('admin.menu.stock_history') }}</span>
                            </a>
                        </div>
                    </div>
                @endif

                <!-- 3. Sales, Orders & POS Dropdown -->
                @if($u && ($u->hasPermission('orders.view') || $u->hasPermission('offline_sales.view') || $u->hasPermission('invoices.view') || $u->hasPermission('orders') || $u->hasPermission('offline_sales') || $u->hasPermission('invoices')))
                    <div class="sidebar-dropdown-group {{ $isSalesActive ? 'is-open' : '' }}" id="group-sales">
                        <button type="button" class="sidebar-dropdown-btn {{ $isSalesActive ? 'active-parent' : '' }}" onclick="toggleSidebarDropdown('group-sales')">
                            <div class="sidebar-dropdown-label">
                                <i class="fa-solid fa-cash-register sidebar-link-icon text-amber-400"></i>
                                <span>{{ __('admin.menu.sales') }}</span>
                            </div>
                            <div class="sidebar-dropdown-meta">
                                <i class="fa-solid fa-chevron-down sidebar-dropdown-chevron"></i>
                            </div>
                        </button>
                        <div class="sidebar-submenu">
                            @if($u->hasPermission('orders.view') || $u->hasPermission('orders'))
                                <a href="{{ route('admin.orders.index') }}"
                                    class="sidebar-sublink {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                                    <i class="fa-solid fa-bag-shopping text-xs"></i>
                                    <span>{{ __('admin.menu.online_orders') }}</span>
                                </a>
                            @endif
                            @if($u->hasPermission('offline_sales.view') || $u->hasPermission('offline_sales'))
                                <a href="{{ route('admin.offline-sales.index') }}"
                                    class="sidebar-sublink {{ request()->routeIs('admin.offline-sales.index') || request()->routeIs('admin.offline-sales.show') ? 'active' : '' }}">
                                    <i class="fa-solid fa-store text-xs"></i>
                                    <span>{{ __('admin.menu.offline_sales') }}</span>
                                </a>
                                <a href="{{ route('admin.offline-sales.create') }}"
                                    class="sidebar-sublink {{ request()->routeIs('admin.offline-sales.create') ? 'active' : '' }}">
                                    <i class="fa-solid fa-calculator text-xs"></i>
                                    <span>{{ app()->getLocale() === 'ar' ? 'نقطة بيع جديدة (POS)' : 'New POS Sale' }}</span>
                                </a>
                            @endif
                            @if($u->hasPermission('invoices.view') || $u->hasPermission('invoices'))
                                <a href="{{ route('admin.invoices.index') }}"
                                    class="sidebar-sublink {{ request()->routeIs('admin.invoices.*') ? 'active' : '' }}">
                                    <i class="fa-solid fa-file-invoice-dollar text-xs"></i>
                                    <span>{{ __('admin.menu.invoices') }}</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- 4. Customers & Reports Dropdown -->
                @if($u && ($u->hasPermission('customers.view') || $u->hasPermission('reports.view') || $u->hasPermission('customers') || $u->hasPermission('reports')))
                    <div class="sidebar-dropdown-group {{ $isCustomersActive ? 'is-open' : '' }}" id="group-customers">
                        <button type="button" class="sidebar-dropdown-btn {{ $isCustomersActive ? 'active-parent' : '' }}" onclick="toggleSidebarDropdown('group-customers')">
                            <div class="sidebar-dropdown-label">
                                <i class="fa-solid fa-users-gear sidebar-link-icon text-cyan-400"></i>
                                <span>{{ __('admin.menu.customers') }} & {{ __('admin.menu.reports') }}</span>
                            </div>
                            <div class="sidebar-dropdown-meta">
                                <i class="fa-solid fa-chevron-down sidebar-dropdown-chevron"></i>
                            </div>
                        </button>
                        <div class="sidebar-submenu">
                            @if($u->hasPermission('customers.view') || $u->hasPermission('customers'))
                                <a href="{{ route('admin.customers.index') }}"
                                    class="sidebar-sublink {{ request()->routeIs('admin.customers.index') || request()->routeIs('admin.customers.show') ? 'active' : '' }}">
                                    <i class="fa-solid fa-users text-xs"></i>
                                    <span>{{ __('admin.menu.customers') }}</span>
                                </a>
                                @if($u->hasPermission('customers.create') || $u->hasPermission('customers'))
                                    <a href="{{ route('admin.customers.create') }}"
                                        class="sidebar-sublink {{ request()->routeIs('admin.customers.create') ? 'active' : '' }}">
                                        <i class="fa-solid fa-user-plus text-xs"></i>
                                        <span>{{ app()->getLocale() === 'ar' ? 'إضافة عميل' : 'Add Customer' }}</span>
                                    </a>
                                @endif
                            @endif
                            @if($u->hasPermission('reports.view') || $u->hasPermission('reports'))
                                <a href="{{ route('admin.reports.index') }}"
                                    class="sidebar-sublink {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                                    <i class="fa-solid fa-chart-pie text-xs"></i>
                                    <span>{{ __('admin.menu.reports') }}</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- 5. Content & System Access Dropdown -->
                @if($u && ($u->hasPermission('content.view') || $u->hasPermission('users.view') || $u->hasPermission('roles.view') || $u->hasPermission('content') || $u->hasPermission('users') || $u->hasPermission('roles')))
                    <div class="sidebar-dropdown-group {{ $isContentAccessActive ? 'is-open' : '' }}" id="group-access">
                        <button type="button" class="sidebar-dropdown-btn {{ $isContentAccessActive ? 'active-parent' : '' }}" onclick="toggleSidebarDropdown('group-access')">
                            <div class="sidebar-dropdown-label">
                                <i class="fa-solid fa-user-shield sidebar-link-icon text-rose-400"></i>
                                <span>{{ __('admin.menu.content') }} & {{ __('admin.menu.access_control') }}</span>
                            </div>
                            <div class="sidebar-dropdown-meta">
                                <i class="fa-solid fa-chevron-down sidebar-dropdown-chevron"></i>
                            </div>
                        </button>
                        <div class="sidebar-submenu">
                            @if($u->hasPermission('content.view') || $u->hasPermission('content'))
                                <a href="{{ Route::has('admin.content.index') ? route('admin.content.index') : url('/admin/content') }}"
                                    class="sidebar-sublink {{ request()->routeIs('admin.content.*') ? 'active' : '' }}">
                                    <i class="fa-solid fa-newspaper text-xs"></i>
                                    <span>{{ __('admin.menu.content') }}</span>
                                </a>
                            @endif
                            @if($u->hasPermission('users.view') || $u->hasPermission('users'))
                                <a href="{{ route('admin.users.index') }}"
                                    class="sidebar-sublink {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                                    <i class="fa-solid fa-user text-xs"></i>
                                    <span>{{ __('admin.menu.users') }}</span>
                                </a>
                            @endif
                            @if($u->hasPermission('roles.view') || $u->hasPermission('roles'))
                                <a href="{{ route('admin.roles.index') }}"
                                    class="sidebar-sublink {{ request()->routeIs('admin.roles.index') || request()->routeIs('admin.roles.create') || request()->routeIs('admin.roles.edit') ? 'active' : '' }}">
                                    <i class="fa-solid fa-id-badge text-xs"></i>
                                    <span>{{ __('admin.menu.roles') }}</span>
                                </a>
                                <a href="{{ route('admin.roles.matrix') }}"
                                    class="sidebar-sublink {{ request()->routeIs('admin.roles.matrix') ? 'active' : '' }}">
                                    <i class="fa-solid fa-table-cells text-xs text-sky-400"></i>
                                    <span>{{ app()->getLocale() === 'ar' ? 'مصفوفة الصلاحيات' : 'Permission Matrix' }}</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- 6. Settings & System Control Dropdown -->
                @if($u && ($u->hasPermission('settings.view') || $u->hasPermission('settings') || $u->isSuperAdmin() || $u->isAdmin()))
                    <div class="sidebar-dropdown-group {{ $isSettingsActive ? 'is-open' : '' }}" id="group-settings">
                        <button type="button" class="sidebar-dropdown-btn {{ $isSettingsActive ? 'active-parent' : '' }}" onclick="toggleSidebarDropdown('group-settings')">
                            <div class="sidebar-dropdown-label">
                                <i class="fa-solid fa-sliders sidebar-link-icon text-violet-400"></i>
                                <span>{{ __('admin.menu.settings') }}</span>
                            </div>
                            <div class="sidebar-dropdown-meta">
                                <i class="fa-solid fa-chevron-down sidebar-dropdown-chevron"></i>
                            </div>
                        </button>
                        <div class="sidebar-submenu">
                            <a href="{{ route('admin.settings.index') }}"
                                class="sidebar-sublink {{ request()->routeIs('admin.settings.index') && !str_contains(request()->fullUrl(), '#tab-typography') ? 'active' : '' }}">
                                <i class="fa-solid fa-gear text-xs"></i>
                                <span>{{ __('admin.menu.settings') }}</span>
                            </a>
                            <a href="{{ Route::has('admin.settings.geo.index') ? route('admin.settings.geo.index') : url('/admin/settings/geo') }}"
                                class="sidebar-sublink {{ request()->routeIs('admin.settings.geo.*') || request()->routeIs('admin.countries.*') ? 'active' : '' }}"
                                title="{{ app()->getLocale() == 'ar' ? 'إدارة النطاقات الجغرافية والدول والمدن' : 'Countries, Cities & Geographic Hub' }}">
                                <i class="fa-solid fa-earth-americas text-xs text-emerald-400"></i>
                                <span>{{ app()->getLocale() == 'ar' ? 'الدول والمدن (Geo)' : 'Countries & Cities' }}</span>
                            </a>
                            <a href="{{ route('admin.settings.index') }}#tab-typography" class="sidebar-sublink"
                                title="{{ app()->getLocale() == 'ar' ? 'المعاينة الحية والتحكم في خطوط النظام' : 'Live Interactive Typography Control' }}">
                                <i class="fa-solid fa-font text-xs text-sky-400"></i>
                                <span>{{ app()->getLocale() == 'ar' ? 'الخطوط والطباعة (Live)' : 'Typography & Fonts (Live)' }}</span>
                            </a>
                            <a href="{{ route('admin.profile.index') }}"
                                class="sidebar-sublink {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}">
                                <i class="fa-solid fa-user-gear text-xs"></i>
                                <span>{{ __('admin.profile.title') }}</span>
                            </a>
                        </div>
                    </div>
                @else
                    <a href="{{ route('admin.profile.index') }}"
                        class="sidebar-link {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}"
                        title="{{ __('admin.profile.title') }}">
                        <i class="fa-solid fa-user-gear sidebar-link-icon text-violet-400"></i>
                        <span>{{ __('admin.profile.title') }}</span>
                    </a>
                @endif
            </nav>
        </aside>

        <!-- Admin Content Shell (Section Main) -->
        <main class="admin-main" id="adminMain" role="main">
            <!-- Impersonation Active Banner (Blue Zone Brand Theme Adaptive) -->
            @if(session()->has('impersonated_by'))
                <div class="bg-[#062B49] dark:bg-[#031827] text-white px-4 py-2.5 text-xs sm:text-sm font-medium flex items-center justify-between shadow-lg relative z-[99999] border-b border-[#0A4F78]/60 dark:border-[#15456E] transition-colors">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-amber-500/20 text-amber-300 border border-amber-500/40 flex items-center justify-center text-sm shadow-xs flex-shrink-0 animate-pulse">
                            <i class="fa-solid fa-user-secret"></i>
                        </span>
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-black uppercase tracking-wider bg-amber-500/20 text-amber-300 border border-amber-500/40">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                {{ app()->getLocale() === 'ar' ? 'جلسة محاكاة نشطة' : 'Impersonation Mode' }}
                            </span>
                            <span class="text-slate-200">
                                {{ app()->getLocale() === 'ar' ? 'أنت مسجل حالياً كالموظف:' : 'You are currently logged in as:' }}
                                <strong class="text-white font-bold">{{ auth()->user()?->name ?? 'Staff' }}</strong>
                                <span class="text-slate-400 text-xs">({{ app()->getLocale() === 'ar' ? 'بواسطة ' : 'by ' }}<span class="text-slate-200 font-semibold">{{ session('impersonator_name', 'Admin') }}</span>)</span>
                            </span>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('admin.impersonate.leave') }}" class="m-0 p-0 flex-shrink-0">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-extrabold bg-amber-500 hover:bg-amber-400 text-slate-950 transition-all shadow-md hover:scale-[1.02] cursor-pointer">
                            <i class="fa-solid fa-arrow-right-from-bracket rtl:rotate-180"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'إنهاء المحاكاة والعودة لحسابي' : 'Leave & Return to Admin' }}</span>
                        </button>
                    </form>
                </div>
            @endif

            <!-- Header -->
            <header class="admin-header">
                <div class="header-left">
                    <!-- Sidebar Collapse/Expand & Off-Canvas Toggle Button -->
                    <button type="button" class="btn btn-ghost btn-icon cursor-pointer admin-sidebar-toggle"
                        onclick="toggleAdminSidebar()" 
                        title="{{ app()->getLocale() === 'ar' ? 'طي / توسيع القائمة الجانبية' : 'Toggle Sidebar' }}" 
                        aria-label="{{ app()->getLocale() === 'ar' ? 'طي / توسيع القائمة الجانبية' : 'Toggle Sidebar' }}">
                        <i class="fa-solid fa-bars-staggered text-lg"></i>
                    </button>

                    <div class="breadcrumbs hidden sm:flex">
                        <a href="{{ route('admin.dashboard') }}"
                            class="breadcrumb-link">{{ __('admin.menu.dashboard') }}</a>
                        @foreach($breadcrumbs as $label => $url)
                            <span class="breadcrumb-separator">›</span>
                            @if($loop->last)
                                <span class="breadcrumb-current">{{ $label }}</span>
                            @else
                                <a href="{{ $url }}" class="breadcrumb-link">{{ $label }}</a>
                            @endif
                        @endforeach
                    </div>
                </div>

                <div class="header-right">
                    <!-- Language Switcher -->
                    @if(app()->getLocale() === 'ar')
                        <a href="{{ route('locale.switch', 'en') }}" class="btn btn-secondary btn-sm font-bold">
                            EN
                        </a>
                    @else
                        <a href="{{ route('locale.switch', 'ar') }}" class="btn btn-secondary btn-sm font-bold">
                            العربية
                        </a>
                    @endif

                    <!-- Theme Toggle -->
                    <button type="button"
                        onclick="if(window.BLUEZONE_THEME){BLUEZONE_THEME.toggle();}else{toggleTheme();}"
                        data-theme-toggle class="btn btn-ghost btn-icon cursor-pointer"
                        title="{{ __('app.theme.title') }}">
                        <i class="fa-solid fa-circle-half-stroke"></i>
                    </button>

                    <!-- Notifications Bell & Dropdown -->
                    @php
                        $authUser = auth()->user();
                        $adminNotifications = $authUser ? $authUser->notifications()->latest()->limit(8)->get() : collect();
                        $adminUnreadCount = $authUser ? $authUser->unreadNotifications()->count() : 0;
                    @endphp
                    <div class="admin-notification-wrapper" id="adminNotificationWrapper">
                        <button type="button" id="adminNotificationBtn" class="admin-notification-btn"
                            onclick="toggleAdminNotifications(event)"
                            aria-label="{{ __('admin.notifications.title') ?? 'Notifications' }}"
                            title="{{ __('admin.notifications.title') ?? 'Notifications' }}">
                            <i class="fa-regular fa-bell text-lg"></i>
                            <span id="adminNotificationBadge"
                                class="admin-notification-badge {{ $adminUnreadCount > 0 ? '' : 'hidden' }}">
                                {{ $adminUnreadCount > 99 ? '99+' : $adminUnreadCount }}
                            </span>
                        </button>

                        <!-- Notification Dropdown -->
                        <div id="adminNotificationDropdown" class="admin-notification-dropdown">
                            <div class="notification-dropdown-header">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-sm text-slate-800 dark:text-white">
                                        {{ __('admin.notifications.title') ?? 'Notifications' }}
                                    </span>
                                    <span id="adminNotificationHeaderCount"
                                        class="text-xs bg-sky-100 text-sky-700 dark:bg-sky-900/60 dark:text-sky-300 px-2 py-0.5 rounded-full font-bold {{ $adminUnreadCount > 0 ? '' : 'hidden' }}">
                                        {{ $adminUnreadCount }} {{ __('admin.notifications.unread') ?? 'new' }}
                                    </span>
                                </div>
                                @if($adminUnreadCount > 0)
                                    <button type="button" onclick="markAllNotificationsAsRead(event)"
                                        id="adminMarkAllReadBtn"
                                        class="text-xs text-sky-600 dark:text-sky-400 font-semibold hover:underline bg-transparent border-none p-0 cursor-pointer">
                                        {{ __('admin.notifications.mark_all_read') ?? 'Mark all as read' }}
                                    </button>
                                @endif
                            </div>

                            <div class="notification-dropdown-body" id="adminNotificationList">
                                @forelse($adminNotifications as $notif)
                                    @php
                                        $isUnread = is_null($notif->read_at);
                                        $nData = $notif->data ?? [];
                                        $nIcon = $nData['icon'] ?? 'fa-solid fa-bell text-sky-500';
                                        $rawUrl = $nData['action_url'] ?? null;
                                        if ($rawUrl && (str_starts_with($rawUrl, 'http://') || str_starts_with($rawUrl, 'https://'))) {
                                            $parsed = parse_url($rawUrl);
                                            $rawUrl = ($parsed['path'] ?? '') . (isset($parsed['query']) ? '?' . $parsed['query'] : '') . (isset($parsed['fragment']) ? '#' . $parsed['fragment'] : '');
                                        }
                                        $nUrl = $rawUrl;
                                    @endphp
                                    <a href="{{ $nUrl ?? 'javascript:void(0)' }}"
                                        onclick="handleNotificationClick('{{ $notif->id }}', '{{ $nUrl }}', event)"
                                        class="notification-item-row {{ $isUnread ? 'is-unread' : '' }}"
                                        data-notif-id="{{ $notif->id }}">
                                        <div
                                            class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center flex-shrink-0 mt-0.5">
                                            <i class="{{ $nIcon }} text-sm"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-xs font-bold text-slate-800 dark:text-white truncate">
                                                {{ $nData['title'] ?? 'Notification' }}
                                            </div>
                                            <div
                                                class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 mt-0.5 leading-snug">
                                                {{ $nData['message'] ?? '' }}
                                            </div>
                                            <div
                                                class="text-[10px] text-slate-400 dark:text-slate-500 mt-1 flex items-center gap-1">
                                                <i class="fa-regular fa-clock text-[9px]"></i>
                                                <span>{{ $notif->created_at ? $notif->created_at->diffForHumans() : '' }}</span>
                                            </div>
                                        </div>
                                        @if($isUnread)
                                            <span class="notification-unread-dot" title="Unread"></span>
                                        @endif
                                    </a>
                                @empty
                                    <div class="p-8 text-center">
                                        <i
                                            class="fa-solid fa-bell-slash text-slate-300 dark:text-slate-600 text-2xl mb-2 block"></i>
                                        <p class="text-xs text-muted m-0">
                                            {{ __('admin.notifications.no_notifications') ?? 'No notifications' }}</p>
                                    </div>
                                @endforelse
                            </div>

                            <div class="notification-dropdown-footer"
                                style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 1rem;">
                                <a href="{{ route('admin.notifications.index') }}"
                                    class="text-xs font-bold text-sky-600 dark:text-sky-400 hover:underline flex items-center gap-1">
                                    <span>{{ __('admin.notifications.view_all') ?? 'View all notifications' }}</span>
                                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                </a>
                                <button type="button" onclick="openFcmPermissionModal()"
                                    class="text-xs text-slate-500 hover:text-sky-600 dark:text-slate-400 dark:hover:text-sky-300 font-semibold bg-transparent border-none p-0 cursor-pointer flex items-center gap-1.5"
                                    title="{{ __('admin.notifications.browser_setup_title') }}">
                                    <i class="fa-solid fa-gear text-[11px]"></i>
                                    <span>{{ __('admin.notifications.browser_setup') }}</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- View Storefront Link -->
                    <a href="{{ route('customer.home') }}" class="btn btn-outline btn-sm hidden md:inline-flex"
                        target="_blank">
                        {{ __('app.nav.home') }} <i class="fa-solid fa-arrow-up-right-from-square mr-1 ml-1"></i>
                    </a>

                    <!-- User Profile Pill & Dropdown -->
                    @php
                        $userName = $authUser ? $authUser->name : 'Administrator';
                        $userEmail = $authUser ? $authUser->email : 'admin@bluezone.com';

                        $userRole = 'Super Administrator';
                        if ($authUser) {
                            if ($authUser->relationLoaded('role') && $authUser->role) {
                                $userRole = is_object($authUser->role) ? ($authUser->role->name ?? 'Admin') : (string) $authUser->role;
                            } elseif (method_exists($authUser, 'role') && $authUser->role) {
                                $userRole = is_object($authUser->role) ? ($authUser->role->name ?? 'Admin') : (string) $authUser->role;
                            } elseif (!empty($authUser->role_id)) {
                                $roleObj = \App\Models\Role::find($authUser->role_id);
                                $userRole = $roleObj ? $roleObj->name : 'Admin';
                            } elseif (!empty($authUser->role) && is_string($authUser->role)) {
                                $userRole = ucfirst($authUser->role);
                            }
                        }

                        $initials = $authUser ? strtoupper(substr(trim($authUser->name), 0, 2)) : 'AD';
                        $avatarUrl = $authUser ? $authUser->avatar_url : null;
                    @endphp

                    <div class="admin-profile-wrapper" id="adminProfileWrapper">
                        <button type="button" id="adminProfileDropdownToggle" class="admin-profile-btn"
                            onclick="toggleAdminProfileDropdown(event)" aria-expanded="false" aria-haspopup="true"
                            style="display: inline-flex; align-items: center; gap: 0.625rem; vertical-align: middle; padding: 0.35rem 0.625rem; border-radius: 9999px; cursor: pointer; text-decoration: none; border: 1px solid transparent; box-sizing: border-box;">
                            <div
                                style="display: flex; align-items: center; justify-content: center; width: 34px; height: 34px; flex-shrink: 0;">
                                @if($avatarUrl)
                                    <img src="{{ $avatarUrl }}" alt="{{ $userName }}"
                                        style="width: 34px; height: 34px; border-radius: 50%; object-fit: cover; border: 2px solid var(--color-primary-light);"
                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div
                                        style="display: none; width: 34px; height: 34px; border-radius: 50%; background: linear-gradient(135deg, #0A4F78, #2A8FC2); color: #FFF; align-items: center; justify-content: center; font-weight: 700; font-size: 0.8125rem; border: 2px solid rgba(255,255,255,0.2); flex-shrink: 0;">
                                        {{ $initials }}
                                    </div>
                                @else
                                    <div
                                        style="width: 34px; height: 34px; border-radius: 50%; background: linear-gradient(135deg, #0A4F78, #2A8FC2); color: #FFF; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.8125rem; border: 2px solid rgba(255,255,255,0.2); flex-shrink: 0;">
                                        {{ $initials }}
                                    </div>
                                @endif
                            </div>
                            <div class="admin-profile-meta hidden sm:flex flex-col text-start"
                                style="display: flex; flex-direction: column; justify-content: center; text-align: start; line-height: normal;">
                                <span class="text-sm font-bold text-slate-800 dark:text-white"
                                    style="line-height: 1.2;">
                                    <bdi>{{ $userName }}</bdi>
                                </span>
                                <span style="font-size: 0.7rem; color: var(--color-text-muted); line-height: 1.1;">
                                    {{ $userRole }}
                                </span>
                            </div>
                            <i class="fa-solid fa-chevron-down" id="adminProfileChevron"
                                style="font-size: 0.7rem; opacity: 0.6; transition: transform 0.2s ease;"></i>
                        </button>

                        <!-- Dropdown Menu -->
                        <div id="adminProfileDropdown" class="admin-profile-dropdown">
                            <div
                                style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--color-border); background: var(--color-bg-subtle, rgba(0,0,0,0.02));">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div style="position: relative; width: 44px; height: 44px; flex-shrink: 0;">
                                        @if($avatarUrl)
                                            <img src="{{ $avatarUrl }}" alt="{{ $userName }}"
                                                style="width: 44px; height: 44px; border-radius: 50%; object-fit: cover;"
                                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <div
                                                style="display: none; width: 44px; height: 44px; border-radius: 50%; background: linear-gradient(135deg, #0A4F78, #2A8FC2); color: #FFF; align-items: center; justify-content: center; font-weight: 800; font-size: 1rem;">
                                                {{ $initials }}
                                            </div>
                                        @else
                                            <div
                                                style="width: 44px; height: 44px; border-radius: 50%; background: linear-gradient(135deg, #0A4F78, #2A8FC2); color: #FFF; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1rem;">
                                                {{ $initials }}
                                            </div>
                                        @endif
                                    </div>
                                    <div style="flex: 1; min-width: 0;">
                                        <div
                                            style="font-weight: 700; font-size: 0.9375rem; color: var(--color-text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            <bdi>{{ $userName }}</bdi></div>
                                        <div
                                            style="font-size: 0.75rem; color: var(--color-text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            {{ $userEmail }}</div>
                                    </div>
                                </div>
                                <div style="margin-top: 0.6rem;">
                                    <span
                                        style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.2rem 0.6rem; border-radius: 9999px; background: rgba(10, 79, 120, 0.1); color: var(--color-primary); font-size: 0.7rem; font-weight: 700;">
                                        <i class="fa-solid fa-shield-halved"></i> {{ $userRole }}
                                    </span>
                                </div>
                            </div>

                            <div style="padding: 0.5rem;">
                                <a href="{{ route('admin.profile.index') }}" class="admin-dropdown-item">
                                    <i class="fa-solid fa-user-pen"
                                        style="width: 18px; color: var(--color-primary);"></i>
                                    <span>{{ __('admin.profile.title') }}</span>
                                </a>
                                <a href="{{ route('admin.profile.index') }}#security" class="admin-dropdown-item">
                                    <i class="fa-solid fa-shield-keyhole" style="width: 18px; color: #0284c7;"></i>
                                    <span>{{ __('admin.profile.password') }}</span>
                                </a>
                                @if($u && ($u->hasPermission('settings.view') || $u->hasPermission('settings') || (method_exists($u, 'isSuperAdmin') && $u->isSuperAdmin())))
                                    <a href="{{ route('admin.settings.index') }}" class="admin-dropdown-item">
                                        <i class="fa-solid fa-sliders" style="width: 18px; color: #10b981;"></i>
                                        <span>{{ __('admin.menu.settings') }}</span>
                                    </a>
                                @endif
                                <a href="{{ route('customer.home') }}" target="_blank" class="admin-dropdown-item">
                                    <i class="fa-solid fa-store" style="width: 18px; color: #8b5cf6;"></i>
                                    <span style="flex: 1;">{{ __('app.nav.home') }}</span>
                                    <i class="fa-solid fa-arrow-up-right-from-square"
                                        style="font-size: 0.7rem; opacity: 0.5;"></i>
                                </a>
                            </div>

                            <div style="padding: 0.5rem; border-top: 1px solid var(--color-border);">
                                @if(session()->has('impersonated_by'))
                                    <form method="POST" action="{{ route('admin.impersonate.leave') }}" style="margin: 0; padding-bottom: 0.35rem;">
                                        @csrf
                                        <button type="submit" class="admin-dropdown-item font-bold text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-950/40 w-full text-start cursor-pointer">
                                            <i class="fa-solid fa-arrow-right-from-bracket rtl:rotate-180" style="width: 18px; color: #d97706;"></i>
                                            <span style="color: #d97706;">{{ app()->getLocale() === 'ar' ? 'إنهاء المحاكاة والعودة لحسابي' : 'Leave Impersonation' }}</span>
                                        </button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('admin.logout') }}" id="adminLogoutForm"
                                    style="margin: 0;">
                                    @csrf
                                    <button type="submit" class="admin-dropdown-item admin-dropdown-logout-btn">
                                        <i class="fa-solid fa-right-from-bracket"
                                            style="width: 18px; color: #ef4444;"></i>
                                        <span
                                            style="color: #ef4444; font-weight: 700;">{{ __('app.nav.logout') }}</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Body -->
            <div class="admin-content-body">
                @if($pageTitle)
                    <div class="page-header">
                        <div>
                            <h1 class="page-title">{{ $pageTitle }}</h1>
                            @if($pageSubtitle)
                                <p class="page-subtitle">{{ $pageSubtitle }}</p>
                            @endif
                        </div>

                        @isset($actions)
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                {{ $actions }}
                            </div>
                        @endisset
                    </div>
                @endif

                {{ $slot }}
            </div>
        </main>
    </div>

    <!-- Global Destructive & Force Delete Confirmation Modal -->
    <div id="globalDeleteModal" class="modal-backdrop"
        onclick="if(event.target === this) closeModal('globalDeleteModal')">
        <div class="modal-dialog">
            <form id="globalDeleteForm" method="POST" action="">
                @csrf
                <input type="hidden" name="_method" value="DELETE">

                <div class="modal-header">
                    <h4 class="modal-title font-bold text-base"
                        style="display: flex; align-items: center; gap: 0.625rem; margin: 0; color: #DC2626;">
                        <i id="globalDeleteIcon" class="fa-solid fa-triangle-exclamation"></i>
                        <span id="globalDeleteTitle">{{ __('app.actions.delete') }}</span>
                    </h4>
                    <button type="button" class="btn btn-ghost btn-sm cursor-pointer"
                        onclick="closeModal('globalDeleteModal')" aria-label="Close" style="padding: 0.35rem 0.6rem;">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <p id="globalDeleteMessage"
                        style="color: var(--color-text-secondary); margin: 0; font-size: 0.9375rem; line-height: 1.6;">
                        {{ __('admin.confirm_action') }}
                    </p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary text-sm cursor-pointer"
                        onclick="closeModal('globalDeleteModal')">
                        {{ __('app.actions.cancel') }}
                    </button>
                    <button type="submit" id="globalDeleteBtn" class="btn btn-danger text-sm font-bold cursor-pointer">
                        <i class="fa-solid fa-trash-can mr-1.5 ml-1.5"></i>
                        <span id="globalDeleteBtnText">{{ __('app.actions.delete') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Global Restore Form (Hidden) -->
    <form id="globalRestoreForm" method="POST" action="" style="display: none;">
        @csrf
    </form>

    <script>
        function openModal(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.add('is-active', 'active');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.remove('is-active', 'active');
                document.body.style.overflow = '';
            }
        }

        function confirmDelete(actionUrl, itemName = '', isForceDelete = false) {
            const modal = document.getElementById('globalDeleteModal');
            const form = document.getElementById('globalDeleteForm');
            const title = document.getElementById('globalDeleteTitle');
            const message = document.getElementById('globalDeleteMessage');
            const btnText = document.getElementById('globalDeleteBtnText');
            const btn = document.getElementById('globalDeleteBtn');
            const icon = document.getElementById('globalDeleteIcon');

            if (form && modal) {
                form.action = actionUrl;
                const isAr = document.documentElement.lang === 'ar' || document.documentElement.dir === 'rtl';

                if (isForceDelete) {
                    title.textContent = isAr ? 'تأكيد الحذف النهائي الشامل' : 'Confirm Permanent Deletion';
                    message.textContent = isAr
                        ? `تحذير بالغ الأهمية: هل أنت متأكد من رغبتك في حذف [${itemName || 'العنصر'}] نهائياً من قاعدة البيانات؟ سيتم إزالة جميع السجلات المرتبطة به ولن تتمكن من استعادته لاحقاً!`
                        : `Extreme Warning: Are you sure you want to permanently erase [${itemName || 'this record'}] from the database? This action is irreversible!`;
                    btnText.textContent = isAr ? 'حذف نهائي فوري' : 'Permanently Delete';
                    btn.className = 'btn btn-danger text-sm font-bold cursor-pointer';
                    icon.className = 'fa-solid fa-radiation';
                } else {
                    title.textContent = isAr ? 'تأكيد النقل لسلة المحذوفات' : 'Confirm Move to Trash';
                    message.textContent = isAr
                        ? `هل أنت متأكد من نقل [${itemName || 'العنصر'}] إلى سلة المحذوفات؟ يمكنك مراجعته أو استعادته لاحقاً.`
                        : `Are you sure you want to move [${itemName || 'this item'}] to trash? You can restore it anytime later.`;
                    btnText.textContent = isAr ? 'نقل للمحذوفات' : 'Move to Trash';
                    btn.className = 'btn btn-danger text-sm font-bold cursor-pointer';
                    icon.className = 'fa-solid fa-trash-can';
                }

                openModal('globalDeleteModal');
            }
        }

        function confirmRestore(restoreUrl, itemName = '') {
            const isAr = document.documentElement.lang === 'ar' || document.documentElement.dir === 'rtl';
            const msg = isAr
                ? `هل ترغب في استعادة [${itemName || 'العنصر'}] وإعادته إلى السجلات النشطة؟`
                : `Do you want to restore [${itemName || 'this record'}] back to active status?`;

            if (confirm(msg)) {
                const form = document.getElementById('globalRestoreForm');
                if (form) {
                    form.action = restoreUrl;
                    form.submit();
                }
            }
        }

        /* Sidebar Dropdown Accordion Toggle */
        function toggleSidebarDropdown(groupId) {
            const group = document.getElementById(groupId);
            if (!group) return;

            // If desktop layout is collapsed, expand it when clicking a group
            const layout = document.querySelector('.admin-layout');
            if (layout && layout.classList.contains('sidebar-collapsed') && window.innerWidth >= 1024) {
                toggleAdminSidebar();
            }

            group.classList.toggle('is-open');
        }

        /* Sidebar Toggle Logic (Desktop Rail Collapse / Expand & Mobile Drawer) */
        function toggleAdminSidebar() {
            const sidebar = document.getElementById('adminSidebar');
            const backdrop = document.getElementById('adminSidebarBackdrop');
            const layout = document.querySelector('.admin-layout');

            if (window.innerWidth < 1024) {
                // Mobile Drawer
                if (sidebar) {
                    const isOpen = sidebar.classList.toggle('is-open');
                    if (backdrop) {
                        if (isOpen) {
                            backdrop.classList.add('active');
                            document.body.style.overflow = 'hidden';
                        } else {
                            backdrop.classList.remove('active');
                            document.body.style.overflow = '';
                        }
                    }
                }
            } else {
                // Desktop Collapse / Expand
                if (layout) {
                    const isCollapsed = layout.classList.toggle('sidebar-collapsed');
                    try {
                        localStorage.setItem('bz_admin_sidebar_collapsed', isCollapsed ? 'true' : 'false');
                    } catch(e) {}
                }
            }
        }

        // Restore desktop collapsed state on DOMContentLoaded
        document.addEventListener('DOMContentLoaded', function() {
            try {
                if (window.innerWidth >= 1024 && localStorage.getItem('bz_admin_sidebar_collapsed') === 'true') {
                    const layout = document.querySelector('.admin-layout');
                    if (layout) layout.classList.add('sidebar-collapsed');
                }
            } catch(e) {}
        });

        /* Profile Dropdown Logic */
        function toggleAdminProfileDropdown(event) {
            if (event) {
                event.stopPropagation();
            }
            const dropdown = document.getElementById('adminProfileDropdown');
            const toggleBtn = document.getElementById('adminProfileDropdownToggle');
            const chevron = document.getElementById('adminProfileChevron');

            // Close notification dropdown if open
            const notifDropdown = document.getElementById('adminNotificationDropdown');
            if (notifDropdown) notifDropdown.classList.remove('show');

            if (dropdown) {
                const isShown = dropdown.classList.toggle('show');
                if (toggleBtn) {
                    toggleBtn.setAttribute('aria-expanded', isShown ? 'true' : 'false');
                }
                if (chevron) {
                    chevron.style.transform = isShown ? 'rotate(180deg)' : '';
                }
            }
        }

        /* Notification Dropdown Logic */
        function toggleAdminNotifications(event) {
            if (event) {
                event.stopPropagation();
            }
            const notifDropdown = document.getElementById('adminNotificationDropdown');
            const profileDropdown = document.getElementById('adminProfileDropdown');
            const profileChevron = document.getElementById('adminProfileChevron');
            const profileBtn = document.getElementById('adminProfileDropdownToggle');

            if (profileDropdown && profileDropdown.classList.contains('show')) {
                profileDropdown.classList.remove('show');
                if (profileBtn) profileBtn.setAttribute('aria-expanded', 'false');
                if (profileChevron) profileChevron.style.transform = '';
            }

            if (notifDropdown) {
                notifDropdown.classList.toggle('show');
            }
        }

        function handleNotificationClick(id, actionUrl, event) {
            let targetUrl = actionUrl;
            if (targetUrl && (targetUrl.startsWith('http://') || targetUrl.startsWith('https://'))) {
                try {
                    const u = new URL(targetUrl);
                    targetUrl = u.pathname + u.search + u.hash;
                } catch (e) { }
            }

            fetch('/admin/notifications/' + id + '/read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            }).then(res => res.json()).then(data => {
                if (data.success) {
                    updateNotificationBadge(data.unread_count);
                    const row = document.querySelector(`[data-notif-id="${id}"]`);
                    if (row) {
                        row.classList.remove('is-unread');
                        const dot = row.querySelector('.notification-unread-dot');
                        if (dot) dot.remove();
                    }
                }
            }).catch(e => console.error(e));

            if (targetUrl && targetUrl !== 'javascript:void(0)') {
                window.location.href = targetUrl;
            }
        }

        function markAllNotificationsAsRead(event) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }

            fetch('/admin/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            }).then(res => res.json()).then(data => {
                if (data.success) {
                    updateNotificationBadge(0);
                    document.querySelectorAll('.notification-item-row.is-unread').forEach(el => {
                        el.classList.remove('is-unread');
                        const dot = el.querySelector('.notification-unread-dot');
                        if (dot) dot.remove();
                    });
                    const markAllBtn = document.getElementById('adminMarkAllReadBtn');
                    if (markAllBtn) markAllBtn.remove();
                    const headerCount = document.getElementById('adminNotificationHeaderCount');
                    if (headerCount) headerCount.classList.add('hidden');
                }
            }).catch(e => console.error(e));
        }

        function updateNotificationBadge(count) {
            const badge = document.getElementById('adminNotificationBadge');
            const headerCount = document.getElementById('adminNotificationHeaderCount');

            if (badge) {
                if (count > 0) {
                    badge.textContent = count > 99 ? '99+' : count;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            }

            if (headerCount) {
                if (count > 0) {
                    headerCount.textContent = count + ' {{ __("admin.notifications.unread") ?? "new" }}';
                    headerCount.classList.remove('hidden');
                } else {
                    headerCount.classList.add('hidden');
                }
            }
        }

        /* Outside click & Escape listener */
        document.addEventListener('click', function (event) {
            const wrapper = document.getElementById('adminProfileWrapper');
            const dropdown = document.getElementById('adminProfileDropdown');
            const chevron = document.getElementById('adminProfileChevron');
            const toggleBtn = document.getElementById('adminProfileDropdownToggle');

            if (dropdown && dropdown.classList.contains('show')) {
                if (wrapper && !wrapper.contains(event.target)) {
                    dropdown.classList.remove('show');
                    if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'false');
                    if (chevron) chevron.style.transform = '';
                }
            }

            const notifWrapper = document.getElementById('adminNotificationWrapper');
            const notifDropdown = document.getElementById('adminNotificationDropdown');
            if (notifDropdown && notifDropdown.classList.contains('show')) {
                if (notifWrapper && !notifWrapper.contains(event.target)) {
                    notifDropdown.classList.remove('show');
                }
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                const dropdown = document.getElementById('adminProfileDropdown');
                const chevron = document.getElementById('adminProfileChevron');
                const toggleBtn = document.getElementById('adminProfileDropdownToggle');
                if (dropdown && dropdown.classList.contains('show')) {
                    dropdown.classList.remove('show');
                    if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'false');
                    if (chevron) chevron.style.transform = '';
                }

                const notifDropdown = document.getElementById('adminNotificationDropdown');
                if (notifDropdown && notifDropdown.classList.contains('show')) {
                    notifDropdown.classList.remove('show');
                }

                const sidebar = document.getElementById('adminSidebar');
                const backdrop = document.getElementById('adminSidebarBackdrop');
                if (sidebar && sidebar.classList.contains('is-open')) {
                    sidebar.classList.remove('is-open');
                    if (backdrop) backdrop.classList.remove('active');
                    document.body.style.overflow = '';
                }
            }
        });
    </script>

    <!-- Toast Notification Container for Real-time FCM Alerts -->
    <div id="adminToastContainer"
        style="position: fixed; top: 1.25rem; right: 1.25rem; z-index: 999999; display: flex; flex-direction: column; gap: 0.75rem; pointer-events: none; max-width: 400px; width: calc(100% - 2.5rem);"
        dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"></div>

    <!-- Firebase App & Messaging SDKs (v9 Compat) -->
    <script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-messaging-compat.js"></script>

    <script>
        /* =========================================================================
           Real-time Notification & FCM Engine (Singleton Service Architecture)
           ========================================================================= */
        function playNotificationChime() {
            try {
                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                if (!AudioCtx) return;
                const ctx = new AudioCtx();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.type = 'sine';
                const now = ctx.currentTime;
                osc.frequency.setValueAtTime(587.33, now); // D5
                osc.frequency.setValueAtTime(880, now + 0.08); // A5
                gain.gain.setValueAtTime(0.18, now);
                gain.gain.exponentialRampToValueAtTime(0.001, now + 0.35);
                osc.start(now);
                osc.stop(now + 0.35);
            } catch (e) {
                // AudioContext autoplay policies handled silently
            }
        }

        function showAdminToast(title, message, iconClass, actionUrl) {
            playNotificationChime();
            const container = document.getElementById('adminToastContainer');
            if (!container) return;

            const toast = document.createElement('div');
            toast.style.cssText = 'pointer-events: auto; background: var(--color-surface, #FFFFFF); color: var(--color-text, #1E293B); border: 1px solid var(--color-border, #E2E8F0); border-radius: 1rem; padding: 1rem; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.15), 0 8px 10px -6px rgba(0,0,0,0.1); display: flex; align-items: flex-start; gap: 0.875rem; transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1); position: relative; overflow: hidden;';

            // Top accent border
            const accent = document.createElement('div');
            accent.style.cssText = 'position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, #0A4F78, #0284c7);';
            toast.appendChild(accent);

            // Icon box
            const iconBox = document.createElement('div');
            iconBox.style.cssText = 'width: 38px; height: 38px; border-radius: 0.75rem; background: rgba(10, 79, 120, 0.1); display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;';
            iconBox.innerHTML = `<i class="${iconClass || 'fa-solid fa-bell text-sky-500'} text-base"></i>`;
            toast.appendChild(iconBox);

            // Content
            const content = document.createElement('div');
            content.style.cssText = 'flex: 1; min-width: 0; text-align: start;';
            let html = `<div style="font-size: 0.875rem; font-weight: 700; line-height: 1.25; margin-bottom: 0.25rem;">${title}</div>
                        <div style="font-size: 0.775rem; opacity: 0.8; line-height: 1.4;">${message}</div>`;
            if (actionUrl && actionUrl !== 'javascript:void(0)') {
                html += `<a href="${actionUrl}" style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.75rem; font-weight: 700; color: #0284c7; margin-top: 0.5rem; text-decoration: none;"><span>{{ __("admin.notifications.view_details") ?? "View details" }}</span> <i class="fa-solid fa-arrow-right" style="font-size: 0.65rem;"></i></a>`;
            }
            content.innerHTML = html;
            toast.appendChild(content);

            // Dismiss button
            const closeBtn = document.createElement('button');
            closeBtn.type = 'button';
            closeBtn.style.cssText = 'background: transparent; border: none; cursor: pointer; opacity: 0.5; font-size: 0.875rem; padding: 0.25rem; line-height: 1;';
            closeBtn.innerHTML = '<i class="fa-solid fa-xmark"></i>';
            closeBtn.onclick = () => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(-10px)';
                setTimeout(() => toast.remove(), 300);
            };
            toast.appendChild(closeBtn);

            container.appendChild(toast);

            setTimeout(() => {
                if (toast.parentElement) {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateY(-10px)';
                    setTimeout(() => toast.remove(), 300);
                }
            }, 6500);
        }

        function prependNotificationToDropdown(title, message, iconClass, actionUrl, notifId) {
            const list = document.getElementById('adminNotificationList');
            if (!list) return;

            // Remove empty state message if present
            const emptyState = list.querySelector('.fa-bell-slash')?.parentElement;
            if (emptyState) emptyState.remove();

            const row = document.createElement('a');
            row.href = actionUrl || 'javascript:void(0)';
            row.className = 'notification-item-row is-unread';
            if (notifId) {
                row.setAttribute('data-notif-id', notifId);
                row.onclick = (e) => handleNotificationClick(notifId, actionUrl, e);
            }
            row.innerHTML = `
                <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <i class="${iconClass || 'fa-solid fa-bell text-sky-500'} text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-xs font-bold text-slate-800 dark:text-white truncate">${title}</div>
                    <div class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 mt-0.5 leading-snug">${message}</div>
                    <div class="text-[10px] text-slate-400 dark:text-slate-500 mt-1 flex items-center gap-1">
                        <i class="fa-regular fa-clock text-[9px]"></i>
                        <span>{{ __("app.time.just_now") ?? "Just now" }}</span>
                    </div>
                </div>
                <span class="notification-unread-dot" title="Unread"></span>
            `;

            list.insertBefore(row, list.firstChild);

            // Increment badge counter
            const badge = document.getElementById('adminNotificationBadge');
            const curCount = badge && !badge.classList.contains('hidden') ? (parseInt(badge.textContent.trim()) || 0) : 0;
            updateNotificationBadge(curCount + 1);
        }

        window.triggerFcmTestPush = function () {
            const btn = document.getElementById('btnTestFcmPush');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>Sending...</span>';
            }

            fetch('/admin/notifications/test-push', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        showAdminToast(
                            'تجربة إشعارات FCM | FCM Realtime Test',
                            'تم إرسال إشعار تجريبي بنجاح عبر نظام FCM Realtime.',
                            'fa-solid fa-satellite-dish text-sky-500',
                            '/admin/inventory'
                        );
                        prependNotificationToDropdown(
                            'تجربة إشعارات FCM',
                            'تم إرسال إشعار تجريبي بنجاح عبر نظام FCM Realtime.',
                            'fa-solid fa-satellite-dish text-sky-500',
                            '/admin/inventory'
                        );
                    }
                })
                .catch(err => {
                    console.error('Test push error:', err);
                })
                .finally(() => {
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fa-solid fa-satellite-dish text-sky-500"></i> <span>{{ __("admin.notifications.test_push") ?? "Test FCM Push" }}</span>';
                    }
                });
        };
    </script>

    <!-- =========================================================================
         BlueZone Branded FCM Notification Permission & Browser Settings Modal
         ========================================================================= -->
    <style>
        /* =========================================================================
           Sidebar Dropdown Accordions & Responsive Desktop Collapse
           ========================================================================= */
        .sidebar-dropdown-group {
            margin-bottom: 0.25rem;
            border-radius: 0.625rem;
            transition: all 0.2s ease;
        }

        .sidebar-dropdown-btn {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.6875rem 0.875rem;
            border-radius: 0.5rem;
            color: #94A3B8;
            font-size: 0.875rem;
            font-weight: 600;
            background: transparent;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            text-align: start;
            user-select: none;
        }

        .sidebar-dropdown-btn:hover {
            background-color: rgba(255, 255, 255, 0.07);
            color: #FFFFFF;
        }

        .sidebar-dropdown-group.is-open .sidebar-dropdown-btn {
            color: #FFFFFF;
            background-color: rgba(255, 255, 255, 0.04);
        }

        .sidebar-dropdown-btn.active-parent {
            color: #38BDF8;
            font-weight: 700;
        }

        .sidebar-dropdown-label {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            min-width: 0;
        }

        .sidebar-dropdown-meta {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-shrink: 0;
        }

        .sidebar-dropdown-chevron {
            font-size: 0.75rem;
            transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            color: #64748B;
        }

        .sidebar-dropdown-group.is-open .sidebar-dropdown-chevron {
            transform: rotate(180deg);
            color: #38BDF8;
        }

        [dir="rtl"] .sidebar-dropdown-group.is-open .sidebar-dropdown-chevron {
            transform: rotate(-180deg);
        }

        /* Submenu Container */
        .sidebar-submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.25s ease, padding 0.2s ease;
            opacity: 0;
            display: flex;
            flex-direction: column;
            gap: 0.15rem;
            padding-inline-start: 1.25rem;
            margin-top: 0.1rem;
            border-inline-start: 2px solid rgba(255, 255, 255, 0.08);
            margin-inline-start: 1.25rem;
        }

        .sidebar-dropdown-group.is-open .sidebar-submenu {
            max-height: 4000px;
            opacity: 1;
            padding-top: 0.25rem;
            padding-bottom: 0.35rem;
        }

        .sidebar-sublink {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            padding: 0.45rem 0.75rem;
            border-radius: 0.4rem;
            color: #94A3B8;
            font-size: 0.8125rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .sidebar-sublink:hover {
            color: #FFFFFF;
            background-color: rgba(255, 255, 255, 0.06);
            transform: translateX(3px);
        }

        [dir="rtl"] .sidebar-sublink:hover {
            transform: translateX(-3px);
        }

        .sidebar-sublink.active {
            color: #38BDF8;
            background: rgba(56, 189, 248, 0.12);
            font-weight: 700;
        }

        /* Desktop Collapsible Rail Mode */
        @media (min-width: 1024px) {
            .admin-layout.sidebar-collapsed .admin-sidebar {
                width: 78px;
            }
            
            .admin-layout.sidebar-collapsed .sidebar-header {
                padding: 0 0.75rem;
                justify-content: center;
            }

            .admin-layout.sidebar-collapsed .sidebar-brand-title,
            .admin-layout.sidebar-collapsed .sidebar-link span,
            .admin-layout.sidebar-collapsed .sidebar-dropdown-btn span,
            .admin-layout.sidebar-collapsed .sidebar-dropdown-meta,
            .admin-layout.sidebar-collapsed .sidebar-submenu {
                display: none !important;
            }

            .admin-layout.sidebar-collapsed .sidebar-link,
            .admin-layout.sidebar-collapsed .sidebar-dropdown-btn {
                justify-content: center;
                padding: 0.75rem 0.5rem;
            }

            .admin-layout.sidebar-collapsed .sidebar-link-icon {
                margin: 0;
                font-size: 1.25rem;
            }
        }

        @keyframes bzModalPop {
            0% {
                transform: scale(0.93) translateY(10px);
                opacity: 0;
            }

            100% {
                transform: scale(1) translateY(0);
                opacity: 1;
            }
        }

        @keyframes bzBellRing {

            0%,
            100% {
                transform: rotate(0deg);
            }

            10%,
            30% {
                transform: rotate(-14deg);
            }

            20%,
            40% {
                transform: rotate(14deg);
            }

            50% {
                transform: rotate(0deg);
            }
        }

        .bz-fcm-modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(10, 25, 45, 0.78);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            z-index: 999999;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 0.75rem;
            box-sizing: border-box;
            overflow-y: auto;
        }

        @media (min-width: 640px) {
            .bz-fcm-modal-backdrop {
                padding: 1.25rem;
            }
        }

        .bz-fcm-modal-dialog {
            background: var(--color-surface, #FFFFFF);
            color: var(--color-text, #1E293B);
            border: 1px solid rgba(10, 79, 120, 0.2);
            border-radius: 1.25rem;
            max-width: 520px;
            width: 100%;
            max-height: calc(100vh - 1.5rem);
            max-height: calc(100dvh - 1.5rem);
            box-shadow: 0 25px 50px -12px rgba(10, 79, 120, 0.35), 0 0 0 1px rgba(255, 255, 255, 0.1);
            overflow-y: auto;
            overflow-x: hidden;
            position: relative;
            box-sizing: border-box;
            margin: auto;
            animation: bzModalPop 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @media (min-width: 640px) {
            .bz-fcm-modal-dialog {
                border-radius: 1.5rem;
                max-height: calc(100vh - 2.5rem);
                max-height: calc(100dvh - 2.5rem);
            }
        }

        .dark .bz-fcm-modal-dialog {
            background: #0F172A;
            color: #F8FAFC;
            border-color: rgba(255, 255, 255, 0.1);
        }

        .bz-fcm-modal-body {
            padding: 1.25rem 1rem 1rem 1rem;
            text-align: start;
            box-sizing: border-box;
        }

        @media (min-width: 640px) {
            .bz-fcm-modal-body {
                padding: 2rem 2rem 1.75rem 2rem;
            }
        }

        .bz-config-card {
            border: 1.5px solid var(--color-border, #E2E8F0);
            border-radius: 1rem;
            padding: 0.875rem 1rem;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            background: var(--color-bg-subtle, rgba(0,0,0,0.02));
            box-sizing: border-box;
        }

        @media (min-width: 640px) {
            .bz-config-card {
                padding: 1rem 1.15rem;
                gap: 0.875rem;
            }
        }

        .dark .bz-config-card {
            border-color: rgba(255, 255, 255, 0.08);
            background: rgba(255, 255, 255, 0.03);
        }

        .bz-config-card:hover {
            border-color: #0284C7;
            transform: translateY(-1px);
        }

        .bz-config-card.selected-allow {
            border-color: #0284C7;
            background: rgba(2, 132, 199, 0.06);
            box-shadow: 0 0 0 2px rgba(2, 132, 199, 0.2);
        }

        .dark .bz-config-card.selected-allow {
            background: rgba(2, 132, 199, 0.15);
            border-color: #38BDF8;
            box-shadow: 0 0 0 2px rgba(56, 189, 248, 0.25);
        }

        .bz-config-card.selected-deny {
            border-color: #94A3B8;
            background: rgba(148, 163, 184, 0.08);
            box-shadow: 0 0 0 2px rgba(148, 163, 184, 0.2);
        }

        .dark .bz-config-card.selected-deny {
            border-color: #64748B;
            background: rgba(100, 116, 139, 0.15);
        }

        .bz-sound-config-row {
            background: var(--color-bg-subtle, rgba(0,0,0,0.03));
            border: 1.5px solid var(--color-border, #E2E8F0);
            border-radius: 1rem;
            padding: 0.85rem 0.95rem;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            box-sizing: border-box;
        }

        @media (min-width: 480px) {
            .bz-sound-config-row {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
                gap: 0.875rem;
                padding: 0.95rem 1.15rem;
            }
        }

        .dark .bz-sound-config-row {
            border-color: rgba(255, 255, 255, 0.08);
            background: rgba(255, 255, 255, 0.03);
        }

        .bz-sound-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.65rem;
            width: 100%;
        }

        @media (min-width: 480px) {
            .bz-sound-actions {
                justify-content: flex-end;
                width: auto;
            }
        }

        .bz-sound-toggle-track {
            width: 48px;
            height: 26px;
            border-radius: 9999px;
            background: #CBD5E1;
            position: relative;
            cursor: pointer;
            transition: background-color 0.25s;
            flex-shrink: 0;
        }

        .dark .bz-sound-toggle-track {
            background: #334155;
        }

        .bz-sound-toggle-track.active {
            background: linear-gradient(135deg, #0A4F78, #0284C7);
        }

        .bz-sound-toggle-thumb {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #FFFFFF;
            position: absolute;
            top: 3px;
            left: 3px;
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        [dir="rtl"] .bz-sound-toggle-thumb {
            left: auto;
            right: 3px;
        }

        .bz-sound-toggle-track.active .bz-sound-toggle-thumb {
            transform: translateX(22px);
        }

        [dir="rtl"] .bz-sound-toggle-track.active .bz-sound-toggle-thumb {
            transform: translateX(-22px);
        }
    </style>

    <div id="fcmPermissionModal" class="bz-fcm-modal-backdrop"
        onclick="if(event.target === this) closeFcmPermissionModal(3)">
        <div class="bz-fcm-modal-dialog" role="dialog" aria-modal="true">

            <!-- Top Brand Gradient Bar -->
            <div style="height: 6px; background: linear-gradient(90deg, #0A4F78 0%, #0284C7 50%, #10B981 100%); width: 100%;"></div>

            <!-- Close Cross Button -->
            <button type="button" onclick="closeFcmPermissionModal(3)"
                style="position: absolute; top: 1rem; {{ app()->getLocale() == 'ar' ? 'left: 1rem;' : 'right: 1rem;' }} background: rgba(0,0,0,0.06); border: none; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--color-text-muted); transition: all 0.2s;"
                onmouseover="this.style.background='rgba(0,0,0,0.12)'"
                onmouseout="this.style.background='rgba(0,0,0,0.06)'" aria-label="Close">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>

            <!-- STATE 1: Professional Notification & Sound Configurator -->
            <div id="fcmModalStatePrompt" class="bz-fcm-modal-body">
                <!-- Header with Animated Aura Icon -->
                <div style="display: flex; align-items: center; gap: 0.875rem; margin-bottom: 1.25rem;">
                    <div style="width: 46px; height: 46px; border-radius: 0.875rem; background: linear-gradient(135deg, rgba(10, 79, 120, 0.15), rgba(2, 132, 199, 0.25)); border: 1.5px solid rgba(2, 132, 199, 0.35); display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 8px 20px -4px rgba(2, 132, 199, 0.3);">
                        <i class="fa-solid fa-sliders text-xl text-sky-600 dark:text-sky-400"></i>
                    </div>
                    <div>
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                            <h3 style="font-size: 1.1rem; line-height: 1.25; font-weight: 900; color: var(--color-text); margin: 0;">
                                {{ app()->getLocale() == 'ar' ? 'إعدادات وتخصيص إشعارات النظام' : 'Notification & Sound Preferences' }}
                            </h3>
                        </div>
                        <p style="font-size: 0.775rem; color: var(--color-text-muted); margin: 0; line-height: 1.35;">
                            {{ app()->getLocale() == 'ar'
                                ? 'حدد خيارك لاستلام الإشعارات المنبثقة وتشغيل النغمة الصوتية للأوامر والمخزون.'
                                : 'Configure push alert permissions and audio chime feedback for live store events.' }}
                        </p>
                    </div>
                </div>

                <!-- Interactive Choices Container -->
                <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1.25rem;">

                    <!-- CHOICE 1: Allow Push Notifications (YES) -->
                    <div id="choiceCardAllow" class="bz-config-card selected-allow" onclick="selectNotificationChoice('allow')">
                        <div style="margin-top: 2px;">
                            <div id="radioIndicatorAllow" style="width: 20px; height: 20px; border-radius: 50%; border: 2px solid #0284C7; display: flex; align-items: center; justify-content: center; background: #0284C7; flex-shrink: 0;">
                                <div style="width: 8px; height: 8px; border-radius: 50%; background: #FFFFFF;"></div>
                            </div>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.35rem; margin-bottom: 0.25rem;">
                                <strong style="font-size: 0.875rem; color: var(--color-text); font-weight: 800; display: flex; align-items: center; gap: 0.4rem;">
                                    <i class="fa-solid fa-bell text-emerald-500"></i>
                                    <span>{{ app()->getLocale() == 'ar' ? 'نعم، السماح بالإشعارات الفورية (موصى به)' : 'Yes, Allow Real-Time Notifications' }}</span>
                                </strong>
                                <span class="badge badge-success text-[10px] font-bold" style="padding: 0.15rem 0.45rem;">
                                    {{ app()->getLocale() == 'ar' ? 'موصى به' : 'Recommended' }}
                                </span>
                            </div>
                            <div style="font-size: 0.75rem; color: var(--color-text-muted); line-height: 1.35;">
                                {{ app()->getLocale() == 'ar'
                                    ? 'استلام تنبيهات سطح المكتب اللحظية للطلبات الجديدة، وتنبيهات نفاد المخزون والتحويلات.'
                                    : 'Receive live desktop alerts for new orders, critical inventory warnings, and transfers.' }}
                            </div>
                        </div>
                    </div>

                    <!-- CHOICE 2: Do Not Allow / Mute (NOT ALLOW) -->
                    <div id="choiceCardDeny" class="bz-config-card" onclick="selectNotificationChoice('deny')">
                        <div style="margin-top: 2px;">
                            <div id="radioIndicatorDeny" style="width: 20px; height: 20px; border-radius: 50%; border: 2px solid #94A3B8; display: flex; align-items: center; justify-content: center; background: transparent; flex-shrink: 0;">
                                <div style="width: 8px; height: 8px; border-radius: 50%; background: transparent;"></div>
                            </div>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div style="margin-bottom: 0.25rem;">
                                <strong style="font-size: 0.875rem; color: var(--color-text); font-weight: 800; display: flex; align-items: center; gap: 0.4rem;">
                                    <i class="fa-solid fa-bell-slash text-slate-400"></i>
                                    <span>{{ app()->getLocale() == 'ar' ? 'عدم السماح / كتم الإشعارات المنبثقة' : 'Do Not Allow / Mute Push Alerts' }}</span>
                                </strong>
                            </div>
                            <div style="font-size: 0.75rem; color: var(--color-text-muted); line-height: 1.35;">
                                {{ app()->getLocale() == 'ar'
                                    ? 'تعطيل الإشعارات المنبثقة على هذا الجهاز. يمكنك مراجعة الإشعارات دائماً داخل جرس التنبيهات.'
                                    : 'Disable desktop popups on this browser. Notifications will still be visible in your top bell ledger.' }}
                            </div>
                        </div>
                    </div>

                    <!-- SOUND CONFIGURATION & TEST CHIME ROW -->
                    <div class="bz-sound-config-row">
                        <div style="display: flex; align-items: center; gap: 0.75rem; min-width: 0;">
                            <div style="width: 36px; height: 36px; border-radius: 0.75rem; background: rgba(2, 132, 199, 0.12); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i id="soundIconState" class="fa-solid fa-volume-high text-sky-600 dark:text-sky-400"></i>
                            </div>
                            <div style="min-width: 0;">
                                <strong style="font-size: 0.825rem; color: var(--color-text); font-weight: 800; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ app()->getLocale() == 'ar' ? 'النغمة والتنبيه الصوتي' : 'Notification Audio Chime' }}
                                </strong>
                                <span style="font-size: 0.725rem; color: var(--color-text-muted); line-height: 1.3; display: block;">
                                    {{ app()->getLocale() == 'ar' ? 'تشغيل رنة نقية عند حدوث أحداث جديدة' : 'Play an acoustic chime on incoming events' }}
                                </span>
                            </div>
                        </div>

                        <div class="bz-sound-actions">
                            <!-- Test Sound Button -->
                            <button type="button" onclick="playNotificationChime(true)"
                                style="padding: 0.4rem 0.75rem; border-radius: 0.6rem; font-size: 0.75rem; font-weight: 700; background: rgba(2, 132, 199, 0.1); color: #0284C7; border: 1px solid rgba(2, 132, 199, 0.25); cursor: pointer; display: flex; align-items: center; gap: 0.35rem; transition: all 0.2s;"
                                onmouseover="this.style.background='rgba(2, 132, 199, 0.2)'"
                                onmouseout="this.style.background='rgba(2, 132, 199, 0.1)'">
                                <i class="fa-solid fa-play text-[10px]"></i>
                                <span>{{ app()->getLocale() == 'ar' ? 'تجربة الصوت' : 'Test Chime' }}</span>
                            </button>

                            <!-- Sound Toggle Switch -->
                            <div id="soundToggleTrack" class="bz-sound-toggle-track active" onclick="toggleSoundPreference()" title="Toggle Sound">
                                <div class="bz-sound-toggle-thumb"></div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Actions -->
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <button type="button" id="btnApplyNotificationConfig" onclick="applyNotificationConfiguration()"
                        class="btn btn-primary"
                        style="width: 100%; padding: 0.85rem; font-size: 0.9rem; font-weight: 800; border-radius: 0.75rem; background: linear-gradient(135deg, #0A4F78, #0284C7); border: none; box-shadow: 0 4px 14px rgba(10, 79, 120, 0.35); display: flex; align-items: center; justify-content: center; gap: 0.5rem; cursor: pointer; transition: all 0.2s;">
                        <i class="fa-solid fa-circle-check"></i>
                        <span id="btnApplyNotificationText">{{ app()->getLocale() == 'ar' ? 'حفظ وتطبيق الإعدادات' : 'Save & Apply Preferences' }}</span>
                    </button>

                    <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 0.25rem;">
                        <button type="button" onclick="showBrowserConfigGuide()"
                            style="color: #0284C7; font-weight: 700; font-size: 0.775rem; text-decoration: none; padding: 0; background: none; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 0.35rem;">
                            <i class="fa-solid fa-circle-question"></i>
                            <span>{{ app()->getLocale() == 'ar' ? 'دليل إعدادات المتصفح' : 'Browser setup guide' }}</span>
                        </button>

                        <button type="button" onclick="closeFcmPermissionModal(7)"
                            style="color: var(--color-text-muted); font-size: 0.775rem; text-decoration: none; padding: 0; background: none; border: none; cursor: pointer;">
                            {{ app()->getLocale() == 'ar' ? 'إغلاق' : 'Close' }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- STATE 2: Illustrated Browser Configuration & Unblock Guide -->
            <div id="fcmModalStateGuide" style="display: none;" class="bz-fcm-modal-body">
                <!-- Header -->
                <div style="display: flex; align-items: center; gap: 0.875rem; margin-bottom: 1.25rem;">
                    <div style="width: 48px; height: 48px; border-radius: 0.875rem; background: rgba(234, 88, 12, 0.12); display: flex; align-items: center; justify-content: center; flex-shrink: 0; border: 1px solid rgba(234, 88, 12, 0.25);">
                        <i class="fa-solid fa-shield-halved text-amber-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 style="font-size: 1.2rem; font-weight: 900; color: var(--color-text); margin: 0;">
                            {{ app()->getLocale() == 'ar' ? 'تعديل إعدادات المتصفح لتفعيل الإشعارات' : 'Browser Settings Setup & Unblock Guide' }}
                        </h3>
                        <p style="font-size: 0.75rem; color: var(--color-text-muted); margin: 0.2rem 0 0 0;">
                            {{ app()->getLocale() == 'ar' ? '3 خطوات بسيطة للسماح بالإشعارات في المتصفح' : 'Follow 3 easy steps to allow notifications in Chrome / Edge / Firefox' }}
                        </p>
                    </div>
                </div>

                <!-- 3 Steps Visual Guide -->
                <div style="display: flex; flex-direction: column; gap: 0.875rem; margin-bottom: 1.75rem;">
                    <!-- Step 1 -->
                    <div style="display: flex; gap: 0.875rem; background: var(--color-bg-subtle, rgba(0,0,0,0.03)); padding: 0.875rem 1rem; border-radius: 0.875rem; border: 1px solid var(--color-border); align-items: flex-start;">
                        <div style="width: 26px; height: 26px; border-radius: 50%; background: #0A4F78; color: #FFFFFF; font-weight: 800; font-size: 0.75rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;">
                            1
                        </div>
                        <div style="flex: 1; font-size: 0.8125rem;">
                            <strong style="color: var(--color-text); display: block; margin-bottom: 0.2rem;">
                                {{ app()->getLocale() == 'ar' ? 'اضغط على أيقونة الإعدادات أو القفل' : 'Click the Settings or Lock Icon' }}
                            </strong>
                            <div style="color: var(--color-text-muted); line-height: 1.4; font-size: 0.75rem;">
                                {{ app()->getLocale() == 'ar'
                                    ? 'في أعلى المتصفح، اضغط على أيقونة عناصر التحكم بجوار رابط الموقع في شريط العناوين (أيقونة القفل أو أشرطة الإعدادات).'
                                    : 'In the address bar at the top, click the Lock icon or site controls button beside the URL.' }}
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 0.5rem; background: rgba(10, 79, 120, 0.1); color: #0A4F78; flex-shrink: 0;">
                            <i class="fa-solid fa-sliders"></i>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div style="display: flex; gap: 0.875rem; background: var(--color-bg-subtle, rgba(0,0,0,0.03)); padding: 0.875rem 1rem; border-radius: 0.875rem; border: 1px solid var(--color-border); align-items: flex-start;">
                        <div style="width: 26px; height: 26px; border-radius: 50%; background: #0A4F78; color: #FFFFFF; font-weight: 800; font-size: 0.75rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;">
                            2
                        </div>
                        <div style="flex: 1; font-size: 0.8125rem;">
                            <strong style="color: var(--color-text); display: block; margin-bottom: 0.2rem;">
                                {{ app()->getLocale() == 'ar' ? 'تغيير خيار الإشعارات إلى سماح' : 'Switch Notifications to "Allow"' }}
                            </strong>
                            <div style="color: var(--color-text-muted); line-height: 1.4; font-size: 0.75rem;">
                                {{ app()->getLocale() == 'ar'
                                    ? 'ابحث عن خيار "الإشعارات" (Notifications) وقم بتحويله من "حظر" إلى "سماح" (Allow).'
                                    : 'Find "Notifications" in site permissions and switch toggle from "Block" to "Allow".' }}
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 0.5rem; background: rgba(16, 185, 129, 0.1); color: #10B981; flex-shrink: 0;">
                            <i class="fa-solid fa-toggle-on text-lg"></i>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div style="display: flex; gap: 0.875rem; background: var(--color-bg-subtle, rgba(0,0,0,0.03)); padding: 0.875rem 1rem; border-radius: 0.875rem; border: 1px solid var(--color-border); align-items: flex-start;">
                        <div style="width: 26px; height: 26px; border-radius: 50%; background: #0A4F78; color: #FFFFFF; font-weight: 800; font-size: 0.75rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;">
                            3
                        </div>
                        <div style="flex: 1; font-size: 0.8125rem;">
                            <strong style="color: var(--color-text); display: block; margin-bottom: 0.2rem;">
                                {{ app()->getLocale() == 'ar' ? 'إعادة تحميل الصفحة الآن' : 'Reload the Page' }}
                            </strong>
                            <div style="color: var(--color-text-muted); line-height: 1.4; font-size: 0.75rem;">
                                {{ app()->getLocale() == 'ar'
                                    ? 'اضغط على زر إعادة التحميل بالأسفل لتطبيق الإعداد الجديد والبدء في استلام الإشعارات.'
                                    : 'Click reload page below to apply new browser permissions and connect FCM.' }}
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 0.5rem; background: rgba(2, 132, 199, 0.1); color: #0284C7; flex-shrink: 0;">
                            <i class="fa-solid fa-rotate-right"></i>
                        </div>
                    </div>
                </div>

                <!-- Guide Actions -->
                <div style="display: flex; gap: 0.75rem;">
                    <button type="button" onclick="window.location.reload()" class="btn btn-primary"
                        style="flex: 1; padding: 0.75rem; font-weight: 800; border-radius: 0.75rem; background: linear-gradient(135deg, #0A4F78, #0284C7); border: none; display: flex; align-items: center; justify-content: center; gap: 0.5rem; cursor: pointer;">
                        <i class="fa-solid fa-rotate-right"></i>
                        <span>{{ app()->getLocale() == 'ar' ? 'إعادة تحميل الصفحة الآن' : 'Reload Page Now' }}</span>
                    </button>
                    <button type="button" onclick="showFcmPermissionPrompt()" class="btn btn-secondary"
                        style="padding: 0.75rem 1.25rem; font-weight: 700; border-radius: 0.75rem; cursor: pointer;">
                        {{ app()->getLocale() == 'ar' ? 'العودة' : 'Back' }}
                    </button>
                </div>
            </div>

        </div>
    </div>

    <script>
        /* =========================================================================
           FCM Notification & Sound Preferences Manager (Web Audio Synthesizer)
           ========================================================================= */
        window.currentNotificationChoice = localStorage.getItem('bz_notifications_allowed') || 'allow';
        window.currentSoundEnabled = localStorage.getItem('bz_sound_enabled') !== '0';

        // Native High-Fidelity Web Audio Polyphonic Chime (D5 -> A5 -> D6)
        window.playNotificationChime = function (force = false) {
            if (!force && !window.isNotificationSoundEnabled()) {
                return;
            }

            try {
                const AudioContext = window.AudioContext || window.webkitAudioContext;
                if (!AudioContext) return;
                
                const ctx = new AudioContext();
                if (ctx.state === 'suspended') {
                    ctx.resume();
                }

                const now = ctx.currentTime;
                // Harmonic triad frequencies: D5 (587.33Hz), A5 (880.00Hz), D6 (1174.66Hz)
                const notes = [
                    { freq: 587.33, start: now, duration: 0.35, gain: 0.15 },
                    { freq: 880.00, start: now + 0.08, duration: 0.40, gain: 0.18 },
                    { freq: 1174.66, start: now + 0.16, duration: 0.60, gain: 0.22 }
                ];

                notes.forEach(n => {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();

                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(n.freq, n.start);

                    gain.gain.setValueAtTime(0, n.start);
                    gain.gain.linearRampToValueAtTime(n.gain, n.start + 0.02);
                    gain.gain.exponentialRampToValueAtTime(0.001, n.start + n.duration);

                    osc.connect(gain);
                    gain.connect(ctx.destination);

                    osc.start(n.start);
                    osc.stop(n.start + n.duration);
                });
            } catch (e) {
                console.warn('Audio chime note:', e);
            }
        };

        window.isNotificationSoundEnabled = function () {
            return localStorage.getItem('bz_sound_enabled') !== '0';
        };

        window.selectNotificationChoice = function (choice) {
            window.currentNotificationChoice = choice;
            const allowCard = document.getElementById('choiceCardAllow');
            const denyCard = document.getElementById('choiceCardDeny');
            const radioAllow = document.getElementById('radioIndicatorAllow');
            const radioDeny = document.getElementById('radioIndicatorDeny');
            const btnText = document.getElementById('btnApplyNotificationText');

            if (choice === 'allow') {
                if (allowCard) {
                    allowCard.className = 'bz-config-card selected-allow';
                }
                if (denyCard) {
                    denyCard.className = 'bz-config-card';
                }
                if (radioAllow) {
                    radioAllow.style.background = '#0284C7';
                    radioAllow.style.borderColor = '#0284C7';
                    radioAllow.firstElementChild.style.background = '#FFFFFF';
                }
                if (radioDeny) {
                    radioDeny.style.background = 'transparent';
                    radioDeny.style.borderColor = '#94A3B8';
                    radioDeny.firstElementChild.style.background = 'transparent';
                }
                if (btnText) {
                    btnText.textContent = '{{ app()->getLocale() == "ar" ? "نعم، تفعيل وحفظ الإعدادات" : "Allow & Save Preferences" }}';
                }
            } else {
                if (allowCard) {
                    allowCard.className = 'bz-config-card';
                }
                if (denyCard) {
                    denyCard.className = 'bz-config-card selected-deny';
                }
                if (radioAllow) {
                    radioAllow.style.background = 'transparent';
                    radioAllow.style.borderColor = '#94A3B8';
                    radioAllow.firstElementChild.style.background = 'transparent';
                }
                if (radioDeny) {
                    radioDeny.style.background = '#64748B';
                    radioDeny.style.borderColor = '#64748B';
                    radioDeny.firstElementChild.style.background = '#FFFFFF';
                }
                if (btnText) {
                    btnText.textContent = '{{ app()->getLocale() == "ar" ? "حفظ التفضيلات (كتم الإشعارات)" : "Save as Muted (Do Not Allow)" }}';
                }
            }
        };

        window.toggleSoundPreference = function () {
            window.currentSoundEnabled = !window.currentSoundEnabled;
            localStorage.setItem('bz_sound_enabled', window.currentSoundEnabled ? '1' : '0');
            updateSoundToggleUI();

            if (window.currentSoundEnabled) {
                window.playNotificationChime(true);
            }
        };

        function updateSoundToggleUI() {
            const track = document.getElementById('soundToggleTrack');
            const icon = document.getElementById('soundIconState');
            if (track) {
                if (window.currentSoundEnabled) {
                    track.classList.add('active');
                } else {
                    track.classList.remove('active');
                }
            }
            if (icon) {
                if (window.currentSoundEnabled) {
                    icon.className = 'fa-solid fa-volume-high text-sky-600 dark:text-sky-400';
                } else {
                    icon.className = 'fa-solid fa-volume-xmark text-slate-400';
                }
            }
        }

        window.openFcmPermissionModal = function () {
            const modal = document.getElementById('fcmPermissionModal');
            if (!modal) return;

            // Sync current stored choices into UI
            window.currentSoundEnabled = localStorage.getItem('bz_sound_enabled') !== '0';
            updateSoundToggleUI();
            selectNotificationChoice(localStorage.getItem('bz_notifications_allowed') === 'deny' ? 'deny' : 'allow');

            if (typeof Notification !== 'undefined' && Notification.permission === 'denied') {
                window.showBrowserConfigGuide();
            } else {
                window.showFcmPermissionPrompt();
            }

            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        };

        window.closeFcmPermissionModal = function (dismissDays = 0) {
            const modal = document.getElementById('fcmPermissionModal');
            if (modal) {
                modal.style.display = 'none';
                document.body.style.overflow = '';
            }
            if (dismissDays > 0) {
                const expiry = Date.now() + (dismissDays * 24 * 60 * 60 * 1000);
                localStorage.setItem('bz_fcm_dismissed_until', expiry.toString());
            }
        };

        window.showFcmPermissionPrompt = function () {
            const promptEl = document.getElementById('fcmModalStatePrompt');
            const guideEl = document.getElementById('fcmModalStateGuide');
            if (promptEl) promptEl.style.display = 'block';
            if (guideEl) guideEl.style.display = 'none';
        };

        window.showBrowserConfigGuide = function () {
            const promptEl = document.getElementById('fcmModalStatePrompt');
            const guideEl = document.getElementById('fcmModalStateGuide');
            if (promptEl) promptEl.style.display = 'none';
            if (guideEl) guideEl.style.display = 'block';
        };

        window.applyNotificationConfiguration = function () {
            const btn = document.getElementById('btnApplyNotificationConfig');
            const originalHtml = btn ? btn.innerHTML : '';
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>{{ app()->getLocale() == "ar" ? "جاري التطبيق..." : "Applying..." }}</span>';
            }

            localStorage.setItem('bz_notifications_allowed', window.currentNotificationChoice);
            localStorage.setItem('bz_sound_enabled', window.currentSoundEnabled ? '1' : '0');

            if (window.currentNotificationChoice === 'deny') {
                setTimeout(function () {
                    window.closeFcmPermissionModal(30);
                    showAdminToast(
                        '{{ app()->getLocale() == "ar" ? "تم حفظ التفضيلات" : "Preferences Saved" }}',
                        '{{ app()->getLocale() == "ar" ? "تم كتم الإشعارات المنبثقة على هذا الجهاز بنجاح." : "Desktop push popups muted for this device." }}',
                        'fa-solid fa-bell-slash text-slate-500'
                    );
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = originalHtml;
                    }
                }, 300);
                return;
            }

            // If user selected "Allow (Yes)"
            if (!('Notification' in window)) {
                alert('{{ app()->getLocale() == "ar" ? "متصفحك لا يدعم خاصية الإشعارات المكتبية." : "Your browser does not support desktop notifications." }}');
                window.closeFcmPermissionModal();
                return;
            }

            Notification.requestPermission().then(permission => {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }

                if (permission === 'granted') {
                    window.closeFcmPermissionModal();
                    if (window.currentSoundEnabled) {
                        window.playNotificationChime(true);
                    }
                    showAdminToast(
                        '{{ app()->getLocale() == "ar" ? "تم تفعيل الإشعارات بنجاح" : "Notifications Activated Successfully" }}',
                        '{{ app()->getLocale() == "ar" ? "أنت الآن متصل بنظام إشعارات بلو زون الفورية والصوتية." : "You are connected to BlueZone Realtime Audio & Push Alerts." }}',
                        'fa-solid fa-circle-check text-emerald-500'
                    );
                    if (window.initializeBluezoneFcm) {
                        window.initializeBluezoneFcm();
                    }
                } else if (permission === 'denied') {
                    window.showBrowserConfigGuide();
                } else {
                    window.closeFcmPermissionModal(3);
                }
            }).catch(err => {
                console.error('Permission request error:', err);
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
                window.closeFcmPermissionModal();
            });
        };

        window.activateFcmTokenDirectly = function (triggerBtn) {
            const originalHtml = triggerBtn ? triggerBtn.innerHTML : '';
            if (triggerBtn) {
                triggerBtn.disabled = true;
                triggerBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> {{ app()->getLocale() == "ar" ? "جاري الاتصال..." : "Connecting..." }}';
            }

            if (!('Notification' in window)) {
                alert('{{ app()->getLocale() == "ar" ? "متصفحك لا يدعم خاصية الإشعارات المكتبية." : "Your browser does not support desktop notifications." }}');
                if (triggerBtn) { triggerBtn.disabled = false; triggerBtn.innerHTML = originalHtml; }
                return;
            }

            if (!window.isSecureContext && window.location.hostname !== 'localhost' && window.location.hostname !== '127.0.0.1') {
                alert('{{ app()->getLocale() == "ar" ? "تنبيه: خاصية إشعارات FCM تشترط تشغيل الموقع عبر HTTPS أو localhost. إذا كنت تستخدم Laragon، يرجى تفعيل SSL أو استخدام HTTPS." : "Web Push requires HTTPS or http://localhost. Please use HTTPS." }}');
            }

            Notification.requestPermission().then(permission => {
                if (permission === 'granted') {
                    if (window.initializeBluezoneFcm) {
                        window.initializeBluezoneFcm(function (result) {
                            if (triggerBtn) {
                                triggerBtn.disabled = false;
                                triggerBtn.innerHTML = originalHtml;
                            }
                            if (result && result.error) {
                                alert(('{{ app()->getLocale() == "ar" ? "تنبيه FCM: " : "FCM Note: " }}') + result.error);
                            }
                        });
                    }
                } else if (permission === 'denied') {
                    if (triggerBtn) {
                        triggerBtn.disabled = false;
                        triggerBtn.innerHTML = originalHtml;
                    }
                    window.openFcmPermissionModal();
                    window.showBrowserConfigGuide();
                } else {
                    if (triggerBtn) {
                        triggerBtn.disabled = false;
                        triggerBtn.innerHTML = originalHtml;
                    }
                }
            }).catch(err => {
                console.error('Permission request error:', err);
                if (triggerBtn) {
                    triggerBtn.disabled = false;
                    triggerBtn.innerHTML = originalHtml;
                }
                alert('Permission error: ' + err.message);
            });
        };

        // Firebase Web SDK Client & Real-time Engine
        window.initializeBluezoneFcm = function (callback) {
            const firebaseConfig = {
                apiKey: "{{ \App\Models\Setting::get('fcm_api_key') ?: config('fcm.api_key') ?: config('services.firebase.api_key') }}",
                authDomain: "{{ \App\Models\Setting::get('fcm_auth_domain') ?: config('fcm.auth_domain') ?: config('services.firebase.auth_domain') }}",
                projectId: "{{ \App\Models\Setting::get('fcm_project_id') ?: config('fcm.project_id') ?: config('services.firebase.project_id') }}",
                storageBucket: "{{ \App\Models\Setting::get('fcm_storage_bucket') ?: config('fcm.storage_bucket') ?: config('services.firebase.storage_bucket') }}",
                messagingSenderId: "{{ \App\Models\Setting::get('fcm_messaging_sender_id') ?: config('fcm.messaging_sender_id') ?: config('services.firebase.messaging_sender_id') }}",
                appId: "{{ \App\Models\Setting::get('fcm_app_id') ?: config('fcm.app_id') ?: config('services.firebase.app_id') }}",
                measurementId: "{{ \App\Models\Setting::get('fcm_measurement_id') ?: config('fcm.measurement_id') ?: config('services.firebase.measurement_id') }}"
            };
            const vapidKey = "{{ \App\Models\Setting::get('fcm_vapid_key') ?: config('fcm.vapid_key') ?: config('services.firebase.vapid_key') }}";

            if (firebaseConfig.projectId && typeof firebase !== 'undefined' && 'serviceWorker' in navigator) {
                try {
                    if (!firebase.apps.length) {
                        firebase.initializeApp(firebaseConfig);
                    }
                    const messaging = firebase.messaging();

                    const swUrl = '/firebase-messaging-sw.js?projectId=' + encodeURIComponent(firebaseConfig.projectId)
                        + '&apiKey=' + encodeURIComponent(firebaseConfig.apiKey)
                        + '&authDomain=' + encodeURIComponent(firebaseConfig.authDomain || '')
                        + '&storageBucket=' + encodeURIComponent(firebaseConfig.storageBucket || '')
                        + '&messagingSenderId=' + encodeURIComponent(firebaseConfig.messagingSenderId)
                        + '&appId=' + encodeURIComponent(firebaseConfig.appId);

                    navigator.serviceWorker.register(swUrl).then(registration => {
                        if (Notification.permission === 'granted') {
                            if (!vapidKey) {
                                console.warn('[FCM Notice]: Web Push requires a VAPID Public Key. Generate it in Firebase Console > Project Settings > Cloud Messaging > Web Push certificates, then save it in Admin Settings.');
                            }
                            const tokenOpts = { serviceWorkerRegistration: registration };
                            if (vapidKey) tokenOpts.vapidKey = vapidKey;

                            messaging.getToken(tokenOpts).then(token => {
                                if (token) {
                                    window.currentAdminFcmToken = token;
                                    
                                    const ua = navigator.userAgent || '';
                                    let detectedBrowser = 'Chrome';
                                    if (ua.indexOf('Edg/') !== -1) detectedBrowser = 'Edge';
                                    else if (ua.indexOf('Firefox/') !== -1) detectedBrowser = 'Firefox';
                                    else if (ua.indexOf('Safari/') !== -1 && ua.indexOf('Chrome/') === -1) detectedBrowser = 'Safari';

                                    let detectedOs = 'Windows';
                                    if (ua.indexOf('Mac') !== -1) detectedOs = 'macOS';
                                    else if (ua.indexOf('Android') !== -1) detectedOs = 'Android';
                                    else if (ua.indexOf('iPhone') !== -1 || ua.indexOf('iPad') !== -1) detectedOs = 'iOS';
                                    else if (ua.indexOf('Linux') !== -1) detectedOs = 'Linux';

                                    const detectedType = (window.innerWidth <= 768 || /Android|iPhone|iPad/i.test(ua)) ? 'mobile' : 'desktop';

                                    fetch('/admin/notifications/fcm-token', {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                                            'Accept': 'application/json',
                                            'Content-Type': 'application/json'
                                        },
                                        body: JSON.stringify({
                                            fcm_token: token,
                                            device_type: detectedType,
                                            browser: detectedBrowser,
                                            os: detectedOs
                                        })
                                    }).then(res => res.json()).then(data => {
                                        const tokenContainer = document.getElementById('fcmTokenBadgeContainer');
                                        if (tokenContainer) {
                                            tokenContainer.innerHTML = `
                                                    <span class="badge badge-success text-xs font-bold" style="padding: 0.35rem 0.65rem;">
                                                        <i class="fa-solid fa-circle-check mr-1 ml-1"></i> {{ app()->getLocale() == 'ar' ? 'جهازك متصل بـ FCM' : 'Device FCM Linked' }}
                                                    </span>
                                                    <button type="button" class="btn btn-outline btn-xs" onclick="copyFcmToken('${token}')">
                                                        <i class="fa-regular fa-copy mr-1 ml-1"></i> {{ app()->getLocale() == 'ar' ? 'نسخ التوكن' : 'Copy Token' }}
                                                    </button>
                                                `;
                                        }
                                        // Only show toast if triggered manually by user action, avoid showing on every page refresh
                                        if (typeof callback === 'function') {
                                            showAdminToast(
                                                '{{ app()->getLocale() == "ar" ? "تم ربط توكن FCM بنجاح" : "FCM Device Token Linked" }}',
                                                '{{ app()->getLocale() == "ar" ? "متصفحك متصل الآن بالإشعارات السحابية الفورية." : "Your browser is now registered for live push notifications." }}',
                                                'fa-solid fa-circle-check text-emerald-500'
                                            );
                                            callback({ success: true, token: token });
                                        }
                                    }).catch(err => {
                                        console.warn('Token sync note:', err);
                                        if (typeof callback === 'function') callback({ success: false, error: err.message });
                                    });
                                }
                            }).catch(e => {
                                console.warn('FCM getToken note:', e);
                                if (!vapidKey) {
                                    console.warn('[FCM Setup Needed]: Please set fcm_vapid_key in Admin Settings.');
                                }
                                if (typeof callback === 'function') callback({ success: false, error: e.message });
                            });
                        }
                    });

                    // Foreground real-time message handler
                    messaging.onMessage(payload => {
                        console.log('[FCM Foreground Event]:', payload);
                        const title = (payload.notification && payload.notification.title) || (payload.data && payload.data.title) || 'BlueZone System Alert';
                        const body = (payload.notification && payload.notification.body) || (payload.data && payload.data.body) || '';
                        const actionUrl = (payload.data && payload.data.action_url) || '/admin';
                        const icon = (payload.data && payload.data.icon) || 'fa-solid fa-bell text-sky-500';

                        if (typeof window.isNotificationSoundEnabled === 'function' && window.isNotificationSoundEnabled()) {
                            window.playNotificationChime();
                        }

                        showAdminToast(title, body, icon, actionUrl);
                        prependNotificationToDropdown(title, body, icon, actionUrl);
                    });
                } catch (e) {
                    console.warn('Firebase client setup note:', e);
                }
            }
        };

        document.addEventListener('DOMContentLoaded', function () {
            // Initialize Firebase client
            window.initializeBluezoneFcm();

            // Auto-prompt permission modal if default and not dismissed recently
            if (typeof Notification !== 'undefined' && Notification.permission === 'default') {
                const userPref = localStorage.getItem('bz_notifications_allowed');
                const dismissedUntil = localStorage.getItem('bz_fcm_dismissed_until');
                const now = Date.now();
                if (userPref !== 'deny' && (!dismissedUntil || now > parseInt(dismissedUntil))) {
                    setTimeout(function () {
                        window.openFcmPermissionModal();
                    }, 1200);
                }
            }

            // Real-time Heartbeat Polling (Active sync fallback every 40s)
            let lastCheckedCount = parseInt(document.getElementById('adminNotificationBadge')?.textContent.trim()) || 0;
            setInterval(function () {
                fetch('/admin/notifications?ajax=1', {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(r => r.json())
                    .then(data => {
                        if (typeof data.unread_count !== 'undefined') {
                            if (data.unread_count > lastCheckedCount) {
                                const newest = (data.notifications && data.notifications.length > 0) ? data.notifications[0] : null;
                                if (newest) {
                                    if (typeof window.isNotificationSoundEnabled === 'function' && window.isNotificationSoundEnabled()) {
                                        window.playNotificationChime();
                                    }
                                    showAdminToast(newest.title, newest.message, newest.icon, newest.action_url);
                                    prependNotificationToDropdown(newest.title, newest.message, newest.icon, newest.action_url, newest.id);
                                }
                            }
                            lastCheckedCount = data.unread_count;
                            updateNotificationBadge(data.unread_count);
                        }
                    })
                    .catch(() => { });
            }, 40000);
        });
    </script>

    <!-- Global Admin Table Engine & Tools -->
    <script src="{{ asset('js/admin-table-tools.js') }}"></script>
    <script src="{{ asset('assets/js/mr-table-sort.js') }}"></script>
    <style>
        /* Table Sorting & Enhancements */
        table.table th[data-sort-dir="asc"],
        table.table th[data-sort-dir="desc"],
        table.table th.sorted-asc,
        table.table th.sorted-desc {
            background-color: rgba(10, 79, 120, 0.08) !important;
            color: #0284C7 !important;
        }
        .dark table.table th[data-sort-dir="asc"],
        .dark table.table th[data-sort-dir="desc"],
        .dark table.table th.sorted-asc,
        .dark table.table th.sorted-desc {
            background-color: rgba(2, 132, 199, 0.15) !important;
            color: #38BDF8 !important;
        }
        table.table th:hover .sort-indicator {
            opacity: 0.9 !important;
        }
        .bz-table-toolbar {
            transition: all 0.2s ease;
        }
        .btn-table-action {
            user-select: none;
            display: inline-flex;
            align-items: center;
        }
    </style>
</x-layouts.app>