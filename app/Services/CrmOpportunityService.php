<?php

namespace App\Services;

use App\Models\CrmActivity;
use App\Models\CrmCampaign;
use App\Models\CrmLead;
use App\Models\CrmNote;
use App\Models\CrmOpportunity;
use App\Models\CrmPipeline;
use App\Models\CrmPipelineStage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CrmOpportunityService
{
    /**
     * Generate sequential opportunity number: OPP-YYYY-XXXXX
     */
    public function generateOpportunityNumber(): string
    {
        $year = date('Y');
        $latest = CrmOpportunity::withTrashed()
            ->where('opportunity_number', 'like', "OPP-{$year}-%")
            ->orderBy('id', 'desc')
            ->value('opportunity_number');

        if ($latest && preg_match("/OPP-{$year}-(\d+)/", $latest, $matches)) {
            $nextSeq = (int) $matches[1] + 1;
        } else {
            $nextSeq = 1;
        }

        return sprintf('OPP-%s-%05d', $year, $nextSeq);
    }

    /**
     * Create Opportunity.
     */
    public function createOpportunity(array $data, int $userId): CrmOpportunity
    {
        return DB::transaction(function () use ($data, $userId) {
            $stage = CrmPipelineStage::findOrFail($data['stage_id']);

            $status = 'open';
            $wonAt = null;
            $lostAt = null;

            if ($stage->is_won) {
                $status = 'won';
                $wonAt = now();
            } elseif ($stage->is_lost) {
                $status = 'lost';
                $lostAt = now();
            }

            $opportunity = CrmOpportunity::create([
                'opportunity_number' => $this->generateOpportunityNumber(),
                'lead_id' => $data['lead_id'] ?? null,
                'customer_id' => $data['customer_id'] ?? null,
                'company_id' => $data['company_id'] ?? null,
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'pipeline_id' => $data['pipeline_id'],
                'stage_id' => $stage->id,
                'owner_id' => $data['owner_id'] ?? $userId,
                'source_id' => $data['source_id'] ?? null,
                'campaign_id' => $data['campaign_id'] ?? null,
                'value' => $data['value'] ?? 0.00,
                'currency' => $data['currency'] ?? \App\Services\CurrencyService::code(),
                'probability' => $data['probability'] ?? $stage->probability,
                'expected_close_date' => $data['expected_close_date'] ?? null,
                'status' => $status,
                'won_at' => $wonAt,
                'lost_at' => $lostAt,
                'last_activity_at' => now(),
            ]);

            // If won immediately and linked to a campaign, attribute revenue
            if ($status === 'won' && $opportunity->campaign_id) {
                CrmCampaign::where('id', $opportunity->campaign_id)->increment('revenue_generated', $opportunity->value);
            }

            Log::info("CRM: Opportunity {$opportunity->opportunity_number} created by user {$userId}");

            return $opportunity;
        });
    }

    /**
     * Move opportunity to new stage with validation, audit, and campaign attribution.
     */
    public function updateStage(CrmOpportunity $opportunity, int $newStageId, int $userId, ?string $reason = null): CrmOpportunity
    {
        return DB::transaction(function () use ($opportunity, $newStageId, $userId, $reason) {
            $newStage = CrmPipelineStage::where('pipeline_id', $opportunity->pipeline_id)
                ->where('id', $newStageId)
                ->firstOrFail();

            $oldStageName = $opportunity->stage?->name ?? 'Unknown Stage';
            $previousStatus = $opportunity->status;

            $status = 'open';
            $wonAt = $opportunity->won_at;
            $lostAt = $opportunity->lost_at;
            $lostReason = $opportunity->lost_reason;

            if ($newStage->is_won) {
                $status = 'won';
                $wonAt = now();
                $lostAt = null;
                $lostReason = null;

                // Attribute revenue to campaign if transitioned into Won
                if ($previousStatus !== 'won' && $opportunity->campaign_id) {
                    CrmCampaign::where('id', $opportunity->campaign_id)->increment('revenue_generated', $opportunity->value);
                }
            } elseif ($newStage->is_lost) {
                $status = 'lost';
                $lostAt = now();
                $lostReason = $reason ?? 'Lost during stage progression';

                // Reverse revenue attribution if moved from won to lost
                if ($previousStatus === 'won' && $opportunity->campaign_id) {
                    CrmCampaign::where('id', $opportunity->campaign_id)->decrement('revenue_generated', min($opportunity->value, CrmCampaign::find($opportunity->campaign_id)->revenue_generated));
                }
            } else {
                $status = 'open';
                $wonAt = null;
                $lostAt = null;
                $lostReason = null;
            }

            $opportunity->update([
                'stage_id' => $newStage->id,
                'probability' => $newStage->probability,
                'status' => $status,
                'won_at' => $wonAt,
                'lost_at' => $lostAt,
                'lost_reason' => $lostReason,
                'last_activity_at' => now(),
            ]);

            // Synchronize status with linked Lead if opportunity originated from a lead
            if ($opportunity->lead_id) {
                try {
                    $leadStatus = match ($status) {
                        'won' => 'converted',
                        'lost' => 'lost',
                        default => (in_array($newStage->slug, ['contacted-qualified', 'contacted_qualified']) ? 'contacted' : 'qualified')
                    };
                    CrmLead::where('id', $opportunity->lead_id)->update([
                        'status' => $leadStatus,
                        'stage' => $newStage->slug,
                        'lost_reason' => $status === 'lost' ? $lostReason : null,
                    ]);
                } catch (\Throwable $e) {
                    Log::warning("CRM: Failed to sync lead status for opportunity {$opportunity->id}: " . $e->getMessage());
                }
            }

            // Add timeline note
            CrmNote::create([
                'user_id' => $userId,
                'opportunity_id' => $opportunity->id,
                'customer_id' => $opportunity->customer_id,
                'body' => "Stage changed from '{$oldStageName}' to '{$newStage->name}'." . ($reason ? " Reason: {$reason}" : ""),
                'is_private' => false,
            ]);

            Log::info("CRM: Opportunity {$opportunity->opportunity_number} moved to stage {$newStage->slug}");

            return $opportunity->fresh(['stage', 'pipeline', 'owner', 'customer']);
        });
    }

    /**
     * Ensure all unlinked leads are automatically represented on the Kanban board under stage 1 (New Lead).
     */
    public function syncUnlinkedLeadsToPipeline(CrmPipeline $pipeline): void
    {
        try {
            $firstStage = $pipeline->stages()->where('is_active', true)->orderBy('sort_order')->first();
            if (!$firstStage) {
                return;
            }

            $existingLeadIds = CrmOpportunity::whereNotNull('lead_id')->pluck('lead_id')->toArray();
            $unlinkedLeads = CrmLead::whereNotIn('id', $existingLeadIds)
                ->whereNotIn('status', ['lost', 'unqualified'])
                ->get();

            foreach ($unlinkedLeads as $lead) {
                $this->createOpportunity([
                    'name' => $lead->full_name . (!empty($lead->company_name) ? " — {$lead->company_name}" : ' — Longevity Consultation'),
                    'pipeline_id' => $pipeline->id,
                    'stage_id' => $firstStage->id,
                    'lead_id' => $lead->id,
                    'customer_id' => $lead->customer_id,
                    'company_id' => $lead->company_id,
                    'owner_id' => $lead->owner_id ?? \App\Models\User::value('id'),
                    'source_id' => $lead->source_id,
                    'campaign_id' => $lead->campaign_id,
                    'value' => (float) ($lead->estimated_value ?? 0),
                    'currency' => $lead->currency ?? \App\Services\CurrencyService::code(),
                    'probability' => $firstStage->probability ?? 10,
                    'expected_close_date' => $lead->next_follow_up_at ? \Carbon\Carbon::parse($lead->next_follow_up_at)->addDays(14) : now()->addDays(14),
                    'description' => $lead->notes ?? ('Pipeline Opportunity for lead ' . $lead->lead_number),
                ], $lead->owner_id ?? \App\Models\User::value('id') ?? 0);
            }
        } catch (\Throwable $e) {
            Log::warning("CRM: Could not sync unlinked leads to pipeline {$pipeline->id}: " . $e->getMessage());
        }
    }

    /**
     * Get optimized Kanban Board structure for a pipeline.
     */
    public function getKanbanBoard(int $pipelineId): array
    {
        $pipeline = CrmPipeline::with(['stages' => function ($q) {
            $q->where('is_active', true)->orderBy('sort_order');
        }])->findOrFail($pipelineId);

        // Auto-sync any leads that don't have an opportunity card yet into the initial stage
        $this->syncUnlinkedLeadsToPipeline($pipeline);

        $stagesData = [];
        $totalPipelineValue = 0;
        $totalWeightedValue = 0;

        foreach ($pipeline->stages as $stage) {
            $opportunities = CrmOpportunity::where('pipeline_id', $pipelineId)
                ->where('stage_id', $stage->id)
                ->with(['owner:id,name,avatar', 'customer:id,name,phone,email', 'company:id,name', 'lead:id,full_name,phone,email'])
                ->orderBy('created_at', 'desc')
                ->get();

            $stageValue = (float) $opportunities->sum('value');
            $stageWeighted = (float) $opportunities->sum('weighted_value');

            $totalPipelineValue += $stageValue;
            $totalWeightedValue += $stageWeighted;

            $stagesData[] = [
                'stage' => $stage,
                'opportunities' => $opportunities,
                'count' => $opportunities->count(),
                'total_value' => $stageValue,
                'weighted_value' => $stageWeighted,
            ];
        }

        return [
            'pipeline' => $pipeline,
            'stages' => $stagesData,
            'total_value' => $totalPipelineValue,
            'total_weighted' => $totalWeightedValue,
        ];
    }
}
