<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'التخصصات الطبية' : 'Medical Specialties Directory'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'إدارة التخصصات الطبية وتصنيف الأطباء والمراكز الطبية حسب التخصص' : 'Manage medical specialty categories and associate doctor portfolios accordingly.'"
    :breadcrumbs="[
        (app()->getLocale() === 'ar' ? 'نظام المندوب الطبي' : 'MR CRM') => route('admin.mr.live-map'),
        (app()->getLocale() === 'ar' ? 'التخصصات الطبية' : 'Specialties') => route('admin.mr.specialties.index')
    ]"
>
    <x-slot name="actions">
        <div class="flex items-center gap-2">
            <a href="{{ request()->fullUrlWithQuery(['export' => 'xlsx']) }}" class="btn btn-secondary text-sm font-bold shadow-sm flex items-center gap-2" title="Export Specialties">
                <i class="fa-solid fa-file-excel text-emerald-500"></i>
                <span>{{ app()->getLocale() === 'ar' ? 'تصدير إكسيل (.xlsx)' : 'Export Excel (.xlsx)' }}</span>
            </a>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Specialties Table (2 Cols) -->
        <div class="lg:col-span-2 card p-0 overflow-hidden shadow-sm border border-gray-100 dark:border-gray-800">
            <div class="p-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between bg-gray-50/50 dark:bg-gray-800/40">
                <h3 class="font-bold text-sm text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fa-solid fa-heart-pulse text-rose-500"></i>
                    {{ app()->getLocale() === 'ar' ? 'دليل التخصصات المسجلة' : 'Registered Medical Specialties' }}
                </h3>
                <span class="badge badge-outline text-xs">{{ $specialties->count() }} Specialties</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm table bz-sortable-table" data-table-sortable="true">
                    <thead class="bg-gray-50 dark:bg-gray-800/60 text-gray-600 dark:text-gray-400 font-semibold border-b border-gray-200 dark:border-gray-700 text-xs">
                        <tr>
                            <th class="px-5 py-3.5">{{ app()->getLocale() === 'ar' ? 'الرمز' : 'Code' }}</th>
                            <th class="px-5 py-3.5">{{ app()->getLocale() === 'ar' ? 'اسم التخصص' : 'Specialty Name' }}</th>
                            <th class="px-5 py-3.5">{{ app()->getLocale() === 'ar' ? 'الوصف' : 'Description' }}</th>
                            <th class="px-5 py-3.5 text-center">{{ app()->getLocale() === 'ar' ? 'عدد الأطباء' : 'Doctors' }}</th>
                            <th class="px-5 py-3.5 text-right no-sort" data-no-sort>{{ app()->getLocale() === 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($specialties as $sp)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition-colors">
                                <td class="px-5 py-4 font-mono font-bold text-xs text-sky-600 dark:text-sky-400">
                                    {{ $sp->code }}
                                </td>
                                <td class="px-5 py-4 font-bold text-gray-900 dark:text-white">
                                    {{ $sp->name }}
                                </td>
                                <td class="px-5 py-4 text-xs text-gray-500 max-w-xs truncate">
                                    {{ $sp->description ?: '—' }}
                                </td>
                                <td class="px-5 py-4 text-center font-bold text-gray-700 dark:text-gray-300">
                                    {{ $sp->contacts_count }}
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button type="button" onclick="editSpecialty({{ json_encode($sp) }})" class="btn btn-sm btn-ghost p-1.5 text-gray-500 hover:text-amber-500" title="Edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        @if($sp->contacts_count == 0)
                                            <form method="POST" action="{{ route('admin.mr.specialties.destroy', $sp->id) }}" onsubmit="return confirm('Delete this specialty?')" class="inline">
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
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-8 text-center text-gray-400">
                                    {{ app()->getLocale() === 'ar' ? 'لا توجد تخصصات مسجلة بعد.' : 'No specialties registered yet.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add / Edit Specialty Card (1 Col) -->
        <div class="card p-6 shadow-sm border border-gray-100 dark:border-gray-800">
            <h3 id="form-title" class="font-bold text-sm text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-800 pb-2 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-plus-circle text-sky-500"></i>
                <span id="form-title-text">{{ app()->getLocale() === 'ar' ? 'إضافة تخصص جديد' : 'Add New Specialty' }}</span>
            </h3>

            <form id="specialty-form" method="POST" action="{{ route('admin.mr.specialties.store') }}" class="space-y-4">
                @csrf
                <div id="method-container"></div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                        {{ app()->getLocale() === 'ar' ? 'اسم التخصص *' : 'Specialty Name *' }}
                    </label>
                    <input type="text" id="sp-name" name="name" required class="form-control text-sm" placeholder="e.g. Cardiology, Neurology">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                        {{ app()->getLocale() === 'ar' ? 'كود التخصص *' : 'Specialty Code *' }}
                    </label>
                    <input type="text" id="sp-code" name="code" required class="form-control text-sm uppercase font-mono" placeholder="e.g. CARD, NEUR">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">
                        {{ app()->getLocale() === 'ar' ? 'وصف التخصص' : 'Description' }}
                    </label>
                    <textarea id="sp-desc" name="description" rows="3" class="form-control text-sm" placeholder="Optional notes or scope..."></textarea>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" name="is_active" value="1" id="sp-active" class="form-checkbox rounded text-sky-600" checked>
                    <label for="sp-active" class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                        {{ app()->getLocale() === 'ar' ? 'التخصص نشط ومتاح للأطباء' : 'Active & Selectable' }}
                    </label>
                </div>

                <div class="pt-4 border-t border-gray-100 dark:border-gray-800 flex items-center justify-end gap-2">
                    <button type="button" id="btn-cancel" onclick="resetSpecialtyForm()" class="btn btn-secondary text-xs hidden">
                        {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                    </button>
                    <button type="submit" id="btn-submit" class="btn btn-primary font-bold text-xs shadow-sm w-full">
                        <i class="fa-solid fa-floppy-disk mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'حفظ التخصص' : 'Save Specialty' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function editSpecialty(item) {
            document.getElementById('form-title-text').innerText = '{{ app()->getLocale() === 'ar' ? 'تعديل التخصص: ' : 'Edit Specialty: ' }}' + item.name;
            const form = document.getElementById('specialty-form');
            form.action = '/admin/mr/specialties/' + item.id;
            document.getElementById('method-container').innerHTML = '<input type="hidden" name="_method" value="PUT">';

            document.getElementById('sp-name').value = item.name;
            document.getElementById('sp-code').value = item.code;
            document.getElementById('sp-desc').value = item.description || '';
            document.getElementById('sp-active').checked = !!item.is_active;

            document.getElementById('btn-cancel').classList.remove('hidden');
            document.getElementById('btn-submit').innerText = '{{ app()->getLocale() === 'ar' ? 'تحديث التخصص' : 'Update Specialty' }}';
        }

        function resetSpecialtyForm() {
            document.getElementById('form-title-text').innerText = '{{ app()->getLocale() === 'ar' ? 'إضافة تخصص جديد' : 'Add New Specialty' }}';
            const form = document.getElementById('specialty-form');
            form.action = '{{ route('admin.mr.specialties.store') }}';
            document.getElementById('method-container').innerHTML = '';
            form.reset();
            document.getElementById('btn-cancel').classList.add('hidden');
            document.getElementById('btn-submit').innerText = '{{ app()->getLocale() === 'ar' ? 'حفظ التخصص' : 'Save Specialty' }}';
        }
    </script>
</x-layouts.admin>
