<x-layouts.admin 
    :pageTitle="__('admin.menu.inventory')" 
    :pageSubtitle="__('admin.inventory.title')"
    :breadcrumbs="[__('admin.menu.inventory') => route('admin.inventory.index')]"
>
    <x-slot name="actions">
        <button type="button" class="btn btn-secondary" onclick="openQuickAdjustModal()">
            <i class="fa-solid fa-sliders mr-1.5 ml-1.5"></i> {{ app()->getLocale() == 'ar' ? 'تسوية جردية سريعة' : 'Quick Adjustment' }}
        </button>
        <a href="{{ route('admin.inventory.transfers') }}" class="btn btn-primary">
            <i class="fa-solid fa-arrow-right-arrow-left mr-1.5 ml-1.5"></i> {{ __('admin.inventory.transfer_title') }}
        </a>
        <a href="{{ route('admin.inventory.history') }}" class="btn btn-secondary">
            <i class="fa-solid fa-clock-rotate-left mr-1.5 ml-1.5"></i> {{ __('admin.menu.stock_history') }}
        </a>
    </x-slot>

    @if(session('status'))
        <div class="alert alert-success" style="margin-bottom: 1.5rem;">
            <i class="fa-solid fa-circle-check mr-1.5 ml-1.5 text-success"></i> {{ session('status') }}
        </div>
    @endif

    @if($errors->has('adjustment_error'))
        <div class="alert alert-danger" style="margin-bottom: 1.5rem;">
            <i class="fa-solid fa-triangle-exclamation mr-1.5 ml-1.5 text-danger"></i> {{ $errors->first('adjustment_error') }}
        </div>
    @endif

    <!-- Network KPI Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 1.75rem;">
        <div class="card" style="padding: 1.25rem;">
            <div class="text-xs text-muted font-bold" style="text-transform: uppercase;">
                {{ app()->getLocale() == 'ar' ? 'إجمالي المخزون الفعلي' : 'Total Physical Stock' }}
            </div>
            <div class="font-black text-2xl text-primary" style="margin-top: 0.35rem;">
                {{ number_format($kpis['total_units']) }} <span class="text-xs text-muted font-normal">{{ app()->getLocale() == 'ar' ? 'وحدة' : 'units' }}</span>
            </div>
            <div class="text-xs text-muted" style="margin-top: 0.25rem;">
                {{ app()->getLocale() == 'ar' ? 'موزعة عبر جميع القنوات' : 'Across all distribution channels' }}
            </div>
        </div>

        <div class="card" style="padding: 1.25rem;">
            <div class="text-xs text-muted font-bold" style="text-transform: uppercase;">
                {{ app()->getLocale() == 'ar' ? 'مستودع الأونلاين' : 'Online Fulfillment' }}
            </div>
            <div class="font-black text-2xl" style="margin-top: 0.35rem; color: #3B82F6;">
                {{ number_format($kpis['online_units']) }} <span class="text-xs text-muted font-normal">{{ app()->getLocale() == 'ar' ? 'وحدة' : 'units' }}</span>
            </div>
            <div class="text-xs text-muted" style="margin-top: 0.25rem;">
                {{ app()->getLocale() == 'ar' ? 'جاهز لشحن المتجر الإلكتروني' : 'Ready for e-commerce dispatch' }}
            </div>
        </div>

        <div class="card" style="padding: 1.25rem;">
            <div class="text-xs text-muted font-bold" style="text-transform: uppercase;">
                {{ app()->getLocale() == 'ar' ? 'مخزون المعرض (POS)' : 'Flagship Boutique' }}
            </div>
            <div class="font-black text-2xl" style="margin-top: 0.35rem; color: #10B981;">
                {{ number_format($kpis['offline_units']) }} <span class="text-xs text-muted font-normal">{{ app()->getLocale() == 'ar' ? 'وحدة' : 'units' }}</span>
            </div>
            <div class="text-xs text-muted" style="margin-top: 0.25rem;">
                {{ app()->getLocale() == 'ar' ? 'مخصص للمبيعات المباشرة' : 'Allocated for direct walk-in POS' }}
            </div>
        </div>

        <div class="card" style="padding: 1.25rem;">
            <div class="text-xs text-muted font-bold" style="text-transform: uppercase;">
                {{ app()->getLocale() == 'ar' ? 'المستودع المركزي' : 'Central Depot' }}
            </div>
            <div class="font-black text-2xl" style="margin-top: 0.35rem; color: #8B5CF6;">
                {{ number_format($kpis['central_units']) }} <span class="text-xs text-muted font-normal">{{ app()->getLocale() == 'ar' ? 'وحدة' : 'units' }}</span>
            </div>
            <div class="text-xs text-muted" style="margin-top: 0.25rem;">
                {{ app()->getLocale() == 'ar' ? 'احتياطي إعادة التوريد' : 'Quarantine & replenishment buffer' }}
            </div>
        </div>

        <div class="card" style="padding: 1.25rem; border-inline-start: 4px solid #F59E0B;">
            <div class="text-xs text-muted font-bold" style="text-transform: uppercase;">
                {{ app()->getLocale() == 'ar' ? 'تنبيهات انخفاض المخزون' : 'Low Stock Alerts' }}
            </div>
            <div class="font-black text-2xl" style="margin-top: 0.35rem; color: #D97706;">
                {{ $kpis['low_stock_count'] }} <span class="text-xs text-muted font-normal">{{ app()->getLocale() == 'ar' ? 'تنبيه' : 'alerts' }}</span>
            </div>
            <div class="text-xs text-muted" style="margin-top: 0.25rem;">
                {{ app()->getLocale() == 'ar' ? 'أقل من حد الأمان' : 'Items at or below safety threshold' }}
            </div>
        </div>

        <div class="card" style="padding: 1.25rem; border-inline-start: 4px solid #EF4444;">
            <div class="text-xs text-muted font-bold" style="text-transform: uppercase;">
                {{ app()->getLocale() == 'ar' ? 'نفد من المخزون' : 'Out of Stock' }}
            </div>
            <div class="font-black text-2xl" style="margin-top: 0.35rem; color: #EF4444;">
                {{ $kpis['out_of_stock_count'] }} <span class="text-xs text-muted font-normal">{{ app()->getLocale() == 'ar' ? 'منتج' : 'items' }}</span>
            </div>
            <div class="text-xs text-muted" style="margin-top: 0.25rem;">
                {{ app()->getLocale() == 'ar' ? 'الرصيد الفعلي 0' : 'Immediate replenishment needed' }}
            </div>
        </div>
    </div>

    <!-- Clickable Interactive Location Filter Tabs -->
    <div style="display: flex; gap: 0.75rem; margin-bottom: 1.5rem; flex-wrap: wrap; align-items: center;">
        <span class="text-xs font-bold text-muted" style="margin-inline-end: 0.5rem;">
            <i class="fa-solid fa-location-dot mr-1 ml-1"></i> {{ app()->getLocale() == 'ar' ? 'تصفية حسب الموقع:' : 'Filter Location:' }}
        </span>

        <a href="{{ route('admin.inventory.index', ['location' => 'all', 'status' => $selectedStatus, 'search' => $search]) }}" 
           class="btn btn-sm {{ $selectedLocation === 'all' ? 'btn-primary' : 'btn-secondary' }}" 
           style="border-radius: 9999px;">
            <i class="fa-solid fa-layer-group mr-1 ml-1"></i>
            {{ app()->getLocale() == 'ar' ? 'جميع المواقع (المخزون المركزي المجمع)' : 'All Locations (Centralized)' }}
        </a>

        @foreach($locations as $loc)
            @php
                $locId = is_array($loc) ? $loc['id'] : $loc->id;
                $locNameAr = is_array($loc) ? ($loc['name_ar'] ?? $loc['name_en']) : ($loc->name_ar ?? $loc->name_en);
                $locNameEn = is_array($loc) ? $loc['name_en'] : $loc->name_en;
                $locDisplay = app()->getLocale() === 'ar' ? $locNameAr : $locNameEn;
                $isActive = $selectedLocation === $locId;
            @endphp
            <a href="{{ route('admin.inventory.index', ['location' => $locId, 'status' => $selectedStatus, 'search' => $search]) }}" 
               class="btn btn-sm {{ $isActive ? 'btn-primary' : 'btn-secondary' }}" 
               style="border-radius: 9999px;">
                {{ $locDisplay }}
            </a>
        @endforeach
    </div>

    <!-- Search & Status Toolbar -->
    <div class="card" style="padding: 1rem 1.5rem; margin-bottom: 1.5rem;">
        <form method="GET" action="{{ route('admin.inventory.index') }}" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center; justify-content: space-between;">
            <input type="hidden" name="location" value="{{ $selectedLocation }}">

            <!-- Search input -->
            <div class="search-wrapper" style="flex: 1; min-width: 260px;">
                <svg class="search-icon" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" name="search" value="{{ $search }}" class="form-control search-input text-sm" placeholder="{{ app()->getLocale() == 'ar' ? 'بحث بالرمز SKU، اسم التركيبة، الباركود...' : 'Filter by SKU, product formulation, barcode...' }}">
            </div>

            <!-- Status Pills -->
            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                <a href="{{ route('admin.inventory.index', ['location' => $selectedLocation, 'status' => 'all', 'search' => $search]) }}" 
                   class="btn btn-xs {{ $selectedStatus === 'all' ? 'btn-neutral active' : 'btn-secondary' }}">
                    {{ app()->getLocale() == 'ar' ? 'الكل' : 'All Statuses' }}
                </a>
                <a href="{{ route('admin.inventory.index', ['location' => $selectedLocation, 'status' => 'in_stock', 'search' => $search]) }}" 
                   class="btn btn-xs {{ $selectedStatus === 'in_stock' ? 'btn-success text-white' : 'btn-secondary' }}">
                    <i class="fa-solid fa-circle-check mr-1 ml-1"></i> {{ app()->getLocale() == 'ar' ? 'متوفر' : 'In Stock' }}
                </a>
                <a href="{{ route('admin.inventory.index', ['location' => $selectedLocation, 'status' => 'low_stock', 'search' => $search]) }}" 
                   class="btn btn-xs {{ $selectedStatus === 'low_stock' ? 'btn-warning text-white' : 'btn-secondary' }}">
                    <i class="fa-solid fa-triangle-exclamation mr-1 ml-1"></i> {{ app()->getLocale() == 'ar' ? 'تنبيه انخفاض المخزون' : 'Low Stock Alerts' }}
                </a>
                <a href="{{ route('admin.inventory.index', ['location' => $selectedLocation, 'status' => 'out_of_stock', 'search' => $search]) }}" 
                   class="btn btn-xs {{ $selectedStatus === 'out_of_stock' ? 'btn-danger text-white' : 'btn-secondary' }}">
                    <i class="fa-solid fa-circle-xmark mr-1 ml-1"></i> {{ app()->getLocale() == 'ar' ? 'نفد من المخزون' : 'Out of Stock' }}
                </a>
            </div>

            <button type="submit" class="btn btn-secondary btn-sm">
                <i class="fa-solid fa-filter mr-1 ml-1"></i> {{ app()->getLocale() == 'ar' ? 'تطبيق' : 'Apply' }}
            </button>
        </form>
    </div>

    <!-- Inventory Table -->
    <div class="card">
        <div class="table-responsive" style="border: none; border-radius: 0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ app()->getLocale() == 'ar' ? 'التركيبة / رمز SKU' : 'Formulation / SKU' }}</th>
                        <th>{{ __('admin.inventory.location') }}</th>
                        <th>{{ __('admin.inventory.current_stock') }}</th>
                        <th>{{ __('admin.inventory.available') }}</th>
                        <th>{{ __('admin.inventory.reserved') }}</th>
                        <th>{{ __('admin.inventory.threshold') }}</th>
                        <th>{{ __('admin.inventory.status') }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stockItems as $item)
                        @php
                            $iId = $item->id;
                            $pId = $item->product_id;
                            $product = $item->product;
                            $iName = app()->getLocale() == 'ar' ? ($product->name_ar ?? $product->name_en ?? 'تركيبة حيوية') : ($product->name_en ?? 'Bioceutical Compound');
                            $iLoc = app()->getLocale() == 'ar' ? ($item->location_name_ar ?? $item->location_name_en) : $item->location_name_en;
                            $iSku = $product->sku ?? 'BZ-SKU';
                            $iVar = $item->variant_en ?? 'Standard Pack';
                            $iCurr = $item->current_stock ?? 0;
                            $iAvail = $item->available_stock ?? 0;
                            $iRes = $item->reserved_stock ?? 0;
                            $iThresh = $item->low_stock_threshold ?? 15;
                            $iStatus = $item->status ?? 'in_stock';
                        @endphp
                        <tr>
                            <td>
                                <div class="font-bold text-sm text-primary">
                                    <a href="{{ route('admin.inventory.show', $pId) }}">
                                        {{ $iName }}
                                    </a>
                                </div>
                                <div class="text-xs text-muted font-mono">
                                    {{ $iSku }} • {{ $iVar }}
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-neutral text-xs font-semibold">
                                    {{ $iLoc }}
                                </span>
                            </td>
                            <td class="font-bold">{{ $iCurr }} {{ app()->getLocale() == 'ar' ? 'وحدة' : 'units' }}</td>
                            <td class="font-bold {{ $iAvail <= 0 ? 'text-danger' : ($iAvail <= $iThresh ? 'text-warning' : 'text-success') }}">
                                {{ $iAvail }} {{ app()->getLocale() == 'ar' ? 'وحدة' : 'units' }}
                            </td>
                            <td class="text-muted">{{ $iRes }} {{ app()->getLocale() == 'ar' ? 'وحدة' : 'units' }}</td>
                            <td>
                                <span class="badge badge-subtle text-xs">
                                    {{ $iThresh }} {{ app()->getLocale() == 'ar' ? 'وحدة' : 'units' }}
                                </span>
                            </td>
                            <td>
                                @if($iStatus === 'out_of_stock' || $iCurr <= 0)
                                    <span class="badge badge-danger text-xs font-bold">
                                        <i class="fa-solid fa-circle-xmark mr-1 ml-1"></i> {{ app()->getLocale() == 'ar' ? 'نفد من المخزون' : 'Out of Stock' }}
                                    </span>
                                @elseif($iStatus === 'low_stock' || $iCurr <= $iThresh)
                                    <span class="badge badge-warning text-xs font-bold">
                                        <i class="fa-solid fa-triangle-exclamation mr-1 ml-1"></i> {{ app()->getLocale() == 'ar' ? 'تنبيه انخفاض المخزون' : 'Low Stock Alert' }}
                                    </span>
                                @else
                                    <span class="badge badge-success text-xs font-bold">
                                        <i class="fa-solid fa-circle-check mr-1 ml-1"></i> {{ app()->getLocale() == 'ar' ? 'متوفر في المخزون' : 'In Stock' }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="table-actions" style="display: flex; gap: 0.35rem;">
                                    <!-- Transfer Button -->
                                    <a href="{{ route('admin.inventory.transfers') }}?product_id={{ $pId }}&from_location={{ $item->location_id }}" 
                                       class="btn btn-secondary btn-xs" 
                                       title="{{ __('admin.inventory.transfer_title') }}">
                                        <i class="fa-solid fa-arrow-right-arrow-left mr-1 ml-1"></i> {{ app()->getLocale() == 'ar' ? 'تحويل' : 'Transfer' }}
                                    </a>

                                    <!-- Quick Adjustment Button -->
                                    <button type="button" 
                                            class="btn btn-secondary btn-xs" 
                                            onclick="openQuickAdjustModal('{{ $pId }}', '{{ $item->location_id }}', '{{ addslashes($iName) }}', {{ $iCurr }})"
                                            title="{{ app()->getLocale() == 'ar' ? 'تسوية المخزون' : 'Adjust Stock' }}">
                                        <i class="fa-solid fa-sliders mr-1 ml-1"></i> {{ app()->getLocale() == 'ar' ? 'تسوية' : 'Adjust' }}
                                    </button>

                                    <!-- Product History Button -->
                                    <a href="{{ route('admin.inventory.show', $pId) }}" 
                                       class="btn btn-secondary btn-xs" 
                                       title="{{ app()->getLocale() == 'ar' ? 'سجل حركات التركيبة' : 'View Product Audit' }}">
                                        <i class="fa-solid fa-clock-rotate-left mr-1 ml-1"></i> {{ app()->getLocale() == 'ar' ? 'السجل' : 'Audit' }}
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-muted">
                                <i class="fa-solid fa-boxes-stacked fa-2x mb-2" style="display: block; opacity: 0.4;"></i>
                                {{ app()->getLocale() == 'ar' ? 'لم يتم العثور على أي عناصر مخزون مطابقة للبحث أو التصفية الحالية.' : 'No inventory items match the current filter or search criteria.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-pagination :currentPage="$currentPage" :totalPages="$totalPages" :totalItems="$stockItems->total()" />
    </div>

    <!-- Quick Stock Adjustment Modal -->
    <div id="quickAdjustModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 9999; align-items: center; justify-content: center; padding: 1rem;">
        <div class="card" style="max-width: 520px; width: 100%; padding: 2rem; border-radius: var(--radius-lg); box-shadow: var(--shadow-xl); position: relative;">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                <h3 style="font-size: 1.25rem; font-weight: 800; margin: 0;">
                    <i class="fa-solid fa-sliders text-primary mr-1.5 ml-1.5"></i>
                    {{ app()->getLocale() == 'ar' ? 'تسوية المخزون وتعديل الرصيد' : 'Manual Stock Adjustment' }}
                </h3>
                <button type="button" class="btn btn-secondary btn-xs" onclick="closeQuickAdjustModal()">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form action="{{ route('admin.inventory.adjustments.store') }}" method="POST">
                @csrf
                <!-- Target Product -->
                <div style="margin-bottom: 1rem;">
                    <label class="text-xs font-bold text-muted">{{ app()->getLocale() == 'ar' ? 'التركيبة المستهدفة' : 'Target Formulation' }}</label>
                    <select name="product_id" id="modalProductId" class="form-select text-sm" style="width: 100%; margin-top: 0.25rem;" required>
                        @foreach($allProducts as $prod)
                            <option value="{{ $prod->id }}">
                                {{ app()->getLocale() == 'ar' ? ($prod->name_ar ?? $prod->name_en) : $prod->name_en }} ({{ $prod->sku }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Target Location -->
                <div style="margin-bottom: 1rem;">
                    <label class="text-xs font-bold text-muted">{{ app()->getLocale() == 'ar' ? 'موقع المخزون' : 'Inventory Location' }}</label>
                    <select name="location_id" id="modalLocationId" class="form-select text-sm" style="width: 100%; margin-top: 0.25rem;" required>
                        @foreach($locations as $loc)
                            @php
                                $locId = is_array($loc) ? $loc['id'] : $loc->id;
                                $locDisplay = is_array($loc) ? (app()->getLocale() == 'ar' ? ($loc['name_ar'] ?? $loc['name_en']) : $loc['name_en']) : (app()->getLocale() == 'ar' ? ($loc->name_ar ?? $loc->name_en) : $loc->name_en);
                            @endphp
                            <option value="{{ $locId }}">{{ $locDisplay }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Adjustment Type -->
                <div style="margin-bottom: 1rem;">
                    <label class="text-xs font-bold text-muted">{{ app()->getLocale() == 'ar' ? 'نوع الحركة / التسوية' : 'Movement / Adjustment Type' }}</label>
                    <select name="movement_type" id="modalMovementType" class="form-select text-sm" style="width: 100%; margin-top: 0.25rem;" required>
                        <option value="Stock In">{{ app()->getLocale() == 'ar' ? '➕ توريد / إدخال مخزون (Stock In)' : '➕ Stock In (Supplier Inbound)' }}</option>
                        <option value="Stock Out">{{ app()->getLocale() == 'ar' ? '➖ إخراج مخزون (Stock Out)' : '➖ Stock Out' }}</option>
                        <option value="Return">{{ app()->getLocale() == 'ar' ? '↩️ مرتجع عميل صالح (Return)' : '↩️ Customer Return' }}</option>
                        <option value="Damaged">{{ app()->getLocale() == 'ar' ? '⚠️ تالف / تخريد (Damaged)' : '⚠️ Damaged Goods' }}</option>
                        <option value="Expired">{{ app()->getLocale() == 'ar' ? '⏳ منتهي الصلاحية (Expired)' : '⏳ Expired Batch' }}</option>
                        <option value="Manual Adjustment">{{ app()->getLocale() == 'ar' ? '⚙️ تسوية جردية يدوية (Manual Adjustment)' : '⚙️ Manual Stock Adjustment' }}</option>
                    </select>
                </div>

                <!-- Quantity -->
                <div style="margin-bottom: 1rem;">
                    <label class="text-xs font-bold text-muted">{{ app()->getLocale() == 'ar' ? 'الكمية (عدد الوحدات)' : 'Quantity (Units)' }}</label>
                    <input type="number" name="quantity" id="modalQuantity" class="form-control" value="10" min="1" required style="margin-top: 0.25rem;">
                </div>

                <!-- Reason / Justification -->
                <div style="margin-bottom: 1.5rem;">
                    <label class="text-xs font-bold text-muted">{{ app()->getLocale() == 'ar' ? 'سبب وتبرير التسوية (إلزامي للتدقيق)' : 'Reason / Note (Required for Audit)' }}</label>
                    <textarea name="reason" id="modalReason" class="form-control" rows="2" placeholder="{{ app()->getLocale() == 'ar' ? 'مثال: جرد أسبوعي دوري، وصول شحنة توريد جديدة، تلف غلاف كبسولات...' : 'e.g. Weekly inventory audit reconciliation, fresh batch arrival...' }}" required style="margin-top: 0.25rem;"></textarea>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                    <button type="button" class="btn btn-secondary" onclick="closeQuickAdjustModal()">
                        {{ app()->getLocale() == 'ar' ? 'إلغاء' : 'Cancel' }}
                    </button>
                    <button type="submit" class="btn btn-primary font-bold">
                        <i class="fa-solid fa-check mr-1 ml-1"></i> {{ app()->getLocale() == 'ar' ? 'تأكيد التسوية وحفظ الحركة' : 'Execute Adjustment' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openQuickAdjustModal(productId, locationId, productName, currentStock) {
            const modal = document.getElementById('quickAdjustModal');
            if (productId) {
                document.getElementById('modalProductId').value = productId;
            }
            if (locationId) {
                document.getElementById('modalLocationId').value = locationId;
            }
            modal.style.display = 'flex';
        }

        function closeQuickAdjustModal() {
            document.getElementById('quickAdjustModal').style.display = 'none';
        }

        // Close modal on Escape key
        window.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeQuickAdjustModal();
            }
        });
    </script>
</x-layouts.admin>
