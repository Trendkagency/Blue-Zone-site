<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'مركز قيادة وإشراف العمليات الميدانية للمناديب' : 'Field MR Supervision & Operations Command'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'إشراف فريق المناديب، متابعة تغطية الأطباء، ومؤشرات الالتزام الجغرافي المباشر' : 'Team supervision, doctor territory coverage, and real-time field route telemetry.'"
    :breadcrumbs="[
        (app()->getLocale() === 'ar' ? 'لوحة التحكم' : 'Dashboard') => route('admin.dashboard'),
        (app()->getLocale() === 'ar' ? 'إشراف المناديب' : 'MR Supervision') => route('admin.dashboard', ['view' => 'mr_manager'])
    ]"
>
    <!-- TOP VIEW SWITCHER (For Admins & Managers) -->
    @include('admin.dashboard.partials.view_switcher', ['currentView' => 'mr_manager'])

    <!-- 1. HERO MR MANAGER COMMAND BANNER -->
    <div class="bz-hero-banner">
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <!-- Left Info -->
            <div class="flex items-start sm:items-center gap-4">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center text-3xl font-black text-cyan-300 shadow-inner">
                    <i class="fa-solid fa-user-tie"></i>
                </div>

                <div>
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h2 class="text-xl sm:text-2xl font-black tracking-tight text-white mb-0">
                            {{ auth()->user()->name }}
                        </h2>
                        <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-black bg-cyan-400/20 text-cyan-200 border border-cyan-300/30">
                            <i class="fa-solid fa-user-doctor text-[11px]"></i>
                            {{ auth()->user()->role?->name ?? (app()->getLocale() === 'ar' ? 'مشرف الفريق الميداني (MR Line Manager)' : 'MR Line Manager') }}
                        </span>
                        @if($activeCycle)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white/10 text-white border border-white/15">
                                <i class="fa-solid fa-arrows-rotate text-[10px] text-cyan-300"></i>
                                {{ $activeCycle->name }} ({{ $daysRemainingInCycle }} {{ app()->getLocale() === 'ar' ? 'يوم متبقي' : 'days left' }})
                            </span>
                        @endif
                    </div>

                    <p class="text-xs sm:text-sm text-cyan-100/90 mt-1.5 mb-0 flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center gap-1">
                            <i class="fa-solid fa-map-location-dot text-cyan-300"></i>
                            {{ app()->getLocale() === 'ar' ? 'إشراف كافة قطاعات المملكة' : 'Kingdom-wide Supervision' }}
                        </span>
                        <span>•</span>
                        <span class="inline-flex items-center gap-1 text-white/80">
                            <i class="fa-regular fa-calendar text-cyan-300"></i>
                            {{ now()->translatedFormat('l, d F Y') }}
                        </span>
                    </p>

                    <!-- Shift Attendance Chip -->
                    <div class="mt-3 bz-hero-chip">
                        <i class="fa-solid fa-clock text-cyan-300"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'حالة دوامك اليوم:' : 'Attendance Today:' }}</span>
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
                                <button type="submit" class="btn btn-xs bg-cyan-500 hover:bg-cyan-600 text-white rounded-lg text-[10px] px-2.5 py-0.5 border-0 font-bold shadow-xs">
                                    <i class="fa-solid fa-fingerprint mr-1 ml-1"></i>
                                    {{ app()->getLocale() === 'ar' ? 'تسجيل الحضور الآن' : 'Clock In Now' }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Fast CTAs: Highlights LIVE OPS MAP -->
            <div class="flex items-center gap-2.5 flex-wrap">
                <a href="{{ route('admin.mr.live-map') }}" class="bz-hero-btn-accent animate-pulse">
                    <i class="fa-solid fa-earth-americas text-white"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'خريطة العمليات الميدانية المباشرة' : 'Live Ops Field Map' }}</span>
                </a>

                <a href="{{ route('admin.mr.dashboard') }}" class="bz-hero-btn-secondary">
                    <i class="fa-solid fa-calendar-check text-cyan-300"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'جداول الزيارات' : 'Visit Schedules' }}</span>
                </a>

                <a href="{{ route('admin.mr.contacts.index') }}" class="bz-hero-btn-secondary">
                    <i class="fa-solid fa-user-doctor text-emerald-300"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'دليل الأطباء' : 'Doctors Portfolio' }}</span>
                </a>
            </div>
        </div>
    </div>

    <!-- 2. TOP 4 VIBRANT FIELD MR SUPERVISOR KPI CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- 1. Total Reps Supervised -->
        <div class="card p-5 rounded-2xl border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] shadow-xs relative overflow-hidden transition-all hover:shadow-md hover:border-cyan-400">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    {{ app()->getLocale() === 'ar' ? 'فريق المناديب الميدانيين' : 'Field Medical Reps' }}
                </span>
                <span class="w-10 h-10 rounded-xl bg-cyan-100 dark:bg-cyan-950/60 text-[#0A4F78] dark:text-cyan-400 flex items-center justify-center text-sm shadow-inner">
                    <i class="fa-solid fa-users"></i>
                </span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-slate-900 dark:text-white">{{ $totalRepsCount }}</span>
                <span class="text-xs font-bold text-cyan-600 dark:text-cyan-400">
                    {{ app()->getLocale() === 'ar' ? 'مندوب دعاية معتمد' : 'licensed reps' }}
                </span>
            </div>
            <div class="mt-3 flex items-center justify-between text-xs pt-2 border-t border-slate-100 dark:border-[#15456E]/60 text-slate-500">
                <span>{{ app()->getLocale() === 'ar' ? 'مندوبون في الميدان اليوم:' : 'Active today:' }} <strong class="text-emerald-600 dark:text-emerald-400">{{ $activeRepsTodayCount }}</strong></span>
                <a href="{{ route('admin.dashboard', ['view' => 'mr']) }}" class="text-cyan-600 dark:text-cyan-400 font-bold hover:underline">
                    {{ app()->getLocale() === 'ar' ? 'عرض كمندوب ↗' : 'Rep View ↗' }}
                </a>
            </div>
        </div>

        <!-- 2. Team Planned Visits Today -->
        <div class="card p-5 rounded-2xl border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] shadow-xs relative overflow-hidden transition-all hover:shadow-md hover:border-emerald-400">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    {{ app()->getLocale() === 'ar' ? 'خطة زيارات الفريق لليوم' : 'Team Route Today' }}
                </span>
                <span class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-sm shadow-inner">
                    <i class="fa-solid fa-calendar-check"></i>
                </span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-slate-900 dark:text-white">{{ $teamCompletedToday }} / {{ $teamPlannedToday }}</span>
                <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">
                    ({{ $teamProgressRate }}%)
                </span>
            </div>
            <div class="mt-3 flex items-center justify-between text-xs pt-2 border-t border-slate-100 dark:border-[#15456E]/60 text-slate-500">
                <span>{{ app()->getLocale() === 'ar' ? 'إجمالي الزيارات المنفذة' : 'Completed visits' }}</span>
                <span class="text-emerald-600 dark:text-emerald-400 font-bold">{{ $teamCompletedToday }} {{ app()->getLocale() === 'ar' ? 'زيارة' : 'visits' }}</span>
            </div>
        </div>

        <!-- 3. GPS Compliance Rate -->
        <div class="card p-5 rounded-2xl border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] shadow-xs relative overflow-hidden transition-all hover:shadow-md hover:border-amber-400">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    {{ app()->getLocale() === 'ar' ? 'دقة التوثيق الجغرافي (GPS)' : 'GPS Verification Rate' }}
                </span>
                <span class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center text-sm shadow-inner">
                    <i class="fa-solid fa-location-crosshairs"></i>
                </span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-slate-900 dark:text-white">{{ $teamGpsAccuracy }}%</span>
                <span class="text-xs font-bold text-amber-600 dark:text-amber-400">
                    {{ app()->getLocale() === 'ar' ? 'مطابقة ميدانية' : 'verified coords' }}
                </span>
            </div>
            <div class="mt-3 flex items-center justify-between text-xs pt-2 border-t border-slate-100 dark:border-[#15456E]/60 text-slate-500">
                <span>{{ app()->getLocale() === 'ar' ? 'نطاق السماح:' : 'Fence tolerance:' }} <strong>150m</strong></span>
                <span class="text-emerald-600 dark:text-emerald-400 font-bold">{{ app()->getLocale() === 'ar' ? 'مطابق للمعايير ✓' : 'Compliant ✓' }}</span>
            </div>
        </div>

        <!-- 4. Cycle Doctors Coverage -->
        <div class="card p-5 rounded-2xl border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] shadow-xs relative overflow-hidden transition-all hover:shadow-md hover:border-purple-400">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    {{ app()->getLocale() === 'ar' ? 'تغطية قائمة الأطباء بالدورة' : 'Cycle Doctor Target' }}
                </span>
                <span class="w-10 h-10 rounded-xl bg-purple-100 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center text-sm shadow-inner">
                    <i class="fa-solid fa-bullseye"></i>
                </span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-slate-900 dark:text-white">{{ $teamCycleVisitsDone }} / {{ $teamCycleTargetVisits }}</span>
                <span class="text-xs font-bold text-purple-600 dark:text-purple-400">
                    ({{ $teamCycleProgressPct }}%)
                </span>
            </div>
            <div class="mt-3 flex items-center justify-between text-xs pt-2 border-t border-slate-100 dark:border-[#15456E]/60 text-slate-500">
                <span>{{ app()->getLocale() === 'ar' ? 'إجمالي الأطباء المستهدفين:' : 'Target doctors:' }} <strong>{{ $totalDoctorsCount }}</strong></span>
                <span class="text-purple-600 dark:text-purple-400 font-bold">{{ $teamCycleProgressPct }}%</span>
            </div>
        </div>
    </div>

    <!-- 3. TEAM REPS LEADERBOARD & RECENT EXECUTED VISITS -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-6">
        <!-- Team Roster & Performance Table (8 Cols) -->
        <div class="lg:col-span-8 card rounded-2xl border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] shadow-xs overflow-hidden flex flex-col">
            <div class="p-5 border-b border-slate-100 dark:border-[#15456E] flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-cyan-100 dark:bg-cyan-950/50 text-[#0A4F78] dark:text-cyan-400 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-ranking-star"></i>
                    </span>
                    <div>
                        <h3 class="font-black text-sm text-slate-900 dark:text-white mb-0">
                            {{ app()->getLocale() === 'ar' ? 'سجل متابعة وإنجاز فريق المناديب اليوم' : 'Medical Reps Field Performance Leaderboard' }}
                        </h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mb-0">
                            {{ app()->getLocale() === 'ar' ? 'متابعة خطة كل مندوب، عدد الزيارات المنجزة، والتوثيق الجغرافي' : 'Route execution, visits completed, and GPS validation per field agent' }}
                        </p>
                    </div>
                </div>

                <a href="{{ route('admin.mr.dashboard') }}" class="btn btn-secondary btn-xs font-bold text-xs">
                    {{ app()->getLocale() === 'ar' ? 'عرض التفاصيل' : 'All Reps' }}
                </a>
            </div>

            <div class="table-responsive flex-1">
                <table class="table mb-0 w-full text-xs">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-[#031827] text-slate-500">
                            <th class="p-3 font-bold">{{ app()->getLocale() === 'ar' ? 'المندوب' : 'Representative' }}</th>
                            <th class="p-3 font-bold">{{ app()->getLocale() === 'ar' ? 'المنطقة المعينة' : 'Assigned Territory' }}</th>
                            <th class="p-3 font-bold text-center">{{ app()->getLocale() === 'ar' ? 'زيارات اليوم' : 'Today Visits' }}</th>
                            <th class="p-3 font-bold text-center">{{ app()->getLocale() === 'ar' ? 'الإنجاز' : 'Progress' }}</th>
                            <th class="p-3 font-bold text-center">{{ app()->getLocale() === 'ar' ? 'الحضور' : 'Attendance' }}</th>
                            <th class="p-3 font-bold text-end">{{ app()->getLocale() === 'ar' ? 'لوحة المندوب' : 'Portal' }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-[#15456E]">
                        @foreach($teamRepsData as $rep)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-[#0A4F78]/15 transition-colors">
                                <td class="p-3">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-[#0A4F78] text-white flex items-center justify-center font-bold text-xs">
                                            {{ substr($rep['user']->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900 dark:text-white">
                                                {{ $rep['user']->name }}
                                            </div>
                                            <div class="text-[10px] text-slate-500">
                                                {{ $rep['user']->email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-3 text-slate-600 dark:text-slate-300 font-medium">
                                    <i class="fa-solid fa-location-dot text-cyan-500 text-[10px] mr-1 ml-1"></i>
                                    {{ $rep['territory'] }}
                                </td>
                                <td class="p-3 text-center">
                                    <span class="font-extrabold text-slate-900 dark:text-white">
                                        {{ $rep['completed_visits'] }} / {{ $rep['planned_visits'] }}
                                    </span>
                                </td>
                                <td class="p-3 text-center">
                                    <div class="inline-flex items-center gap-2">
                                        <div class="w-16 bg-slate-100 dark:bg-[#031827] rounded-full h-2 overflow-hidden border border-slate-200/60 dark:border-[#15456E]">
                                            <div class="bg-gradient-to-r from-cyan-500 to-emerald-500 h-2 rounded-full" style="width: {{ $rep['progress_pct'] }}%"></div>
                                        </div>
                                        <span class="font-bold text-[11px] text-slate-700 dark:text-slate-300">{{ $rep['progress_pct'] }}%</span>
                                    </div>
                                </td>
                                <td class="p-3 text-center">
                                    @if($rep['attendance'])
                                        <span class="badge badge-success text-[10px] px-2 py-0.5 font-bold">
                                            <i class="fa-solid fa-circle-check mr-1 ml-1 text-[9px]"></i>
                                            {{ \Carbon\Carbon::parse($rep['attendance']->check_in)->format('h:i A') }}
                                        </span>
                                    @else
                                        <span class="badge badge-warning text-[10px] px-2 py-0.5 font-bold">
                                            {{ app()->getLocale() === 'ar' ? 'لم يسجل' : 'Not In' }}
                                        </span>
                                    @endif
                                </td>
                                <td class="p-3 text-end">
                                    <a href="{{ route('admin.dashboard', ['view' => 'mr', 'mr_id' => $rep['user']->id]) }}" class="btn btn-outline btn-xs font-bold text-[11px] px-2 py-0.5">
                                        <i class="fa-solid fa-arrow-up-right-from-square mr-1 ml-1 text-[10px]"></i>
                                        {{ app()->getLocale() === 'ar' ? 'عرض' : 'View' }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Field Visits Stream (4 Cols) -->
        <div class="lg:col-span-4 card rounded-2xl border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] shadow-xs overflow-hidden flex flex-col">
            <div class="p-5 border-b border-slate-100 dark:border-[#15456E] flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-stethoscope"></i>
                    </span>
                    <div>
                        <h3 class="font-black text-sm text-slate-900 dark:text-white mb-0">
                            {{ app()->getLocale() === 'ar' ? 'آخر الزيارات المنفذة' : 'Latest Field Visits' }}
                        </h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mb-0">
                            {{ app()->getLocale() === 'ar' ? 'التقارير المرفوعة مباشرة من العيادات' : 'Live check-ins stream' }}
                        </p>
                    </div>
                </div>

                <a href="{{ route('admin.mr.visits.index') }}" class="btn btn-secondary btn-xs font-bold text-xs">
                    {{ app()->getLocale() === 'ar' ? 'الكل' : 'All' }}
                </a>
            </div>

            <div class="p-4 space-y-3 flex-1 overflow-y-auto max-h-[380px]">
                @forelse($recentTeamVisits as $visit)
                    <div class="p-3.5 rounded-xl border border-slate-200/70 dark:border-[#15456E] bg-slate-50/50 dark:bg-[#031827]/40 transition-all hover:border-[#0A4F78]">
                        <div class="flex items-center justify-between gap-2 mb-1.5">
                            <span class="font-bold text-xs text-slate-900 dark:text-white">
                                {{ $visit->contact?->name ?? 'Dr. Clinic' }}
                            </span>
                            @if($visit->gps_verified)
                                <span class="badge badge-success text-[10px] px-1.5 py-0.5 font-bold">
                                    <i class="fa-solid fa-location-crosshairs mr-1 ml-1 text-[9px]"></i>
                                    GPS ✓
                                </span>
                            @else
                                <span class="badge badge-warning text-[10px] px-1.5 py-0.5 font-bold">
                                    Manual
                                </span>
                            @endif
                        </div>
                        <div class="text-[11px] text-slate-500 flex items-center justify-between gap-2">
                            <span>
                                <i class="fa-solid fa-user-doctor text-cyan-500 mr-1 ml-1"></i>
                                {{ $visit->representative?->name ?? 'Rep' }}
                            </span>
                            <span class="font-mono text-[10px]">
                                {{ $visit->checkin_at ? \Carbon\Carbon::parse($visit->checkin_at)->diffForHumans() : 'Today' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-slate-400">
                        {{ app()->getLocale() === 'ar' ? 'لا توجد زيارات مسجلة حديثاً' : 'No visits recorded yet.' }}
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.admin>
