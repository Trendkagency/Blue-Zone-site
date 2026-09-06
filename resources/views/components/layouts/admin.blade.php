@props([
    'title' => null,
    'pageTitle' => null,
    'pageSubtitle' => null,
    'breadcrumbs' => [],
])

<x-layouts.app :title="($title ?? $pageTitle ?? 'Admin') . ' — ' . __('admin.portal_title')">
    <div class="admin-layout">
<<<<<<< HEAD
        <!-- Admin Sidebar -->
        <aside class="admin-sidebar">
=======
        <!-- Mobile Sidebar Backdrop -->
        <div id="adminSidebarBackdrop" class="admin-sidebar-backdrop" onclick="toggleAdminSidebar()"></div>

        <!-- Admin Sidebar -->
        <aside class="admin-sidebar" id="adminSidebar">
>>>>>>> origin/main
            <div class="sidebar-header">
                <a href="{{ route('admin.dashboard') }}" style="display: flex; align-items: center; gap: 0.75rem; text-decoration: none;">
                    <img src="{{ asset('assets/logo/logo-main.png') }}" alt="{{ __('app.brand_name') }}" style="height: 32px;" onerror="this.onerror=null; this.src='{{ asset('bluezone logo.png') }}';">
                    <span class="sidebar-brand-title">BZ-OS</span>
                </a>
<<<<<<< HEAD
=======
                <button type="button" class="btn btn-ghost btn-sm lg:hidden cursor-pointer" onclick="toggleAdminSidebar()" aria-label="Close sidebar" style="color: #94A3B8; padding: 0.25rem 0.5rem;">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
>>>>>>> origin/main
            </div>

            <nav class="sidebar-menu">
                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
<<<<<<< HEAD
                    <svg class="sidebar-link-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
=======
                    <i class="fa-solid fa-chart-line sidebar-link-icon"></i>
>>>>>>> origin/main
                    <span>{{ __('admin.menu.dashboard') }}</span>
                </a>

                <!-- Catalog -->
                <div class="menu-category">{{ __('admin.menu.catalog') }}</div>
                <a href="{{ route('admin.products.index') }}" class="sidebar-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
<<<<<<< HEAD
                    <svg class="sidebar-link-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <span>{{ __('admin.menu.products') }}</span>
                </a>
                <a href="{{ route('admin.categories.index') }}" class="sidebar-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <svg class="sidebar-link-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                    </svg>
=======
                    <i class="fa-solid fa-boxes-stacked sidebar-link-icon"></i>
                    <span>{{ __('admin.menu.products') }}</span>
                </a>
                <a href="{{ route('admin.categories.index') }}" class="sidebar-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-layer-group sidebar-link-icon"></i>
>>>>>>> origin/main
                    <span>{{ __('admin.menu.categories') }}</span>
                </a>

                <!-- Inventory -->
                <div class="menu-category">{{ __('admin.menu.inventory') }}</div>
                <a href="{{ route('admin.inventory.index') }}" class="sidebar-link {{ request()->routeIs('admin.inventory.index') || request()->routeIs('admin.inventory.show') ? 'active' : '' }}">
<<<<<<< HEAD
                    <svg class="sidebar-link-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span>{{ __('admin.menu.stock_levels') }}</span>
                </a>
                <a href="{{ route('admin.inventory.transfers') }}" class="sidebar-link {{ request()->routeIs('admin.inventory.transfers') ? 'active' : '' }}">
                    <svg class="sidebar-link-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                    <span>{{ __('admin.menu.stock_transfers') }}</span>
                </a>
                <a href="{{ route('admin.inventory.history') }}" class="sidebar-link {{ request()->routeIs('admin.inventory.history') ? 'active' : '' }}">
                    <svg class="sidebar-link-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
=======
                    <i class="fa-solid fa-warehouse sidebar-link-icon"></i>
                    <span>{{ __('admin.menu.stock_levels') }}</span>
                </a>
                <a href="{{ route('admin.inventory.transfers') }}" class="sidebar-link {{ request()->routeIs('admin.inventory.transfers') ? 'active' : '' }}">
                    <i class="fa-solid fa-arrow-right-arrow-left sidebar-link-icon"></i>
                    <span>{{ __('admin.menu.stock_transfers') }}</span>
                </a>
                <a href="{{ route('admin.inventory.history') }}" class="sidebar-link {{ request()->routeIs('admin.inventory.history') ? 'active' : '' }}">
                    <i class="fa-solid fa-clock-rotate-left sidebar-link-icon"></i>
>>>>>>> origin/main
                    <span>{{ __('admin.menu.stock_history') }}</span>
                </a>

                <!-- Sales -->
                <div class="menu-category">{{ __('admin.menu.sales') }}</div>
                <a href="{{ route('admin.orders.index') }}" class="sidebar-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
<<<<<<< HEAD
                    <svg class="sidebar-link-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <span>{{ __('admin.menu.online_orders') }}</span>
                </a>
                <a href="{{ route('admin.offline-sales.index') }}" class="sidebar-link {{ request()->routeIs('admin.offline-sales.*') ? 'active' : '' }}">
                    <svg class="sidebar-link-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    <span>{{ __('admin.menu.offline_sales') }}</span>
                </a>
                <a href="{{ route('admin.invoices.index') }}" class="sidebar-link {{ request()->routeIs('admin.invoices.*') ? 'active' : '' }}">
                    <svg class="sidebar-link-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
=======
                    <i class="fa-solid fa-bag-shopping sidebar-link-icon"></i>
                    <span>{{ __('admin.menu.online_orders') }}</span>
                </a>
                <a href="{{ route('admin.offline-sales.index') }}" class="sidebar-link {{ request()->routeIs('admin.offline-sales.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-cash-register sidebar-link-icon"></i>
                    <span>{{ __('admin.menu.offline_sales') }}</span>
                </a>
                <a href="{{ route('admin.invoices.index') }}" class="sidebar-link {{ request()->routeIs('admin.invoices.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-file-invoice-dollar sidebar-link-icon"></i>
>>>>>>> origin/main
                    <span>{{ __('admin.menu.invoices') }}</span>
                </a>

                <!-- Customers & Analytics -->
                <div class="menu-category">{{ __('admin.menu.customers') }} & {{ __('admin.menu.reports') }}</div>
                <a href="{{ route('admin.customers.index') }}" class="sidebar-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
<<<<<<< HEAD
                    <svg class="sidebar-link-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span>{{ __('admin.menu.customers') }}</span>
                </a>
                <a href="{{ route('admin.reports.index') }}" class="sidebar-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                    <svg class="sidebar-link-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
=======
                    <i class="fa-solid fa-users-gear sidebar-link-icon"></i>
                    <span>{{ __('admin.menu.customers') }}</span>
                </a>
                <a href="{{ route('admin.reports.index') }}" class="sidebar-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-pie sidebar-link-icon"></i>
>>>>>>> origin/main
                    <span>{{ __('admin.menu.reports') }}</span>
                </a>

                <!-- Content & Access -->
                <div class="menu-category">{{ __('admin.menu.content') }} & {{ __('admin.menu.access_control') }}</div>
                <a href="{{ route('admin.content.index') }}" class="sidebar-link {{ request()->routeIs('admin.content.*') ? 'active' : '' }}">
<<<<<<< HEAD
                    <svg class="sidebar-link-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <span>{{ __('admin.menu.content') }}</span>
                </a>
                <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <svg class="sidebar-link-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span>{{ __('admin.menu.users') }}</span>
                </a>
                <a href="{{ route('admin.roles.index') }}" class="sidebar-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                    <svg class="sidebar-link-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <span>{{ __('admin.menu.roles') }}</span>
                </a>
                <a href="{{ route('admin.settings.index') }}" class="sidebar-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <svg class="sidebar-link-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
=======
                    <i class="fa-solid fa-newspaper sidebar-link-icon"></i>
                    <span>{{ __('admin.menu.content') }}</span>
                </a>
                <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-user-shield sidebar-link-icon"></i>
                    <span>{{ __('admin.menu.users') }}</span>
                </a>
                <a href="{{ route('admin.roles.index') }}" class="sidebar-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-id-badge sidebar-link-icon"></i>
                    <span>{{ __('admin.menu.roles') }}</span>
                </a>
                <a href="{{ route('admin.profile.index') }}" class="sidebar-link {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-user-gear sidebar-link-icon"></i>
                    <span>{{ __('admin.profile.title') }}</span>
                </a>
                <a href="{{ route('admin.settings.index') }}" class="sidebar-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-sliders sidebar-link-icon"></i>
>>>>>>> origin/main
                    <span>{{ __('admin.menu.settings') }}</span>
                </a>
            </nav>
        </aside>

        <!-- Admin Content Shell -->
        <div class="admin-main">
            <!-- Header -->
            <header class="admin-header">
                <div class="header-left">
<<<<<<< HEAD
                    <button type="button" class="btn btn-ghost btn-icon" onclick="toggleAdminSidebar()" title="Toggle Sidebar">
                        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <div class="breadcrumbs">
=======
                    <button type="button" class="btn btn-ghost btn-icon cursor-pointer" onclick="toggleAdminSidebar()" title="Toggle Sidebar" aria-label="Toggle Sidebar">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>

                    <div class="breadcrumbs hidden sm:flex">
>>>>>>> origin/main
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
<<<<<<< HEAD
                    <button type="button" onclick="toggleTheme()" class="btn btn-ghost btn-icon" title="{{ __('app.theme') }}">
                        🌓
                    </button>

                    <!-- View Storefront Link -->
                    <a href="{{ route('customer.home') }}" class="btn btn-outline btn-sm" target="_blank">
                        {{ __('app.nav.home') }} ↗
                    </a>

                    <!-- User Pill -->
                    <div style="display: flex; align-items: center; gap: 0.5rem; padding-inline-start: 0.5rem; border-inline-start: 1px solid var(--color-border);">
                        <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--color-primary); color: #FFF; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.8125rem;">
                            TM
                        </div>
                        <span class="text-sm font-bold" style="display: none; @media(min-width: 768px){display:inline;}">
                            Tariq M.
                        </span>
=======
                    <button type="button" onclick="if(window.BLUEZONE_THEME){BLUEZONE_THEME.toggle();}else{toggleTheme();}" data-theme-toggle class="btn btn-ghost btn-icon cursor-pointer" title="{{ __('app.theme') }}">
                        <i class="fa-solid fa-circle-half-stroke"></i>
                    </button>

                    <!-- View Storefront Link -->
                    <a href="{{ route('customer.home') }}" class="btn btn-outline btn-sm hidden md:inline-flex" target="_blank">
                        {{ __('app.nav.home') }} <i class="fa-solid fa-arrow-up-right-from-square mr-1 ml-1"></i>
                    </a>

                    <!-- User Profile Pill & Dropdown -->
                    @php
                        $authUser = auth()->user();
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
                                aria-haspopup="true">
                            <div style="position: relative; width: 34px; height: 34px; flex-shrink: 0;">
                                @if($avatarUrl)
                                    <img src="{{ $avatarUrl }}" alt="{{ $userName }}" style="width: 34px; height: 34px; border-radius: 50%; object-fit: cover; border: 2px solid var(--color-primary-light);" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div style="display: none; width: 34px; height: 34px; border-radius: 50%; background: linear-gradient(135deg, #0A4F78, #2A8FC2); color: #FFF; align-items: center; justify-content: center; font-weight: 700; font-size: 0.8125rem; border: 2px solid rgba(255,255,255,0.2);">
                                        {{ $initials }}
                                    </div>
                                @else
                                    <div style="width: 34px; height: 34px; border-radius: 50%; background: linear-gradient(135deg, #0A4F78, #2A8FC2); color: #FFF; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.8125rem; border: 2px solid rgba(255,255,255,0.2);">
                                        {{ $initials }}
                                    </div>
                                @endif
                            </div>
                            <div class="admin-profile-meta hidden md:flex flex-col text-start">
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
                                <a href="{{ route('admin.profile.index') }}#preferences" class="admin-dropdown-item">
                                    <i class="fa-solid fa-volume-high" style="width: 18px; color: #10b981;"></i>
                                    <span>{{ __('admin.profile.acoustic_feedback') }}</span>
                                </a>
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
>>>>>>> origin/main
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
        </div>
    </div>
<<<<<<< HEAD
=======

    <!-- Global Destructive & Force Delete Confirmation Modal -->
    <div id="globalDeleteModal" class="modal-backdrop" style="display: none; align-items: center; justify-content: center;">
        <div class="modal-dialog" style="max-width: 480px; width: 90%; background: var(--color-surface); border-radius: var(--radius-lg); box-shadow: var(--shadow-xl); border: 1px solid var(--color-border); overflow: hidden;">
            <form id="globalDeleteForm" method="POST" action="">
                @csrf
                <input type="hidden" name="_method" value="DELETE">

                <div class="modal-header" style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--color-border); display: flex; align-items: center; justify-content: space-between;">
                    <h4 class="modal-title font-bold text-base" style="display: flex; align-items: center; gap: 0.625rem; margin: 0; color: #DC2626;">
                        <i id="globalDeleteIcon" class="fa-solid fa-triangle-exclamation"></i>
                        <span id="globalDeleteTitle">{{ __('app.actions.delete') }}</span>
                    </h4>
                    <button type="button" class="btn btn-ghost btn-sm" onclick="closeModal('globalDeleteModal')" aria-label="Close" style="padding: 0.35rem 0.6rem;">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="modal-body" style="padding: 1.5rem;">
                    <p id="globalDeleteMessage" style="color: var(--color-text-secondary); margin: 0; font-size: 0.9375rem; line-height: 1.6;">
                        {{ __('admin.confirm_action') }}
                    </p>
                </div>

                <div class="modal-footer" style="padding: 1rem 1.5rem; background: var(--color-bg-subtle); border-top: 1px solid var(--color-border); display: flex; justify-content: flex-end; gap: 0.75rem;">
                    <button type="button" class="btn btn-secondary text-sm" onclick="closeModal('globalDeleteModal')">
                        {{ __('app.actions.cancel') }}
                    </button>
                    <button type="submit" id="globalDeleteBtn" class="btn btn-danger text-sm font-bold">
                        <i class="fa-solid fa-trash-can mr-1 ml-1"></i>
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
                modal.style.display = 'flex';
                modal.classList.add('active');
            }
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.style.display = 'none';
                modal.classList.remove('active');
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
                    btnText.textContent = isAr ? 'حذف نهائي نهائياً' : 'Permanently Delete';
                    btn.className = 'btn btn-danger text-sm font-bold';
                    icon.className = 'fa-solid fa-radiation';
                } else {
                    title.textContent = isAr ? 'تأكيد النقل لسلة المحذوفات' : 'Confirm Move to Trash';
                    message.textContent = isAr
                        ? `هل أنت متأكد من نقل [${itemName || 'العنصر'}] إلى سلة المحذوفات؟ يمكنك مراجعته أو استعادته لاحقاً.`
                        : `Are you sure you want to move [${itemName || 'this item'}] to trash? You can restore it anytime later.`;
                    btnText.textContent = isAr ? 'نقل للمحذوفات' : 'Move to Trash';
                    btn.className = 'btn btn-danger text-sm font-bold';
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
>>>>>>> origin/main
</x-layouts.app>
