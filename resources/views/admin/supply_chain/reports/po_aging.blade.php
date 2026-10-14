<x-layouts.admin 
    :pageTitle="__('supply_chain_reports.po_aging')" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'تحليل أعمار أوامر الشراء المفتوحة والمتأخرة وتتبع البنود غير المكتملة لدى الموردين' : 'Open Purchase Order Aging Buckets, Fulfillment Backlogs & Overdue Lead-Time Analysis'"
    :breadcrumbs="[__('purchase_orders.title') => route('admin.purchase-orders.index'), __('supply_chain_reports.po_aging') => '#']"
>
    <x-slot name="actions">
        <a href="{{ route('admin.supply-chain-reports.po-aging', ['export' => 'xlsx']) }}" class="btn btn-primary font-bold shadow-sm">
            <i class="fa-solid fa-file-excel mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'تصدير إكسل' : 'Export Excel' }}
        </a>
    </x-slot>

    <!-- Aging Buckets Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="card p-4 border border-emerald-100 dark:border-emerald-950 bg-emerald-50/20 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-emerald-700 uppercase">0 - 15 {{ app()->getLocale() === 'ar' ? 'يوم' : 'Days' }}</p>
                <p class="text-2xl font-black text-emerald-800 dark:text-white mt-1">{{ $bucket0_15->count() }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-clock"></i>
            </div>
        </div>
        <div class="card p-4 border border-sky-100 dark:border-sky-950 bg-sky-50/20 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-sky-700 uppercase">16 - 30 {{ app()->getLocale() === 'ar' ? 'يوم' : 'Days' }}</p>
                <p class="text-2xl font-black text-sky-800 dark:text-white mt-1">{{ $bucket16_30->count() }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-hourglass-half"></i>
            </div>
        </div>
        <div class="card p-4 border border-amber-100 dark:border-amber-950 bg-amber-50/20 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-amber-700 uppercase">31 - 60 {{ app()->getLocale() === 'ar' ? 'يوم' : 'Days' }}</p>
                <p class="text-2xl font-black text-amber-800 dark:text-white mt-1">{{ $bucket31_60->count() }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-business-time"></i>
            </div>
        </div>
        <div class="card p-4 border border-rose-100 dark:border-rose-950 bg-rose-50/20 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-rose-700 uppercase">60+ {{ app()->getLocale() === 'ar' ? 'يوم (متأخر)' : 'Days (Severely Aged)' }}</p>
                <p class="text-2xl font-black text-rose-800 dark:text-white mt-1">{{ $bucket60_plus->count() }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
        </div>
    </div>

    <!-- PO Aging Table -->
    <x-admin.table-toolbar 
        table="#agingTable" 
        :title="app()->getLocale() === 'ar' ? 'جدول أعمار أوامر الشراء المفتوحة' : 'Open Purchase Orders Aging Ledger'" 
    />

    <div class="card shadow-sm border border-gray-100 dark:border-gray-800">
        <div class="table-responsive">
            <table class="table text-xs" id="agingTable">
                <thead>
                    <tr>
                        <th data-sort-type="text">{{ __('purchase_orders.po_number') }}</th>
                        <th data-sort-type="text">{{ __('purchase_orders.supplier') }}</th>
                        <th data-sort-type="date">{{ __('purchase_orders.order_date') }}</th>
                        <th data-sort-type="date">{{ __('purchase_orders.expected_delivery_date') }}</th>
                        <th data-sort-type="number">{{ app()->getLocale() === 'ar' ? 'عمر الطلب (أيام)' : 'Age (Days)' }}</th>
                        <th data-sort-type="number">{{ __('purchase_orders.total_amount') }}</th>
                        <th data-sort-type="text">{{ __('purchase_orders.status') }}</th>
                        <th data-sort-type="text">{{ app()->getLocale() === 'ar' ? 'حاوية العمر' : 'Bucket' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $po)
                        @php
                            $age = $po->order_date ? (int) $po->order_date->diffInDays(now()) : 0;
                        @endphp
                        <tr>
                            <td class="font-mono font-bold text-sky-600">
                                <a href="{{ route('admin.purchase-orders.show', $po) }}" class="hover:underline">
                                    {{ $po->po_number }}
                                </a>
                            </td>
                            <td class="font-bold text-slate-800 dark:text-white">
                                {{ $po->supplier?->name }}
                            </td>
                            <td class="font-mono">{{ $po->order_date?->format('Y-m-d') }}</td>
                            <td class="font-mono">
                                {{ $po->expected_delivery_date?->format('Y-m-d') ?? '—' }}
                            </td>
                            <td class="font-mono font-bold text-center {{ $age > 60 ? 'text-rose-600' : ($age > 30 ? 'text-amber-500' : 'text-slate-800 dark:text-white') }}">
                                {{ $age }}
                            </td>
                            <td class="font-mono font-bold">{{ number_format($po->total_amount, 2) }} {{ $po->currency }}</td>
                            <td>
                                <span class="badge {{ $po->status->badgeClass() }} text-[11px]">
                                    {{ $po->status->label() }}
                                </span>
                            </td>
                            <td>
                                @if($age <= 15)
                                    <span class="badge badge-emerald text-[10px]">0-15d</span>
                                @elseif($age <= 30)
                                    <span class="badge badge-sky text-[10px]">16-30d</span>
                                @elseif($age <= 60)
                                    <span class="badge badge-amber text-[10px]">31-60d</span>
                                @else
                                    <span class="badge badge-rose text-[10px]">60d+</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
