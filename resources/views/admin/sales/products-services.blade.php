<x-layouts.admin 
    :pageTitle="app()->getLocale() === 'ar' ? 'المنتجات والخدمات التجارية' : 'Commercial Products & Services Catalog'" 
    :pageSubtitle="app()->getLocale() === 'ar' ? 'دليل التسعير التجاري، التركيبات الصيدلانية، البروتوكولات الإكلينيكية، وهوامش الربحية لأوامر البيع وعروض الأسعار.' : 'Commercial pricing book, longevity formulations, clinical diagnostic protocols, and sales margins.'"
    :breadcrumbs="[
        (app()->getLocale() === 'ar' ? 'المبيعات' : 'Sales') => route('admin.sales.orders.index'),
        (app()->getLocale() === 'ar' ? 'المنتجات والخدمات' : 'Products & Services') => route('admin.sales.products-services')
    ]"
>
    <x-slot name="actions">
        <!-- New Quotation Quick Action -->
        <a href="{{ route('admin.sales.quotations.create') }}" class="btn btn-secondary font-semibold text-xs sm:text-sm">
            <i class="fa-solid fa-file-invoice text-sky-400 mr-1.5 ml-1.5"></i>
            {{ app()->getLocale() === 'ar' ? 'إنشاء عرض سعر' : 'New Quotation' }}
        </a>

        <!-- New POS Sale Action -->
        <a href="{{ route('admin.offline-sales.create') }}" class="btn btn-secondary font-semibold text-xs sm:text-sm">
            <i class="fa-solid fa-calculator text-emerald-400 mr-1.5 ml-1.5"></i>
            {{ app()->getLocale() === 'ar' ? 'نقطة بيع سريعة (POS)' : 'New POS Sale' }}
        </a>

        <!-- Add New Formulation or Service -->
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary font-bold text-xs sm:text-sm shadow-sm">
            <i class="fa-solid fa-plus mr-1.5 ml-1.5"></i>
            {{ app()->getLocale() === 'ar' ? 'إضافة منتج أو خدمة جديدة' : 'Add Product / Service' }}
        </a>
    </x-slot>

    <div class="space-y-6">

        <!-- Flash Notices -->
        @if(session('status'))
            <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-800 dark:text-emerald-300 text-sm flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400 text-lg"></i>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <!-- 1. Commercial Intelligence Metrics -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- Total Catalog Offerings -->
            <div class="card p-5 bg-white dark:bg-[#062B49] border border-slate-200/90 dark:border-[#0E4A7A] rounded-2xl shadow-xs border-s-4 border-s-indigo-500">
                <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider">{{ app()->getLocale() === 'ar' ? 'إجمالي المعروضات التجارية' : 'Total Catalog Offerings' }}</span>
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-boxes-packing text-sm"></i>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">
                    {{ $totalItems }}
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">{{ app()->getLocale() === 'ar' ? 'بند تجاري' : 'items' }}</span>
                </div>
                <div class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium flex items-center gap-2">
                    <span>{{ $productsCount }} {{ app()->getLocale() === 'ar' ? 'منتج ملموس' : 'Products' }}</span>
                    <span>•</span>
                    <span>{{ $servicesCount }} {{ app()->getLocale() === 'ar' ? 'خدمة وبروتوكول' : 'Services' }}</span>
                </div>
            </div>

            <!-- Commercial Valuation -->
            <div class="card p-5 bg-white dark:bg-[#062B49] border border-slate-200/90 dark:border-[#0E4A7A] rounded-2xl shadow-xs border-s-4 border-s-emerald-500">
                <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider">{{ app()->getLocale() === 'ar' ? 'قيمة البيع التقديرية للأرصدة' : 'Retail Inventory Equity' }}</span>
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-tag text-sm"></i>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-black text-emerald-600 dark:text-emerald-400 tracking-tight font-mono">
                    @currency($totalRetailValuation)
                </div>
                <div class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium">
                    {{ app()->getLocale() === 'ar' ? 'محسوبة بأسعار البيع بالتجزئة المعتمدة' : 'Valued at active consumer retail pricing' }}
                </div>
            </div>

            <!-- Average Commercial Margin -->
            <div class="card p-5 bg-white dark:bg-[#062B49] border border-slate-200/90 dark:border-[#0E4A7A] rounded-2xl shadow-xs border-s-4 border-s-sky-500">
                <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider">{{ app()->getLocale() === 'ar' ? 'متوسط الهامش الربحي' : 'Average Gross Margin' }}</span>
                    <div class="w-8 h-8 rounded-lg bg-sky-50 dark:bg-sky-500/10 text-sky-600 dark:text-sky-400 border border-sky-200 dark:border-sky-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-chart-line-up text-sm"></i>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-black text-sky-600 dark:text-sky-400 tracking-tight font-mono">
                    {{ $avgMargin }}%
                </div>
                <div class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium">
                    {{ app()->getLocale() === 'ar' ? 'الفارق بين سعر التوريد وسعر البيع' : 'Gross commercial markup over landing cost' }}
                </div>
            </div>

            <!-- Clinical Services & Consultations -->
            <div class="card p-5 bg-white dark:bg-[#062B49] border border-slate-200/90 dark:border-[#0E4A7A] rounded-2xl shadow-xs border-s-4 border-s-purple-500">
                <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider">{{ app()->getLocale() === 'ar' ? 'الخدمات والبروتوكولات' : 'Clinical Protocols' }}</span>
                    <div class="w-8 h-8 rounded-lg bg-purple-50 dark:bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-200 dark:border-purple-500/20 flex items-center justify-center">
                        <i class="fa-solid fa-stethoscope text-sm"></i>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-black text-purple-600 dark:text-purple-400 tracking-tight font-mono">
                    {{ $servicesCount }}
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">{{ app()->getLocale() === 'ar' ? 'خدمة نشطة' : 'services' }}</span>
                </div>
                <div class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-medium">
                    {{ app()->getLocale() === 'ar' ? 'استشارات طول العمر وفحوصات المؤشرات الحيوية' : 'Longevity consults & biomarker packages' }}
                </div>
            </div>

        </div>

        <!-- 2. Catalog Search & Filter Tabs -->
        <div class="card p-4 sm:p-5 bg-white dark:bg-[#062B49] border border-slate-200/90 dark:border-[#0E4A7A] rounded-2xl shadow-xs">
            <form method="GET" action="{{ route('admin.sales.products-services') }}" class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 flex-wrap">
                <!-- Search -->
                <div class="flex-1 min-w-[220px]">
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute top-1/2 -translate-y-1/2 start-3.5 text-slate-400 text-xs pointer-events-none"></i>
                        <input type="text" name="search" value="{{ $search }}" class="form-control ps-9 text-xs sm:text-sm w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-xl" placeholder="{{ app()->getLocale() === 'ar' ? 'بحث برمز SKU، اسم المنتج أو الخدمة، الباركود...' : 'Filter by SKU, formulation name, barcode...' }}">
                    </div>
                </div>

                <!-- Type Selector (All, Product, Service) -->
                <div class="w-full sm:w-auto min-w-[170px]">
                    <select name="type" class="form-select text-xs sm:text-sm w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-xl" onchange="this.form.submit()">
                        <option value="all">{{ app()->getLocale() === 'ar' ? 'جميع البنود (منتجات وخدمات)' : 'All Offerings (Products & Services)' }}</option>
                        <option value="product" {{ $selectedType === 'product' ? 'selected' : '' }}>💊 {{ app()->getLocale() === 'ar' ? 'المنتجات والتركيبات الحيوية' : 'Physical Formulations' }}</option>
                        <option value="service" {{ $selectedType === 'service' ? 'selected' : '' }}>🩺 {{ app()->getLocale() === 'ar' ? 'الخدمات والبروتوكولات الإكلينيكية' : 'Clinical Protocols & Services' }}</option>
                    </select>
                </div>

                <!-- Category Selector -->
                <div class="w-full sm:w-auto min-w-[170px]">
                    <select name="category_id" class="form-select text-xs sm:text-sm w-full bg-slate-50 dark:bg-[#041E34] border-slate-200 dark:border-[#0E4A7A] text-slate-900 dark:text-white rounded-xl" onchange="this.form.submit()">
                        <option value="all">{{ app()->getLocale() === 'ar' ? 'جميع التصنيفات' : 'All Categories' }}</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ $selectedCategory == $cat->id ? 'selected' : '' }}>
                                {{ app()->getLocale() === 'ar' ? ($cat->name_ar ?? $cat->name_en) : $cat->name_en }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Actions -->
                <div class="flex items-center gap-2">
                    <button type="submit" class="btn btn-secondary btn-sm text-xs font-bold px-3">
                        <i class="fa-solid fa-filter mr-1 ml-1 text-slate-400"></i>
                        {{ app()->getLocale() === 'ar' ? 'تصفية' : 'Filter' }}
                    </button>
                    @if($search || $selectedType !== 'all' || $selectedCategory !== 'all')
                        <a href="{{ route('admin.sales.products-services') }}" class="btn btn-neutral btn-sm text-xs" title="Reset Filters">
                            <i class="fa-solid fa-rotate-left mr-1 ml-1"></i>
                            {{ app()->getLocale() === 'ar' ? 'إلغاء' : 'Reset' }}
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- 3. Commercial Catalog Data Grid -->
        <div class="card bg-white dark:bg-[#062B49] border border-slate-200/90 dark:border-[#0E4A7A] rounded-2xl shadow-xs overflow-hidden">
            <div class="table-responsive">
                <table class="table w-full text-start" id="salesProductsServicesTable">
                    <thead class="bg-slate-50 dark:bg-[#041E34] border-b border-slate-200/80 dark:border-[#0E4A7A] text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        <tr>
                            <th class="py-3.5 px-4 text-start">{{ app()->getLocale() === 'ar' ? 'البند والمواصفة' : 'Offering Item' }}</th>
                            <th class="py-3.5 px-4 text-start">{{ app()->getLocale() === 'ar' ? 'النوع' : 'Type' }}</th>
                            <th class="py-3.5 px-4 text-start">{{ app()->getLocale() === 'ar' ? 'التصنيف الطبي' : 'Category' }}</th>
                            <th class="py-3.5 px-4 text-end">{{ app()->getLocale() === 'ar' ? 'سعر البيع' : 'Retail Price' }}</th>
                            <th class="py-3.5 px-4 text-end">{{ app()->getLocale() === 'ar' ? 'التكلفة والهامش' : 'Cost & Margin' }}</th>
                            <th class="py-3.5 px-4 text-center">{{ app()->getLocale() === 'ar' ? 'جاهزية التوريد' : 'Availability / Stock' }}</th>
                            <th class="py-3.5 px-4 text-end">{{ app()->getLocale() === 'ar' ? 'الإجراءات التجارية' : 'Commercial Actions' }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                        @forelse($items as $item)
                            @php
                                $price = (float) $item->price;
                                $cost = (float) ($item->cost_price ?? ($price * 0.45));
                                $margin = $price > 0 ? round((($price - $cost) / $price) * 100, 1) : 0;
                                $isService = ($item->type === 'service');
                                $totalStock = (int) $item->stock_online + (int) $item->stock_offline;
                            @endphp
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-colors">
                                <!-- Item Info -->
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ \App\Models\Product::normalizeUrl($item->primary_image_url ?? $item->image) }}" alt="{{ $item->name_en }}" class="w-10 h-10 rounded-lg object-cover border border-slate-200 dark:border-slate-700 flex-shrink-0 bg-slate-100 dark:bg-slate-800" onerror="this.src='/images/placeholder-product.svg'">
                                        <div class="min-w-0">
                                            <a href="{{ route('admin.products.edit', $item->id) }}" class="font-bold text-slate-900 dark:text-white hover:text-primary transition-colors block truncate">
                                                {{ app()->getLocale() === 'ar' ? ($item->name_ar ?? $item->name_en) : $item->name_en }}
                                            </a>
                                            <div class="text-[11px] font-mono text-slate-400 mt-0.5 flex items-center gap-2">
                                                <span>{{ $item->sku }}</span>
                                                @if($item->barcode)
                                                    <span>•</span>
                                                    <span>{{ $item->barcode }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Type -->
                                <td class="py-3.5 px-4">
                                    @if($isService)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-purple-50 dark:bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-200 dark:border-purple-500/20">
                                            <i class="fa-solid fa-stethoscope text-[9px]"></i>
                                            {{ app()->getLocale() === 'ar' ? 'خدمة / بروتوكول' : 'Clinical Service' }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20">
                                            <i class="fa-solid fa-pills text-[9px]"></i>
                                            {{ app()->getLocale() === 'ar' ? 'منتج دوائي / مكمل' : 'Product' }}
                                        </span>
                                    @endif
                                </td>

                                <!-- Category -->
                                <td class="py-3.5 px-4 text-slate-600 dark:text-slate-300">
                                    <span class="text-xs font-semibold">
                                        {{ app()->getLocale() === 'ar' ? ($item->category?->name_ar ?? $item->category?->name_en ?? 'عام') : ($item->category?->name_en ?? 'General') }}
                                    </span>
                                </td>

                                <!-- Retail Price -->
                                <td class="py-3.5 px-4 text-end font-mono">
                                    <div class="text-sm font-black text-slate-900 dark:text-white">
                                        @currency($price)
                                    </div>
                                    @if($item->sale_price && $item->sale_price < $price)
                                        <div class="text-[10px] text-emerald-600 dark:text-emerald-400 font-bold">
                                            {{ app()->getLocale() === 'ar' ? 'عرض خاص:' : 'Special Promo:' }} @currency($item->sale_price)
                                        </div>
                                    @endif
                                </td>

                                <!-- Cost & Margin -->
                                <td class="py-3.5 px-4 text-end font-mono">
                                    <div class="text-xs font-bold text-slate-500 dark:text-slate-400">
                                        @currency($cost)
                                    </div>
                                    <div class="text-[11px] font-black text-sky-600 dark:text-sky-400">
                                        {{ $margin }}% {{ app()->getLocale() === 'ar' ? 'هامش' : 'margin' }}
                                    </div>
                                </td>

                                <!-- Availability / Stock -->
                                <td class="py-3.5 px-4 text-center font-mono">
                                    @if($isService)
                                        <span class="inline-flex items-center gap-1 text-emerald-600 dark:text-emerald-400 text-xs font-bold">
                                            <i class="fa-solid fa-calendar-check text-[10px]"></i>
                                            {{ app()->getLocale() === 'ar' ? 'متاح للحجز' : 'On Schedule' }}
                                        </span>
                                    @else
                                        @if($totalStock > 10)
                                            <span class="badge badge-success text-[11px] font-bold">
                                                {{ $totalStock }} {{ app()->getLocale() === 'ar' ? 'متوفر' : 'units' }}
                                            </span>
                                        @elseif($totalStock > 0)
                                            <span class="badge badge-warning text-[11px] font-bold">
                                                {{ $totalStock }} {{ app()->getLocale() === 'ar' ? 'منخفض' : 'low' }}
                                            </span>
                                        @else
                                            <span class="badge badge-danger text-[11px] font-bold">
                                                {{ app()->getLocale() === 'ar' ? 'غير متوفر (0)' : 'Out of Stock' }}
                                            </span>
                                        @endif
                                    @endif
                                </td>

                                <!-- Commercial Actions -->
                                <td class="py-3.5 px-4 text-end">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- Add to Quote -->
                                        <a href="{{ route('admin.sales.quotations.create', ['product_id' => $item->id]) }}" class="btn btn-secondary btn-sm text-[11px] font-bold py-1 px-2.5 text-sky-600 dark:text-sky-400" title="{{ app()->getLocale() === 'ar' ? 'إدراج في عرض سعر' : 'Add to Quotation' }}">
                                            <i class="fa-solid fa-file-invoice mr-1 ml-1"></i>
                                            {{ app()->getLocale() === 'ar' ? 'تسعير' : 'Quote' }}
                                        </a>

                                        <!-- POS Direct Sale -->
                                        <a href="{{ route('admin.offline-sales.create', ['product_id' => $item->id]) }}" class="btn btn-primary btn-sm text-[11px] font-bold py-1 px-2.5 shadow-xs" title="{{ app()->getLocale() === 'ar' ? 'بيع مباشر بالكاشير' : 'POS Checkout' }}">
                                            <i class="fa-solid fa-cash-register mr-1 ml-1"></i>
                                            {{ app()->getLocale() === 'ar' ? 'بيع' : 'Sell' }}
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-12 text-slate-400">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 mx-auto flex items-center justify-center text-xl mb-3 opacity-60">
                                        <i class="fa-solid fa-boxes-packing"></i>
                                    </div>
                                    <h4 class="text-sm font-bold text-slate-800 dark:text-slate-200">
                                        {{ app()->getLocale() === 'ar' ? 'لم يتم العثور على أي منتجات أو خدمات مطابقة للبحث' : 'No products or services found matching criteria' }}
                                    </h4>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($items->hasPages())
                <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                    <x-pagination :currentPage="$currentPage" :totalPages="$totalPages" :totalItems="$items->total()" />
                </div>
            @endif
        </div>

    </div>
</x-layouts.admin>
