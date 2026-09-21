<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'تصنيفات الأطباء الفئوية (A+/A/B/C)' : 'Doctor Classifications & Scoring (A+/A/B/C)'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'إدارة فئات الأطباء، النقاط المستهدفة، وعدد الزيارات المطلوبة لكل دورة (قابلة للتعديل والإضافة بالكامل)' : 'Manage doctor tier classifications, target points, and required visit frequencies per cycle.'"
    :breadcrumbs="[
        (app()->getLocale() === 'ar' ? 'نظام المندوب الطبي' : 'MR CRM') => route('admin.mr.live-map'),
        (app()->getLocale() === 'ar' ? 'تصنيفات الأطباء' : 'Doctor Classes') => route('admin.mr.classifications.index')
    ]"
>
    <x-slot name="actions">
        <div class="flex items-center gap-2">
            <a href="{{ request()->fullUrlWithQuery(['export' => 'xlsx']) }}" class="btn btn-secondary text-sm font-bold shadow-sm flex items-center gap-2" title="Export Classifications">
                <i class="fa-solid fa-file-excel text-emerald-500"></i>
                <span>{{ app()->getLocale() === 'ar' ? 'تصدير إكسيل (.xlsx)' : 'Export Excel (.xlsx)' }}</span>
            </a>
        </div>
    </x-slot>

    <!-- Top Info Alert -->
    <div class="card p-4 mb-6 bg-sky-50/50 dark:bg-sky-950/20 border-sky-200 dark:border-sky-800">
        <div class="flex items-start gap-3">
            <i class="fa-solid fa-circle-info text-sky-500 text-lg mt-0.5"></i>
            <div class="text-xs text-sky-900 dark:text-sky-300">
                <span class="font-bold">{{ app()->getLocale() === 'ar' ? 'آلية النقاط والتردد الإلزامي (§3.3 من المواصفات):' : 'Scoring & Required Visit Frequency Rules (§3.3):' }}</span>
                {{ app()->getLocale() === 'ar' 
                    ? 'يتم احتساب نقاط المندوب بناءً على فئة الطبيب المزار، ويتم وضع سقف للنقاط مساوٍ لعدد الزيارات المطلوبة (Prevent Point Farming). يمكن للمدير إضافة فئات جديدة مثل (VIP, S) أو تعديل النقاط والتردد في أي وقت.' 
                    : 'Rep points are derived from doctor tier and strictly capped at the required visits count per cycle to prevent point-farming. You can add custom tiers (e.g. VIP, S) or adjust points and frequencies at any time.' }}
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Classes Table (2 Cols) -->
        <div class="lg:col-span-2 card p-0 overflow-hidden shadow-sm border border-gray-100 dark:border-gray-800">
            <div class="p-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between bg-gray-50/50 dark:bg-gray-800/40">
                <h3 class="font-bold text-sm text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-ranking-star text-amber-500"></i>
                    {{ app()->getLocale() === 'ar' ? 'فئات الأطباء النشطة وقواعد الاحتساب' : 'Active Classifications & Rules' }}
                </h3>
                <span class="badge badge-outline text-xs">{{ $classifications->count() }} Tiers</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm table bz-sortable-table" data-table-sortable="true">
                    <thead class="bg-gray-50 dark:bg-gray-800/60 text-gray-600 dark:text-gray-400 font-semibold border-b border-gray-200 dark:border-gray-700 text-xs">
                        <tr>
                            <th class="px-5 py-3.5">{{ app()->getLocale() === 'ar' ? 'رمز الفئة' : 'Code' }}</th>
                            <th class="px-5 py-3.5">{{ app()->getLocale() === 'ar' ? 'اسم الفئة' : 'Label' }}</th>
                            <th class="px-5 py-3.5 text-center">{{ app()->getLocale() === 'ar' ? 'نقاط الزيارة' : 'Points / Visit' }}</th>
                            <th class="px-5 py-3.5 text-center">{{ app()->getLocale() === 'ar' ? 'الزيارات المطلوبة' : 'Req. Visits / Cycle' }}</th>
                            <th class="px-5 py-3.5 text-center">{{ app()->getLocale() === 'ar' ? 'الحد الأقصى للنقاط' : 'Max Points / Doctor' }}</th>
                            <th class="px-5 py-3.5 text-center">{{ app()->getLocale() === 'ar' ? 'عدد الأطباء' : 'Doctors Count' }}</th>
                            <th class="px-5 py-3.5 text-right no-sort" data-no-sort>{{ app()->getLocale() === 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach($classifications as $cl)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition-colors">
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg font-black text-sm {{ $cl->code === 'A+' ? 'bg-amber-100 text-amber-900 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-300 dark:border-amber-700' : ($cl->code === 'A' ? 'bg-sky-100 text-sky-900 dark:bg-sky-950/60 dark:text-sky-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300') }}">
                                        {{ $cl->code }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 font-bold text-gray-900 dark:text-white">
                                    {{ $cl->label }}
                                </td>
                                <td class="px-5 py-4 text-center font-bold text-sky-600 dark:text-sky-400">
                                    {{ $cl->points }} pts
                                </td>
                                <td class="px-5 py-4 text-center font-bold text-gray-800 dark:text-gray-200">
                                    {{ $cl->required_visits }} visits
                                </td>
                                <td class="px-5 py-4 text-center font-bold text-emerald-600 dark:text-emerald-400">
                                    {{ $cl->points * $cl->required_visits }} pts
                                </td>
                                <td class="px-5 py-4 text-center text-xs font-semibold text-gray-500">
                                    {{ $cl->contacts_count }} {{ app()->getLocale() === 'ar' ? 'طبيب' : 'doctors' }}
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button type="button" 
                                                onclick="editClassification({{ json_encode($cl) }})" 
                                                class="btn btn-sm btn-ghost p-1.5 text-gray-500 hover:text-amber-500" 
                                                title="Edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        @if($cl->contacts_count == 0)
                                            <form method="POST" action="{{ route('admin.mr.classifications.destroy', $cl->id) }}" onsubmit="return confirm('Delete this classification tier?')" class="inline">
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

        <!-- Add / Edit Form Card (1 Col) -->
        <div class="card p-6 shadow-sm border border-gray-100 dark:border-gray-800">
            <h3 id="form-title" class="font-bold text-sm text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-2 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-plus-circle text-sky-500"></i>
                <span id="form-title-text">{{ app()->getLocale() === 'ar' ? 'إضافة فئة جديدة' : 'Add New Tier' }}</span>
            </h3>

            <form id="classification-form" method="POST" action="{{ route('admin.mr.classifications.store') }}" class="space-y-4">
                @csrf
                <div id="method-container"></div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                        {{ app()->getLocale() === 'ar' ? 'رمز الفئة (Code) *' : 'Tier Code *' }}
                    </label>
                    <input type="text" id="cl-code" name="code" required class="form-control text-sm font-bold uppercase" placeholder="e.g. VIP, S, A+">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                        {{ app()->getLocale() === 'ar' ? 'مسمى الفئة (Label) *' : 'Tier Label *' }}
                    </label>
                    <input type="text" id="cl-label" name="label" required class="form-control text-sm" placeholder="e.g. High Priority Specialists">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                            {{ app()->getLocale() === 'ar' ? 'النقاط / الزيارة *' : 'Points / Visit *' }}
                        </label>
                        <input type="number" id="cl-points" min="1" name="points" value="1" required class="form-control text-sm font-bold">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                            {{ app()->getLocale() === 'ar' ? 'الزيارات المطلوبة *' : 'Req. Visits *' }}
                        </label>
                        <input type="number" id="cl-visits" min="1" name="required_visits" value="1" required class="form-control text-sm font-bold">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                        {{ app()->getLocale() === 'ar' ? 'ترتيب العرض' : 'Sort Order' }}
                    </label>
                    <input type="number" id="cl-sort" name="sort_order" value="0" class="form-control text-sm">
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" name="is_active" value="1" id="cl-active" class="form-checkbox rounded text-sky-600" checked>
                    <label for="cl-active" class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                        {{ app()->getLocale() === 'ar' ? 'الفئة نشطة ومتاحة' : 'Active & Selectable' }}
                    </label>
                </div>

                <div class="pt-4 border-t border-gray-100 dark:border-gray-800 flex items-center justify-end gap-2">
                    <button type="button" id="btn-cancel" onclick="resetClassificationForm()" class="btn btn-secondary text-xs hidden">
                        {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                    </button>
                    <button type="submit" id="btn-submit" class="btn btn-primary font-bold text-xs shadow-sm w-full">
                        <i class="fa-solid fa-floppy-disk mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'حفظ الفئة' : 'Save Classification' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function editClassification(item) {
            document.getElementById('form-title-text').innerText = '{{ app()->getLocale() === 'ar' ? 'تعديل الفئة: ' : 'Edit Tier: ' }}' + item.code;
            const form = document.getElementById('classification-form');
            form.action = '/admin/mr/classifications/' + item.id;
            document.getElementById('method-container').innerHTML = '<input type="hidden" name="_method" value="PUT">';

            document.getElementById('cl-code').value = item.code;
            document.getElementById('cl-label').value = item.label;
            document.getElementById('cl-points').value = item.points;
            document.getElementById('cl-visits').value = item.required_visits;
            document.getElementById('cl-sort').value = item.sort_order;
            document.getElementById('cl-active').checked = !!item.is_active;

            document.getElementById('btn-cancel').classList.remove('hidden');
            document.getElementById('btn-submit').innerText = '{{ app()->getLocale() === 'ar' ? 'تحديث الفئة' : 'Update Classification' }}';
        }

        function resetClassificationForm() {
            document.getElementById('form-title-text').innerText = '{{ app()->getLocale() === 'ar' ? 'إضافة فئة جديدة' : 'Add New Tier' }}';
            const form = document.getElementById('classification-form');
            form.action = '{{ route('admin.mr.classifications.store') }}';
            document.getElementById('method-container').innerHTML = '';
            form.reset();
            document.getElementById('btn-cancel').classList.add('hidden');
            document.getElementById('btn-submit').innerText = '{{ app()->getLocale() === 'ar' ? 'حفظ الفئة' : 'Save Classification' }}';
        }
    </script>
</x-layouts.admin>
