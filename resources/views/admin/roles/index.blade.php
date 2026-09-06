<x-layouts.admin 
    :pageTitle="__('admin.roles.title')" 
    :pageSubtitle="__('admin.roles.subtitle')"
    :breadcrumbs="[__('admin.menu.access_control') => route('admin.roles.index'), __('admin.menu.roles') => route('admin.roles.index')]"
>
    <x-slot name="actions">
        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary font-bold shadow-sm">
            <i class="fa-solid fa-plus mr-1.5 ml-1.5"></i> {{ __('admin.roles.create_title') }}
        </a>
    </x-slot>

    <!-- Filter Tabs & Search -->
    <div class="mb-6 space-y-4">
        <div class="flex items-center gap-2 border-b border-gray-200 dark:border-gray-700 pb-3 flex-wrap">
            <a href="{{ route('admin.roles.index') }}" 
               class="px-4 py-2 rounded-lg text-sm font-bold transition-colors {{ !$isTrashed ? 'bg-[#0A4F78] text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                <i class="fa-solid fa-shield-halved mr-1.5 ml-1.5"></i>
                {{ app()->getLocale() === 'ar' ? 'الأدوار النشطة' : 'Active Roles' }}
                <span class="ml-1.5 mr-1.5 px-2 py-0.5 text-xs rounded-full {{ !$isTrashed ? 'bg-white/20 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">{{ $activeCount }}</span>
            </a>

            <a href="{{ route('admin.roles.index', ['status' => 'trashed']) }}" 
               class="px-4 py-2 rounded-lg text-sm font-bold transition-colors {{ $isTrashed ? 'bg-red-600 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-red-50 dark:hover:bg-red-900/20' }}">
                <i class="fa-solid fa-trash-can mr-1.5 ml-1.5"></i>
                {{ app()->getLocale() === 'ar' ? 'سلة المحذوفات' : 'Trash Archive' }}
                <span class="ml-1.5 mr-1.5 px-2 py-0.5 text-xs rounded-full {{ $isTrashed ? 'bg-white/20 text-white' : 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300' }}">{{ $trashedCount }}</span>
            </a>
        </div>
    </div>

    <div class="card shadow-sm border border-gray-100 dark:border-gray-800">
        <div class="table-responsive" style="border: none; border-radius: 0;">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('admin.roles.role_name') }}</th>
                        <th>{{ __('admin.roles.description') }}</th>
                        <th>{{ __('admin.roles.assigned_staff') }}</th>
                        <th>{{ __('admin.roles.permission_count') }}</th>
                        <th style="text-align: center;">{{ __('app.actions.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $r)
                        @php
                            $rId = $r['id'] ?? 1;
                            $rName = $r['name'] ?? 'Role';
                            $rDesc = $r['description'] ?? '';
                            $rUsersCount = $r['users_count'] ?? 0;
                            $rPerms = $r['permissions'] ?? [];
                            $isSystem = !empty($r['is_system']) || in_array(strtolower($rName), ['super admin', 'admin']);
                            $isItemTrashed = !empty($r['deleted_at']);
                        @endphp
                        <tr>
                            <td class="font-bold text-primary">
                                @if(!$isItemTrashed)
                                    <a href="{{ route('admin.roles.edit', $rId) }}" class="hover:underline">
                                        {{ $rName }}
                                    </a>
                                @else
                                    <span>{{ $rName }}</span>
                                @endif
                                @if($isSystem)
                                    <span class="badge badge-accent text-[10px] ml-1 mr-1">System</span>
                                @endif
                            </td>
                            <td class="text-sm text-secondary">{{ $rDesc }}</td>
                            <td class="font-bold">{{ __('admin.roles.users_count', ['count' => $rUsersCount]) }}</td>
                            <td>
                                <span class="badge badge-neutral text-xs font-mono">
                                    {{ in_array('*', (array)$rPerms) ? __('admin.roles.full_authority') : __('admin.roles.modules_count', ['count' => count((array)$rPerms)]) }}
                                </span>
                            </td>
                            <td>
                                <div class="table-actions justify-center flex items-center gap-1.5">
                                    @if(!$isItemTrashed)
                                        <a href="{{ route('admin.roles.edit', $rId) }}" class="action-btn" title="{{ __('app.actions.edit') }}">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        @if(!$isSystem)
                                            <button type="button" class="action-btn action-danger cursor-pointer" 
                                                    onclick="confirmDelete('{{ route('admin.roles.destroy', $rId) }}', '{{ addslashes($rName) }}', false)" 
                                                    title="{{ __('app.actions.delete') }}">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        @endif
                                    @else
                                        <button type="button" class="action-btn text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 cursor-pointer" 
                                                onclick="confirmRestore('{{ route('admin.roles.restore', $rId) }}', '{{ addslashes($rName) }}')" 
                                                title="{{ app()->getLocale() === 'ar' ? 'استعادة' : 'Restore' }}">
                                            <i class="fa-solid fa-rotate-left"></i>
                                        </button>
                                        <button type="button" class="action-btn action-danger cursor-pointer" 
                                                onclick="confirmDelete('{{ route('admin.roles.force-delete', $rId) }}', '{{ addslashes($rName) }}', true)" 
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
                                    <i class="fa-solid fa-shield-halved text-3xl text-gray-400"></i>
                                    <p class="text-sm font-semibold">{{ app()->getLocale() === 'ar' ? 'لا توجد أدوار وصلاحيات مطابقة' : 'No roles found' }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-pagination :currentPage="$currentPage" :totalPages="$totalPages" :totalItems="count($roles)" />
    </div>
</x-layouts.admin>
