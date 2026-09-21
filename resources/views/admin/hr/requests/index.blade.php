<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'طلبات واستفسارات الموظفين' : 'Employee Requests Center'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'متابعة الطلبات الإدارية، الشهادات، تعديلات المواعيد، والاستجابة' : 'Manage employee administrative requests, document issuance, schedule changes, and approvals'"
>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0; color: var(--text-color, #1E293B);">
                <i class="fa-solid fa-clipboard-question text-sky-500 mr-2 ml-2"></i> 
                {{ app()->getLocale() === 'ar' ? 'طلبات الموظفين' : 'Workforce Requests' }}
            </h2>
        </div>
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <a href="{{ route('admin.hr.requests.complaints') }}" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-triangle-exclamation mr-1 ml-1 text-rose-500"></i> {{ app()->getLocale() === 'ar' ? 'الشكاوى' : 'Complaints' }}
            </a>
            <a href="{{ route('admin.hr.requests.suggestions') }}" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-lightbulb mr-1 ml-1 text-amber-500"></i> {{ app()->getLocale() === 'ar' ? 'المقترحات' : 'Suggestions' }}
            </a>
            <a href="{{ route('admin.hr.requests.transfers') }}" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-right-left mr-1 ml-1 text-indigo-500"></i> {{ app()->getLocale() === 'ar' ? 'النقل الإداري' : 'Transfers' }}
            </a>
            <a href="{{ route('admin.hr.requests.disciplinary') }}" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-gavel mr-1 ml-1 text-red-500"></i> {{ app()->getLocale() === 'ar' ? 'الجزاءات' : 'Disciplinary' }}
            </a>
            <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('newRequestModal').style.display='flex'">
                <i class="fa-solid fa-plus text-xs mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'تسجيل طلب جديد' : 'New Request' }}
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="padding: 0.75rem 1rem; margin-bottom: 1rem; border-radius: 0.5rem; background: #DCFCE7; color: #166534; border: 1px solid #BBF7D0;">
            <i class="fa-solid fa-circle-check mr-1 ml-1"></i> {{ session('success') }}
        </div>
    @endif

    <!-- Requests Table -->
    <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
        <div class="table-responsive">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                <thead>
                    <tr style="border-bottom: 2px solid #E2E8F0; text-align: left; color: #64748B;">
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الموظف' : 'Employee' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'نوع الطلب' : 'Type' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الموضوع والبيان' : 'Subject & Details' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                        <th style="padding: 0.75rem 0.5rem; text-align: right;">{{ app()->getLocale() === 'ar' ? 'القرار والإجراء' : 'Decision Action' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.75rem 0.5rem;">
                                <a href="{{ route('admin.hr.employees.show', $req->employee_id) }}" style="color: #0284C7; font-weight: 700; text-decoration: none;">
                                    {{ $req->employee?->full_name ?? 'N/A' }}
                                </a>
                                <span style="font-size: 0.75rem; color: #64748B; display: block;">{{ $req->employee?->department?->name }}</span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <span style="background: #F1F5F9; color: #334155; font-weight: 700; font-size: 0.75rem; padding: 0.2rem 0.5rem; border-radius: 999px;">
                                    {{ ucfirst(str_replace('_', ' ', $req->request_type)) }}
                                </span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; max-width: 320px;">
                                <strong style="color: #0F172A; display: block;">{{ $req->subject }}</strong>
                                <span style="font-size: 0.75rem; color: #64748B;">{{ \Illuminate\Support\Str::limit($req->description, 90) }}</span>
                                @if($req->admin_response)
                                    <div style="margin-top: 0.25rem; font-size: 0.75rem; color: #0284C7; background: #F0F9FF; padding: 0.2rem 0.4rem; border-radius: 0.25rem;">
                                        <strong>HR:</strong> {{ $req->admin_response }}
                                    </div>
                                @endif
                            </td>
                            <td style="padding: 0.75rem 0.5rem;">
                                @php
                                    $bg = match($req->status) {
                                        'approved', 'completed' => '#DCFCE7',
                                        'rejected' => '#FEE2E2',
                                        'in_progress' => '#E0F2FE',
                                        default => '#FEF3C7',
                                    };
                                    $col = match($req->status) {
                                        'approved', 'completed' => '#166534',
                                        'rejected' => '#991B1B',
                                        'in_progress' => '#0369A1',
                                        default => '#92400E',
                                    };
                                @endphp
                                <span class="badge" style="background: {{ $bg }}; color: {{ $col }}; padding: 0.2rem 0.5rem; border-radius: 999px; font-weight: 700;">
                                    {{ ucfirst($req->status) }}
                                </span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; text-align: right;">
                                @if($req->status === 'pending')
                                    <div style="display: inline-flex; gap: 0.25rem;">
                                        <form action="{{ route('admin.hr.requests.update-status', $req->id) }}" method="POST" style="display: inline-block;">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="btn btn-primary btn-xs" title="{{ app()->getLocale() === 'ar' ? 'موافقة' : 'Approve' }}">
                                                <i class="fa-solid fa-check"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.hr.requests.update-status', $req->id) }}" method="POST" style="display: inline-block;">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="btn btn-ghost btn-xs text-red-500" title="{{ app()->getLocale() === 'ar' ? 'رفض' : 'Reject' }}">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span style="font-size: 0.75rem; color: #64748B;">{{ app()->getLocale() === 'ar' ? 'تمت المعالجة' : 'Processed' }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 3rem; color: #94A3B8;">
                                <i class="fa-solid fa-clipboard-question" style="font-size: 2.5rem; margin-bottom: 0.75rem; display: block; opacity: 0.4;"></i>
                                {{ app()->getLocale() === 'ar' ? 'لا توجد طلبات معلقة حالياً.' : 'No pending employee requests.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 1rem;">
            {{ $requests->links() }}
        </div>
    </div>

    <!-- Submit Request Modal -->
    <div id="newRequestModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: #ffffff; border-radius: 0.75rem; padding: 1.5rem; width: 100%; max-width: 480px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0;">{{ app()->getLocale() === 'ar' ? 'تسجيل طلب إداري' : 'Register Employee Request' }}</h3>
                <button type="button" onclick="document.getElementById('newRequestModal').style.display='none'" style="background: none; border: none; font-size: 1.25rem; cursor: pointer;">&times;</button>
            </div>
            <form action="{{ route('admin.hr.requests.store') }}" method="POST">
                @csrf
                <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'الموظف *' : 'Employee *' }}</label>
                        <select name="employee_id" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->full_name }} ({{ $emp->employee_number }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'نوع الطلب *' : 'Request Type *' }}</label>
                        <select name="request_type" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                            <option value="hr_certificate">Salary / Employment Certificate</option>
                            <option value="schedule_change">Schedule / Shift Modification</option>
                            <option value="document_request">Official Document Retrieval</option>
                            <option value="allowance_request">Expense / Allowance Request</option>
                            <option value="general">General Administrative Request</option>
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'موضوع الطلب *' : 'Subject *' }}</label>
                        <input type="text" name="subject" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'تفاصيل الطلب *' : 'Details *' }}</label>
                        <textarea name="description" required rows="3" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;"></textarea>
                    </div>
                    <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1rem;">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('newRequestModal').style.display='none'">{{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}</button>
                        <button type="submit" class="btn btn-primary btn-sm">{{ app()->getLocale() === 'ar' ? 'إرسال الطلب' : 'Submit Request' }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
