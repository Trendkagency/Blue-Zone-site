<x-layouts.admin 
    :pageTitle="$supplier->name" 
    :pageSubtitle="__('suppliers.code') . ': ' . $supplier->code . ' | ' . ($supplier->type?->label() ?? 'Supplier')"
    :breadcrumbs="[__('suppliers.title') => route('admin.suppliers.index'), $supplier->name => '#']"
>
    <x-slot name="actions">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="btn btn-secondary font-bold text-sm">
                <i class="fa-solid fa-pen-to-square mr-1 ml-1"></i> {{ __('suppliers.edit_supplier') }}
            </a>
            @if($supplier->is_active)
                <form method="POST" action="{{ route('admin.suppliers.deactivate', $supplier) }}" onsubmit="return confirm('{{ app()->getLocale() === 'ar' ? 'هل أنت متأكد من تجميد هذا المورد؟ لن يسمح بإنشاء طلبات شراء جديدة له.' : 'Are you sure you want to deactivate this supplier? No new POs can be drafted.' }}')">
                    @csrf
                    <button type="submit" class="btn btn-danger font-bold text-sm">
                        <i class="fa-solid fa-ban mr-1 ml-1"></i> {{ __('suppliers.deactivate') }}
                    </button>
                </form>
            @endif
        </div>
    </x-slot>

    <!-- Dual Approval Status Alert Banner -->
    <div class="card p-5 mb-6 border shadow-sm {{ $supplier->is_active ? 'border-emerald-200 dark:border-emerald-900/40 bg-emerald-50/40 dark:bg-emerald-950/20' : 'border-amber-200 dark:border-amber-900/40 bg-amber-50/40 dark:bg-amber-950/20' }}">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl {{ $supplier->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                    <i class="fa-solid {{ $supplier->is_active ? 'fa-shield-check' : 'fa-user-shield' }}"></i>
                </div>
                <div>
                    <h4 class="text-sm font-black text-slate-800 dark:text-white">
                        {{ $supplier->is_active ? __('suppliers.dual_approved_active') : __('suppliers.pending_dual_approval') }}
                    </h4>
                    <p class="text-xs text-slate-600 dark:text-gray-400">
                        {{ app()->getLocale() === 'ar' ? 'يتطلب بروتوكول cGMP موافقة منفصلة من مدير الجودة (QA) ورئيس المشتريات لتفعيل المورد.' : 'cGMP compliance mandates two distinct sign-offs (QA Director + Procurement Head) to qualify a vendor.' }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <!-- QA Approval Box -->
                <div class="bg-white dark:bg-slate-900 border border-gray-200 dark:border-gray-800 rounded-lg p-2.5 text-xs text-center min-w-[150px]">
                    <span class="text-gray-500 block mb-1 font-bold">{{ __('suppliers.qa_director') }}</span>
                    @if($supplier->qa_approved_at)
                        <span class="text-emerald-600 font-bold flex items-center justify-center gap-1">
                            <i class="fa-solid fa-circle-check"></i> {{ $supplier->qaApprover?->name ?? 'QA Director' }}
                        </span>
                        <span class="text-[10px] text-gray-400 block">{{ $supplier->qa_approved_at->format('Y-m-d H:i') }}</span>
                    @else
                        <form method="POST" action="{{ route('admin.suppliers.qa-approve', $supplier) }}">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-xs w-full font-bold">
                                <i class="fa-solid fa-stamp mr-1 ml-1"></i> {{ __('suppliers.approve_qa') }}
                            </button>
                        </form>
                    @endif
                </div>

                <!-- Procurement Approval Box -->
                <div class="bg-white dark:bg-slate-900 border border-gray-200 dark:border-gray-800 rounded-lg p-2.5 text-xs text-center min-w-[150px]">
                    <span class="text-gray-500 block mb-1 font-bold">{{ __('suppliers.procurement_head') }}</span>
                    @if($supplier->procurement_approved_at)
                        <span class="text-emerald-600 font-bold flex items-center justify-center gap-1">
                            <i class="fa-solid fa-circle-check"></i> {{ $supplier->procurementApprover?->name ?? 'Procurement Head' }}
                        </span>
                        <span class="text-[10px] text-gray-400 block">{{ $supplier->procurement_approved_at->format('Y-m-d H:i') }}</span>
                    @else
                        <form method="POST" action="{{ route('admin.suppliers.procurement-approve', $supplier) }}">
                            @csrf
                            <button type="submit" class="btn btn-secondary btn-xs w-full font-bold">
                                <i class="fa-solid fa-stamp mr-1 ml-1"></i> {{ __('suppliers.approve_procurement') }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Main Supplier Details Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Overview Card -->
        <div class="card p-5 border border-gray-100 dark:border-gray-800 shadow-sm space-y-3">
            <h4 class="text-xs font-black uppercase text-gray-500 tracking-wider border-b border-gray-100 dark:border-gray-800 pb-2">
                {{ app()->getLocale() === 'ar' ? 'ملخص المنشأة والاتصال' : 'Facility & Contact Profile' }}
            </h4>
            <div class="space-y-2 text-xs">
                <div class="flex justify-between">
                    <span class="text-gray-500">{{ __('suppliers.country') }}:</span>
                    <span class="font-bold text-slate-800 dark:text-white">{{ $supplier->country }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">{{ __('suppliers.email') }}:</span>
                    <a href="mailto:{{ $supplier->email }}" class="text-sky-600 hover:underline font-mono">{{ $supplier->email }}</a>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">{{ __('suppliers.phone') }}:</span>
                    <span class="font-mono text-slate-800 dark:text-white">{{ $supplier->phone ?? '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">{{ __('suppliers.contact_person') }}:</span>
                    <span class="font-bold text-slate-800 dark:text-white">{{ $supplier->contact_person ?? '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">{{ __('suppliers.payment_terms') }}:</span>
                    <span class="font-mono text-slate-800 dark:text-white">{{ $supplier->payment_terms ?? 'Net 30' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">{{ __('suppliers.lead_time_days') }}:</span>
                    <span class="font-mono text-slate-800 dark:text-white">{{ $supplier->lead_time_days }} {{ app()->getLocale() === 'ar' ? 'يوم' : 'days' }}</span>
                </div>
            </div>
        </div>

        <!-- Compliance & Rating -->
        <div class="card p-5 border border-gray-100 dark:border-gray-800 shadow-sm space-y-3">
            <h4 class="text-xs font-black uppercase text-gray-500 tracking-wider border-b border-gray-100 dark:border-gray-800 pb-2">
                {{ app()->getLocale() === 'ar' ? 'التقييم وضمان الجودة cGMP' : 'Quality Rating & Audit Window' }}
            </h4>
            <div class="text-center py-2">
                <div class="inline-flex items-center justify-center p-3 rounded-2xl bg-amber-50 dark:bg-amber-950/30 text-amber-500 mb-2">
                    <span class="text-3xl font-black">{{ number_format($supplier->rating, 1) }}</span>
                    <span class="text-lg font-bold">/100</span>
                </div>
                <p class="text-xs text-gray-500">
                    {{ app()->getLocale() === 'ar' ? 'مبني على نسبة نجاح فحص المواد الأولية والالتزام بمواعيد الشحن وسلامة أختام الحاويات' : 'Computed from QC lot pass rate, on-time delivery & container seal integrity' }}
                </p>
            </div>
            <div class="pt-2 border-t border-gray-100 dark:border-gray-800 text-xs flex justify-between items-center">
                <span class="text-gray-500">{{ __('suppliers.audit_valid_until') }}:</span>
                <span class="font-mono font-bold {{ $supplier->audit_valid_until?->isPast() ? 'text-rose-600' : 'text-slate-800 dark:text-white' }}">
                    {{ $supplier->audit_valid_until?->format('Y-m-d') ?? 'N/A' }}
                </span>
            </div>
        </div>

        <!-- Certifications on File -->
        <div class="card p-5 border border-gray-100 dark:border-gray-800 shadow-sm space-y-3">
            <h4 class="text-xs font-black uppercase text-gray-500 tracking-wider border-b border-gray-100 dark:border-gray-800 pb-2">
                {{ app()->getLocale() === 'ar' ? 'الشهادات والاعتمادات المرفقة' : 'Qualification Dossier Files' }}
            </h4>
            <div class="space-y-2">
                @php
                    $docs = [
                        'doc_cgmp' => ['title' => 'cGMP Audit Certificate', 'icon' => 'fa-certificate', 'color' => 'text-emerald-500'],
                        'doc_iso' => ['title' => 'ISO Certificate', 'icon' => 'fa-award', 'color' => 'text-blue-500'],
                        'doc_halal' => ['title' => 'Halal/Kosher/Organic', 'icon' => 'fa-hand-holding-heart', 'color' => 'text-amber-500'],
                        'doc_allergen' => ['title' => 'Allergen Statement', 'icon' => 'fa-shield-virus', 'color' => 'text-rose-500'],
                    ];
                @endphp
                @foreach($docs as $col => $doc)
                    @php $media = $supplier->getFirstMedia($col); @endphp
                    <div class="flex items-center justify-between p-2 rounded-lg bg-gray-50 dark:bg-slate-800/50 text-xs">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid {{ $doc['icon'] }} {{ $doc['color'] }}"></i>
                            <span class="font-bold text-slate-700 dark:text-gray-300">{{ $doc['title'] }}</span>
                        </div>
                        @if($media)
                            <a href="{{ $media->getUrl() }}" target="_blank" class="btn btn-ghost btn-xs text-sky-600 font-bold">
                                <i class="fa-solid fa-file-pdf mr-1"></i> PDF
                            </a>
                        @else
                            <span class="text-gray-400 text-[11px]">{{ app()->getLocale() === 'ar' ? 'غير مرفق' : 'Missing' }}</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Sub-tables: Raw Materials Pricelist & Recent Purchase Orders -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Raw Materials Supplied -->
        <div class="card p-5 border border-gray-100 dark:border-gray-800 shadow-sm">
            <h4 class="text-sm font-bold text-slate-800 dark:text-white flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3 mb-4">
                <span><i class="fa-solid fa-flask-vial text-sky-600 mr-2 ml-2"></i> {{ app()->getLocale() === 'ar' ? 'المواد الأولية المعتمدة والتسعير' : 'Approved Raw Materials & Price Master' }}</span>
                <span class="badge badge-sky text-xs">{{ $supplier->materialPrices->count() }}</span>
            </h4>
            <div class="table-responsive">
                <table class="table text-xs">
                    <thead>
                        <tr>
                            <th>{{ app()->getLocale() === 'ar' ? 'المادة الأولية' : 'Raw Material' }}</th>
                            <th>{{ app()->getLocale() === 'ar' ? 'رقم الكتالوج' : 'Part #' }}</th>
                            <th>{{ app()->getLocale() === 'ar' ? 'السعر' : 'Unit Price' }}</th>
                            <th>{{ app()->getLocale() === 'ar' ? 'أقل كمية' : 'MOQ' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($supplier->materialPrices as $price)
                            <tr>
                                <td class="font-bold">
                                    <a href="{{ route('admin.raw-materials.show', $price->rawMaterial) }}" class="text-sky-600 hover:underline">
                                        {{ $price->rawMaterial?->name }}
                                    </a>
                                </td>
                                <td class="font-mono text-gray-500">{{ $price->supplier_part_number ?? '—' }}</td>
                                <td class="font-mono font-bold">{{ number_format($price->unit_price, 2) }} {{ $price->currency }}</td>
                                <td class="font-mono">{{ number_format($price->min_order_qty, 0) }} {{ $price->rawMaterial?->uom?->value }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-gray-400">
                                    {{ app()->getLocale() === 'ar' ? 'لا توجد مواد مسجلة لهذا المورد حالياً.' : 'No materials linked in price master.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Purchase Orders History -->
        <div class="card p-5 border border-gray-100 dark:border-gray-800 shadow-sm">
            <h4 class="text-sm font-bold text-slate-800 dark:text-white flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-3 mb-4">
                <span><i class="fa-solid fa-file-invoice text-indigo-600 mr-2 ml-2"></i> {{ app()->getLocale() === 'ar' ? 'أحدث أوامر الشراء' : 'Recent Purchase Orders' }}</span>
                <span class="badge badge-indigo text-xs">{{ $supplier->purchaseOrders->count() }}</span>
            </h4>
            <div class="table-responsive">
                <table class="table text-xs">
                    <thead>
                        <tr>
                            <th>{{ app()->getLocale() === 'ar' ? 'رقم الطلب' : 'PO #' }}</th>
                            <th>{{ app()->getLocale() === 'ar' ? 'التاريخ' : 'Date' }}</th>
                            <th>{{ app()->getLocale() === 'ar' ? 'الإجمالي' : 'Total (AED)' }}</th>
                            <th>{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($supplier->purchaseOrders->take(5) as $po)
                            <tr>
                                <td class="font-mono font-bold text-sky-600">
                                    <a href="{{ route('admin.purchase-orders.show', $po) }}" class="hover:underline">
                                        {{ $po->po_number }}
                                    </a>
                                </td>
                                <td>{{ $po->order_date?->format('Y-m-d') }}</td>
                                <td class="font-mono font-bold">{{ number_format($po->total_amount, 2) }}</td>
                                <td>
                                    <span class="badge {{ $po->status->badgeClass() }} text-[11px]">
                                        {{ $po->status->label() }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-gray-400">
                                    {{ app()->getLocale() === 'ar' ? 'لا توجد أوامر شراء منشأة لهذا المورد بعد.' : 'No purchase orders generated yet.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.admin>
