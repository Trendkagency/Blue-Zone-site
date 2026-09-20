<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'توزيع وتكليف الأطباء (محفظة الزيارات)' : 'Doctor Assignments & Cycle Allocation'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'تخصيص محافظ الأطباء للمندوبين لكل دورة زيارة وتتبع نسب إنجاز الزيارات والنقاط المستهدفة' : 'Assign doctor portfolios to medical representatives per cycle and track visit & point milestones.'"
    :breadcrumbs="[
        (app()->getLocale() === 'ar' ? 'نظام المندوب الطبي' : 'MR CRM') => route('admin.mr.live-map'),
        (app()->getLocale() === 'ar' ? 'تكليفات الأطباء' : 'Assignments') => route('admin.mr.assignments.index')
    ]"
>
    <!-- Filter Toolbar & Bulk Assign Trigger -->
    <div class="card p-4 mb-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <form method="GET" action="{{ route('admin.mr.assignments.index') }}" class="flex flex-wrap items-center gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">
                        {{ app()->getLocale() === 'ar' ? 'دورة الزيارات' : 'Visit Cycle' }}
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
                    <select name="mr_id" onchange="this.form.submit()" class="form-select text-sm py-1.5 px-3">
                        <option value="">{{ app()->getLocale() === 'ar' ? 'جميع المناديب' : 'All Medical Reps' }}</option>
                        @foreach($medicalReps as $rep)
                            <option value="{{ $rep->id }}" {{ request('mr_id') == $rep->id ? 'selected' : '' }}>
                                {{ $rep->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">
                        {{ app()->getLocale() === 'ar' ? 'بحث بالطبيب' : 'Search Doctor' }}
                    </label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control text-sm py-1.5 px-3" placeholder="{{ app()->getLocale() === 'ar' ? 'اسم الطبيب أو الكود...' : 'Doctor name or code...' }}">
                </div>

                <div class="pt-5">
                    <button type="submit" class="btn btn-outline text-xs">
                        <i class="fa-solid fa-filter mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'تطبيق الفلتر' : 'Filter' }}
                    </button>
                </div>
            </form>

            <div>
                <button type="button" onclick="document.getElementById('assign-modal').classList.remove('hidden')" class="btn btn-primary font-bold text-sm shadow-sm">
                    <i class="fa-solid fa-user-plus mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'تكليف أطباء لمندوب' : 'Assign Doctors to Rep' }}
                </button>
            </div>
        </div>
    </div>

    <!-- Assignments Table -->
    <div class="card p-0 overflow-hidden shadow-sm border border-gray-100 dark:border-gray-800 mb-8">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800/60 text-gray-600 dark:text-gray-300 font-semibold border-b border-gray-200 dark:border-gray-700 text-xs">
                    <tr>
                        <th class="px-5 py-3.5">{{ app()->getLocale() === 'ar' ? 'الطبيب / المركز' : 'Doctor & Facility' }}</th>
                        <th class="px-5 py-3.5">{{ app()->getLocale() === 'ar' ? 'التخصص' : 'Specialty' }}</th>
                        <th class="px-5 py-3.5 text-center">{{ app()->getLocale() === 'ar' ? 'الفئة' : 'Class' }}</th>
                        <th class="px-5 py-3.5">{{ app()->getLocale() === 'ar' ? 'المندوب المكلف' : 'Assigned MR' }}</th>
                        <th class="px-5 py-3.5 text-center">{{ app()->getLocale() === 'ar' ? 'الزيارات المنفذة' : 'Visits Done / Req' }}</th>
                        <th class="px-5 py-3.5 text-center">{{ app()->getLocale() === 'ar' ? 'النقاط المحققة' : 'Points / Target' }}</th>
                        <th class="px-5 py-3.5 text-center">{{ app()->getLocale() === 'ar' ? 'نسبة الإنجاز' : 'Progress %' }}</th>
                        <th class="px-5 py-3.5 text-right">{{ app()->getLocale() === 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($assignments as $a)
                        @php
                            $targetVisits = (int) $a->target_visits;
                            $doneVisits = (int) $a->visits_done;
                            $pct = $targetVisits > 0 ? min(100, round(($doneVisits / $targetVisits) * 100)) : 0;
                        @endphp
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition-colors">
                            <td class="px-5 py-4">
                                <a href="{{ route('admin.mr.contacts.show', $a->contact_id) }}" class="font-bold text-gray-900 dark:text-white hover:text-sky-500">
                                    {{ $a->contact?->name ?? 'Doctor' }}
                                </a>
                                <div class="text-xs text-gray-500 font-mono mt-0.5">
                                    {{ $a->contact?->code }} • {{ $a->contact?->hospital_clinic_name }}
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="badge badge-outline text-xs">
                                    {{ $a->contact?->specialty?->name ?? 'General' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                @if($a->contact?->classification)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-black {{ $a->contact->classification->code === 'A+' ? 'bg-amber-100 text-amber-800 dark:bg-amber-950/50 dark:text-amber-400' : 'bg-sky-100 text-sky-800 dark:bg-sky-950/50 dark:text-sky-400' }}">
                                        {{ $a->contact->classification->code }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 font-bold text-gray-800 dark:text-gray-200">
                                {{ $a->representative?->name ?? 'Rep' }}
                            </td>
                            <td class="px-5 py-4 text-center font-bold">
                                <span class="{{ $doneVisits >= $targetVisits ? 'text-emerald-600 dark:text-emerald-400' : ($doneVisits > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-rose-600 dark:text-rose-400') }}">
                                    {{ $doneVisits }} / {{ $targetVisits }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-center font-bold text-sky-600 dark:text-sky-400">
                                {{ $a->achieved_points }} / {{ $a->target_points }} pts
                            </td>
                            <td class="px-5 py-4 text-center">
                                <div class="w-24 mx-auto">
                                    <div class="flex items-center justify-between text-[11px] font-bold mb-1">
                                        <span>{{ $pct }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 h-1.5 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full {{ $pct >= 100 ? 'bg-emerald-500' : ($pct > 0 ? 'bg-amber-500' : 'bg-rose-500') }}" style="width: {{ $pct }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <form method="POST" action="{{ route('admin.mr.assignments.destroy', $a->id) }}" onsubmit="return confirm('Remove doctor from rep assignment for this cycle?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-ghost p-1.5 text-gray-400 hover:text-rose-500" title="Remove Assignment">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center text-gray-400">
                                <i class="fa-solid fa-clipboard-user text-4xl mb-2"></i>
                                <p class="text-sm font-semibold">{{ app()->getLocale() === 'ar' ? 'لا توجد تكليفات مسجلة لهذه الدورة أو المندوب.' : 'No doctor assignments recorded for this cycle and filter.' }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($assignments->hasPages())
            <div class="p-4 border-t border-gray-100 dark:border-gray-800">
                {{ $assignments->links() }}
            </div>
        @endif
    </div>

    <!-- Assignment Modal -->
    <div id="assign-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 hidden backdrop-blur-sm">
        <div class="card max-w-lg w-full p-6 shadow-2xl relative">
            <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3 mb-4">
                <h3 class="font-bold text-base text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-user-plus text-sky-500"></i>
                    {{ app()->getLocale() === 'ar' ? 'تكليف أطباء بمندوب طبي' : 'Assign Doctors to Rep' }}
                </h3>
                <button type="button" onclick="document.getElementById('assign-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.mr.assignments.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="cycle_id" value="{{ $selectedCycleId }}">

                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                        {{ app()->getLocale() === 'ar' ? 'المندوب الطبي الميداني *' : 'Medical Representative *' }}
                    </label>
                    <select name="mr_id" required class="form-select text-sm">
                        <option value="">{{ app()->getLocale() === 'ar' ? 'اختر المندوب' : 'Select Representative' }}</option>
                        @foreach($medicalReps as $rep)
                            <option value="{{ $rep->id }}">{{ $rep->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                        {{ app()->getLocale() === 'ar' ? 'اختر الأطباء للمحفظة (يمكن اختيار أكثر من طبيب) *' : 'Select Doctors Portfolio (Multi-select) *' }}
                    </label>
                    <select name="contact_ids[]" multiple required size="7" class="form-select text-sm font-sans">
                        @foreach($availableDoctors as $doc)
                            <option value="{{ $doc->id }}">
                                {{ $doc->name }} ({{ $doc->code }}) — {{ $doc->hospital_clinic_name }}
                            </option>
                        @endforeach
                    </select>
                    <span class="text-[11px] text-gray-400 mt-1 block">
                        {{ app()->getLocale() === 'ar' ? 'اضغط باستمرار على Ctrl أو Cmd لتحديد أطباء متعددين' : 'Hold Ctrl or Cmd key to select multiple doctors' }}
                    </span>
                </div>

                <div class="pt-4 border-t border-gray-100 dark:border-gray-800 flex items-center justify-end gap-2">
                    <button type="button" onclick="document.getElementById('assign-modal').classList.add('hidden')" class="btn btn-secondary text-xs">
                        {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                    </button>
                    <button type="submit" class="btn btn-primary font-bold text-xs shadow-sm">
                        <i class="fa-solid fa-check mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'تأكيد التكليف' : 'Confirm Assignment' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
