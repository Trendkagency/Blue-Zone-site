<?php

namespace App\Http\Controllers\Admin\Hr;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HrSettingController extends Controller
{
    public function index(): View
    {
        $settings = [
            // Approval Workflows
            'leave_approval_workflow' => Setting::get('hr_leave_approval_workflow', 'manager_then_hr'),
            'overtime_approval_workflow' => Setting::get('hr_overtime_approval_workflow', 'manager_then_hr'),
            'expense_advance_approval' => Setting::get('hr_expense_advance_approval', 'hr_only'),

            // Notifications
            'notify_contract_expiry_days' => Setting::get('hr_notify_contract_expiry_days', '30,15,7'),
            'notify_document_expiry_days' => Setting::get('hr_notify_document_expiry_days', '30,7'),
            'fcm_enabled_hr' => Setting::get('hr_fcm_enabled', true),

            // Leave Settings
            'annual_leave_default_days' => Setting::get('hr_annual_leave_default_days', 21),
            'sick_leave_default_days' => Setting::get('hr_sick_leave_default_days', 14),
            'probation_leave_allowed' => Setting::get('hr_probation_leave_allowed', false),

            // Attendance Settings
            'default_grace_period_mins' => Setting::get('hr_default_grace_period_mins', 15),
            'overtime_rate_multiplier' => Setting::get('hr_overtime_rate_multiplier', 1.5),

            // Payroll Settings
            'payroll_generation_day' => Setting::get('hr_payroll_generation_day', 25),
            'currency_code' => Setting::get('currency_code', 'SAR'),
        ];

        return view('admin.hr.settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $inputs = $request->except(['_token', '_method']);

        foreach ($inputs as $key => $value) {
            Setting::set("hr_{$key}", $value, 'hr');
        }

        return back()->with('success', __('hr.settings_saved', ['default' => 'HR system settings saved successfully.']));
    }
}
