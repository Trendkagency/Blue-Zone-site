<x-layouts.admin 
<<<<<<< HEAD
    :pageTitle="__('admin.menu.users')" 
    pageSubtitle="Manage administrative users, store specialists, inventory leads, and assigned roles."
    :breadcrumbs="['Users' => route('admin.users.index')]"
>
    <x-slot name="actions">
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">+ New Staff User</a>
    </x-slot>

    <div class="card">
=======
    :pageTitle="__('admin.users.title')" 
    :pageSubtitle="__('admin.users.subtitle')"
    :breadcrumbs="[__('admin.menu.users') => route('admin.users.index')]"
>
    <x-slot name="actions">
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary font-bold shadow-sm">
            <i class="fa-solid fa-user-plus mr-1.5 ml-1.5"></i> {{ __('admin.users.new_user_btn') }}
        </a>
    </x-slot>

    <!-- Filter Tabs & Search -->
    <div class="mb-6 space-y-4">
        <div class="flex items-center gap-2 border-b border-gray-200 dark:border-gray-700 pb-3 flex-wrap">
            <a href="{{ route('admin.users.index') }}" 
               class="px-4 py-2 rounded-lg text-sm font-bold transition-colors {{ !$isTrashed ? 'bg-[#0A4F78] text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                <i class="fa-solid fa-users mr-1.5 ml-1.5"></i>
                {{ app()->getLocale() === 'ar' ? 'المستخدمون النشطون' : 'Active Users' }}
                <span class="ml-1.5 mr-1.5 px-2 py-0.5 text-xs rounded-full {{ !$isTrashed ? 'bg-white/20 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">{{ $activeCount }}</span>
            </a>

            <a href="{{ route('admin.users.index', ['status' => 'trashed']) }}" 
               class="px-4 py-2 rounded-lg text-sm font-bold transition-colors {{ $isTrashed ? 'bg-red-600 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-red-50 dark:hover:bg-red-900/20' }}">
                <i class="fa-solid fa-trash-can mr-1.5 ml-1.5"></i>
                {{ app()->getLocale() === 'ar' ? 'سلة المحذوفات' : 'Trash Archive' }}
                <span class="ml-1.5 mr-1.5 px-2 py-0.5 text-xs rounded-full {{ $isTrashed ? 'bg-white/20 text-white' : 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300' }}">{{ $trashedCount }}</span>
            </a>
        </div>

        <form method="GET" action="{{ route('admin.users.index') }}" class="shop-toolbar flex items-center justify-between gap-4 flex-wrap">
            @if($isTrashed)
                <input type="hidden" name="status" value="trashed">
            @endif

            <div class="search-wrapper flex-1 min-w-[260px] max-w-md">
                <svg class="search-icon" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control search-input text-sm" placeholder="{{ app()->getLocale() === 'ar' ? 'بحث بالاسم أو البريد...' : 'Search name or email...' }}">
            </div>

            <button type="submit" class="btn btn-secondary btn-sm font-bold">
                <i class="fa-solid fa-filter mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'بحث' : 'Search' }}
            </button>
        </form>
    </div>

    <div class="card shadow-sm border border-gray-100 dark:border-gray-800">
>>>>>>> origin/main
        <div class="table-responsive" style="border: none; border-radius: 0;">
            <table class="table">
                <thead>
                    <tr>
<<<<<<< HEAD
                        <th>User</th>
                        <th>Mobile</th>
                        <th>Assigned Role</th>
                        <th>Status</th>
                        <th>Last Login</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $u)
                        <tr>
                            <td>
                                <div class="font-bold text-sm text-primary">{{ $u['name'] }}</div>
                                <div class="text-xs text-muted">{{ $u['email'] }}</div>
                            </td>
                            <td class="text-xs">{{ $u['mobile'] }}</td>
                            <td>
                                <span class="badge badge-accent text-xs">{{ $u['role'] }}</span>
                            </td>
                            <td><x-status-badge :status="$u['status']" /></td>
                            <td class="text-xs text-muted">{{ $u['last_login_at'] }}</td>
                            <td class="text-xs text-muted">{{ $u['created_at'] }}</td>
                            <td>
                                <div class="table-actions">
                                    <a href="{{ route('admin.users.edit', $u['id']) }}" class="action-btn" title="Edit">✏️</a>
                                    <button type="button" class="action-btn action-danger" title="Suspend">🔒</button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
=======
                        <th>{{ __('admin.users.name') }}</th>
                        <th>{{ __('admin.users.role') }}</th>
                        <th>{{ __('admin.users.status') }}</th>
                        <th>{{ __('admin.users.last_login') }}</th>
                        <th style="text-align: center;">{{ __('app.actions.title') ?? 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                        @php
                            $uId = $u['id'] ?? 1;
                            $uName = $u['name'] ?? 'Admin';
                            $uEmail = $u['email'] ?? '';
                            $uRole = $u['role'] ?? 'Admin';
                            $uStatus = $u['status'] ?? 'active';
                            $isItemTrashed = !empty($u['deleted_at']);
                        @endphp
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div style="width: 36px; height: 36px; border-radius: 50%; background: var(--color-primary); color: #FFF; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.875rem;">
                                        {{ strtoupper(substr($uName, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-sm text-primary">
                                            @if(!$isItemTrashed)
                                                <a href="{{ route('admin.users.edit', $uId) }}" class="hover:underline">
                                                    {{ $uName }}
                                                </a>
                                            @else
                                                <span>{{ $uName }}</span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-muted">{{ $uEmail }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-accent text-xs">{{ $uRole }}</span>
                            </td>
                            <td>
                                @if($isItemTrashed)
                                    <span class="badge badge-danger text-xs font-bold">
                                        <i class="fa-solid fa-trash-can mr-1 ml-1"></i> {{ app()->getLocale() === 'ar' ? 'محذوف' : 'Trashed' }}
                                    </span>
                                @else
                                    <x-status-badge :status="$uStatus" />
                                @endif
                            </td>
                            <td class="text-xs text-muted">{{ $u['last_login'] ?? 'Recently' }}</td>
                            <td>
                                <div class="table-actions justify-center flex items-center gap-1.5">
                                    @if(!$isItemTrashed)
                                        <a href="{{ route('admin.users.edit', $uId) }}" class="action-btn" title="{{ __('app.actions.edit') }}">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        @if(auth()->id() !== $uId)
                                            <button type="button" class="action-btn action-danger cursor-pointer" 
                                                    onclick="confirmDelete('{{ route('admin.users.destroy', $uId) }}', '{{ addslashes($uName) }}', false)" 
                                                    title="{{ __('app.actions.delete') }}">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        @endif
                                    @else
                                        <button type="button" class="action-btn text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 cursor-pointer" 
                                                onclick="confirmRestore('{{ route('admin.users.restore', $uId) }}', '{{ addslashes($uName) }}')" 
                                                title="{{ app()->getLocale() === 'ar' ? 'استعادة' : 'Restore' }}">
                                            <i class="fa-solid fa-rotate-left"></i>
                                        </button>
                                        <button type="button" class="action-btn action-danger cursor-pointer" 
                                                onclick="confirmDelete('{{ route('admin.users.force-delete', $uId) }}', '{{ addslashes($uName) }}', true)" 
                                                title="{{ app()->getLocale() === 'ar' ? 'حذف نهائي فوري' : 'Force Delete Permanently' }}">
                                            <i class="fa-solid fa-radiation"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-8 text-muted">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i class="fa-solid fa-users text-3xl text-gray-400"></i>
                                    <p class="text-sm font-semibold">{{ app()->getLocale() === 'ar' ? 'لا يوجد مستخدمون مطابقون' : 'No users found' }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-pagination :currentPage="$currentPage" :totalPages="$totalPages" :totalItems="count($users)" />
>>>>>>> origin/main
    </div>
</x-layouts.admin>
