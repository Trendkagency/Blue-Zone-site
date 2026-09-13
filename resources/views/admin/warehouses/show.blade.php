<x-layouts.admin 
    :pageTitle="$warehouse->name"
    :pageSubtitle="app()->getLocale() === 'ar' ? 'تفاصيل المستودع والأرصدة الحية وسجل التحويلات الشامل' : 'Facility stock dossier, active SKUs breakdown, and activity audit trail'"
    :breadcrumbs="[
        (app()->getLocale() === 'ar' ? 'المخزون' : 'Inventory') => route('admin.inventory.index'),
        (app()->getLocale() === 'ar' ? 'المستودعات' : 'Warehouses') => route('admin.warehouses.index'),
        $warehouse->name => route('admin.warehouses.show', $warehouse->id)
    ]"
>
    <x-slot name="actions">
        <a href="{{ route('admin.inventory.allocator') }}" class="btn btn-secondary font-semibold">
            <i class="fa-solid fa-network-wired text-sky-500 mr-1.5 ml-1.5"></i>
            {{ app()->getLocale() === 'ar' ? 'موزع المخزون' : 'Stock Allocator' }}
        </a>

        <a href="{{ route('admin.warehouses.edit', $warehouse->id) }}" class="btn btn-primary font-bold shadow-sm">
            <i class="fa-solid fa-pen-to-square mr-1.5 ml-1.5"></i>
            {{ app()->getLocale() === 'ar' ? 'تعديل بيانات المنشأة' : 'Edit Facility' }}
        </a>
    </x-slot>

    <div class="space-y-6">
        <!-- Facility Overview Header Card -->
        <div class="card p-6 shadow-sm border border-slate-200/80 dark:border-slate-800/80">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-500/20 flex items-center justify-center text-2xl flex-shrink-0">
                        <i class="fa-solid fa-warehouse"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-3 flex-wrap">
                            <h2 class="text-xl lg:text-2xl font-black text-slate-900 dark:text-white">{{ $warehouse->name }}</h2>
                            <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $warehouse->type_badge_class }}">
                                {{ $warehouse->type_label }}
                            </span>
                            <span class="font-mono text-xs px-2.5 py-0.5 rounded bg-slate-100 dark:bg-slate-950 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-800 font-bold">
                                {{ $warehouse->code }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-2">
                            <i class="fa-solid fa-location-dot text-slate-400"></i>
                            <span>{{ $warehouse->city ? $warehouse->city . ' — ' : '' }}{{ $warehouse->address ?? (app()->getLocale() === 'ar' ? 'العنوان الجغرافي غير محدد' : 'Address not specified') }}</span>
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 flex-wrap">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold {{ $warehouse->is_active ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20' : 'bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-500/20' }}">
                        <span class="w-2 h-2 rounded-full {{ $warehouse->is_active ? 'bg-emerald-500 animate-pulse' : 'bg-rose-500' }}"></span>
                        {{ $warehouse->is_active ? (app()->getLocale() === 'ar' ? 'منشأة نشطة وتعمل' : 'Active Facility') : (app()->getLocale() === 'ar' ? 'منشأة معطلة' : 'Inactive Facility') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Facility Dossier Metrics Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Stock Units -->
            <div class="card p-5 shadow-sm border border-slate-200/80 dark:border-slate-800/80">
                <div class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">{{ app()->getLocale() === 'ar' ? 'رصيد المستودع الفعلي' : 'Current Stock' }}</div>
                <div class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">{{ number_format($totalUnits) }} <span class="text-xs font-normal text-slate-500 dark:text-slate-400">{{ app()->getLocale() === 'ar' ? 'وحدة' : 'units' }}</span></div>
                <div class="text-xs text-indigo-600 dark:text-indigo-400 mt-1 font-semibold">
                    {{ $stockItems->total() }} {{ app()->getLocale() === 'ar' ? 'صنف مسجل' : 'Registered SKUs' }}
                </div>
            </div>

            <!-- Capacity Utilization -->
            <div class="card p-5 shadow-sm border border-slate-200/80 dark:border-slate-800/80">
                <div class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">{{ app()->getLocale() === 'ar' ? 'نسبة إشغال السعة' : 'Capacity Gauge' }}</div>
                <div class="text-2xl font-black {{ $utilizationPercent > 85 ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400' }} tracking-tight">{{ $utilizationPercent }}%</div>
                <div class="w-full bg-slate-100 dark:bg-slate-950 rounded-full h-1.5 mt-2 overflow-hidden border border-slate-200 dark:border-slate-800">
                    <div class="h-full rounded-full {{ $utilizationPercent > 85 ? 'bg-rose-500' : 'bg-emerald-500' }}" style="width: {{ $utilizationPercent }}%"></div>
                </div>
            </div>

            <!-- Financial Valuation -->
            <div class="card p-5 shadow-sm border border-slate-200/80 dark:border-slate-800/80">
                <div class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">{{ app()->getLocale() === 'ar' ? 'قيمة مخزون المنشأة' : 'Inventory Valuation' }}</div>
                <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 tracking-tight">${{ number_format($valuation, 2) }}</div>
                <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ app()->getLocale() === 'ar' ? 'على أساس سعر التكلفة' : 'Cost basis valuation' }}</div>
            </div>

            <!-- Manager & Support -->
            <div class="card p-5 shadow-sm border border-slate-200/80 dark:border-slate-800/80">
                <div class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">{{ app()->getLocale() === 'ar' ? 'إدارة المنشأة' : 'Operations Contact' }}</div>
                <div class="text-base font-bold text-slate-900 dark:text-white truncate">{{ $warehouse->manager_name ?? 'Facility Lead' }}</div>
                <div class="text-xs text-slate-500 dark:text-slate-400 mt-1 truncate">{{ $warehouse->phone ?? ($warehouse->email ?? (app()->getLocale() === 'ar' ? 'غير مسجل' : 'N/A')) }}</div>
            </div>
        </div>

        <!-- Inventory Breakdown Table Inside this Facility -->
        <div class="card shadow-sm border border-slate-200/80 dark:border-slate-800/80 overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-slate-200/80 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ app()->getLocale() === 'ar' ? 'أصناف المنتجات المخزنة بالمنشأة' : 'Stocked Product Formulations' }}</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ app()->getLocale() === 'ar' ? 'الأرصدة الحية، نقاط إعادة الطلب، وحالة المخزون داخل هذا المستودع.' : 'Live units, reorder points, and status specifically in this warehouse.' }}</p>
                </div>

                <form method="GET" action="{{ route('admin.warehouses.show', $warehouse->id) }}" class="flex items-center gap-2">
                    <div class="search-wrapper">
                        <i class="fa-solid fa-magnifying-glass search-icon text-slate-400"></i>
                        <input type="text" name="search" value="{{ request('search') }}" 
                            placeholder="{{ app()->getLocale() === 'ar' ? 'بحث بالاسم، الرمز...' : 'Search SKU, name...' }}"
                            class="form-control search-input text-xs w-48 sm:w-64">
                    </div>
                    <button type="submit" class="btn btn-secondary btn-sm font-bold">
                        <i class="fa-solid fa-filter"></i>
                    </button>
                </form>
            </div>

            <div class="table-responsive" style="border: none; border-radius: 0;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ app()->getLocale() === 'ar' ? 'المنتج / التركيبة' : 'Product & Formulation' }}</th>
                            <th>{{ app()->getLocale() === 'ar' ? 'رمز SKU' : 'SKU' }}</th>
                            <th style="text-align: center;">{{ app()->getLocale() === 'ar' ? 'الرصيد الفعلي' : 'Current' }}</th>
                            <th style="text-align: center;">{{ app()->getLocale() === 'ar' ? 'المحجوز' : 'Reserved' }}</th>
                            <th style="text-align: center;">{{ app()->getLocale() === 'ar' ? 'المتوفر للبيع' : 'Available' }}</th>
                            <th style="text-align: center;">{{ app()->getLocale() === 'ar' ? 'حد إعادة الطلب' : 'Reorder Point' }}</th>
                            <th>{{ app()->getLocale() === 'ar' ? 'قيمة الرصيد' : 'Value' }}</th>
                            <th style="text-align: center;">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stockItems as $item)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold text-xs border border-slate-200 dark:border-slate-700 flex-shrink-0">
                                            {{ Str::substr($item->product->name_en ?? 'P', 0, 2) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                                {{ app()->getLocale() === 'ar' ? $item->product->name_ar : $item->product->name_en }}
                                            </div>
                                            <div class="text-[11px] text-slate-500 dark:text-slate-400">{{ $item->variant_en ?? 'Standard' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="font-mono text-slate-700 dark:text-slate-300 font-semibold">{{ $item->product->sku ?? $item->sku }}</td>
                                <td style="text-align: center;" class="font-bold text-slate-900 dark:text-white text-sm">{{ $item->current_stock }}</td>
                                <td style="text-align: center;" class="font-mono text-slate-500 dark:text-slate-400">{{ $item->reserved_stock }}</td>
                                <td style="text-align: center;" class="font-bold {{ $item->available_stock > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }} text-sm">{{ $item->available_stock }}</td>
                                <td style="text-align: center;" class="font-mono text-slate-500 dark:text-slate-400">{{ $item->low_stock_threshold }}</td>
                                <td class="font-mono font-semibold text-slate-800 dark:text-slate-200">${{ number_format($item->current_stock * $item->unit_cost, 2) }}</td>
                                <td style="text-align: center;">
                                    @if($item->current_stock <= 0)
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-500/20">
                                            {{ app()->getLocale() === 'ar' ? 'نفد المخزون' : 'Out of Stock' }}
                                        </span>
                                    @elseif($item->current_stock <= $item->low_stock_threshold)
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-500/20">
                                            {{ app()->getLocale() === 'ar' ? 'مخزون منخفض' : 'Low Stock' }}
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20">
                                            {{ app()->getLocale() === 'ar' ? 'متوفر وجاهز' : 'Healthy' }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-8 text-slate-500 dark:text-slate-400">
                                    {{ app()->getLocale() === 'ar' ? 'لا توجد أصناف مخزنة حالياً' : 'No stocked formulations found' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($stockItems->hasPages())
                <div class="p-4 border-t border-slate-200/80 dark:border-slate-800">
                    {{ $stockItems->links() }}
                </div>
            @endif
        </div>

        <!-- Recent Movements Audit Trail -->
        <div class="card p-6 shadow-sm border border-slate-200/80 dark:border-slate-800/80">
            <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-indigo-500"></i>
                {{ app()->getLocale() === 'ar' ? 'آخر حركات النقل والشحنات المتعلقة بهذا المستودع' : 'Recent Transfer Audit Log for this Facility' }}
            </h3>

            <div class="table-responsive" style="border: none; border-radius: 0;">
                <table class="table text-xs">
                    <thead>
                        <tr>
                            <th>{{ app()->getLocale() === 'ar' ? 'الوقت والتاريخ' : 'Timestamp' }}</th>
                            <th>{{ app()->getLocale() === 'ar' ? 'المنتج' : 'Product' }}</th>
                            <th>{{ app()->getLocale() === 'ar' ? 'نوع الحركة' : 'Type' }}</th>
                            <th>{{ app()->getLocale() === 'ar' ? 'من' : 'From' }}</th>
                            <th>{{ app()->getLocale() === 'ar' ? 'إلى' : 'To' }}</th>
                            <th style="text-align: center;">{{ app()->getLocale() === 'ar' ? 'الكمية' : 'Qty' }}</th>
                            <th>{{ app()->getLocale() === 'ar' ? 'المستخدم' : 'User' }}</th>
                        </tr>
                    </thead>
                    <tbody class="font-mono">
                        @forelse($recentMovements as $mov)
                            <tr>
                                <td class="text-slate-500 dark:text-slate-400">{{ $mov->date }} {{ $mov->time }}</td>
                                <td class="font-sans font-bold text-slate-900 dark:text-white">{{ $mov->product_name_en }}</td>
                                <td class="font-sans">
                                    <span class="px-2 py-0.5 rounded text-[10px] bg-slate-100 dark:bg-slate-800 text-indigo-700 dark:text-indigo-300 border border-slate-200 dark:border-slate-700 font-semibold">{{ $mov->movement_type }}</span>
                                </td>
                                <td class="text-rose-600 dark:text-rose-400 font-sans">{{ $mov->from_location }}</td>
                                <td class="text-emerald-600 dark:text-emerald-400 font-sans">{{ $mov->to_location }}</td>
                                <td style="text-align: center;" class="font-bold text-slate-900 dark:text-white">{{ $mov->quantity }}</td>
                                <td class="text-slate-500 dark:text-slate-400 font-sans">{{ $mov->user }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-6 text-slate-500 dark:text-slate-400 font-sans">
                                    {{ app()->getLocale() === 'ar' ? 'لا توجد حركات مسجلة لهذا المستودع حتى الآن' : 'No recorded movements for this warehouse yet' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.admin>
