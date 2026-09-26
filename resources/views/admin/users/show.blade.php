<x-layouts.admin 
    :pageTitle="$user['name'] ?? __('admin.users.title')" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'الملف التعريفي والوظيفي الشامل للموظف، سجل الحضور، ومؤشرات الأداء التخصصية' : 'Comprehensive staff profile, role-based operational insights, and attendance records.'"
    :breadcrumbs="[__('admin.menu.users') => route('admin.users.index'), ($user['name'] ?? 'User') => route('admin.users.show', $user['id'] ?? 1)]"
>
    <x-slot name="actions">
        <div class="flex items-center gap-2 flex-wrap">
            @if(empty($user['deleted_at']))
                <a href="{{ route('admin.users.edit', $user['id'] ?? 1) }}" class="btn btn-secondary font-bold text-xs sm:text-sm">
                    <i class="fa-solid fa-user-pen mr-1.5 ml-1.5"></i> {{ __('admin.users.edit_credentials') }}
                </a>

                @if(!empty($employee))
                    <button type="button" onclick="openAttendanceModal()" class="btn btn-primary font-bold text-xs sm:text-sm shadow-sm flex items-center gap-1.5 bg-[#0A4F78] hover:bg-[#062B49] text-white">
                        <i class="fa-solid fa-calendar-check"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'تسجيل حضور / انصراف' : 'Log Attendance' }}</span>
                    </button>
                @endif

                @if(auth()->id() !== ($user['id'] ?? 0))
                    @if(($user['status'] ?? 'active') === 'active')
                        <form method="POST" action="{{ route('admin.users.impersonate', $user['id'] ?? 1) }}" class="inline m-0 p-0" onsubmit="return confirm('{{ app()->getLocale() === 'ar' ? 'هل أنت متأكد من تسجيل الدخول كالموظف: ' . addslashes($user['name'] ?? 'User') . '؟' : 'Are you sure you want to login as staff user: ' . addslashes($user['name'] ?? 'User') . '?' }}')">
                            @csrf
                            <button type="submit" class="btn btn-secondary font-bold text-xs sm:text-sm text-sky-600 dark:text-sky-400 hover:text-sky-700 hover:bg-sky-50 dark:hover:bg-sky-950/40 border border-sky-300 dark:border-sky-800 cursor-pointer" title="{{ app()->getLocale() === 'ar' ? 'تسجيل الدخول كالموظف' : 'Login as Staff' }}">
                                <i class="fa-solid fa-arrow-right-to-bracket mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'تسجيل الدخول كالموظف' : 'Login as Staff' }}
                            </button>
                        </form>
                    @endif

                    <form method="POST" action="{{ route('admin.users.toggle-status', $user['id'] ?? 1) }}" class="inline m-0 p-0">
                        @csrf
                        @if(($user['status'] ?? 'active') === 'active')
                            <button type="submit" class="btn btn-secondary font-bold text-xs sm:text-sm text-amber-600 dark:text-amber-400 hover:text-amber-700 hover:bg-amber-50 dark:hover:bg-amber-950/40 border border-amber-300 dark:border-amber-800 cursor-pointer" title="{{ __('admin.users.suspend_title') }}">
                                <i class="fa-solid fa-user-lock mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'تعليق وتجميد الحساب' : 'Suspend Account' }}
                            </button>
                        @else
                            <button type="submit" class="btn btn-secondary font-bold text-xs sm:text-sm text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 border border-emerald-300 dark:border-emerald-800 cursor-pointer" title="{{ __('admin.users.activate_title') }}">
                                <i class="fa-solid fa-user-check mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'تنشيط وتفعيل الحساب' : 'Activate Account' }}
                            </button>
                        @endif
                    </form>
                @endif
            @endif

            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary font-bold text-xs sm:text-sm">
                <i class="fa-solid fa-arrow-left rtl:rotate-180 mr-1.5 ml-1.5"></i> {{ __('admin.users.all_users') }}
            </a>
        </div>
    </x-slot>

    <!-- 1. Hero Identity Banner (Blue Zone Brand Theme Adaptive) -->
    <div class="card p-6 mb-6 shadow-sm border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] rounded-2xl relative overflow-hidden transition-colors">
        <div class="absolute top-0 right-0 w-80 h-80 bg-gradient-to-br from-[#0A4F78]/15 via-[#2A8FC2]/10 to-transparent rounded-full blur-3xl pointer-events-none"></div>

        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 relative z-10">
            <!-- Left: Avatar & Profile Info -->
            <div class="flex items-center gap-5">
                <div class="relative flex-shrink-0">
                    <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-gradient-to-br from-[#0A4F78] via-[#2A8FC2] to-[#67B34A] text-white flex items-center justify-center font-extrabold text-2xl sm:text-3xl shadow-lg border-2 border-white dark:border-[#15456E]">
                        {{ strtoupper(substr($user['name'] ?? 'U', 0, 2)) }}
                    </div>
                    <span class="absolute -bottom-1.5 -right-1.5 w-6 h-6 rounded-full flex items-center justify-center border-2 border-white dark:border-[#062B49] {{ ($user['status'] ?? 'active') === 'active' ? 'bg-emerald-500 text-white' : 'bg-rose-500 text-white' }}" title="{{ ($user['status'] ?? 'active') === 'active' ? 'Active / Activated' : 'Suspended' }}">
                        <i class="fa-solid {{ ($user['status'] ?? 'active') === 'active' ? 'fa-check' : 'fa-xmark' }} text-[10px]"></i>
                    </span>
                </div>

                <div>
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white m-0 tracking-tight">
                            {{ $user['name'] ?? 'Staff Member' }}
                        </h1>
                        @if(!empty($employee?->first_name_ar))
                            <span class="text-sm font-semibold text-slate-500 dark:text-slate-400">
                                ({{ $employee->first_name_ar }} {{ $employee->last_name_ar }})
                            </span>
                        @endif

                        <!-- Activation Badge -->
                        @if(($user['status'] ?? 'active') === 'active')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/70 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                {{ app()->getLocale() === 'ar' ? 'حساب مفعّل نشط' : 'Active (Activated)' }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 dark:bg-rose-950/70 dark:text-rose-300 border border-rose-200 dark:border-rose-800">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                {{ app()->getLocale() === 'ar' ? 'حساب معلق ومجمد' : 'Suspended (Deactivated)' }}
                            </span>
                        @endif

                        <!-- Role Badge -->
                        <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-bold bg-sky-50 text-[#0A4F78] dark:bg-sky-950/70 dark:text-sky-300 border border-sky-200 dark:border-sky-800">
                            <i class="fa-solid fa-shield-halved text-[10px]"></i>
                            <span>{{ $user['role'] ?? 'Staff' }}</span>
                        </span>

                        @if(!empty($employee?->employee_number))
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-xs font-mono font-bold bg-slate-100 text-slate-700 dark:bg-[#031827] dark:text-slate-300 border border-slate-200 dark:border-[#15456E]">
                                <i class="fa-solid fa-id-badge text-slate-400"></i>
                                {{ $employee->employee_number }}
                            </span>
                        @endif
                    </div>

                    <!-- Meta Tags Row -->
                    <div class="mt-2 flex items-center gap-4 text-xs text-slate-500 dark:text-slate-400 flex-wrap">
                        <span class="flex items-center gap-1.5">
                            <i class="fa-regular fa-envelope text-slate-400"></i>
                            <span>{{ $user['email'] ?? '—' }}</span>
                        </span>
                        @if(!empty($user['phone']))
                            <span class="flex items-center gap-1.5">
                                <i class="fa-solid fa-phone text-slate-400"></i>
                                <span dir="ltr">{{ $user['phone'] }}</span>
                            </span>
                        @endif
                        @if(!empty($employee?->department))
                            <span class="flex items-center gap-1.5">
                                <i class="fa-solid fa-building-user text-slate-400"></i>
                                <span>{{ $employee->department->name }}</span>
                            </span>
                        @endif
                        @if(!empty($employee?->position))
                            <span class="flex items-center gap-1.5 text-[#0A4F78] dark:text-sky-400 font-semibold">
                                <i class="fa-solid fa-briefcase"></i>
                                <span>{{ $employee->position->title }}</span>
                            </span>
                        @endif
                        @if(!empty($user['area']))
                            <span class="flex items-center gap-1.5 text-sky-600 dark:text-sky-400 font-semibold">
                                <i class="fa-solid fa-map-location-dot"></i>
                                <span>{{ $user['area'] }} ({{ $user['city'] ?? '' }})</span>
                            </span>
                        @endif
                    </div>

                    @if(!empty($user['bio']))
                        <p class="mt-2 text-xs text-slate-600 dark:text-slate-300 max-w-2xl leading-relaxed">
                            {{ $user['bio'] }}
                        </p>
                    @endif
                </div>
            </div>

            <!-- Right: Quick Stat Chips -->
            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-2 gap-3 w-full lg:w-auto flex-shrink-0">
                <div class="p-3 rounded-xl bg-slate-50/80 dark:bg-[#031827]/70 border border-slate-200/80 dark:border-[#15456E]/80 text-center transition-colors">
                    <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase block tracking-wider">{{ app()->getLocale() === 'ar' ? 'نسبة الحضور' : 'Attendance Rate' }}</span>
                    <span class="text-xl font-black {{ $attendanceStats['attendance_rate'] >= 90 ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }} mt-0.5 block">
                        {{ $attendanceStats['attendance_rate'] }}%
                    </span>
                    <span class="text-[10px] text-slate-400 block">{{ $attendanceStats['total_days'] }} {{ app()->getLocale() === 'ar' ? 'يوم مسجل' : 'days logged' }}</span>
                </div>

                <div class="p-3 rounded-xl bg-slate-50/80 dark:bg-[#031827]/70 border border-slate-200/80 dark:border-[#15456E]/80 text-center transition-colors">
                    <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase block tracking-wider">{{ app()->getLocale() === 'ar' ? 'ساعات العمل' : 'Hours Worked' }}</span>
                    <span class="text-xl font-black text-[#0A4F78] dark:text-sky-400 mt-0.5 block">
                        {{ $attendanceStats['worked_hours'] }}h
                    </span>
                    <span class="text-[10px] text-slate-400 block">+{{ $attendanceStats['overtime_hours'] }}h {{ app()->getLocale() === 'ar' ? 'إضافي' : 'OT' }}</span>
                </div>

                @if($roleType === 'mr')
                    <div class="p-3 rounded-xl bg-slate-50/80 dark:bg-[#031827]/70 border border-slate-200/80 dark:border-[#15456E]/80 text-center transition-colors">
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase block tracking-wider">{{ app()->getLocale() === 'ar' ? 'أطباء المحفظة' : 'Assigned Docs' }}</span>
                        <span class="text-xl font-black text-sky-600 dark:text-sky-400 mt-0.5 block">
                            {{ $roleData['assigned_doctors'] ?? 0 }}
                        </span>
                        <span class="text-[10px] text-slate-400 block">{{ app()->getLocale() === 'ar' ? 'طبيب ومرفق' : 'doctors' }}</span>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50/80 dark:bg-[#031827]/70 border border-slate-200/80 dark:border-[#15456E]/80 text-center transition-colors">
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase block tracking-wider">{{ app()->getLocale() === 'ar' ? 'دقة الـ GPS' : 'GPS Accuracy' }}</span>
                        <span class="text-xl font-black text-emerald-600 dark:text-emerald-400 mt-0.5 block">
                            {{ $roleData['gps_rate'] ?? 100 }}%
                        </span>
                        <span class="text-[10px] text-slate-400 block">{{ $roleData['total_visits'] ?? 0 }} {{ app()->getLocale() === 'ar' ? 'زيارة' : 'visits' }}</span>
                    </div>
                @elseif($roleType === 'sales')
                    <div class="p-3 rounded-xl bg-slate-50/80 dark:bg-[#031827]/70 border border-slate-200/80 dark:border-[#15456E]/80 text-center transition-colors">
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase block tracking-wider">{{ app()->getLocale() === 'ar' ? 'العملاء المحتملون' : 'Active Leads' }}</span>
                        <span class="text-xl font-black text-sky-600 dark:text-sky-400 mt-0.5 block">
                            {{ $roleData['total_leads'] ?? 0 }}
                        </span>
                        <span class="text-[10px] text-slate-400 block">{{ app()->getLocale() === 'ar' ? 'فرص قيد المتابعة' : 'leads assigned' }}</span>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50/80 dark:bg-[#031827]/70 border border-slate-200/80 dark:border-[#15456E]/80 text-center transition-colors">
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase block tracking-wider">{{ app()->getLocale() === 'ar' ? 'الصفقات المكتملة' : 'Won Deals' }}</span>
                        <span class="text-xl font-black text-emerald-600 dark:text-emerald-400 mt-0.5 block">
                            {{ $roleData['won_opportunities'] ?? 0 }}
                        </span>
                        <span class="text-[10px] text-slate-400 block">{{ number_format($roleData['won_revenue'] ?? 0) }} SAR</span>
                    </div>
                @elseif($roleType === 'inventory')
                    <div class="p-3 rounded-xl bg-slate-50/80 dark:bg-[#031827]/70 border border-slate-200/80 dark:border-[#15456E]/80 text-center transition-colors">
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase block tracking-wider">{{ app()->getLocale() === 'ar' ? 'حركات المخزون' : 'Movements' }}</span>
                        <span class="text-xl font-black text-sky-600 dark:text-sky-400 mt-0.5 block">
                            {{ $roleData['total_movements'] ?? 0 }}
                        </span>
                        <span class="text-[10px] text-slate-400 block">{{ app()->getLocale() === 'ar' ? 'حركة مسجلة' : 'movements' }}</span>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50/80 dark:bg-[#031827]/70 border border-slate-200/80 dark:border-[#15456E]/80 text-center transition-colors">
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase block tracking-wider">{{ app()->getLocale() === 'ar' ? 'إجمالي الأصناف' : 'Active Catalog' }}</span>
                        <span class="text-xl font-black text-emerald-600 dark:text-emerald-400 mt-0.5 block">
                            {{ $roleData['active_products'] ?? 0 }}
                        </span>
                        <span class="text-[10px] text-slate-400 block">{{ app()->getLocale() === 'ar' ? 'منتج متاح' : 'active SKUs' }}</span>
                    </div>
                @else
                    <div class="p-3 rounded-xl bg-slate-50/80 dark:bg-[#031827]/70 border border-slate-200/80 dark:border-[#15456E]/80 text-center transition-colors">
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase block tracking-wider">{{ app()->getLocale() === 'ar' ? 'تاريخ التعيين' : 'Hire Date' }}</span>
                        <span class="text-sm font-bold text-slate-800 dark:text-slate-200 mt-1 block">
                            {{ $employee?->hire_date ? $employee->hire_date->format('d M Y') : 'N/A' }}
                        </span>
                        <span class="text-[10px] text-slate-400 block">{{ $employee?->employment_type ? ucfirst(str_replace('_', ' ', $employee->employment_type)) : 'Staff' }}</span>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50/80 dark:bg-[#031827]/70 border border-slate-200/80 dark:border-[#15456E]/80 text-center transition-colors">
                        <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase block tracking-wider">{{ app()->getLocale() === 'ar' ? 'نوع الحساب' : 'Account Type' }}</span>
                        <span class="text-sm font-bold text-[#0A4F78] dark:text-sky-400 mt-1 block">
                            {{ $user['role'] ?? 'Staff' }}
                        </span>
                        <span class="text-[10px] text-slate-400 block">ID #{{ $user['id'] }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- 2. Modern Tab Navigation Bar (Blue Zone Theme Adaptive) -->
    <div class="flex items-center gap-2 border-b border-slate-200 dark:border-[#15456E] mb-6 overflow-x-auto pb-1">
        <button type="button" onclick="switchStaffTab('role')" id="tab-btn-role" 
            class="staff-tab-btn inline-flex items-center gap-2 px-4 py-2.5 font-bold text-sm border-b-2 border-[#0A4F78] text-[#0A4F78] dark:border-sky-400 dark:text-sky-300 transition-all cursor-pointer">
            @if($roleType === 'mr')
                <i class="fa-solid fa-user-doctor"></i>
                <span>{{ app()->getLocale() === 'ar' ? 'عمليات المندوب الطبي (MR)' : 'Medical Rep Portfolio & Visits' }}</span>
            @elseif($roleType === 'sales')
                <i class="fa-solid fa-briefcase"></i>
                <span>{{ app()->getLocale() === 'ar' ? 'عمليات المبيعات (Sales CRM)' : 'Commercial Sales Portfolio' }}</span>
            @elseif($roleType === 'inventory')
                <i class="fa-solid fa-boxes-stacked"></i>
                <span>{{ app()->getLocale() === 'ar' ? 'عمليات المخزون والمستودع' : 'Warehouse & Inventory Ops' }}</span>
            @else
                <i class="fa-solid fa-chart-pie"></i>
                <span>{{ app()->getLocale() === 'ar' ? 'نظرة عامة والمسؤوليات' : 'Role Overview & Activities' }}</span>
            @endif
        </button>

        <button type="button" onclick="switchStaffTab('attendance')" id="tab-btn-attendance" 
            class="staff-tab-btn inline-flex items-center gap-2 px-4 py-2.5 font-bold text-sm border-b-2 border-transparent text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 transition-all cursor-pointer">
            <i class="fa-solid fa-clipboard-user"></i>
            <span>{{ app()->getLocale() === 'ar' ? 'سجل الحضور والانصراف (Attendance)' : 'Attendance & Time Clock' }}</span>
            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                {{ $attendanceStats['total_days'] }}
            </span>
        </button>

        <button type="button" onclick="switchStaffTab('hr')" id="tab-btn-hr" 
            class="staff-tab-btn inline-flex items-center gap-2 px-4 py-2.5 font-bold text-sm border-b-2 border-transparent text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 transition-all cursor-pointer">
            <i class="fa-solid fa-id-card-clip"></i>
            <span>{{ app()->getLocale() === 'ar' ? 'الملف الوظيفي (HR Dossier)' : 'HR Employee Dossier' }}</span>
            @if(!empty($employee))
                <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-sky-100 text-[#0A4F78] dark:bg-sky-950 dark:text-sky-300">
                    {{ $employee->employee_number }}
                </span>
            @endif
        </button>

        <button type="button" onclick="switchStaffTab('security')" id="tab-btn-security" 
            class="staff-tab-btn inline-flex items-center gap-2 px-4 py-2.5 font-bold text-sm border-b-2 border-transparent text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 transition-all cursor-pointer">
            <i class="fa-solid fa-key"></i>
            <span>{{ app()->getLocale() === 'ar' ? 'الصلاحيات والأمان (Permissions)' : 'Security & Permissions' }}</span>
        </button>
    </div>

    <!-- ========================================== -->
    <!-- TAB 1: ROLE-SPECIFIC OPERATIONS (DYNAMIC) -->
    <!-- ========================================== -->
    <div id="tab-content-role" class="staff-tab-panel">
        @if($roleType === 'mr')
            <!-- Medical Representative Operations -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="card p-4 border border-teal-200/80 dark:border-teal-900/50 bg-teal-50/40 dark:bg-[#062B49] rounded-2xl shadow-sm transition-colors">
                    <span class="text-xs font-bold text-teal-700 dark:text-teal-400 uppercase tracking-wider block mb-1">
                        {{ app()->getLocale() === 'ar' ? 'أطباء المحفظة المكلف بها' : 'Assigned Doctor Portfolio' }}
                    </span>
                    <div class="flex items-baseline justify-between">
                        <span class="text-2xl font-black text-slate-900 dark:text-white">{{ $roleData['assigned_doctors'] }}</span>
                        <i class="fa-solid fa-stethoscope text-teal-500 dark:text-teal-400 text-xl"></i>
                    </div>
                    <span class="text-[11px] text-slate-500 dark:text-slate-400 mt-2 block">{{ app()->getLocale() === 'ar' ? 'عيادات ومراكز طبية مسندة' : 'Active medical contacts assigned' }}</span>
                </div>

                <div class="card p-4 border border-sky-200/80 dark:border-sky-900/50 bg-sky-50/40 dark:bg-[#062B49] rounded-2xl shadow-sm transition-colors">
                    <span class="text-xs font-bold text-[#0A4F78] dark:text-sky-400 uppercase tracking-wider block mb-1">
                        {{ app()->getLocale() === 'ar' ? 'إجمالي الزيارات المنفذة' : 'Total Executed Visits' }}
                    </span>
                    <div class="flex items-baseline justify-between">
                        <span class="text-2xl font-black text-slate-900 dark:text-white">{{ $roleData['total_visits'] }}</span>
                        <i class="fa-solid fa-route text-sky-500 dark:text-sky-400 text-xl"></i>
                    </div>
                    <span class="text-[11px] text-slate-500 dark:text-slate-400 mt-2 block">{{ $roleData['gps_verified_visits'] }} {{ app()->getLocale() === 'ar' ? 'مؤكدة جغرافياً (GPS)' : 'verified in radius' }}</span>
                </div>

                <div class="card p-4 border border-emerald-200/80 dark:border-emerald-900/50 bg-emerald-50/40 dark:bg-[#062B49] rounded-2xl shadow-sm transition-colors">
                    <span class="text-xs font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider block mb-1">
                        {{ app()->getLocale() === 'ar' ? 'دقة الـ GPS الميدانية' : 'GPS Verification Rate' }}
                    </span>
                    <div class="flex items-baseline justify-between">
                        <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ $roleData['gps_rate'] }}%</span>
                        <i class="fa-solid fa-satellite-dish text-emerald-500 dark:text-emerald-400 text-xl"></i>
                    </div>
                    <span class="text-[11px] text-emerald-700 dark:text-emerald-400 font-semibold mt-2 block">{{ app()->getLocale() === 'ar' ? 'تسجيلات وصول داخل نطاق العيادة' : 'Within clinic geofence' }}</span>
                </div>

                <div class="card p-4 border border-amber-200/80 dark:border-amber-900/50 bg-amber-50/40 dark:bg-[#062B49] rounded-2xl shadow-sm transition-colors">
                    <span class="text-xs font-bold text-amber-700 dark:text-amber-400 uppercase tracking-wider block mb-1">
                        {{ app()->getLocale() === 'ar' ? 'زيارات اليوم المجدولة' : "Today's Planned Visits" }}
                    </span>
                    <div class="flex items-baseline justify-between">
                        <span class="text-2xl font-black text-amber-600 dark:text-amber-400">{{ $roleData['today_scheduled'] }}</span>
                        <i class="fa-solid fa-calendar-day text-amber-500 dark:text-amber-400 text-xl"></i>
                    </div>
                    <span class="text-[11px] text-slate-500 dark:text-slate-400 mt-2 block">{{ app()->getLocale() === 'ar' ? 'جاهزة للتنفيذ ببوابة المندوب' : 'Ready in MR daily portal' }}</span>
                </div>
            </div>

            <!-- MR Quick Shortcuts & Recent Visits -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Recent Executed Visits Table (2 Cols) -->
                <div class="lg:col-span-2 card p-0 overflow-hidden shadow-sm border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] rounded-2xl transition-colors">
                    <div class="p-4 border-b border-slate-200/80 dark:border-[#15456E] flex items-center justify-between bg-slate-50/80 dark:bg-[#031827]/60">
                        <h3 class="font-bold text-sm text-slate-900 dark:text-white flex items-center gap-2">
                            <i class="fa-solid fa-clock-rotate-left text-sky-500"></i>
                            {{ app()->getLocale() === 'ar' ? 'آخر الزيارات الميدانية المنفذة بواسطة المندوب' : 'Recent Field Visits Logged by Representative' }}
                        </h3>
                        <a href="{{ route('admin.mr.visits.index', ['mr_id' => $user['id'], 'tab' => 'history']) }}" class="text-xs font-bold text-[#0A4F78] dark:text-sky-400 hover:underline">
                            {{ app()->getLocale() === 'ar' ? 'عرض السجل الكامل ←' : 'View All Logged Visits →' }}
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm table">
                            <thead class="bg-slate-50 dark:bg-[#031827]/80 text-slate-700 dark:text-slate-300 font-bold border-b border-slate-200 dark:border-[#15456E] text-xs">
                                <tr>
                                    <th class="px-4 py-3">{{ app()->getLocale() === 'ar' ? 'الطبيب' : 'Doctor' }}</th>
                                    <th class="px-4 py-3">{{ app()->getLocale() === 'ar' ? 'التخصص والفئة' : 'Specialty & Class' }}</th>
                                    <th class="px-4 py-3">{{ app()->getLocale() === 'ar' ? 'التوقيت' : 'Time & Date' }}</th>
                                    <th class="px-4 py-3 text-center">{{ app()->getLocale() === 'ar' ? 'حالة الـ GPS' : 'GPS' }}</th>
                                    <th class="px-4 py-3 text-center">{{ app()->getLocale() === 'ar' ? 'النتيجة' : 'Outcome' }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-[#15456E]/60 text-xs">
                                @forelse($roleData['recent_visits'] ?? [] as $v)
                                    <tr class="hover:bg-slate-50/60 dark:hover:bg-[#031827]/40 transition-colors">
                                        <td class="px-4 py-3 font-bold text-slate-900 dark:text-white">
                                            <div class="flex items-center gap-2">
                                                <div class="w-7 h-7 rounded-full bg-sky-100 text-[#0A4F78] dark:bg-sky-950 dark:text-sky-300 flex items-center justify-center font-bold text-[10px]">
                                                    {{ strtoupper(substr($v->contact?->name ?? 'Dr', 0, 2)) }}
                                                </div>
                                                <div>
                                                    <span class="block">{{ $v->contact?->name ?? 'Doctor' }}</span>
                                                    <span class="text-[10px] text-slate-400">{{ $v->contact?->workplace_name }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="badge badge-outline text-[10px]">{{ $v->contact?->specialty?->name ?? 'General' }}</span>
                                            @if($v->contact?->classification)
                                                <span class="badge badge-accent text-[10px] font-bold">{{ $v->contact->classification->code }}</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-slate-500 dark:text-slate-400">
                                            <div>{{ $v->checkin_at ? $v->checkin_at->format('d M Y') : '—' }}</div>
                                            <div class="text-[10px] text-slate-400">{{ $v->checkin_at ? $v->checkin_at->format('h:i A') : '' }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            @if($v->gps_verified)
                                                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-600 dark:text-emerald-400">
                                                    <i class="fa-solid fa-satellite-dish"></i> {{ app()->getLocale() === 'ar' ? 'مؤكد' : 'Verified' }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-slate-400">
                                                    <i class="fa-solid fa-location-dot"></i> {{ app()->getLocale() === 'ar' ? 'يدوي' : 'Manual' }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="badge badge-success text-[10px]">{{ ucfirst($v->outcome ?: 'Completed') }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-slate-400">
                                            <i class="fa-solid fa-calendar-xmark text-2xl mb-1.5 block"></i>
                                            {{ app()->getLocale() === 'ar' ? 'لا توجد زيارات مسجلة لهذا المندوب حتى الآن.' : 'No visits recorded yet for this representative.' }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Right Card: MR Performance & Quick Links -->
                <div class="space-y-4">
                    <div class="card p-5 shadow-sm border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] rounded-2xl transition-colors">
                        <h4 class="font-bold text-sm text-slate-900 dark:text-white mb-3 flex items-center justify-between">
                            <span class="flex items-center gap-2">
                                <i class="fa-solid fa-bullseye text-[#0A4F78] dark:text-sky-400"></i>
                                {{ app()->getLocale() === 'ar' ? 'بطاقة إنجاز الدورة الحالية' : 'Cycle Target Scorecard' }}
                            </span>
                            <span class="text-xs font-mono px-2 py-0.5 rounded bg-sky-100 dark:bg-sky-950 text-[#0A4F78] dark:text-sky-300 font-bold">
                                {{ $roleData['active_cycle'] ?? 'Cycle 2026' }}
                            </span>
                        </h4>

                        <div class="space-y-3">
                            <div>
                                <div class="flex items-center justify-between text-xs mb-1">
                                    <span class="text-slate-500 dark:text-slate-400">{{ app()->getLocale() === 'ar' ? 'النقاط المحققة' : 'Points Achieved' }}</span>
                                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ $roleData['achieved_points'] }} / {{ $roleData['target_points'] ?: 100 }}</span>
                                </div>
                                <div class="w-full bg-slate-100 dark:bg-[#031827] rounded-full h-2 overflow-hidden border border-slate-200/60 dark:border-[#15456E]">
                                    @php
                                        $target = max(1, (int)($roleData['target_points'] ?? 100));
                                        $achieved = (int)($roleData['achieved_points'] ?? 0);
                                        $pct = min(100, round(($achieved / $target) * 100));
                                    @endphp
                                    <div class="bg-gradient-to-r from-[#0A4F78] to-[#2A8FC2] h-2 rounded-full transition-all" style="width: {{ $pct }}%"></div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-100 dark:border-[#15456E] text-center">
                                <div class="p-2 rounded-lg bg-slate-50/80 dark:bg-[#031827]/70">
                                    <span class="text-[10px] text-slate-500 dark:text-slate-400 block">{{ app()->getLocale() === 'ar' ? 'معدل التغطية' : 'Coverage' }}</span>
                                    <span class="text-sm font-black text-teal-600 dark:text-teal-400">{{ $roleData['coverage_rate'] ?? 0 }}%</span>
                                </div>
                                <div class="p-2 rounded-lg bg-slate-50/80 dark:bg-[#031827]/70">
                                    <span class="text-[10px] text-slate-500 dark:text-slate-400 block">{{ app()->getLocale() === 'ar' ? 'الإنجاز' : 'Target %' }}</span>
                                    <span class="text-sm font-black text-[#0A4F78] dark:text-sky-400">{{ $pct }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- MR Action Links -->
                    <div class="card p-4 shadow-sm border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] rounded-2xl transition-colors space-y-2">
                        <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block mb-2">{{ app()->getLocale() === 'ar' ? 'روابط عمليات المندوب السريعة' : 'Quick Operations Shortcuts' }}</span>
                        
                        <a href="{{ route('admin.mr.dashboard') }}" class="w-full flex items-center justify-between p-2.5 rounded-xl bg-slate-50 hover:bg-sky-50 dark:bg-[#031827]/70 dark:hover:bg-[#031827] text-xs font-bold text-slate-800 dark:text-slate-200 border border-slate-200/80 dark:border-[#15456E] transition-all">
                            <span class="flex items-center gap-2">
                                <i class="fa-solid fa-calendar-days text-[#0A4F78] dark:text-sky-400"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'فتح تقويم جدول المندوب' : 'Open CRM Rep Calendar' }}</span>
                            </span>
                            <i class="fa-solid fa-arrow-right rtl:rotate-180 text-slate-400"></i>
                        </a>

                        <a href="{{ route('admin.mr.assignments.index', ['mr_id' => $user['id']]) }}" class="w-full flex items-center justify-between p-2.5 rounded-xl bg-slate-50 hover:bg-sky-50 dark:bg-[#031827]/70 dark:hover:bg-[#031827] text-xs font-bold text-slate-800 dark:text-slate-200 border border-slate-200/80 dark:border-[#15456E] transition-all">
                            <span class="flex items-center gap-2">
                                <i class="fa-solid fa-user-check text-teal-600 dark:text-teal-400"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'إدارة وتعيين أطباء المحفظة' : 'Manage Doctor Assignments' }}</span>
                            </span>
                            <i class="fa-solid fa-arrow-right rtl:rotate-180 text-slate-400"></i>
                        </a>

                        <a href="{{ route('admin.mr.live-map') }}" class="w-full flex items-center justify-between p-2.5 rounded-xl bg-slate-50 hover:bg-sky-50 dark:bg-[#031827]/70 dark:hover:bg-[#031827] text-xs font-bold text-slate-800 dark:text-slate-200 border border-slate-200/80 dark:border-[#15456E] transition-all">
                            <span class="flex items-center gap-2">
                                <i class="fa-solid fa-earth-americas text-emerald-600 dark:text-emerald-400"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'خريطة العمليات المباشرة (GPS)' : 'Live Field GPS Map' }}</span>
                            </span>
                            <i class="fa-solid fa-arrow-right rtl:rotate-180 text-slate-400"></i>
                        </a>
                    </div>
                </div>
            </div>

        @elseif($roleType === 'sales')
            <!-- Commercial Sales Operations -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="card p-4 border border-sky-200/80 dark:border-sky-900/50 bg-sky-50/40 dark:bg-[#062B49] rounded-2xl shadow-sm transition-colors">
                    <span class="text-xs font-bold text-[#0A4F78] dark:text-sky-400 uppercase tracking-wider block mb-1">
                        {{ app()->getLocale() === 'ar' ? 'العملاء المحتملون (Leads)' : 'Assigned Leads' }}
                    </span>
                    <div class="flex items-baseline justify-between">
                        <span class="text-2xl font-black text-slate-900 dark:text-white">{{ $roleData['total_leads'] }}</span>
                        <i class="fa-solid fa-funnel-dollar text-sky-500 text-xl"></i>
                    </div>
                    <span class="text-[11px] text-slate-500 dark:text-slate-400 mt-2 block">{{ app()->getLocale() === 'ar' ? 'عملاء قيد التفاوض والمتابعة' : 'Leads in sales pipeline' }}</span>
                </div>

                <div class="card p-4 border border-teal-200/80 dark:border-teal-900/50 bg-teal-50/40 dark:bg-[#062B49] rounded-2xl shadow-sm transition-colors">
                    <span class="text-xs font-bold text-teal-700 dark:text-teal-400 uppercase tracking-wider block mb-1">
                        {{ app()->getLocale() === 'ar' ? 'الصفقات المفتوحة' : 'Active Deals / Opportunities' }}
                    </span>
                    <div class="flex items-baseline justify-between">
                        <span class="text-2xl font-black text-slate-900 dark:text-white">{{ $roleData['total_opportunities'] }}</span>
                        <i class="fa-solid fa-handshake text-teal-500 text-xl"></i>
                    </div>
                    <span class="text-[11px] text-slate-500 dark:text-slate-400 mt-2 block">{{ app()->getLocale() === 'ar' ? 'عروض أسعار جارية' : 'Proposals in progress' }}</span>
                </div>

                <div class="card p-4 border border-emerald-200/80 dark:border-emerald-900/50 bg-emerald-50/40 dark:bg-[#062B49] rounded-2xl shadow-sm transition-colors">
                    <span class="text-xs font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider block mb-1">
                        {{ app()->getLocale() === 'ar' ? 'الصفقات المربوحة' : 'Won Deals' }}
                    </span>
                    <div class="flex items-baseline justify-between">
                        <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ $roleData['won_opportunities'] }}</span>
                        <i class="fa-solid fa-trophy text-emerald-500 text-xl"></i>
                    </div>
                    <span class="text-[11px] text-emerald-600 dark:text-emerald-400 font-semibold mt-2 block">{{ app()->getLocale() === 'ar' ? 'إجمالي المحقق بنجاح' : 'Closed won accounts' }}</span>
                </div>

                <div class="card p-4 border border-amber-200/80 dark:border-amber-900/50 bg-amber-50/40 dark:bg-[#062B49] rounded-2xl shadow-sm transition-colors">
                    <span class="text-xs font-bold text-amber-700 dark:text-amber-400 uppercase tracking-wider block mb-1">
                        {{ app()->getLocale() === 'ar' ? 'قيمة الصفقات المغلقة' : 'Won Deals Revenue' }}
                    </span>
                    <div class="flex items-baseline justify-between">
                        <span class="text-xl font-black text-amber-600 dark:text-amber-400">{{ number_format($roleData['won_revenue']) }} SAR</span>
                        <i class="fa-solid fa-money-bill-trend-up text-amber-500 text-xl"></i>
                    </div>
                    <span class="text-[11px] text-slate-500 dark:text-slate-400 mt-2 block">{{ app()->getLocale() === 'ar' ? 'إجمالي المبيعات' : 'Confirmed sales' }}</span>
                </div>
            </div>

            <!-- Sales Recent Leads -->
            <div class="card p-0 overflow-hidden shadow-sm border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] rounded-2xl mb-8 transition-colors">
                <div class="p-4 border-b border-slate-200/80 dark:border-[#15456E] flex items-center justify-between bg-slate-50/80 dark:bg-[#031827]/60">
                    <h3 class="font-bold text-sm text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-users-line text-sky-500"></i>
                        {{ app()->getLocale() === 'ar' ? 'قائمة العملاء المحتملين المسندين للموظف' : 'Assigned Commercial Leads' }}
                    </h3>
                    <a href="{{ route('admin.crm.leads.index') }}" class="btn btn-secondary btn-sm text-xs font-bold">
                        {{ app()->getLocale() === 'ar' ? 'فتح نظام المبيعات CRM ←' : 'Open Sales CRM →' }}
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm table">
                        <thead class="bg-slate-50 dark:bg-[#031827]/80 text-slate-700 dark:text-slate-300 font-bold border-b border-slate-200 dark:border-[#15456E] text-xs">
                            <tr>
                                <th class="px-4 py-3">{{ app()->getLocale() === 'ar' ? 'اسم العميل / الشركة' : 'Lead / Company' }}</th>
                                <th class="px-4 py-3">{{ app()->getLocale() === 'ar' ? 'المصدر' : 'Source' }}</th>
                                <th class="px-4 py-3 text-center">{{ app()->getLocale() === 'ar' ? 'الدرجة' : 'Score' }}</th>
                                <th class="px-4 py-3 text-center">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                                <th class="px-4 py-3">{{ app()->getLocale() === 'ar' ? 'تاريخ الإسناد' : 'Assigned Date' }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-[#15456E]/60 text-xs">
                            @forelse($roleData['recent_leads'] ?? [] as $lead)
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-[#031827]/40 transition-colors">
                                    <td class="px-4 py-3 font-bold text-slate-900 dark:text-white">
                                        <div>{{ $lead->name }}</div>
                                        <div class="text-[10px] text-slate-400">{{ $lead->company ?: ($lead->phone ?: $lead->email) }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ ucfirst($lead->source ?: 'Direct') }}</td>
                                    <td class="px-4 py-3 text-center font-bold text-[#0A4F78] dark:text-sky-400">{{ $lead->score ?? 50 }}</td>
                                    <td class="px-4 py-3 text-center"><span class="badge badge-accent text-[10px]">{{ ucfirst($lead->status) }}</span></td>
                                    <td class="px-4 py-3 text-slate-500 dark:text-slate-400">{{ $lead->created_at ? $lead->created_at->format('d M Y') : '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-slate-400">
                                        {{ app()->getLocale() === 'ar' ? 'لا توجد فرص بيعية مسندة لهذا الموظف.' : 'No commercial leads assigned to this staff member.' }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        @elseif($roleType === 'inventory')
            <!-- Inventory & Logistics Operations -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="card p-4 border border-teal-200/80 dark:border-teal-900/50 bg-teal-50/40 dark:bg-[#062B49] rounded-2xl shadow-sm transition-colors">
                    <span class="text-xs font-bold text-teal-700 dark:text-teal-400 uppercase tracking-wider block mb-1">
                        {{ app()->getLocale() === 'ar' ? 'حركات المخزون المسجلة' : 'Stock Movements' }}
                    </span>
                    <div class="flex items-baseline justify-between">
                        <span class="text-2xl font-black text-slate-900 dark:text-white">{{ $roleData['total_movements'] }}</span>
                        <i class="fa-solid fa-dolly text-teal-500 text-xl"></i>
                    </div>
                    <span class="text-[11px] text-slate-500 dark:text-slate-400 mt-2 block">{{ app()->getLocale() === 'ar' ? 'عمليات إدخال وإخراج وتوريد' : 'Inward & outward transactions' }}</span>
                </div>

                <div class="card p-4 border border-emerald-200/80 dark:border-emerald-900/50 bg-emerald-50/40 dark:bg-[#062B49] rounded-2xl shadow-sm transition-colors">
                    <span class="text-xs font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider block mb-1">
                        {{ app()->getLocale() === 'ar' ? 'توريد واستلام (Received)' : 'Inward Adjustments' }}
                    </span>
                    <div class="flex items-baseline justify-between">
                        <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ $roleData['inward_count'] }}</span>
                        <i class="fa-solid fa-arrow-down text-emerald-500 text-xl"></i>
                    </div>
                    <span class="text-[11px] text-emerald-600 dark:text-emerald-400 font-semibold mt-2 block">{{ app()->getLocale() === 'ar' ? 'حركات إضافة للمستودع' : 'Stock additions' }}</span>
                </div>

                <div class="card p-4 border border-sky-200/80 dark:border-sky-900/50 bg-sky-50/40 dark:bg-[#062B49] rounded-2xl shadow-sm transition-colors">
                    <span class="text-xs font-bold text-[#0A4F78] dark:text-sky-400 uppercase tracking-wider block mb-1">
                        {{ app()->getLocale() === 'ar' ? 'أصناف الكتالوج النشطة' : 'Active Catalog SKUs' }}
                    </span>
                    <div class="flex items-baseline justify-between">
                        <span class="text-2xl font-black text-slate-900 dark:text-white">{{ $roleData['active_products'] }}</span>
                        <i class="fa-solid fa-boxes-packing text-sky-500 text-xl"></i>
                    </div>
                    <span class="text-[11px] text-slate-500 dark:text-slate-400 mt-2 block">{{ app()->getLocale() === 'ar' ? 'منتجات بمستودعات الشركة' : 'SKUs tracked across locations' }}</span>
                </div>

                <div class="card p-4 border border-slate-200/80 dark:border-[#15456E] bg-slate-50/40 dark:bg-[#062B49] rounded-2xl shadow-sm transition-colors">
                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider block mb-1">
                        {{ app()->getLocale() === 'ar' ? 'النطاق والموقع' : 'Territory / Location' }}
                    </span>
                    <div class="flex items-baseline justify-between">
                        <span class="text-lg font-bold text-slate-900 dark:text-white">{{ $user['area'] ?: ($user['city'] ?: 'Central Hub') }}</span>
                        <i class="fa-solid fa-warehouse text-slate-400 text-xl"></i>
                    </div>
                    <span class="text-[11px] text-slate-500 dark:text-slate-400 mt-2 block">{{ app()->getLocale() === 'ar' ? 'المستودع الرئيسي' : 'Assigned logistics center' }}</span>
                </div>
            </div>

            <div class="card p-5 shadow-sm border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] rounded-2xl mb-8 transition-colors">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-sm text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-arrows-split-up-and-left text-teal-500"></i>
                        {{ app()->getLocale() === 'ar' ? 'سجل حركات الأصناف والمستودع الأخيرة' : 'Recent Stock Adjustments & Movements' }}
                    </h3>
                    <a href="{{ route('admin.inventory.index') }}" class="btn btn-secondary btn-sm text-xs font-bold">
                        {{ app()->getLocale() === 'ar' ? 'فتح مركز المخزون ←' : 'Open Inventory Hub →' }}
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm table">
                        <thead class="bg-slate-50 dark:bg-[#031827]/80 text-slate-700 dark:text-slate-300 font-bold border-b border-slate-200 dark:border-[#15456E] text-xs">
                            <tr>
                                <th class="px-4 py-2.5">{{ app()->getLocale() === 'ar' ? 'المنتج' : 'Product' }}</th>
                                <th class="px-4 py-2.5">{{ app()->getLocale() === 'ar' ? 'النوع' : 'Movement' }}</th>
                                <th class="px-4 py-2.5 text-center">{{ app()->getLocale() === 'ar' ? 'الكمية' : 'Quantity' }}</th>
                                <th class="px-4 py-2.5">{{ app()->getLocale() === 'ar' ? 'التاريخ' : 'Date' }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-[#15456E]/60 text-xs">
                            @forelse($roleData['recent_movements'] ?? [] as $m)
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-[#031827]/40 transition-colors">
                                    <td class="px-4 py-2.5 font-bold text-slate-900 dark:text-white">{{ $m->product_name_en ?? $m->product?->name }}</td>
                                    <td class="px-4 py-2.5"><span class="badge badge-accent text-[10px]">{{ ucfirst($m->movement_type) }}</span></td>
                                    <td class="px-4 py-2.5 text-center font-bold text-emerald-600 dark:text-emerald-400">{{ $m->quantity > 0 ? '+' . $m->quantity : $m->quantity }}</td>
                                    <td class="px-4 py-2.5 text-slate-500 dark:text-slate-400">{{ $m->date ? $m->date->format('d M Y') : '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-6 text-center text-slate-400">
                                        {{ app()->getLocale() === 'ar' ? 'لا توجد حركات مسجلة حالياً.' : 'No recent inventory movements.' }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        @else
            <!-- Manager / Super Admin Overview -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="card p-4 border border-sky-200/80 dark:border-sky-900/50 bg-sky-50/40 dark:bg-[#062B49] rounded-2xl shadow-sm transition-colors">
                    <span class="text-xs font-bold text-[#0A4F78] dark:text-sky-400 uppercase tracking-wider block mb-1">
                        {{ app()->getLocale() === 'ar' ? 'إجمالي كادر وموظفي النظام' : 'Total System Staff' }}
                    </span>
                    <div class="flex items-baseline justify-between">
                        <span class="text-2xl font-black text-slate-900 dark:text-white">{{ $roleData['total_staff'] ?? 0 }}</span>
                        <i class="fa-solid fa-users text-sky-500 text-xl"></i>
                    </div>
                    <span class="text-[11px] text-slate-500 dark:text-slate-400 mt-2 block">{{ $roleData['active_staff'] ?? 0 }} {{ app()->getLocale() === 'ar' ? 'حسابات نشطة' : 'active accounts' }}</span>
                </div>

                <div class="card p-4 border border-teal-200/80 dark:border-teal-900/50 bg-teal-50/40 dark:bg-[#062B49] rounded-2xl shadow-sm transition-colors">
                    <span class="text-xs font-bold text-teal-700 dark:text-teal-400 uppercase tracking-wider block mb-1">
                        {{ app()->getLocale() === 'ar' ? 'سجلات الموارد البشرية (HR)' : 'HR Employee Profiles' }}
                    </span>
                    <div class="flex items-baseline justify-between">
                        <span class="text-2xl font-black text-slate-900 dark:text-white">{{ $roleData['total_employees'] ?? 0 }}</span>
                        <i class="fa-solid fa-people-roof text-teal-500 text-xl"></i>
                    </div>
                    <span class="text-[11px] text-slate-500 dark:text-slate-400 mt-2 block">{{ app()->getLocale() === 'ar' ? 'عقود وهياكل مسجلة' : 'Registered employee profiles' }}</span>
                </div>

                <div class="card p-4 border border-slate-200/80 dark:border-[#15456E] bg-slate-50/40 dark:bg-[#062B49] rounded-2xl shadow-sm transition-colors">
                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider block mb-1">
                        {{ app()->getLocale() === 'ar' ? 'الإدارة والإشراف' : 'Department Managed' }}
                    </span>
                    <div class="flex items-baseline justify-between">
                        <span class="text-lg font-bold text-slate-900 dark:text-white">{{ $employee?->department?->name ?? 'Executive' }}</span>
                        <i class="fa-solid fa-sitemap text-slate-400 text-xl"></i>
                    </div>
                    <span class="text-[11px] text-slate-500 dark:text-slate-400 mt-2 block">{{ $employee?->position?->title ?? 'Director' }}</span>
                </div>

                <div class="card p-4 border border-emerald-200/80 dark:border-emerald-900/50 bg-emerald-50/40 dark:bg-[#062B49] rounded-2xl shadow-sm transition-colors">
                    <span class="text-xs font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider block mb-1">
                        {{ app()->getLocale() === 'ar' ? 'مستوى الوصول' : 'Privilege Level' }}
                    </span>
                    <div class="flex items-baseline justify-between">
                        <span class="text-lg font-bold text-emerald-600 dark:text-emerald-400">{{ $user['role'] }}</span>
                        <i class="fa-solid fa-user-shield text-emerald-500 text-xl"></i>
                    </div>
                    <span class="text-[11px] text-emerald-600 dark:text-emerald-400 font-semibold mt-2 block">{{ app()->getLocale() === 'ar' ? 'صلاحيات قيادية وإدارية كاملة' : 'Full access authority' }}</span>
                </div>
            </div>
        @endif
    </div>

    <!-- ========================================== -->
    <!-- TAB 2: ATTENDANCE & TIME CLOCK (FULL TRACKING) -->
    <!-- ========================================== -->
    <div id="tab-content-attendance" class="staff-tab-panel hidden">
        <!-- Live Time Clock Banner -->
        <div class="card p-5 mb-6 shadow-sm border border-slate-200/80 dark:border-[#15456E] bg-gradient-to-r from-slate-50 via-white to-sky-50/60 dark:from-[#031827]/80 dark:via-[#062B49] dark:to-[#031827]/80 rounded-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 transition-colors">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-xl bg-[#0A4F78] text-white flex items-center justify-center font-bold text-xl shadow-md flex-shrink-0">
                    <i class="fa-solid fa-fingerprint"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="font-bold text-sm sm:text-base text-slate-900 dark:text-white m-0">
                            {{ app()->getLocale() === 'ar' ? 'حالة الحضور اليوم:' : "Today's Attendance Status:" }} {{ now()->translatedFormat('l, d F Y') }}
                        </h3>
                        @if($todayAttendance)
                            <span class="badge badge-success text-xs font-bold">
                                <i class="fa-solid fa-circle-check mr-1 ml-1"></i> {{ ucfirst($todayAttendance->status) }}
                            </span>
                        @else
                            <span class="badge badge-warning text-xs font-bold">
                                <i class="fa-solid fa-clock mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'لم يسجل حضور اليوم حتى الآن' : 'No Check-In Recorded Today' }}
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 mb-0">
                        @if($todayAttendance)
                            {{ app()->getLocale() === 'ar' ? 'حضور:' : 'Check-In:' }} <strong class="text-slate-800 dark:text-slate-200">{{ $todayAttendance->check_in ?? '—' }}</strong> |
                            {{ app()->getLocale() === 'ar' ? 'انصراف:' : 'Check-Out:' }} <strong class="text-slate-800 dark:text-slate-200">{{ $todayAttendance->check_out ?? (app()->getLocale() === 'ar' ? 'في العمل حالياً' : 'Currently on duty') }}</strong> |
                            {{ app()->getLocale() === 'ar' ? 'المصدر:' : 'Source:' }} <span class="badge badge-outline text-[10px]">{{ ucfirst($todayAttendance->source ?: 'Web') }}</span>
                        @else
                            {{ app()->getLocale() === 'ar' ? 'يمكنك تسجيل حضور الموظف يدوياً أو عبر الربط بالبصمة / التطبيق.' : 'You can log check-in manually or via mobile biometric sync.' }}
                        @endif
                    </p>
                </div>
            </div>

            @if(!empty($employee))
                <button type="button" onclick="openAttendanceModal()" class="btn btn-primary text-xs sm:text-sm font-bold shadow-sm flex items-center gap-2 bg-[#0A4F78] hover:bg-[#062B49] text-white flex-shrink-0">
                    <i class="fa-solid fa-plus-circle"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'تسجيل / تعديل سجل اليوم' : "Record / Adjust Today's Log" }}</span>
                </button>
            @endif
        </div>

        <!-- Monthly Attendance KPI Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
            <div class="card p-3 text-center border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] rounded-xl transition-colors">
                <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase block mb-1">{{ app()->getLocale() === 'ar' ? 'إجمالي الأيام' : 'Total Days' }}</span>
                <span class="text-2xl font-black text-slate-900 dark:text-white">{{ $attendanceStats['total_days'] }}</span>
                <span class="text-[10px] text-slate-400 block mt-0.5">{{ app()->getLocale() === 'ar' ? 'أيام عمل مرصودة' : 'Logged dates' }}</span>
            </div>

            <div class="card p-3 text-center border border-emerald-200/80 dark:border-emerald-900/50 bg-emerald-50/30 dark:bg-[#062B49] rounded-xl transition-colors">
                <span class="text-[11px] font-bold text-emerald-700 dark:text-emerald-400 uppercase block mb-1">{{ app()->getLocale() === 'ar' ? 'حاضر (في الموعد)' : 'On Time' }}</span>
                <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ $attendanceStats['present'] }}</span>
                <span class="text-[10px] text-emerald-600 dark:text-emerald-400 block mt-0.5">{{ app()->getLocale() === 'ar' ? 'حضور نظامي' : 'Present' }}</span>
            </div>

            <div class="card p-3 text-center border border-amber-200/80 dark:border-amber-900/50 bg-amber-50/30 dark:bg-[#062B49] rounded-xl transition-colors">
                <span class="text-[11px] font-bold text-amber-700 dark:text-amber-400 uppercase block mb-1">{{ app()->getLocale() === 'ar' ? 'تأخير' : 'Late' }}</span>
                <span class="text-2xl font-black text-amber-600 dark:text-amber-400">{{ $attendanceStats['late'] }}</span>
                <span class="text-[10px] text-amber-600 dark:text-amber-400 block mt-0.5">{{ app()->getLocale() === 'ar' ? 'مرات تأخير' : 'Late instances' }}</span>
            </div>

            <div class="card p-3 text-center border border-rose-200/80 dark:border-rose-900/50 bg-rose-50/30 dark:bg-[#062B49] rounded-xl transition-colors">
                <span class="text-[11px] font-bold text-rose-700 dark:text-rose-400 uppercase block mb-1">{{ app()->getLocale() === 'ar' ? 'غياب' : 'Absent' }}</span>
                <span class="text-2xl font-black text-rose-600 dark:text-rose-400">{{ $attendanceStats['absent'] }}</span>
                <span class="text-[10px] text-rose-600 dark:text-rose-400 block mt-0.5">{{ app()->getLocale() === 'ar' ? 'أيام غياب' : 'Absent days' }}</span>
            </div>

            <div class="card p-3 text-center border border-sky-200/80 dark:border-sky-900/50 bg-sky-50/30 dark:bg-[#062B49] rounded-xl transition-colors">
                <span class="text-[11px] font-bold text-[#0A4F78] dark:text-sky-400 uppercase block mb-1">{{ app()->getLocale() === 'ar' ? 'ساعات العمل' : 'Work Hours' }}</span>
                <span class="text-2xl font-black text-[#0A4F78] dark:text-sky-400">{{ $attendanceStats['worked_hours'] }}h</span>
                <span class="text-[10px] text-sky-600 dark:text-sky-400 block mt-0.5">{{ app()->getLocale() === 'ar' ? 'ساعة فعلية' : 'Hours' }}</span>
            </div>

            <div class="card p-3 text-center border border-teal-200/80 dark:border-teal-900/50 bg-teal-50/30 dark:bg-[#062B49] rounded-xl transition-colors">
                <span class="text-[11px] font-bold text-teal-700 dark:text-teal-400 uppercase block mb-1">{{ app()->getLocale() === 'ar' ? 'ساعات إضافية' : 'Overtime' }}</span>
                <span class="text-2xl font-black text-teal-600 dark:text-teal-400">+{{ $attendanceStats['overtime_hours'] }}h</span>
                <span class="text-[10px] text-teal-600 dark:text-teal-400 block mt-0.5">{{ app()->getLocale() === 'ar' ? 'عمل إضافي' : 'Overtime' }}</span>
            </div>
        </div>

        <!-- Attendance Records Table -->
        <div class="card p-0 overflow-hidden shadow-sm border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] rounded-2xl mb-8 transition-colors">
            <div class="p-4 border-b border-slate-200/80 dark:border-[#15456E] flex items-center justify-between bg-slate-50/80 dark:bg-[#031827]/60">
                <div>
                    <h3 class="font-bold text-sm text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-list-check text-emerald-500"></i>
                        {{ app()->getLocale() === 'ar' ? 'سجل الحضور والانصراف التفصيلي (آخر 30 سجل)' : 'Detailed Attendance & Punch Log (Recent 30 Records)' }}
                    </h3>
                </div>
                @if(!empty($employee))
                    <a href="{{ route('admin.hr.attendance.index', ['search' => $employee->employee_number]) }}" class="text-xs font-bold text-[#0A4F78] dark:text-sky-400 hover:underline">
                        {{ app()->getLocale() === 'ar' ? 'عرض السجل بمركز الـ HR ←' : 'Open in HR Module →' }}
                    </a>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm table">
                    <thead class="bg-slate-50 dark:bg-[#031827]/80 text-slate-700 dark:text-slate-300 font-bold border-b border-slate-200 dark:border-[#15456E] text-xs">
                        <tr>
                            <th class="px-5 py-3">{{ app()->getLocale() === 'ar' ? 'تاريخ الحضور' : 'Date' }}</th>
                            <th class="px-5 py-3 text-center">{{ app()->getLocale() === 'ar' ? 'وقت الدخول' : 'Check-In' }}</th>
                            <th class="px-5 py-3 text-center">{{ app()->getLocale() === 'ar' ? 'وقت الخروج' : 'Check-Out' }}</th>
                            <th class="px-5 py-3 text-center">{{ app()->getLocale() === 'ar' ? 'ساعات العمل' : 'Hours Worked' }}</th>
                            <th class="px-5 py-3 text-center">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                            <th class="px-5 py-3 text-center">{{ app()->getLocale() === 'ar' ? 'التأخير' : 'Late' }}</th>
                            <th class="px-5 py-3 text-center">{{ app()->getLocale() === 'ar' ? 'المصدر' : 'Source' }}</th>
                            <th class="px-5 py-3">{{ app()->getLocale() === 'ar' ? 'ملاحظات' : 'Notes' }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-[#15456E]/60 text-xs">
                        @forelse($attendanceRecords as $att)
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-[#031827]/40 transition-colors">
                                <td class="px-5 py-3 font-bold text-slate-900 dark:text-white">
                                    <div>{{ $att->attendance_date ? $att->attendance_date->format('d M Y') : '—' }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $att->attendance_date ? $att->attendance_date->format('l') : '' }}</div>
                                </td>
                                <td class="px-5 py-3 text-center font-mono">
                                    @if($att->check_in)
                                        <span class="text-emerald-700 dark:text-emerald-400 font-bold">{{ substr($att->check_in, 0, 5) }}</span>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-center font-mono">
                                    @if($att->check_out)
                                        <span class="text-[#0A4F78] dark:text-sky-400 font-bold">{{ substr($att->check_out, 0, 5) }}</span>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-center font-bold text-slate-900 dark:text-white">
                                    {{ $att->worked_hours }}h
                                    @if($att->overtime_minutes > 0)
                                        <span class="text-[10px] text-teal-600 dark:text-teal-400 block">+{{ round($att->overtime_minutes / 60, 1) }}h OT</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-center">
                                    @if($att->status === 'present')
                                        <span class="badge badge-success text-[10px] font-bold">{{ app()->getLocale() === 'ar' ? 'حاضر' : 'Present' }}</span>
                                    @elseif($att->status === 'late')
                                        <span class="badge badge-warning text-[10px] font-bold">{{ app()->getLocale() === 'ar' ? 'تأخير' : 'Late' }}</span>
                                    @elseif($att->status === 'absent')
                                        <span class="badge badge-danger text-[10px] font-bold">{{ app()->getLocale() === 'ar' ? 'غياب' : 'Absent' }}</span>
                                    @elseif($att->status === 'half_day')
                                        <span class="badge badge-accent text-[10px] font-bold">{{ app()->getLocale() === 'ar' ? 'نصف يوم' : 'Half Day' }}</span>
                                    @elseif($att->status === 'on_leave')
                                        <span class="badge badge-outline text-[10px] font-bold">{{ app()->getLocale() === 'ar' ? 'إجازة' : 'On Leave' }}</span>
                                    @else
                                        <span class="badge badge-outline text-[10px]">{{ ucfirst($att->status) }}</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-center">
                                    @if($att->late_minutes > 0)
                                        <span class="text-amber-600 dark:text-amber-400 font-bold font-mono">{{ $att->late_minutes }}m</span>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-center text-slate-500 dark:text-slate-400">
                                    <span class="text-[11px] capitalize">{{ str_replace('_', ' ', $att->source ?: 'Web') }}</span>
                                </td>
                                <td class="px-5 py-3 text-slate-500 dark:text-slate-400 max-w-xs truncate" title="{{ $att->notes }}">
                                    {{ $att->notes ?: '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-8 text-center text-slate-400">
                                    <i class="fa-solid fa-fingerprint text-2xl mb-1.5 block"></i>
                                    {{ app()->getLocale() === 'ar' ? 'لا توجد سجلات حضور مسجلة لهذا الموظف.' : 'No attendance logs recorded for this employee.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- TAB 3: HR EMPLOYEE DOSSIER (COMPLETE HR SPECS) -->
    <!-- ========================================== -->
    <div id="tab-content-hr" class="staff-tab-panel hidden">
        @if($employee)
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Left 2 Columns: Employment & Personal Dossier -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Employment Contract Info -->
                    <div class="card p-6 border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] rounded-2xl shadow-sm transition-colors">
                        <div class="flex items-center justify-between border-b border-slate-100 dark:border-[#15456E] pb-3 mb-4">
                            <h3 class="font-bold text-base text-slate-900 dark:text-white flex items-center gap-2 m-0">
                                <i class="fa-solid fa-briefcase text-[#0A4F78] dark:text-sky-400"></i>
                                {{ app()->getLocale() === 'ar' ? 'البيانات الوظيفية والتعاقدية' : 'Employment & Job Specification' }}
                            </h3>
                            <a href="{{ route('admin.hr.employees.show', $employee->id) }}" class="btn btn-secondary btn-sm text-xs font-bold">
                                <i class="fa-solid fa-up-right-from-square mr-1 ml-1"></i>
                                {{ app()->getLocale() === 'ar' ? 'فتح الملف بمركز HR' : 'Open in HR Module' }}
                            </a>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 text-xs">
                            <div class="p-3 rounded-xl bg-slate-50/80 dark:bg-[#031827]/70 border border-slate-200/60 dark:border-[#15456E]/60">
                                <span class="text-slate-400 block mb-0.5">{{ app()->getLocale() === 'ar' ? 'الرقم الوظيفي' : 'Employee Number' }}</span>
                                <span class="font-mono font-bold text-slate-900 dark:text-white text-sm">{{ $employee->employee_number }}</span>
                            </div>

                            <div class="p-3 rounded-xl bg-slate-50/80 dark:bg-[#031827]/70 border border-slate-200/60 dark:border-[#15456E]/60">
                                <span class="text-slate-400 block mb-0.5">{{ app()->getLocale() === 'ar' ? 'القسم / الإدارة' : 'Department' }}</span>
                                <span class="font-bold text-slate-900 dark:text-white">{{ $employee->department?->name ?? '—' }}</span>
                            </div>

                            <div class="p-3 rounded-xl bg-slate-50/80 dark:bg-[#031827]/70 border border-slate-200/60 dark:border-[#15456E]/60">
                                <span class="text-slate-400 block mb-0.5">{{ app()->getLocale() === 'ar' ? 'المسمى الوظيفي' : 'Job Position' }}</span>
                                <span class="font-bold text-[#0A4F78] dark:text-sky-400">{{ $employee->position?->title ?? '—' }}</span>
                            </div>

                            <div class="p-3 rounded-xl bg-slate-50/80 dark:bg-[#031827]/70 border border-slate-200/60 dark:border-[#15456E]/60">
                                <span class="text-slate-400 block mb-0.5">{{ app()->getLocale() === 'ar' ? 'المدير المباشر' : 'Direct Manager' }}</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $employee->manager ? $employee->manager->first_name . ' ' . $employee->manager->last_name : 'Executive Management' }}</span>
                            </div>

                            <div class="p-3 rounded-xl bg-slate-50/80 dark:bg-[#031827]/70 border border-slate-200/60 dark:border-[#15456E]/60">
                                <span class="text-slate-400 block mb-0.5">{{ app()->getLocale() === 'ar' ? 'تاريخ التعيين' : 'Hire Date' }}</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $employee->hire_date ? $employee->hire_date->format('d M Y') : '—' }}</span>
                            </div>

                            <div class="p-3 rounded-xl bg-slate-50/80 dark:bg-[#031827]/70 border border-slate-200/60 dark:border-[#15456E]/60">
                                <span class="text-slate-400 block mb-0.5">{{ app()->getLocale() === 'ar' ? 'نوع التوظيف' : 'Employment Type' }}</span>
                                <span class="badge badge-accent text-[10px]">{{ ucfirst(str_replace('_', ' ', $employee->employment_type ?? 'Full Time')) }}</span>
                            </div>

                            <div class="p-3 rounded-xl bg-slate-50/80 dark:bg-[#031827]/70 border border-slate-200/60 dark:border-[#15456E]/60">
                                <span class="text-slate-400 block mb-0.5">{{ app()->getLocale() === 'ar' ? 'نظام العمل / الوردية' : 'Work Schedule' }}</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $employee->workSchedule?->name ?? 'Standard (09:00 - 17:00)' }}</span>
                            </div>

                            <div class="p-3 rounded-xl bg-slate-50/80 dark:bg-[#031827]/70 border border-slate-200/60 dark:border-[#15456E]/60">
                                <span class="text-slate-400 block mb-0.5">{{ app()->getLocale() === 'ar' ? 'الفرع / الموقع' : 'Location / Hub' }}</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $employee->location?->name ?? ($user['city'] ?? 'Headquarters') }}</span>
                            </div>

                            <div class="p-3 rounded-xl bg-slate-50/80 dark:bg-[#031827]/70 border border-slate-200/60 dark:border-[#15456E]/60">
                                <span class="text-slate-400 block mb-0.5">{{ app()->getLocale() === 'ar' ? 'الحالة الوظيفية' : 'Employment Status' }}</span>
                                <span class="badge badge-success text-[10px]">{{ ucfirst($employee->employment_status ?? 'Active') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Personal & Legal Info -->
                    <div class="card p-6 border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] rounded-2xl shadow-sm transition-colors">
                        <h3 class="font-bold text-base text-slate-900 dark:text-white flex items-center gap-2 border-b border-slate-100 dark:border-[#15456E] pb-3 mb-4">
                            <i class="fa-solid fa-address-card text-sky-500"></i>
                            {{ app()->getLocale() === 'ar' ? 'البيانات الشخصية والهوية' : 'Personal & Identification Details' }}
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 text-xs">
                            <div class="p-3 rounded-xl bg-slate-50/80 dark:bg-[#031827]/70 border border-slate-200/60 dark:border-[#15456E]/60">
                                <span class="text-slate-400 block mb-0.5">{{ app()->getLocale() === 'ar' ? 'الهوية الوطنية / الإقامة' : 'National ID / Residency' }}</span>
                                <span class="font-mono font-bold text-slate-900 dark:text-white">{{ $employee->national_id ?: '—' }}</span>
                            </div>

                            <div class="p-3 rounded-xl bg-slate-50/80 dark:bg-[#031827]/70 border border-slate-200/60 dark:border-[#15456E]/60">
                                <span class="text-slate-400 block mb-0.5">{{ app()->getLocale() === 'ar' ? 'الجنسية' : 'Nationality' }}</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $employee->nationality ?: 'Saudi' }}</span>
                            </div>

                            <div class="p-3 rounded-xl bg-slate-50/80 dark:bg-[#031827]/70 border border-slate-200/60 dark:border-[#15456E]/60">
                                <span class="text-slate-400 block mb-0.5">{{ app()->getLocale() === 'ar' ? 'الجنس والحالة الاجتماعية' : 'Gender & Marital Status' }}</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200">{{ ucfirst($employee->gender ?? 'Male') }} / {{ ucfirst($employee->marital_status ?? 'Single') }}</span>
                            </div>

                            <div class="p-3 rounded-xl bg-slate-50/80 dark:bg-[#031827]/70 border border-slate-200/60 dark:border-[#15456E]/60">
                                <span class="text-slate-400 block mb-0.5">{{ app()->getLocale() === 'ar' ? 'تاريخ الميلاد' : 'Date of Birth' }}</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $employee->date_of_birth ? $employee->date_of_birth->format('d M Y') : '—' }}</span>
                            </div>

                            <div class="p-3 rounded-xl bg-slate-50/80 dark:bg-[#031827]/70 border border-slate-200/60 dark:border-[#15456E]/60">
                                <span class="text-slate-400 block mb-0.5">{{ app()->getLocale() === 'ar' ? 'العنوان' : 'Address' }}</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $employee->address ?: ($user['territory_label'] ?: '—') }}</span>
                            </div>

                            <div class="p-3 rounded-xl bg-slate-50/80 dark:bg-[#031827]/70 border border-slate-200/60 dark:border-[#15456E]/60">
                                <span class="text-slate-400 block mb-0.5">{{ app()->getLocale() === 'ar' ? 'هاتف بديل للطوارئ' : 'Emergency Contact' }}</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200" dir="ltr">{{ $employee->alternate_phone ?: ($user['phone'] ?: '—') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Compensation & Banking Card -->
                <div class="space-y-6">
                    <div class="card p-5 border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] rounded-2xl shadow-sm transition-colors">
                        <h4 class="font-bold text-sm text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-wallet text-emerald-500"></i>
                            {{ app()->getLocale() === 'ar' ? 'هيكل الراتب والبدلات' : 'Compensation Breakdown' }}
                        </h4>

                        <div class="space-y-3 text-xs">
                            <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50/80 dark:bg-[#031827]/70 border border-slate-200/60 dark:border-[#15456E]/60">
                                <span class="text-slate-500 dark:text-slate-400">{{ app()->getLocale() === 'ar' ? 'الراتب الأساسي' : 'Basic Salary' }}</span>
                                <span class="font-mono font-bold text-slate-900 dark:text-white">{{ number_format((float)($employee->basic_salary ?: 6500), 2) }} SAR</span>
                            </div>

                            <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50/80 dark:bg-[#031827]/70 border border-slate-200/60 dark:border-[#15456E]/60">
                                <span class="text-slate-500 dark:text-slate-400">{{ app()->getLocale() === 'ar' ? 'بدل سكن' : 'Housing Allowance' }}</span>
                                <span class="font-mono font-bold text-slate-900 dark:text-white">{{ number_format((float)($employee->housing_allowance ?: 1500), 2) }} SAR</span>
                            </div>

                            <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50/80 dark:bg-[#031827]/70 border border-slate-200/60 dark:border-[#15456E]/60">
                                <span class="text-slate-500 dark:text-slate-400">{{ app()->getLocale() === 'ar' ? 'بدل انتقال' : 'Transport Allowance' }}</span>
                                <span class="font-mono font-bold text-slate-900 dark:text-white">{{ number_format((float)($employee->transportation_allowance ?: 800), 2) }} SAR</span>
                            </div>

                            @php
                                $totalComp = (float)($employee->basic_salary ?: 6500) + (float)($employee->housing_allowance ?: 1500) + (float)($employee->transportation_allowance ?: 800);
                            @endphp
                            <div class="flex items-center justify-between p-3 rounded-xl bg-emerald-50/40 dark:bg-emerald-950/30 border border-emerald-200/80 dark:border-emerald-800/60">
                                <span class="font-bold text-emerald-800 dark:text-emerald-300">{{ app()->getLocale() === 'ar' ? 'إجمالي الأجر الشهري' : 'Total Gross Compensation' }}</span>
                                <span class="font-mono font-black text-sm text-emerald-700 dark:text-emerald-400">{{ number_format($totalComp, 2) }} SAR</span>
                            </div>
                        </div>
                    </div>

                    <!-- Banking Details -->
                    <div class="card p-5 border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] rounded-2xl shadow-sm transition-colors">
                        <h4 class="font-bold text-sm text-slate-900 dark:text-white mb-3 flex items-center gap-2">
                            <i class="fa-solid fa-building-columns text-[#0A4F78] dark:text-sky-400"></i>
                            {{ app()->getLocale() === 'ar' ? 'بيانات الحساب البنكي' : 'Banking & Payroll Details' }}
                        </h4>

                        <div class="space-y-2 text-xs">
                            <div class="p-2.5 rounded-xl bg-slate-50/80 dark:bg-[#031827]/70 border border-slate-200/60 dark:border-[#15456E]/60">
                                <span class="text-slate-400 block mb-0.5">{{ app()->getLocale() === 'ar' ? 'اسم البنك' : 'Bank Name' }}</span>
                                <span class="font-bold text-slate-800 dark:text-slate-200">{{ $employee->bank_name ?: 'Al Rajhi Bank' }}</span>
                            </div>

                            <div class="p-2.5 rounded-xl bg-slate-50/80 dark:bg-[#031827]/70 border border-slate-200/60 dark:border-[#15456E]/60">
                                <span class="text-slate-400 block mb-0.5">{{ app()->getLocale() === 'ar' ? 'رقم الآيبان (IBAN)' : 'IBAN Number' }}</span>
                                <span class="font-mono font-bold text-slate-900 dark:text-white" dir="ltr">{{ $employee->iban ?: 'SA44 8000 0201 6080 1000 9999' }}</span>
                            </div>

                            <div class="p-2.5 rounded-xl bg-slate-50/80 dark:bg-[#031827]/70 border border-slate-200/60 dark:border-[#15456E]/60">
                                <span class="text-slate-400 block mb-0.5">{{ app()->getLocale() === 'ar' ? 'طريقة تحويل الراتب' : 'Payment Method' }}</span>
                                <span class="badge badge-accent text-[10px]">{{ ucfirst($employee->payment_method ?: 'Bank Transfer') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="card p-12 text-center border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] rounded-2xl transition-colors">
                <i class="fa-solid fa-id-card text-4xl text-slate-300 dark:text-slate-600 mb-3 block"></i>
                <h3 class="text-base font-bold text-slate-900 dark:text-white mb-1">
                    {{ app()->getLocale() === 'ar' ? 'لا يوجد ملف وظيفي (HR Employee) مرتبط' : 'No HR Employee Dossier Linked' }}
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 max-w-md mx-auto mb-4">
                    {{ app()->getLocale() === 'ar' ? 'هذا الحساب مسجل كمستخدم نظام فقط ولم يتم ربطه بملف تعاقدي ومسمى وظيفي رسمي بمركز الموارد البشرية.' : 'This account is registered as a system user only and does not have an official HR personnel profile linked.' }}
                </p>
                <a href="{{ route('admin.hr.employees.create', ['user_id' => $user['id']]) }}" class="btn btn-primary text-xs font-bold bg-[#0A4F78] hover:bg-[#062B49] text-white">
                    <i class="fa-solid fa-plus mr-1.5 ml-1.5"></i>
                    {{ app()->getLocale() === 'ar' ? 'إنشاء ملف وظيفي HR لهذا الموظف' : 'Create HR Employee Dossier' }}
                </a>
            </div>
        @endif
    </div>

    <!-- ========================================== -->
    <!-- TAB 4: SECURITY & PERMISSIONS MATRIX -->
    <!-- ========================================== -->
    <div id="tab-content-security" class="staff-tab-panel hidden">
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;" class="flex flex-col lg:grid mb-8">
            <!-- Left: Granular CRUD Permissions Matrix -->
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                <div class="card p-6 border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] rounded-2xl shadow-sm transition-colors">
                    <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--color-border); padding-bottom: 0.85rem; margin-bottom: 1.25rem;">
                        <div>
                            <h3 style="font-size: 1rem; font-weight: 800; margin: 0; display: flex; align-items: center; gap: 0.5rem; color: var(--color-text-primary);">
                                <i class="fa-solid fa-table-cells text-primary"></i>
                                {{ __('admin.users.permissions_matrix') }}
                            </h3>
                            <p class="text-xs text-muted" style="margin: 0;">
                                {{ __('admin.users.permissions_desc') }}
                            </p>
                        </div>
                        @if(!empty($user['role_id']))
                            <a href="{{ route('admin.roles.edit', $user['role_id']) }}" class="btn btn-secondary btn-sm font-bold">
                                <i class="fa-solid fa-sliders mr-1 ml-1"></i> {{ __('admin.users.modify_role_matrix') }}
                            </a>
                        @endif
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem;">
                        @php
                            $actionLabels = [
                                'view' => ['en' => 'View', 'ar' => 'عرض', 'icon' => 'fa-eye'],
                                'create' => ['en' => 'Create', 'ar' => 'إضافة', 'icon' => 'fa-plus'],
                                'edit' => ['en' => 'Edit', 'ar' => 'تعديل', 'icon' => 'fa-pen'],
                                'delete' => ['en' => 'Delete', 'ar' => 'حذف', 'icon' => 'fa-trash'],
                            ];
                        @endphp

                        @foreach($evaluatedPermissions as $modKey => $modData)
                            <div style="background: var(--color-bg-subtle); padding: 1.25rem; border-radius: var(--radius-lg); border: 1px solid var(--color-border); display: flex; flex-direction: column; justify-content: space-between;">
                                <div>
                                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.85rem;">
                                        <h4 style="font-size: 0.9375rem; font-weight: 800; color: var(--color-primary); margin: 0;">
                                            {{ $modData['label'] }}
                                        </h4>
                                        <span class="text-[11px] font-mono text-muted">admin.{{ $modKey }}</span>
                                    </div>

                                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.5rem;">
                                        @foreach($actionLabels as $actKey => $actMeta)
                                            @php
                                                $isAllowed = !empty($modData['actions'][$actKey]);
                                            @endphp
                                            <div style="display: flex; align-items: center; gap: 0.5rem; padding: 0.4rem 0.6rem; border-radius: var(--radius-md); font-size: 0.75rem; font-weight: 700; {{ $isAllowed ? 'background: rgba(16, 185, 129, 0.12); color: #059669; border: 1px solid rgba(16, 185, 129, 0.25);' : 'background: rgba(156, 163, 175, 0.08); color: #9CA3AF; border: 1px dashed rgba(156, 163, 175, 0.2);' }}">
                                                @if($isAllowed)
                                                    <i class="fa-solid fa-check text-emerald-600 dark:text-emerald-400"></i>
                                                @else
                                                    <i class="fa-solid fa-xmark text-slate-400"></i>
                                                @endif
                                                <span>{{ app()->getLocale() === 'ar' ? $actMeta['ar'] : $actMeta['en'] }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Right Sidebar: Role & Security Overview -->
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                <div class="card p-6 border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] rounded-2xl shadow-sm transition-colors">
                    <h4 style="font-size: 1rem; font-weight: 800; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; color: var(--color-primary);">
                        <i class="fa-solid fa-id-badge text-primary"></i>
                        {{ __('admin.users.security_role') }}
                    </h4>
                    <div class="text-sm" style="display: flex; flex-direction: column; gap: 0.75rem;">
                        <div>
                            <div class="text-xs text-muted font-medium">{{ __('admin.users.role_title') }}</div>
                            <div class="font-bold text-primary mt-0.5">{{ $user['role'] ?? 'Staff User' }}</div>
                        </div>
                        @if(!empty($user['role_description']))
                            <div>
                                <div class="text-xs text-muted font-medium">{{ __('admin.users.role_purpose') }}</div>
                                <div class="text-xs text-secondary mt-0.5 leading-relaxed">{{ $user['role_description'] }}</div>
                            </div>
                        @endif
                        <div class="pt-2 border-t border-slate-100 dark:border-[#15456E]">
                            <div class="text-xs text-muted font-medium">{{ __('admin.users.access_level') }}</div>
                            <div class="font-bold text-xs mt-0.5">
                                @if(in_array(strtolower($user['role']), ['super admin', 'super-admin']))
                                    <span class="text-purple-600 dark:text-purple-400 flex items-center gap-1">
                                        <i class="fa-solid fa-crown"></i> {{ __('admin.users.root_authority') }}
                                    </span>
                                @else
                                    <span class="text-sky-600 dark:text-sky-400 flex items-center gap-1">
                                        <i class="fa-solid fa-user-gear"></i> {{ __('admin.users.granular_role') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card p-6 border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] rounded-2xl shadow-sm transition-colors">
                    <h4 style="font-size: 1rem; font-weight: 800; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; color: var(--color-primary);">
                        <i class="fa-solid fa-mobile-screen text-primary"></i>
                        {{ app()->getLocale() === 'ar' ? 'الإشعارات والأجهزة' : 'Devices & Notifications' }}
                    </h4>
                    <div class="text-xs space-y-2.5 text-slate-600 dark:text-slate-300">
                        <div class="flex items-center justify-between">
                            <span>{{ __('admin.users.push_notifications') }}:</span>
                            @if(!empty($user['has_fcm']))
                                <span class="badge badge-success text-[10px]">{{ $user['fcm_device'] ?? 'Connected' }}</span>
                            @else
                                <span class="badge badge-outline text-[10px]">{{ __('admin.users.none') }}</span>
                            @endif
                        </div>
                        <div class="flex items-center justify-between">
                            <span>{{ __('admin.users.registered') }}:</span>
                            <span class="font-bold">{{ $user['created_at'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>{{ __('admin.users.last_updated') }}:</span>
                            <span class="font-bold">{{ $user['updated_at'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MANUAL ATTENDANCE LOGGING MODAL -->
    <!-- ========================================== -->
    @if(!empty($employee))
        <div id="attendance-modal" class="fixed inset-0 z-[1050] overflow-y-auto bg-slate-950/70 p-3 sm:p-4 hidden backdrop-blur-sm" onclick="if(event.target === this) closeAttendanceModal()">
            <div class="min-h-full flex items-center justify-center p-0">
                <div class="card max-w-lg w-full p-0 shadow-2xl relative flex flex-col max-h-[88vh] border border-slate-200 dark:border-[#15456E] rounded-2xl overflow-hidden bg-white dark:bg-[#062B49] transition-colors">
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-[#15456E] px-6 py-4 flex-shrink-0 bg-slate-50/70 dark:bg-[#031827]/70">
                        <h3 class="font-bold text-base text-slate-900 dark:text-white flex items-center gap-2.5 m-0">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/70 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-100 dark:border-emerald-900 flex-shrink-0">
                                <i class="fa-solid fa-fingerprint text-sm"></i>
                            </div>
                            <div>
                                <span>{{ app()->getLocale() === 'ar' ? 'تسجيل وتحديث حضور الموظف' : 'Log / Adjust Staff Attendance' }}</span>
                                <p class="text-[11px] font-normal text-slate-500 dark:text-slate-400 m-0">
                                    {{ $user['name'] }} ({{ $employee->employee_number }})
                                </p>
                            </div>
                        </h3>
                        <button type="button" onclick="closeAttendanceModal()" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-[#031827] transition-colors cursor-pointer">
                            <i class="fa-solid fa-xmark text-base"></i>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('admin.users.attendance.record', $user['id']) }}" class="flex flex-col flex-1 min-h-0 overflow-hidden m-0 p-0">
                        @csrf
                        <div class="flex-1 overflow-y-auto px-6 py-4 space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                                    {{ app()->getLocale() === 'ar' ? 'تاريخ الحضور *' : 'Attendance Date *' }}
                                </label>
                                <input type="date" name="attendance_date" value="{{ now()->toDateString() }}" required class="form-control text-sm w-full">
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                                        {{ app()->getLocale() === 'ar' ? 'وقت الدخول (Check-In)' : 'Check-In Time' }}
                                    </label>
                                    <input type="time" name="check_in" value="{{ $todayAttendance?->check_in ? substr($todayAttendance->check_in, 0, 5) : '09:00' }}" class="form-control text-sm w-full">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                                        {{ app()->getLocale() === 'ar' ? 'وقت الخروج (Check-Out)' : 'Check-Out Time' }}
                                    </label>
                                    <input type="time" name="check_out" value="{{ $todayAttendance?->check_out ? substr($todayAttendance->check_out, 0, 5) : '17:00' }}" class="form-control text-sm w-full">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                                    {{ app()->getLocale() === 'ar' ? 'حالة الحضور *' : 'Attendance Status *' }}
                                </label>
                                <select name="status" required class="form-select text-sm w-full">
                                    <option value="present" {{ ($todayAttendance?->status ?? 'present') === 'present' ? 'selected' : '' }}>
                                        {{ app()->getLocale() === 'ar' ? 'حاضر (في الموعد - Present)' : 'Present (On Time)' }}
                                    </option>
                                    <option value="late" {{ ($todayAttendance?->status ?? '') === 'late' ? 'selected' : '' }}>
                                        {{ app()->getLocale() === 'ar' ? 'تأخير (Late)' : 'Late' }}
                                    </option>
                                    <option value="half_day" {{ ($todayAttendance?->status ?? '') === 'half_day' ? 'selected' : '' }}>
                                        {{ app()->getLocale() === 'ar' ? 'نصف يوم (Half Day)' : 'Half Day' }}
                                    </option>
                                    <option value="absent" {{ ($todayAttendance?->status ?? '') === 'absent' ? 'selected' : '' }}>
                                        {{ app()->getLocale() === 'ar' ? 'غياب (Absent)' : 'Absent' }}
                                    </option>
                                    <option value="on_leave" {{ ($todayAttendance?->status ?? '') === 'on_leave' ? 'selected' : '' }}>
                                        {{ app()->getLocale() === 'ar' ? 'إجازة مصرح بها (On Leave)' : 'On Leave' }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                                    {{ app()->getLocale() === 'ar' ? 'ملاحظات الإدارة' : 'HR Notes / Reason' }}
                                </label>
                                <textarea name="notes" rows="2" placeholder="{{ app()->getLocale() === 'ar' ? 'سبب التأخير أو تسجيل حضور يدوي...' : 'Manual punch log or explanation...' }}" class="form-control text-xs w-full"></textarea>
                            </div>
                        </div>

                        <div class="px-6 py-3.5 bg-slate-50/90 dark:bg-[#031827]/80 border-t border-slate-100 dark:border-[#15456E] flex items-center justify-end gap-2 flex-shrink-0">
                            <button type="button" onclick="closeAttendanceModal()" class="btn btn-secondary text-xs px-4 py-2 cursor-pointer">
                                {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                            </button>
                            <button type="submit" class="btn btn-primary font-bold text-xs px-5 py-2 shadow-sm bg-[#0A4F78] hover:bg-[#062B49] text-white cursor-pointer">
                                <i class="fa-solid fa-check mr-1.5 ml-1.5"></i>
                                {{ app()->getLocale() === 'ar' ? 'حفظ واعتماد الحضور' : 'Save & Confirm Punch' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <script>
        function switchStaffTab(tabKey) {
            document.querySelectorAll('.staff-tab-panel').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.staff-tab-btn').forEach(btn => {
                btn.classList.remove('border-[#0A4F78]', 'text-[#0A4F78]', 'dark:border-sky-400', 'dark:text-sky-300');
                btn.classList.add('border-transparent', 'text-slate-500');
            });

            const targetPanel = document.getElementById('tab-content-' + tabKey);
            const targetBtn = document.getElementById('tab-btn-' + tabKey);

            if (targetPanel) targetPanel.classList.remove('hidden');
            if (targetBtn) {
                targetBtn.classList.add('border-[#0A4F78]', 'text-[#0A4F78]', 'dark:border-sky-400', 'dark:text-sky-300');
                targetBtn.classList.remove('border-transparent', 'text-slate-500');
            }
        }

        function openAttendanceModal() {
            const m = document.getElementById('attendance-modal');
            if (m) {
                m.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeAttendanceModal() {
            const m = document.getElementById('attendance-modal');
            if (m) {
                m.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAttendanceModal();
            }
        });
    </script>
</x-layouts.admin>
