@php
    $isAr = app()->getLocale() === 'ar';
    $user = auth()->user();
    $employee = $user ? $user->getOrCreateEmployee() : null;
    $today = now()->toDateString();
    $year = (int) now()->year;

    $todayAttendance = $employee ? \App\Models\AttendanceRecord::where('employee_id', $employee->id)->whereDate('attendance_date', $today)->first() : null;
    $isCheckedIn = !empty($todayAttendance?->check_in);
    $isCheckedOut = !empty($todayAttendance?->check_out);
    $workedHours = $todayAttendance ? round($todayAttendance->worked_minutes / 60, 1) : 0;

    $leaveTypes = \App\Models\LeaveType::where('is_active', true)->get();
    $leaveBalances = $employee ? \App\Models\LeaveBalance::where('employee_id', $employee->id)->where('year', $year)->get() : collect();
@endphp

<!-- Employee Self-Service Modal Backdrop & Dialog -->
<div id="employeeSelfServiceModal" class="hidden fixed inset-0 z-[999999] items-center justify-center bg-slate-950/75 backdrop-blur-md p-3 sm:p-5 overflow-y-auto" role="dialog" aria-modal="true">
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 w-full max-w-2xl shadow-2xl overflow-hidden flex flex-col max-h-[92vh] transition-all transform animate-scale-up relative">
        
        <!-- Top Radiant Gradient Accent Bar -->
        <div class="h-1.5 w-full bg-gradient-to-r from-[#0A4F78] via-sky-500 to-teal-400"></div>

        <!-- Modal Header -->
        <div class="p-5 sm:p-6 border-b border-slate-100 dark:border-slate-800/80 bg-slate-50/70 dark:bg-slate-900/90 flex items-center justify-between relative">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#0A4F78] via-sky-600 to-cyan-500 text-white flex items-center justify-center font-black text-xl shadow-lg shadow-sky-500/25 flex-shrink-0 border border-white/20">
                    <i class="fa-solid fa-fingerprint"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-base sm:text-lg font-black text-slate-900 dark:text-white m-0 tracking-tight">
                            {{ $isAr ? 'بوابة الخدمة الذاتية للموظف' : 'Employee Self-Service' }}
                        </h2>
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black font-mono bg-sky-100 dark:bg-sky-950/70 text-sky-700 dark:text-sky-300 border border-sky-200 dark:border-sky-800/80 shadow-xs">
                            {{ $employee?->employee_number ?? 'EMP-001' }}
                        </span>
                    </div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 mt-0.5 mb-0 flex items-center gap-2 flex-wrap">
                        <span class="text-slate-800 dark:text-slate-200 font-bold">{{ $employee?->full_name ?? ($user?->name ?? 'Staff') }}</span>
                        <span class="opacity-40">&bull;</span>
                        <span class="text-sky-600 dark:text-sky-400">{{ $employee?->position?->name ?? ($isAr ? 'موظف' : 'Staff') }}</span>
                        @if($employee?->department)
                            <span class="opacity-40">&bull;</span>
                            <span>{{ $employee->department->name }}</span>
                        @endif
                    </p>
                </div>
            </div>

            <button type="button" onclick="closeEmployeeSelfServiceModal()" class="w-9 h-9 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-200 dark:hover:bg-slate-700 flex items-center justify-center transition-all cursor-pointer border border-slate-200/50 dark:border-slate-700/50" aria-label="Close">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Modern Segmented Pill Tabs Navigation -->
        <div class="px-5 sm:px-6 py-3 border-b border-slate-100 dark:border-slate-800/80 bg-white dark:bg-slate-900/60">
            <div class="grid grid-cols-3 gap-1.5 p-1.5 rounded-2xl bg-slate-100/90 dark:bg-slate-800/90 border border-slate-200/70 dark:border-slate-700/70">
                <button type="button" onclick="switchSelfServiceTab('clock')" id="tabBtn-clock" class="self-service-tab-btn active py-2.5 px-3 rounded-xl text-xs font-black transition-all flex items-center justify-center gap-2 cursor-pointer shadow-sm bg-white dark:bg-sky-600 text-slate-900 dark:text-white border border-slate-200/80 dark:border-sky-500">
                    <i class="fa-solid fa-stopwatch text-sky-500 dark:text-white text-sm"></i>
                    <span class="truncate">{{ $isAr ? 'تسجيل الحضور' : 'Time Clock' }}</span>
                </button>
                <button type="button" onclick="switchSelfServiceTab('leave')" id="tabBtn-leave" class="self-service-tab-btn py-2.5 px-3 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2 cursor-pointer text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white">
                    <i class="fa-solid fa-plane-departure text-amber-500 text-sm"></i>
                    <span class="truncate">{{ $isAr ? 'طلب إجازة' : 'Apply Leave' }}</span>
                </button>
                <button type="button" onclick="switchSelfServiceTab('history')" id="tabBtn-history" class="self-service-tab-btn py-2.5 px-3 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2 cursor-pointer text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white">
                    <i class="fa-solid fa-clock-rotate-left text-teal-500 text-sm"></i>
                    <span class="truncate">{{ $isAr ? 'سجل إجازاتي' : 'My History' }}</span>
                </button>
            </div>
        </div>

        <!-- Modal Body Content -->
        <div class="p-5 sm:p-6 overflow-y-auto flex-1 space-y-4">
            
            <!-- Dynamic Notification / Error Alert in Modal -->
            <div id="selfServiceModalAlert" class="hidden p-4 rounded-2xl text-xs font-bold flex items-center gap-3 shadow-sm border transition-all animate-fade-in">
                <span id="selfServiceModalAlertIcon" class="w-7 h-7 rounded-xl flex items-center justify-center text-xs text-white flex-shrink-0 shadow-sm"></span>
                <span id="selfServiceModalAlertText" class="flex-1 leading-relaxed"></span>
            </div>

            <!-- ========================================== -->
            <!-- TAB 1: Time Clock / Check In & Check Out   -->
            <!-- ========================================== -->
            <div id="tabContent-clock" class="self-service-tab-content space-y-5">
                
                <!-- Digital Animated Clock Visual Card -->
                <div class="p-6 rounded-3xl bg-gradient-to-b from-[#062B49] via-[#0A4F78] to-[#041E34] text-white text-center shadow-xl border border-sky-500/20 relative overflow-hidden">
                    <!-- Subtle ambient lighting -->
                    <div class="absolute -top-16 -right-16 w-40 h-40 bg-cyan-400/15 rounded-full blur-3xl pointer-events-none"></div>
                    <div class="absolute -bottom-16 -left-16 w-40 h-40 bg-sky-500/15 rounded-full blur-3xl pointer-events-none"></div>

                    <div class="relative z-10">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/15 text-[11px] font-black uppercase tracking-widest text-cyan-300 mb-2 shadow-inner">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                            {{ $isAr ? 'توقيت النظام المباشر' : 'Live System Time' }}
                        </div>
                        
                        <div id="modalDigitalClock" class="text-4xl sm:text-5xl font-black font-mono tracking-widest text-transparent bg-clip-text bg-gradient-to-r from-sky-300 via-cyan-200 to-emerald-300 drop-shadow-md my-1">
                            {{ now()->format('H:i:s') }}
                        </div>

                        <div class="text-xs text-slate-300 font-semibold mt-1">
                            {{ now()->translatedFormat('l, d F Y') }} &bull; <span class="text-cyan-300 font-mono">{{ config('app.timezone', 'UTC') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Shift & Today Telemetry Cards Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    
                    <!-- Shift Status Box -->
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-800 flex items-center gap-3.5 shadow-xs">
                        <div class="w-10 h-10 rounded-xl {{ $isCheckedIn && !$isCheckedOut ? 'bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30' : ($isCheckedOut ? 'bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300' : 'bg-amber-500/15 text-amber-600 dark:text-amber-400 border border-amber-500/30') }} flex items-center justify-center text-base font-bold flex-shrink-0">
                            <i class="fa-solid {{ $isCheckedIn && !$isCheckedOut ? 'fa-user-check' : ($isCheckedOut ? 'fa-circle-check' : 'fa-clock') }}"></i>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">{{ $isAr ? 'حالة الحضور اليوم' : "Today's Status" }}</span>
                            <span class="text-xs sm:text-sm font-black text-slate-800 dark:text-white">
                                @if($isCheckedIn && !$isCheckedOut)
                                    <span class="text-emerald-600 dark:text-emerald-400">{{ $isAr ? 'حاضر في الوردية' : 'Present (On Shift)' }}</span>
                                @elseif($isCheckedOut)
                                    <span class="text-slate-600 dark:text-slate-300">{{ $isAr ? 'تم تسجيل الانصراف' : 'Shift Completed' }}</span>
                                @else
                                    <span class="text-amber-600 dark:text-amber-400">{{ $isAr ? 'لم يسجل الحضور بعد' : 'Not Clocked In' }}</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <!-- Worked Duration Box -->
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-800 flex items-center gap-3.5 shadow-xs">
                        <div class="w-10 h-10 rounded-xl bg-sky-500/15 text-sky-600 dark:text-sky-400 border border-sky-500/30 flex items-center justify-center text-base font-bold flex-shrink-0">
                            <i class="fa-solid fa-hourglass-half"></i>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">{{ $isAr ? 'ساعات العمل اليوم' : 'Hours Worked' }}</span>
                            <span class="text-xs sm:text-sm font-black font-mono text-slate-800 dark:text-white">
                                @if($isCheckedIn)
                                    {{ $workedHours }} <span class="text-xs font-normal text-slate-500">{{ $isAr ? 'ساعة' : 'hrs' }}</span>
                                    <span class="text-[10px] text-slate-400 block">({{ $isAr ? 'حضور:' : 'In:' }} {{ substr($todayAttendance->check_in, 0, 5) }})</span>
                                @else
                                    <span class="text-slate-400">0.0 {{ $isAr ? 'ساعة' : 'hrs' }}</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Big Luxury Action Punch Buttons -->
                <div id="modalPunchActionContainer" class="pt-1">
                    @if(!$isCheckedIn)
                        <button type="button" onclick="submitModalPunch('check-in')" id="modalPunchInBtn" class="w-full flex items-center justify-center gap-3 py-4 px-6 rounded-2xl font-black text-base bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 hover:from-emerald-500 hover:to-teal-500 text-white shadow-xl hover:shadow-emerald-500/30 transition-all transform hover:-translate-y-0.5 cursor-pointer border border-emerald-400/30">
                            <i class="fa-solid fa-right-to-bracket text-xl"></i>
                            <span>{{ $isAr ? 'تسجيل الحضور الآن' : 'Punch In (Check In)' }}</span>
                        </button>
                    @elseif($isCheckedIn && !$isCheckedOut)
                        <button type="button" onclick="submitModalPunch('check-out')" id="modalPunchOutBtn" class="w-full flex items-center justify-center gap-3 py-4 px-6 rounded-2xl font-black text-base bg-gradient-to-r from-rose-600 via-pink-600 to-rose-700 hover:from-rose-500 hover:to-pink-500 text-white shadow-xl hover:shadow-rose-500/30 transition-all transform hover:-translate-y-0.5 cursor-pointer border border-rose-400/30">
                            <i class="fa-solid fa-right-from-bracket text-xl"></i>
                            <span>{{ $isAr ? 'تسجيل الانصراف' : 'Punch Out (Check Out)' }}</span>
                        </button>
                    @else
                        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/70 text-center">
                            <div class="w-10 h-10 rounded-full bg-emerald-500 text-white flex items-center justify-center mx-auto mb-2 text-base font-bold shadow-md shadow-emerald-500/20">
                                <i class="fa-solid fa-check"></i>
                            </div>
                            <h4 class="text-xs sm:text-sm font-black text-emerald-900 dark:text-emerald-200 m-0">
                                {{ $isAr ? 'اكتمل تسجيل الحضور والانصراف اليوم بنجاح' : 'Attendance & Departure Logged' }}
                            </h4>
                            <p class="text-[11px] text-emerald-700 dark:text-emerald-400 mt-1 mb-0 font-medium">
                                {{ $isAr ? 'حضور:' : 'In:' }} <strong class="font-mono">{{ $todayAttendance->check_in }}</strong> &bull; 
                                {{ $isAr ? 'انصراف:' : 'Out:' }} <strong class="font-mono">{{ $todayAttendance->check_out }}</strong> &bull; 
                                {{ $isAr ? 'المدة:' : 'Total:' }} <strong class="font-mono">{{ $workedHours }} {{ $isAr ? 'ساعة' : 'hrs' }}</strong>
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- ========================================== -->
            <!-- TAB 2: Apply for Leave (Interactive Form)  -->
            <!-- ========================================== -->
            <div id="tabContent-leave" class="self-service-tab-content hidden space-y-4">
                <form id="modalLeaveForm" onsubmit="submitModalLeaveForm(event)" class="space-y-4">
                    @csrf
                    
                    <!-- Selectable Leave Type Cards -->
                    <div>
                        <label class="block text-xs font-black text-slate-800 dark:text-slate-200 mb-2 flex items-center justify-between">
                            <span>{{ $isAr ? 'اختر نوع الإجازة *' : 'Select Leave Type *' }}</span>
                            <span class="text-[10px] font-normal text-slate-400">{{ $isAr ? 'اضغط لتحديد النوع والرصيد' : 'Click to select' }}</span>
                        </label>
                        
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2" id="leaveTypeCardsGrid">
                            @foreach($leaveTypes as $lt)
                                @php
                                    $bal = $leaveBalances->where('leave_type_id', $lt->id)->first();
                                    $remDays = $bal ? (float)$bal->remaining_days : (float)$lt->annual_days;
                                    $icon = match($lt->code) {
                                        'sick' => 'fa-hospital text-rose-500',
                                        'emergency' => 'fa-bolt text-amber-500',
                                        'unpaid' => 'fa-circle-pause text-slate-400',
                                        'maternity', 'paternity' => 'fa-baby text-pink-500',
                                        default => 'fa-umbrella-beach text-sky-500',
                                    };
                                @endphp
                                <label class="leave-type-card-label relative flex flex-col justify-between p-3 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-sky-400 dark:hover:border-sky-500 bg-slate-50/50 dark:bg-slate-800/40 cursor-pointer transition-all hover:shadow-xs select-none">
                                    <input type="radio" name="leave_type_id" value="{{ $lt->id }}" data-remaining="{{ $remDays }}" class="hidden" {{ $loop->first ? 'checked' : '' }} onchange="updateLeaveTypeCardStyles(this)">
                                    <div class="flex items-center justify-between">
                                        <i class="fa-solid {{ $icon }} text-base"></i>
                                        <span class="text-[10px] font-mono font-black px-1.5 py-0.5 rounded-md bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300">
                                            {{ $remDays }}d
                                        </span>
                                    </div>
                                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200 mt-2 block">
                                        {{ $isAr ? $lt->name_ar : $lt->name_en }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Date Range -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                {{ $isAr ? 'تاريخ البدء *' : 'Start Date *' }}
                            </label>
                            <input type="date" name="start_date" id="modalLeaveStartDate" required min="{{ now()->toDateString() }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-sky-500 shadow-xs">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                                {{ $isAr ? 'تاريخ الانتهاء *' : 'End Date *' }}
                            </label>
                            <input type="date" name="end_date" id="modalLeaveEndDate" required min="{{ now()->toDateString() }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white text-xs font-semibold focus:ring-2 focus:ring-sky-500 shadow-xs">
                        </div>
                    </div>

                    <!-- Dynamic Days Calculation Pill -->
                    <div id="modalLeaveDaysBadge" class="hidden p-3.5 rounded-2xl bg-sky-50 dark:bg-sky-950/40 border border-sky-200 dark:border-sky-800/70 flex items-center justify-between text-xs animate-fade-in shadow-xs">
                        <span class="text-sky-800 dark:text-sky-300 font-bold flex items-center gap-2">
                            <i class="fa-solid fa-calculator text-sky-600 dark:text-sky-400"></i>
                            {{ $isAr ? 'إجمالي الأيام المطلوبة:' : 'Total Requested Duration:' }}
                        </span>
                        <span id="modalLeaveDaysText" class="font-mono font-black text-xs text-sky-800 dark:text-sky-200 bg-white dark:bg-slate-900 px-3 py-1 rounded-xl border border-sky-200 dark:border-sky-700 shadow-xs">
                            0 {{ $isAr ? 'يوم' : 'days' }}
                        </span>
                    </div>

                    <!-- Reason -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            {{ $isAr ? 'سبب طلب الإجازة *' : 'Reason for Leave *' }}
                        </label>
                        <textarea name="reason" rows="2" required placeholder="{{ $isAr ? 'يرجى كتابة تفاصيل سبب طلب الإجازة...' : 'Provide details for your request...' }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white text-xs focus:ring-2 focus:ring-sky-500 shadow-xs"></textarea>
                    </div>

                    <!-- Attachment -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            {{ $isAr ? 'مرفق اختياري (تقرير طبي أو مستند)' : 'Attachment (Optional)' }}
                        </label>
                        <input type="file" name="attachment" accept=".pdf,.jpg,.jpeg,.png,.webp,.doc,.docx" class="w-full text-xs text-slate-500 dark:text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-sky-100 file:text-sky-700 dark:file:bg-sky-950/60 dark:file:text-sky-300 hover:file:bg-sky-200 cursor-pointer">
                    </div>

                    <div class="pt-1">
                        <button type="submit" id="modalLeaveSubmitBtn" class="w-full flex items-center justify-center gap-2 py-3.5 px-6 rounded-2xl font-black text-xs bg-gradient-to-r from-sky-600 via-cyan-600 to-sky-700 hover:from-sky-500 hover:to-cyan-500 text-white shadow-lg hover:shadow-sky-500/30 transition-all cursor-pointer border border-sky-400/30">
                            <i class="fa-solid fa-paper-plane text-sm"></i>
                            <span>{{ $isAr ? 'إرسال طلب الإجازة للاعتماد' : 'Submit Leave Application' }}</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- ========================================== -->
            <!-- TAB 3: My Leaves History                   -->
            <!-- ========================================== -->
            <div id="tabContent-history" class="self-service-tab-content hidden space-y-4">
                <div id="modalRecentLeavesContainer" class="space-y-2.5">
                    <div class="p-8 text-center text-slate-400 text-xs">
                        <i class="fa-solid fa-spinner fa-spin text-2xl text-sky-500 mb-2 block"></i>
                        {{ $isAr ? 'جاري تحميل سجل الإجازات...' : 'Loading history...' }}
                    </div>
                </div>

                <div class="pt-2 text-center border-t border-slate-100 dark:border-slate-800">
                    <a href="{{ route('admin.self-service.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-sky-600 dark:text-sky-400 hover:underline">
                        <span>{{ $isAr ? 'الانتقال إلى لوحة الخدمة الذاتية الكاملة' : 'View Full Self-Service Hub' }}</span>
                        <i class="fa-solid fa-arrow-right rtl:rotate-180 text-[10px]"></i>
                    </a>
                </div>
            </div>

        </div>

    </div>
</div>

<!-- Modal Interactive Logic Script -->
<script>
    window.openEmployeeSelfServiceModal = function (defaultTab = 'clock') {
        const modal = document.getElementById('employeeSelfServiceModal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            switchSelfServiceTab(defaultTab);
            loadSelfServiceStatus();

            // Highlight default selected radio card in leave tab
            const activeRadio = document.querySelector('input[name="leave_type_id"]:checked');
            if (activeRadio) updateLeaveTypeCardStyles(activeRadio);
        }
    };

    window.closeEmployeeSelfServiceModal = function () {
        const modal = document.getElementById('employeeSelfServiceModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    };

    window.switchSelfServiceTab = function (tabKey) {
        document.querySelectorAll('.self-service-tab-btn').forEach(btn => {
            btn.classList.remove('bg-white', 'dark:bg-sky-600', 'text-slate-900', 'dark:text-white', 'shadow-sm', 'border-slate-200/80', 'dark:border-sky-500', 'font-black', 'active');
            btn.classList.add('text-slate-600', 'dark:text-slate-300', 'font-bold');
        });

        document.querySelectorAll('.self-service-tab-content').forEach(content => {
            content.classList.add('hidden');
        });

        const activeBtn = document.getElementById('tabBtn-' + tabKey);
        const activeContent = document.getElementById('tabContent-' + tabKey);

        if (activeBtn) {
            activeBtn.classList.remove('text-slate-600', 'dark:text-slate-300', 'font-bold');
            activeBtn.classList.add('bg-white', 'dark:bg-sky-600', 'text-slate-900', 'dark:text-white', 'shadow-sm', 'border-slate-200/80', 'dark:border-sky-500', 'font-black', 'active');
        }
        if (activeContent) {
            activeContent.classList.remove('hidden');
        }

        if (tabKey === 'history') {
            loadSelfServiceStatus();
        }
    };

    window.updateLeaveTypeCardStyles = function (selectedRadio) {
        document.querySelectorAll('.leave-type-card-label').forEach(label => {
            label.classList.remove('ring-2', 'ring-sky-500', 'bg-sky-50/80', 'dark:bg-sky-950/40', 'border-sky-400', 'dark:border-sky-500');
            label.classList.add('border-slate-200', 'dark:border-slate-800', 'bg-slate-50/50', 'dark:bg-slate-800/40');
        });

        if (selectedRadio && selectedRadio.parentElement) {
            selectedRadio.parentElement.classList.remove('border-slate-200', 'dark:border-slate-800', 'bg-slate-50/50', 'dark:bg-slate-800/40');
            selectedRadio.parentElement.classList.add('ring-2', 'ring-sky-500', 'bg-sky-50/80', 'dark:bg-sky-950/40', 'border-sky-400', 'dark:border-sky-500');
        }
    };

    // Live Digital Clock in Modal
    function updateModalClock() {
        const clockEl = document.getElementById('modalDigitalClock');
        if (!clockEl) return;
        const now = new Date();
        const h = String(now.getHours()).padStart(2, '0');
        const m = String(now.getMinutes()).padStart(2, '0');
        const s = String(now.getSeconds()).padStart(2, '0');
        clockEl.textContent = `${h}:${m}:${s}`;
    }
    setInterval(updateModalClock, 1000);

    // Dynamic Leave Days in Modal
    document.addEventListener('DOMContentLoaded', function () {
        const sIn = document.getElementById('modalLeaveStartDate');
        const eIn = document.getElementById('modalLeaveEndDate');
        const bBox = document.getElementById('modalLeaveDaysBadge');
        const bTxt = document.getElementById('modalLeaveDaysText');

        function calcModalDays() {
            if (!sIn || !eIn || !bBox || !bTxt) return;
            const sv = sIn.value;
            const ev = eIn.value;
            if (sv && ev) {
                const s = new Date(sv);
                const e = new Date(ev);
                if (e >= s) {
                    const diffDays = Math.ceil(Math.abs(e - s) / (1000 * 60 * 60 * 24)) + 1;
                    bBox.classList.remove('hidden');
                    bTxt.textContent = `{{ $isAr ? '${diffDays} يوم' : '${diffDays} days' }}`.replace('${diffDays}', diffDays);
                    return;
                }
            }
            bBox.classList.add('hidden');
        }

        if (sIn) sIn.addEventListener('change', calcModalDays);
        if (eIn) eIn.addEventListener('change', calcModalDays);
    });

    // Fetch and sync status
    window.loadSelfServiceStatus = function () {
        fetch('{{ route("admin.self-service.status") }}', {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) return;

            // Render Recent Leaves in History Tab
            const historyContainer = document.getElementById('modalRecentLeavesContainer');
            if (historyContainer && data.recent_leaves) {
                if (data.recent_leaves.length === 0) {
                    historyContainer.innerHTML = `
                        <div class="p-8 text-center text-slate-400 text-xs rounded-2xl bg-slate-50/50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                            <div class="w-12 h-12 rounded-2xl bg-sky-50 dark:bg-sky-950/40 text-sky-500 flex items-center justify-center mx-auto mb-2 text-xl">
                                <i class="fa-solid fa-calendar-check"></i>
                            </div>
                            <h4 class="font-bold text-slate-700 dark:text-slate-300 m-0">{{ $isAr ? "لا توجد طلبات إجازة سابقة" : "No previous leave requests" }}</h4>
                            <p class="text-[11px] text-slate-400 mt-1 mb-3">{{ $isAr ? "يمكنك تقديم طلب إجازة جديد في أي وقت" : "You can submit a new leave application anytime" }}</p>
                            <button type="button" onclick="switchSelfServiceTab('leave')" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold bg-sky-600 hover:bg-sky-500 text-white shadow-sm cursor-pointer">
                                <i class="fa-solid fa-plus"></i>
                                <span>{{ $isAr ? "تقديم طلب إجازة الآن" : "Apply for Leave Now" }}</span>
                            </button>
                        </div>
                    `;
                } else {
                    let html = '';
                    data.recent_leaves.forEach(lr => {
                        const stBadge = lr.status === 'approved' 
                            ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800'
                            : (lr.status === 'rejected' ? 'bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 border-rose-200 dark:border-rose-800' : 'bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border-amber-200 dark:border-amber-800');
                        const stLabel = lr.status === 'approved' ? '{{ $isAr ? "تمت الموافقة" : "Approved" }}' : (lr.status === 'rejected' ? '{{ $isAr ? "مرفوض" : "Rejected" }}' : '{{ $isAr ? "قيد المراجعة" : "Pending" }}');

                        html += `
                            <div class="p-3.5 rounded-2xl bg-slate-50/80 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-800 flex items-center justify-between text-xs hover:border-sky-300 dark:hover:border-sky-700 transition-colors shadow-xs">
                                <div>
                                    <div class="font-black text-slate-900 dark:text-white">${lr.type_name} &bull; <span class="font-mono text-sky-600 dark:text-sky-400 font-bold">${lr.total_days} {{ $isAr ? "يوم" : "days" }}</span></div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 font-mono mt-0.5">${lr.start_date} &rarr; ${lr.end_date}</div>
                                    ${lr.rejection_reason ? `<div class="text-[10px] text-rose-500 mt-1 font-bold">{{ $isAr ? "سبب الرفض: " : "Reason: " }}${lr.rejection_reason}</div>` : ''}
                                </div>
                                <div>
                                    <span class="px-2.5 py-1 rounded-full font-black text-[10px] border ${stBadge}">${stLabel}</span>
                                </div>
                            </div>
                        `;
                    });
                    historyContainer.innerHTML = html;
                }
            }
        })
        .catch(err => console.warn('Self-service sync error:', err));
    };

    // Submit Punch (Check In / Check Out) via AJAX
    window.submitModalPunch = function (actionType) {
        const url = actionType === 'check-in' 
            ? '{{ route("admin.self-service.check-in") }}' 
            : '{{ route("admin.self-service.check-out") }}';

        const btn = document.getElementById(actionType === 'check-in' ? 'modalPunchInBtn' : 'modalPunchOutBtn');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin text-lg"></i> <span>{{ $isAr ? "جاري التسجيل..." : "Processing..." }}</span>`;
        }

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showModalAlert(data.message, 'success');
                setTimeout(() => window.location.reload(), 900);
            } else {
                showModalAlert(data.message || '{{ $isAr ? "حدث خطأ أثناء التسجيل" : "Failed to record punch" }}', 'error');
                if (btn) btn.disabled = false;
            }
        })
        .catch(err => {
            showModalAlert('{{ $isAr ? "حدث خطأ في الاتصال بالخادم" : "Server connection error" }}', 'error');
            if (btn) btn.disabled = false;
        });
    };

    // Submit Leave Form via AJAX
    window.submitModalLeaveForm = function (e) {
        e.preventDefault();
        const form = document.getElementById('modalLeaveForm');
        const submitBtn = document.getElementById('modalLeaveSubmitBtn');
        const formData = new FormData(form);

        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> <span>{{ $isAr ? "جاري الإرسال..." : "Submitting..." }}</span>`;
        }

        fetch('{{ route("admin.self-service.leave") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showModalAlert(data.message, 'success');
                form.reset();
                const bBox = document.getElementById('modalLeaveDaysBadge');
                if (bBox) bBox.classList.add('hidden');
                setTimeout(() => {
                    switchSelfServiceTab('history');
                }, 900);
            } else {
                showModalAlert(data.message || '{{ $isAr ? "تعذر تقديم طلب الإجازة" : "Failed to submit leave" }}', 'error');
            }
        })
        .catch(err => {
            showModalAlert('{{ $isAr ? "حدث خطأ أثناء الاتصال بالخادم" : "Server connection error" }}', 'error');
        })
        .finally(() => {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = `<i class="fa-solid fa-paper-plane text-sm"></i> <span>{{ $isAr ? "إرسال طلب الإجازة للاعتماد" : "Submit Leave Application" }}</span>`;
            }
        });
    };

    function showModalAlert(text, type = 'success') {
        const alertBox = document.getElementById('selfServiceModalAlert');
        const icon = document.getElementById('selfServiceModalAlertIcon');
        const txt = document.getElementById('selfServiceModalAlertText');

        if (!alertBox || !icon || !txt) return;

        txt.textContent = text;
        alertBox.classList.remove('hidden', 'bg-emerald-50', 'text-emerald-800', 'border-emerald-200', 'bg-rose-50', 'text-rose-800', 'border-rose-200', 'dark:bg-emerald-950/60', 'dark:text-emerald-200', 'dark:border-emerald-800', 'dark:bg-rose-950/60', 'dark:text-rose-200', 'dark:border-rose-800');

        if (type === 'success') {
            alertBox.classList.add('bg-emerald-50', 'dark:bg-emerald-950/60', 'text-emerald-800', 'dark:text-emerald-200', 'border-emerald-200', 'dark:border-emerald-800');
            icon.className = 'w-7 h-7 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-xs flex-shrink-0 shadow-sm';
            icon.innerHTML = '<i class="fa-solid fa-check"></i>';
        } else {
            alertBox.classList.add('bg-rose-50', 'dark:bg-rose-950/60', 'text-rose-800', 'dark:text-rose-200', 'border-rose-200', 'dark:border-rose-800');
            icon.className = 'w-7 h-7 rounded-xl bg-rose-500 text-white flex items-center justify-center text-xs flex-shrink-0 shadow-sm';
            icon.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i>';
        }
    }
</script>
