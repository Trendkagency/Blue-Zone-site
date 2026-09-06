<x-layouts.admin 
    :pageTitle="__('admin.orders.title')" 
    :pageSubtitle="__('admin.orders.subtitle')"
    :breadcrumbs="[__('admin.menu.sales') => route('admin.orders.index'), __('admin.orders.title') => route('admin.orders.index')]"
>
    <!-- Filter & Status Tabs -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div style="display: flex; gap: 0.5rem; align-items: center;">
            <a href="{{ route('admin.orders.index') }}" class="btn btn-sm {{ !$isTrashed ? 'btn-primary font-bold' : 'btn-secondary' }}">
                <i class="fa-solid fa-bag-shopping mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'الطلبات النشطة' : 'Active Orders' }}
                <span class="badge badge-neutral text-xs" style="margin-inline-start: 0.35rem;">{{ $activeCount }}</span>
            </a>
            <a href="{{ route('admin.orders.index', ['status' => 'trashed']) }}" class="btn btn-sm {{ $isTrashed ? 'btn-danger font-bold' : 'btn-ghost' }}" style="{{ $isTrashed ? '' : 'color: var(--color-danger);' }}">
                <i class="fa-solid fa-trash-can mr-1.5 ml-1.5"></i> {{ app()->getLocale() === 'ar' ? 'سلة المحذوفات' : 'Trash' }}
                <span class="badge badge-danger text-xs" style="margin-inline-start: 0.35rem;">{{ $trashedCount }}</span>
            </a>
        </div>
    </div>

    <!-- Filter Toolbar -->
    <form method="GET" action="{{ route('admin.orders.index') }}" class="shop-toolbar" style="margin-bottom: 1.5rem;">
        @if($isTrashed)
            <input type="hidden" name="status" value="trashed">
        @endif
        <div class="search-wrapper" style="max-width: 300px;">
            <svg class="search-icon" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input type="text" name="search" value="{{ request('search') }}" class="form-control search-input text-sm" placeholder="{{ app()->getLocale() == 'ar' ? 'البحث برقم الطلب أو العميل...' : 'Search order #, customer...' }}">
        </div>

        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
            @if(!$isTrashed)
                <select name="status" onchange="this.form.submit()" class="form-select text-sm" style="width: auto;">
                    <option value="">{{ app()->getLocale() == 'ar' ? 'جميع حالات الطلب' : 'All Fulfillment Statuses' }}</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? 'قيد الانتظار' : 'Pending' }}</option>
                    <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? 'قيد التجهيز' : 'Processing' }}</option>
                    <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? 'تم الشحن' : 'Shipped' }}</option>
                    <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? 'تم التوصيل' : 'Delivered' }}</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? 'ملغي' : 'Cancelled' }}</option>
                </select>
            @endif

            <select name="channel" onchange="this.form.submit()" class="form-select text-sm" style="width: auto;">
                <option value="">{{ app()->getLocale() == 'ar' ? 'جميع القنوات' : 'All Channels' }}</option>
                <option value="online" {{ request('channel') === 'online' ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? 'المتجر الإلكتروني' : 'Online Hub' }}</option>
                <option value="offline" {{ request('channel') === 'offline' ? 'selected' : '' }}>{{ app()->getLocale() == 'ar' ? 'معرض البوتيك' : 'Flagship POS' }}</option>
            </select>

            <button type="submit" class="btn btn-secondary btn-sm font-bold">
                <i class="fa-solid fa-filter mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'تصفية' : 'Filter' }}
            </button>
        </div>
    </form>

    <!-- Orders Table -->
    <div class="card shadow-sm border border-gray-100 dark:border-gray-800">
        <div class="table-responsive" style="border: none; border-radius: 0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('admin.orders.order_number') }}</th>
                        <th>{{ __('admin.orders.customer') }}</th>
                        <th>{{ __('admin.orders.channel') }}</th>
                        <th>{{ __('admin.orders.status') }}</th>
                        <th>{{ __('admin.orders.payment') }}</th>
                        <th>{{ __('admin.orders.amount') }}</th>
                        <th>{{ __('admin.orders.date') }}</th>
                        <th style="text-align: center;">{{ app()->getLocale() == 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        @php
                            $ordId = is_array($order) ? ($order['id'] ?? $order['order_number']) : ($order->id ?? $order->order_number);
                            $numericId = is_array($order) ? ($order['id'] ?? 1) : ($order->id ?? 1);
                            $ordNum = is_array($order) ? ($order['order_number'] ?? 'BZ-ORD') : ($order->order_number ?? 'BZ-ORD');
                            $custName = is_array($order) ? ($order['customer_name'] ?? 'Client') : ($order->customer_name ?? 'Client');
                            $custEmail = is_array($order) ? ($order['customer_email'] ?? '') : ($order->customer_email ?? '');
                            $channel = is_array($order) ? ($order['channel'] ?? 'online') : ($order->channel ?? 'online');
                            $status = is_array($order) ? ($order['status'] ?? 'pending') : ($order->status ?? 'pending');
                            $payStatus = is_array($order) ? ($order['payment_status'] ?? 'paid') : ($order->payment_status ?? 'paid');
                            $total = is_array($order) ? ($order['total'] ?? 0) : ($order->total ?? 0);
                            $ordDate = is_array($order) ? ($order['date'] ?? '') : ($order->date?->format('Y-m-d') ?? '');
                            $isItemTrashed = is_object($order) && method_exists($order, 'trashed') ? $order->trashed() : (!empty($order['deleted_at']));
                        @endphp
                        <tr>
                            <td>
                                <a href="{{ route('admin.orders.show', $ordId) }}" class="font-bold text-primary hover:underline">
                                    {{ $ordNum }}
                                </a>
                            </td>
                            <td>
                                <div class="font-bold text-sm">{{ $custName }}</div>
                                <div class="text-xs text-muted">{{ $custEmail }}</div>
                            </td>
                            <td>
                                <span class="badge {{ $channel === 'online' ? 'badge-accent' : 'badge-warning' }} text-xs">
                                    {{ $channel === 'online' ? (app()->getLocale() == 'ar' ? 'متجر إلكتروني' : 'Online') : (app()->getLocale() == 'ar' ? 'معرض POS' : 'POS') }}
                                </span>
                            </td>
                            <td>
                                @if($isItemTrashed)
                                    <span class="badge badge-danger text-xs font-bold">
                                        <i class="fa-solid fa-trash-can mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'ملغي ومحذوف' : 'Trashed' }}
                                    </span>
                                @else
                                    <x-status-badge :status="$status" />
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-success text-xs">{{ $payStatus }}</span>
                            </td>
                            <td class="font-bold">${{ number_format((float)$total, 2) }}</td>
                            <td class="text-xs text-muted">{{ $ordDate }}</td>
                            <td>
                                <div class="table-actions justify-center flex items-center gap-1.5">
                                    @if(!$isItemTrashed)
                                        <a href="{{ route('admin.orders.show', $ordId) }}" class="action-btn" title="{{ app()->getLocale() == 'ar' ? 'إدارة الطلب' : 'Manage Order' }}">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.invoices.show', $ordId) }}" class="action-btn" title="{{ app()->getLocale() == 'ar' ? 'عرض الفاتورة' : 'View Tax Invoice' }}">
                                            <i class="fa-solid fa-file-invoice-dollar"></i>
                                        </a>
                                        @if(is_numeric($numericId) && $numericId > 0)
                                            <button type="button" class="action-btn action-danger cursor-pointer" 
                                                    onclick="confirmDelete('{{ route('admin.orders.destroy', $numericId) }}', '{{ addslashes($ordNum) }}', false)" 
                                                    title="{{ __('app.actions.delete') }}">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        @endif
                                    @else
                                        <button type="button" class="action-btn text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 cursor-pointer" 
                                                onclick="confirmRestore('{{ route('admin.orders.restore', $numericId) }}', '{{ addslashes($ordNum) }}')" 
                                                title="{{ app()->getLocale() === 'ar' ? 'استعادة الطلب' : 'Restore Order' }}">
                                            <i class="fa-solid fa-rotate-left"></i>
                                        </button>
                                        <button type="button" class="action-btn action-danger cursor-pointer" 
                                                onclick="confirmDelete('{{ route('admin.orders.force-delete', $numericId) }}', '{{ addslashes($ordNum) }}', true)" 
                                                title="{{ app()->getLocale() === 'ar' ? 'حذف نهائي فوري' : 'Force Delete Permanently' }}">
                                            <i class="fa-solid fa-radiation"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-muted">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i class="fa-solid fa-receipt text-3xl text-gray-400"></i>
                                    <p class="text-sm font-semibold">{{ app()->getLocale() === 'ar' ? 'لا توجد طلبات مطابقة' : 'No orders found' }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-pagination :currentPage="$currentPage" :totalPages="$totalPages" :totalItems="count($orders)" />
    </div>
</x-layouts.admin>
