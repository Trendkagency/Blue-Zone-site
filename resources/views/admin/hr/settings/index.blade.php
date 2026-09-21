<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'إعدادات وسياسات الموارد البشرية' : 'HR System Policies & Configuration'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'تخصيص مسارات الموافقات، تنبيهات العقود، ضوابط الحضور والرواتب' : 'Configure approval hierarchies, contract expiry thresholds, attendance grace period, and payroll cycles'"
>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0; color: var(--text-color, #1E293B);">
                <i class="fa-solid fa-gears text-indigo-500 mr-2 ml-2"></i> 
                {{ app()->getLocale() === 'ar' ? 'إعدادات وسياسات HR' : 'HR System Settings' }}
            </h2>
        </div>
        <div>
            <a href="{{ route('admin.hr.dashboard') }}" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-arrow-left mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'لوحة التحكم' : 'HR Dashboard' }}
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="padding: 0.75rem 1rem; margin-bottom: 1rem; border-radius: 0.5rem; background: #DCFCE7; color: #166534; border: 1px solid #BBF7D0;">
            <i class="fa-solid fa-circle-check mr-1 ml-1"></i> {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.hr.settings.update') }}" method="POST">
        @csrf
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <!-- 1. Approval Workflows -->
            <div class="card" style="padding: 1.5rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
                <h3 style="font-size: 1rem; font-weight: 700; color: #0F172A; margin: 0 0 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fa-solid fa-network-wired text-sky-500"></i> {{ app()->getLocale() === 'ar' ? 'مسارات سلاسل الاعتماد والموافقات' : 'Approval Workflows & Delegation' }}
                </h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem;">
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.35rem; color: #334155;">
                            {{ app()->getLocale() === 'ar' ? 'مسار اعتماد الإجازات' : 'Leave Approval Workflow' }}
                        </label>
                        <select name="leave_approval_workflow" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                            <option value="manager_then_hr" {{ $settings['leave_approval_workflow'] === 'manager_then_hr' ? 'selected' : '' }}>
                                {{ app()->getLocale() === 'ar' ? 'المدير المباشر ثم الموارد البشرية (موصى به)' : 'Direct Manager → HR Department (Recommended)' }}
                            </option>
                            <option value="hr_only" {{ $settings['leave_approval_workflow'] === 'hr_only' ? 'selected' : '' }}>
                                {{ app()->getLocale() === 'ar' ? 'الموارد البشرية مباشرة' : 'HR Department Direct Approval' }}
                            </option>
                            <option value="manager_hr_admin" {{ $settings['leave_approval_workflow'] === 'manager_hr_admin' ? 'selected' : '' }}>
                                {{ app()->getLocale() === 'ar' ? 'المدير ثم HR ثم الإدارة العليا' : 'Direct Manager → HR → General Admin' }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.35rem; color: #334155;">
                            {{ app()->getLocale() === 'ar' ? 'مسار اعتماد العمل الإضافي' : 'Overtime Approval Workflow' }}
                        </label>
                        <select name="overtime_approval_workflow" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                            <option value="manager_then_hr" {{ $settings['overtime_approval_workflow'] === 'manager_then_hr' ? 'selected' : '' }}>
                                {{ app()->getLocale() === 'ar' ? 'المدير المباشر ثم الموارد البشرية' : 'Direct Manager → HR' }}
                            </option>
                            <option value="hr_only" {{ $settings['overtime_approval_workflow'] === 'hr_only' ? 'selected' : '' }}>
                                {{ app()->getLocale() === 'ar' ? 'الموارد البشرية فقط' : 'HR Department Direct' }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.35rem; color: #334155;">
                            {{ app()->getLocale() === 'ar' ? 'مسار اعتماد السلف والتمويل' : 'Advance & Loan Approval' }}
                        </label>
                        <select name="expense_advance_approval" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                            <option value="hr_only" {{ $settings['expense_advance_approval'] === 'hr_only' ? 'selected' : '' }}>
                                {{ app()->getLocale() === 'ar' ? 'إدارة الموارد البشرية والمالية' : 'HR & Finance Authorized Review' }}
                            </option>
                            <option value="manager_then_hr" {{ $settings['expense_advance_approval'] === 'manager_then_hr' ? 'selected' : '' }}>
                                {{ app()->getLocale() === 'ar' ? 'المدير المباشر ثم HR' : 'Manager Endorsement → HR Approval' }}
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- 2. Alerts & Notifications -->
            <div class="card" style="padding: 1.5rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
                <h3 style="font-size: 1rem; font-weight: 700; color: #0F172A; margin: 0 0 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fa-solid fa-bell text-amber-500"></i> {{ app()->getLocale() === 'ar' ? 'التنبيهات والإشعارات الاستباقية' : 'Proactive Expiration & FCM Alerts' }}
                </h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem;">
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.35rem; color: #334155;">
                            {{ app()->getLocale() === 'ar' ? 'تنبيه انتهاء العقود قبل (أيام مفصولة بفواصل)' : 'Contract Expiry Notice Days (Comma Separated)' }}
                        </label>
                        <input type="text" name="notify_contract_expiry_days" value="{{ $settings['notify_contract_expiry_days'] }}" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        <span style="font-size: 0.75rem; color: #64748B;">Default: 90, 60, 30, 7 days</span>
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.35rem; color: #334155;">
                            {{ app()->getLocale() === 'ar' ? 'تنبيه انتهاء المستندات والهويات قبل (أيام)' : 'Document Expiry Notice Days' }}
                        </label>
                        <input type="text" name="notify_document_expiry_days" value="{{ $settings['notify_document_expiry_days'] }}" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        <span style="font-size: 0.75rem; color: #64748B;">National ID, Passport, Medical License, etc.</span>
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.35rem; color: #334155;">
                            {{ app()->getLocale() === 'ar' ? 'تفعيل إشعارات FCM الفورية للموارد البشرية' : 'Enable Firebase FCM Push Notifications' }}
                        </label>
                        <select name="fcm_enabled_hr" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                            <option value="1" {{ $settings['fcm_enabled_hr'] ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'مفعل (نظام Firebase FCM نشط)' : 'Enabled (Queued FCM)' }}</option>
                            <option value="0" {{ !$settings['fcm_enabled_hr'] ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'معطل' : 'Disabled' }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- 3. Attendance & Leave Rules -->
            <div class="card" style="padding: 1.5rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
                <h3 style="font-size: 1rem; font-weight: 700; color: #0F172A; margin: 0 0 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fa-solid fa-business-time text-emerald-500"></i> {{ app()->getLocale() === 'ar' ? 'سياسات الحضور والإجازات' : 'Attendance & Leave Policies' }}
                </h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem;">
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.35rem; color: #334155;">
                            {{ app()->getLocale() === 'ar' ? 'فترة السماح الافتراضية للتأخير (دقائق)' : 'Grace Period for Late Check-in (Mins)' }}
                        </label>
                        <input type="number" name="default_grace_period_mins" value="{{ $settings['default_grace_period_mins'] }}" min="0" max="60" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.35rem; color: #334155;">
                            {{ app()->getLocale() === 'ar' ? 'معامل احتساب أجر العمل الإضافي (ساعة)' : 'Overtime Hourly Multiplier' }}
                        </label>
                        <input type="number" step="0.1" name="overtime_rate_multiplier" value="{{ $settings['overtime_rate_multiplier'] }}" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        <span style="font-size: 0.75rem; color: #64748B;">1.5x standard hourly rate</span>
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.35rem; color: #334155;">
                            {{ app()->getLocale() === 'ar' ? 'رصيد الإجازة السنوية الافتراضي (أيام)' : 'Default Annual Leave Quota (Days)' }}
                        </label>
                        <input type="number" name="annual_leave_default_days" value="{{ $settings['annual_leave_default_days'] }}" min="1" max="60" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                    </div>
                </div>
            </div>

            <!-- 4. Payroll Automation -->
            <div class="card" style="padding: 1.5rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
                <h3 style="font-size: 1rem; font-weight: 700; color: #0F172A; margin: 0 0 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fa-solid fa-money-check-dollar text-emerald-500"></i> {{ app()->getLocale() === 'ar' ? 'ضوابط صرف ومسيرات الرواتب' : 'Payroll Generation & Currency' }}
                </h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem;">
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.35rem; color: #334155;">
                            {{ app()->getLocale() === 'ar' ? 'يوم إصدار مسيرات الرواتب من كل شهر' : 'Monthly Payroll Generation Day' }}
                        </label>
                        <input type="number" name="payroll_generation_day" value="{{ $settings['payroll_generation_day'] }}" min="1" max="31" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                        <span style="font-size: 0.75rem; color: #64748B;">e.g. Day 25 or 28 of each calendar month</span>
                    </div>
                    <div>
                        <label style="font-size: 0.8rem; font-weight: 600; display: block; margin-bottom: 0.35rem; color: #334155;">
                            {{ app()->getLocale() === 'ar' ? 'عملة النظام المعتمدة' : 'System Default Currency' }}
                        </label>
                        <input type="text" name="currency_code" value="{{ $settings['currency_code'] ?? 'SAR' }}" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid #CBD5E1; border-radius: 0.375rem;">
                    </div>
                </div>
            </div>

            <!-- Save Action Button -->
            <div style="display: flex; justify-content: flex-end; gap: 1rem;">
                <button type="submit" class="btn btn-primary" style="padding: 0.65rem 2rem; font-weight: 700;">
                    <i class="fa-solid fa-floppy-disk mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'حفظ إعدادات الموارد البشرية' : 'Save System Settings' }}
                </button>
            </div>
        </div>
    </form>
</x-layouts.admin>
