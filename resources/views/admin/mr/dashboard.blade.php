<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'لوحة تحكم CRM والتقويم الشامل' : 'CRM Dashboard & Schedules'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'لوحة قيادة مركزية لمتابعة وجدولة مواعيد مندوبي الدعاية، والتقويم التفاعلي مع إجراءات الزر الأيمن، وملفات المناديب 360°' : 'Central command dashboard to manage medical representative schedules, interactive calendar with right-click quick actions, and 360° rep intelligence.'"
    :breadcrumbs="[
        (app()->getLocale() === 'ar' ? 'نظام CRM' : 'CRM') => route('admin.mr.dashboard'),
        (app()->getLocale() === 'ar' ? 'لوحة تحكم CRM' : 'CRM Dashboard') => route('admin.mr.dashboard')
    ]"
>
    @push('styles')
    <style>
        /* FullCalendar Custom Theme Overrides for Blue Zone */
        .fc {
            font-family: inherit !important;
        }
        .fc .fc-toolbar {
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-bottom: 1.25rem !important;
        }
        .fc .fc-toolbar-title {
            font-size: 1.15rem !important;
            font-weight: 800 !important;
            color: inherit !important;
            letter-spacing: -0.01em;
        }
        .fc .fc-button-primary {
            background-color: #0A4F78 !important;
            border-color: #0A4F78 !important;
            font-size: 0.75rem !important;
            font-weight: 700 !important;
            border-radius: 0.5rem !important;
            padding: 0.35rem 0.75rem !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05) !important;
            transition: all 0.2s ease !important;
        }
        .fc .fc-button-primary:hover, .fc .fc-button-primary:focus {
            background-color: #062B49 !important;
            border-color: #062B49 !important;
            transform: translateY(-1px);
        }
        .fc .fc-button-primary:disabled {
            background-color: #94A3B8 !important;
            border-color: #94A3B8 !important;
            opacity: 0.6;
        }
        .fc .fc-button-active {
            background-color: #2A8FC2 !important;
            border-color: #2A8FC2 !important;
            box-shadow: 0 2px 8px rgba(42, 143, 194, 0.4) !important;
        }
        .fc-theme-standard td, .fc-theme-standard th {
            border-color: rgba(226, 232, 240, 0.8) !important;
        }
        .dark .fc-theme-standard td, .dark .fc-theme-standard th {
            border-color: rgba(255, 255, 255, 0.08) !important;
        }
        .fc .fc-col-header-cell-cushion {
            font-size: 0.75rem !important;
            font-weight: 800 !important;
            color: #64748B !important;
            text-transform: uppercase !important;
            padding: 8px 4px !important;
        }
        .dark .fc .fc-col-header-cell-cushion {
            color: #94A3B8 !important;
        }
        .fc .fc-daygrid-day-number {
            font-size: 0.8rem !important;
            font-weight: 700 !important;
            padding: 6px 8px !important;
            color: inherit !important;
        }
        .fc .fc-daygrid-day.fc-day-today {
            background-color: rgba(42, 143, 194, 0.08) !important;
        }
        .dark .fc .fc-daygrid-day.fc-day-today {
            background-color: rgba(42, 143, 194, 0.15) !important;
        }
        .fc-event-mr-schedule {
            border-radius: 8px !important;
            padding: 2px 6px !important;
            font-size: 0.72rem !important;
            font-weight: 700 !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08) !important;
            cursor: pointer !important;
            transition: transform 0.15s ease, box-shadow 0.15s ease !important;
            border-width: 1.5px !important;
        }
        .fc-event-mr-schedule:hover {
            transform: translateY(-1px) scale(1.02) !important;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15) !important;
        }
        .fc .fc-list-event:hover td {
            background-color: rgba(42, 143, 194, 0.08) !important;
        }
        .dark .fc .fc-list-event:hover td {
            background-color: rgba(42, 143, 194, 0.15) !important;
        }
    </style>
    @endpush

    <x-slot name="actions">
        <div class="flex items-center gap-2 flex-wrap">
            <button type="button" onclick="openScheduleModal()" class="btn btn-primary text-xs sm:text-sm font-bold shadow-sm flex items-center gap-2 bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-700 hover:to-blue-700 text-white border-0 cursor-pointer">
                <i class="fa-solid fa-calendar-plus"></i>
                <span>{{ app()->getLocale() === 'ar' ? 'جدولة زيارة سريعة' : 'Quick Schedule Visit' }}</span>
            </button>

            <button type="button" onclick="openBulkScheduleModal()" class="btn btn-secondary text-xs sm:text-sm font-bold shadow-sm flex items-center gap-2 bg-purple-50 text-purple-700 border-purple-200 hover:bg-purple-100 dark:bg-purple-950/40 dark:text-purple-300 dark:border-purple-800 cursor-pointer">
                <i class="fa-solid fa-bolt text-purple-600 dark:text-purple-400"></i>
                <span>{{ app()->getLocale() === 'ar' ? 'جدولة جماعية متعددة' : 'Bulk Schedule Dispatcher' }}</span>
            </button>

            <button type="button" onclick="openRecordDirectModal()" class="btn btn-outline text-xs sm:text-sm font-bold shadow-sm flex items-center gap-2 cursor-pointer">
                <i class="fa-solid fa-circle-check text-emerald-500"></i>
                <span>{{ app()->getLocale() === 'ar' ? 'تسجيل زيارة مباشرة' : 'Direct Visit Entry' }}</span>
            </button>

            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary text-xs sm:text-sm font-bold shadow-sm flex items-center gap-1.5 border border-slate-200 dark:border-[#15456E]">
                <i class="fa-solid fa-chart-pie text-cyan-500"></i>
                <span>{{ app()->getLocale() === 'ar' ? 'لوحة النظرة العامة' : 'MR Overview' }}</span>
            </a>

            @if(auth()->user() && auth()->user()->canManageAllMr())
            <a href="{{ route('admin.mr.live-map') }}" class="btn btn-secondary text-xs sm:text-sm font-bold shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-earth-americas text-cyan-500"></i>
                <span>{{ app()->getLocale() === 'ar' ? 'الخريطة المباشرة' : 'Live Ops Map' }}</span>
            </a>
            @endif

            <a href="{{ url('/mr') }}" target="_blank" class="btn btn-ghost text-xs sm:text-sm font-bold shadow-sm flex items-center gap-1.5 text-cyan-600 dark:text-cyan-400">
                <i class="fa-solid fa-mobile-screen-button"></i>
                <span>{{ app()->getLocale() === 'ar' ? 'بوابة المندوب ↗' : 'MR Portal ↗' }}</span>
            </a>
        </div>
    </x-slot>

    <!-- TOP KPI & OPERATIONAL INTELLIGENCE METRIC CARDS -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <!-- 1. Active Reps Today -->
        <div class="card p-4 border border-cyan-100 dark:border-cyan-900/40 bg-gradient-to-br from-cyan-50/60 to-white dark:from-cyan-950/20 dark:to-gray-800 shadow-xs relative overflow-hidden">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-cyan-700 dark:text-cyan-400 uppercase tracking-wider">
                    {{ app()->getLocale() === 'ar' ? 'المناديب النشطين اليوم' : 'Active Reps on Duty' }}
                </span>
                <span class="w-8 h-8 rounded-lg bg-cyan-100 dark:bg-cyan-900/60 flex items-center justify-center text-cyan-600 dark:text-cyan-300">
                    <i class="fa-solid fa-user-doctor"></i>
                </span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white">{{ $kpis['active_reps_today'] }}</span>
                <span class="text-xs text-gray-500 dark:text-gray-400 font-semibold">/ {{ $kpis['total_reps'] }} {{ app()->getLocale() === 'ar' ? 'مندوب كلي' : 'total' }}</span>
            </div>
            <div class="mt-2 flex items-center gap-1.5 text-xs text-cyan-600 dark:text-cyan-400 font-medium">
                <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>{{ $kpis['total_reps'] > 0 ? round(($kpis['active_reps_today'] / $kpis['total_reps']) * 100) : 0 }}% {{ app()->getLocale() === 'ar' ? 'في الميدان اليوم' : 'in field today' }}</span>
            </div>
        </div>

        <!-- 2. Today's Scheduled Visits & Completion -->
        <div class="card p-4 border border-emerald-100 dark:border-emerald-900/40 bg-gradient-to-br from-emerald-50/60 to-white dark:from-emerald-950/20 dark:to-gray-800 shadow-xs relative overflow-hidden">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider">
                    {{ app()->getLocale() === 'ar' ? 'زيارات اليوم المنجزة' : "Today's Visits Completed" }}
                </span>
                <span class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/60 flex items-center justify-center text-emerald-600 dark:text-emerald-300">
                    <i class="fa-solid fa-calendar-check"></i>
                </span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-black text-emerald-600 dark:text-emerald-400">{{ $kpis['today_completed'] }}</span>
                <span class="text-xs text-gray-500 dark:text-gray-400 font-semibold">/ {{ $kpis['today_total'] }} {{ app()->getLocale() === 'ar' ? 'مجدولة' : 'planned' }}</span>
            </div>
            <div class="mt-2 w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5 overflow-hidden">
                <div class="bg-emerald-500 h-1.5 rounded-full transition-all duration-500" style="width: {{ $kpis['today_completion_rate'] }}%"></div>
            </div>
            <div class="mt-1.5 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                <span>{{ $kpis['today_completion_rate'] }}% {{ app()->getLocale() === 'ar' ? 'مكتمل' : 'completed' }}</span>
                <span>{{ $kpis['today_planned'] }} {{ app()->getLocale() === 'ar' ? 'متبقي' : 'pending' }}</span>
            </div>
        </div>

        <!-- 3. Active Cycle Target vs Executed -->
        <div class="card p-4 border border-indigo-100 dark:border-indigo-900/40 bg-gradient-to-br from-indigo-50/60 to-white dark:from-indigo-950/20 dark:to-gray-800 shadow-xs relative overflow-hidden">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-indigo-700 dark:text-indigo-400 uppercase tracking-wider">
                    {{ app()->getLocale() === 'ar' ? 'تغطية الدورة الحالية' : 'Active Cycle Coverage' }}
                </span>
                <span class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/60 flex items-center justify-center text-indigo-600 dark:text-indigo-300">
                    <i class="fa-solid fa-chart-pie"></i>
                </span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-black text-indigo-600 dark:text-indigo-400">{{ $kpis['cycle_visits_done'] }}</span>
                <span class="text-xs text-gray-500 dark:text-gray-400 font-semibold">/ {{ $kpis['cycle_target_visits'] }} {{ app()->getLocale() === 'ar' ? 'مطلوبة' : 'target' }}</span>
            </div>
            <div class="mt-2 w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5 overflow-hidden">
                <div class="bg-indigo-500 h-1.5 rounded-full transition-all duration-500" style="width: {{ min(100, $kpis['cycle_visit_progress_rate']) }}%"></div>
            </div>
            <div class="mt-1.5 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                <span class="font-bold text-indigo-600 dark:text-indigo-300">{{ $kpis['cycle_visit_progress_rate'] }}%</span>
                <span class="truncate max-w-[120px]">{{ $activeCycle?->name ?? 'Cycle' }}</span>
            </div>
        </div>

        <!-- 4. Geofence & GPS Accuracy Rate -->
        <div class="card p-4 border border-amber-100 dark:border-amber-900/40 bg-gradient-to-br from-amber-50/60 to-white dark:from-amber-950/20 dark:to-gray-800 shadow-xs relative overflow-hidden">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-amber-700 dark:text-amber-400 uppercase tracking-wider">
                    {{ app()->getLocale() === 'ar' ? 'دقة ومطابقة GPS' : 'GPS Geofence Compliance' }}
                </span>
                <span class="w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-900/60 flex items-center justify-center text-amber-600 dark:text-amber-300">
                    <i class="fa-solid fa-satellite-dish"></i>
                </span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-2xl sm:text-3xl font-black text-amber-600 dark:text-amber-400">{{ $kpis['today_gps_rate'] }}%</span>
                <span class="text-xs text-gray-500 dark:text-gray-400 font-semibold">{{ app()->getLocale() === 'ar' ? 'نطاق معتمد' : 'verified' }}</span>
            </div>
            <div class="mt-2 flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
                <i class="fa-solid fa-award text-amber-500"></i>
                <span>{{ $kpis['cycle_achieved_points'] }} / {{ $kpis['cycle_target_points'] }} {{ app()->getLocale() === 'ar' ? 'نقاط مكتسبة' : 'pts achieved' }}</span>
            </div>
        </div>
    </div>

    <!-- ACTIVE MEDICAL REPRESENTATIVES HORIZONTAL INTELLIGENCE RIBBON -->
    <div class="card p-4 mb-6 shadow-sm">
        <div class="flex items-center justify-between mb-3 pb-2 border-b border-gray-100 dark:border-gray-700/60">
            <div class="flex items-center gap-2">
                <i class="fa-solid {{ $isRep ? 'fa-user-check text-emerald-500' : 'fa-users text-cyan-600 dark:text-cyan-400' }}"></i>
                <h3 class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">
                    @if($isRep)
                        {{ app()->getLocale() === 'ar' ? 'حالة نشاطي ومؤشرات أدائي الميداني' : 'My Live Status & Daily Target' }}
                    @else
                        {{ app()->getLocale() === 'ar' ? 'مناديب الدعاية الطبية ومتابعة الأداء الميداني' : 'Medical Representatives Quick Filter & Live Status' }}
                    @endif
                </h3>
            </div>
            <div class="flex items-center gap-2">
                @if(!$isRep && $selectedMrId)
                    <button type="button" onclick="selectRepFilter('')" class="text-xs text-rose-500 hover:text-rose-700 font-bold flex items-center gap-1 cursor-pointer">
                        <i class="fa-solid fa-xmark"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'عرض جميع المناديب' : 'Reset Rep Filter' }}</span>
                    </button>
                @endif
            </div>
        </div>

        @if($isRep)
            @php $myRep = $repsData->first(); @endphp
            <div class="flex flex-wrap items-center justify-between gap-4 bg-gradient-to-r from-cyan-50/70 via-white to-blue-50/70 dark:from-cyan-950/20 dark:via-gray-800 dark:to-blue-950/20 p-3.5 rounded-xl border border-cyan-100 dark:border-cyan-900/40">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-cyan-600 to-blue-600 text-white flex items-center justify-center font-black text-base shadow-sm">
                        {{ substr($currentUser->name, 0, 1) }}
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h4 class="text-sm font-black text-gray-900 dark:text-white">{{ $currentUser->name }}</h4>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                {{ app()->getLocale() === 'ar' ? 'نشط في الميدان' : 'Field Active' }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            <i class="fa-solid fa-location-dot text-cyan-500 text-[10px]"></i>
                            {{ $myRep['area_name'] !== '—' ? $myRep['area_name'] : ($myRep['city_name'] !== '—' ? $myRep['city_name'] : (app()->getLocale() === 'ar' ? 'المنطقة المخصصة' : 'Assigned Territory')) }}
                            &bull; <span class="text-gray-400">{{ $currentUser->email }}</span>
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-4 sm:gap-6 flex-wrap">
                    <div class="text-center sm:text-start">
                        <span class="text-[10px] font-bold text-gray-400 uppercase block">{{ app()->getLocale() === 'ar' ? 'أطباء محفظتي' : 'My Doctors' }}</span>
                        <span class="text-sm font-black text-gray-800 dark:text-gray-200">{{ $myRep['assigned_doctors_count'] ?? 0 }} {{ app()->getLocale() === 'ar' ? 'طبيب' : 'doctors' }}</span>
                    </div>

                    <div class="text-center sm:text-start">
                        <span class="text-[10px] font-bold text-gray-400 uppercase block">{{ app()->getLocale() === 'ar' ? 'إنجاز اليوم' : "Today's Score" }}</span>
                        <span class="text-sm font-black text-cyan-600 dark:text-cyan-400">{{ $myRep['today_completed'] ?? 0 }} / {{ $myRep['today_total'] ?? 0 }}</span>
                    </div>

                    <div class="text-center sm:text-start">
                        <span class="text-[10px] font-bold text-gray-400 uppercase block">{{ app()->getLocale() === 'ar' ? 'معدل الدورة' : 'Cycle Progress' }}</span>
                        <span class="text-sm font-black text-indigo-600 dark:text-indigo-400">{{ $myRep['cycle_pct'] ?? 0 }}%</span>
                    </div>

                    <button type="button" onclick="openRepDrawer({{ $currentUser->id }})" class="btn btn-outline text-xs font-bold py-1.5 px-3 flex items-center gap-1.5 cursor-pointer shadow-2xs">
                        <i class="fa-solid fa-id-badge text-cyan-600"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'ملفي الطبي 360°' : 'My 360° Dossier' }}</span>
                    </button>
                </div>
            </div>
        @else
            <div class="flex items-center gap-3 overflow-x-auto pb-2 scrollbar-thin">
                <!-- All Reps Pill -->
                <button type="button" onclick="selectRepFilter('')" class="flex-shrink-0 p-3 rounded-xl border text-start transition-all cursor-pointer {{ empty($selectedMrId) ? 'border-cyan-500 bg-cyan-50/50 dark:bg-cyan-950/40 shadow-xs ring-2 ring-cyan-500/20' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-cyan-300' }}">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-700 dark:text-gray-300 font-bold text-xs">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-gray-900 dark:text-white">{{ app()->getLocale() === 'ar' ? 'جميع المناديب' : 'All Representatives' }}</span>
                            <span class="text-[10px] text-gray-500">{{ $medicalReps->count() }} {{ app()->getLocale() === 'ar' ? 'مندوب' : 'reps' }}</span>
                        </div>
                    </div>
                </button>

                <!-- Individual Rep Cards -->
                @foreach($repsData as $rep)
                    @php
                        $isSelected = ($selectedMrId == $rep['id']);
                    @endphp
                    <div class="flex-shrink-0 p-3 rounded-xl border text-start transition-all relative group {{ $isSelected ? 'border-cyan-500 bg-cyan-50/50 dark:bg-cyan-950/40 shadow-xs ring-2 ring-cyan-500/20' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-cyan-400' }}">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-lg bg-gradient-to-tr from-cyan-600 to-indigo-600 text-white flex items-center justify-center font-black text-xs relative cursor-pointer" onclick="selectRepFilter('{{ $rep['id'] }}')">
                                {{ substr($rep['name'], 0, 1) }}
                                @if($rep['today_total'] > 0)
                                    <span class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-emerald-500 border-2 border-white dark:border-gray-800 rounded-full"></span>
                                @endif
                            </div>
                            <div class="cursor-pointer" onclick="selectRepFilter('{{ $rep['id'] }}')">
                                <span class="block text-xs font-bold text-gray-900 dark:text-white truncate max-w-[130px]">{{ $rep['name'] }}</span>
                                <span class="text-[10px] text-gray-500 block truncate max-w-[130px]">{{ $rep['area_name'] !== '—' ? $rep['area_name'] : $rep['city_name'] }}</span>
                            </div>
                            <div class="border-l rtl:border-l-0 rtl:border-r border-gray-100 dark:border-gray-700 pl-2 rtl:pl-0 rtl:pr-2 flex flex-col items-end gap-1">
                                <span class="text-[10px] font-black {{ $rep['today_pct'] >= 100 ? 'text-emerald-600' : 'text-cyan-600' }}">
                                    {{ $rep['today_completed'] }}/{{ $rep['today_total'] }}
                                </span>
                                <button type="button" onclick="openRepDrawer({{ $rep['id'] }})" class="text-[10px] text-gray-400 hover:text-cyan-600 flex items-center gap-0.5" title="View 360 Dossier">
                                    <i class="fa-solid fa-id-badge"></i>
                                    <span>360°</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- MAIN INTERACTIVE CONTROL TOOLBAR & FILTERS -->
    <div class="card p-4 mb-6 shadow-sm">
        <form method="GET" action="{{ route('admin.mr.dashboard') }}" id="filterForm">
            <!-- Hidden View & Date state -->
            <input type="hidden" name="view" id="viewModeInput" value="{{ $viewMode }}">
            <input type="hidden" name="date" id="selectedDateInput" value="{{ $selectedDate }}">

            <!-- Row 1: Date Bar & View Switcher -->
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-4 border-b border-gray-100 dark:border-gray-700/60">
                <!-- Date Navigator -->
                @php
                    $carbonDate = \Carbon\Carbon::parse($selectedDate);
                    $prevDate = $carbonDate->copy()->subDay()->toDateString();
                    $nextDate = $carbonDate->copy()->addDay()->toDateString();
                    $todayDate = now()->toDateString();
                @endphp
                <div class="flex items-center gap-2 flex-wrap">
                    <div class="inline-flex rounded-lg shadow-2xs border border-gray-200 dark:border-gray-700 overflow-hidden bg-white dark:bg-gray-800">
                        <button type="button" onclick="setDateAndSubmit('{{ $prevDate }}')" class="px-3 py-1.5 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors cursor-pointer" title="{{ app()->getLocale() === 'ar' ? 'اليوم السابق' : 'Previous Day' }}">
                            <i class="fa-solid fa-chevron-right rtl:rotate-0 ltr:rotate-180 text-xs"></i>
                        </button>
                        <button type="button" onclick="setDateAndSubmit('{{ $todayDate }}')" class="px-3 py-1.5 text-xs font-bold cursor-pointer {{ $selectedDate === $todayDate ? 'bg-cyan-600 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                            {{ app()->getLocale() === 'ar' ? 'اليوم' : 'Today' }}
                        </button>
                        <button type="button" onclick="setDateAndSubmit('{{ $nextDate }}')" class="px-3 py-1.5 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors cursor-pointer" title="{{ app()->getLocale() === 'ar' ? 'اليوم التالي' : 'Next Day' }}">
                            <i class="fa-solid fa-chevron-left rtl:rotate-0 ltr:rotate-180 text-xs"></i>
                        </button>
                    </div>

                    <div class="relative flex items-center">
                        <input type="date" value="{{ $selectedDate }}" onchange="setDateAndSubmit(this.value)" class="form-input text-xs sm:text-sm py-1 px-2.5 rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 font-semibold text-gray-800 dark:text-gray-200">
                    </div>

                    <span class="text-xs sm:text-sm font-bold text-gray-700 dark:text-gray-300 hidden sm:inline-block">
                        {{ $carbonDate->isoFormat('dddd, D MMMM YYYY') }}
                    </span>
                </div>

                <!-- View Switcher Pills -->
                <div class="inline-flex rounded-xl p-1 bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-xs font-bold gap-1 self-start lg:self-auto overflow-x-auto max-w-full">
                    <!-- 1. Interactive Calendar View (NEW) -->
                    <button type="button" onclick="setViewAndSubmit('calendar')" class="px-3 py-1.5 rounded-lg transition-all flex items-center gap-1.5 cursor-pointer {{ $viewMode === 'calendar' ? 'bg-gradient-to-r from-cyan-600 to-blue-600 text-white shadow-xs' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                        <i class="fa-regular fa-calendar-days text-xs"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'التقويم الشامل (Calender)' : 'Interactive Calendar' }}</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $viewMode === 'calendar' ? 'bg-white/20 text-white' : 'bg-cyan-100 text-cyan-800 dark:bg-cyan-950 dark:text-cyan-300' }}">{{ count($calendarEvents) }}</span>
                    </button>

                    <!-- 2. Daily Agenda List -->
                    <button type="button" onclick="setViewAndSubmit('day')" class="px-3 py-1.5 rounded-lg transition-all flex items-center gap-1.5 cursor-pointer {{ $viewMode === 'day' ? 'bg-white dark:bg-gray-700 text-cyan-600 dark:text-cyan-400 shadow-xs' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900' }}">
                        <i class="fa-solid fa-list-check"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'بطاقات اليوم' : 'Daily Cards' }}</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-cyan-100 text-cyan-800 dark:bg-cyan-950 dark:text-cyan-300">{{ $scheduledVisits->count() }}</span>
                    </button>

                    <!-- 3. Hourly Timeline -->
                    <button type="button" onclick="setViewAndSubmit('timeline')" class="px-3 py-1.5 rounded-lg transition-all flex items-center gap-1.5 cursor-pointer {{ $viewMode === 'timeline' ? 'bg-white dark:bg-gray-700 text-cyan-600 dark:text-cyan-400 shadow-xs' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900' }}">
                        <i class="fa-solid fa-timeline"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'خط زمني' : 'Timeline' }}</span>
                    </button>

                    <!-- 4. 7-Day Week Matrix -->
                    <button type="button" onclick="setViewAndSubmit('week')" class="px-3 py-1.5 rounded-lg transition-all flex items-center gap-1.5 cursor-pointer {{ $viewMode === 'week' ? 'bg-white dark:bg-gray-700 text-cyan-600 dark:text-cyan-400 shadow-xs' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900' }}">
                        <i class="fa-solid fa-calendar-week"></i>
                        <span>{{ app()->getLocale() === 'ar' ? '7 أيام' : '7 Days' }}</span>
                    </button>

                    <!-- 5. Reps 360 Hub -->
                    <button type="button" onclick="setViewAndSubmit('reps')" class="px-3 py-1.5 rounded-lg transition-all flex items-center gap-1.5 cursor-pointer {{ $viewMode === 'reps' ? 'bg-white dark:bg-gray-700 text-cyan-600 dark:text-cyan-400 shadow-xs' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900' }}">
                        <i class="fa-solid fa-users-gear text-indigo-500"></i>
                        <span>{{ $isRep ? (app()->getLocale() === 'ar' ? 'ملفي 360°' : 'My 360° Profile') : (app()->getLocale() === 'ar' ? 'ملفات 360°' : 'Reps 360°') }}</span>
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-indigo-100 text-indigo-800 dark:bg-indigo-950 dark:text-indigo-300">{{ $medicalReps->count() }}</span>
                    </button>
                </div>
            </div>

            <!-- Row 2: Rep Quick Selector & Multi-Filters -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3 pt-4">
                @if($isRep)
                    <!-- Rep Fixed Badge for Medical Representative -->
                    <div class="sm:col-span-2">
                        <label class="block text-[11px] font-bold text-cyan-700 dark:text-cyan-400 uppercase tracking-wider mb-1">
                            {{ app()->getLocale() === 'ar' ? 'المندوب الطبي الحالي (أنت)' : 'Medical Representative (You)' }}
                        </label>
                        <div class="flex items-center gap-2 p-2 rounded-lg bg-cyan-50/70 dark:bg-cyan-950/40 border border-cyan-200 dark:border-cyan-800">
                            <div class="w-7 h-7 rounded-md bg-gradient-to-tr from-cyan-600 to-blue-600 text-white flex items-center justify-center font-bold text-xs">
                                {{ substr($currentUser->name, 0, 1) }}
                            </div>
                            <span class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white">{{ $currentUser->name }}</span>
                            <span class="ms-auto text-[10px] font-bold px-2 py-0.5 rounded-full bg-cyan-100 text-cyan-800 dark:bg-cyan-900 dark:text-cyan-300">
                                {{ app()->getLocale() === 'ar' ? 'جدولي الميداني' : 'My Field Schedule' }}
                            </span>
                        </div>
                        <input type="hidden" name="mr_id" value="{{ $currentUser->id }}">
                    </div>
                @else
                    <!-- Admin Rep Dropdown Selector -->
                    <div class="sm:col-span-2">
                        <label class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                            {{ app()->getLocale() === 'ar' ? 'المندوب الطبي' : 'Medical Representative' }}
                        </label>
                        <select name="mr_id" id="repFilterSelect" onchange="this.form.submit()" class="form-select w-full text-xs sm:text-sm py-1.5 rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 font-semibold">
                            <option value="">{{ app()->getLocale() === 'ar' ? 'جميع المناديب (' . $medicalReps->count() . ')' : 'All Medical Reps (' . $medicalReps->count() . ')' }}</option>
                            @foreach($medicalReps as $rep)
                                <option value="{{ $rep->id }}" {{ $selectedMrId == $rep->id ? 'selected' : '' }}>
                                    {{ $rep->name }} ({{ $rep->city?->name ?? 'Territory' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- Cycle Dropdown -->
                <div>
                    <label class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                        {{ app()->getLocale() === 'ar' ? 'دورة الزيارات' : 'Visit Cycle' }}
                    </label>
                    <select name="cycle_id" onchange="this.form.submit()" class="form-select w-full text-xs sm:text-sm py-1.5 rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800">
                        @foreach($cycles as $cycle)
                            <option value="{{ $cycle->id }}" {{ $cycle->id == $activeCycle?->id ? 'selected' : '' }}>
                                {{ $cycle->name }} {{ $cycle->status === 'active' ? '★' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                        {{ app()->getLocale() === 'ar' ? 'حالة الموعد' : 'Schedule Status' }}
                    </label>
                    <select name="status" onchange="this.form.submit()" class="form-select w-full text-xs sm:text-sm py-1.5 rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800">
                        <option value="all" {{ $selectedStatus === 'all' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'الكل' : 'All Statuses' }}</option>
                        <option value="planned" {{ $selectedStatus === 'planned' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'مجدولة (قيد الانتظار)' : 'Planned' }}</option>
                        <option value="completed" {{ $selectedStatus === 'completed' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'مكتملة' : 'Completed' }}</option>
                        <option value="in_progress" {{ $selectedStatus === 'in_progress' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'جارية الآن' : 'In Progress' }}</option>
                        <option value="cancelled" {{ $selectedStatus === 'cancelled' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'ملغاة' : 'Cancelled' }}</option>
                    </select>
                </div>

                <!-- Specialty Filter -->
                <div>
                    <label class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                        {{ app()->getLocale() === 'ar' ? 'تخصص الطبيب' : 'Doctor Specialty' }}
                    </label>
                    <select name="specialty_id" onchange="this.form.submit()" class="form-select w-full text-xs sm:text-sm py-1.5 rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800">
                        <option value="">{{ app()->getLocale() === 'ar' ? 'جميع التخصصات' : 'All Specialties' }}</option>
                        @foreach($specialties as $sp)
                            <option value="{{ $sp->id }}" {{ $selectedSpecialtyId == $sp->id ? 'selected' : '' }}>
                                {{ $sp->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Doctor Search Input (Client Filter) -->
                <div>
                    <label class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">
                        {{ app()->getLocale() === 'ar' ? 'بحث سريع' : 'Live Filter' }}
                    </label>
                    <div class="relative">
                        <input type="text" id="liveSearchInput" onkeyup="filterScheduleCards(this.value)" placeholder="{{ app()->getLocale() === 'ar' ? 'اسم الطبيب / العيادة...' : 'Filter Doctor / Clinic...' }}" class="form-input w-full text-xs sm:text-sm py-1.5 pr-8 pl-3 rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800">
                        <i class="fa-solid fa-magnifying-glass absolute right-2.5 rtl:right-auto rtl:left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- ==================== MAIN VIEW SWITCHER BODY ==================== -->

    <!-- VIEW 1: FULL INTERACTIVE CALENDAR WITH RIGHT-CLICK QUICK ACTIONS & SCHEDULES AGENDA -->
    @if($viewMode === 'calendar')
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 mb-6 items-start">
            <!-- LEFT 8 COLS: FULLCALENDAR MAIN CANVAS -->
            <div class="xl:col-span-8 card p-5 bg-white dark:bg-gray-800 shadow-sm rounded-2xl">
                <!-- Calendar Header Bar with Legend & Instructions -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4 pb-3 border-b border-gray-100 dark:border-gray-700">
                    <div class="flex items-center gap-2">
                        <span class="w-9 h-9 rounded-xl bg-gradient-to-tr from-cyan-600 to-blue-600 text-white flex items-center justify-center text-sm shadow-xs">
                            <i class="fa-regular fa-calendar-days"></i>
                        </span>
                        <div>
                            <h2 class="text-base font-black text-gray-900 dark:text-white">
                                {{ app()->getLocale() === 'ar' ? 'التقويم التفاعلي لمواعيد وزيارات المناديب' : 'Interactive MR Schedule & Visit Calendar' }}
                            </h2>
                            <span class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1.5 mt-0.5">
                                <i class="fa-solid fa-arrow-pointer text-cyan-500 text-[11px]"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'انقر بالزر الأيمن (Right-Click) على أي موعد أو خانة يوم للإجراءات السريعة، أو اسحب الموعد لإعادة جدولته' : 'Right-click any visit event or date cell for quick actions, or drag & drop to reschedule' }}</span>
                            </span>
                        </div>
                    </div>

                    <!-- Color Legend Pills -->
                    <div class="flex items-center gap-2 flex-wrap text-xs">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800 text-[11px] font-bold">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            <span>{{ app()->getLocale() === 'ar' ? 'مجدولة (Planned)' : 'Planned' }}</span>
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-cyan-50 text-cyan-700 border border-cyan-200 dark:bg-cyan-950/40 dark:text-cyan-300 dark:border-cyan-800 text-[11px] font-bold">
                            <span class="w-2 h-2 rounded-full bg-cyan-500"></span>
                            <span>{{ app()->getLocale() === 'ar' ? 'جارية (In Progress)' : 'In Progress' }}</span>
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800 text-[11px] font-bold">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>{{ app()->getLocale() === 'ar' ? 'مكتملة (Completed)' : 'Completed' }}</span>
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-rose-50 text-rose-700 border border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800 text-[11px] font-bold">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                            <span>{{ app()->getLocale() === 'ar' ? 'ملغاة (Cancelled)' : 'Cancelled' }}</span>
                        </span>
                    </div>
                </div>

                <!-- FullCalendar Mount Container -->
                <div id="fullCalendarContainer" class="min-h-[680px] p-2 bg-white dark:bg-gray-800 rounded-xl"></div>
            </div>

            <!-- RIGHT 4 COLS: SCHEDULES AGENDA & ACTIONS DOSSIER -->
            <div class="xl:col-span-4 space-y-4">
                <div class="card p-4 bg-white dark:bg-gray-800 shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700/80">
                    <!-- Panel Header -->
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-700/60 mb-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-cyan-500 animate-pulse"></span>
                                <h3 class="font-black text-sm text-gray-900 dark:text-white">
                                    {{ app()->getLocale() === 'ar' ? 'أجندة مواعيد اليوم' : "Day's Scheduled Visits" }}
                                </h3>
                            </div>
                            <span class="text-[11px] text-gray-500 dark:text-gray-400 block mt-0.5">
                                {{ $carbonDate->isoFormat('dddd, D MMMM YYYY') }}
                            </span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="px-2 py-0.5 rounded-full text-xs font-black bg-cyan-100 text-cyan-800 dark:bg-cyan-950 dark:text-cyan-300">
                                {{ $scheduledVisits->count() }} {{ app()->getLocale() === 'ar' ? 'زيارة' : 'visits' }}
                            </span>
                            <button type="button" onclick="openScheduleModal('{{ $selectedMrId ?? '' }}', '{{ $selectedDate }}', '10:00')" class="w-7 h-7 rounded-lg bg-cyan-600 hover:bg-cyan-700 text-white flex items-center justify-center text-xs shadow-2xs cursor-pointer" title="{{ app()->getLocale() === 'ar' ? 'إضافة موعد جديد' : 'Add New Schedule' }}">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Mini Status Breakdown Bar -->
                    <div class="grid grid-cols-3 gap-2 p-2 rounded-xl bg-gray-50/80 dark:bg-gray-900/40 mb-3 text-center text-xs">
                        <div>
                            <span class="text-[10px] text-gray-400 uppercase font-bold block">{{ app()->getLocale() === 'ar' ? 'المكتمل' : 'Done' }}</span>
                            <span class="font-black text-emerald-600 dark:text-emerald-400 text-sm">{{ $scheduledVisits->where('status', 'completed')->count() }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-gray-400 uppercase font-bold block">{{ app()->getLocale() === 'ar' ? 'قيد التنفيذ' : 'In Field' }}</span>
                            <span class="font-black text-cyan-600 dark:text-cyan-400 text-sm">{{ $scheduledVisits->where('status', 'in_progress')->count() }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-gray-400 uppercase font-bold block">{{ app()->getLocale() === 'ar' ? 'متبقي' : 'Planned' }}</span>
                            <span class="font-black text-amber-600 dark:text-amber-400 text-sm">{{ $scheduledVisits->where('status', 'planned')->count() }}</span>
                        </div>
                    </div>

                    <!-- Instant Search within Day Visits -->
                    <div class="relative mb-3">
                        <input type="text" onkeyup="filterSideSchedules(this.value)" placeholder="{{ app()->getLocale() === 'ar' ? 'بحث في مواعيد اليوم...' : 'Filter day visits...' }}" class="form-input w-full text-xs py-1.5 pr-8 pl-3 rounded-lg border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/30">
                        <i class="fa-solid fa-magnifying-glass absolute right-2.5 rtl:right-auto rtl:left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-[10px] pointer-events-none"></i>
                    </div>

                    <!-- Scrollable List of Scheduled Visits -->
                    <div class="space-y-3 max-h-[580px] overflow-y-auto pr-1 scrollbar-thin" id="sideSchedulesList">
                        @forelse($scheduledVisits as $visit)
                            @php
                                $contact = $visit->contact;
                                $rep = $visit->representative;
                                $statusColor = match($visit->status) {
                                    'completed' => 'emerald',
                                    'in_progress' => 'cyan',
                                    'cancelled' => 'rose',
                                    default => 'amber'
                                };
                                $timeStr = $visit->scheduled_at ? $visit->scheduled_at->format('h:i A') : '--:--';
                                $cleanDoc = $contact?->name ?? 'Doctor';
                                if (!preg_match('/^(dr\.|د\.|د\/|dr )/i', trim($cleanDoc))) {
                                    $cleanDoc = 'Dr. ' . $cleanDoc;
                                }
                                $classCode = $contact?->classification?->code ?? 'C';
                            @endphp
                            <div class="p-3 rounded-xl border border-gray-200 dark:border-gray-700/80 bg-gray-50/50 dark:bg-gray-900/30 hover:border-cyan-400 dark:hover:border-cyan-500 transition-all text-xs"
                                 id="side-schedule-item-{{ $visit->id }}"
                                 data-search="{{ strtolower($cleanDoc . ' ' . ($contact?->hospital_clinic_name ?? '') . ' ' . ($rep?->name ?? '') . ' ' . ($contact?->specialty?->name ?? '')) }}">
                                
                                <!-- Top Bar: Time, Status & Dropdown -->
                                <div class="flex items-center justify-between pb-2 border-b border-gray-100 dark:border-gray-700/60 mb-2">
                                    <div class="flex items-center gap-1.5">
                                        <span class="px-2 py-0.5 rounded font-black text-[11px] bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                            <i class="fa-regular fa-clock text-cyan-600 dark:text-cyan-400 text-[10px]"></i>
                                            <span>{{ $timeStr }}</span>
                                        </span>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800 dark:bg-{{ $statusColor }}-950/60 dark:text-{{ $statusColor }}-300">
                                            {{ $visit->status }}
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-1">
                                        <button type="button" onclick="openRescheduleModal({{ $visit->id }}, '{{ addslashes($cleanDoc) }}', '{{ $visit->scheduled_at?->format('Y-m-d') }}', '{{ $visit->scheduled_at?->format('H:i') }}')" class="w-6 h-6 rounded hover:bg-gray-200 dark:hover:bg-gray-700 flex items-center justify-center text-indigo-600 cursor-pointer" title="{{ app()->getLocale() === 'ar' ? 'إعادة جدولة' : 'Reschedule' }}">
                                            <i class="fa-solid fa-clock-rotate-left text-[11px]"></i>
                                        </button>
                                        @if($visit->status !== 'completed')
                                            <button type="button" onclick="updateScheduleStatus({{ $visit->id }}, 'completed')" class="w-6 h-6 rounded bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 hover:bg-emerald-600 hover:text-white flex items-center justify-center text-[10px] font-bold cursor-pointer" title="{{ app()->getLocale() === 'ar' ? 'اعتماد كمكتملة' : 'Mark Done' }}">
                                                <i class="fa-solid fa-check"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                <!-- Doctor Information -->
                                <div class="mb-2">
                                    <div class="flex items-start justify-between gap-1">
                                        <a href="{{ route('admin.mr.contacts.show', $contact?->id ?? 0) }}" class="font-black text-gray-900 dark:text-white hover:text-cyan-600 transition-colors truncate block">
                                            {{ $cleanDoc }}
                                        </a>
                                        <span class="px-1.5 py-0.2 rounded text-[10px] font-black {{ $classCode === 'A+' || $classCode === 'A' ? 'bg-amber-100 text-amber-900 dark:bg-amber-950 dark:text-amber-300' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                                            Class {{ $classCode }}
                                        </span>
                                    </div>
                                    <div class="text-[11px] text-gray-500 truncate mt-0.5">
                                        {{ $contact?->hospital_clinic_name ?? 'Clinic' }}
                                        @if($contact?->specialty)
                                            <span class="text-cyan-600 dark:text-cyan-400 font-semibold">• {{ $contact->specialty->name }}</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Rep Bar -->
                                <div class="p-1.5 rounded-lg bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/60 flex items-center justify-between gap-2 mb-2">
                                    <div class="flex items-center gap-1.5 truncate">
                                        <div class="w-5 h-5 rounded-full bg-cyan-600 text-white font-black text-[10px] flex items-center justify-center flex-shrink-0">
                                            {{ substr($rep?->name ?? 'MR', 0, 1) }}
                                        </div>
                                        <span class="text-[11px] font-semibold text-gray-700 dark:text-gray-300 truncate">{{ $rep?->name ?? 'Rep' }}</span>
                                    </div>
                                    <button type="button" onclick="openRepDrawer({{ $rep?->id ?? 0 }})" class="text-[10px] text-indigo-600 hover:underline font-bold flex-shrink-0 cursor-pointer">
                                        360°
                                    </button>
                                </div>

                                <!-- Action icons row -->
                                <div class="flex items-center justify-between text-[11px] pt-1">
                                    <div class="flex items-center gap-2">
                                        @if($contact?->phone)
                                            <a href="tel:{{ $contact->phone }}" class="text-gray-500 hover:text-cyan-600 flex items-center gap-0.5" title="Call">
                                                <i class="fa-solid fa-phone text-emerald-500 text-[10px]"></i>
                                                <span>{{ app()->getLocale() === 'ar' ? 'اتصال' : 'Call' }}</span>
                                            </a>
                                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contact->phone) }}" target="_blank" class="text-gray-500 hover:text-emerald-500 flex items-center gap-0.5" title="WhatsApp">
                                                <i class="fa-brands fa-whatsapp text-emerald-500 text-[10px]"></i>
                                                <span>واتساب</span>
                                            </a>
                                        @endif
                                        @if($contact?->latitude && $contact?->longitude)
                                            <a href="https://maps.google.com/?q={{ $contact->latitude }},{{ $contact->longitude }}" target="_blank" class="text-gray-500 hover:text-rose-500 flex items-center gap-0.5" title="Google Maps">
                                                <i class="fa-solid fa-location-dot text-rose-500 text-[10px]"></i>
                                                <span>GPS</span>
                                            </a>
                                        @endif
                                    </div>

                                    @if($visit->status !== 'completed')
                                        <button type="button" onclick="updateScheduleStatus({{ $visit->id }}, 'completed')" class="px-2 py-0.5 rounded bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[10px] flex items-center gap-1 cursor-pointer">
                                            <i class="fa-solid fa-check text-[9px]"></i>
                                            <span>{{ app()->getLocale() === 'ar' ? 'إنجاز' : 'Done' }}</span>
                                        </button>
                                    @else
                                        <span class="text-emerald-600 font-bold text-[10px] flex items-center gap-1">
                                            <i class="fa-solid fa-circle-check text-[9px]"></i>
                                            <span>{{ app()->getLocale() === 'ar' ? 'تم الإنجاز' : 'Verified' }}</span>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center border-dashed border-2 border-gray-200 dark:border-gray-700 rounded-xl bg-white/40 dark:bg-gray-800/40">
                                <div class="w-10 h-10 rounded-full bg-cyan-50 dark:bg-cyan-950/60 text-cyan-600 flex items-center justify-center mx-auto mb-2 text-base">
                                    <i class="fa-solid fa-calendar-xmark"></i>
                                </div>
                                <h4 class="font-bold text-xs text-gray-900 dark:text-white mb-1">
                                    {{ app()->getLocale() === 'ar' ? 'لا توجد مواعيد لهذا اليوم' : 'No visits scheduled for this date' }}
                                </h4>
                                <p class="text-[11px] text-gray-400 mb-3">
                                    {{ app()->getLocale() === 'ar' ? 'يمكنك جدولة زيارة فورية بضغطة زر واحدة' : 'Schedule a visit with one click' }}
                                </p>
                                <button type="button" onclick="openScheduleModal('{{ $selectedMrId ?? '' }}', '{{ $selectedDate }}', '10:00')" class="btn btn-primary text-xs py-1.5 px-3 bg-cyan-600 hover:bg-cyan-700 text-white font-bold cursor-pointer">
                                    <i class="fa-solid fa-plus text-[10px] mr-1"></i>
                                    <span>{{ app()->getLocale() === 'ar' ? 'جدولة زيارة الآن' : 'Schedule Visit Now' }}</span>
                                </button>
                            </div>
                        @endforelse
                    </div>

                    <!-- Panel Footer -->
                    <div class="pt-3 border-t border-gray-100 dark:border-gray-700/60 mt-3 flex items-center justify-between text-xs">
                        <button type="button" onclick="setViewAndSubmit('day')" class="text-cyan-600 hover:text-cyan-700 dark:text-cyan-400 font-bold flex items-center gap-1 cursor-pointer">
                            <span>{{ app()->getLocale() === 'ar' ? 'عرض شبكة البطاقات اليومية' : 'View Daily Cards Grid' }}</span>
                            <i class="fa-solid fa-arrow-right rtl:rotate-180 text-[10px]"></i>
                        </button>
                        <button type="button" onclick="printScheduleAgenda()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 flex items-center gap-1 cursor-pointer">
                            <i class="fa-solid fa-print text-[11px]"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'طباعة' : 'Print' }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- VIEW 2: DAILY AGENDA LIST / CARDS -->
    @if($viewMode === 'day')
        <div class="space-y-4 mb-6">
            <!-- Header bar of today's schedule -->
            <div class="flex items-center justify-between flex-wrap gap-2 px-1">
                <div class="flex items-center gap-2">
                    <h2 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <i class="fa-solid fa-clipboard-list text-cyan-600 dark:text-cyan-400"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'جدول مواعيد الزيارات ليوم' : 'Scheduled Visits Agenda for' }} {{ $carbonDate->format('Y-m-d') }}</span>
                    </h2>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-black bg-cyan-100 text-cyan-800 dark:bg-cyan-950 dark:text-cyan-300">
                        {{ $scheduledVisits->count() }} {{ app()->getLocale() === 'ar' ? 'زيارة' : 'visits' }}
                    </span>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" onclick="printScheduleAgenda()" class="btn btn-secondary text-xs py-1.5 px-3 font-semibold flex items-center gap-1.5 shadow-2xs cursor-pointer">
                        <i class="fa-solid fa-print"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'طباعة الأجندة' : 'Print Agenda' }}</span>
                    </button>
                    <button type="button" onclick="openScheduleModal()" class="btn btn-primary text-xs py-1.5 px-3 font-semibold flex items-center gap-1.5 bg-cyan-600 hover:bg-cyan-700 text-white shadow-2xs cursor-pointer">
                        <i class="fa-solid fa-plus"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'إضافة موعد' : 'Add Schedule' }}</span>
                    </button>
                </div>
            </div>

            @if($scheduledVisits->isEmpty())
                <div class="card p-12 text-center border-dashed border-2 border-gray-300 dark:border-gray-700 bg-white/50 dark:bg-gray-800/50 rounded-2xl">
                    <div class="w-16 h-16 rounded-full bg-cyan-50 dark:bg-cyan-950/50 text-cyan-600 dark:text-cyan-400 flex items-center justify-center mx-auto mb-4 text-2xl">
                        <i class="fa-solid fa-calendar-xmark"></i>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1">
                        {{ app()->getLocale() === 'ar' ? 'لا توجد مواعيد مجدولة لهذا اليوم' : 'No visits scheduled for this date' }}
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 max-w-md mx-auto mb-5">
                        {{ app()->getLocale() === 'ar' ? 'يمكنك بدء جدولة زيارات المناديب الميدانية أو استخدام الجدولة الجماعية التلقائية لتوزيع مواعيد الأطباء فوراً.' : 'You can quickly schedule visits for reps or use the bulk dispatcher to schedule all doctors on this date.' }}
                    </p>
                    <div class="flex items-center justify-center gap-3">
                        <button type="button" onclick="openScheduleModal()" class="btn btn-primary text-xs font-bold bg-cyan-600 hover:bg-cyan-700 text-white py-2 px-4 shadow-sm cursor-pointer">
                            <i class="fa-solid fa-plus mr-1"></i> {{ app()->getLocale() === 'ar' ? 'جدولة زيارة الآن' : 'Schedule Visit Now' }}
                        </button>
                        <button type="button" onclick="openBulkScheduleModal()" class="btn btn-secondary text-xs font-bold py-2 px-4 shadow-sm cursor-pointer">
                            <i class="fa-solid fa-bolt text-purple-600 mr-1"></i> {{ app()->getLocale() === 'ar' ? 'جدولة جماعية' : 'Bulk Dispatcher' }}
                        </button>
                    </div>
                </div>
            @else
                <!-- Schedule Cards Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4" id="scheduleCardsContainer">
                    @foreach($scheduledVisits as $visit)
                        @php
                            $contact = $visit->contact;
                            $rep = $visit->representative;
                            $statusColor = match($visit->status) {
                                'completed' => 'emerald',
                                'in_progress' => 'cyan',
                                'cancelled' => 'rose',
                                default => 'amber'
                            };
                            $timeStr = $visit->scheduled_at ? $visit->scheduled_at->format('h:i A') : '--:--';
                            $classBadge = $contact?->classification?->code ?? 'C';
                        @endphp
                        <div class="card p-4 border border-gray-200 dark:border-gray-700/80 hover:border-cyan-400 dark:hover:border-cyan-500 transition-all shadow-xs relative bg-white dark:bg-gray-800 rounded-xl schedule-card"
                             data-doctor="{{ strtolower($contact?->name ?? '') }}"
                             data-rep="{{ strtolower($rep?->name ?? '') }}"
                             data-clinic="{{ strtolower($contact?->hospital_clinic_name ?? '') }}"
                             data-specialty="{{ strtolower($contact?->specialty?->name ?? '') }}"
                             id="schedule-card-{{ $visit->id }}">
                            
                            <!-- Card Header: Time & Status -->
                            <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-gray-700/60 mb-3">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-black bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                        <i class="fa-regular fa-clock text-cyan-600 dark:text-cyan-400"></i>
                                        <span>{{ $timeStr }}</span>
                                    </span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800 dark:bg-{{ $statusColor }}-950/60 dark:text-{{ $statusColor }}-300">
                                        {{ $visit->status }}
                                    </span>
                                </div>

                                <div class="flex items-center gap-1">
                                    <!-- Quick Status Toggle Dropdown / Modal Trigger -->
                                    <div class="relative inline-block text-left" x-data="{ open: false }">
                                        <button type="button" @click="open = !open" class="w-7 h-7 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center justify-center text-gray-500 hover:text-gray-900 transition-colors cursor-pointer" title="{{ app()->getLocale() === 'ar' ? 'خيارات سريعة' : 'Quick Actions' }}">
                                            <i class="fa-solid fa-ellipsis-vertical text-xs"></i>
                                        </button>
                                        <div x-show="open" @click.away="open = false" x-cloak class="origin-top-right absolute right-0 rtl:right-auto rtl:left-0 mt-1 w-48 rounded-xl shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 z-20 py-1 divide-y divide-gray-100 dark:divide-gray-700 text-xs">
                                            <div class="py-1">
                                                <button type="button" onclick="updateScheduleStatus({{ $visit->id }}, 'completed')" class="w-full text-start px-3 py-1.5 text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 flex items-center gap-2 cursor-pointer">
                                                    <i class="fa-solid fa-circle-check"></i>
                                                    <span>{{ app()->getLocale() === 'ar' ? 'اعتماد كمكتملة' : 'Mark Completed' }}</span>
                                                </button>
                                                <button type="button" onclick="updateScheduleStatus({{ $visit->id }}, 'in_progress')" class="w-full text-start px-3 py-1.5 text-cyan-600 hover:bg-cyan-50 dark:hover:bg-cyan-950/40 flex items-center gap-2 cursor-pointer">
                                                    <i class="fa-solid fa-person-walking"></i>
                                                    <span>{{ app()->getLocale() === 'ar' ? 'جارية بالميدان' : 'Mark In Progress' }}</span>
                                                </button>
                                                <button type="button" onclick="openRescheduleModal({{ $visit->id }}, '{{ $contact?->name }}', '{{ $visit->scheduled_at?->format('Y-m-d') }}', '{{ $visit->scheduled_at?->format('H:i') }}')" class="w-full text-start px-3 py-1.5 text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 flex items-center gap-2 cursor-pointer">
                                                    <i class="fa-solid fa-calendar-days"></i>
                                                    <span>{{ app()->getLocale() === 'ar' ? 'إعادة جدولة التاريخ/الوقت' : 'Reschedule Date/Time' }}</span>
                                                </button>
                                            </div>
                                            <div class="py-1">
                                                <button type="button" onclick="updateScheduleStatus({{ $visit->id }}, 'cancelled')" class="w-full text-start px-3 py-1.5 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 flex items-center gap-2 cursor-pointer">
                                                    <i class="fa-solid fa-ban"></i>
                                                    <span>{{ app()->getLocale() === 'ar' ? 'إلغاء الموعد' : 'Cancel Visit' }}</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Doctor Information -->
                            <div class="mb-3">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <a href="{{ route('admin.mr.contacts.show', $contact?->id ?? 0) }}" class="font-black text-sm text-gray-900 dark:text-white hover:text-cyan-600 transition-colors flex items-center gap-1.5">
                                            <i class="fa-solid fa-user-doctor text-cyan-500"></i>
                                            <span>{{ preg_match('/^(dr\.|د\.|د\/|dr )/i', trim($contact?->name ?? '')) ? $contact?->name : 'Dr. ' . ($contact?->name ?? 'Unknown Doctor') }}</span>
                                        </a>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 flex items-center gap-1">
                                            <i class="fa-solid fa-hospital text-[10px]"></i>
                                            <span>{{ $contact?->hospital_clinic_name ?? 'Clinic' }}</span>
                                            @if($contact?->city)
                                                <span>• {{ $contact->city->name }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="px-2 py-0.5 rounded-md text-[11px] font-black {{ $classBadge === 'A+' || $classBadge === 'A' ? 'bg-amber-100 text-amber-900 dark:bg-amber-950 dark:text-amber-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}" title="Doctor Classification">
                                        Class {{ $classBadge }}
                                    </span>
                                </div>

                                <div class="mt-2 flex items-center gap-2 flex-wrap">
                                    @if($contact?->specialty)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-50 text-rose-700 dark:bg-rose-950/50 dark:text-rose-300 border border-rose-100 dark:border-rose-900/40">
                                            <i class="fa-solid fa-heart-pulse text-[9px]"></i>
                                            <span>{{ $contact->specialty->name }}</span>
                                        </span>
                                    @endif

                                    @if($contact?->phone)
                                        <a href="tel:{{ $contact->phone }}" class="text-[10px] font-semibold text-gray-600 hover:text-cyan-600 flex items-center gap-1" title="{{ $contact->phone }}">
                                            <i class="fa-solid fa-phone text-emerald-500"></i>
                                            <span>{{ $contact->phone }}</span>
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <!-- Rep Information Bar -->
                            <div class="p-2.5 rounded-lg bg-gray-50 dark:bg-gray-900/60 border border-gray-100 dark:border-gray-800 flex items-center justify-between gap-2 mb-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-cyan-600 text-white font-black text-xs flex items-center justify-center">
                                        {{ substr($rep?->name ?? 'MR', 0, 1) }}
                                    </div>
                                    <div class="truncate">
                                        <span class="block text-xs font-bold text-gray-900 dark:text-white truncate">{{ $rep?->name ?? 'Unassigned Rep' }}</span>
                                        <span class="text-[10px] text-gray-500 dark:text-gray-400 block truncate">{{ $rep?->territory_label ?? 'Field Rep' }}</span>
                                    </div>
                                </div>
                                <button type="button" onclick="openRepDrawer({{ $rep?->id ?? 0 }})" class="text-xs font-bold text-cyan-600 dark:text-cyan-400 hover:underline flex items-center gap-1 flex-shrink-0 cursor-pointer">
                                    <i class="fa-solid fa-id-card"></i>
                                    <span>360°</span>
                                </button>
                            </div>

                            <!-- Notes or Products -->
                            @if($visit->notes)
                                <div class="text-[11px] text-gray-600 dark:text-gray-400 italic line-clamp-1 mb-3">
                                    "{{ $visit->notes }}"
                                </div>
                            @endif

                            <!-- Bottom Quick Action Toolbar -->
                            <div class="pt-2 border-t border-gray-100 dark:border-gray-700/60 flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2">
                                    @if($contact?->latitude && $contact?->longitude)
                                        <a href="https://maps.google.com/?q={{ $contact->latitude }},{{ $contact->longitude }}" target="_blank" class="text-gray-500 hover:text-emerald-600 transition-colors flex items-center gap-1 text-[11px]" title="{{ app()->getLocale() === 'ar' ? 'عرض على خرائط جوجل' : 'Open in Google Maps' }}">
                                            <i class="fa-solid fa-location-dot text-emerald-500"></i>
                                            <span>{{ app()->getLocale() === 'ar' ? 'الموقع' : 'GPS' }}</span>
                                        </a>
                                    @endif

                                    @if($contact?->phone)
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contact->phone) }}" target="_blank" class="text-gray-500 hover:text-emerald-500 transition-colors text-[11px] flex items-center gap-1" title="WhatsApp Doctor">
                                            <i class="fa-brands fa-whatsapp text-emerald-500"></i>
                                            <span>WhatsApp</span>
                                        </a>
                                    @endif
                                </div>

                                <div class="flex items-center gap-1.5">
                                    <button type="button" onclick="openRescheduleModal({{ $visit->id }}, '{{ $contact?->name }}', '{{ $visit->scheduled_at?->format('Y-m-d') }}', '{{ $visit->scheduled_at?->format('H:i') }}')" class="btn btn-ghost py-1 px-2 text-xs text-indigo-600 hover:bg-indigo-50 font-bold cursor-pointer" title="Reschedule">
                                        <i class="fa-solid fa-clock-rotate-left"></i>
                                    </button>

                                    @if($visit->status !== 'completed')
                                        <button type="button" onclick="updateScheduleStatus({{ $visit->id }}, 'completed')" class="btn btn-primary py-1 px-2.5 text-xs bg-emerald-600 hover:bg-emerald-700 text-white font-bold flex items-center gap-1 cursor-pointer">
                                            <i class="fa-solid fa-check"></i>
                                            <span>{{ app()->getLocale() === 'ar' ? 'إنجاز' : 'Done' }}</span>
                                        </button>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-emerald-600 dark:text-emerald-400 font-bold text-xs">
                                            <i class="fa-solid fa-circle-check"></i>
                                            <span>{{ app()->getLocale() === 'ar' ? 'منجز' : 'Verified' }}</span>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    <!-- VIEW 3: HOURLY CHRONOLOGICAL TIMELINE VIEW -->
    @if($viewMode === 'timeline')
        <div class="card p-6 bg-white dark:bg-gray-800 shadow-sm rounded-2xl mb-6">
            <h3 class="text-base font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                <i class="fa-solid fa-timeline text-cyan-600"></i>
                <span>{{ app()->getLocale() === 'ar' ? 'المخطط الزمني للساعات' : 'Hourly Chronological Schedule' }} ({{ $carbonDate->format('Y-m-d') }})</span>
            </h3>

            @php
                $hours = range(8, 20); // 08:00 AM to 08:00 PM
            @endphp
            <div class="relative border-l-2 rtl:border-r-2 rtl:border-l-0 border-cyan-500/40 ml-4 rtl:ml-0 rtl:mr-4 space-y-6">
                @foreach($hours as $hour)
                    @php
                        $slotVisits = $scheduledVisits->filter(function($v) use ($hour) {
                            return $v->scheduled_at && (int)$v->scheduled_at->format('H') === $hour;
                        });
                        $formattedHour = Carbon\Carbon::createFromTime($hour, 0)->format('h:i A');
                    @endphp
                    <div class="relative pl-6 rtl:pl-0 rtl:pr-6">
                        <!-- Hour Bullet -->
                        <div class="absolute -left-[9px] rtl:-left-auto rtl:-right-[9px] top-1 w-4 h-4 rounded-full bg-white dark:bg-gray-800 border-4 border-cyan-500"></div>
                        <div class="text-xs font-black text-gray-400 mb-2">{{ $formattedHour }}</div>

                        @if($slotVisits->isEmpty())
                            <div class="text-[11px] text-gray-400 italic">{{ app()->getLocale() === 'ar' ? 'لا توجد مواعيد مجدولة في هذه الساعة' : 'No appointments scheduled' }}</div>
                        @else
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach($slotVisits as $visit)
                                    <div class="p-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/70 dark:bg-gray-900/40 flex items-start justify-between gap-2">
                                        <div>
                                            <div class="font-bold text-xs text-gray-900 dark:text-white">
                                                {{ preg_match('/^(dr\.|د\.|د\/|dr )/i', trim($visit->contact?->name ?? '')) ? $visit->contact?->name : 'Dr. ' . ($visit->contact?->name ?? '') }} ({{ $visit->contact?->specialty?->name ?? 'Specialist' }})
                                            </div>
                                            <div class="text-[11px] text-cyan-600 dark:text-cyan-400 font-semibold mt-0.5">
                                                Rep: {{ $visit->representative?->name }}
                                            </div>
                                            <div class="text-[10px] text-gray-500 mt-1 flex items-center gap-1">
                                                <i class="fa-regular fa-clock"></i>
                                                <span>{{ $visit->scheduled_at?->format('h:i A') }}</span>
                                                <span>•</span>
                                                <span class="capitalize font-semibold text-{{ $visit->status === 'completed' ? 'emerald' : 'amber' }}-600">{{ $visit->status }}</span>
                                            </div>
                                        </div>
                                        <button type="button" onclick="openRescheduleModal({{ $visit->id }}, '{{ $visit->contact?->name }}', '{{ $visit->scheduled_at?->format('Y-m-d') }}', '{{ $visit->scheduled_at?->format('H:i') }}')" class="text-xs text-indigo-600 hover:text-indigo-800 cursor-pointer" title="Reschedule">
                                            <i class="fa-solid fa-clock-rotate-left"></i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- VIEW 4: 7-DAY WEEK MATRIX VIEW -->
    @if($viewMode === 'week')
        <div class="card p-6 bg-white dark:bg-gray-800 shadow-sm rounded-2xl mb-6">
            <h3 class="text-base font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                <i class="fa-solid fa-calendar-week text-cyan-600"></i>
                <span>{{ app()->getLocale() === 'ar' ? 'مصفوفة الأسبوع (7 أيام)' : '7-Day Weekly Schedule Matrix' }}</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3">
                @foreach($weekDates as $wd)
                    @php
                        $dayData = $weekMatrix[$wd['date']] ?? ['visits' => collect(), 'total' => 0, 'completed' => 0, 'planned' => 0];
                    @endphp
                    <div class="p-3 rounded-xl border {{ $wd['is_selected'] ? 'border-cyan-500 bg-cyan-50/20 dark:bg-cyan-950/20 shadow-xs' : 'border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/30' }}">
                        <div class="flex items-center justify-between mb-2 pb-2 border-b border-gray-100 dark:border-gray-700">
                            <div>
                                <span class="block text-xs font-bold text-gray-500">{{ $wd['day_name'] }}</span>
                                <span class="text-lg font-black {{ $wd['is_today'] ? 'text-cyan-600' : 'text-gray-900 dark:text-white' }}">{{ $wd['day_num'] }}</span>
                            </div>
                            <button type="button" onclick="setDateAndSubmit('{{ $wd['date'] }}', 'day')" class="w-6 h-6 rounded-md bg-gray-100 dark:bg-gray-700 hover:bg-cyan-600 hover:text-white flex items-center justify-center text-xs text-gray-600 transition-colors cursor-pointer" title="{{ app()->getLocale() === 'ar' ? 'عرض يوم' : 'View Day' }}">
                                <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                            </button>
                        </div>

                        <div class="flex items-center justify-between text-[11px] mb-3">
                            <span class="font-bold text-gray-700 dark:text-gray-300">{{ $dayData['total'] }} {{ app()->getLocale() === 'ar' ? 'زيارة' : 'visits' }}</span>
                            <span class="text-emerald-600 font-bold">{{ $dayData['completed'] }} ✓</span>
                        </div>

                        <!-- Mini list of visits -->
                        <div class="space-y-1.5 max-h-60 overflow-y-auto pr-1">
                            @forelse($dayData['visits']->take(6) as $v)
                                <div class="p-1.5 rounded-lg bg-white dark:bg-gray-800 border border-gray-200/70 dark:border-gray-700 text-[10px] truncate shadow-2xs">
                                    <div class="font-bold text-gray-800 dark:text-gray-200 truncate">{{ preg_match('/^(dr\.|د\.|د\/|dr )/i', trim($v->contact?->name ?? '')) ? $v->contact?->name : 'Dr. ' . ($v->contact?->name ?? '') }}</div>
                                    <div class="text-gray-500 flex items-center justify-between mt-0.5">
                                        <span class="truncate">{{ $v->representative?->name }}</span>
                                        <span class="font-semibold text-{{ $v->status === 'completed' ? 'emerald' : 'amber' }}-500">{{ $v->scheduled_at?->format('H:i') }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="text-[10px] text-gray-400 italic py-2 text-center">{{ app()->getLocale() === 'ar' ? 'فارغ' : 'Empty' }}</div>
                            @endforelse

                            @if($dayData['total'] > 6)
                                <div class="text-[10px] text-center font-bold text-cyan-600">
                                    +{{ $dayData['total'] - 6 }} {{ app()->getLocale() === 'ar' ? 'المزيد' : 'more' }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- VIEW 5: MEDICAL REPRESENTATIVES 360° MANAGEMENT HUB -->
    @if($viewMode === 'reps')
        <div class="space-y-4 mb-6">
            <div class="flex items-center justify-between flex-wrap gap-2 px-1">
                <h2 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-users-gear text-indigo-600"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'دليل وبطاقات مناديب الدعاية الطبية 360°' : 'Medical Representatives 360° Intelligence Directory' }}</span>
                </h2>
                <span class="text-xs text-gray-500 dark:text-gray-400">{{ app()->getLocale() === 'ar' ? 'اضغط على أي مندوب لعرض ملفه الشامل، جدول أطبائه، وتحركاته الميدانية' : 'Click on any rep to view full dossier, assigned doctors, and field route' }}</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($repsData as $rep)
                    <div class="card p-5 border border-gray-200 dark:border-gray-700/80 hover:border-indigo-400 transition-all shadow-xs bg-white dark:bg-gray-800 rounded-2xl relative">
                        <!-- Top Rep Header -->
                        <div class="flex items-start justify-between gap-3 mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-cyan-600 to-indigo-600 text-white font-black text-lg flex items-center justify-center shadow-xs">
                                    {{ substr($rep['name'], 0, 1) }}
                                </div>
                                <div>
                                    <h3 class="font-black text-sm text-gray-900 dark:text-white">{{ $rep['name'] }}</h3>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1 mt-0.5">
                                        <i class="fa-solid fa-map-pin text-rose-500 text-[10px]"></i>
                                        <span>{{ $rep['territory'] }}</span>
                                    </div>
                                </div>
                            </div>

                            <button type="button" onclick="openRepDrawer({{ $rep['id'] }})" class="btn btn-secondary text-xs py-1 px-2.5 font-bold shadow-2xs flex items-center gap-1.5 text-indigo-600 hover:bg-indigo-50 border-indigo-200 cursor-pointer">
                                <span>{{ app()->getLocale() === 'ar' ? 'ملف 360°' : '360° Dossier' }}</span>
                                <i class="fa-solid fa-chevron-left rtl:rotate-0 ltr:rotate-180 text-[10px]"></i>
                            </button>
                        </div>

                        <!-- Key Performance Stats Box -->
                        <div class="grid grid-cols-2 gap-2 p-3 rounded-xl bg-gray-50 dark:bg-gray-900/60 border border-gray-100 dark:border-gray-800 mb-4 text-xs">
                            <div>
                                <span class="text-gray-500 block text-[10px] uppercase font-bold">{{ app()->getLocale() === 'ar' ? 'الأطباء المعينين' : 'Assigned Doctors' }}</span>
                                <span class="font-black text-gray-900 dark:text-white text-base">{{ $rep['assigned_doctors_count'] }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500 block text-[10px] uppercase font-bold">{{ app()->getLocale() === 'ar' ? 'إنجاز دورة الزيارات' : 'Cycle Coverage' }}</span>
                                <div class="flex items-center gap-1.5">
                                    <span class="font-black text-indigo-600 dark:text-indigo-400 text-base">{{ $rep['cycle_pct'] }}%</span>
                                    <span class="text-[10px] text-gray-400">({{ $rep['cycle_visits_done'] }}/{{ $rep['cycle_target_visits'] }})</span>
                                </div>
                            </div>
                        </div>

                        <!-- Today's Schedule Progress -->
                        <div class="mb-4">
                            <div class="flex items-center justify-between text-xs font-bold mb-1.5">
                                <span class="text-gray-700 dark:text-gray-300 flex items-center gap-1.5">
                                    <i class="fa-regular fa-calendar-check text-cyan-500"></i>
                                    <span>{{ app()->getLocale() === 'ar' ? 'جدول اليوم' : "Today's Agenda" }}</span>
                                </span>
                                <span class="text-cyan-600 dark:text-cyan-400 font-extrabold">{{ $rep['today_completed'] }} / {{ $rep['today_total'] }} ({{ $rep['today_pct'] }}%)</span>
                            </div>
                            <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                                <div class="bg-cyan-500 h-2 rounded-full transition-all duration-500" style="width: {{ $rep['today_pct'] }}%"></div>
                            </div>
                        </div>

                        <!-- Last GPS Check-in & Contact Info -->
                        <div class="pt-3 border-t border-gray-100 dark:border-gray-700 text-xs flex items-center justify-between">
                            <div class="text-[11px] text-gray-500 flex items-center gap-1 truncate max-w-[170px]" title="Last Check-in">
                                <i class="fa-solid fa-location-crosshairs text-emerald-500"></i>
                                <span class="truncate">{{ $rep['latest_checkin_at'] }}</span>
                            </div>

                            <div class="flex items-center gap-2">
                                @if($rep['phone'])
                                    <a href="tel:{{ $rep['phone'] }}" class="w-7 h-7 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-cyan-600 hover:text-white flex items-center justify-center text-gray-600 transition-colors" title="Call Rep">
                                        <i class="fa-solid fa-phone text-xs"></i>
                                    </a>
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $rep['phone']) }}" target="_blank" class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-950/50 hover:bg-emerald-600 hover:text-white flex items-center justify-center text-emerald-600 transition-colors" title="WhatsApp Rep">
                                        <i class="fa-brands fa-whatsapp text-xs"></i>
                                    </a>
                                @endif
                                <button type="button" onclick="openScheduleModal({{ $rep['id'] }})" class="btn btn-primary py-1 px-2.5 text-xs bg-cyan-600 hover:bg-cyan-700 text-white font-bold flex items-center gap-1 cursor-pointer" title="Schedule for this Rep">
                                    <i class="fa-solid fa-plus text-[10px]"></i>
                                    <span>{{ app()->getLocale() === 'ar' ? 'جدولة' : 'Schedule' }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- ==================== OPERATIONAL INTELLIGENCE & FIELD ACTIVITY WIDGETS ==================== -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- 1. Doctor Classification & Coverage Breakdown -->
        <div class="card p-5 bg-white dark:bg-gray-800 shadow-sm rounded-2xl">
            <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-ranking-star text-amber-500"></i>
                    <span>{{ app()->getLocale() === 'ar' ? 'توزيع الأطباء حسب التصنيف' : 'Doctor Classification Distribution' }}</span>
                </h3>
                <a href="{{ route('admin.mr.classifications.index') }}" class="text-xs text-cyan-600 dark:text-cyan-400 font-bold hover:underline">
                    {{ app()->getLocale() === 'ar' ? 'إدارة' : 'Manage' }}
                </a>
            </div>

            <div class="space-y-3">
                @foreach($classDistribution as $cls)
                    <div class="p-3 rounded-xl border border-gray-100 dark:border-gray-700 bg-gray-50/60 dark:bg-gray-900/40 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg flex items-center justify-center font-black text-xs {{ $cls->code === 'A+' ? 'bg-amber-100 text-amber-900 border border-amber-300' : ($cls->code === 'A' ? 'bg-sky-100 text-sky-900' : 'bg-gray-100 text-gray-800') }}">
                                {{ $cls->code }}
                            </span>
                            <div>
                                <span class="font-bold text-xs text-gray-900 dark:text-white block">{{ $cls->label ?: 'Class ' . $cls->code }}</span>
                                <span class="text-[10px] text-gray-500">{{ $cls->required_visits }} {{ app()->getLocale() === 'ar' ? 'زيارات مطلوبة' : 'required visits' }} • {{ $cls->points }} {{ app()->getLocale() === 'ar' ? 'نقاط' : 'pts' }}</span>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="text-sm font-black text-gray-900 dark:text-white">{{ $cls->contacts_count }}</span>
                            <span class="text-[10px] text-gray-400 block">{{ app()->getLocale() === 'ar' ? 'طبيب' : 'doctors' }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- 2. Recent Live GPS Field Check-ins Feed -->
        <div class="lg:col-span-2 card p-5 bg-white dark:bg-gray-800 shadow-sm rounded-2xl">
            <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                        <i class="fa-solid fa-satellite-dish text-emerald-500"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'آخر تسجيلات الوصول المباشرة في الميدان (Live GPS Stream)' : 'Real-Time Field Check-In Feed (GPS Audit)' }}</span>
                    </h3>
                </div>
                <a href="{{ route('admin.mr.visits.index') }}" class="text-xs text-cyan-600 dark:text-cyan-400 font-bold hover:underline">
                    {{ app()->getLocale() === 'ar' ? 'سجل الزيارات الكامل ↗' : 'View Full Visits Log ↗' }}
                </a>
            </div>

            @if($recentCheckins->isEmpty())
                <div class="text-center py-10 text-gray-400 text-xs italic">
                    {{ app()->getLocale() === 'ar' ? 'لا توجد تسجيلات وصول ميدانية مسجلة حتى الآن' : 'No field check-ins recorded yet.' }}
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($recentCheckins as $chk)
                        <div class="p-3 rounded-xl border border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/30 flex items-start justify-between gap-2 text-xs">
                            <div>
                                <div class="font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                    <i class="fa-solid fa-user-doctor text-cyan-500 text-[11px]"></i>
                                    <span>{{ preg_match('/^(dr\.|د\.|د\/|dr )/i', trim($chk->contact?->name ?? '')) ? $chk->contact?->name : 'Dr. ' . ($chk->contact?->name ?? 'Doctor') }}</span>
                                </div>
                                <div class="text-[11px] text-gray-500 mt-0.5 flex items-center gap-1">
                                    <span>Rep:</span>
                                    <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $chk->representative?->name }}</span>
                                    <span>•</span>
                                    <span>{{ $chk->contact?->city?->name }}</span>
                                </div>
                                <div class="text-[10px] text-gray-400 mt-1 flex items-center gap-1">
                                    <i class="fa-regular fa-clock"></i>
                                    <span>{{ $chk->checkin_at ? $chk->checkin_at->diffForHumans() : '—' }}</span>
                                </div>
                            </div>

                            <div class="text-end flex-shrink-0">
                                @if($chk->gps_verified)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                                        <i class="fa-solid fa-circle-check text-[9px]"></i>
                                        <span>GPS Verified</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300">
                                        <i class="fa-solid fa-triangle-exclamation text-[9px]"></i>
                                        <span>Manual/Flag</span>
                                    </span>
                                @endif
                                @if($chk->distance_from_contact_m)
                                    <span class="text-[10px] text-gray-400 block mt-1">{{ round($chk->distance_from_contact_m) }}m away</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- ==================== MODALS, SLIDE-OVERS & CONTEXT MENUS ==================== -->

    <!-- FLOATING TOAST NOTIFICATION -->
    <div id="mrToast" class="fixed bottom-5 right-5 rtl:right-auto rtl:left-5 z-[100005] hidden transition-all duration-300 transform translate-y-2 opacity-0">
        <div class="px-4 py-3 rounded-2xl shadow-2xl flex items-center gap-3 text-xs font-bold text-white bg-gray-900/95 dark:bg-gray-800/95 border border-cyan-500/40 backdrop-blur-md">
            <i id="mrToastIcon" class="fa-solid fa-circle-check text-emerald-400 text-sm"></i>
            <span id="mrToastText">Notification</span>
        </div>
    </div>

    <!-- FLOATING RIGHT-CLICK CONTEXT MENU -->
    <div id="calendarContextMenu" class="fixed hidden z-[100002] min-w-[240px] max-w-[300px] bg-white/95 dark:bg-gray-800/95 backdrop-blur-md rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 py-1.5 text-xs select-none transition-opacity duration-150 ring-1 ring-black/5 divide-y divide-gray-100 dark:divide-gray-700/80">
        <!-- Content dynamically populated by openEventContextMenu or openDateContextMenu -->
    </div>

    <!-- CALENDAR EVENT QUICK DETAILS MODAL -->
    <div id="calendarEventModal" class="fixed inset-0 overflow-y-auto hidden" style="z-index: 99999;" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity cursor-pointer" onclick="closeCalendarEventModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl text-left rtl:text-right overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-gray-200 dark:border-gray-700">
                <div class="p-5 bg-gradient-to-r from-slate-900 via-indigo-950 to-cyan-950 text-white flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-lg bg-cyan-600 text-white flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-calendar-check"></i>
                        </span>
                        <div>
                            <h3 id="calModalTitle" class="text-sm font-black text-white">Visit Details</h3>
                            <span id="calModalTime" class="text-[11px] text-cyan-300">Time</span>
                        </div>
                    </div>
                    <button type="button" onclick="closeCalendarEventModal()" class="text-white/80 hover:text-white p-1 cursor-pointer">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                <div class="p-5 space-y-3.5 text-xs">
                    <!-- Doctor Card -->
                    <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-900/60 border border-gray-100 dark:border-gray-700">
                        <div class="flex items-center justify-between mb-1">
                            <span id="calModalDoctor" class="font-black text-sm text-gray-900 dark:text-white">Doctor Name</span>
                            <span id="calModalClass" class="px-2 py-0.5 rounded text-[10px] font-black bg-amber-100 text-amber-900">Class</span>
                        </div>
                        <div id="calModalClinic" class="text-gray-500 text-[11px]">Clinic</div>
                        <div id="calModalSpecialty" class="text-cyan-600 font-semibold text-[11px] mt-0.5">Specialty</div>
                    </div>

                    <!-- Rep Card -->
                    <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-900/60 border border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div id="calModalRepAvatar" class="w-7 h-7 rounded-full bg-indigo-600 text-white font-bold flex items-center justify-center text-xs">R</div>
                            <div>
                                <span id="calModalRepName" class="font-bold text-gray-900 dark:text-white block">Rep Name</span>
                                <span id="calModalRepTerritory" class="text-gray-400 text-[10px] block">Territory</span>
                            </div>
                        </div>
                        <button type="button" id="calModalRepBtn" class="text-indigo-600 dark:text-indigo-400 font-bold text-xs hover:underline cursor-pointer">
                            360° Dossier
                        </button>
                    </div>

                    <!-- Notes -->
                    <div id="calModalNotesWrap" class="text-[11px] text-gray-600 dark:text-gray-300 italic p-2 rounded-lg bg-gray-50 dark:bg-gray-900/40 border border-gray-100 dark:border-gray-800">
                        "<span id="calModalNotes">Notes</span>"
                    </div>

                    <!-- Action Buttons -->
                    <div class="grid grid-cols-2 gap-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                        <button type="button" id="calModalDoneBtn" class="btn btn-primary text-xs py-2 font-bold bg-emerald-600 hover:bg-emerald-700 text-white flex items-center justify-center gap-1.5 cursor-pointer">
                            <i class="fa-solid fa-check"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'اعتماد كمكتمل' : 'Mark Completed' }}</span>
                        </button>

                        <button type="button" id="calModalRescheduleBtn" class="btn btn-secondary text-xs py-2 font-bold text-indigo-600 hover:bg-indigo-50 border-indigo-200 flex items-center justify-center gap-1.5 cursor-pointer">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                            <span>{{ app()->getLocale() === 'ar' ? 'إعادة جدولة' : 'Reschedule' }}</span>
                        </button>
                    </div>

                    <div class="flex items-center justify-between pt-2 text-[11px]">
                        <div class="flex items-center gap-3">
                            <a href="#" id="calModalCallBtn" class="text-gray-600 hover:text-emerald-600 font-semibold flex items-center gap-1">
                                <i class="fa-solid fa-phone text-emerald-500"></i>
                                <span>Call</span>
                            </a>
                            <a href="#" id="calModalWhatsappBtn" target="_blank" class="text-gray-600 hover:text-emerald-600 font-semibold flex items-center gap-1">
                                <i class="fa-brands fa-whatsapp text-emerald-500"></i>
                                <span>WhatsApp</span>
                            </a>
                            <a href="#" id="calModalMapBtn" target="_blank" class="text-gray-600 hover:text-cyan-600 font-semibold flex items-center gap-1">
                                <i class="fa-solid fa-location-dot text-cyan-500"></i>
                                <span>Maps</span>
                            </a>
                        </div>
                        <button type="button" id="calModalCancelBtn" class="text-rose-500 hover:text-rose-700 font-bold cursor-pointer">
                            {{ app()->getLocale() === 'ar' ? 'إلغاء الموعد' : 'Cancel Visit' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SLIDE-OVER DRAWER: MEDICAL REP 360° COMPREHENSIVE DOSSIER -->
    <div id="repDrawer" class="fixed inset-0 overflow-hidden hidden" style="z-index: 99999;" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm transition-opacity cursor-pointer" onclick="closeRepDrawer()"></div>

        <div class="pointer-events-none fixed inset-y-0 right-0 rtl:right-auto rtl:left-0 flex max-w-full pl-6 sm:pl-10 rtl:pl-0 rtl:pr-6 sm:rtl:pr-10" style="z-index: 100000;">
            <div class="pointer-events-auto w-screen max-w-2xl bg-white dark:bg-gray-800 shadow-2xl flex flex-col h-full overflow-hidden border-l rtl:border-l-0 rtl:border-r border-gray-200 dark:border-gray-700">
                <!-- Drawer Header -->
                <div class="p-5 sm:p-6 bg-gradient-to-r from-slate-900 via-indigo-950 to-cyan-950 text-white flex items-center justify-between flex-shrink-0 shadow-md">
                    <div class="flex items-center gap-3">
                        <div id="drawerAvatar" class="w-12 h-12 rounded-xl bg-cyan-600 text-white font-black text-xl flex items-center justify-center shadow-md border-2 border-white/20">
                            R
                        </div>
                        <div>
                            <h3 id="drawerRepName" class="text-base font-black text-white">Medical Representative</h3>
                            <div id="drawerRepTerritory" class="text-xs text-cyan-300 mt-0.5">Territory</div>
                        </div>
                    </div>
                    <button type="button" onclick="closeRepDrawer()" class="text-white bg-white/10 hover:bg-white/20 border border-white/20 px-3.5 py-2 rounded-lg flex items-center gap-1.5 text-xs font-bold transition-colors cursor-pointer shadow-sm">
                        <i class="fa-solid fa-xmark text-sm"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'إغلاق' : 'Close' }}</span>
                    </button>
                </div>

                <!-- Drawer Body / Tabs -->
                <div class="flex-1 overflow-y-auto p-6 space-y-6" id="drawerBody">
                    <div id="drawerLoading" class="text-center py-16">
                        <i class="fa-solid fa-spinner fa-spin text-3xl text-cyan-600"></i>
                        <p class="text-xs text-gray-500 mt-2">Loading 360° Rep Dossier...</p>
                    </div>

                    <div id="drawerContent" class="hidden space-y-6">
                        <!-- Stats Grid -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-900/70 border border-gray-200 dark:border-gray-700 text-center">
                                <span class="text-[10px] uppercase font-bold text-gray-500 block">Assigned Doctors</span>
                                <span id="drawerTotalDoctors" class="text-xl font-black text-gray-900 dark:text-white">0</span>
                            </div>
                            <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-900/70 border border-gray-200 dark:border-gray-700 text-center">
                                <span class="text-[10px] uppercase font-bold text-gray-500 block">Coverage Rate</span>
                                <span id="drawerCoveragePct" class="text-xl font-black text-indigo-600">0%</span>
                            </div>
                            <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-900/70 border border-gray-200 dark:border-gray-700 text-center">
                                <span class="text-[10px] uppercase font-bold text-gray-500 block">Visits Compliance</span>
                                <span id="drawerCompliancePct" class="text-xl font-black text-emerald-600">0%</span>
                            </div>
                            <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-900/70 border border-gray-200 dark:border-gray-700 text-center">
                                <span class="text-[10px] uppercase font-bold text-gray-500 block">Points Achieved</span>
                                <span id="drawerPointsPct" class="text-xl font-black text-amber-600">0%</span>
                            </div>
                        </div>

                        <!-- Today's Route & Agenda -->
                        <div>
                            <h4 class="font-bold text-sm text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                                <i class="fa-solid fa-route text-cyan-600"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'جدول ومسار زيارات اليوم للمندوب' : "Today's Agenda & Route" }}</span>
                            </h4>
                            <div id="drawerTodayAgendaList" class="space-y-2">
                                <!-- Populated dynamically -->
                            </div>
                        </div>

                        <!-- Assigned Doctors Directory -->
                        <div>
                            <h4 class="font-bold text-sm text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                                <i class="fa-solid fa-stethoscope text-indigo-600"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'الأطباء المعينين في الدورة' : 'Assigned Doctors in Cycle' }}</span>
                            </h4>
                            <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-xl">
                                <table class="w-full text-xs text-left rtl:text-right">
                                    <thead class="bg-gray-50 dark:bg-gray-900/80 text-gray-500 uppercase font-bold text-[10px]">
                                        <tr>
                                            <th class="px-3 py-2">Doctor</th>
                                            <th class="px-3 py-2">Specialty</th>
                                            <th class="px-3 py-2">Class</th>
                                            <th class="px-3 py-2">Visits</th>
                                        </tr>
                                    </thead>
                                    <tbody id="drawerAssignedDoctorsTable" class="divide-y divide-gray-100 dark:divide-gray-800">
                                        <!-- Populated dynamically -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Drawer Footer -->
                <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/60 flex items-center justify-between">
                    <button type="button" onclick="closeRepDrawer()" class="btn btn-secondary text-xs font-bold py-2 px-4 cursor-pointer">
                        {{ app()->getLocale() === 'ar' ? 'إغلاق' : 'Close' }}
                    </button>
                    <button type="button" id="drawerScheduleBtn" class="btn btn-primary text-xs font-bold bg-cyan-600 text-white py-2 px-4 cursor-pointer">
                        {{ app()->getLocale() === 'ar' ? '+ إضافة موعد لهذا المندوب' : '+ Schedule Visit' }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL 1: QUICK SCHEDULE VISIT MODAL -->
    <div id="scheduleModal" class="fixed inset-0 overflow-y-auto hidden" style="z-index: 99999;" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity cursor-pointer" onclick="closeScheduleModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl text-left rtl:text-right overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-200 dark:border-gray-700">
                <form action="{{ route('admin.mr.schedules.quick-create') }}" method="POST" id="quickScheduleForm">
                    @csrf
                    <div class="p-6 bg-gradient-to-r from-cyan-600 to-blue-700 text-white flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-calendar-plus text-xl"></i>
                            <h3 class="text-base font-black">{{ app()->getLocale() === 'ar' ? 'جدولة زيارة سريعة لمندوب' : 'Quick Schedule Rep Visit' }}</h3>
                        </div>
                        <button type="button" onclick="closeScheduleModal()" class="text-white/80 hover:text-white cursor-pointer">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>

                    <div class="p-6 space-y-4">
                        <!-- Rep Selection -->
                        @if($isRep)
                            <input type="hidden" name="mr_id" id="scheduleModalRepSelect" value="{{ $currentUser->id }}">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                                    {{ app()->getLocale() === 'ar' ? 'المندوب الطبي' : 'Medical Representative' }}
                                </label>
                                <div class="p-2.5 rounded-lg bg-cyan-50/70 dark:bg-cyan-950/40 border border-cyan-200 dark:border-cyan-800 text-xs font-bold text-gray-800 dark:text-gray-200 flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-md bg-cyan-600 text-white flex items-center justify-center font-bold text-[10px]">{{ substr($currentUser->name, 0, 1) }}</span>
                                    <span>{{ $currentUser->name }} ({{ app()->getLocale() === 'ar' ? 'أنت' : 'You' }})</span>
                                </div>
                            </div>
                        @else
                            <div>
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                                    {{ app()->getLocale() === 'ar' ? 'المندوب الطبي *' : 'Medical Representative *' }}
                                </label>
                                <select name="mr_id" id="scheduleModalRepSelect" required class="form-select w-full text-xs sm:text-sm py-2 rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 font-semibold">
                                    <option value="">{{ app()->getLocale() === 'ar' ? '-- اختر المندوب --' : '-- Select Representative --' }}</option>
                                    @foreach($medicalReps as $rep)
                                        <option value="{{ $rep->id }}" {{ $selectedMrId == $rep->id ? 'selected' : '' }}>
                                            {{ $rep->name }} ({{ $rep->city?->name ?? 'Territory' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <!-- Doctor / Clinic Selection -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                                {{ app()->getLocale() === 'ar' ? 'الطبيب / العيادة المستهدفة *' : 'Target Doctor / Clinic *' }}
                            </label>
                            <select name="contact_id" id="scheduleModalDoctorSelect" required class="form-select w-full text-xs sm:text-sm py-2 rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900">
                                <option value="">{{ app()->getLocale() === 'ar' ? '-- اختر الطبيب --' : '-- Select Doctor --' }}</option>
                                @foreach($availableDoctors as $doc)
                                    <option value="{{ $doc->id }}">
                                        {{ preg_match('/^(dr\.|د\.|د\/|dr )/i', trim($doc->name)) ? $doc->name : 'Dr. ' . $doc->name }} ({{ $doc->specialty?->name ?? 'Spec' }} - Class {{ $doc->classification?->code ?? 'C' }}) - {{ $doc->hospital_clinic_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Date & Time Row -->
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                                    {{ app()->getLocale() === 'ar' ? 'تاريخ الموعد *' : 'Visit Date *' }}
                                </label>
                                <input type="date" name="scheduled_date" id="scheduleModalDateInput" value="{{ $selectedDate }}" required class="form-input w-full text-xs sm:text-sm py-2 rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                                    {{ app()->getLocale() === 'ar' ? 'وقت الزيارة *' : 'Visit Time *' }}
                                </label>
                                <input type="time" name="scheduled_time" id="scheduleModalTimeInput" value="10:00" required class="form-input w-full text-xs sm:text-sm py-2 rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900">
                            </div>
                        </div>

                        <!-- Visit Cycle -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                                {{ app()->getLocale() === 'ar' ? 'دورة الزيارات' : 'Visit Cycle' }}
                            </label>
                            <select name="cycle_id" class="form-select w-full text-xs sm:text-sm py-2 rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900">
                                @foreach($cycles as $c)
                                    <option value="{{ $c->id }}" {{ $c->id == $activeCycle?->id ? 'selected' : '' }}>
                                        {{ $c->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Notes / Agenda Focus -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                                {{ app()->getLocale() === 'ar' ? 'ملاحظات وتوجيهات الزيارة' : 'Visit Instructions / Notes' }}
                            </label>
                            <textarea name="notes" id="scheduleModalNotesInput" rows="2" placeholder="{{ app()->getLocale() === 'ar' ? 'مثال: التركيز على المنتج الجديد، تسليم عينات مجانية...' : 'Focus on new product sample, discuss formulary...' }}" class="form-input w-full text-xs sm:text-sm rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900"></textarea>
                        </div>
                    </div>

                    <div class="p-4 bg-gray-50 dark:bg-gray-900/60 border-t border-gray-200 dark:border-gray-700 flex items-center justify-end gap-2">
                        <button type="button" onclick="closeScheduleModal()" class="btn btn-secondary text-xs font-bold py-2 px-4 cursor-pointer">
                            {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                        </button>
                        <button type="submit" class="btn btn-primary text-xs font-bold bg-cyan-600 hover:bg-cyan-700 text-white py-2 px-5 shadow-sm cursor-pointer">
                            <i class="fa-solid fa-check mr-1"></i> {{ app()->getLocale() === 'ar' ? 'حفظ وتأكيد الموعد' : 'Save & Dispatch Schedule' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 2: QUICK RESCHEDULE MODAL -->
    <div id="rescheduleModal" class="fixed inset-0 overflow-y-auto hidden" style="z-index: 99999;" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity cursor-pointer" onclick="closeRescheduleModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl text-left rtl:text-right overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-gray-200 dark:border-gray-700">
                <form id="rescheduleForm" onsubmit="submitReschedule(event)">
                    @csrf
                    <input type="hidden" id="rescheduleVisitId" value="">

                    <div class="p-5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-clock-rotate-left text-lg"></i>
                            <h3 class="text-sm font-black">{{ app()->getLocale() === 'ar' ? 'إعادة جدولة موعد الزيارة' : 'Reschedule Visit Appointment' }}</h3>
                        </div>
                        <button type="button" onclick="closeRescheduleModal()" class="text-white/80 hover:text-white cursor-pointer">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>

                    <div class="p-6 space-y-4">
                        <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-900/70 border border-gray-200 dark:border-gray-700">
                            <span class="text-[10px] text-gray-500 uppercase font-bold block">{{ app()->getLocale() === 'ar' ? 'الطبيب المستهدف' : 'Target Doctor' }}</span>
                            <span id="rescheduleDoctorName" class="font-bold text-xs text-gray-900 dark:text-white">Doctor Name</span>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                                    {{ app()->getLocale() === 'ar' ? 'التاريخ الجديد *' : 'New Date *' }}
                                </label>
                                <input type="date" id="rescheduleDate" required class="form-input w-full text-xs sm:text-sm py-2 rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                                    {{ app()->getLocale() === 'ar' ? 'الوقت الجديد *' : 'New Time *' }}
                                </label>
                                <input type="time" id="rescheduleTime" required class="form-input w-full text-xs sm:text-sm py-2 rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                                {{ app()->getLocale() === 'ar' ? 'سبب إعادة الجدولة / ملاحظات' : 'Reschedule Reason / Notes' }}
                            </label>
                            <input type="text" id="rescheduleNotes" placeholder="{{ app()->getLocale() === 'ar' ? 'مثال: تغيير جدول العيادة بناء على طلب الطبيب' : 'Doctor requested evening slot' }}" class="form-input w-full text-xs sm:text-sm rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900">
                        </div>
                    </div>

                    <div class="p-4 bg-gray-50 dark:bg-gray-900/60 border-t border-gray-200 dark:border-gray-700 flex items-center justify-end gap-2">
                        <button type="button" onclick="closeRescheduleModal()" class="btn btn-secondary text-xs font-bold py-2 px-4 cursor-pointer">
                            {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                        </button>
                        <button type="submit" class="btn btn-primary text-xs font-bold bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-5 shadow-sm cursor-pointer">
                            <i class="fa-solid fa-save mr-1"></i> {{ app()->getLocale() === 'ar' ? 'تحديث الموعد فوراً' : 'Update Appointment' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 3: BULK SCHEDULE DISPATCHER MODAL -->
    <div id="bulkScheduleModal" class="fixed inset-0 overflow-y-auto hidden" style="z-index: 99999;" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity cursor-pointer" onclick="closeBulkScheduleModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl text-left rtl:text-right overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-gray-200 dark:border-gray-700">
                <form action="{{ route('admin.mr.schedules.bulk-create') }}" method="POST">
                    @csrf
                    <div class="p-6 bg-gradient-to-r from-purple-700 via-indigo-700 to-cyan-700 text-white flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-bolt text-xl text-yellow-300"></i>
                            <div>
                                <h3 class="text-base font-black">{{ app()->getLocale() === 'ar' ? 'الجدولة الجماعية الذكية للمناديب' : 'Bulk Schedule Dispatcher' }}</h3>
                                <p class="text-xs text-purple-200">{{ app()->getLocale() === 'ar' ? 'جدولة مواعيد متعددة دفعة واحدة لمندوب في تاريخ محدد بفواصل زمنية تلقائية' : 'Batch schedule multiple doctors with automatic time-spacing' }}</p>
                            </div>
                        </div>
                        <button type="button" onclick="closeBulkScheduleModal()" class="text-white/80 hover:text-white cursor-pointer">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>

                    <div class="p-6 space-y-4">
                        <!-- Rep Selection -->
                        @if($isRep)
                            <input type="hidden" name="mr_id" id="bulkModalRepSelect" value="{{ $currentUser->id }}">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                                    {{ app()->getLocale() === 'ar' ? 'المندوب الطبي' : 'Target Representative' }}
                                </label>
                                <div class="p-2.5 rounded-lg bg-purple-50/70 dark:bg-purple-950/40 border border-purple-200 dark:border-purple-800 text-xs font-bold text-gray-800 dark:text-gray-200 flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-md bg-purple-600 text-white flex items-center justify-center font-bold text-[10px]">{{ substr($currentUser->name, 0, 1) }}</span>
                                    <span>{{ $currentUser->name }} ({{ app()->getLocale() === 'ar' ? 'أنت' : 'You' }})</span>
                                </div>
                            </div>
                        @else
                            <div>
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                                    {{ app()->getLocale() === 'ar' ? 'اختر المندوب الطبي المستهدف *' : 'Target Representative *' }}
                                </label>
                                <select name="mr_id" id="bulkModalRepSelect" required class="form-select w-full text-xs sm:text-sm py-2 rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 font-semibold">
                                    <option value="">{{ app()->getLocale() === 'ar' ? '-- اختر المندوب --' : '-- Select Representative --' }}</option>
                                    @foreach($medicalReps as $rep)
                                        <option value="{{ $rep->id }}" {{ $selectedMrId == $rep->id ? 'selected' : '' }}>
                                            {{ $rep->name }} ({{ $rep->city?->name ?? 'Territory' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <!-- Date & Spacing Parameters -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                                    {{ app()->getLocale() === 'ar' ? 'تاريخ اليوم *' : 'Target Date *' }}
                                </label>
                                <input type="date" name="scheduled_date" id="bulkModalDateInput" value="{{ $selectedDate }}" required class="form-input w-full text-xs sm:text-sm py-2 rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                                    {{ app()->getLocale() === 'ar' ? 'وقت البدء *' : 'Start Time *' }}
                                </label>
                                <input type="time" name="start_time" value="09:00" required class="form-input w-full text-xs sm:text-sm py-2 rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                                    {{ app()->getLocale() === 'ar' ? 'الفارق بين الزيارات (دقيقة)' : 'Interval (Minutes)' }}
                                </label>
                                <select name="interval_minutes" class="form-select w-full text-xs sm:text-sm py-2 rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900">
                                    <option value="30">30 دقيقة (30 min)</option>
                                    <option value="45" selected>45 دقيقة (45 min)</option>
                                    <option value="60">60 دقيقة (1 hour)</option>
                                    <option value="90">90 دقيقة (1.5 hours)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Multi Doctor Selector -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">
                                    {{ app()->getLocale() === 'ar' ? 'حدد الأطباء المطلوب جدولتها في هذا اليوم *' : 'Select Target Doctors for Today *' }}
                                </label>
                                <button type="button" onclick="toggleSelectAllBulkDoctors()" class="text-xs font-bold text-cyan-600 dark:text-cyan-400 hover:underline cursor-pointer">
                                    {{ app()->getLocale() === 'ar' ? 'تحديد الكل / إلغاء' : 'Select All / None' }}
                                </button>
                            </div>
                            <div class="max-h-56 overflow-y-auto border border-gray-200 dark:border-gray-700 rounded-xl p-3 space-y-2 bg-gray-50/50 dark:bg-gray-900/40">
                                @foreach($availableDoctors as $doc)
                                    <label class="flex items-center gap-2.5 p-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 hover:border-cyan-400 cursor-pointer text-xs">
                                        <input type="checkbox" name="contact_ids[]" value="{{ $doc->id }}" class="bulk-doc-checkbox rounded text-cyan-600 focus:ring-cyan-500">
                                        <div class="flex-1">
                                             <span class="font-bold text-gray-900 dark:text-white">{{ preg_match('/^(dr\.|د\.|د\/|dr )/i', trim($doc->name)) ? $doc->name : 'Dr. ' . $doc->name }}</span>
                                            <span class="text-gray-500 dark:text-gray-400 text-[11px] block">{{ $doc->hospital_clinic_name }} • Class {{ $doc->classification?->code ?? 'C' }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="p-4 bg-gray-50 dark:bg-gray-900/60 border-t border-gray-200 dark:border-gray-700 flex items-center justify-end gap-2">
                        <button type="button" onclick="closeBulkScheduleModal()" class="btn btn-secondary text-xs font-bold py-2 px-4 cursor-pointer">
                            {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                        </button>
                        <button type="submit" class="btn btn-primary text-xs font-bold bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white py-2 px-5 shadow-sm border-0 cursor-pointer">
                            <i class="fa-solid fa-bolt mr-1 text-yellow-300"></i> {{ app()->getLocale() === 'ar' ? 'توليد المواعيد آلياً' : 'Generate Batch Schedule' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 4: DIRECT VISIT ENTRY LOGGER -->
    <div id="recordDirectModal" class="fixed inset-0 overflow-y-auto hidden" style="z-index: 99999;" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity cursor-pointer" onclick="closeRecordDirectModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-2xl text-left rtl:text-right overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-200 dark:border-gray-700">
                <form action="{{ route('admin.mr.visits.quick-record') }}" method="POST">
                    @csrf
                    <div class="p-5 bg-gradient-to-r from-emerald-600 to-teal-700 text-white flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-circle-check text-xl"></i>
                            <h3 class="text-base font-black">{{ app()->getLocale() === 'ar' ? 'تسجيل واعتماد زيارة ميدانية منفذة' : 'Log Direct Executed Visit' }}</h3>
                        </div>
                        <button type="button" onclick="closeRecordDirectModal()" class="text-white/80 hover:text-white cursor-pointer">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>

                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-2 gap-3">
                            @if($isRep)
                                <input type="hidden" name="mr_id" id="directRecordRepSelect" value="{{ $currentUser->id }}">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                                        {{ app()->getLocale() === 'ar' ? 'المندوب الطبي' : 'Representative' }}
                                    </label>
                                    <div class="p-2 rounded-lg bg-emerald-50/70 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs font-bold text-gray-800 dark:text-gray-200 flex items-center gap-2">
                                        <span class="w-5 h-5 rounded-md bg-emerald-600 text-white flex items-center justify-center font-bold text-[10px]">{{ substr($currentUser->name, 0, 1) }}</span>
                                        <span>{{ $currentUser->name }}</span>
                                    </div>
                                </div>
                            @else
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                                        {{ app()->getLocale() === 'ar' ? 'المندوب الطبي *' : 'Representative *' }}
                                    </label>
                                    <select name="mr_id" id="directRecordRepSelect" required class="form-select w-full text-xs py-2 rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900">
                                        <option value="">{{ app()->getLocale() === 'ar' ? '-- اختر --' : '-- Select --' }}</option>
                                        @foreach($medicalReps as $rep)
                                            <option value="{{ $rep->id }}" {{ $selectedMrId == $rep->id ? 'selected' : '' }}>
                                                {{ $rep->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            <div>
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                                    {{ app()->getLocale() === 'ar' ? 'تاريخ ووقت الزيارة *' : 'Visit Timestamp *' }}
                                </label>
                                <input type="datetime-local" name="visited_at" value="{{ now()->format('Y-m-d\TH:i') }}" required class="form-input w-full text-xs py-2 rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                                {{ app()->getLocale() === 'ar' ? 'الطبيب المزار *' : 'Visited Doctor *' }}
                            </label>
                            <select name="contact_id" required class="form-select w-full text-xs py-2 rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900">
                                <option value="">{{ app()->getLocale() === 'ar' ? '-- اختر الطبيب --' : '-- Select Doctor --' }}</option>
                                @foreach($availableDoctors as $doc)
                                    <option value="{{ $doc->id }}">
                                        {{ preg_match('/^(dr\.|د\.|د\/|dr )/i', trim($doc->name)) ? $doc->name : 'Dr. ' . $doc->name }} ({{ $doc->hospital_clinic_name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                                {{ app()->getLocale() === 'ar' ? 'نتيجة الزيارة *' : 'Visit Outcome *' }}
                            </label>
                            <select name="outcome" required class="form-select w-full text-xs py-2 rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900">
                                <option value="successful">ناجحة ومفيدة (Successful)</option>
                                <option value="doctor_interested">الطبيب مهتم ومتحمس (Doctor Interested)</option>
                                <option value="order_placed">تم طلب كمية / طلبية (Order Placed)</option>
                                <option value="neutral">عادية / محايدة (Neutral)</option>
                                <option value="doctor_busy">الطبيب كان مشغولاً (Doctor Busy)</option>
                            </select>
                        </div>

                        <!-- Sample Products Promoted -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                                {{ app()->getLocale() === 'ar' ? 'الأصناف المروجة / العينات المسلمة *' : 'Promoted Products / Samples *' }}
                            </label>
                            <select name="product_ids[]" multiple required class="form-select w-full text-xs py-1.5 rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 h-20">
                                @foreach($products as $prod)
                                    <option value="{{ $prod->id }}">{{ $prod->name_en }} ({{ $prod->name_ar }})</option>
                                @endforeach
                            </select>
                            <span class="text-[10px] text-gray-400 block mt-1">{{ app()->getLocale() === 'ar' ? 'اضغط Ctrl لاختيار أكثر من صنف' : 'Hold Ctrl to select multiple products' }}</span>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                                {{ app()->getLocale() === 'ar' ? 'تقرير وملاحظات المقابلة *' : 'Feedback Notes *' }}
                            </label>
                            <textarea name="notes" rows="2" required placeholder="{{ app()->getLocale() === 'ar' ? 'ملخص ما تم في الزيارة وتجاوب الطبيب...' : 'Summary of doctor discussion...' }}" class="form-input w-full text-xs rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900"></textarea>
                        </div>
                    </div>

                    <div class="p-4 bg-gray-50 dark:bg-gray-900/60 border-t border-gray-200 dark:border-gray-700 flex items-center justify-end gap-2">
                        <button type="button" onclick="closeRecordDirectModal()" class="btn btn-secondary text-xs font-bold py-2 px-4 cursor-pointer">
                            {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                        </button>
                        <button type="submit" class="btn btn-primary text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white py-2 px-5 shadow-sm cursor-pointer">
                            <i class="fa-solid fa-check mr-1"></i> {{ app()->getLocale() === 'ar' ? 'تسجيل واعتماد' : 'Record & Verify' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT LOGIC & REACTIVE ACTIONS -->
    @push('scripts')
    <!-- FullCalendar 6 Global Bundle CDN -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>

    <script>
        // Calendar & Global Events Store
        window.MR_CALENDAR_EVENTS = @json($calendarEvents);
        window.MR_CALENDAR_INSTANCE = null;

        // Navigation Form Submitters
        function setDateAndSubmit(dateVal, viewMode = null) {
            document.getElementById('selectedDateInput').value = dateVal;
            if (viewMode) {
                document.getElementById('viewModeInput').value = viewMode;
            }
            document.getElementById('filterForm').submit();
        }

        function setViewAndSubmit(viewVal) {
            document.getElementById('viewModeInput').value = viewVal;
            document.getElementById('filterForm').submit();
        }

        function selectRepFilter(repId) {
            const sel = document.getElementById('repFilterSelect');
            if (sel) {
                sel.value = repId;
                document.getElementById('filterForm').submit();
            }
        }

        // Live filter for schedule cards
        function filterScheduleCards(term) {
            const lower = term.toLowerCase().trim();
            const cards = document.querySelectorAll('.schedule-card');
            cards.forEach(card => {
                const doc = card.getAttribute('data-doctor') || '';
                const rep = card.getAttribute('data-rep') || '';
                const clinic = card.getAttribute('data-clinic') || '';
                const spec = card.getAttribute('data-specialty') || '';
                if (!lower || doc.includes(lower) || rep.includes(lower) || clinic.includes(lower) || spec.includes(lower)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });

            // Also filter FullCalendar events if calendar view active
            if (window.MR_CALENDAR_INSTANCE) {
                if (!lower) {
                    window.MR_CALENDAR_INSTANCE.removeAllEvents();
                    window.MR_CALENDAR_INSTANCE.addEventSource(window.MR_CALENDAR_EVENTS);
                } else {
                    const filtered = window.MR_CALENDAR_EVENTS.filter(ev => {
                        const props = ev.extendedProps || {};
                        const txt = ((ev.title || '') + ' ' + (props.doctor_name || '') + ' ' + (props.mr_name || '') + ' ' + (props.clinic_name || '') + ' ' + (props.specialty || '')).toLowerCase();
                        return txt.includes(lower);
                    });
                    window.MR_CALENDAR_INSTANCE.removeAllEvents();
                    window.MR_CALENDAR_INSTANCE.addEventSource(filtered);
                }
            }
        }

        // Print Schedule Agenda
        function printScheduleAgenda() {
            window.print();
        }

        // Quick Modal Open / Close Handlers
        function openScheduleModal(repId = null, dateVal = null, timeVal = null) {
            if (repId) {
                const sel = document.getElementById('scheduleModalRepSelect');
                if (sel) sel.value = repId;
            }
            if (dateVal) {
                const dInput = document.getElementById('scheduleModalDateInput');
                if (dInput) dInput.value = dateVal;
            }
            if (timeVal) {
                const tInput = document.getElementById('scheduleModalTimeInput');
                if (tInput) tInput.value = timeVal;
            }
            document.getElementById('scheduleModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            closeContextMenu();
        }
        function closeScheduleModal() {
            document.getElementById('scheduleModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        function openBulkScheduleModal(repId = null, dateVal = null) {
            if (repId) {
                const sel = document.getElementById('bulkModalRepSelect');
                if (sel) sel.value = repId;
            }
            if (dateVal) {
                const dInput = document.getElementById('bulkModalDateInput');
                if (dInput) dInput.value = dateVal;
            }
            document.getElementById('bulkScheduleModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            closeContextMenu();
        }
        function closeBulkScheduleModal() {
            document.getElementById('bulkScheduleModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        function openRecordDirectModal(repId = null, dateVal = null) {
            if (repId) {
                const sel = document.getElementById('directRecordRepSelect');
                if (sel) sel.value = repId;
            }
            document.getElementById('recordDirectModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            closeContextMenu();
        }
        function closeRecordDirectModal() {
            document.getElementById('recordDirectModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        function toggleSelectAllBulkDoctors() {
            const boxes = document.querySelectorAll('.bulk-doc-checkbox');
            if (boxes.length === 0) return;
            const anyUnchecked = Array.from(boxes).some(b => !b.checked);
            boxes.forEach(b => b.checked = anyUnchecked);
        }

        // Reschedule Modal Handlers
        function openRescheduleModal(visitId, docName, dateVal, timeVal) {
            document.getElementById('rescheduleVisitId').value = visitId;
            const cleanDoc = docName.startsWith('Dr.') || docName.startsWith('د.') ? docName : 'Dr. ' . docName;
            document.getElementById('rescheduleDoctorName').textContent = cleanDoc;
            document.getElementById('rescheduleDate').value = dateVal;
            document.getElementById('rescheduleTime').value = timeVal;
            document.getElementById('rescheduleModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            closeContextMenu();
        }
        function closeRescheduleModal() {
            document.getElementById('rescheduleModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        async function submitReschedule(e) {
            e.preventDefault();
            const visitId = document.getElementById('rescheduleVisitId').value;
            const newDate = document.getElementById('rescheduleDate').value;
            const newTime = document.getElementById('rescheduleTime').value;
            const notes = document.getElementById('rescheduleNotes').value;

            try {
                const res = await fetch(`{{ url('admin/mr/schedules') }}/${visitId}/reschedule`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        scheduled_date: newDate,
                        scheduled_time: newTime,
                        notes: notes
                    })
                });
                const data = await res.json();
                if (data.success) {
                    closeRescheduleModal();
                    window.location.reload();
                } else {
                    alert(data.message || 'Error rescheduling visit');
                }
            } catch (err) {
                console.error(err);
                alert('Error updating schedule.');
            }
        }

        // Toast Notification System
        function showToast(text, type = 'success') {
            const t = document.getElementById('mrToast');
            const msg = document.getElementById('mrToastText');
            const icon = document.getElementById('mrToastIcon');
            if (!t || !msg || !icon) return;
            msg.textContent = text;
            if (type === 'success') {
                icon.className = 'fa-solid fa-circle-check text-emerald-400 text-sm';
            } else if (type === 'error') {
                icon.className = 'fa-solid fa-triangle-exclamation text-rose-400 text-sm';
            } else {
                icon.className = 'fa-solid fa-circle-info text-cyan-400 text-sm';
            }
            t.classList.remove('hidden');
            requestAnimationFrame(() => {
                t.classList.remove('translate-y-2', 'opacity-0');
            });
            setTimeout(() => {
                t.classList.add('translate-y-2', 'opacity-0');
                setTimeout(() => t.classList.add('hidden'), 300);
            }, 3500);
        }

        // Live Filter for Side Schedules Panel
        function filterSideSchedules(query) {
            const q = query.toLowerCase().trim();
            const items = document.querySelectorAll('#sideSchedulesList > div[data-search]');
            items.forEach(el => {
                const text = el.getAttribute('data-search') || '';
                el.style.display = text.includes(q) ? '' : 'none';
            });
        }

        // Quick Update Status (AJAX with in-place UI update)
        async function updateScheduleStatus(visitId, status) {
            closeContextMenu();
            try {
                const res = await fetch(`{{ url('admin/mr/schedules') }}/${visitId}/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ status: status })
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message || 'Status updated', 'success');
                    if (window.MR_CALENDAR_INSTANCE) {
                        const ev = window.MR_CALENDAR_INSTANCE.getEventById(String(visitId));
                        if (ev) {
                            const color = status === 'completed' ? '#10B981' : (status === 'in_progress' ? '#06B6D4' : (status === 'cancelled' ? '#EF4444' : '#F59E0B'));
                            ev.setProp('backgroundColor', color);
                            ev.setProp('borderColor', color);
                            ev.setExtendedProp('status', status);
                        }
                    }
                    setTimeout(() => window.location.reload(), 700);
                } else {
                    alert(data.message || 'Failed to update status');
                }
            } catch (err) {
                console.error(err);
                alert('Failed to update status.');
            }
        }

        // Delete / Cancel Schedule Action (AJAX)
        async function deleteScheduleAction(visitId) {
            closeContextMenu();
            if (!confirm('{{ app()->getLocale() === "ar" ? "هل أنت متأكد من حذف هذا الموعد المجدول؟" : "Are you sure you want to delete this scheduled visit?" }}')) {
                return;
            }
            try {
                const res = await fetch(`{{ url('admin/mr/schedules') }}/${visitId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    showToast(data.message || 'Visit deleted successfully', 'success');
                    if (window.MR_CALENDAR_INSTANCE) {
                        const ev = window.MR_CALENDAR_INSTANCE.getEventById(String(visitId));
                        if (ev) ev.remove();
                    }
                    const sideItem = document.getElementById(`side-schedule-item-${visitId}`);
                    if (sideItem) sideItem.remove();
                    const card = document.getElementById(`schedule-card-${visitId}`);
                    if (card) card.remove();
                } else {
                    alert(data.message || 'Failed to delete appointment');
                }
            } catch(e) {
                console.error(e);
                alert('Failed to delete appointment.');
            }
        }

        // ==================== CALENDAR INITIALIZATION & RIGHT-CLICK CONTEXT MENU ====================

        document.addEventListener('DOMContentLoaded', function() {
            const calEl = document.getElementById('fullCalendarContainer');
            if (!calEl) return;

            const isRtl = {{ app()->getLocale() === 'ar' ? 'true' : 'false' }};
            const calendar = new FullCalendar.Calendar(calEl, {
                initialView: 'dayGridMonth',
                direction: isRtl ? 'rtl' : 'ltr',
                locale: '{{ app()->getLocale() }}',
                initialDate: '{{ $selectedDate }}',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
                },
                buttonText: {
                    today: '{{ app()->getLocale() === "ar" ? "اليوم" : "Today" }}',
                    month: '{{ app()->getLocale() === "ar" ? "شهر" : "Month" }}',
                    week: '{{ app()->getLocale() === "ar" ? "أسبوع" : "Week" }}',
                    day: '{{ app()->getLocale() === "ar" ? "يوم" : "Day" }}',
                    list: '{{ app()->getLocale() === "ar" ? "قائمة الأجندة" : "Agenda List" }}'
                },
                navLinks: true,
                editable: true,
                eventDurationEditable: true,
                eventResizableFromStart: false,
                dayMaxEvents: 4,
                events: window.MR_CALENDAR_EVENTS,
                
                // Drag & Drop Reschedule Handler
                eventDrop: async function(info) {
                    const event = info.event;
                    const p = event.extendedProps || {};
                    const visitId = p.visit_id;
                    const start = event.start;
                    if (!start) return;

                    const year = start.getFullYear();
                    const month = String(start.getMonth() + 1).padStart(2, '0');
                    const day = String(start.getDate()).padStart(2, '0');
                    const newDate = `${year}-${month}-${day}`;

                    const hours = String(start.getHours()).padStart(2, '0');
                    const minutes = String(start.getMinutes()).padStart(2, '0');
                    const newTime = `${hours}:${minutes}`;

                    try {
                        const res = await fetch(`{{ url('admin/mr/schedules') }}/${visitId}/reschedule`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                scheduled_date: newDate,
                                scheduled_time: newTime
                            })
                        });
                        const data = await res.json();
                        if (data.success) {
                            showToast(data.message || (isRtl ? 'تمت إعادة جدولة الموعد بنجاح' : 'Visit rescheduled successfully'), 'success');
                            event.setExtendedProp('scheduled_date', newDate);
                            event.setExtendedProp('scheduled_time', newTime);
                        } else {
                            alert(data.message || 'Reschedule failed');
                            info.revert();
                        }
                    } catch(err) {
                        console.error(err);
                        alert('Failed to reschedule');
                        info.revert();
                    }
                },

                // Resize Duration Handler
                eventResize: async function(info) {
                    const event = info.event;
                    const p = event.extendedProps || {};
                    const visitId = p.visit_id;
                    const start = event.start;
                    if (!start) return;

                    const year = start.getFullYear();
                    const month = String(start.getMonth() + 1).padStart(2, '0');
                    const day = String(start.getDate()).padStart(2, '0');
                    const newDate = `${year}-${month}-${day}`;

                    const hours = String(start.getHours()).padStart(2, '0');
                    const minutes = String(start.getMinutes()).padStart(2, '0');
                    const newTime = `${hours}:${minutes}`;

                    try {
                        const res = await fetch(`{{ url('admin/mr/schedules') }}/${visitId}/reschedule`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                scheduled_date: newDate,
                                scheduled_time: newTime
                            })
                        });
                        const data = await res.json();
                        if (data.success) {
                            showToast(data.message || (isRtl ? 'تم تحديث توقيت الموعد بنجاح' : 'Visit timing updated successfully'), 'success');
                        } else {
                            info.revert();
                        }
                    } catch(err) {
                        info.revert();
                    }
                },

                // Left click on empty date slot -> Quick Schedule
                dateClick: function(info) {
                    let datePart = info.dateStr;
                    let timePart = '10:00';
                    if (datePart.includes('T')) {
                        const parts = datePart.split('T');
                        datePart = parts[0];
                        timePart = parts[1].substring(0, 5);
                    }
                    openScheduleModal('{{ $selectedMrId ?? "" }}', datePart, timePart);
                },

                // Left click on existing visit -> Open Event Details Modal
                eventClick: function(info) {
                    info.jsEvent.preventDefault();
                    openCalendarEventModal(info.event);
                },

                // Event rendering hook to attach contextmenu (Right-Click) listener
                eventDidMount: function(info) {
                    info.el.addEventListener('contextmenu', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        openEventContextMenu(e, info.event);
                    });
                }
            });

            calendar.render();
            window.MR_CALENDAR_INSTANCE = calendar;

            // Global contextmenu listener on Calendar Container to capture right-clicks on empty day cells or time slots
            calEl.addEventListener('contextmenu', function(e) {
                const eventEl = e.target.closest('.fc-event');
                if (eventEl) {
                    return; // Handled by eventDidMount
                }

                const cell = e.target.closest('[data-date], .fc-daygrid-day, .fc-timegrid-slot, .fc-timegrid-col');
                if (cell) {
                    e.preventDefault();
                    let dateVal = cell.getAttribute('data-date');
                    let timeVal = '10:00';

                    const slot = e.target.closest('.fc-timegrid-slot[data-time]');
                    if (slot) {
                        timeVal = slot.getAttribute('data-time').substring(0, 5);
                    }

                    if (!dateVal) {
                        const parentDate = cell.closest('[data-date]');
                        if (parentDate) {
                            dateVal = parentDate.getAttribute('data-date');
                        }
                    }

                    if (!dateVal) {
                        const col = e.target.closest('.fc-timegrid-col');
                        if (col && col.hasAttribute('data-date')) {
                            dateVal = col.getAttribute('data-date');
                        }
                    }

                    if (!dateVal) {
                        dateVal = '{{ $selectedDate }}';
                    }

                    openDateContextMenu(e, dateVal, timeVal);
                }
            });
        });

        // Event Details Modal Handlers
        function openCalendarEventModal(event) {
            const p = event.extendedProps || {};
            document.getElementById('calModalTitle').textContent = event.title;
            document.getElementById('calModalTime').textContent = (p.scheduled_date || '') + ' • ' + (p.time_formatted || '');
            document.getElementById('calModalDoctor').textContent = p.doctor_name || event.title;
            document.getElementById('calModalClinic').textContent = p.clinic_name + (p.city ? ' • ' + p.city : '');
            document.getElementById('calModalSpecialty').textContent = p.specialty || '';
            document.getElementById('calModalClass').textContent = 'Class ' + (p.classification || 'C');
            document.getElementById('calModalRepAvatar').textContent = p.rep_avatar || 'R';
            document.getElementById('calModalRepName').textContent = p.mr_name || 'Rep';
            document.getElementById('calModalRepTerritory').textContent = p.rep_territory || 'Territory';
            
            if (p.notes) {
                document.getElementById('calModalNotes').textContent = p.notes;
                document.getElementById('calModalNotesWrap').style.display = 'block';
            } else {
                document.getElementById('calModalNotesWrap').style.display = 'none';
            }

            // Buttons
            document.getElementById('calModalDoneBtn').onclick = function() {
                closeCalendarEventModal();
                updateScheduleStatus(p.visit_id, 'completed');
            };

            document.getElementById('calModalRescheduleBtn').onclick = function() {
                closeCalendarEventModal();
                openRescheduleModal(p.visit_id, p.doctor_name, p.scheduled_date, p.scheduled_time);
            };

            document.getElementById('calModalCancelBtn').onclick = function() {
                closeCalendarEventModal();
                updateScheduleStatus(p.visit_id, 'cancelled');
            };

            document.getElementById('calModalRepBtn').onclick = function() {
                closeCalendarEventModal();
                openRepDrawer(p.mr_id);
            };

            // Phone & WhatsApp
            const callBtn = document.getElementById('calModalCallBtn');
            const waBtn = document.getElementById('calModalWhatsappBtn');
            const mapBtn = document.getElementById('calModalMapBtn');

            if (p.doctor_phone) {
                callBtn.href = 'tel:' + p.doctor_phone;
                callBtn.style.display = 'inline-flex';
                waBtn.href = 'https://wa.me/' + p.doctor_phone.replace(/[^0-9]/g, '');
                waBtn.style.display = 'inline-flex';
            } else {
                callBtn.style.display = 'none';
                waBtn.style.display = 'none';
            }

            if (p.doctor_lat && p.doctor_lng) {
                mapBtn.href = `https://maps.google.com/?q=${p.doctor_lat},${p.doctor_lng}`;
                mapBtn.style.display = 'inline-flex';
            } else {
                mapBtn.style.display = 'none';
            }

            document.getElementById('calendarEventModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            closeContextMenu();
        }

        function closeCalendarEventModal() {
            document.getElementById('calendarEventModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        // ==================== RIGHT-CLICK CONTEXT MENU CONTROLLERS ====================

        function openEventContextMenu(e, event) {
            const menu = document.getElementById('calendarContextMenu');
            const p = event.extendedProps || {};
            const visitId = p.visit_id;
            const docName = p.doctor_name || event.title;
            const mrId = p.mr_id;
            const status = p.status || 'planned';

            let statusBadge = `<span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-${status === 'completed' ? 'emerald' : (status === 'in_progress' ? 'cyan' : 'amber')}-100 text-${status === 'completed' ? 'emerald' : (status === 'in_progress' ? 'cyan' : 'amber')}-800">${status}</span>`;

            menu.innerHTML = `
                <div class="px-3 py-2 bg-gray-50/90 dark:bg-gray-900/70 rounded-t-xl mb-1 flex items-center justify-between gap-2 border-b border-gray-100 dark:border-gray-700/60">
                    <div class="truncate">
                        <span class="font-extrabold text-gray-900 dark:text-white block truncate text-xs">${docName}</span>
                        <span class="text-[10px] text-gray-500 block truncate">${p.clinic_name || ''} ${p.specialty ? '• ' + p.specialty : ''}</span>
                    </div>
                    ${statusBadge}
                </div>
                <div class="py-1">
                    <button type="button" onclick="updateScheduleStatus(${visitId}, 'completed')" class="w-full text-start px-3 py-1.5 text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 flex items-center gap-2 font-bold cursor-pointer">
                        <i class="fa-solid fa-circle-check text-xs"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'اعتماد كمكتملة' : 'Mark Completed' }}</span>
                    </button>
                    <button type="button" onclick="updateScheduleStatus(${visitId}, 'in_progress')" class="w-full text-start px-3 py-1.5 text-cyan-600 hover:bg-cyan-50 dark:hover:bg-cyan-950/40 flex items-center gap-2 font-bold cursor-pointer">
                        <i class="fa-solid fa-person-walking text-xs"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'جارية بالميدان' : 'Mark In Progress' }}</span>
                    </button>
                    <button type="button" onclick="openRescheduleModal(${visitId}, '${docName.replace(/'/g, "\\'")}', '${p.scheduled_date || ""}', '${p.scheduled_time || ""}')" class="w-full text-start px-3 py-1.5 text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-950/40 flex items-center gap-2 font-bold cursor-pointer">
                        <i class="fa-solid fa-clock-rotate-left text-xs"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'إعادة جدولة التاريخ/الوقت' : 'Reschedule Date/Time' }}</span>
                    </button>
                </div>
                <div class="py-1">
                    <button type="button" onclick="openRepDrawer(${mrId})" class="w-full text-start px-3 py-1.5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/60 flex items-center gap-2 cursor-pointer font-semibold">
                        <i class="fa-solid fa-id-card text-xs text-indigo-500"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'ملف المندوب 360°' : 'Rep 360° Dossier' }}</span>
                    </button>
                    ${p.contact_id ? `
                    <a href="{{ url('admin/mr/contacts') }}/${p.contact_id}" target="_blank" class="w-full text-start px-3 py-1.5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/60 flex items-center gap-2 font-semibold">
                        <i class="fa-solid fa-user-doctor text-xs text-cyan-500"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'ملف الطبيب' : 'Doctor Profile' }}</span>
                    </a>
                    ` : ''}
                    ${p.doctor_phone ? `
                    <a href="https://wa.me/${p.doctor_phone.replace(/[^0-9]/g, '')}" target="_blank" class="w-full text-start px-3 py-1.5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/60 flex items-center gap-2 font-semibold">
                        <i class="fa-brands fa-whatsapp text-xs text-emerald-500"></i>
                        <span>WhatsApp Doctor</span>
                    </a>
                    <a href="tel:${p.doctor_phone}" class="w-full text-start px-3 py-1.5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/60 flex items-center gap-2 font-semibold">
                        <i class="fa-solid fa-phone text-xs text-emerald-500"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'اتصال بالطبيب' : 'Call Doctor' }}</span>
                    </a>
                    ` : ''}
                    ${p.doctor_lat && p.doctor_lng ? `
                    <a href="https://maps.google.com/?q=${p.doctor_lat},${p.doctor_lng}" target="_blank" class="w-full text-start px-3 py-1.5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/60 flex items-center gap-2 font-semibold">
                        <i class="fa-solid fa-location-dot text-xs text-rose-500"></i>
                        <span>Google Maps</span>
                    </a>
                    ` : ''}
                </div>
                <div class="py-1">
                    <button type="button" onclick="updateScheduleStatus(${visitId}, 'cancelled')" class="w-full text-start px-3 py-1.5 text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-950/40 flex items-center gap-2 font-bold cursor-pointer">
                        <i class="fa-solid fa-ban text-xs"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'إلغاء الموعد' : 'Cancel Visit' }}</span>
                    </button>
                    <button type="button" onclick="deleteScheduleAction(${visitId})" class="w-full text-start px-3 py-1.5 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 flex items-center gap-2 font-bold cursor-pointer">
                        <i class="fa-solid fa-trash-can text-xs"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'حذف الموعد' : 'Delete Visit' }}</span>
                    </button>
                </div>
            `;

            positionContextMenu(e, menu);
        }

        function openDateContextMenu(e, dateStr, timeStr = '10:00') {
            const menu = document.getElementById('calendarContextMenu');
            const targetRepId = '{{ $selectedMrId ?? "" }}';

            menu.innerHTML = `
                <div class="px-3 py-2 bg-gray-50/90 dark:bg-gray-900/70 rounded-t-xl mb-1 border-b border-gray-100 dark:border-gray-700/60">
                    <span class="text-[10px] text-gray-400 block uppercase font-bold">{{ app()->getLocale() === 'ar' ? 'إجراءات التاريخ والوقت' : 'Date & Slot Actions' }}</span>
                    <span class="font-black text-gray-900 dark:text-white text-xs">${dateStr} • ${timeStr}</span>
                </div>
                <div class="py-1">
                    <button type="button" onclick="openScheduleModal('${targetRepId}', '${dateStr}', '${timeStr}')" class="w-full text-start px-3 py-1.5 text-cyan-600 hover:bg-cyan-50 dark:hover:bg-cyan-950/40 flex items-center gap-2 font-bold cursor-pointer">
                        <i class="fa-solid fa-calendar-plus text-xs"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'جدولة موعد في هذا التوقيت' : 'Schedule Visit on this Slot' }}</span>
                    </button>
                    <button type="button" onclick="openBulkScheduleModal('${targetRepId}', '${dateStr}')" class="w-full text-start px-3 py-1.5 text-purple-600 hover:bg-purple-50 dark:hover:bg-purple-950/40 flex items-center gap-2 font-bold cursor-pointer">
                        <i class="fa-solid fa-bolt text-xs"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'جدولة جماعية لهذا اليوم' : 'Bulk Schedule on this Date' }}</span>
                    </button>
                    <button type="button" onclick="openRecordDirectModal('${targetRepId}', '${dateStr}')" class="w-full text-start px-3 py-1.5 text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 flex items-center gap-2 font-bold cursor-pointer">
                        <i class="fa-solid fa-circle-check text-xs"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'تسجيل زيارة منفذة مباشرة' : 'Log Direct Executed Visit' }}</span>
                    </button>
                </div>
                <div class="py-1">
                    <button type="button" onclick="setDateAndSubmit('${dateStr}', 'day')" class="w-full text-start px-3 py-1.5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/60 flex items-center gap-2 cursor-pointer font-semibold">
                        <i class="fa-solid fa-list-check text-xs text-gray-400"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'فتح بطاقات هذا اليوم' : 'Open Day Agenda Cards' }}</span>
                    </button>
                    <button type="button" onclick="setDateAndSubmit('${dateStr}', 'calendar')" class="w-full text-start px-3 py-1.5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/60 flex items-center gap-2 cursor-pointer font-semibold">
                        <i class="fa-regular fa-calendar text-xs text-cyan-500"></i>
                        <span>{{ app()->getLocale() === 'ar' ? 'عرض هذا اليوم باللوحة الرئيسية' : 'Focus Dashboard on this Date' }}</span>
                    </button>
                </div>
            `;

            positionContextMenu(e, menu);
        }

        function positionContextMenu(e, menu) {
            menu.classList.remove('hidden');
            const menuWidth = 260;
            const menuHeight = 310;
            let posX = e.clientX;
            let posY = e.clientY;

            // Check viewport overflow
            if (posX + menuWidth > window.innerWidth) {
                posX = window.innerWidth - menuWidth - 10;
            }
            if (posY + menuHeight > window.innerHeight) {
                posY = window.innerHeight - menuHeight - 10;
            }

            menu.style.left = posX + 'px';
            menu.style.top = posY + 'px';
        }

        function closeContextMenu() {
            const menu = document.getElementById('calendarContextMenu');
            if (menu) menu.classList.add('hidden');
        }

        // Global click-outside listener to dismiss context menu
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#calendarContextMenu')) {
                closeContextMenu();
            }
        });

        // Rep 360 Drawer Handlers
        async function openRepDrawer(repId) {
            @if($isRep)
                repId = {{ $currentUser->id }};
            @endif
            if (!repId) return;
            const drawer = document.getElementById('repDrawer');
            const loading = document.getElementById('drawerLoading');
            const content = document.getElementById('drawerContent');
            drawer.classList.remove('hidden');
            loading.classList.remove('hidden');
            content.classList.add('hidden');
            document.body.style.overflow = 'hidden';
            closeContextMenu();

            try {
                const res = await fetch(`{{ url('admin/mr/reps') }}/${repId}/details-json?cycle_id={{ $activeCycle?->id }}`, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (data.success && data.rep) {
                    const r = data.rep;
                    document.getElementById('drawerAvatar').textContent = r.name.charAt(0);
                    document.getElementById('drawerRepName').textContent = r.name;
                    document.getElementById('drawerRepTerritory').textContent = r.territory + ' (' + (r.phone || 'No phone') + ')';
                    document.getElementById('drawerTotalDoctors').textContent = r.stats.total_assigned_doctors;
                    document.getElementById('drawerCoveragePct').textContent = r.stats.coverage_pct + '%';
                    document.getElementById('drawerCompliancePct').textContent = r.stats.compliance_pct + '%';
                    document.getElementById('drawerPointsPct').textContent = r.stats.points_pct + '%';

                    // Schedule Button in drawer
                    document.getElementById('drawerScheduleBtn').onclick = function() {
                        closeRepDrawer();
                        openScheduleModal(r.id);
                    };

                    // Populate Today's Agenda
                    const agendaContainer = document.getElementById('drawerTodayAgendaList');
                    agendaContainer.innerHTML = '';
                    if (r.today_agenda && r.today_agenda.length > 0) {
                        r.today_agenda.forEach(item => {
                            const time = item.scheduled_at ? new Date(item.scheduled_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : '--';
                            const rawDoc = item.contact ? item.contact.name : 'Doctor';
                            const docName = rawDoc.replace(/^(Dr\.\s*)+/i, 'Dr. ');
                            agendaContainer.innerHTML += `
                                <div class="p-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50/70 dark:bg-gray-900/40 flex items-center justify-between text-xs">
                                    <div>
                                        <span class="font-bold text-gray-900 dark:text-white">${docName}</span>
                                        <span class="text-gray-500 block text-[10px]">${item.contact ? item.contact.hospital_clinic_name : ''}</span>
                                    </div>
                                    <div class="text-end">
                                        <span class="font-semibold text-gray-700 dark:text-gray-300 block">${time}</span>
                                        <span class="text-[10px] font-bold uppercase text-${item.status === 'completed' ? 'emerald' : 'amber'}-600">${item.status}</span>
                                    </div>
                                </div>
                            `;
                        });
                    } else {
                        agendaContainer.innerHTML = '<div class="text-xs text-gray-400 italic">No visits scheduled for today.</div>';
                    }

                    // Populate Assigned Doctors Table
                    const docTable = document.getElementById('drawerAssignedDoctorsTable');
                    docTable.innerHTML = '';
                    if (r.assignments && r.assignments.length > 0) {
                        r.assignments.forEach(asgn => {
                            const c = asgn.contact || {};
                            const spec = c.specialty ? (c.specialty.name || '') : '—';
                            const cls = c.classification ? c.classification.code : '—';
                            const docName = (c.name || '—').replace(/^(Dr\.\s*)+/i, 'Dr. ');
                            docTable.innerHTML += `
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/40">
                                    <td class="px-3 py-2 font-bold text-gray-900 dark:text-white">${docName}</td>
                                    <td class="px-3 py-2 text-gray-500">${spec}</td>
                                    <td class="px-3 py-2 font-bold">${cls}</td>
                                    <td class="px-3 py-2 font-semibold text-cyan-600">${asgn.visits_done || 0} / ${asgn.target_visits || 1}</td>
                                </tr>
                            `;
                        });
                    } else {
                        docTable.innerHTML = '<tr><td colspan="4" class="px-3 py-4 text-center text-gray-400 italic">No doctors assigned in this cycle.</td></tr>';
                    }

                    loading.classList.add('hidden');
                    content.classList.remove('hidden');
                }
            } catch (err) {
                console.error(err);
                loading.innerHTML = '<p class="text-xs text-rose-500">Failed to load rep data.</p>';
            }
        }
        function closeRepDrawer() {
            document.getElementById('repDrawer').classList.add('hidden');
            document.body.style.overflow = '';
        }

        // Global ESC key listener to close drawer and modals
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeContextMenu();
                closeRepDrawer();
                closeCalendarEventModal();
                closeScheduleModal();
                closeRescheduleModal();
                closeBulkScheduleModal();
                closeRecordDirectModal();
            }
        });
    </script>
    @endpush
</x-layouts.admin>
