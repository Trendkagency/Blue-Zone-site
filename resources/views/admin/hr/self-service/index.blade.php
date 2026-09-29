<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'بوابة الخدمة الذاتية للموظف' : 'Employee Self-Service Hub'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'تسجيل الحضور والانصراف، تقديم ومتابعة طلبات الإجازات والأرصدة' : 'Punch attendance, submit leave requests & track balances'"
>
    @php
        $isAr = app()->getLocale() === 'ar';
        $isCheckedIn = !empty($todayAttendance?->check_in);
        $isCheckedOut = !empty($todayAttendance?->check_out);
        $workedHours = $todayAttendance ? round($todayAttendance->worked_minutes / 60, 1) : 0;
        $totalRemainingDays = $leaveBalances->sum('remaining_days');
    @endphp

    <!-- Top Alert Messages -->
    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 text-emerald-800 dark:text-emerald-300 flex items-center gap-3 shadow-sm animate-fade-in">
            <span class="w-9 h-9 rounded-lg bg-emerald-500 text-white flex items-center justify-center flex-shrink-0 font-bold">
                <i class="fa-solid fa-check"></i>
            </span>
            <div class="text-sm font-semibold flex-1">
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/60 text-rose-800 dark:text-rose-300 flex items-center gap-3 shadow-sm">
            <span class="w-9 h-9 rounded-lg bg-rose-500 text-white flex items-center justify-center flex-shrink-0 font-bold">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </span>
            <div class="text-sm font-semibold flex-1">
                <ul class="m-0 pl-4 list-disc space-y-1">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Profile & Quick Stats Banner -->
    <div class="mb-8 rounded-2xl bg-gradient-to-r from-[#062B49] via-[#0A4F78] to-[#15618D] dark:from-[#031827] dark:via-[#062B49] dark:to-[#0A4F78] p-6 lg:p-8 text-white shadow-xl relative overflow-hidden border border-[#0A4F78]/50">
        <!-- Background Glow -->
        <div class="absolute -right-16 -bottom-16 w-64 h-64 bg-cyan-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-16 -top-16 w-64 h-64 bg-sky-400/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center text-2xl sm:text-3xl font-black text-cyan-300 shadow-inner flex-shrink-0">
                    {{ strtoupper(substr($employee->full_name, 0, 2)) }}
                </div>
                <div>
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h1 class="text-xl sm:text-2xl font-black tracking-tight text-white m-0">
                            {{ $employee->full_name }}
                        </h1>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-cyan-400/20 text-cyan-200 border border-cyan-400/30">
                            {{ $employee->employee_number }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-400/20 text-emerald-200 border border-emerald-400/30">
                            {{ $employee->employment_status === 'active' ? ($isAr ? 'نشط' : 'Active') : $employee->employment_status }}
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-300 mt-1 mb-0 flex items-center gap-3 flex-wrap">
                        <span><i class="fa-solid fa-briefcase text-cyan-400 mr-1 ml-1"></i> {{ $employee->position?->name ?? ($isAr ? 'موظف' : 'Staff') }}</span>
                        <span><i class="fa-solid fa-sitemap text-sky-400 mr-1 ml-1"></i> {{ $employee->department?->name ?? ($isAr ? 'العام' : 'General') }}</span>
                        <span><i class="fa-solid fa-envelope text-slate-400 mr-1 ml-1"></i> {{ $employee->email }}</span>
                    </p>
                </div>
            </div>

            <!-- Header Quick Actions -->
            <div class="flex items-center gap-3 w-full lg:w-auto">
                <button type="button" onclick="openSelfServiceModal('leave')" class="flex-1 lg:flex-none inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl font-bold text-sm bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 transition-all shadow-lg hover:shadow-amber-500/25 cursor-pointer transform hover:-translate-y-0.5">
                    <i class="fa-solid fa-plane-departure text-base"></i>
                    <span>{{ $isAr ? 'تقديم طلب إجازة' : 'Apply for Leave' }}</span>
                </button>
            </div>
        </div>

        <!-- 4 Grid KPI Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6 pt-6 border-t border-white/10">
            <div class="bg-white/5 backdrop-blur-sm rounded-xl p-3.5 border border-white/10">
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-300">
                    {{ $isAr ? 'حالة الحضور اليوم' : "Today's Status" }}
                </div>
                <div class="text-base sm:text-lg font-black mt-1 flex items-center gap-2">
                    @if($isCheckedOut)
                        <span class="w-2.5 h-2.5 rounded-full bg-slate-400"></span>
                        <span class="text-slate-200">{{ $isAr ? 'انصرف' : 'Checked Out' }}</span>
                    @elseif($isCheckedIn)
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="text-emerald-300">{{ $isAr ? 'حاضر الآن' : 'Present (Clocked In)' }}</span>
                    @else
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                        <span class="text-amber-300">{{ $isAr ? 'لم يسجل الحضور' : 'Not Clocked In' }}</span>
                    @endif
                </div>
            </div>

            <div class="bg-white/5 backdrop-blur-sm rounded-xl p-3.5 border border-white/10">
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-300">
                    {{ $isAr ? 'ساعات العمل اليوم' : 'Hours Worked Today' }}
                </div>
                <div class="text-base sm:text-lg font-black mt-1 text-cyan-300 font-mono">
                    {{ $workedHours }} <span class="text-xs font-normal text-slate-300">{{ $isAr ? 'ساعة' : 'hrs' }}</span>
                </div>
            </div>

            <div class="bg-white/5 backdrop-blur-sm rounded-xl p-3.5 border border-white/10">
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-300">
                    {{ $isAr ? 'الرصيد المتاح للإجازات' : 'Available Leave Days' }}
                </div>
                <div class="text-base sm:text-lg font-black mt-1 text-emerald-300 font-mono">
                    {{ number_format($totalRemainingDays, 1) }} <span class="text-xs font-normal text-slate-300">{{ $isAr ? 'يوم' : 'days' }}</span>
                </div>
            </div>

            <div class="bg-white/5 backdrop-blur-sm rounded-xl p-3.5 border border-white/10">
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-300">
                    {{ $isAr ? 'طلبات الإجازة المسجلة' : 'Total Leave Requests' }}
                </div>
                <div class="text-base sm:text-lg font-black mt-1 text-purple-300 font-mono">
                    {{ $leaveRequests->total() }} <span class="text-xs font-normal text-slate-300">{{ $isAr ? 'طلب' : 'requests' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main 2-Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-8">
        
        <!-- Left Column (5 Cols): Live Time Clock & Quick Punch Card -->
        <div class="lg:col-span-5 space-y-6">
            
            <!-- Time Clock Card -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 flex items-center justify-center font-bold text-lg border border-sky-100 dark:border-sky-900/50">
                            <i class="fa-solid fa-stopwatch"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-800 dark:text-white m-0">
                                {{ $isAr ? 'تسجيل الحضور والانصراف' : 'Live Attendance Time Clock' }}
                            </h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400 m-0">
                                {{ now()->translatedFormat('l, d F Y') }}
                            </p>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold {{ $isCheckedIn && !$isCheckedOut ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400' }}">
                        <span class="w-2 h-2 rounded-full {{ $isCheckedIn && !$isCheckedOut ? 'bg-emerald-500 animate-ping' : 'bg-slate-400' }}"></span>
                        {{ $isCheckedIn && !$isCheckedOut ? ($isAr ? 'في الوردية' : 'On Shift') : ($isCheckedOut ? ($isAr ? 'انتهت الوردية' : 'Shift Ended') : ($isAr ? 'غير مسجل' : 'Off Clock')) }}
                    </span>
                </div>

                <!-- Digital Clock Display -->
                <div class="my-6 p-6 rounded-2xl bg-gradient-to-b from-slate-900 to-slate-950 border border-slate-800 text-center shadow-inner relative">
                    <div class="text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-1">
                        {{ $isAr ? 'توقيت النظام الحالي' : 'Live Server Time' }}
                    </div>
                    <div id="liveDigitalClock" class="text-4xl sm:text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-sky-400 via-cyan-300 to-emerald-400 font-mono tracking-wider">
                        {{ now()->format('H:i:s') }}
                    </div>
                    <div class="text-xs text-slate-400 mt-2 font-medium">
                        {{ now()->format('A') }} &bull; {{ config('app.timezone', 'UTC') }}
                    </div>
                </div>

                <!-- Punch Actions -->
                <div class="space-y-4">
                    @if(!$isCheckedIn)
                        <form action="{{ route('admin.attendance.self-checkin') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full flex items-center justify-center gap-3 py-3.5 px-6 rounded-xl font-bold text-base bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white shadow-lg hover:shadow-emerald-500/25 transition-all transform hover:-translate-y-0.5 cursor-pointer">
                                <i class="fa-solid fa-right-to-bracket text-xl"></i>
                                <span>{{ $isAr ? 'تسجيل الحضور الآن' : 'Punch In (Check In)' }}</span>
                            </button>
                        </form>
                    @elseif($isCheckedIn && !$isCheckedOut)
                        <div class="p-3.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/50 flex items-center justify-between">
                            <div class="text-xs text-emerald-800 dark:text-emerald-300">
                                <span class="font-bold">{{ $isAr ? 'وقت الحضور:' : 'Clocked in at:' }}</span>
                                <span class="font-mono font-black mr-1 ml-1">{{ $todayAttendance->check_in }}</span>
                            </div>
                            <span class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                                {{ $isAr ? 'المصدر: الويب' : 'Source: Web' }}
                            </span>
                        </div>

                        <form action="{{ route('admin.attendance.self-checkout') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full flex items-center justify-center gap-3 py-3.5 px-6 rounded-xl font-bold text-base bg-gradient-to-r from-rose-600 to-pink-600 hover:from-rose-500 hover:to-pink-500 text-white shadow-lg hover:shadow-rose-500/25 transition-all transform hover:-translate-y-0.5 cursor-pointer">
                                <i class="fa-solid fa-right-from-bracket text-xl"></i>
                                <span>{{ $isAr ? 'تسجيل الانصراف' : 'Punch Out (Check Out)' }}</span>
                            </button>
                        </form>
                    @else
                        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/60 text-center">
                            <div class="w-12 h-12 rounded-full bg-emerald-100 dark:bg-emerald-900/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mx-auto mb-2 text-xl">
                                <i class="fa-solid fa-circle-check"></i>
                            </div>
                            <h4 class="text-sm font-bold text-slate-800 dark:text-white m-0">
                                {{ $isAr ? 'اكتمل تسجيل الحضور والانصراف اليوم' : 'Daily Punch Complete' }}
                            </h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 mb-0">
                                {{ $isAr ? 'الحضور:' : 'In:' }} <strong class="font-mono text-slate-700 dark:text-slate-300">{{ $todayAttendance->check_in }}</strong> | 
                                {{ $isAr ? 'الانصراف:' : 'Out:' }} <strong class="font-mono text-slate-700 dark:text-slate-300">{{ $todayAttendance->check_out }}</strong> | 
                                {{ $isAr ? 'المدة:' : 'Duration:' }} <strong class="font-mono text-cyan-600 dark:text-cyan-400">{{ $workedHours }} {{ $isAr ? 'ساعة' : 'hrs' }}</strong>
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Shift Telemetry -->
                @if($employee->workSchedule)
                    <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <div class="text-xs font-bold text-slate-600 dark:text-slate-400 mb-2 flex items-center gap-1.5">
                            <i class="fa-solid fa-calendar-check text-sky-500"></i>
                            <span>{{ $isAr ? 'بيانات الوردية الرسمية' : 'Assigned Shift Schedule' }}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-3 text-xs">
                            <div class="p-2.5 rounded-lg bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                                <span class="text-slate-400 block text-[10px]">{{ $isAr ? 'اسم الوردية' : 'Shift Name' }}</span>
                                <span class="font-bold text-slate-700 dark:text-slate-200">{{ $employee->workSchedule->name }}</span>
                            </div>
                            <div class="p-2.5 rounded-lg bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                                <span class="text-slate-400 block text-[10px]">{{ $isAr ? 'أوقات العمل' : 'Shift Hours' }}</span>
                                <span class="font-bold font-mono text-slate-700 dark:text-slate-200">{{ $employee->workSchedule->start_time }} &ndash; {{ $employee->workSchedule->end_time }}</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Leave Balances Card -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center font-bold text-lg border border-purple-100 dark:border-purple-900/50">
                            <i class="fa-solid fa-wallet"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-800 dark:text-white m-0">
                                {{ $isAr ? 'أرصدة الإجازات السنوية' : 'Annual Leave Balances' }}
                            </h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400 m-0">
                                {{ $isAr ? "لعام $year" : "Year $year" }}
                            </p>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-purple-950/40 px-2.5 py-1 rounded-full">
                        {{ count($leaveBalances) }} {{ $isAr ? 'أنواع' : 'types' }}
                    </span>
                </div>

                <div class="space-y-3 mt-4">
                    @forelse($leaveBalances as $bal)
                        @php
                            $lt = $bal->leaveType;
                            $rem = (float) $bal->remaining_days;
                            $alloc = (float) $bal->allocated_days;
                            $percent = $alloc > 0 ? min(100, max(0, round(($rem / $alloc) * 100))) : 0;
                        @endphp
                        <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800 hover:border-purple-200 dark:hover:border-purple-800/50 transition-colors">
                            <div class="flex items-center justify-between text-xs font-bold">
                                <span class="text-slate-800 dark:text-white">
                                    {{ $isAr ? ($lt?->name_ar ?? $lt?->name_en) : ($lt?->name_en ?? $lt?->name_ar) }}
                                </span>
                                <span class="font-mono text-purple-600 dark:text-purple-400">
                                    {{ $rem }} / {{ $alloc }} {{ $isAr ? 'يوم متاح' : 'days left' }}
                                </span>
                            </div>
                            <div class="w-full bg-slate-200 dark:bg-slate-700 h-2 rounded-full mt-2 overflow-hidden">
                                <div class="bg-gradient-to-r from-purple-500 to-indigo-500 h-full rounded-full transition-all duration-500" style="width: {{ $percent }}%"></div>
                            </div>
                            <div class="flex items-center justify-between text-[10px] text-slate-400 mt-1.5">
                                <span>{{ $isAr ? 'مستخدم:' : 'Used:' }} <strong class="text-slate-600 dark:text-slate-300 font-mono">{{ $bal->used_days }}</strong></span>
                                <span>{{ $isAr ? 'قيد الانتظار:' : 'Pending:' }} <strong class="text-amber-500 font-mono">{{ $bal->pending_days }}</strong></span>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-slate-400 text-xs">
                            <i class="fa-solid fa-folder-open text-2xl mb-1 block text-slate-300 dark:text-slate-600"></i>
                            {{ $isAr ? 'لا توجد أرصدة مخصصة مسجلة حالياً.' : 'No allocated leave balances recorded.' }}
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Right Column (7 Cols): Submit Leave Request & My Requests History -->
        <div class="lg:col-span-7 space-y-6">
            
            <!-- Submit Leave Request Form Card -->
            <div id="applyLeaveFormCard" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center font-bold text-lg border border-amber-100 dark:border-amber-900/50">
                            <i class="fa-solid fa-paper-plane"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-800 dark:text-white m-0">
                                {{ $isAr ? 'تقديم طلب إجازة جديد' : 'Submit New Leave Request' }}
                            </h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400 m-0">
                                {{ $isAr ? 'اختر نوع الإجازة والفترة المطلوبة للمراجعة والاعتماد' : 'Select leave type and dates for approval' }}
                            </p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('admin.self-service.leave') }}" method="POST" enctype="multipart/form-data" class="space-y-4 mt-5" id="selfLeaveForm">
                    @csrf
                    
                    <!-- Leave Type Select -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            {{ $isAr ? 'نوع الإجازة *' : 'Leave Type *' }}
                        </label>
                        <select name="leave_type_id" id="selfLeaveTypeSelect" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white text-sm focus:ring-2 focus:ring-sky-500 transition-colors">
                            <option value="" disabled selected>{{ $isAr ? '— اختر نوع الإجازة —' : '— Select Leave Type —' }}</option>
                            @foreach($leaveTypes as $lt)
                                @php
                                    $bal = $leaveBalances->where('leave_type_id', $lt->id)->first();
                                    $remDays = $bal ? (float)$bal->remaining_days : (float)$lt->annual_days;
                                @endphp
                                <option value="{{ $lt->id }}" data-days="{{ $remDays }}">
                                    {{ $isAr ? $lt->name_ar : $lt->name_en }} &mdash; ({{ $remDays }} {{ $isAr ? 'يوم متاح' : 'days available' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date Range Inputs -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                {{ $isAr ? 'تاريخ البدء *' : 'Start Date *' }}
                            </label>
                            <input type="date" name="start_date" id="selfLeaveStartDate" required min="{{ now()->toDateString() }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white text-sm focus:ring-2 focus:ring-sky-500 transition-colors">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                {{ $isAr ? 'تاريخ الانتهاء *' : 'End Date *' }}
                            </label>
                            <input type="date" name="end_date" id="selfLeaveEndDate" required min="{{ now()->toDateString() }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white text-sm focus:ring-2 focus:ring-sky-500 transition-colors">
                        </div>
                    </div>

                    <!-- Dynamic Calculated Days Badge -->
                    <div id="selfLeaveDaysCalculation" class="hidden p-3 rounded-xl bg-sky-50 dark:bg-sky-950/40 border border-sky-200 dark:border-sky-800/60 flex items-center justify-between text-xs">
                        <span class="text-sky-800 dark:text-sky-300 font-semibold">
                            <i class="fa-solid fa-calculator mr-1 ml-1"></i> {{ $isAr ? 'إجمالي الأيام المحتسبة:' : 'Total Calculated Days:' }}
                        </span>
                        <span id="selfLeaveDaysCount" class="font-mono font-black text-sm text-sky-700 dark:text-sky-300 bg-white dark:bg-slate-800 px-2.5 py-0.5 rounded-lg border border-sky-200 dark:border-sky-700">
                            0 {{ $isAr ? 'يوم' : 'days' }}
                        </span>
                    </div>

                    <!-- Reason Textarea -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            {{ $isAr ? 'سبب الإجازة وملاحظات الموظف *' : 'Reason / Notes *' }}
                        </label>
                        <textarea name="reason" rows="3" required placeholder="{{ $isAr ? 'يرجى كتابة تفاصيل سبب طلب الإجازة...' : 'Please specify the reason for this leave request...' }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white text-sm focus:ring-2 focus:ring-sky-500 transition-colors"></textarea>
                    </div>

                    <!-- Optional Attachment -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            {{ $isAr ? 'مرفق داعم (اختياري - تقرير طبي أو مستند)' : 'Supporting Attachment (Optional - PDF, Image)' }}
                        </label>
                        <input type="file" name="attachment" accept=".pdf,.jpg,.jpeg,.png,.webp,.doc,.docx" class="w-full text-xs text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-sky-50 file:text-sky-700 dark:file:bg-sky-950/60 dark:file:text-sky-300 hover:file:bg-sky-100 cursor-pointer">
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 py-3 px-6 rounded-xl font-bold text-sm bg-gradient-to-r from-sky-600 to-cyan-600 hover:from-sky-500 hover:to-cyan-500 text-white shadow-lg hover:shadow-sky-500/25 transition-all cursor-pointer">
                            <i class="fa-solid fa-check"></i>
                            <span>{{ $isAr ? 'إرسال طلب الإجازة للاعتماد' : 'Submit Leave Application' }}</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Leave Requests History Card -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 flex items-center justify-center font-bold text-lg border border-teal-100 dark:border-teal-900/50">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-800 dark:text-white m-0">
                                {{ $isAr ? 'سجل طلبات الإجازة الخاصة بي' : 'My Leave Requests History' }}
                            </h2>
                            <p class="text-xs text-slate-500 dark:text-slate-400 m-0">
                                {{ $isAr ? 'متابعة حالة الطلبات وقرارات الإدارة' : 'Track your applications & review statuses' }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto mt-4">
                    <table class="w-full text-xs text-start">
                        <thead>
                            <tr class="border-b border-slate-100 dark:border-slate-800 text-slate-400 font-bold uppercase tracking-wider text-[10px]">
                                <th class="pb-3 text-start">{{ $isAr ? 'نوع الإجازة' : 'Type' }}</th>
                                <th class="pb-3 text-start">{{ $isAr ? 'الفترة' : 'Dates' }}</th>
                                <th class="pb-3 text-start">{{ $isAr ? 'الأيام' : 'Days' }}</th>
                                <th class="pb-3 text-start">{{ $isAr ? 'الحالة' : 'Status' }}</th>
                                <th class="pb-3 text-start">{{ $isAr ? 'السبب / الملاحظات' : 'Reason' }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                            @forelse($leaveRequests as $lr)
                                @php
                                    $st = $lr->status;
                                    $stBadge = match($st) {
                                        'approved' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800',
                                        'rejected' => 'bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-400 border-rose-200 dark:border-rose-800',
                                        default => 'bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-400 border-amber-200 dark:border-amber-800',
                                    };
                                    $stLabel = match($st) {
                                        'approved' => ($isAr ? 'تمت الموافقة' : 'Approved'),
                                        'rejected' => ($isAr ? 'مرفوض' : 'Rejected'),
                                        default => ($isAr ? 'قيد المراجعة' : 'Pending Approval'),
                                    };
                                @endphp
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition-colors">
                                    <td class="py-3 font-bold text-slate-800 dark:text-white">
                                        {{ $isAr ? ($lr->leaveType?->name_ar ?? $lr->leaveType?->name_en) : ($lr->leaveType?->name_en ?? $lr->leaveType?->name_ar) }}
                                    </td>
                                    <td class="py-3 font-mono text-slate-600 dark:text-slate-300">
                                        {{ $lr->start_date }} &rarr; {{ $lr->end_date }}
                                    </td>
                                    <td class="py-3 font-mono font-bold text-sky-600 dark:text-sky-400">
                                        {{ (float)$lr->total_days }} {{ $isAr ? 'يوم' : 'd' }}
                                    </td>
                                    <td class="py-3">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full font-bold text-[11px] border {{ $stBadge }}">
                                            {{ $stLabel }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-slate-500 dark:text-slate-400 max-w-xs truncate">
                                        {{ $lr->reason }}
                                        @if($lr->rejection_reason)
                                            <div class="text-[10px] text-rose-500 mt-0.5 font-semibold">
                                                {{ $isAr ? 'سبب الرفض: ' : 'Rejection Reason: ' }} {{ $lr->rejection_reason }}
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-slate-400">
                                        <i class="fa-solid fa-inbox text-3xl mb-2 block text-slate-300 dark:text-slate-700"></i>
                                        {{ $isAr ? 'لا توجد طلبات إجازة سابقة.' : 'No previous leave requests found.' }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($leaveRequests->hasPages())
                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800">
                        {{ $leaveRequests->links() }}
                    </div>
                @endif
            </div>

            <!-- Recent Attendance History Card -->
            <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 flex items-center justify-center font-bold text-lg border border-sky-100 dark:border-sky-900/50">
                            <i class="fa-solid fa-calendar-days"></i>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-800 dark:text-white m-0">
                                {{ $isAr ? 'سجل الحضور الأخير (آخر أسبوعين)' : 'Recent Attendance Log (Last 14 Days)' }}
                            </h2>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto mt-4">
                    <table class="w-full text-xs text-start">
                        <thead>
                            <tr class="border-b border-slate-100 dark:border-slate-800 text-slate-400 font-bold uppercase tracking-wider text-[10px]">
                                <th class="pb-3 text-start">{{ $isAr ? 'التاريخ' : 'Date' }}</th>
                                <th class="pb-3 text-start">{{ $isAr ? 'وقت الحضور' : 'Check In' }}</th>
                                <th class="pb-3 text-start">{{ $isAr ? 'وقت الانصراف' : 'Check Out' }}</th>
                                <th class="pb-3 text-start">{{ $isAr ? 'ساعات العمل' : 'Hours' }}</th>
                                <th class="pb-3 text-start">{{ $isAr ? 'الحالة' : 'Status' }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                            @forelse($attendanceHistory as $att)
                                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition-colors">
                                    <td class="py-2.5 font-mono font-bold text-slate-800 dark:text-white">
                                        {{ $att->attendance_date }}
                                    </td>
                                    <td class="py-2.5 font-mono text-emerald-600 dark:text-emerald-400 font-bold">
                                        {{ $att->check_in ?? '—' }}
                                    </td>
                                    <td class="py-2.5 font-mono text-slate-600 dark:text-slate-300">
                                        {{ $att->check_out ?? '—' }}
                                    </td>
                                    <td class="py-2.5 font-mono font-bold text-cyan-600 dark:text-cyan-400">
                                        {{ round($att->worked_minutes / 60, 1) }} {{ $isAr ? 'س' : 'h' }}
                                    </td>
                                    <td class="py-2.5">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full font-bold text-[10px] {{ $att->status === 'present' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400' : 'bg-amber-50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-400' }}">
                                            {{ $att->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-slate-400">
                                        {{ $isAr ? 'لا يوجد سجلات حضور سابقة.' : 'No recent attendance logs.' }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>

    <!-- Client-Side Live Digital Clock and Leave Days Calculator -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Live Digital Clock
            function updateClock() {
                const clockEl = document.getElementById('liveDigitalClock');
                if (!clockEl) return;
                const now = new Date();
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const seconds = String(now.getSeconds()).padStart(2, '0');
                clockEl.textContent = `${hours}:${minutes}:${seconds}`;
            }
            setInterval(updateClock, 1000);
            updateClock();

            // 2. Dynamic Leave Days Calculation
            const startInput = document.getElementById('selfLeaveStartDate');
            const endInput = document.getElementById('selfLeaveEndDate');
            const daysCalcBox = document.getElementById('selfLeaveDaysCalculation');
            const daysCount = document.getElementById('selfLeaveDaysCount');

            function calculateDays() {
                if (!startInput || !endInput || !daysCalcBox || !daysCount) return;
                const sVal = startInput.value;
                const eVal = endInput.value;

                if (sVal && eVal) {
                    const start = new Date(sVal);
                    const end = new Date(eVal);
                    if (end >= start) {
                        const diffTime = Math.abs(end - start);
                        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                        daysCalcBox.classList.remove('hidden');
                        daysCount.textContent = `{{ $isAr ? '${diffDays} يوم' : '${diffDays} days' }}`.replace('${diffDays}', diffDays);
                        return;
                    }
                }
                daysCalcBox.classList.add('hidden');
            }

            if (startInput) startInput.addEventListener('change', calculateDays);
            if (endInput) endInput.addEventListener('change', calculateDays);
        });
    </script>
</x-layouts.admin>
