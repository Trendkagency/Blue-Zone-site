<x-layouts.admin 
    :pageTitle="__('admin.roles.edit_title', ['name' => $role['name']])" 
    :pageSubtitle="__('admin.roles.edit_subtitle')"
    :breadcrumbs="[
        (__('admin.menu.roles') ?? 'Roles') => route('admin.roles.index'),
        (app()->getLocale() === 'ar' ? 'مصفوفة الصلاحيات' : 'Permission Matrix') => route('admin.roles.matrix'),
        $role['name'] => route('admin.roles.edit', $role['id'])
    ]"
>
    @php
        $perms = (array) ($role['permissions'] ?? []);
        $isWildcard = in_array('*', $perms, true) || in_array('all', $perms, true) || isset($perms['*']);
        $isSystemRole = in_array(strtolower($role['name']), ['super admin', 'admin']);
    @endphp

    <form method="POST" action="{{ route('admin.roles.update', $role['id']) }}">
        @csrf
        @method('PUT')

        <div class="flex items-center justify-between gap-3 mb-6 flex-wrap">
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.roles.matrix') }}" class="btn btn-secondary text-xs sm:text-sm font-bold">
                    <i class="fa-solid fa-table-cells mr-1.5 ml-1.5 text-sky-500"></i>
                    {{ app()->getLocale() === 'ar' ? 'عرض مصفوفة الصلاحيات' : 'View Full Matrix' }}
                </a>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary text-xs sm:text-sm">{{ __('app.actions.cancel') }}</a>
                <button type="submit" class="btn btn-primary font-bold text-xs sm:text-sm shadow-sm bg-[#0A4F78] hover:bg-[#062B49] text-white">
                    <i class="fa-solid fa-floppy-disk mr-1.5 ml-1.5"></i> {{ __('admin.roles.save_role') }}
                </button>
            </div>
        </div>

        <div class="space-y-6">
            <!-- 1. Role Identity Card -->
            <div class="card p-6 border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] rounded-2xl shadow-sm transition-colors">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-[#15456E] pb-3 mb-4">
                    <h3 class="font-bold text-base text-slate-900 dark:text-white flex items-center gap-2 m-0">
                        <i class="fa-solid fa-id-badge text-[#0A4F78] dark:text-sky-400"></i>
                        {{ __('admin.roles.role_identity') }}
                    </h3>
                    @if($isSystemRole)
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300">
                            <i class="fa-solid fa-shield text-[10px]"></i> {{ app()->getLocale() === 'ar' ? 'دور نظام محمي' : 'Protected System Role' }}
                        </span>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                            {{ __('admin.roles.role_name') }} *
                        </label>
                        <input type="text" name="name" value="{{ old('name', $role['name']) }}" required class="form-control text-sm w-full font-bold">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">
                            {{ __('admin.roles.description') }}
                        </label>
                        <input type="text" name="description" value="{{ old('description', $role['description']) }}" class="form-control text-sm w-full" placeholder="{{ app()->getLocale() === 'ar' ? 'وصف طبيعة عمل هذا الدور...' : 'Brief summary of role responsibilities...' }}">
                    </div>
                </div>

                <!-- Wildcard Super Authority Toggle -->
                <div class="mt-4 p-3.5 rounded-xl bg-amber-50/60 dark:bg-amber-950/20 border border-amber-200/80 dark:border-amber-900/40 flex items-center justify-between">
                    <div>
                        <span class="font-bold text-xs text-amber-900 dark:text-amber-200 block">
                            {{ app()->getLocale() === 'ar' ? 'صلاحية المدير العام الشاملة (*)' : 'Super Admin Root Authority (*)' }}
                        </span>
                        <span class="text-[11px] text-amber-700 dark:text-amber-400">
                            {{ app()->getLocale() === 'ar' ? 'تفعيل هذا الخيار يمنح المستخدمين التابعين لهذا الدور صلاحيات كاملة وتلقائية لكافة الشاشات الحالية والمستقبلية.' : 'Grants full, automatic authority across every current and future module in the application.' }}
                        </span>
                    </div>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_wildcard" id="isWildcardToggle" value="1" {{ $isWildcard ? 'checked' : '' }} class="form-check-input" onchange="handleWildcardToggle(this.checked)">
                        <span class="text-xs font-bold text-amber-900 dark:text-amber-200">{{ app()->getLocale() === 'ar' ? 'شامل (*)' : 'Wildcard (*)' }}</span>
                    </label>
                </div>
            </div>

            <!-- 2. Preset Role Templates Bar -->
            <div class="card p-4 border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] rounded-2xl shadow-sm transition-colors">
                <span class="text-xs font-bold text-slate-700 dark:text-slate-300 block mb-2">
                    <i class="fa-solid fa-wand-magic-sparkles text-amber-500 mr-1 ml-1"></i>
                    {{ app()->getLocale() === 'ar' ? 'تطبيق قالب صلاحيات سريع على هذا الدور:' : 'Apply Preset Role Template:' }}
                </span>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2">
                    @foreach($templates as $tmplKey => $tmpl)
                        <button type="button" onclick="applyTemplate('{{ $tmplKey }}')" class="p-2.5 rounded-xl text-left rtl:text-right border border-slate-200/80 dark:border-[#15456E] hover:border-[#0A4F78] hover:bg-sky-50/60 dark:hover:bg-[#031827] transition-all cursor-pointer">
                            <span class="font-bold text-xs text-slate-900 dark:text-white block">{{ app()->getLocale() === 'ar' ? $tmpl['name_ar'] : $tmpl['name'] }}</span>
                            <span class="text-[10px] text-slate-400 line-clamp-1 mt-0.5">{{ $tmpl['description'] }}</span>
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- 3. Granular Permission Matrix -->
            <div class="card p-6 border border-slate-200/80 dark:border-[#15456E] bg-white dark:bg-[#062B49] rounded-2xl shadow-sm transition-colors" id="matrixSection">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-[#15456E] pb-3 mb-6 flex-wrap gap-2">
                    <div>
                        <h3 class="font-bold text-base text-slate-900 dark:text-white m-0 flex items-center gap-2">
                            <i class="fa-solid fa-table-cells text-[#0A4F78] dark:text-sky-400"></i>
                            {{ __('admin.roles.permission_matrix') }}
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 mb-0">
                            {{ app()->getLocale() === 'ar' ? 'حدد الصلاحيات الدقيقة لهذا الدور عبر كافة أقسام ووظائف النظام:' : 'Configure precise CRUD privileges for this role across all system modules:' }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" class="btn btn-secondary btn-sm text-xs font-bold" onclick="document.querySelectorAll('.matrix-checkbox').forEach(cb => cb.checked = true);">
                            {{ __('admin.roles.select_all') }}
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm text-xs font-bold" onclick="document.querySelectorAll('.matrix-checkbox').forEach(cb => cb.checked = false);">
                            {{ __('admin.roles.deselect_all') }}
                        </button>
                    </div>
                </div>

                <!-- Domain Accordions -->
                <div class="space-y-6">
                    @foreach($categorizedModules as $domainKey => $domainMeta)
                        <div class="border border-slate-200/80 dark:border-[#15456E] rounded-2xl overflow-hidden bg-white dark:bg-[#062B49]">
                            <!-- Domain Header Bar -->
                            <div class="bg-slate-50 dark:bg-[#031827] px-5 py-3 flex items-center justify-between border-b border-slate-200/80 dark:border-[#15456E]">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-lg bg-[#0A4F78] text-white flex items-center justify-center text-xs">
                                        <i class="fa-solid {{ $domainMeta['icon'] }}"></i>
                                    </div>
                                    <div>
                                        <span class="font-bold text-xs sm:text-sm text-slate-900 dark:text-white">
                                            {{ app()->getLocale() === 'ar' ? $domainMeta['label']['ar'] : $domainMeta['label']['en'] }}
                                        </span>
                                        <span class="text-[11px] text-slate-400 block font-normal">
                                            {{ count($domainMeta['modules']) }} {{ app()->getLocale() === 'ar' ? 'أقسام مدمجة' : 'modules' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button type="button" onclick="toggleDomain('{{ $domainKey }}', true)" class="text-[11px] font-bold text-[#0A4F78] dark:text-sky-400 hover:underline cursor-pointer">
                                        {{ app()->getLocale() === 'ar' ? 'تحديد الكل' : 'Select All' }}
                                    </button>
                                    <span class="text-slate-300 dark:text-slate-600">|</span>
                                    <button type="button" onclick="toggleDomain('{{ $domainKey }}', false)" class="text-[11px] font-bold text-slate-500 hover:underline cursor-pointer">
                                        {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Clear' }}
                                    </button>
                                </div>
                            </div>

                            <!-- Modules Grid inside Domain -->
                            <div class="p-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($domainMeta['modules'] as $moduleKey => $moduleMeta)
                                    @php
                                        $modPerms = $perms[$moduleKey] ?? [];
                                        $domainWildcard = false;
                                        if (str_starts_with($moduleKey, 'mr_') && (in_array('mr.*', $perms, true) || in_array('mr', $perms, true))) $domainWildcard = true;
                                        if (str_starts_with($moduleKey, 'crm_') && (in_array('crm.*', $perms, true) || in_array('crm', $perms, true))) $domainWildcard = true;
                                        if (str_starts_with($moduleKey, 'hr_') && (in_array('hr.*', $perms, true) || in_array('hr', $perms, true))) $domainWildcard = true;
                                        $modWildcard = in_array("{$moduleKey}.*", $perms, true) || in_array($moduleKey, $perms, true);
                                    @endphp

                                    <div class="p-3.5 rounded-xl bg-slate-50/80 dark:bg-[#031827]/70 border border-slate-200/70 dark:border-[#15456E]/70 flex flex-col justify-between">
                                        <div>
                                            <div class="flex items-start justify-between gap-1 mb-1">
                                                <h4 class="font-bold text-xs text-slate-900 dark:text-white m-0">
                                                    {{ app()->getLocale() === 'ar' ? $moduleMeta['name_ar'] : $moduleMeta['name_en'] }}
                                                </h4>
                                                <span class="text-[9px] font-mono text-slate-400">admin.{{ $moduleKey }}</span>
                                            </div>
                                            <p class="text-[10px] text-slate-500 dark:text-slate-400 mb-2 line-clamp-2">
                                                {{ app()->getLocale() === 'ar' ? $moduleMeta['desc_ar'] : $moduleMeta['desc_en'] }}
                                            </p>
                                        </div>

                                        <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-200/60 dark:border-[#15456E]/60 text-xs">
                                            @foreach($actions as $actionKey => $actionMeta)
                                                @php
                                                    $isChecked = $isWildcard || $domainWildcard || $modWildcard 
                                                        || in_array("{$moduleKey}.{$actionKey}", $perms, true) 
                                                        || (isset($modPerms[$actionKey]) && $modPerms[$actionKey]);
                                                @endphp
                                                <label class="form-check flex items-center gap-1.5 cursor-pointer">
                                                    <input 
                                                        type="checkbox" 
                                                        name="permissions[{{ $moduleKey }}][{{ $actionKey }}]" 
                                                        class="form-check-input matrix-checkbox domain-cb-{{ $domainKey }} cb-{{ $moduleKey }}-{{ $actionKey }}" 
                                                        value="1" 
                                                        {{ $isChecked ? 'checked' : '' }}
                                                    >
                                                    <span class="text-[11px] font-semibold text-slate-700 dark:text-slate-300">
                                                        {{ app()->getLocale() === 'ar' ? $actionMeta['ar'] : $actionMeta['en'] }}
                                                    </span>
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
        </div>
    </form>

    @push('scripts')
    <script>
        const TEMPLATES = @json($templates);

        function handleWildcardToggle(checked) {
            if (checked) {
                document.querySelectorAll('.matrix-checkbox').forEach(cb => cb.checked = true);
            }
        }

        function toggleDomain(domainKey, status) {
            document.querySelectorAll('.domain-cb-' + domainKey).forEach(cb => cb.checked = status);
        }

        function applyTemplate(templateKey) {
            const tmpl = TEMPLATES[templateKey];
            if (!tmpl || !tmpl.permissions) return;

            document.querySelectorAll('.matrix-checkbox').forEach(cb => cb.checked = false);
            const wildcardCb = document.getElementById('isWildcardToggle');

            const isWildcard = (Array.isArray(tmpl.permissions) && tmpl.permissions.includes('*')) ||
                               tmpl.permissions === '*' ||
                               (typeof tmpl.permissions === 'object' && tmpl.permissions !== null && tmpl.permissions['*']);

            if (isWildcard) {
                if (wildcardCb) wildcardCb.checked = true;
                document.querySelectorAll('.matrix-checkbox').forEach(cb => cb.checked = true);
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
    </script>
    @endpush
</x-layouts.admin>
