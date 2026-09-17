<?php

namespace Database\Seeders;

use App\Models\CrmActivity;
use App\Models\CrmCampaign;
use App\Models\CrmCompany;
use App\Models\CrmLead;
use App\Models\CrmLeadSource;
use App\Models\CrmNote;
use App\Models\CrmOpportunity;
use App\Models\CrmPipeline;
use App\Models\CrmPipelineStage;
use App\Models\CrmTag;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class CrmDummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure basic CRM structures exist
        $this->call(CrmSeeder::class);

        // Fetch references
        $owner = User::first() ?? User::create([
            'name' => 'Dr. Alexander Vance',
            'email' => 'alexander@bluezone.local',
            'password' => bcrypt('password123'),
        ]);

        $sources = CrmLeadSource::all()->keyBy('slug');
        $retailPipeline = CrmPipeline::where('slug', 'retail-longevity-sales')->first();
        $b2bPipeline = CrmPipeline::where('slug', 'b2b-clinic-partnerships')->first();

        $retailStages = CrmPipelineStage::where('pipeline_id', $retailPipeline?->id)->get()->keyBy('slug');
        $b2bStages = CrmPipelineStage::where('pipeline_id', $b2bPipeline?->id)->get()->keyBy('slug');

        $tagVip = CrmTag::where('slug', 'vip')->first();
        $tagHighValue = CrmTag::where('slug', 'high-value')->first();
        $tagNad = CrmTag::where('slug', 'nad-interest')->first();
        $tagClinic = CrmTag::where('slug', 'clinic-wholesale')->first();
        $currency = \App\Services\CurrencyService::code();

        // 2. Seed Realistic B2B Companies
        $companiesData = [
            [
                'name' => 'Al-Nukhba Longevity & Wellness Clinic',
                'legal_name' => 'Al-Nukhba Advanced Health LLC',
                'email' => 'partnerships@nukhbaclinic.com',
                'phone' => '+966 11 482 9901',
                'website' => 'https://nukhbaclinic.com',
                'industry' => 'Integrative Longevity Medicine',
                'company_size' => '50-100',
                'tax_number' => '300192847100003',
                'registration_number' => '1010847291',
                'address' => 'King Fahd Road, Al-Olaya, Riyadh',
                'status' => 'active',
                'notes' => 'Prestigious private longevity clinic in Riyadh specializing in NAD+ IV therapy and biomarker tracking.',
            ],
            [
                'name' => 'Dar Al-Hikma Integrative Pharmacy Network',
                'legal_name' => 'Dar Al-Hikma Pharmaceutical Group',
                'email' => 'procurement@daralhikmapharma.com',
                'phone' => '+966 12 654 3210',
                'website' => 'https://daralhikmapharma.com',
                'industry' => 'Pharmaceutical & Nutraceitical Retail',
                'company_size' => '200+',
                'tax_number' => '300293848200003',
                'registration_number' => '4030192837',
                'address' => 'Prince Sultan Street, Al-Rawdah, Jeddah',
                'status' => 'active',
                'notes' => '14 premium pharmacy branches seeking exclusive shelf placement for Blue Zone NMN & Resveratrol.',
            ],
            [
                'name' => 'BioAge Cellular Institute',
                'legal_name' => 'BioAge Wellness Care Co.',
                'email' => 'dr.tariq@bioage-wellness.com',
                'phone' => '+966 13 890 1234',
                'website' => 'https://bioage-wellness.com',
                'industry' => 'Functional Anti-Aging Center',
                'company_size' => '20-50',
                'tax_number' => '300394857300003',
                'registration_number' => '2050182736',
                'address' => 'Corniche Road, Al-Khobar',
                'status' => 'active',
                'notes' => 'Functional medicine practice prescribing clinical doses of cellular energy cofactors.',
            ],
            [
                'name' => 'Gulf Longevity & Bioceuticals Distribution',
                'legal_name' => 'Gulf Bioceuticals Logistics Ltd.',
                'email' => 'accounts@gulfbioceuticals.com',
                'phone' => '+971 4 399 8811',
                'website' => 'https://gulfbioceuticals.com',
                'industry' => 'Regional Health Distribution',
                'company_size' => '100-250',
                'tax_number' => '100492837100003',
                'registration_number' => '884920',
                'address' => 'Business Bay, Dubai, UAE',
                'status' => 'active',
                'notes' => 'Regional GCC distributor negotiating distribution rights across UAE, Kuwait, and Bahrain.',
            ],
        ];

        $companies = [];
        foreach ($companiesData as $data) {
            $company = CrmCompany::firstOrCreate(
                ['email' => $data['email']],
                array_merge($data, ['owner_id' => $owner->id])
            );
            if ($tagClinic && $tagHighValue) {
                $company->tags()->syncWithoutDetaching([$tagClinic->id, $tagHighValue->id]);
            }
            $companies[] = $company;
        }

        // 3. Seed Marketing Campaigns
        $campaignsData = [
            [
                'name' => 'Q3 Cellular NAD+ Protocol Push',
                'description' => 'Targeted educational campaign emphasizing cellular rejuvenation and mitochondrial health.',
                'type' => 'paid_ads',
                'status' => 'active',
                'budget' => 35000.00,
                'currency' => $currency,
                'start_at' => Carbon::now()->subDays(45),
                'end_at' => Carbon::now()->addDays(45),
                'utm_source' => 'meta',
                'utm_medium' => 'cpc',
                'utm_campaign' => 'nad_cellular_q3',
                'target_leads' => 500,
                'converted_leads' => 64,
                'revenue_generated' => 128500.00,
            ],
            [
                'name' => 'Physician Longevity Referral Network',
                'description' => 'B2B physician onboarding program offering clinical samples and CPD webinar accreditations.',
                'type' => 'referral',
                'status' => 'active',
                'budget' => 15000.00,
                'currency' => $currency,
                'start_at' => Carbon::now()->subDays(60),
                'end_at' => Carbon::now()->addDays(90),
                'utm_source' => 'medical_conference',
                'utm_medium' => 'offline',
                'utm_campaign' => 'physician_network_2026',
                'target_leads' => 50,
                'converted_leads' => 12,
                'revenue_generated' => 240000.00,
            ],
            [
                'name' => 'TikTok Longevity Protocol Series',
                'description' => 'Influencer partnerships explaining the biochemical pathways of Resveratrol and Sirtuin activation.',
                'type' => 'social',
                'status' => 'active',
                'budget' => 20000.00,
                'currency' => $currency,
                'start_at' => Carbon::now()->subDays(30),
                'end_at' => Carbon::now()->addDays(30),
                'utm_source' => 'tiktok',
                'utm_medium' => 'influencer',
                'utm_campaign' => 'sirtuin_biohack',
                'target_leads' => 800,
                'converted_leads' => 95,
                'revenue_generated' => 85400.00,
            ],
            [
                'name' => 'Executive Bio-Optimization Ramadan',
                'description' => 'Fasting protocols paired with mitochondrial cellular support.',
                'type' => 'email',
                'status' => 'completed',
                'budget' => 8000.00,
                'currency' => $currency,
                'start_at' => Carbon::now()->subDays(120),
                'end_at' => Carbon::now()->subDays(60),
                'utm_source' => 'newsletter',
                'utm_medium' => 'email',
                'utm_campaign' => 'ramadan_longevity',
                'target_leads' => 300,
                'converted_leads' => 48,
                'revenue_generated' => 72000.00,
            ],
        ];

        $campaigns = [];
        foreach ($campaignsData as $cData) {
            $campaign = CrmCampaign::firstOrCreate(
                ['name' => $cData['name']],
                array_merge($cData, ['owner_id' => $owner->id])
            );
            $campaigns[] = $campaign;
        }

        // 4. Ensure Customers exist for testing Customer 360 & Conversions
        $c1 = Customer::firstOrCreate(
            ['email' => 'fahad.alsaud@vip.local'],
            [
                'name' => 'HRH Prince Fahad Al-Saud',
                'phone' => '+966 50 111 2233',
                'status' => 'active',
                'total_spent' => 14500.00,
                'total_orders' => 4,
                'loyalty_points' => 950,
            ]
        );
        if ($tagVip && $tagHighValue && $tagNad) {
            $c1->crmTags()->syncWithoutDetaching([$tagVip->id, $tagHighValue->id, $tagNad->id]);
        }

        $c2 = Customer::firstOrCreate(
            ['email' => 'dr.mona.shams@nukhbaclinic.com'],
            [
                'name' => 'Dr. Mona Al-Shams',
                'phone' => '+966 54 999 8877',
                'status' => 'active',
                'total_spent' => 42000.00,
                'total_orders' => 6,
                'loyalty_points' => 2800,
            ]
        );
        if ($tagClinic && $tagHighValue) {
            $c2->crmTags()->syncWithoutDetaching([$tagClinic->id, $tagHighValue->id]);
        }

        $c3 = Customer::firstOrCreate(
            ['email' => 'khalid.tamimi@corporate.local'],
            [
                'name' => 'Khalid Al-Tamimi',
                'phone' => '+966 55 432 1098',
                'status' => 'active',
                'total_spent' => 1200.00,
                'total_orders' => 1,
                'loyalty_points' => 80,
            ]
        );

        // 5. Seed Diverse Leads
        $leadsData = [
            [
                'lead_number' => 'LD-2026-00010',
                'first_name' => 'Sultan',
                'last_name' => 'Al-Hokair',
                'full_name' => 'Sultan Al-Hokair',
                'email' => 'sultan.hokair@invest.local',
                'phone' => '+966 50 882 1920',
                'company_name' => 'Al-Hokair Holding',
                'job_title' => 'Managing Director',
                'source_id' => $sources['whatsapp']->id ?? null,
                'campaign_id' => $campaigns[0]->id,
                'status' => 'qualified',
                'stage' => 'Formulation Consultation',
                'priority' => 'urgent',
                'estimated_value' => 8500.00,
                'currency' => $currency,
                'owner_id' => $owner->id,
                'next_follow_up_at' => Carbon::now()->addDays(1),
                'notes' => 'Interested in bespoke 12-month NAD+ protocol and mitochondrial bioceuticals.',
            ],
            [
                'lead_number' => 'LD-2026-00011',
                'first_name' => 'Dr. Rania',
                'last_name' => 'Badr',
                'full_name' => 'Dr. Rania Badr',
                'email' => 'dr.rania@dermalongevity.sa',
                'phone' => '+966 56 321 7890',
                'company_id' => $companies[1]->id,
                'company_name' => 'Derma & Longevity Center',
                'job_title' => 'Chief Dermatologist',
                'source_id' => $sources['referral']->id ?? null,
                'campaign_id' => $campaigns[1]->id,
                'status' => 'contacted',
                'stage' => 'Needs Assessment',
                'priority' => 'high',
                'estimated_value' => 25000.00,
                'currency' => $currency,
                'owner_id' => $owner->id,
                'last_contacted_at' => Carbon::now()->subDays(2),
                'next_follow_up_at' => Carbon::now()->addDays(2),
                'notes' => 'Looking to dispense Blue Zone formulations to executive anti-aging patients.',
            ],
            [
                'lead_number' => 'LD-2026-00012',
                'first_name' => 'Nasser',
                'last_name' => 'Al-Ghamdi',
                'full_name' => 'Nasser Al-Ghamdi',
                'email' => 'nasser.ghamdi@fitness.sa',
                'phone' => '+966 55 901 2345',
                'company_name' => 'Apex Performance Club',
                'job_title' => 'Athletic Director',
                'source_id' => $sources['instagram']->id ?? null,
                'campaign_id' => $campaigns[2]->id,
                'status' => 'new',
                'stage' => 'Initial Outreach',
                'priority' => 'normal',
                'estimated_value' => 4500.00,
                'currency' => $currency,
                'owner_id' => $owner->id,
                'next_follow_up_at' => Carbon::now()->addHours(6),
                'notes' => 'Inquired via Instagram ad about cellular recovery post high-intensity training.',
            ],
            [
                'lead_number' => 'LD-2026-00013',
                'first_name' => 'Dr. Tariq',
                'last_name' => 'Al-Mansoor',
                'full_name' => 'Dr. Tariq Al-Mansoor',
                'email' => 'dr.tariq@bioage-wellness.com',
                'phone' => '+966 13 890 1234',
                'company_id' => $companies[2]->id,
                'company_name' => 'BioAge Cellular Institute',
                'job_title' => 'Medical Director',
                'source_id' => $sources['clinic-partner']->id ?? null,
                'campaign_id' => $campaigns[1]->id,
                'status' => 'converted',
                'stage' => 'Contract Won',
                'priority' => 'high',
                'estimated_value' => 60000.00,
                'currency' => $currency,
                'owner_id' => $owner->id,
                'converted_at' => Carbon::now()->subDays(10),
                'converted_customer_id' => $c2->id,
                'notes' => 'Converted successfully into institutional wholesale client.',
            ],
            [
                'lead_number' => 'LD-2026-00014',
                'first_name' => 'Laila',
                'last_name' => 'Al-Harbi',
                'full_name' => 'Laila Al-Harbi',
                'email' => 'laila.harbi@yahoo.com',
                'phone' => '+966 50 678 9123',
                'company_name' => null,
                'job_title' => 'Health Enthusiast',
                'source_id' => $sources['tiktok']->id ?? null,
                'campaign_id' => $campaigns[2]->id,
                'status' => 'lost',
                'stage' => 'Lost / Disqualified',
                'priority' => 'low',
                'estimated_value' => 1200.00,
                'currency' => $currency,
                'owner_id' => $owner->id,
                'lost_reason' => 'Client looking for cheap generic multivitamins, not premium longevity bioceuticals.',
                'notes' => 'Disqualified due to mismatch with clinical positioning.',
            ],
            [
                'lead_number' => 'LD-2026-00015',
                'first_name' => 'Abdulrahman',
                'last_name' => 'Al-Dosari',
                'full_name' => 'Abdulrahman Al-Dosari',
                'email' => 'dosari.a@venture.sa',
                'phone' => '+966 53 123 9988',
                'company_name' => 'Dosari Family Office',
                'job_title' => 'Chief Investment Officer',
                'source_id' => $sources['website']->id ?? null,
                'campaign_id' => $campaigns[0]->id,
                'status' => 'qualified',
                'stage' => 'Proposal Presented',
                'priority' => 'urgent',
                'estimated_value' => 18000.00,
                'currency' => $currency,
                'owner_id' => $owner->id,
                'next_follow_up_at' => Carbon::now()->addDays(2),
                'notes' => 'Seeking family wellness protocol with quarterly biomarker reviews.',
            ],
        ];

        $leads = [];
        foreach ($leadsData as $lData) {
            $lead = CrmLead::firstOrCreate(
                ['lead_number' => $lData['lead_number']],
                $lData
            );
            if ($tagNad) {
                $lead->tags()->syncWithoutDetaching([$tagNad->id]);
            }
            $leads[] = $lead;
        }

        // 6. Seed Opportunities across Retail and B2B Pipelines
        $oppsData = [
            [
                'opportunity_number' => 'OPP-2026-00101',
                'lead_id' => $leads[0]->id,
                'customer_id' => $c1->id,
                'name' => 'Sultan Al-Hokair — Annual Longevity Protocol',
                'description' => '12-month supply of Blue Zone NMN Platinum, Resveratrol, and Cellular Detox complexes.',
                'pipeline_id' => $retailPipeline->id,
                'stage_id' => $retailStages['formulation-consultation']->id ?? $retailStages->first()->id,
                'owner_id' => $owner->id,
                'source_id' => $sources['whatsapp']->id ?? null,
                'campaign_id' => $campaigns[0]->id,
                'value' => 12500.00,
                'currency' => $currency,
                'probability' => 60,
                'expected_close_date' => Carbon::now()->addDays(14),
                'status' => 'open',
            ],
            [
                'opportunity_number' => 'OPP-2026-00102',
                'customer_id' => $c2->id,
                'company_id' => $companies[0]->id,
                'name' => 'Al-Nukhba Clinic — Q4 Institutional Bulk Supply',
                'description' => 'Wholesale clinical packs for clinic in-house longevity protocols (250 units).',
                'pipeline_id' => $b2bPipeline->id,
                'stage_id' => $b2bStages['wholesale-proposal']->id ?? $b2bStages->first()->id,
                'owner_id' => $owner->id,
                'source_id' => $sources['clinic-partner']->id ?? null,
                'campaign_id' => $campaigns[1]->id,
                'value' => 85000.00,
                'currency' => $currency,
                'probability' => 70,
                'expected_close_date' => Carbon::now()->addDays(21),
                'status' => 'open',
            ],
            [
                'opportunity_number' => 'OPP-2026-00103',
                'customer_id' => $c2->id,
                'company_id' => $companies[1]->id,
                'name' => 'Dar Al-Hikma Pharmacy — Pilot Shelf Distribution',
                'description' => 'Initial 5-branch rollout of OTC longevity formulations with branded display units.',
                'pipeline_id' => $b2bPipeline->id,
                'stage_id' => $b2bStages['contract-signed-stock-delivered']->id ?? $b2bStages->last()->id,
                'owner_id' => $owner->id,
                'source_id' => $sources['referral']->id ?? null,
                'campaign_id' => $campaigns[1]->id,
                'value' => 140000.00,
                'currency' => $currency,
                'probability' => 100,
                'expected_close_date' => Carbon::now()->subDays(5),
                'won_at' => Carbon::now()->subDays(5),
                'status' => 'won',
            ],
            [
                'opportunity_number' => 'OPP-2026-00104',
                'lead_id' => $leads[5]->id,
                'name' => 'Abdulrahman Al-Dosari — Executive Protocol',
                'description' => 'Executive anti-aging protocol tailored to high-stress performance optimization.',
                'pipeline_id' => $retailPipeline->id,
                'stage_id' => $retailStages['checkout-order-placed']->id ?? $retailStages->first()->id,
                'owner_id' => $owner->id,
                'source_id' => $sources['website']->id ?? null,
                'campaign_id' => $campaigns[0]->id,
                'value' => 18000.00,
                'currency' => $currency,
                'probability' => 90,
                'expected_close_date' => Carbon::now()->addDays(4),
                'status' => 'open',
            ],
            [
                'opportunity_number' => 'OPP-2026-00105',
                'customer_id' => $c3->id,
                'name' => 'Khalid Al-Tamimi — Protocol Renewal',
                'description' => '3-month reorder of cellular NAD+ booster.',
                'pipeline_id' => $retailPipeline->id,
                'stage_id' => $retailStages['lost-disqualified']->id ?? $retailStages->last()->id,
                'owner_id' => $owner->id,
                'source_id' => $sources['website']->id ?? null,
                'value' => 3600.00,
                'currency' => $currency,
                'probability' => 0,
                'expected_close_date' => Carbon::now()->subDays(15),
                'lost_at' => Carbon::now()->subDays(15),
                'lost_reason' => 'Client paused protocol temporarily due to extensive international travel.',
                'status' => 'lost',
            ],
        ];

        $opportunities = [];
        foreach ($oppsData as $oData) {
            $opp = CrmOpportunity::firstOrCreate(
                ['opportunity_number' => $oData['opportunity_number']],
                $oData
            );
            if ($tagHighValue) {
                $opp->tags()->syncWithoutDetaching([$tagHighValue->id]);
            }
            $opportunities[] = $opp;
        }

        // 7. Seed Activities & Tasks (Pending, Completed, Overdue)
        $activitiesData = [
            [
                'activity_type' => 'task',
                'subject' => 'Prepare Wholesale Distribution Agreement for Dar Al-Hikma',
                'description' => 'Finalize payment terms and logistics schedule for 5 pilot branches.',
                'company_id' => $companies[1]->id,
                'opportunity_id' => $opportunities[2]->id,
                'assigned_to' => $owner->id,
                'created_by' => $owner->id,
                'due_at' => Carbon::now()->subDays(2),
                'status' => 'completed',
                'priority' => 'urgent',
                'outcome' => 'Contract executed and forwarded to warehouse dispatch team.',
                'completed_at' => Carbon::now()->subDays(1),
            ],
            [
                'activity_type' => 'call',
                'subject' => 'Scientific Longevity Consultation with Sultan Al-Hokair',
                'description' => 'Discuss NAD+ vs NR clinical bioavailability and customized titration doses.',
                'lead_id' => $leads[0]->id,
                'opportunity_id' => $opportunities[0]->id,
                'assigned_to' => $owner->id,
                'created_by' => $owner->id,
                'due_at' => Carbon::now()->today()->setHour(15),
                'status' => 'pending',
                'priority' => 'high',
            ],
            [
                'activity_type' => 'meeting',
                'subject' => 'Clinical Board Presentation at Al-Nukhba Clinic',
                'description' => 'Present third-party clinical purity lab results to Dr. Mona and senior clinical team.',
                'company_id' => $companies[0]->id,
                'customer_id' => $c2->id,
                'opportunity_id' => $opportunities[1]->id,
                'assigned_to' => $owner->id,
                'created_by' => $owner->id,
                'scheduled_at' => Carbon::now()->addDays(3)->setHour(11),
                'due_at' => Carbon::now()->addDays(3)->setHour(12),
                'status' => 'scheduled',
                'priority' => 'urgent',
            ],
            [
                'activity_type' => 'follow_up',
                'subject' => 'Overdue: Follow up on Inactive Client Reactivation',
                'description' => 'Reach out to client to evaluate 90-day progress and propose mitochondrial re-charge formulation.',
                'customer_id' => $c3->id,
                'assigned_to' => $owner->id,
                'created_by' => $owner->id,
                'due_at' => Carbon::now()->subDays(3),
                'status' => 'overdue',
                'priority' => 'normal',
            ],
            [
                'activity_type' => 'task',
                'subject' => 'Dispatch Sample Clinical Formulation Kits to BioAge Institute',
                'description' => 'Courier 10 sample kits of NMN 500mg sublingual formulation to Dr. Tariq.',
                'company_id' => $companies[2]->id,
                'assigned_to' => $owner->id,
                'created_by' => $owner->id,
                'due_at' => Carbon::now()->today()->setHour(18),
                'status' => 'pending',
                'priority' => 'normal',
            ],
        ];

        foreach ($activitiesData as $act) {
            CrmActivity::create($act);
        }

        // 8. Seed Internal CRM Notes
        $notesData = [
            [
                'user_id' => $owner->id,
                'customer_id' => $c1->id,
                'body' => 'Client tolerates 1000mg NMN extremely well. Reported significantly enhanced REM sleep and morning mental acuity.',
                'is_private' => false,
            ],
            [
                'user_id' => $owner->id,
                'company_id' => $companies[0]->id,
                'body' => 'Dr. Mona requested priority cold-chain batch delivery for July shipments to ensure maximum NAD+ stability.',
                'is_private' => true,
            ],
            [
                'user_id' => $owner->id,
                'lead_id' => $leads[0]->id,
                'body' => 'High conversion probability. Personal assistant requested invoices addressed to Al-Hokair Holding.',
                'is_private' => false,
            ],
            [
                'user_id' => $owner->id,
                'opportunity_id' => $opportunities[1]->id,
                'body' => 'Wholesale discount negotiated at 22% for orders exceeding 50,000 SAR quarterly.',
                'is_private' => true,
            ],
        ];

        foreach ($notesData as $n) {
            CrmNote::create($n);
        }

        // 9. Re-calculate dynamic segments with seeded records
        app(\App\Services\CrmSegmentService::class)->refreshSegmentCounts();
    }
}
