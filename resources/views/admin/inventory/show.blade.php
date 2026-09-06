<<<<<<< HEAD
<x-layouts.admin 
    :pageTitle="'Stock Details: ' . $item['product_name_en']" 
    pageSubtitle="Location-specific stock inventory levels and allocated reservation metrics."
    :breadcrumbs="['Inventory' => route('admin.inventory.index'), $item['product_name_en'] => route('admin.inventory.show', $item['id'])]"
>
    <div style="display: grid; grid-template-columns: 1fr 340px; gap: 2rem;">
        <div>
            <!-- Stock Detail KPI -->
            <div class="card" style="padding: 2rem; margin-bottom: 2rem;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1.5rem;">
                    <div>
                        <div class="text-xs text-muted">Current Physical Stock</div>
                        <div class="font-black text-2xl" style="color: var(--color-text-primary);">{{ $item['current_stock'] }} units</div>
                    </div>
                    <div>
                        <div class="text-xs text-muted">Available for Dispatch</div>
                        <div class="font-black text-2xl text-success">{{ $item['available_stock'] }} units</div>
                    </div>
                    <div>
                        <div class="text-xs text-muted">Allocated / Reserved</div>
                        <div class="font-black text-2xl text-muted">{{ $item['reserved_stock'] }} units</div>
=======
@php
    $pName = app()->getLocale() == 'ar' ? ($product->name_ar ?? $product->name_en) : $product->name_en;
    $totalStock = $locationBreakdowns->sum('current_stock');
    $totalValuation = $totalStock * ($product->cost_price ?? ($product->price * 0.4));
@endphp

<x-layouts.admin 
    :pageTitle="app()->getLocale() == 'ar' ? 'سجل وتدقيق مخزون: ' . $pName : 'Stock Audit: ' . $pName" 
    :pageSubtitle="app()->getLocale() == 'ar' ? 'تتبع تفصيلي لمستويات المخزون وسجل تاريخ الحركات وتطور الرصيد عبر الفروع والمستودعات.' : 'Location-specific inventory levels and comprehensive chronological stock progression audit.'"
    :breadcrumbs="[__('admin.menu.inventory') => route('admin.inventory.index'), $pName => route('admin.inventory.show', $product->id)]"
>
    <x-slot name="actions">
        <a href="{{ route('admin.inventory.transfers') }}?product_id={{ $product->id }}" class="btn btn-primary">
            <i class="fa-solid fa-arrow-right-arrow-left mr-1.5 ml-1.5"></i> {{ __('admin.inventory.transfer_title') }}
        </a>
        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-secondary">
            <i class="fa-solid fa-pen mr-1.5 ml-1.5"></i> {{ app()->getLocale() == 'ar' ? 'تعديل بيانات المنتج' : 'Edit Formulation' }}
        </a>
    </x-slot>

    <!-- Product Top Card -->
    <div class="card" style="padding: 1.75rem; margin-bottom: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem;">
            <div style="display: flex; gap: 1.25rem; align-items: center;">
                <img src="{{ asset($product->image ?? 'assets/products/blue-mind.jpg') }}" alt="{{ $pName }}" style="width: 72px; height: 72px; border-radius: var(--radius-md); object-fit: cover; background: var(--color-bg-subtle);" onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';">
                <div>
                    <h2 style="font-size: 1.35rem; font-weight: 800; margin: 0 0 0.25rem 0;">{{ $pName }}</h2>
                    <div class="font-mono text-xs text-muted">
                        SKU: <strong class="text-primary">{{ $product->sku }}</strong> 
                        @if($product->barcode) • Barcode: <strong>{{ $product->barcode }}</strong> @endif
                        • {{ app()->getLocale() == 'ar' ? 'الحد الأدنى للتنبيه:' : 'Safety Threshold:' }} <strong>{{ $product->low_stock_threshold }} {{ app()->getLocale() == 'ar' ? 'وحدة' : 'units' }}</strong>
>>>>>>> origin/main
                    </div>
                </div>
            </div>

<<<<<<< HEAD
            <!-- Recent Movements for this SKU -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Movement Audit Log</h3>
                </div>
                <div class="table-responsive" style="border: none; border-radius: 0;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Log ID</th>
                                <th>Action</th>
                                <th>Delta</th>
                                <th>Authorized By</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(array_slice($movements, 0, 4) as $m)
                                <tr>
                                    <td class="font-mono text-xs">{{ $m['id'] }}</td>
                                    <td><span class="badge badge-neutral text-xs">{{ $m['movement_type'] }}</span></td>
                                    <td class="font-bold {{ $m['quantity'] < 0 ? 'text-danger' : 'text-success' }}">
                                        {{ $m['quantity'] > 0 ? '+' : '' }}{{ $m['quantity'] }}
                                    </td>
                                    <td class="text-xs">{{ $m['user'] }}</td>
                                    <td class="text-xs text-muted">{{ $m['date'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Side Location Card -->
        <div class="card" style="padding: 1.5rem;">
            <h4 style="font-size: 1rem; font-weight: 800; margin-bottom: 1rem;">Location Specification</h4>
            <div class="text-sm" style="display: flex; flex-direction: column; gap: 0.75rem;">
                <div><strong>Location:</strong> {{ $item['location_name_en'] }}</div>
                <div><strong>SKU:</strong> {{ $item['sku'] }}</div>
                <div><strong>Variant:</strong> {{ $item['variant_en'] }}</div>
                <div><strong>Inventory Valuation:</strong> ${{ number_format($item['current_stock'] * $item['unit_cost'], 2) }}</div>
            </div>

            <a href="{{ route('admin.inventory.transfers') }}" class="btn btn-primary btn-sm" style="width: 100%; margin-top: 1.5rem;">
                🔄 Relocate Stock
            </a>
        </div>
=======
            <!-- Total Valuation / Network Units -->
            <div style="display: flex; gap: 2rem; align-items: center;">
                <div>
                    <div class="text-xs text-muted font-bold">{{ app()->getLocale() == 'ar' ? 'إجمالي المخزون المجمع' : 'Total Aggregated Stock' }}</div>
                    <div class="font-black text-2xl text-primary">{{ $totalStock }} <span class="text-xs font-normal text-muted">{{ app()->getLocale() == 'ar' ? 'وحدة' : 'units' }}</span></div>
                </div>
                <div>
                    <div class="text-xs text-muted font-bold">{{ app()->getLocale() == 'ar' ? 'القيمة التقديرية للتكلفة' : 'Cost Valuation' }}</div>
                    <div class="font-black text-2xl" style="color: #10B981;">${{ number_format($totalValuation, 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Location-Specific Breakdown Cards -->
    <h3 style="font-size: 1.15rem; font-weight: 800; margin-bottom: 1rem;">
        <i class="fa-solid fa-map-location-dot text-primary mr-1 ml-1"></i>
        {{ app()->getLocale() == 'ar' ? 'توزيع المخزون حسب القنوات والمواقع' : 'Location & Channel Breakdown' }}
    </h3>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.25rem; margin-bottom: 2.5rem;">
        @foreach($locationBreakdowns as $locItem)
            @php
                $locName = app()->getLocale() == 'ar' ? ($locItem->location_name_ar ?? $locItem->location_name_en) : $locItem->location_name_en;
                $borderCol = $locItem->location_id === 'online' ? '#3B82F6' : ($locItem->location_id === 'offline' ? '#10B981' : '#8B5CF6');
            @endphp
            <div class="card" style="padding: 1.5rem; border-top: 4px solid {{ $borderCol }};">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <span class="font-bold text-sm">{{ $locName }}</span>
                    @if($locItem->status === 'out_of_stock')
                        <span class="badge badge-danger text-xs">{{ app()->getLocale() == 'ar' ? 'نفد' : 'Out of Stock' }}</span>
                    @elseif($locItem->status === 'low_stock')
                        <span class="badge badge-warning text-xs">{{ app()->getLocale() == 'ar' ? 'منخفض' : 'Low Stock' }}</span>
                    @else
                        <span class="badge badge-success text-xs">{{ app()->getLocale() == 'ar' ? 'متوفر' : 'In Stock' }}</span>
                    @endif
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.85rem;">
                    <div style="display: flex; justify-content: space-between;">
                        <span class="text-muted">{{ __('admin.inventory.current_stock') }}:</span>
                        <strong class="font-bold text-base">{{ $locItem->current_stock }} {{ app()->getLocale() == 'ar' ? 'وحدة' : 'units' }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span class="text-muted">{{ __('admin.inventory.available') }}:</span>
                        <span class="font-bold text-success">{{ $locItem->available_stock }} {{ app()->getLocale() == 'ar' ? 'وحدة' : 'units' }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span class="text-muted">{{ __('admin.inventory.reserved') }}:</span>
                        <span>{{ $locItem->reserved_stock }} {{ app()->getLocale() == 'ar' ? 'وحدة' : 'units' }}</span>
                    </div>
                </div>

                <div style="margin-top: 1.25rem; pt: 0.75rem; border-top: 1px solid var(--color-border);">
                    <a href="{{ route('admin.inventory.transfers') }}?product_id={{ $product->id }}&from_location={{ $locItem->location_id }}" 
                       class="btn btn-secondary btn-xs" style="width: 100%;">
                        <i class="fa-solid fa-arrow-right-arrow-left mr-1 ml-1"></i>
                        {{ app()->getLocale() == 'ar' ? 'تحويل من هذا الموقع' : 'Relocate From Here' }}
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Full Product Stock History Timeline (Requirement 24 & 25) -->
    <div class="card">
        <div class="card-header" style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--color-border); display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h3 class="card-title" style="font-size: 1.15rem; font-weight: 800; margin: 0;">
                    <i class="fa-solid fa-timeline text-primary mr-1 ml-1"></i>
                    {{ app()->getLocale() == 'ar' ? 'سجل تطور رصيد التركيبة (Audit Ledger)' : 'Stock Progression Audit Trail' }}
                </h3>
                <div class="text-xs text-muted" style="margin-top: 0.25rem;">
                    {{ app()->getLocale() == 'ar' ? 'يوضح جميع الحركات التاريخية وكيف وصل الرصيد الحالي إلى الرقم الفعلي' : 'Explains how the current inventory arrived at the current level step-by-step' }}
                </div>
            </div>
        </div>

        <div class="table-responsive" style="border: none; border-radius: 0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('admin.inventory.movement_id') }}</th>
                        <th>{{ __('admin.inventory.movement_type') }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'المسار (من ← إلى)' : 'Routing (From → To)' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'تغير الكمية' : 'Quantity Delta' }}</th>
                        <th>{{ __('admin.inventory.prev_qty') }} → {{ __('admin.inventory.new_qty') }}</th>
                        <th>{{ __('admin.inventory.logged_user') }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'الوقت والسبب' : 'Timestamp & Justification' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movements as $m)
                        @php
                            $mId = $m->movement_number ?? ('MOV-' . $m->id);
                            $mType = $m->movement_type ?? 'Stock In';
                            $mQty = (int) $m->quantity;
                            
                            $badgeClass = 'badge-neutral';
                            if (in_array($mType, ['Stock In', 'Return'], true)) {
                                $badgeClass = 'badge-success';
                            } elseif (in_array($mType, ['Online Sale', 'Offline Sale'], true)) {
                                $badgeClass = 'badge-primary';
                            } elseif (in_array($mType, ['Damaged', 'Expired', 'Stock Out'], true)) {
                                $badgeClass = 'badge-danger';
                            } elseif ($mType === 'Stock Transfer') {
                                $badgeClass = 'badge-warning';
                            }
                        @endphp
                        <tr>
                            <td class="font-mono text-xs font-bold text-primary">{{ $mId }}</td>
                            <td>
                                <span class="badge {{ $badgeClass }} text-xs font-bold">{{ $mType }}</span>
                            </td>
                            <td class="text-xs">
                                <div><strong>{{ app()->getLocale() == 'ar' ? 'من:' : 'From:' }}</strong> {{ $m->from_location }}</div>
                                <div><strong>{{ app()->getLocale() == 'ar' ? 'إلى:' : 'To:' }}</strong> {{ $m->to_location }}</div>
                            </td>
                            <td class="font-bold {{ $mQty < 0 ? 'text-danger' : 'text-success' }}">
                                {{ $mQty > 0 ? '+' : '' }}{{ $mQty }}
                            </td>
                            <td class="text-xs font-mono">
                                {{ $m->previous_qty ?? 0 }} → <strong>{{ $m->new_qty ?? 0 }}</strong>
                            </td>
                            <td class="text-xs">
                                <i class="fa-solid fa-user-shield mr-1 ml-1 text-muted"></i> {{ $m->user }}
                            </td>
                            <td class="text-xs text-muted">
                                <div>{{ $m->date?->format('Y-m-d') }} {{ $m->time }}</div>
                                @if($m->note)
                                    <div class="text-secondary" style="font-style: italic; margin-top: 0.15rem;">"{{ $m->note }}"</div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-6 text-muted">
                                {{ app()->getLocale() == 'ar' ? 'لا توجد حركات مسجلة لهذه التركيبة حتى الآن.' : 'No movements logged for this formulation yet.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(is_a($movements, \Illuminate\Pagination\LengthAwarePaginator::class))
            <x-pagination :currentPage="$movements->currentPage()" :totalPages="$movements->lastPage()" :totalItems="$movements->total()" />
        @endif
>>>>>>> origin/main
    </div>
</x-layouts.admin>
