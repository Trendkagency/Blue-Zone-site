<x-layouts.admin 
    :pageTitle="(app()->getLocale() === 'ar' ? 'قسيمة راتب - ' : 'Payslip - ') . ($payslip->employee?->full_name ?? 'Employee')" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'بيان مفصل بمفردات الراتب والبدلات والاستقطاعات الشهرية' : 'Itemized employee monthly compensation and statutory deductions slip'"
>
    <!-- Top Actions -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <a href="{{ route('admin.hr.payroll.payslips') }}" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-arrow-left mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'العودة لقائمة القسائم' : 'Back to Payslips' }}
            </a>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <button type="button" class="btn btn-primary btn-sm" onclick="window.print()">
                <i class="fa-solid fa-print mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'طباعة القسيمة (PDF)' : 'Print Payslip (PDF)' }}
            </button>
        </div>
    </div>

    <!-- Payslip Document Container (Designed for both Web & Print) -->
    <div class="card" style="max-width: 860px; margin: 0 auto; padding: 2.5rem; border-radius: 0.75rem; background: #ffffff; border: 1px solid #CBD5E1; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
        <!-- Company & Document Header -->
        <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #0284C7; padding-bottom: 1.5rem; margin-bottom: 2rem;">
            <div>
                <h1 style="font-size: 1.5rem; font-weight: 800; color: #0284C7; margin: 0;">BLUE ZONE BIOCEUTICALS</h1>
                <p style="color: #64748B; font-size: 0.85rem; margin: 0.25rem 0 0;">Human Resources & Payroll Administration</p>
                <p style="color: #94A3B8; font-size: 0.75rem; margin: 0;">Management Platform &bull; Official Employee Record</p>
            </div>
            <div style="text-align: right;">
                <span style="font-size: 1.1rem; font-weight: 800; color: #0F172A; text-transform: uppercase; display: block;">PAYSLIP / قسيمة راتب</span>
                <span style="font-family: monospace; font-size: 0.85rem; color: #64748B; display: block;">{{ $payslip->payslip_number ?? 'SLIP-'.str_pad($payslip->id, 5, '0', STR_PAD_LEFT) }}</span>
                <span style="font-size: 0.8rem; color: #0284C7; font-weight: 700;">{{ $payslip->period?->name ?? 'Period' }}</span>
            </div>
        </div>

        <!-- Employee & Period Summary Details -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; background: #F8FAFC; padding: 1.25rem; border-radius: 0.5rem; margin-bottom: 2rem; border: 1px solid #E2E8F0; font-size: 0.85rem;">
            <div>
                <div style="margin-bottom: 0.5rem;">
                    <span style="color: #64748B; font-size: 0.75rem; display: block;">EMPLOYEE NAME / اسم الموظف</span>
                    <strong style="color: #0F172A; font-size: 1rem;">{{ $payslip->employee?->full_name ?? 'N/A' }}</strong>
                </div>
                <div style="margin-bottom: 0.5rem;">
                    <span style="color: #64748B; font-size: 0.75rem; display: block;">EMPLOYEE ID / الرقم الوظيفي</span>
                    <strong style="font-family: monospace; color: #0284C7;">{{ $payslip->employee?->employee_number }}</strong>
                </div>
                <div>
                    <span style="color: #64748B; font-size: 0.75rem; display: block;">DEPARTMENT &bull; POSITION</span>
                    <span style="color: #334155; font-weight: 600;">
                        {{ $payslip->employee?->department?->name ?? 'Dept' }} &bull; {{ $payslip->employee?->position?->name ?? 'Position' }}
                    </span>
                </div>
            </div>
            <div>
                <div style="margin-bottom: 0.5rem;">
                    <span style="color: #64748B; font-size: 0.75rem; display: block;">PAYROLL PERIOD / دورة الراتب</span>
                    <strong style="color: #334155;">{{ $payslip->period?->start_date }} &ndash; {{ $payslip->period?->end_date }}</strong>
                </div>
                <div style="margin-bottom: 0.5rem;">
                    <span style="color: #64748B; font-size: 0.75rem; display: block;">DISBURSEMENT DATE / تاريخ الصرف</span>
                    <strong style="color: #334155;">{{ $payslip->period?->payment_date ?? now()->toDateString() }}</strong>
                </div>
                <div>
                    <span style="color: #64748B; font-size: 0.75rem; display: block;">BANK ACCOUNT / رقم الحساب البنكي</span>
                    <span style="font-family: monospace; color: #334155;">
                        {{ $payslip->employee?->bank_name ? $payslip->employee->bank_name . ' - ' : '' }}
                        {{ $payslip->employee?->bank_account_number ? '••••' . substr($payslip->employee->bank_account_number, -4) : 'Direct Transfer' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Breakdown Grid (Earnings vs Deductions) -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
            <!-- Earnings -->
            <div>
                <h3 style="font-size: 0.95rem; font-weight: 700; color: #059669; border-bottom: 2px solid #A7F3D0; padding-bottom: 0.5rem; margin-top: 0;">
                    <i class="fa-solid fa-plus-circle mr-1 ml-1"></i> EARNINGS / المستحقات والبدلات
                </h3>
                <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                    <tbody>
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.5rem 0; color: #475569;">Basic Salary / الراتب الأساسي</td>
                            <td style="padding: 0.5rem 0; text-align: right; font-family: monospace; font-weight: 600;">
                                {{ number_format($payslip->basic_salary, 2) }}
                            </td>
                        </tr>
                        @php
                            $components = is_string($payslip->earnings_breakdown) ? json_decode($payslip->earnings_breakdown, true) : (is_array($payslip->earnings_breakdown) ? $payslip->earnings_breakdown : []);
                        @endphp
                        @if(!empty($components))
                            @foreach($components as $compName => $compAmount)
                                <tr style="border-bottom: 1px solid #F1F5F9;">
                                    <td style="padding: 0.5rem 0; color: #475569;">{{ ucfirst(str_replace('_', ' ', $compName)) }}</td>
                                    <td style="padding: 0.5rem 0; text-align: right; font-family: monospace; font-weight: 600;">
                                        {{ number_format((float)$compAmount, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr style="border-bottom: 1px solid #F1F5F9;">
                                <td style="padding: 0.5rem 0; color: #475569;">Housing & Transport Allowances</td>
                                <td style="padding: 0.5rem 0; text-align: right; font-family: monospace; font-weight: 600;">
                                    {{ number_format(max(0, $payslip->gross_salary - $payslip->basic_salary), 2) }}
                                </td>
                            </tr>
                        @endif
                    </tbody>
                    <tfoot>
                        <tr style="font-weight: 700; color: #059669; border-top: 2px solid #E2E8F0;">
                            <td style="padding: 0.75rem 0;">GROSS EARNINGS / الإجمالي</td>
                            <td style="padding: 0.75rem 0; text-align: right; font-family: monospace; font-size: 1rem;">
                                {{ number_format($payslip->gross_salary, 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Deductions -->
            <div>
                <h3 style="font-size: 0.95rem; font-weight: 700; color: #DC2626; border-bottom: 2px solid #FECACA; padding-bottom: 0.5rem; margin-top: 0;">
                    <i class="fa-solid fa-minus-circle mr-1 ml-1"></i> DEDUCTIONS / الاستقطاعات والخصومات
                </h3>
                <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                    <tbody>
                        @php
                            $deductions = is_string($payslip->deductions_breakdown) ? json_decode($payslip->deductions_breakdown, true) : (is_array($payslip->deductions_breakdown) ? $payslip->deductions_breakdown : []);
                        @endphp
                        @if(!empty($deductions))
                            @foreach($deductions as $dName => $dAmount)
                                <tr style="border-bottom: 1px solid #F1F5F9;">
                                    <td style="padding: 0.5rem 0; color: #475569;">{{ ucfirst(str_replace('_', ' ', $dName)) }}</td>
                                    <td style="padding: 0.5rem 0; text-align: right; font-family: monospace; font-weight: 600; color: #DC2626;">
                                        -{{ number_format((float)$dAmount, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr style="border-bottom: 1px solid #F1F5F9;">
                                <td style="padding: 0.5rem 0; color: #475569;">Social Insurance / Lateness / Advances</td>
                                <td style="padding: 0.5rem 0; text-align: right; font-family: monospace; font-weight: 600; color: #DC2626;">
                                    -{{ number_format($payslip->total_deductions, 2) }}
                                </td>
                            </tr>
                        @endif
                    </tbody>
                    <tfoot>
                        <tr style="font-weight: 700; color: #DC2626; border-top: 2px solid #E2E8F0;">
                            <td style="padding: 0.75rem 0;">TOTAL DEDUCTIONS / المستقطع</td>
                            <td style="padding: 0.75rem 0; text-align: right; font-family: monospace; font-size: 1rem;">
                                -{{ number_format($payslip->total_deductions, 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Net Payable Hero Banner -->
        <div style="background: linear-gradient(135deg, #0284C7, #0369A1); color: #ffffff; padding: 1.5rem; border-radius: 0.5rem; display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <span style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; opacity: 0.9; display: block;">NET PAYABLE AMOUNT / صافي الراتب المستحق</span>
                <span style="font-size: 0.8rem; opacity: 0.8;">Disbursed directly via Blue Zone payroll gateway</span>
            </div>
            <div style="font-size: 2rem; font-weight: 900; font-family: monospace;">
                {{ number_format($payslip->net_salary, 2) }} <span style="font-size: 1rem; font-weight: 600;">{{ function_exists('currency_code') ? currency_code() : 'SAR' }}</span>
            </div>
        </div>

        <!-- Signatures & Verification -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-top: 3rem; padding-top: 1.5rem; border-top: 1px dashed #CBD5E1; font-size: 0.8rem; color: #64748B;">
            <div>
                <p style="margin: 0 0 2.5rem;"><strong>Prepared by:</strong> Blue Zone HR & Payroll Dept</p>
                <div style="border-top: 1px solid #94A3B8; width: 180px; padding-top: 0.25rem;">Authorized Signature</div>
            </div>
            <div style="text-align: right;">
                <p style="margin: 0 0 2.5rem;"><strong>Employee Confirmation:</strong> {{ $payslip->employee?->full_name }}</p>
                <div style="border-top: 1px solid #94A3B8; width: 180px; margin-left: auto; padding-top: 0.25rem;">Employee Signature</div>
            </div>
        </div>
    </div>
</x-layouts.admin>
