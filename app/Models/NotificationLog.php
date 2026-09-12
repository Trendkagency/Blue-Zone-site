<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'notification_id',
        'user_id',
        'device_token',
        'provider',
        'status',
        'message_id',
        'error_message',
        'payload',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
        ];
    }

    /**
     * Get the user associated with this notification delivery log.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper to log a successful FCM push.
     */
    public static function logSuccess(
        ?string $notificationId,
        ?int $userId,
        ?string $token,
        string $provider,
        ?string $messageId,
        array $payload = []
    ): self {
        return self::create([
            'notification_id' => $notificationId,
            'user_id'         => $userId,
            'device_token'    => $token ? substr($token, 0, 50) . '...' : null,
            'provider'        => $provider,
            'status'          => 'sent',
            'message_id'      => $messageId,
            'payload'         => $payload,
        ]);
    }

    /**
     * Helper to log a failed delivery attempt.
     */
    public static function logFailure(
        ?string $notificationId,
        ?int $userId,
        ?string $token,
        string $provider,
        string $error,
        array $payload = []
    ): self {
        return self::create([
            'notification_id' => $notificationId,
            'user_id'         => $userId,
            'device_token'    => $token ? substr($token, 0, 50) . '...' : null,
            'provider'        => $provider,
            'status'          => 'failed',
            'error_message'   => $error,
            'payload'         => $payload,
        ]);
    }

    /**
     * Helper to log an invalid / unregistered token response.
     */
    public static function logInvalidToken(
        ?string $notificationId,
        ?int $userId,
        ?string $token,
        string $provider,
        string $error,
        array $payload = []
    ): self {
        return self::create([
            'notification_id' => $notificationId,
            'user_id'         => $userId,
            'device_token'    => $token ? substr($token, 0, 50) . '...' : null,
            'provider'        => $provider,
            'status'          => 'invalid_token',
            'error_message'   => $error,
            'payload'         => $payload,
        ]);
    }
}
