@props([
    'title' => null,
    'pageTitle' => null,
    'pageSubtitle' => null,
    'breadcrumbs' => [],
])

<x-layouts.app :title="($title ?? $pageTitle ?? 'Admin') . ' — ' . __('admin.portal_title')">
    <div class="admin-layout">
        <!-- Mobile Sidebar Backdrop -->
        <div id="adminSidebarBackdrop" class="admin-sidebar-backdrop" onclick="toggleAdminSidebar()"></div>

        <!-- Admin Sidebar -->
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="sidebar-header">
                <a href="{{ route('admin.dashboard') }}" style="display: flex; align-items: center; gap: 0.75rem; text-decoration: none;">
                    <img src="{{ asset('assets/logo/logo-dark.webp') }}" alt="{{ __('app.brand_name') }}" style="height: 28px;" onerror="this.onerror=null; this.src='{{ asset('assets/logo/logo-dark.png') }}';">
                    <span class="sidebar-brand-title">BZ-OS</span>
                </a>
                <button type="button" class="btn btn-ghost btn-sm lg:hidden cursor-pointer" onclick="toggleAdminSidebar()" aria-label="Close sidebar" style="color: #94A3B8; padding: 0.25rem 0.5rem;">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <nav class="sidebar-menu">
                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-line sidebar-link-icon"></i>
                    <span>{{ __('admin.menu.dashboard') }}</span>
                </a>

                @php
                    $u = auth()->user();
                @endphp

                <!-- Catalog -->
                @if($u && ($u->hasPermission('products.view') || $u->hasPermission('products')))
                    <div class="menu-category">{{ __('admin.menu.catalog') }}</div>
                    <a href="{{ route('admin.products.index') }}" class="sidebar-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-boxes-stacked sidebar-link-icon"></i>
                        <span>{{ __('admin.menu.products') }}</span>
                    </a>
                    <a href="{{ route('admin.categories.index') }}" class="sidebar-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-layer-group sidebar-link-icon"></i>
                        <span>{{ __('admin.menu.categories') }}</span>
                    </a>
                @endif

                <!-- Inventory -->
                @if($u && ($u->hasPermission('inventory.view') || $u->hasPermission('inventory')))
                    <div class="menu-category">{{ __('admin.menu.inventory') }}</div>
                    <a href="{{ route('admin.inventory.index') }}" class="sidebar-link {{ request()->routeIs('admin.inventory.index') || request()->routeIs('admin.inventory.show') ? 'active' : '' }}">
                        <i class="fa-solid fa-warehouse sidebar-link-icon"></i>
                        <span>{{ __('admin.menu.stock_levels') }}</span>
                    </a>
                    <a href="{{ route('admin.inventory.transfers') }}" class="sidebar-link {{ request()->routeIs('admin.inventory.transfers') ? 'active' : '' }}">
                        <i class="fa-solid fa-arrow-right-arrow-left sidebar-link-icon"></i>
                        <span>{{ __('admin.menu.stock_transfers') }}</span>
                    </a>
                    <a href="{{ route('admin.inventory.history') }}" class="sidebar-link {{ request()->routeIs('admin.inventory.history') ? 'active' : '' }}">
                        <i class="fa-solid fa-clock-rotate-left sidebar-link-icon"></i>
                        <span>{{ __('admin.menu.stock_history') }}</span>
                    </a>
                @endif

                <!-- Sales -->
                @if($u && ($u->hasPermission('orders.view') || $u->hasPermission('offline_sales.view') || $u->hasPermission('invoices.view') || $u->hasPermission('orders') || $u->hasPermission('offline_sales') || $u->hasPermission('invoices')))
                    <div class="menu-category">{{ __('admin.menu.sales') }}</div>
                    @if($u->hasPermission('orders.view') || $u->hasPermission('orders'))
                        <a href="{{ route('admin.orders.index') }}" class="sidebar-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-bag-shopping sidebar-link-icon"></i>
                            <span>{{ __('admin.menu.online_orders') }}</span>
                        </a>
                    @endif
                    @if($u->hasPermission('offline_sales.view') || $u->hasPermission('offline_sales'))
                        <a href="{{ route('admin.offline-sales.index') }}" class="sidebar-link {{ request()->routeIs('admin.offline-sales.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-cash-register sidebar-link-icon"></i>
                            <span>{{ __('admin.menu.offline_sales') }}</span>
                        </a>
                    @endif
                    @if($u->hasPermission('invoices.view') || $u->hasPermission('invoices'))
                        <a href="{{ route('admin.invoices.index') }}" class="sidebar-link {{ request()->routeIs('admin.invoices.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-file-invoice-dollar sidebar-link-icon"></i>
                            <span>{{ __('admin.menu.invoices') }}</span>
                        </a>
                    @endif
                @endif

                <!-- Customers & Analytics -->
                @if($u && ($u->hasPermission('customers.view') || $u->hasPermission('reports.view') || $u->hasPermission('customers') || $u->hasPermission('reports')))
                    <div class="menu-category">{{ __('admin.menu.customers') }} & {{ __('admin.menu.reports') }}</div>
                    @if($u->hasPermission('customers.view') || $u->hasPermission('customers'))
                        <a href="{{ route('admin.customers.index') }}" class="sidebar-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-users-gear sidebar-link-icon"></i>
                            <span>{{ __('admin.menu.customers') }}</span>
                        </a>
                    @endif
                    @if($u->hasPermission('reports.view') || $u->hasPermission('reports'))
                        <a href="{{ route('admin.reports.index') }}" class="sidebar-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-chart-pie sidebar-link-icon"></i>
                            <span>{{ __('admin.menu.reports') }}</span>
                        </a>
                    @endif
                @endif

                <!-- Content & Access -->
                @if($u && ($u->hasPermission('content.view') || $u->hasPermission('users.view') || $u->hasPermission('roles.view') || $u->hasPermission('settings.view') || $u->hasPermission('content') || $u->hasPermission('users') || $u->hasPermission('roles') || $u->hasPermission('settings')))
                    <div class="menu-category">{{ __('admin.menu.content') }} & {{ __('admin.menu.access_control') }}</div>
                    @if($u->hasPermission('content.view') || $u->hasPermission('content'))
                        <a href="{{ route('admin.content.index') }}" class="sidebar-link {{ request()->routeIs('admin.content.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-newspaper sidebar-link-icon"></i>
                            <span>{{ __('admin.menu.content') }}</span>
                        </a>
                    @endif
                    @if($u->hasPermission('users.view') || $u->hasPermission('users'))
                        <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-user-shield sidebar-link-icon"></i>
                            <span>{{ __('admin.menu.users') }}</span>
                        </a>
                    @endif
                    @if($u->hasPermission('roles.view') || $u->hasPermission('roles'))
                        <a href="{{ route('admin.roles.index') }}" class="sidebar-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-id-badge sidebar-link-icon"></i>
                            <span>{{ __('admin.menu.roles') }}</span>
                        </a>
                    @endif
                    <a href="{{ route('admin.profile.index') }}" class="sidebar-link {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-user-gear sidebar-link-icon"></i>
                        <span>{{ __('admin.profile.title') }}</span>
                    </a>
                    @if($u->hasPermission('settings.view') || $u->hasPermission('settings'))
                        <a href="{{ route('admin.settings.index') }}" class="sidebar-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-sliders sidebar-link-icon"></i>
                            <span>{{ __('admin.menu.settings') }}</span>
                        </a>
                        <a href="{{ route('admin.settings.index') }}#tab-typography" class="sidebar-link" title="{{ app()->getLocale() == 'ar' ? 'المعاينة الحية والتحكم في خطوط النظام' : 'Live Interactive Typography Control' }}">
                            <i class="fa-solid fa-font sidebar-link-icon text-sky-400"></i>
                            <span>{{ app()->getLocale() == 'ar' ? 'الخطوط والطباعة (Live)' : 'Typography & Fonts (Live)' }}</span>
                        </a>
                    @endif
                @endif
            </nav>
        </aside>

        <!-- Admin Content Shell (Section Main) -->
        <main class="admin-main" id="adminMain" role="main">
            <!-- Header -->
            <header class="admin-header">
                <div class="header-left">
                    <button type="button" class="btn btn-ghost btn-icon cursor-pointer admin-mobile-toggle lg:hidden" onclick="toggleAdminSidebar()" title="Toggle Sidebar" aria-label="Toggle Sidebar">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>

                    <div class="breadcrumbs hidden sm:flex">
                        <a href="{{ route('admin.dashboard') }}" class="breadcrumb-link">{{ __('admin.menu.dashboard') }}</a>
                        @foreach($breadcrumbs as $label => $url)
                            <span class="breadcrumb-separator">›</span>
                            @if($loop->last)
                                <span class="breadcrumb-current">{{ $label }}</span>
                            @else
                                <a href="{{ $url }}" class="breadcrumb-link">{{ $label }}</a>
                            @endif
                        @endforeach
                    </div>
                </div>

                <div class="header-right">
                    <!-- Language Switcher -->
                    @if(app()->getLocale() === 'ar')
                        <a href="{{ route('locale.switch', 'en') }}" class="btn btn-secondary btn-sm font-bold">
                            EN
                        </a>
                    @else
                        <a href="{{ route('locale.switch', 'ar') }}" class="btn btn-secondary btn-sm font-bold">
                            العربية
                        </a>
                    @endif

                    <!-- Theme Toggle -->
                    <button type="button" onclick="if(window.BLUEZONE_THEME){BLUEZONE_THEME.toggle();}else{toggleTheme();}" data-theme-toggle class="btn btn-ghost btn-icon cursor-pointer" title="{{ __('app.theme') }}">
                        <i class="fa-solid fa-circle-half-stroke"></i>
                    </button>

                    <!-- Notifications Bell & Dropdown -->
                    @php
                        $authUser = auth()->user();
                        $adminNotifications = $authUser ? $authUser->notifications()->latest()->limit(8)->get() : collect();
                        $adminUnreadCount = $authUser ? $authUser->unreadNotifications()->count() : 0;
                    @endphp
                    <div class="admin-notification-wrapper" id="adminNotificationWrapper">
                        <button type="button" 
                                id="adminNotificationBtn" 
                                class="admin-notification-btn" 
                                onclick="toggleAdminNotifications(event)" 
                                aria-label="{{ __('admin.notifications.title') ?? 'Notifications' }}"
                                title="{{ __('admin.notifications.title') ?? 'Notifications' }}">
                            <i class="fa-regular fa-bell text-lg"></i>
                            <span id="adminNotificationBadge" 
                                  class="admin-notification-badge {{ $adminUnreadCount > 0 ? '' : 'hidden' }}">
                                {{ $adminUnreadCount > 99 ? '99+' : $adminUnreadCount }}
                            </span>
                        </button>

                        <!-- Notification Dropdown -->
                        <div id="adminNotificationDropdown" class="admin-notification-dropdown">
                            <div class="notification-dropdown-header">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-sm text-slate-800 dark:text-white">
                                        {{ __('admin.notifications.title') ?? 'Notifications' }}
                                    </span>
                                    <span id="adminNotificationHeaderCount" class="text-xs bg-sky-100 text-sky-700 dark:bg-sky-900/60 dark:text-sky-300 px-2 py-0.5 rounded-full font-bold {{ $adminUnreadCount > 0 ? '' : 'hidden' }}">
                                        {{ $adminUnreadCount }} {{ __('admin.notifications.unread') ?? 'new' }}
                                    </span>
                                </div>
                                @if($adminUnreadCount > 0)
                                    <button type="button" 
                                            onclick="markAllNotificationsAsRead(event)" 
                                            id="adminMarkAllReadBtn"
                                            class="text-xs text-sky-600 dark:text-sky-400 font-semibold hover:underline bg-transparent border-none p-0 cursor-pointer">
                                        {{ __('admin.notifications.mark_all_read') ?? 'Mark all as read' }}
                                    </button>
                                @endif
                            </div>

                            <div class="notification-dropdown-body" id="adminNotificationList">
                                @forelse($adminNotifications as $notif)
                                    @php
                                        $isUnread = is_null($notif->read_at);
                                        $nData = $notif->data ?? [];
                                        $nIcon = $nData['icon'] ?? 'fa-solid fa-bell text-sky-500';
                                        $rawUrl = $nData['action_url'] ?? null;
                                        if ($rawUrl && (str_starts_with($rawUrl, 'http://') || str_starts_with($rawUrl, 'https://'))) {
                                            $parsed = parse_url($rawUrl);
                                            $rawUrl = ($parsed['path'] ?? '') . (isset($parsed['query']) ? '?' . $parsed['query'] : '') . (isset($parsed['fragment']) ? '#' . $parsed['fragment'] : '');
                                        }
                                        $nUrl = $rawUrl;
                                    @endphp
                                    <a href="{{ $nUrl ?? 'javascript:void(0)' }}" 
                                       onclick="handleNotificationClick('{{ $notif->id }}', '{{ $nUrl }}', event)"
                                       class="notification-item-row {{ $isUnread ? 'is-unread' : '' }}" 
                                       data-notif-id="{{ $notif->id }}">
                                        <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center flex-shrink-0 mt-0.5">
                                            <i class="{{ $nIcon }} text-sm"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-xs font-bold text-slate-800 dark:text-white truncate">
                                                {{ $nData['title'] ?? 'Notification' }}
                                            </div>
                                            <div class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 mt-0.5 leading-snug">
                                                {{ $nData['message'] ?? '' }}
                                            </div>
                                            <div class="text-[10px] text-slate-400 dark:text-slate-500 mt-1 flex items-center gap-1">
                                                <i class="fa-regular fa-clock text-[9px]"></i>
                                                <span>{{ $notif->created_at ? $notif->created_at->diffForHumans() : '' }}</span>
                                            </div>
                                        </div>
                                        @if($isUnread)
                                            <span class="notification-unread-dot" title="Unread"></span>
                                        @endif
                                    </a>
                                @empty
                                    <div class="p-8 text-center">
                                        <i class="fa-solid fa-bell-slash text-slate-300 dark:text-slate-600 text-2xl mb-2 block"></i>
                                        <p class="text-xs text-muted m-0">{{ __('admin.notifications.no_notifications') ?? 'No notifications' }}</p>
                                    </div>
                                @endforelse
                            </div>

                            <div class="notification-dropdown-footer" style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 1rem;">
                                <a href="{{ route('admin.notifications.index') }}" class="text-xs font-bold text-sky-600 dark:text-sky-400 hover:underline flex items-center gap-1">
                                    <span>{{ __('admin.notifications.view_all') ?? 'View all notifications' }}</span>
                                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                </a>
                                <button type="button" onclick="openFcmPermissionModal()" class="text-xs text-slate-500 hover:text-sky-600 dark:text-slate-400 dark:hover:text-sky-300 font-semibold bg-transparent border-none p-0 cursor-pointer flex items-center gap-1.5" title="{{ __('admin.notifications.browser_setup_title') }}">
                                    <i class="fa-solid fa-gear text-[11px]"></i>
                                    <span>{{ __('admin.notifications.browser_setup') }}</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- View Storefront Link -->
                    <a href="{{ route('customer.home') }}" class="btn btn-outline btn-sm hidden md:inline-flex" target="_blank">
                        {{ __('app.nav.home') }} <i class="fa-solid fa-arrow-up-right-from-square mr-1 ml-1"></i>
                    </a>

                    <!-- User Profile Pill & Dropdown -->
                    @php
                        $userName = $authUser ? $authUser->name : 'Administrator';
                        $userEmail = $authUser ? $authUser->email : 'admin@bluezone.com';
                        
                        $userRole = 'Super Administrator';
                        if ($authUser) {
                            if ($authUser->relationLoaded('role') && $authUser->role) {
                                $userRole = is_object($authUser->role) ? ($authUser->role->name ?? 'Admin') : (string) $authUser->role;
                            } elseif (method_exists($authUser, 'role') && $authUser->role) {
                                $userRole = is_object($authUser->role) ? ($authUser->role->name ?? 'Admin') : (string) $authUser->role;
                            } elseif (!empty($authUser->role_id)) {
                                $roleObj = \App\Models\Role::find($authUser->role_id);
                                $userRole = $roleObj ? $roleObj->name : 'Admin';
                            } elseif (!empty($authUser->role) && is_string($authUser->role)) {
                                $userRole = ucfirst($authUser->role);
                            }
                        }

                        $initials = $authUser ? strtoupper(substr(trim($authUser->name), 0, 2)) : 'AD';
                        $avatarUrl = $authUser ? $authUser->avatar_url : null;
                    @endphp

                    <div class="admin-profile-wrapper" id="adminProfileWrapper">
                        <button type="button" 
                                id="adminProfileDropdownToggle" 
                                class="admin-profile-btn" 
                                onclick="toggleAdminProfileDropdown(event)"
                                aria-expanded="false" 
                                aria-haspopup="true"
                                style="display: inline-flex; align-items: center; gap: 0.625rem; vertical-align: middle; padding: 0.35rem 0.625rem; border-radius: 9999px; cursor: pointer; text-decoration: none; border: 1px solid transparent; box-sizing: border-box;">
                            <div style="display: flex; align-items: center; justify-content: center; width: 34px; height: 34px; flex-shrink: 0;">
                                @if($avatarUrl)
                                    <img src="{{ $avatarUrl }}" alt="{{ $userName }}" style="width: 34px; height: 34px; border-radius: 50%; object-fit: cover; border: 2px solid var(--color-primary-light);" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div style="display: none; width: 34px; height: 34px; border-radius: 50%; background: linear-gradient(135deg, #0A4F78, #2A8FC2); color: #FFF; align-items: center; justify-content: center; font-weight: 700; font-size: 0.8125rem; border: 2px solid rgba(255,255,255,0.2); flex-shrink: 0;">
                                        {{ $initials }}
                                    </div>
                                @else
                                    <div style="width: 34px; height: 34px; border-radius: 50%; background: linear-gradient(135deg, #0A4F78, #2A8FC2); color: #FFF; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.8125rem; border: 2px solid rgba(255,255,255,0.2); flex-shrink: 0;">
                                        {{ $initials }}
                                    </div>
                                @endif
                            </div>
                            <div class="admin-profile-meta hidden sm:flex flex-col text-start" style="display: flex; flex-direction: column; justify-content: center; text-align: start; line-height: normal;">
                                <span class="text-sm font-bold text-slate-800 dark:text-white" style="line-height: 1.2;">
                                    <bdi>{{ $userName }}</bdi>
                                </span>
                                <span style="font-size: 0.7rem; color: var(--color-text-muted); line-height: 1.1;">
                                    {{ $userRole }}
                                </span>
                            </div>
                            <i class="fa-solid fa-chevron-down" id="adminProfileChevron" style="font-size: 0.7rem; opacity: 0.6; transition: transform 0.2s ease;"></i>
                        </button>

                        <!-- Dropdown Menu -->
                        <div id="adminProfileDropdown" class="admin-profile-dropdown">
                            <div style="padding: 1rem 1.25rem; border-bottom: 1px solid var(--color-border); background: var(--color-bg-subtle, rgba(0,0,0,0.02));">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div style="position: relative; width: 44px; height: 44px; flex-shrink: 0;">
                                        @if($avatarUrl)
                                            <img src="{{ $avatarUrl }}" alt="{{ $userName }}" style="width: 44px; height: 44px; border-radius: 50%; object-fit: cover;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <div style="display: none; width: 44px; height: 44px; border-radius: 50%; background: linear-gradient(135deg, #0A4F78, #2A8FC2); color: #FFF; align-items: center; justify-content: center; font-weight: 800; font-size: 1rem;">
                                                {{ $initials }}
                                            </div>
                                        @else
                                            <div style="width: 44px; height: 44px; border-radius: 50%; background: linear-gradient(135deg, #0A4F78, #2A8FC2); color: #FFF; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1rem;">
                                                {{ $initials }}
                                            </div>
                                        @endif
                                    </div>
                                    <div style="flex: 1; min-width: 0;">
                                        <div style="font-weight: 700; font-size: 0.9375rem; color: var(--color-text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><bdi>{{ $userName }}</bdi></div>
                                        <div style="font-size: 0.75rem; color: var(--color-text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $userEmail }}</div>
                                    </div>
                                </div>
                                <div style="margin-top: 0.6rem;">
                                    <span style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.2rem 0.6rem; border-radius: 9999px; background: rgba(10, 79, 120, 0.1); color: var(--color-primary); font-size: 0.7rem; font-weight: 700;">
                                        <i class="fa-solid fa-shield-halved"></i> {{ $userRole }}
                                    </span>
                                </div>
                            </div>

                            <div style="padding: 0.5rem;">
                                <a href="{{ route('admin.profile.index') }}" class="admin-dropdown-item">
                                    <i class="fa-solid fa-user-pen" style="width: 18px; color: var(--color-primary);"></i>
                                    <span>{{ __('admin.profile.title') }}</span>
                                </a>
                                <a href="{{ route('admin.profile.index') }}#security" class="admin-dropdown-item">
                                    <i class="fa-solid fa-shield-keyhole" style="width: 18px; color: #0284c7;"></i>
                                    <span>{{ __('admin.profile.password') }}</span>
                                </a>
                                @if($u->hasPermission('settings.view') || $u->hasPermission('settings') || $u->isSuperAdmin())
                                    <a href="{{ route('admin.settings.index') }}" class="admin-dropdown-item">
                                        <i class="fa-solid fa-sliders" style="width: 18px; color: #10b981;"></i>
                                        <span>{{ __('admin.menu.settings') }}</span>
                                    </a>
                                @endif
                                <a href="{{ route('customer.home') }}" target="_blank" class="admin-dropdown-item">
                                    <i class="fa-solid fa-store" style="width: 18px; color: #8b5cf6;"></i>
                                    <span style="flex: 1;">{{ __('app.nav.home') }}</span>
                                    <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 0.7rem; opacity: 0.5;"></i>
                                </a>
                            </div>

                            <div style="padding: 0.5rem; border-top: 1px solid var(--color-border);">
                                <form method="POST" action="{{ route('admin.logout') }}" id="adminLogoutForm" style="margin: 0;">
                                    @csrf
                                    <button type="submit" class="admin-dropdown-item admin-dropdown-logout-btn">
                                        <i class="fa-solid fa-right-from-bracket" style="width: 18px; color: #ef4444;"></i>
                                        <span style="color: #ef4444; font-weight: 700;">{{ __('app.nav.logout') }}</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Body -->
            <div class="admin-content-body">
                @if($pageTitle)
                    <div class="page-header">
                        <div>
                            <h1 class="page-title">{{ $pageTitle }}</h1>
                            @if($pageSubtitle)
                                <p class="page-subtitle">{{ $pageSubtitle }}</p>
                            @endif
                        </div>

                        @isset($actions)
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                {{ $actions }}
                            </div>
                        @endisset
                    </div>
                @endif

                {{ $slot }}
            </div>
        </main>
    </div>

    <!-- Global Destructive & Force Delete Confirmation Modal -->
    <div id="globalDeleteModal" class="modal-backdrop" onclick="if(event.target === this) closeModal('globalDeleteModal')">
        <div class="modal-dialog">
            <form id="globalDeleteForm" method="POST" action="">
                @csrf
                <input type="hidden" name="_method" value="DELETE">

                <div class="modal-header">
                    <h4 class="modal-title font-bold text-base" style="display: flex; align-items: center; gap: 0.625rem; margin: 0; color: #DC2626;">
                        <i id="globalDeleteIcon" class="fa-solid fa-triangle-exclamation"></i>
                        <span id="globalDeleteTitle">{{ __('app.actions.delete') }}</span>
                    </h4>
                    <button type="button" class="btn btn-ghost btn-sm cursor-pointer" onclick="closeModal('globalDeleteModal')" aria-label="Close" style="padding: 0.35rem 0.6rem;">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <p id="globalDeleteMessage" style="color: var(--color-text-secondary); margin: 0; font-size: 0.9375rem; line-height: 1.6;">
                        {{ __('admin.confirm_action') }}
                    </p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary text-sm cursor-pointer" onclick="closeModal('globalDeleteModal')">
                        {{ __('app.actions.cancel') }}
                    </button>
                    <button type="submit" id="globalDeleteBtn" class="btn btn-danger text-sm font-bold cursor-pointer">
                        <i class="fa-solid fa-trash-can mr-1.5 ml-1.5"></i>
                        <span id="globalDeleteBtnText">{{ __('app.actions.delete') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Global Restore Form (Hidden) -->
    <form id="globalRestoreForm" method="POST" action="" style="display: none;">
        @csrf
    </form>

    <script>
        function openModal(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.add('is-active', 'active');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.remove('is-active', 'active');
                document.body.style.overflow = '';
            }
        }

        function confirmDelete(actionUrl, itemName = '', isForceDelete = false) {
            const modal = document.getElementById('globalDeleteModal');
            const form = document.getElementById('globalDeleteForm');
            const title = document.getElementById('globalDeleteTitle');
            const message = document.getElementById('globalDeleteMessage');
            const btnText = document.getElementById('globalDeleteBtnText');
            const btn = document.getElementById('globalDeleteBtn');
            const icon = document.getElementById('globalDeleteIcon');

            if (form && modal) {
                form.action = actionUrl;
                const isAr = document.documentElement.lang === 'ar' || document.documentElement.dir === 'rtl';

                if (isForceDelete) {
                    title.textContent = isAr ? 'تأكيد الحذف النهائي الشامل' : 'Confirm Permanent Deletion';
                    message.textContent = isAr
                        ? `تحذير بالغ الأهمية: هل أنت متأكد من رغبتك في حذف [${itemName || 'العنصر'}] نهائياً من قاعدة البيانات؟ سيتم إزالة جميع السجلات المرتبطة به ولن تتمكن من استعادته لاحقاً!`
                        : `Extreme Warning: Are you sure you want to permanently erase [${itemName || 'this record'}] from the database? This action is irreversible!`;
                    btnText.textContent = isAr ? 'حذف نهائي فوري' : 'Permanently Delete';
                    btn.className = 'btn btn-danger text-sm font-bold cursor-pointer';
                    icon.className = 'fa-solid fa-radiation';
                } else {
                    title.textContent = isAr ? 'تأكيد النقل لسلة المحذوفات' : 'Confirm Move to Trash';
                    message.textContent = isAr
                        ? `هل أنت متأكد من نقل [${itemName || 'العنصر'}] إلى سلة المحذوفات؟ يمكنك مراجعته أو استعادته لاحقاً.`
                        : `Are you sure you want to move [${itemName || 'this item'}] to trash? You can restore it anytime later.`;
                    btnText.textContent = isAr ? 'نقل للمحذوفات' : 'Move to Trash';
                    btn.className = 'btn btn-danger text-sm font-bold cursor-pointer';
                    icon.className = 'fa-solid fa-trash-can';
                }

                openModal('globalDeleteModal');
            }
        }

        function confirmRestore(restoreUrl, itemName = '') {
            const isAr = document.documentElement.lang === 'ar' || document.documentElement.dir === 'rtl';
            const msg = isAr
                ? `هل ترغب في استعادة [${itemName || 'العنصر'}] وإعادته إلى السجلات النشطة؟`
                : `Do you want to restore [${itemName || 'this record'}] back to active status?`;
            
            if (confirm(msg)) {
                const form = document.getElementById('globalRestoreForm');
                if (form) {
                    form.action = restoreUrl;
                    form.submit();
                }
            }
        }

        /* Sidebar Toggle Logic */
        function toggleAdminSidebar() {
            const sidebar = document.getElementById('adminSidebar');
            const backdrop = document.getElementById('adminSidebarBackdrop');
            if (sidebar) {
                const isOpen = sidebar.classList.toggle('is-open');
                if (backdrop) {
                    if (isOpen) {
                        backdrop.classList.add('active');
                        document.body.style.overflow = 'hidden';
                    } else {
                        backdrop.classList.remove('active');
                        document.body.style.overflow = '';
                    }
                }
            }
        }

        /* Profile Dropdown Logic */
        function toggleAdminProfileDropdown(event) {
            if (event) {
                event.stopPropagation();
            }
            const dropdown = document.getElementById('adminProfileDropdown');
            const toggleBtn = document.getElementById('adminProfileDropdownToggle');
            const chevron = document.getElementById('adminProfileChevron');

            // Close notification dropdown if open
            const notifDropdown = document.getElementById('adminNotificationDropdown');
            if (notifDropdown) notifDropdown.classList.remove('show');

            if (dropdown) {
                const isShown = dropdown.classList.toggle('show');
                if (toggleBtn) {
                    toggleBtn.setAttribute('aria-expanded', isShown ? 'true' : 'false');
                }
                if (chevron) {
                    chevron.style.transform = isShown ? 'rotate(180deg)' : '';
                }
            }
        }

        /* Notification Dropdown Logic */
        function toggleAdminNotifications(event) {
            if (event) {
                event.stopPropagation();
            }
            const notifDropdown = document.getElementById('adminNotificationDropdown');
            const profileDropdown = document.getElementById('adminProfileDropdown');
            const profileChevron = document.getElementById('adminProfileChevron');
            const profileBtn = document.getElementById('adminProfileDropdownToggle');

            if (profileDropdown && profileDropdown.classList.contains('show')) {
                profileDropdown.classList.remove('show');
                if (profileBtn) profileBtn.setAttribute('aria-expanded', 'false');
                if (profileChevron) profileChevron.style.transform = '';
            }

            if (notifDropdown) {
                notifDropdown.classList.toggle('show');
            }
        }

        function handleNotificationClick(id, actionUrl, event) {
            let targetUrl = actionUrl;
            if (targetUrl && (targetUrl.startsWith('http://') || targetUrl.startsWith('https://'))) {
                try {
                    const u = new URL(targetUrl);
                    targetUrl = u.pathname + u.search + u.hash;
                } catch(e) {}
            }

            fetch('/admin/notifications/' + id + '/read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            }).then(res => res.json()).then(data => {
                if (data.success) {
                    updateNotificationBadge(data.unread_count);
                    const row = document.querySelector(`[data-notif-id="${id}"]`);
                    if (row) {
                        row.classList.remove('is-unread');
                        const dot = row.querySelector('.notification-unread-dot');
                        if (dot) dot.remove();
                    }
                }
            }).catch(e => console.error(e));

            if (targetUrl && targetUrl !== 'javascript:void(0)') {
                window.location.href = targetUrl;
            }
        }

        function markAllNotificationsAsRead(event) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }

            fetch('/admin/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            }).then(res => res.json()).then(data => {
                if (data.success) {
                    updateNotificationBadge(0);
                    document.querySelectorAll('.notification-item-row.is-unread').forEach(el => {
                        el.classList.remove('is-unread');
                        const dot = el.querySelector('.notification-unread-dot');
                        if (dot) dot.remove();
                    });
                    const markAllBtn = document.getElementById('adminMarkAllReadBtn');
                    if (markAllBtn) markAllBtn.remove();
                    const headerCount = document.getElementById('adminNotificationHeaderCount');
                    if (headerCount) headerCount.classList.add('hidden');
                }
            }).catch(e => console.error(e));
        }

        function updateNotificationBadge(count) {
            const badge = document.getElementById('adminNotificationBadge');
            const headerCount = document.getElementById('adminNotificationHeaderCount');

            if (badge) {
                if (count > 0) {
                    badge.textContent = count > 99 ? '99+' : count;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            }

            if (headerCount) {
                if (count > 0) {
                    headerCount.textContent = count + ' {{ __("admin.notifications.unread") ?? "new" }}';
                    headerCount.classList.remove('hidden');
                } else {
                    headerCount.classList.add('hidden');
                }
            }
        }

        /* Outside click & Escape listener */
        document.addEventListener('click', function(event) {
            const wrapper = document.getElementById('adminProfileWrapper');
            const dropdown = document.getElementById('adminProfileDropdown');
            const chevron = document.getElementById('adminProfileChevron');
            const toggleBtn = document.getElementById('adminProfileDropdownToggle');

            if (dropdown && dropdown.classList.contains('show')) {
                if (wrapper && !wrapper.contains(event.target)) {
                    dropdown.classList.remove('show');
                    if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'false');
                    if (chevron) chevron.style.transform = '';
                }
            }

            const notifWrapper = document.getElementById('adminNotificationWrapper');
            const notifDropdown = document.getElementById('adminNotificationDropdown');
            if (notifDropdown && notifDropdown.classList.contains('show')) {
                if (notifWrapper && !notifWrapper.contains(event.target)) {
                    notifDropdown.classList.remove('show');
                }
            }
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const dropdown = document.getElementById('adminProfileDropdown');
                const chevron = document.getElementById('adminProfileChevron');
                const toggleBtn = document.getElementById('adminProfileDropdownToggle');
                if (dropdown && dropdown.classList.contains('show')) {
                    dropdown.classList.remove('show');
                    if (toggleBtn) toggleBtn.setAttribute('aria-expanded', 'false');
                    if (chevron) chevron.style.transform = '';
                }

                const notifDropdown = document.getElementById('adminNotificationDropdown');
                if (notifDropdown && notifDropdown.classList.contains('show')) {
                    notifDropdown.classList.remove('show');
                }

                const sidebar = document.getElementById('adminSidebar');
                const backdrop = document.getElementById('adminSidebarBackdrop');
                if (sidebar && sidebar.classList.contains('is-open')) {
                    sidebar.classList.remove('is-open');
                    if (backdrop) backdrop.classList.remove('active');
                    document.body.style.overflow = '';
                }
            }
        });
    </script>

    <!-- Toast Notification Container for Real-time FCM Alerts -->
    <div id="adminToastContainer" style="position: fixed; top: 1.25rem; right: 1.25rem; z-index: 999999; display: flex; flex-direction: column; gap: 0.75rem; pointer-events: none; max-width: 400px; width: calc(100% - 2.5rem);" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"></div>

    <!-- Firebase App & Messaging SDKs (v9 Compat) -->
    <script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.23.0/firebase-messaging-compat.js"></script>

    <script>
        /* =========================================================================
           Real-time Notification & FCM Engine (Singleton Service Architecture)
           ========================================================================= */
        function playNotificationChime() {
            try {
                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                if (!AudioCtx) return;
                const ctx = new AudioCtx();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.type = 'sine';
                const now = ctx.currentTime;
                osc.frequency.setValueAtTime(587.33, now); // D5
                osc.frequency.setValueAtTime(880, now + 0.08); // A5
                gain.gain.setValueAtTime(0.18, now);
                gain.gain.exponentialRampToValueAtTime(0.001, now + 0.35);
                osc.start(now);
                osc.stop(now + 0.35);
            } catch (e) {
                // AudioContext autoplay policies handled silently
            }
        }

        function showAdminToast(title, message, iconClass, actionUrl) {
            playNotificationChime();
            const container = document.getElementById('adminToastContainer');
            if (!container) return;

            const toast = document.createElement('div');
            toast.style.cssText = 'pointer-events: auto; background: var(--color-surface, #FFFFFF); color: var(--color-text, #1E293B); border: 1px solid var(--color-border, #E2E8F0); border-radius: 1rem; padding: 1rem; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.15), 0 8px 10px -6px rgba(0,0,0,0.1); display: flex; align-items: flex-start; gap: 0.875rem; transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1); position: relative; overflow: hidden;';

            // Top accent border
            const accent = document.createElement('div');
            accent.style.cssText = 'position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, #0A4F78, #0284c7);';
            toast.appendChild(accent);

            // Icon box
            const iconBox = document.createElement('div');
            iconBox.style.cssText = 'width: 38px; height: 38px; border-radius: 0.75rem; background: rgba(10, 79, 120, 0.1); display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;';
            iconBox.innerHTML = `<i class="${iconClass || 'fa-solid fa-bell text-sky-500'} text-base"></i>`;
            toast.appendChild(iconBox);

            // Content
            const content = document.createElement('div');
            content.style.cssText = 'flex: 1; min-width: 0; text-align: start;';
            let html = `<div style="font-size: 0.875rem; font-weight: 700; line-height: 1.25; margin-bottom: 0.25rem;">${title}</div>
                        <div style="font-size: 0.775rem; opacity: 0.8; line-height: 1.4;">${message}</div>`;
            if (actionUrl && actionUrl !== 'javascript:void(0)') {
                html += `<a href="${actionUrl}" style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.75rem; font-weight: 700; color: #0284c7; margin-top: 0.5rem; text-decoration: none;"><span>{{ __("admin.notifications.view_details") ?? "View details" }}</span> <i class="fa-solid fa-arrow-right" style="font-size: 0.65rem;"></i></a>`;
            }
            content.innerHTML = html;
            toast.appendChild(content);

            // Dismiss button
            const closeBtn = document.createElement('button');
            closeBtn.type = 'button';
            closeBtn.style.cssText = 'background: transparent; border: none; cursor: pointer; opacity: 0.5; font-size: 0.875rem; padding: 0.25rem; line-height: 1;';
            closeBtn.innerHTML = '<i class="fa-solid fa-xmark"></i>';
            closeBtn.onclick = () => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(-10px)';
                setTimeout(() => toast.remove(), 300);
            };
            toast.appendChild(closeBtn);

            container.appendChild(toast);

            setTimeout(() => {
                if (toast.parentElement) {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateY(-10px)';
                    setTimeout(() => toast.remove(), 300);
                }
            }, 6500);
        }

        function prependNotificationToDropdown(title, message, iconClass, actionUrl, notifId) {
            const list = document.getElementById('adminNotificationList');
            if (!list) return;

            // Remove empty state message if present
            const emptyState = list.querySelector('.fa-bell-slash')?.parentElement;
            if (emptyState) emptyState.remove();

            const row = document.createElement('a');
            row.href = actionUrl || 'javascript:void(0)';
            row.className = 'notification-item-row is-unread';
            if (notifId) {
                row.setAttribute('data-notif-id', notifId);
                row.onclick = (e) => handleNotificationClick(notifId, actionUrl, e);
            }
            row.innerHTML = `
                <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <i class="${iconClass || 'fa-solid fa-bell text-sky-500'} text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-xs font-bold text-slate-800 dark:text-white truncate">${title}</div>
                    <div class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 mt-0.5 leading-snug">${message}</div>
                    <div class="text-[10px] text-slate-400 dark:text-slate-500 mt-1 flex items-center gap-1">
                        <i class="fa-regular fa-clock text-[9px]"></i>
                        <span>{{ __("app.time.just_now") ?? "Just now" }}</span>
                    </div>
                </div>
                <span class="notification-unread-dot" title="Unread"></span>
            `;

            list.insertBefore(row, list.firstChild);

            // Increment badge counter
            const badge = document.getElementById('adminNotificationBadge');
            const curCount = badge && !badge.classList.contains('hidden') ? (parseInt(badge.textContent.trim()) || 0) : 0;
            updateNotificationBadge(curCount + 1);
        }

        window.triggerFcmTestPush = function() {
            const btn = document.getElementById('btnTestFcmPush');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>Sending...</span>';
            }

            fetch('/admin/notifications/test-push', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showAdminToast(
                        'تجربة إشعارات FCM | FCM Realtime Test',
                        'تم إرسال إشعار تجريبي بنجاح عبر نظام FCM Realtime.',
                        'fa-solid fa-satellite-dish text-sky-500',
                        '/admin/inventory'
                    );
                    prependNotificationToDropdown(
                        'تجربة إشعارات FCM',
                        'تم إرسال إشعار تجريبي بنجاح عبر نظام FCM Realtime.',
                        'fa-solid fa-satellite-dish text-sky-500',
                        '/admin/inventory'
                    );
                }
            })
            .catch(err => {
                console.error('Test push error:', err);
            })
            .finally(() => {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa-solid fa-satellite-dish text-sky-500"></i> <span>{{ __("admin.notifications.test_push") ?? "Test FCM Push" }}</span>';
                }
            });
        };
    </script>

    <!-- =========================================================================
         BlueZone Branded FCM Notification Permission & Browser Settings Modal
         ========================================================================= -->
    <style>
            @keyframes bzModalPop {
                0% { transform: scale(0.93) translateY(10px); opacity: 0; }
                100% { transform: scale(1) translateY(0); opacity: 1; }
            }
            @keyframes bzBellRing {
                0%, 100% { transform: rotate(0deg); }
                10%, 30% { transform: rotate(-14deg); }
                20%, 40% { transform: rotate(14deg); }
                50% { transform: rotate(0deg); }
            }
            .bz-fcm-modal-backdrop {
                position: fixed;
                inset: 0;
                background: rgba(10, 25, 45, 0.78);
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
                z-index: 999999;
                display: none;
                align-items: center;
                justify-content: center;
                padding: 1.25rem;
                box-sizing: border-box;
            }
            .bz-fcm-modal-dialog {
                background: var(--color-surface, #FFFFFF);
                color: var(--color-text, #1E293B);
                border: 1px solid rgba(10, 79, 120, 0.2);
                border-radius: 1.5rem;
                max-width: 520px;
                width: 100%;
                box-shadow: 0 25px 50px -12px rgba(10, 79, 120, 0.35), 0 0 0 1px rgba(255,255,255,0.1);
                overflow: hidden;
                position: relative;
                animation: bzModalPop 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            }
            .dark .bz-fcm-modal-dialog {
                background: #0F172A;
                color: #F8FAFC;
                border-color: rgba(255, 255, 255, 0.1);
            }
        </style>

        <div id="fcmPermissionModal" class="bz-fcm-modal-backdrop" onclick="if(event.target === this) closeFcmPermissionModal(3)">
            <div class="bz-fcm-modal-dialog" role="dialog" aria-modal="true">
                
                <!-- Top Brand Gradient Accent -->
                <div style="height: 6px; background: linear-gradient(90deg, #0A4F78 0%, #0284C7 50%, #B8D98A 100%); width: 100%;"></div>

                <!-- Close Cross Button -->
                <button type="button" onclick="closeFcmPermissionModal(3)" style="position: absolute; top: 1.15rem; {{ app()->getLocale() == 'ar' ? 'left: 1.15rem;' : 'right: 1.15rem;' }} background: rgba(0,0,0,0.06); border: none; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--color-text-muted); transition: all 0.2s;" onmouseover="this.style.background='rgba(0,0,0,0.12)'" onmouseout="this.style.background='rgba(0,0,0,0.06)'" aria-label="Close">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>

                <!-- STATE 1: Branded Permission Prompt & Value Proposition -->
                <div id="fcmModalStatePrompt" style="padding: 2.25rem 2rem 2rem 2rem; text-align: center;">
                    <!-- Floating Animated Bell -->
                    <div style="width: 76px; height: 76px; margin: 0 auto 1.25rem auto; border-radius: 1.25rem; background: linear-gradient(135deg, rgba(10, 79, 120, 0.12), rgba(2, 132, 199, 0.2)); border: 2px solid rgba(2, 132, 199, 0.3); display: flex; align-items: center; justify-content: center; position: relative; box-shadow: 0 12px 24px -6px rgba(2, 132, 199, 0.25);">
                        <i class="fa-solid fa-bell text-3xl" style="color: #0A4F78; animation: bzBellRing 3s infinite ease-in-out;"></i>
                        <span style="position: absolute; top: 14px; right: 16px; width: 12px; height: 12px; background: #10B981; border: 2px solid #FFFFFF; border-radius: 50%;"></span>
                    </div>

                    <h3 style="font-size: 1.35rem; font-weight: 900; color: var(--color-text); margin: 0 0 0.5rem 0;">
                        {{ app()->getLocale() == 'ar' ? 'تفعيل إشعارات بلو زون الفورية' : 'Enable Real-Time BlueZone Alerts' }}
                    </h3>
                    <p style="font-size: 0.875rem; color: var(--color-text-muted); margin: 0 0 1.5rem 0; line-height: 1.5;">
                        {{ app()->getLocale() == 'ar' 
                            ? 'ابقَ على اتصال فوري ومستمر بجميع أحداث المتجر والمخزون وحركات المنتجات دون الحاجة لتحديث الصفحة.' 
                            : 'Stay instantly updated on incoming orders, stock thresholds, and inventory transfers without refreshing.' }}
                    </p>

                    <!-- Value Propositions -->
                    <div style="display: flex; flex-direction: column; gap: 0.75rem; text-align: start; background: var(--color-bg-subtle, rgba(0,0,0,0.03)); padding: 1rem 1.25rem; border-radius: 1rem; border: 1px solid var(--color-border); margin-bottom: 1.75rem;">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 34px; height: 34px; border-radius: 0.6rem; background: rgba(2, 132, 199, 0.12); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fa-solid fa-boxes-stacked text-sky-600 text-sm"></i>
                            </div>
                            <div style="font-size: 0.8125rem;">
                                <strong style="color: var(--color-text); display: block;">{{ app()->getLocale() == 'ar' ? 'تنبيهات المخزون والحدود الحرجة' : 'Live Inventory Warnings' }}</strong>
                                <span style="color: var(--color-text-muted); font-size: 0.75rem;">{{ app()->getLocale() == 'ar' ? 'إشعار مباشر عند هبوط المخزون تحت الحد الأدنى أو نفاد أي منتج' : 'Instant alert when stock drops below threshold or depletes' }}</span>
                            </div>
                        </div>

                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 34px; height: 34px; border-radius: 0.6rem; background: rgba(16, 185, 129, 0.12); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fa-solid fa-cart-shopping text-emerald-600 text-sm"></i>
                            </div>
                            <div style="font-size: 0.8125rem;">
                                <strong style="color: var(--color-text); display: block;">{{ app()->getLocale() == 'ar' ? 'الطلبات والمبيعات الجديدة' : 'Real-time Customer Orders' }}</strong>
                                <span style="color: var(--color-text-muted); font-size: 0.75rem;">{{ app()->getLocale() == 'ar' ? 'تنبيه صوتي وبصري فوري عند إتمام عمليات شراء جديدة بالمتجر' : 'Audio chime and instant alert on incoming storefront purchases' }}</span>
                            </div>
                        </div>

                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 34px; height: 34px; border-radius: 0.6rem; background: rgba(99, 102, 241, 0.12); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fa-solid fa-arrow-right-arrow-left text-indigo-600 text-sm"></i>
                            </div>
                            <div style="font-size: 0.8125rem;">
                                <strong style="color: var(--color-text); display: block;">{{ app()->getLocale() == 'ar' ? 'حركات التحويل بين الفروع' : 'Stock Transfer Movements' }}</strong>
                                <span style="color: var(--color-text-muted); font-size: 0.75rem;">{{ app()->getLocale() == 'ar' ? 'متابعة مباشرة لعمليات النقل بين المستودعات ومنافذ البيع' : 'Live tracking for inventory relocation and multi-branch movements' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div style="display: flex; flex-direction: column; gap: 0.625rem;">
                        <button type="button" id="btnActivateFcmModal" onclick="triggerBrowserFcmPermission()" class="btn btn-primary" style="width: 100%; padding: 0.85rem; font-size: 0.9375rem; font-weight: 800; border-radius: 0.75rem; background: linear-gradient(135deg, #0A4F78, #0284C7); border: none; box-shadow: 0 4px 14px rgba(10, 79, 120, 0.35); display: flex; align-items: center; justify-content: center; gap: 0.5rem; cursor: pointer;">
                            <i class="fa-solid fa-bell"></i>
                            <span>{{ app()->getLocale() == 'ar' ? 'تفعيل الإشعارات الآن' : 'Enable Notifications Now' }}</span>
                        </button>

                        <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 0.5rem;">
                            <button type="button" onclick="showBrowserConfigGuide()" style="color: #0284C7; font-weight: 700; font-size: 0.775rem; text-decoration: none; padding: 0; background: none; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 0.35rem;">
                                <i class="fa-solid fa-circle-question"></i>
                                <span>{{ app()->getLocale() == 'ar' ? 'كيفية ضبط إعدادات المتصفح' : 'Browser setup guide' }}</span>
                            </button>

                            <button type="button" onclick="closeFcmPermissionModal(7)" style="color: var(--color-text-muted); font-size: 0.775rem; text-decoration: none; padding: 0; background: none; border: none; cursor: pointer;">
                                {{ app()->getLocale() == 'ar' ? 'ربما لاحقاً' : 'Maybe later' }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- STATE 2: Illustrated Browser Configuration & Unblock Guide -->
                <div id="fcmModalStateGuide" style="display: none; padding: 2.25rem 2rem 2rem 2rem; text-align: start;">
                    <!-- Header -->
                    <div style="display: flex; align-items: center; gap: 0.875rem; margin-bottom: 1.25rem;">
                        <div style="width: 48px; height: 48px; border-radius: 0.875rem; background: rgba(234, 88, 12, 0.12); display: flex; align-items: center; justify-content: center; flex-shrink: 0; border: 1px solid rgba(234, 88, 12, 0.25);">
                            <i class="fa-solid fa-shield-halved text-amber-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 style="font-size: 1.2rem; font-weight: 900; color: var(--color-text); margin: 0;">
                                {{ app()->getLocale() == 'ar' ? 'تعديل إعدادات المتصفح لتفعيل الإشعارات' : 'Browser Settings Setup & Unblock Guide' }}
                            </h3>
                            <p style="font-size: 0.75rem; color: var(--color-text-muted); margin: 0.2rem 0 0 0;">
                                {{ app()->getLocale() == 'ar' ? '3 خطوات بسيطة للسماح بالإشعارات في المتصفح' : 'Follow 3 easy steps to allow notifications in Chrome / Edge / Firefox' }}
                            </p>
                        </div>
                    </div>

                    <!-- 3 Steps Visual Guide -->
                    <div style="display: flex; flex-direction: column; gap: 0.875rem; margin-bottom: 1.75rem;">
                        
                        <!-- Step 1 -->
                        <div style="display: flex; gap: 0.875rem; background: var(--color-bg-subtle, rgba(0,0,0,0.03)); padding: 0.875rem 1rem; border-radius: 0.875rem; border: 1px solid var(--color-border); align-items: flex-start;">
                            <div style="width: 26px; height: 26px; border-radius: 50%; background: #0A4F78; color: #FFFFFF; font-weight: 800; font-size: 0.75rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;">
                                1
                            </div>
                            <div style="flex: 1; font-size: 0.8125rem;">
                                <strong style="color: var(--color-text); display: block; margin-bottom: 0.2rem;">
                                    {{ app()->getLocale() == 'ar' ? 'اضغط على أيقونة الإعدادات أو القفل' : 'Click the Settings or Lock Icon' }}
                                </strong>
                                <div style="color: var(--color-text-muted); line-height: 1.4; font-size: 0.75rem;">
                                    {{ app()->getLocale() == 'ar' 
                                        ? 'في أعلى المتصفح، اضغط على أيقونة عناصر التحكم بجوار رابط الموقع في شريط العناوين (أيقونة القفل أو أشرطة الإعدادات).' 
                                        : 'In the address bar at the top, click the Lock icon or site controls button beside the URL.' }}
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 0.5rem; background: rgba(10, 79, 120, 0.1); color: #0A4F78; flex-shrink: 0;">
                                <i class="fa-solid fa-sliders"></i>
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div style="display: flex; gap: 0.875rem; background: var(--color-bg-subtle, rgba(0,0,0,0.03)); padding: 0.875rem 1rem; border-radius: 0.875rem; border: 1px solid var(--color-border); align-items: flex-start;">
                            <div style="width: 26px; height: 26px; border-radius: 50%; background: #0A4F78; color: #FFFFFF; font-weight: 800; font-size: 0.75rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;">
                                2
                            </div>
                            <div style="flex: 1; font-size: 0.8125rem;">
                                <strong style="color: var(--color-text); display: block; margin-bottom: 0.2rem;">
                                    {{ app()->getLocale() == 'ar' ? 'تغيير خيار الإشعارات إلى سماح' : 'Switch Notifications to "Allow"' }}
                                </strong>
                                <div style="color: var(--color-text-muted); line-height: 1.4; font-size: 0.75rem;">
                                    {{ app()->getLocale() == 'ar' 
                                        ? 'ابحث عن خيار "الإشعارات" (Notifications) وقم بتحويله من "حظر" إلى "سماح" (Allow).' 
                                        : 'Find "Notifications" in site permissions and switch toggle from "Block" to "Allow".' }}
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 0.5rem; background: rgba(16, 185, 129, 0.1); color: #10B981; flex-shrink: 0;">
                                <i class="fa-solid fa-toggle-on text-lg"></i>
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div style="display: flex; gap: 0.875rem; background: var(--color-bg-subtle, rgba(0,0,0,0.03)); padding: 0.875rem 1rem; border-radius: 0.875rem; border: 1px solid var(--color-border); align-items: flex-start;">
                            <div style="width: 26px; height: 26px; border-radius: 50%; background: #0A4F78; color: #FFFFFF; font-weight: 800; font-size: 0.75rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;">
                                3
                            </div>
                            <div style="flex: 1; font-size: 0.8125rem;">
                                <strong style="color: var(--color-text); display: block; margin-bottom: 0.2rem;">
                                    {{ app()->getLocale() == 'ar' ? 'إعادة تحميل الصفحة الآن' : 'Reload the Page' }}
                                </strong>
                                <div style="color: var(--color-text-muted); line-height: 1.4; font-size: 0.75rem;">
                                    {{ app()->getLocale() == 'ar' 
                                        ? 'اضغط على زر إعادة التحميل بالأسفل لتطبيق الإعداد الجديد والبدء في استلام الإشعارات.' 
                                        : 'Click reload page below to apply new browser permissions and connect FCM.' }}
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 0.5rem; background: rgba(2, 132, 199, 0.1); color: #0284C7; flex-shrink: 0;">
                                <i class="fa-solid fa-rotate-right"></i>
                            </div>
                        </div>

                    </div>

                    <!-- Guide Actions -->
                    <div style="display: flex; gap: 0.75rem;">
                        <button type="button" onclick="window.location.reload()" class="btn btn-primary" style="flex: 1; padding: 0.75rem; font-weight: 800; border-radius: 0.75rem; background: linear-gradient(135deg, #0A4F78, #0284C7); border: none; display: flex; align-items: center; justify-content: center; gap: 0.5rem; cursor: pointer;">
                            <i class="fa-solid fa-rotate-right"></i>
                            <span>{{ app()->getLocale() == 'ar' ? 'إعادة تحميل الصفحة الآن' : 'Reload Page Now' }}</span>
                        </button>
                        <button type="button" onclick="showFcmPermissionPrompt()" class="btn btn-secondary" style="padding: 0.75rem 1.25rem; font-weight: 700; border-radius: 0.75rem; cursor: pointer;">
                            {{ app()->getLocale() == 'ar' ? 'العودة' : 'Back' }}
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <script>
            /* =========================================================================
               FCM Permission & Browser Settings UI Management
               ========================================================================= */
            window.openFcmPermissionModal = function() {
                const modal = document.getElementById('fcmPermissionModal');
                if (!modal) return;
                
                if (typeof Notification !== 'undefined' && Notification.permission === 'denied') {
                    window.showBrowserConfigGuide();
                } else {
                    window.showFcmPermissionPrompt();
                }
                
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            };

            window.closeFcmPermissionModal = function(dismissDays = 0) {
                const modal = document.getElementById('fcmPermissionModal');
                if (modal) {
                    modal.style.display = 'none';
                    document.body.style.overflow = '';
                }
                if (dismissDays > 0) {
                    const expiry = Date.now() + (dismissDays * 24 * 60 * 60 * 1000);
                    localStorage.setItem('bz_fcm_dismissed_until', expiry.toString());
                }
            };

            window.showFcmPermissionPrompt = function() {
                const promptEl = document.getElementById('fcmModalStatePrompt');
                const guideEl = document.getElementById('fcmModalStateGuide');
                if (promptEl) promptEl.style.display = 'block';
                if (guideEl) guideEl.style.display = 'none';
            };

            window.showBrowserConfigGuide = function() {
                const promptEl = document.getElementById('fcmModalStatePrompt');
                const guideEl = document.getElementById('fcmModalStateGuide');
                if (promptEl) promptEl.style.display = 'none';
                if (guideEl) guideEl.style.display = 'block';
            };

            window.triggerBrowserFcmPermission = function() {
                const btn = document.getElementById('btnActivateFcmModal');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>Connecting...</span>';
                }

                if (!('Notification' in window)) {
                    alert('{{ app()->getLocale() == "ar" ? "متصفحك لا يدعم خاصية الإشعارات المكتبية." : "Your browser does not support desktop notifications." }}');
                    window.closeFcmPermissionModal();
                    return;
                }

                Notification.requestPermission().then(permission => {
                    if (permission === 'granted') {
                        window.closeFcmPermissionModal();
                        showAdminToast(
                            '{{ app()->getLocale() == "ar" ? "تم تفعيل الإشعارات بنجاح" : "Notifications Activated Successfully" }}',
                            '{{ app()->getLocale() == "ar" ? "أنت الآن متصل بنظام إشعارات بلو زون الفورية (FCM)." : "You are now connected to BlueZone Realtime FCM Alerts." }}',
                            'fa-solid fa-bell text-emerald-500'
                        );
                        if (window.initializeBluezoneFcm) {
                            window.initializeBluezoneFcm();
                        }
                    } else if (permission === 'denied') {
                        if (btn) {
                            btn.disabled = false;
                            btn.innerHTML = '<i class="fa-solid fa-bell"></i> <span>{{ app()->getLocale() == "ar" ? "تفعيل الإشعارات الآن" : "Enable Notifications Now" }}</span>';
                        }
                        window.showBrowserConfigGuide();
                    } else {
                        if (btn) {
                            btn.disabled = false;
                            btn.innerHTML = '<i class="fa-solid fa-bell"></i> <span>{{ app()->getLocale() == "ar" ? "تفعيل الإشعارات الآن" : "Enable Notifications Now" }}</span>';
                        }
                    }
                }).catch(err => {
                    console.error('Permission request error:', err);
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fa-solid fa-bell"></i> <span>{{ app()->getLocale() == "ar" ? "تفعيل الإشعارات الآن" : "Enable Notifications Now" }}</span>';
                    }
                });
            };

            window.activateFcmTokenDirectly = function(triggerBtn) {
                const originalHtml = triggerBtn ? triggerBtn.innerHTML : '';
                if (triggerBtn) {
                    triggerBtn.disabled = true;
                    triggerBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> {{ app()->getLocale() == "ar" ? "جاري الاتصال..." : "Connecting..." }}';
                }

                if (!('Notification' in window)) {
                    alert('{{ app()->getLocale() == "ar" ? "متصفحك لا يدعم خاصية الإشعارات المكتبية." : "Your browser does not support desktop notifications." }}');
                    if (triggerBtn) { triggerBtn.disabled = false; triggerBtn.innerHTML = originalHtml; }
                    return;
                }

                if (!window.isSecureContext && window.location.hostname !== 'localhost' && window.location.hostname !== '127.0.0.1') {
                    alert('{{ app()->getLocale() == "ar" ? "تنبيه: خاصية إشعارات FCM تشترط تشغيل الموقع عبر HTTPS أو localhost. إذا كنت تستخدم Laragon، يرجى تفعيل SSL أو استخدام HTTPS." : "Web Push requires HTTPS or http://localhost. Please use HTTPS." }}');
                }

                Notification.requestPermission().then(permission => {
                    if (permission === 'granted') {
                        if (window.initializeBluezoneFcm) {
                            window.initializeBluezoneFcm(function(result) {
                                if (triggerBtn) {
                                    triggerBtn.disabled = false;
                                    triggerBtn.innerHTML = originalHtml;
                                }
                                if (result && result.error) {
                                    alert(( '{{ app()->getLocale() == "ar" ? "تنبيه FCM: " : "FCM Note: " }}' ) + result.error);
                                }
                            });
                        }
                    } else if (permission === 'denied') {
                        if (triggerBtn) {
                            triggerBtn.disabled = false;
                            triggerBtn.innerHTML = originalHtml;
                        }
                        window.openFcmPermissionModal();
                        window.showBrowserConfigGuide();
                    } else {
                        if (triggerBtn) {
                            triggerBtn.disabled = false;
                            triggerBtn.innerHTML = originalHtml;
                        }
                    }
                }).catch(err => {
                    console.error('Permission request error:', err);
                    if (triggerBtn) {
                        triggerBtn.disabled = false;
                        triggerBtn.innerHTML = originalHtml;
                    }
                    alert('Permission error: ' + err.message);
                });
            };

            // Firebase Web SDK Client & Real-time Engine
            window.initializeBluezoneFcm = function(callback) {
                const firebaseConfig = {
                    apiKey: "{{ \App\Models\Setting::get('fcm_api_key') ?: config('fcm.api_key') ?: config('services.firebase.api_key') }}",
                    authDomain: "{{ \App\Models\Setting::get('fcm_auth_domain') ?: config('fcm.auth_domain') ?: config('services.firebase.auth_domain') }}",
                    projectId: "{{ \App\Models\Setting::get('fcm_project_id') ?: config('fcm.project_id') ?: config('services.firebase.project_id') }}",
                    storageBucket: "{{ \App\Models\Setting::get('fcm_storage_bucket') ?: config('fcm.storage_bucket') ?: config('services.firebase.storage_bucket') }}",
                    messagingSenderId: "{{ \App\Models\Setting::get('fcm_messaging_sender_id') ?: config('fcm.messaging_sender_id') ?: config('services.firebase.messaging_sender_id') }}",
                    appId: "{{ \App\Models\Setting::get('fcm_app_id') ?: config('fcm.app_id') ?: config('services.firebase.app_id') }}",
                    measurementId: "{{ \App\Models\Setting::get('fcm_measurement_id') ?: config('fcm.measurement_id') ?: config('services.firebase.measurement_id') }}"
                };
                const vapidKey = "{{ \App\Models\Setting::get('fcm_vapid_key') ?: config('fcm.vapid_key') ?: config('services.firebase.vapid_key') }}";

                if (firebaseConfig.projectId && typeof firebase !== 'undefined' && 'serviceWorker' in navigator) {
                    try {
                        if (!firebase.apps.length) {
                            firebase.initializeApp(firebaseConfig);
                        }
                        const messaging = firebase.messaging();

                        const swUrl = '/firebase-messaging-sw.js?projectId=' + encodeURIComponent(firebaseConfig.projectId)
                            + '&apiKey=' + encodeURIComponent(firebaseConfig.apiKey)
                            + '&authDomain=' + encodeURIComponent(firebaseConfig.authDomain || '')
                            + '&storageBucket=' + encodeURIComponent(firebaseConfig.storageBucket || '')
                            + '&messagingSenderId=' + encodeURIComponent(firebaseConfig.messagingSenderId)
                            + '&appId=' + encodeURIComponent(firebaseConfig.appId);

                        navigator.serviceWorker.register(swUrl).then(registration => {
                            if (Notification.permission === 'granted') {
                                if (!vapidKey) {
                                    console.warn('[FCM Notice]: Web Push requires a VAPID Public Key. Generate it in Firebase Console > Project Settings > Cloud Messaging > Web Push certificates, then save it in Admin Settings.');
                                }
                                const tokenOpts = { serviceWorkerRegistration: registration };
                                if (vapidKey) tokenOpts.vapidKey = vapidKey;

                                messaging.getToken(tokenOpts).then(token => {
                                    if (token) {
                                        window.currentAdminFcmToken = token;
                                        fetch('/admin/notifications/fcm-token', {
                                            method: 'POST',
                                            headers: {
                                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                                                'Accept': 'application/json',
                                                'Content-Type': 'application/json'
                                            },
                                            body: JSON.stringify({ fcm_token: token })
                                        }).then(res => res.json()).then(data => {
                                            const tokenContainer = document.getElementById('fcmTokenBadgeContainer');
                                            if (tokenContainer) {
                                                tokenContainer.innerHTML = `
                                                    <span class="badge badge-success text-xs font-bold" style="padding: 0.35rem 0.65rem;">
                                                        <i class="fa-solid fa-circle-check mr-1 ml-1"></i> {{ app()->getLocale() == 'ar' ? 'جهازك متصل بـ FCM' : 'Device FCM Linked' }}
                                                    </span>
                                                    <button type="button" class="btn btn-outline btn-xs" onclick="copyFcmToken('${token}')">
                                                        <i class="fa-regular fa-copy mr-1 ml-1"></i> {{ app()->getLocale() == 'ar' ? 'نسخ التوكن' : 'Copy Token' }}
                                                    </button>
                                                `;
                                            }
                                            // Only show toast if triggered manually by user action, avoid showing on every page refresh
                                            if (typeof callback === 'function') {
                                            showAdminToast(
                                                '{{ app()->getLocale() == "ar" ? "تم ربط توكن FCM بنجاح" : "FCM Device Token Linked" }}',
                                                '{{ app()->getLocale() == "ar" ? "متصفحك متصل الآن بالإشعارات السحابية الفورية." : "Your browser is now registered for live push notifications." }}',
                                                'fa-solid fa-circle-check text-emerald-500'
                                            );
                                                callback({ success: true, token: token });
                                            }
                                        }).catch(err => {
                                            console.warn('Token sync note:', err);
                                            if (typeof callback === 'function') callback({ success: false, error: err.message });
                                        });
                                    }
                                }).catch(e => {
                                    console.warn('FCM getToken note:', e);
                                    if (!vapidKey) {
                                        console.warn('[FCM Setup Needed]: Please set fcm_vapid_key in Admin Settings.');
                                    }
                                    if (typeof callback === 'function') callback({ success: false, error: e.message });
                                });
                            }
                        });

                        // Foreground real-time message handler
                        messaging.onMessage(payload => {
                            console.log('[FCM Foreground Event]:', payload);
                            const title = (payload.notification && payload.notification.title) || (payload.data && payload.data.title) || 'BlueZone System Alert';
                            const body = (payload.notification && payload.notification.body) || (payload.data && payload.data.body) || '';
                            const actionUrl = (payload.data && payload.data.action_url) || '/admin';
                            const icon = (payload.data && payload.data.icon) || 'fa-solid fa-bell text-sky-500';

                            showAdminToast(title, body, icon, actionUrl);
                            prependNotificationToDropdown(title, body, icon, actionUrl);
                        });
                    } catch (e) {
                        console.warn('Firebase client setup note:', e);
                    }
                }
            };

            document.addEventListener('DOMContentLoaded', function() {
                // Initialize Firebase client
                window.initializeBluezoneFcm();

                // Auto-prompt permission modal if default and not dismissed recently
                if (typeof Notification !== 'undefined' && Notification.permission === 'default') {
                    const dismissedUntil = localStorage.getItem('bz_fcm_dismissed_until');
                    const now = Date.now();
                    if (!dismissedUntil || now > parseInt(dismissedUntil)) {
                        setTimeout(function() {
                            window.openFcmPermissionModal();
                        }, 1200);
                    }
                }

                // Real-time Heartbeat Polling (Active sync fallback every 40s)
                let lastCheckedCount = parseInt(document.getElementById('adminNotificationBadge')?.textContent.trim()) || 0;
                setInterval(function() {
                    fetch('/admin/notifications?ajax=1', {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (typeof data.unread_count !== 'undefined') {
                            if (data.unread_count > lastCheckedCount) {
                                const newest = (data.notifications && data.notifications.length > 0) ? data.notifications[0] : null;
                                if (newest) {
                                    showAdminToast(newest.title, newest.message, newest.icon, newest.action_url);
                                    prependNotificationToDropdown(newest.title, newest.message, newest.icon, newest.action_url, newest.id);
                                }
                            }
                            lastCheckedCount = data.unread_count;
                            updateNotificationBadge(data.unread_count);
                        }
                    })
                    .catch(() => {});
                }, 40000);
            });
        </script>
</x-layouts.app>

