<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'لوحة قيادة الموارد البشرية وشؤون الموظفين' : 'HR & People Operations Command'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'متابعة سجلات الحضور اليومي، دوام الموظفين، وحركة القوى العاملة' : 'Workforce attendance tracking, shift monitoring, and personnel operations.'"
    :breadcrumbs="[
        (app()->getLocale() === 'ar' ? 'لوحة التحكم' : 'Dashboard') => route('admin.dashboard'),
        (app()->getLocale() === 'ar' ? 'الموارد البشرية' : 'HR & Staff') => route('admin.dashboard', ['view' => 'hr'])
    ]"
>
    <!-- TOP VIEW SWITCHER (For Admins & Managers) -->
    @include('admin.dashboard.partials.view_switcher', ['currentView' => 'hr'])

    <!-- 1. HERO HR COMMAND BANNER -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-[#062B49] via-[#0A4F78] to-[#4c1d95] text-white p-6 sm:p-7 mb-6 shadow-md border border-[#15456E]">
        <div class="absolute -right-16 -top-16 w-64 h-64 bg-purple-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-16 -bottom-16 w-64 h-64 bg-cyan-500/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <!-- Left Info -->
            <div class="flex items-start sm:items-center gap-4">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center text-3xl font-black text-purple-300 shadow-inner">
                    <i class="fa-solid fa-users-gear"></i>
                </div>

                <div>
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h2 class="text-xl sm:text-2xl font-black tracking-tight text-white mb-0">
                            {{ auth()->user()->name }}
                        </h2>
                        <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-black bg-purple-400/20 text-purple-200 border border-purple-300/30">
                            <i class="fa-solid fa-id-badge text-[11px]"></i>
                            {{ auth()->user()->role?->name ?? (app()->getLocale() === 'ar' ? 'مدير الموارد البشرية' : 'HR Manager') }}
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white/10 text-white border border-white/15">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            {{ app()->getLocale() === 'ar' ? 'سجل الدوام نشط' : 'Attendance Active' }}
                        </span>
                    </div>

                    <p class="text-xs sm:text-sm text-cyan-100/90 mt-1.5 mb-0 flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center gap-1">
                            <i class="fa-solid fa-building-user text-purple-300"></i>
                            {{ app()->getLocale() === 'ar' ? 'المقر الإداري الرئيسي — الرياض' : 'Headquarters — Riyadh' }}
                        </span>
                        <span>•</span>
                        <span class="inline-flex items-center gap-1 text-white/80">
                            <i class="fa-regular fa-calendar text-cyan-300"></i>
                            {{ now()->translatedFormat('l, d F Y') }}
                        </span>
                    </p>

                    <!-- Shift Attendance Chip -->
                    <div class="mt-3 inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-black/20 backdrop-blur-sm border border-white/10 text-xs">
                        <i class="fa-solid fa-clock text-purple-300"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'حالة دوامك اليوم:' : 'Your Attendance Today:' }}</span>
                        @if($todayAttendance && $todayAttendance->check_in)
                            <span class="font-bold text-emerald-300 flex items-center gap-1">
                                <i class="fa-solid fa-circle-check text-[10px]"></i>
                                {{ app()->getLocale() === 'ar' ? 'تم الحضور' : 'Clocked In' }} ({{ \Carbon\Carbon::parse($todayAttendance->check_in)->format('h:i A') }})
                            </span>
                            @if(!$todayAttendance->check_out)
                                <form method="POST" action="{{ route('admin.attendance.self-checkout') }}" class="inline m-0">
                                    @csrf
                                    <button type="submit" class="btn btn-xs bg-red-500/80 hover:bg-red-600 text-white rounded-lg text-[10px] px-2 py-0.5 ml-1 mr-1 border-0">
                                        <i class="fa-solid fa-right-from-bracket mr-1 ml-1"></i>
                                        {{ app()->getLocale() === 'ar' ? 'تسجيل انصراف' : 'Punch Out' }}
                                    </button>
                                </form>
                            @else
                                <span class="font-bold text-cyan-200">
                                    — {{ app()->getLocale() === 'ar' ? 'انصرف' : 'Out' }} ({{ \Carbon\Carbon::parse($todayAttendance->check_out)->format('h:i A') }})
                                </span>
                            @endif
                        @else
                            <form method="POST" action="{{ route('admin.attendance.self-checkin') }}" class="inline m-0">
                                @csrf
                                <button type="submit" class="btn btn-xs bg-purple-500 hover:bg-purple-600 text-white rounded-lg text-[10px] px-2.5 py-0.5 border-0 font-bold shadow-xs">
                                    <i class="fa-solid fa-fingerprint mr-1 ml-1"></i>
                                    {{ app()->getLocale() === 'ar' ? 'تسجيل حضورك الآن' : 'Clock In Now' }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Fast HR CTAs -->
            <div class="flex items-center gap-2.5 flex-wrap">
                <a href="{{ route('admin.users.create') }}" class="btn font-bold text-xs sm:text-sm px-4 py-2.5 rounded-xl shadow-sm bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-600 hover:to-indigo-700 text-white border-0 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-user-plus"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'إضافة موظف جديد' : 'New Employee' }}</span>
                </a>

                <a href="{{ route('admin.users.index') }}" class="btn font-bold text-xs sm:text-sm px-4 py-2.5 rounded-xl shadow-sm bg-white/15 hover:bg-white/25 text-white border border-white/20 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-address-book text-purple-300"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'دليل الموظفين' : 'Staff Directory' }}</span>
                </a>

                <a href="{{ route('admin.roles.index') }}" class="btn font-bold text-xs sm:text-sm px-4 py-2.5 rounded-xl shadow-sm bg-white/10 hover:bg-white/20 text-white border border-white/15 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-shield-halved text-cyan-300"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'الأدوار والصلاحيات' : 'Roles & Matrix' }}</span>
                </a>
            </div>
        </div>
    </div>

    <!-- 2. TOP 4 VIBRANT HR KPI CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- 1. Total Active Workforce -->
        <div class="card p-5 rounded-2xl border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] shadow-xs relative overflow-hidden transition-all hover:shadow-md hover:border-purple-400">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    {{ app()->getLocale() === 'ar' ? 'إجمالي القوى العاملة' : 'Total Workforce' }}
                </span>
                <span class="w-10 h-10 rounded-xl bg-purple-100 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center text-sm shadow-inner">
                    <i class="fa-solid fa-users"></i>
                </span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-slate-900 dark:text-white">{{ $totalEmployeesCount }}</span>
                <span class="text-xs font-bold text-purple-600 dark:text-purple-400">
                    {{ app()->getLocale() === 'ar' ? 'موظف مثبت' : 'active staff' }}
                </span>
            </div>
            <div class="mt-3 flex items-center justify-between text-xs pt-2 border-t border-slate-100 dark:border-[#15456E]/60 text-slate-500">
                <span>{{ app()->getLocale() === 'ar' ? 'الأقسام النشطة:' : 'Departments:' }} <strong>{{ $departmentsCount }}</strong></span>
                <a href="{{ route('admin.users.index') }}" class="text-purple-600 dark:text-purple-400 font-bold hover:underline">
                    {{ app()->getLocale() === 'ar' ? 'عرض السجل ↗' : 'Directory ↗' }}
                </a>
            </div>
        </div>

        <!-- 2. Present Today -->
        <div class="card p-5 rounded-2xl border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] shadow-xs relative overflow-hidden transition-all hover:shadow-md hover:border-emerald-400">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    {{ app()->getLocale() === 'ar' ? 'حضور اليوم' : 'Present Today' }}
                </span>
                <span class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-sm shadow-inner">
                    <i class="fa-solid fa-user-check"></i>
                </span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-emerald-600 dark:text-emerald-400">{{ $presentCount }}</span>
                <span class="text-xs font-bold text-emerald-500">
                    ({{ $attendanceRate }}% {{ app()->getLocale() === 'ar' ? 'نسبة الانضباط' : 'rate' }})
                </span>
            </div>
            <div class="mt-3 flex items-center justify-between text-xs pt-2 border-t border-slate-100 dark:border-[#15456E]/60 text-slate-500">
                <span>{{ app()->getLocale() === 'ar' ? 'تم تسجيل حضورهم' : 'Clocked In' }}</span>
                <span class="text-emerald-600 dark:text-emerald-400 font-bold">
                    <i class="fa-solid fa-circle-check text-[10px] mr-1 ml-1"></i>
                    {{ app()->getLocale() === 'ar' ? 'منتظم' : 'Active' }}
                </span>
            </div>
        </div>

        <!-- 3. Late Check-ins Today -->
        <div class="card p-5 rounded-2xl border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] shadow-xs relative overflow-hidden transition-all hover:shadow-md hover:border-amber-400">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    {{ app()->getLocale() === 'ar' ? 'تسجيلات التأخير اليوم' : 'Late Check-ins' }}
                </span>
                <span class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center text-sm shadow-inner">
                    <i class="fa-solid fa-user-clock"></i>
                </span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-amber-600 dark:text-amber-400">{{ $lateCount }}</span>
                <span class="text-xs font-bold text-amber-500">
                    {{ app()->getLocale() === 'ar' ? 'تجاوز موعد 09:00' : 'after 09:00 AM' }}
                </span>
            </div>
            <div class="mt-3 flex items-center justify-between text-xs pt-2 border-t border-slate-100 dark:border-[#15456E]/60 text-slate-500">
                <span>{{ app()->getLocale() === 'ar' ? 'تحت متابعة المشرف' : 'Under Review' }}</span>
                <span class="text-amber-600 dark:text-amber-400 font-bold">{{ app()->getLocale() === 'ar' ? 'تأخير مبرر' : 'Late Flagged' }}</span>
            </div>
        </div>

        <!-- 4. Absent / On Leave Today -->
        <div class="card p-5 rounded-2xl border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] shadow-xs relative overflow-hidden transition-all hover:shadow-md hover:border-blue-400">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    {{ app()->getLocale() === 'ar' ? 'إجازات وغياب اليوم' : 'Absent / On Leave' }}
                </span>
                <span class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center text-sm shadow-inner">
                    <i class="fa-solid fa-plane-departure"></i>
                </span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-slate-900 dark:text-white">{{ $absentCount }}</span>
                <span class="text-xs font-bold text-blue-500">
                    {{ app()->getLocale() === 'ar' ? 'إجازات رسمية / غير مسجل' : 'approved leave' }}
                </span>
            </div>
            <div class="mt-3 flex items-center justify-between text-xs pt-2 border-t border-slate-100 dark:border-[#15456E]/60 text-slate-500">
                <span>{{ app()->getLocale() === 'ar' ? 'طلبات الإجازة المعلقة:' : 'Pending Leaves:' }} <strong>0</strong></span>
                <span class="text-blue-500 font-bold">{{ app()->getLocale() === 'ar' ? 'محدث' : 'Synced' }}</span>
            </div>
        </div>
    </div>

    <!-- 3. MAIN SECTION: TODAY'S LIVE ATTENDANCE ROSTER & DEPARTMENTS -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-6">
        <!-- Live Attendance Roster (8 Cols) -->
        <div class="lg:col-span-8 card rounded-2xl border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] shadow-xs overflow-hidden flex flex-col">
            <div class="p-5 border-b border-slate-100 dark:border-[#15456E] flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-clipboard-user"></i>
                    </span>
                    <div>
                        <h3 class="font-black text-sm text-slate-900 dark:text-white mb-0">
                            {{ app()->getLocale() === 'ar' ? 'سجل الحضور والانصراف المباشر لليوم' : 'Today\'s Live Attendance Stream' }}
                        </h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mb-0">
                            {{ app()->getLocale() === 'ar' ? 'حركات البصمة والدوام المسجلة اليوم لكافة موظفي الشركة' : 'Real-time time clock and shift punches across all company departments' }}
                        </p>
                    </div>
                </div>

                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-xs font-bold text-xs">
                    {{ app()->getLocale() === 'ar' ? 'إدارة الموظفين' : 'Manage Staff' }}
                </a>
            </div>

            <div class="table-responsive flex-1">
                <table class="table mb-0 w-full text-xs">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-[#031827] text-slate-500">
                            <th class="p-3 font-bold">{{ app()->getLocale() === 'ar' ? 'الموظف' : 'Employee' }}</th>
                            <th class="p-3 font-bold">{{ app()->getLocale() === 'ar' ? 'القسم' : 'Department' }}</th>
                            <th class="p-3 font-bold text-center">{{ app()->getLocale() === 'ar' ? 'وقت الحضور' : 'Check In' }}</th>
                            <th class="p-3 font-bold text-center">{{ app()->getLocale() === 'ar' ? 'وقت الانصراف' : 'Check Out' }}</th>
                            <th class="p-3 font-bold text-center">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                            <th class="p-3 font-bold text-end">{{ app()->getLocale() === 'ar' ? 'الملف' : 'Dossier' }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-[#15456E]">
                        @forelse($todayAttendanceRecords as $record)
                            @php
                                $emp = $record->employee;
                                $usr = $emp?->user;
                            @endphp
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-[#0A4F78]/15 transition-colors">
                                <td class="p-3">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-[#0A4F78] text-white flex items-center justify-center font-bold text-xs">
                                            {{ substr($emp ? ($emp->first_name . ' ' . $emp->last_name) : 'U', 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900 dark:text-white">
                                                {{ $emp ? ($emp->first_name . ' ' . $emp->last_name) : 'Staff Member' }}
                                            </div>
                                            <div class="text-[10px] text-slate-500">
                                                {{ $emp->employee_number ?? ('EMP-' . $emp?->id) }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-3 text-slate-600 dark:text-slate-300 font-medium">
                                    {{ $emp?->department?->name ?? (app()->getLocale() === 'ar' ? 'عام' : 'General') }}
                                </td>
                                <td class="p-3 text-center">
                                    @if($record->check_in)
                                        <span class="font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                            {{ \Carbon\Carbon::parse($record->check_in)->format('h:i A') }}
                                        </span>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="p-3 text-center">
                                    @if($record->check_out)
                                        <span class="font-mono font-bold text-slate-700 dark:text-slate-300">
                                            {{ \Carbon\Carbon::parse($record->check_out)->format('h:i A') }}
                                        </span>
                                    @else
                                        <span class="badge badge-warning text-[10px] px-2 py-0.5 font-bold">
                                            {{ app()->getLocale() === 'ar' ? 'في الوردية' : 'On Duty' }}
                                        </span>
                                    @endif
                                </td>
                                <td class="p-3 text-center">
                                    @if($record->status === 'present')
                                        <span class="badge badge-success text-[10px] px-2 py-0.5 font-bold">
                                            {{ app()->getLocale() === 'ar' ? 'حاضر' : 'Present' }}
                                        </span>
                                    @elseif($record->status === 'late')
                                        <span class="badge badge-warning text-[10px] px-2 py-0.5 font-bold">
                                            {{ app()->getLocale() === 'ar' ? 'متأخر' : 'Late' }}
                                        </span>
                                    @elseif($record->status === 'half_day')
                                        <span class="badge badge-primary text-[10px] px-2 py-0.5 font-bold">
                                            {{ app()->getLocale() === 'ar' ? 'نصف يوم' : 'Half Day' }}
                                        </span>
                                    @else
                                        <span class="badge badge-danger text-[10px] px-2 py-0.5 font-bold">
                                            {{ app()->getLocale() === 'ar' ? 'غائب' : 'Absent' }}
                                        </span>
                                    @endif
                                </td>
                                <td class="p-3 text-end">
                                    @if($usr)
                                        <a href="{{ route('admin.users.show', $usr->id) }}" class="btn btn-outline btn-xs font-bold text-[11px] px-2 py-0.5">
                                            <i class="fa-solid fa-eye mr-1 ml-1 text-[10px]"></i>
                                            {{ app()->getLocale() === 'ar' ? 'عرض' : 'View' }}
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-6 text-center text-slate-400">
                                    {{ app()->getLocale() === 'ar' ? 'لا توجد تسجيلات حضور حتى الآن اليوم' : 'No attendance recorded yet today.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Department Breakdown (4 Cols) -->
        <div class="lg:col-span-4 card rounded-2xl border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] shadow-xs overflow-hidden flex flex-col">
            <div class="p-5 border-b border-slate-100 dark:border-[#15456E] flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-950/50 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-sitemap"></i>
                    </span>
                    <div>
                        <h3 class="font-black text-sm text-slate-900 dark:text-white mb-0">
                            {{ app()->getLocale() === 'ar' ? 'توزيع القوى العاملة' : 'Staff By Department' }}
                        </h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mb-0">
                            {{ app()->getLocale() === 'ar' ? 'توزيع الموظفين بالأقسام' : 'Headcount distribution' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="p-5 space-y-4 flex-1">
                @foreach($departmentHeadcounts as $dept)
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1.5">
                            <span class="font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                                {{ $dept['name'] }}
                            </span>
                            <span class="font-extrabold text-purple-600 dark:text-purple-400">
                                {{ $dept['count'] }} {{ app()->getLocale() === 'ar' ? 'موظف' : 'staff' }}
                            </span>
                        </div>
                        <div class="w-full bg-slate-100 dark:bg-[#031827] rounded-full h-2 overflow-hidden border border-slate-200/60 dark:border-[#15456E]">
                            <div class="bg-gradient-to-r from-purple-500 to-indigo-500 h-2 rounded-full" style="width: {{ $dept['percent'] }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-layouts.admin>
