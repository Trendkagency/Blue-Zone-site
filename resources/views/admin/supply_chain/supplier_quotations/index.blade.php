<x-layouts.admin 
    :pageTitle="__('supplier_quotations.title')" 
    :pageSubtitle="__('supplier_quotations.subtitle')"
    :breadcrumbs="[__('supplier_quotations.title') => route('admin.supplier-quotations.index')]"
>
    <x-slot name="actions">
        <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary font-bold text-xs shadow-sm inline-flex items-center gap-1.5 px-3 py-2">
            <i class="fa-solid fa-truck-field text-blue-500"></i>
            <span>{{ app()->getLocale() === 'ar' ? 'سجل الموردين' : 'Suppliers' }}</span>
        </a>
        <a href="{{ route('admin.purchase-orders.index') }}" class="btn btn-secondary font-bold text-xs shadow-sm inline-flex items-center gap-1.5 px-3 py-2">
            <i class="fa-solid fa-file-invoice text-indigo-500"></i>
            <span>{{ app()->getLocale() === 'ar' ? 'أوامر الشراء' : 'Purchase Orders' }}</span>
        </a>
        <a href="{{ route('admin.supplier-quotations.create') }}" class="btn btn-primary font-bold shadow-sm inline-flex items-center gap-2 px-4 py-2 text-sm">
            <i class="fa-solid fa-plus"></i>
            <span>{{ __('supplier_quotations.create_quote') }}</span>
        </a>
    </x-slot>

    <!-- Top KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">{{ __('supplier_quotations.kpis.total_quotes') }}</p>
                <p class="text-2xl font-black text-slate-800 dark:text-white mt-1 font-mono">{{ $kpis['total_quotes'] }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-sky-50 dark:bg-sky-900/30 text-sky-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-file-invoice"></i>
            </div>
        </div>

        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">{{ __('supplier_quotations.kpis.pending') }}</p>
                <p class="text-2xl font-black text-amber-500 mt-1 font-mono">{{ $kpis['pending'] }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-900/30 text-amber-500 flex items-center justify-center text-xl">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
        </div>

        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">{{ __('supplier_quotations.kpis.accepted') }}</p>
                <p class="text-2xl font-black text-emerald-600 mt-1 font-mono">{{ $kpis['accepted'] }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-check-double"></i>
            </div>
        </div>

        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase">{{ __('supplier_quotations.kpis.converted') }}</p>
                <p class="text-2xl font-black text-purple-600 mt-1 font-mono">{{ $kpis['converted'] }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-900/30 text-purple-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-cart-flatbed"></i>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card p-4 mb-6 border border-gray-100 dark:border-gray-800 shadow-sm">
        <form method="GET" action="{{ route('admin.supplier-quotations.index') }}" class="flex items-center justify-between gap-4 flex-wrap">
            <div class="search-wrapper flex-1 min-w-[260px] max-w-md">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       class="form-control search-input text-sm w-full" 
                       placeholder="{{ app()->getLocale() === 'ar' ? 'بحث برقم العرض، المرجع، اسم المورد...' : 'Search quote #, RFQ ref, supplier name, code...' }}">
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                <select name="supplier_id" onchange="this.form.submit()" class="form-select text-sm w-auto">
                    <option value="">{{ app()->getLocale() === 'ar' ? 'جميع الموردين' : 'All Suppliers' }}</option>
                    @foreach($suppliers as $sup)
                        <option value="{{ $sup->id }}" {{ request('supplier_id') == $sup->id ? 'selected' : '' }}>
                            {{ app()->getLocale() === 'ar' ? ($sup->name_ar ?? $sup->name_en) : $sup->name_en }} ({{ $sup->code }})
                        </option>
                    @endforeach
                </select>

                <select name="status" onchange="this.form.submit()" class="form-select text-sm w-auto">
                    <option value="">{{ app()->getLocale() === 'ar' ? 'جميع الحالات' : 'All Statuses' }}</option>
                    @foreach(['draft', 'pending', 'accepted', 'rejected', 'converted'] as $st)
                        <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>
                            {{ __('supplier_quotations.statuses.' . $st) }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="btn btn-secondary btn-sm font-bold">
                    <i class="fa-solid fa-filter mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'تصفية' : 'Filter' }}
                </button>

                @if(request()->hasAny(['search', 'supplier_id', 'status']))
                    <a href="{{ route('admin.supplier-quotations.index') }}" class="btn btn-ghost btn-sm text-rose-500 font-bold" title="{{ app()->getLocale() === 'ar' ? 'إعادة ضبط' : 'Reset' }}">
                        <i class="fa-solid fa-xmark mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Reset' }}
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Toolbar with Export, Print & Client Filter -->
    <x-admin.table-toolbar 
        table="#quotationsTable" 
        :title="app()->getLocale() === 'ar' ? 'سجل عروض أسعار الموردين وطلبات التسعير (RFQ)' : 'Supplier Quotations & RFQ Directory'" 
    />

    <!-- Data Table Card -->
    <div class="card shadow-sm border border-gray-100 dark:border-gray-800">
        <div class="table-responsive">
            <table class="table" id="quotationsTable">
                <thead>
                    <tr>
                        <th data-sort-type="text">{{ __('supplier_quotations.quote_number') }}</th>
                        <th data-sort-type="text">{{ __('supplier_quotations.supplier') }}</th>
                        <th data-sort-type="date">{{ __('supplier_quotations.quote_date') }}</th>
                        <th data-sort-type="date">{{ __('supplier_quotations.valid_until') }}</th>
                        <th data-sort-type="number">{{ __('supplier_quotations.line_items') }}</th>
                        <th data-sort-type="number">{{ __('supplier_quotations.grand_total') }}</th>
                        <th data-sort-type="text">{{ __('supplier_quotations.status') }}</th>
                        <th style="text-align: center;" data-no-sort data-no-export>{{ app()->getLocale() === 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quotations as $quote)
                        <tr>
                            <td class="font-mono font-bold text-sky-600 dark:text-sky-400">
                                <a href="{{ route('admin.supplier-quotations.show', $quote) }}" class="hover:underline flex items-center gap-1.5">
                                    <i class="fa-solid fa-file-contract text-xs"></i>
                                    <span>{{ $quote->quote_number }}</span>
                                </a>
                                @if($quote->rfq_reference)
                                    <span class="text-[11px] text-gray-400 font-mono block mt-0.5">Ref: {{ $quote->rfq_reference }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="font-bold text-slate-800 dark:text-white">
                                    <a href="{{ route('admin.suppliers.show', $quote->supplier) }}" class="hover:underline">
                                        {{ app()->getLocale() === 'ar' ? ($quote->supplier?->name_ar ?? $quote->supplier?->name_en) : $quote->supplier?->name_en }}
                                    </a>
                                </div>
                                <span class="text-xs text-gray-500 font-mono">{{ $quote->supplier?->code }}</span>
                            </td>
                            <td class="font-mono text-xs text-slate-700 dark:text-slate-300">
                                {{ $quote->quote_date?->format('Y-m-d') }}
                            </td>
                            <td class="font-mono text-xs">
                                @if($quote->valid_until)
                                    @php
                                        $isExpired = $quote->valid_until->isPast() && $quote->status !== 'converted';
                                    @endphp
                                    <span class="{{ $isExpired ? 'text-rose-600 font-bold' : 'text-slate-700 dark:text-slate-300' }}">
                                        {{ $quote->valid_until->format('Y-m-d') }}
                                        @if($isExpired)
                                            <span class="text-rose-500 text-[10px]">({{ app()->getLocale() === 'ar' ? 'منتهي' : 'Expired' }})</span>
                                        @endif
                                    </span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-secondary font-mono text-xs">
                                    {{ $quote->items_count }} {{ app()->getLocale() === 'ar' ? 'بنود' : 'Items' }}
                                </span>
                            </td>
                            <td class="font-mono font-bold text-slate-800 dark:text-white">
                                {{ number_format($quote->grand_total, 2) }} <span class="text-xs text-gray-500">{{ $quote->currency }}</span>
                            </td>
                            <td>
                                @if($quote->status === 'converted')
                                    <span class="badge badge-purple text-xs">
                                        <i class="fa-solid fa-cart-shopping mr-1 ml-1"></i> {{ __('supplier_quotations.statuses.converted') }}
                                    </span>
                                    @if($quote->convertedPurchaseOrder)
                                        <a href="{{ route('admin.purchase-orders.show', $quote->convertedPurchaseOrder) }}" class="text-[11px] font-mono font-bold text-purple-600 dark:text-purple-400 hover:underline block mt-1">
                                            <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i> {{ $quote->convertedPurchaseOrder->po_number }}
                                        </a>
                                    @endif
                                @elseif($quote->status === 'accepted')
                                    <span class="badge badge-emerald text-xs">
                                        <i class="fa-solid fa-check mr-1 ml-1"></i> {{ __('supplier_quotations.statuses.accepted') }}
                                    </span>
                                @elseif($quote->status === 'pending')
                                    <span class="badge badge-amber text-xs">
                                        <i class="fa-solid fa-clock-rotate-left mr-1 ml-1"></i> {{ __('supplier_quotations.statuses.pending') }}
                                    </span>
                                @elseif($quote->status === 'rejected')
                                    <span class="badge badge-rose text-xs">
                                        <i class="fa-solid fa-ban mr-1 ml-1"></i> {{ __('supplier_quotations.statuses.rejected') }}
                                    </span>
                                @else
                                    <span class="badge badge-neutral text-xs">
                                        {{ __('supplier_quotations.statuses.draft') }}
                                    </span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                    @if($quote->status !== 'converted')
                                        <!-- 1-Click Convert to PO -->
                                        <form method="POST" action="{{ route('admin.supplier-quotations.convert', $quote) }}" class="inline-block" onsubmit="return confirm('{{ app()->getLocale() === 'ar' ? 'هل أنت متأكد من تحويل هذا العرض مباشرة إلى أمر شراء رسمي؟' : 'Convert this supplier quotation directly into an official Purchase Order?' }}')">
                                            @csrf
                                            <button type="submit" class="btn btn-xs btn-primary font-bold shadow-xs flex items-center gap-1" title="{{ __('supplier_quotations.actions.convert_to_po') }}">
                                                <i class="fa-solid fa-cart-plus"></i>
                                                <span class="hidden md:inline">{{ __('supplier_quotations.actions.convert_to_po') }}</span>
                                            </button>
                                        </form>

                                        <!-- Edit -->
                                        <a href="{{ route('admin.supplier-quotations.edit', $quote) }}" class="btn btn-xs btn-secondary" title="{{ __('supplier_quotations.edit_quote') }}">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                    @endif

                                    <!-- View Details -->
                                    <a href="{{ route('admin.supplier-quotations.show', $quote) }}" class="btn btn-xs btn-outline" title="{{ app()->getLocale() === 'ar' ? 'عرض التفاصيل' : 'View Details' }}">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>

                                    <!-- Print -->
                                    <a href="{{ route('admin.supplier-quotations.print', $quote) }}" target="_blank" class="btn btn-xs btn-secondary" title="{{ __('supplier_quotations.actions.print') }}">
                                        <i class="fa-solid fa-print"></i>
                                    </a>

                                    @if($quote->status !== 'converted')
                                        <!-- Delete -->
                                        <form method="POST" action="{{ route('admin.supplier-quotations.destroy', $quote) }}" class="inline-block" onsubmit="return confirm('{{ app()->getLocale() === 'ar' ? 'هل أنت متأكد من حذف هذا العرض؟' : 'Are you sure you want to delete this quote?' }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-ghost text-rose-500 hover:bg-rose-50" title="{{ app()->getLocale() === 'ar' ? 'حذف' : 'Delete' }}">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 text-slate-400">
                                <i class="fa-solid fa-file-circle-question text-4xl mb-3 block text-slate-300"></i>
                                <span>{{ app()->getLocale() === 'ar' ? 'لا توجد عروض أسعار موردين مسجلة حتى الآن.' : 'No supplier quotations or RFQs recorded yet.' }}</span>
                                <div class="mt-3">
                                    <a href="{{ route('admin.supplier-quotations.create') }}" class="btn btn-sm btn-primary">
                                        <i class="fa-solid fa-plus mr-1 ml-1"></i> {{ __('supplier_quotations.create_quote') }}
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($quotations->hasPages())
            <div class="p-4 border-t border-gray-100 dark:border-gray-800">
                {{ $quotations->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>
