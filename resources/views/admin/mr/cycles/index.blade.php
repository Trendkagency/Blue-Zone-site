<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'دورات الزيارات الميدانية (Visit Cycles)' : 'Field Visit Cycles Management'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'إدارة فترات وتواريخ دورات الزيارات الشهرية والربع سنوية ومؤشرات الأداء المستهدفة' : 'Define time-boxed cycle periods against which rep coverage, compliance, and KPIs are computed.'"
    :breadcrumbs="[
        (app()->getLocale() === 'ar' ? 'نظام المندوب الطبي' : 'MR CRM') => route('admin.mr.live-map'),
        (app()->getLocale() === 'ar' ? 'دورات الزيارات' : 'Visit Cycles') => route('admin.mr.cycles.index')
    ]"
>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Cycles Table (2 Cols) -->
        <div class="lg:col-span-2 card p-0 overflow-hidden shadow-sm border border-gray-100 dark:border-gray-800">
            <div class="p-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between bg-gray-50/50 dark:bg-gray-800/40">
                <h3 class="font-bold text-sm text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-calendar-days text-purple-500"></i>
                    {{ app()->getLocale() === 'ar' ? 'سجل دورات الزيارة' : 'Visit Cycles Log' }}
                </h3>
                <span class="badge badge-outline text-xs">{{ $cycles->count() }} Cycles</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800/60 text-gray-600 dark:text-gray-400 font-semibold border-b border-gray-200 dark:border-gray-700 text-xs">
                        <tr>
                            <th class="px-5 py-3.5">{{ app()->getLocale() === 'ar' ? 'اسم الدورة' : 'Cycle Name' }}</th>
                            <th class="px-5 py-3.5">{{ app()->getLocale() === 'ar' ? 'الكود' : 'Code' }}</th>
                            <th class="px-5 py-3.5">{{ app()->getLocale() === 'ar' ? 'النطاق الزمني' : 'Date Range' }}</th>
                            <th class="px-5 py-3.5 text-center">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                            <th class="px-5 py-3.5 text-center">{{ app()->getLocale() === 'ar' ? 'التكليفات' : 'Assignments' }}</th>
                            <th class="px-5 py-3.5 text-center">{{ app()->getLocale() === 'ar' ? 'الزيارات' : 'Visits' }}</th>
                            <th class="px-5 py-3.5 text-right">{{ app()->getLocale() === 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach($cycles as $c)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition-colors">
                                <td class="px-5 py-4 font-bold text-gray-900 dark:text-white">
                                    {{ $c->name }}
                                </td>
                                <td class="px-5 py-4 font-mono text-xs text-gray-500 font-bold">
                                    {{ $c->code }}
                                </td>
                                <td class="px-5 py-4 text-xs text-gray-600 dark:text-gray-400">
                                    {{ $c->start_date->format('d M Y') }} — {{ $c->end_date->format('d M Y') }}
                                </td>
                                <td class="px-5 py-4 text-center">
                                    @if($c->status === 'active')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-400">
                                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                                            Active
                                        </span>
                                    @elseif($c->status === 'upcoming')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-sky-100 text-sky-800 dark:bg-sky-950/60 dark:text-sky-400">
                                            Upcoming
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400">
                                            Closed
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-center font-bold text-gray-700 dark:text-gray-300">
                                    {{ $c->assignments_count }}
                                </td>
                                <td class="px-5 py-4 text-center font-bold text-sky-600 dark:text-sky-400">
                                    {{ $c->visits_count }}
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button type="button" onclick="editCycle({{ json_encode($c) }})" class="btn btn-sm btn-ghost p-1.5 text-gray-500 hover:text-amber-500" title="Edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        @if($c->visits_count == 0)
                                            <form method="POST" action="{{ route('admin.mr.cycles.destroy', $c->id) }}" onsubmit="return confirm('Delete this cycle?')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-ghost p-1.5 text-gray-500 hover:text-rose-500" title="Delete">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add / Edit Cycle Form Card (1 Col) -->
        <div class="card p-6 shadow-sm border border-gray-100 dark:border-gray-800">
            <h3 id="form-title" class="font-bold text-sm text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-2 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-plus-circle text-sky-500"></i>
                <span id="form-title-text">{{ app()->getLocale() === 'ar' ? 'إنشاء دورة زيارات جديدة' : 'Create New Visit Cycle' }}</span>
            </h3>

            <form id="cycle-form" method="POST" action="{{ route('admin.mr.cycles.store') }}" class="space-y-4">
                @csrf
                <div id="method-container"></div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                        {{ app()->getLocale() === 'ar' ? 'اسم الدورة *' : 'Cycle Name *' }}
                    </label>
                    <input type="text" id="cy-name" name="name" required class="form-control text-sm" placeholder="e.g. October 2026 Cycle">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                        {{ app()->getLocale() === 'ar' ? 'كود الدورة *' : 'Cycle Code *' }}
                    </label>
                    <input type="text" id="cy-code" name="code" required class="form-control text-sm uppercase font-mono" placeholder="e.g. CYCLE-2026-10">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                            {{ app()->getLocale() === 'ar' ? 'تاريخ البداية *' : 'Start Date *' }}
                        </label>
                        <input type="date" id="cy-start" name="start_date" required class="form-control text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                            {{ app()->getLocale() === 'ar' ? 'تاريخ النهاية *' : 'End Date *' }}
                        </label>
                        <input type="date" id="cy-end" name="end_date" required class="form-control text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                        {{ app()->getLocale() === 'ar' ? 'حالة الدورة *' : 'Status *' }}
                    </label>
                    <select id="cy-status" name="status" required class="form-select text-sm">
                        <option value="active">Active (Current Running Cycle)</option>
                        <option value="upcoming">Upcoming (Future Planning)</option>
                        <option value="closed">Closed (Archived)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                        {{ app()->getLocale() === 'ar' ? 'ملاحظات الدورة' : 'Cycle Notes' }}
                    </label>
                    <textarea id="cy-notes" name="notes" rows="3" class="form-control text-sm" placeholder="Goals, focus products..."></textarea>
                </div>

                <div class="pt-4 border-t border-gray-100 dark:border-gray-800 flex items-center justify-end gap-2">
                    <button type="button" id="btn-cancel" onclick="resetCycleForm()" class="btn btn-secondary text-xs hidden">
                        {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                    </button>
                    <button type="submit" id="btn-submit" class="btn btn-primary font-bold text-xs shadow-sm w-full">
                        <i class="fa-solid fa-floppy-disk mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'حفظ الدورة' : 'Save Cycle' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function editCycle(item) {
            document.getElementById('form-title-text').innerText = '{{ app()->getLocale() === 'ar' ? 'تعديل الدورة: ' : 'Edit Cycle: ' }}' + item.name;
            const form = document.getElementById('cycle-form');
            form.action = '/admin/mr/cycles/' + item.id;
            document.getElementById('method-container').innerHTML = '<input type="hidden" name="_method" value="PUT">';

            document.getElementById('cy-name').value = item.name;
            document.getElementById('cy-code').value = item.code;
            document.getElementById('cy-start').value = item.start_date.substring(0, 10);
            document.getElementById('cy-end').value = item.end_date.substring(0, 10);
            document.getElementById('cy-status').value = item.status;
            document.getElementById('cy-notes').value = item.notes || '';

            document.getElementById('btn-cancel').classList.remove('hidden');
            document.getElementById('btn-submit').innerText = '{{ app()->getLocale() === 'ar' ? 'تحديث الدورة' : 'Update Cycle' }}';
        }

        function resetCycleForm() {
            document.getElementById('form-title-text').innerText = '{{ app()->getLocale() === 'ar' ? 'إنشاء دورة زيارات جديدة' : 'Create New Visit Cycle' }}';
            const form = document.getElementById('cycle-form');
            form.action = '{{ route('admin.mr.cycles.store') }}';
            document.getElementById('method-container').innerHTML = '';
            form.reset();
            document.getElementById('btn-cancel').classList.add('hidden');
            document.getElementById('btn-submit').innerText = '{{ app()->getLocale() === 'ar' ? 'حفظ الدورة' : 'Save Cycle' }}';
        }
    </script>
</x-layouts.admin>
