<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications or return JSON for AJAX requests.
     */
    public function index(Request $request): View|JsonResponse
    {
        $user = $request->user() ?? auth()->user();

        if ($request->wantsJson() || $request->ajax()) {
            $notifications = $user->notifications()->latest()->limit(15)->get()->map(function ($n) {
                return [
                    'id'         => $n->id,
                    'title'      => $n->data['title'] ?? 'Notification',
                    'message'    => $n->data['message'] ?? '',
                    'type'       => $n->data['type'] ?? 'system',
                    'icon'       => $n->data['icon'] ?? 'fa-solid fa-bell text-sky-500',
                    'action_url' => $n->data['action_url'] ?? null,
                    'read'       => $n->read_at !== null,
                    'time_ago'   => $n->created_at ? $n->created_at->diffForHumans() : '',
                    'created_at' => $n->created_at ? $n->created_at->format('Y-m-d H:i') : '',
                ];
            });

            return response()->json([
                'unread_count'  => $user->unreadNotifications()->count(),
                'notifications' => $notifications,
            ]);
        }

        // Full Page View
        $filter = $request->query('filter', 'all');
        $query = $user->notifications();

        if ($filter === 'unread') {
            $query->whereNull('read_at');
        } elseif ($filter === 'read') {
            $query->whereNotNull('read_at');
        }

        $notifications = $query->latest()->paginate(20)->withQueryString();
        $unreadCount = $user->unreadNotifications()->count();
        $totalCount = $user->notifications()->count();

        return view('admin.notifications.index', compact('notifications', 'unreadCount', 'totalCount', 'filter'));
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(Request $request, string $id): JsonResponse|RedirectResponse
    {
        $user = $request->user() ?? auth()->user();
        $notification = $user ? $user->notifications()->where('id', $id)->first() : null;

        if ($notification && is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'      => true,
                'unread_count' => $user ? $user->unreadNotifications()->count() : 0,
                'message'      => 'Notification marked as read',
            ]);
        }

        return back()->with('success', __('admin.notifications.marked_as_read') ?? 'Notification marked as read.');
    }

    /**
     * Mark all unread notifications as read.
     */
    public function markAllAsRead(Request $request): JsonResponse|RedirectResponse
    {
        $user = $request->user() ?? auth()->user();
        if ($user) {
            $user->unreadNotifications->markAsRead();
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'      => true,
                'unread_count' => 0,
                'message'      => 'All notifications marked as read',
            ]);
        }

        return back()->with('success', __('admin.notifications.all_marked_as_read') ?? 'All notifications marked as read.');
    }

    /**
     * Remove the specified notification from storage.
     */
    public function destroy(Request $request, string $id): JsonResponse|RedirectResponse
    {
        $user = $request->user() ?? auth()->user();
        $notification = $user ? $user->notifications()->where('id', $id)->first() : null;

        if ($notification) {
            $notification->delete();
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'      => true,
                'unread_count' => $user ? $user->unreadNotifications()->count() : 0,
                'message'      => 'Notification deleted successfully',
            ]);
        }

        return back()->with('success', __('admin.notifications.deleted') ?? 'Notification deleted.');
    }

    /**
     * Store or refresh the FCM device token for the authenticated user (Multi-device enabled).
     */
    public function updateFcmToken(Request $request): JsonResponse
    {
        $request->validate([
            'fcm_token'    => ['required', 'string'],
            'device_type'  => ['nullable', 'string', 'max:50'],
            'browser'      => ['nullable', 'string', 'max:100'],
            'os'           => ['nullable', 'string', 'max:100'],
            'device_info'  => ['nullable'],
        ]);

        $user = $request->user() ?? auth()->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated user.',
            ], 401);
        }

        $token = trim((string) $request->input('fcm_token'));
        $userAgent = (string) ($request->userAgent() ?: 'Unknown Browser');

        // Detect OS and Browser if not explicitly passed
        $browser = $request->input('browser');
        $os = $request->input('os');
        $deviceType = $request->input('device_type') ?: 'desktop';

        if (!$browser) {
            if (str_contains($userAgent, 'Edg/')) $browser = 'Edge';
            elseif (str_contains($userAgent, 'Chrome/')) $browser = 'Chrome';
            elseif (str_contains($userAgent, 'Firefox/')) $browser = 'Firefox';
            elseif (str_contains($userAgent, 'Safari/')) $browser = 'Safari';
            else $browser = 'Browser';
        }

        if (!$os) {
            if (str_contains($userAgent, 'Windows')) $os = 'Windows';
            elseif (str_contains($userAgent, 'Macintosh') || str_contains($userAgent, 'Mac OS')) $os = 'macOS';
            elseif (str_contains($userAgent, 'Android')) { $os = 'Android'; $deviceType = 'mobile'; }
            elseif (str_contains($userAgent, 'iPhone') || str_contains($userAgent, 'iPad')) { $os = 'iOS'; $deviceType = 'mobile'; }
            elseif (str_contains($userAgent, 'Linux')) $os = 'Linux';
            else $os = 'OS';
        }

        // 1. Multi-Device Registry: Upsert device into user_devices table
        $device = \App\Models\UserDevice::updateOrCreate(
            [
                'user_id' => $user->id,
                'token'   => $token,
            ],
            [
                'device_type'    => $deviceType,
                'browser'        => $browser,
                'os'             => $os,
                'ip_address'     => $request->ip(),
                'last_active_at' => now(),
                'is_active'      => true,
            ]
        );

        // 2. Backward-Compatibility Sync: Also update users table single token reference
        $user->update([
            'fcm_token'       => $token,
            'fcm_device_info' => [
                'device_id'   => $device->id,
                'device_type' => $deviceType,
                'browser'     => $browser,
                'os'          => $os,
                'ip'          => $request->ip(),
                'user_agent'  => $userAgent,
                'linked_at'   => now()->toIso8601String(),
            ],
        ]);

        return response()->json([
            'success'   => true,
            'message'   => 'FCM token and device registered successfully.',
            'device_id' => $device->id,
        ]);
    }

    /**
     * Send a test push notification to the current admin or specified target.
     */
    public function testPush(Request $request): JsonResponse
    {
        $user = $request->user() ?? auth()->user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $title = $request->input('title') ?: 'تجربة إشعارات FCM | FCM Live Test';
        $message = $request->input('message') ?: 'نظام الإشعارات اللحظية يعمل بنجاح للمخزون والتنبيهات الإدارية. Real-time push dispatched!';
        $type = $request->input('type') ?: 'system';
        $target = $request->input('target') ?: 'self';
        $actionUrl = $request->input('action_url') ?: '/admin/inventory';
        $targetToken = $request->input('target_token');
        $clientToken = $request->input('client_token');

        if (empty($user->fcm_token) && !empty($clientToken)) {
            $user->update(['fcm_token' => $clientToken]);
        }

        $icon = match($type) {
            'stock'    => 'fa-solid fa-triangle-exclamation text-amber-500',
            'transfer' => 'fa-solid fa-arrow-right-arrow-left text-cyan-500',
            'issue'    => 'fa-solid fa-circle-exclamation text-rose-500',
            'order'    => 'fa-solid fa-cart-shopping text-emerald-500',
            default    => 'fa-solid fa-satellite-dish text-sky-500',
        };

        $fcm = \App\Services\FcmService::getInstance();

        if ($target === 'token' && !empty($targetToken)) {
            $result = $fcm->sendToToken($targetToken, $title, $message, [
                'type'       => $type,
                'action_url' => $actionUrl,
                'icon'       => $icon,
            ]);
        } elseif ($target === 'all_admins') {
            $result = $fcm->sendToAdmins($title, $message, [
                'type'       => $type,
                'action_url' => $actionUrl,
                'icon'       => $icon,
            ]);
        } else {
            $result = $fcm->sendToUser($user, $title, $message, [
                'type'       => $type,
                'action_url' => $actionUrl,
                'icon'       => $icon,
            ]);
        }

        // Also record it into database notifications so it appears in the dropdown
        $user->notify(new \App\Notifications\AdminNotification([
            'title'      => $title,
            'message'    => $message,
            'type'       => $type,
            'icon'       => $icon,
            'action_url' => $actionUrl,
        ]));

        $cfg = $fcm->getConfig();

        return response()->json([
            'success'    => true,
            'message'    => 'Test notification dispatched successfully.',
            'title'      => $title,
            'body'       => $message,
            'type'       => $type,
            'icon'       => $icon,
            'action_url' => $actionUrl,
            'details'    => $result,
            'config'     => [
                'server_key_set' => !empty($cfg['server_key']),
                'project_id'     => $cfg['project_id'],
                'user_has_token' => !empty($user->fcm_token),
            ],
        ]);
    }
}


