<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\User;
use App\Models\UserDevice;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Singleton Service for Firebase Cloud Messaging (FCM).
 *
 * Implements the Singleton design pattern to maintain a single source
 * of FCM configuration, authentication state, and dispatch logic across the application.
 *
 * Supports both:
 * 1. FCM HTTP v1 API (OAuth2 RS256 Bearer Token from Service Account)
 * 2. Legacy FCM Server Key API (Authorization: key=SERVER_KEY)
 * 3. Graceful Mock / Development Mode with zero production crashes
 */
class FcmService
{
    private static ?FcmService $instance = null;

    protected array $config;
    protected ?string $serverKey;
    protected string $projectId;
    protected ?string $serviceAccountPath;
    protected ?string $serviceAccountJson;

    /**
     * Protected constructor to enforce singleton pattern.
     */
    protected function __construct()
    {
        $this->refreshConfig();
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
     * Reload active configuration dynamically.
     */
    public function refreshConfig(): void
    {
        $dbServerKey = Setting::get('fcm_server_key');
        $dbProjectId = Setting::get('fcm_project_id');
        $dbApiKey = Setting::get('fcm_api_key');
        $dbSenderId = Setting::get('fcm_messaging_sender_id');
        $dbAppId = Setting::get('fcm_app_id');
        $dbVapidKey = Setting::get('fcm_vapid_key');
        $dbServiceAccountJson = Setting::get('fcm_service_account_json');

        $this->serverKey = $dbServerKey ?: config('fcm.server_key') ?: config('services.firebase.server_key');
        $this->projectId = $dbProjectId ?: config('fcm.project_id') ?: config('services.firebase.project_id', 'bluezone-998e6');
        $this->serviceAccountPath = config('services.firebase.service_account_path');
        $this->serviceAccountJson = $dbServiceAccountJson ?: null;

        $this->config = [
            'server_key'             => $this->serverKey,
            'project_id'             => $this->projectId,
            'api_key'                => $dbApiKey ?: config('fcm.api_key') ?: config('services.firebase.api_key', ''),
            'messaging_sender_id'    => $dbSenderId ?: config('fcm.messaging_sender_id') ?: config('services.firebase.messaging_sender_id', ''),
            'app_id'                 => $dbAppId ?: config('fcm.app_id') ?: config('services.firebase.app_id', ''),
            'vapid_key'              => $dbVapidKey ?: config('fcm.vapid_key') ?: config('services.firebase.vapid_key', ''),
            'service_account_path'   => $this->serviceAccountPath,
            'has_service_account'    => !empty($this->serviceAccountJson) || (!empty($this->serviceAccountPath) && file_exists($this->serviceAccountPath)),
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
     * Get client configuration parameters for frontend initialization.
     */
    public function getClientConfig(): array
    {
        return [
            'apiKey'            => $this->config['api_key'] ?? '',
            'authDomain'        => Setting::get('fcm_auth_domain') ?: config('fcm.auth_domain', "{$this->projectId}.firebaseapp.com"),
            'projectId'         => $this->projectId,
            'storageBucket'     => Setting::get('fcm_storage_bucket') ?: config('fcm.storage_bucket', "{$this->projectId}.firebasestorage.app"),
            'messagingSenderId' => $this->config['messaging_sender_id'] ?? '',
            'appId'             => $this->config['app_id'] ?? '',
            'vapidKey'          => $this->config['vapid_key'] ?? '',
        ];
    }

    /**
     * Send real-time FCM notification to a specific device token (alias).
     */
    public function sendToToken(string $token, string $title, string $body, array $data = []): array
    {
        return $this->sendPush($token, $title, $body, $data);
    }

    /**
     * Send real-time FCM notification to all active devices of a user.
     */
    public function sendToUser(User $user, string $title, string $body, array $data = []): array
    {
        $tokens = $user->getActiveFcmTokens();

        if (empty($tokens)) {
            Log::info("FCM: User #{$user->id} ({$user->email}) has no registered FCM device tokens. Skipping push.");
            return ['success' => false, 'reason' => 'no_token'];
        }

        $results = [];
        foreach ($tokens as $token) {
            $results[$token] = $this->sendPush($token, $title, $body, $data);
        }

        return [
            'success' => true,
            'count'   => count($results),
            'details' => $results,
        ];
    }

    /**
     * Send real-time FCM notification to all administrative operators dynamically.
     */
    public function sendToAdmins(string $title, string $body, array $data = []): array
    {
        // Dynamic targeting: Query users who have admin/staff roles or permissions
        $adminUsers = User::whereHas('role', function ($query) {
            $query->whereIn('name', [
                'Super Admin',
                'super_admin',
                'Admin',
                'admin',
                'Manager',
                'Inventory Staff',
                'Sales Staff',
            ]);
        })->get();

        $results = [];
        $dispatchedTokens = [];

        foreach ($adminUsers as $admin) {
            /** @var User $admin */
            $tokens = $admin->getActiveFcmTokens();
            foreach ($tokens as $token) {
                if (in_array($token, $dispatchedTokens, true)) {
                    continue;
                }
                $dispatchedTokens[] = $token;
                $results[$admin->id][$token] = $this->sendPush($token, $title, $body, $data);
            }
        }

        Log::info("FCM: Realtime alert dispatched to " . count($dispatchedTokens) . " active admin devices across {$adminUsers->count()} administrators. Title: [{$title}]");

        return [
            'dispatched_count' => count($dispatchedTokens),
            'details'          => $results,
        ];
    }

    /**
     * Send FCM push payload to a specific device token.
     * Automatically chooses HTTP v1, Legacy API, or Mock mode.
     */
    public function sendPush(string $token, string $title, string $body, array $data = []): array
    {
        $data['timestamp'] = (string) now()->timestamp;
        $data['click_action'] = $data['action_url'] ?? '/admin';
        $stringData = array_map(fn($v) => is_scalar($v) ? (string) $v : json_encode($v), $data);

        // 1. Try FCM HTTP v1 API if OAuth2 credentials / Service Account is available
        $oauthToken = $this->getHttpV1AccessToken();
        if ($oauthToken) {
            return $this->dispatchHttpV1($token, $title, $body, $stringData, $oauthToken);
        }

        // 2. Try Legacy Server Key API if serverKey is present
        if (!empty($this->serverKey)) {
            return $this->dispatchLegacy($token, $title, $body, $stringData);
        }

        // 3. Fallback: Development / Mock Mode
        Log::info("FCM [DEV/MOCK]: Push simulated for token [" . substr($token, 0, 12) . "...] Title: '{$title}' Body: '{$body}'");
        return [
            'success'    => true,
            'mock'       => true,
            'provider'   => 'mock',
            'message_id' => 'mock-fcm-' . uniqid(),
        ];
    }

    /**
     * Dispatch push via Firebase Cloud Messaging HTTP v1 API.
     */
    protected function dispatchHttpV1(string $token, string $title, string $body, array $stringData, string $oauthToken): array
    {
        $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

        $payload = [
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => $title,
                    'body'  => $body,
                ],
                'data' => $stringData,
                'webpush' => [
                    'notification' => [
                        'icon'  => '/assets/logo/logo-dark.png',
                        'badge' => '/favicon.ico',
                    ],
                    'fcm_options' => [
                        'link' => $stringData['action_url'] ?? '/admin',
                    ],
                ],
            ],
        ];

        try {
            $response = Http::withToken($oauthToken)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->timeout(10)
                ->post($url, $payload);

            if ($response->successful()) {
                $name = $response->json('name') ?? 'fcm-v1-' . uniqid();
                Log::info("FCM HTTP v1: Dispatched successfully to token " . substr($token, 0, 12) . "... Message: {$name}");
                return [
                    'success'    => true,
                    'provider'   => 'fcm_v1',
                    'message_id' => $name,
                ];
            }

            $errorBody = $response->body();
            $isInvalid = $this->isInvalidTokenResponse($response->status(), $errorBody);

            if ($isInvalid) {
                $this->deactivateInvalidToken($token);
            }

            Log::warning("FCM HTTP v1 Error ({$response->status()}): {$errorBody}");
            return [
                'success'       => false,
                'provider'      => 'fcm_v1',
                'invalid_token' => $isInvalid,
                'error'         => $errorBody,
            ];
        } catch (\Throwable $e) {
            Log::error("FCM HTTP v1 Exception: " . $e->getMessage());
            return [
                'success'  => false,
                'provider' => 'fcm_v1',
                'error'    => $e->getMessage(),
            ];
        }
    }

    /**
     * Dispatch push via Firebase Cloud Messaging Legacy Server Key API.
     */
    protected function dispatchLegacy(string $token, string $title, string $body, array $stringData): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $this->serverKey,
                'Content-Type'  => 'application/json',
            ])->timeout(10)->post('https://fcm.googleapis.com/fcm/send', [
                'to' => $token,
                'notification' => [
                    'title' => $title,
                    'body'  => $body,
                    'icon'  => '/assets/logo/logo-dark.png',
                    'sound' => 'default',
                ],
                'data' => $stringData,
                'priority' => 'high',
            ]);

            if ($response->successful()) {
                $json = $response->json();
                $failure = $json['failure'] ?? 0;
                $errorMsg = $json['results'][0]['error'] ?? null;

                if ($failure > 0 && $errorMsg) {
                    $isInvalid = in_array($errorMsg, ['NotRegistered', 'InvalidRegistration', 'MismatchSenderId'], true);
                    if ($isInvalid) {
                        $this->deactivateInvalidToken($token);
                    }
                    return [
                        'success'       => false,
                        'provider'      => 'fcm_legacy',
                        'invalid_token' => $isInvalid,
                        'error'         => $errorMsg,
                    ];
                }

                $messageId = $json['results'][0]['message_id'] ?? 'fcm-legacy-' . uniqid();
                Log::info("FCM Legacy: Dispatched successfully to token " . substr($token, 0, 12) . "...");
                return [
                    'success'    => true,
                    'provider'   => 'fcm_legacy',
                    'message_id' => $messageId,
                ];
            }

            $errorBody = $response->body();
            $isInvalid = $this->isInvalidTokenResponse($response->status(), $errorBody);
            if ($isInvalid) {
                $this->deactivateInvalidToken($token);
            }

            Log::warning("FCM Legacy Error ({$response->status()}): {$errorBody}");
            return [
                'success'       => false,
                'provider'      => 'fcm_legacy',
                'invalid_token' => $isInvalid,
                'error'         => $errorBody,
            ];
        } catch (\Throwable $e) {
            Log::error("FCM Legacy Exception: " . $e->getMessage());
            return [
                'success'  => false,
                'provider' => 'fcm_legacy',
                'error'    => $e->getMessage(),
            ];
        }
    }

    /**
     * Determine if a response indicates that a token is permanently invalid or unregistered.
     */
    protected function isInvalidTokenResponse(int $status, string $responseBody): bool
    {
        $lower = strtolower($responseBody);
        return $status === 404
            || str_contains($lower, 'unregistered')
            || str_contains($lower, 'not_found')
            || str_contains($lower, 'invalid_argument')
            || str_contains($lower, 'registration-token-not-registered')
            || str_contains($lower, 'notregistered')
            || str_contains($lower, 'invalidregistration');
    }

    /**
     * Automatically deactivate an invalid device token in the database.
     */
    protected function deactivateInvalidToken(string $token): void
    {
        UserDevice::where('token', $token)->update(['is_active' => false]);
        User::where('fcm_token', $token)->update(['fcm_token' => null]);
        Log::info("FCM: Deactivated permanently invalid token [" . substr($token, 0, 12) . "...]");
    }

    /**
     * Resolve or generate a cached OAuth2 access token for FCM HTTP v1.
     * Generates a signed RS256 JWT using PHP native OpenSSL (Zero external dependencies).
     */
    public function getHttpV1AccessToken(): ?string
    {
        return Cache::remember('fcm_http_v1_access_token', now()->addMinutes(50), function () {
            $serviceAccount = $this->loadServiceAccountData();
            if (!$serviceAccount) {
                return null;
            }

            $clientEmail = $serviceAccount['client_email'] ?? null;
            $privateKey = $serviceAccount['private_key'] ?? null;

            if (!$clientEmail || !$privateKey) {
                return null;
            }

            $now = time();
            $header = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
            $claim = json_encode([
                'iss'   => $clientEmail,
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud'   => 'https://oauth2.googleapis.com/token',
                'exp'   => $now + 3600,
                'iat'   => $now,
            ]);

            $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
            $base64UrlClaim = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($claim));
            $signatureInput = $base64UrlHeader . '.' . $base64UrlClaim;

            $signature = '';
            $keyResource = openssl_pkey_get_private($privateKey);
            if (!$keyResource) {
                Log::warning("FCM HTTP v1: Unable to parse private key from service account credentials.");
                return null;
            }

            if (!openssl_sign($signatureInput, $signature, $keyResource, OPENSSL_ALGO_SHA256)) {
                Log::warning("FCM HTTP v1: OpenSSL failed to sign assertion JWT.");
                return null;
            }

            $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
            $assertion = $signatureInput . '.' . $base64UrlSignature;

            try {
                $response = Http::asForm()->timeout(10)->post('https://oauth2.googleapis.com/token', [
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion'  => $assertion,
                ]);

                if ($response->successful()) {
                    return $response->json('access_token');
                }

                Log::warning("FCM OAuth2 token generation failed: " . $response->body());
                return null;
            } catch (\Throwable $e) {
                Log::warning("FCM OAuth2 request exception: " . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Load service account array from JSON setting or file path.
     */
    protected function loadServiceAccountData(): ?array
    {
        if (!empty($this->serviceAccountJson)) {
            $decoded = json_decode($this->serviceAccountJson, true);
            if (is_array($decoded) && !empty($decoded['client_email']) && !empty($decoded['private_key'])) {
                return $decoded;
            }
        }

        if (!empty($this->serviceAccountPath) && file_exists($this->serviceAccountPath)) {
            $contents = file_get_contents($this->serviceAccountPath);
            $decoded = json_decode($contents, true);
            if (is_array($decoded) && !empty($decoded['client_email']) && !empty($decoded['private_key'])) {
                return $decoded;
            }
        }

        return null;
    }
}
