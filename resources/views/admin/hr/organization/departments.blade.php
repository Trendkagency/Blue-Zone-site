<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'إدارة الأقسام والهيكل التنظيمي' : 'Departments Management'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'الهيكل الإداري، الأقسام، مدراء الوحدات، وتوزيع الموظفين' : 'Corporate Organizational Structure, Functional Departments & Unit Leadership'"
    :breadcrumbs="[
        (app()->getLocale() === 'ar' ? 'الموارد البشرية' : 'HR') => route('admin.hr.dashboard'),
        (app()->getLocale() === 'ar' ? 'الهيكل التنظيمي' : 'Organization') => route('admin.hr.organization.departments.index'),
        (app()->getLocale() === 'ar' ? 'الأقسام' : 'Departments') => route('admin.hr.organization.departments.index')
    ]"
>
    @php
        $isAr = app()->getLocale() === 'ar';
        $totalEmployeesInDepts = $departments->sum('employees_count');
        $activeDepts = $departments->where('is_active', true)->count();
        $managedDepts = $departments->filter(fn($d) => !empty($d->manager_employee_id))->count();
    @endphp

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white flex items-center gap-2.5">
                <i class="fa-solid fa-sitemap text-teal-500 text-2xl"></i>
                <span>{{ $isAr ? 'الأقسام والوحدات الإدارية' : 'Departments & Organizational Units' }}</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                {{ $isAr ? 'إدارة البنية الهيكلية وتعيين القيادات وتصنيف فرق العمل' : 'Manage corporate operational divisions, assign division managers, and organize workforce structure' }}
            </p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('admin.hr.organization.positions.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-700 hover:bg-slate-200 transition-colors">
                <i class="fa-solid fa-id-badge text-sky-500"></i>
                <span>{{ $isAr ? 'المسميات الوظيفية' : 'Job Positions' }}</span>
            </a>
            <a href="{{ route('admin.hr.organization.work-schedules.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-700 hover:bg-slate-200 transition-colors">
                <i class="fa-solid fa-business-time text-indigo-500"></i>
                <span>{{ $isAr ? 'ورديات العمل' : 'Work Schedules' }}</span>
            </a>
            <button type="button" onclick="openAddDeptModal()" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-black bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-500 hover:to-emerald-500 text-white shadow-md shadow-teal-600/25 transition-all">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>{{ $isAr ? 'إضافة قسم جديد' : 'Add New Department' }}</span>
            </button>
        </div>
    </div>

    <!-- Feedback Alerts -->
    @if(session('success'))
        <div class="p-4 rounded-xl mb-6 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-300 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-bold flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(isset($errors) && $errors->any())
        <div class="p-4 rounded-xl mb-6 bg-rose-50 dark:bg-rose-950/40 border border-rose-300 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-xs space-y-1">
            <div class="font-bold flex items-center gap-1.5">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>{{ $isAr ? 'يرجى مراجعة الأخطاء التالية:' : 'Please correct the following errors:' }}</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Department Summary KPIs -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="p-4 rounded-2xl bg-white dark:bg-[#071F33] border border-slate-200 dark:border-[#133957] shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 block mb-1">
                {{ $isAr ? 'إجمالي الأقسام' : 'Total Departments' }}
            </span>
            <div class="text-2xl font-black font-mono text-slate-900 dark:text-white">
                {{ $departments->total() }}
            </div>
            <span class="text-[10px] text-teal-600 dark:text-teal-400 mt-1 inline-flex items-center gap-1 font-bold">
                <i class="fa-solid fa-sitemap"></i> {{ $isAr ? 'وحدات تنظيمية معتمدة' : 'Registered Units' }}
            </span>
        </div>

        <div class="p-4 rounded-2xl bg-white dark:bg-[#071F33] border border-slate-200 dark:border-[#133957] shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 block mb-1">
                {{ $isAr ? 'الأقسام النشطة' : 'Active Divisions' }}
            </span>
            <div class="text-2xl font-black font-mono text-emerald-600 dark:text-emerald-400">
                {{ $activeDepts }}
            </div>
            <span class="text-[10px] text-slate-500 dark:text-slate-400 mt-1 block">
                {{ $isAr ? 'تعمل بكامل الصلاحيات' : 'Operational Status' }}
            </span>
        </div>

        <div class="p-4 rounded-2xl bg-white dark:bg-[#071F33] border border-slate-200 dark:border-[#133957] shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 block mb-1">
                {{ $isAr ? 'الأقسام ذات الإدارة المباشرة' : 'Assigned Managers' }}
            </span>
            <div class="text-2xl font-black font-mono text-sky-600 dark:text-cyan-400">
                {{ $managedDepts }}
            </div>
            <span class="text-[10px] text-sky-600 dark:text-cyan-400 mt-1 inline-flex items-center gap-1 font-bold">
                <i class="fa-solid fa-user-tie"></i> {{ $isAr ? 'تحت إشراف مباشر' : 'Active Department Heads' }}
            </span>
        </div>

        <div class="p-4 rounded-2xl bg-white dark:bg-[#071F33] border border-slate-200 dark:border-[#133957] shadow-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 block mb-1">
                {{ $isAr ? 'إجمالي القوى العاملة بالأقسام' : 'Departmental Workforce' }}
            </span>
            <div class="text-2xl font-black font-mono text-indigo-600 dark:text-indigo-400">
                {{ $totalEmployeesInDepts }}
            </div>
            <span class="text-[10px] text-slate-500 dark:text-slate-400 mt-1 block">
                {{ $isAr ? 'موظف مسجل بالهيكل' : 'Assigned Personnel' }}
            </span>
        </div>
    </div>

    <!-- Departments Data Table Card -->
    <div class="p-5 rounded-2xl bg-white dark:bg-[#071F33] border border-slate-200 dark:border-[#133957] shadow-xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-list-check text-teal-500"></i>
                <h3 class="text-sm font-black uppercase tracking-wider text-slate-900 dark:text-white m-0">
                    {{ $isAr ? 'سجل الأقسام الإدارية والوحدات' : 'Corporate Departments Directory' }}
                </h3>
            </div>
            <span class="text-xs font-bold text-slate-400">
                {{ $departments->total() }} {{ $isAr ? 'أقسام' : 'departments' }}
            </span>
        </div>

        <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-800">
            <table class="w-full text-xs text-start border-collapse">
                <thead>
                    <tr class="bg-slate-100 dark:bg-[#0A2942] text-slate-600 dark:text-slate-300 font-extrabold uppercase tracking-wider text-[10px] border-b border-slate-200 dark:border-slate-800">
                        <th class="py-3 px-3 text-start">{{ $isAr ? 'كود القسم' : 'Code' }}</th>
                        <th class="py-3 px-3 text-start">{{ $isAr ? 'اسم القسم (إنجليزي)' : 'Department Name (EN)' }}</th>
                        <th class="py-3 px-3 text-start">{{ $isAr ? 'اسم القسم (عربي)' : 'Department Name (AR)' }}</th>
                        <th class="py-3 px-3 text-start">{{ $isAr ? 'المدير المباشر' : 'Department Manager' }}</th>
                        <th class="py-3 px-3 text-center">{{ $isAr ? 'الوظائف' : 'Positions' }}</th>
                        <th class="py-3 px-3 text-center">{{ $isAr ? 'الموظفين' : 'Headcount' }}</th>
                        <th class="py-3 px-3 text-center">{{ $isAr ? 'الحالة' : 'Status' }}</th>
                        <th class="py-3 px-3 text-end">{{ $isAr ? 'الإجراءات' : 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800 text-slate-800 dark:text-slate-200">
                    @forelse($departments as $dept)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="py-3 px-3 font-mono font-black text-sky-600 dark:text-cyan-400">
                                <span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                                    {{ $dept->code }}
                                </span>
                            </td>
                            <td class="py-3 px-3 font-bold text-slate-900 dark:text-white">
                                {{ $dept->name_en }}
                                @if($dept->description)
                                    <div class="text-[10px] text-slate-400 font-normal mt-0.5 truncate max-w-xs">{{ $dept->description }}</div>
                                @endif
                            </td>
                            <td class="py-3 px-3 font-bold text-slate-900 dark:text-white font-arabic">
                                {{ $dept->name_ar }}
                            </td>
                            <td class="py-3 px-3 text-slate-600 dark:text-slate-300">
                                @if($dept->manager)
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-teal-500/20 text-teal-600 dark:text-teal-400 flex items-center justify-center font-bold text-[10px]">
                                            <i class="fa-solid fa-user-tie text-[9px]"></i>
                                        </div>
                                        <div>
                                            <span class="font-bold text-slate-900 dark:text-white block leading-tight">{{ $dept->manager->full_name }}</span>
                                            <span class="text-[9px] font-mono text-slate-400">{{ $dept->manager->employee_number }}</span>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-slate-400 font-italic text-[11px]">— {{ $isAr ? 'لم يعين' : 'Unassigned' }} —</span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-center">
                                <span class="px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 font-mono font-bold text-slate-700 dark:text-slate-300">
                                    {{ $dept->positions->count() }}
                                </span>
                            </td>
                            <td class="py-3 px-3 text-center">
                                <span class="px-2.5 py-1 rounded-lg bg-sky-500/10 text-sky-700 dark:text-cyan-300 font-mono font-black">
                                    {{ $dept->employees_count }}
                                </span>
                            </td>
                            <td class="py-3 px-3 text-center">
                                @if($dept->is_active)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/25">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        {{ $isAr ? 'نشط' : 'Active' }}
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-slate-200 dark:bg-slate-800 text-slate-500">
                                        {{ $isAr ? 'معطل' : 'Inactive' }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-end">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Edit Button -->
                                    <button type="button" 
                                        onclick="openEditDeptModal({{ json_encode($dept) }})" 
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-sky-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors" 
                                        title="{{ $isAr ? 'تعديل بيانات القسم' : 'Edit Department' }}">
                                        <i class="fa-regular fa-pen-to-square text-xs"></i>
                                    </button>

                                    <!-- Delete Button -->
                                    <form action="{{ route('admin.hr.organization.departments.destroy', $dept->id) }}" method="POST" onsubmit="return confirm('{{ $isAr ? 'هل أنت متأكد من رغبتك في حذف هذا القسم؟' : 'Are you sure you want to delete this department?' }}');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors" title="{{ $isAr ? 'حذف القسم' : 'Delete Department' }}">
                                            <i class="fa-regular fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 text-slate-400 font-medium">
                                {{ $isAr ? 'لا توجد أقسام مسجلة حتى الآن. انقر على زر إضافة قسم جديد للبدء.' : 'No departments registered yet. Click Add New Department to get started.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-3">
            {{ $departments->links() }}
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL: ADD NEW DEPARTMENT                  -->
    <!-- ========================================== -->
    <div id="addDeptModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm hidden p-4">
        <div class="w-full max-w-lg bg-white dark:bg-[#071F33] rounded-3xl border border-slate-200 dark:border-[#133957] p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-teal-500/20 text-teal-600 dark:text-teal-400 flex items-center justify-center">
                        <i class="fa-solid fa-sitemap text-sm"></i>
                    </div>
                    <h3 class="text-sm font-black text-slate-900 dark:text-white m-0">
                        {{ $isAr ? 'إضافة قسم جديد' : 'Add New Department' }}
                    </h3>
                </div>
                <button type="button" onclick="closeAddDeptModal()" class="text-slate-400 hover:text-slate-600 text-lg">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form action="{{ route('admin.hr.organization.departments.store') }}" method="POST" class="space-y-3.5 text-xs">
                @csrf
                
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                        {{ $isAr ? 'كود القسم الفريد (Code) *' : 'Department Code *' }}
                    </label>
                    <input type="text" name="code" required placeholder="e.g. HR, BIO-DEV, SALES, FIN" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-[#0A2942] border border-slate-300 dark:border-[#1E4E73] text-slate-900 dark:text-white font-mono uppercase">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                            {{ $isAr ? 'الاسم بالإنجليزية *' : 'Department Name (English) *' }}
                        </label>
                        <input type="text" name="name_en" required placeholder="e.g. Human Resources" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-[#0A2942] border border-slate-300 dark:border-[#1E4E73] text-slate-900 dark:text-white font-bold">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                            {{ $isAr ? 'الاسم بالعربية *' : 'Department Name (Arabic) *' }}
                        </label>
                        <input type="text" name="name_ar" required placeholder="e.g. إدارة الموارد البشرية" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-[#0A2942] border border-slate-300 dark:border-[#1E4E73] text-slate-900 dark:text-white font-bold">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                        {{ $isAr ? 'مدير القسم المباشر' : 'Department Manager' }}
                    </label>
                    <select name="manager_employee_id" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-[#0A2942] border border-slate-300 dark:border-[#1E4E73] text-slate-900 dark:text-white font-semibold">
                        <option value="">{{ $isAr ? '-- بدون مدير حالياً --' : '-- No Manager Currently Assigned --' }}</option>
                        @foreach($managers as $mgr)
                            <option value="{{ $mgr->id }}">{{ $mgr->full_name }} ({{ $mgr->employee_number }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                        {{ $isAr ? 'الوصف والمهام' : 'Description / Responsibilities' }}
                    </label>
                    <textarea name="description" rows="2" placeholder="{{ $isAr ? 'نبذة عن مهام القسم والمسؤوليات...' : 'Brief summary of unit operational responsibilities...' }}" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-[#0A2942] border border-slate-300 dark:border-[#1E4E73] text-slate-900 dark:text-white"></textarea>
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" name="is_active" id="dept_active_add" value="1" checked class="w-4 h-4 rounded text-teal-600 focus:ring-teal-500 border-slate-300 dark:border-slate-700">
                    <label for="dept_active_add" class="font-bold text-slate-700 dark:text-slate-300 cursor-pointer">
                        {{ $isAr ? 'تفعيل القسم بالهيكل التنظيمي' : 'Active Department Status' }}
                    </label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" onclick="closeAddDeptModal()" class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                        {{ $isAr ? 'إلغاء' : 'Cancel' }}
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl text-xs font-black bg-teal-600 hover:bg-teal-500 text-white shadow-md transition-all">
                        {{ $isAr ? 'حفظ القسم' : 'Save Department' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL: EDIT DEPARTMENT                     -->
    <!-- ========================================== -->
    <div id="editDeptModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm hidden p-4">
        <div class="w-full max-w-lg bg-white dark:bg-[#071F33] rounded-3xl border border-slate-200 dark:border-[#133957] p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-sky-500/20 text-sky-600 dark:text-cyan-400 flex items-center justify-center">
                        <i class="fa-regular fa-pen-to-square text-sm"></i>
                    </div>
                    <h3 class="text-sm font-black text-slate-900 dark:text-white m-0">
                        {{ $isAr ? 'تعديل بيانات القسم' : 'Edit Department Profile' }}
                    </h3>
                </div>
                <button type="button" onclick="closeEditDeptModal()" class="text-slate-400 hover:text-slate-600 text-lg">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="editDeptForm" method="POST" class="space-y-3.5 text-xs">
                @csrf
                @method('PUT')
                
                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                        {{ $isAr ? 'كود القسم *' : 'Department Code *' }}
                    </label>
                    <input type="text" id="edit_code" name="code" required class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-[#0A2942] border border-slate-300 dark:border-[#1E4E73] text-slate-900 dark:text-white font-mono uppercase">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                            {{ $isAr ? 'الاسم بالإنجليزية *' : 'Department Name (English) *' }}
                        </label>
                        <input type="text" id="edit_name_en" name="name_en" required class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-[#0A2942] border border-slate-300 dark:border-[#1E4E73] text-slate-900 dark:text-white font-bold">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                            {{ $isAr ? 'الاسم بالعربية *' : 'Department Name (Arabic) *' }}
                        </label>
                        <input type="text" id="edit_name_ar" name="name_ar" required class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-[#0A2942] border border-slate-300 dark:border-[#1E4E73] text-slate-900 dark:text-white font-bold">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                        {{ $isAr ? 'المدير المباشر' : 'Department Manager' }}
                    </label>
                    <select id="edit_manager_id" name="manager_employee_id" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-[#0A2942] border border-slate-300 dark:border-[#1E4E73] text-slate-900 dark:text-white font-semibold">
                        <option value="">{{ $isAr ? '-- بدون مدير --' : '-- No Manager --' }}</option>
                        @foreach($managers as $mgr)
                            <option value="{{ $mgr->id }}">{{ $mgr->full_name }} ({{ $mgr->employee_number }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 dark:text-slate-300 mb-1">
                        {{ $isAr ? 'الوصف' : 'Description' }}
                    </label>
                    <textarea id="edit_description" name="description" rows="2" class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-[#0A2942] border border-slate-300 dark:border-[#1E4E73] text-slate-900 dark:text-white"></textarea>
                </div>

                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" name="is_active" id="edit_is_active" value="1" class="w-4 h-4 rounded text-teal-600 focus:ring-teal-500 border-slate-300 dark:border-slate-700">
                    <label for="edit_is_active" class="font-bold text-slate-700 dark:text-slate-300 cursor-pointer">
                        {{ $isAr ? 'تفعيل القسم' : 'Active Status' }}
                    </label>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" onclick="closeEditDeptModal()" class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                        {{ $isAr ? 'إلغاء' : 'Cancel' }}
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl text-xs font-black bg-sky-600 hover:bg-sky-500 text-white shadow-md transition-all">
                        {{ $isAr ? 'حفظ التعديلات' : 'Update Department' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Interaction Script -->
    <script>
        function openAddDeptModal() {
            document.getElementById('addDeptModal')?.classList.remove('hidden');
        }
        function closeAddDeptModal() {
            document.getElementById('addDeptModal')?.classList.add('hidden');
        }

        function openEditDeptModal(dept) {
            const form = document.getElementById('editDeptForm');
            form.action = '{{ url("admin/hr/organization/departments") }}/' + dept.id;
            
            document.getElementById('edit_code').value = dept.code || '';
            document.getElementById('edit_name_en').value = dept.name_en || '';
            document.getElementById('edit_name_ar').value = dept.name_ar || '';
            document.getElementById('edit_manager_id').value = dept.manager_employee_id || '';
            document.getElementById('edit_description').value = dept.description || '';
            document.getElementById('edit_is_active').checked = !!dept.is_active;

            document.getElementById('editDeptModal')?.classList.remove('hidden');
        }

        function closeEditDeptModal() {
            document.getElementById('editDeptModal')?.classList.add('hidden');
        }
    </script>
</x-layouts.admin>
