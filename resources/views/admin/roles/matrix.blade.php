<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'مصفوفة الصلاحيات المتقدمة للأدوار' : 'Granular Permission Matrix'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'استعراض وإدارة الصلاحيات الدقيقة لكافة الأدوار على مستوى جميع أقسام ووظائف النظام (CRM، المبيعات، الموارد البشرية، المخزون، الإدارة)' : 'Cross-functional permission matrix mapping all system roles against every functional section and CRUD operation.'"
    :breadcrumbs="[
        (__('admin.menu.access_control') ?? 'Access Control') => route('admin.roles.index'),
        (__('admin.menu.roles') ?? 'Roles') => route('admin.roles.index'),
        (app()->getLocale() === 'ar' ? 'مصفوفة الصلاحيات' : 'Permission Matrix') => route('admin.roles.matrix')
    ]"
>
    <x-slot name="actions">
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('admin.roles.create') }}" class="btn btn-primary font-bold text-xs sm:text-sm shadow-sm bg-[#0A4F78] hover:bg-[#062B49] text-white">
                <i class="fa-solid fa-plus mr-1.5 ml-1.5"></i>
                <span>{{ app()->getLocale() === 'ar' ? 'إنشاء دور مخصص جديد' : 'Create Custom Role' }}</span>
            </a>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary font-bold text-xs sm:text-sm">
                <i class="fa-solid fa-shield-halved mr-1.5 ml-1.5"></i>
                <span>{{ app()->getLocale() === 'ar' ? 'قائمة الأدوار' : 'Roles List' }}</span>
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary font-bold text-xs sm:text-sm">
                <i class="fa-solid fa-users mr-1.5 ml-1.5"></i>
                <span>{{ app()->getLocale() === 'ar' ? 'المستخدمون والموظفون' : 'Staff Users' }}</span>
            </a>
        </div>
    </x-slot>

    <!-- Top Navigation Tabs (Aligned with Blue Zone Design System) -->
    <div class="mb-6 space-y-4">
        <div class="flex items-center gap-2 border-b border-slate-200 dark:border-[#15456E] pb-3 flex-wrap">
            <a href="{{ route('admin.roles.index') }}" 
               class="px-4 py-2 rounded-xl text-sm font-bold transition-all text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-[#062B49]">
                <i class="fa-solid fa-shield-halved mr-1.5 ml-1.5"></i>
                {{ app()->getLocale() === 'ar' ? 'الأدوار النشطة' : 'Active Roles' }}
                <span class="ml-1.5 mr-1.5 px-2 py-0.5 text-xs rounded-full bg-slate-200 dark:bg-[#031827] text-slate-700 dark:text-slate-300">{{ $totalRolesCount }}</span>
            </a>

            <a href="{{ route('admin.roles.matrix') }}" 
               class="px-4 py-2 rounded-xl text-sm font-black transition-all bg-[#0A4F78] text-white shadow-sm flex items-center gap-1.5">
                <i class="fa-solid fa-table-cells"></i>
                <span>{{ app()->getLocale() === 'ar' ? 'مصفوفة الصلاحيات المتقدمة' : 'Granular Permission Matrix' }}</span>
                <span class="ml-1 mr-1 px-2 py-0.5 text-xs rounded-full bg-white/20 text-white font-mono">{{ $totalModulesCount }} {{ app()->getLocale() === 'ar' ? 'قسم' : 'sections' }}</span>
            </a>

            <a href="{{ route('admin.roles.index', ['status' => 'trashed']) }}" 
               class="px-4 py-2 rounded-xl text-sm font-bold transition-all text-slate-600 dark:text-slate-300 hover:bg-rose-50 dark:hover:bg-rose-950/20">
                <i class="fa-solid fa-trash-can mr-1.5 ml-1.5"></i>
                {{ app()->getLocale() === 'ar' ? 'سلة المحذوفات' : 'Trash Archive' }}
            </a>
        </div>
    </div>

    <!-- Matrix Summary KPI Chips -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
        <div class="card p-4 border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] rounded-2xl shadow-sm transition-colors text-center">
            <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block mb-1">
                {{ app()->getLocale() === 'ar' ? 'الأدوار المعرفة' : 'Configured Roles' }}
            </span>
            <span class="text-2xl font-black text-[#0A4F78] dark:text-sky-400">{{ $totalRolesCount }}</span>
            <span class="text-[10px] text-slate-400 block mt-0.5">{{ app()->getLocale() === 'ar' ? 'أدوار نشطة بالنظام' : 'Active system roles' }}</span>
        </div>

        <div class="card p-4 border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] rounded-2xl shadow-sm transition-colors text-center">
            <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block mb-1">
                {{ app()->getLocale() === 'ar' ? 'أقسام ووظائف النظام' : 'Monitored Sections' }}
            </span>
            <span class="text-2xl font-black text-teal-600 dark:text-teal-400">{{ $totalModulesCount }}</span>
            <span class="text-[10px] text-slate-400 block mt-0.5">{{ count($categorizedModules) }} {{ app()->getLocale() === 'ar' ? 'مجالات وظيفية' : 'functional domains' }}</span>
        </div>

        <div class="card p-4 border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] rounded-2xl shadow-sm transition-colors text-center">
            <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block mb-1">
                {{ app()->getLocale() === 'ar' ? 'إجمالي كادر الموظفين' : 'Assigned Staff Members' }}
            </span>
            <span class="text-2xl font-black text-sky-600 dark:text-sky-400">{{ $totalStaffCount }}</span>
            <span class="text-[10px] text-slate-400 block mt-0.5">{{ app()->getLocale() === 'ar' ? 'مستخدم مربوط بأدوار' : 'Users attached to roles' }}</span>
        </div>

        <div class="card p-4 border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] rounded-2xl shadow-sm transition-colors text-center">
            <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block mb-1">
                {{ app()->getLocale() === 'ar' ? 'التحكم الإجرائي (CRUD)' : 'CRUD Action Levels' }}
            </span>
            <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ count($actions) }}</span>
            <span class="text-[10px] text-slate-400 block mt-0.5">View | Create | Edit | Delete</span>
        </div>
    </div>

    <!-- Domain Filter Bar -->
    <div class="flex items-center gap-2 mb-4 overflow-x-auto pb-1">
        <button type="button" onclick="filterMatrixDomain('all')" id="domain-pill-all"
            class="domain-filter-pill px-3 py-1.5 rounded-lg text-xs font-bold transition-all bg-[#0A4F78] text-white shadow-xs cursor-pointer">
            <i class="fa-solid fa-layer-group mr-1 ml-1"></i>
            {{ app()->getLocale() === 'ar' ? 'كافة الأقسام والمجالات' : 'All Sections' }} ({{ $totalModulesCount }})
        </button>

        @foreach($categorizedModules as $domainKey => $domainMeta)
            <button type="button" onclick="filterMatrixDomain('{{ $domainKey }}')" id="domain-pill-{{ $domainKey }}"
                class="domain-filter-pill px-3 py-1.5 rounded-lg text-xs font-bold transition-all bg-white dark:bg-[#062B49] text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-[#15456E] hover:border-[#0A4F78] dark:hover:border-sky-400 cursor-pointer">
                <i class="fa-solid {{ $domainMeta['icon'] }} mr-1 ml-1 text-sky-500"></i>
                {{ app()->getLocale() === 'ar' ? $domainMeta['label']['ar'] : $domainMeta['label']['en'] }}
                <span class="text-[10px] opacity-70">({{ count($domainMeta['modules']) }})</span>
            </button>
        @endforeach
    </div>

    <!-- Main Granular Permission Matrix Table -->
    <div class="card p-0 overflow-hidden shadow-sm border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] rounded-2xl mb-8 transition-colors">
        <div class="p-4 border-b border-slate-200/80 dark:border-[#15456E] flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 bg-slate-50/80 dark:bg-[#031827]/70">
            <div>
                <h3 class="font-bold text-sm sm:text-base text-slate-900 dark:text-white flex items-center gap-2 m-0">
                    <i class="fa-solid fa-table text-[#0A4F78] dark:text-sky-400"></i>
                    {{ app()->getLocale() === 'ar' ? 'مصفوفة الصلاحيات التفصيلية عبر الأدوار' : 'Complete Functional Permission Matrix' }}
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 mb-0">
                    {{ app()->getLocale() === 'ar' ? 'دليل الرموز: V = عرض | C = إضافة | E = تعديل | D = حذف | 🟢 = مصرح | ⚪ = محجوب' : 'Legend: V = View | C = Create | E = Edit | D = Delete | 🟢 = Granted | ⚪ = Restricted' }}
                </p>
            </div>

            <!-- Action Legend Badges -->
            <div class="flex items-center gap-1.5 text-[11px] font-bold">
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                    <i class="fa-solid fa-check text-[10px]"></i> {{ app()->getLocale() === 'ar' ? 'متاح ومفعل' : 'Granted' }}
                </span>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-slate-100 text-slate-500 dark:bg-[#031827] dark:text-slate-400">
                    <i class="fa-solid fa-minus text-[10px]"></i> {{ app()->getLocale() === 'ar' ? 'محجوب' : 'Restricted' }}
                </span>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300">
                    <i class="fa-solid fa-crown text-[10px]"></i> {{ app()->getLocale() === 'ar' ? 'شامل (*)' : 'Wildcard (*)' }}
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse" id="matrixTable">
                <thead>
                    <tr class="bg-slate-50 dark:bg-[#031827]/90 text-slate-800 dark:text-slate-200 border-b border-slate-200 dark:border-[#15456E]">
                        <!-- Left Column: System Section / Module -->
                        <th class="px-5 py-4 min-w-[260px] sticky left-0 z-20 bg-slate-50 dark:bg-[#031827] border-r dark:border-l border-slate-200/80 dark:border-[#15456E]">
                            <div class="font-extrabold text-xs uppercase tracking-wider text-slate-600 dark:text-slate-400">
                                {{ app()->getLocale() === 'ar' ? 'القسم / الوظيفة' : 'System Module / Section' }}
                            </div>
                        </th>

                        <!-- Columns for each Active Role -->
                        @foreach($roles as $role)
                            @php
                                $roleMeta = $matrixData[$role->id] ?? null;
                                $isSuper = in_array(strtolower($role->name), ['super admin', 'admin']);
                            @endphp
                            <th class="px-4 py-4 min-w-[170px] text-center border-l border-slate-200/60 dark:border-[#15456E]/60 align-top">
                                <div class="flex flex-col items-center">
                                    <div class="flex items-center gap-1.5 justify-center mb-1">
                                        <span class="font-bold text-sm text-slate-900 dark:text-white">{{ $role->name }}</span>
                                        @if($isSuper)
                                            <i class="fa-solid fa-crown text-amber-500 text-xs" title="Super Admin"></i>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-1.5 mb-2">
                                        <span class="text-[10px] font-mono px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 dark:bg-[#031827] dark:text-slate-300">
                                            {{ $role->users_count }} {{ app()->getLocale() === 'ar' ? 'موظف' : 'staff' }}
                                        </span>
                                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded {{ ($roleMeta['coverage_pct'] ?? 0) >= 80 ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-sky-100 text-sky-800 dark:bg-sky-950 dark:text-sky-300' }}">
                                            {{ $roleMeta['coverage_pct'] ?? 0 }}%
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-1">
                                        <a href="{{ route('admin.roles.edit', $role->id) }}" class="text-[11px] font-bold text-[#0A4F78] dark:text-sky-400 hover:underline flex items-center gap-1" title="{{ app()->getLocale() === 'ar' ? 'تعديل الدور' : 'Edit Role' }}">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                            <span>{{ app()->getLocale() === 'ar' ? 'تعديل' : 'Edit' }}</span>
                                        </a>
                                        <span class="text-slate-300 dark:text-slate-600">|</span>
                                        <button type="button" onclick="openQuickMatrixModal({{ $role->id }}, '{{ addslashes($role->name) }}')" class="text-[11px] font-bold text-teal-600 dark:text-teal-400 hover:underline flex items-center gap-1 cursor-pointer">
                                            <i class="fa-solid fa-sliders"></i>
                                            <span>{{ app()->getLocale() === 'ar' ? 'تخصيص' : 'Customize' }}</span>
                                        </button>
                                    </div>
                                </div>
                            </th>
                        @endforeach
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100 dark:divide-[#15456E]/60">
                    @foreach($categorizedModules as $domainKey => $domainMeta)
                        <!-- Domain Section Header Row -->
                        <tr class="domain-row domain-{{ $domainKey }} bg-slate-100/80 dark:bg-[#031827]/95 border-y border-slate-200 dark:border-[#15456E]">
                            <td colspan="{{ count($roles) + 1 }}" class="px-5 py-2.5">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-md bg-[#0A4F78] text-white flex items-center justify-center text-xs">
                                            <i class="fa-solid {{ $domainMeta['icon'] }}"></i>
                                        </div>
                                        <span class="font-extrabold text-xs text-slate-800 dark:text-slate-100 uppercase tracking-wide">
                                            {{ app()->getLocale() === 'ar' ? $domainMeta['label']['ar'] : $domainMeta['label']['en'] }}
                                        </span>
                                        <span class="text-[11px] font-normal text-slate-500 dark:text-slate-400">
                                            ({{ count($domainMeta['modules']) }} {{ app()->getLocale() === 'ar' ? 'أقسام وظيفية' : 'sections' }})
                                        </span>
                                    </div>
                                </div>
                            </td>
                        </tr>

                        <!-- Modules in Domain -->
                        @foreach($domainMeta['modules'] as $modKey => $modMeta)
                            <tr class="module-row domain-{{ $domainKey }} hover:bg-slate-50/70 dark:hover:bg-[#031827]/50 transition-colors">
                                <!-- Module Name & Key (Sticky Left) -->
                                <td class="px-5 py-3 sticky left-0 z-10 bg-white dark:bg-[#062B49] border-r dark:border-l border-slate-200/80 dark:border-[#15456E]">
                                    <div class="flex items-start gap-2.5">
                                        <div class="mt-0.5 text-slate-400">
                                            <i class="fa-solid fa-chevron-right rtl:rotate-180 text-[10px]"></i>
                                        </div>
                                        <div>
                                            <div class="font-bold text-xs text-slate-900 dark:text-white">
                                                {{ app()->getLocale() === 'ar' ? $modMeta['name_ar'] : $modMeta['name_en'] }}
                                            </div>
                                            <div class="text-[10px] text-slate-400 font-mono">
                                                admin.{{ $modKey }}
                                            </div>
                                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 mb-0 max-w-sm">
                                                {{ app()->getLocale() === 'ar' ? $modMeta['desc_ar'] : $modMeta['desc_en'] }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Role Matrix Cells -->
                                @foreach($roles as $role)
                                    @php
                                        $roleInfo = $matrixData[$role->id] ?? null;
                                        $isWildcard = !empty($roleInfo['is_wildcard']);
                                        $modPerms = $roleInfo['matrix'][$modKey] ?? [];
                                        $allGranted = !empty($modPerms['view']) && !empty($modPerms['create']) && !empty($modPerms['edit']) && !empty($modPerms['delete']);
                                        $noneGranted = empty($modPerms['view']) && empty($modPerms['create']) && empty($modPerms['edit']) && empty($modPerms['delete']);
                                    @endphp

                                    <td class="px-3 py-3 text-center border-l border-slate-200/60 dark:border-[#15456E]/60 align-middle">
                                        @if($isWildcard)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black bg-amber-100 text-amber-900 dark:bg-amber-950/80 dark:text-amber-300 border border-amber-200 dark:border-amber-800" title="{{ app()->getLocale() === 'ar' ? 'صلاحية شاملة غير مقيدة لكافة وظائف النظام' : 'Unrestricted Wildcard Authority' }}">
                                                <i class="fa-solid fa-crown text-[9px] text-amber-500"></i>
                                                {{ app()->getLocale() === 'ar' ? 'شامل (*)' : 'Wildcard (*)' }}
                                            </span>
                                        @elseif($allGranted)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/70 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                                <i class="fa-solid fa-circle-check text-[9px]"></i>
                                                {{ app()->getLocale() === 'ar' ? 'وصول كامل (CRUD)' : 'Full (CRUD)' }}
                                            </span>
                                        @elseif($noneGranted)
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-slate-100 text-slate-400 dark:bg-[#031827] dark:text-slate-600 text-xs" title="{{ app()->getLocale() === 'ar' ? 'محجوب تماماً' : 'No Access' }}">
                                                <i class="fa-solid fa-minus"></i>
                                            </span>
                                        @else
                                            <!-- Detailed Granular Badges: V C E D -->
                                            <div class="inline-flex items-center gap-1 bg-slate-50 dark:bg-[#031827] p-1 rounded-lg border border-slate-200/80 dark:border-[#15456E]">
                                                @foreach(['view' => 'V', 'create' => 'C', 'edit' => 'E', 'delete' => 'D'] as $act => $badgeLabel)
                                                    @php $allowed = !empty($modPerms[$act]); @endphp
                                                    <span class="w-5 h-5 rounded flex items-center justify-center text-[10px] font-black {{ $allowed ? 'bg-emerald-600 text-white dark:bg-emerald-500' : 'bg-slate-200 text-slate-400 dark:bg-slate-800 dark:text-slate-600' }}" 
                                                          title="{{ ucfirst($act) }}: {{ $allowed ? 'Granted' : 'Denied' }}">
                                                        {{ $badgeLabel }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Role Configuration Modal -->
    <div id="quick-matrix-modal" class="fixed inset-0 z-[1050] overflow-y-auto bg-slate-950/70 p-3 sm:p-4 hidden backdrop-blur-sm" onclick="if(event.target === this) closeQuickMatrixModal()">
        <div class="min-h-full flex items-center justify-center p-0">
            <div class="card max-w-2xl w-full p-0 shadow-2xl relative flex flex-col max-h-[90vh] border border-slate-200 dark:border-[#15456E] rounded-2xl overflow-hidden bg-white dark:bg-[#062B49] transition-colors">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-[#15456E] px-6 py-4 flex-shrink-0 bg-slate-50/70 dark:bg-[#031827]/70">
                    <h3 class="font-bold text-base text-slate-900 dark:text-white flex items-center gap-2.5 m-0">
                        <div class="w-8 h-8 rounded-lg bg-sky-50 dark:bg-sky-950/70 text-[#0A4F78] dark:text-sky-300 flex items-center justify-center border border-sky-100 dark:border-sky-900 flex-shrink-0">
                            <i class="fa-solid fa-sliders text-sm"></i>
                        </div>
                        <div>
                            <span>{{ app()->getLocale() === 'ar' ? 'تخصيص صلاحيات الدور السريع' : 'Customize Role Permissions' }}</span>
                            <p class="text-[11px] font-normal text-slate-500 dark:text-slate-400 m-0" id="modalRoleSubtitle">
                                Role Name
                            </p>
                        </div>
                    </h3>
                    <button type="button" onclick="closeQuickMatrixModal()" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-[#031827] transition-colors cursor-pointer">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.roles.matrix.update') }}" class="flex flex-col flex-1 min-h-0 overflow-hidden m-0 p-0" id="quickMatrixForm">
                    @csrf
                    <input type="hidden" name="role_id" id="modalRoleId" value="">

                    <div class="flex-1 overflow-y-auto px-6 py-4 space-y-4">
                        <!-- Quick Templates Bar -->
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-[#031827]/70 border border-slate-200/80 dark:border-[#15456E]">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 block mb-2">
                                <i class="fa-solid fa-wand-magic-sparkles text-amber-500 mr-1 ml-1"></i>
                                {{ app()->getLocale() === 'ar' ? 'تطبيق قالب صلاحيات جاهز بضغطة واحدة:' : 'Apply Preset Role Template:' }}
                            </span>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                @foreach($templates as $tmplKey => $tmpl)
                                    <button type="button" onclick="applyTemplateToModal('{{ $tmplKey }}')" class="p-2 rounded-lg text-left rtl:text-right border border-slate-200 dark:border-[#15456E] hover:border-[#0A4F78] hover:bg-sky-50 dark:hover:bg-[#062B49] transition-all cursor-pointer">
                                        <span class="font-bold text-xs text-slate-900 dark:text-white block">{{ app()->getLocale() === 'ar' ? $tmpl['name_ar'] : $tmpl['name'] }}</span>
                                        <span class="text-[10px] text-slate-400 line-clamp-1">{{ $tmpl['description'] }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <!-- Wildcard Checkbox -->
                        <div class="p-3 rounded-xl bg-amber-50/50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900/50 flex items-center justify-between">
                            <div>
                                <span class="font-bold text-xs text-amber-900 dark:text-amber-200 block">
                                    {{ app()->getLocale() === 'ar' ? 'صلاحيات المدير العام الشاملة (*)' : 'Grant Super Admin Wildcard (*)' }}
                                </span>
                                <span class="text-[11px] text-amber-700 dark:text-amber-400">
                                    {{ app()->getLocale() === 'ar' ? 'يمنح هذا الدور وصولاً غير مقيد لكافة وظائف وشاشات النظام.' : 'Grants full, unrestricted access to every past, present, and future module.' }}
                                </span>
                            </div>
                            <input type="checkbox" name="is_wildcard" id="modalWildcardCheckbox" value="1" class="form-check-input">
                        </div>

                        <!-- Quick Bulk Actions -->
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-slate-600 dark:text-slate-400">{{ app()->getLocale() === 'ar' ? 'تحديد جماعي للصلاحيات:' : 'Bulk Selection:' }}</span>
                            <div class="flex gap-2">
                                <button type="button" onclick="document.querySelectorAll('.modal-perm-cb').forEach(cb => cb.checked = true)" class="text-[#0A4F78] dark:text-sky-400 font-bold hover:underline cursor-pointer">
                                    {{ app()->getLocale() === 'ar' ? 'تحديد الكل' : 'Select All' }}
                                </button>
                                <span>•</span>
                                <button type="button" onclick="document.querySelectorAll('.modal-perm-cb').forEach(cb => cb.checked = false)" class="text-rose-600 font-bold hover:underline cursor-pointer">
                                    {{ app()->getLocale() === 'ar' ? 'إلغاء الكل' : 'Deselect All' }}
                                </button>
                            </div>
                        </div>

                        <!-- Categorized Modules Checkbox Grid -->
                        <div class="space-y-4">
                            @foreach($categorizedModules as $dKey => $dMeta)
                                <div class="border border-slate-200 dark:border-[#15456E] rounded-xl overflow-hidden">
                                    <div class="bg-slate-50 dark:bg-[#031827] px-4 py-2 flex items-center justify-between border-b border-slate-200 dark:border-[#15456E]">
                                        <div class="flex items-center gap-2">
                                            <i class="fa-solid {{ $dMeta['icon'] }} text-[#0A4F78] dark:text-sky-400 text-xs"></i>
                                            <span class="font-bold text-xs text-slate-900 dark:text-white">{{ app()->getLocale() === 'ar' ? $dMeta['label']['ar'] : $dMeta['label']['en'] }}</span>
                                        </div>
                                        <button type="button" onclick="toggleDomainCheckboxes('{{ $dKey }}')" class="text-[11px] text-[#0A4F78] dark:text-sky-400 font-semibold hover:underline cursor-pointer">
                                            {{ app()->getLocale() === 'ar' ? 'تحديد هذا المجال' : 'Toggle Domain' }}
                                        </button>
                                    </div>

                                    <div class="p-3 divide-y divide-slate-100 dark:divide-[#15456E]/40 text-xs">
                                        @foreach($dMeta['modules'] as $mKey => $mMeta)
                                            <div class="py-2 first:pt-0 last:pb-0 flex items-center justify-between flex-wrap gap-2">
                                                <div>
                                                    <span class="font-bold text-slate-800 dark:text-slate-200 block">{{ app()->getLocale() === 'ar' ? $mMeta['name_ar'] : $mMeta['name_en'] }}</span>
                                                    <span class="text-[10px] text-slate-400 font-mono">admin.{{ $mKey }}</span>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    @foreach(['view' => 'View', 'create' => 'Create', 'edit' => 'Edit', 'delete' => 'Delete'] as $actKey => $actLabel)
                                                        <label class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-600 dark:text-slate-300 cursor-pointer">
                                                            <input type="checkbox" name="permissions[{{ $mKey }}][{{ $actKey }}]" value="1" class="form-check-input modal-perm-cb domain-cb-{{ $dKey }} cb-{{ $mKey }}-{{ $actKey }}">
                                                            <span>{{ $actLabel }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="px-6 py-3.5 bg-slate-50/90 dark:bg-[#031827]/80 border-t border-slate-100 dark:border-[#15456E] flex items-center justify-end gap-2 flex-shrink-0">
                        <button type="button" onclick="closeQuickMatrixModal()" class="btn btn-secondary text-xs px-4 py-2 cursor-pointer">
                            {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                        </button>
                        <button type="submit" class="btn btn-primary font-bold text-xs px-5 py-2 shadow-sm bg-[#0A4F78] hover:bg-[#062B49] text-white cursor-pointer">
                            <i class="fa-solid fa-floppy-disk mr-1.5 ml-1.5"></i>
                            {{ app()->getLocale() === 'ar' ? 'حفظ واعتماد الصلاحيات' : 'Save & Apply Permissions' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const MATRIX_DATA = @json($matrixData);
        const TEMPLATES = @json($templates);

        function filterMatrixDomain(domainKey) {
            document.querySelectorAll('.domain-filter-pill').forEach(btn => {
                btn.classList.remove('bg-[#0A4F78]', 'text-white', 'shadow-xs');
                btn.classList.add('bg-white', 'dark:bg-[#062B49]', 'text-slate-700', 'dark:text-slate-300');
            });

            const activeBtn = document.getElementById('domain-pill-' + domainKey);
            if (activeBtn) {
                activeBtn.classList.remove('bg-white', 'dark:bg-[#062B49]', 'text-slate-700', 'dark:text-slate-300');
                activeBtn.classList.add('bg-[#0A4F78]', 'text-white', 'shadow-xs');
            }

            if (domainKey === 'all') {
                document.querySelectorAll('.domain-row, .module-row').forEach(row => row.classList.remove('hidden'));
            } else {
                document.querySelectorAll('.domain-row, .module-row').forEach(row => row.classList.add('hidden'));
                document.querySelectorAll('.domain-' + domainKey).forEach(row => row.classList.remove('hidden'));
            }
        }

        function openQuickMatrixModal(roleId, roleName) {
            document.getElementById('modalRoleId').value = roleId;
            document.getElementById('modalRoleSubtitle').textContent = roleName + ' (ID #' + roleId + ')';

            // Reset checkboxes
            document.querySelectorAll('.modal-perm-cb').forEach(cb => cb.checked = false);
            const wildcardCb = document.getElementById('modalWildcardCheckbox');
            if (wildcardCb) wildcardCb.checked = false;

            const roleInfo = MATRIX_DATA[roleId];
            if (roleInfo) {
                if (roleInfo.is_wildcard) {
                    if (wildcardCb) wildcardCb.checked = true;
                    document.querySelectorAll('.modal-perm-cb').forEach(cb => cb.checked = true);
                } else if (roleInfo.matrix) {
                    for (const modKey in roleInfo.matrix) {
                        for (const actKey in roleInfo.matrix[modKey]) {
                            if (roleInfo.matrix[modKey][actKey]) {
                                const targetCb = document.querySelector('.cb-' + modKey + '-' + actKey);
                                if (targetCb) targetCb.checked = true;
                            }
                        }
                    }
                }
            }

            const m = document.getElementById('quick-matrix-modal');
            if (m) {
                m.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeQuickMatrixModal() {
            const m = document.getElementById('quick-matrix-modal');
            if (m) {
                m.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }

        function toggleDomainCheckboxes(domainKey) {
            const cbs = document.querySelectorAll('.domain-cb-' + domainKey);
            const allChecked = Array.from(cbs).every(cb => cb.checked);
            cbs.forEach(cb => cb.checked = !allChecked);
        }

        function applyTemplateToModal(templateKey) {
            const tmpl = TEMPLATES[templateKey];
            if (!tmpl || !tmpl.permissions) return;

            document.querySelectorAll('.modal-perm-cb').forEach(cb => cb.checked = false);
            const wildcardCb = document.getElementById('modalWildcardCheckbox');

            const isWildcard = (Array.isArray(tmpl.permissions) && tmpl.permissions.includes('*')) ||
                               tmpl.permissions === '*' ||
                               (typeof tmpl.permissions === 'object' && tmpl.permissions !== null && tmpl.permissions['*']);

            if (isWildcard) {
                if (wildcardCb) wildcardCb.checked = true;
                document.querySelectorAll('.modal-perm-cb').forEach(cb => cb.checked = true);
                return;
            }

            if (wildcardCb) wildcardCb.checked = false;

            if (Array.isArray(tmpl.permissions)) {
                tmpl.permissions.forEach(p => {
                    if (typeof p === 'string' && p.includes('.')) {
                        const parts = p.split('.');
                        const m = parts[0];
                        const a = parts[1];
                        if (a === '*') {
                            document.querySelectorAll('[class*="cb-' + m + '-"]').forEach(cb => cb.checked = true);
                        } else {
                            const target = document.querySelector('.cb-' + m + '-' + a);
                            if (target) target.checked = true;
                        }
                    } else if (typeof p === 'string') {
                        document.querySelectorAll('[class*="cb-' + p + '-"]').forEach(cb => cb.checked = true);
                    }
                });
                return;
            }

            if (typeof tmpl.permissions === 'object' && tmpl.permissions !== null) {
                for (const modKey in tmpl.permissions) {
                    const actions = tmpl.permissions[modKey];
                    if (actions && typeof actions === 'object') {
                        for (const actKey in actions) {
                            if (actions[actKey]) {
                                const targetCb = document.querySelector('.cb-' + modKey + '-' + actKey);
                                if (targetCb) targetCb.checked = true;
                            }
                        }
                    } else if (actions === true) {
                        document.querySelectorAll('[class*="cb-' + modKey + '-"]').forEach(cb => cb.checked = true);
                    }
                }
            }
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeQuickMatrixModal();
            }
        });
    </script>
    @endpush
</x-layouts.admin>
