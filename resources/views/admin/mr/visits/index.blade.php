<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'إدارة وزيارات مناديب الدعاية الميدانية' : 'Medical Rep Visits & Daily Field Agenda'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'جدولة ومتابعة زيارات اليوم لكل مندوب، وتسجيل الزيارات الميدانية وتتبع الـ GPS' : 'Manage and schedule today\'s visits for any rep, audit executed visits, and track GPS compliance.'"
    :breadcrumbs="[
        (app()->getLocale() === 'ar' ? 'نظام المندوب الطبي' : 'MR CRM') => route('admin.mr.live-map'),
        (app()->getLocale() === 'ar' ? 'إدارة الزيارات' : 'Visits Management') => route('admin.mr.visits.index')
    ]"
>
    <x-slot name="actions">
        <div class="flex items-center gap-2 flex-wrap">
            <button type="button" onclick="openScheduleModal()" class="btn btn-primary text-xs sm:text-sm font-bold shadow-sm flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white">
                <i class="fa-solid fa-calendar-plus"></i>
                <span>{{ app()->getLocale() === 'ar' ? 'جدولة زيارة لمندوب' : 'Schedule Visit for Rep' }}</span>
            </button>

            <button type="button" onclick="openRecordModal()" class="btn btn-outline text-xs sm:text-sm font-bold shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-500"></i>
                <span>{{ app()->getLocale() === 'ar' ? 'تسجيل زيارة منفذة' : 'Record Direct Visit' }}</span>
            </button>

            <a href="{{ request()->fullUrlWithQuery(['export' => 'xlsx']) }}" class="btn btn-secondary text-xs sm:text-sm font-bold shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-file-excel text-emerald-600 dark:text-emerald-400"></i>
                <span>{{ app()->getLocale() === 'ar' ? 'تصدير إكسيل (.xlsx)' : 'Export Excel (.xlsx)' }}</span>
            </a>
        </div>
    </x-slot>

    <!-- Navigation Tabs (Today's Agenda vs Executed Visits Log) -->
    <div class="flex items-center gap-2 border-b border-gray-200 dark:border-gray-700 mb-6">
        <a href="{{ route('admin.mr.visits.index', array_merge(request()->except(['tab', 'page']), ['tab' => 'today'])) }}" 
           class="inline-flex items-center gap-2 px-4 py-2.5 font-bold text-sm border-b-2 transition-all {{ $activeTab === 'today' ? 'border-indigo-600 text-indigo-600 dark:border-indigo-400 dark:text-indigo-300' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">
            <i class="fa-solid fa-calendar-day"></i>
            <span>{{ app()->getLocale() === 'ar' ? 'أجندة زيارات اليوم والمجدولة' : "Today's Agenda & Planned Visits" }}</span>
            <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $activeTab === 'today' ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-950 dark:text-indigo-300' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300' }}">
                {{ $todayKpi['total'] ?? 0 }}
            </span>
        </a>

        <a href="{{ route('admin.mr.visits.index', array_merge(request()->except(['tab', 'page']), ['tab' => 'history'])) }}" 
           class="inline-flex items-center gap-2 px-4 py-2.5 font-bold text-sm border-b-2 transition-all {{ $activeTab === 'history' ? 'border-indigo-600 text-indigo-600 dark:border-indigo-400 dark:text-indigo-300' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">
            <i class="fa-solid fa-clock-rotate-left"></i>
            <span>{{ app()->getLocale() === 'ar' ? 'سجل الزيارات المنفذة والـ GPS' : 'Executed Visits & GPS Audit' }}</span>
            <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $activeTab === 'history' ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-950 dark:text-indigo-300' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300' }}">
                {{ $visits->total() }}
            </span>
        </a>
    </div>

    @if($activeTab === 'today')
        <!-- TODAY'S FIELD AGENDA TAB -->
        <!-- KPI Metric Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5 mb-6">
            <div class="card p-4 border border-indigo-100 dark:border-indigo-900/50 bg-gradient-to-br from-indigo-50/50 to-white dark:from-indigo-950/20 dark:to-gray-800 shadow-xs">
                <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider block mb-1">
                    {{ app()->getLocale() === 'ar' ? 'إجمالي زيارات التاريخ المحدد' : 'Total Scheduled on Date' }}
                </span>
                <div class="flex items-baseline justify-between">
                    <span class="text-2xl font-black text-gray-900 dark:text-white">{{ $todayKpi['total'] }}</span>
                    <i class="fa-solid fa-calendar-check text-indigo-400 text-xl"></i>
                </div>
            </div>

            <div class="card p-4 border border-emerald-100 dark:border-emerald-900/50 bg-gradient-to-br from-emerald-50/50 to-white dark:from-emerald-950/20 dark:to-gray-800 shadow-xs">
                <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider block mb-1">
                    {{ app()->getLocale() === 'ar' ? 'زيارات مكتملة' : 'Completed Visits' }}
                </span>
                <div class="flex items-baseline justify-between">
                    <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ $todayKpi['completed'] }}</span>
                    <i class="fa-solid fa-circle-check text-emerald-400 text-xl"></i>
                </div>
            </div>

            <div class="card p-4 border border-amber-100 dark:border-amber-900/50 bg-gradient-to-br from-amber-50/50 to-white dark:from-amber-950/20 dark:to-gray-800 shadow-xs">
                <span class="text-xs font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider block mb-1">
                    {{ app()->getLocale() === 'ar' ? 'قيد الانتظار / مجدولة' : 'Planned / In Progress' }}
                </span>
                <div class="flex items-baseline justify-between">
                    <span class="text-2xl font-black text-amber-600 dark:text-amber-400">{{ $todayKpi['planned'] }}</span>
                    <i class="fa-solid fa-hourglass-half text-amber-400 text-xl"></i>
                </div>
            </div>

            <div class="card p-4 border border-gray-200 dark:border-gray-700/80 bg-white dark:bg-gray-800 shadow-xs">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider block mb-1">
                    {{ app()->getLocale() === 'ar' ? 'زيارات ملغاة' : 'Cancelled Visits' }}
                </span>
                <div class="flex items-baseline justify-between">
                    <span class="text-2xl font-black text-gray-700 dark:text-gray-300">{{ $todayKpi['cancelled'] }}</span>
                    <i class="fa-solid fa-ban text-gray-400 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Filter Card for Today Agenda -->
        <div class="card p-4 mb-6">
            <form method="GET" action="{{ route('admin.mr.visits.index') }}" class="flex flex-wrap items-center justify-between gap-3">
                <input type="hidden" name="tab" value="today">

                <div class="flex flex-wrap items-center gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">
                            {{ app()->getLocale() === 'ar' ? 'تاريخ الأجندة' : 'Agenda Date' }}
                        </label>
                        <div class="flex items-center gap-1.5">
                            <input type="date" name="date" value="{{ $selectedDate }}" onchange="this.form.submit()" class="form-control text-xs py-1.5 px-2.5 rounded">
                            <a href="{{ route('admin.mr.visits.index', ['tab' => 'today', 'date' => now()->toDateString(), 'mr_id' => request('mr_id')]) }}" 
                               class="btn btn-outline text-xs py-1.5 px-2.5 {{ $selectedDate === now()->toDateString() ? 'bg-indigo-50 border-indigo-300 text-indigo-600 font-bold' : '' }}">
                                {{ app()->getLocale() === 'ar' ? 'اليوم' : 'Today' }}
                            </a>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">
                            {{ app()->getLocale() === 'ar' ? 'المندوب الطبي' : 'Medical Representative' }}
                        </label>
                        <select name="mr_id" onchange="this.form.submit()" class="form-select text-sm py-1.5 px-3">
                            <option value="">{{ app()->getLocale() === 'ar' ? 'جميع المناديب' : 'All Representatives' }}</option>
                            @foreach($medicalReps as $rep)
                                <option value="{{ $rep->id }}" {{ request('mr_id') == $rep->id ? 'selected' : '' }}>
                                    {{ $rep->name }}{{ $rep->area ? ' (📍 ' . $rep->area->name . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">
                            {{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}
                        </label>
                        <select name="status" onchange="this.form.submit()" class="form-select text-sm py-1.5 px-3">
                            <option value="">{{ app()->getLocale() === 'ar' ? 'جميع الحالات' : 'All Statuses' }}</option>
                            <option value="planned" {{ request('status') === 'planned' ? 'selected' : '' }}>
                                {{ app()->getLocale() === 'ar' ? 'مجدولة / قيد الانتظار' : 'Planned / In Progress' }}
                            </option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>
                                {{ app()->getLocale() === 'ar' ? 'مكتملة' : 'Completed' }}
                            </option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>
                                {{ app()->getLocale() === 'ar' ? 'ملغاة' : 'Cancelled' }}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="pt-4 flex items-center gap-2">
                    <a href="{{ route('admin.mr.visits.index', ['tab' => 'today']) }}" class="btn btn-outline text-xs">
                        <i class="fa-solid fa-arrows-rotate mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'إعادة ضبط' : 'Reset' }}
                    </a>
                </div>
            </form>
        </div>

        <!-- Today's Visits Table -->
        <div class="card p-0 overflow-hidden shadow-sm border border-gray-100 dark:border-gray-800 mb-8">
            <div class="p-4 bg-gray-50/70 dark:bg-gray-800/60 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between flex-wrap gap-2">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-list-check text-indigo-500"></i>
                    <h3 class="font-bold text-sm text-gray-900 dark:text-white">
                        {{ app()->getLocale() === 'ar' ? 'قائمة زيارات اليوم المجدولة للمناديب' : "Scheduled Visits Agenda for Date: " . \Carbon\Carbon::parse($selectedDate)->format('d M Y') }}
                    </h3>
                </div>
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    {{ count($todayVisits) }} {{ app()->getLocale() === 'ar' ? 'زيارة مجدولة' : 'visits on agenda' }}
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm table bz-sortable-table" data-table-sortable="true">
                    <thead class="bg-gray-50 dark:bg-gray-800/60 text-gray-600 dark:text-gray-300 font-semibold border-b border-gray-200 dark:border-gray-700 text-xs">
                        <tr>
                            <th class="px-5 py-3.5">{{ app()->getLocale() === 'ar' ? 'الوقت' : 'Scheduled Time' }}</th>
                            <th class="px-5 py-3.5">{{ app()->getLocale() === 'ar' ? 'المندوب الطبي' : 'Medical Rep' }}</th>
                            <th class="px-5 py-3.5">{{ app()->getLocale() === 'ar' ? 'الطبيب والعيادة' : 'Doctor & Facility' }}</th>
                            <th class="px-5 py-3.5 text-center">{{ app()->getLocale() === 'ar' ? 'الفئة' : 'Class' }}</th>
                            <th class="px-5 py-3.5 text-center">{{ app()->getLocale() === 'ar' ? 'حالة الزيارة' : 'Visit Status' }}</th>
                            <th class="px-5 py-3.5">{{ app()->getLocale() === 'ar' ? 'المنتجات والملاحظات' : 'Products & Notes' }}</th>
                            <th class="px-5 py-3.5 text-center">{{ app()->getLocale() === 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($todayVisits as $sv)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition-colors">
                                <td class="px-5 py-3.5 text-xs whitespace-nowrap">
                                    <div class="font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                        <i class="fa-regular fa-clock text-indigo-500"></i>
                                        {{ $sv->scheduled_at->format('h:i A') }}
                                    </div>
                                    <div class="text-[10px] text-gray-400 mt-0.5">
                                        {{ $sv->scheduled_at->format('Y-m-d') }}
                                    </div>
                                </td>

                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300 font-bold text-xs flex items-center justify-center">
                                            {{ strtoupper(substr($sv->representative?->name ?? 'M', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 dark:text-white text-xs">
                                                {{ $sv->representative?->name ?? 'Rep #' . $sv->mr_id }}
                                            </div>
                                            <div class="text-[10px] text-gray-400">
                                                {{ $sv->cycle?->name ?? 'Active Cycle' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-5 py-3.5">
                                    <div class="font-bold text-gray-900 dark:text-white text-xs">
                                        {{ $sv->contact?->name ?? 'Doctor #' . $sv->contact_id }}
                                        <span class="text-[10px] font-mono text-gray-400 font-normal">({{ $sv->contact?->code }})</span>
                                    </div>
                                    <div class="text-[11px] text-gray-500 dark:text-gray-400 flex items-center gap-1.5 mt-0.5 flex-wrap">
                                        @if($sv->contact?->specialty)
                                            <span class="text-sky-600 dark:text-sky-400 font-medium">{{ $sv->contact->specialty->name }}</span>
                                        @endif
                                        @if($sv->contact?->hospital_clinic_name)
                                            <span>•</span>
                                            <span>{{ $sv->contact->hospital_clinic_name }}</span>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-5 py-3.5 text-center">
                                    @if($sv->contact?->classification)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-black {{ $sv->contact->classification->code === 'A+' ? 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300' : 'bg-sky-100 text-sky-800 dark:bg-sky-950/60 dark:text-sky-300' }}">
                                            {{ $sv->contact->classification->code }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-xs">—</span>
                                    @endif
                                </td>

                                <td class="px-5 py-3.5 text-center whitespace-nowrap">
                                    @if($sv->status === 'completed' || $sv->visit)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-400">
                                            <i class="fa-solid fa-circle-check"></i>
                                            {{ app()->getLocale() === 'ar' ? 'تمت الزيارة' : 'Completed' }}
                                        </span>
                                    @elseif($sv->status === 'cancelled')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-400">
                                            <i class="fa-solid fa-ban"></i>
                                            {{ app()->getLocale() === 'ar' ? 'ملغاة' : 'Cancelled' }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-indigo-100 text-indigo-800 dark:bg-indigo-950/60 dark:text-indigo-400">
                                            <i class="fa-solid fa-clock"></i>
                                            {{ app()->getLocale() === 'ar' ? 'مجدولة (بانتظار المندوب)' : 'Planned / Ready in App' }}
                                        </span>
                                    @endif
                                </td>

                                <td class="px-5 py-3.5 text-xs max-w-xs">
                                    @if($sv->visit)
                                        @if($sv->visit->products && $sv->visit->products->count() > 0)
                                            <div class="flex flex-wrap gap-1 mb-1">
                                                @foreach($sv->visit->products as $p)
                                                    <span class="px-1.5 py-0.2 rounded text-[10px] bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                                        {{ $p->name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                        <div class="truncate text-gray-600 dark:text-gray-400" title="{{ $sv->visit->notes }}">
                                            {{ $sv->visit->notes }}
                                        </div>
                                    @else
                                        <span class="text-gray-400 italic text-[11px]">{{ $sv->notes ?: '—' }}</span>
                                    @endif
                                </td>

                                <td class="px-5 py-3.5 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1.5">
                                        @if($sv->status !== 'completed' && !$sv->visit)
                                            <button type="button" 
                                                onclick="openRecordModalForSchedule({{ $sv->id }}, {{ $sv->mr_id }}, {{ $sv->contact_id }}, '{{ addslashes($sv->contact?->name ?? '') }}', '{{ addslashes($sv->representative?->name ?? '') }}')" 
                                                class="btn btn-outline text-xs py-1 px-2 text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950" 
                                                title="{{ app()->getLocale() === 'ar' ? 'تسجيل إتمام الزيارة نيابة عن المندوب' : 'Log / Complete Visit for MR' }}">
                                                <i class="fa-solid fa-check mr-1 ml-1"></i>
                                                {{ app()->getLocale() === 'ar' ? 'إتمام' : 'Complete' }}
                                            </button>

                                            <form method="POST" action="{{ route('admin.mr.visits.cancel-schedule', $sv->id) }}" onsubmit="return confirm('{{ app()->getLocale() === 'ar' ? 'هل أنت متأكد من إلغاء هذه الزيارة المجدولة؟' : 'Are you sure you want to cancel this scheduled visit?' }}')" class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline text-xs py-1 px-2 text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950" title="{{ app()->getLocale() === 'ar' ? 'إلغاء الزيارة' : 'Cancel' }}">
                                                    <i class="fa-solid fa-xmark"></i>
                                                </button>
                                            </form>
                                        @else
                                            @if($sv->visit)
                                                <a href="{{ route('admin.mr.visits.show', $sv->visit->id) }}" class="btn btn-outline text-xs py-1 px-2">
                                                    <i class="fa-solid fa-eye mr-1 ml-1"></i>
                                                    {{ app()->getLocale() === 'ar' ? 'عرض' : 'View' }}
                                                </a>
                                            @else
                                                <span class="text-gray-400 text-xs">—</span>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center text-gray-400">
                                    <div class="w-12 h-12 rounded-full bg-indigo-50 dark:bg-indigo-950/50 text-indigo-500 flex items-center justify-center mx-auto mb-3 text-xl">
                                        <i class="fa-solid fa-calendar-xmark"></i>
                                    </div>
                                    <p class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">
                                        {{ app()->getLocale() === 'ar' ? 'لا توجد زيارات مجدولة في هذا التاريخ.' : 'No scheduled visits on this date.' }}
                                    </p>
                                    <p class="text-xs text-gray-500 max-w-sm mx-auto mb-4">
                                        {{ app()->getLocale() === 'ar' ? 'يمكنك جدولة زيارة فورية لأي مندوب لتظهر في أجندة اليوم بالبوابة مباشرةً.' : 'You can schedule an immediate visit for any MR so it appears in their portal agenda ready for check-in.' }}
                                    </p>
                                    <button type="button" onclick="openScheduleModal()" class="btn btn-primary text-xs py-2 px-4 shadow-sm">
                                        <i class="fa-solid fa-calendar-plus mr-1.5 ml-1.5"></i>
                                        {{ app()->getLocale() === 'ar' ? 'جدولة زيارة جديدة لليوم' : 'Schedule Visit for Today' }}
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @else
        <!-- EXECUTED VISITS AUDIT LOG TAB -->
        <!-- Filter Card -->
        <div class="card p-4 mb-6">
            <form method="GET" action="{{ route('admin.mr.visits.index') }}" class="flex flex-wrap items-center justify-between gap-3">
                <input type="hidden" name="tab" value="history">

                <div class="flex flex-wrap items-center gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">
                            {{ app()->getLocale() === 'ar' ? 'دورة الزيارة' : 'Visit Cycle' }}
                        </label>
                        <select name="cycle_id" onchange="this.form.submit()" class="form-select text-sm py-1.5 px-3">
                            <option value="">{{ app()->getLocale() === 'ar' ? 'جميع الدورات' : 'All Cycles' }}</option>
                            @foreach($cycles as $c)
                                <option value="{{ $c->id }}" {{ $selectedCycleId == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">
                            {{ app()->getLocale() === 'ar' ? 'المندوب الطبي' : 'Medical Representative' }}
                        </label>
                        <select name="mr_id" onchange="this.form.submit()" class="form-select text-sm py-1.5 px-3">
                            <option value="">{{ app()->getLocale() === 'ar' ? 'جميع المناديب' : 'All Reps' }}</option>
                            @foreach($medicalReps as $rep)
                                <option value="{{ $rep->id }}" {{ request('mr_id') == $rep->id ? 'selected' : '' }}>
                                    {{ $rep->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">
                            {{ app()->getLocale() === 'ar' ? 'التحقق الجغرافي GPS' : 'GPS Verification' }}
                        </label>
                        <select name="gps_status" onchange="this.form.submit()" class="form-select text-sm py-1.5 px-3">
                            <option value="">{{ app()->getLocale() === 'ar' ? 'الكل' : 'All' }}</option>
                            <option value="verified" {{ request('gps_status') === 'verified' ? 'selected' : '' }}>
                                ✓ {{ app()->getLocale() === 'ar' ? 'مؤكد جغرافياً فقط' : 'Verified Within Radius' }}
                            </option>
                            <option value="unverified" {{ request('gps_status') === 'unverified' ? 'selected' : '' }}>
                                ⚠️ {{ app()->getLocale() === 'ar' ? 'تجاوز النطاق / غير مؤكد' : 'Flagged (Outside Radius)' }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">
                            {{ app()->getLocale() === 'ar' ? 'النتيجة' : 'Outcome' }}
                        </label>
                        <select name="outcome" onchange="this.form.submit()" class="form-select text-sm py-1.5 px-3">
                            <option value="">{{ app()->getLocale() === 'ar' ? 'جميع النتائج' : 'All Outcomes' }}</option>
                            <option value="successful" {{ request('outcome') === 'successful' ? 'selected' : '' }}>Successful</option>
                            <option value="positive" {{ request('outcome') === 'positive' ? 'selected' : '' }}>Positive</option>
                            <option value="doctor_interested" {{ request('outcome') === 'doctor_interested' ? 'selected' : '' }}>Doctor Interested</option>
                            <option value="order_placed" {{ request('outcome') === 'order_placed' ? 'selected' : '' }}>Order Placed</option>
                            <option value="neutral" {{ request('outcome') === 'neutral' ? 'selected' : '' }}>Neutral</option>
                            <option value="doctor_busy" {{ request('outcome') === 'doctor_busy' ? 'selected' : '' }}>Doctor Busy</option>
                        </select>
                    </div>
                </div>

                <div class="pt-5 flex items-center gap-2">
                    <a href="{{ route('admin.mr.visits.index', ['tab' => 'history']) }}" class="btn btn-outline text-xs">
                        <i class="fa-solid fa-arrows-rotate mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'إعادة ضبط' : 'Reset' }}
                    </a>
                </div>
            </form>
        </div>

        <!-- Executed Visits Table -->
        <div class="card p-0 overflow-hidden shadow-sm border border-gray-100 dark:border-gray-800 mb-8">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm table bz-sortable-table" data-table-sortable="true">
                    <thead class="bg-gray-50 dark:bg-gray-800/60 text-gray-600 dark:text-gray-300 font-semibold border-b border-gray-200 dark:border-gray-700 text-xs">
                        <tr>
                            <th class="px-5 py-3.5">{{ app()->getLocale() === 'ar' ? 'المندوب' : 'Representative' }}</th>
                            <th class="px-5 py-3.5">{{ app()->getLocale() === 'ar' ? 'الطبيب / العيادة' : 'Doctor & Facility' }}</th>
                            <th class="px-5 py-3.5">{{ app()->getLocale() === 'ar' ? 'وقت تسجيل الوصول' : 'Check-In' }}</th>
                            <th class="px-5 py-3.5">{{ app()->getLocale() === 'ar' ? 'وقت الانصراف' : 'Check-Out' }}</th>
                            <th class="px-5 py-3.5 text-center">{{ app()->getLocale() === 'ar' ? 'المدة' : 'Duration' }}</th>
                            <th class="px-5 py-3.5 text-center">{{ app()->getLocale() === 'ar' ? 'التحقق GPS والمسافة' : 'GPS Verification & Distance' }}</th>
                            <th class="px-5 py-3.5 text-center">{{ app()->getLocale() === 'ar' ? 'النتيجة' : 'Outcome' }}</th>
                            <th class="px-5 py-3.5">{{ app()->getLocale() === 'ar' ? 'المنتجات المناقشة' : 'Discussed Products' }}</th>
                            <th class="px-5 py-3.5">{{ app()->getLocale() === 'ar' ? 'الملاحظات' : 'Visit Notes' }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($visits as $v)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition-colors">
                                <td class="px-5 py-4 font-bold text-gray-900 dark:text-white">
                                    {{ $v->representative?->name ?? 'Rep' }}
                                </td>
                                <td class="px-5 py-4">
                                    <a href="{{ route('admin.mr.contacts.show', $v->contact_id) }}" class="font-bold text-gray-900 dark:text-white hover:text-sky-500">
                                        {{ $v->contact?->name ?? 'Doctor' }}
                                    </a>
                                    <div class="text-xs text-gray-500 mt-0.5">
                                        {{ $v->contact?->specialty?->name }} • {{ $v->contact?->hospital_clinic_name }}
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-xs">
                                    <div>{{ $v->checkin_at?->format('d M Y, H:i') }}</div>
                                    <div class="text-[11px] text-gray-400">{{ $v->checkin_at?->diffForHumans() }}</div>
                                </td>
                                <td class="px-5 py-4 text-xs">
                                    @if($v->checkout_at)
                                        <div>{{ $v->checkout_at->format('d M Y, H:i') }}</div>
                                        <div class="text-[11px] text-gray-400">{{ $v->checkout_at->diffForHumans() }}</div>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">
                                            In Progress
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-center font-bold text-gray-700 dark:text-gray-300">
                                    {{ $v->duration_minutes ? $v->duration_minutes . ' min' : '—' }}
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold {{ $v->gps_verified ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-400' : 'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-400' }}">
                                        <i class="fa-solid {{ $v->gps_verified ? 'fa-circle-check' : 'fa-triangle-exclamation' }}"></i>
                                        {{ $v->gps_verified ? 'Verified' : 'Flagged' }}
                                    </span>
                                    @if($v->distance_from_contact_m)
                                        <div class="text-[11px] font-mono text-gray-500 mt-0.5">
                                            {{ $v->distance_from_contact_m }}m from clinic
                                        </div>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-center">
                                    @if($v->outcome)
                                        <span class="badge badge-outline text-[11px] font-bold">
                                            {{ ucfirst($v->outcome) }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-xs">
                                    @if($v->products && $v->products->count() > 0)
                                        <div class="flex flex-wrap gap-1 max-w-xs">
                                            @foreach($v->products as $prod)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60">
                                                    <i class="fa-solid fa-capsules text-[10px]"></i>
                                                    {{ $prod->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @elseif($v->product)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/60">
                                            <i class="fa-solid fa-capsules text-[10px]"></i>
                                            {{ $v->product->name }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 italic text-[11px]">{{ app()->getLocale() === 'ar' ? 'غير محدد' : 'None' }}</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-xs text-gray-600 dark:text-gray-400 max-w-xs truncate" title="{{ $v->notes }}">
                                    {{ $v->notes ?: '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-5 py-12 text-center text-gray-400">
                                    <i class="fa-solid fa-list-check text-4xl mb-2"></i>
                                    <p class="text-sm font-semibold">{{ app()->getLocale() === 'ar' ? 'لا توجد زيارات مسجلة مطابقة للبحث.' : 'No visits recorded matching your criteria.' }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($visits->hasPages())
                <div class="p-4 border-t border-gray-100 dark:border-gray-800">
                    {{ $visits->links() }}
                </div>
            @endif
        </div>
    @endif

    <!-- MODAL 1: Schedule Visit for MR Modal -->
    <div id="schedule-visit-modal" class="fixed inset-0 z-[1050] overflow-y-auto bg-black/60 p-3 sm:p-4 hidden backdrop-blur-sm" onclick="if(event.target === this) closeScheduleModal()">
        <div class="min-h-full flex items-center justify-center p-0">
            <div class="card max-w-xl w-full p-0 shadow-2xl relative flex flex-col max-h-[88vh] border border-gray-100 dark:border-gray-800 rounded-2xl overflow-hidden bg-white dark:bg-gray-850">
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 px-6 py-3.5 flex-shrink-0 bg-white dark:bg-gray-800">
                    <h3 class="font-bold text-base text-gray-900 dark:text-white flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center border border-indigo-100 dark:border-indigo-900 flex-shrink-0">
                            <i class="fa-solid fa-calendar-plus text-sm"></i>
                        </div>
                        <div>
                            <span>{{ app()->getLocale() === 'ar' ? 'جدولة زيارة لمندوب دعاية' : 'Schedule Visit for Rep' }}</span>
                            <p class="text-[11px] font-normal text-gray-500 dark:text-gray-400">
                                {{ app()->getLocale() === 'ar' ? 'تظهر الزيارة فوراً في أجندة المندوب بالبوابة ليوم الزيارة' : 'Appears immediately in MR Portal daily agenda for check-in' }}
                            </p>
                        </div>
                    </h3>
                    <button type="button" onclick="closeScheduleModal()" class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.mr.visits.schedule') }}" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    @csrf

                    <div class="flex-1 overflow-y-auto px-6 py-4 space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                                {{ app()->getLocale() === 'ar' ? 'المندوب الطبي الميداني *' : 'Medical Representative *' }}
                            </label>
                            <select name="mr_id" required class="form-select text-sm w-full">
                                <option value="">{{ app()->getLocale() === 'ar' ? 'اختر المندوب' : 'Select Representative' }}</option>
                                @foreach($medicalReps as $rep)
                                    <option value="{{ $rep->id }}" {{ request('mr_id') == $rep->id ? 'selected' : '' }}>
                                        {{ $rep->name }}{{ $rep->area ? ' (📍 ' . $rep->area->name . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                                {{ app()->getLocale() === 'ar' ? 'الطبيب المستهدف *' : 'Target Doctor / Contact *' }}
                            </label>
                            <select name="contact_id" required class="form-select text-sm w-full">
                                <option value="">{{ app()->getLocale() === 'ar' ? 'اختر الطبيب' : 'Select Doctor' }}</option>
                                @foreach($availableDoctors as $doc)
                                    <option value="{{ $doc->id }}">
                                        {{ $doc->name }} ({{ $doc->code }}) - {{ $doc->specialty?->name }} [{{ $doc->hospital_clinic_name }}]
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                                    {{ app()->getLocale() === 'ar' ? 'تاريخ الزيارة *' : 'Visit Date *' }}
                                </label>
                                <input type="date" name="scheduled_date" value="{{ now()->toDateString() }}" required class="form-control text-sm w-full">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                                    {{ app()->getLocale() === 'ar' ? 'وقت الموعد *' : 'Scheduled Time *' }}
                                </label>
                                <input type="time" name="scheduled_time" value="10:00" required class="form-control text-sm w-full">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                                {{ app()->getLocale() === 'ar' ? 'تعليمات وملاحظات الزيارة' : 'Visit Instructions / Notes' }}
                            </label>
                            <textarea name="notes" rows="2" placeholder="{{ app()->getLocale() === 'ar' ? 'مثال: مناقشة خط المنتجات الخلوية الجديد...' : 'e.g. Discuss new cellular longevity product line...' }}" class="form-control text-xs w-full"></textarea>
                        </div>
                    </div>

                    <div class="px-6 py-3 bg-gray-50/90 dark:bg-gray-900/60 border-t border-gray-100 dark:border-gray-800 flex items-center justify-end gap-2 flex-shrink-0">
                        <button type="button" onclick="closeScheduleModal()" class="btn btn-secondary text-xs px-4 py-2">
                            {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                        </button>
                        <button type="submit" class="btn btn-primary font-bold text-xs px-5 py-2 shadow-sm bg-indigo-600 hover:bg-indigo-700 text-white">
                            <i class="fa-solid fa-calendar-check mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'تأكيد الجدولة والإرسال للمندوب' : 'Confirm Schedule & Dispatch' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 2: Record Direct Completed Visit for MR Modal -->
    <div id="record-visit-modal" class="fixed inset-0 z-[1050] overflow-y-auto bg-black/60 p-3 sm:p-4 hidden backdrop-blur-sm" onclick="if(event.target === this) closeRecordModal()">
        <div class="min-h-full flex items-center justify-center p-0">
            <div class="card max-w-xl w-full p-0 shadow-2xl relative flex flex-col max-h-[88vh] border border-gray-100 dark:border-gray-800 rounded-2xl overflow-hidden bg-white dark:bg-gray-850">
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 px-6 py-3.5 flex-shrink-0 bg-white dark:bg-gray-800">
                    <h3 class="font-bold text-base text-gray-900 dark:text-white flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-100 dark:border-emerald-900 flex-shrink-0">
                            <i class="fa-solid fa-file-signature text-sm"></i>
                        </div>
                        <div>
                            <span>{{ app()->getLocale() === 'ar' ? 'تسجيل زيارة منفذة نيابة عن المندوب' : 'Record Direct Visit on Behalf of MR' }}</span>
                            <p class="text-[11px] font-normal text-gray-500 dark:text-gray-400">
                                {{ app()->getLocale() === 'ar' ? 'اعتماد زيارة تمت ميدانياً وحساب نقاط التكليف فوراً' : 'Log completed visit, products, and update performance points' }}
                            </p>
                        </div>
                    </h3>
                    <button type="button" onclick="closeRecordModal()" class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.mr.visits.record-direct') }}" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    @csrf
                    <input type="hidden" name="scheduled_visit_id" id="record-scheduled-visit-id" value="">

                    <div class="flex-1 overflow-y-auto px-6 py-4 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                                    {{ app()->getLocale() === 'ar' ? 'المندوب الطبي *' : 'Medical Representative *' }}
                                </label>
                                <select name="mr_id" id="record-mr-id" required class="form-select text-sm w-full">
                                    <option value="">{{ app()->getLocale() === 'ar' ? 'اختر المندوب' : 'Select Representative' }}</option>
                                    @foreach($medicalReps as $rep)
                                        <option value="{{ $rep->id }}">{{ $rep->name }}{{ $rep->area ? ' (📍 ' . $rep->area->name . ')' : '' }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                                    {{ app()->getLocale() === 'ar' ? 'الطبيب *' : 'Doctor *' }}
                                </label>
                                <select name="contact_id" id="record-contact-id" required class="form-select text-sm w-full">
                                    <option value="">{{ app()->getLocale() === 'ar' ? 'اختر الطبيب' : 'Select Doctor' }}</option>
                                    @foreach($availableDoctors as $doc)
                                        <option value="{{ $doc->id }}">{{ $doc->name }} ({{ $doc->code }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                                    {{ app()->getLocale() === 'ar' ? 'تاريخ ووقت الزيارة *' : 'Visited At *' }}
                                </label>
                                <input type="datetime-local" name="visited_at" value="{{ now()->format('Y-m-d\TH:i') }}" required class="form-control text-xs w-full">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                                    {{ app()->getLocale() === 'ar' ? 'نتيجة الزيارة *' : 'Visit Outcome *' }}
                                </label>
                                <select name="outcome" required class="form-select text-xs w-full">
                                    <option value="successful">{{ app()->getLocale() === 'ar' ? 'ناجحة (Successful)' : 'Successful' }}</option>
                                    <option value="positive">{{ app()->getLocale() === 'ar' ? 'إيجابية (Positive)' : 'Positive' }}</option>
                                    <option value="doctor_interested">{{ app()->getLocale() === 'ar' ? 'الطبيب مهتم بالمنتج' : 'Doctor Interested' }}</option>
                                    <option value="order_placed">{{ app()->getLocale() === 'ar' ? 'تم طلب أوردر' : 'Order Placed' }}</option>
                                    <option value="neutral">{{ app()->getLocale() === 'ar' ? 'محايدة' : 'Neutral' }}</option>
                                    <option value="doctor_busy">{{ app()->getLocale() === 'ar' ? 'الطبيب كان مشغولاً' : 'Doctor Busy' }}</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1.5">
                                {{ app()->getLocale() === 'ar' ? 'المنتجات التي تمت مناقشتها *' : 'Products Discussed *' }}
                            </label>
                            <div class="max-h-28 overflow-y-auto p-2.5 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50/40 dark:bg-gray-900/40 grid grid-cols-2 gap-2">
                                @foreach($products as $prod)
                                    <label class="flex items-center gap-2 p-1.5 rounded hover:bg-white dark:hover:bg-gray-800 cursor-pointer select-none">
                                        <input type="checkbox" name="product_ids[]" value="{{ $prod->id }}" class="h-3.5 w-3.5 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                        <span class="text-xs font-semibold text-gray-800 dark:text-gray-200 truncate">{{ $prod->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                                {{ app()->getLocale() === 'ar' ? 'ملاحظات الزيارة ورد فعل الطبيب *' : 'Meeting Notes & Feedback *' }}
                            </label>
                            <textarea name="notes" rows="3" required placeholder="{{ app()->getLocale() === 'ar' ? 'اكتب ملخص ما تم مناقشته خلال الزيارة...' : 'Detailed feedback and summary of discussion...' }}" class="form-control text-xs w-full"></textarea>
                        </div>
                    </div>

                    <div class="px-6 py-3 bg-gray-50/90 dark:bg-gray-900/60 border-t border-gray-100 dark:border-gray-800 flex items-center justify-end gap-2 flex-shrink-0">
                        <button type="button" onclick="closeRecordModal()" class="btn btn-secondary text-xs px-4 py-2">
                            {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                        </button>
                        <button type="submit" class="btn btn-primary font-bold text-xs px-5 py-2 shadow-sm bg-emerald-600 hover:bg-emerald-700 text-white">
                            <i class="fa-solid fa-check mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'حفظ واعتماد الزيارة' : 'Save & Verify Visit' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openScheduleModal() {
            const modal = document.getElementById('schedule-visit-modal');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeScheduleModal() {
            const modal = document.getElementById('schedule-visit-modal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }

        function openRecordModal() {
            const modal = document.getElementById('record-visit-modal');
            if (modal) {
                document.getElementById('record-scheduled-visit-id').value = '';
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        function openRecordModalForSchedule(scheduleId, mrId, contactId, contactName, mrName) {
            const modal = document.getElementById('record-visit-modal');
            if (modal) {
                document.getElementById('record-scheduled-visit-id').value = scheduleId;
                const mrSelect = document.getElementById('record-mr-id');
                const contactSelect = document.getElementById('record-contact-id');
                if (mrSelect && mrId) mrSelect.value = mrId;
                if (contactSelect && contactId) contactSelect.value = contactId;
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeRecordModal() {
            const modal = document.getElementById('record-visit-modal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeScheduleModal();
                closeRecordModal();
            }
        });
    </script>
</x-layouts.admin>
