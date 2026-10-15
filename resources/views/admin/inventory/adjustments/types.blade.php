<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'أنواع تسويات الجرد' : 'Stock Adjustment Types'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'التحكم الكامل في أسباب وأنواع التسويات، تحديد أثرها على رصيد المخزون، وتوثيق سجلات التدقيق.' : 'Full control over adjustment categories, stock impact rules (Inward/Outward/Reconciliation), and audit classifications.'"
    :breadcrumbs="[
        (app()->getLocale() === 'ar' ? 'المخزون' : 'Inventory') => route('admin.inventory.dashboard'),
        (app()->getLocale() === 'ar' ? 'تسويات الجرد' : 'Stock Adjustments') => route('admin.inventory.adjustments'),
        (app()->getLocale() === 'ar' ? 'أنواع التسويات' : 'Adjustment Types') => route('admin.inventory.adjustments.types.index')
    ]"
>
    <x-slot name="actions">
        <!-- New Type Modal Trigger -->
        <button type="button" onclick="openCreateTypeModal()" class="btn btn-primary font-bold text-xs sm:text-sm shadow-sm">
            <i class="fa-solid fa-plus mr-1.5 ml-1.5"></i>
            {{ app()->getLocale() === 'ar' ? 'إضافة نوع تسوية جديد' : 'New Adjustment Type' }}
        </button>

        <!-- Back to Adjustments Log -->
        <a href="{{ route('admin.inventory.adjustments') }}" class="btn btn-secondary font-semibold text-xs sm:text-sm">
            <i class="fa-solid fa-arrow-left rtl:rotate-180 mr-1.5 ml-1.5"></i>
            {{ app()->getLocale() === 'ar' ? 'سجل تسويات الجرد' : 'Adjustments Log' }}
        </a>
    </x-slot>

    <div class="space-y-6">

        <!-- Flash Notices -->
        @if(session('status'))
            <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-800 dark:text-emerald-300 text-sm flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400 text-lg"></i>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if(isset($errors) && $errors->has('type_error'))
            <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 text-rose-800 dark:text-rose-300 text-sm flex items-center gap-3">
                <i class="fa-solid fa-triangle-exclamation text-rose-600 dark:text-rose-400 text-lg"></i>
                <span>{{ $errors->first('type_error') }}</span>
            </div>
        @endif

        @if(isset($errors) && $errors->any() && !$errors->has('type_error'))
            <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/20 text-rose-800 dark:text-rose-300 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- 1. Key Metrics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- Total Types -->
            <div class="card p-5 bg-white dark:bg-[#062B49] border border-slate-200/90 dark:border-[#0E4A7A] rounded-2xl shadow-xs border-s-4 border-s-indigo-500">
                <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider">{{ app()->getLocale() === 'ar' ? 'إجمالي أنواع التسويات' : 'Total Types' }}</span>
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-tags text-sm"></i>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">
                    {{ $totalTypes }}
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">{{ app()->getLocale() === 'ar' ? 'نوع مسجل' : 'registered' }}</span>
                </div>
                <div class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium">
                    {{ $activeTypes }} {{ app()->getLocale() === 'ar' ? 'نوع نشط حالياً' : 'currently active' }}
                </div>
            </div>

            <!-- Inward Types (+) -->
            <div class="card p-5 bg-white dark:bg-[#062B49] border border-slate-200/90 dark:border-[#0E4A7A] rounded-2xl shadow-xs border-s-4 border-s-emerald-500">
                <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider">{{ app()->getLocale() === 'ar' ? 'أنواع الإضافة (+)' : 'Inward Additions (+)' }}</span>
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-arrow-trend-up text-sm"></i>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-black text-emerald-600 dark:text-emerald-400 tracking-tight font-mono">
                    {{ $increaseTypes }}
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">{{ app()->getLocale() === 'ar' ? 'نوع إضافة' : 'types' }}</span>
                </div>
                <div class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium">
                    {{ app()->getLocale() === 'ar' ? 'تزيد رصيد المخزون تلقائياً' : 'Increments physical stock' }}
                </div>
            </div>

            <!-- Outward Types (-) -->
            <div class="card p-5 bg-white dark:bg-[#062B49] border border-slate-200/90 dark:border-[#0E4A7A] rounded-2xl shadow-xs border-s-4 border-s-rose-500">
                <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider">{{ app()->getLocale() === 'ar' ? 'أنواع الخصم والإتلاف (-)' : 'Outward Deductions (-)' }}</span>
                    <div class="w-8 h-8 rounded-lg bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-arrow-trend-down text-sm"></i>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-black text-rose-600 dark:text-rose-400 tracking-tight font-mono">
                    {{ $decreaseTypes }}
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">{{ app()->getLocale() === 'ar' ? 'نوع خصم' : 'types' }}</span>
                </div>
                <div class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium">
                    {{ app()->getLocale() === 'ar' ? 'تخصم من المخزون (توالف/عجز)' : 'Decrements physical stock' }}
                </div>
            </div>

            <!-- Audit Ledger Alignment -->
            <div class="card p-5 bg-white dark:bg-[#062B49] border border-slate-200/90 dark:border-[#0E4A7A] rounded-2xl shadow-xs border-s-4 border-s-sky-500">
                <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider">{{ app()->getLocale() === 'ar' ? 'التوافق والتوثيق' : 'Regulatory Audit' }}</span>
                    <div class="w-8 h-8 rounded-lg bg-sky-50 dark:bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-200 dark:border-sky-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-shield-halved text-sm"></i>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-black text-sky-600 dark:text-sky-400 tracking-tight">
                    100%
                </div>
                <div class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium">
                    {{ app()->getLocale() === 'ar' ? 'تتبع لحظي مطابق لمعايير SFDA' : 'Complete auditable ledger traceability' }}
                </div>
            </div>

        </div>

        <!-- 2. Search & Filter Bar -->
        <div class="card p-4 sm:p-5 bg-white dark:bg-[#062B49] border border-slate-200/90 dark:border-[#0E4A7A] rounded-2xl shadow-xs">
            <form method="GET" action="{{ route('admin.inventory.adjustments.types.index') }}" class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 flex-wrap">
                <!-- Search -->
                <div class="flex-1 min-w-[220px]">
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute top-1/2 -translate-y-1/2 start-3.5 text-slate-400 text-xs pointer-events-none"></i>
                        <input type="text" name="search" value="{{ $search }}" class="form-control ps-9 text-xs sm:text-sm w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-xl" placeholder="{{ app()->getLocale() === 'ar' ? 'بحث باسم النوع، الرمز، الوصف...' : 'Search type name, code, description...' }}">
                    </div>
                </div>

                <!-- Direction Filter -->
                <div class="w-full sm:w-auto min-w-[170px]">
                    <select name="direction" class="form-select text-xs sm:text-sm w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-xl" onchange="this.form.submit()">
                        <option value="all">{{ app()->getLocale() === 'ar' ? 'جميع اتجاهات الأثر' : 'All Stock Impact Directions' }}</option>
                        <option value="increase" {{ $selectedDirection === 'increase' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'إضافة للمخزون (+)' : 'Stock Inward (+)' }}</option>
                        <option value="decrease" {{ $selectedDirection === 'decrease' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'خصم من المخزون (-)' : 'Stock Outward (-)' }}</option>
                        <option value="neutral" {{ $selectedDirection === 'neutral' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'تسوية فارق جرد (±)' : 'Reconciliation (±)' }}</option>
                    </select>
                </div>

                <!-- Status Filter -->
                <div class="w-full sm:w-auto min-w-[150px]">
                    <select name="status" class="form-select text-xs sm:text-sm w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-xl" onchange="this.form.submit()">
                        <option value="all">{{ app()->getLocale() === 'ar' ? 'جميع الحالات' : 'All Statuses' }}</option>
                        <option value="active" {{ $selectedStatus === 'active' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'نشط فقط' : 'Active Only' }}</option>
                        <option value="inactive" {{ $selectedStatus === 'inactive' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'معطل فقط' : 'Inactive Only' }}</option>
                    </select>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-2">
                    <button type="submit" class="btn btn-secondary btn-sm text-xs font-bold px-3">
                        <i class="fa-solid fa-filter mr-1 ml-1 text-slate-400"></i>
                        {{ app()->getLocale() === 'ar' ? 'تصفية' : 'Filter' }}
                    </button>
                    @if($search || $selectedDirection !== 'all' || $selectedStatus !== 'all')
                        <a href="{{ route('admin.inventory.adjustments.types.index') }}" class="btn btn-neutral btn-sm text-xs" title="Reset Filters">
                            <i class="fa-solid fa-rotate-left mr-1 ml-1"></i>
                            {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Reset' }}
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- 3. Types Control Table Card -->
        <div class="card bg-white dark:bg-[#062B49] border border-slate-200/90 dark:border-[#0E4A7A] rounded-2xl shadow-xs overflow-hidden">
            <div class="table-responsive">
                <table class="table w-full text-start">
                    <thead class="bg-slate-50 dark:bg-[#041E34] border-b border-slate-200/80 dark:border-[#0E4A7A] text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        <tr>
                            <th class="py-3.5 px-4 text-start">{{ app()->getLocale() === 'ar' ? 'نوع التسوية' : 'Adjustment Type' }}</th>
                            <th class="py-3.5 px-4 text-start">{{ app()->getLocale() === 'ar' ? 'الرمز البرمجي' : 'Code Key' }}</th>
                            <th class="py-3.5 px-4 text-start">{{ app()->getLocale() === 'ar' ? 'أثر حركة المخزون' : 'Stock Direction / Effect' }}</th>
                            <th class="py-3.5 px-4 text-center">{{ app()->getLocale() === 'ar' ? 'الاستخدام في السجل' : 'Ledger Usage' }}</th>
                            <th class="py-3.5 px-4 text-center">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                            <th class="py-3.5 px-4 text-center">{{ app()->getLocale() === 'ar' ? 'الترتيب' : 'Sort' }}</th>
                            <th class="py-3.5 px-4 text-center w-28">{{ app()->getLocale() === 'ar' ? 'الإجراءات والتحكم' : 'Actions' }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                        @forelse($types as $type)
                            @php
                                $badge = $type->direction_badge;
                            @endphp
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors">
                                <!-- Type Name & Description -->
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-2">
                                        <div class="font-bold text-slate-900 dark:text-white text-sm">
                                            {{ $type->name_en }}
                                        </div>
                                        @if($type->is_system)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                                {{ app()->getLocale() === 'ar' ? 'أساسي' : 'Core System' }}
                                            </span>
                                        @endif
                                    </div>
                                    @if($type->name_ar)
                                        <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                            {{ $type->name_ar }}
                                        </div>
                                    @endif
                                    @if($type->description)
                                        <div class="text-[11px] text-slate-400 italic mt-1 line-clamp-1">
                                            {{ $type->description }}
                                        </div>
                                    @endif
                                </td>

                                <!-- Code Key -->
                                <td class="py-3.5 px-4 font-mono font-bold text-slate-600 dark:text-slate-300">
                                    {{ $type->code }}
                                </td>

                                <!-- Direction Badge -->
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-bold border {{ $badge['bg'] }}">
                                        <i class="fa-solid {{ $badge['icon'] }}"></i>
                                        {{ app()->getLocale() === 'ar' ? $badge['label_ar'] : $badge['label_en'] }}
                                    </span>
                                </td>

                                <!-- Movements Count -->
                                <td class="py-3.5 px-4 text-center">
                                    <span class="font-mono text-xs font-bold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 px-2.5 py-1 rounded-lg">
                                        {{ $type->movements_count }} {{ app()->getLocale() === 'ar' ? 'حركة' : 'logs' }}
                                    </span>
                                </td>

                                <!-- Status -->
                                <td class="py-3.5 px-4 text-center">
                                    <form action="{{ route('admin.inventory.adjustments.types.toggle-status', $type->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold transition-all {{ $type->is_active ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-500/20' : 'bg-slate-500/10 text-slate-500 dark:text-slate-400 hover:bg-slate-500/20' }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $type->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                            {{ $type->is_active ? (app()->getLocale() === 'ar' ? 'نشط' : 'Active') : (app()->getLocale() === 'ar' ? 'معطل' : 'Inactive') }}
                                        </button>
                                    </form>
                                </td>

                                <!-- Sort Order -->
                                <td class="py-3.5 px-4 text-center font-mono text-slate-500 dark:text-slate-400">
                                    {{ $type->sort_order }}
                                </td>

                                <!-- Actions -->
                                <td class="py-3.5 px-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <!-- Edit Button -->
                                        <button type="button" 
                                                onclick="openEditTypeModal({{ json_encode($type) }})" 
                                                class="btn btn-secondary btn-sm p-1.5 rounded-lg text-sky-500 hover:text-sky-600" 
                                                title="{{ app()->getLocale() === 'ar' ? 'تعديل النوع' : 'Edit Type' }}">
                                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                                        </button>

                                        <!-- Delete Button -->
                                        @if(!$type->is_system && $type->movements_count == 0)
                                            <form action="{{ route('admin.inventory.adjustments.types.destroy', $type->id) }}" method="POST" class="inline" onsubmit="return confirm('{{ app()->getLocale() === 'ar' ? 'هل أنت متأكد من حذف هذا النوع؟' : 'Delete this adjustment type?' }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-secondary btn-sm p-1.5 rounded-lg text-rose-500 hover:text-rose-600" title="{{ app()->getLocale() === 'ar' ? 'حذف' : 'Delete' }}">
                                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-slate-300 dark:text-slate-700 p-1.5 cursor-not-allowed" title="{{ $type->is_system ? (app()->getLocale() === 'ar' ? 'نوع نظام أساسي محمي' : 'Core protected system type') : (app()->getLocale() === 'ar' ? 'مرتبط بسجلات تدقيق تاريخية' : 'Linked to historical audit movements') }}">
                                                <i class="fa-solid fa-lock text-xs"></i>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-12 text-slate-400">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 mx-auto flex items-center justify-center text-xl mb-3 opacity-60">
                                        <i class="fa-solid fa-tags"></i>
                                    </div>
                                    <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ app()->getLocale() === 'ar' ? 'لا توجد أنواع مطابقة' : 'No adjustment types found' }}</h4>
                                    <p class="text-xs text-slate-400 mt-1">{{ app()->getLocale() === 'ar' ? 'يمكنك إضافة نوع جديد من الزر بالأعلى.' : 'Create a new adjustment type using the top action button.' }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($types->hasPages())
                <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                    <x-pagination :currentPage="$currentPage" :totalPages="$totalPages" :totalItems="$types->total()" />
                </div>
            @endif
        </div>

    </div>

    <!-- 4. Interactive "Create Adjustment Type" Modal -->
    <div id="createTypeModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 hidden transition-opacity">
        <div class="bg-white dark:bg-[#062B49] border border-slate-200 dark:border-[#0E4A7A] rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden animate-in fade-in zoom-in-95 duration-200">
            <!-- Modal Header -->
            <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-plus text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">
                            {{ app()->getLocale() === 'ar' ? 'إضافة نوع تسوية جرد جديد' : 'New Stock Adjustment Type' }}
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            {{ app()->getLocale() === 'ar' ? 'تحديد مواصفات نوع التسوية والأثر التلقائي على الرصيد.' : 'Configure type specifications and automatic stock delta sign.' }}
                        </p>
                    </div>
                </div>
                <button type="button" onclick="closeCreateTypeModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-white text-lg">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Modal Form -->
            <form action="{{ route('admin.inventory.adjustments.types.store') }}" method="POST" class="p-6 space-y-4">
                @csrf

                <!-- Name English & Arabic -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            {{ app()->getLocale() === 'ar' ? 'الاسم بالإنجليزية' : 'Type Name (English)' }} <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="name_en" required placeholder="e.g. Internal Clinic Testing" class="form-control text-sm w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-xl">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            {{ app()->getLocale() === 'ar' ? 'الاسم بالعربية' : 'Type Name (Arabic)' }}
                        </label>
                        <input type="text" name="name_ar" placeholder="مثال: فحص واختبار سريري" class="form-control text-sm w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-xl">
                    </div>
                </div>

                <!-- Stock Effect / Direction & Sort Order -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            {{ app()->getLocale() === 'ar' ? 'الأثر التلقائي على الرصيد' : 'Stock Direction Effect' }} <span class="text-rose-500">*</span>
                        </label>
                        <select name="direction" required class="form-select text-sm w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-xl">
                            <option value="decrease">{{ app()->getLocale() === 'ar' ? 'خصم من المخزون (-)' : 'Decrease Stock (- Outward)' }}</option>
                            <option value="increase">{{ app()->getLocale() === 'ar' ? 'إضافة للمخزون (+)' : 'Increase Stock (+ Inward)' }}</option>
                            <option value="neutral">{{ app()->getLocale() === 'ar' ? 'تسوية ومطابقة فارق جرد (±)' : 'Count Discrepancy (± Neutral)' }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            {{ app()->getLocale() === 'ar' ? 'ترتيب الظهور' : 'Sort Order' }}
                        </label>
                        <input type="number" name="sort_order" value="10" min="0" class="form-control text-sm w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-xl font-mono">
                    </div>
                </div>

                <!-- Description / SOP -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        {{ app()->getLocale() === 'ar' ? 'الوصف ومبررات التدقيق الرقابي' : 'Description & Audit SOP Rule' }}
                    </label>
                    <textarea name="description" rows="2" placeholder="{{ app()->getLocale() === 'ar' ? 'شرح الحالات التي يتم فيها استخدام هذا النوع من التسويات...' : 'Describe when this adjustment type should be applied in warehouse audits...' }}" class="form-control text-sm w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-xl"></textarea>
                </div>

                <!-- Active Toggle -->
                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" name="is_active" id="createIsActive" value="1" checked class="form-checkbox text-indigo-600 rounded">
                    <label for="createIsActive" class="text-xs font-bold text-slate-700 dark:text-slate-300 cursor-pointer">
                        {{ app()->getLocale() === 'ar' ? 'تفعيل هذا النوع فوراً للاستخدام في شاشات الجرد' : 'Active and available in stock adjustment modals immediately' }}
                    </label>
                </div>

                <!-- Actions -->
                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="closeCreateTypeModal()" class="btn btn-neutral text-xs sm:text-sm font-semibold">
                        {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                    </button>
                    <button type="submit" class="btn btn-primary text-xs sm:text-sm font-bold shadow-md">
                        <i class="fa-solid fa-check mr-1 ml-1"></i>
                        {{ app()->getLocale() === 'ar' ? 'حفظ نوع التسوية' : 'Save Adjustment Type' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 5. Interactive "Edit Adjustment Type" Modal -->
    <div id="editTypeModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 hidden transition-opacity">
        <div class="bg-white dark:bg-[#062B49] border border-slate-200 dark:border-[#0E4A7A] rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden animate-in fade-in zoom-in-95 duration-200">
            <!-- Modal Header -->
            <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-sky-50 dark:bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-200 dark:border-sky-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-pen-to-square text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">
                            {{ app()->getLocale() === 'ar' ? 'تعديل نوع التسوية' : 'Edit Stock Adjustment Type' }}
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400" id="editTypeSubtitle">
                            {{ app()->getLocale() === 'ar' ? 'تحديث مسميات وشروط نوع التسوية.' : 'Update adjustment type rules and properties.' }}
                        </p>
                    </div>
                </div>
                <button type="button" onclick="closeEditTypeModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-white text-lg">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Modal Form -->
            <form id="editTypeForm" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')

                <!-- Name English & Arabic -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            {{ app()->getLocale() === 'ar' ? 'الاسم بالإنجليزية' : 'Type Name (English)' }} <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="name_en" id="editNameEn" required class="form-control text-sm w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-xl">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            {{ app()->getLocale() === 'ar' ? 'الاسم بالعربية' : 'Type Name (Arabic)' }}
                        </label>
                        <input type="text" name="name_ar" id="editNameAr" class="form-control text-sm w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-xl">
                    </div>
                </div>

                <!-- Stock Effect / Direction & Sort Order -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            {{ app()->getLocale() === 'ar' ? 'الأثر التلقائي على الرصيد' : 'Stock Direction Effect' }} <span class="text-rose-500">*</span>
                        </label>
                        <select name="direction" id="editDirection" required class="form-select text-sm w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-xl">
                            <option value="decrease">{{ app()->getLocale() === 'ar' ? 'خصم من المخزون (-)' : 'Decrease Stock (- Outward)' }}</option>
                            <option value="increase">{{ app()->getLocale() === 'ar' ? 'إضافة للمخزون (+)' : 'Increase Stock (+ Inward)' }}</option>
                            <option value="neutral">{{ app()->getLocale() === 'ar' ? 'تسوية ومطابقة فارق جرد (±)' : 'Count Discrepancy (± Neutral)' }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                            {{ app()->getLocale() === 'ar' ? 'ترتيب الظهور' : 'Sort Order' }}
                        </label>
                        <input type="number" name="sort_order" id="editSortOrder" min="0" class="form-control text-sm w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-xl font-mono">
                    </div>
                </div>

                <!-- Description / SOP -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        {{ app()->getLocale() === 'ar' ? 'الوصف ومبررات التدقيق الرقابي' : 'Description & Audit SOP Rule' }}
                    </label>
                    <textarea name="description" id="editDescription" rows="2" class="form-control text-sm w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-xl"></textarea>
                </div>

                <!-- Active Toggle -->
                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" name="is_active" id="editIsActive" value="1" class="form-checkbox text-indigo-600 rounded">
                    <label for="editIsActive" class="text-xs font-bold text-slate-700 dark:text-slate-300 cursor-pointer">
                        {{ app()->getLocale() === 'ar' ? 'الحالة: نشط ومتاح في خيارات التسوية' : 'Active and available in stock adjustment options' }}
                    </label>
                </div>

                <!-- Actions -->
                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="closeEditTypeModal()" class="btn btn-neutral text-xs sm:text-sm font-semibold">
                        {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Cancel' }}
                    </button>
                    <button type="submit" class="btn btn-primary text-xs sm:text-sm font-bold shadow-md">
                        <i class="fa-solid fa-check mr-1 ml-1"></i>
                        {{ app()->getLocale() === 'ar' ? 'تحديث التعديلات' : 'Update Type' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        function openCreateTypeModal() {
            const modal = document.getElementById('createTypeModal');
            if (modal) modal.classList.remove('hidden');
        }

        function closeCreateTypeModal() {
            const modal = document.getElementById('createTypeModal');
            if (modal) modal.classList.add('hidden');
        }

        function openEditTypeModal(type) {
            const modal = document.getElementById('editTypeModal');
            const form = document.getElementById('editTypeForm');
            if (!modal || !form) return;

            form.action = "{{ url('admin/inventory/adjustments/types') }}/" + type.id;
            document.getElementById('editNameEn').value = type.name_en || '';
            document.getElementById('editNameAr').value = type.name_ar || '';
            document.getElementById('editDirection').value = type.direction || 'decrease';
            document.getElementById('editSortOrder').value = type.sort_order || 0;
            document.getElementById('editDescription').value = type.description || '';
            document.getElementById('editIsActive').checked = !!type.is_active;

            const dirSelect = document.getElementById('editDirection');
            if (type.is_system) {
                dirSelect.setAttribute('disabled', 'disabled');
            } else {
                dirSelect.removeAttribute('disabled');
            }

            modal.classList.remove('hidden');
        }

        function closeEditTypeModal() {
            const modal = document.getElementById('editTypeModal');
            if (modal) modal.classList.add('hidden');
        }

        // Close on ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeCreateTypeModal();
                closeEditTypeModal();
            }
        });
    </script>
</x-layouts.admin>
