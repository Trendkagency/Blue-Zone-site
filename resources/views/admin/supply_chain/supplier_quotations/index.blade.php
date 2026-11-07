<x-layouts.admin 
    :pageTitle="__('supplier_quotations.title')" 
    :pageSubtitle="__('supplier_quotations.subtitle')"
    :breadcrumbs="[__('supplier_quotations.title') => '#']"
>
    <x-slot name="actions">
        <a href="{{ route('admin.supplier-quotations.create') }}" class="btn btn-primary text-sm font-bold shadow-sm flex items-center gap-1.5">
            <i class="fa-solid fa-plus"></i>
            <span>{{ __('supplier_quotations.create_quote') }}</span>
        </a>
    </x-slot>

    <!-- KPI Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-sky-50 dark:bg-sky-950/60 text-sky-600 dark:text-sky-400 flex items-center justify-center text-xl shadow-xs">
                <i class="fa-solid fa-file-invoice"></i>
            </div>
            <div>
                <span class="text-xs text-slate-500 dark:text-slate-400 font-bold block">{{ __('supplier_quotations.kpis.total_quotes') }}</span>
                <span class="text-2xl font-black text-slate-900 dark:text-white font-mono">{{ $kpis['total_quotes'] }}</span>
            </div>
        </div>

        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xl shadow-xs">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <div>
                <span class="text-xs text-slate-500 dark:text-slate-400 font-bold block">{{ __('supplier_quotations.kpis.pending') }}</span>
                <span class="text-2xl font-black text-amber-600 dark:text-amber-400 font-mono">{{ $kpis['pending'] }}</span>
            </div>
        </div>

        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl shadow-xs">
                <i class="fa-solid fa-check-double"></i>
            </div>
            <div>
                <span class="text-xs text-slate-500 dark:text-slate-400 font-bold block">{{ __('supplier_quotations.kpis.accepted') }}</span>
                <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400 font-mono">{{ $kpis['accepted'] }}</span>
            </div>
        </div>

        <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xl shadow-xs">
                <i class="fa-solid fa-cart-flatbed"></i>
            </div>
            <div>
                <span class="text-xs text-slate-500 dark:text-slate-400 font-bold block">{{ __('supplier_quotations.kpis.converted') }}</span>
                <span class="text-2xl font-black text-purple-600 dark:text-purple-400 font-mono">{{ $kpis['converted'] }}</span>
            </div>
        </div>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="card p-4 border border-gray-100 dark:border-gray-800 shadow-sm mb-6">
        <form method="GET" action="{{ route('admin.supplier-quotations.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-center">
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute start-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       class="form-control text-xs ps-8" 
                       placeholder="{{ app()->getLocale() === 'ar' ? 'بحث برقم العرض أو اسم المورد...' : 'Search quote # or vendor...' }}">
            </div>

            <div>
                <select name="supplier_id" class="form-select text-xs" onchange="this.form.submit()">
                    <option value="">-- {{ __('supplier_quotations.select_supplier') }} --</option>
                    @foreach($suppliers as $sup)
                        <option value="{{ $sup->id }}" {{ request('supplier_id') == $sup->id ? 'selected' : '' }}>
                            {{ app()->getLocale() === 'ar' ? ($sup->name_ar ?? $sup->name_en) : $sup->name_en }} ({{ $sup->code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <select name="status" class="form-select text-xs" onchange="this.form.submit()">
                    <option value="">-- {{ __('supplier_quotations.status') }} --</option>
                    @foreach(['draft', 'pending', 'accepted', 'rejected', 'converted'] as $st)
                        <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>
                            {{ __('supplier_quotations.statuses.' . $st) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="btn btn-secondary text-xs font-bold w-full">
                    <i class="fa-solid fa-filter mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'تصفية' : 'Filter' }}
                </button>
                @if(request()->hasAny(['search', 'supplier_id', 'status']))
                    <a href="{{ route('admin.supplier-quotations.index') }}" class="btn btn-ghost text-xs text-rose-500" title="{{ app()->getLocale() === 'ar' ? 'إعادة ضبط' : 'Reset' }}">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="card overflow-hidden border border-gray-100 dark:border-gray-800 shadow-sm">
        <div class="table-responsive">
            <table class="table-modern text-xs">
                <thead>
                    <tr>
                        <th>{{ __('supplier_quotations.quote_number') }}</th>
                        <th>{{ __('supplier_quotations.supplier') }}</th>
                        <th>{{ __('supplier_quotations.quote_date') }}</th>
                        <th>{{ __('supplier_quotations.valid_until') }}</th>
                        <th>{{ __('supplier_quotations.line_items') }}</th>
                        <th class="text-end">{{ __('supplier_quotations.grand_total') }}</th>
                        <th>{{ __('supplier_quotations.status') }}</th>
                        <th class="text-end">{{ app()->getLocale() === 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($quotations as $quote)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                            <td>
                                <a href="{{ route('admin.supplier-quotations.show', $quote) }}" class="font-bold font-mono text-sky-600 dark:text-sky-400 hover:underline flex items-center gap-1.5">
                                    <i class="fa-solid fa-file-invoice text-xs"></i>
                                    <span>{{ $quote->quote_number }}</span>
                                </a>
                                @if($quote->rfq_reference)
                                    <span class="text-[10px] text-gray-500 block">Ref: {{ $quote->rfq_reference }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="font-bold text-slate-800 dark:text-slate-200">
                                    {{ app()->getLocale() === 'ar' ? ($quote->supplier?->name_ar ?? $quote->supplier?->name_en) : $quote->supplier?->name_en }}
                                </div>
                                <span class="text-[10px] font-mono text-gray-500">{{ $quote->supplier?->code }}</span>
                            </td>
                            <td>{{ $quote->quote_date?->format('Y-m-d') }}</td>
                            <td>
                                @if($quote->valid_until)
                                    <span class="{{ $quote->valid_until->isPast() && $quote->status !== 'converted' ? 'text-rose-500 font-bold' : '' }}">
                                        {{ $quote->valid_until->format('Y-m-d') }}
                                    </span>
                                @else
                                    <span class="text-gray-400">--</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-secondary font-mono">
                                    {{ $quote->items_count }} {{ app()->getLocale() === 'ar' ? 'بنود' : 'Items' }}
                                </span>
                            </td>
                            <td class="text-end font-bold font-mono text-slate-900 dark:text-white">
                                {{ number_format($quote->grand_total, 2) }} {{ $quote->currency }}
                            </td>
                            <td>
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold border {{ $quote->statusBadgeClass() }}">
                                    {{ __('supplier_quotations.statuses.' . $quote->status) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if($quote->status !== 'converted')
                                        <!-- 1-Click Convert to PO -->
                                        <form method="POST" action="{{ route('admin.supplier-quotations.convert', $quote) }}" class="inline-block" onsubmit="return confirm('{{ app()->getLocale() === 'ar' ? 'هل أنت متأكد من تحويل هذا العرض مباشرة إلى أمر شراء رسمي؟' : 'Convert this supplier quotation directly into an official Purchase Order?' }}')">
                                            @csrf
                                            <button type="submit" class="p-1.5 px-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center gap-1 shadow-xs cursor-pointer" title="{{ __('supplier_quotations.actions.convert_to_po') }}">
                                                <i class="fa-solid fa-cart-plus"></i>
                                                <span class="hidden sm:inline">{{ __('supplier_quotations.actions.convert_to_po') }}</span>
                                            </button>
                                        </form>
                                    @else
                                        <!-- View Linked PO -->
                                        @if($quote->convertedPurchaseOrder)
                                            <a href="{{ route('admin.purchase-orders.show', $quote->convertedPurchaseOrder) }}" class="p-1.5 px-2 rounded-lg bg-purple-100 text-purple-700 dark:bg-purple-950 dark:text-purple-300 font-bold text-xs flex items-center gap-1 hover:underline">
                                                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                                <span>{{ $quote->convertedPurchaseOrder->po_number }}</span>
                                            </a>
                                        @endif
                                    @endif

                                    <!-- View Details -->
                                    <a href="{{ route('admin.supplier-quotations.show', $quote) }}" class="p-1.5 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 text-xs" title="View Details">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>

                                    <!-- Print -->
                                    <a href="{{ route('admin.supplier-quotations.print', $quote) }}" target="_blank" class="p-1.5 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 text-xs" title="Print Quotation">
                                        <i class="fa-solid fa-print"></i>
                                    </a>
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
