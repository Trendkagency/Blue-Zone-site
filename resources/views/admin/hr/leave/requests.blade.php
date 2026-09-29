<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'إدارة طلبات الإجازات' : 'Leave Requests Management'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'سجل طلبات إجازات الموظفين ومتابعة القرارات والاعتمادات' : 'View, manage and track workforce leave applications'"
>
    @php
        $isAr = app()->getLocale() === 'ar';
    @endphp

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-800 dark:text-white m-0 flex items-center gap-2.5">
                <span class="w-10 h-10 rounded-xl bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 flex items-center justify-center text-lg border border-sky-100 dark:border-sky-900/50">
                    <i class="fa-solid fa-calendar-check"></i>
                </span>
                <span>{{ $isAr ? 'إدارة طلبات الإجازات' : 'Leave Requests' }}</span>
            </h1>
        </div>
        <div class="flex items-center gap-2.5 flex-wrap">
            <a href="{{ route('admin.hr.leave.approvals') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold bg-pink-50 text-pink-700 dark:bg-pink-950/40 dark:text-pink-300 border border-pink-200 dark:border-pink-800/60 hover:bg-pink-100 dark:hover:bg-pink-900/50 transition-colors shadow-sm">
                <i class="fa-solid fa-stamp text-xs"></i>
                <span>{{ $isAr ? 'قائمة الاعتمادات المعلقة' : 'Pending Approvals Queue' }}</span>
            </a>
            <a href="{{ route('admin.self-service.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold bg-sky-50 text-sky-700 dark:bg-sky-950/40 dark:text-sky-300 border border-sky-200 dark:border-sky-800/60 hover:bg-sky-100 dark:hover:bg-sky-900/50 transition-colors shadow-sm">
                <i class="fa-solid fa-user-clock text-xs"></i>
                <span>{{ $isAr ? 'بوابة الخدمة الذاتية' : 'Self-Service Hub' }}</span>
            </a>
            <button type="button" onclick="openCreateLeaveModal()" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold bg-gradient-to-r from-sky-600 to-cyan-600 hover:from-sky-500 hover:to-cyan-500 text-white transition-all shadow-md hover:shadow-sky-500/25 cursor-pointer">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>{{ $isAr ? 'تقديم طلب إجازة' : 'New Leave Request' }}</span>
            </button>
        </div>
    </div>

    <!-- Feedback Alerts -->
    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 text-emerald-800 dark:text-emerald-300 flex items-center gap-3 shadow-sm">
            <span class="w-8 h-8 rounded-lg bg-emerald-500 text-white flex items-center justify-center flex-shrink-0 font-bold text-sm">
                <i class="fa-solid fa-check"></i>
            </span>
            <div class="text-sm font-semibold flex-1">
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800/60 text-rose-800 dark:text-rose-300 flex items-center gap-3 shadow-sm">
            <span class="w-8 h-8 rounded-lg bg-rose-500 text-white flex items-center justify-center flex-shrink-0 font-bold text-sm">
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

    <!-- Filters & Search Bar -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 mb-6 shadow-sm">
        <form method="GET" action="{{ route('admin.hr.leave.requests') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1 uppercase tracking-wider">
                    {{ $isAr ? 'البحث بالاسم أو الرقم' : 'Search Employee' }}
                </label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ $isAr ? 'اسم الموظف أو رقمه الوظيفي...' : 'Name or employee number...' }}" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1 uppercase tracking-wider">
                    {{ $isAr ? 'حالة الطلب' : 'Status' }}
                </label>
                <select name="status" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white" onchange="this.form.submit()">
                    <option value="">{{ $isAr ? 'جميع الحالات' : 'All Statuses' }}</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ $isAr ? 'قيد المراجعة' : 'Pending' }}</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>{{ $isAr ? 'تمت الموافقة' : 'Approved' }}</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>{{ $isAr ? 'مرفوض' : 'Rejected' }}</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1 uppercase tracking-wider">
                    {{ $isAr ? 'نوع الإجازة' : 'Leave Type' }}
                </label>
                <select name="leave_type_id" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white" onchange="this.form.submit()">
                    <option value="">{{ $isAr ? 'جميع الأنواع' : 'All Leave Types' }}</option>
                    @foreach($leaveTypes as $lt)
                        <option value="{{ $lt->id }}" {{ request('leave_type_id') == $lt->id ? 'selected' : '' }}>
                            {{ $isAr ? $lt->name_ar : $lt->name_en }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 rounded-xl text-xs font-bold bg-slate-800 hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-600 text-white transition-colors cursor-pointer">
                    <i class="fa-solid fa-filter mr-1 ml-1"></i> {{ $isAr ? 'تصفية' : 'Filter' }}
                </button>
                @if(request()->hasAny(['search', 'status', 'leave_type_id']))
                    <a href="{{ route('admin.hr.leave.requests') }}" class="px-3 py-2 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 transition-colors">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Requests Table Card -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-start">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 text-slate-400 font-bold uppercase tracking-wider text-[10px]">
                        <th class="py-3.5 px-4 text-start">{{ $isAr ? 'الموظف' : 'Employee' }}</th>
                        <th class="py-3.5 px-4 text-start">{{ $isAr ? 'نوع الإجازة' : 'Leave Type' }}</th>
                        <th class="py-3.5 px-4 text-start">{{ $isAr ? 'الفترة' : 'Period' }}</th>
                        <th class="py-3.5 px-4 text-start">{{ $isAr ? 'المدة' : 'Days' }}</th>
                        <th class="py-3.5 px-4 text-start">{{ $isAr ? 'السبب / الملاحظات' : 'Reason' }}</th>
                        <th class="py-3.5 px-4 text-start">{{ $isAr ? 'الحالة' : 'Status' }}</th>
                        <th class="py-3.5 px-4 text-end">{{ $isAr ? 'الإجراءات' : 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                    @forelse($requests as $req)
                        @php
                            $st = $req->status;
                            $stBadge = match($st) {
                                'approved' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800',
                                'rejected' => 'bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-400 border-rose-200 dark:border-rose-800',
                                default => 'bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-400 border-amber-200 dark:border-amber-800',
                            };
                            $stLabel = match($st) {
                                'approved' => ($isAr ? 'تمت الموافقة' : 'Approved'),
                                'rejected' => ($isAr ? 'مرفوض' : 'Rejected'),
                                default => ($isAr ? 'قيد المراجعة' : 'Pending'),
                            };
                        @endphp
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-sky-100 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300 font-bold flex items-center justify-center text-xs flex-shrink-0">
                                        {{ strtoupper(substr($req->employee?->full_name ?? 'EM', 0, 2)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.hr.employees.show', $req->employee_id) }}" class="font-bold text-slate-800 dark:text-white hover:text-sky-600 dark:hover:text-sky-400">
                                            {{ $req->employee?->full_name ?? 'N/A' }}
                                        </a>
                                        <span class="block text-[10px] font-mono text-slate-400">{{ $req->employee?->employee_number }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 font-bold text-slate-700 dark:text-slate-200">
                                {{ $isAr ? ($req->leaveType?->name_ar ?? $req->leaveType?->name_en) : ($req->leaveType?->name_en ?? $req->leaveType?->name_ar) }}
                            </td>
                            <td class="py-3.5 px-4 font-mono text-slate-600 dark:text-slate-300 whitespace-nowrap">
                                {{ $req->start_date }} &rarr; {{ $req->end_date }}
                            </td>
                            <td class="py-3.5 px-4 font-mono font-bold text-sky-600 dark:text-sky-400">
                                {{ (float)$req->total_days }} {{ $isAr ? 'يوم' : 'd' }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-500 dark:text-slate-400 max-w-xs truncate">
                                {{ $req->reason ?? '—' }}
                                @if($req->rejection_reason)
                                    <div class="text-[10px] text-rose-500 font-semibold mt-0.5">
                                        {{ $isAr ? 'الرفض: ' : 'Rejected: ' }} {{ $req->rejection_reason }}
                                    </div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full font-bold text-[11px] border {{ $stBadge }}">
                                    {{ $stLabel }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-end">
                                @if($req->status === 'pending')
                                    <div class="inline-flex items-center gap-1.5">
                                        <form action="{{ route('admin.hr.leave.requests.approve', $req->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-1.5 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 dark:bg-emerald-950/60 dark:text-emerald-400 dark:hover:bg-emerald-900/60 border border-emerald-200 dark:border-emerald-800/60 font-bold transition-colors cursor-pointer" title="{{ $isAr ? 'موافقة واعتماد' : 'Approve' }}">
                                                <i class="fa-solid fa-check text-xs"></i>
                                            </button>
                                        </form>
                                        <button type="button" onclick="openRejectModal({{ $req->id }}, '{{ addslashes($req->employee?->full_name ?? '') }}')" class="p-1.5 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 dark:bg-rose-950/60 dark:text-rose-400 dark:hover:bg-rose-900/60 border border-rose-200 dark:border-rose-800/60 font-bold transition-colors cursor-pointer" title="{{ $isAr ? 'رفض الطلب' : 'Reject' }}">
                                            <i class="fa-solid fa-xmark text-xs"></i>
                                        </button>
                                    </div>
                                @else
                                    <span class="text-[10px] text-slate-400 font-semibold">
                                        {{ $req->status === 'approved' ? ($isAr ? 'معتمد' : 'Authorized') : ($isAr ? 'مرفوض' : 'Declined') }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <i class="fa-solid fa-folder-open text-4xl mb-2 block text-slate-300 dark:text-slate-700"></i>
                                {{ $isAr ? 'لا توجد طلبات إجازة مسجلة تطابق معايير البحث.' : 'No leave requests recorded.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($requests->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $requests->links() }}
            </div>
        @endif
    </div>

    <!-- Apply Leave Modal (for Admin / HR on behalf of any employee) -->
    <div id="adminCreateLeaveModal" class="hidden fixed inset-0 z-[99999] items-center justify-center bg-slate-950/60 backdrop-blur-sm p-4 overflow-y-auto">
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 w-full max-w-lg shadow-2xl overflow-hidden animate-scale-up">
            <div class="flex items-center justify-between p-5 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-sky-100 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 flex items-center justify-center font-bold text-sm">
                        <i class="fa-solid fa-calendar-plus"></i>
                    </span>
                    <h3 class="text-base font-bold text-slate-800 dark:text-white m-0">
                        {{ $isAr ? 'تقديم طلب إجازة لموظف' : 'Create Leave Request for Employee' }}
                    </h3>
                </div>
                <button type="button" onclick="closeCreateLeaveModal()" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 flex items-center justify-center cursor-pointer">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form action="{{ route('admin.hr.leave.requests.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        {{ $isAr ? 'اختر الموظف *' : 'Employee *' }}
                    </label>
                    <select name="employee_id" required class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:ring-2 focus:ring-sky-500">
                        <option value="" disabled selected>{{ $isAr ? '— اختر الموظف —' : '— Select Employee —' }}</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">
                                {{ $emp->full_name }} ({{ $emp->employee_number }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        {{ $isAr ? 'نوع الإجازة *' : 'Leave Type *' }}
                    </label>
                    <select name="leave_type_id" required class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:ring-2 focus:ring-sky-500">
                        <option value="" disabled selected>{{ $isAr ? '— اختر نوع الإجازة —' : '— Select Leave Type —' }}</option>
                        @foreach($leaveTypes as $lt)
                            <option value="{{ $lt->id }}">
                                {{ $isAr ? $lt->name_ar : $lt->name_en }} ({{ $lt->annual_days }} {{ $isAr ? 'أيام/سنة' : 'days/yr' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            {{ $isAr ? 'تاريخ البدء *' : 'Start Date *' }}
                        </label>
                        <input type="date" name="start_date" required class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:ring-2 focus:ring-sky-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            {{ $isAr ? 'تاريخ الانتهاء *' : 'End Date *' }}
                        </label>
                        <input type="date" name="end_date" required class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:ring-2 focus:ring-sky-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        {{ $isAr ? 'سبب الإجازة وملاحظات' : 'Reason / Notes' }}
                    </label>
                    <textarea name="reason" rows="3" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:ring-2 focus:ring-sky-500"></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" onclick="closeCreateLeaveModal()" class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition-colors cursor-pointer">
                        {{ $isAr ? 'إلغاء' : 'Cancel' }}
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold bg-gradient-to-r from-sky-600 to-cyan-600 hover:from-sky-500 hover:to-cyan-500 text-white shadow-md transition-all cursor-pointer">
                        {{ $isAr ? 'حفظ وإرسال' : 'Submit Application' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Reject Reason Modal -->
    <div id="adminRejectLeaveModal" class="hidden fixed inset-0 z-[99999] items-center justify-center bg-slate-950/60 backdrop-blur-sm p-4">
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 w-full max-w-md shadow-2xl overflow-hidden animate-scale-up">
            <div class="flex items-center justify-between p-5 border-b border-slate-100 dark:border-slate-800 bg-rose-50/50 dark:bg-rose-950/20">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-lg bg-rose-100 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center font-bold text-sm">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </span>
                    <h3 class="text-base font-bold text-slate-800 dark:text-white m-0">
                        {{ $isAr ? 'رفض طلب الإجازة' : 'Reject Leave Request' }}
                    </h3>
                </div>
                <button type="button" onclick="closeRejectModal()" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 flex items-center justify-center cursor-pointer">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form id="rejectLeaveForm" method="POST" class="p-6 space-y-4">
                @csrf
                <p class="text-xs text-slate-600 dark:text-slate-300 m-0">
                    {{ $isAr ? 'هل أنت متأكد من رفض طلب الإجازة للموظف:' : 'Are you sure you want to reject the leave request for:' }}
                    <strong id="rejectEmployeeName" class="text-rose-600 dark:text-rose-400"></strong>؟
                </p>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        {{ $isAr ? 'سبب الرفض (سيظهر للموظف)' : 'Rejection Reason (Visible to employee)' }}
                    </label>
                    <textarea name="rejection_reason" rows="3" required placeholder="{{ $isAr ? 'يرجى توضيح سبب رفض الطلب...' : 'Please specify the rejection reason...' }}" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:ring-2 focus:ring-rose-500"></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" onclick="closeRejectModal()" class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition-colors cursor-pointer">
                        {{ $isAr ? 'تراجع' : 'Cancel' }}
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold bg-gradient-to-r from-rose-600 to-pink-600 hover:from-rose-500 hover:to-pink-500 text-white shadow-md transition-all cursor-pointer">
                        {{ $isAr ? 'تأكيد الرفض' : 'Confirm Rejection' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openCreateLeaveModal() {
            const m = document.getElementById('adminCreateLeaveModal');
            if (m) {
                m.classList.remove('hidden');
                m.classList.add('flex');
            }
        }
        function closeCreateLeaveModal() {
            const m = document.getElementById('adminCreateLeaveModal');
            if (m) {
                m.classList.add('hidden');
                m.classList.remove('flex');
            }
        }

        function openRejectModal(requestId, empName) {
            const form = document.getElementById('rejectLeaveForm');
            const nameEl = document.getElementById('rejectEmployeeName');
            const modal = document.getElementById('adminRejectLeaveModal');
            if (form && modal) {
                form.action = `{{ url('admin/hr/leave/requests') }}/${requestId}/reject`;
                if (nameEl) nameEl.textContent = empName;
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        }
        function closeRejectModal() {
            const modal = document.getElementById('adminRejectLeaveModal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }
    </script>
</x-layouts.admin>
