<x-layouts.admin 
    :pageTitle="__('admin.menu.customers')" 
<<<<<<< HEAD
    pageSubtitle="Customer relationship profiles, member tiers, purchase history, and delivery addresses."
    :breadcrumbs="['Customers' => route('admin.customers.index')]"
>
    <x-slot name="actions">
        <a href="{{ route('admin.customers.create') }}" class="btn btn-primary">
            + New Customer Profile
        </a>
    </x-slot>

    <!-- Customers Table -->
    <div class="card">
=======
    :pageSubtitle="__('admin.customers.subtitle')"
    :breadcrumbs="[__('admin.menu.customers') => route('admin.customers.index')]"
>
    <x-slot name="actions">
        <a href="{{ route('admin.customers.create') }}" class="btn btn-primary font-bold shadow-sm">
            <i class="fa-solid fa-user-plus mr-1.5 ml-1.5"></i> {{ __('admin.customers.create_title') }}
        </a>
    </x-slot>

    <!-- Filter Tabs & Search -->
    <div class="mb-6 space-y-4">
        <div class="flex items-center gap-2 border-b border-gray-200 dark:border-gray-700 pb-3 flex-wrap">
            <a href="{{ route('admin.customers.index') }}" 
               class="px-4 py-2 rounded-lg text-sm font-bold transition-colors {{ !$isTrashed ? 'bg-[#0A4F78] text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                <i class="fa-solid fa-user-group mr-1.5 ml-1.5"></i>
                {{ app()->getLocale() === 'ar' ? 'العملاء المسجلون' : 'Active Clients' }}
                <span class="ml-1.5 mr-1.5 px-2 py-0.5 text-xs rounded-full {{ !$isTrashed ? 'bg-white/20 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">{{ $activeCount }}</span>
            </a>

            <a href="{{ route('admin.customers.index', ['status' => 'trashed']) }}" 
               class="px-4 py-2 rounded-lg text-sm font-bold transition-colors {{ $isTrashed ? 'bg-red-600 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-red-50 dark:hover:bg-red-900/20' }}">
                <i class="fa-solid fa-trash-can mr-1.5 ml-1.5"></i>
                {{ app()->getLocale() === 'ar' ? 'سلة المحذوفات' : 'Trash Archive' }}
                <span class="ml-1.5 mr-1.5 px-2 py-0.5 text-xs rounded-full {{ $isTrashed ? 'bg-white/20 text-white' : 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300' }}">{{ $trashedCount }}</span>
            </a>
        </div>

        <form method="GET" action="{{ route('admin.customers.index') }}" class="shop-toolbar flex items-center justify-between gap-4 flex-wrap">
            @if($isTrashed)
                <input type="hidden" name="status" value="trashed">
            @endif

            <div class="search-wrapper flex-1 min-w-[260px] max-w-md">
                <svg class="search-icon" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control search-input text-sm" placeholder="{{ app()->getLocale() === 'ar' ? 'بحث بالاسم، البريد أو الهاتف...' : 'Search name, email or phone...' }}">
            </div>

            <button type="submit" class="btn btn-secondary btn-sm font-bold">
                <i class="fa-solid fa-filter mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'بحث' : 'Search' }}
            </button>
        </form>
    </div>

    <!-- Customers Table -->
    <div class="card shadow-sm border border-gray-100 dark:border-gray-800">
>>>>>>> origin/main
        <div class="table-responsive" style="border: none; border-radius: 0;">
            <table class="table">
                <thead>
                    <tr>
<<<<<<< HEAD
                        <th>Customer</th>
                        <th>Contact</th>
                        <th>Location</th>
                        <th>Member Tier</th>
                        <th>Orders Count</th>
                        <th>Total Spent</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customers as $c)
                        <tr>
                            <td>
                                <div class="font-bold text-sm text-primary">
                                    <a href="{{ route('admin.customers.show', $c['id']) }}">
                                        {{ $c['name'] }}
                                    </a>
                                </div>
                                <div class="text-xs text-muted">Member since {{ $c['registered_at'] }}</div>
                            </td>
                            <td class="text-sm">
                                <div>{{ $c['email'] }}</div>
                                <div class="text-xs text-muted">{{ $c['phone'] }}</div>
                            </td>
                            <td>{{ $c['city'] }}, {{ $c['country'] }}</td>
                            <td>
                                <span class="badge badge-accent text-xs">{{ $c['tier'] }}</span>
                            </td>
                            <td class="font-bold">{{ $c['orders_count'] }} orders</td>
                            <td class="font-bold text-success">${{ number_format($c['total_spent'], 2) }}</td>
                            <td>
                                <x-status-badge :status="$c['status']" />
                            </td>
                            <td>
                                <a href="{{ route('admin.customers.show', $c['id']) }}" class="action-btn" title="View Customer Dossier">
                                    👁️
                                </a>
                            </td>
                        </tr>
                    @endforeach
=======
                        <th>{{ __('admin.orders.customer') }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'معلومات التواصل' : 'Contact' }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'الموقع / المدينة' : 'Location' }}</th>
                        <th>{{ __('admin.customers.member_tier') }}</th>
                        <th>{{ __('admin.customers.orders_count') }}</th>
                        <th>{{ __('admin.customers.total_spent') }}</th>
                        <th>{{ app()->getLocale() == 'ar' ? 'الحالة' : 'Status' }}</th>
                        <th style="text-align: center;">{{ app()->getLocale() == 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $c)
                        @php
                            $cId = $c['id'] ?? 1;
                            $cName = $c['name'] ?? 'Client';
                            $cEmail = $c['email'] ?? '';
                            $cPhone = $c['phone'] ?? '';
                            $cCity = $c['city'] ?? 'Riyadh';
                            $cCountry = $c['country'] ?? 'Saudi Arabia';
                            $cTier = $c['tier'] ?? 'Member';
                            $cCount = $c['orders_count'] ?? 0;
                            $cSpent = $c['total_spent'] ?? 0;
                            $cStatus = $c['status'] ?? 'active';
                            $cReg = $c['registered_at'] ?? '';
                            $isItemTrashed = !empty($c['deleted_at']);
                        @endphp
                        <tr>
                            <td>
                                <div class="font-bold text-sm text-primary">
                                    <a href="{{ route('admin.customers.show', $cId) }}" class="hover:underline">
                                        {{ $cName }}
                                    </a>
                                </div>
                                <div class="text-xs text-muted">{{ __('admin.customers.member_since') }} {{ $cReg }}</div>
                            </td>
                            <td class="text-sm">
                                <div>{{ $cEmail }}</div>
                                <div class="text-xs text-muted">{{ $cPhone }}</div>
                            </td>
                            <td>{{ $cCity }}, {{ $cCountry }}</td>
                            <td>
                                <span class="badge badge-accent text-xs">{{ $cTier }}</span>
                            </td>
                            <td class="font-bold">{{ $cCount }} {{ app()->getLocale() == 'ar' ? 'طلبات' : 'orders' }}</td>
                            <td class="font-bold text-success">${{ number_format((float)$cSpent, 2) }}</td>
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
                                    <a href="{{ route('admin.customers.show', $cId) }}" class="action-btn" title="{{ app()->getLocale() == 'ar' ? 'عرض ملف العميل' : 'View Customer Dossier' }}">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    @if(!$isItemTrashed)
                                        <a href="{{ route('admin.customers.edit', $cId) }}" class="action-btn" title="{{ __('app.actions.edit') }}">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <button type="button" class="action-btn action-danger cursor-pointer" 
                                                onclick="confirmDelete('{{ route('admin.customers.destroy', $cId) }}', '{{ addslashes($cName) }}', false)" 
                                                title="{{ __('app.actions.delete') }}">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    @else
                                        <button type="button" class="action-btn text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 cursor-pointer" 
                                                onclick="confirmRestore('{{ route('admin.customers.restore', $cId) }}', '{{ addslashes($cName) }}')" 
                                                title="{{ app()->getLocale() === 'ar' ? 'استعادة' : 'Restore' }}">
                                            <i class="fa-solid fa-rotate-left"></i>
                                        </button>
                                        <button type="button" class="action-btn action-danger cursor-pointer" 
                                                onclick="confirmDelete('{{ route('admin.customers.force-delete', $cId) }}', '{{ addslashes($cName) }}', true)" 
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
                                    <i class="fa-solid fa-user-group text-3xl text-gray-400"></i>
                                    <p class="text-sm font-semibold">{{ app()->getLocale() === 'ar' ? 'لا يوجد عملاء مطابقون للبحث' : 'No clients found' }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
>>>>>>> origin/main
                </tbody>
            </table>
        </div>

        <x-pagination :currentPage="$currentPage" :totalPages="$totalPages" :totalItems="count($customers)" />
    </div>
</x-layouts.admin>
