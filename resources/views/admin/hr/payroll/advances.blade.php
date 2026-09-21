<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'سلف رواتب الموظفين' : 'Salary Advances Ledger'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'إدارة ومتابعة سلف الرواتب وجدولة الاستقطاعات الشهرية' : 'Manage employee short-term salary advances and scheduled payroll deductions'"
>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0; color: var(--text-color, #1E293B);">
                <i class="fa-solid fa-hand-holding-dollar text-amber-500 mr-2 ml-2"></i> 
                {{ app()->getLocale() === 'ar' ? 'سلف الرواتب' : 'Salary Advances' }}
            </h2>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('admin.hr.payroll.index') }}" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-arrow-left mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'سجل الرواتب' : 'Payroll' }}
            </a>
            <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('newAdvanceModal').style.display='flex'">
                <i class="fa-solid fa-plus text-xs mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'منح سلفة جديدة' : 'Issue Salary Advance' }}
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="padding: 0.75rem 1rem; margin-bottom: 1rem; border-radius: 0.5rem; background: #DCFCE7; color: #166534; border: 1px solid #BBF7D0;">
            <i class="fa-solid fa-circle-check mr-1 ml-1"></i> {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger" style="padding: 0.75rem 1rem; margin-bottom: 1rem; border-radius: 0.5rem; background: #FEE2E2; color: #991B1B; border: 1px solid #FECACA;">
            <ul style="margin: 0; padding-left: 1.25rem;">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
        <div class="table-responsive">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                <thead>
                    <tr style="border-bottom: 2px solid #E2E8F0; text-align: left; color: #64748B;">
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الموظف' : 'Employee' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'مبلغ السلفة' : 'Advance Amount' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الأقساط' : 'Installments' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'القسط الشهري' : 'Monthly Rate' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'المتبقي' : 'Remaining' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'تاريخ الطلب' : 'Request Date' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($advances as $adv)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.75rem 0.5rem;">
                                <a href="{{ route('admin.hr.employees.show', $adv->employee_id) }}" style="color: #0284C7; font-weight: 700; text-decoration: none;">
                                    {{ $adv->employee?->full_name ?? 'N/A' }}
                                </a>
                                <span style="font-size: 0.75rem; color: #64748B; display: block;">{{ $adv->employee?->employee_number }}</span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; font-family: monospace; font-weight: 700; color: #D97706;">
                                {{ number_format($adv->amount, 2) }} {{ function_exists('currency_code') ? currency_code() : 'SAR' }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem; font-weight: 600;">{{ $adv->installments }} {{ app()->getLocale() === 'ar' ? 'أشهر' : 'months' }}</td>
                            <td style="padding: 0.75rem 0.5rem; font-family: monospace; font-weight: 600;">
                                {{ number_format($adv->installment_amount, 2) }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem; font-family: monospace; font-weight: 700; color: {{ $adv->remaining_amount > 0 ? '#DC2626' : '#059669' }};">
                                {{ number_format($adv->remaining_amount, 2) }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem; color: #334155;">{{ $adv->request_date }}</td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <span class="badge" style="background: {{ $adv->status === 'approved' ? '#DCFCE7' : '#FEF3C7' }}; color: {{ $adv->status === 'approved' ? '#166534' : '#92400E' }}; padding: 0.2rem 0.5rem; border-radius: 999px; font-weight: 700;">
                                    {{ ucfirst($adv->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 3rem; color: #94A3B8;">
                                <i class="fa-solid fa-hand-holding-dollar" style="font-size: 2.5rem; margin-bottom: 0.75rem; display: block; opacity: 0.4;"></i>
                                {{ app()->getLocale() === 'ar' ? 'لا توجد سلف رواتب مسجلة حالياً.' : 'No salary advances active.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 1rem;">
            {{ $advances->links() }}
        </div>
    </div>

    <!-- Issue Advance Modal -->
    <div id="newAdvanceModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: #ffffff; border-radius: 0.75rem; padding: 1.5rem; width: 100%; max-width: 480px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0; color: #0F172A;">
                    <i class="fa-solid fa-hand-holding-dollar text-amber-500 mr-2"></i> {{ app()->getLocale() === 'ar' ? 'منح سلفة راتب' : 'Issue Salary Advance' }}
                </h3>
                <button type="button" onclick="document.getElementById('newAdvanceModal').style.display='none'" style="background: none; border: none; font-size: 1.25rem; cursor: pointer;">&times;</button>
            </div>
            <form action="{{ route('admin.hr.payroll.advances.store') }}" method="POST">
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
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'مبلغ السلفة *' : 'Advance Amount *' }}</label>
                            <input type="number" step="0.01" name="amount" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        </div>
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'عدد الأقساط الشهرية *' : 'Installments *' }}</label>
                            <input type="number" name="installments" value="1" min="1" max="12" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'تاريخ الطلب *' : 'Request Date *' }}</label>
                            <input type="date" name="request_date" required value="{{ now()->toDateString() }}" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        </div>
                        <div>
                            <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'بداية الاستقطاع *' : 'Repayment Start *' }}</label>
                            <input type="date" name="repayment_start_date" required value="{{ now()->startOfMonth()->toDateString() }}" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        </div>
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'ملاحظات وسبب السلفة' : 'Reason / Notes' }}</label>
                        <textarea name="reason" rows="2" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;"></textarea>
                    </div>
                    <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1rem;">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('newAdvanceModal').style.display='none'">{{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}</button>
                        <button type="submit" class="btn btn-primary btn-sm">{{ app()->getLocale() === 'ar' ? 'اعتماد وصرف' : 'Approve & Issue' }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
