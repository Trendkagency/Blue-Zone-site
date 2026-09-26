<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'تقرير التغطية والأطباء غير المزارين' : 'Doctor Coverage & Unvisited Report'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'متابعة شمولية التغطية الميدانية، الأطباء المتأخرين عن التردد الإلزامي، والأطباء غير المزارين' : 'Auto-generated portfolio compliance report (§8) with exact point and frequency audit.'"
    :breadcrumbs="[
        (app()->getLocale() === 'ar' ? 'نظام المندوب الطبي' : 'MR CRM') => route('admin.mr.live-map'),
        (app()->getLocale() === 'ar' ? 'تقرير التغطية' : 'Coverage Report') => route('admin.mr.reports.coverage')
    ]"
>
    <x-slot name="actions">
        <div class="flex items-center gap-2">
            <a href="{{ request()->fullUrlWithQuery(['export' => 'xlsx']) }}" class="btn btn-primary text-sm font-bold shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-file-excel text-emerald-300"></i>
                <span>{{ app()->getLocale() === 'ar' ? 'تصدير إكسيل احترافي (Excel .xlsx)' : 'Export Excel (.xlsx)' }}</span>
            </a>
            <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="btn btn-secondary text-sm font-medium shadow-sm flex items-center gap-1.5" title="{{ app()->getLocale() === 'ar' ? 'تصدير بصيغة CSV' : 'Export as raw CSV' }}">
                <i class="fa-solid fa-file-csv text-gray-400"></i>
                <span class="hidden sm:inline">CSV</span>
            </a>
        </div>
    </x-slot>

    <!-- Filter Card -->
    <div class="card p-4 mb-6">
        <form method="GET" action="{{ route('admin.mr.reports.coverage') }}" class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">
                        {{ app()->getLocale() === 'ar' ? 'دورة الزيارة' : 'Visit Cycle' }}
                    </label>
                    <select name="cycle_id" onchange="this.form.submit()" class="form-select text-sm py-1.5 px-3">
                        @foreach($cycles as $c)
                            <option value="{{ $c->id }}" {{ $selectedCycleId == $c->id ? 'selected' : '' }}>
                                {{ $c->name }} ({{ ucfirst($c->status) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">
                        {{ app()->getLocale() === 'ar' ? 'المندوب الطبي' : 'Medical Representative' }}
                    </label>
                    @if(!empty($isRep) && $isRep)
                        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-cyan-50 dark:bg-cyan-950/40 text-cyan-700 dark:text-cyan-300 font-bold text-xs rounded border border-cyan-200 dark:border-cyan-800">
                            <i class="fa-solid fa-user-check"></i>
                            <span>{{ $currentUser->name }}</span>
                        </div>
                        <input type="hidden" name="mr_id" value="{{ $currentUser->id }}">
                    @else
                        <select name="mr_id" onchange="this.form.submit()" class="form-select text-sm py-1.5 px-3">
                            <option value="">{{ app()->getLocale() === 'ar' ? 'جميع المناديب' : 'All Medical Reps' }}</option>
                            @foreach($medicalReps as $rep)
                                <option value="{{ $rep->id }}" {{ $selectedMrId == $rep->id ? 'selected' : '' }}>
                                    {{ $rep->name }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">
                        {{ app()->getLocale() === 'ar' ? 'حالة زيارة الطبيب' : 'Doctor Status' }}
                    </label>
                    <select name="status" onchange="this.form.submit()" class="form-select text-sm py-1.5 px-3">
                        <option value="all" {{ $filterStatus === 'all' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'جميع الأطباء' : 'All Doctors' }}</option>
                        <option value="unvisited" {{ $filterStatus === 'unvisited' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'غير مزار (0 زيارة)' : 'Unvisited (0 Visits)' }}</option>
                        <option value="behind" {{ $filterStatus === 'behind' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'متأخر عن التردد المطلوب' : 'Behind Frequency' }}</option>
                        <option value="completed" {{ $filterStatus === 'completed' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'تم تحقيق المستهدف' : 'Target Completed' }}</option>
                    </select>
                </div>
            </div>

            <div class="pt-5 flex items-center gap-2">
                <span class="badge badge-outline text-xs">{{ $rows->count() }} Doctors in Report</span>
            </div>
        </form>
    </div>

    <!-- Report Table matching §8 Spec -->
    <div class="card p-0 overflow-hidden shadow-sm border border-gray-100 dark:border-gray-800 mb-8">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm table bz-sortable-table" data-table-sortable="true">
                <thead class="bg-gray-50 dark:bg-gray-800/60 text-gray-600 dark:text-gray-300 font-semibold border-b border-gray-200 dark:border-gray-700 text-xs">
                    <tr>
                        <th class="px-4 py-3.5">{{ app()->getLocale() === 'ar' ? 'كود الطبيب' : 'Contact Code' }}</th>
                        <th class="px-4 py-3.5">{{ app()->getLocale() === 'ar' ? 'اسم الطبيب' : 'Contact Name' }}</th>
                        <th class="px-4 py-3.5">{{ app()->getLocale() === 'ar' ? 'المنطقة / المدينة' : 'Region / City' }}</th>
                        <th class="px-4 py-3.5">{{ app()->getLocale() === 'ar' ? 'التخصص' : 'Specialty' }}</th>
                        <th class="px-4 py-3.5 text-center">{{ app()->getLocale() === 'ar' ? 'الفئة' : 'Class' }}</th>
                        <th class="px-4 py-3.5 text-center">{{ app()->getLocale() === 'ar' ? 'التردد المطلوب' : 'Req. Freq' }}</th>
                        <th class="px-4 py-3.5">{{ app()->getLocale() === 'ar' ? 'المندوب المكلف' : 'Assigned User' }}</th>
                        <th class="px-4 py-3.5 text-center">{{ app()->getLocale() === 'ar' ? 'الزيارات المنفذة' : 'Visits Done' }}</th>
                        <th class="px-4 py-3.5 text-center">{{ app()->getLocale() === 'ar' ? 'النقاط المستهدفة' : 'Target Points' }}</th>
                        <th class="px-4 py-3.5 text-center">{{ app()->getLocale() === 'ar' ? 'النقاط المحققة' : 'Achieved Points' }}</th>
                        <th class="px-4 py-3.5 text-center">{{ app()->getLocale() === 'ar' ? 'نسبة الالتزام' : 'Compliance %' }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-xs">
                    @forelse($rows as $r)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition-colors {{ $r['visits_done'] == 0 ? 'bg-rose-50/20 dark:bg-rose-950/10' : '' }}">
                            <td class="px-4 py-3.5 font-mono text-gray-500 font-bold">
                                {{ $r['contact_code'] }}
                            </td>
                            <td class="px-4 py-3.5 font-bold text-gray-900 dark:text-white">
                                {{ $r['contact_name'] }}
                            </td>
                            <td class="px-4 py-3.5 text-gray-600 dark:text-gray-400">
                                {{ $r['region_city'] }}
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="badge badge-outline text-[11px]">{{ $r['specialty'] }}</span>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded font-black text-xs {{ $r['class'] === 'A+' ? 'bg-amber-100 text-amber-900 dark:bg-amber-950/50 dark:text-amber-300' : 'bg-sky-100 text-sky-900 dark:bg-sky-950/50 dark:text-sky-300' }}">
                                    {{ $r['class'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-center font-bold text-gray-700 dark:text-gray-300">
                                {{ $r['required_frequency'] }}
                            </td>
                            <td class="px-4 py-3.5 font-semibold text-gray-800 dark:text-gray-200">
                                {{ $r['assigned_user'] }}
                            </td>
                            <td class="px-4 py-3.5 text-center font-bold">
                                <span class="{{ $r['visits_done'] >= $r['required_frequency'] ? 'text-emerald-600 dark:text-emerald-400' : ($r['visits_done'] > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-rose-600 dark:text-rose-400 font-black') }}">
                                    {{ $r['visits_done'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-center font-semibold text-gray-600 dark:text-gray-400">
                                {{ $r['target_points'] }}
                            </td>
                            <td class="px-4 py-3.5 text-center font-bold text-sky-600 dark:text-sky-400">
                                {{ $r['achieved_points'] }}
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                @php
                                    $compPct = (float) ($r['compliance_pct'] ?? $r['visit_compliance_pct'] ?? 0);
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $compPct >= 100 ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-400' : ($compPct > 0 ? 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-400' : 'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-400') }}">
                                    {{ $compPct }}%
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="px-4 py-12 text-center text-gray-400">
                                <i class="fa-solid fa-chart-column text-4xl mb-2"></i>
                                <p class="text-sm font-semibold">{{ app()->getLocale() === 'ar' ? 'لا توجد بيانات مطابقة لخيارات التصفية.' : 'No doctors match the selected report filter.' }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
