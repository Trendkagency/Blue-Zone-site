<x-layouts.admin 
    :pageTitle="__('admin.menu.stock_history')" 
    :pageSubtitle="app()->getLocale() == 'ar' ? 'سجل تدقيق المخزون المالي والمكاني لجميع عمليات الإدخال والإخراج والتحويل والمبيعات والتسويات.' : 'Auditable ledger tracking every single inbound, outbound, transfer, sale, and adjustment transaction.'"
    :breadcrumbs="[__('admin.menu.inventory') => route('admin.inventory.index'), __('admin.menu.stock_history') => route('admin.inventory.history')]"
>
    <!-- Filter Bar Form -->
    <div class="card" style="padding: 1.25rem 1.5rem; margin-bottom: 1.5rem;">
        <form method="GET" action="{{ route('admin.inventory.history') }}" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center; justify-content: space-between;">
            
            <!-- Search wrapper -->
            <div class="search-wrapper" style="flex: 1; min-width: 240px;">
                <svg class="search-icon" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" name="search" value="{{ $search }}" class="form-control search-input text-sm" placeholder="{{ app()->getLocale() == 'ar' ? 'بحث برقم الحركة، التركيبة، SKU، المستخدم...' : 'Filter by Log ID, product, SKU, user, note...' }}">
            </div>

            <!-- Movement Type Dropdown (All 10 Types) -->
            <div style="min-width: 180px;">
                <select name="movement_type" class="form-select text-sm" style="width: 100%;" onchange="this.form.submit()">
                    <option value="all">{{ app()->getLocale() == 'ar' ? 'جميع أنواع الحركات' : 'All Movement Types' }}</option>
                    @foreach($movementTypes as $key => $type)
                        <option value="{{ $key }}" {{ $selectedType === $key ? 'selected' : '' }}>
                            {{ app()->getLocale() == 'ar' ? $type['ar'] : $type['en'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Product Dropdown -->
            <div style="min-width: 180px;">
                <select name="product_id" class="form-select text-sm" style="width: 100%;" onchange="this.form.submit()">
                    <option value="all">{{ app()->getLocale() == 'ar' ? 'جميع التركيبات' : 'All Formulations' }}</option>
                    @foreach($products as $prod)
                        <option value="{{ $prod->id }}" {{ $selectedProduct == $prod->id ? 'selected' : '' }}>
                            {{ app()->getLocale() == 'ar' ? ($prod->name_ar ?? $prod->name_en) : $prod->name_en }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Location Filter -->
            <div style="min-width: 160px;">
                <select name="location" class="form-select text-sm" style="width: 100%;" onchange="this.form.submit()">
                    <option value="all">{{ app()->getLocale() == 'ar' ? 'جميع المواقع' : 'All Locations' }}</option>
                    @foreach($locations as $loc)
                        @php
                            $locId = is_array($loc) ? $loc['id'] : $loc->id;
                            $locDisplay = app()->getLocale() === 'ar' ? (is_array($loc) ? ($loc['name_ar'] ?? $loc['name_en']) : ($loc->name_ar ?? $loc->name_en)) : (is_array($loc) ? $loc['name_en'] : $loc->name_en);
                        @endphp
                        <option value="{{ $locId }}" {{ $selectedLocation === $locId ? 'selected' : '' }}>
                            {{ $locDisplay }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-secondary btn-sm">
                    <i class="fa-solid fa-filter mr-1 ml-1"></i> {{ app()->getLocale() == 'ar' ? 'تصفية' : 'Filter' }}
                </button>
                @if($search || $selectedType !== 'all' || $selectedProduct !== 'all' || $selectedLocation !== 'all')
                    <a href="{{ route('admin.inventory.history') }}" class="btn btn-neutral btn-sm" title="Clear Filters">
                        <i class="fa-solid fa-rotate-left mr-1 ml-1"></i> {{ app()->getLocale() == 'ar' ? 'إعادة ضبط' : 'Reset' }}
                    </a>
                @endif
                <button type="button" class="btn btn-secondary btn-sm" onclick="window.print()">
                    <i class="fa-solid fa-print mr-1 ml-1"></i> {{ app()->getLocale() == 'ar' ? 'طباعة' : 'Print' }}
                </button>
            </div>
        </form>
    </div>

    <!-- Movement Ledger Table -->
    <div class="card">
        <div class="table-responsive" style="border: none; border-radius: 0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('admin.inventory.movement_id') }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'التركيبة والمواصفة' : 'Product & Variant' }}</th>
                        <th>{{ __('admin.inventory.movement_type') }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'مسار الحركة (من ← إلى)' : 'Routing (From → To)' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'فرق الكمية' : 'Qty Delta' }}</th>
                        <th>{{ __('admin.inventory.prev_qty') }} → {{ __('admin.inventory.new_qty') }}</th>
                        <th>{{ __('admin.inventory.logged_user') }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'الوقت والتبرير' : 'Timestamp & Justification' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movements as $m)
                        @php
                            $mId = $m->movement_number ?? ('MOV-' . $m->id);
                            $mName = app()->getLocale() == 'ar' ? ($m->product_name_ar ?? $m->product_name_en ?? 'تركيبة حيوية') : ($m->product_name_en ?? 'Compound');
                            $mSku = $m->sku ?? 'BZ-SKU';
                            $mVariant = $m->variant ?? 'Standard Pack';
                            $mType = $m->movement_type ?? 'Transfer';
                            $mFrom = $m->from_location ?? 'Central Warehouse';
                            $mTo = $m->to_location ?? 'Boutique POS';
                            $mQty = (int) $m->quantity;
                            $mPrev = $m->previous_qty ?? 0;
                            $mNew = $m->new_qty ?? 0;
                            $mUser = $m->user ?? 'Admin';
                            $mDate = $m->date?->format('Y-m-d') ?? '';
                            $mTime = $m->time ?? '';
                            $mReason = $m->note ?? '';

                            // Badge color mapping
                            $badgeClass = 'badge-neutral';
                            if (in_array($mType, ['Stock In', 'Return'], true)) {
                                $badgeClass = 'badge-success';
                            } elseif (in_array($mType, ['Online Sale', 'Offline Sale'], true)) {
                                $badgeClass = 'badge-primary';
                            } elseif (in_array($mType, ['Damaged', 'Expired', 'Stock Out'], true)) {
                                $badgeClass = 'badge-danger';
                            } elseif ($mType === 'Stock Transfer') {
                                $badgeClass = 'badge-warning';
                            } elseif ($mType === 'Cancelled Order') {
                                $badgeClass = 'badge-accent';
                            }
                        @endphp
                        <tr>
                            <td class="font-mono text-xs font-bold text-primary">{{ $mId }}</td>
                            <td>
                                <div class="font-bold text-sm">
                                    <a href="{{ route('admin.inventory.show', $m->product_id ?? 1) }}">
                                        {{ $mName }}
                                    </a>
                                </div>
                                <div class="text-xs text-muted font-mono">{{ $mSku }} • {{ $mVariant }}</div>
                            </td>
                            <td>
                                <span class="badge {{ $badgeClass }} text-xs font-bold">{{ $mType }}</span>
                            </td>
                            <td class="text-xs">
                                <div><strong>{{ app()->getLocale() == 'ar' ? 'من:' : 'From:' }}</strong> {{ $mFrom }}</div>
                                <div><strong>{{ app()->getLocale() == 'ar' ? 'إلى:' : 'To:' }}</strong> {{ $mTo }}</div>
                            </td>
                            <td class="font-bold {{ $mQty < 0 ? 'text-danger' : 'text-success' }}">
                                {{ $mQty > 0 ? '+' : '' }}{{ $mQty }}
                            </td>
                            <td class="text-xs font-mono">
                                {{ $mPrev }} → <strong>{{ $mNew }}</strong>
                            </td>
                            <td class="text-xs">
                                <i class="fa-solid fa-user-shield mr-1 ml-1 text-muted"></i> {{ $mUser }}
                            </td>
                            <td class="text-xs text-muted">
                                <div>{{ $mDate }} {{ $mTime }}</div>
                                @if($mReason)
                                    <div class="text-secondary" style="font-style: italic; margin-top: 0.15rem;">"{{ $mReason }}"</div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-muted">
                                <i class="fa-solid fa-clock-rotate-left fa-2x mb-2" style="display: block; opacity: 0.4;"></i>
                                {{ app()->getLocale() == 'ar' ? 'لم يتم العثور على أي حركات مخزون مطابقة لشروط التصفية.' : 'No inventory movements found matching the filter criteria.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-pagination :currentPage="$currentPage" :totalPages="$totalPages" :totalItems="$movements->total()" />
    </div>
</x-layouts.admin>
