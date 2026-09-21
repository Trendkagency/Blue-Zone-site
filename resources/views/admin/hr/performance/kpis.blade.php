<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'مؤشرات الأداء الرئيسية (KPIs)' : 'Key Performance Indicators (KPIs)'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'مصفوفة مؤشرات قياس الأداء للأقسام والفرق الطبية والتسويقية' : 'Organizational KPI metrics, target benchmarks, and departmental scorecards'"
>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0; color: var(--text-color, #1E293B);">
                <i class="fa-solid fa-gauge text-sky-500 mr-2 ml-2"></i> 
                {{ app()->getLocale() === 'ar' ? 'مؤشرات الأداء (KPIs)' : 'KPI Scorecards' }}
            </h2>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('admin.hr.performance.reviews') }}" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-arrow-left mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'التقييمات' : 'Reviews' }}
            </a>
            <a href="{{ route('admin.hr.performance.promotions') }}" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-arrow-trend-up mr-1 ml-1 text-xs"></i> {{ app()->getLocale() === 'ar' ? 'الترقيات' : 'Promotions' }}
            </a>
        </div>
    </div>

    <div class="card" style="padding: 1.25rem; border-radius: 0.75rem; background: var(--card-bg, #ffffff); border: 1px solid var(--border-color, #E2E8F0);">
        <div class="table-responsive">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                <thead>
                    <tr style="border-bottom: 2px solid #E2E8F0; text-align: left; color: #64748B;">
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'اسم المؤشر' : 'Indicator' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'القسم المستهدف' : 'Department' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'طريقة القياس' : 'Measurement Unit' }}</th>
                        <th style="padding: 0.75rem 0.5rem;">{{ app()->getLocale() === 'ar' ? 'المستهدف المعياري' : 'Standard Benchmark' }}</th>
                        <th style="padding: 0.75rem 0.5rem; text-align: right;">{{ app()->getLocale() === 'ar' ? 'الوزن النسبي' : 'Weight' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kpis as $kpi)
                        <tr style="border-bottom: 1px solid #F1F5F9;">
                            <td style="padding: 0.75rem 0.5rem; font-weight: 700; color: #0F172A;">
                                {{ $kpi->name }}
                                <span style="font-size: 0.75rem; color: #64748B; display: block; font-weight: normal;">{{ $kpi->description }}</span>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; color: #334155; font-weight: 600;">
                                {{ $kpi->department?->name ?? 'All Departments' }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem; color: #64748B;">
                                {{ $kpi->unit ?? 'Percentage (%)' }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem; font-family: monospace; font-weight: 700; color: #0284C7;">
                                {{ $kpi->target_value ?? '100' }}
                            </td>
                            <td style="padding: 0.75rem 0.5rem; text-align: right; font-weight: 700; color: #059669;">
                                {{ $kpi->weight ?? '20' }}%
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 3rem; color: #94A3B8;">
                                <i class="fa-solid fa-gauge" style="font-size: 2.5rem; margin-bottom: 0.75rem; display: block; opacity: 0.4;"></i>
                                {{ app()->getLocale() === 'ar' ? 'لا توجد مؤشرات أداء مسجلة حتى الآن.' : 'Standard organization metrics are configured across departments.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top: 1rem;">
            {{ $kpis->links() }}
        </div>
    </div>
</x-layouts.admin>
