<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\CrmActivity;
use App\Models\CrmCampaign;
use App\Models\CrmLead;
use App\Models\CrmLeadSource;
use App\Models\CrmOpportunity;
use App\Models\CrmPipeline;
use App\Models\CrmPipelineStage;
use App\Models\CrmSegment;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Role;
use App\Models\User;
use App\Services\CrmCustomerService;
use App\Services\CrmLeadService;
use App\Services\CrmOpportunityService;
use App\Services\CrmSegmentService;
use Database\Seeders\CrmSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrmFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $salesAgent;
    protected User $unauthorizedUser;
    protected CrmPipeline $pipeline;
    protected CrmPipelineStage $newStage;
    protected CrmPipelineStage $wonStage;
    protected CrmLeadSource $source;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed CRM structures
        $this->seed(CrmSeeder::class);

        // Super Admin with wildcard permission
        $superAdminRole = Role::firstOrCreate(
            ['slug' => 'super-admin'],
            ['name' => 'Super Administrator', 'permissions' => ['*'], 'is_system' => true]
        );

        $this->admin = User::firstOrCreate(
            ['email' => 'crm_admin@bluezone.com'],
            ['name' => 'CRM Administrator', 'password' => bcrypt('password'), 'role_id' => $superAdminRole->id, 'status' => 'active']
        );

        // Staff without CRM permissions
        $guestRole = Role::firstOrCreate(
            ['slug' => 'restricted-staff'],
            ['name' => 'Restricted Staff', 'permissions' => ['products.view'], 'is_system' => false]
        );

        $this->unauthorizedUser = User::firstOrCreate(
            ['email' => 'restricted@bluezone.com'],
            ['name' => 'Restricted User', 'password' => bcrypt('password'), 'role_id' => $guestRole->id, 'status' => 'active']
        );

        $this->pipeline = CrmPipeline::first();
        $this->newStage = $this->pipeline->stages()->first();
        $this->wonStage = $this->pipeline->stages()->where('is_won', true)->first();
        $this->source = CrmLeadSource::first();
    }

    public function test_unauthorized_user_is_forbidden_from_crm_dashboard(): void
    {
        $response = $this->actingAs($this->unauthorizedUser, 'web')->get(route('admin.crm.dashboard'));
        $response->assertStatus(403);
    }

    public function test_authorized_admin_can_access_crm_dashboard(): void
    {
        $response = $this->actingAs($this->admin, 'web')->get(route('admin.crm.dashboard'));
        $response->assertStatus(200);
        $response->assertSee(__('crm.dashboard.title'));
    }

    public function test_admin_can_create_lead_with_auto_generated_number(): void
    {
        $payload = [
            'first_name' => 'Tariq',
            'last_name' => 'Al-Harbi',
            'email' => 'tariq.harbi@example.com',
            'phone' => '+966 55 123 4567',
            'company_name' => 'Longevity Wellness Riyadh',
            'status' => 'new',
            'priority' => 'high',
            'source_id' => $this->source->id,
            'estimated_value' => 4500.00,
        ];

        $response = $this->actingAs($this->admin, 'web')->post(route('admin.crm.leads.store'), $payload);

        $response->assertRedirect();
        $this->assertDatabaseHas('crm_leads', [
            'first_name' => 'Tariq',
            'last_name' => 'Al-Harbi',
            'email' => 'tariq.harbi@example.com',
            'status' => 'new',
            'priority' => 'high',
        ]);

        $lead = CrmLead::where('email', 'tariq.harbi@example.com')->first();
        $this->assertNotNull($lead);
        $this->assertStringStartsWith('LD-', $lead->lead_number);
        $this->assertGreaterThan(0, $lead->score);
    }

    public function test_duplicate_detection_api_detects_existing_email_and_phone(): void
    {
        CrmLead::create([
            'lead_number' => 'LD-2026-99999',
            'first_name' => 'Existing',
            'last_name' => 'Lead',
            'full_name' => 'Existing Lead',
            'email' => 'existing@bluezone.local',
            'phone' => '+966509999999',
            'status' => 'new',
            'priority' => 'normal',
        ]);

        $response = $this->actingAs($this->admin, 'web')->getJson(
            route('admin.crm.leads.check-duplicates', ['email' => 'existing@bluezone.local'])
        );

        $response->assertStatus(200);
        $response->assertJson(['has_duplicates' => true]);
    }

    public function test_lead_conversion_creates_customer_and_opportunity_transactionally(): void
    {
        $lead = CrmLead::create([
            'lead_number' => 'LD-2026-88888',
            'first_name' => 'Fatima',
            'last_name' => 'Zahra',
            'full_name' => 'Fatima Zahra',
            'email' => 'fatima.zahra@example.com',
            'phone' => '+966508888888',
            'company_name' => 'Zahra Wellness Clinic',
            'status' => 'qualified',
            'priority' => 'high',
            'estimated_value' => 12000.00,
        ]);

        $conversionPayload = [
            'create_company' => true,
            'create_opportunity' => true,
            'opportunity_name' => 'Zahra Longevity Clinic Partnership',
            'pipeline_id' => $this->pipeline->id,
            'stage_id' => $this->newStage->id,
            'opportunity_value' => 12000.00,
        ];

        $response = $this->actingAs($this->admin, 'web')->post(
            route('admin.crm.leads.convert', $lead->id),
            $conversionPayload
        );

        $response->assertRedirect();

        // Verify lead is converted
        $lead->refresh();
        $this->assertEquals('converted', $lead->status);
        $this->assertNotNull($lead->converted_at);
        $this->assertNotNull($lead->converted_customer_id);
        $this->assertNotNull($lead->converted_opportunity_id);

        // Verify Customer created
        $this->assertDatabaseHas('customers', [
            'id' => $lead->converted_customer_id,
            'email' => 'fatima.zahra@example.com',
        ]);

        // Verify Opportunity created
        $this->assertDatabaseHas('crm_opportunities', [
            'id' => $lead->converted_opportunity_id,
            'name' => 'Zahra Longevity Clinic Partnership',
            'value' => 12000.00,
        ]);
    }

    public function test_opportunity_stage_movement_and_revenue_attribution(): void
    {
        $campaign = CrmCampaign::create([
            'name' => 'Cellular Q3 Campaign',
            'type' => 'social',
            'status' => 'active',
            'budget' => 5000.00,
            'revenue_generated' => 0.00,
        ]);

        $opportunity = CrmOpportunity::create([
            'opportunity_number' => 'OPP-2026-77777',
            'name' => 'Major Pharmacy Batch',
            'pipeline_id' => $this->pipeline->id,
            'stage_id' => $this->newStage->id,
            'owner_id' => $this->admin->id,
            'campaign_id' => $campaign->id,
            'value' => 15000.00,
            'currency' => 'SAR',
            'probability' => 10,
            'status' => 'open',
        ]);

        // Move to WON stage via AJAX
        $response = $this->actingAs($this->admin, 'web')->postJson(
            route('admin.crm.opportunities.update-stage', $opportunity->id),
            ['stage_id' => $this->wonStage->id]
        );

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $opportunity->refresh();
        $this->assertEquals('won', $opportunity->status);
        $this->assertNotNull($opportunity->won_at);

        // Verify campaign revenue attribution incremented
        $campaign->refresh();
        $this->assertEquals(15000.00, (float) $campaign->revenue_generated);
    }

    public function test_activity_scheduling_and_completion(): void
    {
        $payload = [
            'activity_type' => 'call',
            'subject' => 'Call Dr. Mansour regarding Cellular Formulations',
            'priority' => 'urgent',
            'due_at' => now()->addDay()->format('Y-m-d H:i'),
            'assigned_to' => $this->admin->id,
        ];

        $response = $this->actingAs($this->admin, 'web')->post(route('admin.crm.activities.store'), $payload);
        $response->assertRedirect();

        $activity = CrmActivity::where('subject', $payload['subject'])->first();
        $this->assertNotNull($activity);
        $this->assertEquals('pending', $activity->status);

        // Complete activity
        $completeResponse = $this->actingAs($this->admin, 'web')->post(
            route('admin.crm.activities.complete', $activity->id),
            ['outcome' => 'Client ordered 50 bottles of NMN Longevity Complex']
        );

        $completeResponse->assertRedirect();
        $activity->refresh();
        $this->assertEquals('completed', $activity->status);
        $this->assertEquals('Client ordered 50 bottles of NMN Longevity Complex', $activity->outcome);
    }

    public function test_customer_360_profile_aggregates_delivered_orders_only(): void
    {
        $customer = Customer::create([
            'name' => 'VIP Customer',
            'email' => 'vip@bluezone.local',
            'status' => 'active',
            'total_spent' => 8000.00,
            'total_orders' => 2,
            'loyalty_points' => 350,
        ]);

        // Delivered order (should be counted)
        Order::create([
            'order_number' => 'BZ-DELIV-1',
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'date' => now()->toDateString(),
            'total' => 5000.00,
            'status' => 'delivered',
            'payment_status' => 'paid',
        ]);

        // Cancelled order (should NOT be counted as revenue)
        Order::create([
            'order_number' => 'BZ-CANCEL-2',
            'customer_id' => $customer->id,
            'customer_name' => $customer->name,
            'date' => now()->toDateString(),
            'total' => 3000.00,
            'status' => 'cancelled',
            'payment_status' => 'failed',
        ]);

        $service = app(CrmCustomerService::class);
        $profile = $service->getCustomer360($customer);

        $this->assertEquals(5000.00, $profile['metrics']['total_revenue']);
        $this->assertEquals(1, $profile['metrics']['delivered_orders_count']);
        $this->assertEquals(5000.00, $profile['metrics']['average_order_value']);

        // Check customer 360 web endpoint
        $response = $this->actingAs($this->admin, 'web')->get(route('admin.customers.crm-360', $customer->id));
        $response->assertStatus(200);
        $response->assertSee('VIP Customer');
    }

    public function test_crm_segment_evaluation(): void
    {
        // High value customer
        Customer::create([
            'name' => 'High Value Client',
            'email' => 'high.value@example.com',
            'total_spent' => 4500.00,
            'total_orders' => 3,
            'status' => 'active',
        ]);

        $highValueSegment = CrmSegment::where('slug', 'high-value')->first();
        $this->assertNotNull($highValueSegment);

        $service = app(CrmSegmentService::class);
        $customers = $service->getCustomersForSegment($highValueSegment);

        $this->assertTrue($customers->contains('email', 'high.value@example.com'));
    }

    public function test_crm_reports_page_and_aggregations_execute_without_ambiguous_columns(): void
    {
        $response = $this->actingAs($this->admin, 'web')->get(route('admin.crm.reports.index', [
            'start_date' => now()->subDays(30)->toDateString(),
            'end_date' => now()->toDateString(),
        ]));

        $response->assertStatus(200);
        $response->assertSee(__('crm.reports.title'));

        $reportService = app(\App\Services\CrmReportService::class);
        $leadsReport = $reportService->getLeadsReport(now()->subDays(30)->toDateString(), now()->toDateString());
        $this->assertArrayHasKey('total_leads', $leadsReport);
        $this->assertArrayHasKey('by_source', $leadsReport);

        $oppsReport = $reportService->getOpportunitiesReport(now()->subDays(30)->toDateString(), now()->toDateString());
        $this->assertArrayHasKey('total_deals', $oppsReport);
        $this->assertArrayHasKey('open_pipeline_value', $oppsReport);
    }

    public function test_crm_uses_dynamic_system_setting_currency(): void
    {
        // 1. Test USD
        \App\Models\Setting::set('currency', 'USD');
        $this->assertEquals('USD', \App\Services\CurrencyService::code());

        $usdResponse = $this->actingAs($this->admin, 'web')->get(route('admin.crm.dashboard'));
        $usdResponse->assertStatus(200);
        $usdResponse->assertSee('USD');

        // 2. Test EUR
        \App\Models\Setting::set('currency', 'EUR');
        $this->assertEquals('EUR', \App\Services\CurrencyService::code());

        $eurResponse = $this->actingAs($this->admin, 'web')->get(route('admin.crm.dashboard'));
        $eurResponse->assertStatus(200);
        $eurResponse->assertSee('EUR');

        // Reset to SAR
        \App\Models\Setting::set('currency', 'SAR');
    }
}
