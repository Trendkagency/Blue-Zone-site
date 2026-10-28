<?php

namespace Tests\Feature;

use App\Models\CollectionEntry;
use App\Models\FinancialAccount;
use App\Models\Order;
use App\Models\Role;
use App\Models\User;
use App\Services\CollectionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CollectionEntryIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected FinancialAccount $bankAccount;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::firstOrCreate(
            ['name' => 'Super Administrator'],
            ['slug' => 'super-admin', 'permissions' => ['*'], 'is_system' => true]
        );

        $this->admin = User::firstOrCreate(
            ['email' => 'admin@bluezone.com'],
            ['name' => 'Finance Director', 'password' => bcrypt('password'), 'role_id' => $role->id, 'status' => 'active']
        );

        $this->bankAccount = FinancialAccount::create([
            'code' => 'ACC-RAJHI-01',
            'account_number' => 'ACC-SA-9901',
            'name_ar' => 'مصرف الراجحي - الحساب الرئيسي',
            'name_en' => 'Al Rajhi Corporate Bank Account',
            'type' => 'bank',
            'currency' => 'SAR',
            'current_balance' => 10000.00,
            'is_active' => true,
        ]);
    }

    public function test_admin_can_access_collection_entry_index_and_see_spreadsheet_table(): void
    {
        $order = Order::create([
            'order_number' => 'ORD-B2B-101',
            'invoice_number' => 'INV-B2B-101',
            'date' => now()->toDateString(),
            'status' => 'completed',
            'payment_status' => 'Paid',
            'payment_method' => 'bank_transfer',
            'subtotal' => 2000.00,
            'tax' => 300.00,
            'total' => 2300.00,
            'customer_name' => 'Specialized Longevity Center',
        ]);

        CollectionEntry::create([
            'collection_number' => 'COL-2026-0001',
            'entry_date' => now()->toDateString(),
            'category' => 'sales',
            'description' => 'Direct B2B Clinic Payment',
            'customer_code' => 'CUST-8821',
            'customer_source' => 'Specialized Longevity Center',
            'amount_excl_vat' => 2000.00,
            'vat_rate' => 15.00,
            'vat_amount' => 300.00,
            'gross_amount' => 2300.00,
            'financial_account_id' => $this->bankAccount->id,
            'payment_method' => 'bank_transfer',
            'order_id' => $order->id,
            'status' => 'posted',
        ]);

        $response = $this->actingAs($this->admin, 'web')->get(route('admin.finance.collections.index'));

        $response->assertStatus(200);
        $response->assertSee('Collection Entry');
        $response->assertSee('COL-2026-0001');
        $response->assertSee('Specialized Longevity Center');
        $response->assertSee('2,300.00');
        $response->assertSee('Amount Excl. VAT');
        $response->assertSee('Gross Amount');
        $response->assertSee('Al Rajhi Corporate Bank Account');
        $response->assertSee(route('admin.orders.show', $order->id));
    }

    public function test_admin_can_create_manual_collection_entry_with_dynamic_vat(): void
    {
        $initialBalance = $this->bankAccount->current_balance;

        $response = $this->actingAs($this->admin, 'web')->post(route('admin.finance.collections.store'), [
            'entry_date' => now()->toDateString(),
            'category' => 'sales',
            'customer_code' => 'CUST-0099',
            'customer_source' => 'Al-Amal Clinic Network',
            'description' => 'Invoice #INV-2026-7788 payment settlement',
            'gross_amount' => 1150.00,
            'vat_rate' => 15.00,
            'payment_method' => 'bank_transfer',
            'financial_account_id' => $this->bankAccount->id,
        ]);

        $response->assertRedirect(route('admin.finance.collections.index'));

        $this->assertDatabaseHas('collection_entries', [
            'customer_source' => 'Al-Amal Clinic Network',
            'gross_amount' => 1150.00,
            'amount_excl_vat' => 1000.00,
            'vat_amount' => 150.00,
            'category' => 'sales',
        ]);

        $this->bankAccount->refresh();
        $this->assertEquals($initialBalance + 1150.00, (float) $this->bankAccount->current_balance);
    }

    public function test_admin_can_create_equities_collection_with_zero_vat(): void
    {
        $response = $this->actingAs($this->admin, 'web')->post(route('admin.finance.collections.store'), [
            'entry_date' => now()->toDateString(),
            'category' => 'equities',
            'customer_code' => 'PARTNER-01',
            'customer_source' => 'Founder Capital Contribution',
            'description' => 'Q4 Capital Injection',
            'amount_excl_vat' => 50000.00,
            'vat_rate' => 0.00,
            'payment_method' => 'bank_transfer',
            'financial_account_id' => $this->bankAccount->id,
        ]);

        $response->assertRedirect(route('admin.finance.collections.index'));

        $this->assertDatabaseHas('collection_entries', [
            'customer_source' => 'Founder Capital Contribution',
            'category' => 'equities',
            'amount_excl_vat' => 50000.00,
            'vat_amount' => 0.00,
            'gross_amount' => 50000.00,
        ]);
    }

    public function test_auto_sync_online_sales_orders_generates_collection_entry(): void
    {
        $order = Order::create([
            'order_number' => 'ORD-AUTO-7711',
            'invoice_number' => 'INV-AUTO-7711',
            'date' => now()->toDateString(),
            'status' => 'completed',
            'payment_status' => 'Paid',
            'payment_method' => 'Apple Pay Gateway',
            'subtotal' => 1000.00,
            'tax' => 150.00,
            'total' => 1150.00,
            'customer_name' => 'Sarah Al-Ghamdi',
            'customer_phone' => '+966509988776',
        ]);

        $service = app(CollectionService::class);
        $synced = $service->syncOrder($order);

        $this->assertNotNull($synced);
        $this->assertEquals('online_sales_auto', $synced->category);
        $this->assertEquals('Invoice #INV-AUTO-7711', $synced->description);
        $this->assertEquals(1000.00, (float) $synced->amount_excl_vat);
        $this->assertEquals(150.00, (float) $synced->vat_amount);
        $this->assertEquals(1150.00, (float) $synced->gross_amount);
        $this->assertTrue($synced->is_auto_synced);
        $this->assertEquals($order->id, $synced->order_id);

        // Verify sync route batch trigger
        $response = $this->actingAs($this->admin, 'web')->post(route('admin.finance.collections.sync-online'));
        $response->assertRedirect(route('admin.finance.collections.index'));
    }

    public function test_admin_can_print_official_receipt_voucher(): void
    {
        $entry = CollectionEntry::create([
            'collection_number' => 'COL-2026-9988',
            'entry_date' => now()->toDateString(),
            'category' => 'non_operational_revenue',
            'description' => 'Laboratory Equipment Leasing Fee',
            'customer_code' => 'CLINIC-REF-10',
            'customer_source' => 'Bio-Research Medical Labs',
            'amount_excl_vat' => 3000.00,
            'vat_rate' => 15.00,
            'vat_amount' => 450.00,
            'gross_amount' => 3450.00,
            'payment_method' => 'cheque',
            'cheque_number' => 'CHQ-882109',
            'cheque_bank' => 'Saudi National Bank',
            'cheque_due_date' => now()->addDays(15)->toDateString(),
            'financial_account_id' => $this->bankAccount->id,
            'status' => 'posted',
        ]);

        $response = $this->actingAs($this->admin, 'web')->get(route('admin.finance.collections.voucher', $entry->id));

        $response->assertStatus(200);
        $response->assertSee('COL-2026-9988');
        $response->assertSee('Bio-Research Medical Labs');
        $response->assertSee('CHQ-882109');
        $response->assertSee('Saudi National Bank');
        $response->assertSee('3,450.00');
    }

    public function test_admin_can_export_collections_to_csv(): void
    {
        CollectionEntry::create([
            'collection_number' => 'COL-EXPORT-01',
            'entry_date' => now()->toDateString(),
            'category' => 'sales',
            'customer_source' => 'Export Customer Test',
            'amount_excl_vat' => 500.00,
            'vat_rate' => 15.00,
            'vat_amount' => 75.00,
            'gross_amount' => 575.00,
            'payment_method' => 'cash',
            'status' => 'posted',
        ]);

        $response = $this->actingAs($this->admin, 'web')->get(route('admin.finance.collections.export'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition');
        $this->assertStringContainsString('COL-EXPORT-01', $response->streamedContent());
    }

    public function test_admin_can_collect_from_system_customer_with_auto_filled_details(): void
    {
        $customer = \App\Models\Customer::create([
            'name' => 'Dr. Zaid Al-Harbi',
            'email' => 'zaid.harbi@example.com',
            'phone' => '+966 50 123 4567',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->admin, 'web')->post(route('admin.finance.collections.store'), [
            'entry_date' => now()->toDateString(),
            'category' => 'sales',
            'source_type' => 'customer',
            'customer_id' => $customer->id,
            'amount_excl_vat' => 1000.00,
            'vat_rate' => 15.00,
            'gross_amount' => 1150.00,
            'payment_method' => 'bank_transfer',
            'financial_account_id' => $this->bankAccount->id,
        ]);

        $response->assertRedirect(route('admin.finance.collections.index'));

        $this->assertDatabaseHas('collection_entries', [
            'customer_id' => $customer->id,
            'customer_source' => 'Dr. Zaid Al-Harbi',
            'source_type' => 'customer',
            'amount_excl_vat' => 1000.00,
            'gross_amount' => 1150.00,
        ]);
    }

    public function test_admin_can_collect_from_supplier_for_refund(): void
    {
        $supplier = \App\Models\Supplier::create([
            'code' => 'SUP-TEST-99',
            'name_en' => 'ChromaDex Labs Inc.',
            'name_ar' => 'مختبرات كروما دكس',
            'email' => 'chromadex@test.com',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin, 'web')->post(route('admin.finance.collections.store'), [
            'entry_date' => now()->toDateString(),
            'category' => 'supplier_refund',
            'source_type' => 'supplier',
            'supplier_id' => $supplier->id,
            'amount_excl_vat' => 500.00,
            'vat_rate' => 15.00,
            'gross_amount' => 575.00,
            'payment_method' => 'bank_transfer',
            'financial_account_id' => $this->bankAccount->id,
        ]);

        $response->assertRedirect(route('admin.finance.collections.index'));

        $this->assertDatabaseHas('collection_entries', [
            'supplier_id' => $supplier->id,
            'customer_source' => 'ChromaDex Labs Inc.',
            'customer_code' => 'SUP-TEST-99',
            'category' => 'supplier_refund',
            'source_type' => 'supplier',
            'gross_amount' => 575.00,
        ]);
    }

    public function test_admin_can_collect_with_selected_invoice_automatically(): void
    {
        $order = Order::create([
            'order_number' => 'BZ-99201',
            'invoice_number' => 'INV-2026-99201',
            'customer_name' => 'Sara Al-Mansoor',
            'subtotal' => 205.00,
            'tax' => 30.75,
            'total' => 235.75,
            'payment_status' => 'Pending',
            'date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($this->admin, 'web')->post(route('admin.finance.collections.store'), [
            'entry_date' => now()->toDateString(),
            'category' => 'sales',
            'order_id' => $order->id,
            'customer_source' => 'Sara Al-Mansoor',
            'amount_excl_vat' => 205.00,
            'vat_rate' => 15.00,
            'gross_amount' => 235.75,
            'payment_method' => 'bank_transfer',
            'financial_account_id' => $this->bankAccount->id,
        ]);

        $response->assertRedirect(route('admin.finance.collections.index'));

        $this->assertDatabaseHas('collection_entries', [
            'order_id' => $order->id,
            'description' => 'Invoice #INV-2026-99201',
            'amount_excl_vat' => 205.00,
            'vat_amount' => 30.75,
            'gross_amount' => 235.75,
        ]);
    }
}
