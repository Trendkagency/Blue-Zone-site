<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'حساب المخالصات ومكافأة نهاية الخدمة' : 'Final Settlements & Severance Clearance'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'احتساب مستحقات نهاية الخدمة، رصيد الإجازات، وتصفية السلف والقروض' : 'Compute end-of-service gratuity, unused leave encashment, deduction clearance, and final payout'"
>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0; color: var(--text-color, #1E293B);">
                <i class="fa-solid fa-calculator text-emerald-500 mr-2 ml-2"></i> 
                {{ app()->getLocale() === 'ar' ? 'المخالصات والتسويات النهائية' : 'End of Service Settlements' }}
            </h2>
        </div>
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <a href="{{ route('admin.hr.offboarding.resignations') }}" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-person-walking-dashed-line-arrow-right mr-1 ml-1 text-sky-500"></i> {{ app()->getLocale() === 'ar' ? 'الاستقالات' : 'Resignations' }}
            </a>
            <a href="{{ route('admin.hr.offboarding.terminations') }}" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-user-xmark mr-1 ml-1 text-red-500"></i> {{ app()->getLocale() === 'ar' ? 'إنهاء الخدمة' : 'Terminations' }}
            </a>
            <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('calculateModal').style.display='flex'">
                <i class="fa-solid fa-calculator text-xs mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'احتساب مخالصة جديدة' : 'Compute Settlement' }}
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
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الراتب المتبقي' : 'Unpaid Salary' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'بدل الإجازات' : 'Leave Encashment' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'مكافأة نهاية الخدمة' : 'Severance Pay' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الاستقطاعات والقروض' : 'Deductions' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'صافي المخالصة' : 'Net Settlement' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($settlements as $set)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.75rem 0.5rem;">
                                <a href="{{ route('admin.hr.employees.show', $set->employee_id) }}" style="color: #0284C7; font-weight: 700; text-decoration: none;">
                                    {{ $set->employee?->full_name ?? 'N/A' }}
                                </a>
                                <span style="font-size: 0.75rem; color: #64748B; display: block;">{{ $set->employee?->employee_number }}</span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; font-family: monospace; font-weight: 600;">
                                {{ number_format($set->remaining_salary ?? 0, 2) }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem; font-family: monospace; font-weight: 600; color: #0284C7;">
                                {{ number_format($set->unused_leave_amount ?? 0, 2) }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem; font-family: monospace; font-weight: 700; color: #059669;">
                                {{ number_format($set->end_of_service_award ?? 0, 2) }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem; font-family: monospace; font-weight: 600; color: #DC2626;">
                                -{{ number_format($set->total_deductions ?? 0, 2) }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <strong style="font-family: monospace; font-size: 1rem; color: #059669;">
                                    {{ number_format($set->net_amount ?? 0, 2) }} {{ function_exists('currency_code') ? currency_code() : 'SAR' }}
                                </strong>
                            </td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <span class="badge" style="background: #DCFCE7; color: #166534; padding: 0.2rem 0.5rem; border-radius: 999px; font-weight: 700;">
                                    {{ ucfirst($set->status ?? 'calculated') }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 3rem; color: #94A3B8;">
                                <i class="fa-solid fa-calculator" style="font-size: 2.5rem; margin-bottom: 0.75rem; display: block; opacity: 0.4;"></i>
                                {{ app()->getLocale() === 'ar' ? 'لا توجد مخالصات نهاية خدمة محسوبة حالياً.' : 'No final settlement dossiers generated yet.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 1rem;">
            {{ $settlements->links() }}
        </div>
    </div>

    <!-- Calculate Settlement Modal -->
    <div id="calculateModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
        <div style="background: #ffffff; border-radius: 0.75rem; padding: 1.5rem; width: 100%; max-width: 440px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="font-size: 1.1rem; font-weight: 700; margin: 0;">{{ app()->getLocale() === 'ar' ? 'احتساب تصفية ومخالصة نهائية' : 'Compute Final Settlement' }}</h3>
                <button type="button" onclick="document.getElementById('calculateModal').style.display='none'" style="background: none; border: none; font-size: 1.25rem; cursor: pointer;">&times;</button>
            </div>
            <form action="{{ route('admin.hr.offboarding.settlements.calculate') }}" method="POST">
                @csrf
                <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.25rem;">{{ app()->getLocale() === 'ar' ? 'ملف نهاية الخدمة / الموظف *' : 'Offboarding Record *' }}</label>
                        <select name="offboarding_id" required class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                            @foreach($offboardings as $off)
                                <option value="{{ $off->id }}">{{ $off->employee?->full_name }} ({{ ucfirst($off->offboarding_type) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <p style="font-size: 0.75rem; color: #64748B; margin: 0;">
                        {{ app()->getLocale() === 'ar' ? 'سيقوم النظام باحتساب مكافأة نهاية الخدمة، بدل الإجازات غير المستخدمة، واستقطاع أي متبقي من السلف أو القروض تلقائياً.' : 'The system will automatically audit remaining salary, unused leave balance days, gratuity entitlement, and deduct outstanding loan/advance balances.' }}
                    </p>
                    <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1rem;">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('calculateModal').style.display='none'">{{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}</button>
                        <button type="submit" class="btn btn-primary btn-sm">{{ app()->getLocale() === 'ar' ? 'بدء الحساب التلقائي' : 'Run Calculation' }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
