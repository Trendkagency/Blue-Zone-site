<?php

namespace App\Services\Hr;

use App\Jobs\SendFcmPushJob;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HrNotificationService
{
    private static ?self $instance = null;

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Send notification to an employee (database notification + FCM push if device active).
     */
    public function sendEmployeeNotification(Employee $employee, string $title, string $message, array $data = []): void
    {
        if (!$employee->user_id) {
            return;
        }

        $user = $employee->user;
        if (!$user) {
            return;
        }

        $this->sendUserNotification($user, $title, $message, $data);
    }

    /**
     * Send notification to all HR administrators and super admins.
     */
    public function notifyHrAdmins(string $title, string $message, array $data = []): void
    {
        $admins = User::whereHas('role', function ($q) {
            $q->whereIn('name', ['admin', 'super_admin', 'super admin', 'Super Admin', 'HR Manager']);
        })->get();

        foreach ($admins as $admin) {
            $this->sendUserNotification($admin, $title, $message, $data);
        }
    }

    /**
     * Internal helper to create DB notification record and queue FCM push job.
     */
    public function sendUserNotification(User $user, string $title, string $message, array $data = []): void
    {
        $notificationId = (string) Str::uuid();

        // 1. Create Laravel database notification record
        DB::table('notifications')->insert([
            'id' => $notificationId,
            'type' => 'App\\Notifications\\HrNotification',
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'data' => json_encode(array_merge([
                'title' => $title,
                'message' => $message,
                'type' => $data['type'] ?? 'hr',
                'action_url' => $data['action_url'] ?? null,
                'icon' => $data['icon'] ?? 'fa-solid fa-user-tie text-teal-400',
            ], $data)),
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Queue FCM push jobs for active user devices
        $tokens = $user->getActiveFcmTokens();
        foreach ($tokens as $token) {
            SendFcmPushJob::dispatch($token, $title, $message, $data, $user->id, $notificationId);
        }
    }
}
