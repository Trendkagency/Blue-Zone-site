<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use App\Services\InvoiceTemplateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceTemplateStudioTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Order $order;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::firstOrCreate(
            ['name' => 'Super Administrator'],
            ['slug' => 'super-admin', 'permissions' => ['*'], 'is_system' => true]
        );

        $this->admin = User::firstOrCreate(
            ['email' => 'admin@bluezone.com'],
            ['name' => 'Bluezone Admin', 'password' => bcrypt('password'), 'role_id' => $role->id, 'status' => 'active']
        );

        $this->order = Order::create([
            'order_number' => 'ORD-INV-9901',
            'invoice_number' => 'INV-2026-9901',
            'date' => now()->toDateString(),
            'invoice_date' => now(),
            'channel' => 'B2B Distribution',
            'status' => 'completed',
            'payment_status' => 'paid',
            'payment_method' => 'bank_transfer',
            'subtotal' => 3000.00,
            'tax' => 450.00,
            'total' => 3450.00,
            'customer_name' => 'Al-Amal Specialized Hospital',
            'customer_phone' => '+966551122334',
        ]);
    }

    public function test_admin_can_access_settings_and_see_invoice_template_studio(): void
    {
        $response = $this->actingAs($this->admin, 'web')->get(route('admin.settings.index'));

        $response->assertStatus(200);
        $response->assertSee('tab-invoice');
        $response->assertSee('Invoice Template');
        $response->assertSee('subtab-inv-content');
        $response->assertSee('subtab-inv-columns');
        $response->assertSee('subtab-inv-positions');
        $response->assertSee('subtab-inv-preview');
    }

    public function test_admin_can_render_invoice_template_preview(): void
    {
        $response = $this->actingAs($this->admin, 'web')->get(route('admin.settings.invoice-template.preview'));

        $response->assertStatus(200);
        $response->assertSee('Blue Zone Pharmaceuticals');
        $response->assertSee('فاتورة مبيعات');
        $response->assertSee('تاريخ الصلاحية / التشغيلة');
        $response->assertSee('طريقة الدفع:');
        $response->assertSee('api.qrserver.com');
    }

    public function test_admin_can_update_invoice_template_configuration(): void
    {
        $customConfig = InvoiceTemplateService::defaultTemplate();
        $customConfig['company_name_en'] = 'Blue Zone Longevity Pharma Group';
        $customConfig['title_ar'] = 'فاتورة مبيعات وتوريد صيدلاني';

        $response = $this->actingAs($this->admin, 'web')->post(route('admin.settings.update'), [
            'site_name' => 'Blue Zone Platform',
            'tax_percentage' => 15,
            'tax_number' => '31004829100003',
            'invoice_template_json' => json_encode($customConfig),
        ]);

        $response->assertRedirect();

        $savedTemplate = InvoiceTemplateService::getTemplate();
        $this->assertEquals('Blue Zone Longevity Pharma Group', $savedTemplate['company_name_en']);
        $this->assertEquals('فاتورة مبيعات وتوريد صيدلاني', $savedTemplate['title_ar']);

        // Verify printed invoice reflects the customized setting
        $printRes = $this->actingAs($this->admin, 'web')->get(route('admin.invoices.print', $this->order->id));
        $printRes->assertStatus(200);
        $printRes->assertSee('Blue Zone Longevity Pharma Group');
        $printRes->assertSee('فاتورة مبيعات وتوريد صيدلاني');
    }

    public function test_admin_can_reset_invoice_template_to_default(): void
    {
        // First change to something custom
        $customConfig = InvoiceTemplateService::defaultTemplate();
        $customConfig['company_name_en'] = 'Custom Temporary Name';
        InvoiceTemplateService::saveTemplate($customConfig);

        $this->assertEquals('Custom Temporary Name', InvoiceTemplateService::getTemplate()['company_name_en']);

        // Call reset endpoint
        $response = $this->actingAs($this->admin, 'web')->post(route('admin.settings.invoice-template.reset'));
        $response->assertRedirect();

        // Check it returned back to default
        $this->assertEquals('Blue Zone Pharmaceuticals', InvoiceTemplateService::getTemplate()['company_name_en']);
    }

    public function test_order_public_verify_and_download_routes(): void
    {
        $verifyRes = $this->get(route('orders.verify', $this->order->order_number));
        $verifyRes->assertStatus(200);
        $verifyRes->assertSee('ORD-INV-9901');

        $downloadRes = $this->get(route('orders.download-invoice', $this->order->order_number));
        $downloadRes->assertStatus(200);
        $downloadRes->assertSee('ORD-INV-9901');
        $downloadRes->assertSee('فاتورة مبيعات');
    }
}
