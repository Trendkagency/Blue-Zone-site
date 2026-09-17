<?php

namespace App\Services;

use App\Models\City;
use App\Models\Country;
use App\Models\CrmActivity;
use App\Models\CrmCampaign;
use App\Models\CrmLead;
use App\Models\CrmLeadSource;
use App\Models\CrmOpportunity;
use App\Models\CrmPipeline;
use App\Models\Customer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Border;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Reader\CSV\Reader as CsvReader;
use OpenSpout\Reader\XLSX\Reader as XlsxReader;
use OpenSpout\Writer\CSV\Writer as CsvWriter;
use OpenSpout\Writer\XLSX\Writer as XlsxWriter;

class CrmLeadImportService
{
    public function __construct(
        protected CrmLeadService $leadService,
        protected CrmOpportunityService $opportunityService
    ) {}

    /**
     * Generate and save a sample Excel / CSV template with realistic leads and tasks.
     */
    public function generateSampleTemplate(string $format = 'xlsx'): string
    {
        $filename = 'blue_zone_leads_with_tasks_sample_' . date('Y-m-d') . '.' . $format;
        $tempPath = storage_path('app/' . $filename);

        if (!file_exists(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0755, true);
        }

        $writer = $format === 'csv' ? new CsvWriter() : new XlsxWriter();
        $writer->openToFile($tempPath);

        // Header Row
        $headers = [
            'first_name',
            'last_name',
            'company_name',
            'job_title',
            'email',
            'phone',
            'secondary_phone',
            'country',
            'city',
            'status',
            'priority',
            'estimated_value',
            'currency',
            'source',
            'campaign',
            'notes',
            'assigned_owner_email',
            'task_subject',
            'task_type',
            'task_due_date',
            'task_priority',
            'task_description',
        ];

        $writer->addRow(Row::fromValues($headers));

        // Sample Rows with realistic Blue Zone medical, pharmacy, and VIP client data
        $samples = [
            [
                'Dr. Hisham',
                'Al-Khatib',
                'Oasis Longevity & Wellness Clinic',
                'Chief Medical Officer',
                'dr.hisham@oasisclinic.sa',
                '+966 50 112 3344',
                '+966 11 445 5667',
                'Saudi Arabia',
                'Riyadh',
                'new',
                'high',
                '45000.00',
                'SAR',
                'Clinic Outreach',
                'Cellular Health 2026',
                'Seeking bulk NMN 18000 and Resveratrol longevity protocol for 50 clinic members.',
                'admin@bluezone.com',
                'Zoom Meeting: Clinical Longevity Protocol Presentation',
                'meeting',
                Carbon::now()->addDays(3)->format('Y-m-d 10:00'),
                'high',
                'Present clinical dossier, Certificate of Analysis (COA), and institutional tier pricing.',
            ],
            [
                'Sarah',
                'Mansour',
                'Dermacare Pharmacy Group',
                'Head of Purchasing',
                'sarah.m@dermacare.com',
                '+966 55 998 7766',
                '',
                'Saudi Arabia',
                'Jeddah',
                'contacted',
                'normal',
                '18500.00',
                'SAR',
                'Referral',
                'Q4 Longevity Summit',
                'Inquired about exclusive shelf retail distribution for Blue Mind cognitive formula.',
                'admin@bluezone.com',
                'Send Wholesale Price Matrix and Shelf Display Agreement',
                'task',
                Carbon::now()->addDays(2)->format('Y-m-d 15:00'),
                'normal',
                'Email certified laboratory specification sheets and sample shelf display mockups.',
            ],
            [
                'Dr. Youssef',
                'El-Shennawy',
                'Cairo Cellular Aging Institute',
                'Medical Director',
                'youssef@cellularaging.eg',
                '+20 10 1234 5678',
                '',
                'Egypt',
                'Cairo',
                'qualified',
                'urgent',
                '60000.00',
                'EGP',
                'Website',
                'Cellular Health 2026',
                'High interest in NAD+ Booster cellular longevity program for executive patients.',
                'admin@bluezone.com',
                'Urgent Consultation Call: Confirm Pilot Batch Delivery',
                'call',
                Carbon::now()->addDays(1)->format('Y-m-d 12:30'),
                'urgent',
                'Verify receipt of pilot test formulation batch and schedule onboarding session with lab staff.',
            ],
            [
                'Reem',
                'Al-Zahrani',
                'Private Longevity Client',
                'Executive Director',
                'reem.z@vip-wellness.sa',
                '+966 54 332 1100',
                '',
                'Saudi Arabia',
                'Khobar',
                'new',
                'high',
                '8500.00',
                'SAR',
                'WhatsApp',
                '',
                'Personalized annual protocol inquiry for cellular anti-aging and daily energy vitality.',
                'admin@bluezone.com',
                'Phone Consultation: Recommend Individual Longevity Schedule',
                'call',
                Carbon::now()->addDays(2)->format('Y-m-d 16:30'),
                'high',
                'Review client daily regimen and propose personalized morning Blue Mind + evening cellular health combo.',
            ],
        ];

        foreach ($samples as $sample) {
            $writer->addRow(Row::fromValues($sample));
        }

        $writer->close();

        return $tempPath;
    }

    /**
     * Process and import uploaded Excel/CSV file into Leads, Pipeline Opportunities, and Tasks.
     */
    public function import(UploadedFile $file, array $options, int $userId): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $filePath = $file->getRealPath();

        $defaultOwnerId = !empty($options['default_owner_id']) ? (int) $options['default_owner_id'] : $userId;
        $defaultPipelineId = !empty($options['pipeline_id']) ? (int) $options['pipeline_id'] : null;
        $duplicateAction = $options['duplicate_action'] ?? 'skip'; // 'skip' or 'update'
        $createTasks = !empty($options['create_tasks']);

        // Preload caches for fast in-memory resolution without N+1 overhead
        $countries = Country::all()->keyBy(fn ($c) => strtolower(trim($c->name_en)));
        $countriesAr = Country::all()->keyBy(fn ($c) => strtolower(trim($c->name_ar)));
        $cities = City::all()->keyBy(fn ($c) => strtolower(trim($c->name_en)));
        $sources = CrmLeadSource::all()->keyBy(fn ($s) => strtolower(trim($s->name_en)));
        $campaigns = CrmCampaign::all()->keyBy(fn ($c) => strtolower(trim($c->name)));
        $usersByEmail = User::all()->keyBy(fn ($u) => strtolower(trim($u->email)));
        $usersByName = User::all()->keyBy(fn ($u) => strtolower(trim($u->name)));

        // Resolve active default pipeline
        $pipeline = $defaultPipelineId ? CrmPipeline::find($defaultPipelineId) : null;
        if (!$pipeline) {
            $pipeline = CrmPipeline::where('is_default', true)->where('is_active', true)->first()
                     ?? CrmPipeline::where('is_active', true)->first();
        }

        // Initialize Reader
        $reader = in_array($extension, ['csv', 'txt']) ? new CsvReader() : new XlsxReader();
        $reader->open($filePath);

        $results = [
            'total_rows' => 0,
            'imported_leads' => 0,
            'updated_leads' => 0,
            'created_tasks' => 0,
            'skipped_duplicates' => 0,
            'errors' => [],
        ];

        $headers = null;
        $rowNumber = 0;

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $rowNumber++;
                $cells = $row->toArray();

                // Detect headers from row 1
                if ($headers === null) {
                    $headers = array_map(fn ($h) => $this->normalizeHeader((string) $h), $cells);
                    continue;
                }

                // Skip completely empty rows
                if (empty(array_filter($cells, fn ($v) => $v !== null && trim((string)$v) !== ''))) {
                    continue;
                }

                $results['total_rows']++;
                $data = $this->combineRowData($headers, $cells);

                try {
                    $this->processRow(
                        $data,
                        $rowNumber,
                        $defaultOwnerId,
                        $pipeline,
                        $duplicateAction,
                        $createTasks,
                        $countries,
                        $countriesAr,
                        $cities,
                        $sources,
                        $campaigns,
                        $usersByEmail,
                        $usersByName,
                        $userId,
                        $results
                    );
                } catch (\Throwable $e) {
                    Log::warning("CRM Import: Error on row {$rowNumber}: " . $e->getMessage());
                    $results['errors'][] = [
                        'row' => $rowNumber,
                        'name' => ($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''),
                        'error' => $e->getMessage(),
                    ];
                }
            }
            break; // Process only first sheet
        }

        $reader->close();

        return $results;
    }

    /**
     * Process a single mapped row.
     */
    protected function processRow(
        array $data,
        int $rowNumber,
        int $defaultOwnerId,
        ?CrmPipeline $pipeline,
        string $duplicateAction,
        bool $createTasks,
        $countries,
        $countriesAr,
        $cities,
        $sources,
        $campaigns,
        $usersByEmail,
        $usersByName,
        int $userId,
        array &$results
    ): void {
        $firstName = trim($data['first_name'] ?? '');
        $lastName = trim($data['last_name'] ?? '');
        $companyName = trim($data['company_name'] ?? '');
        $email = !empty($data['email']) ? strtolower(trim($data['email'])) : null;
        $phone = !empty($data['phone']) ? trim($data['phone']) : null;

        if (empty($firstName) && empty($companyName)) {
            throw new \Exception("Row {$rowNumber} is missing required First Name or Company Name.");
        }

        if (empty($firstName)) {
            $firstName = $companyName;
        }

        $normalizedPhone = !empty($phone) ? $this->normalizePhone($phone) : null;

        // Duplicate Check
        $existingLead = null;
        if (!empty($email) || !empty($normalizedPhone)) {
            $existingLead = CrmLead::where(function ($q) use ($email, $normalizedPhone) {
                if (!empty($email)) {
                    $q->where('email', $email);
                }
                if (!empty($normalizedPhone)) {
                    $q->orWhere('phone_normalized', $normalizedPhone)
                      ->orWhere('phone', $normalizedPhone);
                }
            })->first();
        }

        // Handle Duplicates
        if ($existingLead) {
            if ($duplicateAction === 'skip') {
                $results['skipped_duplicates']++;
                return;
            }

            // Update existing lead
            $this->updateExistingLead($existingLead, $data, $countries, $countriesAr, $cities, $sources, $campaigns);
            $results['updated_leads']++;
            $lead = $existingLead;
        } else {
            // Resolve Owner
            $ownerId = $defaultOwnerId;
            if (!empty($data['assigned_owner_email'])) {
                $ownerEmail = strtolower(trim($data['assigned_owner_email']));
                if (isset($usersByEmail[$ownerEmail])) {
                    $ownerId = $usersByEmail[$ownerEmail]->id;
                }
            }

            // Resolve Country & City
            $countryId = null;
            if (!empty($data['country'])) {
                $cKey = strtolower(trim($data['country']));
                $countryId = $countries[$cKey]->id ?? $countriesAr[$cKey]->id ?? null;
            }

            $cityId = null;
            if (!empty($data['city'])) {
                $cityKey = strtolower(trim($data['city']));
                $cityId = $cities[$cityKey]->id ?? null;
            }

            // Resolve Source & Campaign
            $sourceId = null;
            if (!empty($data['source'])) {
                $sKey = strtolower(trim($data['source']));
                $sourceId = $sources[$sKey]->id ?? null;
            }

            $campaignId = null;
            if (!empty($data['campaign'])) {
                $campKey = strtolower(trim($data['campaign']));
                $campaignId = $campaigns[$campKey]->id ?? null;
            }

            $status = in_array(strtolower($data['status'] ?? ''), ['new', 'contacted', 'qualified', 'unqualified', 'lost'])
                ? strtolower($data['status']) : 'new';

            $priority = in_array(strtolower($data['priority'] ?? ''), ['low', 'normal', 'high', 'urgent'])
                ? strtolower($data['priority']) : 'normal';

            $estimatedValue = !empty($data['estimated_value']) ? (float) preg_replace('/[^0-9.]/', '', (string)$data['estimated_value']) : 0.00;
            $currency = !empty($data['currency']) ? strtoupper(trim($data['currency'])) : \App\Services\CurrencyService::code();

            // Create Lead through CrmLeadService (which automatically creates the Opportunity in the pipeline!)
            $lead = $this->leadService->createLead([
                'first_name' => $firstName,
                'last_name' => $lastName ?: null,
                'company_name' => $companyName ?: null,
                'job_title' => $data['job_title'] ?? null,
                'email' => $email,
                'phone' => $phone,
                'secondary_phone' => $data['secondary_phone'] ?? null,
                'country_id' => $countryId,
                'city_id' => $cityId,
                'source_id' => $sourceId,
                'campaign_id' => $campaignId,
                'status' => $status,
                'priority' => $priority,
                'estimated_value' => $estimatedValue,
                'currency' => $currency,
                'owner_id' => $ownerId,
                'pipeline_id' => $pipeline?->id,
                'notes' => $data['notes'] ?? null,
            ], $userId);

            $results['imported_leads']++;
        }

        // Create Task / Activity if provided
        if ($createTasks && !empty(trim($data['task_subject'] ?? ''))) {
            $this->createAttachedTask($lead, $data, $userId);
            $results['created_tasks']++;
        }
    }

    /**
     * Update an existing lead with sheet data if update mode is enabled.
     */
    protected function updateExistingLead(CrmLead $lead, array $data, $countries, $countriesAr, $cities, $sources, $campaigns): void
    {
        $updates = [];

        if (!empty($data['company_name'])) $updates['company_name'] = trim($data['company_name']);
        if (!empty($data['job_title'])) $updates['job_title'] = trim($data['job_title']);
        if (!empty($data['estimated_value'])) $updates['estimated_value'] = (float) preg_replace('/[^0-9.]/', '', (string)$data['estimated_value']);
        if (!empty($data['notes'])) $updates['notes'] = ($lead->notes ? $lead->notes . "\n---\n" : '') . trim($data['notes']);

        if (!empty($data['country'])) {
            $cKey = strtolower(trim($data['country']));
            $cid = $countries[$cKey]->id ?? $countriesAr[$cKey]->id ?? null;
            if ($cid) $updates['country_id'] = $cid;
        }

        if (!empty($data['city'])) {
            $cityKey = strtolower(trim($data['city']));
            $ctId = $cities[$cityKey]->id ?? null;
            if ($ctId) $updates['city_id'] = $ctId;
        }

        if (!empty($updates)) {
            $lead->update($updates);
        }
    }

    /**
     * Create an activity task attached to the lead.
     */
    protected function createAttachedTask(CrmLead $lead, array $data, int $creatorId): CrmActivity
    {
        $subject = trim($data['task_subject']);
        $rawType = strtolower(trim($data['task_type'] ?? 'task'));
        $type = in_array($rawType, ['call', 'meeting', 'task', 'email']) ? $rawType : 'task';

        $rawPriority = strtolower(trim($data['task_priority'] ?? ''));
        $priority = in_array($rawPriority, ['low', 'normal', 'high', 'urgent']) ? $rawPriority : ($lead->priority ?? 'normal');

        $dueAt = now()->addDays(2);
        if (!empty($data['task_due_date'])) {
            try {
                $parsed = Carbon::parse($data['task_due_date']);
                if ($parsed) {
                    $dueAt = $parsed;
                }
            } catch (\Throwable) {
                $dueAt = now()->addDays(2);
            }
        }

        $opportunity = CrmOpportunity::where('lead_id', $lead->id)->latest()->first();

        return CrmActivity::create([
            'activity_type' => $type,
            'subject' => $subject,
            'description' => !empty($data['task_description']) ? trim($data['task_description']) : 'Lead follow-up task imported from Excel.',
            'lead_id' => $lead->id,
            'customer_id' => $lead->customer_id,
            'company_id' => $lead->company_id,
            'opportunity_id' => $opportunity?->id,
            'assigned_to' => $lead->owner_id ?? $creatorId,
            'created_by' => $creatorId,
            'due_at' => $dueAt,
            'scheduled_at' => in_array($type, ['call', 'meeting']) ? $dueAt : null,
            'status' => 'pending',
            'priority' => $priority,
        ]);
    }

    /**
     * Normalize header name into clean snake_case.
     */
    protected function normalizeHeader(string $header): string
    {
        $cleaned = trim(strtolower($header));
        $cleaned = str_replace([' ', '-', '/', '.'], '_', $cleaned);
        $cleaned = preg_replace('/[^a-z0-9_]/', '', $cleaned);

        // Alias common variations
        $aliases = [
            'name' => 'first_name',
            'fname' => 'first_name',
            'lname' => 'last_name',
            'company' => 'company_name',
            'title' => 'job_title',
            'mobile' => 'phone',
            'cell' => 'phone',
            'phone_number' => 'phone',
            'email_address' => 'email',
            'value' => 'estimated_value',
            'deal_value' => 'estimated_value',
            'owner' => 'assigned_owner_email',
            'owner_email' => 'assigned_owner_email',
            'task' => 'task_subject',
            'task_title' => 'task_subject',
            'task_due' => 'task_due_date',
            'due_date' => 'task_due_date',
            'task_notes' => 'task_description',
            'task_desc' => 'task_description',
        ];

        return $aliases[$cleaned] ?? $cleaned;
    }

    /**
     * Combine normalized headers with row cell values.
     */
    protected function combineRowData(array $headers, array $cells): array
    {
        $data = [];
        foreach ($headers as $index => $key) {
            $data[$key] = isset($cells[$index]) ? trim((string)$cells[$index]) : null;
        }
        return $data;
    }

    /**
     * Normalize phone number to international E.164-like standard.
     */
    protected function normalizePhone(?string $phone): ?string
    {
        if (empty($phone)) return null;
        $cleaned = preg_replace('/[^0-9+]/', '', $phone);
        if (str_starts_with($cleaned, '00')) {
            $cleaned = '+' . substr($cleaned, 2);
        } elseif (str_starts_with($cleaned, '05') && strlen($cleaned) === 10) {
            $cleaned = '+966' . substr($cleaned, 1);
        } elseif (str_starts_with($cleaned, '01') && strlen($cleaned) === 11) {
            $cleaned = '+20' . substr($cleaned, 1);
        }
        return $cleaned;
    }
}
