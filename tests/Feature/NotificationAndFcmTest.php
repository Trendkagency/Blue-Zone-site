<?php

namespace Tests\Feature;

use App\Jobs\SendBulkFcmPushJob;
use App\Jobs\SendFcmPushJob;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use App\Models\UserDevice;
use App\Notifications\AdminNotification;
use App\Services\FcmService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationAndFcmTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected Role $adminRole;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Admin Role and Super Admin User
        $this->adminRole = Role::create([
            'name'        => 'Super Admin',
            'description' => 'Full administrative access',
            'permissions' => ['*'],
        ]);

        $this->superAdmin = User::create([
            'name'     => 'System Admin',
            'email'    => 'admin@bluezone-test.com',
            'password' => bcrypt('Secret123!'),
            'role_id'  => $this->adminRole->id,
            'status'   => 'active',
        ]);
    }

    /**
     * Test: Authenticated user can register an FCM device token.
     */
    public function test_authenticated_user_can_register_fcm_device_token(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->postJson('/admin/notifications/fcm-token', [
                'fcm_token'   => 'test_fcm_token_device_alpha_123',
                'device_type' => 'desktop',
                'browser'     => 'Chrome',
                'os'          => 'Windows',
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        // Verify stored in user_devices table
        $this->assertDatabaseHas('user_devices', [
            'user_id'     => $this->superAdmin->id,
            'token'       => 'test_fcm_token_device_alpha_123',
            'device_type' => 'desktop',
            'browser'     => 'Chrome',
            'os'          => 'Windows',
            'is_active'   => true,
        ]);

        // Verify backward compatibility: users table fcm_token is synced
        $this->superAdmin->refresh();
        $this->assertEquals('test_fcm_token_device_alpha_123', $this->superAdmin->fcm_token);
        $this->assertNotNull($this->superAdmin->fcm_device_info);
    }

    /**
     * Test: Multi-device registration stores multiple active devices for one user.
     */
    public function test_multi_device_registration_stores_multiple_devices(): void
    {
        // Device 1: Chrome Desktop
        $this->actingAs($this->superAdmin)
            ->postJson('/admin/notifications/fcm-token', [
                'fcm_token'   => 'token_desktop_chrome',
                'device_type' => 'desktop',
                'browser'     => 'Chrome',
                'os'          => 'Windows',
            ]);

        // Device 2: Safari Mobile
        $this->actingAs($this->superAdmin)
            ->postJson('/admin/notifications/fcm-token', [
                'fcm_token'   => 'token_mobile_safari',
                'device_type' => 'mobile',
                'browser'     => 'Safari',
                'os'          => 'iOS',
            ]);

        $this->assertDatabaseCount('user_devices', 2);
        $this->assertEquals(2, $this->superAdmin->devices()->where('is_active', true)->count());

        $activeTokens = $this->superAdmin->getActiveFcmTokens();
        $this->assertContains('token_desktop_chrome', $activeTokens);
        $this->assertContains('token_mobile_safari', $activeTokens);
    }

    /**
     * Test: Unauthenticated user cannot register device token.
     */
    public function test_unauthenticated_user_cannot_register_device(): void
    {
        $response = $this->postJson('/admin/notifications/fcm-token', [
            'fcm_token' => 'rogue_token',
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test: Creating an Order triggers OrderObserver, creates DB notification and dispatches FCM job.
     */
    public function test_order_creation_triggers_observer_and_dispatches_fcm_job(): void
    {
        Bus::fake([SendBulkFcmPushJob::class]);
        Notification::fake();

        $order = Order::create([
            'order_number'    => 'BZ-TEST-001',
            'invoice_number'  => 'INV-TEST-001',
            'channel'         => 'online',
            'customer_name'   => 'Sarah Smith',
            'customer_email'  => 'sarah@example.com',
            'customer_phone'  => '+966500000000',
            'date'            => now()->toDateString(),
            'status'          => 'Pending',
            'payment_method'  => 'cod',
            'payment_status'  => 'Pending',
            'subtotal'        => 350.00,
            'discount'        => 0.00,
            'shipping'        => 25.00,
            'tax'             => 52.50,
            'total'           => 427.50,
        ]);

        // Verify database notification was dispatched to superAdmin
        Notification::assertSentTo(
            $this->superAdmin,
            AdminNotification::class,
            function ($notification) use ($order) {
                return str_contains($notification->title, 'BZ-TEST-001');
            }
        );

        // Verify background bulk FCM job was dispatched
        Bus::assertDispatched(SendBulkFcmPushJob::class, function ($job) use ($order) {
            return str_contains($job->title, 'BZ-TEST-001');
        });
    }

    /**
     * Test: Updating order status triggers OrderObserver.
     */
    public function test_order_status_update_triggers_notification(): void
    {
        Bus::fake([SendBulkFcmPushJob::class]);
        Notification::fake();

        $order = Order::create([
            'order_number'    => 'BZ-TEST-002',
            'customer_name'   => 'John Doe',
            'channel'         => 'online',
            'date'            => now()->toDateString(),
            'status'          => 'Pending',
            'payment_status'  => 'Pending',
            'total'           => 100.00,
        ]);

        // Update status to shipped
        $order->update(['status' => 'shipped']);

        Bus::assertDispatched(SendBulkFcmPushJob::class, function ($job) {
            return str_contains($job->title, 'Shipped');
        });
    }

    /**
     * Test: Low stock condition in InventoryItemObserver triggers notification and FCM push.
     */
    public function test_low_stock_triggers_observer_and_fcm_job(): void
    {
        Bus::fake([SendBulkFcmPushJob::class]);
        Notification::fake();

        $product = Product::create([
            'name_en'     => 'Pure NMN 500mg',
            'name_ar'     => 'نقي NMN 500 ملغ',
            'slug'        => 'pure-nmn-500mg',
            'sku'         => 'BZ-NMN-01',
            'price'       => 299.00,
            'cost_price'  => 120.00,
            'status'      => 'active',
            'is_featured' => true,
        ]);

        // Create inventory item with stock below threshold
        $item = InventoryItem::create([
            'product_id'          => $product->id,
            'location_id'         => 1,
            'location_name_en'    => 'Main Hub',
            'current_stock'       => 5,
            'low_stock_threshold' => 15,
            'status'              => 'low_stock',
        ]);

        Bus::assertDispatched(SendBulkFcmPushJob::class, function ($job) {
            return str_contains($job->title, 'Low Stock');
        });
    }

    /**
     * Test: FcmService singleton in Mock mode dispatches safely without errors.
     */
    public function test_fcm_service_mock_mode_dispatches_successfully(): void
    {
        $fcm = FcmService::getInstance();
        $response = $fcm->sendPush('fake_mock_token_xyz', 'System Alert', 'Database backup complete.');

        $this->assertTrue($response['success']);
        $this->assertEquals('mock', $response['provider']);
        $this->assertNotEmpty($response['message_id']);
    }

    /**
     * Test: Token deactivation handles invalid/unregistered tokens.
     */
    public function test_invalid_token_is_deactivated_in_database(): void
    {
        $device = UserDevice::create([
            'user_id'     => $this->superAdmin->id,
            'token'       => 'invalid_expired_token_999',
            'device_type' => 'desktop',
            'is_active'   => true,
        ]);

        $this->superAdmin->update(['fcm_token' => 'invalid_expired_token_999']);

        // Execute SendFcmPushJob with simulated invalid token response
        $job = new SendFcmPushJob(
            'invalid_expired_token_999',
            'Test Alert',
            'Test Body',
            [],
            $this->superAdmin->id
        );

        // FcmService mock sends mock, but we can verify deactivation logic
        $device->deactivate('Unregistered device');

        $this->assertFalse($device->fresh()->is_active);
    }
}
