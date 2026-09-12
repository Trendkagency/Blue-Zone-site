<?php

namespace App\Jobs;

use App\Models\NotificationLog;
use App\Models\User;
use App\Models\UserDevice;
use App\Services\FcmService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendFcmPushJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 15;
    public array $backoff = [5, 15, 30];

    public string $token;
    public string $title;
    public string $body;
    public array $data;
    public ?int $userId;
    public ?string $notificationId;

    /**
     * Create a new job instance.
     */
    public function __construct(
        string $token,
        string $title,
        string $body,
        array $data = [],
        ?int $userId = null,
        ?string $notificationId = null
    ) {
        $this->token = $token;
        $this->title = $title;
        $this->body = $body;
        $this->data = $data;
        $this->userId = $userId;
        $this->notificationId = $notificationId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $fcm = FcmService::getInstance();
        $result = $fcm->sendPush($this->token, $this->title, $this->body, $this->data);

        $provider = $result['provider'] ?? 'fcm';
        $messageId = $result['message_id'] ?? null;

        if (!empty($result['success'])) {
            NotificationLog::logSuccess(
                $this->notificationId,
                $this->userId,
                $this->token,
                $provider,
                $messageId,
                $this->data
            );
            return;
        }

        // Handle Token Invalidation: deactive token so we don't spam an unregistered device
        if (!empty($result['invalid_token'])) {
            Log::warning("SendFcmPushJob: Token is invalid/unregistered. Deactivating token reference.");
            UserDevice::where('token', $this->token)->update(['is_active' => false]);

            if ($this->userId) {
                User::where('id', $this->userId)
                    ->where('fcm_token', $this->token)
                    ->update(['fcm_token' => null]);
            }

            NotificationLog::logInvalidToken(
                $this->notificationId,
                $this->userId,
                $this->token,
                $provider,
                $result['error'] ?? 'Token unregistered',
                $this->data
            );
            return;
        }

        // Transient failure
        $error = $result['error'] ?? 'Unknown FCM error';
        NotificationLog::logFailure(
            $this->notificationId,
            $this->userId,
            $this->token,
            $provider,
            $error,
            $this->data
        );

        // If it's the last attempt or permanent, fail cleanly without breaking the application
        if ($this->attempts() >= $this->tries) {
            Log::error("SendFcmPushJob: Exhausted {$this->tries} attempts to send push to token. Error: {$error}");
        } else {
            // Re-throw to trigger Laravel queue retry with backoff
            throw new \RuntimeException("FCM Delivery Temporary Failure: {$error}");
        }
    }
}
