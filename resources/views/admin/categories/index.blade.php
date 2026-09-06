<x-layouts.admin 
    :pageTitle="__('admin.menu.categories')" 
    pageSubtitle="Configure biological systems, longevity categories, and clinical subcategories."
    :breadcrumbs="['Categories' => route('admin.categories.index')]"
>
    <x-slot name="actions">
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary font-bold shadow-sm">
            <i class="fa-solid fa-plus mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'إضافة تصنيف جديد' : 'New Category System' }}
        </a>
    </x-slot>

    <!-- Filter Tabs & Toolbar -->
    <div class="mb-6 space-y-4">
        <div class="flex items-center gap-2 border-b border-gray-200 dark:border-gray-700 pb-3 flex-wrap">
            <a href="{{ route('admin.categories.index') }}" 
               class="px-4 py-2 rounded-lg text-sm font-bold transition-colors {{ !$isTrashed ? 'bg-[#0A4F78] text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                <i class="fa-solid fa-layer-group mr-1.5 ml-1.5"></i>
                {{ app()->getLocale() === 'ar' ? 'جميع التصنيفات النشطة' : 'Active Categories' }}
                <span class="ml-1.5 mr-1.5 px-2 py-0.5 text-xs rounded-full {{ !$isTrashed ? 'bg-white/20 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">{{ $activeCount }}</span>
            </a>

            <a href="{{ route('admin.categories.index', ['status' => 'trashed']) }}" 
               class="px-4 py-2 rounded-lg text-sm font-bold transition-colors {{ $isTrashed ? 'bg-red-600 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-red-50 dark:hover:bg-red-900/20' }}">
                <i class="fa-solid fa-trash-can mr-1.5 ml-1.5"></i>
                {{ app()->getLocale() === 'ar' ? 'سلة المحذوفات' : 'Trash Archive' }}
                <span class="ml-1.5 mr-1.5 px-2 py-0.5 text-xs rounded-full {{ $isTrashed ? 'bg-white/20 text-white' : 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300' }}">{{ $trashedCount }}</span>
            </a>
        </div>

        <form method="GET" action="{{ route('admin.categories.index') }}" class="shop-toolbar flex items-center justify-between gap-4 flex-wrap">
            @if($isTrashed)
                <input type="hidden" name="status" value="trashed">
            @endif

            <div class="search-wrapper flex-1 min-w-[260px] max-w-md">
                <svg class="search-icon" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control search-input text-sm" placeholder="{{ app()->getLocale() === 'ar' ? 'بحث في أسماء التصنيفات...' : 'Search categories...' }}">
            </div>

            <button type="submit" class="btn btn-secondary btn-sm font-bold">
                <i class="fa-solid fa-filter mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'بحث' : 'Search' }}
            </button>
        </form>
    </div>

    <div class="card shadow-sm border border-gray-100 dark:border-gray-800">
        <div class="table-responsive" style="border: none; border-radius: 0;">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 60px;">Sort</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'نظام التصنيف' : 'Category System' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'المسمى العربي' : 'Arabic Designation' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'التركيبات المرتبطة' : 'Formulations' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                        <th style="text-align: center;">{{ app()->getLocale() === 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $cat)
                        @php
                            $cId = $cat['id'] ?? $cat->id ?? 1;
                            $cNameEn = $cat['name_en'] ?? $cat->name_en ?? 'Category';
                            $cNameAr = $cat['name_ar'] ?? $cat->name_ar ?? '';
                            $cDisplayName = app()->getLocale() === 'ar' && !empty($cNameAr) ? $cNameAr : $cNameEn;
                            $cSlug = $cat['slug'] ?? $cat->slug ?? '';
                            $cSort = $cat['sort_order'] ?? $cat->sort_order ?? 1;
                            $cCount = $cat['products_count'] ?? $cat->products_count ?? 0;
                            $cStatus = $cat['status'] ?? ($cat['is_active'] ?? true ? 'active' : 'inactive');
                            $isItemTrashed = !empty($cat['deleted_at']);
                        @endphp
                        <tr>
                            <td class="font-bold text-muted text-center">{{ $cSort }}</td>
                            <td>
                                <div class="font-bold text-sm text-primary">
                                    @if(!$isItemTrashed)
                                        <a href="{{ route('admin.categories.edit', $cId) }}" class="hover:underline">
                                            {{ $cNameEn }}
                                        </a>
                                    @else
                                        <span>{{ $cNameEn }}</span>
                                    @endif
                                </div>
                                <div class="text-xs text-muted">{{ $cSlug }}</div>
                            </td>
                            <td class="font-bold" dir="rtl">{{ $cNameAr }}</td>
                            <td class="font-bold">
                                <span class="badge badge-neutral text-xs">
                                    <i class="fa-solid fa-flask mr-1 ml-1"></i> {{ $cCount }} {{ app()->getLocale() === 'ar' ? 'منتج' : 'products' }}
                                </span>
                            </td>
                            <td>
                                @if($isItemTrashed)
                                    <span class="badge badge-danger text-xs font-bold">
                                        <i class="fa-solid fa-trash-can mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'محذوف' : 'Trashed' }}
                                    </span>
                                @else
                                    <x-status-badge :status="$cStatus" />
                                @endif
                            </td>
                            <td>
                                <div class="table-actions justify-center flex items-center gap-1.5">
                                    @if(!$isItemTrashed)
                                        <a href="{{ route('admin.categories.edit', $cId) }}" class="action-btn" title="{{ __('app.actions.edit') }}">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <button type="button" class="action-btn action-danger cursor-pointer" 
                                                onclick="confirmDelete('{{ route('admin.categories.destroy', $cId) }}', '{{ addslashes($cDisplayName) }}', false)" 
                                                title="{{ __('app.actions.delete') }}">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    @else
                                        <button type="button" class="action-btn text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 cursor-pointer" 
                                                onclick="confirmRestore('{{ route('admin.categories.restore', $cId) }}', '{{ addslashes($cDisplayName) }}')" 
                                                title="{{ app()->getLocale() === 'ar' ? 'استعادة' : 'Restore' }}">
                                            <i class="fa-solid fa-rotate-left"></i>
                                        </button>
                                        <button type="button" class="action-btn action-danger cursor-pointer" 
                                                onclick="confirmDelete('{{ route('admin.categories.force-delete', $cId) }}', '{{ addslashes($cDisplayName) }}', true)" 
                                                title="{{ app()->getLocale() === 'ar' ? 'حذف نهائي فوري' : 'Force Delete Permanently' }}">
                                            <i class="fa-solid fa-radiation"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-8 text-muted">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i class="fa-solid fa-layer-group text-3xl text-gray-400"></i>
                                    <p class="text-sm font-semibold">{{ app()->getLocale() === 'ar' ? 'لا توجد تصنيفات مطابقة' : 'No categories found' }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-pagination :currentPage="$currentPage" :totalPages="$totalPages" :totalItems="count($categories)" />
    </div>
</x-layouts.admin>
