<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'لوحة المتابعة الميدانية للمندوب' : 'Medical Rep Overview Dashboard'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'متابعة خط السير اليومي، إنجاز زيارات الأطباء، ومؤشرات التغطية الميدانية' : 'Real-time field route execution, doctor coverage progress, and territory performance.'"
    :breadcrumbs="[
        (app()->getLocale() === 'ar' ? 'نظام CRM' : 'CRM') => route('admin.mr.dashboard'),
        (app()->getLocale() === 'ar' ? 'لوحة المندوب' : 'MR Overview') => route('admin.dashboard')
    ]"
>
    <!-- TOP VIEW SWITCHER (Only visible for Admins / Line Managers who can manage MRs) -->
    @include('admin.dashboard.partials.view_switcher', ['currentView' => 'mr'])

    @if(auth()->user() && auth()->user()->canManageAllMr() && !empty($medicalReps) && count($medicalReps) > 1)
        <div class="mb-4 flex items-center justify-between flex-wrap gap-3 bg-white dark:bg-[#062B49] p-3 rounded-2xl border border-slate-200/80 dark:border-[#15456E] shadow-xs">
            <form method="GET" action="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                <input type="hidden" name="view" value="mr">
                <span class="text-xs text-slate-500 font-semibold">{{ app()->getLocale() === 'ar' ? 'عرض كمندوب:' : 'View as Rep:' }}</span>
                <select name="mr_id" onchange="this.form.submit()" class="form-select text-xs py-1.5 px-3 rounded-lg border-slate-200 dark:border-[#15456E] dark:bg-[#031827] dark:text-white">
                    @foreach($medicalReps as $rep)
                        <option value="{{ $rep->id }}" {{ $repId == $rep->id ? 'selected' : '' }}>
                            {{ $rep->name }} ({{ $rep->area?->name ?? ($rep->city?->name ?? 'Rep') }})
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    @endif

    <!-- 1. HERO REP COMMAND BANNER -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-[#062B49] via-[#0A4F78] to-[#15456E] text-white p-6 sm:p-7 mb-6 shadow-md border border-[#15456E]">
        <!-- Decorative Ambient Glows -->
        <div class="absolute -right-16 -top-16 w-64 h-64 bg-cyan-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-16 -bottom-16 w-64 h-64 bg-blue-500/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <!-- Left Info -->
            <div class="flex items-start sm:items-center gap-4">
                <div class="relative">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center text-2xl sm:text-3xl font-black text-white shadow-inner">
                        @if($repUser->avatar_url)
                            <img src="{{ $repUser->avatar_url }}" alt="{{ $repUser->name }}" class="w-full h-full object-cover rounded-2xl">
                        @else
                            {{ substr($repUser->name, 0, 1) }}
                        @endif
                    </div>
                    <span class="absolute -bottom-1 -right-1 flex h-4 w-4">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-4 w-4 bg-emerald-500 border-2 border-[#062B49]"></span>
                    </span>
                </div>

                <div>
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h2 class="text-xl sm:text-2xl font-black tracking-tight text-white mb-0">
                            {{ $repUser->name }}
                        </h2>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-cyan-400/20 text-cyan-200 border border-cyan-300/30">
                            <i class="fa-solid fa-user-doctor text-[10px]"></i>
                            {{ app()->getLocale() === 'ar' ? 'مندوب دعاية طبية' : 'Medical Representative' }}
                        </span>
                        @if($activeCycle)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white/10 text-white border border-white/15">
                                <i class="fa-solid fa-arrows-rotate text-[10px] text-cyan-300"></i>
                                {{ $activeCycle->name }}
                            </span>
                        @endif
                    </div>

                    <p class="text-xs sm:text-sm text-cyan-100/90 mt-1.5 mb-0 flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center gap-1">
                            <i class="fa-solid fa-location-dot text-cyan-300"></i>
                            {{ $territoryName }}
                        </span>
                        <span>•</span>
                        <span class="inline-flex items-center gap-1 text-white/80">
                            <i class="fa-regular fa-calendar text-cyan-300"></i>
                            {{ now()->translatedFormat('l, d F Y') }}
                        </span>
                    </p>

                    <!-- Shift Attendance Chip -->
                    <div class="mt-3 inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-black/20 backdrop-blur-sm border border-white/10 text-xs">
                        <i class="fa-solid fa-clock text-cyan-300"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'سجل دوام اليوم:' : 'Attendance Today:' }}</span>
                        @if($todayAttendance && $todayAttendance->check_in)
                            <span class="font-bold text-emerald-300 flex items-center gap-1">
                                <i class="fa-solid fa-circle-check text-[10px]"></i>
                                {{ app()->getLocale() === 'ar' ? 'تم تسجيل الحضور' : 'Checked In' }} ({{ \Carbon\Carbon::parse($todayAttendance->check_in)->format('h:i A') }})
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

            <!-- Right Fast CTAs -->
            <div class="flex items-center gap-2.5 flex-wrap">
                <a href="{{ route('admin.mr.visits.index') }}" class="btn font-bold text-xs sm:text-sm px-4 py-2.5 rounded-xl shadow-sm bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white border-0 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-list-check"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'سجل الزيارات والتقارير' : 'Visits History & Entry' }}</span>
                </a>

                <a href="{{ route('admin.mr.dashboard') }}" class="btn font-bold text-xs sm:text-sm px-4 py-2.5 rounded-xl shadow-sm bg-white/15 hover:bg-white/25 text-white border border-white/20 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-calendar-days text-cyan-300"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'جدول المواعيد' : 'Field Schedules' }}</span>
                </a>

                @if(auth()->user() && auth()->user()->canManageAllMr())
                <a href="{{ route('admin.mr.live-map') }}" class="btn font-bold text-xs sm:text-sm px-4 py-2.5 rounded-xl shadow-sm bg-white/10 hover:bg-white/20 text-white border border-white/15 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-map-location-dot text-amber-300"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'خريطة السير' : 'Route Map' }}</span>
                </a>
                @endif
            </div>
        </div>

        <!-- Today's Route Progress Bar -->
        <div class="mt-5 pt-4 border-t border-white/10 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-2">
                <span class="font-bold text-white">{{ app()->getLocale() === 'ar' ? 'مسار زيارات اليوم:' : "Today's Route Progress:" }}</span>
                <span class="text-cyan-200 font-extrabold">{{ $todayCompletedCount }} / {{ $todayPlannedCount }} {{ app()->getLocale() === 'ar' ? 'زيارات منجزة' : 'visits completed' }}</span>
                <span class="text-white/60">({{ $todayProgressRate }}%)</span>
            </div>
            <div class="w-full sm:w-64 bg-black/30 rounded-full h-2 overflow-hidden border border-white/10">
                <div class="bg-gradient-to-r from-cyan-400 to-emerald-400 h-2 rounded-full transition-all duration-700" style="width: {{ $todayProgressRate }}%"></div>
            </div>
        </div>
    </div>

    <!-- 2. TOP 4 VIBRANT MR KPI METRIC CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- 1. Today's Planned Visits -->
        <div class="card p-5 rounded-2xl border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] shadow-xs relative overflow-hidden transition-all hover:shadow-md hover:border-[#0A4F78]">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    {{ app()->getLocale() === 'ar' ? 'جدول زيارات اليوم' : "Today's Field Route" }}
                </span>
                <span class="w-10 h-10 rounded-xl bg-cyan-100 dark:bg-cyan-950/60 text-[#0A4F78] dark:text-cyan-300 flex items-center justify-center text-sm shadow-inner">
                    <i class="fa-solid fa-calendar-check"></i>
                </span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-slate-900 dark:text-white">{{ $todayPlannedCount }}</span>
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400">
                    {{ app()->getLocale() === 'ar' ? 'عيادات مجدولة' : 'planned clinics' }}
                </span>
            </div>
            <div class="mt-3 flex items-center justify-between text-xs pt-2 border-t border-slate-100 dark:border-[#15456E]/60">
                <span class="text-emerald-600 dark:text-emerald-400 font-bold flex items-center gap-1">
                    <i class="fa-solid fa-circle-check text-[10px]"></i>
                    {{ $todayCompletedCount }} {{ app()->getLocale() === 'ar' ? 'منجز' : 'completed' }}
                </span>
                <span class="text-amber-600 dark:text-amber-400 font-bold flex items-center gap-1">
                    <i class="fa-solid fa-hourglass-half text-[10px]"></i>
                    {{ max(0, $todayPlannedCount - $todayCompletedCount) }} {{ app()->getLocale() === 'ar' ? 'متبقي' : 'pending' }}
                </span>
            </div>
        </div>

        <!-- 2. Monthly Cycle Coverage Target -->
        <div class="card p-5 rounded-2xl border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] shadow-xs relative overflow-hidden transition-all hover:shadow-md hover:border-[#0A4F78]">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    {{ app()->getLocale() === 'ar' ? 'مستهدف الدورة الشهرية' : 'Monthly Cycle Target' }}
                </span>
                <span class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-950/60 text-blue-600 dark:text-blue-300 flex items-center justify-center text-sm shadow-inner">
                    <i class="fa-solid fa-bullseye"></i>
                </span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-blue-600 dark:text-blue-400">{{ $cycleVisitsDone }}</span>
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400">
                    / {{ $cycleTargetVisits }} {{ app()->getLocale() === 'ar' ? 'زيارة مطلوبة' : 'target' }}
                </span>
            </div>
            <div class="mt-3 pt-2 border-t border-slate-100 dark:border-[#15456E]/60">
                <div class="flex items-center justify-between text-xs mb-1">
                    <span class="font-extrabold text-[#0A4F78] dark:text-cyan-300">{{ $cycleProgressPct }}% {{ app()->getLocale() === 'ar' ? 'نسبة الإنجاز' : 'Achieved' }}</span>
                    <span class="text-slate-400">{{ $daysRemainingInCycle }} {{ app()->getLocale() === 'ar' ? 'أيام متبقية' : 'days left' }}</span>
                </div>
                <div class="w-full bg-slate-100 dark:bg-[#031827] rounded-full h-1.5 overflow-hidden">
                    <div class="bg-blue-600 h-1.5 rounded-full transition-all duration-700" style="width: {{ min(100, $cycleProgressPct) }}%"></div>
                </div>
            </div>
        </div>

        <!-- 3. Assigned Doctor Portfolio -->
        <div class="card p-5 rounded-2xl border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] shadow-xs relative overflow-hidden transition-all hover:shadow-md hover:border-[#0A4F78]">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    {{ app()->getLocale() === 'ar' ? 'محفظة الأطباء المعتمدة' : 'Assigned Doctor Portfolio' }}
                </span>
                <span class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-300 flex items-center justify-center text-sm shadow-inner">
                    <i class="fa-solid fa-user-doctor"></i>
                </span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-emerald-600 dark:text-emerald-400">{{ $assignedDoctorsCount }}</span>
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400">
                    {{ app()->getLocale() === 'ar' ? 'طبيب ومستشار' : 'doctors' }}
                </span>
            </div>
            <div class="mt-3 flex items-center gap-1.5 text-[11px] pt-2 border-t border-slate-100 dark:border-[#15456E]/60 flex-wrap">
                <span class="px-1.5 py-0.5 rounded bg-purple-100 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 font-black">A+: {{ $classCounts['A+'] ?? 0 }}</span>
                <span class="px-1.5 py-0.5 rounded bg-blue-100 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 font-bold">A: {{ $classCounts['A'] ?? 0 }}</span>
                <span class="px-1.5 py-0.5 rounded bg-teal-100 dark:bg-teal-950/60 text-teal-700 dark:text-teal-300 font-bold">B: {{ $classCounts['B'] ?? 0 }}</span>
                <span class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold">C: {{ $classCounts['C'] ?? 0 }}</span>
            </div>
        </div>

        <!-- 4. GPS Geofence Accuracy & Field Compliance -->
        <div class="card p-5 rounded-2xl border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] shadow-xs relative overflow-hidden transition-all hover:shadow-md hover:border-[#0A4F78]">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-black uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    {{ app()->getLocale() === 'ar' ? 'دقة التحقق الجغرافي GPS' : 'GPS Geofence Compliance' }}
                </span>
                <span class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-300 flex items-center justify-center text-sm shadow-inner">
                    <i class="fa-solid fa-satellite-dish"></i>
                </span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl font-black text-amber-600 dark:text-amber-400">{{ $gpsAccuracyPct }}%</span>
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400">
                    {{ app()->getLocale() === 'ar' ? 'داخل نطاق العيادة' : 'in-clinic radius' }}
                </span>
            </div>
            <div class="mt-3 flex items-center justify-between text-xs pt-2 border-t border-slate-100 dark:border-[#15456E]/60 text-slate-500 dark:text-slate-400">
                <span class="flex items-center gap-1 font-semibold">
                    <i class="fa-solid fa-award text-amber-500"></i>
                    {{ $cycleAchievedPoints }} {{ app()->getLocale() === 'ar' ? 'نقطة مكتسبة' : 'pts' }}
                </span>
                <span class="flex items-center gap-1">
                    <i class="fa-regular fa-clock text-slate-400"></i>
                    {{ $avgVisitMinutes }} {{ app()->getLocale() === 'ar' ? 'د/زيارة' : 'min avg' }}
                </span>
            </div>
        </div>
    </div>

    <!-- 3. MAIN DASHBOARD CONTENT (Two-Column Layout) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        
        <!-- LEFT 2 COLUMNS: Today's Scheduled Field Route (The Core Daily Focus) -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Today's Visits Agenda Card -->
            <div class="card rounded-3xl border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] shadow-sm overflow-hidden">
                <div class="p-5 sm:p-6 border-b border-slate-100 dark:border-[#15456E] flex items-center justify-between flex-wrap gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-[#0A4F78]/10 dark:bg-cyan-950/60 text-[#0A4F78] dark:text-cyan-400 flex items-center justify-center text-lg">
                            <i class="fa-solid fa-route"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-black text-slate-900 dark:text-white mb-0">
                                {{ app()->getLocale() === 'ar' ? 'خط سير الزيارات الميدانية اليوم' : "Today's Field Route & Scheduled Clinics" }}
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 mb-0">
                                {{ now()->translatedFormat('l, d F Y') }} • {{ count($todayScheduledVisits) }} {{ app()->getLocale() === 'ar' ? 'زيارات مجدولة' : 'visits on route' }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.mr.dashboard') }}" class="btn btn-secondary text-xs font-bold px-3 py-1.5 rounded-xl">
                            <i class="fa-solid fa-calendar mr-1 ml-1 text-cyan-500"></i>
                            {{ app()->getLocale() === 'ar' ? 'عرض التقويم' : 'Open Calendar' }}
                        </a>
                    </div>
                </div>

                <div class="p-5 sm:p-6">
                    @if(count($todayScheduledVisits) > 0)
                        <div class="space-y-4">
                            @foreach($todayScheduledVisits as $visit)
                                @php
                                    $doc = $visit->contact;
                                    $isDone = $visit->status === 'completed';
                                    $isInProg = $visit->status === 'in_progress';
                                    $classCode = $doc?->classification?->code ?? 'A';
                                @endphp
                                <div class="p-4 rounded-2xl border transition-all duration-200 {{ $isDone ? 'bg-emerald-50/40 dark:bg-emerald-950/10 border-emerald-200/80 dark:border-emerald-900/50' : ($isInProg ? 'bg-cyan-50/60 dark:bg-cyan-950/20 border-cyan-300 dark:border-cyan-800 ring-2 ring-cyan-500/20' : 'bg-slate-50/60 dark:bg-[#031827]/70 border-slate-200 dark:border-[#15456E] hover:border-[#0A4F78]') }}">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                        <!-- Doctor & Clinic Info -->
                                        <div class="flex items-start gap-3.5">
                                            <!-- Time Badge -->
                                            <div class="flex-shrink-0 text-center w-14 py-2 px-1 rounded-xl {{ $isDone ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300' : 'bg-white dark:bg-[#062B49] text-slate-800 dark:text-white border border-slate-200 dark:border-[#15456E]' }} shadow-2xs">
                                                <span class="block text-[11px] font-black uppercase leading-tight">
                                                    {{ \Carbon\Carbon::parse($visit->scheduled_at)->format('h:i') }}
                                                </span>
                                                <span class="block text-[9px] font-bold text-slate-400">
                                                    {{ \Carbon\Carbon::parse($visit->scheduled_at)->format('A') }}
                                                </span>
                                            </div>

                                            <div>
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <h4 class="text-sm font-black text-slate-900 dark:text-white mb-0">
                                                        {{ $doc?->name ?? 'Doctor Name' }}
                                                    </h4>

                                                    <!-- Classification Badge -->
                                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-black {{ $classCode === 'A+' ? 'bg-purple-100 text-purple-800 dark:bg-purple-950 dark:text-purple-300' : ($classCode === 'A' ? 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300' : 'bg-teal-100 text-teal-800 dark:bg-teal-950 dark:text-teal-300') }}">
                                                        Class {{ $classCode }}
                                                    </span>

                                                    <!-- Specialty Badge -->
                                                    @if($doc?->specialty)
                                                        <span class="text-[11px] font-semibold text-slate-500 dark:text-slate-400">
                                                            • {{ $doc->specialty->name }}
                                                        </span>
                                                    @endif
                                                </div>

                                                <!-- Clinic / Location -->
                                                <div class="text-xs text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-2 flex-wrap">
                                                    <span class="inline-flex items-center gap-1 font-medium">
                                                        <i class="fa-solid fa-hospital text-cyan-600 dark:text-cyan-400 text-[10px]"></i>
                                                        {{ $doc?->workplace_name ?? ($doc?->city?->name ?? 'Private Clinic') }}
                                                    </span>
                                                    @if($doc?->phone)
                                                        <span>•</span>
                                                        <a href="tel:{{ $doc->phone }}" class="text-[#0A4F78] dark:text-cyan-400 hover:underline flex items-center gap-1">
                                                            <i class="fa-solid fa-phone text-[9px]"></i>
                                                            {{ $doc->phone }}
                                                        </a>
                                                    @endif
                                                </div>

                                                @if(!empty($visit->notes))
                                                    <p class="text-[11px] text-slate-600 dark:text-slate-300 italic mt-1.5 mb-0">
                                                        "{{ Str::limit($visit->notes, 80) }}"
                                                    </p>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Status & Action CTAs -->
                                        <div class="flex items-center sm:flex-col sm:items-end justify-between sm:justify-center gap-2 flex-shrink-0">
                                            @if($isDone)
                                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800">
                                                    <i class="fa-solid fa-circle-check text-[10px]"></i>
                                                    {{ app()->getLocale() === 'ar' ? 'تمت الزيارة' : 'Completed' }}
                                                </span>
                                            @elseif($isInProg)
                                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-cyan-100 text-cyan-800 dark:bg-cyan-950/80 dark:text-cyan-300 border border-cyan-300 animate-pulse">
                                                    <i class="fa-solid fa-spinner fa-spin text-[10px]"></i>
                                                    {{ app()->getLocale() === 'ar' ? 'جارية الآن' : 'In Progress' }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                                    <i class="fa-regular fa-clock text-[10px]"></i>
                                                    {{ app()->getLocale() === 'ar' ? 'مجدولة' : 'Planned' }}
                                                </span>
                                            @endif

                                            <div class="flex items-center gap-1.5">
                                                @if(!$isDone)
                                                    <a href="{{ route('admin.mr.dashboard') }}" class="btn btn-primary text-xs font-bold px-3 py-1 rounded-lg bg-[#0A4F78] hover:bg-[#062B49] text-white">
                                                        <i class="fa-solid fa-play mr-1 ml-1 text-[9px]"></i>
                                                        {{ app()->getLocale() === 'ar' ? 'بدء الزيارة' : 'Start Visit' }}
                                                    </a>
                                                @endif
                                                @if($doc)
                                                    <a href="{{ route('admin.mr.contacts.show', $doc->id) }}" class="btn btn-secondary text-xs px-2.5 py-1 rounded-lg" title="{{ app()->getLocale() === 'ar' ? 'ملف الطبيب 360°' : 'Doctor Dossier' }}">
                                                        <i class="fa-solid fa-id-card text-slate-500"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <!-- Empty State for Today -->
                        <div class="text-center py-10 px-4">
                            <div class="w-16 h-16 rounded-3xl bg-slate-100 dark:bg-[#031827] text-slate-400 flex items-center justify-center text-2xl mx-auto mb-3">
                                <i class="fa-solid fa-clipboard-check"></i>
                            </div>
                            <h4 class="text-sm font-bold text-slate-800 dark:text-white mb-1">
                                {{ app()->getLocale() === 'ar' ? 'لا توجد زيارات مجدولة لهذا اليوم' : 'No Scheduled Visits for Today' }}
                            </h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto mb-4">
                                {{ app()->getLocale() === 'ar' ? 'يمكنك جدولة مواعيد جديدة مع أطباء منطقتك أو تسجيل زيارة ميدانية مباشرة.' : 'You can schedule new appointments from your doctor portfolio or log a direct walk-in visit.' }}
                            </p>
                            <div class="flex items-center justify-center gap-3">
                                <a href="{{ route('admin.mr.dashboard') }}" class="btn btn-primary text-xs font-bold px-4 py-2 rounded-xl bg-[#0A4F78] text-white">
                                    <i class="fa-solid fa-plus-circle mr-1.5 ml-1.5"></i>
                                    {{ app()->getLocale() === 'ar' ? 'تسجيل زيارة مباشرة' : 'Direct Visit Entry' }}
                                </a>
                                <a href="{{ route('admin.mr.dashboard') }}" class="btn btn-secondary text-xs font-bold px-4 py-2 rounded-xl">
                                    <i class="fa-solid fa-calendar-plus mr-1.5 ml-1.5"></i>
                                    {{ app()->getLocale() === 'ar' ? 'جدولة موعد في التقويم' : 'Schedule on Calendar' }}
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Executed Visits History Feed -->
            <div class="card rounded-3xl border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 dark:border-[#15456E] flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <i class="fa-solid fa-clock-rotate-left text-[#0A4F78] dark:text-cyan-400"></i>
                        <h3 class="text-sm font-black text-slate-900 dark:text-white mb-0">
                            {{ app()->getLocale() === 'ar' ? 'سجل الزيارات المنجزة الأخيرة' : 'Recent Completed Visits & Clinical Feedback' }}
                        </h3>
                    </div>
                    <a href="{{ route('admin.mr.visits.index') }}" class="text-xs font-bold text-[#0A4F78] dark:text-cyan-400 hover:underline">
                        {{ app()->getLocale() === 'ar' ? 'عرض الكل' : 'View All Visits' }} &rarr;
                    </a>
                </div>

                <div class="divide-y divide-slate-100 dark:divide-[#15456E]/50">
                    @forelse($recentExecutedVisits as $execVisit)
                        <div class="p-4 hover:bg-slate-50/70 dark:hover:bg-[#031827]/40 transition-colors">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-xs text-slate-900 dark:text-white">
                                            {{ $execVisit->contact?->name ?? 'Dr. Specialist' }}
                                        </span>
                                        @if($execVisit->contact?->classification)
                                            <span class="text-[10px] font-black px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                                {{ $execVisit->contact->classification->code }}
                                            </span>
                                        @endif
                                        <span class="text-[10px] text-slate-400">
                                            {{ $execVisit->checkin_at ? \Carbon\Carbon::parse($execVisit->checkin_at)->diffForHumans() : ($execVisit->created_at ? $execVisit->created_at->diffForHumans() : 'Recently') }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-600 dark:text-slate-300 mt-1 mb-1">
                                        {{ $execVisit->notes ?? ($execVisit->summary ?? (app()->getLocale() === 'ar' ? 'تمت مناقشة المنتجات وتقديم العينات بنجاح.' : 'Detiled products and delivered sample formulations successfully.')) }}
                                    </p>
                                    @if($execVisit->products && count($execVisit->products) > 0)
                                        <div class="flex items-center gap-1.5 flex-wrap mt-1.5">
                                            <span class="text-[10px] text-slate-400 font-semibold">{{ app()->getLocale() === 'ar' ? 'المنتجات:' : 'Products:' }}</span>
                                            @foreach($execVisit->products as $p)
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-cyan-50 dark:bg-cyan-950/60 text-[#0A4F78] dark:text-cyan-300 border border-cyan-200 dark:border-cyan-800">
                                                    {{ $p->name_en ?? $p->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <div class="text-right rtl:text-left flex-shrink-0">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                        <i class="fa-solid fa-check text-[9px]"></i>
                                        {{ app()->getLocale() === 'ar' ? 'معتمدة' : 'Verified' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-xs text-slate-400">
                            {{ app()->getLocale() === 'ar' ? 'لم يتم تسجيل زيارات ميدانية بعد.' : 'No recent visits recorded yet.' }}
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- RIGHT 1 COLUMN: Doctor Portfolio & Detailing Samples Focus -->
        <div class="space-y-6">

            <!-- Doctor Tier Classification Card -->
            <div class="card p-5 rounded-3xl border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-black text-slate-900 dark:text-white mb-0 flex items-center gap-2">
                        <i class="fa-solid fa-ranking-star text-amber-500"></i>
                        {{ app()->getLocale() === 'ar' ? 'تغطية فئات الأطباء (Classes)' : 'Doctor Classes Coverage' }}
                    </h3>
                    <a href="{{ route('admin.mr.contacts.index') }}" class="text-[11px] font-bold text-[#0A4F78] dark:text-cyan-400 hover:underline">
                        {{ app()->getLocale() === 'ar' ? 'الدليل' : 'Directory' }}
                    </a>
                </div>

                <div class="space-y-3.5">
                    @foreach(['A+' => ['label' => 'Class A+ (Key Opinion Leaders)', 'color' => 'purple', 'target' => '4 visits/mo'], 'A' => ['label' => 'Class A (High Prescribers)', 'color' => 'blue', 'target' => '2 visits/mo'], 'B' => ['label' => 'Class B (Medium Potential)', 'color' => 'teal', 'target' => '1 visit/mo'], 'C' => ['label' => 'Class C (General Coverage)', 'color' => 'slate', 'target' => '1 visit/cycle']] as $code => $meta)
                        @php
                            $cnt = $classCounts[$code] ?? 0;
                            $pct = $assignedDoctorsCount > 0 ? round(($cnt / $assignedDoctorsCount) * 100) : 0;
                        @endphp
                        <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-[#031827] border border-slate-100 dark:border-[#15456E]">
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="font-bold text-slate-800 dark:text-white flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-{{ $meta['color'] }}-500"></span>
                                    Class {{ $code }}
                                </span>
                                <span class="font-extrabold text-slate-900 dark:text-white">{{ $cnt }} {{ app()->getLocale() === 'ar' ? 'طبيب' : 'docs' }}</span>
                            </div>
                            <div class="flex items-center justify-between text-[10px] text-slate-400 mb-1">
                                <span>{{ $meta['target'] }}</span>
                                <span>{{ $pct }}% {{ app()->getLocale() === 'ar' ? 'من المحفظة' : 'of portfolio' }}</span>
                            </div>
                            <div class="w-full bg-slate-200 dark:bg-slate-800 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-{{ $meta['color'] }}-500 h-1.5 rounded-full" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Assigned Doctors Quick Directory -->
            <div class="card p-5 rounded-3xl border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-black text-slate-900 dark:text-white mb-0 flex items-center gap-2">
                        <i class="fa-solid fa-address-book text-cyan-500"></i>
                        {{ app()->getLocale() === 'ar' ? 'أطباء محفظتي المعتمدة' : 'My Assigned Doctors' }}
                    </h3>
                    <span class="text-xs text-slate-400 font-bold">{{ count($assignedDoctorsList) }}</span>
                </div>

                <div class="divide-y divide-slate-100 dark:divide-[#15456E]/40 max-h-80 overflow-y-auto pr-1 scrollbar-thin">
                    @forelse($assignedDoctorsList as $assignedDoc)
                        <div class="py-2.5 first:pt-0 last:pb-0 flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex items-center gap-1.5">
                                    <span class="font-bold text-xs text-slate-900 dark:text-white truncate block">
                                        {{ $assignedDoc->name }}
                                    </span>
                                    @if($assignedDoc->classification)
                                        <span class="px-1.5 py-0.2 rounded text-[9px] font-black bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300">
                                            {{ $assignedDoc->classification->code }}
                                        </span>
                                    @endif
                                </div>
                                <span class="text-[11px] text-slate-400 truncate block">
                                    {{ $assignedDoc->specialty?->name ?? 'General' }} • {{ $assignedDoc->workplace_name ?? ($assignedDoc->city?->name ?? 'Clinic') }}
                                </span>
                            </div>
                            <div class="flex items-center gap-1 flex-shrink-0">
                                @if($assignedDoc->phone)
                                    <a href="tel:{{ $assignedDoc->phone }}" class="w-7 h-7 rounded-lg bg-slate-100 dark:bg-[#031827] text-slate-600 dark:text-slate-300 flex items-center justify-center text-xs hover:bg-[#0A4F78] hover:text-white transition-colors" title="{{ app()->getLocale() === 'ar' ? 'اتصال' : 'Call' }}">
                                        <i class="fa-solid fa-phone"></i>
                                    </a>
                                @endif
                                <a href="{{ route('admin.mr.dashboard') }}" class="w-7 h-7 rounded-lg bg-cyan-50 dark:bg-cyan-950 text-[#0A4F78] dark:text-cyan-300 flex items-center justify-center text-xs hover:bg-[#0A4F78] hover:text-white transition-colors" title="{{ app()->getLocale() === 'ar' ? 'جدولة موعد' : 'Schedule Visit' }}">
                                    <i class="fa-solid fa-calendar-plus"></i>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="py-4 text-center text-xs text-slate-400">
                            {{ app()->getLocale() === 'ar' ? 'لا يوجد أطباء معينين حالياً.' : 'No doctors assigned in this cycle.' }}
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Targeted Product Formulations Focus -->
            <div class="card p-5 rounded-3xl border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-black text-slate-900 dark:text-white mb-0 flex items-center gap-2">
                        <i class="fa-solid fa-prescription-bottle-medical text-emerald-500"></i>
                        {{ app()->getLocale() === 'ar' ? 'المنتجات المستهدفة في الدورة' : 'Cycle Focus Formulations' }}
                    </h3>
                </div>

                <div class="space-y-2.5">
                    @forelse($focusProducts as $fp)
                        <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-[#031827] border border-slate-100 dark:border-[#15456E] flex items-center justify-between gap-3">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-8 h-8 rounded-lg bg-[#0A4F78]/10 dark:bg-cyan-950/60 text-[#0A4F78] dark:text-cyan-300 flex items-center justify-center text-xs font-black flex-shrink-0">
                                    <i class="fa-solid fa-pills"></i>
                                </div>
                                <div class="min-w-0">
                                    <span class="font-bold text-xs text-slate-900 dark:text-white block truncate">
                                        {{ app()->getLocale() === 'ar' ? ($fp->name_ar ?? $fp->name_en) : ($fp->name_en ?? $fp->name_ar) }}
                                    </span>
                                    <span class="text-[10px] text-slate-400 font-mono block">SKU: {{ $fp->sku }}</span>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                Active
                            </span>
                        </div>
                    @empty
                        <div class="text-center text-xs text-slate-400 py-3">
                            {{ app()->getLocale() === 'ar' ? 'لا توجد منتجات مسجلة' : 'No formulations registered' }}
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>
</x-layouts.admin>
