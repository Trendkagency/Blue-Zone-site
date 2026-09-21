<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'هيكل وبنود الرواتب والبدلات' : 'Salary Structures & Components'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'تحديد هياكل الرواتب وتصنيف بنود الاستحقاقات والاستقطاعات' : 'Define organizational compensation frameworks, allowances, and statutory deduction policies'"
>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0; color: var(--text-color, #1E293B);">
                <i class="fa-solid fa-sliders text-sky-500 mr-2 ml-2"></i> 
                {{ app()->getLocale() === 'ar' ? 'هيكل الرواتب والمزايا' : 'Salary Structure Architecture' }}
            </h2>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('admin.hr.payroll.index') }}" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-arrow-left mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'سجل الرواتب' : 'Payroll Ledger' }}
            </a>
        </div>
    </div>

    <!-- Structures & Components Dual Grid -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; align-items: flex-start;">
        <!-- Salary Structures -->
        <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="font-size: 1rem; font-weight: 700; margin: 0; color: #0F172A;">
                    <i class="fa-solid fa-layer-group text-sky-500 mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'نماذج هياكل الرواتب' : 'Salary Structures' }}
                </h3>
            </div>
            <div class="table-responsive">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                    <thead>
                        <tr style="border-bottom: 2px solid #E2E8F0; text-align: left; color: #64748B;">
                            <th style="padding: 0.5rem;">{{ app()->getLocale() === 'ar' ? 'اسم الهيكل' : 'Structure Name' }}</th>
                            <th style="padding: 0.5rem;">{{ app()->getLocale() === 'ar' ? 'البنود' : 'Components' }}</th>
                            <th style="padding: 0.5rem;">{{ app()->getLocale() === 'ar' ? 'الرمز' : 'Code' }}</th>
                            <th style="padding: 0.5rem; text-align: right;">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($structures as $struct)
                            <tr style="border-bottom: 1px solid #F1F5F9;">
                                <td style="padding: 0.6rem 0.5rem; font-weight: 700; color: #0F172A;">
                                    {{ $struct->name }}
                                </td>
                                <td style="padding: 0.6rem 0.5rem;">
                                    <span style="background: #E0F2FE; color: #0369A1; font-weight: 700; font-size: 0.75rem; padding: 0.15rem 0.5rem; border-radius: 999px;">
                                        {{ $struct->components_count ?? 0 }} items
                                    </span>
                                </td>
                                <td style="padding: 0.6rem 0.5rem; font-family: monospace; color: #64748B;">
                                    {{ $struct->code ?? 'STD' }}
                                </td>
                                <td style="padding: 0.6rem 0.5rem; text-align: right;">
                                    <span class="badge" style="background: #DCFCE7; color: #166534; padding: 0.15rem 0.45rem; border-radius: 999px; font-size: 0.75rem; font-weight: 700;">
                                        Active
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 2rem; color: #94A3B8;">
                                    Standard Corporate Structure (Default)
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Components Breakdown -->
        <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="font-size: 1rem; font-weight: 700; margin: 0; color: #0F172A;">
                    <i class="fa-solid fa-list-check text-indigo-500 mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'بنود الراتب المعتمدة' : 'Salary Components' }}
                </h3>
            </div>
            <div class="table-responsive">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                    <thead>
                        <tr style="border-bottom: 2px solid #E2E8F0; text-align: left; color: #64748B;">
                            <th style="padding: 0.5rem;">{{ app()->getLocale() === 'ar' ? 'البند' : 'Component' }}</th>
                            <th style="padding: 0.5rem;">{{ app()->getLocale() === 'ar' ? 'النوع' : 'Type' }}</th>
                            <th style="padding: 0.5rem;">{{ app()->getLocale() === 'ar' ? 'طريقة الاحتساب' : 'Method' }}</th>
                            <th style="padding: 0.5rem; text-align: right;">{{ app()->getLocale() === 'ar' ? 'الخضوع للضريبة' : 'Taxable' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($components as $comp)
                            <tr style="border-bottom: 1px solid #F1F5F9;">
                                <td style="padding: 0.6rem 0.5rem; font-weight: 600; color: #0F172A;">
                                    {{ $comp->name }}
                                </td>
                                <td style="padding: 0.6rem 0.5rem;">
                                    <span class="badge" style="background: {{ $comp->type === 'earning' ? '#DCFCE7' : '#FEE2E2' }}; color: {{ $comp->type === 'earning' ? '#166534' : '#991B1B' }}; padding: 0.15rem 0.45rem; border-radius: 999px; font-weight: 700; font-size: 0.75rem;">
                                        {{ ucfirst($comp->type) }}
                                    </span>
                                </td>
                                <td style="padding: 0.6rem 0.5rem; color: #64748B;">
                                    {{ ucfirst($comp->calculation_type ?? 'fixed') }}
                                </td>
                                <td style="padding: 0.6rem 0.5rem; text-align: right; color: #64748B;">
                                    <i class="fa-solid {{ $comp->is_taxable ? 'fa-check text-emerald-500' : 'fa-minus text-slate-300' }}"></i>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 2rem; color: #94A3B8;">
                                    Base Salary, Housing, Transportation, Lateness, Social Insurance
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.admin>
