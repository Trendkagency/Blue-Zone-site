<?php

namespace App\Services;

use App\Models\CrmActivity;
use App\Models\CrmNote;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CrmActivityService
{
    /**
     * Create an activity/task.
     */
    public function createActivity(array $data, int $userId): CrmActivity
    {
        return DB::transaction(function () use ($data, $userId) {
            $activity = CrmActivity::create([
                'activity_type' => $data['activity_type'] ?? 'task',
                'subject' => $data['subject'],
                'description' => $data['description'] ?? null,
                'lead_id' => $data['lead_id'] ?? null,
                'customer_id' => $data['customer_id'] ?? null,
                'company_id' => $data['company_id'] ?? null,
                'opportunity_id' => $data['opportunity_id'] ?? null,
                'assigned_to' => $data['assigned_to'] ?? $userId,
                'created_by' => $userId,
                'scheduled_at' => $data['scheduled_at'] ?? null,
                'due_at' => $data['due_at'] ?? now()->addDay(),
                'status' => $data['status'] ?? 'pending',
                'priority' => $data['priority'] ?? 'normal',
                'metadata' => $data['metadata'] ?? [],
            ]);

            Log::info("CRM: Created activity #{$activity->id} [{$activity->activity_type}] by user {$userId}");
            return $activity;
        });
    }

    /**
     * Mark activity as completed.
     */
    public function completeActivity(CrmActivity $activity, ?string $outcome, int $userId): CrmActivity
    {
        return DB::transaction(function () use ($activity, $outcome, $userId) {
            $activity->update([
                'status' => 'completed',
                'completed_at' => now(),
                'outcome' => $outcome,
            ]);

            // Add note to record outcome
            if ($outcome) {
                CrmNote::create([
                    'user_id' => $userId,
                    'lead_id' => $activity->lead_id,
                    'customer_id' => $activity->customer_id,
                    'opportunity_id' => $activity->opportunity_id,
                    'company_id' => $activity->company_id,
                    'body' => "Activity [{$activity->subject}] completed. Outcome: {$outcome}",
                    'is_private' => false,
                ]);
            }

            Log::info("CRM: Activity #{$activity->id} marked completed by user {$userId}");
            return $activity;
        });
    }

    /**
     * Cancel an activity.
     */
    public function cancelActivity(CrmActivity $activity, ?string $reason, int $userId): CrmActivity
    {
        $activity->update([
            'status' => 'cancelled',
            'outcome' => $reason,
        ]);

        Log::info("CRM: Activity #{$activity->id} cancelled by user {$userId}");
        return $activity;
    }

    /**
     * Get overdue activities.
     */
    public function getOverdueActivities(?int $userId = null)
    {
        $query = CrmActivity::overdue()
            ->with(['assignee:id,name,avatar', 'customer:id,name,phone', 'lead:id,full_name,phone', 'opportunity:id,name'])
            ->orderBy('due_at', 'asc');

        if ($userId) {
            $query->where('assigned_to', $userId);
        }

        return $query->get();
    }

    /**
     * Get tasks due today.
     */
    public function getTodayTasks(int $userId)
    {
        return CrmActivity::dueToday()
            ->where('assigned_to', $userId)
            ->with(['customer:id,name,phone', 'lead:id,full_name,phone', 'opportunity:id,name'])
            ->orderBy('due_at', 'asc')
            ->get();
    }
}
