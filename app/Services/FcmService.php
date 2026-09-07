<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Singleton Service for Firebase Cloud Messaging (FCM).
 *
 * Implements the Singleton design pattern to maintain a single source
 * of FCM configuration, connection state, and dispatch logic across the application.
 */
class FcmService
{
    private static ?FcmService $instance = null;

    protected array $config;
    protected ?string $serverKey;
    protected string $projectId;

    /**
     * Protected constructor to enforce singleton pattern.
     */
    protected function __construct()
    {
        $this->refreshConfig();
    }

    /**
     * Reload active configuration dynamically.
     */
    public function refreshConfig(): void
    {
        $dbServerKey = \App\Models\Setting::get('fcm_server_key');
        $dbProjectId = \App\Models\Setting::get('fcm_project_id');
        $dbApiKey = \App\Models\Setting::get('fcm_api_key');
        $dbSenderId = \App\Models\Setting::get('fcm_messaging_sender_id');
        $dbAppId = \App\Models\Setting::get('fcm_app_id');
        $dbVapidKey = \App\Models\Setting::get('fcm_vapid_key');

        $this->serverKey = $dbServerKey ?: config('fcm.server_key') ?: config('services.firebase.server_key');
        $this->projectId = $dbProjectId ?: config('fcm.project_id') ?: config('services.firebase.project_id', 'blue-zone-site');

        $this->config = [
            'server_key'          => $this->serverKey,
            'project_id'          => $this->projectId,
            'api_key'             => $dbApiKey ?: config('fcm.api_key') ?: config('services.firebase.api_key', ''),
            'messaging_sender_id' => $dbSenderId ?: config('fcm.messaging_sender_id') ?: config('services.firebase.messaging_sender_id', ''),
            'app_id'              => $dbAppId ?: config('fcm.app_id') ?: config('services.firebase.app_id', ''),
            'vapid_key'           => $dbVapidKey ?: config('fcm.vapid_key') ?: config('services.firebase.vapid_key', ''),
        ];
    }

    /**
     * Get active resolved FCM configuration.
     */
    public function getConfig(): array
    {
        $this->refreshConfig();
        return $this->config;
    }


    /**
     * Prevent cloning of singleton instance.
     */
    private function __clone() {}

    /**
     * Prevent unserializing of singleton instance.
     */
    public function __wakeup()
    {
        throw new \Exception('Cannot unserialize a singleton.');
    }

    /**
     * Get the globally shared singleton instance.
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Send real-time FCM notification to a specific user.
     */
    public function sendToUser(User $user, string $title, string $body, array $data = []): array
    {
        if (empty($user->fcm_token)) {
            Log::info("FCM: User #{$user->id} ({$user->email}) has no registered FCM device token. Skipping push.");
            return ['success' => false, 'reason' => 'no_token'];
        }

        return $this->sendPush($user->fcm_token, $title, $body, $data);
    }

    /**
     * Send real-time FCM notification to all administrative operators (Super Admin, Manager, Staff).
     */
    public function sendToAdmins(string $title, string $body, array $data = []): array
    {
        $adminUsers = User::whereHas('role', function ($query) {
            $query->whereIn('name', ['Super Admin', 'Admin', 'Manager', 'Inventory Staff']);
        })->orWhere('role_id', 1)->get();

        $results = [];
        foreach ($adminUsers as $admin) {
            if (!empty($admin->fcm_token)) {
                $results[$admin->id] = $this->sendPush($admin->fcm_token, $title, $body, $data);
            }
        }

        // Also record a centralized log of the real-time event
        Log::info("FCM: Realtime alert dispatched to " . count($results) . " active admin devices. Title: [{$title}]");

        return [
            'dispatched_count' => count($results),
            'details'          => $results,
        ];
    }

    /**
     * Send FCM push payload to a specific device token.
     */
    public function sendPush(string $token, string $title, string $body, array $data = []): array
    {
        // Add timestamp and relative action URL normalization to payload
        $data['timestamp'] = (string) now()->timestamp;
        $data['click_action'] = $data['action_url'] ?? '/admin/inventory';

        // Check if real FCM server key is provided
        if (!empty($this->serverKey)) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'key=' . $this->serverKey,
                    'Content-Type'  => 'application/json',
                ])->post('https://fcm.googleapis.com/fcm/send', [
                    'to' => $token,
                    'notification' => [
                        'title' => $title,
                        'body'  => $body,
                        'icon'  => '/assets/logo/logo-dark.png',
                        'sound' => 'default',
                    ],
                    'data' => $data,
                    'priority' => 'high',
                ]);

                if ($response->successful()) {
                    Log::info("FCM: Push dispatched successfully to token " . substr($token, 0, 12) . "...");
                    return [
                        'success'    => true,
                        'message_id' => $response->json('results.0.message_id') ?? 'fcm-' . uniqid(),
                    ];
                }

                Log::warning("FCM: Cloud messaging API returned error: " . $response->body());
                return [
                    'success' => false,
                    'error'   => $response->body(),
                ];
            } catch (\Throwable $e) {
                Log::error("FCM: Exception during push dispatch: " . $e->getMessage());
                return [
                    'success' => false,
                    'error'   => $e->getMessage(),
                ];
            }
        }

        // Development/Mock Mode: When credentials are not yet added to .env
        Log::info("FCM [DEV/MOCK]: Push simulated for token [" . substr($token, 0, 10) . "...] Title: '{$title}' Body: '{$body}' Payload: " . json_encode($data));

        return [
            'success'    => true,
            'mock'       => true,
            'message_id' => 'mock-fcm-' . uniqid(),
        ];
    }

    /**
     * Get client configuration parameters for frontend initialization.
     */
    public function getClientConfig(): array
    {
        return [
            'apiKey'            => $this->config['api_key'] ?? '',
            'authDomain'        => $this->config['auth_domain'] ?? '',
            'projectId'         => $this->projectId,
            'storageBucket'     => $this->config['storage_bucket'] ?? '',
            'messagingSenderId' => $this->config['messaging_sender_id'] ?? '',
            'appId'             => $this->config['app_id'] ?? '',
            'vapidKey'          => $this->config['vapid_key'] ?? '',
        ];
    }
}
