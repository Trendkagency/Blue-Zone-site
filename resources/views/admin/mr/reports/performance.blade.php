<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'بطاقة أداء المناديب (Scorecard)' : 'Rep Performance Scorecard & KPIs'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'تقييم شامل لأداء مندوبي الدعاية الطبية، نسب التغطية، دقة الـ GPS، والنقاط المحققة' : 'Comprehensive cycle evaluation (§8) covering visit compliance, GPS accuracy, and target points.'"
    :breadcrumbs="[
        (app()->getLocale() === 'ar' ? 'نظام المندوب الطبي' : 'MR CRM') => route('admin.mr.live-map'),
        (app()->getLocale() === 'ar' ? 'بطاقة الأداء' : 'Rep Scorecard') => route('admin.mr.reports.performance')
    ]"
>
    <x-slot name="actions">
        <a href="{{ request()->fullUrlWithQuery(['recalculate' => 1]) }}" class="btn btn-secondary text-sm font-semibold">
            <i class="fa-solid fa-arrows-rotate mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'إعادة احتساب المؤشرات الآن' : 'Recalculate Snapshot' }}
        </a>
        <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="btn btn-primary text-sm font-bold shadow-sm">
            <i class="fa-solid fa-file-excel mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'تصدير (CSV / Excel)' : 'Export CSV / Excel' }}
        </a>
    </x-slot>

    <!-- Cycle Selector Bar -->
    <div class="card p-4 mb-6">
        <form method="GET" action="{{ route('admin.mr.reports.performance') }}" class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    {{ app()->getLocale() === 'ar' ? 'دورة التقييم:' : 'Evaluation Cycle:' }}
                </label>
                <select name="cycle_id" onchange="this.form.submit()" class="form-select text-sm py-1.5 px-3">
                    @foreach($cycles as $c)
                        <option value="{{ $c->id }}" {{ $selectedCycleId == $c->id ? 'selected' : '' }}>
                            {{ $c->name }} ({{ ucfirst($c->status) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="text-xs text-gray-400">
                {{ app()->getLocale() === 'ar' ? 'يتم تحديث اللقطة الإحصائية يومياً عند 23:00 وتحديثها فورياً بالضغط على زر إعادة الاحتساب' : 'Automated nightly KPI recalculation at 23:00 or instant manual refresh.' }}
            </div>
        </form>
    </div>

    <!-- Scorecard Table matching §8 Spec -->
    <div class="card p-0 overflow-hidden shadow-sm border border-gray-100 dark:border-gray-800 mb-8">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800/60 text-gray-600 dark:text-gray-300 font-semibold border-b border-gray-200 dark:border-gray-700 text-xs">
                    <tr>
                        <th class="px-5 py-3.5">{{ app()->getLocale() === 'ar' ? 'اسم المندوب' : 'Rep Name' }}</th>
                        <th class="px-5 py-3.5 text-center">{{ app()->getLocale() === 'ar' ? 'نسبة التغطية' : 'Coverage Rate %' }}</th>
                        <th class="px-5 py-3.5 text-center">{{ app()->getLocale() === 'ar' ? 'الزيارات المنفذة' : 'Visits Done' }}</th>
                        <th class="px-5 py-3.5 text-center">{{ app()->getLocale() === 'ar' ? 'الزيارات المخططة' : 'Planned Visits' }}</th>
                        <th class="px-5 py-3.5 text-center">{{ app()->getLocale() === 'ar' ? 'دقة الـ GPS' : 'Accuracy %' }}</th>
                        <th class="px-5 py-3.5 text-center">{{ app()->getLocale() === 'ar' ? 'الالتزام بالزيارات' : 'Visit Compliance %' }}</th>
                        <th class="px-5 py-3.5 text-center">{{ app()->getLocale() === 'ar' ? 'النقاط المستهدفة / المحققة' : 'Target / Achieved Points' }}</th>
                        <th class="px-5 py-3.5 text-center">{{ app()->getLocale() === 'ar' ? 'الأيام غير المسجلة' : 'Unreported Days' }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-xs">
                    @forelse($snapshots as $s)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition-colors">
                            <td class="px-5 py-4 font-bold text-gray-900 dark:text-white">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-sky-100 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300 flex items-center justify-center font-bold text-xs">
                                        <i class="fa-solid fa-user-doctor"></i>
                                    </div>
                                    <div>
                                        <div>{{ $s->representative?->name ?? 'Rep #' . $s->mr_id }}</div>
                                        <div class="text-[11px] text-gray-400">{{ $s->total_assigned_contacts }} assigned doctors</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Coverage Rate % -->
                            <td class="px-5 py-4 text-center font-bold">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs {{ $s->coverage_rate_pct >= 80 ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-400' : ($s->coverage_rate_pct >= 50 ? 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-400' : 'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-400') }}">
                                    {{ $s->coverage_rate_pct }}%
                                </span>
                            </td>

                            <!-- Visits Done -->
                            <td class="px-5 py-4 text-center font-bold text-emerald-600 dark:text-emerald-400">
                                {{ $s->visits_done }}
                            </td>

                            <!-- Planned Visits -->
                            <td class="px-5 py-4 text-center font-semibold text-gray-700 dark:text-gray-300">
                                {{ $s->planned_visits }}
                            </td>

                            <!-- GPS Accuracy % -->
                            <td class="px-5 py-4 text-center font-bold">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs {{ $s->gps_accuracy_pct >= 85 ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-400' : 'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-400' }}">
                                    {{ $s->gps_accuracy_pct }}%
                                </span>
                            </td>

                            <!-- Visit Compliance % -->
                            <td class="px-5 py-4 text-center font-bold">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs {{ $s->visit_compliance_pct >= 80 ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-400' : ($s->visit_compliance_pct >= 50 ? 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-400' : 'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-400') }}">
                                    {{ $s->visit_compliance_pct }}%
                                </span>
                            </td>

                            <!-- Target / Achieved Points -->
                            <td class="px-5 py-4 text-center">
                                <div class="font-bold text-sky-600 dark:text-sky-400">
                                    {{ $s->achieved_points }} / {{ $s->target_points }} pts
                                </div>
                                <div class="text-[10px] text-gray-400">({{ $s->points_achieved_pct }}% achieved)</div>
                            </td>

                            <!-- Unreported Days -->
                            <td class="px-5 py-4 text-center font-bold">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs {{ $s->unreported_days_count > 3 ? 'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-400' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300' }}">
                                    @if($s->unreported_days_count > 3)
                                        <i class="fa-solid fa-triangle-exclamation"></i>
                                    @endif
                                    {{ $s->unreported_days_count }} {{ app()->getLocale() === 'ar' ? 'أيام' : 'days' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center text-gray-400">
                                <i class="fa-solid fa-trophy text-4xl mb-2"></i>
                                <p class="text-sm font-semibold">{{ app()->getLocale() === 'ar' ? 'لا توجد بيانات أداء مسجلة لهذه الدورة بعد. اضغط على إعادة احتساب المؤشرات.' : 'No performance snapshots found for this cycle yet. Click Recalculate.' }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
