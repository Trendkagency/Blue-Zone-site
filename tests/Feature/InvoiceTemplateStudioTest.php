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

    public function test_admin_can_view_invoice_show_with_new_official_format(): void
    {
        $response = $this->actingAs($this->admin, 'web')->get(route('admin.invoices.show', $this->order->id));
        $response->assertStatus(200);
        $response->assertSee('فاتورة مبيعات');
        $response->assertSee('TAX INVOICE');
        $response->assertSee('invoice-3d-scene');
        $response->assertSee('invoice-3d-canvas');
        $response->assertSee('inv-header-grid');
        $response->assertSee('sheet-table');
        $response->assertSee('ORD-INV-9901');
    }

    public function test_admin_can_update_invoice_details_and_layout_in_realtime(): void
    {
        $response = $this->actingAs($this->admin, 'web')->postJson(route('admin.invoices.update', $this->order->id), [
            'customer_name' => 'Realtime Updated Medical Center',
            'discount' => 150.00,
            'sections_order' => [
                'title_banner',
                'header_company_client',
                'items_table',
                'totals_and_payments',
                'signatures_stamp',
                'footer_notes',
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->order->refresh();
        $this->assertEquals('Realtime Updated Medical Center', $this->order->customer_name);
        $this->assertEquals(150.00, (float)$this->order->discount);
    }

    public function test_admin_can_see_purchase_invoice_template_studio_tabs_and_panes(): void
    {
        $response = $this->actingAs($this->admin, 'web')->get(route('admin.settings.index'));

        $response->assertStatus(200);
        $response->assertSee('tab-btn-inv-sales');
        $response->assertSee('tab-btn-inv-purchase');
        $response->assertSee('invoice-studio-purchase-pane');
        $response->assertSee('subtab-pur-content');
        $response->assertSee('subtab-pur-columns');
        $response->assertSee('subtab-pur-positions');
        $response->assertSee('subtab-pur-preview');
        $response->assertSee('10 Columns Active');
    }

    public function test_admin_can_render_purchase_invoice_template_preview(): void
    {
        $response = $this->actingAs($this->admin, 'web')->get(route('admin.settings.purchase-invoice-template.preview'));

        $response->assertStatus(200);
        $response->assertSee('Blue Zone Pharmaceuticals');
        $response->assertSee('فاتورة مشتريات');
        $response->assertSee('تاريخ الصلاحية / التشغيله');
        $response->assertSee('سعر الوحدة');
        $response->assertSee('إجمالي بدون الضريبة');
        $response->assertSee('إجمالي الصنف شامل الضريبة');
        $response->assertSee('Partial Payment');
        $response->assertSee('دفع جزئي');
        $response->assertSee('api.qrserver.com');
    }

    public function test_admin_can_update_purchase_invoice_template_configuration(): void
    {
        $customConfig = InvoiceTemplateService::defaultPurchaseTemplate();
        $customConfig['company_name_en'] = 'Blue Zone Global Inbound Procurement';
        $customConfig['title_ar'] = 'سند وفاتورة توريد مشتريات طبية';

        $response = $this->actingAs($this->admin, 'web')->post(route('admin.settings.update'), [
            'site_name' => 'Blue Zone Platform',
            'tax_percentage' => 15,
            'tax_number' => '31004829100003',
            'purchase_invoice_template_json' => json_encode($customConfig),
        ]);

        $response->assertRedirect();

        $savedTemplate = InvoiceTemplateService::getPurchaseTemplate();
        $this->assertEquals('Blue Zone Global Inbound Procurement', $savedTemplate['company_name_en']);
        $this->assertEquals('سند وفاتورة توريد مشتريات طبية', $savedTemplate['title_ar']);
    }

    public function test_admin_can_reset_purchase_invoice_template_to_default(): void
    {
        $customConfig = InvoiceTemplateService::defaultPurchaseTemplate();
        $customConfig['company_name_en'] = 'Temporary Vendor Hub';
        InvoiceTemplateService::savePurchaseTemplate($customConfig);

        $this->assertEquals('Temporary Vendor Hub', InvoiceTemplateService::getPurchaseTemplate()['company_name_en']);

        $response = $this->actingAs($this->admin, 'web')->post(route('admin.settings.purchase-invoice-template.reset'));
        $response->assertRedirect();

        $this->assertEquals('Blue Zone Pharmaceuticals', InvoiceTemplateService::getPurchaseTemplate()['company_name_en']);
    }
}
