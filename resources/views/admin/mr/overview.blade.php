<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'لوحة المتابعة الميدانية للمندوب' : 'Medical Rep Overview Dashboard'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'متابعة خط السير اليومي، إنجاز زيارات الأطباء، ومؤشرات التغطية الميدانية' : 'Real-time field route execution, doctor coverage progress, and territory performance.'"
    :breadcrumbs="[
        (app()->getLocale() === 'ar' ? 'نظام CRM' : 'CRM') => route('admin.mr.dashboard'),
        (app()->getLocale() === 'ar' ? 'لوحة المندوب' : 'MR Overview') => route('admin.dashboard')
    ]"
>
@php
    $visitsDetailsData = $todayScheduledVisits->map(function($v) {
        $c = $v->contact;
        $q = $c ? ($c->quota_info ?? $c->getVisitQuotaStatus($v->cycle_id)) : null;
        $exec = $v->visit;
        return [
            'id' => $v->id,
            'status' => $v->status,
            'scheduled_at' => $v->scheduled_at ? \Carbon\Carbon::parse($v->scheduled_at)->format('Y-m-d h:i A') : '—',
            'scheduled_date' => $v->scheduled_at ? \Carbon\Carbon::parse($v->scheduled_at)->format('Y-m-d') : now()->toDateString(),
            'scheduled_time' => $v->scheduled_at ? \Carbon\Carbon::parse($v->scheduled_at)->format('H:i') : now()->format('H:i'),
            'notes' => $v->notes ?? '',
            'cycle_name' => $v->cycle?->name ?? 'Active Cycle',
            'has_executed' => (bool)$exec,
            'checkin_at' => $exec && $exec->checkin_at ? \Carbon\Carbon::parse($exec->checkin_at)->format('Y-m-d h:i A') : null,
            'checkout_at' => $exec && $exec->checkout_at ? \Carbon\Carbon::parse($exec->checkout_at)->format('Y-m-d h:i A') : null,
            'duration_minutes' => $exec ? (int)$exec->duration_minutes : null,
            'gps_verified' => $exec ? (bool)$exec->gps_verified : null,
            'gps_flag' => $exec ? $exec->gps_flag : null,
            'distance_m' => $exec ? (int)$exec->distance_from_contact_m : null,
            'outcome' => $exec ? $exec->outcome : null,
            'exec_notes' => $exec ? $exec->notes : null,
            'products' => $exec && $exec->products ? $exec->products->map(fn($p) => $p->name_en ?? $p->name)->toArray() : [],
            'doctor' => $c ? [
                'id' => $c->id,
                'name' => $c->name,
                'phone' => $c->phone ?? '—',
                'email' => $c->email ?? '—',
                'specialty' => $c->specialty?->name ?? 'General Practice',
                'workplace' => $c->workplace_name ?? ($c->hospital_clinic_name ?? ($c->city?->name ?? 'Private Clinic')),
                'city' => $c->city?->name ?? '—',
                'address' => $c->address ?? '—',
                'class_code' => $c->classification?->code ?? 'C',
                'dossier_url' => route('admin.mr.contacts.show', $c->id),
                'quota' => $q,
            ] : null,
        ];
    });

    $doctorsCatalogData = $assignedDoctorsList->map(function($d) {
        return [
            'id' => $d->id,
            'name' => $d->name,
            'class_code' => $d->classification?->code ?? 'C',
            'specialty' => $d->specialty?->name ?? 'General Practice',
            'workplace' => $d->workplace_name ?? ($d->hospital_clinic_name ?? ($d->city?->name ?? 'Clinic')),
            'phone' => $d->phone ?? '',
            'quota' => $d->quota_info ?? $d->getVisitQuotaStatus($activeCycle?->id),
        ];
    });
@endphp

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
    <div class="bz-hero-banner">
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
                        <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-black bg-cyan-400/25 text-cyan-200 border border-cyan-300/40">
                            <i class="fa-solid fa-user-doctor text-[10px]"></i>
                            {{ app()->getLocale() === 'ar' ? 'مندوب دعاية طبية' : 'Medical Representative' }}
                        </span>
                        @if($activeCycle)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-white/15 text-white border border-white/20">
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
                        <span class="inline-flex items-center gap-1 text-white/90">
                            <i class="fa-regular fa-calendar text-cyan-300"></i>
                            {{ now()->translatedFormat('l, d F Y') }}
                        </span>
                    </p>

                    <!-- Shift Attendance Chip -->
                    <div class="mt-3 bz-hero-chip">
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
                                    <button type="submit" class="btn btn-xs bg-red-500/90 hover:bg-red-600 text-white rounded-lg text-[10px] px-2 py-0.5 ml-1 mr-1 border-0 shadow-xs font-bold">
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
                                <button type="submit" class="btn btn-xs bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg text-[10px] px-2.5 py-0.5 border-0 font-bold shadow-xs">
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
                <a href="{{ route('admin.mr.visits.index') }}" class="bz-hero-btn-primary">
                    <i class="fa-solid fa-list-check"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'سجل الزيارات والتقارير' : 'Visits History & Entry' }}</span>
                </a>

                <a href="{{ route('admin.mr.dashboard') }}" class="bz-hero-btn-secondary">
                    <i class="fa-solid fa-calendar-days text-cyan-300"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'جدول المواعيد' : 'Field Schedules' }}</span>
                </a>

                @if(auth()->user() && auth()->user()->canManageAllMr())
                <a href="{{ route('admin.mr.live-map') }}" class="bz-hero-btn-accent">
                    <i class="fa-solid fa-earth-americas text-amber-300"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'خريطة السير' : 'Route Map' }}</span>
                </a>
                @endif
            </div>
        </div>

        <!-- Today's Route Progress Bar -->
        <div class="mt-5 pt-4 border-t border-white/15 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-2">
                <span class="font-bold text-white">{{ app()->getLocale() === 'ar' ? 'مسار زيارات اليوم:' : "Today's Route Progress:" }}</span>
                <span class="text-emerald-300 font-black">{{ $todayCompletedCount }} / {{ $todayPlannedCount }} {{ app()->getLocale() === 'ar' ? 'زيارات منجزة' : 'visits completed' }}</span>
                <span class="text-white/80 font-bold">({{ $todayProgressRate }}%)</span>
            </div>
            <div class="w-full sm:w-64 bz-progress-track">
                <div class="bz-progress-bar" style="width: {{ $todayProgressRate }}%"></div>
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
                        <button type="button" onclick="openScheduleModal()" class="btn btn-primary text-xs font-bold px-3.5 py-1.5 rounded-xl bg-[#0A4F78] hover:bg-[#062B49] text-white flex items-center gap-1.5 shadow-sm transition-all hover:shadow">
                            <i class="fa-solid fa-calendar-plus text-xs"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'جدولة زيارة طبيب' : 'Schedule Visit' }}</span>
                        </button>
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
                                    $quota = $doc?->quota_info;
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
                                                    <button type="button" onclick="openVisitDetailsModal({{ $visit->id }})" class="text-sm font-black text-slate-900 dark:text-white hover:text-[#0A4F78] dark:hover:text-cyan-400 text-left rtl:text-right transition-colors flex items-center gap-1.5 cursor-pointer">
                                                        <span>{{ $doc?->name ?? 'Doctor Name' }}</span>
                                                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-slate-400"></i>
                                                    </button>

                                                    <!-- Classification Badge -->
                                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-black {{ $classCode === 'A+' ? 'bg-purple-100 text-purple-800 dark:bg-purple-950 dark:text-purple-300' : ($classCode === 'A' ? 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300' : ($classCode === 'B' ? 'bg-teal-100 text-teal-800 dark:bg-teal-950 dark:text-teal-300' : 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300')) }}">
                                                        Class {{ $classCode }}
                                                    </span>

                                                    <!-- Quota Progress Indicator -->
                                                    @if($quota)
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold {{ $quota['can_schedule'] ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300' : 'bg-rose-100 text-rose-800 dark:bg-rose-950/80 dark:text-rose-300' }}" title="{{ $quota['can_schedule'] ? ($quota['remaining_visits'] . ' visits remaining this cycle') : 'Visit quota reached for Class ' . $quota['class_code'] }}">
                                                            <i class="fa-solid fa-bullseye text-[9px]"></i>
                                                            <span>{{ $quota['current_count'] }}/{{ $quota['max_visits'] }}</span>
                                                            <span class="hidden md:inline">{{ app()->getLocale() === 'ar' ? 'زيارات' : 'visits' }}</span>
                                                        </span>
                                                    @endif

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

                                            <div class="flex items-center gap-1.5 flex-wrap justify-end">
                                                <!-- Full Details Trigger -->
                                                <button type="button" onclick="openVisitDetailsModal({{ $visit->id }})" class="btn btn-secondary text-xs px-2.5 py-1 rounded-lg flex items-center gap-1 text-slate-700 dark:text-slate-200 hover:text-[#0A4F78] dark:hover:text-cyan-400 border border-slate-200 dark:border-[#15456E]" title="{{ app()->getLocale() === 'ar' ? 'عرض التفاصيل الكاملة' : 'View Full Details' }}">
                                                    <i class="fa-solid fa-circle-info text-cyan-600 dark:text-cyan-400 text-xs"></i>
                                                    <span class="hidden sm:inline">{{ app()->getLocale() === 'ar' ? 'التفاصيل' : 'Details' }}</span>
                                                </button>

                                                <!-- Schedule Next Visit for This Doctor -->
                                                @if($doc)
                                                    <button type="button" onclick="openScheduleModalForDoctor({{ $doc->id }})" class="btn btn-secondary text-xs px-2.5 py-1 rounded-lg flex items-center gap-1 text-[#0A4F78] dark:text-cyan-400 border border-[#0A4F78]/30 dark:border-cyan-800 hover:bg-[#0A4F78]/10" title="{{ app()->getLocale() === 'ar' ? 'جدولة زيارة لهذا الطبيب' : 'Schedule Visit For This Doctor' }}">
                                                        <i class="fa-solid fa-calendar-plus text-xs"></i>
                                                        <span class="hidden sm:inline">{{ app()->getLocale() === 'ar' ? 'جدولة' : 'Schedule' }}</span>
                                                    </button>
                                                @endif

                                                <!-- Start Visit Execution CTA -->
                                                @if(!$isDone)
                                                    <a href="{{ route('admin.mr.dashboard') }}" class="btn btn-primary text-xs font-bold px-3 py-1 rounded-lg bg-[#0A4F78] hover:bg-[#062B49] text-white flex items-center gap-1">
                                                        <i class="fa-solid fa-play text-[9px]"></i>
                                                        {{ app()->getLocale() === 'ar' ? 'بدء الزيارة' : 'Start Visit' }}
                                                    </a>
                                                @endif

                                                <!-- Doctor Dossier Profile Link -->
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
                                <button type="button" onclick="openScheduleModal()" class="btn btn-primary text-xs font-bold px-4 py-2 rounded-xl bg-[#0A4F78] hover:bg-[#062B49] text-white">
                                    <i class="fa-solid fa-calendar-plus mr-1.5 ml-1.5"></i>
                                    {{ app()->getLocale() === 'ar' ? 'جدولة زيارة طبيب' : 'Schedule Doctor Visit' }}
                                </button>
                                <a href="{{ route('admin.mr.dashboard') }}" class="btn btn-secondary text-xs font-bold px-4 py-2 rounded-xl">
                                    <i class="fa-solid fa-calendar mr-1.5 ml-1.5 text-cyan-500"></i>
                                    {{ app()->getLocale() === 'ar' ? 'عرض التقويم' : 'Open Calendar' }}
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
                        @php
                            $aq = $assignedDoc->quota_info ?? null;
                        @endphp
                        <div class="py-2.5 first:pt-0 last:pb-0 flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="font-bold text-xs text-slate-900 dark:text-white truncate block">
                                        {{ $assignedDoc->name }}
                                    </span>
                                    @if($assignedDoc->classification)
                                        <span class="px-1.5 py-0.2 rounded text-[9px] font-black bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300">
                                            {{ $assignedDoc->classification->code }}
                                        </span>
                                    @endif
                                    @if($aq)
                                        <span class="px-1.5 py-0.2 rounded text-[9px] font-black {{ $aq['can_schedule'] ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300' }}" title="{{ $aq['can_schedule'] ? ($aq['remaining_visits'] . ' visits remaining this cycle') : 'Cycle quota reached for Class ' . $aq['class_code'] }}">
                                            {{ $aq['current_count'] }}/{{ $aq['max_visits'] }}
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
                                <button type="button" onclick="openScheduleModalForDoctor({{ $assignedDoc->id }})" class="w-7 h-7 rounded-lg bg-cyan-50 dark:bg-cyan-950 text-[#0A4F78] dark:text-cyan-300 flex items-center justify-center text-xs hover:bg-[#0A4F78] hover:text-white transition-colors cursor-pointer" title="{{ app()->getLocale() === 'ar' ? 'جدولة موعد لهذا الطبيب' : 'Schedule Visit For This Doctor' }}">
                                    <i class="fa-solid fa-calendar-plus"></i>
                                </button>
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

    <!-- ========================================================================= -->
    <!-- MODAL 1: FULL DOCTOR & VISIT 360° DETAILS MODAL                           -->
    <!-- ========================================================================= -->
    <div id="visit-details-modal" class="fixed inset-0 z-[1050] overflow-y-auto bg-slate-950/70 p-3 sm:p-4 hidden backdrop-blur-sm" onclick="if(event.target === this) closeVisitDetailsModal()">
        <div class="min-h-full flex items-center justify-center p-0">
            <div class="card max-w-2xl w-full p-0 shadow-2xl relative flex flex-col max-h-[90vh] border border-slate-200 dark:border-[#15456E] rounded-3xl overflow-hidden bg-white dark:bg-[#062B49]">
                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-[#15456E] px-6 py-4 flex-shrink-0 bg-slate-50/70 dark:bg-[#031827]">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-[#0A4F78]/10 dark:bg-cyan-950/80 text-[#0A4F78] dark:text-cyan-400 flex items-center justify-center text-lg flex-shrink-0">
                            <i class="fa-solid fa-user-doctor"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 id="vdm-doc-name" class="font-black text-base text-slate-900 dark:text-white mb-0">Doctor Details</h3>
                                <span id="vdm-doc-class" class="px-2 py-0.5 rounded-md text-[10px] font-black bg-purple-100 text-purple-800 dark:bg-purple-950 dark:text-purple-300">Class A+</span>
                            </div>
                            <p id="vdm-doc-subtitle" class="text-xs text-slate-500 dark:text-slate-400 mb-0">Specialty • Facility</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeVisitDetailsModal()" class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-200/50 dark:hover:bg-[#15456E] transition-colors">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="flex-1 overflow-y-auto p-6 space-y-6">
                    
                    <!-- Doctor Classification & Quota Card -->
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-[#031827] border border-slate-200 dark:border-[#15456E]">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                                <i class="fa-solid fa-bullseye text-cyan-600 dark:text-cyan-400"></i>
                                {{ app()->getLocale() === 'ar' ? 'حالة حصة الزيارات في الدورة الحالية (Classification Quota)' : 'Current Cycle Visit Quota & Limit' }}
                            </span>
                            <span id="vdm-quota-count" class="text-xs font-black text-[#0A4F78] dark:text-cyan-400">0 / 0 Visits</span>
                        </div>
                        <div class="w-full bg-slate-200 dark:bg-slate-800 rounded-full h-2 overflow-hidden mb-2">
                            <div id="vdm-quota-bar" class="h-2 rounded-full transition-all duration-300 bg-[#0A4F78]" style="width: 0%"></div>
                        </div>
                        <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400">
                            <span id="vdm-quota-rule">Class limit rule</span>
                            <span id="vdm-quota-remaining" class="font-bold">0 visits remaining</span>
                        </div>
                        <div id="vdm-quota-alert" class="mt-2.5 p-2 rounded-xl text-xs font-semibold hidden"></div>
                    </div>

                    <!-- Visit Status & Schedule Info -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-[#031827]/70 border border-slate-200/80 dark:border-[#15456E]">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">
                                {{ app()->getLocale() === 'ar' ? 'حالة الزيارة' : 'Visit Status' }}
                            </span>
                            <div id="vdm-status-badge">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-200 text-slate-700">Planned</span>
                            </div>
                        </div>
                        <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-[#031827]/70 border border-slate-200/80 dark:border-[#15456E]">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">
                                {{ app()->getLocale() === 'ar' ? 'الموعد المجدول' : 'Scheduled Time' }}
                            </span>
                            <span id="vdm-scheduled-at" class="text-xs font-black text-slate-900 dark:text-white">—</span>
                        </div>
                    </div>

                    <!-- Detailing Objectives & Notes -->
                    <div class="p-4 rounded-2xl bg-white dark:bg-[#062B49] border border-slate-200 dark:border-[#15456E]">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 flex items-center gap-1.5">
                            <i class="fa-solid fa-note-sticky text-amber-500"></i>
                            {{ app()->getLocale() === 'ar' ? 'أهداف الزيارة والملاحظات الميدانية' : 'Detailing Objectives & Field Notes' }}
                        </h4>
                        <p id="vdm-notes" class="text-xs text-slate-700 dark:text-slate-300 italic mb-0 leading-relaxed">
                            No notes specified for this visit.
                        </p>
                    </div>

                    <!-- Doctor Contact & Workplace Directory Details -->
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-[#031827] border border-slate-200 dark:border-[#15456E] space-y-2.5">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2 flex items-center gap-1.5">
                            <i class="fa-solid fa-address-card text-cyan-600 dark:text-cyan-400"></i>
                            {{ app()->getLocale() === 'ar' ? 'بيانات التواصل والعيادة / المركز الطبي' : 'Clinic & Contact Information' }}
                        </h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                            <div>
                                <span class="text-slate-400 block text-[10px]">{{ app()->getLocale() === 'ar' ? 'الهاتف:' : 'Phone:' }}</span>
                                <span id="vdm-phone" class="font-bold text-slate-800 dark:text-white">—</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[10px]">{{ app()->getLocale() === 'ar' ? 'البريد الإلكتروني:' : 'Email:' }}</span>
                                <span id="vdm-email" class="font-bold text-slate-800 dark:text-white">—</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[10px]">{{ app()->getLocale() === 'ar' ? 'المنشأة الطبية:' : 'Workplace:' }}</span>
                                <span id="vdm-workplace" class="font-bold text-slate-800 dark:text-white">—</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[10px]">{{ app()->getLocale() === 'ar' ? 'المدينة / المنطقة:' : 'City / Area:' }}</span>
                                <span id="vdm-city" class="font-bold text-slate-800 dark:text-white">—</span>
                            </div>
                        </div>
                    </div>

                    <!-- Execution Telemetry (if executed) -->
                    <div id="vdm-exec-section" class="p-4 rounded-2xl bg-emerald-50/50 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-900/50 space-y-3 hidden">
                        <h4 class="text-xs font-black uppercase text-emerald-800 dark:text-emerald-300 flex items-center gap-1.5 mb-2">
                            <i class="fa-solid fa-circle-check text-emerald-600"></i>
                            {{ app()->getLocale() === 'ar' ? 'بيانات تنفيذ الزيارة والتحقق من الموقع (GPS)' : 'Execution & GPS Verification Telemetry' }}
                        </h4>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs">
                            <div>
                                <span class="text-slate-400 block text-[10px]">{{ app()->getLocale() === 'ar' ? 'وقت تسجيل الدخول:' : 'Check-In:' }}</span>
                                <span id="vdm-checkin" class="font-bold text-slate-800 dark:text-white">—</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[10px]">{{ app()->getLocale() === 'ar' ? 'وقت الخروج:' : 'Check-Out:' }}</span>
                                <span id="vdm-checkout" class="font-bold text-slate-800 dark:text-white">—</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[10px]">{{ app()->getLocale() === 'ar' ? 'المدة الزمنية:' : 'Duration:' }}</span>
                                <span id="vdm-duration" class="font-bold text-slate-800 dark:text-white">—</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 pt-2 border-t border-emerald-200/60 dark:border-emerald-900/40">
                            <span class="text-xs text-slate-500">{{ app()->getLocale() === 'ar' ? 'التحقق الميداني:' : 'GPS Status:' }}</span>
                            <span id="vdm-gps-badge" class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Verified</span>
                            <span id="vdm-distance" class="text-xs text-slate-500"></span>
                        </div>
                        <div id="vdm-products-wrapper" class="pt-2 hidden">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">{{ app()->getLocale() === 'ar' ? 'المنتجات التي تم تقديمها:' : 'Detailed Products:' }}</span>
                            <div id="vdm-products-list" class="flex items-center gap-1.5 flex-wrap"></div>
                        </div>
                    </div>

                </div>

                <!-- Modal Footer -->
                <div class="border-t border-slate-100 dark:border-[#15456E] px-6 py-4 flex items-center justify-between flex-wrap gap-3 bg-slate-50/70 dark:bg-[#031827]">
                    <a id="vdm-dossier-btn" href="#" class="btn btn-secondary text-xs font-bold px-3 py-2 rounded-xl flex items-center gap-1.5">
                        <i class="fa-solid fa-id-card text-cyan-600 dark:text-cyan-400"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'الملف الشامل للطبيب' : 'Doctor Dossier' }}</span>
                    </a>

                    <div class="flex items-center gap-2">
                        <button type="button" onclick="closeVisitDetailsModal()" class="btn btn-secondary text-xs font-bold px-4 py-2 rounded-xl">
                            {{ app()->getLocale() === 'ar' ? 'إغلاق' : 'Close' }}
                        </button>
                        <button id="vdm-schedule-btn" type="button" onclick="scheduleFromDetailsModal()" class="btn btn-primary text-xs font-bold px-4 py-2 rounded-xl bg-[#0A4F78] hover:bg-[#062B49] text-white flex items-center gap-1.5">
                            <i class="fa-solid fa-calendar-plus"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'جدولة زيارة لهذا الطبيب' : 'Schedule Next Visit' }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 2: SCHEDULE DOCTOR VISIT MODAL (WITH STRICT CLASS QUOTA LOGIC)      -->
    <!-- ========================================================================= -->
    <div id="schedule-doctor-modal" class="fixed inset-0 z-[1050] overflow-y-auto bg-slate-950/70 p-3 sm:p-4 hidden backdrop-blur-sm" onclick="if(event.target === this) closeScheduleModal()">
        <div class="min-h-full flex items-center justify-center p-0">
            <div class="card max-w-xl w-full p-0 shadow-2xl relative flex flex-col max-h-[90vh] border border-slate-200 dark:border-[#15456E] rounded-3xl overflow-hidden bg-white dark:bg-[#062B49]">
                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-[#15456E] px-6 py-4 flex-shrink-0 bg-slate-50/70 dark:bg-[#031827]">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-[#0A4F78]/10 dark:bg-cyan-950/80 text-[#0A4F78] dark:text-cyan-400 flex items-center justify-center text-lg flex-shrink-0">
                            <i class="fa-solid fa-calendar-plus"></i>
                        </div>
                        <div>
                            <h3 class="font-black text-base text-slate-900 dark:text-white mb-0">
                                {{ app()->getLocale() === 'ar' ? 'جدولة موعد زيارة طبيب' : 'Schedule Doctor Visit' }}
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mb-0">
                                {{ app()->getLocale() === 'ar' ? 'الالتزام بحصة الزيارات المحددة لكل تصنيف (A+, A, B, C)' : 'Visits strictly restricted by doctor classification quotas' }}
                            </p>
                        </div>
                    </div>
                    <button type="button" onclick="closeScheduleModal()" class="w-8 h-8 rounded-xl flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-200/50 dark:hover:bg-[#15456E] transition-colors">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                <!-- Schedule Form -->
                <form id="schedule-doctor-form" method="POST" action="{{ route('admin.mr.visits.schedule') }}" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    @csrf
                    <input type="hidden" name="mr_id" value="{{ $repId }}">

                    <div class="flex-1 overflow-y-auto p-6 space-y-4">
                        
                        <!-- Doctor Selector -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">
                                {{ app()->getLocale() === 'ar' ? 'اختر الطبيب المستهدف *' : 'Target Doctor *' }}
                            </label>
                            <select id="sch-doctor-select" name="contact_id" required onchange="onDoctorSelected(this.value)" class="form-select text-xs w-full py-2.5 px-3 rounded-xl border-slate-200 dark:border-[#15456E] dark:bg-[#031827] dark:text-white">
                                <option value="">{{ app()->getLocale() === 'ar' ? '-- اختر الطبيب --' : '-- Select Doctor --' }}</option>
                                @foreach($assignedDoctorsList as $docItem)
                                    <option value="{{ $docItem->id }}" data-class="{{ $docItem->classification?->code ?? 'C' }}">
                                        {{ $docItem->name }} (Class {{ $docItem->classification?->code ?? 'C' }} - {{ $docItem->specialty?->name ?? 'General' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- LIVE QUOTA EVALUATION CARD -->
                        <div id="sch-quota-box" class="p-4 rounded-2xl border transition-all duration-200 bg-slate-50 dark:bg-[#031827] border-slate-200 dark:border-[#15456E]">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <span id="sch-quota-class-badge" class="px-2 py-0.5 rounded text-[10px] font-black bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300">Class -</span>
                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-200">{{ app()->getLocale() === 'ar' ? 'مؤشر الحصة الميدانية للدورة' : 'Cycle Quota Status' }}</span>
                                </div>
                                <span id="sch-quota-stat" class="text-xs font-black text-slate-800 dark:text-white">0 / 0</span>
                            </div>
                            <div class="w-full bg-slate-200 dark:bg-slate-800 rounded-full h-2 overflow-hidden mb-2">
                                <div id="sch-quota-bar" class="h-2 rounded-full bg-[#0A4F78] transition-all duration-300" style="width: 0%"></div>
                            </div>
                            <p id="sch-quota-feedback" class="text-[11px] text-slate-500 dark:text-slate-400 mb-0">
                                {{ app()->getLocale() === 'ar' ? 'حدد طبيباً للتحقق من الحصة المتاحة بناءً على تصنيفه.' : 'Select a doctor to verify remaining visit quota.' }}
                            </p>
                            <!-- Alert Message if Quota is Reached -->
                            <div id="sch-quota-warning" class="mt-2.5 p-3 rounded-xl text-xs font-bold hidden"></div>
                        </div>

                        <!-- Visit Cycle Selection -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">
                                {{ app()->getLocale() === 'ar' ? 'دورة الزيارات' : 'Visit Cycle' }}
                            </label>
                            <select id="sch-cycle-select" name="cycle_id" class="form-select text-xs w-full py-2.5 px-3 rounded-xl border-slate-200 dark:border-[#15456E] dark:bg-[#031827] dark:text-white">
                                @foreach($allCycles as $c)
                                    <option value="{{ $c->id }}" {{ $activeCycle && $activeCycle->id == $c->id ? 'selected' : '' }}>
                                        {{ $c->name }} ({{ $c->start_date }} &rarr; {{ $c->end_date }}) {{ $c->status === 'active' ? '★ Active' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Date & Time Row -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">
                                    {{ app()->getLocale() === 'ar' ? 'تاريخ الزيارة *' : 'Visit Date *' }}
                                </label>
                                <input type="date" name="scheduled_date" id="sch-date-input" required value="{{ now()->toDateString() }}" class="form-input text-xs w-full py-2.5 px-3 rounded-xl border-slate-200 dark:border-[#15456E] dark:bg-[#031827] dark:text-white">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">
                                    {{ app()->getLocale() === 'ar' ? 'وقت الزيارة *' : 'Visit Time *' }}
                                </label>
                                <input type="time" name="scheduled_time" id="sch-time-input" required value="{{ now()->addHour()->format('H:00') }}" class="form-input text-xs w-full py-2.5 px-3 rounded-xl border-slate-200 dark:border-[#15456E] dark:bg-[#031827] dark:text-white">
                            </div>
                        </div>

                        <!-- Detailing Objectives & Notes -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1.5">
                                {{ app()->getLocale() === 'ar' ? 'الهدف التسويقي وملاحظات الزيارة' : 'Detailing Objectives & Notes' }}
                            </label>
                            <textarea name="notes" id="sch-notes-input" rows="3" class="form-textarea text-xs w-full p-3 rounded-xl border-slate-200 dark:border-[#15456E] dark:bg-[#031827] dark:text-white" placeholder="{{ app()->getLocale() === 'ar' ? 'أدخل أهداف المقابلة الطبية أو المنتجات المستهدفة...' : 'Key talking points, product indications, or follow-up notes...' }}"></textarea>
                        </div>

                    </div>

                    <!-- Modal Actions -->
                    <div class="border-t border-slate-100 dark:border-[#15456E] px-6 py-4 flex items-center justify-end gap-3 bg-slate-50/70 dark:bg-[#031827]">
                        <button type="button" onclick="closeScheduleModal()" class="btn btn-secondary text-xs font-bold px-4 py-2 rounded-xl">
                            {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                        </button>
                        <button id="sch-submit-btn" type="submit" class="btn btn-primary text-xs font-bold px-5 py-2.5 rounded-xl bg-[#0A4F78] hover:bg-[#062B49] text-white flex items-center gap-1.5 shadow-sm transition-all">
                            <i class="fa-solid fa-check"></i>
                            <span id="sch-submit-label">{{ app()->getLocale() === 'ar' ? 'تأكيد وحفظ الجدولة' : 'Confirm & Schedule Visit' }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- CLIENT JAVASCRIPT: DATA STORES, MODALS & LIVE CLASSIFICATION QUOTA LOGIC  -->
    <!-- ========================================================================= -->
    <script>
        const VISITS_DATA = @json($visitsDetailsData);
        const DOCTORS_DATA = @json($doctorsCatalogData);
        let selectedDoctorIdForSchedule = null;

        function openVisitDetailsModal(visitId) {
            const v = VISITS_DATA.find(item => item.id === visitId);
            if (!v) return;

            const doc = v.doctor || {};
            const q = doc.quota || {};

            // Header
            document.getElementById('vdm-doc-name').textContent = doc.name || 'Doctor';
            document.getElementById('vdm-doc-class').textContent = 'Class ' + (doc.class_code || 'C');
            document.getElementById('vdm-doc-subtitle').textContent = (doc.specialty || 'General') + ' • ' + (doc.workplace || 'Clinic');

            // Quota
            const maxV = q.max_visits || 1;
            const curV = q.current_count || 0;
            const remV = q.remaining_visits !== undefined ? q.remaining_visits : 0;
            const pct = Math.min(100, Math.round((curV / maxV) * 100));

            document.getElementById('vdm-quota-count').textContent = curV + ' / ' + maxV + ' {{ app()->getLocale() === "ar" ? "زيارات الدورة" : "Visits in Cycle" }}';
            document.getElementById('vdm-quota-bar').style.width = pct + '%';
            document.getElementById('vdm-quota-rule').textContent = 'Class ' + (doc.class_code || 'C') + ' Quota: ' + maxV + ' {{ app()->getLocale() === "ar" ? "زيارات لكل دورة" : "visits per cycle" }}';
            document.getElementById('vdm-quota-remaining').textContent = remV + ' {{ app()->getLocale() === "ar" ? "متبقية" : "remaining" }}';

            const alertEl = document.getElementById('vdm-quota-alert');
            if (!q.can_schedule) {
                alertEl.className = 'mt-2.5 p-2 rounded-xl text-xs font-semibold bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300 border border-rose-300 dark:border-rose-900 block';
                alertEl.innerHTML = '<i class="fa-solid fa-triangle-exclamation mr-1"></i> {{ app()->getLocale() === "ar" ? "تم الوصول للحد الأقصى للزيارات لهذا الطبيب في الدورة الحالية بناءً على تصنيفه." : "Visit quota reached for this doctor in current cycle based on classification limit." }}';
            } else {
                alertEl.className = 'mt-2.5 p-2 rounded-xl text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-900 block';
                alertEl.innerHTML = '<i class="fa-solid fa-circle-check mr-1"></i> {{ app()->getLocale() === "ar" ? "الحصة الميدانية متاحة للزيارة القادمة." : "Quota available for scheduling." }}';
            }

            // Status Badge
            const statusContainer = document.getElementById('vdm-status-badge');
            if (v.status === 'completed') {
                statusContainer.innerHTML = '<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-300"><i class="fa-solid fa-check"></i> {{ app()->getLocale() === "ar" ? "تمت الزيارة" : "Completed" }}</span>';
            } else if (v.status === 'in_progress') {
                statusContainer.innerHTML = '<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-cyan-100 text-cyan-800 dark:bg-cyan-950 dark:text-cyan-300 border border-cyan-300"><i class="fa-solid fa-spinner fa-spin"></i> {{ app()->getLocale() === "ar" ? "جارية الآن" : "In Progress" }}</span>';
            } else {
                statusContainer.innerHTML = '<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300"><i class="fa-regular fa-clock"></i> {{ app()->getLocale() === "ar" ? "مجدولة" : "Planned" }}</span>';
            }

            document.getElementById('vdm-scheduled-at').textContent = v.scheduled_at || '—';
            document.getElementById('vdm-notes').textContent = v.notes ? ('"' + v.notes + '"') : '{{ app()->getLocale() === "ar" ? "لا توجد ملاحظات مسجلة." : "No detailing notes specified." }}';

            // Contact Info
            document.getElementById('vdm-phone').textContent = doc.phone || '—';
            document.getElementById('vdm-email').textContent = doc.email || '—';
            document.getElementById('vdm-workplace').textContent = doc.workplace || '—';
            document.getElementById('vdm-city').textContent = (doc.city || '—') + (doc.address ? (' (' + doc.address + ')') : '');

            // Execution Telemetry
            const execSec = document.getElementById('vdm-exec-section');
            if (v.has_executed) {
                execSec.classList.remove('hidden');
                document.getElementById('vdm-checkin').textContent = v.checkin_at || '—';
                document.getElementById('vdm-checkout').textContent = v.checkout_at || 'In Progress';
                document.getElementById('vdm-duration').textContent = v.duration_minutes ? (v.duration_minutes + ' min') : '—';
                
                const gpsBadge = document.getElementById('vdm-gps-badge');
                if (v.gps_verified) {
                    gpsBadge.className = 'px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300';
                    gpsBadge.textContent = 'GPS Verified';
                } else {
                    gpsBadge.className = 'px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300';
                    gpsBadge.textContent = 'GPS Flagged';
                }
                document.getElementById('vdm-distance').textContent = v.distance_m ? ('(' + v.distance_m + 'm from clinic)') : '';

                const prodWrapper = document.getElementById('vdm-products-wrapper');
                const prodList = document.getElementById('vdm-products-list');
                if (v.products && v.products.length > 0) {
                    prodWrapper.classList.remove('hidden');
                    prodList.innerHTML = v.products.map(p => '<span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-cyan-100 text-cyan-800 dark:bg-cyan-950 dark:text-cyan-300 border border-cyan-200 dark:border-cyan-800">' + p + '</span>').join('');
                } else {
                    prodWrapper.classList.add('hidden');
                }
            } else {
                execSec.classList.add('hidden');
            }

            // Dossier button
            document.getElementById('vdm-dossier-btn').href = doc.dossier_url || '#';
            selectedDoctorIdForSchedule = doc.id || null;

            document.getElementById('visit-details-modal').classList.remove('hidden');
        }

        function closeVisitDetailsModal() {
            document.getElementById('visit-details-modal').classList.add('hidden');
        }

        function scheduleFromDetailsModal() {
            closeVisitDetailsModal();
            if (selectedDoctorIdForSchedule) {
                openScheduleModalForDoctor(selectedDoctorIdForSchedule);
            } else {
                openScheduleModal();
            }
        }

        function openScheduleModal() {
            const selectEl = document.getElementById('sch-doctor-select');
            if (selectEl && selectEl.options.length > 1 && !selectEl.value) {
                selectEl.selectedIndex = 1;
                onDoctorSelected(selectEl.value);
            }
            document.getElementById('schedule-doctor-modal').classList.remove('hidden');
        }

        function openScheduleModalForDoctor(doctorId) {
            const selectEl = document.getElementById('sch-doctor-select');
            if (selectEl) {
                selectEl.value = doctorId;
                onDoctorSelected(doctorId);
            }
            document.getElementById('schedule-doctor-modal').classList.remove('hidden');
        }

        function closeScheduleModal() {
            document.getElementById('schedule-doctor-modal').classList.add('hidden');
        }

        function onDoctorSelected(doctorId) {
            if (!doctorId) {
                updateQuotaDisplay(null);
                return;
            }

            const doc = DOCTORS_DATA.find(d => String(d.id) === String(doctorId));
            if (doc && doc.quota) {
                updateQuotaDisplay(doc.quota);
            } else {
                // Live server fallback
                fetch(`/admin/mr/contacts/${doctorId}/quota`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.success && data.quota) {
                            updateQuotaDisplay(data.quota);
                        }
                    })
                    .catch(() => {});
            }
        }

        function updateQuotaDisplay(quota) {
            const box = document.getElementById('sch-quota-box');
            const classBadge = document.getElementById('sch-quota-class-badge');
            const statEl = document.getElementById('sch-quota-stat');
            const barEl = document.getElementById('sch-quota-bar');
            const feedbackEl = document.getElementById('sch-quota-feedback');
            const warningEl = document.getElementById('sch-quota-warning');
            const submitBtn = document.getElementById('sch-submit-btn');
            const submitLabel = document.getElementById('sch-submit-label');

            if (!quota) {
                classBadge.textContent = 'Class -';
                statEl.textContent = '0 / 0';
                barEl.style.width = '0%';
                feedbackEl.textContent = '{{ app()->getLocale() === "ar" ? "حدد طبيباً للتحقق من الحصة المتاحة." : "Select a doctor to verify quota." }}';
                warningEl.classList.add('hidden');
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                return;
            }

            const cur = quota.current_count || 0;
            const max = quota.max_visits || 1;
            const rem = quota.remaining_visits !== undefined ? quota.remaining_visits : 0;
            const code = quota.class_code || 'C';
            const canSchedule = Boolean(quota.can_schedule);
            const pct = Math.min(100, Math.round((cur / max) * 100));

            classBadge.textContent = 'Class ' + code;
            statEl.textContent = cur + ' / ' + max + ' {{ app()->getLocale() === "ar" ? "زيارات" : "visits" }}';
            barEl.style.width = pct + '%';

            if (!canSchedule) {
                box.className = 'p-4 rounded-2xl border transition-all duration-200 bg-rose-50 dark:bg-rose-950/20 border-rose-300 dark:border-rose-900';
                barEl.className = 'h-2 rounded-full bg-rose-500 transition-all duration-300';
                feedbackEl.textContent = '{{ app()->getLocale() === "ar" ? "تم استنفاد الحد الأقصى للزيارات لهذا الطبيب." : "Doctor visit quota reached for this cycle." }}';
                
                warningEl.className = 'mt-2.5 p-3 rounded-xl text-xs font-black bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300 border border-rose-300 dark:border-rose-900 block';
                warningEl.innerHTML = '<i class="fa-solid fa-ban mr-1.5"></i> {{ app()->getLocale() === "ar" ? "تنبيه: لا يمكن جدولة الزيارة! تم الوصول للحد الأقصى لتصنيف Class " : "Notice: Cannot schedule visit! Maximum limit reached for Class " }}' + code + ' (' + cur + '/' + max + ' {{ app()->getLocale() === "ar" ? "زيارات)" : "visits)" }}';

                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                submitLabel.textContent = '{{ app()->getLocale() === "ar" ? "تم استنفاد الحصة المسموحة" : "Quota Reached (Blocked)" }}';
            } else {
                box.className = 'p-4 rounded-2xl border transition-all duration-200 bg-emerald-50/60 dark:bg-emerald-950/20 border-emerald-300 dark:border-emerald-900';
                barEl.className = 'h-2 rounded-full bg-emerald-500 transition-all duration-300';
                feedbackEl.textContent = rem + ' {{ app()->getLocale() === "ar" ? "زيارات متبقية مسموح بها في هذه الدورة." : "visits remaining allowed in this cycle." }}';

                warningEl.className = 'mt-2.5 p-3 rounded-xl text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-900 block';
                warningEl.innerHTML = '<i class="fa-solid fa-circle-check mr-1.5"></i> {{ app()->getLocale() === "ar" ? "الحصة متاحة: يمكنك جدولة الزيارة الآن بنجاح." : "Quota available: You can schedule this visit." }}';

                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                submitLabel.textContent = '{{ app()->getLocale() === "ar" ? "تأكيد وحفظ الجدولة" : "Confirm & Schedule Visit" }}';
            }
        }

        // Close on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeVisitDetailsModal();
                closeScheduleModal();
            }
        });
    </script>
</x-layouts.admin>
