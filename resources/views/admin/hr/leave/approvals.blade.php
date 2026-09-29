<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'اعتمادات الإجازات المعلقة' : 'Pending Leave Approvals Queue'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'طلبات الإجازة المعلقة بانتظار موافقة أو رفض الإدارة' : 'Pending leave authorizations awaiting administrative decision'"
>
    @php
        $isAr = app()->getLocale() === 'ar';
    @endphp

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-800 dark:text-white m-0 flex items-center gap-2.5">
                <span class="w-10 h-10 rounded-xl bg-pink-50 dark:bg-pink-950/60 text-pink-600 dark:text-pink-400 flex items-center justify-center text-lg border border-pink-100 dark:border-pink-900/50">
                    <i class="fa-solid fa-stamp"></i>
                </span>
                <span>{{ $isAr ? 'طلبات الإجازة المعلقة للاعتماد' : 'Pending Leave Approvals' }}</span>
            </h1>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('admin.hr.leave.requests') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                <i class="fa-solid fa-list-check"></i>
                <span>{{ $isAr ? 'جميع طلبات الإجازات' : 'All Leave Requests' }}</span>
            </a>
            <a href="{{ route('admin.self-service.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold bg-sky-50 text-sky-700 dark:bg-sky-950/40 dark:text-sky-300 border border-sky-200 dark:border-sky-800/60 hover:bg-sky-100 dark:hover:bg-sky-900/50 transition-colors">
                <i class="fa-solid fa-user-clock"></i>
                <span>{{ $isAr ? 'بوابة الخدمة الذاتية' : 'Self-Service Hub' }}</span>
            </a>
        </div>
    </div>

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

    <!-- Table Card -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-start">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 text-slate-400 font-bold uppercase tracking-wider text-[10px]">
                        <th class="py-3.5 px-4 text-start">{{ $isAr ? 'الموظف' : 'Employee' }}</th>
                        <th class="py-3.5 px-4 text-start">{{ $isAr ? 'القسم / المسمى' : 'Department / Position' }}</th>
                        <th class="py-3.5 px-4 text-start">{{ $isAr ? 'نوع الإجازة' : 'Leave Type' }}</th>
                        <th class="py-3.5 px-4 text-start">{{ $isAr ? 'الفترة' : 'Dates' }}</th>
                        <th class="py-3.5 px-4 text-start">{{ $isAr ? 'المدة' : 'Duration' }}</th>
                        <th class="py-3.5 px-4 text-start">{{ $isAr ? 'السبب وملاحظات الموظف' : 'Reason' }}</th>
                        <th class="py-3.5 px-4 text-end">{{ $isAr ? 'القرار الإداري' : 'Decision' }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                    @forelse($pendingRequests as $req)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-pink-100 dark:bg-pink-950/60 text-pink-700 dark:text-pink-300 font-bold flex items-center justify-center text-xs flex-shrink-0">
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
                            <td class="py-3.5 px-4">
                                <div class="font-semibold text-slate-700 dark:text-slate-200">{{ $req->employee?->department?->name ?? '—' }}</div>
                                <div class="text-[10px] text-slate-400">{{ $req->employee?->position?->name ?? '—' }}</div>
                            </td>
                            <td class="py-3.5 px-4 font-bold text-slate-800 dark:text-white">
                                {{ $isAr ? ($req->leaveType?->name_ar ?? $req->leaveType?->name_en) : ($req->leaveType?->name_en ?? $req->leaveType?->name_ar) }}
                            </td>
                            <td class="py-3.5 px-4 font-mono text-slate-600 dark:text-slate-300 whitespace-nowrap">
                                {{ $req->start_date }} &rarr; {{ $req->end_date }}
                            </td>
                            <td class="py-3.5 px-4 font-mono font-bold text-sky-600 dark:text-sky-400">
                                {{ (float)$req->total_days }} {{ $isAr ? 'يوم' : 'days' }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-500 dark:text-slate-400 max-w-xs">
                                {{ $req->reason ?? '—' }}
                            </td>
                            <td class="py-3.5 px-4 text-end">
                                <div class="inline-flex items-center gap-2">
                                    <form action="{{ route('admin.hr.leave.requests.approve', $req->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl font-bold text-xs bg-emerald-600 hover:bg-emerald-500 text-white shadow-sm transition-all cursor-pointer">
                                            <i class="fa-solid fa-check"></i>
                                            <span>{{ $isAr ? 'موافقة' : 'Approve' }}</span>
                                        </button>
                                    </form>
                                    <button type="button" onclick="openApprovalRejectModal({{ $req->id }}, '{{ addslashes($req->employee?->full_name ?? '') }}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl font-bold text-xs bg-rose-600 hover:bg-rose-500 text-white shadow-sm transition-all cursor-pointer">
                                        <i class="fa-solid fa-xmark"></i>
                                        <span>{{ $isAr ? 'رفض' : 'Reject' }}</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-16 text-center text-slate-400">
                                <div class="w-14 h-14 rounded-full bg-emerald-50 dark:bg-emerald-950/40 text-emerald-500 flex items-center justify-center mx-auto mb-3 text-2xl">
                                    <i class="fa-solid fa-circle-check"></i>
                                </div>
                                <h4 class="text-sm font-bold text-slate-800 dark:text-white m-0">
                                    {{ $isAr ? 'لا توجد طلبات إجازة معلقة حالياً' : 'No Pending Leave Requests' }}
                                </h4>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 mb-0">
                                    {{ $isAr ? 'تم اتخاذ القرار في كافة طلبات إجازات الموظفين.' : 'All leave applications have been authorized or reviewed.' }}
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pendingRequests->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $pendingRequests->links() }}
            </div>
        @endif
    </div>

    <!-- Reject Reason Modal -->
    <div id="approvalsRejectModal" class="hidden fixed inset-0 z-[99999] items-center justify-center bg-slate-950/60 backdrop-blur-sm p-4">
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
                <button type="button" onclick="closeApprovalRejectModal()" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 flex items-center justify-center cursor-pointer">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form id="approvalRejectForm" method="POST" class="p-6 space-y-4">
                @csrf
                <p class="text-xs text-slate-600 dark:text-slate-300 m-0">
                    {{ $isAr ? 'هل أنت متأكد من رفض طلب الإجازة للموظف:' : 'Are you sure you want to reject the leave request for:' }}
                    <strong id="approvalRejectName" class="text-rose-600 dark:text-rose-400"></strong>؟
                </p>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                        {{ $isAr ? 'سبب الرفض (سيظهر للموظف)' : 'Rejection Reason (Visible to employee)' }}
                    </label>
                    <textarea name="rejection_reason" rows="3" required placeholder="{{ $isAr ? 'يرجى توضيح سبب رفض الطلب...' : 'Please specify the rejection reason...' }}" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:ring-2 focus:ring-rose-500"></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                    <button type="button" onclick="closeApprovalRejectModal()" class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition-colors cursor-pointer">
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
        function openApprovalRejectModal(requestId, empName) {
            const form = document.getElementById('approvalRejectForm');
            const nameEl = document.getElementById('approvalRejectName');
            const modal = document.getElementById('approvalsRejectModal');
            if (form && modal) {
                form.action = `{{ url('admin/hr/leave/requests') }}/${requestId}/reject`;
                if (nameEl) nameEl.textContent = empName;
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        }
        function closeApprovalRejectModal() {
            const modal = document.getElementById('approvalsRejectModal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }
    </script>
</x-layouts.admin>
