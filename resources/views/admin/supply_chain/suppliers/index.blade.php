<x-layouts.admin 
    :pageTitle="__('suppliers.title')" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'إدارة الموردين المؤهلين وفق معايير cGMP وتتبع تدقيقات الجودة والتوريدات' : 'cGMP Qualified Supplier Directory, Compliance Audits & Material Quality Ratings'"
    :breadcrumbs="[__('suppliers.title') => route('admin.suppliers.index')]"
>
    <x-slot name="actions">
        <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary font-bold shadow-sm">
            <i class="fa-solid fa-plus mr-1.5 ml-1.5"></i> {{ __('suppliers.new_supplier') }}
        </a>
    </x-slot>

    <!-- Top KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">{{ app()->getLocale() === 'ar' ? 'إجمالي الموردين' : 'Total Vendors' }}</p>
                <p class="text-2xl font-black text-slate-800 dark:text-white mt-1">{{ $kpis['total_vendors'] ?? $suppliers->total() }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-truck-field"></i>
            </div>
        </div>
        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">{{ app()->getLocale() === 'ar' ? 'موردون معتمدون' : 'Active Qualified' }}</p>
                <p class="text-2xl font-black text-emerald-600 mt-1">{{ $kpis['active_qualified'] ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-certificate"></i>
            </div>
        </div>
        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">{{ app()->getLocale() === 'ar' ? 'بانتظار الاعتماد المزدوج' : 'Pending Dual-Approval' }}</p>
                <p class="text-2xl font-black text-amber-500 mt-1">{{ $kpis['pending_approval'] ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-900/30 text-amber-500 flex items-center justify-center text-xl">
                <i class="fa-solid fa-user-check"></i>
            </div>
        </div>
        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">{{ app()->getLocale() === 'ar' ? 'تدقيقات تقترب من الانتهاء' : 'Audit Expiring (<60d)' }}</p>
                <p class="text-2xl font-black text-rose-500 mt-1">{{ $kpis['audit_expiring'] ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-50 dark:bg-rose-900/30 text-rose-500 flex items-center justify-center text-xl">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card p-4 mb-6 border border-gray-100 dark:border-gray-800 shadow-sm">
        <form method="GET" action="{{ route('admin.suppliers.index') }}" class="flex items-center justify-between gap-4 flex-wrap">
            <div class="search-wrapper flex-1 min-w-[260px] max-w-md">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control search-input text-sm w-full" placeholder="{{ app()->getLocale() === 'ar' ? 'بحث بالاسم، الرمز، البريد...' : 'Search supplier name, code, email...' }}">
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                <select name="type" onchange="this.form.submit()" class="form-select text-sm w-auto">
                    <option value="">{{ app()->getLocale() === 'ar' ? 'جميع الأنواع' : 'All Types' }}</option>
                    @foreach(\App\Enums\SupplierType::cases() as $type)
                        <option value="{{ $type->value }}" {{ request('type') === $type->value ? 'selected' : '' }}>
                            {{ $type->label() }}
                        </option>
                    @endforeach
                </select>

                <select name="status" onchange="this.form.submit()" class="form-select text-sm w-auto">
                    <option value="">{{ app()->getLocale() === 'ar' ? 'جميع الحالات' : 'All Statuses' }}</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'نشط ومعتمد' : 'Active & Approved' }}</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'قيد الموافقة' : 'Pending Approval' }}</option>
                    <option value="audit_due" {{ request('status') === 'audit_due' ? 'selected' : '' }}>{{ app()->getLocale() === 'ar' ? 'مطلوب تجديد التدقيق' : 'Audit Due' }}</option>
                </select>

                <button type="submit" class="btn btn-secondary btn-sm font-bold">
                    <i class="fa-solid fa-filter mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'تصفية' : 'Filter' }}
                </button>
            </div>
        </form>
    </div>

    <!-- Suppliers Table -->
    <x-admin.table-toolbar 
        table="#suppliersTable" 
        :title="app()->getLocale() === 'ar' ? 'دليل موردي المواد الأولية cGMP' : 'cGMP Supplier Directory'" 
    />

    <div class="card shadow-sm border border-gray-100 dark:border-gray-800">
        <div class="table-responsive">
            <table class="table" id="suppliersTable">
                <thead>
                    <tr>
                        <th data-sort-type="text">{{ __('suppliers.code') }}</th>
                        <th data-sort-type="text">{{ __('suppliers.name') }}</th>
                        <th data-sort-type="text">{{ __('suppliers.type') }}</th>
                        <th data-sort-type="text">{{ __('suppliers.country') }}</th>
                        <th data-sort-type="number">{{ __('suppliers.quality_rating') }}</th>
                        <th data-sort-type="date">{{ __('suppliers.audit_valid_until') }}</th>
                        <th data-sort-type="text">{{ __('suppliers.status') }}</th>
                        <th style="text-align: center;" data-no-sort data-no-export>{{ app()->getLocale() === 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                        <tr>
                            <td class="font-mono font-bold text-sky-600 dark:text-sky-400">
                                <a href="{{ route('admin.suppliers.show', $supplier) }}" class="hover:underline">
                                    {{ $supplier->code }}
                                </a>
                            </td>
                            <td>
                                <div class="font-bold text-slate-800 dark:text-white">
                                    {{ $supplier->name }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $supplier->contact_person ?? $supplier->email }}
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-indigo text-xs">
                                    {{ $supplier->type?->label() ?? 'Supplier' }}
                                </span>
                            </td>
                            <td class="text-sm">
                                {{ $supplier->country ?? 'N/A' }}
                            </td>
                            <td>
                                <div class="flex items-center gap-1.5">
                                    <span class="font-bold text-sm {{ $supplier->rating >= 90 ? 'text-emerald-600' : ($supplier->rating >= 75 ? 'text-amber-500' : 'text-rose-500') }}">
                                        {{ number_format($supplier->rating, 1) }}%
                                    </span>
                                    <i class="fa-solid fa-star text-xs text-amber-400"></i>
                                </div>
                            </td>
                            <td>
                                @if($supplier->audit_valid_until)
                                    @php
                                        $isExpiring = $supplier->audit_valid_until->isPast() || $supplier->audit_valid_until->diffInDays(now()) <= 60;
                                    @endphp
                                    <span class="text-xs font-mono {{ $isExpiring ? 'text-rose-600 font-bold' : 'text-gray-600 dark:text-gray-300' }}">
                                        {{ $supplier->audit_valid_until->format('Y-m-d') }}
                                        @if($supplier->audit_valid_until->isPast())
                                            <span class="text-rose-500">({{ app()->getLocale() === 'ar' ? 'منتهي' : 'Expired' }})</span>
                                        @endif
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                            <td>
                                @if($supplier->is_active)
                                    <span class="badge badge-emerald text-xs">
                                        <i class="fa-solid fa-check mr-1 ml-1"></i> {{ __('suppliers.active') }}
                                    </span>
                                @else
                                    <span class="badge badge-amber text-xs">
                                        <i class="fa-solid fa-hourglass-half mr-1 ml-1"></i> {{ __('suppliers.pending_approval') }}
                                    </span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('admin.suppliers.show', $supplier) }}" class="btn btn-ghost btn-sm text-sky-600" title="{{ app()->getLocale() === 'ar' ? 'عرض التفاصيل' : 'View Details' }}">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="btn btn-ghost btn-sm text-indigo-600" title="{{ app()->getLocale() === 'ar' ? 'تعديل' : 'Edit' }}">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-gray-500">
                                <i class="fa-solid fa-truck-ramp-box text-3xl mb-2 text-gray-400"></i>
                                <p>{{ app()->getLocale() === 'ar' ? 'لم يتم العثور على أي موردين.' : 'No suppliers found matching criteria.' }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($suppliers->hasPages())
            <div class="p-4 border-t border-gray-100 dark:border-gray-800">
                {{ $suppliers->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>
