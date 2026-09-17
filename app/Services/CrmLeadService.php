<?php

namespace App\Services;

use App\Models\CrmActivity;
use App\Models\CrmCompany;
use App\Models\CrmLead;
use App\Models\CrmNote;
use App\Models\CrmOpportunity;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CrmLeadService
{
    /**
     * Generate unique sequential lead number: LD-YYYY-XXXXX
     */
    public function generateLeadNumber(): string
    {
        $year = date('Y');
        $latest = CrmLead::withTrashed()
            ->where('lead_number', 'like', "LD-{$year}-%")
            ->orderBy('id', 'desc')
            ->value('lead_number');

        if ($latest && preg_match("/LD-{$year}-(\d+)/", $latest, $matches)) {
            $nextSeq = (int) $matches[1] + 1;
        } else {
            $nextSeq = 1;
        }

        return sprintf('LD-%s-%05d', $year, $nextSeq);
    }

    /**
     * Normalize international phone numbers for clean duplicate detection.
     */
    public function normalizePhone(?string $phone): ?string
    {
        if (!$phone) {
            return null;
        }
        $normalized = preg_replace('/[^\d+]/', '', $phone);
        // Replace leading 00 with +
        if (str_starts_with($normalized, '00')) {
            $normalized = '+' . substr($normalized, 2);
        }
        return $normalized;
    }

    /**
     * Calculate transparent and explainable lead score.
     */
    public function calculateLeadScore(array $data): int
    {
        $score = 0;
        if (!empty($data['email'])) $score += 15;
        if (!empty($data['phone'])) $score += 20;
        if (!empty($data['company_name']) || !empty($data['company_id'])) $score += 15;
        if (!empty($data['job_title'])) $score += 10;
        if (!empty($data['estimated_value']) && (float) $data['estimated_value'] > 1000) $score += 20;
        if (!empty($data['source_id'])) $score += 10;
        if (!empty($data['campaign_id'])) $score += 10;
        return min(100, $score);
    }

    /**
     * Create a new Lead with idempotency and audit tracking.
     */
    public function createLead(array $data, ?int $creatorId = null): CrmLead
    {
        return DB::transaction(function () use ($data, $creatorId) {
            $fullName = trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''));
            if (empty($fullName)) {
                $fullName = $data['company_name'] ?? 'Anonymous Lead';
            }

            $normalizedPhone = $this->normalizePhone($data['phone'] ?? null);

            $lead = CrmLead::create([
                'lead_number' => $this->generateLeadNumber(),
                'customer_id' => $data['customer_id'] ?? null,
                'company_id' => $data['company_id'] ?? null,
                'first_name' => $data['first_name'] ?? 'Lead',
                'last_name' => $data['last_name'] ?? null,
                'full_name' => $fullName,
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'phone_normalized' => $normalizedPhone,
                'secondary_phone' => $data['secondary_phone'] ?? null,
                'country_id' => $data['country_id'] ?? null,
                'city_id' => $data['city_id'] ?? null,
                'job_title' => $data['job_title'] ?? null,
                'company_name' => $data['company_name'] ?? null,
                'source_id' => $data['source_id'] ?? null,
                'campaign_id' => $data['campaign_id'] ?? null,
                'status' => $data['status'] ?? 'new',
                'stage' => $data['stage'] ?? 'initial_outreach',
                'priority' => $data['priority'] ?? 'normal',
                'estimated_value' => $data['estimated_value'] ?? 0.00,
                'currency' => $data['currency'] ?? \App\Services\CurrencyService::code(),
                'owner_id' => $data['owner_id'] ?? $creatorId,
                'assigned_at' => !empty($data['owner_id']) ? now() : null,
                'last_contacted_at' => null,
                'next_follow_up_at' => $data['next_follow_up_at'] ?? now()->addDays(2),
                'lost_reason' => null,
                'notes' => $data['notes'] ?? null,
                'score' => $this->calculateLeadScore($data),
                'metadata' => $data['metadata'] ?? [],
            ]);

            // Create initial activity if scheduled
            if (!empty($lead->owner_id)) {
                CrmActivity::create([
                    'activity_type' => 'task',
                    'subject' => 'Initial Lead Contact: ' . $lead->full_name,
                    'description' => 'Reach out to new lead via Phone/WhatsApp to qualify interest in Blue Zone Longevity formulations.',
                    'lead_id' => $lead->id,
                    'assigned_to' => $lead->owner_id,
                    'created_by' => $creatorId ?? $lead->owner_id,
                    'due_at' => $lead->next_follow_up_at,
                    'status' => 'pending',
                    'priority' => $lead->priority,
                ]);
            }

            Log::info("CRM: Created lead {$lead->lead_number}", ['id' => $lead->id, 'creator' => $creatorId]);

            return $lead;
        });
    }

    /**
     * Detect potential duplicate leads and existing customers.
     */
    public function detectDuplicates(?string $email, ?string $phone, ?string $name = null, ?int $excludeLeadId = null): array
    {
        $normalized = $this->normalizePhone($phone);
        $leadsQuery = CrmLead::query();
        $customersQuery = Customer::query();

        if ($excludeLeadId) {
            $leadsQuery->where('id', '!=', $excludeLeadId);
        }

        $leadMatches = $leadsQuery->where(function ($q) use ($email, $phone, $normalized, $name) {
            if ($email) {
                $q->orWhere('email', $email);
            }
            if ($phone) {
                $q->orWhere('phone', $phone);
            }
            if ($normalized) {
                $q->orWhere('phone_normalized', $normalized);
            }
            if ($name && strlen($name) > 3) {
                $q->orWhere('full_name', 'like', "%{$name}%");
            }
        })->take(5)->get();

        $customerMatches = $customersQuery->where(function ($q) use ($email, $phone, $name) {
            if ($email) {
                $q->orWhere('email', $email);
            }
            if ($phone) {
                $q->orWhere('phone', $phone);
            }
            if ($name && strlen($name) > 3) {
                $q->orWhere('name', 'like', "%{$name}%");
            }
        })->take(5)->get();

        return [
            'duplicate_leads' => $leadMatches,
            'existing_customers' => $customerMatches,
            'has_duplicates' => $leadMatches->isNotEmpty() || $customerMatches->isNotEmpty(),
        ];
    }

    /**
     * Convert Lead to Customer and Opportunity within a database transaction.
     */
    public function convertLead(CrmLead $lead, array $options, int $userId): array
    {
        return DB::transaction(function () use ($lead, $options, $userId) {
            // Concurrency & idempotency check
            if ($lead->status === 'converted') {
                throw new \InvalidArgumentException("Lead {$lead->lead_number} is already converted.");
            }

            // 1. Resolve or Create Customer (Do not duplicate)
            $customer = null;
            if (!empty($options['existing_customer_id'])) {
                $customer = Customer::findOrFail($options['existing_customer_id']);
            } elseif (!empty($lead->customer_id)) {
                $customer = $lead->customer;
            } else {
                // Search existing customer by email or phone first
                if ($lead->email) {
                    $customer = Customer::where('email', $lead->email)->first();
                }
                if (!$customer && $lead->phone) {
                    $customer = Customer::where('phone', $lead->phone)->first();
                }

                if (!$customer) {
                    $customer = Customer::create([
                        'name' => $lead->full_name,
                        'email' => $lead->email ?: 'lead_' . $lead->id . '@crm.bluezone.local',
                        'phone' => $lead->phone,
                        'country' => $lead->country?->name_en ?? 'Saudi Arabia',
                        'city' => $lead->city?->name_en ?? 'Riyadh',
                        'status' => 'active',
                        'password' => bcrypt(\Illuminate\Support\Str::random(16)),
                        'loyalty_points' => 0,
                        'total_spent' => 0,
                    ]);
                }
            }

            // 2. Resolve or Create B2B Company if applicable
            $company = null;
            if (!empty($options['create_company']) && !empty($lead->company_name)) {
                $company = CrmCompany::firstOrCreate(
                    ['name' => $lead->company_name],
                    [
                        'email' => $lead->email,
                        'phone' => $lead->phone,
                        'country_id' => $lead->country_id,
                        'city_id' => $lead->city_id,
                        'owner_id' => $lead->owner_id ?? $userId,
                        'status' => 'active',
                    ]
                );
            } elseif (!empty($lead->company_id)) {
                $company = $lead->company;
            }

            if ($company && !$customer->company_id) {
                $customer->update(['company_id' => $company->id]);
            }

            // 3. Create Opportunity if requested
            $opportunity = null;
            if (!empty($options['create_opportunity'])) {
                $oppService = app(CrmOpportunityService::class);
                $opportunity = $oppService->createOpportunity([
                    'name' => $options['opportunity_name'] ?? ('Deal: ' . $lead->full_name),
                    'lead_id' => $lead->id,
                    'customer_id' => $customer->id,
                    'company_id' => $company?->id,
                    'pipeline_id' => $options['pipeline_id'] ?? 1,
                    'stage_id' => $options['stage_id'] ?? 1,
                    'owner_id' => $lead->owner_id ?? $userId,
                    'source_id' => $lead->source_id,
                    'campaign_id' => $lead->campaign_id,
                    'value' => $options['opportunity_value'] ?? $lead->estimated_value ?? 0.00,
                    'currency' => $lead->currency,
                    'expected_close_date' => $options['expected_close_date'] ?? now()->addMonth(),
                ], $userId);
            }

            // 4. Update Lead Record with conversion details
            $lead->update([
                'status' => 'converted',
                'customer_id' => $customer->id,
                'company_id' => $company?->id ?? $lead->company_id,
                'converted_at' => now(),
                'converted_customer_id' => $customer->id,
                'converted_opportunity_id' => $opportunity?->id,
            ]);

            // 5. Add audit Note
            CrmNote::create([
                'user_id' => $userId,
                'lead_id' => $lead->id,
                'customer_id' => $customer->id,
                'opportunity_id' => $opportunity?->id,
                'body' => "Lead converted successfully to Customer #{$customer->id}" . ($opportunity ? " and Opportunity {$opportunity->opportunity_number}" : "") . ".",
                'is_private' => false,
            ]);

            Log::info("CRM: Lead {$lead->lead_number} converted to customer {$customer->id}", [
                'lead_id' => $lead->id,
                'customer_id' => $customer->id,
                'opportunity_id' => $opportunity?->id,
            ]);

            return [
                'customer' => $customer,
                'opportunity' => $opportunity,
                'company' => $company,
            ];
        });
    }

    /**
     * Bulk assign leads to owner with transaction.
     */
    public function bulkAssign(array $leadIds, int $ownerId, int $currentUserId): int
    {
        return DB::transaction(function () use ($leadIds, $ownerId, $currentUserId) {
            $updated = CrmLead::whereIn('id', $leadIds)->update([
                'owner_id' => $ownerId,
                'assigned_at' => now(),
            ]);

            Log::info("CRM: Bulk assigned " . count($leadIds) . " leads to owner {$ownerId} by user {$currentUserId}");
            return $updated;
        });
    }

    /**
     * Bulk update lead status.
     */
    public function bulkUpdateStatus(array $leadIds, string $status, int $currentUserId): int
    {
        return DB::transaction(function () use ($leadIds, $status, $currentUserId) {
            $updated = CrmLead::whereIn('id', $leadIds)->update([
                'status' => $status,
            ]);

            Log::info("CRM: Bulk status updated to {$status} for " . count($leadIds) . " leads by user {$currentUserId}");
            return $updated;
        });
    }
}
