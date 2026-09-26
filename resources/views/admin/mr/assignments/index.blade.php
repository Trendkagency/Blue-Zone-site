<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'توزيع وتكليف الأطباء (محفظة الزيارات)' : 'Doctor Assignments & Cycle Allocation'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'تخصيص محافظ الأطباء للمندوبين لكل دورة زيارة وتتبع نسب إنجاز الزيارات والنقاط المستهدفة' : 'Assign doctor portfolios to medical representatives per cycle and track visit & point milestones.'"
    :breadcrumbs="[
        (app()->getLocale() === 'ar' ? 'نظام المندوب الطبي' : 'MR CRM') => route('admin.mr.live-map'),
        (app()->getLocale() === 'ar' ? 'تكليفات الأطباء' : 'Assignments') => route('admin.mr.assignments.index')
    ]"
>
    <x-slot name="actions">
        <div class="flex items-center gap-2">
            <a href="{{ request()->fullUrlWithQuery(['export' => 'xlsx']) }}" class="btn btn-secondary text-sm font-bold shadow-sm flex items-center gap-2" title="Export Assignments">
                <i class="fa-solid fa-file-excel text-emerald-500"></i>
                <span>{{ app()->getLocale() === 'ar' ? 'تصدير إكسيل (.xlsx)' : 'Export Excel (.xlsx)' }}</span>
            </a>
            @if(empty($isRep) || !$isRep)
            <button type="button" onclick="openAssignModal()" class="btn btn-primary font-bold text-sm shadow-sm">
                <i class="fa-solid fa-user-plus mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'تكليف أطباء لمندوب' : 'Assign Doctors to Rep' }}
            </button>
            @endif
        </div>
    </x-slot>

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
                                <option value="{{ $rep->id }}" {{ request('mr_id') == $rep->id ? 'selected' : '' }}>
                                    {{ $rep->name }}{{ $rep->area ? ' (📍 ' . $rep->area->name . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    @endif
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

            @if(empty($isRep) || !$isRep)
            <div>
                <button type="button" onclick="openAssignModal()" class="btn btn-primary font-bold text-sm shadow-sm">
                    <i class="fa-solid fa-user-plus mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'تكليف أطباء لمندوب' : 'Assign Doctors to Rep' }}
                </button>
            </div>
            @endif
        </div>
    </div>

    <!-- Assignments Table -->
    <div class="card p-0 overflow-hidden shadow-sm border border-gray-100 dark:border-gray-800 mb-8">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm table bz-sortable-table" data-table-sortable="true">
                <thead class="bg-gray-50 dark:bg-gray-800/60 text-gray-600 dark:text-gray-300 font-semibold border-b border-gray-200 dark:border-gray-700 text-xs">
                    <tr>
                        <th class="px-5 py-3.5">{{ app()->getLocale() === 'ar' ? 'الطبيب / المركز' : 'Doctor & Facility' }}</th>
                        <th class="px-5 py-3.5">{{ app()->getLocale() === 'ar' ? 'التخصص' : 'Specialty' }}</th>
                        <th class="px-5 py-3.5 text-center">{{ app()->getLocale() === 'ar' ? 'الفئة' : 'Class' }}</th>
                        <th class="px-5 py-3.5">{{ app()->getLocale() === 'ar' ? 'المندوب المكلف' : 'Assigned MR' }}</th>
                        <th class="px-5 py-3.5 text-center">{{ app()->getLocale() === 'ar' ? 'الزيارات المنفذة' : 'Visits Done / Req' }}</th>
                        <th class="px-5 py-3.5 text-center">{{ app()->getLocale() === 'ar' ? 'النقاط المحققة' : 'Points / Target' }}</th>
                        <th class="px-5 py-3.5 text-center">{{ app()->getLocale() === 'ar' ? 'نسبة الإنجاز' : 'Progress %' }}</th>
                        <th class="px-5 py-3.5 text-right no-sort" data-no-sort>{{ app()->getLocale() === 'ar' ? 'الإجراءات' : 'Actions' }}</th>
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
                                @if(empty($isRep) || !$isRep)
                                <form method="POST" action="{{ route('admin.mr.assignments.destroy', $a->id) }}" onsubmit="return confirm('Remove doctor from rep assignment for this cycle?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-ghost p-1.5 text-gray-400 hover:text-rose-500" title="Remove Assignment">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                                @else
                                <span class="text-xs text-gray-400">—</span>
                                @endif
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
    <div id="assign-modal" class="fixed inset-0 z-[1050] overflow-y-auto bg-black/60 p-3 sm:p-4 hidden backdrop-blur-sm" onclick="if(event.target === this) closeAssignModal()">
        <div class="min-h-full flex items-center justify-center p-0">
            <div class="card max-w-2xl w-full p-0 shadow-2xl relative flex flex-col max-h-[88vh] border border-gray-100 dark:border-gray-800 rounded-2xl overflow-hidden bg-white dark:bg-gray-850">
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 px-6 py-3.5 flex-shrink-0 bg-white dark:bg-gray-800">
                    <h3 class="font-bold text-base text-gray-900 dark:text-white flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 flex items-center justify-center border border-sky-100 dark:border-sky-900 flex-shrink-0">
                            <i class="fa-solid fa-user-plus text-sm"></i>
                        </div>
                        <div>
                            <span>{{ app()->getLocale() === 'ar' ? 'تكليف أطباء بمندوب طبي' : 'Assign Doctors to Rep' }}</span>
                            <p class="text-[11px] font-normal text-gray-500 dark:text-gray-400">
                                {{ app()->getLocale() === 'ar' ? 'إسناد محفظة أطباء وجدولة زيارات المندوب' : 'Assign doctor portfolio and configure visit schedule' }}
                            </p>
                        </div>
                    </h3>
                    <button type="button" onclick="closeAssignModal()" class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                <form id="assign-doctors-form" method="POST" action="{{ route('admin.mr.assignments.store') }}" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                    @csrf
                    <input type="hidden" name="cycle_id" value="{{ $selectedCycleId }}">

                    <!-- Scrollable Modal Body -->
                    <div class="flex-1 overflow-y-auto px-6 py-4 space-y-4">
                        <div class="flex-shrink-0">
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                                {{ app()->getLocale() === 'ar' ? 'المندوب الطبي الميداني *' : 'Medical Representative *' }}
                            </label>
                            <select name="mr_id" required class="form-select text-sm w-full">
                                <option value="">{{ app()->getLocale() === 'ar' ? 'اختر المندوب' : 'Select Representative' }}</option>
                                @foreach($medicalReps as $rep)
                                    <option value="{{ $rep->id }}">{{ $rep->name }}{{ $rep->area ? ' (📍 ' . $rep->area->name . ')' : '' }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex flex-col min-h-0">
                            <!-- Header with Counter Badge -->
                            <div class="flex items-center justify-between mb-1.5 flex-shrink-0">
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase">
                                    {{ app()->getLocale() === 'ar' ? 'محفظة الأطباء (تحديد متعدد) *' : 'Select Doctors Portfolio (Multi-select) *' }}
                                </label>
                                <span id="doc-selection-badge" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-sky-100 text-sky-800 dark:bg-sky-950 dark:text-sky-300 border border-sky-200 dark:border-sky-800">
                                    <span id="doc-selected-count">0</span>&nbsp;{{ app()->getLocale() === 'ar' ? 'طبيب محدد' : 'selected' }}
                                </span>
                            </div>

                            <!-- Search and Action Toolbar -->
                            <div class="flex items-center gap-2 mb-2 flex-shrink-0">
                                <div class="relative flex-1">
                                    <i class="fa-solid fa-magnifying-glass absolute top-1/2 -translate-y-1/2 {{ app()->getLocale() === 'ar' ? 'right-3' : 'left-3' }} text-gray-400 text-xs pointer-events-none"></i>
                                    <input type="text" id="doctor-search-input" 
                                        placeholder="{{ app()->getLocale() === 'ar' ? 'بحث بالاسم، الكود، التخصص، المركز...' : 'Search doctor name, code, facility...' }}" 
                                        class="form-control text-xs w-full {{ app()->getLocale() === 'ar' ? 'pr-8 pl-8' : 'pl-8 pr-8' }} py-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/60 focus:bg-white dark:focus:bg-gray-800 transition-colors">
                                    <button type="button" id="doctor-search-clear" class="hidden absolute top-1/2 -translate-y-1/2 {{ app()->getLocale() === 'ar' ? 'left-2.5' : 'right-2.5' }} text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-xs">
                                        <i class="fa-solid fa-circle-xmark"></i>
                                    </button>
                                </div>
                                <button type="button" id="btn-select-all-docs" class="btn btn-outline text-xs py-1.5 px-2.5 whitespace-nowrap shadow-xs" title="{{ app()->getLocale() === 'ar' ? 'تحديد الكل المعروض' : 'Select All Filtered' }}">
                                    <i class="fa-solid fa-check-double text-sky-500 mr-1 ml-1"></i>
                                    <span>{{ app()->getLocale() === 'ar' ? 'تحديد الكل' : 'Select All' }}</span>
                                </button>
                                <button type="button" id="btn-deselect-all-docs" class="btn btn-outline text-xs py-1.5 px-2.5 whitespace-nowrap shadow-xs" title="{{ app()->getLocale() === 'ar' ? 'إلغاء التحديد' : 'Deselect All' }}">
                                    <i class="fa-solid fa-rotate-left text-gray-400 mr-1 ml-1"></i>
                                    <span>{{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Clear' }}</span>
                                </button>
                            </div>

                            <!-- Selected Chips Bar -->
                            <div id="selected-chips-bar" class="hidden flex-wrap gap-1.5 max-h-16 overflow-y-auto p-2 mb-2 rounded-lg bg-sky-50/60 dark:bg-sky-950/20 border border-sky-100 dark:border-sky-900/40 flex-shrink-0">
                                <!-- Chips inserted via JS -->
                            </div>

                            <!-- Scrollable Doctor Cards List -->
                            <div id="doctors-list-wrapper" class="flex-1 overflow-y-auto max-h-40 sm:max-h-44 rounded-lg border border-gray-200 dark:border-gray-700/80 bg-gray-50/40 dark:bg-gray-900/40 p-2 space-y-1.5">
                                @forelse($availableDoctors as $doc)
                                    <div class="doctor-card group flex items-center justify-between gap-3 p-2 rounded-lg cursor-pointer transition-all duration-150 border border-gray-200/80 dark:border-gray-700/80 bg-white dark:bg-gray-800/90 hover:border-sky-400 dark:hover:border-sky-500 hover:shadow-xs select-none"
                                        data-id="{{ $doc->id }}"
                                        data-name="{{ $doc->name }}"
                                        data-code="{{ $doc->code }}"
                                        data-search="{{ strtolower($doc->name . ' ' . $doc->code . ' ' . $doc->hospital_clinic_name . ' ' . ($doc->specialty?->name ?? '') . ' ' . ($doc->classification?->code ?? '')) }}">
                                        
                                        <div class="flex items-center gap-3 min-w-0 flex-1">
                                            <input type="checkbox" name="contact_ids[]" value="{{ $doc->id }}" 
                                                class="doctor-checkbox h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-sky-600 focus:ring-sky-500 dark:bg-gray-800 transition-colors pointer-events-none flex-shrink-0">
                                            
                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <span class="font-bold text-sm text-gray-900 dark:text-white group-hover:text-sky-600 dark:group-hover:text-sky-400 transition-colors">
                                                        {{ $doc->name }}
                                                    </span>
                                                    <span class="px-1.5 py-0.2 rounded text-[11px] font-mono font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                                                        {{ $doc->code }}
                                                    </span>
                                                    @if($doc->classification)
                                                        <span class="px-1.5 py-0.2 rounded text-[10px] font-black {{ $doc->classification->code === 'A+' ? 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300' : 'bg-sky-100 text-sky-800 dark:bg-sky-950/60 dark:text-sky-300' }}">
                                                            {{ $doc->classification->code }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 mt-0.5 flex-wrap">
                                                    @if($doc->hospital_clinic_name)
                                                        <span class="truncate flex items-center gap-1">
                                                            <i class="fa-regular fa-hospital text-[11px] text-gray-400"></i>
                                                            {{ $doc->hospital_clinic_name }}
                                                        </span>
                                                    @endif
                                                    @if($doc->specialty)
                                                        <span>•</span>
                                                        <span class="text-sky-600 dark:text-sky-400 font-medium">
                                                            {{ $doc->specialty->name }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="check-indicator hidden text-sky-500 text-sm flex-shrink-0">
                                            <i class="fa-solid fa-circle-check"></i>
                                        </div>
                                    </div>
                                @empty
                                    <div class="p-6 text-center text-gray-400 text-xs">
                                        <i class="fa-solid fa-user-doctor text-2xl mb-1 text-gray-300 dark:text-gray-600 block"></i>
                                        {{ app()->getLocale() === 'ar' ? 'لا يوجد أطباء نشطين متاحين حالياً.' : 'No active doctors available.' }}
                                    </div>
                                @endforelse

                                <!-- No Search Match Placeholder -->
                                <div id="no-doctors-found" class="hidden p-6 text-center text-gray-400 text-xs">
                                    <i class="fa-solid fa-user-slash text-2xl mb-2 text-gray-300 dark:text-gray-600 block"></i>
                                    <span class="font-medium">{{ app()->getLocale() === 'ar' ? 'لا توجد نتائج مطابقة لبحثك.' : 'No doctors matched your search query.' }}</span>
                                </div>
                            </div>

                            <!-- Inline Validation Error -->
                            <div id="doctor-selection-error" class="hidden text-xs text-rose-500 font-semibold mt-1.5 flex items-center gap-1.5 flex-shrink-0">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'يرجى اختيار طبيب واحد على الأقل للمحفظة.' : 'Please select at least one doctor before confirming assignment.' }}</span>
                            </div>
                        </div>

                        <!-- Automatic Visit Scheduling Section -->
                        <div class="rounded-xl border border-indigo-100 dark:border-indigo-900/60 bg-gradient-to-br from-indigo-50/60 via-sky-50/40 to-white dark:from-indigo-950/20 dark:via-sky-950/15 dark:to-gray-800/80 p-3.5 space-y-3">
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center shadow-xs flex-shrink-0">
                                        <i class="fa-solid fa-calendar-check text-sm"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                            {{ app()->getLocale() === 'ar' ? 'جدولة الزيارات التلقائية' : 'Automatic Visit Scheduling' }}
                                            <span class="px-1.5 py-0.5 rounded-full text-[10px] font-semibold bg-indigo-100 text-indigo-700 dark:bg-indigo-900/60 dark:text-indigo-300">
                                                {{ app()->getLocale() === 'ar' ? 'أسبوعي / شهري / يومي' : 'Weekly / Monthly / Daily' }}
                                            </span>
                                        </h4>
                                        <p class="text-[11px] text-gray-500 dark:text-gray-400">
                                            {{ app()->getLocale() === 'ar' ? 'توليد زيارات مجدولة لتظهر فوراً في أجندة المندوب بالبوابة' : 'Schedule visits so they appear in MR Portal today & upcoming agenda' }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Enable Switch -->
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="auto_schedule" value="1" id="auto-schedule-toggle" checked class="sr-only peer">
                                    <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-gray-600 peer-checked:bg-indigo-600"></div>
                                </label>
                            </div>

                            <!-- Schedule Configuration Container -->
                            <div id="schedule-options-container" class="space-y-3 pt-2.5 border-t border-indigo-100/80 dark:border-indigo-900/40">
                                <!-- Cadence Selection (Weekly / Monthly / Per Days) -->
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-700 dark:text-gray-300 uppercase mb-1.5">
                                        {{ app()->getLocale() === 'ar' ? 'نمط التكرار والجدولة *' : 'Visit Cadence / Frequency *' }}
                                    </label>
                                    <div class="grid grid-cols-3 gap-2">
                                        <label class="cadence-option flex flex-col p-2.5 rounded-lg border cursor-pointer transition-all border-indigo-500 bg-white dark:bg-gray-800 shadow-xs ring-1 ring-indigo-500/20">
                                            <input type="radio" name="schedule_cadence" value="weekly" checked class="sr-only cadence-radio">
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                                    <i class="fa-solid fa-calendar-week text-indigo-500 text-xs"></i>
                                                    {{ app()->getLocale() === 'ar' ? 'أسبوعياً' : 'Weekly' }}
                                                </span>
                                                <i class="fa-solid fa-circle-check text-indigo-600 cadence-check text-xs"></i>
                                            </div>
                                            <span class="text-[10px] text-gray-500 dark:text-gray-400">
                                                {{ app()->getLocale() === 'ar' ? 'زيارة كل 7 أيام' : '1 visit every 7 days' }}
                                            </span>
                                        </label>

                                        <label class="cadence-option flex flex-col p-2.5 rounded-lg border cursor-pointer transition-all border-gray-200 dark:border-gray-700 bg-white/70 dark:bg-gray-800/70 hover:border-indigo-300">
                                            <input type="radio" name="schedule_cadence" value="monthly" class="sr-only cadence-radio">
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                                    <i class="fa-regular fa-calendar text-sky-500 text-xs"></i>
                                                    {{ app()->getLocale() === 'ar' ? 'شهرياً' : 'Monthly' }}
                                                </span>
                                                <i class="fa-solid fa-circle-check text-indigo-600 cadence-check text-xs hidden"></i>
                                            </div>
                                            <span class="text-[10px] text-gray-500 dark:text-gray-400">
                                                {{ app()->getLocale() === 'ar' ? 'موزعة على الدورة' : 'Distributed per cycle' }}
                                            </span>
                                        </label>

                                        <label class="cadence-option flex flex-col p-2.5 rounded-lg border cursor-pointer transition-all border-gray-200 dark:border-gray-700 bg-white/70 dark:bg-gray-800/70 hover:border-indigo-300">
                                            <input type="radio" name="schedule_cadence" value="daily" class="sr-only cadence-radio">
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="text-xs font-bold text-gray-900 dark:text-white flex items-center gap-1.5">
                                                    <i class="fa-solid fa-calendar-day text-emerald-500 text-xs"></i>
                                                    {{ app()->getLocale() === 'ar' ? 'يومياً (كل يوم)' : 'Every Day (Daily)' }}
                                                </span>
                                                <i class="fa-solid fa-circle-check text-indigo-600 cadence-check text-xs hidden"></i>
                                            </div>
                                            <span class="text-[10px] text-gray-500 dark:text-gray-400">
                                                {{ app()->getLocale() === 'ar' ? 'زيارات مجدولة كل يوم' : 'Visits scheduled every day' }}
                                            </span>
                                        </label>
                                    </div>

                                    <!-- Daily Continuous Configuration (Toggled when Daily is selected) -->
                                    <div id="daily-settings-bar" class="hidden mt-2 p-2.5 rounded-lg bg-emerald-50/70 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/60 flex flex-wrap items-center justify-between gap-3 text-xs">
                                        <label class="flex items-center gap-2 cursor-pointer select-none">
                                            <input type="checkbox" name="daily_all_days" value="1" class="h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-emerald-600 focus:ring-emerald-500">
                                            <span class="font-bold text-gray-800 dark:text-gray-200 text-xs flex items-center gap-1.5">
                                                <i class="fa-solid fa-calendar-days text-emerald-600"></i>
                                                {{ app()->getLocale() === 'ar' ? 'كل يوم متتالي (بما في ذلك عطلات الأسبوع)' : 'Continuous Every Day (Include Weekends)' }}
                                            </span>
                                        </label>
                                        <div class="flex items-center gap-2">
                                            <span class="text-[11px] text-gray-600 dark:text-gray-400 font-medium">
                                                {{ app()->getLocale() === 'ar' ? 'تكرار لعدد أيام:' : 'Repeat Days:' }}
                                            </span>
                                            <input type="number" name="daily_count" min="1" max="60" placeholder="{{ app()->getLocale() === 'ar' ? 'تلقائي' : 'Auto' }}" class="form-control text-xs w-20 py-1 px-2 rounded border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                                        </div>
                                    </div>
                                </div>

                                <!-- Start Today Guarantee & Date/Time Settings -->
                                <div class="grid grid-cols-1 sm:grid-cols-12 gap-2.5 pt-1">
                                    <div class="sm:col-span-6 p-2.5 rounded-lg bg-white/90 dark:bg-gray-800/90 border border-gray-200/80 dark:border-gray-700 flex items-center">
                                        <label class="flex items-center gap-2.5 cursor-pointer w-full">
                                            <input type="checkbox" name="schedule_today" value="1" checked class="h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500 dark:bg-gray-700 flex-shrink-0">
                                            <div class="min-w-0 flex-1">
                                                <span class="text-xs font-bold text-gray-800 dark:text-gray-200 flex items-center gap-1">
                                                    <i class="fa-solid fa-bolt text-amber-500 text-[11px]"></i>
                                                    {{ app()->getLocale() === 'ar' ? 'بدء أول زيارة اليوم فوراً' : 'Start First Visit Today' }}
                                                </span>
                                                <p class="text-[10px] text-gray-500 dark:text-gray-400 truncate">
                                                    {{ app()->getLocale() === 'ar' ? 'تظهر في أجندة اليوم ببوابة المندوب' : 'Instantly shows in MR Portal Today Agenda' }}
                                                </p>
                                            </div>
                                        </label>
                                    </div>

                                    <div class="sm:col-span-3">
                                        <label class="block text-[10px] font-bold text-gray-600 dark:text-gray-400 uppercase mb-1">
                                            {{ app()->getLocale() === 'ar' ? 'تاريخ البدء' : 'Start Date' }}
                                        </label>
                                        <input type="date" name="schedule_start_date" value="{{ now()->toDateString() }}" min="{{ now()->toDateString() }}" class="form-control text-xs w-full py-1.5 px-2 rounded border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                                    </div>
                                    <div class="sm:col-span-3">
                                        <label class="block text-[10px] font-bold text-gray-600 dark:text-gray-400 uppercase mb-1">
                                            {{ app()->getLocale() === 'ar' ? 'وقت البدء' : 'Default Time' }}
                                        </label>
                                        <input type="time" name="schedule_time" value="09:30" class="form-control text-xs w-full py-1.5 px-2 rounded border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sticky Modal Footer -->
                    <div class="px-6 py-3 bg-gray-50/90 dark:bg-gray-900/60 border-t border-gray-100 dark:border-gray-800 flex items-center justify-end gap-2.5 flex-shrink-0">
                        <button type="button" onclick="closeAssignModal()" class="btn btn-secondary text-xs px-4 py-2">
                            {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                        </button>
                        <button type="submit" class="btn btn-primary font-bold text-xs px-5 py-2 shadow-sm bg-gradient-to-r from-sky-600 to-indigo-600 hover:from-sky-700 hover:to-indigo-700 text-white">
                            <i class="fa-solid fa-check mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'تأكيد التكليف والجدولة' : 'Confirm Assignment & Schedule' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openAssignModal() {
            const modal = document.getElementById('assign-modal');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeAssignModal() {
            const modal = document.getElementById('assign-modal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAssignModal();
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const listWrapper = document.getElementById('doctors-list-wrapper');
            const searchInput = document.getElementById('doctor-search-input');
            const clearSearchBtn = document.getElementById('doctor-search-clear');
            const btnSelectAll = document.getElementById('btn-select-all-docs');
            const btnDeselectAll = document.getElementById('btn-deselect-all-docs');
            const selectedCountElem = document.getElementById('doc-selected-count');
            const chipsBar = document.getElementById('selected-chips-bar');
            const form = document.getElementById('assign-doctors-form');
            const errorElem = document.getElementById('doctor-selection-error');
            const noResultsElem = document.getElementById('no-doctors-found');

            if (!listWrapper) return;

            const cards = Array.from(listWrapper.querySelectorAll('.doctor-card'));

            function updateCardVisual(card, isChecked) {
                const checkIndicator = card.querySelector('.check-indicator');
                if (isChecked) {
                    card.classList.add('bg-sky-50/80', 'dark:bg-sky-950/40', 'border-sky-400', 'dark:border-sky-500', 'ring-1', 'ring-sky-400/30');
                    card.classList.remove('bg-white', 'dark:bg-gray-800/90', 'border-gray-200/80', 'dark:border-gray-700/80');
                    if (checkIndicator) checkIndicator.classList.remove('hidden');
                } else {
                    card.classList.remove('bg-sky-50/80', 'dark:bg-sky-950/40', 'border-sky-400', 'dark:border-sky-500', 'ring-1', 'ring-sky-400/30');
                    card.classList.add('bg-white', 'dark:bg-gray-800/90', 'border-gray-200/80', 'dark:border-gray-700/80');
                    if (checkIndicator) checkIndicator.classList.add('hidden');
                }
            }

            function syncSelectedState() {
                const checkedBoxes = listWrapper.querySelectorAll('.doctor-checkbox:checked');
                const count = checkedBoxes.length;

                if (selectedCountElem) {
                    selectedCountElem.textContent = count;
                }

                if (count > 0 && errorElem) {
                    errorElem.classList.add('hidden');
                }

                // Render selected chips
                if (chipsBar) {
                    chipsBar.innerHTML = '';
                    if (count === 0) {
                        chipsBar.classList.add('hidden');
                        chipsBar.classList.remove('flex');
                    } else {
                        chipsBar.classList.remove('hidden');
                        chipsBar.classList.add('flex');

                        checkedBoxes.forEach(cb => {
                            const card = cb.closest('.doctor-card');
                            if (!card) return;
                            const docName = card.getAttribute('data-name');
                            const docId = card.getAttribute('data-id');

                            const chip = document.createElement('span');
                            chip.className = 'inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-semibold bg-sky-100 text-sky-800 dark:bg-sky-900/60 dark:text-sky-200 border border-sky-200 dark:border-sky-700';
                            chip.innerHTML = `
                                <span>${docName}</span>
                                <button type="button" class="text-sky-600 hover:text-rose-500 dark:text-sky-300 dark:hover:text-rose-400 text-[11px] leading-none" data-remove-id="${docId}">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            `;
                            chipsBar.appendChild(chip);
                        });
                    }
                }
            }

            // Click card to toggle
            cards.forEach(card => {
                card.addEventListener('click', function(e) {
                    const checkbox = card.querySelector('.doctor-checkbox');
                    if (!checkbox) return;

                    checkbox.checked = !checkbox.checked;
                    updateCardVisual(card, checkbox.checked);
                    syncSelectedState();
                });
            });

            // Chips remove button click
            if (chipsBar) {
                chipsBar.addEventListener('click', function(e) {
                    const btn = e.target.closest('[data-remove-id]');
                    if (!btn) return;
                    const docId = btn.getAttribute('data-remove-id');
                    const targetCard = cards.find(c => c.getAttribute('data-id') === docId);
                    if (targetCard) {
                        const cb = targetCard.querySelector('.doctor-checkbox');
                        if (cb) {
                            cb.checked = false;
                            updateCardVisual(targetCard, false);
                            syncSelectedState();
                        }
                    }
                });
            }

            // Search filtering
            function filterDoctors() {
                const query = (searchInput.value || '').trim().toLowerCase();
                let visibleCount = 0;

                if (clearSearchBtn) {
                    if (query.length > 0) {
                        clearSearchBtn.classList.remove('hidden');
                    } else {
                        clearSearchBtn.classList.add('hidden');
                    }
                }

                cards.forEach(card => {
                    const searchStr = card.getAttribute('data-search') || '';
                    if (!query || searchStr.includes(query)) {
                        card.style.display = '';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                if (noResultsElem) {
                    if (visibleCount === 0 && cards.length > 0) {
                        noResultsElem.classList.remove('hidden');
                    } else {
                        noResultsElem.classList.add('hidden');
                    }
                }
            }

            if (searchInput) {
                searchInput.addEventListener('input', filterDoctors);
            }

            if (clearSearchBtn) {
                clearSearchBtn.addEventListener('click', function() {
                    searchInput.value = '';
                    filterDoctors();
                    searchInput.focus();
                });
            }

            // Select all visible
            if (btnSelectAll) {
                btnSelectAll.addEventListener('click', function() {
                    cards.forEach(card => {
                        if (card.style.display !== 'none') {
                            const cb = card.querySelector('.doctor-checkbox');
                            if (cb) {
                                cb.checked = true;
                                updateCardVisual(card, true);
                            }
                        }
                    });
                    syncSelectedState();
                });
            }

            // Deselect all
            if (btnDeselectAll) {
                btnDeselectAll.addEventListener('click', function() {
                    cards.forEach(card => {
                        const cb = card.querySelector('.doctor-checkbox');
                        if (cb) {
                            cb.checked = false;
                            updateCardVisual(card, false);
                        }
                    });
                    syncSelectedState();
                });
            }

            // Form validation
            if (form) {
                form.addEventListener('submit', function(e) {
                    const checkedCount = listWrapper.querySelectorAll('.doctor-checkbox:checked').length;
                    if (checkedCount === 0) {
                        e.preventDefault();
                        if (errorElem) {
                            errorElem.classList.remove('hidden');
                            errorElem.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                        }
                    } else {
                        if (errorElem) {
                            errorElem.classList.add('hidden');
                        }
                    }
                });
            }

            // Initialize any pre-checked cards
            cards.forEach(card => {
                const cb = card.querySelector('.doctor-checkbox');
                if (cb && cb.checked) {
                    updateCardVisual(card, true);
                }
            });
            syncSelectedState();

            // Auto-schedule toggle and cadence selector handling
            const autoScheduleToggle = document.getElementById('auto-schedule-toggle');
            const scheduleContainer = document.getElementById('schedule-options-container');
            const cadenceOptions = document.querySelectorAll('.cadence-option');

            if (autoScheduleToggle && scheduleContainer) {
                autoScheduleToggle.addEventListener('change', function() {
                    if (this.checked) {
                        scheduleContainer.classList.remove('hidden');
                    } else {
                        scheduleContainer.classList.add('hidden');
                    }
                });
            }

            const dailySettingsBar = document.getElementById('daily-settings-bar');

            if (cadenceOptions.length > 0) {
                cadenceOptions.forEach(opt => {
                    opt.addEventListener('click', function() {
                        const radio = opt.querySelector('.cadence-radio');
                        if (radio) {
                            radio.checked = true;
                        }

                        if (dailySettingsBar) {
                            if (radio && radio.value === 'daily') {
                                dailySettingsBar.classList.remove('hidden');
                            } else {
                                dailySettingsBar.classList.add('hidden');
                            }
                        }

                        cadenceOptions.forEach(other => {
                            const otherRadio = other.querySelector('.cadence-radio');
                            const checkIcon = other.querySelector('.cadence-check');
                            if (otherRadio && otherRadio.checked) {
                                other.classList.add('border-indigo-500', 'bg-white', 'dark:bg-gray-800', 'shadow-xs', 'ring-1', 'ring-indigo-500/20');
                                other.classList.remove('border-gray-200', 'dark:border-gray-700', 'bg-white/70', 'dark:bg-gray-800/70');
                                if (checkIcon) checkIcon.classList.remove('hidden');
                            } else {
                                other.classList.remove('border-indigo-500', 'bg-white', 'dark:bg-gray-800', 'shadow-xs', 'ring-1', 'ring-indigo-500/20');
                                other.classList.add('border-gray-200', 'dark:border-gray-700', 'bg-white/70', 'dark:bg-gray-800/70');
                                if (checkIcon) checkIcon.classList.add('hidden');
                            }
                        });
                    });
                });
            }
        });
    </script>
</x-layouts.admin>
