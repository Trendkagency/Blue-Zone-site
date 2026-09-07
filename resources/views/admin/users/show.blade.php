<x-layouts.admin 
    :pageTitle="$user['name'] ?? __('admin.users.title')" 
    :pageSubtitle="__('admin.users.subtitle')"
    :breadcrumbs="[__('admin.menu.users') => route('admin.users.index'), ($user['name'] ?? 'User') => route('admin.users.show', $user['id'] ?? 1)]"
>
    <x-slot name="actions">
        <div class="flex items-center gap-2 flex-wrap">
            @if(empty($user['deleted_at']))
                <a href="{{ route('admin.users.edit', $user['id'] ?? 1) }}" class="btn btn-primary font-bold shadow-sm">
                    <i class="fa-solid fa-user-pen mr-1.5 ml-1.5"></i> {{ __('admin.users.edit_credentials') }}
                </a>

                @if(auth()->id() !== ($user['id'] ?? 0))
                    <form method="POST" action="{{ route('admin.users.toggle-status', $user['id'] ?? 1) }}" class="inline">
                        @csrf
                        @if(($user['status'] ?? 'active') === 'active')
                            <button type="submit" class="btn btn-secondary font-bold text-amber-600 hover:text-amber-700 hover:bg-amber-50 dark:hover:bg-amber-900/30 border border-amber-300 dark:border-amber-700" title="{{ __('admin.users.suspend_title') }}">
                                <i class="fa-solid fa-user-lock mr-1.5 ml-1.5"></i> {{ __('admin.users.suspend') }}
                            </button>
                        @else
                            <button type="submit" class="btn btn-secondary font-bold text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 border border-emerald-300 dark:border-emerald-700" title="{{ __('admin.users.activate_title') }}">
                                <i class="fa-solid fa-user-check mr-1.5 ml-1.5"></i> {{ __('admin.users.activate') }}
                            </button>
                        @endif
                    </form>
                @endif
            @endif

            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary font-bold">
                <i class="fa-solid fa-arrow-left rtl:rotate-180 mr-1.5 ml-1.5"></i> {{ __('admin.users.all_users') }}
            </a>
        </div>
    </x-slot>

    <div class="customer-dossier-grid">
        <!-- Main Content Area: Profile Banner & Permissions Matrix -->
        <div style="display: flex; flex-direction: column; gap: 2rem;">
            
            <!-- Luxury User Identity Card -->
            <div class="card" style="padding: 2rem; position: relative; overflow: hidden;">
                <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 1.5rem; flex-wrap: wrap;">
                    <div style="display: flex; align-items: center; gap: 1.5rem;">
                        <div style="width: 72px; height: 72px; border-radius: 50%; background: linear-gradient(135deg, var(--color-primary) 0%, #062B49 100%); color: #FFF; display: flex; align-items: center; justify-content: center; font-size: 1.75rem; font-weight: 800; border: 3px solid rgba(255,255,255,0.2); box-shadow: 0 10px 25px -5px rgba(10, 79, 120, 0.4);">
                            {{ strtoupper(substr($user['name'] ?? 'U', 0, 2)) }}
                        </div>
                        <div>
                            <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                                <h2 style="font-size: 1.5rem; font-weight: 800; margin: 0; color: var(--color-primary);">
                                    {{ $user['name'] ?? 'User' }}
                                </h2>
                                <x-status-badge :status="$user['status'] ?? 'active'" />
                                <span class="badge badge-accent font-bold" style="font-size: 0.8125rem;">
                                    <i class="fa-solid fa-shield-halved mr-1 ml-1"></i> {{ $user['role'] ?? 'Staff' }}
                                </span>
                            </div>
                            <div class="text-sm text-secondary" style="margin-top: 0.35rem; display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                                <span><i class="fa-regular fa-envelope mr-1 ml-1"></i> {{ $user['email'] ?? '-' }}</span>
                                @if(!empty($user['phone']))
                                    <span><i class="fa-solid fa-phone mr-1 ml-1"></i> {{ $user['phone'] }}</span>
                                @endif
                            </div>
                            @if(!empty($user['bio']))
                                <div class="text-xs text-muted" style="margin-top: 0.75rem; line-height: 1.5; max-width: 650px;">
                                    {{ $user['bio'] }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Fast Specs Grid -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 1rem; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--color-border);">
                    <div style="background: var(--color-bg-subtle); padding: 0.875rem 1rem; border-radius: var(--radius-md); border: 1px solid var(--color-border);">
                        <div class="text-xs text-secondary font-medium">{{ __('admin.users.registered') }}</div>
                        <div class="font-bold text-sm text-primary" style="margin-top: 0.25rem;">{{ $user['created_at'] ?? '-' }}</div>
                    </div>
                    <div style="background: var(--color-bg-subtle); padding: 0.875rem 1rem; border-radius: var(--radius-md); border: 1px solid var(--color-border);">
                        <div class="text-xs text-secondary font-medium">{{ __('admin.users.last_updated') }}</div>
                        <div class="font-bold text-sm text-primary" style="margin-top: 0.25rem;">{{ $user['updated_at'] ?? 'Recently' }}</div>
                    </div>
                    <div style="background: var(--color-bg-subtle); padding: 0.875rem 1rem; border-radius: var(--radius-md); border: 1px solid var(--color-border);">
                        <div class="text-xs text-secondary font-medium">{{ __('admin.users.email_verification') }}</div>
                        <div class="font-bold text-sm" style="margin-top: 0.25rem;">
                            @if(!empty($user['email_verified_at']))
                                <span class="text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                                    <i class="fa-solid fa-circle-check"></i> {{ __('admin.users.verified') }}
                                </span>
                            @else
                                <span class="text-amber-600 dark:text-amber-400 flex items-center gap-1">
                                    <i class="fa-solid fa-clock"></i> {{ __('admin.users.pending') }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div style="background: var(--color-bg-subtle); padding: 0.875rem 1rem; border-radius: var(--radius-md); border: 1px solid var(--color-border);">
                        <div class="text-xs text-secondary font-medium">{{ __('admin.users.push_notifications') }}</div>
                        <div class="font-bold text-sm" style="margin-top: 0.25rem;">
                            @if(!empty($user['has_fcm']))
                                <span class="text-sky-600 dark:text-sky-400 flex items-center gap-1">
                                    <i class="fa-solid fa-mobile-screen-button"></i> {{ $user['fcm_device'] ?? __('admin.users.connected') }}
                                </span>
                            @else
                                <span class="text-gray-400 flex items-center gap-1">
                                    <i class="fa-solid fa-bell-slash"></i> {{ __('admin.users.none') }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Granular Permissions Matrix Inspector -->
            <div class="card" style="padding: 2rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid var(--color-border); flex-wrap: wrap; gap: 1rem;">
                    <div>
                        <h3 style="font-size: 1.25rem; font-weight: 800; margin: 0 0 0.25rem 0; color: var(--color-primary); display: flex; align-items: center; gap: 0.5rem;">
                            <i class="fa-solid fa-key text-primary"></i>
                            {{ __('admin.users.permissions_matrix') }}
                        </h3>
                        <p class="text-xs text-muted" style="margin: 0;">
                            {{ __('admin.users.permissions_desc') }}
                        </p>
                    </div>
                    @if(!empty($user['role_id']))
                        <a href="{{ route('admin.roles.edit', $user['role_id']) }}" class="btn btn-secondary btn-sm font-bold">
                            <i class="fa-solid fa-sliders mr-1 ml-1"></i> {{ __('admin.users.modify_role_matrix') }}
                        </a>
                    @endif
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.25rem;">
                    @php
                        $actionLabels = [
                            'view' => ['en' => 'View', 'ar' => 'عرض', 'icon' => 'fa-eye'],
                            'create' => ['en' => 'Create', 'ar' => 'إضافة', 'icon' => 'fa-plus'],
                            'edit' => ['en' => 'Edit', 'ar' => 'تعديل', 'icon' => 'fa-pen'],
                            'delete' => ['en' => 'Delete', 'ar' => 'حذف', 'icon' => 'fa-trash'],
                        ];
                    @endphp

                    @foreach($permissionsMatrix as $modKey => $modData)
                        <div style="background: var(--color-bg-subtle); padding: 1.25rem; border-radius: var(--radius-lg); border: 1px solid var(--color-border); display: flex; flex-direction: column; justify-content: space-between;">
                            <div>
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.85rem;">
                                    <h4 style="font-size: 0.9375rem; font-weight: 800; color: var(--color-primary); margin: 0;">
                                        {{ $modData['label'] }}
                                    </h4>
                                    <span class="text-[11px] font-mono text-muted">admin.{{ $modKey }}</span>
                                </div>

                                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.5rem;">
                                    @foreach($actionLabels as $actKey => $actMeta)
                                        @php
                                            $isAllowed = !empty($modData['actions'][$actKey]);
                                        @endphp
                                        <div style="display: flex; align-items: center; gap: 0.5rem; padding: 0.4rem 0.6rem; border-radius: var(--radius-md); font-size: 0.75rem; font-weight: 700; {{ $isAllowed ? 'background: rgba(16, 185, 129, 0.12); color: #059669; border: 1px solid rgba(16, 185, 129, 0.25);' : 'background: rgba(156, 163, 175, 0.08); color: #9CA3AF; border: 1px dashed rgba(156, 163, 175, 0.2);' }}">
                                            @if($isAllowed)
                                                <i class="fa-solid fa-check text-emerald-600"></i>
                                            @else
                                                <i class="fa-solid fa-xmark text-gray-400"></i>
                                            @endif
                                            <span>{{ app()->getLocale() === 'ar' ? $actMeta['ar'] : $actMeta['en'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

        <!-- Right Sidebar: Security & Role Information -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            
            <!-- Assigned Role Card -->
            <div class="card" style="padding: 1.5rem;">
                <h4 style="font-size: 1rem; font-weight: 800; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; color: var(--color-primary);">
                    <i class="fa-solid fa-id-badge text-primary"></i>
                    {{ __('admin.users.security_role') }}
                </h4>
                <div class="text-sm" style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <div>
                        <div class="text-xs text-muted font-medium">{{ __('admin.users.role_title') }}</div>
                        <div class="font-bold text-primary mt-0.5">{{ $user['role'] ?? 'Staff User' }}</div>
                    </div>
                    @if(!empty($user['role_description']))
                        <div>
                            <div class="text-xs text-muted font-medium">{{ __('admin.users.role_purpose') }}</div>
                            <div class="text-xs text-secondary mt-0.5 leading-relaxed">{{ $user['role_description'] }}</div>
                        </div>
                    @endif
                    <div class="pt-2 border-t border-gray-100 dark:border-gray-800">
                        <div class="text-xs text-muted font-medium">{{ __('admin.users.access_level') }}</div>
                        <div class="font-bold text-xs mt-0.5">
                            @if(in_array(strtolower($user['role']), ['super admin', 'super-admin']))
                                <span class="text-purple-600 dark:text-purple-400 flex items-center gap-1">
                                    <i class="fa-solid fa-crown"></i> {{ __('admin.users.root_authority') }}
                                </span>
                            @else
                                <span class="text-sky-600 dark:text-sky-400 flex items-center gap-1">
                                    <i class="fa-solid fa-user-gear"></i> {{ __('admin.users.granular_role') }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact & Action Options -->
            <div class="card" style="padding: 1.5rem;">
                <h4 style="font-size: 1rem; font-weight: 800; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; color: var(--color-primary);">
                    <i class="fa-solid fa-address-book text-primary"></i>
                    {{ __('admin.users.contact_actions') }}
                </h4>
                <div class="flex flex-col gap-2.5">
                    @if(!empty($user['email']))
                        <a href="mailto:{{ $user['email'] }}" class="btn btn-secondary btn-sm flex items-center justify-between">
                            <span><i class="fa-regular fa-envelope mr-1.5 ml-1.5"></i> {{ __('admin.users.send_email') }}</span>
                            <i class="fa-solid fa-arrow-up-right-from-square text-xs text-muted"></i>
                        </a>
                    @endif

                    @if(!empty($user['phone']))
                        <a href="tel:{{ $user['phone'] }}" class="btn btn-secondary btn-sm flex items-center justify-between">
                            <span><i class="fa-solid fa-phone mr-1.5 ml-1.5"></i> {{ __('admin.users.phone_call') }}</span>
                            <i class="fa-solid fa-phone-flip text-xs text-muted"></i>
                        </a>
                    @endif

                    <a href="{{ route('admin.users.edit', $user['id']) }}" class="btn btn-secondary btn-sm flex items-center justify-between">
                        <span><i class="fa-solid fa-pen-to-square mr-1.5 ml-1.5"></i> {{ __('admin.users.edit_credentials') }}</span>
                        <i class="fa-solid fa-chevron-right text-xs text-muted rtl:rotate-180"></i>
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-layouts.admin>
