<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\UserDevice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class SendBulkFcmPushJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $title;
    public string $body;
    public array $data;
    public ?string $targetType; // 'admins', 'users', 'roles'
    public array $targetIds;
    public ?string $notificationId;

    /**
     * Create a new job instance.
     *
     * @param string $title
     * @param string $body
     * @param array $data
     * @param string|null $targetType ('admins' by default)
     * @param array $targetIds Specific user IDs or role names if targetType is 'users' or 'roles'
     * @param string|null $notificationId
     */
    public function __construct(
        string $title,
        string $body,
        array $data = [],
        ?string $targetType = 'admins',
        array $targetIds = [],
        ?string $notificationId = null
    ) {
        $this->title = $title;
        $this->body = $body;
        $this->data = $data;
        $this->targetType = $targetType ?: 'admins';
        $this->targetIds = $targetIds;
        $this->notificationId = $notificationId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $users = $this->resolveRecipients();

        if ($users->isEmpty()) {
            Log::info("SendBulkFcmPushJob: No eligible recipients found for target type '{$this->targetType}'.");
            return;
        }

        $dispatchedTokens = [];
        $jobCount = 0;

        foreach ($users as $user) {
            /** @var User $user */
            $tokens = $user->getActiveFcmTokens();

            foreach ($tokens as $token) {
                // Prevent sending duplicate push to the exact same device token in one bulk run
                if (in_array($token, $dispatchedTokens, true)) {
                    continue;
                }

                $dispatchedTokens[] = $token;
                SendFcmPushJob::dispatch(
                    $token,
                    $this->title,
                    $this->body,
                    $this->data,
                    $user->id,
                    $this->notificationId
                );
                $jobCount++;
            }
        }

        Log::info("SendBulkFcmPushJob: Dispatched {$jobCount} FCM push jobs across " . count($dispatchedTokens) . " unique devices for target '{$this->targetType}'. Title: [{$this->title}]");
    }

    /**
     * Resolve eligible recipients dynamically without hardcoded IDs.
     */
    protected function resolveRecipients(): Collection
    {
        if ($this->targetType === 'users' && !empty($this->targetIds)) {
            return User::whereIn('id', $this->targetIds)->get();
        }

        if ($this->targetType === 'roles' && !empty($this->targetIds)) {
            return User::whereHas('role', function ($q) {
                $q->whereIn('name', $this->targetIds);
            })->get();
        }

        // Default: 'admins' (Super Admin, Admin, Manager, Inventory Staff, Sales Staff)
        return User::whereHas('role', function ($q) {
            $q->whereIn('name', [
                'Super Admin',
                'super_admin',
                'Admin',
                'admin',
                'Manager',
                'Inventory Staff',
                'Sales Staff',
            ]);
        })->get();
    }
}
