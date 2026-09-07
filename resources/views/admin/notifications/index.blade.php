<x-layouts.admin 
    :pageTitle="__('admin.notifications.title') ?? 'Notifications Center'" 
    :pageSubtitle="__('admin.notifications.subtitle') ?? 'Manage your real-time alerts, system triggers, and clinical order notices'"
    :breadcrumbs="[__('admin.notifications.title') ?? 'Notifications' => route('admin.notifications.index')]"
>
    <x-slot name="actions">
        <div class="flex items-center gap-2">
            <button type="button" 
                    id="btnTestFcmPush" 
                    onclick="triggerFcmTestPush()" 
                    class="btn btn-outline btn-sm font-semibold flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-satellite-dish text-sky-500"></i>
                <span>{{ __('admin.notifications.test_push') ?? 'Test FCM Push' }}</span>
            </button>

            @if($unreadCount > 0)
                <form method="POST" action="{{ route('admin.notifications.mark-all-read') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-secondary btn-sm font-semibold flex items-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-check-double text-emerald-500"></i>
                        <span>{{ __('admin.notifications.mark_all_read') ?? 'Mark All as Read' }}</span>
                    </button>
                </form>
            @endif
        </div>
    </x-slot>


    <!-- Notification Filters and Stats -->
    <div class="card mb-6">
        <div class="card-body p-4 sm:p-5">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.notifications.index', ['filter' => 'all']) }}" 
                       class="btn btn-sm {{ $filter === 'all' ? 'btn-primary' : 'btn-ghost' }}">
                        {{ __('admin.notifications.all') ?? 'All' }} ({{ $totalCount }})
                    </a>
                    <a href="{{ route('admin.notifications.index', ['filter' => 'unread']) }}" 
                       class="btn btn-sm {{ $filter === 'unread' ? 'btn-primary' : 'btn-ghost' }} flex items-center gap-1.5">
                        {{ __('admin.notifications.unread') ?? 'Unread' }}
                        @if($unreadCount > 0)
                            <span class="bg-red-500 text-white text-xs px-1.5 py-0.5 rounded-full font-bold leading-none">{{ $unreadCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('admin.notifications.index', ['filter' => 'read']) }}" 
                       class="btn btn-sm {{ $filter === 'read' ? 'btn-primary' : 'btn-ghost' }}">
                        {{ __('admin.notifications.read') ?? 'Read' }}
                    </a>
                </div>

                <div class="text-xs text-muted">
                    {{ __('admin.notifications.showing') ?? 'Showing latest operational events' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Items List -->
    <div class="card">
        @if($notifications->count() > 0)
            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @foreach($notifications as $item)
                    @php
                        $data = $item->data ?? [];
                        $isUnread = is_null($item->read_at);
                        $iconClass = $data['icon'] ?? 'fa-solid fa-bell text-sky-500';
                        $rawUrl = $data['action_url'] ?? null;
                        if ($rawUrl && (str_starts_with($rawUrl, 'http://') || str_starts_with($rawUrl, 'https://'))) {
                            $p = parse_url($rawUrl);
                            $rawUrl = ($p['path'] ?? '') . (isset($p['query']) ? '?' . $p['query'] : '') . (isset($p['fragment']) ? '#' . $p['fragment'] : '');
                        }
                        $actionUrl = $rawUrl;
                    @endphp
                    <div class="p-4 sm:p-5 flex items-start gap-4 transition-colors {{ $isUnread ? 'bg-sky-50/50 dark:bg-sky-950/20' : 'hover:bg-slate-50/60 dark:hover:bg-slate-900/40' }}">
                        <!-- Icon Circle -->
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 {{ $isUnread ? 'bg-sky-100 dark:bg-sky-900/40' : 'bg-slate-100 dark:bg-slate-800' }}">
                            <i class="{{ $iconClass }} text-lg"></i>
                        </div>

                        <!-- Content Area -->
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <div class="flex items-center gap-2">
                                    <h4 class="text-sm font-bold {{ $isUnread ? 'text-slate-900 dark:text-white' : 'text-slate-700 dark:text-slate-300' }} m-0">
                                        {{ $data['title'] ?? 'Notification' }}
                                    </h4>
                                    @if($isUnread)
                                        <span class="w-2 h-2 rounded-full bg-sky-500 flex-shrink-0" title="Unread"></span>
                                    @endif
                                </div>
                                <span class="text-xs text-muted flex items-center gap-1">
                                    <i class="fa-regular fa-clock text-[10px]"></i>
                                    {{ $item->created_at ? $item->created_at->diffForHumans() : '' }}
                                </span>
                            </div>

                            <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 mt-1 mb-2 leading-relaxed">
                                {{ $data['message'] ?? '' }}
                            </p>

                            <div class="flex flex-wrap items-center gap-3 mt-2">
                                @if($actionUrl)
                                    <a href="{{ $actionUrl }}" class="btn btn-outline btn-sm text-xs py-1 px-2.5 flex items-center gap-1.5">
                                        <span>{{ __('admin.notifications.view_details') ?? 'View Details' }}</span>
                                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                                    </a>
                                @endif

                                @if($isUnread)
                                    <form method="POST" action="{{ route('admin.notifications.read', $item->id) }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="text-xs font-semibold text-sky-600 dark:text-sky-400 hover:underline cursor-pointer bg-transparent border-none p-0">
                                            {{ __('admin.notifications.mark_read') ?? 'Mark as read' }}
                                        </button>
                                    </form>
                                @endif

                                <form method="POST" action="{{ route('admin.notifications.destroy', $item->id) }}" style="display: inline;" onsubmit="return confirm('{{ __('admin.notifications.confirm_delete') ?? 'Delete this notification?' }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-rose-500 hover:text-rose-700 cursor-pointer bg-transparent border-none p-0 ml-auto">
                                        <i class="fa-solid fa-trash-can mr-1 ml-1"></i>
                                        {{ __('app.actions.delete') ?? 'Delete' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $notifications->links() }}
            </div>
        @else
            <div class="empty-state py-12 text-center">
                <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto mb-4 text-slate-400 text-2xl">
                    <i class="fa-solid fa-bell-slash"></i>
                </div>
                <h3 class="text-base font-bold text-slate-800 dark:text-slate-200 m-0 mb-1">
                    {{ __('admin.notifications.no_notifications') ?? 'No notifications found' }}
                </h3>
                <p class="text-xs text-muted max-w-sm mx-auto">
                    {{ __('admin.notifications.empty_desc') ?? 'When orders, stock events, or system alerts occur, they will appear here in real time.' }}
                </p>
            </div>
        @endif
    </div>
</x-layouts.admin>
