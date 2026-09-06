<x-layouts.admin 
    :pageTitle="__('admin.menu.products')" 
    pageSubtitle="Manage clinical formulations, batch metadata, pricing, and variant availability."
    :breadcrumbs="['Products' => route('admin.products.index')]"
>
    <x-slot name="actions">
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary font-bold shadow-sm">
            <i class="fa-solid fa-plus mr-1.5 ml-1.5"></i> {{ __('admin.menu.add_product') }}
        </a>
    </x-slot>

    <!-- Toolbar & Filter Tabs -->
    <div class="mb-6 space-y-4">
        <div class="flex items-center gap-2 border-b border-gray-200 dark:border-gray-700 pb-3 flex-wrap">
            <a href="{{ route('admin.products.index') }}" 
               class="px-4 py-2 rounded-lg text-sm font-bold transition-colors {{ !$isTrashed && empty($currentStatus) ? 'bg-[#0A4F78] text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                <i class="fa-solid fa-boxes-stacked mr-1.5 ml-1.5"></i>
                {{ app()->getLocale() === 'ar' ? 'جميع التركيبات النشطة' : 'All Active Formulations' }}
                <span class="ml-1.5 mr-1.5 px-2 py-0.5 text-xs rounded-full {{ !$isTrashed && empty($currentStatus) ? 'bg-white/20 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">{{ $activeCount }}</span>
            </a>

            <a href="{{ route('admin.products.index', ['status' => 'trashed']) }}" 
               class="px-4 py-2 rounded-lg text-sm font-bold transition-colors {{ $isTrashed ? 'bg-red-600 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-red-50 dark:hover:bg-red-900/20' }}">
                <i class="fa-solid fa-trash-can mr-1.5 ml-1.5"></i>
                {{ app()->getLocale() === 'ar' ? 'سلة المحذوفات' : 'Trash Archive' }}
                <span class="ml-1.5 mr-1.5 px-2 py-0.5 text-xs rounded-full {{ $isTrashed ? 'bg-white/20 text-white' : 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300' }}">{{ $trashedCount }}</span>
            </a>
        </div>

        <form method="GET" action="{{ route('admin.products.index') }}" class="shop-toolbar flex items-center justify-between gap-4 flex-wrap">
            @if($isTrashed)
                <input type="hidden" name="status" value="trashed">
            @endif

            <div class="search-wrapper flex-1 min-w-[260px] max-w-md">
                <svg class="search-icon" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control search-input text-sm" placeholder="{{ app()->getLocale() === 'ar' ? 'بحث بالاسم، الرمز SKU، الباركود...' : 'Search formulation, SKU, barcode...' }}">
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                <select name="category_id" onchange="this.form.submit()" class="form-select text-sm w-auto">
                    <option value="">{{ app()->getLocale() === 'ar' ? 'جميع التصنيفات' : 'All Categories' }}</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat['id'] ?? $cat->id }}" {{ request('category_id') == ($cat['id'] ?? $cat->id) ? 'selected' : '' }}>
                            {{ app()->getLocale() === 'ar' ? ($cat['name_ar'] ?? $cat['name_en'] ?? $cat->name_ar) : ($cat['name_en'] ?? $cat->name_en) }}
                        </option>
                    @endforeach
                </select>

                @if(!$isTrashed)
                    <select name="status" onchange="this.form.submit()" class="form-select text-sm w-auto">
                        <option value="">{{ app()->getLocale() === 'ar' ? 'كل الحالات' : 'All Statuses' }}</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="low_stock" {{ request('status') === 'low_stock' ? 'selected' : '' }}>Low Stock Alert</option>
                        <option value="out_of_stock" {{ request('status') === 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                    </select>
                @endif

                <button type="submit" class="btn btn-secondary btn-sm font-bold">
                    <i class="fa-solid fa-filter mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'تصفية' : 'Filter' }}
                </button>
            </div>
        </form>
    </div>

    <!-- Products Data Table -->
    <div class="card shadow-sm border border-gray-100 dark:border-gray-800">
        <div class="table-responsive" style="border: none; border-radius: 0;">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 40px;"><input type="checkbox" class="form-check-input"></th>
                        <th>{{ app()->getLocale() === 'ar' ? 'التركيبة' : 'Product Formulation' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'الرمز والباركود' : 'SKU / Barcode' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'التصنيف' : 'Category' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'سعر البيع' : 'Retail Price' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'مخزون الأونلاين' : 'Online Stock' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'مخزون البوتيك' : 'Offline Stock' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                        <th style="text-align: center;">{{ app()->getLocale() === 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        @php
                            $pId = $product['id'] ?? $product->id ?? 1;
                            $pNameEn = $product['name_en'] ?? $product->name_en ?? 'Formulation';
                            $pNameAr = $product['name_ar'] ?? $product->name_ar ?? '';
                            $pDisplayName = app()->getLocale() === 'ar' && !empty($pNameAr) ? $pNameAr : $pNameEn;
                            $pSku = $product['sku'] ?? $product->sku ?? 'BZ-SKU';
                            $pBarcode = $product['barcode'] ?? $product->barcode ?? '6281100000000';
                            $pCat = $product['category_en'] ?? $product['category_name_en'] ?? 'Precision Cellular';
                            $pPrice = $product['price'] ?? $product->price ?? 0;
                            $pOnlineStock = $product['stock_online'] ?? $product->stock_online ?? 0;
                            $pOfflineStock = $product['stock_offline'] ?? $product->stock_offline ?? 0;
                            $pLowThreshold = $product['low_stock_threshold'] ?? $product->low_stock_threshold ?? 10;
                            $pStatus = $product['status'] ?? $product->status ?? 'active';
                            $pImage = $product['image'] ?? $product->image ?? 'assets/products/blue-mind.jpg';
                            $isItemTrashed = !empty($product['deleted_at']);
                        @endphp
                        <tr>
                            <td><input type="checkbox" class="form-check-input"></td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <img src="{{ asset($pImage) }}" alt="{{ $pNameEn }}" style="width: 44px; height: 44px; border-radius: var(--radius-sm); object-fit: cover; background: var(--color-bg-subtle);" onerror="this.onerror=null; this.src='{{ asset('image.jpg') }}';">
                                    <div>
                                        <div class="font-bold text-sm">
                                            @if(!$isItemTrashed)
                                                <a href="{{ route('admin.products.edit', $pId) }}" class="text-primary hover:underline">
                                                    {{ $pDisplayName }}
                                                </a>
                                            @else
                                                <span class="text-gray-700 dark:text-gray-300">{{ $pDisplayName }}</span>
                                            @endif
                                        </div>
                                        @if($pNameAr && app()->getLocale() !== 'ar')
                                            <div class="text-xs text-muted" dir="rtl">
                                                {{ $pNameAr }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="font-mono text-xs">
                                <div>{{ $pSku }}</div>
                                <div class="text-muted">{{ $pBarcode }}</div>
                            </td>
                            <td>
                                <span class="badge badge-neutral text-xs">{{ $pCat }}</span>
                            </td>
                            <td class="font-bold">${{ number_format((float)$pPrice, 2) }}</td>
                            <td>
                                <span class="badge {{ $pOnlineStock <= $pLowThreshold ? 'badge-warning' : 'badge-success' }}">
                                    {{ $pOnlineStock }} units
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $pOfflineStock <= $pLowThreshold ? 'badge-danger' : 'badge-neutral' }}">
                                    {{ $pOfflineStock }} units
                                </span>
                            </td>
                            <td>
                                @if($isItemTrashed)
                                    <span class="badge badge-danger text-xs font-bold">
                                        <i class="fa-solid fa-trash-can mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'محذوف' : 'Trashed' }}
                                    </span>
                                @else
                                    <x-status-badge :status="$pStatus" />
                                @endif
                            </td>
                            <td>
                                <div class="table-actions justify-center flex items-center gap-1.5">
                                    @if(!$isItemTrashed)
                                        <a href="{{ route('admin.products.edit', $pId) }}" class="action-btn" title="{{ __('app.actions.edit') }}">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <a href="{{ route('admin.products.show', $pId) }}" class="action-btn" title="{{ __('app.actions.view') }}">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <button type="button" class="action-btn action-danger cursor-pointer" 
                                                onclick="confirmDelete('{{ route('admin.products.destroy', $pId) }}', '{{ addslashes($pDisplayName) }}', false)" 
                                                title="{{ __('app.actions.delete') }}">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    @else
                                        <button type="button" class="action-btn text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 cursor-pointer" 
                                                onclick="confirmRestore('{{ route('admin.products.restore', $pId) }}', '{{ addslashes($pDisplayName) }}')" 
                                                title="{{ app()->getLocale() === 'ar' ? 'استعادة' : 'Restore' }}">
                                            <i class="fa-solid fa-rotate-left"></i>
                                        </button>
                                        <button type="button" class="action-btn action-danger cursor-pointer" 
                                                onclick="confirmDelete('{{ route('admin.products.force-delete', $pId) }}', '{{ addslashes($pDisplayName) }}', true)" 
                                                title="{{ app()->getLocale() === 'ar' ? 'حذف نهائي فوري' : 'Force Delete Permanently' }}">
                                            <i class="fa-solid fa-radiation"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-8 text-muted">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i class="fa-solid fa-box-open text-3xl text-gray-400"></i>
                                    <p class="text-sm font-semibold">{{ app()->getLocale() === 'ar' ? 'لا توجد عناصر مطابقة في هذا القسم' : 'No formulations found matching criteria' }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-pagination :currentPage="$currentPage" :totalPages="$totalPages" :totalItems="count($products)" />
    </div>
</x-layouts.admin>
