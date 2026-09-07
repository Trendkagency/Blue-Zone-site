<x-layouts.admin 
    :pageTitle="__('admin.customers.title')" 
    :pageSubtitle="__('admin.customers.subtitle')"
    :breadcrumbs="[__('admin.menu.customers') => route('admin.customers.index')]"
>
    <x-slot name="actions">
        <a href="{{ route('admin.customers.create') }}" class="btn btn-primary font-bold shadow-sm">
            <i class="fa-solid fa-user-plus mr-1.5 ml-1.5"></i> {{ __('admin.customers.new_customer_btn') }}
        </a>
    </x-slot>

    <!-- Filter Tabs & Search -->
    <div class="mb-6 space-y-4">
        <div class="flex items-center gap-2 border-b border-gray-200 dark:border-gray-700 pb-3 flex-wrap">
            <a href="{{ route('admin.customers.index') }}" 
               class="px-4 py-2 rounded-lg text-sm font-bold transition-colors {{ !$isTrashed && empty(request('status')) ? 'bg-[#0A4F78] text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                <i class="fa-solid fa-users mr-1.5 ml-1.5"></i>
                {{ __('admin.customers.active_clients') }}
                <span class="ml-1.5 mr-1.5 px-2 py-0.5 text-xs rounded-full {{ !$isTrashed && empty(request('status')) ? 'bg-white/20 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">{{ $activeCount }}</span>
            </a>

            <a href="{{ route('admin.customers.index', ['status' => 'inactive']) }}" 
               class="px-4 py-2 rounded-lg text-sm font-bold transition-colors {{ request('status') === 'inactive' ? 'bg-amber-600 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-amber-50 dark:hover:bg-amber-900/20' }}">
                <i class="fa-solid fa-user-slash mr-1.5 ml-1.5"></i>
                {{ __('admin.customers.inactive_clients') }}
            </a>

            <a href="{{ route('admin.customers.index', ['status' => 'trashed']) }}" 
               class="px-4 py-2 rounded-lg text-sm font-bold transition-colors {{ $isTrashed ? 'bg-red-600 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-red-50 dark:hover:bg-red-900/20' }}">
                <i class="fa-solid fa-trash-can mr-1.5 ml-1.5"></i>
                {{ __('admin.customers.trash_archive') }}
                <span class="ml-1.5 mr-1.5 px-2 py-0.5 text-xs rounded-full {{ $isTrashed ? 'bg-white/20 text-white' : 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300' }}">{{ $trashedCount }}</span>
            </a>
        </div>

        <form method="GET" action="{{ route('admin.customers.index') }}" class="shop-toolbar flex items-center justify-between gap-4 flex-wrap">
            @if($isTrashed)
                <input type="hidden" name="status" value="trashed">
            @elseif(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif

            <div class="flex items-center gap-3 flex-1 min-w-[260px] max-w-2xl flex-wrap">
                <div class="search-wrapper flex-1 min-w-[200px]">
                    <svg class="search-icon" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control search-input text-sm" placeholder="{{ __('admin.customers.search_placeholder') }}">
                </div>

                <div class="w-44">
                    <select name="tier" class="form-control text-sm" onchange="this.form.submit()">
                        <option value="">{{ __('admin.customers.all_tiers') }}</option>
                        <option value="VIP" {{ request('tier') === 'VIP' ? 'selected' : '' }}>VIP</option>
                        <option value="Gold" {{ request('tier') === 'Gold' ? 'selected' : '' }}>Gold</option>
                        <option value="Silver" {{ request('tier') === 'Silver' ? 'selected' : '' }}>Silver</option>
                        <option value="Bronze" {{ request('tier') === 'Bronze' ? 'selected' : '' }}>Bronze</option>
                        <option value="Member" {{ request('tier') === 'Member' ? 'selected' : '' }}>Member</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-secondary btn-sm font-bold">
                <i class="fa-solid fa-filter mr-1 ml-1"></i> {{ __('admin.customers.filter') }}
            </button>
        </form>
    </div>

    <!-- Customers Table -->
    <div class="card shadow-sm border border-gray-100 dark:border-gray-800">
        <div class="table-responsive" style="border: none; border-radius: 0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('admin.orders.customer') }}</th>
                        <th>{{ __('admin.customers.contact') }}</th>
                        <th>{{ __('admin.customers.location') }}</th>
                        <th>{{ __('admin.customers.member_tier') }}</th>
                        <th>{{ __('admin.customers.orders_count') }}</th>
                        <th>{{ __('admin.customers.total_spent') }}</th>
                        <th>{{ __('admin.customers.status') }}</th>
                        <th style="text-align: center;">{{ __('admin.customers.actions') }}</th>
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
                                    <a href="{{ route('admin.customers.show', $cId) }}" class="hover:underline flex items-center gap-1.5">
                                        {{ $cName }}
                                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-muted opacity-60"></i>
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
                                <span class="badge badge-accent text-xs font-bold">{{ $cTier }}</span>
                            </td>
                            <td class="font-bold">{{ $cCount }} {{ __('admin.customers.orders') }}</td>
                            <td class="font-bold text-success">@currency((float)$cSpent)</td>
                            <td>
                                <x-status-badge :status="$cStatus" />
                            </td>
                            <td style="text-align: center;">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('admin.customers.show', $cId) }}" class="action-btn" title="{{ __('app.actions.view') }}">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    @if(!$isItemTrashed)
                                        <a href="{{ route('admin.customers.edit', $cId) }}" class="action-btn" title="{{ __('app.actions.edit') }}">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <button type="button" 
                                                class="action-btn action-btn-danger" 
                                                title="{{ __('app.actions.delete') }}"
                                                onclick="confirmDelete('{{ route('admin.customers.destroy', $cId) }}', '{{ addslashes($cName) }}')">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    @else
                                        <button type="button" 
                                                class="action-btn text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50" 
                                                title="{{ __('admin.users.activate') }}"
                                                onclick="confirmRestore('{{ route('admin.customers.restore', $cId) }}', '{{ addslashes($cName) }}')">
                                            <i class="fa-solid fa-rotate-left"></i>
                                        </button>
                                        <button type="button" 
                                                class="action-btn action-btn-danger" 
                                                title="{{ __('app.actions.delete') }}"
                                                onclick="confirmDelete('{{ route('admin.customers.force-delete', $cId) }}', '{{ addslashes($cName) }}', true)">
                                            <i class="fa-solid fa-radiation"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 3rem 1rem;" class="text-secondary">
                                <i class="fa-solid fa-users-slash" style="font-size: 2rem; margin-bottom: 0.75rem; opacity: 0.4; display: block;"></i>
                                {{ __('admin.customers.no_addresses') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($customers instanceof \Illuminate\Pagination\LengthAwarePaginator && $customers->hasPages())
            <div class="card-footer border-t border-gray-100 dark:border-gray-800 p-4">
                {{ $customers->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>
